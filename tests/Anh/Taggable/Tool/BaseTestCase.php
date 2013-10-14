<?php

namespace Anh\Taggable\Tool;

use Anh\Taggable\TaggableManager;
use Anh\Taggable\TaggableListener;

use Doctrine\ORM\Configuration;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Annotations\AnnotationReader;

abstract class BaseTestCase extends \PHPUnit_Framework_TestCase
{
    const TAG = 'Anh\Taggable\Entity\Tag';
    const TAGGING = 'Anh\Taggable\Entity\Tagging';
    const ARTICLE = 'Anh\Taggable\Fixtures\Article';
    const POST = 'Anh\Taggable\Fixtures\Post';

    protected $em;
    protected $manager;

    public function setUp()
    {
        $config = new Configuration();
        $config->setMetadataCacheImpl(new ArrayCache);
        $config->setQueryCacheImpl(new ArrayCache);
        $config->setProxyDir(__DIR__ . '/../../../temp');
        $config->setProxyNamespace('Anh\Taggable\Proxies');
        $driver = new AnnotationDriver(new AnnotationReader());
        $config->setMetadataDriverImpl($driver);

        $this->em = EntityManager::create(
            array(
                'driver' => 'pdo_sqlite',
                'memory' => true
            ),
            $config
        );

        $schema = new SchemaTool($this->em);
        $schema->dropSchema(array());
        $schema->createSchema(array(
            $this->em->getClassMetadata(self::TAG),
            $this->em->getClassMetadata(self::TAGGING),
            $this->em->getClassMetadata(static::ARTICLE),
            $this->em->getClassMetadata(static::POST),
        ));

        $this->manager = new TaggableManager($this->em, self::TAG, self::TAGGING);
        $this->em->getEventManager()->addEventSubscriber(new TaggableListener($this->manager));
    }

    protected function createFixture($class = null)
    {
        if ($class === null) {
            $class = static::ARTICLE;
        }

        $article = new $class();
        $article->setTitle('There is a star in the sky');
        $tags = $this->manager->loadOrCreateTags(array('pulsar', 'nebula', 'galaxy'));
        $article->addTags($tags);

        return $article;
    }

    protected function loadFixture($class = null)
    {
        $article = $this->createFixture($class);

        $this->em->persist($article);
        $this->em->flush();
        $this->em->clear();

        // $this->manager->detach($article);
        // $this->em->detach($article);

        return $this->em->find(static::ARTICLE, $article->getId());
    }
}

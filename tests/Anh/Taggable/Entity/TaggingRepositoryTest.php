<?php

use Anh\Taggable\Tool\BaseTestCase;

class TaggingRepositoryTest extends BaseTestCase
{
    public function testGetResourcesWithTag()
    {
        $this->loadFixture(self::ARTICLE);
        $this->loadFixture(self::ARTICLE);
        $this->loadFixture(self::ARTICLE);
        $article = $this->loadFixture(self::ARTICLE);

        $this->loadFixture(self::POST);
        $this->loadFixture(self::POST);
        $post = $this->loadFixture(self::POST);

        // ungrouped
        $tag = $this->manager->loadOrCreateTag('galaxy');
        $resources = $this->em->getRepository(self::TAGGING)
            ->getResourcesWithTag($tag)
        ;
        $this->assertEquals(7, count($resources));

        // grouped by type
        $resources = $this->em->getRepository(self::TAGGING)
            ->getResourcesWithTag($tag, true)
        ;
        $this->assertEquals(2, count($resources));

        $articleType = $article->getTaggableType();
        $postType = $post->getTaggableType();

        $this->assertTrue(isset($resources[$articleType]));
        $this->assertEquals(4, count($resources[$articleType]));
        $this->assertTrue(isset($resources[$postType]));
        $this->assertEquals(4, count($resources[$postType]));
    }

    public function testGetResourcesWithTypeAndTag()
    {
        $this->loadFixture(self::ARTICLE);
        $this->loadFixture(self::ARTICLE);
        $this->loadFixture(self::ARTICLE);
        $article = $this->loadFixture(self::ARTICLE);
        $this->loadFixture(self::POST);
        $this->loadFixture(self::POST);
        $this->loadFixture(self::POST);

        $tag = $this->manager->loadOrCreateTag('galaxy');
        $resources = $this->em->getRepository(self::TAGGING)
            ->getResourcesWithTypeAndTag($article->getTaggableType(), $tag)
        ;

        $this->assertEquals(4, count($resources));
    }

    public function testGetTagsAndResourcesCount()
    {
        $tag = $this->manager->loadOrCreateTag('galaxy');
        $this->loadFixture(self::ARTICLE);
        $article = $this->loadFixture(self::ARTICLE);
        $article->removeTag($tag);
        $this->em->flush();

        $tags = $this->em->getRepository(self::TAGGING)
            ->getTagsAndResourcesCount()
        ;

        foreach ($tags as $tag) {
            switch ($tag['tag']->getName()) {
                case 'galaxy':
                    $this->assertEquals(1, $tag['count']);
                    break;
                case 'nebula':
                    $this->assertEquals(2, $tag['count']);
                    break;
                case 'pulsar':
                    $this->assertEquals(2, $tag['count']);
                    break;
            }
        }
    }
}
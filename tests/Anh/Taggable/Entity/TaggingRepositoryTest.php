<?php

use Anh\Taggable\Tool\BaseTestCase;

class TaggingRepositoryTest extends BaseTestCase
{
    public function testGetResourcesByTag()
    {
        $this->loadFixture(self::ARTICLE);
        $this->loadFixture(self::ARTICLE);
        $this->loadFixture(self::ARTICLE);
        $this->loadFixture(self::ARTICLE);
        $this->loadFixture(self::POST);
        $this->loadFixture(self::POST);
        $this->loadFixture(self::POST);

        $tag = $this->manager->loadOrCreateTag('galaxy');
        $resources = $this->em->getRepository(self::TAGGING)
            ->getResourcesByTypeWithTag('article', $tag)
        ;

        $this->assertEquals(4, count($resources));
    }
}
<?php

use Anh\Taggable\Tool\BaseTestCase;

class TaggableManagerTest extends BaseTestCase
{
    public function testGetTagClass()
    {
        $this->assertEquals(self::TAG, $this->manager->getTagClass());
    }

    public function testGetTaggingClass()
    {
        $this->assertEquals(self::TAGGING, $this->manager->getTaggingClass());
    }

    public function testCreateTag()
    {
        $this->assertInstanceOf(self::TAG, $this->manager->createTag());
    }

    public function testCreateTagging()
    {
        $this->assertInstanceOf(self::TAGGING, $this->manager->createTagging());
    }

    public function testGetTagRepository()
    {
        $this->assertInstanceOf(
            '\Anh\Taggable\Entity\TagRepository',
            $this->manager->getTagRepository()
        );
    }

    public function testGetTaggingRepository()
    {
        $this->assertInstanceOf(
            '\Anh\Taggable\Entity\TaggingRepository',
            $this->manager->getTaggingRepository()
        );
    }

    public function testLoadOrCreateTag()
    {
        $tag1 = $this->manager->loadOrCreateTag('test');
        $this->assertInstanceOf(self::TAG, $tag1);
        $tag2 = $this->manager->loadOrCreateTag('test');
        $this->assertEquals($tag1->getId(), $tag2->getId());
    }

    public function testLoadOrCreateTagWithEmptyNameShouldThrowException()
    {
        $this->setExpectedException('\InvalidArgumentException');
        $tag = $this->manager->loadOrCreateTag('');
    }

    public function testLoadOrCreateTags()
    {
        $tag = $this->manager->loadOrCreateTag('test');
        $tags = $this->manager->loadOrCreateTags(array('test', 'another'));
        $this->assertInstanceOf('\Doctrine\Common\Collections\ArrayCollection', $tags);
        $this->assertEquals($tag, $tags[0]);
        $this->assertEquals($tags[1]->getName(), 'another');
    }

    public function testLoadOrCreateTagsWithEmptyNamesShouldReturnEmptyCollection()
    {
        $tags = $this->manager->loadOrCreateTags(array());
        $this->assertInstanceOf('\Doctrine\Common\Collections\ArrayCollection', $tags);
        $this->assertTrue($tags->isEmpty());
    }

    public function testLoadTags()
    {
        $article = $this->loadFixture();

        $tags = $this->manager->loadTags($article);
        $this->assertInstanceOf('\Doctrine\Common\Collections\ArrayCollection', $tags);
        $this->assertEquals(3, $tags->count());
        $this->assertEquals('pulsar', $tags[0]->getName());
        $this->assertEquals('nebula', $tags[1]->getName());
        $this->assertEquals('galaxy', $tags[2]->getName());
    }

    public function testDeleteTagging()
    {
        // $article = $this->createFixture();
        $article = $this->loadFixture();
        $tags = $this->manager->loadOrCreateTags(array('andromeda', 'orion'));
        $article->addTags($tags);
        $this->manager->deleteTagging($article);
        $this->em->flush();

        $repository = $this->em->getRepository(self::TAGGING);
        $tagging = $repository->findBy(array(
            'resourceId' => $article->getId(),
            'resourceType' => $article->getTaggableType()
        ));
        $this->assertTrue(empty($tagging));
    }

    public function testSyncTaggingAdd()
    {
        $article = $this->createFixture();
        $this->em->persist($article);
        $tags = $this->manager->loadOrCreateTags(array('tag1', 'tag2', 'tag3'));
        $article->addTags($tags);
        $this->em->flush();
        $this->em->clear();

        $article = $this->em->find(static::ARTICLE, $article->getId());
        $this->assertEquals(6, $article->getTags()->count());
    }

    public function testSyncTaggingRemove()
    {
        $article = $this->createFixture();
        $this->em->persist($article);
        $tag = $this->manager->loadOrCreateTag('galaxy');
        $article->removeTag($tag);
        $this->em->flush();
        $this->em->clear();

        $article = $this->em->find(static::ARTICLE, $article->getId());
        $this->assertEquals(2, $article->getTags()->count());
    }

    public function testSyncTaggingAddLoaded()
    {
        $article = $this->loadFixture();
        $tags = $this->manager->loadOrCreateTags(array('tag1', 'tag2', 'tag3'));
        $article->addTags($tags);
        $this->em->flush();
        $this->em->clear();

        $article = $this->em->find(static::ARTICLE, $article->getId());
        $this->assertEquals(6, $article->getTags()->count());
    }

    public function testSyncTaggingRemoveLoaded()
    {
        $article = $this->loadFixture();
        $tag = $this->manager->loadOrCreateTag('galaxy');
        $article->removeTag($tag);
        $this->em->flush();
        $this->em->clear();

        $article = $this->em->find(static::ARTICLE, $article->getId());
        $this->assertEquals(2, $article->getTags()->count());
    }

    public function testTagUniqueConstraint()
    {
        $this->setExpectedException('\Doctrine\DBAL\DBALException');

        $tag1 = $this->manager->createTag();
        $tag1->setName('test');
        $this->em->persist($tag1);

        $tag2 = $this->manager->createTag();
        $tag2->setName('test');
        $this->em->persist($tag2);

        $this->em->flush();
    }

    public function testTaggingUniqueConstraint()
    {
        $this->setExpectedException('\Doctrine\DBAL\DBALException');

        $article = $this->createFixture();
        $tag = $this->manager->loadOrCreateTag('test');

        $tagging1 = $this->manager->createTagging();
        $tagging1->setResource($article);
        $tagging1->setTag($tag);
        $this->em->persist($tagging1);

        $tagging2 = $this->manager->createTagging();
        $tagging2->setResource($article);
        $tagging2->setTag($tag);
        $this->em->persist($tagging2);

        $this->em->flush();
    }

    public function testDeleteTagsByIdList()
    {
        $this->loadFixture();
        $tags = $this->manager->loadOrCreateTags(array('galaxy', 'nebula'));
        $idList = array_map(function($tag) { return $tag->getId(); }, $tags->toArray());
        $this->manager->deleteTagsByIdList($idList);
        $tags = $this->manager->getTagRepository()->findAll();
        $this->assertEquals(1, count($tags));
        $this->assertEquals('pulsar', $tags[0]->getName());
    }
}

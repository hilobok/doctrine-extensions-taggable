<?php

namespace Anh\Taggable\Tool;

abstract class TaggableTestCase extends BaseTestCase
{
    abstract protected function getArticle();

    public function testGetTags()
    {
        $article = $this->getArticle();
        $tags = $article->getTags();
        $this->assertInstanceOf('\Doctrine\Common\Collections\ArrayCollection', $tags);
        $this->assertEquals(3, $tags->count());
    }

    public function testGetTagNames()
    {
        $article = $this->getArticle();
        $names = $article->getTagNames();
        $this->assertTrue($names == array('pulsar', 'nebula', 'galaxy'));
    }

    public function testSetTagNamesException()
    {
        $this->setExpectedException('\BadMethodCallException');

        $article = $this->getArticle();
        $article->setTaggableManager(null);
        $newNames = array('constellations', 'milky way');
        $article->setTagNames($newNames);
    }

    public function testSetTagNames()
    {
        $article = $this->getArticle();
        $article->setTaggableManager($this->manager);
        $newNames = array('constellations', 'milky way');
        $article->setTagNames($newNames);
        $this->assertTrue($newNames == $article->getTagNames());
    }

    public function testAddTag()
    {
        $article = $this->getArticle();
        $tag = $this->manager->loadOrCreateTag('blue giant');
        $article->addTag($tag);
        $this->assertEquals(4, $article->getTags()->count());
    }

    public function testAddTags()
    {
        $article = $this->getArticle();
        $tags = $this->manager->loadOrCreateTags(array('red dwarf', 'black hole'));
        $article->addTags($tags);
        $this->assertEquals(5, $article->getTags()->count());
    }

    public function testRemoveTag()
    {
        $article = $this->getArticle();
        $tag = $this->manager->loadOrCreateTag('nebula');
        $article->removeTag($tag);
        $this->assertEquals(2, $article->getTags()->count());
    }

    public function testRemoveTagIssueWithProxy()
    {
        $tag = $this->manager->loadOrCreateTag('nebula');
        $article = $this->getArticle();
        $article->removeTag($tag);
        $this->assertEquals(2, $article->getTags()->count());
    }

    public function testClearTags()
    {
        $article = $this->getArticle();
        $article->clearTags();
        $this->assertEquals(0, $article->getTags()->count());
    }

    public function testReplaceTags()
    {
        $article = $this->getArticle();
        $newNames = array('tag1', 'tag2');
        $tags = $this->manager->loadOrCreateTags($newNames);
        $article->replaceTags($tags);
        $this->assertEquals(2, $article->getTags()->count());
        $this->assertTrue($newNames == $article->getTagNames());
    }

    public function testGetTaggableId()
    {
        $article = $this->getArticle();
        $this->assertEquals($article->getId(), $article->getTaggableId());
    }

    public function testGetTaggableType()
    {
        $article = $this->getArticle();
        $this->assertEquals('article', $article->getTaggableType());
    }

    public function testSetGetTaggableManager()
    {
        $article = $this->getArticle();
        $article->setTaggableManager($this->manager);
        $this->assertEquals($this->manager, $article->getTaggableManager());
    }
}

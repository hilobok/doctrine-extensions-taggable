<?php

use Anh\Taggable\Tool\BaseTestCase;

class TaggableListenerTest extends BaseTestCase
{
    public function testPostLoad()
    {
        $article = $this->loadFixture();
        $this->assertEquals($this->manager, $article->getTaggableManager());
    }

    public function testPostPersist()
    {
        $article = $this->createFixture();
        $tags = $this->manager->loadOrCreateTags(array('white dwarf', 'neutron star'));
        $article->addTags($tags);
        $this->em->persist($article);
        $this->em->flush();

        $id = $article->getTaggableId();
        $type = $article->getTaggableType();

        $repository = $this->em->getRepository(self::TAGGING);
        $tagging = $repository->findBy(array(
            'resourceId' => $id,
            'resourceType' => $type
        ));
        $this->assertEquals(5, count($tagging));
    }

    public function testPreFlush()
    {
        $article = $this->loadFixture();
        $tags = $this->manager->loadOrCreateTags(array('helium star', 'iron star'));
        $article->addTags($tags);
        $this->em->flush();

        $id = $article->getTaggableId();
        $type = $article->getTaggableType();

        $repository = $this->em->getRepository(self::TAGGING);
        $tagging = $repository->findBy(array(
            'resourceId' => $id,
            'resourceType' => $type
        ));
        $this->assertEquals(5, count($tagging));
    }

    public function testPreRemove()
    {
        $article = $this->loadFixture();
        $id = $article->getTaggableId();
        $type = $article->getTaggableType();

        $repository = $this->em->getRepository(self::TAGGING);
        $tagging = $repository->findBy(array(
            'resourceId' => $id,
            'resourceType' => $type
        ));

        // ensure tagging exists
        $this->assertFalse(empty($tagging));

        $this->em->remove($article);
        $this->em->flush();

        $tagging = $repository->findBy(array(
            'resourceId' => $id,
            'resourceType' => $type
        ));

        // ensure tagging is empty
        $this->assertTrue(empty($tagging));
    }
}

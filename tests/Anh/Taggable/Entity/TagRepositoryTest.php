<?php

use Anh\Taggable\Tool\BaseTestCase;

class TagRepositoryTest extends BaseTestCase
{
    public function testSearch()
    {
        $this->loadFixture();
        $repository = $this->manager->getTagRepository();

        $tags = $repository->search('');
        $this->assertEquals(3, count($tags));

        $tags = $repository->search('ul');
        $this->assertEquals(2, count($tags)); // 'pULsar', 'nebULa'

        $this->manager->loadOrCreateTag('puls');
        $this->em->flush();
        $tags = $repository->search('puls', true);
        $this->assertEquals(1, count($tags));

        $tags = $repository->search('galaxy', true, 'name');
        $this->assertEquals(1, count($tags));
        $this->assertEquals('galaxy', $tags[0]);

        $tags = $repository->search('', false, null, 2);
        $this->assertEquals(2, count($tags));
        $this->assertInstanceOf('\Anh\Taggable\Entity\Tag', $tags[0]);
    }
}

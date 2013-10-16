<?php

use Anh\Taggable\Tool\BaseTestCase;

class TagRepositoryTest extends BaseTestCase
{
    public function testGetAllTagNames()
    {
        $this->loadFixture();
        $names = $this->manager->getTagRepository()->getAllTagNames();
        $this->assertEmpty(array_diff($names, array('pulsar', 'nebula', 'galaxy')));
    }
}

<?php

namespace Anh\Taggable\Entity;

use Doctrine\ORM\EntityRepository;

class TagRepository extends EntityRepository
{
    /**
     * Returns array with all tag names.
     *
     * @return array
     */
    public function getAllTagNames()
    {
        $tags = $this->findAll();

        $names = array();

        foreach ($tags as $tag) {
            $names[] = $tag->getName();
        }

        return $names;
    }
}
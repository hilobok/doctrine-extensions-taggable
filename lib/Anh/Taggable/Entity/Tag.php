<?php

namespace Anh\DoctrineExtensions\Taggable\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Table()
 * @ORM\Entity()
 */
class Tag extends MappedSuperclass\AbstractTag
{
    /**
     * @var $tagging
     *
     * @ORM\OneToMany(targetEntity="Tagging", mappedBy="tag", cascade={"remove"})
     */
    protected $tagging;
}
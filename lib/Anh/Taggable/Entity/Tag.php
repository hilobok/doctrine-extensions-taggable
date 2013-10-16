<?php

namespace Anh\Taggable\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(name="name_idx", columns={"name"})
 *      }
 * )
 * @ORM\Entity(repositoryClass="Anh\Taggable\Entity\TagRepository")
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

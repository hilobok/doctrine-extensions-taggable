<?php

namespace Anh\Taggable\Entity\MappedSuperclass;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
abstract class AbstractTag
{
    /**
     * @var integer $id
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @var string $name
     *
     * @ORM\Column(type="string", unique=true)
     */
    protected $name;

    /**
     * Related tagging with OneToMany relation
     * must be mapped by user
     */
    protected $tagging;

    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the tag's name
     *
     * @param string $name Name to set
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns tag's name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Compares two tags.
     *
     * @param AbstractTag $tag
     *
     * @see strcasecmp
     */
    public function compareTo(AbstractTag $tag)
    {
        return strcasecmp($this->getName(), $tag->getName());
    }

    /**
     * Compares if two tags is equal.
     *
     * @param AbstractTag $tag
     *
     * @return boolean
     */
    public function isEqualTo(AbstractTag $tag)
    {
        return $this->compareTo($tag) === 0;
    }
}

<?php

namespace Anh\Taggable\Entity\MappedSuperclass;

use Doctrine\ORM\Mapping as ORM;
use Anh\Taggable\TaggableInterface;

/**
 * @ORM\MappedSuperclass
 */
abstract class AbstractTagging
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
     * @var string $resourceType
     *
     * @ORM\Column(type="string")
     */
    protected $resourceType;

    /**
     * @var integer $resourceId
     *
     * @ORM\Column(type="integer")
     */
    protected $resourceId;

    /**
     * Related tag with ManyToOne relation
     * must be mapped by user
     */
    protected $tag;

    /**
     * Sets the resource
     *
     * @param TaggableInterface $resource Resource to set
     */
    public function setResource(TaggableInterface $resource)
    {
        $this->resourceType = $resource->getTaggableType();
        $this->resourceId = $resource->getTaggableId();
    }

    /**
     * Returns the tagged resource type
     *
     * @return string
     */
    public function getResourceType()
    {
        return $this->resourceType;
    }

    /**
     * Returns the tagged resource id
     *
     * @return integer
     */
    public function getResourceId()
    {
        return $this->resourceId;
    }

    /**
     * Sets the tag object
     *
     * @param AbstractTag $tag Tag to set
     */
    public function setTag(AbstractTag $tag)
    {
        $this->tag = $tag;
    }

    /**
     * Returns the tag object
     *
     * @return AbstractTag
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }
}
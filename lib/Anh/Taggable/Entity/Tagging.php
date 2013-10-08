<?php

namespace Anh\Taggable\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *      uniqueConstraints={
 *          @UniqueConstraint(name="tagging_idx", columns={"tagId", "resourceType", "resourceId"})
 *      }
 * )
 * @ORM\Entity()
 */
class Tagging extends MappedSuperclass\AbstractTagging
{
    /**
     * @var $tag
     *
     * @ORM\ManyToOne(targetEntity="Tag", inversedBy="tagging")
     * @ORM\JoinColumn(name="tagId", referencedColumnName="id")
     */
    protected $tag;
}
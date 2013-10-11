<?php

namespace Anh\Taggable\Fixtures;

use Doctrine\ORM\Mapping as ORM;
use Anh\Taggable\TaggableInterface;
use Anh\Taggable\AbstractTaggable;
use Anh\Taggable\TaggableTrait;

/**
 * @ORM\Entity()
 */
class ArticleWithTrait implements TaggableInterface
{
    use TaggableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $title;

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTaggableType()
    {
        return 'article';
    }
}

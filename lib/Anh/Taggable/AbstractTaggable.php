<?php

namespace Anh\Taggable;

use Anh\Taggable\Entity\Tag;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * AbstractTaggable class implements basic functionalities for taggable behaviour.
 * All taggable entities should inherit it or use TaggableTrait otherwise.
 */
abstract class AbstractTaggable implements TaggableInterface
{
    /**
     * Holds collection of associated tags.
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $tags;

    /**
     * TaggableManager
     *
     * @var \Anh\Taggable\TaggableManager
     */
    protected $taggableManager;

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTags()
    {
        if ($this->tags === null) {
            $this->tags = new ArrayCollection();

            if ($this->taggableManager) {
                $this->tags = $this->taggableManager->loadTags($this);
            }
        }

        return $this->tags;
    }

    /**
     * Set tags
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $tags
     */
    public function setTags(ArrayCollection $tags)
    {
        $this->tags = $tags;
    }

    /**
     * Get tag names
     *
     * @return array
     */
    public function getTagNames()
    {
        return array_map(
            function($tag) { return $tag->getName(); },
            $this->getTags()->toArray()
        );
    }

    /**
     * Set tag names
     *
     * Taggable manager must be set.
     *
     * @param array $names
     *
     * @return \Anh\Taggable\TaggableInterface
     */
    public function setTagNames(array $names)
    {
        if (!$this->taggableManager) {
            throw new \BadMethodCallException('Taggable manager must be set.');
        }

        $tags = $this->taggableManager->loadOrCreateTags($names);
        $this->replaceTags($tags);

        return $this;
    }

    /**
     * Add tag
     *
     * @param \Anh\Taggable\Entity\Tag $tag
     *
     * @return \Anh\Taggable\TaggableInterface
     */
    public function addTag(Tag $tag)
    {
        $this->getTags()->add($tag);

        return $this;
    }

    /**
     * Add tags
     *
     * @param array $tags
     *
     * @return \Anh\Taggable\TaggableInterface
     */
    public function addTags(array $tags)
    {
        foreach ($tags as $tag) {
            $this->addTag($tag);
        }

        return $this;
    }

    /**
     * Remove tag
     *
     * @param \Anh\Taggable\Entity\Tag $tag
     *
     * @return \Anh\Taggable\TaggableInterface
     */
    public function removeTag(Tag $tag)
    {
        $tags = $this->getTags();

        foreach ($tags as $key => $value) {
            if ($value->isEqualTo($tag)) {
                $tags->remove($key);
                break;
            }
        }

        return $this;
    }

    /**
     * Remove tags
     *
     * @param array $tag
     *
     * @return \Anh\Taggable\TaggableInterface
     */
    public function removeTags(array $tags)
    {
        foreach ($tags as $tag) {
            $this->removeTag($tag);
        }

        return $this;
    }

    /**
     * Clear tags
     *
     * @return \Anh\Taggable\TaggableInterface
     */
    public function clearTags()
    {
        $this->getTags()->clear();

        return $this;
    }

    /**
     * Replace tags
     *
     * @param array $tags
     *
     * @return \Anh\Taggable\TaggableInterface
     */
    public function replaceTags(array $tags)
    {
        $this->clearTags()->addTags($tags);

        return $this;
    }

    /**
     * Get taggable id used for tagging. Id is sufficient in most cases.
     *
     * @return mixed
     */
    public function getTaggableId()
    {
        return $this->getId();
    }

    /**
     * Get taggable manager
     *
     * @return \Anh\Taggable\TaggableManager
     */
    public function getTaggableManager()
    {
        return $this->taggableManager;
    }

    /**
     * Set taggable manager
     *
     * @param \Anh\Taggable\TaggableManager $taggableManager
     *
     * @return \Anh\Taggable\TaggableManager
     */
    public function setTaggableManager($taggableManager)
    {
        return $this->taggableManager = $taggableManager;
    }

    /**
     * Get string identifier used for tagging.
     *
     * @return string
     */
    abstract public function getTaggableType();
}

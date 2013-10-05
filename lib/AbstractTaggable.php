<?php

namespace Anh\DoctrineExtensions\Taggable;

// use Doctrine\Common\Collections\ArrayCollection;

abstract class AbstractTaggable implements TaggableInterface
{
    public function getTags()
    {
        if ($this->tags === null and $this->taggableManager) {
            // $this->tags = new ArrayCollection();

            // if ($this->taggableManager) {
                $this->tags = $this->taggableManager->loadTagging($this);
            // }
        }

        return $this->tags;
    }

    public function getTagNames()
    {
        $tags = $this->getTags();

        return array_map(function($tag) { return $tag->getName(); }, $tags);
    }

    public function addTag(Tag $tag)
    {
        $tags = $this->getTags();
        $tags->add($tag);

        return $this;
    }

    public function addTags(array $tags)
    {
        foreach ($tags as $tag) {
            $this->addTag($tag);
        }

        return $this;
    }

    public function removeTag(Tag $tag)
    {
        $tags = $this->getTags();
        $tags->removeElement($tag);

        return $this;
    }

    public function removeTags(array $tags)
    {
        foreach ($tags as $tag) {
            $this->removeTag($tag);
        }

        return $this;
    }

    public function clearTags()
    {
        $this->getTags()->clear();

        return $this;
    }

    public function replaceTags(array $tags)
    {
        $this->clearTags()->addTags($tags);
    }

    public function getTaggableId()
    {
        return $this->getId();
    }

    abstract public function getTaggableType();

    public function setTaggableManager($taggableManager)
    {
        $this->taggableManager = $taggableManager;
    }
}
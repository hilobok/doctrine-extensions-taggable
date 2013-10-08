<?php

namespace Anh\DoctrineExtensions\Taggable;

interface TaggableInterface
{
    protected $tags;

    protected $taggableManager;

    public function getTags();

    public function getTaggableId();

    public function getTaggableType();

    public function setTaggableManager($taggableManager);
}
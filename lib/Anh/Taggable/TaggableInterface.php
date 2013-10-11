<?php

namespace Anh\Taggable;

interface TaggableInterface
{
    public function getTags();

    public function getTaggableId();

    public function getTaggableType();
}

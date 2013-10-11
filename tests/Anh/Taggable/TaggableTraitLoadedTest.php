<?php

use Anh\Taggable\Tool\TaggableTraitTestCase;

class TaggableTraitLoadedTest extends TaggableTraitTestCase
{
    protected function getArticle()
    {
        return $this->loadArticle();
    }
}

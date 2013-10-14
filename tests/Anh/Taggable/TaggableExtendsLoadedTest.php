<?php

use Anh\Taggable\Tool\TaggableTestCase;

class TaggableExtendsLoadedTest extends TaggableTestCase
{
    protected function getArticle()
    {
        return $this->loadFixture();
    }
}

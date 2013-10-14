<?php

use Anh\Taggable\Tool\TaggableTraitTestCase;

class TaggableTraitTest extends TaggableTraitTestCase
{
    protected function getArticle()
    {
        return $this->createFixture();
    }
}

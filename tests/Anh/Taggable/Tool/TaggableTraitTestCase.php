<?php

namespace Anh\Taggable\Tool;

abstract class TaggableTraitTestCase extends TaggableTestCase
{
    const ARTICLE = 'Anh\Taggable\Fixtures\ArticleWithTrait';

    public function setUp()
    {
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            $this->markTestSkipped('PHP 5.4 required for traits.');

            return;
        }

        parent::setUp();
    }
}

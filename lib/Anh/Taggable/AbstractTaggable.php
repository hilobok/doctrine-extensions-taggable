<?php

namespace Anh\Taggable;

use Anh\Taggable\Entity\Tag;
use Anh\Taggable\TaggableTrait;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * AbstractTaggable class implements basic functionalities for taggable behaviour.
 * All taggable entities should inherit it or use TaggableTrait otherwise.
 */
abstract class AbstractTaggable implements TaggableInterface
{
    use TaggableTrait;
}

# Doctrine2 taggable behavior extension

[![Build Status](https://travis-ci.org/hilobok/doctrine-extensions-taggable.png?branch=master)](https://travis-ci.org/hilobok/doctrine-extensions-taggable) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/8cdcc0f0-1f7b-4cb1-91ea-7b3bd3f899cc/mini.png)](https://insight.sensiolabs.com/projects/8cdcc0f0-1f7b-4cb1-91ea-7b3bd3f899cc)

## Installation
```json
    "require": {
        "anh/doctrine-extensions-taggable": "~1.0"
    }
```

### Symfony

There is bundle for that — [AnhTaggableBundle](https://github.com/hilobok/AnhTaggableBundle)

#### Basic integration

edit **app/config/config.yml**:

```yaml
doctrine:
    dbal:
# ...

    orm:
# ...
        mappings:
            taggable:
                type: annotation
                alias: AnhTaggable
                prefix: Anh\Taggable\Entity
                dir: "%kernel.root_dir%/../vendor/anh/doctrine-extensions-taggable/lib/Anh/Taggable/Entity"
```

edit **Acme/DemoBundle/Resources/config/services.yml** to add a service and event subscriber

```yaml
# ...
services:
# ...
    anh_taggable.manager:
        class: Anh\Taggable\TaggableManager
        arguments:
            - @doctrine.orm.entity_manager
            - Anh\Taggable\Entity\Tag
            - Anh\Taggable\Entity\Tagging

    anh_taggable.subscriber:
        class: Anh\TaggableBundle\TaggableSubscriber
        arguments:
            - @service_container
        tags:
            - { name: doctrine.event_subscriber }
```

## Example

### Create taggable entity

```php
<?php

use Anh\Taggable\TaggableInterface;
use Anh\Taggable\AbstractTaggable;

class Article extends AbstractTaggable implements TaggableInterface
{
    // ...

    public function getTaggableType()
    {
        return 'article';
    }
}
```

### Using taggable extension

```php
<?php

use Anh\Taggable\TaggableManager;
use Anh\Taggable\TaggableSubscriber;

// create entity manager
// $em = EntityManager::create(...);

// create taggable manager
$taggableManager = new TaggableManager(
    $em, 'Anh\Taggable\Entity\Tag', 'Anh\Taggable\Entity\Tagging'
);

// add event subscriber
$em->getEventManager()->addEventSubscriber(
    new TaggableSubscriber($taggableManager)
);

// create and fill entity
$article = new Article();
// $article->setTitle(...);

// add tag
$tag = $taggableManager->loadOrCreateTag('This is a tag');
$article->addTag($tag);

// or add multiple tags
$tags = $taggableManager->loadOrCreateTags(array('tag1', 'tag2', 'tag3'));
$article->addTags($tags);

// see Anh\Taggable\AbstractTaggable for more

$em->persist($article);
$em->flush();

// ...

// getting tagged resources
$repository = $taggableManager->getTaggingRepository();

$tag = $taggableManager->loadOrCreateTag('Some tag')
// returns all resources tagged with tag 'Some tag'
$resources = $repository->getResourcesWithTag($tag);
// returns only articles with tag 'Some tag'
$articles = $repository->getResourcesWithTypeAndTag('article', $tag);
```

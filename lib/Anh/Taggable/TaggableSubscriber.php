<?php

namespace Anh\Taggable;

use Anh\Taggable\TaggableManager;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Event\OnClearEventArgs;

class TaggableSubscriber implements EventSubscriber
{
    protected $manager;

    /**
     * Constructor
     *
     * @param TagManager $manager
     */
    public function __construct(TaggableManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @see Doctrine\Common\EventSubscriber
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::postLoad,
            Events::postPersist,
            Events::preFlush,
            Events::preRemove,
            Events::onClear
        );
    }

    /**
     * Inject TaggableManager into resource
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $resource = $args->getEntity();

        if ($resource instanceof TaggableInterface) {
            $resource->setTaggableManager($this->manager);
        }
    }

    /**
     * Sync tagging when id already known for new entities
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $resource = $args->getEntity();

        if ($resource instanceof TaggableInterface) {
            $this->manager->syncTagging($resource, true);
        }
    }

    /**
     * Sync tagging for all taggable entities
     */
    public function preFlush(PreFlushEventArgs $args)
    {
        $map = $args
            ->getEntityManager()
            ->getUnitOfWork()
            ->getIdentityMap()
        ;

        $keys = array_filter(
            array_keys($map),
            function($class) {
                return is_subclass_of($class, '\Anh\Taggable\TaggableInterface');
            }
        );

        foreach ($keys as $key) {
            foreach ($map[$key] as $resource) {
                $this->manager->syncTagging($resource);
            }
        }
    }

    /**
     * Remove assocated tagging.
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $resource = $args->getEntity();

        if ($resource instanceof TaggableInterface) {
            $this->manager->deleteTagging($resource);
        }
    }

    /**
     * Clear taggingMap when clearing entity manager
     */
    public function onClear(OnClearEventArgs $args)
    {
        $class = $args->getEntityClass();

        if (empty($class) or is_subclass_of('Anh\Taggable\TaggableInterface', $class)) {
            $this->manager->clearTaggingMap();

            return;
        }
    }
}

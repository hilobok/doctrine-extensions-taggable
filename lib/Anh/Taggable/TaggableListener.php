<?php

namespace Anh\Taggable;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;

class TaggableListener implements EventSubscriber
{
    protected $manager;

    /**
     * Constructor
     *
     * @param TagManager $manager
     */
    public function __construct(/*TagManager*/ $manager)
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
            Events::onFlush,
            Events::postPersist,
            Events::postRemove
        );
    }

    /**
     * Inject TaggableManager into resource for lazy loading of tags
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $resource = $args->getEntity();

        if (!$resource instanceof TaggableInterface) {
            return;
        }

        $resource->setTaggableManager($this->manager);
    }

    /**
     * Id for new entities not known at this stage (at least for mysql) so we process only entities scheduled for updates
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $resource) {
            if (!$resource instanceof TaggableInterface) {
                continue;
            }

            $manager->saveTagging($resource);
        }
    }

    /**
     * Id already known for new entities
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $resource = $args->getEntity();

        if (!$resource instanceof TaggableInterface) {
            return;
        }

        $manager->saveTagging($resource);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        $resource = $args->getEntity();

        if (!$resource instanceof TaggableInterface) {
            return;
        }

        $this->manager->deleteTagging($resource);
    }
}
<?php

namespace Anh\DoctrineExtensions\Taggable;

use Doctrine\ORM\EntityManager;

class TaggableManager
{
    /**
     * Doctrine entity manager for db interaction
     * @var EntityManager
     */
    protected $em;

    /**
     * Class name of tag entity
     * @var string
     */
    protected $tagClass;

    /**
     * Class name of tagging entity
     * @var string
     */
    protected $taggingClass;

    protected $tagRepository;
    protected $taggingRepository;

    public function __construct(EntityManager $em, $tagClass, $taggingClass)
    {
        $this->em = $em;
        $this->tagClass = $tagClass;
        $this->taggingClass = $taggingClass;
    }

    public function getTagClass()
    {
        return $this->tagClass;
    }

    public function getTaggingClass()
    {
        return $this->taggingClass;
    }

    public function createTag()
    {
        $class = $this->getTagClass();

        return new $class;
    }

    public function createTagging()
    {
        $class = $this->getTaggingClass();

        return new $class;
    }

    public function loadOrCreateTag($name)
    {
        $tags = $this->loadOrCreateTags((array) $name);

        return reset($tags);
    }

    public function loadOrCreateTags(array $names)
    {
        $names = array_unique(array_map('trim', $names));

        $tags = $this->em->createQueryBuilder()
            ->select('tag')
            ->from($this->tagClass, 'tag')
            ->where('tag.name in :names')
            ->setParameter('names', $names)
            ->getQuery()
            ->getResult()
        ;

        $existent = array_map(function($tag) { return $tag->getName(); }, $tags);
        $missing = array_udiff($names, $existent, 'strcasecmp');

        if (count($missing)) {
            $last = $missing[count($missing) - 1];

            foreach ($missing as $name) {
                $tag = $this->createTag();
                $tag->setName($name);
                $this->save($tag, $last == $name);
                $tags[] = $tag;
            }
        }

        return $tags;
    }

    /**
     * Save entity
     * @return TaggableManager
     */
    public function save($entity, $flush = true)
    {
        $this->em->persist($entity);

        if ($flush) {
            $this->em->flush();
        }

        return $this;
    }

    /**
     * Delete entity
     * @return TaggableManager
     */
    public function delete($entity, $flush = true)
    {
        $this->em->remove($entity);

        if ($flush) {
            $this->em->flush();
        }

        return $this;
    }

    public function loadTagging(TaggableInterface $resource)
    {
        return $this->em->createQueryBuilder()
            ->select('tag')
            ->from($this->tagClass, 'tag')
            ->innerJoin(
                'tag.tagging', 'tagging', Expr\Join::WITH,
                'tagging.resourceId = :resourceId AND tagging.resourceType = :resourceType'
            )
            ->setParameters(array(
                'resourceId' => $resource->getTaggableId(),
                'resourceType' => $resource->getTaggableType()
            ))
            ->getQuery()
            ->getResult()
        ;
    }

    public function saveTagging(TaggableInterface $resource)
    {
        $new = $resource->getTags();
        $old = $this->loadTagging($resource);

        $compare = function($tag1, $tag2) { return strcasecmp($tag1->getName(), $tag2->getName()); };

        $removed = array_udiff($old, $new, $compare);
        if (!empty($removed)) {
            $removed = array_map(function($tag) { return $tag->getId(); }, $removed);

            $this->em->createQueryBuilder()
                ->delete($this->taggingClass, 'tagging')
                ->where('tagging.tagId in :removed')
                ->andWhere('tagging.resourceId = :resourceId')
                ->andWhere('tagging.resourceType = :resourceType')
                ->setParameters(array(
                    'removed' => $removed,
                    'resourceId' => $resource->getTaggableId(),
                    'resourceType' => $resource->getTaggableType()
                ))
                ->getQuery()
                ->getResult()
            ;
        }

        $added = array_udiff($new, $old, $compare);
        foreach ($added as $tag) {
            $tagging = $this->createTagging();
            $tagging->setResource($resource);
            $tagging->setTag($tag);
            $this->save($tagging);
        }
    }

    public function deleteTagging(TaggableInterface $resource)
    {
        $this->em->createQueryBuilder()
            ->delete($this->taggingClass, 'tagging')
            ->where('tagging.resourceId = :resourceId')
            ->andWhere('tagging.resourceType = :resourceType')
            ->setParameters(array(
                'resourceId' => $resource->getTaggableId(),
                'resourceType' => $resource->getTaggableType()
            ))
            ->getQuery()
            ->getResult()
        ;
    }
}
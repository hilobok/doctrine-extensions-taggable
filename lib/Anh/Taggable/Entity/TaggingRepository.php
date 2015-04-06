<?php

namespace Anh\Taggable\Entity;

use Anh\DoctrineResource\ORM\ResourceRepository;

class TaggingRepository extends ResourceRepository
{
    /**
     * Returns resource ids tagged with tag.
     *
     * @param Anh\Taggable\Entity\Tag $tag
     *
     * @return array
     */
    public function getResourcesWithTag(Tag $tag, $group = false)
    {
        $taggingList = $this->createQueryBuilder('tagging')
            ->select(
                'tagging.resourceType',
                'tagging.resourceId'
            )
            ->where('tagging.tag = :tag')
            ->setParameter('tag', $tag)
            ->getQuery()
            ->getResult()
        ;

        if ($group) {
            $result = array();

            foreach ($taggingList as $tagging) {
                $result[$tagging['resourceType']][] = $tagging['resourceId'];
            }

            $taggingList = $result;
        }

        return $taggingList;
    }

    /**
     * Returns resource ids with given type and tagged with tag.
     *
     * @param Anh\Taggable\Entity\Tag $tag
     *
     * @return array
     */
    public function getResourcesWithTypeAndTag($type, Tag $tag)
    {
        $taggingList = $this->createQueryBuilder('tagging')
            ->select('tagging.resourceId')
            ->where('tagging.tag = :tag')
            ->andWhere('tagging.resourceType = :resourceType')
            ->setParameters(array(
                'tag' => $tag,
                'resourceType' => $type
            ))
            ->getQuery()
            ->getResult()
        ;

        $result = array();

        foreach ($taggingList as $tagging) {
            $result[] = $tagging['resourceId'];
        }

        return $result;
    }

    /**
     *
     */
    public function getTagsAndResourcesCount($resourceType = null, $threshold = null)
    {
        $queryBuilder = $this->createQueryBuilder('tagging')
            ->select(
                'tagging AS taggingEntity',
                'count(tagging.tag) AS resourcesCount'
            )
            ->orderBy('resourcesCount', 'DESC')
            ->groupBy('tagging.tag')
        ;

        if (!empty($threshold)) {
            $queryBuilder
                ->having('resourcesCount > :threshold')
                ->setParameter('threshold', $threshold)
            ;
        }

        if (!empty($resourceType)) {
            $queryBuilder
                ->where('tagging.resourceType = :resourceType')
                ->setParameter('resourceType', $resourceType)
            ;
        }

        $taggingList = $queryBuilder
            ->getQuery()
            ->getResult()
        ;

        foreach ($taggingList as $tagging) {
            $result[] = array(
                'tag' => $tagging['taggingEntity']->getTag(),
                'count' => $tagging['resourcesCount']
            );
        }

        return $result;
    }
}

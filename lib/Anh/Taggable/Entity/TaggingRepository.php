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
    public function getTagsAndResourcesCount()
    {
        $taggingList = $this->createQueryBuilder('tagging')
            ->select(
                'tagging AS taggingEntity',
                // 'IDENTITY(tagging.tag) AS tag'
                'count(tagging.tag) AS resourcesCount'
            )
            ->groupBy('tagging.tag')
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

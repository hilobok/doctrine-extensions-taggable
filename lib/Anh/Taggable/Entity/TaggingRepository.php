<?php

namespace Anh\Taggable\Entity;

use Doctrine\ORM\EntityRepository;

class TaggingRepository extends EntityRepository
{
    /**
     * Returns resource ids tagged with tag.
     *
     * @param Anh\Taggable\Entity\Tag $tag
     *
     * @return array
     */
    public function getResourcesWithTag(Tag $tag)
    {
        $taggingList = $this->createQueryBuilder('tagging')
            ->select('tagging.resourceId')
            ->where('tagging.tag = :tag')
            ->setParameter('tag', $tag)
            ->getQuery()
            ->getResult()
        ;

        return $this->hydrateResult($taggingList, 'resourceId');
    }

    /**
     * Returns resource ids with given type tagged with tag.
     *
     * @param Anh\Taggable\Entity\Tag $tag
     *
     * @return array
     */
    public function getResourcesByTypeWithTag($type, Tag $tag)
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

        return $this->hydrateResult($taggingList, 'resourceId');
    }

    protected function hydrateResult($array, $field)
    {
        $result = array();

        foreach ($array as $value) {
            $result[] = $value[$field];
        }

        return $result;
    }
}
<?php

namespace Anh\Taggable\Entity;

use Doctrine\ORM\EntityRepository;

class TagRepository extends EntityRepository
{
    /**
     * Search tags by given query.
     *
     * @param string  $query Search query
     * @param boolean $exact Search exact query if true
     * @param boolean $field Return this field instead of whole entity
     * @param integer $limit Limit result set
     *
     * @return array
     */
    public function search($query, $exact = false, $field = null, $limit = null)
    {
        if (!$exact and strpos($query, '%') === false) {
            $query = sprintf('%%%s%%', $query);
        }

        $query = $this->createQueryBuilder('tag')
            ->where('tag.name like :query')
            ->setParameter('query', $query)
        ;

        if ($field) {
            $query->select(sprintf('tag.%s', $field));
        }

        if ($limit) {
            $query->setMaxResults($limit);
        }

        $result = $query->getQuery()->getResult();

        if ($field) {
            $rows = $result;
            $result = array();

            foreach ($rows as $row) {
                $result[] = $row[$field];
            }
        }

        return $result;
    }
}
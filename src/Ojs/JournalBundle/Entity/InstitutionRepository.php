<?php

namespace Ojs\JournalBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

/**
 * IssueRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class InstitutionRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function getOnlyNames()
    {
        $qb = $this->createQueryBuilder("i");
        $qb->select("i.name,i.slug,i.verified");
        return $qb->getQuery()->getResult(Query::HYDRATE_OBJECT);
    }
}
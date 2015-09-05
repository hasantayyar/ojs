<?php

namespace Ojs\JournalBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Class PublisherTypesRepository.
 */
class PublisherTypesRepository extends EntityRepository
{
    /**
     * @param array $data
     *
     * @return Publisher[]
     */
    public function getByIds(array $data)
    {
        $qb = $this->createQueryBuilder('j');
        $qb->where(
            $qb->expr()->in('j.id', ':data')
        )
            ->setParameter('data', $data);

        return $qb->getQuery()->getResult();
    }
}

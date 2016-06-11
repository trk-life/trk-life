<?php

namespace TrkLife\Repository;

use Doctrine\ORM\EntityRepository;
use TrkLife\Entity\Token;

/**
 * Class TokenRepository
 *
 * @package TrkLife\Entity
 * @author George Webb <george@webb.uno>
 */
class TokenRepository extends EntityRepository
{
    /**
     * Finds a single token entity by a token string
     *
     * @param string $token The token string
     * @return Token | null The token entity
     */
    public function findOneByToken($token)
    {
        $queryBuilder = $this->createQueryBuilder('t');

        $queryBuilder->where(
            $queryBuilder->expr()->andX(
                't.token = :token',
                $queryBuilder->expr()->orX(
                    't.created + t.expires_after > :current_time',
                    't.last_accessed > :current_time_plus_1_hour'
                )
            )
        );

        $queryBuilder->setParameter('token', Token::hashToken($token));
        $queryBuilder->setParameter('current_time', (new \DateTime())->getTimestamp());
        $queryBuilder->setParameter('current_time_plus_1_hour', (new \DateTime())->sub(new \DateInterval('PT1H'))->getTimestamp());

        return $queryBuilder->getQuery()->setMaxResults(1)->getOneOrNullResult();
    }
}

<?php

namespace TrkLife\Repository;

use Doctrine\ORM\EntityRepository;
use TrkLife\Entity\ForgottenPassword;

/**
 * Class ForgottenPasswordRepository
 *
 * @package TrkLife\Entity
 * @author George Webb <george@webb.uno>
 */
class ForgottenPasswordRepository extends EntityRepository
{
    /**
     * Finds a single forgotten password entity by a token string
     *
     * @param string $token The token string
     * @return ForgottenPassword | null The forgotten password entity
     */
    public function findOneByToken($token)
    {
        $queryBuilder = $this->createQueryBuilder('f');
        $queryBuilder->where('f.token = :token');
        $queryBuilder->setParameter('token', ForgottenPassword::hashToken($token));
        return $queryBuilder->getQuery()->setMaxResults(1)->getOneOrNullResult();
    }
}

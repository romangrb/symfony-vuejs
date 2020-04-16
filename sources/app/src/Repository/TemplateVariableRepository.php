<?php

namespace App\Repository;

use App\Entity\TemplateVariable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method TemplateVariable|null find($id, $lockMode = null, $lockVersion = null)
 * @method TemplateVariable|null findOneBy(array $criteria, array $orderBy = null)
 * @method TemplateVariable[]    findAll()
 * @method TemplateVariable[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TemplateVariableRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TemplateVariable::class);
    }

    /**
     * Get all users variables template
     *
     * @param int $user_id
     * @return TemplateVariable[] Returns an array of TemplateVariable objects
     */
    public function getUserTemplateVariables(int $user_id)
    {
        return $this->createQueryBuilder('tv')
            ->where('tv.user = :user_id')
            ->setParameter('user_id', $user_id)
            ->orderBy('tv.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Find users variable template
     *
     * @param int $user_id
     * @param int $id
     * @return TemplateVariable|null Returns an array of TemplateVariable objects
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findUserTemplateVariable(int $user_id, int $id): ?TemplateVariable
    {
        return $this->createQueryBuilder('tv')
            ->where('tv.user = :user_id')
            ->andWhere('tv.id = :id')
            ->setParameter('user_id', $user_id)
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}

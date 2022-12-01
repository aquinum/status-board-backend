<?php

namespace App\Repository;

use App\Entity\ApiTokens;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ApiTokens>
 *
 * @method ApiTokens|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApiTokens|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApiTokens[]    findAll()
 * @method ApiTokens[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApiTokensRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApiTokens::class);
    }

    public function save(ApiTokens $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ApiTokens $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneByServiceName(string $serviceName): ?ApiTokens
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.serviceName = :serviceName')
            ->setParameter('serviceName', 'netatmo')
            ->getQuery()
            ->getOneOrNullResult();
    }
}

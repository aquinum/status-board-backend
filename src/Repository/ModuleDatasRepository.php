<?php

namespace App\Repository;

use App\Entity\ModuleDatas;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ModuleDatas>
 *
 * @method ModuleDatas|null find($id, $lockMode = null, $lockVersion = null)
 * @method ModuleDatas|null findOneBy(array $criteria, array $orderBy = null)
 * @method ModuleDatas[]    findAll()
 * @method ModuleDatas[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModuleDatasRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ModuleDatas::class);
    }

    /**
     * @param string $moduleId
     * @return array<int, ModuleDatas>
     */
    public function findByModule(string $moduleId): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.moduleId = :moduleId')
            ->setParameter('moduleId', $moduleId)
            ->addOrderBy('m.updatedAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function save(ModuleDatas $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ModuleDatas $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ModuleDatas[] Returns an array of ModuleDatas objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ModuleDatas
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

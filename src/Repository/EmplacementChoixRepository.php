<?php

namespace App\Repository;

use App\Entity\EmplacementChoix;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EmplacementChoix>
 *
 * @method EmplacementChoix|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmplacementChoix|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmplacementChoix[]    findAll()
 * @method EmplacementChoix[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmplacementChoixRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmplacementChoix::class);
    }

    public function save(EmplacementChoix $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EmplacementChoix $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return EmplacementChoix[] Returns an array of EmplacementChoix objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?EmplacementChoix
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

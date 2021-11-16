<?php

namespace App\Repository;

use App\Entity\Computer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Computer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Computer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Computer[]    findAll()
 * @method Computer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ComputerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Computer::class);
    }

    
    // public function findAllQuery($date): Query
    // {
    //     return $this->createQueryBuilder('c')
    //         ->select('c')
    //         ->from('computer', 'c')
    //         ->leftJoin("c.attribution", "attribution")
    //         ->where("attribution.date LIKE :val")
    //         ->setParameter("val", '%' . $date . '%')
    //         ->getQuery();
    // }

    public function findAllWithAttributions($date){
        $qb = $this->GetEntityManager()->createQueryBuilder();
        $qb
            ->select('c', 'a')
            ->from('App\Entity\Computer', 'c')
            ->leftJoin('c.attributions', 'a', 'WITH', 'a.date = :date')
            ->setParameter('date', $date);
            
        return $qb->getQuery()->getArrayResult();
    }

    // /**
    //  * @return Computer[] Returns an array of Computer objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Computer
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

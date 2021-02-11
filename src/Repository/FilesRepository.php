<?php

namespace App\Repository;

use App\Entity\Files;
use App\Entity\Documents;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Files|null find($id, $lockMode = null, $lockVersion = null)
 * @method Files|null findOneBy(array $criteria, array $orderBy = null)
 * @method Files[]    findAll()
 * @method Files[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FilesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Files::class);
    }

    // /**
    //  * @return Files[] Returns an array of Files objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Files
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findAllFilesByUser($user){
       //select user_id, name, path, extension, size, type, description, documents_id from documents, files where documents_id in (select id from documents where user_id=1);
        return $this->createQueryBuilder('f')
        ->join ('f.documents','d')
        ->andWhere('d.user = :val')
        ->setParameter('val', $user)
        ->orderBy('f.id', 'ASC')
        ->getQuery()
        ->getResult()
    ;
    ;

    }

    public function findAllFilesByDocument($document){
       
        return $this->createQueryBuilder('f')
        ->andWhere('f.documents = :val')
        ->setParameter('val', $document)
        ->orderBy('f.id', 'ASC')
        //->setMaxResults(35)
        ->getQuery()
        ->getResult()
    ;
    ;

    }

   
}

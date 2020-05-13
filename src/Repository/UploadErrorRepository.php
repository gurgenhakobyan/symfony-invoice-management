<?php

namespace App\Repository;

use App\Entity\UploadError;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UploadError|null find($id, $lockMode = null, $lockVersion = null)
 * @method UploadError|null findOneBy(array $criteria, array $orderBy = null)
 * @method UploadError[]    findAll()
 * @method UploadError[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UploadErrorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UploadError::class);
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return UploadError[] Returns an array of UploadError objects
     */
    public function listUploadErrors(int $limit, int $offset)
    {
        return $this->createQueryBuilder('ie')
            ->orderBy('ie.id', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult()
            ;
    }

}

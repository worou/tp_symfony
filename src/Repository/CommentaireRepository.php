<?php

namespace App\Repository;

use App\Entity\Commentaire;
use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Commentaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commentaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commentaire[]    findAll()
 * @method Commentaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commentaire::class);
    }

    /*

    SELECT * FROM commentaire c
    ON c.id_article = :id
    setParameter(a.id = 'id')
    INNER JOIN article a
    
    */
    // src/Repository/ProductRepository.php
    public function getCommentaireByArticle($id)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.article = :id')
            ->innerJoin('App\Entity\Article','a')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult()
        ;
    }


// public function getCommentairesByArticle($articleId)
// {
//     $entityManager = $this->getEntityManager();

//     $query = $entityManager->createQuery(
//         'SELECT a,c
//         FROM App\Entity\Article c
//         INNER JOIN a.commentaire c
//         WHERE a.id = :id'
//     )->setParameter('id', $articleId);

//     return $query->getResult();
// }


    // /**
    //  * @return Commentaire[] Returns an array of Commentaire objects
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
    public function findOneBySomeField($value): ?Commentaire
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

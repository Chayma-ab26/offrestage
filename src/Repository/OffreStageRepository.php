<?php

namespace App\Repository;

use App\Entity\OffreStage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OffreStage>
 */
class OffreStageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OffreStage::class);
    }
    public function countOffres(): int
    {
        return $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function findAllWithUserDetails()
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.entreprise', 'u') // Joindre la relation avec l'entité User (entreprise)
            ->addSelect('u') // Sélectionner les données de l'entreprise
            ->getQuery()
            ->getResult();
    }
    public function findBySecteuractivite(string $secteuractivite)
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.entreprise', 'e')
            ->andWhere('e.secteuractivite = :secteuractivite')
            ->setParameter('secteuractivite', $secteuractivite)
            ->getQuery()
            ->getResult();
    }
    public function findBySecteur($secteur)
    {
        if (!$secteur) {
            // Si aucun secteur n'est sélectionné, retourner toutes les offres
            return $this->findAll();
        }

        // Rechercher les offres en fonction du secteur
        return $this->createQueryBuilder('o')
            ->andWhere('o.secteur = :secteur')
            ->setParameter('secteur', $secteur)
            ->getQuery()
            ->getResult();
    }

    /**
    //     * @return OffreStage[] Returns an array of OffreStage objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('o.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?OffreStage
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

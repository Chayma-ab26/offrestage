<?php

namespace App\Repository;

use App\Entity\Candidature;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Candidature>
 */
class CandidatureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Candidature::class);
    }
    public function countSubmittedCandidatures(): int
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function findAllWithRelations(): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.etudiant', 'e') // Charger les étudiants associés
            ->addSelect('e')
            ->leftJoin('c.offreStage', 'o') // Charger les offres de stage associées
            ->addSelect('o')
            ->getQuery()
            ->getResult();


        return $this->render('candidature/index.html.twig', [
            'candidatures' => $candidatures,
        ]);
    }
        public function countAccepted(): int
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.status = :status')
            ->setParameter('status', 'accepted')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countCandidaturesByMonth(): array
    {
        $sql = "
        SELECT 
            DATE_FORMAT(c.date_soumission, '%Y') AS year, 
            DATE_FORMAT(c.date_soumission, '%m') AS month, 
            COUNT(c.id) AS count
        FROM candidature c
        GROUP BY year, month
        ORDER BY year ASC, month ASC
    ";

        return $this->getEntityManager()
            ->getConnection()
            ->executeQuery($sql)
            ->fetchAllAssociative();
    }

    public function findAllWithRelation(): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.offreStage', 'o')->addSelect('o')
            ->leftJoin('c.user', 'u')->addSelect('u')
            ->getQuery()
            ->getResult();
    }
    public function countCandidaturesByStudent($etudiant)
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.etudiant = :etudiant')
            ->setParameter('etudiant', $etudiant)
            ->getQuery()
            ->getSingleScalarResult();
    }
    // src/Repository/CandidatureRepository.php
    public function findAcceptedCandidatures()
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.status = :status')
            ->setParameter('status', 'accepted') // Adaptez le statut selon vos valeurs
            ->orderBy('c.dateSoumission', 'DESC') // Optionnel : trier par date ou autre champ
            ->getQuery()
            ->getResult();
    }





    //    /**
    //     * @return Candidature[] Returns an array of Candidature objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Candidature
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

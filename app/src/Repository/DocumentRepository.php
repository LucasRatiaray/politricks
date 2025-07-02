<?php

namespace App\Repository;

use App\Entity\Document;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Document::class);
    }

    public function findWithFilters(string $search = '', string $type = '', string $date = '', string $confidentiality = ''): array
    {
        $qb = $this->createQueryBuilder('d');

        if (!empty($search)) {
            $qb->andWhere('d.nom LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        if (!empty($confidentiality)) {
            $qb->andWhere('d.niveauConfidentialite = :confidentiality')
               ->setParameter('confidentiality', $confidentiality);
        }

        if (!empty($date)) {
            $now = new \DateTime();
            switch ($date) {
                case 'today':
                    $startOfDay = new \DateTime('today');
                    $endOfDay = new \DateTime('tomorrow');
                    $qb->andWhere('d.dateCreation >= :startOfDay AND d.dateCreation < :endOfDay')
                       ->setParameter('startOfDay', $startOfDay)
                       ->setParameter('endOfDay', $endOfDay);
                    break;
                case 'week':
                    $weekStart = new \DateTime('-7 days');
                    $qb->andWhere('d.dateCreation >= :weekStart')
                       ->setParameter('weekStart', $weekStart);
                    break;
                case 'month':
                    $monthStart = new \DateTime('-30 days');
                    $qb->andWhere('d.dateCreation >= :monthStart')
                       ->setParameter('monthStart', $monthStart);
                    break;
            }
        }

        if (!empty($type)) {
            switch ($type) {
                case 'image':
                    $qb->andWhere('d.nom LIKE :imageExt1 OR d.nom LIKE :imageExt2 OR d.nom LIKE :imageExt3 OR d.nom LIKE :imageExt4')
                       ->setParameter('imageExt1', '%.jpg')
                       ->setParameter('imageExt2', '%.jpeg')
                       ->setParameter('imageExt3', '%.png')
                       ->setParameter('imageExt4', '%.gif');
                    break;
                case 'document':
                    $qb->andWhere('d.nom LIKE :docExt1 OR d.nom LIKE :docExt2 OR d.nom LIKE :docExt3 OR d.nom LIKE :docExt4')
                       ->setParameter('docExt1', '%.pdf')
                       ->setParameter('docExt2', '%.doc')
                       ->setParameter('docExt3', '%.docx')
                       ->setParameter('docExt4', '%.txt');
                    break;
                case 'video':
                    $qb->andWhere('d.nom LIKE :vidExt1 OR d.nom LIKE :vidExt2 OR d.nom LIKE :vidExt3')
                       ->setParameter('vidExt1', '%.mp4')
                       ->setParameter('vidExt2', '%.avi')
                       ->setParameter('vidExt3', '%.mov');
                    break;
                case 'audio':
                    $qb->andWhere('d.nom LIKE :audioExt1 OR d.nom LIKE :audioExt2 OR d.nom LIKE :audioExt3')
                       ->setParameter('audioExt1', '%.mp3')
                       ->setParameter('audioExt2', '%.wav')
                       ->setParameter('audioExt3', '%.m4a');
                    break;
            }
        }

        return $qb->orderBy('d.dateCreation', 'DESC')
                  ->getQuery()
                  ->getResult();
    }
}
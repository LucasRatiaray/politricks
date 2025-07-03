<?php

namespace App\Controller;

use App\Entity\Document;
use App\Enum\DocumentNiveauConfidentialiteEnum;
use App\Enum\DocumentLangueDocumentEnum;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\DelitRepository;
use App\Entity\Delit;

class DocumentController extends AbstractController
{
    #[Route('/medias', name: 'medias_list')]
    public function index(Request $request, DocumentRepository $documentRepository): Response
    {
    $search = $request->get('search', '');
    $typeFilter = $request->get('type', '');
    $dateFilter = $request->get('date', '');
    $confidentialityFilter = $request->get('confidentiality', '');

    $media = $documentRepository->findWithFiltersAndDelits($search, $typeFilter, $dateFilter, $confidentialityFilter);
    
    return $this->render('media/media.html.twig', [
        'media' => $media,
        'search' => $search,
        'typeFilter' => $typeFilter,
        'dateFilter' => $dateFilter,
        'confidentialityFilter' => $confidentialityFilter,
    ]);
    }

    #[Route('/media/add', name: 'media_add', methods: ['POST'])]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $file = $request->files->get('file');
        if (!$file) {
            $this->addFlash('error', 'Aucun fichier sélectionné');
            return $this->redirectToRoute('medias_list');
        }

        $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $nom = $file->getClientOriginalName();
        $file->move($uploadDir, $nom);

        $document = new Document();
        $document->setNom($nom);
        $document->setChemin('/uploads/' . $nom);
        $document->setDateCreation(new \DateTime());
        $document->setEstArchive(false);
        $document->setNiveauConfidentialite(DocumentNiveauConfidentialiteEnum::Public);
        $document->setLangueDocument(DocumentLangueDocumentEnum::FR);

        $em->persist($document);
        $em->flush();

        $this->addFlash('success', 'Document ajouté avec succès !');
        return $this->redirectToRoute('medias_list');
    }

    #[Route('/media/edit/{id}', name: 'media_edit', methods: ['GET'])]
    public function edit(Document $document, DelitRepository $delitRepository): Response
    {
    $delits = $delitRepository->findAll();
    $delitsData = [];
    foreach ($delits as $delit) {
        $delitsData[] = [
            'id' => $delit->getId(),
            'label' => sprintf(
                '#%d - %s (%s) - %s', 
                $delit->getId(),
                $delit->getType()->value,
                $delit->getStatut()->value,
                substr($delit->getDescription(), 0, 50) . '...'
            )
        ];
    }

    return $this->json([
        'id' => $document->getId(),
        'nom' => $document->getNom(),
        'delitId' => $document->getDelit() ? $document->getDelit()->getId() : null,
        'delits' => $delitsData,
        'niveauConfidentialite' => $document->getNiveauConfidentialite()->value,
        'langueDocument' => $document->getLangueDocument()->value
    ]);
}

    #[Route('/media/update/{id}', name: 'media_update', methods: ['POST'])]
public function update(Document $document, Request $request, EntityManagerInterface $em): Response
{
    $nom = $request->get('nom');
    $delitId = $request->get('delit_id');
    $confidentialite = $request->get('confidentialite');
    $langue = $request->get('langue');
    
    if ($nom) {
        $document->setNom($nom);
    }
    
    if ($confidentialite) {
        $document->setNiveauConfidentialite(DocumentNiveauConfidentialiteEnum::from($confidentialite));
    }
    
    if ($langue) {
        $document->setLangueDocument(DocumentLangueDocumentEnum::from($langue));
    }
    
    if (!empty($delitId)) {
        $delit = $em->getRepository(Delit::class)->find($delitId);
        if ($delit) {
            $document->setDelit($delit);
        }
    } else {
        $document->setDelit(null);
    }
    
    $em->persist($document);
    $em->flush();
    $em->clear();
    
    return $this->json(['success' => true]);
}

    #[Route('/media/delete/{id}', name: 'media_delete', methods: ['POST'])]
    public function delete(Document $document, EntityManagerInterface $em): Response
    {
        $filePath = $this->getParameter('kernel.project_dir') . '/public' . $document->getChemin();
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        $em->remove($document);
        $em->flush();

        return $this->json(['success' => true]);
    }

    private function getDocumentType(Document $document): string
    {
        $extension = strtolower(pathinfo($document->getNom(), PATHINFO_EXTENSION));
        
        return match ($extension) {
            'jpg', 'jpeg', 'png', 'gif', 'bmp' => 'image',
            'mp4', 'avi', 'mov', 'mkv' => 'video',
            'mp3', 'wav', 'm4a', 'aac' => 'audio',
            'pdf', 'doc', 'docx', 'txt' => 'document',
            default => 'fichier'
        };
    }
}
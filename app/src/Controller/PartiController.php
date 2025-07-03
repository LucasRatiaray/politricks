<?php

namespace App\Controller;

use App\Entity\Parti;
use App\Repository\PartiRepository;
use App\Repository\PoliticienRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PartiController extends AbstractController
{
    #[Route('/partis', name: 'partis_index', methods: ['GET'])]
    public function index(PartiRepository $partiRepository, Request $request): Response
    {
        $partis = $partiRepository->findAll();
        $selected = null;
        if ($request->isXmlHttpRequest() && $request->query->get('id')) {
            $selected = $partiRepository->find($request->query->get('id'));
            return $this->render('partis/components/parti_detail.html.twig', [
                'selected' => $selected,
            ]);
        }
        return $this->render('partis/partis.html.twig', [
            'partis' => $partis,
            'selected' => $partis[0] ?? null,
        ]);
    }

    #[Route('/partis/{id}', name: 'partis_detail', methods: ['GET'])]
    public function detail(Parti $parti): Response
    {
        return $this->render('partis/components/parti_detail.html.twig', [
            'selected' => $parti,
        ]);
    }
} 
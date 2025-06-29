<?php

namespace App\Controller;

use App\Entity\Partenaire;
use App\Entity\PartenairePhysique;
use App\Entity\PartenaireMoral;
use App\Entity\User;
use App\Enum\PartenaireNiveauRisqueEnum;
use App\Repository\PartenaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/partners')]
class PartnerController extends AbstractController
{
    #[Route('', name: 'app_partners')]
    public function index(PartenaireRepository $partenaireRepository): Response
    {
        $partners = $partenaireRepository->findAll();
        $selected = $partners[0] ?? null;

        return $this->render('partner/partner.html.twig', [
            'partners' => $partners,
            'selected' => $selected,
        ]);
    }

    #[Route('/add', name: 'app_partners_add', methods: ['POST'])]
    public function add(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return new JsonResponse(['success' => false, 'message' => 'Données invalides'], 400);
            }

            $type = $data['type'] ?? '';
            
            // Validation des champs requis selon le type
            if ($type === 'physique') {
                if (empty($data['prenom']) || empty($data['nomFamille'])) {
                    return new JsonResponse(['success' => false, 'message' => 'Prénom et nom de famille sont obligatoires pour une personne physique'], 400);
                }
            } elseif ($type === 'moral') {
                if (empty($data['raisonSociale']) || empty($data['formeJuridique'])) {
                    return new JsonResponse(['success' => false, 'message' => 'Raison sociale et forme juridique sont obligatoires pour une personne morale'], 400);
                }
            } else {
                return new JsonResponse(['success' => false, 'message' => 'Type de partenaire invalide'], 400);
            }

            // Validation des champs communs
            if (empty($data['email'])) {
                return new JsonResponse(['success' => false, 'message' => 'Email est obligatoire'], 400);
            }

            if ($type === 'physique') {
                $partner = new PartenairePhysique();
                $partner->setPrenom($data['prenom']);
                $partner->setNomFamille($data['nomFamille']);
                $partner->setNom($data['prenom'] . ' ' . $data['nomFamille']);
                
                if (!empty($data['dateNaissance'])) {
                    $partner->setDateNaissance(new \DateTime($data['dateNaissance']));
                }
                $partner->setLieuNaissance($data['lieuNaissance'] ?? null);
                $partner->setNationalite($data['nationalite'] ?? null);
                $partner->setProfession($data['profession'] ?? null);
                $partner->setNumeroSecu($data['numeroSecu'] ?? null);
                $partner->setNumeroCNI($data['numeroCNI'] ?? null);
                $partner->setSituationFamiliale($data['situationFamiliale'] ?? null);
                $partner->setCasierJudiciaire(isset($data['casierJudiciaire']) && $data['casierJudiciaire'] === 'on');
                $partner->setFortuneEstimee(!empty($data['fortuneEstimee']) ? $data['fortuneEstimee'] : null);
                
            } elseif ($type === 'moral') {
                $partner = new PartenaireMoral();
                $partner->setRaisonSociale($data['raisonSociale']);
                $partner->setNom($data['raisonSociale']);
                $partner->setFormeJuridique($data['formeJuridique']);
                $partner->setSiret($data['siret'] ?? null);
                $partner->setSecteurActivite($data['secteurActivite'] ?? null);
                $partner->setDirigeantPrincipal($data['dirigeantPrincipal'] ?? null);
                $partner->setChiffreAffaires(!empty($data['chiffreAffaires']) ? $data['chiffreAffaires'] : null);
                $partner->setNombreEmployes(!empty($data['nombreEmployes']) ? (int)$data['nombreEmployes'] : null);
                $partner->setPaysFiscal($data['paysFiscal'] ?? null);
                
                if (!empty($data['dateCreationEntreprise'])) {
                    $partner->setDateCreationEntreprise(new \DateTime($data['dateCreationEntreprise']));
                }
                $partner->setCapitalSocial(!empty($data['capitalSocial']) ? $data['capitalSocial'] : null);
            }

            // Champs communs
            $partner->setEmail($data['email']);
            $partner->setTelephone($data['telephone'] ?? null);
            $partner->setAdresse($data['adresse'] ?? null);
            $partner->setSiteWeb($data['siteWeb'] ?? null);
            $partner->setNotes($data['notes'] ?? null);
            $partner->setDateCreation(new \DateTime());
            $partner->setVille($data['ville'] ?? null);
            $partner->setCodePostal($data['codePostal'] ?? null);
            $partner->setPays($data['pays'] ?? null);
            $partner->setEstActif(true);
            $partner->setNombreDelitsImplique(0);
            
            if (!empty($data['datePremiereCollaboration'])) {
                $partner->setDatePremiereCollaboration(new \DateTime($data['datePremiereCollaboration']));
            }

            // Niveau de risque
            $niveauRisque = match($data['niveauRisque'] ?? '') {
                'faible' => PartenaireNiveauRisqueEnum::Faible,
                'modere' => PartenaireNiveauRisqueEnum::Modere,
                'eleve' => PartenaireNiveauRisqueEnum::Eleve,
                'tres_eleve' => PartenaireNiveauRisqueEnum::TresEleve,
                'tres_faible' => PartenaireNiveauRisqueEnum::TresFaible,
                default => PartenaireNiveauRisqueEnum::Modere,
            };
            $partner->setNiveauRisque($niveauRisque);

            $entityManager->persist($partner);
            $entityManager->flush();

            return new JsonResponse(['success' => true, 'message' => 'Partenaire ajouté avec succès']);

        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => 'Erreur lors de l\'ajout: ' . $e->getMessage()], 500);
        }
    }

    #[Route('/{id}/edit', name: 'app_partner_edit', methods: ['GET'])]
    public function edit(int $id, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        if (!$user || !$user instanceof User || !in_array('ROLE_ADMIN', $user->getRoles())) {
            return new JsonResponse(['success' => false, 'message' => 'Accès refusé']);
        }

        $partenaire = $em->getRepository(Partenaire::class)->find($id);
        
        if (!$partenaire) {
            return new JsonResponse(['success' => false, 'message' => 'Partenaire non trouvé']);
        }
        
        // Préparer les données du partenaire
        $partnerData = [
            'id' => $partenaire->getId(),
            'nom' => $partenaire->getNom(),
            'email' => $partenaire->getEmail(),
            'telephone' => $partenaire->getTelephone(),
            'niveauRisque' => $partenaire->getNiveauRisque()->value ?? 'modere',
            'adresse' => $partenaire->getAdresse(),
            'ville' => $partenaire->getVille(),
            'codePostal' => $partenaire->getCodePostal(),
            'pays' => $partenaire->getPays(),
            'siteWeb' => $partenaire->getSiteWeb(),
            'dateCreation' => $partenaire->getDateCreation() ? $partenaire->getDateCreation()->format('Y-m-d') : null,
            'datePremiereCollaboration' => $partenaire->getDatePremiereCollaboration() ? $partenaire->getDatePremiereCollaboration()->format('Y-m-d') : null,
            'nombreDelitsImplique' => $partenaire->getNombreDelitsImplique(),
            'estActif' => $partenaire->isEstActif(),
            'notes' => $partenaire->getNotes(),
            'commentairesInternes' => $partenaire->getCommentairesInternes(),
        ];
        
        // Ajouter les données spécifiques selon le type
        if ($partenaire instanceof PartenairePhysique) {
            $partnerData['type'] = 'Individuel';
            $partnerData['prenom'] = $partenaire->getPrenom();
            $partnerData['nomFamille'] = $partenaire->getNomFamille();
            $partnerData['dateNaissance'] = $partenaire->getDateNaissance() ? $partenaire->getDateNaissance()->format('Y-m-d') : null;
            $partnerData['lieuNaissance'] = $partenaire->getLieuNaissance();
            $partnerData['nationalite'] = $partenaire->getNationalite();
            $partnerData['profession'] = $partenaire->getProfession();
            $partnerData['numeroSecu'] = $partenaire->getNumeroSecu();
            $partnerData['numeroCNI'] = $partenaire->getNumeroCNI();
            $partnerData['situationFamiliale'] = $partenaire->getSituationFamiliale();
            $partnerData['casierJudiciaire'] = $partenaire->isCasierJudiciaire();
            $partnerData['fortuneEstimee'] = $partenaire->getFortuneEstimee();
        } elseif ($partenaire instanceof PartenaireMoral) {
            $partnerData['type'] = 'Organisation';
            $partnerData['raisonSociale'] = $partenaire->getRaisonSociale();
            $partnerData['formeJuridique'] = $partenaire->getFormeJuridique();
            $partnerData['siret'] = $partenaire->getSiret();
            $partnerData['secteurActivite'] = $partenaire->getSecteurActivite();
            $partnerData['dirigeantPrincipal'] = $partenaire->getDirigeantPrincipal();
            $partnerData['chiffreAffaires'] = $partenaire->getChiffreAffaires();
            $partnerData['nombreEmployes'] = $partenaire->getNombreEmployes();
            $partnerData['paysFiscal'] = $partenaire->getPaysFiscal();
            $partnerData['dateCreationEntreprise'] = $partenaire->getDateCreationEntreprise() ? $partenaire->getDateCreationEntreprise()->format('Y-m-d') : null;
            $partnerData['capitalSocial'] = $partenaire->getCapitalSocial();
        } else {
            $partnerData['type'] = 'Partenaire';
        }

        return new JsonResponse(['success' => true, 'partner' => $partnerData]);
    }

    #[Route('/{id}/update', name: 'app_partner_update', methods: ['POST'])]
    public function update(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        if (!$user || !$user instanceof User || !in_array('ROLE_ADMIN', $user->getRoles())) {
            return new JsonResponse(['success' => false, 'message' => 'Accès refusé']);
        }

        $partenaire = $em->getRepository(Partenaire::class)->find($id);
        
        if (!$partenaire) {
            return new JsonResponse(['success' => false, 'message' => 'Partenaire non trouvé']);
        }

        $data = json_decode($request->getContent(), true);
        
        try {
            // Validation des données requises
            if (empty($data['nom']) || empty($data['email'])) {
                return new JsonResponse(['success' => false, 'message' => 'Nom et email sont obligatoires']);
            }
            
            // Vérifier si l'email n'est pas déjà utilisé par un autre partenaire
            $existingPartner = $em->getRepository(Partenaire::class)->findOneBy(['email' => $data['email']]);
            if ($existingPartner && $existingPartner->getId() !== $partenaire->getId()) {
                return new JsonResponse(['success' => false, 'message' => 'Cet email est déjà utilisé par un autre partenaire']);
            }
            
            // Mise à jour des champs de base
            $partenaire->setNom($data['nom']);
            $partenaire->setEmail($data['email']);
            $partenaire->setTelephone($data['telephone'] ?? null);
            $partenaire->setNiveauRisque(PartenaireNiveauRisqueEnum::from($data['niveauRisque'] ?? 'modere'));
            $partenaire->setAdresse($data['adresse'] ?? null);
            $partenaire->setVille($data['ville'] ?? null);
            $partenaire->setCodePostal($data['codePostal'] ?? null);
            $partenaire->setPays($data['pays'] ?? null);
            $partenaire->setSiteWeb($data['siteWeb'] ?? null);
            $partenaire->setEstActif($data['estActif'] ?? false);
            $partenaire->setNotes($data['notes'] ?? null);
            $partenaire->setCommentairesInternes($data['commentairesInternes'] ?? null);
            
            // Dates
            if (!empty($data['dateCreation'])) {
                $partenaire->setDateCreation(new \DateTime($data['dateCreation']));
            } else {
                $partenaire->setDateCreation(null);
            }
            
            if (!empty($data['datePremiereCollaboration'])) {
                $partenaire->setDatePremiereCollaboration(new \DateTime($data['datePremiereCollaboration']));
            } else {
                $partenaire->setDatePremiereCollaboration(null);
            }
            
            // Nombre de délits impliqués
            $partenaire->setNombreDelitsImplique($data['nombreDelitsImplique'] ?? 0);
            
            // Mise à jour des champs spécifiques selon le type
            if ($partenaire instanceof PartenairePhysique) {
                // Validation des champs obligatoires pour les partenaires physiques
                if (empty($data['prenom']) || empty($data['nomFamille'])) {
                    return new JsonResponse(['success' => false, 'message' => 'Prénom et nom de famille sont obligatoires pour un partenaire individuel']);
                }
                
                $partenaire->setPrenom($data['prenom']);
                $partenaire->setNomFamille($data['nomFamille']);
                $partenaire->setLieuNaissance($data['lieuNaissance'] ?? null);
                $partenaire->setNationalite($data['nationalite'] ?? null);
                $partenaire->setProfession($data['profession'] ?? null);
                $partenaire->setNumeroSecu($data['numeroSecu'] ?? null);
                $partenaire->setNumeroCNI($data['numeroCNI'] ?? null);
                $partenaire->setSituationFamiliale($data['situationFamiliale'] ?? null);
                $partenaire->setCasierJudiciaire($data['casierJudiciaire'] ?? false);
                $partenaire->setFortuneEstimee(!empty($data['fortuneEstimee']) ? $data['fortuneEstimee'] : null);
                
                if (!empty($data['dateNaissance'])) {
                    $partenaire->setDateNaissance(new \DateTime($data['dateNaissance']));
                } else {
                    $partenaire->setDateNaissance(null);
                }
                
            } elseif ($partenaire instanceof PartenaireMoral) {
                // Validation des champs obligatoires pour les partenaires moraux
                if (empty($data['raisonSociale']) || empty($data['formeJuridique'])) {
                    return new JsonResponse(['success' => false, 'message' => 'Raison sociale et forme juridique sont obligatoires pour un partenaire organisation']);
                }
                
                $partenaire->setRaisonSociale($data['raisonSociale']);
                $partenaire->setFormeJuridique($data['formeJuridique']);
                $partenaire->setSiret($data['siret'] ?? null);
                $partenaire->setSecteurActivite($data['secteurActivite'] ?? null);
                $partenaire->setDirigeantPrincipal($data['dirigeantPrincipal'] ?? null);
                $partenaire->setChiffreAffaires(!empty($data['chiffreAffaires']) ? $data['chiffreAffaires'] : null);
                $partenaire->setNombreEmployes(!empty($data['nombreEmployes']) ? (int)$data['nombreEmployes'] : null);
                $partenaire->setPaysFiscal($data['paysFiscal'] ?? null);
                $partenaire->setCapitalSocial(!empty($data['capitalSocial']) ? $data['capitalSocial'] : null);
                
                if (!empty($data['dateCreationEntreprise'])) {
                    $partenaire->setDateCreationEntreprise(new \DateTime($data['dateCreationEntreprise']));
                } else {
                    $partenaire->setDateCreationEntreprise(null);
                }
            }
            
            $em->flush();
            
            return new JsonResponse(['success' => true, 'message' => 'Partenaire modifié avec succès']);
            
        } catch (\Exception $e) {
            error_log('Erreur modification partenaire: ' . $e->getMessage());
            return new JsonResponse(['success' => false, 'message' => 'Erreur lors de la modification du partenaire: ' . $e->getMessage()]);
        }
    }
} 
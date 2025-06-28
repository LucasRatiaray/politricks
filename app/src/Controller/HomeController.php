<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Partenaire;
use App\Entity\PartenairePhysique;
use App\Entity\PartenaireMoral;
use App\Enum\PartenaireNiveauRisqueEnum;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('index.html.twig');
    }

    #[Route('/offenses', name: 'app_offenses')]
    public function offenses(): Response
    {
        // Les données seront simulées pour l'instant
        $offenses = [
            [
                'type' => 'Bribery',
                'description' => 'Accepting illicit funds for favorable policy decisions',
                'date' => '2023-05-15',
                'status' => 'Open',
                'severity' => 'High',
                'politician' => 'Senator Thompson',
            ],
            [
                'type' => 'Embezzlement',
                'description' => 'Misappropriation of public funds for personal use',
                'date' => '2023-08-22',
                'status' => 'Closed',
                'severity' => 'High',
                'politician' => 'Representative Davis',
            ],
            [
                'type' => 'Fraud',
                'description' => 'Falsifying financial records to conceal illegal activities',
                'date' => '2023-11-10',
                'status' => 'Open',
                'severity' => 'Medium',
                'politician' => 'Governor Martinez',
            ],
            [
                'type' => 'Obstruction of Justice',
                'description' => 'Interfering with a criminal investigation',
                'date' => '2024-01-28',
                'status' => 'Closed',
                'severity' => 'High',
                'politician' => 'Attorney General Clark',
            ],
            [
                'type' => 'Perjury',
                'description' => 'Lying under oath in a court of law',
                'date' => '2024-03-05',
                'status' => 'Open',
                'severity' => 'Medium',
                'politician' => 'Councilman Harris',
            ],
            [
                'type' => 'Campaign Finance Violation',
                'description' => 'Accepting illegal contributions to a campaign',
                'date' => '2024-05-12',
                'status' => 'Closed',
                'severity' => 'Low',
                'politician' => 'Mayor Johnson',
            ],
            [
                'type' => 'Abuse of Power',
                'description' => 'Using official position for personal gain',
                'date' => '2024-07-18',
                'status' => 'Open',
                'severity' => 'High',
                'politician' => 'President Walker',
            ],
            [
                'type' => 'Conflict of Interest',
                'description' => 'Making decisions that benefit personal interests',
                'date' => '2024-09-25',
                'status' => 'Closed',
                'severity' => 'Medium',
                'politician' => 'Secretary Evans',
            ],
            [
                'type' => 'Influence Peddling',
                'description' => 'Using connections to gain undue advantages',
                'date' => '2024-11-02',
                'status' => 'Open',
                'severity' => 'Low',
                'politician' => 'Ambassador Lewis',
            ],
            [
                'type' => 'Tax Evasion',
                'description' => 'Illegally avoiding payment of taxes',
                'date' => '2025-01-09',
                'status' => 'Closed',
                'severity' => 'High',
                'politician' => 'Treasurer Scott',
            ],
        ];
        return $this->render('offenses/offenses.html.twig', [
            'offenses' => $offenses
        ]);
    }

    #[Route('/politics', name: 'app_politics')]
    public function politics(EntityManagerInterface $em): Response
    {
        // Récupérer tous les utilisateurs et filtrer côté PHP
        $allUsers = $em->getRepository(User::class)->findAll();
        
        $politicians = [];
        foreach ($allUsers as $user) {
            if (in_array('ROLE_POLITICIAN', $user->getRoles())) {
                $politicians[] = $user;
            }
        }

        $politiciansData = [];
        
        foreach ($politicians as $politician) {
            // Générer une image placeholder basée sur le nom
            $placeholderImage = $this->generatePlaceholderImage($politician->getFirstName(), $politician->getLastName());
            
            $politiciansData[] = [
                'id' => $politician->getId(),
                'name' => $politician->getFirstName() . ' ' . $politician->getLastName(),
                'firstName' => $politician->getFirstName(),
                'lastName' => $politician->getLastName(),
                'email' => $politician->getEmail(),
                'role' => $this->getPoliticianRole($politician),
                'offenses' => [], // À implémenter plus tard avec les vraies données
                'timeline' => [], // À implémenter plus tard
                'bio' => $this->generateBio($politician),
                'image' => $placeholderImage,
                'telephone' => $politician->getTelephone(),
                'nationalite' => $politician->getNationalite(),
                'profession' => $politician->getProfession(),
                'dateNaissance' => $politician->getDateNaissance(),
                'dateCreation' => $politician->getDateCreation(),
            ];
        }

        $selected = !empty($politiciansData) ? $politiciansData[0] : null;
        
        return $this->render('politics/politics.html.twig', [
            'politicians' => $politiciansData,
            'selected' => $selected,
        ]);
    }

    #[Route('/politics/{id}/partial', name: 'app_politics_partial')]
    public function politicsPartial(int $id, EntityManagerInterface $em): Response
    {
        // Récupérer le politicien spécifique
        $politician = $em->getRepository(User::class)->find($id);
        
        if (!$politician || !in_array('ROLE_POLITICIAN', $politician->getRoles())) {
            throw $this->createNotFoundException('Politicien non trouvé');
        }

        // Générer une image placeholder basée sur le nom
        $placeholderImage = $this->generatePlaceholderImage($politician->getFirstName(), $politician->getLastName());
        
        $politicianData = [
            'id' => $politician->getId(),
            'name' => $politician->getFirstName() . ' ' . $politician->getLastName(),
            'firstName' => $politician->getFirstName(),
            'lastName' => $politician->getLastName(),
            'email' => $politician->getEmail(),
            'role' => $this->getPoliticianRole($politician),
            'offenses' => [], // À implémenter plus tard avec les vraies données
            'timeline' => [], // À implémenter plus tard
            'bio' => $this->generateBio($politician),
            'image' => $placeholderImage,
            'telephone' => $politician->getTelephone(),
            'nationalite' => $politician->getNationalite(),
            'profession' => $politician->getProfession(),
            'dateNaissance' => $politician->getDateNaissance(),
            'dateCreation' => $politician->getDateCreation(),
        ];

        return $this->render('politics/components/politician_detail.html.twig', [
            'selected' => $politicianData,
        ]);
    }

    #[Route('/politics/add', name: 'app_politics_add', methods: ['POST'])]
    public function politicsAdd(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        // Validation des données requises
        if (empty($data['firstName']) || empty($data['lastName']) || empty($data['email']) || empty($data['password'])) {
            return new JsonResponse(['success' => false, 'message' => 'Tous les champs obligatoires doivent être remplis']);
        }
        
        // Vérifier si l'email existe déjà
        $existingUser = $em->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return new JsonResponse(['success' => false, 'message' => 'Un utilisateur avec cet email existe déjà']);
        }
        
        // Créer le nouvel utilisateur
        $user = new User();
        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);
        $user->setEmail($data['email']);
        $user->setPassword($passwordHasher->hashPassword($user, $data['password']));
        $user->setRoles(['ROLE_POLITICIAN']);
        
        // Champs optionnels
        if (!empty($data['telephone'])) {
            $user->setTelephone($data['telephone']);
        }
        if (!empty($data['nationalite'])) {
            $user->setNationalite($data['nationalite']);
        }
        if (!empty($data['profession'])) {
            $user->setProfession($data['profession']);
        }
        if (!empty($data['dateNaissance'])) {
            $user->setDateNaissance(new \DateTime($data['dateNaissance']));
        }
        
        try {
            $em->persist($user);
            $em->flush();
            
            return new JsonResponse(['success' => true, 'message' => 'Politicien ajouté avec succès']);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => 'Erreur lors de l\'ajout du politicien']);
        }
    }

    #[Route('/politics/{id}/delete', name: 'app_politics_delete', methods: ['DELETE'])]
    public function politicsDelete(int $id, EntityManagerInterface $em): JsonResponse
    {
        try {
            $politician = $em->getRepository(User::class)->find($id);
            
            if (!$politician) {
                return new JsonResponse(['success' => false, 'message' => 'Politicien non trouvé']);
            }
            
            if (!in_array('ROLE_POLITICIAN', $politician->getRoles())) {
                return new JsonResponse(['success' => false, 'message' => 'Cet utilisateur n\'est pas un politicien']);
            }
            
            // Vérifier s'il y a des relations qui empêchent la suppression
            // Pour l'instant, on va juste supprimer le rôle POLITICIAN au lieu de supprimer l'utilisateur
            $roles = $politician->getRoles();
            $roles = array_filter($roles, function($role) {
                return $role !== 'ROLE_POLITICIAN';
            });
            
            if (empty($roles)) {
                $roles = ['ROLE_USER']; // Garder au moins ROLE_USER
            }
            
            $politician->setRoles($roles);
            $em->flush();
            
            return new JsonResponse(['success' => true, 'message' => 'Politicien supprimé avec succès']);
            
        } catch (\Exception $e) {
            // Log l'erreur pour le debugging
            error_log('Erreur suppression politicien: ' . $e->getMessage());
            return new JsonResponse(['success' => false, 'message' => 'Erreur lors de la suppression du politicien: ' . $e->getMessage()]);
        }
    }

    #[Route('/politics/{id}/edit', name: 'app_politics_edit', methods: ['GET'])]
    public function politicsEdit(int $id, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        if (!$user || !$user instanceof User || !in_array('ROLE_ADMIN', $user->getRoles())) {
            return new JsonResponse(['success' => false, 'message' => 'Accès refusé']);
        }

        $politician = $em->getRepository(User::class)->find($id);
        
        if (!$politician) {
            return new JsonResponse(['success' => false, 'message' => 'Politicien non trouvé']);
        }
        
        if (!in_array('ROLE_POLITICIAN', $politician->getRoles())) {
            return new JsonResponse(['success' => false, 'message' => 'Cet utilisateur n\'est pas un politicien']);
        }
        
        $politicianData = [
            'id' => $politician->getId(),
            'firstName' => $politician->getFirstName(),
            'lastName' => $politician->getLastName(),
            'email' => $politician->getEmail(),
            'telephone' => $politician->getTelephone(),
            'nationalite' => $politician->getNationalite(),
            'profession' => $politician->getProfession(),
            'dateNaissance' => $politician->getDateNaissance() ? $politician->getDateNaissance()->format('Y-m-d') : null,
        ];

        return new JsonResponse(['success' => true, 'politician' => $politicianData]);
    }

    #[Route('/politics/{id}/update', name: 'app_politics_update', methods: ['POST'])]
    public function politicsUpdate(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        if (!$user || !$user instanceof User || !in_array('ROLE_ADMIN', $user->getRoles())) {
            return new JsonResponse(['success' => false, 'message' => 'Accès refusé']);
        }

        $politician = $em->getRepository(User::class)->find($id);
        
        if (!$politician) {
            return new JsonResponse(['success' => false, 'message' => 'Politicien non trouvé']);
        }
        
        if (!in_array('ROLE_POLITICIAN', $politician->getRoles())) {
            return new JsonResponse(['success' => false, 'message' => 'Cet utilisateur n\'est pas un politicien']);
        }

        $data = json_decode($request->getContent(), true);
        
        try {
            // Validation des données requises
            if (empty($data['firstName']) || empty($data['lastName']) || empty($data['email'])) {
                return new JsonResponse(['success' => false, 'message' => 'Prénom, nom et email sont obligatoires']);
            }
            
            // Vérifier si l'email n'est pas déjà utilisé par un autre utilisateur
            $existingUser = $em->getRepository(User::class)->findOneBy(['email' => $data['email']]);
            if ($existingUser && $existingUser->getId() !== $politician->getId()) {
                return new JsonResponse(['success' => false, 'message' => 'Cet email est déjà utilisé par un autre utilisateur']);
            }
            
            // Mise à jour des champs
            $politician->setFirstName($data['firstName']);
            $politician->setLastName($data['lastName']);
            $politician->setEmail($data['email']);
            
            // Champs optionnels
            $politician->setTelephone($data['telephone'] ?? null);
            $politician->setNationalite($data['nationalite'] ?? null);
            $politician->setProfession($data['profession'] ?? null);
            
            if (!empty($data['dateNaissance'])) {
                $politician->setDateNaissance(new \DateTime($data['dateNaissance']));
            } else {
                $politician->setDateNaissance(null);
            }
            
            $em->flush();
            
            return new JsonResponse(['success' => true, 'message' => 'Politicien modifié avec succès']);
            
        } catch (\Exception $e) {
            error_log('Erreur modification politicien: ' . $e->getMessage());
            return new JsonResponse(['success' => false, 'message' => 'Erreur lors de la modification du politicien']);
        }
    }

    #[Route('/partners', name: 'app_partner')]
    public function partner(EntityManagerInterface $em): Response
    {
        // Récupérer tous les partenaires de la base de données
        $partenaires = $em->getRepository(Partenaire::class)->findAll();
        
        // Fonction helper pour formater les dates de manière sécurisée
        $formatDate = function($date) {
            if ($date instanceof \DateTime) {
                return $date->format('d/m/Y');
            }
            return null;
        };
        
        $partnersData = [];
        foreach ($partenaires as $partenaire) {
            // Déterminer le type de partenaire
            $type = 'Partenaire';
            if ($partenaire instanceof PartenairePhysique) {
                $type = 'Individuel';
                $name = $partenaire->getPrenom() . ' ' . $partenaire->getNomFamille();
            } elseif ($partenaire instanceof PartenaireMoral) {
                $type = 'Organisation';
                $name = $partenaire->getRaisonSociale();
            } else {
                $name = $partenaire->getNom();
            }
            
            // Récupérer les délits associés
            $delits = [];
            foreach ($partenaire->getDelits() as $delit) {
                $delits[] = [
                    'date' => $delit->getDateDelit() ? $delit->getDateDelit()->format('d/m/Y') : 'Date inconnue',
                    'type' => $delit->getType()->value ?? 'Type inconnu',
                    'description' => $delit->getDescription() ?? 'Aucune description',
                    'status' => $delit->getStatut()->value ?? 'Statut inconnu',
                ];
            }
            
            $partnersData[] = [
                'id' => $partenaire->getId(),
                'name' => $name,
                'risk' => $partenaire->getNiveauRisque() ? $partenaire->getNiveauRisque()->value : 'Inconnu',
                'type' => $type,
                'email' => $partenaire->getEmail(),
                'phone' => $partenaire->getTelephone(),
                'offenses' => $delits,
                'notes' => $partenaire->getNotes(),
                'adresse' => $partenaire->getAdresse(),
                'ville' => $partenaire->getVille(),
                'codePostal' => $partenaire->getCodePostal(),
                'pays' => $partenaire->getPays(),
                'siteWeb' => $partenaire->getSiteWeb(),
                'dateCreation' => $formatDate($partenaire->getDateCreation()),
                'datePremiereCollaboration' => $formatDate($partenaire->getDatePremiereCollaboration()),
                'nombreDelitsImplique' => $partenaire->getNombreDelitsImplique(),
                'estActif' => $partenaire->isEstActif(),
                'commentairesInternes' => $partenaire->getCommentairesInternes(),
                // Champs spécifiques aux partenaires physiques
                'prenom' => $partenaire instanceof PartenairePhysique ? $partenaire->getPrenom() : null,
                'nomFamille' => $partenaire instanceof PartenairePhysique ? $partenaire->getNomFamille() : null,
                'dateNaissance' => $partenaire instanceof PartenairePhysique ? $formatDate($partenaire->getDateNaissance()) : null,
                'lieuNaissance' => $partenaire instanceof PartenairePhysique ? $partenaire->getLieuNaissance() : null,
                'nationalite' => $partenaire instanceof PartenairePhysique ? $partenaire->getNationalite() : null,
                'profession' => $partenaire instanceof PartenairePhysique ? $partenaire->getProfession() : null,
                'numeroSecu' => $partenaire instanceof PartenairePhysique ? $partenaire->getNumeroSecu() : null,
                'numeroCNI' => $partenaire instanceof PartenairePhysique ? $partenaire->getNumeroCNI() : null,
                'situationFamiliale' => $partenaire instanceof PartenairePhysique ? $partenaire->getSituationFamiliale() : null,
                'casierJudiciaire' => $partenaire instanceof PartenairePhysique ? $partenaire->isCasierJudiciaire() : null,
                'fortuneEstimee' => $partenaire instanceof PartenairePhysique ? $partenaire->getFortuneEstimee() : null,
                // Champs spécifiques aux partenaires moraux
                'raisonSociale' => $partenaire instanceof PartenaireMoral ? $partenaire->getRaisonSociale() : null,
                'formeJuridique' => $partenaire instanceof PartenaireMoral ? $partenaire->getFormeJuridique() : null,
                'siret' => $partenaire instanceof PartenaireMoral ? $partenaire->getSiret() : null,
                'secteurActivite' => $partenaire instanceof PartenaireMoral ? $partenaire->getSecteurActivite() : null,
                'dirigeantPrincipal' => $partenaire instanceof PartenaireMoral ? $partenaire->getDirigeantPrincipal() : null,
                'chiffreAffaires' => $partenaire instanceof PartenaireMoral ? $partenaire->getChiffreAffaires() : null,
                'nombreEmployes' => $partenaire instanceof PartenaireMoral ? $partenaire->getNombreEmployes() : null,
                'paysFiscal' => $partenaire instanceof PartenaireMoral ? $partenaire->getPaysFiscal() : null,
                'dateCreationEntreprise' => $partenaire instanceof PartenaireMoral ? $formatDate($partenaire->getDateCreationEntreprise()) : null,
                'capitalSocial' => $partenaire instanceof PartenaireMoral ? $partenaire->getCapitalSocial() : null,
            ];
        }
        
        $selected = !empty($partnersData) ? $partnersData[0] : null;
        
        return $this->render('partner/partner.html.twig', [
            'partners' => $partnersData,
            'selected' => $selected,
        ]);
    }

    #[Route('/partners/{id}/partial', name: 'app_partner_partial')]
    public function partnerPartial(int $id, EntityManagerInterface $em): Response
    {
        $partenaire = $em->getRepository(Partenaire::class)->find($id);
        
        if (!$partenaire) {
            throw $this->createNotFoundException('Partenaire non trouvé');
        }
        
        // Fonction helper pour formater les dates de manière sécurisée
        $formatDate = function($date) {
            if ($date instanceof \DateTime) {
                return $date->format('d/m/Y');
            }
            return null;
        };
        
        // Déterminer le type de partenaire
        $type = 'Partenaire';
        if ($partenaire instanceof PartenairePhysique) {
            $type = 'Individuel';
            $name = $partenaire->getPrenom() . ' ' . $partenaire->getNomFamille();
        } elseif ($partenaire instanceof PartenaireMoral) {
            $type = 'Organisation';
            $name = $partenaire->getRaisonSociale();
        } else {
            $name = $partenaire->getNom();
        }
        
        // Récupérer les délits associés
        $delits = [];
        foreach ($partenaire->getDelits() as $delit) {
            $delits[] = [
                'date' => $delit->getDateDelit() ? $delit->getDateDelit()->format('d/m/Y') : 'Date inconnue',
                'type' => $delit->getType()->value ?? 'Type inconnu',
                'description' => $delit->getDescription() ?? 'Aucune description',
                'status' => $delit->getStatut()->value ?? 'Statut inconnu',
            ];
        }
        
        $selected = [
            'id' => $partenaire->getId(),
            'name' => $name,
            'risk' => $partenaire->getNiveauRisque() ? $partenaire->getNiveauRisque()->value : 'Inconnu',
            'type' => $type,
            'email' => $partenaire->getEmail(),
            'phone' => $partenaire->getTelephone(),
            'offenses' => $delits,
            'notes' => $partenaire->getNotes(),
            'adresse' => $partenaire->getAdresse(),
            'ville' => $partenaire->getVille(),
            'codePostal' => $partenaire->getCodePostal(),
            'pays' => $partenaire->getPays(),
            'siteWeb' => $partenaire->getSiteWeb(),
            'dateCreation' => $formatDate($partenaire->getDateCreation()),
            'datePremiereCollaboration' => $formatDate($partenaire->getDatePremiereCollaboration()),
            'nombreDelitsImplique' => $partenaire->getNombreDelitsImplique(),
            'estActif' => $partenaire->isEstActif(),
            'commentairesInternes' => $partenaire->getCommentairesInternes(),
            // Champs spécifiques aux partenaires physiques
            'prenom' => $partenaire instanceof PartenairePhysique ? $partenaire->getPrenom() : null,
            'nomFamille' => $partenaire instanceof PartenairePhysique ? $partenaire->getNomFamille() : null,
            'dateNaissance' => $partenaire instanceof PartenairePhysique ? $formatDate($partenaire->getDateNaissance()) : null,
            'lieuNaissance' => $partenaire instanceof PartenairePhysique ? $partenaire->getLieuNaissance() : null,
            'nationalite' => $partenaire instanceof PartenairePhysique ? $partenaire->getNationalite() : null,
            'profession' => $partenaire instanceof PartenairePhysique ? $partenaire->getProfession() : null,
            'numeroSecu' => $partenaire instanceof PartenairePhysique ? $partenaire->getNumeroSecu() : null,
            'numeroCNI' => $partenaire instanceof PartenairePhysique ? $partenaire->getNumeroCNI() : null,
            'situationFamiliale' => $partenaire instanceof PartenairePhysique ? $partenaire->getSituationFamiliale() : null,
            'casierJudiciaire' => $partenaire instanceof PartenairePhysique ? $partenaire->isCasierJudiciaire() : null,
            'fortuneEstimee' => $partenaire instanceof PartenairePhysique ? $partenaire->getFortuneEstimee() : null,
            // Champs spécifiques aux partenaires moraux
            'raisonSociale' => $partenaire instanceof PartenaireMoral ? $partenaire->getRaisonSociale() : null,
            'formeJuridique' => $partenaire instanceof PartenaireMoral ? $partenaire->getFormeJuridique() : null,
            'siret' => $partenaire instanceof PartenaireMoral ? $partenaire->getSiret() : null,
            'secteurActivite' => $partenaire instanceof PartenaireMoral ? $partenaire->getSecteurActivite() : null,
            'dirigeantPrincipal' => $partenaire instanceof PartenaireMoral ? $partenaire->getDirigeantPrincipal() : null,
            'chiffreAffaires' => $partenaire instanceof PartenaireMoral ? $partenaire->getChiffreAffaires() : null,
            'nombreEmployes' => $partenaire instanceof PartenaireMoral ? $partenaire->getNombreEmployes() : null,
            'paysFiscal' => $partenaire instanceof PartenaireMoral ? $partenaire->getPaysFiscal() : null,
            'dateCreationEntreprise' => $partenaire instanceof PartenaireMoral ? $formatDate($partenaire->getDateCreationEntreprise()) : null,
            'capitalSocial' => $partenaire instanceof PartenaireMoral ? $partenaire->getCapitalSocial() : null,
        ];
        
        return $this->render('partner/components/partner_detail.html.twig', [
            'selected' => $selected,
        ]);
    }

    #[Route('/partners/{id}/edit', name: 'app_partner_edit', methods: ['GET'])]
    public function partnerEdit(int $id, EntityManagerInterface $em): JsonResponse
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

    #[Route('/partners/{id}/update', name: 'app_partner_update', methods: ['POST'])]
    public function partnerUpdate(int $id, Request $request, EntityManagerInterface $em): JsonResponse
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
                $partenaire->setFortuneEstimee($data['fortuneEstimee'] ?? null);
                
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
                $partenaire->setChiffreAffaires($data['chiffreAffaires'] ?? null);
                $partenaire->setNombreEmployes($data['nombreEmployes'] ?? null);
                $partenaire->setPaysFiscal($data['paysFiscal'] ?? null);
                $partenaire->setCapitalSocial($data['capitalSocial'] ?? null);
                
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

    #[Route('/medias', name: 'app_media')]
    public function media(): Response
    {
        $media = [
            [
                'title' => 'Image of a protest',
                'type' => 'Image',
                'date' => '2023-08-15',
                'offense' => 'Public Disorder',
                'confidentiality' => 'Public',
            ],
            [
                'title' => 'Video of a speech',
                'type' => 'Video',
                'date' => '2023-07-22',
                'offense' => 'Incitement',
                'confidentiality' => 'Confidential',
            ],
            [
                'title' => 'Audio recording of a meeting',
                'type' => 'Audio',
                'date' => '2023-06-10',
                'offense' => 'Conspiracy',
                'confidentiality' => 'Secret',
            ],
            [
                'title' => 'PDF document of financial records',
                'type' => 'PDF',
                'date' => '2023-05-01',
                'offense' => 'Corruption',
                'confidentiality' => 'Top Secret',
            ],
            [
                'title' => 'Image of a rally',
                'type' => 'Image',
                'date' => '2023-04-18',
                'offense' => 'Public Disorder',
                'confidentiality' => 'Public',
            ],
            [
                'title' => 'Video of a debate',
                'type' => 'Video',
                'date' => '2023-05-25',
                'offense' => 'Incitement',
                'confidentiality' => 'Confidential',
            ],
            [
                'title' => 'Audio recording of a phone call',
                'type' => 'Audio',
                'date' => '2023-02-12',
                'offense' => 'Conspiracy',
                'confidentiality' => 'Secret',
            ],
            [
                'title' => 'PDF document of legal filings',
                'type' => 'PDF',
                'date' => '2023-01-05',
                'offense' => 'Obstruction of Justice',
                'confidentiality' => 'Top Secret',
            ],
            [
                'title' => 'Image of a press conference',
                'type' => 'Image',
                'date' => '2022-12-20',
                'offense' => 'Public Disorder',
                'confidentiality' => 'Public',
            ],
            [
                'title' => 'Video of a town hall',
                'type' => 'Video',
                'date' => '2022-11-10',
                'offense' => 'Incitement',
                'confidentiality' => 'Confidential',
            ],
        ];
        return $this->render('media/media.html.twig', [
            'media' => $media,
        ]);
    }

    #[Route('/profile', name: 'app_profile')]
    public function profile(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('profile/profile.html.twig', [
            'user' => $user
        ]);
    }

    #[Route('/profile/update', name: 'app_profile_update', methods: ['POST'])]
    public function profileUpdate(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        if (!$user || !$user instanceof User) {
            return new JsonResponse(['success' => false, 'message' => 'Utilisateur non connecté']);
        }

        $data = json_decode($request->getContent(), true);
        
        try {
            // Mise à jour des champs de base
            if (!empty($data['firstName'])) {
                $user->setFirstName($data['firstName']);
            }
            if (!empty($data['lastName'])) {
                $user->setLastName($data['lastName']);
            }
            if (!empty($data['email'])) {
                // Vérifier si l'email n'est pas déjà utilisé par un autre utilisateur
                $existingUser = $em->getRepository(User::class)->findOneBy(['email' => $data['email']]);
                if ($existingUser && $existingUser->getId() !== $user->getId()) {
                    return new JsonResponse(['success' => false, 'message' => 'Cet email est déjà utilisé par un autre utilisateur']);
                }
                $user->setEmail($data['email']);
            }
            
            // Mise à jour des champs optionnels
            if (isset($data['telephone'])) {
                $user->setTelephone($data['telephone'] ?: null);
            }
            if (isset($data['nationalite'])) {
                $user->setNationalite($data['nationalite'] ?: null);
            }
            if (isset($data['profession'])) {
                $user->setProfession($data['profession'] ?: null);
            }
            if (!empty($data['dateNaissance'])) {
                $user->setDateNaissance(new \DateTime($data['dateNaissance']));
            } elseif (isset($data['dateNaissance']) && empty($data['dateNaissance'])) {
                $user->setDateNaissance(null);
            }
            
            $em->flush();
            
            return new JsonResponse(['success' => true, 'message' => 'Profil mis à jour avec succès']);
            
        } catch (\Exception $e) {
            error_log('Erreur mise à jour profil: ' . $e->getMessage());
            return new JsonResponse(['success' => false, 'message' => 'Erreur lors de la mise à jour du profil']);
        }
    }

    /**
     * Génère une image placeholder basée sur les initiales du politicien
     */
    private function generatePlaceholderImage(string $firstName, string $lastName): string
    {
        $initials = strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
        $colors = ['#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7', '#DDA0DD', '#98D8C8', '#F7DC6F'];
        $color = $colors[array_rand($colors)];
        
        return "https://ui-avatars.com/api/?name=" . urlencode($initials) . "&background=" . substr($color, 1) . "&color=fff&size=200&bold=true";
    }

    /**
     * Détermine le rôle du politicien basé sur son email ou autres critères
     */
    private function getPoliticianRole(User $politician): string
    {
        $email = $politician->getEmail();
        
        if (str_contains($email, 'elysee')) {
            return 'Président de la République';
        } elseif (str_contains($email, 'rn.fr')) {
            return 'Chef de parti politique';
        } elseif (str_contains($email, 'politicien')) {
            return 'Politicien';
        } else {
            return 'Représentant politique';
        }
    }

    /**
     * Génère une bio basée sur les informations du politicien
     */
    private function generateBio(User $politician): string
    {
        $firstName = $politician->getFirstName();
        $lastName = $politician->getLastName();
        $profession = $politician->getProfession();
        $nationalite = $politician->getNationalite();
        
        $bio = "{$firstName} {$lastName} est un politicien";
        
        if ($nationalite) {
            $bio .= " {$nationalite}";
        }
        
        if ($profession) {
            $bio .= " spécialisé dans {$profession}";
        }
        
        $bio .= ". Il/elle s'engage activement dans la vie politique et représente les intérêts de ses concitoyens.";
        
        return $bio;
    }
} 
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

    #[Route('/partners', name: 'app_partner')]
    public function partner(): Response
    {
        $partners = [
            [
                'id' => 1,
                'name' => 'Ethan Carter',
                'risk' => 'High',
                'type' => 'Individual',
                'email' => 'ethan.carter@email.com',
                'phone' => '+1-555-123-4567',
                'offenses' => [
                    [
                        'date' => '2023-05-15',
                        'type' => 'Campaign Finance Violation',
                        'description' => 'Exceeded contribution limits',
                        'status' => 'Pending',
                    ],
                    [
                        'date' => '2023-08-22',
                        'type' => 'Misleading Advertising',
                        'description' => 'False claims in campaign ads',
                        'status' => 'Resolved',
                    ],
                    [
                        'date' => '2024-01-10',
                        'type' => 'Ethical Misconduct',
                        'description' => 'Inappropriate use of public funds',
                        'status' => 'Under Review',
                    ],
                ],
                'notes' => 'Ethan Carter has a history of high-risk behavior, including multiple offenses related to campaign finance and ethical conduct. Close monitoring is recommended. Further investigation into recent activities is warranted.',
            ],
            [
                'id' => 2,
                'name' => 'Civic Action Group',
                'risk' => 'Medium',
                'type' => 'Organization',
                'email' => 'contact@civicgroup.org',
                'phone' => '+1-555-222-3333',
                'offenses' => [],
                'notes' => 'Civic Action Group is a medium-risk organization focused on community engagement.',
            ],
            [
                'id' => 3,
                'name' => 'Olivia Bennett',
                'risk' => 'Low',
                'type' => 'Individual',
                'email' => 'olivia.bennett@email.com',
                'phone' => '+1-555-987-6543',
                'offenses' => [],
                'notes' => 'Olivia Bennett is a low-risk individual with no known offenses.',
            ],
        ];
        $selected = $partners[0];
        return $this->render('partner/partner.html.twig', [
            'partners' => $partners,
            'selected' => $selected,
        ]);
    }

    #[Route('/partners/{id}/partial', name: 'app_partner_partial')]
    public function partnerPartial(int $id): Response
    {
        $partners = [
            [
                'id' => 1,
                'name' => 'Ethan Carter',
                'risk' => 'High',
                'type' => 'Individual',
                'email' => 'ethan.carter@email.com',
                'phone' => '+1-555-123-4567',
                'offenses' => [
                    [
                        'date' => '2023-05-15',
                        'type' => 'Campaign Finance Violation',
                        'description' => 'Exceeded contribution limits',
                        'status' => 'Pending',
                    ],
                    [
                        'date' => '2023-08-22',
                        'type' => 'Misleading Advertising',
                        'description' => 'False claims in campaign ads',
                        'status' => 'Resolved',
                    ],
                    [
                        'date' => '2024-01-10',
                        'type' => 'Ethical Misconduct',
                        'description' => 'Inappropriate use of public funds',
                        'status' => 'Under Review',
                    ],
                ],
                'notes' => 'Ethan Carter has a history of high-risk behavior, including multiple offenses related to campaign finance and ethical conduct. Close monitoring is recommended. Further investigation into recent activities is warranted.',
            ],
            [
                'id' => 2,
                'name' => 'Civic Action Group',
                'risk' => 'Medium',
                'type' => 'Organization',
                'email' => 'contact@civicgroup.org',
                'phone' => '+1-555-222-3333',
                'offenses' => [],
                'notes' => 'Civic Action Group is a medium-risk organization focused on community engagement.',
            ],
            [
                'id' => 3,
                'name' => 'Olivia Bennett',
                'risk' => 'Low',
                'type' => 'Individual',
                'email' => 'olivia.bennett@email.com',
                'phone' => '+1-555-987-6543',
                'offenses' => [],
                'notes' => 'Olivia Bennett is a low-risk individual with no known offenses.',
            ],
        ];
        $selected = array_filter($partners, fn($p) => $p['id'] == $id);
        $selected = $selected ? array_values($selected)[0] : $partners[0];
        return $this->render('partner/components/partner_detail.html.twig', [
            'selected' => $selected,
        ]);
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
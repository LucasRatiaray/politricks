<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
    public function politics(): Response
    {
        $politicians = [
            [
                'id' => 1,
                'name' => 'Liam Harrison',
                'role' => 'Representative',
                'offenses' => [
                    'Campaign Finance Violation',
                    'Conflict of Interest',
                    'Misuse of Public Funds',
                ],
                'timeline' => [
                    ['year' => 2017, 'event' => 'Elected to City Council', 'icon' => '🏛️'],
                    ['year' => 2020, 'event' => 'Announces Candidacy for Mayor', 'icon' => '📢'],
                    ['year' => 2023, 'event' => 'Wins Mayoral Election', 'icon' => '🏆'],
                ],
                'bio' => 'Liam has served as a respected politician known for his strong stance on fiscal responsibility and environmental protection. He has received numerous accolades, including for policies that promoted transparent governance and economic growth. Harrison is recognized for his ability to bring together community stakeholders, maintain consensus, and implement lasting change.',
                'image' => 'https://randomuser.me/api/portraits/men/32.jpg',
            ],
            [
                'id' => 2,
                'name' => 'Jessica Ross',
                'role' => 'Senator',
                'offenses' => [],
                'timeline' => [],
                'bio' => '',
                'image' => 'https://randomuser.me/api/portraits/women/44.jpg',
            ],
            [
                'id' => 3,
                'name' => 'Michael Brown',
                'role' => 'Councilman',
                'offenses' => [],
                'timeline' => [],
                'bio' => '',
                'image' => 'https://randomuser.me/api/portraits/men/45.jpg',
            ],
        ];
        $selected = $politicians[0];
        return $this->render('politics/politics.html.twig', [
            'politicians' => $politicians,
            'selected' => $selected,
        ]);
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
        // TODO: remplacer par l'utilisateur connecté
        $user = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'john.doe@example.com',
            'telephone' => '0601020304',
            'dateNaissance' => '1990-01-01',
            'nationalite' => 'Française',
            'profession' => 'Développeur',
        ];
        return $this->render('profile/profile.html.twig', [
            'user' => $user
        ]);
    }
} 
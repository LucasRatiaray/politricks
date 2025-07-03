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
use App\Entity\Delit;
use App\Entity\DelitFinancier;
use App\Entity\DelitFraude;
use App\Entity\DelitVol;
use App\Entity\Lieu;
use App\Entity\Commentaire;
use App\Entity\Document;
use App\Entity\DocumentImage;
use App\Entity\DocumentVideo;
use App\Entity\DocumentAudio;
use App\Entity\DocumentFichier;
use App\Enum\PartenaireNiveauRisqueEnum;
use App\Enum\DelitStatutEnum;
use App\Enum\DelitGraviteEnum;
use App\Entity\Politicien;
use App\Enum\DelitTypeEnum;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $em): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();
        
        // Statistiques globales
        $totalDelits = $em->getRepository(Delit::class)->count([]);
        
        // Compter les politiciens (utilisateurs avec ROLE_POLITICIAN) - utiliser SQL natif pour PostgreSQL
        $connection = $em->getConnection();
        $sql = "SELECT COUNT(*) FROM users WHERE roles::text LIKE '%ROLE_POLITICIAN%'";
        $totalPoliticiens = $connection->executeQuery($sql)->fetchOne();
        
        $totalPartenaires = $em->getRepository(Partenaire::class)->count([]);
        
        // Calculer les variations (comparaison avec le mois précédent)
        $lastMonth = new \DateTime('-1 month');
        
        // Délits du mois dernier
        $qb = $em->createQueryBuilder();
        $qb->select('COUNT(d.id)')
           ->from(Delit::class, 'd')
           ->where('d.date >= :lastMonth')
           ->setParameter('lastMonth', $lastMonth);
        $delitsLastMonth = $qb->getQuery()->getSingleScalarResult();
        
        // Politiciens créés le mois dernier
        $sql = "SELECT COUNT(*) FROM users WHERE roles::text LIKE '%ROLE_POLITICIAN%' AND date_creation >= :lastMonth";
        $politiciensLastMonth = $connection->executeQuery($sql, ['lastMonth' => $lastMonth->format('Y-m-d')])->fetchOne();
        
        // Partenaires créés le mois dernier
        $qb = $em->createQueryBuilder();
        $qb->select('COUNT(p.id)')
           ->from(Partenaire::class, 'p')
           ->where('p.dateCreation >= :lastMonth')
           ->setParameter('lastMonth', $lastMonth);
        $partenairesLastMonth = $qb->getQuery()->getSingleScalarResult();
        
        $delitsVariation = $delitsLastMonth > 0 ? round((($totalDelits - $delitsLastMonth) / $delitsLastMonth) * 100) : 0;
        // Cards %
        $now = new \DateTime();
        $thirtyDaysAgo = (clone $now)->modify('-30 days');
        $sqlTotalPolitician = "SELECT COUNT(*) FROM users WHERE roles::text LIKE '%ROLE_POLITICIAN%'";
        $totalPoliticiens = $connection->executeQuery($sqlTotalPolitician)->fetchOne();
        // Nombre total de partenaires
        $totalPartners = $em->getRepository(Partenaire::class)->count([]);
        // Partenaires créés dans les 30 derniers jours
        $qb = $em->createQueryBuilder();
        $qb->select('COUNT(p.id)')
           ->from(Partenaire::class, 'p')
           ->where('p.dateCreation >= :since')
           ->setParameter('since', $thirtyDaysAgo);
        $recentPartners = $qb->getQuery()->getSingleScalarResult();
        // 30 derniers jours politiciens
        $sqlRecentPolitic = "SELECT COUNT(*) FROM users WHERE roles::text LIKE '%ROLE_POLITICIAN%' AND date_creation >= :since";
        $recentPoliticiens = $connection->executeQuery($sqlRecentPolitic, ['since' => $thirtyDaysAgo->format('Y-m-d H:i:s')])->fetchOne();

        if ($recentPoliticiens > 0 && $totalPoliticiens > 0) {
            $politiciensVariation = round(($recentPoliticiens / $totalPoliticiens) * 100);
            $politiciensVariationDisplay = ($politiciensVariation > 0 ? '+' : '') . $politiciensVariation . '%';
        } else {
            $politiciensVariationDisplay = '';
        }

        if ($recentPartners > 0 && $totalPartners > 0) {
            $partnersVariation = round(($recentPartners / $totalPartners) * 100);
            $partnersVariationDisplay = ($partnersVariation > 0 ? '+' : '') . $partnersVariation . '%';
        } else {
            $partnersVariationDisplay = '';
        }

        $variationColor = '#22c55e';
        $partenairesVariation = $partenairesLastMonth > 0 ? round((($totalPartenaires - $partenairesLastMonth) / $partenairesLastMonth) * 100) : 0;

        // Activités récentes de l'utilisateur connecté (7 derniers jours)
        $recentActivities = [];
        $sevenDaysAgo = new \DateTime('-7 days');
        
        if ($user) {
            // Commentaires créés par l'utilisateur
            $qb = $em->createQueryBuilder();
            $qb->select('c')
                ->from(Commentaire::class, 'c')
                ->where('c.dateCreation >= :sevenDaysAgo')
                ->andWhere('c.auteur = :user')
                ->orderBy('c.dateCreation', 'DESC')
                ->setParameter('sevenDaysAgo', $sevenDaysAgo)
                ->setParameter('user', $user);
            
            $recentCommentaires = $qb->getQuery()->getResult();
            
            foreach ($recentCommentaires as $commentaire) {
                $delitId = $commentaire->getDelit() ? $commentaire->getDelit()->getId() : 'N/A';
                $recentActivities[] = [
                    'type' => 'commentaire',
                    'text' => 'Commentaire ajouté sur le délit #' . $delitId,
                    'date' => $this->getTimeAgo($commentaire->getDateCreation()),
                    'icon' => '💬'
                ];
            }
            
            // Documents uploadés par l'utilisateur
            $qb = $em->createQueryBuilder();
            $qb->select('d')
                ->from(Document::class, 'd')
                ->where('d.dateCreation >= :sevenDaysAgo')
                ->andWhere('d.auteur = :user')
                ->orderBy('d.dateCreation', 'DESC')
                ->setParameter('sevenDaysAgo', $sevenDaysAgo)
                ->setParameter('user', $user);
            
            $recentDocuments = $qb->getQuery()->getResult();
            
            foreach ($recentDocuments as $document) {
                $recentActivities[] = [
                    'type' => 'document',
                    'text' => 'Document uploadé : ' . $document->getNom(),
                    'date' => $this->getTimeAgo($document->getDateCreation()),
                    'icon' => '📄'
                ];
            }

            // Trier les activités par date (les plus récentes en premier)
            usort($recentActivities, function($a, $b) {
                // Les dates sont déjà formatées en "il y a X temps", donc on utilise l'ordre des requêtes
                // qui sont déjà triées par date de création décroissante
                return 0;
            });
            
            $recentActivities = array_slice($recentActivities, 0, 10);
        }
        
        return $this->render('index.html.twig', [
            'totalDelits' => $totalDelits,
            'totalPoliticiens' => $totalPoliticiens,
            'totalPartenaires' => $totalPartenaires,
            'delitsVariation' => $delitsVariation,
            'politiciensVariation' => $politiciensVariationDisplay,
            'partenairesVariation' => $partnersVariationDisplay,
            'recentActivities' => $recentActivities,
        ]);
    }

    #[Route('/offenses', name: 'app_offenses')]
    public function offenses(EntityManagerInterface $em): Response
    {
        // Récupérer tous les délits de la base de données
        $delits = $em->getRepository(Delit::class)->findAll();
        
        // Fonction helper pour formater les dates de manière sécurisée
        $formatDate = function($date) {
            if ($date instanceof \DateTime) {
                return $date->format('d/m/Y');
            }
            return null;
        };
        
        $offensesData = [];
        foreach ($delits as $delit) {
            // Utiliser le vrai type de délit depuis l'enum
            $type = $delit->getType() ? $delit->getType()->value : 'delit';
            
            // Récupérer les politiciens impliqués
            $politiciens = [];
            foreach ($delit->getPoliticiens() as $politicien) {
                $politiciens[] = [
                    'id' => $politicien->getId(),
                    'name' => $politicien->getFirstName() . ' ' . $politicien->getLastName(),
                    'email' => $politicien->getEmail(),
                ];
            }
            
            // Récupérer les partenaires impliqués
            $partenaires = [];
            foreach ($delit->getPartenaires() as $partenaire) {
                if ($partenaire instanceof PartenairePhysique) {
                    $partenaires[] = [
                        'id' => $partenaire->getId(),
                        'name' => $partenaire->getPrenom() . ' ' . $partenaire->getNomFamille(),
                        'type' => 'Individuel',
                    ];
                } elseif ($partenaire instanceof PartenaireMoral) {
                    $partenaires[] = [
                        'id' => $partenaire->getId(),
                        'name' => $partenaire->getRaisonSociale(),
                        'type' => 'Organisation',
                    ];
                } else {
                    $partenaires[] = [
                        'id' => $partenaire->getId(),
                        'name' => $partenaire->getNom(),
                        'type' => 'Partenaire',
                    ];
                }
            }
            
            $offensesData[] = [
                'id' => $delit->getId(),
                'type' => $type,
                'description' => $delit->getDescription(),
                'date' => $formatDate($delit->getDate()),
                'dateDeclaration' => $formatDate($delit->getDateDeclaration()),
                'statut' => $delit->getStatut() ? $delit->getStatut()->value : 'Inconnu',
                'gravite' => $delit->getGravite() ? $delit->getGravite()->value : 'Inconnu',
                'numeroAffaire' => $delit->getNumeroAffaire(),
                'procureurResponsable' => $delit->getProcureurResponsable(),
                'temoinsPrincipaux' => $delit->getTemoinsPrincipaux(),
                'preuvesPrincipales' => $delit->getPreuvesPrincipales(),
                'lieu' => $delit->getLieu() ? $delit->getLieu()->getAdresse() : null,
                'politiciens' => $politiciens,
                'partenaires' => $partenaires,
                'nombreCommentaires' => $delit->getCommentaires()->count(),
                'nombreDocuments' => $delit->getDocuments()->count(),
            ];
        }
        
        $selected = !empty($offensesData) ? $offensesData[0] : null;
        
        // Si on a un délit sélectionné, récupérer les données complètes
        if ($selected) {
            $delit = $em->getRepository(Delit::class)->find($selected['id']);

            // trier par ordre croissant
            $qb = $em->createQueryBuilder();
            $qb->select('c')
                ->from(Commentaire::class, 'c')
                ->where('c.delit = :delit')
                ->orderBy('c.dateCreation', 'DESC')
                ->setParameter('delit', $delit);
            
            $commentairesEntities = $qb->getQuery()->getResult();
            
            $commentaires = [];
            foreach ($commentairesEntities as $commentaire) {
                $commentaires[] = [
                    'id' => $commentaire->getId(),
                    'contenu' => $commentaire->getContenu(),
                    'dateCreation' => $formatDate($commentaire->getDateCreation()),
                    'auteur' => $commentaire->getAuteur() ? $commentaire->getAuteur()->getFirstName() . ' ' . $commentaire->getAuteur()->getLastName() : 'Anonyme',
                ];
            }
            
            // Récupérer les documents
            $documents = [];
            foreach ($delit->getDocuments() as $document) {
                // Déterminer le type de document
                $documentType = 'Document';
                if ($document instanceof DocumentImage) {
                    $documentType = 'Image';
                } elseif ($document instanceof DocumentVideo) {
                    $documentType = 'Vidéo';
                } elseif ($document instanceof DocumentAudio) {
                    $documentType = 'Audio';
                } elseif ($document instanceof DocumentFichier) {
                    $documentType = 'Fichier';
                }
                
                $documents[] = [
                    'id' => $document->getId(),
                    'titre' => $document->getNom(),
                    'type' => $documentType,
                    'dateCreation' => $formatDate($document->getDateCreation()),
                    'niveauConfidentialite' => $document->getNiveauConfidentialite() ? $document->getNiveauConfidentialite()->value : null,
                ];
            }
            
            $selected = [
                'id' => $delit->getId(),
                'type' => $delit->getType() ? $delit->getType()->value : 'delit',
                'description' => $delit->getDescription(),
                'date' => $formatDate($delit->getDate()),
                'dateDeclaration' => $formatDate($delit->getDateDeclaration()),
                'statut' => $delit->getStatut() ? $delit->getStatut()->value : 'Inconnu',
                'gravite' => $delit->getGravite() ? $delit->getGravite()->value : 'Inconnu',
                'numeroAffaire' => $delit->getNumeroAffaire(),
                'procureurResponsable' => $delit->getProcureurResponsable(),
                'temoinsPrincipaux' => $delit->getTemoinsPrincipaux(),
                'preuvesPrincipales' => $delit->getPreuvesPrincipales(),
                'lieu' => $delit->getLieu() ? $delit->getLieu()->getAdresse() : null,
                'politiciens' => $politiciens,
                'partenaires' => $partenaires,
                'commentaires' => $commentaires,
                'documents' => $documents,
                'typeFraude' => null,
                'documentsManipules' => null,
                'nombreVictimes' => null,
                'prejudiceEstime' => null,
                'methodeFraude' => null,
                'complicesIdentifies' => null,
                'systemeInformatique' => null,
                'fraudeOrganisee' => null,
                'biensDerobes' => null,
                'valeurEstimee' => null,
                'biensRecuperes' => null,
                'pourcentageRecupere' => null,
                'lieuStockage' => null,
                'methodeDerriereVol' => null,
                'receleurs' => null,
                'volPremedite' => null,
                'montantEstime' => null,
                'devise' => null,
                'methodePaiement' => null,
                'compteBancaire' => null,
                'paradissFiscal' => null,
                'blanchimentSoupçonne' => null,
                'institutionsImpliquees' => null,
                'circuitFinancier' => null,
                'montantRecupere' => null,
                'argentRecupere' => null,
            ];
            
            // Ajouter les données spécifiques selon le type
            if ($delit instanceof DelitFinancier) {
                $selected['montantEstime'] = $delit->getMontantEstime();
                $selected['devise'] = $delit->getDedevise() ? $delit->getDedevise()->value : null;
                $selected['methodePaiement'] = $delit->getMethodePaiement();
                $selected['compteBancaire'] = $delit->getCompteBancaire();
                $selected['paradissFiscal'] = $delit->getParadissFiscal();
                $selected['blanchimentSoupçonne'] = $delit->isBlanchimentSoupçonne();
                $selected['institutionsImpliquees'] = $delit->getInstitutionsImpliquees();
                $selected['circuitFinancier'] = $delit->getCircuitFinancier();
                $selected['montantRecupere'] = $delit->getMontantRecupere();
                $selected['argentRecupere'] = $delit->isArgentRecupere();
            } elseif ($delit instanceof DelitFraude) {
                $selected['typeFraude'] = $delit->getTypeFraude() ? $delit->getTypeFraude()->value : null;
                $selected['documentsManipules'] = $delit->getDocumentsManipules();
                $selected['nombreVictimes'] = $delit->getNombreVictimes();
                $selected['prejudiceEstime'] = $delit->getPrejudiceEstime();
                $selected['methodeFraude'] = array_map(fn($m) => $m->value, $delit->getMethodeFraude());
                $selected['complicesIdentifies'] = $delit->getComplicesIdentifies();
                $selected['systemeInformatique'] = $delit->isSystemeInformatique();
                $selected['fraudeOrganisee'] = $delit->isFraudeOrganisee();
            } elseif ($delit instanceof DelitVol) {
                $selected['biensDerobes'] = $delit->getBiensDerobes();
                $selected['valeurEstimee'] = $delit->getValeurEstimee();
                $selected['biensRecuperes'] = $delit->isBiensRecuperes();
                $selected['pourcentageRecupere'] = $delit->getPourcentageRecupere();
                $selected['lieuStockage'] = $delit->getLieuStockage();
                $selected['methodeDerriereVol'] = $delit->getMethodeDerriereVol();
                $selected['receleurs'] = $delit->getReceleurs();
                $selected['volPremedite'] = $delit->isVolPremedite();
            }
        }

        // Récupérer les types de délits depuis l'enum pour les filtres
        $delitTypes = [];
        foreach (DelitTypeEnum::cases() as $type) {
            $delitTypes[] = $type->value;
        }

        // Récupérer les statuts pour les filtres
        $delitStatuts = [];
        foreach (DelitStatutEnum::cases() as $statut) {
            $delitStatuts[] = $statut->value;
        }

        // Récupérer les gravités pour les filtres
        $delitGravites = [];
        foreach (DelitGraviteEnum::cases() as $gravite) {
            $delitGravites[] = $gravite->value;
        }
        
        return $this->render('offenses/offenses.html.twig', [
            'offenses' => $offensesData,
            'selected' => $selected,
            'politicians' => $this->getPoliticiansForTemplate($em),
            'partners' => $this->getPartnersForTemplate($em),
            'delitTypes' => $delitTypes,
            'delitStatuts' => $delitStatuts,
            'delitGravites' => $delitGravites,
        ]);
    }

    #[Route('/offenses/{id}/partial', name: 'app_offenses_partial')]
    public function offensesPartial(int $id, EntityManagerInterface $em): Response
    {
        $delit = $em->getRepository(Delit::class)->find($id);
        
        if (!$delit) {
            throw $this->createNotFoundException('Délit non trouvé');
        }
        
        // Fonction helper pour formater les dates de manière sécurisée
        $formatDate = function($date) {
            if ($date instanceof \DateTime) {
                return $date->format('d/m/Y');
            }
            return null;
        };
        
        // Déterminer le type de délit
        $type = $delit->getType() ? $delit->getType()->value : 'delit';
        
        // Récupérer les politiciens impliqués
        $politiciens = [];
        foreach ($delit->getPoliticiens() as $politicien) {
            $politiciens[] = [
                'id' => $politicien->getId(),
                'name' => $politicien->getFirstName() . ' ' . $politicien->getLastName(),
                'email' => $politicien->getEmail(),
            ];
        }
        
        // Récupérer les partenaires impliqués
        $partenaires = [];
        foreach ($delit->getPartenaires() as $partenaire) {
            if ($partenaire instanceof PartenairePhysique) {
                $partenaires[] = [
                    'id' => $partenaire->getId(),
                    'name' => $partenaire->getPrenom() . ' ' . $partenaire->getNomFamille(),
                    'type' => 'Individuel',
                ];
            } elseif ($partenaire instanceof PartenaireMoral) {
                $partenaires[] = [
                    'id' => $partenaire->getId(),
                    'name' => $partenaire->getRaisonSociale(),
                    'type' => 'Organisation',
                ];
            } else {
                $partenaires[] = [
                    'id' => $partenaire->getId(),
                    'name' => $partenaire->getNom(),
                    'type' => 'Partenaire',
                ];
            }
        }
        
        // Récupérer les commentaires (croissants)
        $qb = $em->createQueryBuilder();
        $qb->select('c')
            ->from(Commentaire::class, 'c')
            ->where('c.delit = :delit')
            ->orderBy('c.dateCreation', 'DESC')
            ->setParameter('delit', $delit);
        
        $commentairesEntities = $qb->getQuery()->getResult();
        
        $commentaires = [];
        foreach ($commentairesEntities as $commentaire) {
            $commentaires[] = [
                'id' => $commentaire->getId(),
                'contenu' => $commentaire->getContenu(),
                'dateCreation' => $formatDate($commentaire->getDateCreation()),
                'auteur' => $commentaire->getAuteur() ? $commentaire->getAuteur()->getFirstName() . ' ' . $commentaire->getAuteur()->getLastName() : 'Anonyme',
            ];
        }
        
        // Récupérer les documents
        $documents = [];
        foreach ($delit->getDocuments() as $document) {
            // Déterminer le type de document
            $documentType = 'Document';
            if ($document instanceof DocumentImage) {
                $documentType = 'Image';
            } elseif ($document instanceof DocumentVideo) {
                $documentType = 'Vidéo';
            } elseif ($document instanceof DocumentAudio) {
                $documentType = 'Audio';
            } elseif ($document instanceof DocumentFichier) {
                $documentType = 'Fichier';
            }
            
            $documents[] = [
                'id' => $document->getId(),
                'titre' => $document->getNom(),
                'type' => $documentType,
                'dateCreation' => $formatDate($document->getDateCreation()),
                'niveauConfidentialite' => $document->getNiveauConfidentialite() ? $document->getNiveauConfidentialite()->value : null,
            ];
        }
        
        $selected = [
            'id' => $delit->getId(),
            'type' => $delit->getType() ? $delit->getType()->value : 'delit',
            'description' => $delit->getDescription(),
            'date' => $formatDate($delit->getDate()),
            'dateDeclaration' => $formatDate($delit->getDateDeclaration()),
            'statut' => $delit->getStatut() ? $delit->getStatut()->value : 'Inconnu',
            'gravite' => $delit->getGravite() ? $delit->getGravite()->value : 'Inconnu',
            'numeroAffaire' => $delit->getNumeroAffaire(),
            'procureurResponsable' => $delit->getProcureurResponsable(),
            'temoinsPrincipaux' => $delit->getTemoinsPrincipaux(),
            'preuvesPrincipales' => $delit->getPreuvesPrincipales(),
            'lieu' => $delit->getLieu() ? $delit->getLieu()->getAdresse() : null,
            'politiciens' => $politiciens,
            'partenaires' => $partenaires,
            'commentaires' => $commentaires,
            'documents' => $documents,
            'typeFraude' => null,
            'documentsManipules' => null,
            'nombreVictimes' => null,
            'prejudiceEstime' => null,
            'methodeFraude' => null,
            'complicesIdentifies' => null,
            'systemeInformatique' => null,
            'fraudeOrganisee' => null,
            'biensDerobes' => null,
            'valeurEstimee' => null,
            'biensRecuperes' => null,
            'pourcentageRecupere' => null,
            'lieuStockage' => null,
            'methodeDerriereVol' => null,
            'receleurs' => null,
            'volPremedite' => null,
            'montantEstime' => null,
            'devise' => null,
            'methodePaiement' => null,
            'compteBancaire' => null,
            'paradissFiscal' => null,
            'blanchimentSoupçonne' => null,
            'institutionsImpliquees' => null,
            'circuitFinancier' => null,
            'montantRecupere' => null,
            'argentRecupere' => null,
        ];
        
        // Ajouter les données spécifiques selon le type
        if ($delit instanceof DelitFinancier) {
            $selected['montantEstime'] = $delit->getMontantEstime();
            $selected['devise'] = $delit->getDedevise() ? $delit->getDedevise()->value : null;
            $selected['methodePaiement'] = $delit->getMethodePaiement();
            $selected['compteBancaire'] = $delit->getCompteBancaire();
            $selected['paradissFiscal'] = $delit->getParadissFiscal();
            $selected['blanchimentSoupçonne'] = $delit->isBlanchimentSoupçonne();
            $selected['institutionsImpliquees'] = $delit->getInstitutionsImpliquees();
            $selected['circuitFinancier'] = $delit->getCircuitFinancier();
            $selected['montantRecupere'] = $delit->getMontantRecupere();
            $selected['argentRecupere'] = $delit->isArgentRecupere();
        } elseif ($delit instanceof DelitFraude) {
            $selected['typeFraude'] = $delit->getTypeFraude() ? $delit->getTypeFraude()->value : null;
            $selected['documentsManipules'] = $delit->getDocumentsManipules();
            $selected['nombreVictimes'] = $delit->getNombreVictimes();
            $selected['prejudiceEstime'] = $delit->getPrejudiceEstime();
            $selected['methodeFraude'] = array_map(fn($m) => $m->value, $delit->getMethodeFraude());
            $selected['complicesIdentifies'] = $delit->getComplicesIdentifies();
            $selected['systemeInformatique'] = $delit->isSystemeInformatique();
            $selected['fraudeOrganisee'] = $delit->isFraudeOrganisee();
        } elseif ($delit instanceof DelitVol) {
            $selected['biensDerobes'] = $delit->getBiensDerobes();
            $selected['valeurEstimee'] = $delit->getValeurEstimee();
            $selected['biensRecuperes'] = $delit->isBiensRecuperes();
            $selected['pourcentageRecupere'] = $delit->getPourcentageRecupere();
            $selected['lieuStockage'] = $delit->getLieuStockage();
            $selected['methodeDerriereVol'] = $delit->getMethodeDerriereVol();
            $selected['receleurs'] = $delit->getReceleurs();
            $selected['volPremedite'] = $delit->isVolPremedite();
        }
        
        return $this->render('offenses/components/offense_detail.html.twig', [
            'selected' => $selected,
        ]);
    }

    #[Route('/politics', name: 'app_politics')]
    public function politics(EntityManagerInterface $em): Response
    {
        // Récupérer tous les utilisateurs et filtrer côté PHP
        $users = $em->getRepository(User::class)->findAll();
        
        $politicians = [];
        foreach ($users as $user) {
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
                'offenses' => [],
                'timeline' => [],
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

        // Récupérer les délits associés au politicien
        $qb = $em->createQueryBuilder();
        $qb->select('delit')
        ->from(Delit::class, 'delit')
        ->join('delit.politiciens', 'politic')
        ->where('politic.id = :politicianId')
        ->orderBy('delit.date', 'DESC')
        ->setParameter('politicianId', $politician->getId());
        
        $offenses = $qb->getQuery()->getResult();
        
        // Formattage
        $offensesData = [];
        foreach ($offenses as $offense) {
            $offensesData[] = [
                'id' => $offense->getId(),
                'type' => $offense->getType()->value,
                'description' => $offense->getDescription(),
                'date' => $offense->getDate()->format('d/m/Y'),
                'statut' => $offense->getStatut()->value,
                'gravite' => $offense->getGravite()->value,
            ];
        }

        // Générer la timeline
        $timelineData = [];
        
        // Ajouter les délits à la timeline
        foreach ($offenses as $offense) {
            $timelineData[] = [
                'date' => $offense->getDate()->format('d/m/Y'),
                'title' => 'Délit: ' . $offense->getType()->value,
                'description' => $offense->getDescription(),
                'type' => 'delit',
                'statut' => $offense->getStatut()->value,
                'gravite' => $offense->getGravite()->value,
            ];
        }

        if ($politician->getDateEntreePolitique()) {
            $timelineData[] = [
                'date' => $politician->getDateEntreePolitique()->format('d/m/Y'),
                'title' => 'Entrée en politique',
                'description' => 'Début de carrière politique',
                'type' => 'carriere',
            ];
        }

        if ($politician->getDateNaissance()) {
            $timelineData[] = [
                'date' => $politician->getDateNaissance()->format('d/m/Y'),
                'title' => 'Naissance',
                'description' => 'Date de naissance',
                'type' => 'naissance',
            ];
        }
        
        // Trier la timeline par date (du plus récent au plus ancien)
        usort($timelineData, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        // Générer une image placeholder basée sur le nom
        $placeholderImage = $this->generatePlaceholderImage($politician->getFirstName(), $politician->getLastName());
        
        $politicianData = [
            'id' => $politician->getId(),
            'name' => $politician->getFirstName() . ' ' . $politician->getLastName(),
            'firstName' => $politician->getFirstName(),
            'lastName' => $politician->getLastName(),
            'email' => $politician->getEmail(),
            'role' => $this->getPoliticianRole($politician),
            'offenses' => $offensesData,
            'timeline' => $timelineData,
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
        $user = new Politicien();
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
        if (!empty($data['dateEntreePolitique'])) {
            $user->setDateEntreePolitique(new \DateTime($data['dateEntreePolitique']));
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
                    'date' => $delit->getDate() ? $delit->getDate()->format('d/m/Y') : 'Date inconnue',
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
                'date' => $delit->getDate() ? $delit->getDate()->format('d/m/Y') : 'Date inconnue',
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

//    #[Route('/medias', name: 'app_media')]
//    public function media(): Response
//    {
//        $media = [];
//        return $this->render('media/media.html.twig', [
//            'media' => $media,
//        ]);
//    }

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

    #[Route('/offenses/add', name: 'offenses_add', methods: ['POST'])]
    public function addOffense(Request $request, EntityManagerInterface $em): JsonResponse
    {
        error_log('=== DÉBUT ADD OFFENSE ===');
        
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        try {
            error_log('Récupération des données du formulaire...');
            
            $type = $request->request->get('type');
            $description = $request->request->get('description');
            $date = $request->request->get('date');
            $dateDeclaration = $request->request->get('dateDeclaration');
            $statut = $request->request->get('statut');
            $gravite = $request->request->get('gravite');
            $numeroAffaire = $request->request->get('numeroAffaire');
            $procureurResponsable = $request->request->get('procureurResponsable');
            $politiciensIds = $request->request->all('politiciens');
            $partenairesIds = $request->request->all('partenaires');
            
            error_log('Type: ' . $type);
            error_log('Description: ' . $description);
            error_log('Date: ' . $date);
            error_log('Statut: ' . $statut);
            error_log('Gravité: ' . $gravite);
            error_log('Politiciens: ' . json_encode($politiciensIds));
            error_log('Partenaires: ' . json_encode($partenairesIds));
            
            // Créer le délit selon le type
            error_log('Création du délit selon le type: ' . $type);
            
            // Créer un délit de base et définir le type
            $delit = new Delit();
            $delit->setType(DelitTypeEnum::from($type));
            
            error_log('Délit créé, classe: ' . get_class($delit));
            
            // Remplir les propriétés communes
            error_log('Remplissage des propriétés communes...');
            $delit->setDescription($description);
            $delit->setDate(new \DateTime($date));
            if ($dateDeclaration) {
                $delit->setDateDeclaration(new \DateTime($dateDeclaration));
            }
            $delit->setStatut(DelitStatutEnum::from($statut));
            $delit->setGravite(DelitGraviteEnum::from($gravite));
            $delit->setNumeroAffaire($numeroAffaire);
            $delit->setProcureurResponsable($procureurResponsable);
            
            error_log('Propriétés communes remplies');
            
            // Ajouter les politiciens (optionnel pour le moment)
            error_log('Ajout des politiciens...');
            foreach ($politiciensIds as $politicienId) {
                if ($politicienId) {
                    try {
                        $politicien = $em->getRepository(Politicien::class)->find($politicienId);
                        if ($politicien) {
                            $delit->addPoliticien($politicien);
                            error_log('Politicien ajouté: ' . $politicien->getEmail());
                        } else {
                            error_log('Politicien non trouvé avec ID: ' . $politicienId);
                        }
                    } catch (\Exception $e) {
                        error_log('Erreur lors de la récupération du politicien: ' . $e->getMessage());
                    }
                }
            }
            
            // Ajouter les partenaires (optionnel pour le moment)
            error_log('Ajout des partenaires...');
            foreach ($partenairesIds as $partenaireId) {
                if ($partenaireId) {
                    try {
                        $partenaire = $em->getRepository(Partenaire::class)->find($partenaireId);
                        if ($partenaire) {
                            $delit->addPartenaire($partenaire);
                            error_log('Partenaire ajouté: ' . $partenaire->getNom());
                        } else {
                            error_log('Partenaire non trouvé avec ID: ' . $partenaireId);
                        }
                    } catch (\Exception $e) {
                        error_log('Erreur lors de la récupération du partenaire: ' . $e->getMessage());
                    }
                }
            }
            
            error_log('Persistance du délit...');
            $em->persist($delit);
            
            error_log('Flush de la base de données...');
            $em->flush();
            
            error_log('Délit ajouté avec succès, ID: ' . $delit->getId());
            error_log('=== FIN ADD OFFENSE SUCCÈS ===');
            
            return new JsonResponse(['success' => true, 'id' => $delit->getId()]);
            
        } catch (\Exception $e) {
            error_log('ERREUR dans addOffense: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            error_log('=== FIN ADD OFFENSE ERREUR ===');
            return new JsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    #[Route('/api/politicians', name: 'api_politicians')]
    public function getPoliticians(EntityManagerInterface $em): JsonResponse
    {
        $users = $em->getRepository(User::class)->findAll();
        
        error_log('API Politicians - Total users found: ' . count($users));
        
        $politicians = [];
        foreach ($users as $user) {
            error_log('API Politicians - User: ' . $user->getEmail() . ' - Roles: ' . json_encode($user->getRoles()));
            if (in_array('ROLE_POLITICIAN', $user->getRoles())) {
                $politicians[] = [
                    'id' => $user->getId(),
                    'name' => $user->getFirstName() . ' ' . $user->getLastName(),
                    'email' => $user->getEmail(),
                ];
            }
        }
        
        error_log('API Politicians - Politicians found: ' . count($politicians));
        
        return new JsonResponse($politicians);
    }

    #[Route('/api/partners', name: 'api_partners')]
    public function getPartners(EntityManagerInterface $em): JsonResponse
    {
        $partenaires = $em->getRepository(Partenaire::class)->findAll();
        
        error_log('API Partners - Total partners found: ' . count($partenaires));
        
        $partners = [];
        foreach ($partenaires as $partenaire) {
            if ($partenaire instanceof PartenairePhysique) {
                $partners[] = [
                    'id' => $partenaire->getId(),
                    'name' => $partenaire->getPrenom() . ' ' . $partenaire->getNomFamille(),
                    'type' => 'Individuel',
                ];
            } elseif ($partenaire instanceof PartenaireMoral) {
                $partners[] = [
                    'id' => $partenaire->getId(),
                    'name' => $partenaire->getRaisonSociale(),
                    'type' => 'Entreprise',
                ];
            }
        }
        
        error_log('API Partners - Processed partners: ' . count($partners));
        
        return new JsonResponse($partners);
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
        
        if (str_contains($email, 'president.fr')) {
            return 'Président de la République';
        } elseif (str_contains($email, 'chefpolitique.fr')) {
            return 'Chef de parti politique';
        } elseif (str_contains($email, 'gouv.fr')) {
            return 'Membre du gouvernement';
        } elseif (str_contains($email, 'politicien.fr')) {
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
        
        $bio .= ". Elle s'engage activement dans la vie politique et représente les intérêts de ses concitoyens.";
        
        return $bio;
    }

    #[Route('/offenses/{id}/edit', name: 'offenses_edit', methods: ['GET'])]
    public function editOffense(int $id, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $delit = $em->getRepository(Delit::class)->find($id);
        
        if (!$delit) {
            return new JsonResponse(['success' => false, 'message' => 'Délit non trouvé']);
        }
        
        // Récupérer les IDs des politiciens et partenaires
        $politiciensIds = [];
        foreach ($delit->getPoliticiens() as $politicien) {
            $politiciensIds[] = $politicien->getId();
        }
        
        $partenairesIds = [];
        foreach ($delit->getPartenaires() as $partenaire) {
            $partenairesIds[] = $partenaire->getId();
        }
        
        $offenseData = [
            'id' => $delit->getId(),
            'type' => $delit->getType()->value,
            'description' => $delit->getDescription(),
            'date' => $delit->getDate() ? $delit->getDate()->format('Y-m-d') : null,
            'dateDeclaration' => $delit->getDateDeclaration() ? $delit->getDateDeclaration()->format('Y-m-d') : null,
            'statut' => $delit->getStatut()->value,
            'gravite' => $delit->getGravite()->value,
            'numeroAffaire' => $delit->getNumeroAffaire(),
            'procureurResponsable' => $delit->getProcureurResponsable(),
            'politiciens' => $politiciensIds,
            'partenaires' => $partenairesIds,
        ];
        
        return new JsonResponse(['success' => true, 'offense' => $offenseData]);
    }

    #[Route('/offenses/{id}/update', name: 'offenses_update', methods: ['POST'])]
    public function updateOffense(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $delit = $em->getRepository(Delit::class)->find($id);
        
        if (!$delit) {
            return new JsonResponse(['success' => false, 'error' => 'Délit non trouvé']);
        }
        
        try {
            $description = $request->request->get('description');
            $date = $request->request->get('date');
            $dateDeclaration = $request->request->get('dateDeclaration');
            $statut = $request->request->get('statut');
            $gravite = $request->request->get('gravite');
            $numeroAffaire = $request->request->get('numeroAffaire');
            $procureurResponsable = $request->request->get('procureurResponsable');
            $politiciensIds = $request->request->all('politiciens');
            $partenairesIds = $request->request->all('partenaires');
            
            // Mettre à jour les propriétés
            $delit->setDescription($description);
            $delit->setDate(new \DateTime($date));
            if ($dateDeclaration) {
                $delit->setDateDeclaration(new \DateTime($dateDeclaration));
            }
            $delit->setStatut(DelitStatutEnum::from($statut));
            $delit->setGravite(DelitGraviteEnum::from($gravite));
            $delit->setNumeroAffaire($numeroAffaire);
            $delit->setProcureurResponsable($procureurResponsable);
            
            // Vider les collections existantes
            $delit->getPoliticiens()->clear();
            $delit->getPartenaires()->clear();
            
            // Ajouter les nouveaux politiciens
            foreach ($politiciensIds as $politicienId) {
                if ($politicienId) {
                    $politicien = $em->getRepository(Politicien::class)->find($politicienId);
                    if ($politicien) {
                        $delit->addPoliticien($politicien);
                    }
                }
            }
            
            // Ajouter les nouveaux partenaires
            foreach ($partenairesIds as $partenaireId) {
                if ($partenaireId) {
                    $partenaire = $em->getRepository(Partenaire::class)->find($partenaireId);
                    if ($partenaire) {
                        $delit->addPartenaire($partenaire);
                    }
                }
            }
            
            $em->flush();
            
            return new JsonResponse(['success' => true]);
            
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Récupère les politiciens pour le template
     */
    private function getPoliticiansForTemplate(EntityManagerInterface $em): array
    {
        $politiciens = $em->getRepository(Politicien::class)->findAll();
        
        $politicians = [];
        foreach ($politiciens as $politicien) {
            $politicians[] = [
                'id' => $politicien->getId(),
                'name' => $politicien->getFirstName() . ' ' . $politicien->getLastName(),
                'email' => $politicien->getEmail(),
            ];
        }
        
        return $politicians;
    }

    /**
     * Récupère les partenaires pour le template
     */
    private function getPartnersForTemplate(EntityManagerInterface $em): array
    {
        $partenaires = $em->getRepository(Partenaire::class)->findAll();
        
        $partners = [];
        foreach ($partenaires as $partenaire) {
            if ($partenaire instanceof PartenairePhysique) {
                $partners[] = [
                    'id' => $partenaire->getId(),
                    'name' => $partenaire->getPrenom() . ' ' . $partenaire->getNomFamille(),
                    'type' => 'Individuel',
                ];
            } elseif ($partenaire instanceof PartenaireMoral) {
                $partners[] = [
                    'id' => $partenaire->getId(),
                    'name' => $partenaire->getRaisonSociale(),
                    'type' => 'Entreprise',
                ];
            }
        }
        
        return $partners;
    }

    private function getTimeAgo(\DateTime $date): string
    {
        $now = new \DateTime();
        $diff = $now->diff($date);

        if ($diff->y > 0) {
            return 'il y a ' . $diff->y . ' an' . ($diff->y > 1 ? 's' : '');
        } elseif ($diff->m > 0) {
            return 'il y a ' . $diff->m . ' mois';
        } elseif ($diff->d > 0) {
            return 'il y a ' . $diff->d . ' jour' . ($diff->d > 1 ? 's' : '');
        } elseif ($diff->h > 0) {
            return 'il y a ' . $diff->h . ' heure' . ($diff->h > 1 ? 's' : '');
        } elseif ($diff->i > 0) {
            return 'il y a ' . $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
        } else {
            return 'À l\'instant';
        }
    }

    #[Route('/offenses/{id}/comment', name: 'offenses_add_comment', methods: ['POST'])]
    public function addComment(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        try {
            $delit = $em->getRepository(Delit::class)->find($id);
            if (!$delit) {
                return new JsonResponse(['success' => false, 'error' => 'Délit non trouvé'], 404);
            }

            $contenu = $request->request->get('contenu');
            if (!$contenu) {
                return new JsonResponse(['success' => false, 'error' => 'Le contenu du commentaire est requis'], 400);
            }

            // Récupérer l'utilisateur connecté
            $user = $this->getUser();
            if (!$user) {
                return new JsonResponse(['success' => false, 'error' => 'Utilisateur non connecté'], 401);
            }

            // Créer le commentaire
            $commentaire = new Commentaire();
            $commentaire->setContenu($contenu);
            $commentaire->setDateCreation(new \DateTime());
            $commentaire->setDelit($delit);
            $commentaire->setAuteur($user);
            $commentaire->setEstPublic(true);

            $em->persist($commentaire);
            $em->flush();

            // Retourner les données du commentaire créé
            $commentaireData = [
                'id' => $commentaire->getId(),
                'contenu' => $commentaire->getContenu(),
                'dateCreation' => $commentaire->getDateCreation()->format('d/m/Y H:i'),
                'auteur' => $user->getFirstName() . ' ' . $user->getLastName(),
            ];

            return new JsonResponse([
                'success' => true,
                'commentaire' => $commentaireData
            ]);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    #[Route('/offenses/{id}/comments', name: 'offenses_get_comments', methods: ['GET'])]
    public function getComments(int $id, EntityManagerInterface $em): JsonResponse
    {
        try {
            $delit = $em->getRepository(Delit::class)->find($id);
            if (!$delit) {
                return new JsonResponse(['success' => false, 'error' => 'Délit non trouvé'], 404);
            }

            // Récupérer les commentaires (croissants)
            $qb = $em->createQueryBuilder();
            $qb->select('c')
                ->from(Commentaire::class, 'c')
                ->where('c.delit = :delit')
                ->orderBy('c.dateCreation', 'DESC')
                ->setParameter('delit', $delit);
            
            $commentairesEntities = $qb->getQuery()->getResult();
            
            $commentaires = [];
            foreach ($commentairesEntities as $commentaire) {
                $auteur = $commentaire->getAuteur();
                $nomAuteur = 'Anonyme';
                if ($auteur instanceof User) {
                    $nomAuteur = $auteur->getFirstName() . ' ' . $auteur->getLastName();
                }
                
                $commentaires[] = [
                    'id' => $commentaire->getId(),
                    'contenu' => $commentaire->getContenu(),
                    'dateCreation' => $commentaire->getDateCreation() ? $commentaire->getDateCreation()->format('d/m/Y H:i') : null,
                    'auteur' => $nomAuteur,
                ];
            }

            return new JsonResponse([
                'success' => true,
                'commentaires' => $commentaires
            ]);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
} 
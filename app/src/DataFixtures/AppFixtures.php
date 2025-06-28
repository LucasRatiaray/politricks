<?php

namespace App\DataFixtures;

use App\Entity\Commentaire;
use App\Entity\Delit;
use App\Entity\Document;
use App\Entity\Lieu;
use App\Entity\Partenaire;
use App\Entity\Parti;
use App\Entity\Politicien;
use App\Entity\User;
use App\Enum\DelitGraviteEnum;
use App\Enum\DelitStatutEnum;
use App\Enum\DelitTypeEnum;
use App\Enum\DocumentLangueDocumentEnum;
use App\Enum\DocumentNiveauConfidentialiteEnum;
use App\Enum\PartenaireNiveauRisqueEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $passwordHash = '$2y$13$JFjWZsbpfZXIPranyB44deXu5l8vffHUpIKEmkG0xiEi2552AR1ou'; // hash de "password" fait avec : docker exec -it politricks-php-1 php bin/console security:hash-password

        // Créer les utilisateurs
        $users = $this->createUsers($manager, $passwordHash);
        
        // Créer les partis
        $partis = $this->createPartis($manager);
        
        // Créer les politiciens
        $politiciens = $this->createPoliticiens($manager, $passwordHash, $partis);
        
        // Créer les lieux
        $lieux = $this->createLieux($manager);
        
        // Créer les délits
        $delits = $this->createDelits($manager, $lieux);
        
        // Créer les commentaires
        $this->createCommentaires($manager, $politiciens, $delits);
        
        // Créer les documents
        $this->createDocuments($manager, $politiciens, $delits);
        
        // Créer les partenaires
        $this->createPartenaires($manager);

        $manager->flush();
    }

    private function createUsers(ObjectManager $manager, string $passwordHash): array
    {
        $users = [];

        // Admin
        $admin = new User();
        $admin->setEmail('admin@politricks.com');
        $admin->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $admin->setPassword($passwordHash);
        $admin->setFirstName('Admin');
        $admin->setLastName('System');
        $admin->setDateCreation(new \DateTime());
        $admin->setEstActif(true);
        $admin->setTelephone('+33123456789');
        $admin->setDateNaissance(new \DateTime('1980-01-01'));
        $admin->setNationalite('Française');
        $admin->setProfession('Administrateur système');
        $manager->persist($admin);
        $users[] = $admin;

        // Modérateur
        $moderator = new User();
        $moderator->setEmail('moderator@politricks.com');
        $moderator->setRoles(['ROLE_MODERATOR', 'ROLE_USER']);
        $moderator->setPassword($passwordHash);
        $moderator->setFirstName('Jean');
        $moderator->setLastName('Modérateur');
        $moderator->setDateCreation(new \DateTime());
        $moderator->setEstActif(true);
        $moderator->setTelephone('+33987654321');
        $moderator->setDateNaissance(new \DateTime('1985-05-05'));
        $moderator->setNationalite('Française');
        $moderator->setProfession('Journaliste');
        $manager->persist($moderator);
        $users[] = $moderator;

        // Utilisateurs normaux
        for ($i = 1; $i <= 5; $i++) {
            $user = new User();
            $user->setEmail("user{$i}@example.com");
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($passwordHash);
            $user->setFirstName("Prénom{$i}");
            $user->setLastName("Nom{$i}");
            $user->setDateCreation(new \DateTime());
            $user->setEstActif(true);
            $user->setTelephone("+33" . rand(100000000, 999999999));
            $user->setDateNaissance(new \DateTime('-' . rand(18, 70) . ' years'));
            $user->setNationalite('Française');
            $user->setProfession("Profession{$i}");
            $manager->persist($user);
            $users[] = $user;
        }

        return $users;
    }

    private function createPartis(ObjectManager $manager): array
    {
        $partis = [];

        $partiData = [
            [
                'nom' => 'La République En Marche',
                'couleur' => '#FFEB3B',
                'slogan' => 'En Marche vers le progrès',
                'description' => 'Parti politique centriste français fondé par Emmanuel Macron',
                'dateCreation' => new \DateTime('2016-04-06'),
                'siteWeb' => 'https://en-marche.fr',
                'adresseSiege' => '63 rue Sainte-Anne, 75002 Paris',
                'telephoneContact' => '01 44 50 50 50',
                'emailContact' => 'contact@en-marche.fr',
                'orientationPolitique' => 'Centre',
                'budgetAnnuel' => 15000000,
                'nombreAdherents' => 400000,
                'partiActif' => true
            ],
            [
                'nom' => 'Les Républicains',
                'couleur' => '#0066CC',
                'slogan' => 'Liberté, Responsabilité, Solidarité',
                'description' => 'Parti politique de droite français',
                'dateCreation' => new \DateTime('2015-05-30'),
                'siteWeb' => 'https://www.republicains.fr',
                'adresseSiege' => '238 rue de Vaugirard, 75015 Paris',
                'telephoneContact' => '01 40 76 60 00',
                'emailContact' => 'contact@republicains.fr',
                'orientationPolitique' => 'Droite',
                'budgetAnnuel' => 12000000,
                'nombreAdherents' => 150000,
                'partiActif' => true
            ],
            [
                'nom' => 'Parti Socialiste',
                'couleur' => '#FF69B4',
                'slogan' => 'Changer la vie',
                'description' => 'Parti politique de gauche français',
                'dateCreation' => new \DateTime('1971-07-11'),
                'siteWeb' => 'https://www.parti-socialiste.fr',
                'adresseSiege' => '10 rue de Solférino, 75007 Paris',
                'telephoneContact' => '01 45 56 77 00',
                'emailContact' => 'contact@parti-socialiste.fr',
                'orientationPolitique' => 'Gauche',
                'budgetAnnuel' => 8000000,
                'nombreAdherents' => 80000,
                'partiActif' => true
            ],
            [
                'nom' => 'Rassemblement National',
                'couleur' => '#003399',
                'slogan' => 'Au nom du peuple',
                'description' => 'Parti politique d\'extrême droite français',
                'dateCreation' => new \DateTime('1972-10-05'),
                'siteWeb' => 'https://rassemblementnational.fr',
                'adresseSiege' => '8 rue du Général Clergerie, 75016 Paris',
                'telephoneContact' => '01 41 20 90 90',
                'emailContact' => 'contact@rn.fr',
                'orientationPolitique' => 'Extrême droite',
                'budgetAnnuel' => 10000000,
                'nombreAdherents' => 100000,
                'partiActif' => true
            ]
        ];

        foreach ($partiData as $data) {
            $parti = new Parti();
            $parti->setNom($data['nom']);
            $parti->setCouleur($data['couleur']);
            $parti->setSlogan($data['slogan']);
            $parti->setDescription($data['description']);
            $parti->setDateCreation($data['dateCreation']);
            $parti->setSiteWeb($data['siteWeb']);
            $parti->setAdresseSiege($data['adresseSiege']);
            $parti->setTelephoneContact($data['telephoneContact']);
            $parti->setEmailContact($data['emailContact']);
            $parti->setOrientationPolitique($data['orientationPolitique']);
            $parti->setBudgetAnnuel($data['budgetAnnuel']);
            $parti->setNombreAdherents($data['nombreAdherents']);
            $parti->setPartiActif($data['partiActif']);
            
            $manager->persist($parti);
            $partis[] = $parti;
        }

        return $partis;
    }

    private function createPoliticiens(ObjectManager $manager, string $passwordHash, array $partis): array
    {
        $politiciens = [];

        // Emmanuel Macron
        $macron = new Politicien();
        $macron->setEmail('emmanuel.macron@elysee.fr');
        $macron->setRoles(['ROLE_POLITICIAN', 'ROLE_USER']);
        $macron->setPassword($passwordHash);
        $macron->setFirstName('Emmanuel');
        $macron->setLastName('Macron');
        $macron->setDateCreation(new \DateTime());
        $macron->setEstActif(true);
        $macron->setTelephone('+33144507000');
        $macron->setDateNaissance(new \DateTime('1977-12-21'));
        $macron->setNationalite('Française');
        $macron->setProfession('Président de la République');
        $macron->setBiographie('Président de la République française depuis 2017');
        $macron->setFonction('Président de la République');
        $macron->setDateEntreePolitique(new \DateTime('2012-05-15'));
        $macron->setMandatActuel('Président de la République (2017-2027)');
        $macron->setCirconscription('National');
        $macron->setSalaireMensuel('15140');
        $macron->setDeclarationPatrimoine([
            'immobilier' => 1200000,
            'mobilier' => 300000,
            'comptes' => 150000
        ]);
        $macron->setCasierJudiciaire('Vierge');
        $macron->setParti($partis[0]); // LREM
        $manager->persist($macron);
        $politiciens[] = $macron;

        // Marine Le Pen
        $lepen = new Politicien();
        $lepen->setEmail('marine.lepen@rn.fr');
        $lepen->setRoles(['ROLE_POLITICIAN', 'ROLE_USER']);
        $lepen->setPassword($passwordHash);
        $lepen->setFirstName('Marine');
        $lepen->setLastName('Le Pen');
        $lepen->setDateCreation(new \DateTime());
        $lepen->setEstActif(true);
        $lepen->setTelephone('+33141209090');
        $lepen->setDateNaissance(new \DateTime('1968-08-05'));
        $lepen->setNationalite('Française');
        $lepen->setProfession('Députée européenne');
        $lepen->setBiographie('Présidente du Rassemblement National');
        $lepen->setFonction('Présidente de parti');
        $lepen->setDateEntreePolitique(new \DateTime('1986-09-01'));
        $lepen->setMandatActuel('Députée européenne');
        $lepen->setCirconscription('Pas-de-Calais (11ème)');
        $lepen->setSalaireMensuel('7239');
        $lepen->setDeclarationPatrimoine([
            'immobilier' => 800000,
            'mobilier' => 200000,
            'comptes' => 100000
        ]);
        $lepen->setCasierJudiciaire('Vierge');
        $lepen->setParti($partis[3]); // RN
        $manager->persist($lepen);
        $politiciens[] = $lepen;

        // Politiciens fictifs
        for ($i = 1; $i <= 10; $i++) {
            $politicien = new Politicien();
            $politicien->setEmail("politicien{$i}@example.com");
            $politicien->setRoles(['ROLE_POLITICIAN', 'ROLE_USER']);
            $politicien->setPassword($passwordHash);
            $politicien->setFirstName("Prénom{$i}");
            $politicien->setLastName("Nom{$i}");
            $politicien->setDateCreation(new \DateTime());
            $politicien->setEstActif(true);
            $politicien->setTelephone("+33" . rand(100000000, 999999999));
            $politicien->setDateNaissance(new \DateTime('-' . rand(25, 70) . ' years'));
            $politicien->setNationalite('Française');
            $politicien->setProfession('Député');
            $politicien->setBiographie("Biographie du politicien {$i}");
            $politicien->setFonction('Député');
            $politicien->setDateEntreePolitique(new \DateTime('-' . rand(2, 20) . ' years'));
            $politicien->setMandatActuel('Député');
            $politicien->setCirconscription("Circonscription {$i}");
            $politicien->setSalaireMensuel((string)rand(3000, 15000));
            $politicien->setDeclarationPatrimoine([
                'immobilier' => rand(200000, 2000000),
                'mobilier' => rand(50000, 500000),
                'comptes' => rand(10000, 300000)
            ]);
            $politicien->setCasierJudiciaire('Vierge');
            $politicien->setParti($partis[array_rand($partis)]);
            $manager->persist($politicien);
            $politiciens[] = $politicien;
        }

        return $politiciens;
    }

    private function createLieux(ObjectManager $manager): array
    {
        $lieux = [];

        $lieuData = [
            [
                'adresse' => '55 rue du Faubourg Saint-Honoré',
                'ville' => 'Paris',
                'pays' => 'France',
                'codePostal' => '75008',
                'latitude' => '48.8700',
                'longitude' => '2.3165',
                'typeEtablissement' => 'Palais présidentiel',
                'estPublic' => true,
                'niveauSecurite' => 'Maximum',
                'capaciteAccueil' => 500,
                'horaireAcces' => 'Sur rendez-vous uniquement',
                'responsableSecurite' => 'Service de protection présidentielle',
                'videoSurveillance' => true
            ],
            [
                'adresse' => 'Palais du Luxembourg',
                'ville' => 'Paris',
                'pays' => 'France',
                'codePostal' => '75006',
                'latitude' => '48.8482',
                'longitude' => '2.3370',
                'typeEtablissement' => 'Sénat',
                'estPublic' => true,
                'niveauSecurite' => 'Élevé',
                'capaciteAccueil' => 1000,
                'horaireAcces' => '9h-18h du lundi au vendredi',
                'responsableSecurite' => 'Garde républicaine',
                'videoSurveillance' => true
            ],
            [
                'adresse' => '126 rue de l\'Université',
                'ville' => 'Paris',
                'pays' => 'France',
                'codePostal' => '75007',
                'latitude' => '48.8606',
                'longitude' => '2.3102',
                'typeEtablissement' => 'Assemblée Nationale',
                'estPublic' => true,
                'niveauSecurite' => 'Élevé',
                'capaciteAccueil' => 800,
                'horaireAcces' => '9h-18h selon sessions',
                'responsableSecurite' => 'Service de sécurité parlementaire',
                'videoSurveillance' => true
            ]
        ];

        foreach ($lieuData as $data) {
            $lieu = new Lieu();
            $lieu->setAdresse($data['adresse']);
            $lieu->setVille($data['ville']);
            $lieu->setPays($data['pays']);
            $lieu->setCodePostal($data['codePostal']);
            $lieu->setLatitude($data['latitude']);
            $lieu->setLongitude($data['longitude']);
            $lieu->setTypeEtablissement($data['typeEtablissement']);
            $lieu->setEstPublic($data['estPublic']);
            $lieu->setNiveauSecurite($data['niveauSecurite']);
            $lieu->setCapaciteAccueil($data['capaciteAccueil']);
            $lieu->setHoraireAcces($data['horaireAcces']);
            $lieu->setResponsableSecurite($data['responsableSecurite']);
            $lieu->setVideoSurveillance($data['videoSurveillance']);
            
            $manager->persist($lieu);
            $lieux[] = $lieu;
        }

        // Lieux fictifs
        for ($i = 1; $i <= 5; $i++) {
            $lieu = new Lieu();
            $lieu->setAdresse("Adresse {$i}");
            $lieu->setVille("Ville {$i}");
            $lieu->setPays('France');
            $lieu->setCodePostal(rand(10000, 99999));
            $lieu->setLatitude((string)(rand(43000000, 51000000) / 1000000));
            $lieu->setLongitude((string)(rand(-5000000, 8000000) / 1000000));
            $lieu->setTypeEtablissement('Mairie');
            $lieu->setEstPublic(true);
            $lieu->setNiveauSecurite('Modéré');
            $lieu->setCapaciteAccueil(rand(50, 1000));
            $lieu->setHoraireAcces('9h-17h');
            $lieu->setResponsableSecurite("Responsable {$i}");
            $lieu->setVideoSurveillance(true);
            
            $manager->persist($lieu);
            $lieux[] = $lieu;
        }

        return $lieux;
    }

    private function createDelits(ObjectManager $manager, array $lieux): array
    {
        $delits = [];

        $delitData = [
            [
                'type' => DelitTypeEnum::Fraude,
                'description' => 'Affaire de corruption présumée impliquant des élus locaux',
                'date' => new \DateTime('-1 year'),
                'statut' => DelitStatutEnum::EnCours,
                'gravite' => DelitGraviteEnum::Grave,
                'lieu' => $lieux[0]
            ],
            [
                'type' => DelitTypeEnum::Escroquerie,
                'description' => 'Fraude fiscale présumée dans une entreprise publique',
                'date' => new \DateTime('-6 months'),
                'statut' => DelitStatutEnum::EnInstruction,
                'gravite' => DelitGraviteEnum::Modere,
                'lieu' => $lieux[1]
            ]
        ];

        foreach ($delitData as $data) {
            $delit = new Delit();
            $delit->setType($data['type']);
            $delit->setDescription($data['description']);
            $delit->setDate($data['date']);
            $delit->setStatut($data['statut']);
            $delit->setGravite($data['gravite']);
            $delit->setDateDeclaration($data['date']);
            $delit->setNumeroAffaire('AF' . rand(100000, 999999));
            $delit->setProcureurResponsable('Procureur ' . rand(1, 10));
            $delit->setTemoinsPrincipaux(['Témoin 1', 'Témoin 2', 'Témoin 3']);
            $delit->setPreuvesPrincipales(['Document', 'Témoignage', 'Enregistrement']);
            $delit->setLieu($data['lieu']);
            
            $manager->persist($delit);
            $delits[] = $delit;
        }

        // Délits fictifs
        for ($i = 1; $i <= 10; $i++) {
            $delit = new Delit();
            $delit->setType(DelitTypeEnum::cases()[array_rand(DelitTypeEnum::cases())]);
            $delit->setDescription("Description du délit {$i}");
            $delit->setDate(new \DateTime('-' . rand(1, 60) . ' months'));
            $delit->setStatut(DelitStatutEnum::cases()[array_rand(DelitStatutEnum::cases())]);
            $delit->setGravite(DelitGraviteEnum::cases()[array_rand(DelitGraviteEnum::cases())]);
            $delit->setDateDeclaration(new \DateTime('-' . rand(1, 60) . ' months'));
            $delit->setNumeroAffaire('AF' . rand(100000, 999999));
            $delit->setProcureurResponsable('Procureur ' . rand(1, 10));
            $delit->setTemoinsPrincipaux(['Témoin 1', 'Témoin 2']);
            $delit->setPreuvesPrincipales(['Document', 'Témoignage']);
            $delit->setLieu($lieux[array_rand($lieux)]);
            
            $manager->persist($delit);
            $delits[] = $delit;
        }

        return $delits;
    }

    private function createCommentaires(ObjectManager $manager, array $politiciens, array $delits): void
    {
        for ($i = 1; $i <= 20; $i++) {
            $commentaire = new Commentaire();
            $commentaire->setContenu("Contenu du commentaire {$i}");
            $commentaire->setDateCreation(new \DateTime('-' . rand(1, 12) . ' months'));
            $commentaire->setEstModere(false);
            $commentaire->setScoreCredibilite(rand(1, 10));
            $commentaire->setTypeCommentaire('public');
            $commentaire->setDomaineExpertise('Droit');
            $commentaire->setEstPublic(true);
            $commentaire->setNombreLikes(rand(0, 50));
            $commentaire->setNombreDislikes(rand(0, 20));
            $commentaire->setEstSignale(false);
            $commentaire->setAuteur($politiciens[array_rand($politiciens)]);
            $commentaire->setDelit($delits[array_rand($delits)]);
            
            $manager->persist($commentaire);
        }
    }

    private function createDocuments(ObjectManager $manager, array $politiciens, array $delits): void
    {
        for ($i = 1; $i <= 15; $i++) {
            $document = new Document();
            $document->setNom("Document {$i}");
            $document->setChemin("/documents/document_{$i}.pdf");
            $document->setDateCreation(new \DateTime('-' . rand(1, 12) . ' months'));
            $document->setDescription("Description du document {$i}");
            $document->setTailleFichier((string)rand(1024, 10485760));
            $document->setNiveauConfidentialite(DocumentNiveauConfidentialiteEnum::cases()[array_rand(DocumentNiveauConfidentialiteEnum::cases())]);
            $document->setSourceInformation('Service d\'enquête');
            $document->setPersonnesAutorisees(['Personne 1', 'Personne 2']);
            $document->setNombreConsultations(rand(0, 20));
            $document->setDerniereConsultation(new \DateTime('-' . rand(1, 30) . ' days'));
            $document->setEstArchive(false);
            $document->setChecksum('checksum' . rand(100000, 999999));
            $document->setMotsCles(['mot1', 'mot2', 'mot3']);
            $document->setLangueDocument(DocumentLangueDocumentEnum::cases()[array_rand(DocumentLangueDocumentEnum::cases())]);
            $document->setAuteur($politiciens[array_rand($politiciens)]);
            $document->setDelit($delits[array_rand($delits)]);
            
            $manager->persist($document);
        }
    }

    private function createPartenaires(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $partenaire = new Partenaire();
            $partenaire->setNom("Partenaire {$i}");
            $partenaire->setEmail("partenaire{$i}@example.com");
            $partenaire->setTelephone("+33" . rand(100000000, 999999999));
            $partenaire->setAdresse("Adresse partenaire {$i}");
            $partenaire->setSiteWeb("https://partenaire{$i}.fr");
            $partenaire->setNotes("Notes sur le partenaire {$i}");
            $partenaire->setDateCreation(new \DateTime('-' . rand(1, 60) . ' months'));
            $partenaire->setNiveauRisque(PartenaireNiveauRisqueEnum::cases()[array_rand(PartenaireNiveauRisqueEnum::cases())]);
            $partenaire->setVille("Ville {$i}");
            $partenaire->setCodePostal(rand(10000, 99999));
            $partenaire->setPays('France');
            $partenaire->setDatePremiereCollaboration(new \DateTime('-' . rand(1, 60) . ' months'));
            $partenaire->setNombreDelitsImplique(rand(0, 5));
            $partenaire->setEstActif(true);
            $partenaire->setCommentairesInternes("Commentaires internes partenaire {$i}");
            
            $manager->persist($partenaire);
        }
    }
}
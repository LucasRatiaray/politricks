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
use Faker\Factory;

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
        
        // Créer les partenaires (on récupère le tableau)
        $partenaires = $this->createPartenaires($manager);
        
        // Créer les délits (ajout de délits spécifiques liés à des politiciens et partenaires)
        $delits = $this->createDelits($manager, $lieux, $politiciens, $partenaires);
        
        // Lier politiciens et délits (remplir la table de jointure)
        $this->linkPoliticiensToDelits($manager, $politiciens, $delits);
        
        // Créer les commentaires
        $this->createCommentaires($manager, $politiciens, $delits);
        
        // Créer les documents
        $this->createDocuments($manager, $politiciens, $delits);

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

        // Baptiste
        $baptiste = new User();
        $baptiste->setEmail('baptiste@politricks.com');
        $baptiste->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $baptiste->setPassword($passwordHash);
        $baptiste->setFirstName('Baptiste');
        $baptiste->setLastName('Vasseur');
        $baptiste->setDateCreation(new \DateTime());
        $baptiste->setEstActif(true);
        $baptiste->setTelephone('+33123456789');
        $baptiste->setDateNaissance(new \DateTime('1980-01-01'));
        $baptiste->setNationalite('Française');
        $baptiste->setProfession('Administrateur système');
        $manager->persist($baptiste);
        $users[] = $baptiste;

        // Edouard
        $edouard = new User();
        $edouard->setEmail('edouard@politricks.com');
        $edouard->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $edouard->setPassword($passwordHash);
        $edouard->setFirstName('Edouard');
        $edouard->setLastName('Sombie');
        $edouard->setDateCreation(new \DateTime());
        $edouard->setEstActif(true);
        $edouard->setTelephone('+33123456789');
        $edouard->setDateNaissance(new \DateTime('1980-01-01'));
        $edouard->setNationalite('Française');
        $edouard->setProfession('Administrateur système');
        $manager->persist($edouard);
        $users[] = $edouard;

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
        for ($i = 1; $i <= 3; $i++) {
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
            ],
            [
                'nom' => 'La France Insoumise',
                'couleur' => '#FF0000',
                'slogan' => 'Ensemble, nous sommes plus forts',
                'description' => 'Parti politique d\'extrême gauche français',
                'dateCreation' => new \DateTime('2016-02-10'),
                'siteWeb' => 'https://lafranceinsoumise.fr',
                'adresseSiege' => '21 rue de la République, 75002 Paris',
                'telephoneContact' => '01 42 33 00 00',
                'emailContact' => 'contact@lfi.fr',
                'orientationPolitique' => 'Extrême gauche',
                'budgetAnnuel' => 8000000,
                'nombreAdherents' => 120000,
                'partiActif' => true
            ],
            [
                'nom' => 'Reconquête',
                'couleur' => '#FF4500',
                'slogan' => 'Pour la France, pour les Français',
                'description' => 'Parti politique d\'extrême droite français',
                'dateCreation' => new \DateTime('2021-07-05'),
                'siteWeb' => 'https://reconquete.fr',
                'adresseSiege' => '12 rue de la République, 75001 Paris',
                'telephoneContact' => '01 42 00 00 00',
                'emailContact' => 'contact@reconquete.fr',
                'orientationPolitique' => 'Extrême droite',
                'budgetAnnuel' => 10000000,
                'nombreAdherents' => 50000,
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

        // Jean-Marie Le Pen
        $lepen = new Politicien();
        $lepen->setEmail('jean-marie.lepen@gouv.fr');
        $lepen->setRoles(['ROLE_POLITICIAN']);
        $lepen->setPassword($passwordHash);
        $lepen->setFirstName('Jean-Marie');
        $lepen->setLastName('Le Pen');
        $lepen->setDateCreation(new \DateTime());
        $lepen->setEstActif(false);
        $lepen->setTelephone('+33123456789');
        $lepen->setDateNaissance(new \DateTime('1928-06-20'));
        $lepen->setNationalite('Française');
        $lepen->setProfession('Homme politique');
        $lepen->setBiographie("Fondateur du Front National, connu pour ses prises de position controversées.");
        $lepen->setFonction('Président du FN (ancien)');
        $lepen->setDateEntreePolitique(new \DateTime('1956-01-01'));
        $lepen->setMandatActuel("Retraité");
        $lepen->setCirconscription("Provence-Alpes-Côte d'Azur (anciennement)");
        $lepen->setSalaireMensuel('0');
        $lepen->setDeclarationPatrimoine([
            'immobilier' => 1200000,
            'mobilier' => 500000,
            'comptes' => 300000
        ]);
        $lepen->setCasierJudiciaire('Condamnations');
        $lepen->setParti($partis[3]); // Rassemblement National
        $manager->persist($lepen);
        $politiciens[] = $lepen;

        // Anne Hidalgo
        $hidalgo = new Politicien();
        $hidalgo->setEmail('anne.hidalgo@gouv.fr');
        $hidalgo->setRoles(['ROLE_POLITICIAN']);
        $hidalgo->setPassword($passwordHash);
        $hidalgo->setFirstName('Anne');
        $hidalgo->setLastName('Hidalgo');
        $hidalgo->setDateCreation(new \DateTime());
        $hidalgo->setEstActif(true);
        $hidalgo->setTelephone('+33144567890');
        $hidalgo->setDateNaissance(new \DateTime('1959-06-19'));
        $hidalgo->setNationalite('Française');
        $hidalgo->setProfession('Femme politique');
        $hidalgo->setBiographie("Maire de Paris depuis 2014, elle est engagée pour l'écologie urbaine et la justice sociale.");
        $hidalgo->setFonction('Maire de Paris');
        $hidalgo->setDateEntreePolitique(new \DateTime('1997-01-01'));
        $hidalgo->setMandatActuel("Maire de Paris");
        $hidalgo->setCirconscription('Paris');
        $hidalgo->setSalaireMensuel('7000');
        $hidalgo->setDeclarationPatrimoine([
            'immobilier' => 900000,
            'mobilier' => 150000,
            'comptes' => 80000
        ]);
        $hidalgo->setCasierJudiciaire('Vierge');
        $hidalgo->setParti($partis[2]); // Parti Socialiste
        $manager->persist($hidalgo);
        $politiciens[] = $hidalgo;

        // Jordan Bardella
        $bardella = new Politicien();
        $bardella->setEmail('jordan.bardella@gouv.fr');
        $bardella->setRoles(['ROLE_POLITICIAN']);
        $bardella->setPassword($passwordHash);
        $bardella->setFirstName('Jordan');
        $bardella->setLastName('Bardella');
        $bardella->setDateCreation(new \DateTime());
        $bardella->setEstActif(true);
        $bardella->setTelephone('+33777889900');
        $bardella->setDateNaissance(new \DateTime('1995-09-13'));
        $bardella->setNationalite('Française');
        $bardella->setProfession('Homme politique');
        $bardella->setBiographie("Président du Rassemblement National, il incarne la jeune génération de l'extrême droite.");
        $bardella->setFonction('Président du RN');
        $bardella->setDateEntreePolitique(new \DateTime('2012-01-01'));
        $bardella->setMandatActuel("Député européen");
        $bardella->setCirconscription('Île-de-France');
        $bardella->setSalaireMensuel('9000');
        $bardella->setDeclarationPatrimoine([
            'immobilier' => 400000,
            'mobilier' => 100000,
            'comptes' => 60000
        ]);
        $bardella->setCasierJudiciaire('Vierge');
        $bardella->setParti($partis[3]); // Rassemblement National
        $manager->persist($bardella);
        $politiciens[] = $bardella;

        // François Fillon
        $fillon = new Politicien();
        $fillon->setEmail('francois.fillon@gouv.fr');
        $fillon->setRoles(['ROLE_POLITICIAN']);
        $fillon->setPassword($passwordHash);
        $fillon->setFirstName('François');
        $fillon->setLastName('Fillon');
        $fillon->setDateCreation(new \DateTime());
        $fillon->setEstActif(false);
        $fillon->setTelephone('+33612121212');
        $fillon->setDateNaissance(new \DateTime('1954-03-04'));
        $fillon->setNationalite('Française');
        $fillon->setProfession('Homme politique');
        $fillon->setBiographie("Ancien Premier ministre, impliqué dans une affaire d'emplois fictifs avec son épouse.");
        $fillon->setFonction('Premier ministre (ancien)');
        $fillon->setDateEntreePolitique(new \DateTime('1981-01-01'));
        $fillon->setMandatActuel("Aucun");
        $fillon->setCirconscription('Sarthe (anciennement)');
        $fillon->setSalaireMensuel('0');
        $fillon->setDeclarationPatrimoine([
            'immobilier' => 1500000,
            'mobilier' => 200000,
            'comptes' => 100000
        ]);
        $fillon->setCasierJudiciaire('Condamnations');
        $fillon->setParti($partis[1]); // Les Républicains
        $manager->persist($fillon);
        $politiciens[] = $fillon;

        // Nicolas Sarkozy
        $sarkozy = new Politicien();
        $sarkozy->setEmail('nicolas.sarkozy@gouv.fr');
        $sarkozy->setRoles(['ROLE_POLITICIAN']);
        $sarkozy->setPassword($passwordHash);
        $sarkozy->setFirstName('Nicolas');
        $sarkozy->setLastName('Sarkozy');
        $sarkozy->setDateCreation(new \DateTime());
        $sarkozy->setEstActif(false);
        $sarkozy->setTelephone('+33101010101');
        $sarkozy->setDateNaissance(new \DateTime('1955-01-28'));
        $sarkozy->setNationalite('Française');
        $sarkozy->setProfession('Homme politique');
        $sarkozy->setBiographie("Ancien président de la République, impliqué dans plusieurs affaires judiciaires.");
        $sarkozy->setFonction('Président (ancien)');
        $sarkozy->setDateEntreePolitique(new \DateTime('1977-01-01'));
        $sarkozy->setMandatActuel("Aucun");
        $sarkozy->setCirconscription('Hauts-de-Seine (anciennement)');
        $sarkozy->setSalaireMensuel('0');
        $sarkozy->setDeclarationPatrimoine([
            'immobilier' => 2500000,
            'mobilier' => 500000,
            'comptes' => 400000
        ]);
        $sarkozy->setCasierJudiciaire('Condamnations');
        $sarkozy->setParti($partis[1]); // Les Républicains
        $manager->persist($sarkozy);
        $politiciens[] = $sarkozy;

        // Isabelle Balkany
        $isabelle = new Politicien();
        $isabelle->setEmail('isabelle.balkany@lr.fr');
        $isabelle->setRoles(['ROLE_POLITICIAN']);
        $isabelle->setPassword($passwordHash);
        $isabelle->setFirstName('Isabelle');
        $isabelle->setLastName('Balkany');
        $isabelle->setDateCreation(new \DateTime());
        $isabelle->setEstActif(false);
        $isabelle->setTelephone('+33912345678');
        $isabelle->setDateNaissance(new \DateTime('1947-09-20'));
        $isabelle->setNationalite('Française');
        $isabelle->setProfession('Ancienne élue locale');
        $isabelle->setBiographie("Adjointe au maire puis maire par intérim de Levallois-Perret, connue pour des affaires politico-financières. Polémique sur des propos jugés racistes.");
        $isabelle->setFonction('Maire adjointe (ancienne)');
        $isabelle->setDateEntreePolitique(new \DateTime('1983-01-01'));
        $isabelle->setMandatActuel('Aucun');
        $isabelle->setCirconscription('Levallois-Perret');
        $isabelle->setSalaireMensuel('0');
        $isabelle->setDeclarationPatrimoine([
            'immobilier' => 2000000,
            'mobilier' => 500000,
            'comptes' => 100000
        ]);
        $isabelle->setCasierJudiciaire('Condamnée pour fraude fiscale');
        $isabelle->setParti($partis[1]); // Les Républicains
        $manager->persist($isabelle);
        $politiciens[] = $isabelle;

        // Patrick Balkany
        $patrick = new Politicien();
        $patrick->setEmail('patrick.balkany@lr.fr');
        $patrick->setRoles(['ROLE_POLITICIAN']);
        $patrick->setPassword($passwordHash);
        $patrick->setFirstName('Patrick');
        $patrick->setLastName('Balkany');
        $patrick->setDateCreation(new \DateTime());
        $patrick->setEstActif(false);
        $patrick->setTelephone('+33987654321');
        $patrick->setDateNaissance(new \DateTime('1948-08-16'));
        $patrick->setNationalite('Française');
        $patrick->setProfession('Ancien maire');
        $patrick->setBiographie("Maire historique de Levallois-Perret, proche de Nicolas Sarkozy, il a été condamné pour fraude fiscale et blanchiment.");
        $patrick->setFonction('Maire (ancien)');
        $patrick->setDateEntreePolitique(new \DateTime('1983-01-01'));
        $patrick->setMandatActuel('Aucun');
        $patrick->setCirconscription('Hauts-de-Seine');
        $patrick->setSalaireMensuel('0');
        $patrick->setDeclarationPatrimoine([
            'immobilier' => 3000000,
            'mobilier' => 800000,
            'comptes' => 150000
        ]);
        $patrick->setCasierJudiciaire('Condamné pour fraude fiscale');
        $patrick->setParti($partis[1]); // Les Républicains
        $manager->persist($patrick);
        $politiciens[] = $patrick;

        // Jacques Chirac
        $chirac = new Politicien();
        $chirac->setEmail('jacques.chirac@elysee.fr');
        $chirac->setRoles(['ROLE_POLITICIAN']);
        $chirac->setPassword($passwordHash);
        $chirac->setFirstName('Jacques');
        $chirac->setLastName('Chirac');
        $chirac->setDateCreation(new \DateTime());
        $chirac->setEstActif(false);
        $chirac->setTelephone('+33123456789');
        $chirac->setDateNaissance(new \DateTime('1932-11-29'));
        $chirac->setNationalite('Française');
        $chirac->setProfession('Président de la République (ancien)');
        $chirac->setBiographie("Président de la République de 1995 à 2007, maire de Paris pendant 18 ans. Dernier chef de l'État condamné dans une affaire d'emplois fictifs.");
        $chirac->setFonction('Président de la République (ancien)');
        $chirac->setDateEntreePolitique(new \DateTime('1967-01-01'));
        $chirac->setMandatActuel('Décédé en 2019');
        $chirac->setCirconscription('Paris');
        $chirac->setSalaireMensuel('0');
        $chirac->setDeclarationPatrimoine([
            'immobilier' => 1600000,
            'mobilier' => 400000,
            'comptes' => 100000
        ]);
        $chirac->setCasierJudiciaire('Condamné en 2011 pour emplois fictifs');
        $chirac->setParti($partis[1]); // Les Républicains
        $manager->persist($chirac);
        $politiciens[] = $chirac;

        // Bruno Retailleau
        $retailleau = new Politicien();
        $retailleau->setEmail('bruno.retailleau@gouv.fr');
        $retailleau->setRoles(['ROLE_POLITICIAN']);
        $retailleau->setPassword($passwordHash);
        $retailleau->setFirstName('Bruno');
        $retailleau->setLastName('Retailleau');
        $retailleau->setDateCreation(new \DateTime());
        $retailleau->setEstActif(true);
        $retailleau->setTelephone('+33944747000');
        $retailleau->setDateNaissance(new \DateTime('1960-11-20'));
        $retailleau->setNationalite('Française');
        $retailleau->setProfession("Ministre de l'intérieur");
        $retailleau->setBiographie("Ministre de l'intérieur de la République française d'Emmanuel Macron");
        $retailleau->setFonction("Ministre de l'intérieur");
        $retailleau->setDateEntreePolitique(new \DateTime('1988-05-15'));
        $retailleau->setMandatActuel("Ministre de l'intérieur (2022-2027)");
        $retailleau->setCirconscription('National');
        $retailleau->setSalaireMensuel('15000');
        $retailleau->setDeclarationPatrimoine([
            'immobilier' => 1000000,
            'mobilier' => 500000,
            'comptes' => 150000
        ]);
        $retailleau->setCasierJudiciaire('Vierge');
        $retailleau->setParti($partis[1]); // LR
        $manager->persist($retailleau);
        $politiciens[] = $retailleau;

        // Éric Zemmour
        $zemmour = new Politicien();
        $zemmour->setEmail('eric.zemmour@gouv.fr');
        $zemmour->setRoles(['ROLE_POLITICIAN']);
        $zemmour->setPassword($passwordHash);
        $zemmour->setFirstName('Éric');
        $zemmour->setLastName('Zemmour');
        $zemmour->setDateCreation(new \DateTime());
        $zemmour->setEstActif(true);
        $zemmour->setTelephone('+33944747003');
        $zemmour->setDateNaissance(new \DateTime('1958-08-31'));
        $zemmour->setNationalite('Française');
        $zemmour->setProfession('Essayiste et homme politique');
        $zemmour->setBiographie("Ancien journaliste et polémiste, Éric Zemmour est le fondateur du parti Reconquête!. Il défend des positions nationalistes, identitaires et conservatrices, axées sur l'immigration, la sécurité et l'identité française.");
        $zemmour->setFonction('Président de Reconquête!');
        $zemmour->setDateEntreePolitique(new \DateTime('2021-12-05'));
        $zemmour->setMandatActuel("Président du parti Reconquête! (2021 - présent)");
        $zemmour->setCirconscription('National');
        $zemmour->setSalaireMensuel('6000');
        $zemmour->setDeclarationPatrimoine([
            'immobilier' => 1800000,
            'mobilier' => 250000,
            'comptes' => 90000
        ]);
        $zemmour->setCasierJudiciaire('Condamnations pour provocation à la haine raciale');
        $zemmour->setParti($partis[5]); // Reconquête!
        $manager->persist($zemmour);
        $politiciens[] = $zemmour;

        // Jean-Luc Mélenchon
        $melenchon = new Politicien();
        $melenchon->setEmail('jean-luc.melenchon@gouv.fr');
        $melenchon->setRoles(['ROLE_POLITICIAN']);
        $melenchon->setPassword($passwordHash);
        $melenchon->setFirstName('Jean-Luc');
        $melenchon->setLastName('Mélenchon');
        $melenchon->setDateCreation(new \DateTime());
        $melenchon->setEstActif(true);
        $melenchon->setTelephone('+33944747004');
        $melenchon->setDateNaissance(new \DateTime('1951-08-19'));
        $melenchon->setNationalite('Française');
        $melenchon->setProfession('Homme politique');
        $melenchon->setBiographie("Fondateur de La France insoumise, Jean-Luc Mélenchon est une figure majeure de la gauche radicale française. Il défend une 6e République, la planification écologique et une rupture avec l'ordre néolibéral.");
        $melenchon->setFonction('Député (ancien)');
        $melenchon->setDateEntreePolitique(new \DateTime('1976-01-01'));
        $melenchon->setMandatActuel("Leader de La France insoumise");
        $melenchon->setCirconscription('Bouches-du-Rhône (anciennement)');
        $melenchon->setSalaireMensuel('7000');
        $melenchon->setDeclarationPatrimoine([
            'immobilier' => 800000,
            'mobilier' => 300000,
            'comptes' => 100000
        ]);
        $melenchon->setCasierJudiciaire('Vierge');
        $melenchon->setParti($partis[4]); // LFI
        $manager->persist($melenchon);
        $politiciens[] = $melenchon;

        // Emmanuel Macron
        $macron = new Politicien();
        $macron->setEmail('emmanuel.macron@president.fr');
        $macron->setRoles(['ROLE_POLITICIAN']);
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

        // Marlène Schiappa
        $schiappa = new Politicien();
        $schiappa->setEmail('marlene.schiappa@politicien.fr');
        $schiappa->setRoles(['ROLE_POLITICIAN']);
        $schiappa->setPassword($passwordHash);
        $schiappa->setFirstName('Marlène');
        $schiappa->setLastName('Schiappa');
        $schiappa->setDateCreation(new \DateTime());
        $schiappa->setEstActif(true);
        $schiappa->setTelephone('+33123456789');
        $schiappa->setDateNaissance(new \DateTime('1982-11-18'));
        $schiappa->setNationalite('Française');
        $schiappa->setProfession('Ex-secrétaire d\'État');
        $schiappa->setBiographie('Ancienne secrétaire d\'État, impliquée dans plusieurs polémiques dont le fonds Marianne.');
        $schiappa->setFonction('Ancienne secrétaire d\'État à la Citoyenneté');
        $schiappa->setDateEntreePolitique(new \DateTime('2017-05-17'));
        $schiappa->setMandatActuel('Aucun');
        $schiappa->setCirconscription('N/A');
        $schiappa->setSalaireMensuel('7200');
        $schiappa->setDeclarationPatrimoine([
            'immobilier' => 500000,
            'mobilier' => 150000,
            'comptes' => 70000
        ]);
        $schiappa->setCasierJudiciaire('Vierge');
        $schiappa->setParti($partis[0]); // LREM
        $manager->persist($schiappa);
        $politiciens[] = $schiappa;

        // Alexandre Benalla
        $benalla = new Politicien();
        $benalla->setEmail('alexandre.benalla@politicien.fr');
        $benalla->setRoles(['ROLE_POLITICIAN']);
        $benalla->setPassword($passwordHash);
        $benalla->setFirstName('Alexandre');
        $benalla->setLastName('Benalla');
        $benalla->setDateCreation(new \DateTime());
        $benalla->setEstActif(false);
        $benalla->setTelephone('+33666666666');
        $benalla->setDateNaissance(new \DateTime('1991-09-08'));
        $benalla->setNationalite('Française');
        $benalla->setProfession('Ex-chargé de mission');
        $benalla->setBiographie('Chargé de mission à l\'Élysée, connu pour l\'affaire des violences du 1er mai 2018.');
        $benalla->setFonction('Ex-chargé de mission');
        $benalla->setDateEntreePolitique(new \DateTime('2017-05-15'));
        $benalla->setMandatActuel('Aucun');
        $benalla->setCirconscription('N/A');
        $benalla->setSalaireMensuel('5000');
        $benalla->setDeclarationPatrimoine([
            'immobilier' => 0,
            'mobilier' => 30000,
            'comptes' => 10000
        ]);
        $benalla->setCasierJudiciaire('Condamné à 3 ans dont un ferme (affaire Benalla)');
        $benalla->setParti($partis[0]); // LREM
        $manager->persist($benalla);
        $politiciens[] = $benalla;

        // Éric Dupond-Moretti
        $moretti = new Politicien();
        $moretti->setEmail('eric.dupond-moretti@justice.gouv.fr');
        $moretti->setRoles(['ROLE_POLITICIAN']);
        $moretti->setPassword($passwordHash);
        $moretti->setFirstName('Éric');
        $moretti->setLastName('Dupond-Moretti');
        $moretti->setDateCreation(new \DateTime());
        $moretti->setEstActif(true);
        $moretti->setTelephone('+33111223344');
        $moretti->setDateNaissance(new \DateTime('1961-04-20'));
        $moretti->setNationalite('Française');
        $moretti->setProfession('Ministre de la Justice');
        $moretti->setBiographie('Ancien avocat pénaliste, nommé ministre, mis en examen pour prise illégale d\'intérêt.');
        $moretti->setFonction('Ministre de la Justice');
        $moretti->setDateEntreePolitique(new \DateTime('2020-07-06'));
        $moretti->setMandatActuel('Ministre de la Justice');
        $moretti->setCirconscription('National');
        $moretti->setSalaireMensuel('10000');
        $moretti->setDeclarationPatrimoine([
            'immobilier' => 800000,
            'mobilier' => 200000,
            'comptes' => 50000
        ]);
        $moretti->setCasierJudiciaire('Mis en examen en 2021 pour prise illégale d\'intérêt');
        $moretti->setParti($partis[0]); // LREM
        $manager->persist($moretti);
        $politiciens[] = $moretti;

        // Marine Le Pen
        $lepen = new Politicien();
        $lepen->setEmail('marine.lepen@chefpolitique.fr');
        $lepen->setRoles(['ROLE_POLITICIAN']);
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
        for ($i = 1; $i <= 2; $i++) {
            $politicien = new Politicien();
            $politicien->setEmail("politicien{$i}@example.com");
            $politicien->setRoles(['ROLE_POLITICIAN']);
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
        for ($i = 1; $i <= 2; $i++) {
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

    private function createDelits(ObjectManager $manager, array $lieux, array $politiciens = [], array $partenaires = []): array
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

        // Ajout de délits "célèbres" pour les politiciens connus
        if (!empty($politiciens)) {
            // Créer les délits spécifiques pour Jean-Marie Le Pen
            $delits = array_merge($delits, $this->createJMLPDelits($manager, $politiciens[0], $lieux, $partenaires));
            
            // Délit pour Macron
            $delitMacron = new Delit();
            $delitMacron->setType(DelitTypeEnum::Fraude);
            $delitMacron->setDescription('Financement illégal de campagne présidentielle');
            $delitMacron->setDate(new \DateTime('-3 years'));
            $delitMacron->setStatut(DelitStatutEnum::EnInstruction);
            $delitMacron->setGravite(DelitGraviteEnum::Grave);
            $delitMacron->setDateDeclaration(new \DateTime('-3 years'));
            $delitMacron->setNumeroAffaire('AF' . rand(100000, 999999));
            $delitMacron->setProcureurResponsable('Procureur 7');
            $delitMacron->setTemoinsPrincipaux(['Témoin A', 'Témoin B']);
            $delitMacron->setPreuvesPrincipales(['Factures', 'Transferts bancaires']);
            $delitMacron->setLieu($lieux[0]);
            // Ajout d'un partenaire pour tester la jointure
            if (!empty($partenaires)) {
                $delitMacron->addPartenaire($partenaires[0]);
            }
            $manager->persist($delitMacron);
            $delits[] = $delitMacron;

            // Délit pour Benalla
            $delitBenalla = new Delit();
            $delitBenalla->setType(DelitTypeEnum::Agression);
            $delitBenalla->setDescription('Violences lors du 1er mai');
            $delitBenalla->setDate(new \DateTime('-5 years'));
            $delitBenalla->setStatut(DelitStatutEnum::Condamne);
            $delitBenalla->setGravite(DelitGraviteEnum::Grave);
            $delitBenalla->setDateDeclaration(new \DateTime('-5 years'));
            $delitBenalla->setNumeroAffaire('AF' . rand(100000, 999999));
            $delitBenalla->setProcureurResponsable('Procureur 2');
            $delitBenalla->setTemoinsPrincipaux(['Policier', 'Manifestant']);
            $delitBenalla->setPreuvesPrincipales(['Vidéo', 'Rapport police']);
            $delitBenalla->setLieu($lieux[0]);
            // Ajout d'un partenaire pour tester la jointure
            if (!empty($partenaires)) {
                $delitBenalla->addPartenaire($partenaires[1 % count($partenaires)]);
            }
            $manager->persist($delitBenalla);
            $delits[] = $delitBenalla;

            // Délit pour Le Pen
            $delitLePen = new Delit();
            $delitLePen->setType(DelitTypeEnum::Fraude);
            $delitLePen->setDescription('Emplois fictifs au Parlement européen');
            $delitLePen->setDate(new \DateTime('-7 years'));
            $delitLePen->setStatut(DelitStatutEnum::EnInstruction);
            $delitLePen->setGravite(DelitGraviteEnum::Grave);
            $delitLePen->setDateDeclaration(new \DateTime('-7 years'));
            $delitLePen->setNumeroAffaire('AF' . rand(100000, 999999));
            $delitLePen->setProcureurResponsable('Procureur 3');
            $delitLePen->setTemoinsPrincipaux(['Assistant', 'Député']);
            $delitLePen->setPreuvesPrincipales(['Contrats', 'Relevés bancaires']);
            $delitLePen->setLieu($lieux[2]);
            // Ajout de deux partenaires pour tester la jointure
            if (!empty($partenaires)) {
                $delitLePen->addPartenaire($partenaires[2 % count($partenaires)]);
                $delitLePen->addPartenaire($partenaires[3 % count($partenaires)]);
            }
            $manager->persist($delitLePen);
            $delits[] = $delitLePen;

            // Nouveau délit impliquant 2 politiciens et 2 partenaires
            $delitCollusion = new Delit();
            $delitCollusion->setType(DelitTypeEnum::Fraude);
            $delitCollusion->setDescription('Affaire de collusion entre deux politiciens et des partenaires privés');
            $delitCollusion->setDate(new \DateTime('-2 years'));
            $delitCollusion->setStatut(DelitStatutEnum::EnInstruction);
            $delitCollusion->setGravite(DelitGraviteEnum::Grave);
            $delitCollusion->setDateDeclaration(new \DateTime('-2 years'));
            $delitCollusion->setNumeroAffaire('AF' . rand(100000, 999999));
            $delitCollusion->setProcureurResponsable('Procureur 8');
            $delitCollusion->setTemoinsPrincipaux(['Témoin X', 'Témoin Y']);
            $delitCollusion->setPreuvesPrincipales(['Emails', 'Factures']);
            $delitCollusion->setLieu($lieux[1]);
            // Ajout de 2 politiciens
            if (count($politiciens) > 2) {
                $delitCollusion->addPoliticien($politiciens[0]);
                $delitCollusion->addPoliticien($politiciens[1]);
            }
            // Ajout de 2 partenaires
            if (count($partenaires) > 3) {
                $delitCollusion->addPartenaire($partenaires[0]);
                $delitCollusion->addPartenaire($partenaires[1]);
            }
            $manager->persist($delitCollusion);
            $delits[] = $delitCollusion;
        }

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

            if (!empty($politiciens)) {
                $delit->addPoliticien($politiciens[array_rand($politiciens)]);
            }
            
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

            if (!empty($politiciens)) {
                $delit->addPoliticien($politiciens[array_rand($politiciens)]);
            }
            
            $manager->persist($delit);
            $delits[] = $delit;
        }

        return $delits;
    }

    private function createCommentaires(ObjectManager $manager, array $politiciens, array $delits): void
    {
        $faker = Factory::create('fr_FR');

        $phrasesComplices = [
            "Chapeau bas pour l'évasion fiscale, tu m'expliqueras !",
            "J'ai fait pareil avec une société écran à {$faker->country()}, nickel.",
            "Attention, {$faker->firstName()} commence à trop parler...",
            "Pas mal pour un amateur. Moi j'ai planqué le tout dans une ONG.",
            "Haha, tu crois que ça passera inaperçu ? Classique.",
            "Encore mieux : j'ai fait voter une loi pour légaliser le truc.",
            "Faut vraiment qu'on échange nos astuces, c'est brillant.",
            "{$faker->firstName()} m'a soufflé cette combine, faudra remercier.",
            "T'as pensé à effacer les mails ? On ne sait jamais.",
            "Bravo, on sent l'expérience du Sénat là !",
            "J'aurais pas osé... respect !",
            "On dirait presque que t'as une conscience professionnelle.",
            "Tu bluffes, c'était pas possible sans une taupe chez {$faker->company()} !",
            "Ils n'ont encore rien vu... attends la suite.",
            "Tu veux que je t'envoie mon notaire ? Il est discret.",
            "Même la Cour des Comptes n'a rien pigé, bien joué.",
            "J'ai utilisé une fondation bidon au Luxembourg, tip top.",
            "Tu crois qu'ils vont remonter jusqu'à toi ? Haha.",
            "Trop visible. Moi j'aurais utilisé un consultant offshore.",
            "Planquer ça dans un contrat de conseil ? Faut oser.",
            "{$faker->firstName()} t'as couvert, non ?",
            "T'as pensé à changer de SIM après ça ?",
            "Le coup de la fausse facture, c'est du grand art.",
            "Tu pourrais faire un tuto sur la corruption.",
            "Même les journalistes n'ont pas capté. Respect.",
            "On est entre nous ici, balance tes secrets.",
            "T'as bien mérité ton poste chez {$faker->company()} après ça.",
            "J'ai noté l'astuce. Je teste ça sur le prochain appel d'offre.",
            "C'est discret... mais pas trop. Gaffe à {$faker->firstName()} !",
            "T'aurais dû breveter ta technique, sérieux.",
            "Encore un coup de maître signé {$faker->lastName()} !",
            "Un petit rappel de mandat fictif, ça fait toujours plaisir.",
            "T'as bien appris depuis l'affaire de 2012, hein 😏.",
            "J'ai tout vu, j'ai rien dit. Comme d'hab.",
            "Si ça sort, je te couvre. Mais tu me dois un truc.",
        ];

        $domainesExpertise = [
            'Droit pénal', 'Droit civil', 'Droit administratif', 'Droit constitutionnel',
            'Droit fiscal', 'Droit des affaires', 'Droit international', 'Droit européen',
            'Criminologie', 'Sociologie politique', 'Économie', 'Journalisme d\'investigation',
            'Sciences politiques', 'Histoire', 'Philosophie du droit'
        ];

        $typesCommentaire = ['public', 'expert', 'journaliste', 'citoyen', 'analyste'];

        for ($i = 1; $i <= 30; $i++) {
            $commentaire = new Commentaire();
            $commentaire->setContenu($faker->randomElement($phrasesComplices));
            $commentaire->setDateCreation($faker->dateTimeBetween('-1 year', 'now'));
            $commentaire->setEstModere(false);
            $commentaire->setScoreCredibilite($faker->numberBetween(1, 10));
            $commentaire->setTypeCommentaire($faker->randomElement($typesCommentaire));
            $commentaire->setDomaineExpertise($faker->randomElement($domainesExpertise));
            $commentaire->setEstPublic(true);
            $commentaire->setNombreLikes($faker->numberBetween(0, 150));
            $commentaire->setNombreDislikes($faker->numberBetween(0, 50));
            $commentaire->setEstSignale(false);
            $commentaire->setAuteur($faker->randomElement($politiciens));
            $commentaire->setDelit($faker->randomElement($delits));

            $manager->persist($commentaire);
        }
    }

    private function createDocuments(ObjectManager $manager, array $politiciens, array $delits): void
    {
        for ($i = 1; $i <= 3; $i++) {
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

    private function createPartenaires(ObjectManager $manager): array
    {
        $partenaires = [];
        for ($i = 1; $i <= 10; $i++) {
            if ($i % 2 === 0) {
                // Partenaire Physique
                $partenaire = new \App\Entity\PartenairePhysique();
                $partenaire->setPrenom("Prénom{$i}");
                $partenaire->setNomFamille("NomFamille{$i}");
                $partenaire->setDateNaissance(new \DateTime('-' . rand(20, 60) . ' years'));
            } else {
                // Partenaire Moral
                $partenaire = new \App\Entity\PartenaireMoral();
                $partenaire->setRaisonSociale("Entreprise{$i}");
                $partenaire->setSiret(strval(rand(10000000000000, 99999999999999)));
                $partenaire->setSecteurActivite('Secteur ' . $i);
            }
            $partenaire->setNom("Partenaire {$i}");
            $partenaire->setEmail("partenaire{$i}@example.com");
            $partenaire->setTelephone("+33" . rand(100000000, 999999999));
            $partenaire->setAdresse("Adresse partenaire {$i}");
            $partenaire->setSiteWeb("https://partenaire{$i}.fr");
            $partenaire->setNotes("Notes sur le partenaire {$i}");
            $partenaire->setDateCreation(new \DateTime('-' . rand(1, 60) . ' months'));
            $partenaire->setNiveauRisque(\App\Enum\PartenaireNiveauRisqueEnum::cases()[array_rand(\App\Enum\PartenaireNiveauRisqueEnum::cases())]);
            $partenaire->setVille("Ville {$i}");
            $partenaire->setCodePostal(rand(10000, 99999));
            $partenaire->setPays('France');
            $partenaire->setDatePremiereCollaboration(new \DateTime('-' . rand(1, 60) . ' months'));
            $partenaire->setNombreDelitsImplique(rand(0, 5));
            $partenaire->setEstActif(true);
            $partenaire->setCommentairesInternes("Commentaires internes partenaire {$i}");
            $manager->persist($partenaire);
            $partenaires[] = $partenaire;
        }
        return $partenaires;
    }

    // Créer les délits spécifiques pour Jean-Marie Le Pen
    private function createJMLPDelits(ObjectManager $manager, Politicien $jmlp, array $lieux, array $partenaires): array
    {
        $jmlpDelits = [];
        
        // Délits pour Jean-Marie Le Pen
        $jmlpDelitsData = [
            [
                'type' => DelitTypeEnum::Fraude, 
                'desc' => "Emplois fictifs au Parlement européen", 
                'date' => '-7 years', 
                'statut' => DelitStatutEnum::EnInstruction, 
                'gravite' => DelitGraviteEnum::Grave, 
                'lieu' => $lieux[2],
                'temoins' => ['Bruno Gollnisch', 'Marine Le Pen', 'Jean-Claude Martinez'],
                'preuves' => ['Contrats de travail', 'Relevés bancaires', 'Déclarations parlementaires'],
                'procureur' => 'Procureur de Paris'
            ],
            [
                'type' => DelitTypeEnum::CorruptionOfAMinor, 
                'desc' => "Discours haineux et incitation à la haine raciale", 
                'date' => '-10 years', 
                'statut' => DelitStatutEnum::Condamne, 
                'gravite' => DelitGraviteEnum::Grave, 
                'lieu' => $lieux[3] ?? $lieux[0],
                'temoins' => ['Témoins de l\'époque', 'Journalistes', 'Associations anti-racisme'],
                'preuves' => ['Enregistrements audio', 'Articles de presse', 'Dépositions'],
                'procureur' => 'Procureur de Nanterre'
            ],
            [
                'type' => DelitTypeEnum::Escroquerie, 
                'desc' => "Détournement de fonds associatifs", 
                'date' => '-5 years', 
                'statut' => DelitStatutEnum::EnCours, 
                'gravite' => DelitGraviteEnum::Modere, 
                'lieu' => $lieux[1],
                'temoins' => ['Trésorier de l\'association', 'Membres du bureau'],
                'preuves' => ['Comptes bancaires', 'Procès-verbaux', 'Expertise comptable'],
                'procureur' => 'Procureur financier'
            ],
            [
                'type' => DelitTypeEnum::Fraude, 
                'desc' => "Fraude électorale lors d'élections locales", 
                'date' => '-12 years', 
                'statut' => DelitStatutEnum::Condamne, 
                'gravite' => DelitGraviteEnum::Grave, 
                'lieu' => $lieux[0],
                'temoins' => ['Assesseurs', 'Candidats concurrents', 'Électeurs'],
                'preuves' => ['Procès-verbaux de vote', 'Listes électorales', 'Témoignages'],
                'procureur' => 'Procureur de la République'
            ],
            [
                'type' => DelitTypeEnum::Agression, 
                'desc' => "Violences lors d'un meeting politique", 
                'date' => '-6 years', 
                'statut' => DelitStatutEnum::EnInstruction, 
                'gravite' => DelitGraviteEnum::Grave, 
                'lieu' => $lieux[4] ?? $lieux[0],
                'temoins' => ['Manifestants', 'Policiers', 'Spectateurs'],
                'preuves' => ['Vidéos', 'Rapports de police', 'Certificats médicaux'],
                'procureur' => 'Procureur de Paris'
            ],
            [
                'type' => DelitTypeEnum::Escroquerie, 
                'desc' => "Utilisation abusive de fonds publics", 
                'date' => '-8 years', 
                'statut' => DelitStatutEnum::EnCours, 
                'gravite' => DelitGraviteEnum::Grave, 
                'lieu' => $lieux[1],
                'temoins' => ['Fonctionnaires', 'Contrôleurs', 'Experts comptables'],
                'preuves' => ['Comptes publics', 'Audits', 'Rapports d\'inspection'],
                'procureur' => 'Procureur financier'
            ],
            [
                'type' => DelitTypeEnum::Fraude, 
                'desc' => "Déclaration fiscale mensongère", 
                'date' => '-4 years', 
                'statut' => DelitStatutEnum::EnInstruction, 
                'gravite' => DelitGraviteEnum::Modere, 
                'lieu' => $lieux[0],
                'temoins' => ['Comptable', 'Expert-comptable', 'Inspecteur des impôts'],
                'preuves' => ['Déclarations fiscales', 'Relevés bancaires', 'Expertise fiscale'],
                'procureur' => 'Procureur fiscal'
            ],
            [
                'type' => DelitTypeEnum::Harcelement, 
                'desc' => "Harcelement moral dans un parti politique", 
                'date' => '-3 years', 
                'statut' => DelitStatutEnum::EnCours, 
                'gravite' => DelitGraviteEnum::Modere, 
                'lieu' => $lieux[3] ?? $lieux[0],
                'temoins' => ['Membres du parti', 'Anciens collaborateurs', 'Psychologue'],
                'preuves' => ['Emails', 'Témoignages', 'Certificats médicaux'],
                'procureur' => 'Procureur de Nanterre'
            ],
            [
                'type' => DelitTypeEnum::Vol, 
                'desc' => "Vol de matériel lors d'une manifestation", 
                'date' => '-9 years', 
                'statut' => DelitStatutEnum::Condamne, 
                'gravite' => DelitGraviteEnum::Modere, 
                'lieu' => $lieux[4] ?? $lieux[0],
                'temoins' => ['Manifestants', 'Policiers', 'Propriétaires du matériel'],
                'preuves' => ['Vidéos de surveillance', 'Inventaires', 'Témoignages'],
                'procureur' => 'Procureur de Paris'
            ],
            [
                'type' => DelitTypeEnum::Fraude, 
                'desc' => "Financement illégal d'une campagne électorale", 
                'date' => '-11 years', 
                'statut' => DelitStatutEnum::EnInstruction, 
                'gravite' => DelitGraviteEnum::Grave, 
                'lieu' => $lieux[2],
                'temoins' => ['Dirigeants de campagne', 'Comptables', 'Donateurs'],
                'preuves' => ['Comptes de campagne', 'Transferts bancaires', 'Factures'],
                'procureur' => 'Procureur financier'
            ],
        ];

        foreach ($jmlpDelitsData as $data) {
            $delit = new Delit();
            $delit->setType($data['type']);
            $delit->setDescription($data['desc']);
            $delit->setDate(new \DateTime($data['date']));
            $delit->setStatut($data['statut']);
            $delit->setGravite($data['gravite']);
            $delit->setDateDeclaration(new \DateTime($data['date']));
            $delit->setNumeroAffaire('AF' . rand(100000, 999999));
            $delit->setProcureurResponsable($data['procureur']);
            $delit->setTemoinsPrincipaux($data['temoins']);
            $delit->setPreuvesPrincipales($data['preuves']);
            $delit->setLieu($data['lieu']);
            
            // Lier directement à Jean-Marie Le Pen
            $delit->addPoliticien($jmlp);
            
            // Ajouter un partenaire aléatoire si disponible
            if (!empty($partenaires)) {
                $delit->addPartenaire($partenaires[array_rand($partenaires)]);
            }
            
            $manager->persist($delit);
            $jmlpDelits[] = $delit;
        }

        return $jmlpDelits;
    }

    // Ajout : Lier politiciens et délits (ManyToMany)
    private function linkPoliticiensToDelits(ObjectManager $manager, array $politiciens, array $delits): void
    {
        // Exemples de liens pertinents
        // Jean-Marie Le Pen (index 0) → Délits spécifiques créés via createJMLPDelits()
        // Pas besoin de liens supplémentaires car les délits JMLP sont créés avec addPoliticien()
        
        // Emmanuel Macron (index 1) → Délit "Financement illégal de campagne" (dernier délit ajouté)
        if (isset($politiciens[1], $delits[12])) {
            $politiciens[1]->addDelit($delits[12]);
        }
        // Alexandre Benalla (index 3) → Délit "Violences lors du 1er mai" (avant-dernier délit ajouté)
        if (isset($politiciens[3], $delits[11])) {
            $politiciens[3]->addDelit($delits[11]);
        }
        // Marine Le Pen (index 5) → Délit "Emplois fictifs au Parlement européen" (délit 10)
        if (isset($politiciens[5], $delits[10])) {
            $politiciens[5]->addDelit($delits[10]);
        }
        // Politicien fictif 1 (index 6) → Délit fictif 1
        if (isset($politiciens[6], $delits[2])) {
            $politiciens[6]->addDelit($delits[2]);
        }
        // Politicien fictif 2 (index 7) → Délit fictif 2
        if (isset($politiciens[7], $delits[3])) {
            $politiciens[7]->addDelit($delits[3]);
        }
        // Bruno Retailleau (index 0) → Délit fictif 3
        if (isset($politiciens[0], $delits[4])) {
            $politiciens[0]->addDelit($delits[4]);
        }
        // Marlène Schiappa (index 2) → Délit fictif 4
        if (isset($politiciens[2], $delits[5])) {
            $politiciens[2]->addDelit($delits[5]);
        }
        // Éric Dupond-Moretti (index 4) → Délit fictif 5
        if (isset($politiciens[4], $delits[6])) {
            $politiciens[4]->addDelit($delits[6]);
        }
        // On peut aussi faire des liens multiples
        if (isset($politiciens[1], $delits[0])) {
            $politiciens[1]->addDelit($delits[0]);
        }
        if (isset($politiciens[5], $delits[1])) {
            $politiciens[5]->addDelit($delits[1]);
        }
    }
}
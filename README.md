# PoliTricks

Application Symfony dockerisée avec PHP 8.3 et PostgreSQL.

## Description du projet

PoliTricks est une application web développée avec Symfony 7 LTS, qui utilise une base de données PostgreSQL et s'exécute dans un environnement Docker. Ce projet est configuré avec un environnement de développement léger et optimal.

## Prérequis

Avant de commencer, assurez-vous d'avoir installé les outils suivants sur votre machine :

- [Docker](https://www.docker.com/products/docker-desktop/)
- [Docker Compose](https://docs.docker.com/compose/install/) (généralement inclus avec Docker Desktop)
- [Git](https://git-scm.com/downloads)

## Installation

Suivez ces étapes pour installer et exécuter l'application :

1. Clonez le dépôt :
   ```bash
   git clone https://github.com/Nasraoui1/Projet-S1-Symfony.git Politricks
   ````
   
   ```bash
   cd PoliTricks
   ```

2. Démarrez l'environnement Docker :
   ```bash
   docker compose up -d
   ```

3. Installez les dépendances Symfony :
   ```bash
   docker compose exec php composer install
   ```

4. Créez la base de données (si elle n'existe pas déjà) :
   ```bash
   docker compose exec php bin/console doctrine:database:create
   ```

5. Exécutez les migrations :
   ```bash
   docker compose exec php bin/console doctrine:migrations:migrate
   ```

6. Installez les dépendances JavaScript et compilez les assets :
   ```bash
   cd app 
   npm install --save-dev @symfony/webpack-encore
   npx encore dev
   ```
   
7. L'application est maintenant accessible à l'adresse : [http://localhost:8080](http://localhost:8080)

8. Accédez à pgAdmin pour gérer la base de données : [http://localhost:5050](http://localhost:5050)
   - Email : `admin@example.com` (configurable dans .env)
   - Mot de passe : `admin` (configurable dans .env)
   - Pour vous connecter à la base de données, créez un nouveau serveur avec :
     - Nom : PoliTricks
     - Hôte : database
     - Port : 5432
     - Base de données : app
     - Utilisateur : symfony
     - Mot de passe : password

## Détails de l'environnement Docker

L'environnement Docker comprend les services suivants :
- **php** : Service PHP 8.3-FPM basé sur Alpine Linux avec toutes les extensions nécessaires pour Symfony
- **nginx** : Serveur web configuré pour Symfony
- **database** : Base de données PostgreSQL
- **pgadmin** : Interface d'administration pour PostgreSQL

### Volumes configurés

- `./app:/var/www/symfony` : Le code source de l'application Symfony
- `postgres_data:/var/lib/postgresql/data` : Stockage persistant pour la base de données

### Ports exposés

- **8080** : Port du serveur web (Nginx)
- **5432** : Port de PostgreSQL
- **5050** : Port de pgAdmin

## Workflow de développement

Pour travailler sur le projet :

1. Toutes les commandes Symfony doivent être exécutées à l'intérieur du conteneur PHP :
   ```bash
   docker compose exec php bin/console <commande>
   ```

2. Les modifications du code sont automatiquement prises en compte (le volume est monté)

3. Pour installer de nouvelles dépendances :
   ```bash
   docker compose exec php composer require <nom-du-package>
   ```

## Déploiement en production

Le projet inclut une configuration Docker optimisée pour la production. Cette configuration offre une meilleure sécurité, des performances optimisées et une stabilité accrue grâce à des versions spécifiques des services.

### Prérequis pour la production

- Serveur Linux avec Docker et Docker Compose installés
- Accès SSH au serveur
- Certificats SSL pour HTTPS (recommandé)

### Configuration de l'environnement de production

1. Copiez le fichier d'exemple des variables d'environnement de production :
   ```bash
   cp .env.prod.sample .env.prod
   ```

2. Éditez le fichier `.env.prod` avec vos valeurs de production :
   ```bash
   # Modifiez ces valeurs avec des secrets forts
   APP_SECRET=votre_secret_securise
   POSTGRES_DB=app
   POSTGRES_USER=utilisateur_production
   POSTGRES_PASSWORD=mot_de_passe_securise
   ```

3. Pour les certificats SSL, placez-les dans `docker/nginx/ssl/` :
   - `certificate.crt` : votre certificat
   - `private.key` : votre clé privée

### Déploiement en production

1. Construisez les images Docker pour la production :
   ```bash
   docker compose -f docker-compose.prod.yml build
   ```

2. Démarrez les services en mode production :
   ```bash
   docker compose -f docker-compose.prod.yml up -d
   ```

3. Exécutez les migrations de base de données :
   ```bash
   docker compose -f docker-compose.prod.yml exec php bin/console doctrine:migrations:migrate --no-interaction --env=prod
   ```

4. Réchauffez le cache :
   ```bash
   docker compose -f docker-compose.prod.yml exec php bin/console cache:warmup --env=prod
   ```

### Spécificités de la configuration de production

La configuration de production inclut plusieurs optimisations :

1. **Sécurité** :
   - Versions spécifiques et stables de PHP (8.3-fpm-alpine), Nginx (1.24-alpine) et PostgreSQL (16-alpine)
   - Suppression des outils de développement (pgAdmin, etc.)
   - Protection contre l'accès aux fichiers sensibles via Nginx
   - Utilisation d'un utilisateur non-root pour PHP

2. **Performance** :
   - Configuration PHP optimisée avec opcache
   - Cache Nginx pour les assets statiques
   - Réduction de la taille des images Docker

3. **Fiabilité** :
   - Healthchecks pour tous les services
   - Politique de redémarrage automatique (restart: unless-stopped)
   - Volumes persistants sécurisés

### Maintenance en production

Pour mettre à jour l'application en production :

```bash
# Récupérer les dernières modifications
git pull

# Reconstruire si nécessaire
docker compose -f docker-compose.prod.yml build

# Redémarrer les services
docker compose -f docker-compose.prod.yml up -d

# Exécuter les migrations si nécessaire
docker compose -f docker-compose.prod.yml exec php bin/console doctrine:migrations:migrate --no-interaction --env=prod

# Vider le cache
docker compose -f docker-compose.prod.yml exec php bin/console cache:clear --env=prod
```

## Commandes disponibles

Voici quelques commandes utiles pour le développement :

- **Démarrer l'environnement Docker** :
  ```bash
  docker compose up -d
  ```

- **Arrêter l'environnement Docker** :
  ```bash
  docker compose down
  ```

- **Exécuter une commande Symfony** :
  ```bash
  docker compose exec php bin/console <commande>
  ```

- **Créer une entité** :
  ```bash
  docker compose exec php bin/console make:entity
  ```

- **Créer une migration** :
  ```bash
  docker compose exec php bin/console make:migration
  ```

- **Exécuter les migrations** :
  ```bash
  docker compose exec php bin/console doctrine:migrations:migrate
  ```

- **Créer un contrôleur** :
  ```bash
  docker compose exec php bin/console make:controller
  ```

- **Vider le cache** :
  ```bash
  docker compose exec php bin/console cache:clear
  ```

## Structure du projet

```
PoliTricks/
├── app/                 # Application Symfony
├── docker/              # Configurations Docker
│   ├── nginx/           # Configuration Nginx
│   └── php/             # Dockerfile pour PHP
├── .env                 # Variables d'environnement pour Docker
├── .env.prod.sample     # Exemple de variables d'environnement pour la production
├── docker-compose.yml   # Configuration Docker Compose (développement)
├── docker-compose.prod.yml # Configuration Docker Compose (production)
└── README.md            # Ce fichier
```

## Résolution des problèmes courants

Si vous rencontrez des problèmes avec l'application :

1. **Problèmes de permissions** : 
   ```bash
   docker compose exec php chown -R symfony:symfony /var/www/symfony/var
   ```

2. **Problèmes de connexion à la base de données** :
   - Vérifiez que les informations de connexion dans `app/.env` correspondent à celles dans `docker-compose.yml`
   - Vérifiez que le service de base de données est en cours d'exécution : `docker compose ps`

3. **Problèmes avec le cache** :
   ```bash
   docker compose exec php bin/console cache:clear
   ```

# Documentation des Entités

## Entités actuelles et leurs relations :

1. User (ex-Utilisateur)
•  OneToMany avec Delit (1 User -> N Delits)
•  ManyToMany avec DelitComplice (N Users -> N DelitComplices)
•  ManyToMany avec UserTypedelit (N Users -> N UserTypedelits)
2. Delit
•  ManyToOne avec User (N Delits -> 1 User)
•  ManyToOne avec Lieu (N Delits -> 1 Lieu)
•  ManyToMany avec Preuve (N Delits -> N Preuves)
•  ManyToMany avec DelitComplice (N Delits -> N DelitComplices)
•  ManyToMany avec DelitPartenaire (N Delits -> N DelitPartenaires)
3. Lieu
•  OneToMany avec Delit (1 Lieu -> N Delits)
4. Preuve
•  ManyToMany avec Delit (N Preuves -> N Delits)
5. DelitComplice
•  ManyToMany avec User (N DelitComplices -> N Users)
•  ManyToMany avec Delit (N DelitComplices -> N Delits)
6. DelitPartenaire
•  ManyToMany avec Partenaire (N DelitPartenaires -> N Partenaires)
•  ManyToMany avec Delit (N DelitPartenaires -> N Delits)
7. Partenaire
•  ManyToMany avec DelitPartenaire (N Partenaires -> N DelitPartenaires)
8. TypeDelit
•  ManyToMany avec UserTypedelit (N TypeDelits -> N UserTypedelits)
9. UserTypedelit
•  ManyToMany avec User (N UserTypedelits -> N Users)
•  ManyToMany avec TypeDelit (N UserTypedelits -> N TypeDelits)

## Analyse par rapport aux contraintes :

1. Nombre d'entités : 9 entités (il en manque 1 pour atteindre les 10 requises)
2. Relations ManyToMany : 7 relations (déjà plus que les 2 requises)
•  User <-> DelitComplice
•  User <-> UserTypedelit
•  Delit <-> Preuve
•  Delit <-> DelitComplice
•  Delit <-> DelitPartenaire
•  Partenaire <-> DelitPartenaire
•  TypeDelit <-> UserTypedelit
3. Relations OneToMany : 2 relations (il en manque 6 pour atteindre les 8 requises)
•  User -> Delit
•  Lieu -> Delit
4. Héritage d'entité : Actuellement aucun héritage d'entité n'est implémenté

## Modifications à apporter :

1. Ajouter une entité "PoliticienTypeDelit", qui sera une relation ManyToMany entre "User" et "TypeDelit". Cela permettra de lier un politicien à un type de délit spécifique.
2. Ajouter une entité "StatutDelit", qui sera une relation OneToMany entre "Delit" et "StatutDélit". Cela permettra de suivre le statut d'un délit (par exemple, en cours, résolu, etc.).

## Identifiants de connexion Administrateur
- Email : `admin@politricks.com`
- Mot de passe : `password`

## Commandes utiles pour la base de données

### Supprimer le schéma de la base de données (toutes les tables)
   ```bash
   docker exec politricks-php-1 php bin/console doctrine:schema:drop --force --full-database
   ```
### Recréer le schéma (toutes les tables vides)
   ```bash
   docker exec politricks-php-1 php bin/console doctrine:schema:create
   ```
### Charger les fixtures PHP (remplir la base avec les données de AppFixtures.php)
   ```bash
   docker exec politricks-php-1 php bin/console doctrine:fixtures:load --no-interaction
   ```

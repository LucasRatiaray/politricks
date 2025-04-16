# PoliTricks

Application Symfony dockerisée avec PHP 8.3 et PostgreSQL.

## Description du projet

PoliTricks est une application web développée avec Symfony 6.4 LTS, qui utilise une base de données PostgreSQL et s'exécute dans un environnement Docker. Ce projet est configuré avec un environnement de développement léger et optimal.

## Prérequis

Avant de commencer, assurez-vous d'avoir installé les outils suivants sur votre machine :

- [Docker](https://www.docker.com/products/docker-desktop/)
- [Docker Compose](https://docs.docker.com/compose/install/) (généralement inclus avec Docker Desktop)
- [Git](https://git-scm.com/downloads)

## Installation

Suivez ces étapes pour installer et exécuter l'application :

1. Clonez le dépôt :
   ```bash
   git clone <url-du-dépôt>
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

6. L'application est maintenant accessible à l'adresse : [http://localhost:8080](http://localhost:8080)

7. Accédez à pgAdmin pour gérer la base de données : [http://localhost:5050](http://localhost:5050)
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
├── docker-compose.yml   # Configuration Docker Compose
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


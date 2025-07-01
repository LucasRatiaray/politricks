# 🎯 Politricks - Système de Gestion de Délits Politiques

Application Symfony 7 avec architecture Docker pour le développement et déploiement Railway pour la production.

## 🚀 Démarrage Rapide

```bash
# Installation complète (première fois)
make install

# Démarrage dev (après installation)
make start

# Accès aux services
# 🌐 App Symfony: http://localhost:8000
# 🗄️ Adminer (DB): http://localhost:8080 (user: symfony, pass: symfony, db: symfony_db, serveur: db)
# 📊 Base PostgreSQL: localhost:5433
```

## Identifiants de connexion Administrateur
- Email : `admin@politricks.com`
- Mot de passe : `password`

## 🧑‍💻 Développement Local

### Commandes Symfony Simplifiées

**Raccourcis directs (recommandé) :**
```bash
make migrate       # Appliquer les migrations
make migration     # Créer une nouvelle migration
make fixtures      # Charger les fixtures
make clear         # Vider le cache
make entity        # Créer une entité
make controller    # Créer un contrôleur
make routes        # Afficher les routes
```

**Console avancée :**
```bash
# Pour commandes non-couvertes par les raccourcis
make console make:form         # Créer un formulaire
make console make:voter        # Créer un voter
make console debug:container   # Debug container
```

### Commandes Symfony dans Docker

**Format général :**
```bash
docker-compose exec app php bin/console [commande]
```

**Commandes courantes :**
```bash
# 📋 Voir toutes les commandes disponibles
docker-compose exec app php bin/console

# 🏗️ Génération de code
docker-compose exec app php bin/console make:entity          # Créer entité
docker-compose exec app php bin/console make:controller      # Créer contrôleur
docker-compose exec app php bin/console make:form           # Créer formulaire
docker-compose exec app php bin/console make:migration      # Créer migration

# 🗄️ Base de données
docker-compose exec app php bin/console doctrine:migrations:migrate  # Appliquer migrations
docker-compose exec app php bin/console doctrine:fixtures:load       # Charger fixtures
docker-compose exec app php bin/console doctrine:database:create     # Créer BDD

# 🧹 Cache et debug
docker-compose exec app php bin/console cache:clear         # Vider cache
docker-compose exec app php bin/console debug:router        # Debug routes
docker-compose exec app php bin/console debug:container     # Debug services

# 🎨 Assets frontend
docker-compose exec app npm run dev                         # Build dev
docker-compose exec app npm run watch                       # Watch mode
docker-compose exec app npm run build                       # Build production
```


## 📋 Architecture

### 🐳 Docker Multi-Stage
- **Production** (`Dockerfile`): Alpine + PHP 8.3 + Nginx + Supervisor
- **Développement** (`Dockerfile.dev`): Symfony CLI + outils de dev
- **Base de données**: PostgreSQL 15

## 🛠️ Commandes Make

**Gestion du projet :**
```bash
make help           # 📋 Aide complète
make install        # 🚀 Installation complète du projet (première fois)
make start          # ▶️ Démarrer conteneurs + build assets
make stop           # ⏹️ Arrêter conteneurs
make clean          # 🧹 Nettoyer tout (conteneurs + volumes)
```

**Développement Symfony :**
```bash
make migrate        # 🔄 Appliquer les migrations
make migration      # ➕ Créer une nouvelle migration
make fixtures       # 📊 Charger les fixtures
make clear          # 🧹 Vider le cache
make entity         # 🏗️ Créer une entité
make controller     # 🎮 Créer un contrôleur
make routes         # 🗺️ Afficher les routes
```

**Outils :**
```bash
make logs           # 📊 Logs en temps réel
make shell          # 🐚 Accès shell conteneur app
make test           # 🧪 Lancer tests PHPUnit
make composer       # 📦 Installer dépendances Composer
make npm            # 📦 Installer dépendances NPM
make console        # 🎯 Console Symfony - Usage: make console cache:clear
```

### Shell d'accès rapide
```bash
# Accéder au conteneur pour plusieurs commandes
make shell

# Dans le conteneur :
php bin/console make:entity
php bin/console doctrine:migrations:migrate
npm run dev
exit
```

## 🔧 Configuration

### Développement
```bash
cp .env.local.example .env.local
# Modifier DATABASE_URL si nécessaire
```

### Production Railway
Variables d'environnement automatiques :
- `APP_ENV=prod`
- `COMPOSER_ALLOW_SUPERUSER=1`
- `DATABASE_URL=postgresql://...` (Neon/Supabase)
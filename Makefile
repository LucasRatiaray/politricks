# Makefile pour faciliter le développement

.PHONY: help install start stop build logs shell db-reset test

help: ## Affiche cette aide
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

install: ## Installation complète du projet
	@echo "🚀 Installation du projet..."
	docker-compose down -v
	docker-compose build --no-cache
	docker-compose up -d
	@echo "⏳ Attente que la base soit prête..."
	sleep 10
	docker-compose exec app composer install
	docker-compose exec app npm install
	docker-compose exec app npm run build
	docker-compose exec app php bin/console doctrine:database:create --if-not-exists
	docker-compose exec app php bin/console doctrine:migrations:migrate --no-interaction
	@echo "✅ Installation terminée!"
	@echo "🌐 App: http://localhost:8000"
	@echo "🗄️  Adminer: http://localhost:8080"

start: ## Démarre les conteneurs
	docker-compose up -d
	@echo "⏳ Build des assets..."
	docker-compose exec app npm run build
	@echo "✅ Conteneurs démarrés!"

stop: ## Arrête les conteneurs
	docker-compose down
	@echo "✅ Conteneurs arrêtés!"

build: ## Rebuild les conteneurs
	docker-compose build --no-cache

logs: ## Affiche les logs
	docker-compose logs -f

shell: ## Accès shell au conteneur app
	docker-compose exec app bash

db-reset: ## Recrée la base de données
	docker-compose exec app php bin/console doctrine:database:drop --force --if-exists
	docker-compose exec app php bin/console doctrine:database:create
	docker-compose exec app php bin/console doctrine:migrations:migrate --no-interaction

test: ## Lance les tests
	docker-compose exec app php bin/phpunit

composer: ## Installer les dépendances Composer
	docker-compose exec app composer install

npm: ## Installer les dépendances NPM
	docker-compose exec app npm install

deploy-build: ## Build pour Railway
	docker build -t symfony-railway -f Dockerfile .

clean: ## Nettoie tout
	docker-compose down -v
	docker system prune -f

console: ## Console Symfony - Usage: make console d:m:m
	docker-compose exec app php bin/console $(ARGS)

# === Raccourcis Symfony ===
migrate: ## Appliquer les migrations
	docker-compose exec app php bin/console doctrine:migrations:migrate --no-interaction

migration: ## Créer une nouvelle migration
	docker-compose exec app php bin/console make:migration

fixtures: ## Charger les fixtures
	docker-compose exec app php bin/console doctrine:fixtures:load --no-interaction

clear: ## Vider le cache
	docker-compose exec app php bin/console cache:clear

entity: ## Créer une entité
	docker-compose exec app php bin/console make:entity

controller: ## Créer un contrôleur
	docker-compose exec app php bin/console make:controller

routes: ## Afficher les routes
	docker-compose exec app php bin/console debug:router
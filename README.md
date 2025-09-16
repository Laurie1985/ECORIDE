# ECORIDE
Plateforme de covoiturage écologique

# A propos

Ecoride est une plateforme de covoiturage écologique. Cette application a été conçue pour répondre aux besoins de la startup française "Ecoride" qui a pour objectif de réduire limpact environnemental des déplacements en encourageant le covoiturage, particulièrement
avec des voitures électriques.

Ecoride a pour ambition de devenir la principale plateforme de covoiturage pour les voyageurs soucieux de l'environnement et ceux qui recherchent une solution économique pour leurs déplacements en voiture.

# Technologies utilisées

Front-end : 

- HTML5
- CSS3 (Bootstrap)
- Javascript

Back-end : 

- PHP 8.4 (vanilla) avec architecture MVC et design pattern Singleton
- Composer pour la gestion des dépendances
- FastRoute pour le routage
- PHPMailer pour l'envoi d'emails

Base de données : 

- MySQl 8.0 pour les données relationnelles
- MongoDB 6.0 pour les données non relationnelles

Conteneurisation :

- Docker

Gestion des dépendances :

- Composer

Maquettage :

- Figma
- Looping

Eléments de design (logo, picto) :

- Illustrator

Gestion de projet : 

- Trello

# Fonctionnalités principales

Pour les visiteurs :
- Consultation de la page d'accueil
- Recherche d'itinéraires de covoiturage
- Accéder aux détails d'un covoiturage
- Visualisation des trajets disponibles avec filtres
- Création de compte utilisateur

Pour les utilisateurs connectés :
- Profil utilisateur avec chois du rôle (passager/conducteur/les deux)
- Participation à des covoiturages
- Création et gestion de trajets pour les conducteurs
- Historique des covoiturages
- système d'évaluation et d'avis

Fonctionnalités spécifiques :
- système de crédits : monnaie virtuelle pour les transactions
- identification des voyages écologiques par pictogramme
- Filtres de recherche avancés
- Espaces dédiés : espace employés et administrateur

# Installation en local

1. Prérequis :

    - Docker
    - Docker Compose
    - Git

2. Installation locale (avec Docker) :

    - Cloner le projet 

        git clone https://github.com/Laurie1985/ECORIDE.git cd ecoride

    - Installer les dépendances :

        composer install
    
    - Créer un fichier .env : 

        DB_name=ecoride_db
        DB_USER=ecoride_user
        DB_PASSWORD=ecoride_password
        DB_ROOT_PASSWORD=root_password
        MONGO_INITDB_ROOT_USERNAME=mongo_admin
        MONGO_INITDB_ROOT_PASSWORD=mongo_password
        MONGO_DATABASE=ecoride_nosql

    - Construire et démarrer les services : 

        docker-compose up -d --build

    - Vérifier que les services sont démarrés :

        docker-compose ps













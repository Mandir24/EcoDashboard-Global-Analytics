<div align="center">

# SAE - Tableau de Bord Interactif pour l'Analyse de l'Économie Mondiale

## Description

Ce projet vise à concevoir un tableau de bord interactif dédié à l’analyse de l’économie mondiale. Il permettra de visualiser les principaux indicateurs économiques (PIB, croissance démographique, espérance de vie, développement humain, consommation énergétique, etc.) afin de mettre en évidence les grandes tendances et les relations entre ces facteurs. À travers des visualisations claires et dynamiques, cet outil offrira une compréhension approfondie des évolutions économiques globales, facilitant ainsi l’identification des dynamiques de croissance, des disparités régionales et des opportunités de développement.

</div>

<div align="center">

## Fonctionnalités

</div>

- Sélection d’un pays et vue d’ensemble des indicateurs économiques.
- Analyse de l’évolution d’un indicateur clé sur plusieurs décennies.
- Comparaison des pays en fonction de divers classements et corrélations.
- Analyse des indicateurs économiques par région.
- Visualisations interactives et dynamiques des données économiques.

<div align="center">

## Technologies

</div>

- **Langage** : PHP, HTML, CSS, JavaScript
- **Frameworks / Bibliothèques** : Chart.js
- **Base de données** : MySQL
- **Outils de visualisation** : Chart.js
- **Gestion de versions** : Git
- **Serveur local** : XAMPP

<div align="center">

## Installation

</div>

### Prérequis

- **OS** : Windows, macOS, Linux
- **Dépendances** : PHP, MySQL
- **Serveur local** : XAMPP

### Étapes d'installation

> [!TIP]
> Vous pouvez passer par l'application GitHub Desktop si vous n'êtes pas à l'aise avec le terminal.

1. Cloner le projet :
   ```sh
   git clone https://github.com/EkiaND/DED.git
   ```
2. Accéder au répertoire du projet :
   ```sh
   cd DED
   ```
3. Configurer la base de données :
   - Configurez les paramètres de connexion à la base de données dans `config.inc.php` pour utiliser MySQL.
4. Démarrer XAMPP et placer le projet dans le répertoire `htdocs` de XAMPP.
5. Accéder à l'application via `http://localhost/DED`.

> [!CAUTION]
> Si vous ne respectez pas ces étapes, attendez-vous à ce que le projet ne fonctionne pas correctement.

<div align="center">

## Structure du projet

</div>

```
DED 
│-- models/  
│   │-- pays.php                 # Gestion des pays  
│   │-- indicateur.php           # Gestion des indicateurs économiques  
│   │-- indices.php              # Gestion des indices économiques  
│   │-- data.php                 # Requêtes complexes et analyses  
│  
│-- views/  
│   │-- dashboard.php            # Vue principale du tableau de bord  
│   │-- infos_pays.php           # Vue pour les informations détaillées sur un pays  
│   │-- comparaison_pays.php     # Vue pour la comparaison entre pays  
│   │-- partials/                # Fragments de vue réutilisables  
│       │-- header.php           # En-tête de page  
│       │-- footer.php           # Pied de page  
│       │-- selecteurs.php       # Éléments de sélection communs  
│  
│-- controllers/  
│   │-- indicateurs.php          # Contrôleur pour la gestion des indicateurs  
│   │-- regions.php              # Contrôleur pour la gestion des régions  
│  
│-- public/  
│   │-- css/  
│       │-- style.css            # Styles principaux  
│       │-- responsive.css       # Styles pour l'adaptation mobile  
│   │-- js/  
│       │-- graphiques.js        # Fonctions de génération des graphiques  
│       │-- interactions.js      # Gestion des interactions utilisateur  
│       │-- interaction_info.js  # Affichage des informations supplémentaires lors de l'interaction  
│       │-- map.js               # Fonctions liées à la gestion de la carte  
│   │-- img/  
│       │-- github-mark-white.png # Logo de GitHub  
│       │-- image_20.png         # Image de présentation  
│       │-- logo-IUT.png         # Logo de l'IUT  
│   │-- data/  
│       │-- ne_110m_admin_0_countries.dbf # Données en DBF des pays  
│       │-- ne_110m_admin_0_countries.shp # Données en SHP des pays  
│       │-- ne_110m_admin_0_countries.shx # Index des données SHP  
│       │-- regions.geojson       # Données des régions au format GeoJSON  
│       │-- world.geojson        # Données du monde au format GeoJSON  
│  
│-- config.inc.php               # Configuration de l'application (connexion à la base de données)  
│-- index.php                    # Point d'entrée de l'application  
│-- .htaccess                    # Configuration du serveur  
│-- sql/  
│   │-- economie_mondiale.db     # Base de données SQLite  
│  
│-- docs/  
│   │-- howtogit.md              # Guide d'utilisation de Git  
│   │-- MCD.png                  # Diagramme du Modèle Conceptuel de Données  
│   │-- MLD.png                  # Diagramme du Modèle Logique de Données  
│  
│-- README.md                    # Documentation principale  
│-- DEV.md                       # Guide de développement  
│-- IMPORTANT.txt                # Fichier important avec des notes ou instructions  
│-- rapport.tex                  # Rapport LaTeX du projet  
│-- Rapport_SAE.pdf              # Rapport final du projet au format PDF  
```

<div align="center">

## Auteurs et répartition des tâches

</div>

### Auteurs
- **LESUEUR Romain**
- **YON Anthony**
- **DIOP Mandir**
- **SAINT-HUBERT Courteney**

Voici une version corrigée et clarifiée de la répartition des tâches pour votre projet :

### Répartition des Tâches

- **Anthony YON :**
  - **Responsabilités :**
    - Création et maintien du code dans `/models/pays.php`.
    - Création et maintien du code dans `/views/dashboard.php`.
    - Product Owner de la partie `/controllers/indicateurs.php` concernant le dashboard principal.
    - Création et maintien du code concernant les headers/footers.
    - Création d'au moins un graphique sur le dashboard principal.

- **Mandir DIOP :**
  - **Responsabilités :**
    - Création et maintien du code dans `config.inc.php`. 
    - Création et maintien du code dans `/models/indicateur.php`.
    - Création du MLD (Modèle Logique de Données).
    - Création et maintien du code dans `/views/infos_pays.php`.
    - Création et maintien du code dans `/controllers/indicateur.php` concernant `/views/infos_pays.php`.
    - Création d'au moins un graphique sur le dashboard principal.

- **Romain LESUEUR :**
  - **Responsabilités :**
    - Création et maintien du code dans `/models/data.php`.
    - Création et maintien du dossier `/tests/`.
    - Création et maintien du logger.
    - Assistance sur le développement des parties `/views/` et `/controllers/`.
    - Création et maintien du code dans `/controllers/indicateurs.php` concernant le dashboard principal.
    - Création et maintien des fichiers suivants : `DEV.md`, `README.md`, `.htaccess`, `/docs/howtogit.md`.
    - Création d'au moins un graphique sur le dashboard principal.
    - Supervision de tout le projet.

- **Courteney SAINT-HUBERT :**
  - **Responsabilités :**
    - Création et maintien du code dans `/models/indices.php`.
    - Création du MCD (Modèle Conceptuel de Données).
    - Création et maintien du code dans `/views/comparaison_pays.php`.
    - Création et maintien du code dans `/controllers/indicateurs.php` concernant `/views/comparaison_pays.php`.
    - Création d'au moins un graphique sur le dashboard principal.

### Clarifications

- **Product Owner :** Responsable de la vision globale et des priorités pour la partie spécifique du projet.
- **Supervision :** Assurer la cohérence et la qualité globale du projet, coordonner les efforts de l'équipe, sans être responsable de la volonté de travail de chacun.
- **Graphiques :** Chaque membre est responsable de la création d'au moins un graphique pour le dashboard principal, ce qui permet de diversifier les visualisations et d'enrichir l'interface utilisateur.

<div align="center">

## Licence

[![MIT Image](https://upload.wikimedia.org/wikipedia/commons/0/0c/MIT_logo.svg)](https://fr.wikipedia.org/wiki/Licence_MIT)

</div>

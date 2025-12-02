# Guide de développement - EcoDashboard

Ce document détaille les étapes de développement du tableau de bord interactif pour l'analyse de l'économie mondiale. Il présente une todolist structurée et la planification du développement.

## Table des matières
1. [Phases de développement](#phases-de-développement)
2. [Todolist détaillée](#todolist-détaillée)
3. [Structure finale du projet](#structure-finale-du-projet)
4. [Calendrier de développement](#calendrier-de-développement)
5. [Bonnes pratiques](#bonnes-pratiques)

## Phases de développement

Le développement sera divisé en 5 phases principales:

1. **Phase préparatoire**: Configuration de l'environnement, étude du modèle de données
2. **Phase de construction du backend**: Développement des fonctions d'accès aux données
3. **Phase de développement du frontend**: Création des interfaces utilisateur
4. **Phase d'intégration**: Connexion frontend-backend et implémentation des graphiques
5. **Phase de finalisation**: Tests, optimisation et documentation

## Todolist détaillée

### Phase 1: Préparation (Jours 1-2)
- [X] **1.1.** Mettre en place l'environnement de développement XAMPP
- [X] **1.2.** Initialiser le dépôt Git et créer la structure de base du projet
- [X] **1.3.** Étudier le schéma de la base de données SQLite `economie_mondiale.db`
  - [X] Identifier les tables et leurs relations
  - [X] Comprendre la nature des indicateurs disponibles
  - [X] Documenter le modèle de données dans un diagramme
- [X] **1.4.** Créer le fichier de configuration `config.inc.php` pour la connexion à la base de données
- [X] **1.5.** Configurer le `.htaccess` pour les règles de réécriture d'URL

### Phase 2: Backend (Jours 3-7)
- [ ] **2.1.** Développer les modèles de base (`models/`)
  - [X] **2.1.1.** Créer le modèle `pays.php` pour la gestion des pays
    - [X] **`getPays()`** : Récupérer la liste de tous les pays (code et nom) pour alimenter les listes déroulantes ou afficher des informations générales.
    - [X] **`getDetailsPays($codePays)`** : Récupérer les informations détaillées d'un pays spécifique (nom, région, groupe de revenu).
    - [X] **`getPaysParRegion($idRegion)`** : Récupérer la liste des pays appartenant à une région spécifique.
    - [X] **`getPaysParGroupeRevenu($groupeRevenu)`** : Récupérer la liste des pays appartenant à un groupe de revenu spécifique (ex. "High income").
    - [X] **`getRegions()`** : Récupérer la liste des régions disponibles dans la base de données.
    - [X] **`getNombrePaysParRegion($idRegion)`** : Récupérer le nombre de pays dans une région spécifique.

  - [X] **2.1.2.** Créer le modèle `indicateur.php` pour la gestion des indicateurs
    - [X] **`getIndicateurs()`** : Récupérer la liste des indicateurs disponibles (ex. taux de natalité, PIB, espérance de vie).
    - [X] **`getValeursIndicateur($idIndicateur, $codePays)`** : Récupérer les valeurs d'un indicateur pour un pays spécifique sur plusieurs années.
    - [X] **`getMoyenneIndicateurParRegion($idIndicateur, $idRegion)`** : Récupérer la moyenne d'un indicateur pour une région donnée.
    - [X] **`getIndicateursParAnnee($annee)`** : Récupérer les indicateurs disponibles pour une année spécifique.

  - [X] **2.1.3.** Créer le modèle `indices.php` pour la gestion des indices de développement
    - [X] **`getIndicesParPays($codePays)`** : Récupérer les indices de développement pour un pays donné.
    - [X] **`getIndiceGenreParPays($codePays, $annee)`** : Récupérer l'indice de développement par genre pour un pays et une année donnés.
    - [X] **`getEvolutionIndice($codePays, $indice)`** : Obtenir l'évolution d'un indice spécifique (ex. IDH) pour un pays donné.
    - [X] **`getComparaisonIndice($indice, $listePays)`** : Comparer un indice donné pour une liste de pays.
    - [X] **`getTopPaysParIndice($indice, $annee, $ordre = 'DESC')`** : Récupérer le classement des pays selon un indice donné pour une année spécifique.

  - [X] **2.1.4.** Créer le modèle `data.php` pour les requêtes complexes
    - [X] **`getTop10PaysParIndicateur($idIndicateur, $ordre = 'DESC', $annee = null)`** : Récupérer le top 10 des pays selon un indicateur (ordre croissant ou décroissant), avec la possibilité de filtrer par année. La sortie inclut les pays, leurs valeurs maximales pour l'indicateur et les années correspondantes.
    - [X] **`getCorrelationEntreIndicateurs($idIndicateur1, $idIndicateur2, $filtre = null, $typeFiltre = 'pays')`** : Analyser les corrélations entre deux indicateurs, avec la possibilité de filtrer par un pays ou une région. La sortie inclut le coefficient de corrélation, le filtre appliqué et le type de filtre.
    - [X] **`getEvolutionIndicateurParRegion($idIndicateur, $idRegion)`** : Obtenir l'évolution d'un indicateur dans le temps pour une région donnée. La sortie inclut les années et les statistiques suivantes : moyenne, médiane, écart-type, premier quartile (Q1) et troisième quartile (Q3).
    - [X] **`getDistributionIndicateurParRegion($idIndicateur, $annee)`** : Récupérer la distribution d'un indicateur par région pour une année donnée.

- [X] **2.2.** Tester les requêtes SQL et optimiser les performances
  - [X] Vérifier la validité des requêtes SQL pour chaque fonction des modèles.
  - [X] Utiliser des index sur les colonnes fréquemment utilisées dans les clauses `WHERE` et `JOIN`.
  - [X] Optimiser les requêtes complexes (ex. moyennes, corrélations) pour réduire le temps d'exécution.

### Phase 3: Frontend - Structure de base (Jours 8-12)
- [X] **3.1.** Développer la structure HTML de base
  - [X] Créer le template principal dans `index.php`
  - [X] Organiser le tableau de bord en 4 sections selon le cahier des charges
- [X] **3.2.** Développer les vues (`views/`)
  - [X] **3.2.1.** Créer `dashboard.php` pour l'interface générale
  - [X] **3.2.2.** Créer `infos_pays.php` pour les parties A et B
  - [X] **3.2.3.** Créer `comparaison_pays.php` pour les parties C et D
- [ ] **3.3.** Développer les styles CSS (`public/style.css`)
  - [x] Définir une palette de couleurs cohérente
  - [ ] Implémenter un design responsive

### Phase 4: Intégration (Jours 13-20)
- [X] **4.1.** Développer les contrôleurs (`controllers/`)
  - [X] **4.1.1.** Créer le contrôleur principal `indicateurs.php`
    - [X] Fonction pour gérer les sélections d'utilisateur
    - [X] Fonction pour préparer les données des graphiques
- [X] **4.2.** Intégrer Chart.js et développer les visualisations (`public/graphiques.js`)
  - [X] **4.2.1.** Créer les fonctions pour la partie A (Vue d'ensemble pays)
    - [X] Afficher les indicateurs clés sous forme de fiches
  - [X] **4.2.2.** Créer les fonctions pour la partie B (Évolution d'un indicateur)
    - [X] Graphique linéaire d'évolution temporelle
  - [ ] **4.2.3.** Créer les fonctions pour la partie C (Comparaisons)
    - [X] Diagrammes à barres pour les classements
    - [ ] Diagrammes camembert pour les corrélations
  - [ ] **4.2.4.** Créer les fonctions pour la partie D (Analyse par région)
    - [X] Graphiques en aires empilées
    - [ ] Graphique linéaire pour le taux de chômage par région
- [ ] **4.3.** Ajouter l'interactivité aux visualisations
  - [X] Implémentation des filtres et sélections
  - [x] Ajout d'info-bulles au survol
  - [X] Gestion des animations de transition

### Phase 5: Finalisation (Jours 21-25)
- [X] **5.1.** Effectuer des tests complets de l'application
  - [X] Vérifier l'exactitude des calculs et des visualisations
- [ ] **5.2.** Optimiser les performances
  - [X] Optimiser les requêtes SQL
  - [ ] Minimiser le chargement des ressources JavaScript et CSS
- [ ] **5.3.** Ajouter le storytelling aux visualisations
  - [ ] Rédiger des commentaires explicatifs pour chaque visualisation
  - [ ] Mettre en évidence les tendances importantes
- [ ] **5.4.** Finaliser la documentation
  - [X] Compléter le rapport PDF
  - [X] Commenter le code selon les normes PHPDoc
- [X] **5.5.** Préparer la livraison finale
  - [X] Vérifier que toutes les fonctionnalités sont implémentées
  - [X] Créer l'archive compressée au format spécifié

## Structure finale du projet

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
│       │-- responsive.css       # Styles pour l'adaptation mobile  (VIDE)
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
│   │-- howtogit.md              # Guide d'utilisation de Git, Github et Github Desktop (simplifié)
│   │-- MCD.png                  # Diagramme du Modèle Conceptuel de Données  
│   │-- MLD.png                  # Diagramme du Modèle Logique de Données  
│  
│-- README.md                    # Documentation principale  
│-- DEV.md                       # Guide de développement  
│-- IMPORTANT.txt                # Fichier important avec des notes ou instructions  
│-- rapport.tex                  # Rapport LaTeX du projet  
│-- Rapport_SAE.pdf              # Rapport final du projet au format PDF  
   
```

## Calendrier de développement

Basé sur une échéance au 13/04:

| Semaine | Dates      | Objectifs                                           |
|---------|------------|-----------------------------------------------------|
| 1       | 13/03-19/03| Phase 1 (Préparation) & début Phase 2 (Backend)     |
| 2       | 20/03-26/03| Fin Phase 2 (Backend) & début Phase 3 (Frontend)    |
| 3       | 27/03-02/04| Fin Phase 3 (Frontend) & début Phase 4 (Intégration)|
| 4       | 03/04-09/04| Fin Phase 4 (Intégration) & Phase 5 (Finalisation)  |
| 5       | 10/04-13/04| Revue finale, tests et préparation de la livraison  |

## Bonnes pratiques

### Conventions de nommage
- **Fichiers PHP**: Nommez les fichiers en minuscules avec des underscores (ex: `info_pays.php`)
- **Fonctions**: Utilisez le camelCase pour les noms de fonctions (ex: `getPaysParRegion()`)
- **Variables**: Utilisez des noms explicites en camelCase (ex: `$listePays`)
- **Constantes**: Utilisez des majuscules avec underscores (ex: `DB_HOST`)

### Documentation du code
- Commentez chaque fonction avec le format PHPDoc
- Ajoutez des commentaires pour expliquer la logique métier complexe
- Documentez les requêtes SQL particulières

### Gestion de version
- Créez des branches par fonctionnalité
- Faites des commits fréquents avec des messages clairs
- Effectuez des merges réguliers vers la branche principale

### Sécurité
- Utilisez des requêtes préparées pour toutes les requêtes SQL
- Validez et filtrez toutes les entrées utilisateur
- N'exposez pas d'informations sensibles dans les messages d'erreur

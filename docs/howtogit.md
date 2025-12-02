# HowToGit: Comprendre et Utiliser Git et GitHub

## 1. Introduction à Git et GitHub

### Git
Git est un système de contrôle de version distribué qui permet de suivre l'évolution des fichiers, de collaborer efficacement et de gérer plusieurs versions d'un projet. Il fonctionne en local et permet de conserver un historique des modifications apportées.

### GitHub
GitHub est une plateforme en ligne qui héberge des dépôts Git. Elle permet de partager du code, de collaborer avec d'autres développeurs et d'utiliser des outils avancés de gestion de projet, comme les pull requests et les issues.

---

## 2. Installation et Configuration de Git

### Installation
- **Linux** : `sudo apt install git`
- **Mac** : `brew install git`
- **Windows** : Télécharger [Git for Windows](https://git-scm.com/download/win)

### Configuration
Avant d'utiliser Git, configurez votre identité :
```sh
# Configurer votre nom d'utilisateur et votre email
git config --global user.name "Votre Nom"
git config --global user.email "votre.email@example.com"
```

Vérifiez la configuration avec :
```sh
git config --list
```

---

## 3. Premiers Pas avec Git

### Initialisation d'un Dépôt Git
Un dépôt Git est un répertoire dans lequel Git suit les modifications des fichiers.
```sh
git init nom_du_projet
cd nom_du_projet
```

### Clonage d'un Dépôt GitHub
Pour récupérer un dépôt existant depuis GitHub :
```sh
git clone https://github.com/utilisateur/repo.git
```
Cela crée un dossier local avec le contenu du dépôt distant.

---

## 4. Cycle de Vie des Fichiers Git

### Vérifier l'État du Dépôt
La commande suivante permet de voir les fichiers modifiés, ajoutés ou en attente de validation :
```sh
git status
```

### Ajouter des Fichiers à la Staging Area
Avant d’enregistrer des modifications, il faut les ajouter à la zone de staging :
```sh
git add fichier.txt  # Ajouter un fichier spécifique
git add .            # Ajouter tous les fichiers modifiés
```

### Valider (Commit) les Modifications
Un commit enregistre les modifications ajoutées :
```sh
git commit -m "Message explicatif des changements"
```
Chaque commit a un identifiant unique et fait partie de l'historique du projet.

---

## 5. Travailler avec GitHub

### Associer un Dépôt Local à GitHub
Si le dépôt n'est pas encore lié à GitHub, il faut l’associer :
```sh
git remote add origin https://github.com/utilisateur/repo.git
```

### Envoyer les Modifications sur GitHub
Pour publier les modifications locales sur GitHub :
```sh
git push origin main
```

### Récupérer les Dernières Modifications
Si d'autres personnes ont modifié le dépôt sur GitHub, récupérez leurs changements :
```sh
git pull origin main
```
Cela met à jour votre copie locale.

---

## 6. Branches et Fusion (Merging)

### Créer une Nouvelle Branche
Les branches permettent de travailler sur de nouvelles fonctionnalités sans affecter la version principale.
```sh
git branch nouvelle_branche
```

### Changer de Branche
Pour basculer sur une autre branche :
```sh
git checkout nouvelle_branche
```

### Fusionner une Branche dans `main`
Une fois les modifications terminées, fusionnez-les dans `main` :
```sh
git checkout main
git merge nouvelle_branche
```

---

## 7. Gestion des Conflits

Lorsqu’un conflit survient lors d’une fusion, Git indique quels fichiers sont en conflit. Modifiez ces fichiers en choisissant quelles modifications conserver, puis ajoutez-les à la zone de staging :
```sh
git add fichier_conflit.txt
git commit -m "Résolution du conflit"
```

---

## 8. Utilisation de GitHub Desktop
GitHub Desktop est une application graphique qui facilite l’utilisation de Git sans passer par la ligne de commande.

### Installation
Téléchargez et installez [GitHub Desktop](https://desktop.github.com/).

### Cloner un Dépôt
1. Ouvrez GitHub Desktop.
2. Cliquez sur **File > Clone Repository**.
3. Sélectionnez un dépôt GitHub existant et cliquez sur **Clone**.

### Ajouter et Commiter des Modifications
1. Modifiez vos fichiers localement.
2. Ouvrez GitHub Desktop : les fichiers modifiés apparaissent automatiquement.
3. Rédigez un message de commit explicatif.
4. Cliquez sur **Commit to main**.

### Pousser les Modifications sur GitHub
Après avoir commité vos changements :
1. Cliquez sur **Push origin** pour envoyer les modifications sur GitHub.

### Récupérer les Dernières Modifications
1. Cliquez sur **Fetch origin**, puis **Pull** pour synchroniser votre dépôt local avec GitHub.

---

## 9. Bonnes Pratiques
- **Commitez souvent** avec des messages clairs et explicites.
- **Utilisez des branches** pour développer des fonctionnalités indépendantes.
- **Effectuez un `pull` avant un `push`** pour éviter les conflits inutiles.
- **Utilisez un fichier `.gitignore`** pour exclure les fichiers temporaires ou sensibles.

---

## 10. Ressources Utiles
- [Documentation officielle de Git](https://git-scm.com/doc)
- [Guide GitHub](https://docs.github.com/en/get-started)

Git et GitHub sont des outils indispensables pour la gestion de code et la collaboration. Leur maîtrise améliore l’organisation et la productivité dans un projet de développement !
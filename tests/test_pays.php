<?php

/**
 * Ce fichier est un script de test pour les fonctions définies dans le fichier pays.php.
 * Il inclut des tests pour les fonctions suivantes :
 * 
 * - getPays() : Récupère la liste de tous les pays.
 * - getDetailsPays($codePays) : Récupère les détails d'un pays spécifique en fonction de son code.
 * - getPaysParRegion($idRegion) : Récupère la liste des pays appartenant à une région spécifique.
 * - getPaysParGroupeRevenu($groupeRevenu) : Récupère la liste des pays appartenant à un groupe de revenu spécifique.
 * - getRegions() : Récupère la liste des régions disponibles.
 * - getNombrePaysParRegion($idRegion) : Récupère le nombre de pays dans une région spécifique.
 * 
 * Fonctions utilitaires :
 * - afficherResultat($titre, $resultat, $limite = 5) : Affiche les résultats de manière lisible
 *   avec une limite optionnelle sur le nombre de résultats affichés.
 * 
 * Note : Assurez-vous que le fichier pays.php est correctement inclus et que les fonctions
 * testées sont définies et fonctionnelles.
 */

// Inclure le fichier contenant les fonctions
require_once '../models/pays.php';

/**
 * Fonction utilitaire pour afficher les résultats de manière lisible.
 *
 * @param string $titre Titre du test.
 * @param array $resultat Résultat à afficher.
 * @param int $limite Nombre maximum de résultats à afficher.
 */
function afficherResultat($titre, $resultat, $limite = 5) {
    echo "<h2>$titre</h2>";
    if (empty($resultat)) {
        echo "<p>Aucun résultat trouvé.</p>";
    } else {
        echo "<pre>";
        // Limiter l'affichage à $limite résultats
        $affichage = array_slice($resultat, 0, $limite);
        print_r($affichage);
        if (count($resultat) > $limite) {
            echo "\n...et " . (count($resultat) - $limite) . " résultat(s) supplémentaire(s) non affiché(s).";
        }
        echo "</pre>";
    }
}

// Tester la fonction getPays
echo "<h1>Tests pour le modèle pays.php</h1>";
afficherResultat("Test de la fonction getPays", getPays());

// Tester la fonction getDetailsPays
$codePays = "FRA"; // Exemple de code pays
afficherResultat("Test de la fonction getDetailsPays (Pays : $codePays)", getDetailsPays($codePays));

// Tester la fonction getPaysParRegion
$idRegion = "Europe & Central Asia"; // Exemple de région
afficherResultat("Test de la fonction getPaysParRegion (Région : $idRegion)", getPaysParRegion($idRegion));

// Tester la fonction getPaysParGroupeRevenu
$groupeRevenu = "High income: nonOECD"; // Exemple de groupe de revenu
afficherResultat("Test de la fonction getPaysParGroupeRevenu (Groupe de revenu : $groupeRevenu)", getPaysParGroupeRevenu($groupeRevenu));

// Tester la fonction getRegions
afficherResultat("Test de la fonction getRegions", getRegions());

// Tester la fonction getNombrePaysParRegion
$idRegion = "1"; // Exemple d'ID de région
afficherResultat("Test de la fonction getNombrePaysParRegion (Région : $idRegion)", [getNombrePaysParRegion($idRegion)]);

?>
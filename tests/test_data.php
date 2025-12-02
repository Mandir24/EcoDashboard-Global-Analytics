<?php

/**
 * Ce fichier est un script de test pour les fonctions définies dans le fichier data.php.
 * Il inclut des tests pour les fonctions suivantes :
 * 
 * - getTop10PaysParIndicateur($idIndicateur, $ordre) : Récupère le top 10 des pays selon un indicateur.
 * - getCorrelationEntreIndicateurs($idIndicateur1, $idIndicateur2) : Analyse les corrélations entre deux indicateurs.
 * - getEvolutionIndicateurParRegion($idIndicateur, $idRegion) : Obtenir l'évolution d'un indicateur dans le temps pour une région donnée.
 * - getDistributionIndicateurParRegion($idIndicateur, $annee) : Récupérer la distribution d'un indicateur par région pour une année donnée.
 * 
 * Fonctions utilitaires :
 * - afficherResultat($titre, $resultat, $limite = 5) : Affiche les résultats de manière lisible
 *   avec une limite optionnelle sur le nombre de résultats affichés.
 * 
 * Note : Assurez-vous que le fichier data.php est correctement inclus et que les fonctions
 * testées sont définies et fonctionnelles.
 */

// Inclure le fichier contenant les fonctions
require_once '../models/data.php';

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

// Tester la fonction getTop10PaysParIndicateur sans année
$idIndicateur = "pib"; // Exemple d'indicateur
$ordre = "DESC";
afficherResultat("Test de la fonction getTop10PaysParIndicateur (Indicateur : $idIndicateur, Ordre : $ordre, Sans année)", getTop10PaysParIndicateur($idIndicateur, $ordre));

// Tester la fonction getTop10PaysParIndicateur avec une année
$annee = 2014; // Exemple d'année
afficherResultat("Test de la fonction getTop10PaysParIndicateur (Indicateur : $idIndicateur, Ordre : $ordre, Année : $annee)", getTop10PaysParIndicateur($idIndicateur, $ordre, $annee));

// Tester la fonction getCorrelationEntreIndicateurs pour un pays
$idIndicateur1 = "pib";
$idIndicateur2 = "esperance_vie";
$codePays = "FRA"; // Exemple de code pays
afficherResultat(
    "Test de la fonction getCorrelationEntreIndicateurs (Indicateurs : $idIndicateur1, $idIndicateur2, Pays : $codePays)",
    [getCorrelationEntreIndicateurs($idIndicateur1, $idIndicateur2, $codePays, 'pays')]
);

// Tester la fonction getCorrelationEntreIndicateurs pour une région
$idRegion = "1"; // Exemple d'ID de région
afficherResultat(
    "Test de la fonction getCorrelationEntreIndicateurs (Indicateurs : $idIndicateur1, $idIndicateur2, Région : $idRegion)",
    [getCorrelationEntreIndicateurs($idIndicateur1, $idIndicateur2, $idRegion, 'region')]
);

// Tester la fonction getEvolutionIndicateurParRegion
$idIndicateur = "esperance_vie";
$idRegion = "1"; // Exemple d'ID de région
afficherResultat("Test de la fonction getEvolutionIndicateurParRegion (Indicateur : $idIndicateur, Région : $idRegion)", getEvolutionIndicateurParRegion($idIndicateur, $idRegion));

// Tester la fonction getDistributionIndicateurParRegion
$idIndicateur = "pib";
$annee = 2014; // Exemple d'année
afficherResultat("Test de la fonction getDistributionIndicateurParRegion (Indicateur : $idIndicateur, Année : $annee)", getDistributionIndicateurParRegion($idIndicateur, $annee));

?>
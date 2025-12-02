<?php

/**
 * Ce fichier est un script de test pour les fonctions définies dans le fichier indicateur.php.
 * Il inclut des tests pour les fonctions suivantes :
 * 
 * - getIndicateurs() : Récupère la liste des indicateurs disponibles.
 * - getValeursIndicateur($idIndicateur, $codePays) : Récupère les valeurs d'un indicateur pour un pays spécifique sur plusieurs années.
 * - getMoyenneIndicateurParRegion($idIndicateur, $idRegion) : Récupère la moyenne d'un indicateur pour une région donnée.
 * - getIndicateursParAnnee($annee) : Récupère les indicateurs disponibles pour une année spécifique.
 * 
 * Fonctions utilitaires :
 * - afficherResultat($titre, $resultat, $limite = 5) : Affiche les résultats de manière lisible
 *   avec une limite optionnelle sur le nombre de résultats affichés.
 * 
 * Tests effectués :
 * - Test de la fonction getIndicateurs pour vérifier la récupération des indicateurs disponibles.
 * - Test de la fonction getValeursIndicateur avec un indicateur et un pays spécifiques.
 * - Test de la fonction getMoyenneIndicateurParRegion avec un indicateur et une région spécifiques.
 * - Test de la fonction getIndicateursParAnnee avec une année spécifique.
 * 
 * Note : Assurez-vous que le fichier indicateur.php est correctement inclus et que les fonctions
 * testées sont définies et fonctionnelles.
 */

// Inclure le fichier contenant les fonctions
require_once '../models/indicateur.php';

// Fonction utilitaire pour afficher les résultats de manière lisible
function afficherResultat($titre, $resultat, $limite = 5) {
    echo "<h2>$titre</h2>";
    if (is_array($resultat)) {
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
    } else {
        // Gérer les résultats non-array (e.g., float, null, etc.)
        echo "<pre>";
        echo "Résultat : " . (is_null($resultat) ? "null" : $resultat) . "\n";
        echo "</pre>";
    }
}

// Tester la fonction getIndicateurs
afficherResultat("Test de la fonction getIndicateurs", getIndicateurs());

// Tester la fonction getValeursIndicateur
$idIndicateur = "pib";
$codePays = "FRA";
afficherResultat("Test de la fonction getValeursIndicateur (Indicateur : $idIndicateur, Pays : $codePays)", getValeursIndicateur($idIndicateur, $codePays));

// Tester la fonction getMoyenneIndicateurParRegion
$idIndicateur = "esperance_vie";
$idRegion = "1"; // Exemple d'ID de région
afficherResultat(
    "Test de la fonction getMoyenneIndicateurParRegion (Indicateur : $idIndicateur, Région : $idRegion)",
    getMoyenneIndicateurParRegion($idIndicateur, $idRegion)
);

// Tester la fonction getIndicateursParAnnee
$annee = 1990;
afficherResultat("Test de la fonction getIndicateursParAnnee (Année : $annee)", getIndicateursParAnnee($annee));

?>
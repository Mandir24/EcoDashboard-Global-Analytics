<?php

/**
 * Ce fichier est un script de test pour les fonctions définies dans le fichier indices.php.
 * Il inclut des tests pour les fonctions suivantes :
 * 
 * - getIndicesParPays($codePays) : Récupère les indices de développement pour un pays donné.
 * - getIndiceGenreParPays($codePays, $annee) : Récupère l'indice de développement par genre pour un pays et une année donnés.
 * - getEvolutionIndice($codePays, $indice) : Obtenir l'évolution d'un indice spécifique pour un pays donné.
 * - getComparaisonIndice($indice, $listePays) : Comparer un indice donné pour une liste de pays.
 * - getTopPaysParIndice($indice, $annee, $ordre) : Récupérer le classement des pays selon un indice donné pour une année spécifique.
 * 
 * Fonctions utilitaires :
 * - afficherResultat($titre, $resultat, $limite = 5) : Affiche les résultats de manière lisible
 *   avec une limite optionnelle sur le nombre de résultats affichés.
 * 
 * Note : Assurez-vous que le fichier indices.php est correctement inclus et que les fonctions
 * testées sont définies et fonctionnelles.
 */

// Inclure le fichier contenant les fonctions
require_once '../models/indices.php';

/**
 * Fonction utilitaire pour afficher les résultats de manière lisible.
 *
 * @param string $titre Titre du test.
 * @param array|mixed $resultat Résultat à afficher.
 * @param int $limite Nombre maximum de résultats à afficher.
 */
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

// Tester la fonction getIndicesParPays
$codePays = "FRA"; // Exemple de code pays
afficherResultat("Test de la fonction getIndicesParPays (Pays : $codePays)", getIndicesParPays($codePays));

// Tester la fonction getIndiceGenreParPays
$annee = 1999; // Exemple d'année
afficherResultat("Test de la fonction getIndiceGenreParPays (Pays : $codePays, Année : $annee)", getIndiceGenreParPays($codePays, $annee));

// Tester la fonction getEvolutionIndice
$indice = "idh"; // Exemple d'indice
afficherResultat("Test de la fonction getEvolutionIndice (Pays : $codePays, Indice : $indice)", getEvolutionIndice($codePays, $indice));

// Tester la fonction getComparaisonIndice
$listePays = ["FRA", "USA", "DEU"]; // Exemple de liste de pays
afficherResultat("Test de la fonction getComparaisonIndice (Indice : $indice, Pays : " . implode(", ", $listePays) . ")", getComparaisonIndice($indice, $listePays));

// Tester la fonction getTopPaysParIndice
$ordre = "DESC"; // Ordre de tri
afficherResultat("Test de la fonction getTopPaysParIndice (Indice : $indice, Année : $annee, Ordre : $ordre)", getTopPaysParIndice($indice, $annee, $ordre));

?>

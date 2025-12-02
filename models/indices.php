<?php
require_once __DIR__ . '/../config.inc.php';

/**
 * Récupérer les indices de développement pour un pays et une année donnés.
 *
 * @param string $codePays Code du pays.
 * @param int $annee Année.
 * @return array|null Indices de développement sous forme de tableau associatif.
 */
function getIndicesParPaysEtAnnee($codePays, $annee) {
    try {
        $conn = getBDD();
        $query = "SELECT genre, idh, esperance_vie, annees_scolarisation_attendues, annees_scolarisation_moyenne, 
                         revenu_national_brut, indice_developpement_genre, idh_inegalite, coefficient_inegalite, 
                         perte_humaine, inegalite_esperance_vie, inegalite_education, inegalite_revenu, 
                         indice_inegalite_genre, taux_mortalite_maternelle, taux_naissance_adolescents, 
                         education_secondaire, representation_parlementaire, taux_participation, empreinte_materielle, 
                         emissions_co2
                  FROM indices_dvpt
                  WHERE code_pays = ? AND annee = ?;";
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt === false) {
            throw new Exception(mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "si", $codePays, $annee);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception(mysqli_error($conn));
        }

        $res = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($res) ?: null;
    } catch (Exception $e) {
        logError($e->getMessage(), __FILE__, __LINE__);
        return null;
    }
}

/**
 * Récupérer les indices de développement pour une région donnée et une année.
 *
 * @param string $idRegion ID de la région.
 * @param int $annee Année.
 * @return array Liste des indices de développement pour les pays de la région.
 */
function getIndicesParRegionEtAnnee($idRegion, $annee) {
    try {
        $conn = getBDD();
        $query = "SELECT p.nom_pays, i.genre, i.idh, i.esperance_vie, i.annees_scolarisation_attendues, 
                         i.annees_scolarisation_moyenne, i.revenu_national_brut, i.indice_developpement_genre, 
                         i.idh_inegalite, i.coefficient_inegalite, i.perte_humaine, i.inegalite_esperance_vie, 
                         i.inegalite_education, i.inegalite_revenu, i.indice_inegalite_genre, 
                         i.taux_mortalite_maternelle, i.taux_naissance_adolescents, i.education_secondaire, 
                         i.representation_parlementaire, i.taux_participation, i.empreinte_materielle, i.emissions_co2
                  FROM indices_dvpt AS i
                  JOIN pays AS p ON i.code_pays = p.code_pays
                  JOIN regions AS r ON p.id_region = r.id_region
                  WHERE r.id_region = ? AND i.annee = ?;";
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt === false) {
            throw new Exception(mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "si", $idRegion, $annee);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception(mysqli_error($conn));
        }

        $res = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_all($res, MYSQLI_ASSOC) ?: [];
    } catch (Exception $e) {
        logError($e->getMessage(), __FILE__, __LINE__);
        return [];
    }
}

/**
 * Récupérer les indices de développement pour une région donnée et une année.
 *
 * @param string $codePays code du Pays.
 * @return array IDH le plus récent disponible pour le pays.
 */
function getDernierIdhParPays($codePays) {
    $conn = getBDD();
    
    $stmt = mysqli_prepare($conn, "
        SELECT annee, idh 
        FROM indices_dvpt 
        WHERE code_pays = ? AND idh IS NOT NULL  AND genre = 'total'
        ORDER BY annee DESC 
        LIMIT 1
    ");
    
    mysqli_stmt_bind_param($stmt, "s", $codePays);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    
    return mysqli_fetch_assoc($res) ?: null;
}

/**
 * Récupérer les indices de développement pour un pays donné.
 *
 * @param string $codePays Code du pays.
 * @return array|null Indices de développement sous forme de tableau associatif pour le genre 'total'.
 */
function getIndicesParPays($codePays) {
    try {
        $conn = getBDD();
        $query = "SELECT * FROM indices_dvpt WHERE code_pays = ? AND genre = 'total';";
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt === false) {
            throw new Exception(mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "s", $codePays);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception(mysqli_error($conn));
        }

        $res = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_all($res, MYSQLI_ASSOC) ?: null;
    } catch (Exception $e) {
        logError($e->getMessage(), __FILE__, __LINE__);
        return null;
    }
}

/**
 * Récupérer les indices de développement par genre pour un pays et une année donnés.
 *
 * @param string $codePays Code du pays.
 * @param int $annee Année.
 * @return array|null Tableau contenant toutes les colonnes pour les genres 'homme' et 'femme' ou null en cas d'erreur.
 */
function getIndiceGenreParPays($codePays, $annee) {
    try {
        $conn = getBDD();
        $query = "SELECT * FROM indices_dvpt 
                  WHERE code_pays = ? AND annee = ? AND genre IN ('homme', 'femme');";
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt === false) {
            throw new Exception(mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "si", $codePays, $annee);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception(mysqli_error($conn));
        }

        $res = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_all($res, MYSQLI_ASSOC) ?: null;
    } catch (Exception $e) {
        logError($e->getMessage(), __FILE__, __LINE__);
        return null;
    }
}

/**
 * Obtenir l'évolution d'un indice spécifique pour un pays donné.
 *
 * @param string $codePays Code du pays.
 * @param string $indice Nom de l'indice (ex. 'idh').
 * @return array Liste des valeurs de l'indice par année.
 */
function getEvolutionIndice($codePays, $indice) {
    try {
        $conn = getBDD();
        $query = "SELECT annee, $indice AS valeur FROM indices_dvpt WHERE code_pays = ? ORDER BY annee ASC;";
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt === false) {
            throw new Exception(mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "s", $codePays);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception(mysqli_error($conn));
        }

        $res = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_all($res, MYSQLI_ASSOC) ?: [];
    } catch (Exception $e) {
        logError($e->getMessage(), __FILE__, __LINE__);
        return [];
    }
}

/**
 * Comparer un indice donné pour une liste de pays.
 *
 * @param string $indice Nom de l'indice (ex. 'idh').
 * @param array $listePays Liste des codes des pays.
 * @return array Liste des valeurs de l'indice pour chaque pays.
 */
function getComparaisonIndice($indice, $listePays) {
    try {
        $conn = getBDD();
        $placeholders = implode(',', array_fill(0, count($listePays), '?'));
        $query = "SELECT code_pays, $indice AS valeur FROM indices_dvpt WHERE code_pays IN ($placeholders);";
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt === false) {
            throw new Exception(mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, str_repeat('s', count($listePays)), ...$listePays);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception(mysqli_error($conn));
        }

        $res = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_all($res, MYSQLI_ASSOC) ?: [];
    } catch (Exception $e) {
        logError($e->getMessage(), __FILE__, __LINE__);
        return [];
    }
}

/**
 * Récupérer le classement des pays selon un indice donné pour une année spécifique.
 *
 * @param string $indice Nom de l'indice (ex. 'idh').
 * @param int $annee Année.
 * @param string $ordre Ordre de tri ('ASC' ou 'DESC'). Par défaut 'DESC'.
 * @return array Liste des pays classés par l'indice.
 */
function getTopPaysParIndice($indice, $annee, $ordre = 'DESC') {
    try {
        $conn = getBDD();
        $query = "SELECT code_pays, $indice AS valeur FROM indices_dvpt 
                  WHERE annee = ? AND $indice IS NOT NULL 
                  ORDER BY valeur $ordre;";
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt === false) {
            throw new Exception(mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "i", $annee);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception(mysqli_error($conn));
        }

        $res = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_all($res, MYSQLI_ASSOC) ?: [];
    } catch (Exception $e) {
        logError($e->getMessage(), __FILE__, __LINE__);
        return [];
    }
}
?>

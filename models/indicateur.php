<?php
// Fichier pour gérer les entités indicateurs

require_once __DIR__ . '/../config.inc.php'; // Inclure le fichier de configuration
// require_once __DIR__ . '/../logger/logger.php'; // Inclure le logger si besoin

/**
 * Récupérer la liste des indicateurs disponibles.
 *
 * Cette fonction utilise la table système `INFORMATION_SCHEMA.COLUMNS` pour récupérer dynamiquement
 * les colonnes de la table `indicateurs` qui représentent des indicateurs, tout en excluant les colonnes
 * techniques comme `id`, `code_pays`, et `annee`.
 *
 * @return array Liste des indicateurs sous forme de tableaux associatifs (id, nom de l'indicateur).
 *               Retourne un tableau vide en cas d'erreur.
 */
function getIndicateurs() {
    try {
        $conn = getBDD();

        $req = "SELECT COLUMN_NAME AS nom_indicateur 
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_NAME = 'indicateurs' 
                AND COLUMN_NAME NOT IN ('id', 'code_pays', 'annee');";

        $stmt = mysqli_prepare($conn, $req);
        if ($stmt === false) {
            throw new Exception(mysqli_error($conn));
        }

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

if (isset($_GET['action']) && $_GET['action'] === 'getIndicateurs') {
    echo json_encode(getIndicateurs());
    exit;
}

/**
 * Récupérer les valeurs d'un indicateur pour un pays spécifique sur plusieurs années.
 *
 * @param string $idIndicateur Nom de l'indicateur (ex. 'pib', 'esperance_vie').
 * @param string $codePays Code du pays (ex. 'FRA').
 * @return array Liste des valeurs de l'indicateur pour le pays sur plusieurs années.
 *               Retourne un tableau vide en cas d'erreur.
 */
function getValeursIndicateur($idIndicateur, $codePays) {
    try {
        $conn = getBDD();

        $query = "SELECT annee, $idIndicateur AS valeur 
                  FROM indicateurs 
                  WHERE code_pays = ? AND $idIndicateur IS NOT NULL";

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
 * Récupérer la moyenne d'un indicateur pour une région donnée.
 *
 * Cette fonction calcule dynamiquement la moyenne d'un indicateur (ex: PIB, espérance de vie)
 * pour tous les pays appartenant à une même région.
 *
 * @param string $idIndicateur Nom de l'indicateur (ex. 'pib', 'esperance_vie').
 * @param string $idRegion ID de la région (clé étrangère depuis la table 'pays').
 * @return array Retourne un tableau associatif contenant une seule entrée avec la clé 'moyenne'.
 *               Retourne un tableau vide en cas d'erreur ou si aucune donnée n'est trouvée.
 */
function getMoyenneIndicateurParRegion($idIndicateur, $idRegion) {
    try {
        $conn = getBDD();

        $query = "SELECT AVG(i.$idIndicateur) AS moyenne 
                  FROM indicateurs i
                  INNER JOIN pays p ON i.code_pays = p.code_pays
                  WHERE p.id_region = ? AND i.$idIndicateur IS NOT NULL";

        $stmt = mysqli_prepare($conn, $query);
        if ($stmt === false) {
            throw new Exception(mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "s", $idRegion);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception(mysqli_error($conn));
        }

        $res = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($res);

        return $row ? [['moyenne' => $row['moyenne']]] : [];
    } catch (Exception $e) {
        logError($e->getMessage(), __FILE__, __LINE__);
        return [];
    }
}

/**
 * Récupérer les valeurs de tous les indicateurs pour chaque pays à une année donnée.
 *
 * Cette fonction sélectionne dynamiquement toutes les colonnes de la table `indicateurs` 
 * (sauf les colonnes techniques) et renvoie les valeurs pour une année spécifique.
 *
 * @param int $annee Année pour laquelle on souhaite récupérer les données (ex. 2020).
 * @return array Liste de tableaux associatifs contenant les valeurs des indicateurs
 *               pour chaque pays à cette année. Retourne un tableau vide en cas d'erreur.
 */
function getIndicateursParAnnee($annee) {
    try {
        // Connexion à la base de données
        $conn = getBDD();

        // Récupération des colonnes (indicateurs) dynamiques
        $colonnes = getIndicateurs();
        if (empty($colonnes)) {
            throw new Exception("Aucun indicateur trouvé dans la table.");
        }

        // Extraction des noms d'indicateurs à partir du tableau
        $nomsColonnes = array_map(fn($col) => $col['nom_indicateur'], $colonnes);
        $select = implode(', ', $nomsColonnes);

        // Construction de la requête SQL
        $query = "SELECT code_pays, $select 
                  FROM indicateurs 
                  WHERE annee = ?";

        // Préparation de la requête
        $stmt = mysqli_prepare($conn, $query);
        if ($stmt === false) {
            throw new Exception(mysqli_error($conn));
        }

        // Liaison du paramètre d'année
        mysqli_stmt_bind_param($stmt, "i", $annee);

        // Exécution de la requête
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception(mysqli_error($conn));
        }

        // Récupération des résultats
        $res = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_all($res, MYSQLI_ASSOC) ?: [];

        // Remplacement des valeurs vides (null ou chaîne vide) par "NA"
        foreach ($data as &$row) {
            foreach ($row as $key => $value) {
                if ($value === null || $value === '') {
                    $row[$key] = "NA";
                }
            }
        }

        return $data;
    } catch (Exception $e) {
        // En cas d'erreur, on log le message et on retourne un tableau vide
        logError($e->getMessage(), __FILE__, __LINE__);
        return [];
    }
}

/**
 * Récupérer les valeurs de tous les indicateurs pour un pays spécifique à une année donnée.
 *
 * Cette fonction sélectionne dynamiquement toutes les colonnes de la table `indicateurs` 
 * (sauf les colonnes techniques) et renvoie les valeurs pour un pays et une année spécifiques.
 *
 * @param int $annee Année pour laquelle on souhaite récupérer les données (ex. 2020).
 * @param string $codePays Code du pays pour lequel on souhaite récupérer les données (ex. 'FRA').
 * @return array Liste de tableaux associatifs contenant les valeurs des indicateurs
 *               pour le pays spécifié à cette année. Retourne un tableau vide en cas d'erreur.
 */
function getIndicateursParAnneePays($annee, $codePays) {
    try {
        // Connexion à la base de données
        $conn = getBDD();
        
        // Récupération des colonnes (indicateurs) dynamiques
        $colonnes = getIndicateurs();
        if (empty($colonnes)) {
            throw new Exception("Aucun indicateur trouvé dans la table.");
        }
        
        // Extraction des noms d'indicateurs à partir du tableau
        $nomsColonnes = array_map(fn($col) => $col['nom_indicateur'], $colonnes);
        $select = implode(', ', $nomsColonnes);
        
        // Construction de la requête SQL avec filtre sur code_pays
        $query = "SELECT code_pays, $select 
                 FROM indicateurs 
                 WHERE annee = ? AND code_pays = ?";
        
        // Préparation de la requête
        $stmt = mysqli_prepare($conn, $query);
        if ($stmt === false) {
            throw new Exception(mysqli_error($conn));
        }
        
        // Liaison des paramètres (année et code pays)
        mysqli_stmt_bind_param($stmt, "is", $annee, $codePays);
        
        // Exécution de la requête
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception(mysqli_error($conn));
        }
        
        // Récupération des résultats
        $res = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_all($res, MYSQLI_ASSOC) ?: [];
        
        // Remplacement des valeurs vides (null ou chaîne vide) par "NA"
        foreach ($data as &$row) {
            foreach ($row as $key => $value) {
                if ($value === null || $value === '') {
                    $row[$key] = "NA";
                }
            }
        }
        
        return $data;
    } catch (Exception $e) {
        // En cas d'erreur, on log le message et on retourne un tableau vide
        logError($e->getMessage(), __FILE__, __LINE__);
        return [];
    }
}
?>
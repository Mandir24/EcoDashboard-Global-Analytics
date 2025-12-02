<?php
// Fichier pour les requêtes complexes et analyses

//require_once __DIR__ . '/../logger/logger.php'; // Inclure le logger
require_once __DIR__ . '/../config.inc.php'; // Inclure le fichier de configuration

/**
 * Récupérer le top 10 des pays selon un indicateur.
 *
 * @param string $idIndicateur Nom de l'indicateur (ex. 'pib', 'esperance_vie').
 * @param string $ordre Ordre de tri ('ASC' ou 'DESC'). Par défaut 'DESC'.
 * @return array Liste des 10 pays avec leurs valeurs pour l'indicateur.
 */
function getTop10PaysParIndicateur($idIndicateur, $ordre = 'DESC', $annee = null) {
    try {
        $conn = getBDD();
        $query = "SELECT p.nom_pays, MAX(i.$idIndicateur) AS valeur, i.annee 
                  FROM indicateurs AS i
                  JOIN pays AS p ON i.code_pays = p.code_pays
                  WHERE i.$idIndicateur IS NOT NULL";
        
        // Ajouter une condition pour l'année si elle est spécifiée
        if ($annee !== null) {
            $query .= " AND i.annee = ?";
        }

        $query .= " GROUP BY p.nom_pays, i.annee
                    ORDER BY valeur $ordre
                    LIMIT 10;";
        
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt === false) {
            throw new Exception(mysqli_error($conn));
        }

        // Lier les paramètres si l'année est spécifiée
        if ($annee !== null) {
            mysqli_stmt_bind_param($stmt, "i", $annee);
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

/**
 * Analyser les corrélations entre deux indicateurs.
 *
 * @param string $idIndicateur1 Nom du premier indicateur (ex. 'pib').
 * @param string $idIndicateur2 Nom du second indicateur (ex. 'esperance_vie').
 * @param string|null $filtre Code du pays ou ID de la région pour filtrer les données (optionnel).
 * @param string $typeFiltre Type de filtre à appliquer : 'pays' pour un pays ou 'region' pour une région. Par défaut 'pays'.
 * @return array Résultat contenant le coefficient de corrélation, le filtre appliqué, et le type de filtre. Retourne null pour la corrélation en cas d'erreur.
 */
function getCorrelationEntreIndicateurs($idIndicateur1, $idIndicateur2, $filtre = null, $typeFiltre = 'pays') {
    try {
        $conn = getBDD();
        $query = "SELECT 
                    (SUM(i1.$idIndicateur1 * i2.$idIndicateur2) - 
                     SUM(i1.$idIndicateur1) * SUM(i2.$idIndicateur2) / COUNT(*)) /
                    (SQRT(SUM(POW(i1.$idIndicateur1, 2)) - POW(SUM(i1.$idIndicateur1), 2) / COUNT(*)) *
                     SQRT(SUM(POW(i2.$idIndicateur2, 2)) - POW(SUM(i2.$idIndicateur2), 2) / COUNT(*))) AS correlation";

        // Ajouter les jointures nécessaires
        $query .= " FROM indicateurs AS i1
                    JOIN indicateurs AS i2 ON i1.code_pays = i2.code_pays
                    JOIN pays AS p ON i1.code_pays = p.code_pays";

        // Ajouter une condition pour le filtre (pays ou région)
        if ($filtre !== null) {
            if ($typeFiltre === 'pays') {
                $query .= " WHERE p.code_pays = ?";
            } elseif ($typeFiltre === 'region') {
                $query .= " WHERE p.id_region = ?";
            }
        }

        $query .= " AND i1.$idIndicateur1 IS NOT NULL AND i2.$idIndicateur2 IS NOT NULL;";

        $stmt = mysqli_prepare($conn, $query);

        if ($stmt === false) {
            throw new Exception(mysqli_error($conn));
        }

        // Lier les paramètres si un filtre est spécifié
        if ($filtre !== null) {
            mysqli_stmt_bind_param($stmt, "s", $filtre);
        }

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception(mysqli_error($conn));
        }

        $res = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($res);

        // Ajouter le filtre (pays ou région) dans la sortie
        return [
            'correlation' => $row['correlation'] ?? null,
            'filtre' => $filtre,
            'typeFiltre' => $typeFiltre
        ];
    } catch (Exception $e) {
        logError($e->getMessage(), __FILE__, __LINE__);
        return [
            'correlation' => null,
            'filtre' => $filtre,
            'typeFiltre' => $typeFiltre
        ];
    }
}

/**
 * Obtenir l'évolution d'un indicateur dans le temps pour une région donnée.
 *
 * @param string $idIndicateur Nom de l'indicateur.
 * @param string $idRegion ID de la région.
 * @return array Liste des années avec les statistiques suivantes pour l'indicateur dans la région :
 *               - valeur_moyenne : Moyenne des valeurs pour l'année.
 *               - mediane : Médiane des valeurs pour l'année.
 *               - ecart_type : Écart-type des valeurs pour l'année.
 *               - q1 : Premier quartile des valeurs pour l'année.
 *               - q3 : Troisième quartile des valeurs pour l'année.
 */
function getEvolutionIndicateurParRegion($idIndicateur, $idRegion) {
    try {
        $conn = getBDD();
        $query = "SELECT i.annee, i.$idIndicateur AS valeur
                  FROM indicateurs AS i
                  JOIN pays AS p ON i.code_pays = p.code_pays
                  WHERE p.id_region = ? AND i.$idIndicateur IS NOT NULL
                  ORDER BY i.annee;";
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt === false) {
            throw new Exception(mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "s", $idRegion);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception(mysqli_error($conn));
        }

        $res = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_all($res, MYSQLI_ASSOC);

        // Regrouper les données par année
        $result = [];
        $groupedData = [];
        foreach ($data as $row) {
            $groupedData[$row['annee']][] = $row['valeur'];
        }

        // Calculer les statistiques pour chaque année
        foreach ($groupedData as $annee => $valeurs) {
            sort($valeurs); // Trier les valeurs pour les calculs de médiane et quartiles
            $count = count($valeurs);
            $mean = array_sum($valeurs) / $count;
            $median = $valeurs[floor(($count - 1) / 2)];
            if ($count % 2 === 0) {
                $median = ($median + $valeurs[$count / 2]) / 2;
            }
            $q1 = $valeurs[floor(($count - 1) / 4)];
            $q3 = $valeurs[floor(3 * ($count - 1) / 4)];
            $variance = array_sum(array_map(fn($x) => pow($x - $mean, 2), $valeurs)) / $count;
            $stdDev = sqrt($variance);

            $result[] = [
                'annee' => $annee,
                'valeur_moyenne' => $mean,
                'mediane' => $median,
                'ecart_type' => $stdDev,
                'q1' => $q1,
                'q3' => $q3,
            ];
        }

        return $result;
    } catch (Exception $e) {
        logError($e->getMessage(), __FILE__, __LINE__);
        return [];
    }
}

/**
 * Récupérer la distribution d'un indicateur par région pour une année donnée.
 *
 * @param string $idIndicateur Nom de l'indicateur.
 * @param int $annee Année pour laquelle récupérer la distribution.
 * @return string JSON contenant la liste des régions et des valeurs moyennes de l'indicateur.
 */
function getDistributionIndicateurParRegion($idIndicateur, $annee) {
    try {
        $conn = getBDD();
        $query = "SELECT r.nom_region, AVG(i.$idIndicateur) AS valeur_moyenne
                  FROM indicateurs AS i
                  JOIN pays AS p ON i.code_pays = p.code_pays
                  JOIN regions AS r ON p.id_region = r.id_region
                  WHERE i.annee = ? AND i.$idIndicateur IS NOT NULL
                  GROUP BY r.nom_region
                  ORDER BY valeur_moyenne DESC;";
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt === false) {
            throw new Exception(mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "i", $annee);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception(mysqli_error($conn));
        }

        $res = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_all($res, MYSQLI_ASSOC) ?: [];
        return json_encode($data); // Retourne un JSON valide
    } catch (Exception $e) {
        return json_encode([]); // Retourne un tableau vide en cas d'erreur
    }
}

/**
 * Map country codes from the database to match those in world.geojson.
 *
 * @param string $codePays Code from the database.
 * @return string Mapped code for world.geojson.
 */
function mapCountryCode($codePays) {
    $mapping = [
        'CYN' => 'CYP', // Northern Cyprus to Cyprus
        'KOS' => 'SRB', // Kosovo to Serbia
        'GRL' => 'DNK', // Greenland to Denmark
        'CHI' => 'CHE', // Switzerland
        'MKD' => 'MKD', // North Macedonia
        'GBR' => 'GBR', // United Kingdom
        'DEU' => 'DEU', // Germany
        'FRA' => 'FRA', // France
        'ITA' => 'ITA', // Italy
        'RUS' => 'RUS', // Russia
        'ESP' => 'ESP', // Spain
        'NLD' => 'NLD', // Netherlands
        'TUR' => 'TUR', // Turkey
        'CHE' => 'CHE', // Switzerland
        'POL' => 'POL', // Poland
        'SWE' => 'SWE', // Sweden
        'BEL' => 'BEL', // Belgium
        'AUT' => 'AUT', // Austria
        'NOR' => 'NOR', // Norway
        'IRL' => 'IRL', // Ireland
        'DNK' => 'DNK', // Denmark
        'FIN' => 'FIN', // Finland
        'CZE' => 'CZE', // Czechia
        'ROU' => 'ROU', // Romania
        'PRT' => 'PRT', // Portugal
        'GRC' => 'GRC', // Greece
        'KAZ' => 'KAZ', // Kazakhstan
        'HUN' => 'HUN', // Hungary
        'UKR' => 'UKR', // Ukraine
        'SVK' => 'SVK', // Slovakia
        'LUX' => 'LUX', // Luxembourg
        'BGR' => 'BGR', // Bulgaria
        'HRV' => 'HRV', // Croatia
        'BLR' => 'BLR', // Belarus
        'SVN' => 'SVN', // Slovenia
        'LTU' => 'LTU', // Lithuania
        'SRB' => 'SRB', // Serbia
        'UZB' => 'UZB', // Uzbekistan
        'AZE' => 'AZE', // Azerbaijan
        'TKM' => 'TKM', // Turkmenistan
        'LVA' => 'LVA', // Latvia
        'EST' => 'EST', // Estonia
        'ISL' => 'ISL', // Iceland
        'CYP' => 'CYP', // Cyprus
        'BIH' => 'BIH', // Bosnia and Herzegovina
        'GEO' => 'GEO', // Georgia
        'ALB' => 'ALB', // Albania
        'MKD' => 'MKD', // North Macedonia
        'ARM' => 'ARM', // Armenia
        'MDA' => 'MDA', // Moldova
        'KGZ' => 'KGZ', // Kyrgyzstan
        'TJK' => 'TJK', // Tajikistan
        'MNE' => 'MNE', // Montenegro
        'AND' => 'AND', // Andorra
    ];
    return $mapping[$codePays] ?? $codePays;
}

/**
 * Récupérer la distribution d'un indicateur par pays pour une région donnée et une année.
 *
 * @param string $idIndicateur Nom de l'indicateur.
 * @param int $annee Année pour laquelle récupérer la distribution.
 * @param string $nomRegion Nom de la région.
 * @return string JSON contenant la liste des pays et des valeurs de l'indicateur.
 */
function getDistributionIndicateurParPays($idIndicateur, $annee, $nomRegion) {
    try {
        $conn = getBDD();
        $query = "SELECT p.nom_pays AS nom_pays_bdd, p.code_pays, i.$idIndicateur AS valeur
                  FROM indicateurs AS i
                  JOIN pays AS p ON i.code_pays = p.code_pays
                  JOIN regions AS r ON p.id_region = r.id_region
                  WHERE i.annee = ? AND r.nom_region = ? AND i.$idIndicateur IS NOT NULL
                  ORDER BY valeur DESC;";
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt === false) {
            error_log("Erreur de préparation SQL : " . mysqli_error($conn)); // Log en cas d'erreur de préparation
            throw new Exception(mysqli_error($conn));
        }

        error_log("Exécution de la requête : $query avec annee=$annee et nomRegion=$nomRegion");
        mysqli_stmt_bind_param($stmt, "is", $annee, $nomRegion);
        if (!mysqli_stmt_execute($stmt)) {
            error_log("Erreur d'exécution SQL : " . mysqli_error($conn)); // Log en cas d'erreur d'exécution
            throw new Exception(mysqli_error($conn));
        }

        $res = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_all($res, MYSQLI_ASSOC) ?: [];

        if (empty($data)) {
            error_log("Aucune donnée trouvée pour la région $nomRegion et l'année $annee.");
        } else {
            error_log("Données retournées : " . json_encode($data));
        }

        // Map country codes to match world.geojson
        $mappedData = [];
        foreach ($data as $row) {
            $mappedData[] = [
                'nom_pays_geojson' => mapCountryCode($row['code_pays']),
                'valeur' => $row['valeur']
            ];
        }
        error_log("Codes pays mappés : " . json_encode(array_column($mappedData, 'nom_pays_geojson')));

        return json_encode($mappedData); // Retourne un JSON valide
    } catch (Exception $e) {
        error_log("Exception capturée : " . $e->getMessage()); // Log des exceptions
        return json_encode([]); // Retourne un tableau vide en cas d'erreur
    }
}

/**
 * Récupérer les données pour un pays spécifique.
 *
 * @param string $codePays Code du pays.
 * @return array Données du pays sous forme de tableau associatif.
 */
function getCountryData($codePays) {
    try {
        $conn = getBDD();
        $query = "SELECT * FROM indicateurs WHERE code_pays = ? ORDER BY annee DESC LIMIT 1;";
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt === false) {
            throw new Exception(mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "s", $codePays);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception(mysqli_error($conn));
        }

        $res = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($res) ?: [];
    } catch (Exception $e) {
        logError($e->getMessage(), __FILE__, __LINE__);
        return [];
    }
}

/**
 * Récupère le top 5 des pays ayant le plus haut PIB moyen par région entre 1960 et 2018.
 *
 * @return array Tableau associatif contenant les régions comme clés et pour chaque région,
 *               un tableau de 5 pays avec leur nom et la valeur moyenne de leur PIB.
 *               En cas d'erreur, retourne un tableau avec la clé 'error' et le message d’erreur.
 */
function getTop5PIBParRegion() {
    try {
        $conn = getBDD();
        $query = "
            SELECT 
                r.nom_region,
                p.nom_pays,
                AVG(i.pib) AS moyenne_pib
            FROM indicateurs i
            JOIN pays p ON i.code_pays = p.code_pays
            JOIN regions r ON p.id_region = r.id_region
            WHERE i.pib IS NOT NULL
            GROUP BY r.nom_region, p.nom_pays
            ORDER BY r.nom_region, moyenne_pib DESC;
        ";

        $stmt = mysqli_prepare($conn, $query);
        if (!$stmt) {
            error_log("Erreur de préparation SQL : " . mysqli_error($conn));
            throw new Exception("Erreur de préparation SQL.");
        }

        if (!mysqli_stmt_execute($stmt)) {
            error_log("Erreur d'exécution SQL : " . mysqli_error($conn));
            throw new Exception("Erreur d'exécution SQL.");
        }

        $result = mysqli_stmt_get_result($stmt);
        if (!$result) {
            error_log("Erreur lors de la récupération des résultats : " . mysqli_error($conn));
            throw new Exception("Erreur lors de la récupération des résultats.");
        }

        $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

        if (empty($data)) {
            error_log("Aucune donnée trouvée pour la requête.");
            throw new Exception("Aucune donnée trouvée.");
        }

        $topParRegion = [];
        foreach ($data as $row) {
            $region = $row['nom_region'];
            if (!isset($topParRegion[$region])) {
                $topParRegion[$region] = [];
            }
            if (count($topParRegion[$region]) < 5) {
                $topParRegion[$region][] = [
                    'pays' => $row['nom_pays'],
                    'moyenne_pib' => round($row['moyenne_pib'], 2)
                ];
            }
        }

        return $topParRegion;
    } catch (Exception $e) {
        error_log("Erreur dans getTop5PIBParRegion: " . $e->getMessage());
        return ['error' => 'Erreur lors de la récupération des données.'];
    }
}
?>
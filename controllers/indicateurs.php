<?php
// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclure les modèles nécessaires
require_once __DIR__ . '/../models/indicateur.php';
require_once __DIR__ . '/../models/pays.php';
require_once __DIR__ . '/../models/data.php';
require_once __DIR__ . '/../models/indices.php';

// Définir le type de contenu comme JSON
header('Content-Type: application/json');

// Vérifier l'action demandée
if (isset($_GET['action'])) {
    $action = $_GET['action'];

    switch ($action) {
        case 'getMoyennePIBMondial':
            try {
                $result = getMoyennePIBMondial();
                if (isset($result['error'])) {
                    error_log("Erreur dans getMoyennePIBMondial: " . $result['error']);
                }
                echo json_encode($result);
            } catch (Exception $e) {
                error_log("Exception dans getMoyennePIBMondial: " . $e->getMessage());
                echo json_encode(['error' => 'Une erreur est survenue lors du chargement des données.']);
            }
            break;

        case 'getEsperanceVieMondiale':
            echo json_encode(getEsperanceVieMondiale());
            break;

        case 'getRatioParRegionParAnnee':
            echo json_encode(getRatioParRegionParAnnee());
            break;

        case 'getAllGlobalStats': // 
            $result = [
                'moyenne_pib_mondial' => getMoyennePIBMondial(),
                'esperance_vie_mondiale' => getEsperanceVieMondiale(),
                'ratio_par_region_par_annee' => getRatioParRegionParAnnee()
            ];
            echo json_encode($result);
            break;
            
        case 'getIdhParPays':
            if (isset($_GET['code'])) {
                $code = $_GET['code'];
                $idhData = getDernierIdhParPays($code); // Appel à ta fonction du modèle
                echo json_encode($idhData ?: ["idh" => null]);
            } else {
                echo json_encode(["error" => "Code pays manquant"]);
            }
            exit;

        case 'comparerPays':
            $pays1 = $_GET['pays1'] ?? null;
            $pays2 = $_GET['pays2'] ?? null;
            $indicateur = $_GET['indicateur'] ?? null;

            if (!$pays1 || !$pays2 || !$indicateur || $pays1 === $pays2) {
                echo json_encode(['error' => 'Paramètres invalides.']);
                exit;
            }

            $valeurs1 = getValeursIndicateur($indicateur, $pays1);
            $valeurs2 = getValeursIndicateur($indicateur, $pays2);

            $annees = array_map(fn($v) => $v['annee'], $valeurs1);
            $valeursPays1 = array_map(fn($v) => $v['valeur'], $valeurs1);
            $valeursPays2 = array_map(fn($v) => $v['valeur'], $valeurs2);

            $details1 = getDetailsPays($pays1);
            $details2 = getDetailsPays($pays2);

            echo json_encode([
                'indicateur' => $indicateur,
                'nomPays1' => $details1['nom_pays'] ?? $pays1,
                'nomPays2' => $details2['nom_pays'] ?? $pays2,
                'annees' => $annees,
                'valeurs1' => $valeursPays1,
                'valeurs2' => $valeursPays2
            ]);
            exit;

        case 'getDistributionIndicateurParRegion':
            if (isset($_GET['idIndicateur'], $_GET['annee'])) {
                $idIndicateur = $_GET['idIndicateur'];
                $annee = (int)$_GET['annee'];
                echo getDistributionIndicateurParRegion($idIndicateur, $annee);
            } else {
                echo json_encode(['error' => 'Paramètres manquants']);
            }
            break;

        case 'getDistributionIndicateurParPays':
            if (isset($_GET['idIndicateur'], $_GET['annee'], $_GET['region'])) {
                $idIndicateur = $_GET['idIndicateur'];
                $annee = (int)$_GET['annee'];
                $region = urldecode($_GET['region']); // Décoder le nom de la région
                error_log("Nom de la région après décodage : $region");
                error_log("Paramètres reçus : idIndicateur=$idIndicateur, annee=$annee, region=$region");
                echo getDistributionIndicateurParPays($idIndicateur, $annee, $region);
            } else {
                error_log("Paramètres manquants pour getDistributionIndicateurParPays");
                echo json_encode(['error' => 'Paramètres manquants']);
            }
            break;

            case 'getCountryData':
                if (isset($_GET['codePays'])) {
                    $codePays = $_GET['codePays'];
                    echo json_encode(getCountryData($codePays));
                } else {
                    echo json_encode(['error' => 'Paramètre codePays manquant']);
                }
            break;

            case 'getIndicateurs':
                echo json_encode(getIndicateurs());
                break;
    
            case 'getValeursIndicateur':
                if (isset($_GET['indicateur'], $_GET['code_pays'])) {
                    $indicateur = $_GET['indicateur'];
                    $codePays = $_GET['code_pays'];
                    echo json_encode(getValeursIndicateur($indicateur, $codePays));
                } else {
                    echo json_encode(['error' => 'Paramètres manquants']);
                }
                break;
    
            case 'getMoyenneIndicateurParRegion':
                if (isset($_GET['indicateur'], $_GET['region'])) {
                    $indicateur = $_GET['indicateur'];
                    $region = $_GET['region'];
                    echo json_encode(getMoyenneIndicateurParRegion($indicateur, $region));
                } else {
                    echo json_encode(['error' => 'Paramètres manquants']);
                }
                break;
    
            case 'getIndicateursParAnnee':
                if (isset($_GET['annee'])) {
                    $annee = (int)$_GET['annee'];
                    echo json_encode(getIndicateursParAnnee($annee));
                } else {
                    echo json_encode(['error' => 'Paramètre année manquant']);
                }
                break;
            
            case 'getIndicateursParAnneePays':
                    if (isset($_GET['annee']) && isset($_GET['code_pays'])) {
                        $annee = (int)$_GET['annee'];
                        $codePays = $_GET['code_pays'];
                        echo json_encode(getIndicateursParAnneePays($annee, $codePays));
                    } else {
                        echo json_encode(['error' => 'Paramètres année ou code pays manquants']);
                    }
            break;
    
            default:
                echo json_encode(['error' => 'Action inconnue']);
                break;
        }
        exit;
}

// Si aucune action n'est spécifiée, retourner une erreur
echo json_encode(['error' => 'Aucune action spécifiée']);
exit;


// ----------------------------------
// FONCTIONS DE CALCUL
// ----------------------------------

function getMoyennePIBMondial() {
    return getTop5PIBParRegion();
}

function getEsperanceVieMondiale() {
    $pays = getPays();
    $esperanceParAnnee = [];

    if ($pays === null || !is_array($pays)) {
        return ['error' => 'Aucun pays trouvé ou erreur de récupération des pays.'];
    }

    foreach ($pays as $paysInfo) {
        $codePays = $paysInfo['code_pays'];
        $valeurs = getValeursIndicateur('esperance_vie', $codePays);

        if ($valeurs === null || empty($valeurs)) {
            continue;
        }

        foreach ($valeurs as $data) {
            $annee = $data['annee'];
            $esperance = $data['valeur'];

            if (is_null($esperance) || $esperance <= 0) {
                continue;
            }

            if (!isset($esperanceParAnnee[$annee])) {
                $esperanceParAnnee[$annee] = ['total' => 0, 'nb_pays' => 0];
            }

            $esperanceParAnnee[$annee]['total'] += $esperance;
            $esperanceParAnnee[$annee]['nb_pays']++;
        }
    }

    $moyenneEsperance = [];
    foreach ($esperanceParAnnee as $annee => $data) {
        if ($data['nb_pays'] > 0) {
            $moyenneEsperance[$annee] = $data['total'] / $data['nb_pays'];
        }
    }

    return $moyenneEsperance;
}

function getRatioParRegionParAnnee() {
    $conn = getBDD();
    $pays = getPays();
    $dataParRegionAnnee = [];

    if ($pays === null || !is_array($pays)) {
        return ['error' => 'Aucun pays trouvé ou erreur de récupération des pays.'];
    }

    foreach ($pays as $paysInfo) {
        $codePays = $paysInfo['code_pays'];
        $details = getDetailsPays($codePays);
        if ($details === null || !isset($details['nom_region'])) {
            continue;
        }

        $region = $details['nom_region'];
        $valeursNatalite = getValeursIndicateur('taux_natalite', $codePays);
        $valeursMortalite = getValeursIndicateur('taux_mortalite', $codePays);

        if (empty($valeursNatalite) || empty($valeursMortalite)) {
            continue;
        }

        foreach ($valeursNatalite as $index => $nataliteData) {
            $annee = $nataliteData['annee'];
            $tauxNatalite = $nataliteData['valeur'];
            $tauxMortalite = $valeursMortalite[$index]['valeur'] ?? null;

            if (is_null($tauxNatalite) || is_null($tauxMortalite) || $tauxMortalite <= 0) {
                continue;
            }

            if (!isset($dataParRegionAnnee[$region][$annee])) {
                $dataParRegionAnnee[$region][$annee] = ['total' => 0, 'nb' => 0];
            }

            $dataParRegionAnnee[$region][$annee]['total'] += $tauxNatalite / $tauxMortalite;
            $dataParRegionAnnee[$region][$annee]['nb']++;
        }
    }

    $resultat = [];
    foreach ($dataParRegionAnnee as $region => $annees) {
        foreach ($annees as $annee => $data) {
            if ($data['nb'] > 0) {
                $resultat[$region][$annee] = round($data['total'] / $data['nb'], 2);
            }
        }
    }

    return $resultat;
}
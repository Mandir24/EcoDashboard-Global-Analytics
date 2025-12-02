<?php
require_once __DIR__ . '/../models/pays.php';
require_once __DIR__ . '/../models/indicateur.php';

header('Content-Type: application/json');

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    switch ($action) {
        case 'getRegions':
            echo json_encode(getRegions());
            exit;

        case 'getPaysParRegion':
            if (isset($_GET['region'])) {
                $region = $_GET['region'];
                echo json_encode(getPaysParRegion($region));
            } else {
                echo json_encode(['error' => 'Paramètre "region" manquant']);
            }
            exit;

        case 'getDetailsPays':
            if (isset($_GET['code_pays'])) {
                $code = $_GET['code_pays'];
                echo json_encode(getDetailsPays($code));
            } else {
                echo json_encode(['error' => 'Paramètre "code_pays" manquant']);
            }
            exit;

        default:
            echo json_encode(['error' => 'Action non reconnue']);
            exit;
    }
}

// Si aucune action n'est spécifiée, retourner une erreur
echo json_encode(['error' => 'Aucune action spécifiée']);
exit;
?>
<?php

/**
 * Configuration de l'application (connexion à la base de données)
 *
 * Ce fichier configure la connexion à la base de données MySQL utilisée par l'application.
 *
 * Connexion à la base de données:
 * - La fonction `getBDD` établit une connexion à la base de données MySQL en utilisant `mysqli`.
 * - Active le mode d'exception pour `mysqli` afin de lever des exceptions en cas d'erreur SQL.
 * - En cas d'erreur de connexion, un message est affiché et l'exécution est arrêtée.
 *
 * Variables:
 * - $db_host (string): Adresse du serveur de base de données.
 * - $db_name (string): Nom de la base de données.
 * - $db_user (string): Nom d'utilisateur pour la connexion.
 * - $db_pass (string): Mot de passe pour la connexion.
 *
 * Retour:
 * - mysqli: Instance de connexion MySQLi.
 *
 * Exceptions:
 * - Utilise `mysqli_sql_exception` pour signaler les erreurs SQL.
 * - Arrête l'exécution en cas d'erreur de connexion.
 */
function getBDD() {
    // Render fournit ces informations via des variables d'environnement
    $db_host = getenv('DB_HOST') ?: "localhost";
    $db_name = getenv('DB_NAME') ?: "economie_mondiale";
    $db_user = getenv('DB_USER') ?: "root"; 
    $db_pass = getenv('DB_PASS') ?: "root"; 

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
        return $conn;
    } catch (Exception $e) {
        die("Erreur de connexion à la base de données : " . $e->getMessage());
    }
}
?>


    <nav>
        <ul>
            <li><a href="index.php?url=infos_pays">Informations sur les pays</a></li>
            <li><a href="index.php?url=dashboard">Tableau de bord</a></li>
            <li><a href="index.php?url=comparaison_pays">Comparaison des pays</a></li>
        </ul>
    </nav>

    <?php
    // Point d'entrée de l'application

    // Récupérer l'URL demandée
    $url = isset($_GET['url']) ? $_GET['url'] : '';

    // Charger les fichiers en fonction de l'URL
    switch ($url) {
        case 'dashboard':
            include 'views/dashboard.php';
            break;  
        case 'infos_pays':
            include 'views/infos_pays.php';
            break;
        case 'comparaison_pays':
            include 'views/comparaison_pays.php';
            break;
        default:
            include 'views/dashboard.php';
            break;
    }
    ?>




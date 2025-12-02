<?php
/**
 * 
 * Ceci est le point d'entrée principal de l'application DED.
 * 
 * La structure HTML inclut un menu de navigation avec des liens vers différentes sections de l'application :
 * - Tableau de bord
 * - Informations sur les pays
 * - Comparaison des pays
 * 
 * Le code PHP récupère le paramètre d'URL demandé et inclut le fichier correspondant.
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Application DED</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>

    <?php
    include('views/partials/header.php'); // Inclusion du header
    include('views/partials/selecteurs.php'); // Inclusion du sélecteur
    include('views/partials/footer.php'); // Inclusion du footer
    ?>
    

    
</body>
</html>
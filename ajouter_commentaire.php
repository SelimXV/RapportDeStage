<?php
session_start();

// Vérifie si l'utilisateur est un professeur
if (!isset($_SESSION['login']) || $_SESSION['role'] != 1) {
    header('Location: index.php');
    exit();
}

include '_conf.php';

$connexion = mysqli_connect($serveurBDD, $userBDD, $mdpBDD, $nomBDD);
if (!$connexion) {
    die("Connexion à la base de données échouée");
}

// Traitement du formulaire d'ajout de commentaire
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['commentaire'])) {
    $id_cr = $_POST['id_cr'];
    $commentaire = $_POST['commentaire'];
    $id_prof = $_SESSION['id_user'];

    $query = "
        INSERT INTO commentaires (id_cr, id_prof, commentaire)
        VALUES ($id_cr, $id_prof, '$commentaire')
    ";
    mysqli_query($connexion, $query);
    header('Location: liste_comptes_rendus_prof.php'); // Redirige après l'ajout
}

mysqli_close($connexion);
?>
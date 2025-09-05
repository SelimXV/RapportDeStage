<?php
require_once '_conf.php';
session_start();

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['id_user'])) {
    echo "Erreur : Vous n'êtes pas connecté.";
    exit();
}

$id_user = $_SESSION['id_user'];

// Vérifier si un ID de compte rendu est passé dans l'URL
if (!isset($_GET['id'])) {
    echo "Erreur : ID du compte rendu manquant.";
    exit();
}

$id_cr = $_GET['id'];

// Connexion à la base de données
try {
    $pdo = new PDO("mysql:host=$serveurBDD;dbname=$nomBDD", $userBDD, $mdpBDD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Vérifier que le compte rendu appartient bien à l'utilisateur connecté
$stmt = $pdo->prepare("SELECT id FROM CR WHERE id = :id_cr AND id_user = :id_user");
$stmt->execute(['id_cr' => $id_cr, 'id_user' => $id_user]);
$compte_rendu = $stmt->fetch();

if (!$compte_rendu) {
    echo "Erreur : Ce compte rendu ne vous appartient pas ou n'existe pas.";
    exit();
}

// Supprimer le compte rendu
$stmt = $pdo->prepare("DELETE FROM CR WHERE id = :id_cr");
$stmt->execute(['id_cr' => $id_cr]);

// Rediriger vers la liste des comptes rendus avec un message de succès
header('Location: liste_comptes_rendus.php?message=Le compte rendu a été supprimé avec succès.');
exit();
?>
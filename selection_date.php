<?php
session_start();
require_once '_conf.php';

if (!isset($_SESSION['id_user'])) {
    echo "Erreur : Vous n'êtes pas connecté.";
    exit();
}

$id_user = $_SESSION['id_user'];

// Connexion à la base de données
try {
    $pdo = new PDO("mysql:host=$serveurBDD;dbname=$nomBDD", $userBDD, $mdpBDD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Si une date est sélectionnée, on peut l'inclure dans le formulaire
$date_selectionnee = isset($_GET['date_selectionnee']) ? $_GET['date_selectionnee'] : date('Y-m-d H:i:s');

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sujet = $_POST['sujet'];
    $contenu = $_POST['contenu'];

    $stmt = $pdo->prepare("INSERT INTO CR (id_user, sujet, contenu, date_creation) VALUES (:id_user, :sujet, :contenu, :date_creation)");
    $stmt->execute([
        'id_user' => $id_user,
        'sujet' => $sujet,
        'contenu' => $contenu,
        'date_creation' => $date_selectionnee
    ]);

    header('Location: liste_comptes_rendus.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un compte rendu</title>
</head>
<body>

<h1>Créer un nouveau compte rendu</h1>

<form method="POST" action="selection_date.php">
    <label for="sujet">Sujet :</label>
    <input type="text" name="sujet" id="sujet" required>

    <label for="contenu">Contenu :</label>
    <textarea name="contenu" id="contenu" required></textarea>

    <label for="date_creation">Date de création :</label>
    <input type="text" name="date_creation" id="date_creation" value="<?= htmlspecialchars($date_selectionnee) ?>" disabled>

    <input type="submit" value="Créer le compte rendu">
</form>

</body>
</html>

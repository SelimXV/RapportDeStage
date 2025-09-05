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

// Traitement du formulaire d'ajout/modification de commentaire
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['commentaire'])) {
    $id_cr = $_POST['id_cr'];
    $commentaire = mysqli_real_escape_string($connexion, $_POST['commentaire']);
    $id_prof = $_SESSION['id_user'];

    // Vérifier si le professeur a déjà commenté ce CR
    $query_check = "SELECT id FROM commentaires WHERE id_cr = $id_cr AND id_prof = $id_prof";
    $result_check = mysqli_query($connexion, $query_check);
    
    if (mysqli_num_rows($result_check) > 0) {
        // Le professeur a déjà commenté → UPDATE
        $query = "UPDATE commentaires SET commentaire = '$commentaire', date_commentaire = NOW() WHERE id_cr = $id_cr AND id_prof = $id_prof";
    } else {
        // Premier commentaire du professeur → INSERT
        $query = "INSERT INTO commentaires (id_cr, id_prof, commentaire) VALUES ($id_cr, $id_prof, '$commentaire')";
    }
    
    mysqli_query($connexion, $query);
    header('Location: liste_comptes_rendus_prof.php'); // Redirige après l'ajout/modification
}

// Récupérer la liste des élèves pour le filtre (seulement ceux assignés au professeur connecté)
$id_prof_connecte = $_SESSION['id_user'];
$query_eleves = "
    SELECT user.id, user.nom, user.prenom 
    FROM user 
    JOIN stage ON user.id = stage.id_user
    WHERE user.id_statut = 2 
    AND stage.id_prof = $id_prof_connecte
";
$result_eleves = mysqli_query($connexion, $query_eleves);

// Récupérer l'élève sélectionné (s'il y en a un)
$id_eleve_filtre = $_GET['eleve'] ?? null;

// Récupérer tous les comptes rendus avec les informations des élèves (seulement ceux du professeur connecté)
$query = "
    SELECT CR.*, user.nom, user.prenom 
    FROM CR 
    JOIN user ON CR.id_user = user.id 
    JOIN stage ON user.id = stage.id_user
    WHERE stage.id_prof = $id_prof_connecte
";

// Ajouter un filtre si un élève est sélectionné
if ($id_eleve_filtre) {
    $query .= " AND CR.id_user = $id_eleve_filtre ";
}

$query .= " ORDER BY CR.date_creation DESC";
$result = mysqli_query($connexion, $query);

mysqli_close($connexion);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comptes Rendus des Élèves - Espace Professeur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3b71ca;
            --primary-hover: #2c5eaa;
            --secondary-color: #f0f2f5;
        }
        
        body {
            background-color: var(--secondary-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            padding-top: 60px;
        }
        
        .navbar {
            background-color: var(--primary-color);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-brand {
            font-weight: 600;
            font-size: 1.25rem;
            color: white !important;
        }
        
        .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: color 0.2s;
        }
        
        .nav-link:hover {
            color: white !important;
        }
        
        .reports-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .welcome-header {
            background-color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        .filter-section {
            background-color: white;
            padding: 1.5rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        .report-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 1.5rem;
            border: 1px solid #e5e7eb;
            overflow: hidden;
        }
        
        .report-header {
            background-color: #f8fafc;
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .report-body {
            padding: 1.5rem;
        }
        
        .report-content {
            background-color: #f8fafc;
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
            border: 1px solid #e5e7eb;
            white-space: pre-wrap;
        }
        
        .comment-section {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e5e7eb;
        }
        
        .comment-item {
            background-color: #f8fafc;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            border: 1px solid #e5e7eb;
        }
        
        .form-control {
            border-radius: 8px;
            padding: 0.75rem 1rem;
            border: 1px solid #ced4da;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(59, 113, 202, 0.25);
        }
        
        .form-select {
            border-radius: 8px;
            padding: 0.75rem 1rem;
            border: 1px solid #ced4da;
        }
        
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(59, 113, 202, 0.25);
        }
        
        .btn-custom {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .date-info {
            color: #6b7280;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        .student-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="accueil_prof.php">
            <i class="fas fa-chalkboard-teacher me-2"></i>Espace Professeur
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="accueil_prof.php">
                        <i class="fas fa-home me-1"></i>Accueil
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="liste_comptes_rendus_prof.php">
                        <i class="fas fa-list-alt me-1"></i>Comptes rendus
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="perso_prof.php">
                        <i class="fas fa-user-cog me-1"></i>Mon profil
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="deconnexion.php">
                        <i class="fas fa-sign-out-alt me-1"></i>Déconnexion
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="reports-container">
    <!-- En-tête -->
    <div class="welcome-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1">Comptes Rendus des Élèves</h1>
                <p class="text-muted mb-0">Consultez et commentez les comptes rendus</p>
            </div>
        </div>
    </div>
    
    <!-- Filtre par élève -->
    <div class="filter-section">
        <form method="GET" action="liste_comptes_rendus_prof.php" class="row align-items-end g-3">
            <div class="col-md-8">
                <label for="eleve" class="form-label">
                    <i class="fas fa-user-graduate me-2"></i>Filtrer par élève
                </label>
                <select name="eleve" id="eleve" class="form-select">
                    <option value="">Tous les élèves</option>
                    <?php while ($eleve = mysqli_fetch_assoc($result_eleves)): ?>
                        <option value="<?= $eleve['id'] ?>" <?= $id_eleve_filtre == $eleve['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($eleve['prenom'] . ' ' . $eleve['nom']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-custom flex-grow-1">
                    <i class="fas fa-filter me-2"></i>Filtrer
                </button>
                <?php if ($id_eleve_filtre): ?>
                    <a href="liste_comptes_rendus_prof.php" class="btn btn-secondary btn-custom">
                        <i class="fas fa-times me-2"></i>Réinitialiser
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
    
    <!-- Liste des comptes rendus -->
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <div class="report-card">
            <div class="report-header">
                <div class="student-info">
                    <i class="fas fa-user-graduate"></i>
                    <?= htmlspecialchars($row['prenom'] . ' ' . $row['nom']) ?>
                </div>
                <div class="date-info">
                    <i class="fas fa-clock me-1"></i>Créé le <?= date('d/m/Y à H:i', strtotime($row['date_creation'])) ?>
                    <?php if ($row['date_modif']): ?>
                        <br>
                        <i class="fas fa-edit me-1"></i>Modifié le <?= date('d/m/Y à H:i', strtotime($row['date_modif'])) ?>
                    <?php endif; ?>
                </div>
                <h5 class="mb-0">
                    <i class="fas fa-file-alt me-2"></i><?= htmlspecialchars($row['sujet']) ?>
                </h5>
            </div>
            
            <div class="report-body">
                <div class="report-content">
                    <?= nl2br(htmlspecialchars($row['contenu'])) ?>
                </div>
                
                <div class="comment-section">
                    <h6 class="mb-3">
                        <i class="fas fa-comments me-2"></i>Commentaires
                    </h6>
                    
                    <?php
                    $connexion = mysqli_connect($serveurBDD, $userBDD, $mdpBDD, $nomBDD);
                    $query_commentaires = "
                        SELECT commentaires.*, user.nom, user.prenom 
                        FROM commentaires 
                        JOIN user ON commentaires.id_prof = user.id 
                        WHERE commentaires.id_cr = {$row['id']}
                        ORDER BY commentaires.date_commentaire DESC
                    ";
                    $result_commentaires = mysqli_query($connexion, $query_commentaires);
                    
                    // Vérifier si le professeur connecté a déjà commenté ce CR
                    $prof_a_commente = false;
                    $date_dernier_commentaire_prof = null;
                    
                    mysqli_data_seek($result_commentaires, 0); // Reset du curseur
                    while ($commentaire = mysqli_fetch_assoc($result_commentaires)):
                        if ($commentaire['id_prof'] == $_SESSION['id_user']) {
                            $prof_a_commente = true;
                            $date_dernier_commentaire_prof = $commentaire['date_commentaire'];
                            break;
                        }
                    endwhile;
                    
                    // Reset du curseur pour l'affichage
                    mysqli_data_seek($result_commentaires, 0);
                    while ($commentaire = mysqli_fetch_assoc($result_commentaires)):
                    ?>
                        <div class="comment-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="student-info mb-2">
                                        <i class="fas fa-user-tie"></i>
                                        <?= htmlspecialchars($commentaire['prenom'] . ' ' . $commentaire['nom']) ?>
                                    </div>
                                    <?= nl2br(htmlspecialchars($commentaire['commentaire'])) ?>
                                </div>
                                <small class="text-muted">
                                    <?= date('d/m/Y à H:i', strtotime($commentaire['date_commentaire'])) ?>
                                </small>
                            </div>
                        </div>
                    <?php endwhile;
                    
                    // Logique pour déterminer si on peut afficher le formulaire de commentaire
                    $peut_commenter = false;
                    $message_restriction = "";
                    
                    if (!$prof_a_commente) {
                        // Le prof n'a jamais commenté → peut commenter
                        $peut_commenter = true;
                    } else {
                        // Le prof a déjà commenté → vérifier si le CR a été modifié après son commentaire
                        if ($row['date_modif'] && $date_dernier_commentaire_prof) {
                            $date_modif_cr = new DateTime($row['date_modif']);
                            $date_commentaire = new DateTime($date_dernier_commentaire_prof);
                            
                            if ($date_modif_cr > $date_commentaire) {
                                $peut_commenter = true;
                                $message_restriction = "Le compte rendu a été modifié depuis votre dernier commentaire.";
                            } else {
                                $peut_commenter = false;
                                $message_restriction = "Vous avez déjà commenté ce compte rendu. Vous pourrez commenter à nouveau si l'élève le modifie.";
                            }
                        } else {
                            $peut_commenter = false;
                            $message_restriction = "Vous avez déjà commenté ce compte rendu. Vous pourrez commenter à nouveau si l'élève le modifie.";
                        }
                    }
                    
                    mysqli_close($connexion);
                    ?>
                    
                    <!-- Formulaire pour ajouter un commentaire ou message de restriction -->
                    <?php if ($peut_commenter): ?>
                        <?php if (!empty($message_restriction)): ?>
                            <div class="alert alert-info mb-3">
                                <i class="fas fa-info-circle me-2"></i><?= $message_restriction ?>
                            </div>
                        <?php endif; ?>
                        <form method="POST" action="liste_comptes_rendus_prof.php" class="mt-4">
                            <input type="hidden" name="id_cr" value="<?= $row['id'] ?>">
                            <div class="mb-3">
                                <label for="commentaire_<?= $row['id'] ?>" class="form-label">
                                    <i class="fas fa-comment me-2"></i><?= $prof_a_commente ? 'Modifier votre commentaire' : 'Ajouter un commentaire' ?>
                                </label>
                                <textarea name="commentaire" class="form-control" id="commentaire_<?= $row['id'] ?>" 
                                          rows="3" placeholder="Votre commentaire..." required></textarea>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary btn-custom">
                                    <i class="fas fa-paper-plane me-2"></i><?= $prof_a_commente ? 'Modifier' : 'Envoyer' ?>
                                </button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-warning mt-4">
                            <i class="fas fa-exclamation-triangle me-2"></i><?= $message_restriction ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
    
    <?php if (mysqli_num_rows($result) == 0): ?>
        <div class="text-center py-5">
            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
            <p class="text-muted">Aucun compte rendu trouvé.</p>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
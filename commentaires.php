<?php

require_once '_conf.php';
session_start();

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['id_user'])) {
    echo "Erreur : Vous n'êtes pas connecté.";
    exit();
}

$id_user = $_SESSION['id_user'];
$role = $_SESSION['role'];  // Récupération du rôle (professeur ou élève)

// Connexion à la base de données
try {
    $pdo = new PDO("mysql:host=$serveurBDD;dbname=$nomBDD", $userBDD, $mdpBDD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Récupérer le prénom de l'utilisateur pour l'affichage
$stmt = $pdo->prepare("SELECT prenom FROM user WHERE id = :id_user");
$stmt->execute(['id_user' => $id_user]);
$user = $stmt->fetch();
$prenom = $user ? $user['prenom'] : 'utilisateur';

// Si un ID de compte rendu est spécifié, on affiche les commentaires de ce compte rendu
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_cr = $_GET['id'];
    $mode = "single";
    
    // Récupérer le sujet du compte rendu
    $stmt = $pdo->prepare("SELECT sujet FROM CR WHERE id = :id_cr");
    $stmt->execute(['id_cr' => $id_cr]);
    $cr = $stmt->fetch();
    $sujet_cr = $cr ? $cr['sujet'] : 'Compte rendu inconnu';
    
    // Récupérer les commentaires de ce compte rendu
    $query = $pdo->prepare("SELECT c.commentaire, c.date_commentaire, u.nom, u.prenom 
                            FROM commentaires c
                            JOIN user u ON c.id_prof = u.id 
                            WHERE c.id_cr = :id_cr 
                            ORDER BY c.date_commentaire DESC");
    $query->execute(['id_cr' => $id_cr]);
    $commentaires = $query->fetchAll();
    
    // Si un commentaire a été ajouté/modifié (uniquement pour les professeurs)
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['commentaire']) && $role == 1) {
        $commentaire = $_POST['commentaire'];
        
        // Vérifier si le professeur a déjà commenté ce CR
        $check_query = $pdo->prepare("SELECT id FROM commentaires WHERE id_cr = :id_cr AND id_prof = :id_prof");
        $check_query->execute(['id_cr' => $id_cr, 'id_prof' => $id_user]);
        
        if ($check_query->fetch()) {
            // UPDATE du commentaire existant
            $update_query = $pdo->prepare("UPDATE commentaires SET commentaire = :commentaire, date_commentaire = NOW() 
                                           WHERE id_cr = :id_cr AND id_prof = :id_prof");
            $update_query->execute([
                'commentaire' => $commentaire,
                'id_cr' => $id_cr,
                'id_prof' => $id_user
            ]);
        } else {
            // INSERT d'un nouveau commentaire
            $insert_query = $pdo->prepare("INSERT INTO commentaires (id_cr, id_prof, commentaire, date_commentaire) 
                                            VALUES (:id_cr, :id_prof, :commentaire, NOW())");
            $insert_query->execute([
                'id_cr' => $id_cr,
                'id_prof' => $id_user,
                'commentaire' => $commentaire
            ]);
        }
        
        // Rediriger pour afficher le commentaire ajouté
        header("Location: commentaires.php?id=$id_cr");
        exit();
    }
} else {
    // Si aucun ID n'est spécifié, on affiche tous les commentaires des comptes rendus de l'élève
    $mode = "all";
    
    // Pour un élève, on récupère tous ses comptes rendus avec leurs commentaires
    if ($role == 2) { // Élève
        $query = $pdo->prepare("
            SELECT cr.id, cr.sujet, c.commentaire, c.date_commentaire, u.nom, u.prenom 
            FROM CR cr
            LEFT JOIN commentaires c ON cr.id = c.id_cr
            LEFT JOIN user u ON c.id_prof = u.id
            WHERE cr.id_user = :id_user AND c.id IS NOT NULL
            ORDER BY c.date_commentaire DESC
        ");
        $query->execute(['id_user' => $id_user]);
        $tous_commentaires = $query->fetchAll();
        
        // Organiser les commentaires par compte rendu
        $commentaires_par_cr = [];
        foreach ($tous_commentaires as $com) {
            if (!isset($commentaires_par_cr[$com['id']])) {
                $commentaires_par_cr[$com['id']] = [
                    'sujet' => $com['sujet'],
                    'commentaires' => []
                ];
            }
            $commentaires_par_cr[$com['id']]['commentaires'][] = [
                'commentaire' => $com['commentaire'],
                'date_commentaire' => $com['date_commentaire'],
                'nom' => $com['nom'],
                'prenom' => $com['prenom']
            ];
        }
    } else { // Professeur
        // Si c'est un professeur, on le redirige vers la liste des comptes rendus des élèves
        header('Location: liste_comptes_rendus_prof.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commentaires - Espace Élève</title>
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
        
        .comments-container {
            max-width: 1000px;
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
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            background: white;
            margin-bottom: 2rem;
        }
        
        .comment-section {
            background-color: #f8fafc;
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
        }
        
        .comment-item {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            border: 1px solid #e5e7eb;
        }
        
        .btn-custom {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .report-card {
            background: white;
            border-radius: 12px;
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
        
        .report-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }
        
        .footer {
            margin-top: 3rem;
            padding: 1rem 0;
            background-color: var(--secondary-color);
            text-align: center;
            color: #6c757d;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="accueil_eleve.php">
            <i class="fas fa-user-graduate me-2"></i>Espace Élève
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="accueil_eleve.php">
                        <i class="fas fa-home me-1"></i>Accueil
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="liste_comptes_rendus.php">
                        <i class="fas fa-list-alt me-1"></i>Comptes rendus
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="commentaires.php">
                        <i class="fas fa-comments me-1"></i>Commentaires
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="perso.php">
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

<div class="comments-container">
    <!-- En-tête -->
    <div class="welcome-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <?php if ($mode == "single"): ?>
                    <h1 class="h3 mb-1">Commentaires du compte rendu</h1>
                    <p class="text-muted mb-0"><?= htmlspecialchars($sujet_cr) ?></p>
                <?php else: ?>
                    <h1 class="h3 mb-1">Tous mes commentaires</h1>
                    <p class="text-muted mb-0">Commentaires reçus sur vos comptes rendus</p>
                <?php endif; ?>
            </div>
            <a href="liste_comptes_rendus.php" class="btn btn-secondary btn-custom">
                <i class="fas fa-arrow-left"></i> Retour aux comptes rendus
            </a>
        </div>
    </div>
    
    <?php if ($mode == "single"): ?>
        <!-- Affichage des commentaires d'un compte rendu spécifique -->
        <div class="card">
            <div class="card-body">
                <?php if (empty($commentaires)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-comment-slash fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Aucun commentaire pour ce compte rendu.</p>
                    </div>
                <?php else: ?>
                    <div class="comment-section">
                        <h6 class="mb-3">
                            <i class="fas fa-comments me-2"></i>Commentaires (<?= count($commentaires) ?>)
                        </h6>
                        <?php foreach ($commentaires as $commentaire): ?>
                            <div class="comment-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>
                                            <i class="fas fa-user-tie me-1"></i>
                                            <?= htmlspecialchars($commentaire['prenom'] . ' ' . $commentaire['nom']) ?>
                                        </strong>
                                        <div class="mt-1">
                                            <?= nl2br(htmlspecialchars($commentaire['commentaire'])) ?>
                                        </div>
                                    </div>
                                    <small class="text-muted">
                                        <?= date('d/m/Y à H:i', strtotime($commentaire['date_commentaire'])) ?>
                                    </small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Affichage du formulaire pour ajouter un commentaire uniquement pour le professeur -->
                <?php if ($role == 1): ?>
                    <div class="mt-4">
                        <h6 class="mb-3">
                            <i class="fas fa-comment-plus me-2"></i>Ajouter un commentaire
                        </h6>
                        <form method="POST">
                            <div class="mb-3">
                                <textarea name="commentaire" class="form-control" rows="4" 
                                          placeholder="Votre commentaire..." required></textarea>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary btn-custom">
                                    <i class="fas fa-paper-plane me-2"></i>Envoyer
                                </button>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <!-- Affichage de tous les commentaires organisés par compte rendu -->
        <?php if (empty($commentaires_par_cr)): ?>
            <div class="text-center py-5">
                <i class="fas fa-comment-slash fa-3x text-muted mb-3"></i>
                <p class="text-muted">Vous n'avez reçu aucun commentaire sur vos comptes rendus.</p>
                <a href="liste_comptes_rendus.php" class="btn btn-primary btn-custom mt-3">
                    <i class="fas fa-file-alt me-2"></i>Voir mes comptes rendus
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($commentaires_par_cr as $id_cr => $data): ?>
                <div class="report-card">
                    <div class="report-header">
                        <div class="report-title">
                            <i class="fas fa-file-alt me-2"></i><?= htmlspecialchars($data['sujet']) ?>
                        </div>
                    </div>
                    <div class="report-body">
                        <div class="comment-section">
                            <h6 class="mb-3">
                                <i class="fas fa-comments me-2"></i>Commentaires (<?= count($data['commentaires']) ?>)
                            </h6>
                            <?php foreach ($data['commentaires'] as $commentaire): ?>
                                <div class="comment-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong>
                                                <i class="fas fa-user-tie me-1"></i>
                                                <?= htmlspecialchars($commentaire['prenom'] . ' ' . $commentaire['nom']) ?>
                                            </strong>
                                            <div class="mt-1">
                                                <?= nl2br(htmlspecialchars($commentaire['commentaire'])) ?>
                                            </div>
                                        </div>
                                        <small class="text-muted">
                                            <?= date('d/m/Y à H:i', strtotime($commentaire['date_commentaire'])) ?>
                                        </small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="text-end mt-3">
                            <a href="commentaires.php?id=<?= $id_cr ?>" class="btn btn-primary btn-custom">
                                <i class="fas fa-eye me-2"></i>Voir le détail
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <p class="mb-0">&copy; <?php echo date('Y'); ?> - Espace de gestion des stages</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>

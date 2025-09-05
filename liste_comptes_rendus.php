<?php
require_once '_conf.php';
session_start();

// Vérification si l'utilisateur est connecté
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

// Vérifier si une date a été sélectionnée
if (isset($_GET['date']) && $_GET['date'] !== '') {
    $selection_date = $_GET['date'];
    $date_lisible = date('d F', strtotime($selection_date));
    $stmt = $pdo->prepare("SELECT * FROM CR WHERE id_user = :id_user AND DATE(date_creation) = :selection_date");
    $stmt->execute(['id_user' => $id_user, 'selection_date' => $selection_date]);
} else {
    $selection_date = null;
    $date_lisible = 'Tous les comptes rendus';
    $stmt = $pdo->prepare("SELECT * FROM CR WHERE id_user = :id_user");
    $stmt->execute(['id_user' => $id_user]);
}

// Récupérer tous les comptes rendus
$comptes_rendus = $stmt->fetchAll();

// Afficher un message de succès après suppression
if (isset($_GET['message'])) {
    $message = htmlspecialchars($_GET['message']);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Comptes Rendus - Espace Élève</title>
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
        
        .card-header {
            background: var(--primary-color);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 15px 15px 0 0;
            border: none;
            font-weight: 500;
        }
        
        .card-body {
            padding: 2rem;
        }
        
        .filter-section {
            background-color: white;
            padding: 1.5rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        .report-item {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border: 1px solid #e5e7eb;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .report-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        }
        
        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }
        
        .report-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }
        
        .report-date {
            color: #6b7280;
            font-size: 0.9rem;
        }
        
        .report-actions {
            display: flex;
            gap: 0.5rem;
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
        
        .success-message {
            background-color: #ecfdf5;
            color: #065f46;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
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
                    <a class="nav-link active" href="liste_comptes_rendus.php">
                        <i class="fas fa-list-alt me-1"></i>Comptes rendus
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

<div class="reports-container">
    <!-- En-tête -->
    <div class="welcome-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1">Mes Comptes Rendus</h1>
                <p class="text-muted mb-0"><?= htmlspecialchars($date_lisible) ?></p>
            </div>
            <a href="creer_modifier_compte_rendu.php" class="btn btn-primary btn-custom">
                <i class="fas fa-plus"></i>Nouveau compte rendu
            </a>
        </div>
    </div>
    
    <!-- Message de succès -->
    <?php if (isset($message)): ?>
        <div class="success-message">
            <i class="fas fa-check-circle"></i>
            <?= $message ?>
        </div>
    <?php endif; ?>
    
    <!-- Filtre par date -->
    <div class="filter-section">
        <form method="get" action="liste_comptes_rendus.php" class="row align-items-end g-3">
            <div class="col-md-8">
                <label for="selection_date" class="form-label">
                    <i class="fas fa-calendar me-2"></i>Filtrer par date
                </label>
                <input type="date" name="date" class="form-control" id="selection_date" 
                       value="<?= htmlspecialchars($selection_date) ?>">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-custom flex-grow-1">
                    <i class="fas fa-filter me-2"></i>Filtrer
                </button>
                <?php if ($selection_date): ?>
                    <a href="liste_comptes_rendus.php" class="btn btn-secondary btn-custom">
                        <i class="fas fa-times me-2"></i>Réinitialiser
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
    
    <!-- Liste des comptes rendus -->
    <?php if (count($comptes_rendus) > 0): ?>
        <?php foreach ($comptes_rendus as $cr): ?>
            <div class="report-item">
                <div class="report-header">
                    <div>
                        <div class="report-title">
                            <i class="fas fa-file-alt me-2"></i><?= htmlspecialchars($cr['sujet']) ?>
                        </div>
                        <div class="report-date">
                            <i class="fas fa-clock me-1"></i>Créé le <?= date('d/m/Y à H:i', strtotime($cr['date_creation'])) ?>
                            <?php if ($cr['date_modif']): ?>
                                <br>
                                <i class="fas fa-edit me-1"></i>Modifié le <?= date('d/m/Y à H:i', strtotime($cr['date_modif'])) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="report-actions">
                        <a href="creer_modifier_compte_rendu.php?id=<?= $cr['id'] ?>" 
                           class="btn btn-primary btn-custom">
                            <i class="fas fa-edit"></i>Modifier
                        </a>
                        <a href="supprimer_compte_rendu.php?id=<?= $cr['id'] ?>" 
                           class="btn btn-danger btn-custom"
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce compte rendu ?');">
                            <i class="fas fa-trash"></i>Supprimer
                        </a>
                    </div>
                </div>
                
                <!-- Commentaires -->
                <?php
                $stmt_commentaires = $pdo->prepare("
                    SELECT commentaires.*, user.nom, user.prenom 
                    FROM commentaires 
                    JOIN user ON commentaires.id_prof = user.id 
                    WHERE commentaires.id_cr = :id_cr
                    ORDER BY commentaires.date_commentaire DESC
                ");
                $stmt_commentaires->execute(['id_cr' => $cr['id']]);
                $commentaires = $stmt_commentaires->fetchAll();
                
                if (count($commentaires) > 0):
                ?>
                    <div class="comment-section">
                        <h6 class="mb-3">
                            <i class="fas fa-comments me-2"></i>Commentaires
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
                                            <?= htmlspecialchars($commentaire['commentaire']) ?>
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
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
            <p class="text-muted">Aucun compte rendu trouvé pour cette période.</p>
            <a href="creer_modifier_compte_rendu.php" class="btn btn-primary btn-custom mt-3">
                <i class="fas fa-plus me-2"></i>Créer mon premier compte rendu
            </a>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
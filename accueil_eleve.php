<?php
session_start();

if (!isset($_SESSION['login']) || $_SESSION['role'] != 2) {
    header('Location: index.php');
    exit();
}

include '_conf.php';

// Connexion à la base de données
$connexion = mysqli_connect($serveurBDD, $userBDD, $mdpBDD, $nomBDD);
if (!$connexion) {
    die("Connexion à la base de données échouée");
}

$id_user = $_SESSION['id_user'];

// Récupérer les informations de l'utilisateur
$query_user = "SELECT nom, prenom, adresse FROM user WHERE id = $id_user";
$result_user = mysqli_query($connexion, $query_user);

if ($result_user && $row_user = mysqli_fetch_assoc($result_user)) {
    $nom = $row_user['nom'];
    $prenom = $row_user['prenom'];
    $adresse = $row_user['adresse'];
} else {
    $nom = '';
    $prenom = isset($_SESSION['prenom']) ? $_SESSION['prenom'] : 'utilisateur';
    $adresse = null;
}

// Récupérer les informations du stage de l'utilisateur
$query_stage = "SELECT dateD, dateF FROM stage WHERE id_user = $id_user LIMIT 1";
$result_stage = mysqli_query($connexion, $query_stage);

$message_bienvenue = '';
$stage_info = null;

if ($result_stage && $row_stage = mysqli_fetch_assoc($result_stage)) {
    $stage_info = $row_stage;
    $date_debut_stage = $row_stage['dateD'];
    $date_fin_stage = $row_stage['dateF'];
    
    if ($date_debut_stage && $date_fin_stage) {
        $aujourd_hui = new DateTime();
        $debut_stage = new DateTime($date_debut_stage);
        $fin_stage = new DateTime($date_fin_stage);
        
        // Vérifier si on est dans la période de stage ou après
        if ($aujourd_hui >= $debut_stage) {
            // Vérifier s'il y a eu des comptes rendus pendant la période de stage
            $query_cr = "SELECT MAX(date_creation) as derniere_saisie, COUNT(*) as nb_cr
                         FROM CR 
                         WHERE id_user = $id_user 
                         AND DATE(date_creation) BETWEEN '$date_debut_stage' AND '$date_fin_stage'";
            $result_cr = mysqli_query($connexion, $query_cr);
            
            if ($result_cr && $row_cr = mysqli_fetch_assoc($result_cr)) {
                $derniere_saisie = $row_cr['derniere_saisie'];
                $nb_cr = $row_cr['nb_cr'];
                
                if ($nb_cr == 0 || empty($derniere_saisie)) {
                    // Aucun compte rendu pendant le stage
                    $timestamp_debut = $debut_stage->getTimestamp();
                    $timestamp_aujourd_hui = $aujourd_hui->getTimestamp();
                    $jours_sans_cr = floor(($timestamp_aujourd_hui - $timestamp_debut) / (24 * 3600));
                    
                    if ($jours_sans_cr > 0) {
                        $message_bienvenue = "Enfin de retour M. " . htmlspecialchars($nom) . ", vous n'avez saisi aucun CR depuis " . $jours_sans_cr . " jours";
                    } else {
                        $message_bienvenue = "Bienvenue M. " . htmlspecialchars($nom);
                    }
                } else {
                    // Il y a eu au moins un compte rendu
                    $date_derniere_saisie = new DateTime($derniere_saisie);
                    
                    // Calcul plus précis des jours
                    $timestamp_derniere = $date_derniere_saisie->getTimestamp();
                    $timestamp_aujourd_hui = $aujourd_hui->getTimestamp();
                    $jours_depuis_derniere_saisie = floor(($timestamp_aujourd_hui - $timestamp_derniere) / (24 * 3600));
                    
                    // Si plus de 1 jour sans saisie
                    if ($jours_depuis_derniere_saisie > 1) {
                        $message_bienvenue = "Enfin de retour M. " . htmlspecialchars($nom) . ", vous n'avez saisi aucun CR depuis " . $jours_depuis_derniere_saisie . " jours";
                    } else {
                        $message_bienvenue = "Bienvenue M. " . htmlspecialchars($nom);
                    }
                }
            } else {
                $message_bienvenue = "Bienvenue M. " . htmlspecialchars($nom);
            }
        } else {
            $message_bienvenue = "Bienvenue M. " . htmlspecialchars($nom);
        }
    } else {
        $message_bienvenue = "Bienvenue M. " . htmlspecialchars($nom);
    }
} else {
    // Pas de stage défini - mais vérifier s'il y a des comptes rendus
    $query_cr_total = "SELECT COUNT(*) as total_cr FROM CR WHERE id_user = $id_user";
    $result_cr_total = mysqli_query($connexion, $query_cr_total);
    
    if ($result_cr_total && $row_cr_total = mysqli_fetch_assoc($result_cr_total)) {
        $total_cr = $row_cr_total['total_cr'];
        
        if ($total_cr == 0) {
            // Aucun compte rendu du tout
            $message_bienvenue = "Enfin de retour M. " . htmlspecialchars($nom) . ", vous n'avez saisi aucun CR";
        } else {
            // Il y a des comptes rendus
            $message_bienvenue = "Bienvenue M. " . htmlspecialchars($nom);
        }
    } else {
        $message_bienvenue = "Bienvenue M. " . htmlspecialchars($nom);
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page d'accueil - Élève</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3b71ca;
            --primary-hover: #2c5eaa;
            --secondary-color: #f0f2f5;
            --accent-color: #14a44d;
            --danger-color: #dc4c64;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 56px;
            min-height: 100vh;
        }
        
        .navbar-brand {
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .welcome-header {
            background-color: white;
            padding: 2rem 0;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 2rem;
        }
        
        .welcome-text {
            font-size: 1.8rem;
            font-weight: 500;
            color: #333;
        }
        
        .menu-card {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            margin-bottom: 1.5rem;
            border-radius: 12px;
            overflow: hidden;
        }
        
        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.15);
        }
        
        .card-header {
            background-color: var(--primary-color);
            color: white;
            font-weight: 500;
            padding: 1rem;
            text-align: center;
        }
        
        .menu-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }
        
        .btn-custom {
            width: 100%;
            padding: 0.75rem;
            font-weight: 500;
            letter-spacing: 0.5px;
            border-radius: 8px;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }
        
        .alert {
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
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

<!-- Navbar fixe -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
    <div class="container">
        <a class="navbar-brand" href="accueil_eleve.php">Espace Élève</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="accueil_eleve.php"><i class="fas fa-home"></i> Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="perso.php"><i class="fas fa-user"></i> Profil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="deconnexion.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Bandeau de bienvenue -->
<div class="welcome-header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="welcome-text"><?php echo $message_bienvenue; ?> !</h1>
            <div class="d-none d-md-block">
                <span class="badge bg-primary p-2"><i class="fas fa-user-graduate"></i> Espace Élève</span>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <!-- Alerte adresse manquante -->
    <?php if (empty($adresse)): ?>
        <div class="alert alert-danger d-flex align-items-center" role="alert">
            <i class="fas fa-exclamation-triangle fs-4 me-3"></i>
            <div>
                <strong>Attention !</strong> Votre adresse n'est pas saisie. Veuillez la <a href="perso.php" class="alert-link">saisir au plus vite</a>.
            </div>
        </div>
    <?php endif; ?>

    <!-- Menu principal avec cartes -->
    <h2 class="mb-4 text-center">Menu des options</h2>
    
    <div class="row g-4">
        <!-- Carte 1 : Liste des comptes rendus -->
        <div class="col-md-6 col-lg-4">
            <div class="menu-card card h-100">
                <div class="card-body text-center p-4">
                    <i class="fas fa-list-alt menu-icon"></i>
                    <h4 class="card-title">Comptes rendus</h4>
                    <p class="card-text">Consultez la liste de vos comptes rendus de stage.</p>
                    <a href="liste_comptes_rendus.php" class="btn btn-primary btn-custom mt-3">
                        <i class="fas fa-eye me-2"></i> Voir mes comptes rendus
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Carte 2 : Créer un compte rendu -->
        <div class="col-md-6 col-lg-4">
            <div class="menu-card card h-100">
                <div class="card-body text-center p-4">
                    <i class="fas fa-edit menu-icon"></i>
                    <h4 class="card-title">Nouveau rapport</h4>
                    <p class="card-text">Créez un nouveau compte rendu de stage.</p>
                    <a href="creer_modifier_compte_rendu.php" class="btn btn-primary btn-custom mt-3">
                        <i class="fas fa-plus me-2"></i> Créer un compte rendu
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Carte 3 : Commentaires -->
        <div class="col-md-6 col-lg-4">
            <div class="menu-card card h-100">
                <div class="card-body text-center p-4">
                    <i class="fas fa-comments menu-icon"></i>
                    <h4 class="card-title">Commentaires</h4>
                    <p class="card-text">Consultez les commentaires sur vos rapports.</p>
                    <a href="commentaires.php" class="btn btn-primary btn-custom mt-3">
                        <i class="fas fa-comment me-2"></i> Voir les commentaires
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Carte 4 : Informations personnelles -->
        <div class="col-md-6 col-lg-4">
            <div class="menu-card card h-100">
                <div class="card-body text-center p-4">
                    <i class="fas fa-user-cog menu-icon"></i>
                    <h4 class="card-title">Mon profil</h4>
                    <p class="card-text">Gérez vos informations personnelles.</p>
                    <a href="perso.php" class="btn btn-primary btn-custom mt-3">
                        <i class="fas fa-cog me-2"></i> Modifier mon profil
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Carte 5 : Informations du stage -->
        <div class="col-md-6 col-lg-4">
            <div class="menu-card card h-100">
                <div class="card-body text-center p-4">
                    <i class="fas fa-briefcase menu-icon"></i>
                    <h4 class="card-title">Mon stage</h4>
                    <p class="card-text">Consultez les informations sur votre stage.</p>
                    <a href="information_stage.php" class="btn btn-primary btn-custom mt-3">
                        <i class="fas fa-info-circle me-2"></i> Détails du stage
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Carte 6 : Déconnexion -->
        <div class="col-md-6 col-lg-4">
            <div class="menu-card card h-100">
                <div class="card-body text-center p-4">
                    <i class="fas fa-sign-out-alt menu-icon text-danger"></i>
                    <h4 class="card-title">Déconnexion</h4>
                    <p class="card-text">Quitter votre session en toute sécurité.</p>
                    <form method="POST" action="deconnexion.php">
                        <button type="submit" class="btn btn-danger btn-custom mt-3">
                            <i class="fas fa-power-off me-2"></i> Se déconnecter
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="footer mt-5">
    <div class="container">
        <p class="mb-0">&copy; <?php echo date('Y'); ?> - Espace de gestion des stages</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>
</html>

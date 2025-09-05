<?php
session_start();

// Vérifie si l'utilisateur est un professeur
if (!isset($_SESSION['login']) || $_SESSION['role'] != 1) {
    header('Location: index.php');
    exit();
}

$prenom = isset($_SESSION['prenom']) ? $_SESSION['prenom'] : 'utilisateur';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page d'accueil - Espace Professeur</title>
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
            background-color: var(--secondary-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 56px;
            min-height: 100vh;
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
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="accueil_prof.php">
            <i class="fas fa-chalkboard-teacher me-2"></i>Espace Professeur
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="accueil_prof.php">
                        <i class="fas fa-home me-1"></i>Accueil
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="liste_comptes_rendus_prof.php">
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

<!-- Bandeau de bienvenue -->
<div class="welcome-header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="welcome-text">Bienvenue, <?php echo htmlspecialchars($prenom); ?> !</h1>
            <div class="d-none d-md-block">
                <span class="badge bg-primary p-2">
                    <i class="fas fa-chalkboard-teacher"></i> Espace Professeur
                </span>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <!-- Menu principal avec cartes -->
    <h2 class="mb-4 text-center">Menu des options</h2>
    
    <div class="row g-4">
        <!-- Carte 1 : Liste des comptes rendus -->
        <div class="col-md-6 col-lg-4">
            <div class="menu-card card h-100">
                <div class="card-body text-center p-4">
                    <i class="fas fa-list-alt menu-icon"></i>
                    <h4 class="card-title">Comptes rendus</h4>
                    <p class="card-text">Consultez et commentez les comptes rendus des élèves.</p>
                    <a href="liste_comptes_rendus_prof.php" class="btn btn-primary btn-custom mt-3">
                        <i class="fas fa-eye me-2"></i>Voir les comptes rendus
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Carte 2 : Liste des élèves -->
        <div class="col-md-6 col-lg-4">
            <div class="menu-card card h-100">
                <div class="card-body text-center p-4">
                    <i class="fas fa-user-graduate menu-icon"></i>
                    <h4 class="card-title">Liste des élèves</h4>
                    <p class="card-text">Consultez la liste des élèves et leurs informations.</p>
                    <a href="liste_eleves_prof.php" class="btn btn-primary btn-custom mt-3">
                        <i class="fas fa-users me-2"></i>Voir les élèves
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Carte 3 : Profil -->
        <div class="col-md-6 col-lg-4">
            <div class="menu-card card h-100">
                <div class="card-body text-center p-4">
                    <i class="fas fa-user-cog menu-icon"></i>
                    <h4 class="card-title">Mon profil</h4>
                    <p class="card-text">Gérez vos informations personnelles.</p>
                    <a href="perso_prof.php" class="btn btn-primary btn-custom mt-3">
                        <i class="fas fa-cog me-2"></i>Modifier mon profil
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Carte 4 : Déconnexion -->
        <div class="col-md-6 col-lg-4">
            <div class="menu-card card h-100">
                <div class="card-body text-center p-4">
                    <i class="fas fa-sign-out-alt menu-icon text-danger"></i>
                    <h4 class="card-title">Déconnexion</h4>
                    <p class="card-text">Quitter votre session en toute sécurité.</p>
                    <form method="POST" action="deconnexion.php">
                        <button type="submit" class="btn btn-danger btn-custom mt-3">
                            <i class="fas fa-power-off me-2"></i>Se déconnecter
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
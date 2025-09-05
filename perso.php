<?php
session_start();

if (!isset($_SESSION['login'])) {
    header('Location: index.php');
    exit();
}

include '_conf.php';
$connexion = mysqli_connect($serveurBDD, $userBDD, $mdpBDD, $nomBDD);
if (!$connexion) {
    die("Connexion à la base de données échouée");
}

$login = $_SESSION['login'];
$query = "SELECT nom, prenom, dateN, email, tel, adresse, code_postal, ville FROM user WHERE login = '$login'";
$result = mysqli_query($connexion, $query);
$user = mysqli_fetch_assoc($result);

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $newEmail = mysqli_real_escape_string($connexion, $_POST['email']);
    $newTel = mysqli_real_escape_string($connexion, $_POST['tel']);
    $newAdresse = mysqli_real_escape_string($connexion, $_POST['adresse']);
    $newCodePostal = mysqli_real_escape_string($connexion, $_POST['code_postal']);
    $newVille = mysqli_real_escape_string($connexion, $_POST['ville']);

    $updateQuery = "
        UPDATE user 
        SET email = '$newEmail', tel = '$newTel', 
            adresse = '$newAdresse', code_postal = '$newCodePostal', ville = '$newVille'
        WHERE login = '$login'
    ";
    if (mysqli_query($connexion, $updateQuery)) {
        $message = "Informations mises à jour avec succès.";
        $user['email'] = $newEmail;
        $user['tel'] = $newTel;
        $user['adresse'] = $newAdresse;
        $user['code_postal'] = $newCodePostal;
        $user['ville'] = $newVille;
    } else {
        $message = "Erreur lors de la mise à jour des informations.";
    }

    if (!empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];

        if ($newPassword === $confirmPassword) {
            $newPasswordHashed = md5($newPassword);
            $updatePasswordQuery = "UPDATE user SET mdp = '$newPasswordHashed' WHERE login = '$login'";
            if (mysqli_query($connexion, $updatePasswordQuery)) {
                $message .= " Mot de passe mis à jour avec succès.";
            } else {
                $message .= " Erreur lors de la mise à jour du mot de passe.";
            }
        } else {
            $message .= " Les mots de passe ne correspondent pas.";
        }
    }
}

mysqli_close($connexion);
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - Espace Élève</title>
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
        
        .profile-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .welcome-header {
            background-color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            text-align: center;
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
        
        .form-label {
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .form-control {
            border-radius: 8px;
            padding: 0.75rem 1rem;
            border: 1px solid #ced4da;
            margin-bottom: 1rem;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(59, 113, 202, 0.25);
        }
        
        .form-control[readonly] {
            background-color: #f8f9fa;
            cursor: not-allowed;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            letter-spacing: 0.5px;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }
        
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
        }
        
        .section-title {
            color: #374151;
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            display: flex;
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
        
        .error-message {
            background-color: #fde8e8;
            color: #991b1b;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .address-section {
            background-color: #f8fafc;
            padding: 1.5rem;
            border-radius: 8px;
            margin: 1rem 0;
            border: 1px solid #e5e7eb;
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
                    <a class="nav-link active" href="perso.php">
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

<div class="profile-container">
    <!-- En-tête de bienvenue -->
    <div class="welcome-header">
        <h1 class="h3 mb-0">Mon Profil</h1>
        <p class="text-muted mb-0">Gérez vos informations personnelles</p>
    </div>
    
    <?php if ($message): ?>
        <div class="<?php echo strpos($message, 'succès') !== false ? 'success-message' : 'error-message'; ?>">
            <i class="<?php echo strpos($message, 'succès') !== false ? 'fas fa-check-circle' : 'fas fa-exclamation-circle'; ?>"></i>
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
    
    <!-- Informations personnelles -->
    <div class="card">
        <div class="card-header">
            <i class="fas fa-user-circle me-2"></i>Informations personnelles
        </div>
        <div class="card-body">
            <form method="POST" action="perso.php">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="prenom" class="form-label">
                                <i class="fas fa-user me-2"></i>Prénom
                            </label>
                            <input type="text" class="form-control" id="prenom" name="prenom" 
                                   value="<?php echo htmlspecialchars($user['prenom']); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nom" class="form-label">
                                <i class="fas fa-user me-2"></i>Nom
                            </label>
                            <input type="text" class="form-control" id="nom" name="nom" 
                                   value="<?php echo htmlspecialchars($user['nom']); ?>" readonly>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="dateN" class="form-label">
                        <i class="fas fa-calendar me-2"></i>Date de naissance
                    </label>
                    <input type="date" class="form-control" id="dateN" name="dateN" 
                           value="<?php echo htmlspecialchars($user['dateN']); ?>" readonly>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-2"></i>Email
                            </label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($user['email']); ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="tel" class="form-label">
                                <i class="fas fa-phone me-2"></i>Téléphone
                            </label>
                            <input type="tel" class="form-control" id="tel" name="tel" 
                                   value="<?php echo htmlspecialchars($user['tel']); ?>">
                        </div>
                    </div>
                </div>
                
                <div class="section-title mt-4">
                    <i class="fas fa-map-marker-alt me-2"></i>Adresse
                </div>
                
                <div class="address-section">
                    <div class="mb-3">
                        <label for="adresse" class="form-label">
                            <i class="fas fa-home me-2"></i>Adresse complète
                        </label>
                        <input type="text" class="form-control" id="adresse" name="adresse" 
                               value="<?php echo htmlspecialchars($user['adresse'] ?? ''); ?>">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="code_postal" class="form-label">
                                    <i class="fas fa-map-pin me-2"></i>Code Postal
                                </label>
                                <input type="text" class="form-control" id="code_postal" name="code_postal" 
                                       value="<?php echo htmlspecialchars($user['code_postal'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ville" class="form-label">
                                    <i class="fas fa-city me-2"></i>Ville
                                </label>
                                <input type="text" class="form-control" id="ville" name="ville" 
                                       value="<?php echo htmlspecialchars($user['ville'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="section-title mt-4">
                    <i class="fas fa-lock me-2"></i>Changer le mot de passe
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Nouveau mot de passe</label>
                            <input type="password" class="form-control" id="new_password" name="new_password">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                        </div>
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <a href="accueil_eleve.php" class="btn btn-secondary me-md-2">
                        <i class="fas fa-arrow-left me-2"></i>Retour
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>

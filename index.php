<?php
session_start();

if (isset($_SESSION['login'])) {
    if ($_SESSION['role'] == 1) {
        header('Location: accueil_prof.php');
        exit();
    } elseif ($_SESSION['role'] == 2) {
        header('Location: accueil_eleve.php');
        exit();
    }
}

include '_conf.php';

$connexion = mysqli_connect($serveurBDD, $userBDD, $mdpBDD, $nomBDD);
if (!$connexion) {
    die("Connexion à la base de données échouée");
}

// Connexion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_connexion'])) {
    $login = $_POST['login'];
    $password = md5($_POST['mdp']);

    $query = "SELECT * FROM user WHERE login = '$login' AND mdp = '$password'";
    $result = mysqli_query($connexion, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['login'] = $user['login'];
        $_SESSION['role'] = $user['id_statut'];
        $_SESSION['prenom'] = $user['prenom'];
        $_SESSION['id_user'] = $user['id'];

        if ($_SESSION['role'] == 1) {
            header('Location: accueil_prof.php');
        } elseif ($_SESSION['role'] == 2) {
            header('Location: accueil_eleve.php');
        }
        exit();
    } else {
        $error = "Login ou mot de passe incorrect.";
    }
}

mysqli_close($connexion);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Espace de gestion des stages</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            max-width: 450px;
            width: 100%;
            padding: 2rem;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            background: white;
        }
        
        .card-header {
            background: var(--primary-color);
            color: white;
            text-align: center;
            padding: 1.5rem;
            border-radius: 15px 15px 0 0;
            border: none;
        }
        
        .card-header .fa-user-circle {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .card-body {
            padding: 2rem;
        }
        
        .form-floating {
            margin-bottom: 1rem;
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
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.75rem;
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
            padding: 0.75rem;
            border-radius: 8px;
        }
        
        .error-message {
            background-color: #fde8e8;
            color: #991b1b;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .divider {
            height: 1px;
            background-color: #e5e7eb;
            margin: 1.5rem 0;
        }
        
        .forgot-password {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }
        
        .forgot-password:hover {
            color: var(--primary-hover);
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="login-container">
    <div class="card">
        <div class="card-header">
            <i class="fas fa-user-circle"></i>
            <h2 class="mb-0">Connexion</h2>
            <p class="mb-0">Espace de gestion des stages</p>
        </div>
        
        <div class="card-body">
            <?php if (isset($error)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="index.php">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="login" name="login" placeholder="Votre identifiant" required>
                    <label for="login"><i class="fas fa-user me-2"></i>Identifiant</label>
                </div>
                
                <div class="form-floating mb-4">
                    <input type="password" class="form-control" id="mdp" name="mdp" placeholder="Votre mot de passe" required>
                    <label for="mdp"><i class="fas fa-lock me-2"></i>Mot de passe</label>
                </div>
                
                <button type="submit" name="send_connexion" class="btn btn-primary w-100 mb-3">
                    <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                </button>
                
                <div class="text-center">
                    <a href="oubli.php" class="forgot-password">
                        <i class="fas fa-key me-1"></i>Mot de passe oublié ?
                    </a>
                </div>
            </form>
            
            <div class="divider"></div>
            
            <div class="text-center mb-3">
                <p class="text-muted mb-0">Pas encore de compte ?</p>
            </div>
            
            <a href="inscription.php" class="btn btn-secondary w-100">
                <i class="fas fa-user-plus me-2"></i>Créer un compte
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
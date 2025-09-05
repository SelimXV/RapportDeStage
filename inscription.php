<?php
include '_conf.php';

$connexion = mysqli_connect($serveurBDD, $userBDD, $mdpBDD, $nomBDD);
if (!$connexion) {
    die("Connexion à la base de données échouée");
}

// Inscription
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_inscription'])) {
    $nom = mysqli_real_escape_string($connexion, $_POST['nom']);
    $prenom = mysqli_real_escape_string($connexion, $_POST['prenom']);
    $email = mysqli_real_escape_string($connexion, $_POST['email']);
    $login = mysqli_real_escape_string($connexion, $_POST['login']);
    $password = md5(mysqli_real_escape_string($connexion, $_POST['mdp']));
    $dateN = mysqli_real_escape_string($connexion, $_POST['dateN']);
    $id_statut = 2; // Par défaut, élèves

    // Vérification des doublons
    $checkQuery = "SELECT * FROM user WHERE login = '$login' OR email = '$email'";
    $checkResult = mysqli_query($connexion, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        $error = "Un compte avec cet email ou login existe déjà.";
    } else {
        // Insertion
        $query = "INSERT INTO user (nom, prenom, dateN, email, login, mdp, id_statut) VALUES ('$nom', '$prenom', '$dateN', '$email', '$login', '$password', $id_statut)";
        if (mysqli_query($connexion, $query)) {
            $success = "Compte créé avec succès ! <a href='index.php'>Connectez-vous ici</a>";
        } else {
            $error = "Erreur lors de la création du compte : " . mysqli_error($connexion);
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
    <title>Inscription - Espace de gestion des stages</title>
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
            padding: 2rem 0;
        }
        
        .register-container {
            max-width: 600px;
            width: 100%;
            padding: 1rem;
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
        
        .card-header .fa-user-plus {
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
        
        .success-message {
            background-color: #ecfdf5;
            color: #065f46;
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
    </style>
</head>
<body>
<div class="register-container">
    <div class="card">
        <div class="card-header">
            <i class="fas fa-user-plus"></i>
            <h2 class="mb-0">Créer un compte</h2>
            <p class="mb-0">Espace de gestion des stages</p>
        </div>
        
        <div class="card-body">
            <?php if (isset($error)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($success)): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="inscription.php" class="row g-3">
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="nom" name="nom" placeholder="Votre nom" required>
                        <label for="nom"><i class="fas fa-user me-2"></i>Nom</label>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="prenom" name="prenom" placeholder="Votre prénom" required>
                        <label for="prenom"><i class="fas fa-user me-2"></i>Prénom</label>
                    </div>
                </div>
                
                <div class="col-12">
                    <div class="form-floating">
                        <input type="email" class="form-control" id="email" name="email" placeholder="Votre email" required>
                        <label for="email"><i class="fas fa-envelope me-2"></i>Email</label>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="login" name="login" placeholder="Votre identifiant" required>
                        <label for="login"><i class="fas fa-user-circle me-2"></i>Identifiant</label>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="password" class="form-control" id="mdp" name="mdp" placeholder="Votre mot de passe" required>
                        <label for="mdp"><i class="fas fa-lock me-2"></i>Mot de passe</label>
                    </div>
                </div>
                
                <div class="col-12">
                    <div class="form-floating">
                        <input type="date" class="form-control" id="dateN" name="dateN" required>
                        <label for="dateN"><i class="fas fa-calendar me-2"></i>Date de naissance</label>
                    </div>
                </div>
                
                <div class="col-12">
                    <button type="submit" name="send_inscription" class="btn btn-primary w-100">
                        <i class="fas fa-user-plus me-2"></i>S'inscrire
                    </button>
                </div>
            </form>
            
            <div class="divider"></div>
            
            <a href="index.php" class="btn btn-secondary w-100">
                <i class="fas fa-arrow-left me-2"></i>Retour à la connexion
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
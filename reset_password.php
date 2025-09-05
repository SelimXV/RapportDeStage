<?php
include "_conf.php";

$message = ""; // Variable pour stocker les messages

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_password'])) {
    $token = $_GET['token'];
    $newPassword = md5($_POST['new_password']);

    if ($connexion = mysqli_connect($serveurBDD, $userBDD, $mdpBDD, $nomBDD)) {
        $query = "SELECT * FROM user WHERE reset_token = '$token' AND token_expiry > NOW()";
        $result = mysqli_query($connexion, $query);

        if (mysqli_num_rows($result) == 1) {
            $updateQuery = "UPDATE user SET mdp = '$newPassword', reset_token = NULL, token_expiry = NULL WHERE reset_token = '$token'";
            if (mysqli_query($connexion, $updateQuery)) {
                $message = "Mot de passe réinitialisé avec succès.";
            } else {
                $message = "Erreur lors de la réinitialisation du mot de passe.";
            }
        } else {
            $message = "Le lien de réinitialisation est invalide ou a expiré.";
        }

        mysqli_close($connexion);
    }
} elseif (isset($_GET['token'])) {
    // Formulaire pour réinitialiser le mot de passe
    $form = true;
} else {
    $message = "Lien de réinitialisation invalide.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation du mot de passe - Espace de gestion des stages</title>
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
        
        .reset-container {
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
        
        .card-header .fa-lock {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .card-body {
            padding: 2rem;
        }
        
        .form-floating {
            margin-bottom: 1.5rem;
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
        
        .info-message {
            background-color: #eff6ff;
            color: #1e40af;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .divider {
            height: 1px;
            background-color: #e5e7eb;
            margin: 1.5rem 0;
        }
        
        .info-text {
            text-align: center;
            color: #6b7280;
            font-size: 0.95rem;
            margin-bottom: 1.5rem;
        }
        
        .password-requirements {
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 0.5rem;
        }
        
        .password-requirements ul {
            padding-left: 1.25rem;
            margin-bottom: 0;
        }
    </style>
</head>
<body>
<div class="reset-container">
    <div class="card">
        <div class="card-header">
            <i class="fas fa-lock"></i>
            <h2 class="mb-0">Réinitialisation du mot de passe</h2>
            <p class="mb-0">Créez votre nouveau mot de passe</p>
        </div>
        
        <div class="card-body">
            <?php if (!empty($message)): ?>
                <?php if (strpos($message, "succès") !== false): ?>
                    <div class="success-message">
                        <i class="fas fa-check-circle"></i>
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php elseif (strpos($message, "invalide") !== false || strpos($message, "Erreur") !== false): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php else: ?>
                    <div class="info-message">
                        <i class="fas fa-info-circle"></i>
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            
            <?php if (isset($form)): ?>
                <p class="info-text">
                    Veuillez créer un nouveau mot de passe sécurisé pour votre compte.
                </p>
                
                <form method="POST" action="">
                    <div class="form-floating mb-4">
                        <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Nouveau mot de passe" required>
                        <label for="new_password"><i class="fas fa-key me-2"></i>Nouveau mot de passe</label>
                        <div class="password-requirements mt-2">
                            <p class="mb-1">Votre mot de passe doit contenir :</p>
                            <ul>
                                <li>Au moins 8 caractères</li>
                                <li>Au moins une lettre majuscule</li>
                                <li>Au moins un chiffre</li>
                                <li>Au moins un caractère spécial</li>
                            </ul>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        <i class="fas fa-save me-2"></i>Réinitialiser le mot de passe
                    </button>
                </form>
            <?php endif; ?>
            
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

<?php
include "_conf.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    if ($connexion = mysqli_connect($serveurBDD, $userBDD, $mdpBDD, $nomBDD)) {
        $query = "SELECT * FROM user WHERE email = '$email'";
        $result = mysqli_query($connexion, $query);
        if (mysqli_num_rows($result) == 1) {

            $token = bin2hex(random_bytes(50));
            $expiry = date("Y-m-d H:i:s", strtotime("+15 minutes"));

            $updateQuery = "UPDATE user SET reset_token = '$token', token_expiry = '$expiry' WHERE email = '$email'";
            mysqli_query($connexion, $updateQuery);

            $resetLink = "https://www.sioslam.fr/selikhal/PROJET/AP/reset_password.php?token=$token";
            $subject = "Réinitialisation de mot de passe";
            $message = "Bonjour, cliquez sur le lien suivant pour réinitialiser votre mot de passe : $resetLink";

            // Utilisation d'un en-tête 'From' valide
            $headers = "From: noreply@sioslam.com\r\n"; // Remplacez par une adresse e-mail valide
            $headers .= "Reply-To: noreply@sioslam.com\r\n"; // Optionnel
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n"; // Optionnel

            // Essayez d'envoyer l'e-mail
            if (mail($email, $subject, $message, $headers)) {
                $successMessage = "Un email de réinitialisation a été envoyé.";
            } else {
                $errorMessage = "Erreur lors de l'envoi de l'email.";
            }
        } else {
            $errorMessage = "Adresse email introuvable.";
        }

        mysqli_close($connexion);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de Passe Oublié - Espace de gestion des stages</title>
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
        
        .forgot-container {
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
        
        .card-header .fa-key {
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
    </style>
</head>
<body>
<div class="forgot-container">
    <div class="card">
        <div class="card-header">
            <i class="fas fa-key"></i>
            <h2 class="mb-0">Mot de passe oublié</h2>
            <p class="mb-0">Réinitialisation de votre mot de passe</p>
        </div>
        
        <div class="card-body">
            <?php if (isset($successMessage)): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($successMessage); ?>
                </div>
            <?php elseif (isset($errorMessage)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($errorMessage); ?>
                </div>
            <?php endif; ?>
            
            <p class="info-text">
                Entrez votre adresse e-mail ci-dessous et nous vous enverrons un lien pour réinitialiser votre mot de passe.
            </p>
            
            <form method="POST" action="">
                <div class="form-floating mb-4">
                    <input type="email" class="form-control" id="email" name="email" placeholder="Votre email" required>
                    <label for="email"><i class="fas fa-envelope me-2"></i>Adresse e-mail</label>
                </div>
                
                <button type="submit" class="btn btn-primary w-100 mb-3">
                    <i class="fas fa-paper-plane me-2"></i>Envoyer le lien
                </button>
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
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

$stmt = $pdo->prepare("SELECT * FROM stage WHERE id_user = :id_user LIMIT 1");
$stmt->execute(['id_user' => $id_user]);
$stage = $stmt->fetch();

// Si aucune information de stage n'est trouvée, on permet à l'utilisateur de créer un stage
if (!$stage) {
    $stage = [
        'id' => null,
        'titre' => '',
        'dateD' => '',
        'dateF' => '',
        'monEntreprise' => '',
        'monTuteur' => '',
        'telTuteur' => '',
        'adresse' => '',
        'ville' => '',
        'codePostal' => ''
    ];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titre = $_POST['titre'];
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];
    $entreprise = $_POST['entreprise'];
    $tuteur = $_POST['tuteur'];
    $telTuteur = $_POST['telTuteur'];
    $adresse = $_POST['adresse'];
    $ville = $_POST['ville'];
    $codePostal = $_POST['codePostal'];

    if ($stage['id']) {
        $stmt = $pdo->prepare("UPDATE stage SET titre = :titre, dateD = :date_debut, dateF = :date_fin, monEntreprise = :entreprise, monTuteur = :tuteur, telTuteur = :telTuteur, adresse = :adresse, ville = :ville, codePostal = :codePostal WHERE id = :id_stage AND id_user = :id_user");
        $stmt->execute([
            'titre' => $titre,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
            'entreprise' => $entreprise,
            'tuteur' => $tuteur,
            'telTuteur' => $telTuteur,
            'adresse' => $adresse,
            'ville' => $ville,
            'codePostal' => $codePostal,
            'id_stage' => $stage['id'],
            'id_user' => $id_user
        ]);
        $message = "Les informations du stage ont été mises à jour avec succès.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO stage (id_user, titre, dateD, dateF, monEntreprise, monTuteur, telTuteur, adresse, ville, codePostal) VALUES (:id_user, :titre, :date_debut, :date_fin, :entreprise, :tuteur, :telTuteur, :adresse, :ville, :codePostal)");
        $stmt->execute([
            'id_user' => $id_user,
            'titre' => $titre,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
            'entreprise' => $entreprise,
            'tuteur' => $tuteur,
            'telTuteur' => $telTuteur,
            'adresse' => $adresse,
            'ville' => $ville,
            'codePostal' => $codePostal
        ]);
        $message = "Le stage a été créé avec succès.";
    }

}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informations du Stage - Espace Élève</title>
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
        
        .stage-container {
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
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            background: white;
            margin-bottom: 2rem;
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
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(59, 113, 202, 0.25);
        }
        
        .btn-custom {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
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
            border: 1px solid #059669;
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
            border: 1px solid #3b82f6;
        }
        
        .stage-info {
            background-color: #f8fafc;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            border: 1px solid #e5e7eb;
        }
        
        .stage-info-item {
            display: flex;
            align-items: start;
            gap: 1rem;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .stage-info-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        
        .stage-info-label {
            min-width: 150px;
            font-weight: 500;
            color: #4b5563;
        }
        
        .stage-info-value {
            color: #111827;
            flex: 1;
        }
        
        .footer {
            margin-top: 3rem;
            padding: 1rem 0;
            background-color: var(--secondary-color);
            text-align: center;
            color: #6c757d;
        }
        
        .form-group-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
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
                    <a class="nav-link active" href="information_stage.php">
                        <i class="fas fa-briefcase me-1"></i>Stage
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

<div class="stage-container">
    <!-- En-tête -->
    <div class="welcome-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1">
                    <i class="fas fa-briefcase me-2"></i>Informations du Stage
                </h1>
                <p class="text-muted mb-0">
                    <?php if ($stage['id']): ?>
                        Consultez et modifiez les informations de votre stage
                    <?php else: ?>
                        Renseignez les informations de votre stage
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
    
    <?php if (isset($message)): ?>
        <div class="success-message">
            <i class="fas fa-check-circle"></i>
            <?= $message ?>
        </div>
    <?php endif; ?>
    
    <?php if (!$stage['id']): ?>
        <div class="info-message">
            <i class="fas fa-info-circle"></i>
            <span>Vous n'avez pas encore d'informations sur votre stage. Veuillez remplir les champs ci-dessous pour créer un stage.</span>
        </div>
    <?php else: ?>
        <!-- Affichage des informations actuelles -->
        <div class="stage-info">
            <div class="stage-info-item">
                <div class="stage-info-label">
                    <i class="fas fa-bookmark me-2"></i>Titre
                </div>
                <div class="stage-info-value"><?= htmlspecialchars($stage['titre']) ?></div>
            </div>
            
            <div class="stage-info-item">
                <div class="stage-info-label">
                    <i class="fas fa-calendar me-2"></i>Période
                </div>
                <div class="stage-info-value">
                    Du <?= date('d/m/Y', strtotime($stage['dateD'])) ?> 
                    au <?= date('d/m/Y', strtotime($stage['dateF'])) ?>
                </div>
            </div>
            
            <div class="stage-info-item">
                <div class="stage-info-label">
                    <i class="fas fa-building me-2"></i>Entreprise
                </div>
                <div class="stage-info-value"><?= htmlspecialchars($stage['monEntreprise']) ?></div>
            </div>
            
            <div class="stage-info-item">
                <div class="stage-info-label">
                    <i class="fas fa-user-tie me-2"></i>Tuteur
                </div>
                <div class="stage-info-value">
                    <?= htmlspecialchars($stage['monTuteur']) ?>
                    <br>
                    <a href="tel:<?= htmlspecialchars($stage['telTuteur']) ?>" class="text-primary">
                        <i class="fas fa-phone me-1"></i><?= htmlspecialchars($stage['telTuteur']) ?>
                    </a>
                </div>
            </div>
            
            <div class="stage-info-item">
                <div class="stage-info-label">
                    <i class="fas fa-map-marker-alt me-2"></i>Adresse
                </div>
                <div class="stage-info-value">
                    <?= htmlspecialchars($stage['adresse']) ?><br>
                    <?= htmlspecialchars($stage['codePostal']) ?> <?= htmlspecialchars($stage['ville']) ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Formulaire -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-4">
                <?php if ($stage['id']): ?>
                    <i class="fas fa-edit me-2"></i>Modifier les informations
                <?php else: ?>
                    <i class="fas fa-plus me-2"></i>Créer un nouveau stage
                <?php endif; ?>
            </h5>
            
            <form method="POST" action="information_stage.php" id="stageForm">
                <div class="mb-4">
                    <label for="titre" class="form-label">
                        <i class="fas fa-bookmark me-2"></i>Titre du stage
                    </label>
                    <input type="text" name="titre" id="titre" class="form-control" 
                           value="<?= htmlspecialchars($stage['titre']) ?>" required
                           placeholder="Ex: Stage de développement web">
                </div>
                
                <div class="form-group-row mb-4">
                    <div>
                        <label for="date_debut" class="form-label">
                            <i class="fas fa-calendar-alt me-2"></i>Date de début
                        </label>
                        <input type="date" name="date_debut" id="date_debut" class="form-control" 
                               value="<?= htmlspecialchars($stage['dateD']) ?>" required>
                    </div>
                    
                    <div>
                        <label for="date_fin" class="form-label">
                            <i class="fas fa-calendar-alt me-2"></i>Date de fin
                        </label>
                        <input type="date" name="date_fin" id="date_fin" class="form-control" 
                               value="<?= htmlspecialchars($stage['dateF']) ?>" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="entreprise" class="form-label">
                        <i class="fas fa-building me-2"></i>Entreprise
                    </label>
                    <input type="text" name="entreprise" id="entreprise" class="form-control" 
                           value="<?= htmlspecialchars($stage['monEntreprise']) ?>" required
                           placeholder="Nom de l'entreprise">
                </div>
                
                <div class="form-group-row mb-4">
                    <div>
                        <label for="tuteur" class="form-label">
                            <i class="fas fa-user-tie me-2"></i>Tuteur
                        </label>
                        <input type="text" name="tuteur" id="tuteur" class="form-control" 
                               value="<?= htmlspecialchars($stage['monTuteur']) ?>" required
                               placeholder="Nom et prénom du tuteur">
                    </div>
                    
                    <div>
                        <label for="telTuteur" class="form-label">
                            <i class="fas fa-phone me-2"></i>Téléphone du tuteur
                        </label>
                        <input type="tel" name="telTuteur" id="telTuteur" class="form-control" 
                               value="<?= htmlspecialchars($stage['telTuteur']) ?>" required
                               placeholder="Ex: 06 12 34 56 78"
                               pattern="[0-9]{2}\s?[0-9]{2}\s?[0-9]{2}\s?[0-9]{2}\s?[0-9]{2}|[0-9]{10}"
                               title="Format: 0612345678 ou 06 12 34 56 78">
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="adresse" class="form-label">
                        <i class="fas fa-map-marked-alt me-2"></i>Adresse
                    </label>
                    <input type="text" name="adresse" id="adresse" class="form-control" 
                           value="<?= htmlspecialchars($stage['adresse']) ?>" required
                           placeholder="Numéro et nom de rue">
                </div>
                
                <div class="form-group-row mb-4">
                    <div>
                        <label for="ville" class="form-label">
                            <i class="fas fa-city me-2"></i>Ville
                        </label>
                        <input type="text" name="ville" id="ville" class="form-control" 
                               value="<?= htmlspecialchars($stage['ville']) ?>" required
                               placeholder="Nom de la ville">
                    </div>
                    
                    <div>
                        <label for="codePostal" class="form-label">
                            <i class="fas fa-map-pin me-2"></i>Code postal
                        </label>
                        <input type="text" name="codePostal" id="codePostal" class="form-control" 
                               value="<?= htmlspecialchars($stage['codePostal']) ?>" required
                               placeholder="Ex: 75000" pattern="[0-9]{5}">
                    </div>
                </div>
                
                <div class="d-flex gap-2 justify-content-between">
                    <a href="accueil_eleve.php" class="btn btn-secondary btn-custom">
                        <i class="fas fa-arrow-left me-2"></i>Retour
                    </a>
                    <button type="submit" class="btn btn-primary btn-custom">
                        <?php if ($stage['id']): ?>
                            <i class="fas fa-save me-2"></i>Enregistrer les modifications
                        <?php else: ?>
                            <i class="fas fa-plus me-2"></i>Créer le stage
                        <?php endif; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <p class="mb-0">&copy; <?php echo date('Y'); ?> - Espace de gestion des stages</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
<script>
// Format phone number input
document.getElementById('telTuteur').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, ''); // Supprime tout sauf les chiffres
    
    // Limite à 10 chiffres
    if (value.length > 10) {
        value = value.substring(0, 10);
    }
    
    // Formate avec des espaces : 06 12 34 56 78
    if (value.length > 0) {
        value = value.match(/.{1,2}/g).join(' ');
        if (value.length > 14) { // Limite la longueur formatée
            value = value.substring(0, 14);
        }
    }
    
    e.target.value = value;
});

// Nettoie le numéro avant envoi (enlève les espaces)
document.getElementById('stageForm').addEventListener('submit', function(e) {
    const telInput = document.getElementById('telTuteur');
    const telValue = telInput.value.replace(/\s/g, ''); // Supprime les espaces
    
    // Vérification du format (10 chiffres exactement)
    if (telValue.length !== 10 || !/^[0-9]{10}$/.test(telValue)) {
        e.preventDefault();
        alert('Le numéro de téléphone doit contenir exactement 10 chiffres.');
        return;
    }
    
    // Valide les dates
    const dateDebut = new Date(document.getElementById('date_debut').value);
    const dateFin = new Date(document.getElementById('date_fin').value);
    
    if (dateFin < dateDebut) {
        e.preventDefault();
        alert('La date de fin doit être postérieure à la date de début.');
        return;
    }
    
    // Nettoie le téléphone pour l'envoi (garde seulement les chiffres)
    telInput.value = telValue;
});
</script>
</body>
</html>

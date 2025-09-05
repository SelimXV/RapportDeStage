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

// Vérifier si un ID de CR est passé dans l'URL pour modifier un CR existant
if (isset($_GET['id'])) {
    $id_cr = $_GET['id'];

    // Récupérer le compte rendu existant
    $stmt = $pdo->prepare("SELECT * FROM CR WHERE id = :id_cr AND id_user = :id_user");
    $stmt->execute(['id_cr' => $id_cr, 'id_user' => $id_user]);
    $compte_rendu = $stmt->fetch();

    // Si le compte rendu n'existe pas, on affiche une erreur
    if (!$compte_rendu) {
        echo "Erreur : Compte rendu non trouvé.";
        exit();
    }

    $sujet = $compte_rendu['sujet'];
    $contenu = $compte_rendu['contenu'];
    $date_creation = $compte_rendu['date_creation'];
    $date_modification = $compte_rendu['date_modif'];
} else {
    // Si c'est une création de nouveau CR, on initialise les variables à vide
    $id_cr = null;
    $sujet = '';
    $contenu = '';
    $date_creation = date('Y-m-d H:i:s');
    $date_modification = null;
}

// Traitement du formulaire de création ou modification de CR
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sujet = $_POST['sujet'];
    $contenu = $_POST['contenu'];

    if ($id_cr) {

        $date_modification = date('Y-m-d H:i:s');
        $stmt = $pdo->prepare("UPDATE CR SET sujet = :sujet, contenu = :contenu, date_modif = :date_modification WHERE id = :id_cr AND id_user = :id_user");
        $stmt->execute([
            'sujet' => $sujet,
            'contenu' => $contenu,
            'date_modification' => $date_modification,
            'id_cr' => $id_cr,
            'id_user' => $id_user
        ]);
        $message = "Le compte rendu a été mis à jour avec succès.";
    } else {
        // Création d'un nouveau CR
        $stmt = $pdo->prepare("INSERT INTO CR (id_user, sujet, contenu, date_creation) VALUES (:id_user, :sujet, :contenu, :date_creation)");
        $stmt->execute([
            'id_user' => $id_user,
            'sujet' => $sujet,
            'contenu' => $contenu,
            'date_creation' => $date_creation
        ]);
        $message = "Le compte rendu a été créé avec succès.";
    }

    // Redirection vers la liste des CR
    header('Location: liste_comptes_rendus.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $id_cr ? 'Modifier' : 'Créer' ?> un compte rendu - Espace Élève</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
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
        
        .report-container {
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
        
        .form-control[readonly] {
            background-color: #f8f9fa;
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
        
        .editor-container {
            border: 1px solid #ced4da;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        
        .ql-toolbar {
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            border: none !important;
            border-bottom: 1px solid #ced4da !important;
        }
        
        .ql-container {
            border: none !important;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
            background-color: #fff;
            min-height: 200px;
        }
        
        .date-info {
            background-color: #f8fafc;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            border: 1px solid #e5e7eb;
        }
        
        .date-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #6b7280;
            margin-bottom: 0.5rem;
        }
        
        .date-item:last-child {
            margin-bottom: 0;
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

<div class="report-container">
    <!-- En-tête -->
    <div class="welcome-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1">
                    <?php if ($id_cr): ?>
                        <i class="fas fa-edit me-2"></i>Modifier le compte rendu
                    <?php else: ?>
                        <i class="fas fa-plus me-2"></i>Nouveau compte rendu
                    <?php endif; ?>
                </h1>
                <p class="text-muted mb-0">
                    <?php if ($id_cr): ?>
                        Mettez à jour les informations de votre compte rendu
                    <?php else: ?>
                        Créez un nouveau compte rendu de stage
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
    
    <!-- Formulaire -->
    <div class="card">
        <div class="card-body">
            <form method="POST" action="creer_modifier_compte_rendu.php<?= $id_cr ? '?id=' . $id_cr : '' ?>" id="reportForm">
                <div class="mb-4">
                    <label for="sujet" class="form-label">
                        <i class="fas fa-heading me-2"></i>Sujet du compte rendu
                    </label>
                    <input type="text" name="sujet" id="sujet" class="form-control" 
                           value="<?= htmlspecialchars($sujet) ?>" required
                           placeholder="Ex: Première semaine de stage - Introduction à l'entreprise">
                </div>
                
                <div class="mb-4">
                    <label for="editor" class="form-label">
                        <i class="fas fa-file-alt me-2"></i>Contenu du compte rendu
                    </label>
                    <div class="editor-container">
                        <div id="editor"><?= htmlspecialchars($contenu) ?></div>
                    </div>
                    <input type="hidden" name="contenu" id="contenu">
                </div>
                
                <div class="date-info">
                    <div class="date-item">
                        <i class="fas fa-clock"></i>
                        <span>Créé le <?= date('d/m/Y à H:i', strtotime($date_creation)) ?></span>
                    </div>
                    <?php if ($date_modification): ?>
                        <div class="date-item">
                            <i class="fas fa-edit"></i>
                            <span>Dernière modification le <?= date('d/m/Y à H:i', strtotime($date_modification)) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="d-flex gap-2 justify-content-between">
                    <a href="liste_comptes_rendus.php" class="btn btn-secondary btn-custom">
                        <i class="fas fa-arrow-left me-2"></i>Retour
                    </a>
                    <button type="submit" class="btn btn-primary btn-custom">
                        <?php if ($id_cr): ?>
                            <i class="fas fa-save me-2"></i>Enregistrer les modifications
                        <?php else: ?>
                            <i class="fas fa-plus me-2"></i>Créer le compte rendu
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
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
// Initialize Quill rich text editor
var quill = new Quill('#editor', {
    theme: 'snow',
    modules: {
        toolbar: [
            [{ 'header': [1, 2, 3, false] }],
            ['bold', 'italic', 'underline'],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            [{ 'color': [] }, { 'background': [] }],
            ['clean']
        ]
    },
    placeholder: 'Rédigez votre compte rendu ici...'
});

// Update hidden form field before submission
document.getElementById('reportForm').onsubmit = function() {
    document.getElementById('contenu').value = quill.root.innerHTML;
    return true;
};
</script>
</body>
</html>
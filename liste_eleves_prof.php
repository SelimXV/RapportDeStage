<?php
session_start();

// Vérifie si l'utilisateur est un professeur
if (!isset($_SESSION['login']) || $_SESSION['role'] != 1) {
    header('Location: index.php');
    exit();
}

include '_conf.php';

$connexion = mysqli_connect($serveurBDD, $userBDD, $mdpBDD, $nomBDD);
if (!$connexion) {
    die("Connexion à la base de données échouée");
}

// Récupérer tous les élèves avec leurs stages (seulement ceux assignés au professeur connecté)
$id_prof_connecte = $_SESSION['id_user'];
$query = "
    SELECT user.id, user.nom, user.prenom, user.email, stage.titre, stage.dateD, stage.dateF, stage.monEntreprise 
    FROM user 
    LEFT JOIN stage ON user.id = stage.id_user 
    WHERE user.id_statut = 2 
    AND (stage.id_prof = $id_prof_connecte OR stage.id_prof IS NULL)
";
$result = mysqli_query($connexion, $query);

mysqli_close($connexion);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Élèves - Espace Professeur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
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
        
        .students-container {
            max-width: 1200px;
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
            padding: 1.5rem;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table thead th {
            background-color: #f8fafc;
            font-weight: 600;
            color: #374151;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .table td {
            vertical-align: middle;
            color: #4b5563;
        }
        
        .badge-stage {
            font-size: 0.85rem;
            padding: 0.35rem 0.65rem;
            border-radius: 6px;
        }
        
        .badge-active {
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #059669;
        }
        
        .badge-completed {
            background-color: #eff6ff;
            color: #1e40af;
            border: 1px solid #3b82f6;
        }
        
        .badge-pending {
            background-color: #fff7ed;
            color: #9a3412;
            border: 1px solid #ea580c;
        }
        
        .footer {
            margin-top: 3rem;
            padding: 1rem 0;
            background-color: var(--secondary-color);
            text-align: center;
            color: #6c757d;
        }
        
        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #ced4da;
            border-radius: 8px;
            padding: 0.375rem 0.75rem;
        }
        
        .dataTables_wrapper .dataTables_length select:focus,
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(59, 113, 202, 0.25);
        }
        
        .email-link {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .email-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="accueil_prof.php">
            <i class="fas fa-chalkboard-teacher me-2"></i>Espace Professeur
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="accueil_prof.php">
                        <i class="fas fa-home me-1"></i>Accueil
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="liste_comptes_rendus_prof.php">
                        <i class="fas fa-list-alt me-1"></i>Comptes rendus
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="liste_eleves_prof.php">
                        <i class="fas fa-user-graduate me-1"></i>Élèves
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

<div class="students-container">
    <!-- En-tête -->
    <div class="welcome-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1">Liste des Élèves</h1>
                <p class="text-muted mb-0">Consultez les informations des élèves et leurs stages</p>
            </div>
        </div>
    </div>
    
    <!-- Table des élèves -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="studentsTable" class="table table-hover">
                    <thead>
                    <tr>
                        <th>
                            <i class="fas fa-user me-2"></i>Nom & Prénom
                        </th>
                        <th>
                            <i class="fas fa-envelope me-2"></i>Email
                        </th>
                        <th>
                            <i class="fas fa-briefcase me-2"></i>Stage
                        </th>
                        <th>
                            <i class="fas fa-building me-2"></i>Entreprise
                        </th>
                        <th>
                            <i class="fas fa-calendar me-2"></i>Période
                        </th>
                        <th>
                            <i class="fas fa-info-circle me-2"></i>Statut
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($row['nom'] . ' ' . $row['prenom']) ?></strong>
                            </td>
                            <td>
                                <a href="mailto:<?= htmlspecialchars($row['email']) ?>" class="email-link">
                                    <?= htmlspecialchars($row['email']) ?>
                                </a>
                            </td>
                            <td>
                                <?php if ($row['titre']): ?>
                                    <?= htmlspecialchars($row['titre']) ?>
                                <?php else: ?>
                                    <span class="text-muted">Non défini</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($row['monEntreprise']): ?>
                                    <?= htmlspecialchars($row['monEntreprise']) ?>
                                <?php else: ?>
                                    <span class="text-muted">Non définie</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($row['dateD'] && $row['dateF']): ?>
                                    <?= date('d/m/Y', strtotime($row['dateD'])) ?> au <?= date('d/m/Y', strtotime($row['dateF'])) ?>
                                <?php else: ?>
                                    <span class="text-muted">Dates non définies</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $today = new DateTime();
                                $start = $row['dateD'] ? new DateTime($row['dateD']) : null;
                                $end = $row['dateF'] ? new DateTime($row['dateF']) : null;
                                
                                if (!$start || !$end) {
                                    echo '<span class="badge badge-stage badge-pending">En attente</span>';
                                } elseif ($today > $end) {
                                    echo '<span class="badge badge-stage badge-completed">Terminé</span>';
                                } elseif ($today >= $start && $today <= $end) {
                                    echo '<span class="badge badge-stage badge-active">En cours</span>';
                                } else {
                                    echo '<span class="badge badge-stage badge-pending">À venir</span>';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <p class="mb-0">&copy; <?php echo date('Y'); ?> - Espace de gestion des stages</p>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    $('#studentsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json'
        },
        order: [[0, 'asc']],
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Tous"]],
        responsive: true,
        columnDefs: [
            { orderable: false, targets: [5] }
        ]
    });
});
</script>
</body>
</html>
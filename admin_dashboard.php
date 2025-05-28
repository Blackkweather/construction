<?php
require 'auth.php';
requireLogin();
requireRole('admin');

include 'db.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Tableau de bord administrateur</title>
    <link rel="stylesheet" href="style.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        .dashboard-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }
        .dashboard-card:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 16px rgba(0,0,0,0.3);
        }
        .btn-light {
            display: inline-block;
            padding: 0.5rem 1rem;
            font-weight: 600;
            color: #2c3e50;
            background-color: #ecf0f1;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn-light:hover {
            background-color: #d6d8db;
            color: #212529;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#">Tableau de bord administrateur</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Basculer la navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="admin_dashboard.php"><i class="bi bi-house-door"></i> Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_users.php"><i class="bi bi-people"></i> Utilisateurs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_vehicules.php"><i class="bi bi-truck"></i> Véhicules</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_bookings.php"><i class="bi bi-calendar-check"></i> Réservations</a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link text-danger" href="logout.php"><i class="bi bi-box-arrow-right"></i> Déconnexion</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link text-success" href="login.php"><i class="bi bi-box-arrow-in-right"></i> Connexion</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="text-center mb-5 display-5 fw-bold text-primary">Bienvenue, Administrateur</h1>
        <div class="row g-4 justify-content-center">

<?php
// Fetch statistics
try {
    $stmtUsersCount = $pdo->query('SELECT COUNT(*) FROM utilisateurs');
    $usersCount = $stmtUsersCount->fetchColumn();

    $stmtVehiclesCount = $pdo->query('SELECT COUNT(*) FROM vehicules');
    $vehiclesCount = $stmtVehiclesCount->fetchColumn();

    $stmtBookingsCount = $pdo->query('SELECT COUNT(*) FROM reservations');
    $bookingsCount = $stmtBookingsCount->fetchColumn();

    $stmtTopVehicles = $pdo->query('
        SELECT v.nom, COUNT(r.id) AS booking_count
        FROM vehicules v
        LEFT JOIN reservations r ON v.id = r.vehicle_id
        GROUP BY v.id
        ORDER BY booking_count DESC
        LIMIT 3
    ');
    $topVehicles = $stmtTopVehicles->fetchAll();
} catch (Exception $e) {
    $usersCount = $vehiclesCount = $bookingsCount = 0;
    $topVehicles = [];
}
?>

<div class="col-12 mb-4">
    <h2 class="text-center text-primary">Statistiques</h2>
    <div class="row g-4 justify-content-center">
        <div class="col-md-3">
            <div class="card text-white bg-info shadow">
                <div class="card-body text-center">
                    <h5 class="card-title">Utilisateurs</h5>
                    <p class="display-4 fw-bold"><?php echo htmlspecialchars($usersCount); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success shadow">
                <div class="card-body text-center">
                    <h5 class="card-title">Véhicules</h5>
                    <p class="display-4 fw-bold"><?php echo htmlspecialchars($vehiclesCount); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning shadow">
                <div class="card-body text-center">
                    <h5 class="card-title">Réservations</h5>
                    <p class="display-4 fw-bold"><?php echo htmlspecialchars($bookingsCount); ?></p>
                </div>
            </div>
        </div>
    </div>
    <h3 class="mt-4 text-center">Véhicules les plus loués</h3>
    <?php if (count($topVehicles) === 0): ?>
        <p class="text-center">Aucun véhicule loué pour le moment.</p>
    <?php else: ?>
        <ul class="list-group list-group-flush w-50 mx-auto">
            <?php foreach ($topVehicles as $vehicle): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?php echo htmlspecialchars($vehicle['nom']); ?>
                    <span class="badge bg-primary rounded-pill"><?php echo htmlspecialchars($vehicle['booking_count']); ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

<div class="col-md-4">
    <div class="card text-white bg-secondary mb-3 dashboard-card h-100 shadow" onclick="window.location.href='manage_users.php'" style="cursor:pointer;">
        <div class="card-body d-flex flex-column align-items-center">
            <i class="bi bi-people display-5 mb-2"></i>
            <h5 class="card-title">Utilisateurs</h5>
            <p class="card-text text-center small">Voir et gérer tous les utilisateurs enregistrés.</p>
            <div class="d-flex w-100 gap-2 mt-auto">
                <a href="manage_users.php" class="btn btn-light flex-fill" onclick="event.stopPropagation();">Gérer</a>
                <a href="add_user.php" class="btn btn-outline-light flex-fill" onclick="event.stopPropagation();">Ajouter</a>
            </div>
        </div>
    </div>
</div>
<div class="col-md-4">
    <div class="card text-white bg-success mb-3 dashboard-card h-100 shadow" onclick="window.location.href='manage_vehicules.php'" style="cursor:pointer;">
        <div class="card-body d-flex flex-column align-items-center">
            <i class="bi bi-truck display-5 mb-2"></i>
            <h5 class="card-title">Véhicules</h5>
            <p class="card-text text-center small">Voir et gérer tous les véhicules listés.</p>
            <div class="d-flex w-100 gap-2 mt-auto">
                <a href="manage_vehicules.php" class="btn btn-light flex-fill" onclick="event.stopPropagation();">Gérer</a>
                <a href="add_vehicle.php" class="btn btn-outline-light flex-fill" onclick="event.stopPropagation();">Ajouter</a>
            </div>
        </div>
    </div>
</div>
<div class="col-md-4">
    <div class="card text-white bg-warning mb-3 dashboard-card h-100 shadow" onclick="window.location.href='manage_bookings.php'" style="cursor:pointer;">
        <div class="card-body d-flex flex-column align-items-center">
            <i class="bi bi-calendar-check display-5 mb-2"></i>
            <h5 class="card-title">Réservations</h5>
            <p class="card-text text-center small">Voir et gérer toutes les réservations.</p>
            <div class="d-flex w-100 gap-2 mt-auto">
                <a href="manage_bookings.php" class="btn btn-light flex-fill" onclick="event.stopPropagation();">Gérer</a>
                <a href="add_booking.php" class="btn btn-outline-light flex-fill" onclick="event.stopPropagation();">Ajouter</a>
            </div>
            <a href="booking_history.php" class="btn btn-link w-100 mt-2" onclick="event.stopPropagation();">Historique</a>
        </div>
    </div>
</div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
require 'auth.php';
requireLogin();

// Strict role checking
if (!in_array($_SESSION['role'], ['client', 'admin'])) {
    header('Location: login.php');
    exit();
}

include 'header.php';
include 'db.php';

$user_id = $_SESSION['user_id'];

// Fetch available vehicles with prepared statement
try {
    $stmt = $pdo->prepare('SELECT * FROM vehicules WHERE disponibilite = 1 ORDER BY id DESC');
    $stmt->execute();
    $vehicules = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Vehicle fetch error: " . $e->getMessage());
    $vehicules = [];
}

// Fetch rental history with pagination support
$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$itemsPerPage = 5;
$offset = ($currentPage - 1) * $itemsPerPage;

try {
    // Get total count for pagination
    $countStmt = $pdo->prepare('
        SELECT COUNT(*) 
        FROM reservations r
        JOIN vehicules v ON r.vehicle_id = v.id
        WHERE r.user_id = ?
    ');
    $countStmt->execute([$user_id]);
    $totalItems = $countStmt->fetchColumn();
    
    $totalPages = ceil($totalItems / $itemsPerPage);
    
    // Get paginated results
    $stmtHistory = $pdo->prepare('
        SELECT r.*, v.nom AS vehicle_name, v.image_path
        FROM reservations r
        JOIN vehicules v ON r.vehicle_id = v.id
        WHERE r.user_id = ?
        ORDER BY r.start_date DESC
        LIMIT ? OFFSET ?
    ');
    $stmtHistory->bindValue(1, $user_id, PDO::PARAM_INT);
    $stmtHistory->bindValue(2, $itemsPerPage, PDO::PARAM_INT);
    $stmtHistory->bindValue(3, $offset, PDO::PARAM_INT);
    $stmtHistory->execute();
    $rentalHistory = $stmtHistory->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Rental history error: " . $e->getMessage());
    $rentalHistory = [];
    $totalPages = 1;
}

// Fetch statistics with improved query
try {
    $stmtStats = $pdo->prepare('
        SELECT 
            COUNT(*) AS total_rentals,
            COALESCE(SUM(total_price), 0) AS total_spent,
            MIN(start_date) AS first_rental,
            MAX(start_date) AS last_rental
        FROM reservations
        WHERE user_id = ?
    ');
    $stmtStats->execute([$user_id]);
    $stats = $stmtStats->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Stats error: " . $e->getMessage());
    $stats = ['total_rentals' => 0, 'total_spent' => 0, 'first_rental' => null, 'last_rental' => null];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord client | <?= htmlspecialchars($_SESSION['username']) ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <!-- Custom CSS -->
    <style>
        .card-img-custom {
            height: 200px;
            object-fit: cover;
        }
        .stats-card {
            transition: transform 0.3s;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .status-badge {
            font-size: 0.8rem;
            padding: 0.35em 0.65em;
        }
        .rental-card {
            border-left: 4px solid #0d6efd;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active text-white" href="#">
                                <i class="bi bi-speedometer2 me-2"></i>Tableau de bord
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#vehicles">
                                <i class="bi bi-truck me-2"></i>Véhicules
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#history">
                                <i class="bi bi-clock-history me-2"></i>Historique
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Tableau de bord</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-download me-1"></i>Exporter
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Welcome message with user's name -->
                <div class="alert alert-primary">
                    <h4><i class="bi bi-person me-2"></i>Bienvenue, <?= htmlspecialchars($_SESSION['username']) ?>!</h4>
                    <p class="mb-0">Consultez les véhicules disponibles, votre historique de locations et vos statistiques.</p>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-white bg-primary mb-3 stats-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Locations totales</h6>
                                        <h2 class="mb-0"><?= htmlspecialchars($stats['total_rentals']) ?></h2>
                                    </div>
                                    <i class="bi bi-car-front fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-success mb-3 stats-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Total dépensé</h6>
                                        <h2 class="mb-0">€<?= number_format($stats['total_spent'], 2) ?></h2>
                                    </div>
                                    <i class="bi bi-currency-euro fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-info mb-3 stats-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Première location</h6>
                                        <h6 class="mb-0">
                                            <?= $stats['first_rental'] ? date('d/m/Y', strtotime($stats['first_rental'])) : 'N/A' ?>
                                        </h6>
                                    </div>
                                    <i class="bi bi-calendar-event fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-warning mb-3 stats-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Dernière location</h6>
                                        <h6 class="mb-0">
                                            <?= $stats['last_rental'] ? date('d/m/Y', strtotime($stats['last_rental'])) : 'N/A' ?>
                                        </h6>
                                    </div>
                                    <i class="bi bi-calendar-check fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rental History -->
                <div class="card mb-4" id="history">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Historique de locations</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($rentalHistory)): ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>Aucune location trouvée.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Véhicule</th>
                                            <th>Période</th>
                                            <th>Prix total</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($rentalHistory as $rental): ?>
                                            <tr class="rental-card">
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <?php if (!empty($rental['image_path']) && file_exists($rental['image_path'])): ?>
                                                            <img src="<?= htmlspecialchars($rental['image_path']) ?>" class="rounded me-3" width="60" height="40" style="object-fit: cover">
                                                        <?php else: ?>
                                                            <img src="https://via.placeholder.com/60x40?text=No+Image" class="rounded me-3" width="60" height="40">
                                                        <?php endif; ?>
                                                        <span><?= htmlspecialchars($rental['vehicle_name']) ?></span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?= date('d/m/Y', strtotime($rental['start_date'])) ?> - 
                                                    <?= date('d/m/Y', strtotime($rental['end_date'])) ?>
                                                </td>
                                                <td>€<?= number_format($rental['total_price'], 2) ?></td>
                                                <td>
                                                    <?php 
                                                    $badgeClass = [
                                                        'confirmed' => 'bg-success',
                                                        'pending' => 'bg-warning',
                                                        'cancelled' => 'bg-danger',
                                                        'completed' => 'bg-info'
                                                    ][$rental['status']] ?? 'bg-secondary';
                                                    ?>
                                                    <span class="badge <?= $badgeClass ?> status-badge">
                                                        <?= htmlspecialchars(ucfirst($rental['status'])) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#detailsModal<?= $rental['id'] ?>">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <?php if ($rental['status'] === 'pending'): ?>
                                                        <a href="cancel_rental.php?id=<?= $rental['id'] ?>" class="btn btn-sm btn-outline-danger">
                                                            <i class="bi bi-x"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <?php if ($totalPages > 1): ?>
                                <nav aria-label="Page navigation">
                                    <ul class="pagination justify-content-center">
                                        <li class="page-item <?= $currentPage == 1 ? 'disabled' : '' ?>">
                                            <a class="page-link" href="?page=<?= $currentPage - 1 ?>" aria-label="Previous">
                                                <span aria-hidden="true">&laquo;</span>
                                            </a>
                                        </li>
                                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                            <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        <li class="page-item <?= $currentPage == $totalPages ? 'disabled' : '' ?>">
                                            <a class="page-link" href="?page=<?= $currentPage + 1 ?>" aria-label="Next">
                                                <span aria-hidden="true">&raquo;</span>
                                            </a>
                                        </li>
                                    </ul>
                                </nav>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Available Vehicles -->
                <div class="card" id="vehicles">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="bi bi-truck me-2"></i>Véhicules disponibles à la location</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($vehicules)): ?>
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>Aucun véhicule disponible à la location pour le moment.
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($vehicules as $vehicule): ?>
                                    <div class="col-md-6 col-lg-4 mb-4">
                                        <div class="card h-100 shadow-sm">
                                            <?php if (!empty($vehicule['image_path']) && file_exists($vehicule['image_path'])): ?>
                                                <img src="<?= htmlspecialchars($vehicule['image_path']) ?>" class="card-img-top card-img-custom" alt="<?= htmlspecialchars($vehicule['nom']) ?>">
                                            <?php else: ?>
                                                <img src="https://via.placeholder.com/400x200?text=Pas+d'image" class="card-img-top card-img-custom" alt="Pas d'image">
                                            <?php endif; ?>
                                            <div class="card-body">
                                                <h5 class="card-title"><?= htmlspecialchars($vehicule['nom']) ?></h5>
                                                <div class="mb-3">
                                                    <span class="badge bg-primary"><?= htmlspecialchars($vehicule['type']) ?></span>
                                                    <?php if ($vehicule['prix_par_jour'] < 50): ?>
                                                        <span class="badge bg-success ms-1">Économique</span>
                                                    <?php elseif ($vehicule['prix_par_jour'] > 100): ?>
                                                        <span class="badge bg-danger ms-1">Luxe</span>
                                                    <?php endif; ?>
                                                </div>
                                                <p class="card-text">
                                                    <i class="bi bi-tag me-2"></i><strong>Prix/jour:</strong> €<?= number_format($vehicule['prix_par_jour'], 2) ?>
                                                </p>
                                                <p class="card-text">
                                                    <i class="bi bi-info-circle me-2"></i><?= nl2br(htmlspecialchars($vehicule['caracteristiques'])) ?>
                                                </p>
                                            </div>
                                            <div class="card-footer bg-white">
                                                    <a href="rent_vehicle.php?id=<?= $vehicule['id'] ?>" class="btn btn-primary d-grid">
                                                        <i class="bi bi-calendar-check me-1"></i>Louer ce véhicule
                                                    </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modals for rental details -->
    <?php foreach ($rentalHistory as $rental): ?>
        <div class="modal fade" id="detailsModal<?= $rental['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Détails de la location</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <?php if (!empty($rental['image_path']) && file_exists($rental['image_path'])): ?>
                                    <img src="<?= htmlspecialchars($rental['image_path']) ?>" class="img-fluid rounded">
                                <?php else: ?>
                                    <img src="https://via.placeholder.com/300x200?text=No+Image" class="img-fluid rounded">
                                <?php endif; ?>
                            </div>
                            <div class="col-md-8">
                                <h5><?= htmlspecialchars($rental['vehicle_name']) ?></h5>
                                <p>
                                    <strong>Période:</strong> <?= date('d/m/Y', strtotime($rental['start_date'])) ?> - <?= date('d/m/Y', strtotime($rental['end_date'])) ?>
                                </p>
                                <p>
                                    <strong>Prix total:</strong> €<?= number_format($rental['total_price'], 2) ?>
                                </p>
                                <p>
                                    <strong>Statut:</strong> 
                                    <?php 
                                    $badgeClass = [
                                        'confirmed' => 'bg-success',
                                        'pending' => 'bg-warning',
                                        'cancelled' => 'bg-danger',
                                        'completed' => 'bg-info'
                                    ][$rental['status']] ?? 'bg-secondary';
                                    ?>
                                    <span class="badge <?= $badgeClass ?>">
                                        <?= htmlspecialchars(ucfirst($rental['status'])) ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                        <?php if (!empty($rental['notes'])): ?>
                            <div class="alert alert-light">
                                <h6>Notes:</h6>
                                <p><?= nl2br(htmlspecialchars($rental['notes'])) ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        <?php if ($rental['status'] === 'pending'): ?>
                            <a href="cancel_rental.php?id=<?= $rental['id'] ?>" class="btn btn-danger">
                                <i class="bi bi-x me-1"></i>Annuler
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include 'footer.php'; ?>
</body>
</html>

<?php
require 'auth.php';
requireLogin();
requireRole('locataire');
include 'header.php';
include 'db.php';

$user_id = $_SESSION['user_id'];

$message = '';

// Fetch total vehicles owned by locataire
try {
    $stmtVehicles = $pdo->prepare('SELECT COUNT(*) FROM vehicules WHERE proprietaire_id = ?');
    $stmtVehicles->execute([$user_id]);
    $totalVehicles = $stmtVehicles->fetchColumn();
} catch (Exception $e) {
    $totalVehicles = 0;
}

// Fetch total bookings and revenue for locataire's vehicles
try {
    $stmtBookings = $pdo->prepare('
        SELECT 
            COUNT(r.id) AS total_bookings,
            COALESCE(SUM(r.total_price), 0) AS total_revenue
        FROM reservations r
        JOIN vehicules v ON r.vehicle_id = v.id
        WHERE v.proprietaire_id = ?
    ');
    $stmtBookings->execute([$user_id]);
    $stats = $stmtBookings->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $stats = ['total_bookings' => 0, 'total_revenue' => 0];
}

// Fetch recent bookings (last 5)
try {
    $stmtRecent = $pdo->prepare('
        SELECT r.*, v.nom AS vehicle_name
        FROM reservations r
        JOIN vehicules v ON r.vehicle_id = v.id
        WHERE v.proprietaire_id = ?
        ORDER BY r.start_date DESC
        LIMIT 5
    ');
    $stmtRecent->execute([$user_id]);
    $recentBookings = $stmtRecent->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $recentBookings = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Tableau de bord Locataire</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        .stats-card {
            transition: transform 0.3s;
            cursor: pointer;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .status-badge {
            font-size: 0.8rem;
            padding: 0.35em 0.65em;
        }
    </style>
</head>
<body>
<div class="container my-5">
    <h1 class="mb-4">Bienvenue, Locataire</h1>
    <p class="mb-4">Gérez vos véhicules et consultez vos revenus estimés.</p>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary stats-card p-3" onclick="window.location.href='locataire_vehicles.php'">
                <h5>Véhicules possédés <i class="bi bi-truck"></i></h5>
                <h2><?= htmlspecialchars($totalVehicles) ?></h2>
                <a href="locataire_vehicles.php" class="btn btn-light btn-sm mt-2">Gérer les véhicules <i class="bi bi-pencil-square"></i></a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success stats-card p-3" onclick="window.location.href='manage_bookings.php'">
                <h5>Réservations totales <i class="bi bi-journal-check"></i></h5>
                <h2><?= htmlspecialchars($stats['total_bookings']) ?></h2>
                <a href="manage_bookings.php" class="btn btn-light btn-sm mt-2">Voir les réservations <i class="bi bi-eye"></i></a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info stats-card p-3">
                <h5>Revenu estimé <i class="bi bi-currency-euro"></i></h5>
                <h2>€<?= number_format($stats['total_revenue'], 2) ?></h2>
            </div>
        </div>
    </div>

    <h3>Réservations récentes</h3>
    <?php if (empty($recentBookings)): ?>
        <p>Aucune réservation récente.</p>
    <?php else: ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Véhicule</th>
                    <th>Date début</th>
                    <th>Date fin</th>
                    <th>Prix total</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentBookings as $booking): ?>
                    <tr>
                        <td><?= htmlspecialchars($booking['vehicle_name']) ?></td>
                        <td><?= htmlspecialchars($booking['start_date']) ?></td>
                        <td><?= htmlspecialchars($booking['end_date']) ?></td>
                        <td>€<?= number_format($booking['total_price'], 2) ?></td>
                        <td>
                            <?php
                            $badgeClass = [
                                'confirmed' => 'badge bg-success',
                                'pending' => 'badge bg-warning',
                                'cancelled' => 'badge bg-danger',
                                'completed' => 'badge bg-info'
                            ][$booking['status']] ?? 'badge bg-secondary';
                            ?>
                            <span class="<?= $badgeClass ?>"><?= htmlspecialchars(ucfirst($booking['status'])) ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include 'footer.php'; ?>
</body>
</html>

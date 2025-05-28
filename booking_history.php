<?php
require 'auth.php';
requireLogin();
requireRole('admin');
include 'header.php';
include 'db.php';

$message = '';

try {
    $today = date('Y-m-d');
    $stmt = $pdo->prepare('SELECT r.id, u.nom AS user_name, v.nom AS vehicle_name, r.start_date, r.end_date, r.total_price, r.status 
                           FROM reservations r 
                           JOIN utilisateurs u ON r.user_id = u.id 
                           JOIN vehicules v ON r.vehicle_id = v.id 
                           WHERE r.end_date < ? 
                           ORDER BY r.end_date DESC');
    $stmt->execute([$today]);
    $old_bookings = $stmt->fetchAll();
} catch (Exception $e) {
    $message = 'Erreur lors de la récupération de l\'historique des réservations : ' . htmlspecialchars($e->getMessage());
    $old_bookings = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Historique des réservations</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <h1>Historique des réservations</h1>
    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>
    <?php if (count($old_bookings) === 0): ?>
        <p>Aucune réservation ancienne trouvée.</p>
    <?php else: ?>
    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Utilisateur</th>
                <th>Véhicule</th>
                <th>Date début</th>
                <th>Date fin</th>
                <th>Prix total</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($old_bookings as $booking): ?>
            <tr>
                <td><?php echo htmlspecialchars($booking['id']); ?></td>
                <td><?php echo htmlspecialchars($booking['user_name']); ?></td>
                <td><?php echo htmlspecialchars($booking['vehicle_name']); ?></td>
                <td><?php echo htmlspecialchars($booking['start_date']); ?></td>
                <td><?php echo htmlspecialchars($booking['end_date']); ?></td>
                <td><?php echo htmlspecialchars($booking['total_price']); ?> €</td>
                <td><?php echo htmlspecialchars($booking['status']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
    <br />
    <a href="admin_dashboard.php">Retour au tableau de bord</a>
</body>
</html>

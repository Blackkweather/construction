<?php
require 'auth.php';
requireLogin();
requireRole('client');
include 'header.php';
include 'db.php';

$message = '';

try {
    $stmt = $pdo->prepare('SELECT r.id, v.nom AS vehicle_name, r.start_date, r.end_date, r.total_price, r.status FROM reservations r JOIN vehicules v ON r.vehicle_id = v.id WHERE r.user_id = ? ORDER BY r.start_date DESC');
    $stmt->execute([$_SESSION['user_id']]);
    $reservations = $stmt->fetchAll();
} catch (Exception $e) {
    $message = 'Error fetching reservations: ' . htmlspecialchars($e->getMessage());
    $reservations = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Reservation History</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <h1>Your Reservation History</h1>
    <?php if ($message): ?>
        <div class="alert alert-danger"><?php echo $message; ?></div>
    <?php endif; ?>
    <?php if (count($reservations) === 0): ?>
        <p>You have no reservations.</p>
    <?php else: ?>
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Vehicle</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Total Price</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservations as $res): ?>
                <tr>
                    <td><?php echo htmlspecialchars($res['id']); ?></td>
                    <td><?php echo htmlspecialchars($res['vehicle_name']); ?></td>
                    <td><?php echo htmlspecialchars($res['start_date']); ?></td>
                    <td><?php echo htmlspecialchars($res['end_date']); ?></td>
                    <td><?php echo htmlspecialchars($res['total_price']); ?> €</td>
                    <td><?php echo htmlspecialchars($res['status']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <a href="client_dashboard.php">Back to Dashboard</a>
</body>
</html>

<?php
require 'auth.php';
requireLogin();
requireRole('admin');
include 'header.php';
include 'db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mise à jour du statut de réservation
    if (isset($_POST['booking_id']) && isset($_POST['status'])) {
        $booking_id = (int)$_POST['booking_id'];
        $status = $_POST['status'];
        $allowed_statuses = ['pending', 'confirmed', 'cancelled'];
        if (in_array($status, $allowed_statuses)) {
            $stmt = $pdo->prepare('UPDATE reservations SET status = ? WHERE id = ?');
            if ($stmt->execute([$status, $booking_id])) {
                $message = "Statut de la réservation mis à jour avec succès.";
            } else {
                $message = "Échec de la mise à jour du statut.";
            }
        } else {
            $message = "Valeur de statut invalide.";
        }
    }

    // Suppression de réservation
    if (isset($_POST['delete_booking_id'])) {
        $delete_id = (int)$_POST['delete_booking_id'];
        $stmt = $pdo->prepare('DELETE FROM reservations WHERE id = ?');
        if ($stmt->execute([$delete_id])) {
            $message = "Réservation supprimée avec succès.";
        } else {
            $message = "Échec de la suppression de la réservation.";
        }
    }
}

try {
    $stmt = $pdo->query('SELECT r.id, u.nom AS user_name, v.nom AS vehicle_name, r.start_date, r.end_date, r.total_price, r.status FROM reservations r JOIN utilisateurs u ON r.user_id = u.id JOIN vehicules v ON r.vehicle_id = v.id ORDER BY r.id DESC');
    $bookings = $stmt->fetchAll();
} catch (Exception $e) {
    $message = 'Erreur lors de la récupération des réservations : ' . htmlspecialchars($e->getMessage());
    $bookings = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Gérer les réservations</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <h1>Gestion des réservations</h1>
    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>
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
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookings as $booking): ?>
            <tr>
                <td><?php echo htmlspecialchars($booking['id']); ?></td>
                <td><?php echo htmlspecialchars($booking['user_name']); ?></td>
                <td><?php echo htmlspecialchars($booking['vehicle_name']); ?></td>
                <td><?php echo htmlspecialchars($booking['start_date']); ?></td>
                <td><?php echo htmlspecialchars($booking['end_date']); ?></td>
                <td><?php echo htmlspecialchars($booking['total_price']); ?> €</td>
                <td><?php echo htmlspecialchars($booking['status']); ?></td>
                <td>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>" />
                        <select name="status">
                            <option value="pending" <?php if ($booking['status'] === 'pending') echo 'selected'; ?>>En attente</option>
                            <option value="confirmed" <?php if ($booking['status'] === 'confirmed') echo 'selected'; ?>>Confirmée</option>
                            <option value="cancelled" <?php if ($booking['status'] === 'cancelled') echo 'selected'; ?>>Annulée</option>
                        </select>
                        <button type="submit">Mettre à jour</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a href="admin_dashboard.php">Retour au tableau de bord</a>
</body>
</html>

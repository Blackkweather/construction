<?php
require 'auth.php';
requireLogin();
requireRole('locataire');
include 'header.php';
include 'db.php';

$message = '';

// Handle vehicle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_vehicle_id'])) {
    $delete_id = (int)$_POST['delete_vehicle_id'];
    // Verify ownership before deletion
    $stmt = $pdo->prepare('SELECT proprietaire_id FROM vehicules WHERE id = ?');
    $stmt->execute([$delete_id]);
    $owner = $stmt->fetchColumn();
    if ($owner == $_SESSION['user_id']) {
        $delStmt = $pdo->prepare('DELETE FROM vehicules WHERE id = ?');
        if ($delStmt->execute([$delete_id])) {
            $message = "Vehicle deleted successfully.";
        } else {
            $message = "Failed to delete vehicle.";
        }
    } else {
        $message = "Unauthorized action.";
    }
}

try {
    $stmt = $pdo->prepare('SELECT * FROM vehicules WHERE proprietaire_id = ? ORDER BY id ASC');
    $stmt->execute([$_SESSION['user_id']]);
    $vehicles = $stmt->fetchAll();
} catch (Exception $e) {
    $message = 'Error fetching vehicles: ' . htmlspecialchars($e->getMessage());
    $vehicles = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Manage Your Vehicles</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <h1>Your Vehicles</h1>
    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>
    <a href="add_listing.php">Add New Vehicle</a>
    <?php if (count($vehicles) === 0): ?>
        <p>You have no vehicles listed.</p>
    <?php else: ?>
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Price Per Day</th>
                    <th>Availability</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vehicles as $vehicle): ?>
                <tr>
                    <td><?php echo htmlspecialchars($vehicle['id']); ?></td>
                    <td><?php echo htmlspecialchars($vehicle['nom']); ?></td>
                    <td><?php echo htmlspecialchars($vehicle['type']); ?></td>
                    <td><?php echo htmlspecialchars($vehicle['prix_par_jour']); ?> €</td>
                    <td><?php echo $vehicle['disponibilite'] ? 'Available' : 'Unavailable'; ?></td>
                    <td>
                        <a href="edit_listing.php?id=<?php echo $vehicle['id']; ?>">Edit</a>
                        <form method="post" onsubmit="return confirm('Are you sure you want to delete this vehicle?');" style="display:inline;">
                            <input type="hidden" name="delete_vehicle_id" value="<?php echo $vehicle['id']; ?>" />
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <a href="locataire_dashboard.php">Back to Dashboard</a>
</body>
</html>

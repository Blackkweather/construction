
<?php
require 'auth.php';
requireLogin();
requireRole('locataire');
include 'header.php';
include 'db.php';

$user_email = 'loc@loc.com';

// Get user ID for loc@loc.com
$stmtUser = $pdo->prepare('SELECT id FROM utilisateurs WHERE email = ?');
$stmtUser->execute([$user_email]);
$user_id = $stmtUser->fetchColumn();

if (!$user_id) {
    echo "User with email $user_email not found.";
    exit;
}

// Fetch vehicles owned by loc@loc.com
$stmt = $pdo->prepare('SELECT * FROM vehicules WHERE proprietaire_id = ?');
$stmt->execute([$user_id]);
$vehicles = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Debug Vehicles for loc@loc.com</title>
</head>
<body>
    <h1>Vehicles owned by <?= htmlspecialchars($user_email) ?></h1>
    <?php if (empty($vehicles)): ?>
        <p>No vehicles found for this user.</p>
    <?php else: ?>
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Price Per Day</th>
                    <th>Availability</th>
                    <th>Characteristics</th>
                    <th>Image Path</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vehicles as $vehicle): ?>
                <tr>
                    <td><?= htmlspecialchars($vehicle['id']) ?></td>
                    <td><?= htmlspecialchars($vehicle['nom']) ?></td>
                    <td><?= htmlspecialchars($vehicle['type']) ?></td>
                    <td><?= htmlspecialchars($vehicle['prix_par_jour']) ?> €</td>
                    <td><?= $vehicle['disponibilite'] ? 'Available' : 'Unavailable' ?></td>
                    <td><?= htmlspecialchars($vehicle['caracteristiques']) ?></td>
                    <td><?= htmlspecialchars($vehicle['image_path']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <a href="locataire_dashboard.php">Back to Dashboard</a>
</body>
</html>

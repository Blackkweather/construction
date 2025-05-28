<?php
require 'auth.php';
requireLogin();
requireRole('admin');
include 'header.php';
include 'db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Suppression de véhicule
    if (isset($_POST['delete_vehicle_id'])) {
        $delete_id = (int)$_POST['delete_vehicle_id'];
        $stmt = $pdo->prepare('DELETE FROM vehicules WHERE id = ?');
        if ($stmt->execute([$delete_id])) {
            $message = "Véhicule supprimé avec succès.";
        } else {
            $message = "Échec de la suppression du véhicule.";
        }
    }
}

try {
    $stmt = $pdo->query('SELECT * FROM vehicules ORDER BY id ASC');
    $vehicles = $stmt->fetchAll();
} catch (Exception $e) {
    $message = 'Erreur lors de la récupération des véhicules : ' . htmlspecialchars($e->getMessage());
    $vehicles = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Gérer les véhicules</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <h1>Gestion des véhicules</h1>
    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>
    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Type</th>
                <th>Prix par jour</th>
                <th>Disponibilité</th>
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
                <td><?php echo $vehicle['disponibilite'] ? 'Disponible' : 'Indisponible'; ?></td>
                <td>
                    <a href="edit_vehicle.php?id=<?php echo $vehicle['id']; ?>" class="btn btn-sm btn-primary">Modifier</a>
                    <form method="post" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce véhicule ?');" style="display:inline;">
                        <input type="hidden" name="delete_vehicle_id" value="<?php echo $vehicle['id']; ?>" />
                        <button type="submit">Supprimer</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a href="admin_dashboard.php">Retour au tableau de bord</a>
</body>
</html>

<?php
require 'auth.php';
requireLogin();
requireRole('admin');
include 'header.php';
include 'db.php';

$message = '';

// Fetch users and vehicles for selection
try {
    $stmtUsers = $pdo->query('SELECT id, nom FROM utilisateurs ORDER BY nom ASC');
    $users = $stmtUsers->fetchAll();
} catch (Exception $e) {
    $users = [];
}

try {
    $stmtVehicles = $pdo->query('SELECT id, nom FROM vehicules WHERE disponibilite = 1 ORDER BY nom ASC');
    $vehicles = $stmtVehicles->fetchAll();
} catch (Exception $e) {
    $vehicles = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = (int)($_POST['user_id'] ?? 0);
    $vehicle_id = (int)($_POST['vehicle_id'] ?? 0);
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $total_price = floatval($_POST['total_price'] ?? 0);
    $status = $_POST['status'] ?? 'pending';

    if ($user_id && $vehicle_id && $start_date && $end_date && $total_price > 0) {
        $allowed_statuses = ['pending', 'confirmed', 'cancelled'];
        if (!in_array($status, $allowed_statuses)) {
            $message = "Statut invalide.";
        } else {
            $stmt = $pdo->prepare('INSERT INTO reservations (user_id, vehicle_id, start_date, end_date, total_price, status) VALUES (?, ?, ?, ?, ?, ?)');
            if ($stmt->execute([$user_id, $vehicle_id, $start_date, $end_date, $total_price, $status])) {
                $message = "Réservation ajoutée avec succès.";
            } else {
                $message = "Erreur lors de l'ajout de la réservation.";
            }
        }
    } else {
        $message = "Veuillez remplir tous les champs obligatoires.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Ajouter une réservation</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <h1>Ajouter une réservation</h1>
    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <form method="post">
        <label for="user_id">Utilisateur :</label><br />
        <select id="user_id" name="user_id" required>
            <option value="">Sélectionnez un utilisateur</option>
            <?php foreach ($users as $user): ?>
                <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['nom']); ?></option>
            <?php endforeach; ?>
        </select><br />

        <label for="vehicle_id">Véhicule :</label><br />
        <select id="vehicle_id" name="vehicle_id" required>
            <option value="">Sélectionnez un véhicule</option>
            <?php foreach ($vehicles as $vehicle): ?>
                <option value="<?php echo $vehicle['id']; ?>"><?php echo htmlspecialchars($vehicle['nom']); ?></option>
            <?php endforeach; ?>
        </select><br />

        <label for="start_date">Date début :</label><br />
        <input type="date" id="start_date" name="start_date" required /><br />

        <label for="end_date">Date fin :</label><br />
        <input type="date" id="end_date" name="end_date" required /><br />

        <label for="total_price">Prix total (€) :</label><br />
        <input type="number" step="0.01" id="total_price" name="total_price" required /><br />

        <label for="status">Statut :</label><br />
        <select id="status" name="status" required>
            <option value="pending">En attente</option>
            <option value="confirmed">Confirmée</option>
            <option value="cancelled">Annulée</option>
        </select><br /><br />

        <button type="submit">Ajouter</button>
    </form>
    <br />
    <a href="manage_bookings.php">Retour à la gestion des réservations</a>
</body>
</html>

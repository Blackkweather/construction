<?php
require 'auth.php';
requireLogin();
requireRole('admin');
include 'header.php';
include 'db.php';

$message = '';

if (!isset($_GET['id'])) {
    header('Location: manage_vehicules.php');
    exit;
}

$vehicle_id = (int)$_GET['id'];

try {
    $stmt = $pdo->prepare('SELECT * FROM vehicules WHERE id = ?');
    $stmt->execute([$vehicle_id]);
    $vehicle = $stmt->fetch();
    if (!$vehicle) {
        header('Location: manage_vehicules.php');
        exit;
    }
} catch (Exception $e) {
    $message = 'Erreur lors de la récupération du véhicule : ' . htmlspecialchars($e->getMessage());
    $vehicle = null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $type = trim($_POST['type'] ?? '');
    $prix_par_jour = floatval($_POST['prix_par_jour'] ?? 0);
    $disponibilite = isset($_POST['disponibilite']) ? 1 : 0;
    $image_path = trim($_POST['image_path'] ?? '');

    // Handle image upload
    if (isset($_FILES['new_image']) && $_FILES['new_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        $ext = pathinfo($_FILES['new_image']['name'], PATHINFO_EXTENSION);
        $new_filename = uniqid() . '.' . $ext;
        $target_path = $upload_dir . $new_filename;
        if (move_uploaded_file($_FILES['new_image']['tmp_name'], $target_path)) {
            $image_path = $target_path;
        } else {
            $message = "Erreur lors du téléchargement de l'image.";
        }
    }

    if ($nom && $type && $prix_par_jour > 0) {
        $stmt = $pdo->prepare('UPDATE vehicules SET nom = ?, type = ?, prix_par_jour = ?, disponibilite = ?, image_path = ? WHERE id = ?');
        if ($stmt->execute([$nom, $type, $prix_par_jour, $disponibilite, $image_path, $vehicle_id])) {
            $message = "Véhicule mis à jour avec succès.";
            // Refresh vehicle data
            $stmt = $pdo->prepare('SELECT * FROM vehicules WHERE id = ?');
            $stmt->execute([$vehicle_id]);
            $vehicle = $stmt->fetch();
        } else {
            $message = "Erreur lors de la mise à jour du véhicule.";
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
    <title>Modifier un véhicule</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <h1>Modifier un véhicule</h1>
    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <?php if ($vehicle): ?>
    <form method="post" enctype="multipart/form-data">
        <label for="nom">Nom :</label><br />
        <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($vehicle['nom']); ?>" required /><br />

        <label for="type">Type :</label><br />
        <input type="text" id="type" name="type" value="<?php echo htmlspecialchars($vehicle['type']); ?>" required /><br />

        <label for="prix_par_jour">Prix par jour (€) :</label><br />
        <input type="number" step="0.01" id="prix_par_jour" name="prix_par_jour" value="<?php echo htmlspecialchars($vehicle['prix_par_jour']); ?>" required /><br />

        <label for="disponibilite">Disponible :</label>
        <input type="checkbox" id="disponibilite" name="disponibilite" <?php if ($vehicle['disponibilite']) echo 'checked'; ?> /><br />

        <label for="image_path">Chemin de l'image :</label><br />
        <input type="text" id="image_path" name="image_path" value="<?php echo htmlspecialchars($vehicle['image_path']); ?>" />
        <?php if (!empty($vehicle['image_path']) && file_exists($vehicle['image_path'])): ?>
            <br /><img src="<?php echo htmlspecialchars($vehicle['image_path']); ?>" alt="Image actuelle" style="max-width:200px;max-height:120px;" />
        <?php endif; ?><br />
        <label for="new_image">Ou téléverser une nouvelle image :</label><br />
        <input type="file" id="new_image" name="new_image" accept="image/*" /><br /><br />

        <button type="submit">Mettre à jour</button>
    </form>
    <?php endif; ?>
    <br />
    <a href="manage_vehicules.php">Retour à la gestion des véhicules</a>
</body>
</html>

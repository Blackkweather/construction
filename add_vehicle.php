<?php
require 'auth.php';
requireLogin();
requireRole('admin');
include 'header.php';
include 'db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $type = trim($_POST['type'] ?? '');
    $prix_par_jour = floatval($_POST['prix_par_jour'] ?? 0);
    $disponibilite = isset($_POST['disponibilite']) ? 1 : 0;
    $image_path = trim($_POST['image_path'] ?? '');

    if ($nom && $type && $prix_par_jour > 0) {
        $stmt = $pdo->prepare('INSERT INTO vehicules (nom, type, prix_par_jour, disponibilite, image_path) VALUES (?, ?, ?, ?, ?)');
        if ($stmt->execute([$nom, $type, $prix_par_jour, $disponibilite, $image_path])) {
            $message = "Véhicule ajouté avec succès.";
        } else {
            $message = "Erreur lors de l'ajout du véhicule.";
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
    <title>Ajouter un véhicule</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <h1>Ajouter un véhicule</h1>
    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <form method="post">
        <label for="nom">Nom :</label><br />
        <input type="text" id="nom" name="nom" required /><br />

        <label for="type">Type :</label><br />
        <input type="text" id="type" name="type" required /><br />

        <label for="prix_par_jour">Prix par jour (€) :</label><br />
        <input type="number" step="0.01" id="prix_par_jour" name="prix_par_jour" required /><br />

        <label for="disponibilite">Disponible :</label>
        <input type="checkbox" id="disponibilite" name="disponibilite" checked /><br />

        <label for="image_path">Chemin de l'image :</label><br />
        <input type="text" id="image_path" name="image_path" placeholder="uploads/image.jpg" /><br /><br />

        <button type="submit">Ajouter</button>
    </form>
    <br />
    <a href="manage_vehicules.php">Retour à la gestion des véhicules</a>
</body>
</html>

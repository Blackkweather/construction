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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un véhicule</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1a3a6c;
            --secondary: #f39c12;
            --light: #f8f9fa;
            --dark: #2c3e50;
        }
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f7fa;
            color: var(--dark);
            padding-top: 80px;
        }
        .container {
            max-width: 700px;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        h1 {
            color: var(--primary);
            font-weight: 700;
            text-align: center;
            margin-bottom: 30px;
        }
        .form-label {
            font-weight: 600;
            color: var(--dark);
        }
        .form-control, .form-check-input {
            border-radius: 6px;
            padding: 10px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        .form-control:focus, .form-check-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 5px rgba(26, 58, 108, 0.3);
        }
        .btn-primary {
            background-color: var(--primary);
            border: none;
            padding: 12px 25px;
            font-weight: 600;
            border-radius: 6px;
            transition: all 0.3s ease;
            width: 100%;
        }
        .btn-primary:hover {
            background-color: #0f2a52;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .alert {
            border-radius: 6px;
            margin-bottom: 20px;
        }
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Ajouter un véhicule</h1>
        <?php if ($message): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="mb-3">
                <label for="nom" class="form-label">Nom</label>
                <input type="text" id="nom" name="nom" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="type" class="form-label">Type</label>
                <input type="text" id="type" name="type" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="prix_par_jour" class="form-label">Prix par jour (€)</label>
                <input type="number" step="0.01" id="prix_par_jour" name="prix_par_jour" class="form-control" required>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" id="disponibilite" name="disponibilite" class="form-check-input" checked>
                <label for="disponibilite" class="form-check-label">Disponible</label>
            </div>
            <div class="mb-3">
                <label for="image_path" class="form-label">Chemin de l'image</label>
                <input type="text" id="image_path" name="image_path" class="form-control" placeholder="Uploads/image.jpg">
            </div>
            <button type="submit" class="btn btn-primary">Ajouter</button>
        </form>
        <div class="text-center mt-4">
            <a href="manage_vehicules.php" class="btn btn-outline-primary">Retour à la gestion des véhicules</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
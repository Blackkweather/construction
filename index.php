<?php
include 'header.php';
include 'db.php';

try {
    $stmt = $pdo->query('SELECT * FROM vehicules WHERE disponibilite = 1 ORDER BY id DESC LIMIT 6');
    $vehicles = $stmt->fetchAll();
} catch (Exception $e) {
    $vehicles = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Location Matériel Construction - Accueil</title>
    <link rel="stylesheet" href="style.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        .vehicle-card {
            transition: transform 0.3s ease;
            cursor: pointer;
        }
        .vehicle-card:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 16px rgba(0,0,0,0.3);
        }
        .hero-section {
            background: url('uploads/hero_background.jpg') no-repeat center center;
            background-size: cover;
            color: white;
            padding: 100px 20px;
            text-align: center;
        }
        .hero-section h1 {
            font-size: 3rem;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.7);
        }
        .hero-section p {
            font-size: 1.5rem;
            margin-top: 20px;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.6);
        }
    </style>
</head>
<body>
    <div class="hero-section">
        <h1>Bienvenue sur Location Construction</h1>
        <p>Votre partenaire de confiance pour la location de matériel de chantier</p>
        <a href="listing.php" class="btn btn-primary btn-lg mt-4">Voir les véhicules</a>
    </div>

    <div class="container mt-5">
        <h2 class="mb-4">Véhicules en vedette</h2>
        <?php if (count($vehicles) === 0): ?>
            <p>Aucun véhicule disponible pour le moment.</p>
        <?php else: ?>
            <div class="row">
                <?php foreach ($vehicles as $vehicle): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card vehicle-card h-100">
                            <?php if (!empty($vehicle['image_path']) && file_exists($vehicle['image_path'])): ?>
                                <img src="<?php echo htmlspecialchars($vehicle['image_path']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($vehicle['nom']); ?>" />
                            <?php else: ?>
                                <img src="https://via.placeholder.com/400x200?text=Pas+d'image" class="card-img-top" alt="Pas d'image" />
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($vehicle['nom']); ?></h5>
                                <p class="card-text"><strong>Type :</strong> <?php echo htmlspecialchars($vehicle['type']); ?></p>
                                <p class="card-text"><strong>Prix par jour :</strong> €<?php echo number_format($vehicle['prix_par_jour'], 2); ?></p>
                                <a href="rent_vehicle.php?id=<?php echo $vehicle['id']; ?>" class="btn btn-primary">Louer maintenant</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

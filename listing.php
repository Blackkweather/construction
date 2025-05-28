<?php
include 'header.php';
include 'db.php';

try {
    $stmt = $pdo->query('SELECT * FROM vehicules ORDER BY id DESC');
    $vehicules = $stmt->fetchAll();
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Erreur lors de la récupération des véhicules : ' . htmlspecialchars($e->getMessage()) . '</div>';
    $vehicules = [];
}
?>

<h2>Véhicules disponibles</h2>

<?php if (count($vehicules) === 0): ?>
    <p>Aucun véhicule disponible.</p>
<?php else: ?>
    <div class="row">
        <?php foreach ($vehicules as $vehicule): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <?php if (!empty($vehicule['image_path']) && file_exists($vehicule['image_path'])): ?>
                        <img src="<?php echo htmlspecialchars($vehicule['image_path']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($vehicule['nom']); ?>">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/400x200?text=Pas+d'image" class="card-img-top" alt="Pas d'image">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($vehicule['nom']); ?></h5>
                        <p class="card-text"><strong>Type :</strong> <?php echo htmlspecialchars($vehicule['type']); ?></p>
                        <p class="card-text"><strong>Prix par jour :</strong> €<?php echo number_format($vehicule['prix_par_jour'], 2); ?></p>
                        <p class="card-text"><strong>Année :</strong> <?php echo htmlspecialchars($vehicule['annee']); ?></p>
                        <p class="card-text"><strong>Puissance moteur :</strong> <?php echo htmlspecialchars($vehicule['puissance_moteur']); ?> CV</p>
                        <p class="card-text"><strong>Type de carburant :</strong> <?php echo htmlspecialchars($vehicule['type_carburant']); ?></p>
                        <p class="card-text"><strong>Capacité de poids :</strong> <?php echo htmlspecialchars($vehicule['capacite_poids']); ?> tonnes</p>
                        <p class="card-text"><strong>Dimensions :</strong> <?php echo htmlspecialchars($vehicule['dimensions']); ?></p>
                        <p class="card-text"><strong>Caractéristiques :</strong> <?php echo nl2br(htmlspecialchars($vehicule['caracteristiques'])); ?></p>
                        <form action="rent_vehicle.php" method="GET">
                            <input type="hidden" name="id" value="<?php echo $vehicule['id']; ?>">
                            <button type="submit" class="btn btn-primary">Louer ce véhicule</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include 'footer.php'; ?>

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

<h2>Véhicules disponibles - DataTable Example</h2>

<?php if (count($vehicules) === 0): ?>
    <p>Aucun véhicule disponible.</p>
<?php else: ?>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.css">
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.js"></script>

    <table id="vehiculesTable" class="display" style="width:100%">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Type</th>
                <th>Prix par jour (€)</th>
                <th>Année</th>
                <th>Puissance moteur (CV)</th>
                <th>Type de carburant</th>
                <th>Capacité de poids (tonnes)</th>
                <th>Dimensions</th>
                <th>Caractéristiques</th>
                <th>Image</th>
                <th>Louer</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($vehicules as $vehicule): ?>
                <tr>
                    <td><?php echo htmlspecialchars($vehicule['nom']); ?></td>
                    <td><?php echo htmlspecialchars($vehicule['type']); ?></td>
                    <td><?php echo number_format($vehicule['prix_par_jour'], 2); ?></td>
                    <td><?php echo htmlspecialchars($vehicule['annee']); ?></td>
                    <td><?php echo htmlspecialchars($vehicule['puissance_moteur']); ?></td>
                    <td><?php echo htmlspecialchars($vehicule['type_carburant']); ?></td>
                    <td><?php echo htmlspecialchars($vehicule['capacite_poids']); ?></td>
                    <td><?php echo htmlspecialchars($vehicule['dimensions']); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($vehicule['caracteristiques'])); ?></td>
                    <td>
                        <?php if (!empty($vehicule['image_path']) && file_exists($vehicule['image_path'])): ?>
                            <img src="<?php echo htmlspecialchars($vehicule['image_path']); ?>" alt="<?php echo htmlspecialchars($vehicule['nom']); ?>" style="max-width:100px; max-height:60px;">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/100x60?text=Pas+d'image" alt="Pas d'image">
                        <?php endif; ?>
                    </td>
                    <td>
                        <form action="rent_vehicle.php" method="GET">
                            <input type="hidden" name="id" value="<?php echo $vehicule['id']; ?>">
                            <button type="submit" class="btn btn-primary btn-sm">Louer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        $(document).ready(function () {
            $('#vehiculesTable').DataTable();
        });
    </script>
<?php endif; ?>

<?php include 'footer.php'; ?>

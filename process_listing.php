<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $vehicleType = trim($_POST['vehicle_type'] ?? '');
    $brandModel = trim($_POST['brand_model'] ?? '');
    $year = intval($_POST['year'] ?? 0);
    $enginePower = intval($_POST['engine_power'] ?? 0);
    $fuelType = trim($_POST['fuel_type'] ?? '');
    $weightCapacity = floatval($_POST['weight_capacity'] ?? 0);
    $dimensions = trim($_POST['dimensions'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $features = trim($_POST['features'] ?? '');

    // Validate inputs
    if (empty($title) || empty($description) || empty($vehicleType) || empty($brandModel) || $year <= 0 || $enginePower <= 0 || $weightCapacity <= 0 || empty($dimensions) || $price <= 0) {
        die('Invalid input data.');
    }

    // Handle image upload
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        die('Image upload failed.');
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $fileType = $_FILES['image']['type'];
    if (!in_array($fileType, $allowedTypes)) {
        die('Only JPG, PNG, and GIF images are allowed.');
    }

    $uploadsDir = 'uploads';
    if (!is_dir($uploadsDir)) {
        mkdir($uploadsDir, 0755, true);
    }

    $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
    $targetFilePath = $uploadsDir . '/' . $fileName;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
        die('Failed to move uploaded file.');
    }

    // Insert into database
    $sql = "INSERT INTO vehicules (nom, type, prix_par_jour, disponibilite, proprietaire_id, image_path, description, annee, puissance_moteur, type_carburant, capacite_poids, dimensions, caracteristiques) 
            VALUES (:nom, :type, :prix_par_jour, :disponibilite, :proprietaire_id, :image_path, :description, :annee, :puissance_moteur, :type_carburant, :capacite_poids, :dimensions, :caracteristiques)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nom' => $title,
        ':type' => $vehicleType,
        ':prix_par_jour' => $price,
        ':disponibilite' => true,
        ':proprietaire_id' => $_SESSION['user_id'], // Assuming the user is logged in
        ':image_path' => $targetFilePath,
        ':description' => $description,
        ':annee' => $year,
        ':puissance_moteur' => $enginePower,
        ':type_carburant' => $fuelType,
        ':capacite_poids' => $weightCapacity,
        ':dimensions' => $dimensions,
        ':caracteristiques' => $features,
    ]);

    header('Location: listing.php');
    exit;
} else {
    die('Invalid request method.');
}
?>

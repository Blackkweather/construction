<?php
// scripts/clean_and_import_vehicles.php
// Script to delete reservations and vehicles without pictures, then import new vehicles

$host = 'localhost';
$db   = 'construction_rental';
$user = 'root';
$pass = 'root';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Delete all reservations
    $pdo->exec("DELETE FROM reservations");
    echo "Deleted all reservations.\n";

    // Delete vehicles without pictures (empty or NULL image_path)
    $pdo->exec("DELETE FROM vehicules WHERE image_path IS NULL OR image_path = ''");
    echo "Deleted vehicles without pictures.\n";

    // TODO: Scrape and import new vehicles from external website
    // This part requires scraping logic and image download, which can be done separately.

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
    exit(1);
}
?>

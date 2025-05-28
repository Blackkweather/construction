<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch vehicle details
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM vehicules WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$id]);
    $vehicle = $stmt->fetch();

    if (!$vehicle) {
        echo "Vehicle not found.";
        exit();
    }
} else {
    echo "No vehicle ID provided.";
    exit();
}

$message = '';
$total_price = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $days = (int)$_POST['days'];
    if ($days < 1) {
        $message = 'Please enter a valid number of days.';
    } else {
        $total_price = $days * $vehicle['prix_par_jour'];

        // Insert reservation into database
        $user_id = $_SESSION['user_id'];
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d', strtotime("+$days days"));
        $status = 'pending';

        $insertQuery = "INSERT INTO reservations (vehicle_id, user_id, start_date, end_date, total_price, status) VALUES (?, ?, ?, ?, ?, ?)";
        $insertStmt = $pdo->prepare($insertQuery);
        try {
            $insertStmt->execute([$id, $user_id, $start_date, $end_date, $total_price, $status]);
            $message = 'Reservation successful!';
        } catch (PDOException $e) {
            $message = 'Failed to make reservation: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Réserver un véhicule</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Flatpickr CSS (Calendar) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #ff6b35;
            --secondary-color: #2e4057;
            --accent-color: #f7c59f;
            --light-color: #efefef;
            --dark-color: #2b2d42;
            --success-color: #06d6a0;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', 'Arial', sans-serif;
        }
        
        .container {
            max-width: 800px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .header {
            background-color: var(--secondary-color);
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
            margin: -30px -30px 30px -30px;
        }
        
        h1 {
            color: white;
            margin-bottom: 10px;
            text-align: center;
            font-weight: 700;
            font-size: 2rem;
        }
        
        .vehicle-info {
            background-color: var(--light-color);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
        }
        
        .vehicle-info p {
            font-size: 18px;
            margin: 10px 0;
            color: var(--dark-color);
        }
        
        .vehicle-image {
            position: relative;
            overflow: hidden;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .vehicle-image img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .vehicle-image:hover img {
            transform: scale(1.05);
        }
        
        .booking-form {
            background-color: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .form-label {
            font-weight: 600;
            display: block;
            margin-bottom: 10px;
            color: var(--secondary-color);
        }
        
        .form-control {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(255, 107, 53, 0.25);
            outline: none;
        }
        
        .btn-reserve {
            margin-top: 20px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 14px 20px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn-reserve:hover {
            background-color: #e85a2a;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 53, 0.3);
        }
        
        .total-price {
            margin-top: 25px;
            padding: 15px;
            background-color: var(--light-color);
            border-radius: 8px;
        }
        
        .total-price h2 {
            font-size: 24px;
            font-weight: 700;
            color: var(--success-color);
            text-align: center;
            margin: 0;
        }
        
        .message {
            margin-top: 20px;
            padding: 15px;
            font-size: 18px;
            text-align: center;
            border-radius: 8px;
            background-color: #f8d7da;
            color: #842029;
        }
        
        .success {
            background-color: #d1e7dd;
            color: #0f5132;
        }
        
        .back-link {
            display: block;
            margin-top: 30px;
            text-align: center;
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .back-link:hover {
            color: var(--primary-color);
            transform: translateX(-5px);
        }
        
        /* Flatpickr Calendar Styles */
        .flatpickr-input {
            background-color: white !important;
            cursor: pointer;
        }
        
        @media (max-width: 768px) {
            .container {
                margin: 20px 10px;
                padding: 20px;
            }
            
            .header {
                padding: 15px;
                margin: -20px -20px 20px -20px;
            }
            
            h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Réserver le véhicule : <?php echo htmlspecialchars($vehicle['nom']); ?></h1>
        </div>
        
        <div class="vehicle-info">
            <?php if (!empty($vehicle['image_path']) && file_exists($vehicle['image_path'])): ?>
                <div class="vehicle-image">
                    <img src="<?php echo htmlspecialchars($vehicle['image_path']); ?>" alt="<?php echo htmlspecialchars($vehicle['nom']); ?>" />
                </div>
            <?php endif; ?>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Type :</strong> <?php echo htmlspecialchars($vehicle['type']); ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Prix par jour :</strong> <span class="badge bg-primary"><?php echo htmlspecialchars($vehicle['prix_par_jour']); ?> €</span></p>
                </div>
            </div>
        </div>
        
        <div class="booking-form">
            <form action="" method="POST">
                <div class="mb-3">
                    <label for="datepicker" class="form-label">Sélectionnez une période :</label>
                    <input type="text" id="datepicker" class="form-control" placeholder="Choisir des dates" readonly>
                </div>
                <div class="mb-3">
                    <label for="days" class="form-label">Nombre de jours :</label>
                    <input type="number" class="form-control" name="days" id="days" min="1" required readonly>
                </div>
                <button type="submit" class="btn-reserve">Réserver maintenant</button>
            </form>
        </div>
        
        <?php if ($message): ?>
            <div class="message <?php echo $message === 'Reservation successful!' ? 'success' : ''; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($total_price > 0): ?>
            <div class="total-price">
                <h2>Prix total : <?php echo $total_price; ?> €</h2>
            </div>
        <?php endif; ?>
        
        <a href="client_dashboard.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Retour au tableau de bord
        </a>
    </div>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Flatpickr JS (Calendar) -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/fr.js"></script> <!-- French locale -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize Flatpickr (Calendar)
            flatpickr("#datepicker", {
                mode: "range",          // Allow date range selection
                locale: "fr",           // French language
                minDate: "today",       // Disable past dates
                dateFormat: "Y-m-d",    // Format: YYYY-MM-DD
                onChange: function(selectedDates) {
                    if (selectedDates.length === 2) {
                        const startDate = selectedDates[0];
                        const endDate = selectedDates[1];
                        const diffDays = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;
                        document.getElementById('days').value = diffDays;
                    }
                }
            });
        });
    </script>
</body>
</html>
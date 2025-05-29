<?php
require 'auth.php';
requireLogin();
requireRole('admin');
include 'header.php';
include 'db.php';

$message = '';

try {
    $today = date('Y-m-d');
    $stmt = $pdo->prepare('SELECT r.id, u.nom AS user_name, v.nom AS vehicle_name, r.start_date, r.end_date, r.total_price, r.status 
                           FROM reservations r 
                           JOIN utilisateurs u ON r.user_id = u.id 
                           JOIN vehicules v ON r.vehicle_id = v.id 
                           WHERE r.end_date < ? 
                           ORDER BY r.end_date DESC');
    $stmt->execute([$today]);
    $old_bookings = $stmt->fetchAll();
} catch (Exception $e) {
    $message = 'Erreur lors de la récupération de l\'historique des réservations : ' . htmlspecialchars($e->getMessage());
    $old_bookings = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des réservations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1a3a6c;
            --secondary: #f39c12;
            --light: #f8f9fa;
            --dark: #2c3e50;
            --gray: #6c757d;
        }
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f7fa;
            color: var(--dark);
            padding-top: 80px;
        }
        .container {
            max-width: 1200px;
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
        .table {
            border-radius: 8px;
            overflow: hidden;
        }
        .table thead {
            background: var(--primary);
            color: white;
        }
        .table tbody tr {
            transition: background 0.3s ease;
        }
        .table tbody tr:hover {
            background: #f1f3f5;
        }
        .badge {
            font-size: 0.9rem;
            padding: 0.5em 1em;
        }
        .btn-primary {
            background-color: var(--primary);
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            transition: all 0.3s ease;
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
            .table {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Historique des réservations</h1>
        <?php if ($message): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <?php if (count($old_bookings) === 0): ?>
            <div class="alert alert-warning text-center">Aucune réservation ancienne trouvée.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table id="bookingsTable" class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Utilisateur</th>
                            <th>Véhicule</th>
                            <th>Date début</th>
                            <th>Date fin</th>
                            <th>Prix total</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($old_bookings as $booking): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($booking['id']); ?></td>
                                <td><?php echo htmlspecialchars($booking['user_name']); ?></td>
                                <td><?php echo htmlspecialchars($booking['vehicle_name']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($booking['start_date'])); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($booking['end_date'])); ?></td>
                                <td>€<?php echo number_format($booking['total_price'], 2); ?></td>
                                <td>
                                    <span class="badge <?php echo $booking['status'] == 'confirmed' ? 'bg-success' : ($booking['status'] == 'pending' ? 'bg-warning' : 'bg-danger'); ?>">
                                        <?php echo htmlspecialchars(ucfirst($booking['status'])); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        <div class="text-center mt-4">
            <a href="admin_dashboard.php" class="btn btn-primary">Retour au tableau de bord</a>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#bookingsTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
                },
                "pageLength": 10,
                "order": [[3, "desc"]]
            });
        });
    </script>
</body>
</html>
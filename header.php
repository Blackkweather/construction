<?php
session_start();
include 'db.php';

// Vérification de la connexion utilisateur
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['username'] : '';
$userRole = $isLoggedIn ? $_SESSION['role'] : '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>LocationPro - Matériel professionnel pour le BTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet" />
    <style>
        html {
            scroll-behavior: smooth;
        }
        :root {
            --primary: #1a3a6c;
            --secondary: #f39c12;
            --accent: #e74c3c;
            --light: #f8f9fa;
            --dark: #2c3e50;
            --gray: #6c757d;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Roboto', sans-serif;
            color: var(--dark);
            background-color: #f5f7fa;
            line-height: 1.6;
        }
        .navbar {
            background-color: white;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }
        .navbar.sticky-nav {
            background-color: rgba(255, 255, 255, 0.95);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            animation: slideDown 0.5s ease forwards;
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.8rem;
            color: var(--primary);
            display: flex;
            align-items: center;
        }
        .navbar-brand i {
            color: var(--secondary);
            margin-right: 10px;
        }
        .nav-link {
            font-weight: 500;
            color: var(--dark);
            margin: 0 10px;
            padding: 8px 15px;
            border-radius: 4px;
            transition: all 0.3s ease;
        }
        .nav-link:hover {
            background-color: var(--primary);
            color: white;
        }
    </style>
</head>
<body>
<header>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-hard-hat"></i>
                LOCATION<span style="color: var(--secondary);">PRO</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="listing.php">Nos véhicules</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#how-it-works">Comment ça marche</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#testimonials">Témoignages</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact</a>
                    </li>
                </ul>
                <div class="ms-lg-3 mt-3 mt-lg-0">
                    <?php if($isLoggedIn): ?>
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" id="userMenu" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-2"></i><?= htmlspecialchars($userName) ?>
                            </button>
                            <ul class="dropdown-menu">
<?php
$dashboardLink = 'dashboard.php';
if ($isLoggedIn) {
    if ($userRole === 'admin') {
        $dashboardLink = 'admin_dashboard.php';
    } elseif ($userRole === 'client') {
        $dashboardLink = 'client_dashboard.php';
    } elseif ($userRole === 'locataire') {
        $dashboardLink = 'locataire_dashboard.php';
    }
}
?>
<li><a class="dropdown-item" href="<?= htmlspecialchars($dashboardLink) ?>">Tableau de bord</a></li>
<li><a class="dropdown-item" href="reservation_history.php">Mes réservations</a></li>
                                <?php if($userRole === 'locataire' || $userRole === 'admin'): ?>
                                    <li><a class="dropdown-item" href="add_listing.php">Ajouter un véhicule</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php">Déconnexion</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-primary">
                            <i class="fas fa-user me-2"></i>Connexion
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
</header>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Sticky Navbar
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.navbar');
        if (window.scrollY > 50) {
            navbar.classList.add('sticky-nav');
        } else {
            navbar.classList.remove('sticky-nav');
        }
    });
</script>
</body>
</html>

<?php
session_start();
include 'db.php';

// Récupération des véhicules disponibles
try {
    $stmt = $pdo->query('SELECT * FROM vehicules WHERE disponibilite = 1 ORDER BY id DESC LIMIT 6');
    $vehicles = $stmt->fetchAll();
} catch (Exception $e) {
    $vehicles = [];
}

// Récupération des témoignages depuis la base de données
try {
    $stmt = $pdo->query('SELECT * FROM testimonials ORDER BY created_at DESC');
    $testimonials = $stmt->fetchAll();
} catch (Exception $e) {
    $testimonials = [];
}

// Vérification de la connexion utilisateur
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['username'] : '';
$userRole = $isLoggedIn ? $_SESSION['role'] : '';

// Statistiques pour la section "Pourquoi nous choisir"
$stats = [
    'equipment' => 42,
    'clients' => 128,
    'locations' => 347,
    'support' => '24/7'
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LocationPro - Matériel professionnel pour le BTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <!-- Smooth Scrolling -->
    <style>
        html {
            scroll-behavior: smooth;
        }
    </style>
    <style>
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
        
        /* Navigation Bar */
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
        
        /* Button Styles */
        .btn-primary {
            background-color: var(--primary);
            border: none;
            padding: 12px 30px;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary:hover {
            background-color: #0f2a52;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        
        .btn-primary::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255, 255, 255, 0.2),
                transparent
            );
            transition: 0.5s;
        }
        
        .btn-primary:hover::after {
            left: 100%;
        }
        
        .btn-secondary {
            background-color: var(--secondary);
            border: none;
            padding: 12px 30px;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-secondary:hover {
            background-color: #e08e0b;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        
        .btn-secondary::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255, 255, 255, 0.2),
                transparent
            );
            transition: 0.5s;
        }
        
        .btn-secondary:hover::after {
            left: 100%;
        }
        
        /* Hero Section */
        .hero-section {
            position: relative;
            color: white;
            padding: 150px 0;
            text-align: center;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('https://images.unsplash.com/photo-1605100804763-247f67b3557e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1920&q=80') no-repeat center center;
            background-size: cover;
            filter: blur(8px);
            z-index: -2;
        }
        
        .hero-section::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: -1;
        }
        
        .hero-section h1 {
            font-size: 4rem;
            font-weight: 800;
            margin-bottom: 25px;
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.7);
            animation: fadeIn 1s ease-in;
        }
        
        .hero-section p {
            font-size: 1.6rem;
            max-width: 800px;
            margin: 0 auto 40px;
            text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.7);
            animation: fadeIn 1s ease-in 0.2s;
            animation-fill-mode: both;
        }
        
        .hero-btns {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
            animation: fadeIn 1s ease-in 0.4s;
            animation-fill-mode: both;
        }
        
        /* Features Section (Card Interactions) */
        .features-section {
            padding: 80px 0;
            background-color: white;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 60px;
            color: var(--primary);
        }
        
        .section-title h2 {
            font-weight: 700;
            position: relative;
            display: inline-block;
            padding-bottom: 15px;
        }
        
        .section-title h2:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--secondary);
        }
        
        .feature-box {
            text-align: center;
            padding: 30px 20px;
            border-radius: 10px;
            transition: all 0.3s ease;
            background-color: #fff;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            height: 100%;
            transform: translateY(0);
        }
        
        .feature-box:hover {
            transform: translateY(-15px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
        }
        
        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary), #2c5282);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 32px;
            transition: transform 0.3s ease;
        }
        
        .feature-box:hover .feature-icon {
            transform: scale(1.1);
        }
        
        .feature-box h3 {
            color: var(--primary);
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes slideDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .fade-in {
            animation: fadeIn 1s ease-in;
            animation-fill-mode: both;
        }
        
        .feature-box:nth-child(1) { animation-delay: 0.1s; }
        .feature-box:nth-child(2) { animation-delay: 0.2s; }
        .feature-box:nth-child(3) { animation-delay: 0.3s; }
        .feature-box:nth-child(4) { animation-delay: 0.4s; }
        
        /* Vehicles Section */
        .vehicles-section {
            padding: 80px 0;
            background-color: #f5f7fa;
        }
        
        .vehicle-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            height: 100%;
            transform: translateY(0);
        }
        
        .vehicle-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }
        
        .vehicle-img {
            height: 220px;
            width: 100%;
            object-fit: cover;
        }
        
        .vehicle-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background-color: var(--secondary);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 14px;
        }
        
        .vehicle-body {
            padding: 25px;
        }
        
        .vehicle-title {
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .vehicle-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .vehicle-price {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
        }
        
        .vehicle-price span {
            font-size: 1rem;
            font-weight: 400;
            color: var(--gray);
        }
        
        .vehicle-features {
            margin-bottom: 20px;
        }
        
        .vehicle-feature {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }
        
        .vehicle-feature i {
            color: var(--secondary);
            margin-right: 8px;
            width: 20px;
        }
        
        /* Stats Section */
        .stats-section {
            background-color: var(--primary);
            color: white;
            padding: 60px 0;
            text-align: center;
        }
        
        .stat-box {
            padding: 20px;
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--secondary);
        }
        
        /* Testimonials */
        .testimonials-section {
            padding: 80px 0;
            background-color: white;
        }
        
        .testimonial-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 30px;
            height: 100%;
            position: relative;
            border: 1px solid #eee;
        }
        
        .testimonial-card:before {
            content: '"';
            position: absolute;
            top: 20px;
            left: 20px;
            font-size: 80px;
            color: var(--secondary);
            opacity: 0.1;
            font-family: serif;
            line-height: 1;
        }
        
        .testimonial-rating {
            color: var(--secondary);
            margin-bottom: 15px;
        }
        
        .testimonial-author {
            display: flex;
            align-items: center;
            margin-top: 20px;
        }
        
        .testimonial-author img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 15px;
            border: 2px solid var(--secondary);
        }
        
        /* Contact CTA */
        .contact-cta {
            background: linear-gradient(135deg, var(--primary), #2c5282);
            color: white;
            padding: 80px 0;
            text-align: center;
        }
        
        .contact-cta h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }
        
        .contact-cta p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto 40px;
            opacity: 0.9;
        }
        
        /* Footer */
        .footer {
            background-color: #1a2a4c;
            color: white;
            padding: 70px 0 30px;
        }
        
        .footer h5 {
            color: white;
            margin-bottom: 25px;
            font-weight: 600;
            position: relative;
            padding-bottom: 10px;
        }
        
        .footer h5:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: var(--secondary);
        }
        
        .footer-links {
            list-style: none;
            padding: 0;
        }
        
        .footer-links li {
            margin-bottom: 12px;
        }
        
        .footer-links a {
            color: #bbb;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .footer-links a:hover {
            color: var(--secondary);
            padding-left: 5px;
        }
        
        .contact-info {
            color: #bbb;
            margin-bottom: 15px;
            display: flex;
            align-items: flex-start;
        }
        
        .contact-info i {
            color: var(--secondary);
            margin-right: 15px;
            margin-top: 5px;
        }
        
        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .social-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.3s ease;
        }
        
        .social-links a:hover {
            background-color: var(--secondary);
            transform: translateY(-3px);
        }
        
        .copyright {
            text-align: center;
            padding-top: 30px;
            margin-top: 50px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: #bbb;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero-section {
                padding: 100px 0;
            }
            
            .hero-section h1 {
                font-size: 2.8rem;
            }
            
            .hero-section p {
                font-size: 1.3rem;
            }
            
            .hero-btns {
                flex-direction: column;
                align-items: center;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
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
                                    <li><a class="dropdown-item" href="dashboard.php">Tableau de bord</a></li>
                                    <li><a class="dropdown-item" href="reservations.php">Mes réservations</a></li>
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

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1>Location de matériel professionnel pour le bâtiment</h1>
            <p>Des équipements de qualité pour tous vos chantiers, disponibles immédiatement et à prix compétitifs.</p>
            <div class="hero-btns">
                <a href="listing.php" class="btn btn-secondary btn-lg">
                    <i class="fas fa-search me-2"></i>Voir nos véhicules
                </a>
                <a href="#how-it-works" class="btn btn-outline-light btn-lg">
                    <i class="fas fa-question-circle me-2"></i>Comment louer ?
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <div class="section-title">
                <h2>Pourquoi nous choisir ?</h2>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="feature-box fade-in">
                        <div class="feature-icon">
                            <i class="fas fa-euro-sign"></i>
                        </div>
                        <h3>Tarifs Compétitifs</h3>
                        <p>Des prix transparents sans frais cachés. Location à la journée, semaine ou mois.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-box fade-in">
                        <div class="feature-icon">
                            <i class="fas fa-tools"></i>
                        </div>
                        <h3>Matériel Professionnel</h3>
                        <p>Équipements haut de gamme, régulièrement entretenus et contrôlés.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-box fade-in">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3>Assurance Incluse</h3>
                        <p>Toutes nos locations incluent une assurance responsabilité civile complète.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-box fade-in">
                        <div class="feature-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h3>Support 24/7</h3>
                        <p>Notre équipe technique est disponible pour vous aider à tout moment.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-box">
                        <div class="stat-number"><?= $stats['equipment'] ?>+</div>
                        <div>Équipements disponibles</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box">
                        <div class="stat-number"><?= $stats['clients'] ?></div>
                        <div>Clients satisfaits</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box">
                        <div class="stat-number"><?= $stats['locations'] ?></div>
                        <div>Locations réalisées</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box">
                        <div class="stat-number"><?= $stats['support'] ?></div>
                        <div>Support technique</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Vehicles Section -->
    <section class="vehicles-section">
        <div class="container">
            <div class="section-title">
                <h2>Nos véhicules en vedette</h2>
                <p class="text-muted">Équipements récents et parfaitement entretenus</p>
            </div>
            <div class="row g-4">
                <?php if (count($vehicles) > 0): ?>
                    <?php foreach ($vehicles as $vehicle): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="vehicle-card">
                                <div class="position-relative">
                                    <?php if (!empty($vehicle['image_path']) && file_exists($vehicle['image_path']) && $vehicle['image_path'] !== 'Uploads/no_image.jpg'): ?>
                                        <img src="<?= htmlspecialchars($vehicle['image_path']) ?>" class="vehicle-img" alt="<?= htmlspecialchars($vehicle['nom']) ?>">
                                    <?php else: ?>
                                        <img src="https://images.unsplash.com/photo-1581091226033-d5c48150dbaa?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80" class="vehicle-img" alt="Image du matériel">
                                    <?php endif; ?>
                                    <div class="vehicle-badge">Disponible</div>
                                </div>
                                <div class="vehicle-body">
                                    <h4 class="vehicle-title"><?= htmlspecialchars($vehicle['nom']) ?></h4>
                                    <div class="vehicle-meta">
                                        <div><i class="fas fa-gas-pump me-2"></i><?= htmlspecialchars($vehicle['type_carburant']) ?></div>
                                        <div><i class="fas fa-bolt me-2"></i><?= htmlspecialchars($vehicle['puissance_moteur']) ?> CV</div>
                                    </div>
                                    <div class="vehicle-price">€<?= number_format($vehicle['prix_par_jour'], 2) ?> <span>/jour</span></div>
                                    <div class="vehicle-features">
                                        <div class="vehicle-feature">
                                            <i class="fas fa-weight"></i>
                                            <span>Type: <?= htmlspecialchars($vehicle['type']) ?></span>
                                        </div>
                                        <div class="vehicle-feature">
                                            <i class="fas fa-cube"></i>
                                            <span>Capacité: <?= htmlspecialchars($vehicle['capacite_poids']) ?> tonnes</span>
                                        </div>
                                        <div class="vehicle-feature">
                                            <i class="fas fa-calendar-alt"></i>
                                            <span>Année: <?= htmlspecialchars($vehicle['annee']) ?></span>
                                        </div>
                                    </div>
                                    <a href="rent_vehicle.php?id=<?= $vehicle['id'] ?>" class="btn btn-primary w-100">
                                        <i class="fas fa-calendar-check me-2"></i>Réserver maintenant
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <h3>Aucun véhicule disponible pour le moment</h3>
                        <p class="text-muted">Revenez plus tard pour découvrir nos nouvelles disponibilités</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="text-center mt-5">
                <a href="listing.php" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-truck-moving me-2"></i>Voir tout notre parc
                </a>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="features-section">
        <div class="container">
            <div class="section-title">
                <h2>Comment louer en 3 étapes simples</h2>
                <p class="text-muted">Un processus rapide et sans tracas</p>
            </div>
            <div class="row text-center">
                <div class="col-md-4 mb-4">
                    <div class="feature-icon mx-auto mb-4" style="background: linear-gradient(135deg, var(--secondary), #e08e0b);">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>1. Trouvez votre matériel</h3>
                    <p>Parcourez notre catalogue et sélectionnez l'équipement dont vous avez besoin</p>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-icon mx-auto mb-4" style="background: linear-gradient(135deg, var(--primary), #2c5282);">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3>2. Réservez en ligne</h3>
                    <p>Choisissez vos dates et effectuez votre réservation en quelques clics</p>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-icon mx-auto mb-4" style="background: linear-gradient(135deg, #27ae60, #2ecc71);">
                        <i class="fas fa-truck-loading"></i>
                    </div>
                    <h3>3. Réceptionnez l'équipement</h3>
                    <p>Récupérez votre matériel sur notre site ou profitez de notre service de livraison</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="testimonials-section">
        <div class="container">
            <div class="section-title">
                <h2>Ce que disent nos clients</h2>
                <p class="text-muted">Des professionnels satisfaits de notre service</p>
            </div>
            <?php if (count($testimonials) > 0): ?>
                <div class="row g-4">
                    <?php foreach ($testimonials as $testimonial): ?>
                        <div class="col-md-4">
                            <div class="testimonial-card">
                                <div class="testimonial-rating">
                                    <?php for ($i = 0; $i < $testimonial['rating']; $i++): ?>
                                        <i class="fas fa-star"></i>
                                    <?php endfor; ?>
                                </div>
                                <p class="testimonial-text"><?= htmlspecialchars($testimonial['text']) ?></p>
                                <div class="testimonial-author">
                                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($testimonial['name']) ?>&background=random" alt="<?= htmlspecialchars($testimonial['name']) ?>">
                                    <div>
                                        <strong><?= htmlspecialchars($testimonial['name']) ?></strong>
                                        <div class="text-muted"><?= htmlspecialchars($testimonial['company']) ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <h4>Aucun témoignage disponible pour le moment</h4>
                    <p class="text-muted">Soyez le premier à partager votre expérience !</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Contact CTA -->
    <section class="contact-cta">
        <div class="container">
            <h2>Besoin d'un équipement spécifique ?</h2>
            <p>Notre équipe est à votre disposition pour vous conseiller et trouver la solution adaptée à votre chantier.</p>
            <a href="contact.php" class="btn btn-light btn-lg">
                <i class="fas fa-phone-alt me-2"></i>Contactez-nous
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-4">
                    <h5>Location Construction Pro</h5>
                    <p class="mt-4">Votre partenaire de confiance pour la location de matériel de chantier depuis 2010. Qualité, fiabilité et service client au cœur de notre métier.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h5>Liens rapides</h5>
                    <ul class="footer-links">
                        <li><a href="index.php">Accueil</a></li>
                        <li><a href="listing.php">Nos véhicules</a></li>
                        <li><a href="#how-it-works">Comment louer</a></li>
                        <li><a href="contact.php">Contact</a></li>
                        <li><a href="conditions.php">Conditions générales</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5>Catégories</h5>
                    <ul class="footer-links">
                        <li><a href="listing.php?category=terrassement">Engins de terrassement</a></li>
                        <li><a href="listing.php?category=transport">Transport de matériaux</a></li>
                        <li><a href="listing.php?category=beton">Centrales à béton</a></li>
                        <li><a href="listing.php?category=levage">Matériel de levage</a></li>
                        <li><a href="listing.php?category=outillage">Outillage électrique</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5>Contact</h5>
                    <div class="contact-info">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>123 Rue des Constructeurs, 75000 Paris, France</span>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-phone-alt"></i>
                        <span>+33 1 23 45 67 89</span>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-envelope"></i>
                        <span>contact@locationpro.fr</span>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-clock"></i>
                        <span>Lun-Ven: 7h-19h | Sam: 8h-12h</span>
                    </div>
                </div>
            </div>
            <div class="copyright">
                <p>© <?= date('Y') ?> Location Construction Pro. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

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
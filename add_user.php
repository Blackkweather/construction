<?php
require 'auth.php';
requireLogin();
requireRole('admin');
include 'header.php';
include 'db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $role = $_POST['role'] ?? '';

    if ($nom && $email && $mot_de_passe && $role) {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM utilisateurs WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            $message = "Un utilisateur avec cet email existe déjà.";
        } else {
            $hashed_password = password_hash($mot_de_passe, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)');
            if ($stmt->execute([$nom, $email, $hashed_password, $role])) {
                $message = "Utilisateur ajouté avec succès.";
            } else {
                $message = "Erreur lors de l'ajout de l'utilisateur.";
            }
        }
    } else {
        $message = "Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un utilisateur</title>
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
            padding-top: 0;
        }
        .container {
            max-width: 700px;
            padding: 30px;
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
        .form-control, .form-select {
            border-radius: 6px;
            padding: 10px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
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
        <h1>Ajouter un utilisateur</h1>
        <?php if ($message): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="mb-3">
                <label for="nom" class="form-label">Nom</label>
                <input type="text" id="nom" name="nom" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="mot_de_passe" class="form-label">Mot de passe</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Rôle</label>
                <select id="role" name="role" class="form-select" required>
                    <option value="">Sélectionnez un rôle</option>
                    <option value="admin">Admin</option>
                    <option value="client">Client</option>
                    <option value="locataire">Locataire</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Ajouter</button>
        </form>
        <div class="text-center mt-4">
            <a href="manage_users.php" class="btn btn-outline-primary">Retour à la gestion des utilisateurs</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
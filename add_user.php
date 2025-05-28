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
        // Check if email already exists
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
    <meta charset="UTF-8" />
    <title>Ajouter un utilisateur</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <h1>Ajouter un utilisateur</h1>
    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <form method="post">
        <label for="nom">Nom :</label><br />
        <input type="text" id="nom" name="nom" required /><br />

        <label for="email">Email :</label><br />
        <input type="email" id="email" name="email" required /><br />

        <label for="mot_de_passe">Mot de passe :</label><br />
        <input type="password" id="mot_de_passe" name="mot_de_passe" required /><br />

        <label for="role">Rôle :</label><br />
        <select id="role" name="role" required>
            <option value="">Sélectionnez un rôle</option>
            <option value="admin">Admin</option>
            <option value="client">Client</option>
            <option value="locataire">Locataire</option>
        </select><br /><br />

        <button type="submit">Ajouter</button>
    </form>
    <br />
    <a href="manage_users.php">Retour à la gestion des utilisateurs</a>
</body>
</html>

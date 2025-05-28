<?php
require 'auth.php';
requireLogin();
requireRole('admin');
include 'header.php';
include 'db.php';

$message = '';

if (!isset($_GET['id'])) {
    header('Location: manage_users.php');
    exit;
}

$user_id = (int)$_GET['id'];

try {
    $stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE id = ?');
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    if (!$user) {
        header('Location: manage_users.php');
        exit;
    }
} catch (Exception $e) {
    $message = 'Erreur lors de la récupération de l\'utilisateur : ' . htmlspecialchars($e->getMessage());
    $user = null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $role = $_POST['role'] ?? '';

    if ($nom && $email && $role) {
        // Check if email is used by another user
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM utilisateurs WHERE email = ? AND id != ?');
        $stmt->execute([$email, $user_id]);
        if ($stmt->fetchColumn() > 0) {
            $message = "Un autre utilisateur utilise cet email.";
        } else {
            if ($mot_de_passe) {
                $hashed_password = password_hash($mot_de_passe, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('UPDATE utilisateurs SET nom = ?, email = ?, mot_de_passe = ?, role = ? WHERE id = ?');
                $success = $stmt->execute([$nom, $email, $hashed_password, $role, $user_id]);
            } else {
                $stmt = $pdo->prepare('UPDATE utilisateurs SET nom = ?, email = ?, role = ? WHERE id = ?');
                $success = $stmt->execute([$nom, $email, $role, $user_id]);
            }
            if ($success) {
                $message = "Utilisateur mis à jour avec succès.";
                // Refresh user data
                $stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE id = ?');
                $stmt->execute([$user_id]);
                $user = $stmt->fetch();
            } else {
                $message = "Erreur lors de la mise à jour de l'utilisateur.";
            }
        }
    } else {
        $message = "Veuillez remplir tous les champs obligatoires.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Modifier un utilisateur</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <h1>Modifier un utilisateur</h1>
    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <?php if ($user): ?>
    <form method="post">
        <label for="nom">Nom :</label><br />
        <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" required /><br />

        <label for="email">Email :</label><br />
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required /><br />

        <label for="mot_de_passe">Mot de passe (laisser vide pour ne pas changer) :</label><br />
        <input type="password" id="mot_de_passe" name="mot_de_passe" /><br />

        <label for="role">Rôle :</label><br />
        <select id="role" name="role" required>
            <option value="admin" <?php if ($user['role'] === 'admin') echo 'selected'; ?>>Admin</option>
            <option value="client" <?php if ($user['role'] === 'client') echo 'selected'; ?>>Client</option>
            <option value="locataire" <?php if ($user['role'] === 'locataire') echo 'selected'; ?>>Locataire</option>
        </select><br /><br />

        <button type="submit">Mettre à jour</button>
    </form>
    <?php endif; ?>
    <br />
    <a href="manage_users.php">Retour à la gestion des utilisateurs</a>
</body>
</html>

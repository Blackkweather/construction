<?php
require 'auth.php';
requireLogin();
requireRole('admin');
include 'header.php';
include 'db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Suppression d'utilisateur
    if (isset($_POST['delete_user_id'])) {
        $delete_id = (int)$_POST['delete_user_id'];
        $stmt = $pdo->prepare('DELETE FROM utilisateurs WHERE id = ?');
        if ($stmt->execute([$delete_id])) {
            $message = "Utilisateur supprimé avec succès.";
        } else {
            $message = "Échec de la suppression de l'utilisateur.";
        }
    }

    // Modification d'utilisateur
    if (isset($_POST['edit_user_id'])) {
        $edit_id = (int)$_POST['edit_user_id'];
        $nom = $_POST['nom'] ?? '';
        $email = $_POST['email'] ?? '';
        $role = $_POST['role'] ?? '';
        $password = $_POST['password'] ?? '';

        // Validate inputs (basic)
        if (empty($nom) || empty($email) || empty($role)) {
            $message = "Nom, email et rôle sont obligatoires.";
        } else {
            // Update query
            if (!empty($password)) {
                // Hash password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('UPDATE utilisateurs SET nom = ?, email = ?, role = ?, mot_de_passe = ? WHERE id = ?');
                $success = $stmt->execute([$nom, $email, $role, $hashedPassword, $edit_id]);
            } else {
                $stmt = $pdo->prepare('UPDATE utilisateurs SET nom = ?, email = ?, role = ? WHERE id = ?');
                $success = $stmt->execute([$nom, $email, $role, $edit_id]);
            }

            if ($success) {
                header('Location: manage_users.php?msg=success');
                exit;
            } else {
                $message = "Échec de la modification de l'utilisateur.";
            }
        }
    }
}

try {
    $stmt = $pdo->query('SELECT id, nom, email, role FROM utilisateurs ORDER BY id ASC');
    $users = $stmt->fetchAll();
} catch (Exception $e) {
    $message = 'Erreur lors de la récupération des utilisateurs : ' . htmlspecialchars($e->getMessage());
    $users = [];
}

try {
    $stmtAdmins = $pdo->query('SELECT email FROM utilisateurs WHERE role = "admin"');
    $adminEmails = $stmtAdmins->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    $adminEmails = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Gérer les utilisateurs</title>
    <link rel="stylesheet" href="style.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
</head>
<body>
    <h1>Gestion des utilisateurs</h1>
    <div style="margin-bottom:20px;">
        <strong>Emails de connexion des administrateurs :</strong>
        <?php if (count($adminEmails) === 0): ?>
            <span>Aucun administrateur trouvé.</span>
        <?php else: ?>
            <ul>
                <?php foreach ($adminEmails as $email): ?>
                    <li><?php echo htmlspecialchars($email); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'success'): ?>
        <div class="alert alert-success">Utilisateur modifié avec succès.</div>
    <?php elseif ($message): ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>
    <table class="table table-striped table-bordered">
        <thead class="table-primary">
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['id']); ?></td>
                <td><?php echo htmlspecialchars($user['nom']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['role']); ?></td>
            <td>
                <form method="post" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');" style="display:inline;">
                    <input type="hidden" name="delete_user_id" value="<?php echo $user['id']; ?>" />
                    <button type="submit" class="btn btn-danger btn-sm" title="Supprimer"><i class="bi bi-trash"></i></button>
                </form>
                <button type="button" class="btn btn-primary btn-sm ms-2" title="Modifier" onclick="showEditForm(<?php echo $user['id']; ?>)"><i class="bi bi-pencil"></i></button>

                <div id="editForm<?php echo $user['id']; ?>" class="edit-form" style="display:none; margin-top:10px;">
                    <form method="post">
                        <input type="hidden" name="edit_user_id" value="<?php echo $user['id']; ?>" />
                        <div class="mb-2">
                            <label>Nom:</label>
                            <input type="text" name="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" required class="form-control" />
                        </div>
                        <div class="mb-2">
                            <label>Email:</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required class="form-control" />
                        </div>
                        <div class="mb-2">
                            <label>Rôle:</label>
                            <select name="role" required class="form-select">
                                <option value="admin" <?php if ($user['role'] === 'admin') echo 'selected'; ?>>Admin</option>
                                <option value="client" <?php if ($user['role'] === 'client') echo 'selected'; ?>>Client</option>
                                <option value="locataire" <?php if ($user['role'] === 'locataire') echo 'selected'; ?>>Locataire</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label>Mot de passe (laisser vide pour ne pas changer):</label>
                            <input type="password" name="password" class="form-control" />
                        </div>
                        <button type="submit" class="btn btn-success btn-sm">Enregistrer</button>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="hideEditForm(<?php echo $user['id']; ?>)">Annuler</button>
                    </form>
                </div>
            </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a href="admin_dashboard.php" class="btn btn-secondary mt-3">Retour au tableau de bord</a>
<script>
function showEditForm(userId) {
    document.getElementById('editForm' + userId).style.display = 'block';
}
function hideEditForm(userId) {
    document.getElementById('editForm' + userId).style.display = 'none';
}
</script>
</body>
</html>

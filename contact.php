<?php
session_start();
include 'db.php';

// Check if form is submitted
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $messageContent = $_POST['message'] ?? '';

    // Basic validation
    if ($name && $email && $subject && $messageContent) {
        // Here you can add code to save the message to the database or send an email
        $message = "Merci, votre message a été envoyé avec succès.";
    } else {
        $message = "Veuillez remplir tous les champs.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Contact - LocationPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet" />
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
            padding-top: 60px;
        }
        .container {
            max-width: 700px;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        h1 {
            color: var(--primary);
            margin-bottom: 30px;
            text-align: center;
        }
        label {
            font-weight: 600;
            margin-top: 15px;
        }
        input, textarea {
            width: 100%;
            padding: 10px 15px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1rem;
            font-family: 'Roboto', sans-serif;
        }
        textarea {
            resize: vertical;
            min-height: 120px;
        }
        button {
            margin-top: 20px;
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 12px 25px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100%;
        }
        button:hover {
            background-color: #0f2a52;
        }
        .message {
            margin-top: 20px;
            text-align: center;
            font-weight: 600;
            color: var(--secondary);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Contactez-nous</h1>
        <?php if ($message): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <form method="POST" action="contact.php">
            <label for="name">Nom complet</label>
            <input type="text" id="name" name="name" required />

            <label for="email">Adresse email</label>
            <input type="email" id="email" name="email" required />

            <label for="subject">Sujet</label>
            <input type="text" id="subject" name="subject" required />

            <label for="message">Message</label>
            <textarea id="message" name="message" required></textarea>

            <button type="submit">Envoyer</button>
        </form>
    </div>
</body>
</html>

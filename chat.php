<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$loggedInUser = $_SESSION['username'];

try {
    $pdo = new PDO('mysql:host=localhost;dbname=miniChat', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer les utilisateurs sauf celui connecté
    $stmt = $pdo->prepare('SELECT username FROM users WHERE username != ?');
    $stmt->execute([$loggedInUser]);
    $users = $stmt->fetchAll();

    // Récupérer les messages de cet utilisateur
    $stmt = $pdo->prepare('SELECT * FROM messages WHERE sender = ? OR recipient = ? ORDER BY date DESC');
    $stmt->execute([$loggedInUser, $loggedInUser]);
    $messages = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}

// Envoyer un message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message']) && isset($_POST['recipient'])) {
    $recipient = trim($_POST['recipient']);
    $message = htmlentities(trim($_POST['message']));

    if (!empty($recipient) && !empty($message)) {
        $stmt = $pdo->prepare('INSERT INTO messages (sender, recipient, message, date) VALUES (?, ?, ?, NOW())');
        $stmt->execute([$loggedInUser, $recipient, $message]);
        header('Location: chat.php');
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mini Chat</title>
    <style>
        /* Fond sombre avec des dégradés lumineux */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #1a1a1a, #0d0d0d);
    color: white;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100vh;
    overflow: hidden;
}

/* Barre de navigation */
.navbar {
    background: rgba(0, 0, 0, 0.8);
    width: 100%;
    padding: 15px;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1000;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
}

.navbar .logo {
    color: #00eaff;
    font-size: 1.5em;
    text-transform: uppercase;
    font-weight: bold;
    letter-spacing: 2px;
    text-shadow: 0px 0px 10px rgba(0, 255, 255, 0.8);
}

.navbar .user-info span {
    color: #fff;
    font-size: 1.1em;
    margin-right: 15px;
}

.navbar button {
    background-color: #ff3b5c;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.navbar button:hover {
    background-color: #ff1f3c;
}

/* Conteneur de chat */
.chat-container {
    width: 100%;
    max-width: 800px;
    margin-top: 80px;
    padding: 20px;
    background: rgba(0, 0, 0, 0.8);
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0, 255, 255, 0.3);
}

/* Conteneur de saisie */
.input-container {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

select, input[type="text"] {
    padding: 12px;
    border: 2px solid #00eaff;
    background-color: #1a1a1a;
    color: white;
    border-radius: 5px;
    font-size: 1.1em;
    outline: none;
    box-sizing: border-box;
    transition: border-color 0.3s ease;
}

select:focus, input[type="text"]:focus {
    border-color: #ff3b5c;
}

button[type="submit"] {
    background-color: #00eaff;
    border: none;
    padding: 12px;
    color: white;
    font-size: 1.1em;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button[type="submit"]:hover {
    background-color: #00c7d8;
}

/* Messages */
.messages-container {
    margin-top: 30px;
    overflow-y: auto;
    max-height: 400px;
    padding-right: 20px;
}

.message {
    background-color: rgba(0, 0, 0, 0.6);
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0, 255, 255, 0.2);
}

.message.sent {
    background-color: #00eaff;
    text-align: right;
    color: white;
}

.message.received {
    background-color: #333;
    text-align: left;
    color: white;
}

.message .message-user {
    font-weight: bold;
    color: #ff3b5c;
    font-size: 0.9em;
    text-shadow: 0 0 5px #ff3b5c;
}

.message .message-recipient {
    font-size: 0.9em;
    color: #ff9b00;
    text-shadow: 0 0 5px #ff9b00;
}

.message p {
    margin: 5px 0;
}

    </style>
</head>
<body>

<div class="navbar">
    <div class="logo">Mini Chat</div>
    <div class="user-info">
        <span><?= htmlspecialchars($_SESSION['username']) ?></span>
        <form method="POST" action="logout.php">
            <button type="submit">Déconnexion</button>
        </form>
    </div>
</div>

<div class="chat-container">

    <form method="POST">
        <div class="input-container">
            <select name="recipient" required>
                <option value="">Choisir un destinataire</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= $user['username'] ?>"><?= $user['username'] ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="message" placeholder="Votre message" required>
            <button type="submit">Envoyer</button>
        </div>
    </form>

    <div class="messages-container">
        <?php foreach ($messages as $msg): ?>
            <div class="message <?= $msg['sender'] == $loggedInUser ? 'sent' : 'received' ?>">
                <span class="message-user"><?= $msg['sender'] ?> (<?= $msg['date'] ?>):</span>
                <p><?= $msg['message'] ?></p>
                <span class="message-recipient">À : <?= $msg['recipient'] ?></span>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>

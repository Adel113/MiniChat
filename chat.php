
<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$pdo = new PDO('mysql:host=localhost;dbname=mini_chat', 'root', '');
$loggedInUser = $_SESSION['user'];

// Récupérer tous les utilisateurs (sauf l'utilisateur connecté)
$stmt = $pdo->prepare('SELECT username FROM users WHERE username != ?');
$stmt->execute([$loggedInUser]);
$users = $stmt->fetchAll();

// Récupérer les messages envoyés ou reçus par l'utilisateur
$stmt = $pdo->prepare('SELECT * FROM messages WHERE sender = ? OR recipient = ? ORDER BY date DESC');
$stmt->execute([$loggedInUser, $loggedInUser]);
$messages = $stmt->fetchAll();

// Envoyer un message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['message']) && !empty($_POST['recipient'])) {
    $recipient = $_POST['recipient'];
    $message = htmlentities($_POST['message']);
    
    // Insérer le message dans la base de données
    $stmt = $pdo->prepare('INSERT INTO messages (sender, recipient, message) VALUES (?, ?, ?)');
    $stmt->execute([$loggedInUser, $recipient, $message]);
    header('Location: chat.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini Chat</title>
    <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f4f7fc;
        margin: 0;
        padding: 0;
    }
    .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #6C87AD;
        color: white;
        padding: 10px 20px;
    }

    .navbar .logo {
        font-size: 1.5em;
        font-weight: bold;
    }

    .navbar .user-info {
        display: flex;
        align-items: center;
    }

    .navbar .user-info span {
        margin-right: 20px;
    }

    .navbar button {
        background-color: #fc5c7d;
        color: white;
        border: none;
        padding: 10px 20px;
        cursor: pointer;
        border-radius: 5px;
    }

    .navbar button:hover {
        background-color: #f05454;
    }

    h1 {
        text-align: center;
        margin-top: 30px;
        color: #333;
        font-weight: bold;
        font-size: 2.5em;
        text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.1);
        padding: 10px;
        background: #6C87AD;

        border-radius: 15px;
    }

    .chat-container {
        width: 90%;
        max-width: 800px;
        margin: 30px auto;
        background-color: #ffffff;
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        height: 600px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .messages-container {
        flex-grow: 1;
        overflow-y: auto;
        margin-bottom: 20px;
        padding-right: 10px;
        padding-left: 10px;
    }

    .message {
        margin: 12px 0;
        padding: 15px;
        border-radius: 15px;
        max-width: 80%;
        word-wrap: break-word;
        font-size: 1.1em;
        line-height: 1.5;
    }

    .message.sent {
        background: #6C87AD;

        color: white;
        align-self: flex-end;
        border-bottom-right-radius: 0;
    }

    .message.received {
        background: #f0f0f0;
        color: #333;
        align-self: flex-start;
        border-bottom-left-radius: 0;
    }

    .message-user {
        font-weight: bold;
        color: #333;
    }

    .message-date {
        font-size: 0.8em;
        color: #888;
        margin-top: 5px;
    }

    .message-recipient {
        font-size: 0.9em;
        color: #444;
        margin-top: 5px;
    }

    .input-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 15px;
    }

    input[type="text"] {
        width: 75%;
        padding: 12px;
        font-size: 1.1em;
        border: 2px solid #ddd;
        border-radius: 15px;
        transition: border-color 0.3s ease;
        margin-right: 15px;
    }

    input[type="text"]:focus {
        border-color: #007bff;
        outline: none;
    }

    select {
        width: 20%;
        padding: 12px;
        font-size: 1.1em;
        border: 2px solid #ddd;
        border-radius: 15px;
        background-color: #fff;
        margin-right: 15px;
    }

    button {
        padding: 12px 25px;
        font-size: 1.1em;
        background: #6C87AD;

        color: white;
        border: none;
        border-radius: 15px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #0056b3;
    }

    .pagination {
        text-align: center;
        margin-top: 20px;
    }

    .pagination a {
        padding: 10px 15px;
        margin: 0 5px;
        font-size: 1em;
        text-decoration: none;
        background-color: #007bff;
        color: white;
        border-radius: 10px;
        transition: background-color 0.3s ease;
    }

    .pagination a:hover {
        background-color: #0056b3;
    }

    .pagination a.active {
        background-color: #fc5c7d;
    }
    </style>


</head>
<body>

<div class="navbar">
    <div class="logo">Mini Chat</div>
    <div class="user-info">
        <span><?= $_SESSION['user'] ?></span>
        <form action="register.php" method="POST">
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
                <span class="message-recipient">
                    À : <?= $msg['recipient'] ?>
                </span>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>

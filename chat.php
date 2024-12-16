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

    // Récupérer les 20 derniers messages
    $stmt = $pdo->prepare('SELECT * FROM messages WHERE sender = ? OR recipient = ? ORDER BY date DESC LIMIT 20');
    $stmt->execute([$loggedInUser, $loggedInUser]);
    $messages = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini Chat</title>
    <link rel="stylesheet" href="stylechat.css">
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
    <form id="chat-form">
        <div class="input-container">
            <select name="recipient" id="recipient-select" required>
                <option value="">Choisir un destinataire</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= $user['username'] ?>"><?= $user['username'] ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="message" id="message-input" placeholder="Votre message" required>
            <button type="submit">Envoyer</button>
        </div>
    </form>

    <div class="messages-container" id="messages-container">
        <?php foreach (array_reverse($messages) as $msg): ?>
            <div class="message <?= $msg['sender'] == $loggedInUser ? 'sent' : 'received' ?>">
                <span class="message-user"><?= htmlspecialchars($msg['sender']) ?> (<?= $msg['date'] ?>):</span>
                <p><?= htmlspecialchars($msg['message']) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
    const messagesContainer = document.getElementById('messages-container');
    const form = document.getElementById('chat-form');
    const messageInput = document.getElementById('message-input');
    const recipientSelect = document.getElementById('recipient-select');

    // Ajouter un message dans la vue
    function addMessage(message) {
    const messageDiv = document.createElement('div');

    // Si le message est envoyé par l'utilisateur connecté, classe "sent", sinon "received"
    messageDiv.className = message.sender === "<?= $loggedInUser ?>" ? 'message sent' : 'message received';

    messageDiv.innerHTML = `
        <span class="message-user">${message.sender} (${message.date}):</span>
        <p>${message.message}</p>
        ${message.sender === "<?= $loggedInUser ?>" ? `<span class="message-recipient">Envoyé à : ${message.recipient}</span>` : ''}
    `;

    // Ajouter le message dans le conteneur
    messagesContainer.appendChild(messageDiv);

    // Faire défiler automatiquement vers le bas
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

    // Envoi de message
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const message = messageInput.value.trim();
        const recipient = recipientSelect.value;

        if (message && recipient) {
            fetch('send_message.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ recipient, message }),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        addMessage({
                            sender: "<?= $loggedInUser ?>",
                            recipient,
                            message,
                            date: new Date().toLocaleString(),
                        });
                        messageInput.value = '';
                    } else {
                        alert(data.message);
                    }
                })
                .catch((err) => console.error('Erreur lors de l\'envoi :', err));
        }
    });

    // Récupérer les nouveaux messages toutes les 3 secondes
    setInterval(() => {
        fetch('fetch_messages.php')
            .then((response) => response.json())
            .then((data) => {
                messagesContainer.innerHTML = ''; // Réinitialiser l'affichage
                data.forEach(addMessage);
            })
            .catch((err) => console.error('Erreur lors de la récupération :', err));
    }, 1000);
});

</script>
</body>
</html>

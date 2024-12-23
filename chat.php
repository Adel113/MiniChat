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
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC); // Assurez-vous que nous obtenons un tableau

    // Vérifier si des utilisateurs sont récupérés
    if (!$users) {
        $users = []; // Si aucun utilisateur n'est trouvé, initialiser en tableau vide
    }

    // Récupérer les 20 derniers messages
    $stmt = $pdo->prepare('SELECT * FROM messages WHERE sender = ? OR recipient = ? ORDER BY date DESC LIMIT 20');
    $stmt->execute([$loggedInUser, $loggedInUser]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC); // Assurez-vous que nous obtenons un tableau

    // Vérifier si des messages sont récupérés
    if (!$messages) {
        $messages = []; // Si aucun message n'est trouvé, initialiser en tableau vide
    }

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
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .selected-recipient {
            background-color: red !important;
        }
    </style>
</head>
<body class="bg-gray-100">

<div class="flex items-center justify-between bg-blue-600 p-4 text-white">
    <div class="text-xl font-bold">Mini Chat</div>
    <div class="flex items-center space-x-4">
        <span><?= htmlspecialchars($loggedInUser) ?></span>
        <form method="POST" action="logout.php">
            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white px-4 py-2 rounded">Déconnexion</button>
        </form>
    </div>
</div>

<div class="flex container mx-auto p-4 space-x-4">
    <!-- Choix des destinataires -->
    

    <!-- Contenu de chat -->
    <div class="w-[50.5%] bg-white shadow-lg rounded-lg p-4 flex flex-col">
        <!-- Affichage des messages -->
        <div class="messages-container space-y-4 overflow-y-auto max-h-96 p-4" id="messages-container">
            <?php if ($messages): ?>
                <?php foreach (array_reverse($messages) as $msg): ?>
                    <div class="message <?= $msg['sender'] == $loggedInUser ? 'text-right' : 'text-left' ?> p-4 rounded-lg shadow-md">
                        <span class="font-semibold"><?= htmlspecialchars($msg['sender']) ?> (<?= $msg['date'] ?>):</span>
                        <p class="mt-2"><?= htmlspecialchars($msg['message']) ?></p>
                        <span class="text-gray-600 text-sm">Envoyé à <?= htmlspecialchars($msg['recipient']) ?></span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucun message trouvé</p>
            <?php endif; ?>
        </div>

        <!-- Formulaire d'envoi de message -->
        <form id="chat-form" class="flex space-x-4 mb-4">
            <input type="text" name="recipient" id="recipient-input" placeholder="Destinataire" class="hidden" required>
            <input type="text" name="message" id="message-input" placeholder="Votre message" class="w-full p-2 border rounded" required>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded">Envoyer</button>
        </form>
    </div>

    <div class="w-[50.5%] flex flex-col items-center space-y-2">
        <?php if ($users): ?>
            <?php foreach ($users as $user): ?>
                <button 
                    class="w-full bg-blue-500 hover:bg-blue-700 text-white p-2 rounded-lg recipient-button"
                    onclick="selectRecipient('<?= $user['username'] ?>', this)">
                    <?= $user['username'] ?>
                </button>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucun utilisateur trouvé</p>
        <?php endif; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const messagesContainer = document.getElementById('messages-container');
        const form = document.getElementById('chat-form');
        const messageInput = document.getElementById('message-input');
        const recipientInput = document.getElementById('recipient-input');

        // Fonction pour définir le destinataire et mettre à jour le bouton sélectionné
        window.selectRecipient = (username, button) => {
            recipientInput.value = username;
            
            // Retirer la classe "selected" des autres boutons
            const buttons = document.querySelectorAll('.recipient-button');
            buttons.forEach(btn => btn.classList.remove('selected-recipient'));
            
            // Ajouter la classe "selected" au bouton sélectionné
            button.classList.add('selected-recipient');
        };

        // Ajouter un message dans la vue
        function addMessage(message) {
            const messageDiv = document.createElement('div');
            messageDiv.className = message.sender === "<?= $loggedInUser ?>" ? 'text-right p-4 rounded-lg shadow-md bg-blue-100 text-blue-800' : 'text-left p-4 rounded-lg shadow-md bg-gray-100 text-gray-800';
            messageDiv.innerHTML = ` 
                <span class="font-semibold">${message.sender} (${message.date}):</span>
                <p class="mt-2">${message.message}</p>
                <span class="text-gray-600 text-sm">Envoyé à ${message.recipient}</span>
            `;
            messagesContainer.appendChild(messageDiv);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        // Envoi de message
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const message = messageInput.value.trim();
            const recipient = recipientInput.value;

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

// Créer une connexion WebSocket
const socket = new WebSocket('ws://localhost:8080');

// Lorsque la connexion est ouverte
socket.onopen = () => {
    console.log('Connexion établie avec le serveur WebSocket');
};

// Lorsque le serveur envoie un message
socket.onmessage = (event) => {
    console.log('Message du serveur: ', event.data);
    // Ajoutez ici la logique pour mettre à jour l'interface avec les nouveaux messages
};

// Lorsque la connexion est fermée
socket.onclose = () => {
    console.log('Connexion fermée');
};

// Envoi d'un message au serveur
function sendMessage(message) {
    const data = {
        sender: '<?= $loggedInUser ?>',  // Exemple de sender
        recipient: document.getElementById('recipient-input').value,  // Utiliser le destinataire sélectionné
        message: message
    };
    socket.send(JSON.stringify(data));
}
</script>

</body>
</html>

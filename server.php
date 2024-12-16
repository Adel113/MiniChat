<?php
require __DIR__ . '/vendor/autoload.php';
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "Nouvelle connexion ! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        echo "Message reçu : " . $msg . "\n"; // Affiche le message reçu
        $data = json_decode($msg, true);
    
        if (isset($data['sender'], $data['recipient'], $data['message'])) {
            try {
                $pdo = new PDO('mysql:host=localhost;dbname=miniChat', 'root', '');
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $stmt = $pdo->prepare('INSERT INTO messages (sender, recipient, message, date) VALUES (?, ?, ?, ?)');
                $stmt->execute([$data['sender'], $data['recipient'], $data['message'], date('Y-m-d H:i:s')]);
                echo "Message sauvegardé dans la base de données.\n"; // Confirme la sauvegarde
            } catch (PDOException $e) {
                echo "Erreur de base de données : {$e->getMessage()}\n";
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        echo "Connexion {$conn->resourceId} déconnectée\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Erreur : {$e->getMessage()}\n";
        $conn->close();
    }
}

use Ratchet\Server\IoServer;

$server = IoServer::factory(
    new Chat(),
    8080
);

echo "Serveur WebSocket démarré sur le port 8080\n";
$server->run();

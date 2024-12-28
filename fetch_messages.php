<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$loggedInUser = $_SESSION['username'];
$recipient = $_GET['recipient'] ?? '';

// Valider que le destinataire est défini
if ($recipient) {
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=miniChat', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Récupérer les messages entre l'utilisateur et le destinataire
        $stmt = $pdo->prepare('
            SELECT * FROM messages
            WHERE (sender = ? AND recipient = ?) OR (sender = ? AND recipient = ?)
            ORDER BY date ASC');
        $stmt->execute([$loggedInUser, $recipient, $recipient, $loggedInUser]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($messages);  // Retourner les messages en JSON
    } catch (PDOException $e) {
        die("Erreur : " . $e->getMessage());
    }
} else {
    echo json_encode([]);  // Retourner un tableau vide si pas de destinataire
}

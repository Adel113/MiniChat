<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Utilisateur non connecté.',
    ]);
    exit();
}
$loggedInUser = $_SESSION['username'];
// décoder les données JSON envoyées depuis fetch
$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['recipient'], $data['message'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Données invalides.',
    ]);
    exit();
}
$recipient = trim($data['recipient']);
$message = trim($data['message']);
if (empty($recipient) || empty($message)) {
    echo json_encode([
        'success' => false,
        'message' => 'Le message ou le destinataire est vide.',
    ]);
    exit();
}
try {
    $pdo = new PDO('mysql:host=localhost;dbname=miniChat', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare('INSERT INTO messages (sender, recipient, message, date) VALUES (?, ?, ?, NOW())');
    $stmt->execute([$loggedInUser, $recipient, $message]);

    echo json_encode([
        'success' => true,
        'message' => 'Message envoyé',
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de l\'enregistrement du message : ' . $e->getMessage(),
    ]);
}
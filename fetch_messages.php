<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Content-Type: application/json');
    echo json_encode([]);
    exit();
}

$loggedInUser = $_SESSION['username'];

try {
    $pdo = new PDO('mysql:host=localhost;dbname=miniChat', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare('SELECT id, sender, recipient, message, date FROM messages WHERE sender = ? OR recipient = ? ORDER BY date DESC LIMIT 20');
    $stmt->execute([$loggedInUser, $loggedInUser]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(array_reverse($messages));
} catch (PDOException $e) {
    echo json_encode([]);
}
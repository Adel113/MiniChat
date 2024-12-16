<?php
session_start();

if (isset($_SESSION['username'])) {
    header('Location: chat.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $passwordConfirm = trim($_POST['password_confirm']);

    if ($password === $passwordConfirm) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            $pdo = new PDO('mysql:host=localhost;dbname=miniChat', 'root', '');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Vérifier si l'utilisateur existe déjà
            $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user) {
                $error = "Le nom d'utilisateur est déjà pris.";
            } else {
                // Insérer le nouvel utilisateur
                $stmt = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
                $stmt->execute([$username, $hashedPassword]);
                $_SESSION['username'] = $username;
                header('Location: chat.php');
                exit();
            }
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    } else {
        $error = "Les mots de passe ne correspondent pas.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link rel="stylesheet" href="styleregister.css">
</head>
<body>

<div class="form-container">
    <h1>Inscription</h1>
    <?php if (isset($error)) { ?>
        <div class="error"><?= $error ?></div>
    <?php } ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Nom d'utilisateur" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <input type="password" name="password_confirm" placeholder="Confirmer le mot de passe" required>
        <button type="submit">S'inscrire</button>
    </form>
</div>

</body>
</html>

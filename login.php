//login
<?php
session_start();

// Vérifier si l'utilisateur est déjà connecté
if (isset($_SESSION['username'])) {
    header('Location: chat.php');
    exit();
}

// Gérer la connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // Connexion à la base de données
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=minichat', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Erreur de connexion à la base de données : " . $e->getMessage());
    }

    // Vérifier si l'utilisateur existe
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // Vérifier si l'utilisateur existe et si le mot de passe est correct
    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: chat.php');
        exit();
    } else {
        $error = "Nom d'utilisateur ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Se connecter</title>
    <link rel="stylesheet" href="stylelogin.css">
</head>
<body>

<div class="form-container">
    <h1>Connexion</h1>
    <?php if (isset($error)) { ?>
        <div class="error"><?= $error ?></div>
    <?php } ?>
    <img src="https://play-lh.googleusercontent.com/_vUXpp4n8hX29SQkIa6Yc13GQa7BAfNhDd3NauSkUYZqteOM9Ux7J69xX9mZSypdXg" alt="Chat Image" width="500">

    <form method="POST">
    <input type="text" name="username" placeholder="Nom d'utilisateur" required>
    <input type="password" name="password" placeholder="Mot de passe" required>
    <button type="submit">Se connecter</button>
</form>
</div>

</body>
</html>

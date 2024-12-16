<?php
session_start();

// Vérifier si l'utilisateur est déjà connecté
if (isset($_SESSION['username'])) {
    header('Location: chat.php');
    exit();
}

// Gérer l'enregistrement
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $passwordConfirm = trim($_POST['password_confirm']);

    if ($password === $passwordConfirm) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            $pdo = new PDO('mysql:host=localhost;dbname=miniChat', 'root', '');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // si d'utilisateur existe deja
            $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user) {
                $error = "Le nom d'utilisateur est déjà pris.";
            } else {
                // Insérer le nouvel utilisateur dans la base de données
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
    <style>
        body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #0d0d0d, #1a1a1a); 
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    text-align: center;
    color: white;
}

/* Formulaire d'inscription */
.form-container {
    background: rgba(0, 0, 0, 0.7); /* Fond semi-transparent noir */
    padding: 40px;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 255, 255, 0.3);
    width: 100%;
    max-width: 400px;
    transition: transform 0.3s ease;
}

.form-container:hover {
    transform: scale(1.05); 
}

h1 {
    color: #00eaff; /* Texte bleu électrique */
    font-size: 2em;
    text-shadow: 0px 0px 15px rgba(0, 255, 255, 0.8); 
    margin-bottom: 30px;
}

/* Erreurs */
.error {
    color: #ff4d4d;
    margin-bottom: 15px;
    font-weight: bold;
}

/* Champs de formulaire */
input[type="text"], input[type="password"] {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid #00eaff;
    border-radius: 10px;
    color: white;
    font-size: 1.1em;
    transition: background 0.3s ease, border-color 0.3s ease;
}

input[type="text"]:focus, input[type="password"]:focus {
    background: rgba(0, 0, 0, 0.3);
    border-color: #00eaff;
}

button {
    background: #00eaff;
    color: black;
    padding: 12px 25px;
    border: none;
    border-radius: 20px;
    font-size: 1.2em;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.3s ease;
}

button:hover {
    background-color: #0099cc; 
    transform: translateY(-3px); 

button:focus {
    outline: none;
}

/* Image */
img {
    width: 60%;
    max-width: 180px;
    border-radius: 50%;
    margin-top: 20px;
    margin-bottom: 30px;
    transition: transform 0.3s ease;
}

img:hover {
    transform: rotate(15deg);
}

    </style>
</head>
<body>

<div class="form-container">
    <h1>Inscription</h1>
    <?php if (isset($error)) { ?>
        <div class="error"><?= $error ?></div>
    <?php } ?>
    <img src="https://play-lh.googleusercontent.com/_vUXpp4n8hX29SQkIa6Yc13GQa7BAfNhDd3NauSkUYZqteOM9Ux7J69xX9mZSypdXg" alt="Chat Image" width="500">

    <form method="POST">
        <input type="text" name="username" placeholder="Nom d'utilisateur" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <input type="password" name="password_confirm" placeholder="Confirmer le mot de passe" required>
        <button type="submit">S'inscrire</button>
    </form>
</div>

</body>
</html>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>S'inscrire</title>
    <style>
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

       
        .form-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        
        h1 {
            color: #333;
            margin-bottom: 30px;
            font-size: 2em;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            font-size: 1em;
            border: 2px solid #ddd;
            border-radius: 10px;
            margin: 10px 0;
            box-sizing: border-box; 
            transition: border-color 0.3s ease;
        }

        
        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #007bff;
            outline: none;
        }


        button {
            width: 50%;
            padding: 12px;
            font-size: 1.1em;
            background: #6C87AD;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 15px; /* Espacement entre le bouton et les champs de texte */
        }

        button:hover {
            background-color: #0056b3;
        }

        /* Message d'erreur ou de succès */
        .error-message,
        .success-message {
            margin-top: 15px;
            font-size: 1.1em;
            color: #f44336;
        }

        .success-message {
            color: #4caf50;
        }
        img {
            width: 50%;
            max-width: 150px;
            border-radius: 100px;
            margin-top: 20px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h1>S'inscrire</h1>
        <img src="https://play-lh.googleusercontent.com/_vUXpp4n8hX29SQkIa6Yc13GQa7BAfNhDd3NauSkUYZqteOM9Ux7J69xX9mZSypdXg" alt="Chat Image" width="500">

        <form method="POST">
            <input type="text" name="username" placeholder="Nom d'utilisateur" required><br>
            <input type="password" name="password" placeholder="Mot de passe" required><br>
            <button type="submit">S'inscrire</button>
        </form>
        
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);
            
            if (!empty($username) && !empty($password)) {
                $pdo = new PDO('mysql:host=localhost;dbname=mini_chat', 'root', '');
                $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
                $stmt->execute([$username]);
                
                if ($stmt->rowCount() === 0) {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
                    $stmt->execute([$username, $hashedPassword]);
                    echo '<div class="success-message">Inscription réussie ! Vous pouvez vous connecter.</div>';
                    header('Location: login.php');
                    exit;
                } else {
                    echo '<div class="error-message">Nom d\'utilisateur déjà pris.</div>';
                }
            } else {
                echo '<div class="error-message">Veuillez remplir tous les champs.</div>';
            }
        }
        ?>
    </div>

</body>
</html>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
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
            text-align: center;
        }

        .container {
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }

        h1 {
            color: #333;
            font-size: 1.5em;
            margin-bottom: 30px;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
        }

        a {
            background: #6C87AD;
            font-size: 1.2em;
            color: black;
            text-decoration: none;
            padding: 8px 20px;
            border-radius: 10px;
            margin: 0 15px;
            transition: background-color 0.3s ease;
        }

        a:hover {
            background-color: #007bff;
            color: white;
        }

        a:focus {
            outline: none;
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

    <div class="container">
        <h1>Bienvenue sur notre Mini Chat !</h1>
        <img src="https://play-lh.googleusercontent.com/_vUXpp4n8hX29SQkIa6Yc13GQa7BAfNhDd3NauSkUYZqteOM9Ux7J69xX9mZSypdXg" alt="Chat Image" width="500">
        <p> 
            <a href="register.php">Inscription</a>
            <a href="login.php">Connexion</a>
        </p>
    </div>

</body>
</html>

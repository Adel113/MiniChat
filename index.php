<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <style>
        body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #1a1a1a, #0d0d0d);
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    text-align: center;
    color: white;
}


.container {
    background: rgba(0, 0, 0, 0.7);
    padding: 40px;
    border-radius: 15px;
    box-shadow: 0 4px 30px rgba(0, 255, 255, 0.4);
    width: 100%;
    max-width: 500px;
    transition: transform 0.3s ease;
}

.container:hover {
    transform: scale(1.05);
}

h1 {
    color: #00eaff;
    font-size: 2em;
    text-shadow: 0px 0px 15px rgba(0, 255, 255, 0.8);
    margin-bottom: 30px;
}


a {
    background: #6C87AD;
    font-size: 1.2em;
    color: white;
    text-decoration: none;
    padding: 12px 25px;
    border-radius: 20px;
    margin: 10px;
    transition: background-color 0.3s ease, transform 0.3s ease;
}

a:hover {
    background-color: #00eaff;
    color: black;
    transform: translateY(-3px);
}

a:focus {
    outline: none;
}


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

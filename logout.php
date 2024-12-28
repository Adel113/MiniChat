<?php
session_start();

// supprime toutes les variables de session
session_unset(); 

// détruit la session
session_destroy();

// redirige vers la page de login
header('Location: login.php'); 
exit();
?>
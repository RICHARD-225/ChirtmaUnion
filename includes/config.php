<?php
$host = 'localhost';
$db = 'dbchrist';
$user = 'root';
$pwd = '';

// instenciation de la connexion a la base de donnee
$pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pwd);

// afficher l'erreure en cas d'echec de connexion 
try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connexion reussi !";
} catch (PDOException $e) {
    echo "echec de connxion".$e->getMessage();
    
}

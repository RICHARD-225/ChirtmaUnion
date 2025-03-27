<?php
require __DIR__ . '/../vendor/autoload.php'; // Charger l'autoload de Composer

use Dotenv\Dotenv;

try {
    // Charger le fichier .env
    // / Vérifier si la classe existe
    if (!class_exists('Dotenv\Dotenv')) {
        die("❌ ERREUR : La classe Dotenv\Dotenv n'est pas trouvée. Assurez-vous que phpdotenv est bien installé avec Composer.");
    }
    
    // Charger .env
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
    
    echo "✅ Dotenv chargé avec succès !";
    

    // Récupérer les variables d'environnement
    $host = $_ENV['DB_HOST'] ?? 'localhost';
    $db = $_ENV['DB_NAME'] ?? 'test';
    $user = $_ENV['DB_USER'] ?? 'root';
    $pass = $_ENV['DB_PASS'] ?? '';

    // Instanciation de la connexion à la base de données
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Activer le mode exception
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Mode de récupération par défaut
        PDO::ATTR_EMULATE_PREPARES => false, // Désactiver l'émulation des requêtes préparées
    ]);

    echo "✅ Connexion réussie à la base de données !";

} catch (PDOException $e) {
    die("❌ Erreur de connexion à la base de données : " . $e->getMessage());
}
?>

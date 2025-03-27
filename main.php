<?php
session_start();
require 'includes/config.php'; // Inclure le fichier de configuration

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Rediriger vers la page de connexion si non connecté
    exit;
}

// Récupérer l'ID et le sexe de l'utilisateur connecté
$userId = $_SESSION['user_id'];
$sql = "SELECT sexe FROM users WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$userId]);
$currentUser = $stmt->fetch(PDO::FETCH_ASSOC);

// Déterminer le sexe à filtrer
$genderToShow = ($currentUser['sexe'] === 'Homme') ? 'Femme' : 'Homme';

// Récupérer les profils des utilisateurs en fonction du sexe
$sql = "SELECT * FROM users WHERE sexe = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$genderToShow]);
$profiles = $stmt->fetchAll(PDO::FETCH_ASSOC);


include 'navbar.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profils Utilisateurs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Profils Utilisateurs</h1>
        <div class="row">
            <?php foreach ($profiles as $profile): ?>
                <div class="col-md-4">
                    <div class="card mb-4">
                        <img src="<?php echo htmlspecialchars($profile['photo_profil']); ?>" class="card-img-top" alt="Photo de profil">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($profile['prenom']) . ' ' . htmlspecialchars($profile['nom']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($profile['descri']); ?></p>
                            <a href="send_message.php?receiver_id=<?php echo $profile['id']; ?>" class="btn btn-primary">Envoyer un message</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

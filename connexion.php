<?php
session_start();
require 'includes/config.php'; // Assurez-vous que ce fichier contient la connexion à la base de données

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $email = $_POST['email'];
    $mot_de_passe = $_POST['password'];

    // Vérification des informations d'identification
    $sql_user = "SELECT * FROM users WHERE email = ?";
    $stmt_user = $pdo->prepare($sql_user);
    $stmt_user->execute([$email]);
    $user = $stmt_user->fetch();

    if ($user && password_verify($mot_de_passe, $user['pwd'])) {
        // Authentification réussie
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        
        // Redirection vers la page d'accueil ou tableau de bord
        header("Location: main.php"); // Changez cela vers la page que vous souhaitez
        exit;
    } else {
        // Authentification échouée
        echo json_encode(['success' => false, 'message' => 'Email ou mot de passe incorrect.']);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - ChristianUnion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar (même que index.html) -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <!-- ... copier la navbar de index.html ... -->
    </nav>

    <div class="container mt-5 pt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title mb-0">Connexion</h3>
                    </div>
                    <div class="card-body">
                        <form id="connexionForm" action="connexion.php" method="POST">
                            <div class="mb-3">
                                <label for="loginEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" id="loginEmail" required>
                            </div>
                            <div class="mb-3">
                                <label for="loginPassword" class="form-label">Mot de passe</label>
                                <input type="password" class="form-control" name="password" id="loginPassword" required>
                            </div>
                            <div id="messageRetourConnexion" class="mb-3" style="display: none;"></div>
                            <button type="submit" class="btn btn-primary w-100">Se connecter</button>
                        </form>
                        <div class="mt-3 text-center">
                            <p>Pas encore de compte ? <a href="inscription.html" class="nav-link-custom">Inscrivez-vous ici</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-light py-4 mt-5">
        <!-- ... copier le footer de index.html ... -->
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/connexion.js"></script>
</body>
</html> 
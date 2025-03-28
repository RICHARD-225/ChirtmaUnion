<?php
session_start();
require 'includes/config.php';
require 'vendor/autoload.php'; // Assurez-vous d'avoir installé Firebase PHP SDK via Composer

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;

// Initialiser Firebase
$factory = (new Factory)->withServiceAccount($_ENV['FIREBASE_CREDENTIALS']); // Remplacez par le chemin vers votre fichier de clés
$auth = $factory->createAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $prenom = $_POST['prenom'];
    $nom = $_POST['nom'];
    $sexe = $_POST['genre'];
    $email = $_POST['email'];
    $ddn = $_POST['ddn'];
    $denomination = $_POST['denomination'];
    $frequence = $_POST['frequence'];
    $description = $_POST['description'];
    $mot_de_passe = $_POST['password'];
    $confirm_mdp = $_POST['confirm_password'];
    $ville = $_POST['ville'];

    // Calcul de l'âge
    $dateNaissance = new DateTime($ddn);
    $aujourdhui = new DateTime();
    $age = $aujourdhui->diff($dateNaissance)->y;

    // Vérification de l'âge
    if ($age < 20) {
        echo "Vous devez avoir au moins 20 ans pour vous inscrire.";
        exit; // Arrêter le script si l'âge est invalide
    }

    // Validation des mots de passe
    if ($mot_de_passe !== $confirm_mdp) {
        echo "Les mots de passe ne correspondent pas.";
        exit;
    }

    // Hachage du mot de passe
    $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_BCRYPT);

    // Vérification si l'email existe déjà
    $sql_check_email = "SELECT COUNT(*) FROM users WHERE email = ?";
    $stmt_check_email = $pdo->prepare($sql_check_email);
    $stmt_check_email->execute([$email]);
    $email_exists = $stmt_check_email->fetchColumn();

    if ($email_exists > 0) {
        echo "Cet email est déjà utilisé. Veuillez en choisir un autre.";
        exit;
    }

    // Gestion de la photo de profil
    $photo_profil = null;
    if (isset($_FILES['photo_profil']) && $_FILES['photo_profil']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $photo_profil = $target_dir . basename($_FILES['photo_profil']['name']);
        
        // Vérification du type de fichier image
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['photo_profil']['type'], $allowed_types)) {
            echo "Format de fichier non autorisé. Seules les images JPEG, PNG et GIF sont autorisées.";
            exit;
        }

        // Déplacer le fichier téléchargé
        if (!move_uploaded_file($_FILES['photo_profil']['tmp_name'], $photo_profil)) {
            echo "Erreur lors du téléchargement de la photo.";
            exit;
        }
    }

    try {
        // Créer l'utilisateur dans Firebase
        $user = $auth->createUserWithEmailAndPassword($email, $mot_de_passe);

        // Insérer l'utilisateur dans la base de données MySQL
        $sql_user = "INSERT INTO users (email, pwd, prenom, nom, sexe, ddn, ville, photo_profil, descri, eglise) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_user = $pdo->prepare($sql_user);
        $stmt_user->execute([$email, $mot_de_passe_hash, $prenom, $nom, $sexe, $ddn, $ville, $photo_profil, $description, $denomination]);

        // Récupérer l'ID de l'utilisateur inséré
        $utilisateur_id = $pdo->lastInsertId();

        // Insertion dans la table `profils`
        $sql_profil = "INSERT INTO profils (utilisateur_id, denomination, niveau_pratique) VALUES (?, ?, ?)";
        $stmt_profil = $pdo->prepare($sql_profil);
        $stmt_profil->execute([$utilisateur_id, $denomination, $frequence]);

        // Inclure le fichier d'envoi d'e-mail
        require 'includes/sendMail.php';
        envoyerEmailConfirmation($email); // Appeler la fonction pour envoyer l'e-mail

        echo "Inscription réussie ! Un e-mail de confirmation a été envoyé.";
        header("Location: connexion.php");
        exit;
    } catch (Exception $e) {
        echo "Erreur lors de la création de l'utilisateur : " . $e->getMessage();
    }
}
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - ChristianUnion</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/style.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #800080; /* violet */
            --secondary-color: #FFD700; /* or */
            --light-color: #FFFFFF; /* blanc */
        }

        body {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                url('image1/cta/cta-bg.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: var(--light-color);
        }

        .navbar {
            background-color: var(--primary-color) !important;
        }
        

        .navbar-light .navbar-nav .nav-link {
            color: var(--light-color);
        }

        .card-header {
            background-color: var(--primary-color) !important;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            color: var(--primary-color);
        }

        footer {
            background-color: var(--primary-color) !important;
            color: var(--light-color);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.25rem rgba(128, 0, 128, 0.25);
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        /* Styles pour les liens */
        .nav-link-custom {
            color: var(--primary-color);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .nav-link-custom:hover {
            color: var(--secondary-color);
        }
    </style>
</head>
<body>
    <!-- Même Navbar que index.html -->
    <!-- <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top"> -->
    <!-- ... copier la navbar de index.html ... -->
    <!-- </nav> -->

    <div class="container mt-5 pt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title mb-0">Créer votre compte</h3>
                    </div>
                    <div class="card-body">
                    <form action="inscription.php" method="POST" enctype="multipart/form-data" id="inscriptionForm" class="needs-validation" novalidate>
    <!-- Informations personnelles -->
    <div class="row mb-4">
        <h4>Informations personnelles</h4>
        <div class="col-md-6 mb-3">
            <label for="prenom" class="form-label">Prénom</label>
            <input type="text" class="form-control" name="prenom" id="prenom" required>
        </div>
        <div class="col-md-6 mb-3">
            <label for="nom" class="form-label">Nom</label>
            <input type="text" class="form-control" name="nom" id="nom" required>
        </div>
        <div class="col-md-6 mb-3">
            <label for="genre" class="form-label">Genre</label>
            <select class="form-select" name="genre" id="genre" required>
                <option value="">Choisir...</option>
                <option value="homme">Homme</option>
                <option value="femme">Femme</option>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" name="email" id="email" required>
        </div>
        <div class="col-md-6 mb-3">
            <label for="dateNaissance" class="form-label">Date de naissance</label>
            <input type="date" class="form-control" name="ddn" id="dateNaissance" required>
        </div>
        <div class="col-md-6 mb-3">
                            <label class="form-label">Ville</label>
                            <select name="ville" class="form-control" class="form-select">
                                <option value="">Choisir...</option>
                                <option value="">Toutes les régions</option>
                                <option value="abidjan">Abidjan</option>
                                <option value="yamoussoukro">Yamoussoukro</option>
                                <option value="bouake">Bouaké</option>
                                <option value="daloa">Daloa</option>
                                <option value="san_pedro">San Pedro</option>
                                <option value="korhogo">Korhogo</option>
                                <option value="man">Man</option>
                                <option value="abengourou">Abengourou</option>
                                <option value="gagnoa">Gagnoa</option>
                                <option value="sikasso">Sikasso</option>
                                <option value="seguela">Séguéla</option>
                                <option value="bongouanou">Bongouanou</option>
                                <option value="katiola">Katiola</option>
                                <option value="odienne">Odienné</option>
                                <option value="issia">Issia</option>
                                <option value="treichville">Treichville</option>
                                <option value="port_bouet">Port-Bouët</option>
                                <option value="marcory">Marcory</option>
                                <option value="cocody">Cocody</option>
                                <option value="attecoubé">Attécoubé</option>
                                <option value="abobo">Abobo</option>
                                <option value="aboisso">Aboisso</option>
                                <option value="ferkessedougou">Ferkessédougou</option>
                                <option value="toumodi">Toumodi</option>
                                <option value="lakota">Lakota</option>
                                <option value="tanda">Tanda</option>
                                <option value="toulepleu">Toulepleu</option>
                                <option value="grand_bassam">Grand-Bassam</option>
                                <option value="bingerville">Bingerville</option>
                                <option value="yopougon">Yopougon</option>
                                <option value="agnibilekrou">Agnibilékrou</option>
                                <option value="bondoukou">Bondoukou</option>
                                <option value="guiglo">Guiglo</option>
                                <option value="zuenoula">Zuenoula</option>
                                <option value="sassandra">Sassandra</option>
                                <option value="tiapoum">Tiapoum</option>
                                <option value="koun_fao">Koun-Fao</option>
                                <option value="oume">Oumé</option>
                                <option value="agboville">Agboville</option>
                                <option value="fresco">Fresco</option>
                                <option value="prikro">Prikro</option>
                                <option value="tiebissou">Tiebissou</option>
                                <option value="grand_lahou">Grand-Lahou</option>
                                <option value="divo">Divo</option>
                                <option value="dimbokro">Dimbokro</option>
                                <option value="sanwi">Sanwi</option>
                                <option value="bouna">Bouna</option>
                                <option value="kani">Kani</option>
                                <option value="sinfra">Sinfra</option>  
                        </select>
        </div>
    </div>

    <!-- Informations spirituelles -->
    <div class="row mb-4">
        <h4>Votre foi</h4>
        <div class="col-md-6 mb-3">
        <label for="denomination" class="form-label">Eglise</label>
            <select class="form-select" name="denomination" id="denomination" required>
                <option value="">Choisir...</option>
                            <option value="evangelique">Évangélique</option>
                            <option value="pentecote">Pentecôte</option>
                            <option value="catholique">Catholique</option>
                            <option value="baptiste">Baptiste</option>
                            <option value="methodiste">Méthodiste</option>
                            <option value="protestant">Protestant</option>
                            <option value="celeste">Celeste</option>
                            <option value="bethesda">Bethesda</option>
                            <option value="communion">Communion</option>
                            <option value="delivrance">De la Délivrance</option>
                            <option value="assemblee_de_dieu">Assemblée de Dieu</option>
                            <option value="benevolence">Bénévolence</option>
                            <option value="oratoire">Oratoire</option>
                            <option value="temoin_de_jehova">Temoins de Jehova</option>
                            <option value="CMA">CMA</option>
                            <option value="missionnaire">Missionnaire</option>
                        
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label for="frequenceEglise" class="form-label">Fréquence à l'église</label>
            <select class="form-select" name="frequence" id="frequenceEglise" required>
                <option value="">Choisir...</option>
                <option value="Plusieurs fois par semaine">Plusieurs fois par semaine</option>
                <option value="Chaque dimanche">Chaque dimanche</option>
                <option value="Occasionnellement">Occasionnellement</option>
            </select>
        </div>
    </div>

    <!-- Photo et description -->
    <div class="row mb-4">
        <h4>Votre profil</h4>
        <div class="col-12 mb-3">
            <label for="photo_profil" class="form-label">Photo de profil</label>
            <input type="file" class="form-control" name="photo_profil" id="photo" accept="image/*" required>
        </div>
        <div class="col-12 mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" name="description" id="description" rows="4" required></textarea>
        </div>
    </div>

    <!-- Mot de passe -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <label for="password" class="form-label">Mot de passe</label>
            <input type="password" class="form-control" name="password" id="password" required>
        </div>
        <div class="col-md-6 mb-3">
            <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
            <input type="password" class="form-control" name="confirm_password" id="confirmPassword" required>
        </div>
    </div>

    <!-- Conditions -->
    <div class="mb-4">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="conditions" required>
            <label class="form-check-label" for="conditions">
                J'accepte les conditions d'utilisation et la politique de confidentialité
            </label>
        </div>
    </div>

    <!-- Liens -->
    <div class="mb-3 text-center">
        <p>
            Déjà inscrit ? <a href="connexion.html" class="nav-link-custom">Connectez-vous ici</a>
        </p>
        <p>
            <a href="index.html" class="nav-link-custom">Retourner à la page d'accueil</a>
        </p>
    </div>

   

    <button type="submit" name="envoi" class="btn btn-primary btn-lg w-100">Créer mon compte</button>
</form> 
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-light py-4 mt-5">
        <!-- ... copier le footer de index.html ... -->
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="js/inscription.js"></script>
</body>
</html> 
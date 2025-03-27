<?php
session_start();
require 'includes/config.php'; // Assurez-vous que ce fichier contient la connexion à la base de données

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    
    // Récupérer la description du formulaire
    $description = $_POST['description'];

    // Mettre à jour uniquement la description de l'utilisateur
    $sql_update_description = "UPDATE users SET descri = ? WHERE id = ?";
    $stmt_update_description = $pdo->prepare($sql_update_description);
    $stmt_update_description->execute([$description, $user_id]);

    // Gérer la mise à jour de la photo de profil si un fichier est téléchargé
    if (isset($_FILES['photo_profil']) && $_FILES['photo_profil']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['photo_profil']['tmp_name'];
        $file_name = basename($_FILES['photo_profil']['name']);
        $upload_dir = 'uploads/'; // Assurez-vous que ce dossier existe et est accessible en écriture
        move_uploaded_file($file_tmp, $upload_dir . $file_name);

        // Mettre à jour le chemin de la photo de profil dans la base de données
        $sql_photo_update = "UPDATE users SET photo_profil = ? WHERE id = ?";
        $stmt_photo_update = $pdo->prepare($sql_photo_update);
        $stmt_photo_update->execute([$upload_dir . $file_name, $user_id]);
    }

    // Rediriger vers le profil après la mise à jour
    header("Location: profil.php");
    exit;
}
?> 
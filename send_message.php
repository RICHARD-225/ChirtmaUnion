<?php
session_start();
require 'includes/config.php'; // Inclure le fichier de configuration

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Utilisateur non connecté.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $expediteur_id = $_SESSION['user_id'];
    $destinataire_id = $_POST['receiver_id'];
    $message = $_POST['message'] ?? '';
    $image = null;

    // Vérifier si un fichier a été téléchargé
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $imageName = uniqid() . '_' . basename($_FILES['image']['name']);
        $imagePath = $uploadDir . $imageName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
            $image = $imagePath;
        } else {
            echo json_encode(['success' => false, 'error' => 'Erreur lors du téléchargement de l\'image.']);
            exit;
        }
    }

    // Insérer le message dans la base de données
    $sql = "INSERT INTO messages (expediteur_id, destinataire_id, message, image) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$expediteur_id, $destinataire_id, $message, $image]);

    echo json_encode(['success' => true, 'message' => $message, 'image' => $image]);
    exit;
}
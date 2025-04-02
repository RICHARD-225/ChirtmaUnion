<?php
session_start();
require 'includes/config.php'; // Inclure le fichier de configuration

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Utilisateur non connecté.']);
    exit;
}

$userId = $_SESSION['user_id'];
$receiverId = isset($_POST['receiver_id']) ? (int)$_POST['receiver_id'] : 0;

if ($receiverId > 0) {
    // Mettre à jour les messages comme "vus"
    $sql_update_vu = "UPDATE messages SET est_vu = 1 WHERE destinataire_id = ? AND expediteur_id = ? AND est_vu = 0";
    $stmt_update_vu = $pdo->prepare($sql_update_vu);
    $stmt_update_vu->execute([$userId, $receiverId]);

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Aucun destinataire sélectionné.']);
}
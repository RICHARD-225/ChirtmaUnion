<?php
session_start();
require 'includes/config.php'; // Inclure le fichier de configuration

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Utilisateur non connecté.']);
    exit;
}

$userId = $_SESSION['user_id'];
$receiverId = isset($_GET['receiver_id']) ? (int)$_GET['receiver_id'] : 0;

if ($receiverId > 0) {
    // Mettre à jour les messages comme "reçus"
    $sql_update_recu = "UPDATE messages SET est_recu = 1 WHERE destinataire_id = ? AND expediteur_id = ? AND est_recu = 0";
    $stmt_update_recu = $pdo->prepare($sql_update_recu);
    $stmt_update_recu->execute([$userId, $receiverId]);

    // Récupérer les messages
    $sql_messages = "SELECT * FROM messages 
                     WHERE (expediteur_id = ? AND destinataire_id = ?) 
                        OR (expediteur_id = ? AND destinataire_id = ?) 
                     ORDER BY envoye_le ASC";
    $stmt_messages = $pdo->prepare($sql_messages);
    $stmt_messages->execute([$userId, $receiverId, $receiverId, $userId]);
    $messages = $stmt_messages->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'messages' => $messages]);
} else {
    echo json_encode(['success' => false, 'error' => 'Aucun destinataire sélectionné.']);
}

function fetchMessages() {
    const receiverId = <?php echo $receiverId; ?>;
    const chatContainer = document.querySelector('.chat-container');

    // Afficher un indicateur de chargement
    chatContainer.innerHTML = '<p>Chargement des messages...</p>';

    fetch(`fetch_messages.php?receiver_id=${receiverId}`)
        .then(response => response.json())
        .then(data => {
            chatContainer.innerHTML = ''; // Effacer l'indicateur de chargement

            data.messages.forEach(message => {
                const messageDiv = document.createElement('div');
                messageDiv.className = `message ${message.expediteur_id == <?php echo $userId; ?> ? 'sent' : 'received'}`;

                if (message.message) {
                    messageDiv.innerHTML += `<p>${message.message}</p>`;
                }
                if (message.image) {
                    messageDiv.innerHTML += `<img src="${message.image}" alt="Image" style="max-width: 200px; border-radius: 10px;">`;
                }

                chatContainer.appendChild(messageDiv);
            });

            chatContainer.scrollTop = chatContainer.scrollHeight;
        });
}
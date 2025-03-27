<?php
session_start();
require 'includes/config.php'; // Inclure le fichier de configuration

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Rediriger vers la page de connexion si non connecté
    exit;
}

// Récupérer l'ID de l'utilisateur connecté
$userId = $_SESSION['user_id'];
$receiverId = $_GET['destinataire_id']; // ID de l'utilisateur destinataire

// Récupérer les messages entre l'utilisateur connecté et le destinataire
$sql = "SELECT * FROM messages WHERE (expediteur_id = ? AND destinataire_id = ?) OR (expediteur_id = ? AND destinataire_id = ?) ORDER BY envoye_le ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$userId, $receiverId, $receiverId, $userId]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les informations du destinataire
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$receiverId]);
$receiver = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messagerie avec <?php echo htmlspecialchars($receiver['prenom']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .chat-container {
            height: 80vh;
            overflow-y: auto;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
        }
        .message {
            margin-bottom: 15px;
        }
        .message.sent {
            text-align: right;
        }
        .message.received {
            text-align: left;
        }
        .message p {
            display: inline-block;
            padding: 10px;
            border-radius: 5px;
        }
        .message.sent p {
            background-color: #007bff;
            color: white;
        }
        .message.received p {
            background-color: #f1f1f1;
            color: black;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1>Messagerie avec <?php echo htmlspecialchars($receiver['prenom']); ?></h1>
        <div class="chat-container">
            <?php foreach ($messages as $message): ?>
                <div class="message <?php echo ($message['expeditaire_id'] == $userId) ? 'sent' : 'received'; ?>">
                    <p><?php echo htmlspecialchars($message['message']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="input-group mt-3">
            <textarea id="messageInput" class="form-control" placeholder="Écrivez votre message ici..." rows="2"></textarea>
            <button id="sendMessageButton" class="btn btn-primary">Envoyer</button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('sendMessageButton').addEventListener('click', function() {
            const message = document.getElementById('messageInput').value;
            const senderId = <?php echo $userId; ?>;
            const receiverId = <?php echo $receiverId; ?>;

            if (message.trim() === '') {
                alert('Veuillez entrer un message.');
                return;
            }

            // Envoyer le message via AJAX
            fetch('send_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ sender_id: senderId, receiver_id: receiverId, message: message }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Ajouter le message à la conversation
                    const chatContainer = document.querySelector('.chat-container');
                    const newMessage = document.createElement('div');
                    newMessage.className = 'message sent';
                    newMessage.innerHTML = `<p>${message}</p>`;
                    chatContainer.appendChild(newMessage);
                    document.getElementById('messageInput').value = ''; // Réinitialiser le champ de message
                } else {
                    alert('Erreur lors de l\'envoi du message.');
                }
            });
        });
    </script>
</body>
</html>

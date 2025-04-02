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

// Récupérer la liste des utilisateurs (sauf l'utilisateur connecté)
$sql_users = "SELECT id, prenom, nom FROM users WHERE id != ?";
$stmt_users = $pdo->prepare($sql_users);
$stmt_users->execute([$userId]);
$users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);

// Récupérer l'ID du destinataire sélectionné
$receiverId = isset($_GET['destinataire_id']) ? (int)$_GET['destinataire_id'] : 0;

// Récupérer les messages entre l'utilisateur connecté et le destinataire
$messages = [];
if ($receiverId > 0) {
    $sql_messages = "SELECT * FROM messages 
                     WHERE (expediteur_id = ? AND destinataire_id = ?) 
                        OR (expediteur_id = ? AND destinataire_id = ?) 
                     ORDER BY envoye_le ASC";
    $stmt_messages = $pdo->prepare($sql_messages);
    $stmt_messages->execute([$userId, $receiverId, $receiverId, $userId]);
    $messages = $stmt_messages->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer les informations du destinataire
    $sql_receiver = "SELECT * FROM users WHERE id = ?";
    $stmt_receiver = $pdo->prepare($sql_receiver);
    $stmt_receiver->execute([$receiverId]);
    $receiver = $stmt_receiver->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messagerie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .messagerie-container {
            display: flex;
            height: 90vh;
            border: 1px solid #ccc;
            border-radius: 5px;
            overflow: hidden;
        }

        .user-list {
            width: 30%;
            background-color: #ffffff;
            border-right: 1px solid #ccc;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        .user-list-header {
            padding: 15px;
            border-bottom: 1px solid #ccc;
            background-color: #f8f9fa;
        }

        .user-list-header input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 20px;
        }

        .user-list ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .user-list li {
            padding: 15px;
            border-bottom: 1px solid #f1f1f1;
            cursor: pointer;
        }

        .user-list li:hover {
            background-color: #f1f1f1;
        }

        .chat-section {
            width: 70%;
            display: flex;
            flex-direction: column;
        }

        .chat-header {
            background-color: #007bff;
            color: white;
            padding: 15px;
            text-align: center;
        }

        .chat-container {
            flex: 1;
            overflow-y: auto;
            padding: 15px;
            background-color: #ffffff;
        }

        .message {
            margin-bottom: 15px;
        }

        .message.sent {
            text-align: right;
        }

        .message p {
            display: inline-block;
            padding: 10px 15px;
            border-radius: 20px;
            max-width: 70%;
        }

        .message.sent p {
            background-color: #007bff;
            color: white;
        }

        .message.received p {
            background-color: #f1f1f1;
            color: black;
        }

        .input-group {
            padding: 15px;
            background-color: #f8f9fa;
            border-top: 1px solid #ccc;
            display: flex;
            align-items: center;
        }

        #messageInput {
            border-radius: 20px;
            flex: 1;
        }

        #sendMessageButton {
            border-radius: 20px;
            margin-left: 10px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="messagerie-container">
        <!-- Liste des utilisateurs -->
        <div class="user-list">
            <div class="user-list-header">
                <input type="text" id="searchUser" placeholder="Rechercher un utilisateur..." onkeyup="filterUsers()">
            </div>
            <ul id="userList">
                <?php foreach ($users as $user): ?>
                    <li onclick="window.location.href='messages.php?destinataire_id=<?php echo $user['id']; ?>'">
                        <?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Section de chat -->
        <div class="chat-section">
            <?php if ($receiverId > 0): ?>
                <div class="chat-header">
                    <h5>Conversation avec <?php echo htmlspecialchars($receiver['prenom'] . ' ' . $receiver['nom']); ?></h5>
                </div>
                <div class="chat-container">
                    <?php foreach ($messages as $message): ?>
                        <div class="message <?php echo ($message['expediteur_id'] == $userId) ? 'sent' : 'received'; ?>">
                            <?php if (!empty($message['message'])): ?>
                                <p><?php echo htmlspecialchars($message['message']); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($message['image'])): ?>
                                <img src="<?php echo htmlspecialchars($message['image']); ?>" alt="Image" style="max-width: 200px; border-radius: 10px;">
                            <?php endif; ?>

                            <!-- Indicateurs "reçu" et "vu" -->
                            <?php if ($message['expediteur_id'] == $userId): ?>
                                <small>
                                    <?php if ($message['est_vu']): ?>
                                        ✅ Vu
                                    <?php elseif ($message['est_recu']): ?>
                                        ✔️ Reçu
                                    <?php else: ?>
                                        ⏳ En attente
                                    <?php endif; ?>
                                </small>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="input-group">
                    <input type="file" id="imageInput" class="form-control" accept="image/*">
                    <textarea id="messageInput" class="form-control" placeholder="Rédigez un message..." rows="2"></textarea>
                    <button id="sendMessageButton" class="btn btn-primary">Envoyer</button>
                </div>
            <?php else: ?>
                <div class="chat-header">
                    <h5>Sélectionnez un utilisateur pour commencer une conversation</h5>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    function filterUsers() {
        const searchInput = document.getElementById('searchUser').value.toLowerCase();
        const userList = document.getElementById('userList');
        const users = userList.getElementsByTagName('li');

        for (let i = 0; i < users.length; i++) {
            const userName = users[i].textContent.toLowerCase();
            if (userName.includes(searchInput)) {
                users[i].style.display = '';
            } else {
                users[i].style.display = 'none';
            }
        }
    }

    document.getElementById('sendMessageButton')?.addEventListener('click', function () {
        const messageInput = document.getElementById('messageInput');
        const imageInput = document.getElementById('imageInput');
        const message = messageInput.value;
        const receiverId = <?php echo $receiverId; ?>;

        const formData = new FormData();
        formData.append('receiver_id', receiverId);
        formData.append('message', message);
        if (imageInput.files[0]) {
            formData.append('image', imageInput.files[0]);
        }

        fetch('send_message.php', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const chatContainer = document.querySelector('.chat-container');
                const newMessage = document.createElement('div');
                newMessage.className = 'message sent';

                if (data.message) {
                    newMessage.innerHTML += `<p>${data.message}</p>`;
                }
                if (data.image) {
                    newMessage.innerHTML += `<img src="${data.image}" alt="Image" style="max-width: 200px; border-radius: 10px;">`;
                }

                chatContainer.appendChild(newMessage);
                chatContainer.scrollTop = chatContainer.scrollHeight;

                messageInput.value = '';
                imageInput.value = '';
            } else {
                alert('Erreur lors de l\'envoi du message.');
            }
        });
    });

    // Fonction pour récupérer les nouveaux messages
    function fetchMessages() {
        const receiverId = <?php echo $receiverId; ?>;

        fetch(`fetch_messages.php?receiver_id=${receiverId}`)
            .then(response => response.json())
            .then(data => {
                const chatContainer = document.querySelector('.chat-container');
                chatContainer.innerHTML = ''; // Effacer les anciens messages

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

    // Rafraîchir les messages toutes les 3 secondes
    setInterval(fetchMessages, 3000);

    function markMessagesAsSeen() {
        const receiverId = <?php echo $receiverId; ?>;

        fetch('update_seen.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ receiver_id: receiverId }),
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.error('Erreur lors de la mise à jour des messages comme vus.');
            }
        });
    }

    // Appeler la fonction lorsque la conversation est chargée
    markMessagesAsSeen();
</script>
</body>
</html>

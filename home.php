<?php
// home.php - Updated to optionally limit contacts if needed, but keeping general for all users
 
session_start();
require 'db.php';
 
if (!isset($_SESSION['user_id'])) {
    echo '<script>window.location.href = "login.php";</script>';
    exit;
}
 
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
 
// Get all users except self for contacts
$stmt = $pdo->prepare("SELECT * FROM users WHERE id != ?");
$stmt->execute([$user_id]);
$contacts = $stmt->fetchAll();
 
// For chat list, show all contacts with last message preview
$chat_list = [];
foreach ($contacts as $contact) {
    $last_msg_stmt = $pdo->prepare("
        SELECT message, timestamp FROM messages 
        WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) 
        ORDER BY timestamp DESC LIMIT 1
    ");
    $last_msg_stmt->execute([$user_id, $contact['id'], $contact['id'], $user_id]);
    $last_msg = $last_msg_stmt->fetch();
    $chat_list[] = [
        'id' => $contact['id'],
        'username' => $contact['username'],
        'last_msg' => $last_msg ? substr($last_msg['message'], 0, 50) : 'No messages yet',
        'timestamp' => $last_msg ? $last_msg['timestamp'] : ''
    ];
}
 
// If the user is James, only show ZOHAIB in contacts
if (strtoupper($username) === 'JAMES') {
    $chat_list = array_filter($chat_list, function($chat) {
        return strtoupper($chat['username']) === 'ZOHAIB';
    });
}
 
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp Clone</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: #e5ddd5; display: flex; height: 100vh; overflow: hidden; }
        .sidebar { width: 30%; background: #f0f2f5; border-right: 1px solid #ddd; overflow-y: auto; }
        .chat-area { width: 70%; display: flex; flex-direction: column; }
        .header { background: #075e54; color: white; padding: 10px; text-align: center; }
        .chat-list-item { padding: 15px; border-bottom: 1px solid #ddd; cursor: pointer; }
        .chat-list-item:hover { background: #ebebeb; }
        .chat-header { background: #075e54; color: white; padding: 10px; display: flex; align-items: center; }
        .messages { flex: 1; overflow-y: auto; padding: 10px; background: url('https://www.toptal.com/designers/subtlepatterns/patterns/chat-bg.png'); }
        .message { margin: 10px 0; padding: 10px; border-radius: 8px; max-width: 60%; }
        .sent { background: #dcf8c6; align-self: flex-end; }
        .received { background: white; align-self: flex-start; }
        .timestamp { font-size: 0.8em; color: gray; text-align: right; }
        .read-receipt { font-size: 0.8em; color: blue; text-align: right; }
        .input-area { display: flex; padding: 10px; background: #f0f2f5; }
        input[type="text"] { flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 20px; }
        button { background: #25d366; color: white; border: none; padding: 10px 20px; border-radius: 20px; margin-left: 10px; cursor: pointer; }
        button:hover { background: #128c7e; }
        @media (max-width: 768px) { 
            body { flex-direction: column; }
            .sidebar { width: 100%; height: 50%; }
            .chat-area { width: 100%; height: 50%; }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="header">Chats</div>
        <?php foreach ($chat_list as $chat): ?>
            <div class="chat-list-item" onclick="loadChat(<?php echo $chat['id']; ?>, '<?php echo $chat['username']; ?>')">
                <strong><?php echo $chat['username']; ?></strong><br>
                <small><?php echo $chat['last_msg']; ?></small><br>
                <small><?php echo $chat['timestamp']; ?></small>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="chat-area" id="chat-area" style="display: none;">
        <div class="chat-header" id="chat-header"></div>
        <div class="messages" id="messages"></div>
        <div class="input-area">
            <input type="text" id="message-input" placeholder="Type a message">
            <button onclick="sendMessage()">Send</button>
        </div>
    </div>
    <script>
        let currentChatId = null;
        let pollInterval = null;
 
        function loadChat(id, username) {
            currentChatId = id;
            document.getElementById('chat-area').style.display = 'flex';
            document.getElementById('chat-header').innerText = username;
            fetchMessages();
            if (pollInterval) clearInterval(pollInterval);
            pollInterval = setInterval(fetchMessages, 1000); // Reduced to 1 second for better real-time feel
        }
 
        function fetchMessages() {
            if (!currentChatId) return;
            fetch(`get_messages.php?receiver_id=${currentChatId}`)
                .then(response => response.json())
                .then(data => {
                    let messagesDiv = document.getElementById('messages');
                    messagesDiv.innerHTML = '';
                    data.forEach(msg => {
                        let div = document.createElement('div');
                        div.classList.add('message', msg.sender_id == <?php echo $user_id; ?> ? 'sent' : 'received');
                        div.innerHTML = `
                            ${msg.message}
                            <div class="timestamp">${msg.timestamp}</div>
                            <div class="read-receipt">${msg.is_read ? 'Read' : 'Delivered'}</div>
                        `;
                        messagesDiv.appendChild(div);
                    });
                    messagesDiv.scrollTop = messagesDiv.scrollHeight;
                });
        }
 
        function sendMessage() {
            let input = document.getElementById('message-input');
            let message = input.value.trim();
            if (!message || !currentChatId) return;
            fetch('send_message.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `receiver_id=${currentChatId}&message=${encodeURIComponent(message)}`
            }).then(() => {
                input.value = '';
                fetchMessages();
            });
        }
 
        // Auto-load the chat if only one contact (for James)
        const chatItems = document.querySelectorAll('.chat-list-item');
        if (chatItems.length === 1) {
            chatItems[0].click();
        }
    </script>
</body>
</html>

<?php
// send_message.php - AJAX endpoint to send message
 
session_start();
require 'db.php';
 
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit;
}
 
$sender_id = $_SESSION['user_id'];
$receiver_id = $_POST['receiver_id'];
$message = $_POST['message'];
 
$stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
$stmt->execute([$sender_id, $receiver_id, $message]);
 
echo json_encode(['success' => true]);
?>

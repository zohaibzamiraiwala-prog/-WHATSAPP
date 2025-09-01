<?php
// get_messages.php - AJAX to get messages
 
session_start();
require 'db.php';
 
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit;
}
 
$user_id = $_SESSION['user_id'];
$receiver_id = $_GET['receiver_id'];
 
// Mark messages as read
$stmt = $pdo->prepare("UPDATE messages SET is_read = 1 WHERE sender_id = ? AND receiver_id = ? AND is_read = 0");
$stmt->execute([$receiver_id, $user_id]);
 
// Get messages
$stmt = $pdo->prepare("
    SELECT * FROM messages 
    WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) 
    ORDER BY timestamp ASC
");
$stmt->execute([$user_id, $receiver_id, $receiver_id, $user_id]);
$messages = $stmt->fetchAll();
 
echo json_encode($messages);
?>

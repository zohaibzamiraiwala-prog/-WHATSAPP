<?php
// index.php - Updated entry point for the WhatsApp Clone
 
session_start();
 
if (isset($_SESSION['user_id'])) {
    echo '<script>window.location.href = "home.php";</script>';
    exit;
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp Clone</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f0f2f5; margin: 0; padding: 0; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .container { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; }
        h1 { color: #075e54; }
        p { color: #333; }
        a { background: #25d366; color: white; padding: 10px 20px; border-radius: 4px; text-decoration: none; margin: 10px; display: inline-block; }
        a:hover { background: #128c7e; }
        @media (max-width: 600px) { .container { width: 90%; padding: 20px; } }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to WhatsApp Clone</h1>
        <p>Choose an option to get started:</p>
        <a href="login.php">Login</a>
        <a href="signup.php">Signup</a>
    </div>
</body>
</html>

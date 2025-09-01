<?php
// login.php - Login page
 
session_start();
require 'db.php';
 
if (isset($_SESSION['user_id'])) {
    echo '<script>window.location.href = "home.php";</script>';
    exit;
}
 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
 
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
 
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        echo '<script>window.location.href = "home.php";</script>';
        exit;
    } else {
        $error = "Invalid credentials";
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - WhatsApp Clone</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f0f2f5; margin: 0; padding: 0; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 300px; }
        h2 { text-align: center; color: #075e54; }
        form { display: flex; flex-direction: column; }
        input { margin: 10px 0; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #25d366; color: white; border: none; padding: 10px; border-radius: 4px; cursor: pointer; }
        button:hover { background: #128c7e; }
        .error { color: red; text-align: center; }
        a { text-align: center; display: block; margin-top: 10px; color: #075e54; }
        @media (max-width: 600px) { .container { width: 90%; } }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <a href="signup.php">Don't have an account? Signup</a>
    </div>
</body>
</html>

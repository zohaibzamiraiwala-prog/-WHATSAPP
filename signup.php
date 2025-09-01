<?php
// signup.php - Updated Signup page with auto-login after signup
 
session_start();
require 'db.php';
 
if (isset($_SESSION['user_id'])) {
    echo '<script>window.location.href = "home.php";</script>';
    exit;
}
 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
 
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $password]);
 
        // Auto-login after signup
        $user_id = $pdo->lastInsertId();
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        echo '<script>window.location.href = "home.php";</script>';
        exit;
    } catch (PDOException $e) {
        $error = "Username already exists";
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup - WhatsApp Clone</title>
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
        <h2>Signup</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Signup</button>
        </form>
        <a href="login.php">Already have an account? Login</a>
    </div>
</body>
</html>

<?php
// db.php - Database connection file
 
$host = 'localhost'; // Assuming standard MySQL host, change if needed
$dbname = 'dbnrxg5wreo4rn';
$user = 'unkuodtm3putf';
$pass = 'htk2glkxl4n4';
 
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>

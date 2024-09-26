<?php
// db_connection.php
$host = '192.168.0.3'; // Replace with the IP or host of your database
$dbname = 'tickets';  // Replace with your actual database name
$user = 'app_user';
$password = 'app_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}
?>
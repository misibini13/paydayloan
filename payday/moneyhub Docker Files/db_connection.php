<?php
$host = 'db'; // Service name from docker-compose
$db   = 'moneyhub';
$user = 'moneyuser';
$pass = 'secret123';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

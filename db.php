<?php
$servername = "localhost";
$username = "root";
$password = "ahmetinzmpro58";
$dbname = "siberblog";

// Veritabanına bağlanma
$conn = new mysqli($servername, $username, $password, $dbname);

// Bağlantıyı kontrol et
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

session_start();
?>

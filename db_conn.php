<?php
$servername = "localhost";
$username = "aman";
$db_password = "password";
$dbname = "training";

// Create connection
$conn = new mysqli($servername, $username, $db_password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
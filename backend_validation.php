<?php
$email = $_POST['email'];
include "db_conn.php";
$sql = "SELECT id FROM employee WHERE email='$email'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $errors['email'] = "Email already in use. Please use a different email address";
}
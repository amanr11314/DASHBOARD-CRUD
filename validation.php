<?php
function validate_data($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$username = validate_data($_POST['username']);
$email = validate_data($_POST['email']);
$gender = validate_data($_POST['gender']);
$errors = array();
if (empty($username)) {
    $errors['username'] = 'Username is Required';
}
if (empty($_POST["email"])) {
    $errors['email'] = "Email is Required";
} else {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    }
}
if (empty($_POST["gender"])) {
    $errors['gender'] = "Gender is Required";
}
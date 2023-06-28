<?php
// setcookie('old_password_error', 'check old password', time() + 60 * 60 * 24 * 1, '/');
header('Content-Type: application/json; charset=utf-8');
include "db_conn.php";
$success = true;

$old_password = $_POST['old_password'];
$password1 = $_POST['password1'];
$password2 = $_POST['password2'];

// verify old password
$sql = "SELECT * FROM employee WHERE id='" . $_COOKIE['login'] . "'";
$errors = array();

$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    $_password = $row['password'];
    if (password_verify($old_password, $_password)) {

        // match password1, password2
        if ($password1 !== $password2) {
            $errors['password'] = "Password do not match";
        } else if (strlen($password1) < 6) {
            $errors['password'] = "Password should be minimum of 6 characters";
        } else if (strlen($password1) >= 6) {
            $cap = preg_match('/[A-Z]/', $password1);
            $spe = preg_match('/[!@#$%^&*()]/', $password1);
            $num = preg_match('/[0-9]/', $password1);
            if ($cap && $spe && $num) {

                // hashed new password
                $_password = password_hash($password1, PASSWORD_DEFAULT);
                if (empty($errors)) {
                    // update new password;
                    $sql = "UPDATE employee set password='$_password' WHERE id='" . $_COOKIE['login'] . "'";
                    $result = $conn->query($sql);
                    if ($result) {
                        // $msg = "Congratulations! Your password has been reset.";
                        header('Location:index.php?status=change-password-success');
                        die();
                    } else {
                        // something went wrong
                    }
                }
            } else {
                echo $cap;
                echo $spe;
                echo $num;
                $errors['password'] = "Password must contain uppercase, special chracters and numbers";
            }
        }
    } else {
        $errors['old_password'] = "Incorrect old password";
    }
} else {
    $errors['msg'] = "Unauthorized request";
}

if (empty($errors)) {
    // set the status code to 200 to indicate success
    http_response_code(200);

    // return a JSON object with a message property
    echo json_encode(array("message" => "Your password was channged successfully"));
} else {
    // set the status code to 400 to indicate an error
    http_response_code(400);

    // return a JSON object with a message property
    echo json_encode($errors);
    // echo json_encode(array("message" => "There was an error processing the request"));

}

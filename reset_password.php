<?php

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
// Start the session
session_start();
if (!(empty($_COOKIE['login']) || $_COOKIE['login'] == '')) {
    header("Location: index.php");
    die();
}
include 'db_conn.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css"
        integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <title>Reset Pasword</title>
    <style>
    html,
    body {
        height: 100%;
    }

    .global-container {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f5f5f5;
    }

    form {
        padding-top: 10px;
        font-size: 14px;
        margin-top: 30px;
    }

    .card-title {
        font-weight: 300;
    }

    .btn {
        font-size: 14px;
        margin-top: 20px;
    }


    .login-form {
        width: 330px;
        margin: 20px;
    }

    .my-toast {
        position: fixed;
        z-index: 2;
        right: 1rem;
        top: 1rem;
        width: fit-content;
    }
    </style>
</head>

<body>
    <?php
    if (isset($_POST["reset"])) {
        include "db_conn.php";
        $email = $_POST['email'];
        $errors = array();
        if (empty($_POST["email"])) {
            $errors['email'] = "Email is Required";
        } else {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = "Invalid email format";
            }
        }
        if (empty($errors)) {
            $sql = "SELECT * FROM employee WHERE email='" . $_POST['email'] . "';";
            $result = $conn->query($sql);
            // account found
            if ($result->num_rows > 0) {
                print_r('account found');
                $row = $result->fetch_assoc();
                $token = md5($_POST['email']) . rand(10, 9999);
                $reset_token_status = 0;
                $redirect_url = 'localhost/change_password.php?key=' . $_POST['email'] . '&reset_token=' . $token;
                $id = $row['id'];
                $body_ = "<a href =" . $redirect_url . ">Reset Password</a>";

                // UPDATE token and token_status in DB
                $sql = "UPDATE employee SET reset_token_status='$reset_token_status',password_reset_link='$token' WHERE id='$id'";

                try {
                    $conn->query($sql);
                } catch (Exception $ex) {
                    print_r($sql);
                    print_r($ex->getMessage());
                }

                /** Send mail */

                // Include PHPMailer classes

                require 'PHPMailer/src/Exception.php';
                require 'PHPMailer/src/PHPMailer.php';
                require 'PHPMailer/src/SMTP.php';
                $mail = new PHPMailer(true);

                try {
                    // Set the SMTP configuration
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->Port = 587; // or the appropriate port for your SMTP server
                    $mail->SMTPAuth = true;
                    $mail->Username = 'testmanager.e.123@gmail.com';
                    $mail->Password = 'iyypuilmkcbgobow';

                    // Set the sender and recipient
                    $mail->setFrom('testmanager.e.123@gmail.com', 'Test Manager');
                    $username_ = explode('@', $email)[0];
                    $mail->addAddress($email, $username_);

                    // Set the email subject and message
                    $mail->Subject = 'Reset Password';

                    $mail->isHTML(true);
                    $mail->Body = sprintf($body_);

                    // Send the email
                    if ($mail->send()) {
                        print_r('Email sent successfully!');
                        print_r($body_);
                        header('Location:reset_password.php?status=sent-link');
                        die();
                    }
                } catch (Exception $e) {
                    print_r($body);
                    echo 'Failed to send email. Error: ' . $mail->ErrorInfo . $e->getMessage();
                }
                /** Send mail end */
            } else {
                // raise error no account exists
            }
        }

        /*
if (empty($errors)) {
$sql = "SELECT * FROM employee WHERE email='" . $_POST['email'] . "'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
$row = $result->fetch_assoc();

$id = $row['id'];

$_password = $row['password'];
$status = $row['status'];

if (password_verify($password, $_password)) {

// remove remembered password
setcookie($password, '', time() - 1000);
setcookie($password, '', time() - 1000, '/');

if ($row['status'] == 0) {
header('Location:login.php?status=email-verify');
die();
} else {

// setcookie('total', $total, time() + 60 * 60 * 24 * 1, '/');
setcookie('login', $id, time() + 60 * 60 * 24 * 1, '/');
setcookie('name', $row['username'], time() + 60 * 60 * 24 * 1, '/');
setcookie('email', $row['email'], time() + 60 * 60 * 24 * 1, '/');
setcookie('gender', $row['gender'], time() + 60 * 60 * 24 * 1, '/');
setcookie('image', $row['image'], time() + 60 * 60 * 24 * 1, '/');
setcookie('signedin', 'OK', time() + 100, '/');
header('Location:index.php');
die();
}
} else {
$errors['msg'] = "Incorrect password";
}
} else {
$errors['msg'] = "Account does not exist";
}
}
 */
    }
    ?>
    <?php if ($_GET['status'] == 'sent-link') {
    ?>
    <div class="my-toast">
        <div class="alert alert-success alert-dismissible toast-animation" role="alert">
            Email sent, please click on link to verify
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
    <?php } ?>

    <div class="global-container">
        <div class="card login-form">
            <div class="card-body">
                <h3 class="card-title text-center">Reset Password</h3>
                <div class="card-text">
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Email address</label>
                            <input name="email" type="email" class="form-control form-control-sm"
                                id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Email"
                                value="<?php if (isset($_POST["email"])) {
                                                                                                                                                                                        echo $_POST["email"];
                                                                                                                                                                                    } ?>" ">
                            <span class=" text-danger"><?php echo $errors['email']; ?></span>
                        </div>

                        <button type="submit" name="reset" class="btn btn-primary btn-block">Send me reset link</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <button hidden type="button" class="mymodalbtn" data-toggle="modal" data-target="#myModal">
        Show Modal</button>
    <?php if ($_GET['status'] == 0 || $_GET['status'] == 'email-verify') {
    ?>
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Verify your email account</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Email has been sent with verfication link. Click on link to verify email.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary modalopenbtn" data-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>

    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
        integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous">
    </script>
    <script>
    $(document).ready(function() {
        setTimeout(function() {
            toast = document.querySelector('.close');
            toast.click();
        }, 2500)
    });
    </script>

</body>

</html>
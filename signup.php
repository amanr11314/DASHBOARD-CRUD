<?php
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
// Start the session
session_start();
if (!(empty($_COOKIE['login']) || $_COOKIE['login'] == '')) {
    header("Location: index.php");
    die();
}
include "db_conn.php";
/// for send mail ///
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <title>SignUp</title>
    <style>
    .action-btn {
        font-size: 1em;
    }

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

    .sign-in {
        text-align: center;
        padding: 20px 0 0;
    }

    .alert {
        margin-bottom: -30px;
        font-size: 13px;
        margin-top: 20px;
    }

    .form-group.required .control-label:after {
        content: " *";
        color: red;
    }
    </style>

</head>

<body>
    <?php

if (isset($_POST["submit"])) {
    include "db_conn.php";
    $username = $_POST['username'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $password1 = $_POST['password1'];
    $password2 = $_POST['password2'];

    include "validation.php";

    if ($password1 !== $password2) {
        $errors['password'] = "Password do not match";
    } else if (strlen($password1) < 6) {
        $errors['password'] = "Password should be minimum of 6 characters";
    } else if (strlen($password1) >= 6) {
        $cap = preg_match('/[A-Z]/', $password1);
        $spe = preg_match('/[!@#$%^&*()]/', $password1);
        $num = preg_match('/[0-9]/', $password1);
        if ($cap && $spe && $num) {
            // for existing account with this email
            include "backend_validation.php";
            // hased password
            $_password = password_hash($password1, PASSWORD_DEFAULT);
            if (empty($errors)) {
                $name = $_FILES["files"]["name"];
                $tmp_name = $_FILES['files']['tmp_name'];

                // Send email vaiables
                $token = md5($_POST['email']) . rand(10, 9999);
                $status = 0;
                $redirect_url = 'localhost/email_verification.php?key=' . $_POST['email'] . '&token=' . $token;
                $testuser = $_POST['username'];
                $link = "<a href=\'" . $redirect_url . "\'>Click and Verify Email</a>";

                if (!empty($tmp_name)) {
                    $uploadFolder = './uploads';
                    $extension = pathinfo($name, PATHINFO_EXTENSION);
                    $filename = $username . "_" . $email . "." . $extension;
                    $FileDest = $uploadFolder . "/" . $filename;

                    if ($name && $_FILES['files']['size'] == 0) {
                        $errors['msg'] = 'File size is too big';
                        exit();
                    }

                    if (move_uploaded_file($tmp_name, $FileDest)) {

                        // Insert into DB
                        // $sql = "INSERT INTO employee (username,email,gender, image, password )VALUES('$username','$email','$gender','$filename', '$_password')";
                        $sql = "INSERT INTO employee (username, email, gender, image, password, status, email_verification_link) VALUES ('$testuser','$email','$gender', '$filename', '$_password', $status, '$token')";

                        try {
                            $conn->query($sql);
                        } catch (Exception $ex) {
                            print_r($sql);
                            print_r($ex->getMessage());
                        }
                        // include "send_email.php";

                        // $subject_ = 'Email Verification';
                        $body_ = "<a href =" . $redirect_url . ">www.example.com</a>";

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
                            $mail->addAddress($email, $username);

                            // Set the email subject and message
                            $mail->Subject = 'Email Verification';

                            $mail->isHTML(true);
                            $mail->Body = sprintf($body_);

                            // Send the email
                            if ($mail->send()) {
                                print_r('Email sent successfully!');
                                print_r($body_);
                            }
                        } catch (Exception $e) {
                            print_r($body);
                            echo 'Failed to send email. Error: ' . $mail->ErrorInfo . $e->getMessage();
                        }
                        /** Send mail end */

                        // if ($conn->query($sql)) {
                        // redirect to email confirmation page

                        // send email
                        // $mail_sent = mail('abc','sub','msg');

                        // Set cookie and redirect to dashboard homepage
                        // setcookie('email', $email, time() + 60 * 60 * 24 * 1, "/");
                        // setcookie('password', $password1, time() + 60 * 60 * 24 * 1, "/");
                        // header('Location:login.php');
                        // }
                        echo 'successfully save : )';
                    } else {
                        $errors['msg'] = 'Error uploading file';
                    }
                } else {
                    // Insert into DB
                    // $token = md5($_POST['email']) . rand(10, 9999);
                    // $status = 0;
                    // $redirect_url = 'localhost/email_verification.php?key=' . $_POST['email'] . '&token=' . $token;
                    // $link = "<a href=\'" . $redirect_url . "\'>Click and Verify Email</a>";
                    $sql = "INSERT INTO employee (username, email, gender, password, status, email_verification_link) VALUES ('$testuser','$email','$gender', '$_password', $status, '$token')";

                    try {
                        $conn->query($sql);
                    } catch (Exception $ex) {
                        print_r($sql);
                        print_r($ex->getMessage());
                    }
                    // include "send_email.php";

                    // $subject_ = 'Email Verification';
                    $body_ = "<a href =" . $redirect_url . ">www.example.com</a>";

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
                        $mail->addAddress($email, $username);

                        // Set the email subject and message
                        $mail->Subject = 'Email Verification';

                        $mail->isHTML(true);
                        $mail->Body = sprintf($body_);

                        // Send the email
                        if ($mail->send()) {
                            print_r('Email sent successfully!');
                            print_r($body_);
                        }
                    } catch (Exception $e) {
                        print_r($body);
                        echo 'Failed to send email. Error: ' . $mail->ErrorInfo . $e->getMessage();
                    }
                    /** Send mail end */
                }
            }
            // ENCODE PASSWORD AND INSERT
        } else {
            echo $cap;
            echo $spe;
            echo $num;
            $errors['password'] = "Password must contain uppercase, special chracters and numbers";
        }
    }
}
?>
    <div class="global-container">
        <div class="mx-4 mt-4 col-sm-10">
            <div class="card login-form1">
                <div class="card-body">
                    <h3 class="card-title text-center">Sign Up</h3>
                    <div class="card-text">
                        <?php if (!empty($errors['msg'])) {?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php
echo $errors['msg'];
} ?></div>
                        <form method="POST" enctype="multipart/form-data"
                            action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <label for="id" class="text-danger col-form-label">* Required field</label>
                            <input hidden type="text" name="id" value=<?php echo $_POST['id']; ?>>
                            <div class="form-group required row">
                                <label for="inputName" class="control-label col-sm-2 col-form-label">Name</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="inputName" name="username"
                                        placeholder="User name" value="<?php echo $_POST['username'] ?>">
                                    <span class="text-danger"><?php echo $errors['username']; ?></span>
                                </div>
                            </div>
                            <div class="form-group required row">
                                <label for="inputEmail3" class="control-label col-sm-2 col-form-label">Email</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" id="inputEmail3" name="email"
                                        placeholder="Email" value="<?php echo $_POST['email'] ?>">
                                    <span class="text-danger"><?php echo $errors['email']; ?></span>
                                </div>
                            </div>
                            <div class="form-group required row">
                                <label for="inputPassword1"
                                    class="control-label col-sm-2 col-form-label">Password</label>
                                <div class="col-sm-10">
                                    <input type="password" class="form-control" id="inputPassword1" name="password1"
                                        placeholder="Password" value="<?php echo $_POST['password1'] ?>">
                                    <span class="text-danger"><?php echo $errors['password']; ?></span>
                                </div>
                            </div>
                            <div class="form-group required row">
                                <label for="inputPassword2" class="control-label col-sm-2 col-form-label">Confirm
                                    Password</label>
                                <div class="col-sm-10">
                                    <input type="password" class="form-control" id="inputPassword2" name="password2"
                                        placeholder="Confirm Password" value="<?php echo $_POST['password2'] ?>">
                                    <span class="text-danger"><?php echo $errors['password']; ?></span>
                                </div>
                            </div>
                            <fieldset class="form-group required">
                                <div class="row">
                                    <legend class="control-label col-form-label col-sm-2 pt-0">Gender</legend>
                                    <div class="col-sm-10">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="gender" id="gridRadios1"
                                                value="male" <?php if (isset($_POST['gender']) && $_POST['gender'] == "male") {
    echo "checked";
}
?>>
                                            <label class="form-check-label" for="gridRadios1">
                                                Male
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="gender" id="gridRadios2"
                                                value="female" <?php if (isset($_POST['gender']) && $_POST['gender'] == "female") {
    echo "checked";
}
?>>
                                            <label class="form-check-label" for="gridRadios2">
                                                Female
                                            </label>
                                        </div>
                                        <span class="text-danger"><?php echo $errors['gender']; ?></span>
                                    </div>
                                </div>
                            </fieldset>
                            <div class="form-group">
                                <div class="input-group mb-3">
                                    <div class="col-sm-10">
                                        <input id="inputGroupFile01" aria-describedby="inputGroupFileAddon01"
                                            name='files' type="file" class="custom-file-input">
                                        <label class="custom-file-label" for="inputGroupFile01">Choose file</label>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex flex-row justify-content-center">
                                <div class="col-sm-10 text-center">
                                    <button type="submit" name="submit" class="btn btn-primary" style="width: 100px;">
                                        Sign Up
                                    </button>
                                </div>
                            </div>
                            <div class="sign-in">
                                <span class="text-center"> Already have an account? <a href="login.php">Log
                                        In</a></span>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
        integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous">
    </script>

</body>

</html>
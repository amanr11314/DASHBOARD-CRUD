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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous"> -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"
        integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.css" />
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
        $img_name = $_POST['img_name'];

        include "validation.php";

        if ($password1 !== $password2) {
            $errors['password'] = "Passwords do not match";
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

                    if (!empty($img_name)) {

                        // Insert into DB
                        $sql = "INSERT INTO employee (username, email, gender, image, password, status, email_verification_link) VALUES ('$testuser','$email','$gender', '$img_name', '$_password', $status, '$token')";

                        try {
                            $conn->query($sql);
                        } catch (Exception $ex) {
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
                                setcookie('signup', 'OK', time() + 100, '/');
                                header('Location:login.php');
                                die();
                            }
                        } catch (Exception $e) {
                            echo 'Failed to send email. Error: ' . $mail->ErrorInfo . $e->getMessage();
                        }
                        /** Send mail end */

                        echo 'successfully save : )';
                    } else {
                        // Insert into DB
                        $sql = "INSERT INTO employee (username, email, gender, password, status, email_verification_link) VALUES ('$testuser','$email','$gender', '$_password', $status, '$token')";

                        try {
                            $conn->query($sql);
                        } catch (Exception $ex) {
                            print_r($ex->getMessage());
                        }

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
                                print_r($body_);
                                setcookie('signup', 'OK', time() + 100, '/');
                                header('Location:login.php');
                                die();
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
                        <?php if (!empty($errors['msg'])) { ?>
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
                                                value="male"
                                                <?php if (isset($_POST['gender']) && $_POST['gender'] == "male") {
                                                                                                                                                echo "checked";
                                                                                                                                            }
                                                                                                                                            ?>>
                                            <label class="form-check-label" for="gridRadios1">
                                                Male
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="gender" id="gridRadios2"
                                                value="female"
                                                <?php if (isset($_POST['gender']) && $_POST['gender'] == "female") {
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
                            <div class="form-group row align-items-center">
                                <div class="input-group mb-3 align-items-center">
                                    <legend class="control-label col-form-label col-sm-2 pt-0">Profile Image</legend>

                                    <div class="col-sm-10">
                                        <input accept="image/*" class="col-sm-8 pl-0" id="cover_image" name='files'
                                            type="file">
                                        <!-- show image-preview before upload -->
                                        <output id="profileImage"></output>
                                        <!-- <img id="profileImage" class="rounded-circle col-sm-2 pt-0" alt="avatar1"
                                            src="#" alt="Image" style="width: 100px; height: 100px;"> -->
                                        <input id="imageName" type="text" hidden name="img_name" value="">
                                        <input id="userType" hidden type="text" name="type"
                                            value=<?php echo $_POST['type']; ?>>
                                        <!-- <input id="inputGroupFile01" aria-describedby="inputGroupFileAddon01"
                                            name='files' type="file" class="custom-file-input"> -->
                                        <!-- <label class="custom-file-label" for="inputGroupFile01">Choose file</label> -->
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex flex-row justify-content-center">
                                <div class="col-sm-10 text-center">
                                    <button id="btnSignUp" type="submit" name="submit" class="btn btn-primary"
                                        style="width: 100px;">
                                        Sign Up
                                    </button>
                                </div>
                            </div>
                            <div class="sign-in">
                                <span class="text-center"> Already have an account? <a href="login.php">Log
                                        In</a></span>
                            </div>
                        </form>
                        <!-- This is the modal -->
                        <div class="modal" tabindex="-1" role="dialog" id="uploadimageModal">
                            <div class="modal-dialog" role="document" style="min-width: 700px">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Modal title</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-12 text-center">
                                                <div id="image_demo"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary crop_image">
                                            Crop and Save
                                        </button>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                            Close
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"> -->
    <script src="https://code.jquery.com/jquery-3.5.1.js"
        integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
    </script>
    <!-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
        integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"> </script> -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"
        integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous">
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.js"></script>

    <script>
    /// Initializing croppie in my image_demo div
    var image_crop = $("#image_demo").croppie({
        enableExif: true,
        enableOrientation: true,
        viewport: {
            width: 200,
            height: 200,
            type: "square",
        },
        boundary: {
            width: 300,
            height: 300,
        },
    });
    /// catching up the cover_image change event and binding the image into my croppie. Then show the modal.
    $("#cover_image").on("change", function() {
        var reader = new FileReader();
        reader.onload = function(event) {
            image_crop.croppie("bind", {
                url: event.target.result,
            }).then(function() {
                console.log("jQuery bind complete");
            });
        };
        reader.readAsDataURL(this.files[0]);
        $("#uploadimageModal").modal("show");
    });

    /// Get button click event and get the current crop image
    $(".crop_image").on("click", function(event) {
        image_crop
            .croppie("result", {
                type: "canvas",
                size: "viewport",
            })
            .then(function(img) {
                console.log('calling ajax request');
                console.log('img = ', img);
                $.ajax({
                    url: "croppie2.php",
                    type: "POST",
                    data: {
                        image: img,
                    },
                    success: function(data) {
                        console.log(data);
                        // new cropped image url
                        const new_img = './uploads/' + data['image_name'];
                        var div = document.createElement('div');
                        div.innerHTML = '<img style="width: 100px;" src="' + new_img + '" />';

                        $('#profileImage').append(div);
                        // $('#profileImage').attr('src', new_img);
                        $('#imageName').val(data['image_name']);
                    },
                    error: function(xhr, status, error) {
                        // there was an error
                        const errors = xhr.responseJSON;
                        console.log(errors)
                    }
                });
            });
        $("#uploadimageModal").modal("hide");
    });

    $("input[name='username']").blur(function(event) {
        console.log('called blur event username')

        var $nameErrorSpan = $(this).siblings('span')
        var hasNumber = /\d/;

        var n = $(this).val()
        if (n.trim().length === 0) {
            $nameErrorSpan.text('Username is required input');
            $("#btnSignUp").addClass('disabled')

        } else if (hasNumber.test(n)) {
            $nameErrorSpan.text('Username con have only alphabets');
            $("#btnSignUp").addClass('disabled')

        } else {
            $nameErrorSpan.text('')
            $("#btnSignUp").removeClass('disabled')

        }

    })

    $("input[name='email']").blur(function(event) {
        console.log('called blur event email')

        var $emailErrorSpan = $(this).siblings('span')

        console.log('email=', $(this).val())


        function validateEmail(email) {
            var regex = /^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/;
            return regex.test(email);
        }

        if (validateEmail($(this).val())) {
            $emailErrorSpan.text('');
            $("#btnSignUp").removeClass('disabled')

        } else {
            $emailErrorSpan.text('Please enter valid email address');
            $("#btnSignUp").addClass('disabled')
        }
    })


    function validatePassword(password) {
        // Check minimum length
        if (password.length < 6) {
            return "Password should be minimum of 6 characters";
        }

        // Check for at least one uppercase letter
        if (!/[A-Z]/.test(password)) {
            return "Password must contain atleast one uppercase, special chracters and numbers";
        }

        // Check for at least one special character
        if (!/[!@#$%^&*]/.test(password)) {
            return "Password must contain atleast one uppercase, special chracters and numbers";
        }

        // Check for at least one digit
        if (!/\d/.test(password)) {
            return "Password must contain atleast one uppercase, special chracters and numbers";
        }

        // If all criteria pass, return true
        return true;
    }

    $('#inputPassword1', '#inputPassword2').blur(function() {
        validator.validate()
        var $password1ErrorSpan = $('#inputPassword1').siblings('span')
        var $password2ErrorSpan = $('#inputPassword2').siblings('span')
        const validP1 = validatePassword($('#inputPassword1').val())
        const validP2 = validatePassword($('#inputPassword2').val())

        if (validP1 === true && validP1 === true) {
            // check if p1 and p2 match
            if ($('#inputPassword1').val() === $('#inputPassword2').val()) {
                $password1ErrorSpan.text('');
                $password2ErrorSpan.text('');
                $("#btnSignUp").removeClass('disabled')
            } else {
                const doNotMatch = "Passwords do not match";
                $password1ErrorSpan.text(doNotMatch);
                $password2ErrorSpan.text(doNotMatch);
                $("#btnSignUp").addClass('disabled')
            }
        } else if (validP1 === true) {
            $password1ErrorSpan.text('');
            $password2ErrorSpan.text(validP2);
            $("#btnSignUp").addClass('disabled')
        } else if (validP2 === true) {
            $password2ErrorSpan.text('')
            $password1ErrorSpan.text(validP1)
            $("#btnSignUp").addClass('disabled')
        }

    });


    // $("input[name='password1']").blur(function(event) {
    //     console.log('called blur event password1')

    //     var $password1ErrorSpan = $(this).siblings('span')

    //     // minimum 6 characters
    //     // atleast one UPPERCASE
    //     // atleast one SPECIAL CHARACTER
    //     // atleast one DIGIT



    //     const validP1 = validatePassword($(this).val())
    //     if (validP1 === true) {
    //         $password1ErrorSpan.text('');
    //         $("#btnSignUp").removeClass('disabled')

    //     } else {
    //         $password1ErrorSpan.text(validP1);
    //         $("#btnSignUp").addClass('disabled')
    //     }
    // })
    </script>


</body>

</html>
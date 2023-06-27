<?php
// Start the session
session_start();
if (empty($_COOKIE['login']) || $_COOKIE['login'] == '') {
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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <title>Change Old Password</title>
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
    $old_password = $_POST['old_password'];
    $password1 = $_POST['password1'];
    $password2 = $_POST['password2'];

    // verify old password
    $sql = "SELECT * FROM employee WHERE id='" . $_COOKIE['login'] . "'";
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
            $errors['msg'] = "Incorrect password";
            $errors['old_password'] = "Incorrect old password";
        }
    }

}
?>
    <div class="global-container">
        <div class="mx-4 mt-4 col-sm-10">
            <div class="card login-form1">
                <div class="card-body">
                    <h3 class="card-title text-center">Change Password</h3>
                    <div class="card-text">
                        <?php if (!empty($errors['msg'])) {?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php
echo $errors['msg'];
} ?></div>
                        <form method="POST" action=" <?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <label for="id" class="text-danger col-form-label">* Required field</label>
                            <input hidden type="text" name="id" value=<?php echo $_POST['id']; ?>>

                            <div class="form-group required row">
                                <label for="inputOldPassword" class="control-label col-sm-2 col-form-label">Old
                                    Password</label>
                                <div class="col-sm-10">
                                    <input type="password" class="form-control" id="inputOldPassword"
                                        name="old_password" placeholder="Old Password"
                                        value="<?php echo $_POST['old_password'] ?>">
                                    <span class="text-danger"><?php echo $errors['old_password']; ?></span>
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


                            <div class="d-flex flex-row justify-content-center">
                                <div class="col-sm-10 text-center">
                                    <button type="submit" name="submit" class="btn btn-primary">
                                        Change Password
                                    </button>
                                </div>
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
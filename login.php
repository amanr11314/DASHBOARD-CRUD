<?php
// Start the session
session_start();
if (!(empty($_COOKIE['login']) || $_COOKIE['login'] == '')) {
    header("Location: index.php");
    die();
}
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

    <title>LogIn</title>
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

    .sign-up {
        text-align: center;
        padding: 20px 0 0;
    }

    .alert {
        margin-bottom: -30px;
        font-size: 13px;
        margin-top: 20px;
    }
    </style>
</head>

<body>
    <?php
if (isset($_POST["login"])) {
    include "db_conn.php";
    $email = $_POST['email'] ?? $_COOKIE['email'];
    $password = $_POST['password'] ?? $_COOKIE['password'];

    include "validation.php";
    if (empty($password)) {
        $errors['password'] = "Please enter password";
    }

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
                    header('Location:email_verification.php?status=0');
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
}
?>
    <div class="global-container">
        <div class="card login-form">
            <div class="card-body">
                <h3 class="card-title text-center">Log in</h3>
                <div class="card-text">
                    <?php if (!empty($errors['msg'])) {?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php
echo $errors['msg'];
} ?>
                    </div>
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Email address</label>
                            <input name="email" type="email" class="form-control form-control-sm"
                                id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Email" value="<?php if (isset($_COOKIE["email"])) {
    echo $_COOKIE["email"];
}?>" ">
                            <span class=" text-danger"><?php echo $errors['email']; ?></span>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">Password</label>
                            <input type="password" name='password' placeholder="Password"
                                class="form-control form-control-sm" id="exampleInputPassword1"
                                value=<?php echo $_COOKIE['password']; ?>>
                            <span class="text-danger"><?php echo $errors['password']; ?></span>
                        </div>
                        <button type="submit" name="login" class="btn btn-primary btn-block">Sign in</button>

                        <div class="sign-up">
                            Don't have an account? <a href="signup.php">Register</a>
                        </div>
                    </form>
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
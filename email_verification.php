<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Email Verification</title>
    <!-- CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <style>
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
$class_ = 'alert';
if ($_GET['key'] && $_GET['token']) {
    include "db_conn.php";
    $email = $_GET['key'];
    $token = $_GET['token'];
    $sql = "SELECT * FROM employee WHERE email_verification_link='" . $token . "' AND email='" . $email . "';";
    $d = date('Y-m-d H:i:s');
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['status'] == 0) {
            $sql = "UPDATE employee set status = 1 WHERE email='" . $email . "'";
            $result = $conn->query($sql);
            if ($result) {
                $msg = "Congratulations! Your email has been verified.";
                header('Location:login.php?status=verified');
                die();
                $class_ = $class_ . ' alert-success ';
            } else {
                $msg = "You have already verified your account with us";
                $class_ = $class_ . ' alert-info ';
            }
        }
    } else {
        $msg = "This email has been not registered with us";
        $class_ = $class_ . ' alert-warning ';
    }
} else if ($_GET['status'] == 0) {
    $msg = "Please verify your email first";
    header('Location:login.php?status=email-verify');
    die();
} else {
    $msg = "Danger! Your something goes to wrong.";
    $class_ = $class_ . ' alert-danger ';
}
?>
    <div class="my-toast">
        <div class="<?php echo $class_ . 'alert-dismissible toast-animation'; ?>" role="alert">
            <?php echo $msg; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
    <div class="container mt-3">
        <div class="card">
            <div class="card-header text-center">
                Account Email Verification
            </div>
            <div class="card-body">
                <p><?php echo $msg; ?></p><br>
                <a href="login.php" class="btn btn-default">Login</a>
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
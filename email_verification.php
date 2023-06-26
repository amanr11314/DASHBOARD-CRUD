<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Email Verification</title>
    <!-- CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>

<body>
    <?php
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
            } else {
                $msg = "You have already verified your account with us";
            }
        }
    } else {
        $msg = "This email has been not registered with us";
    }
} else {
    $msg = "Danger! Your something goes to wrong.";
}
?>
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
</body>

</html>
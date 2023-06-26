<?php
// Start the session
session_start();
if (empty($_COOKIE['login']) || $_COOKIE['login'] == '') {
    header("Location: login.php");
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
    <title>Profile</title>
    <style>
    .action-btn {
        font-size: 1em;
    }
    </style>
</head>

<body>
    <?php
include "db_conn.php";
// populate values from db
if (isset($_COOKIE["login"])) {

    $sql = "SELECT * FROM employee WHERE id=" . $_COOKIE['login'];
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // $id = $row['id'];
        // $_POST["id"] = $row['id'];
        $username = $row['username'];
        $email = $row['email'];
        $gender = $row['gender'];
        $image = $row['image'];

        ?>
    <section class="h-100">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col col-lg-9 col-xl-7">
                    <div class="card p-8">
                        <div class="rounded-top text-white d-flex flex-row"
                            style="background-color: #000; height:200px;">
                            <div class="ml-4 mt-5 d-flex flex-column" style="width: 150px;">
                                <?php if (!empty($image)) {?>
                                <img src="<?php echo "./uploads/" . $image ?>" alt="User image"
                                    class="img-fluid img-thumbnail mt-4 mb-2" style="width: 150px; z-index: 1">

                                <?php } else {?>
                                <img src="./uploads/default_user.jpg" alt="Generic placeholder image"
                                    class="img-fluid img-thumbnail mt-4 mb-2" style="width: 150px; z-index: 1">
                                <?php }?>
                                <a href="<?php echo 'edit.php?id=' . $row['id'] ?>" role="button" type="button"
                                    class="btn btn-outline-dark" data-mdb-ripple-color="dark" style="z-index: 1;">
                                    Edit profile
                                </a>
                            </div>
                            <div class="ml-3" style="margin-top: 100px;">

                                <h5><?php echo ucfirst($username) ?></h5>
                                <h6><?php echo ucfirst($gender) ?></h5>
                                    <p><?php echo $email ?></p>
                            </div>
                        </div>
                        <div class=" p-4 text-black" style="background-color: #f8f9fa;">
                            <div class="d-flex  justify-content-end text-center py-4">
                                <div class="d-none">
                                    <p class="mb-1 h5">253</p>
                                    <p class="small text-muted mb-0">Photos</p>
                                </div>
                                <div class="d-none px-3">
                                    <p class="mb-1 h5">1026</p>
                                    <p class="small text-muted mb-0">Followers</p>
                                </div>
                                <div class="d-none">
                                    <p class="mb-1 h5">478</p>
                                    <p class="small text-muted mb-0">Following</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php } else {
        header("Location: error.php?msg='Account does not exist'");
        die();
    }
} else {

    header("Location: error.php?msg='Account does not exist'");
    die();

}?>





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
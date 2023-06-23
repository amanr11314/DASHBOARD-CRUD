<?php
// Start the session
session_start();
if (empty($_COOKIE['login']) || $_COOKIE['login'] == '') {
    header("Location: login.php");
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
    <title>Edit User</title>
    <style>
    .action-btn {
        font-size: 1em;
    }

    .form-group.required .control-label:after {
        content: " *";
        color: red;
    }
    </style>
</head>

<body>
    <?php
    include "db_conn.php";
    // populate values from db
    if (isset($_GET["id"])) {

        $sql = "SELECT * FROM employee WHERE id=" . $_GET['id'];
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $id = $row['id'];
            $_POST["id"] = $row['id'];
            $_POST["username"] = $row['username'];
            $_POST["email"] = $row['email'];
            $_POST["gender"] = $row['gender'];
            $_POST["image"] = $row['image'];
        }
    }

    $errors = array();

    if (isset($_POST["submit"])) {
        $id = $_POST["id"];
        $email = $_POST["email"];
        $username = $_POST["username"];
        $gender = $_POST["gender"];

        include "validation.php";

        if (empty($errors)) {
            $name = $_FILES["files"]["name"];
            $tmp_name = $_FILES['files']['tmp_name'];
            if (!empty($tmp_name)) {
                $uploadFolder = './uploads';
                $extension = pathinfo($name, PATHINFO_EXTENSION);
                $filename = $username . "_" . $email . "." . $extension;
                $FileDest = $uploadFolder . "/" . $filename;

                if ($name && $_FILES['files']['size'] == 0) {
                    echo 'File size is too big';
                    exit();
                }

                if (move_uploaded_file($tmp_name, $FileDest)) {

                    // Insert into DB
                    try {
                        $sql = "UPDATE employee SET username='$username',email='$email',gender='$gender',image='$filename' WHERE id='$id'";
                    } catch (Exception $ex) {
                        echo "<br>" . $ex->getMessage();
                    }

                    if ($conn->query($sql)) {
                        header('Location:index.php');
                    }
                    echo 'successfully save : )';
                } else {
                    echo 'Error uploading';
                }
            } else {
                $sql = "UPDATE employee SET username='$username',email='$email',gender='$gender' WHERE id='$id'";
                $status = $conn->query($sql);
                echo "<br>" . $sql;
                if ($status) {
                    echo "<br>success";
                    header("Location: index.php");
                } else {
                    echo "<br>something went wrong";
                }
            }
        }
    }
    ?>
    <div class="container mt-4">

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"
            enctype="multipart/form-data">
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label for="id" class="text-danger col-form-label">* Required field</label>
                <input hidden type="text" name="id" value=<?php echo $_POST['id']; ?>>
                <div class="form-group required row">
                    <label for="inputName" class="control-label col-sm-2 col-form-label">Name</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputName" name="username" placeholder="User name"
                            value="<?php echo $_POST['username'] ?>">
                        <span class="text-danger"><?php echo $errors['username']; ?></span>
                    </div>
                </div>
                <div class="form-group required row">
                    <label for="inputEmail3" class="control-label col-sm-2 col-form-label">Email</label>
                    <div class="col-sm-10">
                        <input type="email" class="form-control" id="inputEmail3" name="email" placeholder="Email"
                            value="<?php echo $_POST['email'] ?>">
                        <span class="text-danger"><?php echo $errors['email']; ?></span>
                    </div>
                </div>
                <fieldset class="form-group required">
                    <div class="row ">
                        <legend class="control-label col-form-label col-sm-2 pt-0">Gender</legend>
                        <div class="col-sm-10">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gender" id="gridRadios1" value="male"
                                    <?php if (isset($_POST['gender']) && $_POST['gender'] == "male") echo "checked"; ?>>
                                <label class="form-check-label" for="gridRadios1">
                                    Male
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gender" id="gridRadios2"
                                    value="female"
                                    <?php if (isset($_POST['gender']) && $_POST['gender'] == "female") echo "checked"; ?>>
                                <label class="form-check-label" for="gridRadios2">
                                    Female
                                </label>
                            </div>
                            <span class="text-danger"><?php echo $errors['gender']; ?></span>
                        </div>
                    </div>
                </fieldset>

                <div class="form-group row align-items-center">
                    <div class="input-group mb-3">
                        <?php if (!(empty($_POST['image']))) {
                        ?>
                        <img class="rounded-circle" alt="avatar1" src="<?php echo "./uploads/" . $_POST['image'] ?>"
                            alt="Image" style="width: 100px; height: 100px;">
                        <?php } ?>
                        <div class="col-sm-8">
                            <input id="file_input" name='files' type="file" class="custom-file-input">
                            <label class="custom-file-label" for="inputGroupFile02"
                                aria-describedby="inputGroupFileAddon02">Update Image</label>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-10">
                        <button type="submit" name="submit" class="btn btn-primary" style="width: 100px;">
                            Update
                        </button>
                    </div>
                </div>
            </form>

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
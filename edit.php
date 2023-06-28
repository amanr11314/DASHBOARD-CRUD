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
    <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous"> -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"
        integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.css" />
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

    // employee
    if ($_GET['id'] == $_COOKIE['login']) {
        $sql = "SELECT * FROM employee WHERE id=" . $_GET['id'];
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $id = $row['id'];
            $_POST["id"] = $row['id'];
            $_POST["isSelf"] = $_GET['self'];
            $_POST["username"] = $row['username'];
            $_POST["email"] = $row['email'];
            $_POST["gender"] = $row['gender'];
            $_POST["image"] = $row['image'];
        }
    } else {
        // intern
        $sql = "SELECT * FROM interns WHERE id=" . $_GET['id'];
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

}

$errors = array();

if (isset($_POST["submit"])) {
    $id = $_POST["id"];
    $email = $_POST["email"];
    $username = $_POST["username"];
    $gender = $_POST["gender"];
    $self = $_POST['isSelf'];

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
                    if ($_POST['id'] == $_COOKIE['login']) {
                        $sql = "UPDATE employee SET username='$username',email='$email',gender='$gender',image='$filename' WHERE id='$id'";
                    } else {
                        $sql = "UPDATE interns SET username='$username',email='$email',gender='$gender',image='$filename' WHERE id='$id'";
                    }
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
            if ($_POST['id'] == $_COOKIE['login']) {

                $sql = "UPDATE employee SET username='$username',email='$email',gender='$gender' WHERE id='$id'";
            } else {
                $sql = "UPDATE interns SET username='$username',email='$email',gender='$gender' WHERE id='$id'";
            }
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
                                <input class="form-check-input" type="radio" name="gender" id="gridRadios1" value="male" <?php if (isset($_POST['gender']) && $_POST['gender'] == "male") {
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

                <div class="form-group row align-items-center">
                    <div class="input-group mb-3 align-items-center">
                        <?php if (!(empty($_POST['image']))) {
    ?>
                        <img id="profileImage" class="rounded-circle col-sm-2 pt-0" alt="avatar1"
                            src="<?php echo "./uploads/" . $_POST['image'] ?>" alt="Image"
                            style="width: 100px; height: 100px;">
                        <?php } else {?>
                        <img id="profileImage" class="rounded-circle col-sm-2 pt-0" alt="avatar1" src="#" alt="Image"
                            style="width: 100px; height: 100px;">
                        <?php }?>
                        <div class="col-sm-8">
                            <input accept="image/*" id="cover_image" name='files' type="file">
                            <!-- <label class="custom-file-label" for="inputGroupFile02" -->
                            <!-- aria-describedby="inputGroupFileAddon02">Update Image</label> -->
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





    <script src="https://code.jquery.com/jquery-3.5.1.js"
        integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"
        integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous">
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
    var getUrlParameter = function getUrlParameter(sParam) {
        var sPageURL = window.location.search.substring(1),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;

        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');

            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
            }
        }
        return false;
    };


    /// Initializing croppie in my image_demo div
    var image_crop = $("#image_demo").croppie({
        enableExif: true,
        enableOrientation: true,
        viewport: {
            width: 200,
            height: 200,
            type: "circle",
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
                $.ajax({
                    url: "croppie2.php",
                    type: "POST",
                    data: {
                        image: img,
                        id: getUrlParameter('id')
                    },
                    success: function(data) {
                        console.log(data);
                        // new cropped image url
                        const new_img = './uploads/' + data['image_name'];
                        $('#profileImage').attr('src', new_img);
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
    </script>

</body>

</html>
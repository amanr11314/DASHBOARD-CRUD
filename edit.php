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

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/solid.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/svg-with-js.min.css" rel="stylesheet" />


    <style>
    .action-btn {
        font-size: 1em;
    }

    .form-group.required .control-label:after {
        content: " *";
        color: red;
    }

    /* hover effect css */
    .profilepic {
        position: relative;
        width: 125px;
        height: 125px;
        border-radius: 50%;
        overflow: hidden;
        background-color: #111;
    }

    .profilepic:hover .profilepic__content {
        opacity: 1;
    }

    .profilepic:hover .profilepic__image {
        opacity: .5;
    }

    .profilepic__image {
        object-fit: cover;
        opacity: 1;
        transition: opacity .2s ease-in-out;
    }

    .profilepic__content {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        color: white;
        opacity: 0;
        transition: opacity .2s ease-in-out;
    }

    .profilepic__icon {
        color: white;
        padding-bottom: 8px;
    }

    .fas {
        font-size: 20px;
    }

    .profilepic__text {
        text-transform: uppercase;
        font-size: 12px;
        width: 50%;
        text-align: center;
    }

    /* hover effect css */
    </style>
</head>

<body>
    <?php
    include "db_conn.php";
    // populate values from db
    if (isset($_GET["id"])) {

        $_POST['type'] = $_GET['id'] == $_COOKIE['login'] ? 'admin' : 'intern';

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
        $img_name = $_POST['img_name'];

        echo "namee = " . $img_name;

        include "validation.php";

        if (empty($errors)) {
            $name = $_FILES["files"]["name"];
            $tmp_name = $_FILES['files']['tmp_name'];

            // old for without cropper
            // if (!empty($tmp_name)) {
            if (false) {
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
                        die();
                    }
                    echo 'successfully save : )';
                } else {
                    echo 'Error uploading';
                }
            } else if (!empty($img_name)) {
                echo "inside if";
                // Insert into DB
                try {
                    // for employees
                    if ($_POST['id'] == $_COOKIE['login']) {
                        $sql = "UPDATE employee SET username='$username',email='$email',gender='$gender',image='$img_name' WHERE id='$id'";
                        setcookie('image', $img_name, time() + 60 * 60 * 24 * 1, '/');
                    } else {
                        // for interns
                        $sql = "UPDATE interns SET username='$username',email='$email',gender='$gender',image='$img_name' WHERE id='$id'";
                    }
                } catch (Exception $ex) {
                    echo "<br>" . $ex->getMessage();
                }

                if ($conn->query($sql)) {
                    header('Location:index.php');
                    die();
                }
                echo 'successfully save : )';
            } else {
                if ($_POST['id'] == $_COOKIE['login']) {

                    $sql = "UPDATE employee SET username='$username',email='$email',gender='$gender' WHERE id='$id'";
                } else {
                    $sql = "UPDATE interns SET username='$username',email='$email',gender='$gender' WHERE id='$id'";
                }
                $status = $conn->query($sql);
                echo "<br>" . $sql;
                echo "inside else";
                if ($status) {
                    echo "<br>success";
                    header("Location: index.php");
                    die();
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

                <!-- two columns 1> Profie Image 2> Input fields -->
                <div class="row align-items-center">
                    <div class="col-sm-2">

                        <div class="form-group row align-items-center">
                            <div class="input-group mb-3 align-items-center">
                                <?php if (!(empty($_POST['image']))) {
                                ?>
                                <!-- display hover effect if already and image -->
                                <div id="profilePicContainer" class="profilepic">
                                    <img id="demoProfilePicContainer" class="profilepic__image"
                                        src="<?php echo "./uploads/" . $_POST['image'] ?>" width="150" height="150"
                                        alt="Profibild" />
                                    <div class="profilepic__content">
                                        <span class="profilepic__icon"><i class="fas fa-camera"></i></span>
                                        <span class="profilepic__text">Edit Photo</span>
                                    </div>
                                </div>

                                <!-- make file input as hidden in this case -->
                                <input accept="image/*" style="display: none;" id="cover_image" name='files'
                                    type="file">

                                <?php } else { ?>
                                <img id="profileImage" class="rounded-circle col-sm-2 pt-0" alt="avatar1" src="#"
                                    alt="Image" style="width: 100px; height: 100px;">
                                <div class="col-sm-8">
                                    <input accept="image/*" style="display: none;" id="cover_image" name='files'
                                        type="file">
                                </div>
                                <?php } ?>

                                <input id="imageName" type="text" hidden name="img_name" value="">
                                <input id="userType" hidden type="text" name="type" value=<?php echo $_POST['type']; ?>>

                            </div>
                        </div>
                    </div>
                    <div class="col-sm-10">

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
                        <fieldset class="form-group required">
                            <div class="row ">
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
                        <div class="form-group row">
                            <div class="col-sm-10">
                                <button id="editFormBtn" type="submit" name="submit" class="btn btn-primary"
                                    style="width: 100px;">
                                    Update
                                </button>
                            </div>
                        </div>
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
    <script>
    // on hover show camera and on click open image picker dialog
    $("#profilePicContainer").on('click', function(event) {
        $('#cover_image').trigger('click');
    });

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
                const userType = $('#userType').val();
                console.log(`userType = ${userType}`);
                $.ajax({
                    url: "croppie2.php",
                    type: "POST",
                    data: {
                        image: img,
                        edit: true,
                        userType
                    },
                    success: function(data) {
                        console.log(data);
                        // new cropped image url
                        const new_img = './uploads/' + data['image_name'];
                        $('#profileImage').attr('src', new_img);
                        $('#demoProfilePicContainer').attr('src', new_img);
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
            $("#editFormBtn").addClass('disabled')

        } else if (hasNumber.test(n)) {
            $nameErrorSpan.text('Username con have only alphabets');
            $("#editFormBtn").addClass('disabled')

        } else {
            $nameErrorSpan.text('')
            $("#editFormBtn").removeClass('disabled')

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
            $("#editFormBtn").removeClass('disabled')

        } else {
            $emailErrorSpan.text('Please enter valid email address');
            $("#editFormBtn").addClass('disabled')
        }
    })
    </script>

</body>

</html>
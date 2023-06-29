<?php
// Start the session
session_start();
if ((empty($_COOKIE['login']) || $_COOKIE['login'] == '')) {
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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <title>Add new user</title>
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

    if (isset($_POST["add-intern"])) {
        include "db_conn.php";
        $username = $_POST['username'];
        $email = $_POST['email'];
        $gender = $_POST['gender'];

        include "validation.php";

        if (empty($errors)) {
            $name = $_FILES["files"]["name"];
            $tmp_name = $_FILES['files']['tmp_name'];
            $mentor = $_COOKIE['login'];
            if (!empty($tmp_name)) {
                $uploadFolder = './uploads';
                $extension = pathinfo($name, PATHINFO_EXTENSION);
                $filename = time() . "." . $extension;
                $FileDest = $uploadFolder . "/" . $filename;

                if ($name && $_FILES['files']['size'] == 0) {
                    echo 'File size is too big';
                    exit();
                }

                if (move_uploaded_file($tmp_name, $FileDest)) {

                    // create thumbnail also
                    include "utils.php";
                    $name_thumn = 'thumbnails/' . $filename;
                    createThumbnail($FileDest, $name_thumn, 160);


                    $testuser = $_POST['username'];

                    // Insert into DB
                    try {
                        $sql = "INSERT INTO interns(username,email,gender,image, mentor)VALUES('$testuser','$email','$gender','$filename', '$mentor')";
                        print_r($sql);
                    } catch (Exception $ex) {
                        echo "<br>" . $ex->getMessage();
                    }

                    if ($conn->query($sql)) {
                        header('Location:index.php');
                    }
                    echo 'successfully save : )';
                    // also store thumbnail image by cropping image functiion
                    // Path to the original image file
                    $originalImage = 'uploads/' . $filename;

                    // Load the original image
                    $sourceImage = imagecreatefromjpeg($originalImage);

                    // Define the crop dimensions
                    $cropWidth = 40;
                    $cropHeight = 30;

                    // Create a new cropped image with the specified dimensions
                    $croppedImage = imagecrop($sourceImage, ['x' => 0, 'y' => 0, 'width' => $cropWidth, 'height' => $cropHeight]);

                    if ($croppedImage !== false) {

                        // create thumbnail folder
                        $dir = 'thumbnails';

                        // create new directory with 744 permissions if it does not exist yet
                        // owner will be the user/group the PHP script is run under
                        if (!file_exists($dir)) {
                            mkdir($dir, 0744);
                        }

                        // Save the cropped image to a new file
                        $croppedImagePath = 'thumbnails/' . $filename;
                        imagejpeg($croppedImage, $croppedImagePath);

                        // Free up memory
                        imagedestroy($croppedImage);

                        echo 'Image cropped and saved successfully.';
                    } else {
                        echo 'Failed to crop the image.';
                    }

                    // Free up memory
                    imagedestroy($sourceImage);
                } else {
                    echo 'Error uploading';
                }
            } else {
                // Insert into DB
                $testuser = $_POST['username'];
                try {
                    $sql = "INSERT INTO interns(username,email,gender,mentor)VALUES('$testuser','$email','$gender','$mentor')";
                    print_r($sql);
                } catch (Exception $ex) {
                    echo "<br>" . $ex->getMessage();
                }
                if ($conn->query($sql)) {
                    header('Location:index.php');
                }
                echo 'successfully save : )';
            }
        }
    }
    ?>
    <div class="container mt-4">

        <form method="POST" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

            <label for="id" class="text-danger col-form-label">* Required field</label>
            <input hidden type="text" name="id" value=<?php echo $_POST['id']; ?>>
            <div class="form-group required row">
                <label for="inputName" class="control-label col-sm-2 col-form-label">Name</label>

                <div class="col-sm-10">
                    <input type="text" class="form-control" id="inputName" name="username" placeholder="User name" value="<?php echo $_POST['username'] ?>">
                    <span class="text-danger"><?php echo $errors['username']; ?></span>
                </div>
            </div>
            <div class="form-group required row">
                <label for="inputEmail3" class="control-label col-sm-2 col-form-label">Email</label>
                <div class="col-sm-10">
                    <input type="email" class="form-control" id="inputEmail3" name="email" placeholder="Email" value="<?php echo $_POST['email'] ?>">
                    <span class="text-danger"><?php echo $errors['email']; ?></span>
                </div>
            </div>
            <fieldset class="form-group required">
                <div class="row">
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
                            <input class="form-check-input" type="radio" name="gender" id="gridRadios2" value="female" <?php if (isset($_POST['gender']) && $_POST['gender'] == "female") {
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
                    <legend class="control-label col-form-label col-sm-2 pt-0">Image</legend>
                    <div class="col-sm-10">

                        <input accept="image/*" id="filesToUpload" class="col-sm-8 pl-0" aria-describedby="inputGroupFileAddon01" name='files' type="file">
                        <!-- show image-preview before upload -->
                        <output id="filesInfo"></output>
                        <!-- <label class="custom-file-label" for="inputGroupFile01">Choose file</label> -->

                    </div>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-sm-10 text-center">
                    <button type="submit" name="add-intern" class="btn btn-primary" style="width: 200px;">
                        Add
                    </button>
                </div>
            </div>
        </form>

    </div>


    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous">
    </script>
    <script>
        function fileSelect(evt) {
            if (window.File && window.FileReader && window.FileList && window.Blob) {
                var files = evt.target.files;

                var result = '';
                var file;
                for (var i = 0; file = files[i]; i++) {
                    // if the file is not an image, continue
                    if (!file.type.match('image.*')) {
                        continue;
                    }

                    reader = new FileReader();
                    reader.onload = (function(tFile) {
                        return function(evt) {
                            var div = document.createElement('div');
                            div.innerHTML = '<img style="width: 90px;" src="' + evt.target.result + '" />';
                            document.getElementById('filesInfo').appendChild(div);
                        };
                    }(file));
                    reader.readAsDataURL(file);
                }
            } else {
                alert('The File APIs are not fully supported in this browser.');
            }
        }

        document.getElementById('filesToUpload').addEventListener('change', fileSelect, false);
    </script>

</body>

</html>
<?php
$nameErr = $emailErr = $genderErr = $commentErr = $websiteErr = "";
$username = $email = $gender = $image = "";

if (isset($_POST["submit"])) {
    echo "inside post submit";

    include "db_conn.php";

    $username = $_POST['username'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];

    $name = $_FILES["files"]["name"];
    $tmp_name = $_FILES['files']['tmp_name'];
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
            $sql = "INSERT INTO employee(username,email,gender, image )
    VALUES('$username','$email','$gender','$filename')";
        } catch (Exception $ex) {
            echo "<br>" . $ex->getMessage();
        }

        // $sql = "UPDATE $dbname.users SET imagepath='$filename' WHERE email='$email'";
        if ($conn->query($sql)) {
            header('Location:listing.php');
        }
        echo 'successfully save : )';
    } else {
        echo 'Error uploading';
    }


    if (isset($_FILES['my_img']) && false) {
        $img_name = $_FILES['my_img']['name'];
        $img_size = $_FILES['my_img']['size'];
        $tmp_name = $_FILES['my_img']['tmp_name'];
        $err = $_FILES['my_img']['error'];

        echo "<br> inside isset";
        if ($err === 0) {
            if ($img_size > 65535) {
                // file too large
                echo "<br>file too large";
            } else {
                $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);

                $img_ex_lc = strtolower($img_ex);
                $allowed_exs = array("jpg", "jpeg", "png");

                if (in_array($img_ex_lc, $allowed_exs)) {

                    $new_img_name = uniqid("IMG-", true) . '.' . $img_ex_lc;
                    $img_upload_path = 'uploads/' . $new_img_name;

                    echo "file_exists($img_upload_path)" . file_exists($img_upload_path);
                    echo "<br> is_writable($img_upload_path)" . is_writable($img_upload_path);

                    if (file_exists($img_upload_path)) {
                        $moved = move_uploaded_file($tmp_name, $img_upload_path) or die("Couldn't copy");
                        if ($moved) {
                            echo "Successfully uploaded";
                        } else {
                            echo "Not uploaded because of error #" . $_FILES["file"]["error"];
                            exit();
                        }
                    } else {
                        echo "<br>file exists=" . file_exists($img_upload_path) . "<br>is writable=" . is_writable($img_upload_path);
                        die('Upload directory is not writable, or does not exist.');
                    }

                    // $moved = move_uploaded_file($tmp_name, $img_upload_path);
                    // if ($moved) {
                    //     echo "Successfully uploaded";
                    // } else {
                    //     echo "Not uploaded because of error #" . $_FILES["file"]["error"];
                    //     exit();
                    // }

                    echo "<br>img_upload_path = " . $img_upload_path;

                    // Insert into DB
                    try {
                        $sql = "INSERT INTO employee(username,email,gender,image)
                VALUES('$username','$email','$gender','$new_img_name')";
                        $status = $conn->query($sql);
                        echo "<br>" . $sql;
                        if ($status) {
                            echo "<br>success";
                            header("Location: listing.php");
                        } else {
                            echo "<br>something went wrong";
                        }
                    } catch (Exception $ex) {
                        echo "<br>" . $ex->getMessage();
                    }
                } else {
                    // Unsupported file type
                    echo "Unsupported file type";
                }
            }
        } else {
            echo "<br> errors found";
        }
    } else if (false) {
        // Insert into DB
        try {
            $sql = "INSERT INTO employee(username,email,gender)
    VALUES('$username','$email','$gender')";
            $status = $conn->query($sql);
            echo "<br>" . $sql;
            if ($status) {
                echo "<br>success";
                header("Location: listing.php");
            } else {
                echo "<br>something went wrong";
            }
        } catch (Exception $ex) {
            echo "<br>" . $ex->getMessage();
        }
    }
}
function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
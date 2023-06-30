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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    <!-- font-awesome -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

    <!-- datepicker styles -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker3.min.css">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>



    <title>Add new user</title>
    <style>
    .action-btn {
        font-size: 1em;
    }

    .form-group.required .control-label:after {
        content: " *";
        color: red;
    }

    .custom-menu {
        width: 200px;
        max-height: 250px;
        overflow-x: hidden;
        overflow-y: auto;
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
        $country = $_POST['country'];
        $state = $_POST['state'];
        $city = $_POST['city'];
        $joining_date = $_POST['date'];

        $address = $city . ',' . $state . ',' . $country;


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
                        $sql = "INSERT INTO interns(username,email,gender,image, mentor, Address, joining_date)VALUES('$testuser','$email','$gender','$filename', '$mentor', '$address','$joining_date')";
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
                    $sql = "INSERT INTO interns(username,email,gender,mentor, Address, joining_date)VALUES('$testuser','$email','$gender','$mentor','$address','$joining_date')";
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

        <form method="POST" enctype="multipart/form-data"
            action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

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
                <div class="row">
                    <legend class="control-label col-form-label col-sm-2 pt-0">Gender</legend>
                    <div class="col-sm-10">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="gender" id="gridRadios1" value="male"
                                <?php if (isset($_POST['gender']) && $_POST['gender'] == "male") {
                                                                                                                            echo "checked";
                                                                                                                        }
                                                                                                                        ?>>
                            <label class="form-check-label" for="gridRadios1">
                                Male
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="gender" id="gridRadios2" value="female"
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
            <div class="form-group row align-items-center">
                <div class="input-group mb-3 align-items-center">
                    <legend class="control-label col-form-label col-sm-2 pt-0">Image</legend>
                    <div class="col-sm-10">

                        <input accept="image/*" id="filesToUpload" class="col-sm-8 pl-0"
                            aria-describedby="inputGroupFileAddon01" name='files' type="file">
                        <!-- show image-preview before upload -->
                        <output id="filesInfo"></output>
                        <!-- <label class="custom-file-label" for="inputGroupFile01">Choose file</label> -->

                    </div>
                </div>
            </div>

            <div class="form-group row align-items-center">
                <div class="input-group mb-3 align-items-center">
                    <legend class="control-label col-form-label col-sm-2 pt-0">Address</legend>

                    <div class="col-sm-10">
                        <div class="row my-4">
                            <div class="col-sm-4">
                                <div class="btn-group dropright">
                                    <button class="btn btn-info dropdown-toggle selected-country" type="button"
                                        id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        Select Country
                                    </button>
                                    <input id="selectedCountry" type="hidden" name="country" value="" />
                                    <div class="dropdown-menu custom-menu menu-countries"
                                        aria-labelledby="dropdownMenuButton">
                                        <?php
                                        include "db_conn.php";
                                        $sql = "SELECT id, name FROM countries ORDER BY name ASC";
                                        $result = $conn->query($sql);
                                        if ($result->num_rows > 0) {
                                            while ($country = $result->fetch_assoc()) {
                                                $country_id = $country['id'];
                                        ?>
                                        <option value="<?php echo $country_id ?>" class="dropdown-item country-item">
                                            <?php echo $country['name'] ?>
                                        </option>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="btn-group dropright">
                                    <button class="btn btn-info dropdown-toggle selected-state disabled" type="button"
                                        id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        Select State
                                    </button>
                                    <input id="selectedState" type="hidden" name="state" value="" />

                                    <div class="dropdown-menu custom-menu menu-states"
                                        aria-labelledby="dropdownMenuButton">
                                        <option class="dropdown-item state-tem"></option>

                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="btn-group">
                                    <button class="btn btn-info dropdown-toggle selected-city disabled" type="button"
                                        id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        Select City
                                    </button>
                                    <input id="selectedCity" type="hidden" name="city" value="" />

                                    <div class="dropdown-menu custom-menu menu-cities"
                                        aria-labelledby="dropdownMenuButton">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- <button type="button" class="btn btn-outline-success submit">Success</button> -->

                    </div>
                </div>
            </div>

            <!-- datepicker here -->
            <!-- Date Picker -->
            <div class="form-group mb-4 row align-items-center">
                <legend class="control-label col-form-label col-sm-2 pt-0">Joining Date</legend>
                <div class="col-sm-10">

                    <div class="datepicker date input-group">
                        <input type="text" name="date" value="" placeholder="Choose Date" class="form-control"
                            id="fecha1">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                        </div>
                    </div>
                </div>

            </div>
            <!-- // Date Picker -->

            <div class="form-group row">
                <div class="col-sm-10 text-center">
                    <button type="submit" name="add-intern" class="btn btn-primary" style="width: 200px;">
                        Add
                    </button>
                </div>
            </div>
        </form>

    </div>


    <!-- <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous">
    </script> -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
        integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous">
    </script>
    <!-- Datepicker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js">
    </script>
    <script>
    $(function() {
        $('.datepicker').datepicker({
            language: "es",
            autoclose: true,
            format: "yyyy-mm-dd",
            startDate: '-1m'
        });
    });
    $('form').submit(function(event) {
        if (true) {

            event.preventDefault();

            // grab all reqired fields and do client side validation
            var $name = $("input[name='username']")
            var $nameErrorSpan = $name.siblings('span')
            var hasNumber = /\d/;

            console.log('name=', $name.val())

            var n = $name.val()
            if (n.trim().length === 0) {
                $nameErrorSpan.text('Username is required input');
            } else if (hasNumber.test(n)) {
                $nameErrorSpan.text('Username con have only alphabets');
            } else {
                $nameErrorSpan.text('')
            }

            var $email = $("input[name='email']")
            var $emailErrorSpan = $email.siblings('span')

            console.log('email=', $email.val())


            function validateEmail(email) {
                var regex = /^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/;
                return regex.test(email);
            }

            if (validateEmail($email.val())) {
                $emailErrorSpan.text('')

            } else {
                $emailErrorSpan.text('Please enter valid email address');
            }


            var $gender = $("input[name='gender']").val()

        } else {
            console.log('post method called')
        }
    });
    // select an country on click
    $('option.country-item').click(function() {
        console.log('clicked country item');
        var $menuItem = $(this);

        // remove previous selected class
        $('option.country-item.active').removeClass('active');

        // remove all prevoius states,cities in dropdown
        $('.menu-states').empty();
        $('.menu-cities').empty();

        // disable state and cities menu
        $('.selected-state').addClass('disabled');
        $('.selected-city').addClass('disabled');

        // reset to default for selected city and state
        $('button.selected-state').text('Select State')
        $('button.selected-city').text('Select City')

        // make selected country active
        $menuItem.addClass('active');
        // set selected country as dropdown button text
        $('button.selected-country').text($menuItem.text())
        $('#selectedCountry').val(($menuItem.text()).trim());
        console.log('clicked ', $menuItem.text(), ' id= ', $menuItem.val())

        // make post request for country id
        $.ajax({
            url: "world.php",
            type: "POST",
            data: {
                table: 'state',
                id: $menuItem.val()
            },
            success: function(data) {
                // populate states in dropdown list
                // console.log(data)
                // remove disabled option from state dropdown
                $('.selected-state').removeClass('disabled');
                $(data).each(function(idx, state) {
                    var state_option = "<option value='" + state['id'] +
                        "'class='dropdown-item state-item'>" + state['name'] +
                        "</option>";
                    $('.menu-states').append(state_option)

                    // console.log(state_option)
                })

                // make states dropdown button enabled

            },
            error: function(xhr, status, error) {
                // there was an error
                const errors = xhr.responseJSON;
                console.log(errors)
            }
        });
    });
    // select an state on click
    $(document).on('click', 'option.state-item', function() {
        console.log('clicked state item');
        var $menuItemState = $(this);

        // remove previous selected class
        $('option.state-item.active').removeClass('active');

        // remove all prevoius cities in dropdown
        $('.menu-cities').empty();

        // reset to default for selected city 
        $('button.selected-city').text('Select City')

        // make selected country active
        $menuItemState.addClass('active');

        // disable state and cities menu
        $('.selected-city').addClass('disabled');

        // set selected country as dropdown button text
        $('button.selected-state').text($menuItemState.text())
        $('#selectedState').val(($menuItemState.text()))
        console.log('clicked ', $menuItemState.text(), ' id= ', $menuItemState.val())

        // make post request for country id
        $.ajax({
            url: "world.php",
            type: "POST",
            data: {
                table: 'city',
                id: $menuItemState.val()
            },
            success: function(data) {
                // populate cities in dropdown list
                // remove disabled option from city dropdown
                $('.selected-city').removeClass('disabled');
                $(data).each(function(idx, city) {
                    var city_option = "<option value='" + city['id'] +
                        "'class='dropdown-item city-item'>" + city['name'] +
                        "</option>";
                    $('.menu-cities').append(city_option)

                    // console.log(state_option)
                })

                // make states dropdown button enabled

            },
            error: function(xhr, status, error) {
                // there was an error
                const errors = xhr.responseJSON;
                console.log(errors)
            }
        });
    });
    // select an city on click
    $(document).on('click', 'option.city-item', function() {
        console.log('clicked city item');
        var $menuItemCity = $(this);

        // remove previous selected class
        $('option.city-item.active').removeClass('active');

        // make selected country active
        $menuItemCity.addClass('active');
        // set selected country as dropdown button text
        $('button.selected-city').text($menuItemCity.text())
        $('#selectedCity').val(($menuItemCity.text()))
        console.log('clicked ', $menuItemCity.text(), ' id= ', $menuItemCity.val())

    });

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
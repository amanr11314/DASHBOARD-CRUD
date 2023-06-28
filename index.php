<?php
// Start the session
//TODO:: WORK ON PAGINATION https://codepen.io/paulobrien/pen/LBrMxa
session_start();
if (empty($_COOKIE['login']) || $_COOKIE['login'] == '') {
    header("Location: login.php");
    die();
}
include "db_conn.php";
// Sorting column and order
$sortColumn = $_GET['sortColumn'] ?? "id"; // Replace with the actual column name you want to sort
$limit = $_GET['limit'] ?? 5;
$offset = $_GET['page'] ?? 1;
$currentPage = $_GET['page'] ?? 1;
function getSortOrder()
{
    if (isset($_GET['sortOrder']) && !empty($_GET['sortOrder'])) {
        return $_GET['sortOrder'];
    }
    return -1;
}
function getOrder($order)
{
    if ($order > 0) {
        return 'DESC';
    }

    return 'ASC';
}
if ($_GET['action'] === 'delete' && isset($_GET['id'])) {
    try {
        // Prepare the update query
        // $stmt = $conn->prepare("DELETE FROM employee WHERE id = ?");
        $stmt = $conn->prepare("DELETE FROM interns WHERE id = ? and mentor = ?");

        /* BK: always check whether the prepare() succeeded */
        if ($stmt === false) {
            trigger_error($this->mysqli->error, E_USER_ERROR);
            exit();
        }
        $stmt->bind_param("ss", $_GET['id'], $_COOKIE['login']);

        $status = $stmt->execute();
        if ($status) {
            // delete self account which is logged in
            if ($_GET['id'] === $_COOKIE['login']) {
                header("Location: logout.php");
                die();
            }
            // printf("%d Row inserted.\n", $stmt->affected_rows);

            printf("%s", mysqli_error($conn));
        } else {
            trigger_error($stmt->error, E_USER_ERROR);
        }
    } catch (Exception $ex) {
        $toastMsg = $e->getMessage();
        echo $toastMsg;
    }
    header("Location: index.php");
    exit();
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
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>


    <title>Dashboard</title>
    <style>
    .action-btn {
        font-size: 1em;
    }

    .my-toast {
        position: fixed;
        z-index: 2;
        right: 1rem;
        top: 1rem;
        width: fit-content;
    }

    .custom-footer-fixed-bottom {
        position: absolute;
        bottom: 0;
        width: 100%;
    }

    .my-custom-scrollbar {
        position: relative;
        height: 500px;
        overflow: auto;
    }

    .table-wrapper-scroll-y {
        display: block;
    }
    </style>


</head>

<body class="bg-dark">
    <?php if ($_COOKIE['signedin'] == 'OK') {

    // show generic toast //
    setcookie('signedin', '', time() - 60, '/');
    ?>
    <div class="my-toast">
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Login Success
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
    <?php } else if ($_GET['status'] == 'change-password-success') {

    // show generic toast on password change success //
    ?>
    <div class="my-toast">
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            "Congratulations! Your password has been changed.";
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
    <?php }?>
    <header>
        <div class="content-wrapper">
            <div>
                <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                    <div class="container-fluid">
                        <!-- Navbar brand/logo -->
                        <a class="navbar-brand" href="#">Dashboard</a>



                        <!-- Search bar -->
                        <form method="POST" name="search-form"
                            action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="d-flex ms-auto">
                            <input class="form-control mx-2" name="query" value="<?php echo $_POST['query']; ?>"
                                type="search" placeholder="Search" aria-label="Search">
                            <button class="btn btn-outline-primary" name="search" type="submit">Search</button>
                        </form>
                        <!-- Add New Subuser -->
                        <a href="add.php" class="mr-3 btn btn-primary action-btn">Add</a>

                        <div class="dropdown mr-4 ">
                            <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">


                                <?php if (!empty($_COOKIE['image'])) {?>
                                <img src="<?php echo "./uploads/" . $_COOKIE['image'] ?>" class="rounded-circle"
                                    alt="Avatar" width="40" height="40">

                                <?php } else {?>
                                <img src="./uploads/default_user.jpg" class="rounded-circle" alt="Avatar" width="40"
                                    height="40">
                                <?php }?>
                            </a>



                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink">
                                <a class="dropdown-item" href="profile.php">Profile</a>
                                <!-- <a class="dropdown-item" href="change_old_password.php">Change Password</a> -->
                                <a href="" class="dropdown-item" data-toggle="modal"
                                    data-target="#modalChangePassword">Change Password</a>
                                <a class="dropdown-item" href="logout.php">Logout</a>
                                <a class="dropdown-item" href="logout.php">Crop Image</a>
                            </div>
                        </div>


                    </div>
                </nav>
            </div>
        </div>
    </header>

    <div class="table-wrapper-scroll-y my-custom-scrollbar">

        <table class="table table-striped table-dark">
            <thead>
                <tr>
                    <th class="text-center" scope="col">
                        <a class="mr-3 badge badge-secondary action-btn"
                            href="<?php echo "index.php?sortColumn=id&sortOrder=" . getSortOrder() * -1; ?>">S.N</a>
                    </th>
                    <th class="text-center" scope="col">
                        <a class="mr-3 badge badge-secondary action-btn"
                            href="<?php echo "index.php?sortColumn=username&sortOrder=" . getSortOrder() * -1; ?>">Name</a>
                    </th>
                    <th class="text-center" scope="col"><a class="mr-3 badge badge-secondary action-btn"
                            href="<?php echo "index.php?sortColumn=email&sortOrder=" . getSortOrder() * -1; ?>">Email</a>
                    </th>
                    <th class="text-center" scope="col"><a class="mr-3 badge badge-secondary action-btn"
                            href="<?php echo "index.php?sortColumn=gender&sortOrder=" . getSortOrder() * -1; ?>">Gender</a>
                    </th>
                    <th class="text-center" scope="col">Image</th>
                    <th class="text-center" scope="col" colspan="2">Action</th>
                </tr>
            </thead>

            <tbody>
                <?php

// Display employees in table
$sql = "SELECT * FROM interns WHERE";
if (isset($_POST["search"])) {
    $query = $_POST['query'];
    $query = trim($query);
    $query = stripslashes($query);
    $query = htmlspecialchars($query);
    $sql = $sql . " concat( `username`, `email` ) LIKE '%$query%'";
    $sql = $sql . " AND mentor=" . $_COOKIE['login'];
} else {
    $sql = $sql . " mentor=" . $_COOKIE['login'];
}
$offset = ($offset - 1) * $limit;
$sql = $sql . " ORDER BY $sortColumn " . getOrder(getSortOrder());
$sql = $sql . " LIMIT " . $offset . ',' . $limit;
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($data = $result->fetch_assoc()) {
        ?>
                <tr>
                    <td class="text-center" scope="row"> <?php echo $data['id']; ?></td>
                    <td class="text-center"><?php echo $data['username']; ?> </td>
                    <td class="text-center"><?php echo $data['email']; ?> </td>
                    <td class="text-center"><?php echo ucfirst($data['gender']); ?> </td>
                    <td class="text-center">
                        <?php if (!empty($data['image'])) {?>
                        <button type="button" class="" data-toggle="modal"
                            data-target="#myPreviewModal<?=$data['id']?>">
                            <img src="<?php echo "./thumbnails/" . $data['image'] ?>" alt="Image"
                                style="width: 40px; height: 30px;"></button>
                        <?php } else {?>
                        -
                        <?php }?>
                    </td>
                    <td class="text-center">
                        <div class="btn-group" role="group" aria-label="Basic example">
                            <a href="edit.php?id=<?php echo $data['id']; ?>"
                                class="mr-3 btn btn-primary action-btn">Edit</a>
                            <button type="button" class="btn btn-danger" data-toggle="modal"
                                data-target="#myModal<?=$data['id']?>">Delete</button>
                        </div>
                    </td>
                </tr>
                <!-- Delete Confirmation Modal -->
                <div class="modal fade" id="myModal<?=$data['id']?>" tabindex="-1" role="dialog"
                    aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Delete User</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                                <a href="<?php echo "index.php?action=delete&id=" . $data['id']; ?>"
                                    class="btn btn-danger">Delete</a>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- Image Preview Modal -->
                <div class="modal fade" id="myPreviewModal<?=$data['id']?>" tabindex="-1" role="dialog"
                    aria-labelledby="myPreviewModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Image Preview User</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="card" style="width: 24rem;">
                                    <img class="card-img-top" src="<?php echo "./uploads/" . $data['image'] ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Change Password Modal -->
                <?php $modalChangePasswordError = json_decode($_COOKIE['change_old_password'], true);?>
                <div class="modal fade" id="modalChangePassword" tabindex="-1" role="dialog"
                    aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header text-center">
                                <h4 class="modal-title w-100 font-weight-bold">Change Password</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body mx-3">
                                <form method="POST" action="change_password2.php">
                                    <label for="id" class="text-danger col-form-label">* Required field</label>
                                    <input hidden type="text" name="id" value=<?php echo $_COOKIE['login']; ?>>

                                    <div class="form-group required row">
                                        <label for="inputOldPassword" class="control-label col-sm-4 col-form-label">Old
                                            Password</label>
                                        <div class="col-sm-6">
                                            <input type="password" class="form-control" id="inputOldPassword"
                                                name="old_password" placeholder="Old Password"
                                                value="<?php echo $_POST['old_password'] ?>">
                                            <span id="changeOldPasswordError"
                                                class="text-danger"><?php echo $modalChangePasswordError['old_password']; ?></span>
                                        </div>
                                    </div>
                                    <div class="form-group required row">
                                        <label for="inputPassword1"
                                            class="control-label col-sm-4 col-form-label">Password</label>
                                        <div class="col-sm-6">
                                            <input type="password" class="form-control" id="inputPassword1"
                                                name="password1" placeholder="Password"
                                                value="<?php echo $_POST['password1'] ?>">
                                            <span id="changePassword1Error"
                                                class="text-danger"><?php echo $modalChangePasswordError['password']; ?></span>
                                        </div>
                                    </div>
                                    <div class="form-group required row">
                                        <label for="inputPassword2"
                                            class="control-label col-sm-4 col-form-label">Confirm
                                            Password</label>
                                        <div class="col-sm-6">
                                            <input type="password" class="form-control" id="inputPassword2"
                                                name="password2" placeholder="Confirm Password"
                                                value="<?php echo $_POST['password2'] ?>">
                                            <span id="changePassword2Error"
                                                class="text-danger"><?php echo $modalChangePasswordError['password']; ?></span>
                                        </div>
                                    </div>


                                    <div class="d-flex flex-row justify-content-center">
                                        <div class="col-sm-10 text-center">

                                            <button type="submit" name="submit" class="btn btn-primary">
                                                Change Password
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <?php
}
    ?>
            </tbody>

        </table>
    </div>
    <!-- <footer class="navbar-fixed-bottom mt-auto py-3 bg-dark"> -->
    <footer class="custom-footer-fixed-bottom py-3 bg-dark">
        <div class="d-flex flex-row justify-content-end mr-4">
            <!-- Default dropup button -->
            <div class="dropup">
                <a role="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">
                    <?php echo 'Row Per Page: ' . $limit; ?>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="<?php if ($limit == 3) {
        echo 'dropdown-item active';
    } else {
        echo 'dropdown-item';
    }?>" href="index.php?limit=3">3</a>
                    <a class="<?php if ($limit == 5) {
        echo 'dropdown-item active';
    } else {
        echo 'dropdown-item';
    }?>" href="
                        index.php?limit=5">5</a>
                    <a class="<?php if ($limit == 10) {
        echo 'dropdown-item active';
    } else {
        echo 'dropdown-item';
    }?>" href="
                        index.php?limit=10">10</a>
                </div>
            </div>
            <nav aria-label="Pagination" class="mx-4">
                <ul class="pagination pagination-dark">
                    <?php
$sql2 = "SELECT COUNT(*) as total FROM interns WHERE mentor=" . $_COOKIE['login'];
    $result2 = $conn->query($sql2);
    if ($result2->num_rows > 0) {
        $row_ = $result2->fetch_assoc();
        $total = ($row_['total']);
        // print
    }
    $totalRows = intval($total);
    $totalPages = intval($totalRows / $limit);
    $previousPage = intval($currentPage) - 1;
    $nextPage = intval($currentPage) + 1;
    $end_ = $limit * intval($currentPage);
    $start_ = $end_ - $limit + 1;
    $isNextPage = $end_ < $totalRows;
    if ($end_ > $totalRows) {
        $end_ = $totalRows;
    }

    if ($previousPage > 0) {?>
                    <li class="page-item">
                        <?php } else {?>
                    <li class="page-item disabled">
                        <?php }?>
                        <a class="page-link" href="<?php echo 'index.php?limit=' . $limit . '&page=' . $previousPage ?>"
                            aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                            <span class="sr-only">Previous</span>
                        </a>
                    </li>
                    <li class="page-item"><a class="page-link" href="#">
                            <?php echo $start_ . ' - ' . $end_ . ' of ' . $totalRows ?>
                        </a></li>
                    <?php if ($isNextPage) {?>
                    <li class="page-item">
                        <?php } else {?>
                    <li class="page-item disabled">
                        <?php }?>
                        <a class="page-link" href="<?php echo 'index.php?limit=' . $limit . '&page=' . $nextPage ?>"
                            aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                            <span class="sr-only">Next</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </footer>
    <?php
} else {
    echo "0 results";
}
?>
    <!-- <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
    </script>
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"> -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
        integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous">
    </script>
    <script>
    $(document).ready(function() {
        // for auto toast close
        setTimeout(function() {
            toast = document.querySelector('.close');
            toast.click();
        }, 2500)

        // for modal change password
        $('.modal-body form').submit(function(event) {
            // to stop the form from submitting (and the page reloading, so the modal will not close)
            event.preventDefault();

            $.ajax({
                url: "change_password2.php",
                type: "POST",
                data: $(this).serialize(),
                success: function(data) {
                    // enable toast on homepage for successful password change
                    document.location = 'index.php?status=change-password-success';

                },
                error: function(xhr, status, error) {
                    // there was an error
                    const errors = xhr.responseJSON;
                    const old_password_error = errors['old_password'];
                    const new_password_error = errors['password'];
                    if (!!old_password_error) {
                        $('#changeOldPasswordError').text(old_password_error);
                    } else {
                        $('#changeOldPasswordError').text('');
                    }
                    if (!!new_password_error) {
                        $('#changePassword1Error').text(new_password_error);
                        $('#changePassword2Error').text(new_password_error);
                    } else {
                        $('#changePassword1Error').text('');
                        $('#changePassword2Error').text('');
                    }
                }
            });
        });
    });
    </script>

</body>

</html>
<?php
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
    <title>Document</title>
    <style>
    .action-btn {
        font-size: 1em;
    }
    </style>
    <script>
    function confirmDelete() {
        document.getElementById("deleteDataForm").submit();
    }
    </script>
</head>

<body>
    <?php
    include "db_conn.php";
    // populate values from db
    if (isset($_GET["id"])) {

        $sql = "SELECT id, username, email, gender FROM employee WHERE id=" . $_GET['id'];
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $id = $row['id'];
            $_POST["id"] = $row['id'];
            $_POST["username"] = $row['username'];
            $_POST["email"] = $row['email'];
            $_POST["gender"] = $row['gender'];
        }
    }

    // if (isset($_POST["delete"])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // The request is using the POST method
        $id = $_POST["id"];
        $email = $_POST["email"];
        $username = $_POST["username"];
        $gender = $_POST["gender"];

        try {
            // Prepare the update query
            $stmt = $conn->prepare("DELETE FROM employee WHERE id = ?");

            /* BK: always check whether the prepare() succeeded */
            if ($stmt === false) {
                trigger_error($this->mysqli->error, E_USER_ERROR);
                exit();
            }
            $stmt->bind_param("s", $id);

            $status = $stmt->execute();
            if ($status) {
                // printf("%d Row inserted.\n", $stmt->affected_rows);

                printf("%s", mysqli_error($conn));
            } else {
                trigger_error($stmt->error, E_USER_ERROR);
            }
        } catch (Exception $ex) {
            $toastMsg = $e->getMessage();
            echo $toastMsg;
        }
        header("Location: listing.php");
        exit(); // Ensure that no further code is executed after the redirect

    }
    ?>
    <div class="container mt-4">
        <form method="POST" action="" id="deleteDataForm">

            <input hidden type=" text" name="id" value=<?php echo $_POST['id']; ?>>
            <div class="form-group row">
                <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                <div class="col-sm-10">
                    <input type="text" disabled class="form-control" id="inputName" name="username"
                        placeholder="User name" value="<?php echo $_POST['username'] ?>">
                </div>
            </div>
            <div class="form-group row">
                <label for="inputEmail3" class="col-sm-2 col-form-label">Email</label>
                <div class="col-sm-10">
                    <input disabled type="email" class="form-control" id="inputEmail3" name="email" placeholder="Email"
                        value="<?php echo $_POST['email'] ?>">
                </div>
            </div>
            <fieldset disabled class="form-group">
                <div class="row">
                    <legend class="col-form-label col-sm-2 pt-0">Gender</legend>
                    <div class="col-sm-10">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="gender" id="gridRadios1" value="male"
                                <?php if (isset($_POST['gender']) && $_POST['gender'] == "male") echo "checked"; ?>>
                            <label class="form-check-label" for="gridRadios1">
                                Male
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="gender" id="gridRadios2" value="female"
                                <?php if (isset($_POST['gender']) && $_POST['gender'] == "female") echo "checked"; ?>>
                            <label class="form-check-label" for="gridRadios2">
                                Female
                            </label>
                        </div>
                    </div>
                </div>
            </fieldset>
            <div class="form-group row">
                <div class="col-sm-10">
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#exampleModal">
                        Delete
                    </button>
                    <!-- <button type="submit" name="delete" class="btn btn-danger" style="width: 100px;">
                        Delete
                    </button> -->
                </div>
            </div>
        </form>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Are you sure?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">No</button>
                        <button onclick="confirmDelete()" type="button" class="btn btn-danger">Yes</button>
                    </div>
                </div>
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

</body>

</html>
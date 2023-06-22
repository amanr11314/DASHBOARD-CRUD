<?php
include "db_conn.php";
// Sorting column and order
$sortColumn = $_GET['sortColumn'] ?? "id"; // Replace with the actual column name you want to sort
function getSortOrder()
{
    if (isset($_GET['sortOrder']) && !empty($_GET['sortOrder'])) {
        return $_GET['sortOrder'];
    }
    return -1;
}
function getOrder($order)
{
    if ($order > 0) return 'DESC';
    return 'ASC';
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

    <title>Document</title>
    <style>
    .action-btn {
        font-size: 1em;
    }
    </style>
</head>

<body>

    <div>
        <div class="px-4 my-4 d-flex justify-content-center btn-group" role="group" aria-label="Basic example">
            <a href="add.php" class="mr-3 badge badge-dark action-btn">Add</a>
        </div>


        <table class="table table-striped table-dark">
            <thead>
                <tr>
                    <!-- <th class="text-center" scope="col">S.N</th> -->
                    <th class="text-center" scope="col">
                        <a class="mr-3 badge badge-secondary action-btn"
                            href="<?php echo "listing.php?sortColumn=id&sortOrder=" . getSortOrder() * -1; ?>">S.N</a>
                    </th>
                    <!-- <th class="text-center" scope="col">Name</th> -->
                    <th class="text-center" scope="col">
                        <a class="mr-3 badge badge-secondary action-btn"
                            href="<?php echo "listing.php?sortColumn=username&sortOrder=" . getSortOrder() * -1; ?>">Name</a>
                    </th>
                    <!-- <th class="text-center" scope="col">Email</th> -->
                    <th class="text-center" scope="col"><a class="mr-3 badge badge-secondary action-btn"
                            href="<?php echo "listing.php?sortColumn=email&sortOrder=" . getSortOrder() * -1; ?>">Email</a>
                    </th>
                    <!-- <th class="text-center" scope="col">Gender</th> -->
                    <th class="text-center" scope="col"><a class="mr-3 badge badge-secondary action-btn"
                            href="<?php echo "listing.php?sortColumn=gender&sortOrder=" . getSortOrder() * -1; ?>">Gender</a>
                    </th>
                    <th class="text-center" scope="col">Image</th>
                    <th class="text-center" scope="col" colspan="2">Action</th>
                </tr>
            </thead>

            <?php


            // Display employees in table 
            // $sql = "SELECT id,username,email, gender, image FROM employee";
            // $result = $conn->query($sql);
            $sql = "SELECT * FROM employee ORDER BY $sortColumn " . getOrder(getSortOrder());
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($data = $result->fetch_assoc()) {
            ?>
            <tbody>
                <tr>
                    <td class="text-center" scope="row"> <?php echo $data['id']; ?></td>
                    <td class="text-center"><?php echo $data['username']; ?> </td>
                    <td class="text-center"><?php echo $data['email']; ?> </td>
                    <td class="text-center"><?php echo ucfirst($data['gender']); ?> </td>
                    <td class="text-center">
                        <?php if (!empty($data['image'])) { ?>
                        <img src="<?php echo "./uploads/" . $data['image'] ?>" alt="Image"
                            style="width: 100px; height: 100px;">
                        <?php } else { ?>
                        -
                        <?php } ?>
                    </td>
                    <td class="text-center">
                        <div class="btn-group" role="group" aria-label="Basic example">
                            <a href="edit.php?id=<?php echo $data['id']; ?>"
                                class="mr-3 badge badge-primary action-btn">Edit</a>
                            <a href="delete.php?id=<?php echo $data['id']; ?>"
                                class="mr-3 badge badge-danger action-btn">Delete</a>
                        </div>
                    </td>
                </tr>
                <?php
                }
                    ?>
            </tbody>

        </table>
        <?php
            } else {
                echo "0 results";
            }
    ?>
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
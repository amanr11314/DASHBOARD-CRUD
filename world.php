<?php
header('Content-Type: application/json; charset=utf-8');
include "db_conn.php";
$success = true;
$errors = array();


$table = $_POST['table'];
$id = $_POST['id'];

$sql = '';
if ($table == 'state') {
    $sql = "SELECT id,name FROM states WHERE country_id=" . $id . " ORDER BY name ASC";
} else if ($table == 'city') {
    $sql = "SELECT id,name FROM cities WHERE state_id=" . $id . " ORDER BY name ASC";
}

$result = $conn->query($sql);
$data = array();
if ($result->num_rows > 0) {
    while ($node = $result->fetch_assoc()) {
        $data[] = $node;
    }
    http_response_code(200);
    echo json_encode($data);
} else {
    http_response_code(404);
    $errors['message'] = 'No States Found';
    echo json_encode($errors);
}


// if (empty($errors)) {
//     // set the status code to 200 to indicate success
//     http_response_code(200);

//     // return a JSON object with a message property
//     echo json_encode(array("message" => "Your password was channged successfully"));
// } else {
//     // set the status code to 400 to indicate an error
//     http_response_code(400);

//     // return a JSON object with a message property
//     echo json_encode($errors);
//     // echo json_encode(array("message" => "There was an error processing the request"));

// }
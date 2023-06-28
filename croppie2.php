<?php
header('Content-Type: application/json; charset=utf-8');
$image = $_POST['image'];

list($type, $image) = explode(';', $image);
list(, $image) = explode(',', $image);

$image = base64_decode($image);
$image_name = 'from-modal-' . time() . '.png';
if (file_put_contents('uploads/' . $image_name, $image)) {

// echo 'successfully uploaded= ' . $image_name;

    // set the status code to 200 to indicate success
    http_response_code(200);
    $response = array();
    $response['message'] = "image cropped successfully";
    $response['image_name'] = $image_name;

    // return a JSON object with a message property
    echo json_encode($response);
} else {
    // set the status code to 400 to indicate an error
    http_response_code(400);
    $errors = array();
    $errors = 'falied to upload something went wrong';
    if (empty($_POST['image']) || (!isset($_POST['image']))) {
        $errors['img'] = 'not received cropped image';
    }

    // return a JSON object with a message property
    echo json_encode($errors);
    // echo json_encode(array("message" => "There was an error processing the request"));

}
<?php
header('Content-Type: application/json; charset=utf-8');

$file_extension = $_POST['extension'];

// process cropped image
$image = $_POST['image'];

list($type, $image) = explode(';', $image);
list(, $image) = explode(',', $image);

$image = base64_decode($image);
// $image_name = time() . '.png';
$image_name = 'cropped' . time() . '.png';
file_put_contents('uploads/' . $image_name, $image);

// process original image
$original_image = $_POST['file'];

list($type, $original_image) = explode(';', $original_image);
list(, $original_image) = explode(',', $original_image);

$original_image = base64_decode($original_image);
$original_image_name = 'now' . time() . '.png';
// $original_image_name = 'now' . time() . '.' . $file_extension;
file_put_contents('uploads/' . $original_image_name, $original_image);

$resp = array();
$resp['file_extension'] = $file_extension;
$resp['original_image_name'] = $original_image_name;
$resp['image_name'] = $image_name;

http_response_code(200);
echo json_encode($resp);
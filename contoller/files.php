<?php 
ini_set('display_errors',1);
header("Access-Control-Allow-Origin: *");
$jwt=getallheaders()['Authorization'];
function decodeJWT($jwt) {
    $jwt = str_replace('Bearer ', '', $jwt);
    $jwtParts = explode('.', $jwt);
    
    if (count($jwtParts) !== 3) {
        return false; 
    }
    
    $decodedPayload = base64_decode($jwtParts[1]);
    
    $payloadData = json_decode($decodedPayload, true);
    
    return $payloadData;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $jwt) {
    $payload=decodeJWT($jwt);
      
    header('Content-Type: application/json'); 
    $target_file = '/var/www/html/apps/views/uploads/'.$_FILES["file"]["name"];
    $type=$_FILES['file']['type'];
    
    $allowed_file_types = ['jpg', 'jpeg', 'png'];
    $allowed_mime_types=['image/png','image/jpg','image/jpeg'];
    $imageFileType = explode(".",$_FILES['file']['name']);
    if (!in_array($imageFileType[1], $allowed_file_types) || !in_array($type,$allowed_mime_types)) {
        echo json_encode(['message' => "Sorry, only JPG, JPEG, PNG files are alloweasdasdd."]);
        exit;
    }
    else if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
        
        setcookie('photo',base64_encode($_FILES["file"]["name"]),0,"/");
        echo json_encode(['message' =>'File '.$_FILES["file"]["name"].' successfully loaded']);
        
    }else {
        echo json_encode(['message' =>'File not uploaded']); 
    }
      
    
} else {
    // POST isteği yok
    echo json_encode(['error' => 'Token or Method error']);
}

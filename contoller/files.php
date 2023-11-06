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
    $target_file = '/var/www/html/apps/public/uploads/'.$_FILES["file"]["name"];
    $imageFileType = explode('.',$_FILES["file"]["name"]);
    if ($imageFileType[1] != "jpg" && $imageFileType[1] != "png" && $imageFileType[1] != "jpeg") {
        echo json_encode(['message'=>"Sorry, only JPG, JPEG, PNG files are allowed."]);
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
<?php
// header("Access-Control-Allow-Origin: *");
if (isset($_SERVER["HTTP_ORIGIN"])) {

    $allowedOrigins = [
        "http://localhost:3000",
        "http://192.168.1.155:3000"
    ];

    if (in_array($_SERVER["HTTP_ORIGIN"], $allowedOrigins)) {
        echo "?????";
        header("Access-Control-Allow-Origin: " . $_SERVER["HTTP_ORIGIN"]);
    }
    header("Access-Control-Allow-Methods: POST, GET, DELETE");
    header("Access-Control-Allow-Headers: origin, content-type, accept,userID, UserID");
    
}

echo json_encode(getallheaders());
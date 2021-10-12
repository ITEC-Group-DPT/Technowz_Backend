<?php
header("Access-Control-Allow-Origin: *");
echo "?";
if (isset($_SERVER["HTTP_ORIGIN"])) {
    echo "lz Cuong";
    // $allowedOrigins = [
    //     "http://localhost:3000/",
    //     "http://192.168.1.155:3000/",
    //     // ... etc
    // ];

    // if (in_array($_SERVER["HTTP_ORIGIN"], $allowedOrigins)) {
    //     header("Access-Control-Allow-Origin: " . $_SERVER["HTTP_ORIGIN"]);
    // }
    // header("Access-Control-Allow-Methods: POST, GET, DELETE");
    // header("Access-Control-Allow-Headers: origin, content-type, accept");
}

// echo "cho Tri";
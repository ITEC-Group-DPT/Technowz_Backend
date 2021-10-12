<?php
header("Access-Control-Allow-Origin: *");
if (isset($_SERVER["HTTP_ORIGIN"])) {
    $allowedOrigins = array(
        "http://localhost:3000/",
        "http://192.168.1.155:3000/"
    );

    foreach ($allowedOrigins as $origin) {
        echo $origin . " ";
        if (strcmp($origin,$_SERVER["HTTP_ORIGIN"])) {
            echo "?????";
            // header("Access-Control-Allow-Origin: " . $_SERVER["HTTP_ORIGIN"]);
            break;
        }
    }
    // if (in_array($_SERVER["HTTP_ORIGIN"], $allowedOrigins)) {
    //     echo "?????";
    //     // header("Access-Control-Allow-Origin: " . $_SERVER["HTTP_ORIGIN"]);
    // }
    // header("Access-Control-Allow-Methods: POST, GET, DELETE");
    // header("Access-Control-Allow-Headers: origin, content-type, accept");
}

// echo "cho Tri";
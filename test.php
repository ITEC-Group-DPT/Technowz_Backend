<?php
// header("Access-Control-Allow-Origin: *");
if (isset($_SERVER["HTTP_ORIGIN"])) {
    $allowedOrigins = array(
        "http://localhost:3000/",
        "http://192.168.1.155:3000/"
    );

    foreach ($allowedOrigins as $origin) {
        if ($origin == $_SERVER["HTTP_ORIGIN"]) {
            echo "1";
            header("Access-Control-Allow-Origin: " . $_SERVER["HTTP_ORIGIN"]);
            echo "2";
            break;
        }
    }
    // if (in_array($_SERVER["HTTP_ORIGIN"], $allowedOrigins)) {
    //     echo "?????";
    //     // header("Access-Control-Allow-Origin: " . $_SERVER["HTTP_ORIGIN"]);
    // }
    // header("Access-Control-Allow-Methods: POST, GET, DELETE");
    // header("Access-Control-Allow-Headers: origin, content-type, accept");
    var_dump($_SERVER["HTTP_ORIGIN"]);
}

// echo "cho Tri";
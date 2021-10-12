<?php
    include '../database/db.php';
    if (isset($_SERVER["HTTP_ORIGIN"])) {
        $allowedOrigins = [
            "http://localhost:3000",
            "http://192.168.1.155:3000",
            "http://localhost:8080"
        ];

        if (in_array($_SERVER["HTTP_ORIGIN"], $allowedOrigins)) {
            header("Access-Control-Allow-Origin: " . $_SERVER["HTTP_ORIGIN"]);
        }
        header("Access-Control-Allow-Methods: POST, GET, DELETE");
        header("Access-Control-Allow-Headers: origin, content-type, accept, Userid");
    }

    function errorAPI(){
        http_response_code(600);
		exit;
    }
?>
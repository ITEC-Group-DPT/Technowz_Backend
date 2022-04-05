<?php
    include '../api/apiheader.php';
    include '../classes/User.php';
    include '../classes/Order.php';

    $header = getallheaders();
    $user = new User($conn);


    if (isset($header['Userid'])) {

        $userID = $header['Userid'];
        $isAdmin = $user->verifyAdmin($userID);

        if ($isAdmin == false) failApi("Access-Control Denied");

        if (isset($_GET['command']) == null) failApi("Invalid request");

        $command = $_GET['command'];

        if ($command == "getOverallStatistic") {

            $orderData = Order::getTotalOrderData($conn);
            $userData = User::getTotalAccountNum($conn);

            $summaryData = $orderData + $userData;
            successApi($summaryData);
        }

    }

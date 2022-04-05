<?php

include '../api/apiheader.php';

include '../classes/Mock.php';
include '../classes/Statistic.php';


$mock = new Mock($conn);
$stat = new Statistic($conn);

$userArray = $mock->getAllUserID();
$productArray = $mock->getAllProductID();

if (!isset($_POST['command'])) failApi("Invalid request");

$command = $_POST['command'];

if ($command == "UserVisit") {
    $users = $userArray;

    array_push($users, -1);

    for ($i = 0; $i < 100; $i++) {

        $randomUser = $users[array_rand($users, 1)];

        $int = rand(1627750800, time());
        $time = date("Y-m-d H:i:s", $int);

        $stat->updateUserVisit($randomUser, $time);
    }

    successApi("Added user visit data successfully");
}
if ($command == "ProductView") {
    for ($i = 0; $i < 100; $i++) {

        $randomProduct = $productArray[array_rand($productArray, 1)];

        $int = rand(1627750800, time());
        $time = date("Y-m-d H:i:s", $int);

        $stat->updateProductView($randomProduct, $time);
    }

    successApi("Added product view data successfully");
}

if ($command == "CreateOrder") {

}
    // if ()

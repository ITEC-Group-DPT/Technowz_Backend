<?php

include '../api/apiheader.php';

include '../classes/User.php';
include '../classes/Order.php';
include '../classes/Product.php';



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

    for ($j = 0; $j < 50; $j++) {
        $productList = [];
        $totalPrice = 0;

        for ($i = 0; $i < 2; $i++) {
            $randomProduct = $productArray[array_rand($productArray, 1)];
            $randomQty = rand(1, 3);

            $curProduct = new Product($conn, $randomProduct);
            $productPrice = $curProduct->getProduct()['price'];

            $totalPrice += $productPrice * $randomQty;

            array_push($productList, [$randomProduct, $randomQty, $productPrice]);
        }

        $randomUserID = $userArray[array_rand($userArray, 1)];
        $user = new User($conn);
        $randomName = $user->getUser("userID", $randomUserID)['username'];

        $int = rand(1627750800, time());
        $time = date("Y-m-d H:i:s", $int);

        $order = new Order($conn);
        $order->createOrder(
            $randomUserID,
            $randomName,
            "No Address Provided",
            "01234xxxx",
            $totalPrice,
            $productList,
            $time,
        );
    }
    successApi("Added orders data successfully");
}
    // if ()

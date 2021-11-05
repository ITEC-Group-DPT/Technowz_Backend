<?php
    include './apiheader.php';
    include '../classes/Order.php';
    include '../classes/DeliveryInfo.php';
    include '../classes/Cart.php';
    include '../classes/Product.php';

    $header = getallheaders();
    if (isset($header['Userid'])){
        $userID = $header['Userid'];
        $order = new Order($conn);
        if (isset($_POST['command'])){
            if ($_POST['command'] == 'createOrder'){
                $name = $_POST['name'];
                $address = $_POST['address'];
                $phone = $_POST['phone'];
                $itemList = json_decode($_POST['list']);
                if ($order->createOrder($userID, $name, $address, $phone, $_POST['totalPrice'], $itemList)){
                    $deliInfo = new DeliveryInfo($conn, $userID);
                    if ($_POST['deliID'] == -1) 
                        $deliInfo->createDeliveryInfo($name, $address, $phone);
                    else $deliInfo->updateDeliveryInfo($_POST['deliID'], $name, $address, $phone);
                    successApi('Order created');
                }
                else failApi('Can not create order');
            }
            else if ($_POST['command'] == 'rateProduct'){
                $productID = $_POST['productID'];
                $rating = $_POST['rating'];
                if($order->rateProduct($_POST['orderID'], $productID, $rating)){
                    (new Product($conn, $productID))->updateProductRating($rating);
                    successApi("Rate successfully");
                }
                else failApi("Can not rate this product");
            }
            else failApi('No command found');
        }
        else if (isset($_GET['command'])){
            if ($_GET['command'] == 'getOrderDetail'){
                $arr = [];
                $arr['orderInfo'] = $order->getOrderInfo($_GET['orderID'], $userID);
                $arr['itemList'] =  $order->getItemList($_GET['orderID'], $userID);
                if ($arr['orderInfo'] != false)
                    successApi($arr);
                else failApi('No order detail found');
            }
            else if ($_GET['command'] == 'getOrderList'){
                $offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
                $limit = isset($_POST['limit']) ? $_POST['limit'] : 5;
                $data = $order->getOrderList($userID,$offset,$limit);
                successApi($data);
            }
            else failApi('No command found');
        }
        else failApi('No command found');
    }
    else failApi('No userID found');
?>
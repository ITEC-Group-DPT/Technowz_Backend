<?php 
    include './apiheader.php';
    include '../classes/Order.php';

    $header = getallheaders();
    if(isset($header['userid'])){
        $userID = $header['userid'];
        $order = new Order($conn);
        if(isset($_POST['command'])){
            if($_POST['command'] == 'createOrder'){
                $itemList = json_decode($_POST['list']);
                if($order->createOrder($userID, $_POST['name'], $_POST['address'], $_POST['phone'], $_POST['totalPrice'], $itemList))
                    successApi('Order created');
                else failApi('Can not create order');
            }
            else failApi('No command found');
        }
        else if(isset($_GET['command'])){
            if($_GET['command'] == 'getOrderDetail'){
                $arr = [];
                $arr['orderInfo'] = $order->getOrderInfo($_GET['orderID'], $userID);
                $arr['itemList'] =  $order->getItemList($_GET['orderID'], $userID);
                if($arr['orderInfo'] != false)
                    successApi($arr);
                else failApi('No order detail found');
            }
            else if($_GET['command'] == 'getOrderList'){
                $data = $order->getOrderList($userID);
                successApi($data);
            }
            else failApi('No command found');
        }
        else failApi('No command found');
    }
    else failApi('No userID found');
?>
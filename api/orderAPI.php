<?php 
    include './apiheader.php';
    include '../classes/Order.php';

    $header = getallheaders();
    $userID = $header['Userid'];
    
    if($userID == NULL) errorAPI();
    else{
        $order = new Order($conn, $userID);
        if(isset($_POST['command'])){
            if($_POST['command'] == 'createOrder'){
                $itemList = json_decode($_POST['list']);
                $order->createOrder($_POST['name'], $_POST['address'], $_POST['phone'], $_POST['totalPrice'], $itemList);
                echo "created";
            }
        }
        else if(isset($_GET['command'])){
            if($_GET['command'] == 'getOrderDetail'){
                $arr = [];
                $arr['orderInfo'] = $order->getOrderInfo($_GET['orderID']);
                $arr['itemList'] =  $order->getItemList($_GET['orderID']);
                echo json_encode($arr);
            }
            else if($_GET['command'] == 'getOrderList'){
                echo json_encode($order->getOrderList());
            }
        }
    }
?>
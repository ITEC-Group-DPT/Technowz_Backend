<?php 
    include './apiheader.php';
    include '../classed/Order.php';

    $header = getallheaders();
    $userID = $header['Userid'];

    if($userID == NULL) errorAPI();
    else{
        $order = new Order($conn, $userID);
        if($_POST['command'] == 'create'){
            $arr = json_decode($_POST['list']);
            $order->createOrder($_POST['name'], $_POST['address'], $_POST['phone'], $userID, $arr, $_POST['total']);
            echo "created";
        }
        else if($_GET['command'] == 'getOrderDetail'){
            $arr = [];
            $arr['orderInfo'] = $order->getOrderInfo($_GET['orderID']);
            $arr['itemList'] =  $order->getItemList($_GET['orderID']);

            echo json_encode($arr);
        }
        else if($_GET['command'] == 'getOrderList'){
            echo json_encode($order->getOrderList($userID));
        }
    }
?>
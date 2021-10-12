<?php 
    include './apiheader.php';
    include '../classed/Order.php';

    $header = getallheaders();
    $userID = $header['Userid'];

    if($userID == NULL) errorAPI();
    else{
        $order = new Order($conn);
        if($_POST['command'] == 'create'){
            $arr = json_decode($_POST['list']);
            $order->createOrder($_POST['name'], $_POST['address'], $_POST['phone'], $userID, $arr, $_POST['total']);
            echo "created";
        }
        else if($_GET['command'] == 'getorder'){
            $arr = [];
            $arr['list'] = $order->getOrder($_GET['orderid']);
            $arr['datediff'] = $order->getDateDiff($_GET['orderid']);
            echo json_encode($arr);
        }
        else if($_GET['command'] == 'getdatediff'){
            echo $order->getDateDiff($_GET['orderid']);
        }
        else if($_GET['command'] == 'getuserorders'){
            echo json_encode($order->getUserOrders($userID));
        }
    }
?>
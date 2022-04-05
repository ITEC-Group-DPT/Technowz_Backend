<?php
    include '../api/apiheader.php';
    include '../classes/Order.php';

    $header = getallheaders();

    if (isset($_GET['command'])){
        if ($_GET['command'] == 'getOrderSummary'){
            $data = Order::getOrderSummary($conn, $_GET['sortBy']);
            successApi($data);
        }

        else if ($_GET['command'] == 'getIncomeSummary'){
            $data = Order::getIncomeSummary($conn, $_GET['sortBy']);
            successApi($data);
        }

        else if($_GET['command'] == 'getOrderTotalPage'){
            $data = Order::getTotalOrder($conn);
            successApi($data);
        }

        else if ($_GET['command'] == 'getOrderListByPage'){
            $data = Order::getOrderListByPage($conn,
                                                $_GET['sortByStatus'],
                                                $_GET['offset'],
                                                $_GET['limit']);
            successApi($data);
        }
    }

    else if (isset($_POST['command'])){

    }

?>
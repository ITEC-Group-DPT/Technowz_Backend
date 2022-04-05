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

        else if ($_GET['command'] == 'getOrderByStatus'){
            $data = Order::getOrderByStatus($conn, $_GET['sortByStatus']);
            successApi($data);
        }

        else if ($_GET['command'] == 'getOrderByPage'){
            $data = Order::getOrderByPage($conn,
                                            $_GET['sortByStatus'],
                                            $_GET['offset'],
                                            $_GET['limit']);
            successApi($data);
        }

        else if ($_GET['command'] == 'getOrderBySearch'){
            $data = Order::getOrderBySearch($conn, $_GET['search']);
            successApi($data);
        }
    }

    else if (isset($_POST['command'])){

    }

?>
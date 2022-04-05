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

        else if ($_GET['command'] == 'getOrderByPage'){
            // return a list of order (with pagination)
            $data = Order::getOrderByOption($conn,
                                            $_GET['search'],
                                            $_GET['sortByStatus'],
                                            false,
                                            $_GET['offset'],
                                            $_GET['limit']);
            successApi($data);
        }

        else if ($_GET['command'] == 'getOrderByFilter'){
            // return an object with totalPage and a list of order (default set list of page 1)
            $data = Order::getOrderByOption($conn,
                                            $_GET['search'],
                                            $_GET['sortByStatus']);
            successApi($data);
        }

        // else if ($_GET['command'] == 'getOrderBySearch'){
        //     // return an object with totalPage and a list of order (default set list of page 1)
        //     $data = Order::getOrderBySearch($conn,
        //                                     $_GET['search'],
        //                                     $_GET['sortByStatus']);
        //     successApi($data);
        // }
    }

    else if (isset($_POST['command'])){

    }

?>
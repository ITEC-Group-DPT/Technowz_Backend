<?php
    include '../api/apiheader.php';
    include '../classes/Order.php';

    $header = getallheaders();

    if (isset($_POST['command'])){

    }

    else if (isset($_GET['command'])){
        if ($_GET['command'] == 'getOrderSummary'){
            $data = Order::getOrderSummary($conn, $_GET['sortBy']);
            successApi($data);
        }
        else if ($_GET['command'] == 'getIncomeSummary'){

        }
    }

?>
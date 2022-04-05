<?php
    include '../api/apiheader.php';
    include '../classes/OrderReport.php';

    $header = getallheaders();

    if (isset($_GET['command'])){
        $orderReport = new OrderReport($conn);

        if ($_GET['command'] == 'getOrderSummary'){
            $data = $orderReport->getOrderSummary($_GET['sortBy']);
            successApi($data);
        }

        else if ($_GET['command'] == 'getIncomeSummary'){
            $data = $orderReport->getIncomeSummary($_GET['sortBy']);
            successApi($data);
        }

        else if ($_GET['command'] == 'getOrderByPage'){
            // return a list of order (with pagination)
            $data = $orderReport->getOrderByOption($_GET['search'],
                                                   $_GET['sortByStatus'],
                                                   false,
                                                   $_GET['offset'],
                                                   $_GET['limit']);
            successApi($data);
        }

        else if ($_GET['command'] == 'getOrderByFilter'){
            // return an object with totalPage and a list of order (default set list of page 1)
            $data = $orderReport->getOrderByOption($_GET['search'],
                                                   $_GET['sortByStatus']);
            successApi($data);
        }
    }

    else if (isset($_POST['command'])){

    }

?>
<?php
    include '../api/apiheader.php';
    include '../classes/OrderReport.php';

    $header = getallheaders();
    $orderReport = new OrderReport($conn);

    if (isset($_GET['command'])){
        $data = [];

        if ($_GET['command'] == 'getOrderSummary')
            $data = $orderReport->getOrderSummary($_GET['sortBy']);

        elseif ($_GET['command'] == 'getIncomeSummary')
            $data = $orderReport->getIncomeSummary($_GET['sortBy']);

        elseif ($_GET['command'] == 'getOrderByPage')
            // return a list of order (with pagination)
            $data = $orderReport->getOrderByOption($_GET['search'],
                                                   $_GET['sortByStatus'],
                                                   false,
                                                   $_GET['offset'],
                                                   $_GET['limit']);

        elseif ($_GET['command'] == 'getOrderByFilter')
            // return an object with totalPage and a list of order (default set list of page 1)
            $data = $orderReport->getOrderByOption($_GET['search'],
                                                   $_GET['sortByStatus']);

        successApi($data);
    }

    elseif (isset($_POST['command'])){
        if ($_POST['command'] == 'updateStatus')
            if ($orderReport->updateStatus($_POST['orderID'], $_POST['statusID']))
                successApi("Status was updated successfully");
    }

?>
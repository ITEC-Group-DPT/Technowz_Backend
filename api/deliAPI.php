<?php
    include './apiheader.php';
    include '../classes/DeliveryInfo.php';

    $header = getallheaders();
    if(isset($header['Userid'])){
        $userID = $header['Userid'];
        $deli = new DeliveryInfo($conn, $userID);
        if(isset($_POST['command'])){
            if ($_POST['command']== 'update'){
                if($deli->updateDeliveryInfo($_POST['deliID'], $_POST['name'], $_POST['address'], $_POST['phone']))
                    successApi("Updated delivery info");
                else failApi("Can not update delivery info");
            }
            else if ($_POST['command']== 'create'){
                $id = $deli->createDeliveryInfo($_POST['name'], $_POST['address'], $_POST['phone']);
                if($id != false)
                    successApi($id);
                else failApi("Can not create delivery info");
            }
            else if ($_POST['command']== 'delete'){
                if($deli->deleteDelivery($_POST['deliID']))
                    successApi("Deleted delivery info");
                else failApi("Can not delete delivery info");
            }
            else failApi('No command found');
        }
        else if(isset($_GET['command'])){
            if($_GET['command']== 'getDelivery'){
                $arr = $deli->getDeliveryInfo();
                successApi($arr);
            }
            else failApi('No command found');
        }
        else failApi('No command found');
    }
    else failApi('No userID found');
?>
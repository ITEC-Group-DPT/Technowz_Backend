<?php 
    include './apiheader.php';
    include '../classes/DeliveryInfo.php';
    
    $header = getallheaders();
    $userID = $header['Userid'];

    if ($userID == NULL) errorAPI();
    else{ 
        $deli = new DeliveryInfo($conn, $userID);
        if(isset($_POST['command'])){
            if ($_POST['command']== 'update'){
                $deli->updateDeliveryInfo($_POST['deliID'], $_POST['name'], $_POST['address'], $_POST['phone']);
                echo "updated";
            }
            else if ($_POST['command']== 'create'){
                $id = $deli->createDeliveryInfo($_POST['name'], $_POST['address'], $_POST['phone']);
                echo "created";
            }
            else if ($_POST['command']== 'delete'){
                $deli->deleteDelivery($_POST['deliID']);
                echo "deleted";
            }
        }
        else if(isset($_GET['command'])){
            if($_GET['command']== 'getDelivery'){
                $array = $deli->getDeliveryInfo();
                echo json_encode($array);
            }
        }
    }
?>
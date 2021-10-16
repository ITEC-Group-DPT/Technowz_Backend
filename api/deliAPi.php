<?php 
    include './apiheader.php';
    include '../classes/DeliveryInfo.php';
    
    $header = getallheaders();
    $userID = $header['Userid'];

    if ($userID == NULL) errorAPI();
    else{
        $deli = new DeliveryInfo($conn, $userID); 
        if ($_GET['command']== 'getDelivery'){
            $array = $deli->getDeliveryInfo();
            echo json_encode($array);
        }
        elseif ($_POST['command']== 'update'){
            $deli->updateDeliveryInfo($_POST['deliID'], $_POST['name'], $_POST['address'], $_POST['phone']);
            echo "updated";
        }
        elseif ($_POST['command']== 'create'){
            $id = $deli->createDeliveryInfo($_POST['name'], $_POST['address'], $_POST['phone']);
            echo "created";
        }
        elseif ($_POST['command']== 'delete'){
            $deli->deleteDelivery($_POST['deliID']);
            echo "deleted";
        }
    }
?>
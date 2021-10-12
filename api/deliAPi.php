<?php 
include './apiheader.php';
include '../classes/DeliveryInfo.php';
$header = getallheaders();
$userid = $header['Userid'];

if ($userid == NULL){
    errorAPI();
}else{
    $deli = new DeliveryInfo($conn); 
    if ($_GET['command']== 'getdelivery'){
        $array = $deli->getDeliveryInfo($userid);
        echo json_encode($array);
    }
    elseif ($_POST['command']== 'update'){
        $deli->updateDeliveryInfo($_POST['deliID'],$_POST['name'],$_POST['address'],$_POST['phone']);
        echo "updated";
    }
    elseif ($_POST['command']== 'create'){
        $id = $deli->createDeliveryInfo($_POST['name'],$_POST['address'],$_POST['phone'],$userid);
        echo "created";
    }
    elseif ($_POST['command']== 'delete'){
        $deli->deleteDelivery($_POST['deliID']);
        echo "deleted";
    }
}
?>
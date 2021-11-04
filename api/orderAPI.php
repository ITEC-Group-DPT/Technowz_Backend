<?php
include './apiheader.php';
include '../classes/Order.php';
include '../classes/DeliveryInfo.php';
include '../classes/Cart.php';
$header = getallheaders();
if (isset($header['Userid']))
{
    $userID = $header['Userid'];
    $order = new Order($conn);
    if (isset($_POST['command']))
    {
        if ($_POST['command'] == 'createOrder')
        {
            $itemList = json_decode($_POST['list']);
            if ($order->createOrder($userID, $_POST['name'], $_POST['address'], $_POST['phone'], $_POST['totalPrice'], $itemList))
            {
                (new Cart($conn, $userID))->removeall();
                if ($_POST['deliID'] == -1) (new DeliveryInfo($conn, $userID))->createDeliveryInfo($_POST['name'], $_POST['address'], $_POST['phone']);
                else (new DeliveryInfo($conn, $userID))->updateDeliveryInfo($_POST['deliID'], $_POST['name'], $_POST['address'], $_POST['phone']);
                successApi('Order created');
            }
            else failApi('Can not create order');
        }
        else failApi('No command found');
    }
    else if (isset($_GET['command']))
    {
        if ($_GET['command'] == 'getOrderDetail')
        {
            $arr = [];
            $arr['orderInfo'] = $order->getOrderInfo($_GET['orderID'], $userID);
            $arr['itemList'] =  $order->getItemList($_GET['orderID'], $userID);
            if ($arr['orderInfo'] != false)
                successApi($arr);
            else failApi('No order detail found');
        }
        else if ($_GET['command'] == 'getOrderList')
        {
            $offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
            $limit = isset($_POST['limit']) ? $_POST['limit'] : 5;
            $data = $order->getOrderList($userID,$offset,$limit);
            successApi($data);
        }
        else failApi('No command found');
    }
    else failApi('No command found');
}
else failApi('No userID found');

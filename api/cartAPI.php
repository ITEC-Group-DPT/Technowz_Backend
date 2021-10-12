<?php
	echo "dm cuoc doi";
	return;
	include './apiheader.php';
	include '../classes/Cart.php';

	$header = getallheaders();
	$userID = $header['userID'];

	if ($userID == NULL) errorAPI();
	else{
		$cart = new Cart($conn, $userID);
		$productID = (isset($_POST['productID'])) ? $_POST['productID'] : '';
		if(isset($_POST['command'])){
			if($_POST['command'] == 'add'){
				if($cart->addItemToCart($productID))
					echo $cart->getTotalQuantity();
				else errorAPI();
			}
			else if($_POST['command'] == 'remove'){
				if($cart->removeItem($productID))
					echo $cart->getTotalQuantity();
				else errorAPI();
			}
			else if($_POST['command'] == 'increase'){
				if($cart->increaseQuantity($productID))
					echo $cart->getTotalQuantity();
				else errorAPI();
			}
			else if($_POST['command'] == 'decrease'){
				if($cart->decreaseQuantity($productID))
					echo $cart->getTotalQuantity();
				else errorAPI();
			}
			else if($_POST['command'] == 'removeAll'){
				if($cart->removeAll())
					echo 'remove all success';
				else errorAPI();
			}
		}
		else if(isset($_GET['command'])){
			$productID = (isset($_GET['productID'])) ? $_GET['productID'] : '';
			if($_GET['command'] == 'getQuantity'){
				echo $cart->getQuantity($productID);
			}
			else if($_GET['command'] == 'getCartList'){
				$arr = [];
				$arr['cartList'] = $cart->getCartList();
				$arr['totalPrice'] = $cart->getTotalPrice();
				$arr['totalQuantity'] = $cart->getTotalQuantity();
				echo json_encode($arr);
			}
			else if($_GET['command'] == 'getTotalPrice'){
				echo $cart->getTotalPrice();
			}
			else if($_GET['command'] == 'getTotalQuantity'){
				echo $cart->getTotalQuantity();
			}
		}
	}
?>
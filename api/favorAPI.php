<?php 
	include './apiheader.php';
	include '../classes/Favorite.php';

	$header = getallheaders();
	$userID = $header['userID'];

	if ($userID == NULL) errorAPI();
	else{
		$favorite = new Favorite($conn, $userID);
        if(isset($_POST['command'])){
            $productID = (isset($_POST['productID'])) ? $_POST['productID'] : '';
             if($_POST['command'] == 'changeFavorite'){
                $arr['isLike'] = $favorite->changeFavorite($productID);  // like = true, not like = false
                echo json_encode($arr);
            }
        }
        else if(isset($_GET['command'])){
            if($_GET['command'] == 'getFavoriteList'){
                echo json_encode($favorite->getFavoriteList());
            }
        }
	}
?>
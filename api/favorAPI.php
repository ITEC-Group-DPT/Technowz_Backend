<?php
	include './apiheader.php';
	include '../classes/Favorite.php';

	$header = getallheaders();
    if(isset($header['Userid'])){
        $userID = $header['Userid'];
        $favorite = new Favorite($conn, $userID);
        if(isset($_POST['command'])){
            $productID = (isset($_POST['productID'])) ? $_POST['productID'] : '';
            if($_POST['command'] == 'changeFavorite'){
                $arr['isLike'] = $favorite->changeFavorite($productID);  // like = true, not like = false
                successApi($arr);
            }
            else failApi('No command found');
        }
        else if(isset($_GET['command'])){
            if($_GET['command'] == 'getFavoriteList'){
                $data = $favorite->getFavoriteList();
                successApi($data);
            }
            else failApi('No command found');
        }
        else failApi('No command found');
    }
    else failApi('No userID found');
?>
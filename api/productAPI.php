<?php 
	include './apiheader.php';
	include '../classes/Product.php';
    include '../classes/Favorite.php';

    $header = getallheaders();
    $userID = 0;
    if(isset($header['Userid'])){
        $userID = $header['Userid'];
    }

    if(isset($_GET['command'])){
        $productID = (isset($_GET['productID'])) ? $_GET['productID'] : '';
        if($_GET['command'] == 'getProduct'){
            $product = new Product($conn, $productID);
            $arr = [];
            $arr['product'] = $product->getProduct();
            $arr['favorite'] = false;
            if($userID != 0){
                $favorite = new Favorite($conn, $userID);
                $arr['favorite'] = $favorite->checkFavorite($productID);
            }
            if($arr['product'] != ''){
                successApi($arr);
            } else failAPI("No product found");
        }
        else if($_GET['command'] == 'getProductCategory'){
            $limit = (isset($_GET['limit'])) ? $_GET['limit'] : 20;
            $data = Product::getProductsByCategory($conn, $_GET['typeOfProduct'], $limit);
            if($data != []){
                successApi($data);
            }
            else failAPI("No product category");
        }
        else if($_GET['command'] == 'getTopRating'){
            $limit = (isset($_GET['limit'])) ? $_GET['limit'] : 20;
            $data = Product::getTopRating($conn, $limit);
            if($data != []){
                successApi($data);
            }
            else failAPI("No top rating");
        }
        else if($_GET['command'] == 'searchProducts'){
            $limit = (isset($_GET['limit'])) ? $_GET['limit'] : 5;
            $data = Product::getProducts($conn, $_GET['searchValue'], $limit);
            if($data != []){
                successApi($data);
            }
            else failAPI("No product found");
        }
	}
    else echo failApi("No command found!");
?>
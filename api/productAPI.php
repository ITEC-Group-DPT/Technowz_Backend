<?php 
	include './apiheader.php';
	include '../classes/Product.php';
    include '../classes/Favorite.php';

    if(isset($_GET['command'])){
        $productID = (isset($_GET['productID'])) ? $_GET['productID'] : '';
        if($_GET['command'] == 'getProduct'){
            $header = getallheaders();
            $product = new Product($conn, $productID);
            $arr = [];
            $arr['product'] = $product->getProduct();

            if(isset($header['userid'])){
                $favorite = new Favorite($conn, $header['userid']);
                $arr['favorite'] = $favorite->checkFavorite($productID);
            } 
            else $arr['favorite'] = false;

            if($arr['product'] != [])
                successApi($arr);
            else failAPI("No product found");
        }
        else if($_GET['command'] == 'getProductCategory'){
            $limit = (isset($_GET['limit'])) ? $_GET['limit'] : 20;
            $data = Product::getProductsByCategory($conn, $_GET['typeOfProduct'], $limit);
            successApi($data);
        }
        else if($_GET['command'] == 'getTopRating'){
            $limit = (isset($_GET['limit'])) ? $_GET['limit'] : 20;
            $data = Product::getTopRating($conn, $limit);
            successApi($data);
        }
        else if($_GET['command'] == 'searchProducts'){
            $limit = (isset($_GET['limit'])) ? $_GET['limit'] : 5;
            $data = Product::getProducts($conn, $_GET['searchValue'], $limit);
            successApi($data);
        }
        else failApi("No command found!");
	}
    else failApi("No command found!");
?>
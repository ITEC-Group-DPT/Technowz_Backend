<?php
	include './apiheader.php';

	include '../classes/Product.php';
    include '../classes/ProductStatistic.php';

    include '../classes/Favorite.php';

    $header = getallheaders();
if (isset($header['userid']))
{
    $userID = $header['userid'];
    }

    if(isset($_GET['command'])){
        $productID = (isset($_GET['productID'])) ? $_GET['productID'] : '';
        if($_GET['command'] == 'getProduct'){
            $product = new Product($conn, $productID);
            $arr = [];
            $arr['product'] = $product->getProduct();
            $arr['comment'] = $product->getProductComment();
            
            if(isset($userID)){
                $favorite = new Favorite($conn, $userID);
                $arr['favorite'] = $favorite->checkFavorite($productID);
            }
            else $arr['favorite'] = false;

            if($arr['product'] != [])
                successApi($arr);
            else failAPI("No product found");
        }
        else if($_GET['command'] == 'getTotalCategory'){
            $data = Product::getTotalCategory($conn, $_GET['typeOfProduct']);
            successApi($data);
        }
        else if($_GET['command'] == 'getProductCategory'){
            $data = Product::getProductsByCategory($conn, $_GET['typeOfProduct'], $_GET['orderBy'], $_GET['option'], $_GET['offset'], $_GET['limit']);
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
    else if(isset($_POST['command'])){
        if ($_POST['command'] == 'updateProductView') {

            $stat = new ProductStatistic($conn);
    
            $productID = isset($_POST['productID']) ? $_POST['productID'] : -1;

            if ($productID == -1) failApi("Invalid productID");
    
            $success = $stat->updateProductView($productID);
    
            $success
                ? successApi("Update productview successfully")
                : failApi("Update productview fail");
                
        }
        else failApi('No command found');
    }
    else failApi("No command found!");

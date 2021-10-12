<?php 
    echo "ai cho t luong thien 1 :((((((";
    return;
	include './apiheader.php';
    echo "ai cho t luong thien 2 :((((((";
    return;
	include '../classes/Product.php';
    include '../classes/Favorite.php';
    echo "ai cho t luong thien 3 :((((((";
    return;
    $header = getallheaders();
    echo "ai cho t luong thien 4 :((((((";
    return;
    if(isset($header['Userid'])){
        $userID = $header['Userid'];
    }
    if(isset($_GET['command'])){
        $productID = (isset($_GET['productID'])) ? $_GET['productID'] : '';
        if($_GET['command'] == 'getProduct'){
            $product = new Product($conn, $productID);
            $favorite = new Favorite($conn, $userID);
            $arr = [];
            $arr['product'] = $product->getProduct();
            $arr['favorite'] = (!isset($userID)) ? false : $favorite->checkFavorite($productID);
            echo json_encode($arr);
        }
        else if($_GET['command'] == 'getProductCategory'){
            $limit = (isset($_GET['limit'])) ? $_GET['limit'] : 20;
            echo json_encode(Product::getProductsByCategory($conn, $_GET['typeOfProduct'], $limit));
        }
        else if($_GET['command'] == 'getTopRating'){
            $limit = (isset($_GET['limit'])) ? $_GET['limit'] : 20;
            echo json_encode(Product::getTopRating($conn, $limit));
        }
        else if($_GET['command'] == 'searchProducts'){
            $limit = (isset($_GET['limit'])) ? $_GET['limit'] : 5;
            echo json_encode(Product::getProducts($conn, $_GET['searchValue'], $limit));
        }
	}
?>
<?php
	include './apiheader.php';
	include '../classes/Product.php';
    include '../classes/Favorite.php';

    $header = getallheaders();
    if(isset($header['Userid'])){
        $userID = $header['Userid'];
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
        $productID = (isset($_POST['productID'])) ? $_POST['productID'] : '';
        $product = new Product($conn, $productID);
        $data = [];
        $data['type'] = isset($_POST['type']) ? $_POST['type']  :  "";
        $data['description'] = isset($_POST['description']) ? $_POST['description'] : "";
        $data['spec'] = isset($_POST['spec']) ? $_POST['spec'] : "";
        $data['name'] = isset($_POST['name']) ? $_POST['name'] : "";
        $data['price'] = isset($_POST['price']) ? $_POST['price'] : "";
        $data['rating'] = isset($_POST['rating']) ? $_POST['rating'] : "";
        $data['sold'] = isset($_POST['sold']) ? $_POST['sold'] : "";
        $data['img1'] = isset($_POST['img1']) ? $_POST['img1'] : "";
        $data['img2'] = isset($_POST['img2']) ? $_POST['img2'] : "";
        $data['img3'] = isset($_POST['img3']) ? $_POST['img3'] : "";
        $data['img4'] = isset($_POST['img4']) ? $_POST['img4'] : "";

        if($_POST['command'] == 'create'){
            $res = $product->createProduct($data);
            if($res)
                successApi($res);
            else failApi("No product created");
        }
        else if($_POST['command'] == 'modify'){
            if($product->modifyProduct($data))
                successApi("A product is modified");
            else failApi("No product is modified");
        }
        else if($_POST['command'] == 'remove'){
            if($product->removeProduct())
                successApi("A product is removed");
            else failApi("No product is removed");
        }
        else failApi('No command found');
    }
    else failApi("No command found!");

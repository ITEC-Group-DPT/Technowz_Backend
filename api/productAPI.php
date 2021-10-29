<?php
	include './apiheader.php';
	include '../classes/Product.php';
    include '../classes/Favorite.php';

    $header = getallheaders();
    if(isset($header['userid'])){
        $userID = $header['userid'];
        if(isset($_GET['command'])){
            $productID = (isset($_GET['productID'])) ? $_GET['productID'] : '';
            if($_GET['command'] == 'getProduct'){
                // $header = getallheaders();
                $product = new Product($conn, $productID);
                $arr = [];
                $arr['product'] = $product->getProduct();
    
                if(isset($header['Userid'])){
                    $favorite = new Favorite($conn, $header['Userid']);
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
        else if(isset($_POST['command'])){
            $productID = (isset($_POST['productID'])) ? $_POST['productID'] : '';
            $product = new Product($conn, $productID);
            $data = [];
            $data['type'] = $_POST['type'];
            $data['description'] = $_POST['description'];
            $data['spec'] = $_POST['spec'];
            $data['name'] = $_POST['name'];
            $data['price'] = $_POST['price'];
            $data['rating'] = $_POST['rating'];
            $data['sold'] = $_POST['sold'];
            $data['img1'] = $_POST['img1'];
            $data['img2'] = $_POST['img2'];
            $data['img3'] = $_POST['img3'];
            $data['img4'] = $_POST['img4'];
            
            if($_POST['command'] == 'create'){
				if($product->createProduct($data))
					successApi("New product created");
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
    }
    
?>
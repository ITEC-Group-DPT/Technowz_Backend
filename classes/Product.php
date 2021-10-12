<?php
    class Product {
        private $conn;
        private $productID;

        //constructor
        public function __construct($conn, $productID){
            $this->conn = $conn;
            $this->productID = $productID;
        }

        public function getProduct(){
            $stmt = $this->conn->prepare("SELECT * 
                                        from products p, productimage i 
                                        where p.productID = ? and p.productID = i.productID");
            $stmt->bind_param("i", $this->productID);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows == 1) 
                return $result->fetch_assoc();
            else return false;
        }

        //static
        public static function getProductsByCategory($conn, $type, $limit = 20, $offset = 0){
            $stmt = $conn->prepare("SELECT p.productID, p.name, pimg.img1, p.rating, p.sold, p.price
                                    from products p, productimage pimg
                                    where p.type = ? and p.productID = pimg.productID limit ?,?");
            $stmt->bind_param("sii", $type, $offset, $limit);
            $stmt->execute();
            $results = $stmt->get_result();
            return $results->fetch_all(MYSQLI_ASSOC);
        }

        public static function getTopRating($conn, $limit = 20){
            $stmt = $conn->prepare("SELECT p.productID, p.name, pimg.img1, p.rating, p.sold, p.price
                                    from products p, productimage pimg 
                                    where p.productID = pimg.productID 
                                    order by p.sold desc limit ?");
            $stmt->bind_param("i", $limit);
            $stmt->execute();
            $results = $stmt->get_result();
            return $results->fetch_all(MYSQLI_ASSOC);
        }

        public static function getProducts($conn, $value, $limit = 5){
          $value = "%". $value ."%";
          $stmt = $conn->prepare("SELECT p.productID, p.name, pimg.img1, p.rating, p.sold, p.price
                                from products p, productimage pimg
                                where p.productID = pimg.productID and p.name like ? limit ?");
          $stmt->bind_param("si", $value, $limit);
          $stmt->execute();
          $results = $stmt->get_result();
          if ($results->num_rows != 0)
            return $results->fetch_all(MYSQLI_ASSOC);
          else return false;
        }
    }
?>
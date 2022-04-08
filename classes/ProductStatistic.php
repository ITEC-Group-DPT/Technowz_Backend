<?php 
    class ProductStatistic{
        private $conn;

        public function __construct($conn)
        {
            $this->conn = $conn;
        }

        public function getTotalNumberOfProductAdmin(){
            $stmt = $this->conn->prepare("SELECT DISTINCT COUNT(p.productID)
                                    from products p");
            $stmt->execute();
            $results = $stmt->get_result();
            return $results->fetch_all(MYSQLI_ASSOC);
        }
    
        public function getNumberOfProductByCategoryAdmin($type){
            $stmt = $this->conn->prepare("SELECT DISTINCT COUNT(p.productID)
                                    from products p
                                    where p.type = ?");
            $stmt->bind_param("s", $type);
            $stmt->execute();
            $results = $stmt->get_result();
            return $results->fetch_all(MYSQLI_ASSOC);
        }
    
        public function getAllProductByPageAdmin($offset, $limit = 6){
            $stmt = $this->conn->prepare("SELECT *
                                from products p, productimage img
                                where p.productID = img.productID
                                limit ?, ?");
            $stmt->bind_param("ii", $offset, $limit);
            $stmt->execute();
            $results = $stmt->get_result();
            return $results->fetch_all(MYSQLI_ASSOC);
        }
    
        public function getProductByCategoryAdmin($type, $offset, $limit = 6)
        {
            $stmt = $this->conn->prepare("SELECT *
                                    from products p, productimage pimg
                                    where p.type = ? and p.productID = pimg.productID
                                    limit ?, ?");
            $stmt->bind_param("sii", $type, $offset, $limit);
            $stmt->execute();
            $results = $stmt->get_result();
            return $results->fetch_all(MYSQLI_ASSOC);
        }
    }
?>

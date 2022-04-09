<?php 
    class ProductStatistic{
        private $conn;

        public function __construct($conn)
        {
            $this->conn = $conn;
        }

        public function getTotalNumberOfProductAdmin($value){
            $stmt = $this->conn->prepare("SELECT DISTINCT COUNT(p.productID) as totalPage
                                        from products p
                                        where p.name like ? or p.productID like ?");
            $stmt->bind_param("ss", $value, $value);
			$stmt->execute();
			$results = $stmt->get_result();
			return $results->fetch_all(MYSQLI_ASSOC);
        }
    
        public function getNumberOfProductByCategoryAdmin($type, $value){
            $stmt = $this->conn->prepare("SELECT DISTINCT COUNT(p.productID) as totalPage
                                        from products p
                                        where p.type like ? and (p.name like ? or p.productID like ?)");
            $stmt->bind_param("sss", $type, $value, $value);
            $stmt->execute();
            $results = $stmt->get_result();
            return $results->fetch_all(MYSQLI_ASSOC);
        }
    
        public function getAllProductByPageAdmin($offset, $limit = 6, $value, $orderBy, $order){
			$temp = "p." . $orderBy;
            $stmt = $this->conn->prepare("SELECT *
                                        from products p, productimage img
                                        where p.productID = img.productID and (p.name like ? or p.productID like ?)
                                        order by $temp $order
                                        limit ?, ?");
            $stmt->bind_param("ssii", $value, $value, $offset, $limit);
            $stmt->execute();
            $results = $stmt->get_result();
            return $results->fetch_all(MYSQLI_ASSOC);
        }
    
        public function getProductByCategoryAdmin($type, $offset, $limit = 6, $value, $orderBy, $order)
        {
			$temp = "p." . $orderBy;
            $stmt = $this->conn->prepare("SELECT *
                                        from products p, productimage pimg
                                        where p.type like ? and p.productID = pimg.productID and (p.name like ? or p.productID like ?)
                                        order by $temp $order
                                        limit ?, ?");
            $temp = 'p.' . $orderBy;
            $stmt->bind_param("sssii", $type, $value, $value, $offset, $limit);
            $stmt->execute();
            $results = $stmt->get_result();
            return $results->fetch_all(MYSQLI_ASSOC);
        }

        public function updateProductView($productID)
        {
			$stmt = $this->conn->prepare("INSERT INTO productview (productID) VALUES (?)");
            $stmt->bind_param("i", $productID);

            $stmt->execute();

            return $stmt->affected_rows == 1;
        }
    }
?>

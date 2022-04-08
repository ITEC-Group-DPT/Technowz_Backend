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
                                        where p.name like ?");
            $stmt->bind_param("s", $value);
			$stmt->execute();
			$results = $stmt->get_result();
			return $results->fetch_all(MYSQLI_ASSOC);
        }
    
        public function getNumberOfProductByCategoryAdmin($type, $value){
            $stmt = $this->conn->prepare("SELECT DISTINCT COUNT(p.productID) as totalPage
                                        from products p
                                        where p.type like ? and p.name like ?");
            $stmt->bind_param("ss", $type, $value);
            $stmt->execute();
            $results = $stmt->get_result();
            return $results->fetch_all(MYSQLI_ASSOC);
        }
    
        public function getAllProductByPageAdmin($offset, $limit = 6, $value, $orderBy, $order){
			$temp = "p." . $orderBy;
            $stmt = $this->conn->prepare("SELECT *
                                        from products p, productimage img
                                        where p.productID = img.productID and p.name like ?
                                        order by $temp $order
                                        limit ?, ?");
            $stmt->bind_param("sii", $value, $offset, $limit);
            $stmt->execute();
            $results = $stmt->get_result();
            return $results->fetch_all(MYSQLI_ASSOC);
        }
    
        public function getProductByCategoryAdmin($type, $offset, $limit = 6, $value, $orderBy, $order)
        {
			$temp = "p." . $orderBy;
            $stmt = $this->conn->prepare("SELECT *
                                        from products p, productimage pimg
                                        where p.type like ? and p.productID = pimg.productID and p.name like ?
                                        order by $temp $order
                                        limit ?, ?");
            $temp = 'p.' . $orderBy;
            $stmt->bind_param("ssii", $type, $value, $offset, $limit);
            $stmt->execute();
            $results = $stmt->get_result();
            return $results->fetch_all(MYSQLI_ASSOC);
        }
    }
?>

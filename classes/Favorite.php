<?php
    class Favorite{
        private $conn;
        private $userID;

        public function __construct($conn, $userID){
            $this->conn = $conn;
            $this->userID = $userID;
        }

        public function addToFavorite($productID){
            $stmt = $this->conn->prepare("INSERT into favorites(userID, productID) values (?, ?)");
            $stmt->bind_param('ii', $this->userID, $productID);
            $stmt->execute();
            if($stmt->affected_rows == 1) return true;
            else return false;
        }

        public function removeFavorite($productID){
            $stmt = $this->conn->prepare("DELETE from favorites where userID = ? and productID = ?");
            $stmt->bind_param('ii', $this->userID, $productID);
            $stmt->execute();
            if($stmt->affected_rows == 1) return true;
            else return false;
        }
        
        public function checkFavorite($productID){
            $stmt = $this->conn->prepare("SELECT * from favorites where userID = ? and productID = ?");
            $stmt->bind_param('ii', $this->userID, $productID);
            $stmt->execute();
            $result = $stmt->get_result();
            if($result->num_rows != 0) return true;
            else return false;
        }

        public function getFavoriteList(){
            $stmt = $this->conn->prepare("SELECT p.productID, p.name, pimg.img1, p.sold, p.rating, p.price
                                        from favorites f, products p, productimage pimg
                                        where f.userID = ? and f.productID = p.productID and p.productID = pimg.productID");
            $stmt->bind_param('i', $this->userID);
            $stmt->execute();
            $results = $stmt->get_result();
            return $results->fetch_all(MYSQLI_ASSOC);
        }

        public function changeFavorite($productID){
            if($this->checkFavorite($productID)) {
                if($this->removeFavorite($productID)) 
                    return false;
            }
            else{
                if($this->addToFavorite($productID)) 
                    return true;
            }
        }
    }
?>
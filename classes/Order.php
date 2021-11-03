<?php
    class Order{
        private $conn;

        public function __construct($conn){
            $this->conn = $conn;
        }

        public function createOrder($userID, $name, $address, $phone, $totalPrice, $productList){
            $stmt = $this->conn->prepare('INSERT into orders (userID, name, address, phone, totalPrice) values (?,?,?,?,?)');
            $stmt->bind_param('isssi', $userID, $name, $address, $phone, $totalPrice);
            $stmt->execute();
            $orderID = $this->conn->insert_id;
            foreach ($productList as $product) {
                $stmt1 = $this->conn->prepare('INSERT into orderdetails (orderID, productID, quantity) values (?,?,?)');
                $stmt1->bind_param('sss', $orderID, $product[0], $product[1]);
                $stmt1->execute();
            }
            if($stmt->affected_rows != 0) return true;
            else return false;
        }

        public function getOrderList($userID,$offset=0,$limit=5){
            $stmt = $this->conn->prepare("SELECT ord.orderID, p.productID, p.name, i.img1, p.price, ordz.quantity, p.rating, p.sold
                                        FROM orders ord, orderdetails ordz,products p, productimage i 
                                        WHERE ord.userID = ? and ord.orderID = ordz.orderID 
                                                and p.productID = i.productID and p.productID = ordz.productID 
                                        ORDER BY ord.orderID desc limit ?,?");
            $stmt->bind_param("iii", $userID,$offset,$limit);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_all(MYSQLI_ASSOC);
            $ords = [];
            foreach ($row as $item) {
                $orderID = $item['orderID'];
                $obj = [
                    "productID" => $item['productID'],
                    "name" => $item['name'],
                    "img1" => $item['img1'],
                    "price" => $item['price'],
                    'quantity' => $item['quantity'],
                    "rating" => $item['rating'],
                    "sold" => $item['sold'],
                ];
                if (!isset($ords[$orderID])) $ords[$orderID] = [];
                array_push($ords[$orderID], $obj);
            }
            return $ords;
        }

        public function getItemList($orderID, $userID){
            $stmt = $this->conn->prepare("SELECT p.productID, p.name, i.img1, p.price, ordz.quantity, p.rating, p.sold
                                        from orders o, orderdetails ordz, products p, productimage i
                                        where o.orderID = ? and o.userID = ? and ordz.orderID = o.orderID 
                                                and ordz.productID = p.productID and p.productID = i.productID");
            $stmt->bind_param("ii", $orderID, $userID);
            $stmt->execute();
            $result = $stmt->get_result();
            if($result->num_rows != 0)
                return $result->fetch_all(MYSQLI_ASSOC);
            else return false;
        }

        public function getOrderInfo($orderID, $userID){
            $stmt = $this->conn->prepare("SELECT *, TIMESTAMPDIFF(minute, dateCreated, NOW()) as 'dateDiff' 
                                        from orders 
                                        where orderID = ? and userID = ?");
            $stmt->bind_param("ii", $orderID, $userID);
            $stmt->execute();
            $result = $stmt->get_result();
            if($result->num_rows != 0)
                return $result->fetch_assoc();
            else return false;
        }
    }
?>
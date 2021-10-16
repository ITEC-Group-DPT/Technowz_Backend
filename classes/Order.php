<?php
    class Order{
        private $conn;
        private $userID;

        public function __construct($conn, $userID){
            $this->conn = $conn;
            $this->userID = $userID;
        }

        public function createOrder($name, $address, $phone, $totalPrice, $productList){
            $stmt = $this->conn->prepare('INSERT into orders (userID, name, address, phone, totalPrice) values (?,?,?,?,?)');
            $stmt->bind_param('sssis', $this->userID, $this->name, $this->address, $this->phone, $this->totalPrice);
            $stmt->execute();
            $this->orderID = $this->conn->insert_id;
            foreach ($this->productList as $product) {
                $stmt = $this->conn->prepare('INSERT into orderdetails (orderID, productID, quantity) values (?,?,?)');
                $stmt->bind_param('sss', $this->orderID, $product[0], $product[1]);
                $stmt->execute();
            }
        }

        public function getOrderList(){
            $stmt = $this->conn->prepare("SELECT ord.orderID, p.productID, p.name, i.img1, p.price, ordz.quantity, p.rating, p.sold
                                        FROM orders ord, orderdetails ordz,products p, productimage i 
                                        WHERE ord.userID = ? and ord.orderID = ordz.orderID and p.productID = i.productID and p.productID = ordz.productID 
                                        ORDER BY ord.orderID desc");
            $stmt->bind_param("i", $this->userID);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $ords = [];
            foreach ($result as $item) {
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
                //if (!isset($ords[$orderID])) $ords[$orderID] = [];
                array_push($ords[$orderID], $obj);
            }
            return $ords;
        }

        public function getItemList($orderID){
            $stmt = $this->conn->prepare("SELECT p.productID, p.name, i.img1, p.price, ordz.quantity, p.rating, p.sold
                                        from orderdetails ordz, products p, productimage i
                                        where ordz.orderID = ? and ordz.productID = p.productID and p.productID = i.productID");
            $stmt->bind_param("i", $orderID);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            return $result;
        }

        public function getOrderInfo($orderID){
            $stmt = $this->conn->prepare("SELECT *, TIMESTAMPDIFF(minute, dateCreated, NOW()) as 'dateDiff' from orders where orderID = ?");
            $stmt->bind_param("i", $this->orderID);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            return $result;
        }
    }
?>
<?php
    class Order{
        private $conn;
        private $name;
        private $id;
        private $address;
        private $phone;
        private $userID;
        private $datecreated;
        private $total;
        private $products = [];

        public function __construct($conn){
            $this->conn = $conn;
        }

        public function getOrderDetail($id){
            $this->id = $id;
            $sql = "SELECT ord.*, ord.name as 'customer', ordz.quantity, p.productID, p.price, p.name, p.rating, p.sold, pimg.img1 
                    from orderdetails ordz, orders ord, products p, productimage pimg 
                    where ord.orderID = ? and ord.orderID = ordz.orderID and ordz.productID = p.productID and p.productID = pimg.productID";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $this->id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $ord = $result->fetch_all(MYSQLI_ASSOC);
                $this->name = $ord[0]['customer'];
                $this->address = $ord[0]['address'];
                $this->phone = $ord[0]['phone'];
                $this->datecreated = $ord[0]['dateCreated'];
                $this->total = $ord[0]['totalPrice'];
                $this->products = $ord;
            }
            return $this->products;
        }

        public function createOrder($name, $address, $phone, $userID, $productlist, $total){
            $this->name = $name;
            $this->address = $address;
            $this->phone = $phone;
            $this->userID = $userID;
            $this->products = $productlist;
            $this->total = $total;
            $stmt = $this->conn->prepare('INSERT into orders (address, name, phone, userID, totalPrice) values (?,?,?,?,?)');
            $stmt->bind_param('sssis', $this->address, $this->name, $this->phone, $this->userID, $this->total);
            $stmt->execute();
            $row = $this->conn->insert_id;
            foreach ($this->products as $product) {
                $stmt = $this->conn->prepare('INSERT into orderdetails (orderID,productID,quantity) values (?,?,?)');
                $stmt->bind_param('sss', $row, $product[0], $product[1]);
                $stmt->execute();
            }
        }

        public function getDateDiff($id){
            $stmt = $this->conn->prepare("SELECT 
                                        TIMESTAMPDIFF(minute, dateCreated, NOW()) as 'datediff' 
                                        from orders where orderID = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                $datediff = $row['datediff'];
                //convert date diff to days, hours or minutes
                $type = " minutes";
                if ($datediff >= 1440) {
                    $datediff /= 1440;
                    $type = " days";
                } else if ($datediff >= 60) {
                    $datediff /= 60;
                    $type = " hours";
                };
                return intval($datediff) . $type . " ago";
            } 
            else return "Error display this";
        }

        public function getOrderList($userID){
            $sql = "SELECT ord.orderID, ordz.quantity, p.*, pimg.img1 
                    FROM orders ord, orderdetails ordz,products p, productimage pimg 
                    WHERE ord.userID = ? and ord.orderID = ordz.orderID and p.productID = pimg.productID and p.productID = ordz.productID 
                    ORDER BY ord.orderID desc";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $userID);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_all(MYSQLI_ASSOC);
            $ords = [];
            foreach ($row as $item) {
                $ID = $item['orderID'];
                $obj = [
                    "productID" => $item['productID'],
                    "name" => $item['name'],
                    "img1" => $item['img1'],
                    "price" => $item['price'],
                    'quantity' => $item['quantity'],
                    "rating" => $item['rating'],
                    "sold" => $item['sold'],
                ];
                if (!isset($ords[$ID])) $ords[$ID] = [];
                array_push($ords[$ID], $obj);
            }
            return $ords;
        }
    }
?>
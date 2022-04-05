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
            foreach ($productList as $product){
                $stmt1 = $this->conn->prepare('INSERT into orderdetails (orderID, productID, quantity) values (?,?,?)');
                $stmt1->bind_param('sss', $orderID, $product[0], $product[1]);
                $stmt1->execute();
                $this->updateSoldProduct($product[0], $product[1]);
            }
            if ($stmt->affected_rows != 0) return true;
            else return false;
        }

        public function updateSoldProduct($productID, $soldQuantity){
            $stmt = $this->conn->prepare('SELECT sold
                                        from products
                                        where productID = ?');

            $stmt->bind_param("i", $productID);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $newSold = $result['sold'] + $soldQuantity;
            $stmt1 = $this->conn->prepare('UPDATE products
                                        set sold = ?
                                        where productID = ?');
            $stmt1->bind_param('ii', $newSold, $productID);
            $stmt1->execute();
            if($stmt1->affected_rows == 1) return true;
            else return false;
        }

        public function getOrderList($userID, $offset = 0, $limit = 5){
            $stmt = $this->conn->prepare("SELECT ord.orderID, p.productID, p.name, i.img1, p.price, ordz.quantity, p.rating, p.sold
                                            FROM orders ord, orderdetails ordz,products p, productimage i
                                            WHERE ord.userID = ? and ord.orderID = ordz.orderID
                                                    and p.productID = i.productID and p.productID = ordz.productID
                                            ORDER BY ord.orderID desc ");
            $stmt->bind_param("i", $userID);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_all(MYSQLI_ASSOC);
            $ords = [];
            foreach ($row as $item){
                if($offset > 0) {
                    $offset--;
                    continue;
                }elseif (count($ords) == $limit){
                    break;
                }
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
            $stmt = $this->conn->prepare("SELECT p.productID, p.name, i.img1, p.price, ordz.quantity, p.rating, p.sold, ordz.rating as 'customerRating'
                                            from orders o, orderdetails ordz, products p, productimage i
                                            where o.orderID = ? and o.userID = ? and ordz.orderID = o.orderID
                                                    and ordz.productID = p.productID and p.productID = i.productID");
            $stmt->bind_param("ii", $orderID, $userID);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows != 0)
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
            if ($result->num_rows != 0)
                return $result->fetch_assoc();
            else return false;
        }

        public function rateProduct($orderID, $productID, $rating){
            $stmt = $this->conn->prepare("UPDATE orderdetails
                                        set rating = ?
                                        where orderID = ? and productID = ?");
            $stmt->bind_param("dii", $rating, $orderID, $productID);
            $stmt->execute();
            if($stmt->affected_rows == 1) return true;
            else return false;
        }












        public static function getOrderSummary($conn, $sortBy = 'month', $interval = 5){
            $stmt = $conn->prepare("SELECT $sortBy(dateCreated) as $sortBy, COUNT(*) as 'orders'
                                    FROM orders
                                    WHERE dateCreated >= CURDATE() - INTERVAL ? $sortBy
                                    GROUP BY $sortBy(dateCreated)
                                    ORDER BY dateCreated ASC");

            $stmt->bind_param("i", $interval);
            $stmt->execute();
            $results = $stmt->get_result();
            return $results->fetch_all(MYSQLI_ASSOC);
        }

        public static function getIncomeSummary($conn, $sortBy = 'month', $interval = 5){
            $stmt = $conn->prepare("SELECT $sortBy(dateCreated) as $sortBy, SUM(totalPrice) as 'income'
                                    FROM orders
                                    WHERE dateCreated >= CURDATE() - INTERVAL ? $sortBy
                                    GROUP BY $sortBy(dateCreated)
                                    ORDER BY dateCreated ASC");

            $stmt->bind_param("i", $interval);
            $stmt->execute();
            $results = $stmt->get_result();
            return $results->fetch_all(MYSQLI_ASSOC);
        }

        public static function getOrderByOption($conn, $searchVal = "", $sortByStatus = "All", $getTotalOrder = true, $offset = 0, $limit = 10){
            $result = [];

            $searchVal = "%" . str_replace(' ', '%', $searchVal) . "%";

            $sqlOrderList ="SELECT
                                o.orderID as 'id',
                                o.name as 'cusName',
                                DATE_FORMAT(o.dateCreated, '%d/%m/%Y') as 'date',
                                o.totalPrice as 'price',
                                s.statusID as 'status'
                            FROM
                                orders o,
                                orderstatus s,
                                statusname n
                            WHERE
                                o.orderID = s.orderID and
                                s.statusID = n.statusID and
                                (o.orderID like ? or o.name like ?) and
                                s.updateDate = (SELECT MAX(s2.updateDate)
                                                FROM
                                                    orders o2,
                                                    orderstatus s2
                                                WHERE
                                                    o2.orderID = s2.orderID and
                                                    o.orderID = o2.orderID)";

            if($sortByStatus != "All")
                $sqlOrderList .= " and n.statusName = ?";

            $sqlOrderList .= " ORDER BY o.dateCreated DESC
                      LIMIT ?, ?";

            $stmtOrderList = $conn->prepare($sqlOrderList);

            if($sortByStatus != "All")
                $stmtOrderList->bind_param("sssii", $searchVal, $searchVal, $sortByStatus, $offset, $limit);
            else
                $stmtOrderList->bind_param("ssii", $searchVal, $searchVal, $offset, $limit);

            $stmtOrderList->execute();
            $resultOrderList = $stmtOrderList->get_result()->fetch_all(MYSQLI_ASSOC);

            $result['orderList'] = $resultOrderList;

            if($getTotalOrder == true) {
                $sqlTotalOrder="SELECT count(distinct(o.orderID)) as 'total'
                                FROM
                                    orders o,
                                    orderstatus s,
                                    statusname n
                                WHERE
                                    o.orderID = s.orderID and
                                    s.statusID = n.statusID";

                if($sortByStatus != "All")
                    $sqlTotalOrder .= " and n.statusName = ?";

                $stmtTotalOrder = $conn->prepare($sqlTotalOrder);

                if($sortByStatus != "All")
                    $stmtTotalOrder->bind_param("s", $sortByStatus);

                $stmtTotalOrder->execute();
                $resultTotalOrder = $stmtTotalOrder->get_result()->fetch_assoc();

                $result['total'] = $resultTotalOrder['total'];
            }

            return $result;
        }

        public static function getOrderByFilter2($conn, $searchVal= "", $sortByStatus = "All", $offset = 0, $limit = 10){
            $sqlOrderList ="SELECT
                                o.orderID as 'id',
                                o.name as 'cusName',
                                DATE_FORMAT(o.dateCreated, '%d/%m/%Y') as 'date',
                                o.totalPrice as 'price',
                                s.statusID as 'status'
                            FROM
                                orders o,
                                orderstatus s,
                                statusname n
                            WHERE
                                o.orderID = s.orderID and
                                s.statusID = n.statusID and
                                s.updateDate = (SELECT MAX(s2.updateDate)
                	                            FROM
                                                    orders o2,
                                                    orderstatus s2
                 	                            WHERE
                                                    o2.orderID = s2.orderID and
                                                    o.orderID = o2.orderID)";

            $sqlTotalOrder="SELECT count(distinct(o.orderID)) as 'total'
                            FROM
                                orders o,
                                orderstatus s,
                                statusname n
                            WHERE
                                o.orderID = s.orderID and
                                s.statusID = n.statusID";

            if($sortByStatus != "All") {
                $sqlOrderList .= " and n.statusName = ?";
                $sqlTotalOrder .= " and n.statusName = ?";
            }

            $sqlOrderList .= " ORDER BY o.dateCreated DESC
                               LIMIT ?, ?";

            $stmtOrderList = $conn->prepare($sqlOrderList);
            $stmtTotalOrder = $conn->prepare($sqlTotalOrder);

            if($sortByStatus != "All") {
                $stmtOrderList->bind_param("sii", $sortByStatus, $offset, $limit);
                $stmtTotalOrder->bind_param("s", $sortByStatus);
            }
            else
                $stmtOrderList->bind_param("ii", $offset, $limit);

            $stmtOrderList->execute();
            $resultOrderList = $stmtOrderList->get_result()->fetch_all(MYSQLI_ASSOC);

            $stmtTotalOrder->execute();
            $resultTotalOrder = $stmtTotalOrder->get_result()->fetch_assoc();

            $result = [];
            $result['total'] = $resultTotalOrder['total'];
            $result['orderList'] = $resultOrderList;

            return($result);
        }

        // public static function getOrderBySearch($conn, $search = "", $offset = 0, $limit = 10){
        //     $search = "%" . str_replace(' ', '%', $search) . "%";

        //     $sqlOrderList ="SELECT
        //                         o.orderID as 'id',
        //                         o.name as 'cusName',
        //                         DATE_FORMAT(o.dateCreated, '%d/%m/%Y') as 'date',
        //                         o.totalPrice as 'price',
        //                         s.statusID as 'status'
        //                     FROM
        //                         orders o,
        //                         orderstatus s,
        //                         statusname n
        //                     WHERE
        //                         o.orderID = s.orderID and
        //                         s.statusID = n.statusID and
        //                         (o.orderID like ? or o.name like ?) and
        //                         s.updateDate = (SELECT MAX(s2.updateDate)
        //                                         FROM
        //                                             orders o2,
        //                                             orderstatus s2
        //                                         WHERE
        //                                             o2.orderID = s2.orderID and
        //                                             o.orderID = o2.orderID)
        //                     ORDER BY o.dateCreated DESC
        //                     LIMIT ?, ?";

        //     $sqlTotalOrder = "SELECT count(distinct(o.orderID)) as 'total'
        //                       FROM orders
        //                       WHERE (orderID like ? or name like ?)";

        //     $stmtOrderList = $conn->prepare($sqlOrderList);
        //     $stmtTotalOrder = $conn->prepare($sqlTotalOrder);

        //     $stmtOrderList->bind_param("ssii",$search ,$search, $offset, $limit);
        //     $stmtTotalOrder->bind_param("ss",$search ,$search);

        //     $stmtOrderList->execute();
        //     $resultOrderList = $stmtOrderList->get_result()->fetch_all(MYSQLI_ASSOC);

        //     $stmtTotalOrder->execute();
        //     $resultTotalOrder = $stmtTotalOrder->get_result()->fetch_assoc();

        //     $result = [];
        //     $result['total'] = $resultTotalOrder['total'];
        //     $result['orderList'] = $resultOrderList;

        //     return($result);
        // }
    }

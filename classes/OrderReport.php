<?php
    class OrderReport {
        private $conn;

        public function __construct($conn){
            $this->conn = $conn;
        }


        public function getOrderSummary($sortBy = 'month', $interval = 5){
            $stmt = $this->conn->prepare("SELECT $sortBy(dateCreated) as $sortBy, COUNT(*) as 'orders'
                                    FROM orders
                                    WHERE dateCreated >= CURDATE() - INTERVAL ? $sortBy
                                    GROUP BY $sortBy(dateCreated)
                                    ORDER BY dateCreated ASC");

            $stmt->bind_param("i", $interval);
            $stmt->execute();
            $results = $stmt->get_result();
            return $results->fetch_all(MYSQLI_ASSOC);
        }


        public function getIncomeSummary($sortBy = 'month', $interval = 5){
            $stmt = $this->conn->prepare("SELECT $sortBy(dateCreated) as $sortBy, SUM(totalPrice) as 'income'
                                    FROM orders
                                    WHERE dateCreated >= CURDATE() - INTERVAL ? $sortBy
                                    GROUP BY $sortBy(dateCreated)
                                    ORDER BY dateCreated ASC");

            $stmt->bind_param("i", $interval);
            $stmt->execute();
            $results = $stmt->get_result();
            return $results->fetch_all(MYSQLI_ASSOC);
        }


        public function getOrderByOption($searchVal = "", $sortByStatus = "All", $getTotalOrder = true, $offset = 0, $limit = 10){
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

            $stmtOrderList = $this->conn->prepare($sqlOrderList);

            if($sortByStatus != "All")
                $stmtOrderList->bind_param("sssii", $searchVal, $searchVal, $sortByStatus, $offset, $limit);
            else
                $stmtOrderList->bind_param("ssii", $searchVal, $searchVal, $offset, $limit);

            $stmtOrderList->execute();
            $resultOrderList = $stmtOrderList->get_result()->fetch_all(MYSQLI_ASSOC);

            $result['orderList'] = $resultOrderList;

            if($getTotalOrder == true) {
                $sqlTotalOrder="SELECT count(o.orderID) as 'total'
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
                    $sqlTotalOrder .= " and n.statusName = ?";

                $stmtTotalOrder = $this->conn->prepare($sqlTotalOrder);

                if($sortByStatus != "All")
                    $stmtTotalOrder->bind_param("sss", $searchVal, $searchVal, $sortByStatus);
                else
                    $stmtTotalOrder->bind_param("ss", $searchVal, $searchVal);

                $stmtTotalOrder->execute();
                $resultTotalOrder = $stmtTotalOrder->get_result()->fetch_assoc();

                $result['total'] = $resultTotalOrder['total'];
            }

            return $result;
        }


        public function updateStatus($orderID, $statusID){
            $stmt1 = $this->conn->prepare('INSERT INTO orderstatus (orderID, statusID)
                                           VALUES (?, ?);');
            $stmt1->bind_param('ii', $orderID, $statusID);
            $stmt1->execute();
            if($stmt1->affected_rows == 1) return true;
            else return false;
        }
    }
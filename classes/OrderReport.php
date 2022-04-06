<?php
    class OrderReport {
        private $conn;

        public function __construct($conn){
            $this->conn = $conn;
        }

        private function getOption($sortBy){
            $res = [];
            switch ($sortBy) {
                case 'Day':
                    $res['format'] = "'%d/%m'";
                    $res['interval'] = 7;
                    break;

                case 'Month':
                    $res['format'] = "'%b'";
                    $res['interval'] = 6;
                    break;

                case 'Year':
                    $res['format'] = "'%Y'";
                    $res['interval'] = 2;
                    break;
            }
            return $res;
        }

        private function substitution($char, $type) {
            $plain = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
            $cipher = ['5', 'T', '7', 'I', 'Z', '9', 'M', 'A', 'E', '4'];

            if($type == 'enc') {
                $index = array_search($char, $plain);
                return $cipher[$index];
            }

            if($type == 'dec') {
                $index = array_search($char, $cipher);
                return $plain[$index];
            }
        }

        private function encrypt($orderID) {
            $encryptedID = "";
            $chars = str_split($orderID);

            foreach ($chars as $char) {
                $encryptedID .= $this->substitution($char, 'enc');
            }

            return $encryptedID;
        }

        private function decrypt($encryptedID) {
            $orderID = "";
            $chars = str_split($encryptedID);

            foreach ($chars as $char) {
                $orderID .= $this->substitution($char, 'dec');
            }

            return $orderID;
        }

        public function getOrderSummary($sortBy = 'Month'){
            $option = $this->getOption($sortBy);
            $format = $option['format'];
            $interval = $option['interval'];

            $sql = "";

            for ($dateBackward = $interval - 1; $dateBackward >= 0; $dateBackward--) {
                $keyFormat = "date(now() - interval $dateBackward $sortBy)";

                $sql .= "SELECT date_format($keyFormat , $format) as 'key', ifnull(count(*), 0) as 'orders'
                         FROM orders
                         WHERE   ";

                if ($sortBy == 'Day')
                    $sql .= "date(dateCreated) = date(now() - interval $dateBackward Day)";

                else {
                    if($sortBy == 'Month')
                        $firstFormat = "'%Y-%m-01'";
                    else
                        $firstFormat = "'%Y-01-01'";

                    if ($dateBackward == 0)
                        $sql .="(dateCreated between date_format(now() , $firstFormat) and now())";
                    else {
                        $prev = $dateBackward - 1;
                        $sql .="(dateCreated between
                                 date_format(now() - interval $dateBackward $sortBy ,$firstFormat) and
                                 date_format(now() - interval $prev $sortBy , $firstFormat))";
                    }
                }

                if ($dateBackward > 0)
                    $sql .= "
                            UNION
                            ";
            }

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $results = $stmt->get_result();
            return $results->fetch_all(MYSQLI_ASSOC);
        }

        public function getIncomeSummary($sortBy = 'month'){
            $option = $this->getOption($sortBy);
            $format = $option['format'];
            $interval = $option['interval'];

            $sql = "";

            for ($dateBackward = $interval - 1; $dateBackward >= 0; $dateBackward--) {
                $keyFormat = "date(now() - interval $dateBackward $sortBy)";

                $sql .= "SELECT date_format($keyFormat , $format) as 'key', ifnull(sum(o.totalPrice), 0) as 'income'
                         FROM orders o, orderstatus s
                         WHERE o.orderID = s.orderID and s.statusID = 4 and   ";

                if ($sortBy == 'Day')
                    $sql .= "date(o.dateCreated) = date(now() - interval $dateBackward Day)";

                else {
                    if($sortBy == 'Month')
                        $firstFormat = "'%Y-%m-01'";
                    else
                        $firstFormat = "'%Y-01-01'";

                    if ($dateBackward == 0)
                        $sql .="(o.dateCreated between date_format(now() , $firstFormat) and now())";
                    else {
                        $prev = $dateBackward - 1;
                        $sql .="(o.dateCreated between
                                 date_format(now() - interval $dateBackward $sortBy ,$firstFormat) and
                                 date_format(now() - interval $prev $sortBy , $firstFormat))";
                    }
                }

                if ($dateBackward > 0)
                    $sql .= "
                            UNION
                            ";
            }

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $results = $stmt->get_result();
            return $results->fetch_all(MYSQLI_ASSOC);
        }

        public function getOrderByOption($searchVal = "", $sortByStatus = "All", $getTotalOrder = true, $offset = 0, $limit = 10){
            $result = [];

            $decryptedSearch = $this->decrypt($searchVal);

            $searchVal = "%" . $searchVal . "%";
            $decryptedSearch = "%" .  $decryptedSearch . "%";

            // echo($searchVal);
            // echo("\n\n");
            // echo($decryptedSearch);

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

            if ($sortByStatus != "All")
                $sqlOrderList .= " and n.statusName = ?";

            $sqlOrderList .= " ORDER BY o.dateCreated DESC
                      LIMIT ?, ?";

            $stmtOrderList = $this->conn->prepare($sqlOrderList);

            if ($sortByStatus != "All")
                $stmtOrderList->bind_param("sssii", $decryptedSearch, $searchVal, $sortByStatus, $offset, $limit);
            else
                $stmtOrderList->bind_param("ssii", $decryptedSearch, $searchVal, $offset, $limit);

            $stmtOrderList->execute();
            $resultOrderList = $stmtOrderList->get_result()->fetch_all(MYSQLI_ASSOC);

            $result['orderList'] = $resultOrderList;

            if ($getTotalOrder == true) {
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

                if ($sortByStatus != "All")
                    $sqlTotalOrder .= " and n.statusName = ?";

                $stmtTotalOrder = $this->conn->prepare($sqlTotalOrder);

                if ($sortByStatus != "All")
                    $stmtTotalOrder->bind_param("sss", $decryptedSearch, $searchVal, $sortByStatus);
                else
                    $stmtTotalOrder->bind_param("ss", $decryptedSearch, $searchVal);

                $stmtTotalOrder->execute();
                $resultTotalOrder = $stmtTotalOrder->get_result()->fetch_assoc();

                $result['total'] = $resultTotalOrder['total'];
            }

            foreach ($result['orderList'] as &$order) {
                $order['id'] = $this->encrypt($order['id']);
            }

            return $result;
        }

        public function updateStatus($orderID, $statusID){
            $decryptedOrderID = $this->decrypt($orderID);

            $stmt1 = $this->conn->prepare('INSERT INTO orderstatus (orderID, statusID)
                                           VALUES (?, ?);');
            $stmt1->bind_param('ii', $decryptedOrderID, $statusID);
            $stmt1->execute();

            if ($stmt1->affected_rows == 1)
                return true;
            else
                return false;
        }
    }
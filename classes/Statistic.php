<?php
class Statistic
{

    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function updateUserVisit($userID, $time)
    {
        $stmt = $this->conn->prepare("INSERT INTO uservisit VALUES (?,?)");
        $stmt->bind_param("is", $userID, $time);


        $stmt->execute();
    }

    public function updateProductView($productID, $time)
    {
        $stmt = $this->conn->prepare("INSERT INTO productview VALUES (?,?)");
        $stmt->bind_param("is", $productID, $time);


        $stmt->execute();
    }

    public function getTotalOrderData()
    {
        $stmt = $this->conn->prepare("SELECT COUNT(orderID) as 'totalOrders', 
        SUM(totalPrice) as 'totalSales' 
        FROM orders");

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows != 0)
            return $result->fetch_assoc();
    }

    public function getOrderDataByTime($filter)
    {

        $where_clauseA = "YEAR(dateCreated) = YEAR(CURDATE())";
        $where_clauseB = "YEAR(dateCreated) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 $filter))";

        $filter = strtoupper($filter);


        if ($filter == "MONTH" || $filter == "DAY") {
            $where_clauseA .= " AND MONTH(dateCreated) = MONTH(CURDATE())";
            $where_clauseB .= " AND MONTH(dateCreated) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 $filter))";
        }
        if ($filter == "DAY") {
            $where_clauseA .= " AND DAY(dateCreated) = DAY(CURDATE())";
            $where_clauseB .= " AND DAY(dateCreated) = DAY(DATE_SUB(CURDATE(), INTERVAL 1 $filter))";
        }


        $stmt = $this->conn->prepare(
            "SELECT COUNT(DISTINCT orders.orderID) as 'curOrders', SUM(price * quantity) as 'curSales', SUM(quantity) as 'curItems' 
        FROM orders, orderdetails 
        WHERE $where_clauseA" . " AND orders.orderID = orderdetails.orderID"
        );

        $stmt->execute();


        $resultA = $stmt->get_result();


        $stmt2 = $this->conn->prepare("SELECT COUNT(DISTINCT orders.orderID) as 'pastOrders', SUM(price * quantity) as 'pastSales', SUM(quantity) as 'pastItems' 
        FROM orders, orderdetails 
        WHERE $where_clauseB" . " AND orders.orderID = orderdetails.orderID");


        $stmt2->execute();
        $resultB = $stmt2->get_result();

        $assoc = $resultA->fetch_assoc() + $resultB->fetch_assoc();

        if ($assoc['curSales'] == null) $assoc['curSales'] = 0;
        if ($assoc['pastSales'] == null) $assoc['pastSales'] = 0;

        if ($assoc['curItems'] == null) $assoc['curItems'] = 0;
        if ($assoc['pastItems'] == null) $assoc['pastItems'] = 0;

        $object = [];

        $object['sale'] = array(
            'current' => (int)$assoc['curSales'],
            'past' => (int)$assoc['pastSales'],
        );

        $object['order'] = array(
            'current' =>(int) $assoc['curOrders'],
            'past' => (int)$assoc['pastOrders']
        );


        $object['item'] = array(
            'current' => (int)$assoc['curItems'],
            'past' => (int)$assoc['pastItems']
        );

        return $object;
    }

    public function getTotalAccountNum()
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as 'totalUsers'
        FROM users");

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows != 0)
            return $result->fetch_assoc();
    }

    public function getVisitByTime($filter)
    {
        $where_clauseA = "YEAR(time) = YEAR(CURDATE())";
        $where_clauseB = "YEAR(time) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 $filter))";

        $filter = strtoupper($filter);


        if (strcmp($filter, "MONTH") == 0 || strcmp($filter, "DAY") == 0) {
            $where_clauseA .= " AND MONTH(`time`) = MONTH(CURDATE())";
            $where_clauseB .= " AND MONTH(`time`) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 $filter))";
        }
        if (strcmp($filter, "DAY") == 0) {
            $where_clauseA .= " AND DAY(`time`) = DAY(CURDATE())";
            $where_clauseB .= " AND DAY(`time`) = DAY(DATE_SUB(CURDATE(), INTERVAL 1 $filter))";
        }


        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) as 'current'
        FROM uservisit 
        WHERE $where_clauseA"
        );


        $stmt->execute();

        $resultA = $stmt->get_result();


        $stmt2 = $this->conn->prepare(
            "SELECT COUNT(*) as 'past'
        FROM uservisit 
        WHERE $where_clauseB"
        );


        $stmt2->execute();
        $resultB = $stmt2->get_result();

        $assoc = $resultA->fetch_assoc() + $resultB->fetch_assoc();

        return $assoc;
    }
}

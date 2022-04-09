<?php
class Statistic
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getSaleInTime($currentInterval)
    {
        $stmt = $this->conn->prepare("SELECT count(orders.orderID) count FROM orders , orderstatus where orderstatus.orderID = orders.orderID and orderstatus.statusID = 4 and orders.dateCreated >= {$currentInterval}");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_all(MYSQLI_ASSOC);
        return $row;
    }

    public function getItemOnSale($currentInterval)
    {
        $stmt = $this->conn->prepare("SELECT count(DISTINCT(orderdetails.productID)) count from orders, orderdetails , orderstatus where orderstatus.orderID = orders.orderID and orderstatus.statusID = 4 and orders.orderID = orderdetails.orderID and orders.dateCreated >= {$currentInterval}");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_all(MYSQLI_ASSOC);
        return $row;
    }
    public function getTopRevenue($currentInterval)
    {
        $stmt = $this->conn->prepare("SELECT products.productID, (SELECT productimage.img1 FROM productimage where productimage.productID = products.productID) productimg, products.name, products.price, ifnull(sum(orderdetails.quantity) * products.price,0) as revenue 
        FROM products LEFT JOIN (orderdetails inner JOIN (orders inner Join orderstatus on orderstatus.orderID = orders.orderID and orderstatus.statusID = 4)  on  orders.orderID = orderdetails.orderID and orders.dateCreated >= {$currentInterval}) on products.productID = orderdetails.productID GROUP by products.productID, products.name, products.price ORDER by revenue DESC LIMIT 5;");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_all(MYSQLI_ASSOC);
        return $row;
    }

    public function getBestSeller($currentInterval, $previousInterval)
    {
        $stmt = $this->conn->prepare("SELECT products.productID,(SELECT productimage.img1 FROM productimage where productimage.productID = products.productID) productimg, products.name, products.price, ifnull(sum(orderdetails.quantity),0) unit, IFNULL(((sum(orderdetails.quantity)) - (SELECT sum(od.quantity) FROM orderdetails od, orders , orderstatus where orderstatus.orderID = orders.orderID and orderstatus.statusID = 4 and  orders.orderID = od.orderID and orders.dateCreated >= {$previousInterval} and orders.dateCreated < {$currentInterval} and od.productID = orderdetails.productID)) / (SELECT  sum(od.quantity) FROM orderdetails od, orders , orderstatus where orderstatus.orderID = orders.orderID and orderstatus.statusID = 4 and  orders.orderID = od.orderID and orders.dateCreated >= {$previousInterval} and orders.dateCreated < {$currentInterval} and od.productID = orderdetails.productID) * 100,100) as up 
        FROM products LEFT JOIN (orderdetails inner JOIN (orders inner Join orderstatus on orderstatus.orderID = orders.orderID and orderstatus.statusID = 4) on  orders.orderID = orderdetails.orderID and orders.dateCreated >= {$currentInterval}) on products.productID = orderdetails.productID GROUP by products.productID, products.name, products.price ORDER by unit DESC limit 1;
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_all(MYSQLI_ASSOC);
        return $row;
    }
    public function getMostViewed($currentInterval, $previousInterval)
    {
        $stmt = $this->conn->prepare("SELECT products.productID, (SELECT productimage.img1 FROM productimage where productimage.productID = products.productID) productimg, products.name,COUNT(productview.productID) view , IFNULL(((COUNT(productview.productID) - (SELECT count(productID) FROM productview where productID = products.productID and productview.datetime >= {$previousInterval} and productview.datetime < {$currentInterval})) / (SELECT count(productID) FROM productview where productID = products.productID and productview.datetime >= {$previousInterval} and productview.datetime < {$currentInterval}) * 100),100) as up FROM products LEFT JOIN productview on products.productID = productview.productID and productview.datetime >= {$currentInterval} GROUP by products.productID,products.name ORDER by view DESC limit 1 ");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_all(MYSQLI_ASSOC);
        return $row;
    }
    public function getMostProfitableCate($currentInterval, $previousInterval)
    {
        $stmt = $this->conn->prepare("SELECT p.type, IFNULL(total,0) total, dense_rank() OVER ( ORDER BY total DESC ) AS rank, ((
            select DISTINCT(rank) from (
                SELECT p.type, IFNULL(total,0) total, dense_rank() OVER ( ORDER BY total DESC ) AS rank FROM products p LEFT JOIN (SELECT products.type type, sum(products.price) total FROM orders, orderdetails, products , orderstatus where orderstatus.orderID = orders.orderID and orderstatus.statusID = 4 and  orders.dateCreated >= {$previousInterval} and orders.dateCreated < {$currentInterval} and orders.orderID = orderdetails.orderID and products.productID = orderdetails.productID GROUP by products.type ORDER BY `total` DESC) a on p.type = a.type) pa where pa.type = p.type) - dense_rank() OVER ( ORDER BY total DESC )) as up     
        FROM products p LEFT JOIN (SELECT products.type type, sum(products.price) total FROM orders, orderdetails, products , orderstatus where orderstatus.orderID = orders.orderID and orderstatus.statusID = 4 and  orders.dateCreated >= {$currentInterval} and orders.orderID = orderdetails.orderID and products.productID = orderdetails.productID GROUP by products.type ORDER BY `total` DESC) a on p.type = a.type GROUP BY p.type ORDER BY  rank limit 5; ");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_all(MYSQLI_ASSOC);
        return $row;
    }
    public function getIncomeLineChart($sortby)
    {
        $defaultcol = 6;
        if ($sortby == 'year')
        {
            $defaultcol = 2;
        }
        $res = [];
        while ($defaultcol > 0)
        {
            $defaultcol = $defaultcol - 1;
            $date = "DATE(now() - INTERVAL {$defaultcol} {$sortby})";
            if ($sortby == 'day')
                $stmt = $this->conn->prepare("select DATE_FORMAT({$date}, '%d/%m')  month, ifnull(sum(orders.totalPrice),0) income  FROM orders , orderstatus where orderstatus.orderID = orders.orderID and orderstatus.statusID = 4 and  date(orderstatus.updateDate) = {$date};");
            elseif ($sortby == 'month')
                $stmt = $this->conn->prepare("select LEFT(monthname({$date}),3) month, ifnull(sum(orders.totalPrice),0) income  FROM orders , orderstatus where orderstatus.orderID = orders.orderID and orderstatus.statusID = 4 and  {$sortby}(orderstatus.updateDate) = {$sortby}({$date}) and year(orderstatus.updateDate) = year({$date});");
            elseif ($sortby == 'year')
                $stmt = $this->conn->prepare("select year({$date}) month, ifnull(sum(orders.totalPrice),0) income  FROM orders , orderstatus where orderstatus.orderID = orders.orderID and orderstatus.statusID = 4 and  {$sortby}(orderstatus.updateDate) = {$sortby}({$date});");
            $stmt->execute();
            $result = $stmt->get_result();
            array_push($res, $result->fetch_all(MYSQLI_ASSOC)[0]);
        }
        return $res;
    }
}

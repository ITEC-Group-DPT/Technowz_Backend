<?php

class CustomerStatistic
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getVisitedUsers($time)
    {
        $sortby = strtolower($time);

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
                $stmt = $this->conn->prepare("select DATE_FORMAT(DATE(now() - INTERVAL {$defaultcol} DAY), '%d/%m')  day, ifnull(count(uservisit.userID),0) guests FROM uservisit where uservisit.userID = -1 and date(uservisit.time) = DATE(now() - INTERVAL {$defaultcol} DAY);");
            elseif ($sortby == 'month')
                $stmt = $this->conn->prepare("select LEFT(monthname({$date}),3) month, ifnull(count(uservisit.userID),0) guests FROM uservisit
                where uservisit.userID = -1 and {$sortby}(uservisit.time) = {$sortby}({$date}) and year(uservisit.time) = year({$date});");
            elseif ($sortby == 'year')
                $stmt = $this->conn->prepare("select year({$date}) year, ifnull(count(uservisit.userID),0) guests FROM uservisit
                where uservisit.userID = -1 and  {$sortby}(uservisit.time) = {$sortby}({$date});");
            $stmt->execute();
            $result = $stmt->get_result();
            array_push($res, $result->fetch_all(MYSQLI_ASSOC)[0]);
        }
        $res1 = [];
        $res1['isSuccess'] = true;
        $res1['data'] = $res;
        return $res1;
    }

    public function getActiveUsers($time)
    {
        $time = strtolower($time);

        $sortby = strtolower($time);

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
                $stmt = $this->conn->prepare("select DATE_FORMAT(DATE(now() - INTERVAL {$defaultcol} DAY), '%d/%m') day, ifnull(count(users.userID),0) users FROM users where date(users.dateCreated) = DATE(now() - INTERVAL {$defaultcol} DAY);");
            elseif ($sortby == 'month')
                $stmt = $this->conn->prepare("select LEFT(monthname({$date}),3) month, ifnull(count(users.userID),0) users FROM users WHERE {$sortby}(users.dateCreated) = {$sortby}({$date}) and year(users.dateCreated) = year({$date});");
            elseif ($sortby == 'year')
                $stmt = $this->conn->prepare("select year({$date}) year, ifnull(count(users.userID),0) users FROM users
                where {$sortby}(users.dateCreated) = {$sortby}({$date});");
            $stmt->execute();
            $result = $stmt->get_result();
            array_push($res, $result->fetch_all(MYSQLI_ASSOC)[0]);
        }
        $res1 = [];
        $res1['isSuccess'] = true;
        $res1['data'] = $res;
        return $res1;
    }

    public function getLeaderBoardData($limit = null, $time = 'month')
    {
        $time = strtolower($time);
        $str = ($limit != null) ? " LIMIT {$limit} " : "";
        if ($time == 'month')
        {
            $stmt = $this->conn->prepare("SELECT ROW_NUMBER() OVER (ORDER BY sum(o.totalPrice) DESC) AS rank, u.username, ifnull(sum(o.totalPrice),0) AS purchasedAmount FROM users u LEFT JOIN orders o ON u.userID = o.userID
            and MONTH(o.dateCreated) = MONTH(NOW()) AND YEAR(o.dateCreated) = YEAR(NOW())
            GROUP BY u.userID ORDER BY sum(o.totalPrice) DESC {$str}");
        }
        else if ($time == 'year')
        {
            $stmt = $this->conn->prepare("SELECT ROW_NUMBER() OVER (ORDER BY sum(o.totalPrice) DESC) AS rank, u.username,  ifnull(sum(o.totalPrice),0)  AS purchasedAmount FROM users u LEFT JOIN orders o ON u.userID = o.userID
            and YEAR(o.dateCreated) = YEAR(NOW())
            GROUP BY u.userID ORDER BY sum(o.totalPrice) DESC  {$str}");
        }
        else if ($time == 'day')
        {
            $stmt = $this->conn->prepare("SELECT ROW_NUMBER() OVER (ORDER BY sum(o.totalPrice) DESC) AS rank, u.username, ifnull(sum(o.totalPrice),0)  AS purchasedAmount FROM users u LEFT JOIN orders o ON u.userID = o.userID
            and DATE(o.dateCreated) = DATE(NOW())
            GROUP BY u.userID ORDER BY sum(o.totalPrice) DESC  {$str}");
        }
        $stmt->execute();
        $result = $stmt->get_result();

        $arr['isSuccess'] = true;
        $arr['data'] = $result->fetch_all(MYSQLI_ASSOC);
        return $arr;
    }

    public function getChartsData($time)
    {
        $activeData = $this->getActiveUsers($time);
        $visitedData = $this->getVisitedUsers($time);
        $arr = [];
        $arr['isSuccess'] = true;
        $arr['data']['active'] = $activeData['data'];
        $arr['data']['visited'] = $visitedData['data'];
        return $arr;
    }


    public function updateUserVisit($userID)
    {
        $stmt = $this->conn->prepare("INSERT INTO uservisit (userID) VALUES (?)");
        $stmt->bind_param("i", $userID);


        $stmt->execute();

        return $stmt->affected_rows == 1;
    }
}
?>
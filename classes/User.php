<?php
class User
{
    private $conn;
    private $userID;
    private $email;
    private $username;
    private $password;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function checkSignUp($email, $username, $password)
    {
        if ($this->getUser("email", $email) == false) {
            $this->email = $email;
            $this->username = $username;
            $this->password = password_hash($password, PASSWORD_DEFAULT);
            return true;
        } else return false;
    }

    public function checkSignIn($email, $password, $isAdmin = false)
    {
        $res = [];
        $res['isSuccess'] = false;

        if ($this->getUser("email", $email) != false) {

            $row = $this->getUser("email", $email);


            if ($isAdmin && ($row['userRole'] == 1)) {
                $res['data']['errorEmail'] = "Email not found";
                return $res;
            }
            if (password_verify($password, $row['password'])) {
                $arr = [];
                $arr['userID'] = $row['userID'];
                $arr['username'] = $row['username'];
                $arr['userRole'] = $row['userRole'];
                $res['isSuccess'] = true;
                $res['data'] = $arr;
            } else $res['data']['errorPassword'] = "Password is incorrect";
        } else $res['data']['errorEmail'] = "Email not found";
        return $res;
    }

    public function createUser()
    {
        $stmt = $this->conn->prepare("INSERT INTO users (email, username, password) VALUES (?,?,?)");
        $stmt->bind_param("sss", $this->email, $this->username, $this->password);
        $stmt->execute();
        if ($stmt->affected_rows == 1) {
            $this->userID = $stmt->insert_id;
            $this->alterCartTable();
            $arr = [];
            $arr['userID'] = $this->userID;
            $arr['username'] = $this->username;
            return $arr;
        }
    }

    public function alterCartTable()
    {
        $stmt = $this->conn->prepare("INSERT INTO carts (userID) values (?)");
        $stmt->bind_param("i", $this->userID);
        $stmt->execute();
    }

    // public function recordSignIn($userID){
    //     $stmt = $this->conn->prepare("INSERT INTO uservisit (userID) values (?)");
    //     $stmt->bind_param("i", $userID);
    //     $stmt->execute();
    //     $result = $stmt->get_result();
    //     if ($result->num_rows == 1) return true;
    //     else return false;
    // }

    public function getVisitedUsers($time)
    {
        if ($time == 'month') {
            $stmt = $this->conn->prepare("SELECT MONTH(time) AS month, COUNT(time) AS guests FROM uservisit WHERE MONTH(time) >= MONTH(NOW() - INTERVAL 3 MONTH) AND MONTH(time) <= MONTH(NOW()) AND userID = -1 GROUP BY MONTH(time) ORDER BY MONTH(time) ASC");
        } else if ($time == 'week') {
            $stmt = $this->conn->prepare("SELECT WEEK(uv.time) AS week, COUNT(uv.time) AS guests FROM uservisit uv WHERE uv.userID = -1 GROUP BY WEEK(uv.time)");
        } else if ($time == 'year') {
            $stmt = $this->conn->prepare("SELECT YEAR(time) AS year, COUNT(userID) AS guests FROM uservisit WHERE YEAR(time) >= YEAR(NOW() - INTERVAL 1 YEAR) AND YEAR(time) <= YEAR(NOW()) AND userID = -1 GROUP BY YEAR(time) ORDER BY YEAR(time)");
        } else {
            $stmt = $this->conn->prepare("SELECT T.days AS day, COALESCE(X.guests,0) AS guests FROM (SELECT 1 days UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10 UNION SELECT 11 UNION SELECT 12 UNION SELECT 13 UNION SELECT 14 UNION SELECT 15 UNION SELECT 16 UNION SELECT 17 UNION SELECT 18 UNION SELECT 19 UNION SELECT 20 UNION SELECT 21 UNION SELECT 22 UNION SELECT 23 UNION SELECT 24 UNION SELECT 25 UNION SELECT 26 UNION SELECT 27 UNION SELECT 28 UNION SELECT 29 UNION SELECT 30 UNION SELECT 31) T 
                LEFT JOIN (SELECT DAY(time) AS days, COUNT(time) AS guests
                FROM uservisit 
                WHERE time BETWEEN DATE_SUB(NOW(), INTERVAL 6 DAY) AND NOW() AND userID = -1
                GROUP BY DAY(time)) X ON T.days = X.days
                WHERE T.days BETWEEN DAY(DATE_SUB(NOW(), INTERVAL 6 DAY)) AND DAY(NOW())");
        }
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows != 0) {
            $arr = [];
            $arr['isSuccess'] = true;
            $arr['data'] = $result->fetch_all(MYSQLI_ASSOC);
            return $arr;
        } else return false;
    }

    public function getActiveUsers($time)
    {
        if ($time == 'month') {
            $stmt = $this->conn->prepare("SELECT T.months AS month, COALESCE(X.users,0) AS users
                FROM (SELECT 1 months UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10 UNION SELECT 11 UNION SELECT 12) T 
                LEFT JOIN (SELECT MONTH(dateCreated) AS month, COUNT(userID) AS users FROM users
                WHERE MONTH(dateCreated) >= MONTH(NOW() - INTERVAL 3 MONTH) AND MONTH(dateCreated) <= MONTH(NOW()) 
                GROUP BY MONTH(dateCreated) ORDER BY MONTH(dateCreated) ASC) X ON T.months = X.month
                WHERE T.months >= MONTH(NOW() - INTERVAL 3 MONTH) AND T.months <= MONTH(NOW())");
        } else if ($time == 'week') {
            $stmt = $this->conn->prepare("SELECT WEEK(dateCreated) AS week, COUNT(userID) AS users FROM users GROUP BY WEEK(dateCreated)");
        } else if ($time == 'year') {
            $stmt = $this->conn->prepare("SELECT YEAR(dateCreated) AS year, COUNT(userID) AS users FROM users GROUP BY YEAR(dateCreated)");
        } else {
            $stmt = $this->conn->prepare("SELECT T.days AS day, COALESCE(X.users,0) AS users 
                FROM (SELECT 1 days UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10 UNION SELECT 11 UNION SELECT 12 UNION SELECT 13 UNION SELECT 14 UNION SELECT 15 UNION SELECT 16 UNION SELECT 17 UNION SELECT 18 UNION SELECT 19 UNION SELECT 20 UNION SELECT 21 UNION SELECT 22 UNION SELECT 23 UNION SELECT 24 UNION SELECT 25 UNION SELECT 26 UNION SELECT 27 UNION SELECT 28 UNION SELECT 29 UNION SELECT 30 UNION SELECT 31) T 
                LEFT JOIN (SELECT DAY(dateCreated) AS days, COUNT(userID) AS users
                           FROM users
                           WHERE dateCreated BETWEEN DATE_SUB(NOW(), INTERVAL 6 DAY) AND NOW()
                           GROUP BY DAY(dateCreated)) X ON T.days = X.days
                           WHERE T.days BETWEEN DAY(DATE_SUB(NOW(), INTERVAL 6 DAY)) AND DAY(NOW())");
        }
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows != 0) {
            $arr = [];
            $arr['isSuccess'] = true;
            $arr['data'] = $result->fetch_all(MYSQLI_ASSOC);
            return $arr;
        } else return false;
    }

    public function getLeaderBoardData($limit = null,$time='month')
    {
        $str = ($limit != null) ? " LIMIT {$limit} ": "";
        if($time == 'month'){
            $stmt = $this->conn->prepare("SELECT ROW_NUMBER() OVER (ORDER BY sum(o.totalPrice) DESC) AS rank, u.username, sum(o.totalPrice) AS purchasedAmount FROM users u LEFT JOIN orders o ON u.userID = o.userID
            WHERE MONTH(o.dateCreated) = MONTH(NOW()) AND YEAR(o.dateCreated) = YEAR(NOW())
            GROUP BY u.userID ORDER BY sum(o.totalPrice) DESC {$str}");
        }else if($time == 'year'){
            $stmt = $this->conn->prepare("SELECT ROW_NUMBER() OVER (ORDER BY sum(o.totalPrice) DESC) AS rank, u.username, sum(o.totalPrice) AS purchasedAmount FROM users u LEFT JOIN orders o ON u.userID = o.userID
            WHERE YEAR(o.dateCreated) = YEAR(NOW())
            GROUP BY u.userID ORDER BY sum(o.totalPrice) DESC  {$str}");
        }else if($time == 'day'){
            $stmt = $this->conn->prepare("SELECT ROW_NUMBER() OVER (ORDER BY sum(o.totalPrice) DESC) AS rank, u.username, sum(o.totalPrice) AS purchasedAmount FROM users u LEFT JOIN orders o ON u.userID = o.userID
            WHERE DATE(o.dateCreated) = DATE(NOW())
            GROUP BY u.userID ORDER BY sum(o.totalPrice) DESC  {$str}");
        }
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows != 0) {
            $arr = [];
            $arr['isSuccess'] = true;
            $arr['data'] = $result->fetch_all(MYSQLI_ASSOC);
            return $arr;
        } else return false;
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
    public function getUser($type, $data)
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE $type = ?");
        $stmt->bind_param("s", $data);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 1) return $result->fetch_assoc();
        else return false;
    }


    public function verifyAdmin($userID)
    {
        $stmt = $this->conn->prepare("SELECT userRole FROM users where userID = ?");
        $stmt->bind_param("i", $userID);

        $stmt->execute();

        $result = $stmt->get_result();

        $object = $result->fetch_assoc();

        return $object['userRole'] == 0;
    }
}

<?php
    class User{
        private $conn;
        private $userID;
        private $email;
        private $username;
        private $password;

        public function __construct($conn){
            $this->conn = $conn;
        }

        public function checkSignUp($email, $username, $password){
            if ($this->getUser("email", $email) == false){
                $this->email = $email;
                $this->username = $username;
                $this->password = password_hash($password, PASSWORD_DEFAULT);
                return true;
            }
            else return false;
        }

        public function checkSignIn($email, $password){
            $res = [];
            $res['isSuccess'] = false;
            if ($this->getUser("email", $email) != false) {
                $row = $this->getUser("email", $email);
                if (password_verify($password, $row['password'])) {
                    $arr = [];
                    $arr['userID'] = $row['userID'];
                    $arr['username'] = $row['username'];
                    $arr['userRole'] = $row['userRole'];
                    $res['isSuccess'] = true;
                    $res['data'] = $arr;
                    // $this->recordSignIn($row['userID']);
                }
                else $res['data']['errorPassword'] = "Password is incorrect";
            }
            else $res['data']['errorEmail'] = "Email not found";
            return $res;
        }

        public function createUser(){
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

        public function getUser($type, $data){
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE $type = ?");
            $stmt->bind_param("s", $data);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows == 1) return $result->fetch_assoc();
            else return false;
        }

        public function alterCartTable(){
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
    
        public function getVisitedUsers($time){
            if($time == 'month'){
                $stmt = $this->conn->prepare("SELECT MONTH(uv.time), COUNT(uv.time) FROM uservisit uv WHERE uv.userID = -1 GROUP BY MONTH(uv.time)");
            }else if($time == 'week'){
                $stmt = $this->conn->prepare("SELECT WEEK(uv.time), COUNT(uv.time) FROM uservisit uv WHERE uv.userID = -1 GROUP BY WEEK(uv.time))");
            }else if($time == 'year'){
                $stmt = $this->conn->prepare("SELECT YEAR(uv.time), COUNT(uv.time) FROM uservisit uv WHERE uv.userID = -1 GROUP BY YEAR(uv.time)");
            }else{
                $stmt = $this->conn->prepare("SELECT DAY(uv.time), COUNT(uv.time) FROM uservisit uv WHERE uv.userID = -1 GROUP BY DAY(uv.time)");
            }
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows != 0){
                $arr = [];
                $arr['isSuccess'] = true;
                $arr['data'] = $result->fetch_all();
                return $arr;
            }
            else return false;
        }

        public function getActiveUsers($time){
            if($time == 'month'){
                $stmt = $this->conn->prepare("SELECT MONTH(dateCreated), COUNT(userID) FROM users GROUP BY MONTH(dateCreated))");
            }else if($time == 'week'){
                $stmt = $this->conn->prepare("SELECT WEEK(dateCreated), COUNT(userID) FROM users GROUP BY WEEK(dateCreated)");
            }else if($time == 'year'){
                $stmt = $this->conn->prepare("SELECT YEAR(dateCreated), COUNT(userID) FROM users GROUP BY YEAR(dateCreated)");
            }else{
                $stmt = $this->conn->prepare("SELECT DAY(dateCreated), COUNT(userID) FROM users GROUP BY DAY(dateCreated))");
            }
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows != 0){
                $arr = [];
                $arr['isSuccess'] = true;
                $arr['data'] = $result->fetch_all();
                return $arr;
            }
            else return false;
        }

        public function getLeaderBoardData(){
            $res = [];
            $res['isSuccess'] = false;
            $stmt = $this->conn->prepare("SELECT u.userID, u.username, sum(o.totalPrice) AS purchasedAmount FROM users u LEFT JOIN orders o ON u.userID = o.userID GROUP BY u.userID ORDER BY sum(o.totalPrice) DESC");
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows != 0){
                $arr = [];
                $arr['isSuccess'] = true;
                $arr['data'] = $result->fetch_all(MYSQLI_ASSOC);
                return $arr;
            }
            else return false;
        }
    }

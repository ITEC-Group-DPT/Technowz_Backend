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
            if ($this->getUser("email", $email) != false) return false;
            else {
                $this->email = $email;
                $this->username = $username;
                $this->password = password_hash($password, PASSWORD_DEFAULT);
                return $this->createUser();
            }
        }

        public function checkSignIn($email, $password){
            if ($this->getUser("email", $email) != false) {
                $row = $this->getUser("email", $email);
                if (password_verify($password, $row['password'])) {
                    $arr = [];
                    $arr['userID'] = $row['userID'];
                    $arr['username'] = $row['username'];
                    return $arr;
                } else return false;
            } else return false;
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
                $arr['email'] = $this->email;
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
            $stmt = $this->conn->prepare("INSERT INTO carts (userID) values ?");
            $stmt->bind_param("i", $this->userID);
            $stmt->execute();
        }
    }
?>
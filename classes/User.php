<?php
    class User{
        private $conn;
        public $userID;
        private $email;
        private $username;
        private $password;
        public $errors = [];

        public function __construct($conn){
            $this->conn = $conn;
        }

        private function getUser($type, $data){
            $sql = "SELECT * FROM users WHERE $type = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $data);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                return $result->fetch_assoc();
            } else return false;
        }

        public function checkSignUp($email, $username, $password1){
            if ($this->getUser("email", $email) != false) {
                $this->errors["email"] = "Email is already taken";
                return "Email is already taken";
            }
            if (empty($this->errors)) {
                $this->email = $email;
                $this->username = $username;
                $this->password = password_hash($password1, PASSWORD_DEFAULT);
                return $this->createUser();
            }
        }

        public function checkSignIn($email, $password){
            if ($this->getUser("email", $email) != false) {
                $row = $this->getUser("email", $email);
                if (password_verify($password, $row['password'])) {
                    $arr = [];
                    $arr['userID'] = $row['userID'];
                    $arr['email'] = $row['email'];
                    $arr['username'] = $row['username'];
                    // json_encode($arr);
                    return $arr;
                } else return "Password is incorrect";
            } else return "Email not found";
        }
        
        private function createUser(){
            $sql = "INSERT INTO users (email, username, password) VALUES (?,?,?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sss", $this->email, $this->username, $this->password);
            $stmt->execute();
            if ($stmt->affected_rows == 1) {
                $this->userID = $stmt->insert_id;
                $sql2 = "INSERT INTO carts (userID) values ($this->userID)";
                $stmt2 = $this->conn->prepare($sql2);
                $stmt2->execute();
                $arr = [];
                $arr['userID'] = $this->userID;
                $arr['email'] = $this->email;
                $arr['username'] = $this->username;
                return $arr;
            }
            return "cannot create";
        }
    }
?>
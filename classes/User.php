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

        public function updateUsername($userID, $newUsername){
            $res = [];
            $res['isSuccess'] = false;
            $res['data'] = 'Unable to change username';
            $stmt = $this->conn->prepare("UPDATE users 
                                        SET username = ?
                                        WHERE userID = ?");
            $stmt->bind_param("si", $newUsername, $userID);
            $stmt->execute();
            if($stmt->affected_rows == 1){
                $res['isSuccess'] = true;
                $res['data'] = 'Change username successfully';
            }
            return $res;
        }

        public function updatePassword($userID,$old,$new){
            $res = [];
            $res['isSuccess'] = false;
            if ($this->getUser("userID", $userID) != false) {
                $row = $this->getUser("userID", $userID);
                
                if (password_verify($old, $row['password'])) {
                    $newH = password_hash($new, PASSWORD_DEFAULT);
                    if (password_verify($old,$newH)){
                        $res['data'] = 'Password cannot be the same with your current';
                        return $res;
                    }
                    $stmt = $this->conn->prepare("UPDATE users SET password = ? where userID = ? ");
                                        $stmt->bind_param("si", $newH, $userID);
                    $stmt->execute();
                    if($stmt->affected_rows == 1){
                        $res['isSuccess'] = true;
                        $res['data'] = 'Change password successfully';
                    }
                }
                else $res['data'] = "Your current password is incorrect";
            }
            else $res['data'] = "UserID not found";
            return $res;
        }
       
    }

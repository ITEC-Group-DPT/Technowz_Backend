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

        if ($this->getUser("email", $email) != false)
        {

            $row = $this->getUser("email", $email);


            if ($isAdmin && ($row['userRole'] == 1))
            {
                $res['data']['errorEmail'] = "Email not found";
                return $res;
            }
            if (password_verify($password, $row['password']))
            {
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

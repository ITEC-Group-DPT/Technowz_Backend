<?php

class Mock
{

    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    //getter
    public function getAllUserID()
    {
        $stmt = $this->conn->prepare("SELECT userID FROM users");


        $stmt->execute();

        $result = $stmt->get_result();

        $fetch = $result->fetch_all();

        $object = [];
        foreach ($fetch as $value) {
            array_push($object, $value[0]);
        }

        return $object;
    }

    public function getAllProductID()
    {
        $stmt = $this->conn->prepare("SELECT productID FROM products");


        $stmt->execute();

        $result = $stmt->get_result();

        $fetch = $result->fetch_all();

        $object = [];
        foreach ($fetch as $value) {
            array_push($object, $value[0]);
        }

        return $object;
    }

}
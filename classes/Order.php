<?php
class Order
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    private function substitution($char, $type) {
        $plain = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $cipher = ['5', 'T', '7', 'I', 'Z', '9', 'M', 'A', 'E', '4'];

        if($type == 'enc') {
            $index = array_search($char, $plain);
            return $cipher[$index];
        }

        if($type == 'dec') {
            $index = array_search($char, $cipher);
            return $plain[$index];
        }
    }

    private function encrypt($orderID) {
        $encryptedID = "";
        $chars = str_split($orderID);

        foreach ($chars as $char) {
            $encryptedID .= $this->substitution($char, 'enc');
        }

        return $encryptedID;
    }

    private function decrypt($encryptedID) {
        $orderID = "";
        $chars = str_split($encryptedID);

        foreach ($chars as $char) {
            $orderID .= $this->substitution($char, 'dec');
        }

        return $orderID;
    }

    public function createOrder($userID, $name, $address, $phone, $totalPrice, $productList, $date = null)
    {
        if ($date == null)
        {
            $stmt = $this->conn->prepare('INSERT into orders (userID, name, address, phone, totalPrice) values (?,?,?,?,?)');

            $stmt->bind_param('isssi', $userID, $name, $address, $phone, $totalPrice);
        }
        else
        {
            $stmt = $this->conn->prepare('INSERT into orders (userID, name, address, phone, totalPrice, dateCreated) values (?,?,?,?,?,?)');

            $stmt->bind_param('isssis', $userID, $name, $address, $phone, $totalPrice, $date);
        }

        $stmt->execute();
        $orderID = $this->conn->insert_id;

        foreach ($productList as $product)
        {
            $stmt1 = $this->conn->prepare('INSERT into orderdetails (orderID, productID, quantity, price) values (?,?,?,?)');

            $stmt1->bind_param('ssss', $orderID, $product[0], $product[1], $product[2]);

            $stmt1->execute();
            $this->updateSoldProduct($product[0], $product[1]);
        }

        $stmt2 = $this->conn->prepare("INSERT INTO orderstatus (orderID, statusID) values (?, 1)");


        $stmt2->bind_param('i', $orderID);

        $stmt2->execute();

        if ($stmt->affected_rows != 0) return true;
        else return false;
    }

    public function updateSoldProduct($productID, $soldQuantity)
    {
        $stmt = $this->conn->prepare('SELECT sold
                                        from products
                                        where productID = ?');

        $stmt->bind_param("i", $productID);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $newSold = $result['sold'] + $soldQuantity;
        $stmt1 = $this->conn->prepare('UPDATE products
                                        set sold = ?
                                        where productID = ?');
        $stmt1->bind_param('ii', $newSold, $productID);
        $stmt1->execute();
        if ($stmt1->affected_rows == 1) return true;
        else return false;
    }

    public function getOrderList($userID, $offset = 0, $limit = 5)
    {
        $stmt = $this->conn->prepare("SELECT ord.orderID, p.productID, p.name, i.img1, p.price, ordz.quantity, p.rating, p.sold
                                            FROM orders ord, orderdetails ordz,products p, productimage i
                                            WHERE ord.userID = ? and ord.orderID = ordz.orderID
                                                    and p.productID = i.productID and p.productID = ordz.productID
                                            ORDER BY ord.orderID desc ");
        $stmt->bind_param("i", $userID);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_all(MYSQLI_ASSOC);
        $ords = [];
        foreach ($row as $item)
        {
            if ($offset > 0)
            {
                $offset--;
                continue;
            }
            elseif (count($ords) == $limit)
            {
                break;
            }

            $orderID = $this->encrypt($item['orderID']);

            $obj = [
                "productID" => $item['productID'],
                "name" => $item['name'],
                "img1" => $item['img1'],
                "price" => $item['price'],
                'quantity' => $item['quantity'],
                "rating" => $item['rating'],
                "sold" => $item['sold'],
            ];
            if (!isset($ords[$orderID])) $ords[$orderID] = [];
            array_push($ords[$orderID], $obj);
        }
        return $ords;
    }

    public function getItemList($orderID, $userID)
    {
        $decryptedID = $this->decrypt($orderID);

        $stmt = $this->conn->prepare("SELECT p.productID, p.name, i.img1, p.price, ordz.quantity, p.rating, p.sold, ordz.rating as 'customerRating'
                                            from orders o, orderdetails ordz, products p, productimage i
                                            where o.orderID = ? and o.userID = ? and ordz.orderID = o.orderID
                                                    and ordz.productID = p.productID and p.productID = i.productID");
        $stmt->bind_param("ii", $decryptedID, $userID);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows != 0)
            return $result->fetch_all(MYSQLI_ASSOC);
        else return false;
    }

    public function getOrderInfo($orderID, $userID)
    {
        $decryptedID = $this->decrypt($orderID);

        $stmt = $this->conn->prepare("SELECT *, TIMESTAMPDIFF(minute, dateCreated, NOW()) as 'dateDiff'
                                            from orders
                                            where orderID = ? and userID = ?");
        $stmt->bind_param("ii", $decryptedID, $userID);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows != 0)
            return $result->fetch_assoc();
        else return false;
    }

    public function rateProduct($orderID, $productID, $rating)
    {
        $decryptedID = $this->decrypt($orderID);

        $stmt = $this->conn->prepare("UPDATE orderdetails
                                        set rating = ?
                                        where orderID = ? and productID = ?");
        $stmt->bind_param("dii", $rating, $decryptedID, $productID);
        $stmt->execute();
        if ($stmt->affected_rows == 1) return true;
        else return false;
    }

    public function commentProduct($orderID, $productID, $comment)
    {
        $decryptedID = $this->decrypt($orderID);

        $stmt = $this->conn->prepare("UPDATE orderdetails
                                        set comment = ?
                                        where orderID = ? and productID = ?");
        $stmt->bind_param("sii", $comment, $decryptedID, $productID);
        $stmt->execute();
        if ($stmt->affected_rows == 1) return true;
        else return false;
    }
}

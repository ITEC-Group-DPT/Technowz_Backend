<?php
class Product
{
    private $conn;
    private $productID;

    //constructor
    public function __construct($conn, $productID)
    {
        $this->conn = $conn;
        $this->productID = $productID;
    }

    public function getProduct()
    {
        $stmt = $this->conn->prepare("SELECT *
                                        from products p, productimage i
                                        where p.productID = ? and p.productID = i.productID");
        $stmt->bind_param("i", $this->productID);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 1)
            return $result->fetch_assoc();
        else return false;
    }

    public function updateProductRating($rating)
    {
        $stmt1 = $this->conn->prepare("SELECT *
                        from products p
                        where p.productID = ?");
        $stmt1->bind_param("i", $this->productID);
        $stmt1->execute();
        $result = $stmt1->get_result()->fetch_assoc();
        $oldRating = $result['rating'];
        $numRate = $result['numRate'];
        $newRating = (($oldRating * $numRate) + $rating) / ($numRate + 1);
        $stmt2 = $this->conn->prepare("UPDATE products
                                        Set rating = ?, numRate = ?
                                        where productID = ?");
        $numRate += 1;
        $stmt2->bind_param("dii", $newRating, $numRate, $this->productID);
        $stmt2->execute();
        if ($stmt2->affected_rows == 1) return true;
        else return false;
    }

    public function createProduct($data)
    {
        $stmt1 = $this->conn->prepare("INSERT INTO products
                                        (type, description, spec, name, price, rating, sold)
                                        VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt1->bind_param(
            "ssssidi",
            $data['type'],
            $data['description'],
            $data['spec'],
            $data['name'],
            $data['price'],
            $data['rating'],
            $data['sold']
        );
        $stmt1->execute();
        $this->productID = $stmt1->insert_id;

        $stmt2 = $this->conn->prepare("INSERT INTO productimage
                                        (productID, img1, img2, img3, img4)
                                        VALUES (?, ?, ?, ?, ?)");
        $stmt2->bind_param("issss", $this->productID, $data['img1'], $data['img2'], $data['img3'], $data['img4']);
        $stmt2->execute();
        if ($stmt1->affected_rows == 1 && $stmt2->affected_rows == 1) return $stmt1->insert_id;
        // if ($stmt1->affected_rows == 1) return $stmt1->insert_id;
        else return false;
    }

    public function modifyProduct($data)
    {
        $stmt1 = $this->conn->prepare("UPDATE products
                                        SET type = ?, description = ?, spec = ?,
                                            name = ?, price = ?, rating = ?, sold = ?
                                        WHERE productID = ?");
        $stmt1->bind_param(
            "ssssidii",
            $data['type'],
            $data['description'],
            $data['spec'],
            $data['name'],
            $data['price'],
            $data['rating'],
            $data['sold'],
            $this->productID
        );
        $stmt1->execute();

        $stmt2 = $this->conn->prepare("UPDATE productimage
                                        SET img1 = ?, img2 = ?, img3 = ?, img4 = ?
                                        WHERE productID = ?");
        $stmt2->bind_param("ssssi", $data['img1'], $data['img2'], $data['img3'], $data['img4'], $this->productID);
        $stmt2->execute();
        if ($stmt1->affected_rows > 0 || $stmt2->affected_rows > 0) return true;
        else return false;
    }

    public function removeProduct()
    {
        $stmt1 = $this->conn->prepare("DELETE FROM products WHERE productID like ?");
        $stmt1->bind_param("i", $this->productID);
        $stmt1->execute();

        $stmt2 = $this->conn->prepare("DELETE FROM productimage WHERE productID like ?");
        $stmt2->bind_param("i", $this->productID);
        $stmt2->execute();
        if ($stmt1->affected_rows != 0) return true;
        else return false;
    }

    //static
    public static function getTotalCategory($conn, $type)
    {
        $stmt = $conn->prepare("SELECT COUNT(*) as 'total'
                                from products
                                where type = ?");
        $stmt->bind_param("s", $type);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public static function getProductsByCategory($conn, $type, $orderBy, $option, $offset = 0, $limit = 8)
    {
        //security
        $allowedOrderBy = ['price', 'rating'];
        $allowedOption = ['ASC', 'DESC'];
        if (!in_array($orderBy, $allowedOrderBy))
            throw new Exception("Invalid column name");
        if (!in_array($option, $allowedOption))
            throw new Exception("Invalid order by option");

        $stmt = $conn->prepare("SELECT p.productID, p.name, pimg.img1, p.rating, p.sold, p.price
                                    from products p, productimage pimg
                                    where p.type = ? and p.productID = pimg.productID
                                    order by $orderBy $option
                                    limit ?, ?");
        $stmt->bind_param("sii", $type, $offset, $limit);
        $stmt->execute();
        $results = $stmt->get_result();
        return $results->fetch_all(MYSQLI_ASSOC);
    }

    public static function getTopRating($conn, $limit = 10)
    {
        $stmt = $conn->prepare("SELECT p.productID, p.name, pimg.img1, p.rating, p.sold, p.price
                                    from products p, productimage pimg
                                    where p.productID = pimg.productID
                                    order by p.sold desc limit ?");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $results = $stmt->get_result();
        return $results->fetch_all(MYSQLI_ASSOC);
    }

    public static function getProducts($conn, $value, $limit = 5)
    {
        $value = "%" . str_replace(' ', '%', $value) . "%";
        $stmt = $conn->prepare("SELECT p.productID, p.name, pimg.img1, p.rating, p.sold, p.price
                                from products p, productimage pimg
                                where p.productID = pimg.productID and p.name like ? limit ?");
        $stmt->bind_param("si", $value, $limit);
        $stmt->execute();
        $results = $stmt->get_result();
        return $results->fetch_all(MYSQLI_ASSOC);
    }

    public static function getAllProduct($conn){
        $stmt = $conn->prepare("SELECT *
                            from products p, productimage img
                            where p.productID = img.productID");
        $stmt->execute();
        $results = $stmt->get_result();
        return $results->fetch_all(MYSQLI_ASSOC);
    }
}

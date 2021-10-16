<?php
  class Cart{
    private $conn;
    private $userID;
    private $cartID;

    public function __construct($conn, $userID){
      $this->conn = $conn;
      $this->userID = $userID;
      $stmt = $this->conn->prepare("SELECT * from carts WHERE userID = ?");
      $stmt->bind_param("i", $userID);
      $stmt->execute();
      $result = $stmt->get_result();
      $row = $result->fetch_assoc();
      $this->cartID = $row["cartID"];
    }

    public function addItemToCart($itemID){
      $stmt1 = $this->conn->prepare("SELECT * from cartdetails  where cartID = ? and productID = ?");
      $stmt1->bind_param("ii", $this->cartID, $itemID);
      $stmt1->execute();
      $result1 = $stmt1->get_result();
      if ($result1->num_rows == 0) {
        $quantity = 1;
        $stmt2 = $this->conn->prepare("INSERT into cartdetails (cartID, productID, quantity) values (?, ?, ?)");
        $stmt2->bind_param("iii", $this->cartID, $itemID, $quantity);
        $stmt2->execute();
        if ($stmt2->affected_rows == 1) return true;
        else return false;
      }
      else return $this->increaseQuantity($itemID);
    }

    public function getCartList(){
      $stmt = $this->conn->prepare
        ("SELECT p.productID, p.name, pri.img1, p.sold, cd.quantity, p.price
        from cartdetails cd, carts c, products p, productimage pri
        where cd.cartID = c.cartID and c.userID = ? and cd.productID = p.productID and p.productID = pri.productID");
      $stmt->bind_param("i", $this->userID);
      $stmt->execute();
      $result = $stmt->get_result();
      return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function increaseQuantity($itemID){
      $quantity = $this->getQuantity($itemID) + 1;
      $stmt = $this->conn->prepare("UPDATE cartdetails set quantity = ? where cartID = ? and productID = ?");
      $stmt->bind_param("iii", $quantity, $this->cartID, $itemID);
      $stmt->execute();
      if ($stmt->affected_rows == 1) return true;
      else return false;
    }

    public function decreaseQuantity($itemID){
      if ($this->getQuantity($itemID) == 1) return false;
      $quantity = $this->getQuantity($itemID) - 1;
      $stmt = $this->conn->prepare("UPDATE cartdetails set quantity = ? where cartID = ? and productID = ?");
      $stmt->bind_param("iii", $quantity, $this->cartID, $itemID);
      $stmt->execute();
      if ($stmt->affected_rows == 1) return true;
      else return false;
    }

    public function getQuantity($itemID){
      $stmt = $this->conn->prepare("SELECT * from cartdetails where cartID = ? and productID = ?");
      $stmt->bind_param("ii", $this->cartID, $itemID);
      $stmt->execute();
      $result = $stmt->get_result();
      $result = $result->fetch_assoc();
      return $result['quantity'];
    }

    public function removeItem($itemID){
      $stmt = $this->conn->prepare("DELETE from cartdetails where cartID = ? and productID = ?");
      $stmt->bind_param("ii", $this->cartID, $itemID);
      $stmt->execute();
      if ($stmt->affected_rows != 0) return true;
      else return false;
    }

    public function removeAll(){
      $stmt = $this->conn->prepare("DELETE from cartdetails where cartID = ?");
      $stmt->bind_param("i", $this->cartID);
      $stmt->execute();
      if ($stmt->affected_rows != 0) return true;
      else return false;
    }

    public function getTotalQuantity(){
      $stmt = $this->conn->prepare("SELECT sum(quantity) as 'totalQuantity' from cartdetails where cartID = ?");
      $stmt->bind_param("i", $this->cartID);
      $stmt->execute();
      $result = $stmt->get_result();
      $result = $result->fetch_assoc();
      $total = $result['totalQuantity'];
      if ($total == NULL) $total = 0;
      return $total;
    }

    public function getTotalPrice(){
      $stmt = $this->conn->prepare("SELECT SUM(p.price*cd.quantity) as 'totalPrice'
                                    from cartdetails cd, products p
                                    where cd.cartID = ? and cd.productID = p.productID");
      $stmt->bind_param("i", $this->cartID);
      $stmt->execute();
      $result = $stmt->get_result();
      $result = $result->fetch_assoc();
      return (number_format($result['totalPrice']) . '₫');
    }
  }
?>

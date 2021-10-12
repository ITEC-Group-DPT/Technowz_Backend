<?php 
    class DeliveryInfo {
        private $conn;
        public $userID;

        public function __construct($conn){
            $this->conn = $conn;
        }
        
        public function getDeliveryInfo($userID){
            $this->userID = $userID;
            $stmt = $this->conn->prepare('SELECT * from deliveryinfo where userID = ?');
            $stmt->bind_param('i', $userID);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows == 0) return 'No rows';
            $delivery = [];
            while ($row = $result->fetch_assoc()) {
                array_push($delivery, $row);
            }
            return $delivery;
        }

        public function updateDeliveryInfo($deliID, $name, $address, $phone){
            $stmt = $this->conn->prepare('UPDATE deliveryinfo set address = ?, name = ?, phone = ? where deliveryID = ?');
            $stmt->bind_param('ssss', $address, $name, $phone, $deliID);
            $stmt->execute();
        }
        
        public function createDeliveryInfo($name, $address, $phone, $userID){
            $stmt = $this->conn->prepare('INSERT into deliveryinfo (address,name,phone,userid) values (?,?,?,?)');
            $stmt->bind_param('sssi', $address, $name, $phone, $userID);
            $stmt->execute();
        }
    
        public function deleteDelivery($deliID){
            $stmt = $this->conn->prepare('DELETE from deliveryinfo where deliveryID = ?');
            $stmt->bind_param('i', $deliID);
            $stmt->execute();
        }
    }
?>
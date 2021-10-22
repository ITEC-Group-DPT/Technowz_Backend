<?php 
    class DeliveryInfo {
        private $conn;
        private $userID;

        public function __construct($conn, $userID){
            $this->conn = $conn;
            $this->userID = $userID;
        }
        
        public function getDeliveryInfo(){
            $stmt = $this->conn->prepare('SELECT * from deliveryinfo where userID = ?');
            $stmt->bind_param('i', $this->userID);
            $stmt->execute();
            $result = $stmt->get_result();
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
            if($stmt->affected_rows != 0) return true;
            else return false;
        }
        
        public function createDeliveryInfo($name, $address, $phone){
            $stmt = $this->conn->prepare('INSERT into deliveryinfo (address, name, phone, userID) values (?,?,?,?)');
            $stmt->bind_param('sssi', $address, $name, $phone, $this->userID);
            $stmt->execute();
            if($stmt->affected_rows != 0) return true;
            else return false;
        }
    
        public function deleteDelivery($deliID){
            $stmt = $this->conn->prepare('DELETE from deliveryinfo where deliveryID = ?');
            $stmt->bind_param('i', $deliID);
            $stmt->execute();
            if($stmt->affected_rows != 0) return true;
            else return false;
        }
    }
?>
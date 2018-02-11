<?php

class EquipmentMaterial extends IDGenerator{

    // database connection and table name
    private $conn;
    private $table_name = "equipment_material";
    private $error;

    // object properties
    public $serialID;
    public $productID;
    public $locationID;
    public $status;

    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    public function getError() {
        return $this->error;
    }

    function updateLocationAndStatus(){

        // query to insert record
        $query = "UPDATE " . $this->table_name . "
            SET locationID=:locationID, status=:status WHERE serialID=:serialID";
 
        // prepare query
        $stmt = $this->conn->prepare($query);
 
        // sanitize
        $this->locationID = htmlspecialchars(strip_tags($this->locationID));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->serialID = htmlspecialchars(strip_tags($this->serialID));

        // bind values
        $stmt->bindParam(":serialID", $this->serialID);
        $stmt->bindParam(":locationID", $this->locationID);
        $stmt->bindParam(":status", $this->status);
        
        // execute query
        try{    
            $stmt->execute();
            return true;
        }
        catch (Exception $e){ 
            $this->error = $stmt->errorInfo();
            return false;
        }  
    }
    
    function getList($warehouseID) {

        // select all query
        $query = "SELECT A.serialID FROM ". $this->table_name ." as A 
                INNER JOIN location on A.locationID = location.locationID
                WHERE location.warehouseID=:warehouseID";
   
        // prepare query
        $stmt = $this->conn->prepare($query);
 
        // sanitize
        $warehouseID = htmlspecialchars(strip_tags($warehouseID));
      
        // bind values
        $stmt->bindParam(":warehouseID", $warehouseID);

        // execute query
        try{    
            $stmt->execute();
            if($stmt->rowCount() == 0) throw new Exception;
            
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $data = array();
            $i = 0;
            foreach ($rows as $row){
                $data[$i]['serial']['ID'] = $row['serialID'];
                $data[$i]['serial']['Reference'] = "http://petprojects.altervista.org/".$warehouseID."/equipment_material/".$row['serialID']."/";
                $i++;
            }

            return $data;
        }
        catch (Exception $e){ 
            $this->error = $stmt->errorInfo();
            return null;
        }       
   }

   function getListInLocation($warehouseID) {

        // select all query
        $query = "SELECT A.serialID FROM ". $this->table_name ." as A 
                INNER JOIN location on A.locationID = location.locationID
                WHERE A.locationID=:locationID AND location.warehouseID=:warehouseID";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $warehouseID = htmlspecialchars(strip_tags($warehouseID));
        $this->locationID = htmlspecialchars(strip_tags($this->locationID));
    
        // bind values
        $stmt->bindParam(":warehouseID", $warehouseID);
        $stmt->bindParam(":locationID", $this->locationID);

        // execute query
        try{    
            $stmt->execute();
            if($stmt->rowCount() == 0) throw new Exception;
            
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $data = array();
            $i = 0;
            foreach ($rows as $row){
                $data[$i]['serial']['ID'] = $row['serialID'];
                $data[$i]['serial']['Reference'] = "http://petprojects.altervista.org/".$warehouseID."/equipment_material/".$row['serialID']."/";
                $i++;
            }

            return $data;
        }
        catch (Exception $e){ 
            $this->error = $stmt->errorInfo();
            return null;
        }       
    }

    function read($warehouseID){
    
        // select all query
        $query = "SELECT A.status, A.productID, A.locationID FROM ". $this->table_name ." as A 
                INNER JOIN location on A.locationID = location.locationID
                WHERE location.warehouseID=:warehouseID AND A.serialID=:serialID";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
 
        // sanitize
        $this->serialID = htmlspecialchars(strip_tags($this->serialID));
        $warehouseID = htmlspecialchars(strip_tags($warehouseID));
      
        // bind values
        $stmt->bindParam(":serialID", $this->serialID);
        $stmt->bindParam(":warehouseID", $warehouseID);

        // execute query
        try{    
            $stmt->execute();
            if($stmt->rowCount() == 0) throw new Exception;

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $data = array();
            $data['status'] = $row['status'];
            $data['product']['ID'] = $row['productID'];
            $data['product']['Reference'] = "http://petprojects.altervista.org/catalog/".$row['productID']."/";
            $data['location']['ID'] = $row['locationID'];
            $data['location']['Reference'] = "http://petprojects.altervista.org/".$warehouseID."/location/".$row['locationID']."/";

            return $data;
        }
        catch (Exception $e){ 
            $this->error = $stmt->errorInfo();
            return null;
        }
    }

    function create($warehouseID){
 
        // query to insert record
        $query = "INSERT INTO " . $this->table_name . "
            SET serialID=:serialID, productID=:productID, locationID=:locationID, status=:status";
 
        // prepare query
        $stmt = $this->conn->prepare($query);
 
        // sanitize
        $this->serialID = htmlspecialchars(strip_tags($this->serialID));
        $this->productID = htmlspecialchars(strip_tags($this->productID));
        $this->locationID = htmlspecialchars(strip_tags($this->locationID));
        $this->status = "Available";

        // bind values
        $stmt->bindParam(":serialID", $this->serialID);
        $stmt->bindParam(":productID", $this->productID);
        $stmt->bindParam(":locationID", $this->locationID);
        $stmt->bindParam(":status", $this->status);
        
        // execute query
        try{    
            $stmt->execute();
            return array("Reference" => "http://petprojects.altervista.org/".$warehouseID."/equipment_material/".$this->serialID."/");
        }
        catch (Exception $e){ 
            $this->error = $stmt->errorInfo();
            return null;
        }
    }

    function updateStatus() {
 
        // query to insert record
        $query = "UPDATE " . $this->table_name . "
            SET status=:status WHERE serialID=:serialID";
 
        // prepare query
        $stmt = $this->conn->prepare($query);
 
        // sanitize
        $this->serialID = htmlspecialchars(strip_tags($this->serialID));
        $this->status = htmlspecialchars(strip_tags($this->status));

        // bind values
        $stmt->bindParam(":serialID", $this->serialID);
        $stmt->bindParam(":status", $this->status);
        
        // execute query
        try{    
            $stmt->execute();
            return true;
        }
        catch (Exception $e){ 
            $this->error = $stmt->errorInfo();
            return false;
        }        
    }
}

?>
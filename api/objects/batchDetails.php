<?php

class BatchDetails extends IDGenerator{

    // database connection and table name
    private $conn;
    private $table_name = "batchDetails";
    private $error;

    // object properties
    public $batchID;
    public $serialID;
    public $fromLocationID;

    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }
    public function getError(){
        return $this->error;
    }

    function read(){
    
        // select all query
        $query = "SELECT serialID, fromLocationID FROM ". $this->table_name ." WHERE batchID=:batchID";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
 
        // sanitize
        $this->batchID = htmlspecialchars(strip_tags($this->batchID));
      
        // bind values
        $stmt->bindParam(":batchID", $this->batchID);

        // execute query
        try{    
            $stmt->execute();
            if($stmt->rowCount() == 0) throw new Exception;

            $i = 0;
            $data = [];

            foreach ($stmt as $row)
            {
               $l = new BatchDetails($this->conn);
               $l->serialID = $row['serialID'];
               $l->fromLocationID = $row['fromLocationID'];
               unset($l->batchID);
               unset($l->error);
               $data[$i] = $l;
               $i++;
            }
            return $data;
        }
        catch (Exception $e){ 
            $this->error = $stmt->errorInfo();
            return null;
        }
    }

    function getListInbound($orderID){
    
        // select all query
        $query = "SELECT serialID FROM ". $this->table_name ."  
        NATURAL JOIN batch 
        WHERE batch.batchID=:batchID 
        AND batch.orderID=:orderID";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
 
        // sanitize
        $this->batchID = htmlspecialchars(strip_tags($this->batchID));
        $orderID = htmlspecialchars(strip_tags($orderID));
      
        // bind values
        $stmt->bindParam(":batchID", $this->batchID);
        $stmt->bindParam(":orderID", $orderID);

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
    
    function getListOutbound($warehouseID, $orderID){
    
        // select all query
        $query = "SELECT A.serialID, A.fromLocationID FROM ". $this->table_name ." as A 
                INNER JOIN batch on batch.batchID = A.batchID
                WHERE batch.batchID=:batchID AND batch.orderID=:orderID";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
 
        // sanitize
        $this->batchID = htmlspecialchars(strip_tags($this->batchID));
        $orderID = htmlspecialchars(strip_tags($orderID));
      
        // bind values
        $stmt->bindParam(":batchID", $this->batchID);
        $stmt->bindParam(":orderID", $orderID);

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
                $data[$i]['location']['ID'] = $row['fromLocationID'];
                $data[$i]['location']['Reference'] = "http://petprojects.altervista.org/".$warehouseID."/location/".$row['fromLocationID']."/";
                $i++;
            }

            return $data;
        }
        catch (Exception $e){ 
            $this->error = $stmt->errorInfo();
            return null;
        }
    }

    function create($warehouseID, $orderID){
 
        // query to insert record
        $query = "INSERT INTO " . $this->table_name . "
            SET batchID=:batchID, serialID=:serialID, fromLocationID=:fromLocationID";
 
        // prepare query
        $stmt = $this->conn->prepare($query);
 
        // sanitize
        $this->batchID = htmlspecialchars(strip_tags($this->batchID));
        $this->serialID = htmlspecialchars(strip_tags($this->serialID));
        $this->fromLocationID = htmlspecialchars(strip_tags($this->fromLocationID));

        // bind values
        $stmt->bindParam(":batchID", $this->batchID);
        $stmt->bindParam(":serialID", $this->serialID);
        $stmt->bindParam(":fromLocationID", $this->fromLocationID);
        
        // execute query
        try{    
            $stmt->execute();
            return array("batchID" => $this->batchID,
                         "serialID" => $this->serialID,
                         "Reference" => "http://petprojects.altervista.org/".$warehouseID."/outbound/".$orderID."/batch/".$this->batchID."/details/list/");
    
        }
        catch (Exception $e){ 
            $this->error = $stmt->errorInfo();
            return false;
        }
    }
}

?>
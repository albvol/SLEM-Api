<?php

class Batch extends IDGenerator{

    // database connection and table name
    private $conn;
    private $table_name = "batch";
    private $error;

    // object properties
    public $batchID;
    public $orderID;
    public $productID;
    public $quantity;
    public $inboundEmployeeID;
    public $outboundEmployeeID;

    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    public function getError() {
        return $this->error;
    }

    function addInbound(){

        // update query
        $query = "UPDATE ". $this->table_name ." 
                SET inboundEmployeeID=:inboundEmployeeID 
                WHERE batchID=:batchID AND orderID=:orderID";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
 
        // sanitize
        $this->batchID = htmlspecialchars(strip_tags($this->batchID));
        $this->inboundEmployeeID = htmlspecialchars(strip_tags($this->inboundEmployeeID));
        $this->orderID = htmlspecialchars(strip_tags($this->orderID));
      
        // bind values
        $stmt->bindParam(":batchID", $this->batchID);
        $stmt->bindParam(":inboundEmployeeID", $this->inboundEmployeeID);
        $stmt->bindParam(":orderID", $this->orderID);

        
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

    function addOutbound(){

        // update query
        $query = "UPDATE ". $this->table_name ." 
                SET outboundEmployeeID=:outboundEmployeeID 
                WHERE batchID=:batchID AND orderID=:orderID";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
 
        // sanitize
        $this->batchID = htmlspecialchars(strip_tags($this->batchID));
        $this->outboundEmployeeID = htmlspecialchars(strip_tags($this->outboundEmployeeID));
        $this->orderID = htmlspecialchars(strip_tags($this->orderID));
      
        // bind values
        $stmt->bindParam(":batchID", $this->batchID);
        $stmt->bindParam(":outboundEmployeeID", $this->outboundEmployeeID);
        $stmt->bindParam(":orderID", $this->orderID);

        
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

    function getListInbound($warehouseID){
    
        // select all query
        $query = "SELECT A.batchID FROM ". $this->table_name ." as A
                INNER JOIN orders on A.orderID=orders.orderID 
                WHERE A.orderID=:orderID AND orders.toWarehouseID=:warehouseID";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
 
        // sanitize
        $this->orderID = htmlspecialchars(strip_tags($this->orderID));
        $warehouseID = htmlspecialchars(strip_tags($warehouseID));
      
        // bind values
        $stmt->bindParam(":orderID", $this->orderID);
        $stmt->bindParam(":warehouseID", $warehouseID);

        // execute query
        try{    
            $stmt->execute();
            if($stmt->rowCount() == 0) throw new Exception;
            
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $data = array();
            $i = 0;
            foreach ($rows as $row){
                $data[$i]['batch']['ID'] = $row['batchID'];
                $data[$i]['batch']['Reference'] = "http://petprojects.altervista.org/".$warehouseID."/inbound/batch/".$row['batchID']."/";
                $i++;
            }

            return $data;
        }
        catch (Exception $e){ 
            $this->error = $stmt->errorInfo();
            return null;
        }
    }

    function getListOutbound($warehouseID){
    
        // select all query
        $query = "SELECT A.batchID FROM ". $this->table_name ." as A
                INNER JOIN orders on A.orderID=orders.orderID 
                WHERE A.orderID=:orderID AND orders.fromWarehouseID=:warehouseID";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
 
        // sanitize
        $this->orderID = htmlspecialchars(strip_tags($this->orderID));
        $warehouseID = htmlspecialchars(strip_tags($warehouseID));
      
        // bind values
        $stmt->bindParam(":orderID", $this->orderID);
        $stmt->bindParam(":warehouseID", $warehouseID);

        // execute query
        try{    
            $stmt->execute();
            if($stmt->rowCount() == 0) throw new Exception;
            
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $data = array();
            $i = 0;
            foreach ($rows as $row){
                $data[$i]['batch']['ID'] = $row['batchID'];
                $data[$i]['batch']['Reference'] = "http://petprojects.altervista.org/".$warehouseID."/outbound/batch/".$row['batchID']."/";
                $i++;
            }

            return $data;
        }
        catch (Exception $e){ 
            $this->error = $stmt->errorInfo();
            return null;
        }
    }

    function getInbound($warehouseID, $batchID){
    
        // select all query
        $query = "SELECT A.orderID, A.productID, A.quantity, A.inboundEmployeeID FROM ". $this->table_name ." as A
        INNER JOIN orders on orders.orderID = A.orderID
        WHERE A.batchID=:batchID AND A.orderID=:orderID AND orders.toWarehouseID=:warehouseID";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
 
        // sanitize
        $this->batchID = htmlspecialchars(strip_tags($this->batchID));
        $this->orderID = htmlspecialchars(strip_tags($this->orderID));
        $warehouseID = htmlspecialchars(strip_tags($warehouseID));
      
        // bind values
        $stmt->bindParam(":batchID", $this->batchID);
        $stmt->bindParam(":orderID", $this->orderID);
        $stmt->bindParam(":warehouseID", $warehouseID);

        // execute query
        try{    
            $stmt->execute();
            if($stmt->rowCount() == 0) throw new Exception;

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $data = array();
            $i = 0;
            $data['quantity'] = $row['quantity'];
            $data['order']['ID'] = $row['orderID'];
            $data['order']['Reference'] = "http://petprojects.altervista.org/".$warehouseID."/inbound/".$row['orderID']."/";
            $data['product']['ID'] = $row['productID'];
            $data['product']['Reference'] = "http://petprojects.altervista.org/catalog/".$row['productID']."/";
            $data['inboundEmployee']['ID'] = $row['inboundEmployeeID'];
            if($data['inboundEmployee']['ID'] != null) $data['inboundEmployee']['Reference'] = "http://petprojects.altervista.org/".$warehouseID."/employee/".$row['inboundEmployeeID']."/";

            return $data;
        }
        catch (Exception $e){ 
            $this->error = $stmt->errorInfo();
            return null;
        }
    }

    function getOutbound($warehouseID, $batchID){
    
        // select all query
        $query = "SELECT A.orderID, A.productID, A.quantity, A.outboundEmployeeID FROM ". $this->table_name ." as A
        INNER JOIN orders on orders.orderID = A.orderID
        WHERE A.batchID=:batchID AND A.orderID=:orderID AND orders.fromWarehouseID=:warehouseID";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
 
        // sanitize
        $this->batchID = htmlspecialchars(strip_tags($this->batchID));
        $this->orderID = htmlspecialchars(strip_tags($this->orderID));
        $warehouseID = htmlspecialchars(strip_tags($warehouseID));
      
        // bind values
        $stmt->bindParam(":batchID", $this->batchID);
        $stmt->bindParam(":orderID", $this->orderID);
        $stmt->bindParam(":warehouseID", $warehouseID);

        // execute query
        try{    
            $stmt->execute();
            if($stmt->rowCount() == 0) throw new Exception;
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $data['quantity'] = $row['quantity'];
            $data['order']['ID'] = $row['orderID'];
            $data['order']['Reference'] = "http://petprojects.altervista.org/".$warehouseID."/outbound/".$row['orderID']."/";
            $data['product']['ID'] = $row['productID'];
            $data['product']['Reference'] = "http://petprojects.altervista.org/catalog/".$row['productID']."/";
            $data['outboundEmployee']['ID'] = $row['outboundEmployeeID'];
            if($data['outboundEmployee']['ID'] != null) $data['outboundEmployee']['Reference'] = "http://petprojects.altervista.org/".$warehouseID."/employee/".$row['outboundEmployeeID']."/";

            return $data;
        }
        catch (Exception $e){ 
            $this->error = $stmt->errorInfo();
            return null;
        }
    }

    function find(){
    
        // select all query
        $query = "SELECT * FROM ". $this->table_name ." WHERE orderID=:orderID AND productID=:productID";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
 
        // sanitize
        $this->productID = htmlspecialchars(strip_tags($this->productID));
        $this->orderID = htmlspecialchars(strip_tags($this->orderID));
      
        // bind values
        $stmt->bindParam(":productID", $this->productID);
        $stmt->bindParam(":orderID", $this->orderID);

        // execute query
        try{    
            $stmt->execute();
            if($stmt->rowCount() == 0) throw new Exception;
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $data['quantity'] = $row['quantity'];
            $data['order']['ID'] = $row['orderID'];
            $data['product']['ID'] = $row['productID'];
            $data['product']['Reference'] = "http://petprojects.altervista.org/catalog/".$row['productID']."/";
            $data['outboundEmployee']['ID'] = $row['outboundEmployeeID'];
            if($data['outboundEmployee']['ID'] != null) $data['outboundEmployee']['Reference'] = "http://petprojects.altervista.org/".$warehouseID."/employee/".$row['inboundEmployeeID']."/";
            $data['inboundEmployee']['ID'] = $row['inboundEmployee'];
            if($data['inboundEmployee']['ID'] != null) $data['inboundEmployee']['Reference'] = "http://petprojects.altervista.org/".$warehouseID."/employee/".$row['inboundEmployeeID']."/";
    
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
            SET batchID=:batchID, orderID=:orderID, productID=:productID, quantity=:quantity";
 
        // prepare query
        $stmt = $this->conn->prepare($query);
 
        // sanitize
        $this->batchID = $this->generate();
        $this->orderID = htmlspecialchars(strip_tags($this->orderID));
        $this->productID = htmlspecialchars(strip_tags($this->productID));
        $this->quantity = htmlspecialchars(strip_tags($this->quantity));

        // bind values
        $stmt->bindParam(":batchID", $this->batchID);
        $stmt->bindParam(":orderID", $this->orderID);
        $stmt->bindParam(":productID", $this->productID);
        $stmt->bindParam(":quantity", $this->quantity);
        
        // execute query
        try{    
            $stmt->execute();
            return array(
                    "batchID" => $this->batchID,
                    "Reference" => "http://petprojects.altervista.org/".$warehouseID."/outbound/".$this->orderID."/batch/".$this->batchID."/");
        }
        catch (Exception $e){ 
            $this->error = $stmt->errorInfo();
            return false;
        }
    }
}

?>
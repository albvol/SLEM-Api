<?php

class Order extends IDGenerator{

    // database connection and table name
    private $conn;
    private $table_name = "orders";
    private $error;

    // object properties
    public $orderID;
    public $fromWarehouseID;
    public $toWarehouseID;
    public $date;

    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    public function getError() {
        return $this->error;
    }

    function get(){
    
        // select all query
        $query = "SELECT * FROM ". $this->table_name ." WHERE orderID=:orderID AND (toWarehouseID=:toWarehouseID OR fromWarehouseID=:fromWarehouseID)";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
 
        // sanitize
        $this->orderID = htmlspecialchars(strip_tags($this->orderID));
        $this->toWarehouseID = htmlspecialchars(strip_tags($this->toWarehouseID));
        $this->fromWarehouseID = htmlspecialchars(strip_tags($this->fromWarehouseID));
      
        // bind values
        $stmt->bindParam(":orderID", $this->orderID);
        $stmt->bindParam(":toWarehouseID", $this->toWarehouseID);
        $stmt->bindParam(":fromWarehouseID", $this->fromWarehouseID);

        // execute query
        try{    
            $stmt->execute();
            if($stmt->rowCount() == 0) throw new Exception;

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $data = array();
            $data['date'] = $row['date'];
            $data['from']['ID'] = $row['fromWarehouseID'];
            $data['from']['Reference'] = "http://petprojects.altervista.org/warehouse/".$row['fromWarehouseID']."/";
            $data['to']['ID'] = $row['toWarehouseID'];
            $data['to']['Reference'] = "http://petprojects.altervista.org/warehouse/".$row['toWarehouseID']."/";
            
            return $data;
        }
        catch (Exception $e){ 
            $this->error = $stmt->errorInfo();
            return null;
        }
    }

    function readInbound(){
    
        // select all query
        $query = "SELECT * FROM ". $this->table_name ." WHERE toWarehouseID=:toWarehouseID";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
 
        // sanitize
        $this->toWarehouseID = htmlspecialchars(strip_tags($this->toWarehouseID));
      
        // bind values
        $stmt->bindParam(":toWarehouseID", $this->toWarehouseID);

        // execute query
        try{    
            $stmt->execute();
            if($stmt->rowCount() == 0) throw new Exception;
            
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $data = array();
            $i = 0;
            foreach ($rows as $row){ 
                $data[$i]['date'] = $row['date'];
                $data[$i]['order']['ID'] = $row['orderID'];
                $data[$i]['order']['Reference'] = "http://petprojects.altervista.org/".$this->toWarehouseID."/inbound/".$row['orderID']."/";
                $data[$i]['from']['ID'] = $row['fromWarehouseID'];
                $data[$i]['from']['Reference'] = "http://petprojects.altervista.org/warehouse/".$row['fromWarehouseID']."/";
                $data[$i]['to']['ID'] = $row['toWarehouseID'];
                $data[$i]['to']['Reference'] = "http://petprojects.altervista.org/warehouse/".$row['toWarehouseID']."/";
                $i++;
            }

            return $data;
        }
        catch (Exception $e){ 
            $this->error = $stmt->errorInfo();
            return null;
        }
    }

    function getInbound(){
    
        // select all query
        $query = "SELECT * FROM ". $this->table_name ." WHERE toWarehouseID=:toWarehouseID AND orderID=:orderID";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
        
        // sanitize
        $this->toWarehouseID = htmlspecialchars(strip_tags($this->toWarehouseID));
        $this->orderID = htmlspecialchars(strip_tags($this->orderID));
      
        // bind values
        $stmt->bindParam(":toWarehouseID", $this->toWarehouseID);
        $stmt->bindParam(":orderID", $this->orderID);

        // execute query
        try{    
            $stmt->execute();
            //if($stmt->rowCount() == 0) throw new Exception;
            
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $data = array();
            $i = 0;
            foreach ($rows as $row){ 
                $data[$i]['date'] = $row['date'];
                $data[$i]['order']['ID'] = $row['orderID'];
                $data[$i]['order']['Reference'] = "http://petprojects.altervista.org/".$this->toWarehouseID."/inbound/".$row['orderID']."/";
                $data[$i]['from']['ID'] = $row['fromWarehouseID'];
                $data[$i]['from']['Reference'] = "http://petprojects.altervista.org/warehouse/".$row['fromWarehouseID']."/";
                $data[$i]['to']['ID'] = $row['toWarehouseID'];
                $data[$i]['to']['Reference'] = "http://petprojects.altervista.org/warehouse/".$row['toWarehouseID']."/";
                $i++;
            }

            return $data;
        }
        catch (Exception $e){ 
            $this->error = $stmt->errorInfo();
            return null;
        }
    }

    function getOutbound(){
    
        // select all query
        $query = "SELECT * FROM ". $this->table_name ." WHERE fromWarehouseID=:fromWarehouseID AND orderID=:orderID";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
        
        // sanitize
        $this->fromWarehouseID = htmlspecialchars(strip_tags($this->fromWarehouseID));
        $this->orderID = htmlspecialchars(strip_tags($this->orderID));
      
        // bind values
        $stmt->bindParam(":fromWarehouseID", $this->fromWarehouseID);
        $stmt->bindParam(":orderID", $this->orderID);

        // execute query
        try{    
            $stmt->execute();
            //if($stmt->rowCount() == 0) throw new Exception;
            
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $data = array();
            $i = 0;
            foreach ($rows as $row){ 
                $data[$i]['date'] = $row['date'];
                $data[$i]['order']['ID'] = $row['orderID'];
                $data[$i]['order']['Reference'] = "http://petprojects.altervista.org/".$this->fromWarehouseID."/outbound/".$row['orderID']."/";
                $data[$i]['from']['ID'] = $row['fromWarehouseID'];
                $data[$i]['from']['Reference'] = "http://petprojects.altervista.org/warehouse/".$row['fromWarehouseID']."/";
                $data[$i]['to']['ID'] = $row['toWarehouseID'];
                $data[$i]['to']['Reference'] = "http://petprojects.altervista.org/warehouse/".$row['toWarehouseID']."/";
                $i++;
            }

            return $data;
        }
        catch (Exception $e){ 
            $this->error = $stmt->errorInfo();
            return null;
        }
    }

    function readOutbound(){
    
        // select all query
        $query = "SELECT * FROM ". $this->table_name ." WHERE fromWarehouseID=:fromWarehouseID";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
 
        // sanitize
        $this->fromWarehouseID = htmlspecialchars(strip_tags($this->fromWarehouseID));
      
        // bind values
        $stmt->bindParam(":fromWarehouseID", $this->fromWarehouseID);

        // execute query
        try{    
            $stmt->execute();
            if($stmt->rowCount() == 0) throw new Exception;
            
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $data = array();
            $i = 0;
            foreach ($rows as $row){
                $data[$i]['date'] = $row['date'];
                $data[$i]['order']['ID'] = $row['orderID'];
                $data[$i]['order']['Reference'] = "http://petprojects.altervista.org/".$this->fromWarehouseID."/outbound/".$row['orderID']."/";
                $data[$i]['from']['ID'] = $row['fromWarehouseID'];
                $data[$i]['from']['Reference'] = "http://petprojects.altervista.org/warehouse/".$row['fromWarehouseID']."/";
                $data[$i]['to']['ID'] = $row['toWarehouseID'];
                $data[$i]['to']['Reference'] = "http://petprojects.altervista.org/warehouse/".$row['toWarehouseID']."/";
                $i++;
            }

            return $data;
        }
        catch (Exception $e){ 
            $this->error = $stmt->errorInfo();
            return null;
        }
    }

    function create(){
 
        // query to insert record
        $query = "INSERT INTO " . $this->table_name . "
            SET orderID=:orderID, fromWarehouseID=:fromWarehouseID, toWarehouseID=:toWarehouseID, date=:date";
 
        // prepare query
        $stmt = $this->conn->prepare($query);
 
        // sanitize
        $this->orderID = $this->generate();
        $this->fromWarehouseID = htmlspecialchars(strip_tags($this->fromWarehouseID));
        $this->toWarehouseID = htmlspecialchars(strip_tags($this->toWarehouseID));
        $this->date = htmlspecialchars(strip_tags($this->date));

        // bind values
        $stmt->bindParam(":orderID", $this->orderID);
        $stmt->bindParam(":fromWarehouseID", $this->fromWarehouseID);
        $stmt->bindParam(":toWarehouseID", $this->toWarehouseID);
        $stmt->bindParam(":date", $this->date);
        
        // execute query
        try{    
            $stmt->execute();
            
            return array("OrderID" => $this->orderID,
                        "Reference" => "http://petprojects.altervista.org/".$this->fromWarehouseID."/outbound/".$this->orderID."/");

        }
        catch (Exception $e){ 
            $this->error = $stmt->errorInfo();
            return null;
        }
    }
}

?>
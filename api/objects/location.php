<?php

class Location extends IDGenerator{

    // database connection and table name
    private $conn;
    private $table_name = "location";
    private $error;

    // object properties
    public $locationID;
    public $name;
    public $warehouseID;

    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    public function getError() {
        return $this->error;
    }

    function getList() {

         // select all query
         $query = "SELECT locationID FROM ". $this->table_name ." WHERE warehouseID=:warehouseID";
    
         // prepare query
         $stmt = $this->conn->prepare($query);
  
         // sanitize
         $this->warehouseID = htmlspecialchars(strip_tags($this->warehouseID));
       
         // bind values
         $stmt->bindParam(":warehouseID", $this->warehouseID);
 
         // execute query
         try{    
            $stmt->execute();
            if($stmt->rowCount() == 0) throw new Exception;
             
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $data = array();
            $i = 0;
            foreach ($rows as $row){
                $data[$i]['ID'] = $row['locationID'];
                $data[$i]['Reference'] = "http://petprojects.altervista.org/" .$this->warehouseID. "/location/".$row['locationID']."/";
                $i++;
            }

            return $data;
        }
        catch (Exception $e){ 
            $this->error = $stmt->errorInfo();
            return null;
        }    
    }

    function read(){
    
        // select all query
        $query = "SELECT * FROM ". $this->table_name ." WHERE locationID=:locationID AND warehouseID=:warehouseID";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
 
        // sanitize
        $this->locationID = htmlspecialchars(strip_tags($this->locationID));
        $this->warehouseID = htmlspecialchars(strip_tags($this->warehouseID));
      
        // bind values
        $stmt->bindParam(":locationID", $this->locationID);
        $stmt->bindParam(":warehouseID", $this->warehouseID);

        // execute query
        try{    
            $stmt->execute();
            if($stmt->rowCount() == 0) throw new Exception;

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
           
            $data = array();
            $data['name'] = $row['name'];
            $data['warehouse']['ID'] = $row['warehouseID'];
            $data['warehouse']['Reference'] = "http://petprojects.altervista.org/warehouse/" .$row['warehouseID']."/";

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
            SET locationID=:locationID, name=:name, warehouseID=:warehouseID";
 
        // prepare query
        $stmt = $this->conn->prepare($query);
 
        // sanitize
        $this->locationID = $this->generate();
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->warehouseID = htmlspecialchars(strip_tags($this->warehouseID));

        // bind values
        $stmt->bindParam(":locationID", $this->locationID);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":warehouseID", $this->warehouseID);
        
        // execute query
        try{    
            $stmt->execute();

            return array("ID" => $this->locationID,
                        "Reference" => "http://petprojects.altervista.org/".$this->warehouseID."/location/".$this->locationID."/");
    
        }catch (Exception $e){ 
            $this->error = $stmt->errorInfo();
            return null;
        }
    }
}

?>
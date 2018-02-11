<?php

class Warehouse extends IDGenerator{

    // database connection and table name
    private $conn;
    private $table_name = "warehouse";
    private $error;

    // object properties
    public $warehouseID;
    public $name;
    public $city;
    public $prov;
    public $country;

    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    public function getError() {
        return $this->error;
    }

    public function getList(){
    
        // select all query
        $query = "SELECT warehouseID FROM ". $this->table_name;
    
        // prepare query
        $stmt = $this->conn->prepare($query);
 
        // execute query
        try{    
            $stmt->execute();
            
            if($stmt->rowCount() == 0) throw new Exception;

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $data = array();
            $i = 0;
            foreach ($rows as $row){
                $data[$i]['ID'] = $row['warehouseID'];
                $data[$i]['Reference'] = "http://petprojects.altervista.org/warehouse/".$row['warehouseID']."/";
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
        $query = "SELECT * FROM ". $this->table_name ." WHERE warehouseID=:warehouseID";
    
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

            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        catch (Exception $e){ 
            $this->error = $stmt->errorInfo();
            return false;
        }
    }

    function create(){
 
        // query to insert record
        $query = "INSERT INTO " . $this->table_name . "
            SET warehouseID=:warehouseID, name=:name, city=:city, prov=:prov, country=:country";
 
        // prepare query
        $stmt = $this->conn->prepare($query);
 
        // sanitize
        $this->warehouseID = $this->generate();
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->city = htmlspecialchars(strip_tags($this->city));
        $this->prov = htmlspecialchars(strip_tags($this->prov));
        $this->country = htmlspecialchars(strip_tags($this->country));

        // bind values
        $stmt->bindParam(":warehouseID", $this->warehouseID);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":city", $this->city);
        $stmt->bindParam(":prov", $this->prov);
        $stmt->bindParam(":country", $this->country);
        
        // execute query
        try{    
            $stmt->execute();
            
            return array("ID" => $this->warehouseID,
                        "Reference" => "http://petprojects.altervista.org/warehouse/".$this->warehouseID."/");
        }
        catch (Exception $e){ 
            $this->error = $stmt->errorInfo();
            return null;
        }
    }
}

?>
<?php

class Catalog extends IDGenerator{

    // database connection and table name
    private $conn;
    private $table_name = "catalog";
    private $error;

    // object properties
    public $productID;
    public $name;
    public $model;
    public $description;
    public $type;

    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    public function getError() {
        return $this->error;
    }

    function create(){
 
        // query to insert record
        $query = "INSERT INTO " . $this->table_name . "
            SET productID=:productID, name=:name, model=:model, description=:description, type=:type";
 
        // prepare query
        $stmt = $this->conn->prepare($query);
 
        // sanitize
        $this->productID = $this->generate();
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->model = htmlspecialchars(strip_tags($this->model));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->type = htmlspecialchars(strip_tags($this->type));

        // bind values
        $stmt->bindParam(":productID", $this->productID);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":model", $this->model);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":type", $this->type);
        
        // execute query
        try{    
            $stmt->execute();
            
            return array("ID" => $this->productID,
                        "Reference" => "http://petprojects.altervista.org/catalog/".$this->productID."/");

        }
        catch (Exception $e){ 
            $this->error = $stmt->errorInfo();
            return null;
        }
    }

    function read(){
    
        // select all query
        $query = "SELECT * FROM ". $this->table_name ." WHERE productID=:productID";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
 
        // sanitize
        $this->productID = htmlspecialchars(strip_tags($this->productID));

        // bind values
        $stmt->bindParam(":productID", $this->productID);

        // execute query
        try{    
            $stmt->execute();
            if($stmt->rowCount() == 0) throw new Exception;
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        catch (Exception $e){ 
            $this->error = $stmt->errorInfo();
            return null;
        }
    }

    public function getList(){
    
        // select all query
        $query = "SELECT productID FROM ". $this->table_name;
    
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
                $data[$i]['ID'] = $row['productID'];
                $data[$i]['Reference'] = "http://petprojects.altervista.org/catalog/".$row['productID']."/";
                $i++;
            }

            return $data;
        }
        catch (Exception $e){ 
            $this->error = $stmt->errorInfo();
            return null;
        }
    }
}

?>
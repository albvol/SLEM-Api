<?php

class Employee {

    // database connection and table name
    private $conn;
    private $table_name = "employee";
    private $error;

    // object properties
    public $ID;
    public $name;
    public $surname;
    public $birthDate;
    public $roleID;
    public $warehouseID;
    public $mail;
    public $tokenID;

    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    public function getError() {
        return $this->error;
    }

    public function getList(){
    
        // select all query
        $query = "SELECT ID FROM ". $this->table_name ." WHERE warehouseID=:warehouseID";
    
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
                $data[$i]['ID'] = $row['ID'];
                $data[$i]['Reference'] = "http://petprojects.altervista.org/".$this->warehouseID."/employee/".$row['ID']."/";
                $i++;
            }

            return $data;
        }
        catch (Exception $e){ 
            $this->error = $stmt->errorInfo();
            return null;
        }
    }

    function update(){

        // update query
        $query = "UPDATE ". $this->table_name ." 
                SET tokenID=:tokenID 
                WHERE mail=:mail";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
 
        // sanitize
        $this->tokenID = htmlspecialchars(strip_tags($this->tokenID));
        $this->mail = htmlspecialchars(strip_tags($this->mail));
        
        // bind values
        $stmt->bindParam(":tokenID", $this->tokenID);
        $stmt->bindParam(":mail", $this->mail);

        
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

    function read(){
    
        // select all query
        $query = "SELECT * FROM ". $this->table_name ." WHERE ID=:ID AND warehouseID=:warehouseID";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
 
        // sanitize
        $this->ID = htmlspecialchars(strip_tags($this->ID));
        $this->warehouseID = htmlspecialchars(strip_tags($this->warehouseID));

        // bind values
        $stmt->bindParam(":ID", $this->ID);
        $stmt->bindParam(":warehouseID", $this->warehouseID);

        // execute query
        try{    
            $stmt->execute();
            if($stmt->rowCount() == 0) throw new Exception;

            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            
            return array("ID" => $data['ID'],
                        "surname" => $data['surname'],
                        "name" => $data['name'],
                        "birthDate" => $data['birthDate'],
                        "roleID" => $data['roleID'],
                        "mail" => $data['mail'],
                        "tokenID" => $data['tokenID'],
                        "warehouse" =>array("ID" => $data['warehouseID'],
                                            "Reference" => "http://petprojects.altervista.org/warehouse/".$this->warehouseID."/"));

        }
        catch (Exception $e){
            $this->error = $stmt->errorInfo(); 
            return null;
        }
    }

    function create(){
 
        // query to insert record
        $query = "INSERT INTO " . $this->table_name . "
            SET ID=:ID, name=:name, surname=:surname, birthDate=:birthDate, roleID=:roleID, warehouseID=:warehouseID, mail=:mail, tokenID=:tokenID";
 
        // prepare query
        $stmt = $this->conn->prepare($query);
 
        // sanitize
        $this->ID = htmlspecialchars(strip_tags($this->ID));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->surname = htmlspecialchars(strip_tags($this->surname));
        $this->birthDate = htmlspecialchars(strip_tags($this->birthDate));
        $this->roleID = htmlspecialchars(strip_tags($this->roleID));
        $this->mail = htmlspecialchars(strip_tags($this->mail));
        $this->tokenID = htmlspecialchars(strip_tags($this->tokenID));
        $this->warehouseID = htmlspecialchars(strip_tags($this->warehouseID));
 
        // bind values
        $stmt->bindParam(":ID", $this->ID);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":surname", $this->surname);
        $stmt->bindParam(":birthDate", $this->birthDate);
        $stmt->bindParam(":roleID", $this->roleID);
        $stmt->bindParam(":mail", $this->mail);
        $stmt->bindParam(":tokenID", $this->tokenID);
        $stmt->bindParam(":warehouseID", $this->warehouseID);
        
        // execute query
        try{    
            $stmt->execute();
            return array("ID" => $this->ID,
                        "Reference" => "http://petprojects.altervista.org/".$this->warehouseID."/employee/".$this->ID."/");

        }
        catch (Exception $e){ 
            $this->error = $stmt->errorInfo();
            return null;
        }
    }

    function check(){
    
        // select all query
        $query = "SELECT * FROM ". $this->table_name ." WHERE tokenID=:tokenID";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
 
        // sanitize
        $this->tokenID = htmlspecialchars(strip_tags($this->tokenID));

        // bind values
        $stmt->bindParam(":tokenID", $this->tokenID);

        // execute query
        try{    
            $stmt->execute();
            if($stmt->rowCount() == 0) throw new Exception;

            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            return true;

        }
        catch (Exception $e){
            $this->error = $stmt->errorInfo(); 
            return false;
        }
    }
}

?>
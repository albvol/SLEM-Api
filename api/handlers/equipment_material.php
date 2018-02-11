<?php

require_once("../config/SimpleRest.php");
require_once("../config/database.php");
require_once("../config/IDGenerator.php");
require_once("../objects/equipment_material.php");
require_once("../objects/location.php");
		
class EquipmentMaterialRestHandler extends SimpleRest {

	function getAll($warehouseID) {	

		// instantiate database
		$database = new Database();
		$db = $database->getConnection();
		
		$em = new EquipmentMaterial($db);
		$rawData = $em->getList($warehouseID);

		if(empty($rawData)) {
			$statusCode = 404;
			$rawData = array('error' => 'No serials found!',
							'mysql' => $em->getError());	
		} else {
			$statusCode = 200;
		}

		$requestContentType = $_SERVER['HTTP_ACCEPT'];
		$this ->setHttpHeaders($requestContentType, $statusCode);
				
		if(strpos($requestContentType,'application/json') !== false){
			$response = $this->encodeJson($rawData);
			echo $response;
		} else if(strpos($requestContentType,'text/html') !== false){
			$response = $this->encodeHtml($rawData);
			echo $response;
		} else if(strpos($requestContentType,'application/xml') !== false){
			$response = $this->encodeXml($rawData, "serials");
			echo $response;
		}
	}
		
	public function get($warehouseID, $serialID) {

		// instantiate database
		$database = new Database();
		$db = $database->getConnection();
		
		$em = new EquipmentMaterial($db);
		$em->serialID = $serialID;
		$rawData = $em->read($warehouseID);

		if(empty($rawData)) {
			$statusCode = 404;
			$rawData = array('error' => 'No serial found!',
							'mysql' => $em->getError());			
		} else {
			$statusCode = 200;
		}

		$requestContentType = $_SERVER['HTTP_ACCEPT'];
		$this ->setHttpHeaders($requestContentType, $statusCode);
				
		if(strpos($requestContentType,'application/json') !== false){
			$response = $this->encodeJson($rawData);
			echo $response;
		} else if(strpos($requestContentType,'text/html') !== false){
			$response = $this->encodeHtml($rawData);
			echo $response;
		} else if(strpos($requestContentType,'application/xml') !== false){
			$response = $this->encodeXml($rawData, "serial");
			echo $response;
		}
    }	
    		
	public function getSerialInLocation($warehouseID, $locationID) {

		// instantiate database
		$database = new Database();
		$db = $database->getConnection();
		
		$em = new EquipmentMaterial($db);
		$em->locationID = $locationID;
		$rawData = $em->getListInLocation($warehouseID);

		if(empty($rawData)) {
			$statusCode = 404;
			$rawData = array('error' => 'No serial found!',
							'mysql' => $em->getError());			
		} else {
			$statusCode = 200;
		}

		$requestContentType = $_SERVER['HTTP_ACCEPT'];
		$this ->setHttpHeaders($requestContentType, $statusCode);
				
		if(strpos($requestContentType,'application/json') !== false){
			$response = $this->encodeJson($rawData);
			echo $response;
		} else if(strpos($requestContentType,'text/html') !== false){
			$response = $this->encodeHtml($rawData);
			echo $response;
		} else if(strpos($requestContentType,'application/xml') !== false){
			$response = $this->encodeXml($rawData, "serial");
			echo $response;
		}
	}	

	public function set($warehouseID, $data) {

		// instantiate database
		$database = new Database();
		$db = $database->getConnection();
		
		$location = new Location($db);
		$location->locationID = $data->locationID;
		$location->warehouseID = $warehouseID;

		$rawData = $location->read();

		if(empty($rawData)){
			$statusCode = 404;
			$rawData = array('error' => 'This is not a location of this warehouse!',
							'mysql' => $location->getError());
		} else {

			$equipmentMaterial = new EquipmentMaterial($db);

			$equipmentMaterial->serialID = $data->serialID;
			$equipmentMaterial->productID = $data->productID;
			$equipmentMaterial->locationID = $data->locationID;
	
			$rawData = $equipmentMaterial->create($warehouseID);
	
			if(empty($rawData)) {
				$statusCode = 404;
				$rawData = array('error' => 'Cannot add a new serial!',
								'mysql' => $equipmentMaterial->getError());		
			} else {
				$statusCode = 200;
			}
		}

		$requestContentType = $_SERVER['HTTP_ACCEPT'];
		$this ->setHttpHeaders($requestContentType, $statusCode);
				
		if(strpos($requestContentType,'application/json') !== false){
			$response = $this->encodeJson($rawData);
			echo $response;
		} else if(strpos($requestContentType,'text/html') !== false){
			$response = $this->encodeHtml($rawData);
			echo $response;
		} else if(strpos($requestContentType,'application/xml') !== false){
			$response = $this->encodeXml($rawData, "equipment_material");
			echo $response;
		}
	}
}
?>
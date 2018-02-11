<?php

require_once("../config/SimpleRest.php");
require_once("../config/database.php");
require_once("../config/IDGenerator.php");
require_once("../objects/location.php");
		
class LocationRestHandler extends SimpleRest {

	function getAll($warehouseID) {	

		// instantiate database
		$database = new Database();
		$db = $database->getConnection();
		
        $location = new Location($db);
        $location->warehouseID = $warehouseID;
		$rawData = $location->getList();

		if(empty($rawData)) {
			$statusCode = 404;
			$rawData = array('error' => 'No locations found!',
							'mysql' => $location->getError());		
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
			$response = $this->encodeXml($rawData, "locations");
			echo $response;
		}
	}
		
	public function get($warehouseID, $locationID) {

		// instantiate database
		$database = new Database();
		$db = $database->getConnection();
		
        $location = new Location($db);
        $location->warehouseID = $warehouseID;
        $location->locationID = $locationID;
		$rawData = $location->read();

		if(empty($rawData)) {
			$statusCode = 404;
			$rawData = array('error' => 'No location found!',
							'mysql' => $location->getError());			
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
			$response = $this->encodeXml($rawData, "location");
			echo $response;
		}
	}	

	public function set($warehouseID, $data) {

		// instantiate database
		$database = new Database();
		$db = $database->getConnection();
		
        $location = new Location($db);
        $location->warehouseID = $warehouseID;
        $location->locationID = $data->locationID;
        $location->name = $data->name;

		$rawData = $location->create();

		if(empty($rawData)) {
			$statusCode = 404;
			$rawData = array('error' => 'Cannot create the location!',
							'mysql' => $location->getError());			
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
			$response = $this->encodeXml($rawData, "location");
			echo $response;
		}
	}
}
?>
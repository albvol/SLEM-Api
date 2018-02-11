<?php

require_once("../config/SimpleRest.php");
require_once("../config/database.php");
require_once("../config/IDGenerator.php");
require_once("../objects/warehouse.php");
		
class WarehouseRestHandler extends SimpleRest {

	function getAll() {	

		// instantiate database
		$database = new Database();
		$db = $database->getConnection();
		
		$warehouse = new Warehouse($db);
		$rawData = $warehouse->getList();

		if(empty($rawData)) {
			$statusCode = 404;
			$rawData = array('error' => 'No warehouses found!',
							'mysql' => $warehouse->getError());		
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
			$response = $this->encodeXml($rawData, "warehouses");
			echo $response;
		}
	}
		
	public function get($id) {

		// instantiate database
		$database = new Database();
		$db = $database->getConnection();
		
		$warehouse = new Warehouse($db);
		$warehouse->warehouseID = $id;
		$rawData = $warehouse->read();

		if(empty($rawData)) {
			$statusCode = 404;
			$rawData = array('error' => 'No warehouse found!',
							'mysql' => $warehouse->getError());		
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
			$response = $this->encodeXml($rawData, "warehouse");
			echo $response;
		}
	}	

	public function set($data) {

		// instantiate database
		$database = new Database();
		$db = $database->getConnection();
		
		$warehouse = new Warehouse($db);

		$warehouse->name = $data->name;
		$warehouse->city = $data->city;
		$warehouse->prov = $data->prov;
		$warehouse->country = $data->country;

		$rawData = $warehouse->create();

		if(empty($rawData)) {
			$statusCode = 404;
			$rawData = array('error' => 'Cannot create the warehouse!',
							'mysql' => $warehouse->getError());			
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
			$response = $this->encodeXml($rawData, "warehouse");
			echo $response;
		}
	}
}
?>
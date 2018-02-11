<?php

require_once("../config/SimpleRest.php");
require_once("../config/database.php");
require_once("../config/IDGenerator.php");
require_once("../objects/catalog.php");
		
class CatalogRestHandler extends SimpleRest {

	function getAll() {	

		// instantiate database
		$database = new Database();
		$db = $database->getConnection();
		
		$catalog = new Catalog($db);
		$rawData = $catalog->getList();

		if(empty($rawData)) {
			$statusCode = 404;
			$rawData = array('error' => 'No products found!',
							'mysql' => $catalog->getError());			
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
			$response = $this->encodeXml($rawData, "products");
			echo $response;
		}
	}
		
	public function get($id) {

		// instantiate database
		$database = new Database();
		$db = $database->getConnection();
		
		$catalog = new Catalog($db);
		$catalog->productID = $id;
		$rawData = $catalog->read();

		if(empty($rawData)) {
			$statusCode = 404;
			$rawData = array('error' => 'No product found!',
							'mysql' => $catalog->getError());			
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
			$response = $this->encodeXml($rawData, "product");
			echo $response;
		}
	}	

	public function set($data, $type) {

		// instantiate database
		$database = new Database();
		$db = $database->getConnection();
		
		$catalog = new Catalog($db);
		$catalog->type = $type;
		$catalog->name = $data->name;
		$catalog->model = $data->model;
		$catalog->description = $data->description;

		$rawData = $catalog->create();

		if(empty($rawData)) {
			$statusCode = 404;
			$rawData = array('error' => 'Cannot create the product!',
							'mysql' => $catalog->getError());			
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
			$response = $this->encodeXml($rawData, "product");
			echo $response;
		}
	}
}
?>
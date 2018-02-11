<?php

require_once("../config/SimpleRest.php");
require_once("../config/database.php");
require_once("../config/IDGenerator.php");
require_once("../objects/orders.php");
		
class OrdersRestHandler extends SimpleRest {

	function getAll($warehouseID, $type) {	

		// instantiate database
		$database = new Database();
		$db = $database->getConnection();
		
		$order = new Order($db);
		
		if($type == "inbound"){
			$order->toWarehouseID = $warehouseID;
			$rawData = $order->readInbound();
		}else {
			$order->fromWarehouseID = $warehouseID;
			$rawData = $order->readOutbound();
		}

		if(empty($rawData)) {
			$statusCode = 404;
			$rawData = array('error' => 'No orders found!',
							'mysql' => $order->getError());		
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
			$response = $this->encodeXml($rawData, "orders");
			echo $response;
		}
	}
		
	public function get($warehouseID, $orderID) {

		// instantiate database
		$database = new Database();
		$db = $database->getConnection();
		
		$order = new Order($db);
		$order->orderID = $orderID;
		$order->toWarehouseID = $warehouseID;
		$order->fromWarehouseID = $warehouseID;
		$rawData = $order->get();

		if(empty($rawData)) {
			$statusCode = 404;
			$rawData = array('error' => 'No order found!',
				'd' => $order,
							'mysql' => $order->getError());		
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
			$response = $this->encodeXml($rawData, "order");
			echo $response;
		}
	}	

	public function set($data, $fromWarehouseID) {

		// instantiate database
		$database = new Database();
		$db = $database->getConnection();
		
		$order = new Order($db);

		$order->fromWarehouseID = $fromWarehouseID;
		$order->toWarehouseID = $data->toWarehouseID;
		$order->date = $data->date;

		$rawData = $order->create();

		if(empty($rawData)) {
			$statusCode = 404;
			$rawData = array('error' => 'Cannot create the order!',
							'mysql' => $order->getError());			
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
			$response = $this->encodeXml($rawData, "order");
			echo $response;
		}
	}
}
?>
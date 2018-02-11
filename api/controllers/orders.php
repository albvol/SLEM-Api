<?php
require_once("../config/checkToken.php");
require_once("../handlers/orders.php");
		
$view = "";
if(isset($_GET["view"]))
	$view = $_GET["view"];

/*
controls the RESTful services
URL mapping
*/
switch($view){

	case "all":
        // to handle REST Url /{warehouseID}/inbound/list/
		// to handle REST Url /{warehouseID}/outbound/list/
		$orderRestHandler = new OrdersRestHandler();
		$orderRestHandler->getAll($_GET["warehouseID"], $_GET["type"]);
		break;
		
	case "single":
        // to handle REST Url /{warehouseID}/inbound/{orderID}/
		// to handle REST Url /{warehouseID}/outbound/{orderID}/
		$orderRestHandler = new OrdersRestHandler();
		$orderRestHandler->get($_GET["warehouseID"], $_GET["orderID"]);
		break;

	case "new":
		// to handle REST Url /{warehouseID}/outbound/
		$orderRestHandler = new OrdersRestHandler();
		$orderRestHandler->set(json_decode(file_get_contents("php://input")), $_GET["warehouseID"]);
		break;
	
	case "" :
		//404 - not found;
		break;
}
?>

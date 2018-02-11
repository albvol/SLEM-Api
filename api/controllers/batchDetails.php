<?php
require_once("../config/checkToken.php");
require_once("../handlers/batchDetails.php");
	
$view = "";
if(isset($_GET["view"]))
	$view = $_GET["view"];
	
/*
controls the RESTful services
URL mapping
*/
switch($view){

	case "all":
        // to handle REST Url {warehouseID}/inbound/{orderID}/batch/{batchID}/details/list/
		// to handle REST Url {warehouseID}/outbound/{orderID}/batch/{batchID}/details/list/
		$batchRestHandler = new BatchDetailsRestHandler();
		$batchRestHandler->getAll($_GET["warehouseID"], $_GET["orderID"], $_GET["batchID"], $_GET["type"]);
		break;
		
	case "single":
        // to handle REST Url {warehouseID}/inbound/{orderID}/batch/{batchID}/
		// to handle REST Url {warehouseID}/outbound/{orderID}/batch/{batchID}/
		$batchRestHandler = new BatchDetailsRestHandler();
		$batchRestHandler->get($_GET["warehouseID"], $_GET["orderID"], $_GET["batchID"], $_GET["type"]);
		break;

	case "new":
		// to handle REST Url {warehouseID}/outbound/{orderID}/batch/{batchID}/details/
		$batchRestHandler = new BatchDetailsRestHandler();
		$batchRestHandler->set($_GET["warehouseID"], $_GET["orderID"], $_GET["batchID"], json_decode(file_get_contents("php://input")));
		break;
	
	case "":
		//404 - not found;
		break;
}
?>

<?php
require_once("../config/checkToken.php");
require_once("../handlers/batch.php");
	
$view = "";
if(isset($_GET["view"]))
	$view = $_GET["view"];

/*
controls the RESTful services
URL mapping
*/
switch($view){

	case "all":
        // to handle REST Url {warehouseID}/inbound/{orderID}/batch/list/
		// to handle REST Url {warehouseID}/outbound/{orderID}/batch/list/
		$batchRestHandler = new BatchRestHandler();
		$batchRestHandler->getAll($_GET["warehouseID"], $_GET["orderID"], $_GET["type"]);
		break;
		
	case "single":
        // to handle REST Url {warehouseID}/inbound/{orderID}/batch/{batchID}/
		// to handle REST Url {warehouseID}/outbound/{orderID}/batch/{batchID}/
		$batchRestHandler = new BatchRestHandler();
		$batchRestHandler->get($_GET["warehouseID"], $_GET["orderID"], $_GET["batchID"], $_GET["type"]);
		break;

	case "new":
		// to handle REST Url {warehouseID}/outbound/{orderID}/batch/
		$batchRestHandler = new BatchRestHandler();
		$batchRestHandler->set($_GET["warehouseID"], $_GET["orderID"], json_decode(file_get_contents("php://input")));
		break;

	case "updateInbound":
		// to handle REST Url {warehouseID}/outbound/{orderID}/batch/{batchID}/arrived/
		$batchRestHandler = new BatchRestHandler();
		$batchRestHandler->updateInbound($_GET["warehouseID"], $_GET["orderID"], $_GET["batchID"], json_decode(file_get_contents("php://input")));
		break;

	case "updateOutbound":
		// to handle REST Url {warehouseID}/outbound/{orderID}/batch/{batchID}/completed/
		$batchRestHandler = new BatchRestHandler();
		$batchRestHandler->updateOutbound($_GET["warehouseID"], $_GET["orderID"], $_GET["batchID"], json_decode(file_get_contents("php://input")));
		break;
	
	case "":
		//404 - not found;
		break;
}
?>

<?php
require_once("../config/checkToken.php");
require_once("../handlers/location.php");
		
$view = "";
if(isset($_GET["view"]))
	$view = $_GET["view"];

/*
controls the RESTful services
URL mapping
*/
switch($view){

	case "all":
		// to handle REST Url {warehouseID}/location/list/
		$locationRestHandler = new LocationRestHandler();
		$locationRestHandler->getAll($_GET["warehouseID"]);
		break;
		
	case "single":
		// to handle REST Url {warehouseID}/location/{locationID}/
		$locationRestHandler = new LocationRestHandler();
		$locationRestHandler->get($_GET["warehouseID"], $_GET["locationID"]);
		break;

	case "new":
		// to handle REST Url /location/
		$locationRestHandler = new LocationRestHandler();
		$locationRestHandler->set($_GET["warehouseID"], json_decode(file_get_contents("php://input")));
		break;
	
	case "" :
		//404 - not found;
		break;
}
?>

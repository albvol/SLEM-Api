<?php
require_once("../config/checkToken.php");
require_once("../handlers/warehouse.php");
		
$view = "";
if(isset($_GET["view"]))
	$view = $_GET["view"];

/*
controls the RESTful services
URL mapping
*/
switch($view){

	case "all":
		// to handle REST Url /warehouse/list/
		$warehouseRestHandler = new WarehouseRestHandler();
		$warehouseRestHandler->getAll();
		break;
		
	case "single":
		// to handle REST Url /warehouse/<id>/
		$warehouseRestHandler = new WarehouseRestHandler();
		$warehouseRestHandler->get($_GET["id"]);
		break;

	case "new":
		// to handle REST Url /warehouse/
		$warehouseRestHandler = new WarehouseRestHandler();
		$warehouseRestHandler->set(json_decode(file_get_contents("php://input")));
		break;
	
	case "" :
		//404 - not found;
		break;
}
?>

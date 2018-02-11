<?php
require_once("../config/checkToken.php");
require_once("../handlers/catalog.php");
		
$view = "";
if(isset($_GET["view"]))
	$view = $_GET["view"];

/*
controls the RESTful services
URL mapping
*/
switch($view){

	case "all":
		// to handle REST Url /catalog/list/
		$catalogRestHandler = new CatalogRestHandler();
		$catalogRestHandler->getAll();
		break;
		
	case "single":
		// to handle REST Url /catalog/{productID}/
		$catalogRestHandler = new CatalogRestHandler();
		$catalogRestHandler->get($_GET["productID"]);
		break;

    case "new":
		// to handle REST Url /catalog/material/
		// to handle REST Url /catalog/equipment/
		$catalogRestHandler = new CatalogRestHandler();
		$catalogRestHandler->set(json_decode(file_get_contents("php://input")), $_GET["type"]);
		break;
	
	case "" :
		//404 - not found;
		break;
}
?>

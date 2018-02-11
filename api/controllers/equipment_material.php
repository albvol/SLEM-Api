<?php
require_once("../config/checkToken.php");
require_once("../handlers/equipment_material.php");

$view = "";
if(isset($_GET["view"]))
	$view = $_GET["view"];

/*
controls the RESTful services
URL mapping
*/
switch($view){

	case "all":
		// to handle REST Url {warehouseID}/equipment_material/list/
		$emRH = new EquipmentMaterialRestHandler();
		$emRH->getAll($_GET["warehouseID"]);
		break;
		
	case "single":
		// to handle REST Url {warehouseID}/equipment_material/{serialID}/
		$emRH = new EquipmentMaterialRestHandler();
		$emRH->get($_GET["warehouseID"], $_GET["serialID"]);
		break;

	case "serialInLocation":
		// to handle REST Url {warehouseID}/equipment_material/{locationID}/list/
		$emRH = new EquipmentMaterialRestHandler();
		$emRH->getSerialInLocation($_GET["warehouseID"], $_GET["locationID"]);
        break;

	case "new":
		// to handle REST Url {warehouseID}/equipment_material/
		$emRH = new EquipmentMaterialRestHandler();
		$emRH->set($_GET["warehouseID"], json_decode(file_get_contents("php://input")));
        break;
          
	case "" :
		//404 - not found;
		break;
}
?>

<?php
require_once("../config/checkToken.php");
require_once("../handlers/employee.php");
		
$view = "";
if(isset($_GET["view"]))
	$view = $_GET["view"];

/*
controls the RESTful services
URL mapping
*/
switch($view){

	case "all":
		// to handle REST Url {warehouseID}/employee/list/
		$employeeRestHandler = new EmployeeRestHandler();
		$employeeRestHandler->getAll($_GET["warehouseID"]);
		break;
		
	case "single":
		// to handle REST Url {warehouseID}/employee/{employeeID}/
		$employeeRestHandler = new EmployeeRestHandler();
		$employeeRestHandler->get($_GET["employeeID"], $_GET["warehouseID"]);
		break;

	case "new":
		// to handle REST Url /employee/
		$employeeRestHandler = new EmployeeRestHandler();
		$employeeRestHandler->set(json_decode(file_get_contents("php://input")));
		break;

	case "update":
		// to handle REST Url auth/
		$employeeRestHandler = new EmployeeRestHandler();
		$employeeRestHandler->update(json_decode(file_get_contents("php://input")));
		break;
	
	case "" :
		//404 - not found;
		break;
}
?>

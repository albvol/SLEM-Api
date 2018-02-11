<?php

require_once("../config/SimpleRest.php");
require_once("../config/database.php");
require_once("../config/IDGenerator.php");
require_once("../objects/employee.php");
		
class EmployeeRestHandler extends SimpleRest {

	function getAll($warehouseID) {	

		// instantiate database
		$database = new Database();
		$db = $database->getConnection();
		
		$employee = new Employee($db);
		$employee->warehouseID = $warehouseID;
		$rawData = $employee->getList();

		if(empty($rawData)) {
			$statusCode = 404;
			$rawData = array('error' => 'No employees found!',
							'mysql' => $employee->getError());	
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
			$response = $this->encodeXml($rawData, "employees");
			echo $response;
		}
	}
		
	public function get($employeeID, $warehouseID) {

		// instantiate database
		$database = new Database();
		$db = $database->getConnection();
		
		$employee = new Employee($db);
		$employee->warehouseID = $warehouseID;
		$employee->ID = $employeeID;
		$rawData = $employee->read();

		if(empty($rawData)) {
			$statusCode = 404;
			$rawData = array('error' => 'No employee found!',
							'mysql' => $employee->getError());			
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
			$response = $this->encodeXml($rawData, "employee");
			echo $response;
		}
	}	

	public function set($data) {

		// instantiate database
		$database = new Database();
		$db = $database->getConnection();
		
		$employee = new Employee($db);

		$employee->name = $data->name;
		$employee->ID = $data->ID;
		$employee->surname = $data->surname;
		$employee->birthDate = $data->birthDate;
		$employee->roleID = $data->roleID;
		$employee->warehouseID = $data->warehouseID;
		$employee->mail = $data->mail;
		$employee->tokenID = $data->tokenID;

		$rawData = $employee->create();

		if(empty($rawData)) {
			$statusCode = 404;
			$rawData = array('error' => 'Can not create an employee!',
							'mysql' => $employee->getError());		
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
			$response = $this->encodeXml($rawData, "employee");
			echo $response;
		}
	}


	public function update($data) {

		// instantiate database
		$database = new Database();
		$db = $database->getConnection();
		
		$employee = new Employee($db);

		$employee->mail = $data->mail;
		$employee->tokenID = $data->tokenID;

		$rawData = $employee->update();

		if(empty($rawData)) {
			$statusCode = 404;
			$rawData = array('error' => 'Can not update the tokenID of the employee!',
							'mysql' => $employee->getError());		
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
			$response = $this->encodeXml($rawData, "employee");
			echo $response;
		}
	}
}
?>
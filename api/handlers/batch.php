<?php

require_once("../config/SimpleRest.php");
require_once("../config/database.php");
require_once("../config/IDGenerator.php");
require_once("../objects/batch.php");
require_once("../objects/orders.php");
require_once("../objects/employee.php");
require_once("../objects/batchDetails.php");
require_once("../objects/equipment_material.php");
		
class BatchRestHandler extends SimpleRest {

	function getAll($warehouseID, $orderID, $type) {	

		// instantiate database
		$database = new Database();
		$db = $database->getConnection();
		
		$batch = new Batch($db);
        $batch->orderID = $orderID;
        
        if($type == "inbound") {
            $rawData = $batch->getListInbound($warehouseID);
        }else{
            $rawData = $batch->getListOutbound($warehouseID);
        }

		if(empty($rawData)) {
			$statusCode = 404;
			$rawData = array('error' => 'No batches found!',
							'mysql' => $batch->getError());	
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
			$response = $this->encodeXml($rawData, "batches");
			echo $response;
		}
	}
		
	public function get($warehouseID, $orderID, $batchID, $type) {

		// instantiate database
		$database = new Database();
		$db = $database->getConnection();
		
		$batch = new Batch($db);
		$batch->batchID = $batchID;
		$batch->orderID = $orderID;

        if($type == "inbound") {
            $rawData = $batch->getInbound($warehouseID);
        }else{
            $rawData = $batch->getOutbound($warehouseID);
        }

		if(empty($rawData)) {
			$statusCode = 404;
			$rawData = array('error' => 'No batch found!',
							'mysql' => $batch->getError());			
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
			$response = $this->encodeXml($rawData, "batch");
			echo $response;
		}
	}	

	public function set($warehouseID, $orderID, $data) {

		// instantiate database
		$database = new Database();
		$db = $database->getConnection();
		
		$batch = new Batch($db);

		$batch->quantity = $data->quantity;
		$batch->productID = $data->productID;
		$batch->orderID = $orderID;

		$rawData = $batch->find();
		if(empty($rawData)){
			$rawData = $batch->create($warehouseID);

			if(empty($rawData)) {
				$statusCode = 404;
				$rawData = array('error' => 'Can not create a batch!',
								'mysql' => $batch->getError());		
			} else {
				$statusCode = 200;
			}
		}else{
			$statusCode = 404;
			$rawData = array('error' => 'A batch relative to this order and this product altready exist! Update the quantity of that batch!',
							'mysql' => $batch->getError());		
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
			$response = $this->encodeXml($rawData, "batch");
			echo $response;
		}
	}

	public function updateInbound($warehouseID, $orderID, $batchID, $data) {

		// instantiate database
		$database = new Database();
		$db = $database->getConnection();
		
		$order = new Order($db);
		$order->orderID = $orderID;
		$order->toWarehouseID = $warehouseID;

		$isMyOrder = $order->getInbound();
		if(!empty($isMyOrder)){

			$employee = new Employee($db);
			$employee->ID = $data->inboundEmployeeID;
			$employee->warehouseID = $warehouseID;

			$rawData = $employee->read();
			if(!(empty($rawData)) && (($rawData['roleID'] == 0) || ($rawData['roleID'] == 2))){

				$batch = new Batch($db);

				$batch->batchID = $batchID;
				$batch->inboundEmployeeID = $data->inboundEmployeeID;
				$batch->orderID = $orderID;
		
				$isUpdated = $batch->addInbound();
				if($isUpdated){
					
					//READ ALL THE ELEMENTS THAT ARE IN THE BATCH
					$batchDetails = new BatchDetails($db);
					$batchDetails->batchID = $batchID;
					$rawData = $batchDetails->getListInbound($warehouseID, $orderID);
					
					if(!empty($rawData)) {
						
						$x = true;
						foreach ($rawData as $row)
						{
							$em = new EquipmentMaterial($db);
							$em->serialID = $row['serial']['ID'];
							$em->status = "Available";
							$em->locationID = $data->locationID;
							$rawData = $em->updateLocationAndStatus();

							if(!$rawData){
								$x = false;
							}

						}

						if(!$x){
							$statusCode = 404;
							$rawData = array('error' => 'I can\'t update the status and the location!',
											'mysql' => $batchDetails->getError());	
						}else{
							$statusCode = 200;
							$rawData = true;
						}

					} else {
						$statusCode = 404;
						$rawData = array('error' => 'I don\'t kown the serials of this batch to update the location and the status!',
										'mysql' => $batchDetails->getError(),
									'rawData' >= $rawData,
								'v' => [$batchDetails,$warehouseID, $orderID]);		
					}
				}else{
					$statusCode = 404;
					$rawData = array('error' => 'I can\'t update the status of this batch!',
									'mysql' => $batch->getError());		
				}
			}else{
				$statusCode = 404;
				$rawData = array('error' => 'You cannot make this operation!',
								'mysql' => $employee->getError());		
			}
		
		}else{
			$statusCode = 404;
			$rawData = array('error' => 'This is not your order!!',
							'mysql' => $order->getError());		
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
			$response = $this->encodeXml($rawData, "batch");
			echo $response;
		}
	}

	public function updateOutbound($warehouseID, $orderID, $batchID, $data) {

		// instantiate database
		$database = new Database();
		$db = $database->getConnection();
		
		$order = new Order($db);
		$order->orderID = $orderID;
		$order->fromWarehouseID = $warehouseID;

		$isMyOrder = $order->getOutbound();
		if(!empty($isMyOrder)){

			$employee = new Employee($db);
			$employee->ID = $data->outboundEmployeeID;
			$employee->warehouseID = $warehouseID;

			$rawData = $employee->read();
			if(!(empty($rawData)) && (($rawData['roleID'] == 1) || ($rawData['roleID'] == 2))){

				$batch = new Batch($db);

				$batch->batchID = $batchID;
				$batch->outboundEmployeeID = $data->outboundEmployeeID;
				$batch->orderID = $orderID;
		
				$isUpdated = $batch->addOutbound();
				if($isUpdated){

					//READ ALL THE ELEMENTS THAT ARE IN THE BATCH
					$batchDetails = new BatchDetails($db);
					$batchDetails->batchID = $batchID;
					$rawData = $batchDetails->getListOutbound($warehouseID, $orderID);
					
					if(!empty($rawData)) {
						
						
						foreach ($rawData as $row)
						{
							$em = new EquipmentMaterial($db);
							$em->serialID = $row['serial']['ID'];
							$em->status = "Ordered";
							$rawData = $em->updateStatus();

							if(!$rawData){
								$statusCode = 404;
								$rawData = array('error' => 'I don\'t kown the serials of this batch to update the location and the status!',
												'mysql' => $batchDetails->getError(),
												'b'=> $batchDetails);	
							}else $statusCode = 200;
						}

					} else {
						$statusCode = 404;
						$rawData = array('error' => 'I don\'t kown the serials of this batch to update the location and the status!',
										'mysql' => $batchDetails->getError(),
										'b'=> $batchDetails);		
					}
				}else{
					$statusCode = 404;
					$rawData = array('error' => 'I can\'t update the status of this batch!',
									'mysql' => $batch->getError());		
				}
			}else{
				$statusCode = 404;
				$rawData = array('error' => 'You cannot make this operation!',
								'mysql' => $employee->getError());		
			}
		
		}else{
			$statusCode = 404;
			$rawData = array('error' => 'This is not your order!!',
							'mysql' => $order->getError());		
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
			$response = $this->encodeXml($rawData, "batch");
			echo $response;
		}
	}
}
?>
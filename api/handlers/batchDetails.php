<?php

require_once("../config/SimpleRest.php");
require_once("../config/database.php");
require_once("../config/IDGenerator.php");
require_once("../objects/batchDetails.php");
require_once("../objects/orders.php");
require_once("../objects/equipment_material.php");
		
class BatchDetailsRestHandler extends SimpleRest {

	function getAll($warehouseID, $orderID, $batchID, $type) {	

		// instantiate database
		$database = new Database();
		$db = $database->getConnection();
        
		$order = new Order($db);
        $order->orderID = $orderID;

        if($type == "inbound"){
            $order->toWarehouseID = $warehouseID;
            $rawData = $order->readInbound();
        }else{
            $order->fromWarehouseID = $warehouseID;
            $rawData = $order->readOutbound();
        }

        if(empty($rawData)){
			$statusCode = 404;
			$rawData = array('error' => 'This order is not relative to your warehouse!',
							'mysql' => $order->getError());	
		} else {

            $batchDetails = new BatchDetails($db);
            $batchDetails->batchID = $batchID;
            
            if($type == "inbound") {
                $rawData = $batchDetails->getListInbound($orderID);
            }else{
                $rawData = $batchDetails->getListOutbound($warehouseID, $orderID);
            }

            if(empty($rawData)) {
                $statusCode = 404;
                $rawData = array('error' => 'No batch details found!',
                                'mysql' => $batchDetails->getError());	
            } else {
                $statusCode = 200;
            }
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
			$response = $this->encodeXml($rawData, "batchDetails");
			echo $response;
		}
	}

	public function set($warehouseID, $orderID, $batchID, $data) {

		// instantiate database
		$database = new Database();
        $db = $database->getConnection();
        
        $em = new EquipmentMaterial($db);
        $em->serialID = $data->serialID;
        $rawData = $em->read($warehouseID);

        if(!empty($rawData)){

            $batchDetails = new BatchDetails($db);
            $batchDetails->batchID = $batchID;
            $batchDetails->serialID = $data->serialID;
            $batchDetails->fromLocationID = $rawData['location']['ID'];

            $rawData = $batchDetails->create($warehouseID, $orderID);
            if(empty($rawData)){
                $statusCode = 404;
                $rawData = array('error' => 'The batch contains already this element!',
                                  'mysql' => $batchDetails->getError());	
            }else{
                $em->status = "Ordered";
                $em->updateStatus();

                $statusCode = 200;
            }

        }else {
			$statusCode = 404;
            $rawData = array('error' => 'This serial ID is not in this warehouse!',
								'mysql' => $em->getError());	
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
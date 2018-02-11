<?php

require_once("../objects/employee.php");
require_once("../config/SimpleRest.php");
require_once("../config/database.php");

class CheckBearer extends SimpleRest{
    private function getAuthorizationHeader(){
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }

    /**
    * get access token from header
    * */
    private function getBearerToken() {
        $headers = $this->getAuthorizationHeader();
        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }

    public function verify(){
        
        $token = $this->getBearerToken();
        
        $database = new Database();
		$db = $database->getConnection();
        
        $employee = new Employee($db);
        $employee->tokenID = $token;
        if(!$employee->check()){
            $statusCode = 404;
            $rawData = array('error' => 'Invalid Token');	

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
            die();
        }
    }
}

$o = new CheckBearer();
$o->verify();
?>
<?php 
class Auth{

    private $user_gateway;

    public function __construct( UserGateway $user_gateway )
    {
        $this->user_gateway = $user_gateway;
    }

    public function authenticateAPIKey(): bool{

        //$api_key = $_GET["api-key"];
        // echo $api_key;
        // http https//localhost/api/tasks?api-key=abc
        // print_r($_SERVER);
        if( empty($_SERVER["HTTP_X_API_KEY"])){

            http_response_code(400);
            echo json_encode(["message"=>"missing API key"]);
            return false;

        }

        $api_key = $_SERVER["HTTP_X_API_KEY"];

        if( $this->user_gateway->getByAPIKey( $api_key)===false){
            http_response_code(401);
            echo json_encode(["message"=>'Invalid API key']);
            return false;
        }

        return true;
        
        // echo $api_key;
        
        // exit;
    }
}
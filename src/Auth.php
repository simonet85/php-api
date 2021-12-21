<?php 
class Auth{

    private $user_gateway;
    private int $user_id;
    private  $codec;

    public function __construct( UserGateway $user_gateway, JWTCodec $codec )
    {
        $this->user_gateway = $user_gateway;
        $this->codec = $codec;
    }

    //Authenticate access token
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

        $user = $this->user_gateway->getByAPIKey( $api_key);

        if(  $user === false ){
            http_response_code(401);
            echo json_encode(["message"=>'Invalid API key']);
            return false;
        }

        $this->user_id = $user["id"];

        return true;
        
    }

    //Get the user ID
    public function getUserID() : int{
        return $this->user_id;
    }

    public function authenticateAccessToken() :bool{
        if( !  preg_match("/^Bearer\s+(.*)$/",$_SERVER["HTTP_AUTHORIZATION"], $matches)){
            http_response_code(400);
            echo json_encode(["message"=>"incomplete authorization header"]);
            return false;
        }

    /**
         * Authorization usin access token
         * 
        
        * $plain_text = base64_decode($matches[1], true);
        * if($plain_text === false){

        *     http_response_code(400);
        *     echo json_encode(["message"=>"invalid authorization header"]);
        *     return false;
        * }
        * When TRUE, returned objects will be converted into associative arrays.
        * $data = json_decode($plain_text, true);

        * if ($data === null) {
        *     http_response_code(400);
        *     echo json_encode(["message"=>"invalid JSON"]);
        * }
 */
  
        try {

            //Get the user data from the payload
            $data = $this->codec->decode( $matches[1] );

        } catch (InvalidSignatureException | Exception $e) { //Catch invalid Signature exception with the correct http response code

            http_response_code( 401 );
            echo json_encode(["message" => "Invalid signature"]);
            return false;

        } catch (Exception $e) {

            http_response_code( 400 );
            echo json_encode(["message" => $e->getMessage()]);
            return false;

        } 

        
        $this->user_id = $data["sub"]; //sub is equivalent to ID

        return true;
      
    }
}
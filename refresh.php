<?php 
    declare(strict_types=1);

    require __DIR__ . "./bootstrap.php";

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        http_response_code(405);
        header("Allow : POST");
        
        exit;
    }

    $data = (array) json_decode(file_get_contents("php://input"), true); // Gives an associtive array, read the content of php input stream  and convert it into an array

    //Check if the username name and password exists in $data array

    if (! array_key_exists("token", $data) ){
    
        http_response_code(400);
        echo json_encode(["message"=>"missing token"]);
        exit;
    }

    //Decode the refresh token  
    //Throw exception if any
    $codec = new JWTCodec( $_ENV["SECRET_KEY"] );
    try{

        $payload = $codec->decode( $data["token"] );

    }catch( Exception $e){

        http_response_code(400);
        echo json_encode(["message"=>"invlid token"]);
        exit;

    }

    $user_id = $payload["sub"];
    var_dump( $user_id );

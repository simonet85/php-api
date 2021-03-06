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

if (! array_key_exists("username", $data) || 
( ! array_key_exists("password", $data))) {
   
    http_response_code(400);
    echo json_encode(["message"=>"missing login credentials"]);
    exit;
}

//Instanciate the database
$database = new Database(
    $_ENV["DB_HOST"],
    $_ENV["DB_NAME"],
    $_ENV["DB_USER"],
    $_ENV["DB_PASS"]
);

//Pass the database object to the UserGateway constructor 
$user_gateway = new UserGateway( $database );

//Call getByUsername() method to get the username
$user = $user_gateway->getByUsername($data["username"]);


//check if the username is correct
if($user === false){
    http_response_code(401);
    echo json_encode(["message" => "invalid authentication"]);
    exit;
} 

//check if the password is correct
if (! password_verify( $data["password"], $user["password_hash"])) {
    http_response_code(401);
    echo json_encode(["message" => "invalid authentication"]);
    exit;
}

//Payload : data about the user
//sub key claim instead of id key, it's required for JWT standard.


//Instead of using access token
// $access_token = base64_encode(json_encode( $payload ));
//We will use JWT instead
$codec = new JWTCodec( $_ENV["SECRET_KEY"] );

require __DIR__ . "/token.php";

// $refresh_token = $refresh_token_gateway->getByToken($data["token"]);

if ($refresh_token === false) {
    
    http_response_code(400);
    echo json_encode(["message" => "invalid token (not on whitelist)"]);
    exit;
}


$refresh_token_gateway = new RefreshTokenGateway($database, $_ENV["SECRET_KEY"]);
$refresh_token_gateway->create($refresh_token, $refresh_token_expiry);






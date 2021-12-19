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
$database = new Database($_ENV["DB_HOST"], $_ENV["DB_NAME"], $_ENV["DB_USER"],$_ENV["DB_PASS"]);

//Pass the database object to the UserGateway constructor 
$user_gateway = new UserGateway( $database );

//Call getByUsername() method to get the username
$user = $user_gateway->getByUsername($data["username"]);

echo json_encode($user);

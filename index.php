<?php 

declare(strict_types = 1);

// ini_set("display_error", "On");

require __DIR__."/vendor/autoload.php";
//Sets a user-defined error handler function
set_error_handler("ErrorHandler::handleError");
//Sets a user-defined exception handler function
set_exception_handler("ErrorHandler::handleException");


//dotenv
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

//Get the URL without the http protocol
    $path =  parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
//Split the URL from slashes '/'
    $parts = explode("/", $path);
//Get the necessary part of the endpoints
    $resource = $parts[3];
    $id = $parts[4] ?? null;

//  echo $resource, ", ", $id, "\n";

 //Get the HTTP Method
//  echo $_SERVER["REQUEST_METHOD"], "\n";

 //Set status code solution 1
//  if( $resource != "tasks" ){
//      header("{$_SERVER['SERVER_PROTOCOL']} 404 Not Found");
//      exit;
//  }

//Set status code solution 2
if( $resource != "tasks" ){
    http_response_code(404);
    exit;
}

// $api_key = $_GET["api-key"];
// echo $api_key;
// http https//localhost/api/tasks?api-key=abc
// print_r($_SERVER);
if( empty($_SERVER["HTTP_X_API_KEY"])){

    http_response_code(400);
    echo json_encode(["message"=>"missing API key"]);
    exit;

}
$api_key = $_SERVER["HTTP_X_API_KEY"];

//Instanciate the Database class
$database = new Database($_ENV["DB_HOST"],$_ENV["DB_NAME"], $_ENV["DB_USER"], $_ENV["DB_PASS"]);

$user_gateway = new UserGateway( $database );

if( $user_gateway->getByAPIKey( $api_key)===false){
    http_response_code(401);
    echo json_encode(["message"=>'Invalid API key']);
    exit;
}

// echo $api_key;

// exit;

//Set the header content type and charset
header("Content-type: application/json; charset=UTF-8");

//Load the controller file or loading using composer autoload
// $dirname = require __DIR__."/src/TaskController.php";



//call the database method
// $database->getConnection();

//instanciate the TaskController
$task_gateway = new TaskGateway( $database );

//instanciate the TaskController
$controller = new TaskController( $task_gateway );

//passing the request method and the id
$controller->processRequest( $_SERVER["REQUEST_METHOD"] , $id);
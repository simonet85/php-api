<?php 

declare(strict_types = 1);

require __DIR__ . "/bootstrap.php";

// ini_set("display_error", "On");

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

//Instanciate the Database class
$database = new Database($_ENV["DB_HOST"],$_ENV["DB_NAME"], $_ENV["DB_USER"], $_ENV["DB_PASS"]);

$user_gateway = new UserGateway( $database );

//Get the Authorization Header
// var_dump( $_SERVER["HTTP_AUTHORIZATION"]);
// $headers = apache_request_headers();
// echo $headers["Authorization"];
// exit; 

//instanciate the Auth class
$auth = new Auth( $user_gateway );

//Call the authenticateAPIKey method
//And check if it return true or false
// if( ! $auth->authenticateAPIKey()){
//     exit;
// }

//Call the authenticateAccessToken method
//And check if it return true or false
if( ! $auth->authenticateAccessToken() ){
    exit;
}

//get the user ID
$user_id = $auth->getUserID();

// var_dump( $user_id );
// exit;

//instanciate the TaskGateway class
$task_gateway = new TaskGateway( $database );

//instanciate the TaskController class
$controller = new TaskController( $task_gateway, $user_id );

//passing the request method and the id
$controller->processRequest( $_SERVER["REQUEST_METHOD"] , $id);
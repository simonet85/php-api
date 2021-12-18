<?php 

require __DIR__."/vendor/autoload.php";
//Sets a user-defined error handler function
set_error_handler("ErrorHandler::handleError");
//Sets a user-defined exception handler function
set_exception_handler("ErrorHandler::handleException");


//dotenv
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

//Set the header content type and charset
header("Content-type: application/json; charset=UTF-8");
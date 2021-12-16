<?php 

class ErrorHandler{
    //Throwable is the base class for all Exceptions and Errors in PHP
    public static function handleException(Throwable $exception) : void{

        // Get or Set the HTTP response code
        http_response_code(500);
        
        // Returns the JSON representation of a value
        echo json_encode([
            "code" => $exception->getCode(),
            "message" => $exception->getMessage(),
            "file" => $exception->getFile(),
            "line" => $exception->getLine()
        ]);
    }
}
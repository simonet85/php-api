<?php 
class TaskController{
    private $gateway;
    public function __construct( TaskGateway $gateway)
    {
        $this->gateway = $gateway;
    }
    public function processRequest( string $method, ?string $id) : void{
        if($id == null){ //When the id is null
            if($method == 'GET'){
            //   echo 'index'; 
            echo json_encode($this->gateway->getAll()); //Fetch all data
            }elseif( $method == "POST"){
                // echo "Create";
                // print_r($_POST);
                $data = (array) json_decode( file_get_contents("php://input"), true);

                // var_dump( $data );
                $id = $this->gateway->create( $data );

                $this->responseCreated(( $id ));

            }else{
               
                $this->responseMethodNotAllowed("Allow: GET, POST");
            }
        }else{ //When the id isn't null
            $task = $this->gateway->get($id);

            if( $task === false ){
                $this->responseNotFound($id);
                return;
            }

            switch( $method ){
                case "GET" :
                    echo json_encode($this->gateway->get(  $id ));
                    break;
                case "PATCH" :
                    echo "update $id";
                    break;
                case "PUT" :
                    echo "put $id";
                    break;
                case "DELETE" :
                    echo "delete $id";
                    break;
                default : 
                    $this->responseMethodNotAllowed("GET, PATCH, DELETE");
            }
        }
    }
    private function responseMethodNotAllowed(string $allowed_methods): void {
        http_response_code(405);
        header("Allow: $allowed_methods");
    }

    private function responseNotFound( string $id): void{
        http_response_code(404);
        echo json_encode(["message" => "Task with ID $id not found"]);
    }
    
    private function responseCreated( string $id): void{
        http_response_code(201);
        echo json_encode(["message" => "Task created", "id" => $id]);
    }
   
}
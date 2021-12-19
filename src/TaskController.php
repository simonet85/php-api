<?php 
class TaskController{

    private  $gateway;
    private  $user_id;

    public function __construct( TaskGateway $gateway, $user_id )
    {
        $this->gateway = $gateway;
        $this->user_id = $user_id;
    }

    public function processRequest( string $method, ?string $id) : void{
        if($id == null){ //When the id is null
            if($method == 'GET'){
            //   echo 'index'; 
            echo json_encode($this->gateway->getAllForUser( $this->user_id )); //Fetch  data for user
            }elseif( $method == "POST"){
                // echo "Create";
                // print_r($_POST);
                $data = (array) json_decode( file_get_contents("php://input"), true);
              
                $errors = $this->getValidationErrors($data);

                if( ! empty($errors) ){

                    $this->responseUnprocessableEntity( $errors );
                    return;
                }

                // var_dump( $data );
                $id = $this->gateway->create( $data );

                $this->responseCreated($id );

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
                    echo json_encode($task);
                    break;
                case "PATCH" :

                    // echo "Create";
                    // print_r($_POST);
                    $data = (array) json_decode( file_get_contents("php://input"), true);
              
                    $errors = $this->getValidationErrors($data, false );

                    if( ! empty($errors) ){

                        $this->responseUnprocessableEntity( $errors );
                        return;
                    }

                    $rows = $this->gateway->update(  $id,  $data );
                    echo json_encode(["message"=>"Task updated", "rows" =>$rows]);
                    break;

                case "DELETE" :
                    $rows = $this->gateway->delete( $id );
                    echo json_encode(["message"=>"Task delete", "rows" => $rows]);
                    break;

                default : 
                    $this->responseMethodNotAllowed("GET, PATCH, DELETE");
            }
        }
    }

    private function responseUnprocessableEntity(array $errors ): void {
        http_response_code(422);
        echo json_encode(["errors" => $errors]);
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

    private function getValidationErrors( array $data, bool $is_new = true ): array{
        $errors = [];

        if( $is_new && empty( $data["name"])){
            $errors[] = "name is required";
        }

        if( ! empty($data["priority"])){

            if( filter_var($data["priority"], FILTER_VALIDATE_INT) === false){
                $errors[] = "priority must be an integer";
            }
        }

        return $errors;
    } 
}
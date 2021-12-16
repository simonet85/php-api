<?php 
class TaskGateway{
    private PDO $conn;

    public function __construct( Database $database)
    {
        $this->conn = $database->getConnection();
    }

    public function getAll(): array{
        $sql = "SELECT * 
                FROM task 
                ORDER BY name";

        $stmt = $this->conn->query( $sql );

        //Convert bool into bool in json
        $data = [];
        while($row = $stmt->fetch( PDO::FETCH_ASSOC)){ //fetch each row and store the result
            $row['is_completed'] = (bool) $row['is_completed'];

            $data[] = $row;
        }
        return $data;
    }

    public function get( string $id ){
        $sql = "SELECT * 
                FROM task
                WHERE id = :id
         ";

        $stmt = $this->conn->prepare( $sql );
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        $data = $stmt->fetch( PDO::FETCH_ASSOC );

        if( $data !== false){
            $data['is_completed'] = (bool) $data['is_completed'];
        }
        return $data;

    }

    public function create( array $data ): string{

        $sql = "INSERT INTO task (name, priority, is_completed) VALUES (:name, :priority, :is_completed)";

        $stmt = $this->conn->prepare( $sql );
        $stmt->bindValue(":name", $data["name"], PDO::PARAM_STR);

        if( empty($data["priority"]) ){
            $stmt->bindValue(":priority", null, PDO::PARAM_NULL);
        }else{
            $stmt->bindValue(":priority", $data["priority"], PDO::PARAM_INT);
        }

        $stmt->bindValue(":is_completed", $data["is_completd"] ?? false, PDO::PARAM_BOOL);

        $stmt->execute();

        return $this->conn->lastInsertId();
    }
}
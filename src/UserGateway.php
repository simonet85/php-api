<?php 
class UserGateway{
    private PDO $conn; // declare the PDO connection

    public function __construct( Database $database)
    {
        $this->conn = $database->getConnection(); // Initialize the database connection
    }

    //Get the api key function
    public function getByAPIKey( string $key){

        //Select the user with the desired api key
        $sql = "SELECT * 
                FROM user
                WHERE api_key = :api_key";

        $stmt = $this->conn->prepare( $sql ); //prepare 

        $stmt->bindValue(":api_key", $key, PDO::PARAM_STR); // bindvalue

        $stmt->execute(); // execute the query

        return $stmt->fetch( PDO::FETCH_ASSOC ); // return the result as an associative array
    }

    public function getByUsername( string $username ){
        $sql = "SELECT *
                FROM user
                WHERE username = :username";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":username", $username, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getById( int $id ): array | false{
        $sql = "SELECT * 
        FROM user 
        WHERE id = :id";
        $stmt = $this->conn->prepare( $sql );
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch( PDO::FETCH_ASSOC );
        
    }
}
<?php 
class Database{
    private string $host;
    private string $name;
    private string $user;
    private string $password;

    public function __construct( $host, $name, $user, $password)
    {
        $this->host = $host;
        $this->name = $name;
        $this->user = $user;
        $this->password = $password;
    }

    public function getConnection(): PDO{
        $dsn = "mysql:host={$this->host};dbname={$this->name};charset=utf8";

        return new PDO( $dsn, $this->user, $this->password, [
            PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
            //Prevent numeric value to be converted
            PDO::ATTR_EMULATE_PREPARES=>false,
            PDO::ATTR_STRINGIFY_FETCHES=>false,

        ]);
    }
}
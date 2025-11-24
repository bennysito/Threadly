<?php 

class Database{
    private $host = "localhost";
    private $user = "root";
    private $pass = "";
    private $database = "threadly";

    public $threadly_connect;
    function __construct(){
    $this->threadly_connect = new mysqli($this->host, $this->user, $this->pass, $this->database);
        
    if ($this->threadly_connect->connect_error) {
    die("Connection failed: " . $this->threadly_connect->connect_error);
    }

    }

    function prepare($sql){
        return $this->threadly_connect->prepare($sql);
    }

    function close_db(){
        $this->threadly_connect->close();
    }
}
?>

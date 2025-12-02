<?php
class Database {
    private $host     = "localhost";
    private $user     = "root";
    private $pass     = "";
    private $database = "threadly";

    public $threadly_connect;

    public function __construct() {
        $this->threadly_connect = new mysqli($this->host, $this->user, $this->pass, $this->database);

        if ($this->threadly_connect->connect_error) {
            die("Connection failed: " . $this->threadly_connect->connect_error);
        }
    }

    // Optional helper
    public function get_connection() {
        return $this->threadly_connect;
    }

    public function close_db() {
        if ($this->threadly_connect) {
            $this->threadly_connect->close();
        }
    }
}
?>
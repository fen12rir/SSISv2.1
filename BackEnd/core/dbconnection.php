<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '../../Exceptions/DatabaseConnectionException.php';
class Connect {
    protected $conn = null;
    protected $servername;
    protected $username;
    protected $password;
    protected $dbname;

    //automatically run and connect database when object is created
    public function __construct() {
        $this->initialize();
        $this->connect();
    }

    //initialize variables about database
    protected function initialize() {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        $this->servername = $_ENV['SERVERNAME'];
        $this->username = $_ENV['USERNAME'];
        $this->password = $_ENV['PASSWORD'];
        $this->dbname = $_ENV['DBNAME'];
    }
    //connect to database
    protected function connect() {
        try {
            $this->conn = new PDO("mysql:host={$this->servername};dbname={$this->dbname};charset=utf8mb4", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } 
        catch (PDOException $e) {
            throw new DatabaseConnectionException('Failed to establish connection',$e->getCode(),$e);
        }
    }
    //function to call connection if needed
    public function getConnection() {
        if ($this->conn === null) {
            $this->connect();
        }
        return $this->conn;
    }
}
?>

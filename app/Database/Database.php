<?php
declare(strict_types=1);
namespace app\Database;
require_once __DIR__ . '/../../vendor/autoload.php';
use SSIS\Exceptions\DatabaseConnectionException;
use Dotenv\Dotenv;

class Database implements DatabaseInterface {
    public static ?self $instance = null;
    protected ?PDO $conn = null;
    protected string $servername;
    protected string $username;
    protected string $password;
    protected string $dbname;
    
    protected function __construct() {
        $this->initialize();
        $this->connect();
    }
    //initialize variables about database
    protected function initialize() : void {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        $this->servername = $_ENV['SERVERNAME'];
        $this->username = $_ENV['USERNAME'];
        $this->password = $_ENV['PASSWORD'];
        $this->dbname = $_ENV['DBNAME'];
    }
    //create singleton connection
    public static function getInstance() : self {
        if(self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    //connect to database
    protected function connect() {
        try {
            $this->conn = new PDO("mysql:host={$this->servername};dbname={$this->dbname};charset=utf8mb4", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } 
        catch (PDOException $e) {
            throw new DatabaseConnectionException('Failed to establish a connection',$e->getCode(),$e);
        }
    }
    //function to call connection if needed
    public function getConnection() {
        if ($this->conn === null) {
            $this->connect();
        }
        return $this->conn;
    }
    public function disconnect() {
        $this->conn = null;
    }
    public function isConnected() : bool {
        return $this->conn !== null;
    }
    public function beginTransaction() : bool {
        return $this->getConnection()->beginTransaction();
    }
    public function rollBack() : bool {
        return $this->getConnection()->rollBack();
    }
    public function commit() : bool {
        return $this->getConnection()->commit();
    }
}
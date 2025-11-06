<?php
declare(strict_types=1);
namespace SSIS\Database;
use PDO;
interface DatabaseInterface {
    public function getConnection() : PDO;
    public function disconnnect() : bool;
    public function isConnected() : bool;
    public function beginTransaction() : bool;
    public function rollBack() : bool;
    public function commit() : bool;
}
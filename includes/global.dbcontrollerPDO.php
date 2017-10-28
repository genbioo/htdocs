<?php
class DBController {
    private $host = "localhost";
    private $user = "root";
    private $password = "password";
    private $database = "psrms";
    private $conn;
    private $stmt;
    public $update_status;
    public $lastID;
    public $entryCount;

    public function __construct() {
        try {
            $this->conn = new PDO("mysql:host=$this->host;dbname=$this->database", $this->user, $this->password);
            // set the PDO error mode to exception
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $e)
        {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    function runFetch() {
        $this->stmt->execute();
        while($row = $this->stmt->fetch(PDO::FETCH_ASSOC)) {
            $resultset[] = $row;
        }
        $this->entryCount = $this->stmt->rowCount();
        if(!empty($resultset))
            return $resultset;
    }
    
    function fetchWithIn($array) {
        $this->stmt->execute($array);
        while($row = $this->stmt->fetch(PDO::FETCH_ASSOC)) {
            $resultset[] = $row;
        }
        $this->entryCount = $this->stmt->rowCount();
        if(!empty($resultset))
            return $resultset;
    }

    function prepareStatement($query) {
        $this->stmt = $this->conn->prepare($query);
    }

    function bindVar($param, $value, $type, $length) {
        if($length == 0) {
            $this->stmt->bindParam($param, $value, $type);
        } else {
            $this->stmt->bindParam($param, $value, $type, $length);
        }
    }

    function bindNull($param) {
        $this->stmt->bindValue($param, null, PDO::PARAM_INT);
    }

    function runUpdate() {
        $status = $this->stmt->execute();
        if($status) {
            $this->update_status = true;
            $this->lastID = $this->conn->lastInsertId(); //ID of recently inserted value
        }
    }

    function getUpdateStatus() {
        return $this->update_status;
    }

    function getLastInsertID() {
        return $this->lastID;
    }

    function getFetchCount() {
        return $this->entryCount;
    }
}
?>
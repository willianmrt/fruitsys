<?php

function desconecta_PDO($dbconn) {
    $dbconn = null;
    return $dbconn;
}

class PDOTester extends PDO {

    public $drive;
    public $ip;
    public $user;
    public $database;
    public $password;
    public $port;

    public function __construct($driver_options = array()) {
        $fileIni = "assets/config/db.ini";

        $db = parse_ini_file($fileIni, true);
        extract($db, EXTR_PREFIX_SAME, "wddx"); //cria variavel pela posicao do array 

        $this->drive = $drive;
        $this->ip = $ip;
        $this->user = $user;
        $this->database = $database;
        $this->password = $password;
        $this->port = $port;

        switch (trim($this->drive)) {
            case 'POSTGRESQL':
                $dsn = "pgsql:host=$this->ip;dbname=$this->database;port=$this->port";
                break;
            case 'MYSQL':
                $dsn = "mysql:host=$this->ip;port=$this->port;dbname=$this->database";
                break;
        }

        try {
            //create a database connection            
            $pdo = new PDO($dsn, $this->user, $this->password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo $e->getLine; 
        }

        parent::__construct($dsn, $this->user, $this->password, $driver_options);
        $this->setAttribute(PDO::ATTR_STATEMENT_CLASS, array('PDOStatementTester', array($this)));
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
}

class PDOStatementTester extends PDOStatement {

    const NO_MAX_LENGTH = -1;

    protected $connection;
    protected $bound_params = [];

    protected function __construct(PDO $connection){
        $this->connection = $connection;
    }

    public function bindParam($paramno, &$param, $type = PDO::PARAM_STR, $maxlen = null, $driverdata = null) {
        $this->bound_params[$paramno] = [
            'value' => &$param,
            'type' => $type,
            'maxlen' => (is_null($maxlen)) ? self::NO_MAX_LENGTH : $maxlen,               
        ];
        $result = parent::bindParam($paramno, $param, $type, $maxlen, $driverdata);
    }

    public function bindValue($parameter, $value, $data_type = PDO::PARAM_STR) {
        $this->bound_params[$parameter] = [
            'value' => $value,
            'type' => $data_type,
            'maxlen' => self::NO_MAX_LENGTH
        ];
        parent::bindValue($parameter, $value, $data_type);
    }

    public function getSQL($values = []) {
        $sql = $this->queryString;

        if (sizeof($values) > 0) {
            foreach ($values as $key => $value) {
                $sql = str_replace($key, $this->connection->quote($value), $sql);
            }
        }

        if (sizeof($this->bound_params)) {
            foreach ($this->bound_params as $key => $param) {
                $value = $param['value'];
                if (!is_null($param['type'])) {
                    $value = self::cast($value, $param['type']);
                }
                if ($param['maxlen'] && $param['maxlen'] != self::NO_MAX_LENGTH) {
                    $value = self::truncate($value, $param['maxlen']);
                }
                if (!is_null($value)) {
                    $sql = str_replace($key, $this->connection->quote($value), $sql);
                } else {
                    $sql = str_replace($key, 'NULL', $sql);
                }
            }
        }
        return $sql;
    }

    static protected function cast($value, $type) {
        switch ($type) {
            case PDO::PARAM_BOOL:
                return (bool) $value;
                break;
            case PDO::PARAM_NULL:
                return null;
                break;
            case PDO::PARAM_INT:
                return (int) $value;
            case PDO::PARAM_STR:
            default:
                return $value;
        }
    }

    static protected function truncate($value, $length) {
        return substr($value, 0, $length);
    }

}

?>

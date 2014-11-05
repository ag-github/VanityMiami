<?php

class Lib_Db {
    private $pdo;
    /**
     *
     * @var Lib_SettingFile
     */
    private $settings;

    public function __construct(Lib_SettingFile $settings) {
        $this->settings = $settings;
        $this->initDatabase();
        $this->query("SET NAMES utf8");
        $this->query("SET CHARACTER SET utf8");
    }

    public function query($sql) {
        $statement = $this->pdo->prepare($sql, array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true));
        $stm = new Lib_Db_Statement($statement);
        if(false === $stm->execute()) {
            return false;
        }
        return $stm;
    }

    public function getRows($sql) {
        $result = $this->query($sql);
        if (false === $result) {
            throw new Exception('Error getting rows. Error in query.');
        }
        return $result;
    }

    public function getOneRow($sql) {
        $result = $this->query($sql);
        if (false === $result) {
            throw new Exception('Error getting one row. Error in query.');
        }
        if ($result->rowCount() != 1) {
            throw new Exception('Error getting one row. Got ' . $result->rowCount() . ' rows.');
        }
        return $result->fetchArray();
    }

    public function escape($value) {
        return substr($this->pdo->quote($value), 1, -1);
    }

    /**
     * @param String $tableName
     * @param array $columnValues
     * @return number
     */
    public function insertToTableAutoincrement($tableName, array $columnValues) {
        $this->insertToTable($tableName, $columnValues);
        return $this->getLastInsertId();
    }

    private function getLastInsertId() {
        return $this->pdo->lastInsertId();
    }

    public function insertToTable($tableName, array $columnValues) {
        $columns = '';
        $values = '';
        foreach ($columnValues as $key => $value) {
            $columns .= $key.', ';
            $values .= '\''. $this->escape($value).'\', ';
        }
        $insert = 'INSERT INTO '.$tableName.' ('.rtrim($columns, ' ,').')'.
            ' VALUES ('.rtrim($values, ' ,').')';
        try {
            $result = $this->query($insert);
        } catch (PDOException $e) {
            throw new Exception('Insert into table '. $tableName .' failed. Row with values('. $values .') was not inserted. Error: ' . $e->getMessage(), $e->getCode());
        }
    }

    public function saveToDb(Lib_ConvertableToArray $params, $tableName) {
        $this->insertToTable($tableName, $params->toArray());
    }

    private function initDatabase() {
        $dsn = sprintf('mysql:%sdbname=%s;charset=utf8', $this->getHostnameDsn(), $this->settings->get('DB_DATABASE'));
        $this->pdo = new PDO($dsn, $this->settings->get('DB_USERNAME'), $this->settings->get('DB_PASSWORD'),
            $this->getPDOConnectionOptions());
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
    }

    private function getHostnameDsn() {
        $port = '';
        $host = $this->settings->get('DB_HOSTNAME');
        $pos = strpos($host, ':');
        if($pos !== false) {
            $host = substr($host, 0, $pos);
            $port = substr($host, $pos+1);
            return 'host=' . $host . ';port=' . $port . ';';
        }
        return 'host=' . $host . ';';
    }

    private function getPDOConnectionOptions() {
        $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');
    
        if ($this->settings->getFileSettingValue('DB_PERSISTENT') == 'Y') {
            $options[PDO::ATTR_PERSISTENT] = true;
        }
        return $options;
    }
}

class Lib_Db_Statement {
    private $statement;

    public function __construct($statement) {
        $this->statement = $statement;
    }

    public function execute() {
        try {
            $this->statement->execute();
        } catch(Exception $e) {
            return false;
        }
        return true;
    }

    public function rowCount() {
        return $this->statement->rowCount();
    }

    public function fetchArray() {
        return $this->statement->fetch();
    }

    public function fetchAssoc() {
        return $this->statement->fetch(PDO::FETCH_ASSOC);
    }

    public function free() {
        $this->statement->closeCursor();
    }
}
?>

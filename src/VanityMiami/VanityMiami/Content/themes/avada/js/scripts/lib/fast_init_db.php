<?php

class Lib_Db {
    private $db;
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
        $stm = new Lib_Db_Statement($sql, $this->db);
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
        return @mysql_real_escape_string($value, $this->db);
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
        return mysql_insert_id($this->db);
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
        $result = $this->query($insert);
        if (false === $result) {
            throw new Exception('Insert into table '. $tableName .' failed. Row with values('. $values .') was not inserted. Error: ' . mysql_error(), mysql_errno());
        }
    }

    public function saveToDb(Lib_ConvertableToArray $params, $tableName) {
        $this->insertToTable($tableName, $params->toArray());
    }

    private function initDatabase() {
        if ($this->settings->getFileSettingValue('DB_PERSISTENT') == 'Y' && function_exists('mysql_pconnect')) {
            $this->db = mysql_pconnect($this->settings->getFileSettingValue('DB_HOSTNAME'), $this->settings->getFileSettingValue('DB_USERNAME'), $this->settings->getFileSettingValue('DB_PASSWORD'));
        } else {
            $this->db = mysql_connect($this->settings->getFileSettingValue('DB_HOSTNAME'), $this->settings->getFileSettingValue('DB_USERNAME'), $this->settings->getFileSettingValue('DB_PASSWORD'));
        }
        @mysql_select_db($this->settings->getFileSettingValue('DB_DATABASE'), $this->db);
    }
}

class Lib_Db_Statement {
    private $db;
    private $sql;
    private $result = null;
        
    public function __construct($sql, $db) {
        $this->sql = $sql;
        $this->db = $db;
    }
    
    public function execute() {
        $this->result = mysql_query($this->sql, $this->db);
        return $this->result !== false;
    }
    
    public function rowCount() {
        return @mysql_num_rows($this->result);
    }
    
    public function fetchArray() {
        return @mysql_fetch_array($this->result);
    }
    
    public function fetchAssoc() {
        return @mysql_fetch_assoc($this->result);
    }

    public function free() {
        mysql_free_result($this->result);
    }
}
?>

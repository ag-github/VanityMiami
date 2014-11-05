<?php

interface Lib_ConvertableToArray {
    public function toArray();
}

class Lib_Server {
    const SESSION_COOKIE = 'LaSID';
    const VISITOR_COOKIE = 'LaVisitorId';
    const VISITOR_NEW_COOKIE = 'LaVisitorNew';

    public static function getRemoteIp() {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = @$_SERVER['HTTP_X_FORWARDED_FOR'];
            $ipAddresses = explode(',', $ip);
            foreach ($ipAddresses as $ipAddress) {
                $ipAddress = trim($ipAddress);
                if (self::isValidIp($ipAddress)) {
                    return $ipAddress;
                }
            }
        }
        if (isset($_SERVER['REMOTE_ADDR'])) {
            return @$_SERVER['REMOTE_ADDR'];
        }
        return '';
    }

    public static function getUserAgent() {
        return @$_SERVER['HTTP_USER_AGENT'];
    }

    public static function getReferer() {
        return @$_SERVER['HTTP_REFERER'];
    }

    public static function isCrawler() {
        $crawlerList = array('googlebot', 'adsbot-google', 'slurp', 'yahooseeker', 'msnbot', 'teoma');
        $userAgent = strtolower(self::getUserAgent());
        foreach ($crawlerList as $crawler) {
            if (strpos($userAgent, $crawler) !== false) {
                return true;
            }
        }
        return false;
    }

    public static function decodeUrl($url) {
        if (substr($url, 0, 2) == 'H_') {
            return 'http://'.substr($url, 2);
        }
        if (substr($url, 0, 2) == 'S_') {
            return 'https://'.substr($url, 2);
        }
        if (substr($url, 0, 2) == 'A_') {
            return '//'.ltrim(substr($url, 2), '/');
        }
        return $url;
    }

    private static function isValidIp($ip) {
        if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return true;
        }
        return false;
    }
}

class Lib_SettingFile {
    /**
     * @var Lib_Db
     */
    private $db;
    protected $fileName;
    static protected $values = null;

    public function __construct($fileName = null) {
    	if ($fileName != null) {
    		$this->fileName = $fileName;
    		return;
    	}
    	if (@$_SERVER['PROJECT_ACCOUNTS_PATH'] != '') {
    		$this->fileName = $_SERVER['LOCAL_ACCOUNTS_PATH'] . '/accounts/settings.php';
    		if (!@file_exists($this->fileName)) {
    			$this->fileName = $_SERVER['PROJECT_ACCOUNTS_PATH'] . '/accounts/settings.php';
    		}
    	} else {
    		$this->fileName = '../accounts/settings.php';
    	}
    }

    public function setValues($values) {
        self::$values = $values;
    }

    public function load() {
        if(self::$values !== null) {
            return;
        }

        $this->loadValues();

        $this->loadDbSettings();

        $this->setTimezone();
    }

    public function loadValues() {
        self::$values = array();
        $lines = $this->getFileContent();

        foreach($lines as $line) {
            if(false !== strpos($line, '<?') || false !== strpos($line, '?>')) {
                continue;
            }
            $pos = strpos($line, '=');
            if($pos === false) {
                continue;
            }
            $name = substr($line, 0, $pos);
            $value = substr($line, $pos + 1);
            self::$values[$name] = rtrim($value);
        }
    }

    public function loadDbSettings() {
    	$sql = "SELECT name, value from qu_g_settings where name IN
    			('cronLastRun', 'cronSleepUntil', 'TIMEZONE',	'serverName', 'serverNameResolveFrom', 'baseServerUrl', 'serverPort')";

    	$result = $this->getDb()->query($sql);
    	if (false === $result) {
    		throw new Exception('Error in query.');
    	}

		while ($row = $result->fetchAssoc()) {
		    self::$values[$row['name']] = $row['value'];
		}
		$result->free();
    }

    private function loadDbSetting($name) {
    	$sql = "SELECT name, value from qu_g_settings where name='$name'";

    	$result = $this->getDb()->query($sql);
    	if (false === $result) {
    		throw new Exception('Error getting one row. Error in query.');
    	}

    	while ($row = $result->fetchAssoc()) {
    		self::$values[$row['name']] = $row['value'];
    	}
    	$result->free();
    }

    public function get($name) {
    	if (!isset(self::$values[$name]))  {
    		$this->loadDbSetting($name);
    	}
        return @self::$values[$name];
    }

    public function getFileSettingValue($name) {
        if (!isset(self::$values[$name]))  {
            return '';
        }
        return @self::$values[$name];
    }

    public function set($name, $value) {
        self::$values[$name] = $value;
    }

    /**
     *
     * @return Lib_Db
     */
    public function getDb() {
        if ($this->db == null) {
            if (@self::$values['DB_DRIVER'] == 'Pdo') {
                require_once 'lib/fast_init_db_pdo.php';
            } else {
                require_once 'lib/fast_init_db.php';
            }
            $this->db = new Lib_Db($this);
        }
        return $this->db;
    }

    protected function getFileContent() {
        $lines = file($this->fileName);
        if (!empty($lines)) {
            return $lines;
        }
        throw new Exception('Could not read settings file: ' . ' ' . $this->fileName);
    }

    private function setTimezone() {
        if (@self::$values['TIMEZONE'] != '') {
            @date_default_timezone_set(self::$values['TIMEZONE']);
        } else {
            @date_default_timezone_set('America/Los_Angeles');
        }
    }
}

?>

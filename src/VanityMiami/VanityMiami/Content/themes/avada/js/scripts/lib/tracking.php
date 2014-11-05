<?php
require_once 'lib/fast_init.php';
require_once 'lib/contactwidget.php';

if (file_exists('../include/Gpf')) {
    define('TRACKING_GPF_PATH', '../');
} else {
    define('TRACKING_GPF_PATH', '../../../GwtPhp/server/');
}

class Lib_Browser implements Lib_ConvertableToArray {
    /**
     * @var Lib_Db
     */
    private $db;

    /**
     * @var array
     */
    private $values = array();

    public function __construct(Lib_Db $db) {
        $this->db = $db;
    }

    /**
     * @throws Exception
     */
    public function load($visitorId) {
        $this->values = $this->db->getOneRow("SELECT * FROM qu_la_browsers WHERE cookie='".$this->db->escape($visitorId)."'");
        if ($this->values['ip'] != Lib_Server::getRemoteIp()) {
            $oldCountry = $this->values['countrycode'];
            $oldCity = $this->values['city'];
            $oldLatitude = $this->values['latitude'];
            $oldLongitude = $this->values['longitude'];
            $this->resolveLocation();
            $query = "UPDATE qu_la_browsers SET ".
                "ip='".$this->db->escape(Lib_Server::getRemoteIp());
            if ($this->values['countrycode'] != '') {
                if ($oldCountry != $this->values['countrycode']) {
                    $query .= "', countrycode='".$this->db->escape($this->values['countrycode']);
                }
                if ($oldCity != $this->values['city']) {
                    $query .= "', city='".$this->db->escape($this->values['city']);
                }
                if ($oldLatitude != $this->values['latitude']) {
                    $query .= "', latitude='".$this->db->escape($this->values['latitude']);
                }
                if ($oldLongitude != $this->values['longitude']) {
                    $query .= "', longitude='".$this->db->escape($this->values['longitude']);
                }
            }
            $query .= "' WHERE cookie='".$this->db->escape($visitorId)."'";
            $this->db->query($query);
        }
        if ($this->values['useragent'] != Lib_Server::getUserAgent()) {
            $this->values['useragent'] = Lib_Server::getUserAgent();
            $query = "UPDATE qu_la_browsers SET ".
                "useragent='".$this->db->escape($this->values['useragent']) .
                "' WHERE cookie='".$this->db->escape($visitorId)."'";
            $this->db->query($query);
        }
    }

    public function getId() {
        return $this->values['browserid'];
    }

    public function getVisitorId() {
        return $this->values['cookie'];
    }

    public function createNew() {
        $this->values = array(
            'last_contactid' => null,
            'cookie' => Lib_Tracker::generateRandomId(),
            'ip' => Lib_Server::getRemoteIp(),
            'useragent' => Lib_Server::getUserAgent(),
            'city' => null,
            'countrycode' => null,
        	'last_action_date'=> date("Y-m-d H:i:s"),
        	'screen' => @$_REQUEST['sr'],
            'referrerurl' => Lib_Tracker::fullDecodeUrl(@$_GET['ref']),
            'language' => $this->getUserAgentLanguage());
        $this->resolveLocation();
        $this->db->saveToDb($this, 'qu_la_browsers');
        $this->load($this->getVisitorId());
    }

    private function resolveLocation() {
        if (!file_exists(TRACKING_GPF_PATH.'plugins/GeoIp/GeoLiteCity.dat')) {
            return;
        }
        set_include_path(TRACKING_GPF_PATH.'include/Pear/');
        require_once('Net/GeoIP.php');
        $geoIP = Net_GeoIP::getInstance(TRACKING_GPF_PATH.'plugins/GeoIp/GeoLiteCity.dat', Net_GeoIP::STANDARD);
        try {
            $location = $geoIP->lookupLocation(Lib_Server::getRemoteIp());
        } catch (Exception $e) {
            return;
        }
        if ($location == null) {
            return;
        }
        $this->values['city'] = utf8_encode($location->city);
        $this->values['countrycode'] = utf8_encode($location->countryCode);
        $this->values['latitude'] = $location->latitude;
        $this->values['longitude'] = $location->longitude;
    }

    public function toArray() {
        return $this->values;
    }

    public function updateUserDetails($email, $firstName = '', $lastName = '', $phone = '') {
        $contact = new Lib_Contact($this->db);
        $contact->loadAndUpdate($this->values['last_contactid'], $email, $firstName, $lastName, $this->values['city'], $this->values['countrycode'], $phone);
        if ($contact->getId() != $this->values['last_contactid']) {
            $sql = "UPDATE qu_la_browsers SET last_contactid='".$contact->getId()."' WHERE browserid='".$this->getId()."'";
            $this->db->query($sql);
        }
    }
    
    private function getUserAgentLanguage() {
        if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) || $_SERVER['HTTP_ACCEPT_LANGUAGE'] == '') {
            return '';
        }
        $parts = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        $languageParts = explode(';', $parts[0]);
        return $languageParts[0];
    }
}

class Lib_Contact implements Lib_ConvertableToArray {
    /**
     * @var Lib_Db
     */
    private $db;

    /**
     * @var array
     */
    private $values = array();

    public function __construct(Lib_Db $db) {
        $this->db = $db;
    }

    public function toArray() {
        $this->values;
    }

    public function getId() {
        return $this->values['contactid'];
    }

    public function loadAndUpdate($contactId, $email, $firstName, $lastName, $city, $countryCode, $phone) {
        try {
            $uField = $this->db->getOneRow('SELECT contactid FROM qu_la_contact_unique_fields WHERE code = \'email\' AND value=\''.$this->db->escape($email).'\'');
            $this->values = $this->db->getOneRow('SELECT * FROM qu_la_contacts WHERE contactid=\''.$uField['contactid'].'\'');
            $this->update($email, $firstName, $lastName, $phone);
        } catch (Exception $e) {
            $this->insertNewContact($email, $firstName, $lastName, $city, $countryCode, $phone);
        }
    }

    private function insertNewContact($email, $firstName, $lastName, $city, $countryCode, $phone) {
        $this->values['contactid'] = substr(md5(uniqid(rand(), true)), 0, 8);
        $this->values['firstname'] = $firstName;
        $this->values['lastname'] =  $lastName;
        $this->values['rtype'] = 'V';
        $this->values['datecreated'] = date('Y-m-d H:i:s');
        $this->values['city'] = $city;
        $this->values['countrycode'] = $countryCode;
        $this->db->insertToTable('qu_la_contacts', $this->values);

        $this->setUniqueField('email', $email);
        $this->setUniqueField('phone', $phone);
    }

    private function update($email, $firstName, $lastName, $phone) {
        if ($firstName != '' && $firstName != $this->values['firstname'] || $lastName != '' && $lastName != $this->values['lastname']) {
            $this->db->query('UPDATE qu_la_contacts SET firstname = \''.$this->db->escape($firstName).'\', lastname = \''.$this->db->escape($lastName).'\' WHERE contactid=\''.$this->values['contactid'].'\'');
        }
        $this->setUniqueField('email', $email);
        $this->setUniqueField('phone', $phone);
    }


    private function setUniqueField($code, $value) {
        if ($code == '' || $value == '') {
            return;
        }
        try {
            $this->db->getOneRow('SELECT value FROM qu_la_contact_unique_fields WHERE code = \''.$this->db->escape($code).'\' AND value = \''.$this->db->escape($value).'\'');
        } catch (Exception $e) {
            $this->db->insertToTable('qu_la_contact_unique_fields', array('contactid' => $this->values['contactid'], 'rtype' => 'P', 'code' => $code, 'value' => $value));
            $this->updateFields($code);
        }
    }

    private function updateFields($fieldCode) {
        if ($fieldCode == 'email') {
            $this->updateEmailsAndDescription();
        }
    }

    private function updateEmailsAndDescription() {
        $emailsRow = $this->db->getOneRow('SELECT GROUP_CONCAT(value) AS emails FROM qu_la_contact_unique_fields WHERE contactid = \''.$this->values['contactid'].'\' AND code = \'email\' GROUP BY contactid');
        $this->db->query('UPDATE qu_la_contacts SET emails = \''.$this->db->escape($emailsRow['emails']).'\', description = \''.$this->db->escape($emailsRow['emails']).'\' WHERE contactid=\''.$this->values['contactid'].'\'');
    }
}

class Lib_Button extends Lib_ContactWidget {

    public function __construct(Lib_Db $db) {
        parent::__construct($db);
    }

    public function getCode($isIE9orLower) {
        if ($this->getType() == 'S' || $this->getType() == 'P') {
            return $this->getCode_Internal(true, $isIE9orLower);
        }
        if ($this->getType() == 'O') {
            return $this->getCode_Internal(false, $isIE9orLower);
        }
        return $this->getCode_Internal($this->isOnline(), $isIE9orLower);
    }

    private function getCode_Internal($isOnline, $isIE9orLower) {
        if($isOnline) {
            return ($isIE9orLower) ? $this->get('onlinecode_ieold') : $this->get('onlinecode');
        }
        return ($isIE9orLower) ? $this->get('offlinecode_ieold') : $this->get('offlinecode');
    }
}

class Lib_Invitations {

    /**
     * @var Lib_Db
     */
    private $db;
    private $url;
    private $referrerUrl;
    private $newVisitor;
    /**
     * @var array
     */
    private $closedInvitations;
    private $timeOnPage;

    public function __construct(Lib_Db $db, $url, $referrerUrl, $newVisitor, $closedInvitations) {
        $this->db = $db;
        $this->url = Lib_Server::decodeUrl($url);
        $this->referrerUrl = Lib_Tracker::fullDecodeUrl($referrerUrl);
        $this->newVisitor = $newVisitor;
        $this->closedInvitations = explode(',', $closedInvitations);
        $this->timeOnPage = 0;
    }

    /**
     * @throws Exception
     * @return array(contactWidgetId, timeOnPage)
     */
    public function getInvitation() {
        $sql = 'SELECT cw.contactwidgetid AS contactwidgetid, s.rkey AS rkey, s.value AS value,  IF(s.groupId IS NULL,-1,s.groupId) AS groupId '
        .'FROM qu_g_rulesettings s '
        .'INNER JOIN qu_g_rules r ON r.ruleid = s.ruleid AND r.rtype = \'I\' AND r.rstatus = \'E\' AND s.rtype = \'C\' '
        .'INNER JOIN qu_la_contactwidget_attributes cwa ON cwa.name = \'invitation_ruleid\' AND cwa.value = r.ruleid '
        .'INNER JOIN qu_la_contactwidgets cw ON cwa.contactwidgetid = cw.contactwidgetid AND cw.contactwidgetid NOT IN(\''.implode('\',\'', $this->closedInvitations).'\') '
        .'INNER JOIN qu_la_departments d ON d.departmentid = cw.departmentid AND d.onlinestatus LIKE \'%T%\' '
        .'ORDER BY contactwidgetid DESC';
        $ruleSettings = $this->db->getRows($sql);

        if ($ruleSettings->rowCount() > 0) {
            $rules = array();
            while ($setting = $ruleSettings->fetchArray()) {
                if (!array_key_exists($setting['contactwidgetid'], $rules)) {
                    $rules[$setting['contactwidgetid']] = array();
                }
                $rules[$setting['contactwidgetid']][] =
                array('type' => $setting['rkey'], 'condition' => json_decode($setting['value']), 'groupid' => $setting['groupId']);
            }
            $sortByTime = array();
            foreach ($rules as $contactWidgetId => $rule) {
                $this->timeOnPage = 0;
                if ($this->executeRule($rule)) {
                    try {
                        $sortByTime[$this->timeOnPage] = $contactWidgetId;
                        //return array ($contactWidgetId, $this->timeOnPage);
                    } catch (Exception $e) {
                    }
                }
            }
            if (count($sortByTime) > 0) {
                ksort($sortByTime, SORT_NUMERIC);
                $key = key($sortByTime);
                return array($sortByTime[$key], $key);
            }
        }
        throw new Exception('Invitation not found');
    }

    private function groupifyConditions($rule) {
        $groups = array();
        foreach ($rule as $ruleData){
            $groups[$ruleData['groupid']][] = $ruleData;
        }
        return $groups;
    }

    private function hasAdditionalConditions($groups) {
        if (count($groups) == 0) {
            return false;
        }

        if (count($groups) == 1 && array_key_exists("-1", $groups)) {
            return false;
        }

        return true;
    }

    private function executeRule(array $rule) {
        $conditionGroups = $this->groupifyConditions($rule);
        // Are there additional conditions to safisfy?
        if (!$this->hasAdditionalConditions($conditionGroups)) {
            if (count($conditionGroups) == 1) {
                //time rule must be execute
                $this->executeDefaultCondition(($conditionGroups[-1]));
            }
            return true;
        }

        foreach ($conditionGroups as $gid => $conditionGroup) {

            //time rule must be execute
            if ($gid == "-1") {
                $this->executeDefaultCondition($conditionGroup);
                continue;
            }

            $allOK = true;
            foreach ($conditionGroup as $ruleData) {
                if (!$this->executeCondition($ruleData['type'], $ruleData['condition'])) {
                    $allOK = false;
                    break;
                }
            }

            // If a group of conditions is all true, rule is satisfied.
            if($allOK) {
                return true;
            }
        }
        return false;
    }

    private function executeDefaultCondition(array $conditionGroup) {
        foreach ($conditionGroup as $ruleData) {
           $this->executeCondition($ruleData['type'], $ruleData['condition']);
        }
    }

    private function executeCondition($type, array $value) {
        if ($type === 'VTOP') {
            //timeOnPage
            $this->timeOnPage = $this->getValue('timeOnPage', $value);
            return true;
        }

        if ($type === 'VTYP') {
            //newVisitor
            $visitorType = $this->getValue('visitorType', $value);
            if ($visitorType === 'N') {
                return $this->newVisitor;
            }
            if ($visitorType === 'R') {
                return !$this->newVisitor;
            }
        }

        if ($type === 'VURL') {
            //url
            return $this->executeUrlCondition($this->url ,$this->getValue('operator', $value), explode("\n", $this->getValue('value', $value)));
        }

        if ($type === 'RURL') {
            //reffurl
            return $this->executeUrlCondition($this->referrerUrl, $this->getValue('operator', $value), explode("\n", $this->getValue('value', $value)));
        }


        return false;
    }

    private function executeUrlCondition($visitorUrl,$operator, array $urls) {
        $and = false;
        if (in_array($operator, array('NL', 'NE'))) {
            $and = true;
        }
        foreach ($urls as $url) {
            if (trim($url) == '') {
                continue;
            }
            if ($this->matches($visitorUrl, $operator, $url) == !$and) {
                return !$and;
            }
        }
        return $and;
    }

    private function matches($subject, $operator, $value) {
        switch ($operator) {
            case 'L' : return strstr($subject, $value) !== false;
            case 'NL' : return strstr($subject, $value) === false;
            case 'E' : return $subject === $value;
            case 'NE' : return $subject !== $value;
            case 'PM' : return @preg_match($value, $subject) === 1;
        }
        return false;
    }

    private function getValue($name, array $value) {
        // remove first array('name', 'value')
        unset($value[0]);
        foreach ($value as $valueArray) {
            if (in_array($name, $valueArray)) {
                return $valueArray[1];
            }
        }
        return null;
    }
}

class Lib_Tracker {
    /**
     * @var Lib_Db
     */
    private $db;

    /**
     * @var Lib_SettingFile
     */
    private $settings;

    /**
     * @var Lib_Browser
     */
    private $browser;

    /**
     * @var Gpf_Net_MobileDetect
     */
    private $mobileDetect;

    public static function generateRandomId() {
        $stamp = microtime();
        $ip = Lib_Server::getRemoteIp();
        $id = md5($stamp*$ip + rand()) . crypt($ip + $stamp * rand(), CRYPT_BLOWFISH);

        $id = str_replace("$", "0", $id);
        $id = str_replace("/", "0", $id);
        $id = str_replace(".", "0", $id);
        $uniqueid = substr($id, rand(0, 13), 32);
        return $uniqueid;
    }

    public function __construct() {
        $this->settings = new Lib_SettingFile();
        $this->settings->load();
        $this->db = $this->settings->getDb();
    }

    public function track() {
        if ($this->isFirstRequest() && !Lib_Server::isCrawler()) {
            $this->trackVisit();
            $this->initTimeOffset();
            $this->initInvitations();
            echo("LiveAgentTracker.setFastBusEnabled('".$this->settings->get("mod_rewrite_enabled")."');\n");
            echo("LiveAgentTracker.setVisitorId('".$this->getBrowser()->getVisitorId()."');\n");
            echo("LiveAgentTracker.setSid('".$this->getSessionId()."');\n");
        }
        $runingChatCookie = stripslashes(@$_GET['lrc']);
        if ($runingChatCookie != '' && count($runningChatParams = explode('||', $runingChatCookie)) === 3) {
            $this->initRunningChat($runningChatParams[0], $runningChatParams[1], $runningChatParams[2]);
        }
        $this->parseWidgets();
        if (@$_GET['ud'] != '') {
            $this->saveUserDetails(json_decode(stripslashes(@$_GET['ud'])));
        }
    }

    private function initInvitations() {
        try {
            $invitationFeatureActive = $this->db->getOneRow("SELECT value FROM qu_g_settings WHERE name = 'invitations_feature_active' AND accountid IS NULL");
            if ($invitationFeatureActive['value'] == 'Y' && !$this->isVisitorOnline()) {
                $invitations = new Lib_Invitations($this->db, @$_GET['pu'], @$_GET['ref'], @$_GET['vn'] == 'Y', @$_GET['ci']);
                $invitation = $invitations->getInvitation();
                echo("LiveAgentTracker.initInvitation('".$invitation[0]."', '".$invitation[1]."');\n");
            }
        } catch (Exception $e) {
        }
    }

    private function saveUserDetails($userDetails) {
        $this->getBrowser()->updateUserDetails($userDetails->e, $userDetails->f, $userDetails->l, $userDetails->p);
    }

    private function initRunningChat($cid, $buttonId, $chatStyle) {
        $button = new Lib_Button($this->db);
        try {
            $button->load($buttonId);
        } catch (Exception $e) {
            self::logException("Button $buttonId does not exist: ".$e->getMessage());
            return;
        }
        echo("LiveAgentTracker.initRunningChat('".$cid."', '".$this->getContactWidgetsUrl().$button->getChatRunningTemplateName()."', '".$chatStyle."', ".$button->getDateChanged().");\n");
    }

    private function initTimeOffset() {
        echo("LiveAgentTracker.initTimeOffset(".date('Y,m,d,H,i,s').");\n");
    }

    private function initInvitationTimes() {
        try {
            $invitationFeatureActive = $this->db->getOneRow("SELECT value FROM qu_g_settings WHERE name = 'invitations_feature_active' AND accountid IS NULL");
            if ($invitationFeatureActive['value'] == 'Y' && !$this->isVisitorOnline()) {
                echo("LiveAgentTracker.initInvitationTimes(new Array(15,30,45,60,120,300));\n");
            }
        } catch (Exception $e) {
        }
    }

    private function isVisitorOnline() {
        try {
            $sessionCount = $this->db->getOneRow("SELECT COUNT(s.sessionid) AS sessionCount FROM qu_la_browsers b INNER JOIN qu_la_users u ON b.browserid = u.browserid INNER JOIN qu_g_usersessions us ON u.userid = us.userid INNER JOIN qu_g_sessions s ON us.sessionid = s.sessionid WHERE b.cookie = '" . $this->db->escape(@$_COOKIE[Lib_Server::VISITOR_COOKIE]) . "'  AND s.lastreaddate > '" . date('Y-m-d H:i:s', time() - 30) . "'");
        } catch (Exception $e) {
            return false;
        }
        return $sessionCount['sessionCount'] > 0;
    }

    private function isFirstRequest() {
        return @$_GET['rc'] == '' || @$_GET['rc'] < 1;
    }

    private function getReferrerUrl() {
        return $this->fullDecodeUrl(@$_GET['ref']);
    }

    private function getPageUrl() {
        return $this->decodeUrl(@$_GET['pu']);
    }

    private function isMSIE9orLower() {
        return (1 == $this->decodeUrl(@$_GET['ieold']));
    }

    public static function fullDecodeUrl($url) {
        $url = str_replace("_|H|_", "http://", $url);
        $url = str_replace("_|S|_", "https://", $url);
        return $url;
    }

    public function decodeUrl($url) {
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

    private function parseWidgets() {
        $widgetString = stripslashes(@$_GET['wds']);
        if ($widgetString == '') {
            return;
        }
        $widgets = json_decode($widgetString);
        if (!is_array($widgets)) {
            self::logException('Widgets parameter "wds" can not be decoded to array: '.$widgetString);
            return;
        }
        foreach ($widgets as $widget) {
            if ($widget->s == 'Y') {
                continue;
            }
            switch ($widget->t) {
                case 'b': $this->initButton($widget->i, $widget->e); break;
                case 'kb': $this->initKbSearchWidget($widget->i, $widget->e); break;
                case 'f': $this->initInPageForm($widget->i, $widget->e); break;
            }
        }
    }

    private static function logException($message) {
        echo '//'.$message."\n";
    }

    private function getContactWidgetsUrl() {
        return $this->decodeUrl(@$_REQUEST['bu']) . 'accounts/default1/cache/contactwidgets/';
    }

    private function initInPageForm($id, $elementId) {
        $form = new Lib_ContactWidget($this->db);
        try {
	        $form->load($id);
        } catch (Exception $e) {
        	self::logException("Form $id does not exist: ".$e->getMessage());
        	return;
        }
	    echo ("LiveAgentTracker.getWidget('$elementId').setDateChanged('".$form->getDateChanged()."');\n");
        echo ("LiveAgentTracker.getWidget('$elementId').initWidget('".$this->getContactWidgetsUrl().$form->getFormTemplateName()."', ".$form->getFormWindowWidth().", ".$form->getFormWindowHeight().");\n");
        echo ("LiveAgentTracker.getWidget('$elementId').initialize();\n");
    }

    private function initKbSearchWidget($id, $elementId) {
        $kbSearch = new Lib_ContactWidget($this->db);
        try {
        	$kbSearch->load($id);
        } catch (Exception $e) {
        	self::logException("KB Search Widget $id does not exist: ".$e->getMessage());
        	return;
        }
        $kbSearch->updateImpressions($this->getPageUrl());
        echo ("LiveAgentTracker.getWidget('$elementId').setDateChanged('".$kbSearch->getDateChanged()."');\n");
        echo ("LiveAgentTracker.getWidget('$elementId').initWidget('".$this->getContactWidgetsUrl().$kbSearch->getKbSearchTemplateName()."', 360, 65, 'BL');\n");
        echo ("LiveAgentTracker.getWidget('$elementId').initialize();\n");
    }

    private function initButton($buttonId, $elementId) {
        $button = new Lib_Button($this->db);
        try {
            $button->load($buttonId);
        } catch (Exception $e) {
            self::logException("Button $buttonId does not exist: ".$e->getMessage());
            return;
        }

        if ($button->getType() == 'S') {
            $this->initSuggestButton($button, $elementId);
        } elseif ($button->getType() == 'P') {
            $this->initCallButton($button, $elementId);
        } else if ($button->getType() == 'C') {
            $this->initChatButton($button, $elementId);
        } else if ($button->getType() == 'O') {
            $this->initForm($button, $elementId);
        }

        echo ("LiveAgentTracker.getWidget('$elementId').initialize();\n");
    }

    private function initChatButton(Lib_Button $button, $elementId) {
        if (!$button->isOnline() && $button->getChatAttribute('chat_not_available_action') === 'N') {
            return;
        }
        $button->updateImpressions($this->getPageUrl());
        echo ("LiveAgentTracker.getWidget('$elementId').setHtml('".$this->encodeButtonCode($button)."');\n");
        echo ("LiveAgentTracker.getWidget('$elementId').setDateChanged('".$button->getDateChanged()."');\n");
        if ($button->isOnline()) {
            if ($button->getChatAction() === 'F') {
                echo ("LiveAgentTracker.getWidget('$elementId').initContactForm('".$this->getContactWidgetsUrl().$button->getOnlineFormTemplateName()."', '".$button->getOnlineFormWindowWidth()."', '".$button->getOnlineFormWindowHeight()."');\n");
            }
            $windowType = $button->getWindowType();
            if ($this->isMobile()) {
                $windowType = 'M';
            }
            if ($windowType == 'P' || $windowType == 'M') {
                $chatUrl = $this->getContactWidgetsUrl().$button->getPopoutChatTemplateName();
            } else {
                $chatUrl = $this->getContactWidgetsUrl().$button->getChatTemplateName();
            }
            echo ("LiveAgentTracker.getWidget('$elementId').initChat('".$chatUrl."', '".$windowType."', '".$button->getChatWindowWidth()."', '".$button->getChatWindowHeight()."', '".$button->getWindowPosition()."');\n");
            return;
        }

        if ($button->getChatAttribute('chat_not_available_action') === 'F') {
            echo ("LiveAgentTracker.getWidget('$elementId').initContactForm('".$this->getContactWidgetsUrl().$button->getFormTemplateName()."', '".$button->getFormWindowWidth()."', '".$button->getFormWindowHeight()."');\n");
        }
    }

    private function isMobile() {
        if ($this->mobileDetect == null) {
            require_once TRACKING_GPF_PATH.'include/Gpf/Net/MobileDetect.class.php';
            $this->mobileDetect = new Gpf_Net_MobileDetect();
        }
        return $this->mobileDetect->isMobile();
    }

    private function initForm(Lib_Button $button, $elementId) {
        $button->updateImpressions($this->getPageUrl());
        echo ("LiveAgentTracker.getWidget('$elementId').setHtml('".$this->encodeButtonCode($button)."');\n");
        echo ("LiveAgentTracker.getWidget('$elementId').setDateChanged('".$button->getDateChanged()."');\n");
        echo ("LiveAgentTracker.getWidget('$elementId').initContactForm('".$this->getContactWidgetsUrl().$button->getFormTemplateName()."', '".$button->getFormWindowWidth()."', '".$button->getFormWindowHeight()."');\n");
    }

    private function initSuggestButton(Lib_Button $button, $elementId) {
        $button->updateImpressions($this->getPageUrl());
        echo ("LiveAgentTracker.getWidget('$elementId').setHtml('".$this->encodeButtonCode($button)."');\n");
        echo ("LiveAgentTracker.getWidget('$elementId').setDateChanged('".$button->getDateChanged()."');\n");
        echo("LiveAgentTracker.getWidget('$elementId').initContactForm('".$this->getContactWidgetsUrl().$button->getSuggestPopupTemplateName()."', '".$button->getFormWindowWidth()."', '".$button->getFormWindowHeight()."');\n");
        if ($button->getAttribute('', 'allowContactUs') == 'Y' && $button->getAttribute('', 'contactButtonId') != '') {
            echo("LiveAgentTracker.createVirtualButton('" . $button->getAttribute('', 'contactButtonId') . "');");
        }
    }

    private function initCallButton(Lib_Button $button, $elementId) {
        if (!$button->isOnline()) {
            return;
        }
        $button->updateImpressions($this->getPageUrl());
        echo ("LiveAgentTracker.getWidget('$elementId').setHtml('".$this->encodeButtonCode($button)."');\n");
        echo ("LiveAgentTracker.getWidget('$elementId').setDateChanged('".$button->getDateChanged()."');\n");
        echo("LiveAgentTracker.getWidget('$elementId').initContactForm('".$this->getContactWidgetsUrl().$button->getPhonePopupTemplateName()."', '".$button->getFormWindowWidth()."', '".$button->getFormWindowHeight()."');\n");
    }

    private function encodeButtonCode(Lib_Button $button) {
        $code = $button->getCode($this->isMSIE9orLower());
        $code = str_replace("\\", "\\\\", $code);
        $code = str_replace("'", "\\'", $code);
        $code = str_replace("\r", "", $code);
        $code = str_replace("\n", "\\n", $code);
        $code = str_replace("</script>", "</scr'+'ipt>", $code);
        $code = str_replace("<script>", "<scr'+'ipt>", $code);
        return Lib_ContactWidget::encodeToPageCharset($code);
    }

    /**
     * @return Lib_Browser
     */
    private function getBrowser() {
        if ($this->browser != null) {
            return $this->browser;
        }
        $browserId = @$_REQUEST['vid'];
        if ($browserId == '' || $browserId == null || $browserId == 'null') {
            $browserId = @$_COOKIE[Lib_Server::VISITOR_COOKIE];
        }
        $this->browser = new Lib_Browser($this->db);
        try {
            $this->browser->load($browserId);
        } catch (Exception $e) {
            $this->browser->createNew();
            setcookie(Lib_Server::VISITOR_NEW_COOKIE, 'Y', time() + 3600*8, '/');
            setcookie(Lib_Server::VISITOR_COOKIE, $this->browser->getVisitorId(), time() + 3600*24*30, '/');
        }
        return $this->browser;
    }

    private function trackVisit() {
        $this->trackBrowserVisit($this->getBrowser(), $this->getSessionId());
        $this->trackPageVisit();
    }

    private function trackPageVisit() {
        $values = array('browserid' => $this->getBrowser()->getId(),
    			'datevisit' => date('Y-m-d H:i:s'),
    			'title' => $this->fix_utf8(@$_REQUEST['pt']),
    			'url' => $this->getPageUrl(),
    			'referrerurl' => $this->getReferrerUrl());
        $this->db->insertToTable('qu_la_page_visits', $values);
    }
    
    private function fix_utf8($string) {
    	if ($this->isExtensionLoaded('mbstring') && $this->isFunctionEnabled('mb_convert_encoding')) {
    		return mb_convert_encoding($string, 'UTF-8', 'UTF-8');
    	}
    	
    	return $string;
    }
    
    private function isFunctionEnabled($functionName) {
    	if (function_exists($functionName) && strstr(ini_get("disable_functions"), $functionName) === false) {
    		return true;
    	}
    	return false;
    }
    
    private function isExtensionLoaded($extensionName) {
    	return extension_loaded($extensionName);
    }

    private function loadBrowserVisit($browserid, $sessionid) {
        return $row = $this->db->getOneRow("SELECT browserid FROM qu_la_browser_visits".
                " WHERE browserid='".$this->db->escape($browserid)."'".
                "  AND sessionid='".$this->db->escape($sessionid)."'");
    }

    private function trackBrowserVisit(Lib_Browser $browser, $sessionId) {
        $this->purgeOtherBrowserVisits($browser->getId(), $sessionId);
        try {
            $row = $this->loadBrowserVisit($browser->getId(), $sessionId);
            $this->updateBrowserVisit($browser, $sessionId);
        } catch (Exception $e) {
            $this->insertNewBrowserVisit($browser, $sessionId);
        }
        $this->insertBrowserVisitEvent($browser, $sessionId);
    }

    private function insertBrowserVisitEvent(Lib_Browser $browser, $sessionId) {
        $browserVisitData = array();
        try {
            $browserVisitData = $this->loadBrowserVisit($browser->getId(), $sessionId);
        } catch(Exception $e) {
            return;
        }
        $microTime = microtime(true);
        $eventTime = number_format($microTime * 1000000 , 0, '', '');
        try {
        	$this->insertBrowserVisitEventToDb($browser, $sessionId, $eventTime);
        } catch (Exception $e) {
        	if ($e->getCode() != 1062) {
        		throw $e;
        	}
        	$eventTime = number_format($microTime * 1000000 + 1, 0, '', '');
        	try {
        	   $this->insertBrowserVisitEventToDb($browser, $sessionId, $eventTime);
        	} catch (Exception $e) {
        	    if ($e->getCode() != 1062) {
        	    	throw $e;
        	    }   
        	}
        }
    }
    
    protected function insertBrowserVisitEventToDb(Lib_Browser $browser, $sessionId, $eventTime) {
        $values = array('eventid' => $eventTime,
                        'source_sessionid' => $sessionId,
                        'channel' => "BV",
                        'clientdata' => $this->getBrowserVisitClientData($eventTime, $browser->getId(), $sessionId),
                        'subscription' => $browser->getId()
        );
        $this->db->insertToTable('qu_g_events', $values);
        $this->db->query("INSERT INTO qu_g_eventqueues (eventid, sessionid) SELECT '".$eventTime."' AS eventid, sessionid FROM qu_g_eventsubscriptions WHERE starteventid < ".$eventTime." AND channel = 'BV' AND (subscription = '".$browser->getId()."' OR subscription = '')");
    }

    private function getBrowserVisitClientData($eventTime, $browserid) {
        return "{\"t\":\"BV\",\"i\":\"" . $eventTime . "\",\"d\":[[\"name\",\"value\"],[\"browserid\",\"". $browserid ."\"],[\"date_last_visit\",\"" . date('Y-m-d H:i:s') . "\"],[\"url\",\"" . $this->getPageUrl() . "\"],[\"referrerUrl\",\"" . $this->getReferrerUrl() . "\"]]}";
    }

    protected function getNowMicroSeconds($offset = 0) {
        return number_format(microtime(true) * 1000000 + $offset, 0, '', '');
    }

    private function updateBrowserVisit(Lib_Browser $browser, $sessionId) {
        $this->db->query("UPDATE qu_la_browser_visits".
            " SET url='".$this->db->escape($this->getPageUrl())."'".
            " , date_last_visit='".date('Y-m-d H:i:s')."'".
        	" , date_last_activity='".date('Y-m-d H:i:s')."'".
            " WHERE browserid='".$this->db->escape($browser->getId())."'".
            "  AND sessionid='".$this->db->escape($sessionId)."'");
    }

    private function insertNewBrowserVisit(Lib_Browser $browser, $sessionId) {
        $values = array('browserid' => $browser->getId(),
                        'sessionid' => $sessionId,
                        'date_first_visit' => date('Y-m-d H:i:s'),
                        'date_last_visit' => date('Y-m-d H:i:s'),
        				'date_last_activity' => date('Y-m-d H:i:s'),
                        'url' => $this->getPageUrl(),
                        'referrerurl' => $this->getReferrerUrl());
        $this->db->insertToTable('qu_la_browser_visits', $values);
    }

    private function purgeOtherBrowserVisits($browserId, $sessionId) {
        $this->db->query("DELETE FROM qu_la_browser_visits".
            " WHERE browserid='".$this->db->escape($browserId)."'".
            " AND sessionid<>'".$this->db->escape($sessionId)."'");
    }

    private function getSessionId() {
        if (@$_COOKIE[Lib_Server::SESSION_COOKIE] != '') {
            return $_COOKIE[Lib_Server::SESSION_COOKIE];
        }
        $sessionId = self::generateRandomId();
        setcookie(Lib_Server::SESSION_COOKIE, $sessionId, null, '/');
        @$_COOKIE[Lib_Server::SESSION_COOKIE] = $sessionId;
        return $sessionId;
    }
}
?>

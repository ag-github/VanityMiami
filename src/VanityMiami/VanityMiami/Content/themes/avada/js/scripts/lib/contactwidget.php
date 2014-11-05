<?php
class Lib_ContactWidget {
    /**
     * @var array
     */
    private $values = array();

    /**
     * @var array
     */
    private $attributes = array();

    /**
     * @var Lib_Db
     */
    protected $db;

    public function __construct(Lib_Db $db) {
        $this->db = $db;
    }

    /**
     * @throw Exception
     */
    public function load($contactWidgetId) {
        $this->values = $this->db->getOneRow("SELECT * FROM qu_la_contactwidgets WHERE contactwidgetid='".$this->db->escape($contactWidgetId)."'");
        $attributesResource = $this->db->getRows("SELECT section, name, value FROM qu_la_contactwidget_attributes WHERE contactwidgetid='".$this->db->escape($contactWidgetId)."'");
        while ($attribute = $attributesResource->fetchArray()) {
            $this->attributes[$attribute['section']][$attribute['name']] = $attribute['value'];
        }
    }

    public function updateImpressions($pageUrl = null) {
        $this->db->query("UPDATE qu_la_contactwidgets SET impressions=impressions+1, last_display_time = '".date('Y-m-d H:i:s', time())."', last_display_url = '".$pageUrl."' WHERE contactwidgetid='".$this->getId()."'");
    }

    public function getId() {
        return $this->values['contactwidgetid'];
    }

    public function getType() {
        return $this->values['rtype'];
    }
    
    public function getDateChanged() {
        return strtotime($this->values['datechanged']);
    }

    public function getOnlineFormTemplateName() {
        return $this->getId() . '_onlineform.html';
    }

    public function getPopoutChatTemplateName() {
        return $this->getId() . '_chat_popout.html';
    }

    public function getChatTemplateName() {
        return $this->getId() . '_chat.html';
    }

    public function getChatRunningTemplateName() {
        return $this->getId() . '_chatrunning.html';
    }

    public function getFormTemplateName() {
        return $this->getId() . '_form.html';
    }

    public function getSuggestPopupTemplateName() {
        return $this->getId() . '_suggest_popup.html';
    }

    public function getPhonePopupTemplateName() {
        return $this->getId() . '_phone_popup.html';
    }

    public function getKbSearchTemplateName() {
        return $this->getId() . '_kb_search.html';
    }
    
    public function getChatDesign() {
        return $this->getAttribute('chat', 'chat_design');
    }

    public function getPhoneAction() {
        return $this->getAttribute('phone', 'phone_action');
    }

    public function getChatAction() {
        return $this->getAttribute('chat', 'chat_action');
    }

    public function isProvide($service) {
        return strpos($this->values['provide'], $service) !== FALSE;
    }

    public function isProvideOnly($service) {
        return $this->isProvide($service) && strlen($this->values['provide']) === 1;
    }

    public function getChatWindowWidth() {
        return $this->getAttribute('chat', 'chat_window_width');
    }

    public function getChatWindowHeight() {
        return $this->getAttribute('chat', 'chat_window_height');
    }

    public function getFormWindowWidth() {
        return $this->getAttribute('contactForm', 'form_window_width');
    }

    public function getFormWindowHeight() {
        return $this->getAttribute('contactForm', 'form_window_height');
    }
    
    public function getOnlineFormWindowWidth() {
        return $this->getAttribute('contactForm', 'online_form_window_width');
    }
    
    public function getOnlineFormWindowHeight() {
        return $this->getAttribute('contactForm', 'online_form_window_height');
    }

    public function getWindowPosition() {
        if ($this->getWindowType() == "E") {
            return $this->getAttribute('chat', 'embedded_position');
        }
        return $this->getAttribute('chat', 'window_position');
    }

    public function getWindowType() {
        return $this->getAttribute('chat', 'chat_type');
    }

    public function getOnlineCode() {
        return $this->values['onlinecode'];
    }

    public function isOnline() {
        return $this->values['status'] === 'N';
    }

    public function isVisitorChoose() {
        return $this->getDepartmentId() === 'customid';
    }

    public function getDepartmentId() {
        return $this->values['departmentid'];
    }

    public function getChatAttribute($name, $defaultValue = null) {
        return $this->getAttribute('chat', $name);
    }

    public function getFormAttribute($name, $defaultValue = null) {
        return $this->getAttribute('contactForm', $name);
    }

    public function getPhoneAttribute($name, $defaultValue = null) {
        return $this->getAttribute('phone', $name);
    }

    public function getInvitationAttribute($name, $defaultValue = null) {
        return $this->getAttribute('invitation', $name);
    }
    
    public function getButtonAttribute($name, $defaultValue = null) {
        return $this->getAttribute('button', $name);
    }

    public function getChatWindowPosition() {
        if ($this->getChatAttribute('chat_type') == "E") {
            return $this->getChatAttribute('embedded_position');
        }
        return $this->getChatAttribute('window_position');
    }

    public function escapeJS($text, $stringIdentifier = "'") {
        $text = str_replace("$stringIdentifier", "\\$stringIdentifier", $text);
        $text = str_replace("\r", "", $text);
        $text = str_replace("\n", "\\n", $text);
        $text = str_replace("</script>", "</scr" . $stringIdentifier . "+" . $stringIdentifier ."ipt>", $text);
        $text = str_replace("<script", "<scr" . $stringIdentifier . "+" . $stringIdentifier ."ipt", $text);
        return $text;
    }

    public function getChatUrl($baseServerUrl) {
        $contactWidgetsUrl = $this->getContactWidgetsUrl($baseServerUrl);
        if ($this->getChatAttribute('chat_type') === 'P') {
            return $contactWidgetsUrl . $this->getId() . '_chat_popout.html';
        } else {
            return $contactWidgetsUrl . $this->getId() . '_chat.html';
        }
    }

    public function getContactWidgetsUrl($baseServerUrl) {
        return Lib_Server::decodeUrl($baseServerUrl) . 'accounts/default1/cache/contactwidgets/';
    }

    protected function get($name) {
        if (!array_key_exists($name, $this->values)) {
            return null;
        }
        return $this->values[$name];
    }

    public function getAttribute($section, $name, $defaultValue = null) {
        if (array_key_exists($section, $this->attributes) && array_key_exists($name, $this->attributes[$section])) {
            return $this->attributes[$section][$name];
        }
        return $defaultValue;
    }
    
    public static function encodeToPageCharset($code) {
        $pageCharset = @$_GET['chs'];
        if($pageCharset != "" && $pageCharset != null) {
           if(extension_loaded('mbstring') && self::isFunctionEnabled('mb_convert_encoding') && self::isMbstringEncodingSupported($pageCharset)) {
            return self::encodeByMbString($code, $pageCharset);
           }
           return self::encodeByIconv($code, $pageCharset);
        }
        return $code;
    }
    
    private static function encodeByMbString($code, $pageCharset) {
       return mb_convert_encoding($code, $pageCharset, 'utf-8');
    }
    
    private static function encodeByIconv($code, $pageCharset) {
        $pageCharset = str_ireplace('windows-','CP', $pageCharset);
        if(extension_loaded('iconv') && self::isFunctionEnabled('iconv')) {
        	$result = @iconv('utf-8', $pageCharset .'//IGNORE', $code);
        	if ($result !== false) {
        		return $result;
        	}
        }
        return $code;
    }
    
    private static function isFunctionEnabled($functionName) {
        if (function_exists($functionName) && strstr(ini_get("disable_functions"), $functionName) === false) {
            return true;
        }
        return false;
    }
    
    private static function isMbstringEncodingSupported($charset) {
        foreach (mb_list_encodings() as $encoding) {
            if (strtolower($charset) == strtolower($encoding)) {
                return true;
            }
        }
        return false;
    }
}
?>

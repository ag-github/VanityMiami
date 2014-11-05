<?php
require_once 'bootstrap.php';

class La_Scripts_OldSCImport extends Gpf_Module implements Gpf_Controller{

    public function __construct() {
        parent::__construct('agent', 'A');
        $this->addController($this);
        Gpf_Event_Bus::init(new La_Event_Factory());
    }

    public function execute() {
        Gpf_NewSession::init();
        Gpf_NewSession::start();
        if($this->checkIfAdminIsLogin()) {
            $importLaTask = new La_Import_ImportOldSupportCenterData();
            $importLaTask->insertTask();
        } else {
            exit('For running import script, you need to be logged in your agent panel as admin.');
        }
    }

    protected function initProperties() {
        $this->properties = new Gpf_Module_Properties();
    }

    protected function getTheme() {
        return null;
    }

    protected function getSessionHandler() {
        return new Gpf_Session_Handler_Db();
    }

    protected function checkIfAdminIsLogin() {
        $lasid = null;
        if (isset($_COOKIE['A_la_sid'])) {
            $lasid = $_COOKIE['A_la_sid'];
        } else {
            return false;
        }
        $userSession = new Gpf_Db_UserSession();
        $userSession->setId($lasid);
        try {
            $userSession->load();
        } catch (Gpf_Exception $e) {
            return false;
        }
        try {
            $loginUser = La_Model_User::loadUser($userSession->getUserId());
        } catch (Gpf_Exception $e) {
            return false;
        }
        if ($loginUser->isAdmin() && $loginUser->getOnlineStatus() == La_Model_User_Status::ONLINE) {
            return true;
        }
        return false;
    }
}

Gpf_Module::run(new La_Scripts_OldSCImport());
?>

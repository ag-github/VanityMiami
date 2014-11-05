<?php
require_once 'bootstrap.php';

class La_Scripts_Test extends Gpf_Module implements Gpf_Controller{

	public function __construct() {
		parent::__construct('agent', 'A');

		$this->addController($this);
		Gpf_Event_Bus::init(new La_Event_Factory());
	}

	public function execute() {
		Gpf_NewSession::init();
		Gpf_NewSession::start();
		Gpf_Log::addLogger(Gpf_Log_LoggerDatabase::TYPE, Gpf_Log::DEBUG);
		//put you code here, but do not commit this file		

		$a = new La_Page_Kb_SearchWidget();
		echo $a->getHtml();
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
}

Gpf_Module::run(new La_Scripts_Test());
?>

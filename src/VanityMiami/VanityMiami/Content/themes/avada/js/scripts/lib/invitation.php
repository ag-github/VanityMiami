<?php
require_once 'lib/fast_init.php';
require_once 'lib/contactwidget.php';

class Lib_Invitation extends Lib_ContactWidget {

	public function refuse($invitationId) {
		$sql = "UPDATE qu_la_browsers SET last_action_type='R', last_action_date='".$this->db->escape(date('Y-m-d H:i:s'))
		."', last_action_meta='' WHERE cookie='".$this->db->escape(@$_COOKIE[Lib_Server::VISITOR_COOKIE])."'";
		$this->db->query($sql);
	}

	public function printJS($sessionId, $invitationId, $baseServerUrl) {
		$this->load($invitationId);
		try {
			$agent = $this->getAvailableAgent($this->getDepartmentId());
		} catch (Exception $e) {
			echo "//".$e->getMessage();
			exit();
		}		 
		$validToTime = time() + 60;
		$this->deleteInvalidInvitationAgents();
		$invitationAgentId = $this->createInvitationAgent($agent, $sessionId, $validToTime);
		$this->updateImpressions(Lib_Server::decodeUrl(@$_GET['bu']));
		$this->updateBrowser($agent);

		$code = $this->getOnlineCode();
		$code = str_replace('{$open}', "LiveAgentTracker.openInvitationChat();", $code);
		$code = str_replace('{$close}', "LiveAgentTracker.closeInvitation();", $code);
		$code = str_replace('{$agentName}',  $this->getFullName($agent['firstname'], $agent['lastname']), $code);
		$code = str_replace('{$agentAvatarUrl}',  $this->getAvatarUrl($baseServerUrl, $agent['avatar_url']), $code);
		$code = str_replace(chr(10), '', $code);
		$code = Lib_ContactWidget::encodeToPageCharset($code);

		echo "LiveAgentTracker.createInvitation('".$this->getId()."', '".$invitationAgentId."', '".$this->getDateChanged()."');\n"
				. "LiveAgentTracker.setInvitationValidTo(".date('Y,m,d,H,i,s', $validToTime).");\n"
						. "LiveAgentTracker.setInvitationParams('".$this->getButtonAttribute('online_button_width')."', '".$this->getButtonAttribute('online_button_height')."', '".$this->getButtonAttribute('online_button_position')."', '".$this->getButtonAttribute('online_button_animation')."');\n"
								. "LiveAgentTracker.initInvitationChat('".$this->getChatUrl($baseServerUrl)."', '".$this->getChatAttribute('chat_type')."', '".$this->getChatAttribute('chat_window_width')."', '".$this->getChatAttribute('chat_window_height')."', '".$this->getChatWindowPosition()."');\n"
										. "LiveAgentTracker.showInvitation('".$this->escapeJS($code)."');\n";
	}

	private function updateBrowser(array $agent) {
		$sql = "UPDATE qu_la_browsers SET last_action_type='A', last_action_date='".$this->db->escape(date('Y-m-d H:i:s'))
		."', last_action_meta='".$this->db->escape($this->getFullName($agent['firstname'], $agent['lastname']))
		."' WHERE cookie='".$this->db->escape(@$_COOKIE[Lib_Server::VISITOR_COOKIE])."'";
		$this->db->query($sql);
	}

	/**
	 * @return number
	 */
	private function createInvitationAgent(array $agent, $sessionId, $validToTime) {
		return $this->db->insertToTableAutoincrement('qu_la_invitation_agents', array('invitationid' => $this->getId(), 'userid' => $agent['userid'], 'sessionid' => $sessionId, 'validto' => date('Y-m-d H:i:s', $validToTime)));
	}

	private function deleteInvalidInvitationAgents() {
		$this->db->query('DELETE FROM qu_la_invitation_agents WHERE validto < \''.date('Y-m-d H:i:s', time()).'\'');
	}

	/**
	 * @param String $departmentId
	 * @return array
	 */
	private function getAvailableAgent($departmentId) {
		$availableAgentRow = $this->db->getOneRow('SELECT u.userid AS userid, IFNULL(c.cload, 0) + IFNULL(ia.iload, 0) AS fullload '
				.'FROM qu_la_users u '
				.'LEFT JOIN qu_la_user_departments ud ON ud.userid = u.userid '
				.'LEFT JOIN (SELECT userid, SUM(0.1) AS iload FROM qu_la_invitation_agents WHERE validto > \''.date('Y-m-d H:i:s', time()).'\' GROUP BY userid) ia ON ia.userid = u.userid '
				.'LEFT JOIN (SELECT cu.userid AS userid, COUNT(cu.conversationid) AS cload FROM qu_la_conversation_users cu INNER JOIN qu_la_conversations c ON cu.conversationid = c.conversationid WHERE ((cu.rstatus = \'R\' ) OR (cu.rstatus = \'J\' AND c.rstatus = \'T\' )) GROUP BY cu.userid) c ON u.userid = c.userid '
				.'WHERE u.rtype = \'A\' AND ud.departmentid = \''.$departmentId.'\' AND ud.onlinestatus LIKE \'%T%\' AND u.maxload_online > IFNULL(c.cload, 0) + IFNULL(ia.iload, 0) '
				.'ORDER BY u.maxload_online - fullload DESC '
				.'LIMIT 0, 1');
		return $this->db->getOneRow('SELECT u.userid AS userid, c.firstname AS firstname, c.lastname AS lastname, c.avatar_url AS avatar_url FROM qu_la_users u INNER JOIN qu_la_contacts c ON u.contactid = c.contactid WHERE userid = \''.$availableAgentRow['userid'].'\'');
	}

	private function getFullName($first, $last) {
		$fullName = trim($first);
		if (trim($last) != '') {
			$fullName .= ' ' . trim($last);
		}
		return $fullName;
	}

	private function getAvatarUrl($baseServerUrl, $avatarUrl) {
		return str_replace('__BASE_URL__', Lib_Server::decodeUrl($baseServerUrl), $avatarUrl);
	}
}
?>

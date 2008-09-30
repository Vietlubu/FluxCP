<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

$charID = $params->get('id');

if (!$charID) {
	$this->deny();
}

$char = $server->getCharacter($charID);
if ($char) {
	if ($char->account_id != $session->account->account_id && !$auth->allowedToModifyCharPrefs) {
		$this->deny();
	}
	
	$prefs = $server->getPrefs($charID, array('HideFromWhosOnline', 'HideMapFromWhosOnline'));
	
	$hideFromWhosOnline    = $prefs->get('HideFromWhosOnline');
	$hideMapFromWhosOnline = $prefs->get('HideMapFromWhosOnline');
	
	if (count($_POST)) {
		$res = $server->setPrefs($charID, array(
			'HideFromWhosOnline'    => $params->get('hide_from_whos_online') ? 1 : null,
			'HideMapFromWhosOnline' => $params->get('hide_map_from_whos_online') ? 1 : null
		));
		
		if ($res) {
			$session->setMessageData('Preferences have been modified.');
			$this->redirect($this->urlWithQs);
		}
		else {
			$errorMessage = 'Failed to modify preferences.';
		}
	}
}
else {
	
}
?>
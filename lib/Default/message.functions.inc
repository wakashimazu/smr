<?php

function getMessageTypeNames($typeID = false) {
	$typeNames = [
		MSG_GLOBAL => 'Global Messages',
		MSG_PLAYER => 'Player Messages',
		MSG_PLANET => 'Planet Messages',
		MSG_SCOUT => 'Scout Messages',
		MSG_POLITICAL => 'Political Messages',
		MSG_ALLIANCE => 'Alliance Messages',
		MSG_ADMIN => 'Admin Messages',
		MSG_CASINO => 'Casino Messages',
	];
	return $typeID === false ? $typeNames : $typeNames[$typeID];
}

function getAdminBoxNames() {
	return [
		BOX_BUGS_AUTO => 'Automatic Bug Reports',
		BOX_BUGS_REPORTED => 'Player Bug Reports',
		BOX_GLOBALS => 'Global Messages',
		BOX_ALLIANCE_DESCRIPTIONS => 'Alliance Descriptions',
		BOX_ALBUM_COMMENTS => 'Photo Album Comments',
		BOX_BARTENDER => 'Bartender Gossip',
	];
}

function &getMessagePlayer($accountID, $gameID, $messageType = false) {
	if ($accountID == ACCOUNT_ID_PORT)
		$return = '<span class="yellow">Port Defenses</span>';
	else if ($accountID == ACCOUNT_ID_ADMIN)
		$return = '<span class="admin">Administrator</span>';
	else if ($accountID == ACCOUNT_ID_PLANET)
		$return = '<span class="yellow">Planetary Defenses</span>';
	else if ($accountID == ACCOUNT_ID_ALLIANCE_AMBASSADOR)
		$return = '<span class="green">Alliance Ambassador</span>';
	else if ($accountID == ACCOUNT_ID_CASINO)
		$return = '<span class="yellow">Casino</span>';
	else if ($accountID == ACCOUNT_ID_FED_CLERK)
		$return = '<span class="yellow">Federal Clerk</span>';
	else if ($accountID == ACCOUNT_ID_OP_ANNOUNCE || $accountID == ACCOUNT_ID_ALLIANCE_COMMAND)
		$return = '<span class="green">Alliance Command</span>';
	else {
		foreach (Globals::getRaces() as $raceID => $raceInfo) {
			if ($accountID == ACCOUNT_ID_GROUP_RACES + $raceID) {
				$return = '<span class="yellow">' . $raceInfo['Race Name'] . ' Government</span>';
				return $return;
			}
		}
		if (!empty($accountID))
			$return = SmrPlayer::getPlayer($accountID, $gameID);
		else {
			switch ($messageType) {
				case MSG_ADMIN:
					$return = '<span class="admin">Administrator</span>';
				break;
					
				case MSG_ALLIANCE:
					$return = '<span class="green">Alliance Ambassador</span>';
				break;
							
				default:
					$return = 'Unknown';
			}
		}
	}
	return $return;
}

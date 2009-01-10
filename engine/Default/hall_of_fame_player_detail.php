<?
require_once(get_file_loc('SmrHistoryMySqlDatabase.class.inc'));
function cust_round($x) {
	return round($x/10)*10;
}

$acc_id = $var['acc_id'];
//view rankings of other players
$db_acc =& SmrAccount::getAccount($acc_id);
$rank_id = $db_acc->get_rank();
$game_id = $var['game_id'];
$hof_name = stripslashes($db_acc->HoF_name);
$db->query('SELECT * FROM rankings WHERE rankings_id = '.$rank_id);
if ($db->next_record())
	$rank_name = $db->f('rankings_name');

// initialize vars
$kills = 0;
$exp = 0;

// get stats
$db->query('SELECT * from account_has_stats WHERE account_id = '.$db_acc->account_id);
if ($db->next_record()) {
	$kills = ($db->f('kills') > 0) ? $db->f('kills') : 0;
	$exp = ($db->f('experience_traded') > 0) ? $db->f('experience_traded') : 0;
}
if ($var['sending_page'] == 'hof') {
	$smarty->assign('PageTopic','Extended User Rankings for '.$hof_name);
	$PHP_OUTPUT.=($hof_name.' has <font color="red">'.$kills.'</font> kills and <font color="red">'.$exp.'</font> traded experience<br /><br />');
	$PHP_OUTPUT.=($hof_name.' is ranked as a <font size="4" color="greenyellow">'.$rank_name.'</font> player.<br /><br />');
	
	$db2 = new SmrMySqlDatabase();
	$db->query('SELECT * FROM account_has_stats_cache WHERE account_id = $db_acc->account_id');
	if ($db->next_record()) {
	
		$PHP_OUTPUT.=('<b>Extended Stats</b><br />');
		$PHP_OUTPUT.=($hof_name.' has joined ' . $db->f('games_joined') . ' games.<br />');
		$PHP_OUTPUT.=($hof_name.' has busted ' . $db->f('planet_busts') . ' planets.<br />');
		$PHP_OUTPUT.=($hof_name.' has busted a total of ' . $db->f('planet_bust_levels') . ' combined levels on planets.<br />');
		$PHP_OUTPUT.=($hof_name.' has raided ' . $db->f('port_raids') . ' ports.<br />');
		$PHP_OUTPUT.=($hof_name.' has raided a total of ' . $db->f('port_raid_levels') . ' combined levels of ports.<br />');
		$PHP_OUTPUT.=($hof_name.' has done ' . $db->f('planet_damage') . ' damage to planets.<br />');
		$PHP_OUTPUT.=($hof_name.' has done ' . $db->f('port_damage') . ' damage to ports.<br />');
		$PHP_OUTPUT.=($hof_name.' has explored ' . $db->f('sectors_explored') . ' sectors.<br />');
		$PHP_OUTPUT.=($hof_name.' has died ' . $db->f('deaths') . ' times.<br />');
		$PHP_OUTPUT.=($hof_name.' has traded ' . $db->f('goods_traded') . ' goods.<br />');
		$db2->query('SELECT sum(amount) as amount FROM account_donated WHERE account_id = $db_acc->account_id');
		if ($db2->next_record())
		    $PHP_OUTPUT.=($hof_name.' has donated ' . $db2->f('amount') . ' dollars to SMR.<br />');
		$PHP_OUTPUT.=($hof_name.' has claimed ' . $db->f('bounties_claimed') . ' bounties.<br />');
		$PHP_OUTPUT.=($hof_name.' has claimed ' . $db->f('bounty_amount_claimed') . ' credits from bounties.<br />');
		$PHP_OUTPUT.=($hof_name.' has claimed ' . $db->f('military_claimed') . ' credits from military payment.<br />');
		$PHP_OUTPUT.=($hof_name.' has had a total of ' . $db->f('bounty_amount_on') . ' credits bounty placed on him/her.<br />');
		$PHP_OUTPUT.=($hof_name.' has done ' . $db->f('player_damage') . ' damage to other ships.<br />');
		$PHP_OUTPUT.=('The total experience of traders $db_acc->HoF_name has killed is ' . $db->f('traders_killed_exp') . '.<br />');
		$PHP_OUTPUT.=($hof_name.' has gained ' . $db->f('kill_exp') . ' experience from killing other traders.<br />');
		$PHP_OUTPUT.=($hof_name.' has approximately used ' . cust_round($db->f('turns_used')) . ' turns since his/her last death.<br />');
		$PHP_OUTPUT.=($hof_name.' has won ' . $db->f('blackjack_win') . ' credits from Blackjack.<br />');
		$PHP_OUTPUT.=($hof_name.' has lost ' . $db->f('blackjack_lose') . ' credits from Blackjack.<br />');
		$PHP_OUTPUT.=($hof_name.' has won ' . $db->f('lotto') . ' credits from the lotto.<br />');
		$PHP_OUTPUT.=($hof_name.' has had ' . $db->f('drinks') . ' drinks at the bar.<br />');
		$PHP_OUTPUT.=($hof_name.' has bought ' . $db->f('mines') . ' mines.<br />');
		$PHP_OUTPUT.=($hof_name.' has bought ' . $db->f('combat_drones') . ' combat drones.<br />');
		$PHP_OUTPUT.=($hof_name.' has bought ' . $db->f('scout_drones') . ' scout drones.<br />');
		$PHP_OUTPUT.=($hof_name.' has gained ' . $db->f('money_gained') . ' credits from killing.<br />');
		$PHP_OUTPUT.=($hof_name.' has killed ' . $db->f('killed_ships') . ' credits worth of ships.<br />');
		$PHP_OUTPUT.=($hof_name.' has lost ' . $db->f('died_ships') . ' credits worth of ships.<br />');
	}
} else {
	
	//current game stats
	$db2 = new SmrHistoryMySqlDatabase();
	$db2->query('SELECT * FROM game WHERE game_id = '.$game_id);
	//if next record we have an old game so we query the hist db
	if ($db2->next_record()) {
	
		$db = new SmrHistoryMySqlDatabase();
		$past = 'Yes';
	
	} else $db = new SmrMySqlDatabase();
	$db->query('SELECT * FROM player WHERE game_id = '.$game_id.' AND account_id = '.$acc_id);
	if ($db->next_record()) $playerName = stripslashes($db->f('player_name'));
	else $playerName = 'Unknown Player';
	$smarty->assign('PageTopic','Current Game Stats for '.$playerName);
	$db->query('SELECT * FROM player_has_stats_cache WHERE account_id = '.$db_acc->account_id.' AND game_id = '.$game_id);
	if ($db->next_record()) {
		$PHP_OUTPUT.=($playerName.' is ranked as a <font size="4" color="greenyellow">'.$rank_name.'</font> player.<br /><br />');
		$PHP_OUTPUT.=('<b>Current Game Extended Stats</b><br />');
		$PHP_OUTPUT.=($playerName.' has killed ' . $db->f('kills') . ' traders.<br />');
		$PHP_OUTPUT.=($playerName.' has traded ' . $db->f('experience_traded') . ' experience.<br />');
		$PHP_OUTPUT.=($playerName.' has busted ' . $db->f('planet_busts') . ' planets.<br />');
		$PHP_OUTPUT.=($playerName.' has busted a total of ' . $db->f('planet_bust_levels') . ' combined levels on planets.<br />');
		$PHP_OUTPUT.=($playerName.' has raided ' . $db->f('port_raids') . ' ports.<br />');
		$PHP_OUTPUT.=($playerName.' has raided a total of ' . $db->f('port_raid_levels') . ' combined levels of ports.<br />');
		$PHP_OUTPUT.=($playerName.' has done ' . $db->f('planet_damage') . ' damage to planets.<br />');
		$PHP_OUTPUT.=($playerName.' has done ' . $db->f('port_damage') . ' damage to ports.<br />');
		$PHP_OUTPUT.=($playerName.' has explored ' . $db->f('sectors_explored') . ' sectors.<br />');
		$PHP_OUTPUT.=($playerName.' has died ' . $db->f('deaths') . ' times.<br />');
		$PHP_OUTPUT.=($playerName.' has traded ' . $db->f('goods_traded') . ' goods.<br />');
		$PHP_OUTPUT.=($playerName.' has claimed ' . $db->f('bounties_claimed') . ' bounties.<br />');
		$PHP_OUTPUT.=($playerName.' has claimed ' . $db->f('bounty_amount_claimed') . ' credits from bounties.<br />');
		$PHP_OUTPUT.=($playerName.' has claimed ' . $db->f('military_claimed') . ' credits from military payment.<br />');
		$PHP_OUTPUT.=($playerName.' has had a total of ' . $db->f('bounty_amount_on') . ' credits bounty placed on him/her.<br />');
		$PHP_OUTPUT.=($playerName.' has done ' . $db->f('player_damage') . ' damage to other ships.<br />');
		$PHP_OUTPUT.=('The total experience of traders '.$playerName.' has killed is ' . $db->f('traders_killed_exp') . '.<br />');
		$PHP_OUTPUT.=($playerName.' has gained ' . $db->f('kill_exp') . ' experience from killing other traders.<br />');
		$PHP_OUTPUT.=($playerName.' has used ' . $db->f('turns_used') . ' turns since his/her last death.<br />');
		$PHP_OUTPUT.=($playerName.' has won ' . $db->f('blackjack_win') . ' credits from Blackjack.<br />');
		$PHP_OUTPUT.=($playerName.' has lost ' . $db->f('blackjack_lose') . ' credits from Blackjack.<br />');
		$PHP_OUTPUT.=($playerName.' has won ' . $db->f('lotto') . ' credits from the lotto.<br />');
		$PHP_OUTPUT.=($playerName.' has had ' . $db->f('drinks') . ' drinks at the bar.<br />');
		$PHP_OUTPUT.=($playerName.' has bought ' . $db->f('mines') . ' mines.<br />');
		$PHP_OUTPUT.=($playerName.' has bought ' . $db->f('combat_drones') . ' combat drones.<br />');
		$PHP_OUTPUT.=($playerName.' has bought ' . $db->f('scout_drones') . ' scout drones.<br />');
		$PHP_OUTPUT.=($playerName.' has gained ' . $db->f('money_gained') . ' credits from killing.<br />');
		$PHP_OUTPUT.=($playerName.' has killed ' . $db->f('killed_ships') . ' credits worth of ships.<br />');
		$PHP_OUTPUT.=($playerName.' has lost ' . $db->f('died_ships') . ' credits worth of ships.<br />');
	}
}
//this is needed to make the rest of loader function
//FIXME: just rename the hof variable sometime, after reviewing, rewriting the whole page might be best.
$db = new SmrMySqlDatabase();
?>

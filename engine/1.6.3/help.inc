<?php

function echo_nav($topic_id) {

	// database object
	$db = new SmrMySqlDatabase();
	$db2 = new SmrMySqlDatabase();
	$db3 = new SmrMySqlDatabase();

	// get current entry
	$db->query('SELECT * FROM manual WHERE topic_id = '.$topic_id);
	if ($db->nextRecord()) {

		$parent_topic_id = $db->getField('parent_topic_id');
		$order_id = $db->getField('order_id');
		$topic = stripslashes($db->getField('topic'));

		echo ('<table>');
		echo ('<tr>');

		// **************************
		// **  PREVIOUS
		// **************************
		$db2->query('SELECT * FROM manual WHERE parent_topic_id = '.$parent_topic_id.' AND order_id = '.$order_id.' - 1');

		// no result?
		if (!$db2->getNumRows())
			$db2->query('SELECT * FROM manual WHERE topic_id = '.$parent_topic_id);

		echo ('<th width="32">');
		if ($db2->nextRecord()) {

			$previous_topic_id = $db2->getField('topic_id');
			$previous_topic = stripslashes($db2->getField('topic'));
			echo ('<a href="'.URL.'/manual.php?'.$previous_topic_id.'"><img src="'.URL.'/images/help/previous.jpg" width="32" height="32" border="0"></a>');

		} else
			echo ('<img src="'.URL.'/images/help/empty.jpg" width="32" height="32">');
		echo ('</th>');

		// **************************
		// **  UP
		// **************************
		$db2->query('SELECT * FROM manual WHERE topic_id = '.$parent_topic_id);
		echo ('<th width="32">');
		if ($db2->nextRecord()) {

			$up_topic_id = $db2->getField('topic_id');
			$up_topic = stripslashes($db2->getField('topic'));
			echo ('<a href="'.URL.'/manual.php?'.$up_topic_id.'"><img src="'.URL.'/images/help/up.jpg" width="32" height="32" border="0"></a>');

		} else
			echo ('<img src="'.URL.'/images/help/empty.jpg" width="32" height="32">');
		echo ('</th>');

		// **************************
		// **  NEXT
		// **************************
		$db2->query('SELECT * FROM manual WHERE parent_topic_id = '.$topic_id.' AND order_id = 1');

		if (!$db2->getNumRows())
			$db2->query('SELECT * FROM manual WHERE parent_topic_id = '.$parent_topic_id.' AND order_id = '.$order_id.' + 1');

		$curr_parent_topic_id = $parent_topic_id;
		while (!$db2->getNumRows() && $curr_parent_topic_id != 0) {

			$db3->query('SELECT * FROM manual WHERE topic_id = '.$curr_parent_topic_id);
			$db3->nextRecord();
			$curr_order_id = $db3->getField('order_id');
			$curr_parent_topic_id = $db3->getField('parent_topic_id');

			$db2->query('SELECT * FROM manual WHERE parent_topic_id = '.$curr_parent_topic_id.' AND order_id = '.$curr_order_id.' + 1');

		}

		echo ('<th width="32">');
		if ($db2->nextRecord()) {

			$next_topic_id = $db2->getField('topic_id');
			$next_topic = stripslashes($db2->getField('topic'));
			echo ('<a href="'.URL.'/manual.php?'.$next_topic_id.'"><img src="'.URL.'/images/help/next.jpg" width="32" height="32" border="0"></a>');

		} else
			echo ('<img src="'.URL.'/images/help/empty.jpg" width="32" height="32">');
		echo ('</th>');

		echo ('<th width="100%" align="center" validn="middle" style="font-size:18pt;font-weight:bold;">' . get_numbering($topic_id) . $topic.'</th>');
		echo ('<th width="32"><a href="'.URL.'/manual_toc.php"><img src="'.URL.'/images/help/contents.jpg" width="32" height="32" border="0"></a></th>');

		echo ('</tr>');

		echo ('<tr>');
		echo ('<td colspan="5">');
		if (isset($previous_topic_id) && $previous_topic_id > 0)
			echo ('<b>Previous:</b> <a href="'.URL.'/manual.php?'.$previous_topic_id.'">' . get_numbering($previous_topic_id) . $previous_topic.'</a>&nbsp;&nbsp;&nbsp;');
		if (isset($up_topic_id) && $up_topic_id > 0)
			echo ('<b>Up:</b> <a href="'.URL.'/manual.php?'.$up_topic_id.'">' . get_numbering($up_topic_id) . $up_topic.'</a>&nbsp;&nbsp;&nbsp;');
		if (isset($next_topic_id) && $next_topic_id > 0)
			echo ('<b>Next:</b> <a href="'.URL.'/manual.php?'.$next_topic_id.'">' . get_numbering($next_topic_id) . $next_topic.'</a>');
		echo ('</tr>');

		echo ('</table>');

	} else
		echo ('Invalid Topic!');

}

function echo_content($topic_id) {

	// database object
	$db = new SmrMySqlDatabase();

	// get current entry
	$db->query('SELECT * FROM manual WHERE topic_id = '.$topic_id);
	if ($db->nextRecord()) {

		$parent_topic_id = $db->getField('parent_topic_id');
		$order_id = $db->getField('order_id');
		$topic = stripslashes($db->getField('topic'));
		$text = stripslashes($db->getField('text'));

		echo ('<div id="help_content">');
		echo ('<h1>' . get_numbering($topic_id) . $topic.'</h1>');
		echo ('<p>'.$text.'<p>');
		echo ('</div>');

	} else
		echo ('Invalid Topic!');

}

function echo_subsection($topic_id) {

	// database object
	$db = new SmrMySqlDatabase();
	$return='';
	// check if there are subsections
	$db->query('SELECT * FROM manual WHERE parent_topic_id = '.$topic_id.' ORDER BY order_id');
	if ($db->getNumRows()) {

		echo ('<hr noshade width="75%" size="1" align="center"/>');
		echo ('<div id="help_menu">');
		echo ('<h2>Subsections:</h2>');

		echo_menu($topic_id);

		echo ('</div>');
	}
	return $return;
}

function echo_menu($topic_id)
{
	$return='';
	// database object
	$db = new SmrMySqlDatabase();

	$db->query('SELECT * FROM manual WHERE parent_topic_id = '.$topic_id.' ORDER BY order_id');
	if ($db->getNumRows()) {

		echo ('<ul type="disc">');
		while($db->nextRecord()) {

			$sub_topic_id = $db->getField('topic_id');
			$order_id = $db->getField('order_id');
			$sub_topic = stripslashes($db->getField('topic'));

			echo ('<li><a href="'.URL.'/manual.php?'.$sub_topic_id.'">' . get_numbering($sub_topic_id) . $sub_topic.'</a></li>');
			echo_menu($sub_topic_id);

		}
		echo ('</ul>');

	}
	return $return;
}

function get_numbering($topic_id) {

	$db = new SmrMySqlDatabase();

	$db->query('SELECT * FROM manual WHERE topic_id = '.$topic_id);
	if ($db->nextRecord()) {

		$up_topic_id = $db->getField('parent_topic_id');
		$order_id = $db->getField('order_id');

		return get_numbering($up_topic_id) . $order_id . '. ';

	}

}

?>
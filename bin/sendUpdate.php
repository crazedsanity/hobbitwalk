<?php

/* 
 * Licensed under GNU General Public License v2.0+.
 * See associated LICENSE file for full license text.
 */

require_once(__DIR__ .'/../main.class.php');


use crazedsanity\core\ToolBox;
use crazedsanity\template\Template;

ToolBox::$debugPrintOpt = 1;

$db = new cs_phpDB(constant('DB_DSN'), constant('DB_USERNAME'), constant('DB_PASSWORD'));
$hw = new crazedsanity\walk\main($db);

$allRaces = $hw->getAllRaces();


//print_r($allRaces);

foreach($allRaces as $raceId=>$o) {
	$_stats = $hw->parseUpdateMessage(
			$raceId, 
			new Template(TMPLDIR .'/content/hw_updateEmail.tmpl'),
			new Template(TMPLDIR .'/content/hw_updateEmail-dataRow.tmpl')
		);
}

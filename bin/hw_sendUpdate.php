<?php


require_once(__DIR__ .'/../lib/hobbitwalk/main.class.php');
require_once(__DIR__ .'/../lib/includes.php');
//require_once(__DIR__ .'/../vendor/crazedsanity/core/AutoLoader.class.php');

AutoLoader::registerDirectory(dirname(__FILE__) .'/../vendor/crazedsanity/core');

require_once(__DIR__ .'/../vendor/crazedsanity/core/ToolBox.class.php');
require_once(__DIR__ .'/../vendor/crazedsanity/template/interfaces/iTemplate.interface.php');
require_once(__DIR__ .'/../vendor/crazedsanity/template/Template.class.php');

use \crazedsanity\ToolBox;
//use \crazedsanity\Template;

ToolBox::$debugPrintOpt = 1;

$db = new cs_phpDB(constant('DB_DSN'), constant('DB_USERNAME'), constant('DB_PASSWORD'));
$hw = new crazedsanity\walk\main($db);

//$raceData = $hw->getRaceStats(16);
//
//print_r($raceData);

$allRaces = $hw->getAllRaces();


//print_r($allRaces);

foreach($allRaces as $raceId=>$o) {
//	ToolBox::debug_print($o,1);
//	ToolBox::debug_print($hw->getEmailsForRace($raceId),1);
	$_stats = $hw->parseUpdateMessage(
			$raceId, 
			new \crazedsanity\Template(__DIR__ .'/../templates/content/hw_updateEmail.tmpl'),
			new \crazedsanity\Template(__DIR__ .'/../templates/content/hw_updateEmail-dataRow.tmpl')
		);
}
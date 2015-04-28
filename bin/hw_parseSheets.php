<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once(__DIR__ .'/../lib/includes.php');
require_once(__DIR__ .'/../lib/hobbitwalk/csv.class.php');
require_once(__DIR__ .'/../lib/hobbitwalk/data.class.php');

/*
 * Step 1: pull user's settings from the database.
 * Step 2: retrieve user's CSV
 * Step 3: parse the sheet
 * Step 4: push data into the database
 */

$db = new \cs_phpDB(constant('DB_DSN'),constant('DB_USERNAME'),constant('DB_PASSWORD'));

$csv = new crazedsanity\walk\csv($db);

//

$allUsers = $csv->getUserData();

foreach($allUsers as $uid=>$data) {
	$fh = fopen($data['data_source'], 'r');
	
	$allData = $csv->getAll(
			$fh, 
			$data['distance_column'], 
			$data['date_column'],
			$data['date_format'],
			$data['steps_column'],
			$data['distance_unit']
	);
	
	
	$insertedRows = 0;
	foreach($allData as $lineData) {
		try {
			\crazedsanity\walk\data::create($db, $uid, 
				$lineData['date'], $lineData['steps'], $lineData['distance']);
			$insertedRows++;
		}
		catch(Exception $ex) {
			//nothing to see here, move along.
		}
	}
	
	print "user: ". $data['username'] ." (uid=". $uid ."), inserted ". $insertedRows ." rows\n";
}

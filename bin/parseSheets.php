<?php

/* 
 * Licensed under GNU General Public License v2.0+.
 * See associated LICENSE file for full license text.
 */

/*
 * Step 1: pull user's settings from the database.
 * Step 2: retrieve user's CSV
 * Step 3: parse the sheet
 * Step 4: push data into the database
 */

$db = new \cs_phpDB(constant('DB_DSN'),constant('DB_USERNAME'),constant('DB_PASSWORD'));

$csv = new crazedsanity\hobbitwalk\csv($db);

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
			\crazedsanity\hobbitwalk\data::create($db, $uid, 
				$lineData['date'], $lineData['steps'], $lineData['distance']);
			$insertedRows++;
		}
		catch(Exception $ex) {
			//nothing to see here, move along.
		}
	}
	
	print "user: ". $data['username'] ." (uid=". $uid ."), inserted ". $insertedRows ." rows\n";
}

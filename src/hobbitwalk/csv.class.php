<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace crazedsanity\hobbitwalk;

use \cs_global;

/**
 * Description of csv
 *
 * @author danf
 */
class csv {
	const DISTANCE_FORMAT_KILOMETERS = 'km';
	const DISTANCE_FORMAT_MILES = 'mi';
	
	const TABLE = 'hw_csv_table';
	const KEY = 'csv_id';
	const SEQ = 'hw_csv_table_csv_id_seq';
	
	public function __construct($db) {
		$this->db = $db;
	}
	
	
	public function parseLine(array $data, $distCol, $dateCol, $dateFormat, $stepsCol, $distFormat) {
		if(isset($data[$distCol]) && is_numeric($data[$distCol])) {
			$theDistance = $data[$distCol];
		}
		else {
			throw new \Exception("distance column contains invalid data");
		}
		if(isset($data[$dateCol]) && strlen($data[$dateCol])) {
			$dateObject = date_create_from_format($dateFormat, $data[$dateCol]);
			if(is_a($dateObject, 'DateTime')) {
				$theDate = date_format($dateObject, 'Y-m-d');
			}
			else {
				throw new \Exception("date column is not in the expected format, "
						. "expected=(". $dateFormat ."), raw data=(". 
						$data[$dateCol] .")");
			}
		}
		else {
			throw new \Exception('date column contains invalid data');
		}
		
		if(isset($data[$stepsCol]) && is_numeric($data[$stepsCol])) {
			$theSteps = $data[$stepsCol];
			if($theDistance >= ($theSteps/1000)) {
				throw new \ErrorException("user must be a giant, steps (". 
						$data[$stepsCol] .") are too close to "
						. "distance (". $data[$distCol] .")");
			}
		}
		else {
			throw new \Exception("distance column has invalid data.. ". cs_global::debug_print($data,0));
		}
		
		if($distFormat == self::DISTANCE_FORMAT_KILOMETERS) {
			$theDistance = $theDistance * 1.60934;
		}
		
		$returnData = array(
			'date'		=> $theDate,
			'steps'		=> $theSteps,
			'distance'	=> $theDistance,
		);
		
		return $returnData;
	}
	
	
	public function getLine($fh) {
		return fgetcsv($fh);
	}
	
	
	public function getAll($fh, $distCol, $dateCol, $dateFormat, $stepsCol, $distFormat) {
		$myLine = array();
		$allData = array();
		$counter=0;
		while (( $myLine = $this->getLine($fh)) !== FALSE) {
			try {
				$one = $this->parseLine($myLine, $distCol, $dateCol, $dateFormat, $stepsCol, $distFormat);
				$allData[] = $one;
			}
			catch(\Exception $e) {
//				\cs_global::debug_print($e,1);
			}
			$counter++;
		}
		
		return $allData;
	}
}

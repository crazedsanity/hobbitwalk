<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace crazedsanity\hobbitwalk;

use crazedsanity\core\ToolBox;

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
	const DATEFORMAT = 'Y-m-d';
	
	public function __construct($db) {
		$this->db = $db;
	}
	
	
	public static function parseLine(array $data, $distCol, $dateCol, $dateFormat, $stepsCol, $distFormat) {
		$theDistance = self::parseDistance($data[$distCol], $distFormat);
		$theDate = self::parseDate($data[$dateCol], $dateFormat);
		$theSteps = self::parseSteps($data[$stepsCol], $theDistance);
		
		$returnData = array(
			'date'		=> $theDate,
			'steps'		=> $theSteps,
			'distance'	=> $theDistance,
		);
		
		return $returnData;
	}
	
	
	public static function parseDate($date, $dateFormat) {
		if(isset($date) && strlen($date)) {
			$dateObject = date_create_from_format($dateFormat, $date);
			if(is_a($dateObject, 'DateTime')) {
				$theDate = date_format($dateObject, self::DATEFORMAT);
			}
			else {
				throw new \Exception("date column is not in the expected format, "
						. "expected=(". $dateFormat ."), raw data=(". 
						$date .")");
			}
		}
		else {
			throw new \InvalidArgumentException('date appears to be invalid ('. $date .')');
		}
		
		return $theDate;
	}
	
	
	public static function parseDistance($dist, $format=null) {
		if(isset($dist) && is_numeric($dist)) {
			$theDistance = $dist;
			if($format == self::DISTANCE_FORMAT_KILOMETERS) {
				$theDistance = number_format(($theDistance * 1.60934), 2);
			}
		}
		else {
			throw new \InvalidArgumentException("distance appears to be invalid (".  $dist .")");
		}
		
		return $theDistance;
	}
	
	
	public static function parseSteps($steps, $distance) {
		if(isset($steps) && is_numeric($steps)) {
			if($distance >= ($steps/1000)) {
				throw new \ErrorException("user must be a giant, steps (". 
						$steps .") are too close to "
						. "distance (". $distance .")");
			}
		}
		else {
			throw new \Exception("distance column has invalid data... steps=(". $steps ."), distance=(". $distance .").. ");
		}
		
		return $steps;
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
//				if($counter > 0) {
//					throw new \Exception("failed to parse line: ". $e->getMessage());
//				}
			}
			$counter++;
		}
		
		return $allData;
	}
}

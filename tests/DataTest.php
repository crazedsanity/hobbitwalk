<?php

use crazedsanity\core\ToolBox;
use crazedsanity\hobbitwalk\Data;

class TestOfCsv extends crazedsanity\database\TestDbAbstract {
	
	//-------------------------------------------------------------------------
	public function __construct() {
		parent::__construct();
	}//end __construct()
	//-------------------------------------------------------------------------
	
	
	
	//-------------------------------------------------------------------------
	public function setUp() {
		$this->assertTrue(is_object($this->dbObj));
	}//end setUp()
	//-------------------------------------------------------------------------
	
	
	
	//-------------------------------------------------------------------------
	public function tearDown() {
	}//end tearDown()
	//-------------------------------------------------------------------------
	
	
	
	//-------------------------------------------------------------------------
	public function test_parseLine() {
		$data = array(
			0	=> '5/16/2015',
			1	=> 4778,
			2	=> 1.81,
			3	=> 'miles',
		);
		
		$dateCol = 0;
		$stepsCol = 1;
		$distCol = 2;
		$distFormat = 3;
		$dateFormat = 'm/d/Y';
		
		$parsed = crazedsanity\hobbitwalk\csv::parseLine($data, $distCol, $dateCol, $dateFormat, $stepsCol, $distFormat);
		$this->assertTrue(is_array($parsed));
		$this->assertTrue(isset($parsed['date']));
		$this->assertTrue(isset($parsed['steps']));
		$this->assertTrue(isset($parsed['distance']));
		
		
		$this->assertEquals($data[$distCol], $parsed['distance']);
		$this->assertEquals($data[$stepsCol], $parsed['steps']);
		
		$dateObj = date_create_from_format($dateFormat, $data[$dateCol]);
		$this->assertEquals(date_format($dateObj, crazedsanity\hobbitwalk\csv::DATEFORMAT), $parsed['date']);
	}
	//-------------------------------------------------------------------------
	
	
	
	//-------------------------------------------------------------------------
	public function test_getAll() {
		$fh = fopen(__DIR__ .'/files/test1.csv', 'r');
		$x = new crazedsanity\hobbitwalk\csv($this->dbObj);
		
		$dataArray = $x->getAll($fh, 2, 0, 'm/d/Y', 1, 'mi');
		
		$this->assertEquals(171, count($dataArray));
		
		foreach($dataArray as $x=>$record) {
			$this->assertTrue(isset($record['date']));
			$this->assertTrue(isset($record['steps']));
			$this->assertTrue(isset($record['distance']));
		}
	}
	//-------------------------------------------------------------------------
}



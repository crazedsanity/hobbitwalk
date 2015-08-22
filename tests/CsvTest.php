<?php

use crazedsanity\core\ToolBox;
use crazedsanity\hobbitwalk\Data;
use crazedsanity\hobbitwalk\csv;

/*
 * TODO: create test data!
 * TODO: make tests!
 */

class TestOfLockfile extends PHPUnit_Framework_TestCase {
	
	//-------------------------------------------------------------------------
	public function __construct() {
	}//end __construct()
	//-------------------------------------------------------------------------
	
	
	
	//-------------------------------------------------------------------------
	public function setUp() {
	}//end setUp()
	//-------------------------------------------------------------------------
	
	
	
	//-------------------------------------------------------------------------
	public function tearDown() {
	}//end tearDown()
	//-------------------------------------------------------------------------
	
	
	
	
	
	
	//-------------------------------------------------------------------------
	public function test_parsing() {
		$testFile = __DIR__ .'/files/test1.csv';
		$this->assertTrue(file_exists($testFile));
		$this->assertTrue(is_readable($testFile));
		
		
	}
	//-------------------------------------------------------------------------
}



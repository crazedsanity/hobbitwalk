<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace crazedsanity\walk;

use Exception;
use crazedsanity\ToolBox;

/**
 * Description of map
 *
 * @author danf
 */
class main {
	
	protected $_races;
	public $db;
	
	public function __construct(\cs_phpDB $db) {
		$this->db = $db;
	}
	
	
	public function getAllRaces() {
		$sql = "SELECT 
					r.race_id, 
					r.race_name, 
					m.map_name, 
					m.map_description, 
					r.race_start_date 
				FROM 
					hw_race_table AS r 
					INNER JOIN hw_map_table AS m USING (map_id) 
				WHERE 
					is_ended IS FALSE 
					AND is_deleted IS FALSE 
				ORDER BY 
					r.race_start_date, 
					r.race_name;";
		$data = array();
		try {
			$numrows = $this->db->run_query($sql);
			$data = $this->db->farray_fieldnames('race_id');
		} catch (Exception $ex) {
			throw new exception(__METHOD__ .": ". $ex->getMessage(), null, $ex);
		}
		$this->_races = $data;
		
		return $data;
	}
	
	
	public function getEmailsForRace($raceId) {
		$data = array();
		
		if(is_numeric($raceId) && $raceId > 0) {
			$sql = "SELECT 
						a.username, 
						a.email 
					FROM 
						cs_authentication_table AS a 
						INNER JOIN hw_participant_table AS p USING (uid) 
					WHERE 
						p.race_id=:id";
			$this->db->run_query($sql, array('id'=>$raceId));
//			$numrows = $this->db->numRows();
			$data = $this->db->farray_nvp('username', 'email');
		}
		else {
			throw new Exception(__METHOD__ .": invalid raceId");
		}
		
		return $data;
	}
	
    
	public function getRaceStats($raceId) {
		if(is_numeric($raceId) && $raceId > 0) {
			$sql = "SELECT 
				a.username, m.map_name, r.race_name, sum(d.steps) as _total_steps, sum(d.mileage) as _total_mileage 
				FROM 
					hw_data_table AS d 
					INNER JOIN cs_authentication_table AS a USING (uid) 
					INNER JOIN hw_participant_table AS p USING (uid) 
					INNER JOIN hw_race_table AS r USING (race_id) 
					INNER JOIN hw_map_table AS m USING (map_id) 
				WHERE 
					d.entry_date >= r.race_start_date 
					AND race_id=:race_id
				GROUP BY 
					a.username, 
					m.map_name, 
					r.race_name 
				ORDER BY 
					a.username, 
					m.map_name, 
					r.race_name;";
			$data = array();
			try {
				$this->db->run_query($sql, array('race_id' => $raceId));
				$numrows = $this->db->numRows();
				if($numrows >= 1) {
					$data = $this->db->farray_fieldnames('username');
				}
				else {
					throw new exception("invalid number of rows (". $numrows .") for raceId (". $raceId .")");
				}
			} catch (Exception $ex) {
				throw new exception(__METHOD__ .": ". $ex->getMessage(), null, $ex);
			}
		}
		else {
			throw new Exception(__METHOD__ .": no raceId provided");
		}
		return $data;
	}
	
	
	public function parseUpdateMessage($raceId, \crazedsanity\Template $mainTmpl, \crazedsanity\Template $rowTmpl) {
		if(!is_array($this->_races) || !count($this->_races)) {
			$this->_races = $this->getAllRaces();
//			ToolBox::debug_print($this->_races,1);
		}
		$stats = $this->getRaceStats($raceId);
//		ToolBox::debug_print($stats,1);
		$people = $this->getEmailsForRace($raceId);
		
		$finalRow = "";
		
		foreach($stats as $username=>$data) {
//			$rowTmpl->addVar()
			foreach($data as $name=>$val) {
				$rowTmpl->addVar($name, $val);
			}
			$finalRow .= $rowTmpl->render();
		}
		
		if(isset($this->_races[$raceId])) {
			foreach($this->_races[$raceId] as $n=>$v) {
				$mainTmpl->addVar($n,$v);
			}
		}
		
//		$mainTmpl->addVar('salutation', '(BETA TESTER)');
		$mainTmpl->addVar('data', $finalRow);
		
		// now send an email to each person.
		foreach($people as $username => $email) {
			if(constant('HW_OVERRIDE_EMAIL')) {
				$email = constant('HW_OVERRIDE_EMAIL');
			}
			$mainTmpl->addVar('salutation', $username);
			$this->sendEmail(
					$email,
					$username,
					'Project Hobbit Walk Updates: '. $this->_races[$raceId]['race_name'],
					$mainTmpl->render()
				);
		}
	}
	
	
	
	public function sendEmail($to, $toName, $subject, $body) {
		$m = new \PHPMailer();
		$m->setLanguage('en');
		$m->isSendmail();
		
		$m->Host = "localhost";
		$m->From = 'crazedsanity.com+hobbitwalk@gmail.com';
		$m->FromName = "Project Hobbit Walk";
		$m->addAddress($to, $toName);
		$m->ContentType = "text/plain";
		
		$m->Subject = $subject;
		$m->Body = $body;
		
		return $m->send();
	}
}

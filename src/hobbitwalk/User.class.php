<?php

namespace crazedsanity\hobbitwalk;

/**
 * Description of User
 *
 * @author danf
 */
class User {
	
	public static function getUsers() {
		$sql = "SELECT a.participant_id, a.uid, b.username FROM cs_authentication_table AS b INNER JOIN hw_participant_table AS a USING (uid)";
		if($this->db->run_query($sql)) {
			$data = $this->db->farray_fieldnames('uid');
		}
		else {
			throw new \Exception("no records while attempting to retrieve data");
		}
		
		return($data);
	}//end getUsers()
	
	
	
}

<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace crazedsanity\hobbitwalk;

/**
 * Description of data
 *
 * @author danf
 */
class data {
    
    static $tbl = 'hw_data_table';
    static $pkey = 'data_id';
    static $seq = 'hw_data_table_data_id_seq';
    
    
    static function create(\cs_phpDB $db, $uid, $date, $steps, $miles) {
        $sql = "INSERT INTO hw_data_table (uid, entry_date, steps, mileage) "
                . "VALUES (:uid, :date, :steps, :miles)";
        $data = array(
            'uid'   => $uid,
            'date'  => $date,
            'steps' => $steps,
            'miles' => $miles,
        );
        
        return $db->run_insert($sql, $data, self::$seq);
    }
    
    
    static function update(\cs_phpDB $db, $uid, $date, $steps, $miles, $dataId) {
        $sql = "UPDATE ". self::$tbl ." SET uid=:uid, date=:date, steps=:steps, "
                . "mileage=:miles WHERE data_id=:id";
        $data = array(
            'uid'   => $uid,
            'date'  => $date,
            'steps' => $steps,
            'miles' => $miles,
            'id'    => $dataId,
        );
        
        return $db->run_update($sql, $data);
    }
    
    
    static function delete(\cs_phpDB $db, $dataId) {
        $sql = "DELETE FROM ". self::$tbl ." WHERE ". self::$pkey ."=:id";
        $data = array('id'=>$dataId);
        
        return $db->run_update($sql, $data);
    }
}

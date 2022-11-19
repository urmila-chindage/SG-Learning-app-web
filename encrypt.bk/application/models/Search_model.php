<?php 
Class Search_model extends CI_Model
{
	function search_course($param = array()){
		$conditon = array();
		if(!empty($param))
		{
			foreach ($param as $key) {
				$conditon []= " cb_title LIKE '%".$key."%' ESCAPE '!' OR cb_description LIKE '%".$key."%' ESCAPE '!' ";
			}
		}
		$conditon = (sizeof($conditon) > 0)? (' AND (' .implode(' OR ', $conditon).')') : '';
		$query = "SELECT * FROM course_basics
					WHERE cb_status = 1 AND cb_account_id = ".config_item('id')." ".$conditon;
		//echo $query;die;
		return $this->db->query($query)->result_array();
	}
}
<?php
/**
* 
*/
class Assesment_error_model extends CI_model
{
	
	function __construct()
	{
		 parent::__construct();
	}
	//get user list from db user
		public function get_wrong_answer($param = array())
		{
			$user_id 			= isset($param['user_id'])?$param['user_id']:0;
			$query =$this->db->query("SELECT *, case q1.q_answer WHEN ar.ar_answer THEN 1 ELSE 0 END status 
			FROM `assessment_report` ar JOIN questions q1 ON q1.id = ar.ar_question_id WHERE ar.`ar_attempt_id` 
			IN (SELECT id FROM `assessment_attempts` WHERE aa_user_id =".$user_id.")");
			//echo $this->db->last_query();die;

			return $query->result_array();
		}
		public function get_unattempt($param = array())
		{
			$user_id 			= isset($param['user_id'])?$param['user_id']:0;
			$query =$this->db->query("SELECT * FROM `questions` WHERE `id` IN (SELECT aq_question_id
									 FROM  `assessment_questions` 
									 WHERE  `aq_assesment_id` 
									 IN (SELECT aa_assessment_id
									 FROM  `assessment_attempts` 
									 WHERE aa_user_id = ".$user_id.")
									 AND aq_question_id NOT 
									 IN (SELECT ar_question_id
									 FROM assessment_report
									 WHERE ar_question_id))");

			//echo $this->db->last_query();die;
			return $query->result_array();
		}
		public function get_options($options)
		{
			$option  = implode($options);
			//echo "<pre>";print_r($option);die();
			if(!empty($option)){
			$query = $this->db->query("SELECT * FROM `questions_options` WHERE id IN(".$option.")");
			//echo $this->db->last_query();die;
			return $query->result_array();

			}
			//echo "<pre>";print_r($options);die();
			//$options = isset($options)?$options:0;

		}
}
?>
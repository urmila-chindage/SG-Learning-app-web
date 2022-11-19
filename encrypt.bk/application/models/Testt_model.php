<?php

Class Testt_model extends CI_Model
{	
    function __construct()
    {
        parent::__construct();
    }
	public function get_categories($assesment){
        $query ="SELECT id,qc_category_name FROM questions_category WHERE id IN (SELECT DISTINCT q_category FROM questions WHERE id IN (SELECT aq_question_id FROM assessment_questions WHERE aq_assesment_id = ".$assesment['assesment_id']."))";
        //print_r($query);
        return $this->db->query($query)->result();
    }
    public function get_attempt_category_questions($category_id,$assesment_id){
        $query ="SELECT id FROM questions WHERE q_category = ".$category_id." AND id IN (SELECT aq_question_id FROM assessment_questions WHERE aq_assesment_id = ".$assesment_id." )";
        //print_r($query);
        return $this->db->query($query)->result();
    }
    public function questions($param=array())
    {
        $assesment_id   = isset($param['assesment_id'])?$param['assesment_id']:0;
        
        $this->db->select('questions.id,questions.q_type,questions.q_difficulty,questions.q_positive_mark,questions.q_negative_mark,questions.q_directions,questions.q_options,questions.q_answer,questions.q_course_id,questions.q_category,questions.q_status,questions.q_deleted,questions.action_id,questions.action_by,questions.created_date,questions.updated_date');
        $this->db->join('questions', 'assessment_questions.aq_question_id = questions.id', 'left');

        if( $assesment_id )
        {
            $this->db->where('assessment_questions.aq_assesment_id', $assesment_id);
        }
    $result =  $this->db->get('assessment_questions')->result_array();  
        //echo $this->db->last_query();die;
        return $result;
    }
    
}
/*
SELECT id FROM `questions` WHERE `q_category` = 29 AND id IN (SELECT aq_question_id FROM `assessment_questions` WHERE aq_assesment_id = 61 )

SELECT ar_question_id FROM `assessment_report` WHERE ar_attempt_id = 44

*/
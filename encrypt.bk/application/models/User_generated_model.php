<?php 
Class User_generated_model extends CI_Model
{

	function __construct()
    {
        parent::__construct();
    }

    function exam($param=array()){

    	$this->db->select('user_generated_assesment.*');
        if( isset($param['status'])) 
		{
            $this->db->where('user_generated_assesment.uga_status', 1);
		}
        if( isset($param['id'])) 
        {
            $this->db->where('user_generated_assesment.id', $param['id']);
        }

		return $this->db->get('user_generated_assesment')->row_array();
    }

    function questions($param=array())
    {
        $exam_id   = isset($param['exam_id'])?$param['exam_id']:0;
        
        $this->db->select('questions.*');
        $this->db->join('questions', 'user_generated_assesment_question.uga_question_id = questions.id', 'left');

        if( $exam_id )
        {
            $this->db->where('user_generated_assesment_question.uga_assesment_id', $exam_id);
        }
		$result =  $this->db->get('user_generated_assesment_question')->result_array();	
        //echo $this->db->last_query();die;
        return $result;
    }

    function options($param=array())
    {
        $q_options = isset($param['q_options'])?$param['q_options']:false;
        $q_answer = isset($param['q_answer'])?$param['q_answer']:false;
        if( $q_options ) 
	    {
            $this->db->where_in('id', array_map('intval', explode(',', $q_options)));
            $result = $this->db->get('questions_options')->result_array();	
            return $result;
	    }
        if( $q_answer ) 
	    {
            $this->db->where_in('id', array_map('intval', explode(',', $q_answer)));
            $result = $this->db->get('questions_options')->result_array();	
            return $result;
	    }
    }

    function question_categories($param)
    {
        $exam_id = isset($param['exam_id'])?$param['exam_id']:false;
        if(!$exam_id)
        {
            return false;
        }
        $query = 'SELECT exam_question.q_category as category_id, questions_category.qc_category_name 
                  FROM (SELECT exam_question.q_category FROM questions exam_question WHERE exam_question.id IN ( SELECT uga_question_id FROM user_generated_assesment_question WHERE uga_assesment_id ='.$exam_id.' ) GROUP BY exam_question.q_category) exam_question 
                  LEFT JOIN questions_category ON exam_question.q_category = questions_category.id ORDER BY category_id ASC';
        return $this->db->query($query)->result_array();
    }

    function save_attempts($data)
    {
        if($data['id'])
        {
            $this->db->where('id', $data['id']);
            $this->db->update('user_generated_assessment_attempt', $data);
            return $data['id'];
        }
        else
        {
            $this->db->insert('user_generated_assessment_attempt', $data);
            return $this->db->insert_id();
        }
    }

    function save_report($data)
    {
	    if($data['id'])
	    {
	            $this->db->where('id', $data['id']);
	            $this->db->update('user_generated_assessment_report', $data);
	            return $data['id'];
	    }
	    else
	    {
	            $this->db->insert('user_generated_assessment_report', $data);
	            return $this->db->insert_id();
	    }
    }

    function attempted_test($param=array()){

        $user_id        = isset($param['user_id'])?$param['user_id']:false;
        $attempted_id   = isset($param['attempted_id'])?$param['attempted_id']:false;

        $this->db->select('users.*,user_generated_assessment_attempt.uga_attempted_date, user_generated_assessment_attempt.id as attempted_id, user_generated_assesment.uga_duration, user_generated_assessment_attempt.uga_duration as ugaa_duration, user_generated_assesment.uga_title, user_generated_assesment.id as assessment_id, user_generated_assesment.uga_category');
        $this->db->from('user_generated_assessment_attempt');
        $this->db->join('users', 'users.id = user_generated_assessment_attempt.uga_user_id', 'left');
        $this->db->join('user_generated_assesment', 'user_generated_assesment.id = user_generated_assessment_attempt.uga_assessment_id', 'left');
        if($user_id){
            $this->db->where('user_generated_assessment_attempt.uga_user_id', $user_id);
        }
        if($attempted_id){
            $this->db->where('user_generated_assessment_attempt.id', $attempted_id);
        }

        return $this->db->get()->result_array();
    }

    function get_category_name($category_id){

        $this->db->select('ct_name');
        $this->db->from('categories');
        $this->db->where('id', $category_id);
        $this->db->where('categories.ct_account_id', config_item('id'));
        return $this->db->get()->row_array();
    }

    function assessment_report($param =array()){

        $attempted_id   = isset($param['attempted_id'])?$param['attempted_id']:0;
        $this->db->select('questions.q_type, questions.q_positive_mark, questions.q_negative_mark, questions.q_question, questions.q_options, questions.q_answer, user_generated_assessment_report.ugar_answer, user_generated_assessment_report.ugar_mark, user_generated_assessment_report.id as utr_id');
        $this->db->from('user_generated_assessment_attempt');
        $this->db->join('user_generated_assessment_report','user_generated_assessment_report.ugar_attempted_id = user_generated_assessment_attempt.id','left');
        $this->db->join('questions','questions.id = user_generated_assessment_report.ugar_question_id','left');

        if($attempted_id){
            $this->db->where('user_generated_assessment_attempt.id', $attempted_id);
        }
        $result = $this->db->get()->result_array();
        return $result;
    }

    function get_option_value($str){
        $this->db->select('*');
        $this->db->from('questions_options');
        $this->db->where_in('id', explode(',', $str));
        return $this->db->get()->result_array();
    }
}

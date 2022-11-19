<?php 
Class Material_model extends CI_Model
{	
    function __construct()
    {
        parent::__construct();
    }
    
    function courses($param = array())
    {
        $plan_in = isset($param['plan_in'])?$param['plan_in']:0;
        
        $query = 'SELECT id, a_course_id, GROUP_CONCAT(a_lecture_id) as lectures FROM assessments WHERE CONCAT(",", a_plans, ",") LIKE CONCAT("%,", '.$plan_in.', ",%") GROUP BY a_course_id';
        $result = $this->db->query($query)->result_array();
        return $result;
    }
    function course($param = array())
    {
        $course_id = isset($param['course_id'])?$param['course_id']:0;
        
        $query = 'SELECT id, a_course_id, GROUP_CONCAT(a_lecture_id) as lectures FROM assessments WHERE a_course_id = "'.$course_id.'" GROUP BY a_course_id';
        $result = $this->db->query($query)->row_array();
        return $result;
    }
}
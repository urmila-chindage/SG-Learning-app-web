<?php

class Survey_model extends CI_Model{

    function __construct()
    {
        parent::__construct();
    }

    function survey_details($params = array())
    {
        $lecture_id     = isset($params['lecture_id'])? $params['lecture_id'] : false;
        if($lecture_id)
        {
            $this->db->where('s_lecture_id', $lecture_id);
        }
        $this->db->select('id as survey_id, s_name, s_course_id, s_description, s_tutor_name');
        return $this->db->get('survey')->row_array();
    }
    
    function survey_questions($params = array())
    {
        $survey_id      = isset($params['survey_id'])? $params['survey_id'] : false;
        if($survey_id)
        {
            $this->db->where('sq_survey_id', $survey_id);
        }
        $this->db->select('id, sq_question, sq_required, sq_type, sq_options, sq_low_limit, sq_high_limit, sq_low_limit_label, sq_high_limit_label');
        return $this->db->get('survey_questions')->result_array();
    }

    function save_user_response($data = array()){
        return $this->db->insert('survey_user_response', $data);
    }

    function surveys($params = array()) {
        if(isset($params['lecture_id'])) {
            $this->db->where('s_lecture_id', $params['lecture_id']);
        }
        if(isset($params['course_id'])) {
            $this->db->where('s_course_id', $params['course_id']);
        }
        if(isset($params['tutor_id'])) {
            $this->db->where('s_tutor_id', $params['tutor_id']);
        }
        $select = isset($params['select'])?$params['select']:'*';
        $this->db->select($select);
        return $this->db->get('survey')->result_array();
    }
}
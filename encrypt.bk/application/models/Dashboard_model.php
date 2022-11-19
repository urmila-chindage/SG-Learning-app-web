<?php 
Class Dashboard_model extends CI_Model
{	
    function __construct()
    {
        parent::__construct();
    }
    
    function get_attempted_courses($param=array())
    {
        $user_id = isset($param['user_id'])?$param['user_id']:false;
        $return = array();
        if($user_id)
        {
            $query = 'SELECT assessments_cp.*
                        FROM assessment_attempts 
                        LEFT JOIN (SELECT assessments_cp.id, assessments_cp.a_course_id, course_basics.cb_title
                        FROM assessments assessments_cp 
                        LEFT JOIN course_basics ON assessments_cp.a_course_id = course_basics.id
                        ) assessments_cp ON assessment_attempts.aa_assessment_id = assessments_cp.id
                        WHERE aa_user_id = '.$user_id.'
                        GROUP BY assessments_cp.a_course_id';
            $return = $this->db->query($query)->result_array();
        }
        return $return;
    }
    
    function get_assesment_attempts($param = array())
    {
        $user_id    = isset($param['user_id'])?$param['user_id']:false;
        $course_id  = isset($param['course_id'])?$param['course_id']:false;
        $return     = array();
        $assessment_where_condition = '';
        if($course_id)
        {
            $assessment_where_condition = ' AND assessments_cp.a_course_id =  '.$course_id;
        }
        if($user_id)
        {
            $query = 'SELECT assessment_attempts.id as a_attempt_id, assessment_attempts.aa_assessment_id, assessment_attempts.aa_user_id, assessment_attempts.aa_attempted_date, assessment_attempts.aa_duration, assessments_cp.*, assessment_report_cp.total_mark
                        FROM assessment_attempts 
                        LEFT JOIN (SELECT assessments_cp.id, assessments_cp.a_course_id, assessments_cp.a_lecture_id, course_lectures.cl_lecture_name
                        FROM assessments assessments_cp 
                        LEFT JOIN course_lectures ON assessments_cp.a_lecture_id = course_lectures.id
                        ) assessments_cp ON assessment_attempts.aa_assessment_id = assessments_cp.id
                        LEFT JOIN (SELECT ar_attempt_id,  SUM(ar_mark) as total_mark FROM assessment_report assessment_report_cp GROUP BY ar_attempt_id ) assessment_report_cp ON assessment_attempts.id = assessment_report_cp.ar_attempt_id
                        WHERE aa_user_id = '.$user_id.' '.$assessment_where_condition.'
                        ORDER BY assessment_attempts.aa_attempted_date DESC';
            $return = $this->db->query($query)->result_array();
        }
        //echo $this->db->last_query();die;
        return $return;
    }
    
    function get_challenge_zone_attempts($param=array())
    {
        $user_id = isset($param['user_id'])?$param['user_id']:false;
        $return = array();
        if($user_id)
        {
            $query = 'SELECT challenge_zone_attempts.*, challenge_zone.cz_title, challenge_zone_report_cp.*
                        FROM challenge_zone_attempts 
                        LEFT JOIN challenge_zone ON challenge_zone_attempts.cza_challenge_zone_id = challenge_zone.id 
                        LEFT JOIN ( SELECT czr_attempt_id,  SUM(czr_mark) as total_mark FROM challenge_zone_report challenge_zone_report_cp GROUP BY czr_attempt_id ) challenge_zone_report_cp ON challenge_zone_attempts.id = challenge_zone_report_cp.czr_attempt_id
                        WHERE cza_user_id = '.$user_id;
            $return = $this->db->query($query)->result_array();
        }
        return $return;
    }
    
    function get_user_generated_attempts($param=array())
    {
        $user_id = isset($param['user_id'])?$param['user_id']:false;
        $return = array();
        if($user_id)
        {
            $query = 'SELECT user_generated_assessment_attempt.*, user_generated_assesment.uga_title, user_generated_assessment_report_cp.*
                        FROM user_generated_assessment_attempt 
                        LEFT JOIN user_generated_assesment ON user_generated_assessment_attempt.uga_assessment_id = user_generated_assesment.id 
                        LEFT JOIN ( SELECT ugar_attempted_id,  SUM(ugar_mark) as total_mark FROM user_generated_assessment_report user_generated_assessment_report_cp GROUP BY ugar_attempted_id ) user_generated_assessment_report_cp ON user_generated_assessment_attempt.id = user_generated_assessment_report_cp.ugar_attempted_id
                        WHERE user_generated_assesment.uga_user_id = '.$user_id;
            $return = $this->db->query($query)->result_array();
        }
        return $return;
    }

    function get_question_topics($param = array()){
        if(isset($param['select'])){
            $this->db->select($param['select']);
        }else{
            $this->db->select('id,qc_category_name');
        }

        if(isset($param['id'])){
            $this->db->where('id',$param['id']);
        }

        $this->db->where('qc_deleted','0');
        $this->db->where('qc_status','1');

        $result = $this->db->get('questions_category')->result_array();

        return $result;
    }

    // For online test by santhosh

    function assessment_attempt_details($param = array()){
        $attempt_id                 = isset($param['attempt_id'])?$param['attempt_id']:false;
        $select                     = isset($param['select'])?$param['select']:'assessment_attempts.id,assessment_attempts.aa_assessment_id,assessment_attempts.aa_user_id,DATE_FORMAT(assessment_attempts.aa_attempted_date,"%M %D %Y") AS aa_attempted_date,assessment_attempts.aa_duration,assessment_attempts.aa_valuated,assessment_attempts.aa_mark_scored,assessment_attempts.aa_total_mark,assessment_attempts.aa_total_questions,assessment_attempts.aa_total_duration,assessments.a_lecture_id,course_lectures.cl_lecture_name';

        $this->db->select($select);
        $this->db->join('assessments','assessment_attempts.aa_assessment_id = assessments.id','left');
        $this->db->join('course_lectures','assessments.a_lecture_id = course_lectures.id','left');

        if($attempt_id){
            $this->db->where('assessment_attempts.id',$attempt_id);
        }

        $result                     = $this->db->get('assessment_attempts')->row_array();

        return $result;
    }

    function assessment_attempt_questions($param = array()){
        $attempt_id                 = isset($param['attempt_id'])?$param['attempt_id']:false;
        $assessment_id              = isset($param['assessment_id'])?$param['assessment_id']:false;
        $select                     = isset($param['select'])?$param['select']:'assessment_questions.id,assessment_questions.aq_assesment_id,assessment_questions.aq_question_id,assessment_questions.aq_positive_mark,assessment_questions.aq_negative_mark,questions.q_type,questions.q_options,questions.q_answer,assessment_report.ar_attempt_id,assessment_report.ar_question_id,assessment_report.ar_answer,assessment_report.ar_duration,assessment_report.ar_mark';

        $this->db->select($select);

        $this->db->join('questions','assessment_questions.aq_question_id = questions.id','left');

        $this->db->join('assessment_report','questions.id = assessment_report.ar_question_id','left');

        if($assessment_id){
            $this->db->where('assessment_questions.aq_assesment_id',$assessment_id);
        }

        if($attempt_id){
            $this->db->where('assessment_report.ar_attempt_id',$attempt_id);
        }

        $result                     = $this->db->get('assessment_questions')->result_array();

        return $result;
    }

    // For online test by santhosh

     function plan_details($param = array()){
        $plan_ids               = isset($param['plan_ids'])?$param['plan_ids']:false;
        $plan_ids_exclude       = isset($param['exclude'])?$param['exclude']:false;
        $select                 = isset($param['select'])?$param['select']:'plans.id,plans.p_name,plans.p_plan_type,plans.p_slogan,plans.p_price,plans.p_validity_type,plans.p_validity,plans.p_short_description,plans.p_plan_features,plans.p_advantages';

        $this->db->select($select);

        if($plan_ids){
            $this->db->where_in('plans.id',explode(',',$plan_ids));
        }

        if($plan_ids_exclude){
            $this->db->where_not_in('plans.id',$plan_ids_exclude);
        }

        $this->db->where('plans.p_deleted','0');
        $this->db->where('plans.p_status','1');
        $this->db->where('plans.p_account_id',config_item('id'));

        $result                 = $this->db->get('plans')->result_array();

        return $result;
    }
}
?>
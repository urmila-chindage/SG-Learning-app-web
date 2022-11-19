<?php 
Class Scheduler_model extends CI_Model
{	
    function __construct()
    {
        parent::__construct();
    }
    
    function CourseLive($param = array()){
        $event_id       = isset($param['event_id'])?$param['event_id']:false;
        $this->db->select('course_basics.id,course_lectures.cl_lecture_name,course_basics.cb_title,course_basics.id,live_lectures.ll_date,live_lectures.ll_time,live_lectures.ll_files');
        if($event_id){
            $this->db->where('live_lectures.id',$event_id);
        }

        $this->db->where('course_basics.cb_status','1');
        $this->db->where('course_basics.cb_deleted','0');
        $this->db->where('course_basics.cb_account_id',config_item('id'));
        $this->db->join('course_lectures','live_lectures.ll_lecture_id = course_lectures.id','left');
        $this->db->join('course_basics','course_lectures.cl_course_id = course_basics.id','left');

        $result         = $this->db->get('live_lectures')->row_array();

        return $result;
    }

    function Assessments($param = array()){
        $course_id       = isset($param['course_id'])?$param['course_id']:false;
        if(isset($param['select'])){
            $this->db->select($param['select']);
        }else{
            $this->db->select('course_lectures.id,course_lectures.cl_lecture_name,assessments.id AS assessment_id');
        }

        if($course_id){
            $this->db->where('course_lectures.cl_course_id',$course_id);
        }

        $this->db->where('course_lectures.cl_status','1');
        $this->db->where('course_lectures.cl_deleted','0');
        $this->db->where('course_lectures.cl_lecture_type','3');
        $this->db->join('assessments','course_lectures.id = assessments.a_lecture_id','left');

        $result         = $this->db->get('course_lectures')->result_array();

        return $result;
    }
}
<?php

Class Test_model extends CI_Model
{	
    function __construct()
    {
        parent::__construct();
    }
	
	function tests($param = array()){
        $test_id        = isset($param['id'])?$param['id']:false;
		$select 		= isset($param['select'])?$param['select']:false;
		$limit 			= isset($param['limit'])?$param['limit']:0;
        $offset 		= isset($param['offset'])?$param['offset']:0;
        $keyword 		= isset($param['keyword'])?$param['keyword']:false;
        $status 		= isset($param['status'])?$param['status']:'';
        $count       	= isset($param['count'])?$param['count']:false;
        $not_deleted    = isset($param['not_deleted'])?$param['not_deleted']:false;
        $filter 		= isset($param['filter'])?$param['filter']:0;
        
		if($select){
			$this->db->select($select);
		}else{
			$this->db->select('course_lectures.id,course_lectures.cl_lecture_name,course_lectures.cl_status,course_lectures.cl_deleted,course_lectures.updated_date,users.us_name AS action_by,web_actions.id AS action_id,web_actions.wa_name AS action');
		}

		$this->db->join('assessments','course_lectures.id = assessments.a_lecture_id','left');
		$this->db->join('users','course_lectures.action_by = users.id','left');
		$this->db->join('web_actions','course_lectures.action_id = web_actions.id','left');
		$this->db->where('course_lectures.cl_lecture_type','3');

		$this->db->where('course_lectures.cl_account_id',config_item('id'));
		
		if($limit>0)
        {
            $this->db->limit($limit, $offset);
        }

        if($test_id){
            $this->db->where('course_lectures.id',$test_id);
        }

        if($keyword)
        {
            $this->db->like('course_lectures.cl_lecture_name',$keyword); 
        }

        if( $not_deleted )
        {
            $this->db->where('course_lectures.cl_deleted', '0'); 
        }

        if($filter)
        {
            switch ($filter){
                case 'active':
                    $status = '1';
                    $this->db->where('course_lectures.cl_deleted', '0'); 
                    break;
                case 'inactive':
                    $this->db->where('course_lectures.cl_deleted', '0'); 
                    $status = '0';
                    break;
                case 'pending_approval':
                    $this->db->where('course_lectures.cl_deleted', '0'); 
                    $status = '2';
                    break;
                case 'deleted':
                    $this->db->where('course_lectures.cl_deleted', '1'); 
                    break;

                default:
                    break;
            }
        }
        
        if( $status != '' )
        {
            $this->db->where('course_lectures.cl_status', $status); 
        }
        if($test_id){
            $result = $this->db->get('course_lectures')->row_array();
        }else{
            if($count){
                $result = $this->db->get('course_lectures')->num_rows();
            }else{
                $result = $this->db->get('course_lectures')->result_array();
            }
        }
        //echo $this->db->last_query();die;
        return $result;
	}
    
    function instructions($param = array()){
        $select         = isset($param['select'])?$param['select']:false;
        if($select){
            $this->db->select($select);
        }else{
            $this->db->select('assessment_instructions.id,assessment_instructions.ai_title,assessment_instructions.ai_instruction,assessment_instructions.created_date,assessment_instructions.updated_date');
        }

        $this->db->where('assessment_instructions.ai_status','1');
        $this->db->where('assessment_instructions.ai_deleted','0');
        $this->db->where('assessment_instructions.ai_account_id',config_item('id'));

        $result = $this->db->get('assessment_instructions')->result_array();

        return $result;
    }

    function save($param = array()){
        $save           = array();
        course_lecture_activity_save($param);
        if(isset($param['id']))
        {
            $save                                   = array();
            if(isset($param['cl_lecture_name'])){
                $save['cl_lecture_name']            = $param['cl_lecture_name'];
            }
            if(isset($param['cl_status'])){
                $save['cl_status']                  = $param['cl_status'];
            }
            if(isset($param['cl_deleted'])){
                $save['cl_deleted']                 = $param['cl_deleted'];
            }
            if(isset($param['cl_status'])){
                $save['cl_status']                  = $param['cl_status'];
            }
            if(isset($param['cl_limited_access'])){
                $save['cl_limited_access']          = $param['cl_limited_access'];
            }
            if(isset($param['cl_lecture_image'])){
                $save['cl_lecture_image']              = $param['cl_lecture_image'];
            }

            $save['updated_date']   = date('Y-m-d H:i:s');
            $save['action_id']      = $param['action_id'];
            $save['action_by']      = $param['action_by'];
            $this->db->where('course_lectures.id',$param['id']);
            //$this->db->where('course_lectures.id',$param['lecture_id']);
            $this->db->update('course_lectures',$save);
            $save                   = array();
            $save['id']             = $param['assessment_id'];
            $save['a_lecture_id']   = $param['id'];

            if(isset($param['assessment_id'])){
                $save['id']         = $param['assessment_id'];
            }
            //Step one
            if(isset($param['a_category'])){
                $save['a_category']      = $param['a_category'];
            }
            if(isset($param['a_duration'])){
                $save['a_duration']      = $param['a_duration'];
            }
            if(isset($param['a_questions'])){
                $save['a_questions']     = $param['a_questions'];
            }
            if(isset($param['a_mark'])){
                $save['a_mark']          = $param['a_mark'];
            }
            if(isset($param['a_instructions'])){
                $save['a_instructions']  = $param['a_instructions'];
            }

            //Step two

            if(isset($param['a_qgrouping'])){
                $save['a_qgrouping']     = $param['a_qgrouping'];
            }
            if(isset($param['a_qshuffling'])){
                $save['a_qshuffling']    = $param['a_qshuffling'];
            }
            if(isset($param['a_show_mark'])){
                $save['a_show_mark']     = $param['a_show_mark'];
            }
            if(isset($param['a_limit_navigation'])){
                $save['a_limit_navigation']= $param['a_limit_navigation'];
            }
            if(isset($param['a_fail_pass_message'])){
                $save['a_fail_pass_message']= $param['a_fail_pass_message'];
            }
            if(isset($param['a_pass_message'])){
                $save['a_pass_message']  = $param['a_pass_message'];
            }
            if(isset($param['a_fail_message'])){
                $save['a_fail_message']  = $param['a_fail_message'];
            }
            if(isset($param['a_pass_percentage'])){
                $save['a_pass_percentage']= $param['a_pass_percentage'];
            }
            if(isset($param['a_attend_all'])){
                $save['a_attend_all']    = $param['a_attend_all'];
            }
            if(isset($param['a_que_report'])){
                $save['a_que_report']= $param['a_que_report'];
            }
            if(isset($param['a_test_report'])){
                $save['a_test_report']    = $param['a_test_report'];
            }
            if(isset($param['a_show_smessage'])){
                $save['a_show_smessage']= $param['a_show_smessage'];
            }
            if(isset($param['a_smessage'])){
                $save['a_smessage']    = $param['a_smessage'];
            }

            if(isset($param['a_has_pass_fail'])){
                $save['a_has_pass_fail']  = $param['a_has_pass_fail'];
            }

            if(isset($param['a_submit_immediate'])){
                $save['a_submit_immediate']  = $param['a_submit_immediate'];
            }

            //step Four
            if(isset($param['a_from'])){
                $save['a_from']         = $param['a_from'];
            }
            if(isset($param['a_to'])){
                $save['a_to']           = $param['a_to'];
            }
            if(isset($param['a_published'])){
                $save['a_published']    = $param['a_published'];
            }

            //Step Five
            if(isset($param['a_institutions'])){
                $save['a_institutions']  = $param['a_institutions'];
            }

            if(isset($param['a_groups'])){
                $save['a_groups']        = $param['a_groups'];
            }

            if(isset($param['cl_limited_access'])){
                $save['a_total_attempt'] = $param['cl_limited_access'];
            }

            $save['action_id']      = $param['action_id'];
            $save['action_by']      = $param['action_by'];
            $save['update']         = 1;
            //$save['assesment']      = 1;
            $this->saveAssesment($save);
            $insert_id              = $param['id'];
        }
        else
        {
            $save                       = array();
            $save['cl_limited_access']  = $param['cl_limited_access'];
            $save['cl_lecture_name']    = $param['cl_lecture_name'];
            $save['cl_lecture_type']    = $param['cl_lecture_type'];
            $save['cl_status']          = $param['cl_status'];
            $save['cl_deleted']         = $param['cl_deleted'];
            $save['action_id']          = $param['action_id'];
            $save['action_by']          = $param['action_by'];
            $save['cl_account_id']      = $param['cl_account_id'];
            
            $this->db->insert('course_lectures',$save);
            $save                   = array();
            $insert_id              = $this->db->insert_id();
            $save['a_lecture_id']   = $insert_id;
            if(isset($param['a_instructions'])){
                $save['a_instructions']  = $param['a_instructions'];
            }
            $save['action_id']      = $param['action_id'];
            $save['action_by']      = $param['action_by'];
            $this->saveAssesment($save);
        }

        return $insert_id;
    }

    function saveAssesment($param = array()){

        if(isset($param['update'])){
            unset($param['update']);
                $assesment_id               = $param['id'];
                $key                        = 'assesment_'.$assesment_id;
                $objects                    = array();
                $objects['key']             = $key;
                $assessment_cache           = $this->memcache->get($objects);
                if(!empty($assessment_cache)){
                    $this->memcache->delete($key);   
                }
            $this->db->where('assessments.a_lecture_id',$param['a_lecture_id']);
            $this->db->update('assessments',$param);
            //echo $this->db->last_query();die;
            return $param['a_lecture_id'];
        }else{
            $this->db->insert('assessments',$param);
            return $this->db->insert_id();
        }
    }

    function saveAssesmentOverride($param = array()){
        if($param['id']!=0){
            $this->db->where('lecture_override.id',$param['id']);
            $this->db->update('lecture_override',$param);
            return $param['id'];
        }else{
            $this->db->insert('lecture_override',$param);
            return $this->db->insert_id();
        }
        //echo $this->db->last_query();die;
    }

    function deleteAssesmentOverride($param = array()){
        $id           = isset($param['id'])?$param['id']:0;
        $this->db->where('lecture_override.id',$param['id']);
        $this->db->delete('lecture_override');
        return true;
    }

    

    function test_details($param = array()){
        $test_id            = isset($param['test_id'])?$param['test_id']:false;
        $select             = isset($param['select'])?$param['select']:'course_basics.cb_title, course_lectures.id,course_lectures.cl_lecture_name,course_lectures.cl_duration,course_lectures.cl_status,course_lectures.cl_deleted,course_lectures.created_date,course_lectures.updated_date,assessments.id AS assesment_id,assessments.a_lecture_id,assessments.a_instruction_id,assessments.a_duration,assessments.a_pass_percentage';
        $this->db->select($select);
        $this->db->join('assessments','course_lectures.id = assessments.a_lecture_id','left');
        $this->db->join('course_basics','course_lectures.cl_course_id = course_basics.id','left');
        if($test_id){
             $this->db->where('course_lectures.id',$test_id);
        }

        $result             = $this->db->get('course_lectures')->row_array();
        //echo $this->db->last_query();die;
        return $result;
    }

    function test_questions($param = array()){
        $select             = isset($param['select'])?$param['select']:false;
        $assessment_id      = isset($param['assessment_id'])?$param['assessment_id']:false;
        $count              = isset($param['count'])?true:false;
        if($select){
            $this->db->select($select);
        }else{
            $this->db->select('assessment_questions.id,questions.id AS question_id,questions.q_type,assessment_questions.aq_positive_mark,assessment_questions.aq_negative_mark,questions.q_question ');
        }
        $this->db->join('questions','assessment_questions.aq_question_id = questions.id','left');
        $this->db->where('questions.q_account_id',config_item('id'));
        if($assessment_id){
            $this->db->where('assessment_questions.aq_assesment_id',$assessment_id);
        }

        if($count){
            $result             = $this->db->get('assessment_questions')->num_rows();
        }else{
            $result             = $this->db->get('assessment_questions')->result_array();
        }
        //echo $this->db->last_query();die;
        return $result;
    }

    function test_plans($param = array()){
        $select             = isset($param['select'])?$param['select']:false;
        $status             = isset($param['status'])?$param['status']:'1';
        $deleted            = isset($param['deleted'])?$param['deleted']:'0';

        if($select){
            $this->db->select($select);
        }else{
            $this->db->select('plans.id,plans.p_name');
        }

        $this->db->where('plans.p_status',$status);
        $this->db->where('plans.p_deleted',$deleted);
        $this->db->where('plans.p_account_id',config_item('id'));

        $result             = $this->db->get('plans')->result_array();

        return $result;
        
    }

    function save_assessment_question($param = array()){
        $id             = isset($param['id'])?$param['id']:0;
        $save           = $param['save'];
        $this->db->where('assessment_questions.id',$id);
        $this->db->update('assessment_questions',$save);
        return $id;
    }
    
    function update_assesment($param = array()){
        $a_lecture_id           = isset($param['a_lecture_id'])?$param['a_lecture_id']:0;
        // $save['a_questions']    = isset($param['a_questions'])?$param['a_questions']:0;
        // $save['a_mark']         = isset($param['a_mark'])?$param['a_mark']:0;
        $this->db->where('assessments.a_lecture_id',$a_lecture_id);
        $this->db->update('assessments',$param);
        //echo $this->db->last_query();die;
        return $a_lecture_id;
    }

    function delete_aquestion($param = array()){
        $q_id           = isset($param['id'])?$param['id']:0;
        $this->db->where('assessment_questions.id',$q_id);
        $this->db->delete('assessment_questions');
        return true;
    }

    function assessment_validation($param = array()){
        $select                 = isset($param['select'])?$param['select']:'course_lectures.id,course_lectures.cl_lecture_name,assessments.id AS assessment_id,assessments.a_questions,assessments.a_mark,assessments.a_has_pass_fail,COUNT(assessment_questions.id) AS added_questions,SUM(assessment_questions.aq_positive_mark) AS mark_available';

        $lecture_id             = isset($param['lecture_id'])?$param['lecture_id']:false;

        $this->db->select($select);

        $this->db->join('assessments','course_lectures.id = assessments.a_lecture_id','left');
        $this->db->join('assessment_questions','assessments.id = assessment_questions.aq_assesment_id','left');

        $this->db->where('course_lectures.cl_account_id',config_item('id'));
        if($lecture_id){
            $this->db->where('course_lectures.id',$lecture_id);
        }

        $result                 = $this->db->get('course_lectures')->row_array();

        //echo $this->db->last_query();die;

        return $result;
    }

    function override_details($param = array()){
        $select                 = isset($param['select'])?$param['select']:'lecture_override.id,DATE_FORMAT(lecture_override.lo_start_date, "%d-%m-%Y") as lo_start_date,DATE_FORMAT(lecture_override.lo_end_date, "%d-%m-%Y") as lo_end_date,lecture_override.lo_start_time,lecture_override.lo_end_time,lecture_override.lo_duration,lecture_override.lo_attempts,lecture_override.lo_period,lecture_override.lo_period_type,lecture_override.lo_override_batches';
        $lecture_id             = isset($param['lo_lecture_id'])?$param['lo_lecture_id']:false;
        $id                     = isset($param['id'])?$param['id']:false;
        $this->db->select($select);
        if($lecture_id){
            $this->db->where('lecture_override.lo_lecture_id',$lecture_id);
            $this->db->order_by("lecture_override.id", "desc");
        }
        if($id){
            $this->db->where('lecture_override.id',$id);
        }
        $result                 = $this->db->get('lecture_override')->result_array();
      //echo $this->db->last_query();die;
        return $result;
    }

    function override_groups($id)
    {
        $select = "GROUP_CONCAT(gp_institute_code,'-',gp_year,'-',gp_name ORDER BY gp_name SEPARATOR ', ') as groups"; 
        $this->db->select($select);
        $this->db->where("id IN (".$id.")",NULL, false);
        $result                 = $this->db->get('groups')->row_array();
        return $result;
    }

    function save_publish($param = array())
    {
        $id             = isset($param['lecture_id'])?$param['lecture_id']:0;
        unset($param['lecture_id']);
        $this->db->where('assessments.a_lecture_id',$id);
        $this->db->update('assessments',$param);
        return $id;
    }

    function delete_publish_rules($param = array())
    {
        $id  = isset($param['id'])?$param['id']:0;
        $this->db->where('assessment_rules.id',$id);
        $this->db->delete('assessment_rules');
        //echo $this->db->last_query();die;
        return true;
    }

    function save_publish_rules($param = array())
    {
        if($param['id']!=0)
        {
            $this->db->where('assessment_rules.id',$param['id']);
            $this->db->update('assessment_rules',$param);
            return $param['id'];
        }
        else
        {
            $this->db->insert('assessment_rules',$param);
            return $this->db->insert_id();
        }
        
    }

    function availability_rules_details($param = array())
    {
        $select                 = isset($param['select'])?$param['select']:'assessment_rules.id,assessment_rules.selected_lecture,assessment_rules.activity_option,assessment_rules.percentage';
        $lecture_id             = isset($param['lecture_id'])?$param['lecture_id']:false;
        $this->db->select($select);
        $this->db->where('assessment_rules.lecture_id',$lecture_id);
        $result                 = $this->db->get('assessment_rules')->result_array();
       //echo $this->db->last_query();die;
        return $result;
    }

    function delete_aquestion_bulk($question_ids)
    {
        $questions_chunks  = array_chunk($question_ids, 50);
        if(!empty($questions_chunks))
        {
            foreach($questions_chunks as $questions)
            {
                $this->db->trans_start();
                foreach($questions as $q_id)
                {
                    $this->db->where('assessment_questions.id',$q_id);
                    $this->db->delete('assessment_questions');
                }
                $this->db->trans_complete(); 
            }
        }
        return true;
    }

    function check_assessments_expiry($data=array())
    {
        $expiry_date = isset($data['expiry_date'])?$data['expiry_date']:array();
        $return      = array();
        if(!empty($expiry_date))
        {
            $this->db->select("assessments.a_course_id,assessments.a_lecture_id,assessments.a_to as last_date,assessments.a_to_time as end_time,course_basics.id as course_id,course_basics.cb_groups,course_basics.cb_title as course_name,course_lectures.cl_lecture_name as lecture_name"); 
            $this->db->join('course_basics ', 'course_basics.id = assessments.a_course_id', 'left');
            $this->db->join('course_lectures ', 'course_lectures.id = assessments.a_lecture_id', 'left');
            $this->db->where_in('assessments.a_to', $expiry_date);
            $return = $this->db->get('assessments')->result_array();
            //echo $this->db->last_query();die;
        }
        return $return;
    }

    function check_override_expiry($data=array())
    {
        $course_id      = isset($data['course_id'])?$data['course_id']:array();
        $lecture_ids    = isset($data['lecture_ids'])?$data['lecture_ids']:array();
        $return         = array();
        if(!empty($lecture_ids))
        {
            $this->db->select("lecture_override.lo_lecture_id as lecture_id,lecture_override.lo_end_date as last_date,lecture_override.lo_end_time as end_time,lecture_override.lo_override_batches as override_batches"); 
            if(!empty($course_id)){
                $this->db->where_in('lecture_override.lo_course_id', $course_id);
            }
            $this->db->where_in('lecture_override.lo_lecture_id', $lecture_ids);
            $return = $this->db->get('lecture_override')->result_array();
            //echo $this->db->last_query();die;
        }
        return $return;
    }

    function check_assignment_expiry($data=array())
    {
        $expiry_date = isset($data['expiry_date'])?$data['expiry_date']:array();
        $return      = array();
        if(!empty($expiry_date))
        {
            $this->db->select("descrptive_tests.dt_course_id,descrptive_tests.dt_lecture_id,descrptive_tests.dt_last_date as last_date,course_basics.id as course_id,course_basics.cb_groups,course_basics.cb_title as course_name,course_lectures.cl_lecture_name as lecture_name"); 
            $this->db->join('course_basics ', 'course_basics.id = descrptive_tests.dt_course_id', 'left');
            $this->db->join('course_lectures ', 'course_lectures.id = descrptive_tests.dt_lecture_id', 'left');
            $this->db->where_in('descrptive_tests.dt_last_date', $expiry_date);
            $return = $this->db->get('descrptive_tests')->result_array();
            //echo $this->db->last_query();die;
        }
        return $return;
    }

    function get_records( $field = false, $table = false, $params = array() )
    {
        $field              = isset($field)?$field:'*';
        $this->db->select($field);
        $records            = $this->db->get($table)->result_array();
        return $records;
    }

    function save_module_previlages( $params = array() )
    {
        $this->db->insert('roles_modules_meta',$params);
        return true;
    }
}
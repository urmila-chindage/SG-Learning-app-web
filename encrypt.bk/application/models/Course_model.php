<?php
class Course_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    function course_image_old()
    {
        return $this->db->query("SELECT id FROM `course_basics_old`")->result_array();
    }

    public function course_new($param){

        $course_id      = isset($param['course_id']) ? $param['course_id'] : false;
        $select         = isset($param['select']) ? $param['select'] : '*';
        $direction      = isset($param['direction']) ? $param['direction'] : 'ASC';
        $not_deleted    = isset($param['not_deleted']) ? $param['not_deleted'] : '0';
        $order_by       = isset($param['order_by']) ? $param['order_by'] : 'cb_title';
        $limit          = isset($param['limit']) ? $param['limit'] : 0;
        $offset         = isset($param['offset']) ? $param['offset'] : 0;
        $course_id_list = isset($param['course_id_list']) ? $param['course_id_list'] : false;
        $course_id_not  = isset($param['not_in']) ? $param['not_in'] : false;
        $status         = isset($param['status']) ? $param['status'] : false;
        

        $this->db->select($select);
        if($course_id){

            $this->db->where('id', $course_id);
        }
        if($not_deleted)
        {
            $this->db->where('cb_deleted', '0');
        }
        
        if($course_id_list){

            $this->db->where_in('id', $course_id_list);
        }
        if($course_id_not){

            $this->db->where_not_in('id', $course_id_not);
        }
        if ($status) {
            $this->db->where('cb_status', $status);
        }
        
        if ($limit > 0) {
            $this->db->limit($limit, $offset);
        }

        $this->db->order_by($order_by, $direction);

        $this->db->where("cb_account_id", config_item('id'));

        $result = $this->db->get('course_basics');
        if($limit==1){
            return $result->row_array();
        }else{
            return $result->result_array();
        }
        //echo $this->db->last_query();die();
        return $result;
        
    }

    public function courses($param = array())
    {
        $limit          = isset($param['limit']) ? $param['limit'] : 0;
        $offset         = isset($param['offset']) ? $param['offset'] : 0;
        $order_by       = isset($param['order_by']) ? $param['order_by'] : 'id';
        $direction      = isset($param['direction']) ? $param['direction'] : 'DESC';
        $status         = isset($param['status']) ? $param['status'] : '';
        $count          = isset($param['count']) ? $param['count'] : false;
        $not_deleted    = isset($param['not_deleted']) ? $param['not_deleted'] : false;
        $category_id    = isset($param['category_id']) ? $param['category_id'] : '';
        $category_id    = ($category_id == 'uncategorised') ? '0' : $category_id;
        $keyword        = isset($param['keyword']) ? $param['keyword'] : '';
        $filter         = isset($param['filter']) ? $param['filter'] : 0; 
        $institute_id   = isset($param['institute_id']) ? $param['institute_id'] : false; 
        $course_id_list = isset($param['course_id_list']) ? $param['course_id_list'] : false;
        $not_subscribed = isset($param['not_subscribed']) ? $param['not_subscribed'] : false;
        $user_id        = isset($param['user_id']) ? $param['user_id'] : false;
         
        $requested_select = 'course_basics.*, ';
        if (isset($param['select'])) {
            $requested_select = $param['select'] . ', ';
        }

        $teacher_id = isset($param['teacher_id']) ? $param['teacher_id'] : false;
        $tutor_courses = 0;
        if ($teacher_id) {
            $this->db->select('GROUP_CONCAT(ct_course_id) as course_ids');
            $this->db->where('ct_tutor_id', $teacher_id);
            $tutor_courses = $this->db->get('course_tutors')->row_array();
            $tutor_courses = isset($tutor_courses['course_ids']) ? $tutor_courses['course_ids'] : 0;
        }
        $editor_id = isset($param['editor_id']) ? $param['editor_id'] : false;
        $editor_courses = 0;
        if ($editor_id) {
            $this->db->select('GROUP_CONCAT(ct_course_id) as course_ids');
            $this->db->where('ct_tutor_id', $editor_id);
            $editor_courses = $this->db->get('course_tutors')->row_array();
            $editor_courses = isset($editor_courses['course_ids']) ? $editor_courses['course_ids'] : 0;
        }

        if ($institute_id) {
            $this->db->where('users.us_institute_id', $institute_id);
        }

        $this->db->select($requested_select . ' users.us_name, web_actions.wa_name, web_actions.wa_code');
        $this->db->join('users', 'course_basics.action_by = users.id', 'left');
        $this->db->join('web_actions', 'course_basics.action_id = web_actions.id', 'left');
        
        $this->db->order_by($order_by, $direction);
        if ($limit > 0) {
            $this->db->limit($limit, $offset);
        }

        if ($category_id != 'all' && $category_id != '') {
            $this->db->where('cb_category', $category_id);
        }

        if ($keyword) {
            $this->db->like('cb_title', $keyword);
        }

        if ($not_deleted) {
            $this->db->where('cb_deleted', '0');
        }

        if ($filter) {
            switch ($filter) {
                case 'active':
                    $status = '1';
                    $this->db->where('cb_deleted', '0');
                    break;
                case 'inactive':
                    $this->db->where('cb_deleted', '0');
                    $status = '0';
                    break;
                case 'pending_approval':
                    $this->db->where('cb_deleted', '0');
                    $status = '2';
                    break;
                case 'deleted':
                    $this->db->where('cb_deleted', '1');
                    break;

                default:
                    break;
            }
        }

        if ($status != '') {
            $this->db->where('cb_status', $status);
        }

        if ($teacher_id) {
            $this->db->where_in('course_basics.id', explode(',', $tutor_courses));
        }
        if ($editor_id) {
            $this->db->where_in('course_basics.id', explode(',', $editor_courses));
        }

        if($course_id_list){

            $this->db->where_in('course_basics.id', $course_id_list);
        }

        //user not subscribed courses only filter
        if($not_subscribed && $user_id)
        {
            $this->db->where('course_basics.id not in (select course_subscription.cs_course_id from course_subscription where cs_user_id IN('.$user_id.'))');
        }

        $this->db->where('cb_account_id', config_item('id'));
        if ($count) {
            $result = $this->db->count_all_results('course_basics');
        } else {
            $result = $this->db->get('course_basics')->result_array();
        }

        return $result;
    }

    public function courses_new($param = array()) 
    {
        //echo '<pre>'; print_r($param); //exit;
        $limit          = isset($param['limit']) ? $param['limit'] : 0;
        $offset         = isset($param['offset']) ? $param['offset'] : 0;
        $order_by       = isset($param['order_by']) ? $param['order_by'] : 'id';
        $direction      = isset($param['direction']) ? $param['direction'] : 'DESC';
        $status         = isset($param['status']) ? $param['status'] : '';
        $count          = isset($param['count']) ? $param['count'] : false;
        $not_deleted    = isset($param['not_deleted']) ? $param['not_deleted'] : false;
        $category_id    = isset($param['category_id']) ? $param['category_id'] : '';
        $category_id    = ($category_id == 'uncategorised') ? '0' : $category_id;
        $keyword        = isset($param['keyword']) ? $param['keyword'] : '';
        $filter         = isset($param['filter']) ? $param['filter'] : false;
        $check_time     = isset($param['check_deleted_time']) ? $param['check_deleted_time'] : false;
        $course_id_list = isset($param['course_id_list']) ? $param['course_id_list'] : false;
        $course_id_exclude = isset($param['course_id_exclude']) ? $param['course_id_exclude'] : false;
        $select         = isset($param['select'])? $param['select'] : 'course_basics.*';
        
        $tutor_id       = isset($param['tutor_id']) ? $param['tutor_id'] : false;
        $tutor_courses  = 0;
        if ($tutor_id) {
            $this->db->select('GROUP_CONCAT(ct_course_id) as course_ids');
            $this->db->from('course_tutors');
            $this->db->where('ct_tutor_id', $tutor_id);
            $tutor_courses = $this->db->get()->row_array();
            $tutor_courses = isset($tutor_courses['course_ids']) ? $tutor_courses['course_ids'] : 0;
        }

        $this->db->select($select);
        if($course_id_exclude){
            $this->db->where_not_in('course_basics.id', $course_id_exclude);
        }
        $this->db->order_by($order_by, $direction);
        if ($limit > 0) {
            $this->db->limit($limit, $offset);
        }
         
        if ($category_id != 'all' && $category_id != ''){ 
            //echo '<pre>'; print_r($category_id);exit;
            $this->db->where('FIND_IN_SET("'.$category_id.'", course_basics.cb_category)');
         } 
 
        if ($tutor_id) {
            $this->db->where_in('course_basics.id', explode(',', $tutor_courses));
        }

        if ($not_deleted) {
            $this->db->where('cb_deleted', '0');
        }

        if($check_time) 
        {
            $this->db->where('updated_date<= SUBDATE( CURRENT_DATE, INTERVAL 24 HOUR)');
        }

        if ($filter) {
            switch ($filter) {
                case 'active':
                    $status = '1';
                    $this->db->where('cb_deleted', '0');
                    break;
                case 'inactive':
                    $this->db->where('cb_deleted', '0');
                    $status = '0';
                    break;
                case 'pending_approval':
                    $this->db->where('cb_deleted', '0');
                    $status = '2';
                    break;
                case 'deleted':
                    $this->db->where('cb_deleted', '1');
                    break;

                default:
                    break;
            }
        }

        if ($status != '') {
            $this->db->where('cb_status', $status);
        }

        if($course_id_list){
            $this->db->where_in('course_basics.id', $course_id_list);
        }
         
        if ($keyword) {
            $where  = "(`course_basics`.`cb_title` LIKE '%".$keyword."%' OR ";
            $where .= "`course_basics`.`cb_code` LIKE '%".$keyword."%')";
            $this->db->where($where);
        }

        $this->db->where('course_basics.cb_account_id', config_item('id'));
      
        if ($count) {
            $result = $this->db->count_all_results('course_basics');
        } else {
            $result = $this->db->get('course_basics')->result_array();
            //print_r($result); exit;
        }
        //echo $this->db->last_query();
        
        return $result;
    }

    public function lectures($param = array())
    {
        $order_by                       = isset($param['order_by']) ? $param['order_by'] : 'id';
        $direction                      = isset($param['direction']) ? $param['direction'] : 'ASC';
        $status                         = isset($param['status']) ? $param['status'] : '';
        $count                          = isset($param['count']) ? $param['count'] : false;
        $course_id                      = isset($param['course_id']) ? $param['course_id'] : false;
        $section_id                     = isset($param['section_id']) ? $param['section_id'] : false;
        $not_deleted                    = isset($param['not_deleted']) ? $param['not_deleted'] : false;
        $lecture_type                   = isset($param['lecture_type']) ? $param['lecture_type'] : false;
        $lecture_types                  = isset($param['lecture_types']) ? $param['lecture_types'] : array();
        $avoid_lecture_types            = isset($param['avoid_lecture_types']) ? $param['avoid_lecture_types'] : array();
        $select_input                   = isset($param['select']) ? $param['select'] : 'course_lectures.*';
        $checked_lecture_viewed         = isset($param['checked_lecture_viewed']) ? $param['checked_lecture_viewed'] : false;
        $user_id                        = isset($param['user_id']) ? $param['user_id'] : 0;
        $skip_copy_progres_lecture      = isset($param['skip_copy_progres_lecture']) ? $param['skip_copy_progres_lecture'] : false;
        $checked_lecture_viewed = false;

        if ($checked_lecture_viewed) {
            $select_input = $select_input . ', lecture_log_cp.id';
        }

        $this->db->select($select_input);

        if ($checked_lecture_viewed) {
            $this->db->join('(SELECT lecture_log_cp.id, lecture_log_cp.ll_lecture_id FROM lecture_log lecture_log_cp WHERE lecture_log_cp.ll_user_id = "' . $user_id . '" AND lecture_log_cp.ll_lecture_id IN (SELECT id FROM course_lectures WHERE cl_course_id = "' . $user_id . '")) lecture_log_cp', 'course_lectures.id = lecture_log_cp.ll_lecture_id', 'left');
        }
        $this->db->order_by($order_by, $direction);
        
        if (!empty($avoid_lecture_types)) {
            $this->db->where_not_in('cl_lecture_type', $avoid_lecture_types);
        }

        if (!empty($lecture_types)) {
            $this->db->where_in('cl_lecture_type', $lecture_types);
        }

        if ($status != '') {
            $this->db->where('cl_status', $status);
        }

        if ($course_id) {
            $this->db->where('cl_course_id', $course_id);
        }

        if ($lecture_type) {
            $this->db->where('cl_lecture_type', $lecture_type);
        }
        if($skip_copy_progres_lecture)
        {
            $this->db->where('cl_conversion_status <>', '6');  
            $this->db->where('cl_conversion_status <>', '7');  
        }
        if ($not_deleted) {
            $this->db->where('cl_deleted', '0');
        }

        if ($section_id) {
            $this->db->where('cl_section_id', $section_id);
        }

        $this->db->where("cl_account_id", config_item('id'));
        
        if ($count) {
            $result = $this->db->count_all_results('course_lectures');
        } else {
            $result = $this->db->get('course_lectures')->result_array();
        }
        
        // echo $this->db->last_query();die;
        if ($checked_lecture_viewed) {
            // echo $this->db->last_query();die;
        }
        return $result;
    }

    public function studios($param = array())
    {
        $this->db->select('*');
        if(isset($param['id'])) 
        {
            $this->db->where('id', $param['id']);
            return $this->db->get('studio')->row_array();
        } 
        else 
        {
            return $this->db->get('studio')->result_array();
        }        
    }

    /* Created by Yadu Chandran
     * Function for getting course id for a live.
     */
    public function get_course_live($param = array())
    {
        $live_id    = isset($param['id']) ? $param['id'] : false;
        $lecture_id = isset($param['lecture_id']) ? $param['lecture_id'] : false;
        $course_id  = isset($param['course_id']) ? $param['course_id'] : false;
        $limit      = isset($param['limit']) ? $param['limit'] : false;

        $this->db->select('live_lectures.*');
        $this->db->from('live_lectures');
        if($live_id){
            $this->db->where('id', $live_id);
        }
        if($lecture_id){
            $this->db->where('ll_lecture_id', $lecture_id);
        }
        if($course_id){
            $this->db->where('ll_course_id', $course_id);
        }
        
        return $this->db->get()->row_array();
    }

    /* Created by Yadu Chandran
     * Function for updating certificate downloaded status
     */
    public function status_certificate_download($param = array())
    {
        $course_id = isset($param['course_id']) ? $param['course_id'] : false;
        $user_id = isset($param['user_id']) ? $param['user_id'] : false;
        $this->db->set('cs_download_certificate', '1');
        $this->db->where('cs_course_id', $course_id);
        $this->db->where('cs_user_id', $user_id);
        $this->db->update('course_subscription');
        return $user_id;
    }

    public function get_limit_lectures($param = array())
    {
        $order_by = isset($param['order_by']) ? $param['order_by'] : 'id';
        $direction = isset($param['direction']) ? $param['direction'] : 'ASC';
        $status = isset($param['status']) ? $param['status'] : '';
        $course_id = isset($param['course_id']) ? $param['course_id'] : false;
        $not_deleted = isset($param['not_deleted']) ? $param['not_deleted'] : false;
        $lecture_type = isset($param['lecture_type']) ? $param['lecture_type'] : false;
        $limit = isset($param['limit']) ? $param['limit'] : false;

        $this->db->select('course_lectures.*,section.s_name');
        $this->db->join('section', 'section.id = course_lectures.cl_section_id');
        $this->db->order_by($order_by, $direction);
        $this->db->where('cl_deleted', '0');
        $this->db->where('course_lectures.cl_account_id', config_item('id'));
        if ($limit) {
            $this->db->limit($limit);
        }
    }

    public function get_lectures($param = array())
    {

        $order_by           = isset($param['order_by']) ? $param['order_by'] : 'id';
        $direction          = isset($param['direction']) ? $param['direction'] : 'DESC';
        $status             = isset($param['status']) ? $param['status'] : '';
        $count              = isset($param['count']) ? $param['count'] : false;
        $course_id          = isset($param['course_id']) ? $param['course_id'] : false;
        $course_ids         = isset($param['course_ids']) ? $param['course_ids'] : false;
        $section_id         = isset($param['section_id']) ? $param['section_id'] : false;
        $not_deleted        = isset($param['not_deleted']) ? $param['not_deleted'] : false;
        $lecture_type       = isset($param['lecture_type']) ? $param['lecture_type'] : false;
        $not_lecture_type   = isset($param['not_lecture_type']) ? $param['not_lecture_type'] : false; 
        $limit              = isset($param['limit']) ? $param['limit'] : false;
        $select_input       = isset($param['select']) ? $param['select'] : 'course_lectures.*';

        $this->db->select($select_input . ', CONCAT(FLOOR(course_lectures.cl_duration/60),":",MOD(course_lectures.cl_duration,60)) as duration_hm, COUNT(assessment_questions.id) as num_of_question');
        $this->db->join('assessments', 'assessments.a_course_id = course_lectures.cl_course_id AND assessments.a_lecture_id = course_lectures.id', 'left');
        $this->db->join('live_lectures', 'live_lectures.ll_lecture_id = course_lectures.id', 'left');
        $this->db->join('assessment_questions', 'assessments.id = assessment_questions.aq_assesment_id', 'left');
        $this->db->group_by('course_lectures.id');
        $this->db->order_by($order_by, $direction);
        $this->db->where('cl_deleted', '0');
        // $this->db->where_not_in('course_lectures.cl_lecture_type', array('14'));//Prevent cirtificate and survey
        
        if ($status != '') {
            $this->db->where('cl_status', $status);
        }

        if ($course_id) {
            $this->db->where('cl_course_id', $course_id);
        }

        if ($course_ids) {
            $this->db->where_in('cl_course_id', $course_ids);
        }

        if ($lecture_type) {
            $this->db->where('cl_lecture_type', $lecture_type);
        }

        if($not_lecture_type)
        {
            $this->db->where_not_in('cl_lecture_type', $not_lecture_type);
        }

        if ($not_deleted) {
            $this->db->where('cl_deleted', '0');
        }

        if ($section_id) {
            $this->db->where('cl_section_id', $section_id);
        }

        if ($limit) {
            $this->db->limit($limit);
        }
        $this->db->where('course_lectures.cl_account_id', config_item('id'));
        if ($count) {
            $result = $this->db->count_all_results('course_lectures');
        } else {
            $result = $this->db->get('course_lectures')->result_array();
        }
        //echo $this->db->last_query();die;
        return $result;
    }

    public function lecture($param = array())
    {
        $order_by = isset($param['order_by']) ? $param['order_by'] : 'id';
        $direction = isset($param['direction']) ? $param['direction'] : 'DESC';

        if (isset($param['select'])) {
            $this->db->select($param['select']);
        }
        if (isset($param['cl_lecture_type'])) {
            $this->db->where('cl_lecture_type', $param['cl_lecture_type']);
        }
        if (isset($param['status'])) {
            $this->db->where('cl_status', $param['status']);
        }
        if (isset($param['not_deleted'])) {
            $this->db->where('cl_deleted', '0');
        }
        if (isset($param['name'])) {
            if (isset($param['id'])) {
                $this->db->where('id!=', $param['id']);
            }
            $this->db->like('cl_title', $param['name']);
        }
        if (isset($param['id'])) {
            $this->db->where('id', $param['id']);
        }
        if (isset($param['filename'])) {
            $this->db->where('cl_filename', $param['filename']);
        }
        if (isset($param['section_id'])) {
            $this->db->where('cl_section_id', $param['section_id']);
        }

        if (isset($param['course_id'])) {
            $this->db->where('cl_course_id', $param['course_id']);
        }
        if(isset($param['limit'])){
            $this->db->limit($param['limit']);
        }
        if(isset($param['usertype'])){
            $this->db->where('cl_lecture_type', $param['usertype']);
        }
        $this->db->order_by($order_by, $direction);

        $this->db->where('course_lectures.cl_account_id', config_item('id'));

        if (isset($param['count'])) {
            $result = $this->db->count_all_results('course_lectures');
        } else {
            $result = $this->db->get('course_lectures')->row_array();
        }
    // echo $this->db->last_query();exit;
        return $result;
    }

    

    public function assesment($param = array())
    {
        $lecture_id     = isset($param['lecture_id']) ? $param['lecture_id'] : false;
        $course_id      = isset($param['course_id']) ? $param['course_id'] : false;
        $assessment_id  = isset($param['assessment_id']) ? $param['assessment_id'] : false;
        $limit          = isset($param['limit']) ? $param['limit'] : false;
        $select         = isset($param['select']) ? $param['select'] : 'id as assesment_id, a_course_id, a_lecture_id, a_instructions, a_duration, a_show_categories, a_pass_percentage,a_show_smessage,a_smessage';

        $this->db->select($select);
       
        if ($course_id) {
            $this->db->where('a_course_id', $course_id);
        }
        if ($lecture_id) {
            $this->db->where('a_lecture_id', $lecture_id);
        }
        if ($assessment_id) {
            $this->db->where('id', $assessment_id);
        }
        if($limit){
            $this->db->limit($limit);
        }

        $return = $this->db->get('assessments')->row_array();

        //echo $this->db->last_query();die;
        return $return;
    }

    public function survey($param = array())
    {
        $lecture_id = isset($param['lecture_id']) ? $param['lecture_id'] : 0;
        $course_id  = isset($param['course_id']) ? $param['course_id'] : 0;
        $survey_id  = isset($param['survey_id']) ? $param['survey_id'] : false;
        $limit      = isset($param['limit']) ? $param['limit'] : false;

        $this->db->select('id as survey_id, s_name, s_description, s_course_id, s_lecture_id, s_tutor_id, s_response_received');
        if ($course_id) {
            $this->db->where('s_course_id', $course_id);
        }
        if ($lecture_id) {
            $this->db->where('s_lecture_id', $lecture_id);
        }
        if ($survey_id) {
            $this->db->where('id', $survey_id);
        }
        if($limit){
            $this->db->limit($limit);
        }

        return $this->db->get('survey')->row_array();
    }

    public function survey_questions($param = array())
    {
        $survey_id = isset($param['survey_id']) ? $param['survey_id'] : 0;
        if ($survey_id != 0) {
            $this->db->where('sq_survey_id', $survey_id);
        }
        $this->db->select('id, sq_survey_id, sq_lecture_id, sq_required, sq_question, sq_type, sq_options, sq_low_limit, sq_high_limit, sq_low_limit_label, sq_high_limit_label');
        $this->db->order_by('sq_order', 'ASC');
        return $this->db->get('survey_questions')->result_array();
    }

    public function survey_question($param = array())
    {
        $id         = isset($param['id']) ? $param['id'] : 0;
        $survey_id  = isset($param['survey_id']) ? $param['survey_id'] : 0;
        $select     = isset($param['select'])?$param['select']:'id, sq_survey_id, sq_lecture_id, sq_course_id, sq_question, sq_type, sq_options, sq_low_limit, sq_high_limit, sq_low_limit_label, sq_high_limit_label, sq_required';

        if ($survey_id != 0) {
            $this->db->where('sq_survey_id', $survey_id);
        }
        if ($id != 0) {
            $this->db->where('id', $id);
        }
        $this->db->select($select);
        return $this->db->get('survey_questions')->row_array();
    }

    public function get_desctriptive_question($lecture_id)
    {
        $this->db->select('*');
        $this->db->from('descrptive_tests');
        $this->db->where('dt_lecture_id', $lecture_id);
        return $this->db->get()->row_array();

    }

    
    public function questions($param = array())
    {
        $assesment_id           = isset($param['assesment_id']) ? $param['assesment_id'] : 0;
        $not_deleted            = isset($param['not_deleted']) ? $param['not_deleted'] : 0;
        $order_by_random        = isset($param['order_by_random']) ? $param['order_by_random'] : false;
        $not_join               = isset($param['not_join']) ? true : false;
        
        $select                 = 'questions.*, assessment_questions.aq_positive_mark, assessment_questions.aq_negative_mark,questions_topic.qt_topic_name,questions_subject.qs_subject_name';
        if($not_join)
        {
          $select               = 'questions.*';  
        }
        $this->db->select($select);
        $this->db->join('questions', 'assessment_questions.aq_question_id = questions.id', 'left');
        $this->db->join('questions_topic', 'questions_topic.id = questions.q_topic', 'left');
        $this->db->join('questions_subject', 'questions_subject.id = questions.q_subject', 'left');

        if ($assesment_id) {
            $this->db->where('assessment_questions.aq_assesment_id', $assesment_id);
        }
        if ($not_deleted) {
            $this->db->where('questions.q_deleted', '0');
        }

        if ($order_by_random) {
            $this->db->order_by($order_by_random);
        }
        $this->db->where('q_account_id', config_item('id'));
        $result = $this->db->get('assessment_questions')->result_array();
       
        return $result;
    }

    /*
     * To get the questions of user generated test using given assessment id
     * Created by Neethu KP
     * Created at 13/01/2017
     */

    public function uga_questions($param = array())
    {
        $assesment_id = isset($param['uga_id']) ? $param['uga_id'] : 0;

        $this->db->select('questions.*');
        $this->db->join('questions', 'user_generated_assesment_question.uga_question_id = questions.id', 'left');

        if ($assesment_id) {
            $this->db->where('user_generated_assesment_question.uga_assesment_id', $assesment_id);
        }
        $result = $this->db->get('user_generated_assesment_question')->result_array();
        //echo $this->db->last_query();die;
        return $result;
    }

    /*
     * To get the questions of challenge zone test using given assessment id
     * Created by Neethu KP
     * Created at 13/01/2017
     */

    public function cz_questions($param = array())
    {
        $assesment_id = isset($param['cz_assessment_id']) ? $param['cz_assessment_id'] : 0;

        $this->db->select('questions.*');
        $this->db->join('questions', 'challenge_zone_questions.czq_question_id = questions.id', 'left');

        if ($assesment_id) {
            $this->db->where('challenge_zone_questions.czq_challenge_zone_id', $assesment_id);
        }
        $result = $this->db->get('challenge_zone_questions')->result_array();
        return $result;
    }

    public function answers($param = array())
    {
        $attempt_id = isset($param['attempt_id']) ? $param['attempt_id'] : 0;

        $this->db->select('*');
        $this->db->from('assessment_report');
        if ($attempt_id) {
            $this->db->where('assessment_report.ar_attempt_id', $attempt_id);
        }
        $result = $this->db->get()->result_array();
        //echo $this->db->last_query();die;
        return $result;
    }

    /*
     * To get the answers of the user generated test
     * Created by Neethu KP
     * Created at 13/01/2017
     */
    public function uga_answers($param = array())
    {
        $attempt_id = isset($param['uga_attempt_id']) ? $param['uga_attempt_id'] : 0;

        $this->db->select('*');
        $this->db->from('user_generated_assessment_report');
        if ($attempt_id) {
            $this->db->where('user_generated_assessment_report.ugar_attempted_id', $attempt_id);
        }
        $result = $this->db->get()->result_array();
        //echo $this->db->last_query();die;
        return $result;
    }

    /*
     * To get the answers of the user generated test
     * Created by Neethu KP
     * Created at 13/01/2017
     */
    public function cz_answers($param = array())
    {
        $attempt_id = isset($param['cz__attempt_id']) ? $param['cz__attempt_id'] : 0;

        $this->db->select('*');
        $this->db->from('challenge_zone_report');
        if ($attempt_id) {
            $this->db->where('challenge_zone_report.czr_attempt_id', $attempt_id);
        }
        $result = $this->db->get()->result_array();
        //echo $this->db->last_query();die;
        return $result;
    }

    public function get_user_preview_time($param = array())
    {
        $course_id = isset($param['course_id']) ? $param['course_id'] : 0;
        $user_id = isset($param['user_id']) ? $param['user_id'] : 0;

        $this->db->select('*');
        $this->db->from('course_preview_time');
        $this->db->where('cpt_user_id', $user_id);
        $this->db->where('cpt_course_id', $course_id);

        return $this->db->get()->row_array();
    }

    /* Function for taking online test duration */
    public function test_duration($param = array())
    {
        $course_id = isset($param['course_id']) ? $param['course_id'] : 0;
        $lecture_id = isset($param['lecture_id']) ? $param['lecture_id'] : 0;

        $this->db->select('a_duration');
        $this->db->from('assessments');
        $this->db->where('a_course_id', $course_id);
        $this->db->where('a_lecture_id', $lecture_id);

        $result = $this->db->get()->row_array();
        return $result['a_duration'];
    }
    /* Function for taking live lecture duration */
    public function test_duration_live($param = array())
    {
        $course_id = isset($param['course_id']) ? $param['course_id'] : 0;
        $lecture_id = isset($param['lecture_id']) ? $param['lecture_id'] : 0;

        $this->db->select('ll_duration');
        $this->db->from('live_lectures');
        $this->db->where('ll_course_id', $course_id);
        $this->db->where('ll_lecture_id', $lecture_id);

        $result = $this->db->get()->row_array();
        return $result['ll_duration'];
    }
    public function save_remain_preview($data)
    {
        if(isset($data['id'])) 
        {
            $this->db->where('id', $data['id']);
            $this->db->update('course_preview_time', $data);
            return $data['id'];
        } 
        else
        {
            $this->db->insert('course_preview_time', $data);
            return $this->db->insert_id();
        }
    }
    public function save_live_lectures($data)
    {
        $this->db->where('id', $data['id']);
        $this->db->update('live_lectures', $data);
        return $data['id'];
    }

    public function check_rating($param = array())
    {
        $course_id = isset($param['course_id']) ? $param['course_id'] : 0;
        $user_id = isset($param['user_id']) ? $param['user_id'] : 0;

        $this->db->select('*');
        $this->db->from('course_ratings');
        $this->db->where('cc_account_id', config_item('id'));
        $this->db->where('cc_user_id', $user_id);
        $this->db->where('cc_course_id', $course_id);

        return $this->db->get()->row_array();
    }

    public function save_rating($data)
    {
        $data['cc_account_id']   =  config_item('id');
        $this->db->insert('course_ratings', $data);
        return true;
    }

    public function get_course_rating($param)
    {
        if(isset($param['course_id']))
        {
            $this->db->select('sum(cc_rating) as rating_sum, count(cc_rating) as rating_count');
            $this->db->from('course_ratings');
            $this->db->where([ 'cc_course_id' => $param['course_id'], 'cc_status' => '1' ]);
            $query = $this->db->get();
            return $query->row_array(); 
        }
        else
        {
            $response = array();
            return $response;
        }
    }

    public function update_item_sort_order_rating($param)
    {
        if(isset($param['course_id']) && isset($param['item_type']) && isset($param['rating']))
        {
            $data = array();
            $data['iso_item_rating'] = $param['rating'];
            $this->db->set($data);
            $this->db->where(['iso_item_id' => $param['course_id'], 'iso_item_type' => $param['item_type']]);
            return $this->db->update('item_sort_order', $data);
            
        }
        else
        {
            return false;
        }
    }

    public function save_review($data)
    {
        $data['cc_account_id']   =  config_item('id');
        if (isset($data['id']) && $data['id']) {
            $this->db->where('id', $data['id']);
            $this->db->where('cc_account_id', config_item('id'));
            $this->db->update('course_ratings', $data);
            return $data['id'];
        } else {
            $this->db->insert('course_ratings', $data);
            return $this->db->insert_id();
        }
    }

    public function get_assessment_questions($param = array())
    {
        $select         = isset($param['select']) ? $param['select'] : '*';
        $assesment_id   = isset($param['assesment_id']) ? $param['assesment_id'] : '';
        $count          = isset($param['count']) ? $param['count'] : false;

        $this->db->select($select);

        if ($assesment_id) {
            $this->db->where('aq_assesment_id', $assesment_id);
        }

        if ($count) {
            $result = $this->db->count_all_results('assessment_questions');
        } else {
            $result = $this->db->get('assessment_questions')->result_array();
        }
        return $result;
    }

    public function save_assesment_question($param = array())
    {
// echo "<pre>";print_r($param);die;
        $assesment_id = $param['assesment_id'];
        $question_id = $param['question_id'];
        if ($this->db->where(array('aq_assesment_id' => $assesment_id, 'aq_question_id' => $question_id))->count_all_results('assessment_questions') == 0) {
            $save = array();
            $save['aq_assesment_id']  = $assesment_id;
            $save['aq_question_id']   = $question_id;
            $save['aq_positive_mark'] = $param['positive_mark'];
            $save['aq_negative_mark'] = $param['negative_mark'];
            $save['aq_status'] = '1';
            $this->db->insert('assessment_questions', $save);
            $key = 'assesment_' . $assesment_id;
            $objects = array();
            $objects['key'] = $key;
            $assessment_cache = $this->memcache->get($objects);
            if (!empty($assessment_cache)) {
                $this->memcache->delete($key);
            }
            return $question_id;
        } 
    }

    public function save_assessment_attempts($data)
    {
       
        if($data['id']) {
            $this->db->where('id', $data['id']);
            $this->db->update('assessment_attempts', $data);
            return $data['id'];
        } else {
            $this->db->insert('assessment_attempts', $data);
            return $this->db->insert_id();
        }

    }

    public function update_assessment_status($data){
        $this->db->where('aa_assessment_id', $data['aa_assessment_id']);
        $this->db->where('aa_user_id', $data['aa_user_id']);
        $this->db->update('assessment_attempts', $data);
        //echo $this->db->last_query();die;
        return $data['aa_assessment_id'];
    }

    public function save_assessment_report($data)
    {
        $ar_attempt_id  = $data['ar_attempt_id'];
        $ar_question_id = $data['ar_question_id'];
        if ($data['id']) {
            $this->db->where('id', $data['id']);
            $this->db->update('assessment_report', $data);
            return $data['id'];
        } else {
            $this->db->insert('assessment_report', $data);
            return $this->db->insert_id();
        }
    }

    public function save_assessment_report_bulk($datas)
    {
        $return  = array();
        $this->db->trans_start();
        foreach($datas as $data)
        {
            $ar_attempt_id  = $data['ar_attempt_id'];
            $ar_question_id = $data['ar_question_id'];
            if ($data['id']) {
                $this->db->where('id', $data['id']);
                $this->db->update('assessment_report', $data);
                $return[] = $data;
            } else {
                $this->db->insert('assessment_report', $data);
                $data['id'] = $this->db->insert_id();
                $return[] = $data;
            }    
        }
        $this->db->trans_complete();
        return $return;
    }

    public function updateAssementValuated($id)
    {
        $this->db->where('id', $id);
        $this->db->update('assessment_attempts', array('aa_valuated' => '0'));
        return $id;
    }

    public function changeValuationflag($param = array())
    {
        $flag = (isset($param['flag']) && $param['flag'] == 'set') ? "1" : "0";
        $this->db->where('id', $param['attempt_id']);
        $this->db->update('assessment_attempts', array('aa_valuated' => $flag));
        return $param['attempt_id'];
    }

    public function updateChallengeValuated($id)
    {
        $this->db->where('id', $id);
        $this->db->update('challenge_zone_attempts', array('cza_valuated' => '0'));
        return $id;
    }

    public function updateUserGeneratedValuated($id)
    {
        $this->db->where('id', $id);
        $this->db->update('user_generated_assessment_attempt', array('uga_evaluated' => '0'));
        return $id;
    }

    public function question($param = array())
    {
        if (isset($param['status'])) {
            $this->db->where('q_status', '1');
        }
        if (isset($param['id'])) {
            $this->db->where('id', $param['id']);
        }
        $result = $this->db->get('questions')->row_array();
        return $result;
    }

    public function save_question($data)
    {
        //echo '<pre>';print_r($data);die();

       if ($data['id']) {
            $this->db->where('id', $data['id']);
            $this->db->update('questions', $data);
            $question_id = $data['id'];
        } else {
            $this->db->insert('questions', $data);
            $question_id = $this->db->insert_id();
            $randam           = array();
            $randam['q_code'] = substr(str_shuffle(str_repeat("123456789", 2)), 0, 2).$question_id;
            $this->db->where('id', $question_id);
            $this->db->update('questions', $randam);
        }
        //echo $this->db->last_query();die;
        return $question_id; 
    }

    public function save_question_survey($data,$conditions = null)
    {
        $id = isset($conditions['update'])?$conditions['update']:false;
        if ($id) {
            $this->db->where('id', $data['id']);
            $this->db->update('survey_questions', $data);
            
            return $data['id'];
        } else {
            $this->db->insert('survey_questions', $data);
            
            return $this->db->insert_id();
        }
        
    }

    public function options($param = array())
    {
        $q_options = isset($param['q_options']) ? $param['q_options'] : false;
        $q_answer = isset($param['q_answer']) ? $param['q_answer'] : false;
        if ($q_options) {
            $this->db->where_in('id', array_map('intval', explode(',', $q_options)));
            $result = $this->db->get('questions_options')->result_array();
            return $result;
        }
        if ($q_answer) {
            $this->db->where_in('id', array_map('intval', explode(',', $q_answer)));
            $result = $this->db->get('questions_options')->result_array();
            return $result;
        }
        //echo $this->db->last_query();die;
    }

    public function save_option($data)
    {
        if ($data['id']) {
            $this->db->where('id', $data['id']);
            $this->db->update('questions_options', $data);
            return $data['id'];
        } else {
            $this->db->insert('questions_options', $data);
            return $this->db->insert_id();
        }
    }

    public function delete_option($option_id)
    {
        $this->db->where('id', $option_id);
        $this->db->delete('questions_options');
    }

    public function delete_question($question_id)
    {
        //get the options for question that we are going to delete
        $query = "SELECT q_options FROM questions WHERE id='" . $question_id . "'";
        $options = $this->db->query($query)->row_array();
        //end

        //delete the option from option table
        $result = $this->db->where_in('id', explode(',', $options['q_options']));
        $this->db->delete('questions_options');
        //end

        //delete the question connection from assesment
        $this->db->where('aq_question_id', $question_id);
        $this->db->delete('assessment_questions');
        //end

        //finally delete the question
        $this->db->where('id', $question_id);
        $this->db->delete('questions');
        //end
        return true;
    }

    public function delete_assesment_question($param = array())
    {
        $this->db->where($param);
        $this->db->delete('assessment_questions');
        return true;
    }

    public function delete_survey_question($param = array())
    {
        $survey_id      = isset($param['survey_id'])?$param['survey_id']:false;
        $lecture_id     = isset($param['lecture_id'])?$param['lecture_id']:false;
        $course_id      = isset($param['course_id'])?$param['course_id']:false;
        $id             = isset($param['id'])? $param['id']: false;
        if($survey_id){
            $this->db->where('sq_survey_id',$survey_id);
        }
        if($lecture_id){
            $this->db->where('sq_lecture_id',$lecture_id);
        }
        if($course_id){
            $this->db->where('sq_course_id',$course_id);
        }
        if($id) {
            $this->db->where('id', $id);
        }
        
        $this->db->delete('survey_questions');
        return true;
    }
    public function delete_survey_user_response($param = array())
    {
        $survey_id      = isset($param['survey_id'])?$param['survey_id']:false;
        $lecture_id     = isset($param['lecture_id'])?$param['lecture_id']:false;
        $course_id      = isset($param['course_id'])?$param['course_id']:false;
        $user_id        = isset($param['user_id'])?$param['user_id']:false;
        $question_id    = isset($param['question_id'])?$param['question_id']:false;

        if($survey_id){
            $this->db->where('sur_survey_id',$survey_id);
        }
        if($lecture_id){
            $this->db->where('sur_lecture_id',$lecture_id);
        }
        if($course_id){
            $this->db->where('sur_course_id',$course_id);
        }
        if($user_id){
            $this->db->where('sur_user_id',$user_id);
        }
        if($question_id){
            $this->db->where('sur_question_id',$question_id);
        }
        
        $this->db->delete('survey_user_response');
        return true;
    }
    public function delete_survey($param = array())
    {
        $section_id     = isset($param['section_id'])?$param['section_id']:false;
        $lecture_id     = isset($param['lecture_id'])?$param['lecture_id']:false;
        $course_id      = isset($param['course_id'])?$param['course_id']:false;
        $id             = isset($param['id'])? $param['id']: false;
        $tutor_id       = isset($param['tutor_id'])? $param['tutor_id']: false;

        if($section_id){
            $this->db->where('s_section_id',$section_id);
        }
        if($lecture_id){
            $this->db->where('s_lecture_id',$lecture_id);
        }
        if($course_id){
            $this->db->where('s_course_id',$course_id);
        }
        if($id) {
            $this->db->where('id', $id);
        }
        if($tutor_id) {
            $this->db->where('s_tutor_id', $tutor_id);
        }
        
        $this->db->delete('survey');
        return true;
    }
    function delete_live_lecture(){

        $lecture_id     = isset($param['lecture_id'])?$param['lecture_id']:false;
        $course_id      = isset($param['course_id'])?$param['course_id']:false;
        $id             = isset($param['id'])? $param['id']: false;
        $studio_id      = isset($param['studio_id'])? $param['studio_id']: false;

        if($studio_id){
            $this->db->where('ll_studio_id',$studio_id);
        }
        if($lecture_id){
            $this->db->where('ll_lecture_id',$lecture_id);
        }
        if($course_id){
            $this->db->where('ll_course_id',$course_id);
        }
        if($id) {
            $this->db->where('id', $id);
        }
                
        $this->db->delete('live_lectures');
        return true;
    }
    public function course($param = array())
    {
        $not_deleted    = isset($param['not_deleted']) ? $param['not_deleted'] : false;
        $route          = isset($param['route']) ? $param['route'] : false;
        $select         = isset($param['select']) ? $param['select'] : 'course_basics.*,"course" as item_type';
        if (isset($param['status'])) {
            $this->db->where('course_basics.cb_status', 1);
        }
        if (isset($param['name'])) {
            if (isset($param['id'])) {
                $this->db->where('course_basics.id!=', $param['id']);
            }
            $this->db->where('course_basics.cb_title', $param['name']);
        }
        if (isset($param['code'])) {
            
            $this->db->where('course_basics.cb_code', $param['code']);
        }

        if (isset($param['exclude_id'])) {
            $this->db->where('course_basics.id!=', $param['exclude_id']);
        }
        
        if (isset($param['id'])) {
            $this->db->where('course_basics.id', $param['id']);
        }
        if($route)
        {
            $this->db->join('routes', 'course_basics.cb_route_id = routes.id', 'left');
        }
        if ($not_deleted) {
            $this->db->where('course_basics.cb_deleted', '0');
        }
        $this->db->where('course_basics.cb_account_id', config_item('id'));
        $this->db->select($select);
        $return = $this->db->get('course_basics')->row_array();
        //echo $this->db->last_query();die;
        return $return;
    }

    public function save($data)
    {
        $data['cb_account_id']  = config_item('id');
        if ($data['id']) {
            $this->db->where('id', $data['id']);
            $this->db->where('cb_account_id', config_item('id'));
            $this->db->update('course_basics', $data);
            return $data['id'];
        } else {
            $this->db->insert('course_basics', $data);
            return $this->db->insert_id();
        }
    }

    public function save_lecture($data)
    {
        course_lecture_activity_save($data);
        $data['cl_account_id']  = config_item('id');
        course_lecture_activity_save($data);
        if ($data['id']) {
            if (array_key_exists("cl_sent_mail_on_lecture_creation",$data))
            {
                unset($data['cl_sent_mail_on_lecture_creation']);
            }
            $this->db->where('id', $data['id']);
            $this->db->where('cl_account_id', config_item('id'));
            $this->db->update('course_lectures', $data);
            return $data['id'];
        } else {
            $this->db->insert('course_lectures', $data);
            return $this->db->insert_id();
        }
        //echo $this->db->last_query();die;
    }

    public function save_lecture_new($data,$filter_param = array()){

        $logparms = array();
        $logparms = $data;
        $logparms['filter_param'] = $filter_param;
        course_lecture_activity_save($logparms);
        $update = isset($filter_param['update'])?$filter_param['update']:false;
        course_lecture_activity_save($data);
        $data['cl_account_id']  = config_item('id');
        if ($update) {

            $id = isset($filter_param['id'])?$filter_param['id']:false;
            $ids = isset($filter_param['ids'])?$filter_param['ids']:false;
            if($id){
                $this->db->where('id', $id);
                $this->db->where('cl_account_id', config_item('id'));
                $this->db->update('course_lectures', $data);
                return $id;
            }
            if($ids){
                $this->db->where_in('id', $ids);
                $this->db->where('cl_account_id', config_item('id'));
                $this->db->update('course_lectures', $data);
                return true;
            }
            return false;
        } else {
            $this->db->insert('course_lectures', $data);
            return $this->db->insert_id();
        }
    }

    public function save_assesment($data)
    {
       
        if ($data['id']) {
            $this->db->where('id', $data['id']);
            $this->db->update('assessments', $data);
            return $data['id'];
        } else {
            $this->db->insert('assessments', $data);
            return $this->db->insert_id();
        }
    }

    public function save_survey($data)
    {
        $data['s_account_id']= config_item('id');
        if ($data['id']) {
            $this->db->where('id', $data['id']);
            $this->db->update('survey', $data);
            return $data['id'];
        } else {
            $this->db->insert('survey', $data);
            return $this->db->insert_id();
        }
    }
    

    public function save_live_lecture($data)
    {
        if ($data['id']) {
            $this->db->where(array('id' => $data['id'], 'll_course_id' => $data['ll_course_id']));
            //$this->db->where('id', $data['id']);
            $this->db->update('live_lectures', $data);
            return $data['id'];
        } else {
            $this->db->insert('live_lectures', $data);
            return $this->db->insert_id();
        }
    }

    //Written by Alex
    public function cz_total_marks($param = array())
    {
        $attempt_id = isset($param['cz_attempt_id']) ? $param['cz_attempt_id'] : 0;

        $this->db->select('SUM(czr_mark) AS total_mark');
        $this->db->from('challenge_zone_report');
        if ($attempt_id) {
            $this->db->where('challenge_zone_report.czr_attempt_id', $attempt_id);
            //$this->db->group_by("challenge_zone_report.czr_attempt_id");
        }
        $result = $this->db->get()->row_array();
        //echo $this->db->last_query();die;
        return $result;
    }
    //End of written by Alex

    public function live_lecture($param = array())
    {
        $count = isset($param['count']) ? $param['count'] : false;
        $status = isset($param['status']) ? $param['status'] : '';
        $not_deleted = isset($param['not_deleted']) ? $param['not_deleted'] : false;
        $upcommimg = isset($param['upcommimg']) ? $param['upcommimg'] : false;
        $live_id = isset($param['live_id']) ? $param['live_id'] : false;
        $return_type = 'result_array';

        //get user course
        $teacher_id = isset($param['teacher_id']) ? $param['teacher_id'] : false;
        $tutor_courses = 0;
        if ($teacher_id) {
            $this->db->select('GROUP_CONCAT(ct_course_id) as course_ids');
            $this->db->where('ct_tutor_id', $teacher_id);
            $tutor_courses = $this->db->get('course_tutors')->row_array();
            $tutor_courses = isset($tutor_courses['course_ids']) ? $tutor_courses['course_ids'] : 0;
        }
        //End

        $this->db->select('live_lectures.id as live_lecture_id, live_lectures.ll_date, live_lectures.ll_course_id, live_lectures.ll_time, live_lectures.ll_duration, live_lectures.ll_is_online, live_lectures.ll_studio_id, live_lectures.ll_files, course_lectures.cl_lecture_name as live_lecture_name, studio.st_url');
        $this->db->join('course_lectures', 'live_lectures.ll_lecture_id = course_lectures.id', 'left');
        $this->db->join('studio', 'live_lectures.ll_studio_id = studio.id');
        if (isset($param['lecture_id'])) {
            $this->db->where('live_lectures.ll_lecture_id', $param['lecture_id']);
            $return_type = 'row_array';
        }
        if ($live_id) {
            $this->db->where('live_lectures.id', $live_id);
            $return_type = 'row_array';
        }

        if ($teacher_id) {
            $this->db->where_in('live_lectures.ll_course_id', explode(',', $tutor_courses));
        }

        if (isset($param['course_id'])) {
            $this->db->where('live_lectures.ll_course_id', $param['course_id']);
        }
        if ($status != '') {
            $this->db->where('course_lectures.cl_status', $status);
        }
        if ($not_deleted) {
            $this->db->where('course_lectures.cl_deleted', '0');
        }
        if ($upcommimg) {
            $this->db->where('live_lectures.ll_date>', date('Y-m-d'));
        }
        if ($count) {
            $result = $this->db->count_all_results('live_lectures');
        } else {
            $result = $this->db->get('live_lectures')->$return_type();
        }
        // echo $this->db->last_query();exit;
        return $result;
        
    }

    public function get_live_recordings($param = array())
    {
        $live_id = isset($param['live_id']) ? $param['live_id'] : false;
        $count = isset($param['count']) ? $param['count'] : false;
        $status = isset($param['status']) ? $param['status'] : '';

        $this->db->select('live_lecture_recordings.*');
        //$this->db->join('live_lecture_users', 'live_lecture_users.llu_live_id ='.$live_id, 'left');
        if ($live_id) {
            $this->db->where('llr_live_id', $live_id);
        }
        if ($status != '') {
            $this->db->where('llr_status', $status);
        }
        if ($count) {
            $return = $this->db->count_all_results('live_lecture_recordings');
        } else {
            $return = $this->db->get('live_lecture_recordings')->result_array();
        }
        return $return;
    }

    public function get_live_recording($param = array())
    {
        $live_id = isset($param['live_id']) ? $param['live_id'] : false;
        $record_id = isset($param['id']) ? $param['id'] : false;
        $lecture_id = isset($param['lecture_id']) ? $param['lecture_id'] : false;

        $this->db->select('live_lecture_recordings.*');
        //$this->db->join('live_lecture_users', 'live_lecture_users.llu_live_id ='.$live_id, 'left');

        if ($live_id) {
            $this->db->where('llr_live_id', $live_id);
        }
        if ($record_id) {
            $this->db->where('id', $record_id);
        }
        if ($lecture_id) {
            $this->db->where('llr_lecture_id', $lecture_id);
        }
        $return = $this->db->get('live_lecture_recordings')->row_array();
        return $return;
    }

    public function update_live_recording($data)
    {
        $this->db->where('llr_live_id', $data['llr_live_id']);
        $this->db->update('live_lecture_recordings', $data);
        return true;
    }

    public function get_live_users_attended($param = array())
    {
        $live_id = isset($param['live_id']) ? $param['live_id'] : false;

        $this->db->select('live_lecture_users.*');

        $this->db->where('llu_live_id', $live_id);
        $return = $this->db->get('live_lecture_users')->result_array();

        return $return;
    }

    public function recorded_video($param = array())
    {
        if (isset($param['id'])) {
            $this->db->where('id', $param['id']);
        }

        $result = $this->db->get('live_lecture_recordings')->row_array();
        return $result;

    }

    public function save_recently_view($data)
    {
        // if ($this->db->get_where('recently_view_courses', array('rvc_user_id' => $data['rvc_user_id'], 'rvc_course_id' => $data['rvc_course_id']))->result()) {
        //     $this->db->where(array('rvc_user_id' => $data['rvc_user_id'], 'rvc_course_id' => $data['rvc_course_id']));
        //     $this->db->update('recently_view_courses', $data);
        // } else {
        //     $this->db->insert('recently_view_courses', $data);
        // }
        return true;
    }

    public function delete($id, $confirm_delete = false)
    {
        if ($confirm_delete) {
            $this->check_course_delete($id);
            $this->db->where('id', $id);
            $this->db->delete('course_basics');
        } else {
            $save = array();
            $save['id'] = $id;
            $save['cb_deleted'] = 1;
            $this->save($save);
        }
    }

    public function recently_viewed($param = array())
    {
        // $user_id = isset($param['user_id']) ? $param['user_id'] : '';

        // $this->db->select('course_basics.id, course_basics.cb_title');
        // if ($user_id) {
        //     $this->db->where('rvc_user_id', $user_id);
        // }
        // $this->db->join('course_basics', 'recently_view_courses.rvc_course_id = course_basics.id', 'left');
        // $this->db->where('course_basics.cb_title!=', "");
        // $this->db->order_by('rvc_date', 'DESC');
        // $this->db->limit(5, 0);
        // $result = $this->db->get('recently_view_courses')->result_array();
        return $result = array();
    }

    public function lecture_log($param = array())
    {
        $user_id = isset($param['user_id']) ? $param['user_id'] : false;
        $lecture_id = isset($param['lecture_id']) ? $param['lecture_id'] : false;
        $row = isset($param['row']) ? $param['row'] : false;
        if ($user_id) {
            $this->db->where('lecture_log.ll_user_id', $user_id);
        }
        if ($lecture_id) {
            $this->db->where('lecture_log.ll_lecture_id', $lecture_id);
        }
        if ($row) {
            $return = $this->db->get('lecture_log')->row_array();
        } else {
            $return = $this->db->get('lecture_log')->result_array();
        }

        return $return;

    }

    public function lecture_log_attempt($param = array())
    {
        $user_id = isset($param['user_id']) ? $param['user_id'] : false;
        $lecture_id = isset($param['lecture_id']) ? $param['lecture_id'] : false;
        $attempt = isset($param['attempt']) ? $param['attempt'] : false;

        if ($user_id) {
            $this->db->where('lecture_log.ll_user_id', $user_id);
        }

        if ($lecture_id) {
            $this->db->where('lecture_log.ll_lecture_id', $lecture_id);
        }
        if ($attempt) {
            $this->db->where('lecture_log.ll_attempt >', '0');
        }
        $return = $this->db->get('lecture_log')->row_array();
        return $return;
    }

    public function calculate_lecture_log($param = array())
    {
        $user_id = isset($param['user_id']) ? $param['user_id'] : false;
        $course_id = isset($param['course_id']) ? $param['course_id'] : false;
        $attempt = isset($param['attempt']) ? $param['attempt'] : false;
        if ($attempt) {
            $where = 'WHERE  lecture_log_cp.ll_attempt="0"';
        } else {
            $where = '';
        }

        /*$query = 'SELECT ROUND((SUM(lecture_log_cp.ll_percentage)/COUNT(course_lectures_cp.lecture_id))) AS percentage
        FROM (SELECT id as lecture_id FROM course_lectures course_lectures_cp WHERE cl_status="1" AND cl_deleted="0" AND cl_course_id='.$course_id.') course_lectures_cp
        LEFT JOIN (SELECT * FROM lecture_log lecture_log_cp WHERE ll_user_id='.$user_id.') lecture_log_cp
        ON course_lectures_cp.lecture_id = lecture_log_cp.ll_lecture_id'.' '.$where;*/
        $query = "SELECT ROUND(SUM(ll_percentage_new)/COUNT(*)) as percentage
                FROM  course_lectures
                LEFT JOIN course_basics ON course_lectures.cl_course_id = course_basics.id
                LEFT JOIN (SELECT ll_user_id, ll_lecture_id, ll_attempt,
                                    (CASE
                                        WHEN ll_attempt > 1 THEN 100
                                        ELSE ll_percentage
                                    END ) AS ll_percentage_new
                            FROM lecture_log lecture_log_cp
                            WHERE ll_user_id = " . $user_id . " AND ll_lecture_id IN (SELECT id FROM course_lectures WHERE cl_course_id = " . $course_id . " AND cl_deleted = '0' AND cl_status = '1')
                            ORDER BY ll_user_id ASC
                        ) lecture_log_cp ON course_lectures.id = lecture_log_cp.ll_lecture_id
                        WHERE course_lectures.cl_course_id = " . $course_id . " AND cl_deleted = '0' AND cl_status = '1'";

        $result = $this->db->query($query)->row_array();
        return isset($result['percentage']) ? $result['percentage'] : 0;
    }

    public function calculate_lecture_log_full($param = array())
    {
        $user_id = isset($param['user_id']) ? $param['user_id'] : false;
        $course_id = isset($param['course_id']) ? $param['course_id'] : false;

        $query = 'SELECT (COUNT(course_lectures_cp.lecture_id)) AS percentage
                  FROM (SELECT id as lecture_id FROM course_lectures course_lectures_cp WHERE cl_status="1" AND cl_deleted="0" AND cl_course_id=' . $course_id . ') course_lectures_cp
                  LEFT JOIN (SELECT * FROM lecture_log lecture_log_cp WHERE ll_user_id=' . $user_id . ') lecture_log_cp
                  ON course_lectures_cp.lecture_id = lecture_log_cp.ll_lecture_id WHERE lecture_log_cp.ll_attempt>0';
        $result = $this->db->query($query)->row_array();
        //echo $this->db->last_query();die;
        if ($result['percentage'] > 0) {
            return '100';
        } else {
            return '0';
        }
        //return isset($result['percentage'])?$result['percentage']:0;
    }

    public function save_lecture_log($data)
    {
        if (isset($data['old']) && $data['old'] == true) {
            unset($data['old']);
            $this->db->where('ll_user_id', $data['ll_user_id']);
            $this->db->where('ll_lecture_id', $data['ll_lecture_id']);
            $this->db->update('lecture_log', $data);
            //echo $this->db->last_query();die;
            return true;
        } else {
            $this->db->insert('lecture_log', $data);
            return $this->db->insert_id();
        }
    }

    public function delete_live_users($live_id)
    {
        $this->db->where('llu_live_id', $live_id);
        $this->db->delete('live_lecture_users');
    }

    public function get_course_lecture($param=array())
    {
        $course_id  = isset($param['course_id']) ? $param['course_id'] : false;
        $select     = isset($param['select']) ? $param['select'] : false;
        $status     = isset($param['status']) ? $param['status'] : false;
        $deleted    = isset($param['deleted']) ? $param['deleted'] : false;
        
        $this->db->select($select);
        $this->db->from('course_lectures');
        if($status)
        {
            $this->db->where('cl_status',$status);
        }
        if($deleted)
        {
            $this->db->where('cl_deleted',$deleted);
        }
        $this->db->where('cl_course_id',$course_id);
        $this->db->where('cl_account_id', config_item('id'));
        $query = $this->db->get();
        return $query->result_array();


    }
    
    
    public function reset_user_lecture_result($param = array())
    {
        $user_id = isset($param['user_id']) ? $param['user_id'] : false;
        $course_id = isset($param['course_id']) ? $param['course_id'] : false;

        $query = 'SELECT  course_lectures_cp.lecture_id, lecture_log_cp.*
                  FROM (SELECT id as lecture_id FROM course_lectures course_lectures_cp WHERE cl_status="1" AND cl_deleted="0" AND cl_course_id=' . $course_id . ') course_lectures_cp
                  LEFT JOIN (SELECT * FROM lecture_log lecture_log_cp WHERE ll_user_id=' . $user_id . ') lecture_log_cp
                  ON course_lectures_cp.lecture_id = lecture_log_cp.ll_lecture_id
                ';
        $lecture_logs = $this->db->query($query)->result_array();
        if (!empty($lecture_logs)) {
            foreach ($lecture_logs as $lecture_log) {
                $save = array();
                $save['id'] = $lecture_log['id'];
                if ($lecture_log['id']) {
                    $save['old'] = true;
                }
                $save['ll_user_id'] = $user_id;
                $save['ll_lecture_id'] = $lecture_log['lecture_id'];
                $save['ll_percentage'] = '0';
                $save['ll_attempt'] = intval($lecture_log['ll_attempt'] + 1);
                $this->save_lecture_log($save);
            }
        }
    }

    public function unsubscribe_user($param = array())
    {
        $user_id    = isset($param['user_id']) ? $param['user_id'] : false;
        $user_ids   = isset($param['user_ids']) ? $param['user_ids'] : false;
        $course_id  = isset($param['course_id']) ? $param['course_id'] : false;

        if($course_id)
        {
            $this->db->where('cs_course_id', $course_id);
        }

        if($user_id)
        {
            $this->db->where('cs_user_id', $user_id);
        }

        if($user_ids)
        {
            $this->db->where_in('cs_user_id', $user_ids);
        }

        if($course_id || $user_id || $user_ids)
        {
            $result = $this->db->delete('course_subscription');
        }
        else
        {
            $result = false;
        }

        if(($user_ids || $user_id) && $course_id)
        {
            if($user_id)
            {
                $this->db->where('sr_user_id', $user_id);
            }
            if($user_ids)
            {
                $this->db->where_in('sr_user_id', $user_ids);
            }
            $this->db->where('sr_course_id', $course_id);
            $result = $this->db->delete('subject_report');
        }
        
        //echo $this->db->last_query();die;
        return $result;
       
    }

    public function reset_certificates($param = array())
    {
        $user_ids   = isset($param['user_ids']) ? $param['user_ids'] : false;
        $course_id  = isset($param['course_id']) ? $param['course_id'] : false;
        $data['cs_download_certificate']       = json_encode(array());
        if($course_id){
            $this->db->where('cs_course_id', $course_id);
        }
        if($user_ids){
            $this->db->where_in('cs_user_id', $user_ids);
        }
        $result = $this->db->update('course_subscription', $data);
        //echo $this->db->last_query();die;
        return $result;
       
    }

    public function remove_logs($param){

        $user_id    = isset($param['user_id']) ? $param['user_id'] : false;
        $user_ids   = isset($param['user_ids']) ? $param['user_ids'] : false;
        $course_id  = isset($param['course_id']) ? $param['course_id'] : false;
        $course_ids  = isset($param['course_ids']) ? $param['course_ids'] : false;
        $lecture_id = isset($param['lecture_id'])?$param['lecture_id']:false;
        $lecture_ids = isset($param['lecture_ids'])?$param['lecture_ids']:false;

        if($course_id){

            $this->db->where('ll_course_id', $course_id);
        }
        if($course_ids){

            $this->db->where_in('ll_course_id', $course_ids);
        }
        if($user_ids){

            $this->db->where_in('ll_user_id', $user_ids);
        }
        if($user_id){

            $this->db->where('ll_user_id', $user_id);
        }
        if($lecture_id){

            $this->db->where('ll_lecture_id', $lecture_id);
        }
        if($lecture_ids){

            $this->db->where_in('ll_lecture_id', $lecture_ids);
        }
        
        $result = $this->db->delete('lecture_log');
        return $result;
    }
    public function remove_asessment_attempts($param){

        $user_id    = isset($param['user_id']) ? $param['user_id'] : false;
        $user_ids   = isset($param['user_ids']) ? $param['user_ids'] : false;
        $course_id  = isset($param['course_id']) ? $param['course_id'] : false;
        $course_ids  = isset($param['course_ids']) ? $param['course_ids'] : false;
        $lecture_id = isset($param['lecture_id'])?$param['lecture_id']:false;
        $lecture_ids = isset($param['lecture_ids'])?$param['lecture_ids']:false;

        if($course_id){

            $this->db->where('aa_course_id', $course_id);
        }
        if($course_ids){

            $this->db->where_in('aa_course_id', $course_ids);
        }
        if($user_id){

            $this->db->where('aa_user_id', $user_id);
        }
        if($user_ids){

            $this->db->where_in('aa_user_id', $user_ids);
        }
        if($lecture_id){

            $this->db->where('aa_lecture_id', $lecture_id);
        }
        if($lecture_ids){

            $this->db->where_in('aa_lecture_id', $lecture_ids);
        }
        $result = $this->db->delete('assessment_attempts');
        
        return $result;
    }
    public function remove_asessment_report($param){

        $user_id     = isset($param['user_id']) ? $param['user_id'] : false;
        $user_ids   = isset($param['user_ids']) ? $param['user_ids'] : false;
        $course_id   = isset($param['course_id']) ? $param['course_id'] : false;
        $course_ids   = isset($param['course_ids']) ? $param['course_ids'] : false;
        $lecture_id  = isset($param['lecture_id'])?$param['lecture_id']:false;
        $lecture_ids = isset($param['lecture_ids'])?$param['lecture_ids']:false;

        if($course_id){

            $this->db->where('ar_course_id', $course_id);
        }
        if($course_ids){

            $this->db->where_in('ar_course_id', $course_ids);
        }
        if($user_id){

            $this->db->where('ar_user_id', $user_id);
        }
        if($user_ids){

            $this->db->where_in('ar_user_id', $user_ids);
        }
        if($lecture_id){

            $this->db->where('ar_lecture_id', $lecture_id);
        }
        if($lecture_ids){

            $this->db->where_in('ar_lecture_id', $lecture_ids);
        }
        
        $result = $this->db->delete('assessment_report');
        return $result;
    }

    /*
     * To get the assigned tutors's details
     * Modified by Neethu KP
     * Modified at 16/01/2017
     */
    public function assigned_tutors($param = array())
    {
        $course_id = isset($param['course_id']) ? $param['course_id'] : 0;
        $concat = isset($param['concat']) ? $param['concat'] : false;
        if ($concat) {
            $this->db->select('GROUP_CONCAT(users.us_name SEPARATOR ", ") as tutors');
        } else {
            $this->db->select('users.id, users.us_name, users.us_image,users.us_email');
        }
        $this->db->join('users', 'course_tutors.ct_tutor_id = users.id', 'left');
        $this->db->where('ct_course_id', $course_id);
        $this->db->where('us_role_id', '3');

        if ($concat) {
            $result = $this->db->get('course_tutors')->row_array();
        } else {
            $result = $this->db->get('course_tutors')->result_array();
        }
        return $result;
    }

    public function sections($param = array())
    {
        $order_by   = isset($param['order_by']) ? $param['order_by'] : 'id';
        $select     = isset($param['select']) ? $param['select'] : 'section.*';
        $direction  = isset($param['direction']) ? $param['direction'] : 'ASC';
        $status     = isset($param['status']) ? $param['status'] : '';
        $count      = isset($param['count']) ? $param['count'] : false;
        $course_id  = isset($param['course_id']) ? $param['course_id'] : false;
        $limit      = isset($param['limit']) ? $param['limit'] : false;
        $offset     = isset($param['offset']) ? $param['offset'] : false;

        $this->db->select($select);
        $this->db->order_by($order_by, $direction);
        $this->db->where('s_deleted', '0');
        if ($offset) {
            $this->db->limit(100, $offset);
        }
        if ($status != '') {
            $this->db->where('s_status', $status);
        }
        if ($course_id) {
            $this->db->where('s_course_id', $course_id);
        }

        if ($limit) {
            $this->db->limit($limit);
        }
        
        $this->db->where('s_account_id', config_item('id'));
        if ($count) {
            $result = $this->db->count_all_results('section');
        } else {
            $result = $this->db->get('section')->result_array();
        }
        //echo $this->db->last_query();die;
        return $result;
    }

    public function save_section($data)
    {
        $data['s_account_id'] = config_item('id');
        course_lecture_activity_save($data);
        if ($data['id']) {
            $this->db->where('id', $data['id']);
            $this->db->where('s_account_id', config_item('id'));
            $this->db->update('section', $data);
            return $data['id'];
        } else {
            $this->db->insert('section', $data);
            return $this->db->insert_id();
        }
    }

    public function section($param = array())
    {
        $select = isset($param['select'])?$param['select']:'*';
        $count = isset($param['count']) ? $param['count'] : false;
        $course_id = isset($param['course_id']) ? $param['course_id'] : false;
        $this->db->select($select);
        //echo '<pre>'; print_r($param);die;
        if (isset($param['status'])) {
            $this->db->where('s_status', 1);
        }
        if (isset($param['name'])) {
            if (isset($param['filter_id'])) {
                $this->db->where('id!=', $param['filter_id']);
            }
            $this->db->where('s_name', $param['name']);
        }

        if (isset($param['id'])) 
        {
            $this->db->where('id', $param['id']);
        }

        if (isset($param['deleted']) && $param['deleted'] != '') 
        {
            $this->db->where('s_deleted', $param['deleted']);
        }

        if (isset($param['course_id'])) 
        {
            $this->db->where('s_deleted', '0');
            $this->db->where('s_course_id', $param['course_id']);
        }

        $this->db->where('s_account_id', config_item('id'));

        if ($count)
        {
            $result = $this->db->count_all_results('section');
        }
        else
        {
            $result = $this->db->get('section')->row_array();
        }
        //echo $this->db->last_query();die;
        return $result;
    }

    public function course_enrolled($param){

        $course_id  = isset($param['course_id']) ? $param['course_id'] : false;
        $user_id    = isset($param['user_id']) ? $param['user_id'] : false;
        $course_ids = isset($param['course_ids'])? $param['course_ids']:false;
        $user_ids   = isset($param['user_ids'])? $param['user_ids']:false;
        $user_email_needed   = isset($param['user_email_needed'])? $param['user_email_needed']:false;
        $select     = isset($param['select'])?$param['select']:'*';

        $this->db->select($select);
        $this->db->from('course_subscription');
        if( $user_email_needed )
        {
            $this->db->join('users', 'course_subscription.cs_user_id = users.id', 'left');
        }
        if ($course_id) {
            $this->db->where('course_subscription.cs_course_id', $course_id);
        }

        if($course_ids){
            $this->db->where_in('course_subscription.cs_course_id', $course_ids);
        }

        if($user_id){

            $this->db->where('course_subscription.cs_user_id', $user_id);
        }

        if($user_ids){

            $this->db->where_in('course_subscription.cs_user_id', $user_ids);
        }
        $this->db->where('cs_account_id', config_item('id'));
        $query = $this->db->get();
        return $query->result_array();
        //echo $this->db->last_query();die;
    }

    public function enrolled($param = array())
    {
        $course_id      = isset($param['course_id']) ? $param['course_id'] : false;
        $count          = isset($param['count']) ? $param['count'] : false;
        $check_completion = isset($param['check_completion']) ? $param['check_completion'] : false;

        $approved       = isset($param['approved']) ? $param['approved'] : '';
        $expired        = isset($param['expired']) ? $param['expired'] : '';
        $certificate_issued = isset($param['certificate_issued']) ? $param['certificate_issued'] : '';
        $keyword        = isset($param['keyword']) ? $param['keyword'] : '';
        $filter         = isset($param['filter']) ? $param['filter'] : 0;
        $institute_id   = isset($param['institute_id'])? $param['institute_id'] : '';
        $branch_id      = isset($param['branch_id'])? $param['branch_id'] : '';
        $batch_id       = isset($param['batch_id'])? $param['batch_id'] : '';

        $order_by = isset($param['order_by']) ? $param['order_by'] : 'users.us_name';
        $direction = isset($param['direction']) ? $param['direction'] : 'ASC';
        $limit = isset($param['limit']) ? $param['limit'] : 0;
        $offset = isset($param['offset']) ? $param['offset'] : 0;

        $select     = isset($param['select'])? $param['select'] : 'course_subscription.*, users.us_name, users.us_email, users.us_image, users.us_phone';
        $this->db->select($select);
        
        $this->db->join('users', 'course_subscription.cs_user_id = users.id', 'left');
        if ($course_id) {
            $this->db->where('course_subscription.cs_course_id', $course_id);
        }

        if ($approved != '') {
            $this->db->where('course_subscription.cs_approved', $approved);
        }

        if ($expired != '') {
            if ($expired == '1') {
                $this->db->where('course_subscription.cs_end_date <', date('Y-m-d'));
            } else {
                $this->db->where('course_subscription.cs_end_date >=', date('Y-m-d'));
            }
        }

        if ($certificate_issued != '') {
            $this->db->where('course_subscription.cs_certificate_issued', $certificate_issued);
        }

        if ($keyword) {
            $this->db->group_start();
            $this->db->or_like(['us_name' => $keyword, 'us_institute_code' => $keyword, 'us_phone' => $keyword]);
            $this->db->group_end();
        }

        if ($institute_id) {
            $this->db->where('users.us_institute_id', $institute_id);
        }
        if($branch_id) {
            $this->db->where('users.us_branch', $branch_id);
        }
        if($batch_id) {
            $this->db->like('users.us_groups', $batch_id);
        }

        if ($filter) {
            switch ($filter) {
                case 'active':
                    $this->db->where('course_subscription.cs_approved', '1');
                    // $this->db->where('course_subscription.cs_end_date >=', date('Y-m-d'));
                    break;
                case 'suspended':
                    
                    $this->db->where_in('course_subscription.cs_approved', array('0','2'));
                    break;
                case 'completed':
                    $check_completion = true;
                    $this->db->where('(course_subscription.cs_percentage > 95 OR course_subscription.cs_completion_registered = "1")');
                    $method = $filter;
                    break;
                case 'incompleted':
                    $check_completion = true;
                    $this->db->where('course_subscription.cs_percentage < 95 AND course_subscription.cs_completion_registered = "0"');
                    $method = $filter;
                    break;
                case 'not_started':
                    $check_completion = true;
                    $this->db->where('course_subscription.cs_percentage = 0');
                    $method = $filter;
                    break;
                default:
                    break;
            }
        }
        //to prevent the deleted resoceds
        $this->db->where("users.id is NOT NULL");
        //end
        $this->db->where('cs_account_id', config_item('id'));
        $this->db->order_by($order_by, $direction);
        if ($limit > 0) {
            $this->db->limit($limit, $offset);
        }
        
        $result = $this->db->get('course_subscription')->result_array();
        //echo $this->db->last_query(); die;
        if ($count) {
            return sizeof($result);
        } else {
            return $result;
        }
    }

    // function to get enrolled users without joining users
    public function enrolled_users($param = array())
    {
        //echo '<pre>';print_r($param);die();
        $course_id      = isset($param['course_id']) ? $param['course_id'] : false;
        $count          = isset($param['count']) ? $param['count'] : false;
        $check_completion = isset($param['check_completion']) ? $param['check_completion'] : false;

        $approved       = isset($param['approved']) ? $param['approved'] : '';
        $expired        = isset($param['expired']) ? $param['expired'] : '';
        $archived       = isset($param['archived']) ? $param['archived'] : '2';
        $certificate_issued = isset($param['certificate_issued']) ? $param['certificate_issued'] : '';
        $keyword        = isset($param['keyword']) ? $param['keyword'] : '';
        $filter         = isset($param['filter']) ? $param['filter'] : 0;
        $institute_id   = isset($param['institute_id'])? $param['institute_id'] : '';
        $branch_id      = isset($param['branch_id'])? $param['branch_id'] : '';
        $batch_id       = isset($param['batch_id'])? $param['batch_id'] : '';
        $grade          = isset($param['grade'])? $param['grade'] : 0;

        $order_by       = isset($param['order_by']) ? $param['order_by'] : 'cs_user_name';
        $direction      = isset($param['direction']) ? $param['direction'] : 'ASC';
        $limit          = isset($param['limit']) ? $param['limit'] : 0;
        $offset         = isset($param['offset']) ? $param['offset'] : 0;

        $select     = isset($param['select'])? $param['select'] : 'course_subscription.*';
        $this->db->select($select);
        
        // $this->db->join('users', 'course_subscription.cs_user_id = users.id', 'left');
        if ($course_id) {
            $this->db->where('course_subscription.cs_course_id', $course_id);
        }

        if ($approved != '') {
            $this->db->where('course_subscription.cs_approved', $approved);
        }

        if ($expired != '') {
            if ($expired == '1') {
                $this->db->where('course_subscription.cs_end_date <', date('Y-m-d'));
            } else {
                $this->db->where('course_subscription.cs_end_date >=', date('Y-m-d'));
            }
        }

        if($archived !='2')
        {
            $this->db->where('course_subscription.cs_archived', $archived);
        }

        if ($certificate_issued != '') {
            $this->db->where('course_subscription.cs_certificate_issued', $certificate_issued);
        }

        if ($keyword) {
            // $this->db->group_start();
            $this->db->like('course_subscription.cs_user_name', $keyword);
            // $this->db->group_end();
        }

        if ($institute_id) {
            $this->db->where('course_subscription.cs_user_institute', $institute_id);
        }
        
        if($batch_id) {
            $where = "FIND_IN_SET('".$batch_id."', course_subscription.cs_user_groups)"; 
            $this->db->where($where);
            //$this->db->like('course_subscription.cs_user_groups', $batch_id);
        }

        if($grade) {
            $this->db->where('(course_subscription.cs_auto_grade = "'.$grade.'" OR course_subscription.cs_manual_grade="'.$grade.'")');
        }

        if ($filter) {
            switch ($filter) {
                case 'active':
                    $this->db->where('course_subscription.cs_approved', '1');
                    $this->db->where('course_subscription.cs_end_date >=', date('Y-m-d'));
                    break;
                case 'suspended':
                    $this->db->where('course_subscription.cs_approved', '0');
                    $this->db->or_where('course_subscription.cs_approved', '2');
                    break;
                case 'completed':
                    $check_completion = true;
                    $this->db->where('course_subscription.cs_percentage > 95');
                    $method = $filter;
                    break;
                case 'incompleted':
                    $check_completion = true;
                    $this->db->where('course_subscription.cs_percentage < 95');
                    $method = $filter;
                    break;
                case 'not_started':
                    $check_completion = true;
                    $this->db->where('course_subscription.cs_percentage = 0');
                    $method = $filter;
                    break;
                default:
                    break;
            }
        }
        //to prevent the deleted resoceds
        $this->db->where("course_subscription.cs_user_id is NOT NULL");
        //end
        $this->db->order_by($order_by, $direction);
        if ($limit > 0) {
            $this->db->limit($limit, $offset);
        }
        $this->db->where('cs_account_id', config_item('id'));
        $result = $this->db->get('course_subscription');
        //echo $this->db->last_query(); die;
        if ($count) {
            return $result->num_rows();
        } else {
            return $result->result_array();
        }
    }

    public function course_wishlist($param = array())
    {
        $course_id = isset($param['course_id']) ? $param['course_id'] : false;
        $count = isset($param['count']) ? $param['count'] : false;

        $this->db->select('course_wishlist.*, users.us_name, users.us_email, users.us_image');
        $this->db->join('users', 'course_wishlist.cw_user_id = users.id', 'left');
        if ($course_id) {
            $this->db->where('course_wishlist.cw_course_id', $course_id);
        }

        $result = $this->db->get('course_wishlist')->result_array();

        if ($count) {
            return sizeof($result);
        } else {
            return $result;
        }
    }

    private function completion_type($percentage, $case)
    {
        switch ($case) {
            case 'completed':
                $return = ($percentage > 95);
                break;
            case 'incompleted':
                $return = ($percentage < 95);
                break;
            case 'not_started':
                $return = ($percentage == 0);
                break;

            default:
                $return = false;
                break;
        }
        return $return;
    }

    public function get_user_email_form_group($group_id)
    {

        $this->db->select('users.us_email');
        $this->db->from('users');
        $this->db->like('concat(",",users.us_groups,",")', ',' . $group_id . ',');
        $result = $this->db->get();
        return $result->result_array();
    }

    public function save_descriptive_test($save)
    {
         $this->db->insert('descrptive_tests', $save);
         //echo $this->db->last_query();
    }

    public function update_descriptive_test($save, $lecture_id)
    {

        $this->db->where('dt_lecture_id', $lecture_id);
        $this->db->update('descrptive_tests', $save);
    }

    public function change_descriptive_file($save, $lecture_id)
    {

        $this->db->where('dt_lecture_id', $lecture_id);
        $this->db->update('descrptive_tests', $save);
    }

    public function get_attended_descriptive_test($id)
    {
        $this->db->select('users.us_name, users.us_image, descrptive_test_user_answered.dtua_user_id, descrptive_test_user_answered.dtua_lecture_id,descrptive_test_user_answered.updated_date, descrptive_test_user_answered.mark, descrptive_test_user_answered.id as attempted_id');
        $this->db->where('descrptive_test_user_answered.dtua_lecture_id', $id);
        $this->db->where('descrptive_test_user_answered.status', '1');
        $this->db->from('descrptive_test_user_answered');
        $this->db->join('users', 'users.id = descrptive_test_user_answered.dtua_user_id');
        $this->db->order_by('updated_date', 'desc');
        return $this->db->get()->result_array();
    }

    public function get_user_data($user_id)
    {
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where('id', $user_id);
        $this->db->where('us_account_id', config_item('id'));
        return $this->db->get()->row_array();
    }

    public function get_reported_comments($param = array())
    {
        $parent_id = isset($param['parent_id']) ? $param['parent_id'] : '';
        $child_id = isset($param['child_id']) ? $param['child_id'] : '';
        $this->db->select('*');
        $this->db->from('course_discussions');
        if ($child_id) {
            $this->db->where('id', $child_id);
        } else {
            $this->db->where('id', $parent_id);
        }
        return $this->db->get()->row_array();
    }

    public function get_answer_details($lecture_id, $user_id)
    {

        $this->db->select('*');
        $this->db->from('descrptive_test_user_answered');
        $this->db->where('dtua_lecture_id', $lecture_id);
        $this->db->where('dtua_user_id', $user_id);
        return $this->db->get()->row_array();
    }

    public function get_assignment_attempt($param=array())
    {
        $select = isset($param['select']) ? $param['select'] : false;
        if($select){
            $this->db->select($select);
        } else {
            $this->db->select('*');
        }
        $this->db->from('descrptive_test_user_answered');
        $this->db->where('id', $param['id']);
        return $this->db->get()->row_array();
       // echo $this->db->last_query();
    }

    public function get_course_comments($param = array())
    {
        $limit = isset($param['limit']) ? $param['limit'] : 0;
        $course_id = isset($param['course_id']) ? $param['course_id'] : 0;
        $parent_id = isset($param['parent_id']) ? $param['parent_id'] : 1;
        $this->db->select('*');
        $this->db->from('course_discussions');
        $this->db->where('course_id', $course_id);
        if ($parent_id == 0) {
            $this->db->where('parent_id', $parent_id);
        }
        $this->db->order_by('id', 'desc');
        if ($limit > 0) {
            $this->db->limit($limit);
        }
        return $this->db->get()->result_array();
    }

    public function get_course_comments_user($param = array())
    {

        $limit = isset($param['limit']) ? $param['limit'] : 0;
        $course_id = isset($param['course_id']) ? $param['course_id'] : 0;
        $discussion_id = isset($param['discussion_id']) ? $param['discussion_id'] : 0;
        $parent_id = isset($param['parent_id']) ? $param['parent_id'] : '';
        $keyword = isset($param['keyword']) ? $param['keyword'] : '';
        //$order_by   = isset($param['order_by'])?$param['order_by']:'ASC';

        //get user course
        $teacher_id = isset($param['teacher_id']) ? $param['teacher_id'] : false;
        $tutor_courses = 0;
        if ($teacher_id) {
            $this->db->select('GROUP_CONCAT(ct_course_id) as course_ids');
            $this->db->where('ct_tutor_id', $teacher_id);
            $tutor_courses = $this->db->get('course_tutors')->row_array();
            $tutor_courses = isset($tutor_courses['course_ids']) ? $tutor_courses['course_ids'] : 0;
        }
        //End

        $this->db->select('course_discussions.*, users.us_image, users.us_name, users.us_role_id, roles.rl_type,roles.id as role_id');
        $this->db->from('course_discussions');
        $this->db->join('users', 'users.id = course_discussions.user_id');
        $this->db->join('roles', 'users.us_role_id = roles.id');
        $this->db->where('comment_deleted', '0');
        if ($teacher_id) {
            $this->db->where_in('course_discussions.course_id', explode(',', $tutor_courses));
        }

        if ($course_id) {
            $this->db->where('course_discussions.course_id', $course_id);
        }

        if ($discussion_id) {
            $this->db->where('course_discussions.id', $discussion_id);
        }
        if ($parent_id == '0' || $parent_id) {
            $this->db->where('course_discussions.parent_id', $parent_id);
        }
        $this->db->order_by('id', 'DESC');
        if ($limit > 0) {
            $this->db->limit($limit);
        }
        if ($keyword) {
            $this->db->where("(course_discussions.comment_title LIKE '%$keyword%' || course_discussions.comment LIKE '%$keyword%')");

        }
        $result = $this->db->get()->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    public function get_comments($param = array())
    {

        $attempt_id = isset($param['attempt_id']) ? $param['attempt_id'] : false;
        $user_id = isset($param['user_id']) ? $param['user_id'] : false;
        $this->db->select('descrptive_test_answers.id as comment_id, descrptive_test_answers.comment, descrptive_test_answers.file, descrptive_test_answers.updated_date, descrptive_test_answers.da_user_id, users.id as user_id, users.us_image, users.us_name');
        $this->db->from('descrptive_test_answers');
        $this->db->join('users', 'users.id = descrptive_test_answers.da_user_id');
        $this->db->where('da_attempt_id', $attempt_id);
        //$this->db->where("(da_user_id='1' OR da_user_id='".$user_id."')");
        $this->db->order_by('updated_date');
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function delete_comment($param = array())
    {
        $comment_id = isset($param['comment_id']) ? $param['comment_id'] : false;
        if ($comment_id) {
            $this->db->where('id', $comment_id);
            $this->db->delete('descrptive_test_answers');
        }
    }

    public function delete_comments_admin($param = array())
    {
        $parent_id = isset($param['parent_id']) ? $param['parent_id'] : false;
        $child_id = isset($param['child_id']) ? $param['child_id'] : false;
        $course_id = isset($param['course_id']) ? $param['course_id'] : false;
        if (!$child_id) {
            //$this->db->where('id',$parent_id);
            $this->db->where(array('id' => $parent_id, 'course_id' => $course_id));
            $this->db->delete('course_discussions');

            //$this->db->where('parent_id',$parent_id);
            $this->db->where(array('parent_id' => $parent_id, 'course_id' => $course_id));
            $this->db->delete('course_discussions');
        }
        if ($child_id) {
            //$this->db->where('id', $child_id);
            $this->db->where(array('id' => $child_id, 'course_id' => $course_id));
            $this->db->delete('course_discussions');
        }
    }

    public function report_comments_admin($save)
    {
        $this->db->insert('course_discussion_report', $save);
        return $this->db->insert_id();
    }

    public function savecomment($save)
    {
        $this->db->insert('descrptive_test_answers', $save);
        return $this->db->insert_id();
    }

    public function save_course_comment($save)
    {
        $this->db->insert('course_discussions', $save);
        return $this->db->insert_id();
    }

    public function save_new_discussion_admin($save)
    {
        $this->db->insert('course_discussions', $save);
        return $this->db->insert_id();
    }

    public function savemark($save, $where)
    {

        $this->db->where($where);
        $this->db->update('descrptive_test_user_answered', $save);
        //echo $this->db->last_query();
    }

    public function get_descriptive_test_item($id)
    {
        $this->db->select('*');
        $this->db->from('descrptive_tests');
        $this->db->where('dt_lecture_id', $id);
        return $this->db->get()->row_array();
    }

    public function get_all_courses($param = array())
    {
        $account_id = isset($param['account_id']) ? $param['account_id'] : false;
        $this->db->select('id, cb_title, cb_code');
        $this->db->from('course_basics');
        //$this->db->where('cb_status', '1');
        $this->db->where('cb_deleted', '0');
        $this->db->where('cb_account_id', config_item('id'));
        $result = $this->db->get()->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    public function get_selected_tests($cid, $only_active = true)
    {
        $this->db->select('cl_lecture_name, id');
        $this->db->from('course_lectures');
        $this->db->where('cl_course_id', $cid);
        $this->db->where('cl_lecture_type', 8);
        if ($only_active) {
            $this->db->where('cl_status', '1');
            $this->db->where('cl_deleted', '0');
        }
        return $this->db->get()->result_array();
    }

    public function get_selected_assesments($cid)
    {
        $this->db->select('cl_lecture_name, id');
        $this->db->from('course_lectures');
        $this->db->where('cl_course_id', $cid);
        $this->db->where('cl_lecture_type', 3);
        $this->db->where('cl_status', '1');
        $this->db->where('cl_deleted', '0');
        return $this->db->get()->result_array();
    }

    public function assessment_attempt($param = array())
    {
        $user_id = isset($param['user_id']) ? $param['user_id'] : false;
        $course_id = isset($param['course_id']) ? $param['course_id'] : false;
        $approved = isset($param['approved']) ? $param['approved'] : '';

        $this->db->select('users.*, assessment_attempts.aa_assessment_id, assessment_attempts.aa_user_id, assessment_attempts.aa_attempted_date, assessment_attempts.aa_duration, assessments.a_lecture_id, course_lectures.cl_lecture_name, course_lectures.cl_course_id, course_basics.cb_title, course_basics.cb_status, assessments.a_duration, assessments.a_lecture_id, assessment_attempts.id as attempt_id');
        $this->db->join('users', 'users.id = assessment_attempts.aa_user_id', 'left');
        $this->db->join('assessments', 'assessments.id = assessment_attempts.aa_assessment_id', 'left');
        $this->db->join('course_lectures', 'course_lectures.id = assessments.a_lecture_id', 'left');
        $this->db->join('course_basics', 'course_basics.id = course_lectures.cl_course_id', 'left');

        if ($user_id) {
            $this->db->where('users.id', $user_id);
        }
        if ($course_id) {
            $this->db->where('course_lectures.id', $course_id);
        }
        $result = $this->db->get('assessment_attempts')->result_array();
        //echo $this->db->last_query();die;
        return $result;
    }

    public function assessment_report($param = array())
    {
        $assessment_id = isset($param['assessment_id']) ? $param['assessment_id'] : false;
        $user_id = isset($param['user_id']) ? $param['user_id'] : false;
        $lecture_id = isset($param['lecture_id']) ? $param['lecture_id'] : false;

        $this->db->select('users.*, assessment_attempts.id, assessment_attempts.aa_assessment_id, assessment_attempts.aa_user_id, assessment_attempts.aa_attempted_date, assessment_attempts.aa_duration, assessment_questions.aq_question_id, questions.q_type, questions.q_question, questions.q_answer, questions.q_options, questions.q_positive_mark, q_negative_mark , assessment_report.ar_question_id, assessment_report.ar_answer, assessment_report.ar_mark, assessment_report.id as ar_id');
        $this->db->join('users', 'users.id = assessment_attempts.aa_user_id', 'left');
        $this->db->join('assessment_questions', 'assessment_questions.aq_assesment_id = assessment_attempts.aa_assessment_id', 'left');
        $this->db->join('questions', 'questions.id = assessment_questions.aq_question_id', 'left');
        $this->db->join('assessment_report', 'assessment_report.ar_question_id = assessment_questions.aq_question_id AND assessment_report.ar_attempt_id = assessment_attempts.id', 'left');

        if ($user_id) {
            $this->db->where('users.id', $user_id);
        }

        if ($lecture_id) {
            $this->db->where('users.id', $user_id);
        }

        if ($assessment_id) {
            $this->db->where('assessment_attempts.id', $assessment_id);
        }
        $result = $this->db->get('assessment_attempts')->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    public function assessment_report_test($param = array())
    {
        $attempt_id = isset($param['attempt_id']) ? $param['attempt_id'] : false;
        $user_id = isset($param['user_id']) ? $param['user_id'] : false;
        $lecture_id = isset($param['lecture_id']) ? $param['lecture_id'] : false;

        $this->db->select('assessment_attempts.id, assessment_attempts.aa_duration, assessment_attempts.aa_assessment_id, assessment_attempts.aa_user_id, assessment_attempts.aa_attempted_date, assessment_questions.aq_question_id, questions.q_type, questions.q_question, questions.q_answer, questions.q_options, questions.q_positive_mark, q_negative_mark , assessment_report.ar_question_id, assessment_report.ar_answer, assessment_report.ar_mark, assessment_report.id as ar_id');
        $this->db->join('users', 'users.id = assessment_attempts.aa_user_id', 'left');
        $this->db->join('assessment_questions', 'assessment_questions.aq_assesment_id = assessment_attempts.aa_assessment_id', 'left');
        $this->db->join('questions', 'questions.id = assessment_questions.aq_question_id', 'left');
        $this->db->join('assessment_report', 'assessment_report.ar_question_id = assessment_questions.aq_question_id AND assessment_report.ar_attempt_id = assessment_attempts.id', 'left');

        if ($attempt_id) {
            $this->db->where('assessment_attempts.id', $attempt_id);
        }
        $result = $this->db->get('assessment_attempts')->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    public function get_assessment_attempt_details($param = array())
    {
        $assessment_attempt_id = isset($param['assessment_attempt_id']) ? $param['assessment_attempt_id'] : false;

        $this->db->select('users.us_name, users.us_image, assessment_attempts.*');
        $this->db->from('assessment_attempts');
        $this->db->join('users', 'users.id = assessment_attempts.aa_user_id');

        if ($assessment_attempt_id) {
            $this->db->where('assessment_attempts.id', $assessment_attempt_id);
        }
        //echo $this->db->last_query(); die;
        $result = $this->db->get()->row_array();
        if ($result) {
            return $result;
        }
    }

    /*
     * To get the details regarding user generated test by attempt_id
     * Created By Neethu KP
     * Created at 13/01/2017
     */

    public function get_user_test_attempt_details($param = array())
    {
        $attempt_id = isset($param['user_generated_test_attempt_id']) ? $param['user_generated_test_attempt_id'] : false;

        $this->db->select('users.us_name, users.us_image, user_generated_assessment_attempt.*');
        $this->db->from('user_generated_assessment_attempt');
        $this->db->join('users', 'users.id = user_generated_assessment_attempt.uga_user_id');

        if ($attempt_id) {
            $this->db->where('user_generated_assessment_attempt.id', $attempt_id);
        }
        //echo $this->db->last_query(); die;
        $result = $this->db->get()->row_array();
        if ($result) {
            return $result;
        }
    }

    /*
     * To get the details regarding user generated test by attempt_id
     * Created By Neethu KP
     * Created at 13/01/2017
     */

    public function get_challenge_zone_attempt_details($param = array())
    {
        $attempt_id = isset($param['cz__attempt_id']) ? $param['cz__attempt_id'] : false;

        $this->db->select('users.us_name, users.us_image, challenge_zone_attempts.*');
        $this->db->from('challenge_zone_attempts');
        $this->db->join('users', 'users.id = challenge_zone_attempts.cza_user_id');

        if ($attempt_id) {
            $this->db->where('challenge_zone_attempts.id', $attempt_id);
        }
        //echo $this->db->last_query(); die;
        $result = $this->db->get()->row_array();
        if ($result) {
            return $result;
        }
    }

    public function get_assessment_details($param = array())
    {
        $attempt_id = isset($param['attempt_id']) ? $param['attempt_id'] : false;
        $select = array('assessment_report.ar_duration', 'count(assessment_report.ar_question_id) as Total');
        $this->db->select($select);
        $this->db->from('assessment_report');
        if ($attempt_id) {
            $this->db->where('assessment_report.ar_attempt_id', $attempt_id);
        }
        //echo $this->db->last_query(); die;
        $result = $this->db->get()->result_array();
        if ($result) {
            return $result;
        }
    }

    public function get_question_options($options)
    {

        $this->db->select('*');
        $this->db->from('questions_options');
        $this->db->where_in('id', explode(',', $options));
        return $this->db->get()->result_array();
    }

    /*
     * To get the title of user generated test
     * Created By Neethu KP
     * Created at 13/01/2017
     */
    public function get_user_generated_test($param = array())
    {

        $assesment_id = isset($param['uga_id']) ? $param['uga_id'] : false;
        $this->db->select('uga_title');
        $this->db->from('user_generated_assesment');
        if ($assesment_id) {
            $this->db->where('user_generated_assesment.id', $assesment_id);
        }
        $result = $this->db->get()->row_array();
        if ($result) {
            return $result;
        }
    }

    /*
     * To get the title of Challenge zone tests
     * Created By Neethu KP
     * Created at 13/01/2017
     */
    public function get_challenge_zone_test($param = array())
    {
        $assesment_id = isset($param['cz_assessment_id']) ? $param['cz_assessment_id'] : false;
        $this->db->select('cz_title');
        $this->db->from('challenge_zone');
        if ($assesment_id) {
            $this->db->where('challenge_zone.id', $assesment_id);
        }
        $result = $this->db->get()->row_array();
        if ($result) {
            return $result;
        }
    }

    public function get_assesment($param = array())
    {
        $assesment_id = isset($param['assesment_id']) ? $param['assesment_id'] : false;
        $this->db->select('a_lecture_id');
        $this->db->from('assessments');
        if ($assesment_id) {
            $this->db->where('assessments.id', $assesment_id);
        }
        $result = $this->db->get()->row_array();
        if ($result) {
            return $result;
        }
    }

    public function get_survey($param = array())
    {
        $survey_id = isset($param['survey_id']) ? $param['survey_id'] : false;
        $lecture_id = isset($param['lecture_id']) ? $param['lecture_id'] : false;
        $select     = isset($param['select']) ? $param['select'] : false;

        
        if($select)
        {
            $this->db->select($select);
        }
        else{
            $this->db->select('s_lecture_id');
        }
        $this->db->from('survey');
        if ($survey_id) {
            $this->db->where('survey.id', $survey_id);
        }

        if ($lecture_id) {
            $this->db->where('survey.s_lecture_id', $lecture_id);
        }

        return $this->db->get()->row_array();

    }

    public function get_surveys($param = array())
    {
        $course_id = isset($param['course_id']) ? $param['course_id'] : false;
        if ($course_id) {
            $this->db->where('s_course_id', $course_id);
        }
        return $this->db->get()->result_array();
    }

    public function get_lecture($param = array())
    {

        $lecture_id = isset($param['lecture_id']) ? $param['lecture_id'] : false;
        $this->db->select('cl_lecture_name');
        $this->db->from('course_lectures');
        if ($lecture_id) {
            $this->db->where('course_lectures.id', $lecture_id);
        }
        $this->db->where('cl_account_id', config_item('id'));
        $result = $this->db->get()->row_array();
        if ($result) {
            return $result;
        }
    }

    public function save_assessment_explanatory($id, $ar_mark)
    {

        $this->db->where('id', $id);
        $this->db->update('assessment_report', array('ar_mark' => $ar_mark));

        $this->db->select('ar_attempt_id');
        $this->db->from('assessment_report');

        if ($id) {
            $this->db->where('assessment_report.id', $id);
        }
        $result = $this->db->get()->row_array();
        if (!empty($result)) {

            $this->db->where('id', $result['ar_attempt_id']);
            $this->db->update('assessment_attempts', array('aa_valuated' => 1));
        }

        return $result;

    }

    public function get_course_details($param = array())
    {

        $course_id = isset($param['course_id']) ? $param['course_id'] : false;
        $this->db->select('course_basics.cb_title, course_basics.cb_status, course_basics.cb_category , course_basics.cb_is_free,  course_basics.cb_preview, course_basics.cb_preview_time, course_basics.cb_price, course_basics.cb_discount, course_basics.cb_description,course_basics.cb_meta,course_basics.cb_meta_description, course_basics.cb_promo, course_basics.cb_image');
        $this->db->from('course_basics');

        if ($course_id) {
            $this->db->where('course_basics.id', $course_id);
        }
        $this->db->where('cb_account_id', config_item('id'));
        return $this->db->get()->row_array();
    }

    public function get_course_tutors($param = array())
    {

        $course_id = isset($param['course_id']) ? $param['course_id'] : false;
        if (isset($param['select'])) {
            $this->db->select($param['select']);
        } else {
            $this->db->select('users.id, users.us_name, users.us_image');
        }
        $this->db->from('course_tutors');
        $this->db->join('users', 'users.id = course_tutors.ct_tutor_id');
        $this->db->join('roles', 'users.us_role_id = roles.id');
        if ($course_id) {
            $this->db->where('course_tutors.ct_course_id', $course_id);
        }
        if (isset($param['role_filter'])) {
            $this->db->where('us_role_id', $param['role_filter']);
        }
        $this->db->where('us_account_id', config_item('id'));
        return $this->db->get()->result_array();
    }

    public function get_subscription_count($id)
    {
        $this->db->select('id');
        $this->db->from('course_subscription');
        $this->db->where('cs_course_id', $id);
        $this->db->where('cs_account_id', config_item('id'));
        return $this->db->get()->num_rows();
    }

    public function get_all_lectures_count($id)
    {

        $this->db->select('*');
        $this->db->from('course_lectures');
        $this->db->where('cl_course_id', $id);
        $this->db->where('cl_deleted', '0');
        $this->db->where('cl_status', '1');
        $this->db->where('cl_account_id', config_item('id'));
        $result = $this->db->get();
        return $result->num_rows();
    }

    public function get_lecture_count($id)
    {
        $this->db->select('*');
        $this->db->from('course_lectures');
        $this->db->where('cl_course_id', $id);
        $this->db->where('cl_deleted', '0');
        $this->db->where('cl_status', '1');
        $this->db->where('cl_account_id', config_item('id'));
        //$this->db->where_in('cl_lecture_type', array('1', '4', '9'));
        $result = $this->db->get();
        return $result->num_rows();
    }

    public function get_online_test_count($id)
    {
        $this->db->select('*');
        $this->db->from('course_lectures');
        $this->db->where('cl_course_id', $id);
        $this->db->where('cl_deleted', '0');
        $this->db->where('cl_status', '1');
        $this->db->where_in('cl_lecture_type', array('3', '8'));
        $this->db->where('cl_account_id', config_item('id'));
        $result = $this->db->get();
        return $result->num_rows();
    }

    public function get_document_count($id)
    {
        $this->db->select('*');
        $this->db->from('course_lectures');
        $this->db->where('cl_course_id', $id);
        $this->db->where('cl_deleted', '0');
        $this->db->where('cl_status', '1');
        $this->db->where('cl_account_id', config_item('id'));
        $this->db->where_in('cl_lecture_type', array('2', '5', '6'));
        $result = $this->db->get();
        return $result->num_rows();
        //echo $this->db->last_query();die;
    }

    public function get_live_lecture_count($id)
    {

        $this->db->select('*');
        $this->db->from('course_lectures');
        $this->db->where('cl_course_id', $id);
        $this->db->where('cl_deleted', '0');
        $this->db->where('cl_status', '1');
        $this->db->where('cl_lecture_type', 7);
        $this->db->where('cl_account_id', config_item('id'));
        $result = $this->db->get();
        return $result->num_rows();
    }

    public function get_ratting($param = array())
    {

        $course_id  = isset($param['course_id']) ? $param['course_id'] : false;
        $cc_status = isset($param['cc_status']) ? $param['cc_status'] : false;
        $this->db->select('ROUND(AVG(cc_rating), 1) as avg');
        $this->db->from('course_ratings');
        if($cc_status)
        {
            $this->db->where('cc_status', $cc_status);
        }
        $this->db->where('cc_course_id', $course_id);
        $this->db->where('cc_account_id', config_item('id'));
        $result = $this->db->get()->row_array();
        if ($result['avg'] != '') {
            return $result['avg'];
        } else {
            return 0;
        }
    }

    public function get_review_count($param = array())
    {
        $course_id = isset($param['course_id']) ? $param['course_id'] : false;

        $this->db->select('*');
        if ($course_id) {
            $this->db->where('cc_course_id', $course_id);
        }
        $this->db->where('cc_account_id', config_item('id'));
        $result = $this->db->count_all_results('course_ratings');
        //echo $this->db->last_query();die;
        return $result;

    }

    /*
     * Modified by : Neethu KP
     * Modified at : 10/01/2017
     */
    public function get_rating_count($param = array())
    {
        $course_id = isset($param['course_id']) ? $param['course_id'] : false;
        $count_review = isset($param['count_review']) ? $param['count_review'] : false;

        $this->db->select('*');
        if (is_array($course_id)) {
            $this->db->where_in('cc_course_id', $course_id);
        } else {
            $this->db->where('cc_course_id', $course_id);
        }
        if ($count_review) {
            $this->db->where('cc_rating', $count_review);
        }
        $this->db->where('cc_account_id', config_item('id'));
        $result = $this->db->count_all_results('course_ratings');
        //echo $this->db->last_query();die;
        return $result;

    }

    public function get_course_review($param = array())
    {
        $course_id = isset($param['course_id']) ? $param['course_id'] : false;
        $order_by = isset($param['order_by']) ? $param['order_by'] : 'id';
        $direction = isset($param['direction']) ? $param['direction'] : 'DESC';
        $review_block = isset($param['block']) ? $param['block'] : '';

        $this->db->select('course_ratings.*, users.id as user_id, users.us_name, users.us_image');
        $this->db->join('users', 'course_ratings.cc_user_id = users.id', 'left');
        
        if ($review_block) {
            $this->db->where('course_ratings.cc_status', '1');
        }

        $this->db->where('cc_account_id', config_item('id'));
        $this->db->where('course_ratings.cc_course_id', $course_id);
        $this->db->order_by($order_by, $direction);

        $result = $this->db->get('course_ratings')->result_array();
        //echo $this->db->last_query();die;
        return $result;

    }

    public function get_user_review($param = array())
    {
        $course_id = isset($param['course_id']) ? $param['course_id'] : false;
        $user_id = isset($param['user_id']) ? $param['user_id'] : false;

        $this->db->select('cc_reviews');
        $this->db->from('course_ratings');
        $this->db->where('cc_course_id', $course_id);
        $this->db->where('cc_user_id', $user_id);
        $this->db->where('cc_account_id', config_item('id'));
        $result = $this->db->get()->row_array();
        //if($result['cc_reviews'] != ''){
        return $result['cc_reviews'];
        //}
        //else{
        //return 0;
        //}
    }

    public function get_user_review_admin($param = array())
    {
        $course_id = isset($param['course_id']) ? $param['course_id'] : false;
        $user_id = isset($param['user_id']) ? $param['user_id'] : false;

        $this->db->select('*');
        $this->db->from('course_ratings');
        $this->db->where('cc_course_id', $course_id);
        $this->db->where('cc_admin_rating_id', $user_id);
        $this->db->where('cc_account_id', config_item('id'));
        $result = $this->db->get()->row_array();
        if ($result['cc_reviews'] != '') {
            return $result;
        } else {
            return 0;
        }
    }

    public function get_user_ratting($param = array())
    {
        $course_id = isset($param['course_id']) ? $param['course_id'] : false;
        $user_id = isset($param['user_id']) ? $param['user_id'] : false;

        $this->db->select('cc_rating');
        $this->db->from('course_ratings');
        $this->db->where('cc_course_id', $course_id);
        $this->db->where('cc_user_id', $user_id);
        $this->db->where('cc_account_id', config_item('id'));
        $result = $this->db->get()->row_array();
        if ($result['cc_rating'] != '') {
            return $result['cc_rating'];
        } else {
            return 0;
        }
    }

    public function get_user_ratting_admin($param = array())
    {
        $course_id = isset($param['course_id']) ? $param['course_id'] : false;
        $user_id = isset($param['user_id']) ? $param['user_id'] : false;

        $this->db->select('*');
        $this->db->from('course_ratings');
        $this->db->where('cc_course_id', $course_id);
        $this->db->where('cc_admin_rating_id', $user_id);
        $this->db->where('cc_account_id', config_item('id'));
        $result = $this->db->get()->row_array();
        if ($result['cc_rating'] != '') {
            return $result;
        } else {
            return 0;
        }
    }

    //by thanveer for course content delivery page
    public function save_last_played_lecture($save = array())
    {
        if(isset($save['id'])){
            $this->db->where('id', $save['id']);
        }else{
            $this->db->where('cs_course_id', $save['cs_course_id']);
            $this->db->where('cs_user_id', $save['cs_user_id']);
        }
        $this->db->update('course_subscription', $save);
    }

    public function get_first_lecture($data)
    {
        $lecture_id = 0;

        //get the first section
        $section = $this->sections(array('course_id' => $data['course_id'], 'order_by' => 's_order_no', 'limit' => 1, 'offset' => 0, 'status' => 1));
        $section = !empty($section) ? $section[0] : $section;
        if ($section) {
            //get the first lecture for this  section
            $lecture = $this->lectures(array('course_id' => $data['course_id'], 'cl_section_id' => $section['id'], 'order_by' => 'cl_order_no', 'limit' => 1, 'offset' => 0, 'status' => 1));
            $lecture = !empty($lecture) ? $lecture[0] : $lecture;
            if ($lecture) {
                $lecture_id = $lecture['id'];
            }
        }
        return $lecture_id;
        //get the first lecture for thsi section
    }

    public function previous_lecture($param)
    {
        $lecture_id = isset($param['lecture_id']) ? $param['lecture_id'] : 0;
        if ($lecture_id == 0) {
            return $lecture_id;
        } else {
            $lecture = $this->lecture(array('id' => $lecture_id));
            $lecture_id = $this->getLectureFromSection(array('course_id' => $lecture['cl_course_id'], 'order_no' => $lecture['cl_order_no'], 'section_id' => $lecture['cl_section_id'], 'order' => 'DESC', 'condition' => '<'));
            if ($lecture_id > 0) {
                return $lecture_id;
            } else {
                // get the next intermediate section
                $section_id = $this->getIntermediateSection(array('course_id' => $lecture['cl_course_id'], 'section_id' => $lecture['cl_section_id'], 'order' => 'DESC', 'condition' => '<'));
                $lecture_id = $this->getLectureFromSection(array('course_id' => $lecture['cl_course_id'], 'section_id' => $section_id, 'order' => 'DESC'));
                return ($section_id != $lecture['cl_section_id']) ? $lecture_id : 0;
            }
        }
    }

    public function getIntermediateSection($param)
    {
        $course_id = isset($param['course_id']) ? $param['course_id'] : 0;
        $section_id = isset($param['section_id']) ? $param['section_id'] : 0;
        $order = isset($param['order']) ? $param['order'] : 'ASC';
        $condition = isset($param['condition']) ? $param['condition'] : '>';

        $section = $this->section(array('id' => $section_id));
        $order_no = isset($section['s_order_no']) ? $section['s_order_no'] : 0;

        $this->db->where(array('s_course_id' => $course_id, 's_order_no ' . $condition => $order_no));
        $this->db->where(array('s_status' => '1', 's_deleted' => '0'));
        $this->db->order_by('s_order_no', $order);
        $this->db->limit(1, 0);
        $this->db->where('s_account_id', config_item('id'));
        $result = $this->db->get('section')->row_array();
        //echo $this->db->last_query();die;
        return isset($result['id']) ? $result['id'] : $section_id;
    }

    public function next_lecture($param)
    {
        $lecture_id = isset($param['lecture_id']) ? $param['lecture_id'] : 0;
        if ($lecture_id == 0) {
            return $lecture_id;
        } else {
            //getting lecture from same section
            $lecture = $this->lecture(array('id' => $lecture_id));
            $lecture_id = $this->getLectureFromSection(array('course_id' => $lecture['cl_course_id'], 'order_no' => $lecture['cl_order_no'], 'section_id' => $lecture['cl_section_id'], 'order' => 'ASC', 'condition' => '>'));
            if ($lecture_id > 0) {
                return $lecture_id;
            } else {
                // get the next intermediate section
                $section_id = $this->getIntermediateSection(array('course_id' => $lecture['cl_course_id'], 'section_id' => $lecture['cl_section_id'], 'order' => 'ASC', 'condition' => '>'));
                $lecture_id = $this->getLectureFromSection(array('course_id' => $lecture['cl_course_id'], 'section_id' => $section_id, 'order' => 'ASC'));
                return ($section_id != $lecture['cl_section_id']) ? $lecture_id : 0;
            }
        }
    }

    public function getLectureFromSection($param)
    {
        //echo '<pre>'; print_r($param);die;
        $course_id = isset($param['course_id']) ? $param['course_id'] : 0;
        $section_id = isset($param['section_id']) ? $param['section_id'] : 0;
        $order = isset($param['order']) ? $param['order'] : 'ASC';
        $condition = isset($param['condition']) ? $param['condition'] : false;
        $order_no = isset($param['order_no']) ? $param['order_no'] : '';

        $this->db->where(array('cl_course_id' => $course_id, 'cl_section_id' => $section_id));
        if ($order_no != '') {
            $this->db->where(array('cl_order_no ' . $condition => $order_no));
        }
        $this->db->where(array('cl_status' => '1', 'cl_deleted' => '0'));
        $this->db->where('cl_account_id', config_item('id'));
        $this->db->order_by('cl_order_no', $order);
        $this->db->limit(1, 0);
        $result = $this->db->get('course_lectures')->row_array();
        //echo $this->db->last_query();die;
        return isset($result['id']) ? $result['id'] : 0;
    }

    public function get_whishlist_stat($cid, $uid)
    {

        $this->db->select('*');
        $this->db->where('cw_course_id', $cid);
        $this->db->where('cw_user_id', $uid);
        $result = $this->db->get('course_wishlist');
        if ($result->num_rows() > 0) {

            $this->db->select('*');
            $this->db->where('cs_course_id', $cid);
            $this->db->where('cs_user_id', $uid);
            $temp_result = $this->db->get('course_subscription');
            if ($temp_result->num_rows() > 0) {
                return 2;
            } else {
                return 1;
            }

        } else {
            return 0;
        }
    }

    public function get_whish_stat($cid, $uid)
    {
        $this->db->select('*');
        $this->db->where('cs_course_id', $cid);
        $this->db->where('cs_user_id', $uid);
        $result = $this->db->get('course_subscription');
        if ($result->num_rows() == 0) {

            $this->db->select('*');
            $this->db->where('cw_course_id', $cid);
            $this->db->where('cw_user_id', $uid);
            $temp_result = $this->db->get('course_wishlist');
            if ($temp_result->num_rows() > 0) {
                return 1;
            } else {
                return 0;
            }

        } else {
            return 2;
        }
    }

    public function get_purchase_stat($uid, $cid)
    {

        $this->db->select('*');
        $this->db->from('course_subscription');
        $this->db->where('cs_course_id', $cid);
        $this->db->where('cs_user_id', $uid);
        $this->db->where('cs_end_date >= CURDATE()');
        $result = $this->db->get();
        //echo $this->db->last_query();die;
        if ($result->num_rows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function get_subscribe_stat($uid, $cid)
    {

        $this->db->select('*');
        $this->db->from('course_subscription');
        $this->db->where('cs_course_id', $cid);
        $this->db->where('cs_user_id', $uid);
        $this->db->where('CURDATE() > cs_end_date');
        $result = $this->db->get();
        //echo $this->db->last_query();die;
        if ($result->num_rows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function change_whishlist($cid, $uid, $stat)
    {

        $arr = array('cw_course_id' => $cid, 'cw_user_id' => $uid);

        if ($stat == '1') {
            $this->db->insert('course_wishlist', $arr);
        } else if ($stat == '0') {
            $this->db->where($arr);
            $this->db->delete('course_wishlist');
        }
    }

    public function remove_course_wishlist($param = array())
    {
        $this->db->where(array('cw_user_id' => $param['user_id'], 'cw_course_id' => $param['course_id']));
        $this->db->delete('course_wishlist');
        return $param['course_id'];
    }

    public function exam_question_categories($param)
    {
        $assesment_id = isset($param['assesment_id']) ? $param['assesment_id'] : false;
        if (!$assesment_id) {
            return false;
        }
        $query = "SELECT questions_subject.id as subject_id, questions_subject.qs_subject_name
                    FROM ( SELECT q_subject
                            FROM questions questions_cp
                            WHERE id IN (SELECT aq_question_id FROM assessment_questions WHERE aq_assesment_id = " . $assesment_id . ") GROUP BY q_subject
                        ) questions_cp
                    LEFT JOIN questions_subject ON questions_cp.q_subject = questions_subject.id";
        $return = $this->db->query($query)->result_array();
        return $return;
    }

    /*
     * To get the details of question categories of user generated tests
     * Created by Neethu KP
     * Created at 13/01/2017
     */
    public function uga_question_categories($param)
    {
        $assesment_id = isset($param['uga_id']) ? $param['uga_id'] : false;
        if (!$assesment_id) {
            return false;
        }
        $query = 'SELECT exam_question.q_category as category_id, questions_category.qc_category_name
                  FROM (SELECT exam_question.q_category FROM questions exam_question WHERE exam_question.id IN ( SELECT uga_question_id FROM user_generated_assesment_question WHERE uga_assesment_id =' . $assesment_id . ' ) GROUP BY exam_question.q_category) exam_question
                  LEFT JOIN questions_category ON exam_question.q_category = questions_category.id ORDER BY category_id ASC';
        return $this->db->query($query)->result_array();
    }

    /*
     * To get the details of question categories of challenge zone tests
     * Created by Neethu KP
     * Created at 13/01/2017
     */
    public function cz_question_categories($param)
    {
        $assesment_id = isset($param['cz_assessment_id']) ? $param['cz_assessment_id'] : false;
        if (!$assesment_id) {
            return false;
        }
        $query = 'SELECT exam_question.q_category as category_id, questions_category.qc_category_name
                  FROM (SELECT exam_question.q_category FROM questions exam_question WHERE exam_question.id IN ( SELECT czq_question_id FROM challenge_zone_questions WHERE czq_challenge_zone_id =' . $assesment_id . ' ) GROUP BY exam_question.q_category) exam_question
                  LEFT JOIN questions_category ON exam_question.q_category = questions_category.id ORDER BY category_id ASC';
        return $this->db->query($query)->result_array();
    }

    //End

    public function register_user_descriptive_test_attended($data)
    {
        $lecture_id = isset($data['lecture_id']) ? $data['lecture_id'] : 0;
        $user_id = isset($data['user_id']) ? $data['user_id'] : 0;
        if ($lecture_id) {
            $this->db->where('dtua_lecture_id', $lecture_id);
        }
        if ($user_id) {
            $this->db->where('dtua_user_id', $user_id);
        }
        return $this->db->count_all_results('descrptive_test_user_answered');
    }

    public function register_user_descriptive_test($data)
    {
        if ($data['id']) {
            $this->db->where('id', $data['id']);
            $this->db->update('descrptive_test_user_answered', $data);
            return $data['id'];
        } else {
            $this->db->insert('descrptive_test_user_answered', $data);
            return $this->db->insert_id();
        }
    }

    public function get_online_live_lectures($param)
    {
        $live_id = isset($param['live_id']) ? $param['live_id'] : false;
        $course_id = isset($param['course_id']) ? $param['course_id'] : false;
        $query = 'SELECT id FROM live_lectures WHERE ll_is_online = 1 AND id !="' . $live_id . '" AND ll_course_id IN (SELECT id FROM course_basics WHERE id="' . $course_id . '")';
        $result = $this->db->query($query)->result_array();
        return $result;
    }

    public function save_live_lecture_bulk($param)
    {
        $course_id = isset($param['course_id']) ? $param['course_id'] : false;
        $query = 'UPDATE live_lectures SET ll_is_online=0 WHERE ll_course_id IN (SELECT id FROM course_basics WHERE id="' . $course_id . '")';
        $this->db->query($query);
    }

    public function get_posted_user($id)
    {
        $this->db->select('*');
        $this->db->from('course_discussions');
        $this->db->where('course_discussions.id', $id);
        return $this->db->get()->result_array();
    }

    public function check_inactive_courses($param = array())
    {
        $course_id = isset($param['course_id']) ? $param['course_id'] : false;
        $status = isset($data['status']) ? $data['status'] : 0;
        $deleted = isset($data['deleted']) ? $data['deleted'] : 0;
        if (!$course_id) {
            return array();
        }
        $this->db->where('course_basics.cb_account_id', config_item('id'));
        $this->db->where('course_basics.id', $course_id);
        $this->db->where('course_basics.cb_status', '0');
        $this->db->where('course_basics.cb_deleted', '0');
        $result = $this->db->get('course_basics')->result_array();

        //echo $this->db->last_query();
        return $result;
    }

    public function save_conversion_queue($data)
    {
        if ($data['id']) {
            $this->db->where('id', $data['id']);
            $this->db->update('conversion_queue', $data);
            return $data['id'];
        } else {
            $this->db->insert('conversion_queue', $data);
            return $this->db->insert_id();
        }
    }

    public function conversion_queue($param = array())
    {
        $lecture_id = isset($param['lecture_id']) ? $param['lecture_id'] : false;
        $conversion_status = isset($param['conversion_status']) ? $param['conversion_status'] : false;

        $limit = isset($param['limit']) ? $param['limit'] : 0;
        $offset = isset($param['offset']) ? $param['offset'] : 0;
        $order_by = isset($param['order_by']) ? $param['order_by'] : 'created_date';
        $direction = isset($param['direction']) ? $param['direction'] : 'ASC';
        $count = isset($param['count']) ? $param['count'] : false;

        $query = "SELECT conversion_queue.*
                                FROM conversion_queue
                                WHERE created_date IN (SELECT max(created_date) FROM conversion_queue GROUP BY lecture_id) ";
        $where = '';
        $limit_query = '';
        $order_by = ' ORDER BY ' . $order_by . ' ' . $direction;
        if ($limit > 0) {
            $limit_query = ' LIMIT ' . $offset . ', ' . $limit;
        }

        if ($lecture_id) {
            $where .= ' AND lecture_id = ' . $lecture_id;
        }
        if ($conversion_status) {
            $where .= ' AND conversion_status = ' . $conversion_status;
        }
        if ($count) {
            $result = $this->db->query($query . $where . $order_by . $limit_query);
            return $result->num_rows();
        } else {
            return $this->db->query($query . $where . $order_by . $limit_query)->result_array();
        }
    }

    public function get_latest_course_comments_user($param = array())
    {
        $teacher_id = isset($param['teacher_id']) ? $param['teacher_id'] : false;
        $teacher_query = '';
        if ($teacher_id) {
            $this->db->select('GROUP_CONCAT(ct_course_id) as course_ids');
            $this->db->where('ct_tutor_id', $teacher_id);
            $tutor_courses = $this->db->get('course_tutors')->row_array();
            $tutor_courses = isset($tutor_courses['course_ids']) ? $tutor_courses['course_ids'] : 0;
            $teacher_query = ' AND course_discussions.course_id IN (' . $tutor_courses . ')';
        }

        $limit = isset($param['limit']) ? $param['limit'] : 2;
        $query = 'SELECT course_discussions.* , users.us_name
                    FROM course_discussions
                    JOIN (
                            SELECT parent_id, MAX(created_date) AS comment
                            FROM course_discussions WHERE 1 ' . $teacher_query . '
                            GROUP BY parent_id
                        ) latest_comment
                    ON course_discussions.parent_id = latest_comment.parent_id AND course_discussions.created_date = latest_comment.comment
                    LEFT JOIN users ON course_discussions.user_id = users.id
                    ORDER BY id DESC LIMIT 0, ' . $limit;
        $threads = $this->db->query($query)->result_array();
        //echo $this->db->last_query();die;
        return $threads;
    }

    public function languages($param = array())
    {
        $name                       = isset($param['name']) ? $param['name'] : false;
        $restrict_by_tutor_course   = isset($param['restrict_by_tutor_course']) ? $param['restrict_by_tutor_course'] : false;
        $select                     = isset($param['select'])?$param['select']:'course_language.*';
        $direction                  = isset($param['direction'])?$param['direction']:'ASC';
        $language_id                = isset($param['language_id'])?$param['language_id']:false;
        $order_by                   = isset($param['order_by'])?$param['order_by']:'id';
        $course_ids                 = array();
        if ($restrict_by_tutor_course) {
            $tutor_language_query = "SELECT GROUP_CONCAT(us_language_speaks) as us_language_speaks FROM users WHERE us_language_speaks IS NOT NULL AND us_role_id = 3 AND us_deleted='0' AND us_status='1'";
            $tutor_language = $this->db->query($tutor_language_query)->row_array();
            $tutor_language = isset($tutor_language['us_language_speaks']) ? $tutor_language['us_language_speaks'] : 0;
            if ($tutor_language) {
                $course_ids = explode(',', $tutor_language);
            }
        }

        $this->db->select($select);
        if ($language_id) {

            $dj_genres      = trim($language_id, "'");
            $lang_int_ids   = explode(",", $dj_genres);
            $this->db->where_in('id', $lang_int_ids);
        }

        if ($name) {
            $this->db->like('cl_lang_name', $name);
        }
        if (!empty($course_ids)) {
            $this->db->where_in('id', $course_ids);
        }
        $this->db->order_by($order_by, $direction);
        $result             = $this->db->get('course_language')->result_array();
        // echo $this->db->last_query();die;
        return $result;
    }

    public function course_language($param = array())
    {
        $select = isset($param['select'])?$param['select']:'*';
        
        $this->db->select($select);
        if (isset($param['language_name'])) {
            $this->db->where('cl_lang_name', $param['language_name']);
        }
        if (isset($param['language_id'])) {
            $this->db->where('id', $param['language_id']);
        }
        $return = $this->db->get('course_language')->row_array();
        return $return;
    }

    public function save_language($data)
    {
        if ($data['id']) {
            $this->db->where('id', $data['id']);
            $this->db->update('course_language', $data);
            return $data['id'];
        } else {
            $this->db->insert('course_language', $data);
            return $this->db->insert_id();
        }
    }

    public function get_course_by_userid($user_id)
    {

        $this->db->select('us_groups');
        $this->db->where('id', $user_id);
        return $this->db->get('users')->row_array();
    }

    public function subscription_details($param = array())
    {
        $select     = isset($param['select'])? $param['select']:'*';
        $id         = isset($param['id'])? $param['id']:false;
        $ids        = isset($param['ids'])? $param['ids']:false;
        $course_id  = isset($param['course_id'])? $param['course_id']:false;
        $user_id    = isset($param['user_id'])? $param['user_id']:false;
        $course_ids = isset($param['course_ids'])? $param['course_ids']:false;
        $user_ids   = isset($param['user_ids'])? $param['user_ids']:false;

        $this->db->select($select);
        $this->db->from('course_subscription');

        if($id)
        {
            $this->db->where('course_subscription.id', $id);
        }

        if($course_id && !$course_ids)
        {
            $this->db->where('course_subscription.cs_course_id', $course_id);
            
        }

        if($user_id){
            $this->db->where('course_subscription.cs_user_id', $user_id);
        }

        if($ids)
        {
            $this->db->where_in('course_subscription.id', $ids);
            
        }

        if($course_ids)
        {
            $this->db->where_in('course_subscription.cs_course_id', $course_ids);
            
        }

        if($user_ids){
            $this->db->where_in('course_subscription.cs_user_id', $user_ids);
        }

        $this->db->where('cs_account_id', config_item('id'));

        if($course_ids || $user_ids || $ids)
        {
            $result = $this->db->get()->result_array();
        }
        else
        {
            $result = $this->db->get()->row_array();
        }
        
        //echo $this->db->last_query();die('c model 3529');
        return $result;
    }
    public function course_groups($param){

        $group_id       = isset($param['group_id'])?$param['group_id']:false;
        $institute_code = isset($param['institute_code'])?$param['institute_code']:false;
        $select         = isset($param['select'])?$param['select']:'*';

        $this->db->select($select);
        $this->db->from('groups');
        if($group_id){
            $this->db->where(array('id'=>$group_id));
        }
        if($institute_code){
            $this->db->where(array('gp_institute_code'=>$institute_code));
        }
        $this->db->limit(1);
        $query= $this->db->get();
        return $query->row_array();

    }

    public function course_percentage($param = array())
    {
        /* $query = 'SELECT ROUND((SUM(lecture_log_cp.ll_percentage)/COUNT(course_lectures_cp.lecture_id))) AS percentage,  course_lectures_cp.lecture_id, lecture_log_cp.*
        FROM (SELECT id as lecture_id FROM course_lectures course_lectures_cp WHERE cl_status="1" AND cl_deleted="0" AND cl_course_id=' . $param['course_id'] . ') course_lectures_cp
        LEFT JOIN (SELECT * FROM lecture_log lecture_log_cp WHERE ll_user_id=' . $param['user_id'] . ') lecture_log_cp
        ON course_lectures_cp.lecture_id = lecture_log_cp.ll_lecture_id'; */
        $query = "SELECT SUM(ll_percentage_new)/COUNT(*) as percentage, course_basics.cb_title, course_basics.cb_image , COUNT(*) as total_lectures
        FROM  course_lectures
        LEFT JOIN course_basics ON course_lectures.cl_course_id = course_basics.id
        LEFT JOIN (SELECT ll_user_id, ll_lecture_id, ll_attempt,
                            (CASE
                                WHEN ll_attempt > 1 THEN 100
                                ELSE ll_percentage
                            END ) AS ll_percentage_new
                    FROM lecture_log lecture_log_cp
                    WHERE ll_user_id = " . $param['user_id'] . " AND ll_lecture_id IN (SELECT id FROM course_lectures WHERE cl_course_id = " . $param['course_id'] . " AND cl_deleted = '0' AND cl_status = '1')
                    ORDER BY ll_user_id ASC
                ) lecture_log_cp ON course_lectures.id = lecture_log_cp.ll_lecture_id
                WHERE course_lectures.cl_course_id = " . $param['course_id'] . " AND cl_deleted = '0' AND cl_status = '1'";
        $lecture_logs = $this->db->query($query)->row_array();
        $result = $lecture_logs['percentage'];
        return $result;
    }

    //Queries by Alex.
    public function course_overall_rating($param = array())
    {
        $course_id = isset($param['course_id']) ? $param['course_id'] : false;
        $cc_status = isset($param['cc_status']) ? $param['cc_status'] : false;
        $select = isset($param['select']) ? $param['select'] : 'cc_rating, AVG(cc_rating) AS rating, COUNT(id) AS ratings';
        $this->db->select($select);
        if ($course_id) {
            $this->db->where('cc_course_id', $course_id);
        }
        if($cc_status)
        {
            //$this->db->distinct();
            $this->db->where('cc_status', $cc_status);
        }
        $this->db->where('cc_rating >', '0');
        $this->db->where('cc_account_id', config_item('id'));
        $this->db->group_by('cc_rating');
        $result = $this->db->get('course_ratings')->result_array();
        //echo $this->db->last_query();
        //print_r($result); die;
        return $result; 
    }

    public function db_course_details($param = array())
    {
        if (isset($param['select'])) {
            $this->db->select($param['select']);
        } else {
            $this->db->select('course_basics.*,course_subscription.cs_course_validity_status,course_subscription.cs_subscription_date,course_subscription.cs_start_date,course_subscription.cs_end_date');
        }
        $this->db->from('course_basics');
        $this->db->join('course_subscription', 'course_basics.id = course_subscription.cs_course_id', 'left');
        $this->db->where('course_basics.id', $param['course_id']);
        $this->db->where('course_subscription.cs_user_id', $param['user_id']);
        $this->db->where('cs_account_id', config_item('id'));
        $result = $this->db->get()->row_array();
        $percentage = $this->course_percentage(array('user_id' => $param['user_id'], 'course_id' => $param['course_id']));
        $result['percentage'] = $percentage;
        return $result;
    }
    public function db_completed_lectures($param = array())
    {
        $query = 'SELECT COUNT(*) as count FROM lecture_log WHERE ll_user_id = ' . $param['user_id'] . ' AND ll_lecture_id IN (SELECT id FROM course_lectures WHERE cl_course_id = ' . $param['course_id'] . ' AND cl_status = "1" AND cl_deleted = "0") AND ll_attempt >=1';
        return $this->db->query($query)->row_array();
    }
    public function lecture_completed_count($param = array())
    {

        $this->db->select('*');
        $this->db->where('ll_user_id = ' . $param['u_id'] . ' AND ll_attempt >=1');
        $return = $this->db->count_all_results('lecture_log');
        return $return;
    }
    public function db_lectures($param = array())
    {
        $ses_id = array();
        if (isset($param['ses'])) {
            $ses = $param['ses'];
            foreach ($ses as $ses_i) {
                $ses_id[] = $ses_i['id'];
            }
        }
        if (!empty($ses_id)) {
            $this->db->where_in('course_lectures.cl_section_id', $ses_id);
        } else {
            $this->db->where_in('course_lectures.cl_section_id', '0');
        }
        $this->db->from('course_lectures');
        $this->db->select('course_lectures.*,live_lectures.ll_duration,assessments.id as assessment_id');
        $cond = array('course_lectures.cl_course_id' => $param['c_id'], 'course_lectures.cl_status' => '1', 'course_lectures.cl_deleted' => '0');
        $this->db->where($cond);
        $this->db->where('cl_account_id', config_item('id'));
        $this->db->join('live_lectures', 'live_lectures.ll_lecture_id = course_lectures.id', 'LEFT');
        $this->db->join('assessments', 'assessments.a_lecture_id = course_lectures.id', 'LEFT');
        return $this->db->get()->result_array();
        //echo $this->db->last_query();
    }

    public function db_get_rating($param = array())
    {
        $offset      = isset($param['offset']) ? $param['offset'] : 0;
        $limit       = isset($param['limit']) ? $param['limit'] : FALSE;
        $course_id   = isset($param['course_id']) ? $param['course_id'] : FALSE;
        $count       = isset($param['count']) ? $param['count'] : FALSE;
        if($course_id) 
        {
            $course_id = is_array($course_id) ? $course_id : array($course_id);
            $this->db->where_in('cc_course_id', $course_id);
        }
        if ($limit) 
        {
            $this->db->limit($limit, $offset);
        }

        if (isset($param['select'])) {
            $this->db->select($param['select']);
        } else {
            $this->db->select('*');
        }
        
        $cond = array('cc_status' => '1');
        $this->db->where($cond);

        //$this->db->order_by("course_reviews.created_date", "DESC");
        $this->db->where('cc_account_id', config_item('id'));
        if($count)
        {
            return $this->db->count_all_results('course_ratings');
        }
        else
        {
            return $this->db->get('course_ratings')->result_array();
        }
    }
    
    public function db_get_comments($param = array(), $parent_id)
    {
        $user = $this->auth->get_current_user_session('user');
        if (isset($param['select'])) {
            $this->db->select($param['select']);
        } else {
            $this->db->select('course_discussions.id,course_discussions.course_id,course_discussions.user_id,course_discussions.comment_title,course_discussions.comment,course_discussions.parent_id,course_discussions.comment_deleted,course_discussions.created_date,users.us_name,users.us_image');
        }
        $this->db->from('course_discussions');
        $this->db->join('users', 'course_discussions.user_id = users.id', 'left');

        $cond = array('course_discussions.comment_deleted' => '0', 'course_discussions.course_id' => $param['c_id']);
        $this->db->where($cond);
        if (isset($param['order_by'])) {
            $this->db->order_by("course_discussions.created_date", $param['order_by']);
        }
        if (isset($param['keyword']) && $param['keyword'] != '' && $parent_id == 0) {
            $this->db->like('course_discussions.comment', $param['keyword']);
        }
        if (isset($param['user_id']) && $param['user_id'] != '' && $parent_id == 0) {
            $this->db->where('users.id', $user['id']);
        }
        if (isset($parent_id)) {
            $this->db->where('course_discussions.parent_id', $parent_id);
        }
        if (isset($param['comment_id']) && $param['comment_id'] != '' && $parent_id == 0) {
            $this->db->where('course_discussions.id', $param['comment_id']);
        }
        if ($parent_id != 0) {
            $this->db->limit($param['child_limit'], '0');
        } else {
            if (isset($param['limit']) && isset($param['offset'])) {
                $this->db->limit($param['limit'], $param['offset']);
            }
        }

        $discussion_tree = array();
        $discussions = $this->db->get()->result_array();

        if (!empty($discussions)) {
            foreach ($discussions as $discussion) {
                $discussion['report_stat'] = $this->db_report_stat(array('comment_id' => $discussion['id'], 'user_id' => $user['id']));
                $discussion['children'] = $this->db_get_comments($param, $discussion['id']);
                $discussion['children_count'] = $this->db_get_chiild_count($discussion['id']);
                $discussion_tree[$discussion['id']] = $discussion;
            }
        }
        return $discussion_tree;

    }
    public function db_get_child_comments($param = array(), $parent_id)
    {
        if (isset($param['select'])) {
            $this->db->select($param['select']);
        } else {
            $this->db->select('course_discussions.id,course_discussions.course_id,course_discussions.user_id,course_discussions.comment_title,course_discussions.comment,course_discussions.parent_id,course_discussions.comment_deleted,course_discussions.created_date,users.us_name,users.us_image');
        }
        $this->db->from('course_discussions');
        $this->db->join('users', 'course_discussions.user_id = users.id', 'left');
        $cond = array('course_discussions.parent_id' => $parent_id, 'course_discussions.comment_deleted' => '0', 'course_discussions.course_id' => $param['c_id']);
        $this->db->where($cond);
        if (isset($param['limit']) && isset($param['offset'])) {
            $this->db->limit($param['limit'], $param['offset']);
        }
        if (isset($param['order_by'])) {
            $this->db->order_by("course_discussions.created_date", $param['order_by']);
        }
        return $this->db->get()->result_array();
    }
    public function db_comments_count($param = array())
    {
        $this->db->select('id');
        $cond = array('comment_deleted' => '0', 'course_id' => $param['c_id'], 'parent_id' => '0');
        $this->db->where($cond);
        return $this->db->get('course_discussions')->num_rows();
    }
    public function db_get_chiild_count($parent_id)
    {
        $this->db->select('id');
        $cond = array('comment_deleted' => '0', 'parent_id' => $parent_id);
        $this->db->where($cond);
        $return = $this->db->get('course_discussions')->num_rows();
        //echo $this->db->last_query();die;
        return $return;
    }
    public function db_section_count($param = array())
    {
        $cond = array('s_course_id' => $param['course_id'], 's_status' => '1', 's_deleted' => '0');
        $this->db->where($cond);
        $this->db->where('s_account_id', config_item('id'));
        return $this->db->get('section')->num_rows();
        //return $this->db->last_query();
    }
    public function db_delete_comment($param = array())
    {
        $this->db->where($param);
        $val = array('comment_deleted' => '1');
        $this->db->update('course_discussions', $val);
        return $this->db->affected_rows();
    }

    public function db_get_comment($param = array())
    {
        if (isset($param['select'])) {
            $this->db->select($param['select']);
        } else {
            $this->db->select('*');
        }

        if (isset($param['comment_id'])) {
            $this->db->where('id', $param['comment_id']);
        }

        return $this->db->get('course_discussions')->row_array();

    }

    public function db_report_comment($param = array())
    {
        $this->db->where('rt_child_id', $param['values']['rt_child_id']);
        $this->db->where('rt_user_id', $param['values']['rt_user_id']);
        $this->db->where('rt_status', '1');
        if ($this->db->get('course_discussion_report')->num_rows() > 0) {
            return 0;
        } else {
            $this->db->insert('course_discussion_report', $param['values']);
            return $this->db->affected_rows();
        }
    }

    public function db_report_stat($param = array())
    {
        $this->db->where('rt_child_id', $param['comment_id']);
        $this->db->where('rt_user_id', $param['user_id']);
        $this->db->where('rt_status', '1');
        return $this->db->get('course_discussion_report')->num_rows();
    }
    public function db_get_assesments($param)
    {
        isset($param['select']) ? $this->db->select($param['select']) : $this->db->select('*');
        isset($param['course_id']) ? $this->db->where('assessments.a_course_id', $param['course_id']) : '';
        $cond = array('course_lectures.cl_deleted' => '0', 'course_lectures.cl_status' => '1');
        $this->db->where($cond);
        $this->db->from('assessments');
        if (isset($param['id'])) {
            $this->db->where('assessments.a_lecture_id', $param['id']);
        }
        $this->db->join('course_lectures', 'assessments.a_lecture_id = course_lectures.id', 'left');
        if (isset($param['id'])) {
            $return = $this->db->get()->row_array();
        } else {
            $return = $this->db->get()->result_array();
        }

        return $return;
    }
    public function db_question_categories_in_assesment($param)
    {
        $cond = [];
        foreach ($param as $key => $value) {
            $cond[] = $value['id'];
        }
        $query = 'SELECT questions_category.* FROM questions_category LEFT JOIN questions ON (questions.q_category = questions_category.id) LEFT JOIN assessment_questions ON assessment_questions.aq_question_id = questions.id  WHERE questions_category.qc_status = 1 AND assessment_questions.aq_assesment_id IN (' . implode(",", $cond) . ') AND questions.q_account_id = ' . config_item('id') . ' GROUP BY questions_category.id';

        return $this->db->query($query)->result_array();
    }
    public function db_numberof_quesin_cat($cat_id)
    {
        $this->db->select('assessment_questions.id');
        $this->db->from('assessment_questions');
        $this->db->join('questions', 'assessment_questions.aq_question_id=questions.id', 'left');
        $this->db->where('questions.q_category', $cat_id);
        $this->db->where('questions.q_account_id', config_item('id'));
        return $this->db->get()->num_rows();
    }

    public function db_numberof_answered_question_cat($param = array())
    {
        $query = 'SELECT id FROM assessment_report WHERE ar_attempt_id = (SELECT id FROM assessment_attempts WHERE aa_assessment_id = ' . $param['assessment_id'] . ' AND aa_user_id = ' . $param['user_id'] . ' ORDER BY aa_attempted_date DESC LIMIT 1 ) AND ar_question_id IN(SELECT questions.id FROM assessment_questions LEFT JOIN questions ON assessment_questions.aq_question_id=questions.id WHERE questions.q_account_id = ' . config_item('id') . ' AND  questions.q_category = ' . $param['q_cat'] . ') AND ar_mark > 0';
        return $this->db->query($query)->num_rows();
    }

    public function db_assesment_name($assessment_id)
    {
        $this->db->select('course_lectures.id,course_lectures.cl_lecture_name');
        $this->db->from('course_lectures');
        $this->db->join('assessments', 'assessments.a_lecture_id = course_lectures.id', 'leftt');
        $this->db->where('assessments.id', $assessment_id);
        return $this->db->get()->row_array();
    }
    public function db_latest_attempt($param = array())
    {
        $this->db->select('id');
        $this->db->where('aa_user_id', $param['user_id']);
        $this->db->where('aa_assessment_id', $param['assessment_id']);
        $this->db->order_by('aa_attempted_date', 'DESC');
        $this->db->limit(1, 0);
        return $this->db->get('assessment_attempts')->row_array();
    }
    public function db_total_marks($param = array())
    {
        $query = 'SELECT SUM(questions.q_positive_mark) AS total_mark FROM questions LEFT JOIN assessment_questions ON questions.id = assessment_questions.aq_question_id WHERE questions.q_category = ' . $param['category_id'] . ' AND questions.id IN (SELECT assessment_questions.aq_question_id FROM assessment_questions WHERE assessment_questions.aq_assesment_id = ' . $param['assessment_id'] . ')';
        return $this->db->query($query)->row_array();
    }
    public function db_scored_marks($param = array())
    {
        $query = 'SELECT SUM(ar_mark) as scored_mark FROM assessment_report WHERE ar_question_id IN (SELECT id FROM questions WHERE q_category = ' . $param['category_id'] . ' AND id IN (SELECT aq_question_id FROM assessment_questions WHERE aq_assesment_id = ' . $param['assessment_id'] . ')) AND ar_attempt_id =' . $param['attempt_id'];
        return $this->db->query($query)->row_array();
    }
    public function db_get_assesments_detail($param = array())
    {
        if (isset($param['select'])) {
            $this->db->select($param['select']);
        } else {
            $this->db->select('course_lectures.id AS lecture_id,assessments.id,assessments.  a_course_id,assessments.a_lecture_id,assessments.   a_duration,course_lectures.cl_lecture_name');
        }

        if (isset($param['course_id'])) {
            $this->db->where('assessments.a_course_id', $param['course_id']);
        }
        $cond = array('course_lectures.cl_deleted' => '0', 'course_lectures.cl_status' => '1');
        $this->db->where($cond);
        if (isset($param['offset'])) {
            $this->db->limit($param['limit'], $param['offset']);
        } else {
            $this->db->limit($param['limit'], 0);
        }

        $this->db->from('course_lectures');
        $this->db->join('assessments', 'assessments.a_lecture_id = course_lectures.id');

        return $this->db->get()->result_array();
    }

    /*function count_test_join($param=array()){
    $user_id = isset($param['user'])?$param['user']:'';
    $course_id = isset($param['course'])?$param['course']:'';

    $this->db->select('course_basics.*,course_subscription.cs_subscription_date,course_subscription.cs_start_date,course_subscription.cs_end_date, count(course_lectures.id) as total_subscribe');
    $this->db->join('course_subscription', 'course_basics.id = course_subscription.cs_course_id AND course_subscription.cs_user_id = '.$user_id, 'left');
    $this->db->join('course_lectures', 'course_basics.id = course_lectures.cl_course_id', 'left');
    $this->db->where('course_basics.id',$course_id);

    $return = $this->db->get('course_basics')->result_array();

    echo $this->db->last_query();die;

    return $return;
    }*/
    //Queries by Alex end.

    /*
     * To get the course validity (in case of limited by date)  without user login
     * created by Neethu KP
     * created at 14/02/207
     */
    public function get_course_stat($id)
    {

        $this->db->select('*');
        $this->db->from('course_basics');
        $this->db->where('id', $id);
        $this->db->where('cb_account_id', config_item('id'));
        $this->db->where('CURDATE() > cb_validity_date');
        $result = $this->db->get();
        if ($result->num_rows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function top_rated_courses($param = array())
    {
        $user_id = isset($param['user_id']) ? $param['user_id'] : false;
        $return = array();
        if ($user_id) {
            $query = 'SELECT course_ratings_cp.*, course_basics.*, course_wishlist_cp.wishlist, course_tutors_cp.tutors '
            . ' FROM ('
            . '             SELECT cc_course_id, SUM(cc_rating)/COUNT(cc_course_id) as rating '
            . '             FROM course_ratings course_ratings_cp '
            . '             WHERE cc_course_id IN ('
            . '                                     SELECT id '
            . '                                     FROM course_basics '
            . '                                     WHERE course_basics.id NOT IN ( '
            . '                                                                     SELECT cs_course_id '
            . '                                                                     FROM course_subscription '
            . '                                                                     WHERE cs_user_id = ' . $user_id . ' '
            . '                                                                    )'
            . '                                   ) '
            . '             GROUP BY cc_course_id '
            . '             ORDER BY rating DESC LIMIT 0, 4'
            . '      ) course_ratings_cp '
            . ' LEFT JOIN course_basics ON course_ratings_cp.cc_course_id = course_basics.id '
            . ' LEFT JOIN (SELECT cw_course_id, 1 as wishlist FROM course_wishlist course_wishlist_cp WHERE cw_user_id = ' . $user_id . ' ) course_wishlist_cp ON course_ratings_cp.cc_course_id = course_wishlist_cp.cw_course_id'
            . ' LEFT JOIN (SELECT  course_tutors_cp.ct_course_id, GROUP_CONCAT(us_name) as tutors FROM course_tutors course_tutors_cp  LEFT JOIN users ON course_tutors_cp.ct_tutor_id = users.id GROUP BY ct_course_id) course_tutors_cp ON course_ratings_cp.cc_course_id = course_tutors_cp.ct_course_id'
            . ' WHERE course_basics.cb_account_id = ' . config_item('id') . ' AND course_basics.cb_deleted = "0" AND course_basics.cb_status = "1"';
            $return = $this->db->query($query)->result_array();
        }
        return $return;
    }
    
    public function db_get_question_count($id)
    {
        $this->db->select('*');
        $this->db->from('assessments');
        $this->db->where('assessments.a_lecture_id', $id);
        $this->db->join('assessment_questions', 'assessment_questions.aq_assesment_id = assessments.id', 'LEFT');
        $this->db->where('assessment_questions.aq_status', '1');
        return $this->db->get()->num_rows();
    }
    public function db_get_ll_duration($id)
    {
        $this->db->select('ll_duration');
        $this->db->where('ll_lecture_id', $id);
        return $this->db->get('live_lectures')->row_array();
    }
    public function get_course_lecture_count($param = array())
    {
        if (isset($param['select'])) {
            $this->db->select($param['select']);
        } else {
            $this->db->select('id');
        }

        if (isset($param['course_id'])) {
            $this->db->where('cl_course_id', $param['course_id']);
        }

        $this->db->where('cl_deleted', '0');
        $this->db->where('cl_account_id', config_item('id'));
       // $this->db->where('cl_status', '1'); commented for inactive course activation

        return $this->db->get('course_lectures')->num_rows();
    }

    public function get_user_assignments($user_id)
    {
        $this->db->select('course_basics.cb_title AS course_title, descrptive_tests.dt_name AS assignment, descrptive_tests.dt_last_date AS last_date, descrptive_test_user_answered.created_date AS submit_date, course_basics.id AS course_id, course_lectures.id AS lecture_id');
        $this->db->from('course_basics');
        $this->db->join('course_subscription', 'course_subscription.cs_course_id = course_basics.id', 'inner');
        $this->db->join('course_lectures', 'course_subscription.cs_course_id = course_lectures.cl_course_id', 'inner');
        $this->db->join('descrptive_tests', 'course_lectures.id = descrptive_tests.dt_lecture_id', 'inner');
        $this->db->join('descrptive_test_user_answered', 'descrptive_tests.dt_lecture_id = descrptive_test_user_answered.dtua_lecture_id', 'left');
        $this->db->where('course_subscription.cs_user_id', $user_id);
        $this->db->where('cb_account_id', config_item('id'));
        $this->db->order_by('course_basics.id', 'ASC');
        return $this->db->get()->result_array();
    }

    public function get_assessment_report($param = array())
    {
        $lecture_id = $this->db->escape($param['lecture_id']);
        $sort = isset($param['sort']) ? $this->db->escape($param['sort']) : 0;
        $search = isset($param['search']) ? $this->db->escape($param['search']) : "";
        $sortQuery = "";
        $searchQuery = "";

        if (($sort)) {
            switch ($sort) {
                case 1:
                    $sortQuery = " ORDER BY lecture_log_cp.us_name ASC ";
                    break;
                case 2:
                    $sortQuery = " ORDER BY lecture_log_cp.ll_marks DESC ";
                    break;
                case 3:
                    $sortQuery = " ORDER BY lecture_log_cp.ll_marks ASC ";
                    break;
                case 4:
                    $sortQuery = " ORDER BY pass DESC ";
                    break;
                case 5:
                    $sortQuery = " ORDER BY pass ASC ";
                    break;
            }
        }

        if (($search)) {
            $searchQuery = " WHERE lecture_log_cp.us_name LIKE " . $search . " ";
        }

        $query = $this->db->query("SELECT lecture_log_cp.*, assessment_attempts_cp.*, (lecture_log_cp.ll_marks - assessment_attempts_cp.a_pass_percentage) AS pass
                    FROM (SELECT users.us_name, users.us_image, ll_user_id, ll_lecture_id, ll_marks
                            FROM lecture_log lecture_log_cp
                            LEFT JOIN users ON lecture_log_cp.ll_user_id = users.id
                            WHERE ll_lecture_id = " . $lecture_id . "
                         ) lecture_log_cp
                    LEFT JOIN (SELECT assessment_attempts.id as attempt_id, assessments.a_lecture_id, assessment_attempts.*, assessments.a_pass_percentage
                                FROM (SELECT aa_assessment_id, aa_user_id, max(aa_attempted_date) as aa_attempted_date
                                        FROM assessment_attempts assessment_attempts_cp
                                        GROUP BY CONCAT(aa_assessment_id, '_', aa_user_id)
                                     ) assessment_attempts_cp
                                LEFT JOIN assessment_attempts ON assessment_attempts.aa_attempted_date = assessment_attempts_cp.aa_attempted_date AND assessment_attempts.aa_assessment_id = assessment_attempts_cp.aa_assessment_id AND assessment_attempts.aa_user_id = assessment_attempts_cp.aa_user_id
                                LEFT JOIN assessments ON assessment_attempts.aa_assessment_id = assessments.id
                              ) assessment_attempts_cp
                    ON lecture_log_cp.ll_user_id = assessment_attempts_cp.aa_user_id AND lecture_log_cp.ll_lecture_id = assessment_attempts_cp.a_lecture_id" . $searchQuery . $sortQuery);
        return $query->result_array();
    }

    //Written by Alex.

    public function lecture_details($param = array())
    {
        if (isset($param['select'])) {
            $this->db->select($param['select']);
        } else {
            $this->db->select('id,cl_course_id,cl_lecture_type');
        }

        if (isset($param['course_id'])) {
            $this->db->where('cl_course_id', $param['course_id']);
        }

        if (isset($param['lecture_id'])) {
            $this->db->where('id', $param['lecture_id']);
        }
        $this->db->where('cl_account_id', config_item('id'));
        $result = $this->db->get('course_lectures')->row_array();

        return $result;
    }

    public function simple_courses($param = array())
    {
        $select = isset($param['select']) ? $param['select'] : false;
        $category_id = isset($param['category_id']) ? $param['category_id'] : false;
        $deleted = isset($param['deleted']) ? $param['deleted'] : false;
        $status = isset($param['status']) ? $param['status'] : false;

        if ($select) {
            $this->db->select($param['select']);
        } else {
            $this->db->select('id,cb_title');
        }

        if ($category_id) {
            $this->db->where('cb_category', $category_id);
        }

        if ($deleted) {
            $this->db->where('cb_deleted', $deleted);
        } else {
            $this->db->where('cb_deleted', '0');
        }

        if ($status) {
            $this->db->where('cb_status', $status);
        } else {
            $this->db->where('cb_status', '1');
        }

        $this->db->order_by('cb_title', 'ASC');
        $this->db->where('cb_account_id', config_item('id'));

        $result = $this->db->get('course_basics')->result_array();
        //echo $this->db->last_query();die;
        return $result;

    }

    public function get_attempt_mark($param = array())
    {
        $query = 'SELECT SUM(ar_mark) AS mark FROM assessment_report WHERE ar_attempt_id = "' . $param['attempt_id'] . '"';
        $result = $this->db->query($query)->row_array();
        return $result;
    }

    public function update_attempt_mark($param = array())
    {
        $this->db->where('id', $param['attempt_id']);
        $result = $this->db->update('assessment_attempts', array('aa_mark_scored' => $param['mark']));
        return $result;
    }
    
    public function update_attempt_completion($param = array())
    {
        $this->db->where('id', $param['attempt_id']);
        $result = $this->db->update('assessment_attempts', array('aa_completed'=> $param['aa_completed'],'aa_assessment_detail'=>$param['aa_assessment_detail']));
        return $result;
    }
    
    public function get_question($param = array())
    {
        $this->db->select('id,q_options,q_question,q_answer,q_positive_mark,q_negative_mark,q_type');
        if (isset($param['question_id'])) {
            $this->db->where('id', $param['question_id']);
        }
        $result = $this->db->get('questions')->row_array();
        return $result;
    }

    //End of written by Alex.

    public function conversion_queue_by_id($id = 0)
    {
        return $this->db->get_where('conversion_queue', array('id' => $id))->row_array();
    }

    //Online test by santhosh

    public function attempt($param = array())
    {
        $select         = isset($param['select']) ? $param['select'] : false;
        $id             = isset($param['id']) ? $param['id'] : 0;
        $direction      = isset($param['direction']) ? $param['direction'] :'';
        $order_by       = isset($param['order_by']) ? $param['order_by'] : 'id';
        $limit          = isset($param['limit']) ? $param['limit'] : false;
        $assesment_id   = isset($param['assesment_id']) ? $param['assesment_id'] : false;
        $lecture_id     = isset($param['lecture_id']) ? $param['lecture_id'] : false;
        $course_id      = isset($param['course_id']) ? $param['course_id'] : false;
        $user_id        = isset($param['user_id']) ? $param['user_id'] : false;
       
        if ($select) {
            $this->db->select($select);
        }
        if ($id != 0) {
            $this->db->where('id', $id);
        }
        if($user_id){
            $this->db->where('aa_user_id', $user_id);
        }
        if($course_id){
            $this->db->where('aa_course_id', $course_id);
        }
        if($lecture_id){
            $this->db->where('aa_lecture_id', $lecture_id);
        }
        if($assesment_id){
            $this->db->where('aa_assessment_id', $assesment_id);
        }
        if($direction!=''){
            $this->db->order_by($order_by, $direction);
        }
        if($limit){
            $this->db->limit($limit);
        }
        $return = $this->db->get('assessment_attempts')->row_array();
        //echo $this->db->last_query();die;
        return $return;
    }

    public function assessment_question($param = array())
    {
        $assesment_id = isset($param['assesment_id']) ? $param['assesment_id'] : 0;
        $question_id = isset($param['question_id']) ? $param['question_id'] : 0;
        $query = 'SELECT aq_positive_mark, aq_negative_mark FROM assessment_questions WHERE aq_assesment_id = ' . $assesment_id . ' AND aq_question_id = ' . $question_id . '';
        $result = $this->db->query($query)->row_array();
        return $result;
    }

    public function assesment_report($param = array())
    {
        $attempt_id = isset($param['attempt_id']) ? $param['attempt_id'] : 0;
        $question_id = isset($param['question_id']) ? $param['question_id'] : 0;

        $query = 'SELECT * FROM assessment_report WHERE ar_attempt_id = ' . $attempt_id . ' AND ar_question_id =  ' . $question_id;
        $result = $this->db->query($query)->row_array();

        return $result;
    }

    public function assesment_total_mark($param = array())
    {
        $attempt_id = isset($param['attempt_id']) ? $param['attempt_id'] : 0;
        $query = 'SELECT SUM(ar_mark) as total_mark FROM assessment_report WHERE ar_attempt_id = ' . $attempt_id;
        $result = $this->db->query($query)->row_array();
        return (isset($result['total_mark']) && $result['total_mark'] != '') ? $result['total_mark'] : 0;
    }
    public function course_permission($param)
    {

        $this->db->select('ct_course_id');
        $this->db->from('course_tutors');
        $this->db->where(array('ct_tutor_id' => $param['current_logged_user']));
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_restricted_access_faculties()
    {
        
        $query = "SELECT users_cp.*, roles.rl_name
                    FROM ( SELECT id, us_name, us_role_id FROM users users_cp  WHERE us_role_id != 2 AND us_account_id=".config_item('id')." AND us_deleted = '0') users_cp 
                    LEFT JOIN roles ON users_cp.us_role_id = roles.id 
                    WHERE rl_full_course='0'";
        $return = $this->db->query($query)->result_array();
        //echo $this->db->last_query();die;
        return $return;
    }

    function check_course_assign($category_id){
        $query = "SELECT GROUP_CONCAT(`cb_title` ORDER BY `cb_title` SEPARATOR ', ') as `cb_title` FROM course_basics WHERE find_in_set($category_id,`cb_category`)";
        return $this->db->query($query)->row_array();
    }
    
    public function remove_lectures($param){
        course_lecture_activity_save($param);
        $section_id = isset($param['section_id']) ? $param['section_id'] : false;
        $course_id  = isset($param['course_id']) ? $param['course_id'] : false;
        $lecture_id = isset($param['lecture_id'])?$param['lecture_id']:false;
        course_lecture_activity_save($param);
        if($section_id){
            $this->db->where('cl_section_id',$section_id);
        }
        if($course_id){
            $this->db->where('cl_course_id',$course_id);
        }
        if($lecture_id){
            $this->db->where('id',$lecture_id);
        }

        // $data = array();
        // $data['cl_deleted'] = '1';
        // $this->db->update('course_lectures',$data);
        $result = $this->db->delete('course_lectures');
        return true;
    }
    public function remove_sections($param){

        $section_id = isset($param['section_id']) ? $param['section_id'] : false;
        $course_id  = isset($param['course_id']) ? $param['course_id'] : false;
        course_lecture_activity_save($param);
        if($section_id){
            $this->db->where('id',$section_id);
        }
        if($course_id){
            $this->db->where('s_course_id',$course_id);
        }
        $this->db->where('s_account_id', config_item('id'));
        $result = $this->db->delete('section');
        return $result;
    }

    public function all_certificates($param){

        $is_active = isset($param['active'])?$param['active']:false;

        $this->db->select('id,cm_filename,cm_image,cm_is_active');
        $this->db->from('certificate_manage');
        if($is_active){
            $this->db->where('cm_is_active','1');
        }
        $this->db->where('cm_account_id',config_item('id'));
        $this->db->order_by('id', 'DESC');
        $result = $this->db->get();
        return $result->result_array();
    }

    public function get_descriptive_test_report($param)
    {
        $this->db->select('users.us_name, users.us_image,users.us_email, descrptive_test_user_answered.dtua_user_id, descrptive_test_user_answered.dtua_lecture_id,DATE_FORMAT(descrptive_test_user_answered.updated_date, "%d-%m-%Y") as updated_date, descrptive_test_user_answered.mark, descrptive_test_user_answered.dtua_grade,descrptive_test_user_answered.dtua_assigned_to,descrptive_test_user_answered.dtua_evaluated,descrptive_test_user_answered.dtua_comments,DATE_FORMAT(descrptive_test_user_answered.created_date, "%d-%m-%Y") as created_date,descrptive_tests.dt_name,descrptive_tests.dt_uploded_files,descrptive_tests.dt_description,descrptive_tests.dt_last_date,descrptive_tests.dt_total_mark, descrptive_tests.dt_instruction');
        $this->db->where('descrptive_test_user_answered.dtua_lecture_id', $param['id']);
        $this->db->where('descrptive_test_user_answered.id', $param['attempt_id']);
        //$this->db->where('descrptive_test_user_answered.status', '1');
        $this->db->from('descrptive_test_user_answered');
        $this->db->join('users', 'users.id = descrptive_test_user_answered.dtua_user_id');
        $this->db->join('descrptive_tests', 'descrptive_tests.dt_lecture_id = descrptive_test_user_answered.dtua_lecture_id');
        $this->db->order_by('updated_date', 'desc');
        $this->db->where('us_account_id', config_item('id'));
        return $this->db->get()->result_array();
    }

    public function get_descriptive_test_comments($param)
    {
        $this->db->select('comment,file');
        $this->db->where('descrptive_test_answers.da_attempt_id',$param['attempt_id']);
        $this->db->from('descrptive_test_answers');
        $this->db->order_by('updated_date', 'desc');
        return $this->db->get()->result_array();
    }
    
    public function count_ratings($param){

        $course_id = isset($param['course_id'])?$param['course_id']:false;
        $rating    = isset($param['rating_order'])?$param['rating_order']:false;

        $this->db->from('course_ratings');
        if($rating){
            if($rating=='like'){
                $this->db->where('cc_rating>=3');
            }else{
                $this->db->where('cc_rating<3');
            }
        }
        $this->db->where('cc_course_id',$course_id);
        $this->db->where('cc_account_id', config_item('id'));
        $result = $this->db->get();
        $count  = $result->num_rows();
    //    echo $this->db->last_query();exit;
        return $count; 
    }
    function course_institute_ratings($param){

        $course_id  = isset($param['course_id'])?$param['course_id']:false;
        $select     = isset($param['select'])?$param['select']:false;

        $this->db->select($select);
        $this->db->from('course_perfomance');
        if($course_id){
            $this->db->where('cp_course_id',$course_id);
        }
        $result = $this->db->get();
                //    echo $this->db->last_query();exit;
        return $result->result_array(); 

    }
    
    function user_lectures($param = array())
    {
        $user_id    = isset($param['user_id'])?$param['user_id']:false;
        $course_id  = isset($param['course_id'])?$param['course_id']:false;

        $this->db->select('*');
        $this->db->where('ll_user_id',$user_id);
        $this->db->where('ll_course_id',$course_id);

        $result     = $this->db->get('lecture_log')->result_array();

        return $result;
    }

    function assesment_details($param = array())
    {
        $course_id  = isset($param['course_id'])?$param['course_id']:0;
        $lecture_id = isset($param['lecture_id'])?$param['lecture_id']:false;
        $this->db->select('id,a_course_id,a_lecture_id,a_instructions,a_questions,a_to_availability,a_duration,a_mark,a_to,a_to_time,a_from_time,a_from,a_from_availability,a_to_availability');
        $this->db->where('a_course_id',$course_id);

        if($lecture_id){
            $this->db->where('a_lecture_id',$lecture_id);
            $result     = $this->db->get('assessments')->row_array();
        }else{
            $result     = $this->db->get('assessments')->result_array();
        }

        return $result;
    }

    function assignment_details($param = array())
    {
        $course_id  = isset($param['course_id'])?$param['course_id']:0;
        $lecture_id = isset($param['lecture_id'])?$param['lecture_id']:0;

        $this->db->select('*');
        $this->db->where('dt_course_id',$course_id);
        
        if($lecture_id)
        {
            $this->db->where('dt_lecture_id',$lecture_id);
            $result     = $this->db->get('descrptive_tests')->row_array();
        }
        else
        {
            $result     = $this->db->get('descrptive_tests')->result_array();
        }

        return $result;
    }

    function assesment_attempt_details($param = array())
    {
        $user_id        = isset($param['user_id'])?$param['user_id']:0;
        $course_id      = isset($param['course_id'])?$param['course_id']:0;
        $lecture_id     = isset($param['lecture_id'])?$param['lecture_id']:0;

        $this->db->select('*');
        $this->db->where('aa_user_id',$user_id);
        $this->db->where('aa_course_id',$course_id);
        $this->db->where('aa_latest','1');
        if(isset($param['completed'])){
            $this->db->where('aa_completed','1');
        }

        if($lecture_id){
            $this->db->where('aa_lecture_id',$lecture_id);
            $result     = $this->db->get('assessment_attempts')->row_array();
        }else{
            $result     = $this->db->get('assessment_attempts')->result_array();
        }

        return $result;
    }

    function assignment_attempt_details($param = array())
    {
        $user_id    = isset($param['user_id'])?$param['user_id']:false;
        $course_id  = isset($param['course_id'])?$param['course_id']:false;
        $lecture_id = isset($param['lecture_id'])?$param['lecture_id']:false;
        $select     = isset($param['select'])?$param['select']:'*';
        $limit      = isset($param['limit']) ? $param['limit'] : false;

        $this->db->select($select);
        if($user_id){
            $this->db->where('dtua_user_id',$user_id);
        }
        if($course_id){
            $this->db->where('dtua_course_id',$course_id);
        }
        if($lecture_id){
            $this->db->where('dtua_lecture_id',$lecture_id);
        }
        if($limit){
            $this->db->limit($limit);
        }

        if($lecture_id){
            $result = $this->db->get('descrptive_test_user_answered')->row_array();
        }
        else
        {
            $result = $this->db->get('descrptive_test_user_answered')->result_array();
        }
        // echo $this->db->last_query();die;
        return $result;
    }

    function lecute_override($param = array())
    {
        $course_id      = isset($param['course_id'])?$param['course_id']:false;
        $lecture_id     = isset($param['lecture_id'])?$param['lecture_id']:false;
        $batch_query    = isset($param['batch_query'])?$param['batch_query']:false;
        $lecture_type   = isset($param['lecture_type'])?$param['lecture_type']:false;
        $source         = isset($param['source'])?$param['source']:'student';

        $query          = 'SELECT * FROM lecture_override';

        if($course_id)
        {
            $query     .= ' WHERE lo_course_id="'.$course_id.'"';
        }

        if($lecture_id)
        {
            $query     .= ' AND lo_lecture_id="'.$lecture_id.'"';
        }

        if($lecture_type)
        {
            $query     .= ' AND lo_lecture_type="'.$lecture_type.'" ';
        }
        
        if($batch_query){
            $query      .= ' AND ('.$batch_query.')';
        }

        if($batch_query == '' && $source != 'course'){
            $result         = array();
        }else{
            if($lecture_id){
                $result         = $this->db->query($query)->row_array();
            }else{
                $result         = $this->db->query($query)->result_array();
            }
        }

        return $result;
    }
    function grade()
    {
        $this->db->select('id,gr_name,gr_range_from,gr_range_to');
        $this->db->where("(gr_account = '0' OR gr_account='".config_item('id')."')");
        $result     = $this->db->get('grades')->result_array();
        return $result;
    }
    function save_survey_question_order($orders)
    {
        $orders_chunks  = array_chunk($orders, 50);
        $sort_order     = 1;
        if(!empty($orders_chunks))
        {
            foreach($orders_chunks as $orders)
            {
                $this->db->trans_start();
                foreach($orders as $q_id)
                {
                    $this->db->query("UPDATE survey_questions SET sq_order = '".$sort_order."' WHERE id = '".$q_id."';");
                    $sort_order++;
                }
                $this->db->trans_complete(); 
            }
        }
    }
    public function remove_assessments($param){

        $course_id   = isset($param['course_id']) ? $param['course_id'] : false;
        $lecture_id  = isset($param['lecture_id'])?$param['lecture_id']:false;
        $lecture_ids = isset($param['lecture_ids'])?$param['lecture_ids']:false;

        if($course_id){

            $this->db->where('a_course_id', $course_id);
        }
        if($lecture_id){

            $this->db->where('a_lecture_id', $lecture_id);
        }
        if($lecture_ids){

            $this->db->where_in('a_lecture_id', $lecture_ids);
        }
        
        $result = $this->db->delete('assessments');
        return $result;
    }
    public function remove_assessment_questions($param){

        $assesmemt_id   = isset($param['assesmemt_id']) ? $param['assesmemt_id'] : false;
        $lecture_id     = isset($param['lecture_id'])?$param['lecture_id']:false;
        $lecture_ids    = isset($param['lecture_ids'])?$param['lecture_ids']:false;
        $question_id    = isset($param['question_id'])?$param['question_id']:false;

        if($assesment_id){

            $this->db->where('aq_assesment_id', $assesment_id);
        }
        if($question_id){

            $this->db->where('aq_question_id', $question_id);
        }
        if($lecture_id){

            $this->db->where('aq_lecture_id', $lecture_id);
        }
        if($lecture_ids){

            $this->db->where_in('aq_lecture_id', $lecture_ids);
        }
        
        $result = $this->db->delete('assessments');
    }
    function course_topic_progress($param = array())
    {
        $user_id    = isset($param['user_id'])?$param['user_id']:0;
        $course_id  = isset($param['course_id'])?$param['course_id']:0;
        $this->db->select('course_subscription.id,course_subscription.cs_user_id,course_subscription.cs_course_id,course_subscription.cs_topic_progress,course_subscription.cs_invalidate_topic');
        $this->db->where('cs_user_id',$user_id);
        $this->db->where('cs_course_id',$course_id);
        $this->db->where('cs_account_id', config_item('id'));
        $result     = $this->db->get('course_subscription')->row_array();
        return $result;
    }
    function course_subject_progress($param = array())
    {
        $user_id    = isset($param['user_id'])?$param['user_id']:0;
        $course_id  = isset($param['course_id'])?$param['course_id']:0;
        $this->db->select('subject_report.sr_subject_id,subject_report.sr_percentage as percentage, questions_subject.qs_subject_name as name, ');
        $this->db->join('questions_subject', 'questions_subject.id = subject_report.sr_subject_id');
        $this->db->where('subject_report.sr_user_id',$user_id);
        $this->db->where('subject_report.sr_course_id',$course_id);
        $result     = $this->db->get('subject_report')->result_array();
        return $result;
    }
    public function remove_descrptive_answered($param){

        $course_id      = isset($param['course_id']) ? $param['course_id'] : false;
        $lecture_id     = isset($param['lecture_id'])?$param['lecture_id']:false;
        $lecture_ids    = isset($param['lecture_ids'])?$param['lecture_ids']:false;

        $user_id        = isset($param['user_id'])?$param['user_id']:false;
        $user_ids       = isset($param['user_ids'])?$param['user_ids']:false;

        if($user_id){

            $this->db->where('dtua_user_id', $user_id);
        }
        if($user_ids){

            $this->db->where_in('dtua_user_id', $user_ids);
        }

        if($course_id){

            $this->db->where('dtua_course_id', $course_id);
        }
        if($lecture_id){

            $this->db->where('dtua_lecture_id', $lecture_id);
        }
        if($lecture_ids){

            $this->db->where_in('dtua_lecture_id', $lecture_ids);
        }
        
        $result = $this->db->delete('descrptive_test_user_answered');
        return $result;
    }
    public function remove_descrptive_tests($param){

        $course_id      = isset($param['course_id']) ? $param['course_id'] : false;
        $lecture_id     = isset($param['lecture_id'])?$param['lecture_id']:false;
        $lecture_ids    = isset($param['lecture_ids'])?$param['lecture_ids']:false;
        
        if($course_id){

            $this->db->where('dt_course_id', $course_id);
        }
        if($lecture_id){

            $this->db->where('dt_lecture_id', $lecture_id);
        }
        if($lecture_ids){

            $this->db->where_in('dt_lecture_id', $lecture_ids);
        }
        
        $result = $this->db->delete('descrptive_tests');
        return $result;
    }

    public function assesment_reports($param = array())
    {
        $attempt_id     = isset($param['attempt_id']) ? $param['attempt_id'] : false;
        $query          = 'SELECT * FROM assessment_report WHERE ar_attempt_id = ' . $attempt_id;
        $result         = $this->db->query($query)->result_array();
        return $result;
    }

    public function assessment_questions($param = array())
    {
        $assesment_id = isset($param['assesment_id']) ? $param['assesment_id'] : 0;
        $query = 'SELECT * FROM assessment_questions WHERE aq_assesment_id = ' . $assesment_id;
        $result = $this->db->query($query)->result_array();
        return $result;
    }

    public function user_subject($param = array())
    {
        $user_id    = isset($param['user_id'])?$param['user_id']:0;
        $course_id  = isset($param['course_id'])?$param['course_id']:0;
        $subjects   = isset($param['subjects'])?$param['subjects']:array();

        $this->db->select('*');
        $this->db->where('sr_user_id',$user_id);
        $this->db->where('sr_course_id',$course_id);
        $this->db->where_in('sr_subject_id',$subjects);

        $result     = $this->db->get('subject_report')->result_array();

        return $result;
    }

    public function save_user_subject($param = array())
    {
        $update     = isset($param['id'])?$param['id']:false;

        if($update)
        {
            $this->db->where('id',$update);
            $this->db->update('subject_report',$param);
            $result = $update;
        }
        else
        {
            $this->db->insert('subject_report',$param);
            $result = $this->db->insert_id();
        }
        return $result;
    }

    function insert_questions_bulk($save_questions)
    {
        $result            = array();
        $questions_chunks  = array_chunk($save_questions, 100);
        if(!empty($questions_chunks))
        {
            foreach($questions_chunks as $questions)
            {
                $this->db->trans_start();
                foreach($questions as $question)
                {
                    $this->db->insert('questions', $question);
                    $question_id      = $this->db->insert_id();
                    $randam           = array();
                    $randam['q_code'] = substr(str_shuffle(str_repeat("123456789", 2)), 0, 2).$question_id;
                    $this->db->where('id', $question_id);
                    $this->db->update('questions', $randam);
                    $result[]         = $question_id;
                }
                $this->db->trans_complete(); 
            }
        }
        return $result;
    }

    public function save_assesment_questions_bulk($assessment_questions)
    {

        $result            = array();
        //$questions_chunks  = array_chunk($assessment_questions, 100);
        if(!empty($assessment_questions))
        {
            // foreach($questions_chunks as $questions)
            // {
            //   //  $this->db->trans_start();
            //     foreach($questions as $question)
            //     {
                    $this->db->insert_batch('assessment_questions', $assessment_questions);
                   // $result[] = $this->db->insert_id();
            //     }
            //    // $this->db->trans_complete(); 
            // }
        }
        return $result;
        
    }

    public function insert_options_bulk($question_options)
    {
        $result            = array();
        if(!empty($question_options))
        {
            foreach($question_options as $unique_hash => $options)
            {
                $this->db->trans_start();
                foreach($options as $option)
                {
                    $this->db->insert('questions_options', $option);
                   // echo $this->db->last_query();exit;
                    $result[$unique_hash][] = $this->db->insert_id();
                }
                $this->db->trans_complete(); 
            }
        }
        return $result;
        
    }

    public function update_assessment_lecture($param = array())
    {
        $course_id      = isset($param['aa_course_id'])?$param['aa_course_id']:0;
        $lecture_id     = isset($param['aa_lecture_id'])?$param['aa_lecture_id']:0;
        $user_id        = isset($param['aa_user_id'])?$param['aa_user_id']:0;

        $this->db->where('aa_course_id',$course_id);
        $this->db->where('aa_lecture_id',$lecture_id);
        $this->db->where('aa_user_id',$user_id);
        $this->db->update('assessment_attempts',$param);
    }

    public function update_assignment_lecture($param = array())
    {
        $course_id      = isset($param['dtua_course_id'])?$param['dtua_course_id']:0;
        $lecture_id     = isset($param['dtua_lecture_id'])?$param['dtua_lecture_id']:0;
        $user_id        = isset($param['dtua_user_id'])?$param['dtua_user_id']:0;

        $this->db->where('dtua_course_id',$course_id);
        $this->db->where('dtua_lecture_id',$lecture_id);
        $this->db->where('dtua_user_id',$user_id);
        $this->db->update('descrptive_test_user_answered',$param);
    }
    public function descriptive_test($param){

        $course_id  = isset($param['course_id'])?$param['course_id']:false;
        $lecture_id = isset($param['lecture_id'])?$param['lecture_id']:false;
        $limit      = isset($param['limit']) ? $param['limit'] : false;
        $select     = isset($param['select']) ? $param['select'] : '*';

        if($course_id){
            $this->db->where('dt_course_id',$course_id);
        }
        if($lecture_id){
            $this->db->where('dt_lecture_id	',$lecture_id);
        }
        if($limit){
            $this->db->limit($limit);
        }
        $this->db->from('descrptive_tests');
        $result = $this->db->get()->row_array();
        return $result;
    }
    public function save_survey_response($data){
        
        $this->db->insert_batch('survey_user_response', $data); 
        return true;
    }
    public function get_live_event($param){

        $select             = isset($param['select'])?$param['select']:'*';
        $id                 = isset($param['live_id'])?$param['live_id']:false;
        $count              = isset($param['count'])?$param['count']:false;
        $date               = isset($param['date'])?$param['date']:false;
        $preference         = isset($param['preference'])?$param['preference']:false;
        $preference_type    = isset($param['preference_type'])?$param['preference_type']:false;
        $join               = isset($param['join'])?$param['join']:false;

        $this->db->select($select);
        if($id){

            $this->db->where('id',$id);
        }
        if($date){
            $where = "ll_date >= CURDATE()";
            $this->db->where($where);
        }
        if($preference){

            switch($preference_type){

                case 'day':
                    $where = "ll_date = DATE_ADD(CURDATE(),INTERVAL 1 DAY)";
                    $this->db->where($where);
                break;
                case 'week':
                     $where = "ll_date = DATE_ADD(CURDATE(),INTERVAL 7 DAY)";
                    $this->db->where($where);
                break;
                case 'month':
                    $where = "ll_date = DATE_ADD(CURDATE(),INTERVAL 31 DAY)";
                    $this->db->where($where);
                break;

            }
        }
        if($join){
            $this->db->join('course_lectures as b','b.id = a.ll_lecture_id');
        }
        $result = $this->db->get('live_lectures as a');
        // echo $this->db->last_query();exit;
        if($count == '1'){

            return $result->row_array();
        }else{
            return $result->result_array();
        }
            
    }

    public function course_for_consolidation()
    {
        return $this->db->query('SELECT id, cb_title, cb_access_validity,cb_status, cb_validity, cb_validity_date FROM course_basics')->result_array();    
    }

    public function save_consolidation($consolidations)
    {
        $this->db->insert_batch('course_consolidated_report', $consolidations); 
    }

    public function consolidated_report($param = array())
    {
        $select         = isset($param['select'])?$param['select']:'*';
        $institute_id   = isset($param['institute_id'])?$param['institute_id']:false;
        $course_id      = isset($param['course_id'])?$param['course_id']:false;
        $limit          = isset($param['limit'])?$param['limit']:false;
        $tutor_id       = isset($param['tutor_id']) ? $param['tutor_id'] : false;
        $tutor_courses  = 0;
        if ($tutor_id) {
            $this->db->select('GROUP_CONCAT(ct_course_id) as course_ids');
            $this->db->from('course_tutors');
            $this->db->where('ct_tutor_id', $tutor_id);
            $tutor_courses = $this->db->get()->row_array();
            $tutor_courses = isset($tutor_courses['course_ids']) ? $tutor_courses['course_ids'] : 0;
        }
        
        $this->db->select($select);
        $this->db->where("(ccr_account_id = '0' OR ccr_account_id='".config_item('id')."')");
        if($institute_id){
            $this->db->where('ccr_institute_id',$institute_id);
        }
        if($course_id){
            $this->db->where('ccr_course_id',$course_id);
        }
        if ($tutor_id) {
            $this->db->where_in('ccr_course_id', explode(',', $tutor_courses));
        }
        $this->db->from('course_consolidated_report');
        $result = $this->db->get();
        
        if($limit){
            return  $result->row_array();
        }else{
            return  $result->result_array();
        }
    }

    public function get_certificate($param = array())
    {
        $select = (isset($param['select']))?$param['select']:'certificate_manage.*';
        $id     = (isset($param['id']))?$param['id']:false;
        $this->db->select($select);
        $this->db->from('certificate_manage');
        if($id)
        {
            $this->db->where('id',$id);
        }
        $this->db->where('cm_account_id',config_item('id'));
        $result = $this->db->get();
        //echo $this->db->last_query();exit;
        return $result->row_array();
    }

    public function survey_questions_count($param = array())
    {
        $count      = 0;
        $survey_id  = isset($param['survey_id']) ? $param['survey_id'] : 0;
        if ($survey_id) 
        {
            $query = 'SELECT COUNT(id) as total_survey_questions FROM survey_questions WHERE sq_survey_id="'.$survey_id.'"';
            $count = $this->db->query($query)->row_array();
            $count = isset($count['total_survey_questions'])?$count['total_survey_questions']:0;
        }
        return $count;
    }

    public function total_enrolled_students()
    {
        $this->db->select("SUM(ccr_total_enrolled) as total_enrolled_students");
        $total_enrolled_students = $this->db->get('course_consolidated_report')->row_array();
        //echo $this->db->last_query();exit;
        return $total_enrolled_students['total_enrolled_students'];
    }
    public function check_survey_response_exist($param = array()){

        $select         = isset($param['select'])?$param['select']:'*';
        $survey_id      = isset($param['survey_id'])?$param['survey_id']:false;
        $lecture_id     = isset($param['lecture_id'])?$param['lecture_id']:false;
        $user_id        = isset($param['user_id'])?$param['user_id']:false;

        if($survey_id){
            $this->db->where('sur_survey_id',$survey_id);
        }
        if($lecture_id){
            $this->db->where('sur_lecture_id',$lecture_id);
        }
        if($user_id){
            $this->db->where('sur_user_id',$user_id);
        }
        $this->db->select($select);
        $this->db->from('survey_user_response');

        $result = $this->db->get();
        return $result->num_rows();        
    }

    public function save_docs_template($param = array())
    {
        $update     = isset($param['id'])?$param['id']:false;

        if($update)
        {
            $this->db->where('id',$update);
            $this->db->update('doc_unique_templates',$param);
            $result = $update;
        }
        else
        {
            $this->db->insert('doc_unique_templates',$param);
            $result = $this->db->insert_id();
        }
        return $result;
    }

    public function get_docs_template($param = array())
    {
        $query = 'select * FROM doc_unique_templates WHERE 1 ';
        if (isset($param['id'])) 
        {
           $query .= ' AND id = "' . $param['id']. '"'; 
        }
        if (isset($param['dut_date']) && $param['dut_date'] != '') 
        {
            $query .= ' AND dut_date = "' . $param['dut_date']. '"'; 
        }
        if (isset($param['dut_name']) && $param['dut_name'] != '') 
        {
            // $query .= ' AND FIND_IN_SET("' . $param['dut_name']. '",dut_name)';  
            $query .= ' AND (CONCAT(",", dut_name, ",") LIKE ("%' . $param['dut_name']. '%"))';  
        }

        $query .= ' AND dut_account_id = "' . config_item('id'). '"'; 
      
        $result = $this->db->query($query)->row_array();
        // echo $this->db->last_query();die;
        return $result;
    }

    public function delete_courses($ids=array())
    {
        for($i = 0; $i < count($ids); $i++)
        {
            $this->check_course_delete($ids[$i]);
        }

        $this->db->where_in('id', $ids);
        $this->db->where('cb_account_id', config_item('id'));
        $this->db->delete('course_basics');
    }

    public function get_active_academic_year()
    {
        $this->db->where('ay_active', '1');
        $this->db->select('*');
        $this->db->from('academic_year');
        $result = $this->db->get()->row_array();
        return $result;
    }
    
    public function academic_years($param=array())
    {
        $select         = isset($param['select'])?$param['select']:'*';
        $code           = isset($param['code'])?$param['code']:false;
        $id             = isset($param['id'])?$param['id']:false;
        $status         = isset($param['status'])?$param['status']:false;
        
        if($status)
        {
            $this->db->where('ay_active', $status);
        }

        if($id)
        {
            $this->db->where('id', $id);
        }

        if($code)
        {
            //$this->db->where('ay_year_code', $code);
            $this->db->where("ay_year_code LIKE '%$code%'");
        }

        $this->db->select($select);
        $this->db->from('academic_year');
        if($id)
        {
            $result = $this->db->get()->row_array();
        }
        else
        {
            $result = $this->db->get()->result_array();
        }
        //echo $this->db->last_query();die;
        return $result;
    }
    public function save_academic_year($param=array())
    {
        $id             = isset($param['id'])?$param['id']:false;
        if($id)
        {
            $this->db->where('id',$id);
            $this->db->update('academic_year',$param);
            $result = $id;
        }
        else
        {
            $this->db->insert('academic_year',$param);
            $result = $this->db->insert_id();
        }
        return $result;
    }

    public function delete_row($params = array())
    {
        if(isset($params['where']) && $params['where'] && $params['table'])
        {
            $this->db->where($params['where']);
            $this->db->delete($params['table']);
            return $this->db->affected_rows();
        }
    }
    
    public function short_courses($param = array())
    {
        $course_id  = isset($param['course_id'])?$param['course_id']:'0';
        // $select     = "course_basics.id,course_basics.cb_title,course_basics.cb_code, course_basics.cb_price, course_basics.cb_discount, course_basics.cb_is_free, course_basics.cb_slug,'course' as item_type,course_basics.cb_category ,course_basics.cb_image";
        $select     = "course_basics.id,course_basics.cb_code, course_basics.cb_position, course_basics.cb_access_validity, course_basics.cb_language, course_basics.cb_title, course_basics.cb_status, course_basics.cb_code, course_basics.cb_deleted, course_basics.cb_approved, course_basics.cb_groups, course_basics.cb_price, course_basics.cb_discount, course_basics.cb_is_free, course_basics.cb_slug,'course' as item_type,course_basics.cb_category ,course_basics.cb_image,course_basics.cb_has_rating";
        $this->db->select($select);
        $this->db->from('course_basics');
        $this->db->where('course_basics.cb_account_id',config_item('id'));
        $this->db->where('course_basics.cb_status','1');
        $this->db->where('course_basics.cb_deleted','0');
        $this->db->group_by("course_basics.id");
        $this->db->where('course_basics.id',$course_id);
        $result     = $this->db->get()->row_array();
        return $result;
    }
    
    public function item_courses($param = array())
    {
        $featured   = isset($param['featured'])?$param['featured'] : false;
        $popular    = isset($param['popular'])?$param['popular'] : false;
        $select     = "iso_item_type,iso_item_id,iso_item_sort_order,iso_item_price,iso_item_discount_price,";
        $this->db->select($select);
        $this->db->from('item_sort_order');
        $this->db->where('item_sort_order.iso_item_status','1');
        $this->db->where('item_sort_order.iso_item_deleted','0');
        $this->db->where('item_sort_order.iso_account_id',config_item('id'));
        if($featured)
        {
            $this->db->where('item_sort_order.iso_item_featured','1');
        }
        if($popular)
        {
            $this->db->where('item_sort_order.iso_item_popular','1');
        }
        $this->db->where('iso_account_id', config_item('id'));
        $this->db->order_by('item_sort_order.iso_item_sort_order', 'ASC');
        $this->db->order_by('item_sort_order.id', 'DESC');
        // $this->db->order_by("item_sort_order.iso_item_sort_order ASC", "item_sort_order.id DESC");
        // $this->db->group_by("item_sort_order.id");
        $result  = $this->db->get()->result_array();
        return $result;
    }
    
    function check_course_delete($course_id)
    {
        $this->db->query("DELETE FROM announcement WHERE an_course_id = ".$course_id."");
        $this->db->query("DELETE FROM course_lectures WHERE cl_course_id = ".$course_id."");
        $this->db->query("DELETE FROM course_subscription WHERE cs_course_id = ".$course_id." ");
        $this->db->query("DELETE FROM course_perfomance WHERE cp_course_id = ".$course_id." ");
        //$this->db->query("DELETE FROM announcement WHERE an_course_id = ".$course_id." ");
        $this->db->query("DELETE FROM course_tutors WHERE ct_course_id = ".$course_id." ");
        $this->db->query("DELETE FROM section WHERE s_course_id = ".$course_id." ");
        $this->db->query("DELETE FROM item_sort_order WHERE iso_item_id = ".$course_id." AND iso_item_type = 'course';");
        //$this->db->query("DELETE FROM course_basics WHERE id = ".$course_id."");
    }
    
    public function routes($param = array())
    {
        $select = isset($param['select'])?$param['select']:'*';//'id,slug'
        $course_id = isset($param['course_id'])?$param['course_id']:'*';
        $this->db->select();
        $this->db->where('r_account_id',config_item('id'));
        if($course_id)
        {
            $this->db->where('r_account_id',config_item('id'));
        }
        $this->db->from('routes');
        $this->db->get();
    }

    public function update_lecture_conversion_status($param = array())
    {
        if ($param['id']) {
            $this->db->where('id', $param['id']);
            $this->db->update('course_lectures', $param);
            return true;
        }
    }

    public function update_queue_conversion_status($param = array())
    {
        if ($param['lecture_id']) {
            $this->db->where('lecture_id', $param['lecture_id']);
            $this->db->update('conversion_queue', $param);
            return true;
        }
    }

    public function has_parent_lecture($param)
    {
        if ($param['lecture_id']) {
            $return = $this->db->get_where('course_lectures', array('cl_parent_lecture_id =' => $param['lecture_id']))->num_rows();
            return ($return  > 0);
         
        }  
    }
    public function save_copy_queue($save)
    {
        if(isset($save['id']) && $save['id'] != '')
        {
            $this->db->where('id',$save['id']);
            $this->db->update('file_copy_queue',$save);
            $result = $save['id'];
        }
        else
        {
            $this->db->insert('file_copy_queue', $save);
        }
    
    }
    public function get_copy_queue($param = array())
    {
        $status = isset($param['status']) ? $param['status'] : '0';

        $query = 'SELECT * FROM file_copy_queue WHERE cq_status = "'.$status.'" ORDER BY id ASC LIMIT 1';

        return $this->db->query($query)->row_array();
    }
    public function get_videos_count($param)
    {
        if ($param['video_id']) 
        {
            return $this->db->get_where('cl_filename', $param['video_id'])->num_rows();
        }
    }
}
?>
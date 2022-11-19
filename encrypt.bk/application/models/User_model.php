<?php 
Class User_model extends CI_Model
{	
    function __construct()
    {
        parent::__construct();
    }
	
    function users_old($param=array())
    {
        $limit 			= isset($param['limit'])?$param['limit']:0;
        $offset 		= isset($param['offset'])?$param['offset']:0;
        $order_by 		= isset($param['order_by'])?$param['order_by']:'users.id';
        $direction 		= isset($param['direction'])?$param['direction']:'DESC';
        $status 		= isset($param['status'])?$param['status']:'';
        $count       	= isset($param['count'])?$param['count']:false;
        $not_deleted    = isset($param['not_deleted'])?$param['not_deleted']:false;
        $role_id        = isset($param['role_id'])?$param['role_id']:false;
        $institute      = isset($param['institute'])?$param['institute']:false;
        $institute_ids  = isset($param['institute_ids'])?$param['institute_ids']:false;
        $admin          = $this->auth->get_current_user_session('admin');
        $institute_filter= (isset($admin['id'])&&$admin['us_role_id']==8)?$admin['id']:0;
        $keyword 		= isset($param['keyword'])?$param['keyword']:'';
        $filter 		= isset($param['filter'])?$param['filter']:0;
        $course_id 		= isset($param['course_id'])?$param['course_id']:0;
        $not_subscribed = isset($param['not_subscribed'])?$param['not_subscribed']:false;
        
        $course_select = '';
        if($course_id)
        {
            $course_select = ' course_subscription_cp.*, ';
        }
        $select = isset($param['select'])?$param['select']:false;
        if(!$select)
        {
            $select = 'users.*, users_institute.*, '.$course_select.' action_authors.us_name as wa_name_author,  web_actions.wa_name, web_actions.wa_code';
        }
        
        $this->db->select($select);
        $this->db->join('(SELECT * FROM users action_authors WHERE action_authors.us_account_id='.config_item('id').')action_authors', 'users.action_by = action_authors.id', 'left');
        $this->db->join('web_actions', 'users.action_id = web_actions.id', 'left');
        $this->db->join('(SELECT users_institute.id as institute_id, us_name as institute_name FROM users users_institute WHERE us_role_id = 8) users_institute', 'users.us_institute_id=users_institute.institute_id', 'left');
        if($course_id)
        {
            $this->db->join('(SELECT id as subscriber_id, cs_user_id, cs_course_id FROM course_subscription course_subscription_cp WHERE course_subscription_cp.cs_course_id = "'.$course_id.'") course_subscription_cp', 'users.id = course_subscription_cp.cs_user_id', 'left');            
            if( $not_subscribed )
            {
               $this->db->where('course_subscription_cp.subscriber_id is NULL', NULL, FALSE);
            }
            else
            {
                $this->db->where('course_subscription_cp.cs_course_id', $course_id);                 
            }
        }
        
        if($institute_filter != 0){
            $this->db->where('users.us_institute_id',$institute_filter);
        }
        $this->db->order_by($order_by, $direction);
        if($limit>0)
        {
            $this->db->limit($limit, $offset);
        }
        if( $keyword )
        {
            $this->db->like('users.us_name', $keyword); 
        }
        if( $not_deleted )
        {
            $this->db->where('users.us_deleted', '0'); 
        }
        if( $filter )
        {
            switch ($filter) {
                case 'active':
                    $status = '1';
                    $this->db->where('users.us_deleted', '0'); 
                    break;
                case 'inactive':
                    $status = '0';
                    $this->db->where('users.us_deleted', '0'); 
                    break;
                case 'approved':
                    $this->db->where('users.us_deleted', '0'); 
                    $status = '2';
                    break;
                case 'deleted':
                    $this->db->where('users.us_deleted', '1'); 
                    break;
                default:
                    break;
            }
        }
        
        if( $status != '' )
        {
            $this->db->where('users.us_status', $status); 
        }
        
        $role_id_array = explode(",", $role_id);
        $role_id_int_array = array_map('intval', $role_id_array);
        
        if($role_id)
        {
            $this->db->where_in('users.us_role_id', $role_id_int_array);            
        }
        
        if(!$institute)
        {
            $this->db->where('users.us_role_id>', '1');        
        }
        else
        {
            $this->db->where('users.id NOT IN(SELECT sa_user_id FROM super_admins WHERE sa_account_id = '.config_item('id').' )');            
        }
        if( isset($param['institute_id'])) 
    	{
                $this->db->where('users.us_institute_id', $param['institute_id']);
        }
        if( isset($param['institute_ids'])) 
    	{
                $this->db->where_in('users.us_institute_id', $param['institute_ids']);
        }
        $this->db->where('users.us_account_id', config_item('id'));
        
        if( $count )
        {
            $result = $this->db->count_all_results('users');            
        }
        else
        {
            $result = $this->db->get('users')->result_array();
        }
        //echo '<pre>';print_r($param);die;
        //echo $this->db->last_query();die;
        return $result;
    }
    public function users($param = array())
    {
        $limit          = isset($param['limit']) ? $param['limit'] : 0;
        $offset         = isset($param['offset']) ? $param['offset'] : 0;
        $order_by       = isset($param['order_by']) ? $param['order_by'] : 'users.id';
        $direction      = isset($param['direction']) ? $param['direction'] : 'DESC';
        $status         = isset($param['status']) ? $param['status'] : '';
        $count          = isset($param['count']) ? $param['count'] : false;
        $not_deleted    = isset($param['not_deleted']) ? $param['not_deleted'] : false;
        $role_id        = isset($param['role_id']) ? $param['role_id'] : false;
        $institute      = isset($param['institute_id']) ? $param['institute_id'] : false;
        $institute_ids  = isset($param['institute_ids']) ? $param['institute_ids'] : false;
        $branch         = isset($param['branch']) ? $param['branch'] : false;
        $batch          = isset($param['batch_id']) ? $param['batch_id'] : false;
        $user_ids       = isset($param['user_ids']) ? $param['user_ids'] : array();
        $keyword        = isset($param['keyword']) ? $param['keyword'] : '';
        $filter         = isset($param['filter']) ? $param['filter'] : 0;
        $check_time     = isset($param['check_deleted_time']) ? $param['check_deleted_time'] : false;
        $course_id 		= isset($param['course_id'])?$param['course_id']:0;
        $bundle_id 		= isset($param['bundle_id'])?$param['bundle_id']:0;
        $not_subscribed = isset($param['not_subscribed'])?$param['not_subscribed']:false;
        $course_select  = '';
        $select         = isset($param['select'])?$param['select']:false;
        $not_course_ids = isset($param['course_not_bundle'])?$param['course_not_bundle']:false;
        if(!$select)
        {
            $select = 'users.*, '.$course_select.' ';
        }
        
        $this->db->select($select);
        $this->db->order_by($order_by, $direction);
        if ($limit > 0) {
            $this->db->limit($limit, $offset);
        }
        if($course_id)
        {
            //$this->db->join('(SELECT id as subscriber_id, cs_user_id, cs_course_id FROM course_subscription course_subscription_cp WHERE course_subscription_cp.cs_course_id = "'.$course_id.'") course_subscription_cp', 'users.id = course_subscription_cp.cs_user_id', 'left');            
            if( $not_subscribed )
            {
                $this->db->where('id NOT IN (SELECT cs_user_id FROM course_subscription WHERE cs_course_id = "'.$course_id.'")');
               
            }
            else
            {
                $this->db->where('id IN (SELECT cs_user_id FROM course_subscription WHERE cs_course_id = "'.$course_id.'")');                 
            }
        }
        if($bundle_id)
        {
            if( $not_subscribed )
            {
               $this->db->where('id NOT IN (SELECT bs_user_id FROM bundle_subscription WHERE bs_bundle_id = "'.$bundle_id.'")');
            //    if($not_course_ids)
            //    {
            //        $this->db->where('id NOT IN (SELECT cs_user_id FROM course_subscription WHERE cs_course_id in ('.$not_course_ids.'))');
            //    }
            }
            else
            {
                $this->db->where('id IN (SELECT bs_user_id FROM bundle_subscription WHERE bs_bundle_id = "'.$bundle_id.'")');                 
            }
        }

        if ($keyword) 
        {
            $this->db->group_start();
            $this->db->or_like(['us_name' => $keyword, 'us_institute_code' => $keyword, 'us_profile_fields' => $keyword, 'us_email' => $keyword, 'us_phone' => $keyword]);
            $this->db->group_end();
        }

        if ($not_deleted) {
            $this->db->where('users.us_deleted', '0');
        }
        if ($check_time) {
            $this->db->where('updated_date<= SUBDATE( CURRENT_DATE, INTERVAL 24 HOUR)');
        }
        if(isset($param['verified'])){
            $this->db->where('users.us_email_verified','1');
        }
        if($institute) {
            $this->db->where('users.us_institute_id', $institute);
        }
        if($institute_ids) {
            $this->db->where_in('users.us_institute_id', $institute_ids);
        }
        if($branch) {
            $this->db->where('users.us_branch', $branch);
        }
        if($batch) {
            $this->db->like('users.us_groups', $batch);
        }
        if ($filter) {
            switch ($filter) {
                case 'active':
                    $status = '1';
                    $this->db->where('users.us_deleted', '0');
                    break;
                case 'inactive':
                    $status = '0';
                    $this->db->where('users.us_deleted', '0');
                    break;
                case 'not-approved':
                    $this->db->where('users.us_deleted', '0');
                    $status = '2';
                    break;
                case 'deleted':
                    $this->db->where('users.us_deleted', '1');
                    break;
                default:
                    break;
            }
        }
        if ($status != '') {
            $this->db->where('users.us_status', $status);
        }
        $role_id_array = explode(",", $role_id);
        $role_id_int_array = array_map('intval', $role_id_array);
        
        if ($role_id) {
            $this->db->where_in('users.us_role_id', $role_id_int_array);
        }
        
        if(!empty($user_ids))
        {
            $this->db->where_in('users.id', $user_ids);
        }
        $this->db->where('users.us_account_id', config_item('id'));
        if ($count) {
            $result = $this->db->count_all_results('users');
        } else {
            $result = $this->db->get('users')->result_array();
        }
        //echo $this->db->last_query();exit;
        return $result; 
    }
    public function user_old($param = array())
    {
        $this->db->select('users.*,roles.rl_name');
        $this->db->where('users.us_account_id', config_item('id'));
        if( isset($param['status'])) 
    	{
            $this->db->where('users.us_status', 1);
        }
        
        if( isset($param['name'])) 
    	{
                if( isset($param['id'])) 
                {
                    $this->db->where('users.id!=', $param['id']);
                }
                $this->db->like('us_name', $param['name']);
    	}
        if( isset($param['email'])) 
    	{
                $this->db->like('users.us_email', $param['email']);
    	}
    	if( isset($param['id'])) 
    	{
                $this->db->where('users.id', $param['id']);
        }
    	if( isset($param['institute_id']) && ($param['institute_id'])) 
    	{
                $this->db->where('users.us_institute_id', $param['institute_id']);
        }
        $this->db->join('roles', 'users.us_role_id = roles.id', 'left');
        
        $return = $this->db->get('users')->row_array();	
        /*if(isset($param['from_reg']))
        {
           echo $this->db->last_query();die;
        }*/
        return $return;
    }
    function user($param = array())
    {
        $select     = (isset($param['select'])? $param['select']: 'users.*, roles.rl_name');
        if( isset($param['status'])) 
    	{
            $this->db->where('users.us_status', $param['status']);
    	}
        if( isset($param['name'])) 
    	{
            if( isset($param['id'])) 
            {
                $this->db->where('users.id!=', $param['id']);
            }
            $this->db->like('us_name', $param['name']);
    	}
        if( isset($param['email'])) 
    	{
            $this->db->like('users.us_email', $param['email']);
    	}
    	if( isset($param['id'])) 
    	{
            $this->db->where('users.id', $param['id']);
        }
        if(isset($param['verified']))
        {
            $this->db->where('users.us_email_verified','1');
        }
    	if( isset($param['institute_id'])) 
    	{
            $this->db->where('users.us_institute_id', $param['institute_id']);
        }
    	if( isset($param['register_number'])) 
    	{
            $this->db->where('users.us_register_number', $param['register_number']);
        }
        if( isset($param['us_phone'])) 
    	{
            $this->db->where('users.us_phone', $param['us_phone']);
        }
        if (isset($param['exclude_id'])) 
        {
            $this->db->where('users.id!=', $param['exclude_id']);
        }
        
        $this->db->select($select);
        $this->db->join('roles', 'users.us_role_id = roles.id', 'left');
        $this->db->where('users.us_account_id', config_item('id'));
        $return = $this->db->get('users')->row_array();	
        //echo $this->db->last_query();die;
        return $return;
    }
    
    function get_user_details($params = array())
    {
        $select = isset($params['select'])? $params['select'] : '*';
        if(isset($params['id']))
        {
            $this->db->where('id', $params['id']);
        }
        $this->db->select($select);
        $this->db->where('us_account_id', config_item('id'));
        return $this->db->get('users')->row_array();
    }

    function get_users($params = array())
    {
        $select         = isset($params['select'])? $params['select'] : '*';
        $keyword 		= isset($params['keyword'])?$params['keyword']:'';
        $order_by 		= isset($params['order_by'])?$params['order_by']:'id';
        $status 		= isset($params['status'])?$params['status']:'';
        $role_ids_not   = isset($params['role_ids_not'])?$params['role_ids_not']:0;
        $not_deleted    = isset($params['not_deleted'])?$params['not_deleted']:false;
        $direction 		= isset($params['direction'])?$params['direction']:'DESC';
        $limit 			= isset($param['limit'])?$param['limit']:0;
        $offset 		= isset($param['offset'])?$param['offset']:0;

        if($role_ids_not)
        {
            $this->db->where('users.us_role_id !=', $role_ids_not);
        }

        if( $select )
        {
            $this->db->select($select);
        }
        if( $status != '' )
        {
            $this->db->where('users.us_status', $status); 
        }
        
        if( $not_deleted )
        {
            $this->db->where('users.us_deleted', '0'); 
        }

        if($keyword)
        {
            $this->db->where("(`us_name` LIKE '%$keyword%' OR `us_email` LIKE '%$keyword%' OR `us_phone` LIKE '%$keyword%')");
        }
        if($limit > 0 )
        {
            $this->db->limit($limit, $offset);
        }
        $this->db->order_by($order_by, $direction);
        $this->db->where('us_account_id', config_item('id'));
        return $this->db->get('users')->result_array();
    }

    function user_messages($param=array())
    {
        $id = isset($param['id'])?$param['id']:0;
        if( $id ) 
        {
                $this->db->where('user_messages.um_user_id', $id);
                return $this->db->get('user_messages')->row_array();	
        }
    }
    function user_admin($param=array())
    {
        $this->db->select('users.*');
        if( isset($param['role_id'])) 
        {
            $this->db->where('users.us_role_id', $param['role_id']);
        }
        $this->db->where('users.us_account_id', config_item('id'));
        return $this->db->get('users')->row_array();
    }
    
    function recently_viewed($param=array())
    {
        // $user_id = isset($param['user_id'])?$param['user_id']:'';
        // $this->db->select('users.id, users.us_name, users.us_image');
        // if( $user_id )
        // {
        //     $this->db->where('rvu_admin_id', $user_id);		
        // }
        // $this->db->join('users', 'recently_view_users.rvu_user_id = users.id', 'left');
        // $this->db->order_by('rvu_date', 'DESC');
        // $this->db->limit(4, 0);
        // $result  = $this->db->get('recently_view_users')->result_array();  
        return $result = array();
    }
    public function save_user($data){

        if(!empty($data))
        {
            $this->db->trans_start();
            // $this->db->insert_batch('course_subscription', $data); 
            
            foreach($data as $students)
            {
                $this->db->where('id', $students['id']);
                $this->db->update('users', $students);
            }
            $this->db->trans_complete(); 
            
            return true;
        }
        
    }
    function save($data)
    {
        $id=isset($data['id'])?$data['id']:false;
        if($id)
        {
                $this->db->where('id', $data['id']);
                $this->db->update('users', $data);
                return $data['id'];
        }
        else
        {
                $this->db->insert('users', $data);
                return $this->db->insert_id();
        }
    }
    function save_user_messages($data)
    {
        $message = $this->user_messages(array('id'=>$data['um_user_id']));
        if($message)
        {
                $this->db->where('um_user_id', $data['um_user_id']);
                $this->db->update('user_messages', $data);
                return $data['um_user_id'];
            }
        else
        {
                $this->db->insert('user_messages', $data);
                return $this->db->insert_id();
        }
    }
    
    function enrolled_course($param=array())
    {
        $user_id            = isset($param['user_id'])?$param['user_id']:false;
        $approved           = isset($param['approved'])?$param['approved']:'';
        
        $this->db->select('users.id, users.us_name,course_subscription.cs_course_id, course_subscription.cs_approved, course_subscription.cs_user_id, course_subscription.cs_start_date, course_subscription.cs_end_date, course_subscription.cs_course_validity_status, course_basics.id, course_basics.cb_title, course_basics.cb_status,course_basics.cb_image, course_basics.cb_slug,cb_is_free,cb_discount,cb_price,cb_has_renewal,cb_access_validity,course_basics.cb_category,categories.ct_name,course_basics.cb_validity,course_basics.cb_validity_date');
        $this->db->join('users', 'users.id = course_subscription.cs_user_id', 'left');
        $this->db->join('course_basics', 'course_basics.id = course_subscription.cs_course_id', 'left');
        $this->db->join('categories', 'categories.id = course_basics.cb_category', 'left');
        
        $this->db->where('users.id', $user_id);
        
        $this->db->where('course_basics.cb_account_id',config_item('id'));
        $this->db->where('course_basics.cb_deleted', '0');
        $this->db->where('course_basics.cb_status', '1');
        $result = $this->db->get('course_subscription')->result_array();
        //echo $this->db->last_query();die;
        $return = array();
        if(!empty($result))
        {
            foreach ($result as $subscriber)
            {
                $course_id  = $subscriber['cs_course_id'];
                $user_id    = $subscriber['cs_user_id'];
                $query      = "SELECT SUM(ll_percentage_new)/COUNT(*) as percentage, course_basics.cb_title, course_basics.cb_image , COUNT(*) as total_lectures 
                            FROM  course_lectures 
                            LEFT JOIN course_basics ON course_lectures.cl_course_id = course_basics.id 
                            LEFT JOIN (SELECT ll_user_id, ll_lecture_id, ll_attempt, 
                                                (CASE
                                                    WHEN ll_attempt > 1 THEN 100
                                                    ELSE ll_percentage
                                                END ) AS ll_percentage_new
                                        FROM lecture_log lecture_log_cp 
                                        WHERE ll_user_id = ".$user_id." AND ll_lecture_id IN (SELECT id FROM course_lectures WHERE cl_course_id = ".$course_id." AND cl_deleted = '0' AND cl_status = '1')
                                        ORDER BY ll_user_id ASC 
                                    ) lecture_log_cp ON course_lectures.id = lecture_log_cp.ll_lecture_id
                                    WHERE course_lectures.cl_course_id = ".$course_id." AND cl_deleted = '0' AND cl_status = '1'";
                $lecture_logs =  $this->db->query($query)->row_array();   
                $subscriber['percentage'] = $lecture_logs['percentage'];
                //echo '<pre>'; print_r($lecture_logs);die;
                //switching the condition methos based on fileter
                $return[] = $subscriber;
            }
        }
        return $return;
    }
    
    function assessment_attempt($param=array()){
        $user_id            = isset($param['user_id'])?$param['user_id']:false;
        $attempted_id       = isset($param['attempted_id'])?$param['attempted_id']:false;
        $approved           = isset($param['approved'])?$param['approved']:'';
        
        $this->db->select('users.*, assessment_attempts.aa_assessment_id, assessment_attempts.aa_user_id, assessment_attempts.aa_attempted_date,assessment_attempts.aa_duration, course_lectures.cl_lecture_name, course_lectures.cl_course_id, course_basics.cb_title, course_basics.cb_status, assessments.a_duration, assessment_attempts.id as attempted_id');
        $this->db->join('users', 'users.id = assessment_attempts.aa_user_id', 'left');
        $this->db->join('assessments', 'assessments.id = assessment_attempts.aa_assessment_id', 'left');
        $this->db->join('course_lectures', 'course_lectures.id = assessments.a_lecture_id', 'left');
        $this->db->join('course_basics', 'course_basics.id = course_lectures.cl_course_id', 'left');
        
        if($user_id)
        {
            $this->db->where('users.id', $user_id);		
        }
        if($attempted_id){
            $this->db->where('assessment_attempts.id', $attempted_id);
        }
        $this->db->where('course_basics.cb_account_id', config_item('id'));
        $result = $this->db->get('assessment_attempts')->result_array();
        //echo $this->db->last_query(); die;
        return $result;
    }
    
    function assessment_attempt_excel_report($param=array()){
        $user_id            = isset($param['user_id'])?$param['user_id']:false;
        $attempted_id       = isset($param['attempted_id'])?$param['attempted_id']:false;
        $approved           = isset($param['approved'])?$param['approved']:'';
        
        $this->db->select('users.id, assessment_attempts.aa_assessment_id, assessment_attempts.aa_user_id, assessment_attempts.aa_attempted_date,assessment_attempts.aa_duration, course_lectures.cl_lecture_name, course_lectures.cl_course_id, course_basics.cb_title, course_basics.cb_status, assessments.a_duration, assessment_attempts.id as attempted_id');
        $this->db->join('users', 'users.id = assessment_attempts.aa_user_id', 'left');
        $this->db->join('assessments', 'assessments.id = assessment_attempts.aa_assessment_id', 'left');
        $this->db->join('course_lectures', 'course_lectures.id = assessments.a_lecture_id', 'left');
        $this->db->join('course_basics', 'course_basics.id = course_lectures.cl_course_id', 'left');
        
        $this->db->order_by('assessment_attempts.aa_assessment_id');
        
        if($user_id)
        {
            $this->db->where('users.id', $user_id);		
        }
        if($attempted_id){
            $this->db->where('assessment_attempts.id', $attempted_id);
        }
        $this->db->where('course_basics.cb_account_id', config_item('id'));
        $result = $this->db->get('assessment_attempts')->result_array();
        //echo $this->db->last_query(); die;
        return $result;
    }
    
    function assessment_report($param=array())
    {
        $assessment_id  = isset($param['assessment_id'])?$param['assessment_id']:false;
        $user_id        = isset($param['user_id'])?$param['user_id']:false;
        $assessment_attempt_id = isset($param['assessment_attempt_id'])?$param['assessment_attempt_id']:false;
        
        $this->db->select('users.*, assessment_attempts.id, assessment_attempts.aa_assessment_id, assessment_attempts.aa_user_id, assessment_attempts.aa_attempted_date, assessment_questions.aq_question_id, questions.q_type, questions.q_question, questions.q_answer, assessment_report.ar_question_id, assessment_report.ar_answer, assessment_report.ar_mark, questions.q_options, questions.q_positive_mark');
        $this->db->join('users', 'users.id = assessment_attempts.aa_user_id', 'left');
        $this->db->join('assessment_questions', 'assessment_questions.aq_assesment_id = assessment_attempts.aa_assessment_id', 'left' );
        $this->db->join('questions','questions.id = assessment_questions.aq_question_id', 'left');
        $this->db->join('assessment_report','assessment_report.ar_question_id = assessment_questions.aq_question_id AND assessment_report.ar_attempt_id = assessment_attempts.id', 'left');
        
        if($user_id)
        {
            $this->db->where('users.id', $user_id);		
        }
        if($assessment_id)
        {
            $this->db->where('assessment_attempts.aa_assessment_id', $assessment_id);		
        }
        if($assessment_attempt_id)
        {
            $this->db->where('assessment_attempts.id', $assessment_attempt_id);       
        }
        $result = $this->db->get('assessment_attempts')->result_array();
        //echo $this->db->last_query();die;
        return $result;
    }
    
    function challenge_zone_attempt($param=array()){
        $user_id            = isset($param['user_id'])?$param['user_id']:false;
        $approved           = isset($param['approved'])?$param['approved']:'';
        
        $this->db->select('users.*, challenge_zone_attempts.cza_challenge_zone_id, challenge_zone_attempts.cza_user_id, challenge_zone_attempts.cza_attempted_date, challenge_zone_attempts.cza_duration, challenge_zone.cz_title, challenge_zone.cz_category, challenge_zone.cz_duration, challenge_zone_attempts.id as challenge_zone_attempt_id');
        $this->db->join('users', 'users.id = challenge_zone_attempts.cza_user_id', 'left');
        $this->db->join('challenge_zone', 'challenge_zone.id = challenge_zone_attempts.cza_challenge_zone_id', 'left');
        
        if($user_id)
        {
            $this->db->where('users.id', $user_id);		
        }
        $this->db->where('challenge_zone.cz_account_id', config_item('id'));
        $result = $this->db->get('challenge_zone_attempts')->result_array();
        return $result;
    }
    
    function challenge_zone_report($param=array())
    {
        $challenge_zone_id  = isset($param['challenge_zone_id'])?$param['challenge_zone_id']:false;
        $user_id        = isset($param['user_id'])?$param['user_id']:false;
        
        $this->db->select('users.*, challenge_zone_attempts.id, challenge_zone_attempts.cza_challenge_zone_id, challenge_zone_attempts.cza_user_id, challenge_zone_attempts.cza_attempted_date, challenge_zone_questions.czq_question_id, questions.q_type, questions.q_question, questions.q_answer, challenge_zone_report.czr_question_id, challenge_zone_report.czr_answer, questions.q_positive_mark');
        $this->db->join('users', 'users.id = challenge_zone_attempts.cza_user_id', 'left');
        $this->db->join('challenge_zone_questions', 'challenge_zone_questions.czq_challenge_zone_id = challenge_zone_attempts.cza_challenge_zone_id', 'left' );
        $this->db->join('questions','questions.id = challenge_zone_questions.czq_question_id', 'left');
        $this->db->join('challenge_zone_report','challenge_zone_report.czr_question_id = challenge_zone_questions.czq_question_id AND challenge_zone_report.czr_attempt_id = challenge_zone_attempts.id', 'left');
        
        if($user_id)
        {
            $this->db->where('users.id', $user_id);		
        }
        if($challenge_zone_id)
        {
            $this->db->where('challenge_zone_attempts.cza_challenge_zone_id', $challenge_zone_id);		
        }
        $result = $this->db->get('challenge_zone_attempts')->result_array();
        //echo $this->db->last_query();die;
        return $result;
    }
            
    function subscriptions($param=array())
    {
        $select                 = isset($param['select'])?$param['select']:'course_subscription.*';
        $user_id                = isset($param['user_id'])?$param['user_id']:'';
        $course_id              = isset($param['course_id'])?$param['course_id']:'';
        $not_in_bondle          = isset($param['not_bundle_course'])?$param['not_bundle_course']:false;
        if($user_id)
        {
            $this->db->select($select);
        }
        if($user_id)
        {
            $this->db->where('cs_user_id', $user_id);
        }
        if($course_id)
        {
            $this->db->where('cs_course_id', $course_id);
        }
        if($not_in_bondle)
        {
            $this->db->where('cs_bundle_id', '0'); 
        }
        $this->db->join('course_basics', 'course_basics.id = course_subscription.cs_course_id', 'left');
        return $this->db->get('course_subscription')->result_array();
    }
    function subscription($param=array())
    {
        $user_id    = isset($param['user_id'])?$param['user_id']:false;
        $course_id  = isset($param['course_id'])?$param['course_id']:false;
        $limit      = isset($param['limit'])?$param['limit']:0;
        $count      = isset($param['count'])?$param['count']:false;
        $select     = isset($param['select'])?$param['select']:'*';
        $course_ids = isset($param['course_ids'])?$param['course_ids']:false;

        $this->db->select($select);
        if($user_id)
        {
            $this->db->where('cs_user_id', $user_id);
        }
        if($course_id)
        {
            $this->db->where('cs_course_id', $course_id);
        }
        if($course_ids)
        {
            $this->db->where_in('cs_course_id', $course_ids);
        }
        if($limit)
        {
            $this->db->limit($limit);
        }
        if($limit == 1 || ($user_id && $course_id)){
            return $this->db->get('course_subscription')->row_array();
        }else{
            return $this->db->get('course_subscription')->result_array();
        }
        if($count){
            return $this->db->get('course_subscription')->num_rows();
        }
        
    }
    public function subscription_save($data)
    {
        if(!empty($data))
        {
            for($i = 0; $i < count($data); $i++)
            {
                $data[$i]['cs_account_id'] = config_item('id');
            }
            // $this->db->trans_start();
            
            $this->db->insert_batch('course_subscription', $data); 
            
            // foreach($data as $students)
            // {
            //     $this->db->insert('course_subscription', $students);
            // }
            // $this->db->trans_complete(); 
        }
        return true;
    } 
    
    public function save_subscription_new($data,$condition=array()){

        $update     = isset($condition['update'])?$condition['update']:false;
        $data['cs_account_id'] =  config_item('id');
        if($update){

            $user_id    = isset($condition['user_id'])?$condition['user_id']:false;
            $user_ids   = isset($condition['user_ids'])?$condition['user_ids']:false;
            $id         = isset($condition['id'])?$condition['id']:false;
            $course_id  = isset($condition['course_id'])?$condition['course_id']:false;
            $course_ids = isset($condition['course_ids'])?$condition['course_ids']:false;
            if($user_ids){
                $this->db->where_in('cs_user_id', $user_ids);
            }
            if($course_ids){
                $this->db->where_in('cs_course_id', $course_ids);
            }
            if($user_id)
            { 
                $this->db->where('cs_user_id', $user_id);
            }
            if($course_id)
            { 
                $this->db->where('cs_course_id', $course_id);
            }
            if($id)
            { 
                $this->db->where('id', $id);
            }
            $this->db->where('cs_account_id', config_item('id')); 
            $this->db->update('course_subscription', $data);
            return true;
            
            
        //  echo $this->db->last_query();exit;
        }else{
            
            $this->db->insert('course_subscription', $data);
            return $this->db->insert_id();
        }
    }
    
    function save_subscription($data)
    {
		$old = $this->db->select('id')
						->where(array('cs_user_id' => $data['cs_user_id'], 'cs_course_id' => $data['cs_course_id']))
						->get('course_subscription')
						->row_array();
        $old = (isset($old['id'])&&$old['id'])?true:false;
        $data['cs_account_id'] =  config_item('id');
		if($old)
		{
            $this->db->where('cs_user_id', $data['cs_user_id']);
            $this->db->where('cs_course_id', $data['cs_course_id']);
            $this->db->where('cs_account_id', config_item('id'));
            $this->db->update('course_subscription', $data);
            //echo $this->db->last_query();die;
            return true;
		}
		else
		{
            $this->db->insert('course_subscription', $data);
            return $this->db->insert_id();
		}
    }
    function save_invite_user($data)
    {
        $this->db->insert('invited_users', $data);
        return $this->db->insert_id();
    }
    
    function invited_users($param=array())
    {
        if( isset($param['email'])) 
        {
            $this->db->where('iu_email_id', $param['email']);
        }
        $this->db->where('iu_account_id', config_item('id'));
	    return $this->db->get('invited_users')->row_array();
    }
    
    function get_invited_users()
    {
        $result = $this->db->get('invited_users')->result_array();
        return $result;
    }
    
    function groups($param=array())
    {
        $limit          = isset($param['limit']) ? $param['limit'] : 0;
        $offset         = isset($param['offset']) ? $param['offset'] : 0;
        $order_by       = isset($param['order_by']) ? $param['order_by'] : 'id';
        $direction      = isset($param['direction']) ? $param['direction'] : 'DESC';
        $status         = isset($param['status']) ? $param['status'] : '';
        $count          = isset($param['count']) ? $param['count'] : false;
        $not_deleted    = isset($param['not_deleted']) ? $param['not_deleted'] : false;
        $institute_id   = isset($param['institute_id']) ? $param['institute_id'] : false;
        $course_id      = isset($param['course_id']) ? $param['course_id'] : 0;
        $select         = isset($param['select'])?$param['select']:0;
        $not_ids        = isset($param['not_ids'])? $param['not_ids']: arary();
        
        $this->db->select($select);
        // $this->db->join('users', 'groups.action_by = users.id', 'left');
        
        $this->db->order_by($order_by, $direction);
        if($limit>0)
        {
            $this->db->limit($limit, $offset);
        }
        if( $not_deleted )
        {
            $this->db->where('groups.gp_deleted', '0'); 
        }
        if( $status != '' )
        {
            $this->db->where('groups.gp_status', $status); 
        }
        $this->db->where('groups.gp_account_id', config_item('id'));
        if($institute_id){
            $this->db->where('groups.gp_institute_id',$institute_id);
        }
        if (!empty($not_ids)) {
            $this->db->where_not_in('groups.id', $not_ids);
        }
        if ($count) {
            $result = $this->db->count_all_results('users');
        } else {
            $result = $this->db->get('groups')->result_array();
        }
        // echo $this->db->last_query();die;
        return $result;
    }
    
    function group_users($param=array())
    {
        $this->db->like('concat(",", us_groups, ",")', ','.$param['group_id'].',');
        $this->db->where('us_account_id', config_item('id'));
	    $result =  $this->db->get('users')->result_array();	
        //echo '<pre>';print_r($result);die;
        //echo $this->db->last_query();die;
        return $result;
    }
    
    function save_group($data)
    {
        if($data['id'])
        {
            $this->db->where('id', $data['id']);
            $this->db->update('groups', $data);
            return $data['id'];
        }
        else
        {
            $this->db->insert('groups', $data);
            return $this->db->insert_id();
	    }
    }
    
    function save_recently_view($data)
    {
        // if( $this->db->get_where('recently_view_users', array('rvu_admin_id' => $data['rvu_admin_id'], 'rvu_user_id' => $data['rvu_user_id']))->result())
        // {
        //         $this->db->where(array('rvu_admin_id' => $data['rvu_admin_id'], 'rvu_user_id' => $data['rvu_user_id']));
        //         $this->db->update('recently_view_users', $data);
        //     }
        // else
        // {
        //         $this->db->insert('recently_view_users', $data);
        // }
        return true;
    }
    function wishlist_courses($param=array())
    {
        $user_id  = isset($param['user_id'])?$param['user_id']:false;
        $this->db->select('course_wishlist.*, course_basics.id,course_basics.cb_category,course_basics.cb_title, course_basics.cb_status,course_basics.cb_image,categories.ct_name,course_basics.cb_slug');
        $this->db->join('course_basics', 'course_basics.id = course_wishlist.cw_course_id', 'left');
        $this->db->join('categories', 'categories.id = course_basics.cb_category', 'left');
        /*$this->db->select('course_wishlist.cw_course_id');
        $this->db->from('course_wishlist');*/
        $this->db->where('course_basics.cb_account_id', config_item('id'));
        $this->db->where('course_wishlist.cw_user_id', $user_id);
        $this->db->where('course_basics.cb_deleted', '0');
        $result   = $this->db->get('course_wishlist')->result_array();
        return $result;
    }
    function get_tutors($cid){
        $this->db->select('users.us_name');
        $this->db->from('users');
        $this->db->join('course_tutors','users.id = course_tutors.ct_tutor_id');
        $this->db->where('ct_course_id', $cid);
        $result = $this->db->get()->result_array();
        return $result;
    }
    function get_lecture_count($cid, $param = array()){
        $this->db->select('*');
        $this->db->from('course_lectures');
        $this->db->where('cl_course_id', $cid);
        if(count($param) > 0){
            $this->db->where_in('cl_lecture_type', $param);
        }
        $result = $this->db->get();
        return $result->num_rows();
    }
    function get_lecture_completion_count($cid, $uid, $param = array()){
        $this->db->select('*');
        $this->db->from('course_lectures');
        $this->db->join('lecture_log','lecture_log.ll_lecture_id = course_lectures.id');
        $this->db->where('course_lectures.cl_course_id', $cid);
        $this->db->where('lecture_log.ll_user_id', $uid);
        $this->db->where('lecture_log.ll_percentage > ', '95');
        if(count($param) > 0){
            $this->db->where_in('course_lectures.cl_lecture_type', $param);
        }
        $result = $this->db->get();
        return $result->num_rows();
    }
    function get_wishlist_courses($cid){
        $this->db->select('course_basics.*');
        $this->db->from('course_basics');
        $this->db->where('id', $cid);
        $result   = $this->db->get()->result_array();
        return $result;
    }
    function get_course_rating($cid){
        $course_id = isset($cid)?$cid:false;
        $this->db->select('ROUND(AVG(cc_rating), 1) as avg');
        $this->db->from('course_ratings');
        $this->db->where('cc_course_id', $course_id);
        $result = $this->db->get()->row_array();
        if($result['avg'] != ''){
            return $result['avg'];
        }
        else{
            return 0;
        }
    }
    /*  Created by Yadu Chandran
    Function for getting user password
    */
    function get_user_password($param=array())
    {
        $user_id   = isset($param['user_id'])?$param['user_id']:false;
        //$approved  = isset($param['approved'])?$param['approved']:'';
        $this->db->select('us_password');
        $this->db->from('users');
        $this->db->where('id', $user_id);
        $result    = $this->db->get()->result_array();
        return $result[0]['us_password'];
    }
    /*  Created by Yadu Chandran
        Function for update password
    */
    function update_password($data , $filter = array())
    {
        $user_id = isset($filter['id'])?$filter['id']:false;
        $email   = isset($filter['email'])?$filter['email']:false;
        if($user_id)
        {
            $this->db->where('id', $user_id);
        }
        if($email)
        {
            $this->db->where('us_email', $email);
        }
        
        $response = $this->db->update('users', $data);
        if($user_id)
        {
            return $user_id;
        }
        else
        {
            return $response;
        }
        
    }
    function assessment_list($param){
        $user_id        = isset($param['user_id'])?$param['user_id']:false;
        $attempted_id   = isset($param['attempted_id'])?$param['attempted_id']:false;
        $this->db->select('users.*, assessment_attempts.aa_assessment_id, assessment_attempts.aa_user_id, assessment_attempts.aa_attempted_date, course_basics.cb_title, assessments.a_duration, assessment_attempts.id as attempted_id, categories.ct_name, assessment_attempts.aa_duration');
        $this->db->from('assessment_attempts');
        $this->db->join('assessments', 'assessments.id = assessment_attempts.aa_assessment_id');
        $this->db->join('users','users.id = assessment_attempts.aa_user_id');
        $this->db->join('course_basics','course_basics.id = assessments.a_course_id');
        $this->db->join('categories', 'categories.id = course_basics.cb_category');
        if($user_id){
            $this->db->where('users.id',$user_id);
        }
        if($attempted_id){
            $this->db->where('assessment_attempts.id',$attempted_id);
        }
        return $this->db->get()->result_array();
    }
    /*
    * Function used to get the field values in auto suggetion list according to the field id
    * Created by :Neehu KP
    * Created at : 05/01/2017
    */
    function get_field_suggetion_values($param = array()) {
        $field_id = isset($param['field_id']) ? $param['field_id'] : false;
        $keyword  = isset($param['keyword']) ? $param['keyword'] : false;
        $return   = array();
        if ($field_id && $keyword) {
            $query  = "SELECT us_profile_fields FROM users WHERE us_profile_fields LIKE '%{{" . $field_id . "{=>}%" . $keyword . "%}}%'";
            $fields = $this->db->query($query)->result_array();
            if (!empty($fields)) {
                //echo '<pre>'; print_r($fields);die;
                foreach ($fields as $p_field) {
                    $old_value = isset($p_field['us_profile_fields']) ? explode('{#}', $p_field['us_profile_fields']) : array();
                    if (!empty($old_value)) {
                        foreach ($old_value as $field) {
                            $field      = substr($field, 2);
                            $field      = substr($field, 0, -2);
                            $temp_field = explode('{=>}', $field);
                            $key        = isset($temp_field[0]) ? $temp_field[0] : 0;
                            $value      = isset($temp_field[1]) ? trim($temp_field[1]) : '';
                            //echo $key.'==>'.$field_id.'---'.$value.'<pre>';print_r($return);
                            if ($key == $field_id && (!in_array($value, $return))) {
                                $return[] = $value;
                                break;
                            }
                        }
                    }
                }
            }
        }
        //echo '<pre>'; print_r($return);die;
        return $return;
        /* $this->db->select('upf_field_id,upf_field_value');
          $this->db->from('profile_field_values');
          $this->db->where('upf_field_id = ', $field_id);
          $this->db->like('upf_field_value' , $keyword);
          $this->db->group_by('upf_field_value');
          echo $this->db->last_query();die;
          return $this->db->get()->result_array(); */
    }
    /*
    * Function to check whether a autosuggestion is enabled for a particular field or not
    * Created by :Neehu KP
    * Created at : 05/01/2017
    */
    function check_autosuggestion_status($pf_id){
        $this->db->select('pf_auto_suggestion');
        $this->db->where('id =' ,$pf_id );
        $this->db->from('profile_fields');
        return $this->db->get()->row_array();
        
    }
    public function get_registered($param){
        $this->db->select('us_email');
        $this->db->where('us_account_id',config_item('id'));
        $this->db->where_in('us_email',$param['emails']);
        if(isset($param['role_id'])){
            $this->db->where('us_role_id',$param['role_id']);
        }
        if(isset($param['delete'])){
            $this->db->where('us_deleted',$param['delete']);
        }
        return $this->db->get('users')->result_array();
    }

    public function get_user_by_role($param){
        $this->db->select('id');
        if(isset($param['role_id'])){
            $this->db->where('us_role_id',$param['role_id']);
        }
        $this->db->where('us_account_id', config_item('id'));
        return $this->db->get('users')->result_array();
    }

    //By alex
    function institutes($param = array()){
        if(isset($param['select'])){
            $this->db->select($param['select']);
        }else{
            $this->db->select('users.id,users.us_name,users.us_email,users.us_image,users.us_institute_code,users.created_date,users.updated_date');
        }
        if(isset($param['id'])){
            $this->db->where('id',$param['id']);
        }
        $this->db->where('us_deleted','0');
        $this->db->where('us_status','1');
        $this->db->where('us_role_id','8');
        $this->db->where('us_account_id',config_item('id'));
        if(isset($param['id'])){
            $result = $this->db->get('users')->row_array();
        }else{
            if(isset($param['count'])){
                $result = $this->db->get('users')->num_rows();
            }else{
                $result = $this->db->get('users')->result_array();
            }
        }
        return $result;
    }
    function check_email($param = array()){

        $email      = isset($param['email'])?$param['email']:false;
        $select     = isset($param['select'])?$param['select']:'id,us_name';
        $account_id = isset($param['account_id'])?$param['account_id']:true;
        $limit      = isset($param['limit'])?$param['limit']:false;
        $phone      = isset($param['phone'])?$param['phone']:false;
        $this->db->select($select);

        $this->db->where('us_account_id',config_item('id'));
        
        if($limit)
        {
            $this->db->limit($limit);
        }
        if($email)
        {
            $this->db->where('us_email',$email);
        }
        if($phone)
        {
            $this->db->where('us_phone',$phone);
        }
        $response = $this->db->get('users')->num_rows();
        return $response;
    }
    function get_course_assessments($param = array()){
        if(isset($param['select'])){
            $this->db->select($param['select']);
        }else{
            $this->db->select('course_lectures.id,course_lectures.cl_lecture_name,assessments.id AS assessment_id,assessment_attempts_cp.aa_duration,assessment_attempts_cp.aa_attempted_date, assessment_report_cp.mark ');
        }
        $this->db->where('course_lectures.cl_lecture_type','3');
        $this->db->where('course_lectures.cl_deleted','0');
        $this->db->where('course_lectures.cl_status','1');
        if(isset($param['course_id'])){
            $this->db->where('course_lectures.cl_course_id',$param['course_id']);
        }
        $this->db->order_by('course_lectures.id','ASC');
        $this->db->join('assessments','course_lectures.id = assessments.a_lecture_id');
        $this->db->join('(SELECT assessment_attempts.*
                            FROM (SELECT aa_assessment_id, aa_user_id, max(aa_attempted_date) AS aa_attempted_date
                                    FROM assessment_attempts assessment_attempts_cp
                                    GROUP BY CONCAT(aa_assessment_id, "_", aa_user_id)
                                 ) assessment_attempts_cp 
                            LEFT JOIN assessment_attempts ON assessment_attempts.aa_attempted_date = assessment_attempts_cp.aa_attempted_date AND assessment_attempts.aa_assessment_id = assessment_attempts_cp.aa_assessment_id AND assessment_attempts.aa_user_id = assessment_attempts_cp.aa_user_id 
                            WHERE assessment_attempts.aa_user_id = '.$param["user_id"].' 
                            ORDER BY assessment_attempts.aa_attempted_date ASC ) assessment_attempts_cp','assessments.id = assessment_attempts_cp.aa_assessment_id');
        $this->db->join('(SELECT SUM(assessment_report_cp.ar_mark) AS mark,assessment_report_cp.ar_attempt_id FROM assessment_report assessment_report_cp GROUP BY assessment_report_cp.ar_attempt_id) assessment_report_cp','assessments.id = assessment_report_cp.ar_attempt_id','LEFT');
        $result = $this->db->get('course_lectures')->result_array();
        //echo $this->db->last_query();
        return $result;
    }
    function topic_wise_progress($param = array()){
        $course_query = '';
        if(isset($param['course_id']) && $param['course_id'] > 0 )
        {
            $course_query = ' cl_course_id IN (SELECT cs_course_id  FROM course_subscription WHERE cs_course_id = '.$param["course_id"].') AND ';
        }
        if(isset($param['select'])){
            $this->db->select($param['select']);
        }else{
            $this->db->select('questions_category.id,questions_category.qc_category_name,COUNT(question_id) AS questions,AVG(assessment_report_cp.ar_duration) AS duration,SUM(questions_cp.q_positive_mark) AS total_mark,SUM(assessment_report_cp.ar_mark) AS scored_mark');
        }
        $this->db->join('(SELECT questions_cp.id AS question_id,questions_cp.q_category,questions_cp.q_positive_mark FROM questions questions_cp WHERE id IN (SELECT id FROM assessment_questions WHERE aq_assesment_id IN(SELECT id FROM assessments WHERE a_lecture_id IN (SELECT id FROM course_lectures WHERE '.$course_query.' cl_lecture_type = 3 ))) ORDER BY questions_cp.id) questions_cp','questions_category.id = questions_cp.q_category','LEFT');
        
        $this->db->join('(SELECT assessment_report_cp.ar_question_id,assessment_report_cp.ar_mark,assessment_report_cp.ar_duration FROM assessment_report assessment_report_cp WHERE assessment_report_cp.ar_attempt_id IN (
            SELECT assessment_attempts.id
            FROM (SELECT aa_assessment_id, aa_user_id, max(aa_attempted_date) AS aa_attempted_date
                    FROM assessment_attempts assessment_attempts_cp
                    GROUP BY CONCAT(aa_assessment_id, "_", aa_user_id)
                 ) assessment_attempts_cp 
            LEFT JOIN assessment_attempts ON assessment_attempts.aa_attempted_date = assessment_attempts_cp.aa_attempted_date AND assessment_attempts.aa_assessment_id = assessment_attempts_cp.aa_assessment_id AND assessment_attempts.aa_user_id = assessment_attempts_cp.aa_user_id 
            WHERE assessment_attempts.aa_user_id = "'.$param["user_id"].'"
            ORDER BY assessment_attempts.aa_attempted_date ASC 
            ) AND assessment_report_cp.ar_mark > 0) assessment_report_cp','question_id = assessment_report_cp.ar_question_id','LEFT');
        $this->db->group_by('questions_category.id');
        $this->db->order_by('questions_category.qc_category_name','ASC'); 
        $this->db->having('total_mark>0');
        $result = $this->db->get('questions_category')->result_array();
        //echo $this->db->last_query();
        return $result;
    }
    function assessment_details($param =array()){
        $this->db->select('assessments.id AS assessment_id,SUM(questions.q_positive_mark) AS total_mark,COUNT(questions.id) AS question_count');
        
        $this->db->join('assessment_questions','assessments.id=assessment_questions.aq_assesment_id','LEFT');
        $this->db->join('questions','assessment_questions.aq_question_id=questions.id','LEFT');
        
        if(isset($param['lecture_id'])){
            $this->db->where('assessments.a_lecture_id',$param['lecture_id']);
        }
        $result = $this->db->get('assessments')->row_array();
        return $result;
    }
    function assessment_attempt_details($param =array()){
        $this->db->select('assessments.id AS assessment_id,SUM(assessment_report.ar_mark) AS scored_mark,assessment_attempts_cp.id AS attemp_id');
        
        $this->db->join('(SELECT assessment_attempts.*
                            FROM (SELECT aa_assessment_id, aa_user_id, max(aa_attempted_date) AS aa_attempted_date
                                    FROM assessment_attempts assessment_attempts_cp
                                    GROUP BY CONCAT(aa_assessment_id, "_", aa_user_id)
                                 ) assessment_attempts_cp 
                            LEFT JOIN assessment_attempts ON assessment_attempts.aa_attempted_date = assessment_attempts_cp.aa_attempted_date AND assessment_attempts.aa_assessment_id = assessment_attempts_cp.aa_assessment_id AND assessment_attempts.aa_user_id = assessment_attempts_cp.aa_user_id 
                            WHERE assessment_attempts.aa_user_id = '.$param["user_id"].' 
                            ORDER BY assessment_attempts.aa_attempted_date ASC ) assessment_attempts_cp','assessments.id = assessment_attempts_cp.aa_assessment_id','left');
        $this->db->join('assessment_report','assessment_attempts_cp.id=assessment_report.ar_attempt_id','LEFT');
        
        if(isset($param['lecture_id'])){
            $this->db->where('assessments.a_lecture_id',$param['lecture_id']);
        }
        $result = $this->db->get('assessments')->row_array();
        return $result;
    }
    function descriptive_details($param =array()){
        $this->db->select('descrptive_tests.id AS test_id,descrptive_tests.dt_total_mark,descrptive_test_user_answered.mark AS scored_mark');
        
        $this->db->join('descrptive_test_user_answered','descrptive_tests.dt_lecture_id=descrptive_test_user_answered.dtua_lecture_id','LEFT');
        
        if(isset($param['lecture_id'])){
            $this->db->where('descrptive_tests.dt_lecture_id',$param['lecture_id']);
        }
        if(isset($param['user_id'])){
            $this->db->where('descrptive_test_user_answered.dtua_user_id',$param['user_id']);
        }
        $result = $this->db->get('descrptive_tests')->row_array();
        return $result;
    }
    function get_lecture_log($param = array()){
        $this->db->select('*');
        if(isset($param['user_id'])){
            $this->db->where('ll_user_id',$param['user_id']);
        }
        if(isset($param['lecture_id'])){
            $this->db->where('ll_lecture_id',$param['lecture_id']);
        }
        $result = $this->db->get('lecture_log')->row_array();
        return $result;
    }
    function add_lecture_log($param = array()){
        $return = 0;
        if(isset($param['id'])){
            $this->db->where('id',$param['id']);
            $this->db->update('lecture_log',$param['values']);
        }else{
            $this->db->insert('lecture_log',$param['values']);
            $return = $this->db->insert_id();
        }
        return $return;
    }
    function assessment_from_attempt($param = array()){
        $this->db->select('assessment_attempts.id AS attempt_id,assessment_attempts.aa_user_id,assessments.a_lecture_id');
        if(isset($param['attempt_id'])){
            $this->db->where('assessment_attempts.id',$param['attempt_id']);
        }
        $this->db->join('assessments','assessment_attempts.aa_assessment_id=assessments.id','left');
        $result = $this->db->get('assessment_attempts')->row_array();
        return $result;
    }
    function get_assignment_datewise($param = array()){
        $user_id        = isset($param['user_id'])?$param['user_id']:0;
        $from_date      = isset($param['from'])?$param['from']:'0000-00-00';
        $to_date        = isset($param['to'])?$param['to']:'0000-00-00';
        $dt_status      = isset($param['status'])? $param['status'] : false;
        /*$query = 'SELECT course_lectures_cp.id,course_lectures_cp.cl_lecture_name,course_lectures_cp.cl_course_id,course_lectures_cp.cl_lecture_type,course_lectures_type.clt_name,descrptive_tests.dt_last_date,descrptive_test_user_answered.created_date AS submitted_date,descrptive_test_user_answered.mark,CONCAT(descrptive_tests.dt_last_date," 23:59:59") AS descrptive_date_time 
            FROM (SELECT * 
            FROM course_lectures course_lectures_cp 
            WHERE cl_course_id IN (SELECT cs_course_id FROM course_subscription WHERE cs_user_id="'.$user_id.'" AND (cs_course_validity_status="0" OR (cs_end_date > now()))) 
            AND cl_lecture_type IN ("8")
            ) course_lectures_cp 
            LEFT JOIN course_lectures_type ON course_lectures_cp.cl_lecture_type = course_lectures_type.id 
            LEFT JOIN descrptive_tests ON course_lectures_cp.id = descrptive_tests.dt_lecture_id 
            LEFT JOIN descrptive_test_user_answered ON descrptive_tests.dt_lecture_id = descrptive_test_user_answered.dtua_lecture_id AND descrptive_test_user_answered.dtua_user_id = "'.$param["user_id"].'"
            WHERE (descrptive_tests.dt_last_date BETWEEN "'.$from_date.'" AND "'.$to_date.'")';
        */
        if($dt_status !== false)
        {
            $query = 'SELECT descrptive_tests.dt_lecture_id as id, dt_name as cl_lecture_name, dt_course_id as cl_course_id, CONCAT(descrptive_tests.dt_last_date," 23:59:59") AS descrptive_date_time, descrptive_tests.dt_last_date  
                        FROM descrptive_tests 
                        WHERE descrptive_tests.dt_status = "'.$dt_status.'" and descrptive_tests.dt_course_id IN (SELECT cs_course_id FROM course_subscription WHERE cs_user_id="'.$user_id.'" AND cs_approved="1" AND cs_end_date >= "'.date('Y-m-d').'") AND descrptive_tests.dt_last_date BETWEEN "'.$from_date.'" AND "'.$to_date.'"';
        }
        else
        {
            $query = 'SELECT descrptive_tests.dt_lecture_id as id, dt_name as cl_lecture_name, dt_course_id as cl_course_id, CONCAT(descrptive_tests.dt_last_date," 23:59:59") AS descrptive_date_time, descrptive_tests.dt_last_date  
                    FROM descrptive_tests 
                    WHERE descrptive_tests.dt_course_id IN (SELECT cs_course_id FROM course_subscription WHERE cs_user_id="'.$user_id.'" AND cs_approved="1" AND cs_end_date >= "'.date('Y-m-d').'") AND descrptive_tests.dt_last_date BETWEEN "'.$from_date.'" AND "'.$to_date.'"';
     
        }
        $result = $this->db->query($query)->result_array();
        // echo $this->db->last_query();die;
        return $result;
    }

    function get_live_datewise($param = array()){
        $user_id        = isset($param['user_id'])?$param['user_id']:0;
        $from_date      = isset($param['from'])?$param['from']:'0000-00-00';
        $to_date        = isset($param['to'])?$param['to']:'0000-00-00';
        /*$query = 'SELECT course_lectures_cp.id,course_lectures_cp.cl_lecture_name,course_lectures_cp.cl_lecture_type,course_lectures_type.clt_name,live_lectures.ll_mode,live_lectures.id AS live_id,live_lectures.ll_date,live_lectures.ll_time,CONCAT(live_lectures.ll_date," ",live_lectures.ll_time) AS live_date_time 
            FROM (SELECT * 
            FROM course_lectures course_lectures_cp 
            WHERE cl_course_id IN (SELECT cs_course_id FROM course_subscription WHERE cs_user_id="'.$user_id.'" AND (cs_course_validity_status="0" OR (cs_end_date > now()))) 
            AND cl_lecture_type IN ("7")
            ) course_lectures_cp
            LEFT JOIN course_lectures_type ON course_lectures_cp.cl_lecture_type = course_lectures_type.id 
            LEFT JOIN live_lectures ON course_lectures_cp.id = live_lectures.ll_lecture_id
            WHERE (live_lectures.ll_date BETWEEN "'.$from_date.'" AND "'.$to_date.'")';*/
        $query = 'SELECT course_lectures.id, course_lectures.cl_course_id,course_lectures.cl_lecture_name, live_lectures.ll_date, CONCAT(live_lectures.ll_date," ",live_lectures.ll_time) AS live_date_time, ll_mode, live_lectures.id AS live_id
                    FROM live_lectures 
                    LEFT JOIN course_lectures ON live_lectures.ll_lecture_id = course_lectures.id 
                    WHERE live_lectures.ll_course_id IN (SELECT cs_course_id FROM course_subscription WHERE cs_user_id="'.$user_id.'" AND cs_approved="1" AND cs_end_date >= "'.date('Y-m-d').'") AND (live_lectures.ll_date BETWEEN "'.$from_date.'" AND "'.$to_date.'")';
        $result = $this->db->query($query)->result_array();
        // echo $this->db->last_query();die;
        return $result;
    }

/*
    function assignment_ondate($param = array()){
        $query = 'SELECT course_lectures_cp.id,course_lectures_cp.cl_lecture_name,course_lectures_cp.cl_lecture_type,descrptive_tests.dt_last_date,descrptive_test_user_answered.created_date AS submitted_date,descrptive_test_user_answered.mark
        FROM (SELECT * 
                FROM course_lectures course_lectures_cp 
                WHERE cl_course_id IN (SELECT cs_course_id FROM course_subscription WHERE cs_user_id="'.$param["user_id"].'" AND (cs_course_validity_status="0" OR (cs_end_date > now()))) 
                AND cl_lecture_type IN ("7","8")
            ) course_lectures_cp 
        LEFT JOIN descrptive_tests ON course_lectures_cp.id = descrptive_tests.dt_lecture_id 
        LEFT JOIN descrptive_test_user_answered ON descrptive_tests.dt_lecture_id = descrptive_test_user_answered.dtua_lecture_id AND descrptive_test_user_answered.dtua_user_id = "'.$param["user_id"].'"
        WHERE descrptive_tests.dt_last_date = "'.$param["date"].'"';
        $result = $this->db->query($query)->result_array();
        return $result;
    }
*/
    function get_assignment_courses($param = array()){
        $user_id   = isset($param['user_id'])?$param['user_id']:0;
        $this->db->select('course_basics.id,course_basics.cb_title,course_basics.cb_image');
        $this->db->join('course_lectures','course_subscription.cs_course_id = course_lectures.cl_course_id','left');
        $this->db->join('course_basics','course_lectures.cl_course_id = course_basics.id','left');
        $this->db->where('course_subscription.cs_user_id',$user_id);
        $this->db->where('cs_end_date > CURDATE() AND cs_approved = "1" AND course_lectures.cl_lecture_type = "8" AND course_lectures.cl_deleted = "0" AND course_lectures.cl_status = "1"');
        $this->db->order_by('course_basics.cb_title');
        $this->db->group_by('course_basics.id');
        $result = $this->db->get('course_subscription')->result_array();
        return $result;
    }
 
    function get_asseignments($param = array()){
        $user_id   = isset($param['user_id'])?$param['user_id']:0;
        $course_id = isset($param['course_id'])?$param['course_id']:0;
        $this->db->select('course_lectures.id,course_lectures.cl_course_id,course_lectures.cl_lecture_name AS a_name,descrptive_tests.dt_last_date AS last_date,descrptive_tests.dt_total_mark AS alloted_mark,descrptive_test_user_answered.created_date AS submitted_date,descrptive_test_user_answered.mark AS scored_mark');
        $this->db->join('descrptive_tests','course_lectures.id = descrptive_tests.dt_lecture_id','left');
        $this->db->join('descrptive_test_user_answered','descrptive_tests.dt_lecture_id = descrptive_test_user_answered.dtua_lecture_id AND descrptive_test_user_answered.dtua_user_id = "'.$user_id.'"','left');
        //$this->db->where('descrptive_test_user_answered.dtua_user_id',$user_id);
        $this->db->where('course_lectures.cl_course_id',$course_id);
        $this->db->where('course_lectures.cl_lecture_type = "8" AND course_lectures.cl_deleted = "0" AND course_lectures.cl_status = "1"');
        
        $result = $this->db->get('course_lectures')->result_array();
        //echo $this->db->last_query();
        return $result;
    }
    function read_question_data($param = array()){
        $this->db->select('id,q_type,q_positive_mark,q_negative_mark,q_explanation,q_options,q_answer');
        $this->db->where('id',$param['question_id']);
        $this->db->where('q_account_id',config_item('id'));
        $result = $this->db->get('questions')->row_array();
        return $result;
    }
    function read_question_options($param = array()){
        $this->db->select('id,qo_options');
        $this->db->where_in('id',$param['options']);
        $result = $this->db->get('questions_options')->result_array();
        return $result;   
    }
    //End By alex
    // For online test by santhosh
    function user_enrolled_plans($param = array()){
        $select             = isset($param['select'])?$param['select']:false;
        $user_id            = isset($param['user_id'])?$param['user_id']:false;
        $active_plan        = isset($param['active_plan'])?$param['active_plan']:false;
        $subscription_id    = isset($param['id'])?$param['id']:false;
        if($select){
            $this->db->select($select);
        }else{
            $this->db->select('user_plan.id,plans.id AS plan_id,user_plan.up_plan_id,user_plan.up_user_id,user_plan.up_plan_validity_type,DATE_FORMAT(user_plan.up_start_date, "%d-%M-%Y") AS up_start_date,DATE_FORMAT(user_plan.up_end_date, "%d-%M-%Y") AS up_end_date,user_plan.up_status,user_plan.up_active_plan,plans.p_name, plans.p_slogan,DATE_FORMAT(user_plan.updated_date, "%d-%M-%Y") AS updated');
        }
        if($user_id){
            $this->db->where('user_plan.up_user_id',$user_id);
        }
        if($active_plan){
            $this->db->where('user_plan.up_active_plan','1');
        }
        if($subscription_id){
            $this->db->where('user_plan.id',$subscription_id);
        }
        $this->db->join('plans','user_plan.up_plan_id = plans.id','left');
        $this->db->where('user_plan.up_account_id',config_item('id'));
        if($subscription_id || $active_plan){
            $result     = $this->db->get('user_plan')->row_array();
        }else{
            $result     = $this->db->get('user_plan')->result_array();
        }
        //echo $this->db->last_query();die;
        return $result;   
    }
    function user_plans($param = array()){
        $select             = isset($param['select'])?$param['select']:false;
        $user_id            = isset($param['user_id'])?$param['user_id']:false;
        $plan_id            = isset($param['plan_id'])?$param['plan_id']:false;
        $active_plan        = isset($param['active_plan'])?$param['active_plan']:false;
        if($select){
            $this->db->select($select);
        }else{
            $this->db->select('user_plan.id,user_plan.up_user_id,user_plan.up_plan_id');
        }
        if($active_plan){
            $this->db->where('user_plan.up_active_plan','1');
        }
        if($user_id){
            $this->db->where('user_plan.up_user_id',$user_id);
        }
        if($plan_id){
            $this->db->where('user_plan.up_plan_id',$plan_id);
        }
        $this->db->where('user_plan.up_account_id',config_item('id'));
        $result             = array();
        if($user_id){
            $result             = $this->db->get('user_plan')->row_array();
        }else{
            $result             = $this->db->get('user_plan')->result_array();
        }
        return $result;
    }
    function save_token($param = array()){
        $insert         = isset($param['id'])?false:true;
        if($insert){
            $result = $this->db->insert('email_token',$param);
        }else{
            $this->db->where('id',$param['id']);
            $this->db->update('email_token',$param);
            $result = $param['id'];
        }
        return $result;
    }
    function get_token($param = array()){
        $token          = isset($param['token'])?$param['token']:'';
        $this->db->select('*');
        $this->db->where('et_account_id',config_item('id'));
        $this->db->where('et_status','1');
        $this->db->where('et_token', $token);
        $result     = $this->db->get('email_token')->row_array();
        //echo $this->db->last_query();die;
        return $result;
    }
    public function unapproved_user()
    {
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where(array('us_role_id' => '2', 'us_deleted' => '0', 'us_status' => '2'));
        $query = $this->db->get();
        return $query->num_rows();
    }
    public function user_role_permission($param)
    {
        $keywords  = isset($param['permissions']) ? $param['permissions'] : false;
        $module_id = isset($param['module_id']) ? $param['module_id'] : false;
        $this->db->select('role_id');
        $this->db->from('roles_modules_meta');
        $this->db->where(array('module_id' => $module_id));
        $this->db->like('permissions', $keywords, 'both');
        $query = $this->db->get();
        return $query->result_array();
    }
    public function modules($param){
        $module_name = isset($param['module_name']) ? $param['module_name'] : false;
        $select      = isset($param['select']) ? $param['select'] : false;
        $this->db->select($select);
        $this->db->from('modules');
        $this->db->where(array('controller' => $module_name));
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row_array();
    }
    
    public function user_emails($user_roles){
        $this->db->select('us_email');
        $this->db->from('users');
        $this->db->where(array('us_deleted' => '0','us_status' => '1','us_email_verified'=>'1'));
        $this->db->where_in('us_role_id', $user_roles);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function user_roles($param=array()){
        $role_id=isset($param['role_id'])?$param['role_id']:false;
        $this->db->from('roles');
        if($role_id){
            $this->db->where(array('id'=>$role_id));
        }
        $query = $this->db->get();
        return $query->result_array();
    }
    public function score($param = array()){
        $user_id = isset($param['user_id'])?$param['user_id']:0;
        $this->db->where('lap_user_id',$user_id);
        $result = $this->db->get('log_activity_points')->row_array();
        return $result;
    }

    function users_by_columns( $param = array() )
    {
        $select             = isset($param['select'])?$param['select']:'id';
        $email_ids          = isset($param['email_ids'])?$param['email_ids']:array();
        $register_numbers   = isset($param['register_numbers'])?$param['register_numbers']:array();
        $us_phone           = isset($param['us_phone'])?$param['us_phone']:array();
        
        $this->db->select($select);
        if(!empty($email_ids))
        {
            $this->db->where_in('us_email', $email_ids);
        }
        if(!empty($register_numbers))
        {
            $this->db->where_in('us_register_number', $register_numbers);
        }
        if(!empty($us_phone))
        {
            $this->db->where_in('us_phone', $us_phone);
        }
        $this->db->where('us_account_id', config_item('id'));
        $return = $this->db->get('users')->result_array();
        // echo $this->db->last_query();die;
        return $return;
    }

    function group_courses($param = array())
    {
        $select = isset($param['select'])?$param['select']:'id';
        $this->db->select($select);
        $this->db->like('concat(",", cb_groups, ",")', ','.$param['group_id'].',');
        $result = $this->db->get('course_basics')->result_array();	
        return $result;
    } 

    function insert_users_bulk($save_students)
    {
        $students_chunks  = array_chunk($save_students, 100);
        $result           = array();
        if(!empty($students_chunks))
        {
            foreach($students_chunks as $students)
            {
                $this->db->trans_start();
                foreach($students as $student)
                {
                    $this->db->insert('users', $student);
                    $student['id']  = $this->db->insert_id();
                    $result[]       = $student;
                }
                $this->db->trans_complete(); 
            }
        }
        return $result;
    }

    function update_users_bulk($update_students)
    {
        $students_chunks  = array_chunk($update_students, 100);
        if(!empty($students_chunks))
        {
            foreach($students_chunks as $students)
            {
                $this->db->trans_start();
                foreach($students as $student)
                {
                    $this->db->where('id', $student['id']);
                    $this->db->update('users', $student);
                }
                $this->db->trans_complete(); 
            }
        }
    }

    function save_token_bulk($tokens_insert)
    {
        $token_chunks   = array_chunk($tokens_insert, 100);
        if(!empty($token_chunks))
        {
            foreach($token_chunks as $tokens)
            {
                $this->db->trans_start();
                foreach($tokens as $token)
                {
                    $this->db->insert('email_token',$token);
                }
                $this->db->trans_complete(); 
            }
        }
    }

    function save_subscription_bulk($subscription_insert)
    {
        $subscriptions_chunks   = array_chunk($subscription_insert, 100);
        if(!empty($subscriptions_chunks))
        {
            foreach($subscriptions_chunks as $subscriptions)
            {
                $this->db->trans_start();
                foreach($subscriptions as $subscription)
                {
                    $subscription['cs_account_id'] =  config_item('id');
                    $this->db->insert('course_subscription', $subscription);
                }
                $this->db->trans_complete(); 
            }
        }
    }

    function profile_fields()
    {
        $return = $this->db->query('SELECT id, pf_label FROM profile_fields WHERE pf_mandatory = "1" AND pf_account_id="'.config_item('id').'"')->result_array();
        return $return;
    }
    public function check_if_exist($param = array())
    {
        $select     = isset($param['select'])?$param['select']:'*';
        $id         = isset($param['user_id'])?$param['user_id']:'*';
        $this->db->select($select);
        if($id)
        {
            $this->db->where('users.id',$id);
        }
        $this->db->where('users.us_account_id', $this->config->item('id'));
        $this->db->join('roles', 'roles.id = users.us_role_id', 'left');
        $this->db->limit('1');
        $this->db->from('users');
        $result     = $this->db->get();
        $response   = $result->row_array();
        return $response;
    }
    public function save_userdata($data,$param)
    {
        $id             = isset($param['id'])?$param['id']:'0';
        $update         = isset($param['update'])?$param['update']:false;
        $email          = isset($param['email'])?$param['email']:false;
        $phone          = isset($param['phone'])?$param['phone']:false;
        $institute_id   = isset($param['institute_id'])?$param['institute_id']:false;
        $ids            = isset($param['ids'])?$param['ids']:false;
        
        if($update)
        {
            if($institute_id)
            {
                $this->db->where('us_institute_id', $institute_id);
            }
            if($id)
            {
                $this->db->where('id', $id);
            }
            if($ids)
            {
                $this->db->where_in('id', $ids);
            }
            if($email)
            {
                $this->db->where('us_email', $email);
            }
            if($phone)
            {
                $this->db->where('us_phone', $phone);
            }
            $this->db->update('users', $data);
            return $id;
        }
        else
        {
            $this->db->insert('users', $data);
            return $this->db->insert_id();
        }
    }

    public function delete_users($ids=array())
    {
        $this->db->where_in('id', $ids);
        $this->db->delete('users');
        return true;
    }

    function verify_phone_number( $data )
    {
        $this->db->where('us_phone', $data['us_phone']);
        $this->db->update('users', $data);
        return true;
    }
    public function check_user_exist($param = array())
    {
        $email      = isset($param['email'])?$param['email']:false;
        $select     = isset($param['select'])?$param['select']:'id,us_name';
        $limit      = isset($param['limit'])?$param['limit']:false;
        $phone      = isset($param['phone'])?$param['phone']:false;
        $this->db->select($select);

        if($limit)
        {
            $this->db->limit($limit);
        }
        if($email)
        {
            $this->db->where('us_email',$email);
        }
        if($phone)
        {
            $this->db->where('us_phone',$phone);
        }
        $this->db->where('us_account_id', config_item('id'));
        $response = $this->db->get('users')->row_array();
        return $response;
    }
    public function remove_user($params = array())
    {
        $this->db->where('id',$params['id']);
        $this->db->delete('users');

        $this->db->where('cs_user_id',$params['id']);
        $this->db->delete('course_subscription');

        $this->db->where('bs_user_id',$params['id']);
        $this->db->delete('bundle_subscription');

        $this->db->where('sa_user_id',$params['id']);
        $this->db->delete('subscription_archive');
    }
    public function get_all_banners()
    {
        $this->db->where('mb_account_id',config_item('id'));
        $this->db->where('mb_status',1);
        $this->db->order_by('mb_order', 'ASC');
        $this->db->select('id,mb_converted_title,');
        $this->db->from('mobile_banners');
        $data = $this->db->get();
        $banners = $data->result_array();
        return $banners;
    }
}
?>
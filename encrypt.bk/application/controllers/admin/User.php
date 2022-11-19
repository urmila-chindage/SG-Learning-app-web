<?php
class User extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        
        $teacher_methods            = array('profile', 'language');
        $this->__role_query_filter  = array();
        $this->__loggedInUser       = $this->auth->get_current_user_session('admin');

        $redirect	                = $this->auth->is_logged_in(false, false);
        if (!$redirect)
        {
            redirect('login');
        }
        $this->actions                      = $this->config->item('actions');
        $this->load->model(array('Group_model','User_model', 'Course_model', 'Mailtemplate_model', 'Institute_model','Bundle_model'));
        $this->lang->load('user');
        $this->__limit                      = 100;
        $this->privilege                    = array('view' => 1, 'add' => 2, 'edit' => 3, 'delete' => 4);
        $this->student_enroll_privilege     = $this->accesspermission->get_permission(array('role_id' => $this->__loggedInUser['role_id'],'module' => 'student_enrollment'));
        $this->batch_enroll_privilege       = $this->accesspermission->get_permission(array('role_id' => $this->__loggedInUser['role_id'],'module' => 'batch_enrollment'));
        $this->user_privilege               = $this->accesspermission->get_permission(array('role_id' => $this->__loggedInUser['role_id'],'module' => 'user'));
        $this->course_privilege             = $this->accesspermission->get_permission(array('role_id' => $this->__loggedInUser['role_id'], 'module' => 'course'));
        $this->event_privilege              = $this->accesspermission->get_permission(array('role_id' => $this->__loggedInUser['role_id'],'module' => 'event'));
        //processing permission
        if($this->__loggedInUser['us_role_id'] == 8)
        {
            $this->__role_query_filter['institute_id'] = $this->__loggedInUser['us_institute_id'];
        }
       
        //end

    }
    
    function index()
    {
        if(!in_array('1', $this->user_privilege))
        {
            redirect(admin_url()); exit;
        }
        $this->memcache->delete('insbtch'.$this->__loggedInUser['id']);
        $data                           = array();
        $breadcrumb                     = array();
        $breadcrumb[]                   = array( 'label' => 'Home', 'link' => admin_url(''), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]                   = array( 'label' => lang('manage_users'), 'link' => admin_url('user'), 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']             = $breadcrumb;
        $data['title']                  = lang('users');
        $data['show_load_button']       = false;
        $data['limit']                  = $this->__limit;
        $offset                         = isset($_GET['offset'])?$_GET['offset']:0;
        $user_param                     = $this->__role_query_filter;
        $user_param['order_by']         = 'id';
        $user_param['direction']        = 'DESC';
        $user_param['role_id']          = '2';
        $user_param['count']            = true;

        $user_param['filter']           = ($this->input->get('filter') != null)? $this->input->get('filter') : 'active';
        if(!isset($this->__role_query_filter['institute_id'])){
            $user_param['institute_id'] = ($this->input->get('institute_id') != null)? $this->input->get('institute_id'): '';
        }
        $user_param['branch']           = ($this->input->get('branch_id') != null)? $this->input->get('branch_id'): '';
        $user_param['batch_id']         = ($this->input->get('batch_id') != null)? $this->input->get('batch_id') : '';
        if($this->input->get('keyword') != null)
        {
            $keyword_arr                = explode('-', $this->input->get('keyword'));
            $keyword                    = implode(' ',$keyword_arr);
            $user_param['keyword']      = $keyword;
        }
        $this->load->model('User_model');
        $data['total_users']            = $this->User_model->users($user_param);
    
        unset($user_param['count']);
        $user_param['limit']            = $this->__limit;
        
        //processing pagination
        $page                           = $offset;
        if( $page === NULL || $page <= 0 )
        {
            $page                       = 1;
        }
        $page                           = ($page - 1)* $this->__limit;
        //end
        $user_param['offset']           = $page;
        $user_param['select']           = 'id, us_name, us_phone, us_email, us_image, us_role_id, us_institute_id, us_branch, us_branch_code, us_institute_code, us_status, us_degree, us_deleted';
        $data['users']                  = $this->User_model->users($user_param);
        
        if($data['total_users'] > $this->__limit)
        {
            $data['show_load_button']   = true;            
        }

        //Read institutes form memcached.
        $objects                        = array();
        $objects['key']                 = 'institutes';
        $callback                       = 'institutes';
        $institutes                     = $this->memcache->get($objects, $callback, array()); 

        //Read branches form memcached.
        $objects                        = array();
        $objects['key']                 = 'branches';
        $callback                       = 'branches';
        $branches                       = $this->memcache->get($objects, $callback, array()); 

        $data['branches']               = $branches;
        $data['institutes']             = $institutes;  
        $data['admin']                  = $this->__loggedInUser;
        
        $data['batch_enroll_privilege']     = $this->batch_enroll_privilege;
        $data['user_privilege']             = $this->user_privilege;
        $data['student_enroll_privilege']   = $this->student_enroll_privilege;
        $data['batches']                    = array();
        if( $user_param['institute_id'] != '' )
        {
            $this->load->model('Group_model');
            $param                      = array();
            $param['institute_id']      = $user_param['institute_id'];
            $param['select']            = 'id, CONCAT(gp_institute_code," - ",gp_year," - ",gp_name) as batch_name';
            $data['batches']            = $this->Group_model->groups($param);
        }
        $data['profiles']               = $this->User_model->profile_fields();
        //echo '<pre>'; print_r($data);die;
        $this->load->view($this->config->item('admin_folder').'/users', $data);
    }
    
    function users_json()
    {
        $data                       = array();
        $data['show_load_button']   = false;
        $user_param                 = $this->__role_query_filter;
        
        $limit            = $this->__limit;
        $data['limit']    = $limit;
        $offset           = $this->input->post('offset');
        $page             = $offset;
        if($page===NULL||$page<=0)
        {
            $page         = 1;
        }
        $page             = ($page - 1)* $limit;

        $user_param['order_by']       = 'id';
        $user_param['direction']      = 'DESC';
        if(!isset($this->__role_query_filter['institute_id'])){
            $user_param['institute_id']   = $this->input->post('institute_id');
        } else {
            $user_param['institute_id']   = $this->__role_query_filter['institute_id'];
        }
        $user_param['branch']         = $this->input->post('branch_id');
        $user_param['batch_id']       = $this->input->post('batch_id');
        $user_param['keyword']        = $this->input->post('keyword');
        $user_param['filter']         = $this->input->post('filter');
        $user_param['status']         = $this->input->post('status');
        $user_param['not_deleted']    = $this->input->post('not_deleted');
        $user_param['not_subscribed'] = $this->input->post('not_subscribed');
        $user_param['role_id']        = '2';
        $user_param['count']          = true;
        
        $total_users                  = $this->User_model->users($user_param);
        $data['total_users']          = $total_users;       
        unset($user_param['count']);
        $user_param['limit']          = $this->input->post('limit');
        $user_param['offset']         = $page;
        $user_param['select']         = 'id, us_name, us_phone, us_email, us_image, us_role_id, us_institute_id, us_branch, us_branch_code, us_institute_code, us_status, us_degree, us_deleted';

        if($total_users > ($this->__limit*$offset))
        {
            $data['show_load_button']  = true;
        }
        //echo '<pre>'; print_r($user_param);die;
        $data['users']                = $this->User_model->users($user_param);
        $data['batches']              = array();
        if($user_param['institute_id'] != '')
        {
            $this->load->model('Group_model');
            $param                  = array();
            $param['institute_id']  = $user_param['institute_id'];
            $param['select']        = 'id, CONCAT(gp_institute_code," - ",gp_year," - ",gp_name) as batch_name';
            $data['batches']        = $this->Group_model->groups($param);
        }
        $data['privilege']                  = $this->privilege;
        $data['batch_enroll_privilege']     = $this->batch_enroll_privilege;
        $data['user_privilege']             = $this->user_privilege;
        $data['student_enroll_privilege']   = $this->student_enroll_privilege;
        echo json_encode($data);
    }
    
    function groups_json()
    {
        $data               = array();
        $param              = array();
        $user_id            = $this->input->post('user_id');

        $user_param             = array();
        $user_param['select']   = 'users.id,users.us_name,users.us_institute_id, us_groups';
        $user_param['id']       = $user_id;
        $user_details           = $this->User_model->user($user_param);

        if(!empty($user_details)){
            
           $param['institute_id'] = $user_details['us_institute_id'];
        }
        $user_groups        = array();
        if($user_details['us_groups'] != '') {
            $user_groups    = explode(',', $user_details['us_groups']);
        }
        $param['direction']     = 'DESC';
        $param['status']        = '1';  
        $param['not_deleted']   = true;
        $param['not_ids']       = $user_groups;
        $param['select']        = 'groups.id,groups.gp_name,groups.gp_status';
        $data['groups']         = $this->User_model->groups($param);
	    $objects                = array();
        $objects['key']         = 'institute_'.$user_details['us_institute_id'];
        $callback               = 'institute';
        $institute_details      = $this->memcache->get($objects, $callback, array('id' =>$user_details['us_institute_id']));
	    $data['institute_name'] = $institute_details['ib_name'];

        if(!empty($data)){

            $response               = array();
            $response['success']    = true;
            $response['data']       = $data;
        }else{

            $response               = array();
            $response['success']    = false;
            $response['data']       = array();
        }
        echo json_encode($response); 
     
    }
    
    public function group_users()
    {
        $data                    = array();
        $group_id                = $this->input->post('group_id');
        $data['group_users']     = $this->User_model->group_users(array('direction'=>'ASC', 'group_id' => $group_id));
        echo json_encode($data);
    }
    
    public function save_group_users()
    {
        $group_id   = $this->input->post('group_id');
        $user_ids   = json_decode($this->input->post('user_ids'));
        $result     = 'false';
        if(!empty($user_ids))
        {
            $batch_details      = $this->Group_model->group(array('select'=>'gp_name','id'=>$group_id));
            $batch_name         = $batch_details['gp_name'];
            $privilage_user     = $this->__loggedInUser['us_name'];

            // echo "<pre>";print_r($user_ids);exit;
            foreach ($user_ids as $user_id)
            {

                $user       = $this->User_model->user(array('id' => $user_id,'verified'=>true));
                // echo "<pre>";print_r($user);exit;
                $groups             = array();
                
                $groups     = explode(',', $user['us_groups']);

                if (!in_array($group_id, $groups)){

                    
                    $user_name          = $user['us_name'];
                    $verified_email     = $user['us_email'];

                    if (!empty($verified_email)) {
                        
                        $email_param                = array();
                        $email_param['email_code']  = 'student_to_batch';
                        $email_param['email_ids']   = $verified_email;
                        $email_param['contents']    = array(
                            'username'          => $user_name
                            , 'batch_name'      => $batch_name
                            , 'privilage_user'  => $privilage_user
                            , 'date'            => date('Y-M-d h:i:sa')
                            , 'site_url'        => site_url()
                            , 'site_name'       => config_item('site_name'),
                        );
                        $this->send_mail($email_param);
                    }

                    $groups[]               = $group_id;
                    $groups                 = implode(',', array_unique($groups));
                    $save                   = array();
                    $save['id']             = $user_id;
                    $save['us_groups']      = $groups;
                    $save['updated_date']   = date('Y-m-d H:i:s');
                    
                    $result= $this->User_model->save($save); 
                }
            }
        }
        if(!empty($result)){
            $response               = array();
            $response['error']      = 'false';
            $response['message']    = lang('user_added_to_group');
        }else{
            $response               = array();
            $response['error']      = 'true';
            $response['message']    = 'Technical error';
        }
        
        echo json_encode($response);
    }
    
    function send_extend_validity()
    {
        
        $email_content      = array();
        $user_ids           = json_decode($this->input->post('user_id'),true);
        $user_ids           = is_array($user_ids) ? $user_ids : false;
        $course_id          = (preg_match('/^[1-9]+[0-9]*$/', $this->input->post('course_id')) == true)? $this->input->post('course_id') : false;
        $validity           = $this->input->post('updated_validity');
        $validity_date      = explode('/', $validity);
        if(count($validity_date) == 3)
        {
            $checkdate = checkdate($validity_date[0], $validity_date[1], $validity_date[2]);
        }
        else
        {
            $checkdate = false;
        }
        if($course_id !== false && $user_ids!== false && !empty($user_ids) && $checkdate)
        {
            $updated_validity   = date('Y-m-d', strtotime($validity));

            $course_param               = array();
            $course_param['course_id']  = $course_id;
            $course_param['select']     = 'cb_title'; 
            $course_param['limit']      = '1'; 
            $result                     = $this->Course_model->course_new($course_param);
            $course_title               = $result['cb_title'];

            
            $filter_param['user_ids']                   = $user_ids;
            $filter_param['course_id']                  = $course_id;
            $filter_param['update']                     = true;
            $save_param['cs_end_date']                  = $updated_validity;
            $save_param['cs_course_validity_status']    = '2';
            
            
            $this->User_model->save_subscription_new($save_param,$filter_param);

            $student_count = count($user_ids);
            if($student_count==1){
                $student_name           = $this->input->post('student_name');
                $student_count_valid    = '';
            }else{
                $student_name           = '';
                $student_count_valid    = $student_count;
            }
            foreach($user_ids as $user_id){

                $user_param             = array();
                $user_param['id']       = $user_id;
                $user_param['select']   = 'us_name,us_email';
                $user                   = $this->User_model->user($user_param);
                
                $new_email_param['email']       = $user['us_email'];
                $new_email_param['contents']    = array(
                        'user_name'             => $user['us_name']
                        ,'site_name'            => config_item('site_name') 
                        ,'course_title'         => $course_title
                        ,'validity'             => $validity
                        ,'site_url'             => site_url()
                    );
                array_push($email_content,$new_email_param);
                $invalidate_param               = array();
                $invalidate_param['user_id']    = $user_id;
                $invalidate_param['course_id']  = $course_id;
                $this->invalidate_subscription($invalidate_param);
            }
            $this->process_bulk_mail($email_content,'validity_extend');
            $user_data              = array();
            $user_data['user_id']   = $this->__loggedInUser['id'];
            $user_data['username']  = $this->__loggedInUser['us_name'];
            $user_data['useremail']  = $this->__loggedInUser['us_email'];
            $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
            
            $course = $this->Course_model->course(array('id' => $course_id));

            //Notification
            $this->load->library('Notifier');
            $this->notifier->push(
                array(
                    'action_code' => 'course_subscription_validity_changed',
                    'assets' => array('course_name' => $course['cb_title'],'course_id' => $course['id']),
                    'target' => $course_id,
                    'individual' => true,
                    'push_to' => $user_ids
                )
            );
            //End notification

            $message_template                   = array();
            $message_template['username']       = $this->__loggedInUser['us_name'];;
            $message_template['course_name']    = $this->input->post('course_title');
            $message_template['count']          = $student_count_valid;
            $message_template['student_name']   = $student_name ;
            $triggered_activity                 = 'course_subscription_validity_changed';
            log_activity($triggered_activity, $user_data, $message_template); 
            $response           = array();
            $response['error']  = false;  
            $response['message']= 'Validity Period Changed successfully';
        }
        else
        {
            $response            = array();
            $response['error']   = true;
            $response['message'] = 'Something went wrong!! Please try again';
        }
        echo json_encode($response); 
        
        
    }
    
    function set_as_complete()
    {
        $response               = array();
        $response['error']      = false;
        $response['message']    = lang('subscription_saved'); 
        $user_id                = $this->input->post('user_id');
        $course_id              = $this->input->post('course_id');
        $this->Course_model->set_user_lecture_as_complete(array('user_id' => $user_id, 'course_id' => $course_id));
        $this->invalidate_subscription(array('user_id'=>$user_id,'course_id'=>$course_id));
        echo json_encode($response);        
    }
    
    function change_subscription_status()
    {
        $response               = array();
        $response['error']      = false;
        $response['message']    = lang('subscription_saved'); 
        $user_id                = $this->input->post('user_id');
        $course_id              = $this->input->post('course_id');
        $status                 = $this->input->post('status');
        $this->User_model->save_subscription(array( 'cs_user_id' => $user_id, 'cs_course_id' => $course_id,  'cs_approved' => $status));
        $this->invalidate_subscription(array('user_id'=>$user_id,'course_id'=>$course_id));
        echo json_encode($response);
    }
    
    function reset_result()
    {
        $response               = array();
        $response['error']      = false;
        $response['message']    = lang('subscription_saved'); 
        $user_id                = $this->input->post('user_id');
        $course_id              = $this->input->post('course_id');
        $this->Course_model->reset_user_lecture_result(array('user_id' => $user_id, 'course_id' => $course_id));
        $this->invalidate_subscription(array('user_id'=>$user_id,'course_id'=>$course_id));
        echo json_encode($response);        
    }
    
    function delete_subscription()
    {
        $response               = array();
        $response['error']      = false;
        $response['message']    = lang('subscription_removed_success'); 
        $user_id                = $this->input->post('user_id');
        $course_id              = $this->input->post('course_id');
        $this->load->library(array('archive'));
        $archive_param                  = array();
        $archive_param['course_id']     = $course_id;
        $archive_param['user_id']       = $user_id;
        $this->archive->subscription_archive_process($archive_param);
        $this->Course_model->unsubscribe_user(array('user_id' => $user_id, 'course_id' => $course_id));
        $this->invalidate_subscription(array('user_id'=>$user_id,'course_id'=>$course_id));
        echo json_encode($response);                
    }

    function import_users()
    {
        $email_content              = array();
        $institute_id               = (isset($this->__loggedInUser['id'])&&$this->__loggedInUser['us_role_id'] == 8)?$this->__loggedInUser['us_institute_id']:$this->input->post('institute_id');
        $affected_rows              = array();
               
        $directory              = $this->config->item('upload_folder').'/user/'.$this->config->item('acct_domain');
        if (!file_exists($directory)) 
        {
            mkdir($directory, 0777, true);
        }
        $config                     = array();
        $config['upload_path']      = $directory;
        $config['allowed_types']    = "csv";      
        $config['encrypt_name']     = true;
        $this->load->library('upload');
        $this->upload->initialize($config);
        $uploaded = $this->upload->do_upload('file');

        /*============fetching institue details using institute_id==============*/
        $objects                    = array();
        $objects['key']             = 'institutes'; 
        $callback                   = 'institutes';
        $institute_item             = $this->memcache->get($objects, $callback);
        $institute_obj              = array();
        if(!empty($institute_item))
        {
            foreach($institute_item as $ins_object)
            {
                $institute_obj[$ins_object['id']] = $ins_object;
            }
        }
        /*======================================================================= */
    
        if($uploaded)
        {
            $upload_data        = $this->upload->data();
            $file               = $upload_data['full_path'];
            $file               = fopen($file, "r") or die("Unable to open file!");
            $header             = fgetcsv($file);
					
            //$template_header    = array( "student_name", "student_username", "student_email", "student_password", "student_mobile", "student_batch_name");
            $template_header    = array( "student_name", "student_email", "student_password", "student_mobile", "student_batch_name");
            $difference         = array_merge(array_diff($template_header, $header), array_diff($header, $template_header));
            // echo "<pre>"; print_r($template_header); die();
            if(!empty($difference))
            {
                echo json_encode(array('status' => 3, 'message' => 'Invalid template')); die();
            }
            foreach ($header as $h_key => $h_value) 
            {
                $$h_value = strip_tags($h_key);
            }

            $row                        = 0;
            $success_rows               = 0;
            $input_buffer_size          = 200;
            $column_dropdown            = array();
            $user_objects               = array();
            $input_email_ids            = array();
            $input_mobile_numbers       = array();
            $duplicate_emails           = array();
            $duplicate_phone_numbers    = array();
            $group_users                = array();
            $this->group_mails          = array();
            $save_students              = array();
            $student_passwords          = array();

            while (($user = fgetcsv($file, 10000, ",")) !== FALSE)
            {
                if(empty(array_filter($user)))
                {
                    $row++;
                    continue;
                }

                $user[$student_email]       = strtolower(strip_tags($user[$student_email]));
                $input_email_ids[]          = strtolower($user[$student_email]);
                $input_mobile_numbers[]   = $user[$student_mobile];
                $user_objects[]             = $user; 
                if($row%$input_buffer_size == 0)
                {
                    $email_ids = $this->User_model->users_by_columns(array('select' => 'us_email', 'email_ids' => $input_email_ids));
                    if(sizeof($email_ids) > 0 )
                    {
                        foreach($email_ids as $object)
                        {
                            $duplicate_emails[] = strtolower($object['us_email']);
                        }    
                    }

                    $phone_numbers = $this->User_model->users_by_columns(array('select' => 'us_phone', 'us_phone' => $input_mobile_numbers));
                    if(sizeof($phone_numbers) > 0 )
                    {
                        //echo '<pre>'; print_r($phone_numbers); exit;
                        foreach($phone_numbers as $object)
                        {
                            $duplicate_phone_numbers[] = $object['us_phone'];
                        }    
                    }
                    $input_email_ids        = array();
                    $input_mobile_numbers = array();
                }
                $row++;
            }
            if(sizeof($input_email_ids) > 0 )
            {
                $email_ids = $this->User_model->users_by_columns(array('select' => 'us_email', 'email_ids' => $input_email_ids));
                if(sizeof($email_ids) > 0 )
                {
                    foreach($email_ids as $object)
                    {
                        $duplicate_emails[] = strtolower($object['us_email']);
                    }    
                }
            }
            if(sizeof($input_mobile_numbers) > 0 )
            {
                $phone_numbers = $this->User_model->users_by_columns(array('select' => 'us_phone', 'us_phone' => $input_mobile_numbers));
                    if(sizeof($phone_numbers) > 0 )
                    {
                        foreach($phone_numbers as $object)
                        {
                            $duplicate_phone_numbers[] = $object['us_phone'];
                        }    
                    }
            }
            
            //echo '<pre>'; print_r($duplicate_phone_numbers); exit;
            if(empty($user_objects))
            {
                echo json_encode(array('status' => 3, 'message' => 'Template is empty')); die();
            }

            foreach($user_objects as $row => $user_obj)
            { 
                foreach ($template_header as $h_key => $h_value) 
                {
                    $user_obj[$$h_value] = trim(strtolower($user_obj[$$h_value]));
                }
    
                $defective_columns           = array();
                $defect_reason               = array();

                /* new if($user_obj[$student_username] == '')
                {
                    $defective_columns[] = $student_username;
                }
                else
                {
                    if(strlen($user_obj[$student_username]) > 10)
                    {
                        $defective_columns[] = $student_username;
                        $defect_reason[$student_username] = 'Exceeded limit';
                    }
                }*/
                if($user_obj[$student_email] != '')
                {
                    if(filter_var($user_obj[$student_email], FILTER_VALIDATE_EMAIL) === FALSE)
                    {
                        $defective_columns[] = $student_email;
                    }    
                }
                
                if($user_obj[$student_password] == '')
                {
                    $defective_columns[] = $student_password;
                    $defect_reason[$student_password] = 'Password is required';
                }
                else
                {
                    if(strlen($user_obj[$student_password]) < 6)
                    {
                        $defective_columns[] = $student_password;
                        $defect_reason[$student_password] = 'Weak Password';
                    }else if(!preg_match('/^[0-9a-zA-Z!@#$%$]{6,15}$/',$user_obj[$student_password]))
                    {
                        $defective_columns[] = $student_password;
                        $defect_reason[$student_password] = 'Invalid Password';
                    }
                }

                if($user_obj[$student_name] == '')
                {
                    $defective_columns[] = $student_name;
                }
                if(!preg_match('/^[a-zA-Z0-9 .]+$/',$user_obj[$student_name]))
                    {
                        $defective_columns[] = $student_name;
                        $defect_reason[$student_name] = 'Invalid name';
                    }
                if($user_obj[$student_mobile] == '')
                {
                    $defective_columns[] = $student_mobile;
                    $defect_reason[$student_mobile] = 'Phone number required';
                }
                else
                {
                    if(preg_match("/^[0-9]{3}-[0-9]{4}-[0-9]{4}$/", $user_obj[$student_mobile]) === FALSE)
                    {
                        $defective_columns[] = $student_mobile;
                    }
                    if(strlen($user_obj[$student_mobile]) > 10)
                    {
                        $defective_columns[] = $student_mobile;
                        $defect_reason[$student_mobile] = 'Exceeded length';
                    }

                    if(strlen(str_replace(array('+','.'),'',$user_obj[$student_mobile])) < 10)
                    {
                        $defective_columns[] = $student_mobile;
                        $defect_reason[$student_mobile] = 'Exceeded length';
                    }

                    if(!is_numeric($user_obj[$student_mobile]) || strlen(str_replace(array('+','.'),'',$user_obj[$student_mobile])) < 10)
                    {
                        $defective_columns[] = $student_mobile;
                        $defect_reason[$student_mobile] = 'Invalid phone number';
                    }
                }
                // $request                    = array();
                // $request['row']             = $row;
                // $request['field_name']      = 'content['.$row.'][row]['.$student_branch_code.']';
                // $request['field_value']     = $user_obj[$student_branch_code];
                // $response = $this->is_valid_branch_code($request);
                // if($response['valid'] == false)
                // {
                //     $defective_columns[] = $student_branch_code;
                //     $column_dropdown[$row.'_'.$student_branch_code] = $response['content'];
                // }
            
                if( $user_obj[$student_batch_name] != '' )
                { 
                    $request                    = array();
                    $request['row']             = $row;
                    $request['institute_id']    = $institute_id;
                    $request['field_name']      = 'content['.$row.'][row]['.$student_batch_name.']';
                    $request['field_value']     = $user_obj[$student_batch_name];
                    $response                   = $this->is_valid_batch_name($request);
                    if($response['valid'] == false)
                    {
                        $column_dropdown[$row.'_'.$student_batch_name] = $response['content'];
                        $defective_columns[] = $student_batch_name;
                    }    
                }

                $email_counts = array_count_values($input_email_ids);
                if($user_obj[$student_email] != '')
                { 
                    if(in_array(strtolower($user_obj[$student_email]), $duplicate_emails)){
                    
                        $defective_columns[] = $student_email;
                        $defect_reason[$student_email] = 'Email exists';
                    }else if(isset($email_counts[$user_obj[$student_email]]) && $email_counts[$user_obj[$student_email]] > 1)
                    {
                        $defective_columns[] = $student_email;
                        $defect_reason[$student_email] = 'Email exists';
                    }    
                }else{
                    $defective_columns[] = $student_email;
                }

                $phone_counts = array_count_values($input_mobile_numbers);

                if(in_array($user_obj[$student_mobile], $duplicate_phone_numbers))
                {
                    $defective_columns[] = $student_mobile;
                    $defect_reason[$student_mobile] = 'Phone number exists';
                }
                else if(isset($phone_counts[$user_obj[$student_mobile]]) && $phone_counts[$user_obj[$student_mobile]] > 1)
                {
                    $defective_columns[] = $student_mobile;
                    $defect_reason[$student_mobile] = 'Phone number exists';
                }

                if(sizeof($defective_columns) == 0)
                {
                    if(sizeof($defective_columns) == 0)
                    {
                        //your success function here
                        //your success function here
                        //$branch         = isset($this->branch_codes[$user_obj[$student_branch_code]])?$this->branch_codes[$user_obj[$student_branch_code]]:array();
                        $batch          = isset($this->batches[$user_obj[$student_batch_name]])?$this->batches[$user_obj[$student_batch_name]]:array();
                        
                        //$branch_id      = isset($branch['id'])?$branch['id']:0;
                        $group_id       = isset($batch['id'])?$batch['id']:0;

                        $save                       = array();       
                        $save['us_name']            = strip_tags($user_obj[$student_name]);
                        $save['us_email']           = strip_tags($user_obj[$student_email]);
                        $password                   = strip_tags($user_obj[$student_password]);
                        $save['us_password']        = sha1($password);
                        $save['us_phone']           = strip_tags($user_obj[$student_mobile]);
                        $save['us_role_id']         = '2';
                        $save['us_account_id']      = $this->config->item('id');
                        $save['us_status']          = '1';
                        $save['us_institute_id']    = $institute_id;
                        $save['us_branch']          = '1';//$branch_id;
                        $save['us_branch_code']     = isset($branch['branch_code'])?$branch['branch_code']:0;
                        $institute_details          = $institute_obj[$institute_id];
                        $save['us_institute_code']  = $institute_details['ib_institute_code'];
                        $save['action_id']          = '1'; 
                        $save['action_by']          = $this->__loggedInUser['id'];
                        $save['us_deleted']         = '0';
                        $save['updated_date']       = date('Y-m-d H:i:s');
                        $save['us_groups']          = $group_id;
                        // $this->User_model->save($save);
                        $save_students[]            = $save;
                        $duplicate_emails[]             = $save['us_email'];
                        $duplicate_phone_numbers[]   = $save['us_phone'];
                        $student_passwords[sha1($save['us_email'])] = $password;          
                        
                        /*=========================================================== */
                        $success_rows++;
                    }
                    else
                    {
                        $affected_rows[$row] = array('defect_columns' => $defective_columns , 'row' => $user_obj, 'row_number' => $row, 'type' => 'duplicate_data_row' );
                    }
                }
                else
                {
                    $affected_rows[$row] = array( 'defect_columns' => $defective_columns, 'row' => $user_obj, 'row_number' => $row, 'type' => 'invalid_data_row', 'defect_reason' => $defect_reason );
                }
                $row++;
            }
            fclose($file);
            if(!empty($save_students))
            {
                $tokens                 = array();
                $save_students_image    = array();
                $students = $this->User_model->insert_users_bulk($save_students);
                if(!file_exists(user_upload_path()))
                {
                    mkdir(user_upload_path(), 0777, true);
                }        

                $image_upload_param = array();
                $has_s3     = $this->settings->setting('has_s3');
                if( $has_s3['as_superadmin_value'] && $has_s3['as_siteadmin_value'] )
                {
                    $actual_path                    = user_upload_path().'temp_'.$this->__loggedInUser['id'].'/';
                    $image_upload_param['image_to'] = FCPATH.$actual_path;            
                    if(!file_exists($image_upload_param['image_to']))
                    {
                        mkdir($image_upload_param['image_to'], 0777, true);
                    }        
                }      
                else
                {
                    $actual_path                    = user_upload_path();
                    $image_upload_param['image_to'] = FCPATH.$actual_path;            
                }  
                $this->__student_images = array();
                foreach($students as $student)
                {
                    $student_image = $student['id'].'.jpg';

                    //this is to upload student images to s3 , if s3 is enabled
                    $this->__student_images[$actual_path.$student_image] = user_upload_path().$student_image;

                    $image_upload_param['user_id']  = $student['id'];
                    $this->create_profile_image( $image_upload_param );
                    $save_students_image[]  = array('id' => $student['id'], 'us_image' => $student_image);
                    $email_token            = md5(openssl_random_pseudo_bytes(64));            
                    $token                  = array();
                    $token['et_user_id']    = $student['id'];
                    $token['et_user_email'] = $student['us_email'];
                    $token['et_account_id'] = config_item('id');
                    $token['et_token']      = $email_token;
                    $token['et_change_status'] = '0';
                    $token['et_status']     = '1';
                    $tokens[]               = $token;
                    // $token_response         = $this->User_model->save_token($token);
    
                    
                    $new_email_param                = array();
                    $new_email_param['email']       = $student['us_email'];
                    $new_email_param['contents']    = array(
                                                                'user_name'     => $student['us_name']
                                                                ,'site_name'    => config_item('site_name') 
                                                                ,'email_id'     => $student['us_email']
                                                                ,'phone_no'  => $student['us_phone']
                                                                ,'password'     => $student_passwords[sha1($student['us_email'])]
                                                                ,'verification_link' => site_url('register/verify/'.$email_token)
                                                                ,'site_url_login' => site_url('login')
                                                            );
                    $email_content[]                = $new_email_param;
                    
                    if( $group_id > 0 )
                    {
                        if(!isset($group_users[$group_id]))
                        {
                            $group_users[$group_id] = array();
                        }
                        $group_users[$group_id][] = array( 'user_id' => $student['id'], 'user_email' => $student['us_email'], 'user_name' => $student['us_name']);
                    }
                }
                if( $has_s3['as_superadmin_value'] && $has_s3['as_siteadmin_value'] )
                {
                    $s3_param                   = array();
                    $s3_param['source']         = $image_upload_param['image_to'];
                    $s3_param['files']          = $this->__student_images;
                    $this->send_student_image_to_s3($s3_param);                    
                }
                $this->User_model->save_token_bulk($tokens);
                $this->User_model->update_users_bulk($save_students_image);
            }
            if( sizeof($group_users) > 0 )
            {
                $course_users               = array();
                $this->__courses_details    = array();
                $this->__user_subscription  = array();
                foreach( $group_users as $group_id => $users )
                {
                    $courses = $this->User_model->group_courses(array('select' => 'id', 'group_id' => $group_id));
                    if(!empty($courses))
                    {
                        foreach($courses as $course_id)
                        {
                            if(!isset($course_users[$course_id]))
                            {
                                $course_users[$course_id] = array();
                            }
                            if(sizeof($users)>0)
                            {
                                foreach($users as $user)
                                {
                                    if(!in_array($user['user_id'], $course_users[$course_id]))
                                    {
                                        $subscription               = array();
                                        $subscription['user']       = $user;
                                        $subscription['course_id']  = $course_id;
                                        $this->save_user_subscription($subscription); 
                                    }
                                    $course_users[$course_id][] = $user['user_id'];
                                }
                            }
                        }
                    }
                }
                $this->User_model->save_subscription_bulk($this->__user_subscription);
            }

            if( sizeof($email_content) > 0 )
            {
                 $this->process_bulk_mail($email_content,'student_welcome_mail');
            }

            if( sizeof($this->group_mails) > 0 )
            {
                 $this->process_bulk_mail($this->group_mails, 'student_to_course');
            }

            $response               = array();
            $response['status']     = 1;
            $response['message']    = '';
            $message                = array();
            $status                 = 1; // 1 => succees, 2 => warning, 3 => error
            if( $success_rows > 0 )
            {
                $message[] = (($success_rows>1)?(' '.$success_rows.' Users'):'1 User').' imported successfully';
            }
            $total_affected = sizeof($affected_rows);
            if( $total_affected > 0)
            {
                /*Log creation*/
                $imported_count                 = ($total_affected>1)?$total_affected.' students': 'a student';
                $user_data                      = array();
                $user_data['user_id']           = $this->__loggedInUser['id'];
                $user_data['username']          = $this->__loggedInUser['us_name'];
                $user_data['useremail']          = $this->__loggedInUser['us_email'];
                $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
                $message_template               = array();
                $message_template['username']   = $this->__loggedInUser['us_name'];
                $message_template['count']      = $imported_count;
                $triggered_activity             = 'student_imported';
                log_activity($triggered_activity, $user_data, $message_template);

                $memcache_index = ('userimport'.$this->__loggedInUser['id']);
                $response['redirect_url'] = admin_url('user/preview').$memcache_index;
                $status         = 2;
                $message[]      = 'We found some problem with the csv you uploaded. We are redirecting you to preview page.';
                $preview_contents                   = array();
                $preview_contents['headers']        = $template_header;
                $preview_contents['institute_id']   = $institute_id;
                $preview_contents['content']        = $affected_rows;
                $preview_contents['column_dropdown']= $column_dropdown;
                $preview_contents['total_rows']     = $row-1;
                $preview_contents['inserted']       = $success_rows;
                $preview_contents['failed']         = $total_affected;
                $preview_contents['back_to_home']   = admin_url('users');
                $this->memcache->set($memcache_index, $preview_contents);
            }

            $response['status'] = $status;
            $response['message'] = implode('<br />', $message);
            echo json_encode($response);
        }
        else
        {
           echo json_encode(array('status' => 3 , 'message'=>$this->upload->display_errors())); die; 
        }
    }

    public function preview($key='')
    {
        

        if(!$key)
        {
            redirect(admin_url('user'));
        }
        $users_content = $this->memcache->get(array('key' => $key));
        if(empty($users_content['content']))
        {
            redirect(admin_url('user'));
        }

        $email_content          = array();
        $data                   = array();
        $affected_rows          = array();
        $data['instructions']   = array();
        $institute_id           = (isset($this->__loggedInUser['id'])&&$this->__loggedInUser['us_role_id'] == 8)?$this->__loggedInUser['us_institute_id']:$users_content['institute_id'];
        $data['excell']         = $users_content;
        $data['key']            = $key;
        $data['action']         = admin_url('user/preview').$key;
        $data['instructions'][] = 'A valid <b>student_mobile</b> is a mandatory field, and should have maximum of 10 digits';
        $data['instructions'][] = 'A <b>student_email</b> is a mandatory field, and should be a valid email';
        $data['instructions'][] = 'A <b>student_batch_name</b> is not a mandatory field. However if you set any then system will validate it';
        $this->load->library('form_validation');
        if ($this->input->server('REQUEST_METHOD') != 'POST')
        { //print_r($data);die('987');
            // echo "<pre>";print_r($users_content);exit;
            $this->load->view($this->config->item('admin_folder').'/import_preview_users', $data);
        }
        else
        {
            $preview_data               = $this->input->post('preview_data');
            $users                      = json_decode($preview_data, true);
            $row                        = 0;
            $success_rows               = 0;
            $input_buffer_size          = 200;
            $column_dropdown            = array();
            $user_objects               = array();
            $input_email_ids            = array();
            $input_mobile_numbers     = array();
            $duplicate_emails           = array();
            $duplicate_phone_numbers = array();
            $group_users                = array();
            $this->group_mails          = array();
            $save_students              = array();
            $student_passwords          = array();
            
            foreach ($users_content['headers'] as $h_key => $h_value) 
            {
                $$h_value = strip_tags($h_key);
            }

            /*============fetching institue details using institute_id==============*/
            $objects                    = array();
            $objects['key']             = 'institutes'; 
            $callback                   = 'institutes';
            $institute_item             = $this->memcache->get($objects, $callback);
            $institute_obj              = array();
            if(!empty($institute_item))
            {
                foreach($institute_item as $ins_object)
                {
                    $institute_obj[$ins_object['id']] = $ins_object;
                }
            }
            /*======================================================================= */
            $institute_details          = $institute_obj[$institute_id];
            foreach($users as $row => $user)
            {
                if(empty(array_filter($user)))
                {
                    continue;
                }
                $user[$student_email]       = strtolower(strip_tags($user[$student_email]));
                $input_email_ids[]          = strtolower($user[$student_email]);
                $input_mobile_numbers[]     = $user[$student_mobile];
                $user_objects[]             = $user;
                if($row%$input_buffer_size == 0)
                {
                    $email_ids = $this->User_model->users_by_columns(array('select' => 'us_email', 'email_ids' => $input_email_ids));
                    if(sizeof($email_ids) > 0 )
                    {
                        foreach($email_ids as $object)
                        {
                            $duplicate_emails[] = strtolower($object['us_email']);
                        }    
                    }
                    $phone_numbers = $this->User_model->users_by_columns(array('select' => 'us_phone', 'us_phone' => $input_mobile_numbers));
                    if(sizeof($phone_numbers) > 0 )
                    {
                        foreach($phone_numbers as $object)
                        {
                            $duplicate_phone_numbers[] = $object['us_phone'];
                        }    
                    }
                    $input_email_ids        = array();
                    $input_mobile_numbers = array();
                }
            }
            if(sizeof($input_email_ids) > 0 )
            {
                $email_ids = $this->User_model->users_by_columns(array('select' => 'us_email', 'email_ids' => $input_email_ids));
                if(sizeof($email_ids) > 0 )
                {
                    foreach($email_ids as $object)
                    {
                        $duplicate_emails[] = strtolower($object['us_email']);
                    }    
                }
            }
            if(sizeof($input_mobile_numbers) > 0 )
            {
                $phone_numbers = $this->User_model->users_by_columns(array('select' => 'us_phone', 'us_phone' => $input_mobile_numbers));
                    if(sizeof($phone_numbers) > 0 )
                    {
                        foreach($phone_numbers as $object)
                        {
                            $duplicate_phone_numbers[] = $object['us_phone'];
                        }    
                    }
            }

            if(empty($user_objects))
            {
                redirect(admin_url('user'));
            }

            foreach($user_objects as $row => $user_obj)
            { 
                foreach ($users_content['headers'] as $h_key => $h_value) 
                {
                    $user_obj[$$h_value] = trim(strtolower($user_obj[$$h_value]));
                }
    
                $defective_columns           = array();
                $defect_reason               = array();
                /* new if($user_obj[$student_username] == '')
                {
                    $defective_columns[] = $student_username;
                }
                else
                {
                    if(strlen($user_obj[$student_username]) > 10)
                    {
                        $defective_columns[] = $student_username;
                        $defect_reason[$student_username] = 'Exceeded limit';
                    }
                }*/
                if($user_obj[$student_email] != '')
                {
                    if(filter_var($user_obj[$student_email], FILTER_VALIDATE_EMAIL) === FALSE)
                    {
                        $defective_columns[] = $student_email;
                    }    
                } 
                
                if($user_obj[$student_password] == '')
                {
                    $defective_columns[] = $student_password;
                }
                else
                {
                    if(strlen($user_obj[$student_password]) < 6)
                    {
                        $defective_columns[] = $student_password;
                        $defect_reason[$student_password] = 'Weak Password';
                    }else if(!preg_match('/^[a-zA-Z0-9_]+$/',$user_obj[$student_password]))
                    {
                        $defective_columns[] = $student_password;
                        $defect_reason[$student_password] = 'Invalid Password';
                    }
                }

                if($user_obj[$student_name] == '')
                {
                    $defective_columns[] = $student_name;
                }
                if(!preg_match('/^[a-zA-Z0-9 .]+$/',$user_obj[$student_name]))
                    {
                        $defective_columns[] = $student_name;
                        $defect_reason[$student_name] = 'Invalid name';
                    }
                if($user_obj[$student_mobile] == '')
                {
                    $defective_columns[] = $student_mobile;
                }
                else
                {
                //echo strlen(str_replace(array('+','.'),'',$user_obj[$student_mobile])); die;
                    if(preg_match("/^[0-9]{3}-[0-9]{4}-[0-9]{4}$/", $user_obj[$student_mobile]) === FALSE)
                    {
                        $defective_columns[] = $student_mobile;
                    }
                    if(strlen($user_obj[$student_mobile]) > 10)
                    {
                        $defective_columns[] = $student_mobile;
                        $defect_reason[$student_mobile] = 'Exceeded length';
                    }

                    if(strlen(str_replace(array('+','.'),'',$user_obj[$student_mobile])) < 10)
                    {
                        $defective_columns[] = $student_mobile;
                        $defect_reason[$student_mobile] = 'Exceeded length';
                    }

                    if(!is_numeric($user_obj[$student_mobile]) || strlen(str_replace(array('+','.'),'',$user_obj[$student_mobile])) < 10)
                    {
                        $defective_columns[] = $student_mobile;
                        $defect_reason[$student_mobile] = 'Invalid phone number';
                    }
                }
                // $request                    = array();
                // $request['row']             = $row;
                // $request['field_name']      = 'content['.$row.'][row]['.$student_branch_code.']';
                // $request['field_value']     = $user_obj[$student_branch_code];
                // $response = $this->is_valid_branch_code($request);
                // if($response['valid'] == false)
                // {
                //     $defective_columns[] = $student_branch_code;
                //     $column_dropdown[$row.'_'.$student_branch_code] = $response['content'];
                // }
            
                if( $user_obj[$student_batch_name] != '' )
                { 
                    $request                    = array();
                    $request['row']             = $row;
                    $request['institute_id']    = $institute_id;
                    $request['field_name']      = 'content['.$row.'][row]['.$student_batch_name.']';
                    $request['field_value']     = $user_obj[$student_batch_name];
                    $response                   = $this->is_valid_batch_name($request);
                    if($response['valid'] == false)
                    {
                        $column_dropdown[$row.'_'.$student_batch_name] = $response['content'];
                        $defective_columns[] = $student_batch_name;
                    }    
                }

                $email_counts = array_count_values($input_email_ids);
                if($user_obj[$student_email] != '')
                { 
                    if(in_array(strtolower($user_obj[$student_email]), $duplicate_emails)){
                    
                        $defective_columns[] = $student_email;
                        $defect_reason[$student_email] = 'Email exists';
                    }else if(isset($email_counts[$user_obj[$student_email]]) && $email_counts[$user_obj[$student_email]] > 1)
                    {
                        $defective_columns[] = $student_email;
                        $defect_reason[$student_email] = 'Email exists';
                    }    
                }else{
                    $defective_columns[] = $student_email;
                }

                $phone_counts = array_count_values($input_mobile_numbers);

                if(in_array($user_obj[$student_mobile], $duplicate_phone_numbers))
                {
                    $defective_columns[] = $student_mobile;
                    $defect_reason[$student_mobile] = 'Phone number exists';
                }
                else if(isset($phone_counts[$user_obj[$student_mobile]]) && $phone_counts[$user_obj[$student_mobile]] > 1)
                {
                    $defective_columns[] = $student_mobile;
                    $defect_reason[$student_mobile] = 'Phone number exists';
                }
                
                if(sizeof($defective_columns) == 0)
                {
                    if(sizeof($defective_columns) == 0)
                    {
                        //your success function here
                        //$branch         = isset($this->branch_codes[$user_obj[$student_branch_code]])?$this->branch_codes[$user_obj[$student_branch_code]]:array();
                        $batch          = isset($this->batches[$user_obj[$student_batch_name]])?$this->batches[$user_obj[$student_batch_name]]:array();
                        
                        //$branch_id      = isset($branch['id'])?$branch['id']:0;
                        $group_id       = isset($batch['id'])?$batch['id']:0;
                        $save                       = array();       
                        $save['us_name']            = strip_tags($user_obj[$student_name]);
                        $save['us_email']           = strip_tags($user_obj[$student_email]);
                        $password                   = strip_tags($user_obj[$student_password]);
                        $save['us_password']        = sha1($password);
                        $save['us_phone']           = strip_tags($user_obj[$student_mobile]);
                        $save['us_role_id']         = '2';
                        $save['us_account_id']      = $this->config->item('id');
                        $save['us_status']          = '1';
                        $save['us_institute_id']    = $institute_id;
                        $save['us_branch']          = '1'; //$branch_id;
                        $save['us_branch_code']     = isset($branch['branch_code'])?$branch['branch_code']:0;
                        $institute_details          = $institute_obj[$institute_id];
                        $save['us_institute_code']  = $institute_details['ib_institute_code'];
                        $save['action_id']          = '1'; 
                        $save['action_by']          = $this->__loggedInUser['id'];
                        $save['us_deleted']         = '0';
                        $save['updated_date']       = date('Y-m-d H:i:s');
                        $save['us_groups']          = $group_id;
                        // $user_id                    = $this->User_model->save($save);
                        $save_students[]            = $save;
                        
                        $duplicate_emails[]             = $save['us_email'];
                        $duplicate_phone_numbers[]   = $save['us_phone'];
                        $student_passwords[sha1($save['us_email'])] = $password;          
                        /*=========================================================== */
                        $success_rows++;
                    }
                    else
                    {
                        $affected_rows[$row] = array('defect_columns' => $defective_columns , 'row' => $user_obj, 'row_number' => $row, 'type' => 'duplicate_data_row' );
                    }
                }
                else
                {
                    $affected_rows[$row] = array( 'defect_columns' => $defective_columns, 'row' => $user_obj, 'row_number' => $row, 'type' => 'invalid_data_row', 'defect_reason' => $defect_reason );
                }
            }

            if(!empty($save_students))
            {
                $tokens                 = array();
                $save_students_image    = array();
                $students = $this->User_model->insert_users_bulk($save_students);
                if(!file_exists(user_upload_path()))
                {
                    mkdir(user_upload_path(), 0777, true);
                }        

                $image_upload_param = array();
                $has_s3     = $this->settings->setting('has_s3');
                if( $has_s3['as_superadmin_value'] && $has_s3['as_siteadmin_value'] )
                {
                    $actual_path                    = user_upload_path().'temp_'.$this->__loggedInUser['id'].'/';
                    $image_upload_param['image_to'] = FCPATH.$actual_path;            
                    if(!file_exists($image_upload_param['image_to']))
                    {
                        mkdir($image_upload_param['image_to'], 0777, true);
                    }        
                }      
                else
                {
                    $actual_path                    = user_upload_path();
                    $image_upload_param['image_to'] = FCPATH.$actual_path;            
                }  
                $this->__student_images = array();
                foreach($students as $student)
                {
                    $student_image = $student['id'].'.jpg';

                    //this is to upload student images to s3 , if s3 is enabled
                    $this->__student_images[$actual_path.$student_image] = user_upload_path().$student_image;

                    $image_upload_param['user_id']  = $student['id'];
                    $this->create_profile_image( $image_upload_param );
                    $save_students_image[]  = array('id' => $student['id'], 'us_image' => $student_image);
                    $email_token            = md5(openssl_random_pseudo_bytes(64));            
                    $token                  = array();
                    $token['et_user_id']    = $student['id'];
                    $token['et_user_email'] = $student['us_email'];
                    $token['et_account_id'] = config_item('id');
                    $token['et_token']      = $email_token;
                    $token['et_change_status'] = '0';
                    $token['et_status']     = '1';
                    $tokens[]               = $token;
                    // $token_response         = $this->User_model->save_token($token);
    
                    
                    $new_email_param                = array();
                    $new_email_param['email']       = $student['us_email'];
                    $new_email_param['contents']    = array(
                                                                'user_name'     => $student['us_name']
                                                                ,'site_name'    => config_item('site_name') 
                                                                ,'email_id'     => $student['us_email']
                                                                ,'phone_no'  => $student['us_phone']
                                                                ,'password'     => $student_passwords[sha1($student['us_email'])]
                                                                ,'verification_link' => site_url('register/verify/'.$email_token)
                                                                ,'site_url_login' => site_url('login')
                                                            );
                    $email_content[]                = $new_email_param;
                    
                    if( $group_id > 0 )
                    {
                        if(!isset($group_users[$group_id]))
                        {
                            $group_users[$group_id] = array();
                        }
                        $group_users[$group_id][] = array( 'user_id' => $student['id'], 'user_email' => $student['us_email'], 'user_name' => $student['us_name']);
                    }
                }
                if( $has_s3['as_superadmin_value'] && $has_s3['as_siteadmin_value'] )
                {
                    $s3_param                   = array();
                    $s3_param['source']         = $image_upload_param['image_to'];
                    $s3_param['files']          = $this->__student_images;
                    $this->send_student_image_to_s3($s3_param);                    
                }
                $this->User_model->save_token_bulk($tokens);
                $this->User_model->update_users_bulk($save_students_image);
            }
            if( sizeof($group_users) > 0 )
            {
                $course_users               = array();
                $this->__courses_details    = array();
                $this->__user_subscription  = array();
                foreach( $group_users as $group_id => $users )
                {
                    $courses = $this->User_model->group_courses(array('select' => 'id', 'group_id' => $group_id));
                    if(!empty($courses))
                    {
                        foreach($courses as $course_id)
                        {
                            if(!isset($course_users[$course_id]))
                            {
                                $course_users[$course_id] = array();
                            }
                            if(sizeof($users)>0)
                            {
                                foreach($users as $user)
                                {
                                    if(!in_array($user['user_id'], $course_users[$course_id]))
                                    {
                                        $subscription               = array();
                                        $subscription['user']       = $user;
                                        $subscription['course_id']  = $course_id;
                                        $this->save_user_subscription($subscription); 
                                    }
                                    $course_users[$course_id][] = $user['user_id'];
                                }
                            }
                        }
                    }
                }
                $this->User_model->save_subscription_bulk($this->__user_subscription);
            }
            if( sizeof($email_content) > 0 )
            {
                $this->process_bulk_mail($email_content,'student_welcome_mail');
            }
            if( sizeof($this->group_mails) > 0 )
            {
                $this->process_bulk_mail($this->group_mails, 'student_to_course');
            }

            $total_affected = sizeof($affected_rows);
          
        
            if( $total_affected > 0 )
            {
                $memcache_index = $key;
                $redirect_url   = admin_url('user/preview').$memcache_index;
                $preview_contents                   = array();
                $preview_contents['headers']        = $users_content['headers'];
                $preview_contents['institute_id']   = $institute_id;
                $preview_contents['content']        = $affected_rows;
                $preview_contents['column_dropdown']= $column_dropdown;
                $preview_contents['total_rows']     = $row;//$row-1;
                $preview_contents['inserted']       = $success_rows;
                $preview_contents['failed']         = $total_affected;
                $preview_contents['back_to_home']   = admin_url('users');
                //print_r($preview_contents);die;
                $this->memcache->set($memcache_index, $preview_contents);
                redirect($redirect_url);
            }
            else
            {
                $memcache_index = $key;
                $this->memcache->delete($memcache_index);
                
                $response               = array();
                $response['success']    = true;
                $response['message']    = 'Users Imported Successfully';
                $this->session->set_flashdata('popup',$response);
                redirect(admin_url('user'));
            }
        }
    }

    function export_preview($key=false)
    {
        if(!$key)
        {
            redirect(admin_url('user'));
        }
        $key                    = base64_decode($key);
        $users_content          = ($this->memcache->get(array('key' => $key)))?$this->memcache->get(array('key' => $key)):array();
        if(!isset($users_content['content']))
        {
            redirect(admin_url('user'));
        }
        $data                   = array();
        $data['users']          = $users_content['content'];
        $this->load->view($this->config->item('admin_folder').'/export_student_import', $data);
    }
    function sample()
    {
        $response               = array();
        $response['success']    = true;
        $response['message']    = 'Users Imported Successfully';
        $this->session->set_flashdata('popup',$response);
        redirect(admin_url('user'));
    }
    function is_valid_branch_code( $request = array() )
    {
        $request['field_label']     = 'Choose Branch';
        $request['dropdown_for']    = 'all_branches_selector';
        //setting branch html and branch code array
        $this->process_branch_objects();
        //end

        $response           = array();
        $response['valid']  = true;

        $branch_name = trim($request['field_value']);
        if(!$branch_name)
        {
            $request['field_value'] = '--branch_not_assinged--';
            $response['valid']      = false;
            //$request['values']      = $this->branch_html;
            $response['content']    = $this->render_option_html($request);
        }
        else
        {
            if(!array_key_exists($branch_name, $this->branch_codes))
            {
                $response['valid']      = false;
                //$request['values']      = $this->branch_html;
                $response['content']    = $this->render_option_html($request);    
            }
            else
            {
                $response['valid']      = true;    
                $branch_object          = $this->branch_codes[$branch_name];
                $response['data']       = array(
                                                'branch_id' => $branch_object['id'],
                                                'branch_code' => $branch_object['branch_code']
                );    
            }
        }
        return $response;
    }
    function process_branch_objects()
    {
        if(!isset($this->branches))
        {
            $this->branches       = array();
            $objects              = array();
            $objects['key']       = 'branches';
            $callback             = 'branches';
            $this->branches       = $this->memcache->get($objects, $callback); 
        }
        if(!isset($this->branch_codes))
        {
            //$this->branch_html = '';
            $this->branch_codes = array();
            if(!empty($this->branches))
            {
                foreach($this->branches as $branch)
                {
                    $this->branch_codes[$branch['branch_code']] = $branch; 
                    //$this->branch_html   .= '<option value ="'.$branch['branch_code'].'">'.$branch['branch_code'].'-'.$branch['branch_name'].'</option>';
                }
            }
        }
    }
    function is_valid_batch_name( $request = array() )
    {
        $request['field_label']     = 'Choose Batch';
        $request['dropdown_for']    = 'all_batches_selector';
        if(!isset($this->batches))
        {
            $objects              = array();
            $objects['key']       = 'insbtch'.$this->__loggedInUser['id'];
            $callback             = 'institute_batches';
            $batches              = $this->memcache->get($objects, $callback, array('institute_id' => $request['institute_id'], 'select' => 'id, gp_name')); 
            //print_r($batches); die('is_valid_batch_name');
            $this->batch_html  = '';
            $this->batches = array();
            if(!empty($batches))
            {
                foreach($batches as $b_obj)
                {
                    $this->batch_html                .= '<option value ="'.$b_obj['gp_name'].'">'.$b_obj['gp_name'].'</option>';
                    $this->batches[strtolower($b_obj['gp_name'])] = $b_obj;
                }
            }
        }
        
        $batch_name = strtolower(trim($request['field_value']));
        
        if(!$batch_name)
        {
            $request['field_value'] = '--batch_not_assinged--';
            $response['valid']      = false;
            $request['values']      = $this->batch_html;
            $response['content']    = $this->render_option_html($request);
           
        }
        else
        {
            if(!array_key_exists($batch_name, $this->batches))
            {
                $response['valid']      = false;
                $request['values']      = $this->batch_html;
                $response['content']    = $this->render_option_html($request); 
                //print_r($response); die('dfghjk');   
                // if($this->config->item('id') == 41)
                // {
                //      print_r($batch_name); die('is_valid_batch_name');
                // }
            }
            else
            {
                $response['valid']      = true;    
                $batch_object           = $this->batches[$batch_name];
                $response['data']       = array(
                                                'batch_id' => $batch_object['id'],
                                                'batch_name' => $batch_object['gp_name']
                );    
            }
        }
      
         
        return $response;
    }
    
    function render_option_html( $request = array() )
    {
        $field_name     = str_replace(" ","_",$request['field_value']);
        $field_name     = str_replace(".","_",$field_name);
        $option_html    = '<select class="'.$request['dropdown_for'].' '.$field_name.'" name="'.$request['field_name'].'">';
        $option_html    .=  '<option value="'.$request['field_value'].'">'.$request['field_label'].'</option>';
        $option_html    .=  $request['values'];
        $option_html    .= '</select>';
        return $option_html;
    }

    public function save_user_subscription($param)
    {
        if(!isset($this->__courses_details[$param['course_id']]))
        {
            $course_param               = array();
            $course_param['select']     = 'id, cb_title, cb_validity_date, cb_access_validity';
            $course_param['course_id']  = $param['course_id'];
            $this->__courses_details[$param['course_id']] = $this->Course_model->course_new($course_param);
            if ($course_details['cb_access_validity'] == 2) 
            {
                $course_enddate = $course_details['cb_validity_date'];

            } 
            else if ($course_details['cb_access_validity'] == 0) 
            {
                $course_enddate = date('Y-m-d', strtotime('+3000 days'));
            }
            else
            {
                $duration       = ($course_details['cb_validity']) ? $course_details['cb_validity'] : 0;
                $course_enddate = date('Y-m-d', strtotime('+' . $duration . ' days'));
            }
            $this->__courses_details[$param['course_id']]['course_enddate'] = $course_enddate;
        } 

        $save                               = array();
        $save['cs_course_id']               = $param['course_id'];
        $save['cs_user_id']                 = $param['user']['user_id'];
        $save['cs_subscription_date']       = date('Y-m-d');
        $save['cs_end_date']                = $this->__courses_details[$param['course_id']]['course_enddate'];
        $save['cs_course_validity_status']  = $this->__courses_details[$param['course_id']]['cb_access_validity'];
        $save['cs_start_date']              = $save['cs_subscription_date'];
        $save['cs_approved']                = '1';
        $save['action_by']                  = $this->__loggedInUser['id'];
        // $result                             = $this->User_model->save_subscription($save);
        $this->__user_subscription[]        = $save;

        $new_email_param                = array();
        $new_email_param['email']       = $save['user_email'];
        $new_email_param['contents']    = array(
                                                  'username'        => $param['user']['user_name']
                                                , 'course_name'     => $this->__courses_details[$param['course_id']]['cb_title']
                                                , 'privilage_user'  => $this->__loggedInUser['us_name']
                                                , 'date'            => date('Y-M-d h:i:sa')
                                                , 'site_url'        => site_url()
                                                , 'site_name'       => config_item('site_name'),
                                            );
        $this->group_mails[] = $new_email_param;
    }

    function process_bulk_mail($email_param,$email_code)
    {
        
        $data               = array(
            'email_code'    => $email_code,
            'data'          => json_encode($email_param)
        );
        
        $curlHandle         = curl_init(site_url()."cron_job/bulk_process_mail");
        $defaultOptions     = array (
                                CURLOPT_POST => 1,
                                CURLOPT_POSTFIELDS => $data,
                                CURLOPT_RETURNTRANSFER => false ,
                                CURLOPT_TIMEOUT_MS => 1000,
                             );
        curl_setopt_array($curlHandle , $defaultOptions);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, FALSE);     
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2); 
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array(
            'request-token: '.sha1(config_item('acct_domain').config_item('id')),
        ));
        $result = curl_exec($curlHandle);
        curl_close($curlHandle);

    } 

    private function send_student_image_to_s3($s3_params)
    {
        $curlHandle         = curl_init(site_url()."cron_job/send_student_image_to_s3_launch");
        $defaultOptions     = array (
                                CURLOPT_POST => 1,
                                CURLOPT_POSTFIELDS => json_encode($s3_params),
                                CURLOPT_RETURNTRANSFER => false ,
                                CURLOPT_TIMEOUT_MS => 1000,
                             );
        curl_setopt_array($curlHandle , $defaultOptions);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, FALSE);     
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2); 
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array(
            'request-token: '.sha1(config_item('acct_domain').config_item('id')),
        ));
        $result = curl_exec($curlHandle);
        curl_close($curlHandle);
    }
    
    function save_user_image()
    {
        $user_id                = $this->input->post('id');
        $directory              = user_upload_path();
        if(!file_exists($directory)){
            mkdir($directory, 0777, true);
        }
        $config                     = array();
        $config['upload_path']      =  $directory;
        $config['allowed_types']    = 'gif|jpg|png|jpeg';
        //$new_name                   = $_FILES['file']['name'];
        //$new_name                   = explode('.', $new_name);
        $new_name                   = $user_id.'.jpg';//$user_id.'.'.$new_name[sizeof($new_name)-1];
        $config['file_name']        = $new_name;
        $config['overwrite']        = TRUE;
        $this->load->library('upload');
        $this->upload->initialize($config);
        $this->upload->do_upload('file');
        $uploaded_data = $this->upload->data();
        
        $new_file               = $this->crop_image($uploaded_data);
        
        $has_s3     = $this->settings->setting('has_s3');
        if( $has_s3['as_superadmin_value'] && $has_s3['as_siteadmin_value'] )
        {
            $user_profile_path = user_upload_path().$new_file;
            uploadToS3($user_profile_path, $user_profile_path);
            unlink($user_profile_path);
        }
        
        echo json_encode(array('user_image' => user_path().$new_file));
    }
    
    function crop_image($uploaded_data)
    {
        $source_path = $uploaded_data['full_path'];
        define('DESIRED_IMAGE_WIDTH', 155);
        define('DESIRED_IMAGE_HEIGHT', 155);
        /*
         * Add file validation code here
         */

        list($source_width, $source_height, $source_type) = getimagesize($source_path);

        switch ($source_type)
        {
            case IMAGETYPE_GIF:
                $source_gdim = imagecreatefromgif($source_path);
                break;
            case IMAGETYPE_JPEG:
                $source_gdim = imagecreatefromjpeg($source_path);
                break;
            case IMAGETYPE_PNG:
                $source_gdim = imagecreatefrompng($source_path);
                break;
        }

        $source_aspect_ratio = $source_width / $source_height;
        $desired_aspect_ratio = DESIRED_IMAGE_WIDTH / DESIRED_IMAGE_HEIGHT;

        if ($source_aspect_ratio > $desired_aspect_ratio)
        {
            /*
             * Triggered when source image is wider
             */
            $temp_height = DESIRED_IMAGE_HEIGHT;
            $temp_width = ( int ) (DESIRED_IMAGE_HEIGHT * $source_aspect_ratio);
        } else {
            /*
             * Triggered otherwise (i.e. source image is similar or taller)
             */
            $temp_width = DESIRED_IMAGE_WIDTH;
            $temp_height = ( int ) (DESIRED_IMAGE_WIDTH / $source_aspect_ratio);
        }

        /*
         * Resize the image into a temporary GD image
         */

        $temp_gdim = imagecreatetruecolor($temp_width, $temp_height);
        imagecopyresampled(
            $temp_gdim,
            $source_gdim,
            0, 0,
            0, 0,
            $temp_width, $temp_height,
            $source_width, $source_height
        );

        /*
         * Copy cropped region from temporary image into the desired GD image
         */

        $x0 = ($temp_width - DESIRED_IMAGE_WIDTH) / 2;
        $y0 = ($temp_height - DESIRED_IMAGE_HEIGHT) / 2;
        $desired_gdim = imagecreatetruecolor(DESIRED_IMAGE_WIDTH, DESIRED_IMAGE_HEIGHT);
        imagecopy(
            $desired_gdim,
            $temp_gdim,
            0, 0,
            $x0, $y0,
            DESIRED_IMAGE_WIDTH, DESIRED_IMAGE_HEIGHT
        );

        /*
         * Render the image
         * Alternatively, you can save the image in file-system or database
         */

        //header('Content-type: image/jpeg');
        $directory          = user_upload_path();
        $this->make_directory($directory);
        imagejpeg($desired_gdim, $directory.$uploaded_data['raw_name'].'.jpg');

        /*
         * Add clean-up code here
         */
        return $uploaded_data['raw_name'].'.jpg';
    }
    
    private function make_directory($path=false)
    {
        if(!$path )
        {
            return false;
        }
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }
    
    function save_users3_responseURL()
    {
        $updated_date           = date('Y-m-d H:i:s');
        $user_id                = $this->input->post('id');
        $user_img_url           = $this->input->post('filepath');
        
        $encode_url             = urldecode($user_img_url);
        $url_keys               = parse_url($encode_url); // parse the url
        $url_path               = explode("/", $url_keys['path']); // splitting the path
        $user_img_name          = end($url_path); // get the value of the last element 
        
        $save['id']             = $user_id;
        $save['us_image']       = $user_img_name."?v=".rand(10,1000);
        $save['action_by']      = $this->auth->get_current_admin('id');
        $save['action_id']      = $this->actions['update'];
        $save['updated_date']   = $updated_date;
        
        
        $this->User_model->save($save);
        $this->session->set_flashdata('message', lang('user_image_saved')); 
    }
    
    function s3objetcs()
    {
        $response               = array();
        $response['error']      = true;
        $response['s3_object']  = array();
        $s3_setting             = $this->settings->setting('has_s3');
        if( $s3_setting['as_superadmin_value'] && $s3_setting['as_siteadmin_value'] )
        {
            $response['error']      = false;
            $s3_keys                = $s3_setting['as_setting_value']['setting_value'];

            define('S3_BUCKET', $s3_keys->s3_bucket);
            define('S3_KEY',    $s3_keys->s3_access);
            define('S3_SECRET', $s3_keys->s3_secret);
            define('S3_ACL',    'private');

            $now            = time() + (12 * 60 * 60 * 1000);
            $expire         = gmdate('Y-m-d\TH:i:s\Z', $now);
            $url            = 'https://' . S3_BUCKET . '.s3.amazonaws.com'; 
            $policy         = json_encode(  
                                    array(
                                        'expiration' => '2020-01-01T00:00:00Z',
                                        'conditions' => array(
                                            array( 'bucket' => S3_BUCKET ),
                                            array( 'acl' => S3_ACL ), 
                                            array( 'starts-with', '$key', '',),
                                            array( "success_action_status" => "201" )
                                        )
                                    )
                                  );  
            $base64Policy   = base64_encode($policy);
            $signature      = base64_encode(hash_hmac("sha1", $base64Policy, S3_SECRET, $raw_output = true));
            
            $upload_path    = '/'.  catalog_upload_path();
            if(!file_exists($upload_path)){
                mkdir($upload_path, 0777, true);
            }
            
            $response['s3_object']['upload_path']               = $upload_path;
            $response['s3_object']['url']                       = $url;
            $response['s3_object']['access_key']                = S3_KEY;
            $response['s3_object']['acl']                       = S3_ACL;
            $response['s3_object']['success_action_status']     = '201';
            $response['s3_object']['policy']                    = $base64Policy;
            $response['s3_object']['signature']                 = $signature;
            $response['user_path']                              = user_upload_path();
            
        }
        echo json_encode($response);
    }
    
    function export_students($param = false)
    {
        die;
        if(!$param)
        {
            redirect(admin_url('user'));
        }

        $param = json_decode(base64_decode($param), true);
        if(json_last_error() != JSON_ERROR_NONE)
        {
            redirect(admin_url('user'));
        }

        $data                       = array();
        $user_param                 = $this->__role_query_filter;
        $user_param['order_by']     = 'us_name';
        $user_param['direction']    = 'ASC';
        $user_param['select']       = 'id, us_name, us_email, us_phone, us_image, us_role_id, us_institute_id, us_branch, us_branch_code, us_institute_code, us_status, us_degree, us_deleted, us_profile_fields';
        if(!isset($this->__role_query_filter['institute_id']))
        {
            $user_param['institute_id']   = isset($param['institute_id'])?$param['institute_id']:0;
        } 
        else 
        {
            $user_param['institute_id']   = $this->__role_query_filter['institute_id'];
        }
        $user_param['branch']       = isset($param['branch_id'])?$param['branch_id']:0;
        $user_param['batch_id']     = isset($param['batch_id'])?$param['batch_id']:0;
        $user_param['keyword']      = isset($param['keyword'])?strtolower($param['keyword']):'';
        $user_param['filter']       = isset($param['filter'])?$param['filter']:'all';
        $user_param['role_id']      = '2';
        $data['students']           = $this->User_model->users($user_param);  

        /*============fetching institue details==============*/
        $data['institutes']         = array();
        $objects                    = array();
        $objects['key']             = 'institutes'; 
        $callback                   = 'institutes';
        $institutes                 = $this->memcache->get($objects, $callback);
        if(!empty($institutes))
        {
            foreach($institutes as $institute)
            {
                $data['institutes'][$institute['id']] = $institute;
            }
        }
        /*======================================================================= */

        /*============fetching branch details ==============*/
        $data['branches']           = array();
        $objects                    = array();
        $objects['key']             = 'branches'; 
        $callback                   = 'branches';
        $branches                 = $this->memcache->get($objects, $callback);
        if(!empty($branches))
        {
            foreach($branches as $branch)
            {
                $data['branches'][$branch['id']] = $branch;
            }
        }
        /*======================================================================= */
        $data['profiles'] = isset($param['profiles'])?$param['profiles']:array();
        // echo '<pre>'; print_r($data);die;
        $this->load->view(config_item('admin_folder').'/export_students', $data);    
    }
    
    
    function profile($id = false,$view = false) 
    {    
    
        if(!in_array(1, $this->user_privilege))
        {
            redirect($this->config->item('admin_folder'));		
        }
        
        $user_param        = $this->__role_query_filter;		
        $user_param['id']  = $id;	
        $user_param['select']   = 'users.id, us_name, us_image, us_role_id, us_email, us_phone, us_status, us_institute_id, us_branch, us_branch_code, us_profile_fields, us_deleted';
        $user_details      = $this->User_model->user($user_param);		
        if(!$user_details)		
        {		
            redirect($this->config->item('admin_folder').'/user');		
        }
                
        $data               = array();
        $breadcrumb         = array();
        $breadcrumb[]       = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]       = array( 'label' => lang('manage_users'), 'link' => admin_url('user'), 'active' => '', 'icon' => '' );
        $breadcrumb[]       = array( 'label' => $user_details['us_name'], 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb'] = $breadcrumb;
        $data['title']      = 'Profile '.$user_details['us_name'];        
        $data['permission'] = array();
        foreach($this->privilege as $access => $key)
        {
            $data['permission'][$access] = in_array($key, $this->user_privilege)?1:0;
        }
        //setting institute deyails
        $objects        = array();
        $objects['key'] = 'institute_'.$user_details['us_institute_id'];
        $callback       = 'institute';
        $institute      = $this->memcache->get($objects, $callback, array('id' => $user_details['us_institute_id']));        
        if(!empty($institute))
        {
            $user_details['institute_name'] = $institute['ib_name'];
        }
        //End
        //setting branch details
        $objects              = array();
        $objects['key']       = 'branch_'.$user_details['us_branch'];
        $callback             = 'branch';
        $branch               = $this->memcache->get($objects, $callback, array('id' => $user_details['us_branch'])); 
        if(!empty($branch))
        {
            $user_details['branch_name'] = $branch['branch_name'];
        }        
        //end
        $data['user']           = $user_details;
        //echo '<pre>'; print_r($user_details);die;
        $course_param                       = array();	
        $course_param['user_id']            = $user_details['id'];	
        $course_param['not_bundle_course']  = true;	
        $course_param['select']             = 'course_subscription.id, course_subscription.cs_user_id, course_subscription.cs_course_id, course_subscription.cs_approved, course_subscription.cs_percentage as percentage, course_basics.cb_title';		
        $data['user_course_enrolled']       = $this->User_model->subscriptions($course_param); 
        //echo '<pre>'; print_r($data['user_course_enrolled']);die;
        $data['total_enrolled_course']      = sizeof($data['user_course_enrolled']);



        $bundle_param                       = array();
        $bundle_param['user_id']            = $user_details['id'];
        $bundle_param['expired_bundles']    = true; 
        $data['user_bundle_enrolled']       = $this->Bundle_model->enrolled_bundles($bundle_param); 
        
        //getting the fields asscodiated with this user
        $this->load->model('Settings_model');
        $data['user_profile_fields'] = array();
        
        $user_profile_fields         = isset($user_details['us_profile_fields']) ? explode('{#}', $user_details['us_profile_fields']) : array();
        //echo '<pre>'; print_r($session);die;
        if (!empty($user_profile_fields)) {
            foreach ($user_profile_fields as $field) {
                $field                             = substr($field, 2);
                $field                             = substr($field, 0, -2);
                $temp_field                        = explode('{=>}', $field);
                $key                               = isset($temp_field[0]) ? $temp_field[0] : 0;
                $value                             = isset($temp_field[1]) ? $temp_field[1] : '';
                $data['user_profile_fields'][$key] = $value;
            }
        }
        $profile_blocks         = $this->Settings_model->blocks();
        $data['profile_blocks'] = array();
        if (!empty($profile_blocks)) {
            foreach ($profile_blocks as $profile_block) {
                $profile_block['profile_fields']              = $this->Settings_model->profile_fields(array(
                                         'block_id' => $profile_block['id']
                ));
                $data['profile_blocks'][$profile_block['id']] = $profile_block;
            }
        }
        //End
        if((isset($_GET['v']))&&($_GET['v']==1))
        {
            /*Log creation*/
            $user_data                      = array();
            $user_data['user_id']           = $this->__loggedInUser['id'];
            $user_data['username']          = $this->__loggedInUser['us_name'];
            $user_data['useremail']          = $this->__loggedInUser['us_email'];
            $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
            $message_template               = array();
            $message_template['username']   = $this->__loggedInUser['us_name'];
            $message_template['student']    = $user_details['us_name'].'-'.$user_details['us_phone'];
            $triggered_activity             = 'student_profile_viewes';
            log_activity($triggered_activity, $user_data, $message_template);
        } 
        
        $this->load->view($this->config->item('admin_folder').'/user_profile',$data);
    }
    
    
    function save_profile_values() 
    {
        if(!in_array(3, $this->user_privilege))
        {
            echo json_encode(array(
                'message' => 'You don\'t have permission to perform this action',
                'status'  => 2
            ));
            exit;
        }
        $this->load->model('Settings_model');
        $user_id        = $this->input->post('id');
        $profile_values = json_decode($this->input->post('profile_values'));
        $message        = '';
        $error          = false;
        $user_field_values = array();
        $old_value = $this->Settings_model->profile_field_values(array(
                                 'user_id' => $user_id
        ));
        $old_value = isset($old_value['us_profile_fields']) ? explode('{#}', $old_value['us_profile_fields']) : array();
        if (!empty($old_value)) 
        {
            foreach ($old_value as $field) 
            {
                $field                   = substr($field, 2);
                $field                   = substr($field, 0, -2);
                $temp_field              = explode('{=>}', $field);
                $key                     = isset($temp_field[0]) ? $temp_field[0] : 0;
                $value                   = isset($temp_field[1]) ? $temp_field[1] : '';
                $user_field_values[$key] = $value;
            }
        }
        if (!empty($profile_values)) 
        {
            $profile_block_id = 0;
            foreach ($profile_values as $name => $value) 
            {
                $field_object                           = $this->Settings_model->profile_field(array(
                                         'field_name' => $name
                ));
                $user_field_values[$field_object['id']] = $value;
            }
            $us_profile_field = array();
            if (!empty($user_field_values)) 
            {
                foreach ($user_field_values as $field_id => $field_value) {
                    $us_profile_field[] = '{{' . $field_id . '{=>}' . $field_value . '}}';
                }
                $us_profile_field = implode('{#}', $us_profile_field);
            }
            $save                      = array();
            $save['id']                = $user_id;
            $save['us_profile_fields'] = (($us_profile_field) ? $us_profile_field : '');
            $this->Settings_model->save_profile_field_value($save);
            /*Log creation*/
            $user_data                      = array();
            $user_data['user_id']           = $this->__loggedInUser['id'];
            $user_data['username']          = $this->__loggedInUser['us_name'];
            $user_data['useremail']          = $this->__loggedInUser['us_email'];
            $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
            $message_template               = array();
            $message_template['username']   = $this->__loggedInUser['us_name'];
            $message_template['student']    = $this->input->post('user_name').'-'.$this->input->post('user_phone');
            $triggered_activity             = 'student_profile_updated';
            log_activity($triggered_activity, $user_data, $message_template);
        }
        echo json_encode(array(
                                 'message' => 'Student details saved successfully',
                                 'error'   => false
        ));
    }

    
    function save_profile_about() 
    {
        if(!in_array(3, $this->user_privilege))
        {
            echo json_encode(array(
                'message' => 'You don\'t have permission to perform this action',
                'status'  => 2
            ));
            exit;
        }
        $response               = array();
        $response['status']     = 1;
        $response['message']    = 'Student details saved successfully';
        $this->load->model('User_model');
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('', '</br>');
        $this->form_validation->set_rules('us_name', 'lang:add_user_name', 'required');
        $this->form_validation->set_rules('email_id', 'lang:add_user_email', 'valid_email');
        if ($this->form_validation->run() == FALSE)
        {
            $response['status']   = 2;
            $response['message'] = validation_errors();
            echo json_encode($response);die;
        }
        $user_param         = array();
        $user_param['id']   = $this->input->post('id');		
        $user               = $this->User_model->user($user_param);		
        $email_id           = $this->input->post('email_id');
        $phone_number       = $this->input->post('phone_number');
        $us_name            = $this->input->post('us_name');
        $message            = '';
        $email_changed      = false;
        //saving user details
        $save       = array();
        $save['id'] = $user['id'];
        $this->load->model('Authenticate_model');
        if(!$email_id && !$phone_number)
        {
            $response['status']      = 2;
            $response['message'] = 'Both email id and phone number cannot be empty together';
            echo json_encode($response);die;
        }
        if(!$phone_number)
        {
            $response['status']      = 2;
            $response['message'] = 'Phone number cannot be empty';
            echo json_encode($response);die;
        }
        $save['us_phone']       = $phone_number;
        if(!$us_name)
        {
            $response['status']      = 2;
            $response['message'] = 'Name cannot be empty';
            echo json_encode($response);die;
        }
        $save['us_name']       = $us_name;
        
        if($email_id) 
        {
            $email_id_available   = $this->Authenticate_model->get_user_by_field(array('email' => $email_id, 'explicit_user_id' => $user['id']));
            if($email_id_available)
            {
                $response['status']      = 2;
                $message                .= 'Email is already in use';
            }
            else
            {
                $email_changed          = ($email_id!=$user['us_email']);
                if($email_changed)                   
                {
                    $save['us_email_verified']       = '0';
                    $user['us_email_verified']       = '0';
                }
            }
        }
        $save['us_email']       = $email_id;    
        if($phone_number)
        {
            $phone_number_available   = $this->Authenticate_model->get_user_by_field(array('phone_number' => $phone_number, 'explicit_user_id' => $user['id']));
            if($phone_number_available)
            {
                $response['status']      = 2;
                $message                .= '<br />Phone number already occupied';
            }
        }
        $save['us_phone']       = $phone_number;
        //End
        if($response['status'] == 1)
        {
            $user_key               = "user_".$user['id'];
            $profile_change_status  = array(
                                           "status" => "1"
                                       );
            $this->memcache->set($user_key, $profile_change_status);
            $this->User_model->save($save);
            if($email_changed)
            {
                $email_token        = md5(openssl_random_pseudo_bytes(64));
                $to_email		    = $email_id;
                //Save token for verification 
                $token                  = array();
                $token['et_user_id']    = $user['id'];
                $token['et_user_email'] = $to_email;
                $token['et_account_id'] = config_item('id');
                $token['et_token']      = $email_token;
                $token['et_status']     = '1';
                $token['et_change_status'] = '0';
                $this->User_model->save_token($token);
                //End saving token 
                $template           = $this->ofabeemailer->template(array('email_code' => 'email_id_changed'));
                $param              = array();
                $param['to'] 	    = array($to_email);
                $param['subject'] 	= $template['em_subject'];
                $contents           = array(
                                            'user_name' => $user['us_name']
                                            ,'site_name' => config_item('site_name')
                                            ,'verification_link' => site_url('register/verify/'.$email_token)
                                    );
                $param['body']      = $this->ofabeemailer->process_mail_content($contents, $template['em_message']);
                $send = $this->ofabeemailer->send_mail($param);
                $response['message'] = 'Your details saved successfully.<br />Since you have changed your email id and inorder to get notifications please verify your new email id by clicking the verification link that is sent to your mail id.';
                $response['status'] = 3;
            }

            /*Log creation*/
            $user_data                      = array();
            $user_data['user_id']           = $this->__loggedInUser['id'];
            $user_data['username']          = $this->__loggedInUser['us_name'];
            $user_data['useremail']          = $this->__loggedInUser['us_email'];
            $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
            $message_template               = array();
            $message_template['username']   = $this->__loggedInUser['us_name'];
            $message_template['student']    = $us_name.'-'.$phone_number;
            $triggered_activity             = 'student_profile_updated';
            log_activity($triggered_activity, $user_data, $message_template);
        }
        else
        {
            $response['message'] = $message;
        }
        echo json_encode($response);
    }
    
    function course_view_completed()
    {
        $save                           = array();
        $save['id']                     = $this->auth->get_current_admin();
        $save['us_course_first_view']   = '0';
        $admin                          = $this->auth->get_current_user_session();
        $admin['us_course_first_view']  = '0';
        $save['updated_date']           = date('Y-m-d H:i:s');
        $admin['admin']                 = $admin;
        $this->session->set_userdata($admin);
        $this->User_model->save($save);
    }
    
    function getusers()
    {

        $UserDetailsHTML         = '';  
	    $count			 = 1;
        
        $users_detail            = $this->User_model->users(array('direction'=>'DESC', 'role_id' => '2' ));
        foreach($users_detail as $user_data)
        { 
            $UserDetailsHTML		.= '	  <tr>';
            $UserDetailsHTML		.= '  		<td>'.($count++).'</td>';
            $UserDetailsHTML		.= '		<td>'.((isset($user_data['us_name'])&& $user_data['us_name'])? $user_data['us_name']:'NIL').'</td>';
            $UserDetailsHTML		.= '		<td>'.((isset($user_data['us_email']) && $user_data['us_email'])? $user_data['us_email']:'NIL').'</td>';
            $UserDetailsHTML		.= '	  </tr>';	
        }
        if(empty($users_detail)){ 
            $UserDetailsHTML		.= '	  <tr>';
            $UserDetailsHTML		.= '		<td colspan="8">No details found.</td>';
            $UserDetailsHTML		.= '	  </tr>';	
        }
        return $UserDetailsHTML;
    }
    
    function get_email_templates(){
        $data = array();
        $data['mail_template']  = $this->Mailtemplate_model->mail_templates(array('status' => '1'));
        
        echo json_encode($data);
    }
    
    function get_template_data(){
        $template_id    = $this->input->post('mail_template_id');
        $data           = array();
        $data['mail_template_data'] = $this->Mailtemplate_model->mail_template(array('id' => $template_id));
        echo json_encode($data);  
    }
    
    function send_invite_user(){
        $subject    = $this->input->post('invite_subject');
        $message    = base64_decode($this->input->post('invite_message'));
        $emails     = $this->input->post('invite_emails');
        $user_mails = json_decode($emails);
        $save       = array();
        
        foreach ($user_mails as $user_mail){
            $get_invited_users      = $this->User_model->invited_users(array('email' => $user_mail));
            $save['iu_email_id']    = $user_mail;
            $save['iu_account_id']  = $this->config->item('id');
            $save['iu_invited_by']  = $this->auth->get_current_admin('id');
            if($get_invited_users == ''){
                $this->User_model->save_invite_user($save);
            }
        }
        
        $param['from']		= $this->config->item('site_email');
        $param['to']        = $user_mails;
        $param['subject'] 	= $subject;
        $param['body'] 		= $message;
        
        $send = $this->ofabeemailer->send_mail($param);
        
        //print_r($send);die;
    }
   
    function send_message_user()
    {
        $subject    = $this->input->post('send_user_subject');
        $message    = base64_decode($this->input->post('send_user_message'));
        $email      = $this->input->post('user_email');
        $user_id    = $this->input->post('user_id');
        
        //sending notification
        if($user_id)
        {
            $system_message     = array();
            $random_message_id  = rand(1000, 9999);
            $date_time          = date(DateTime::ISO8601);
            $system_message[]   = array(
                "messageId"     => $random_message_id,
                "senderId"      => $this->__loggedInUser['id'],
                "senderName"    => $this->__loggedInUser['us_name'],
                "senderImage"   => user_path().$this->__loggedInUser['us_image'],
                "receiverId"    => $user_id,
                "message"       => $message,
                "datetime"      => $date_time
            );
            if(!empty($system_message))
            {
                $this->load->library('JWT');
                $payload                     = array();
                $payload['id']               = $this->__loggedInUser['id'];
                $payload['email_id']         = $this->__loggedInUser['us_email'];
                $payload['register_number']  = '';
                $token                       = $this->jwt->encode($payload, config_item('jwt_token')); 
                $response                    = array();
                $response['notified']        = send_notification_to_mongo($system_message, $token);
            }    
        }
        $param['from']		= $this->config->item('site_email');
        $param['to']        = $email;
        $param['subject'] 	= $subject;
        $param['body'] 		= $message;
        
        $send = $this->ofabeemailer->send_mail($param);
    }
    
    function get_invited_users()
    {
        $data = array();
        $data['invited_users_list'] = $this->User_model->get_invited_users();
        echo json_encode($data);
    }
    function add_user_to_group_new(){

        $user_id        = $this->input->post('user_id');
        $groups         = $this->input->post('groups');
        $groups_list    = json_decode($groups);

        // $course_name    = array();
        if(!empty($groups_list))
        { 
            $user_param             = array();
            $user_param['id']       = $user_id;
            $user_param['select']   = 'us_groups,us_name,us_phone,us_email';
            $user_groups            = $this->User_model->user($user_param);
            
            $groups         = explode(",",$user_groups['us_groups']);
            $new_groups     = array_diff($groups_list,$groups);
            $group_size     = count($new_groups);
            $groups         = array_merge($groups,$groups_list);
            $groups         = array_unique(array_filter($groups));

            
            $group_string       = implode(",",$groups);
            $save               = array();
            $save['us_groups']  = $group_string;
            $save['id']         = $user_id;
            $result             = $this->User_model->save($save);
            $payment_data       = array();

            /*Course subscription to the user depends on the added group - Start */
            foreach($groups_list  as $group_list)
            {
                $group_courses                                      = $this->Group_model->group_courses(array('group_id' => $group_list,'select'=>'id,cb_code,cb_title,cb_price,cb_discount,cb_tax_method,cb_is_free,cb_access_validity,cb_validity,cb_validity_date'));
                if(!empty($group_courses))
                {
                    foreach($group_courses as $group_course)
                    {
                        $save_subscription                          = array();
                        $save_subscription['cs_user_id']            = $user_id;
                        $save_subscription['cs_course_id']          = $group_course['id'];
                        $save_subscription['cs_approved']           = '1';

                        $course_subscription_date                   = date("Y-m-d H:i:s");
                        $course_validity                            = (($group_course['cb_validity']) && $group_course['cb_validity'] > 0)?$group_course['cb_validity']:1;
                        $course_startdate                           = date("Y-m-d",time());

                        if($group_course['cb_access_validity'] == 2)
                        {
                            $course_enddate                         = $group_course['cb_validity_date'];
                        }
                        else
                        {
                            $course_enddate                         = ($group_course['cb_access_validity'] == 0)?'2070-12-31':date('Y-m-d', time() + ($course_validity - 1) * 60 * 24 * 60);
                        }

                        $save_subscription['updated_date']          = $course_subscription_date;
                        $save_subscription['cs_subscription_date']  = $course_subscription_date;
                        $save_subscription['cs_start_date']         = $course_startdate;
                        $save_subscription['cs_end_date']           = $course_enddate;
                        $save_subscription['action_by']             = $this->__loggedInUser['id'];
                        $this->User_model->save_subscription($save_subscription);

                        /* Payment Details */
                        $user_details                               = array();
                        $user_details['name']                       = $user_groups['us_name'];
                        $user_details['email']                      = $user_groups['us_email'];
                        $user_details['phone']                      = $user_groups['us_phone'];

                        $payment_param                              = array();
                        $payment_param['ph_user_id']                = $user_id;
                        $payment_param['ph_user_details']           = json_encode($user_details);
                        $payment_param['ph_item_id']                = $group_course['id'];
                        $payment_param['ph_item_type']              = '1';
                        $payment_param['ph_item_code']              = $group_course['cb_code'];
                        $payment_param['ph_item_name']              = $group_course['cb_title'];
                        $payment_param['ph_item_base_price']        = $group_course['cb_price'];
                        $payment_param['ph_item_discount_price']    = $group_course['cb_discount'];
                        $payment_param['ph_tax_type']               = $group_course['cb_tax_method'];

                        $course_price                               = ($group_course['cb_discount']!=0)?$group_course['cb_discount']:$group_course['cb_price'];
                        
                        if($group_course['cb_is_free'] == '1')
                        {
                            $payment_param['ph_item_base_price']        = 0;
                            $payment_param['ph_item_discount_price']    = 0;
                            $course_price = 0;
                        }
                        
                        $gst_setting                                = $this->settings->setting('has_tax');
                        $cgst                                       = ($gst_setting['as_setting_value']['setting_value']->cgst != '')?$gst_setting['as_setting_value']['setting_value']->cgst:'0';
                        $sgst                                       = ($gst_setting['as_setting_value']['setting_value']->sgst != '')?$gst_setting['as_setting_value']['setting_value']->sgst:'0';
                        $sgst_price                                 = ($sgst / 100) * $course_price;
                        $cgst_price                                 = ($cgst / 100) * $course_price;
                        //cb_tax_method = 1 is exclusive
                        

                        if($group_course['cb_tax_method'] == '1')
                        {
                            $total_course_price         = $course_price+$sgst_price+$cgst_price;
                        }
                        else 
                        {
                            $total_course_price         = $course_price;
                            $sgst_price                 = ($course_price / (100 + $sgst)) * $sgst;
                            $cgst_price                 = ($course_price / (100 + $cgst)) * $cgst;
                        }
                        
                        $payment_tax_object                         = array();
                        $payment_tax_object['sgst']['percentage']   = $sgst;
                        $payment_tax_object['sgst']['amount']       = round($sgst_price, 2); 
                        $payment_tax_object['cgst']['percentage']   = $cgst;
                        $payment_tax_object['cgst']['amount']       = round($cgst_price, 2); 

                        $payment_param['ph_tax_objects']            = json_encode($payment_tax_object);
                        
                        $payment_param['ph_item_amount_received']   = round($total_course_price, 2);
                        $payment_param['ph_payment_mode']           = '3';

                        $transaction_details                        = array();
                        $transaction_details['transaction_id']      = '';
                        $transaction_details['bank']                = 'By cash';
                        $payment_param['ph_transaction_id']         = '';
                        $payment_param['ph_transaction_details']    = json_encode($transaction_details);
                        $payment_param['ph_account_id']             = config_item('id');
                        $payment_param['ph_payment_gateway_used']   = 'Offline';
                        $payment_param['ph_status']                 = '1';
                        $payment_param['ph_payment_date']           = date('Y-m-d H:i:s');
                        unset($group_course['cb_groups']);
                        $payment_param['ph_item_other_details']     = json_encode($group_course);
                        $payment_data[]                             = $payment_param;
                    }
                }
            }
            $this->memcache->delete('enrolled_'.$user_id);
            $this->memcache->delete('mobile_enrolled_'.$user_id);

            if($payment_data)
            {
                $this->load->model('Payment_model');
                $order_ids = $this->Payment_model->save_history_bulk($payment_data);
        
                if(!empty($order_ids))
                {
                    $order_data                      = array();
                    foreach($order_ids as $order_id)
                    {
                        $order_param                 = array();
                        $order_param['id']           = $order_id;
                        $order_param['ph_order_id']  = date('Y').date('m').date('d').$order_id;
                        $order_data[]                = $order_param;
                    }
                    $this->Payment_model->update_history_bulk($order_data);
                }
            }
            /*Course subscription to the user depends on the added group - End */

            if($result!=null){
                /*Log creation*/
                $group_count                    = ($group_size > 1 ) ? $group_size.' batches': 'a batch';
                $user_data                      = array();
                $user_data['user_id']           = $this->__loggedInUser['id'];
                $user_data['username']          = $this->__loggedInUser['us_name'];
                $user_data['useremail']         = $this->__loggedInUser['us_email'];
                $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
                $message_template               = array();
                $message_template['username']   = $this->__loggedInUser['us_name'];
                $message_template['student']    = $user_groups['us_name'].'-'.$user_groups['us_phone'];
                $message_template['count']      = $group_count;
                $triggered_activity             = 'add_student_to_batch';
                log_activity($triggered_activity, $user_data, $message_template);

                $response = array();
                $response['success'] = 'true';
                $response['message'] = (sizeof($groups_list) > 1)? 'Student added to Batches':'Student added to Batch';                
                echo json_encode($response);
            }

        }else{

            $response               = array();
            $response['success']    = 'false';
            $response['message']    = 'Please select any Batch';
            echo json_encode($response);
        }
        
    }
    
    function add_user_to_course_new()
    {

        $user_id            = $this->input->post('user_id');
        $courses            = $this->input->post('courses');
        $courses_list       = json_decode($courses);
        $course_name        = array();
        $course_list_notify = array();
        $payment_data           = array();
        $user_param             = array();
        $user_param['id']       = $user_id;
        $user_param['verified'] = true;
        $user_param['select']   = 'users.id,users.us_name,users.us_email,users.us_groups, users.us_phone';
        $user_subscribed        = $this->User_model->get_user_details($user_param);
        $subscribed_user        = $user_subscribed;
        
        if(!empty($courses_list))
        {
            $subscribed_courses = array();
            foreach ($courses_list as $course_id)
            {
                //check user already subscribed
                $subscription = $this->User_model->subscription(array('count'=>true,'user_id' => $user_id, 'course_id' => $course_id,'limit'=>1));
                
                if($subscription <= 0)
                {
                    $course_param                   = array();
                    $course_param['select']         = 'id,cb_title,cb_is_free,cb_access_validity,cb_validity,cb_validity_date,cb_price,cb_discount,cb_tax_method';  
                    $course_param['id']             = $course_id;
                    $course                         = $this->Course_model->course($course_param);
                    $course_name[]                  = $course['cb_title'];
                    $course_list_notify[]           = array('course_id'=>$course['id'],'course_name'=>$course['cb_title']);
                    // $role_id                        = $this->__loggedInUser['us_role_id'];
                    // $privilage_user                 = array_column($this->User_model->user_roles(array('role_id'=>$role_id)),'rl_name');
                    
                    $save                               = array();
                    $save['id']                         = false;
                    $save['cs_course_id']               = $course_id;
                    $save['cs_user_id']                 = $user_id;
                    $save['cs_subscription_date']       = date('Y-m-d H:i:s');
                    $save['cs_start_date']              = date('Y-m-d');
                    $save['cs_course_validity_status']  = $course['cb_access_validity'];
                    $save['cs_user_groups']             = $user_subscribed['us_groups'];

                    if ($course['cb_access_validity'] == 2) {

                        $course_enddate = $course['cb_validity_date'];
                    } else if ($course['cb_access_validity'] == 0) {

                        $course_enddate = date('Y-m-d', strtotime('+3000 days'));
                    } else {

                        $duration = ($course['cb_validity']) ? $course['cb_validity'] : 0;
                        $course_enddate = date('Y-m-d', strtotime('+' . $duration . ' days'));
                    }
                                        
                    $save['cs_end_date']            = $course_enddate;
                    $save['cs_approved']            = '1';
                    $save['action_by']              = $this->__loggedInUser['id'];
                    $save['action_id']              = '1';  
                    $subscribed_courses[]           = $save;

                    //----------
                    /* Payment Details */
                    $user_details                               = array();
                    $user_details['name']                       = $subscribed_user['us_name'];
                    $user_details['email']                      = $subscribed_user['us_email'];
                    $user_details['phone']                      = $subscribed_user['us_phone'];

                    $payment_param                              = array();
                    $payment_param['ph_user_id']                = $subscribed_user['id'];
                    $payment_param['ph_user_details']           = json_encode($user_details);
                    $payment_param['ph_item_id']                = $course_id;
                    $payment_param['ph_item_type']              = '1';
                    $payment_param['ph_item_name']              = $course['cb_title'];
                    $payment_param['ph_item_base_price']        = $course['cb_price'];
                    $payment_param['ph_item_discount_price']    = $course['cb_discount'];
                    $payment_param['ph_tax_type']               = $course['cb_tax_method'];
                    $course_price                               = ($course['cb_discount']!=0)?$course['cb_discount']:$course['cb_price'];

                    if( $course['cb_is_free'])
                    {
                        $course_price                           = 0;
                    }

                    $gst_setting                                = $this->settings->setting('has_tax');
                    $cgst                                       = ($gst_setting['as_setting_value']['setting_value']->cgst != '')?$gst_setting['as_setting_value']['setting_value']->cgst:'0';
                    $sgst                                       = ($gst_setting['as_setting_value']['setting_value']->sgst != '')?$gst_setting['as_setting_value']['setting_value']->sgst:'0';
                    
                    $sgst_price                                 = ($sgst / 100) * $course_price;
                    $cgst_price                                 = ($cgst / 100) * $course_price;

                    //cb_tax_method = 1 is exclusive
                    
                    if($course['cb_tax_method'] == '1')
                    {
                        $total_course_price                     = $course_price+$sgst_price+$cgst_price;
                    }
                    else 
                    {
                        $total_course_price                     = $course_price;
                        $sgst_price                             = ($course_price / (100 + $sgst)) * $sgst;
                        $cgst_price                             = ($course_price / (100 + $cgst)) * $cgst;
                    }

                    $payment_tax_object                         = array();
                    $payment_tax_object['sgst']['percentage']   = $sgst;
                    $payment_tax_object['sgst']['amount']       = round($sgst_price, 2); 
                    $payment_tax_object['cgst']['percentage']   = $cgst;
                    $payment_tax_object['cgst']['amount']       = round($cgst_price, 2); 
                    $payment_param['ph_tax_objects']            = json_encode($payment_tax_object);
                    
                    $payment_param['ph_item_amount_received']   = round($total_course_price, 2);
                    $payment_param['ph_payment_mode']           = '3';

                    $transaction_details                        = array();
                    $transaction_details['transaction_id']      = '';
                    $transaction_details['bank']                = 'By cash';
                    $payment_param['ph_transaction_id']         = '';
                    $payment_param['ph_transaction_details']    = json_encode($transaction_details);
                    $payment_param['ph_account_id']             = config_item('id');
                    $payment_param['ph_payment_gateway_used']   = 'Offline';
                    $payment_param['ph_status']                 = '1';
                    $payment_param['ph_payment_date']           = date('Y-m-d H:i:s');
                    $payment_param['ph_item_other_details']     = json_encode($course);
                    if($total_course_price)
                    {
                        $payment_data[]                         = $payment_param;
                    }
                //----------------
                }
            }
                $result                         = false;
            if(!empty($subscribed_courses)){
                $result                         = $this->User_model->subscription_save($subscribed_courses);
            }

            if($payment_data)
            {
                $this->load->model('Payment_model');
                $order_ids = $this->Payment_model->save_history_bulk($payment_data);
                if(!empty($order_ids))
                {
                    $order_data                      = array();
                    foreach($order_ids as $order_id)
                    {
                        $order_param                 = array();
                        $order_param['id']           = $order_id;
                        $order_param['ph_order_id']  = date('Y').date('m').date('d').$order_id;
                        $order_data[]                = $order_param;
                    }
                    $this->Payment_model->update_history_bulk($order_data);
                }
            }
            //echo $this->db->last_query();
            //print_r($subscribed_courses); die;
            if($result){

                $response = array();
                $response['success'] = 'true';
                $response['message'] = lang('user_added_to_course');
                echo json_encode($response);
            }
            $this->invalidate_subscription(array('user_id'=>$user_id));

            $user_name          = $user_subscribed['us_name'];
            $verified_email     = $user_subscribed['us_email'];
            $privilage_user     = $this->__loggedInUser['us_name'];
            if(count($course_name)==1){
                $course_title   = implode(" ",$course_name);
                $course_count   = '';
            }else{
                $course_title   = '';
                $course_count   = count($course_name);
            }
            $course_names       = implode(", ",$course_name);

           if (!empty($verified_email) && $course_names!=null) {

                $email_param                = array();
                $email_param['email_code']  = 'course_admin_enrollment';
                $email_param['email_ids']   = $verified_email;
                $email_param['contents']    = array(
                    'username'          => $user_name
                    , 'course_name'     => $course_names
                    , 'privilage_user'  => $privilage_user
                    , 'date'            => date('Y-M-d h:i:sa')
                    , 'site_url'        => site_url()
                    , 'site_name'       => config_item('site_name'),
                );
                $this->send_mail($email_param);

            }
            $notification_params                    = array();
            $notification_params['course_names']    = $course_list_notify;
            $notification_params['user_id']         = $user_id;
            $this->send_system_notification($notification_params);

            $user_data              = array();
            $user_data['user_id']   = $this->__loggedInUser['id'];
            $user_data['username']  = $this->__loggedInUser['us_name'];
            $user_data['useremail']  = $this->__loggedInUser['us_email'];
            $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
            
            $message_template                   = array();
            $message_template['username']       = $this->__loggedInUser['us_name'];;
            $message_template['course_name']    = $course_title;
            $message_template['count']          = $course_count;
            $message_template['student']        = $this->input->post('username');
            
            $triggered_activity                 = 'admin_user_subscribe';
            log_activity($triggered_activity, $user_data, $message_template); 
        }else{

            $response               = array();
            $response['success']    = 'false';
            $response['message']    = 'Please select any Course';
            echo json_encode($response);
        }
        
    }
    
    function add_user_to_course_bulk()
    {
        $users      = $this->input->post('users');
        $courses    = $this->input->post('courses');
        $course_notifications = array();
        $users      = json_decode($users);
        $courses    = json_decode($courses);
        $this->load->library('Ofapay');
        if(!empty($courses) && !empty($users))
        {
            $this->load->model('Tutor_model');
            foreach ($courses as $course_id)
            {
               $course                         = $this->Course_model->course(array('id' => $course_id));
               
                $course_notifications[$course['id']] = array();
                $course_notifications[$course['id']]['name'] = $course['cb_title'];
                $course_notifications[$course['id']]['users'] = array();
               foreach ($users as $user_id)
               {   
                    $course_notifications[$course['id']]['users'][] = $user_id;
                   //check user already subscribed

                   $subscription = $this->User_model->subscription(array('user_id' => $user_id, 'course_id' => $course_id));
                   if(empty($subscription))
                   {
                       $save                           = array();
                       $save['id']                     = false;
                       $save['cs_course_id']           = $course_id;
                       $save['cs_user_id']             = $user_id;
                       $save['cs_subscription_date']   = date('Y-m-d H:i:s');
                       $save['cs_start_date']          = date('Y-m-d');
                       $save['cs_course_validity_status']= $course['cb_access_validity'];
                        if($course['cb_access_validity'] == 2){
                            $end_date   = $course['cb_validity_date'];
                        }else if($course['cb_access_validity'] == 1){ 
                            $current_date                   = strtotime($save['cs_start_date']);  // if today :2013-05-23
                            $end_date                       = date('Y-m-d',strtotime('+'.$course['cb_validity'].' days', $current_date));
                        }else{
                            $end_date                       = '2035-12-31';
                        }
                       //$current_date                   = strtotime($save['cs_start_date']);  // if today :2013-05-23
                       //$end_date                       = date('Y-m-d',strtotime('+'.$course['cb_validity'].' days', $current_date));
                       $save['cs_end_date']            = $end_date;
                       $save['cs_approved']            = '1';
                       $save['action_by']              = $this->auth->get_current_admin('id');
                       $save['action_id']              = $this->actions['activate'];  
                       $this->User_model->save_subscription($save); 
                    
                        
                        //update the payment history 
                        $param                  = array();
                        $param['user_id']       = $user_id;
                        $param['item_id']       = $course_id;
                        $param['payment_mode']  = 3;
                        $param['amount']        = (isset($course['cb_discount']) && $course['cb_discount'] > 0)?$course['cb_discount']:$course['cb_price'];
                        $this->ofapay->save_payment($param);
                        //End
                        
                   }
                   $this->invalidate_subscription(array('user_id'=>$user_id,'course_id'=>$course_id));
                   //End
               }
            }


            foreach($course_notifications as $course_id => $course_notification)
            {
                //Notification
                $this->load->library('Notifier');
                $this->notifier->push(
                    array(
                        'action_code' => 'course_subscribed',
                        'assets' => array('course_name' => $course_notification['name'],'course_id' => $course_id),
                        'target' => $course_id,
                        'individual' => true,
                        'push_to' => $course_notification['users']
                    )
                );
                //End notification
            }
        }
        $response = array();
        $response['error'] = 'false';
        $response['message'] = lang('user_added_to_course');
        echo json_encode($response);
    }
    
    
    function change_status_bulk()
    {
        $status                         = $this->input->post('status');
        $current_status                 = $this->input->post('current');
        $approve                        = $this->input->post('approve');
        $user_ids                       = json_decode($this->input->post('users'));
        $status_bulk                    = array();
        $notification_ids               = array();
        $remove_users                   = array();
        if(!empty($user_ids))
        {
            
            foreach ($user_ids as $user_id) {
                $save                   = array();
                $save['id']             = $user_id;
                $save['us_status']      = $status;
                $save['us_deleted']     = '0';
                $save['action_by']      = $this->auth->get_current_admin('id');
                $save['updated_date']   = date('Y-m-d H:i:s');
                $status_bulk[]          = $save;

                $notification_ids[]     = $user_id;
                $remove_users[]         = $user_id;
            }
            
            if (!empty($status_bulk))
            {
                $this->User_model->save_user($status_bulk);
                /*Log creation*/
                $student_count                  = (sizeof($status_bulk)>1)?sizeof($status_bulk).' students':'a student';
                $user_data                      = array();
                $user_data['user_id']           = $this->__loggedInUser['id'];
                $user_data['username']          = $this->__loggedInUser['us_name'];
                $user_data['useremail']          = $this->__loggedInUser['us_email'];
                $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
                $message_template               = array();
                $message_template['username']   = $this->__loggedInUser['us_name'];
                $message_template['count']      = $student_count;
                if($save['us_status'] == '0')
                {
                    $triggered_activity         = 'student_deactivated_bulk';
                    $this->remove_user($remove_users);
                } 
                else
                {
                    if($approve == 1)
                    {
                        $triggered_activity     = 'student_account_approved_bulk'; 
                    } else {
                        $triggered_activity     = 'student_activated_bulk'; 
                    }
                    
                }
                log_activity($triggered_activity, $user_data, $message_template);
            }

            $this->load->library('Notifier');
            if($current_status == 2)
            {
                $this->notifier->push(
                    array(
                        'action_code' => 'student_account_approved',
                        'individual' => true,
                        'push_to' => $notification_ids
                    )
                );
            }
            else
            {
                if($status == 1)
                {
                    $this->notifier->push(
                        array(
                            'action_code' => 'student_account_activated',
                            'individual' => true,
                            'push_to' => $notification_ids
                        )
                    );
                }
            }
        }
        $data             = array();
        $data['users']    = $this->User_model->users(array(
                                                'role_id' => '2', 
                                                'user_ids' => $user_ids,
                                                'select' => 'id, us_name, us_phone, us_email, us_image, us_role_id, us_institute_id, us_branch, us_branch_code, us_institute_code, us_status, us_degree, us_deleted, us_email_verified'
                                            ));
        $email_content          = array();
        $email_code     = ($status == 1)? 'student_activated' : 'student_deactivated';
        foreach($data['users'] as $user)
        {
            $new_email_param = array();
            if($user['us_email_verified'] == '1')
            {
                $new_email_param['email']       = $user['us_email'];
                $new_email_param['contents']    = array(
                                                        'username' => $user['us_name']
                                                        ,'site_url_login' => site_url('login')
                                                    );
                array_push($email_content,$new_email_param);
            }            
        }   
        $this->process_bulk_mail($email_content,$email_code);
        echo json_encode($data);
    }
    
    function delete_user_bulk()
    {
        $user_ids   = json_decode($this->input->post('users'));
        if(!empty($user_ids))
        {
            $delete_user    = array();
            foreach ($user_ids as $user_id) {
                $save                   = array();
                $save['id']             = $user_id;
                $save['us_deleted']     = '1';
                $save['action_by']      = $this->auth->get_current_admin('id');
                $save['updated_date']   = date('Y-m-d H:i:s');
                $delete_user[]          = $save;    
            }
            if(!empty($delete_user)){
                $this->User_model->save_user($delete_user);
                /*Log creation*/
                $student_count                  = (sizeof($delete_user)>1)?sizeof($delete_user).' students':'a student';
                $user_data                      = array();
                $user_data['user_id']           = $this->__loggedInUser['id'];
                $user_data['username']          = $this->__loggedInUser['us_name'];
                $user_data['useremail']          = $this->__loggedInUser['us_email'];
                $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
                $message_template               = array();
                $message_template['username']   = $this->__loggedInUser['us_name'];
                $message_template['count']      = $student_count;
                $triggered_activity             = 'student_deleted_bulk';
                log_activity($triggered_activity, $user_data, $message_template);
            }
        }
        $data             = array();
        $data['users']    = $this->User_model->users(array(
                                                    'role_id' => '2', 
                                                    'user_ids' => $user_ids,
                                                    'select' => 'id, us_name, us_phone, us_email, us_image, us_role_id, us_institute_id, us_branch, us_branch_code, us_institute_code, us_status, us_degree, us_deleted, us_email_verified'
                                                ));
        $email_content = array();
        foreach($data['users'] as $user)
        {
            $new_email_param = array();
            if($user['us_email_verified'] == '1')
            {
                $new_email_param['email']       = $user['us_email'];
                $new_email_param['contents']    = array(
                                                        'user_name' => $user['us_name']
                                                        ,'site_url_login' => site_url('login')
                                                    );
                array_push($email_content,$new_email_param);
               
            }
        }
        $this->process_bulk_mail($email_content,'student_deleted');
        echo json_encode($data);
    }
    
    function change_status()
    {
        $response               = array();
        $response['error']      = false;
        $user_id                = $this->input->post('user_id');
        $user                   = $this->User_model->user(array(
                                                        'id' => $user_id,
                                                        'select' => 'users.id, us_deleted, us_status, us_name,us_email,us_email_verified,us_phone'
                                                    ));
        $save                   = array();
        $save['id']             = $user_id;
        $save['action_by']      = $this->auth->get_current_admin('id');
        $save['updated_date']   = date('Y-m-d H:i:s');
        $save['us_status']      = '1';
        
        $response['message']    = lang('activated');
        
        if($user['us_status'] == 1)
        {        
            $save['us_status']      = '0';
            $response['message']    = lang('deactivated');
            $this->remove_user(array($user_id));
        }

        //case if record is not deleted
        if($user['us_deleted'] == 0)
        {
            if($user['us_status'] == 1){
                if($user['us_email_verified'] == 1){
                    $template               = $this->ofabeemailer->template(array('email_code' => 'student_deactivated'));
                    $param_admin                    = array();
                    $param_admin['to']              = array($user['us_email']);
                    $param_admin['subject']         = $template['em_subject'];
                    // $param_admin['body']            = 'Dear '.$user['us_name'].',<br /><br />Your profile has been deactivated by admin and now your login access to the portal is blocked.<br/>For more details please contact admin.';
                    $contents               = array(
                                            'username' => $user['us_name']                                            
                                            ,'site_url_login' => site_url('login')
                                            );
                    $param_admin['body']      = $this->ofabeemailer->process_mail_content($contents, $template['em_message']);
                    $send_admin = $this->ofabeemailer->send_mail($param_admin);
                }                
            }
            else
            {                
                if($user['us_email_verified'] == 1){
                    $param_admin                    = array();
                    $param_admin['to']              = array($user['us_email']);

                    switch($user['us_status']){
                        case 0:
                            $template               = $this->ofabeemailer->template(array('email_code' => 'student_activated'));
                            $param_admin['subject'] = $template['em_subject'];
                            $contents               = array(
                                                        'username' => $user['us_name']                                            
                                                        ,'site_url_login' => site_url('login')
                                                        );
                            $param_admin['body']          = $this->ofabeemailer->process_mail_content($contents, $template['em_message']);
                            
                            $this->load->library('Notifier');
                            $this->notifier->push(
                                array(
                                    'action_code' => 'student_account_activated',
                                    'individual' => true,
                                    'push_to' => array($user['id'])
                                )
                            );
                        break;
                        case 2:
                            $template               = $this->ofabeemailer->template(array('email_code' => 'student_approved'));
                            $param_admin['subject'] = $template['em_subject'];
                            $contents               = array(
                                                        'username' => $user['us_name']                                            
                                                        ,'site_url_login' => site_url('login')
                                                        );
                            $param_admin['body']    = $this->ofabeemailer->process_mail_content($contents, $template['em_message']);
                            
                            $this->load->library('Notifier');
                            $this->notifier->push(
                                array(
                                    'action_code' => 'student_account_approved',
                                    'individual' => true,
                                    'push_to' => array($user['id'])
                                )
                            );

                        break;
                        case 3:
                            $template               = $this->ofabeemailer->template(array('email_code' => 'student_activated'));
                            $param_admin['subject'] = $template['em_subject'];
                            $contents               = array(
                                                        'username' => $user['us_name']                                            
                                                        ,'site_url_login' => site_url('login')
                                                        );
                            $param_admin['body']    = $this->ofabeemailer->process_mail_content($contents, $template['em_message']);
                        break;
                    }
                    $send_admin = $this->ofabeemailer->send_mail($param_admin);
                }                
                
            }
        }
        else
        {
            unset($save['updated_date']);
            unset($save['action_by']);
        }
        
        if(!$this->User_model->save($save))
        {
            $response['error']   = true;
            $response['message'] = lang('error_change_status');
        }
        else
        {
            /*Log creation*/
            $user_data                      = array();
            $user_data['user_id']           = $this->__loggedInUser['id'];
            $user_data['username']          = $this->__loggedInUser['us_name'];
            $user_data['useremail']          = $this->__loggedInUser['us_email'];
            $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
            $message_template               = array();
            $message_template['username']   = $this->__loggedInUser['us_name'];
            $message_template['student']    = $user['us_name'].'-'.$user['us_phone'];
            if($save['us_status'] == '0')
            {
                $triggered_activity         = 'student_deactivated';
            } 
            else
            {
                if($user['us_status'] == 2)
                {
                    $triggered_activity     = 'student_account_approved'; 
                } else {
                    $triggered_activity     = 'student_activated'; 
                }
                 
            }
            log_activity($triggered_activity, $user_data, $message_template);
            $response['user']   = $this->User_model->user(array(
                                                        'id' => $user_id,
                                                        'select' => 'users.id, us_name, us_phone, us_email, us_image, us_role_id, us_institute_id, us_branch, us_branch_code, us_institute_code, us_status, us_degree, us_deleted, us_email_verified'                                                    
                                                    ));
        }
       
        echo json_encode($response);
    }
    
    function restore()
    {
        $response               = array();
        $response['error']      = false;
        $user_id                = $this->input->post('user_id');
        $user                   = $this->User_model->user(array('id' => $user_id));

        $save                   = array();
        $save['id']             = $user_id;
        $save['action_by']      = $this->auth->get_current_admin('id');
        $save['updated_date']   = date('Y-m-d H:i:s');
        $save['us_deleted']     = '0';
        $save['us_status']      = ($user['us_status'] == '2')?$user['us_status']:'0';
        
        $response['message']    = lang('restore_user_success');
        
        if(!$this->User_model->save($save))
        {
            $response['error']   = true;
            $response['message'] = lang('restore_user_failed');
        }
        else
        {
            $response['user']   = $this->User_model->user(array(
                                                        'id' => $user_id,
                                                        'select' => 'users.id, us_name, us_phone, us_email, us_image, us_role_id, us_institute_id, us_branch, us_branch_code, us_institute_code, us_status, us_degree, us_deleted, us_email_verified'                                                    
                                                    ));
            /*Log creation*/
            $user_data                      = array();
            $user_data['user_id']           = $this->__loggedInUser['id'];
            $user_data['username']          = $this->__loggedInUser['us_name'];
            $user_data['useremail']          = $this->__loggedInUser['us_email'];
            $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
            $user_data['phone_number']      = $this->__loggedInUser['us_phone'];
            $message_template               = array();
            $message_template['username']   = $this->__loggedInUser['us_name'];
            $message_template['student']    = $response['user']['us_name'].'-'.$response['user']['us_phone'];
            $triggered_activity             = 'student_restored';
            log_activity($triggered_activity, $user_data, $message_template);
        }
    
        echo json_encode($response);        
    }
    
    function delete()
    {
        $response               = array();
        $response['error']      = false;
        $user_id                = $this->input->post('user_id');
        $user                   = $this->User_model->user(array('id' => $user_id));
        if( !$user )
        {
            $response['error'] = true;
            $response['message'] = lang('user_not_found');
            echo json_encode($response);exit;
        }
        $save                   = array();
        $save['id']             = $user_id;
        $save['action_by']      = $this->auth->get_current_admin('id');        
        $save['updated_date']   = date('Y-m-d H:i:s');
        $save['us_deleted']     = '1';
        
        $response['message']    = lang('user_delete_success');
        
        if(!$this->User_model->save($save))
        {
            $response['error']   = true;
            $response['message'] = lang('delete_user_failed');
        }
        else
        {
            $response['user']   = $this->User_model->user(array(
                'id' => $user_id,
                'select' => 'users.id, us_name, us_phone, us_email, us_image, us_role_id, us_institute_id, us_branch, us_branch_code, us_institute_code, us_status, us_degree, us_deleted, us_email_verified'                                                    
            ));
            // echo "<pre>";print_r($response['user']);exit;
            if($response['user']['us_email_verified'] == 1){
                $template               = $this->ofabeemailer->template(array('email_code' => 'student_deleted'));
                $param_admin            = array();
                $param_admin['to']      = array($response['user']['us_email']);
                $param_admin['subject'] = $template['em_subject'];
                // $param_admin['body']            = 'Dear '.$user['us_name'].',<br /><br />Your profile has been deactivated by admin and now your login access to the portal is blocked.<br/>For more details please contact admin.';
                $contents               = array(
                                        'username' => $response['user']['us_name']
                                        );
                $param_admin['body']    = $this->ofabeemailer->process_mail_content($contents, $template['em_message']);
                $send_admin             = $this->ofabeemailer->send_mail($param_admin);
            }
            /*Log creation*/
            $user_data                      = array();
            $user_data['user_id']           = $this->__loggedInUser['id'];
            $user_data['username']          = $this->__loggedInUser['us_name'];
            $user_data['useremail']          = $this->__loggedInUser['us_email'];
            $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
            $user_data['phone_number']      = $this->__loggedInUser['us_phone'];
            $message_template               = array();
            $message_template['username']   = $this->__loggedInUser['us_name'];
            $message_template['student']    = $response['user']['us_name'].'-'.$response['user']['us_phone'];
            $triggered_activity             = 'student_deleted';
            log_activity($triggered_activity, $user_data, $message_template);
  
        }
       
        echo json_encode($response);        
    }
    
    function reset_password()
    {
        $response               = array();
        $response['error']      = 'false';
        $response['message']    = lang('user_password_reset_success');
      
        $user_id = $this->input->post('user_id');
        $user    = $this->User_model->user(array('id' => $user_id, 'select' => 'users.id, us_name, us_email'));
        if(!$user)
        {
            $response['error']      = 'false';
            $response['message']    = lang('user_not_found');
            echo json_encode($response);        
            exit;
        }
        
        $save                       = array();
        $save['id']                 = $user['id'];
        $password                   = $this->generate_string(8);
        $save['us_password']        = sha1($password);
        $save['us_reset_password']  = '1';
        $save['updated_date']       = date('Y-m-d H:i:s');
        $this->User_model->save($save);
                
        $body = 'Hi '.$user['us_name'].'<br />Your password has been reset by admin. Please use the below credentials to login into the webiste<br />';
        $body .= 'Email : <b>'.$user['us_email'].'</b><br />';
        $body .= 'Password : <b>'.$password.'</b><br />';
        $body .= 'Url : <a href="'.site_url('login').'">'.site_url('login').'</b>';
        echo json_encode($response);        
        $this->ofabeemailer->send_mail(array('to' => array($user['us_email']), 'subject' => lang('password_reset'), 'body' => $body));
    }
    
    private function generate_string($length)
    {
        $seed = str_split('abcdefghijklmnopqrstuvwxyz'.'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.'0123456789!@#$%^&*()'); 
        shuffle($seed); 
        $rand = '';
        foreach (array_rand($seed, $length) as $k)
        {
            $rand .= $seed[$k];
        }
        return $rand;
    }
    
    function language()
    {
        $response               = array();
        $response['language']   = array();
        $response['language']   = get_instance()->lang->language;
        echo json_encode($response);
    }

    /*
    * Function used to get the field values in auto suggetion list according to the field id
    * Created by :Neehu KP
    * Created at : 05/01/2017
    */
    function get_fileds_value(){

    	$response   			= array();
        $field_id   			= $this->input->post('field_id');
        $keyword    			= $this->input->post('keyword');

        $check_autosugsestion	= $this->User_model->check_autosuggestion_status($field_id);
        if($check_autosugsestion['pf_auto_suggestion'] == 1){
        	$response['field_values']  = $this->User_model->get_field_suggetion_values(array('field_id' => $field_id, 'keyword' => $keyword ));
        	echo json_encode($response);
        }

        
    }

    public function create_user()
    {
        $response               = array();
        $response['error']      = false;
        $response['message']    = lang('student_added_success');
        $admin                  = $this->auth->get_current_user_session('admin');
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('', '</br>');
        $this->form_validation->set_rules('student_name', 'lang:add_user_name', 'required');
        $this->form_validation->set_rules('student_email', 'lang:add_user_email', 'valid_email|callback_email_check[0]');
        $this->form_validation->set_rules('phone_number', 'Phone', 'required|callback_phone_number_check[0]');
        $this->form_validation->set_rules('student_password', 'lang:add_user_password', 'required|min_length[6]');
        $this->form_validation->set_rules('student_institute', 'lang:student_institute', 'required');
        //$this->form_validation->set_rules('student_branch', 'Student Branch', 'required');
        if ($this->form_validation->run() == FALSE)
        {
            $response['error']   = true;
            $response['message'] = validation_errors();
        }
        else
        {
            $save                   = array();
            $save['id']             = false;
            $save['us_name']        = $this->input->post('student_name');
            $save['us_status']      = '1';
            $save['us_email']       = $this->input->post('student_email');
            
            $password               = $this->input->post('student_password');
            $save['us_password']    = sha1($password);
            $save['us_institute_id']= $this->input->post('student_institute');
            $save['us_branch']      = '1';//$this->input->post('student_branch');

            $objects            = array();
            $objects['key']     = 'institute_'.$save['us_institute_id'];
            $callback           = 'institute';
            $institute_details  = $this->memcache->get($objects, $callback, array('id' =>$save['us_institute_id']));

            $objects                = array();
            $objects['key']         = 'branch_'.$save['us_branch'];
            $callback               = 'branch';
            $branch_details         = $this->memcache->get($objects, $callback, array('id' => $save['us_branch']));
            
            $save['us_institute_code']  = $institute_details['ib_institute_code'];
            $save['us_branch_code']     = $branch_details['branch_code'];
            
            $save['us_phone']           = $this->input->post('phone_number');
            $save['us_role_id']         = '2';
            $save['action_id']          = '1'; 
            $save['action_by']          = $this->auth->get_current_admin('id');
            $save['us_account_id']      = config_item('id');
            $save['updated_date']       = date('Y-m-d H:i:s');
            
            $response['id']         = $this->User_model->save($save);

            /*Log creation*/
            $user_data                      = array();
            $user_data['user_id']           = $this->__loggedInUser['id'];
            $user_data['username']          = $this->__loggedInUser['us_name'];
            $user_data['useremail']          = $this->__loggedInUser['us_email'];
            $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
            $user_data['phone_number']      = $this->__loggedInUser['us_phone'];
            $message_template               = array();
            $message_template['username']   = $this->__loggedInUser['us_name'];
            $message_template['student']    = $save['us_name'].'-'.$save['us_phone'];
            $triggered_activity             = 'student_created';
            log_activity($triggered_activity, $user_data, $message_template);

            //$response['save']       = $save;
            // $this->process_img(array('user_id'=>$response['id']));
            
            $image_upload_param = array();
            $has_s3             = $this->settings->setting('has_s3');
            if( $has_s3['as_superadmin_value'] && $has_s3['as_siteadmin_value'] )
            {
                $actual_path                    = user_upload_path().'temp_'.$this->__loggedInUser['id'].'/';
                $image_upload_param['image_to'] = FCPATH.$actual_path;            
            }      
            else
            {
                $actual_path                    = user_upload_path();
                $image_upload_param['image_to'] = FCPATH.$actual_path;            
            }  
            if(!file_exists($image_upload_param['image_to']))
            {
                mkdir($image_upload_param['image_to'], 0777, true);
            }        
    
            $this->__student_images = array();
            $student_image          = $response['id'].'.jpg';
            //this is to upload student images to s3 , if s3 is enabled
            $this->__student_images[$actual_path.$student_image] = user_upload_path().$student_image;
            $image_upload_param['user_id']  = $response['id'];
            $this->create_profile_image( $image_upload_param );
            
            if( $has_s3['as_superadmin_value'] && $has_s3['as_siteadmin_value'] )
            {
                $s3_param                   = array();
                $s3_param['source']         = $image_upload_param['image_to'];
                $s3_param['files']          = $this->__student_images;
                $this->send_student_image_to_s3($s3_param);                    
            }

            $image_save             = array();
            $image_save['id']       = $response['id'];
            $image_save['us_image'] = $student_image."?v=".rand(10,1000) ;
            $this->User_model->save($image_save);


            $send_mail              = $this->input->post('send_mail');
            $mail_param             = array();
            if( $send_mail == 1 && $save['us_email'] != '')
            {  
                $email_token        = md5(openssl_random_pseudo_bytes(64));
                $to_email		    = $this->input->post('username');
            
                //Save token for verification 
                $token                      = array();
                $token['et_user_id']        = $response['id'];   
                $token['et_change_status']  = '0';
                $token['et_user_email']     = $save['us_email'];
                $token['et_account_id']     = config_item('id');
                $token['et_token']          = $email_token;
                $token['et_status']         = '1';
                $this->User_model->save_token($token);

                $template               = $this->ofabeemailer->template(array('email_code' => 'student_welcome_mail'));
                $param                  = array();
                $param['to'] 	        = array($save['us_email']);
                $param['subject']       = $template['em_subject'];
                $contents               = array(
                                                'user_name' => $save['us_name']
                                                ,'site_name' => config_item('site_name') 
                                                ,'email_id' => $save['us_email']
                                                ,'phone_no' => $save['us_phone']
                                                ,'password' => $password
                                                ,'verification_link' => site_url('register/verify/'.$email_token)
                                                ,'site_url_login' => site_url('login')
                                            );
                $param['body']      = $this->ofabeemailer->process_mail_content($contents, $template['em_message']);
                $this->ofabeemailer->send_mail($param);
            }
        }
        echo json_encode($response);exit;
    }

    public function email_check($email, $id)
    {
        if($email != "") {
            $email_available = $this->User_model->user(array('email'=>$email));
            if (!empty($email_available) ) {
                $this->form_validation->set_message('email_check', 'Email Id <b>'.$email.'</b> is already used ');
                return FALSE;
            } else {
                return TRUE;
            }
        } else {
            return TRUE;
        }
    }

    function phone_number_check($phone_number, $id)
    {
        if($phone_number != "") {
            $phone_number_available = $this->User_model->user(array('us_phone'=>$phone_number));
            if (!empty($phone_number_available) ) {
                $this->form_validation->set_message('phone_number_check', 'Phone <b>'.$phone_number.'</b> is already in use ');
                return FALSE;
            } else {
                return TRUE;
            }
        } else {
            return TRUE;
        }
    }

    public function edit($id = false){
        if(!$id)
        {
            redirect(admin_url('user'));
        }
        //echo '<pre>';print_r($_POST);echo '<pre>';
        $user                   = $this->User_model->user(array(
                                                                'id'=>$id,
                                                                'select' => 'users.id, roles.rl_name, us_name, us_role_id, us_email, us_image, us_about, us_phone, us_degree'
                                                            ));
        // echo '<pre>';print_r($user);die();
        if(!$user)
        {
            redirect(admin_url('user'));
        }
        
        // $this->load->model('Category_model');
        $data                   = array();
        $breadcrumb             = array();
        $breadcrumb[]           = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]           = array( 'label' => lang('manage_users'), 'link' => admin_url('user'), '' => 'active', 'icon' => '' );
        $breadcrumb[]           = array( 'label' => $user['us_name'], 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']     = $breadcrumb;
        
            
        $data['faculty']        = $user;
        $language_error         = '';
        $language_ids           = array();

        $this->load->library('form_validation');
        $this->form_validation->set_rules('us_name', 'lang:add_user_name', 'required');
        //$this->form_validation->set_rules('us_email', 'lang:add_user_email', 'required|valid_email');
        $this->form_validation->set_rules('us_phone', 'lang:phone_profile', 'required');
        $this->form_validation->set_rules('us_about', 'lang:about_profile', 'required');
        
        
        if ($this->form_validation->run() == FALSE)
        {
            $data['error'] = validation_errors();
            $this->load->view($this->config->item('admin_folder').'/user_form', $data);
        }
        else
        {
            $save                       = array();
            $save['id']                 = $id;
            $save['us_name']            = $this->input->post('us_name');
            //$save['us_email']           = $this->input->post('us_email');
            $save['us_phone']           = $this->input->post('us_phone');
            $save['us_about']           = $this->input->post('us_about');
            $save['action_id']          = $this->actions['update']; 
            $save['action_by']          = $this->auth->get_current_admin('id');
            $save['us_account_id']      = $this->config->item('id');
            $save['updated_date']       = date('Y-m-d H:i:s');
            $check_email                = $this->User_model->check_email(array('email'=>$save['us_email']));
	        $template    = 'success';
        	$message     = lang('student_updated_success');
        	$this->User_model->save($save);
            
            $this->session->set_flashdata($template, $message);            
            redirect(admin_url('user/edit/'.$id));
        }
    }

    function upload_user_image()
    {
        $response               = array();
        $response['error']      = 'false';
        $response['message']    = lang('faculty_image_saved');
        $faculty_id             = $this->input->post('id');
        $directory              = user_upload_path();
        if(!file_exists($directory)){
            mkdir($directory, 0777, true);
        }
        $config                     = array();
        $config['upload_path']      =  $directory;
        $config['allowed_types']    = 'gif|jpg|png|jpeg';
        $config['encrypt_name']     = true;
        $this->load->library('upload');
        $this->upload->initialize($config);
        $upload = $this->upload->do_upload('file');
        if($upload)
        {
            $uploaded_data = $this->upload->data();
            $save                   = array();
            $save['id']             = $faculty_id;
            $save['us_image']       = $uploaded_data['raw_name'].'.jpg'."?v=".rand(10,1000);
            $save['action_by']      = $this->auth->get_current_admin('id');
            $save['action_id']      = $this->actions['update'];
            $save['updated_date']   = date('Y-m-d H:i:s');
            $this->User_model->save($save);
            $new_file               = $this->crop_image($uploaded_data);

            $has_s3     = $this->settings->setting('has_s3');
            if( $has_s3['as_superadmin_value'] && $has_s3['as_siteadmin_value'] )
            {
                uploadToS3(user_upload_path().$new_file, user_upload_path().$new_file);
            }
            $response['user_image']      = user_path().$new_file;
        }
        else
        {
            $response['error']      = 'true';
            $response['message']    = 'Error in updating faculty image';            
        }
        echo json_encode($response);
    }

    //end written by Alex

    function invalidate_subscription($param = array()){
        //Invalidate cache
        $user_id    = isset($param['user_id'])?$param['user_id']:false;
        $course_id  = isset($param['course_id'])?$param['course_id']:false;
        if($user_id && $course_id){
            $this->memcache->delete('enrolled_'.$user_id);
            $this->memcache->delete('subscription_'.$course_id.'_'.$user_id);
            $this->memcache->delete('enrolled_item_ids_'.$user_id);
        }
        if($user_id){
            $this->memcache->delete('mobile_enrolled_'.$user_id);
            $this->memcache->delete('enrolled_'.$user_id);
            $this->memcache->delete('enrolled_item_ids_'.$user_id);
        }
    }

    /*
    purpose     : messaging to specific user
    params      : none
    usage-in    : Batches(Admin),Users(Admin)
    edited      : kiran(12/08)
    */
    function send_message()
    {
    
        $subject            = $this->input->post('send_user_subject');
        $message            = base64_decode($this->input->post('send_user_message'));
        $user_ids           = $this->input->post('user_ids');
        $user_ids           = json_decode($user_ids);
        $user_mail_ids      = array();
                  
        $message_param                      = array();
        $message_param['user_ids']          = $user_ids;
        $message_param['select']            = 'users.id as user_id, users.us_email';
        $message_param['us_email_verified'] = '1';
        $users                              = $this->User_model->users($message_param);
        if(!empty($users))
        {
            $system_message                 = array();
            $random_message_id              = rand(1000, 9999);
            $date_time                      = date(DateTime::ISO8601);
            foreach($users as $user)
            {
                $system_message[]           = array(
                    "messageId"             => $random_message_id,
                    "senderId"              => $this->__loggedInUser['id'],
                    "senderName"            => $this->__loggedInUser['us_name'],
                    "senderImage"           => user_path().$this->__loggedInUser['us_image'],
                    "receiverId"            => $user['user_id'],
                    "message"               => $message,
                    "datetime"              => $date_time
                );
                $user_email_ids[] = $user['us_email'];
            }
            //sending notification
            if(!empty($system_message))
            {
                $this->load->library('JWT');
                $payload                     = array();
                $payload['id']               = $this->__loggedInUser['id'];
                $payload['email_id']         = $this->__loggedInUser['us_email'];
                $payload['register_number']  = '';
                $token                       = $this->jwt->encode($payload, config_item('jwt_token')); 
                $response['notified']        = send_notification_to_mongo($system_message, $token);
            }
            //End
            
            $param                                  = array();
            $param['subject'] 	                    = $subject;
            $param['body'] 		                    = $message;
            $param['to']                            = $user_email_ids;
            $param['force_recepient']               = true;
            $send                                   = $this->ofabeemailer->send_mail($param);
            if($send)
            {
                $response['success']                = true;
                $response['message']                = 'Message sent successfully';
            }
            else
            {
                $response['success']                = false;
                $response['message']                = 'Message failed to sent';
            }
        }
        echo json_encode($response);
    }

    //common function to send mail
    private function send_mail($message_params)
    {

        $email_code     = isset($message_params['email_code']) ? $message_params['email_code'] : false;
        $email_ids      = empty($message_params['email_ids']) ? false : $message_params['email_ids'];
        $contents       = isset($message_params['contents']) ? $message_params['contents'] : false;

        if ($email_code != false && $email_ids != false) {

            $template           = $this->ofabeemailer->template(array('email_code' => $email_code));
            $param['to']        = $email_ids;
            $param['subject']   = $template['em_subject'];
            $param['strictly_to_recepient']         = true;
            $param['body']      = $this->ofabeemailer->process_mail_content($contents, $template['em_message']);
            $result             = $this->ofabeemailer->send_mail($param);

            if ($result) {

                return true;
            } else {
                return false;
            }

        }

    }

    function restore_bulk()
    {
        $user_ids           = json_decode($this->input->post('users'));
        $data               = array();
        $restore_users      = array();
        if(!empty($user_ids))
        {
            $data['users']      = $this->User_model->users(array(
                'role_id' => '2', 
                'user_ids' => $user_ids,
                'select' => 'id, us_status'
            ));

            foreach($data['users'] as $user)
            {
                $save                   = array();
                $save['id']             = $user['id'];
                $save['us_status']      = ($user['us_status'] == '2')?$user['us_status']:'0';
                $save['us_deleted']     = '0';
                $save['action_by']      = $this->auth->get_current_admin('id');
                $save['updated_date']   = date('Y-m-d H:i:s');
                $restore_users[]        = $save;
            }   
            if(!empty($restore_users)){
                $this->User_model->save_user($restore_users);
            }
        }
        echo json_encode($data);
    }

    private function process_img($param)
    {
        $user_id        = isset($param['user_id'])?$param['user_id']:0;
        $image_to_cp    = ($user_id%11).'.jpg';
        $image_from     = FCPATH.badge_upload_path().$image_to_cp;
        $image_to       = FCPATH.user_upload_path().$user_id.'.jpg';
        if(!file_exists(user_upload_path()))
        {
            mkdir(user_upload_path(), 0777, true);
        }
        
        if(copy($image_from,$image_to))
        {
            $I_data                 = array();
            $I_data['id']           = $user_id;
            $I_data['us_image']     = $user_id.'.jpg'."?v=".rand(10,1000);
            $this->User_model->save($I_data);
            return true;
        }
        else
        {
            return false;
        }
    }

    private function create_profile_image($param)
    {
        $user_id        = isset($param['user_id'])?$param['user_id']:0;
        $image_to_cp    = ($user_id%11).'.jpg';
        $image_from     = FCPATH.badge_upload_path().$image_to_cp;
        $image_to       = $param['image_to'].$user_id.'.jpg';
        if(file_exists($image_from))
        {
            return copy($image_from,$image_to);
        }
        return false;
    }
    private function send_system_notification($params){

        
        $curlHandle         = curl_init(site_url()."cron_job/send_notification");
        $defaultOptions     = array (
                                CURLOPT_POST => 1,
                                CURLOPT_POSTFIELDS => json_encode($params),
                                CURLOPT_RETURNTRANSFER => false ,
                                CURLOPT_TIMEOUT_MS => 1000,
                             );
        curl_setopt_array($curlHandle , $defaultOptions);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, FALSE);     
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2); 
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array(
            'request-token: '.sha1(config_item('acct_domain').config_item('id')),
        ));
        $result = curl_exec($curlHandle);
        curl_close($curlHandle);

    }
    public function remove_user($user_ids)
    {
        $filter_param           = array();
        $filter_param['update'] = true;
        $filter_param['ids']    = $user_ids;
        
        $user_params            = array('us_token' => '');
        $this->User_model->save_userdata($user_params, $filter_param);
        foreach($user_ids as $user_id)
        {
            $this->memcache->delete('userdetails_'.$user_id);
        }
        return true;
    }
    // $this->remove_user($user_ids);
}
?>
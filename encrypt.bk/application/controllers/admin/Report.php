<?php
class Report extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Kolkata');
        $this->__role_query_filter = array();
        $redirect   = $this->auth->is_logged_in(false, false);
        $this->__admin_index    = 'admin';
        $this->__loggedInUser   = $this->auth->get_current_user_session('admin');
        
        if (!$redirect)
        {
            $redirect   = true;
            $teacher    = $this->auth->is_logged_in(false, false, 'teacher');
            if($teacher)
            {
                $redirect = false;
                $this->__admin_index = 'teacher';
                $teacher = $this->auth->get_current_user_session('teacher');
                $this->__role_query_filter['teacher_id'] = $teacher['id'];                
                $this->__loggedInUser   = $this->auth->get_current_user_session('teacher');
            }
            $content_editor    = $this->auth->is_logged_in(false, false, 'content_editor');
            if($content_editor)
            {
                $redirect = false;
                $this->__admin_index    = 'content_editor';
                $content_editor         = $this->auth->get_current_user_session('content_editor');
                $this->__role_query_filter['editor_id'] = $content_editor['id'];                
                $this->__loggedInUser   = $this->auth->get_current_user_session('content_editor');
            }
            if($redirect)
            {
                if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
                {    
                    echo json_encode(array('success'=>0,'message'=>'Session timeout.<br/>Login again.'));exit;
                }
                redirect('login');
            }
        }
        else
        {
            if(isset($this->__loggedInUser['us_role_id']) && $this->__loggedInUser['us_role_id'] == 8)
            {
                $this->__role_query_filter['institute_id'] = $this->__loggedInUser['us_institute_id'];                                
            }
        }
        $this->actions = $this->config->item('actions');
        $this->load->model(array('Course_model','User_model','Freepreview_model'));
        $this->lang->load('page');
        $this->__lecture_type_array = array(  '1' => 'video',
                                              '2' => 'document',
                                              '3' => 'quiz',
                                              '4' => 'youtube',
                                              '5' => 'text',
                                              '6' => 'wikipedia',
                                              '7' => 'live',
                                              '8' => 'descriptive_test',
                                              '9' => 'recorded_videos',
                                              '10' => 'scorm',
                                              '11' => 'cisco_recorded_videos',
                                              '12' => 'audio',
                                              '13' => 'survey',
                                              '14' => 'certificate'
                                       );
        $this->__lecture_type_keys_array  = array();
        foreach ($this->__lecture_type_array as $id => $type) {
            $this->__lecture_type_keys_array[$type] = $id;
        }
        $this->privilege        = array('view' => 1, 'add' => 2, 'edit' => 3, 'delete' => 4);
        $this->__limit          = 100;
        $this->report_privilege = $this->accesspermission->get_permission(array('role_id' => $this->__loggedInUser['role_id'], 'module' => 'report'));
        $this->event_privilege            = $this->accesspermission->get_permission(array('role_id' => $this->__loggedInUser['role_id'],'module' => 'event'));
    }

    // function free_preview($keyword = false)
    // {

    //     $data                                   = array();
    //     $breadcrumb                             = array();
    //     $breadcrumb[]                           = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
    //     $breadcrumb[]                           = array( 'label' => 'Report', 'link' => admin_url('report/course'), 'active' => '', 'icon' => '' );
    //     $breadcrumb[]                           = array( 'label' => 'Free Preview Report', 'link' => admin_url('free_preview'), 'active' => 'active', 'icon' => '' );
    //     $data['breadcrumb']                     = $breadcrumb;
    //     $data['title']                          = 'Free Preview Management';
    //     $data['keyword']                        = $keyword;
    //     $preview_course_param                   = array();
    //     $preview_course_param['select']         = 'course_basics.id,course_basics.cb_title,course_basics.cb_preview_time,(SELECT COUNT(cpt_user_id) FROM `course_preview_time` WHERE `cpt_course_id` = course_basics.id) as user_count';
    //     $preview_course_param['direction']      = 'ASC';
    //     $preview_course_param['active']         = '1';
    //     $preview_course_param['deleted']        = '0';
    //     $preview_courses                        = $this->Freepreview_model->get_courses($preview_course_param);
    //     $new_preview_courses                    = array();
    //     $data['preview']                        = $preview_courses;
    //     $data['limit']                          = '5';
        
        
    // }
    
    function send_message_group_bulk()
    {
        $subject                    = $this->input->post('send_user_subject');
        $message                    = base64_decode($this->input->post('send_user_message'));
        $names                      = $this->input->post('send_group_names');
        $user_mails                 = json_decode($names);
        $group_ids                  = json_decode($this->input->post('send_group_id'));
        $emailarr                   = array();
        if (!empty($group_ids)) {
            foreach ($group_ids as $group_id) {
                $group_user = $this->Group_model->group_email(array('group_id' => $group_id));
                foreach ($group_user as $key => $value) {
                    array_push($emailarr, $value['us_email']);
                }
            }
        }
        $param['from'] = $this->config->item('site_email');
        $param['to'] = $emailarr;
        $param['subject'] = $subject;
        $param['body'] = $message;
        $send = $this->ofabeemailer->send_mail($param);
    }
    function get_preview_users_by_course()
    {
        $param                  = array();
        $param['select']        = 'users.us_name,users.us_email,users.id,course_preview_time.updated_date,course_preview_time.cpt_course_time';
        $param['course_id']     = $this->input->post('course_id');
        $data['preview_user']   = $this->Freepreview_model->get_users($param);   
        echo json_encode($data);
    }

    // free preview Start
    function free_preview($keyword = false)
    {
        $data                                   = array();
        $breadcrumb                             = array();
        $keyword                                = (!empty($_GET['keyword']))?str_replace('-',' ',trim($_GET['keyword'])):'';
        
        $breadcrumb[]                           = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]                           = array( 'label' => 'Report', 'link' => admin_url('report/course'), 'active' => '', 'icon' => '' );
        $breadcrumb[]                           = array( 'label' => 'Free Preview Report', 'link' => admin_url('free_preview'), 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']                     = $breadcrumb;
        
        $data['title']                          = 'Free Preview Management';
        $data['keyword']                        = $keyword;
        $data['previews']                       = array();
        
        $preview_course_param                   = array();
        $preview_course_param['keyword']        = $keyword;
        $preview_course_param['select']         = 'course_basics.id,course_basics.cb_title,course_basics.cb_preview_time,(SELECT COUNT(cpt_user_id) FROM `course_preview_time` WHERE `cpt_course_id` = course_basics.id) as user_count';
        $preview_course_param['direction']      = 'ASC';
        $preview_course_param['active']         = '1';
        $preview_course_param['deleted']        = '0';
        $preview_course_param['count']          = true;
        
        $data['total_courses']                  = $this->Freepreview_model->get_courses($preview_course_param);
        unset($preview_course_param['count']);
        
        $preview_course_param['limit']          = 50;
        $preview_course_param['offset']         = 0;
        $data['show_load_button']               = false;
        $preview_courses                        = $this->Freepreview_model->get_courses($preview_course_param);
        if($data['total_courses'] > $preview_course_param['limit'])
        {
            $data['show_load_button']   = true;            
        }
        $data['limit']                      = $preview_course_param['limit'];
        $data['previews']                   = $preview_courses;
        $this->load->view($this->config->item('admin_folder').'/freepreview', $data);         
    }
    
    function free_preview_ajax($keyword = false)
    {
        $data                           = array();
        $data['show_load_button']       = false;           
        $limit                          = $this->input->post('limit');
        $offset                         = $this->input->post('offset');
        $page                           = $offset;
        if($page===NULL||$page<=0)
        {
            $page         = 1;
        }
        $page                                   = ($page - 1)* $limit;
        
        $preview_course_param                   = array();
        $preview_course_param['keyword']        = trim($this->input->post('course_keyword'));
        $preview_course_param['select']         = 'course_basics.id,course_basics.cb_title,course_basics.cb_preview_time,(SELECT COUNT(cpt_user_id) FROM `course_preview_time` WHERE `cpt_course_id` = course_basics.id) as user_count';
        $preview_course_param['direction']      = 'ASC';
        $preview_course_param['active']         = '1';
        $preview_course_param['deleted']        = '0';
        $preview_course_param['count']          = true;
        
        $data['total_courses']                  = $this->Freepreview_model->get_courses($preview_course_param);
        unset($preview_course_param['count']);
        
        $preview_course_param['limit']          = $this->input->post('limit');
        $preview_course_param['offset']         = $page;
        if($data['total_courses'] > ($limit*$offset))
        {
            $data['show_load_button']  = true;
        }
        $data['preview']                        = $this->Freepreview_model->get_courses($preview_course_param);
        $data['limit']                          = $limit;
        echo json_encode($data);
    }

    function export_course_preview( $key = false )
    {
        
        $data                   = array();
        $new_preview_courses    = array();
        $preview_courses        = array();
        $param                  = array();

        if( $key )
        {
            $decoded            = base64_decode($key);
            $decoded_param      = json_decode($decoded,true);

            $param['direction'] = 'ASC';
            $param['active']    = '1';
            $param['deleted']   = '0';
            $param['keyword']   = (!empty($decoded_param['keyword']))?$decoded_param['keyword']:false;
            $param['id']        = ($decoded_param['id'] != '0')?$decoded_param['id']:'0';
            
            $preview_courses    = $this->Freepreview_model->get_courses($param);
           
            if(!empty($preview_courses))
            {
                foreach ( $preview_courses as $key => $courses )
                {
                    $category   = $this->Freepreview_model->get_category(array('category_id' => $courses['cb_category']));
                    $users      = $this->Freepreview_model->get_users(array('course_id' => $courses['id'], 'deleted' => '0', 'status' => '1'));
                   
                    if(count($users)>0)
                    {
                        $new_preview_courses[$courses['id']]                    = $courses;
                        $new_preview_courses[$courses['id']]['users']           = $users;
                        $new_preview_courses[$courses['id']]['category_name']   = $category['ct_name'];
                    }
                }
            }
            $data['previews'] = $new_preview_courses;
            $this->load->view($this->config->item('admin_folder').'/export_prepreview', $data);
        }else{
            redirect(admin_url());
        }
    }


    
    function index()
    {
        redirect(admin_url('coursebuilder/report'));
    }
    
    function index_old()
    {
        $data                       = array();    
        $breadcrumb                 = array();
        $breadcrumb[]               = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]               = array( 'label' => lang('report'), 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']         = $breadcrumb;    
        $course_param               = $this->__role_query_filter;
        $course_param['direction']  = 'DESC'; 
        $course_param['not_deleted'] = '1';
        $course_param['status']     = '1';
        $data['courses']            = $this->Course_model->courses($course_param);
        $data['current_course']     = '';
        $data['current_course_id']  = '';
        foreach ($data['courses'] as $key => $val) {
            //if ($val['id'] === $lecture['cl_course_id']) {
                $data['current_course']     = $val['cb_title'];
                $data['current_course_id']  = $val['id'];
            //}
        }
         $data['current_lecture']     = '';
        $data['tests']      = $this->Course_model->get_selected_assesments($data['current_course_id']);
        foreach ($data['tests'] as $key => $val) {
            //if ($val['id'] === $lecture['id']) {
                $data['current_lecture']     = $val['cl_lecture_name'];
            //}
        }
        $this->load->view($this->config->item('admin_folder').'/view_report',$data);
    }
    
    function excel_report()
    {
        $data                       = array();    
        $breadcrumb                 = array();
        $breadcrumb[]               = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]               = array( 'label' => lang('report'), 'link' => admin_url('coursebuilder/report'), 'active' => 'active', 'icon' => '' );
        $breadcrumb[]               = array( 'label' => 'Excel Report', 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']         = $breadcrumb;    
        $course_param               = $this->__role_query_filter;
        $course_param['direction']  = 'DESC';
        $data['courses']            = $this->Course_model->courses($course_param);
        $this->load->view($this->config->item('admin_folder').'/excel_report',$data);
    }
    function course_report()
    {
        $data                   = array();
        $data['site_name']      = $this->config->item('site_name');
        $data['courses']        = $this->Course_model->courses(array('status'=> '1', 'not_deleted'=> '1', 'direction'=> 'ASC'));
        $data['live_courses']   = $this->Course_model->courses(array('status'=> '1', 'not_deleted'=> '1', 'count'=> '1'));
        $data['live_users']     = $this->Course_model->courses(array('status'=> '1', 'not_deleted'=> '1', 'role_id'=> '2', 'count'=> '1'));
        
        foreach($data['courses'] as $key=>$value){
            $data['courses'][$key]['enrolled_users']             = $this->Course_model->enrolled(array('course_id' => $value['id'], 'count' => '1'));
            $data['courses'][$key]['wishlist_users']             = $this->Course_model->course_wishlist(array('course_id' => $value['id'], 'count' => '1'));
        }    
        
        $this->load->view($this->config->item('admin_folder').'/course_report', $data);
        //echo "<pre>";print_r($data);
    
    }
    function course_details()
    {
        $GLOBALS['course_id'] = '';
        $data                   = array();
        $data['courses_list']                = $this->Course_model->courses(array('status'=> '1', 'not_deleted'=> '1', 'direction'=> 'ASC'));
        $data['live_courses']   = $this->Course_model->courses(array('status'=> '1', 'not_deleted'=> '1', 'count'=> true));
        $data['live_users']     = $this->Course_model->courses(array('status'=> '1', 'not_deleted'=> '1', 'role_id'=> '2', 'count'=> true));
        $data['courses']        = array();
        foreach($data['courses_list'] as $key=>$value){
            $GLOBALS['course_id'] = $value['id'];
            $enrolled_details                                           = $this->Course_model->enrolled(array('course_id' => $value['id']));
            $data['courses'][$value['id']]['enrolled_users']            = sizeof($enrolled_details);
            $data['courses'][$value['id']]['wishlist_users']            = $this->Course_model->course_wishlist(array('course_id' => $value['id'], 'count' => true));
            $data['courses'][$value['id']]['sections']                  = $this->Course_model->sections(array('course_id' => $value['id'], 'count' => true, 'status' => 1));
            $data['courses'][$value['id']]['lectures']                  = $this->Course_model->lectures(array('course_id' => $value['id'], 'count' => true, 'status' => 1));
            $data['courses'][$value['id']]['assesments']                = $this->Course_model->lectures(array('course_id' => $value['id'], 'count' => true, 'status' => 1, 'lecture_type' => '3', 'not_deleted' => true ));
            
            
            $data['courses'][$value['id']]['enrolled_details']     = array();
            
            foreach($enrolled_details as $key=>$user)
            {
                $user['percentage']                 = $this->Course_model->calculate_lecture_log(array('user_id'=>$user['cs_user_id'], 'course_id'=>$GLOBALS['course_id']));
                $user['user_attempted_assessment']  = $this->User_model->assessment_attempt_excel_report(array('user_id' => $user['cs_user_id']));
                
                $user_assessment_report = array();
                foreach ($user['user_attempted_assessment'] as $key => $assessment) {
                    
                    $user_assessment_report[$assessment['aa_assessment_id']]     = $this->User_model->assessment_report(array('user_id' => $user['cs_user_id'], 'assessment_id' => $assessment['aa_assessment_id'], 'assessment_attempt_id' => $assessment['attempted_id']));
                    $user['user_attempted_assessment'][$key]['obtained_mark'] = 0;
                    $user['user_attempted_assessment'][$key]['actual_mark'] = 0;
                    foreach ($user_assessment_report[$assessment['aa_assessment_id']] as $questions) {
                        $user['user_attempted_assessment'][$key]['actual_mark']     += $questions['q_positive_mark'];
                        $user['user_attempted_assessment'][$key]['obtained_mark']   += $questions['ar_mark'];
                    }
                }
                
                $data['courses'][$value['id']]['enrolled_details'][] = $user;
            }
            
            //echo "<pre>";print_r($data);die;
            
            //if(!empty($enrolled_details[$key]))
           // {//echo "<pre>";print_r($enrolled_details[$key]);die;
                /*foreach($enrolled_details as $key=>$user)
                {
                    //echo $user['cs_user_id'];
                    $user['percentage']                 = $this->Course_model->calculate_lecture_log(array('user_id'=>$user['cs_user_id'], 'course_id'=>$course_id));
                    $user['user_attempted_assessment']  = $this->User_model->assessment_attempt(array('user_id' => $user['cs_user_id']));
                    
                    $user['user_assessment_report'] = array();
                    foreach ($user['user_attempted_assessment'] as $key => $assessment) {
                        $user['user_assessment_report'][$assessment['aa_assessment_id']]     = $this->User_model->assessment_report(array('user_id' => $user['cs_user_id'], 'assessment_id' => $assessment['aa_assessment_id'], 'assessment_attempt_id' => $assessment['attempted_id']));
                    
                        
                        $user['user_attempted_assessment'][$key]['obtained_mark'] = 0;
                        $user['user_attempted_assessment'][$key]['actual_mark'] = 0;
                        
                        foreach ($user['user_assessment_report'][$assessment['aa_assessment_id']] as $questions) {
                            $user['user_attempted_assessment'][$key]['actual_mark']     += $questions['q_positive_mark'];
                            $user['user_attempted_assessment'][$key]['obtained_mark']   += $questions['ar_mark'];
                        }
                    }
                    
                    $data['courses'][$key]['enrolled_details'][]         = $user;
                    
                }*/
            //}
        }    
        $this->load->view($this->config->item('admin_folder').'/course_details_report', $data);
        //echo "<pre>";print_r($data);
    }
    
    function assignment()
    {
        if(in_array(1, $this->report_privilege)) 
        {
            $response                       = array();
            
            $breadcrumb                     = array();
            $breadcrumb[]                   = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
            $breadcrumb[]                   = array( 'label' => lang('report'), 'link' => admin_url('report/assignment'), 'active' => 'active', 'icon' => '' );
            $breadcrumb[]                   = array( 'label' => 'Assignment Report', 'link' => '', 'active' => 'active', 'icon' => '' );
            $response['breadcrumb']         = $breadcrumb; 

            $course_param                   = $this->__role_query_filter;
            $course_param['direction']      = 'id'; 
            $course_param['not_deleted']    = '1';
            $course_param['select']         = 'course_basics.id, course_basics.cb_title, course_basics.cb_code';
            $course_param['tutor_id']       = ($this->__loggedInUser['rl_full_course']!='1')?$this->__loggedInUser['id']:false;
            $response['courses']            = $this->Course_model->courses_new($course_param);
            
            $objects                        = array();
            $objects['key']                 = 'institutes';
            $callback                       = 'institutes';
            $institutes                     = $this->memcache->get($objects, $callback, array()); 
            $response['institutes']         = $institutes;

            $grade_object                   = array();
            $grade_object['key']            = 'grade_scale';
            $callback                       = 'grade_scale';
            $grade_objects                  = $this->memcache->get($grade_object, $callback, array()); 
            $response['grades']             = $grade_objects;

            $user_institute                 = (isset($this->__role_query_filter['institute_id']))?$this->__role_query_filter['institute_id']:'0';
            $institute_id                   = 0;
            
            $response['course_id']      = $this->input->get('course_id');
            $response['assignment_id']  = $this->input->get('assignment');
            $response['institute_id']   = $this->input->get('institute_id');
            $response['batch_id']       = $this->input->get('batch_id');
            $response['filter_by']      = base64_decode($this->input->get('filter_by'));


            if( $user_institute > 0 )
            {
                $institute_id  = $user_institute;
            }
            else
            {
                $institute_id  = ($response['institute_id'])?$response['institute_id']:0;                
            }

            if( $institute_id != 0 )
            {
                $this->load->model('Group_model');
                $param                  = array();
                $param['institute_id']  = $institute_id;
                $param['select']        = 'id, CONCAT(gp_institute_code," - ",gp_year," - ",gp_name) as batch_name';
                $param['not_deleted']   = true;
                $response['batches']    = $this->Group_model->groups($param);
            }

            $response['role_manager']   = $this->__role_query_filter;


            if($response['course_id'])
            {
                $assignment_param                  = array();
                $assignment_param['course_id']     = $response['course_id'];
                $assignment_param['lecture_type']  = '8';
                $assignment_param['not_deleted']   = true;
                $assignment_param['select']        = 'course_lectures.id, course_lectures.cl_lecture_name'; 
                $response['assignments']           = $this->Course_model->lectures($assignment_param);
            }

            $this->load->view($this->config->item('admin_folder').'/assignment_report_advanced', $response);
        }
        else 
        {
            redirect(admin_url()); exit;
        }
    }
    function assignment_report()
    {
        $user_institute = (isset($this->__role_query_filter['institute_id']))?$this->__role_query_filter['institute_id']:'0';
        $course_id      = $this->input->get('course');
        $lecture_id     = $this->input->get('assignment');
        $lecture_id     = (($lecture_id!='all') || ($lecture_id!='0'))?$lecture_id:false;
        if( !$course_id || !$lecture_id )
        {
            redirect($this->config->item('admin_folder').'/report/assignment');
        }
        $keyword        = $this->input->get('keyword');
        $institute_id   = $this->input->get('institute_id');
        if( $user_institute > 0 )
        {
            $institute_id  = $user_institute;
        } 
        $batch_id       = $this->input->get('batch_id');
        $filter         = $this->input->get('filter');
        $tutor          = $this->input->get('tutor');
        $sort_by        = $this->input->get('sort_by');
        $offset         = $this->input->get('offset');
        
        $data                           = array();
        //Read institutes form memcached.
        $objects                        = array();
        $objects['key']                 = 'institutes';
        $callback                       = 'institutes';
        $institutes                     = $this->memcache->get($objects, $callback, array()); 
        $data['institutes']             = $institutes;
        $data['user']                   = $this->__loggedInUser;
        $data['role_manager']           = $this->__role_query_filter;
        $data['limit']                  = $this->__limit;
        $offset                         = isset($_GET['offset'])?$_GET['offset']:0;
        $data['batches']                = array();
        $grade_object                   = array();
        $grade_object['key']            = 'grade_scale';
        $callback                       = 'grade_scale';
        $grade_objects                  = $this->memcache->get($grade_object, $callback, array()); 
        $data['grade']                  = $grade_objects;
        $attempts_param                 = array();
        $attempts_param['lecture_id']   = $lecture_id;
        $attempts_param['course_id']    = $course_id;
        $attempts_param['institute_id'] = $institute_id;
        $attempts_param['batch_id']     = $batch_id;
        $attempts_param['filter']       = base64_decode($filter);
        $attempts_param['keyword']      = $keyword;
        $attempts_param['tutor']        = $tutor;
        $attempts_param['sort_by']      = $sort_by;
        $attempts_param['json']         = false;
        $attempts_param['limit']        = $this->__limit;
        $data['selected_course']        = '';
        $data['selected_assignment']    = '';
        if($attempts_param['lecture_id'])
        {
            $assignment                     = $this->Course_model->lecture(array('id'=>$lecture_id, 'not_deleted' => true,'select'=>'cl_lecture_name'));
            $data['selected_assignment']    = $assignment['cl_lecture_name'];
            $course                        = $this->Course_model->course(array('id'=>$course_id,'select'=>'cb_title','not_deleted' => true));
            $data['selected_course']       = $course['cb_title'];
        }
        
        //processing pagination
        $page             = $offset;
        if($page===NULL||$page<=0)
        {
            $page         = 1;
        }
        $page             = ($page - 1)* $this->__limit;
        //end
        $attempts_param['offset']        = $page;
        $attempts_param['count']         = true;
        $total_attempts                  = $this->assignment_attendees($attempts_param);
        $data['total_attempts']          = $total_attempts;
        unset($attempts_param['count']);
        
        if($institute_id)
        {
            if($institute_id!=='all')
            {
                $institute_id_arr               = array();
                foreach($institutes as $institute)
                {
                    $institute_id_arr[] = $institute['id'];
                }
                if(!in_array($institute_id, $institute_id_arr))
                {
                    $this->session->set_flashdata('error', 'No students found.');
                    $redirect_params   =  array();
                    $redirect_params[] = 'course_id='.$course_id;
                    $redirect_params[] = 'quiz_id='.$lecture_id;
                    $redirect_params[] = 'institute_id='.$institute_id;
                    $redirect_params[] = 'batch_id='.$batch_id;
                    $redirect_params[] = 'filter_by='.base64_encode($filter);
                    redirect(admin_url('report/assignment').'?'.implode('&', $redirect_params));
                }
                $this->load->model('Group_model');
                $param                  = array();
                $param['institute_id']  = $institute_id;
                $param['select']        = 'id, CONCAT(gp_institute_code," - ",gp_year," - ",gp_name) as batch_name';
                $param['not_deleted']   = true;
                $data['batches']        = $this->Group_model->groups($param);
            }
        }

        $attempts                       = $this->assignment_attendees($attempts_param);
        if(count($attempts)!=0)
        {
            $data['assignments'][]['attempts'] = $attempts;
            /*Log creation*/
            $user_data                          = array();
            $user_data['user_id']               = $this->__loggedInUser['id'];
            $user_data['username']              = $this->__loggedInUser['us_name'];
            $user_data['useremail']              = $this->__loggedInUser['us_email'];
            $user_data['user_type']             = $this->__loggedInUser['us_role_id'];
            $message_template                   = array();
            $message_template['username']       = $this->__loggedInUser['us_name'];
            $message_template['report']         = 'assignment';
            $triggered_activity                 = 'report_generated';
            log_activity($triggered_activity, $user_data, $message_template);
        } 
        else
        {
            $this->session->set_flashdata('error', 'No students found.');
            $redirect_params   =  array();
            $redirect_params[] = 'course_id='.$course_id;
            $redirect_params[] = 'quiz_id='.$lecture_id;
            $redirect_params[] = 'institute_id='.$institute_id;
            $redirect_params[] = 'batch_id='.$batch_id;
            $redirect_params[] = 'filter_by='.base64_encode($filter);
            redirect(admin_url('report/assignment').'?'.implode('&', $redirect_params));
        }
        $data['access_permission']      = $this->report_privilege;
        $data['tutors']      = $this->Course_model->get_course_tutors(array('course_id' => $attempts_param['course_id']));
        //echo '<pre>'; print_r($data); die;
        $this->load->view(config_item('admin_folder').'/report_assignment_list', $data);
    }

    function log_activity(){

        $this->__loggedInUser           = $this->auth->get_current_user_session('admin');
        $this->load->model(array('Report_model','Role_model'));

        
        $keyword      = isset($_GET['keyword'])?$_GET['keyword']:false;
        $usertype     = isset($_GET['usertype'])?$_GET['usertype']:false;
        $userid       = false;
        if($this->__loggedInUser['role_id'] == '3')
        {
            $usertype = '3';
            $userid   = $this->__loggedInUser['id'];
        }
        $count        = isset($_GET['count'])?$_GET['count']:false;
        $log_date_start = isset($_GET['log_date_start'])?$_GET['log_date_start']:false;
        $log_date_end = isset($_GET['log_date_end'])?$_GET['log_date_end']:false;
        $offset       = isset($_GET['offset'])?$_GET['offset']:1;
        $page                           = $offset;
        if($page===NULL||$page<=0)
        {
            $page                       = 1;
        }
        $page                           = ($page - 1)* $this->__limit;
        //end

        //$limit        = isset($_GET['limit'])?$_GET['limit']:false;
        $data                               = array();
        $log_param                          = array();
        $log_param['keyword']               = $keyword;
        $log_param['log_date_start']        = $log_date_start;
        $log_param['log_date_end']          = $log_date_end;
        $log_param['usertype']              = $usertype;
        $log_param['userid']                = $userid;
        $log_param['count']                 = true;
        $log_param['offset']                = $page;
        $log_param['limit']                 = $this->__limit;
        $log_param['json']                  = false;
        $data['limit']                      = $this->__limit;
        $data['total_activities']           = $this->log_activities($log_param);
        unset($log_param['count']);

        
        $data['activities']                 = $this->log_activities($log_param);
        $role_param                         = array();
        $role_param['select']               = 'id,rl_name';
        $role_param['deleted']              = '0';
        $data['roles']                      = $this->Role_model->roles($role_param);
        $this->load->view(config_item('admin_folder').'/log_activity_list', $data);
    }

    function log_activities($param = array())
    {
        
        $select       = 'id, la_user_name,la_user_email,la_usertype,la_message,DATE_FORMAT(la_created_date,"%d-%m-%Y %H:%i:%s") as la_created_date';
        $keyword      = isset($param['keyword'])?$param['keyword']:false;
        $usertype     = isset($param['usertype'])?$param['usertype']:false;
        $userid       = isset($param['userid'])?$param['userid']:false;
        $log_date_start = isset($param['log_date_start'])?$param['log_date_start']:false;
        $log_date_end = isset($param['log_date_end'])?$param['log_date_end']:false;
        $count        = isset($param['count'])?$param['count']:false;
        $offset       = isset($param['offset'])?$param['offset']:false;
        $limit        = isset($param['limit'])?$param['limit']:false;
        $json         = isset($param['json'])?$param['json']:true;
        
        $this->load->model('Report_model');
        $log_param                      = array();
        $log_param['usertype']          = $usertype;
        $log_param['userid']            = $userid;
        $log_param['keyword']           = $keyword;
        $log_param['select']            = $select;

        
        if($log_date_start){
            $log_param['log_date_start'] = date('Y-m-d', strtotime($log_date_start));
        }
        if($log_date_end){
            $log_param['log_date_end']  = date('Y-m-d', strtotime($log_date_end));
        }
        $log_param['count']             = $count;
        $log_param['offset']            = $offset;
        $log_param['limit']             = $limit;
        
        $return                         = $this->Report_model->log_activities($log_param);

        //echo '<pre>'; print_r($return);die;
        if($json)
        {
            echo json_encode(array('activities' => $return, 'error' => false));
        }
        else
        {
            return $return;        
        }
    }

    function dateformate(){
        $date = $this->input->get('date');
        echo $date;
    }

    function archive()
    {
        $this->load->model('Archive_model');
        $keyword                            = isset($_GET['keyword'])?$_GET['keyword']:false;
        $cs_start_date                      = isset($_GET['cs_start_date'])? $_GET['cs_start_date'].' 00:00:00':false;
        $cs_end_date                        = isset($_GET['cs_end_date'])? $_GET['cs_end_date'].' 23:59:59':false;
        $count                              = isset($_GET['count'])?$_GET['count']:false;
        $offset                             = isset($_GET['offset'])?$_GET['offset']:1;
        $page                               = $offset;
        //
        if($page===NULL||$page<=0)
        {
            $page                           = 1;
        }
        $page                               = ($page - 1)* $this->__limit;
        //end
        $data                               = array();
        $archive_param                      = array();
        $archive_param['keyword']           = $keyword;
        $archive_param['cs_start_date']     = $cs_start_date;
        $archive_param['cs_end_date']       = $cs_end_date;
        $archive_param['count']             = true;
        $archive_param['offset']            = $page;
        $archive_param['limit']             = $this->__limit;
        $archive_param['json']              = false;
        $data['limit']                      = $this->__limit;

        $data['total_archives']             = $this->archive_list($archive_param);
        unset($archive_param['count']);
        $data['archive_list']               = $this->archive_list($archive_param);
        $this->load->view(config_item('admin_folder').'/archive_list', $data);
    }
    
    function archive_list($param = array())
    {
        $this->load->model('Archive_model');
        $select                             = 'id, sa_user_name,sa_user_email,sa_user_register_number,sa_user_phone,sa_course_title,sa_course_code,DATE_FORMAT(sa_cs_startdate,"%d-%m-%Y") as cs_start_date,DATE_FORMAT(sa_cs_enddate,"%d-%m-%Y") as cs_end_date';
        $keyword                            = isset($param['keyword'])?$param['keyword']:false;
        $cs_start_date                      = isset($param['cs_start_date'])?$param['cs_start_date']:false;
        $cs_end_date                        = isset($param['cs_end_date'])?$param['cs_end_date']:false;
        $count                              = isset($param['count'])?$param['count']:false;
        $offset                             = isset($param['offset'])?$param['offset']:false;
        $limit                              = isset($param['limit'])?$param['limit']:false;
        $json                               = isset($param['json'])?$param['json']:true;
        $archive_param                      = array();
        $archive_param['keyword']           = $keyword;
        $archive_param['select']            = $select;
        if($cs_start_date)
        {
            $archive_param['cs_start_date'] = date('Y-m-d', strtotime($cs_start_date));
        }
        if($cs_end_date)
        {
            $archive_param['cs_end_date']   = date('Y-m-d', strtotime($cs_end_date));
        }

        //echo $archive_param['cs_start_date'].' - '.$archive_param['cs_end_date'];die;
        $archive_param['count']             = $count;
        $archive_param['offset']            = $offset;
        $archive_param['limit']             = $limit;
        $return                             = $this->Archive_model->archive_list($archive_param);
        if($json)
        {
            echo json_encode(array('archive_list' => $return, 'error' => false));
        }
        else
        {
            return $return;        
        }
    }

    function get_archive_list($json= true)
    {
        $cs_start_date                  = $this->input->post('cs_start_date');
        $cs_end_date                    = $this->input->post('cs_end_date');
        $offset                         = $this->input->post('offset');
        $keyword                        = $this->input->post('keyword');
        $limit                          = $this->__limit;

        $response                       = array();
        $response['error']              = false;
        $response['message']            = 'Listing archives';
        $archive_param                      = array();
        $archive_param['keyword']           = $keyword;
        $archive_param['cs_start_date']     = $cs_start_date;
        $archive_param['cs_end_date']       = $cs_end_date;
        $archive_param['count']             = true;
        $archive_param['limit']             = $limit;
        $archive_param['json']              = false;

        //processing pagination
        $page                           = $offset;
        if($page===NULL||$page<=0)
        {
            $page                       = 1;
        }
        $page                           = ($page - 1)* $this->__limit;
        //end
        $archive_param['offset']            = $page;
        $response['total_archives']     = $this->archive_list($archive_param);
        unset($archive_param['count']);
        $response['archive_list']       = $this->archive_list($archive_param);

        if($json)
        {
            echo json_encode($response);
        }
        else
        {
            return $response;
        }
        
    }
   
      
    function get_course_assignments($course_id=0, $json = true){
        $response               = array();
        $response['error']      = false;
        $response['message']    = 'Listing course assignments';
        $response['Listing_assignments'] = $this->Course_model->lectures(array('course_id' => $course_id, 'lecture_type' => '8', 'not_deleted' => true, 'select' => 'course_lectures.id, course_lectures.cl_lecture_name, course_lectures.cl_course_id'));
        echo json_encode($response);die(); 
    }
    function get_assignments(){
        $course_id                              = $this->input->post('course_id');
        $response                               = array();
        $response['error']                      = false;
        $response['message']                    = 'Assignments list';
        $assignment_param                       = array();
        $assignment_param['course_id']          = $course_id;
        $assignment_param['lecture_type']       = '8';
        $assignment_param['not_deleted']        = true;
        $assignment_param['select']             = 'course_lectures.id, course_lectures.cl_lecture_name'; 
        $response['results']                    = $this->Course_model->lectures($assignment_param);
        echo json_encode($response);die();
    }
    function log_activity_list($json= true)
    {
        $this->__loggedInUser           = $this->auth->get_current_user_session('admin');
        $usertype                       = $this->input->post('usertype');
        $userid                         = false;
        if($this->__loggedInUser['role_id'] == '3')
        {
            $usertype                   = '3';
            $userid                     = $this->__loggedInUser['id'];
        }
        $offset                         = $this->input->post('offset');
        $keyword                        = $this->input->post('keyword');
        $log_date_start                 = $this->input->post('log_date_start');
        $log_date_end                   = $this->input->post('log_date_end');
        $limit                          = $this->__limit;

        $response                       = array();
        $response['error']              = false;
        $response['message']            = 'Listing log activities';
        $log_param                      = array();
        $log_param['keyword']           = $keyword;
        $log_param['usertype']          = $usertype;
        $log_param['userid']            = $userid;
        $log_param['log_date_start']    = $log_date_start;
        $log_param['log_date_end']      = $log_date_end;
        $log_param['count']             = true;
        $log_param['limit']             = $limit;
        $log_param['json']              = false;

        //processing pagination
        $page                           = $offset;
        if($page===NULL||$page<=0)
        {
            $page                       = 1;
        }
        $page                           = ($page - 1)* $this->__limit;
        //end
        $log_param['offset']            = $page;
        $response['total_activities']   = $this->log_activities($log_param);
        unset($log_param['count']);
        $response['activities']         = $this->log_activities($log_param);

        if($json)
        {
            echo json_encode($response);
        }
        else
        {
            return $response;
        }
        
    }
    function course_assignment($course_id=0,$json= true)
    {
        $user_institute         = (isset($this->__role_query_filter['institute_id']))?$this->__role_query_filter['institute_id']:'0';
        $course_id              = $this->input->post('course_id');
        $assignment_id          = $this->input->post('assignment_id');
        $sort_by                = ($this->input->post('sort_by'))?$this->input->post('sort_by'):false;
        $tutor                  = ($this->input->post('tutor'))?$this->input->post('tutor'):false;
        
        if($user_institute >= 0){
            $institute_id       = $this->input->post('institute_id');
        } else {
            $institute_id       = $user_institute;
        }
        $batch_id               = $this->input->post('batch_id');
        $filter_by              = $this->input->post('filter');
        $filter                 = base64_decode($filter_by);
        $keyword                = $this->input->post('keyword');
        $limit                  = $this->__limit;
        $data['limit']          = $limit;
        $offset                 = $this->input->post('offset');
        $response               = array();
        $response['error']      = false;
        $response['message']    = 'Listing course assignments';
        $course = $this->Course_model->course(array('id' => $course_id));
        if(!$course)
        {
            if($json)
            {
                $response['error']      = true;
                $response['message']    = 'Requested Course details not found.';
                echo json_encode($response);exit;
            }
            else
            {
                return $response;exit;
            }
        }
        
        $assignment_param                       = array();
        $assignment_param['course_id']          = $course_id;
        $assignment_param['lecture_type']       = '8';
        $assignment_param['not_deleted']        = true;
        $assignment_param['select']             = 'course_lectures.id, course_lectures.cl_lecture_name, course_lectures.cl_course_id';
    
        $response['batches']        = array();
        if($institute_id != '')
        {
            $this->load->model('Group_model');
            $param                  = array();
            $param['institute_id']  = $institute_id;
            $param['select']        = 'id, CONCAT(gp_institute_code," - ",gp_year," - ",gp_name) as batch_name';
            $param['not_deleted']   = true;
            $response['batches']    = $this->Group_model->groups($param);
        }
        $response['tutors']         = $this->Course_model->get_course_tutors(array('course_id' => $course_id));
        
        $this->load->model(array('Report_model'));
        $response['assignments'] = array();
        
        $lecture_assignments = $this->Course_model->lectures($assignment_param);
        $attempts_param                  = array();
        $attempts_param['lecture_id']    = $assignment_id;
        $attempts_param['course_id']     = $course_id;
        $attempts_param['institute_id']  = $institute_id;
        $attempts_param['batch_id']      = $batch_id;
        $attempts_param['filter']        = $filter;
        $attempts_param['keyword']       = $keyword;
        $attempts_param['sort_by']       = $sort_by;
        $attempts_param['tutor']         = $tutor;
        
        $attempts_param['json']          = false;
        $attempts_param['count']         = true;
        
        $response['total_attempts']      = $this->assignment_attendees($attempts_param);
        unset($attempts_param['count']);
       
        $attempts_param['limit']         = $this->__limit;
        
        //processing pagination
        $page                            = $offset;
        if($page===NULL||$page<=0)
        {
            $page                        = 1;
        }
        $page                            = ($page - 1)* $this->__limit;
        //end
        $attempts_param['offset']        = $page;
        if(!empty($lecture_assignments))
        {
                if(empty($response['assignments']))
                {
                    $lecture_assignment['attempts'] = $this->assignment_attendees($attempts_param);
                }
                $response['assignments'][] = $lecture_assignment;
        }
        //echo '<pre>'; print_r($response);die;
        if($json)
        {
            echo json_encode($response);
        }
        else
        {
            return $response;
            //echo '<pre>'; print_r($response['assignments']);die;
        }
    }
    
    function assignment_attendees($param = array())
    {
        
        $lecture_id   = isset($param['lecture_id'])?$param['lecture_id']:$this->input->post('lecture_id');
        $institute_id = isset($param['institute_id'])?$param['institute_id']:$this->input->post('institute_id');
        $batch_id     = isset($param['batch_id'])?$param['batch_id']:$this->input->post('batch_id');
        $course_id    = isset($param['course_id'])?$param['course_id']:$this->input->post('course_id');
        $filter       = isset($param['filter'])?$param['filter']:$this->input->post('filter');
        $sort_by      = isset($param['sort_by'])?$param['sort_by']:$this->input->post('sort_by');
        $tutor        = isset($param['tutor'])?$param['tutor']:$this->input->post('tutor');
        
        $keyword      = isset($param['keyword'])?$param['keyword']:$this->input->post('keyword');
        $json         = isset($param['json'])?$param['json']:true;
        $count        = isset($param['count'])?$param['count']:false;
        $offset       = isset($param['offset'])?$param['offset']:1;
        $limit        = isset($param['limit'])?$param['limit']:$this->__limit;
        $this->load->model('Report_model');
        $attendees_param                = $this->__role_query_filter;
        $attendees_param['lecture_id']  = $lecture_id;
        $attendees_param['institute_id']= $institute_id;
        $attendees_param['batch_id']    = $batch_id;
        $attendees_param['course_id']   = $course_id;
        $attendees_param['filter']      = $filter;
        $attendees_param['sort_by']     = $sort_by;
        $attendees_param['tutor']       = $tutor;
        $attendees_param['keyword']     = $keyword;
        $attendees_param['offset']      = $offset;
        $attendees_param['limit']       = $limit;
        $attendees_param['count']       = $count;
        $return                         = $this->Report_model->assignment_attendees($attendees_param);
        
        if($json)
        {
            echo json_encode(array('attendees' => $return, 'error' => false));
        }
        else
        {
            return $return;        
        }
    }

    function export_archive($param=false)
    {
        if(!$param)
        {
            redirect(admin_url('report/archive'));
        }
        $param                              = base64_decode($param);
        $param                              = (array)json_decode($param);
        $keyword                            = isset($param['keyword'])?$param['keyword']:false;
        $cs_start_date                      = isset($param['cs_start_date'])?$param['cs_start_date']:false;
        $cs_end_date                        = isset($param['cs_end_date'])?$param['cs_end_date']:false;
        $offset                             = false;
        $limit                              = false;
        $this->load->model('Archive_model');

        $archive_param                      = $this->__role_query_filter;
        $archive_param['select']            = 'id, sa_user_name,sa_user_email,sa_user_register_number,sa_user_phone,sa_course_title,sa_course_code,DATE_FORMAT(sa_cs_startdate,"%d-%m-%Y") as cs_start_date,DATE_FORMAT(sa_cs_enddate,"%d-%m-%Y") as cs_end_date,sa_subscription_details';
        $archive_param['keyword']           = $keyword;
        if($cs_start_date)
        {
            $archive_param['cs_start_date'] = date('Y-m-d', strtotime($cs_start_date));
        }
        if($cs_end_date)
        {
            $archive_param['cs_end_date']   = date('Y-m-d', strtotime($cs_end_date));
        }
        $archive_param['keyword']           = $keyword;
        $reports                            = $this->Archive_model->archive_list($archive_param);
        //echo '<pre>'; print_r($reports);die;
        $data                               = array();
        $data['reports']                    = $reports;
        $this->load->view(config_item('admin_folder').'/export_archive', $data);
    }

    function export_assignment($param=false)
    {
        
        if(!$param)
        {
            redirect(admin_url('report/assignment'));
        }
        $param = base64_decode($param);
        $param = (array)json_decode($param);
        
        $user_institute = (isset($this->__role_query_filter['institute_id']))?$this->__role_query_filter['institute_id']:'0';
        $filter         = isset($param['filter'])?$param['filter']:false;
        $keyword        = isset($param['keyword'])?$param['keyword']:false;
        $assignment_id  = isset($param['assignment_id'])?$param['assignment_id']:false;
        $course_id      = isset($param['course_id'])?$param['course_id']:false;
        $batch_id       = isset($param['batch_id'])?$param['batch_id']:false;
        $tutor          = isset($param['tutor'])?$param['tutor']:false;
        $sort_by        = isset($param['sort_by'])?$param['sort_by']:false;
        $this->load->model('Report_model');
        
        if($user_institute > 0)
        {
            $institute_id  = $user_institute;
        } else {
            $institute_id   = isset($param['institute_id'])?$param['institute_id']:false;
        }

        $attendees_param                = $this->__role_query_filter;
        $attendees_param['lecture_id']  = $assignment_id;
        $attendees_param['course_id']   = $course_id;
        $attendees_param['filter']      = $filter;
        $attendees_param['keyword']     = $keyword;
        $attendees_param['institute_id']= $institute_id;
        $attendees_param['batch_id']    = $batch_id;
        $attendees_param['tutor']       = $tutor;
        $attendees_param['sort_by']     = $sort_by;
        $attendees_param['limit']       = false;
        $attendees_param['offset']      = false;
        
        //echo '<pre>';print_r($attendees_param);die;
        $reports    = $this->Report_model->assignment_attendees($attendees_param);

        if(count($reports)==0){
            $this->session->set_flashdata('error', 'No students found.');
            $redirect_params   =  array();
            $redirect_params[] = 'course_id='.$course_id;
            $redirect_params[] = 'quiz_id='.$assignment_id;
            $redirect_params[] = 'institute_id='.$institute_id;
            $redirect_params[] = 'batch_id='.$batch_id;
            $redirect_params[] = 'filter_by='.base64_encode($filter);
            redirect(admin_url('report/assignment').'?'.implode('&', $redirect_params));
        }
        
        $course     = $this->Course_model->course(array('id' => $course_id));
        $lecture    = $this->Course_model->lecture(array('id' => $assignment_id));
        $data                   = array();
        $data['reports']        = $reports;
        $data['course_name']    = $course['cb_title'];
        $data['lecture_name']   = $lecture['cl_lecture_name'];
        $this->load->view(config_item('admin_folder').'/export_assignment', $data);
    }

    function export_assessment($param=false)
    {
        if(!$param)
        {
            redirect(admin_url('report/assessments'));
        }
        $user_institute = (isset($this->__role_query_filter['institute_id']))?$this->__role_query_filter['institute_id']:'0';
        $param          = base64_decode($param);
        $param          = (array)json_decode($param);
        $filter         = isset($param['filter'])?$param['filter']:false;
        $keyword        = isset($param['keyword'])?$param['keyword']:false;
        $assessment_id  = isset($param['assessment_id'])?$param['assessment_id']:false;
        $course_id      = isset($param['course_id'])?$param['course_id']:false;
        $batch_id       = isset($param['batch_id'])?$param['batch_id']:false;
        $tutor          = isset($param['tutor'])?$param['tutor']:false;
        $sort_by        = isset($param['sort_by'])?$param['sort_by']:'id_desc';

        if($user_institute > 0)
        {
            $institute_id  = $user_institute;
        } else {
            $institute_id   = isset($param['institute_id'])?$param['institute_id']:false;
        }

        $this->load->model('Report_model');
        $attendees_param                = $this->__role_query_filter;
        $attendees_param['lecture_id']  = $assessment_id;
        $attendees_param['course_id']   = $course_id;
        $attendees_param['filter']      = $filter;
        $attendees_param['keyword']     = $keyword;
        $attendees_param['institute_id']= $institute_id;
        $attendees_param['batch_id']    = $batch_id;
        $attendees_param['tutor']       = $tutor;
        $attendees_param['sort_by']     = $sort_by;
        $attendees_param['limit']       = false;
        $attendees_param['offset']      = false;
        $reports                        = $this->Report_model->assessment_attendees($attendees_param);
        if(count($reports)==0){
            $this->session->set_flashdata('error', 'No students found.');
            $redirect_params   =  array();
            $redirect_params[] = 'course_id='.$course_id;
            $redirect_params[] = 'quiz_id='.$assessment_id;
            $redirect_params[] = 'institute_id='.$institute_id;
            $redirect_params[] = 'batch_id='.$batch_id;
            $redirect_params[] = 'filter_by='.base64_encode($filter);
            redirect(admin_url('report/assessments').'?'.implode('&', $redirect_params));
        }
        
        
        $lecture                        = $this->Course_model->lecture(array('id'=>$assessment_id, 'not_deleted' => true,'select'=>'cl_lecture_name'));
        $course                         = $this->Course_model->course(array('id'=>$course_id,'select'=>'cb_title','not_deleted' => true));
        $data                           = array();
        $data['assessments']            = $reports;
        $data['lecture']                = $lecture['cl_lecture_name'];
        $data['course']                 = $course['cb_title'];
        $this->load->view(config_item('admin_folder').'/export_assessments', $data);
    }
    
    
    function course()
    {
        if(in_array(1, $this->report_privilege)) {
            $response                       = array();
            $breadcrumb                     = array();
            $breadcrumb[]                   = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
            $breadcrumb[]                   = array( 'label' => lang('report'), 'link' => admin_url('report/course'), 'active' => 'active', 'icon' => '' );
            $breadcrumb[]                   = array( 'label' => 'Grade Report', 'link' => '', 'active' => 'active', 'icon' => '' );
            $response['breadcrumb']         = $breadcrumb;        

            $course_param                   = $this->__role_query_filter;
            $course_param['direction']      = 'id'; 
            $course_param['not_deleted']    = '1';
            $course_param['select']         = 'course_basics.id, course_basics.cb_title, cb_code'; 
            $course_param['tutor_id']  = ($this->__loggedInUser['rl_full_course']!='1')?$this->__loggedInUser['id']:false;       
            $response['courses']            = $this->Course_model->courses_new($course_param);


            $objects        = array();
            $objects['key'] = 'institutes';
            $callback       = 'institutes';
            $institutes     = $this->memcache->get($objects, $callback, array()); 
            $response['institutes']     = $institutes;

            $objects            = array();
            $objects['key']     = 'grade_scale';
            $callback           = 'grade_scale';
            $grade_objects      = $this->memcache->get($objects, $callback, array()); 
            $response['grades'] = $grade_objects;
            


            $user_institute             = (isset($this->__role_query_filter['institute_id']))?$this->__role_query_filter['institute_id']:'0';
            $institute_id               = 0;
            $response['course_id']      = $this->input->get('course_id');
            $response['institute_id']   = $this->input->get('institute_id');
            $response['batch_id']       = $this->input->get('batch_id');
            $response['filter_by']      = base64_decode($this->input->get('filter_by'));
            $response['role_manager']   = $this->__role_query_filter;
            if( $user_institute > 0 )
            {
                $institute_id  = $user_institute;
            }
            else
            {
                $institute_id  = ($response['institute_id'])?$response['institute_id']:0;                
            }

            if( $institute_id != 0 )
            {
                $this->load->model('Group_model');
                $param                  = array();
                $param['institute_id']  = $institute_id;
                $param['select']        = 'id, CONCAT(gp_institute_code," - ",gp_year," - ",gp_name) as batch_name';
                $param['not_deleted']   = true;
                $response['batches']    = $this->Group_model->groups($param);
            }
            $this->load->view($this->config->item('admin_folder').'/course_report_advanced', $response);
        }
        else 
        {
            redirect(admin_url()); exit;
        }
    }

    function grade_report( $course_id = false) 
    {
        
        $response           = array();
        $course_id          = ($this->input->get('course') != null)? $this->input->get('course') : '';
        if (!$course_id) 
        {
            redirect(admin_url('report/course'));
        }
        $this->load->model('Report_model');
        $lecture_types      = array($this->__lecture_type_keys_array['quiz'], $this->__lecture_type_keys_array['descriptive_test']);
        $lectures           = $this->Course_model->lectures(array(
                                                    'course_id' => $course_id, 
                                                    'not_deleted' => true, 
                                                    'lecture_types' => $lecture_types,
                                                    'status' => '1', 
                                                    'select' => 'course_lectures.id, course_lectures.cl_lecture_name'
                                                ));
        
        $institute_id   = $this->input->get('institute_id');
        $batch_id       = $this->input->get('batch_id');
        $filter         = $this->input->get('filter');

        $response['course_id']      = $course_id;
        $response['institute_id']   = $institute_id;
        $response['batch_id']       = $batch_id;
        $response['filter']         = $filter;
        if(empty($lectures)) 
        {
            $this->session->set_flashdata('error', 'No lectures found for the course');
            $redirect_params   =  array();
            $redirect_params[] = 'course_id='.$course_id;
            $redirect_params[] = 'institute_id='.$institute_id;
            $redirect_params[] = 'batch_id='.$batch_id;
            $redirect_params[] = 'filter_by='.($filter);
            redirect(admin_url('report/course').'?'.implode('&', $redirect_params));
        }
        
        $institute_id       = ($this->input->get('institute_id') != null)? $this->input->get('institute_id') : '';
        $batch_id           = ($this->input->get('batch_id') != null)? $this->input->get('batch_id') : '';
        $keyword            = ($this->input->get('keyword') != null)? $this->input->get('keyword') : '';
        $course             = $this->Course_model->course(array('id' => $course_id));
        
        //Read institutes form memcached
        $objects                        = array();
        $objects['key']                 = 'institutes';
        $callback                       = 'institutes';
        $institutes                     = $this->memcache->get($objects, $callback, array()); 
        $response['admin']              = $this->__loggedInUser;
        $response['institutes']         = $institutes;
        //End
        //Read institutes form memcached
        $response['batches']    = array();
        $objects                = array();
        $objects['key']         = 'grade_scale';
        $callback               = 'grade_scale';
        $grade_objects          = $this->memcache->get($objects, $callback, array()); 
        $response['grades']     = $grade_objects;
        //End
        
        $response['load_more']  = false;        
        $response['limit']      = 100;
        $response['course_id']  = $course_id;
        $response['selected_course'] = $course['cb_title'];


        $response['lectures']   = $lectures;
        $param                  = array();
        $filter                 = $this->input->get('filter');
        if($filter != '')
        {
            switch(base64_decode($filter))
            {
                case 'A+':
                case 'A':
                case 'B+':
                case 'B':
                case 'C+':
                case 'C':
                case 'D+':
                case 'D':
                case 'E':
                        $param['grade'] = base64_decode($filter);
                        break;
                default:    
                        $param['filter'] = base64_decode($filter);
                        break;
            }
        } 
        
        $param['course_id']         = $course_id;
        $param['institute_id']      = (isset($this->__role_query_filter['institute_id']))?$this->__role_query_filter['institute_id']:$institute_id;
        $param['batch_id']          = $batch_id;
        $param['keyword']           = trim($keyword);
        $param['count']             = true;
        $users_count                = $this->Course_model->enrolled_users($param);
        unset($param['count']);
        $param['limit']             = 100;
        $param['select']            = 'course_subscription.cs_user_name, course_subscription.id, course_subscription.cs_user_id, course_subscription.cs_percentage, course_subscription.cs_lecture_log, course_subscription.cs_auto_grade, course_subscription.cs_manual_grade';
        $enrolled_users             = $this->Course_model->enrolled_users($param);
        $response['total_subscribers'] = $users_count;
        if($users_count > $param['limit'])
        {
            $response['load_more']  = true;
        }
        if(!empty($enrolled_users)) 
        {
            $response['subscribers'] = $enrolled_users;
            /*Log creation*/
            $user_data                          = array();
            $user_data['user_id']               = $this->__loggedInUser['id'];
            $user_data['username']              = $this->__loggedInUser['us_name'];
            $user_data['useremail']              = $this->__loggedInUser['us_email'];
            $user_data['user_type']             = $this->__loggedInUser['us_role_id'];
            $message_template                   = array();
            $message_template['username']       = $this->__loggedInUser['us_name'];
            $message_template['report']         = 'grade';
            $triggered_activity                 = 'report_generated';
            log_activity($triggered_activity, $user_data, $message_template);
        }
        else
        {
            $this->session->set_flashdata('error', 'No students subscribed to the course');
            $redirect_params   =  array();
            $redirect_params[] = 'course_id='.$course_id;
            $redirect_params[] = 'institute_id='.$institute_id;
            $redirect_params[] = 'batch_id='.$batch_id;
            $redirect_params[] = 'filter_by='.($filter);
            redirect(admin_url('report/course').'?'.implode('&', $redirect_params));
        }
        if(isset($this->__role_query_filter['institute_id']) || $param['institute_id'] != '')
        {
            $this->load->model('Group_model');
            $group_param                  = array();
            $group_param['institute_id']  = isset($this->__role_query_filter['institute_id'])? $this->__role_query_filter['institute_id'] : $param['institute_id'];
            $group_param['select']        = 'id, CONCAT(gp_institute_code," - ",gp_year," - ",gp_name) as batch_name';
            $response['batches']          = $this->Group_model->groups($group_param);
        }
        //$response['test']['course_id']          = $course_id;
        $response['lecture']['cl_course_id']    = $course_id;
        $response['role_manager']               = $this->__role_query_filter;
        $response['access_permission']          = $this->report_privilege;
        //echo "<pre>"; print_r($response['subscribers']); die();
        $this->load->view($this->config->item('admin_folder').'/grade_report_advanced', $response);
    }

    function grade_report_json($course_id = 0) 
    {
        $response           = array();
        $course_id          = $this->input->post('course_id');
        if (!$course_id) 
        {
            echo json_encode(array('error' => true, 'message' => 'Invalid course id'));die;
        }
        $this->load->model('Report_model');
        $lecture_types      = array($this->__lecture_type_keys_array['quiz'], $this->__lecture_type_keys_array['descriptive_test']);
        $lectures           = $this->Course_model->lectures(array(
                                                    'course_id' => $course_id, 
                                                    'not_deleted' => true, 
                                                    'lecture_types' => $lecture_types,
                                                    'status' => '1', 
                                                    'select' => 'course_lectures.id, course_lectures.cl_lecture_name'
                                                ));
        
        if(empty($lectures)) 
        {
            echo json_encode(array('error' => true, 'message' => 'No lectures found for the course'));die;
        }
        $institute_id       = ($this->input->get('institute_id') != null)? $this->input->get('institute_id') : '';
        $batch_id           = ($this->input->get('batch_id') != null)? $this->input->get('batch_id') : '';
        $keyword            = ($this->input->get('keyword') != null)? $this->input->get('keyword') : '';
        $course             = $this->Course_model->course(array('id' => $course_id));
        //Read institutes form memcached
        $objects                        = array();
        $objects['key']                 = 'institutes';
        $callback                       = 'institutes';
        $institutes                     = $this->memcache->get($objects, $callback, array()); 
        $response['admin']              = $this->__loggedInUser;
        $response['institutes']         = $institutes;
        //End
        //Read institutes form memcached
        $response['batches']    = array();
        $objects                = array();
        $objects['key']         = 'grade_scale';
        $callback               = 'grade_scale';
        $grade_objects          = $this->memcache->get($objects, $callback, array()); 
        $response['grades']     = $grade_objects;
        //End

        $response['load_more']  = false;        
        $response['limit']      = 100;
        $response['course_id']  = $course_id;
        $response['selected_course'] = $course['cb_title'];
        $response['lectures']   = $lectures;
        $param                  = array();
        $filter                 = $this->input->get('filter');
        if($_POST)
        {
        $filter                     = $this->input->post('filter');
        $param['institute_id']      = (isset($this->__role_query_filter['institute_id']))?$this->__role_query_filter['institute_id']:$this->input->post('institute_id');
        $param['course_id']         = $this->input->post('course_id');
        $param['batch_id']          = $this->input->post('batch_id');
        $param['keyword']           = $this->input->post('keyword');
        }
        if($filter != '')
        {
            switch(base64_decode($filter))
            {
                case 'A+':
                case 'A':
                case 'B+':
                case 'B':
                case 'C+':
                case 'C':
                case 'D+':
                case 'D':
                case 'E':
                        $param['grade'] = base64_decode($filter);
                        break;
                default:    
                        $param['filter'] = base64_decode($filter);
                        break;
            }
        } 
        
        $param['keyword']           = trim($param['keyword']);
        $param['count']             = true;
        $users_count                = $this->Course_model->enrolled_users($param);
        unset($param['count']);
        $limit              = $this->input->post('limit');
        $offset             = $this->input->post('offset');
        $page               = $offset;
        if($page===NULL||$page<=0)
        {
            $page         = 1;
        }
        $page             = ($page - 1)* $limit;
        $param['limit']             = $limit;
        $param['offset']            = $page;
        $param['select']            = 'course_subscription.cs_user_name, course_subscription.id, course_subscription.cs_user_id, course_subscription.cs_percentage, course_subscription.cs_lecture_log, course_subscription.cs_auto_grade, course_subscription.cs_manual_grade';
        $enrolled_users             = $this->Course_model->enrolled_users($param);
        if($users_count > ($limit*$offset))
        {
            $response['load_more']  = true;
        }
        if($users_count > $param['limit'])
        {
            $response['load_more']  = true;
        }
        $response['total_subscribers'] = $users_count;
        $response['subscribers']       = $enrolled_users;
        
        if(isset($this->__role_query_filter['institute_id']) || $param['institute_id'] != '')
        {
            $this->load->model('Group_model');
            $group_param                  = array();
            $group_param['institute_id']  = isset($this->__role_query_filter['institute_id'])? $this->__role_query_filter['institute_id'] : $param['institute_id'];
            $group_param['not_deleted']   = true;
            $group_param['select']        = 'id, CONCAT(gp_institute_code," - ",gp_year," - ",gp_name) as batch_name';
            $response['batches']          = $this->Group_model->groups($group_param);
        }
        echo json_encode($response);        
    }

    function change_grade()
    {
        $response           = array();
        $grade              = $this->input->post('grade');
        $user               = $this->input->post('user');
        $lecture_id         = $this->input->post('lecture');
        $course_id          = $this->input->post('course');
        $subscription_id    = $this->input->post('subscription');
        $this->load->model(array('Course_model'));

        if($lecture_id != '' && $lecture_id != 0)
        {
            $subscription       = $this->Course_model->subscription_details(array('id'=>$subscription_id));
            $objects            = array();
            $objects['key']     = 'course_'.$course_id;
            $callback           = 'course_details';
            $params             = array('id' => $course_id);
            $course_details     = $this->memcache->get($objects, $callback, $params);
            $lecture_details    = array();
            foreach($course_details['lectures'] as $lecture)
            {
                if($lecture_id == $lecture['id'])
                {
                    $lecture_details = $lecture;
                    break;
                }
            }

            $lecture_log                         = $subscription['cs_lecture_log'];
            $lecture_log                         = json_decode($lecture_log,true);
            $lecture_log[$lecture_id]['grade']   = $grade;

            switch($lecture_details['cl_lecture_type'])
            {
                case 3: // Assessment
                    $param                      = array();
                    $param['aa_user_id']        = $user;
                    $param['aa_course_id']      = $course_id;
                    $param['aa_lecture_id']     = $lecture_id;
                    $param['aa_grade']          = $grade;
                    $this->Course_model->update_assessment_lecture($param);
                break;
                case 8: // Assignment
                    $param                          = array();
                    $param['dtua_user_id']          = $user;
                    $param['dtua_course_id']        = $course_id;
                    $param['dtua_lecture_id']       = $lecture_id;
                    $param['dtua_grade']            = $grade;
                    $this->Course_model->update_assignment_lecture($param);
                break;
            }
            $param                          = array();
            $param['id']                    = $subscription_id;
            $param['cs_lecture_log']        = json_encode($lecture_log);
            $this->Course_model->save_last_played_lecture($param);
        }
        else
        {
            $param                          = array();
            $param['id']                    = $subscription_id;
            $param['cs_manual_grade']       = $grade;
            $this->Course_model->save_last_played_lecture($param);
        }
        $response['success']    = true;
        $response['message']    = 'Grade updation success.';
        $response['grade']      = '-';
        $subscription           = $this->Course_model->subscription_details(array('id'=>$subscription_id));
        if(!$subscription['cs_manual_grade'])
        {
            $response['grade']  = $subscription['cs_auto_grade'];
        }
        else
        {
            $response['grade']  = $subscription['cs_manual_grade'];
        }

        echo json_encode($response);
        
    }
    
    function course_subscribers_json($course_id=0)
    {
        $course_id      = ($course_id)?$course_id: $this->input->post('course_id');
        $user_name      = $this->input->post('user_name');
        $limit          = $this->input->post('limit');
        $offset         = $this->input->post('offset');
        $get_lectures   = $this->input->post('get_lectures');
        $institute_id   = $this->input->post('institute');
        $page           = $offset;
        $filters        = $this->input->post('filters');
        $filters        = (array)json_decode($filters);
        if($page===NULL||$page<=0)
        {
            $page                   = 1;
        }
        $page                       = ($page - 1)* $limit;
        $course                         = $this->Course_model->course(array('id' => $course_id));
        $response                       = array();
        $response['error']              = false;
        $response['show_load_button']   = false;
        if(!$course)
        {
            $response['error']      = true;
            $response['message']    = 'Requested Course details not found.';
            echo json_encode($response);exit;
        }
        $this->load->model('Report_model');
        $response['subscribers']    = array();
        $response['lectures']       = array();
        $lectures                   = $this->Course_model->lectures(array('course_id' => $course_id, 'not_deleted' => true, 'status' => '1', 'select' => 'course_lectures.id, course_lectures.cl_lecture_name'));
        if($get_lectures)
        {        
            $response['lectures']   = $lectures;
        }
        if(!empty($lectures))
        {
            $total_subscribers       = $this->Report_model->enrolled_report(array( 'count' => true,'institute'=>$institute_id,'course_id' => $course_id, 'keyword' => $user_name, 'filters' => $filters, 'total_lectures' => count($lectures)));
            $current_page            = ($page==0)?1:$page;
            if($total_subscribers > ($limit*$current_page) )
            {
                $response['show_load_button']   = true;                
            }
            $subscribers             = $this->Report_model->enrolled_report(array('limit'=>$limit,'offset'=>$page,'institute'=>$institute_id,'course_id' => $course_id, 'keyword' => $user_name, 'filters' => $filters, 'total_lectures' => count($lectures)));
            if(!empty($subscribers))
            {
                foreach ($subscribers as $subscriber)
                {
                    $subscriber['lectures'] = $this->Report_model->lecture_completed_status(array('course_id' => $subscriber['cs_course_id'], 'user_id' => $subscriber['cs_user_id']));
                    $response['subscribers'][] = $subscriber; 
                }
            }
        }
        echo json_encode($response);
    }
    function export_grade_report($param = false)
    {
        if(!$param)
        {
            redirect(admin_url('report/course'));
        }

        $param          = base64_decode($param);
        $param          = (array)json_decode($param);

        $response       = array();
        $course         = array();
        $course_id      = isset($param['course_id'])?$param['course_id']:0;
        if(!$course_id) 
        {
            redirect(admin_url('report/course'));
        }

        $institute_id   = isset($param['institute'])?$param['institute']:0;
        $batch_id       = isset($param['batch'])?$param['batch']:0;
        $keyword        = isset($param['keyword'])?$param['keyword']:'';
        $course         = $this->Course_model->course(array('id' => $course_id));
        
        if(empty($course)) 
        {
            redirect(admin_url('report/course'));
        }
        $filter                 = isset($param['filter'])?$param['filter']:'';
        $response['course_id']          = $course_id;
        $response['selected_course']    = $course['cb_title'];
        if($institute_id!=0)
        {
            $objects        = array();
            $objects['key'] = 'institute_' .$institute_id;
            $callback       = 'institute';
            $params         = array('id' => $institute_id);
            $institution    = $this->memcache->get($objects, $callback, $params);
            $response['selected_institution']    = $institution['ib_name'].' ('.$institution['ib_institute_code'].' )';
        }
        else
        {
            $response['selected_institution']    = 'All Institutes';
        }

        $this->load->model('Report_model');
        $lecture_types   = array();
        $lecture_types[] = $this->__lecture_type_keys_array['quiz'];
        $lecture_types[] = $this->__lecture_type_keys_array['descriptive_test'];
        $lectures        = $this->Course_model->lectures(array(
                                                    'course_id' => $course_id, 
                                                    'not_deleted' => true, 
                                                    'lecture_types' => $lecture_types,
                                                    'status' => '1', 
                                                    'select' => 'course_lectures.id, course_lectures.cl_lecture_name'
                                                ));
        if(empty($lectures)) 
        {
            $this->session->set_flashdata('error', 'No lectures found for the course');
            $redirect_params   =  array();
            $redirect_params[] = 'course_id='.$course_id;
            $redirect_params[] = 'institute_id='.$institute_id;
            $redirect_params[] = 'batch_id='.$batch_id;
            $redirect_params[] = 'filter_by='.($filter);
            redirect(admin_url('report/course').'?'.implode('&', $redirect_params));
        }

        $response['lectures']   = $lectures;

        $search = array();
        if($filter != '')
        {
            switch(base64_decode($filter))
            {
                case 'A+':
                case 'A':
                case 'B+':
                case 'B':
                case 'C+':
                case 'C':
                case 'D+':
                case 'D':
                case 'E':
                        $search['grade'] = base64_decode($filter);
                        break;
                default:    
                        $search['filter'] = base64_decode($filter);
                        break;
            }
        }
        $search['course_id']         = $course_id;
        $search['institute_id']      = $institute_id;
        $search['batch_id']          = $batch_id;
        $search['keyword']           = trim($keyword);
        $search['select']            = 'course_subscription.cs_user_name, course_subscription.id, course_subscription.cs_user_id, course_subscription.cs_percentage, course_subscription.cs_lecture_log, course_subscription.cs_auto_grade, course_subscription.cs_manual_grade';
        $response['subscribers']     = $this->Course_model->enrolled_users($search);
        if(empty($response['subscribers']))
        {
            $this->session->set_flashdata('error', 'No students found');
            $redirect_params   =  array();
            $redirect_params[] = 'course_id='.$course_id;
            $redirect_params[] = 'institute_id='.$institute_id;
            $redirect_params[] = 'batch_id='.$batch_id;
            $redirect_params[] = 'filter_by='.($filter);
            redirect(admin_url('report/course').'?'.implode('&', $redirect_params));
        }
        else
        {
            $this->load->view($this->config->item('admin_folder').'/export_grade_report', $response);
        }
    }
    
    function export_course_report($param=false)
    {
        if(!$param)
        {
            redirect(admin_url('report/course'));
        }
        $param = base64_decode($param);
        $param = (array)json_decode($param);
        // echo '<pre>'; print_r($param);die;
        
        $course_id      = isset($param['course_id'])?$param['course_id']:0;
        $user_name      = isset($param['keyword'])?$param['keyword']:'';
        $filters        = isset($param['filters'])?$param['filters']:array();
        $institute_id   = isset($param['institute'])?$param['institute']:0;
        $course         = $this->Course_model->course(array('id' => $course_id));
        $response       = array();
        if(!$course)
        {
            redirect(admin_url('report/course'));
        }
        $this->load->model('Report_model');
        $response['subscribers']        = array();
        $lectures                       = $this->Course_model->lectures(array('course_id' => $course_id, 'status' => '1', 'not_deleted' => true, 'select' => 'course_lectures.id, course_lectures.cl_lecture_name'));
        $response['lectures']           = $lectures;
        $response['selected_course']    = $course['cb_title'];
        if(!empty($lectures))
        {
            $subscribers                    = $this->Report_model->enrolled_report(array('course_id' => $course_id,'institute'=>$institute_id,'keyword' => $user_name, 'filters' => $filters, 'total_lectures' => count($lectures)));
            if(!empty($subscribers))
            {
                foreach ($subscribers as $subscriber)
                {
                    $subscriber['lectures'] = $this->Report_model->lecture_completed_status(array('course_id' => $subscriber['cs_course_id'], 'user_id' => $subscriber['cs_user_id']));
                    $response['subscribers'][] = $subscriber; 
                }
            }
        }
        //echo '<pre>';print_r($response);die;
        $this->load->view($this->config->item('admin_folder').'/export_course_report', $response);
    }
    function survey_report($param = false)
    {
        $this->load->model('Report_model');
        $param       = json_decode(base64_decode($param), true);
        $response    = $this->Report_model->get_survey_report($param);
        // echo $this->db->last_query();exit;
        // echo "<pre>"; print_r($param); die();
        if(!empty($response))
        {
            $data['reports']        = $this->_group_by_order($response);        
            $data['tutor_report']   = false;
            $data['survey_name']    = $response[0]['s_name'];
            $data['tutor_name']     = $response[0]['s_tutor_name'];
            $data['questions']      = array();
            $data['question_id']    = array();
            // echo "<pre>"; print_r($response); die();
            foreach($response as $report){
                if(!in_array($report['sur_question_id'],$data['question_id'])){
                    $data['questions'][]    = $report['sur_question'];
                    $data['question_id'][]  = $report['sur_question_id'];
                }            
            }
            
            if(isset($data['tutor_name']) && $data['tutor_name'] != "") 
            {
                $data['tutor_report'] = true;
            }
            //  echo "<pre>"; print_r($data); die();
             /*Log creation*/
            $user_data                          = array();
            $user_data['user_id']               = $this->__loggedInUser['id'];
            $user_data['username']              = $this->__loggedInUser['us_name'];
            $user_data['useremail']              = $this->__loggedInUser['us_email'];
            $user_data['user_type']             = $this->__loggedInUser['us_role_id'];
            $message_template                   = array();
            $message_template['username']       = $this->__loggedInUser['us_name'];
            $message_template['report']         = 'tutor performance';
            $triggered_activity                 = 'report_generated';
            log_activity($triggered_activity, $user_data, $message_template);
            $this->load->view($this->config->item('admin_folder').'/export_survey_report', $data);
        
        }
        else
        {
            $this->session->set_flashdata('error', 'No responses for the survey');

            // $redirect_params   =  array();
            // $redirect_params[] = 'survey_id='.$param['survey_id'];
            // $redirect_params[] = 'tutor_id='.$param['tutor_id'];
            // redirect(admin_url('report/tutor_performance_report').'?'.implode('&', $redirect_params));
            if(isset($param['lecture_id'])){
                redirect(admin_url('coursebuilder/lecture/').$param['lecture_id']);
            }else{
                redirect(admin_url('report/tutor_performance_report'));
            }
            
        }
        
    }
    function tutor_performance_report() 
    {
        $this->load->model('Faculty_model');
        $response   = array();
        $breadcrumb                     = array();
        $breadcrumb[]                   = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]                   = array( 'label' => lang('report'), 'link' => admin_url('report/course'), 'active' => 'active', 'icon' => '' );
        $breadcrumb[]                   = array( 'label' => 'Tutor Performance Report', 'link' => '', 'active' => 'active', 'icon' => '' );
        $response['breadcrumb']         = $breadcrumb; 
        $response['tutors']             = $this->Faculty_model->faculties(array(
                                            'not_deleted' => true,
                                            'role_id' => '3',
                                            'order_by' => 'users.us_name',
                                            'direction' => 'ASC',
                                            'select' => 'users.id, users.us_name, users.us_status, rl_name'
                                        ));
        $response['tutor_id']  = $this->input->get('tutor_id');
        $response['survey_id'] = $this->input->get('survey_id');
        if($response['tutor_id'])
        {
            $this->load->model('Survey_model');
            $survey_param                       = array();
            $survey_param['tutor_id']           = $response['tutor_id'];
            $survey_param['select']             = 'id, s_name'; 
            $response['surveys']                = $this->Survey_model->surveys($survey_param);    
        }
        // echo "<pre>"; print_r($response); die();
        $this->load->view($this->config->item('admin_folder').'/tutor_performance_report', $response);
    }
    function get_surveys() 
    {
        $this->load->model('Survey_model');
        $tutor_id                               = $this->input->post('tutor_id');
        $response                               = array();
        $response['error']                      = false;
        $response['message']                    = 'Survey list';
        $survey_param                       = array();
        $survey_param['tutor_id']           = $tutor_id;
        $survey_param['select']             = 'id, s_name'; 
        $response['surveys']                = $this->Survey_model->surveys($survey_param);
        echo json_encode($response);die();
    }
    function _group_by_order($reports = array()) 
    {
        $sorted_array = array();
        foreach($reports as $report) 
        {
            if(!isset($sorted_array[$report['sur_user_name']]))
            {
                $sorted_array[$report['sur_user_name']] = array();
            }
            $sorted_array[$report['sur_user_name']][] = $report['sur_answer'];
        }
        return $sorted_array;
    }
    function language()
    {
        $response = array();
        $response['language'] = array();
        $response['language'] = get_instance()->lang->language;
        echo json_encode($response);
    }
    /* Written by Alex. */
    function strength_report(){
        $this->load->model(array('Report_model'));
        $data                           = array();
        $breadcrumb                     = array();
        $data['limit']                  = 10;
        $breadcrumb[]                   = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]                   = array( 'label' => lang('report'), 'link' => admin_url('report/course'), 'active' => '', 'icon' => '' );
        $breadcrumb[]                   = array( 'label' => 'Strength Report', 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']             = $breadcrumb;
        //$courses                        = $this->Course_model->simple_courses(array('status'=>'1','select'=>'id,cb_title'));
        $course_param                   = $this->__role_query_filter;
        $course_param['direction']      = 'id'; 
        $course_param['not_deleted']    = '1';
        $course_param['status']         = '1';
        $course_param['select']         = 'course_basics.id, course_basics.cb_title';
        $courses                        = $this->Course_model->courses($course_param);
        $data['courses']                = $courses;
        $course_pass                    = array();
        foreach($data['courses'] as $course){
            $course_pass[] = $course['id'];
        }
        $course_pass                    = implode(',', $course_pass);
        $data['students']               = $this->Report_model->course_enrolled_students(array('role_param'=>$this->__loggedInUser,'course_id'=>$course_pass,'offset'=>0,'limit'=>$data['limit']));
        //echo '<pre>';print_r($categories);die;
        $this->load->view(config_item('admin_folder').'/strength_report', $data);
    }
    function strength_report_export($student = false){
        $this->load->model(array('Report_model'));
        $user_id    = base64_decode($student);
        $response   = array();
        if($user_id == 0){
            $response['success']    = 0;
            $response['message']    = 'Invalid input provided.';
            echo json_encode($response);exit;
        }
        $user_details  = $this->Report_model->course_enrolled_students(array('role_param'=>$this->__loggedInUser,'user_id'=>$user_id));
        $category_wise = $this->User_model->topic_wise_progress(array('user_id'=>$user_id));
        $response['student']  = $user_details;
        $response['progress'] = $category_wise;
        $this->load->view(config_item('admin_folder').'/export_srength_report', $response);
    }
    function enroled_students_ajax(){
        $this->load->model(array('Report_model'));
        $course_id              = $this->input->post('course_id');
        $course_id              = $course_id==0?'':$course_id;
        $student_name           = $this->input->post('student_name');
        $limit                  = $this->input->post('limit');
        $offset                 = $this->input->post('offset');
        $page                       = $offset;
        if($page===NULL||$page<=0)
        {
            $page                   = 1;
        }
        $page                       = ($page - 1)* $limit;
        $response               = array();
        $course_param                   = $this->__role_query_filter;
        $course_param['direction']      = 'id'; 
        $course_param['not_deleted']    = '1';
        $course_param['status']         = '1';
        $course_param['select']         = 'course_basics.id, course_basics.cb_title';
        $courses                        = $this->Course_model->courses($course_param);
        $data['courses']                = $courses;
        $course_pass                    = array();
        foreach($data['courses'] as $course){
            $course_pass[] = $course['id'];
        }
        if($course_id != ''&&in_array($course_id, $course_pass)){
            $course_pass                    = $course_id;
        }else{
            $course_pass                    = implode(',', $course_pass);
        }
        $enroled_students       = $this->Report_model->course_enrolled_students(array('role_param'=>$this->__loggedInUser,'course_id'=>$course_pass,'student_name'=>$student_name,'offset'=>$page,'limit'=>$limit));
        $response['success']    = 1;
        $response['students']   = $enroled_students;
        echo json_encode($response);
    }
    function topic_wise_progress(){
        $course_id  = $this->input->post('course_id');
        $user_id    = $this->input->post('user_id');
        $response   = array();
        if($user_id == 0){
            $response['success']    = 0;
            $response['message']    = 'Invalid input provided.';
            echo json_encode($response);exit;
        }
        $category_wise = $this->User_model->topic_wise_progress(array('course_id'=>$course_id,'user_id'=>$user_id));
        $response['success']        = 1;
        $response['progress'] = $category_wise;
        echo json_encode($response);
    }
    function advanced_course_report(){
        $breadcrumb                     = array();
        $data['limit']                  = 10;
        $breadcrumb[]                   = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]                   = array( 'label' => lang('report'), 'link' => admin_url('report'), 'active' => '', 'icon' => '' );
        $breadcrumb[]                   = array( 'label' => 'Course Report', 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']             = $breadcrumb;
        $this->load->view(config_item('admin_folder').'/advanced_course_report',$data);
    }
    function get_batches(){
        $institute_id           = $this->input->post('institute_id');
        $this->load->model('Group_model');
        $param                  = array();
        $param['institute_id']  = $institute_id;
        $param['select']        = 'id, CONCAT(gp_institute_code," - ",gp_year," - ",gp_name) as batch_name';
        $param['not_deleted']   = true;
        $response['batches']    = $this->Group_model->groups($param);
        $response['error']      = false;
        echo json_encode($response);die();
    }
    function assign_faculty(){
        $this->load->model(array('Report_model'));
        $attempt_ids            = json_decode($this->input->post('attempt_ids'), true);
        $faculty_id             = $this->input->post('faculty_id');
        if(!empty($attempt_ids))
        {
            $param              = array();
            $param['dtua_assigned_to']=  $faculty_id;
            $response               = array();
            $response['error']      = false;
            $response['results']    = array();
            foreach($attempt_ids as $attempt_id)
            {
                $param['id']    = $attempt_id;
                $response['results']['attempt_id'][] =$attempt_id;
                $this->Report_model->assign_faculty($param);
                $get_faculty_record = $this->User_model->user(array('select'=>'us_name,us_image','id'=>$param['dtua_assigned_to']));
                $response['results']['faculty_name'] = $get_faculty_record['us_name'];
                $response['results']['faculty_img']  = $get_faculty_record['us_image'];
            }
        }
        
        $response['message']    = 'Facuty assigned successfully';
        echo json_encode($response);
    }
    function assign_assessment_faculty(){
        $this->load->model(array('Report_model'));
        $attempt_ids            = json_decode($this->input->post('attempt_ids'), true);
        $faculty_id             = $this->input->post('faculty_id');
        if(!empty($attempt_ids))
        {
            $param                   = array();
            $param['aa_valuated_by'] =  $faculty_id;
            $response                = array();
            $response['error']       = false;
            $response['results']     = array();
            foreach($attempt_ids as $attempt_id)
            {
                $param['id']    = $attempt_id;
                $response['results']['attempt_id'][] = $attempt_id;
                $this->Report_model->assign_assessment_faculty($param);
                $get_faculty_record = $this->User_model->user(array('select'=>'us_name,us_image','id'=>$param['aa_valuated_by']));
                $response['results']['faculty_name'] = $get_faculty_record['us_name'];
                $response['results']['faculty_img']  = $get_faculty_record['us_image'];
            }
        }
        
        $response['message']    = 'Facuty assigned successfully';
        echo json_encode($response);
    }
    
    function add_feedback_comment(){
        $attempt_id                = $this->input->post('attempt_id');
        $attempt_details           = $this->Course_model->get_assignment_attempt(array('select'=>'dtua_comments','id'=>$attempt_id));
        $comment_array             = (isset($attempt_details['dtua_comments']))?json_decode($attempt_details['dtua_comments']):array();
        $comments                  = array();
        $comments['user_id']       = $this->__loggedInUser['id'];
        $comments['user_type']     = '1';
        $comments['comment']       = $this->input->post('feedback_text');
        $comments['update_date']   = date("d-m-Y");
        $comment_array[]           = $comments;
        $feedback_comment          = json_encode($comment_array);
        $param                     = array();
        $param['id']    = $attempt_id;
        $param['dtua_comments']    = $feedback_comment;
        $savecomment               = $this->Course_model->register_user_descriptive_test($param);
        $response                  = array();
        if($savecomment){
            $response['error']       = false;
            $response['message']     = 'Comment added successfully.';
            $response['updated_date']= date("d-m-Y");
        } else {
            $response['error']     = true;
            $response['message']   = 'Error in saving comment.';
        }
        echo json_encode($response);die();
        
    }
    function assign_grade(){
        $this->load->model(array('Report_model','User_model'));
        $attempt_id                = $this->input->post('attempt_id');
        $grade_id                  = $this->input->post('grade_id');
        $grade_txt                 = $this->input->post('grade_txt');
        $assignment_id             = $this->input->post('assignment_id');
        $user_id                   = $this->input->post('user_id');
        $send_mail                 = $this->User_model->user(array('select'=>'us_email,us_name','id'=>$user_id));
        $update_date               = date('Y-m-d H:i:s');
        $param                     = array();
        $param['id']               = $attempt_id;
        $param['dtua_grade']       = $grade_txt;
        $param['dtua_evaluated']   = '1';
        $param['dtua_assigned_to'] = $this->__loggedInUser['id'];
        $param['updated_date']     = $update_date;
        $this->Report_model->assign_grade($param);
        $lecture    = $this->Course_model->lecture(array('id' => $assignment_id,'select' => 'cl_lecture_name,cl_course_id'));
        $course     = $this->Course_model->course(array('id' => $lecture['cl_course_id'],'select'=>'cb_title'));
        
        //Updating lecture grade in course subscription table
        $log_param                                = array();
        $log_param['course_id']                   = $lecture['cl_course_id'];
        $log_param['lecture_id']                  = $assignment_id;
        $log_param['user_id']                     = $user_id;
        $log_param['grade']                       = $grade_txt;
        update_lecture_log_wiht_subscription($log_param);
        //End of Updating lecture grade in course subscription table
        
        $data                   = array();
        $course_title           = $course['cb_title'];
        $data['lecture_name']   = $lecture['cl_lecture_name'];
        $template               = $this->ofabeemailer->template(array('email_code' => 'assign_grade'));
        //$param['to']            = array('santhoshkumar@enfintechnologies.com');
        $param['to']            = $send_mail['us_email'];
        $param['subject']       = $template['em_subject'];
        $contents = array(
            'site_name' => config_item('site_name')
            , 'username' => $send_mail['us_name']
            , 'grade'     => $grade_txt
            , 'course_title' => $course['cb_title']
            , 'assignment'   => $lecture['cl_lecture_name']
            , 'site_url' => site_url()
        );
        $param['body'] = $this->ofabeemailer->process_mail_content($contents, $template['em_message']);
        $this->ofabeemailer->send_mail($param);
        $response               = array();
        $response['error']      = false;
        $response['message']    = 'Grade assigned successfully';
        echo json_encode($response);
    }
    function assign_assessment_grade(){
        $this->load->model(array('Report_model','User_model'));
        $attempt_id                = $this->input->post('attempt_id');
        $grade_id                  = $this->input->post('grade_id');
        $grade_txt                 = $this->input->post('grade_txt');
        $assessment_id             = $this->input->post('assessment_id');
        $user_id                   = $this->input->post('user_id');
        $send_mail                 = $this->User_model->user(array('select'=>'us_email,us_name','id'=>$user_id));
        $param                     = array();
        $param['aa_grade']         = $grade_txt;
        $param['id']               = $attempt_id;
        $this->Report_model->assign_assessment_grade($param);
        $lecture    = $this->Course_model->lecture(array('id' => $assessment_id,'select' => 'cl_lecture_name,cl_course_id'));
        $course     = $this->Course_model->course(array('id' => $lecture['cl_course_id'],'select'=>'cb_title'));
        
         //Updating lecture grade in course subscription table
         $log_param                  = array();
         $log_param['course_id']     = $lecture['cl_course_id'];
         $log_param['lecture_id']    = $assessment_id;
         $log_param['user_id']       = $user_id;
         $log_param['grade']         = $grade_txt;

         update_lecture_log_wiht_subscription($log_param);
         //End of Updating lecture grade in course subscription table


   
        $template = $this->ofabeemailer->template(array('email_code' => 'assessment_grade'));
        //$param['to']         = array('santhoshkumar@enfintechnologies.com');
        $param['to']            = $send_mail['us_email'];
        $param['subject']       = $template['em_subject'];
        $contents = array(
            'site_name' => config_item('site_name')
            , 'username' => $send_mail['us_name']
            , 'grade'     => $grade_txt
            , 'course_title' => $course['cb_title']
            , 'assessment'   => $lecture['cl_lecture_name']
            , 'site_url' => site_url()
        );
        $param['body'] = $this->ofabeemailer->process_mail_content($contents, $template['em_message']);
        $this->ofabeemailer->send_mail($param);
        $response               = array();
        $response['error']      = false;
        $response['message']    = 'Grade assigned successfully';
        echo json_encode($response);
    }
    
    function get_assign_report(){
        $attempt_id    = $this->input->post('attempt_id');
        $assignment_id = $this->input->post('assignment_id');
        $this->load->model(array('User_model'));
        $param               = array();
        $param['id']         = $assignment_id; 
        $param['attempt_id'] = $attempt_id;
        $objects = array();
        $objects['key'] = 'grade_scale';
        $callback = 'grade_scale';
        $grade_objects = $this->memcache->get($objects, $callback, array()); 
        $report              = $this->Course_model->get_descriptive_test_report($param);
        if(isset($report[0]['dtua_assigned_to']))
        {
            $facuty_detail   = $this->User_model->user(array('select'=>'us_name','id'=>$report[0]['dtua_assigned_to']));
        }
        $response            = array();
        $response['assignment'] = $report;
        $response['assignment'][0]['evaluated_by'] = (isset($facuty_detail))?$facuty_detail['us_name']:'';
        $response['grade']                         = $grade_objects;
        echo json_encode($response);
        die();
    }
    function update_grade_report(){
       
        $this->load->model(array('Report_model','User_model','Course_model'));
        $attempt_id                = $this->input->post('attempt_id');
        $grade_id                  = $this->input->post('grade_id');
        $assignment_id             = $this->input->post('assignment_id');
        $assignment_details        = $this->Course_model->descriptive_test(array('lecture_id'=>$assignment_id,'select'=>'dt_total_mark'));
        $objects                   = array();
        $objects['key']            = 'grade_scale';
        $callback                  = 'grade_scale';
        $grade_objects             = $this->memcache->get($objects, $callback, array());
        foreach($grade_objects as $grade_object)
        {
            if($grade_object['id']==$grade_id){
                $grade_name = $grade_object['gr_name'];
                break;
            }
        }

        $assign_param                     = array();
        $log_param                        = array();
        $assign_param['dtua_grade']       = $grade_name;
        if($this->input->post('assignment_mark'))
        {
            $assign_param['mark']                = $this->input->post('assignment_mark');
            $total_mark                          = $assignment_details['dt_total_mark'];
            $percentage                          = ( $assign_param['mark'] / $total_mark ) * 100;
            $log_param['percentage_of_marks']    = $percentage;
        }
        $assign_param['id']               = $attempt_id;
        $assign_param['dtua_evaluated']   = '1';
        $assign_param['dtua_assigned_to'] = $this->__loggedInUser['id'];
        $update_date                      = date('Y-m-d H:i:s');
        $assign_param['updated_date']     = $update_date;
        $this->Report_model->assign_grade($assign_param);

        $params                    = array();
        $params['id']              = $assignment_id; 
        $params['attempt_id']      = $attempt_id;
        $report                    = $this->Course_model->get_descriptive_test_report($params);
        $lecture                   = $this->Course_model->lecture(array('id' => $assignment_id,'select' => 'cl_lecture_name,cl_course_id'));
        $course                    = $this->Course_model->course(array('id' => $lecture['cl_course_id'],'select'=>'cb_title'));
        $user_id                   = $report[0]['dtua_user_id'];
        
        $course_id                 = $lecture['cl_course_id'];
        //Updating lecture grade in course subscription table
        $log_param['course_id']     = $lecture['cl_course_id'];
        $log_param['lecture_id']    = $assignment_id;
        $log_param['user_id']       = $user_id;
        $log_param['grade']         = $grade_name;
        update_lecture_log_wiht_subscription($log_param);
        //End of Updating lecture grade in course subscription table
        
        //Notification
        $this->load->library('Notifier');
        $this->notifier->push(
            array(
                'action_code' => 'assignment_graded',
                'assets' => array('assignment_name' => $lecture['cl_lecture_name'],'course_name' => $course['cb_title'],'grade' => $grade_name,'course_id'=>$course_id),
                'target' => $assignment_id,
                'individual' => true,
                'push_to' => array($user_id)
            )
        );
        //End notification

        
        $data                      = array();
        $course_title              = $course['cb_title'];
        $data['lecture_name']      = $lecture['cl_lecture_name'];
        $template                  = $this->ofabeemailer->template(array('email_code' => 'assign_grade'));
        $param['to']               = $report[0]['us_email'];
        //$param['to']               = 'santhoshkumar@enfintechnologies.com';
        $param['subject']          = $template['em_subject'];
        $contents = array(
            'site_name' => config_item('site_name')
            , 'username' => $report[0]['us_name']
            , 'grade'     => $grade_name
            , 'course_title' => $course['cb_title']
            , 'assignment'   => $lecture['cl_lecture_name']
            , 'site_url' => site_url()
        );
        $param['body'] = $this->ofabeemailer->process_mail_content($contents, $template['em_message']);
        $this->ofabeemailer->send_mail($param);
        $get_faculty_record = $this->User_model->user(array('select'=>'us_name,us_image','id'=>$this->__loggedInUser['id']));
        $response                  = array();
        $response['error']         = false;
        $response['message']       = 'Grade assigned successfully';
        $response['grade']         = $grade_id;
        $response['grade_name']    = $grade_name;
        $response['faculty_name'] = $get_faculty_record['us_name'];
        $response['faculty_img']  = $get_faculty_record['us_image'];
        echo json_encode($response);
    }
    
    /* End written by Alex. */
    
    public function course_performance()
    {
        if(!in_array('1', $this->report_privilege))
        {
            redirect(admin_url()); exit;
        }
        // echo "<pre>";print_r($this->report_privilege);exit;
        $breadcrumb                     = array();
        $breadcrumb[]                   = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]                   = array( 'label' => lang('report'), 'link' => admin_url('report/course'), 'active' => 'active', 'icon' => '' );
        $breadcrumb[]                   = array( 'label' => 'Course Performance Report', 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']             = $breadcrumb;  
        $filter_param                   = array();
        if($this->__loggedInUser['rl_full_course'] != 1){
            $param['current_logged_user']   = $this->__loggedInUser['id'];
            $access_course_list             = $this->Course_model->course_permission($param);
            $course_id_list                 = array_column($access_course_list, 'ct_course_id');
            $filter_param['course_id_list'] = $course_id_list;
        }
        $filter_param['select']         = 'id,cb_title,cb_access_validity,cb_validity,cb_validity_date,cb_course_likes,cb_course_dislikes,cb_course_forum_likes,cb_course_forum_dislikes';
        $data['courses']                = $this->Course_model->course_new($filter_param);
        $this->load->view(config_item('admin_folder').'/course_performance_report',$data);
    }
    public function export_course_performance()
    {
        if(!in_array('1', $this->report_privilege))
        {
            redirect(admin_url()); exit;
        }
        $filter_param                   = array();
        if($this->__loggedInUser['rl_full_course'] != 1){
            $param['current_logged_user']   = $this->__loggedInUser['id'];
            $access_course_list             = $this->Course_model->course_permission($param);
            $course_id_list                 = array_column($access_course_list, 'ct_course_id');
            $filter_param['course_id_list'] = $course_id_list;
        }
        $filter_param['select']         = 'id,cb_title,cb_access_validity,cb_validity,cb_validity_date,cb_course_likes,cb_course_dislikes,cb_course_forum_likes,cb_course_forum_dislikes';
        $data['courses']                = $this->Course_model->course_new($filter_param);
        /*Log creation*/
        $user_data                      = array();
        $user_data['user_id']           = $this->__loggedInUser['id'];
        $user_data['username']          = $this->__loggedInUser['us_name'];
        $user_data['useremail']          = $this->__loggedInUser['us_email'];
        $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
        $message_template               = array();
        $message_template['username']   = $this->__loggedInUser['us_name'];
        $message_template['report']     = 'course performance';
        $triggered_activity             = 'report_generated';
        log_activity($triggered_activity, $user_data, $message_template);
        $this->load->view(config_item('admin_folder').'/export_course_performance',$data);
    }
    public function course_institute_performance($course_id=0, $export=0)
    {
        if(!in_array('1', $this->report_privilege))
        {
            redirect(admin_url()); exit;
        }
        if($this->__loggedInUser['rl_full_course'] != '1')
        {
            $param['current_logged_user']   = $this->__loggedInUser['id'];
            $access_course_list             = $this->Course_model->course_permission($param);
            $course_id_list                 = array_column($access_course_list, 'ct_course_id');
            if (!in_array($course_id, $course_id_list))
            {
                $this->session->set_flashdata('message', lang('course_not_found'));
                redirect($this->config->item('admin_folder') . '/report/course_performance');
                exit;
            }
        }
        $filter_param               = array();
        $filter_param['select']     = 'id,cp_institute_id,cp_course_likes,cp_course_dislikes,cp_forum_likes,cp_forum_dislikes';
        $filter_param['course_id']  = $course_id;
        $data['institutes']         = $this->Course_model->course_institute_ratings($filter_param);
        
        foreach($data['institutes'] as $key => $institute){
            
            $objects                                    = array();
            $objects['key']                             = 'institute_' . $institute['cp_institute_id'];
            $callback                                   = 'institute';
            $params                                     = array('id' => $institute['cp_institute_id']);
            $institute_details                          = $this->memcache->get($objects, $callback, $params);
            $institute_name                             = $institute_details['ib_institute_code'].' - '.$institute_details['ib_name'];
            $data['institutes'][$key]['institute_name'] = $institute_name;
        }
        $objects                = array();
        $objects['key']         = 'course_'.$course_id;
        $callback               = 'course_details';
        $params                 = array('id' => $course_id);
        $course_details         = $this->memcache->get($objects, $callback, $params);
        $data['course_title']   = $course_details['cb_title'];
        $data['course_id']      = $course_id;
        if(!empty($data['institutes'])){
            $data['status']=true;
        }else{
            $data['status']=false;
        }
        if($export)
        {
            $this->load->view(config_item('admin_folder').'/export_institute_perform_report_detail',$data);
        }
        else
        {
            $this->load->view(config_item('admin_folder').'/course_institute_perform_report',$data);
        }    
    }
    /* Quiz Report */
    function assessments()
    {
        if(in_array(1, $this->report_privilege)) {
            $data          		= array();
            $breadcrumb         = array();
            $breadcrumb[]       = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
            $breadcrumb[]       = array( 'label' => 'Report', 'link' => admin_url('report/assessments'), 'active' => '', 'icon' => '' );
            $breadcrumb[]       = array( 'label' => 'Quiz Report', 'link' => '', 'active' => 'active', 'icon' => '' );
            $data['breadcrumb'] = $breadcrumb;

            $course_param                   = array();
            $course_param['direction']      = 'id'; 
            $course_param['not_deleted']    = '1';
            $course_param['select']         = 'course_basics.id, course_basics.cb_title, course_basics.cb_code';
            $course_param['tutor_id']  = ($this->__loggedInUser['rl_full_course']!='1')?$this->__loggedInUser['id']:false;
            $data['courses']                = $this->Course_model->courses_new($course_param);
            $objects                        = array();
            $objects['key']                 = 'institutes';
            $callback                       = 'institutes';
            $institutes                     = $this->memcache->get($objects, $callback, array()); 
            $grade_object                   = array();
            $grade_object['key']            = 'grade_scale';
            $callback                       = 'grade_scale';
            $grade_objects                  = $this->memcache->get($grade_object, $callback, array()); 
            $data['grades']                 = $grade_objects;
            $data['institutes']             = $institutes;
            $user_institute = (isset($this->__role_query_filter['institute_id']))?$this->__role_query_filter['institute_id']:'0';
            $institute_id   = 0;
            if( $user_institute > 0 )
            {
                $institute_id  = $user_institute;
            }
            if($institute_id!=0)
            {
                $this->load->model('Group_model');
                $param                  = array();
                $param['institute_id']  = $institute_id;
                $param['select']        = 'id, CONCAT(gp_institute_code," - ",gp_year," - ",gp_name) as batch_name';
                $param['not_deleted']   = true;
                $data['batches']    = $this->Group_model->groups($param);
            }
            $data['role_manager']   = $this->__role_query_filter;
            

            $data['course_id']      = $this->input->get('course_id');
            $data['quiz_id']        = $this->input->get('quiz_id');
            $data['institute_id']   = $this->input->get('institute_id');
            $data['batch_id']       = $this->input->get('batch_id');
            $data['filter_by']      = base64_decode($this->input->get('filter_by'));
            if($data['course_id'])
            {
                $quiz_param                 = array();
                $quiz_param['course_id']    = $data['course_id'];
                $quiz_param['lecture_type'] = '3';
                $quiz_param['not_deleted']  = true;
                $quiz_param['select']       = 'course_lectures.id, course_lectures.cl_lecture_name'; 
                $data['quizes']             = $this->Course_model->lectures($quiz_param);
            }
            if($data['institute_id'])
            {
                $this->load->model('Group_model');
                $param                  = array();
                $param['institute_id']  = $data['institute_id'];
                $param['select']        = 'id, CONCAT(gp_institute_code," - ",gp_year," - ",gp_name) as batch_name';
                $param['not_deleted']   = true;
                $data['batches']    = $this->Group_model->groups($param);
            }
            // echo '<pre>'; print_r();die;
            $this->load->view($this->config->item('admin_folder').'/assessments_report_advanced', $data);
        }
        else {
            redirect(admin_url()); exit;
        }
        
    }
    function get_assessments(){
        $course_id                              = $this->input->post('course_id');
        $response                               = array();
        $response['error']                      = false;
        $response['message']                    = 'Assessments list';
        $assessment_param                       = array();
        $assessment_param['course_id']          = $course_id;
        $assessment_param['lecture_type']       = '3';
        $assessment_param['not_deleted']        = true;
        $assessment_param['select']             = 'course_lectures.id, course_lectures.cl_lecture_name'; 
        $response['results']                    = $this->Course_model->lectures($assessment_param);
        echo json_encode($response);die();
    }
    function assessments_report(){
        $user_institute = (isset($this->__role_query_filter['institute_id']))?$this->__role_query_filter['institute_id']:'0';
        $course_id      = $this->input->get('course');
        $lecture_id     = $this->input->get('assessment');
        $lecture_id     = (($lecture_id!='all') || ($lecture_id!='0'))?$lecture_id:false;
        if( !$course_id || !$lecture_id )
        {
            redirect($this->config->item('admin_folder').'/report/assessments');
        }
 
        $keyword        = $this->input->get('keyword');
        $institute_id   = $this->input->get('institute_id');
        if( $user_institute > 0 )
        {
            $institute_id  = $user_institute;
        } 
        $batch_id       = $this->input->get('batch_id');
        $filter         = $this->input->get('filter');
        $tutor          = $this->input->get('tutor');
        $sort_by        = $this->input->get('sort_by');
        $offset         = $this->input->get('offset');
        
        $data                           = array();
        //Read institutes form memcached.
        $objects                        = array();
        $objects['key']                 = 'institutes';
        $callback                       = 'institutes';
        $institutes                     = $this->memcache->get($objects, $callback, array()); 
        $data['institutes']             = $institutes;
        $data['user']                   = $this->__loggedInUser;
        $data['role_manager']           = $this->__role_query_filter;
        $data['limit']                  = $this->__limit;
        $offset                         = isset($_GET['offset'])?$_GET['offset']:0;
        $data['batches']                = array();
        $grade_object                   = array();
        $grade_object['key']            = 'grade_scale';
        $callback                       = 'grade_scale';
        $grade_objects                  = $this->memcache->get($grade_object, $callback, array()); 
        $data['grade']                  = $grade_objects;
        $attempts_param                  = array();
        $attempts_param['lecture_id']    = $lecture_id;
        $attempts_param['course_id']     = $course_id;
        $attempts_param['institute_id']  = $institute_id;
        $attempts_param['batch_id']      = $batch_id;
        $attempts_param['filter']        = base64_decode($filter);
        $attempts_param['keyword']       = $keyword;
        $attempts_param['tutor']         = $tutor;
        $attempts_param['sort_by']       = $sort_by;
        $attempts_param['json']          = false;
        $attempts_param['limit']         = $this->__limit;
        $data['selected_course']        = '';
        $data['selected_assignment']    = '';
        if($attempts_param['lecture_id']){
            $assignment                     = $this->Course_model->lecture(array('id'=>$lecture_id, 'not_deleted' => true,'select'=>'cl_lecture_name'));
            $data['selected_assessment']    = $assignment['cl_lecture_name'];
            $course                        = $this->Course_model->course(array('id'=>$course_id,'select'=>'cb_title','not_deleted' => true));
            $data['selected_course']       = $course['cb_title'];
        }
        
        //processing pagination
        $page             = $offset;
        if($page===NULL||$page<=0)
        {
            $page         = 1;
        }
        $page             = ($page - 1)* $this->__limit;
        //end
        $attempts_param['offset']        = $page;
        $attempts_param['count']         = true;
        $total_attempts                  = $this->assessment_attendees($attempts_param);
        $data['total_attempts']          = $total_attempts;
        unset($attempts_param['count']);
        
        if($institute_id)
        {
            if($institute_id!=='all'){
                $institute_id_arr               = array();
                foreach($institutes as $institute){
                    $institute_id_arr[] = $institute['id'];
                }
                if(!in_array($institute_id, $institute_id_arr)){
                    $this->session->set_flashdata('error', 'No students found.');
                    redirect(admin_url('report/assignment'));
                }
                $this->load->model('Group_model');
                $param                  = array();
                $param['institute_id']  = $institute_id;
                $param['select']        = 'id, CONCAT(gp_institute_code," - ",gp_year," - ",gp_name) as batch_name';
                $param['not_deleted']   = true;
                $data['batches']        = $this->Group_model->groups($param);
            }
        }
        $attempts                   = $this->assessment_attendees($attempts_param);
        if(count($attempts)!=0){
            $data['assessments'][]['attempts'] =  $attempts;
            /*Log creation*/
            $user_data                          = array();
            $user_data['user_id']               = $this->__loggedInUser['id'];
            $user_data['username']              = $this->__loggedInUser['us_name'];
            $user_data['useremail']              = $this->__loggedInUser['us_email'];
            $user_data['user_type']             = $this->__loggedInUser['us_role_id'];
            $message_template                   = array();
            $message_template['username']       = $this->__loggedInUser['us_name'];
            $message_template['report']         = 'quiz';
            $triggered_activity                 = 'report_generated';
            log_activity($triggered_activity, $user_data, $message_template);
        } else {
            $this->session->set_flashdata('error', 'No students found.');
            $redirect_params   =  array();
            $redirect_params[] = 'course_id='.$course_id;
            $redirect_params[] = 'quiz_id='.$lecture_id;
            $redirect_params[] = 'institute_id='.$institute_id;
            $redirect_params[] = 'batch_id='.$batch_id;
            $redirect_params[] = 'filter_by='.($filter);
            redirect(admin_url('report/assessments').'?'.implode('&', $redirect_params));
        }
        
        $data['tutors']                 = $this->Course_model->get_course_tutors(array('course_id' => $attempts_param['course_id']));
        $data['access_permission']      = $this->report_privilege;
        // echo '<pre>'; print_r($data); die;
        $this->load->view(config_item('admin_folder').'/report_assessment_list', $data);
    }
   
    function course_assessment($course_id=0,$json= true)
    {
        $user_institute         = (isset($this->__role_query_filter['institute_id']))?$this->__role_query_filter['institute_id']:'0';
        $course_id              = $this->input->post('course_id');
        $assessment_id          = $this->input->post('assessment_id');
        $sort_by                = ($this->input->post('sort_by'))?$this->input->post('sort_by'):false;
        $tutor                  = ($this->input->post('tutor'))?$this->input->post('tutor'):false;
        
        if($user_institute >= 0){
            $institute_id       = $this->input->post('institute_id');
        } else {
            $institute_id       = $user_institute;
        }
        $batch_id               = $this->input->post('batch_id');
        $filter_by              = $this->input->post('filter');
        $filter                 = base64_decode($filter_by);
        $keyword                = $this->input->post('keyword');
        
        $limit                  = $this->__limit;
        $data['limit']          = $limit;
        $offset                 = $this->input->post('offset');
        $response               = array();
        $response['error']      = false;
        $response['message']    = 'Listing course assessments';
        $course = $this->Course_model->course(array('id' => $course_id));
        if(!$course)
        {
            if($json)
            {
                $response['error']      = true;
                $response['message']    = 'Requested Course details not found.';
                echo json_encode($response);exit;
            }
            else
            {
                return $response;exit;
            }
        }
        
        $assessment_param                       = array();
        $assessment_param['course_id']          = $course_id;
        $assessment_param['lecture_type']       = '3';
        $assessment_param['not_deleted']        = true;
        $assessment_param['select']             = 'course_lectures.id, course_lectures.cl_lecture_name, course_lectures.cl_course_id';
    
        $response['batches']        = array();
       
        if($institute_id != '')
        {
            $this->load->model('Group_model');
            $param                  = array();
            $param['institute_id']  = $institute_id;
            $param['select']        = 'id, CONCAT(gp_institute_code," - ",gp_year," - ",gp_name) as batch_name';
            $param['not_deleted']   = true;
            $response['batches']    = $this->Group_model->groups($param);
        }
        $response['tutors']         = $this->Course_model->get_course_tutors(array('course_id' => $course_id));
        
        $this->load->model(array('Report_model'));
        $response['assessments'] = array();
        
        $lecture_assessments = $this->Course_model->lectures($assessment_param);
        $attempts_param                  = array();
        $attempts_param['lecture_id']    = $assessment_id;
        $attempts_param['course_id']     = $course_id;
        $attempts_param['institute_id']  = $institute_id;
        $attempts_param['batch_id']      = $batch_id;
        $attempts_param['filter']        = $filter;
        $attempts_param['keyword']       = $keyword;
        $attempts_param['sort_by']       = $sort_by;
        $attempts_param['tutor']         = $tutor;
        
        $attempts_param['json']          = false;
        $attempts_param['count']         = true;
        $response['total_attempts']      = $this->assessment_attendees($attempts_param);
        unset($attempts_param['count']);
       
        $attempts_param['limit']         = $this->__limit;
        
        //processing pagination
        $page                            = $offset;
        if($page===NULL||$page<=0)
        {
            $page                        = 1;
        }
        $page                            = ($page - 1)* $this->__limit;
        //end
        $attempts_param['offset']     = $page;
        if(!empty($lecture_assessments))
        {
            if(empty($response['assessments']))
                {
                    $lecture_assessment['attempts'] = $this->assessment_attendees($attempts_param);
                }
                $response['assessments'][] = $lecture_assessment;
        }
       
       
   // echo '<pre>'; print_r($response);die;
        if($json)
        {
            echo json_encode($response);
        }
        else
        {
            return $response;
            //echo '<pre>'; print_r($response['assignments']);die;
        }
    }
    function assessment_attendees($param = array())
    {
      
        
        $lecture_id   = isset($param['lecture_id'])?$param['lecture_id']:$this->input->post('lecture_id');
        $institute_id = isset($param['institute_id'])?$param['institute_id']:$this->input->post('institute_id');
        $batch_id     = isset($param['batch_id'])?$param['batch_id']:$this->input->post('batch_id');
        $course_id    = isset($param['course_id'])?$param['course_id']:$this->input->post('course_id');
        $filter       = isset($param['filter'])?$param['filter']:$this->input->post('filter');
        $sort_by      = isset($param['sort_by'])?$param['sort_by']:$this->input->post('sort_by');
        $tutor        = isset($param['tutor'])?$param['tutor']:$this->input->post('tutor');
        
        $keyword      = isset($param['keyword'])?$param['keyword']:$this->input->post('keyword');
        $json         = isset($param['json'])?$param['json']:true;
        $count        = isset($param['count'])?$param['count']:false;
        $offset       = isset($param['offset'])?$param['offset']:1;
        $limit        = isset($param['limit'])?$param['limit']:$this->__limit;
        $this->load->model('Report_model');
        $attendees_param                = $this->__role_query_filter;
        $attendees_param['lecture_id']  = $lecture_id;
        $attendees_param['institute_id']= $institute_id;
        $attendees_param['batch_id']    = $batch_id;
        $attendees_param['course_id']   = $course_id;
        $attendees_param['filter']      = $filter;
        $attendees_param['sort_by']     = $sort_by;
        $attendees_param['tutor']       = $tutor;
        $attendees_param['keyword']     = $keyword;
        $attendees_param['offset']      = $offset;
        $attendees_param['limit']       = $limit;
        $attendees_param['count']       = $count;
   
        $return                         = $this->Report_model->assessment_attendees($attendees_param);
        
        if($json)
        {
            echo json_encode(array('attendees' => $return, 'error' => false));
        }
        else
        {
            return $return;        
        }
    }

    function course_consolidated_report()
    {
        $params                     = array();
        if($this->__loggedInUser['us_role_id'] == '3')
        {
            $params['tutor_id']     = $this->__loggedInUser['id'];
        }
        $consolidated_report        = $this->Course_model->consolidated_report($params);
        $course_wise_report         = array();
        $institute_wise_report      = array();
       
        if(!empty($consolidated_report))
        {
            foreach($consolidated_report as $report)
            {
                $course_id      = $report['ccr_course_id'];
                $institute_id   = $report['ccr_institute_id'];
                if(!isset($course_wise_report[$course_id]))
                {
                    $course_wise_report[$course_id]['enrolled']  = 0;
                    $course_wise_report[$course_id]['completed'] = 0;
                }
                if(!isset($institute_wise_report[$institute_id]))
                {
                    $institute_wise_report[$institute_id] = array();
                }
                if(!isset($institute_wise_report[$institute_id][$course_id]))
                {
                    $institute_wise_report[$institute_id][$course_id]             = array();
                    $institute_wise_report[$institute_id][$course_id]['enrolled'] = 0;
                }
                
                /*
                $course_wise_report[$course_id]['enrolled']  = $course_wise_report[$course_id]['enrolled']+$report['ccr_total_enrolled'];
                $course_wise_report[$course_id]['completed'] = $course_wise_report[$course_id]['completed']+$report['ccr_total_completed'];
                */
                
                $course_wise_report[$course_id]['enrolled']  = $this->Course_model->enrolled(array('course_id' => $course_id, 'count' => true));
                $course_wise_report[$course_id]['completed'] = $this->Course_model->enrolled(array('course_id' => $course_id, 'count' => true,'filter'=>'completed'));
                
                $institute_wise_report[$institute_id][$course_id]['enrolled'] = $institute_wise_report[$institute_id][$course_id]['enrolled']+$report['ccr_total_enrolled'];
            }
        }
        
        $courses            = array();
        $courses_objects    = $this->Course_model->course_for_consolidation();
        
        if(!empty($courses_objects))
        {
            foreach($courses_objects as $course_object)
            {
                $courses[$course_object['id']] = $course_object;
            }
        }

        $institutes         = array();
        $objects            = array();
        $objects['key']     = 'institutes'; 
        $callback           = 'institutes';
        $institutes_objects = $this->memcache->get($objects, $callback);
        if(!empty($institutes_objects))
        {
            foreach($institutes_objects as $institute_object)
            {
                $institutes[$institute_object['id']] = $institute_object;
            }
        }
        $data                           = array();
        $data['institutes']             = $institutes;
        $data['courses']                = $courses;
        $data['course_wise_report']     = $course_wise_report;
        $data['institute_wise_report']  = $institute_wise_report;
        // echo "<pre>";print_r($data);exit;
        $this->load->view(config_item('admin_folder').'/course_consolidated_report', $data);    
        
    }   
}
?>
<?php
class Course extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->lang->load('course');
        $this->__role_query_filter  = array();
        $this->__restrcited_method  = array('groups');
        $this->__admin_index        = 'admin';
        $this->__loggedInUser       = $this->auth->get_current_user_session('admin');
        $redirect                   = $this->auth->is_logged_in(false, false);
        if (!$redirect) {            
            redirect('login');
        }
        if($this->__loggedInUser['us_role_id'] == 8)
        {
            $this->__role_query_filter['institute_id'] = $this->__loggedInUser['us_institute_id'];
        }
        $this->actions = $this->config->item('actions');
        $this->load->model(array('Bundle_model','Course_model', 'Mailtemplate_model', 'Announcement_Model', 'Category_model', 'Catalog_model', 'User_model', 'Group_model'));
        $this->__lecture_type_array = array('1' => 'video',
            '2' => 'document',
            '3' => 'assesment',
            '4' => 'youtube',
            '5' => 'text',
            '6' => 'wikipedia',
            '7' => 'live',
        );
        $this->__lecture_type_keys_array = array();
        foreach ($this->__lecture_type_array as $id => $type) {
            $this->__lecture_type_keys_array[$type] = $id;
        }
        $this->__limit = 100;
        
        //fetching user privilege
        $this->course_id        = ($this->uri->segment(4) != null) ? $this->uri->segment(4) : 0;
        $params                 = array();
        $params['role_id']      = $this->__loggedInUser['role_id'];
        $params['user_id']      = $this->__loggedInUser['id'];
        $params['course_id']    = $this->course_id;
        $params['module']       = 'announcement';
       
        $this->privilege = array('view' => 1, 'add' => 2, 'edit' => 3, 'delete' => 4);

        $access_method                    = ($this->__loggedInUser['rl_full_course'] == '1')?'get_permission':'get_permission_course';
        $this->userPrivilege              = $this->accesspermission->$access_method($params);
        $this->student_enroll_privilege   = $this->accesspermission->$access_method(array('role_id' => $this->__loggedInUser['role_id'],'module' => 'student_enrollment','course_id'=>$params['course_id'],'user_id'=> $this->__loggedInUser['id']));
        $this->batch_enroll_privilege     = $this->accesspermission->$access_method(array('role_id' => $this->__loggedInUser['role_id'],'module' => 'batch_enrollment','course_id'=>$params['course_id'],'user_id'=> $this->__loggedInUser['id']));
        $this->assign_faculty_privilege   = $this->accesspermission->$access_method(array('role_id' => $this->__loggedInUser['role_id'],'module' => 'assign_faculty','course_id'=>$params['course_id'],'user_id'=> $this->__loggedInUser['id']));
        $this->course_content_privilege   = $this->accesspermission->$access_method(array('role_id' => $this->__loggedInUser['role_id'], 'module' => 'course_content','course_id'=>$params['course_id'],'user_id'=> $this->__loggedInUser['id']));
        $this->user_privilege             = $this->accesspermission->get_permission(array('role_id' => $this->__loggedInUser['role_id'],'module' => 'user'));   
        $this->course_privilege           = $this->accesspermission->get_permission(array('role_id' => $this->__loggedInUser['role_id'],'module' => 'course'));
        $this->forum_privilege            = $this->accesspermission->get_permission(array('role_id' => $this->__loggedInUser['role_id'], 'module' => 'course_forum'));
        $this->report_privilege           = $this->accesspermission->get_permission(array('role_id' => $this->__loggedInUser['role_id'], 'module' => 'report'));
        $this->backup_privilege           = $this->accesspermission->$access_method(array('role_id' => $this->__loggedInUser['role_id'],'module' => 'backups','course_id'=>$params['course_id'],'user_id'=> $this->__loggedInUser['id']));
        $this->event_privilege            = $this->accesspermission->get_permission(array('role_id' => $this->__loggedInUser['role_id'],'module' => 'event'));
        $this->review_permission          = $this->accesspermission->get_permission(array( 'role_id' => $this->__loggedInUser['role_id'], 'module' => 'review' ));
    }
    public function index()
    {
        if(!in_array('1', $this->course_privilege))
        {
            redirect(admin_url()); exit;
        }
        $data               = array();
        $breadcrumb         = array();
        $limit              = $this->__limit;
        $offset             = 0;
        $breadcrumb[]       = array('label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>');
        $breadcrumb[]       = array('label' => lang('course_bar_trainings'), 'link' => '', 'active' => 'active', 'icon' => '');
        $data['breadcrumb'] = $breadcrumb;
        $data['title']      = lang('course_bar_trainings');
        $data['categories'] = $this->Category_model->categories(array(
                                                            'direction' => 'DESC', 
                                                            'not_deleted' => true,
                                                            'select' => 'id, ct_name'
                                                        ));
        $data['limit']              = $limit;
        $data['show_load_button']   = false;
        $course_param               = $this->__role_query_filter;
        $course_param['direction']  = 'ASC';
        $course_param['order_by']   = 'cb_title';
        $course_param['count']      = true;
        $course_param['filter']     = ($this->input->get('filter') != null)? $this->input->get('filter') : '';
        $course_param['category_id']    = ($this->input->get('category') != null)? $this->input->get('category') :'';
       
        if($this->input->get('keyword') != null)
        {
            $keyword_arr  = explode('-', $this->input->get('keyword'));
            $keyword      = implode(' ',$keyword_arr);
            $course_param['keyword']        = trim($keyword);
        }
        if ($this->__loggedInUser['rl_full_course'] != 1) {
            $course_param['tutor_id']   = $this->__loggedInUser['id'];
        }        
        if($course_param['filter'] == '') {
            $course_param['not_deleted']    = true;
            $course_param['status']     = '1';
        }       
        $data['total_courses']      = $this->Course_model->courses_new($course_param);
        unset($course_param['count']);
        $course_param['select']     = 'course_basics.id, cb_title, cb_code, cb_category, cb_status, cb_approved, cb_deleted,cb_access_validity,cb_validity,cb_validity_date';
        $course_param['limit']      = $limit;
        $course_param['offset']     = $offset;
        if ($data['total_courses'] > $this->__limit) {
            $data['show_load_button'] = true;
        }
        //echo '<pre>';print_r($course_param);die;
        $data['courses']        = $this->Course_model->courses_new($course_param);
        $data['admin_details']  = $this->__loggedInUser;
        $data['course_privilege']   = $this->course_privilege;
        //echo '<pre>';print_r($data);die('------------------------------');
        $this->load->view($this->config->item('admin_folder') . '/courses', $data);
    }
    //checking new process
    public function course_new_json()
    {
        $user_id            = $this->input->post('user_id');
        $full_course_access = $this->__loggedInUser['rl_full_course'];
        if ($full_course_access != '1') {
            $param['current_logged_user']   = $this->__loggedInUser['id'];
            $access_course_list             = $this->Course_model->course_permission($param);
            $course_param['course_id_list'] = array_column($access_course_list, 'ct_course_id');
        }
        $course_param['direction']      = 'DESC';
        $course_param['order_by']       = 'id';
        // $course_param['status']         = '1';
        $course_param['select']         = 'id,cb_title,cb_status,cb_deleted';
        $course_param['not_deleted']    = $this->input->post('not_deleted');
        $enrolled_courses               = $this->Course_model->course_enrolled(array('user_id'=>$user_id,'select'=>'cs_course_id'));
        $course_param['not_in']         = array_column($enrolled_courses, 'cs_course_id');
        $courses                        = $this->Course_model->course_new($course_param);
        if(!empty($courses)){
            $response               = array();
            $response['success']    = true;
            $response['courses']    = $courses;
        }else{
            $response               = array();
            $response['success']    = false;
            $response['courses']    = array();
        }
        echo json_encode($response);
    }
    //dont delete its still working
    public function course_json()
    {
        $data                       = array();
        $data['show_load_button']   = false;
        $course_param               = $this->__role_query_filter;
        $user_id                    = $this->input->post('user_id');
        $limit                      = $this->input->post('limit');
        $offset                     = $this->input->post('offset');
        $page                       = $offset;
        if ($page === null || $page <= 0) {
            $page = "1";
        }
        $page = ($page - 1) * $limit;
        
        $course_param['direction']      = 'ASC';
        $course_param['order_by']       = 'cb_title';
        $course_param['category_id']    = $this->input->post('category_id');
        $course_param['keyword']        = $this->input->post('keyword');
        $course_param['keyword']        = trim($course_param['keyword']);
        $course_param['filter']         = $this->input->post('filter');

        if ($this->__loggedInUser['rl_full_course'] != 1) {
            $course_param['tutor_id']   = $this->__loggedInUser['id'];
        }
        $course_param['not_deleted']    = $this->input->post('not_deleted');
        $course_param['count']          = true;
        $total_courses                  = $this->Course_model->courses_new($course_param);
        $data['total_courses']          = $total_courses;
        unset($course_param['count']);
        $course_param['select']         = 'course_basics.id, cb_title, cb_code, cb_category, cb_status, cb_approved, cb_deleted,cb_access_validity,cb_validity,cb_validity_date';
        $data['limit']                  = $limit;
        $course_param['limit']          = $limit;
        $course_param['offset']         = $page;
        if ($total_courses > ($this->__limit * $offset)) {
            $data['show_load_button']   = true;
        }
        $data['courses']                = $this->Course_model->courses_new($course_param);
        // echo $this->db->last_query();exit;
        echo json_encode($data);
    }
    public function change_status_bulk()
    {
        $response           = array();
        $response['error']  = false;
        $count              = 0;
        $req                = 0;
        $error_count        = 0;
        $error_msg          = '';
        $status             = (preg_match('/^[01]$/', $this->input->post('status_bulk')) == true) ? $this->input->post('status_bulk') : false ;
        $course_ids         = json_decode($this->input->post('courses'));
        if (!empty($course_ids) && $status !== false) 
        {
            $response['courses']    = array();
            $redirect = $this->auth->is_logged_in(false, false);
            foreach ($course_ids as $course_id) {
                $save = array();  
                $save['id'] = $course_id;
                $save['cb_status'] = $status;
                $save['action_by'] = $this->auth->get_current_admin('id');
                $save['updated_date'] = date('Y-m-d H:i:s');
                
                $course = $this->Course_model->course(array(
                                                    'id' => $course_id,
                                                    'select' => 'course_basics.id, cb_title, cb_code, cb_category, cb_status, cb_approved, cb_deleted, cb_image,cb_access_validity,cb_validity,cb_validity_date'
                                                ));
                $lecture_count = $this->Course_model->get_course_lecture_count(array('course_id' => $course_id));
                if (($status == 1) && $course['cb_status'] == 1) {
                    $count++;
                }
 
                if( $status == 0 )
                {
                    $this->Course_model->save($save);
                    $course['cb_status'] = $status;
                    $response['courses'][]  = $course;
                }
                else
                {

                    $cb_status              = $this->checkcoursecontent(array('course_id'=>$course_id));
                    // $response['cb_status']  = $cb_status['cb_status'];
                    // if($cb_status['cb_status'] == '0')
                    // {
                    //     $response['error']      = true;
                    //     $response['message']    = 'Please activate atleast one lucture first!';
                    //     echo json_encode($response);
                    //     exit;
                    // }


                    if( ($course['cb_image'] != 'default.jpg') && ($lecture_count > 0) && $cb_status['cb_status'] != '0') {
                        $this->Course_model->save($save);
                        $course['cb_status'] = $status;
                        $response['courses'][]  = $course;
                    } else {
                        if($course['cb_image'] == 'default.jpg') {
                            $error_count++;
                            $error_msg      .= 'Course named '.$course['cb_title'].' course image missing </br>';
                        }
                        if($lecture_count < 1) {
                            $error_count++;
                            $error_msg      .= 'Course named '.$course['cb_title'].' does not have any lectures </br>';
                        }

                        if($cb_status['cb_status'] == '0')
                        {
                            $error_count++;
                            $error_msg      .= 'The Course <b>'.$course['cb_title'].'</b> does not have any active lectures</br>';
                        }
                    }
                }                   
                $this->invalidate_course(array('course_id' => $course_id));
                $req++;
            }
            $this->invalidate_course();
            if ( $error_count == 0) {
                $response['error'] = false;
                $response['message'] = ($status == 1)? 'Activated':'Deactivated';
            }
            if ($error_count > 0) {
                $response['error']  = true;
                $message            = 'Activation failed for following courses: </br>';
                $response['message'] = $message.$error_msg;
            }
            // $course_param                   = $this->__role_query_filter;
            
            // $course_param['course_id_list'] = $course_ids;
            // $course_param['select']         = 'course_basics.id, cb_title, cb_code, cb_category, cb_status, cb_approved, cb_deleted';
            // $response['courses']            = $this->Course_model->courses_new($course_param);
        } else {
            $response['error']  = true;
            $response['message']= 'No Students choosen';
        }
        
        echo json_encode($response);
    }
    public function change_status()
    {
        //function used to change the status of the course
        $response               = array();
        $response['error']      = true;
        $response['message']    = lang('error_change_status');
        //validation for course_id
        $course_id              = (preg_match('/^[1-9]+[0-9]*$/', $this->input->post('course_id')) == true) ? $this->input->post('course_id') : false;
        if($course_id)
        {
            $course_param               = array();
            $course_param['id']         = $course_id;
            $course_param['select']     = 'id, cb_image,cb_description,cb_category,cb_status, cb_title, cb_deleted';
            $course                     = $this->Course_model->course($course_param);
            if ($course['cb_image'] == 'default.jpg' && $course['cb_status'] == '0') {
                $response['error']      = true;
                $response['message']    = lang('upload_image_to_publish');
                echo json_encode($response);
                exit;
            }
            if (strip_tags($course['cb_description']) == '' && $course['cb_status'] == '0') {
                $response['error']      = true;
                $response['message']    = lang('decription_to_publish');
                echo json_encode($response);
                exit;
            }
            if ($course['cb_category'] == '' && $course['cb_status'] == '0') {
                $response['error']      = true;
                $response['message']    = lang('category_to_publish');
                echo json_encode($response);
                exit;
            }
            $course_lectures_count      = $this->Course_model->get_course_lecture_count(array('course_id' => $course_id));
            if ($course_lectures_count < 1 && $course['cb_status'] == '0') {
                $response['error']      = true;
                $response['message']    = lang('error_change_status');
                echo json_encode($response);
                exit;
            }
    
            $save                       = array();
            $save['id']                 = $course_id;
            $save['action_by']          = $this->auth->get_current_admin('id');
            $save['updated_date']       = date('Y-m-d H:i:s');
            $save['cb_status']          = '1';
            $mstatus                    = 'course_activated';
            $message                    = lang('published');
            if ($course['cb_status'] == '1') {            
    
                $save['cb_status']      = '0';
                $mstatus                = 'course_deactivated'; 
                $message                = lang('unpublished');
            }else{
                $cb_status              = $this->checkcoursecontent(array('course_id'=>$course_id));
                $response['cb_status']  = $cb_status['cb_status'];
                if($cb_status['cb_status'] == '0')
                {
                    $response['error']      = true;
                    $response['message']    = 'Please activate atleast one lecture first!';
                    echo json_encode($response);
                    exit;
                }
            }
            if ($this->Course_model->save($save)) {
                
                $response['error']          = false;
                $response['message']        = $message;
                $filter_param               = array();
                $filter_param['course_id']  = $course_id;
                $filter_param['match']      = true;
                $matched_bundles            = $this->Bundle_model->get_all_match($filter_param);
                if(!empty($matched_bundles))
                {
                    foreach($matched_bundles as $bundle_key => $bundle)
                    {
                        $bundle_id              = $bundle['id'];
                        $enrolled_courses       = json_decode($bundle['c_courses'],true);
                        foreach ($enrolled_courses as $key => $enrolled_course)
                        {
                            if($course_id == $enrolled_course['id'])
                            {
                                $enrolled_courses[$key]['status'] = ($course['cb_status'] == '1')?'0':'1';
                            }
                        }
                        $save                   = array();
                        $filter                 = array();
                        $save['c_courses']      = json_encode($enrolled_courses);
                        $filter['id']           = $bundle_id;
                        $filter['update']       = true;
                        $result                 = $this->Bundle_model->save($save,$filter);
                    }
                }
            }
            $response['course']         = $this->Course_model->course(array(
                                                                    'id' => $course_id,
                                                                    'select' => 'course_basics.id, cb_title, cb_code, cb_category, cb_status, cb_approved, cb_deleted,cb_access_validity,cb_validity,cb_validity_date'
                                                                ));
            $user_data              = array();
            $user_data['user_id']   = $this->__loggedInUser['id'];
            $user_data['username']  = $this->__loggedInUser['us_name'];
            $user_data['useremail']  = $this->__loggedInUser['us_email'];
            $user_data['user_type'] = $this->__loggedInUser['us_role_id'];
            
            $message_template                   = array();
            $message_template['username']       = $this->__loggedInUser['us_name'];
            $message_template['course_name']    = $course['cb_title'];
            
            $triggered_activity                 = $mstatus;
            log_activity($triggered_activity, $user_data, $message_template); 
    
            $this->invalidate_course(array('course_id' => $course_id));
            $this->invalidate_course();
        }
        else
        {
            $response['message']    = lang('error_unknown_message');
        }
        echo json_encode($response);
    }

    function checkcoursecontent($params = array())
    {
        $lectures = $this->Course_model->lectures(array('count'=>true, 'course_id'=> $params['course_id'], 'status' => '1'));
       
        $sections = $this->Course_model->section(array('count'=>true, 'course_id'=> $params['course_id'], 'status' => '1'));
        
        if(!$lectures || !$sections){
            $save                   = array();
            $save['id']             = $params['course_id'];
            $save['action_by']      = $this->auth->get_current_admin('id');
            $save['updated_date']   = date('Y-m-d H:i:s');
            $save['cb_status']      = '0';
            
            if ($this->Course_model->save($save)) {
                $user_data              = array();
                $user_data['user_id']   = $this->__loggedInUser['id'];
                $user_data['username']  = $this->__loggedInUser['us_name'];
                $user_data['useremail']  = $this->__loggedInUser['us_email'];
                $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
                
                $message_template                   = array();
                $message_template['username']       = $this->__loggedInUser['us_name'];
                $course                             = $this->Course_model->course(array('id' => $params['course_id'], 'select' => 'cb_title'));
                $message_template['course_name']    = $course['cb_title'];
                $mstatus                            = 'course_deactivated'; 
                log_activity($mstatus, $user_data, $message_template); 
                $this->memcache->delete('course_'.$params['course_id']);
                $this->memcache->delete('course_mob'.$params['course_id']);
                return array('cb_status' => '0');
            }
        }
        return array('cb_status' => '1');
    }

    public function approve_course()
    {
        $response = array();
        $response['error'] = false;
        $course_id = $this->input->post('course_id');
        $course = $this->Course_model->course(array('id' => $course_id));
        $save = array();
        $save['id'] = $course_id;
        $save['action_by'] = $this->auth->get_current_admin('id');
        $save['updated_date'] = date('Y-m-d H:i:s');
        if (isset($this->__role_query_filter['editor_id']) || isset($this->__role_query_filter['teacher_id'])) {
            $save['cb_status'] = '2';
            $save['action_id'] = $this->actions['pending'];
            $response['message'] = lang('pending_approval');
            $action_label = $this->actions[$this->actions['pending']]['label'];
            $button_text = lang('pending_approval');
            //case if record is not deleted
            $action_class = 'label-warning';
            $label_class = 'spn-inactive';
            $action = lang('pending_approval');
            $action_list = '<a href="javascript:void(0);" data-target="#publish-course" data-toggle="modal" onclick="changeCourseStatus(\'' . $course['id'] . '\', \'' . base64_encode(lang('are_you_sure_to') . ' ' . lang('publish') . ' ' . lang('course') . ' - ' . str_replace("'", '',$course['cb_title']) . ' ?') . '\',\'' . lang('activate') . '\',\'' . lang('activate') . '\')">' . lang('activate') . '</a>';
        } else {
            $save['cb_status'] = '1';
            $save['action_id'] = $this->actions['activate'];
            $response['message'] = lang('published');
            $action_label = $this->actions[$this->actions['activate']]['label'];
            $button_text = lang('deactivate');
            //case if record is not deleted
            $action_class = 'label-success';
            $label_class = 'spn-active';
            $action = lang('active');
            $action_list = '<a href="javascript:void(0);" data-target="#publish-course" data-toggle="modal" onclick="changeCourseStatus(\'' . $course['id'] . '\', \'' . base64_encode(lang('are_you_sure_to') . ' ' . lang('unpublish') . ' ' . lang('course') . ' - ' . str_replace("'", '',$course['cb_title']) . ' ?') . '\',\'' . lang('deactivate') . '\',\'' . lang('deactivate') . '\')">' . lang('deactivate') . '</a>';
            
        }
        //set the database value
        $action_date = date("d M Y", strtotime($save['updated_date']));
        $action_author = $this->auth->get_current_admin('us_name');
        $action_author = ($action_author) ? $action_author : 'Admin';
        $this->invalidate_course(array('course_id' => $course_id));
        $this->invalidate_course();
        
        if (!$this->Course_model->save($save)) {
            $response['error'] = true;
            $response['message'] = lang('error_change_status');
        }
        $response['actions'] = array();
        $response['actions']['cb_title'] = $course['cb_title'];
        $response['actions']['cb_price'] = $course['cb_price'];
        $response['actions']['cb_discount'] = $course['cb_discount'];
        $response['actions']['cb_is_free'] = $course['cb_is_free'];
        $response['actions']['action_label'] = $action_label;
        $response['actions']['action'] = $action;
        $response['actions']['status'] = 1;
        if (isset($this->__role_query_filter['editor_id']) || isset($this->__role_query_filter['teacher_id'])) {
            $response['actions']['status'] = 2;
        }
        $response['actions']['deleted'] = $course['cb_deleted'];
        $response['actions']['action_date'] = $action_date;
        $response['actions']['action_author'] = $action_author;
        $response['actions']['label_class'] = $label_class;
        $response['actions']['action_class'] = $action_class;
        $response['actions']['button_text'] = $button_text;
        $response['actions']['label_text'] = $action_label . ' by- ' . $action_author . ' on ' . $action_date;
        $response['action_list'] = $action_list;
        echo json_encode($response);
    }
    public function restore()
    {
        $response           = array();
        $response['error']  = false;
        $course_id          = (preg_match('/^[1-9]+[0-9]*$/', $this->input->post('course_id')) == true) ? $this->input->post('course_id') : false;
        if($course_id)
        {
            $course             = $this->Course_model->course(array('id' => $course_id));
            $save               = array();
            $save['id']         = $course_id;
            $save['action_by']  = $this->auth->get_current_admin('id');
            $save['updated_date'] = date('Y-m-d H:i:s');
            $save['cb_deleted'] = '0';
            $save['cb_status']  = '0';
            $response['message'] = lang('restore_course_success');
            if (!$this->Course_model->save($save)) {
                $response['error'] = true;
                $response['message'] = lang('restore_course_failed');
            }
            $response['course']     = $this->Course_model->course(array(
                                                            'id' => $course_id,
                                                            'select' => 'course_basics.id, cb_title, cb_code, cb_category, cb_status, cb_approved, cb_deleted, cb_access_validity,cb_validity,cb_validity_date'
                                                        ));

            //To delete mobile api get subscriptions memcache
            $course_param           = array();
            $course_param['id']     = $course_id;
            $this->reset_mobile_subscriptions($course_param);

            $user_data              = array();
            $user_data['user_id']   = $this->__loggedInUser['id'];
            $user_data['username']  = $this->__loggedInUser['us_name'];
            $user_data['useremail']  = $this->__loggedInUser['us_email'];
            $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
            
            $message_template                   = array();
            $message_template['username']       = $this->__loggedInUser['us_name'];
            $message_template['course_name']    = $course['cb_title'];
            
            $triggered_activity                 = 'course_restore';
            log_activity($triggered_activity, $user_data, $message_template);                                            
            $this->invalidate_course(array('course_id' => $course_id));
            $this->invalidate_course();
        }
        else
        {
            $response['error'] = true;
            $response['message'] = lang('error_unknown');
        }
        
  
        echo json_encode($response);
    }

    public function delete()
    {
        $response           = array();
        $response['error']  = true;
        $course_id          = (preg_match('/^[1-9]+[0-9]*$/', $this->input->post('course_id')) == true) ? $this->input->post('course_id') : false;
        $p_remove           = (preg_match('/^[1-9]+[0-9]*$/',$this->input->post('p_remove')) == true) ? $this->input->post('p_remove') : false;
        if($course_id)
        {
            $course             = $this->Course_model->course(array(
                                            'id' => $course_id,
                                            'select' => 'id, cb_title'
                                        ));
            if (!$course) {
                $response['error']      = true;
                $response['message']    = lang('course_not_found');
                echo json_encode($response);
                exit;
            }
            $save                   = array();
            $save['id']             = $course_id;
            $save['action_by']      = $this->auth->get_current_admin('id');
            $save['updated_date']   = date('Y-m-d H:i:s');
            $save['cb_deleted']     = '1';
            $response['message']    = lang('restore_course_failed');
            if($p_remove != $course_id)
            {
                if ($this->Course_model->save($save)) {
                    $response['error']          = false;
                    $response['message']        = lang('course_delete_success');
                    $filter_param               = array();
                    $filter_param['course_id']  = $course_id;
                    $filter_param['match']      = true;
                    $matched_bundles            = $this->Bundle_model->get_all_match($filter_param);
                    if(!empty($matched_bundles))
                    {
                        foreach($matched_bundles as $bundle_key => $bundle)
                        {
                            $bundle_id              = $bundle['id'];
                            $enrolled_courses       = json_decode($bundle['c_courses'],true);
                            $save_userdata          = array();
                            
                            foreach ($enrolled_courses as $enrolled_course)
                            {
                                if(!in_array($course_id, $enrolled_course))
                                {
                                    array_push($save_userdata,$enrolled_course);
                                }
                            }
                            $enrolled_courses       = count($save_userdata);
                            $save                   = array();
                            $filter                 = array();
                            if($enrolled_courses < 1)
                            {
                                $save['c_status']   = '0';
                            }
                            $save['c_courses']      = json_encode($save_userdata);
                            $filter['id']           = $bundle_id;
                            $filter['update']       = true;

                            $result                 = $this->Bundle_model->save($save,$filter);
                        }
                    }
                    //To delete mobile api get subscriptions memcache
                    $course_param                   = array();
                    $course_param['id']             = $course_id;
                    $this->reset_mobile_subscriptions($course_param);
                }
                $response['course']     = $this->Course_model->course(array(
                                                        'id' => $course_id,
                                                        'select' => 'course_basics.id, cb_title, cb_code, cb_category, cb_status, cb_approved, cb_deleted, cb_access_validity,cb_validity,cb_validity_date'
                                                    ));
            }

            
            $user_data              = array();
            $user_data['user_id']   = $this->__loggedInUser['id'];
            $user_data['username']  = $this->__loggedInUser['us_name'];
            $user_data['useremail'] = $this->__loggedInUser['us_email'];
            $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
            
            $message_template                   = array();
            $message_template['username']       = $this->__loggedInUser['us_name'];
            $message_template['course_name']    = $course['cb_title'];
            
            $triggered_activity                 = 'course_deleted';
            
            if($p_remove == $course_id)
            {
                $this->load->library(array('archive'));
                $archive_param                  = array();
                $archive_param['course_ids']    = array($course_id);
                if(!empty($course_id))
                {
                    $this->archive->archive_course($archive_param);
                    $this->Course_model->delete($course_id, true);
                    $response['message']        = 'Course details permanently deleted!';
                    $response['error']          = false;
                }
                else
                {
                    $response['message']        = 'No data to archieve!';
                }
                $triggered_activity             = 'course_permanently_deleted';
            }

            log_activity($triggered_activity, $user_data, $message_template); 
            log_activity($triggered_activity, $user_data, $message_template); 
            log_activity($triggered_activity, $user_data, $message_template); 
            $this->invalidate_course(array('course_id' => $course_id));
            $this->invalidate_course();
        }
        else
        {
            $response['message'] = lang('error_unknown');
        }
        
        echo json_encode($response);
    }

    public function delete_check()
    {
        $subscription_bundle    = array();
        $response               = array();
        $response['success']    = true;

        $course_id  = (preg_match('/^[1-9]+[0-9]*$/', $this->input->post('course_id')) == true) ? $this->input->post('course_id') : false;
        if($course_id)
        {
            $course     = $this->Course_model->course(array(
                'id' => $course_id,
                'select' => 'id, cb_title'
            ));

            if (!$course) {
                $response['success'] = false;
                $response['message'] = lang('course_not_found');
                echo json_encode($response);
                exit;
            }
            $objects                = array();
            $objects['key']         = 'all_bundles';
            $callback               = 'all_bundles';
            $all_bundles            = $this->memcache->get($objects, $callback,array()); 
            if(!empty($all_bundles))
            {
                $bundle_names       = array();
                foreach($all_bundles as $bundle)
                {
                    $error_count            = 0;
                    $courses_list           = json_decode($bundle['c_courses'],true);
                    $included_course_ids    = (is_array($courses_list))?array_column($courses_list, 'id'):array();
                    if(!empty($included_course_ids))
                    {
                        if(in_array($course_id,$included_course_ids))
                        {
                            $error_count++;
                        }
                    }
                    if($error_count != 0)
                    {
                        $bundle_names[] = $bundle['c_title'];
                    }
                }
                if(!empty($bundle_names))
                {
                    $subscription_bundle = $bundle_names;
                }
            }

            /* Subscriobers alert */
            $subscription_count = $this->Course_model->enrolled(array('course_id' => $course_id, 'count' => true));
            $subscription_alert_text = '';
            if ($subscription_count > 0) {
            $student_text               = ($subscription_count == 1) ? ' Student' : ' Students';
            $subscription_alert_text    = $subscription_count . $student_text;
            }
            // echo $subscription_alert;die;
            /* End Subscriobers alert */
            $response['student_count']  = $subscription_alert_text;
            $response['bundle']         = $subscription_bundle;
        }
        else
        {
            $response['success'] = false;
            $response['message'] = lang('error_unknown');
        }
        echo json_encode($response);
    }
    public function catalogs()
    {
        $data = array();
        $data['catalogs'] = $this->Catalog_model->catalogs(array('direction' => 'DESC', 'status' => '1'));
        echo json_encode($data);
    }
    public function catalog_courses()
    {
        $data = array();
        $catalog_id = $this->input->post('catalog_id');
        $data['catalog'] = $this->Catalog_model->catalog(array('id' => $catalog_id));
        $data['catalog_courses'] = $this->Catalog_model->catalog_courses(array('direction' => 'ASC', 'catalog_id' => $catalog_id));
        echo json_encode($data);
    }
    public function save_catalogs_courses()
    {
        $save = array();
        $save['id'] = $this->input->post('catalog_id');
        if (!$save['id']) {
            $save['c_title'] = $this->input->post('catalog_name');
        }
        $save['c_price'] = $this->input->post('catalog_price');
        $save['c_discount'] = $this->input->post('catalog_price_discount');
        $course_ids = json_decode($this->input->post('course_ids'));
        $course_ids = implode(',', $course_ids);
        $save['c_courses'] = $course_ids;
        $save['c_account_id'] = config_item('id');
        echo $this->Catalog_model->save($save);
    }
    public function create_course()
    {   
       
        $active_academic_year = $this->Course_model->get_active_academic_year();    
        $academic_year_id     = $active_academic_year['id'];
        $academic_year_code   = $active_academic_year['ay_year_code'];
        $response = array();
        $response['error'] = false;
        $response['message'] = lang('course_created_success');
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('', '</br>');
        $this->form_validation->set_rules('course_name', 'Course Name', 'required|callback_name_check[0]');
        $this->form_validation->set_rules('course_code', 'Course Code', 'required|max_length[5]|callback_code_check[0]');
        
        if ($this->form_validation->run() == FALSE)
        {
            $response['error']   = true;
            $response['message'] = validation_errors();
        }
        else
        {
            $course_name    = $this->input->post('course_name');
            $course_name    = trim($course_name);
            $course_code    = $this->input->post('course_code');
            $this->load->helper('text');
            $slug           = $course_name;
            $slug           = url_title(convert_accented_characters($slug), 'dash', true);
            $this->load->model('Routes_model');
            $slug           = $this->Routes_model->validate_slug($slug);
            $route['slug']  = $slug;
            $route_id       = $this->Routes_model->save($route);
            $save                           = array();
            $save['id']                     = false;
            $save['cb_title']               = $course_name;
            $save['cb_code']                = $course_code;
            $save['action_id']              = '0';
            $save['action_by']              = $this->auth->get_current_admin('id');
            $save['cb_route_id']            = $route_id;
            $save['cb_created_by']          = $this->auth->get_current_admin('id');
            $save['cb_slug']                = $slug;
            $save['cb_account_id']          = $this->config->item('id');
            $save['cb_is_free']             = '1';
            $save['cb_status']              = '0';
            $save['cb_self_enroll_date']    = null;
            $course_id              = $this->Course_model->save($save);
            $response['id']         = $course_id;
            $route['id']            = $route_id;
            $route['slug']          = $slug;
            $route['route']         = 'course/view/' . $course_id;
            $route['r_account_id']  = $this->config->item('id');
            $route['r_item_type']   = 'course';
            $route['r_item_id']     = $course_id;
            $this->Routes_model->save($route);
            
            $user_data              = array();
            $user_data['user_id']   = $this->__loggedInUser['id'];
            $user_data['username']  = $this->__loggedInUser['us_name'];
            $user_data['useremail']  = $this->__loggedInUser['us_email'];
            $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;

            $message_template                   = array();
            $message_template['username']       = $this->__loggedInUser['us_name'];
            $message_template['course_name']    = $course_name;
            $triggered_activity                 = 'course_created';

            log_activity($triggered_activity, $user_data, $message_template);

            // IF the course created by a user with not full course access, assign the user to course
            if($this->__loggedInUser['rl_full_course'] != 1) {
                $this->load->model(array('Tutor_model'));
                $save                   = array();
                $save['ct_course_id']   = $course_id;
                $save['ct_tutor_id']    = $this->auth->get_current_admin('id');
                $this->Tutor_model->save($save);
            }
            $this->invalidate_course();

            //Register course to course consolidated report table
            $objects            = array();
            $objects['key']     = 'institutes'; 
            $callback           = 'institutes';
            $institutes         = $this->memcache->get($objects, $callback);
            if(!empty($institutes))
            {
                $consolidations = array();
                foreach($institutes as $institute)
                {
                    $consolidations[] = array( 
                                                'id' => false,
                                                'ccr_course_id' => $course_id,
                                                'ccr_institute_id' => $institute['id'],
                                                'ccr_total_enrolled' => 0,
                                                'ccr_total_completed' => 0,
                                                'ccr_academic_year_id' => $academic_year_id,
                                                'ccr_academic_year_code' => $academic_year_code,
                                                'ccr_account_id'            => config_item('id')
                                            );
                }
                $this->Course_model->save_consolidation($consolidations);
            }
            //End

        }

        echo json_encode($response);
        exit;
    }
    function code_check($code, $id)
    {
        $param              = array();
        $param['exclude_id']        = ($id != '0')? $id : false;
        $param['code']              = $code;
        $param['not_deleted']       = true;
        $param['select']    = 'cb_code, cb_title';
        
        $code_available = $this->Course_model->course($param);
        // echo $this->db->last_query();exit;
        if (!empty($code_available))
        {
            $this->form_validation->set_message('code_check', 'The Course code <b>'.$code.'</b> is already used by <b>'.((strlen($code_available['cb_title'])>30)?substr($code_available['cb_title'], 0, 25).'...':$code_available['cb_title']).'</b>');
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }
    function name_check($name, $id)
    {
        $param              = array();
        $param['exclude_id']        = ($id != '0')? $id : false;
        $param['name']              = $name;
        $param['not_deleted']       = true;
        $param['select']    = 'cb_code, cb_title';
        
        $name_available     = $this->Course_model->course($param);
        if (!empty($name_available))
        {
            $this->form_validation->set_message('name_check', 'The Course name <b>'.$name.'</b> is already used </b>');
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    function ajax_name_check()
    {
        $id                         = $this->input->post('c_id');
        $name                       = $this->input->post('cb_title');
        $param                      = array();
        $param['exclude_id']        = ($id != '0')? $id : false;
        $param['name']              = $name;
        $param['select']            = 'id, cb_code, cb_title';
        $param['limit']             = '1'; //print_r($param); die;
        $name_available             = $this->Course_model->course($param);
        
        if (!empty($name_available))
        {   //print_r($name_available); die;
            $name_available['error'] = true;
            echo json_encode($name_available);
            return FALSE;
        }
        else
        {
            $name_available = array();
            $name_available['error'] = false;
            echo json_encode($name_available);
            return TRUE;
        }
    }
    
    public function basic($id = false)
    {
        
        if (!$id) {
            $this->session->set_flashdata('message', lang('course_not_found'));
            redirect($this->config->item('admin_folder') . '/course');
        }
        if($this->__loggedInUser['rl_full_course'] != '1')
        {
            $param['current_logged_user']   = $this->__loggedInUser['id'];
            $access_course_list             = $this->Course_model->course_permission($param);
            $course_id_list                 = array_column($access_course_list, 'ct_course_id');
            if (in_array($id, $course_id_list))
            {
                $continue                   = true;
            }else{
                $this->session->set_flashdata('message', lang('course_not_found'));
                redirect($this->config->item('admin_folder') . '/course');
                exit;
            }
        } 
        /*============= Redirect if Not Valid Entry =============== */
        $course_param       = $this->__role_query_filter;
        $course_param['id'] = $id;
        $course             = $this->Course_model->course($course_param);
        // echo $this->db->last_query();exit;
        //echo '<pre>';    print_r($course); die;
        if (!$course) {
            $this->session->set_flashdata('message', lang('course_not_found'));
            redirect($this->config->item('admin_folder') . '/course');
        }
        if ($course['cb_deleted'] == '1') {
            redirect($this->config->item('admin_folder') . '/course');
        }

        if($course['cb_discussion_instruction']=='')
        {
            $save                               = array();
            $save['id']                         = $id;
            $save['cb_discussion_instruction']  = $this->get_instruction();
            $this->Course_model->save($save);
            $this->memcache->delete('course_' . $id);
            $this->memcache->delete('course_mob'.$id);
            $this->invalidate_course(array('course_id'=>$id));
        }

        $content_editor = $this->auth->get_current_user_session('content_editor');
        if (isset($content_editor) && ($content_editor)) {
            redirect($this->config->item('admin_folder') . '/coursebuilder/home/' . $id);
        }
        $data               = array();
        $data['course']     = $course;
        $breadcrumb         = array();
        $breadcrumb[]       = array('label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>');
        $breadcrumb[]       = array('label' => lang('trainings'), 'link' => admin_url('course'), 'active' => 'active', 'icon' => '');
        $breadcrumb[]       = array('label' => $course['cb_title'], 'link' => admin_url('course/basic/' . $course['id']), 'active' => 'active', 'icon' => '');
        $breadcrumb[]       = array('label' => 'Overview', 'link' => '', 'active' => 'active', 'icon' => '');
        $data['breadcrumb'] = $breadcrumb;
        $data['title']      = $course['cb_title'];
        $limit_discussion   = 2;
        $parent_id          = 0;
        $data['course_comments']    = $this->Course_model->get_course_comments_user(array('course_id' => $course['id'], 'limit' => $limit_discussion, 'parent_id' => $parent_id));
        $data['total_enrolled']     = $this->Course_model->enrolled(array('course_id' => $course['id'], 'count' => true));
        $data['total_completed']     = $this->Course_model->enrolled(array('course_id' => $course['id'], 'count' => true,'filter'=>'completed'));
        //echo $this->db->last_query();die;
        $data['assigned_tutors']    = $this->Course_model->assigned_tutors(array('course_id' => $course['id']));
        $data['quiz']               = $course['cb_assessment_count'];

        $data['active_learners']    = $data['total_enrolled'];//$course['cb_total_enrolled_users'];
        $data['completed']          = $data['total_completed'];//$course['cb_course_completed_count'];

        if($this->__loggedInUser['us_role_id']!='1'){

            $filter_param                   = array();
            $filter_param['institute_id']   = $this->__loggedInUser['us_institute_id'];
            $filter_param['course_id']      = $course['id'];
            $filter_param['limit']          = true;
            $filter_param['select']         = 'ccr_total_enrolled,ccr_total_completed';
            $consolidated_report            = $this->Course_model->consolidated_report($filter_param);
            $data['active_learners']        = $consolidated_report['ccr_total_enrolled'];
            $data['completed']              = $consolidated_report['ccr_total_completed'];
        }
       
        $data['image_status']       = $course['cb_image'];
        $data['status']             = $course['cb_status'];
        $data['lecture_status']     = $this->Course_model->get_course_lecture_count(array('course_id' => $id));
        
        // $data['active_learners']    = $this->Course_model->enrolled(array('course_id' => $course['id'], 'count' => true, 'approved' => '1', 'certificate_issued' => '0', 'expired' => '0'));
        // $data['sections']           = $this->Course_model->sections(array('course_id' => $course['id'], 'count' => true, 'status' => 1));
        // $data['lectures']           = $this->Course_model->lectures(array('course_id' => $course['id'], 'count' => true, 'status' => 1));
        // $data['assesments']         = $this->Course_model->lectures(array('course_id' => $course['id'], 'count' => true, 'status' => 1, 'lecture_type' => $this->__lecture_type_keys_array['assesment'], 'not_deleted' => true));
        // $data['certificate_issued'] = $this->Course_model->enrolled(array('course_id' => $course['id'], 'count' => true, 'certificate_issued' => '1'));
   
        $data['live_classes']       = $this->Course_model->live_lecture(array('course_id' => $course['id'], 'certificate_issued' => true, 'upcommimg' => true, 'status' => 1));
        $data['admin_details']      = $this->auth->get_current_user_session('admin');
        //echo '<pre>';    print_r($data['active_learners']); die;
        $data['assign_faculty_privilege'] = $this->assign_faculty_privilege;
        $this->load->view($this->config->item('admin_folder') . '/training_content_basics', $data);
    }

    public function users($id = false, $filter = false)
    {
        if (!$id) {
            $this->session->set_flashdata('message', lang('course_not_found'));
            redirect($this->config->item('admin_folder') . '/course');
        }
        
        $continue=false;
        if($this->__loggedInUser['rl_full_course'] != '1')
        {
            $param['current_logged_user']   = $this->__loggedInUser['id'];
            $access_course_list             = $this->Course_model->course_permission($param);
            $course_id_list                 = array_column($access_course_list, 'ct_course_id');
            if (!in_array($id, $course_id_list))
            {
                $this->session->set_flashdata('message', lang('course_not_found'));
                redirect($this->config->item('admin_folder') . '/course');
                exit;
            }
        }   
        if(in_array('1', $this->student_enroll_privilege)){
            $course_param           = $this->__role_query_filter;
            $course_param['id']     = $id;
            $course_param['select'] = 'id, cb_title, cb_code, cb_groups';
            $course                 = $this->Course_model->course($course_param);
        
            if (!$course) {
                $this->session->set_flashdata('message', lang('course_not_found'));
                redirect($this->config->item('admin_folder') . '/course');
            }
            $data           = array();
            $data['course'] = $course;
            $breadcrumb     = array();
            $breadcrumb[]   = array('label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>');
            $breadcrumb[]   = array('label' => lang('trainings'), 'link' => admin_url('course'), 'active' => 'active', 'icon' => '');
            $breadcrumb[]   = array('label' => $course['cb_title'], 'link' => admin_url('course/basic/' . $course['id']), 'active' => 'active', 'icon' => '');
            $breadcrumb[]   = array('label' => 'Students', 'link' => '', 'active' => 'active', 'icon' => '');
            $data['breadcrumb']         = $breadcrumb;
            $data['title']              = $course['cb_title'];
            $data['show_load_button']   = false;
            $data['limit']              = $this->__limit;
            // $data['assigned_tutors'] = $this->Course_model->assigned_tutors(array('course_id' => $course['id']));
            $param                      = $this->__role_query_filter;
            $param['course_id']         = $course['id'];
            $param['select']            = 'course_subscription.*, users.us_name, users.us_email, users.us_phone, users.us_institute_code';
            $param['filter']            = ($this->input->get('filter') != null)? $this->input->get('filter') : 'active';
            if(!isset($this->__role_query_filter['institute_id'])){
                $param['institute_id']   = ($this->input->get('institute_id') != null)? $this->input->get('institute_id'): '';
            }
            $param['branch_id']         = ($this->input->get('branch_id') != null)? $this->input->get('branch_id'): '';
            $param['batch_id']          = ($this->input->get('batch_id') != null)? $this->input->get('batch_id') : '';
            if($this->input->get('keyword') != null)
            {
                $keyword_arr        = explode('-', $this->input->get('keyword'));
                $keyword            = implode(' ',$keyword_arr);
                $param['keyword']   = trim($keyword);
            }
            $param['count']         = true;
            $data['total_enrolled'] = $this->Course_model->enrolled($param);
            unset($param['count']);
            $param['limit']         = $this->__limit;
            $offset                   = isset($_GET['offset'])?$_GET['offset']:0;
            //processing pagination
            $page             = $offset;
            if($page===NULL||$page<=0)
            {
                $page         = 1;
            }
            $page             = ($page - 1)* $this->__limit;
            //end
            $param['offset']        = $page;
            $data['enrolled_users'] = $this->Course_model->enrolled($param);
            // echo $this->db->last_query();exit;
             //echo "<pre>"; print_r($data['enrolled_users']); die();
            if($data['total_enrolled'] > $this->__limit)
            {
                $data['show_load_button']   = true;            
            }
            $data['filter'] = $filter;
            
            $data['admin_details'] = $this->__loggedInUser;
            //Read institutes form memcached.
            $objects        = array();
            $objects['key'] = 'institutes';
            $callback       = 'institutes';
            $institutes     = $this->memcache->get($objects, $callback, array()); 
            //Read branches form memcached.
            $objects        = array();
            $objects['key'] = 'branches';
            $callback       = 'branches';
            $branches       = $this->memcache->get($objects, $callback, array()); 
            $data['branches']   = $branches;
            if(!isset($this->__role_query_filter['institute_id'])){
                $data['institutes'] = $institutes;
            }
            $data['batches']    = array();
            if(isset($this->__role_query_filter['institute_id']) || $param['institute_id'] != '')
            {
                $this->load->model('Group_model');
                $group_param                  = array();
                $group_param['institute_id']  = isset($this->__role_query_filter['institute_id'])? $this->__role_query_filter['institute_id'] : $param['institute_id'];
                $group_param['select']        = 'id, CONCAT(gp_institute_code," - ",gp_year," - ",gp_name) as batch_name';
                $data['batches']        = $this->Group_model->groups($group_param);
            }
            $data['assign_faculty_privilege'] = $this->assign_faculty_privilege;
            // echo '<pre>';    print_r($data); die;
            $this->load->view($this->config->item('admin_folder') . '/training_content_users', $data);
        }  else{
            $this->session->set_flashdata('message', lang('course_not_found'));
            redirect($this->config->item('admin_folder') . '/course/basic/'.$id);
            exit;
        }
        
    }
    function enroll_users($course_id = false) 
    {
        if(!$course_id) {
            redirect(admin_url('course'));
        }
        $limit      = 100;
        $data       = array();
        $data['course_id'] = $course_id;
        $data['show_load_button']   = false;
        $param                  = $this->__role_query_filter;
        $param['course_id']     = $course_id;
        $param['role_id']       = '2';
        $param['not_subscribed'] = true;
        $param['not_deleted']   = true;
        $param['order_by']      = 'users.us_name';
        $param['direction']     = 'ASC';
        $param['count']         = true;
        $data['total_users']    = $this->User_model->users($param);
        unset($param['count']);
        $param['limit']         = $limit;
        $param['offset']        = 0;
        $param['select']        = 'users.id user_id, users.us_name, us_phone, us_email, us_institute_id, us_branch, us_branch_code, us_institute_code, us_status';
        $data['users']          = $this->User_model->users($param);
        
        $data['limit']          = $limit;
        //Read institutes form memcached.
        $objects        = array();
        $objects['key'] = 'institutes';
        $callback       = 'institutes';
        $institutes     = $this->memcache->get($objects, $callback, array()); 
       
        if(!isset($this->__role_query_filter['institute_id'])){
            $data['institutes'] = $institutes;
        }       
        if($data['total_users'] > $limit){
            $data['show_load_button']   = true;
        }
        // echo "<pre>";
        // print_r($data); die();
        $this->load->view($this->config->item('admin_folder').'/course_enroll_student',$data);
    }

    function enroll_users_json()
    {
        $limit                      = 200;
        $data                       = array();
        $data['show_load_button']   = false;
        $user_param                 = $this->__role_query_filter;
        
        $limit            = $limit;
        $data['limit']    = $limit;
        $offset           = $this->input->post('offset');
        $page             = $offset;
        if($page===NULL||$page<=0)
        {
            $page         = 1;
        }
        $page             = ($page - 1)* $limit;
        $user_param['order_by']       = 'us_name';
        $user_param['direction']      = 'ASC';
        $user_param['course_id']      = $this->input->post('course_id');
        if(!isset($this->__role_query_filter['institute_id'])){
            $user_param['institute_id']   = $this->input->post('institute_id');
        } else {
            $user_param['institute_id']   = $this->__role_query_filter['institute_id'];
        }
        $user_param['branch']         = $this->input->post('branch_id');
        $user_param['keyword']        = $this->input->post('keyword');
        $user_param['keyword']        = trim($user_param['keyword']);
        $user_param['not_deleted']    = true;
        $user_param['not_subscribed'] = true;
        $user_param['role_id']        = '2';
        $user_param['count']          = true;
        
        $total_users                  = $this->User_model->users($user_param);
        $data['total_users']          = $total_users;       
        unset($user_param['count']);
        $user_param['limit']          = $this->input->post('limit');
        $user_param['offset']         = $page;
        $user_param['select']         = 'users.id user_id, users.us_name, us_phone, us_email, us_institute_id, us_branch, us_branch_code, us_institute_code, us_status';
        if($total_users > ($limit*$offset))
        {
            $data['show_load_button']  = true;
        }
        $data['users']                = $this->User_model->users($user_param);
        echo json_encode($data);
    }
  
    public function enroll_group_to_course(){
        
        $groups_ids                 = $this->input->post('group_ids');
        $course_id                  = (preg_match('/^[1-9]+[0-9]*$/', $this->input->post('course_id')) == true )? $this->input->post('course_id') : false;
        $groups_list                = json_decode($groups_ids);
        $notify_to                  = array();
        if(!empty($groups_list) && $course_id !== false)
        {
            $course_param               = array();
            $course_param['select']     = 'id,cb_groups,cb_code,cb_title,cb_price,cb_discount,cb_tax_method,cb_is_free,cb_access_validity,cb_validity,cb_validity_date';
            $course_param['course_id']  = $course_id;
            $course_param['limit']      = 1;
            $course                     = $this->Course_model->course_new($course_param);

            $course_groups              = $course['cb_groups'];
            $course_groups              = ($course_groups!='') ? $course_groups :'';
            $course_groups              = explode(',', $course_groups);
            $groups                     = array_merge($course_groups,$groups_list);
            $groups                     = array_unique(array_filter($groups));
            $group_string               = implode(",",$groups);
            $groups_string               = implode(",",$groups_list);

            $save_userdata              = array();
            $save_param                 = array();
            $payment_data               = array();
            $save_param['id']           = $course_id;
            $save_param['cb_groups']    = $group_string;
            $result                     = $this->Course_model->save($save_param);
            
            if($result)
            {
                $email_content               = array();
                foreach($groups_list as $group_id){
                    $group_param             = array();
                    $group_param['select']   = 'users.id,users.us_name,us_phone,users.us_email,users.us_email_verified,users.us_groups';
                    $group_param['group_id'] = $group_id;
                    $users[]                 = $this->Group_model->group_users($group_param);
                }
            
                $group_users    = array_reduce($users, 'array_merge', array());
                $users          = array_intersect_key($group_users , array_unique( array_map('serialize' , $group_users) ) );
                
                $filter_param               = array();
                $filter_param['select']     = 'id,cs_user_id';
                $filter_param['course_id']  = $course_id;
                $enrolled_users             = $this->Course_model->course_enrolled($filter_param);
                $enrolled_users_id          = array_column($enrolled_users,'cs_user_id');
                
                foreach($users as $user)
                {
                
                    if(!in_array($user['id'],$enrolled_users_id)){
                        $notify_to[]                        = $user['id'];
                        $save                               = array();
                        $save['id']                         = false;
                        $save['cs_course_id']               = $course_id;
                        $save['cs_user_id']                 = $user['id'];
                        $save['cs_subscription_date']       = date('Y-m-d H:i:s');
                        $save['cs_start_date']              = date('Y-m-d');
                        $save['cs_course_validity_status']  = $course['cb_access_validity'];
                        $save['cs_user_groups']             = $user['us_groups'];
                        if ($course['cb_access_validity'] == 2) {
                            $course_enddate = $course['cb_validity_date'];
                        } else if ($course['cb_access_validity'] == 0) {
                            $course_enddate = date('Y-m-d', strtotime('+3000 days'));
                        } else {
                            $duration = ($course['cb_validity']) ? $course['cb_validity']-1 : 0;
                            $course_enddate = date('Y-m-d', strtotime('+' . $duration . ' days'));
                        }
                                            
                        $save['cs_end_date'] = $course_enddate;
                        $save['cs_approved'] = '1';
                        $save['action_by']   = $this->__loggedInUser['id'];
                        $save['action_id']   = '1';  
                        $save_userdata[]     = $save;

                        
                        $user_name           = $user['us_name'];
                        $verified_email      = $user['us_email'];
                        $privilage_user      = $this->__loggedInUser['us_name'];
                        $course_names        = $course['cb_title'];
                        if ($user['us_email_verified']=='1' && $course_names!='') 
                        {
                            $email_param                = array();
                            // $email_param['email_code']  = 'student_to_course';
                            $email_param['email']   = $verified_email;
                            $email_param['contents']    = array(
                                'username'          => $user_name
                                , 'course_name'     => $course_names
                                , 'privilage_user'  => $privilage_user
                                , 'date'            => date('Y-M-d h:i:sa')
                                , 'site_url'        => site_url()
                                , 'site_name'       => config_item('site_name'),
                            );
                            array_push($email_content, $email_param);
                        }
                        $this->memcache->delete('enrolled_'.$user['id']);
                        $this->memcache->delete('mobile_enrolled_'.$user['id']);
                    }
                    
                    /* Payment Details */
                    $user_details                               = array();
                    $user_details['name']                       = $user['us_name'];
                    $user_details['email']                      = $user['us_email'];
                    $user_details['phone']                      = $user['us_phone'];

                    $payment_param                              = array();
                    $payment_param['ph_user_id']                = $user['id'];
                    $payment_param['ph_user_details']           = json_encode($user_details);
                    $payment_param['ph_item_id']                = $course_id;
                    $payment_param['ph_item_type']              = '1';
                    $payment_param['ph_item_code']              = $course['cb_code'];
                    $payment_param['ph_item_name']              = $course['cb_title'];
                    $payment_param['ph_item_base_price']        = $course['cb_price'];
                    $payment_param['ph_item_discount_price']    = $course['cb_discount'];
                    $payment_param['ph_tax_type']               = $course['cb_tax_method'];

                    $course_price                               = ($course['cb_discount']!=0)?$course['cb_discount']:$course['cb_price'];
                    
                    if($course['cb_is_free'] == '1')
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
                    

                    if($course['cb_tax_method'] == '1')
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
                    unset($course['cb_groups']);
                    $payment_param['ph_item_other_details']     = json_encode($course);
                    $payment_data[]                             = $payment_param;

                    //ends payment history

                }
                //echo '<pre>'; print_r($payment_data);die;
                if(!empty($save_userdata)){
                    $this->User_model->subscription_save($save_userdata);
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

                $response = array();
                $response['success'] = 'true';
                $batch_text          = (sizeof($groups_list) > 1 )? 'Batches' : 'Batch';
                $response['message'] = $batch_text . ' enrolled to course';
                
                //Notification
                $this->load->library('Notifier');
                $this->notifier->push(
                    array(
                        'action_code' => 'course_subscribed',
                        'assets' => array('course_name' => $course['cb_title'],'course_id' => $course['id']),
                        'target' => $course['id'],
                        'individual' => true,
                        'push_to' => $notify_to
                    )
                );
                if(!empty($email_content)){
                    $this->process_bulk_mail($email_content,'student_to_course');
                }
                //End notification

                $user_data                          = array();
                $user_data['user_id']               = $this->__loggedInUser['id'];
                $user_data['username']              = $this->__loggedInUser['us_name'];
                $user_data['useremail']             = $this->__loggedInUser['us_email'];
                $user_data['user_type']             = $this->__loggedInUser['us_role_id']; ;
                
                $message_template                   = array();
                $message_template['username']       = $this->__loggedInUser['us_name'];
                $message_template['course_name']    =  $course['cb_title'];
                $message_template['group_string']   =  $groups_string; 

                $triggered_activity                 = 'batch_enrolled_to_course';
                log_activity($triggered_activity, $user_data, $message_template); 

                echo json_encode($response);
            }else{
                $response = array();
                $response['error'] = 'false';
                $response['message'] = 'Failed to enroll';
                echo json_encode($response); 
            }
        }
        else
        {
            $response = array();
            $response['error'] = false;
            $response['message'] = lang('course_not_found');
            echo json_encode($response);
        }
    }
    
    public function enroll_students() 
    {
        $user_ids               = $this->input->post('user_ids');
        $course_id              = (preg_match('/^[1-9]+[0-9]*$/', $this->input->post('course_id')) == true)? $this->input->post('course_id') : false;
        
        $users_list             = json_decode($user_ids);
        if($course_id !== false && !empty($users_list))
        {

            $notification_ids       = array();

            $course_param           = array();
            $course_param['select'] = 'id,cb_code,cb_title,cb_is_free,cb_access_validity,cb_validity,cb_validity_date,cb_price,cb_discount,cb_tax_method';  
            $course_param['id']     = $course_id;
            $course                 = $this->Course_model->course($course_param);
            //print_r($course);die;
            $payment_data           = array();

            $user_param             = array();
            
            $us_param = array('user_ids' => $users_list, 'course_id' => $course_id, 'select' => 'course_subscription.cs_user_id');
            $users_subscribed = $this->Course_model->subscription_details($us_param);
            if(isset($users_subscribed[0]['cs_user_id']))
            {
                for($u = 0; $u < count($users_subscribed); $u++)
                {
                    if (($key = array_search($users_subscribed[$u]['cs_user_id'],$users_list)) !== false) {
                        
                        unset($users_list[$key]);
                    }
                }
            }

            $user_param['user_ids'] = $users_list;
            //$user_param['verified'] = true;
            $user_param['select']   = 'users.id,users.us_name,users.us_email,users.us_groups,users.us_email_verified,us_phone';
            $subscribed_users       = $this->User_model->users($user_param);
            
            $users_subscribed       = array();
            $users_groups           = array();
            foreach($subscribed_users as $subscribed_user)
            {
                if($subscribed_user['us_email_verified']=='1'){
                    $users_subscribed[] = $subscribed_user;
                } 
                $users_groups[$subscribed_user['id']] = $subscribed_user['us_groups'];

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
                $payment_param['ph_item_code']              = $course['cb_code'];
                $payment_param['ph_item_name']              = $course['cb_title'];
                $payment_param['ph_item_base_price']        = $course['cb_price'];
                $payment_param['ph_item_discount_price']    = $course['cb_discount'];
                $payment_param['ph_tax_type']               = $course['cb_tax_method'];

                $course_price                               = ($course['cb_discount']!=0)?$course['cb_discount']:$course['cb_price'];
                
                if($course['cb_is_free'] == '1')
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
                

                if($course['cb_tax_method'] == '1')
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
                $payment_param['ph_item_other_details']     = json_encode($course);
                $payment_data[]                             = $payment_param;
            }
        //print_r($payment_data);die;
            if(!empty($users_list))
            {
                $email_content                  = array();
                $save_userdata                  = array();
                $course_name                    = $course['cb_title'];
                foreach ($users_list as $user_id)
                {
                    $save                               = array();
                    $save['id']                         = false;
                    $save['cs_course_id']               = $course_id;
                    $save['cs_user_id']                 = $user_id;
                    $save['cs_subscription_date']       = date('Y-m-d H:i:s');
                    $save['cs_start_date']              = date('Y-m-d');
                    $save['cs_course_validity_status']  = $course['cb_access_validity'];
                    $save['cs_user_groups']             = (isset($users_groups[$user_id]))?$users_groups[$user_id]:'';
                    $notification_ids[]                 = $user_id;
                    if ($course['cb_access_validity'] == 2) {
                        $course_enddate = $course['cb_validity_date'];
                    } else if ($course['cb_access_validity'] == 0) {
                        $course_enddate = date('Y-m-d', strtotime('+3000 days'));
                    } else {
                        $duration = ($course['cb_validity']) ? $course['cb_validity']-1 : 0;
                        $course_enddate = date('Y-m-d', strtotime('+' . $duration . ' days'));
                    }
                                        
                    $save['cs_end_date']            = $course_enddate;
                    $save['cs_approved']            = '1';
                    $save['action_by']              = $this->__loggedInUser['id'];
                    $save['action_id']              = '1';  
                    $save_userdata[]                = $save;     
                    //Invalidation 
                    $this->invalidate_subscription(array('user_id' => $save['cs_user_id'],'course_id'=>$save['cs_course_id']));
                    //End invalidation
                }
                
                if($save_userdata)
                {
                    $this->User_model->subscription_save($save_userdata);
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
                
                if(!empty($users_subscribed)){
                    
                    foreach($users_subscribed as $user_subscribed){
                        // echo "<pre>";print_r($user_subscribed);exit;
                        $user_name              = $user_subscribed['us_name'];
                        $verified_email         = $user_subscribed['us_email'];
                        $privilage_user         = $this->__loggedInUser['us_name'];
                        $course_names           = $course_name;
                        $update_subscription                      = array();
                        $update_subscription['cs_user_groups']    = $user_subscribed['us_groups'];
                        $update_conditions                        = array();
                        $update_conditions['course_id']           = $course_id;
                        $update_conditions['user_id']             = $user_id;
                        $update_conditions['update']              = true;
                        
                        $this->User_model->save_subscription_new($update_subscription,$update_conditions);
                        if (!empty($verified_email) && $course_names!='') 
                        {
                            $email_param                = array();
                            // $email_param['email_code']  = 'student_to_course';
                            $email_param['email']   = $verified_email;
                            $email_param['contents']= array(
                                'username'        => $user_name
                                , 'course_name'     => $course_names
                                , 'privilage_user'  => $privilage_user
                                , 'date'            => date('Y-M-d h:i:sa')
                                , 'site_url'        => site_url()
                                , 'site_name'       => config_item('site_name'),
                            );
                            array_push($email_content, $email_param);
                        }
                    }
                }
                if(!empty($email_content)){
                    $this->process_bulk_mail($email_content,'student_to_course');
                }
                
                //Push notification
                $this->load->library('Notifier');
                $this->notifier->push(
                    array(
                        'action_code' => 'course_subscribed',
                        'assets' => array('course_name' => $course_name,'student_name' => isset($users_subscribed[0]['us_name'])?$users_subscribed[0]['us_name']:'','course_id' => $course_id),
                        'target' => $course_id,
                        'individual' => true,
                        'push_to' => $notification_ids
                    )
                );
                //End notification 
                
                $user_data              = array();
                $user_data['user_id']   = $this->__loggedInUser['id'];
                $user_data['username']  = $this->__loggedInUser['us_name'];
                $user_data['useremail']  = $this->__loggedInUser['us_email'];
                $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
                
                $message_template                   = array();
                $message_template['username']       = $this->__loggedInUser['us_name'];
                $message_template['course_name']    = $course_name;
                $message_template['count']          = count($users_list);
                $triggered_activity                 = "admin_users_subscribe_course";
                log_activity($triggered_activity, $user_data, $message_template); 
                $response = array();
                $response['success'] = true;
                $response['message'] = 'Students enrolled to course';
            } else {
                $response['success']    = false;
                $response['messsage']   = 'Please select any student to enroll';
            }
            echo json_encode($response);
            $this->memcache->delete('course_'.$course_id);
            $this->memcache->delete('courses');
            $this->memcache->delete('all_courses');
            $this->memcache->delete('sales_manager_all_sorted_courses');
            $this->memcache->delete('top_courses');
            $this->memcache->delete('popular_courses');
            $this->memcache->delete('featured_courses');
        }
        else
        {
            $response = array();
            $response['success'] = false;
            $response['message'] = lang('course_not_found');
            echo json_encode($response);
        }
    }
    public function discussion($id = false, $thread_id = false)
    {
        if (!$id) {
            $this->session->set_flashdata('message', lang('course_not_found'));
            redirect($this->config->item('admin_folder') . '/course');
        }
        $course_param = $this->__role_query_filter;
        $course_param['id'] = $id;
        $course = $this->Course_model->course($course_param);
        if (!$course) {
            $this->session->set_flashdata('message', lang('course_not_found'));
            redirect($this->config->item('admin_folder') . '/course');
        }
        //save the recently viewed
        $this->Course_model->save_recently_view(array('rvc_course_id' => $id, 'rvc_date' => date('Y-m-d H:i:s'), 'rvc_user_id' => $this->auth->get_current_admin('id')));
        //end
        $content_editor = $this->auth->get_current_user_session('content_editor');
        if (isset($content_editor) && ($content_editor)) {
            redirect($this->config->item('admin_folder') . '/coursebuilder/home/' . $id);
        }
        $order_by = 'DESC';
        $data = array();
        $data['course'] = $course;
        $breadcrumb = array();
        $breadcrumb[] = array('label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>');
        $breadcrumb[] = array('label' => lang('trainings'), 'link' => admin_url('course'), 'active' => 'active', 'icon' => '');
        $breadcrumb[] = array('label' => $course['cb_title'], 'link' => admin_url('course/basic/' . $course['id']), 'active' => 'active', 'icon' => '');
        $breadcrumb[] = array('label' => 'Discussion', 'link' => '', 'active' => 'active', 'icon' => '');
        $data['breadcrumb'] = $breadcrumb;
        $data['title'] = $course['cb_title'];
        $data['assigned_tutors'] = $this->Course_model->assigned_tutors(array('course_id' => $course['id']));
        $enrolled_users = $this->Course_model->enrolled(array('course_id' => $course['id']));
        $data['total_enrolled'] = sizeof($enrolled_users);
        $data['enrolled_users'] = array();
        if (!empty($enrolled_users)) {
            foreach ($enrolled_users as $user) {
                $user['percentage'] = $this->Course_model->calculate_lecture_log(array('user_id' => $user['cs_user_id'], 'course_id' => $user['cs_course_id']));
                $data['enrolled_users'][] = $user;
            }
        }
        $data['course_comments'] = $this->Course_model->get_course_comments_user(array('course_id' => $id, 'parent_id' => 0, 'order_by' => $order_by));
        $data['children_comments'] = array();
        $data['size_children_comments'] = array();
        $comment = array();
        if (!empty($data['course_comments'])) {
            foreach ($data['course_comments'] as $course_comment) {
                $comment_param = array();
                $comment_param['course_id'] = $id;
                $comment_param['parent_id'] = $course_comment['id'];
                $comment_param['order_by'] = $order_by;
                $comment_param['limit'] = 2;
                if ($thread_id && $course_comment['id'] == $thread_id) {
                    unset($comment_param['limit']);
                }
                $comment = $this->Course_model->get_course_comments_user($comment_param);
                $data['children_comments'][$course_comment['id']] = $comment;
                $data['size_children_comments'][$course_comment['id']] = $this->Course_model->get_course_comments_user(array('course_id' => $id, 'parent_id' => $course_comment['id'], 'order_by' => $order_by));
            }
        }
        //echo '<pre>';    print_r($data); die;
        $data['admin_details'] = $this->__loggedInUser;
        $data['thread_id'] = $thread_id;
        $data['assign_faculty_privilege'] = $this->assign_faculty_privilege;
        $admin                  = $this->__loggedInUser;
        $this->__permission     = $this->accesspermission->get_permission(array(
            'role_id' => $admin['role_id'],
            'module' => 'course_forum'
        ));
        $permission     = array('1','2','2','2');
        if(in_array('2', $this->__permission)){
            $permission[2]  = '1';
        }
        if(in_array('3', $this->__permission)){
            $permission[1]  = '1';
        }
        if(in_array('4', $this->__permission)){
            $permission[3]  = '1';
        }
        $token                  = array('userId'=>$admin['id'],'userName'=>$admin['us_name'],'course'=>$course['id'],'blocked'=>false,'institute'=>$admin['us_institute_id'],'userRole'=>$admin['us_role_id'],'permissions'=>implode('',$permission));
        $data['forum_param']    = '/Auth/'.base64_encode(json_encode($token));
        $this->load->view($this->config->item('admin_folder') . '/training_content_discussion', $data);
    }
    public function reviews_old($id = false)
    {
        if (!$id) 
        {
            $this->session->set_flashdata('message', lang('course_not_found'));
            redirect($this->config->item('admin_folder') . '/course');
        }
        $course_param       = $this->__role_query_filter;
        $course_param['id'] = $id;
        $course             = $this->Course_model->course($course_param);
        if (!$course) 
        {
            $this->session->set_flashdata('message', lang('course_not_found'));
            redirect($this->config->item('admin_folder') . '/course');
        }
       
        $data               = array();
        $data['course']     = $course;
        $breadcrumb         = array();
        $breadcrumb[]       = array('label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>');
        $breadcrumb[]       = array('label' => lang('trainings'), 'link' => admin_url('course'), 'active' => 'active', 'icon' => '');
        $breadcrumb[]       = array('label' => $course['cb_title'], 'link' => admin_url('course/basic/' . $course['id']), 'active' => 'active', 'icon' => '');
        $breadcrumb[]       = array('label' => 'Reviews', 'link' => '', 'active' => 'active', 'icon' => '');
        $data['breadcrumb'] = $breadcrumb;
        $data['title']      = $course['cb_title'];

       
        $this->load->view($this->config->item('admin_folder') . '/training_content_review', $data);
    }
    public function save_rating_review()
    {
        $response = array();
        $user_name = $this->input->post('user_name');
        $user_image = $this->input->post('user_image');
        $user_rating = $this->input->post('user_rating');
        $user_review = $this->input->post('user_review');
        $course_id = $this->input->post('course_id');
        date_default_timezone_set('Asia/Kolkata');
        $today = date('Y-m-d H:i:s');
        $cc_admin_rating_id = md5($course_id . " " . $today);
        $directory = user_upload_path();
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }
        $config = array();
        $config['upload_path'] = $directory;
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['encrypt_name'] = true;
        $this->load->library('upload');
        $this->upload->initialize($config);
        $this->upload->do_upload('file');
        $uploaded_data = $this->upload->data();
        //print_r($uploaded_data);die;
        if (($uploaded_data['file_name'] != '')) {
            $new_file = $this->crop_image($uploaded_data);
        }
        $save_rating = array();
        $save_rating['cc_course_id'] = $course_id;
        $save_rating['cc_user_id'] = '0';
        $save_rating['cc_user_name'] = $user_name;
        $save_rating['cc_user_image'] = isset($new_file) ? $new_file : 'default.jpg'."?v=".rand(10,1000);
        $save_rating['cc_admin_rating_id'] = $cc_admin_rating_id;
        $save_rating['cc_rating'] = $user_rating;
        $save_rating['created_date'] = $today;
        $save_rating['cc_reviews'] = $user_review;

        $rating_result = $this->Course_model->save_rating($save_rating);
        if($rating_result)
        {
            $response['message'] = "Details added successfully";
            echo json_encode($response);
        }
    }
    public function crop_image($uploaded_data)
    {
        $source_path = $uploaded_data['full_path'];
        define('DESIRED_IMAGE_WIDTH', 155);
        define('DESIRED_IMAGE_HEIGHT', 155);
        /*
         * Add file validation code here
         */
        list($source_width, $source_height, $source_type) = getimagesize($source_path);
        switch ($source_type) {
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
        if ($source_aspect_ratio > $desired_aspect_ratio) {
            /*
             * Triggered when source image is wider
             */
            $temp_height = DESIRED_IMAGE_HEIGHT;
            $temp_width = (int) (DESIRED_IMAGE_HEIGHT * $source_aspect_ratio);
        } else {
            /*
             * Triggered otherwise (i.e. source image is similar or taller)
             */
            $temp_width = DESIRED_IMAGE_WIDTH;
            $temp_height = (int) (DESIRED_IMAGE_WIDTH / $source_aspect_ratio);
        }
        /*
         * Resize the image into a temporary GD image
         */
        $temp_gdim = imagecreatetruecolor($temp_width, $temp_height);
        imagecopyresampled(
            $temp_gdim, $source_gdim, 0, 0, 0, 0, $temp_width, $temp_height, $source_width, $source_height
        );
        /*
         * Copy cropped region from temporary image into the desired GD image
         */
        $x0 = ($temp_width - DESIRED_IMAGE_WIDTH) / 2;
        $y0 = ($temp_height - DESIRED_IMAGE_HEIGHT) / 2;
        $desired_gdim = imagecreatetruecolor(DESIRED_IMAGE_WIDTH, DESIRED_IMAGE_HEIGHT);
        imagecopy(
            $desired_gdim, $temp_gdim, 0, 0, $x0, $y0, DESIRED_IMAGE_WIDTH, DESIRED_IMAGE_HEIGHT
        );
        /*
         * Render the image
         * Alternatively, you can save the image in file-system or database
         */
        //header('Content-type: image/jpeg');
        $directory = user_upload_path();
        $this->make_directory($directory);
        imagejpeg($desired_gdim, $directory . $uploaded_data['raw_name'] . '.jpg');
        /*
         * Add clean-up code here
         */
        return $uploaded_data['raw_name'] . '.jpg';
    }
    private function make_directory($path = false)
    {
        if (!$path) {
            return false;
        }
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }
    public function change_review_status()
    {
        $today_date = date('Y-m-d h:i:s');
        $response = array();
        $response['error'] = 'false';
        $course_id = $this->input->post('course_id');
        $user_id = $this->input->post('user_id');
        $course_review = $this->Course_model->get_user_review_admin(array('course_id' => $course_id, 'user_id' => $user_id));
        $user_details = $this->Course_model->get_user_ratting_admin(array('course_id' => $course_id, 'user_id' => $course_review['cc_admin_rating_id']));
        $save = array();
        $save['id'] = $course_review['id'];
        $save['rvs_course_id'] = $course_id;
        $save['rvs_user_id'] = $user_id;
        if ($course_review['rvs_blocked'] == 0) {
            $save['rvs_blocked'] = '1';
        } else {
            $save['rvs_blocked'] = '0';
        }
        $save['action_id'] = $this->actions['update'];
        $save['action_by'] = $this->auth->get_current_admin('id');
        $save['updated_date'] = $today_date;
        $response['course_id'] = $course_id;
        $response['user_id'] = $user_id;
        $response['user_name'] = $user_details['cc_user_name'];
        $response['review_status'] = $save['rvs_blocked'];
        $response['id'] = $this->Course_model->save_review($save);
        echo json_encode($response);
    }
    public function groups($id = false)
    {
        if (!$id) {
            $this->session->set_flashdata('message', lang('course_not_found'));
            redirect($this->config->item('admin_folder') . '/course');
        }
        $course_param           = $this->__role_query_filter;
        $course_param['id']     = $id;
        $course_param['select'] = 'id, cb_title, cb_code, cb_groups';
        $course                 = $this->Course_model->course($course_param);
        if (!$course) {
            $this->session->set_flashdata('message', lang('course_not_found'));
            redirect($this->config->item('admin_folder') . '/course');
        }
        $data = array();
        $data['course'] = $course;
        $breadcrumb = array();
        $breadcrumb[] = array('label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>');
        $breadcrumb[] = array('label' => lang('trainings'), 'link' => admin_url('course'), 'active' => 'active', 'icon' => '');
        $breadcrumb[] = array('label' => $course['cb_title'], 'link' => admin_url('course/basic/' . $course['id']), 'active' => 'active', 'icon' => '');
        $breadcrumb[] = array('label' => 'Batches', 'link' => '', 'active' => 'active', 'icon' => '');
        $data['breadcrumb'] = $breadcrumb;
        $data['title'] = $course['cb_title'];
        $data['show_load_button']   = false;
        $data['limit']          = $this->__limit;
        // $data['assigned_tutors'] = $this->Course_model->assigned_tutors(array('course_id' => $course['id']));
        
        // $enrolled_users = $this->Course_model->enrolled(array('course_id' => $course['id']));
        // $data['total_enrolled'] = sizeof($enrolled_users);
        $param              = $this->__role_query_filter;
        $param['course_id'] = $course['id'];
        if(!isset($this->__role_query_filter['institute_id'])){
            $param['institute_id']   = ($this->input->get('institute_id') != null)? $this->input->get('institute_id'): '';
        }
        if($this->input->get('keyword') != null)
        {
            $keyword_arr  = explode('-', $this->input->get('keyword'));
            $keyword      = implode(' ',$keyword_arr);
            $param['keyword']        = trim($keyword);
        }
        $param['count']         = true;
        $data['total_groups']   = $this->Group_model->course_groups($param);
        unset($param['count']);
        $param['limit']         = $this->__limit;
        $param['offset']        = 0;
        $param['select']    = 'groups.id, CONCAT(gp_institute_code," - ",gp_year," - ",gp_name) as batch_name, gp_name, gp_institute_code, gp_course_code, gp_year, gp_institute_id';
        $course_groups      = $this->Group_model->course_groups($param);
        $data['course_groups'] = array();
        if (!empty($course_groups)) {
            foreach ($course_groups as $group) {
                $group['group_strength'] = $this->Group_model->group_users(array('group_id' => $group['id'], 'count' => true));
                $data['course_groups'][] = $group;
            }
        }
        if($data['total_groups'] > $this->__limit)
        {
            $data['show_load_button']   = true;            
        }
        $data['admin_details'] = $this->__loggedInUser;
        //Read institutes form memcached.
        $objects        = array();
        $objects['key'] = 'institutes';
        $callback       = 'institutes';
        $institutes     = $this->memcache->get($objects, $callback, array()); 
        if(!isset($this->__role_query_filter['institute_id'])){
            $data['institutes'] = $institutes;
        }
        // echo "<pre>"; print_r($data); die();
        $data['admin_details'] = $this->auth->get_current_user_session('admin');
        $data['assign_faculty_privilege'] = $this->assign_faculty_privilege;
        $this->load->view($this->config->item('admin_folder') . '/training_content_groups', $data);
    }
    public function enroll_groups($course_id = false) 
    {
        if(!$course_id) {
            redirect(admin_url('course'));
        }
        $data       = array();
        $data['course_id'] = $course_id;
        $data['show_load_button']   = false;
        $param                  = $this->__role_query_filter;
        $param['course_id']     = $course_id;
        $param['keyword']       = $this->input->post('keyword');
        $param['not_deleted']   = true;
        // $param['order_by']      = 'users.us_name';
        // $param['direction']     = 'ASC';
        $param['count']         = true;
        $data['total_groups']    = $this->Group_model->course_groups_not_added($param);
        unset($param['count']);
        $param['limit']         = $this->__limit;
        // $param['offset']        = 0;
        $param['select']        = 'groups.id, CONCAT(gp_institute_code," - ",gp_year," - ",gp_name) as gp_name';
        $data['groups']          = $this->Group_model->course_groups_not_added($param);
        $data['limit']          = $this->__limit;
        //Read institutes form memcached.
        $objects        = array();
        $objects['key'] = 'institutes';
        $callback       = 'institutes';
        $institutes     = $this->memcache->get($objects, $callback, array()); 
        if(!isset($this->__role_query_filter['institute_id'])){
            $data['institutes'] = $institutes;
        }       
        if($data['total_groups'] > $this->__limit){
            $data['show_load_button']   = true;
        }
        // echo "<pre>";
        // print_r($data); die();
        $this->load->view($this->config->item('admin_folder').'/course_enroll_group',$data);
    }
    function enroll_groups_json() 
    {
        $data                       = array();
        $data['show_load_button']   = false;
        $param                      = $this->__role_query_filter;
        
        $limit            = $this->__limit;
        $data['limit']    = $limit;
        $offset           = $this->input->post('offset');
        $page             = $offset;
        if($page===NULL||$page<=0)
        {
            $page         = 1;
        }
        $page             = ($page - 1)* $limit;
        // $param['order_by']       = 'us_name';
        // $param['direction']      = 'ASC';
        $param['course_id']             = $this->input->post('course_id');
        if(!isset($this->__role_query_filter['institute_id'])){
            $param['institute_id']      = $this->input->post('institute_id');
        } else {
            $param['institute_id']      = $this->__role_query_filter['institute_id'];
        }
        $param['keyword']               = $this->input->post('keyword');
        $param['keyword']               = trim($param['keyword']);
        $param['not_deleted']           = true;
        $param['count']                 = true;
        
        $total_groups                   = $this->Group_model->course_groups_not_added($param);
        $data['total_groups']           = $total_groups;       
        unset($param['count']);
        $param['limit']                 = $this->input->post('limit');
        $param['offset']                = $page;
        $param['select']                = 'groups.id, CONCAT(gp_institute_code," - ",gp_year," - ",gp_name) as gp_name';
        if($total_groups > ($this->__limit*$offset))
        {
            $data['show_load_button']  = true;
        }
        $data['groups']                 = $this->Group_model->course_groups_not_added($param);
        echo json_encode($data);
    }
    public function enrolled_json()
    {
        $data                   = array();
        $data['show_load_button']  = false;
        $data['enrolled_users'] = array();
        $course_id              = $this->input->post('course_id');
        $institute_id           = $this->input->post('institute_id');
        $branch_id              = $this->input->post('branch_id');
        $batch_id               = $this->input->post('batch_id');
        $param                  = $this->__role_query_filter;
        $param['course_id']     = $course_id;
        $param['select']        = 'course_subscription.*, users.us_name, users.us_email, users.us_phone, users.us_institute_code';
        $param['filter']        = ($this->input->post('filter') != '')? $this->input->post('filter') : 'active';
        $param['keyword']       = $this->input->post('keyword');
        $param['keyword']       = trim($param['keyword']);
        $param['institute_id']  = $institute_id;
        // if($this->__loggedInUser['us_role_id']!='1'){
        //     $param['institute_id']   = $this->__loggedInUser['us_institute_id'];
        // }
        $param['branch_id']     = $branch_id;
        $param['batch_id']      = $batch_id;
        $param['count']         = true;
        
        $data['total_enrolled'] = $this->Course_model->enrolled($param);
        unset($param['count']);
        $param['limit']         = $this->input->post('limit');        
        $offset           = $this->input->post('offset');
        $page             = $offset;
        if($page===NULL||$page<=0)
        {
            $page         = 1;
        }
        $page             = ($page - 1)* $param['limit'];
        $param['offset']        = $page;
        $data['enrolled_users'] = $this->Course_model->enrolled($param);
        $data['limit']              = $param['limit'];
        if($data['total_enrolled'] > ($this->__limit*$offset))
        {
            $data['show_load_button']  = true;
        }
        $data['batches']              = array();
        if($institute_id != '')
        {
            $this->load->model('Group_model');
            $param                  = array();
            $param['institute_id']  = $institute_id;
            $param['select']        = 'id, CONCAT(gp_institute_code," - ",gp_year," - ",gp_name) as batch_name';
            $data['batches']        = $this->Group_model->groups($param);
        }
        //echo $this->db->last_query();die;
        echo json_encode($data);
    }
    public function commented_users_json()
    {
        $data = array();
        //$data['commented_users']    = array();
        $course_id = $this->input->post('course_id');
        //$data['commented_users']    = $this->Course_model->commented(array('course_id' => $course_id, 'keyword' => $this->input->post('keyword')));
        $data['course_comments'] = $this->Course_model->get_course_comments_user(array('course_id' => $course_id, 'parent_id' => 0, 'keyword' => $this->input->post('keyword')));
        $data['user_details'] = $this->__loggedInUser;
        echo json_encode($data);
    }
    public function get_discussion_hash()
    {
        $course_id = $this->input->post('course_id');
        $parent_id = $this->input->post('parent_id');
        $child_id = $this->input->post('child_id');
        $data['course_comments'] = $this->Course_model->get_course_comments_user(array('course_id' => $course_id, 'discussion_id' => $parent_id));
        $data['children_comments'] = array();
        $comment = array();
        $data['size_children_comments'] = array();
        if (!empty($data['course_comments'])) {
            foreach ($data['course_comments'] as $course_comment) {
                $comment = $this->Course_model->get_course_comments_user(array('course_id' => $course_id, 'parent_id' => $course_comment['id']));
                $data['children_comments'][$course_comment['id']] = $comment;
                $data['size_children_comments'][$course_comment['id']] = $this->Course_model->get_course_comments_user(array('course_id' => $course_id, 'parent_id' => $course_comment['id']));
            }
        }
        $data['admin_details'] = $this->auth->get_current_user_session('admin');
        echo json_encode($data);
    }
    public function post_admin_comment()
    {
        $course_id = $this->input->post('course_id');
        $comment_id = $this->input->post('comment_id');
        $commented = $this->input->post('comment');
        $user = $this->__loggedInUser;
        $save = array();
        $save['course_id'] = $course_id;
        $save['user_id'] = $user['id'];
        $save['comment_title'] = '';
        $save['comment'] = $commented;
        $save['comment_deleted'] = 0;
        $save['parent_id'] = $comment_id;
        date_default_timezone_set('Asia/Kolkata');
        $save['created_date'] = date('Y-m-d H:i:s');
        $data['inserted_id'] = $this->Course_model->save_course_comment($save);
        $data['course_comments'] = $this->Course_model->get_course_comments_user(array('course_id' => $course_id, 'parent_id' => 0));
        $data['children_comments'] = array();
        $comment = array();
        $data['size_children_comments'] = array();
        if (!empty($data['course_comments'])) {
            foreach ($data['course_comments'] as $course_comment) {
                $comment = $this->Course_model->get_course_comments_user(array('course_id' => $course_id, 'parent_id' => $course_comment['id'], 'limit' => 2));
                $data['children_comments'][$course_comment['id']] = $comment;
                $data['size_children_comments'][$course_comment['id']] = $this->Course_model->get_course_comments_user(array('course_id' => $course_id, 'parent_id' => $course_comment['id']));
            }
        }
        $data['admin_details'] = $this->auth->get_current_user_session('admin');
        echo json_encode($data);
    }
    public function load_previous_comments()
    {
        $discussion = $this->input->post('discussion_id');
        $course_id = $this->input->post('course_id');
        $limit_value = $this->input->post('limit_value');
        $data['course_comments'] = $this->Course_model->get_course_comments_user(array('discussion_id' => $discussion, 'course_id' => $course_id, 'parent_id' => 0));
        $data['children_comments'] = array();
        $comment = array();
        $data['size_children_comments'] = array();
        if (!empty($data['course_comments'])) {
            foreach ($data['course_comments'] as $course_comment) {
                $comment = $this->Course_model->get_course_comments_user(array('course_id' => $course_id, 'parent_id' => $course_comment['id'], 'limit' => $limit_value));
                $data['children_comments'][$course_comment['id']] = $comment;
                $data['size_children_comments'][$course_comment['id']] = $this->Course_model->get_course_comments_user(array('course_id' => $course_id, 'parent_id' => $course_comment['id']));
            }
        }
        $data['admin_details'] = $this->__loggedInUser;
        echo json_encode($data);
    }
    public function post_user_comment()
    {
        $course_id = $this->input->post('course_id');
        $comment_id = $this->input->post('comment_id');
        $commented = $this->input->post('comment');
        $user = $this->__loggedInUser;
        $save = array();
        $save['course_id'] = $course_id;
        $save['user_id'] = $user['id'];
        $save['comment_title'] = '';
        $save['comment'] = $commented;
        $save['comment_deleted'] = 0;
        $save['parent_id'] = $comment_id;
        date_default_timezone_set('Asia/Kolkata');
        $save['created_date'] = date('Y-m-d H:i:s');
        $data['inserted_id'] = $this->Course_model->save_course_comment($save);
        $data['posted_user'] = $this->Course_model->get_posted_user($data['inserted_id']);
        $data['course_comments'] = $this->Course_model->get_course_comments_user(array('course_id' => $course_id, 'parent_id' => 0));
        $data['children_comments'] = array();
        $comment = array();
        $data['size_children_comments'] = array();
        if (!empty($data['course_comments'])) {
            foreach ($data['course_comments'] as $course_comment) {
                $comment = $this->Course_model->get_course_comments_user(array('course_id' => $course_id, 'parent_id' => $course_comment['id'], 'limit' => 2));
                $data['children_comments'][$course_comment['id']] = $comment;
                $data['size_children_comments'][$course_comment['id']] = $this->Course_model->get_course_comments_user(array('course_id' => $course_id, 'parent_id' => $course_comment['id']));
            }
        }
        $data['user_details'] = $this->__loggedInUser;
        echo json_encode($data);
    }
    public function delete_comments_admin()
    {
        $parent_id = $this->input->post('parent_id');
        $course_id = $this->input->post('course_id');
        $child_id = $this->input->post('child_id');
        $data['course_comments'] = $this->Course_model->delete_comments_admin(array('parent_id' => $parent_id, 'course_id' => $course_id, 'child_id' => $child_id));
    }
    public function post_new_discussion()
    {
        $course_id = $this->input->post('course_id');
        $discussion_title = $this->input->post('discussion_title');
        $discussion_comment = $this->input->post('discussion_comment');
        $data['user_details'] = $this->__loggedInUser;
        $save = array();
        $save['course_id'] = $course_id;
        $save['user_id'] = $data['user_details']['id'];
        $save['comment_title'] = $discussion_title;
        $save['comment'] = $discussion_comment;
        $save['comment_deleted'] = 0;
        $save['parent_id'] = 0;
        date_default_timezone_set('Asia/Kolkata');
        $save['created_date'] = date('Y-m-d H:i:s');
        $data['user_details'] = $this->__loggedInUser;
        $data['inserted_id'] = $this->Course_model->save_new_discussion_admin($save);
        echo json_encode($data);
    }
    public function reply_admin_discussion()
    {
        $course_id = $this->input->post('course_id');
        $comment_id = $this->input->post('comment_id');
        $comment = $this->input->post('comment');
        $user = $this->__loggedInUser;
        $save = array();
        $save['course_id'] = $course_id;
        $save['user_id'] = $user['id'];
        $save['comment'] = $comment;
        $save['comment_deleted'] = 0;
        $save['parent_id'] = $comment_id;
        date_default_timezone_set('Asia/Kolkata');
        $save['created_date'] = date('Y-m-d H:i:s');
        $data['inserted_id'] = $this->Course_model->save_new_discussion_admin($save);
    }
    public function check_inactive_courses()
    {
        $data = array();
        $data['courses'] = array();
        $courses_id = json_decode($this->input->post('course_id'));
        if (!empty($courses_id)) {
            foreach ($courses_id as $course_id) {
                $course['inactive_courses'] = $this->Course_model->check_inactive_courses(array('course_id' => $course_id));
                $data['courses'][] = $course;
            }
        }
        echo json_encode($data);
    }
    public function change_subscription_status()
    {
       
        $user_id        = $this->input->post('user_id');
        $course_id      = $this->input->post('course_id');
        $status         = $this->input->post('status');
        if($status=='0'){
            $mstatus    = 'course_subscription_suspended';
        }else{
            $mstatus    = 'course_subscription_approved';
        }
        $filter_param['user_id']    = $user_id;
        $filter_param['course_id']  = $course_id;
        $filter_param['update']     = true;
        $save_param['cs_approved']  = $status;
        $result = $this->User_model->save_subscription_new($save_param,$filter_param);

        $user_data              = array();
        $user_data['user_id']   = $this->__loggedInUser['id'];
        $user_data['username']  = $this->__loggedInUser['us_name'];
        $user_data['useremail']  = $this->__loggedInUser['us_email'];
        $user_data['user_type'] = $this->__loggedInUser['us_role_id']; 

        $course_title                       = $this->input->post('course_title');
        $message_template                   = array();
        $message_template['username']       = $this->__loggedInUser['us_name'];
        $message_template['course_name']    = $course_title;
        $message_template['student']        = $this->input->post('student');
        
        $triggered_activity                 = $mstatus;
        log_activity($triggered_activity, $user_data, $message_template); 
        $this->invalidate_subscription(array('user_id' => $user_id,'course_id'=>$course_id));
        if($result){
            $response               = array();
            $response['error']      = false;
            $response['message']    = lang('subscription_saved');
    
            echo json_encode($response);
        }

        $notification_action = 'course_subscription_suspended';

        if($status == 1)
        {
            $notification_action = 'course_subscription_approved';
        }
        
        // $course = $this->Course_model->course(array('id' => $course_id));

        //Notification
        $this->load->library('Notifier');
        $this->notifier->push(
            array(
                'action_code' => $notification_action,
                'assets' => array('course_name' => $course_title,'course_id' => $course_id),
                'target' => $course_id,
                'individual' => true,
                'push_to' => array($user_id)
            )
        );
        //End notification
    }
    //Get mail body template names
    //Added By Yadu Chandran
    public function get_email_templates()
    {
        $data = array();
        $data['mail_template'] = $this->Mailtemplate_model->mail_templates(array('status' => '1'));
        echo json_encode($data);
    }
    //Get mail body template
    //Added By Yadu Chandran
    public function get_template_data()
    {
        $template_id = $this->input->post('mail_template_id');
        $data = array();
        $data['mail_template_data'] = $this->Mailtemplate_model->mail_template(array('id' => $template_id));
        echo json_encode($data);
    }
    //Send Email for course bulk user invite
    //Added By Yadu Chandran
    public function course_send_message_user()
    {
        $subject = $this->input->post('send_user_subject');
        $message = base64_decode($this->input->post('send_user_message'));
        $emails = $this->input->post('send_user_emails');
        $user_mails = json_decode($emails);
        $param['from'] = $this->config->item('site_email');
        $param['to'] = $user_mails;
        $param['subject'] = $subject;
        $param['body'] = $message;
        $send = $this->ofabeemailer->send_mail($param);
    }
    //Send Email for course bulk group invite
    //Added By Yadu Chandran
    public function send_message_group_bulk()
    {
        $subject = $this->input->post('send_user_subject');
        $message = base64_decode($this->input->post('send_user_message'));
        $names = $this->input->post('send_group_names');
        $user_mails = json_decode($names);
        $group_ids = json_decode($this->input->post('send_group_id'));
        $emailarr = array();
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
    public function subscribe()
    {
        $response = array();
        $subscribed_user_names = array();
        $response['error'] = false;
        $response['message'] = lang('subscription_saved');
        $user_ids = $this->input->post('user_ids');
        $user_ids = json_decode($user_ids);
        $course_id = $this->input->post('course_id');
        $course = $this->Course_model->course(array('id' => $course_id));
        $this->load->library('Ofapay');
        if ($course['cb_access_validity'] == 2) {
            $course_enddate = $course['cb_validity_date'];
        } else if ($course['cb_access_validity'] == 0) {
            $course_enddate = date('Y-m-d', strtotime('+3000 days'));
        } else {
            $duration = ($course['cb_validity']) ? $course['cb_validity']-1 : 0;
            $course_enddate = date('Y-m-d', strtotime('+' . $duration . ' days'));
        }
        $mail_after_subscription = $this->input->post('mail_after_subscription');
        if (!empty($user_ids)) {
            $this->load->model('Tutor_model');
            foreach ($user_ids as $user_id) {
                $save = array();
                $save['cs_course_id'] = $course_id;
                $save['cs_course_validity_status'] = $course['cb_access_validity'];
                $save['cs_user_id'] = $user_id;
                $save['cs_subscription_date'] = date('Y-m-d');
                $save['cs_start_date'] = $save['cs_subscription_date'];
                $save['cs_end_date'] = $course_enddate;
                $save['cs_approved'] = '1';
                $save['action_id'] = $this->actions['create'];
                $save['action_by'] = $this->auth->get_current_admin('id');
                $this->User_model->save_subscription($save);
                $this->invalidate_subscription(array('user_id' => $user_id));
                
                //update the payment history
                $param = array();
                $param['user_id'] = $user_id;
                $param['item_id'] = $course_id;
                $param['payment_mode'] = 3;
                $param['amount'] = (isset($course['cb_discount']) && $course['cb_discount'] > 0) ? $course['cb_discount'] : $course['cb_price'];
                $this->ofapay->save_payment($param);
                //End
                //save the name of subscribed users
                $subscribed_user = $this->User_model->user(array('id' => $user_id));
                $subscribed_user_names[] = $subscribed_user['us_name'];
                //end
            }
            
            $this->invalidate_course(array('course_id' => $course_id));
            if ($mail_after_subscription) {
                $user_mails = array();
                foreach ($user_ids as $user_id) {
                    $user = $this->User_model->user(array('id' => $user_id));
                    $user_mails[] = $user['us_email'];
                }
                $param = array();
                $param['from'] = $this->config->item('site_email');
                $param['to'] = $user_mails;
                $param['subject'] = lang('course_subscripion_by_admin') . ' ' . $course['cb_title'];
                $param['body'] = 'Hi, <br />Congratulations! You has been subscribed to the course named ' . $course['cb_title'];
                $this->ofabeemailer->send_mail($param);
            }
        }
        //echo '<pre>'; print_r($this->input->post());die;
        $this->enrolled_json();
        //echo json_encode($response);
    }
    public function subscribe_group()
    {
        $response = array();
        $response['error'] = false;
        $response['message'] = lang('subscription_saved');
        $course_id = $this->input->post('course_id');
        $course = $this->Course_model->course(array('id' => $course_id));
        //$duration                   = ($course['cb_validity'])?$course['cb_validity']:0;
        if ($course['cb_access_validity'] == 2) {
            $course_enddate = $course['cb_validity_date'];
        } else if ($course['cb_access_validity'] == 0) {
            $course_enddate = date('Y-m-d', strtotime('+3000 days'));
        } else {
            $duration = ($course['cb_validity']) ? $course['cb_validity']-1 : 0;
            $course_enddate = date('Y-m-d', strtotime('+' . $duration . ' days'));
        }
        $mail_after_subscription = $this->input->post('mail_after_subscription');
        $user_mails = array();
        //for savong the group id in course
        $course_groups = $course['cb_groups'];
        $course_groups = ($course_groups != '') ? explode(',', $course_groups) : array();
        //end
        $groups = json_decode($this->input->post('groups'));
        $subscribed_group_names = array();
        if (!empty($groups)) {
            $this->load->model('Tutor_model');
            foreach ($groups as $group_id) {
                $course_groups[] = $group_id;
                $group_users = $this->Group_model->group_users(array('group_id' => $group_id));
                $group = $this->Group_model->group(array('id' => $group_id));
                $subscribed_group_names[] = $group['gp_name'];
                if (!empty($group_users)) {
                    foreach ($group_users as $users) {
                        $user_mails[] = $users['us_email'];
                        $save = array();
                        $save['cs_course_id'] = $course_id;
                        $save['cs_user_id'] = $users['id'];
                        $save['cs_subscription_date'] = date('Y-m-d');
                        $save['cs_start_date'] = $save['cs_subscription_date'];
                        $save['cs_end_date'] = $course_enddate; //date('Y-m-d', strtotime('+'.$duration.' days'));
                        $save['cs_approved'] = '1';
                        $save['action_id'] = $this->actions['create'];
                        $save['action_by'] = $this->auth->get_current_admin('id');
                        $this->User_model->save_subscription($save);
                        $this->invalidate_subscription(array('user_id' => $users['id']));
                        
                    }
                }
            }
            $this->invalidate_course(array('course_id' => $course_id));
        }
        if ($mail_after_subscription) {
            $param = array();
            $param['from'] = $this->config->item('site_email');
            $param['to'] = $user_mails;
            $param['subject'] = lang('course_subscripion_by_admin') . ' ' . $course['cb_title'];
            $param['body'] = 'Hi, <br />Congratulations! You has been subscribed to the course named ' . $course['cb_title'];
            $this->ofabeemailer->send_mail($param);
        }
        //removing groupib from course
        $course_groups = implode(',', array_unique($course_groups));
        $save = array();
        $save['id'] = $course['id'];
        $save['cb_groups'] = $course_groups;
        $this->Course_model->save($save);
        //end
        //echo '<pre>'; print_r($this->input->post());die;
        $this->groups_json();
        //echo json_encode($response);
    }
    public function not_added_groups_json()
    {
        $institute_id = ($this->__loggedInUser['role_id'] == '8') ? $this->__loggedInUser['us_institute_id'] : false;
        $course_id = $this->input->post('course_id');
        $keyword = $this->input->post('keyword');
        $data = array();
        $course_groups = $this->Group_model->course_groups_not_added(
            array(
                'select' => 'groups.id, CONCAT(gp_institute_code," - ",gp_year," - ",gp_name) as gp_name',
                'course_id' => $course_id,
                'keyword' => $keyword,
                'limit' => $this->input->post('limit'),
                'not_deleted' => true,
                'user_id' => $this->__loggedInUser['id'],
                'institute_id' => $institute_id,
            ));
        // echo "<pre>";
        // print_r($course_groups);
        // die;
        $data['groups'] = array();
        if (!empty($course_groups)) {
            foreach ($course_groups as $group) {
                $group['group_strength'] = $this->Group_model->group_users(array('group_id' => $group['id'], 'count' => true));
                $data['groups'][] = $group;
            }
        }
        echo json_encode($data);
    }
    
    public function set_as_complete()
    {
       
        $user_ids                                  = json_decode($this->input->post('user_id'),true);
        $user_ids                                  = is_array($user_ids) ? $user_ids : false;
        $course_id                                 = (preg_match('/^[1-9]+[0-9]*$/', $this->input->post('course_id')) == true)? $this->input->post('course_id') : false;
        if($user_ids !== false && !empty($user_ids) && $course_id !== false)
        {
            $save_param['cs_percentage']               = '100';
            $save_param['cs_completion_registered']    = '1';
            $filter_param['course_id']      = $course_id;
            $filter_param['user_ids']       = $user_ids; 
            $filter_param['update']         = true;
            $this->User_model->save_subscription_new($save_param,$filter_param);
            $student_count          = count($user_ids);
            if($student_count==1){
                $student_name           = trim($this->input->post('student_name'));
                $student_count_valid    = '';
            }else{
                $student_name           = '';
                $student_count_valid    = $student_count;
            }
            if(sizeof($user_ids) > 0 )
            {
                foreach($user_ids as $user_id)
                {
                    $this->invalidate_subscription(array('course_id' => $course_id, 'user_id' => $user_id));   
                }
            }
            $user_data              = array();
            $user_data['user_id']   = $this->__loggedInUser['id'];
            $user_data['username']  = $this->__loggedInUser['us_name'];
            $user_data['useremail']  = $this->__loggedInUser['us_email'];
            $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
            
            $message_template                   = array();
            $message_template['username']       = $this->__loggedInUser['us_name'];
            $message_template['course_name']    = trim($this->input->post('course_title'));
            $message_template['count']          = $student_count_valid;
            $message_template['student_name']   = $student_name;
            $triggered_activity                 = 'course_set_as_complete';
            log_activity($triggered_activity, $user_data, $message_template); 

            $response               = array();
            $response['error']      = false;
            $response['message']    = 'The course progress has been set a completed';
        }
        else
        {
            $response               = array();
            $response['error']      = true;
            $response['message']    = 'Something went wrong!! Please try again.';
        }
        echo json_encode($response);
    }
    
    public function set_as_complete_group()
    {
        
        $course_id  = (preg_match('/^[1-9]+[0-9]*$/', $this->input->post('course_id')) == true) ? $this->input->post('course_id') : false;
        $groups     = json_decode($this->input->post('group_id'));
        $groups     = is_array($groups) ? $groups : false;
        $user_ids   = array();

        if ($groups !== false && !empty($groups) && $course_id !== false) {
            foreach ($groups as $group_id) {
                $group_param['group_id']    = $group_id;
                $group_param['select']      = 'users.id';
                $group_users                = $this->Group_model->group_users($group_param);
                if (!empty($group_users)) {
                    $user_ids[]  = array_column($group_users,'id');
                }
            }
            if(count($groups)==1){
                $batchname   = trim($this->input->post('group_name'));
                $batch_count = '';
            }else{
                $batchname   = '';
                $batch_count = count($groups);
            }
            // $user_ids = call_user_func_array('array_merge',$user_ids);
            $users                          = array_reduce($user_ids, 'array_merge', array());
            $save_param['cs_percentage']    = '100';
            $save_param['cs_completion_registered']    = '1';
            $filter_param['course_id']      = $course_id;
            $filter_param['user_ids']       = $users;
            $filter_param['update']         = true;
            $this->User_model->save_subscription_new($save_param,$filter_param);
            
            $user_data              = array();
            $user_data['user_id']   = $this->__loggedInUser['id'];
            $user_data['username']  = $this->__loggedInUser['us_name'];
            $user_data['useremail']  = $this->__loggedInUser['us_email'];
            $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
            
            $message_template                   = array();
            $message_template['username']       = $this->__loggedInUser['us_name'];
            $message_template['course_name']    = trim($this->input->post('course_title'));
            $message_template['count']          = count($users);
            $message_template['batch_name']     = $batchname;
            $message_template['batch_count']    = $batch_count;

            $triggered_activity                 = 'course_set_as_complete_batch';
            log_activity($triggered_activity, $user_data, $message_template); 
            $response               = array();
            $response['error']      = false;
            $response['message']    = "Subscription Completed for all Students under this Batch";
            echo json_encode($response);
        }else{
            $response               = array();
            $response['error']      = true;
            $response['message']    = 'Please select a Batch';
            echo json_encode($response);
        }
       
    
    }
    public function reset_result()
    {
        $user_ids       = json_decode($this->input->post('user_id'),true);
        $course_id      = $this->input->post('course_id');
        $course_title   = $this->input->post('course_title');

        $filter_param                              = array();
        $filter_param['user_ids']                  = $user_ids;
        $filter_param['course_id']                 = $course_id;
        $filter_param['update']                    = true;
        $save_param['cs_percentage']               = '0';
        $save_param['cs_completion_registered']    = '0';
        $save_param['cs_lecture_log']              = null;
        $this->User_model->save_subscription_new($save_param,$filter_param);
        $result = $this->remove_subscription(array('course_title'=>$course_title,'user_ids'=>$user_ids,'course_id'=>$course_id,'action_code'=>'course_subscription_result_reset'));
        
        foreach($user_ids as $user_id){

            $this->invalidate_subscription(array('course_id' => $course_id, 'user_id' => $user_id));   
        }
        $student_count          = count($user_ids);
        if($student_count==1){
            $student_name           = $this->input->post('student_name');
            $student_count_valid    = '';
        }else{
            $student_name           = '';
            $student_count_valid    = $student_count;
        }
        $user_data              = array();
        $user_data['user_id']   = $this->__loggedInUser['id'];
        $user_data['username']  = $this->__loggedInUser['us_name'];
        $user_data['useremail']  = $this->__loggedInUser['us_email'];
        $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
        
        $message_template                   = array();
        $message_template['username']       = $this->__loggedInUser['us_name'];
        $message_template['course_name']    = $course_title;
        $message_template['count']          = $student_count_valid;
        $message_template['student_name']   = $student_name;
        $triggered_activity                 = 'course_result_reset';
        log_activity($triggered_activity, $user_data, $message_template); 
        $response               = array();
        $response['error']      = false;
        $response['message']    = 'The subscription has been reset';
        echo json_encode($response);
    }
    public function reset_result_group()
    {
        $course_id      = (preg_match('/^[1-9]+[0-9]*$/', $this->input->post('course_id')) == true) ? $this->input->post('course_id') : false;
        $course_title   = $this->input->post('course_title');
        $groups         = json_decode($this->input->post('group_id'),true);
        $user_ids       = array();

        if (!empty($groups) && $course_id !== false ) {
            foreach($groups as $group_id){
                $group_param             = array();
                $group_param['select']   = 'users.id,users.us_name,users.us_email,users.us_email_verified';
                $group_param['group_id'] = $group_id;
                $group_users             = $this->Group_model->group_users($group_param);
                if (!empty($group_users)) {
                    $user_ids[]  = array_column($group_users,'id');
                }
            }
            if(count($groups)==1){
                $batchname   = $this->input->post('group_name');
                $batch_count = '';
            }else{
                $batchname   = '';
                $batch_count = count($groups);
            }
            $users  = array_reduce($user_ids, 'array_merge', array());
            
            if (!empty($users)) {
                $filter_param                   = array();
                $filter_param['user_ids']       = $users;
                $filter_param['course_id']      = $course_id;
                $filter_param['update']         = true;
                $save_param['cs_percentage']    = '0';
                $save_param['cs_completion_registered']    = '0';
            
                $this->User_model->save_subscription_new($save_param,$filter_param);
                $this->remove_subscription(array('course_title'=>$course_title,'user_ids'=>$users,'course_id'=>$course_id,'action_code'=>'course_subscription_result_reset'));

                $user_data              = array();
                $user_data['user_id']   = $this->__loggedInUser['id'];
                $user_data['username']  = $this->__loggedInUser['us_name'];
                $user_data['useremail']  = $this->__loggedInUser['us_email'];
                $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
                
                $message_template                   = array();
                $message_template['username']       = $this->__loggedInUser['us_name'];
                $message_template['course_name']    = $course_title;
                $message_template['count']          = count($users);
                $message_template['batch_name']     = $batchname;
                $message_template['batch_count']    = $batch_count;
    
                $triggered_activity                 = 'course_result_reset_batch';
                log_activity($triggered_activity, $user_data, $message_template); 
                $response = array();
                $response['error'] = false;
                $response['message'] = lang('subscription_saved');
            }
            else{
                $response = array();
                $response['error'] = true;
                $response['message'] = "There are no students in this Batch";
            }
        }else{
            $response = array();
            $response['error'] = true;
            $response['message'] = "Select any one Batch";
        }
        echo json_encode($response);
    }

    public function reset_certificates()
    {
        $user_ids  = json_decode($this->input->post('user_id'),true);
        $course_id = $this->input->post('course_id');
        $result    = $this->Course_model->reset_certificates(array('user_ids'=>$user_ids,'course_id'=>$course_id));

        //remove file
        if(!empty($user_ids))
        {
            foreach($user_ids as $user_id)
            {
                $certificate_source     = FCPATH.certificate_upload_path().$user_id.'_'.$course_id;
                if(is_dir($certificate_source))
                {
                    shell_exec("rm -rf ".$certificate_source);
                }
            }    
        }

        //End

        if(!empty($result)){
            $response               = array();
            $response['error']      = false;
            $response['message']    = 'Certificates resetted successfully!';
        }else{
            $response               = array();
            $response['error']      = true;
            $response['message']    = 'Failed';
        }
        echo json_encode($response);
    }


    public function delete_subscription($params = array())
    {
        $this->load->library(array('archive'));
        if(isset($params['user_ids']))
        {
            $user_ids       = array_unique($params['user_ids']);
            $course_id      = $params['course_id'];
            $course_title   = $params['course_title'];
        }
        else
        {
            $user_ids       = json_decode($this->input->post('user_id'),true);
            $course_id      = $this->input->post('course_id');
            $course_title   = $this->input->post('course_title');
        }
        $notify_to      = array();

        foreach ($user_ids as $user_id) {
            $archive_param                  = array();
            $archive_param['course_id']     = $course_id;
            $archive_param['user_id']       = $user_id;
            $this->archive->subscription_archive_process($archive_param);
            $this->invalidate_subscription(array('course_id' => $course_id, 'user_id' => $user_id));
            $this->memcache->delete('all_courses_'.$user_id);
            $this->memcache->delete('enrolled_'.$user_id);
            $this->memcache->delete('mobile_enrolled_'.$user_id);
            $this->memcache->delete('bundle_enrolled_'.$user_id);
            $this->memcache->delete('enrolled_item_ids_'.$user_id);
        }

        $student_count              = count($user_ids);
        if($student_count==1){
            $student_name           = $this->input->post('student_name');
            $student_count_valid    = '';
        }else{
            $student_name           = '';
            $student_count_valid    = $student_count;
        }
        $user_data              = array();
        $user_data['user_id']   = $this->__loggedInUser['id'];
        $user_data['username']  = $this->__loggedInUser['us_name'];
        $user_data['useremail'] = $this->__loggedInUser['us_email'];
        $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
        
        $message_template                   = array();
        $message_template['username']       = $this->__loggedInUser['us_name'];
        $message_template['course_name']    = $course_title;
        $message_template['count']          = $student_count_valid;
        $message_template['student_name']   = $student_name;
        $triggered_activity                 = 'course_subscription_removed';
        log_activity($triggered_activity, $user_data, $message_template); 
        
        $this->Course_model->unsubscribe_user(array('user_ids'=>$user_ids,'course_id'=>$course_id));
        $result = $this->remove_subscription(array('course_title'=>$course_title,'user_ids'=>$user_ids,'course_id'=>$course_id));
        if(isset($params['user_ids']))
        {
            return true;
        }
        if(!empty($result)){
            $response               = array();
            $response['error']      = false;
            $response['message']    = lang('subscription_removed_success');
        }else{
            $response               = array();
            $response['error']      = true;
            $response['message']    = 'Failed';
        }
        echo json_encode($response);
        $this->memcache->delete('courses');
        $this->memcache->delete('all_courses');
        $this->memcache->delete('sales_manager_all_sorted_courses');
        $this->memcache->delete('top_courses');
        $this->memcache->delete('popular_courses');
        $this->memcache->delete('featured_courses');
        $this->invalidate_course(array('course_id'=>$course_id));
    }

    private function remove_subscription($param = array())
    { 
        $user_param               = array();
        $course_id                = isset($param['course_id'])?$param['course_id']:'';
        $user_param['user_id']    = isset($param['user_id'])?$param['user_id']:'';
        $user_param['user_ids']   = isset($param['user_ids'])?$param['user_ids']:'';
        $user_param['course_id']  = $course_id;
        $user_param['lecture_id'] = isset($param['lecture_id'])?$param['lecture_id']:'';
        $course_title             = isset($param['course_title'])?$param['course_title']:'';
       //die('remove_subscription');
        $result[] = $this->Course_model->remove_logs($user_param);
        $result[] = $this->Course_model->remove_asessment_attempts($user_param);
        $result[] = $this->Course_model->remove_asessment_report($user_param);
        $result[] = $this->Course_model->remove_descrptive_answered($user_param);
        // $course = $this->Course_model->course(array('id' => $user_param['course_id']));

        $user_ids = array();
        if($user_param['user_ids'] != '')
        {
            $user_ids = $user_param['user_ids'];
        }

        if($user_param['user_id'] != '')
        {
            $user_ids[] = $user_param['user_id'];
        }
        $action_code = isset($param['action_code'])?$param['action_code']:'course_subscription_removed';
        
        //Notification
        $this->load->library('Notifier');
        $this->notifier->push(
            array(
                'action_code' => $action_code,
                'assets' => array('course_name' =>  $course_title ,'course_id' => $course_id),
                'target' => $course_id,
                'individual' => true,
                'push_to' => $user_ids
            )
        );
        //End notification

        return $result;
    }
    public function delete_subscription_group()
    {
        $groups                     = json_decode($this->input->post('group_id'),true);
        $course_id                  = (preg_match('/^[1-9]+[0-9]*$/', $this->input->post('course_id')) == true) ? $this->input->post('course_id') : false;
        $course_title               = trim($this->input->post('course_title'));
        $course_param               = array();
        $group_users_ids            = array();
        $users                      = array();
        $course_param['select']     = 'cb_groups'; 
        $course_param['limit']      = 1;
        $course_param['course_id']  = $course_id;
        if($course_id !== false)
        {
            $course                     = $this->Course_model->course_new($course_param);
            
            //removing groupid from course
            $course_groups = $course['cb_groups'];
            $course_groups = ($course_groups) ? $course_groups : array();
            $course_groups = explode(',', $course_groups);
            //end
            
            $filtered_groups = array_diff($course_groups, $groups);
            if(count($groups)==1){
                $batchname   = $this->input->post('group_name');
                $batch_count = '';
            }else{
                $batchname   = '';
                $batch_count = count($groups);
            }
            if (!empty($groups)) {
                foreach ($groups as $group_id) {
                    // if (!empty($course_groups)) {
                    //     if (($key = array_search($group_id, $course_groups)) !== false) {
                    //         unset($course_groups[$key]);
                    //     }
                    // }
                    $group_users = $this->Group_model->group_users(array('group_id' => $group_id));
                    if (!empty($group_users)) {
                        $group_users_ids[] = array_column($group_users,'id');
                    }
                    $check_override_batch    = $this->Group_model->check_override_batch($group_id);
                    if(!empty($check_override_batch)){
                        foreach($check_override_batch as $override_batch){
                            $save_override['id']    = $override_batch['id'];
                            $override_batches       = explode(",",$override_batch['lo_override_batches']);
                            if (($key = array_search($group_id, $override_batches)) !== false) {
                                unset($override_batches[$key]);
                            }
                            $save_override['lo_override_batches']= (!empty($override_batches))?implode(",",$override_batches):'';
                            $this->load->model('Test_model');
                            if(!empty($override_batches)){
                                $this->Test_model->saveAssesmentOverride($save_override);
                            } else {
                                $this->Test_model->deleteAssesmentOverride(array('id'=>$override_batch['id']));
                            }
                        }
                    }
                }
                
                if(count($group_users_ids)>0){
                    $users  = array_reduce($group_users_ids, 'array_merge', array());
                $removeUsers = $this->remove_subscription(array('course_title'=>$course_title,'user_ids'=>$users,'course_id'=>$course_id));
                $delete_subscription                     = array();
                $delete_subscription['user_ids']         = $users;
                $delete_subscription['course_id']        = $course_id;
                $delete_subscription['course_title']     = $course_title;
                    $this->delete_subscription($delete_subscription);
                }

                $course_groups      = implode(',', $filtered_groups);
                $save               = array();
                $save['id']         = $course_id;
                $save['cb_groups']  = $course_groups;
                $result             = $this->Course_model->save($save);

                $user_data              = array();
                $user_data['user_id']   = $this->__loggedInUser['id'];
                $user_data['username']  = $this->__loggedInUser['us_name'];
                $user_data['useremail']  = $this->__loggedInUser['us_email'];
                $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
                
                $message_template                   = array();
                $message_template['username']       = $this->__loggedInUser['us_name'];
                $message_template['course_name']    = $course_title;
                $message_template['count']          = empty($users)?'0':count($users);
                $message_template['batch_name']     = $batchname;
                $message_template['batch_count']    = $batch_count;
                if($message_template['count']=='0'){
                    $triggered_activity                 = 'course_subscription_removed_group_zero';
                }else{
                    $triggered_activity                 = 'course_subscription_removed_group';
                }
                log_activity($triggered_activity, $user_data, $message_template); 
                if($result){
                    $response               = array();
                    $response['error']      = false;
                    $response['message']    = "Batch removed from course successfully";
                }else{
        
                    $response               = array();
                    $response['error']      = true;
                    $response['message']    = 'Failed';
                }
                //echo json_encode($response);
            }else{
                $response               = array();
                $response['error']      = true;
                $response['message']    = 'Please select one batch';
            }
        }
        else
        {
            $response                   = array();
            $response['error']          = true;
            $response['message']        = lang('course_not_found');
        }
        echo json_encode($response);
        
    }
    public function groups_json()
    {
        $data                       = array();
        $data['show_load_button']   = false;
        $param                 = $this->__role_query_filter;
        $param['course_id']    = $this->input->post('course_id');
        if(!isset($this->__role_query_filter['institute_id'])){
            $param['institute_id']   = $this->input->post('institute_id');
        } else {
            $param['institute_id']   = $this->__role_query_filter['institute_id'];
        }
        $param['keyword']       = trim($this->input->post('keyword'));
        //$param['keyword']       = trim($param['keyword']);
        $param['count']         = true;
        $data['total_groups']   = $this->Group_model->course_groups($param);
        unset($param['count']);
        $param['select']        = 'groups.id, CONCAT(gp_institute_code," - ",gp_year," - ",gp_name) as batch_name, gp_name, gp_institute_code, gp_course_code, gp_year, gp_institute_id';
        $param['limit']         = $this->input->post('limit');        
        $offset           = $this->input->post('offset');
        $page             = $offset;
        if($page===NULL||$page<=0)
        {
            $page         = 1;
        }
        $page             = ($page - 1)* $param['limit'];
        $param['offset']  = $page;
        if($data['total_groups'] > ($this->__limit*$offset))
        {
            $data['show_load_button']  = true;
        }
        $data['limit']      = $param['limit'];
        $course_groups  = $this->Group_model->course_groups($param);
        // echo "<pre>"; print_r($course_groups); die();
        $data['groups'] = array();
        if (!empty($course_groups)) {
            foreach ($course_groups as $group) {
                $group['group_strength'] = $this->Group_model->group_users(array('group_id' => $group['id'], 'count' => true));
                $data['groups'][] = $group;
            }
        }
        echo json_encode($data);
    }
    public function change_subscription_status_bulk()
    {
        $status             = $this->input->post('status');
        $course_id          = $this->input->post('course_id');
        $user_ids           = json_decode($this->input->post('users'),true);
        $user_status_data   = array();
        $course_title       = $this->input->post('course_title');
        
        $student_count = count($user_ids);
        if($status == '0'){
            $action = 'suspended';
            
        }else{
            $action = 'approved';
        }
        if (!empty($user_ids)) 
        {
            $subscriptions              = array();
            $subscriptions['user_ids']  = $user_ids;
            $subscriptions['course_id'] = $course_id;
            $subscriptions['update']    = true;

            $save_param                 = array();
            $save_param['cs_approved']  = $status;
            $save_param['updated_date'] = date('Y-m-d H:i:s');
            $this->User_model->save_subscription_new($save_param, $subscriptions);

            foreach ($user_ids as $user_id) 
            {
                $this->invalidate_subscription(array('course_id' => $course_id, 'user_id' => $user_id)); 
            }
        }

        $notification_action = 'course_subscription_suspended';

        if($status == 1)
        {
            $notification_action = 'course_subscription_approved';
        }
        
        // $course = $this->Course_model->course(array('id' => $course_id));

        //Notification
        $this->load->library('Notifier');
        $this->notifier->push(
            array(
                'action_code' => $notification_action,
                'assets' => array('course_name' => $course_title,'course_id' => $course_id),
                'target' => $course_id,
                'individual' => true,
                'push_to' => $user_ids
            )
        );
        //End notification
       
        $user_data              = array();
        $user_data['user_id']   = $this->__loggedInUser['id'];
        $user_data['username']  = $this->__loggedInUser['us_name'];
        $user_data['useremail']  = $this->__loggedInUser['us_email'];
        $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
        
        $message_template                   = array();
        $message_template['username']       = $this->__loggedInUser['us_name'];
        $message_template['course_name']    = $course_title;
        $message_template['count']          = $student_count;
        $message_template['action']         = $action;
        $triggered_activity     = 'course_subscription_status_bulk';
        log_activity($triggered_activity, $user_data, $message_template); 

        $data               = array();
        $data['error']      = false;
        $data['message']    = lang('subscription_saved');
        echo json_encode($data);
    }
    public function change_subscription_status_group()
    {
        $status         = (preg_match('/^[01]$/', $this->input->post('status')) == true)? $this->input->post('status') : false;
        $course_id      = (preg_match('/^[1-9]+[0-9]*$/', $this->input->post('course_id')) == true)? $this->input->post('course_id') : false;
        $group_id       = (preg_match('/^[1-9]+[0-9]*$/', $this->input->post('group_id')) == true)? $this->input->post('group_id') : false;
        $notify_to      = array();
        $course_title   = $this->input->post('course_title');
        if($group_id !== false && $course_id !== false && $status !== false)
        {
            $group_param['group_id']    = $group_id;
            $group_param['select']      = 'users.id';
            $group_users                = $this->Group_model->group_users($group_param);
            
            if (!empty($group_users)) {
                foreach ($group_users as $users) {
                    $notify_to[]                = $users['id'];
                    $filter_param['user_id']    = $users['id'];
                    $filter_param['course_id']  = $course_id;
                    $filter_param['update']     = true;
                    $save_param['cs_approved']  = $status;
                    $save_param['updated_date'] = date('Y-m-d H:i:s');
                    $this->User_model->save_subscription_new($save_param,$filter_param);
                    
                    $this->invalidate_subscription(array('user_id'=>$users['id'],'course_id'=>$course_id));
                }
            
                if($status=='1'){
                    $action = 'approved';
                }else{
                    $action = 'suspended';
                }
                $user_data              = array();
                $user_data['user_id']   = $this->__loggedInUser['id'];
                $user_data['username']  = $this->__loggedInUser['us_name'];
                $user_data['useremail']  = $this->__loggedInUser['us_email'];
                $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
                
                $message_template                   = array();
                $message_template['username']       = $this->__loggedInUser['us_name'];
                $message_template['course_name']    = $course_title;
                $message_template['count']          = count($group_users);
                $message_template['batch_name']     = $this->input->post('group_name');
                $message_template['action']         = $action;

                $triggered_activity                 = 'course_subscription_status_batch';
                log_activity($triggered_activity, $user_data, $message_template); 
                $response               = array();
                $response['error']      = false;
                $response['message']    = lang('subscription_saved');

                $notification_action = 'course_subscription_suspended';

                if($status == 1)
                {
                    $notification_action = 'course_subscription_approved';
                }
                
                // $course = $this->Course_model->course(array('id' => $course_id));

                //Notification
                $this->load->library('Notifier');
                $this->notifier->push(
                    array(
                        'action_code' => $notification_action,
                        'assets' => array('course_name' => $course_title,'student_name' => isset($group_users[0]['us_name'])?$group_users[0]['us_name']:'','course_id' => $course_id),
                        'target' => $course_id,
                        'individual' => true,
                        'push_to' => $notify_to
                    )
                );
                //End notification
                
            }else{
                $response               = array();
                $response['error']      = true;
                $response['message']    = 'Currently there are no students in Batch';
            }
        }
        else
        {
            $response = array();
            $response['error'] = true;
            $response['message'] = lang('course_not_found');
        }
        echo json_encode($response);
    }

    public function change_subscription_status_group_bulk()
    {
        $status     = $this->input->post('status');
        $course_id  = $this->input->post('course_id');
        $groups     = json_decode($this->input->post('groups'));
        $notify_to  = array();
        if (!empty($groups)) 
        {
            foreach ($groups as $group_id) 
            {
                $group_param['group_id']    = $group_id;
                $group_param['select']      = 'users.id';
                $group_users[]              = $this->Group_model->group_users($group_param);
            }
            $group_users  = array_reduce($group_users, 'array_merge', array());
            if (!empty($group_users)) 
            {
                foreach ($group_users as $users) 
                {
                    
                    $notify_to[]                = $users['id'];
                    $filter_param['user_id']    = $users['id'];
                    $filter_param['course_id']  = $course_id;
                    $filter_param['update']     = true;
                    $save_param['cs_approved']  = $status;
                    $save_param['updated_date'] = date('Y-m-d H:i:s');
                    $this->User_model->save_subscription_new($save_param,$filter_param);
                    $this->invalidate_subscription(array('user_id'=>$users['id'],'course_id'=>$course_id));
                }
                $response = array();
                $response['error'] = false;
                $response['message'] = lang('subscription_saved');

                $notification_action = 'course_subscription_suspended';

                if($status == 1)
                {
                    $notification_action = 'course_subscription_approved';
                }
                
                $course = $this->Course_model->course(array('id' => $course_id));

                //Notification
                $this->load->library('Notifier');
                $this->notifier->push(
                    array(
                        'action_code' => $notification_action,
                        'assets' => array('course_name' => $course['cb_title'],'student_name' => isset($group_users[0]['us_name'])?$group_users[0]['us_name']:'','course_id' => $course['id']),
                        'target' => $course_id,
                        'individual' => true,
                        'push_to' => $notify_to
                    )
                );
                //End notification
               
            }else
            {
                $response = array();
                $response['error'] = true;
                $response['message'] = 'Currently there are no students in Batch';
            }
            
        }else{
            $response = array();
            $response['error'] = true;
            $response['message'] = 'Please select any one Batch';
        }
        echo json_encode($response);
    }
    public function set_as_complete_bulk()
    {
        $course_id = $this->input->post('course_id');
        $user_ids = json_decode($this->input->post('users'));
        if (!empty($user_ids)) {
            foreach ($user_ids as $user_id) {
                $this->Course_model->set_user_lecture_as_complete(array('user_id' => $user_id, 'course_id' => $course_id));
                $this->invalidate_subscription(array('course_id' => $course_id, 'user_id' => $user_id));
            }
        }
        $data = array();
        $data['error'] = false;
        $data['message'] = lang('subscription_saved');
        echo json_encode($data);
    }
    public function set_as_complete_group_bulk()
    {
        $course_id = $this->input->post('course_id');
        $groups = json_decode($this->input->post('groups'));
        $i = 0;
        if (!empty($groups)) {
            foreach ($groups as $group_id) {
                $group_users = $this->Group_model->group_users(array('group_id' => $group_id));
                if (!empty($group_users)) {
                    foreach ($group_users as $users) {
                        $this->Course_model->set_user_lecture_as_complete(array('user_id' => $users['id'], 'course_id' => $course_id));
                        $this->invalidate_subscription(array('course_id' => $course_id, 'user_id' => $users['id']));
                    }
                }
                $i++;
            }
        }
        $data = array();
        $data['error'] = false;
        $data['message'] = lang('subscription_saved');

        $user_data              = array();
        $user_data['user_id']   = $this->__loggedInUser['id'];
        $user_data['username']  = $this->__loggedInUser['us_name'];
        $user_data['useremail']  = $this->__loggedInUser['us_email'];
        $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
        
        $message_template                   = array();
        $message_template['username']       = $this->__loggedInUser['us_name'];
        $message_template['course_name']    = $this->input->post('course_title');
        $message_template['count']          = $i;
        $message_template['batch_name']     = '';
        $message_template['batch_count']    = count($groups);

        $triggered_activity                 = 'course_set_as_complete_batch';
        log_activity($triggered_activity, $user_data, $message_template); 

        echo json_encode($data);
    }
    public function reset_result_bulk()
    {
        $course_id = $this->input->post('course_id');
        $user_ids = json_decode($this->input->post('users'));
        if (!empty($user_ids)) {
            foreach ($user_ids as $user_id) {
                $this->Course_model->reset_user_lecture_result(array('user_id' => $user_id, 'course_id' => $course_id));
                $this->invalidate_subscription(array('course_id' => $course_id, 'user_id' => $user_id));
            }
        }
        $data = array();
        $data['error'] = false;
        $data['message'] = lang('subscription_saved');
        echo json_encode($data);

        //Notification
        $this->load->library('Notifier');
        $course = $this->Course_model->course(array('id' => $course_id));
        $this->notifier->push(
            array(
                'action_code' => 'course_subscription_result_reset',
                'assets' => array('course_name' => $course['cb_title'],'course_id' => $course['id']),
                'target' => $course_id,
                'individual' => true,
                'push_to' => $user_ids
            )
        );
        //End notification
    }
    public function reset_result_group_bulk()
    {
        $course_id = $this->input->post('course_id');
        $groups = json_decode($this->input->post('groups'));
        $user_ids = array();
        $course_title   = $this->input->post('course_title');
        $groups         = json_decode($this->input->post('group_id'),true);
        $i = 0;
        if (!empty($groups)) {
            foreach ($groups as $group_id) {
                $group_users = $this->Group_model->group_users(array('group_id' => $group_id));
                if (!empty($group_users)) {
                    foreach ($group_users as $users) {
                        $user_ids[] = $users['id'];
                        $this->Course_model->reset_user_lecture_result(array('user_id' => $users['id'], 'course_id' => $course_id));
                        $this->invalidate_subscription(array('course_id' => $course_id, 'user_id' => $users['id']));
                    }
                    $i++;
                }
            }
        }
        $data = array();
        $data['error'] = false;
        $data['message'] = lang('subscription_saved');
        echo json_encode($data);

        $user_data              = array();
        $user_data['user_id']   = $this->__loggedInUser['id'];
        $user_data['username']  = $this->__loggedInUser['us_name'];
        $user_data['useremail']  = $this->__loggedInUser['us_email'];
        $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
        
        $message_template                   = array();
        $message_template['username']       = $this->__loggedInUser['us_name'];
        $message_template['course_name']    = $course_title;
        $message_template['count']          = count($user_ids);
        $message_template['batch_name']     = '';
        $message_template['batch_count']    = $i;

        $triggered_activity                 = 'course_result_reset_batch';
        log_activity($triggered_activity, $user_data, $message_template); 


        //Notification
        $this->load->library('Notifier');
        $course = $this->Course_model->course(array('id' => $course_id));
        $this->notifier->push(
            array(
                'action_code' => 'course_subscription_result_reset',
                'assets' => array('course_name' => $course['cb_title'],'course_id' => $course['id']),
                'target' => $course_id,
                'individual' => true,
                'push_to' => $user_ids
            )
        );
        //End notification
    }
    public function delete_subscription_bulk()
    {
        $this->load->library(array('archive'));
        $course_id = $this->input->post('course_id');
        $user_ids = json_decode($this->input->post('users'));
        if (!empty($user_ids)) {
            foreach ($user_ids as $user_id) {
                $archive_param                  = array();
                $archive_param['course_id']     = $course_id;
                $archive_param['user_id']       = $user_id;
                $this->archive->subscription_archive_process($archive_param);
                $this->Course_model->unsubscribe_user(array('user_id' => $user_id, 'course_id' => $course_id));
                $this->invalidate_subscription(array('course_id' => $course_id, 'user_id' => $user_id));
            }
        }
        $this->invalidate_course(array('course_id' => $course_id));
        $data = array();
        echo json_encode($data);
    }
    public function delete_subscription_group_bulk()
    {
        $this->load->library(array('archive'));
        $course_id = $this->input->post('course_id');
        $groups = json_decode($this->input->post('groups'));
        $course = $this->Course_model->course(array('id' => $course_id));
        $course_title               = $this->input->post('course_title');
        //removing groupib from course
        $course_groups = $course['cb_groups'];
        $course_groups = ($course_groups) ? $course_groups : array();
        $course_groups = explode(',', $course_groups);
        //end
        $i = 0;
        $b = 0;
        if (!empty($groups)) {
            foreach ($groups as $group_id) {
                if (!empty($course_groups)) {
                    if (($key = array_search($group_id, $course_groups)) !== false) {
                        unset($course_groups[$key]);
                    }
                }
                $group_users = $this->Group_model->group_users(array('group_id' => $group_id));
                if (!empty($group_users)) {
                    foreach ($group_users as $users) {
                        $archive_param                  = array();
                        $archive_param['course_id']     = $course_id;
                        $archive_param['user_id']       = $users['id'];
                        $this->archive->subscription_archive_process($archive_param);
                        $this->Course_model->unsubscribe_user(array('user_id' => $users['id'], 'course_id' => $course_id));
                        $this->invalidate_subscription(array('course_id' => $course_id, 'user_id' => $users['id']));
                        $i++;
                    }
                }
                $b++;
            }
        }
        $course_groups = implode(',', $course_groups);
        $save = array();
        $save['id'] = $course['id'];
        $save['cb_groups'] = $course_groups;
        $this->Course_model->save($save);
        $this->invalidate_course(array('course_id' => $course_id));
        $data = array();
        echo json_encode($data);

        $user_data              = array();
        $user_data['user_id']   = $this->__loggedInUser['id'];
        $user_data['username']  = $this->__loggedInUser['us_name'];
        $user_data['useremail']  = $this->__loggedInUser['us_email'];
        $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
        
        $message_template                   = array();
        $message_template['username']       = $this->__loggedInUser['us_name'];
        $message_template['course_name']    = $course_title;
        $message_template['count']          = $i;
        $message_template['batch_name']     = '';
        $message_template['batch_count']    = $b;
        if($message_template['count']=='0'){
            $triggered_activity                 = 'course_subscription_removed_group_zero';
        }else{
            $triggered_activity                 = 'course_subscription_removed_group';
        }
        log_activity($triggered_activity, $user_data, $message_template);

    }
    public function save_group()
    {
        $response = array();
        $response['error'] = true;
        $response['message'] = lang('group_name_not_available');
        $group_name = $this->input->post('group_name');
        if ($this->Group_model->group(array('name' => $group_name)) == 0) {
            $response['error'] = false;
            $save = array();
            $save['id'] = false;
            $save['gp_account_id'] = $this->config->item('id');
            $save['gp_name'] = $group_name;
            $save['gp_created_by'] = $this->__loggedInUser['id'];
            $response['group_id'] = $this->Group_model->save($save);
        }
        echo json_encode($response);
    }
    public function save_group_users()
    {
        $group_id = $this->input->post('group_id');
        $user_ids = $this->input->post('user_ids');
        $user_ids = json_decode($user_ids);
        if (!empty($user_ids)) {
            foreach ($user_ids as $user_id) {
                $user = $this->User_model->user(array('id' => $user_id));
                $user_groups = $user['us_groups'];
                $user_groups = explode(',', $user_groups);
                $user_groups[] = $group_id;
                $user_groups = implode(',', array_unique($user_groups));
                $save = array();
                $save['id'] = $user_id;
                $save['us_groups'] = $user_groups;
                $this->User_model->save($save);
            }
        }
        $response = array();
        $response['error'] = false;
        echo json_encode($response);
    }

    public function language()
    {
        $response = array();
        $response['language'] = array();
        $response['language'] = get_instance()->lang->language;
        echo json_encode($response);
    }
    /*
    send message to individual group - course
    by chandu s
     */

    public function array_flatten($users)
    {
        foreach ($users as $key => $value) 
        {
            if (is_array($value)) 
            { 
              $result = array_merge($result, array_flatten($value)); 
            } 
            else 
            { 
              $result[$key] = $value; 
            } 
          }
          return $result;
    }

    public function send_group_message()
    {
        $ajax = $this->input->post('is_ajax');
        if ($ajax == 'true') 
        {
            $subject        = $this->input->post('send_user_subject');
            $message        = base64_decode($this->input->post('send_user_message'));
            $group_ids      = $this->input->post('group_ids');
            $group_ids      = json_decode($group_ids,true);
            $user_emails    = array(); 
            $userIds        = array();
            $system_message     = array();
            $response                    = array();
            foreach($group_ids as $group_id)
            {
                $group_param['group_id']    = $group_id;
                //$group_param['verified'] = '1';
                $group_param['select']      = 'users.us_email, users.id';
                $users                      = $this->Group_model->group_users($group_param);
                
                $users_mail                 = array_column($users, 'us_email'); 
                $users_mail                 = array_filter($users_mail);

                $users_id                   = array_column($users, 'id'); 
                $users_id                   = array_filter($users_id);
                
                if(!empty($users_mail))
                {
                    $user_emails[]          = $users_mail;
                } 

                if(!empty($users_id))
                {
                    $userIds[]              = $users_id;
                }
            }

            if (is_array($userIds))
            {
                $user_Ids = array(); 

                foreach ($userIds as $key => $value) 
                { 
                    if (is_array($value))
                    { 
                        $user_Ids               = array_merge($user_Ids, $this->array_flatten($value)); 
                    } 
                    else 
                    { 
                        $user_Ids[$key]         = $value; 
                    } 
                } 
            
                for($i = 0; $i < count($user_Ids); $i++)
                {
                //sending notification
                    if($user_Ids[$i])
                    {
                        $random_message_id  = rand(1000, 9999);
                        $date_time          = date(DateTime::ISO8601);
                        $system_message[]   = array(
                            "messageId"     => $random_message_id,
                            "senderId"      => $this->__loggedInUser['id'],
                            "senderName"    => $this->__loggedInUser['us_name'],
                            "senderImage"   => user_path().$this->__loggedInUser['us_image'],
                            "receiverId"    => $user_Ids[$i],
                            "message"       => $message,
                            "datetime"      => $date_time
                        );
                    }
                }

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
            }

            if (is_array($user_emails))
            {
                $useremails = array(); 

                foreach ($user_emails as $key => $value) 
                { 
                    if (is_array($value))
                    { 
                        $useremails = array_merge($useremails, $this->array_flatten($value)); 
                    } 
                    else 
                    { 
                        $useremails[$key] = $value; 
                    } 
                } 
            //print_r($useremails); die; 
            }

            if(!empty($user_emails))
            {
                $user_emails = $useremails;
            }
            //print_r($user_emails); die;

            if(!empty($user_emails))
            {
                $param                                  = array();
                $param['subject'] 	                    = $subject;
                $param['body'] 		                    = $message;
                $this->process_bulk_mail_normal($param,$user_emails);  
                $response['success']                    = true;
                $response['message']                    = 'Message sent successfully';
                echo json_encode($response);
            }
            else
            {
                $response['success']                    = false;
                $response['message']                    = 'Message not sent to the Batches with no Students';
                echo json_encode($response);
            }
            
        }
    }
    private function process_bulk_mail_normal($email_param,$email_ids)
    {
        $data               = array(
            'email_ids'     => json_encode($email_ids),
            'data'          => json_encode($email_param)
        ); 
        //print_r($data); die;
        $curlHandle         = curl_init(site_url()."cron_job/bulk_process_mail_normal");
        $defaultOptions     = array (
                                CURLOPT_POST => 1,
                                CURLOPT_POSTFIELDS => $data,
                                CURLOPT_RETURNTRANSFER => false ,
                                CURLOPT_TIMEOUT_MS => 100,
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
    public function ann_delete()
    {   /* function used to delete an announcement
        used in admin/course/announcement */ 
        $announcement_id    = (preg_match('/^[0-9]+$/', $this->input->post('an_id')) == true) ? trim($this->input->post('an_id')) : false;
        $announcement_title = trim($this->input->post('an_title'));
        $param          = array();
        $param['id']    = $announcement_id;
        if($announcement_id)
        {
            $result         = $this->Announcement_Model->deleteAnnouncement($param);
        }
        else
        {
            $result = false;
        }
        if($result){

            $user_data              = array();
            $user_data['user_id']   = $this->__loggedInUser['id'];
            $user_data['username']  = $this->__loggedInUser['us_name'];
            $user_data['useremail']  = $this->__loggedInUser['us_email'];
            $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
            
            $message_template                           = array();
            $message_template['username']               = $this->__loggedInUser['us_name'];
            $message_template['announcement_title']     =  $announcement_title;

            $triggered_activity     = 'course_announcement_deleted';
            log_activity($triggered_activity, $user_data, $message_template); 

            $response               = array();
            $response['error']      = false;
            $response['message']    = 'announcement deleted'; 
        }else{
            $response               = array();
            $response['error']      = true;
            $response['message']    = 'failed'; 
        }
        echo json_encode($response);
    }
    public function load_announcement()
    {
        $data = array();
        $data['limit'] = empty($this->input->post('limit')) ? $this->__limit : $this->input->post('limit');
        $course_id = empty($this->input->post('course_id')) ? false : $this->input->post('course_id');
        $is_ajax = $this->input->post('is_ajax');
        $offset = empty($this->input->post('offset')) ? 0 : $this->input->post('offset');
        // $count                     = empty($this->input->post('count')) ? 0 : $this->input->post('count');
        $count = $this->input->post('count');
        $data['show_load_button'] = false;
        $data['default_user_path'] = default_user_path();
        $data['user_path'] = user_path();
        $data['title'] = lang('my_announcement');
        if ($count <= 0) {
            $announcement_param = array('course_id' => $course_id, 'count' => $count);
            $announcements = $this->Announcement_Model->load_announcements($announcement_param);
            $data['total_records'] = count($announcements);
        }
        $announcement_param = array('course_id' => $course_id, 'limit' => $data['limit'], 'offset' => $offset);
        $total_announcements = $this->Announcement_Model->load_announcements($announcement_param);
        $data['start'] = $offset + $data['limit'];
        $count = empty($count) ? $data['total_records'] : $count;
        if ($data['start'] < $count) {
            $data['show_load_button'] = true;
        } else {
            $data['show_load_button'] = false;
        }
        $data['announcement'] = $total_announcements;
        $data['success'] = true;
        echo json_encode($data);
    }
    public function announcement($id = false, $thread_id = false)
    {
        $data['admin_details'] = $this->__loggedInUser;
        if (!empty($this->userPrivilege)) {
            if (!in_array($this->privilege['view'], $this->userPrivilege)) {
                $this->session->set_flashdata('message', lang('No privilege access'));
                redirect($this->config->item('admin_folder') . '/course');
                exit;
            }
        }
        /*
        notes:by edwin
        input['an_to'] -> 1:all student,2:batch,3:instution
         */
        if (!$id) {
            $this->session->set_flashdata('message', lang('course_not_found'));
            redirect($this->config->item('admin_folder') . '/course');
            exit;
        }
        $course_param       = $this->__role_query_filter;
        $course_param['id'] = $id;
        $course             = $this->Course_model->course($course_param);
        if (!$course) {
            $this->session->set_flashdata('message', lang('course_not_found'));
            redirect($this->config->item('admin_folder') . '/course');
        }
        //save the recently viewed
        // $this->Course_model->save_recently_view(array('rvc_course_id' => $id, 'rvc_date' => date('Y-m-d H:i:s'), 'rvc_user_id' => $this->auth->get_current_admin('id')));
        //end
        $content_editor = $this->auth->get_current_user_session('content_editor');
        if (isset($content_editor) && ($content_editor)) {
            redirect($this->config->item('admin_folder') . '/coursebuilder/home/' . $id);
            exit;
        }
        $data                       = array();
        $data['course']             = $course;
        $breadcrumb                 = array();
        $breadcrumb[]               = array('label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>');
        $breadcrumb[]               = array('label' => lang('trainings'), 'link' => admin_url('course'), 'active' => 'active', 'icon' => '');
        $breadcrumb[]               = array('label' => $course['cb_title'], 'link' => admin_url('course/basic/' . $course['id']), 'active' => 'active', 'icon' => '');
        $breadcrumb[]               = array('label' => 'Announcement', 'link' => '', 'active' => 'active', 'icon' => '');
        $data['breadcrumb']         = $breadcrumb;
        $data['title']              = $course['cb_title'];
        $data['assigned_tutors']    = $this->Course_model->assigned_tutors(array('course_id' => $course['id']));
        /* ======= Save new announcement ======== */
        if (!empty($this->input->post())) {
            $data                       = array();
            $data['an_title']           = empty($this->input->post('an_title')) ? '' : $this->input->post('an_title');
            $data['an_description']     = empty($this->input->post('an_description')) ? '' : $this->input->post('an_description');
            $data['an_sent_to']         = empty($this->input->post('an_to')) ? '' : $this->input->post('an_to');
            $data['an_date']            = date('Y-m-d H:i:s');
            $data['an_course_id']       = $id;
            $data['an_batch_ids']       = (is_array($this->input->post('an_batches'))) ? implode(",", $this->input->post('an_batches')) : $this->input->post('an_batches');
            $data['an_institution_ids'] = (is_array($this->input->post('an_instutions'))) ? implode(",", $this->input->post('an_instutions')) : $this->input->post('an_instutions');
            $data['an_created_by']      = $this->__loggedInUser['id'];
            $data['an_created_date']    = date('Y-m-d H:i:s');
            
            $allow                  = true;
            $allow_add_privilage    = in_array($this->privilege['add'], $this->userPrivilege) ? true : false;
            $allow_edit_privilege   = in_array($this->privilege['edit'], $this->userPrivilege) ? true : false;
            if (!empty($this->input->post('an_id'))) {
                $data['id'] = $this->input->post('an_id');
                $allow      = ($allow_edit_privilege) ? true : false;
                $mstatus    = 'updated';
            } else {
                $allow      = ($allow_add_privilage) ? true : false;
                $mstatus    = 'added';
            }
            if ($allow) {
                $announcement_id = $this->Announcement_Model->save($data);
                $this->send_mail_notification($data);
                $user_data              = array();
                $user_data['user_id']   = $this->__loggedInUser['id'];
                $user_data['username']  = $this->__loggedInUser['us_name'];
                $user_data['useremail']  = $this->__loggedInUser['us_email'];
                $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
                
                $message_template                       = array();
                $message_template['username']           = $this->__loggedInUser['us_name'];
                $message_template['announcement_title'] = $data['an_title'];
                $message_template['action']             = $mstatus;

                $triggered_activity                     = 'course_announcement';
                log_activity($triggered_activity, $user_data, $message_template); 
                $this->session->set_flashdata('message', 'Published Announcement');
                
                $notification_param                     = array();
                $notification_param['course_id']        = $data['an_course_id'];
                $notification_param['batch_ids']        = $data['an_batch_ids'];
                $notification_param['institution_ids']  = $data['an_institution_ids'];
                $notification_param['title']            = $course['cb_title'];
                $notification_param['announcement_id']  = $announcement_id;
                $this->send_announcement_notification($notification_param);

            } else {
                $this->session->set_flashdata('message', 'You have no privilege to Publish Announcement');
            }
            return true;
        }
        $data['thread_id']                  = $thread_id;
        $data['course_id']                  = $id;
        $data['assign_faculty_privilege']   = $this->assign_faculty_privilege;
        $this->load->view($this->config->item('admin_folder') . '/training_content_announcement', $data);
    }
    public function all_groups()
    {
        $data                   = array();
        $course_id              = empty($this->input->post('course_id')) ? '' : $this->input->post('course_id');
        $select                 = 'groups.id,groups.gp_name,groups.gp_institute_id';
        $data['course_groups']  = $this->Group_model->course_groups(array('course_id' => $course_id, 'select' => $select));
        $institution_groups     = array_filter(array_unique(array_column($data['course_groups'], 'gp_institute_id')));
        if (!empty($institution_groups)) {
            foreach ($institution_groups as $institution_id) {
                $objects = array();
                $objects['key'] = 'institute_' . $institution_id;
                $callback = 'institute';
                $params = array('id' => $institution_id);
                $data['institution'][] = $this->memcache->get($objects, $callback, $params);
            }
        }
        
        if (empty($data['course_groups'])) {
            $response = array();
            $response['success'] = false;
            $response['groups'] = array();
        } else {
            $response = array();
            $response['success'] = true;
            $response['groups'] = $data;
        }
        echo json_encode($response);
    }
    public function all_institutions()
    {
        $objects = array();
        $params = array();
        if ($this->input->post('is_ajax')) {
            $objects['key'] = 'institutes';
            $callback = 'institutes';
            $institutions = $this->memcache->get($objects, $callback, $params);
        }
        if (empty($institutions)) {
            $response = array();
            $response['success'] = false;
            $response['institution'] = array();
        } else {
            $response = array();
            $response['success'] = true;
            $response['institution'] = $institutions;
        }
        echo json_encode($response);
    }
    public function send_mail_notification($data = array())
    {
        
        $mail_ids = array();
        $sent_to = isset($data['an_sent_to']) ? $data['an_sent_to'] : false;
        if ($sent_to) {
            $course_param               =   array();
            $course_param['course_id']  = $data['an_course_id'];
            $course_param['select']     = "id,cb_title";
            $course_param['limit']      = '1';
            $course = $this->Course_model->course_new($course_param);
            $user = $this->Announcement_Model->fetch_user_details(array('course_id' => $data['an_course_id'], 'created' => $data['an_created_date']));
            switch ($sent_to) {
                case '1':
                    foreach ($user as $user_data) {
                        $mail_ids[] = $user_data['us_email'];
                    }
                    break;
                case '2':
                    $announcement_groups = isset($data['an_batch_ids']) ? explode(',', $data['an_batch_ids']) : false;
                    if (!empty($announcement_groups)) {
                        foreach ($user as $user_data) {
                            $groups = explode(',', $user_data['us_groups']);
                            $status = count(array_intersect($groups, $announcement_groups)) ? true : false;
                            if ($status == true) {
                                $mail_ids[] = $user_data['us_email'];
                            }
                        }
                    }
                    break;
                case '3':
                    $announcement_institutes = isset($data['an_institution_ids']) ? explode(',', $data['an_institution_ids']) : false;
                    foreach ($user as $user_data) {
                        if (in_array($user_data['us_institute_id'], $announcement_institutes)) {
                            $mail_ids[] = $user_data['us_email'];
                        }
                    }
                    break;
                default:
                    return false;
            }
        }
        
        if (!empty($mail_ids)) {
            
            $param                    = array();
            $param['subject']         = "Announcement for ".$course['cb_title'];
            $param['body']            = 'Hi,<br/> Greetings from "'.$this->config->item('site_name').'".<br/>A new announcement <b>"'.$data['an_title'].'"</b> has been created on <b>"'.$data['an_created_date'].'"</b>.';
            $param['to']              = $mail_ids;
            $param['force_recipient'] = true;
            $this->ofabeemailer->send_mail($param); 

            // 'anouncement_url' => site_url('course/dashboard/' . $data['an_course_id'] . '?tab=anouncements')
        }
    }
    
    public function invalidate_course($param = array())
    {
        //Invalidate cache
        $course_id = isset($param['course_id']) ? $param['course_id'] : false;
        if ($course_id) {
            $this->memcache->delete('course_' . $course_id);
            $this->memcache->delete('course_mob' . $course_id);
            $this->memcache->delete('mobile_course_' . $course_id);
        } else {
            $this->memcache->delete('all_courses');
            $this->memcache->delete('sales_manager_all_sorted_courses');
            $this->memcache->delete('courses');
            $this->memcache->delete('top_courses');
        }
        $this->memcache->delete('popular_courses');
        $this->memcache->delete('all_sorted_course');
        $this->memcache->delete('featured_courses');
        $this->memcache->delete('active_courses');
    }
    
    public function invalidate_subscription($param = array())
    {
        //Invalidate cache
        $user_id = isset($param['user_id']) ? $param['user_id'] : false;
        $course_id = isset($param['course_id']) ? $param['course_id'] : false;
        if ($user_id && $course_id) {
            $this->memcache->delete('enrolled_' . $user_id);
            $this->memcache->delete('subscription_' . $course_id . '_' . $user_id);
            $objects_key        = 'enrolled_item_ids_' .$user_id;
            $this->memcache->delete($objects_key);
        }
        if ($user_id) {
            $this->memcache->delete('mobile_enrolled_'.$user_id);
            $this->memcache->delete('enrolled_' . $user_id);
            $objects_key        = 'enrolled_item_ids_' .$user_id;
            $this->memcache->delete($objects_key);
            
        }
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
    function block_user_forum()
    {
        $response           = array();
        $user_id            = (preg_match('/^[1-9]+[0-9]*$/', $this->input->post('user')) == true) ? $this->input->post('user') : false;
        $course_id          = (preg_match('/^[1-9]+[0-9]*$/', $this->input->post('course')) == true) ? $this->input->post('course') : false;
        $action             = $this->input->post('status');
       
        $action             = $action == 0?'1':'0';
        if($action!=1){
            $mstatus = 'approved';
        }else{
            $mstatus = 'restricted';
        }
        if($user_id === false  || $course_id === false ){
            $response['success']    = false;
            $response['message']    = 'Invalid input.';
            echo json_encode($response);die;
        }
        $subscription                       = array();
        $subscription['cs_user_id']         = $user_id;
        $subscription['cs_course_id']       = $course_id;
        $subscription['cs_forum_blocked']   = $action;
        $this->Course_model->save_last_played_lecture($subscription);

        if($action == 1)
        {
            $course = $this->Course_model->course(array('id' => $course_id));
            $user = $this->User_model->user(array('id' => $user_id));
            //Notification
            $this->load->library('Notifier');
            $this->notifier->push(
                array(
                    'action_code' => 'forum_blocked',
                    'assets' => array('course_name' => $course['cb_title'],'student_name' => $user['us_name'],'course_id' => $course['id']),
                    'target' => $course_id,
                    'individual' => true,
                    'push_to' => array($user_id)
                )
            );
            //End notification
        }
        $this->invalidate_subscription(array('user_id'=>$user_id,'course_id'=>$course_id));
        $this->memcache->delete('subscription_' . $course_id . '_' . $user_id);

        $user_data              = array();
        $user_data['user_id']   = $this->__loggedInUser['id'];
        $user_data['username']  = $this->__loggedInUser['us_name'];
        $user_data['useremail']  = $this->__loggedInUser['us_email'];
        $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
        
        $message_template                       = array();
        $message_template['username']           = $this->__loggedInUser['us_name'];
        $message_template['student_name']       = $this->input->post('student_name');
        $message_template['action']             = $mstatus;
        $message_template['course_name']        = $this->input->post('course_title');

        $triggered_activity                     = 'course_forum_access';
        log_activity($triggered_activity, $user_data, $message_template); 
        $response['success']    = true;
        $response['message']    = 'Action success.';
        echo json_encode($response);
    }

    function backups($course_id = false)
    { //print_r($this->backup_privilege);die;
        if(!in_array('1', $this->backup_privilege) || $this->__loggedInUser['role_id'] == '3')
        {
            redirect($this->config->item('admin_folder').'/course');
        }

        $course_param                   = $this->__role_query_filter;
        $course_param['id']             = $course_id;
        $course_param['not_deleted']    = true;
        $course_param['select']         = 'id, cb_title';
        $course                         = $this->Course_model->course($course_param);
        if(!$course || empty($this->backup_privilege))
        {
            redirect($this->config->item('admin_folder').'/course');
        }
        $this->load->model(array('Backup_model'));

        $data               = array();
        $breadcrumb         = array();
        $breadcrumb[]       = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]       = array( 'label' => lang('course_bar_trainings'), 'link' => admin_url('course'), 'active' => 'active', 'icon' => '' );
        $breadcrumb[]       = array( 'label' => $course['cb_title'], 'link' => admin_url('course/basic/' . $course['id']), 'active' => 'active', 'icon' => '' );
        $breadcrumb[]       = array('label' => 'Backup & Restore', 'link' => '', 'active' => 'active', 'icon' => '');
        $data['breadcrumb'] = $breadcrumb;
        $data['course']     = $course;
        $data['backups']    = $this->Backup_model->backups(array('account_id'=>config_item('id'),'course_id' => $course_id, 'select' => 'id, cbk_course_id, cbk_course_name, cbk_course_code, cbk_backup_date, cbk_size','order_by' => 'desc'));
        $this->load->view($this->config->item('admin_folder') . '/training_content_backups', $data);
    }


    public function reviews($id = false)
    {
        $data['admin_details'] = $this->__loggedInUser;
       
        if(!isset($this->review_permission) || !in_array($this->privilege['view'], $this->review_permission))
            {
                $this->session->set_flashdata('message', 'No privilege access to view reviews');
                redirect($this->config->item('admin_folder') . '/course');
                exit;
            }
            
        /*
            notes:by edwin
            input['an_to'] -> 1:all student,2:batch,3:instution
         */

        if (!$id) 
        {
            $this->session->set_flashdata('message', lang('course_not_found'));
            redirect($this->config->item('admin_folder') . '/course');
            exit;
        }
        $course_param       = $this->__role_query_filter;
        $course_param['id'] = $id;
        $course             = $this->Course_model->course($course_param);
        if (!$course) 
        {
            $this->session->set_flashdata('message', lang('course_not_found'));
            redirect($this->config->item('admin_folder') . '/course');
        }

        $data                       = array();
        $data['course']             = $course;
        $breadcrumb                 = array();
        $breadcrumb[]               = array('label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>');
        $breadcrumb[]               = array('label' => lang('trainings'), 'link' => admin_url('course'), 'active' => 'active', 'icon' => '');
        $breadcrumb[]               = array('label' => $course['cb_title'], 'link' => admin_url('course/basic/' . $course['id']), 'active' => 'active', 'icon' => '');
        $breadcrumb[]               = array('label' => 'Reviews', 'link' => '', 'active' => 'active', 'icon' => '');
        $data['breadcrumb']         = $breadcrumb;
        $data['title']              = $course['cb_title'];
        $data['course_id']          = $id;
        $data['limit']              = $this->__limit;
        
        $this->load->view($this->config->item('admin_folder') . '/training_content_review', $data);
    }

    public function load_reviews()
    {
        if(!isset($this->review_permission) || !in_array($this->privilege['view'], $this->review_permission))
            {
                $this->session->set_flashdata('message', 'No privilege access to view reviews');
                redirect($this->config->item('admin_folder') . '/course');
                exit;
            }

        $data           = array();
        $data['limit']  = empty($this->input->post('limit')) ? $this->__limit : $this->input->post('limit');
        $course_id      = empty($this->input->post('course_id')) ? false : $this->input->post('course_id');
        $is_ajax        = $this->input->post('is_ajax');
        $offset         = empty($this->input->post('offset')) ? 0 : $this->input->post('offset');
        
        $data['show_load_button']   = false;
        $data['default_user_path']  = default_user_path();
        $data['user_path']          = user_path();
        $data['title']              = 'Reviews';

        $reviews_param          = array('course_id' => $course_id, 'count' => true);
        $data['total_records']  = $this->Announcement_Model->load_reviews($reviews_param);

        $reviews_param          = array('course_id' => $course_id, 'limit' => $data['limit'], 'offset' => $offset);
        $reviews                = $this->Announcement_Model->load_reviews($reviews_param);
        // echo $this->db->last_query();exit;
        $data['start']          = $offset + $data['limit'];
        $count                  = empty($count) ? $data['total_records'] : $count;
        
        if ($data['start'] < $data['total_records']) 
        {
            $data['show_load_button'] = true;
        } 
        else 
        {
            $data['show_load_button'] = false;
        }
        
        $data['reviews']   = $reviews;
        $data['success']   = true;
        
        echo json_encode($data);
    }

    private function send_announcement_notification($notify_param){

        $curlHandle         = curl_init(site_url()."cron_job/announcement_notification");
        $defaultOptions     = array (
                                CURLOPT_POST => 1,
                                CURLOPT_POSTFIELDS => json_encode($notify_param),
                                CURLOPT_RETURNTRANSFER => false ,
                                CURLOPT_TIMEOUT_MS => 100,
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

    function discussion_instruction($id = false)
    {
        if(!$id)
        {
            $this->session->set_flashdata('error', 'Requested course discussion not found.');
            redirect($this->config->item('admin_folder').'/course');
        }

        if($this->__loggedInUser['rl_full_course'] != '1')
        {
            $param['current_logged_user']   = $this->__loggedInUser['id'];
            $access_course_list             = $this->Course_model->course_permission($param);
            $course_id_list                 = array_column($access_course_list, 'ct_course_id');
            if (!in_array($id, $course_id_list))
            {
                $this->session->set_flashdata('message', lang('course_not_found'));
                redirect($this->config->item('admin_folder') . '/course');
                exit;
            }
        }
        $course_param               = $this->__role_query_filter;
        $course_param['course_id']  = $id;
        $course_param['limit']      = 1;
        $course                     = $this->Course_model->course_new($course_param);

        if($course['cb_discussion_instruction']=='')
        {
            $save                               = array();
            $save['id']                         = $id;
            $save['cb_discussion_instruction']  = $this->get_instruction();
            $this->Course_model->save($save);
            $this->memcache->delete('course_' . $id);
            $this->memcache->delete('course_mob'.$id);
            $this->invalidate_course(array('course_id'=>$id));
        }
        
        $data                       = array();
        $data['id']                 = $id;
        if(empty($course))
        {
            redirect($this->config->item('admin_folder').'/course');
        }

        $data['course']                     = $course;
        $data['cb_discussion_instruction']  = $course['cb_discussion_instruction'];
        $data['default_instruction']        = $this->get_instruction();

        $breadcrumb         = array();
        $breadcrumb[]       = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]       = array( 'label' => lang('course_bar_trainings'), 'link' => admin_url('course'), 'active' => 'active', 'icon' => '' );
        $breadcrumb[]       = array( 'label' => $course['cb_title'], 'link' => admin_url('course/basic/' . $course['id']), 'active' => 'active', 'icon' => '' );
        $breadcrumb[]       = array('label' => 'Discussion Instruction', 'link' => '', 'active' => 'active', 'icon' => '');
        $data['breadcrumb'] = $breadcrumb;
        
        $this->load->helper('form');
        $this->load->library(array('form_validation'));
        $this->form_validation->set_rules('cb_discussion_instruction', 'Discussion Instruction','required');
        if ($this->form_validation->run() == FALSE)
        {
            $data['errors'] = validation_errors(); 
            $this->load->view($this->config->item('admin_folder').'/course_discussion_instruction',$data);
        }
        else
        {
            $save                               = array();
            $save['id']                         = $id;
            $save['cb_discussion_instruction']  = $this->input->post('cb_discussion_instruction');
            
            $this->Course_model->save($save);
            $this->memcache->delete('course_' . $id);
            $this->memcache->delete('course_mob'.$id);
            $this->invalidate_course(array('course_id'=>$id));
            $this->session->set_flashdata('message', 'Course Discussion Instruction saved successfully');
            redirect($this->config->item('admin_folder').'/course/discussion_instruction/'.$id.'/');
        }
    }
    
    public function change_reviews_status()
    {
        if(!isset($this->review_permission) || !in_array($this->privilege['edit'], $this->review_permission))
            {
                $this->session->set_flashdata('message', 'No privilege access to change status');
                redirect($this->config->item('admin_folder') . '/course');
                exit;
            }
        //print_r($this->input->post()); die;
        $review_id      = (preg_match('/^[1-9]+[0-9]*$/', $this->input->post('review_id')) == true ) ? $this->input->post('review_id') : false;
        $status         = (preg_match('/^[012]$/', $this->input->post('status')) == true ) ? $this->input->post('status') : false;
        $course_id      = (preg_match('/^[1-9]+[0-9]*$/',$this->input->post('course_id')) == true) ? $this->input->post('course_id') : false;
        $user_id        = (preg_match('/^[1-9]+[0-9]*$/',$this->input->post('user_id')) == true)? $this->input->post('user_id') : false;
        $response       = array();

        if($review_id && $status!== false && $course_id)
        {
            $status_label   = $status ? 'Publish' : 'Unpublish';
            $filter                 = array();
            $filter['update']       = true;
            $filter['review_id']    = $review_id;

            $save_param                 = array();
            $save_param['cc_status']    = $status;
            $result                     = $this->Announcement_Model->change_review_status($save_param,$filter);
            // $response                   = array();
            $response['message']        = 'failed to '.$status_label.' the review';
            $response['error']          = true;
            if($result)
            {
                $response['message']        = 'Review '.$status_label.'ed successfully';
                $response['error']          = false;

                $result = $this->Course_model->get_course_rating(['course_id' => $course_id ]);
                if($result['rating_count'] != 0)
                {
                    $rating = $result['rating_sum'] / $result['rating_count'];
                }
                else
                {
                    $rating = 0;
                }    
                $this->Course_model->update_item_sort_order_rating(['course_id' => $course_id, 'item_type' => 'course', 'rating' => $rating ]);
            }
            $this->memcache->delete('course_' . $course_id);
            $this->memcache->delete('course_mob'.$course_id);
            $this->memcache->delete('subscription_'.$course_id.'_'.$user_id);
            $this->memcache->delete('all_courses');
            $this->memcache->delete('sales_manager_all_sorted_courses');
            $this->memcache->delete('top_courses');
            $this->memcache->delete('popular_courses');
            $this->memcache->delete('featured_courses');
            $this->memcache->delete('courses');
            $this->invalidate_course(array('course_id'=>$course_id));
            $this->invalidate_subscription(array('user_id'=>$user_id,'course_id'=>$course_id));
        }
        else
        {
            $response['error']   = true;
            $response['message'] = lang('error_unknown_message');
        }
        echo json_encode($response);
    }

    public function admin_review_reply()
    {
        if(!isset($this->review_permission) || !in_array($this->privilege['edit'], $this->review_permission))
            {
                $this->session->set_flashdata('message', 'No privilege access to reply');
                redirect($this->config->item('admin_folder') . '/course');
                exit;
            }
        $admin_reply = $this->input->post('admin_reply');
        $save                   = '';
        $response               = array();
         
        if($admin_reply){
            $save                   = array();
            //$save['rvs_parent_id']   = $this->input->post('review_id');
            $save['cc_user_name']   = $this->__loggedInUser['us_name'];
            $save['cc_user_id']     = $this->__loggedInUser['id'];
            $save['cc_us_image']    = $this->__loggedInUser['us_image'];
            $save['created_date']   = date('Y-m-d H:i:s');
            $save['cc_course_id']   = $this->input->post('course_id');
            $save['cc_review_reply']= $admin_reply;
        }
        
        if($this->Course_model->save_review(array('id' => $this->input->post('review_id'), 'cc_admin_reply' => json_encode($save))))
        {
            $response['message']        = 'Reply successfully saved';
            $response['error']          = false;
            echo json_encode($response);
            $this->invalidate_course(array('course_id' => $this->input->post('course_id')));
            return;
        }

        $response['message']            = 'Failed to save reply';
        $response['error']              = true;
        echo json_encode($response);
        return;
    }

    public function delete_review()
    {
        if(!isset($this->review_permission) || !in_array($this->privilege['delete'], $this->review_permission))
        {
                $this->session->set_flashdata('message', 'No privilege access to delete the review');
                redirect($this->config->item('admin_folder') . '/course');
                exit;
        }
            
        //$delete_rv                 = array();
        $delete_rt                 = array();
        $response                  = array();
        $review_id                 = (preg_match('/^[1-9]+[0-9]*$/', $this->input->post('review_id')) == true) ? $this->input->post('review_id') : false;
        $course_id                 = (preg_match('/^[1-9]+[0-9]*$/', $this->input->post('course_id')) == true) ? $this->input->post('course_id') : false;
        $user_id                   = (preg_match('/^[0-9]+$/', $this->input->post('user_id')) == true) ? $this->input->post('user_id') : false;
        $delete_rt['table']        = 'course_ratings';
        $delete_rt['where']        = array('course_ratings.id' => $review_id);
        
        if($review_id && $course_id )
        {
            if($this->Course_model->delete_row($delete_rt))
            {
                //$this->Course_model->delete_row($delete_rt);
                $response['message']        = 'Review successfully deleted';
                $response['error']          = false;
                echo json_encode($response);
                $this->memcache->delete('subscription_'.$course_id.'_'.$user_id);
                
                $this->invalidate_subscription(array('user_id'=>$user_id,'course_id'=>$course_id));
                $this->invalidate_course(array('course_id' => $course_id));
                $this->memcache->delete('all_courses');
                $this->memcache->delete('sales_manager_all_sorted_courses');
                $this->memcache->delete('top_courses');
                $this->memcache->delete('popular_courses');
                $this->memcache->delete('featured_courses');
                $this->memcache->delete('courses');
        
                return;
            }
            else
            {
                $response['message']            = 'Failed to delete the review';
                $response['error']              = true;
                echo json_encode($response);
                return;
            }
        }
        else
        {
            $response['message']            = lang('error_unknown');
            $response['error']              = true;
            echo json_encode($response);
            return;
        }
        
    }
    //Function to export all bundle review
    public function export_reviews($params)
    {
        if(!isset($this->review_permission) || !in_array($this->privilege['view'], $this->review_permission))
            {
                $this->session->set_flashdata('message', 'No privilege access');
                redirect($this->config->item('admin_folder') . '/course');
                exit;
            }

        $params                         = base64_decode($params);
        $params                         = (array)json_decode($params);
        $data                           = array();
        //print_r($params);
        $params['order_by']             = 'id';
        $params['direction']            = 'DESC';
        $params['block']                = false;
        
        $data['reviews']                = $this->Course_model->get_course_review($params);
        $course_details                 = $this->Course_model->course_new($params);
        isset($course_details) ? $data['cb_title'] = $course_details[0]['cb_title'] : '';
        $data['report_title']           = 'Review report of '.$data['cb_title'];
        //echo '<pre>'; print_r($data);die;
        $this->load->view($this->config->item('admin_folder').'/export_course_reviews', $data);
    }

    public function check_course_valid()
    {
        $course_ids             = $this->input->post('course_ids');
        $course_ids             = empty(json_decode($course_ids)) ? array():json_decode($course_ids);
        $objects                = array();
        $objects['key']         = 'courses';
        $callback               = 'courses';
        $courses                = $this->memcache->get($objects, $callback,array());
        $course_list            = array();
        $active_list            = array();
        $response               = array();
        foreach($courses as $course)
        {
            $course_id          = isset($course['id'])?$course['id']:'';
            $lectures_count     = isset($course['lectures'])?count($course['lectures']):0;
            $error_count        = 0;
            if(in_array($course_id, $course_ids))
            {
                
                if ($course['cb_image'] == 'default.jpg' && $course['cb_status'] == '0') 
                {
                    $error_count ++;
                }
                if (strip_tags($course['cb_description']) == '' && $course['cb_status'] == '0') 
                {
                    $error_count ++;
                }
                if ($course['cb_category'] == '' && $course['cb_status'] == '0') 
                {
                    $error_count ++;
                }
                if ($lectures_count < 1 && $course['cb_status'] == '0') 
                {
                    $error_count ++;
                }
                // if($course['cb_access_validity'] == 2){

                //     $today              = date('Y-m-d H:i:s');
                //     $valid_till         = $course['cb_validity_date'];
                //     $validity_expired   = strtotime($valid_till) > strtotime($today)?false:true;
                //     if($validity_expired){
                //         $error_count ++;
                //     }
                // }

                if($error_count > 0)
                {
                    $course_list[] = $course['cb_code'].' : '.$course['cb_title'];
                }
                else
                {
                    $active_list[] = $course['id'];
                }
            }
                        
        }
        $courses_name                   = empty($course_list)?array():implode(',',$course_list); 
        $response['error']              = false;
        $response['course_list']        = '';
        if(!empty($courses_name))
        {
            $response['error']          = true;
            $response['course_list']    = $courses_name;  
        }
        $response['active_course_list'] = $active_list;
        // echo "<pre>";print_r($response);exit;
        echo json_encode($response);
    }
    private function get_instruction()
    {
        return '<div id="dvInstruction">
            <p class="headings-altr"><strong>Discussion Forum Instructions:</strong></p>
            <ol class="header-child-alt">
            <li>The topics in the discussion forum have to be connected to the course. Think of what you want to gain from the discussions; this will help you post related online discussion activities.</li>
            <li>You can post a question as well as an answer to questions posted by your friends in the forum.</li>
            <li>Online discussions should be regularly used as they help maintain the learning momentum.</li>
            <li>Discussions should be held simultaneously with course, which will help you to understand the topics that you are learning.</li>
            <li>Your active participation in the discussion forum is noted and appreciated.</li>
            <li>Subject matter experts or industry experts shall be participating in the discussion forum to answer your queries, which will add new insights to the topic and enrich the quality of your learning.</li>
            <li>One will be blocked from the forum if the discussion is going off-track, argumentative and abusing.</li>
            </ol>
           </div>';
    }

    public function migrateCourseSubscription($params = array())
    {
        if(isset($params['bundle_id']) && isset($params['course_ids']))
        {
            
        }
        else
        {
            return false;
        }
    }

    //Curl to delete mobile api get subscriptions memcache
    private function reset_mobile_subscriptions($notify_param = array())
    {
        $curlHandle                         = curl_init(site_url()."cron_job/update_mobile_subscription_courses");

        $defaultOptions                     = array (
                                                CURLOPT_POST => 1,
                                                CURLOPT_POSTFIELDS => json_encode($notify_param),
                                                CURLOPT_RETURNTRANSFER => false ,
                                                CURLOPT_TIMEOUT_MS => 100,
                                            );
        curl_setopt_array($curlHandle , $defaultOptions);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, FALSE);     
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2); 
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array(
            'request-token: '.sha1(config_item('acct_domain').config_item('id')),
        ));
        $result                              = curl_exec($curlHandle);
        curl_close($curlHandle);
    }
}
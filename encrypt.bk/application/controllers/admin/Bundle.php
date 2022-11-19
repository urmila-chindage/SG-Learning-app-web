<?php
class Bundle extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->lang->load('bundle');
        date_default_timezone_set('Asia/Kolkata');
        
        $this->__admin_index        = 'admin';
        $this->__loggedInUser       = $this->auth->get_current_user_session('admin');
        $redirect                   = $this->auth->is_logged_in(false, false);
        $this->__role_query_filter  = array();
        if (!$redirect) {            
            redirect('login');
        }
        if($this->__loggedInUser['us_role_id'] == 8)
        {
            $this->__role_query_filter['institute_id'] = $this->__loggedInUser['us_institute_id'];
        }
        $this->actions = $this->config->item('actions');
        $this->load->model(array('Bundle_model','Routes_model','Group_model','Course_model','Category_model','User_model'));
        $this->load->helper(array('form','url'));
        $this->load->library(array('form_validation','upload'));
        $this->__limit = 100;
        
            
        $this->__access = array('view' => 1, 'add' => 2, 'edit' => 3, 'delete' => 4);
        
        $access_method                    = ($this->__loggedInUser['rl_full_course'] == '1')?'get_permission':'get_permission_course';

        // $this->user_privilege             = $this->accesspermission->get_permission(array('role_id' => $this->__loggedInUser['role_id'],'module' => 'user'));   
        // $this->course_privilege           = $this->accesspermission->get_permission(array('role_id' => $this->__loggedInUser['role_id'],'module' => 'course'));
        // $this->forum_privilege            = $this->accesspermission->get_permission(array('role_id' => $this->__loggedInUser['role_id'], 'module' => 'course_forum'));
        // $this->report_privilege           = $this->accesspermission->get_permission(array('role_id' => $this->__loggedInUser['role_id'], 'module' => 'report'));
        // $this->event_privilege            = $this->accesspermission->get_permission(array('role_id' => $this->__loggedInUser['role_id'],'module' => 'event'));
        $this->__bundle_privilege            = $this->accesspermission->get_permission(array('role_id' => $this->__loggedInUser['role_id'],'module' => 'bundle'));
        $this->__bundle_student_enrollment_privilege = $this->accesspermission->get_permission(array('role_id' => $this->__loggedInUser['role_id'],'module' => 'bundle_student_enrollment'));
        $this->review_permission          = $this->accesspermission->get_permission(array( 'role_id' => $this->__loggedInUser['role_id'], 'module' => 'review' ));
        if(!in_array($this->__access['view'], $this->__bundle_privilege))
        {
            redirect(admin_url());
        }
    }
    public function index(){

        // if(!in_array('1', $this->course_privilege))
        // {
        //     redirect(admin_url()); exit;
        // }
    
        $data                               = array();
        $breadcrumb                         = array();
        $offset                             = 0;
        $breadcrumb[]                       = array('label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>');
        $breadcrumb[]                       = array('label' => lang('bundle_bar'), 'link' => '', 'active' => 'active', 'icon' => '');
        $data['breadcrumb']                 = $breadcrumb;
        $data['title']                      = lang('bundle_bar');
        
        $categories_param                   = array();
        $categories_param['direction']      = 'DESC';
        $categories_param['not_deleted']    = true;
        $categories_param['select']         = 'id, ct_name';
        $data['categories']                 = $this->Category_model->categories($categories_param);
        $data['limit']                      = $this->__limit;
        $data['show_load_button']           = false;

        $bundle_param                       = array();
        $bundle_param['direction']          = 'ASC';
        $bundle_param['select']             = 'id,c_title,c_code,c_is_free,c_courses,c_status,c_deleted,c_access_validity,c_validity,c_validity_date';
        $bundle_param['order_by']           = 'c_title';
        $bundle_param['count']              = true;
        $bundle_param['filter']             = ($this->input->get('filter') != null)? $this->input->get('filter') : $this->input->post('filter');
        $bundle_param['category_id']        = ($this->input->get('category') != null)? $this->input->get('category') :$this->input->post('category');
        $keyword                            = ($this->input->get('keyword') != null)? $this->input->get('keyword') :$this->input->post('keyword');
        if($keyword != null)
        {
            $keyword_arr                    = explode('-', $keyword);
            $keyword                        = implode(' ',$keyword_arr);
            $bundle_param['keyword']        = trim($keyword);
        }
              
        if($bundle_param['filter'] == '') 
        {
            $bundle_param['not_deleted']    = true;
            $bundle_param['status']         = '1';
        }       
        $total_bundles                      = $this->Bundle_model->bundles($bundle_param);
        $data['total_bundles']              = $total_bundles;
        unset($bundle_param['count']);
        $limit                              = $this->input->post('limit');
        $bundle_param['limit']              = empty($limit)?$this->__limit:$limit;
        
        $page                               = $offset;
        if(!empty($this->input->post('offset'))){

            $offset                         = $this->input->post('offset');
            if ($offset === null || $offset <= 0) {
                $offset                     = "1";
            }
            $page                           = ($offset - 1) * $limit;
            
        }
        $bundle_param['offset']             = $page;
        $data['bundles']                    = $this->Bundle_model->bundles($bundle_param);
        
        if($this->input->post('is_ajax') == true){
            
            if ($total_bundles > ($this->__limit * $offset)) {
                $data['show_load_button']   = true;
            }
            unset($data['breadcrumb']); 
            unset($data['categories']);  
            echo json_encode($data);
        }else{
            if ($data['total_bundles'] > $this->__limit) {
                $data['show_load_button']   = true;
            }
            //echo '<pre>'; print_r($data); die;
            $this->load->view($this->config->item('admin_folder') . '/bundle_list', $data);
        }
       
    }
    //create bundle with bun dle code and bundle name
    public function create_bundle(){

        $response               = array();
        
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('', '</br>');
        $this->form_validation->set_rules('bundle_name', 'Bundle Name', 'trim|required|callback_name_check[0]');
        $this->form_validation->set_rules('bundle_code', 'Bundle Code', 'trim|required|max_length[5]|alpha_numeric|callback_code_check[0]');
        
        if ($this->form_validation->run() == FALSE)
        {
            $response['error']   = true;
            $response['message'] = validation_errors();
        }
        else
        {
            $bundle_name    = trim($this->input->post('bundle_name'));
            $bundle_code    = trim($this->input->post('bundle_code'));

            //create slug for SEO friendly url
            $this->load->helper('text');
            $this->load->model('Routes_model');
            $slug           = $bundle_name;
            $slug           = url_title(convert_accented_characters($slug), 'dash', true);
            $slug           = $this->Routes_model->validate_slug($slug);
            $route['slug']  = $slug;
            $route_id       = $this->Routes_model->save($route);

            //save bundle
            $save                   = array();
            $save['id']             = false;
            $save['c_title']        = $bundle_name;
            $save['c_code']         = $bundle_code;
            $save['action_by']      = $this->auth->get_current_admin('id');
            $save['c_route_id']     = $route_id;
            $save['c_slug']         = $slug;
            $save['c_account_id']   = $this->config->item('id');
            $bundle_id              = $this->Bundle_model->save($save);

            $route['id']            = $route_id;
            $route['slug']          = $slug;
            $route['route']         = 'bundle/basic/' . $bundle_id;
            $route['r_account_id']  = $this->config->item('id');
            $route['r_item_type']   = 'bundle';
            $route['r_item_id']     = $bundle_id;
            $this->Routes_model->save($route);

            if($bundle_id){

                $response['id']         = $bundle_id;
                $response['error']      = false;
                $response['message']    = lang('bundle_created_success');
            }else{
                $response['error']      = true;
                $response['message']    = lang('bundle_created_fail');
            }
           
            // $user_data                          = array();
            // $user_data['user_id']               = $this->__loggedInUser['id'];
            // $user_data['username']              = $this->__loggedInUser['us_name'];
            // $user_data['user_type']             = $this->__loggedInUser['us_role_id']; ;

            // $message_template                   = array();
            // $message_template['username']       = $this->__loggedInUser['us_name'];;
            // $message_template['course_name']    = $bundle_name;
            // $triggered_activity                 = 'course_created';

            // log_activity($triggered_activity, $user_data, $message_template);

        }
        echo json_encode($response);
        exit;
    }
    function code_check($code, $id)
    {
        $param                      = array();
        $param['exclude_id']        = ($id != '0')? $id : false;
        $param['code']              = $code;
        $param['select']            = 'c_code, c_title';
        $param['limit']             = '1';
        $code_available             = $this->Bundle_model->bundle($param);
        
        if (!empty($code_available))
        {
            $this->form_validation->set_message('code_check', 'The Bundle code <b>'.$code.'</b> is already used by <b>'.((strlen($code_available['c_title'])>30)?substr($code_available['c_title'], 0, 25).'...':$code_available['c_title']).'</b>');
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }
    function name_check($name, $id)
    {
        
        $param                      = array();
        $param['exclude_id']        = ($id != '0')? $id : false;
        $param['name']              = $name;
        $param['select']            = 'c_code, c_title';
        $param['limit']             = '1';
        $name_available             = $this->Bundle_model->bundle($param);
        //print_r($name_available);
        if (!empty($name_available))
        {
            $this->form_validation->set_message('name_check', 'The Bundle name <b>'.$name.'</b> is already used </b>');
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }
    //Function to check bundle exist
    function ajax_name_check()
    {
        $id                         = $this->input->post('b_id');
        $name                       = $this->input->post('cb_title');
        $param                      = array();
        $param['exclude_id']        = ($id != '0')? $id : false;
        $param['name']              = $name;
        $param['select']              = 'id, c_code, c_title';
        $param['limit']             = '1';
        $name_available             = $this->Bundle_model->bundle($param);
        //print_r($name_available);die;
        //echo $this->db->last_query(); die;
        if (!empty($name_available))
        {
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
    //Funuction to delete an bundle with bundle id
    public function delete_check()
    {
        $response                   = array();
        $bundle_id                  = $this->input->post('bundle_id');
        if(!empty($bundle_id ))
        {
            $bundle_param               = array();
            $bundle_param['bundle_id']  = $bundle_id;
            $bundle_param['select']     = 'id, c_title';
            $bundle_param['limit']      = '1';
            $bundle                     = $this->Bundle_model->bundle($bundle_param);
        
            if (!$bundle) {
                $response['success']    = false;
                $response['message']    = lang('bundle_not_found');
                echo json_encode($response);
                exit;
            }
            else
            {
                $subscription_alert         = '';
                $enroll_param               = array();
                $enroll_param['bundle_id']  = $bundle_id;
                $enroll_param['count']      = true;
                $subscription_count         = $this->Bundle_model->enrolled($enroll_param);
                if ($subscription_count > 0) {
                    $student_text           = ($subscription_count == 1) ? ' Student' : ' Students';
                    $subscription_alert     =  $subscription_count . $student_text . ' enrolled on this Bundle. ';
                
                }
                
                $response['success']        = true;
                $response['message']        = $subscription_alert; 
            }
             
        }
        else
        {
            $response['success']        = false;
            $response['message']        = lang('bundle_not_delete');
        }
        
        echo json_encode($response);
    }

    // change status of an bundle to delete if bundle id present
    public function delete()
    {
        $response                   = array();
        $bundle_id                  = $this->input->post('bundle_id');

        $save                       = array();
        $param                      = array();
        $save['action_by']          = $this->auth->get_current_admin('id');
        $save['updated_date']       = date('Y-m-d H:i:s');
        $save['c_deleted']          = '1';
        $param['id']                = $bundle_id;
        $param['update']            = true;
        if(!empty($bundle_id))
        {
            $result                 = $this->Bundle_model->save($save ,$param);
            if($result)
            {
                $bundle_param                   = array();
                $bundle_param['bundle_id']      = $bundle_id;
                $bundle_param['select']         = 'id,c_courses';
                $bundle_param['limit']          = '1';
                $bundle_param['not_deleted']    = '1';
                $bundle                         = $this->Bundle_model->bundle($bundle_param);
                $enrolled_courses                   = json_decode($bundle['c_courses'],true);
                $bundle_courses_ids                 = !empty($enrolled_courses)?array_column($enrolled_courses, 'id'):array();
                if($bundle_id && $bundle_courses_ids)
                {
                    $params                     = array();
                    $params['removefrombundle'] = true;
                    $params['course_ids']       = $bundle_courses_ids;
                    $params['bundle_id']        = $bundle_id;
                    $course_subscription_removed= $this->Bundle_model->migrateCourseSubscription($params);
                    //print_r($course_subscription_removed);//die;
                    if(!empty($course_subscription_removed))
                    {
                        foreach($course_subscription_removed as $user_id)
                        {
                            $this->memcache->delete('enrolled_'.$user_id);
                            $this->memcache->delete('mobile_enrolled_'.$user_id);
                        }
                    }
                    
                }
            }
            //To delete mobile api get subscriptions memcache
            $bundle_param                       = array();
            $bundle_param['id']                 = $bundle_id;
            $this->reset_mobile_subscriptions($bundle_param);
        }
        else
        {
            $result                 = false;   
        }
        if (!$result) {
            $response['error']      = true;
            $response['message']    = 'Bundle delete failed.';
        }else{

            $bundle_param                   = array();
            $bundle_param['bundle_id']      = $bundle_id;
            $bundle_param['select']         = 'id,c_title,c_code,c_is_free,c_courses,c_status,c_deleted,c_access_validity,c_validity,c_validity_date';
            $bundle_param['limit']          = '1';
            $bundle_param['not_deleted']    = '1';
            $bundle                         = $this->Bundle_model->bundle($bundle_param);
            
            
            $response['error']              = false;
            $response['bundle']             = $bundle;
            $response['message']            = 'Deleted successfully';
        }
        // $user_data              = array();
        // $user_data['user_id']   = $this->__loggedInUser['id'];
        // $user_data['username']  = $this->__loggedInUser['us_name'];
        // $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
        
        // $message_template                   = array();
        // $message_template['username']       = $this->__loggedInUser['us_name'];;
        // $message_template['course_name']    = $course['cb_title'];
        
        // $triggered_activity                 = 'course_deleted';
        // log_activity($triggered_activity, $user_data, $message_template); 
        $this->invalidate_bundle(array('bundle_id' => $bundle_id));
        $this->invalidate_bundle();
        echo json_encode($response);
    }
    public function language()
    {
        $response = array();
        $response['language'] = array();
        $response['language'] = get_instance()->lang->language;
        echo json_encode($response);
    }

    //Activating the bundle with status
    public function change_status()
    {
        $response                   = array();
        $bundle_id                  = $this->input->post('bundle_id');
        $bundle_param               = array();
        $bundle_param['bundle_id']  = $bundle_id;
        $bundle_param['limit']      = '1';
        $bundle_param['select']     = 'id, c_image,c_courses,c_description,c_category,c_status, c_title,c_code, c_deleted,c_access_validity,c_validity,c_validity_date';
        $bundle                     = $this->Bundle_model->bundle($bundle_param);
        // echo "<pre>";print_r($bundle);exit;
        if ($bundle['c_image'] == 'default.jpg' && $bundle['c_status'] == '0') {
            $response['error']      = true;
            $response['message']    = lang('upload_image_to_publish');
            echo json_encode($response);
            exit;
        }
        if (strip_tags($bundle['c_description']) == '' && $bundle['c_status'] == '0') {
            $response['error']      = true;
            $response['message']    = lang('decription_to_publish');
            echo json_encode($response);
            exit;
        }
        if ($bundle['c_category'] == '0' && $bundle['c_status'] == '0') {
            $response['error']      = true;
            $response['message']    = lang('category_to_publish');
            echo json_encode($response);
            exit;
        }
        $bundle_lectures_count      = isset($bundle['c_courses'])?count(json_decode($bundle['c_courses'],true)):'';
        if ($bundle_lectures_count < 1 && $bundle['c_status'] == '0') {
            $response['error']      = true;
            $response['message']    = lang('error_change_status');
            echo json_encode($response);
            exit;
        }
        $save                       = array();
        $param                      = array();
        $param['id']                = $bundle_id;
        $param['update']            = true;
        $save['action_by']          = $this->auth->get_current_admin('id');
        $save['updated_date']       = date('Y-m-d H:i:s');
        $save['c_status']           = '1';

        $response['message']        = lang('published');
        if ($bundle['c_status'] == '1') {            
            $save['c_status']       = '0';
            $response['message']    = lang('unpublished');
        }
        if(!empty($param['id']))
        {
            $bundle['c_status']     = $save['c_status'];
            $result                 = $this->Bundle_model->save($save,$param);
        }
        else
        {
            $result                 = "";
        }
        
        if (!$result) {
            $response['error']      = true;
            $response['message']    = lang('error_change_status');
        }else{
            $response['error']      = false;
            $response['bundle']     = $bundle;
        }
        
        // $user_data              = array();
        // $user_data['user_id']   = $this->__loggedInUser['id'];
        // $user_data['username']  = $this->__loggedInUser['us_name'];
        // $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
        
        // $message_template                   = array();
        // $message_template['username']       = $this->__loggedInUser['us_name'];;
        // $message_template['course_name']    = $course['cb_title'];
        
        // $triggered_activity                 = $mstatus;
        // log_activity($triggered_activity, $user_data, $message_template); 
        $this->invalidate_bundle(array('bundle_id' => $bundle_id));
        $this->invalidate_bundle();
        echo json_encode($response);
       
    }
    // change status of bundle bulk 
    public function change_status_bulk()
    {
        $response           = array();
        $all_error_count    = 0;
        $error_msg          = '';
        $status             = $this->input->post('status_bulk');
        $bundles            = json_decode($this->input->post('bundles'));
        if (!empty($bundles)) 
        {
            $response['error_bundles']  = array();
            $response['bundles']        = array();
            foreach ($bundles as $bundle_id) {
                $save                   = array(); 
                $param                  = array(); 
                $error_count            = 0;
                $param['id']            = $bundle_id;
                $param['update']        = true;
                $save['c_status']       = $status;
                $save['action_by']      = $this->auth->get_current_admin('id');
                $save['updated_date']   = date('Y-m-d H:i:s');
                
                $bundle_param               = array();
                $bundle_param['bundle_id']  = $bundle_id;
                $bundle_param['limit']      = '1';
                $bundle_param['select']     = 'id, c_code,c_image,c_courses,c_description,c_category,c_status, c_title, c_deleted,c_access_validity,c_validity,c_validity_date';
                $bundle                     = $this->Bundle_model->bundle($bundle_param);
                
                if( $status == 0 )
                {
                    $bundle['c_status']     = '0';
                    $this->Bundle_model->save($save,$param);
                    $response['bundles'][]  = $bundle;
                }
                else
                {
                    if ($bundle['c_image'] == 'default.jpg' && $bundle['c_status'] == '0') {
                        $error_count++;
                    }
                    if (strip_tags($bundle['c_description']) == '' && $bundle['c_status'] == '0') {
                        $error_count++;
                    }
                    if ($bundle['c_category'] == '0' && $bundle['c_status'] == '0') {
                        $error_count++;
                    }
                    $bundle_lectures_count      = isset($bundle['c_courses'])?count(json_decode($bundle['c_courses'],true)):'';
                    if ($bundle_lectures_count < 1 && $bundle['c_status'] == '0') {
                        $error_count++;
                    }

                    if ( $error_count == 0) {
                        $bundle['c_status']     = '1';
                        $this->Bundle_model->save($save,$param);
                        $response['bundles'][]  = $bundle;
                    }else{
                        $response['error_bundles'][]  = array('bundle_code'=>$bundle['c_code'],'bundle_id'=>$bundle['id'],'bundle_name' => $bundle['c_title']);
                        $all_error_count++;
                    }
                    
                }    
                $this->invalidate_bundle(array('bundle_id' => $bundle_id));
                $this->invalidate_bundle();
            }
            if ( $all_error_count == 0) {
                $response['error']      = false;
                $response['message']    = ($status == 1)? 'PUBLIC':'PRIVATE';
            }
            if ($all_error_count > 0) {
                $response['error']          = true;
                $response['message']        = 'Activation failed';
            }
            
        } else {
            $response['error']  = true;
            $response['message']= 'No Bundles choosen';
        }
        
        echo json_encode($response);
    }
    //BUNDLE SETTING FUNCTION
    public function basic($id =false)
    {
        if (!$id) 
        {
            $this->session->set_flashdata('message', lang('bundle_not_found'));
            redirect($this->config->item('admin_folder') . '/bundle');
        }
        
        $bundle_param               = array();
        $bundle_param['bundle_id']  = $id;
        $bundle_param['limit']      = '1';
        $bundle_param['select']     = 'id,c_title,c_description,c_courses,c_status, c_image,c_category,c_is_free,c_price,c_discount,c_tax_method,c_access_validity,c_validity,c_validity_date,c_rating_enabled';
        $bundle                     = $this->Bundle_model->bundle($bundle_param);
        
        if (!$bundle) 
        {
            $this->session->set_flashdata('message', lang('bundle_not_found'));
            redirect($this->config->item('admin_folder') . '/bundle');
        }
        $bundle_lectures_count      = isset($bundle['c_courses'])?count(json_decode($bundle['c_courses'],true)):'';
        // if ($bundle_lectures_count < 1 && $bundle['c_status'] == '0') {
        //     $this->session->set_flashdata('message', lang('error_change_status'));
        //     redirect($this->config->item('admin_folder') . '/bundle/basic/'.$id);
        // }
        //fetching categories
        $this->memcache->delete('categories');
        $objects                    = array();
        $objects['key']             = 'categories';
        $callback                   = 'get_categories';
        $categories                 = $this->memcache->get($objects, $callback,array()); 

        $data['id']                 = $bundle['id'];
        $data['c_title']            = $bundle['c_title'];
        $data['c_description']      = $bundle['c_description'];
        $data['c_courses']          = $bundle['c_courses'];
        $data['c_image']            = $bundle['c_image'];
        $data['c_category']         = explode(',', $bundle['c_category']);
        $data['c_is_free']          = $bundle['c_is_free'];
        $data['categories']         = $categories;
        $data['c_price']            = $bundle['c_price'];
        $data['c_discount']         = $bundle['c_discount'];
        $data['c_tax_method']       = $bundle['c_tax_method'];
        $data['c_access_validity']  = $bundle['c_access_validity'];
        $data['c_validity']         = $bundle['c_validity'];
        $data['c_validity_date']    = $bundle['c_validity_date'];
        $data['c_rating_enabled']   = $bundle['c_rating_enabled'];
        
        $gst_setting                = $this->settings->setting('has_tax');
        
        if ($gst_setting['as_setting_value']['setting_value']->cgst && $gst_setting['as_setting_value']['setting_value']->cgst != '') {
            $data['cgst'] = $gst_setting['as_setting_value']['setting_value']->cgst;
        } 
        else 
        {
            $data['cgst'] = 0;  
        } 
        
        if ($gst_setting['as_setting_value']['setting_value']->sgst && $gst_setting['as_setting_value']['setting_value']->sgst != '') {
            $data['sgst'] = $gst_setting['as_setting_value']['setting_value']->sgst;
        } 
        else 
        {
            $data['sgst'] = 0;  
        } 
     
        $data['bundle']             = array('id' => $id);
        $breadcrumb         = array();
        $breadcrumb[]       = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]       = array( 'label' => lang('bundle_bar'), 'link' => admin_url('bundle'), 'active' => 'active', 'icon' => '' );
        $breadcrumb[]       = array( 'label' => $bundle['c_title'], 'link' => admin_url('bundle/basic/' . $bundle['id']), 'active' => 'active', 'icon' => '' );
        $breadcrumb[]       = array( 'label' => 'Settings', 'link' => '', 'active' => 'active', 'icon' => '');
        
        $data['breadcrumb'] = $breadcrumb;
        //echo "<pre>";print_r($data);exit;
        
        $this->load->view($this->config->item('admin_folder') . '/bundle_basic',$data);
    }
    
    public function save_param($id='')
    { 
        $this->form_validation->set_rules('c_title', 'Bundle Title','required|callback_name_check['.$id.']');
        $this->form_validation->set_rules('c_description', 'Bundle Description','required');
        
        if ($this->form_validation->run() == FALSE)
        {
            $data['errors'] = validation_errors(); 
            redirect($this->config->item('admin_folder').'/bundle/basic/'.$id);
        }
        else
        {
            $filter                     = array();
            $filter['id']               = $id;
            $filter['update']           = true;

            $save['c_title']            = str_replace(array("\"", "&quot;", "<", ">", "{", "}"), "", htmlspecialchars($this->input->post('c_title')));
            $save['c_title']            = ltrim($save['c_title']," ");
            $save['c_description']      = $this->input->post('c_description');
            $save['c_category']         = $this->input->post('c_category');

            if(!empty($save['c_category'])){
                $save['c_category']     = implode(',', $save['c_category']);
            }
           
            $c_is_free                  = $this->input->post('c_is_free');
            $save['c_is_free']          = empty($c_is_free)?'0':$c_is_free;
            $save['c_price']            = $this->input->post('c_price');
            $save['c_discount']         = $this->input->post('c_discount');
            $save['c_tax_method']       = $this->input->post('c_tax_method');
            $save['c_validity_date']    = NULL;
            $save['c_access_validity']  = $this->input->post('c_access_validity');
            $save['c_rating_enabled']   = $this->input->post('c_rating_enabled');
            
            if($save['c_access_validity'] == 1 )
            {

                $save['c_validity_date']   = 0;
                $save['c_validity']        = $this->input->post('c_validity');
                
            }
            else if( $save['c_access_validity'] == 2)
            {

                $save['c_validity_date']   = date('Y-m-d',strtotime($this->input->post('c_validity_date')));
                $save['c_validity']        = 0;
            }
            else
            {
                $save['c_validity_date']   = 0;
                $save['c_validity']        = 0;
            }
            
            /*====================== file uploading ======================== */
            //As file upload error "No file selected" is number 4,
            //When checking by name or tmp_name, there might be other reasons why these fields didn't get populated, and you may miss these.
            if($_FILES['c_image']['error'] !== 4){
                $this->upload_course_image_to_localserver(array('bundle_id'=>$id));
            }
       
            /*================================================================= */
            $save['action_by']              = $this->__loggedInUser['id'];
            $save['action_id']              = '1';
            $save['updated_date']           = date('Y-m-d H:i:s');
            $this->Bundle_model->save($save,$filter);
            
            // $user_data              = array();
            // $user_data['user_id']   = $this->__loggedInUser['id'];
            // $user_data['username']  = $this->__loggedInUser['us_name'];
            // $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
            
            // $message_template                    = array();
            // $message_template['username']        = $this->__loggedInUser['us_name'];;
            // $message_template['course_name']     =  $save['cb_title'];

            // $triggered_activity                  = 'course_updated';
            // log_activity($triggered_activity, $user_data, $message_template); 
            
            $template = 'message';
            $message  = 'Bundle saved successfully';
            
            $this->invalidate_bundle(array('bundle_id' => $id, 'key' => 'bundle_'.$id));
            $this->invalidate_bundle();


            $savenext = $this->input->post('savenext');
            $this->session->set_flashdata($template, $message);
            redirect($this->config->item('admin_folder').'/bundle/basic/'.$id);
           
        }
    } 

    public function invalidate_bundle($param = array())
    {
        //Invalidate cache
        $bundle_id = isset($param['bundle_id']) ? $param['bundle_id'] : false;
        if(isset($param['key']))
        {
            $this->memcache->delete($param['key']);
        }
        if ($bundle_id) {
            $this->memcache->delete('bundle_' . $bundle_id);
            $this->memcache->delete('bundle_mob'. $bundle_id);
            $this->memcache->delete('mobile_bundle_'. $bundle_id); 
        } else {
            $this->memcache->delete('all_courses');
            $this->memcache->delete('sales_manager_all_sorted_courses');
            $this->memcache->delete('top_courses');
        }
        $this->memcache->delete('popular_courses');
        $this->memcache->delete('featured_courses');
        $this->memcache->delete('all_sorted_course');
    }

    function upload_course_image_to_localserver($param=array())
    {
        $bundle_id              = isset($param['bundle_id'])?$param['bundle_id']:'default';
        $directory              = catalog_upload_path(array('bundle_id' => $bundle_id));
        if(!file_exists($directory)){
            mkdir($directory, 0777, true);
        }
        $config                     = array();
        $config['upload_path']      =  $directory;
        $config['allowed_types']    = 'gif|jpg|png|jpeg';
        $new_name                   = $bundle_id.'.jpg';
        $config['file_name']        = $new_name;
        $config['overwrite']        = TRUE;

        $this->upload->initialize($config);
        $this->upload->do_upload('c_image');
        $uploaded_data = $this->upload->data();

        $save                   = array();
        $filter                 = array();
        $filter['id']           = $bundle_id;
        $filter['update']       = true;
        $save['c_image']        = $uploaded_data['file_name']."?v=".rand(10,1000);
        $save['action_by']      = $this->auth->get_current_admin('id');
        $save['updated_date']   = date('Y-m-d H:i:s');
        $this->Bundle_model->save($save,$filter);
        
        $config                     = array();
        $config['uploaded_data']    = $uploaded_data;
        //echo '<pre>';print_r($uploaded_data);die;
        //convert to given size and return orginal name
        $config['bundle_id']        = $bundle_id;
        $config['width']            = 739;
        $config['height']           = 417;
        $config['orginal_name']     = true;
        $new_file                   = $this->crop_image($config);//orginal name
        
        $config['width']            = 300;
        $config['height']           = 160;
        $config['orginal_name']     = false;
        $new_file_medium            = $this->crop_image($config);
        
        $config['width']            = 85;
        $config['height']           = 85;
        $config['orginal_name']     = false;
        $new_file_small             = $this->crop_image($config);
        
        $has_s3     = $this->settings->setting('has_s3');
        if( $has_s3['as_superadmin_value'] && $has_s3['as_siteadmin_value'] )
        {
            $file_path          = catalog_upload_path(array('bundle_id' => $bundle_id)).$new_file;
            $file_medium_path   = catalog_upload_path(array('bundle_id' => $bundle_id)).$new_file_medium;
            $file_small_path    = catalog_upload_path(array('bundle_id' => $bundle_id)).$new_file_small;
            uploadToS3($file_path, $file_path);
            uploadToS3($file_medium_path, $file_medium_path);
            uploadToS3($file_small_path, $file_small_path);
            unlink($file_path);
            unlink($file_medium_path);
            unlink($file_small_path);
        }
        
        echo json_encode(array('bundle_image' => catalog_path(array('bundle_id' => $bundle_id)).$new_file_medium));
    }
     
    function crop_image($config)
    {
        $uploaded_data  = isset($config['uploaded_data'])?$config['uploaded_data']:array();
        $width          = isset($config['width'])?$config['width']:360;
        $height         = isset($config['height'])?$config['height']:160;
        $orginal_name   = isset($config['orginal_name'])?$config['orginal_name']:false;
        $bundle_id      = isset($config['bundle_id'])?$config['bundle_id']:0;
                
        $source_path    = $uploaded_data['full_path'];
        
        $DESIRED_IMAGE_WIDTH  = $width;
        $DESIRED_IMAGE_HEIGHT = $height;
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
        $desired_aspect_ratio = $DESIRED_IMAGE_WIDTH / $DESIRED_IMAGE_HEIGHT;

        if ($source_aspect_ratio > $desired_aspect_ratio)
        {
            /*
             * Triggered when source image is wider
             */
            $temp_height = $DESIRED_IMAGE_HEIGHT;
            $temp_width = ( int ) ($DESIRED_IMAGE_HEIGHT * $source_aspect_ratio);
        } else {
            /*
             * Triggered otherwise (i.e. source image is similar or taller)
             */
            $temp_width = $DESIRED_IMAGE_WIDTH;
            $temp_height = ( int ) ($DESIRED_IMAGE_WIDTH / $source_aspect_ratio);
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

        $x0 = ($temp_width - $DESIRED_IMAGE_WIDTH) / 2;
        $y0 = ($temp_height - $DESIRED_IMAGE_HEIGHT) / 2;
        $desired_gdim = imagecreatetruecolor($DESIRED_IMAGE_WIDTH, $DESIRED_IMAGE_HEIGHT);
        imagecopy(
            $desired_gdim,
            $temp_gdim,
            0, 0,
            $x0, $y0,
            $DESIRED_IMAGE_WIDTH, $DESIRED_IMAGE_HEIGHT
        );

        /*
         * Render the image
         * Alternatively, you can save the image in file-system or database
         */

        //header('Content-type: image/jpeg');
        $directory          = catalog_upload_path(array('bundle_id' => $bundle_id));
        $this->make_directory($directory);
        if($orginal_name)
        {
            $final_file = $uploaded_data['raw_name'].'.jpg';                    
        }
        else
        {
            $final_file = $uploaded_data['raw_name'].'_'.$width.'x'.$height.'.jpg';        
        }
        imagejpeg($desired_gdim, $directory.$final_file);

        /*
         * Add clean-up code here
         */
        //return $uploaded_data['raw_name'].'.jpg';
        return $final_file;
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

    function enroll_course($bundle_id = false) 
    {
        if(!$bundle_id) {
            redirect(admin_url('bundle'));
        }
        $bundle_param                       = array();
        $data                               = array();
        $filter_param                       = array();
        $bundle_param['bundle_id']          = $bundle_id;
        $bundle_param['limit']              = '1';
        $bundle_param['select']             = 'c_courses';
        $bundle                             = $this->Bundle_model->bundle($bundle_param);
        $enrolled_courses                   = json_decode($bundle['c_courses'],true);
        $bundle_courses_ids                 = !empty($enrolled_courses)?array_column($enrolled_courses, 'id'):array();
        $limit                              = $this->__limit;
        //$filter_param['not_deleted']        = true;
        $filter_param['order_by']           = 'cb_title';
        $filter_param['direction']          = 'ASC';
        $filter_param['count']              = true;
        $filter_param['limit']              = $limit;
        $filter_param['offset']             = 0;
        $filter_param['not_deleted']        = true;
        $filter_param['select']             = 'id,cb_title,cb_code,cb_is_free,cb_price,cb_discount,cb_validity,cb_validity_date,cb_status,cb_access_validity';
        $filter_param['course_id_exclude']  = empty($bundle_courses_ids)?false:$bundle_courses_ids;
        $data['total_users']                = $this->Course_model->courses_new($filter_param);
        unset($filter_param['count']);
        $data['users']                      = $this->Course_model->courses_new($filter_param);
        $data['course_id']                  = $bundle_id;
        $data['show_load_button']           = false;
        $data['limit']                      = $limit;

        if($data['total_users'] > $limit){
            $data['show_load_button']   = true;
        }
        $this->invalidate_bundle();
        $this->invalidate_bundle(array('bundle_id'=>$bundle_id));
        // echo "<pre>";print_r($data['users']); die();
        $this->load->view($this->config->item('admin_folder').'/bundle_enroll_course',$data);
    }
    function enroll_course_json()
    {
        $data                               = array();
        $filter_param                       = array();
        $bundle_param                       = array();
        $bundle_param['bundle_id']          = $this->input->post('course_id');//bundle_id;
        $bundle_param['limit']              = '1';
        $bundle_param['select']             = 'c_courses';
        $bundle                             = $this->Bundle_model->bundle($bundle_param);
        $enrolled_courses                   = json_decode($bundle['c_courses'],true);
        $bundle_courses_ids                 = !empty($enrolled_courses)?array_column($enrolled_courses, 'id'):array();
        
        $limit                              = $this->__limit;
        $offset                             = $this->input->post('offset');
        $page                               = $offset;
        if($page === NULL||$page <= 0)
        {
            $page                           = 1;
        }
        $page                               = ($page - 1) * $limit;
        //$filter_param['not_deleted']        = true;
        $filter_param['order_by']           = 'cb_title';
        $filter_param['direction']          = 'ASC';
        $filter_param['course_id']          = $this->input->post('bundle_id');
        $filter_param['keyword']            = $this->input->post('keyword');
        $filter_param['keyword']            = trim($filter_param['keyword']);
        $filter_param['count']              = true;
        $filter_param['not_deleted']        = true;
        $filter_param['select']             = 'id,cb_title,cb_code,cb_is_free,cb_price,cb_discount,cb_validity,cb_validity_date,cb_status,cb_access_validity';
        $filter_param['course_id_exclude']  = empty($bundle_courses_ids)?false:$bundle_courses_ids;
        $total_course                       = $this->Course_model->courses_new($filter_param);
        unset($filter_param['count']);
        $filter_param['limit']              = $this->input->post('limit');
        $filter_param['offset']             = $page;
        $data['users']                      = $this->Course_model->courses_new($filter_param);
        $data['total_users']                = $total_course;    
        $data['limit']                      = $limit;
        $data['show_load_button']           = false;
        if($total_course > ($limit * $offset))
        {
            $data['show_load_button']       = true;
        }
        echo json_encode($data);
    }
    //Function to check valid course and add to bundle
    public function check_course_valid()
    {
        $course_ids             = $this->input->post('course_ids');
        $course_ids             = empty(json_decode($course_ids))?array():json_decode($course_ids);
        $objects                = array();
        $objects['key']         = 'active_courses';
        $callback               = 'courses';
        $courses                = $this->memcache->get($objects, $callback,array());
        $course_list            = array();
        $active_list            = array();
        $response               = array();
        $response['error_msg']  = '';
        //echo '<pre>'; print_r($courses);die;
        foreach($courses as $course)
        {
            $course_id          = isset($course['id']) ? $course['id'] : '';

            $lectures_count     = $this->Course_model->get_lecture_count($course_id);
            $error_count        = 0;
            if(in_array($course_id, $course_ids))
            {
                if ($course['cb_image'] == 'default.jpg') 
                {
                    $response['error_msg'] ='course image missing and course is inactive';
                    $error_count ++;
                }
                if (strip_tags($course['cb_description']) == '') 
                {
                    $response['error_msg'] ='course description missing';
                    $error_count ++;
                }
                if ($course['cb_category'] == '')
                //if ($course['cb_category'] == '' && $course['cb_status'] == '0') 
                {
                    $response['error_msg'] ='course category missing and course is inactive';
                    $error_count ++;
                }
                if ($lectures_count < 1) 
                {
                    $response['error_msg'] ='Please add atleast one lecture to this course';
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
                    $response['console'] = 'error_count is > 0';
                    $course_list[] = $course['cb_code'].' : '.$course['cb_title'];
                }
                else
                {
                    $response['console'] = 'no errors error_count is = 0';
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
    public function save_courses() 
    {
        $course_ids                     = $this->input->post('course_ids');
        $bundle_id                      = $this->input->post('bundle_id');
        
        $course_list                    = json_decode($course_ids);
        $notification_ids               = array();
        $response                       = array();
        $course_param                   = array();
        $enrolled_courses               = array();
        $course_param['select']         = 'id,c_title,c_courses';  
        $course_param['bundle_id']      = $bundle_id;
        $course_param['limit']          = '1';
        $bundle                         = $this->Bundle_model->bundle($course_param);
        
        if(!empty($course_list))
        {
            $enrolled_courses           = empty($bundle['c_courses']) ? array() : json_decode($bundle['c_courses'],true);
            $save_userdata              = array();
            if(!empty($enrolled_courses)){
                $save_userdata          = array_column($enrolled_courses, 'id');
            }
            $param                      = array();
            $param['select']            = 'id,cb_title,cb_code,cb_status';
            $param['course_id_list']    = $course_list;
            $courses                    = $this->Course_model->course_new($param); 
            
            if(empty($enrolled_courses)){
                $enrolled_courses       = array();
            }
            if(!is_array($enrolled_courses)){
                $enrolled_courses       = array();
            }
            if(!empty($courses)){
                foreach ($courses as $course)
                {
                    if(!in_array($course['id'], $save_userdata)){
                        
                            array_push($enrolled_courses ,array(
                                'id'            => $course['id'],
                                'course_name'   => $course['cb_title'],
                                'course_code'   => $course['cb_code'],
                                'status'        => $course['cb_status']
                            ));
                        
                    }
                    
                }
            }

            $data                       = array();
            $filter                     = array();
            $filter['id']               = $bundle_id;
            $filter['update']           = true;
            $data['c_courses']          = json_encode($enrolled_courses);
            $result                     = $this->Bundle_model->save($data,$filter);   
                  
                // $user_data              = array();
                // $user_data['user_id']   = $this->__loggedInUser['id'];
                // $user_data['username']  = $this->__loggedInUser['us_name'];
                // $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
                
                // $message_template                   = array();
                // $message_template['username']       = $this->__loggedInUser['us_name'];;
                // $message_template['course_name']    = $course_name;
                // $message_template['count']          = count($course_list);
                // $triggered_activity                 = "admin_users_subscribe_course";
                // log_activity($triggered_activity, $user_data, $message_template); 
            if($result){
                $this->migrateCourseSubscription(array('bundle_id' => $bundle_id, 'course_ids' => $course_list));
                $response['success']    = true;
                $response['message']    = 'Items enrolled to Bundle';
                $this->memcache->delete('all_bundles');
                $this->memcache->delete('all_courses');
                $this->memcache->delete('sales_manager_all_sorted_courses');
                $this->memcache->delete('bundle_'.$bundle_id);
                $this->memcache->delete('bundle_mob'.$bundle_id);
                $this->memcache->delete('popular_courses');
                $this->memcache->delete('featured_courses');        
                $this->memcache->delete('bundle_notification_' . $bundle_id);
                $this->memcache->delete('all_sorted_course');
            }
        } else {
            $response['success']    = false;
            $response['messsage']   = 'Please select any item to enroll';
        }
        echo json_encode($response);
    }

    public function migrateCourseSubscription($params = array())
    {
        if(isset($params['bundle_id']) && isset($params['course_ids']))
        {
            $newCourseSubscribers       = $this->Bundle_model->migrateCourseSubscription($params);
            if(!empty($newCourseSubscribers))
            {
                foreach($newCourseSubscribers as $user_id => $courses)
                {
                    if(!empty($courses))
                    {
                        $params['course_ids']    = $courses;
                        $params['user_id']       = $user_id;
                        $this->subscribeUserByCoursesFromBundle($params);
                    }
                }
            }
        }
        else
        {
            return false;
        }
    }

    public function subscribeUserByCoursesFromBundle($params = array()) 
    {
        if(!isset($params['bundle_id']) || !isset($params['course_ids']) || !isset($params['user_id']))
        {
            return false;
        }
        
        $course_ids             = $params['course_ids'];
        $bundle_id              = $params['bundle_id'];
        $user_id             = $params['user_id'];
        $notification_ids       = array();

        
        //print_r($course);die;
        $payment_data           = array();

        $user_param             = array();
        
        $us_param               = array('user_id' => $user_id, 'course_ids' => $course_ids, 'select' => 'course_subscription.cs_user_id');
        $courses_subscribed     = $this->Course_model->subscription_details($us_param);
        if(isset($courses_subscribed[0]['cs_course_id']))
        {
            for($u = 0; $u < count($courses_subscribed); $u++)
            {
                if (($key       = array_search($courses_subscribed[$u]['cs_course_id'],$course_ids)) !== false) {
                    
                    unset($course_ids[$key]);
                }
            }
        }

        $user_param['id']       = $user_id;
        //$user_param['verified'] = true;
        $user_param['select']   = 'users.id,users.us_name,users.us_email,users.us_groups,users.us_email_verified,us_phone';
        $subscribed_user        = $this->User_model->user($user_param);
        
        $users_subscribed       = array();
        $users_groups           = array();

        $course_param           = array();
        $course_param['select'] = 'id,cb_code,cb_title,cb_is_free,cb_access_validity,cb_validity,cb_validity_date,cb_price,cb_discount,cb_tax_method';  
        $course_param['course_id_list']     = $course_ids;
        $courses                = $this->Course_model->course_new($course_param);
       //print_r($payment_data);die;
        if(!empty($courses))
        {
            $course_names = array();
            //Push notification
            $this->load->library('Notifier');
            $email_content              = array();
            $save_userdata              = array();
            $bundle_param               = array();
            $bundle_param['bundle_id']  = $bundle_id;
            $bundle_param['select']     = 'id, c_title, c_code, c_access_validity, c_validity_date, c_validity, c_slug';
            $bundle                     = $this->Bundle_model->bundle($bundle_param);
            foreach ($courses as $course)
            {
                $save                               = array();
                $course_names[]                     = $course['cb_title'];
                $save['id']                         = false;
                $save['cs_course_id']               = $course['id'];
                //CONCAT(
                $save['cs_bundle_id']               = $bundle_id;
                $save['cs_user_id']                 = $user_id;
                $save['cs_subscription_date']       = date('Y-m-d H:i:s');
                $save['cs_start_date']              = date('Y-m-d');
                $save['cs_course_validity_status']  = $bundle['c_access_validity'];
                $save['cs_user_groups']             = (isset($users_groups[$user_id]))?$users_groups[$user_id]:'';
                $notification_ids[$user_id]         = $user_id;
                if ($bundle['c_access_validity'] == 2) {
                    $course_enddate = $bundle['c_validity_date'];
                } else if ($bundle['c_access_validity'] == 0) {
                    $course_enddate = date('Y-m-d', strtotime('+3000 days'));
                } else {
                    $duration = ($bundle['c_validity']) ? $bundle['c_validity']-1 : 0;
                    $course_enddate = date('Y-m-d', strtotime('+' . $duration . ' days'));
                }
                                    
                $save['cs_end_date']            = $course_enddate;
                $save['cs_approved']            = '1';
                $save['action_by']              = $this->__loggedInUser['id'];
                $save['action_id']              = '1';  
                $save_userdata[]                = $save;     
                //Invalidation 
                $this->invalidate_subscription(array('user_id' => $save['cs_user_id'],'course_id'=>$save['cs_course_id']));
                
                $this->memcache->delete('course_'.$course['id']);
                //End invalidation
            }
                $this->memcache->delete('my_bundle_subscription_'.$bundle_id.'_'.$user_id);
                $this->memcache->delete('bundle_'.$bundle_id);
                $this->memcache->delete('bundle_enrolled_'.$user_id);
                
                $this->memcache->delete('enrolled_item_ids_'.$user_id);
                $this->memcache->delete('mobile_enrolled_'.$user_id);
                $this->memcache->delete('all_sorted_course');
                $this->memcache->delete('popular_courses');
                $this->memcache->delete('featured_courses');

            
                if($save_userdata)
                {
                    $this->User_model->subscription_save($save_userdata);
                }

                $user_name                                  = $subscribed_user['us_name'];
                $verified_email                             = $subscribed_user['us_email'];
                $privilage_user                             = $this->__loggedInUser['us_name'];
                $course_names                               = $course_names;
                $update_subscription                      = array();
                $update_subscription['cs_user_groups']    = $subscribed_user['us_groups'];
                $update_conditions                        = array();
                $update_conditions['course_id']           = $course['id'];
                $update_conditions['user_id']             = $user_id;
                $update_conditions['update']              = true;
                
                $this->User_model->save_subscription_new($update_subscription, $update_conditions);
                if (!empty($verified_email) && $course_names!='') 
                {
                    $email_param                = array();
                    // $email_param['email_code']  = 'student_to_course';
                    $email_param['email']     = $verified_email;
                    $email_param['contents']  = array(
                            'username'        => $user_name, 
                            'course_name'     => implode(', ',$course_names), 
                            'privilage_user'  => $privilage_user, 
                            'date'            => date('Y-M-d h:i:sa'), 
                            'site_url'        => site_url(), 
                            'site_name'       => config_item('site_name')
                        );
                    array_push($email_content, $email_param);
                }

                $this->notifier->push(
                    array(
                        'action_code' => 'bundle_course_subscribed',
                        'assets' => array('course_name' => implode(', ',$course_names),'student_name' => isset($subscribed_user['us_name'])?$subscribed_user['us_name']:'','course_id' => $course['id'],'bundle_url' => $bundle['c_slug']),
                        'target' => $course['id'],
                        'individual' => true,
                        'push_to' => $notification_ids
                    )
                );
                //End notification 


            
            if(!empty($email_content))
            {
                $this->process_bulk_mail($email_content,'student_to_course');
            }

            $user_data              = array();
            $user_data['user_id']   = $this->__loggedInUser['id'];
            $user_data['username']  = $this->__loggedInUser['us_name'];
            $user_data['useremail']  = $this->__loggedInUser['us_email'];
            $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
            
            $message_template                   = array();
            $message_template['username']       = $this->__loggedInUser['us_name'];;
            $message_template['course_name']    = $course_names[0];
            $message_template['count']          = 1;
            $triggered_activity                 = "admin_users_subscribe_course";
            log_activity($triggered_activity, $user_data, $message_template); 
            
            
            $this->memcache->delete('courses');
            $this->memcache->delete('all_courses');
            $this->memcache->delete('sales_manager_all_sorted_courses');
            $this->memcache->delete('top_courses');
            $this->memcache->delete('popular_courses');
            $this->memcache->delete('featured_courses');
            return true;
        } 
        else 
        {
            return false;
        }
        
    }

    /*public function subscribeUsersFromBundle($params = array()) 
    {
        if(!isset($params['bundle_id']) || !isset($params['course_id']) || !isset($params['user_ids']))
        {
            return false;
        }
        
        $course_id              = $params['course_id'];
        $bundle_id              = $params['bundle_id'];
        $users_list             = $params['user_ids'];
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
                //CONCAT(
                $save['cs_bundle_id']               = $bundle_id;
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
                $this->memcache->delete('my_bundle_subscription_'.$bundle_id.'_'.$user_id);
                $this->memcache->delete('bundle_enrolled_'.$user_id);
                //End invalidation
            }
            
            if($save_userdata)
            {
                $this->User_model->subscription_save($save_userdata);
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
            $message_template['username']       = $this->__loggedInUser['us_name'];;
            $message_template['course_name']    = $course_name;
            $message_template['count']          = count($users_list);
            $triggered_activity                 = "admin_users_subscribe_course";
            log_activity($triggered_activity, $user_data, $message_template); 
            
            $this->memcache->delete('course_'.$course_id);
            $this->memcache->delete('courses');
            $this->memcache->delete('all_courses');
            $this->memcache->delete('sales_manager_all_sorted_courses');
            $this->memcache->delete('top_courses');
            $this->memcache->delete('popular_courses');
            $this->memcache->delete('featured_courses');
            return true;
        } 
        else 
        {
            return false;
        }
        
    }*/

    public function users($id = false, $filter = false)
    {
        if(!in_array($this->__access['view'], $this->__bundle_student_enrollment_privilege))
        {
            redirect(admin_url('bundle'));
        }
        
        if (!$id) {
            $this->session->set_flashdata('message', lang('bundle_not_found'));
            redirect($this->config->item('admin_folder') . '/bundle');
        }
         
        $bundle_param               = array();
        $bundle_param['bundle_id']  = $id;
        $bundle_param['select']     = 'id, c_title, c_code';
        $bundle                     = $this->Bundle_model->bundle($bundle_param);
        
        if (!$bundle) {
            $this->session->set_flashdata('message', lang('bundle_not_found'));
            redirect($this->config->item('admin_folder') . '/bundle');
        }
        $data                       = array();
        $breadcrumb                 = array();
        $breadcrumb[]               = array('label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>');
        $breadcrumb[]               = array('label' => lang('bundle_bar'), 'link' => admin_url('bundle'), 'active' => 'active', 'icon' => '');
        $breadcrumb[]               = array('label' => $bundle['c_title'], 'link' => admin_url('bundle/basic/' . $bundle['id']), 'active' => 'active', 'icon' => '');
        $breadcrumb[]               = array('label' => 'Students', 'link' => '', 'active' => 'active', 'icon' => '');
        $data['breadcrumb']         = $breadcrumb;

        $data['title']              = $bundle['c_title'];
        $data['show_load_button']   = false;
        $data['limit']              = $this->__limit;
            
        $param                      = array();
        $param['bundle_id']         = $bundle['id'];
        $param['select']            = 'bundle_subscription.*, users.us_name, users.us_email, users.us_phone, users.us_institute_code';
        $param['filter']            = ($this->input->get('filter') != null)? $this->input->get('filter') : 'active';
        if(!isset($this->__role_query_filter['institute_id'])){
            $param['institute_id']   = ($this->input->get('institute_id') != null)? $this->input->get('institute_id'): '';
        }

        if($this->input->get('keyword') != null)
        {
            $keyword_arr            = explode('-', $this->input->get('keyword'));
            $keyword                = implode(' ',$keyword_arr);
            $param['keyword']       = trim($keyword);
        }
        $offset                     = isset($_GET['offset'])?$_GET['offset']:0;
        $page                       = $offset;
        if($page === NULL||$page <= 0)
        {
            $page                   = 1;
        }
        $page                       = ($page - 1)* $this->__limit;
        // $param['institute_id']      = $institute_id;
        // if($this->__loggedInUser['us_role_id']!='1'){

        //     $param['institute_id']   = $this->__loggedInUser['us_institute_id'];
        // }
        $param['count']             = true;
        $data['total_enrolled']     = $this->Bundle_model->enrolled($param);
        unset($param['count']);
        $param['limit']             = $this->__limit;
        $param['offset']            = $page;
        $data['enrolled_users']     = $this->Bundle_model->enrolled($param);
           
        if($data['total_enrolled'] > $this->__limit)
        {
            $data['show_load_button']   = true;            
        }
        $data['filter']                 = $filter;
        
        $data['admin_details']          = $this->__loggedInUser;
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
        $data['course']                 = $bundle;
        $data['bundle_id']              = $bundle['id'];
        $data['branches']               = $branches;
        $data['institutes']             = $institutes;
        $data['bundle']                 = array('id'=>$id);
        $data['limit']                  = $this->__limit;
        $data['title']                  = $bundle['c_title']; 
        // echo '<pre>';    print_r($data); die;
        $this->load->view($this->config->item('admin_folder').'/bundle_user',$data);
    }
    public function enrolled_json()
    {
        $data                       = array();
        $data['show_load_button']   = false;
        $data['enrolled_users']     = array();
        $course_id              = $this->input->post('course_id');
        $institute_id           = $this->input->post('institute_id');
        $branch_id              = $this->input->post('branch_id');
        $batch_id               = $this->input->post('batch_id');
        $param                  = array();
        $param['bundle_id']     = $course_id;
        $param['select']        = 'bundle_subscription.*, users.us_name, users.us_email, users.us_phone, users.us_institute_code';
        $param['filter']        = ($this->input->post('filter') != '')? $this->input->post('filter') : 'active';
        $param['keyword']       = $this->input->post('keyword');
        $param['keyword']       = trim($param['keyword']);
        $param['institute_id']  = $institute_id;
        if($this->__loggedInUser['us_role_id']!='1'){

            $param['institute_id']   = $this->__loggedInUser['us_institute_id'];
        }
        
        $param['branch_id']     = $branch_id;
        $param['batch_id']      = $batch_id;
        $param['count']         = true;
        
        $data['total_enrolled'] = $this->Bundle_model->enrolled($param);
        unset($param['count']);
        $param['limit']         = $this->input->post('limit');        
        $offset                 = $this->input->post('offset');
        $page                   = $offset;
        if($page===NULL||$page<=0)
        {
            $page               = 1;
        }
        $page                   = ($page - 1) * $param['limit'];
        $param['offset']        = $page;
        $data['enrolled_users'] = $this->Bundle_model->enrolled($param);
        $data['limit']          = $param['limit'];
        if($data['total_enrolled'] > ($this->__limit * $offset))
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
        echo json_encode($data);
    }

    public function export_subscriptions($params)
    {
        $params                                        = base64_decode($params);
        $params                                        = (array)json_decode($params);
        
        $filter                                       = $params['filter'] ? $params['filter'] : 'active';
        $course_id                                    = $params['course_id'] ? $params['course_id'] : '';
        $branch_id                                    = $params['branch_id'] ? $params['branch_id'] : '';
        $keyword                                      = $params['keyword'] ? $params['keyword'] : '';
        $institute_id                                 = $params['institute_id'] ? $params['institute_id'] : '';
        //$filter_param['order_by']                   = '';
        //$filter_param['direction']                  = $param['order_by'] ? 'ASC' :'DESC';
        
        $batch_id                                     = $params['batch_id'] ? $params['batch_id'] : '';
        
        if($keyword)
        {
            $keyword                                  = explode('-', $keyword);
            $keyword                                  = implode(' ', $keyword);
        }
        //print_r($filter_param); die;
        
        $data                                           = array();
        $data['enrolled_users']                         = array();
        
        $param                                          = array();
        $param['count']                                 = false;
        $param['bundle_id']                             = $course_id;
        $param['select']                                = 'bundle_subscription.*, users.us_name, users.us_email, users.us_phone, users.us_institute_code';
        $param['filter']                                = $filter;
        $param['keyword']                               = trim($keyword);
        $param['institute_id']                          = $institute_id;
        if($this->__loggedInUser['us_role_id']!='1')
        {
            $param['institute_id']   = $this->__loggedInUser['us_institute_id'];
        }
        
        $param['branch_id']     = $branch_id;
        $param['batch_id']      = $batch_id;
        
        $data['enrolled_users'] = $this->Bundle_model->enrolled($param);
        
        if(isset($data['enrolled_users'][0]))
            {
            $data['batches']              = array();
            if($institute_id != '')
            {
                $this->load->model('Group_model');
                $param                  = array();
                $param['institute_id']  = $institute_id;
                $param['select']        = 'id, CONCAT(gp_institute_code," - ",gp_year," - ",gp_name) as batch_name';
                $data['batches']        = $this->Group_model->groups($param);
            }

            $course_details             = json_decode($data['enrolled_users'][0]['bs_bundle_details']);
            $bundle_details             = $this->Bundle_model->bundle(array('bundle_id' => $course_id, 'select' => 'c_title'));
            $cb_title                   = $bundle_details['c_title'];
            //$items_include              = json_decode($bundle_details['c_courses']);
            //$items                      = array();
            //foreach($items_include as $item)
            //{
               //array_push($items, $item->course_name);
            //}
            //$data['items_included']     = implode(',', $items);
            //echo '<pre>';print_r($data['enrolled_users']); die;
            $data['report_title']    = $cb_title !='' ? 'Bundle subscription report of '.$cb_title : 'Bundle subscription report';
            $data['cb_title']        = $cb_title !='' ? $cb_title : '';
            
            $this->load->view($this->config->item('admin_folder').'/export_bundle_subscriptions', $data);
        }else{
            $this->load->library('user_agent');
            redirect($this->agent->referrer());
        }
      }

    public function groups($id = false)
    {
        if (!$id) {
            $this->session->set_flashdata('message', lang('bundle_not_found'));
            redirect($this->config->item('admin_folder') . '/bundle');
        }
        $bundle_param               = $this->__role_query_filter;
        $bundle_param['bundle_id']  = $id;
        $bundle_param['select']     = 'id, c_title, c_code, c_groups';
        $bundle                     = $this->Bundle_model->bundle($bundle_param);
        if (!$bundle) {
            $this->session->set_flashdata('message', lang('bundle_not_found'));
            redirect($this->config->item('admin_folder') . '/bundle');
        }
        $data                       = array();
        $data['course']             = $bundle;
        $breadcrumb                 = array();
        $breadcrumb[]               = array('label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>');
        $breadcrumb[]               = array('label' => lang('bundle_bar'), 'link' => admin_url('bundle'), 'active' => 'active', 'icon' => '');
        $breadcrumb[]               = array('label' => $bundle['c_title'], 'link' => admin_url('course/basic/' . $bundle['id']), 'active' => 'active', 'icon' => '');
        $breadcrumb[]               = array('label' => 'Batches', 'link' => '', 'active' => 'active', 'icon' => '');
        $data['breadcrumb']         = $breadcrumb;

        $data['title']              = $bundle['cb_title'];
        $data['show_load_button']   = false;
        $data['limit']              = $this->__limit;
        // $data['assigned_tutors'] = $this->Course_model->assigned_tutors(array('course_id' => $bundle['id']));
        
        // $enrolled_users = $this->Course_model->enrolled(array('course_id' => $bundle['id']));
        // $data['total_enrolled'] = sizeof($enrolled_users);
        $param                      = $this->__role_query_filter;
        $param['course_id']         = $bundle['id'];
        if(!isset($this->__role_query_filter['institute_id'])){
            $param['institute_id']  = ($this->input->get('institute_id') != null)? $this->input->get('institute_id'): '';
        }
        if($this->input->get('keyword') != null)
        {
            $keyword_arr            = explode('-', $this->input->get('keyword'));
            $keyword                = implode(' ',$keyword_arr);
            $param['keyword']       = trim($keyword);
        }
        $param['count']         = true;
        $data['total_groups']   = $this->Group_model->course_groups($param);
        unset($param['count']);
        $param['limit']         = $this->__limit;
        $param['offset']        = 0;
        $param['select']    = 'groups.id, CONCAT(gp_institute_code," - ",gp_year," - ",gp_name) as batch_name, gp_name, gp_institute_code, gp_course_code, gp_year, gp_institute_id';
        $bundle_groups      = $this->Group_model->course_groups($param);
        $data['course_groups'] = array();
        if (!empty($bundle_groups)) {
            foreach ($bundle_groups as $group) {
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
        $this->load->view($this->config->item('admin_folder').'/bundle_groups',$data);
    }
    //Function to view bundle reviews
    public function reviews($id = false)
    {
        if (!$id) 
        {
            $this->session->set_flashdata('message', lang('bundle_not_found'));
            redirect($this->config->item('admin_folder') . '/bundle');
            exit;
        }

        $bundle_param               = array();
        $bundle_param['bundle_id']  = $id;
        $bundle_param['limit']      = '1';
        $bundle                     = $this->Bundle_model->bundle($bundle_param);

        if (!$bundle) 
        {
            $this->session->set_flashdata('message', lang('bundle_not_found'));
            redirect($this->config->item('admin_folder') . '/bundle');
        }

        $data                       = array();
        $breadcrumb                 = array();
        $breadcrumb[]               = array('label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>');
        $breadcrumb[]               = array('label' => lang('bundle_bar'), 'link' => admin_url('bundle'), 'active' => 'active', 'icon' => '');
        $breadcrumb[]               = array('label' => $bundle['c_title'], 'link' => admin_url('bundle/basic/' . $bundle['id']), 'active' => 'active', 'icon' => '');
        $breadcrumb[]               = array('label' => 'Reviews', 'link' => '', 'active' => 'active', 'icon' => '');
        $data['breadcrumb']         = $breadcrumb;

        $data['title']              = $bundle['c_title'];
        $data['bundle']             = array('id' => $id);
        $data['bundleId']           = $id;
        $data['limit']              = $this->__limit;
        
        $this->load->view($this->config->item('admin_folder').'/bundle_reviews',$data);
    }
    //Function to load bundle reviews by bundle id
    public function load_reviews()
    {
        $data           = array();
        $data['limit']  = empty($this->input->post('limit')) ? $this->__limit : $this->input->post('limit');
        $bundle_id      = empty($this->input->post('bundle_id')) ? false : $this->input->post('bundle_id');
        $offset         = empty($this->input->post('offset')) ? 0 : $this->input->post('offset');
        
        $data['show_load_button']   = false;
        $data['default_user_path']  = default_user_path();
        $data['user_path']          = user_path();
        $data['title']              = 'Reviews';

        $reviews_param          = array('bundle_id' => $bundle_id, 'count' => true);
        $data['total_records']  = $this->Bundle_model->load_reviews($reviews_param);

        $reviews_param          = array('bundle_id' => $bundle_id, 'limit' => $data['limit'], 'offset' => $offset);
        $reviews                = $this->Bundle_model->load_reviews($reviews_param);
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

    /*Bundle Reviews */
    //Function to delete an bundle with review id
    public function delete_review()
    {
        if(!isset($this->review_permission) || !in_array($this->__access['delete'], $this->review_permission))
            {
                $this->session->set_flashdata('message', 'No privilege access to delete the review');
                redirect($this->config->item('admin_folder') . '/bundle');
                exit;
            }
            
        //$delete_rv                 = array();
        $delete_rt                 = array();
        $response                  = array();
        $review_id                 = $this->input->post('review_id');
        $bundle_id                 = $this->input->post('bundle_id');
        $user_id                   = $this->input->post('user_id');
        $delete_rt['table']        = 'bundle_ratings';
        $delete_rt['where']        = array('bundle_ratings.id' => $review_id);
        if(!empty($review_id))
        {
            if($this->Course_model->delete_row($delete_rt))
            {
                $response['message']        = 'Review successfully deleted';
                $response['error']          = false;
                echo json_encode($response);
                $this->memcache->delete('my_bundle_subscription_'.$bundle_id.'_'.$user_id);
                $this->invalidate_bundle(array('bundle_id' => $bundle_id));
                $this->memcache->delete('all_bundles');
                $this->memcache->delete('bundle_'.$bundle_id);
                $this->memcache->delete('bundle_enrolled_'.$user_id);
                $this->memcache->delete('bundle_mob'.$bundle_id);

                $this->memcache->delete('enrolled_item_ids_'.$user_id);
                $this->memcache->delete('mobile_enrolled_'.$user_id);
                $this->memcache->delete('all_sorted_course');
                $this->memcache->delete('popular_courses');
                $this->memcache->delete('featured_courses');
                return;
            }
        }
        else
        {
            $response['message']            = 'Failed to delete the review';
            $response['error']              = true;
            echo json_encode($response);
            return;
        }

    }

    public function export_reviews($params)
    {
        if(!isset($this->review_permission) || !in_array($this->__access['view'], $this->review_permission))
            {
                $this->session->set_flashdata('message', 'No privilege access');
                redirect($this->config->item('admin_folder') . '/bundle');
                exit;
            }

        $params                         = base64_decode($params);
        $params                         = (array)json_decode($params);
        $data                           = array();
        //print_r($params);
        $params['order_by']             = 'id';
        $params['direction']            = 'DESC';
        $params['block']                = false;
        
        $data['reviews']                = $this->Bundle_model->get_course_review($params);
        $bundle_details                 = $this->Bundle_model->bundle($params);
        //print_r($bundle_details);die;
        isset($bundle_details) ? $data['c_title'] = $bundle_details['c_title'] : '';
        $data['report_title']           = 'Review report of '.$data['c_title'];
        //echo '<pre>'; print_r($data);die;
        $this->load->view($this->config->item('admin_folder').'/export_bundle_reviews', $data);
    }

    //change review status of an bundle
    public function change_reviews_status()
    {
        if(!isset($this->review_permission) || !in_array($this->__access['edit'], $this->review_permission))
            {
                $this->session->set_flashdata('message', 'No privilege access to change status');
                redirect($this->config->item('admin_folder') . '/course');
                exit;
            }
        $review_id      = $this->input->post('review_id');
        $status         = $this->input->post('status');
        $bundle_id      = $this->input->post('bundle_id');
        $user_id        = $this->input->post('user_id');

        $status_label   = $status ? 'Publish' : 'Unpublish';
        $filter                 = array();
        $filter['update']       = true;
        $filter['review_id']    = $review_id;

        $save_param                 = array();
        $save_param['cc_status']    = $status; 
        if(!empty($review_id && $bundle_id && $user_id )) 
        {

            $result                     = $this->Bundle_model->change_review_status($save_param,$filter);
            $response                   = array();
            $response['message']        = 'failed to '.$status_label.' the review';
            $response['error']          = true;
            if($result)
            {
                $response['message']        = 'Review '.$status_label.'ed successfully';
                $response['error']          = false;
            }
            $this->memcache->delete('bundle_' . $bundle_id);
            $this->memcache->delete('bundle_mob' . $bundle_id);
            $this->memcache->delete('my_bundle_subscription_'.$bundle_id.'_'.$user_id);
            $this->memcache->delete('bundle_enrolled_'.$user_id);
            $this->memcache->delete('all_bundles');

            $this->memcache->delete('enrolled_item_ids_'.$user_id);
            $this->memcache->delete('mobile_enrolled_'.$user_id);
            $this->memcache->delete('all_sorted_course');
            $this->memcache->delete('popular_courses');
            $this->memcache->delete('featured_courses');
        }
        else
        {
            $response['message']        = 'failed to '.$status_label.' the review';
            $response['error']          = true;
        }
        echo json_encode($response);
    }
    //Function to reply for an review by admin
    public function admin_review_reply()
    {
        if(!isset($this->review_permission) || !in_array($this->__access['edit'], $this->review_permission))
            {
                $this->session->set_flashdata('message', 'No privilege access to reply');
                redirect($this->config->item('admin_folder') . '/bundle');
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
            $save['cc_bundle_id']   = $this->input->post('bundle_id');
            $save['cc_review_reply']= $admin_reply;
        }
        if(!empty($this->input->post('review_id')))
        {
            if($this->Bundle_model->save_review(array('id' => $this->input->post('review_id'), 'cc_admin_reply' => json_encode($save))))
            {
                $response['message']        = 'Reply successfully saved';
                $response['error']          = false;
                echo json_encode($response);
                $this->invalidate_course(array('bundle_id' => $this->input->post('bundle_id')));
                return;
            }
        }
        else
        {
            $response['message']            = 'Failed to save reply';
            $response['error']              = true;
            echo json_encode($response);
            return;
        }
    }

/*Bundle Reviews */

    public function invalidate_course($param = array())
    {
        //Invalidate cache
        $course_id = isset($param['course_id']) ? $param['course_id'] : false;
        if ($course_id) {
            $this->memcache->delete('course_' . $course_id);
            $this->memcache->delete('course_mob' . $course_id);
        } else {
            $this->memcache->delete('all_courses');
            $this->memcache->delete('sales_manager_all_sorted_courses');
            $this->memcache->delete('top_courses');
        }
        $this->memcache->delete('popular_courses');
        $this->memcache->delete('featured_courses');
        $this->memcache->delete('active_courses');
    }

    //Bundle advanced function
    public function seo($id = false)
    {

        $data                           = array();        
        if($id)
        {
            $bundle_param               = array();
            $bundle_param['bundle_id']  = $id;
            $bundle_param['limit']      = '1';
            $bundle_param['select']     = 'id,c_slug,c_meta,c_title,c_meta_description,c_route_id';
            $bundle_basic               = $this->Bundle_model->bundle($bundle_param);
            
            if(!$bundle_basic){
                redirect($this->config->item('admin_folder').'/bundle');
            }
            $data['bundle']             = array('id' => $id);
            $data['id']                 = $bundle_basic['id'];
            $data['c_slug']             = $bundle_basic['c_slug'];
            $data['c_meta']             = $bundle_basic['c_meta'];
            $data['c_title']            = $bundle_basic['c_title'];
            $data['c_meta_description'] = $bundle_basic['c_meta_description'];
            $data['c_route_id']         = $bundle_basic['c_route_id'];
        }else{
            redirect($this->config->item('admin_folder').'/bundle');
        }

        $breadcrumb         = array();
        $breadcrumb[]       = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]       = array( 'label' => lang('bundle_bar'), 'link' => admin_url('bundle'), 'active' => 'active', 'icon' => '' );
        $breadcrumb[]       = array( 'label' => $bundle_basic['c_title'], 'link' => admin_url('bundle/basic/' . $bundle_basic['id']), 'active' => 'active', 'icon' => '' );
        $breadcrumb[]       = array('label' => 'Advanced',  'link' => '', 'active' => 'active', 'icon' => '');
        $data['breadcrumb'] = $breadcrumb;

        $this->load->view($this->config->item('admin_folder').'/bundle_seo',$data);
    }
    public function save_seo($id = false)
    {

        if($id){
            
            $this->form_validation->set_rules('c_slug', 'Friendly URL','required');

            if ($this->form_validation->run() == FALSE)
            {
                $data['errors'] = validation_errors(); 
                $this->load->view($this->config->item('admin_folder').'/bundle_seo',$data);
            }
            else
            {
                
                $this->load->helper('text');
                $slug       = $this->input->post('c_slug');
                if(empty($slug) || $slug=='')
                {
                    $slug   = $this->input->post('c_title');
                }
                $slug	            = url_title(convert_accented_characters($slug), 'dash', TRUE);
                $route_id	        = $this->input->post('c_route_id');
                $slug	            = $this->Routes_model->validate_slug($slug, $route_id);
                $updated_date       = date('Y-m-d H:i:s');

                $save['c_slug']             = $slug;
                $save['c_meta']             = $this->input->post('c_meta');
                $save['c_title']            = $this->input->post('c_title');
                $save['c_meta_description'] = $this->input->post('c_meta_description');
                $save['c_route_id']         = $route_id;
                $save['action_by']          = $this->auth->get_current_admin('id');
                $save['updated_date']       = $updated_date;

                $filter                     = array();
                $filter['update']           = true;
                $filter['id']               = $id;
                $this->memcache->delete(base64_encode($data['c_slug'].config_item('id')), 0, true);
                $this->Bundle_model->save($save,$filter);
                
                // $user_data              = array();
                // $user_data['user_id']   = $this->__loggedInUser['id'];
                // $user_data['username']  = $this->__loggedInUser['us_name'];
                // $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
                
                // $message_template                    = array();
                // $message_template['username']        = $this->__loggedInUser['us_name'];;
                // $message_template['course_name']     =  $save['cb_title'];

                // $triggered_activity                  = 'course_advanced_updated';
                // log_activity($triggered_activity, $user_data, $message_template); 

                $route['id']	        = $route_id;
                $route['slug']	        = $slug;
                $route['route']	        = 'bundle/basic/'.$id;
                $route['r_item_type']   = 'bundle';
                $route['r_item_id']     = $id;
                $this->Routes_model->save($route);

                $this->invalidate_bundle(array('bundle_id' => $id));
                $this->invalidate_bundle();
                $this->session->set_flashdata('message', lang('bundle_seo_saved'));
                redirect($this->config->item('admin_folder').'/bundle/seo/'.$id);
            }
        }else{
            redirect($this->config->item('admin_folder').'/bundle');
        }
    }
    
    public function delete_course()
    {

        $course_id = $this->input->post('course_id');
        $bundle_id = $this->input->post('bundle_id');
        
        $bundle_param                   = array();
        $bundle_param['select']         = 'id,c_title,c_courses';  
        $bundle_param['bundle_id']      = $bundle_id;
        $bundle_param['limit']          = '1';
        $bundle                         = $this->Bundle_model->bundle($bundle_param);
        
        $enrolled_courses               = json_decode($bundle['c_courses'],true);
        $save_userdata                  = array();
        
        if(!$course_id || !$bundle_id)
        {
            $response['error']      = true;
            $save['c_status']       = '0';
            $response['message']    = 'Failed to delete course from the bundle! please try agin!';
            echo json_encode($response);
            exit();
        }

        //print_r($enrolled_courses);
        //$bundle_course_ids  = array_column($enrolled_courses, 'id');

        foreach ($enrolled_courses as $course)
        {
            if($course_id != $course['id'])
            {
                array_push($save_userdata,$course);
            }
        }

        $enrolled_courses           = count($save_userdata);
        $save                       = array();
        $filter                     = array();

        if($enrolled_courses < 1)
        {
            $save['c_status']       = '0';
        }

        $bundleSubscribers          = $this->Bundle_model->get_subscription_count($bundle_id);

        if($bundleSubscribers > 0 && $enrolled_courses < 1){
            $response['error']      = true;
            $save['c_status']       = '0';
            $response['message']    = 'You can\'t remove the last course from this Bundle, there are <b>'.$bundleSubscribers.'</b> Subscriber(s) in this Bundle!';
            echo json_encode($response);
            return false;
        }

        $save['c_courses']          = json_encode($save_userdata);
        $filter['id']               = $bundle_id;
        $filter['update']           = true;

        $result                     = $this->Bundle_model->save($save,$filter);
        
        if($result){
            $this->removeCourseSubscriptionFromBundle(array('course_id' => $course_id, 'bundle_id' => $bundle_id));
            $this->invalidate_bundle(array('bundle_id' => $bundle_id));
            $this->invalidate_bundle();
            $this->invalidate_bundle_user(array('bundle_id' => $bundle_id));
            $response['error']      = false;
            $response['message']    = 'Item deleted from Bundle';
        }else{
            $response['error']      = true;
            $response['message']    = 'Failed to delete ';
        }
        echo json_encode($response);
    }

    public function removeCourseSubscriptionFromBundle($params = array()) 
    {
        if(isset($params['bundle_id']) && isset($params['course_id']))
        {
            $params['removefrombundle'] = true;
            $params['course_ids']       = array($params['course_id']);
            $course_subscription_removed= $this->Bundle_model->migrateCourseSubscription($params);
        }
    }
    //Function to restore an deleted bundle 
    public function restore()
    {

        $response                       = array();
        $save                           = array();
        $filter_param                   = array();
        $response['bundle']             = array();
        $bundle_id                      = $this->input->post('bundle_id');
        $filter_param['id']             = $bundle_id;
        $filter_param['update']         = true;
        $save['c_deleted']              = '0';
        $save['c_status']               = '0';
        $save['updated_date']           = date('Y-m-d H:i:s');
        $result                         = $this->Bundle_model->save($save,$filter_param);

        //To delete mobile api get subscriptions memcache
        $bundle_param                   = array();
        $bundle_param['id']             = $bundle_id;
        $this->reset_mobile_subscriptions($bundle_param);

        if (!$result) {
            $response['error']          = true;
            $response['message']        = "Bundle restore failed";
        }else{
            if(!empty($bundle_id))
            {
                $course_param               = array();
                $course_param['select']     = 'id,c_title,c_code,c_is_free,c_courses,c_status,c_deleted,c_access_validity,c_validity,c_validity_date';  
                $course_param['bundle_id']  = $bundle_id;
                $course_param['limit']      = '1';
                $response['bundle']         = $this->Bundle_model->bundle($course_param);
                $response['error']          = false;
                $response['message']        = "Bundle restored successfully";
            }
            else
            {
                $response['error']          = true;
                $response['message']        = "Bundle restore failed";
            }
               
        }
                
        // $user_data                          = array();
        // $user_data['user_id']               = $this->__loggedInUser['id'];
        // $user_data['username']              = $this->__loggedInUser['us_name'];
        // $user_data['user_type']             = $this->__loggedInUser['us_role_id']; ;
        // $message_template                   = array();
        // $message_template['username']       = $this->__loggedInUser['us_name'];;
        // $message_template['course_name']    = $course['cb_title'];
        // $triggered_activity                 = 'course_restore';
        // log_activity($triggered_activity, $user_data, $message_template);                                            
       
        echo json_encode($response);
    }

    public function send_extend_validity()
    {
        
        $email_content              = array();
        $user_ids                   = json_decode($this->input->post('user_id'),true);
        $bundle_id                  = $this->input->post('bundle_id');
        $validity                   = $this->input->post('updated_validity');
        $updated_validity           = date('Y-m-d', strtotime($validity));

        $bundle_param               = array();
        $bundle_param['bundle_id']  = $bundle_id;
        $bundle_param['select']     = 'id,c_title'; 
        $bundle_param['limit']      = '1'; 
        $result                     = $this->Bundle_model->bundle($bundle_param);
        $bundle_title               = $result['c_title'];

        
        $filter_param['user_ids']   = $user_ids;
        $filter_param['bundle_id']  = $bundle_id;
        $filter_param['update']     = true;
        $save_param['bs_end_date']  = $updated_validity;
        
        // echo "<pre>";print_r($save_param);exit;
        $this->Bundle_model->save_subscription($save_param,$filter_param);

        $student_count              = count($user_ids);
        if($student_count == 1){
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
                    ,'course_title'         => $bundle_title
                    ,'validity'             => $validity
                    ,'site_url'             => site_url()
                );
            array_push($email_content,$new_email_param);
        }
        $this->process_bulk_mail($email_content,'validity_extend');
        $user_data              = array();
        $user_data['user_id']   = $this->__loggedInUser['id'];
        $user_data['username']  = $this->__loggedInUser['us_name'];
        $user_data['useremail'] = $this->__loggedInUser['us_email'];
        $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
        
        //Notification
        $this->load->library('Notifier');
        $this->notifier->push(
            array(
                'action_code' => 'course_subscription_validity_changed',
                'assets' => array('course_name' => $result['c_title'],'bundle_id' => $result['id']),
                'target' => $bundle_id,
                'individual' => true,
                'push_to' => $user_ids
            )
        );
        //End notification

        $message_template                   = array();
        $message_template['username']       = $this->__loggedInUser['us_name'];;
        $message_template['course_name']    = $result['c_title'];
        $message_template['count']          = $student_count_valid;
        $message_template['student_name']   = $student_name ;
        $triggered_activity                 = 'course_subscription_validity_changed';
        log_activity($triggered_activity, $user_data, $message_template); 
        $response           = array();
        $response['error']  = false;  
        $response['message']= 'Validity Period Changed successfully';
        echo json_encode($response); 
        
        
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
    public function set_as_complete()
    {
       
        $user_ids                               = json_decode($this->input->post('user_id'),true);
        $bundle_id                              = $this->input->post('bundle_id');
        $save_param['bs_percentage']            = '100';
        $save_param['bs_completion_registered'] = '1';
        $filter_param['bundle_id']              = $bundle_id;
        $filter_param['user_ids']               = $user_ids; 
        $filter_param['update']                 = true;
        $this->Bundle_model->save_subscription($save_param,$filter_param);
        $student_count                          = count($user_ids);
        if($student_count == 1){
            $student_name                       = $this->input->post('student_name');
            $student_count_valid                = '';
        }else{
            $student_name                       = '';
            $student_count_valid                = $student_count;
        }
        // if(sizeof($user_ids) > 0 )
        // {
        //     foreach($user_ids as $user_id)
        //     {
        //         $this->invalidate_subscription(array('course_id' => $course_id, 'user_id' => $user_id));   
        //     }
        // }
        $user_data                          = array();
        $user_data['user_id']               = $this->__loggedInUser['id'];
        $user_data['username']              = $this->__loggedInUser['us_name'];
        $user_data['useremail']             = $this->__loggedInUser['us_email'];
        $user_data['user_type']             = $this->__loggedInUser['us_role_id']; ;
        $message_template                   = array();
        $message_template['username']       = $this->__loggedInUser['us_name'];;
        $message_template['course_name']    = $this->input->post('bundle_title');
        $message_template['count']          = $student_count_valid;
        $message_template['student_name']   = $student_name;
        $triggered_activity                 = 'course_set_as_complete';
        log_activity($triggered_activity, $user_data, $message_template); 

        $response               = array();
        $response['error']      = false;
        $response['message']    = 'The course progress has been set a completed';
        echo json_encode($response);
    }

    public function reset_result()
    {
        $user_ids    = json_decode($this->input->post('user_id'),true);
        $bundle_id  = $this->input->post('bundle_id');
        
        $filter_param                              = array();
        $filter_param['user_ids']                  = $user_ids;
        $filter_param['bundle_id']                 = $bundle_id;
        $filter_param['update']                    = true;
        $save_param['bs_percentage']               = '0';
        $save_param['bs_completion_registered']    = '0';
        $this->Bundle_model->save_subscription($save_param,$filter_param);
        // $result = $this->remove_subscription(array('user_ids'=>$user_ids,'course_id'=>$course_id,'action_code'=>'course_subscription_result_reset'));
        
        // foreach($user_ids as $user_id){

        //     $this->invalidate_subscription(array('course_id' => $course_id, 'user_id' => $user_id));   
        // }
        $student_count              = count($user_ids);
        if($student_count == 1){
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
        $message_template['username']       = $this->__loggedInUser['us_name'];;
        $message_template['course_name']    = $this->input->post('bundle_title');
        $message_template['count']          = $student_count_valid;
        $message_template['student_name']   = $student_name;
        $triggered_activity                 = 'course_result_reset';
        log_activity($triggered_activity, $user_data, $message_template); 
        $response               = array();
        $response['error']      = false;
        $response['message']    = 'The subscription has been reset';
        echo json_encode($response);
    }

    public function change_subscription_status()
    {
       
        $user_id        = $this->input->post('user_id');
        $bundle_id      = $this->input->post('bundle_id');
        $status         = $this->input->post('status');
        if($status == '0'){
            $mstatus    = 'course_subscription_suspended';
        }else{
            $mstatus    = 'course_subscription_approved';
        }
        $filter_param['user_id']    = $user_id;
        $filter_param['bundle_id']  = $bundle_id;
        $filter_param['update']     = true;
        $save_param['bs_approved']  = $status;
        $result = $this->Bundle_model->save_subscription($save_param,$filter_param);

        $user_data              = array();
        $user_data['user_id']   = $this->__loggedInUser['id'];
        $user_data['username']  = $this->__loggedInUser['us_name'];
        $user_data['useremail']  = $this->__loggedInUser['us_email'];
        $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
        
        $message_template                   = array();
        $message_template['username']       = $this->__loggedInUser['us_name'];;
        $message_template['course_name']    = $this->input->post('bundle_title');
        $message_template['student']        = $this->input->post('student');
        
        $triggered_activity                 = $mstatus;
        log_activity($triggered_activity, $user_data, $message_template); 
        // $this->invalidate_subscription(array('user_id' => $user_id,'course_id'=>$course_id));
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
        
        //Notification
        $this->load->library('Notifier');
        $this->notifier->push(
            array(
                'action_code' => $notification_action,
                'assets' => array('course_name' => $this->input->post('bundle_title'),'course_id' => $bundle_id),
                'target' => $bundle_id,
                'individual' => true,
                'push_to' => array($user_id)
            )
        );
        //End notification
    }


    //delete users subscription form the courses present in specified bundle
    public function delete_subscription()
    {
        $this->load->library(array('archive'));
        $user_ids                   = json_decode($this->input->post('user_id'),true);
        $bundle_id                  = $this->input->post('bundle_id');
        $bundle_name                = $this->input->post('bundle_title');
        $notify_to                  = array();

        //print_r($user_ids);die;
        // remove courses
        $bundle_param               = array();
        $bundle_param['bundle_id']  = $bundle_id;
        $bundle_param['limit']      = '1';
        $bundle_param['select']     = 'c_courses';
        if(!empty($bundle_id))
        {
            $bundle_courses_ids     = $this->Bundle_model->bundle($bundle_param);
        }
        else
        {
            $bundle_courses_ids     = "";
        }
    
        if( !empty($bundle_courses_ids) )
        {
            $bundle_courses_ids = json_decode($bundle_courses_ids['c_courses'],true);
            $course_ids         = array();
            /*foreach($bundle_courses_ids as $course)
            {
                $course_id          = $course['id'];
                $course_title       = $course['course_name'];
                array_push($course_ids,$course['id']);
            }*/
            $course_ids         = !empty($bundle_courses_ids)?array_column($bundle_courses_ids, 'id'):array();

            if(!empty($user_ids) && !empty($course_ids) && !empty($bundle_id))
            {
                $this->memcache->delete('bundle_'.$bundle_id);
                $course_subscription_removed = $this->Bundle_model->migrateCourseSubscription(array('user_ids' => $user_ids,'course_ids' => $course_ids, 'bundle_id' => $bundle_id, 'removefrombundle' => true));
                if(!empty($course_subscription_removed))
                {
                    $this->remove_subscription(array('course_title' => $bundle_name,'user_ids' => $user_ids,'course_ids' => $course_ids, 'removefrombundle' => true));
                    foreach ($bundle_courses_ids as $course) 
                    {
                        $archive_param                  = array();
                        $archive_param['course_id']     = $course['id'];
                        $archive_param['user_ids']      = $course_subscription_removed;
                        $this->archive->subscription_archive_process($archive_param);
                        $this->invalidate_subscription(array('course_id' => $course['id'], 'user_ids' => $course_subscription_removed));
                    }
                }
            }
        }
        else
        {
            $response               = array();
            $response['error']      = true;
            $response['message']    = 'Failed to unsubscribe the user, The bundle is does not exists!';
            echo json_encode($response);exit;
        }
     
        if(!empty($user_ids) && $bundle_id>0)
        {
            $bundle_param               = array();
            $bundle_param['user_ids']   = $user_ids;
            $bundle_param['bundle_id']  = $bundle_id;
            $result                     = $this->Bundle_model->unsubscribe_user($bundle_param);
    
        }
        else
        {
            $response               = array();
            $response['error']      = true;
            $response['message']    = 'Failed';
            echo json_encode($response);exit;
        }

       

      
        $student_count              = count($user_ids);
        if($student_count == 1){
            $student_name           = $this->input->post('student_name');
            $student_count_valid    = '';
        }else{
            $student_name           = '';
            $student_count_valid    = $student_count;
        }
        $user_data                          = array();
        $user_data['user_id']               = $this->__loggedInUser['id'];
        $user_data['username']              = $this->__loggedInUser['us_name'];
        $user_data['user_type']             = $this->__loggedInUser['us_role_id'];
        $user_data['useremail']             = $this->__loggedInUser['us_email'];
        
        $message_template                   = array();
        $message_template['username']       = $this->__loggedInUser['us_name'];;
        $message_template['course_name']    = $bundle_name;
        $message_template['count']          = $student_count_valid;
        $message_template['student_name']   = $student_name;
        $triggered_activity                 = 'course_subscription_removed';
        log_activity($triggered_activity, $user_data, $message_template); 

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
        return true;
        //$this->Course_model->unsubscribe_user(array('user_ids' => $user_ids,'course_id' => $course_id));
        //$result = $this->remove_subscription(array('course_title' => $course_title,'user_ids' => $user_ids,'course_id'=>$course_id));
    }

    //remove user subscription forom each courses
    private function remove_subscription($param = array())
    {  
        $user_param               = array();
        $course_id                = isset($param['course_id'])?$param['course_id']:'';
        $user_param['user_id']    = isset($param['user_id'])?$param['user_id']:'';
        $user_param['user_ids']   = isset($param['user_ids'])?$param['user_ids']:'';
        $user_param['course_id']  = $course_id;
        $user_param['course_ids'] = isset($param['course_ids'])?$param['course_ids']:'';
        $user_param['lecture_id'] = isset($param['lecture_id'])?$param['lecture_id']:'';
        $course_title             = isset($param['course_title'])?$param['course_title']:'';
       
        $result[] = $this->Course_model->remove_logs($user_param);
        $result[] = $this->Course_model->remove_asessment_attempts($user_param);
        $result[] = $this->Course_model->remove_asessment_report($user_param);
        
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
        $this->invalidate_subscription(array('course_id' => $course_id, 'user_id' => $user_ids));
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
    public function invalidate_subscription($param = array())
    {
        //Invalidate cache
        $user_id = isset($param['user_id']) ? $param['user_id'] : false;
        $course_id = isset($param['course_id']) ? $param['course_id'] : false;
        $user_ids  = (is_array($user_id))?$user_id:array($user_id);
        foreach($user_ids as $user_id)
        {
            if ($user_id && $course_id) {
                $this->memcache->delete('enrolled_' . $user_id);
                $this->memcache->delete('bundle_enrolled_' . $user_id);
                
                $this->memcache->delete('subscription_' . $course_id . '_' . $user_id);
            }
            if ($user_id) {
                $this->memcache->delete('enrolled_' . $user_id);
                $this->memcache->delete('mobile_enrolled_'.$user_id);
                $this->memcache->delete('bundle_enrolled_' . $user_id);
                $this->memcache->delete('enrolled_item_ids_' . $user_id);
            }

        }
    }
    public function change_subscription_status_bulk()
    {
        $status             = $this->input->post('status');
        $bundle_id          = $this->input->post('bundle_id');
        $user_ids           = json_decode($this->input->post('users'),true);
        $user_status_data   = array();
        
        $student_count      = count($user_ids);
        if($status == '0'){
            $action = 'suspended';
            
        }else{
            $action = 'approved';
        }
        if (!empty($user_ids)) 
        {
            $subscriptions              = array();
            $subscriptions['user_ids']  = $user_ids;
            $subscriptions['bundle_id'] = $bundle_id;
            $subscriptions['update']    = true;

            $save_param                 = array();
            $save_param['bs_approved']  = $status;
            $save_param['updated_date'] = date('Y-m-d H:i:s');
            $this->Bundle_model->save_subscription($save_param, $subscriptions);

            // foreach ($user_ids as $user_id) 
            // {
            //     $this->invalidate_subscription(array('course_id' => $course_id, 'user_id' => $user_id)); 
            // }
        }

        $notification_action = 'course_subscription_suspended';

        if($status == 1)
        {
            $notification_action = 'course_subscription_approved';
        }
        
        //Notification
        $this->load->library('Notifier');
        $this->notifier->push(
            array(
                'action_code' => $notification_action,
                'assets' => array('course_name' => $this->input->post('bundle_title'),'course_id' => $bundle_id),
                'target' => $bundle_id,
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
        $message_template['username']       = $this->__loggedInUser['us_name'];;
        $message_template['course_name']    = $this->input->post('bundle_title');
        $message_template['count']          = $student_count;
        $message_template['action']         = $action;
        $triggered_activity     = 'course_subscription_status_bulk';
        log_activity($triggered_activity, $user_data, $message_template); 

        $data               = array();
        $data['error']      = false;
        $data['message']    = lang('subscription_saved');
        echo json_encode($data);
    }

    function enroll_users($bundle_id = false) 
    {
        if(!$bundle_id) {
            redirect(admin_url('bundle'));
        }
        $limit                      =  $this->__limit;
        $data                       = array();
        $param                      = $this->__role_query_filter;
        $bundle_param               = array();
        $bundle_param['bundle_id']  = $bundle_id;
        $bundle_param['select']     = 'c_courses';
        $bundle_param['limit']      = '1';
        $bundle                     = $this->Bundle_model->bundle($bundle_param);
        
        $param['bundle_id']         = $bundle_id;
        $param['role_id']           = '2';
        $param['not_subscribed']    = true;
        $param['not_deleted']       = true;
        $param['order_by']          = 'users.us_name';
        $param['direction']         = 'ASC';
        $param['count']             = true;
        $data['total_users']        = $this->User_model->users($param);
        // echo "<pre>";print_r($course_ids);exit;
        unset($param['count']);
        $param['limit']             = $limit;
        $param['offset']            = 0;
        $param['select']            = 'users.id user_id, users.us_name, us_phone, us_email, us_institute_id, us_branch, us_branch_code, us_institute_code, us_status';
        $data['users']              = $this->User_model->users($param);
        $data['limit']              = $limit;
        //Read institutes form memcached.
        $objects                    = array();
        $objects['key']             = 'institutes';
        $callback                   = 'institutes';
        $institutes                 = $this->memcache->get($objects, $callback, array()); 
        //Read branches form memcached.
        $objects                    = array();
        $objects['key']             = 'branches';
        $callback                   = 'branches';
        $branches                   = $this->memcache->get($objects, $callback, array()); 
        $data['branches']           = $branches;
        $data['bundle_id']          = $bundle_id;
        $data['show_load_button']   = false;

        if(!isset($this->__role_query_filter['institute_id'])){
            $data['institutes']     = $institutes;
        }       
        if($data['total_users'] > $limit){
            $data['show_load_button']   = true;
        }
        
        // echo "<pre>";
        // print_r($data); die();
        $this->load->view($this->config->item('admin_folder').'/bundle_enroll_student',$data);
    }

    function enroll_users_json()
    {
        $limit                          = $this->__limit;
        $data                           = array();
        $data['show_load_button']       = false;
        $user_param                     = $this->__role_query_filter;
        
        $limit                          = $limit;
        $data['limit']                  = $limit;
        $offset                         = $this->input->post('offset');
        $page                           = $offset;
        if($page === NULL||$page <= 0)
        {
            $page                       = 1;
        }
        $page                           = ($page - 1) * $limit;

        $user_param['order_by']         = 'us_name';
        $user_param['direction']        = 'ASC';
        $user_param['bundle_id']        = $this->input->post('bundle_id');
        if(!isset($this->__role_query_filter['institute_id'])){
            $user_param['institute_id'] = $this->input->post('institute_id');
        } else {
            $user_param['institute_id'] = $this->__role_query_filter['institute_id'];
        }
        $user_param['branch']           = $this->input->post('branch_id');
        $user_param['keyword']          = $this->input->post('keyword');
        $user_param['keyword']          = trim($user_param['keyword']);
        $user_param['not_deleted']      = true;
        $user_param['not_subscribed']   = true;
        $user_param['role_id']          = '2';
        $user_param['count']            = true;
        
        $total_users                    = $this->User_model->users($user_param);
        $data['total_users']            = $total_users;       
        unset($user_param['count']);
        $user_param['limit']            = $this->input->post('limit');
        $user_param['offset']           = $page;
        $user_param['select']           = 'users.id user_id, users.us_name, us_phone, us_email, us_institute_id, us_branch, us_branch_code, us_institute_code, us_status';
        if($total_users > ($limit * $offset))
        {
            $data['show_load_button']   = true;
        }
        $data['users']                  = $this->User_model->users($user_param);
        echo json_encode($data);
    }
    //Function to enroll students to the bundle
    public function enroll_students() 
    {
        $user_ids                   = $this->input->post('user_ids');
        $bundle_id                  = $this->input->post('bundle_id');
        $enrolled_courses           = array();
        $users_subscribed           = array();
        $users_groups               = array();
        $bs_payment_details         = array();
        $bd_update                  = array();
        $users_list                 = json_decode($user_ids);
        //print_r($users_list); die; 
        $notification_ids           = array();

        $bundle_param               = array();
        $bundle_param['select']     = 'id,c_code,c_title,c_code,c_is_free,c_courses,c_access_validity,c_validity,c_validity_date,c_price,c_discount,c_tax_method';  
        $bundle_param['bundle_id']  = $bundle_id;
        $bundle                     = $this->Bundle_model->bundle($bundle_param);

        if(!empty($bundle))
        {
            if ($bundle['c_access_validity'] == 2) {
                $course_enddate                         = $bundle['c_validity_date'];
            } else if ($bundle['c_access_validity'] == 0) {
                $course_enddate                         = date('Y-m-d', strtotime('+3000 days'));
            } else {
                $duration                               = ($bundle['c_validity']) ? $bundle['c_validity']-1 : 0;
                $course_enddate                         = date('Y-m-d', strtotime('+' . $duration . ' days'));
            } 

            if($course_enddate <= date('Y-m-d'))
            {
                $response['success']                = false;
                $response['error']                  = true;
                $response['message']                = 'The Bundle Access Validity Should be greater than today';
                echo json_encode($response);
                exit();
            }
        }
        else
        {
            $response['success']                = false;
            $response['error']                  = true;
            $response['message']                = 'Coulnot fetch Bundle details';
            echo json_encode($response);
            exit();
        }
        $enrolled_courses           = json_decode($bundle['c_courses'],true);
        $bundle_courses_ids         = !empty($enrolled_courses)?array_column($enrolled_courses, 'id'):array();
        //echo "<pre>";print_r($bundle);die;
        $payment_data               = array();
        /*$user_param                 = array();
        $user_param['user_ids']     = $users_list;
        //$user_param['verified']     = true;
        $user_param['select']       = 'users.id';
        $user_param['bundle_id']    = $bundle_id;
        $already_subscribed_users   = $this->User_model->users($user_param);
        //echo "<pre>";print_r($subscribed_users);
        $course_subscibed_users     = !empty($already_subscribed_users)?array_column($already_subscribed_users, 'id'):array();*/

        if(!empty($bundle_id) && !empty($bundle_courses_ids) && !empty($users_list))
        { 
            foreach ($users_list as $user_id)
            {
                //bundle subscription
                $save                               = array();
                $save['id']                         = false;
                $save['bs_bundle_id']               = $bundle_id;
                $save['bs_user_id']                 = $user_id;
                $save['bs_subscription_date']       = date('Y-m-d H:i:s');
                $save['bs_start_date']              = date('Y-m-d');
                $save['bs_course_validity_status']  = $bundle['c_access_validity'];
                
                $notification_ids[]                 = $user_id;
                if ($bundle['c_access_validity'] == 2)
                {
                    $bundle_enddate                 = $bundle['c_validity_date'];
                } 
                else if ($bundle['c_access_validity'] == 0)
                {
                    $bundle_enddate                 = date('Y-m-d', strtotime('+3000 days'));
                }
                else
                {
                    $duration                       = ($bundle['c_validity']) ? $bundle['c_validity']-1 : 0;
                    $bundle_enddate                 = date('Y-m-d', strtotime('+' . $duration . ' days'));
                }

                $save['bs_end_date']                = $bundle_enddate;
                $save['bs_approved']                = '1';
                $save['action_by']                  = $this->__loggedInUser['id'];
                $save['bs_bundle_details']          = json_encode(array(
                                                        'id'                => $bundle['id'],
                                                        'c_title'           => $bundle['c_title'],
                                                        'c_code'            => $bundle['c_code'],
                                                        'c_courses'         => json_decode($bundle['c_courses']),
                                                        'c_access_validity' => $bundle['c_access_validity'],
                                                        'c_validity'        => $bundle['c_validity'],
                                                        'c_validity_date'   => $bundle['c_validity_date'],
                                                        'c_price'           => $bundle['c_price'],
                                                        'c_discount'        => $bundle['c_discount'],
                                                        'c_tax_method'      => $bundle['c_tax_method']
                                                    ));
                $save_userdata[]                    = $save;  
                
                $this->invalidate_bundle(array('key' => 'bundle_enrolled_'.$user_id));
                //End invalidation


                //payment starts

                    $user_param                 = array();
                    $user_param['id']           = $user_id;
                    $user_param['select']       = 'users.id, users.us_name, users.us_email, users.us_phone, users.us_email_verified, users.us_groups';
                    $subscribed_user            = $this->User_model->user($user_param);
                    
                        if($subscribed_user['us_email_verified']=='1'){
                            $users_subscribed[] = $subscribed_user;
                        }

                        $users_groups[$subscribed_user['id']]       = $subscribed_user['us_groups'];
                        
                        /* Payment Details */
                        $user_details                               = array();
                        $user_details['name']                       = $subscribed_user['us_name'];
                        $user_details['email']                      = $subscribed_user['us_email'];
                        $user_details['phone']                      = $subscribed_user['us_phone'];
                        $user_details['enrolled_by']                = array('id' => $this->__loggedInUser['id'], 'us_name' => $this->__loggedInUser['us_name']);

                        $payment_param                              = array();
                        $payment_param['ph_user_id']                = $subscribed_user['id'];
                        $payment_param['ph_user_details']           = json_encode($user_details);
                        $payment_param['ph_item_id']                = $bundle_id;
                        $payment_param['ph_item_type']              = '2';
                        $payment_param['ph_item_code']              = $bundle['c_code'];
                        $payment_param['ph_item_name']              = $bundle['c_title'];
                        $payment_param['ph_item_base_price']        = $bundle['c_price'];
                        $payment_param['ph_item_discount_price']    = $bundle['c_discount'];
                        $payment_param['ph_tax_type']               = $bundle['c_tax_method'];
                        $bundle_price                               = ($bundle['c_discount']!=0)?$bundle['c_discount']:$bundle['c_price'];

                        if($bundle['c_is_free'] == '1')
                        {
                            $payment_param['ph_item_base_price']        = 0;
                            $payment_param['ph_item_discount_price']    = 0;
                            $bundle_price = 0;
                        }

                        $gst_setting                                = $this->settings->setting('has_tax');
                        $cgst                                       = ($gst_setting['as_setting_value']['setting_value']->cgst != '')?$gst_setting['as_setting_value']['setting_value']->cgst:'0';
                        $sgst                                       = ($gst_setting['as_setting_value']['setting_value']->sgst != '')?$gst_setting['as_setting_value']['setting_value']->sgst:'0';
                        //$sgst_price                                 = ($sgst / 100) * $course_price;
                        //$cgst_price                                 = ($cgst / 100) * $course_price;
                        //c_tax_method = 1 is exclusive
                        //250/100 + 12*12 inclusive tax amount
                        //250/100 + 12*100 inclusive taxable amount
                        
                        if($bundle['c_tax_method'] == '1')
                        { //exclusive
                            $sgst_price                                 = ($sgst / 100) * $bundle_price;
                            $cgst_price                                 = ($cgst / 100) * $bundle_price;
                            $total_course_price                         = $bundle_price + ($sgst_price + $cgst_price); 
                        }
                        else
                        {
                            $sgst_price                                 = $bundle_price / (100 + $sgst) * $sgst;//($sgst / 100) * $bundle_price;
                            $cgst_price                                 = $bundle_price / (100 + $cgst) * $cgst;
                            $totalTaxPercentage                         = $cgst + $sgst;
                            $total_course_price                         = $bundle_price;
                        
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
                        $payment_param['ph_account_id']             = '1';
                        $payment_param['ph_payment_gateway_used']   = 'Offline';
                        $payment_param['ph_status']                 = '1';
                        $payment_param['ph_payment_date']           = date('Y-m-d H:i:s');
                        $payment_data[$subscribed_user['id']]       = $payment_param;

                        $bd_update['bs_user_groups']                = (isset($users_groups[$subscribed_user['id']])) ? $users_groups[$subscribed_user['id']]:'';
                        $bd_update['bs_payment_details']            = isset($payment_data[$subscribed_user['id']]) ? json_encode($payment_data[$subscribed_user['id']]) : '';
                        
                        $this->memcache->delete('enrolled_' .$subscribed_user['id']);
                        $this->memcache->delete('mobile_enrolled_'.$subscribed_user['id']);
                        $objects_key                                = 'enrolled_item_ids_' .$subscribed_user['id'];
                        $this->memcache->delete($objects_key);
                //paymet ends
            }

                $this->memcache->delete('bundle_'.$bundle_id);
                $this->memcache->delete('all_sorted_course');
                $this->memcache->delete('popular_courses');
                $this->memcache->delete('featured_courses');
            
            if($save_userdata)
            {
                $this->Bundle_model->subscription_save_batch($save_userdata);
            }

            if($payment_data)
            {
                $this->load->model('Payment_model');
                $order_ids = $this->Payment_model->save_history_bulk($payment_data);
                if(!empty($order_ids))
                {/**/
                    $order_data              = array();
                    foreach($order_ids as $order_id)
                    {   $c_courses = json_decode($bundle['c_courses']);
                        $order_param                 = array();
                        $order_param['id']           = $order_id;
                        $order_param['ph_order_id']  = date('Y').date('m').date('d').$order_id;
                        $order_param['ph_item_other_details'] = json_encode(array('id'                  => $bundle['id'],
                                                                                    'c_title'           => $bundle['c_title'],
                                                                                    'c_code'            => $bundle['c_code'],
                                                                                    'c_courses'         => $c_courses,
                                                                                    'c_access_validity' => $bundle['c_access_validity'],
                                                                                    'c_validity'        => $bundle['c_validity'],
                                                                                    'c_validity_date'   => $bundle['c_validity_date'],
                                                                                    'c_price'           => $bundle['c_price'],
                                                                                    'c_discount'        => $bundle['c_discount'],
                                                                                    'c_tax_method'      => $bundle['c_tax_method']
                                                                                ));
                        $order_data[]                = $order_param;
                    }
                    $this->Payment_model->update_history_bulk($order_data);

                }
            }
            
            $users_list                             = $this->Bundle_model->migrateCourseSubscription(array('bundle_id' => $bundle_id, 'course_ids' => $bundle_courses_ids, 'user_ids' => $users_list));
            if(empty($users_list))
            {
                $response['success']                = true;
                $response['message']                = 'Students successfully enrolled to the bundle';
                echo json_encode($response);
                exit();
            }
        }
        else
        {
            $response['success']                    = false;
            $response['error']                      = true;
            $message                                = '';
            if(empty($bundle_id))
            {
                $message                            .= 'The bundle id is Missing!<br/>';
            }

            if(empty($bundle_courses_ids))
            {
                $message                            .= 'This Bundle has no courses, atleaset one course is needed!<br/>';
            }

            if(empty($users_list))
            {
                $message                            .= 'You are not selected any students!';
            }

            $response['message']                    = $message;
            echo json_encode($response);
            exit();
        }

        //student course enrollment starts
        if(!empty($users_list))
        {
            $email_content                                      = array();
            $save_userdata                                      = array();
            $save_subscribe                                     = array();
            $bundle_name                                        = $bundle['c_title'];

            $converted_combinations['data']                     = array();
            $converted_combinations['filter']                   = array();

            //foreach($bundle_courses_ids as $bundle_course){

                
                foreach($users_list as $user_id => $courses)
                {
                    foreach($courses as $course)
                    {
                        $objects                                        = array();
                        $objects['key']                                 = 'course_'.$course;
                        $callback                                       = 'course_details';
                        $params                                         = array('id' => $course);
                        $course_details                                 = $this->memcache->get($objects, $callback, $params);

                        $key_filter                                 = $course.'_'.$user_id;
                        $converted_combinations['filter'][]         = $key_filter;
                        $save_param                                 = array();
                        $save_param['cs_course_id']                 = $course;
                        $save_param['cs_user_id']                   = $user_id;
                        $save_param['cs_bundle_id']                 = $bundle_id;
                        $save_param['cs_subscription_date']         = date('Y-m-d H:i:s');
                        $save_param['cs_start_date']                = date('Y-m-d');
                        $save_param['cs_course_validity_status']    = $bundle['c_access_validity'];
                        $save_param['cs_user_groups']               = (isset($users_groups[$user_id]))?$users_groups[$user_id]:'';
                    
                        if ($bundle['c_access_validity'] == 2) {
                            $course_enddate                         = $bundle['c_validity_date'];
                        } else if ($bundle['c_access_validity'] == 0) {
                            $course_enddate                         = date('Y-m-d', strtotime('+3000 days'));
                        } else {
                            $duration                               = ($bundle['c_validity']) ? $bundle['c_validity']-1 : 0;
                            $course_enddate                         = date('Y-m-d', strtotime('+' . $duration . ' days'));
                        }                    
                        $save_param['cs_end_date']                  = $course_enddate;
                        $save_param['cs_approved']                  = '1';
                        $save_param['action_by']                    = $this->__loggedInUser['id'];
                        $converted_combinations['data'][$key_filter]= $save_param;
                    }
                    
                    $this->memcache->delete('my_bundle_subscription_'.$bundle_id.'_'.$user_id);
                    $this->memcache->delete('bundle_enrolled_'.$user_id);
                    
                }
            //} 

            $fetch_param                                        = array();
            $fetch_param['combinations']                        = $converted_combinations['filter'];
            $result                                             = $this->Bundle_model->fetch_subscribed_user($fetch_param);
            $existing_user                                      = array();
            if($result){
                $existing_user                                  = array_column($result,'sample');
            }

            $save_data                                          = array();
            $update_data                                        = array();
            $course_ids                                         = array();
            $user_ids                                           = array();
            if($converted_combinations['data']){
                
                foreach($converted_combinations['data'] as $user_key => $user_value)
                {
                    if(!in_array($user_key,$existing_user)){
                        
                        array_push($save_data,$user_value);
                    }
                    else
                    {
                        $filter_param               = array();
                        $filter_param['course_id']  = $user_value['cs_course_id'];
                        $filter_param['user_id']    = $user_value['cs_user_id'];
                        $filter_param['update']     = true;
                        $this->User_model->save_subscription_new($user_value,$filter_param);
                    }
                    
                }
            }
            
            if($save_data)
            {
                $this->User_model->subscription_save($save_data);
            }
            
            if(!empty($users_subscribed)){
                
                foreach($users_subscribed as $user_subscribed){
                    // echo "<pre>";print_r($user_subscribed);exit;
                    $user_name                              = $user_subscribed['us_name'];
                    $verified_email                         = $user_subscribed['us_email'];
                    $privilage_user                         = $this->__loggedInUser['us_name'];
                    $bundle_names                           = $bundle_name;
                    $update_subscription                    = array();
                    $update_subscription['bs_user_groups']  = $user_subscribed['us_groups'];
                    $update_conditions                      = array();
                    $update_conditions['bundle_id']         = $bundle_id;
                    $update_conditions['user_id']           = $user_id;
                    $update_conditions['update']            = true;
                    
                    $this->Bundle_model->save_subscription($update_subscription,$update_conditions);
                    if (!empty($verified_email) && $bundle_names!='') 
                    {
                        $email_param                        = array();
                        // $email_param['email_code']  = 'student_to_course';
                        $email_param['email']               = $verified_email;
                        $email_param['contents']            = array(
                              'username'        => $user_name
                            , 'bundle_name'     => $bundle_names
                            , 'privilage_user'  => $privilage_user
                            , 'date'            => date('Y-M-d h:i:sa')
                            , 'site_url'        => site_url()
                            , 'site_name'       => config_item('site_name'),
                        );
                        array_push($email_content, $email_param);
                    }
                }
            }
            // echo "<pre>";print_r($email_content);exit;
            if(!empty($email_content)){
                $this->process_bulk_mail($email_content,'bundle_admin_enrollment');
            }
             
            //Push notification
            $this->load->library('Notifier');
            $this->notifier->push(
                array(
                    'action_code'   => 'bundle_subscribed',
                    'assets'        => array('bundle_name' => $bundle_name,'student_name' => isset($users_subscribed[0]['us_name'])?$users_subscribed[0]['us_name']:'','bundle_id' => $bundle_id),
                    'target'        => $bundle_id,
                    'individual'    => true,
                    'push_to'       => $notification_ids
                )
            );
            //End notification 
              
            $user_data                          = array();
            $user_data['user_id']               = $this->__loggedInUser['id'];
            $user_data['username']              = $this->__loggedInUser['us_name'];
            $user_data['useremail']             = $this->__loggedInUser['us_email'];
            $user_data['user_type']             = $this->__loggedInUser['us_role_id']; ;
            $message_template                   = array();
            $message_template['username']       = $this->__loggedInUser['us_name'];;
            $message_template['bundle_name']    = $bundle_name;
            $message_template['count']          = count($users_list);
            $triggered_activity                 = "admin_users_subscribe_bundle";
            log_activity($triggered_activity, $user_data, $message_template); 
            
            $response['success']                = true;
            $response['message']                = 'Students enrolled to bundle';
        } else {
            $response['success']                = false;
            $response['messsage']               = 'Please select any student to enroll';
        }
        echo json_encode($response);
    }
//Function to view bundle overview
    public function overview($id =false){

        if(!$id) {
            $this->session->set_flashdata('message', lang('bundle_not_found'));
            redirect($this->config->item('admin_folder') . '/bundle');
        }
      
        /*============= Redirect if Not Valid Entry =============== */
        $bundle_param               = array();
        $bundle_param['bundle_id']  = $id;
        $bundle_param['limit']      = '1';
        $bundle_param['select']     = 'id,c_title,c_description,c_status,c_courses,c_image,c_category,c_is_free,c_price,c_discount,c_tax_method,c_access_validity,c_validity,c_validity_date';
        $bundle                     = $this->Bundle_model->bundle($bundle_param);
        // echo "<pre>";print_r( $bundle['c_status'] );exit;
        if (!$bundle) {
            $this->session->set_flashdata('message', lang('bundle_not_found'));
            redirect($this->config->item('admin_folder') . '/bundle');
        }
        
        $data               = array();
        $breadcrumb         = array();
        $breadcrumb[]       = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]       = array( 'label' => lang('bundle_bar'), 'link' => admin_url('bundle'), 'active' => 'active', 'icon' => '' );
        $breadcrumb[]       = array( 'label' => $bundle['c_title'], 'link' => admin_url('bundle/overview/' . $bundle['id']), 'active' => 'active', 'icon' => '' );
        $breadcrumb[]       = array( 'label' => 'Overview', 'link' => '', 'active' => 'active', 'icon' => '');
        $data['breadcrumb'] = $breadcrumb;
        
        $courses                = json_decode($bundle['c_courses'],true);
        $data['active_courses'] = (count($courses)>0)?count($courses):'0';
        $data['image_status']   = $bundle['c_image'];
        $data['bundle']         = array('id'=>$id,'active_status'=>$bundle['c_status']);
        //print_r($data);die;

        $this->load->view($this->config->item('admin_folder') . '/bundle_overview', $data);
    }

    public function categories()
    {
        $response                               = array();
        $search_keyword                         = $this->input->post('keyword');
        $categories_param                       = array();
        $categories_param['status']             = 1;
        $categories_param['direction']          = 'ASC';
        $categories_param['not_deleted']        = true;
        $categories_param['select']             = 'id, ct_name';
        $categories_param['search_keyword']     = $search_keyword;
        $data['categories']                     = $this->Category_model->categories($categories_param);
        echo json_encode($data['categories']);
    }
    public function invalidate_bundle_user($param = array())
    {
        $bundle_id      = isset($param['bundle_id'])?$param['bundle_id']:'';
        if(!empty($bundle_id))
        {
            $all_users  = $this->Bundle_model->subscriptions(array('bundle_id'=>$bundle_id));
            if(!empty($all_users))
            {
                foreach($all_users as $user)
                {
                    $this->memcache->delete('enrolled_item_ids_'.$user['bs_user_id']);
                }
            }
            return true;
        }
    }

    //Curl to delete mobile api get subscriptions memcache
    private function reset_mobile_subscriptions($notify_param = array())
    {
        $curlHandle                         = curl_init(site_url()."cron_job/update_mobile_subscription_bundles");
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
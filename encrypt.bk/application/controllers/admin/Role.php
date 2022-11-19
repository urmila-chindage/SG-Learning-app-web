<?php

class Role extends CI_Controller{

    function __construct(){
        parent::__construct();
        date_default_timezone_set('Asia/Kolkata');
        $redirect               = $this->auth->is_logged_in(false, false);
        $this->__admin_index    = 'admin';
        $this->__loggedInUser   = $this->auth->get_current_user_session('admin');
        if (!$redirect)
        {
            redirect('login');
        }
        // Check whether super admin
        if(!in_array($this->__loggedInUser['id'], array(config_item('super_admin'))) )
        {
            redirect('login');           
        }

        $this->lang->load('role');
        $this->load->model('Role_model');
        $this->load->model('Permission_model');
    }

    function index()
    {
        $data                       = array();
        $breadcrumb                 = array();
        $breadcrumb[]               = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]               = array( 'label' => 'Roles', 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']         = $breadcrumb;

        $param                    = array();        
        $param['type']            = '1';
        $param['rl_default_role'] = '0';
        $param['count']           = true;
        $data['total_roles']      = $this->Role_model->roles($param);
        unset($param['count']);
        $param['select']          = 'id,rl_name,rl_status,rl_deleted,rl_default_role,rl_full_course';
        $data['roles']            = $this->Role_model->roles($param);

        $this->load->view($this->config->item('admin_folder').'/role', $data);
    }

    function create_role()
    {
        $response               = array();
        $response['error']      = false;
        $response['message']    = 'Role created successfully';

        $role_name          = $this->input->post('role_name');
        if(trim($role_name) == '')
        {
            $response['error']   = true;
            $response['message'] = 'Role name required';
            echo json_encode($response);exit;
        }
        $role_name_exists = $this->Role_model->role(array('name' => $role_name));
        if( !empty($role_name_exists))
        {
            $response['error']   = true;
            $response['message'] = 'Role already exist';
            echo json_encode($response);exit;            
        }
        $content_types          = array('0' => 'video',
                                        '1' => 'document',
                                        '2' => 'quiz',
                                        '3' => 'assignment',
                                        '4' => 'live_lecture',
                                        '5' => 'youtube',
                                        '6' => 'survey',
                                        '7' => 'html');

        $params                 = array();
        $params['rl_name']      = $role_name;
        $params['rl_status']    = '1';
        $params['rl_content_types'] = json_encode($content_types, JSON_FORCE_OBJECT);
        $role_id                = $this->Role_model->save($params);
        if(!$role_id) {
            $response['error']   = true;
            $response['message'] = 'Role additon failed';           
        } 
        else
        {
            $user_data              = array();
            $user_data['user_id']   = $this->__loggedInUser['id'];
            $user_data['username']  = $this->__loggedInUser['us_name'];
            $user_data['useremail']  = $this->__loggedInUser['us_email'];
            $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
            
            $message_template                       = array();
            $message_template['username']           = $this->__loggedInUser['us_name'];
            $message_template['action']             = "created";
            $message_template['role']               = $this->input->post('role_name');

            $triggered_activity     = 'role_operation';
            log_activity($triggered_activity, $user_data, $message_template); 

            $response['role_id']    = $role_id;
            $modules    = $this->Permission_model->get_modules(array('select' => 'id'));
            $params     = array();
            foreach($modules as $module){
                $params[] = array(
                                     'id' => false
                                    ,'role_id' => $role_id
                                    ,'module_id' => $module['id']
                                    ,'permissions' => ''
                                );
            }
            if(!empty($params))
            {
                $success    = $this->Permission_model->add_permission($params);
            }
        }

        echo json_encode($response);exit;
    }

    function update_role()
    {
        $response               = array();
        $response['error']      = false;
        $response['message']    = 'Role updated successfully';

        $role_id            = $this->input->post('role_id');
        $role_name          = $this->input->post('role_name');
        $role_status        = $this->input->post('role_status');
        if(trim($role_name) == '')
        {
            $response['error']   = true;
            $response['message'] = 'Role name required';
            echo json_encode($response);exit;
        }
        $role_name_exists = $this->Role_model->role(array('name' => $role_name, 'except_id' => $role_id));
        if( !empty($role_name_exists) )
        {
            $response['error']   = true;
            $response['message'] = 'Role already exist';
            echo json_encode($response);exit;            
        }

        $params                 = array();
        $params['id']           = $role_id;
        $params['rl_name']      = $role_name;
        $params['rl_status']    = $role_status;
        $params['updated_date'] = date('Y-m-d H:i:s');
        $save                   = $this->Role_model->save($params);
        if(!$save) {
            $response['error']   = true;
            $response['message'] = 'Role updation failed'; 
        }
        $user_data              = array();
        $user_data['user_id']   = $this->__loggedInUser['id'];
        $user_data['username']  = $this->__loggedInUser['us_name'];
        $user_data['useremail']  = $this->__loggedInUser['us_email'];
        $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
        
        $message_template                       = array();
        $message_template['username']           = $this->__loggedInUser['us_name'];
        $message_template['action']             = "updated";
        $message_template['role']               = $this->input->post('role_name');
        
        $triggered_activity     = 'role_operation';
        log_activity($triggered_activity, $user_data, $message_template); 
        echo json_encode($response);exit;
    }

    function get_role()
    {
        $response               = array();
        $response['error']      = false;

        $role_id    = $this->input->post('role_id');

        $param              = array();
        $param['id']        = $role_id;
        $param['select']    = 'id,rl_name,rl_status,rl_full_course';
        $role = $this->Role_model->role($param);

        if(isset($role)) {
            $response['role']   = $role;
        } else {
            $response['error']  = true;
            $response['message']= 'No roles found';
        }

        echo json_encode($response);exit;
    }

    function delete_role() 
    {
        $response               = array();
        $response['error']      = false;
        $response['message']    = 'Role deletion successfull';

        $role_id                = $this->input->post('role_id');

        $param                  = array();
        $param['id']            = $role_id;
        $param['select']        = 'id';
        $param['rl_default_role'] = '1';
        $check_role             = $this->Role_model->role($param);
        if(empty($check_role))
        {
            $params                  = array();
            $params['id']            = $role_id;
            $params['rl_deleted']    = '1';
            $save = $this->Role_model->save($params);

            if(!$save) {
                $response['error']   = true;
                $response['message'] = 'Role deletion failed';
                echo json_encode($response);exit;            
            }else{
                $user_data              = array();
                $user_data['user_id']   = $this->__loggedInUser['id'];
                $user_data['username']  = $this->__loggedInUser['us_name'];
                $user_data['useremail']  = $this->__loggedInUser['us_email'];
                $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
                
                $message_template                       = array();
                $message_template['username']           = $this->__loggedInUser['us_name'];
                $message_template['action']             = "deleted";
                $message_template['role']               = $this->input->post('role_name');
                
                $triggered_activity     = 'role_operation';
                log_activity($triggered_activity, $user_data, $message_template); 
            }
        } 
        else
        {
            $response['error']      = true;
            $response['message']    = 'Role cannot be deleted';
        }
        echo json_encode($response);exit;
    }

    function restore_role()
    {
        $response               = array();
        $response['error']      = false;

        $role_id                = $this->input->post('role_id');
        $params                  = array();
        $params['id']            = $role_id;
        $params['rl_deleted']    = '0';
        $save = $this->Role_model->save($params);
        if(!$save) {
            $response['error']   = true;
            $response['message'] = 'Role restore failed';
            echo json_encode($response);exit;            
        }

        echo json_encode($response);exit;
    }

    function language()
    {
        $response = array();
        $response['language'] = array();
        $response['language'] = get_instance()->lang->language;
        echo json_encode($response);
    }

    
    // function get_permission() 
    // {
    //     $this->load->library('accesspermission');
    //     $params             = array();
    //     $params['role_id']  = '4';
    //     $params['module_id']= '';
    //     $permissions        = $this->accesspermission->get_permission($params);
    //     // echo "<pre>";
    //     // print_r($permissions);
    //     // die;
    // }

}
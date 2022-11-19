<?php

class Role_settings extends CI_Controller{

    function __construct(){
        parent::__construct();
        $this->lang->load('role');
        $this->load->model('Role_model');
        $this->load->model('Permission_model');
        $this->load->library('accesspermission');
        $this->__loggedInUser   = $this->auth->get_current_user_session('admin');
        $redirect               = $this->auth->is_logged_in(false, false);
        if (!$redirect)
        {
            redirect('login');
        }
         // Check whether super admin
         if(!in_array($this->__loggedInUser['id'], array(config_item('super_admin'))) )
         {
             redirect('login');           
         }
    }

    function basics($role_id = 0)
    {
        if($role_id < 2)
        {
            redirect(admin_url('role'));
        }

        $data                   = array();
        $breadcrumb             = array();
        $breadcrumb[]           = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]           = array( 'label' => 'Role', 'link' => admin_url('role'), 'active' => 'active', 'icon' => '' );
        $breadcrumb[]           = array( 'label' => 'Role Settings', 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']     = $breadcrumb;

        $params                 = array();
        $params['role_id']      = $role_id;
        $data['permissions']    = $this->accesspermission->get_permission($params);


        $params                 = array();
        $params['select']       = 'id, module_name, controller, parent_id, module_permissions';
        $params['parent_id']    = '0';
        $data['modules']        = $this->Permission_model->get_modules($params);

        foreach($data['modules'] as $module)
        {
            $param                 = array();
            $param['role_id']      = $role_id;
            $param['select']       = 'id, module_name, controller, parent_id, module_permissions';
            $param['parent_id']    = $module['id'];
            $data['module_'.$module['id']]  = $this->Permission_model->get_modules($param);
        }

        $params                 = array();
        $params['select']       = "id,rl_name,rl_full_course, rl_content_types";
        $params['id']           = $role_id;
        $data['role']           = $this->Role_model->role($params);
        $data['role_content_types'] = json_decode($data['role']['rl_content_types'], true);
        $this->load->view($this->config->item('admin_folder').'/role_settings', $data);
    }

    function save()
    {
        $role_id            = $this->input->post('role_id');
        $permissions        = $this->input->post('access');
        $course_full_access = $this->input->post('full_course');
        $content_types      = $this->input->post('content_types');

        $params                 = array();
        $params['select']       = 'id';
        $modules                = $this->Permission_model->get_modules($params);
        // echo "<pre>";
        // print_r($this->input->post());
        // print_r(json_encode($content_types, JSON_FORCE_OBJECT));
        // die();
        foreach($modules as $module)
        {
            if( !empty($permissions[$module['id']]) )
            {
                $module_details         = $this->Permission_model->get_module(array(
                                                                    'select'=>'parent_id',
                                                                    'id'=>$module['id']
                                                                ));
                if( $module_details['parent_id'] != '0' )
                {
                    $parent_permission = ( !empty($permissions[$module_details['parent_id']]) )? $permissions[$module_details['parent_id']] : array();
                    if( in_array(1,$parent_permission) )
                    {
                        $params                 = array();
                        $params['role_id']      = $role_id;
                        $params['module_id']    = $module['id'];
                        $params['permissions']  = implode(',', $permissions[$module['id']]);
                        $this->Permission_model->update_permission($params);
                    }
                    else
                    {
                        $params                 = array();
                        $params['role_id']      = $role_id;
                        $params['module_id']    = $module['id'];
                        $params['permissions']  = '';
                        $this->Permission_model->update_permission($params);
                    }
                }
                else
                {
                    $params                 = array();
                    $params['role_id']      = $role_id;
                    $params['module_id']    = $module['id'];
                    $params['permissions']  = implode(',', $permissions[$module['id']]);
                    $this->Permission_model->update_permission($params);    
                }
            }
            else
            {
                $params                 = array();
                $params['role_id']      = $role_id;
                $params['module_id']    = $module['id'];
                $params['permissions']  = '';
                $this->Permission_model->update_permission($params);
            }
        }
        $params                     = array();
        $params['id']               = $role_id;
        $params['rl_full_course']   = $course_full_access;
        $params['rl_content_types'] = json_encode($content_types, JSON_FORCE_OBJECT);
        $save                       = $this->Role_model->save($params);
        $permissions = $this->accesspermission->update_cache();
        $this->memcache->delete('permissions');
        $this->memcache->delete('module_permissions');
        $response = array(
            'success' => true,
            'message' => 'Updated successfully'
        );
        $this->session->set_flashdata('popup',$response);
        redirect(admin_url('role_settings/basics/').$role_id);
    }

    function language()
    {
        $response = array();
        $response['language'] = array();
        $response['language'] = get_instance()->lang->language;
        echo json_encode($response);
    }
    
}
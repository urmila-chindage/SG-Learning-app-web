<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Accesspermission
{
    var $CI;
    function __construct(){
        $this->CI = & get_instance();
        $this->CI->load->driver('cache');
        $this->__super_admin_role = 1;
        $this->__full_access = array('1', '2', '3', '4');
        $this->timeout = 7200;
    }

    /*
    * Inputing role id and controller name
    * return array permissions
    */
    function get_permission($params = array())
    {
        $role_id        = isset($params['role_id'])? $params['role_id']:false;
        $module         = isset($params['module'])? $params['module']:false;
        $module_id      = false;
        if($module != false)
        {
            $param                  = array();
            $param['controller']    = $module;
            $module_id              = $this->get_module($param);            
        }

        if($role_id == 1)
        {
            return $this->__full_access;
        }
        else
        {
            // $param                  = array();
            // $param['controller']    = $module;
            // $module_id              = $this->get_module($param);            
            if($role_id)
            {
                $permissions    = $this->CI->memcache->get(array( 'key' => 'permissions'));
        
                /* 
                * Check if permissions for the role_id in cache and update cache
                */
                if(!isset($permissions['role_id']) || empty($permissions['role_id']))
                {
                    $permissions = $this->update_cache();
                }

                return ($module_id)? $permissions[$role_id][$module_id]: $permissions[$role_id];
            } 
            else 
            {
                return array('message' => 'Role id required');
            }
        }
    }

    /*
    * Inputing role id and controller name
    * return array permissions
    */
    function get_permission_course($params = array())
    {
        $role_id        = isset($params['role_id'])? $params['role_id']:false;
        $user_id        = isset($params['user_id'])? $params['user_id']:0;
        $course_id      = isset($params['course_id'])? $params['course_id']:0;
        $fetch          = isset($params['fetch'])? $params['fetch']:'module';//module OR course
        if($role_id == 1)
        {
            return $this->__full_access;
        }
        else
        {
            $this->CI->load->model('Permission_model');
            $has_course_permission        = $this->CI->Permission_model->check_user_previlege_for_course($params);
            return ($has_course_permission)?$this->get_permission($params):array();    
        }
    }


    //start system functions
    private function is_superadmin($role_id=0)
    { 
          return ($role_id == $this->__super_admin_role);
    }

    private function get_module($param = array())
    {
        $controller = $param['controller'];
        $module     = $this->CI->memcache->get(array( 'key' => 'module_'.$controller));
        if(!$module)
        {
            $this->CI->load->model('Permission_model');
            $input                 = array();
            $input['controller']   = $param['controller'];
            $module                = $this->CI->Permission_model->get_module($input);
            $module_id             = 0;
            if($module)
            {
                $this->CI->memcache->set('module_'.$controller, $module, $this->timeout);
                $module_id = $module['id'];
            }
        }
        else
        {
            $module_id = $module['id'];
        }
        return $module_id;
    }

    private function _group_by_order($permissions = array()) 
    {
        $sorted_permissions = array();
        foreach($permissions['permissions'] as $permission) 
        {
            if(!isset($sorted_permissions[$permission['role_id']]))
            {
                $sorted_permissions[$permission['role_id']] = array();
            }
            $sorted_permissions[$permission['role_id']][$permission['module_id']] = explode(',',$permission['permissions']);
        }
        return $sorted_permissions;
    }

    function update_cache()
    {
        $this->CI->load->model('Permission_model');
        $permissions    = $this->CI->Permission_model->get_roles_access();
        $permissions    = $this->_group_by_order(array('permissions' => $permissions));
        $this->CI->memcache->set('permissions', $permissions, $this->timeout);
        return $permissions;
    }

    function module_roles($param = array())
    {
        $module         = isset($param['module'])?$param['module']:0;
        $permission     = isset($param['permission'])?$param['permission']:1;
        $role_ids       = array();
        $module_id      = $this->get_module(array('controller' => $module));

        $permissions    = $this->CI->memcache->get(array( 'key' => 'module_permissions'));

        if(!is_array($permissions))
        {
            $this->CI->load->model('Permission_model');
            $param = array();
            if( $module == 'course' )
            {
                $param['full_course_access_only'] = true;
            }
            $permissions            = $this->CI->Permission_model->get_roles_access($param);
            $permissions_sorted     = array();
            foreach($permissions as $permission_single)
            {
                // echo '<pre>'; print_r($permission_single);die();

                $permission_moded = array();
                $permission_moded['permissions'] = explode(',',$permission_single['permissions']);
                $permissions_sorted[$permission_single['module_id']][$permission_single['role_id']] = $permission_moded;

                if($permission_single['module_id'] == $module_id && in_array($permission,$permission_moded['permissions']))
                {
                    $role_ids[] = $permission_single['role_id'];
                }
            }
            $permissions = $permissions_sorted;
            $this->CI->memcache->set('module_permissions', $permissions, $this->timeout);
        }

        if(empty($role_ids))
        {
            foreach($permissions[$module_id] as $role_id => $module_data)
            {
                if(in_array($permission,$module_data['permissions']))
                {
                    $role_ids[] = $role_id;
                }
            }
        }

        return $role_ids;
    }

    function previleged_users($param = array())
    {
        $module         = isset($param['module'])?$param['module']:'';
        $permission     = isset($param['permission'])?$param['permission']:1;
        $user_ids       = array();

        $role_ids       = $this->module_roles(array('module' => $module,'permission' => $permission));

        $this->CI->load->model('Permission_model');
        $users          = $this->CI->Permission_model->get_users_in_roles(array('role_ids' =>$role_ids));

        foreach($users as $user)
        {
            $user_ids[] = $user;
        }

        return $user_ids;
    }

    //end
}
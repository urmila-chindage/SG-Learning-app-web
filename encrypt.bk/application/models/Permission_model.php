<?php


class Permission_model extends CI_Model{

    function __construct() {
        parent::__construct();
    }

    function get_roles_access($params = array())
    {
        $role_id                    = (isset($params['role_id'])? $params['role_id']:false);
        $full_course_access_only    = (isset($params['full_course_access_only'])? $params['full_course_access_only']:false);
        
        if($role_id)
        {
            $this->db->where('role_id', $role_id);
        }
        if($full_course_access_only)
        {
            $this->db->where('role_id IN (SELECT id FROM roles WHERE rl_full_course = "1")');
        }
        $this->db->select('role_id,module_id,permissions');
        $return = $this->db->get('roles_modules_meta')->result_array();
        return $return;
    }

    function get_modules($params = array()) 
    {
        $select     = (isset($params['select'])? $params['select']:'*');
        if(isset($params['parent_id']))
        {
            $this->db->where('parent_id', $params['parent_id']);
        }
        $this->db->where("(account_id = '0' OR account_id='".config_item('id')."')");
        $this->db->select($select);
        return $this->db->get('modules')->result_array();
    }

    function get_module($params = array())
    {
        $module_id          = (isset($params['id'])? $params['id']:false);
        $controller_name    = (isset($params['controller'])? $params['controller']:false);
        $select             = (isset($params['select'])? $params['select']:'*');

        if($module_id)
        {
            $this->db->where('id', $module_id);
        }
        if($controller_name)
        {
            $this->db->where('controller', $controller_name);
        }
        $this->db->select($select);
        return $this->db->get('modules')->row_array();
    }

    function update_permission($params = array())
    {
        $role_id        = (isset($params['role_id'])? $params['role_id']:0);
        $module_id      = (isset($params['module_id'])? $params['module_id']:0);
        $this->db->where('role_id', $role_id);
        $this->db->where('module_id', $module_id);
        return $this->db->update('roles_modules_meta', $params);
    }

    function add_permission($params = array())
    {
        return $this->db->insert_batch('roles_modules_meta', $params);
    }

    function check_user_previlege_for_course($param=array())
    {
        $user_id    = isset($param['user_id'])?$param['user_id']:0;
        $course_id  = isset($param['course_id'])?$param['course_id']:0;
        return $this->db
                        ->where('ct_tutor_id', $user_id)
                        ->where('ct_course_id', $course_id)
                        ->count_all_results('course_tutors');
    }

    function get_users_in_roles($param = array())
    {
        $role_ids   = isset($param['role_ids'])?$param['role_ids']:array();
        $result     = array();
        if(!empty($role_ids))
        {
            $this->db->select('id,us_name,us_email');
            $this->db->where('us_deleted','0');
            $this->db->where('us_status','1');
            $this->db->where_in('us_role_id',$role_ids);
            $this->db->where_not_in('us_role_id',array('8'));
            $this->db->where('us_account_id',config_item('id'));
            $result = $this->db->get('users')->result_array(); 
        }

        return $result;
    }
}
<?php 
Class Role_model extends CI_Model
{	
    function __construct()
    {
        parent::__construct();
    }

    function roles($param = array())
    {
        $select         = isset($param['select'])? $param['select']:'*';
        $limit 			= isset($param['limit'])?$param['limit']:0;
        $offset 		= isset($param['offset'])?$param['offset']:0;
        $order_by 		= isset($param['order_by'])?$param['order_by']:'id';
        $direction 		= isset($param['direction'])?$param['direction']:'ASC';
        $status 		= isset($param['status'])?$param['status']:'';
        $type           = isset($param['type'])?$param['type']:'';
        $count          = isset($param['count'])?$param['count']:false;
        $institute_id   = isset($param['institute_id'])?$param['institute_id']:false;
        $full_course_access = isset($param['full_course_access'])?$param['full_course_access']:'';
        if($institute_id)
        {
            $this->db->where('rl_name !=', 'Subadmin');
        }
        if($status != '')
        {
            $this->db->where('rl_status',$status);
        }
        if($full_course_access != '')
        {
            $this->db->where('rl_full_course',$full_course_access);
        }
        if($type != '')
        {
            $this->db->where('rl_type',$type);
        }
        if(isset($param['rl_default_role']))
        {
            $this->db->where('rl_default_role', $param['rl_default_role']);
        }
        if(isset($param['deleted']))
        {
            $this->db->where('rl_deleted', $param['deleted']);
        }
        
        $this->db->select($select);
        $this->db->order_by($order_by, $direction);
        if($count)
        {
            return $this->db->count_all_results('roles');
        } 
        else 
        {
            return $this->db->get('roles')->result_array();
        }
    }

    function role($param = array())
    {
        $select = isset($param['select'])? $param['select']:'*';
        $name   = (isset($param['name'])? $param['name']:'');
        $id     = (isset($param['id'])? $param['id']:0);
        $except_id = (isset($param['except_id'])? $param['except_id']:false);
        
        if($name != '')
        {
            $this->db->where('rl_name', $name);
        }
        else
        {
            $this->db->where('id', $id);
        }
        if($id) 
        {
            $this->db->where('id', $id);
        }
        if($except_id) 
        {
            $this->db->where('id!=', $except_id);
        }
        if(isset($param['rl_default_role']))
        {
            $this->db->where('rl_default_role', $param['rl_default_role']);
        }
        $this->db->select($select);
        return $this->db->get('roles')->row_array();
        // $this->db->get('roles');
        // return $this->db->last_query();
    }

    function save($param = array()) {
        if(isset($param['id'])){
            $this->db->where('id', $param['id']);
            return $this->db->update('roles', $param);   
        } else {
            $this->db->insert('roles', $param);
            return $this->db->insert_id();
        }        
    }

}
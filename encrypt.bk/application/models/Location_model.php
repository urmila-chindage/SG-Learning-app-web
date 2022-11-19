<?php 
Class Location_model extends CI_Model
{	
    function __construct()
    {
        parent::__construct();
        //$this->exclude_ids = array('2', '1');
        $this->exclude_ids = array(config_item('super_admin'));
    }
    
    function states($param=array())
    {
        $this->db->order_by("state_name", "ASC");
        return $this->db->get('states')->result_array();
    }
    function state($param=array())
    {
        $return = array();
        $id = isset($param['id'])?$param['id']:false;
        if($id)
        {
            $this->db->where('id', $param['id']);
            $return = $this->db->get('states')->row_array();
        }
        return $return;
    }
    function cities($param=array())
    {
        $state_id     = isset($param['state_id'])?$param['state_id']:false;
        $city_name    = isset($param['city_name'])?$param['city_name']:false;
        if($city_name)
        {
            $this->db->where('city_name LIKE "%'.$city_name.'%"');
        }
        if($state_id)
        {
            $this->db->where('state_id', $state_id);
        }
        $this->db->order_by("city_name", "ASC");
        return $this->db->get('cities')->result_array();
    }
    function city($param=array())
    {
        $id           = isset($param['id'])?$param['id']:false;
        $city_name    = isset($param['city_name'])?$param['city_name']:false;
        if($id)
        {
           $this->db->where('id', $param['id']);
        }
        if($city_name)
        {
            $this->db->where('city_name LIKE "'.$city_name.'"');
        }
        return $this->db->get('cities')->row_array();
    }
    function faculty_city($param=array())
    {
        $id           = isset($param['id'])?$param['id']:false;
        $return       = array();
        if($id)
        {
           $this->db->where('id', $param['id']);
           $return = $this->db->get('cities')->row_array();
        }
        return $return;
    }
}
<?php

Class Tutor_model extends CI_Model
{	
    function __construct()
    {
        parent::__construct();
    }
	
    function tutors($param=array())
    {
        $limit 			= isset($param['limit'])?$param['limit']:0;
        $offset 		= isset($param['offset'])?$param['offset']:0;
        $order_by 		= isset($param['order_by'])?$param['order_by']:'id';
        $direction 		= isset($param['direction'])?$param['direction']:'DESC';
        $status 		= isset($param['status'])?$param['status']:0;
        $role   		= isset($param['role'])?$param['role']:0;
		
        $this->db->select('users.*');
        $this->db->order_by($order_by, $direction);
        if($limit>0)
        {
            $this->db->limit($limit, $offset);
        }
        if( $status )
        {
            $this->db->where('us_status', $status); 
        }
        if( $role )
        {
            $this->db->where('us_role_id', '3'); 
        }

        $this->db->where('us_account_id', config_item('id'));
        $result = $this->db->get('users')->result_array();
        //echo $this->db->last_query();die();
        return $result;
    }
    
    function get_tutors_assigned_course($param=array())
    {
        $course     = isset($param['course'])?$param['course']:0;
        
        $this->db->select('course_tutors.*');
        if( $course )
        {
            $this->db->where('ct_course_id', $course); 
        }
        $result = $this->db->get('course_tutors')->result_array();
        return $result;
    }
    
    function get_tutor_name($id){
        $this->db->select('us_name, us_image, id');
        $this->db->where_in('id', array_map('intval', $id));
        $result = $this->db->get('users')->result_array();
        return $result;
    }
    
    // function delete($param){
    //     $tutor_id       = isset($param['tutor_id'])?$param['tutor_id']:false;
    //     $tutor_ids      = isset($param['tutor_ids'])?$param['tutor_ids']:false;
    //     $course_id      = isset($param['course_id'])?$param['course_id']:false; 
    //     if($tutor_id){
    //         $this->db->where('ct_tutor_id', $tutor_id);
    //     }
    //     if($tutor_ids){
    //         $this->db->where_in('ct_tutor_id', $tutor_ids);
    //     }
    //     if($course_id){
    //         $this->db->where('ct_course_id', $course_id);
    //     }
    //     $this->db->delete('course_tutors');
    // }
    function delete($data){
        if(isset($data['ct_course_id']))
        {
            $this->db->where('ct_course_id', $data['ct_course_id']);
        }
        if(isset($data['ct_tutor_id']))
        {
            $this->db->where('ct_tutor_id', $data['ct_tutor_id']);
        }
        if(isset($data['ct_course_id']) && isset($data['ct_tutor_id']))
        {
            $this->db->delete('course_tutors');
        }
    }
    
    function save($data){
        $this->db->where('ct_course_id', $data['ct_course_id']);
        $this->db->where('ct_tutor_id', $data['ct_tutor_id']);
        $this->db->insert('course_tutors', $data);  
    }
    function save_tutors($data){

        $this->db->insert_batch('course_tutors',$data);
        return true;
    }

    function get_tutor_name_by_course($cid){

         
        $this->db->select('users.us_name, users.id, users.us_image,users.us_email');
        $this->db->from('users');
        $this->db->join('course_tutors', 'course_tutors.ct_tutor_id = users.id');
        $this->db->where('course_tutors.ct_course_id', $cid);
        $result = $this->db->get()->result_array();
        return $result;
    }
}

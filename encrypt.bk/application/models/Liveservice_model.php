<?php

Class Liveservice_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    function save_live($data)
    {
	if($data['id'])
	{
            $this->db->where('id', $data['id']);
            $this->db->update('live_lectures', $data);
            return $data['id'];
        }
	else
	{
            $this->db->insert('live_lectures', $data);
            return $this->db->insert_id();
	}
    }
    
    function save_live_recording($data)
    {
	if($data['id'])
	{
            $this->db->where('id', $data['id']);
            $this->db->update('live_lecture_recordings', $data);
            return $data['id'];
        }
	else
	{
            $this->db->insert('live_lecture_recordings', $data);
            return $this->db->insert_id();
	}
    }
    function save_live_presentation($data)
    {
	if($data['id'])
	{
            $this->db->where('id', $data['id']);
            $this->db->update('live_presentation_details', $data);
            return $data['id'];
        }
	else
	{
            $this->db->insert('live_presentation_details', $data);
            return $this->db->insert_id();
	}
    }
    
    function save_live_users($data)
    {
	if($data['id'])
	{
            $this->db->where('id', $data['id']);
            $this->db->update('live_lecture_users', $data);
            return $data['id'];
        }
	else
	{
            $this->db->insert('live_lecture_users', $data);
            return $this->db->insert_id();
	}
    }
    
    function get_live_user($param = array())
    {
        $live_id = isset($param['live_id'])?$param['live_id']:false;
        $user_id = isset($param['user_id'])?$param['user_id']:false;
        if($live_id)
        {
            $this->db->where('llu_live_id' , $live_id);
        }
        if($user_id)
        {
            $this->db->where('llu_user_id' , $user_id);
        }
        return $this->db->get('live_lecture_users')->row_array();
    }
    
    function live($param = array())
    {
        $id = isset($param['id'])?$param['id']:false;
        if($id)
        {
            $this->db->where('id' , $id);
        }
        return $this->db->get('live_lectures')->row_array();
    }
    
    function live_presentations($param=array())
    {
        $live_id = isset($param['live_id'])?$param['live_id']:false;
        if($live_id)
        {
            $this->db->where('lpd_live_id' , $live_id);
        }
        return $this->db->get('live_presentation_details')->result_array();
    }
    
    function live_lecture_recording($param = array())
    {
        $clip_id = isset($param['clip_id'])?$param['clip_id']:false;
        $live_id = isset($param['live_id'])?$param['live_id']:false;
        if($clip_id)
        {
            $this->db->where('llr_clip_id' , $clip_id);
        }
        if($live_id)
        {
            $this->db->where('llr_live_id' , $live_id);
        }
        return $this->db->get('live_lecture_recordings')->row_array();
    }
    
    function delete_presentation($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('live_presentation_details');
    }
}
?>
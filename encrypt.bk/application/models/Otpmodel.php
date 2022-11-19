<?php
Class Otpmodel extends CI_Model
{	
    function __construct()
    {
        parent::__construct();
    }
    
    function check_otp($param = array()){
        $user_id = isset($param['user_id'])?$param['user_id']:false;
        if($user_id){
            $this->db->where('so_user',$user_id);
        }
        $this->db->where('so_account_id',config_item('id'));

        $result = $this->db->get('sms_otp')->row_array();

        return $result;
    }

    function save_otp($param = array())
    {
        if(isset($param['id']))
        {
            $this->db->where('id', $param['id']);
            $this->db->update('sms_otp', $param);
            return $param['id'];
        }
        else
        {
            $this->db->insert('sms_otp', $param);
            return $this->db->insert_id();
        }
    }

    function verify_otp($param = array()){
        $otp            = isset($param['otp'])?$param['otp']:0;
        $user_id        = isset($param['user_id'])?$param['user_id']:0;
        $this->db->where('so_otp',$otp);
        $this->db->where('so_user',$user_id);
        $this->db->where('so_status','1');
        $this->db->where('so_account_id',config_item('id'));

        $result = $this->db->get('sms_otp')->row_array();

        return $result;
    }

    function get_user($param = array()){
        if(isset($param['select'])){
            $this->db->select($param['select']);
        }else{
            $this->db->select('*');
        }

        if(isset($param['user_id'])){
            $this->db->where('id',$param['user_id']);
        }

        $this->db->where('us_account_id',config_item('id'));

        if(isset($param['user_id'])){
            $result = $this->db->get('users')->row_array();
        }else{
            $result = $this->db->get('users')->result_array();
        }

        return $result;
    }
}
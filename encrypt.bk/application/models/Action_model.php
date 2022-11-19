<?php
Class Action_model extends CI_Model
{	
    function __construct()
    {
        parent::__construct();
    }

    function token($param = array()){
        $id         = isset($param['id'])?$param['id']:false;
        $token      = isset($param['token'])?$param['token']:false;
        $status     = isset($param['status'])?$param['status']:'';
        $expiration = isset($param['not_expired'])?true:false;

        $this->db->select('*');

        if($id){
            $this->db->where('id',$id);
        }
        if($token){
            $this->db->where('at_token',$token);
        }
        if($status != ''){
            $this->db->where('at_status',$status);
        }
        if($expiration){
            $this->db->where('at_expire < NOW()');
        }

        $this->db->where('at_account_id',config_item('id'));

        $result     = $this->db->get('actions_token')->row_array();

        return $result;
    }

    function save($param = array()){
        $param['at_account_id']     = config_item('id');
        $insert     = isset($param['id'])?false:true;
        $return     = 0;
        if($insert){
            $this->db->insert('actions_token',$param);
            $return     = $this->db->insert_id();
        }else{
            $this->db->where('actions_token.id',$param['id']);
            $this->db->update('actions_token',$param);
            $return     = $param['id'];
        }

        return $return;
    }

    // function get_existing()
    // {
    //     $this->db->select('id,um_user_id');
    //     $user_ids = $this->db->get('user_messages')->result_array();

    //     return $user_ids;
    // }

    // function get_all_users()
    // {
    //     $this->db->select('id');
    //     $user_ids = $this->db->get('users')->result_array();

    //     return $user_ids;
    // }

    // function insert_batch($data = array())
    // {
    //     $this->db->insert_batch('user_messages', $data);
    // }
}
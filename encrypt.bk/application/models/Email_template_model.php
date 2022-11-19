<?php 
Class Email_template_model extends CI_Model
{	
    function __construct()
    {
        parent::__construct();
    }
    function getEmailtemplates($param = array()){
        
        $order_by       = isset($param['order_by'])?$param['order_by']:'email_templates.em_name';
        $direction      = isset($param['direction'])?$param['direction']:'ASC';
        $limit          = isset($param['limit'])?$param['limit']:false;
        $filter         = (isset($param['filter']))?$param['filter']:'';
        $offset         = isset($param['offset'])?$param['offset']:"0";
        $keyword        = isset($param['keyword'])?$param['keyword']:'';

        $this->db->select('email_templates.id,email_templates.em_name,email_templates.em_message,email_templates.em_type,email_templates.em_deleted,email_templates.em_status,email_templates.action,email_templates.action_by,email_templates.created,email_templates.updated,users.us_name');
        $this->db->join('users','email_templates.action_by = users.id','left');
        if(isset($param['id'])){
            $this->db->where('email_templates.id',$param['id']);
        }

        $this->db->order_by($order_by,$direction);
        if($keyword != '')
        {
            $this->db->like('email_templates.em_name', $keyword);
        }
        
        if($limit){
            $this->db->limit($limit,$offset);
        }
        $this->db->where("(em_account = '0' OR em_account='".config_item('id')."')");

        if(isset($param['id'])){
            $response = $this->db->get('email_templates')->row_array();
        }else{
            $response = $this->db->get('email_templates')->result_array();
        }

        //echo $this->db->last_query();die;
        return $response;
    }
    
    function adminEmail($param = array()){
        $email_id       = isset($param['id'])?$param['id']:false;
        $select         = isset($param['select'])?$param['select']:false;

        if($select){
            $this->db->select($select);
        }else{
            $this->db->select('*');
        }

        if($email_id){
            $this->db->where('id',$email_id);
        }
        $this->db->where('em_account',config_item('id'));
        if($email_id){
            $result = $this->db->get('email_templates')->row_array();
        }else{
            $result = $this->db->get('email_templates')->result_array();
        }

        return $result;
    }

    function addEmail($param = array()){
        $values = isset($param['values'])?$param['values']:false;
        $id     = isset($param['id'])?$param['id']:false;
        $return = false;

        if($values){
            if($id){
                $this->db->where('email_templates.id',$id);
                $this->db->where('em_account',config_item('id'));
                $this->db->update('email_templates',$values);
                $return = true;
            }
        }
        return $return;
    }

    /*function mail_template($param=array())
    {
        $email_id   = isset($param['email_id'])?$param['email_id']:0;
        $email_code = isset($param['email_code'])?$param['email_code']:'';
        if($email_id)
        {
            $this->db->where('email_templates.id',$email_id);
        }
        if($email_code)
        {
            $this->db->where('email_templates.em_code',$email_code);
        }
        $this->db->where('em_account',config_item('id'));
        return $this->db->get('email_templates')->row_array();
    }*/

    function mail_template($param=array())
    {
        $email_id = isset($param['email_id'])?$param['email_id']:0;
        $email_code = isset($param['email_code'])?$param['email_code']:'';
        if($email_id)
        {
            $this->db->where('email_templates.id',$email_id);
        }
        if($email_code)
        {
            $this->db->where('email_templates.em_code',$email_code);
        }
            $this->db->where(' (em_account_id = "'.config_item('id').'" OR em_account_id = "0") ');
        // $this->db->where('em_account',config_item('id'));
        $return = $this->db->get('email_templates')->row_array();
        // echo $this->db->last_query();die;
        return $return;
    }
}
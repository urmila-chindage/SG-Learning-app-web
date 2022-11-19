<?php 
Class Mailtemplate_model extends CI_Model
{	
    function __construct()
    {
        parent::__construct();
    }
    
    function mail_templates($param=array())
    {
        if( isset($param['status'])) 
	{
            $this->db->where('et_status', 1);
	}
        $this->db->where('et_account_id', config_item('id'));
	$result = $this->db->get('email_template')->result_array();
        return $result;
    }
    
    function mail_template($param=array())
    {
        if( isset($param['id'])) 
	{
            $this->db->where('id', $param['id']);
	}
        $this->db->where('et_account_id', config_item('id'));
	return $this->db->get('email_template')->row_array();
    }
}
?>

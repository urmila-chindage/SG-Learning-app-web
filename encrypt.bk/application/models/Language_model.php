<?php 
Class Language_model extends CI_Model
{	
    function __construct()
    {
        parent::__construct();
    }
	
    
    function languages($param = array())
    {
        $result = $this->db->get('web_languages')->result_array();
        return $result;
    }
}
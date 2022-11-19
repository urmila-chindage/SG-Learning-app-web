<?php 
Class Log_model extends CI_Model
{	
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    function log_action_points($param = array())
    {
        $select = isset($param['select'])?$param['select']:'*';
        $this->db->select($select);
        $this->db->from('log_actions');
        $this->db->where('la_points>0');
        $result = $this->db->get();

        return $result->result_array();
    }
    function change_points($save_param,$filter_param=array()){

        $id= isset($filter_param['id'])?$filter_param['id']:false;
        if($id){
            $this->db->where('id',$id);
            
        }
        $result = $this->db->update('log_actions',$save_param);
        return $result;
    }
}
?>

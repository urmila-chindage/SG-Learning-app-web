<?php 
Class Grade_model extends CI_Model
{	
    function __construct()
    {
        parent::__construct();
    }
    
    function addGrade($param = array()){
        $values = isset($param['values'])?$param['values']:false;
        $id     = isset($param['id'])?$param['id']:false;
        $return = false;

        if($values){
            if($id){
                $this->db->select('grades.id');
                $this->db->where('grades.gr_range_from',$values['gr_range_from']);
                $this->db->where('grades.gr_range_to',$values['gr_range_to']);
                $check_data = $this->db->get('grades');
                $results    = $check_data->num_rows();
                if($results>0){
                    $return = false;
                } else {
                    $this->db->where('grades.id',$id);
                    $this->db->where('gr_account',config_item('id'));
                    $this->db->update('grades',$values);
                    $return = true;
                }
            }
        }
        return $return;
    }

    
    function getGrades($param = array()){
        
        $order_by       = isset($param['order_by'])?$param['order_by']:'grades.id';
        $direction      = isset($param['direction'])?$param['direction']:'ASC';
        $limit          = isset($param['limit'])?$param['limit']:false;
        $filter         = (isset($param['filter']))?$param['filter']:'';
        $offset         = isset($param['offset'])?$param['offset']:"0";

        $this->db->select('grades.id,grades.gr_name,grades.gr_range_from,grades.gr_range_to,grades.gr_deleted,grades.gr_status');
        $this->db->join('users','grades.action_by = users.id','left');
        if(isset($param['id'])){
            $this->db->where('grades.id',$param['id']);
        }

        $this->db->order_by($order_by,$direction);

        if($limit){
            $this->db->limit($limit,$offset);
        }

        $this->db->where('gr_account',config_item('id'));

        if(isset($param['id'])){
            $response = $this->db->get('grades')->row_array();
        }else{
            $response = $this->db->get('grades')->result_array();
        }
        return $response;
    }

    function adminGrade($param = array()){
        $id             = isset($param['id'])?$param['id']:false;
        $gr_deleted     = isset($param['gr_deleted']) ? $param['gr_deleted'] : false;
        $select         = isset($param['select'])?$param['select']:false;

        if($select){
            $this->db->select($select);
        }else{
            $this->db->select('*');
        }
        if ($gr_deleted) {
            $this->db->where('gr_deleted', '0');
        }
        if($id){
            $this->db->where('id',$id);
        }
        $this->db->where('gr_account',config_item('id'));
        if($id){
            $result = $this->db->get('grades')->row_array();
        }else{
            $result = $this->db->get('grades')->result_array();
        }

        return $result;
    }

}
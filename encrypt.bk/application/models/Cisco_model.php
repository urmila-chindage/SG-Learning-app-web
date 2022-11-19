<?php
class Cisco_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function save_recording($data)
    {
        if($data['id']) 
        {
            $this->db->where('id', $data['id']);
            $this->db->update('cisco_recordings', $data);
            return $data['id'];
        } 
        else 
        {
            $this->db->insert('cisco_recordings', $data);
            return $this->db->insert_id();
        }
    }

    function cisco_recording_dates()
    {
        return $this->db->query('SELECT DISTINCT cr_date FROM cisco_recordings ORDER BY cr_date DESC')->result_array();
    }
    function cisco_recordings( $param = array() )
    {
        $date       = isset($param['date'])?$param['date']:false;
        $order_by   = isset($param['order_by'])?$param['order_by']:'cr_created_datetime';
        $direction 	= isset($param['direction'])?$param['direction']:'DESC';
        $limit      = isset($param['limit'])?$param['limit']:false;
        $offset     = isset($param['offset'])?$param['offset']:0;
        
        $this->db->order_by($order_by, $direction);
        if($date)
        {
            $this->db->where('cr_date', $date);
        }
        if($limit)
        {
            $this->db->limit($limit, $offset);
        }
        return $this->db->get('cisco_recordings')->result_array();
    }
}
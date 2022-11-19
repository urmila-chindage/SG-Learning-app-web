<?php
Class Expertlectures_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function expert_lectures($param=array())
    {
        $limit 			= isset($param['limit'])?$param['limit']:0;
        $offset 		= isset($param['offset'])?$param['offset']:0;
        $order_by 		= isset($param['order_by'])?$param['order_by']:'id';
        $direction 		= isset($param['direction'])?$param['direction']:'DESC';
        $status 		= isset($param['status'])?$param['status']:'';
        $count       	= isset($param['count'])?$param['count']:false;
        $not_deleted    = isset($param['not_deleted'])?$param['not_deleted']:false;

        $keyword 		= isset($param['keyword'])?$param['keyword']:'';
        $filter 		= isset($param['filter'])?$param['filter']:0;
        

        $this->db->select('expert_lectures.*, users.us_name  as wa_name_author, web_actions.wa_name, web_actions.wa_code');
        $this->db->join('users', 'expert_lectures.action_by = users.id', 'left');
        $this->db->join('web_actions', 'expert_lectures.action_id = web_actions.id', 'left');

        $this->db->order_by($order_by, $direction);
        if($limit>0)
        {
            $this->db->limit($limit, $offset);
        }

        if( $keyword )
        {
            $this->db->like('expert_lectures.el_title', $keyword); 
        }

        if( $not_deleted )
        {
            $this->db->where('expert_lectures.el_deleted', '0'); 
        }

        if( $filter )
        {
            switch ($filter) {
                case 'active':
                    $status = '1';
                    $this->db->where('expert_lectures.el_deleted', '0'); 
                    break;
                case 'inactive':
                    $status = '0';
                    $this->db->where('expert_lectures.el_deleted', '0'); 
                    break;
                case 'deleted':
                    $this->db->where('expert_lectures.el_deleted', '1'); 
                    break;
                default:
                    break;
            }
        }
        
        if( $status != '' )
        {
            $this->db->where('expert_lectures.el_status', $status); 
        }
        
        $this->db->where('expert_lectures.el_account_id', config_item('id'));
        
        if( $count )
        {
            $result = $this->db->count_all_results('expert_lectures');            
        }
        else
        {
            $result = $this->db->get('expert_lectures')->result_array();
        }
        //echo $this->db->last_query();die;
        return $result;
    }

    function expert_lecture($param=array())
    {
        $this->db->select('expert_lectures.*, users.us_name as wa_name_author,  web_actions.wa_name, web_actions.wa_code');
        if( isset($param['status'])) 
        {
            $this->db->where('expert_lectures.el_status', 1);
        }
        if( isset($param['name'])) 
        {
            if( isset($param['id'])) 
            {
                $this->db->where('expert_lectures.id!=', $param['id']);
            }
            $this->db->where('el_title', $param['name']);
        }
        if( isset($param['id'])) 
        {
            $this->db->where('expert_lectures.id', $param['id']);
        }
        
        $this->db->join('users', 'expert_lectures.action_by = users.id', 'left');
        $this->db->join('web_actions', 'users.action_id = web_actions.id', 'left');
        $this->db->where('expert_lectures.el_account_id', config_item('id'));
        $return = $this->db->get('expert_lectures')->row_array(); 
        //echo $this->db->termlast_query();die;
        return $return;
    }

    function save($data)
    {
        if($data['id'])
        {
            $this->db->where('id', $data['id']);
            $this->db->update('expert_lectures', $data);
            return $data['id'];
        }
        else
        {
            $this->db->insert('expert_lectures', $data);
            return $this->db->insert_id();
        }
    }
}
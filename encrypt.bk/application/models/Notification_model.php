<?php 
Class Notification_model extends CI_Model
{	
    function __construct()
    {
        parent::__construct();
    }
    
    function notifications( $param=array() )
    {
        $limit 			= isset($param['limit'])?$param['limit']:0;
        $offset 		= isset($param['offset'])?$param['offset']:0;
        $order_by 		= isset($param['order_by'])?$param['order_by']:'id';
        $direction 		= isset($param['direction'])?$param['direction']:'DESC';
        $status 		= isset($param['status'])?$param['status']:'';
        $count       	= isset($param['count'])?$param['count']:false;
        $select 		= isset($param['select'])?$param['select']:'notifications.*';
        $keyword 		= isset($param['keyword'])?$param['keyword']:'';
        $filter 		= isset($param['filter'])?$param['filter']:'';
        $date 		    = isset($param['date'])?$param['date']:'';

        $this->db->select($select);
        $this->db->order_by($order_by, $direction);
        if($limit>0)
        {
            $this->db->limit($limit, $offset);
        }
        if($keyword)
        {
            $this->db->like('notifications.n_title', $keyword); 
        }
        if($filter != '')
        {
            switch ($filter) {
                case 'active':
                    $status = '1';
                    $this->db->where('notifications.n_expiry_date >=', date('Y-m-d')); 
                    break;
                case 'inactive':
                    $status = '0';
                    $this->db->where('notifications.n_expiry_date >=', date('Y-m-d')); 
                    break;
                case 'expired':
                    $this->db->where('notifications.n_expiry_date <', date('Y-m-d')); 
                    break;
            }
        }
        if($status != '')
        {
            $this->db->where('notifications.n_status', $status); 
        }
        if($date != '')
        {
            $this->db->where('notifications.n_expiry_date >=', $date); 
        }
        $this->db->where('notifications.n_account_id', config_item('id'));
        if($count)
        {
            $result = $this->db->count_all_results('notifications');            
        }
        else
        {
            $result = $this->db->get('notifications')->result_array();
        }
        return $result;
    }
    
    function notification($param=array())
    {
        if( isset($param['select'])) 
        {
            $this->db->select($param['select']);
        }
        if( isset($param['status'])) 
	    {
            $this->db->where('notifications.n_status', 1);
	    }
        if( isset($param['name'])) 
	    {
            if( isset($param['id'])) 
            {
                $this->db->where('notifications.id!=', $param['id']);
            }
            $this->db->where('n_title', $param['name']);
	    }
	    if( isset($param['id'])) 
	    {
            $this->db->where('notifications.id', $param['id']);
	    }
        
	    $return = $this->db->get('notifications')->row_array();	
        return $return;
    }
    
    function save($data)
    {
	    if($data['id'])
	    {
            $this->db->where('id', $data['id']);
            $this->db->update('notifications', $data);
            return $data['id'];
        }
	    else
	    {
            $this->db->insert('notifications', $data);
            return $this->db->insert_id();
	    }
    }

    function save_bulk( $notification_params = array() )
    {
        if(!empty($notification_params))
        {
            $this->db->trans_start();
            foreach($notification_params as $notification_param)
            {
                $this->db->where('id', $notification_param['id']);
                $this->db->update('notifications', $notification_param);
            }
            $this->db->trans_complete();
            return true;
        }
        return false;
    }

    function delete_notification( $notification_id )
    {
        $this->db->where('id', $notification_id);
        $this->db->delete('notifications');
        return true;
    }

    function delete_notification_bulk( $notification_ids = array() )
    {
        if(!empty($notification_ids))
        {
            $this->db->trans_start();
            foreach($notification_ids as $notification_id)
            {
                $this->db->where('id',$notification_id);
                $this->db->delete('notifications');
            }
            $this->db->trans_complete();
            return true;
        }
        return false;
    }

    function record_count()
    {
        $this->db->where(array('n_status' =>1));
        $result = $this->db->count_all_results("notifications");
        return $result;
    }    
}
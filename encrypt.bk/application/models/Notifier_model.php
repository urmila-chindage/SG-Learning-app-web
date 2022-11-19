<?php 
Class Notifier_model extends CI_Model
{	
    function __construct()
    {
        parent::__construct();
    }
    
    function push($param = array())
    {
        $insert     = isset($param['id'])?false:true;
        if($insert)
        {
            $param['nl_account_id'] = config_item('id');
            $return     = $this->db->insert('notification_log',$param);
        }
        else
        {
            $this->db->where('id',$param['id']);
            $this->db->where('nl_account_id', config_item('id'));
            $this->db->update('notification_log',$param);
            $return     = $param['id'];
        }

        return $return;
    }

    function push_update($update = array())
    {
        if(!empty($update))
        {
            $this->db->update_batch('notification_log',$update, 'id');
        }

        return true;
    }

    function push_bulk($param = array())
    {
        if(!empty($param))
        {
            for($i = 0; $i < count($param); $i++)
            {
                $param[$i]['nl_account_id'] = config_item('id');
            }
            $this->db->insert_batch('notification_log', $param);
        }
        return true;
    }

    function check_notification($param = array())
    {
        $action_code    = isset($param['nl_action_code'])?$param['nl_action_code']:array();
        $notify_to      = isset($param['nl_notify_to'])?$param['nl_notify_to']:array();
        $target = isset($param['nl_object'])?$param['nl_object']:0;
        $this->db->select('*');
        $this->db->where_in('nl_action_code',$action_code);
        $this->db->where_in('nl_notify_to',$notify_to);
        $this->db->where('nl_object',$target);
        $this->db->where('nl_account_id', config_item('id'));
        $notifications  = $this->db->get('notification_log')->result_array();

        return $notifications;
    }

    function remove($param = array())
    {
        $action_codes   = isset($param['action_codes'])?$param['action_codes']:array('null');
        $notify_to      = isset($param['notify_to'])?$param['notify_to']:0;

        $this->db->where_in('nl_action_code',$action_codes);
        $this->db->where('nl_notify_to',$notify_to);
        $this->db->where('nl_account_id', config_item('id'));
        $this->db->delete('notification_log');
        return true;
    }

    function fetch($param = array())
    {
        $user_id        = isset($param['user_id'])?$param['user_id']:0;

        $this->db->where('nl_notify_to',$user_id);
        $this->db->where('nl_triggered_by != ','');
        $this->db->where('nl_account_id', config_item('id'));
        $this->db->order_by('nl_date','DESC');
        //echo $this->db->last_query();die;
        // $this->db->order_by('id', 'DESC');
        $notifications  = $this->db->get('notification_log')->result_array();

        return $notifications;
    }

    function user_notifications($param = array())
    {
        $user_id        = isset($param['user_id'])?$param['user_id']:0;
        $select         = isset($param['select'])?$param['select']:'*';
        
        $this->db->select($select);
        $this->db->where('um_user_id',$user_id);
        
        $notifications  = $this->db->get('user_messages')->row_array();

        return $notifications;
    }

    function notify_user($param = array())
    {
        $param['id']    = isset($param['id'])?$param['id']:0;
        $this->db->where('id',$param['id']);
        $this->db->update('user_messages',$param);
        return true;
    }

    function notification_counter($param = array())
    {
        $action         = isset($param['action'])?$param['action']:'neutral';
        $action_targets = isset($param['targets'])?$param['targets']:array(0);

        $this->db->where_in('um_user_id', $action_targets);
        switch($action)
        {
            case 'inc':
                $this->db->set('um_message_count', 'um_message_count+1', FALSE);
            break;
            case 'dcr':
                $this->db->set('um_message_count', 'um_message_count-1', FALSE);
            break;
        }
        $this->db->update('user_messages');

        return true;
    }

    function update_notification_count($param = array())
    {
        if(!empty($param))
        {
            $this->db->trans_start();
            foreach($param as $update)
            {
                $update['count'] = isset($update['count'])?$update['count']:0;
                switch($update['action'])
                {
                    case 'inc':
                        $this->db->query("UPDATE user_messages SET um_message_count = um_message_count+".$update['count']." WHERE um_user_id = '".$update['user_id']."'");
                    break;
                    case 'dnc':
                        $this->db->query("UPDATE user_messages SET um_message_count = um_message_count-".$update['count']." WHERE um_user_id = '".$update['user_id']."'");
                    break;
                }
            }
            $this->db->trans_complete(); 
        }
    }

    function clear_notification( $param = array() )
    {
        $user_id    = isset($param['um_user_id'])?$param['um_user_id']:0;
        $this->db->where('um_user_id', $user_id);
        $this->db->update('user_messages', $param);
        return true;
    }
}
<?php
class Notifier
{
    private $CI;
    private $stack_size;
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->stack_size = 100;
    }

    public function push($param = array())
    {
        // echo "<pre>";print_r($param);exit;
        $response = array();
        $response['success'] = false;
        $response['message'] = 'Unknown error occured.';

        $individual = isset($param['individual'])?$param['individual']:false;
        $state_change = isset($param['state_change']) ? $param['state_change'] : false; //rollback,state_change
        $action_code = isset($param['action_code']) ? $param['action_code'] : false;
        $push_to = isset($param['push_to']) ? $param['push_to'] : array();
        $message = isset($param['message']) ? $param['message'] : false;
        $notification_target = isset($param['target']) ? $param['target'] : 0;
        $counter = array();

        if (!$action_code) 
        {
            $response['success'] = false;
            $response['message'] = 'Action code is mandatory.';
            return $response;
        }
        if (empty($push_to)) 
        {
            $response['success'] = false;
            $response['message'] = 'Notification destination is mandatory.';
            return $response;
        }

        $parameters = array();

        $this->CI->load->config('notification_actions');
        $this->CI->load->model('Notifier_model');

        $notification_template = array();
        $notification_template = $this->CI->config->item('notify_actions');
        $curr_notification_template = $notification_template[$action_code];

        $notify_to = $push_to;

        if(!$individual)
        {
            $notify_to = array();
            foreach($push_to as $p_key => $p_to)
            {
                $notify_to[] = $p_key;
            }
            $notify_to = array_unique($notify_to);
        }

        $mixed_conditions = array();
        $mixed_conditions['nl_action_code'] = array_unique(array_merge($curr_notification_template['opposite_action'], array($action_code)), SORT_REGULAR);
        $mixed_conditions['nl_notify_to'] = $notify_to;
        $mixed_conditions['nl_object'] = $notification_target;
        $mixed_notifications = $this->CI->Notifier_model->check_notification($mixed_conditions);
        
        $previous_notifications = array();
        
        $current_notifications_update = array();
        $current_notifications_insert = array();
        
        $to_remove_ids = array();
        $prev_notification_messages = array();
        $current_update_ids = array();

        foreach($curr_notification_template['opposite_action'] as $prev_msg)
        {
            $prev_notification_messages[$prev_msg] = $notification_template[$prev_msg];
        }

        foreach ($mixed_notifications as $mixed_notification) 
        {
            $alter_notification = $mixed_notification;
            $alter_notification['nl_triggered_by'] = explode(',',$alter_notification['nl_triggered_by']);
            $alter_notification['nl_date'] = date('Y-m-d H:i:s');
            $parameters = json_decode($alter_notification['nl_assets'],true);

            if ($alter_notification['nl_action_code'] == $action_code) 
            {
                $current_update_ids[] = $alter_notification['nl_notify_to'];
                if($individual)
                {
                    $alter_notification['nl_triggered_by'] = array($alter_notification['nl_notify_to']);
                    $parameters['number'] = count($alter_notification['nl_triggered_by']);
                    $alter_notification['nl_message'] = $this->process_notification_template($parameters, $curr_notification_template['specific']['message']);
                }
                else
                {
                    $alter_notification['nl_triggered_by'] = array_unique(array_merge($alter_notification['nl_triggered_by'], $push_to[$alter_notification['nl_notify_to']]), SORT_REGULAR);
                    $parameters['number'] = count($alter_notification['nl_triggered_by']);
                    if($parameters['number'] == 1 && count($push_to[$alter_notification['nl_notify_to']]) == 1 && ($alter_notification['nl_triggered_by'][0] == $push_to[$alter_notification['nl_notify_to']][0]))
                    {
                        $alter_notification['nl_message'] = $this->process_notification_template($parameters, $curr_notification_template['common']['single']['message']);
                    }
                    else
                    {
                        $alter_notification['nl_message'] = $this->process_notification_template($parameters, $curr_notification_template['common']['multiple']['message']);
                    }
                }
                $alter_notification['nl_triggered_by'] = implode(',',$alter_notification['nl_triggered_by']);
                $alter_notification['nl_assets'] = json_encode((object) $parameters);
                $current_notifications_update[] = $alter_notification;
            } 
            else 
            {
                if($individual)
                {
                    $alter_notification['nl_triggered_by'] = array();
                    $notification_counter = array();
                    $notification_counter['user_id'] = $alter_notification['nl_notify_to'];
                    $notification_counter['action'] = 'dnc';
                    $counter[] = $notification_counter;
                }
                else
                {
                    $alter_notification['nl_triggered_by'] = array_diff($alter_notification['nl_triggered_by'],$push_to[$alter_notification['nl_notify_to']]);
                }
                $parameters['number'] = count($alter_notification['nl_triggered_by']);
                if($parameters['number'] == 0 && $alter_notification['nl_invertable'] == 0)
                {
                    $to_remove_ids[] = $alter_notification['id'];
                }
                else
                {
                    $alter_notification['nl_message'] = $this->process_notification_template($parameters, $prev_notification_messages[$alter_notification['nl_action_code']]['common']['multiple']['message']);
                }

                if($parameters['number'] == 0)
                {
                    $notification_counter = array();
                    $notification_counter['user_id'] = $alter_notification['nl_notify_to'];
                    $notification_counter['count'] = 1;
                    $notification_counter['action'] = 'dnc';
                    $counter[] = $notification_counter;
                }

                $alter_notification['nl_triggered_by'] = implode(',',$alter_notification['nl_triggered_by']);
                $alter_notification['nl_assets'] = json_encode((object) $parameters);
                $previous_notifications[] = $alter_notification;
            }
        }

        $parameters = isset($param['assets']) ? $param['assets'] : array();

        $current_insert_ids = array_diff($notify_to,$current_update_ids);

        foreach($current_insert_ids as $c_id)
        {
            $c_insert = array();
            $c_insert['nl_notify_to'] = $c_id;
            $c_insert['nl_date'] = date('Y-m-d H:i:s');
            $c_insert['nl_action_code'] = $action_code;
            $c_insert['nl_invertable'] = (isset($curr_notification_template['invertible'])&&$curr_notification_template['invertible'])?'1':'0';
            $c_insert['nl_message'] = $message;
            if($individual)
            {
                $c_insert['nl_triggered_by'] = array($c_id);
                $parameters['number'] = count($c_id);
                if(!$c_insert['nl_message'])
                {
                    $c_insert['nl_message'] = $this->process_notification_template($parameters, $curr_notification_template['specific']['message']);
                }
                else
                {
                    $c_insert['nl_message'] = $this->process_notification_template($parameters, $c_insert['nl_message']);
                }
            }
            else
            {
                $c_insert['nl_triggered_by'] = $push_to[$c_id];
                $parameters['number'] = count($push_to[$c_id]);
                
                if(!$c_insert['nl_message'])
                {
                    if($parameters['number']>1)
                    {
                        $c_insert['nl_message'] = $this->process_notification_template($parameters, $curr_notification_template['common']['multiple']['message']);
                    }
                    else
                    {
                        $c_insert['nl_message'] = $this->process_notification_template($parameters, $curr_notification_template['common']['single']['message']);
                    }
                }
            }
            $c_insert['nl_assets'] = json_encode((object) $parameters);
            $c_insert['nl_object'] = $notification_target;
            $c_insert['nl_triggered_by'] = implode(',',$c_insert['nl_triggered_by']);

            $current_notifications_insert[] = $c_insert;

            $notification_counter = array();
            $notification_counter['user_id'] = $c_id;
            $notification_counter['count'] = 1;
            $notification_counter['action'] = 'inc';
            $counter[] = $notification_counter;
        }

        $all_updates = $previous_notifications + $current_notifications_update;
        $all_inserts = $current_notifications_insert;

        $this->CI->Notifier_model->update_notification_count($counter);
        $this->CI->Notifier_model->push_update($all_updates);
        $this->CI->Notifier_model->push_bulk($all_inserts);

        $response['success'] = true;
        $response['message'] = 'Notification successfully pushed.';
        return $response;
    }

    public function pull($param = array())
    {
        $response = array();
        $response['success'] = false;
        $response['message'] = 'Unknown error occured.';

        $action_codes = isset($param['action_codes']) ? $param['action_codes'] : array();
        $notify_to = isset($param['notify_to']) ? $param['notify_to'] : false;

        if (!$notify_to) {
            $response['success'] = false;
            $response['message'] = 'Notification destination cannot be empty.';
            return $response;
        }

        if (is_array($action_codes) && empty($action_codes)) {
            $response['success'] = false;
            $response['message'] = 'Action codes cannot be empty.';
            return $response;
        }
        $delete = array();
        $delete['action_codes'] = $action_codes;
        $delete['notify_to'] = $notify_to;
        $this->CI->Notifier_model->remove($delete);

        $response['success'] = true;
        $response['message'] = 'Delete notification groups success.';
        return $response;
    }

    public function fetch($param = array())
    {
        $response = array();
        $response['success'] = false;
        $response['message'] = 'Unknown error occured.';

        $user_id = isset($param['user_id']) ? $param['user_id'] : false;

        if (!$user_id) {
            $response['success'] = false;
            $response['message'] = 'Notification destination cannot be empty.';
            return $response;
        }

        $this->CI->load->model('Notifier_model');
        $this->CI->load->config('notification_actions');

        $notification_message = array();
        $notification_message = $this->CI->config->item('notify_actions');

        $notification_groups = $this->CI->Notifier_model->fetch(array('user_id' => $user_id));
        // echo "<pre>";print_r($notification_groups);exit;
        $new_notifications = array();

        $user_notifications = $this->CI->Notifier_model->user_notifications(array('user_id' => $user_id));
        // echo "<pre>";print_r($user_notifications);exit;
        $all_user_notifications_old = isset($user_notifications['um_messages']) ? $user_notifications['um_messages'] : '{}';
        
        $all_user_notifications_old = json_decode($all_user_notifications_old, true);
         
        $all_user_notifications     = array();
        $all_seen_notification      = array();
        $all_unseen_notification    = array();
        $rm_notifications           = array();

        foreach ($notification_groups as $notification) {
            
            $triggered_by       = explode(',', $notification['nl_triggered_by']);
            $assets             = json_decode($notification['nl_assets'], true);
            
            $message            = $notification_message[$notification['nl_action_code']];
            
            $rm_notifications[] = $notification['nl_action_code'];
            
            $notify             = array();
            $notify['seen']     = 0;
            $notify['message']  = $notification['nl_message'];
            $notify['time']     = date('d-m-Y',strtotime($notification['nl_date'])).' at '.date('g:i a',strtotime($notification['nl_date']));
            
            if (count($triggered_by) == 1 && isset($triggered_by[0]) && $triggered_by[0] == $notification['nl_notify_to']) {
                $notify['link'] = $this->process_notification_template($assets, $message['specific']['link']);
                
            } else {
                if(isset($assets['number'])&&$assets['number']==1)
                {
                    $notify['link'] = $this->process_notification_template($assets, $message['common']['single']['link']);
                }
                else
                {
                    $notify['link'] = $this->process_notification_template($assets, $message['common']['multiple']['link']);
                }
            }
            $notify['type']         = $notification['nl_action_code'];
            $notify['id']           = $notification['nl_object'];
            $notification_index     = $notification['nl_action_code'].'_'.$notification['nl_object'];//$notification['id'];
            $all_user_notifications[$notification_index] = $notify;
            
        }
        $seen_buffer    = 50;
        foreach( $all_user_notifications_old as $notification_index => $notification)
        {
            
            if(!isset($all_user_notifications[$notification_index]))
            {
                if( $notification['seen'] == '1' )
                {
                    
                    if( $seen_buffer > 0 )
                    {
                        $all_user_notifications[$notification_index] = $notification;
                    }
                    $seen_buffer--;
                }
                else
                {
                    $all_user_notifications[$notification_index] = $notification;
                   
                }
            }
            
        }
        foreach ($all_user_notifications as $key => $raw_notifications) {

            $raw_time               = explode('at',$raw_notifications['time']);
            $new_timstamp['time']   = $raw_time[0]." ".$raw_time[1];
            $sort[$key]             = strtotime($new_timstamp['time']);
        }
        array_multisort($sort, SORT_DESC, $all_user_notifications);
      
        // $total_notifications = count($all_user_notifications) + count($notification_groups);
        // $unseen_notifications = array();

        // if ($this->stack_size < $total_notifications) {
        //     $unseen_notifications = array_filter($all_user_notifications, function ($user_notification) {
        //         return $user_notification['seen'] == 0;
        //     });
        // } else {
        //     $unseen_notifications = $all_user_notifications;
        // }

        if (!empty($rm_notifications)) {
           
            $this->CI->Notifier_model->remove(array('action_codes' => $rm_notifications, 'notify_to' => $user_id));
        }

        $all_notificatons                       = $all_user_notifications;//$unseen_notifications + $new_notifications;
        
        $user_notifications['um_messages']      = json_encode((object) $all_notificatons);
        $user_notifications['um_message_count'] = 0;
       
        $this->CI->Notifier_model->notify_user($user_notifications);

        $response['success']        = true;
        $response['message']        = 'Notification fetch success.';
        $response['notifications']  = $all_notificatons;

        return $response;
    }
    public function sort_all_notifications(){


    }
    public function get_notifiction_count($param = array())
    {
        $user_id = isset($param['user_id'])?$param['user_id']:false;
        $count = 0;
        if($user_id)
        {
            $this->CI->load->model('Notifier_model');
            $count = $this->CI->Notifier_model->user_notifications(array(
                'user_id' => $user_id,
                'select' => 'id,um_user_id,um_message_count'
            ));
            $count = isset($count['um_message_count'])?$count['um_message_count']:0;
        }

        return $count;
    }

    function read($param = array())
    {
        $this->CI->load->model('Notifier_model');
        $response = array();

        $user_id = isset($param['user_id'])?$param['user_id']:0;
        $user_notifications = $this->CI->Notifier_model->user_notifications(array('user_id' => $user_id));
        
        $user_notifications = isset($user_notifications['um_messages'])?$user_notifications['um_messages']:'{}';
        $response['success'] = true;
        $response['message'] = 'Notification fetch success.';
        $response['notifications'] = json_decode($user_notifications,true);
        return $response;
    }

    function mark_as_read($param = array())
    {
        $this->CI->load->model('Notifier_model');
        $response = array();

        $notification_id = isset($param['notification_id'])?$param['notification_id']:0;
        $user_id = isset($param['user_id'])?$param['user_id']:0;
        
        $user_notifications = $this->CI->Notifier_model->user_notifications(array('user_id' => $user_id));
        
        $notifications = isset($user_notifications['um_messages'])?$user_notifications['um_messages']:'{}';
        $notifications = json_decode($notifications,true);
        
        if(!empty($notifications) && isset($notifications[$notification_id]))
        {
            $notifications[$notification_id]['seen'] = 1;
            $user_notifications['um_messages'] = json_encode($notifications);
            $this->CI->Notifier_model->notify_user($user_notifications);
        }

        $response['success'] = true;
        $response['message'] = 'Notification marking success.';

        return $response;
    }

    private function process_notification_template($contents = array(), $phrase = '')
    {
        $search = array();
        $replace = array();
        if (!empty($contents)) {
            foreach ($contents as $key => $value) {
                $search[] = '{' . $key . '}';
                $replace[] = $value;
            }
        }
        return str_replace($search, $replace, $phrase);
    }

}

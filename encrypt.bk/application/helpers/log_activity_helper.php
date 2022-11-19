<?php 
if ( ! function_exists('log_activity'))
{
    function log_activity($triggered_activity = '', $content = array(), $message_content = array())
    {    
        $CI = & get_instance();
        $process_content                    = activity_object($triggered_activity);
        $existingCheckParams                = array();
        $existingCheckParams['user_id']     = $content['user_id'];
        $existingCheckParams['user_email']  = $content['useremail'];
        $existingCheckParams['log_action']  = $triggered_activity;
        $existing_log                       = activity_already_exists($existingCheckParams);
        $delete_key                         = 'score_'.$content['user_id'];
        $CI = & get_instance();
        $CI->memcache->delete($delete_key);
        if(!empty($existing_log) && $existing_log['la_created_date'] >= date('Y-m-d H:i:s', strtotime('-3 seconds')))
        {
            return false;
        }

        if(!empty($process_content))
        {
            $CI = & get_instance();
            $CI->load->database();
            $message_content['site_name']    = config_item('site_name');

            if($process_content['la_points']!=0)
            {
                $process_content['la_message'] .= '.'.$process_content['la_points'].' points added.';
            }
            //record log on table log_activity
            $log                        = array();
            $log['la_user_id']          = $content['user_id'];
            $log['la_phone_number']     = isset($content['phone_number'])?$content['phone_number']:'';
            $log['la_user_name']        = isset($content['username'])?$content['username']:'';
            $log['la_user_email']       = isset($content['useremail'])?$content['useremail']:'';
            $log['la_usertype']         = $content['user_type'];
            $log['la_points']           = $process_content['la_points'];
            $log['la_action']           = $triggered_activity;
            $log['la_account_id']       = config_item('id');
            $log['la_message']          = replace_message_content($message_content,$process_content['la_message']);
            $log['la_ip_address']       = $_SERVER['REMOTE_ADDR'];
            $log['la_created_date']     = date("Y-m-d H:i:s");
            $CI->db->insert('log_activity', $log);

            //end
        }
    }    
}


if ( ! function_exists('activity_already_exists'))
{
    function activity_already_exists($params = array())
    {
        $CI = & get_instance();
        $CI->load->database();
        return $CI->db->select('la_user_id, la_action, la_created_date')->where(array('la_user_id' => $params['user_id'], 'la_action' => $params['log_action'], "DATE_FORMAT(`la_created_date`,'%Y-%m-%d')" => date('Y-m-d')))->order_by('id', 'DESC')->limit(1)->get('log_activity')->row_array();
    }
}

if ( ! function_exists('activity_object'))
{
    function activity_object($activity = false)
    {
        $return = array();
        if(!$activity)
        {
            return $return;
        }

        $CI = & get_instance();

        $objects        = array();
        $objects['key'] = 'log_actions';
        $actions        =  $CI->memcache->get($objects);
        
        if(empty($actions))
        {
            $CI->load->database();
            $CI->db->from('log_actions');
            $result  = $CI->db->get();
            $actions = $result->result_array();
            $CI->memcache->set('log_actions', $actions);
        }
        $action_result = array();
        foreach($actions as $action)
        {
            if($action['la_controller'] == $activity)
            {
                $action_result = $action;
                break;
            }
        }
        
        return $action_result;
    }    
}
if ( ! function_exists('replace_message_content'))
{
    function replace_message_content($contents = array(), $phrase = '')
    {
       
        $search = array();
        $replace = array();
        if(!empty($contents))
        {
            foreach($contents as $key => $value)
            {
                $search[] = '{'.$key.'}';
                $replace[] = $value;
            }
        }
        return str_replace($search, $replace, $phrase);
    
    }
}


?>
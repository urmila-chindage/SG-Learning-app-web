<?php 
class Ofabeenotifier
{    
    private $CI;
    private $limit;
    function __construct()
    {
        $this->CI = & get_instance();
        $this->limit = 5;
        $this->memory = 7;
    }
    
    function push_notification($param=array())
    {
        $this->CI->load->model(array('Push_model'));
        $response               = array();
        $response['error']      = false;
        $response['message']    = 'Notification send successfully';
        
        $ids        = isset($param['ids'])?$param['ids']:array();
        $message    = isset($param['message'])?$param['message']:false;
        $link       = isset($param['link'])?$param['link']:'';
        
        if(empty($ids))
        {
            $response['error'] = true;
            $response['message'] = 'Please pass atleast one user id';
            return $response;exit;
        }
        if(!$message)
        {
            $response['error'] = true;
            $response['message'] = 'Message cannot be empty';
            return $response;exit;
        }
        
        foreach($ids as $id)
        {
            $user            = $this->CI->Push_model->user_messages(array('id' => $id));
            $user_message    = array();
            $user_message    = ($user['um_messages'])?(array)json_decode($user['um_messages'], true):array();
            //echo '<pre>'; print_r($user_message);die;

            $objects            = array();
            $objects['link']    = $link;
            $objects['message'] = $message;
            $objects['time']    = date('Y-m-d H:i:s');
            if(!isset($user_message['unseen']))
            {
                $user_message['seen']   = array();
                $user_message['unseen'] = array();
            }
            else 
            {
                //checking the array size and remove the old messages seen by user
                $message_size = sizeof($user_message['seen']);
                if($message_size >= $this->memory)
                {
                    $memory = $message_size-$this->limit;
                    foreach ($user_message['seen'] as $key => $seen)
                    {
                        if($memory == 0)
                        {
                            break;
                        }
                        unset($user_message['seen'][$key]);
                        $memory--;
                    }
                }
            }
            $user_message['unseen'][date('ymdHis'.rand(1001, 9999))] = $objects;
            
            $save                = array();
            $save['um_user_id']  = $id;
            $save['um_messages'] = json_encode($user_message);
            //echo '<pre>'; print_r($save);die;
            $this->CI->Push_model->save_user_messages($save);
        }
        return $param;
    }
    
    function user_notification($param=array())
    {
        $this->CI->load->model(array('Push_model'));
        $user_id  = isset($param['user_id'])?$param['user_id']:false;
        $response = array();
        if(!$user_id)
        {
            return $response;exit;
        }
        
        $user         = $this->CI->Push_model->user_messages(array('id' => $user_id));
        $user_message = (array)json_decode($user['um_messages'], true);
        if(isset($user_message['unseen']))
        {
            krsort($user_message['unseen']);
        }
        if(isset($user_message['seen']))
        {
            krsort($user_message['seen']);
        }
        return $user_message;
    }
    
    function mark_as_read($param=array())
    {
        $this->CI->load->model(array('Push_model'));
        $message_id = isset($param['message_id'])?$param['message_id']:false;//can bar an array or string
        $user_id    = isset($param['user_id'])?$param['user_id']:false;
        if($user_id)
        {
            $save                   = array();
            $save['um_user_id']     = $user_id;
            //processing the message masrk as read
            $user            = $this->CI->Push_model->user_messages(array('id' => $user_id));
            $user_message    = ($user['um_messages'])?(array)json_decode($user['um_messages'], true):array();
            if($message_id)
            {
                //requested message id mark as read
                //in case of array
                if(is_array($message_id))
                {
                    foreach ($message_id as $msg_id)
                    {
                        if(isset($user_message['unseen'][$msg_id]))
                        {
                            $user_message['seen'][$msg_id] = $user_message['unseen'][$msg_id];
                            unset($user_message['unseen'][$msg_id]);
                        }
                    }
                }
                else
                {
                    //incase of single message id
                    if(isset($user_message['unseen'][$message_id]))
                    {
                        $user_message['seen'][$message_id] = $user_message['unseen'][$message_id];
                        unset($user_message['unseen'][$message_id]);
                    }                    
                }
            }
            else 
            {
                //all message under requested user mark as read
                if(isset($user_message['unseen']) && !empty($user_message['unseen']))
                {
                    foreach ($user_message['unseen'] as $key => $seen)
                    {
                        $user_message['seen'][$key] = $seen;
                        unset($user_message['unseen'][$key]);
                    }
                }
            }
            //end
            $save['um_messages'] = json_encode($user_message);
            $this->CI->Push_model->save_user_messages($save);
        }
    }

    function delete_notification($param=array())
    {
        $this->CI->load->model(array('Push_model'));
        $message_id = isset($param['message_id'])?$param['message_id']:false;//can bar an array or string
        $user_id    = isset($param['user_id'])?$param['user_id']:false;
        if($user_id)
        {
            $save                   = array();
            $save['um_user_id']     = $user_id;
            //processing the message masrk as read
            $user            = $this->CI->Push_model->user_messages(array('id' => $user_id));
            $user_message    = ($user['um_messages'])?(array)json_decode($user['um_messages'], true):array();
            if($message_id)
            {
                //requested message id mark as read
                //in case of array
                if(is_array($message_id))
                {
                    foreach ($message_id as $msg_id)
                    {
                        if(isset($user_message['unseen'][$msg_id]))
                        {
                            unset($user_message['unseen'][$msg_id]);
                        }
                    }
                }
                else
                {
                    //incase of single message id
                    if(isset($user_message['unseen'][$message_id]))
                    {
                        unset($user_message['unseen'][$message_id]);
                    }                    
                }
            }
            else 
            {
                //all message under requested user mark as read
                if(isset($user_message['unseen']) && !empty($user_message['unseen']))
                {
                    foreach ($user_message['unseen'] as $key => $seen)
                    {
                        unset($user_message['unseen'][$key]);
                    }
                }
            }
            //end
            $save['um_messages'] = json_encode($user_message);
            $this->CI->Push_model->save_user_messages($save);
        }
    }
    
    function subscription_expiry($data=array())
    {
        $this->CI->load->database();
        $expiry_date = isset($data['expiry_date'])?$data['expiry_date']:array();
        $return      = array();
        if(!empty($expiry_date))
        {
            $this->CI->db->select('accounts.acct_domain, cs_course_id, cs_user_id, users.us_name, cs_end_date, course_basics.cb_title, course_basics.cb_slug, course_tutors_cp.course_tutors');
            $this->CI->db->join('users', 'course_subscription.cs_user_id = users.id', 'left');
            $this->CI->db->join('accounts', 'users.us_account_id = accounts.id', 'left');
            $this->CI->db->join('course_basics', 'course_subscription.cs_course_id = course_basics.id', 'left');
            $this->CI->db->join('(SELECT ct_course_id, GROUP_CONCAT(ct_tutor_id) as course_tutors FROM course_tutors course_tutors_cp GROUP BY ct_course_id) course_tutors_cp', 'course_subscription.cs_course_id = course_tutors_cp.ct_course_id', 'left');
            $this->CI->db->where_in('cs_end_date', $expiry_date);
            $return = $this->CI->db->get('course_subscription')->result_array();
            //echo $this->CI->db->last_query();die;
        }
        //echo '<pre>'; print_r($expiry_date);die;
        return $return;
    }
    
}
?>
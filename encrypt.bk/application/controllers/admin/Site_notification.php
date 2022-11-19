<?php
class Site_notification extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $skip_login                 = array('push_notification', 'mark_as_read_user');
        $this->user_id = 0;
        $redirect	= $this->auth->is_logged_in(false, false);
        if (!$redirect)
        {
            $redirect   = true;
            $teacher    = $this->auth->is_logged_in(false, false, 'teacher');
            if($teacher)
            {
                $redirect       = false;
                $teacher        = $this->auth->get_current_user_session('teacher');
                $this->user_id  = $teacher['id'];                
            }
            $content_editor    = $this->auth->is_logged_in(false, false, 'content_editor');
            if($content_editor)
            {
                $redirect       = false;
                $content_editor = $this->auth->get_current_user_session('content_editor');
                $this->user_id  = $content_editor['id'];                
            }
            if($redirect && !in_array($this->router->fetch_method(), $skip_login))
            {
                echo json_encode(array('error' => true, 'message' => 'You do not have permission to access this module.'));exit;
            }
        }
        else
        {
            $admin = $this->auth->get_current_user_session('admin');
            $this->user_id = $admin['id'];                
        }
    }
    
    function mark_as_read()
    {
        $param               =  array();
        $param['user_id']    = $this->user_id;
        $param['message_id'] = $this->input->post('message_id');
        if($param['message_id'])
        {
            // $this->ofabeenotifier->mark_as_read($param);
            echo json_encode(array('error' => false, 'message' => 'Message status updated.'));exit;        
        }
        else
        {
            echo json_encode(array('error' => true, 'message' => 'Message id missing.'));exit;                    
        }
    }
    
    function mark_as_read_user()
    {
        $param               =  array();
        $user                = $this->auth->get_current_user_session('user');
        $this->user_id       = $user['id'];                
        $param['user_id']    = $this->user_id;
        $param['message_id'] = $this->input->post('message_id');
        if($param['message_id'])
        {
            // $this->ofabeenotifier->mark_as_read($param);
            echo json_encode(array('error' => false, 'message' => 'Message status updated.'));exit;        
        }
        else
        {
            echo json_encode(array('error' => true, 'message' => 'Message id missing.'));exit;                    
        }        
    }
    
    function notify_challenge_zone()
    {
        // $this->load->library('ofabeenotifier');
        $this->load->model('Push_model');
        $challenges = $this->Push_model->get_upcomming_challenge();
        
        if(!empty($challenges))
        {
            foreach ($challenges as $challenge)
            {
                $subscribed_users = $this->Push_model->category_subsciption_users(array('category_id'=>$challenge['cz_category']));
                if(!empty($subscribed_users))
                {
                    $param              = array();
                    $param['ids']       = array();
                    $mailer             = array();
                    $mailer['to']       = array();
                    foreach($subscribed_users as $subscribed_user)
                    {
                        $param['ids'][] = $subscribed_user['cs_user_id'];
                        if($subscribed_user['us_email'])
                        {
                            $mailer['to'][]  = $subscribed_user['us_email'];                    
                        }
                    }
                    $notify_challenge_name  = (strlen($challenge['cz_title'])>50)?substr($challenge['cz_title'], 0, 47).'...':$challenge['cz_title'];
                    $challenge_zone_time    = date("F j, Y", strtotime($challenge['cz_start_date'])).' '.date("g:i a", strtotime($challenge['cz_start_date']));
                    
                    $mailer['from']         = $this->config->item('site_email');
                    $mailer['subject']      = "A new challenge zone ".$challenge['cz_title']." has been added";
                    $mailer['body']         = "Hi,<br/> Greetings from ".$this->config->item('site_name').". A new challenge zone <b>'.$notify_challenge_name.'</b> has been added on <b>'.$challenge_zone_time.'</b>. Please click <a href='".$this->site_url($challenge['acct_domain'].'/'.$this->config->item('index_page').'/material/challenge/'.$challenge['id'])."'>here</a> to attend this challenge.";
                    $this->ofabeemailer->send_mail($mailer);            
                }
            }
        }
        
        /*//send notification to admin
        //End*/

    }
    
    function site_url($uri='')
    {
        $protocol = (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off')?'https://':'http://';
        return $protocol.$uri;
    }
    
    function push_notification()
    {
        // $this->load->library('ofabeenotifier');
        $this->notify_challenge_zone();
        $durations = array();
        $durations[date('Y-m-d', strtotime('-1 day'))] = array(
                                                                'student' => array('message' => 'Student: The course <b>%s</b> has expired.', 'link' => '%s') 
                                                                ,'admin' => 'Admin: The course <b>%s</b> for the student <b>%s</b> has expired.'
                                                              );
        $durations[date('Y-m-d', strtotime('+1 day'))] = array(
                                                                'student' => array('message' => 'Student: The course <b>%s</b> will expire tommorow.') 
                                                                ,'admin' => 'Admin: The course <b>%s</b> for the student <b>%s</b> will expire tommorow.'
                                                              );
        $durations[date('Y-m-d', strtotime('+1 week'))] = array(
                                                                'student' => array('message' => 'Student: The course <b>%s</b> will expire in one week.') 
                                                                ,'admin' => 'Admin: The course <b>%s</b> for the student <b>%s</b> will expire in one week.'
                                                              );
        $durations[date('Y-m-d', strtotime('+2 weeks'))] = array(
                                                                'student' => array('message' => 'Student: The course <b>%s</b> will expire in two weeks.') 
                                                                ,'admin' => 'Admin: The course <b>%s</b> for the student <b>%s</b> will expire in two weeks.'
                                                              );
        //echo '<pre>'; print_r($subscriptions);
        $expiry_dates = array();
        foreach($durations as $expiry_date => $message)
        {
            $expiry_dates[] = $expiry_date;
        }
        // $subscriptions = $this->ofabeenotifier->subscription_expiry(array('expiry_date' => $expiry_dates));
        
    }
}
?>
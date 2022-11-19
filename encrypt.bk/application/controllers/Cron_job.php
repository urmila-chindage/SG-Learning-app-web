<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Cron_job extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if(!in_array($this->router->fetch_method(), array('send_student_image_to_s3_launch')))
        {
            $this->load->model(array('User_model','Course_model','Report_model','Group_model','Events_model', 'Bundle_model'));            
        }
    }
    public function notify_new_student() 
    {
        // if ($_SERVER['HTTP_REQUEST_TOKEN'] == sha1(config_item('acct_domain') . config_item('id')))
        {
            $user_count = $this->User_model->unapproved_user();
            if ($user_count > 0) {
                $module_param['module_name'] = 'user';
                $module_param['select']      = 'id';
                $modules                     = $this->User_model->modules($module_param);
                $param['module_id']          = $modules['id'];
                $param['permissions']        = '1,2';
                $user_roles_list             = $this->User_model->user_role_permission($param);
                $user_roles                  = array_column($user_roles_list, 'role_id');
                if (!empty($user_roles)) {
                    $verified_email_list = $this->User_model->user_emails($user_roles);
                    $verified_emails     = array_column($verified_email_list,'us_email');
                    
                    if (!empty($verified_emails)) {
                        $email_param               = array();
                        $email_param['email_code'] = 'notify_pending_students';
                        $email_param['email_ids']  = $verified_emails;
                        $email_param['contents']   = array(
                            'user_count' => $user_count
                            , 'site_url' => admin_url() . 'user?filter=not-approved'
                            , 'site_name' => config_item('site_name'),
                        );
                        $this->send_mail($email_param);
                    }
                } else {
                    $response            = array();
                    $response['message'] = 'No Admin that have privilege';
                    $response['success'] = false;
                    echo json_encode($response);
                }
            } else {
                $response            = array();
                $response['message'] = 'There are no new student awaiting approval';
                $response['success'] = false;
                echo json_encode($response);
            }
        }
    }
    public function bulk_process_mail()
    {
        $email_code = $this->input->post('email_code');
        $data       = json_decode($this->input->post('data'), true);
        foreach ($data as $user_param) {
            $new_email_param['email_code'] = $email_code;
            $new_email_param['email_ids']  = $user_param['email'];
            $new_email_param['contents']   = $user_param['contents'];
            $this->send_mail($new_email_param);
        }
    }
    public function bulk_process_mail_normal()
    {
        $email_ids = json_decode($this->input->post('email_ids'), true);
        $data      = json_decode($this->input->post('data'), true);
        if(sizeof($email_ids)>0)
        {
            $param                    = array();
            $param['subject']         = $data['subject'];
            $param['body']            = $data['body'];
            $param['to']              = $email_ids;
            $param['force_recipient'] = true;
            $send                     = $this->ofabeemailer->send_mail($param); 
        }
    }
    private function send_mail($message_params)
    {
        $email_code = isset($message_params['email_code']) ? $message_params['email_code'] : false;
        $email_ids  = empty($message_params['email_ids']) ? false : $message_params['email_ids'];
        $contents   = isset($message_params['contents']) ? $message_params['contents'] : '';
        if($email_code)
        {
            $template         = $this->ofabeemailer->template(array('email_code' => $email_code));
            $param['subject'] = $template['em_subject'];
            $param['body']    = $this->ofabeemailer->process_mail_content($contents, $template['em_message']);             
        }
        else
        {
            $subject          = isset($message_params['subject']) ? $message_params['subject'] : '';
            $param['subject'] = $subject;
            $param['body']    = $contents;
        }
        if ($email_ids != false) 
        {
            $param['to']      = $email_ids;
            $result           = $this->ofabeemailer->send_mail($param);
            if ($result == true) {
                $response            = array();
                $response['message'] = 'Email sent successfully';
                $response['success'] = true;
                // echo json_encode($response);
            } else {
                $response            = array();
                $response['message'] = 'Technical error';
                $response['success'] = false;
                // echo json_encode($response);
            }
        }
    }
    public function shoot_bulk_mail_with_unique_body()
    {
        $mail_params  = file_get_contents('php://input');
        $mail_params  = json_decode($mail_params, true);

        if(sizeof($mail_params)>0)
        {
            foreach($mail_params as $mail_param)
            {
                $this->ofabeemailer->send_mail($mail_param);
            }
        }
    }
    public function forum_ratings(){
        $data           = json_decode( file_get_contents( 'php://input' ), true );
        $session_token  = $data['session_token'];
        $contents       = $data['content'];
        if(!empty($contents)){
            foreach($contents as $key=>$forum_data){
                $filter_param                   = array();
                $filter_param['select']         = 'id'; 
                $filter_param['course_id']      = $forum_data['course_id'];
                foreach($forum_data['data'] as $institute_forum){
                    $filter_param['institute_id']   = $institute_forum['institute_id'];
                    $report_id                      = $this->Report_model->check_if_exist_institute($filter_param);
                    $save_param                         = array();
                    $save_param['cp_forum_likes']       = $institute_forum['forum_likes']; 
                    $save_param['cp_forum_dislikes']    = $institute_forum['forum_dislikes']; 
                    if(!empty($report_id)){
                        $param        = array();
                        $param['id']  = $report_id['id'];
                        $this->Report_model->save_course_performance($save_param,$param);
                    }else{
                        $save_param['cp_course_id']             = $forum_data['course_id'];
                        $save_param['cp_institute_id']          = $institute_forum['institute_id'];
                        $save_param['cp_course_institute_map']  = $forum_data['course_id'].$institute_forum['institute_id'];
                        $save_param['cp_account_id']            = config_item('id');
                        $this->Report_model->save_course_performance($save_param);
                    }
                }
            }

            $response               = array();
            $response['error']      = false;
            $response['message']    = 'data updated';
        }else{
            $response = array();
            $response['error'] = true;
            $response['message'] = 'please send content';
        }
        echo json_encode($response);
    }
    public function delete_log_activities(){
        $response                = array();
        $response['error']       = true;
        $delete                  = $this->Report_model->delete_log_activities();
        if($delete) 
        {
            $response['message'] = $delete. ' Log activities deleted.';
        } else {
            $response['message'] = 'No log activities to delete.';
        }
        echo json_encode($response);
    }
    public function course_discussion_log(){
        
        $input              = file_get_contents('php://input');
        $decoded_input      = json_decode($input, true);
        $course_id          = $decoded_input['data']['course_id'];
        $action             = $decoded_input['data']['action'];
        $objects            = array();
        $objects['key']     = 'course_' . $course_id;
        $callback           = 'course_details';
        $params             = array('id' => $course_id);
        $course             = $this->memcache->get($objects, $callback, $params);
        $user_data              = array();
        $user_data['user_id']   = $decoded_input['data']['user_id'];
        $user_data['username']  = $decoded_input['data']['username'];
        $user_data['useremail']  = '';
        $user_data['user_type'] = $decoded_input['data']['user_role_id'];
        $message_template                   = array();
        $message_template['username']       = $decoded_input['data']['username'];
        $message_template['course_name']    = $course['cb_title'];
        $message_template['question']       = "'".$decoded_input['data']['question']."'";
        $activity                           = '';
        switch($action){
            case '1':
                $message_template['action'] = 'added';
                $activity                   = 'forum_question';
            break;
            case '2':
                $message_template['action'] = 'edited';
                $activity                   = 'forum_question';
            break;  
            case '3':
                $message_template['action'] = 'deleted';
                $activity                   = 'forum_question';
            break;  
            case '4':
                $activity                   = 'forum_question_abuse';
            break; 
            case '5':
                $message_template['action'] = 'added';
                $activity                   = 'forum_question_post';
                $notification                   = array();
                $notification['user_id']        = $decoded_input['data']['user_id'];
                $notification['username']       = $decoded_input['data']['username'];
                $notification['course_name']    = $course['cb_title'];
                $notification['question']       = "'".$decoded_input['data']['question']."'";
                $notification['participants']   = $decoded_input['data']['participants'];
                $notification['question_id']    = $decoded_input['data']['question_id'];
                $notification['questioned_by']  = $decoded_input['data']['questioned_by'];
                $this->notify_post_answered($notification);
            break; 
            case '6':
                $message_template['action'] = 'edited';
                $activity                   = 'forum_question_post';
            break;
            case '7':
                $message_template['action'] = 'deleted';
                $activity                   = 'forum_question_post';
            break;
        }
        $triggered_activity                 = $activity;
        // echo "<pre>";print_r($message_template);exit;
        log_activity($triggered_activity, $user_data, $message_template); 
        $response               = array();
        $response['error']      = 'false';
        $response['message']    = 'log created';
        echo json_encode($response);exit;
    }
    
    function notify_post_answered($param)
    {
        $this->load->library('Notifier');
        $participants        = $param['participants'];
        $notify_to           = array_diff($participants, array($param['user_id']));
        echo "<pre>";print_r($notify_to );exit;
        if(($param['user_id'] != $param['questioned_by']))
        {
            $this->notifier->push(
                array(
                    'action_code' => 'post_answered',
                    'assets' => array('username' => $param['username'],'course_name' => $param['course_name'],'question_name' => $param['question']),
                    'target' => $param['question_id'],
                    'message' => 'Few new answers recieved under the question {question_name} you have posted in course {course_name}.',
                    'individual' => true,
                    'push_to' => array($param['questioned_by'])
                )
            );
        }
        if(!empty($notify_to))
        {
            $this->notifier->push(
                array(
                    'action_code' => 'post_answered',
                    'assets' => array('username' => $param['username'],'course_name' => $param['course_name'],'question_name' => $param['question']),
                    'message' => 'Few new answers recieved under the question {question_name} in course {course_name}.',
                    'target' => $param['question_id'],
                    'individual' => true,
                    'push_to' => $notify_to
                )
            );
        }
    }
    public function notify_event(){
        $input              = file_get_contents('php://input');
        $decoded_input      = json_decode($input, true);
        $request_type       = $decoded_input['request_type'];
        $event_id           = $decoded_input['event_id'];
        $event_name         = $decoded_input['event_name'];
          
        $filter_param                   = array();
        $filter_param['request_type']   = $request_type;
        $filter_param['request_id']     = $decoded_input['request_id'];
        $user_ids                       = $this->notification_users($filter_param);
        $this->load->library('Notifier');
        $this->notifier->push(
            array(
                'action_code'   => 'event_created',
                'assets'        => array('event_name' => $event_name,'event_id_hash'=>base64_encode($event_id)),
                'target'        => $event_id,
                'individual'    => true,
                'push_to'       => $user_ids
            )
        );
    }
    
    public function notify_event_deleted(){
        $input              = file_get_contents('php://input');
        $decoded_input      = json_decode($input, true);
        $event_id           = $decoded_input['event_id'];
        if(!empty($event_id)){
            $event_name         = $decoded_input['event_name'];
            $course_users               = array();
            $institute_users            = array();
            $batch_users                = array();
            $filter_param               = array();
            $filter_param['select']     = 'ev_name,ev_course_id,ev_institute_id,ev_batch_id';
            $filter_param['event_id']   = $event_id;
            $filter_param['count']      = '1';
            $filter_param['date']       = true;
            $event                      = $this->Events_model->get_events($filter_param);
            
            if(count($event['ev_course_id'])>0){
                $process_param['request_type']  = 'course';
                $course_ids                     = ($event['ev_course_id'])?explode(',', $event['ev_course_id']):array();
                $process_param['request_id']    = $course_ids;
                $course_users                   = $this->notification_users($process_param);
                
            }
            if(count($event['ev_institute_id'])>0){
                $process_param['request_type']  = 'institute';
                $institute_ids                  = ($event['ev_institute_id'])?explode(',', $event['ev_institute_id']):array();
                $process_param['request_id']    = $institute_ids;
                $institute_users                = $this->notification_users($process_param);
                
            }
            if(count($event['ev_batch_id'])>0){
                $process_param['request_type']  = 'batch';
                $batch_ids                      = ($event['ev_batch_id'])?explode(',', $event['ev_batch_id']):array();
                $process_param['request_id']    = $batch_ids;
                $batch_users                    = $this->notification_users($process_param);    
            }
        
            $this->Events_model->delete_event(array('event_id' => $event_id));
            $user_ids = array_unique(array_merge($course_users,$institute_users,$batch_users));
            
            $this->notify_event_news(
                array(
                    'action_code' => 'event_deleted',
                    'assets' => array('event_name' => $event_name),
                    'target' => $event_id,
                    'individual' => true,
                    'push_to' => $user_ids
                )
            );
        }
        else
        {
            return false;
        }
    }
    public function notify_event_news($param = array())
    {
        if(empty($param))
        {
            $param      = file_get_contents('php://input');
            $param      = json_decode($param, true);
        }
        $user_ids       = isset($param['push_to'])?$param['push_to']:array();
        $destinations   = array_chunk($user_ids, 100);
        $queue_size     = sizeof($destinations);
        if($queue_size>1)
        {
            for($i=0; $i<$queue_size;$i++)
            {   
                $request            = $param;
                $request['push_to'] = ($destinations[$i]);
                $request['trial']   = $i;
                $curlHandle         = curl_init(site_url('cron_job/notify_event_news'));
                $defaultOptions     = array (
                                        CURLOPT_POST => 1,
                                        CURLOPT_POSTFIELDS => json_encode($request),
                                        CURLOPT_RETURNTRANSFER => false ,
                                        CURLOPT_TIMEOUT_MS => 1000,
                                     );
                curl_setopt_array($curlHandle , $defaultOptions);
                curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, FALSE);     
                curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2); 
                curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array(
                    'request-token: '.sha1(config_item('acct_domain').config_item('id')),
                ));
                curl_exec($curlHandle);
                curl_close($curlHandle);
                echo json_encode(array('success' => true, 'message' => 'Message send success'));
            }
        }
        else
        {
            if($_SERVER['HTTP_REQUEST_TOKEN'] == sha1(config_item('acct_domain').config_item('id')))
            {
                
                $this->load->library('Notifier');
                $this->notifier->push($param);        
            }
        }
    }
    public function notification_users($param){
        $request_type = $param['request_type']?$param['request_type']:false;
        $request_ids  = $param['request_id']?$param['request_id']:false;
        switch($request_type){
            case 'course': 
                $course_ids                 = $request_ids;
                $user_ids                   = array();
                $filter_param               = array();
                $filter_param['course_ids'] = $course_ids;
                $filter_param['select']     = 'cs_user_id';
                $result                     = $this->User_model->subscription($filter_param);
                $user_ids                   = array_column($result,'cs_user_id');
            
            break; 
            case 'institute':  
                $institute_ids                  = $request_ids;
                $user_ids                       = array();
                $filter_param                   = array();
                $filter_param['institute_ids']  = $institute_ids;
                $filter_param['select']         = 'id';
                $result                         = $this->User_model->users($filter_param);
                $user_ids                       = array_column($result,'id');
            break; 
            case 'batch':
                $batch_ids       = $request_ids;
                $user_ids        = array();
                $result_users    = array();
                $group_users     = array();
                for($i=0;$i<count($batch_ids);$i++){
                    $filter_param               = array();
                    $filter_param['group_id']   = $batch_ids[$i];
                    $filter_param['select']     = 'users.id';
                    $result_users[]             = $this->Group_model->group_users($filter_param);
                }
                $group_users    = array_reduce($result_users, 'array_merge', array());
                $users          = array_intersect_key($group_users , array_unique( array_map('serialize' , $group_users) ) );
                $user_ids       = array_column($users,'id');
            break;  
        }  
       return $user_ids;
    }
    public function event_reminder(){
        $filter_param                       = array();
        $filter_param['preference']         = true;
        $filter_param['preference_type']    = 'day';
        $filter_param['select']             = 'id,ev_name,ev_date,ev_time,ev_course_id,ev_institute_id,ev_batch_id';
        $events                             = $this->Events_model->get_events($filter_param);
   
        foreach($events as $event){
            $curlHandle         = curl_init(site_url('cron_job/event_reminder_process'));
            $defaultOptions     = array (
                                    CURLOPT_POST => 1,
                                    CURLOPT_POSTFIELDS => json_encode($event),
                                    CURLOPT_RETURNTRANSFER => false ,
                                    CURLOPT_TIMEOUT_MS => 1000,
                                    );
            curl_setopt_array($curlHandle , $defaultOptions);
            curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, FALSE);     
            curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2); 
            curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array(
                'request-token: '.sha1(config_item('acct_domain').config_item('id')),
            ));
            curl_exec($curlHandle);
            curl_close($curlHandle);
        }
       
    }
    public function event_reminder_process(){
        if($_SERVER['HTTP_REQUEST_TOKEN'] == sha1(config_item('acct_domain').config_item('id')))
        {
            $input              = file_get_contents('php://input');
            $decoded_input      = json_decode($input, true);
            $event_id           = $decoded_input['id'];
            $event_name         = $decoded_input['ev_name'];
            $event_date         = $decoded_input['ev_date'];
            $event_time         = $decoded_input['ev_time'];
            $course_users       = array();
            $institute_users    = array();
            $batch_users        = array();
            $user_ids           = array();
                      
            if(count($decoded_input['ev_course_id'])>0){
    
                $process_param['request_type']  = 'course';
                $course_ids                     = ($decoded_input['ev_course_id'])?explode(',', $decoded_input['ev_course_id']):array();
                $process_param['request_id']    = $course_ids;
                $course_users                   = $this->notification_users($process_param);
                
            }
            if(count($decoded_input['ev_institute_id'])>0){
    
                $process_param['request_type']  = 'institute';
                $institute_ids                  = ($decoded_input['ev_institute_id'])?explode(',', $decoded_input['ev_institute_id']):array();
                $process_param['request_id']    = $institute_ids;
                $institute_users                = $this->notification_users($process_param);
                
            }
            if(count($decoded_input['ev_batch_id'])>0){
    
                $process_param['request_type']  = 'batch';
                $batch_ids                      = ($decoded_input['ev_batch_id'])?explode(',', $decoded_input['ev_batch_id']):array();
                $process_param['request_id']    = $batch_ids;
                $batch_users                    = $this->notification_users($process_param);    
            }
            
            $user_ids = array_unique(array_merge($course_users,$institute_users,$batch_users));
            
            $this->notify_event_news(
                array(
                    'action_code' => 'event_reminder',
                    'assets' => array('event_name' => $event_name,'event_date'=>$event_date,'event_time'=>$event_time),
                    'target' => $event_id,
                    'individual' => true,
                    'push_to' => $user_ids
                )
            );
        }
    }
    /* assessment nofify */
    function assessment_override_notify()
    {
        $expiry_dates       = array();
        $expiry_dates[]     = date('Y-m-d', strtotime('-1 day'));
        $expiry_dates[]     = date('Y-m-d', strtotime('+1 day'));
        $expiry_dates[]     = date('Y-m-d', strtotime('+1 week'));
        $expiry_dates[]     = date('Y-m-d', strtotime('+2 weeks'));
        $this->load->model(array('Test_model','Course_model'));
        $assessments        = $this->Test_model->check_assessments_expiry(array('expiry_date' => $expiry_dates));
        $request                = array();
        $request['assessments'] = $assessments;
        $curlHandle             = curl_init(site_url('beta/assessment_override_notify_launch'));
        $defaultOptions         = array (
                                    CURLOPT_POST => 1,
                                    CURLOPT_POSTFIELDS => json_encode($request),
                                    CURLOPT_RETURNTRANSFER => false ,
                                    CURLOPT_TIMEOUT_MS => 1000,
                                );
        curl_setopt_array($curlHandle , $defaultOptions);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, FALSE);     
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2); 
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array(
            'request-token: '.sha1(config_item('acct_domain').config_item('id')),
        ));
        curl_exec($curlHandle);
        curl_close($curlHandle);
        echo json_encode(array('success' => true, 'message' => 'Message send success'));exit;
    }
    public function assessment_override_notify_launch()
    {
        $request      = file_get_contents('php://input');
        $request      = json_decode($request, true);
        $assessments  = $request['assessments'];
        $destinations   = array_chunk($assessments, 3);
        $queue_size     = sizeof($destinations);
        if($queue_size>1)
        {
            for($i=0; $i<$queue_size;$i++)
            {   
                $request['assessments'] = $destinations[$i];
                $request['trial']       = $i;
                $curlHandle             = curl_init(site_url('beta/assessment_override_notify_launch'));
                $defaultOptions         = array (
                                            CURLOPT_POST => 1,
                                            CURLOPT_POSTFIELDS => json_encode($request),
                                            CURLOPT_RETURNTRANSFER => false ,
                                            CURLOPT_TIMEOUT_MS => 1000,
                                        );
                curl_setopt_array($curlHandle , $defaultOptions);
                curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, FALSE);     
                curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2); 
                curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array(
                    'request-token: '.sha1(config_item('acct_domain').config_item('id')),
                ));
                curl_exec($curlHandle);
                curl_close($curlHandle);
                echo json_encode(array('success' => true, 'message' => 'Message send success'));
            }
        }
        else
        {
            // if($_SERVER['HTTP_REQUEST_TOKEN'] == sha1(config_item('acct_domain').config_item('id')))
            {
                $this->assessment_override_notify_init($assessments);        
            }
        }
    }
    public function assessment_override_notify_init($assessments = array())
    {
        $this->load->model(array('Test_model'));
        $this->batch_users  = array();
        if(!empty($assessments))
        {
            $lecture_ids                = array();
            $override_objects            = array();
            foreach($assessments as $assessment)
            {
                $lecture_id             = $assessment['a_lecture_id'];
                $lecture_ids[]          = $lecture_id;
                $course_batches         = explode(',', $assessment['cb_groups']);
                foreach($course_batches as $course_batch_id)
                {
                    if(!isset($this->batch_users[$course_batch_id]))
                    {
                        $this->batch_users[$course_batch_id] = array();
                        $param              = array();
                        $param['group_id']  = $course_batch_id;
                        $param['select']    = 'users.id';
                        $group_users        = $this->Group_model->group_users($param);
                        $users              = array();
                        if(!empty($group_users))
                        {
                            foreach($group_users as $group_user)
                            {
                                $users[] = $group_user['id'];
                            }
                        }    
                        $this->batch_users[$course_batch_id] = $users;
                    }
                    if(!empty($this->batch_users[$course_batch_id]))
                    {
                        $override_objects[$lecture_id]['course_id']    = $assessment['a_course_id'];
                        $override_objects[$lecture_id]['course_name']  = $assessment['course_name'];
                        $override_objects[$lecture_id]['lecture_name'] = $assessment['lecture_name'];
                        $override_objects[$lecture_id]['override'][$course_batch_id]['submission_date'] = $assessment['last_date'];
                        $override_objects[$lecture_id]['override'][$course_batch_id]['users']           = $this->batch_users[$course_batch_id];
                    }
                }
            }
            $lecture_overrides          = $this->Test_model->check_override_expiry(array('lecture_ids' => array_unique($lecture_ids)));
            if(!empty($lecture_overrides))
            {
                foreach($lecture_overrides as $lecture_override)
                {
                    $batches            = explode(',', $lecture_override['override_batches']);
                    if(!empty($batches))
                    {
                        foreach($batches as $batch_id)
                        {
                            $lecture_id     = $lecture_override['lecture_id'];
                            $override_objects[$lecture_id]['override'][$batch_id]['submission_date']   = $lecture_override['last_date'];
                            if(empty($override_objects[$lecture_id]['override'][$batch_id]['users']))
                            {
                                unset($override_objects[$lecture_id]['override'][$batch_id]);
                            }
                        }    
                    }
                }
                
            }
            foreach($override_objects as $override_object)
            {
                $course_title  = $override_object['course_name'];
                $lecture_title = $override_object['lecture_name'];
                $course_id     = $override_object['course_id'];
                foreach($override_object['override'] as $notification)
                {
                     //Notification
                    $this->notify_event_news(
                        array(
                        'action_code' => 'quiz_notify',
                        'assets' => array('course_name' => $course_title,'quiz_name' => $lecture_title,'date' => $notification['submission_date'],'course_id'=> $course_id),
                        'individual' => true,
                        'push_to' => $notification['users']
                        )
                    );
                    //End notification
                }
               
            }
            
        }
    }
//========================================================================
    function assignment_override_notify()
    {
        $expiry_dates       = array();
        $expiry_dates[]     = date('Y-m-d', strtotime('-1 day'));
        $expiry_dates[]     = date('Y-m-d', strtotime('+1 day'));
        $expiry_dates[]     = date('Y-m-d', strtotime('+1 week'));
        $expiry_dates[]     = date('Y-m-d', strtotime('+2 weeks'));
        $this->load->model(array('Test_model','Course_model'));
        $assignments        = $this->Test_model->check_assignment_expiry(array('expiry_date' => $expiry_dates));
        $request                = array();
        $request['assignments'] = $assignments;
        $curlHandle             = curl_init(site_url('beta/assignment_override_notify_launch'));
        $defaultOptions         = array (
                                    CURLOPT_POST => 1,
                                    CURLOPT_POSTFIELDS => json_encode($request),
                                    CURLOPT_RETURNTRANSFER => false ,
                                    CURLOPT_TIMEOUT_MS => 1000,
                                );
        curl_setopt_array($curlHandle , $defaultOptions);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, FALSE);     
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2); 
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array(
            'request-token: '.sha1(config_item('acct_domain').config_item('id')),
        ));
        curl_exec($curlHandle);
        curl_close($curlHandle);
        echo json_encode(array('success' => true, 'message' => 'Message send success'));exit;
    }
    public function assignment_override_notify_launch()
    {
        $request      = file_get_contents('php://input');
        $request      = json_decode($request, true);
        $assignments  = $request['assignments'];
        $destinations   = array_chunk($assignments, 3);
        $queue_size     = sizeof($destinations);
        if($queue_size>1)
        {
            for($i=0; $i<$queue_size;$i++)
            {   
                $request['assignments'] = $destinations[$i];
                $request['trial']       = $i;
                $curlHandle             = curl_init(site_url('beta/assignment_override_notify_launch'));
                $defaultOptions         = array (
                                            CURLOPT_POST => 1,
                                            CURLOPT_POSTFIELDS => json_encode($request),
                                            CURLOPT_RETURNTRANSFER => false ,
                                            CURLOPT_TIMEOUT_MS => 1000,
                                        );
                curl_setopt_array($curlHandle , $defaultOptions);
                curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, FALSE);     
                curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2); 
                curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array(
                    'request-token: '.sha1(config_item('acct_domain').config_item('id')),
                ));
                curl_exec($curlHandle);
                curl_close($curlHandle);
                echo json_encode(array('success' => true, 'message' => 'Message send success'));
            }
        }
        else
        {
            // if($_SERVER['HTTP_REQUEST_TOKEN'] == sha1(config_item('acct_domain').config_item('id')))
            {
                $this->assignment_override_notify_init($assignments);        
            }
        }
    }
    public function assignment_override_notify_init($assignments = array())
    {
        $this->load->model(array('Test_model'));
        $this->batch_users  = array();
        if(!empty($assignments))
        {
            $lecture_ids                = array();
            $override_objects            = array();
            foreach($assignments as $assignment)
            {
                $lecture_id             = $assignment['dt_lecture_id'];
                $lecture_ids[]          = $lecture_id;
                $course_batches         = explode(',', $assignment['cb_groups']);
                foreach($course_batches as $course_batch_id)
                {
                    if(!isset($this->batch_users[$course_batch_id]))
                    {
                        $this->batch_users[$course_batch_id] = array();
                        $param              = array();
                        $param['group_id']  = $course_batch_id;
                        $param['select']    = 'users.id';
                        $group_users        = $this->Group_model->group_users($param);
                        $users              = array();
                        if(!empty($group_users))
                        {
                            foreach($group_users as $group_user)
                            {
                                $users[] = $group_user['id'];
                            }
                        }    
                        $this->batch_users[$course_batch_id] = $users;
                    }
                    if(!empty($this->batch_users[$course_batch_id]))
                    {
                        $override_objects[$lecture_id]['course_id']    = $assignment['dt_course_id'];
                        $override_objects[$lecture_id]['course_name']  = $assignment['course_name'];
                        $override_objects[$lecture_id]['lecture_name'] = $assignment['lecture_name'];
                        $override_objects[$lecture_id]['override'][$course_batch_id]['submission_date'] = $assignment['last_date'];
                        $override_objects[$lecture_id]['override'][$course_batch_id]['users']           = $this->batch_users[$course_batch_id];
                    }
                }
            }
            $lecture_overrides          = $this->Test_model->check_override_expiry(array('lecture_ids' => array_unique($lecture_ids)));
            if(!empty($lecture_overrides))
            {
                foreach($lecture_overrides as $lecture_override)
                {
                    $batches            = explode(',', $lecture_override['override_batches']);
                    if(!empty($batches))
                    {
                        foreach($batches as $batch_id)
                        {
                            $lecture_id     = $lecture_override['lecture_id'];
                            $override_objects[$lecture_id]['override'][$batch_id]['submission_date']   = $lecture_override['last_date'];
                            if(empty($override_objects[$lecture_id]['override'][$batch_id]['users']))
                            {
                                unset($override_objects[$lecture_id]['override'][$batch_id]);
                            }
                        }    
                    }
                }
                
            }
            foreach($override_objects as $override_object)
            {
                $course_title  = $override_object['course_name'];
                $lecture_title = $override_object['lecture_name'];
                $course_id     = $override_object['course_id'];
                foreach($override_object['override'] as $notification)
                {
                     //Notification
                    $this->notify_event_news(
                        array(
                        'action_code' => 'assignment_notify',
                        'assets' => array('course_name' => $course_title,'assignment_name' => $lecture_title,'date' => $notification['submission_date'],'course_id'=> $course_id),
                        'individual' => true,
                        'push_to' => $notification['users']
                        )
                    );
                    //End notification
                }
               
            }
            
        }
    }
    public function live_reminder(){
        $filter_param                       = array();
        $filter_param['date']               = true;
        $filter_param['preference']         = true;
        $filter_param['preference_type']    = 'day';
        $filter_param['join']               = true;
        $filter_param['select']             = 'a.id,a.ll_lecture_id,a.ll_course_id,a.ll_date,a.ll_time,b.cl_lecture_name as live_name';
        $live_events                        = $this->Course_model->get_live_event($filter_param);
        
        if(!empty($live_events)){
            foreach($live_events as $live){
                $curlHandle         = curl_init(site_url('cron_job/live_reminder_process'));
                $defaultOptions     = array (
                                        CURLOPT_POST => 1,
                                        CURLOPT_POSTFIELDS => json_encode($live),
                                        CURLOPT_RETURNTRANSFER => false ,
                                        CURLOPT_TIMEOUT_MS => 100,
                                        );
                curl_setopt_array($curlHandle , $defaultOptions);
                curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, FALSE);     
                curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2); 
                curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array(
                    'request-token: '.sha1(config_item('acct_domain').config_item('id')),
                ));
                curl_exec($curlHandle);
                curl_close($curlHandle);
            }
        }
       
    }
    public function live_reminder_process(){
        if($_SERVER['HTTP_REQUEST_TOKEN'] == sha1(config_item('acct_domain').config_item('id')))
        {
            $input                      = file_get_contents('php://input');
            $decoded_input              = json_decode($input, true);
            $course_id                  = $decoded_input['ll_course_id'];
            $live_date                  = $decoded_input['ll_date'];
            $live_time                  = $decoded_input['ll_time'];
            $lecture_id                 = $decoded_input['ll_lecture_id'];
            $live_name                  = $decoded_input['live_name'];
            $objects                    = array();
            $objects['key']             = 'course_'.$course_id;
            $callback                   = 'course_details';
            $course                     = $this->memcache->get($objects, $callback,array()); 
            
            $course_name                = isset($course['cb_title'])?$course['cb_title']:'';
            $user_ids                   = array();
            $filter_param               = array();
            $filter_param['course_id']  = $course_id;
            $filter_param['select']     = 'cs_user_id';
            $result                     = $this->User_model->subscription($filter_param);
            $user_ids                   = array_unique(array_column($result,'cs_user_id'));
            
            $this->notify_event_news(
                array(
                    'action_code' => 'live_reminder',
                    'assets' => array('live_name' => $live_name,'live_date'=>$live_date,'live_time'=>$live_time,'course_name'=>$course_name),
                    'target' => $lecture_id,
                    'individual' => true,
                    'push_to' => $user_ids
                )
            );
            
        }
    }
    public function send_notification(){

       
        if($_SERVER['HTTP_REQUEST_TOKEN'] == sha1(config_item('acct_domain').config_item('id')))
        {
            $param          = file_get_contents('php://input');
            $course_list    = json_decode($param, true);
            // print_r($course_list['user_id']);exit;
            //Notification
            $this->load->library('Notifier');
            foreach($course_list['course_names'] as $course){

                $this->notifier->push(
                    array(
                        'action_code' => 'course_subscribed',
                        'assets' => array('course_name' => $course['course_name'],'course_id' => $course['course_id']),
                        'target' => $course['course_id'],
                        'individual' => true,
                        'push_to' => array($course_list['user_id'])
                    )
                );
            }
            
        }
    }
    public function announcement_notification(){

        $data               = json_decode( file_get_contents( 'php://input' ), true );
       
        $course_id          = $data['course_id'];
        $batch_ids          = $data['batch_ids'];
        $institute_ids      = $data['institution_ids'];
        $course_title       = $data['title'];
        $announcement_id    = $data['announcement_id'];

        if(!empty($course_id)){

            $filter_param               = array();
            $filter_param['select']     = 'id,cs_user_id';
            $filter_param['course_id']  = $course_id;
            $enrolled_users             = $this->Course_model->course_enrolled($filter_param);
            $notify_to                  = array();
            $notify_to                  = array_column($enrolled_users,'cs_user_id');
        }
        if(!empty($institute_ids)){

            $filter_param                   = array();
            $filter_param['select']         = 'id,us_institute_id';
            $filter_param['institute_ids']  = $institute_ids;
            $enrolled_users                 = $this->User_model->users($filter_param);
            $notify_to                      = array();
            $notify_to                      = array_column($enrolled_users,'id');
        }
        if(!empty($batch_ids)){

            $result_users                   = array();
            $notify_to                      = array();
            
            foreach($batch_ids as $batch_id){

                $filter_param               = array();
                $filter_param['group_id']   = $batch_id;
                $filter_param['select']     = 'users.id';
                $result_users[]             = $this->Group_model->group_users($filter_param);
            }
            
            $group_users    = array_reduce($result_users, 'array_merge', array());
            $users          = array_intersect_key($group_users , array_unique( array_map('serialize' , $group_users) ) );
            $notify_to      = array_column($users,'id');
           
        }
       
        //Notification
        $this->load->library('Notifier');
        $this->notifier->push(
            array(
                'action_code' => 'announcement_created',
                'assets' => array('course_name' => $course_title,'course_id' => $course_id),
                'target' => $course_id,
                'individual' => true,
                'push_to' => $notify_to
            )
        );
        //End notification
    }

    function send_student_image_to_s3_launch()
    {
        if($_SERVER['HTTP_REQUEST_TOKEN'] == sha1(config_item('acct_domain').config_item('id')))
        {
            $config  = file_get_contents('php://input');
            $config  = json_decode($config, true);
            if( isset($config['files']) )
            {
                uploadToS3Bulk($config['files']);
                if(isset($config['source']) && $config['source'] != '')
                {
                    shell_exec('rm -rf  '.$config['source']);   
                }
        
            }
        }
    }

    public function archive_subscription_expiry()
    {
        $response               = array();
        $response['error']      = false;
        $this->load->model(array('Course_model','Archive_model'));

        // get expiry subscriptions
        $param                  = array();
        $param['select']        = 'id,cs_course_id,cs_user_id';
        $param['expired']       = 1;
        $param['archived']      = 0;
        $subscriptions          = $this->Course_model->enrolled_users($param);
        $course_users           = array();
        $subscription_ids       = array();
        $user_ids               = array();
        $course_ids             = array();
        foreach($subscriptions as $subscription)
        {
            if(!isset($course_users[$subscription['cs_course_id']]))
            {
                $course_users[$subscription['cs_course_id']] = array();
            }
            $course_users[$subscription['cs_course_id']][]   = $subscription['cs_user_id'];
            $subscription_ids[]                              = $subscription['id']; 
            $user_ids[]                                      = $subscription['cs_user_id'];
            $course_ids[]                                    = $subscription['cs_course_id'];
        }
        
        $user_ids                                  = array_unique($user_ids);
        $course_ids                                = array_unique($course_ids);
        $save_archive                              = array();
        $save_archive_flag                         = array();
        $courses                                   = array();
        $users                                     = array();
        $subscription_data                         = array();

        // Get courses details
        $courses_param                             = array();
        $courses_param['select']                   = 'id,cb_title,cb_code,cb_is_free';
        $courses_param['course_id_list']           = $course_ids;
        $course_details                            = $this->Course_model->course_new($courses_param);
        foreach($course_details as $course_detail)
        {
            $courses[$course_detail['id']]         = $course_detail;
        }
        
        // Get enrolled users details
        $users_param                               = array();
        $users_param['select']                     = 'id,us_name,us_email,us_register_number,us_image,us_about,us_phone,us_role_id,us_institute_id,us_branch,us_branch_code,us_institute_code,us_register_no,us_groups';
        $users_param['user_ids']                   = $user_ids;
        $user_details                              = $this->User_model->users($users_param);
        foreach($user_details as $user_detail)
        {
            $users[$user_detail['id']]             = $user_detail;
        }

        // Get subscription details
        $subscription_param                        = array();
        $subscription_param['select']              = 'id,cs_course_id,cs_user_id,cs_user_name,cs_user_groups,cs_user_institute,cs_subscription_date,cs_start_date,cs_end_date,cs_certificate_issued,cs_download_certificate,cs_percentage,cs_lecture_log,cs_auto_grade,cs_manual_grade';
        $subscription_param['ids']                 = $subscription_ids;
        $subscription_details                      = $this->Course_model->subscription_details($subscription_param);
        foreach($subscription_details as $subscription_detail)
        {
            $subscription_data[$subscription_detail['cs_course_id']][$subscription_detail['cs_user_id']]            = $subscription_detail;
        }

        // Set archive data
        if(!empty($courses))
        {
            foreach ($courses as $course)
            {
                $enrolled_users                               = (isset($course_users[$course['id']]))?$course_users[$course['id']]:array();
                $enrolled_users                               = array_unique($enrolled_users);
                foreach($enrolled_users as $enrolled_user)
                {
                    $flag_params                              = array();
                    $flag_params['cs_course_id']              = $course['id'];
                    $flag_params['cs_user_id']                = $enrolled_user;
                    $flag_params['cs_archived']               = 1;
                    $archive_params                           = array();
                    $archive_params['sa_course_id']           = $course['id'];
                    $archive_params['sa_course_title']        = (isset($course['cb_title']))?$course['cb_title']:NULL;
                    $archive_params['sa_course_code']         = (isset($course['cb_code']))?$course['cb_code']:NULL;
                    $archive_params['sa_course_details']      = json_encode($course);
                    $archive_params['sa_user_id']             = $enrolled_user; 
                    $user_data                                = (isset($users[$enrolled_user]))?$users[$enrolled_user]:array();        
                    $archive_params['sa_user_name']           = (isset($user_data['us_name']))?$user_data['us_name']:NULL;
                    $archive_params['sa_user_email']          = (isset($user_data['us_email']))?$user_data['us_email']:NULL;
                    $archive_params['sa_user_register_number']= (isset($user_data['us_register_number']))?$user_data['us_register_number']:NULL;
                    $archive_params['sa_user_phone']          = (isset($user_data['us_phone']))?$user_data['us_phone']:NULL;
                    $archive_params['sa_user_institute_id']   = (isset($user_data['us_institute_id']))?$user_data['us_institute_id']:NULL;
                    $archive_params['sa_user_groups']         = (isset($user_data['us_groups']))?$user_data['us_groups']:NULL;
                    $archive_params['sa_user_details']        = json_encode($user_data);
                    $save_subscription                        = (isset($subscription_data[$course['id']][$enrolled_user]))?$subscription_data[$course['id']][$enrolled_user]:array();
                    $archive_params['sa_cs_startdate']        = (isset($save_subscription['cs_start_date']))?$save_subscription['cs_start_date']:NULL;
                    $archive_params['sa_cs_enddate']          = (isset($save_subscription['cs_end_date']))?$save_subscription['cs_end_date']:NULL;
                    $archive_params['sa_subscription_details']= json_encode($save_subscription);
                    $archive_params['sa_account_id']          = config_item('id');
                    $save_archive[]                           = $archive_params; 
                    $save_archive_flag[]                      = $flag_params;
                } 
            }
        }

        // Save archive data and set flag in course subscription
        if(!empty($save_archive))
        {
            $this->Archive_model->save_archive_process($save_archive);
            $this->Archive_model->subscription_archive_flag($save_archive_flag);
            $response['message']    = 'Subscription archieved successfully!';
        }
        else
        {
            $response['message']    = 'No data to archieve!';
        }
        echo json_encode($response);die();

    }

    function archive_deleted_user()
    {
        $response                       = array();
        $response['error']              = false;
        $this->load->library(array('archive'));
        $param                          = array();
        $param['select']                = 'id';
        $param['filter']                = 'deleted';
        $param['check_deleted_time']    = 1;
        $users                          = $this->User_model->users($param);
        $user_ids                       = array_column($users, 'id');
        $archive_param                  = array();
        $archive_param['user_ids']      = $user_ids;
        if(!empty($user_ids))
        {
            $this->archive->archive_user($archive_param);
            $this->User_model->delete_users($user_ids);
            $response['message']        = 'Subscription archieved successfully!';
        }
        else
        {
            $response['message']        = 'No data to archieve!';
        }
        echo json_encode($response);die();
    }

    function archive_deleted_course()
    {
        $response                       = array();
        $response['error']              = false;
        $this->load->library(array('archive'));
        $param                          = array();
        $param['select']                = 'id';
        $param['filter']                = 'deleted';
        $param['check_deleted_time']    = 1;
        $courses                        = $this->Course_model->courses_new($param);
        $course_ids                     = array_column($courses, 'id');
        //echo '<pre>';print_r($course_ids);die();
        $archive_param                  = array();
        $archive_param['course_ids']    = $course_ids;
        if(!empty($course_ids))
        {
            $this->archive->archive_course($archive_param);
            $this->Course_model->delete_courses($course_ids);
            $response['message']        = 'Subscription archieved successfully!';
        }
        else
        {
            $response['message']        = 'No data to archieve!';
        }
        
        echo json_encode($response);die();
    }

    function create_academic_year()
    {
        $response                       = array();
        $response['error']              = false;
        $current_year                   = date('Y');
        $academic_year_code             = ($current_year-1).'-'.$current_year;
        // $academic_year_code             = '2019-2020';
        // $academic_year_label            = 'Academic year of '.$academic_year_code;
        $param                          = array();
        $param['select']                = 'id';
        $param['code']                  = $academic_year_code;
        $academic_year                  = $this->Course_model->academic_years($param);
        if(empty($academic_year))
        {
            $save_academic_year                     = array();
            $save_academic_year['ay_year_code']     = $academic_year_code;
            $save_academic_year['ay_year_label']    = $academic_year_label;
            $save_academic_year['ay_active']        = '0';
            //$academic_year_id                       = $this->Course_model->save_academic_year($save_academic_year);

            //Register course and institutes to course consolidated report table
            $consolidations                         = array();
            $courses                                = $this->Course_model->course_for_consolidation();
            $objects                                = array();
            $objects['key']                         = 'institutes'; 
            $callback                               = 'institutes';
            $institutes                             = $this->memcache->get($objects, $callback);
            foreach($institutes as $institute)
            {
                if(!empty($courses))
                {
                    foreach($courses as $course)
                    {
                        $consolidations[]           = array( 
                                                        'id'                        => false,
                                                        'ccr_course_id'             => $course['id'],
                                                        'ccr_institute_id'          => $institute['id'],
                                                        'ccr_total_enrolled'        => 0,
                                                        'ccr_total_completed'       => 0,
                                                        'ccr_academic_year_id'      => $academic_year_id,
                                                        'ccr_academic_year_code'    => $academic_year_code,
                                                        'ccr_account_id'            => config_item('id')
                                                        );
                    }
                }
            }
            if(!empty($consolidations))
            {
                $this->Course_model->save_consolidation($consolidations);
                $response['message']        = 'Course consolidated rows added successfully!';
            } 
            else
            {
                $response['message']        = 'No data to add!';
            }
            echo json_encode($response);die();
        }
        //echo '<pre>';print_r($academic_year);die();

    }

    public function payment_invoice($order_id = 0)
    {
        if(empty($order_id))
        {
            $status_code            = '404';
            $headers                = array('error' => true,'status_code'=> $status_code,'message' => 'Not available right now');
            send_response($status_code ,$headers);
        }
        $this->load->model('Order_model');
        $data            = array();
        $data['order']   = $this->Order_model->order(array('order_id' => $order_id));
        
        if($data['order']['ph_status']=='1')
        {
            $this->load->view($this->config->item('theme').'/order_invoice', $data);
        }
    }

    //To delete mobile api get subscriptions memcache
    public function update_mobile_subscription_courses()
    {
        $data                                   = json_decode( file_get_contents( 'php://input' ), true );
        $course_id                              = $data['id'];
        if(!empty($course_id))
        {
            $enrolled_users                     = $this->Course_model->course_enrolled(array('course_id'=>$course_id,'select'=>'cs_user_id')); 
            if(!empty($enrolled_users)) 
            {
                foreach($enrolled_users as $enrolled_user)
                {
                    $this->memcache->delete('mobile_enrolled_' . $enrolled_user['cs_user_id']);
                }
            }
        }
    }

    //To delete mobile api get subscriptions memcache
    public function update_mobile_subscription_bundles()
    {
        $data                                   = json_decode( file_get_contents( 'php://input' ), true );
        $bundle_id                              = $data['id'];
        if(!empty($bundle_id))
        {
            $enrolled_users                     = $this->Bundle_model->enrolled(array('bundle_id'=>$bundle_id,'select'=>'bs_user_id')); 
            if(!empty($enrolled_users)) 
            {
                foreach($enrolled_users as $enrolled_user)
                {
                    $this->memcache->delete('mobile_enrolled_' . $enrolled_user['bs_user_id']);
                }
            }
        }
    }
}

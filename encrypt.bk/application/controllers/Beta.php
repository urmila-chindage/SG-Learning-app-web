<?php
class Beta extends CI_Controller 
{   
    function __construct()
    {
        parent::__construct();
        $redirect     = $this->auth->is_logged_in_user(false, false);
        $this->__student    = $this->auth->get_current_user_session('user');
        $this->__response   = array();
        $skip_login         = array();
        // if(empty($this->__student) && !in_array($this->router->fetch_method(), $skip_login))
        // {
        //     $this->set_header(array('error' => true, 'message' => 'Please login to continue the service'));
        //     $this->set_body();
        //     $this->set_response();
        // }

        $this->load->model(array('Content_model','Course_model','User_model','Group_model'));
        $this->load->model(array('Content_model'));
        $input          = file_get_contents('php://input');
        $decoded_input  = json_decode($input, true);
        $method         = isset($decoded_input['header']['method'])? $decoded_input['header']['method'] : '';
        $body           = isset($decoded_input['body']) ? $decoded_input['body'] : array();
        if(!empty($method))
        {
            $this->$method($body);
        }
    }

    public function index()
    {
        //redirect($this->__redirect);
    }

    //system functions. Do not change.
    private function set_header($headers = array())
    {
        if(!isset($this->__response['header']))
        {
            $this->__response['header'] = array( 'error' => false, 'message' => '' );
        }
        if(!empty($headers))
        {
            foreach($headers as $header_key => $header)
            {
                $this->__response['header'][$header_key] = $header;
            }
        }
    }

    private function set_body($body = array())
    {
        if(!isset($this->__response['body']))
        {
            $this->__response['body'] = array();
        }
        if(!empty($body))
        {
            foreach($body as $body_key => $body_value)
            {
                $this->__response['body'][$body_key] = $body_value;
            }
        }
    }

    private function set_response()
    {
        echo json_encode($this->__response);die;
    }

    private function curriculum_list($param = array())
    {
        $course_id                         = $param['course_id'];
        $user                              = $this->auth->get_current_user_session('user');
        $user_id                           = $user['id'];
        //$user_id                           = '2867';

        // Get course details
        $course_objects                    = array();
        $course_objects['key']             = 'course_'.$course_id;
        $course_callback                   = 'course_details';
        $course_params                     = array();
        $course_params['id']               = $course_id;
        $course_details                    = $this->memcache->get($course_objects, $course_callback, $course_params);
        
        // Get override details
        $this->load->model(array('Course_model'));
        $course_override                   = $this->Course_model->lecute_override(array('course_id' => $course_id));

        // Get subscription details
        $subscription_objects              = array();
        $subscription_objects['key']       = 'subscription_'.$course_id.'_'.$user_id;
        $subscription_callback             = 'subscription_details';
        $subscription_params               = array();
        $subscription_params['user_id']    = $user_id;
        $subscription_params['course_id']  = $course_id;
        $subscription_details              = $this->memcache->get($subscription_objects, $subscription_callback, $subscription_params);

        // Get transition details
        $transition_objects                = array();
        $transition_objects['key']         = 'transition_contents';
        $transition_callback               = 'transition_contents';
        $transition_params                 = array();
        $transition_contents               = $this->memcache->get($transition_objects, $transition_callback, $transition_params);
        $random_transitions                = array_rand($transition_contents,(sizeof($course_details['lectures'])+5));
        
        $transition_messages               = array();
        foreach($random_transitions as $random_transition)
        {
            $transition_messages[]         = $transition_contents[$random_transition];
        }

        $sections                          = $course_details['sections'];
        $lectures                          = $course_details['lectures'];
    
        $section_lectures                  = array();
        foreach($lectures as $lecture)
        {
            $section_lectures[$lecture['cl_section_id']][] = $lecture;
        }
        
        $section_details                              = array();
        foreach($sections as $section)
        {
            $section_id                               = $section['id'];
            $section_detail                           = array();
            $section_detail['id']                     = $section['id'];
            $section_detail['s_name']                 = $section['s_name'];
            $section_detail['s_course_id']            = $section['s_course_id'];
            $section_detail['s_order_no']             = $section['s_order_no'];
            $section_detail['s_lectures']             = (isset($section_lectures[$section_id]))?$section_lectures[$section_id]:array();
            $section_details[]                        = $section_detail;
        }

        $body                                          = array();
        $body['curriculum']['sections']                = $section_details;
        $body['curriculum']['override']                = $course_override;
        $body['curriculum']['subscription']            = $subscription_details;
        $body['transition']['transition_contents']     = $transition_messages;
        
        $this->set_header(array('error' => false, 'message' => ''));
        $this->set_body($body);
        $this->set_response();
    }

    public function assignment_submission($param = array()){
        // Get param details
        $course_id          = $param['course_id'];
        $lecture_id         = $param['lecture_id'];
        $subscribe_id       = $param['subscribe_id'];
        $assignment         = $param['subscribe_id'];
        $course             = $param['subscribe_id'];
        //$user_id            = $this->__student['id'];
        $user_id            = 19654;
        $files              = implode($param['files'],",");
        $comments           = $param['comments'];

        if(!$course_id)
        {
            $this->set_header(array('error' => true, 'message' => 'Course id is missing'));
            $this->set_body();
            $this->set_response();
        }

        if(!$lecture_id)
        {
            $this->set_header(array('error' => true, 'message' => 'Lecture_id id is missing'));
            $this->set_body();
            $this->set_response();
        }

        // set assignment comment and files details
        $comments_data                 = array();
        $comments_data['user_id']      = $user_id;
        $comments_data['user_type']    = 0;
        $comments_data['comment']      = $comments;
        $comments_data['file']         = $files;
        $comments_data['update_date']  = date("Y-m-d");
        $comments_param                = array();
        $comments_param[]              = $comments_data;

        $save_param                                 = array();
        $save_param['id']                           = false;
        $save_param['dtua_lecture_id']              = $lecture_id;
        $save_param['dtua_course_id']               = $course_id;
        $save_param['dtua_user_id']                 = $user_id;
        $save_param['dtua_comments']                = json_encode($comments_param);
        $save_param['dtua_grade_higher_value']      = 0;
        

        $this->load->model(array('Course_model'));
        $descriptive_user_answer            = $this->Course_model->register_user_descriptive_test($save_param);
        if($descriptive_user_answer)
        {
            //Notification
            /*$preveleged_users = $this->accesspermission->previleged_users(array('module' => 'course_content'));
            foreach($preveleged_users as $preveleged_user)
            {
                $notify_to[$preveleged_user['id']] = array($param['user_id']);
            }*/

            $objects                = array();
            $objects['key']         = 'course_notification_' . $course_id;
            $callback               = 'course_notification';
            $params                 = array('course_id' => $course_id);
            $discussion_forum       = $this->memcache->get($objects, $callback, $params);

            $preveleged_users       = $discussion_forum['preveleged_users'];

            foreach($preveleged_users as $preveleged_user)
            {
                $notify_to[$preveleged_user] = array($param['user_id']);
            }

            $this->load->library('Notifier');
            $this->notifier->push(
                array(
                    'action_code' => 'assignment_submitted',
                    'assets' => array('assignment_name' => $assignment,'course_name' => $course),
                    'target' => $lecture_id,
                    'individual' => true,
                    'push_to' => $notify_to
                )
            );
            //End notification
            $this->set_header(array('error' => false, 'message' => 'Saved successfully'));
            $this->set_response();
        } 
        else
        {
            $this->set_header(array('error' => false, 'message' => 'Error in saving details'));
            $this->set_response();
        }
    }

    public function remove_assignment_file($param = array()){
        $file = $param['file'];
        $path = 'folder_name/'.$file;
        if(file_exists($path))
        {
            unlink($path);
            $this->set_header(array('error' => false, 'message' => 'Deleted successfully'));
            $this->set_response();
        } 
        else
        {
            $this->set_header(array('error' => false, 'message' => 'File is not found!'));
            $this->set_response();
        } 
        
    }



    // function assessment_override_notify()
    // {
    //     $expiry_dates     = array();
    //     $expiry_dates[]   = date('Y-m-d', strtotime('-1 day'));
    //     $expiry_dates[]   = date('Y-m-d', strtotime('+1 day'));
    //     $expiry_dates[]   = date('Y-m-d', strtotime('+1 week'));
    //     $expiry_dates[]   = date('Y-m-d', strtotime('+2 weeks'));
    //     $this->load->model(array('Test_model','Course_model'));
    //     $assessments       = $this->Test_model->check_assessments_expiry(array('expiry_date' => $expiry_dates));
    //     if(!empty($assessments))
    //     {
    //         $course_id                  = array();
    //         $lecture_id                 = array();
    //         $override_notify            = array();
    //         foreach($assessments as $assessment)
    //         {
    //             $override_batches       = array();
    //             $course_id[]            = $assessment['a_course_id'];
    //             $lecture_id[]           = $assessment['a_lecture_id'];
    //             $submission_date        = $assessment['last_date'].'-'.$assessment['end_time'];
    //             $override_batches       = explode(',', $assessment['cb_groups']);
    //             $course_name            = $assessment['course_name'];
    //             $lecture_name           = $assessment['lecture_name'];
    //             foreach($override_batches as $override_batch){
    //                 $group_users        = array();
    //                 $override_notify[$assessment['a_lecture_id']]['course_id']    = $assessment['a_course_id'];
    //                 $override_notify[$assessment['a_lecture_id']]['course_name']  = $course_name;
    //                 $override_notify[$assessment['a_lecture_id']]['lecture_name'] = $lecture_name;
    //                 $override_notify[$assessment['a_lecture_id']]['override'][$override_batch]['submission_date'] = $submission_date;
    //                 $override_notify[$assessment['a_lecture_id']]['override'][$override_batch]['users']           = array();
    //                 $param              = array();
    //                 $param['group_id']  = $override_batch;
    //                 $param['select']    = 'users.id';
    //                 //$param['select']    = 'users.us_name, users.us_email';
    //                 if(empty($override_notify[$assessment['a_lecture_id']][$override_batch]['users'])){
    //                     $group_users    = $this->Group_model->group_users($param);
    //                     $users          = array();
    //                     if(!empty($group_users)){
    //                         foreach($group_users as $group_user)
    //                         {
    //                             $users[] = $group_user['id'];
    //                         }
    //                     }
    //                 }
    //                 $override_notify[$assessment['a_lecture_id']]['override'][$override_batch]['users'] = $users;
    //             }
    //         }
    
    //         $lecture_overrides       = $this->Test_model->check_override_expiry(array('course_id' => array_unique($course_id),'lecture_id' => array_unique($lecture_id)));
            
    //         if(!empty($lecture_overrides))
    //         {
    //             foreach($lecture_overrides as $lecture_override)
    //             {
    //                 $batches                = array();
    //                 $submission_date        = $lecture_override['last_date'].'-'.$lecture_override['end_time'];
    //                 $batches                = explode(',', $lecture_override['override_batches']);
    //                 foreach($batches as $batch)
    //                 {
    //                     $override_notify[$lecture_override['lecture_id']]['override'][$batch]   = $submission_date;
    //                 }
                    
    //             }
                
    //         }

    //         foreach($override_notify as $override_notification){
    //             $course_title  = $override_notification['course_name'];
    //             $lecture_title = $override_notification['lecture_name'];
    //             $notifications = $override_notification['override'];
    //             foreach($notifications as $notification){
    //                 $submission_date = $notification['submission_date'];
    //                 $notify_to       = $notification['users'];
    //                  //Notification
    //                 $this->notify_event_news(
    //                     array(
    //                     'action_code' => 'quiz_notify',
    //                     'assets' => array('course_name' => $course_title,'quiz_name' => $lecture_title,'date' => $submission_date),
    //                     'individual' => true,
    //                     'push_to' => $notify_to
    //                     )
    //                 );
    //                 //End notification
    //             }
               
    //         }
      
    //     }
    // }


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
                $curlHandle         = curl_init(site_url('beta/notify_event_news'));
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
            // if($_SERVER['HTTP_REQUEST_TOKEN'] == sha1(config_item('acct_domain').config_item('id')))
            {
                $this->load->library('Notifier');
                $this->notifier->push($param);        
            }
        }
    }
    
    //End

    //  {
    // 	"header": {
    // 		"error": false,
    // 		"message": "",
    // 		"service": "",
    // 		"method": ""
    // 	},
    // 	"body": {
    // 		"key1": "value1",
    // 		"key2": {
    // 			"key3": "value 3"
    // 		}
    // 	}
    // }

}
<?php
class Content extends CI_Controller 
{   
    function __construct()
    {
        parent::__construct();
        $this->__student            = '';
        $user_token                 = isset($_SERVER['HTTP_X_API_KEY'])?$_SERVER['HTTP_X_API_KEY']:'';
        if($user_token)
        {
            $verified_data          = $this->api_authentication->verify_token($user_token);
            if($verified_data['token_verified'] == true)
            {
                $this->__student    = $verified_data['user'] ;
            }
        }
        else
        {
            $this->__student        = $this->auth->get_current_user_session('user');
        }
        
        $this->__response           = array();
        $this->__download_certificate_method = 'cdfa570a4072388b696cd320ad8b45589f87a2ca';//sha1('download_certificate');
        $skip_login                 = array('get_user_data','forum_auth','forum_response', $this->__download_certificate_method);


        if(empty($this->__student) && !in_array($this->router->fetch_method(), $skip_login))
        {
            $this->set_header(array('error' => true,'code'=>604, 'message' => 'Please login to continue the service'));
            $this->set_body();
            $this->set_response();
        }

    
        if( !in_array($this->router->fetch_method(), $skip_login) )
        {
            $this->load->model(array('Content_model','Course_model','User_model'));
            $input          = file_get_contents('php://input');
            $decoded_input  = json_decode($input, true);
            $method         = isset($decoded_input['header']['method'])? $decoded_input['header']['method'] : '';
            $body           = isset($decoded_input['body']) ? $decoded_input['body'] : array();
            if(!empty($method))
            {
                $this->$method($body);
            }
        }
    }

    public function index()
    {
        redirect($this->__redirect);
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
        header("Access-Control-Allow-Origin: http://localhost:3000");
        header("Access-Control-Allow-Credentials: true");
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, x-api-key ');
        echo json_encode($this->__response);die;
    }
    
    private function content_config()
    {
        $content_security                   = $this->settings->setting('has_content_security');
        $content_security_status            = $content_security['as_setting_value']['setting_value']->content_security_status;
        $react_config                       = array();
        $react_config['apiUrl']             = rtrim(site_url(),"/");
        $react_config['documentUrl']        = "https://docs.google.com/viewer/viewer?url=".uploads_url()."uploads/".config_item('acct_domain')."/course/{courseId}/documents/{docId}&embedded=true";
        $react_config['mp3Url']             = uploads_url()."uploads/".config_item('acct_domain')."/course/{courseId}/audio/{audioId}";
        $react_config['scormUrl']           = uploads_url()."{scormPath}";
        $react_config['materialUrl']        = uploads_url()."uploads/".config_item('acct_domain')."/course/{courseId}/{purpose}/{file}";
        $react_config['redactorUploadUrl']  = admin_url()."configuration/assignment_redactor_file_upload/{courseId}/{purpose}";
        $react_config['assessmentUrl']      = site_url()."material/test/{testId}#instruction";
        $react_config['siteName']           = config_item('site_name');
        $react_config['content_secuirity']  = $content_security_status;
        $user_details                       = array();
        $user_details['id']                 = $this->__student['id'];
        $user_details['us_name']            = $this->__student['us_name'];
        $user_details['us_batches']         = $this->__student['us_groups'];
        $react_config['user_details']       = json_encode($user_details);
        $this->set_header(array('error' => false, 'message' => 'Content config created successfully!'));
        $this->set_body($react_config);
        $this->set_response();
    }
    
    private function initial_data($param = array())
    {
        $response                           = array();
        // Get param details
        $course_id                          = $param['course_id'];
        $user_id                            = $this->__student['id'];
        if(!$course_id)
        {
            $this->set_header(array('error' => true,'code'=> 601, 'message' => 'Course id is not found!'));
            $this->set_body();
            $this->set_response();
        }
        $content_security                   = $this->settings->setting('has_content_security');
        $content_security_status            = $content_security['as_setting_value']['setting_value']->content_security_status;
        $react_config                       = array();
        $react_config['apiUrl']             = rtrim(site_url(),"/");
        $react_config['documentUrl']        = "https://docs.google.com/viewer/viewer?url=".uploads_url()."uploads/".config_item('acct_domain')."/course/{courseId}/documents/{docId}&embedded=true";
        $react_config['mp3Url']             = uploads_url()."uploads/".config_item('acct_domain')."/course/{courseId}/audio/{audioId}";
        $react_config['scormUrl']           = uploads_url()."{scormPath}";
        $react_config['materialUrl']        = uploads_url()."uploads/".config_item('acct_domain')."/course/{courseId}/{purpose}/{file}";
        $react_config['redactorUploadUrl']  = admin_url()."configuration/assignment_redactor_file_upload/{courseId}/{purpose}";
        $react_config['assessmentUrl']      = site_url()."material/test/{testId}#instruction";
        $react_config['siteName']           = config_item('site_name');
        $react_config['content_secuirity']  = $content_security_status;
        $user_details                       = array();
        $user_details['id']                 = $this->__student['id'];
        $user_details['us_name']            = $this->__student['us_name'];
        $user_details['us_batches']         = $this->__student['us_groups'];
        $react_config['user_details']       = json_encode($user_details);
        $response['config']                 = $react_config;       
        
        /* course preview time details */
        $objects                            = array();
        $objects['key']                     = 'course_'.$course_id;
        $callback                           = 'course_details';
        $params                             = array('id' => $course_id);
        $course_details                     = $this->memcache->get($objects, $callback,$params);     
        $course_preview_time                = $course_details['cb_preview_time'];  
        $course_preview                     = ($course_details['cb_preview']!='')?$course_details['cb_preview']:0;     

        /* user preview time details */ 
        $get_user_time                      = $this->Course_model->get_user_preview_time(array('course_id'=> $course_id, 'user_id'=> $user_id));        
        $user_preview_time                  = isset($get_user_time['cpt_course_time'])?$get_user_time['cpt_course_time']:0;        
        
        $data                               = array();
        $data['course_preview']             = $course_preview;
        $data['course_preview_time']        = $course_preview_time;
        $data['user_preview_time']          = $user_preview_time;  
        $response['course_preview']         = $data;   

        // Get override details
        $course_override                    = $this->Course_model->lecute_override(array('course_id' => $course_id, 'source'=>'course'));

        $subscription_param                 = array();
        $subscription_param['user_id']      = $user_id;
        $subscription_param['course_id']    = $course_id;
        $subscription_param['select']       = 'course_subscription.id,course_subscription.cs_course_id,course_subscription.cs_user_id,course_subscription.cs_course_validity_status,course_subscription.cs_approved,course_subscription.cs_certificate_issued,course_subscription.cs_forum_blocked,course_subscription.cs_percentage,course_subscription.cs_course_validity_status,course_subscription.cs_subscription_date,course_subscription.cs_start_date,course_subscription.cs_end_date,course_subscription.cs_auto_grade,course_subscription.cs_manual_grade,course_subscription.cs_lecture_log,course_subscription.cs_last_played_lecture';
        $this->load->model('Course_model');
        $subscription_details               = $this->Course_model->subscription_details($subscription_param);
        // Get transition details
        $transition_objects                 = array();
        $transition_objects['key']          = 'transition_contents';
        $transition_callback                = 'transition_contents';
        $transition_params                  = array();
        $transition_contents                = $this->memcache->get($transition_objects, $transition_callback, $transition_params);
        $random_transitions                 = array();
        if(!empty($transition_contents) && (count($transition_contents)>=count($course_details['lectures']) + 5))
        {
            $random_transitions             = array_rand($transition_contents,count($course_details['lectures']) + 5);
        }
        
        $transition_messages               = array();
        foreach($random_transitions as $random_transition)
        {
            $transition_messages[]         = $transition_contents[$random_transition];
        }

        $sections                          = $course_details['sections'];
        $lectures                          = $course_details['lectures'];
        $section_lectures                  = array();

        $log_data                          = json_decode($subscription_details['cs_lecture_log'],true);

        foreach($lectures as $lecture)
        {
            if($lecture['cl_lecture_type'] == 7){
                if(!isset($log_data[$lecture['id']])){
                    $section_lectures[$lecture['cl_section_id']][]  = $lecture;
                }
            }else{
                $section_lectures[$lecture['cl_section_id']][]      = $lecture;
            }
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
         
        $curriculum                                         = array();
        $curriculum['curriculum']['sections']               = $section_details;
        $curriculum['curriculum']['override']               = $course_override;
        $curriculum['curriculum']['subscription']           = $subscription_details;
        $curriculum['transition']['transition_contents']    = $transition_messages;
        $curriculum['course']                               = array(
            'id'                    => $course_details['id'],
            'cb_title'              => $course_details['cb_title'],
            'cb_slug'               => $course_details['cb_slug'],
            'cb_deleted'            => $course_details['cb_deleted'],
            'cb_image'              => $course_details['cb_image'],
            'cb_has_lecture_image'  => $course_details['cb_has_lecture_image']
        );
        $response['curriculum_list']                    = $curriculum;   

        $this->set_header(array('error' => false, 'message' => 'Details fetched successfully!'));
        $this->set_body($response);
        $this->set_response();
        
        
    }
    private function curriculum_list($param = array())
    {
        $course_id                         = $param['course_id'];
        $user_id                           = $this->__student['id'];

        if(!$course_id)
        {
            $this->set_header(array('error' => true,'code'=>601, 'message' => 'Course id is not found!'));
            $this->set_body();
            $this->set_response();
        }

        // Get course details
        $course_objects                    = array();
        $course_objects['key']             = 'course_'.$course_id;
        $course_callback                   = 'course_details';
        $course_params                     = array();
        $course_params['id']               = $course_id;
        $course_details                    = $this->memcache->get($course_objects, $course_callback, $course_params);
        
        // Get override details
        $course_override                   = $this->Course_model->lecute_override(array('course_id' => $course_id, 'source'=>'course'));

        $s_param = array('user_id' => $user_id, 'course_id' => $course_id, 'select' => 'course_subscription.id,course_subscription.cs_course_id,course_subscription.cs_user_id,course_subscription.cs_course_validity_status,course_subscription.cs_approved,course_subscription.cs_certificate_issued,course_subscription.cs_forum_blocked,course_subscription.cs_percentage,course_subscription.cs_course_validity_status,course_subscription.cs_subscription_date,course_subscription.cs_start_date,course_subscription.cs_end_date,course_subscription.cs_auto_grade,course_subscription.cs_manual_grade,course_subscription.cs_lecture_log,course_subscription.cs_last_played_lecture');
        $this->load->model('Course_model');
        $subscription_details              = $this->Course_model->subscription_details($s_param);
        // Get transition details
        $transition_objects                = array();
        $transition_objects['key']         = 'transition_contents';
        $transition_callback               = 'transition_contents';
        $transition_params                 = array();
        $transition_contents               = $this->memcache->get($transition_objects, $transition_callback, $transition_params);
        $random_transitions                = array();
        if(!empty($transition_contents) && (count($transition_contents)>=count($course_details['lectures'])+5)){
            $random_transitions                = array_rand($transition_contents,count($course_details['lectures'])+5);
        }
        
        $transition_messages               = array();
        foreach($random_transitions as $random_transition)
        {
            $transition_messages[]         = $transition_contents[$random_transition];
        }

        $sections                          = $course_details['sections'];
        $lectures                          = $course_details['lectures'];
        $section_lectures                  = array();

        $log_data                          = json_decode($subscription_details['cs_lecture_log'],true);

        foreach($lectures as $lecture)
        {
            if($lecture['cl_lecture_type'] == 7){
                if(!isset($log_data[$lecture['id']])){
                    $section_lectures[$lecture['cl_section_id']][] = $lecture;
                }
            }else{
                $section_lectures[$lecture['cl_section_id']][] = $lecture;
            }
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
            //$section_detail['cl_batch_override']      = '';
            $section_details[]                        = $section_detail;
        }
         
        $body                                          = array();
        $body['curriculum']['sections']                = $section_details;
        $body['curriculum']['override']                = $course_override;
        $body['curriculum']['subscription']            = $subscription_details;
        $body['transition']['transition_contents']     = $transition_messages;
        $body['course']                                = array(
            'id' => $course_details['id'],
            'cb_title' => $course_details['cb_title'],
            'cb_slug' => $course_details['cb_slug']
        );

        $this->set_header(array('error' => false, 'message' => 'Curriculum list fetched successfully!'));
        $this->set_body($body);
        $this->set_response();
    }
    
    private function lecture_detail($param = array())
    {

        // html//assignment//survey//assessment//live
        $data           = array();
        $data['lecture']= array();
        $course_id      = $param['course_id'];
        $lecture_id     = $param['lecture_id'];
        $user_id        = $this->__student['id'];
        $lecture_type   = $param['lecture_type'];
        
        if(!$course_id)
        {
            $this->set_header(array('error' => true,'code'=>601, 'message' => 'Course Id missing'));
            $this->set_body();
            $this->set_response();
        }
        if(!$lecture_id)
        {
            $this->set_header(array('error' => true,'code'=>601, 'message' => 'Lecture Id missing'));
            $this->set_body();
            $this->set_response();
        }
        
        //$this->load->model('Course_model');

        switch($lecture_type){

            case '3':
                $assesment_param                   = array();
                $data['lecture']['assesment']      = array();
                $data['lecture']['result']         = array();
                $assesment_param['lecture_id']     = $lecture_id;
                $assesment_param['course_id']      = $course_id;
                $assesment_param['limit']          = '1';
                $assesment_param['select']         = 'id,a_instructions,a_questions,a_mark,a_duration';
                $assesment                         = $this->Course_model->assesment($assesment_param);
                if(!empty($assesment)){
                    $data['lecture']['assesment']  = $assesment;
                }
                $assesment_param['select']         = 'aa_attempted_date,aa_duration,aa_mark_scored,aa_total_duration,aa_grade,aa_total_mark';
                $assesment_param['user_id']        = $user_id;
                $assesment_param['assesment_id']   = $data['lecture']['assesment']['id'];
                $assesment_param['direction']      = 'DESC';
                $result                            = $this->Course_model->attempt($assesment_param);
                if(!empty($result))
                {

                    $data['lecture']['result']     = $result;
                }
                break;
            case '5':
                $data['lecture']['html']     = array();
                $fetch_param                 = array();
                $fetch_param['id']           = $lecture_id ;
                $fetch_param['select']       = 'id,cl_lecture_content';
                $fetch_param['limit']        = '1';
                $fetch_param['course_id']    = $course_id;
                $html                        = $this->Course_model->lecture($fetch_param);
                if(!empty($html)){
                    $data['lecture']['html'] = $html;
                }
                break;
            case '7':
                $live_param                 = array();
                $data['lecture']['live']    = array();
                $live_param['course_id']    = $course_id;
                $live_param['lecture_id']   = $lecture_id;
                $live                       = $this->Course_model->get_course_live($live_param);
                
                $live['attentable']         = (strtotime($live['ll_date'].' '.$live['ll_time']) <= strtotime("now"))?true:false;
                $live['date_formatted']     = date('Y M j ,g:i A',strtotime($live['ll_date'].' '.$live['ll_time']));
                
                //Studio details 
                $studio                     = $this->Course_model->studios(array('id' => $live['ll_studio_id']));
                $live['stream']             = $studio['st_url'];
                //End studio details

                $data['lecture']            = $live;
                break;
            case '8':
                $data['lecture']['assignment']      = array();
                $data['lecture']['result']          = array();
                $assignmet_param                    = array();
                $assignmet_param['course_id']       = $course_id;
                $assignmet_param['lecture_id']      = $lecture_id;
                $assignmet_param['user_id']         = $user_id;
                $assignmet_param['limit']           = '1';
                $assignmet_param['select']          = 'id,dt_name,dt_file,dt_instruction,dt_last_date,dt_description';
                $assignment                         = $this->Course_model->descriptive_test($assignmet_param);
                if(!empty($assignment)){
                    $data['lecture']['assignment']  = $assignment;
                }
                $assignmet_param['select']          = 'id,mark,dtua_grade,DATE_FORMAT(created_date, "%d-%m-%Y") as submission_date,dtua_comments';
                $result                             = $this->Course_model->assignment_attempt_details($assignmet_param);
                if(!empty($result)){
                    $data['lecture']['result']      = $result;
                }
                break;
            case '13':
                $data['lecture']['survey']      = array();
                $data['lecture']['questions']   = array();
                $survey_param                   = array();
                $survey_param['course_id']      = $course_id;
                $survey_param['lecture_id']     = $lecture_id;
                $survey_param['limit']          = '1';
                $survey                         = $this->Course_model->survey($survey_param);
                if(!empty($survey)){
                    $data['lecture']['survey']  = $survey;
                }
                $survey_param['survey_id']      = $survey['survey_id'];
                $questions                      = $this->Course_model->survey_questions($survey_param);
                if(!empty($questions)){
                    $data['lecture']['questions'] = $questions;
                }
                break;
        }
        $this->set_header(array('error' => false,'code'=>601, 'message' => 'lecture details fetched'));
        $this->set_body($data);
        $this->set_response();
    }

    private function update_percentage($param = array())
    {
        $course_id      = $param['course_id'];
        $suscribe_id    = $param['subscriber_id'];
        $course_percent = $param['course_percentage'];
        $lecture_data   = $param['lecture_data'];
        $lecture_type   = $param['lectureType'];
        $lecture_name   = $param['lectureName'];
        $user_id        = $this->__student['id'];
      
        if(!$course_id)
        {
            $this->set_header(array('error' => true,'code'=>601, 'message' => 'Course Id missing'));
            $this->set_body();
            $this->set_response();
        }
        if(!$suscribe_id)
        {
            $this->set_header(array('error' => true,'code'=>601, 'message' => 'Subscriber Id missing'));
            $this->set_body();
            $this->set_response();
        }
        $fetch__param               = array();
        $fetch_param['limit']       = '1';
        $fetch__param['select']     = 'cs_lecture_log';
        $fetch__param['id']         = $suscribe_id; 
        $fetch__param['course_id']  = $course_id;
        $fetch__param['user_id']    = $user_id;
        $subscription               = $this->Course_model->subscription_details($fetch__param);
        $lecture_log                = json_decode($subscription['cs_lecture_log'],true);
        
        $last_played                = 0;
        if(count($lecture_data)>0){
            foreach($lecture_data as $lecture)
            {
                $is_already_logged            = true;
                $lecture_log_for_this_lecture = isset($lecture_log[$lecture['lecture_id']])?$lecture_log[$lecture['lecture_id']]:array();
                if(!empty($lecture_log_for_this_lecture))
                {
                    if( isset($lecture_log[$lecture['lecture_id']]['percentage']) && $lecture_log[$lecture['lecture_id']]['percentage'] >= 95 )
                    {
                        $is_already_logged    = false; 
                    }
                    $lecture_log_for_this_lecture['percentage'] = $lecture['percentage'];
                    $lecture_log_for_this_lecture['views']      = $lecture['views'];
                    $lecture_log[$lecture['lecture_id']]        = $lecture_log_for_this_lecture;
                }
                else
                {
                    $lecture_log[$lecture['lecture_id']] = $lecture;
                }
                $last_played            = $lecture['lecture_id'];

                // lecture_types array('1' => 'Video', '2' => 'Document', '3' => 'Assesment', '4' => 'Youtube', '5' => 'Text', '8' => 'Assignment','11' => 'Recorded Videos', '12' => 'Audio')
                
                if(($lecture_type=='1') || ($lecture_type=='2') || ($lecture_type=='4') || ($lecture_type=='15'))
                {
                    if((($lecture_type=='1') || ($lecture_type=='15') || ($lecture_type=='4')) && ($lecture['percentage'] > 95) && ($is_already_logged == true))
                    
                    {
                        $user_data                              = array();
                        $user_data['user_id']                   = $this->__student['id'];
                        $user_data['username']                  = $this->__student['us_name'];
                        $user_data['useremail']                 = $this->__student['us_email'];
                        $user_data['user_type']                 = $this->__student['us_role_id'];
                        $user_data['phone_number']              = $this->__student['us_phone'];
                        $message_template                       = array();
                        $message_template['username']           = $this->__student['us_name'];
                        $message_template['lecture_name']       = $lecture_name;
                        $triggered_activity                     = 'video_lecture_completed';
                        log_activity($triggered_activity, $user_data, $message_template);
                    }
                    if(($lecture_type=='2') && ($lecture['percentage'] >= 95)  && $lecture_log[$lecture['lecture_id']]['percentage'] < 95)
                    {
                        $user_data                              = array();
                        $user_data['user_id']                   = $this->__student['id'];
                        $user_data['username']                  = $this->__student['us_name'];
                        $user_data['useremail']                 = $this->__student['us_email'];
                        $user_data['user_type']                 = $this->__student['us_role_id'];
                        $user_data['phone_number']              = $this->__student['us_phone'];
                        $message_template                       = array();
                        $message_template['username']           = $this->__student['us_name'];
                        $message_template['lecture_name']       = $lecture_name;
                        $triggered_activity                     = 'document_lecture_completed';
                        log_activity($triggered_activity, $user_data, $message_template);
                    }
                }
            }

            $save_param                             = array();
            $save_param['cs_percentage']            = $course_percent;
            $save_param['cs_last_played_lecture']   = $last_played;
            $save_param['cs_lecture_log']           = json_encode($lecture_log);
            $update_date                            = date('Y-m-d H:i:s');
            $save_param['updated_date']             = $update_date;
            $filter_param               = array();
            $filter_param['course_id']  = $course_id;
            $filter_param['user_id']    = $user_id;
            $filter_param['id']         = $suscribe_id;
            $filter_param['update']     = true;
            
            $this->User_model->save_subscription_new($save_param,$filter_param);
            $this->memcache->delete('mobile_enrolled_' . $user_id);
            $this->set_header(array('error' => false, 'message' => 'Updated Successfully'));
            // $this->set_body();
            $this->set_response();
       }
    }

    private function save_user_survey($param = array())
    {
        
        $data                           = array();
        $save_param                     = array();
        $save_param['sur_survey_id']    = $param['survey_id'];
        $save_param['sur_lecture_id']   = $param['lecture_id'];
        $save_param['sur_tutor_id']     = isset($param['tutor_id'])?$param['tutor_id']:'';
        $save_param['sur_user_id']      = $this->__student['id'];
        $save_param['sur_user_name']    = $this->__student['us_name'];
        $save_param['sur_course_id']    = $param['course_id'];
        $survey_details                 = $param['details'];
        $survey_name                    = $param['survey_name'];
        $course_name                    = $param['course_name'];
        
        if(count($survey_details) > 0)
        {
            $check_param                = array();
            $check_param['select']      = 'id';
            $check_param['survey_id']   = $save_param['sur_survey_id'];
            $check_param['lecture_id']  = $save_param['sur_lecture_id'];
            $check_param['user_id']     = $save_param['sur_user_id'];
            $survey_response            = $this->Course_model->check_survey_response_exist($check_param);
            
            if($survey_response>0){

                $user_response_param                = array();
                $user_response_param['survey_id']   = $save_param['sur_survey_id'];
                $user_response_param['lecture_id']  = $save_param['sur_lecture_id'];
                $user_response_param['user_id']     = $save_param['sur_user_id'];
                $this->Course_model->delete_survey_user_response($user_response_param);

            }else{
                foreach($survey_details as $survey)
                {
    
                    $save_param['sur_question_id']  = $survey['question_id'];
                    $save_param['sur_question']     = $survey['question'];
                    $opinion                        = $survey['opinion'];
                    if(count($opinion) > 0){
                        $answer_option = array();
                        foreach($opinion as $answer){
    
                            $answer_option[] = $answer['answer'];
                        }
                        $save_param['sur_answer'] = implode(',',$answer_option);
                    }
                    $data[] = $save_param;
                }
            }
            
        }
        
        $result = $this->Course_model->save_survey_response($data);
        if($result)
        {
            $notify_to  = array();
            /*$preveleged_users = $this->accesspermission->previleged_users(array('module' => 'course_content'));
            foreach($preveleged_users as $preveleged_user)
            {
                $notify_to[$preveleged_user['id']] = array($this->__student['id']);
            }*/


            $objects                = array();
            $objects['key']         = 'course_notification_' . $param['course_id'];
            $callback               = 'course_notification';
            $params                 = array('course_id' => $param['course_id']);
            $discussion_forum       = $this->memcache->get($objects, $callback, $params);

            $preveleged_users       = $discussion_forum['preveleged_users'];

            foreach($preveleged_users as $preveleged_user)
            {
                $notify_to[$preveleged_user] = array($this->__student['id']);
            }
            
            $survey_lecture_id = json_encode(array("survey_id"=>$param['survey_id'],"lecture_id"=>$param['lecture_id']));
            $this->load->library('Notifier');
            $this->notifier->push(
                array(
                    'action_code' => 'survey_submitted',
                    'assets' => array('survey_name' => $survey_name,'course_name' => $course_name,'course_id'=>$save_param['sur_course_id'],'survey_lecture_id'=>base64_encode($survey_lecture_id)),
                    'target' => $param['course_id'],
                    'individual' => false,
                    'push_to' => $notify_to
                )
            );

            /*Log creation*/
            $user_data                              = array();
            $user_data['user_id']                   = $this->__student['id'];
            $user_data['username']                  = $this->__student['us_name'];
            $user_data['useremail']                  = $this->__student['us_email'];
            $user_data['user_type']                 = $this->__student['us_role_id'];
            $user_data['phone_number']              = $this->__student['us_phone'];
            $message_template                       = array();
            $message_template['username']           = $this->__student['us_name'];
            $message_template['survey_title']       = $survey_name;
            $message_template['course_name']        = $course_name;
            $triggered_activity                     = 'survey_submitted';
            log_activity($triggered_activity, $user_data, $message_template);
            /*End log creation*/

            $this->set_header(array('error' => false, 'message' => ' Successfully'));
            // $this->set_body();Submitted
            $this->set_response();
        }
        else
        {
            $this->set_header(array('error' => true,'code'=>601, 'message' => 'Not Submitted'));
            // $this->set_body();
            $this->set_response();
        }
    }

    public function assignment_submission($param = array()){
        // Get param details
        $course_id          = $param['course_id'];
        $lecture_id         = $param['lecture_id'];
        $subscribe_id       = $param['subscribe_id'];
        $assignment         = $param['assignment_name'];
        $course             = $param['course_name'];
        $user_id            = $this->__student['id'];
        $userid             = $this->__student['id'];
        // $user_id            = 19654;
        $files              = json_encode($param['files']);
        $comments           = $param['comments'];

        if(!$course_id)
        {
            $this->set_header(array('error' => true,'code'=>601, 'message' => 'Course id is missing'));
            $this->set_body();
            $this->set_response();
        }

        if(!$lecture_id)
        {
            $this->set_header(array('error' => true,'code'=>601, 'message' => 'Lecture_id id is missing'));
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

        $descriptive_user_answer            = $this->Course_model->register_user_descriptive_test($save_param);
        if($descriptive_user_answer)
        {
            //Notification
            $notify_to        = array();
            /*$preveleged_users = $this->accesspermission->previleged_users(array('module' => 'course_content'));
            foreach($preveleged_users as $preveleged_user)
            {
                $notify_to[$preveleged_user['id']] = array($userid);
            }*/

            $objects                = array();
            $objects['key']         = 'course_notification_' . $course_id;
            $callback               = 'course_notification';
            $params                 = array('course_id' => $course_id);
            $discussion_forum       = $this->memcache->get($objects, $callback, $params);

            $preveleged_users       = $discussion_forum['preveleged_users'];

            foreach($preveleged_users as $preveleged_user)
            {
                $notify_to[$preveleged_user] = array($userid);
            }

            $this->load->library('Notifier');
            $this->notifier->push(
                array(
                    'action_code' => 'assignment_submitted',
                    'assets' => array('assignment_name' => $assignment,'course_name' => $course,'course_id'=>$save_param['dtua_course_id']),
                    'target' => $lecture_id,
                    'individual' => false,
                    'push_to' => $notify_to
                )
            );
            //End notification

            /*Log creation*/
            $user_data                              = array();
            $user_data['user_id']                   = $this->__student['id'];
            $user_data['username']                  = $this->__student['us_name'];
            $user_data['useremail']                  = $this->__student['us_email'];
            $user_data['user_type']                 = $this->__student['us_role_id'];
            $user_data['phone_number']              = $this->__student['us_phone'];
            $message_template                       = array();
            $message_template['username']           = $this->__student['us_name'];
            $message_template['assignment_title']   = $assignment;
            $message_template['course_name']        = $course;
            $triggered_activity                     = 'assignment_submitted';
            log_activity($triggered_activity, $user_data, $message_template);

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

    private function assessment($param = array())
    {
        $response               = array();

        $course_id      = isset($param['course_id'])?$param['course_id']:false;
        $lecture_id     = isset($param['lecture_id'])?$param['lecture_id']:false;

        if(!$course_id)
        {
            $this->set_header(array('error' => true,'code'=>601, 'message' => 'Invalid course id.'));
            $this->set_response();
        }

        if(!$lecture_id)
        {
            $this->set_header(array('error' => true,'code'=>601, 'message' => 'Invalid lecture id.'));
            $this->set_response();
        }

        $user                   = $this->__student;

        $user_batches       = explode(',',$user['us_groups']);
        $batch_query        = '';
        if($user['us_groups'] != '')
        {
            foreach($user_batches as $b_key => $ub)
            {
                if($b_key == 0)
                {
                    $batch_query    .= ' CONCAT(",",lo_override_batches,",") LIKE "%,'.$ub.',%"';
                }
                else
                {
                    $batch_query    .= ' OR CONCAT(",",lo_override_batches,",") LIKE "%,'.$ub.',%"';
                }
            }
        }
        
        $objects        = array();
        $objects['key'] = 'course_'.$course_id;
        $callback       = 'course_details';
        $params         = array('id' => $course_id);
        $course_details         = $this->memcache->get($objects, $callback, $params); 

        $this->load->model('Course_model');

        $assessment_details     = $this->Course_model->assesment_details(array('course_id' => $course_id,'lecture_id' => $lecture_id));
        $override_data          = $this->Course_model->lecute_override(array('course_id' => $course_id,'batch_query' => $batch_query,'lecture_type'=>'3','lecture_id'=>$lecture_id));
        $assessments_attempt    = $this->Course_model->assesment_attempt_details(array('user_id'=>$user['id'],'course_id'=>$course_id,'lecture_id' => $lecture_id));
        
        // Get subscription details.
        // $subscription_objects              = array();
        // $subscription_objects['key']       = 'subscription_'.$course_id.'_'.$user['id'];
        // $subscription_callback             = 'subscription_details';
        // $subscription_params               = array();
        // $subscription_params['user_id']    = $user['id'];
        // $subscription_params['course_id']  = $course_id;
        // $subscription_details              = $this->memcache->get($subscription_objects, $subscription_callback, $subscription_params);

        $s_param = array('user_id' => $user['id'], 'course_id' => $course_id, 'select' => 'course_subscription.id,course_subscription.cs_course_id,course_subscription.cs_user_id,course_subscription.cs_course_validity_status,course_subscription.cs_approved,course_subscription.cs_certificate_issued,course_subscription.cs_forum_blocked,course_subscription.cs_percentage,course_subscription.cs_course_validity_status,course_subscription.cs_subscription_date,course_subscription.cs_start_date,course_subscription.cs_end_date,course_subscription.cs_auto_grade,course_subscription.cs_manual_grade,course_subscription.cs_lecture_log,course_subscription.cs_last_played_lecture');
        $this->load->model('Course_model');
        $subscription_details              = $this->Course_model->subscription_details($s_param);

        //End subscription details.

        $user_lectures          = json_decode($subscription_details['cs_lecture_log'],true);//$this->Course_model->user_lectures(array('user_id'=>$user['id'],'course_id'=>$course_id));
        // echo '<pre>';print_r($subscription_details);die;
        $assessment             = array();
        $section_id             = array();
        $assessment_sections    = array();

        foreach($course_details['lectures'] as $lecture)
        {
            if($lecture['id'] == $lecture_id)
            {
                $assessment     = $lecture;
                $section_id     = $lecture['cl_section_id'];
                break;
            }
        }

        foreach($course_details['sections'] as $section)
        {
            if($section_id == $section['id']){
                $assessment_section               = $section;
                break;
            }
        }
        

        $assessment['attempted']    = false;
        $assessment['attemptable']  = true;

        $restriction_data           = array('attempts'=>0,'my_attempts'=>0,'from_date'=>'','from_time'=>'','to_date'=>'','to_time'=>'');
        $assessment['attempt_data'] = array();

        $restriction_data['attempts']    = $assessment['cl_limited_access'];
        $restriction_data['my_attempts'] = 0;
        if(!empty($assessments_attempt))
        {
            $assessment['attempted']    = true;
            $assessments_attempt['aa_duration']         = $assessments_attempt['aa_duration']<3600?gmdate('i:s',$assessments_attempt['aa_duration']):gmdate('H:i:s',$assessments_attempt['aa_duration']);
            $assessments_attempt['aa_total_duration']   = $assessments_attempt['aa_total_duration']<3600?gmdate('i:s',$assessments_attempt['aa_total_duration']):gmdate('H:i:s',$assessments_attempt['aa_total_duration']);
            $assessments_attempt['aa_attempted_date']   = date('Y M j S',strtotime($assessments_attempt['aa_attempted_date']));

            if(isset($user_lectures[$assessment['id']]))
            {
                $assessment['log_data']             = $user_lectures[$assessment['id']];
                $restriction_data['my_attempts']    = isset($assessment['log_data']['views'])?$assessment['log_data']['views']:0;
            }
            $assessment['attempt_data'] = $assessments_attempt;
        }
        $assessment['a_data']       = $assessment_details;
        $assessment['o_data']       = array();

        $restriction_data['from_date']          = '';
        $restriction_data['from_time']          = '';
        if($assessment['a_data']['a_from_availability']==1){
            $restriction_data['from_date']          = $assessment['a_data']['a_from'];
            $restriction_data['from_time']          = $assessment['a_data']['a_from_time'];
        }
        $restriction_data['to_date']            = '';
        $restriction_data['to_time']            = '';
        if($assessment['a_data']['a_to_availability']==1){
            $restriction_data['to_date']            = $assessment['a_data']['a_to'];
            $restriction_data['to_time']            = $assessment['a_data']['a_to_time'];
        }
        // echo '<pre>';print_r($override_data);die;
        if(!empty($override_data))
        {
            $restriction_data['from_date']      = $override_data['lo_start_date'];
            $restriction_data['from_time']      = $override_data['lo_start_time'];
            $restriction_data['to_date']        = $override_data['lo_end_date'];
            $restriction_data['to_time']        = $override_data['lo_end_time'];


            $override_data['lo_end_date']    = date('Y M j S',strtotime($override_data['lo_end_date']));
            $override_data['lo_duration']    = ($override_data['lo_duration']*60)<3600?gmdate('i:s',($override_data['lo_duration']*60)):gmdate('H:i:s',($override_data['lo_duration']*60));
            $assessment['o_data']            = $override_data;
            $restriction_data['attempts']    = $override_data['lo_attempts'];
        }

        if($assessment['a_data']['a_to_availability'] == 1)
        {
            $assessment['a_data']['a_to']     = date('Y M j S',strtotime($assessment['a_data']['a_to']));
        }
        else
        {
            $assessment['a_data']['a_to']     = '';
        }

        $now                = strtotime(date('Y-m-d H:i:s'));
        if($restriction_data['from_date'] != ''){
            $date_from          = $restriction_data['from_date'];
            if($restriction_data['from_time'] != ''){
                $date_from      = $date_from.' '.date('H:i:s',strtotime($restriction_data['from_time']));
            }
            $date_from          = strtotime($date_from);
            $assessment['attemptable']  = $now>$date_from?true:false;
        }
        
        if($restriction_data['to_date'] != ''){
            $date_to            = $restriction_data['to_date'];
            if($restriction_data['to_time'] != ''){
                $date_to        = $date_to.' '.date('H:i:s',strtotime($restriction_data['to_time']));
            }
            $date_to            = strtotime($date_to);

            if(!empty($override_data))
            {
                switch($override_data['lo_period_type']){
                    case 1:
                        $date_to     = $date_to + ($override_data['lo_period']*(24*3600));
                    break;
                    case 2:
                        $date_to     = $date_to + ($override_data['lo_period']*3600);
                    break;
                    case 3:
                        $date_to     = $date_to + ($override_data['lo_period']*60);
                    break;
                }
            }

            $assessment['attemptable']  = $now<$date_to?true:false;
        }

        if($assessment['attemptable'] && $restriction_data['attempts'] > 0){
            $assessment['attemptable']  = $restriction_data['my_attempts']>=$restriction_data['attempts']?false:true;
        }

        $assessment['a_data']['a_duration']   = ($assessment['a_data']['a_duration']*60)<3600?gmdate('i:s',($assessment['a_data']['a_duration']*60)):gmdate('H:i:s',($assessment['a_data']['a_duration']*60));
        $assessment_section['assessment']     = $assessment;

        $this->set_header(array('error' => false, 'message' => 'Assesment fetched successfully!'));
        $this->set_body($assessment_section);
        $this->set_response();
    }

    private function assignment($param = array())
    {
        $response               = array();
        
        $course_id      = isset($param['course_id'])?$param['course_id']:false;
        $lecture_id     = isset($param['lecture_id'])?$param['lecture_id']:false;

        if(!$course_id)
        {
            $this->set_header(array('error' => true,'code'=>601, 'message' => 'Invalid course id.'));
            $this->set_response();
        }

        if(!$lecture_id)
        {
            $this->set_header(array('error' => true,'code'=>601, 'message' => 'Invalid lecture id.'));
            $this->set_response();
        }

        $user                   = $this->__student;

        $user_batches       = explode(',',$user['us_groups']);
        $user_batches_new   = array();
        foreach($user_batches as $user_batch){
            if($user_batch !== ''){
                $user_batches_new[] = $user_batch;
            }
        }
        $batch_query        = '';
        if(!empty($user_batches_new))
        {
            foreach($user_batches_new as $b_key => $ub)
            {
                if($b_key == 0)
                {
                    $batch_query    .= ' CONCAT(",",lo_override_batches,",") LIKE "%,'.$ub.',%"';
                }
                else
                {
                    $batch_query    .= ' OR CONCAT(",",lo_override_batches,",") LIKE "%,'.$ub.',%"';
                }
            }
        }
        
        $objects        = array();
        $objects['key'] = 'course_'.$course_id;
        $callback       = 'course_details';
        $params         = array('id' => $course_id);
        $course_details         = $this->memcache->get($objects, $callback, $params); 

        $this->load->model('Course_model');

        // echo '<pre>';print_r($grades);die;
        $assignment_details         = $this->Course_model->assignment_details(array('course_id'=>$course_id,'lecture_id'=>$lecture_id));
        $override_data              = $this->Course_model->lecute_override(array('course_id' => $course_id,'batch_query' => $batch_query,'lecture_type'=>'8','lecture_id'=>$lecture_id));
        $assignment_attempt_details = $this->Course_model->assignment_attempt_details(array('user_id'=>$user['id'],'course_id'=>$course_id,'lecture_id'=>$lecture_id));

        $assignment             = array();
        $section_id             = array();
        $assignment_section     = array();
        
        foreach($course_details['lectures'] as $lecture)
        {
            if($lecture_id == $lecture['id'])
            {
                $assignment     = $lecture;
                break;
            }
        }

        $assignment['attempt_data'] = array();
        
        $assignment['a_data']       = !empty($assignment_details)?$assignment_details:array();
        $assignment['o_data']       = array();
        $assignment['expired']      = false;

        if(isset($assignment['a_data']['dt_last_date']) && $assignment['a_data']['dt_last_date'] != '0000-00-00' && $assignment['a_data']['dt_last_date'] != '')
        {
            $startdate = $assignment['a_data']['dt_last_date'];
            $expire = strtotime($startdate);
            $today = strtotime(date('Y-m-d'));

            if($today > $expire){
                $assignment['expired']      = true;
            }
            $assignment['a_data']['dt_last_date']     = date('Y M j S',strtotime($assignment['a_data']['dt_last_date']));
        }else{
            $assignment['a_data']['dt_last_date'] = '';
        }

        if(!empty($override_data))
        {
            $assignment['a_data']['dt_last_date']       = $override_data['lo_end_date'];
            $startdate  = $override_data['lo_end_date'];
            $expire     = strtotime($startdate);
            $today      = strtotime(date('Y-m-d'));

            switch($override_data['lo_period_type']){
                case 1:
                    $expire     = $expire + ($override_data['lo_period']*(24*3600));
                break;
                case 2:
                    $expire     = $expire + ($override_data['lo_period']*3600);
                break;
                case 3:
                    $expire     = $expire + ($override_data['lo_period']*60);
                break;
            }

            if($today > $expire){
                $assignment['expired']      = true;
            }else{
                $assignment['expired']      = false;
            }
            $assignment['a_data']['dt_last_date']     = date('Y M j S',strtotime($assignment['a_data']['dt_last_date']));
            $override_data['lo_end_date']   = date('Y M j S',strtotime($override_data['lo_end_date']));
            $override_data['lo_duration']   = ($override_data['lo_duration']*60)<3600?gmdate('i:s',($override_data['lo_duration']*60)):gmdate('H:i:s',($override_data['lo_duration']*60));
            $assignment['o_data']           = $override_data;
        }

        $assignment['submitted']    = false;
        if(!empty($assignment_attempt_details))
        {
            $assignment['submitted']                        = true;
            $assignment_attempt_details['created_date']     = date('Y M j S',strtotime($assignment_attempt_details['created_date']));
            $assignment['submission']                       = $assignment_attempt_details;
        }
        
        $this->set_header(array('error' => false, 'message' => 'Assignment fetched successfully!'));
        $this->set_body($assignment); 
        $this->set_response();
    }

    private function create_document($param = array())
    {
        $response       = array();
        $course_id      = isset($param['course_id'])?$param['course_id']:false;
        $lecture_id     = isset($param['lecture_id'])?$param['lecture_id']:false;
        $certificate_id = isset($param['cl_filename'])?$param['cl_filename']:false;
        $has_s3         = $this->settings->setting('has_s3');

        if(!$course_id)
        {
            $this->set_header(array('error' => true,'code'=>601, 'message' => 'Invalid course id.'));
            $this->set_response();
        }

        if(!$lecture_id)
        {
            $this->set_header(array('error' => true,'code'=>601, 'message' => 'Invalid lecture id.'));
            $this->set_response();
        }

        if(!$certificate_id)
        {
            $this->set_header(array('error' => true,'code'=>601, 'message' => 'Invalid certificate id.'));
            $this->set_response();
        }

        $user                   = $this->__student;

        $this->load->library('PHPWord');    
        $course             = $this->Course_model->course(array('id' => $course_id));       

        $docroot                = FCPATH.certificate_upload_path().$user['id'].'_'.$course_id.'/';        

        $subscription_details   = $this->Course_model->subscription_details(array('select'=>'cs_percentage,cs_auto_grade,cs_manual_grade','course_id' => $course_id,'user_id' => $user['id']));
        // echo $this->db->last_query();exit;
        // $grade                  = ($subscription_details['cs_manual_grade'] == '-')?(($subscription_details['cs_auto_grade']!=null)?$subscription_details['cs_auto_grade']:'-'):$subscription_details['cs_manual_grade'];
        if(!$subscription_details['cs_manual_grade'] && $subscription_details['cs_manual_grade'] != '-')
        {
            $grade   = $subscription_details['cs_auto_grade'] ? $subscription_details['cs_auto_grade'] : '-' ;
        }
        else
        {
            $grade   = $subscription_details['cs_manual_grade'];
        }
        $certificate_param               = array();
        $certificate_param['id']         = $certificate_id;
        $certificate_param['select']     = 'cm_filename';
        $active_certificate              = $this->Course_model->get_certificate($certificate_param);

        if( $has_s3['as_superadmin_value'] && $has_s3['as_siteadmin_value'] )
        {
            if(!is_dir(certificate_upload_path()))
            {
                mkdir(certificate_upload_path(), 0777, true);
            }
            copy(certificate_path().$active_certificate['cm_filename'], certificate_upload_path().strstr( $active_certificate['cm_filename'] , '?' , TRUE ));
        }

        $certificate_filename_source     = explode('?', $active_certificate['cm_filename']);
        unset($certificate_filename_source[sizeof($certificate_filename_source)-1]);
        $certificate_filename_source     = implode(',', $certificate_filename_source);
        //echo FCPATH.certificate_upload_path().$certificate_filename_source; die;
        //$document                        = $this->phpword->loadTemplate(FCPATH.certificate_upload_path().$certificate_filename_source);     

        $document                        = $this->phpword->loadTemplate(FCPATH.certificate_upload_path().$active_certificate['cm_filename']);     
        $document->setValue('{Name}', ':'.' '.$user['us_name']);        
        $document->setValue('{Course_name}', ' '.$course['cb_title']); 
        $document->setValue('{Percentage}', ' '.$subscription_details['cs_percentage']); 
        $document->setValue('{Grade}', ' '.$grade); 
        $document->setValue('{dd-mm-yyyy}', ' '.date('d-m-Y'));      

        if (!file_exists($docroot))         
        {       
            mkdir($docroot, 0777, true);        
        }

        $document->save($docroot.$lecture_id.'_'.$user['id'].'.docx');        

        $user_id   = $this->Course_model->status_certificate_download(array('course_id' => $course_id,'user_id' => $user['id']));       
        $this->load->library('ofabeeconverter');        
        $file_name_with_path    = $docroot.$lecture_id.'_'.$user['id'].'.docx';       
        $file_name              = $this->ofabeeconverter->file_name($file_name_with_path);      

        $config                 = array();      
        $config['input']        = $file_name_with_path;     
        $config['output']       = $docroot;     
        $config['s3_upload']    = false;
        $this->ofabeeconverter->initialize_config($config);     
        $response = $this->ofabeeconverter->convert(); 

        $convertion_objects = $response['convertion_objects'];        
        $dimension          = shell_exec('identify -verbose  -format "%Wx%H" '.$convertion_objects['output'].' 2>&1');
        $dimension          = explode('x', $dimension);
        $xaxis              = $dimension[0] - 350;

        //generate QR Code
        $this->load->library('qrlib');
        $qr_param           = array();
        $qr_param['input']  = urldecode(site_url('content').'/'.$this->__download_certificate_method.'/'.$course_id.'/'.$lecture_id);
        $qr_code            = $this->qrlib->qrcode($qr_param);
        //End

        if($qr_code['error'] == false)
        {
            $param              = array();
            $param['xaxis']     = $xaxis;
            $param['yaxis']     = '5';
            $param['qr_code']   = FCPATH.qrcode_upload_path().$qr_code['data']['file_name'];
            $param['input']     = $convertion_objects['output'];
            $param['output']    = $convertion_objects['output'];
            $this->place_qr_code($param);                
        }

        
        if( $has_s3['as_superadmin_value'] && $has_s3['as_siteadmin_value'] )
        {
            $certificate_source     = FCPATH.certificate_upload_path().$user['id'].'_'.$course_id.'/'.$lecture_id.'_'.$user['id'].'/page.jpg';
            $certificate_detination = certificate_upload_path().$user['id'].'_'.$course_id.'/'.$lecture_id.'_'.$user['id'].'/page.jpg';
            uploadToS3($certificate_source, $certificate_detination);
        }

        $user_data                              = array();
        $user_data['user_id']                   = $this->__student['id'];
        $user_data['username']                  = $this->__student['us_name'];
        $user_data['useremail']                  = $this->__student['us_email'];
        $user_data['user_type']                 = $this->__student['us_role_id'];
        $user_data['phone_number']              = $this->__student['us_phone'];
        $message_template                       = array();
        $message_template['username']           = $this->__student['us_name'];
        $message_template['course_name']        = $course['cb_title'];
        $triggered_activity                     = 'course_certificate_generated';
        log_activity($triggered_activity, $user_data, $message_template);

        $this->set_body($param);
        $this->set_header(array('error' => false, 'message' => 'Certificate generated successfully!'));
        $this->set_response();
    }
    
    function place_qr_code($params = array())
    {
        $xaxis      = $params['xaxis'];
        $yaxis      = $params['yaxis'];
        $qr_code    = $params['qr_code'];
        $input      = $params['input'];
        $output     = $params['output'];
        shell_exec('composite -geometry "350x350 +'.$xaxis.'+'.$yaxis.'" '.$qr_code.' '.$input.' '.$output);
    }

    function download_certificate($param)      
    {               
        $response       = array();
        $course_id      = isset($param['course_id'])?$param['course_id']:false;
        $lecture_id     = isset($param['lecture_id'])?$param['lecture_id']:false;
        if(!$course_id)
        {
            $this->set_header(array('error' => true,'code'=>601, 'message' => 'Invalid course id.'));
            $this->set_response();
        }

        if(!$lecture_id)
        {
            $this->set_header(array('error' => true,'code'=>601, 'message' => 'Invalid lecture id.'));
            $this->set_response();
        }

        $user                   = $this->__student;

        $user_id            = $user['id'];
        $file_folder        = certificate_upload_path().$user_id.'_'.$course_id.'/'.$lecture_id.'_'.$user_id;
        $file               = $file_folder.'/page.jpg'; 
        $has_s3         = $this->settings->setting('has_s3');
        if( $has_s3['as_superadmin_value'] && $has_s3['as_siteadmin_value'] )
        {
            $certifcates['url'] = (is_dir($file_folder))?(certificate_path().$user_id.'_'.$course_id.'/'.$lecture_id.'_'.$user_id.'/page.jpg'):'';
            $certificate_file = certificate_upload_path().$user_id.'_'.$course_id.'/'.$lecture_id.'_'.$user_id.'/page.jpg';
            if(file_exists($certificate_file))
            {
                unlink($certificate_file);
            }
        }
        else
        {
            $certifcates['url'] = (file_exists($file))?base_url($file):'';
        }
        
        $this->set_header(array('error' => false, 'message' => 'Certificate url generated successfully!'));
        $this->set_body($certifcates);
        $this->set_response();     
    }

    private function print_certificate($param)      
    {              
        $response       = array();
        $course_id      = isset($param['course_id'])?$param['course_id']:false;
        $lecture_id     = isset($param['lecture_id'])?$param['lecture_id']:false;
        if(!$course_id)
        {
            $this->set_header(array('error' => true,'code'=>601, 'message' => 'Invalid course id.'));
            $this->set_response();
        }

        if(!$lecture_id)
        {
            $this->set_header(array('error' => true,'code'=>601, 'message' => 'Invalid lecture id.'));
            $this->set_response();
        }

        $user                   = $this->__student;

        $user_id            = $user['id'];
        $file               = certificate_upload_path().$user_id.'_'.$course_id.'/'.$lecture_id.'_'.$user_id.'/page.jpg'; 
        $certifcates['url'] = (file_exists($file))?base_url($file):'';
     
        $fp = fopen($certifcates['url'], 'rb');

        // send the right headers
        header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
        header('Expires: January 01, 2013'); // Date in the past
        header('Pragma: no-cache');
        header("Content-Type: image/jpg");

        // dump the picture and stop the script
        fpassthru($fp);
        exit;     
    }

    public function cdfa570a4072388b696cd320ad8b45589f87a2ca($course_id = 0, $lecture_id = 0)
    {
        if(empty($this->__student))
        {
            redirect(site_url('login'));exit;
        }
        $this->print_certificate(array('course_id'=> $course_id, 'lecture_id'=> $lecture_id));
    }

    public function get_user_data()
    {
        $response = array();
        $user     = $this->__student;
        if(empty($user))
        {
            $user = $this->auth->get_current_user_session('admin');
        }
        if(!empty($user))
        {
           $response['error']    = 'false';
           $this->load->library('JWT');
           $payload                     = array();
           $payload['id']               = $user['id'];
           $payload['email_id']         = $user['us_email'];
           $payload['register_number']  = '';
           $key                         = config_item('jwt_token');
           $token                       = $this->jwt->encode($payload, $key); 
           $user_data                                       = array();
           $user_data['token']                              = $token;
           $user_data['student']['id']                      = $user['id'];
           $user_data['student']['student_image']           = ($user['us_image'] == 'default.jpg') ? default_user_path() : user_path() . $user['us_image'];
           $user_data['student']['student_name']            = $user['us_name'];
           $user_data['student']['student_register_number'] = '';
           $user_data['student']['student_email_id']        = $user['us_email'];
           $response['data']     = $user_data; 
        } 
        else 
        {
            $response['error']    = 'true';
            $response['data']     = 'Invalid session'; 
        }
        echo json_encode($response);die();
    }

    public function forum_auth($course_id = 0)
    {
        $response = array();
        $user     = $this->__student;
        if(empty($user))
        {
            $user = $this->auth->get_current_user_session('admin');
        }
        if(!empty($user))
        {
            $response['error']              = false;
            $this->load->library('JWT');
            $permission                     = array('1','2','1','2');
            $payload                        = array();
            $payload['id']                  = $user['id'];
            if($user['us_role_id'] == 2)
            {
                $objects                = array();
                $objects['key']         = 'subscription_'.$course_id.'_'.$user['id'];
                $callback               = 'subscription_details';
                $params                 = array('user_id' => $user['id'],'course_id'=>$course_id);
                $subscription_details   = $this->memcache->get($objects, $callback,$params);
                if(isset($subscription_details['cs_forum_blocked']) && ($subscription_details['cs_forum_blocked']=='1'))
                {
                    $permission     = array('1','0','0','0');
                }
            }else
            {
                $this->__permission     = $this->accesspermission->get_permission(array(
                    'role_id' => $user['role_id'],
                    'module' => 'course_forum'
                ));
                $permission     = array('1','2','2','2');
                if(in_array('3', $this->__permission)){
                    $permission[1]  = '1';
                }
                if(in_array('2', $this->__permission)){
                    $permission[2]  = '1';
                }
                if(in_array('4', $this->__permission)){
                    $permission[3]  = '1';
                }
            }

            $objects        = array();
            $objects['key'] = 'course_'.$course_id;
            $callback       = 'course_details';
            $params         = array('id' => $course_id);
            $course_details                 = $this->memcache->get($objects, $callback,$params);

            $payload['permissions']         = implode('',$permission);
            $key                            = config_item('jwt_token');
            $token                          = $this->jwt->encode($payload, $key); 
            $user_data                      = array();
            $user_data['token']             = $token;
            $user_data['userId']            = $user['id'];
            $user_data['userImage']         = ($user['us_image'] == 'default.jpg') ? default_user_path() : user_path() . $user['us_image'];
            $user_data['userName']          = $user['us_name'];
            $user_data['userRole']          = $user['us_role_id'] == 2?0:$user['us_role_id'];
            $user_data['institute']         = $user['us_institute_id'];
            $user_data['permissions']       = $payload['permissions'];
            $user_data['blocked']           = false;
            $user_data['instructions']      = $course_details['cb_discussion_instruction'];
            if($user['us_role_id'] == 2){
                $user_data['blocked']       = $subscription_details['cs_forum_blocked']==1?true:false;
            }
            $response['data']               = $user_data; 
        } 
        else 
        {
            $response['error']    = true;
            $response['data']     = 'Invalid session'; 
        }
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, x-api-key ');
        echo json_encode($response);
    }

    public function forum_response() 
    {
        $this->load->model(array('User_model','Course_model'));
        $this->load->library('Notifier');
        $response               = array();
        $data                   = file_get_contents('php://input');
        $data                   = json_decode($data,true);
        $question               = $data['question'];

        $course                 = $this->Course_model->course(array('id' => $question['qCourse'],'select'=>'cb_title'));
        $course_name            = $course['cb_title'];

        $subscribed_users       = $this->Course_model->course_enrolled(array('course_id' => $question['qCourse'],'select'=>'cs_user_id'));

        $objects                = array();
        $objects['key']         = 'course_notification_' . $question['qCourse'];
        $callback               = 'course_notification';
        $params                 = array('course_id' => $question['qCourse']);
        $discussion_forum       = $this->memcache->get($objects, $callback, $params);

        $preveleged_users       = $discussion_forum['preveleged_users'];

        $user_id                = $data['loggedInUserId'];
        $triggered_action       = $data['action'];
        $user                   = $this->User_model->user(array('id' => $user_id,'select'=>'us_name,us_email,us_role_id'));
        
        //Notification
        if(!empty($subscribed_users))
        {
            $notification_ids       = array();
            
            foreach($subscribed_users as $subscribed_user)
            {
                $notification_ids[] = $subscribed_user['cs_user_id'];
            }
            if(array_key_exists('answer', $data))
            {
                $this->notifier->push(
                    array(
                        'action_code' => 'question_answered',
                        'assets' => array('course_name' => $course_name,'course_id' => $question['qCourse'],'question_name' => $question['qQuestion'],'user_name' => $data['name']),
                        'target' => $question['qCourse'],
                        'individual' => true,
                        'push_to' => $notification_ids
                    )
                );
            }
            else
            {
                $this->notifier->push(
                    array(
                        'action_code' => 'question_created',
                        'assets' => array('course_name' => $course_name,'course_id' => $question['qCourse'],'question_name' => $question['qQuestion'],'user_name' => $question['qByName']),
                        'target' => $question['qCourse'],
                        'individual' => true,
                        'push_to' => $notification_ids
                    )
                );
            }
        }
        if(!empty($preveleged_users))
        {
            $notify_to                             = array();
            foreach($preveleged_users as $preveleged_user)
            {
                $notify_to[$preveleged_user] = array($user_id);
            }
            if(array_key_exists('answer', $data))
            {
                $this->notifier->push(
                    array(
                        'action_code' => 'question_answered',
                        'assets' => array('course_name' => $course_name,'course_id' => $question['qCourse'],'question_name' => $question['qQuestion'],'user_name' => $data['name']),
                        'target' => $question['qCourse'],
                        'individual' => false,
                        'push_to' => $notify_to
                    )
                );
            }
            else
            {
                $this->notifier->push(
                    array(
                        'action_code' => 'question_created',
                        'assets' => array('course_name' => $course_name,'course_id' => $question['qCourse'],'question_name' => $question['qQuestion'],'user_name' => $question['qByName']),
                        'target' => $question['qCourse'],
                        'individual' => false,
                        'push_to' => $notify_to
                    )
                );
            }
        }
        //End Notification
        
        /*Log creation*/
        $user_data                              = array();
        $user_data['user_id']                   = $user_id;
        $user_data['username']                  = $user['us_name'];
        $user_data['user_type']                 = $user['us_role_id'];
        $user_data['useremail']                 = $user['us_email'];

        if($triggered_action == 'add_answer')
        {
            $message_template                       = array();
            $message_template['username']           = $data['name'];
            $message_template['post_name']          = $question['qQuestion'];
            $message_template['course_name']        = $course_name;
            $triggered_activity                     = 'question_answered';
        }
        else if($triggered_action == 'edit_question')
        {
            $message_template                       = array();
            $message_template['username']           = $question['qByName'];
            $message_template['post_name']          = $question['qQuestion'];
            $message_template['course_name']        = $course_name;
            $triggered_activity                     = 'question_updated';
        }
        else if($triggered_action == 'delete_question')
        {
            $message_template                       = array();
            $message_template['username']           = $question['qByName'];
            $message_template['post_name']          = $question['qQuestion'];
            $message_template['course_name']        = $course_name;
            $triggered_activity                     = 'question_deleted';
        }
        else if($triggered_action == 'report_question')
        {
            $message_template                       = array();
            $message_template['username']           = $question['qByName'];
            $message_template['post_name']          = $question['qQuestion'];
            $message_template['course_name']        = $course_name;
            $triggered_activity                     = 'question_report';
        }
        else
        {
            $message_template                       = array();
            $message_template['username']           = $question['qByName'];
            $message_template['post_name']          = $question['qQuestion'];
            $message_template['course_name']        = $course_name;
            $triggered_activity                     = 'question_added'; 
        }

        log_activity($triggered_activity, $user_data, $message_template);  
        $delete_key        = 'score_'.$user_id;
        $this->memcache->delete($delete_key);
        /*End log creation*/

        $response['error']      = false;
        $response['message']    = 'Success.';
        
        echo json_encode($response);
    }

    public function get_course_preview_time($param = array())
    {
        // Get param details
        $course_id              = $param['course_id'];
        $user_id                = $this->__student['id'];
        if(!$course_id)
        {
            $this->set_header(array('error' => true,'code'=>601, 'message' => 'Course id is missing'));
            $this->set_body();
            $this->set_response();
        }

        /* course preview time details */
        $objects                = array();
        $objects['key']         = 'course_'.$course_id;
        $callback               = 'course_details';
        $params                 = array('id' => $course_id);
        $course_details         = $this->memcache->get($objects, $callback,$params);     
        $course_preview_time    = $course_details['cb_preview_time'];  
        $course_preview         = ($course_details['cb_preview']!='')?$course_details['cb_preview']:0;     

        /* user preview time details */ 
        $get_user_time          = $this->Course_model->get_user_preview_time(array('course_id'=> $course_id, 'user_id'=> $user_id));        
        $user_preview_time      = isset($get_user_time['cpt_course_time'])?$get_user_time['cpt_course_time']:0;        
        
        $data                           = array();
        $data['course_preview']         = $course_preview;
        $data['course_preview_time']    = $course_preview_time;
        $data['user_preview_time']      = $user_preview_time;    
        $this->set_header(array('error' => false, 'message' => 'Preview time fetched successfully!'));
        $this->set_body($data);
        $this->set_response();     
    }

    public function save_course_preview_time($param = array())
    {
        // Get param details
        $course_id              = $param['course_id'];
        $user_id                = $this->__student['id'];
        if(!$course_id)
        {
            $this->set_header(array('error' => true,'code'=>601, 'message' => 'Course id is missing'));
            $this->set_body();
            $this->set_response();
        }

        /* course preview time details */
        $objects                = array();
        $objects['key']         = 'course_'.$course_id;
        $callback               = 'course_details';
        $params                 = array('id' => $course_id);
        $course_details         = $this->memcache->get($objects, $callback,$params);       
        $course_preview_time    = $course_details['cb_preview_time'];   
       
        /* user preview time details */  
        $get_user_time          = $this->Course_model->get_user_preview_time(array('course_id'=> $course_id, 'user_id'=> $user_id));        
        $user_preview_time      = isset($get_user_time['cpt_course_time'])?$get_user_time['cpt_course_time']:0;             
        
        /* set preview time data to save */
        if($get_user_time['id']) 
        {       
            $data['id']         = $get_user_time['id'];     
        }       
        $data['cpt_course_id']  = $course_id;              
        $data['cpt_user_id']    = $user_id;     
        $data['cpt_course_time']= ($user_preview_time == '0')?'5':($user_preview_time + 5);   
        $data['updated_date']   = date('Y-m-d H:i:s');
        $this->Course_model->save_remain_preview($data);        
        $this->set_header(array('error' => false, 'message' => 'Preview time saved successfully!'));
        $this->set_body($data);
        $this->set_response(); 
    }
    /*
    purpose     : To check the user logged in or not.
    params      : none 
    developer   : Lineesh
    edited      : none
    */
    public function is_logged_in()
    {
        
        // $header_req                             = $this->input->request_headers();
        // $raw_token                              = isset($header_req['Authorization'])?$header_req['Authorization']:'';
        // $token                                  = '';
        // if(!empty($raw_token))
        // {
        //     $extracted_token                    = explode(" ",$raw_token);
        //     $token                              = $extracted_token[1];
        // }
        // $logged_in                              = $this->auth->is_logged_in_user(false, false, 'user');
        // if($token == '' && !$logged_in)
        if(!empty($this->__student))
        {
            $this->set_header(array('error' => true,'code'=>200, 'message' => 'user logged in'));
            $this->set_body();
            $this->set_response();
        }
        else
        {
            $this->set_header(array('error' => true,'code'=>604, 'message' => 'Please login to continue the service'));
            $this->set_body();
            $this->set_response();
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
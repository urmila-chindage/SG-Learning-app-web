<?php

require APPPATH . 'third_party/REST_Controller.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class Mobile_api extends REST_Controller 
{ 
    public function __construct()
    {
        // ini_set('display_errors', 1);
        // ini_set('display_startup_errors', 1);
        // error_reporting(E_ALL);
        date_default_timezone_set('Asia/Kolkata');
        parent::__construct();
        $this->actions        = $this->config->item('actions');
        
        $headers                = $this->input->request_headers();
        $raw_token              = isset($headers['Authorization'])?$headers['Authorization']:'';
        $token                  = '';
        if(!empty($raw_token))
        {
            $extracted_token    = explode(" ",$raw_token);
            $token              = $extracted_token[1];
        }
        $input                  = file_get_contents('php://input');
        $decoded_input          = json_decode($input, true);
        $body                   = isset($decoded_input) ? $decoded_input : array();
        $method                 = $this->router->fetch_method();
        //list all functions here
        $this->__methods        = array();
        
        $http_methods                               = array();
        $http_methods['post']                       = array();
        $http_methods['get']                        = array();

        $http_methods['post']['login']              = 'login';
        $http_methods['post']['otp_verification']   = 'otp_verification';
        $http_methods['post']['save_categories']    = 'save_categories';
        $http_methods['post']['signup']             = 'signup';
        $http_methods['post']['home']               = 'home';
        $http_methods['post']['resend_otp']         = 'send_otp';
        $http_methods['post']['forgot_password']    = 'forgot_password';
        $http_methods['post']['edit_profile']       = 'edit_profile';
        $http_methods['post']['upload_user_image']  = 'upload_user_image';
        $http_methods['post']['more_items']         = 'more_items';
        $http_methods['post']['share_app']          = 'share_app';
        $http_methods['post']['delete_user']        = 'delete_user';
        $http_methods['post']['explore_courses']    = 'explore_courses';
        $http_methods['post']['course_info']        = 'course_info';
        $http_methods['post']['check_coupon']       = 'check_coupon';
        $http_methods['post']['payment_request']    = 'payment_request';
        $http_methods['post']['payment_response']   = 'payment_response';
        $http_methods['post']['free_enroll']        = 'free_course_subscription'; 
        $http_methods['post']['student_register_notification']          = 'student_register_notification';
        $http_methods['post']['course_subscription_notification']       = 'course_subscription_notification'; 
        $http_methods['post']['course_enrollment_notification']         = 'course_enrollment_notification'; 
        $http_methods['post']['bundle_enrollment_notification']         = 'bundle_enrollment_notification'; 
        $http_methods['post']['site_log_activity']  = 'site_log_activity';
        $http_methods['post']['is_logged_in']       = 'is_logged_in';

        $http_methods['get']['categories']          = 'categories';
        $http_methods['get']['current_location']    = 'current_location';
        $http_methods['get']['my_courses']          = 'my_courses';
        $http_methods['get']['profile']             = 'profile';
        $http_methods['get']['signout']             = 'signout';
        $http_methods['get']['terms']               = 'terms_condition';
        $http_methods['get']['reset_coupon']        = 'reset_coupon';
        $http_methods['get']['institutes']          = 'institutes';
        
        $http_method                                = $this->input->method();
        $this->__methods                            = $http_methods[$http_method];
        
        
       
        $this->__methods['instructor_info']     = 'instructor_info';
        $this->__methods['events']              = 'events';
        $this->__methods['notifications']       = 'notifications'; 
        $this->__methods['clear_notification']  = 'clear_notification'; 
        
        $this->__methods['more_events']         = 'more_events';
        $this->__methods['notify_new']          = 'notify_new';
        $this->__methods['delete_user']         = 'delete_user'; 

        $method       = isset($this->__methods[$method])?$this->__methods[$method]:'';
        if(!empty($method))
        {
            $method_param               = array();
            $method_param['token']      = $token;
            $method_param['body']       = $body;
            $this->$method($method_param);
        }
        else
        {
            $status_code    = '405';
            $headers        = array('error' => true,'status_code'=>$status_code,'message' => 'Invalid method');
            send_response($status_code ,$headers);
        }  
    }

    /*  
    purpose     : token verification and redirection to methods
    params      : method, body, token
    developer   : kiran
    edited      : none
    */
    public function index($params = array())
    {
        //include all method name which doesn't require session
        // $skip_login                = array('forgot_password','more_items','home','instructor_info','course_info','send_otp','login','categories', 'current_location','signup', 'otp_verification','delete_user');

        // $method                    = isset($params['method'])?$params['method']:'';
        // $body                      = isset($params['body'])?$params['body']:array();
        // $token                     = isset($params['token'])?$params['token']:'';
        // if(in_array($method, $skip_login))
        // {
        //    $this->$method($body);
        // }
        // else
        // {
        //     if(!empty($token))
        //     {

        //         $user_details = $this->api_authentication->verify_token($token);
        //         if($user_details['token_verified'] == true)
        //         {
        //             $user = !empty($user_details['user'])?$user_details['user']:array();
        //             $this->$method($body,$user);
        //         }
        //         else
        //         {
        //             $status_code    = '401';
        //             $headers        = array('error' => true,'status_code'=>$status_code,'message' => 'Please login to continue');
        //             send_response($status_code ,$headers);
        //         }
        //     }
        //     else
        //     {
        //         $status_code    = '401';
        //         $headers        = array('error' => true,'status_code'=>$status_code,'message' => 'Please login to continue');
                
        //         send_response($status_code ,$headers);
        //     }
        // }
    }

    /*
    purpose     : Category listing 
    params      : token
    developer   : kiran
    edited      : none
    performance : checked 
    */
    private function categories($params = array())
    {
        $user                       = array();
        $user_categories            = array();
        $categories                 = array();
        $token                      = isset($params['token'])?$params['token']:'';
        if($token != '')
        {
            $user_details           = $this->api_authentication->verify_token($token);
            if($user_details['token_verified'] == true)
            {
                $user               = !empty($user_details['user'])?$user_details['user']:array();
                $user_categories    = explode(",",$user['us_category_id']); 
            }
            else
            {
                $status_code        = '401';
                $headers            = array('error' => true,'status_code'=>$status_code,'message' => 'Please login to continue');
                send_response($status_code ,$headers);
            }
        }
        $objects['key']             = 'categories';
        $callback                   = 'get_categories';
        $all_categories             = $this->memcache->get($objects, $callback, array());
        if(!empty($all_categories))
        {
            foreach($all_categories as $category)
            {
                $category['selected']       = (in_array($category['id'],$user_categories))? true: false;
                array_push($categories,$category);
            }
        }

        $status_code        = '204';
        $headers            = array('error' => true, 'message' => 'No Categories.', 'status_code' => '404');
        $body               = array();
        if(!empty($categories))
        {
            $status_code    = '200';
            $headers        = array('error' => false, 'message' => 'Successfully Fetched.', 'status_code' => '200');
            $body           = $categories;
        }
        send_response($status_code ,$headers, $body);
    }

    /*
    purpose     : Save categories when user logged in
    params      : category_id , user_id
    developer   : kiran
    edited      : none
    performance : checked 
   */
    private function save_categories($param = array())
    {
        
        $category_id                = isset($param['body']['category_id'])?$param['body']['category_id']:'';
        $token                      = isset($param['token'])?$param['token']:'';
        if($token != '')
        {
            $user_details           = $this->api_authentication->verify_token($token);
            if($user_details['token_verified'] == true)
            {
                $user               = !empty($user_details['user'])?$user_details['user']:array();
                $user_id            = isset($user['id'])?$user['id']:'';
            }
            else
            {
                $status_code        = '401';
                $headers            = array('error' => true,'status_code'=>$status_code,'message' => 'Please login to continue');
                send_response($status_code ,$headers);
            }
        }
        else
        {
            $status_code            = '401';
            $headers                = array('error' => true,'status_code'=>$status_code,'message' => 'Please login to continue');
            send_response($status_code ,$headers);
        }
        

        if($category_id == '' )
        {
            $status_code            = '404';
            $headers                = array('error' => true, 'message' => 'Please select categories', 'status_code' => '404');
            send_response($status_code ,$headers);
        }
        if($user_id == '')
        {
            $status_code            = '404';
            $headers                = array('error' => true, 'message' => 'Failed to Update Category.', 'status_code' => '404');
            send_response($status_code ,$headers);
        }
        
        $data                       = array();
        $filter                     = array();
        $data['us_category_id']     = $category_id;
        $filter['update']           = true;
        $filter['id']               = $user_id;
        $this->load->model(array('User_model'));
        $category_status            = $this->User_model->save_userdata($data, $filter);

        $status_code                = '404';
        $headers                    = array('error' => true, 'message' => 'Failed to Update Category.', 'status_code' => '404');
        if($category_status)
        {
            $object_key             = 'userdetails_'.$user_id;
            $this->memcache->delete($object_key);
            $status_code            = '200';
            $headers                = array('error' => false, 'message' => 'Category Updated Successfully!', 'status_code' => '200');
        }
        send_response($status_code ,$headers);
    }

    /*
    purpose     : OTP verification
    params      : otp, phone, email, identifier(register or login), mode(mobile or email)
    developer   : kiran
    edited      : kiran(16/08)
   */
    private function otp_verification( $params = array() )
    {
        // $this->load->library('api_authentication');
        $name               = isset($params['body']['name'])?$params['body']['name']:'';
        $mobile             = isset($params['body']['phone'])?$params['body']['phone']:'';
        $country_code       = isset($params['body']['country_code'])?$params['body']['country_code']:'';
        $email              = isset($params['body']['email'])?$params['body']['email']:'';
        $password           = isset($params['body']['password'])?$params['body']['password']:'';
        $verification_otp   = isset($params['body']['otp'])?$params['body']['otp']:'';
        $mode               = isset($params['body']['mode'])?$params['body']['mode']:'';
        $identifier         = isset($params['body']['identifier'])?$params['body']['identifier']:'';
        
        if($verification_otp == '')
        {
            $status_code    = '401';
            $headers        = array('error' => true,'status_code'=> $status_code,'message' => 'OTP Missing');
            send_response($status_code ,$headers);
        }
        
        switch($mode)
        {
            case '1': 
                if(empty($mobile))
                {
                    $status_code    = '404';
                    $headers        = array('error' => true,'status_code'=> $status_code,'message' => 'Mobile number Missing');
                    send_response($status_code ,$headers);                   
                }
                $object_key         = $country_code.$mobile;
            break;
            case '2': 
            
                if(empty($email))
                {
                    $status_code    = '404';
                    $headers        = array('error' => true,'status_code'=> $status_code,'message' => 'Email Missing');
                    send_response($status_code ,$headers);                    
                }
                $object_key         = $email;
                
            break;
        }
        
        $objects                    = array();
        $objects['key']             = md5($object_key);
        $stored_otp                 = $this->memcache->get($objects);
        $body                       = array();

        if(empty($stored_otp))
        {
            $status_code    = '401';
            $headers        = array('error' => true,'status_code'=> $status_code,'message' => 'OTP timed out');
            send_response($status_code ,$headers);
        }
        if($verification_otp == $stored_otp['otp'])
        {
            switch($identifier)
            {
                case '1':
                    
                    if((!empty($mobile))||(!empty($email)))
                    {
                        $objects                = array();
                        $objects['key']         = 'institutes';
                        $callback               = 'institutes';
                        $institutes             = $this->memcache->get($objects, $callback, array());
                        $institute_id           = $institutes[0]['id'];

                        $branch_id              = '1';

                        $objects                = array();
                        $objects['key']         = 'branches';
                        $callback               = 'branches';
                        $branches               = $this->memcache->get($objects, $callback, array());
                        $branch_code            = isset($branches[$branch_id]['branch_code'])?$branches[$branch_id]['branch_code']:'';

                        $objects                = array();
                        $objects['key']         = 'institute_'.$institute_id;
                        $callback               = 'institute';
                        $institute              = $this->memcache->get($objects, $callback, array('id' => $institute_id ));                 
                        $institute_code         = isset($institute['ib_institute_code'])?$institute['ib_institute_code']:'';
                    
                        
                        $user_details           = array(
                                                        'us_name'            => $name,
                                                        'us_email'           => $email,
                                                        'us_password'        => sha1($password),
                                                        'us_phone'           => $mobile,
                                                        'us_country_code'    => $country_code,
                                                        'us_phone_verfified' => '1',
                                                        'us_email_verified'  => '0',
                                                        'us_role_id'         => '2',
                                                        'us_account_id'      => $this->config->item('id'),
                                                        'us_institute_id'    => $institute_id,
                                                        'us_branch'          => $branch_id,
                                                        'us_branch_code'     => $branch_code,
                                                        'us_institute_code'  => $institute_code,
                                                        'us_status'          => '1',
                                                        'us_email_exist'     => '1',
                                                        'updated_date'       => date('Y-m-d H:i:s')
                                                    );
                        $this->load->model(array('Authenticate_model', 'User_model'));
                        $new_user_id                    = $this->Authenticate_model->saveUserData($user_details);
                        if($new_user_id)
                        {
                            $this->api_authentication->process_img(array('user_id'=>$new_user_id));
                            $user_param                 = array();
                            $user_param['id']           = $new_user_id;
                            $user_param['us_name']      = $name;
                            $user_param['us_email']     = $email;
                            $user_param['us_phone']     = $mobile;

                            //Log activity starts here
                            $user_param['us_role_id']   = 2;
                            $user_param['activity']     = 'student_registered';
                            $this->site_log_activity($user_param);
                            //Log activity ends here

                            //verification mail starts here
                            $curl_param                 = array();
                            $curl_param['data']         = $user_param;
                            $curl_param['url']          = site_url()."mobile_api/student_register_notification";
                            $this->send_curl($curl_param);
                            //verification mail ends here
                        }

                        // $save_param                             = array();
                        // $filter_param                           = array();
                        // if($mode == '2')
                        // {
                        //     $filter_param['email']              = $email;
                        //     $save_param['us_email_verified']    = '1';
                        // $filter_param['update']                 = true;
                        // $this->User_model->save_userdata($save_param,$filter_param);
                        // }
                        
                    }
                    //Registration
                    $token_params               = array();
                    $token_params['email']      = $email;
                    $token_response             = $this->api_authentication->create_token($token_params);
                    if(!empty($token_response))
                    {
                        $body                   = array();
                        $body['token']          = $token_response['token'];
                        $body['user']           = $this->get_user_data($token_response['user']);
                    }
                    
                break;
                case '2':
                    $change_params              = array();
                    $change_params['email']     = $email;
                    $change_params['password']  = $password;
                    $this->reset_password($change_params);
                break;
                    
            }
            $this->memcache->delete(md5($object_key));
            $status_code    = '200';
            $headers        = array('error' => false,'status_code'=> $status_code,'message' => 'OTP Verified Successfully!');
        } 
        else
        {
            $status_code    = '401';
            $headers        = array('error' => true,'status_code'=> $status_code,'message' => 'Invalid OTP.');
        }
        send_response($status_code ,$headers,$body);
        
    }

    /*
    purpose     : send email verification link to the registered user
    params      : none
    developer   : lineesh
    edited      : none
   */
    private function student_register_notification()
    {
        $request                        = file_get_contents('php://input');
        $request                        = json_decode($request, true);
        $new_user_id                    = $request['id'];
        $user_name                      = $request['us_name'];
        $user_email                     = $request['us_email'];
        $user_phone                     = $request['us_phone'];
        
        $email_token                    = md5(openssl_random_pseudo_bytes(64));
        //Save token for verification 
        $token                          = array();
        $token['et_user_id']            = $new_user_id;
        $token['et_user_email']         = $user_email;
        $token['et_account_id']         = config_item('id');
        $token['et_token']              = $email_token;
        $token['et_status']             = '1';
        $this->load->model(array('User_model'));
        $this->User_model->save_token($token);
        //End saving token 
        $template                       = $this->ofabeemailer->template(array('email_code' => 'registration_mail'));
        $param                          = array();
        $param['to'] 	                = array($user_email); 
        // $param['subject'] 	                = $template['em_subject'];
        $contents                       = array(
                                                'user_name' => $user_name
                                                ,'site_name' => config_item('site_name')
                                                ,'verification_link' => site_url('register/verify/'.$email_token)
                                                );
        $param['subject']                   = $this->ofabeemailer->process_mail_content($contents, $template['em_subject']);
        $param['body']                      = $this->ofabeemailer->process_mail_content($contents, $template['em_message']);
        $this->ofabeemailer->send_mail($param);
    }

     /*
    purpose     : send email when subscribe the course
    params      : none
    developer   : lineesh
    edited      : none
   */
    private function course_subscription_notification()
    {
        $request                        = file_get_contents('php://input');
        $request                        = json_decode($request, true);
        $ids                            = $request['ids'];
        $user_name                      = $request['user_name'];
        $course_name                    = $request['course_name'];
        $course_id                      = $request['course_id'];

        $template                       = $this->ofabeemailer->template(array('email_code' => 'approve_enrollment'));
        $param_admin                    = array();
        $param_admin['to'] 	            = $ids;
        $param_admin['subject']         = $template['em_subject'];
        $contents                       = array(
                                            'student_name' => $user_name,
                                            'course_name'=> $course_name
                                            ,'site_name' => config_item('site_name')
                                            ,'approval_link' => admin_url('course/users').$course_id.'?&filter=suspended&offset=1'
                                        );
        $param_admin['body']    = $this->ofabeemailer->process_mail_content($contents, $template['em_message']);
        $this->ofabeemailer->send_mail($param_admin);
    }

    /*
    purpose     : send email when enroll the course
    params      : none
    developer   : lineesh
    edited      : none
   */
  private function course_enrollment_notification()
  {
      $request                        = file_get_contents('php://input');
      $request                        = json_decode($request, true);
      $ids                            = $request['ids'];
      $user_name                      = $request['user_name'];
      $course_name                    = $request['course_name'];

      $template                       = $this->ofabeemailer->template(array('email_code' => 'approve_enrollment'));
      $param_admin                    = array();
      $param_admin['to']              = $ids;
      $param_admin['subject']         = $template['em_subject'];
      $contents                        = array(
                                            'student_name' => $user_name,
                                            'course_name'=> $course_name
                                            ,'site_name' => config_item('site_name')
                                        );
      $param_admin['body']            = $this->ofabeemailer->process_mail_content($contents, $template['em_message']);
      $this->ofabeemailer->send_mail($param_admin);
  }

   /*
    purpose     : send email when enroll the bundle
    params      : none
    developer   : lineesh
    edited      : none
   */
  private function bundle_enrollment_notification()
  {
      $request                        = file_get_contents('php://input');
      $request                        = json_decode($request, true);
      $ids                            = $request['ids'];
      $user_name                      = $request['user_name'];
      $bundle_name                    = $request['bundle_name'];

      $template                       = $this->ofabeemailer->template(array('email_code' => 'bundle_enrollment'));
      $param_admin                    = array();
      $param_admin['to']              = $ids;
      $param_admin['subject']         = $template['em_subject'];
      $contents                       = array(
                                            'student_name' => $user_name,
                                            'bundle_name'=> $bundle_name
                                            ,'site_name' => config_item('site_name'));
      $param_admin['body']            = $this->ofabeemailer->process_mail_content($contents, $template['em_message']);
      $this->ofabeemailer->send_mail($param_admin);
  }

    /*
    purpose     : To do the log activities
    params      : user params
    developer   : lineesh
    edited      : none
   */
    private function site_log_activity( $param = array() )
    {
        $user_id                        = isset($param['id'])?$param['id']:'';
        $user_name                      = isset($param['us_name'])?$param['us_name']:'';
        $user_email                     = isset($param['us_email'])?$param['us_email']:'';
        $user_phone                     = isset($param['us_phone'])?$param['us_phone']:'';
        $user_role                      = isset($param['us_role_id'])?$param['us_role_id']:'';
        $activity                       = isset($param['activity'])?$param['activity']:'';
        $course_name                    = isset($param['course_name'])?$param['course_name']:'';
        $bundle_name                    = isset($param['bundle_name'])?$param['bundle_name']:'';

        /*Log creation*/
        $user_data                      = array();
        $user_data['user_id']           = $user_id;
        $user_data['username']          = $user_name;
        $user_data['useremail']         = $user_email;
        $user_data['user_type']         = $user_role;
        $user_data['phone_number']      = $user_phone;
        $message_template               = array();
        $message_template['username']   = $user_name;
        if(isset($course_name))
        {
            $message_template['course_name']    = $course_name;
        }
        if(isset($bundle_name))
        {
            $message_template['bundle_name']    = $bundle_name;
        }
        $triggered_activity             = $activity;
        log_activity($triggered_activity, $user_data, $message_template);
    }

    /*
    purpose     : access country code of requested IP 
    params      : none
    developer   : lineesh
    edited      : none
   */
    private function current_location()
    {
        $this->load->model(array('Settings_model'));
        $this->__status_code            = '404';
        $headers                        = array('error' => true, 'message' => 'Error to find user location.', 'status_code' => '404');
        $body                           = array();
        $ip                             = getenv('HTTP_X_FORWARDED_FOR');
        $location_details               = json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip), true);
        $mobile_settings                = $this->Settings_model->get_mobile_settings();
        if($location_details['geoplugin_countryCode'] != '')
        {
            $this->__status_code        = '200';
            $headers                    = array('error' => false, 'message' => 'Location details fetched successfully!', 'status_code' => '200');
            $body['location_name']      = $location_details['geoplugin_countryCode'];
            $body['currency_unicode']   = 'U+20B9';
            foreach($mobile_settings as $mobile_setting)
            {
            $body[$mobile_setting['ms_device']] = array('version' => $mobile_setting['ms_version'], 'force_update' => $mobile_setting['ms_force_update'], 'description' => $mobile_setting['ms_description'], 'app_url' => $mobile_setting['ms_app_url']);
            }
            // $body['regex']              = "^[6-9]\d{9}$"; 
        }
        send_response($this->__status_code ,$headers, $body);
    }

    /*
    purpose     : add new user to the system
    params      : name, phone, email, password
    developer   : lineesh
    edited      : kiran(16/08)
   */
    private function signup( $param = array() )
    {
        $headers                        = array();
        $body                           = array();
        $error_count                    = 0;
        $error_message                  = array();
        $name                           = isset($param['body']['name'])?$param['body']['name']:'';
        $phone                          = isset($param['body']['phone'])?$param['body']['phone']:'';
        $country_code                   = isset($param['body']['country_code'])?$param['body']['country_code']:'';
        $email                          = isset($param['body']['email'])?$param['body']['email']:'';
        $password                       = isset($param['body']['password'])?$param['body']['password']:'';
        $hash_key                       = isset($param['body']['hash_key'])?$param['body']['hash_key']:'';
        if($name == '')
        {
            $error_count                = 1;
            $error_message[]            = 'Name Required.';
        }
        if($email == '')
        {
            $error_count                = 1;
            $error_message[]            = 'Email Required';
        }
        if($password == '')
        {
            $error_count                = 1;
            $error_message[]            = 'Password Required';
        }
        if($password != '' && strlen(trim($password)) < '6')
        {
            $error_count                = 1;
            $error_message[]            = 'Password Should be minimum six characters.';
        }
        if($error_count > 0)
        {
            $this->__status_code        = '404';
            $headers                    = array('error' => true, 'message' => $error_message, 'status_code' => '404');
            send_response($this->__status_code ,$headers);
        }
        $this->load->model(array('Authenticate_model'));
        if(!empty($phone))
        {
            $check_param                = array();
            $check_param['mobile']      = $phone;
            $check_param['email']       = $email;
            $user_exist                 = $this->Authenticate_model->get_user_by_field($check_param);
            if($user_exist > 0)
            {
                $this->__status_code    = '401';
                $headers                = array('error' => true, 'message' => 'Given mobile or email already exists.', 'status_code' => '401');
                send_response($this->__status_code ,$headers);
            }
        }
        
        if(!empty($phone))
        {
            $phone                      = $country_code.$phone;
            $otp_param                  = array();
            $otp_param['phone']         = $phone;
            $otp_param['mode']          = '1';
            $otp_param['hash_key']      = $hash_key;

            $curl_param                 = array();
            $curl_param['data']         = $otp_param;
            $curl_param['url']          = site_url()."mobile_api/resend_otp";
            $this->send_curl($curl_param);
            
            $status_code                = '200';
            $headers                    = array('error' => false,'status_code'=> $status_code,'message' => 'OTP send to your registered mobile.');
        }
        
        else
        {
            $status_code                = '404';
            $headers                    = array('error' => true, 'message' => 'Error in register.', 'status_code' => '404');
        }
        send_response($status_code ,$headers, $body);
    }

    private function send_curl($params = array())
    {
        $url                = isset($params['url'])?$params['url']:site_url();
        $data               = isset($params['data'])?$params['data']:'';

        $curlHandle         = curl_init($url);
        $defaultOptions     = array (
                                CURLOPT_POST => 1,
                                CURLOPT_POSTFIELDS => json_encode($data),
                                CURLOPT_RETURNTRANSFER => false ,
                                CURLOPT_TIMEOUT_MS => 100,
                             );
        curl_setopt_array($curlHandle , $defaultOptions);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, FALSE);     
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2); 
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array(
            'request-token: '.sha1(config_item('acct_domain').config_item('id')),
        ));
        
        $result = curl_exec($curlHandle);
        curl_close($curlHandle);

    }
    /*
    purpose     : user login to system
    params      : mode(mobile or email), phone, email, password
    developer   : lineesh
    edited      : kiran(13/08,16/08)
    */
    private function login( $param = array() )
    {
        
        $headers                        = array();
        $body                           = array();
        $mode                           = isset($param['body']['mode'])?$param['body']['mode']:'1';
        $phone                          = isset($param['body']['phone'])?$param['body']['phone']:'';
        $email                          = isset($param['body']['email'])?$param['body']['email']:'';
        $password                       = isset($param['body']['password'])?$param['body']['password']:'';
        $remember                       = true;
        $error_count                    = 0;
        $error_message                  = array();

        if($mode == '1')
        {
            if($phone == '')
            {
                $error_count            = 1;
                $error_message[]        = 'Phone Number Required';
            }
            if($password == '')
            {
                $error_count            = 1;
                $error_message[]        = 'Password Required';
            }
        }
        if($mode == '2')
        {
            if($email == '')
            {
                $error_count            = 1;
                $error_message[]        = 'Email Required';
            }
            if($password == '')
            {
                $error_count            = 1;
                $error_message[]        = 'Password Required';
            }
        }
        if($error_count > 0)
        {
            $this->__status_code        = '404';
            $headers                    = array('error' => true, 'message' => $error_message, 'status_code' => '404');
            send_response($this->__status_code ,$headers);
        }

        if($mode == '1' && !empty($phone)&& !empty($password))
        {
            $username   = $phone;
        }
        else if($mode == '2' && !empty($email) && !empty($password))
        {
            $username   = $email;
        }
        $this->load->model(array('Authenticate_model'));
        $login_user	    = $this->Authenticate_model->login_user($username, $password);
        if(sizeof($login_user) > 0)
        {
            if($login_user['us_deleted'] == '0' && $login_user['us_status'] == '1')
            {
                $logged_user_data                   = array();
                $logged_user_data['id']             = $login_user['id'];
                $logged_user_data['us_session_id']  = session_id();
                $this->Authenticate_model->save_fb_data($logged_user_data);

                $token_params                       = array();
                if($mode == '1')
                {
                    $token_params['phone']          = $phone;
                }
                else if($mode == '2')
                {
                    $token_params['email']          = $email;
                }
                $this->load->model(array('User_model'));
                $token_response                     = $this->api_authentication->create_token($token_params);
                if(!empty($token_response))
                {
                    /*Log creation*/
                    $login_user['activity']         = 'user_login';
                    $this->site_log_activity($login_user);
                }
                $this->__status_code                = '200';
                $headers                            = array('error' => false, 'message' => 'Login successfully!', 'status_code' => '200');
                $body                               = array();
                $body['token']                      = $token_response['token'];
                $body['user']                       = $this->get_user_data($token_response['user']);
                send_response($this->__status_code ,$headers, $body);
            }
            else if($login_user['us_status'] != '1')
            {
                $this->__status_code        = '401';
                $headers                    = array('error' => true, 'message' => 'Account temporarily blocked.Contact Admin ', 'status_code' => '401');
                send_response($this->__status_code ,$headers);
            }
            else if($login_user['us_deleted'] != '0')
            {
                $this->__status_code        = '401';
                $headers                    = array('error' => true, 'message' => 'User does not exist anymore.', 'status_code' => '401');
                send_response($this->__status_code ,$headers);
            }
            
        }
        else
        {
            $this->__status_code        = '401';
            $headers                    = array('error' => true, 'message' => 'Unauthorised to Login.', 'status_code' => '401');
            send_response($this->__status_code ,$headers);
        }
            
        // }
    }

    /*
    purpose     : supporting function to filter user data
    params      : userdata
    developer   : kiran
    edited      : none
    */
    private function get_user_data($userdata = array())
    {
        $response_user                          = array();
        $response_user['id']                    = $userdata['id'];
        $response_user['us_name']               = empty($userdata['us_name'])?"":$userdata['us_name'];
        $response_user['us_email']              = empty($userdata['us_email'])?"":$userdata['us_email'];       
        $response_user['us_image']              = (($userdata['us_image'] == 'default.jpg')?default_user_path():user_path()).$userdata['us_image'];
        $response_user['us_about']              = empty($userdata['us_about'])?"":$userdata['us_about'];
        $response_user['us_phone']              = empty($userdata['us_phone'])?"":isset($userdata['us_country_code'])?$userdata['us_country_code']." ".$userdata['us_phone']:$userdata['us_phone'];
        $response_user['us_phone_verfified']    = $userdata['us_phone_verfified'];
        $response_user['us_email_verified']     = $userdata['us_email_verified'];
        $response_user['us_role_id']            = $userdata['us_role_id'];
        $response_user['us_category_id']        = empty($userdata['us_category_id'])?"":$userdata['us_category_id'];
        $response_user['us_status']             = empty($userdata['us_status'])?"":$userdata['us_status'];
        return $response_user;
    }

    /*
    purpose     : home page view includes popular and featured courses,online tests
    params      : category_ids, search_item, offset, limit
    developer   : kiran
    edited      : none
   */
    private function home($param = array())
    {
        $this->load->model(array('Course_model','Bundle_model'));
        $response                   = array();
        $response['courses']        = array();
        $response['banners']        = array();
        $enrolled_item_ids          = array();
        $offset                     = 0;
        $limit                      = 5;
        $no_content                 = 0;
        $token                      = isset($param['token'])?$param['token']:'';
        $user_category_ids          = '';
        if($token != '')
        {
            $user_details           = $this->api_authentication->verify_token($token);
            if($user_details['token_verified'] == true)
            {
                $user               = !empty($user_details['user'])?$user_details['user']:array();
                $user_category_ids  = $user['us_category_id'];
                $user_id            = $user['id'];
                $objects['key']     = 'enrolled_item_ids_'.$user_id;
                $callback           = 'enrolled_item_ids';
                $params             = array('user_id' => $user_id);
                $enrolled_item_ids  = $this->memcache->get($objects, $callback, $params);
                
            }
            else
            {
                $status_code        = '401';
                $headers            = array('error' => true,'status_code'=>$status_code,'message' => 'Please login to continue');
                send_response($status_code ,$headers);
            }
        }
        $category_ids               = empty($param['body']['category_ids'])?$user_category_ids:$param['body']['category_ids'];
        $search_item                = isset($param['body']['search_item'])?$param['body']['search_item']:'';
        $objects['key']             = 'popular_courses';
        $callback                   = 'popular_courses';
        $popular_courses            = $this->memcache->get($objects, $callback);
        
        if(!empty($popular_courses))
        {
            $popular_courses      = $this->check_if_enrolled($popular_courses,$enrolled_item_ids);
            if(!empty($search_item))
            {
                $popular_courses        = search_item($search_item,'item_name',$popular_courses);
            }
            
            if(!empty($category_ids))
            {
                $popular_courses        = category_search($category_ids,'item_search',$popular_courses);
            }
        }
        
        if(count($popular_courses) == 0)
        {
            $no_content ++;
        }
        if(count($popular_courses) > 0)
        {
            $data                           = array();
            $data['title']                  = 'Popular Course'; 
            $data['identifier']             = '1'; 
            $data['total_records']          = count($popular_courses);
            $data['list']                   = array_slice($popular_courses,$offset,$limit);
            array_push($response['courses'],$data);
        }
        
        $objects['key']                 = 'featured_courses';
        $callback                       = 'featured_courses';
        $featured_courses               = $this->memcache->get($objects, $callback);
        if(!empty($featured_courses))
        {
            $featured_courses           = $this->check_if_enrolled($featured_courses,$enrolled_item_ids);
            if(!empty($search_item))
            {
                $featured_courses       = search_item($search_item,'item_name',$featured_courses);
            }
            
            if(!empty($category_ids))
            {
                $featured_courses       = category_search($category_ids,'item_search',$featured_courses);
            }
        }
        if(count($featured_courses) == 0)
        {
            $no_content ++;
        }
        if(count($featured_courses) > 0)
        {
            $data                           = array();
            $data['title']                  = 'Featured Course'; 
            $data['identifier']             = '2'; 
            $data['total_records']          = count($featured_courses);
            $data['list']                   = array_slice($featured_courses,$offset,$limit);
            array_push($response['courses'],$data);
        }

        $banner_data_response           = array();
        $this->load->model(array('User_model'));
        $banners                        = $this->User_model->get_all_banners();
        if(!empty($banners))
        {
            foreach($banners as $banner)
            {
                $banner['mb_title']    = mobile_banner_path().$banner['mb_converted_title'];
                array_push($banner_data_response ,$banner);
            }
        }
        $data                           = array();
        $data['title']                  = 'Banners'; 
        $data['identifier']             = '3'; 
        $data['total_records']          = count($banner_data_response);
        $data['list']                   = array_slice($banner_data_response,$offset,$limit,true);
        $response['banners']            = $data;

        $status_code                    = '200';
        $headers                        = array('error' => false,'status_code'=> $status_code,'message' => 'data fetched successfully');
        if($no_content > 1)
        {
            $status_code                = '200'; 
            $headers                    = array('error' => false,'status_code'=> $status_code,'message' => 'No data available');
        }
        $body                           = $response;
        send_response($status_code ,$headers ,$body);
    }

    /*
    purpose     : forgot password
    params      : email, mode(email)
    developer   : kiran
    edited      : none
   */
    private function forgot_password($params)
    {
        $email                      = isset($params['body']['email'])?$params['body']['email']:'';
        $hash_key                   = isset($params['body']['hash_key'])?$params['body']['hash_key']:'';
        $filter_param               = array();
        if(empty($email))
        {
            $status_code            = '404';
            $headers                = array('error' => true,'status_code'=> $status_code,'message' => 'Please provide email');
            send_response($status_code ,$headers);
        }
        $filter_param['email']      = $email;
        $this->load->model(array('User_model'));
        $check_status               = $this->User_model->check_user_exist($filter_param);
        if(!empty($check_status))
        {
            $otp_params['body']             = array();
            $otp_params['body']['email']    = $email;
            $otp_params['body']['mode']     = '2';
            $otp_params['body']['hash_key'] = $hash_key;
            $this->send_otp($otp_params);
        }
        else
        {
            $status_code            = '404';
            $headers                = array('error' => true,'status_code'=> $status_code,'message' => 'User does not exist');
            send_response($status_code ,$headers);
        }

    }
    

    /*
    purpose     : Sending OTP for mobile and email
    params      : phone, email, mode(mobile or email)
    developer   : kiran
    edited      : none
   */
    private function send_otp($params = array())
    {
        $mobile             = isset($params['body']['phone'])?$params['body']['phone']:'';
        $email              = isset($params['body']['email'])?$params['body']['email']:'';
        $mode               = isset($params['body']['mode'])?$params['body']['mode']:'';
        $hash_key           = isset($params['body']['hash_key'])?$params['body']['hash_key']:'';
        
        $status_code        = '404';
        $headers            = array('error' => true,'status_code'=> $status_code,'message' => 'OTP failed to send');
        $generated_otp      = generate_otp();
        
        if($generated_otp)
        {
            switch($mode)
            {
                case '1':   

                    if(empty($mobile))
                    {
                        $status_code            = '404';
                        $headers                = array('error' => true,'status_code'=> $status_code,'message' => 'Please provide mobile number');
                        send_response($status_code ,$headers);
                    }
                    
                    $status_code            = '200';
                    $headers                = array('error' => false,'status_code'=> $status_code,'message' => 'OTP send to your registered mobile.');
                
                    $sms_param              = array();
                    $sms_param['phone']     = $mobile;
                    $sms_param['otp']       = $generated_otp;
                    $sms_param['hash_key']  = $hash_key; 

                    $key                    = md5($mobile);
                    $content['otp']         = $generated_otp;
                    $expiry                 = '300';
                    $this->memcache->set($key, $content, $expiry);
                    send_sms($sms_param);
                    
                break;
                case '2':
                    if(empty($email))
                    {
                        $status_code            = '404';
                        $headers                = array('error' => true,'status_code'=> $status_code,'message' => 'Please provide email');
                        send_response($status_code ,$headers);
                    }
                    
                    $status_code            = '200';
                    $headers                = array('error' => false,'status_code'=> $status_code,'message' => 'OTP send to your registered email.');

                    $mail_param             = array();
                    $mail_param['email']    = $email;
                    $mail_param['otp']      = $generated_otp;

                    $key                    = md5($email);
                    $content['otp']         = $generated_otp;
                    $expiry                 = '300';
                    $this->memcache->set($key, $content, $expiry);
                    send_email($mail_param);
                    
                    
                break;
            }
            
        }
        
        send_response($status_code ,$headers);
    }

    /*
    purpose     : save new password
    params      : email , password
    developer   : kiran
    edited      : none
   */
    private function reset_password($params = array())
    {
        
        $status_code    = '404';
        $headers        = array('error' => true,'status_code'=> $status_code,'message' => 'failed to reset password');
        $email          = isset($params['email'])? $params['email']:'';
        $password       = isset($params['password'])? $params['password']:'';
        // $token          = isset($params['token'])?$params['token']:'';
        // if($token != '')
        // {
        //     $user_details           = $this->api_authentication->verify_token($token);
        //     if($user_details['token_verified'] == true)
        //     {
        //         $user               = !empty($user_details['user'])?$user_details['user']:array();
        //     }
        //     else
        //     {
        //         $status_code        = '401';
        //         $headers            = array('error' => true,'status_code'=>$status_code,'message' => 'Please login to continue');
        //         send_response($status_code ,$headers);
        //     }
        // }
        // else
        // {
        //     $status_code            = '401';
        //     $headers                = array('error' => true,'status_code'=>$status_code,'message' => 'Please login to continue');
        //     send_response($status_code ,$headers);
        // }
        if($email == '')
        {
            $status_code    = '404';
            $headers        = array('error' => true, 'message' => 'Email Required', 'status_code' => '404');
            send_response($status_code ,$headers);
        }
        if($password == '')
        {
            $status_code    = '404';
            $headers        = array('error' => true, 'message' => 'Password Required', 'status_code' => '404');
            send_response($status_code ,$headers);
        }
        $filter_param                   = array();
        $filter_param['email']          = $email;
        $save_userdata                  = array();
        $save_userdata['us_password']   = sha1($password);
        $this->load->model(array('User_model'));
        $changed_status                 = $this->User_model->update_password($save_userdata,$filter_param);
        if($changed_status)
        {
            $status_code                        = '200';
            $headers                            = array('error' => false,'status_code'=> $status_code,'message' => 'password changed');
        }
        if($changed_status && !empty($user))
        {
            /*Log creation*/
            $user['activity']                   = 'student_password_changed';
            $this->site_log_activity($user);
        }
        $object_key                 = $email;
        $objects                    = array();
        $objects['key']             = md5($object_key);
        $this->memcache->delete($object_key);
        send_response($status_code ,$headers);
    }

    /*
    purpose     : log out from system
    params      : user_id
    developer   : lineesh
    edited      : none
   */
    public function signout($param = array() )
    {
        $headers                    = array();
        $body                       = array();
        $token                      = isset($param['token'])?$param['token']:'';
        if($token != '')
        {
            $user_details           = $this->api_authentication->verify_token($token);
            if($user_details['token_verified'] == true)
            {
                $user               = !empty($user_details['user'])?$user_details['user']:array();
            }
            else
            {
                $status_code        = '401';
                $headers            = array('error' => true,'status_code'=>$status_code,'message' => 'Please login to continue');
                send_response($status_code ,$headers);
            }
        }
        else
        {
            $status_code            = '401';
            $headers                = array('error' => true,'status_code'=>$status_code,'message' => 'Please login to continue');
            send_response($status_code ,$headers);
        }
        $user_id                        = isset($user['id'])?$user['id']:'';
        if($user_id == '')
        {
            $this->__status_code        = '204';
            $headers                    = array('error' => true, 'message' => 'This User not available right now.', 'status_code' => '404');
            send_response($this->__status_code ,$headers);
        }
        $user_params                    = array('id' => $user_id, 'us_token' => '');
        $this->load->model(array('User_model'));
        $this->User_model->save($user_params);
        $this->memcache->delete('userdetails_'.$user_id);
        $this->__status_code            = '200';
        $headers                        = array('error' => false, 'message' => 'User Logged out successfully!', 'status_code' => '200');
        send_response($this->__status_code ,$headers);
    }

    /*
    purpose     : view user profile including events list
    params      : user_id, month, year
    developer   : lineesh
    edited      : none
   */
    private function profile( $param = array() )
    {
        
        $token                      = isset($param['token'])?$param['token']:'';
        if($token != '')
        {
            $user_details           = $this->api_authentication->verify_token($token);
            if($user_details['token_verified'] == true)
            {
                $user               = !empty($user_details['user'])?$user_details['user']:array();
            }
            else
            {
                $status_code        = '401';
                $headers            = array('error' => true,'status_code'=>$status_code,'message' => 'Please login to continue');
                send_response($status_code ,$headers);
            }
        }
        else
        {
            $status_code            = '401';
            $headers                = array('error' => true,'status_code'=>$status_code,'message' => 'Please login to continue');
            send_response($status_code ,$headers);
        }
        $headers                        = array();
        $body                           = array();
        $user_id                        = isset($user['id'])?$user['id']:'';
        $user_params                    = 'users.id, users.us_name, users.us_email, users.us_image, users.us_phone,users.us_category_id';
        $this->load->model(array('User_model'));
        $user                           = $this->User_model->get_user_details(array('id' => $user_id, 'select' => $user_params));
        
        $objects                        = array();
        $objects['key']                 = 'score_' . $user_id;
        $callback                       = 'my_score';
        $params                         = array('user_id' => $user_id);
        $score                          = $this->memcache->get($objects, $callback, $params);
        $user['score']                  = $score['lap_user_points'];
        $user['us_category_id']         = empty($user['us_category_id'])?'':$user['us_category_id'];
        $user['us_image']               = (($user['us_image'] == 'default.jpg')?default_user_path():user_path()).$user['us_image'];

        $objects                        = array();
        $objects['key']                 = 'enrolled_' . $user_id;
        $callback                       = 'my_subscriptions';
        $params                         = array('user_id' => $user_id);
        $enrolled                       = $this->memcache->get($objects, $callback, $params);

        $grade                          = array();
        $grade['total']                 = 0;
        $grade['count']                 = 0;
        foreach ($enrolled as $e_key => $enroll) 
        {
            $grade['count']             = $grade['count']+1;
            $grade['total']            += $enroll['cs_percentage'];
        }

        $percentage                     = $grade['count']!= 0 ? $grade['total']/$grade['count']:0;
        if($percentage == 0)
        {
            $percentage_grade           = array('gr_name' => '-');
        }
        $user['grade']                  = $percentage < 1 ? $percentage_grade:convert_percentage_to_grade($percentage);
        if(empty($user))
        {
            $this->__status_code        = '204';
            $headers                    = array('error' => true, 'message' => 'This User details not available right now.', 'status_code' => '404');
            send_response($this->__status_code ,$headers);
        }
        $this->__status_code            = '200';
        $headers                        = array('error' => false, 'message' => 'User details fetched successfully.', 'status_code' => '200');
        $body                           = $user;
        send_response($this->__status_code ,$headers, $body);
    }

    /*
    purpose     : subscribed course list
    params      : user_id
    developer   : lineesh
    edited      : none
   */
    private function my_courses( $param = array() )
    {
        $this->load->model(array('Report_model'));
        $headers                    = array();
        $body                       = array();
        $token                      = isset($param['token'])?$param['token']:'';
        if($token != '')
        {
            $user_details           = $this->api_authentication->verify_token($token);
            if($user_details['token_verified'] == true)
            {
                $user               = !empty($user_details['user'])?$user_details['user']:array();
            }
            else
            {
                $status_code        = '401';
                $headers            = array('error' => true,'status_code'=>$status_code,'message' => 'Please login to continue');
                send_response($status_code ,$headers);
            }
        }
        else
        {
            $status_code            = '401';
            $headers                = array('error' => true,'status_code'=>$status_code,'message' => 'Please login to continue');
            send_response($status_code ,$headers);
        }
      
        $objects                            = array();
        $objects['key']                     = 'mobile_enrolled_' . $user['id'];
        $callback                           = 'get_subscriptions';
        $params                             = array('user_id' => $user['id']);
        $courses                            = $this->memcache->get($objects, $callback, $params);
        if(!empty($courses))
        {
            foreach ($courses as $course_key => $course) {
                $timestamps[$course_key]    = $course['created_date'];
            }
            array_multisort($timestamps, SORT_DESC, $courses);
        }

        if(empty($courses))
        {
            $this->__status_code            = '404';
            $headers                        = array('error' => true, 'message' => 'No Courses Subscribed.', 'status_code' => '404');
            send_response($this->__status_code ,$headers);
        }
        $this->__status_code                = '200';
        $headers                            = array('error' => false, 'message' => 'Subscribed Courses fetched successfully.', 'status_code' => '200');
        $body                               = $courses;
        send_response($this->__status_code ,$headers, $body);
    }

    /*
    purpose     : fetch all courses, bundles, online tests
    params      : identifier, category_ids, search_keyword, offset, limit, date, 
    developer   : kiran
    edited      : none
   */
    private function more_items($params = array())
    {
        $token                      = isset($params['token'])?$params['token']:'';
        $user_category_ids          = array();
        $enrolled_item_ids          = array();
        if($token != '')
        {
            $user_details           = $this->api_authentication->verify_token($token);
            if($user_details['token_verified'] == true)
            {
                $user               = !empty($user_details['user'])?$user_details['user']:array();
                $user_category_ids  = $user['us_category_id'];
                $user_id            = $user['id'];
                $objects['key']     = 'enrolled_item_ids_'.$user_id;
                $callback           = 'enrolled_item_ids';
                $user_params        = array('user_id' => $user_id);
                $enrolled_item_ids  = $this->memcache->get($objects, $callback, $user_params);
            }
            else
            {
                $status_code        = '401';
                $headers            = array('error' => true,'status_code'=>$status_code,'message' => 'Please login to continue');
                send_response($status_code ,$headers);
            }
        }
        $identifier                 = isset($params['body']['identifier'])? $params['body']['identifier'] : '';
        $category_ids               = empty($params['body']['category_ids'])? $user_category_ids: $params['body']['category_ids'] ;
        $search_keyword             = isset($params['body']['search_keyword'])? $params['body']['search_keyword'] : '';
        $offset                     = empty($params['body']['offset'])? 0:$params['body']['offset'];
        $limit                      = empty($params['body']['limit'])? 8:$params['body']['limit'];
        if($offset === NULL||$offset <= 0)
        {
            $offset                 = 1;
        }
        $offset                     = ($offset - 1) * $limit;
        $body                       = array();
        $body_label                 = 'list';
        $status_code                = '404';
        $headers                    = array('error' => true,'status_code'=> $status_code,'message' => 'data not found');
        if($identifier != ''){
            switch($identifier)
            {
                case "1":
                    $objects['key']                 = 'popular_courses';
                    $callback                       = 'popular_courses';
                    $popular_courses                = $this->memcache->get($objects, $callback);
                   
                    if(!empty($popular_courses))
                    {
                        $popular_courses            = $this->check_if_enrolled($popular_courses,$enrolled_item_ids);
                        if(!empty($search_keyword))
                        {
                            $popular_courses        = search_item($search_keyword,'item_name',$popular_courses);
                        }
                        
                        if(!empty($category_ids))
                        {
                            $popular_courses        = category_search($category_ids,'item_search',$popular_courses);
                        }
                    }
                    
                    $body['title']                  = 'Popular Courses';
                    $body['total_records']          = count($popular_courses);
                    $body['identifier']             = "1";
                    $popular_courses                = array_slice($popular_courses,$offset,$limit);
                    $body[$body_label]              = $popular_courses;
                    $status_code                    = '200';
                    $headers                        = array('error' => false,'status_code'=> $status_code,'message' => 'data fetched successfully');
                break;
                case "2":
                    
                    $objects['key']                 = 'featured_courses';
                    $callback                       = 'featured_courses';
                    $item_list                      = $this->memcache->get($objects, $callback);
                    if(!empty($item_list))
                    {
                        $item_list                  = $this->check_if_enrolled($item_list,$enrolled_item_ids);
                        if(!empty($search_keyword))
                        {
                            $item_list              = search_item($search_keyword,'item_name',$item_list);
                        }
                        
                        if(!empty($category_ids))
                        {
                            $item_list              = category_search($category_ids,'item_search',$item_list);
                        }
                    }
                    $body['title']                  = 'Featured Courses';
                    $body['total_records']          = count($item_list);
                    $body['identifier']             = "2";
                    $item_list                      = array_slice($item_list,$offset,$limit);
                    $body[$body_label]              = $item_list;
                    $status_code                    = '200';
                    $headers                        = array('error' => false,'status_code'=> $status_code,'message' => 'data fetched successfully');
                break;
                                
            }
            
        }
        send_response($status_code ,$headers,$body);
    }

    /*
    purpose     : function used for testing purpose to delete user
    params      : mobile ,email
    developer   : kiran
    edited      : none
   */
    private function delete_user($params = array())
    {
        $mobile         = isset($params['body']['mobile'])?$params['body']['mobile']:'';
        $email          = isset($params['body']['email'])?$params['body']['email']:'';
        if((!empty($email) )|| (!empty($mobile) ))
        {
            $filter_param               = array();
            if(!empty($mobile))
            {
                $filter_param['phone']  = $mobile;
            }
            if(!empty($email))
            {
                $filter_param['email']  = $email;
            }
            
            $filter_param['limit']      = '1';
            $filter_param['select']     = 'id,us_name,us_email,us_phone';
            $this->load->model(array('User_model'));
            $check_response             = $this->User_model->check_user_exist($filter_param);
            if(!empty($check_response['id']))
            {
                $this->User_model->remove_user(array('id'=>$check_response['id']));
                $this->memcache->delete('userdetails_'.$check_response['id']);
                $headers  = array('error' => false,'status_code'=> '200','message' => 'user deleted');
            }else{
                $headers  = array('error' => true,'status_code'=> '200','message' => 'user not found');
            }
        }
        else
        {
            $headers  = array('error' => true,'status_code'=> '200','message' => 'Email or Mobile missing');
        }
       
       
        send_response('200' ,$headers);
    }

    /*
    purpose     : Edit profile of user
    params      : name
    developer   : kiran
    edited      : none
   */
    private function edit_profile($param = array())
    {
        $name                       = isset($param['body']['name'])?$param['body']['name']:'';
        // $mobile                 = isset($param['phone'])?$param['phone']:'';
        // $country_code           = isset($params['country_code'])?$params['country_code']:'';
        // $email                  = isset($param['email'])?$param['email']:'';
        $token                      = isset($param['token'])?$param['token']:'';
        if($token != '')
        {
            $user_details           = $this->api_authentication->verify_token($token);
            if($user_details['token_verified'] == true)
            {
                $user               = !empty($user_details['user'])?$user_details['user']:array();
            }
            else
            {
                $status_code        = '401';
                $headers            = array('error' => true,'status_code'=>$status_code,'message' => 'Please login to continue');
                send_response($status_code ,$headers);
            }
        }
        else
        {
            $status_code            = '401';
            $headers                = array('error' => true,'status_code'=>$status_code,'message' => 'Please login to continue');
            send_response($status_code ,$headers);
        }
        $user_id                = $user['id'];
        $data                   = array();
        $filter                 = array();
        $data['us_name']        = $name;
        $filter['update']       = true;
        $filter['id']           = $user_id;
        $this->load->model(array('User_model'));
        $changed_status         = $this->User_model->save_userdata($data,$filter);
        if($changed_status)
        {
            $object_key         = 'userdetails_'.$user_id;
            $this->memcache->delete($object_key);
        }
        $status_code            = 400;
        $headers                = array('error' => true, 'message' => 'Profile update failed', 'status_code' => $status_code);
        $body                   = array();
        $token_params           = array();
        $token_params['phone']  = $user['us_phone'];
        $token_response         = $this->api_authentication->create_token($token_params);
        if(!empty($token_response))
        {
            $status_code            = 200;
            $headers                = array('error' => false, 'message' => 'Profile update successfully', 'status_code' => $status_code);
            $body['token']          = $token_response['token'];
            $body['user']           = $this->get_user_data($token_response['user']);
        }
        send_response($status_code ,$headers, $body);
    }

    /*
    purpose     : Upload profile image of user
    params      : none
    developer   : kiran
    edited      : none
   */
    public function upload_user_image($param )
    {
        $token                      = isset($param['token'])?$param['token']:'';
        if($token != '')
        {
            $user_details           = $this->api_authentication->verify_token($token);
            if($user_details['token_verified'] == true)
            {
                $user               = !empty($user_details['user'])?$user_details['user']:array();
            }
            else
            {
                $status_code        = '401';
                $headers            = array('error' => true,'status_code'=>$status_code,'message' => 'Please login to continue');
                send_response($status_code ,$headers);
            }
        }
        else
        {
            $status_code            = '401';
            $headers                = array('error' => true,'status_code'=>$status_code,'message' => 'Please login to continue');
            send_response($status_code ,$headers);
        }
        $user_id                    = $user['id'];
        $status_code                = 400;
        $headers                    = array('error' => true, 'message' => 'Image upload failed', 'status_code' => $status_code);
        $body                       = array();
        $directory                  = user_upload_path();
        if(!file_exists($directory)){
            mkdir($directory, 0777, true);
        }
        $config                     = array();
        $config['upload_path']      =  $directory;
        $config['allowed_types']    = 'gif|jpg|png|jpeg';
        $new_name                   = $user_id.'.jpg';
        $config['file_name']        = $new_name;
        $config['overwrite']        = TRUE;
        $this->load->library('upload');
        $this->upload->initialize($config);
        $upload                     = $this->upload->do_upload('file');
        if($upload)
        {
            $uploaded_data          = $this->upload->data();
            $new_file               = $this->crop_image($uploaded_data);
            $has_s3                 = $this->settings->setting('has_s3');
            if( $has_s3['as_superadmin_value'] && $has_s3['as_siteadmin_value'] )
            {
                $user_profile_path  = user_upload_path().$new_file;
                uploadToS3($user_profile_path, $user_profile_path);
                unlink($user_profile_path);
            }
            $response['user_image'] = user_path().$new_file; 
            $save                   = array();
            $save['id']             = $user_id;
            $save['us_image']       = $new_file.'?v='.rand(100, 999);   
            $this->load->model(array('User_model'));
            $save_status            = $this->User_model->save($save);
            if($save_status)
            {
                $status_code            = 200;
                $headers                = array('error' => false, 'message' => 'Image upload completed', 'status_code' => $status_code);
                $body['upload_image']   = (($save['us_image'] == 'default.jpg')?default_user_path():user_path()).$save['us_image'];
            }
             
        }
        send_response($status_code ,$headers, $body);
    }
    
    function crop_image($uploaded_data)
    {
        $source_path = $uploaded_data['full_path'];
        define('DESIRED_IMAGE_WIDTH', 155);
        define('DESIRED_IMAGE_HEIGHT', 155);
        /*
         * Add file validation code here
         */

        list($source_width, $source_height, $source_type) = getimagesize($source_path);

        switch ($source_type)
        {
            case IMAGETYPE_GIF:
                $source_gdim = imagecreatefromgif($source_path);
                break;
            case IMAGETYPE_JPEG:
                $source_gdim = imagecreatefromjpeg($source_path);
                break;
            case IMAGETYPE_PNG:
                $source_gdim = imagecreatefrompng($source_path);
                break;
        }

        $source_aspect_ratio    = $source_width / $source_height;
        $desired_aspect_ratio   = DESIRED_IMAGE_WIDTH / DESIRED_IMAGE_HEIGHT;

        if ($source_aspect_ratio > $desired_aspect_ratio)
        {
            /*
             * Triggered when source image is wider
             */
            $temp_height        = DESIRED_IMAGE_HEIGHT;
            $temp_width         = ( int ) (DESIRED_IMAGE_HEIGHT * $source_aspect_ratio);
        } else {
            /*
             * Triggered otherwise (i.e. source image is similar or taller)
             */
            $temp_width         = DESIRED_IMAGE_WIDTH;
            $temp_height        = ( int ) (DESIRED_IMAGE_WIDTH / $source_aspect_ratio);
        }

        /*
         * Resize the image into a temporary GD image
         */

        $temp_gdim              = imagecreatetruecolor($temp_width, $temp_height);
        imagecopyresampled(
            $temp_gdim,
            $source_gdim,
            0, 0,
            0, 0,
            $temp_width, $temp_height,
            $source_width, $source_height
        );

        /*
         * Copy cropped region from temporary image into the desired GD image
        */

        $x0             = ($temp_width - DESIRED_IMAGE_WIDTH) / 2;
        $y0             = ($temp_height - DESIRED_IMAGE_HEIGHT) / 2;
        $desired_gdim   = imagecreatetruecolor(DESIRED_IMAGE_WIDTH, DESIRED_IMAGE_HEIGHT);
        imagecopy(
            $desired_gdim,
            $temp_gdim,
            0, 0,
            $x0, $y0,
            DESIRED_IMAGE_WIDTH, DESIRED_IMAGE_HEIGHT
        );

        /*
         * Render the image
         * Alternatively, you can save the image in file-system or database
         */

        //header('Content-type: image/jpeg');
        $directory      = user_upload_path();
        $this->make_directory($directory);
        imagejpeg($desired_gdim, $directory.$uploaded_data['raw_name'].'.jpg');

        /*
         * Add clean-up code here
         */
        return $uploaded_data['raw_name'].'.jpg';
    }
    
    private function make_directory($path=false)
    {
        if(!$path)
        {
            return false;
        }
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }

    /*
    purpose     : Terms and conditions
    params      : none
    developer   : kiran
    edited      : none
   */
    private function terms_condition()
    {
        $terms_condition    = "";
        $status_code        = '200';
        $headers            = array('error' => false,'status_code'=> $status_code,'message' => 'data fetched');
        $body['terms']      = $terms_condition;
        send_response($status_code ,$headers,$body);
    }

    /*
    purpose     : Share App for Android and IOS
    params      : none
    developer   : kiran
    edited      : none
   */
    private function share_app($params , $user = array())
    {
        $app_type           = isset($params['body']['os_type'])?$params['body']['os_type']:"";
        switch($app_type)
        {
            case 1:
                $url        = "www.ofabee.com";
            break;
            case 2:
                $url        = "www.ofabee.com";
            break;
        }
        $status_code        = '200';
        $headers            = array('error' => false,'status_code'=> $status_code,'message' => 'data fetched');
        $body['app_url']    = $url;
        send_response($status_code ,$headers,$body);
    }

    /*
    purpose     : Explore Course to view all courses and bundles
    params      : token, category_ids
    developer   : kiran
    edited      : none
   */
    public function explore_courses($params = array())
    {
        $token                      = isset($params['token'])?$params['token']:'';
        // $search_keyword             = isset($params['body']['search_keyword'])? $params['body']['search_keyword'] : '';
        $enrolled_item_ids          = array();
        $user_category_ids          = array();
        if($token != '')
        {
            $user_details           = $this->api_authentication->verify_token($token);
            if($user_details['token_verified'] == true)
            {
                $user               = !empty($user_details['user'])?$user_details['user']:array();
                // $user_category_ids  = $user['us_category_id'];
                $user_id            = $user['id'];
                $objects['key']     = 'enrolled_item_ids_'.$user_id;
                $callback           = 'enrolled_item_ids';
                $memcache_params    = array('user_id' => $user_id);
                $enrolled_item_ids  = $this->memcache->get($objects, $callback, $memcache_params);
            }
            else
            {
                $status_code        = '401';
                $headers            = array('error' => true,'status_code'=>$status_code,'message' => 'Please login to continue');
                send_response($status_code ,$headers);
            }
        }
        $category_ids               = empty($params['body']['category_ids'])? $user_category_ids: $params['body']['category_ids'] ;
        $body                       = array();
        $all_course_sorted          = array();
        $all_sorted_course          = array();
        $status_code                = '404';
        $headers                    = array('error' => true,'status_code'=> $status_code,'message' => 'data not found');
        $objects['key']             = 'all_sorted_course';
        $callback                   = 'all_sorted_course';
        $sorted_courses             = $this->memcache->get($objects, $callback);
        if(!empty($sorted_courses))
        {
            $all_sorted_course      = $this->check_if_enrolled($sorted_courses,$enrolled_item_ids);
            // if(!empty($search_keyword))
            // {
            //     $all_sorted_course  = search_item($search_keyword,'item_name',$all_sorted_course);
            // }
            
            if(!empty($category_ids))
            {
                $all_sorted_course  = category_search($category_ids,'item_search',$all_sorted_course);
            }
        }
        
        if(count($all_sorted_course) > 0)
        {
            $all_course_sorted      = $all_sorted_course;
            $status_code            = '200';
            $headers                = array('error' => false,'status_code'=> $status_code,'message' => 'data fetched successfully');
        }
        $body['title']              = 'All Courses';
        $body['total_records']      = count($all_course_sorted);
        $body['all_course']         = $all_course_sorted;
        
        
        send_response($status_code ,$headers,$body);
    }

    /*
    purpose     : supporting function to check if course or bundle subscribed
    params      : token, category_ids
    developer   : kiran
    edited      : none
   */
    private function check_if_enrolled($sorted_courses = array(),$enrolled_item_ids = array())
    {
        $all_sorted_course          = array();
        $enrolled_bundles           = isset($enrolled_item_ids['enrolled_bundles'])?$enrolled_item_ids['enrolled_bundles']:array();
        $enrolled_courses           = isset($enrolled_item_ids['enrolled_courses'])?$enrolled_item_ids['enrolled_courses']:array();
        if(!empty($sorted_courses))
        {
            $enrolled_course_ids    = array_column($enrolled_courses, 'courses');
            $enrolled_bundle_ids    = array_column($enrolled_bundles, 'bundles');
            foreach($sorted_courses as $sorted_course)
            {
                if($sorted_course['item_type'] == 'bundle')
                {
                    $sorted_course['subscription_status']           = '';
                    $sorted_course['enrolled']                      = (in_array($sorted_course['item_id'],$enrolled_bundle_ids))?true:false;
                    foreach($enrolled_bundles as $key => $bundles)
                    {
                        if($sorted_course['item_id'] == $bundles['bundles'])
                        {
                            $sorted_course['subscription_status']   = $bundles['status'];
                            unset($enrolled_bundles[$key]);
                        }
                    }
                    
                }
                else if($sorted_course['item_type'] == 'course')
                {
                    $sorted_course['subscription_status']           = '';
                    $sorted_course['enrolled']                      = (in_array($sorted_course['item_id'],$enrolled_course_ids))?true:false;
                    foreach($enrolled_courses as $key => $courses)
                    {
                        if($sorted_course['item_id'] == $courses['courses'])
                        {
                            $sorted_course['subscription_status']   = $courses['status'];
                            unset($enrolled_courses[$key]);
                        }
                    }
                }
                array_push($all_sorted_course,$sorted_course);
            }
        }
        return $all_sorted_course;
    }

        /*
    purpose : supporting function to check if bundle courses are subscribed
    params : token, category_ids
    developer : Lineesh
    edited : none
    */
    private function check_bundle_courses_enrolled($bundle_courses = array(),$enrolled_item_ids = array())
    {
        $all_sorted_course = array();
        $enrolled_courses = isset($enrolled_item_ids['enrolled_courses'])?$enrolled_item_ids['enrolled_courses']:array();
        if(!empty($bundle_courses))
        {
            $enrolled_course_ids = array_column($enrolled_courses, 'courses');
            foreach($bundle_courses as $bundle_course)
            {
                $bundle_course['subscription_status'] = '';
                foreach($enrolled_courses as $key => $courses)
                {
                    if($bundle_course['id'] == $courses['courses'])
                    {
                        $bundle_course['subscription_status'] = $courses['status'];
                        unset($enrolled_courses[$key]);
                    }
                }
                array_push($all_sorted_course,$bundle_course);
            }
        }
        return $all_sorted_course;
    }
    /*
    purpose     : course details
    params      : item_id, item_type(course or bundle)
    developer   : lineesh
    edited      : kiran
   */

    private function course_info($param = array())
    {
        $item_id                        = isset($param['body']['item_id'])?$param['body']['item_id']:'';
        $item_type                      = isset($param['body']['item_type'])?$param['body']['item_type']:'';
        $item_slug                      = empty($param['body']['slug'])?'':$param['body']['slug'];
        $token                          = isset($param['token'])?$param['token']:'';
        $bundle_ids                     = array();
        $course_ids                     = array();
        $enrolled_item_ids              = array();
        if($token != '')
        {
            $user_details               = $this->api_authentication->verify_token($token);
            
            if($user_details['token_verified'] == true)
            { 
                $user                   = !empty($user_details['user'])?$user_details['user']:array();
                $user_id                = $user['id'];
                $objects['key']         = 'enrolled_item_ids_'.$user_id;
                $callback               = 'enrolled_item_ids';
                $memcache_params        = array('user_id' => $user_id);
                $enrolled_item_ids      = $this->memcache->get($objects, $callback, $memcache_params);
                if(isset($enrolled_item_ids['enrolled_bundles']))
                {
                    $bundle_ids         = array_column($enrolled_item_ids['enrolled_bundles'],'bundles');
                }
                if(isset($enrolled_item_ids['enrolled_courses']))
                {
                    $course_ids         = array_column($enrolled_item_ids['enrolled_courses'],'courses');
                }
            }
            else
            {
                $status_code            = '401';
                $headers                = array('error' => true,'status_code'=>$status_code,'message' => 'Please login to continue');
                send_response($status_code ,$headers);
            }
        }
        if(!empty($item_slug))
        {   
            $this->load->model(array('Bundle_model'));
            $filter_param               = array();
            $filter_param['slug']       = $item_slug;
            $filter_param['select']     = 'r_item_type,r_item_id';
            $route_details              = $this->Bundle_model->get_route($filter_param);
            $item_id                    = $route_details['r_item_id'];
            $item_type                  = $route_details['r_item_type'];
        }
        
        if($item_id == '' || $item_type == '')
        {
            $status_code                = '204';
            $headers                    = array('error' => true, 'message' => 'These details not available right now.', 'status_code' => $status_code);
            send_response($status_code ,$headers);
        }
        $status_code                    = '204';
        $headers                        = array('error' => true, 'message' => 'Details not available right now.', 'status_code' => $status_code);
        $body                           = array();
        if($item_type == 'course')
        {
            $objects                    = array();
            $objects['key']             = 'mobile_course_'.$item_id;
            $callback                   = 'mobile_course_details';
            $params                     = array('id' => $item_id);
            $details                    = $this->memcache->get($objects, $callback, $params);
            $details['course_enrolled'] = false;
            if(in_array($item_id,$course_ids))
            {
                $details['course_enrolled'] = true;
            }
        }
        else if($item_type == 'bundle')
        {
            $objects                    = array();
            $objects['key']             = 'mobile_bundle_'.$item_id;
            $callback                   = 'mobile_bundle_details';
            $params                     = array('id' => $item_id);
            $details                    = $this->memcache->get($objects, $callback, $params);
            $bundle_courses             = $this->memcache->get($objects, $callback, $params)['courses'];
            if(!empty($bundle_courses))
            {
                $details['courses']     = $this->check_bundle_courses_enrolled($bundle_courses,$enrolled_item_ids);
            }
            $details['course_enrolled'] = false;
            if(in_array($item_id,$bundle_ids))
            {
                $details['course_enrolled'] = true;
            }
        }
        if(!empty($details))
        {
            $status_code                = '200';
            $headers                    = array('error' => false, 'message' => 'All details fetched successfully.', 'status_code' => $status_code);
            $body                       = $details;
        }
        send_response($status_code ,$headers, $body);
    }

    /*
    purpose     : check if a coupon is valid or not
    params      : coupon code
    developer   : kiran
    edited      : none
   */
    private function check_coupon($param = array())
    {
        $this->load->library('Promocode');
        $token                      = isset($param['token'])?$param['token']:'';
        $promo_code                 = isset($param['body']['promo_code'])?trim($param['body']['promo_code']):'';
        if($token != '')
        {
            $user_details           = $this->api_authentication->verify_token($token);
            if($user_details['token_verified'] == true)
            {
                $user                       = !empty($user_details['user'])?$user_details['user']:array();
                $user_data                  = array();
                $user_data['id']            = $user['id'];
                $user_data['name']          = $user['us_name'];
                $user_data['email']         = $user['us_email'];
                $user_usage[$user['id']]    = (isset($user_data))?$user_data:array();
            }
            else
            {
                $status_code                = '401';
                $headers                    = array('error' => true,'status_code'=>$status_code,'message' => 'Please login to continue');
                send_response($status_code ,$headers);
            }
        }
        else
        {
            $status_code                    = '401';
            $headers                        = array('error' => true,'status_code'=>$status_code,'message' => 'Please login to continue');
            send_response($status_code ,$headers);
        }
        
        $check_param                        = array();
        $check_param['promocode']           = $promo_code;
        $check_param['user_details']        = json_encode($user_usage);
        $response                           = $this->promocode->check_valid_promocode($check_param);
        $status_code                        = '204';
        $body                               = array();
        if($response['header']['success'])
        {
            $status_code                    = '200';
            $key                            = md5('promocode_'.$user['id']);
            $promocode_data                 = array();
            $promocode_data['type']         = $response['body']['promocode']['pc_discount_type'];
            $promocode_data['rate']         = $response['body']['promocode']['pc_discount_rate'];
            $promocode_data['expiry']       = $response['body']['promocode']['pc_expiry_date'];
            $body                           = $promocode_data;
            $content['promocode']           = $promo_code;
            $expiry                         = '600';
            $this->memcache->set($key, $content, $expiry);
        }
        $headers                            = array('error' => false,'status_code'=>$status_code,'message' => $response['header']['message']);
        send_response($status_code ,$headers,$body);
    }

    private function payment_request($param = array())
    {
        $this->load->library('Promocode','settings');
        $this->load->model(array('order_model','Payment_model'));
        $token                      = isset($param['token'])?$param['token']:'';
        $item_id                    = isset($param['body']['item_id'])?$param['body']['item_id']:'';
        $item_type                  = isset($param['body']['item_type'])?$param['body']['item_type']:'';
        
        if($token != '')
        {
            $user_details           = $this->api_authentication->verify_token($token);
            if($user_details['token_verified'] == true)
            {
                $user               = !empty($user_details['user'])?$user_details['user']:array();
                
            }
            else
            {
                $status_code        = '401';
                $headers            = array('error' => true,'status_code'=>$status_code,'message' => 'Please login to continue');
                send_response($status_code ,$headers);
            }
        }
        else
        {
            $status_code            = '401';
            $headers                = array('error' => true,'status_code'=>$status_code,'message' => 'Please login to continue');
            send_response($status_code ,$headers);
        }

        if($item_id == '' || $item_type == '')
        {
            $status_code            = '204';
            $headers                = array('error' => true, 'message' => 'These details not available right now.', 'status_code' => $status_code);
            send_response($status_code ,$headers);
        }
        $key                        = md5('request_razorpay'.$user['id']);
        $content                    = array('razorpay_item_id' => $item_id,'razorpay_item_type' => $item_type);
        $expiry                     = '600';
        $this->memcache->set($key, $content, $expiry);

        $payment_keys               = $this->settings->setting('payment_gateway');
        $payment_keys               = $payment_keys['as_setting_value']['setting_value']->razorpay;
        switch ($item_type) 
        {
            case "1":
                $objects                        = array();
                $objects['key']                 = 'course_'.$item_id;
                $callback                       = 'course_details';
                $params                         = array('id' => $item_id);
                $course                         = $this->memcache->get($objects, $callback, $params);
                $item_name                      = $course['cb_title'];
                $item_code                      = $course['cb_code'];
                $item_base_price                = $course['cb_price'];
                $item_discount_price            = $course['cb_discount'];
                $tax_type                       = $course['cb_tax_method'];
                $item_price                     = ($course['cb_discount']!=0)?$course['cb_discount']:$course['cb_price'];
                $item_image                     = $course['cb_image'];
                $item_image                     = (($item_image == 'default.jpg') ? default_course_path() : course_path(array('course_id' => $item_id))) . $item_image;
                $item_details                   = $course;
                break;
            case "2":
                $objects                        = array();
                $objects['key']                 = 'bundle_'.$item_id;
                $callback                       = 'bundle_details';
                $params                         = array('id' => $item_id);
                $bundle                         = $this->memcache->get($objects, $callback, $params);
               
                $item_name                      = $bundle['c_title'];
                $item_code                      = $bundle['c_code'];
                $item_base_price                = $bundle['c_price'];
                $item_discount_price            = $bundle['c_discount'];
                $tax_type                       = $bundle['c_tax_method'];
                $item_price                     = ($bundle['c_discount']!=0)?$bundle['c_discount']:$bundle['c_price']; 
                $item_image                     = $bundle['c_image'];
                $item_image                     = (($item_image == 'default.jpg') ? default_catalog_path() : catalog_path(array('bundle_id' => $item_id))) . $item_image;
                
                $item_details                   = array(
                                'id'                => $bundle['id'],
                                'c_title'           => $bundle['c_title'],
                                'c_code'            => $bundle['c_code'],
                                //'c_courses'         => json_decode($bundle['c_courses']),
                                'c_courses'         => $bundle['c_courses'],
                                'c_access_validity' => $bundle['c_access_validity'],
                                'c_validity'        => $bundle['c_validity'],
                                'c_validity_date'   => $bundle['c_validity_date'],
                                'c_price'           => $bundle['c_price'],
                                'c_discount'        => $bundle['c_discount'],
                                'c_tax_method'      => $bundle['c_tax_method']
                            );
                break;
        }

        $objects                                = array();
        $objects['key']                         = md5('promocode_'.$user['id']);
        $promocode                              = $this->memcache->get($objects);
        $promocode                              = $promocode['promocode'];
        if($promocode)
        {
            $user_details                       = array();
            $user_details['id']                 = $user['id'];
            $user_details['name']               = $user['us_name'];
            $user_details['email']              = $user['us_email'];
            $user_detail[$user['id']]           = (isset($user_details))?$user_details:array();

            $param                              = array();
            $param['promocode']                 = $promocode;
            $param['user_details']              = json_encode($user_detail);
            $promocode_response                 = $this->promocode->check_valid_promocode($param);
            if($promocode_response['header']['success'])
            {
                $discout_type                   = (isset($promocode_response['body']['promocode']['pc_discount_type']))?$promocode_response['body']['promocode']['pc_discount_type']:1;
                if($discout_type == 1) 
                {
                    $discount_rate    = ($promocode_response['body']['promocode']['pc_discount_rate']!=NULL)?$promocode_response['body']['promocode']['pc_discount_rate']:0;
                } 
                else 
                {
                    $discount_percentage  = ($promocode_response['body']['promocode']['pc_discount_rate']!=NULL)?$promocode_response['body']['promocode']['pc_discount_rate']:0;
                    $discount_rate        = ($discount_percentage!=0)?round((($discount_percentage/100) * $item_price),2):0;
                }
                
                $item_price               = ($discount_rate<$item_price)?($item_price-$discount_rate):0;           
                
            }
        }
         if($tax_type == '1')
        {
            $gst_setting        = $this->settings->setting('has_tax');
            $cgst               = ($gst_setting['as_setting_value']['setting_value']->cgst != '') ? $gst_setting['as_setting_value']['setting_value']->cgst:'0';
            $sgst               = ($gst_setting['as_setting_value']['setting_value']->sgst != '') ? $gst_setting['as_setting_value']['setting_value']->sgst:'0';
            $sgst_price         = ($sgst / 100) * $item_price;
            $cgst_price         = ($cgst / 100) * $item_price;
            $total_price        = $item_price+$sgst_price + $cgst_price;
        }
        else 
        {
            $total_price        = $item_price;
        }
        
        $pending_order = $this->order_model->get_pending_payment(array('id' => $user['id'], 'item_id' => $item_id));
        if(isset($pending_order['id']))
        {

            $insert_id                  = $pending_order['id'];
            $order_id                   = $pending_order['ph_order_id'];

            $key                        = md5('razorpay_payment'.$user['id']);
            $content                    = array('ofabee_payment_id'   => $insert_id, 'ofabee_order_id' => $order_id);
            $expiry                     = '600';
            $this->memcache->set($key, $content, $expiry);
        }
        else
        {
            $user_details                               = array();
            $user_details['name']                       = $user['us_name'];
            $user_details['email']                      = $user['us_email'];
            $user_details['phone']                      = $user['us_phone'];

            $payment_param                              = array();
            $payment_param['id']                        = false;
            $payment_param['ph_user_id']                = $user['id'];
            $payment_param['ph_user_details']           = json_encode($user_details);//2 
            $payment_param['ph_item_other_details']     = json_encode($item_details);
            $payment_param['ph_promocode']              = '';
            $payment_param['ph_item_id']                = $item_id;
            $payment_param['ph_item_type']              = $item_type;
            $payment_param['ph_item_name']              = $item_name;
            $payment_param['ph_item_base_price']        = $item_base_price;
            $payment_param['ph_item_discount_price']    = $item_discount_price;
            $payment_param['ph_tax_type']               = $tax_type;
            $payment_param['ph_item_code']              = $item_code;

            $gst_setting                                = $this->settings->setting('has_tax');
            $cgst                                       = ($gst_setting['as_setting_value']['setting_value']->cgst != '')?$gst_setting['as_setting_value']['setting_value']->cgst:'0';
            $sgst                                       = ($gst_setting['as_setting_value']['setting_value']->sgst != '')?$gst_setting['as_setting_value']['setting_value']->sgst:'0';
            if($tax_type != '1')
            { //inclusive
                $sgst_price                             = $item_price / (100 + $sgst) * $sgst;
                $cgst_price                             = $item_price / (100 + $cgst) * $cgst;
                $totalTaxPercentage                     = $cgst + $sgst;
                $total_course_price                     = $item_price;
            }
            else
            {
                $sgst_price                                 = ($sgst / 100) * $item_price;
                $cgst_price                                 = ($cgst / 100) * $item_price;
                $total_course_price                         = $item_price + ($sgst_price + $cgst_price);
            }

            $payment_tax_object                         = array();
            $payment_tax_object['sgst']['percentage']   = $sgst;
            $payment_tax_object['sgst']['amount']       = round($sgst_price, 2); 
            $payment_tax_object['cgst']['percentage']   = $cgst;
            $payment_tax_object['cgst']['amount']       = round($cgst_price, 2); 

            $payment_param['ph_tax_objects']            = json_encode($payment_tax_object);
            $payment_param['ph_item_amount_received']   = '0';
            $payment_param['ph_payment_mode']           = '1';

            $payment_param['ph_transaction_id']         = '';
            $payment_param['ph_transaction_details']    = '';
            $payment_param['ph_account_id']             = config_item('id');;
            $payment_param['ph_payment_gateway_used']   = 'razorpay';
            $payment_param['ph_status']                 = '2';
            $payment_param['ph_payment_date']           = date('Y-m-d H:i:s');
            
            $insert_id                                  = $this->Payment_model->save_history($payment_param);
            if($insert_id)
            {
                $order_id                               = date('Y').date('m').date('d').$insert_id;
                $key                                    = md5('razorpay_payment'.$user['id']);
                $content                                = array('ofabee_payment_id'   => $insert_id, 'ofabee_order_id' => $order_id);
                $expiry                                 = '600';
                $this->memcache->set($key, $content, $expiry);
                $order_param                            = array();
                $order_param['id']                      = $insert_id;
                $order_param['ph_order_id']             = $order_id;
                $this->Payment_model->save_history($order_param);
            }
        }
        if($total_price >= 1)
        {
            
            $api_key             = $payment_keys->creditionals->key;
            $auth_token          = $payment_keys->creditionals->secret;

            $config              = array(
                                        'key' => $api_key,
                                        'secret' => $auth_token
                                    );
            $this->load->library('razorpay', $config);
            $total_price        = ($total_price > 1)?$total_price:1;
            $total_price        = round($total_price, 2);
            $order_object       = array(
                                        'amount'          => $total_price * 100, // 2000 rupees in paise
                                        'payment_capture' => 1 // auto capture
                                );
            $order              = $this->razorpay->create_order($order_object);
           
            $status_code        = '200';
            $headers            = array('error' => false,'status_code'=>$status_code,'message' => 'Payment processed');
            $body               = array('order_id'=> $order['order_id'],'item_price'=>$total_price);
            send_response($status_code ,$headers,$body);
        }
        else
        {
            $param                  = array();
            $param['item_type']     = $item_type;
            $param['item_id']       = $item_id;
            $param['user']          = $user;
            $this->discount_enrollment($param);
        }

    }

    private function get_item_details($item_id, $item_type)
    {
        switch ($item_type) 
        {
            case "1":
                $objects                        = array();
                $objects['key']                 = 'course_'.$item_id;
                $callback                       = 'course_details';
                $params                         = array('id' => $item_id);
                $course                         = $this->memcache->get($objects, $callback, $params);

                $item_name                      = $course['cb_title'];
                $item_code                      = $course['cb_code'];
                $item_base_price                = $course['cb_price'];
                $item_discount_price            = $course['cb_discount'];
                $tax_type                       = $course['cb_tax_method'];
                $item_price                     = ($course['cb_discount']!=0)?$course['cb_discount']:$course['cb_price'];
                $course['course_price']         = $item_price;                 
                $item_image                     = $course['cb_image'];
                $item_image                     = (($item_image == 'default.jpg') ? default_course_path() : course_path(array('course_id' => $item_id))) . $item_image;
                $item_details                   = $course;
                break;
            case "2":
                $objects                        = array();
                $objects['key']                 = 'bundle_'.$item_id;
                $callback                       = 'bundle_details';
                $params                         = array('id' => $item_id);
                $bundle                         = $this->memcache->get($objects, $callback, $params);
               
                $item_name                      = $bundle['c_title'];
                $item_code                      = $bundle['c_code'];
                $item_base_price                = $bundle['c_price'];
                $item_discount_price            = $bundle['c_discount'];
                $tax_type                       = $bundle['c_tax_method'];
                $item_price                     = ($bundle['c_discount']!=0)?$bundle['c_discount']:$bundle['c_price']; 
                $item_image                     = $bundle['c_image'];
                $item_image                     = (($item_image == 'default.jpg') ? default_catalog_path() : catalog_path(array('bundle_id' => $item_id))) . $item_image;
                
                $item_details                   = array(
                                'id'                => $bundle['id'],
                                'c_title'           => $bundle['c_title'],
                                'c_code'            => $bundle['c_code'],
                                //'c_courses'         => json_decode($bundle['c_courses']),
                                'c_courses'         => $bundle['c_courses'],
                                'c_access_validity' => $bundle['c_access_validity'],
                                'c_validity'        => $bundle['c_validity'],
                                'c_validity_date'   => $bundle['c_validity_date'],
                                'c_price'           => $bundle['c_price'],
                                'c_discount'        => $bundle['c_discount'],
                                'c_tax_method'      => $bundle['c_tax_method']
                            );
                break;
        }
    }

    private function payment_response($param = array())
    {    
        $this->load->library('Promocode');
        $this->load->model(array('Payment_model'));
        $token                      = isset($param['token'])?$param['token']:'';
        $item_id                    = isset($param['body']['item_id'])?$param['body']['item_id']:'';
        $item_type                  = isset($param['body']['item_type'])?$param['body']['item_type']:'';
        $razorpay_signature         = isset($param['body']['razorpay_signature'])?$param['body']['razorpay_signature']:'';
        $razorpay_payment_id        = isset($param['body']['razorpay_payment_id'])?$param['body']['razorpay_payment_id']:'';
        $razorpay_order_id          = isset($param['body']['razorpay_order_id'])?$param['body']['razorpay_order_id']:'';;
        if($token != '')
        {
            $user_details           = $this->api_authentication->verify_token($token);
            if($user_details['token_verified'] == true)
            {
                $user               = !empty($user_details['user'])?$user_details['user']:array();
            }
            else
            {
                $status_code        = '401';
                $headers            = array('error' => true,'status_code'=>$status_code,'message' => 'Please login to continue');
                send_response($status_code ,$headers);
            }
        }
        else
        {
            $status_code            = '401';
            $headers                = array('error' => true,'status_code'=>$status_code,'message' => 'Please login to continue');
            send_response($status_code ,$headers);
        }
        
        $promocode_details          = array();
        switch ($item_type) 
        {
            case "1":
                $item_type_label    = 'course';
                $objects            = array();
                $objects['key']     = 'course_'.$item_id;
                $callback           = 'course_details';
                $params             = array('id' => $item_id);
                $course             = $this->memcache->get($objects, $callback, $params);
                $item_name          = $course['cb_title'];
                $item_code          = $course['cb_code'];
                $item_base_price    = $course['cb_price'];
                $item_discount_price= $course['cb_discount'];
                $tax_type           = $course['cb_tax_method'];
                $item_price         = ($course['cb_discount']!=0)?$course['cb_discount']:$course['cb_price'];
                $item_details       = $course;
            break;
            case "2":
                $subscription_param = array();
                $item_type_label    = 'bundle';
                $objects            = array();
                $objects['key']     = 'bundle_'.$item_id;
                $callback           = 'bundle_details';
                $params             = array('id' => $item_id);
                $bundle             = $this->memcache->get($objects, $callback, $params);
                $item_name          = $bundle['c_title'];
                $item_code          = $bundle['c_code'];
                $item_base_price    = $bundle['c_price'];
                $item_discount_price= $bundle['c_discount'];
                $tax_type           = $bundle['c_tax_method'];
                $item_price         = ($bundle['c_discount']!=0)?$bundle['c_discount']:$bundle['c_price'];
                $subscription_param['bs_bundle_details'] = array(
                                                                'id'                => $bundle['id'],
                                                                'c_title'           => $bundle['c_title'],
                                                                'c_code'            => $bundle['c_code'],
                                                                'c_courses'         => json_decode($bundle['c_courses']),
                                                                'c_access_validity' => $bundle['c_access_validity'],
                                                                'c_validity'        => $bundle['c_validity'],
                                                                'c_validity_date'   => $bundle['c_validity_date'],
                                                                'c_price'           => $bundle['c_price'],
                                                                'c_discount'        => $bundle['c_discount'],
                                                                'c_tax_method'      => $bundle['c_tax_method']
                                                            );
                $item_details       = $subscription_param['bs_bundle_details'];
                break;
        }

        $objects                                = array();
        $objects['key']                         = md5('promocode_'.$user['id']);
        $promocode                              = $this->memcache->get($objects);
        $promocode                              = $promocode['promocode'];
        if($promocode)
        {
            $user_detail                           = array();
            $user_details                          = array();
            $user_details['id']                    = $user['id'];
            $user_details['name']                  = $user['us_name'];
            $user_details['email']                 = $user['us_email'];
            $user_details['phone']                 = $user['us_phone'];
            $user_details['itemType']              = $item_type_label;
            $user_details['itemName']              = $item_name;
            $user_details['applied_on']            = date('d-m-Y H:i:s');
            $user_detail[$user['id']]           = (isset($user_details))?$user_details:array();
            $param                                 = array();
            $param['promocode']                    = $promocode;
            $param['user_details']                 = json_encode($user_detail);
            $promocode_response                    = $this->promocode->check_valid_promocode($param);
            
            if($promocode_response['header']['success'])
            {
                
                $discout_type                   = (isset($promocode_response['body']['promocode']['pc_discount_type']))?$promocode_response['body']['promocode']['pc_discount_type']:1;
                if($discout_type==1) 
                {
                    $discount_percentage        = 0;
                    $discount_rate              = ($promocode_response['body']['promocode']['pc_discount_rate']!=NULL)?$promocode_response['body']['promocode']['pc_discount_rate']:0;
                } 
                else 
                {
                    $discount_percentage        = ($promocode_response['body']['promocode']['pc_discount_rate']!=NULL)?$promocode_response['body']['promocode']['pc_discount_rate']:0;
                    $discount_rate              = ($discount_percentage!=0)?round((($discount_percentage/100) * $item_price),2):0;
                } 
                
                $item_price                     = ($discount_rate<$item_price)?($item_price-$discount_rate):0;           
               
                /* save promocode details */
                $promocode_details['promocode']           = $promocode;
                $promocode_details['discout_type']        = $discout_type;
                $promocode_details['discount_percentage'] = $discount_percentage;
                $promocode_details['discount_rate']       = $discount_rate;
                $promocode_details['item_net_amount']     = $item_price;
                $save_promocode                           = $this->promocode->record_promocode_usage($param);
            }
        }
        
        // $razorpay_order_id   = $this->session->userdata('razorpay_order_id');
        // if(!$razorpay_order_id)
        // {
        //     redirect('dashboard');exit;
        // }
        $payment_keys        = $this->settings->setting('payment_gateway');
        $api_key             = $payment_keys['as_setting_value']['setting_value']->razorpay->creditionals->key;
        $auth_token          = $payment_keys['as_setting_value']['setting_value']->razorpay->creditionals->secret;
        $config              = array(
                                        'key' => $api_key,
                                        'secret' => $auth_token
                                );
        $this->load->library('razorpay', $config);
        $payload                        = array();
        $payload['razorpay_signature']  = $razorpay_signature;
        $payload['razorpay_payment_id'] = $razorpay_payment_id;
        $payload['razorpay_order_id']   = $razorpay_order_id;
        $razorpay_response              = $this->razorpay->verify_payment_signature($payload);
        // echo "<pre>";print_r($razorpay_response);exit;
        $payment_response               = $razorpay_response['payment'];
        $payment_details                = array();
        foreach($payment_response as $key => $value) 
        {
            $payment_details[$key]      = $value;
        }
        $user_details                               = array();
        $user_details['name']                       = $user['us_name'];
        $user_details['email']                      = $user['us_email'];
        $user_details['phone']                      = $user['us_phone'];

        $payment_param                              = array();
        $payment_param['id']                        = false;
        $payment_param['ph_user_id']                = $user['id'];
        $payment_param['ph_user_details']           = json_encode($user_details);//3
        $payment_param['ph_promocode']              = json_encode($promocode_details);
        $payment_param['ph_item_other_details']     = json_encode($item_details);
        $payment_param['ph_item_id']                = $item_id;
        $payment_param['ph_item_type']              = $item_type;
        $payment_param['ph_item_name']              = $item_name;
        $payment_param['ph_item_base_price']        = $item_base_price;
        $payment_param['ph_item_discount_price']    = $item_discount_price;
        $payment_param['ph_tax_type']               = $tax_type;
        $payment_param['ph_item_code']              = $item_code;
        $course_price                               = ($item_discount_price != 0 ) ? $item_discount_price : $item_base_price;
        $gst_setting                                = $this->settings->setting('has_tax');
        $cgst                                       = ($gst_setting['as_setting_value']['setting_value']->cgst != '')?$gst_setting['as_setting_value']['setting_value']->cgst:'0';
        $sgst                                       = ($gst_setting['as_setting_value']['setting_value']->sgst != '')?$gst_setting['as_setting_value']['setting_value']->sgst:'0';
        
        if($tax_type != '1')
        { //inclusive
            $sgst_price                                 = $item_price / (100 + $sgst) * $sgst;
            $cgst_price                                 = $item_price / (100 + $cgst) * $cgst;
            $totalTaxPercentage                         = $cgst + $sgst;
            $total_course_price                         = $item_price;
        }
        else
        {
            $sgst_price                                 = ($sgst / 100) * $item_price;
            $cgst_price                                 = ($cgst / 100) * $item_price;
            $total_course_price                         = $item_price + ($sgst_price + $cgst_price);
        }

        $payment_tax_object                         = array();
        $payment_tax_object['sgst']['percentage']   = $sgst;
        $payment_tax_object['sgst']['amount']       = round($sgst_price, 2); 
        $payment_tax_object['cgst']['percentage']   = $cgst;
        $payment_tax_object['cgst']['amount']       = round($cgst_price, 2); 
        $payment_param['ph_tax_objects']            = json_encode($payment_tax_object);
    
        $payment_param['ph_item_amount_received']   = $payment_details['amount']/100;//round($total_course_price, 2);//
        $payment_param['ph_payment_mode']           = '1';

        $transaction_details                        = array();
        $transaction_details['transaction_id']      = $payment_details['id'];
        $transaction_details['bank']                = $payment_details['bank'];
        $payment_param['ph_transaction_id']         = $payment_details['id'];
        $payment_param['ph_transaction_details']    = json_encode($transaction_details);
        $payment_param['ph_account_id']             = config_item('id');;
        $payment_param['ph_payment_gateway_used']   = 'razorpay';
        $payment_param['ph_status']                 = '0';
        $payment_param['ph_payment_date']           = date('Y-m-d H:i:s');
        $subscription_param['bs_payment_details']   = $payment_param;//2

        $key                                        = md5('razorpay_payment'.$user['id']);
        $razorpay_response_keys                     = $this->memcache->get($key);
        $insert_id                                  = $razorpay_response_keys['ofabee_payment_id'];
        if(empty($insert_id))
        {
            $insert_id                              = $this->Payment_model->save_history($payment_param);
        }
        
        if($insert_id)
        {
            $order_id                               = $razorpay_response_keys['ofabee_order_id'];
            if(empty($order_id))
            {
                $order_id                           = date('Y').date('m').date('d').$insert_id;
            }
            $order_param                            = array();
            $payment_param['id']                    = $insert_id;
            $payment_param['ph_order_id']           = $order_id;
            $payment_param['ph_status']             = '1';
            if($this->Payment_model->save_history($payment_param))
            {
                
                $subscription_param['id']   = $item_id;
                $subscription_param['type'] = $item_type;
                $item_label                 = 'course';
                if($item_type == 2)
                {
                    $item_label                 = 'bundle';
                    $subscription_param['bundle_id'] = $bundle['id'];
                }
                
                
                switch ($item_type) 
                {
                    case "1":
                        $objects                = array();
                        $objects['key']         = 'course_notification_' . $item_id;
                        $callback               = 'course_notification';
                        $params                 = array('course_id' => $item_id);
                        $discussion_forum       = $this->memcache->get($objects, $callback, $params);

                        $preveleged_users       = $discussion_forum['preveleged_users'];

                        foreach($preveleged_users as $preveleged_user)
                        {
                            $notify_to[$preveleged_user] = array($user['id']);
                        }
                        //Push notification
                        $this->load->library('Notifier');
                        $notify_param =  array(
                            'action_code'   => 'purchase_notify',
                            'assets'        => array('item_type'=>'course','item_name' => $item_name,'student_name' => isset($user['us_name'])?$user['us_name']:''),
                            'target'        => $item_id,
                            'push_to'       => $notify_to
                        );
                        // echo "<pre>";print_r($notify_param);exit;
                        $this->course_subscription($subscription_param,$user);
                        $this->notifier->push($notify_param);
                        //End notification 
                        $status_code            = '200';
                        $headers                = array('error' => false,'status_code'=> $status_code,'message' => 'Your course subscription is successful');
                    break;
                    case "2":
                        $objects                = array();
                        $objects['key']         = 'bundle_notification_' . $item_id;
                        $callback               = 'bundle_notification';
                        $params                 = array('bundle_id' => $item_id);
                        $all_users              = $this->memcache->get($objects, $callback, $params);
            
                        $preveleged_users       = $all_users['preveleged_users'];
            
                        foreach($preveleged_users as $preveleged_user)
                        {
                            $notify_to[$preveleged_user] = array($user['id']);
                        }
                        //Push notification
                        $this->load->library('Notifier');
                        $notify_param =array(
                            'action_code'   => 'purchase_notify',
                            'assets'        => array('item_type'=>'bundle','item_name' => $item_name,'student_name' => isset($user['us_name'])?$user['us_name']:''),
                            'target'        => $item_id,
                            'push_to'       => $notify_to
                        );
                        // echo "<pre>";print_r($notify_param);exit;
                        $this->notifier->push($notify_param);
                        //End notification 
                        $this->bundle_subscription($subscription_param, $user);
                        $status_code            = '200';
                        $headers                = array('error' => false,'status_code'=> $status_code,'message' => 'Your bundle subscription is successful');
                    break;
                }
                $mail_param                 = array();
                $mail_param['username']     = $user['us_name'];
                $mail_param['item_type']    = $item_label ;
                $mail_param['item_name']    = $item_name;
                $mail_param['order_id']     = $insert_id;
                $mail_param['email']        = $user['us_email'];
                $this->send_invoice_mail($mail_param);
            }
        }
        $this->memcache->delete(md5('razorpay_payment'.$user['id']));
        $this->memcache->delete(md5('request_razorpay'.$user['id']));
        $this->memcache->delete(md5('promocode_'.$user['id']));
        send_response($status_code ,$headers);
           
    }

    public function discount_enrollment($param)
    {
        $this->load->library('Promocode');
        $item_id                = $param['item_id'];
        $item_type              = $param['item_type'];
        $user                   = $param['user'];
        $promocode_details   = array();
        switch ($item_type) 
        {
            case "1":
                $item_type_label    = 'course';
                $objects            = array();
                $objects['key']     = 'course_'.$item_id;
                $callback           = 'course_details';
                $params             = array('id' => $item_id);
                $course             = $this->memcache->get($objects, $callback, $params);
                $item_name          = $course['cb_title'];
                $item_base_price    = $course['cb_price'];
                $item_discount_price= $course['cb_discount'];
                $tax_type           = $course['cb_tax_method'];
                $item_price         = ($course['cb_discount']!=0)?$course['cb_discount']:$course['cb_price'];
                $item_details       = $course;
                break;
            case "2": 
            
                $subscription_param = array();
                $item_type_label    = 'bundle';
                $objects            = array();
                $objects['key']     = 'bundle_'.$item_id;
                $callback           = 'bundle_details';
                $params             = array('id' => $item_id);
                $bundle             = $this->memcache->get($objects, $callback, $params);
                $item_name          = $bundle['c_title'];
                $item_base_price    = $bundle['c_price'];
                $item_discount_price= $bundle['c_discount'];
                $tax_type           = $bundle['c_tax_method'];
                $item_price         = ($bundle['c_discount']!=0)?$bundle['c_discount']:$bundle['c_price'];
                $subscription_param['bs_bundle_details'] = array(
                                                                'id'                => $bundle['id'],
                                                                'c_title'           => $bundle['c_title'],
                                                                'c_code'            => $bundle['c_code'],
                                                                'c_courses'         => json_decode($bundle['c_courses']),
                                                                'c_access_validity' => $bundle['c_access_validity'],
                                                                'c_validity'        => $bundle['c_validity'],
                                                                'c_validity_date'   => $bundle['c_validity_date'],
                                                                'c_price'           => $bundle['c_price'],
                                                                'c_discount'        => $bundle['c_discount'],
                                                                'c_tax_method'      => $bundle['c_tax_method']
                                                            );
                $item_details       = $subscription_param['bs_bundle_details'];
                break;
        }

        $objects                                = array();
        $objects['key']                         = md5('promocode_'.$user['id']);
        $promocode                              = $this->memcache->get($objects);
        $promocode                              = $promocode['promocode'];
        if($promocode)
        {
            $user_details                          = array();
            $user_details['id']                    = $user['id'];
            $user_details['name']                  = $user['us_name'];
            $user_details['email']                 = $user['us_email'];
            $user_details['phone']                 = $user['us_phone'];
            $user_details['itemType']              = $item_type_label;
            $user_details['itemName']              = $item_name;
            $user_details['applied_on']            = date('d-m-Y H:i:s');
            $user_detail[$user['id']]              = (isset($user_details))?$user_details:array();
            $param                                 = array();
            $param['promocode']                    = $promocode;
            $param['user_details']                 = json_encode($user_detail);
            $promocode_response                    = $this->promocode->check_valid_promocode($param);
            if($promocode_response['header']['success'])
            {
                
                $discout_type                   = (isset($promocode_response['body']['promocode']['pc_discount_type']))?$promocode_response['body']['promocode']['pc_discount_type']:1;
                if($discout_type == 1) 
                {
                    $discount_percentage        = 0;
                    $discount_rate              = ($promocode_response['body']['promocode']['pc_discount_rate']!=NULL)?$promocode_response['body']['promocode']['pc_discount_rate']:0;
                } 
                else 
                {
                    $discount_percentage        = ($promocode_response['body']['promocode']['pc_discount_rate']!=NULL)?$promocode_response['body']['promocode']['pc_discount_rate']:0;
                    $discount_rate              = ($discount_percentage!=0)?round((($discount_percentage/100) * $item_price),2):0;
                } 
                $item_price                     = ($discount_rate < $item_price)?($item_price - $discount_rate):0;           
               
                /* save promocode details */
                $promocode_details['promocode']           = $promocode;
                $promocode_details['discout_type']        = $discout_type;
                $promocode_details['discount_percentage'] = $discount_percentage;
                $promocode_details['discount_rate']       = round($discount_rate, 2);
                $promocode_details['item_net_amount']     = $item_price;
                $save_promocode                           = $this->promocode->record_promocode_usage($param);
            }
        }

        $user_details                               = array();
        $user_details['name']                       = $user['us_name'];
        $user_details['email']                      = $user['us_email'];
        $user_details['phone']                      = $user['us_phone'];

        $payment_param                              = array();
        $payment_param['id']                        = false;
        $payment_param['ph_user_id']                = $user['id'];
        $payment_param['ph_user_details']           = json_encode($user_details);//4
        $payment_param['ph_promocode']              = json_encode($promocode_details);
        $payment_param['ph_item_other_details']     = json_encode($item_details);
        $payment_param['ph_item_id']                = $item_id;
        $payment_param['ph_item_type']              = $item_type;
        $payment_param['ph_item_name']              = $item_name;
        $payment_param['ph_item_base_price']        = $item_base_price;
        $payment_param['ph_item_discount_price']    = $item_discount_price;
        $payment_param['ph_tax_type']               = $tax_type;
           
        $gst_setting                                = $this->settings->setting('has_tax');
        $cgst                                       = ($gst_setting['as_setting_value']['setting_value']->cgst != '')?$gst_setting['as_setting_value']['setting_value']->cgst:'0';
        $sgst                                       = ($gst_setting['as_setting_value']['setting_value']->sgst != '')?$gst_setting['as_setting_value']['setting_value']->sgst:'0';
        
        $sgst_price                                 = '0';
        $cgst_price                                 = '0';
        $total_course_price                         = '0';
        $payment_tax_object                         = array();
        $payment_tax_object['sgst']['percentage']   = $sgst;
        $payment_tax_object['sgst']['amount']       = round($sgst_price, 2); 
        $payment_tax_object['cgst']['percentage']   = $cgst;
        $payment_tax_object['cgst']['amount']       = round($cgst_price, 2); 
        $payment_param['ph_tax_objects']            = json_encode($payment_tax_object);
        
        $payment_param['ph_item_amount_received']   = '0';
        $payment_param['ph_payment_mode']           = '1';

        $transaction_details                        = array();
        $transaction_details['transaction_id']      = '-';
        $transaction_details['bank']                = '-';
        $payment_param['ph_transaction_id']         = '-';
        $payment_param['ph_transaction_details']    = json_encode($transaction_details);
        $payment_param['ph_account_id']             = config_item('id');;
        $payment_param['ph_payment_gateway_used']   = '-';
        $payment_param['ph_status']                 = '0';
        $payment_param['ph_payment_date']           = date('Y-m-d H:i:s');
        $subscription_param['bs_payment_details']   = $payment_param;//1  

        $objects                                    = array();
        $objects['key']                             = md5('razorpay_payment'.$user['id']);
        $razorpay_request                           = $this->memcache->get($objects);
        $insert_id                                  = $razorpay_request['ofabee_payment_id'];
        if(empty($insert_id))
        {
            $insert_id                              = $this->Payment_model->save_history($payment_param);
        }
        
        if($insert_id)
        {
            $order_id                               = $razorpay_request['ofabee_order_id'];
            if(empty($order_id))
            {
                $order_id                           = date('Y').date('m').date('d').$insert_id;
            }
            $order_param                            = array();
            $payment_param['id']                    = $insert_id;
            $payment_param['ph_order_id']           = $order_id;
            $payment_param['ph_status']             = '1';
            if($this->Payment_model->save_history($payment_param))
            {
                
                $subscription_param['id']   = $item_id;
                $subscription_param['type'] = $item_type;
                $item_label                 = 'course';
                if($item_type == 2)
                {
                    $item_label                 = 'bundle';
                    $subscription_param['bundle_id'] = $bundle['id'];
                }
                $this->course_subscription($subscription_param , $user);
                
                switch ($item_type) 
                {
                    
                    case "1":

                        $objects                = array();
                        $objects['key']         = 'course_notification_' . $item_id;
                        $callback               = 'course_notification';
                        $params                 = array('course_id' => $item_id);
                        $discussion_forum       = $this->memcache->get($objects, $callback, $params);

                        $preveleged_users       = $discussion_forum['preveleged_users'];

                        foreach($preveleged_users as $preveleged_user)
                        {
                            $notify_to[$preveleged_user] = array($user['id']);
                        }

                        //Push notification
                        $this->load->library('Notifier');
                        $notify_param =  array(
                            'action_code'   => 'purchase_notify',
                            'assets'        => array('item_type'=>'course','item_name' => $item_name,'student_name' => isset($user['us_name'])?$user['us_name']:''),
                            'target'        => $item_id,
                            'push_to'       => $notify_to
                        );
                        // echo "<pre>";print_r($notify_param);exit;
                        $this->notifier->push($notify_param);
                        //End notification 

                        $status_code            = '200';
                        $headers                = array('error' => false,'status_code'=> $status_code,'message' => 'Your course subscription is successful');
                    
                    break;
                    case "2":

                        $objects                = array();
                        $objects['key']         = 'bundle_notification_' . $item_id;
                        $callback               = 'bundle_notification';
                        $params                 = array('bundle_id' => $item_id);
                        $all_users              = $this->memcache->get($objects, $callback, $params);
            
                        $preveleged_users       = $all_users['preveleged_users'];
            
                        foreach($preveleged_users as $preveleged_user)
                        {
                            $notify_to[$preveleged_user] = array($user['id']);
                        }

                        //Push notification
                        $this->load->library('Notifier');
                        $notify_param = array(
                            'action_code'   => 'purchase_notify',
                            'assets'        => array('item_type'=>'bundle','item_name' => $item_name,'student_name' => isset($user['us_name'])?$user['us_name']:''),
                            'target'        => $item_id,
                            'push_to'       => $notify_to
                        );
                        // echo "<pre>";print_r($notify_param);exit;
                        $this->notifier->push($notify_param);
                        //End notification //
                        $this->bundle_subscription($subscription_param,$user);

                        $status_code            = '200';
                        $headers                = array('error' => false,'status_code'=> $status_code,'message' => 'Your bundle subscription is successful');
                    break;
                }
                $mail_param                 = array();
                $mail_param['username']     = $user['us_name'];
                $mail_param['item_type']    = $item_label ;
                $mail_param['item_name']    = $item_name;
                $mail_param['order_id']     = $insert_id;
                $mail_param['email']        = $user['us_email'];
                $this->send_invoice_mail($mail_param);
            }
        }
        $this->memcache->delete(md5('razorpay_payment'.$user['id']));
        $this->memcache->delete(md5('request_razorpay'.$user['id']));
        $this->memcache->delete(md5('promocode_'.$user['id']));
        send_response($status_code ,$headers);
    }

    private function bundle_subscription($param = array(), $user = array())
    {
        $this->load->model(array('Bundle_model'));
        $bundle_id                  = isset($param['id'])?$param['id']:false;
        if(!$bundle_id) 
        {
            $status_code            = '204';
            $headers                = array('error' => true,'status_code'=>$status_code,'message' => 'Subscription not successful');
            send_response($status_code ,$headers);
        }
        
        $user_id                    = $user['id'];
        $enrolled_courses           = array();
        $notification_ids           = array();
        $payment_data               = array();

        $objects                        = array();
        $objects['key']                 = 'bundle_'.$bundle_id;
        $callback                       = 'bundle_details';
        $params                         = array('id' => $bundle_id);
        $bundle                         = $this->memcache->get($objects, $callback, $params);
        //$this->load->model('Bundle_model');
        $user_bundle_subscription       = $this->Bundle_model->bundle_subscription_details(array('bundle_id' => $bundle_id, 'user_id' => $user_id));
        $bundle_subscription_id         = !empty($user_bundle_subscription) && $user_bundle_subscription['id'] ? $user_bundle_subscription['id'] : false;
        $update                         = !empty($user_bundle_subscription) && $user_bundle_subscription['id'] ? true : false;
        
        if(!empty($user_id))
        {
            $email_content                      = array();
            $save_userdata                      = array();
            $save_subscribe                     = array();
            $bundle_name                        = $bundle['c_title'];

            $save                               = array();
            $save['bs_bundle_id']               = $bundle_id;
            $save['bs_user_id']                 = $user_id;
            $save['bs_subscription_date']       = date('Y-m-d H:i:s');
            $save['bs_start_date']              = date('Y-m-d');
            $save['bs_course_validity_status']  = $bundle['c_access_validity'];
            $save['bs_user_groups']             = '';
            $save['bs_payment_details']         = isset($param['bs_payment_details']) ? json_encode($param['bs_payment_details']) : '';//4
            $save['bs_bundle_details']          = isset($param['bs_bundle_details']) ? json_encode($param['bs_bundle_details']) : '';
            
            $bundle_id                          = isset($param['id']) ? $param['id'] : false;
            $notification_ids[]                 = $user_id;
            if ($bundle['c_access_validity'] == 2) {
                $bundle_enddate                 = $bundle['c_validity_date'];
            } else if ($bundle['c_access_validity'] == 0) {
                $bundle_enddate                 = date('Y-m-d', strtotime('+3000 days'));
            } else {
                $duration                       = ($bundle['c_validity']) ? $bundle['c_validity']-1 : 0;
                $bundle_enddate                 = date('Y-m-d', strtotime('+' . $duration . ' days'));
            }                    
            $save['bs_end_date']                = $bundle_enddate;
            $save['bs_approved']                = '1';
            $save['action_by']                  = $user_id;         
            if($save)
            {
                $this->Bundle_model->save_subscription($save, array('update' => $update, 'id' => $bundle_subscription_id));
                $bundle_courses                 = json_decode($bundle['c_courses'],true);
                $course_ids                     = !empty($bundle_courses) ? array_column($bundle_courses, 'id') : array();
                if(!empty($course_ids))
                {
                    $users_to_subscribe         = $this->Bundle_model->migrateCourseSubscription(array('bundle_id' => $bundle_id, 'course_ids' => $course_ids, 'user_ids' => array($user_id)));
                    if(!empty($users_to_subscribe))
                    {
                        $course_param               = array();
                        $course_param['id']         = $bundle_id;
                        $course_param['bundle_id']  = $bundle_id;
                        $course_param['type']       = '2';
                        $this->course_subscription($course_param , $user);    
                    }
                }
                if ($bundle_id) {
                    $this->memcache->delete('bundle_' . $bundle_id);
                    $this->memcache->delete('bundle_enrolled_' . $user_id);
                    $this->memcache->delete('enrolled_item_ids_' . $user_id);
                    $this->memcache->delete('mobile_enrolled_'.$user_id);
                    // $this->memcache->delete('my_bundle_subscriptions');
                } else {
                    $this->memcache->delete('all_courses');
                    $this->memcache->delete('sales_manager_all_sorted_courses');
                    $this->memcache->delete('top_courses');
                }
            }              
            
            return true;
        } else {
            return false;
        }
        
    }
    private function free_course_subscription($param = array())
    {
        $this->load->library('Promocode');
        $this->load->model(array('order_model','Payment_model','Bundle_model'));
        $token                      = isset($param['token'])?$param['token']:'';
        $item_id                    = isset($param['body']['item_id'])?$param['body']['item_id']:'';
        $item_type                  = isset($param['body']['item_type'])?$param['body']['item_type']:'';
        
        if($token != '')
        {
            $user_details           = $this->api_authentication->verify_token($token);
            if($user_details['token_verified'] == true)
            {
                $user               = !empty($user_details['user'])?$user_details['user']:array();
                
            }
            else
            {
                $status_code        = '401';
                $headers            = array('error' => true,'status_code'=>$status_code,'message' => 'Please login to continue');
                send_response($status_code ,$headers);
            }
        }
        else
        {
            $status_code            = '401';
            $headers                = array('error' => true,'status_code'=>$status_code,'message' => 'Please login to continue');
            send_response($status_code ,$headers);
        }

        if($item_type == '1')
        {
            $data                               = array();
            $notify_to[config_item('us_id')]    = array($user['id']);
            $objects                            = array();
            $objects['key']                     = 'course_'.$item_id;
            $callback                           = 'course_details';
            $params                             = array('id' => $item_id);
            $course                             = $this->memcache->get($objects, $callback, $params);
            
            if($course['cb_has_self_enroll'] == 0)
            {
                $status_code    = 204;
                $headers        = array('error' => true,'status_code'=>$status_code,'message' => 'This course doesnot support self enrollment contact Admin for more.');
                send_response($status_code ,$headers);
            }

            if(!$course['cb_is_free'] || $course['cb_is_free'] !='1')
            {
                $status_code    = 204;
                $headers        = array('error' => true,'status_code'=>$status_code,'message' => 'This course doesnot support free enrollment contact Admin for more.');
                send_response($status_code ,$headers);
            }

            $course_subscription_date   = date("Y-m-d H:i:s");
            $course_validity            = (($course['cb_validity']) && $course['cb_validity'] > 0)?$course['cb_validity']:1;
            $course_startdate           = date("Y-m-d",time());  
            
            if($course['cb_access_validity'] == 2)
            {
                $course_enddate   = $course['cb_validity_date'];
            }
            else
            {
                $course_enddate   = ($course['cb_access_validity'] == 0) ? date('Y-m-d', strtotime('+3000 days')) : date('Y-m-d', time() + ($course_validity - 1) * 60 * 24 * 60);
            }

            $cs_save                            = array();
            
            if($subscription = $this->Payment_model->check_subscription(array('course_id'=>$item_id, 'user_id'=>$user['id'])))
            {
                $cs_save['id']                  = $subscription['id'];
                $cs_save['action_id']           = $this->actions['update'];
                $cs_save['updated_date']        = $course_subscription_date;
            }
            else
            {
                $cs_save['id']                  = false;
                $cs_save['action_id']           = $this->actions['create'];
            }
       
            $cs_save['cs_user_groups']          = $user['us_groups'];
            $cs_save['cs_course_id']            = $item_id;
            $cs_save['cs_user_id']              = $user['id'];
            $cs_save['cs_approved']             = '1';
            $cs_save['cs_subscription_date']    = $course_subscription_date;
            $cs_save['cs_start_date']           = $course_startdate;
            $cs_save['cs_end_date']             = $course_enddate;
            $cs_save['action_by']               = $user['id'];
            $cs_save['cs_course_validity_status']= $course['cb_access_validity'];
            
            $status_code    = 204;
            $headers        = array('error' => false,'status_code'=>$status_code,'message' => 'Failed to enroll to course');
            $saved_status   = $this->Payment_model->save($cs_save);
            
            if($saved_status)
            {
            
                //Invalidate cahe
                $this->memcache->delete('enrolled_'.$user['id']);
                
                $this->memcache->delete('mobile_enrolled_'.$user['id']);
                $this->memcache->delete('enrolled_item_ids_' . $user['id']);
                $this->Payment_model->remove_wishlist($cs_save);
                $status_code    = 200;
                $headers        = array('error' => false,'status_code'=>$status_code,'message' => 'Successfully enrolled');
            }
        
            $this->load->library('Ofapay');
            
            $param                      = array();
            $param['user_id']           = $user['id'];
            $param['item_id']           = $item_id;
            $param['amount']            = '0';
            $param['payment_mode']      = '2';
            $param['ph_item_name']      = $course['cb_title'];
            $param['ph_item_code']      = $course['cb_code'];
            $param['ph_user_details']   = array('name' => $user['us_name'], 'email' => $user['us_email'], 'phone'=> $user['us_phone']);
            $param['ph_status']         = '1';
            
            $this->ofapay->save_payment($param);
            
            //send notification to admin
            $this->load->model('Tutor_model');
            $tutors             = $this->Tutor_model->get_tutor_name_by_course($course['id']);

            $param              = array();
            $param['ids']       = array();
            $param['ids'][]     = $this->config->item('us_id');
            $mail_ids           = array($this->config->item('site_email'));
        
            if(!empty($tutors))
            {
                foreach($tutors as $tutor)
                {
                    $param['ids'][] = $tutor['id'];
                    $mail_ids[]     = $tutor['us_email'];
                }
            }
            //End
            $institute_admins = array();

            //Institute admin
            $this->load->model(array('User_model'));
            $institute              = $this->User_model->users(array( 'institute_id'=>$user['us_institute_id'],'role_id'=>'8','status'=>'1','not_deleted'=>true, 'select' => 'users.us_email,users.id'));
            if(!empty($institute)) 
            {
                // echo "<pre>";print_r($institute);exit;
                foreach($institute as $i_admin)
                {
                    //$institute_admins[]  = $i_admin['id'];
                    if($i_admin['id'])
                    {
                        $notify_to[$i_admin['id']] = array($user['id']);
                    }
                    $mail_ids[]          = $i_admin['us_email'];
                }
            }
            //End ins admin
            
            //Send email using template.
            $user_param                 = array();
            $user_param['ids']          = $mail_ids;
            $user_param['user_name']    = $user['us_name'];
            $user_param['course_name']  = $course['cb_title'];
            $user_param['course_id']    = $course['id'];

            $curl_param                 = array();
            $curl_param['data']         = $user_param;
            $curl_param['url']          = site_url()."mobile_api/course_subscription_notification";
            $this->send_curl($curl_param);
            
            $objects                = array();
            $objects['key']         = 'course_notification_' . $item_id;
            $callback               = 'course_notification';
            $params                 = array('course_id' => $item_id);
            $discussion_forum       = $this->memcache->get($objects, $callback, $params);

            $preveleged_users       = $discussion_forum['preveleged_users'];

            foreach($preveleged_users as $preveleged_user)
            {
                $notify_to[$preveleged_user] = array($user['id']);
            }
        
            $this->load->library('Notifier');
            $this->notifier->push(
                array(
                    'action_code' => 'course_subscribed',
                    'assets' => array('course_name' => $course['cb_title'],'student_name'=>$user['us_name'],'course_id' => $course['id']),
                    'target' => $course['id'],
                    'individual' => false,
                    'push_to' => $notify_to
                )
            );
            //End notifying.

            /*Log creation*/
            $user['activity']                   = 'course_subscribed';
            $user['course_name']                = ' '.$course['cb_title'];
            $this->site_log_activity($user);
            $this->memcache->delete('all_courses');
            $this->memcache->delete('sales_manager_all_sorted_courses');
        
            send_response($status_code ,$headers);

        }
        else if($item_type == '2')
        {
            $subscription_param         = array();
            $subscription_param['id']   = $item_id;
            $subscription_param['type'] = $item_type;
            $status_count               = 0;

            $bundle_param               = array();
            $bundle_param['select']     = 'id,c_title,c_code,c_courses,c_access_validity,c_validity,c_validity_date,c_price,c_discount,c_tax_method,c_is_free';  
            $bundle_param['bundle_id']  = $item_id;
            $bundle                     = $this->Bundle_model->bundle($bundle_param);
            
            if(!$bundle['c_is_free'] || $bundle['c_is_free'] !='1')
            {
                $status_code            = '204';
                $headers                = array('error' => true,'status_code'=>$status_code,'message' => 'Opps! The course is not available for free right now!');
                send_response($status_code ,$headers);

            }
        
            $subscription_param['bs_bundle_details'] = array(
                                        'id'                => $bundle['id'],
                                        'c_title'           => $bundle['c_title'],
                                        'c_code'            => $bundle['c_code'],
                                        'c_courses'         => json_decode($bundle['c_courses']),
                                        'c_access_validity' => $bundle['c_access_validity'],
                                        'c_validity'        => $bundle['c_validity'],
                                        'c_validity_date'   => $bundle['c_validity_date'],
                                        'c_price'           => $bundle['c_price'],
                                        'c_discount'        => $bundle['c_discount'],
                                        'c_tax_method'      => $bundle['c_tax_method'],
                                        'bundle_id'         => $bundle['id']
                                    );
            //echo "<pre>";print_r($subscription_param);die;
            $subscription_param['bundle_id'] = $bundle['id'];
            $status_code        = 204;
            $headers            = array('error' => true,'status_code'=>$status_code,'message' => 'failed to enrolled');
            if($this->bundle_subscription($subscription_param , $user))
            {
                $user_id                = $user['id'];
                $objects                = array();
                $objects['key']         = 'bundle_notification_' . $item_id;
                $callback               = 'bundle_notification';
                $params                 = array('bundle_id' => $item_id);
                $all_users              = $this->memcache->get($objects, $callback, $params);

                $preveleged_users       = $all_users['preveleged_users'];

                foreach($preveleged_users as $preveleged_user)
                {
                    $notify_to[$preveleged_user] = array($user_id);
                }
            
                //Push notification
                $this->load->library('Notifier');
                $this->notifier->push(
                    array(
                        'action_code'   => 'bundle_subscribed',
                        'assets'        => array('bundle_name' => $bundle['c_title'],'student_name' => isset($user['us_name'])?$user['us_name']:'','bundle_id' => $item_id),
                        'target'        => $item_id,
                        'individual'    => false,
                        'push_to'       => $notify_to
                    )
                );
                //End notification
                
                $status_count++;
                
            }

            if($this->course_subscription($subscription_param , $user))
            {
                $status_count++;
            }

            if($status_count == 2)
            {

                $user_details                               = array();
                $user_details['name']                       = $user['us_name'];
                $user_details['email']                      = $user['us_email'];
                $user_details['phone']                      = $user['us_phone'];

                $payment_param                              = array();
                $payment_param['id']                        = false;
                $payment_param['ph_user_id']                = $user['id'];
                $payment_param['ph_user_details']           = json_encode($user_details);//2 
                $payment_param['ph_item_other_details']     = json_encode( $subscription_param['bs_bundle_details']);
                $payment_param['ph_promocode']              = '';
                $payment_param['ph_item_id']                = $item_id;
                $payment_param['ph_item_type']              = $item_type;
                $payment_param['ph_item_name']              = $bundle['c_title'];
                $payment_param['ph_item_base_price']        = $bundle['c_price'];
                $payment_param['ph_item_discount_price']    = $bundle['c_discount'];
                $payment_param['ph_tax_type']               = $bundle['c_tax_method'];
                $payment_param['ph_item_code']              = $bundle['c_code'];
                $payment_param['ph_tax_objects']            = '';
                $payment_param['ph_item_amount_received']   = '0';
                $payment_param['ph_payment_mode']           = '2';
                $payment_param['ph_transaction_id']         = '';
                $payment_param['ph_transaction_details']    = '';
                $payment_param['ph_account_id']             = config_item('id');;
                $payment_param['ph_payment_gateway_used']   = '';
                $payment_param['ph_status']                 = '1';
           
                $payment_param['ph_payment_date']           = date('Y-m-d H:i:s');

                $ph_insert_id   = $this->Payment_model->save_history($payment_param);
                $payment_param_of_order_id['ph_order_id']               = date('Y').date('m').date('d').$ph_insert_id ;
                $payment_param_of_order_id['id']                        = $ph_insert_id ;
                $this->Payment_model->save_history($payment_param_of_order_id);      

                $status_code    = 200;
                $headers        = array('error' => false,'status_code'=>$status_code,'message' => 'Successfully enrolled');
                
            }
            $this->memcache->delete('enrolled_item_ids_'.$user_id);
            $this->memcache->delete('mobile_enrolled_'.$user_id);
            send_response($status_code ,$headers);
        }
        
    }
    private function course_subscription($param = array(), $user = array()) 
    {
        $user_id             = $user['id'];
        $id                  = $param['id'];
        $type                = $param['type'];
        $courses             = array();
        $bundle_id           = isset($param['bundle_id']) ? $param['bundle_id'] : 0;
        $return              = false;
        switch ($type) 
        {
            case "1":
                $courses[]              = $id;
                $course_objects         = array();
                $course_objects['key']  = 'course_'.$id;
                $course_callback        = 'course_details';
                $course_params          = array('id' => $id);
                $course_details         = $this->memcache->get($course_objects, $course_callback, $course_params);
                $item_name              = $course_details['cb_title']; 
                $course_validity_status = $course_details['cb_access_validity'];
                $course_validity        = (($course_details['cb_validity']) && $course_details['cb_validity'] > 0)?$course_details['cb_validity']:1;
                if($course_details['cb_access_validity'] == 2)
                {
                    $course_enddate   = $course_details['cb_validity_date'];
                }
                else
                {
                    $course_enddate   = ($course_details['cb_access_validity'] == 0) ? date('Y-m-d', strtotime('+3000 days')) : date('Y-m-d', time() + ($course_validity - 1) * 60 * 24 * 60);
                }
                
            break;
            case "2":
                $objects        = array();
                $objects['key'] = 'bundle_'.$id;
                $callback       = 'bundle_details';
                $params         = array('id' => $id);
                $bundles        = $this->memcache->get($objects, $callback, $params);
                $course_details = json_decode($bundles['c_courses'],TRUE);
                
                foreach($course_details as $course_detail)
                {
                    $courses[]          = $course_detail['id'];
                }
                $item_name              = $bundles['c_title'];
                $course_validity_status = $bundles['c_access_validity'];
                if ($bundles['c_access_validity'] == 2) 
                {
                    $course_enddate     = $bundles['c_validity_date'];
                } 
                else if ($bundles['c_access_validity'] == 0) 
                {
                    $course_enddate     = date('Y-m-d', strtotime('+3000 days'));
                } 
                else 
                {
                    $duration           = ($bundles['c_validity']) ? $bundles['c_validity']-1 : 0;
                    $course_enddate     = date('Y-m-d', strtotime('+' . $duration . ' days'));
                }                    
                break;
        }
        if(!empty($courses))
        {
            foreach($courses as $course_id)
            {
                $course_objects        = array();
                $course_objects['key'] = 'course_'.$course_id;
                $course_callback       = 'course_details';
                $course_params         = array('id' => $course_id);
                $course                = $this->memcache->get($course_objects, $course_callback, $course_params);

                $course_subscription_date   = date("Y-m-d H:i:s");
                $course_startdate           = date("Y-m-d",time());  
                $cs_save                                = array();
                
                if($subscription = $this->Payment_model->check_subscription(array('course_id'=>$course_id, 'user_id'=>$user['id'])))
                {
                    $cs_save['id']                      = $subscription['id'];
                    // $cs_save['action_id']               = $this->actions['update'];
                    $cs_save['updated_date']            = $course_subscription_date;
                    if($type == 2)
                    {
                        $bs_bundle_ids                  = explode(',', $subscription['cs_bundle_id']);
                    
                        if(!in_array($bundle_id, $bs_bundle_ids))
                        {
                            $cs_save['cs_bundle_id']        = $subscription['cs_bundle_id'].','.$bundle_id;
                        }
                    }
                }
                else
                {
                    $cs_save['id']                      = false;
                    if($type == 2)
                    {
                        $cs_save['cs_bundle_id']        = $bundle_id;
                    }
                }
                $cs_save['cs_user_groups']          = $user['us_groups'];
                $cs_save['cs_course_id']            = $course_id;
                $cs_save['cs_user_id']              = $user['id'];
                $cs_save['cs_approved']             = '1';
                if($type == 2)
                {
                    $cs_save['cs_bundle_id']        = $bundle_id;
                }
                $cs_save['cs_subscription_date']    = $course_subscription_date;
                $cs_save['cs_start_date']           = $course_startdate;
                $cs_save['cs_end_date']             = $course_enddate;
                $cs_save['action_by']               = $user['id'];
                $cs_save['cs_course_validity_status']= $course_validity_status;
                if($this->Payment_model->save($cs_save))
                {
                    $return                         = true;
                    //Invalidate cahe
                    $this->memcache->delete('enrolled_'.$user['id']);  
                    $this->memcache->delete('mobile_enrolled_'.$user['id']);                    
                    if ($course_id) 
                    {
                        $this->memcache->delete('course_' . $course_id);
                        $this->memcache->delete('my_subscriptions');
                        $this->memcache->delete('enrolled_item_ids_' . $user['id']);
                    } 
                    else
                    {
                        $this->memcache->delete('all_courses');
                        $this->memcache->delete('sales_manager_all_sorted_courses');
                        $this->memcache->delete('top_courses');
                    }
                    $this->Payment_model->remove_wishlist($cs_save);
                }
        
                //send notification to admin
                $this->load->model('Tutor_model');
                $tutors             = $this->Tutor_model->get_tutor_name_by_course($course_id);
                //echo '<pre>';print_r($tutors);die;            
            
            
                $param              = array();
                $param['ids']       = array();
                $param['ids'][]     = $this->config->item('us_id');
                $mail_ids           = array($this->config->item('site_email'));
                if(!empty($tutors))
                {
                    foreach($tutors as $tutor)
                    {
                        $param['ids'][] = $tutor['id'];
                        $mail_ids[]     = $tutor['us_email'];
                    }
                }
                //End
                $institute_admins = array();

                //Institute admin
                $this->load->model(array('User_model'));
                $institute              = $this->User_model->users(array( 'institute_id'=>$user['us_institute_id'],'role_id'=>'8','status'=>'1','not_deleted'=>true, 'select' => 'users.us_email,users.id'));
                if(!empty($institute)) 
                {
                    // echo "<pre>";print_r($institute);exit;
                    foreach($institute as $i_admin)
                    {
                        //$institute_admins[]  = $i_admin['id'];
                        if($i_admin['id'])
                        {
                            $notify_to[$i_admin['id']] = array($user['id']);
                        }
                        $mail_ids[]          = $i_admin['us_email'];
                    }
                }
            }

            switch ($type) 
            {
                case "1":
                    //Send email using template.
                    $user_param                 = array();
                    $user_param['ids']          = $mail_ids;
                    $user_param['user_name']    = $user['us_name'];
                    $user_param['course_name']  = $item_name;

                    $curl_param                 = array();
                    $curl_param['data']         = $user_param;
                    $curl_param['url']          = site_url()."mobile_api/course_enrollment_notification";
                    $this->send_curl($curl_param);

                    /*Log creation*/
                    $user['activity']                   = 'course_subscribed';
                    $user['course_name']                = ' '.$item_name;
                    $this->site_log_activity($user);
                    break;
                case "2":
                    //Send email using template.
                    $user_param                 = array();
                    $user_param['ids']          = $mail_ids;
                    $user_param['user_name']    = $user['us_name'];
                    $user_param['bundle_name']  = $item_name;

                    $curl_param                 = array();
                    $curl_param['data']         = $user_param;
                    $curl_param['url']          = site_url()."mobile_api/bundle_enrollment_notification";
                    $this->send_curl($curl_param);
                    
                    /*Log creation*/
                    $user['activity']                   = 'bundle_subscribed';
                    $user['bundle_name']                = ' '.$item_name;
                    $this->site_log_activity($user);
                    break;
            }
        }
        $this->memcache->delete('all_courses');    
        $this->memcache->delete('sales_manager_all_sorted_courses');    
        return $return;
        
    }

    private function reset_coupon($param)
    {
        $token                      = isset($param['token'])?$param['token']:'';
        if($token != '')
        {
            $user_details           = $this->api_authentication->verify_token($token);
            if($user_details['token_verified'] == true)
            {
                $user               = !empty($user_details['user'])?$user_details['user']:array();
            }
            else
            {
                $status_code        = '401';
                $headers            = array('error' => true,'status_code'=>$status_code,'message' => 'Please login to continue');
                send_response($status_code ,$headers);
            }
        }
        else
        {
            $status_code            = '401';
            $headers                = array('error' => true,'status_code'=>$status_code,'message' => 'Please login to continue');
            send_response($status_code ,$headers);
        }
        $objects                    = array();
        $objects['key']             = md5('promocode_'.$user['id']);
        $promocode                  = $this->memcache->get($objects);
        $promocode                  = $promocode['promocode'];
        $status_code                = '204';
        $headers                    = array('error' => true,'status_code'=> $status_code,'message' => 'No Promocode exist');
        if($promocode)
        {
            $this->memcache->delete(md5('promocode_'.$user['id']));
            $status_code            = '200';
            $headers                = array('error' => false,'status_code'=> $status_code,'message' => 'Promocode removed');
        }
        send_response($status_code ,$headers);
    }

    private function send_invoice_mail($params = array())
    {
        $username       = isset($params['username'])?$params['username']:'';
        $item_type      = isset($params['item_type'])?$params['item_type']:'';
        $item_name      = isset($params['item_name'])?$params['item_name']:'';
        $order_id       = isset($params['order_id'])?$params['order_id']:'';
        $email          = isset($params['email'])?$params['email']:'';
        if(empty($order_id))
        {
            $status_code        = '404';
            $headers            = array('error' => true,'status_code'=>$status_code,'message' => 'Details not available now.');
            send_response($status_code ,$headers);
        }
        $invoice_link   = site_url().'cron_job/payment_invoice/'.$order_id;

        $template               = $this->ofabeemailer->template(array('email_code' => 'student_invoice'));
        $param_admin            = array();
        $param_admin['to'] 	    = array($email);
        $param_admin['subject'] = $template['em_subject'];
        $contents               = array(
                                    'username' => $username,
                                    'item_type'=> $item_type,
                                    'course_name'=> $item_name,
                                    'invoice_link'=> $invoice_link,
                                    'date' => date("d-m-Y",time()),
                                    'site_name' => config_item('site_name')
                                );
        $param_admin['body']    = $this->ofabeemailer->process_mail_content($contents, $template['em_message']);
        $send_admin             = $this->ofabeemailer->send_mail($param_admin);
    }
    /*
    purpose     : Instructor details
    params      : instructor_id
    developer   : lineesh
    edited      : none
   */    
    private function instructor_info( $param = array() )
    {
        $headers                        = array();
        $body                           = array();
        $instructor_id                  = isset($param['instructor_id'])?$param['instructor_id']:'';
        if($instructor_id == '')
        {
            $this->__status_code        = '204';
            $headers                    = array('error' => true, 'message' => 'This Instructor details not available right now.', 'status_code' => '404');
            send_response($this->__status_code ,$headers);
        }
        $objects                        = array();
        $objects['key']                 = 'instructor_'.$instructor_id;
        $callback                       = 'instructor_details';
        $params                         = array('id' => $instructor_id);
        $instructor_details             = $this->memcache->get($objects, $callback, $params);
        //this section need to be checked if existing scenario changes need to delete this section
        if($instructor_details['us_deleted'] == '1' || sizeof($instructor_details) == '0')
        {
            $this->__status_code        = '204';
            $headers                    = array('error' => true, 'message' => 'This Instructor details not available right now.', 'status_code' => '404');
            send_response($this->__status_code ,$headers);
        }
        $this->__status_code            = '200';
        $headers                        = array('error' => false, 'message' => 'Instructor details fetched successfully.', 'status_code' => '200');
        $body                           = $instructor_details;
        send_response($this->__status_code ,$headers, $body);
    }


    
    /*
    purpose     : view events list
    params      : user_id, date, flag(call from another function)
    developer   : lineesh
    edited      : none
   */
  public function events( $param = array(), $user = array() )
  {
      $this->load->library(array('ofabeeevents'));
      $headers                        = array();
      $body                           = array();
      $user_id                        = isset($user['id'])?$user['id']:'';
      // date format shoud be '2019-07-18'
      $date                           = isset($param['date'])?$param['date']:'';
      $flag                           = isset($param['flag'])?$param['flag']:'';
      if($date == '')
      {
          $this->__status_code        = '404';
          $headers                    = array('error' => true, 'message' => 'Please provide the Date', 'status_code' => '404');
          send_response($this->__status_code ,$headers); 
      }
      $range                          = date("Y-m-d", strtotime($date));
      $lectures['assignments']        = $this->User_model->get_assignment_datewise(array('user_id' => $user_id, 'from' => $range, 'to' => $range));
      $lectures['live']               = $this->User_model->get_live_datewise(array('user_id' => $user_id, 'from' => $range, 'to' => $range));
      $custom_events                  = $this->ofabeeevents->getEvents(array('user_id'   => $user_id, 'date_from' => $range, 'date_to' => $range));
      $lectures['events']             = isset($custom_events['events']) ? $custom_events['events'] : array();
      $events                         = array();
      $sorted_array                   = array();
      if(!empty($lectures['assignments']))
      {
          foreach ($lectures['assignments'] as $assignment) 
          {
              $events[]               = array(
                                              'id' => $assignment['id'], 
                                              'event_name' => $assignment['cl_lecture_name'], 
                                              'event_image' => '',
                                              'event_link' => site_url('materials/course').'/'.$assignment['cl_course_id'].'/'.$assignment['id']
                                              );
              $sorted_array[$assignment['id'].'_assignment'] = $assignment['descrptive_date_time'];
          }
      }
      if(!empty($lectures['live']))
      {
          foreach ($lectures['live'] as $live) 
          {
              $events[]               = array(
                                              'id' => $live['id'],  
                                              'event_name' => $live['cl_lecture_name'], 
                                              'event_image' => '',
                                              'event_link' => (($live['ll_mode'] == 2) ? (base_url('conference') . '/?name=' . $user['us_name'] . '&userid=' . $user['id'] . '&room=' . $live['live_id'] . '&type=viewer&app=web') : (site_url('/live') . '/join/' . $live['live_id']))
                                          );
              $sorted_array[$live['id'].'_live'] = $live['live_date_time'];
          }
      }
      if (!empty($lectures['events'])) 
      {
          foreach ($lectures['events'] as $event) 
          {
              $events[]               = array(
                                              'id' => $event['id'], 
                                              'event_name' => $event['ev_name'], 
                                              'event_image' => '',
                                              'event_link' => site_url('events').'/event/'.base64_encode($event['id'])
                                          );
              $sorted_array[$event['id'].'_event'] = $event['events_date_time'];
          }
      }
      array_multisort($sorted_array, SORT_ASC, $events);
      $event_details                  = array();
      foreach ($events as $event) 
      {
          $event_details[]            = array(
                                              'id'          => $event['id'],
                                              'event_name'  => $event['event_name'],
                                              'event_image' => $event['event_image'],
                                              'event_link'  => $event['event_link']
                                              );
      }
      if($flag == '1')
      {
          return $event_details;

      }
      else
      {
          if(empty($event_details))
          {
              $this->__status_code        = '404';
              $headers                    = array('error' => true, 'message' => 'Events not found.', 'status_code' => '404');
              send_response($this->__status_code ,$headers);
          }
          $this->__status_code            = '200';
          $headers                        = array('error' => false, 'message' => 'Events fetched successfully.', 'status_code' => '200');
          $body                           = $event_details;
          send_response($this->__status_code ,$headers, $body);
      }
  }
      /*
    purpose     : checking for new notification occurence of logged in user
    params      : user_id
    developer   : lineesh
    edited      : none
   */
  private function notify_new( $param = array() ,$user = array())
  {
      $this->load->library('Notifier');
      $headers                            = array();
      $body                               = array();
      $user_id                            = isset($user['id'])?$user['id']:'';
      if($user_id == '')
      {
          $this->__status_code            = '404';
          $headers                        = array('error' => true, 'message' => 'Notifications not found.', 'status_code' => '404');
          send_response($this->__status_code ,$headers);
      }
      $notification_count                 = $this->notifier->get_notifiction_count(array('user_id' => $user_id));
      $status_code                        = '200';
      $headers                            = array('error' => false, 'message' => 'No notification available!', 'status_code' => '200');
      $body                               = array('notifications'=>$notification_count);
      if($notification_count > 0)
      {
          $status_code                    = '200';
          $headers                        = array('error' => false, 'message' => 'New notification available!', 'status_code' => '200');
          $body                           = array('notifications'=>$notification_count);
      }
      send_response($status_code ,$headers, $body);
  }
  /*
  purpose     : System notification of logged in user
  params      : user_id
  developer   : lineesh
  edited      : none
 */
  private function notifications( $param = array(),$user = array())
  {
      $this->load->library('Notifier');
      $headers                            = array();
      $body                               = array();
      $user_id                            = isset($user['id'])?$user['id']:'';

      if($user_id == '')
      {
          $this->__status_code            = '404';
          $headers                        = array('error' => true, 'message' => 'Notifications not found.', 'status_code' => '404');
          send_response($this->__status_code ,$headers);
      }

      $notification_count                 = $this->notifier->get_notifiction_count(array('user_id' => $user_id));
      if($notification_count > 0)
      {
          $notifications                  = $this->notifier->fetch(array('user_id' => $user_id));
      }
      else
      {
          $notifications                  = $this->notifier->read(array('user_id' => $user_id));
      }

      $this->__status_code                = '404';
      $headers                            = array('error' => true, 'message' => 'Notifications not found.', 'status_code' => '404');
      if($notifications['notifications'])
      {
          $response                       = array();
          foreach($notifications['notifications'] as $notify)
          {
              $response[]                 = $notify;
          }
          $this->__status_code            = '200';
          $headers                        = array('error' => false, 'message' => 'Notifications Fetched Successfully!', 'status_code' => '200');
          $body                           = $response;
      }
      send_response($this->__status_code ,$headers, $body);
  }

  /*
  purpose     : Clear all notification of logged in user
  params      : user_id
  developer   : lineesh
  edited      : none
 */
  private function clear_notification( $param = array(),$user = array() )
  {
      $this->load->model(array('Notifier_model'));
      $headers                            = array();
      $user_id                            = isset($user['id'])?$user['id']:'';
      if($user_id == '')
      {
          $this->__status_code            = '404';
          $headers                        = array('error' => true, 'message' => 'Notifications not found.', 'status_code' => '404');
          send_response($this->__status_code ,$headers);
      }
      $notification                       = $this->Notifier_model->clear_notification(array('um_user_id' => $user_id, 'um_messages' => '{}'));
      $this->__status_code                = '404';
      $headers                            = array('error' => true, 'message' => 'Unable to clear Notification.', 'status_code' => '404');
      if($notification)
      {
          $this->__status_code            = '200';
          $headers                        = array('error' => false, 'message' => 'Notifications Cleared Successfully!', 'status_code' => '200');
      }
      send_response($this->__status_code ,$headers);
  }
  
  /*
  purpose     : fetch all events when logged-in with pagination
  params      : offset, limit, date, 
  developer   : kiran
  edited      : none
 */
  private function more_events($params = array(),$user = array())
  {
      $user_id            = isset($user['id'])? $user['id']:'';
      $date               = isset($params['date'])?$params['date']:'';
      $offset             = empty($params['offset'])? 0:$params['offset'];
      $limit              = empty($params['limit'])? 8:$params['limit'];
      $body_label         = 'events';
      if($date == '')
      {
          $status_code    = '404';
          $headers        = array('error' => true, 'message' => 'specify date', 'status_code' => '404');
          send_response($status_code ,$headers);
      }
      if($user_id == '')
      {
          $status_code    = '404';
          $headers        = array('error' => true, 'message' => 'Invalid User', 'status_code' => '404');
          send_response($status_code ,$headers);
      }
      $this->load->library(array('ofabeeevents'));
      $range              = date("Y-m-d", strtotime($date));
      
      $custom_events      = $this->ofabeeevents->getEvents(array('user_id'   => $user_id, 'date_from' => $range));
      $lectures['events'] = isset($custom_events['events']) ? $custom_events['events'] : array();
      $item_list          = array();
      $body               = array();
      $status_code        = '204';
      $headers            = array('error' => true,'status_code'=> $status_code,'message' => 'data not found');
      if (!empty($lectures['events'])) 
      {
          foreach ($lectures['events'] as $event) 
          {
              $events[]   = array(
                              'id' => $event['id'], 
                              'event_name' => $event['ev_name'], 
                              'event_image' => '',
                              'event_link' => site_url('events').'/event/'.base64_encode($event['id']),
                              'event_date'=>$event['events_date_time']   
                              );
              $sorted_array[$event['id'].'_event'] = $event['events_date_time'];
          }
          array_multisort($sorted_array, SORT_ASC, $events); 
          $item_list                      = $events;  
          $body['title']                  = 'Events';
          $body['total_count']            = count($item_list);
          $item_list                      = array_slice($item_list,$offset,$limit,true);
          $body[$body_label]              = $item_list;
          $status_code                    = '200';
          $headers                        = array('error' => false,'status_code'=> $status_code,'message' => 'data fetched successfully');
      }
      send_response($status_code ,$headers,$body);
  }
  public function institutes()
  {
    $body                       = array();
    $status_code                = '204';
    $headers                    = array('error' => true,'status_code'=> $status_code,'message' => 'data not found');
    $param['status']            = '1';
    $param['not_deleted']       = true;
    $param['select']            = 'id, ib_name, ib_institute_code';
    $this->load->model(array('Institute_model'));
    $institutes                 = $this->Institute_model->institutes($param);
    if(!empty($institutes))
    {
        $body['title']                  = 'Institutes';
        $body['total_count']            = count($institutes);
        $body['institutes']             = $institutes;
        $status_code                    = '200';
        $headers                        = array('error' => false,'status_code'=> $status_code,'message' => 'data fetched successfully');
        
    }
    send_response($status_code ,$headers,$body);
  }
}

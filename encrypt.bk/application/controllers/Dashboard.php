<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    private $__limit = 5;

    function __construct() {
        parent::__construct();
        $skip_login = array('result_preview');
        if(!in_array($this->router->fetch_method(), $skip_login))
        {
            $redirect = $this->auth->is_logged_in_user(false, false, 'user');
        
            if (!$redirect) {
                redirect('login');
            }    
        }
        $this->lang->load('course');
        $this->lang->load('dashboard');
        $this->lang->load('homepage');
    }
    
    function profile()
    {

        $data                 = array();
        $session              = $this->auth->get_current_user_session('user');
        //echo '<pre>'; print_r($session);die;
        $data['session']        = $session;

        //getting the fields asscodiated with this user
        $data['user_profile_fields'] = array();
        
        $user_profile_fields         = isset($session['us_profile_fields']) ? explode('{#}', $session['us_profile_fields']) : array();
        //echo '<pre>'; print_r($session);die;

        if (!empty($user_profile_fields)) {
            foreach ($user_profile_fields as $field) {
                $field                             = substr($field, 2);
                $field                             = substr($field, 0, -2);
                $temp_field                        = explode('{=>}', $field);
                $key                               = isset($temp_field[0]) ? $temp_field[0] : 0;
                $value                             = isset($temp_field[1]) ? $temp_field[1] : '';
                $data['user_profile_fields'][$key] = $value;
            }
        }
        $objects        = array();
        $objects['key'] = 'profile_blocks';
        $data['profile_blocks'] = $this->memcache->get($objects);
        // echo '<pre>'; print_r($data['profile_blocks']);die;
        if(!$data['profile_blocks'] && !is_array($data['profile_blocks']))
        {
            $this->load->model('Settings_model');
            $profile_blocks         = $this->Settings_model->blocks();
            $data['profile_blocks'] = array();
            if (!empty($profile_blocks)) {
                foreach ($profile_blocks as $profile_block) {
                    $profile_block['profile_fields']              = $this->Settings_model->profile_fields(array(
                                             'block_id' => $profile_block['id']
                    ));
                    $data['profile_blocks'][$profile_block['id']] = $profile_block;
                }
            }    
            $this->memcache->set('profile_blocks', $data['profile_blocks'], 1200);
        }

        //End
        //echo '<pre>'; print_r($data);die;
        $this->load->view($this->config->item('theme') . '/profile', $data);
    }

    function step($step = false) {
        switch ($step) {
            case 1:
                $this->phoneNumberVerify();
                break;
            case 2:
                $this->checkMandatoryProfileFields();
                break;
        }
    }

    function phoneNumberVerify() {
        $this->load->model('User_model');
        $data                 = array();
        $session              = $this->auth->get_current_user_session('user');
        $data['session']      = $session;
        $data['user_details'] = $this->User_model->user(array('id' => $session['id']));

        //echo "<pre>";print_r($data['user_details']);die;

        if ($data['user_details']['us_phone_verfified'] == '0') {
            $this->load->view($this->config->item('theme') . '/phone_verification', $data);
        } else {
            redirect('/dashboard/step/2');
        }
    }

    function checkMandatoryProfileFields() {
        $this->load->model('User_model');
        $data = array();
        //getting dynamic field value saved by user
        $user = $this->auth->get_current_user_session('user');
        //$user                   = $this->User_model->user(array('id'=> $user['id']));
        //echo "<pre>";print_r($user);die;
        if ($user['us_phone_verfified'] == '0') {
            redirect('/dashboard/step/1');
        }
        $this->load->model('Settings_model');
        $data['user_profile_fields'] = array();
        $user_profile_fields         = $this->Settings_model->profile_field_values(array('user_id' => $user['id']));
        $user_profile_fields         = isset($user_profile_fields['us_profile_fields']) ? explode('{#}', $user_profile_fields['us_profile_fields']) : array();

        if (!empty($user_profile_fields)) {
            foreach ($user_profile_fields as $field) {
                $field                             = substr($field, 2);
                $field                             = substr($field, 0, -2);
                $temp_field                        = explode('{=>}', $field);
                $key                               = isset($temp_field[0]) ? $temp_field[0] : 0;
                $value                             = isset($temp_field[1]) ? $temp_field[1] : '';
                $data['user_profile_fields'][$key] = $value;
            }
        }
        //generating excluded fields; this is mandatory fields with values. becuse no need to place it in form.
        $data['excluded_user_profile_fields'] = array();
        if (!empty($data['user_profile_fields'])) {
            foreach ($data['user_profile_fields'] as $u_field_id => $u_field_value) {
                if (trim($u_field_value) != '') {
                    $data['excluded_user_profile_fields'][] = $u_field_id;
                }
            }
        }

        //getting mandatory fields
        $data['mandatory_profile_fields'] = $this->Settings_model->profile_fields(array('strict_order' => true));

        $has_mandatory = 0;
        if (!empty($data['mandatory_profile_fields'])) {
            foreach ($data['mandatory_profile_fields'] as $field) {
                if (!in_array($field['id'], $data['excluded_user_profile_fields'])) {
                    $has_mandatory++;
                }
            }
        }

        if($has_mandatory == 0)
        {
            $result         = $this->User_model->user(array('id' => $user['id']));
            $result['us_profile_completed'] = 1;
            unset($result['us_password']);
            $user          = array();
            $user['user']  = $result;
            $this->session->set_userdata($user);
            redirect(site_url('course/listing'));
        }
        $this->load->view($this->config->item('theme') . '/profile_completion', $data);
    }

    private function topic_wise_progress() {
        $this->load->model('User_model');
        $session  = $this->auth->get_current_user_session('user');
        $user_id  = isset($session['id']) ? $session['id'] : 0;
        $response = array();

        if ($user_id == 0) {
            $response['success'] = 0;
            $response['message'] = 'Invalid input provided.';
            redirect('dashboard');
        }

        $category_wise = $this->User_model->topic_wise_progress(array(
                                 'user_id' => $user_id
        ));

        return $category_wise;

        echo json_encode($response);
    }

    function save_profile($param = array())
    {
        $response               = array();
        $response['status']     = 1;
        $response['message']    = 'User details saved successfully';
        $this->load->model('User_model');
        $user               = $this->auth->get_current_user_session('user');
        $user_name          = isset($param['user_name'])?$param['user_name']:$this->input->post('user_name');
        $user_bio           = isset($param['user_name'])?false:$this->input->post('user_bio');
       
        $save       = array();
        $save['id'] = $user['id'];
        if ($user_name) 
        {
            $save['us_name'] = $user_name;
            $user['us_name'] = $user_name;
        }
        if ($user_bio) 
        {
            $save['us_about'] = $user_bio;
            $user['us_about'] = $user_bio;
        }
        $this->User_model->save($save);
        $this->session->set_userdata(array('user' => $user));
        if(isset($param['user_name'])){
            return true;
        }else{
            echo json_encode($response);
        }
    }
    function save_profile_about() 
    {
        $response               = array();
        $response['status']     = 1;
        $response['message']    = 'User details saved successfully';

        $this->load->model('User_model');
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('', '</br>');
        $this->form_validation->set_rules('email_id', 'Email ID', 'valid_email');
        if ($this->form_validation->run() == FALSE)
        {
            $response['status']   = 2;
            $response['message'] = validation_errors();
            echo json_encode($response);die;
        }
        $user               = $this->auth->get_current_user_session('user');
        $user_name          = $this->input->post('user_name');
        $user_bio           = $this->input->post('user_bio');
        $email_id           = $this->input->post('email_id');
        $phone_number       = $this->input->post('phone_number');
        $message            = '';
        $email_changed      = false;
        $userOldEmail       = $user['us_email'];
        $userOldPhone       = $user['us_phone'];
        //print_r($user);die;
        //saving user details
        $save       = array();
        $save['id'] = $user['id'];
        $this->load->model('Authenticate_model');

        if(!$email_id && !$phone_number)
        {
            $response['status']      = 2;
            $response['message'] = 'Both email id and phone number cannot be empty together';
            echo json_encode($response);die;
        }

        if(!$phone_number)
        {
            $response['status']      = 2;
            $response['message'] = 'Phone number cannot be empty';
            echo json_encode($response);die;
        }

        $save['us_phone']       = $phone_number;
        $user['us_phone']       = $phone_number; 

        if($email_id) 
        {
            $email_id_available   = $this->Authenticate_model->get_user_by_field(array('email' => $email_id, 'explicit_user_id' => $user['id']));
            if($email_id_available)
            {
                $response['status']      = 2;
                $message                .= 'Email is already in use';
            }
            else
            {
                $email_changed          = ($email_id!=$user['us_email']);
                if($email_changed)                   
                {
                    $save['us_email_verified']       = '0';
                    $user['us_email_verified']       = '0'; 
                }
            }
            $save['us_email_exist']      = '1';
        }
        
        $save['us_email']       = $email_id;
        $user['us_email']       = $email_id; 
        
        if($phone_number)
        {
            $phone_number_available   = $this->Authenticate_model->get_user_by_field(array('phone_number' => $phone_number, 'explicit_user_id' => $user['id']));
            if($phone_number_available)
            {
                $response['status']      = 2;
                $message                .= '<br />Phone number already occupied';
            }
        }
        $save['us_phone']       = $phone_number;
                            
        //End

        if($response['status'] == 1)
        {
            $this->User_model->save($save);
            /*Log creation*/
            $user_data                          = array();
            $user_data['user_id']               = $user['id'];
            $user_data['username']              = $user['us_name'];
            $user_data['useremail']             = $user['us_email'];
            $user_data['user_type']             = $user['us_role_id'];
            $user_data['phone_number']          = $phone_number;
            $message_template                   = array();
            $message_template['username']       = $user['us_name'];

            if($user['us_email_exist']==0)
            {
                $message_template['email_id']       = $email_id;
                $triggered_activity                 = 'student_email_id_changed';
                log_activity($triggered_activity, $user_data, $message_template);
                $delete_key        = 'score_' . $user['id'];
                $this->memcache->delete($delete_key);
            }
            else if($userOldEmail != $email_id)
            {
                $message_template['email_id']       = $email_id;
                $triggered_activity                 = 'student_email_id_changed_zero';
                log_activity($triggered_activity, $user_data, $message_template);
            }

            if($userOldPhone != $phone_number)
            {
                $message_template['phone_number']   = $phone_number;
                $triggered_activity                 = 'student_phone_number_changed';
                log_activity($triggered_activity, $user_data, $message_template);
            }

            if($email_id)
            {
                $user['us_email_exist'] = '1';
            }
            $user['us_phone']       = $phone_number;
            $this->session->set_userdata(array('user' => $user));
            if($email_changed)
            {
                $email_token        = md5(openssl_random_pseudo_bytes(64));
                $to_email		    = $email_id;
                //Save token for verification 
                $token                  = array();
                $token['et_user_id']    = $user['id'];
                $token['et_user_email'] = $to_email;
                $token['et_account_id'] = config_item('id');
                $token['et_token']      = $email_token;
                $token['et_status']     = '1';
                $token['et_change_status'] = '0';
                $this->User_model->save_token($token);
                //End saving token 


                $template           = $this->ofabeemailer->template(array('email_code' => 'email_id_changed'));
                $param              = array();
                $param['to'] 	    = array($to_email);
                $param['subject'] 	= $template['em_subject'];
                $contents           = array(
                                            'user_name' => $user['us_name']
                                            ,'site_name' => config_item('site_name')
                                            ,'verification_link' => site_url('register/verify/'.$email_token)
                                    );
                $param['body']      = $this->ofabeemailer->process_mail_content($contents, $template['em_message']);
                $send = $this->ofabeemailer->send_mail($param);
                $response['message'] = 'Your details saved successfully.<br />Since you have changed your email id and inorder to get notifications please verify your new email id by clicking the verification link that is sent to your mail id.';
                $response['status'] = 3;
            }
            
        }
        else
        {
            $response['message'] = $message;
        }
        echo json_encode($response);
    }

    function upload_user_image() {
        $this->load->model('User_model');
        $response            = array();
        $response['error']   = 'false';
        $response['message'] = 'Profile picture updated successfully';
        $user_name           = $this->input->post('user_name');

        $this->save_profile(array('user_name'=>$user_name));

        $user                = $this->auth->get_current_user_session('user');
        $user_profile_image  = $user['us_image'];
        $directory           = user_upload_path();
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }
        $config                  = array();
        $config['upload_path']   = $directory;
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        //$new_name                   = $_FILES['file']['name'];
        //$new_name                   = explode('.', $new_name);
        $new_name                   = $user['id'].'.jpg';//$user['id'].'.'.$new_name[sizeof($new_name)-1];
        $config['file_name']        = $new_name;
        $config['overwrite']        = TRUE;
        $this->load->library('upload');
        $this->upload->initialize($config);
        $upload                  = $this->upload->do_upload('file');
        if ($upload) {
            $uploaded_data    = $this->upload->data();
            $uploaded_data['user_id']   = $user['id'];   
            $save             = array();
            $save['id']       = $user['id'];

            //User image version.
            $v_image            = $user['us_image'];
            $v_version          = explode('?',$v_image);
            $v_old_version      = isset($v_version[1])?explode('=',$v_version[1]):array('0','0');
            $v_old_version      = $v_old_version[1]+1;
            $v_old_version      = '?v='.$v_old_version;
            //End user image version.

            $save['us_image'] = $user['id'] . '.jpg'.$v_old_version;
            //changing session image
            $user['us_image'] = $save['us_image'];
            $this->session->set_userdata(array(
                                     'user' => $user
            ));
            //end
            $this->User_model->save($save);
            $new_file         = $this->crop_image($uploaded_data);

            $has_s3 = $this->settings->setting('has_s3');
            if ($has_s3['as_superadmin_value'] && $has_s3['as_siteadmin_value']) {
                uploadToS3(user_upload_path() . $new_file, user_upload_path() . $new_file);
            }
            $response['user_image'] = user_path() . $new_file;

            $user_data                          = array();
            $user_data['user_id']               = $user['id'];
            $user_data['username']              = $user['us_name'];
            $user_data['useremail']              = $user['us_email'];
            $user_data['user_type']             = $user['us_role_id'];
            $user_data['phone_number']          = $user['us_phone'];
            $message_template                   = array();
            $message_template['username']       = $user['us_name'];

            if($user_profile_image=='default.jpg'){
            /*Log creation*/
                $triggered_activity                 = 'student_profile_picture_changed'; 
            }
            else
            {
                $triggered_activity                 = 'student_profile_picture_changed_zero'; 
            }

            log_activity($triggered_activity, $user_data, $message_template);
            $delete_key        = 'score_' . $user['id'];
            $this->memcache->delete($delete_key);
           
        } else {
            $response['error']   = 'true';
            $response['message'] = 'Error in updating profile picture';
        }
        echo json_encode($response);
    }

    function beta() {
        $data                       = array();
        $this->load->model('Dashboard_model');
        $user                       = $this->auth->get_current_user_session('user');
        $data['assessment_courses'] = array();
        $assessment_courses         = $this->Dashboard_model->get_attempted_courses(array(
                                 'user_id' => $user['id']
        ));
        if (!empty($assessment_courses)) {
            foreach ($assessment_courses as $assessment_course) {
                $assessment_course['attempts']                                 = $this->Dashboard_model->get_assesment_attempts(array(
                                         'user_id'   => $user['id'],
                                         'course_id' => $assessment_course['a_course_id']
                ));
                $data['assessment_courses'][$assessment_course['a_course_id']] = $assessment_course;
            }
        }

        $data['user_generated_attempts'] = $this->Dashboard_model->get_user_generated_attempts(array(
                                 'user_id' => $user['id']
        ));
        $data['challenge_zone_attempts'] = $this->Dashboard_model->get_challenge_zone_attempts(array(
                                 'user_id' => $user['id']
        ));
        $this->load->view($this->config->item('theme') . '/dashboard', $data);
        //echo '<pre>'; print_r($data);die;
    }
    /*
    purpose     : bundle subscription listing dashboard
    params      : none
    developer   : kiran
    edited      : none
    */
    public function my_bundles()
    {
        $data                   = array();
        $user                   = $this->auth->get_current_user_session('user');
        $data['session']        = $user;
        $data['title']          = 'my bundles';
        
        $objects                = array();
        $objects['key']         = 'all_bundles';
        $callback               = 'all_bundles';
        $all_bundles            = $this->memcache->get($objects, $callback);
        if (isset($user['id'])) 
        {
            //bundles
            $subscriptions      = array();
            $objects            = array();
            $objects['key']     = 'bundle_enrolled_' . $user['id'];
            $callback           = 'my_bundle_subscriptions';
            $params             = array('user_id' => $user['id']);
            $enrolled_bundles   = $this->memcache->get($objects, $callback, $params);
            // echo "<pre>";print_r($enrolled_bundles);exit;
            if(!empty($enrolled_bundles))
            {
                foreach ($enrolled_bundles as $key => $enrolled) {
                    //print_r($enrolled['bs_bundle_details']);
                    $c_courses = isset($enrolled['bs_bundle_details']) ? json_decode($enrolled['bs_bundle_details'],true)['c_courses'] : array();
                    $enrolled_bundles[$key]['bundle_length']    = count($c_courses);
                    $enrolled_bundles[$key]['enrolled']         = true;
                    $now                                        = time(); // or your date as well $enroll['cs_end_date']
                    $start_date                                 = strtotime(date('Y-m-d'));
                    $your_date                                  = strtotime($enrolled['bs_end_date'].' + 1 days');
                    $datediff                                   = $your_date - $now;
                    $today                                      = date('Y-m-d');
                    $expire                                     = date_diff(date_create($today), date_create($enrolled['bs_end_date']));
                    $enrolled_bundles[$key]['expired']          = ceil($datediff / (60 * 60 * 24)) > 0? false:true;
                    $enrolled_bundles[$key]['expire_in']        = $expire->format("%R%a");
                    $expires_in                                 = ceil($datediff / (60 * 60 * 24));// ($your_date - $start_date)/60/60/24;
                    $enrolled_bundles[$key]['expire_in_days']   = $expires_in;
                    $enrolled_bundles[$key]['validity_format_date']   = date('d-m-Y', strtotime($enrolled['bs_end_date']));
                }
                $subscriptions  = $enrolled_bundles;
            }
            
            $data['subscription'] = $subscriptions;
        } 
        //echo "<pre>";print_r($enrolled_bundles);exit;
        $this->load->view($this->config->item('theme') . '/mybundles', $data);
    }
    public function courses() {
        $data            = array();
        $user            = $this->auth->get_current_user_session('user');
        // echo json_encode($user);exit;
        $data['session'] = $user;
        $data['title']   = lang('my_courses');

        $objects         = array();
        $objects['key']  = 'enrolled_' . $user['id'];
        $callback        = 'my_subscriptions';
        $params          = array('user_id' => $user['id'], 'courses_only' => true,'order_by' => true);
        $enrolled        = $this->memcache->get($objects, $callback, $params);
        
        // Declare two dates 
 

//  echo '<pre>';print_r($enrolled);die;
        foreach ($enrolled as $e_key => $enroll) {
            
            $course_completion      = 0;
            $lecture_count          = 0;

            foreach($enroll['lectures'] as $lecture)
            {
                $lecture_count++;
            }

            $course_completion += $enroll['cs_percentage'];
            //echo $course_completion.'<br/>';

            $enrolled[$e_key]['course_completion']      = round($course_completion);
            $enrolled[$e_key]['enrolled']               = true;
            $now                                        = time(); // or your date as well $enroll['cs_end_date']
            $start_date                                 = strtotime(date('Y-m-d'));
            $your_date                                  = strtotime($enroll['cs_end_date'].' + 1 days');
            $datediff                                   = $your_date - $now;
            $today                                      = date('Y-m-d');
            $expire                                     = date_diff(date_create($today), date_create($enrolled[$e_key]['cs_end_date']));
            $enrolled[$e_key]['expired']                = ceil($datediff / (60 * 60 * 24)) > 0? false:true;
            $enrolled[$e_key]['expire_in']              = $expire->format("%R%a");
            
            $expires_in                                 = ceil($datediff / (60 * 60 * 24));// ($your_date - $start_date)/60/60/24;
            
            $enrolled[$e_key]['expire_in_days']         = $expires_in;
            //$enrolled[$e_key]['expire_in_days']        = $expire->format("%d");
            $enrolled[$e_key]['validity_format_date']   = date('d-m-Y', strtotime($enrolled[$e_key]['cs_end_date']));
        }
        
        //die;
        $data['course_details'] = $enrolled;
        //  echo "<pre>";print_r($data['course_details']);exit;
        // $objects        = array();
        // $objects['key'] = 'notifications';
        // $callback       = 'notifications';
        // $data['information_bars'] = $this->memcache->get($objects, $callback);
         
        //  echo "<pre>";print_r($data);exit;
        if (isset($user['id'])) 
        {
            //bundles
            $subscriptions      = array();
            $objects            = array();
            $objects['key']     = 'bundle_enrolled_' . $user['id'];
            $callback           = 'my_bundle_subscriptions';
            $params             = array('user_id' => $user['id'],'order_by' => true);
            $enrolled_bundles   = $this->memcache->get($objects, $callback, $params);
            //echo "<pre>";print_r($enrolled_bundles);exit;
            if(!empty($enrolled_bundles))
            {
                foreach ($enrolled_bundles as $key => $enrolled) {
                    //print_r($enrolled['bs_bundle_details']);
                    $c_courses = isset($enrolled['bs_bundle_details']) ? json_decode($enrolled['bs_bundle_details'],true) : array();
                    $enrolled_bundles[$key]['bundle_length']    = count($c_courses);
                    $enrolled_bundles[$key]['enrolled']         = true;
                    $now                                        = time(); // or your date as well $enroll['cs_end_date']
                    $start_date                                 = strtotime(date('Y-m-d'));
                    $your_date                                  = strtotime($enrolled['bs_end_date'].' + 1 days');
                    $datediff                                   = $your_date - $now;
                    $today                                      = date('Y-m-d');
                    $expire                                     = date_diff(date_create($today), date_create($enrolled['bs_end_date']));
                    $enrolled_bundles[$key]['expired']          = ceil($datediff / (60 * 60 * 24)) > 0? false:true;
                    $enrolled_bundles[$key]['expire_in']        = $expire->format("%R%a");
                    $expires_in                                 = ceil($datediff / (60 * 60 * 24));// ($your_date - $start_date)/60/60/24;
                    $enrolled_bundles[$key]['expire_in_days']   = $expires_in;
                    $enrolled_bundles[$key]['validity_format_date']   = date('d-m-Y', strtotime($enrolled['bs_end_date']));
                }
                $subscriptions  = $enrolled_bundles;
            }
            
            $data['subscription'] = $subscriptions;
        }
        //echo '<pre>'; print_r($data);die;
        $this->load->view($this->config->item('theme') . '/mycourses', $data);
    }

    public function assignments() {

        $data                = array();
        $session             = $this->auth->get_current_user_session('user');
        $data['session']     = $session;
        $data['title']       = lang('assignments');
        $data['assignments'] = $this->get_assignments($session);
        //echo '<pre>';print_r($data);die;
        $this->load->view($this->config->item('theme') . '/assignments', $data);
    }

    public function result() {
        $this->load->model(array('Dashboard_model', 'User_model'));
        $data                       = array();
        $session                    = $this->auth->get_current_user_session('user');
        $data['session']            = $session;
        $data['admin']              = $this->config->item('acct_name');
        $data['admin_name']         = $this->config->item('us_name');
        $data['today']              = date('Y-m-d');
        $data['title']              = ($session['us_role_id'] == '5') ? 'Parent Dashboard' : 'Dashboard';
        $data['user_details']       = $this->User_model->user(array(
                                 'id' => $session['id']
        ));
        $user                       = $this->auth->get_current_user_session('user');
        $data['assessment_courses'] = array();
        $assessment_courses         = $this->Dashboard_model->get_attempted_courses(array(
                                 'user_id' => $user['id']
        ));
        if (!empty($assessment_courses)) {
            foreach ($assessment_courses as $assessment_course) {
                $assessment_course['attempts']                                 = $this->Dashboard_model->get_assesment_attempts(array(
                                         'user_id'   => $user['id'],
                                         'course_id' => $assessment_course['a_course_id']
                ));
                $data['assessment_courses'][$assessment_course['a_course_id']] = $assessment_course;
            }
        }

        $this->load->view($this->config->item('theme') . '/result', $data);
    }

    public function quiz() {
        $this->load->model('Dashboard_model');
        $data            = array();
        $session         = $this->auth->get_current_user_session('user');
        $data['session'] = $session;
        $data['title']   = lang('quiz');
        $this->load->model('Report_model');
        /* $courses           = $this->Report_model->enrolled_course(array(
          'user_id' => $session['id']
          )); */

        $data['courses'] = array();
        if (!empty($courses)) {
            foreach ($courses as $course) {
                $course['report']                      = $this->Report_model->user_course_assessment_report(array(
                                         'user_id'   => $session['id'],
                                         'course_id' => $course['course_id']
                ));
                $data['courses'][$course['course_id']] = $course;
            }
        }

        $data['test_report'] = array(); //$this->test_report();
        $this->load->view($this->config->item('theme') . '/scorecard', $data);
    }

    function grade() {
        $data          = array();
        $data['title'] = lang('grade');
        $this->load->view($this->config->item('theme') . '/grade_report', $data);
    }

    public function index() {
        $data              = array();
        $user              = $this->auth->get_current_user_session('user');
        $data['session']   = $user;
        $data['title']     = lang('dashboard');

        $objects           = array();
        $objects['key']    = 'score_' . $user['id'];
        $callback          = 'my_score';
        $params            = array('user_id' => $user['id']);
        $score             = $this->memcache->get($objects, $callback, $params);
        $data['score']     = $score['lap_user_points'];
        
        
        $objects           = array();
        $objects['key']    = 'enrolled_' . $user['id'];
        $callback          = 'my_subscriptions';
        $params            = array('user_id' => $user['id'],'order_by_date' => true,'order_by'=>false);
        $enrolled          = $this->my_last_view_courses($params);
        $grade             = array();
        $grade['total']    = 0;
        $grade['count']    = 0;
        $courses           = array();
        
        foreach ($enrolled as $e_key => $enroll) {

            $course_completion      = 0;
            $lecture_count          = 0;
            foreach($enroll['lectures'] as $lecture)
            {
                $lecture_count++;
            }

            $course_completion += $enroll['cs_percentage'];

            $enrolled[$e_key]['course_completion']      =round($course_completion);
            

            $objects           = array();
            $objects['key']    = 'course_' . $enroll['cs_course_id'];

            $callback          = 'course_details'; 
            $params            = array('id' => $enroll['cs_course_id']);
            $course            = $this->memcache->get($objects, $callback, $params);

            $courses[] = $enroll['cs_course_id'];
            $grade['count']         = $grade['count']+1;
            $grade['total']         = $grade['total']+$enroll['cs_percentage'];
        
            $enrolled[$e_key]['enrolled']               = true;
            $now                                        = time(); // or your date as well $enroll['cs_end_date']
            $start_date                                 = strtotime(date('Y-m-d'));
            $your_date                                  = strtotime($enroll['cs_end_date'].' + 1 days');
            $datediff                                   = $your_date - $now;
            $today                                      = date('Y-m-d');
            $expire                                     = date_diff(date_create($today), date_create($enrolled[$e_key]['cs_end_date']));
            $enrolled[$e_key]['expired']                = ceil($datediff / (60 * 60 * 24)) > 0? false:true;
            $enrolled[$e_key]['expire_in']              = $expire->format("%R%a");
            $expires_in                                 = ceil($datediff / (60 * 60 * 24));// ($your_date - $start_date)/60/60/24;
            $enrolled[$e_key]['expire_in_days']       = $expires_in;//$expire->format("%a")+1;
            $enrolled[$e_key]['validity_format_date'] = date('d-m-Y', strtotime($enrolled[$e_key]['cs_end_date']));
            $enrolled[$e_key]['ratting']              = isset($course['total_ratting'])?$course['total_ratting']:0;
        }

        $data['courses'] = $enrolled;
        // echo "<pre>";print_r($enrolled);die;
        $data['upcomming'] = $this->calendar(array('assignment_status'=> 1,'courses' => $courses));
        $percentage      = $grade['count']!=0?$grade['total']/$grade['count']:0;
        $gr_name         = convert_percentage_to_grade($percentage);
        $gr_name         = isset($gr_name['gr_name']) ? $gr_name['gr_name'] : '';
        $data['grade']   = $percentage == 0 ?'-' : $gr_name;

        // $objects        = array();
        // $objects['key'] = 'notifications';
        // $callback       = 'notifications';
        // $data['information_bars'] = $this->memcache->get($objects, $callback);
        // echo '<pre>';print_r($data);die;
        $this->load->view($this->config->item('theme') . '/dashboard_sdpk', $data);
    }


    private function my_last_view_courses($param = array())
    {
        $this->load->model(array('Report_model', 'Course_model', 'User_model'));
        $user_id            = isset($param['user_id']) ? $param['user_id'] : 0;
        $courses_only       = isset($param['courses_only']) ? $param['courses_only'] : false;
        $response           = array();
     
        $order_by_date      = isset($param['order_by_date']) ? $param['order_by_date'] : false;
        $enrolled_courses   = $this->Report_model->enrolled_course(
                                                                    array(
                                                                        'user_id'       => $user_id, 
                                                                        'courses_only'  => $courses_only,
                                                                       
                                                                        'order_by_date'=> $order_by_date 
                                                                    )
                                                                );
        foreach ($enrolled_courses as $course) {
            $course_tutors = $this->Course_model->assigned_tutors(array('course_id' => $course['course_id']));
            $course['tutors'] = $course_tutors;
            $params                     = array('course_id' => $course['course_id'], 'status' => true);
            
            $percentage = $course['cs_percentage']; //$scope->Course_model->course_percentage(array('user_id' => $user_id, 'course_id' => $course['course_id']));
            $response[$course['course_id']] = $course;
            $response[$course['course_id']]['percentage'] = round($percentage, 2); 
            $response[$course['course_id']]['lectures'] = $this->Course_model->lectures($params);//course_details($params, $scope);
            // $response[$course['course_id']]['ratting'] = $scope->Course_model->get_ratting(array('course_id' => $course['course_id']));
        }
        return $response;
    }

    private function review_questions() {
        $data                 = array();
        $this->load->model('Assesment_error_model');
        $user                 = $this->auth->get_current_user_session('user');
        //$wrong_answer = $this->Assesment_error_model->get_wrong_answer(3);
        $wrong_answer         = $this->Assesment_error_model->get_wrong_answer(array(
                                 'user_id' => $user['id']
        ));
        $data['wrong_answer'] = array();
        foreach ($wrong_answer as $wrong) {

            if ($wrong['status'] == 0) {

                $data['wrong_answer'][$wrong['id']] = $wrong;
            }
        }

        $options_op = array();
        // echo "<pre>"; print_r($data)
        foreach ($data['wrong_answer'] as $value) {

            $options               = array();
            $options[$value['id']] = $value['q_options'];
            $finds_op              = $this->Assesment_error_model->get_options($options);


            if (is_array($finds_op)) {

                foreach ($finds_op as $find) {

                    $options_op[$value['id']][$find['id']] = $find;
                }
            }

            if (is_array($options_op)) {

                foreach ($options_op as $key => $values) {

                    if ($value['id'] == $key) {

                        $data['wrong_answer'][$value['id']]['options'] = $values;
                    }
                }
            }
        }

        $un_attmpts = $this->Assesment_error_model->get_unattempt(array(
                                 'user_id' => $user['id']
        ));
        //echo '<pre>';print_r($un_attmpts);die;
        foreach ($un_attmpts as $un_attmpt) {

            $options                            = array();
            $options[$un_attmpt['id']]          = $un_attmpt['q_options'];
            $data['unattemt'][$un_attmpt['id']] = $un_attmpt;
            $un_finds_op                        = $this->Assesment_error_model->get_options($options);
            //echo "<pre>";print_r($un_finds_op);die;
            $options_un_op                      = array();
            if (!empty($un_finds_op)) {
                foreach ($un_finds_op as $find_op) {

                    $options_un_op[$un_attmpt['id']][$find_op['id']] = $find_op;
                }
            }

            foreach ($options_un_op as $key => $values) {

                if ($un_attmpt['id'] == $key) {

                    $data['unattemt'][$un_attmpt['id']]['options'] = $values;
                }
            }
        }
        return $data;
    }

    function save_profile_values() {
        $this->load->model('Settings_model');
        $user           = $this->auth->get_current_user_session('user');
        $user_id        = $user['id'];
        $profile_values = json_decode($this->input->post('profile_values'));
        $message        = '';
        $error          = false;

        $user_field_values = array();

        $old_value = $this->Settings_model->profile_field_values(array(
                                 'user_id' => $user_id
        ));
        $old_value = isset($old_value['us_profile_fields']) ? explode('{#}', $old_value['us_profile_fields']) : array();

        if (!empty($old_value)) {
            foreach ($old_value as $field) {
                $field                   = substr($field, 2);
                $field                   = substr($field, 0, -2);
                $temp_field              = explode('{=>}', $field);
                $key                     = isset($temp_field[0]) ? $temp_field[0] : 0;
                $value                   = isset($temp_field[1]) ? $temp_field[1] : '';
                $user_field_values[$key] = $value;
            }
        }


        if (!empty($profile_values)) {
            $profile_block_id = 0;
            foreach ($profile_values as $name => $value) {
                $field_object                           = $this->Settings_model->profile_field(array(
                                         'field_name' => $name
                ));
                $user_field_values[$field_object['id']] = $value;
            }

            $us_profile_field = array();
            if (!empty($user_field_values)) {
                foreach ($user_field_values as $field_id => $field_value) {
                    $us_profile_field[] = '{{' . $field_id . '{=>}' . $field_value . '}}';
                }
                $us_profile_field = implode('{#}', $us_profile_field);
            }

            $save                      = array();
            $save['id']                = $user_id;
            $save['us_profile_fields'] = (($us_profile_field) ? $us_profile_field : '');
            $this->Settings_model->save_profile_field_value($save);
            $user['us_profile_fields'] = (($us_profile_field) ? $us_profile_field : '');
            $this->session->set_userdata(array(
                                     'user' => $user
            ));

            /*Log creation*/
            $user_data                          = array();
            $user_data['user_id']               = $user['id'];
            $user_data['username']              = $user['us_name'];
            $user_data['useremail']              = $user['us_email'];
            $user_data['user_type']             = $user['us_role_id'];
            $user_data['phone_number']          = $user['us_phone'];
            $message_template                   = array();
            $message_template['username']       = $user['us_name'];
            $triggered_activity                 = 'student_profile_data_changed';
            log_activity($triggered_activity, $user_data, $message_template);
        }
        echo json_encode(array(
                                 'message' => 'Field details saved',
                                 'error'   => false
        ));
    }

    function user_generated_test_details($assessment_id = '', $user_id = '', $attempted_id = '') {
        $this->load->model(array('User_generated_model', 'Challenge_model'));
        $data                  = array();
        $session               = $this->auth->get_current_user_session('user');
        $data['session']       = $session;
        $data['assessment_id'] = $assessment_id;
        $data['user_id']       = $user_id;
        $data['attempted_id']  = $attempted_id;



        $data['users'] = $this->User_generated_model->attempted_test(array(
                                 'user_id'      => $user_id,
                                 'attempted_id' => $attempted_id
        ));

        if (empty($data['users']) || $data['attempted_id'] == '') {
            redirect('dashboard');
        }

        $data['category_id']      = $data['users'][0]['uga_category'];
        $data['current_category'] = $this->User_generated_model->get_category_name($data['category_id']);
        $data['cz_title']         = $data['users'][0]['uga_title'];



        foreach ($data['users'] as $key => $assessment) {
            $data['users'][$key]['assessment_report'] = $this->User_generated_model->assessment_report(array(
                                     'attempted_id' => $assessment['attempted_id']
            ));
            $data['users'][$key]['total_count']       = count($data['users'][$key]['assessment_report']);
            $temp_correct                             = 0;
            $temp_wrong                               = 0;
            $temp_not_attempted                       = 0;
            $data['users'][$key]['q_type']            = 1;
            foreach ($data['users'][$key]['assessment_report'] as $key2 => $report) {

                if ($report['ugar_answer'] == '' || empty($report['ugar_answer'])) {
                    $temp_not_attempted++;
                    $data['users'][$key]['assessment_report'][$key2]['correct']    = 2;
                    $temp_options                                                  = explode(',', $report['q_options']);
                    $data['users'][$key]['assessment_report'][$key2]['qo_options'] = $temp_options;

                    $temp_options_value = array();
                    $temp_options_value = $this->Challenge_model->get_option_value($report['q_options']);

                    $data['users'][$key]['assessment_report'][$key2]['qo_options_value'] = $temp_options_value;
                    $temp_q_ans                                                          = '';
                    $temp_a_ans                                                          = '';
                    $chr                                                                 = 65;
                    foreach ($temp_options as $value) {
                        if ($value == $report['q_answer']) {

                            if ($temp_q_ans == '') {
                                $temp_q_ans = chr($chr);
                            } else {
                                $temp_q_ans = $temp_q_ans . ',' . chr($chr);
                            }
                        }
                        $chr++;
                    }

                    $data['users'][$key]['assessment_report'][$key2]['correct_answer'] = $temp_q_ans;
                } else {

                    if ($report['q_type'] == 1) {

                        if ($report['q_answer'] == $report['ugar_answer']) {
                            $temp_correct++;
                            $data['users'][$key]['assessment_report'][$key2]['correct'] = 1;
                        } else {
                            $temp_wrong++;
                            $data['users'][$key]['assessment_report'][$key2]['correct'] = 0;
                        }
                        $temp_options = explode(',', $report['q_options']);
                        $temp_arr     = array();

                        $temp_options_value = array();
                        $temp_options_value = $this->Challenge_model->get_option_value($report['q_options']);

                        $data['users'][$key]['assessment_report'][$key2]['qo_options_value'] = $temp_options_value;
                        $data['users'][$key]['assessment_report'][$key2]['qo_options']       = $temp_options;
                        $temp_q_ans                                                          = '';
                        $temp_a_ans                                                          = '';
                        $chr                                                                 = 65;

                        foreach ($temp_options as $value) {
                            if ($value == $report['q_answer']) {

                                if ($temp_q_ans == '') {
                                    $temp_q_ans = chr($chr);
                                } else {
                                    $temp_q_ans = $temp_q_ans . ',' . chr($chr);
                                }
                            }

                            if ($value == $report['ugar_answer']) {

                                if ($temp_a_ans == '') {
                                    $temp_a_ans = chr($chr);
                                } else {
                                    $temp_a_ans = $temp_a_ans . ',' . chr($chr);
                                }
                            }
                            $chr++;
                        }
                        $data['users'][$key]['assessment_report'][$key2]['correct_answer'] = $temp_q_ans;
                        $data['users'][$key]['assessment_report'][$key2]['user_answer']    = $temp_a_ans;
                    } else if ($report['q_type'] == 2) {

                        $user_answers     = explode(',', $report['q_answer']);
                        $original_answers = explode(',', $report['ugar_answer']);
                        sort($user_answers);
                        sort($original_answers);
                        if ($user_answers == $original_answers) {
                            $temp_correct++;
                            $data['users'][$key]['assessment_report'][$key2]['correct'] = 1;
                        } else {
                            $temp_wrong++;
                            $data['users'][$key]['assessment_report'][$key2]['correct'] = 0;
                        }

                        $temp_options                                                        = explode(',', $report['q_options']);
                        $data['users'][$key]['assessment_report'][$key2]['qo_options']       = $temp_options;
                        $temp_options_value                                                  = array();
                        $temp_options_value                                                  = $this->Challenge_model->get_option_value($report['q_options']);
                        $data['users'][$key]['assessment_report'][$key2]['qo_options_value'] = $temp_options_value;

                        $temp_q_ans = '';
                        $temp_a_ans = '';
                        $chr        = 65;

                        foreach ($temp_options as $value) {

                            $temp_q_opt = explode(',', $report['q_answer']);

                            foreach ($temp_q_opt as $opt) {
                                if ($value == $opt) {

                                    if ($temp_q_ans == '') {
                                        $temp_q_ans = chr($chr);
                                    } else {
                                        $temp_q_ans = $temp_q_ans . ',' . chr($chr);
                                    }
                                }
                            }


                            $temp_a_opt = explode(',', $report['ugar_answer']);
                            foreach ($temp_a_opt as $opt) {
                                if ($value == $opt) {

                                    if ($temp_a_ans == '') {
                                        $temp_a_ans = chr($chr);
                                    } else {
                                        $temp_a_ans = $temp_a_ans . ',' . chr($chr);
                                    }
                                }
                            }

                            $chr++;
                        }

                        $data['users'][$key]['assessment_report'][$key2]['correct_answer'] = $temp_q_ans;
                        $data['users'][$key]['assessment_report'][$key2]['user_answer']    = $temp_a_ans;
                    } else if ($report['q_type'] == 3) {
                        $data['users'][$key]['q_type'] = 3;
                    }
                }
            }
            $data['users'][$key]['count_not_tried'] = $temp_not_attempted;
            $data['users'][$key]['correct']         = $temp_correct;
            $data['users'][$key]['incorrect']       = $temp_wrong;
            $data['users'][$key]['percentage']      = round(($temp_correct / $data['users'][$key]['total_count']) * 100, 2);
        }

        $data['prev_id'] = '';
        $data['next_id'] = '';

        foreach ($data['users'] as $key => $assessment) {

            if ($assessment['id'] == $user_id) {

                $data['user'] = $assessment;
            }
        }

        //echo '<pre>';print_r($data);die();

        $this->load->view($this->config->item('theme') . '/user_generated_test_report', $data);
    }

    function print_user_generated_test($assessment_id, $user_id, $attempted_id) {
        $this->load->model(array('User_generated_model', 'Challenge_model'));
        $data                  = array();
        $session               = $this->auth->get_current_user_session('user');
        $data['session']       = $session;
        $data['assessment_id'] = $assessment_id;
        $data['user_id']       = $user_id;
        $data['attempted_id']  = $attempted_id;



        $data['users'] = $this->User_generated_model->attempted_test(array(
                                 'user_id'      => $user_id,
                                 'attempted_id' => $attempted_id
        ));

        $data['category_id']      = $data['users'][0]['uga_category'];
        $data['current_category'] = $this->User_generated_model->get_category_name($data['category_id']);
        $data['cz_title']         = $data['users'][0]['uga_title'];



        foreach ($data['users'] as $key => $assessment) {
            $data['users'][$key]['assessment_report'] = $this->User_generated_model->assessment_report(array(
                                     'attempted_id' => $assessment['attempted_id']
            ));
            $data['users'][$key]['total_count']       = count($data['users'][$key]['assessment_report']);
            $temp_correct                             = 0;
            $temp_wrong                               = 0;
            $temp_not_attempted                       = 0;
            $data['users'][$key]['q_type']            = 1;
            foreach ($data['users'][$key]['assessment_report'] as $key2 => $report) {

                if ($report['ugar_answer'] == '' || empty($report['ugar_answer'])) {
                    $temp_not_attempted++;
                    $data['users'][$key]['assessment_report'][$key2]['correct']    = 2;
                    $temp_options                                                  = explode(',', $report['q_options']);
                    $data['users'][$key]['assessment_report'][$key2]['qo_options'] = $temp_options;

                    $temp_options_value = array();
                    $temp_options_value = $this->Challenge_model->get_option_value($report['q_options']);

                    $data['users'][$key]['assessment_report'][$key2]['qo_options_value'] = $temp_options_value;

                    foreach ($temp_options as $value) {
                        if ($value == $report['q_answer']) {

                            if ($temp_q_ans == '') {
                                $temp_q_ans = chr($chr);
                            } else {
                                $temp_q_ans = $temp_q_ans . ',' . chr($chr);
                            }
                        }
                        $chr++;
                    }

                    $data['users'][$key]['assessment_report'][$key2]['correct_answer'] = $temp_q_ans;
                } else {

                    if ($report['q_type'] == 1) {

                        if ($report['q_answer'] == $report['ugar_answer']) {
                            $temp_correct++;
                            $data['users'][$key]['assessment_report'][$key2]['correct'] = 1;
                        } else {
                            $temp_wrong++;
                            $data['users'][$key]['assessment_report'][$key2]['correct'] = 0;
                        }
                        $temp_options = explode(',', $report['q_options']);
                        $temp_arr     = array();

                        $temp_options_value = array();
                        $temp_options_value = $this->Challenge_model->get_option_value($report['q_options']);

                        $data['users'][$key]['assessment_report'][$key2]['qo_options_value'] = $temp_options_value;
                        $data['users'][$key]['assessment_report'][$key2]['qo_options']       = $temp_options;
                        $temp_q_ans                                                          = '';
                        $temp_a_ans                                                          = '';
                        $chr                                                                 = 65;

                        foreach ($temp_options as $value) {
                            if ($value == $report['q_answer']) {

                                if ($temp_q_ans == '') {
                                    $temp_q_ans = chr($chr);
                                } else {
                                    $temp_q_ans = $temp_q_ans . ',' . chr($chr);
                                }
                            }

                            if ($value == $report['ugar_answer']) {

                                if ($temp_a_ans == '') {
                                    $temp_a_ans = chr($chr);
                                } else {
                                    $temp_a_ans = $temp_a_ans . ',' . chr($chr);
                                }
                            }
                            $chr++;
                        }
                        $data['users'][$key]['assessment_report'][$key2]['correct_answer'] = $temp_q_ans;
                        $data['users'][$key]['assessment_report'][$key2]['user_answer']    = $temp_a_ans;
                    } else if ($report['q_type'] == 2) {

                        $user_answers     = explode(',', $report['q_answer']);
                        $original_answers = explode(',', $report['ugar_answer']);
                        sort($user_answers);
                        sort($original_answers);
                        if ($user_answers == $original_answers) {
                            $temp_correct++;
                            $data['users'][$key]['assessment_report'][$key2]['correct'] = 1;
                        } else {
                            $temp_wrong++;
                            $data['users'][$key]['assessment_report'][$key2]['correct'] = 0;
                        }

                        $temp_options                                                        = explode(',', $report['q_options']);
                        $data['users'][$key]['assessment_report'][$key2]['qo_options']       = $temp_options;
                        $temp_options_value                                                  = array();
                        $temp_options_value                                                  = $this->Challenge_model->get_option_value($report['q_options']);
                        $data['users'][$key]['assessment_report'][$key2]['qo_options_value'] = $temp_options_value;

                        $temp_q_ans = '';
                        $temp_a_ans = '';
                        $chr        = 65;

                        foreach ($temp_options as $value) {

                            $temp_q_opt = explode(',', $report['q_answer']);

                            foreach ($temp_q_opt as $opt) {
                                if ($value == $opt) {

                                    if ($temp_q_ans == '') {
                                        $temp_q_ans = chr($chr);
                                    } else {
                                        $temp_q_ans = $temp_q_ans . ',' . chr($chr);
                                    }
                                }
                            }


                            $temp_a_opt = explode(',', $report['ugar_answer']);
                            foreach ($temp_a_opt as $opt) {
                                if ($value == $opt) {

                                    if ($temp_a_ans == '') {
                                        $temp_a_ans = chr($chr);
                                    } else {
                                        $temp_a_ans = $temp_a_ans . ',' . chr($chr);
                                    }
                                }
                            }

                            $chr++;
                        }

                        $data['users'][$key]['assessment_report'][$key2]['correct_answer'] = $temp_q_ans;
                        $data['users'][$key]['assessment_report'][$key2]['user_answer']    = $temp_a_ans;
                    } else if ($report['q_type'] == 3) {
                        $data['users'][$key]['q_type'] = 3;
                    }
                }
            }
            $data['users'][$key]['count_not_tried'] = $temp_not_attempted;
            $data['users'][$key]['correct']         = $temp_correct;
            $data['users'][$key]['incorrect']       = $temp_wrong;
            $data['users'][$key]['percentage']      = round(($temp_correct / $data['users'][$key]['total_count']) * 100, 2);
        }

        $data['prev_id'] = '';
        $data['next_id'] = '';

        foreach ($data['users'] as $key => $assessment) {

            if ($assessment['id'] == $user_id) {

                $data['user'] = $assessment;
            }
        }

        //echo '<pre>';print_r($data);die();

        $this->load->view($this->config->item('theme') . '/user_generated_test_report_print', $data);
    }

    function assessment_details($assessment_id = '', $user_id = '', $attempted_id = '') {
        $this->load->model(array('User_model', 'Challenge_model'));
        $data                  = array();
        $session               = $this->auth->get_current_user_session('user');
        $data['session']       = $session;
        $data['assessment_id'] = $assessment_id;
        $data['user_id']       = $user_id;
        $data['attempted_id']  = $attempted_id;

        $data['users'] = $this->User_model->assessment_list(array(
                                 'user_id'      => $user_id,
                                 'attempted_id' => $attempted_id
        ));

        if (empty($data['users']) || $data['attempted_id'] == '' || $data['assessment_id'] == '' || $data['user_id'] == '') {
            redirect('dashboard');
        }


        $data['current_category'] = $data['users'][0]['ct_name'];
        $data['cz_title']         = $data['users'][0]['cb_title'];



        foreach ($data['users'] as $key => $assessment) {

            $data['users'][$key]['assessment_report'] = $this->User_model->assessment_report(array(
                                     'attempted_id' => $attempted_id,
                                     'user_id'      => $user_id
            ));

            $data['users'][$key]['total_count'] = count($data['users'][$key]['assessment_report']);

            $temp_correct                  = 0;
            $temp_wrong                    = 0;
            $temp_not_attempted            = 0;
            $data['users'][$key]['q_type'] = 1;
            foreach ($data['users'][$key]['assessment_report'] as $key2 => $report) {

                if ($report['ar_answer'] == '' || empty($report['ar_answer'])) {
                    $temp_not_attempted++;
                    $data['users'][$key]['assessment_report'][$key2]['correct']    = 2;
                    $temp_options                                                  = explode(',', $report['q_options']);
                    $data['users'][$key]['assessment_report'][$key2]['qo_options'] = $temp_options;

                    $temp_options_value = array();
                    $temp_options_value = $this->Challenge_model->get_option_value($report['q_options']);

                    $data['users'][$key]['assessment_report'][$key2]['qo_options_value'] = $temp_options_value;

                    foreach ($temp_options as $value) {
                        if ($value == $report['q_answer']) {

                            if ($temp_q_ans == '') {
                                $temp_q_ans = chr($chr);
                            } else {
                                $temp_q_ans = $temp_q_ans . ',' . chr($chr);
                            }
                        }
                        $chr++;
                    }

                    $data['users'][$key]['assessment_report'][$key2]['correct_answer'] = $temp_q_ans;
                } else {

                    if ($report['q_type'] == 1) {

                        if ($report['q_answer'] == $report['ar_answer']) {
                            $temp_correct++;
                            $data['users'][$key]['assessment_report'][$key2]['correct'] = 1;
                        } else {
                            $temp_wrong++;
                            $data['users'][$key]['assessment_report'][$key2]['correct'] = 0;
                        }
                        $temp_options = explode(',', $report['q_options']);
                        $temp_arr     = array();

                        $temp_options_value = array();
                        $temp_options_value = $this->Challenge_model->get_option_value($report['q_options']);

                        $data['users'][$key]['assessment_report'][$key2]['qo_options_value'] = $temp_options_value;
                        $data['users'][$key]['assessment_report'][$key2]['qo_options']       = $temp_options;
                        $temp_q_ans                                                          = '';
                        $temp_a_ans                                                          = '';
                        $chr                                                                 = 65;

                        foreach ($temp_options as $value) {
                            if ($value == $report['q_answer']) {

                                if ($temp_q_ans == '') {
                                    $temp_q_ans = chr($chr);
                                } else {
                                    $temp_q_ans = $temp_q_ans . ',' . chr($chr);
                                }
                            }

                            if ($value == $report['ar_answer']) {

                                if ($temp_a_ans == '') {
                                    $temp_a_ans = chr($chr);
                                } else {
                                    $temp_a_ans = $temp_a_ans . ',' . chr($chr);
                                }
                            }
                            $chr++;
                        }
                        $data['users'][$key]['assessment_report'][$key2]['correct_answer'] = $temp_q_ans;
                        $data['users'][$key]['assessment_report'][$key2]['user_answer']    = $temp_a_ans;
                    } else if ($report['q_type'] == 2) {

                        $user_answers     = explode(',', $report['q_answer']);
                        $original_answers = explode(',', $report['ar_answer']);
                        sort($user_answers);
                        sort($original_answers);
                        if ($user_answers == $original_answers) {
                            $temp_correct++;
                            $data['users'][$key]['assessment_report'][$key2]['correct'] = 1;
                        } else {
                            $temp_wrong++;
                            $data['users'][$key]['assessment_report'][$key2]['correct'] = 0;
                        }

                        $temp_options                                                        = explode(',', $report['q_options']);
                        $data['users'][$key]['assessment_report'][$key2]['qo_options']       = $temp_options;
                        $temp_options_value                                                  = array();
                        $temp_options_value                                                  = $this->Challenge_model->get_option_value($report['q_options']);
                        $data['users'][$key]['assessment_report'][$key2]['qo_options_value'] = $temp_options_value;

                        $temp_q_ans = '';
                        $temp_a_ans = '';
                        $chr        = 65;

                        foreach ($temp_options as $value) {

                            $temp_q_opt = explode(',', $report['q_answer']);

                            foreach ($temp_q_opt as $opt) {
                                if ($value == $opt) {

                                    if ($temp_q_ans == '') {
                                        $temp_q_ans = chr($chr);
                                    } else {
                                        $temp_q_ans = $temp_q_ans . ',' . chr($chr);
                                    }
                                }
                            }


                            $temp_a_opt = explode(',', $report['ar_answer']);
                            foreach ($temp_a_opt as $opt) {
                                if ($value == $opt) {

                                    if ($temp_a_ans == '') {
                                        $temp_a_ans = chr($chr);
                                    } else {
                                        $temp_a_ans = $temp_a_ans . ',' . chr($chr);
                                    }
                                }
                            }

                            $chr++;
                        }

                        $data['users'][$key]['assessment_report'][$key2]['correct_answer'] = $temp_q_ans;
                        $data['users'][$key]['assessment_report'][$key2]['user_answer']    = $temp_a_ans;
                    } else if ($report['q_type'] == 3) {
                        $data['users'][$key]['q_type'] = 3;
                    }
                }
            }
            $data['users'][$key]['count_not_tried'] = $temp_not_attempted;
            $data['users'][$key]['correct']         = $temp_correct;
            $data['users'][$key]['incorrect']       = $temp_wrong;
            $data['users'][$key]['percentage']      = round(($temp_correct / $data['users'][$key]['total_count']) * 100, 2);
        }


        foreach ($data['users'] as $key => $assessment) {

            if ($assessment['id'] == $user_id) {

                $data['user'] = $assessment;
            }
        }

        //echo '<pre>';print_r($data);die();

        $this->load->view($this->config->item('theme') . '/assessment_report', $data);
    }

    function print_assessment_report($assessment_id, $user_id, $attempted_id) {
        $this->load->model(array('User_model', 'Challenge_model'));
        $data                  = array();
        $session               = $this->auth->get_current_user_session('user');
        $data['session']       = $session;
        $data['assessment_id'] = $assessment_id;
        $data['user_id']       = $user_id;
        $data['attempted_id']  = $attempted_id;



        $data['users'] = $this->User_model->assessment_list(array(
                                 'user_id'      => $user_id,
                                 'attempted_id' => $attempted_id
        ));

        //echo '<pre>';print_r($data);die();

        $data['current_category'] = $data['users'][0]['ct_name'];
        $data['cz_title']         = $data['users'][0]['cb_title'];



        foreach ($data['users'] as $key => $assessment) {

            $data['users'][$key]['assessment_report'] = $this->User_model->assessment_report(array(
                                     'attempted_id' => $attempted_id,
                                     'user_id'      => $user_id
            ));

            $data['users'][$key]['total_count'] = count($data['users'][$key]['assessment_report']);

            $temp_correct                  = 0;
            $temp_wrong                    = 0;
            $temp_not_attempted            = 0;
            $data['users'][$key]['q_type'] = 1;
            foreach ($data['users'][$key]['assessment_report'] as $key2 => $report) {

                if ($report['ar_answer'] == '' || empty($report['ar_answer'])) {
                    $temp_not_attempted++;
                    $data['users'][$key]['assessment_report'][$key2]['correct']    = 2;
                    $temp_options                                                  = explode(',', $report['q_options']);
                    $data['users'][$key]['assessment_report'][$key2]['qo_options'] = $temp_options;

                    $temp_options_value = array();
                    $temp_options_value = $this->Challenge_model->get_option_value($report['q_options']);

                    $data['users'][$key]['assessment_report'][$key2]['qo_options_value'] = $temp_options_value;

                    foreach ($temp_options as $value) {
                        if ($value == $report['q_answer']) {

                            if ($temp_q_ans == '') {
                                $temp_q_ans = chr($chr);
                            } else {
                                $temp_q_ans = $temp_q_ans . ',' . chr($chr);
                            }
                        }
                        $chr++;
                    }

                    $data['users'][$key]['assessment_report'][$key2]['correct_answer'] = $temp_q_ans;
                } else {

                    if ($report['q_type'] == 1) {

                        if ($report['q_answer'] == $report['ar_answer']) {
                            $temp_correct++;
                            $data['users'][$key]['assessment_report'][$key2]['correct'] = 1;
                        } else {
                            $temp_wrong++;
                            $data['users'][$key]['assessment_report'][$key2]['correct'] = 0;
                        }
                        $temp_options = explode(',', $report['q_options']);
                        $temp_arr     = array();

                        $temp_options_value = array();
                        $temp_options_value = $this->Challenge_model->get_option_value($report['q_options']);

                        $data['users'][$key]['assessment_report'][$key2]['qo_options_value'] = $temp_options_value;
                        $data['users'][$key]['assessment_report'][$key2]['qo_options']       = $temp_options;
                        $temp_q_ans                                                          = '';
                        $temp_a_ans                                                          = '';
                        $chr                                                                 = 65;

                        foreach ($temp_options as $value) {
                            if ($value == $report['q_answer']) {

                                if ($temp_q_ans == '') {
                                    $temp_q_ans = chr($chr);
                                } else {
                                    $temp_q_ans = $temp_q_ans . ',' . chr($chr);
                                }
                            }

                            if ($value == $report['ar_answer']) {

                                if ($temp_a_ans == '') {
                                    $temp_a_ans = chr($chr);
                                } else {
                                    $temp_a_ans = $temp_a_ans . ',' . chr($chr);
                                }
                            }
                            $chr++;
                        }
                        $data['users'][$key]['assessment_report'][$key2]['correct_answer'] = $temp_q_ans;
                        $data['users'][$key]['assessment_report'][$key2]['user_answer']    = $temp_a_ans;
                    } else if ($report['q_type'] == 2) {

                        $user_answers     = explode(',', $report['q_answer']);
                        $original_answers = explode(',', $report['ar_answer']);
                        sort($user_answers);
                        sort($original_answers);
                        if ($user_answers == $original_answers) {
                            $temp_correct++;
                            $data['users'][$key]['assessment_report'][$key2]['correct'] = 1;
                        } else {
                            $temp_wrong++;
                            $data['users'][$key]['assessment_report'][$key2]['correct'] = 0;
                        }

                        $temp_options                                                        = explode(',', $report['q_options']);
                        $data['users'][$key]['assessment_report'][$key2]['qo_options']       = $temp_options;
                        $temp_options_value                                                  = array();
                        $temp_options_value                                                  = $this->Challenge_model->get_option_value($report['q_options']);
                        $data['users'][$key]['assessment_report'][$key2]['qo_options_value'] = $temp_options_value;

                        $temp_q_ans = '';
                        $temp_a_ans = '';
                        $chr        = 65;

                        foreach ($temp_options as $value) {

                            $temp_q_opt = explode(',', $report['q_answer']);

                            foreach ($temp_q_opt as $opt) {
                                if ($value == $opt) {

                                    if ($temp_q_ans == '') {
                                        $temp_q_ans = chr($chr);
                                    } else {
                                        $temp_q_ans = $temp_q_ans . ',' . chr($chr);
                                    }
                                }
                            }


                            $temp_a_opt = explode(',', $report['ar_answer']);
                            foreach ($temp_a_opt as $opt) {
                                if ($value == $opt) {

                                    if ($temp_a_ans == '') {
                                        $temp_a_ans = chr($chr);
                                    } else {
                                        $temp_a_ans = $temp_a_ans . ',' . chr($chr);
                                    }
                                }
                            }

                            $chr++;
                        }

                        $data['users'][$key]['assessment_report'][$key2]['correct_answer'] = $temp_q_ans;
                        $data['users'][$key]['assessment_report'][$key2]['user_answer']    = $temp_a_ans;
                    } else if ($report['q_type'] == 3) {
                        $data['users'][$key]['q_type'] = 3;
                    }
                }
            }
            $data['users'][$key]['count_not_tried'] = $temp_not_attempted;
            $data['users'][$key]['correct']         = $temp_correct;
            $data['users'][$key]['incorrect']       = $temp_wrong;
            $data['users'][$key]['percentage']      = round(($temp_correct / $data['users'][$key]['total_count']) * 100, 2);
        }


        foreach ($data['users'] as $key => $assessment) {

            if ($assessment['id'] == $user_id) {

                $data['user'] = $assessment;
            }
        }

        //echo '<pre>';print_r($data);die();

        $this->load->view($this->config->item('theme') . '/assessment_report_print', $data);
    }

    function logout() {
        $this->auth->logout();
        //when someone logs out, automatically redirect them to the login page.
        $this->session->set_flashdata('message', lang('message_logged_out'));
        redirect('/login');
    }

    /* Function for removing courses from user wish list
     * By Yadu Chandran  */

    function remove() {
        $this->load->model('Course_model');
        $response          = array();
        $response['error'] = false;
        $course_id         = $this->input->post('course_id');
        $user_id           = $this->input->post('user_id');
        $course            = $this->Course_model->remove_course_wishlist(array(
                                 'course_id' => $course_id,
                                 'user_id'   => $user_id
        ));
        if ($course) {
            $response['success'] = true;
            $response['id']      = $course;
            $response['message'] = "Course removed successfully from wishlist.";
        } else {
            $response['success'] = false;
            $response['message'] = "Failed removing course from wishlist.";
        }
        echo json_encode($response);
    }

    /* Function for removing courses from user wish list
     * By Yadu Chandran  */

    function change_password() {
        $this->load->model('User_model');
        $session           = $this->auth->get_current_user_session('user');
        $response          = array();
        $data              = array();
        $response['error'] = false;
        $current_password  = $this->input->post('current_pass');
        $new_password      = $this->input->post('new_password');
        $confirm_password  = $this->input->post('confirm_pass');
        $check_password    = $this->User_model->get_user_password(array(
                                 'user_id' => $session['id']
        ));
        if (sha1($current_password) == $check_password) {
            $response['error']      = true;
            $response['message']    = "success";
            $filter                 = array();
            $filter['id']           = $session['id'];
            $data['us_password']    = sha1($confirm_password);
            $data['user_pass']      = $this->User_model->update_password($data,$filter);

            /*Log creation*/
            $user_data                          = array();
            $user_data['user_id']               = $session['id'];
            $user_data['username']              = $session['us_name'];
            $user_data['useremail']              = $session['us_email'];
            $user_data['user_type']             = $session['us_role_id'];
            $user_data['phone_number']          = $session['us_phone'];
            $message_template                   = array();
            $message_template['username']       = $session['us_name'];
            $triggered_activity                 = 'student_password_changed';
            log_activity($triggered_activity, $user_data, $message_template);
            echo json_encode($response);
        } else {
            $response['error']   = true;
            $response['message'] = "Old password is incorrect";
            echo json_encode($response);
        }
    }

    function update_profile() {
        $this->load->model('User_model');
        $data             = array();
        $response         = array();
        $session          = $this->auth->get_current_user_session('user');
        $data['id']       = $session['id'];
        $data['us_name']  = $this->input->post('us_name');
        $data['us_phone'] = $this->input->post('us_phone');
        $data['us_about'] = $this->input->post('us_about');
        if ($this->User_model->save($data)) {
            $response['error']   = true;
            $response['message'] = "success";
            echo json_encode($response);
        }
    }

    /*
     * Function used to get the field values in auto suggetion list according to the field id
     * Created by :Neehu KP
     * Created at : 06/01/2017
     */
    /*
      function get_fileds_value()
      {

      $response = array();
      $field_id = $this->input->post('field_id');
      $keyword  = $this->input->post('keyword');

      $response['field_values'] = array();
      $queue_values             = array();
      $check_autosugsestion     = $this->User_model->check_autosuggestion_status($field_id);
      if ($check_autosugsestion['pf_auto_suggestion'] == 1) {
      $old_values = $this->User_model->get_field_suggetion_values(array(
      'field_id' => $field_id,
      'keyword' => $keyword
      ));
      if (!empty($old_values)) {
      foreach ($old_values as $old_value) {
      $old_value = isset($old_value['us_profile_fields']) ? explode('{#}', $old_value['us_profile_fields']) : array();
      if (!empty($old_value)) {
      foreach ($old_value as $field) {
      $field      = substr($field, 2);
      $field      = substr($field, 0, -2);
      $temp_field = explode('{=>}', $field);
      $key        = isset($temp_field[0]) ? $temp_field[0] : 0;
      $value      = isset($temp_field[1]) ? $temp_field[1] : '';
      if ($key == $field_id && !in_array($value, $queue_values)) {
      $queue_values[]             = $value;
      $response['field_values'][] = array(
      'upf_field_id' => $key,
      'upf_field_value' => $value
      );
      }
      }
      }
      }
      }
      echo json_encode($response);
      }


      }
     */

    function get_fileds_value() {
        $this->load->model('User_model');
        $response = array();
        $field_id = $this->input->post('field_id');
        $keyword  = $this->input->post('keyword');

        $check_autosugsestion = $this->User_model->check_autosuggestion_status($field_id);
        if ($check_autosugsestion['pf_auto_suggestion'] == 1) {
            $response['field_values'] = $this->User_model->get_field_suggetion_values(array('field_id' => $field_id, 'keyword' => $keyword));
            echo json_encode($response);
        }
    }

    function calendar_events($assignment_status = false) {
        $this->load->model('User_model');
        $this->load->library(array(
                                 'ofabeeevents'
        ));
        $year  = $this->input->post('year');
        $month = $this->input->post('month');
        //$assignment_status = 1;
        $assignment_status = ($assignment_status)? $assignment_status : false;
        //print_r($_POST);
        //$year   = 2019;
        //$month  = 7;
        
        $user          = $this->auth->get_current_user_session('user');
        
        $range         = array();
        $range['from'] = date("Y-m-d", strtotime($year . '-' . $month . '-' . '1'));
        $range['to']   = date('Y-m-t', strtotime($year . '-' . $month . '-' . '1'));
        
        $lectures['assignments'] = $this->User_model->get_assignment_datewise(array(
                                 'user_id' => $user['id'],
                                 'from'    => $range['from'],
                                 'to'      => $range['to'],
                                 'status'  => $assignment_status
        ));
        $lectures['live']        = $this->User_model->get_live_datewise(array(
                                 'user_id' => $user['id'],
                                 'from'    => $range['from'],
                                 'to'      => $range['to']
        ));
        $student_institute      = isset($user['us_institute_id']) ? $user['us_institute_id'] : '';
        if(isset($user['us_groups']) && $user['us_groups']!='') { $student_batches =  explode(",",$user['us_groups']); } else { $student_batches = ''; };
        
        $this->load->model('Course_model');
        $courses        = $this->Course_model->course_enrolled(array('user_id' => $user['id'], 'select' => 'course_subscription.cs_course_id')); 
        $student_courses = array();
        if(!empty($courses))
        {
            for($i = 0; $i < count($courses); $i++){
                $student_courses[] = $courses[$i]['cs_course_id'];
            }
        }
        //echo $this->db->last_query();die;
        //print_r($student_courses);
        $custom_events['events']  = $this->Events_model->getEvents(array(
                                 'user_id'      => $user['id'],
                                 'date_from'    => $range['from'],
                                  'date_to'      => $range['to'],
                                  'institute_id' => $student_institute,
                                  'batches'      => $student_batches,
                                  'courses'      => $student_courses
        )); 
        //echo $this->db->last_query();die;
        //print_r($custom_events); die;
        $lectures['events']      = isset($custom_events['events']) ? $custom_events['events'] : array();
        $events       = array();
        $sorted_array = array();
        
        foreach ($lectures['assignments'] as $assignment) {
            // $events[]                        = $assignment;
            $events[]                        = array(
                'id' => $assignment['id'], 
                'clt_name' => 'Assignment',
                'cl_lecture_type' => '8',
                'event_name' => $assignment['cl_lecture_name'], 
                'event_date' => $assignment['dt_last_date'],
                'event_date_time' => $assignment['descrptive_date_time'],
                'event_link' => site_url('materials/course').'/'.$assignment['cl_course_id'].'/'.$assignment['id']
            );//$assignment;
            $assignment['type']              = 'assignment';
            $sorted_array[$assignment['id'].'_assignment'] = $assignment['descrptive_date_time'];
        } 
        foreach ($lectures['live'] as $live) {
            // $events[]                  = $live;
            $events[]                        = array(
                'id' => $live['id'], 
                'clt_name' => 'Live',
                'cl_lecture_type' => '7',
                'event_name' => $live['cl_lecture_name'], 
                'event_date' => $live['ll_date'],
                'event_date_time' => $live['live_date_time'],
                'event_link' => (($live['ll_mode'] == 2) ? (base_url('conference') . '/?name=' . $user['us_name'] . '&userid=' . $user['id'] . '&room=' . $live['live_id'] . '&type=viewer&app=web') : (site_url('/live') . '/join/' . $live['live_id']))
            );//$live;
            $live['type']              = 'live';
            $sorted_array[$live['id'].'_live'] = $live['live_date_time'];
        }
        if (!empty($lectures['events'])) {
            foreach ($lectures['events'] as $e_key => $event) {
                $lectures['events'][$e_key]['cl_lecture_type'] = 0;
                $events[]                        = array(
                    'id' => $event['id'], 
                    'clt_name' => 'Event',
                    'cl_lecture_type' => '0',
                    'event_name' => $event['ev_name'], 
                    'event_date' => $event['ev_date'],
                    'event_date_time' => $event['events_date_time'],
                    'event_link' => site_url('events').'/event/'.base64_encode($event['id'])
                );
                // $events[]                                      = $lectures['events'][$e_key];
                $event['type']                                 = 'general';
                $sorted_array[$event['id'].'_event']                    = $event['events_date_time'];
            }
        }
        //print_r($lectures); die;
        array_multisort($sorted_array, SORT_ASC, $events);
        $output = array();
        $link   = '';
        foreach ($events as $event) {
            $link = $event['event_link'];//'javascript:void(0)';
            switch ($event['cl_lecture_type']) {
                case 7:
                    $link = $event['event_link'];//$event['ll_mode'] == 2 ? base_url('conference') . '/?name=' . $user['us_name'] . '&userid=' . $user['id'] . '&room=' . $event['live_id'] . '&type=viewer&app=web' : site_url('/live') . '/join/' . $event['live_id'];
                    $output[$event['event_date']][] = array(
                                             'link'    => $link,
                                             'title'   => 'Live lecture',
                                             'message' => 'A <strong>live</strong> named <strong>' . $event['event_name'] . '</strong> has been scheduled by ' . date('g:i A', strtotime($event['event_date_time'])) . '.'
                    );
                    break;
                case 8:
                    $link                             = $event['event_link'];//site_url('materials/course') . '/' . $event['cl_course_id'] . '/0/0#' . $event['id'];
                    $output[$event['event_date']][] = array(
                                             'link'    => $link,
                                             'title'   => 'Assignment submission',
                                             'message' => 'An <strong>assignment</strong> named <strong>' . $event['event_name'] . '</strong> to be submitted.'
                    );
                    break;
                case 0:
                    $link                        = $event['event_link'];//site_url('events') . '/event/' . base64_encode($event['id']);
                    $output[$event['event_date']][] = array(
                                             'link'    => $link,
                                             'title'   => 'Custom Event',
                                             'message' => 'An <strong>event</strong> named <strong>' . $event['event_name'] . '</strong> has been scheduled by ' . date('g:i A', strtotime($event['event_date_time'])) . '.'
                    );
                    break;
            }
        }
        $main_out = array();
        $inc      = 0;
        foreach ($output as $out_key => $out) {
            $main_out[$inc]['date']   = $out_key;
            $main_out[$inc]['events'] = $out;
            $inc++;
        }
        //echo '<pre>';print_r($main_out); die;
        echo json_encode($main_out);
    }


    function crop_image($uploaded_data) {
        $source_path = $uploaded_data['full_path'];
        define('DESIRED_IMAGE_WIDTH', 255);
        define('DESIRED_IMAGE_HEIGHT', 255);
        /*
         * Add file validation code here
         */

        list($source_width, $source_height, $source_type) = getimagesize($source_path);

        switch ($source_type) {
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

        $source_aspect_ratio  = $source_width / $source_height;
        $desired_aspect_ratio = DESIRED_IMAGE_WIDTH / DESIRED_IMAGE_HEIGHT;

        if ($source_aspect_ratio > $desired_aspect_ratio) {
            /*
             * Triggered when source image is wider
             */
            $temp_height = DESIRED_IMAGE_HEIGHT;
            $temp_width  = (int) (DESIRED_IMAGE_HEIGHT * $source_aspect_ratio);
        } else {
            /*
             * Triggered otherwise (i.e. source image is similar or taller)
             */
            $temp_width  = DESIRED_IMAGE_WIDTH;
            $temp_height = (int) (DESIRED_IMAGE_WIDTH / $source_aspect_ratio);
        }

        /*
         * Resize the image into a temporary GD image
         */

        $temp_gdim = imagecreatetruecolor($temp_width, $temp_height);
        imagecopyresampled($temp_gdim, $source_gdim, 0, 0, 0, 0, $temp_width, $temp_height, $source_width, $source_height);

        /*
         * Copy cropped region from temporary image into the desired GD image
         */

        $x0           = ($temp_width - DESIRED_IMAGE_WIDTH) / 2;
        $y0           = ($temp_height - DESIRED_IMAGE_HEIGHT) / 2;
        $desired_gdim = imagecreatetruecolor(DESIRED_IMAGE_WIDTH, DESIRED_IMAGE_HEIGHT);
        imagecopy($desired_gdim, $temp_gdim, 0, 0, $x0, $y0, DESIRED_IMAGE_WIDTH, DESIRED_IMAGE_HEIGHT);

        /*
         * Render the image
         * Alternatively, you can save the image in file-system or database
         */

        //header('Content-type: image/jpeg');
        $directory = user_upload_path();
        $this->make_directory($directory);
        imagejpeg($desired_gdim, $directory . $uploaded_data['user_id'] . '.jpg');

        /*
         * Add clean-up code here
         */
        return $uploaded_data['user_id'] . '.jpg';
    }

    private function make_directory($path = false) {
        if (!$path) {
            return false;
        }
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }

    function mentor_profile_json() {
        $this->load->model('Development_model');
        $response = array();
        $about    = $this->input->post('about');
        $session  = $this->auth->get_current_user_session('user');
        if (isset($session)) {
            $resp = $this->Development_model->db_update_mentor_profile(array(
                                     'mentor_id' => $session['id'],
                                     'about'     => $about
            ));
            if ($resp) {
                $response['error']   = false;
                $response['message'] = "Profile updated successfully.";
            } else {
                $response['error']   = true;
                $response['message'] = "Failed update.";
            }
        } else {
            $response['error']   = true;
            $response['message'] = "Please login and try editing profile.";
        }

        echo json_encode($response);
    }

    private function test_report() {
        $this->load->model('Report_model');
        $data                       = array();
        $user                       = $this->auth->get_current_user_session('user');
        $courses                    = $this->Report_model->enrolled_course(array(
                                 'user_id' => $user['id']
        ));
        $data['assessment_courses'] = array();
        //$data['course_completion']  = array();
        if (!empty($courses)) {
            foreach ($courses as $course) {
                $course['report']                                 = $this->Report_model->user_course_assessment_report(array(
                                         'user_id'   => $user['id'],
                                         'course_id' => $course['course_id']
                ));
                $data['assessment_courses'][$course['course_id']] = $course;
                //$data['course_completion'][$course['course_id']] = $this->Report_model->course_completion(array('user_id' => $user['id'], 'course_id' => $course['course_id']));
            }
        }
        //echo '<pre>'; print_r($data);die;
        return $data;
    }

    function common_report() {
        $this->load->model(array(
                                 'Report_model',
                                 'Plan_model'
        ));
        $data                      = array();
        $user                      = $this->auth->get_current_user_session('user');
        $courses                   = $this->Report_model->enrolled_course(array(
                                 'user_id' => $user['id']
        ));
        $data['course_completion'] = array();
        $data['upcomming']         = $this->calendar(array(
                                 'date' => date('Y-m-d')
        ));
        if (!empty($courses)) {
            foreach ($courses as $course) {
                $data['course_completion'][$course['course_id']] = $this->Report_model->course_completion(array(
                                         'user_id'   => $user['id'],
                                         'course_id' => $course['course_id']
                ));
            }
        }

        $range                     = '0';
        $study_plan                = $this->Plan_model->plan(array(
                                 'user_id' => $user['id'],
                                 'week'    => $this->get_week_format(array(
                                                          'range' => $range
                                 ))
        ));
        $study_plan['sp_lectures'] = json_decode($study_plan['sp_lectures']);
        $data['study_plan']        = $this->Plan_model->plan_report(array(
                                 'user_id'     => $user['id'],
                                 'lecture_ids' => $study_plan['sp_lectures']
        ));

        //echo '<pre>'; print_r($data);die;
        $this->load->view($this->config->item('theme') . '/course_complete_report', $data);
    }

    private function get_week_format($param = array()) {
        $range = isset($param['range']) ? $param['range'] : 0;
        $date  = isset($param['date']) ? $param['date'] : date('Y-m-d');
        if ($range) {
            $week = strtotime($range . " week +1 day");
        } else {
            $week = strtotime($date);
            $day  = date('D', $week);
            switch ($day) {
                case "Sun":
                    $date = date('Y-m-d', strtotime('+1 day', strtotime($date)));
                    $week = strtotime($date);
                    break;
                case "Sat":
                    $date = date('Y-m-d', strtotime('-1 day', strtotime($date)));
                    $week = strtotime($date);
                    break;
                default:
                    break;
            }
        }

        $start_week = strtotime("last sunday midnight", $week);
        $end_week   = strtotime("next saturday", $start_week);

        $start_week = date("d-m-Y", $start_week);
        $end_week   = date("d-m-Y", $end_week);

        return $start_week . '<=>' . $end_week;
    }

    /* Written By Alex. */

    private function get_assignments($user = array()) {
        $this->load->model('User_model');
        $courses = $this->User_model->get_assignment_courses(array(
                                 'user_id' => $user['id']
        ));
        foreach ($courses as $c_key => $course) {
            $courses[$c_key]['assignments'] = $this->User_model->get_asseignments(array(
                                     'course_id' => $course['id'],
                                     'user_id'   => $user['id']
            ));
        }

        return $courses;
    }

    private function calendar($param = array()) {
        $this->load->model('User_model');
        $this->load->library(array(
                                 'ofabeeevents'
        ));
        $range         = array();
        $date_input    = isset($param['date']) ? $param['date'] : date('Y-m-d');
        $range['from'] = date("Y-m-d", strtotime($date_input));
        $range['to']   = date('Y-m-d', strtotime("+7 days"));
        $user          = $this->auth->get_current_user_session('user');
        $courses       = isset($param['courses']) ? $param['courses'] : array();
        $assignment_status = isset($param['assignment_status'])? $param['assignment_status'] : false;
        $lectures['assignments'] = $this->User_model->get_assignment_datewise(array(
                                 'user_id' => $user['id'],
                                 'from'    => $range['from'],
                                 'to'      => $range['to'],
                                 'status'  => $assignment_status
        ));
        $lectures['live']        = $this->User_model->get_live_datewise(array(
                                 'user_id' => $user['id'],
                                 'from'    => $range['from'],
                                 'to'      => $range['to']
        ));
        $custom_events           = $this->ofabeeevents->getEvents(array(
                                    'user'   => array('institute_id' => $user['us_institute_id'], 'batches' => $user['us_groups'], 'courses' => $courses),
                                    'user_id'   => $user['id'],
                                    'date_from' => $range['from'],
                                    'date_to'   => $range['to']
        ));
        $lectures['events']      = isset($custom_events['events']) ? $custom_events['events'] : array();
        $events       = array();
        $sorted_array = array();
        foreach ($lectures['assignments'] as $assignment) {
            $events[]                        = array(
                                                    'id' => $assignment['id'], 
                                                    'clt_name' => 'Assignment',
                                                    'cl_lecture_type' => '8',
                                                    'event_name' => $assignment['cl_lecture_name'], 
                                                    'event_date' => $assignment['descrptive_date_time'],
                                                    'event_link' => site_url('materials/course').'/'.$assignment['cl_course_id'].'/'.$assignment['id']
                                                );//$assignment;
            $assignment['type']              = 'assignment';
            $sorted_array[$assignment['id'].'_assignment'] = $assignment['descrptive_date_time'];
        }
        // echo "<pre>";print_r($lectures);exit;
        foreach ($lectures['live'] as $live) {
            $events[]   = array(
                            'id' => $live['id'], 
                            'clt_name' => 'Live',
                            'cl_lecture_type' => '7',
                            'event_name' => $live['cl_lecture_name'], 
                            'event_date' => $live['live_date_time'],
                            'event_link' => (($live['ll_mode'] == 2) ? site_url('materials/course').'/'.$live['cl_course_id'].'/'.$live['id'] : (site_url('/live') . '/join/' . $live['live_id']))
                        );//$live;
            $live['type']              = 'live';
            $sorted_array[$live['id'].'_live'] = $live['live_date_time'];
        }
        if (!empty($lectures['events'])) {
            foreach ($lectures['events'] as $e_key => $event) {
                $lectures['events'][$e_key]['cl_lecture_type'] = 0;
                $events[]                        = array(
                                                        'id' => $event['id'], 
                                                        'clt_name' => 'Event',
                                                        'cl_lecture_type' => '0',
                                                        'event_name' => $event['ev_name'], 
                                                        'event_date' => $event['events_date_time'],
                                                        'event_link' => site_url('events').'/event/'.base64_encode($event['id'])
                                                    );
                // $events[]                                      = $lectures['events'][$e_key];
                $event['type']                                 = 'general';
                $sorted_array[$event['id'].'_event']                = $event['events_date_time'];
            }
        }
        array_multisort($sorted_array, SORT_ASC, $events);
        // echo '<pre>';print_r($custom_events);die;
        return $events;
    }


    public function get_answer($id = 0) {
        $this->load->model('User_model');
        $question_id   = $this->input->post('question_id');
        $response      = array();
        $question_data = $this->User_model->read_question_data(array(
                                 'question_id' => $question_id
        ));
        switch ($question_data['q_type']) {
            case 1:
                $option_array             = explode(',', $question_data['q_options']);
                $question_options         = $this->User_model->read_question_options(array(
                                         'options' => $option_array
                ));
                $question_answer          = $question_data['q_answer'];
                $question_data['options'] = $question_options;
                $question_data['answer']  = $question_answer;
                break;

            case 2:
                $option_array             = explode(',', $question_data['q_options']);
                $question_options         = $this->User_model->read_question_options(array(
                                         'options' => $option_array
                ));
                $question_answer          = explode(',', $question_data['q_answer']);
                $question_data['options'] = $question_options;
                $question_data['answer']  = $question_answer;
                break;

            case 3:
                $option_array             = explode(',', $question_data['q_options']);
                $question_options         = $this->User_model->read_question_options(array(
                                         'options' => $option_array
                ));
                $question_answer          = $question_data['q_answer'];
                $question_data['options'] = '';
                $question_data['answer']  = '';
                break;
        }
        $response['success']       = 1;
        $response['message']       = 'Answer fetching success';
        $response['question_data'] = $question_data;
        echo '<pre>';
        print_r($response);
    }

    /* End Written By Alex. */

    function save_mobile_ajax() {
        $this->load->model('User_model');
        $response                                = array();
        $update                                  = array();
        $session                                 = $this->auth->get_current_user_session('user');
        $mobile_number                           = $this->input->post('number');
        $update['id']                            = $session['id'];
        $update['us_phone']                      = $mobile_number;
        $update['us_phone_verfified']            = '1';
        $update['us_profile_completed']          = '0';
        $updated_session                         = $session;
        $updated_session['us_phone']             = $mobile_number;
        $updated_session['us_phone_verfified']   = '1';
        $updated_session['us_profile_completed'] = '0';
        $update_id                               = $this->User_model->save($update);
        if ($update_id) {
            $this->session->set_userdata('user', $updated_session);
            $response['success'] = true;
            $response['message'] = 'User number updation successfull.';
        } else {
            $response['success'] = false;
            $response['message'] = 'User number updation failed.';
        }

        echo json_encode($response);
    }

    function save_profile_values_step2() {
        //echo "<pre>";print_r($this->input->post('profile_values'));die;
        $this->load->model('Settings_model');
        $user           = $this->auth->get_current_user_session('user');
        $user_id        = $user['id'];
        $profile_values = json_decode($this->input->post('profile_values'));
        $message        = '';
        $error          = false;

        $user_field_values = array();

        $old_value = $this->Settings_model->profile_field_values(array('user_id' => $user_id));
        $old_value = isset($old_value['us_profile_fields']) ? explode('{#}', $old_value['us_profile_fields']) : array();

        if (!empty($old_value)) {
            foreach ($old_value as $field) {
                $field                   = substr($field, 2);
                $field                   = substr($field, 0, -2);
                $temp_field              = explode('{=>}', $field);
                $key                     = isset($temp_field[0]) ? $temp_field[0] : 0;
                $value                   = isset($temp_field[1]) ? $temp_field[1] : '';
                $user_field_values[$key] = $value;
            }
        }


        if (!empty($profile_values)) {
            $profile_block_id = 0;
            foreach ($profile_values as $name => $value) {
                $field_object                           = $this->Settings_model->profile_field(array('field_name' => $name));
                $user_field_values[$field_object['id']] = $value;
            }

            $us_profile_field = array();
            if (!empty($user_field_values)) {
                foreach ($user_field_values as $field_id => $field_value) {
                    $us_profile_field[] = '{{' . $field_id . '{=>}' . $field_value . '}}';
                }
                $us_profile_field = implode('{#}', $us_profile_field);
            }

            $save                         = array();
            $save['id']                   = $user_id;
            $us_profile_field             = (($us_profile_field) ? $us_profile_field : '');
            $save['us_profile_fields']    = $us_profile_field;
            $save['us_profile_completed'] = '1';
            $this->Settings_model->save_profile_field_value($save);

            $user_session                 = array();
            $user['us_profile_completed'] = '1';
            $user['us_profile_fields']    = $us_profile_field;
            $user_session['user']         = $user;
            $this->session->set_userdata($user_session);
        }
        echo json_encode(array('message' => 'Field details saved', 'error' => false));
    }

    // For online test by santhosh

    function result_preview($attempt_id = 0, $token = '') {      
        if($token)
        {
            $verified_data          = $this->api_authentication->verify_token($token);
            if($verified_data['token_verified'] != true)
            {
                redirect('login');
            }
        }
        else
        {
            $redirect = $this->auth->is_logged_in_user(false, false, 'user');
            if (!$redirect) 
            {
                redirect('login');
            }        
        }

        $this->load->model(array('Course_model', 'Dashboard_model'));

        $response        = array();
        if($token)
        {
            $response['student_token']  = $token;
        }
        //$attempt_id                 = 8;//$this->input->post('attempt');
        $attempt_details = $this->Dashboard_model->assessment_attempt_details(array('attempt_id' => $attempt_id));
        if (empty($attempt_details) || $attempt_id==0) {
            redirect(site_url('dashboard'));
        }
        $assesment_details   = $this->Course_model->assesment(array('assessment_id' => $attempt_details['aa_assessment_id'], 'select' => 'assessments.a_has_pass_fail, assessments.a_pass_message, assessments.a_fail_message, assessments.a_pass_percentage'));
        /* echo '<pre>'; 
          print_r($assesment_details);
          print_r($attempt_details);
          die; */
        $response['success'] = true;
        //echo '<pre>';print_r($attempt_details);die;
        $response['manual_evaluation_needed']         = false;
        if ($attempt_details['aa_valuated'] == 0) {
            $response['success'] = false;
            $response['message'] = 'Assessment is not evaluated yet.';
            $response['manual_evaluation_needed']     = true;
        }
        $attempts = $this->Dashboard_model->assessment_attempt_questions(array('attempt_id' => $attempt_id, 'assessment_id' => $attempt_details['aa_assessment_id']));

        $response['test_name']    = $attempt_details['cl_lecture_name'];
        $response['date']         = $attempt_details['aa_attempted_date'];
        $response['write_answer'] = 0;
        $response['wrong_answer'] = 0;
        $response['unattemted']   = 0;
        $response['mark']         = 0;
        

        if ($assesment_details['a_has_pass_fail'] == '1') {
            $response['passed']         = ($assesment_details['a_pass_percentage'] <= (($attempt_details['aa_mark_scored'] / $attempt_details['aa_total_mark']) * 100)) ? true : false;
            $response['result_message'] = ($response['passed']) ? $assesment_details['a_pass_message'] : $assesment_details['a_fail_message'];
        }

        foreach ($attempts as $a_key => $attempt) {
            switch ($attempt['q_type']) {
                case 1:
                    if ($attempt['ar_answer'] == '') {
                        $response['unattemted'] ++;
                    } elseif ($attempt['q_answer'] == $attempt['ar_answer']) {
                        $response['write_answer'] ++;
                        $response['mark'] += $attempt['ar_mark'];
                    } else {
                        $response['wrong_answer'] ++;
                        $response['mark'] += $attempt['ar_mark'];
                    }
                    break;

                case 2:
                    $q_ans    = explode(',', $attempt['q_answer']);
                    $my_ans   = explode(',', $attempt['ar_answer']);
                    $rslt_arr = array_diff($q_ans, $my_ans);
                    if ($attempt['ar_answer'] == '') {
                        $response['unattemted'] ++;
                    } elseif (empty($rslt_arr) && (count($q_ans) == count($my_ans))) {
                        $response['write_answer'] ++;
                        $response['mark'] += $attempt['ar_mark'];
                    } else {
                        $response['wrong_answer'] ++;
                        $response['mark'] += $attempt['ar_mark'];
                    }
                    break;

                case 3:
                if ($attempt['ar_answer'] != "") {
                        //$response['manual_evaluation_needed']         = true;
                        if ($response['mark'] > 0) {
                            $response['write_answer'] ++;
                            $response['mark'] += $attempt['ar_mark'];
                        }
                    } else {
                        $response['unattemted'] ++; 
                    }
                    break;
                
                case 4:
                    //$response['manual_evaluation_needed']         = true;
                    break;
            }
        }

        $response['time'] = $attempt_details['aa_duration'];
        if ($response['wrong_answer'] == 0 && $response['write_answer'] == 0) {
            $response['accuracy']   = 0;
        } else {
            $response['accuracy']   = ($response['write_answer'] / ($response['write_answer'] + $response['wrong_answer'])) * 100;
        }
        $response['accuracy']       = round($response['accuracy'], 2);
        if($response['time']!=0){
            $response['speed']      = (($response['write_answer'] + $response['wrong_answer']) / ($response['time'] / 60)) * 60;
            $response['speed']      = round($response['speed']);
        } else {
            $response['speed']      = 0;
        }
        $response['time']           = $this->secondsToTime($response['time']);
        $response['attempt_id']     = $attempt_id;
        $response['message']        = 'Assessment details successfully fetched.';
        $this->load->view($this->config->item('theme') . '/assesment/test_report', $response);
        //echo json_encode($response);die;
    }

    function secondsToTime($seconds) {
        $t = round($seconds);
        return sprintf('%02d:%02d:%02d', ($t / 3600), ($t / 60 % 60), $t % 60);
    }

    public function plans($lecture_id = '') {
        $user = $this->auth->get_current_user_session('user');

        $this->load->model(array('Dashboard_model', 'User_model'));
        $data          = array();
        $my_plan       = $this->User_model->user_plans(array('user_id' => $user['id'], 'active_plan' => true, 'select' => 'user_plan.id,user_plan.up_user_id,user_plan.up_plan_id'));
        $exclude_plans = isset($my_plan['up_plan_id']) ? $my_plan['up_plan_id'] : '';
        if ($lecture_id == '') {
            $available_plans = $this->Dashboard_model->plan_details(array('exclude' => $exclude_plans));
        } else {
            $lecture_id = base64_decode($lecture_id);

            $lecture_details = $this->Dashboard_model->lecture_details(array('lecture_id' => $lecture_id));

            $available_plans     = $this->Dashboard_model->plan_details(array('plan_ids' => $lecture_details['a_plans'], 'exclude' => $exclude_plans));
            $data['test_detail'] = $lecture_details;
        }
        $data['count']              = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        $data['plans']              = $available_plans;
    }
    
    public function announcement() {

        $this->load->model('Announcement_Model');

        $data                      = array();
        $temp_data                 = array();
        $data['limit']             = empty($this->input->post('limit')) ? $this->__limit : $this->input->post('limit');
        $course_id                 = empty($this->input->post('course_id')) ? FALSE : $this->input->post('course_id');
        $is_ajax                   = $this->input->post('is_ajax');
        $offset                    = empty($this->input->post('offset')) ? 0 : $this->input->post('offset');
        // $count                     = empty($this->input->post('count')) ? 0 : $this->input->post('count');
        $count                     = $this->input->post('count');
        $data['show_load_button']  = false;
        $data['default_user_path'] = default_user_path();
        $data['user_path']         = user_path();
        $user                      = $this->auth->get_current_user_session('user');
        $data['subscriptions']     = $this->Announcement_Model->subscription_details(array('user_id' => $user['id'], 'course_id' => $course_id));
        $data['title']             = lang('my_announcement');
        $groups                    = explode(',',$user['us_groups']);
        $institute                 = $user['us_institute_id'];

        foreach ($data['subscriptions'] as $individual_subscribe) {

            $course_ids[]   = $individual_subscribe['cs_course_id'];
            $subscription[] = $individual_subscribe['cs_subscription_date'];
        }
        
        $announcement_param = array('course_id' => $course_ids, 'subscription_date' => $subscription);
        $total_announcements = $this->Announcement_Model->load_announcements($announcement_param);
    
        foreach($total_announcements as $announcement){

            if($announcement['an_sent_to']=='1'){

                array_push($temp_data,$announcement);
            }else if($announcement['an_sent_to']=='2'){

                $announcement_groups=explode(',',$announcement['an_batch_ids']);

                $status=count(array_intersect($groups,$announcement_groups))?true:false;
            
                if($status==true){
                    array_push($temp_data,$announcement);
                }
            }
            else if($announcement['an_sent_to']=='3'){

                $announcement_institutes=explode(',',$announcement['an_institution_ids']);
                
                if(in_array($institute,$announcement_institutes)){
                    array_push($temp_data,$announcement);
                }
            }
        }
        $announment_full=array_slice($temp_data,$offset,$data['limit']);
        if($count <= 0){
        
            $data['total_records'] = count($temp_data);
        }
        $data['start'] = $offset + $this->__limit;
        $count=empty($count)?$data['total_records']:$count;

        if ($data['start'] < $count) {
            $data['show_load_button'] = true;
        } else {
            $data['show_load_button'] = false;
        }
        $data['announcement']=$announment_full;
        $data['success'] = true;
        echo json_encode($data);
        
        
    }

    function language()
    {
        $response               = array();
        $response['language']   = array();
        $response['language']   = get_instance()->lang->language;
        echo json_encode($response);
    }

    function notifications()
    {
        $response               = array();
        $session                = $this->auth->get_current_user_session('user');
        
        $this->load->library('Notifier');
        
        $response['success']    = true;

        if($session['notification']['count'] > 0)
        {
            $response['message']    = 'Notifications fetch success.';
            $notifications = $this->notifier->fetch(
                array(
                    'user_id' => $session['id']
                )
            );
            $response['notifications'] = $notifications['notifications'];
            $session['notification']['count'] = 0;
            $this->session->set_userdata(array('user' => $session));
        }
        else
        {
            $response['message']    = 'Notifications read success.';
            $notifications = $this->notifier->read(
                array(
                    'user_id' => $session['id']
                )
            );
            $response['notifications'] = $notifications['notifications'];
        }
        // echo "<pre>";print_r($response);exit;
        echo json_encode($response);
    }

    function read_notification()
    {
        $response               = array();
        $session                = $this->auth->get_current_user_session('user');
        
        $this->load->library('Notifier');
        
        $response['success']    = true;
        $response['message']    = 'Notifications marking success.';

        $notification_id        = $this->input->post('notification');

        if(!$notification_id)
        {
            $response['success']    = false;
            $response['message']    = 'Notifications marking failure.';
            echo json_encode($response);die;
        }

        $this->notifier->mark_as_read(
            array(
                'user_id' => $session['id'],
                'notification_id' => $notification_id
            )
        );

        echo json_encode($response);
    }

    public function notification_count()
    {
        $response           = array();
        $session            = $this->auth->get_current_user_session('user');
        $this->load->library('Notifier');
        $notification_count = $this->notifier->get_notifiction_count(array('user_id' => $session['id']));

        $session['notification']['count'] = $notification_count;
        $this->session->set_userdata(array('user' => $session));

        $response['success']    = true;
        $response['message']    = 'Count fetch success.';
        $response['count']      = $notification_count;
        echo json_encode($response);
    }
    public function notify_notification(){

        $response               = array();
        $session                = $this->auth->get_current_user_session('user');
        
        $this->load->library('Notifier');
        
        $response['success']    = true;

        if($session['notification']['count'] > 0)
        {
            $response['message']    = 'Notifications fetch success.';
            $notifications = $this->notifier->fetch(
                array(
                    'user_id' => $session['id']
                )
            );
            $response['notifications'] = $notifications['notifications'];
            $session['notification']['count'] = 0;
            $this->session->set_userdata(array('user' => $session));
        }
        else
        {
            $response['message']    = 'Notifications read success.';
            $notifications = $this->notifier->read(
                array(
                    'user_id' => $session['id']
                )
            );
            $response['notifications'] = $notifications['notifications'];
        }
        
        $this->load->view($this->config->item('theme').'/notification_xs',$response);
    }

}
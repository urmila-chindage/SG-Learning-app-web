<?php
 
 class Register extends CI_Controller {
 
     function __construct()
     {
         parent::__construct();
         $this->lang->load('login');
         $this->actions = $this->config->item('actions');
     }
 
     function index(){ 
         $this->register();
     }
     
     function register()
     {
         $this->auth->is_logged_in_common();
         $this->load->helper('form');
         $this->load->library('form_validation');
         $this->load->library('Newsletter');
         $captcha_validation_verified = true;
 
         //set validation rules
         $this->form_validation->set_rules('firstname', 'First Name', 'trim|required');
         $this->form_validation->set_rules('username', 'Email ID', 'trim|required|valid_email');
         $this->form_validation->set_rules('password', 'Password', 'trim|required');
         $this->form_validation->set_rules('number', 'Number', 'trim|required');
         //validate form input
 
         //Read institutes form memcached.
         $objects        = array();
         $objects['key'] = 'institutes';
         $callback       = 'institutes';
         $institutes     = $this->memcache->get($objects, $callback, array()); 
 
         $objects        = array();
         $objects['key'] = 'branches';
         $callback       = 'branches';
         $branches       = $this->memcache->get($objects, $callback, array()); 
         
         if ($this->form_validation->run() == FALSE)
         {
             $contents = array();
             $contents['institutes'] = $institutes; 
             $contents['branches']   = $branches; 
 
             //$contents['institutes'] = $this->User_model->users(array('role_id' => '8', 'not_deleted' => true, 'status' => '1', 'institute' => true));
             // echo '<pre>'; print_r($contents['institutes']);die;
             $post_data = $this->input->post();
             if(!empty($post_data))
             {
                 foreach($post_data as $key => $value)
                 {
                     $contents[$key] = $value;
                 }
             }
             $this->load->view($this->config->item('theme').'/register', $contents);
         }
         else
         {
             $captcha_access   = $this->settings->setting('has_google_recaptcha');
             
             if(($captcha_access['as_superadmin_value'] && $captcha_access['as_siteadmin_value']) == 1)
             { 
                 foreach( $captcha_access['as_setting_value']['setting_value'] as $key=>$value )
                 {
                     $$key = $value;
                 }
                 $captcha_validation_verified = false;
                 $g_recapcha = $this->input->post('g-recaptcha-response');
                 if(!empty($g_recapcha))
                 {
                     $secret                  = isset($secret_key)?$secret_key:'';
                     $verifyResponse          = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$this->input->post('g-recaptcha-response'));
                     $responseData            = json_decode($verifyResponse);
                     if($responseData->success)
                     {
                         $captcha_validation_verified = true;
                     }
                     else
                     {
                         $data['institutes'] = $institutes;
                         $post_details = $this->input->post();
                         if(!empty($post_details))
                         {
                             foreach($post_details as $key => $value)
                             {
                                 $data[$key] = $value;
                             }
                         }
                         $this->session->set_flashdata('error', lang('robot_failed'));
                         //redirect('/register');
                         $this->load->view($this->config->item('theme').'/register', $data);
                     }
                 }
                 else
                 {
                     $post_details = $this->input->post();
                     $data['institutes'] = $institutes;
                     if(!empty($post_details))
                     {
                         foreach($post_details as $key => $value)
                         {
                             $data[$key] = $value;
                         }
                     }
                     $this->session->set_flashdata('error', lang('click_captcha'));
                     $this->load->view($this->config->item('theme').'/register', $data);
                 }
             } 
             //echo '<pre>'; print_r($this->input->post());die;
             
             
             if($captcha_validation_verified)
             {
                 $this->load->model(array('Authenticate_model'));
                 $this->load->model(array('User_model'));
                 $data                    = array();
                 $data['error']           = array();
                 $email_available         = $this->Authenticate_model->get_user_by_field(array('email' => $this->input->post('username')));
                 $reg_number_available    = $this->Authenticate_model->get_user_by_field(array('phone_number' => $this->input->post('number')));
 
                 if($email_available)
                 {
                     $data['error'][]    = lang('mail_exist');
                 }
                 if($reg_number_available)
                 {
                     $data['error'][]    = 'Phone number already occupied';
                 }
 
                 if(!empty($data['error']))
                 {
                     $data['institutes'] = $institutes; 
                     $data['branches']   = $branches; 
                     $post_data          = $this->input->post();
                     if(!empty($post_data))
                     {
                         foreach($post_data as $key => $value)
                         {
                             $data[$key] = $value;
                         }
                     }
                     $data['error'] = implode('<br />', $data['error']);
                     $this->load->view($this->config->item('theme').'/register', $data);
                 }
                 else
                 {
                     $submitted                  = $this->input->post('submitted');
                     if($submitted)
                     {
                             //insert the user registration details into database
                             $data                      = array();
                             $data['us_name']           = $this->input->post('firstname');
                             $data['us_role_id']        = $this->input->post('register_type');
                             
                             $data['us_institute_id']   = $this->input->post('institute');
                             $objects        = array();
                             $objects['key'] = 'institute_'.$data['us_institute_id'];
                             $callback       = 'institute';
                             $institute      = $this->memcache->get($objects, $callback, array('id' => $data['us_institute_id'] ));                 
                             $data['us_institute_code']   = isset($institute['ib_institute_code'])?$institute['ib_institute_code']:'';
                                 
                             $data['us_branch']         = $this->input->post('branch');
                             $branch_code               = isset($branches[$data['us_branch']]['branch_code'])?$branches[$data['us_branch']]['branch_code']:'';
                             $data['us_branch_code']    = $branch_code;
                             $data['us_email']          = strtolower($this->input->post('username'));
                             $data['us_password']       = sha1($this->input->post('password'));
                             $data['us_phone']          = $this->input->post('number');
                             $data['us_status']         = '1';
                             $data['us_email_exist']    = '1';
                             $data['us_account_id']     = $this->config->item('id');
                             $data['updated_date']      = date('Y-m-d H:i:s');
                             //echo '<pre>'; print_r($data);die;
                             // insert form data into database
                             $new_user_id = $this->Authenticate_model->saveUserData($data);
                            
 
                         if ($new_user_id)
                         {
                             /*Log creation*/
                             $user_data                          = array();
                             $user_data['user_id']               = $new_user_id;
                             $user_data['username']              = $data['us_name'];
                             $user_data['useremail']              = $data['us_email'];
                             $user_data['user_type']             = $data['us_role_id'];
                             $user_data['phone_number']          = $data['us_phone'];
                             $message_template                   = array();
                             $message_template['username']       = $data['us_name'];
                             $triggered_activity                 = 'student_registered';
                             log_activity($triggered_activity, $user_data, $message_template);
                             $this->process_img(array('user_id'=>$new_user_id));
 
                             $email_token        = md5(openssl_random_pseudo_bytes(64));
                             $to_email		    = $this->input->post('username');
                         
                             //Save token for verification 
                             $token                  = array();
                             $token['et_user_id']    = $new_user_id;
                             $token['et_user_email'] = $data['us_email'];
                             $token['et_account_id'] = config_item('id');
                             $token['et_token']      = $email_token;
                             $token['et_status']     = '1';
                             $this->User_model->save_token($token);
                             //End saving token 
 
 
                             $template           = $this->ofabeemailer->template(array('email_code' => 'registration_mail'));
                             $param              = array();
                             $param['to'] 	    = array($to_email);
                             $param['subject'] 	= $template['em_subject'];
                             $contents           = array(
                                                         'user_name' => $data['us_name']
                                                        ,'site_name' => config_item('site_name')
                                                        ,'verification_link' => site_url('register/verify/'.$email_token)
                                                 );
                             $param['subject']   = $this->ofabeemailer->process_mail_content($contents, $template['em_subject']);
                             $param['body']      = $this->ofabeemailer->process_mail_content($contents, $template['em_message']);
                             $send = $this->ofabeemailer->send_mail($param);
                             //echo "<pre>";print_r($send); die;
                             // send email
                             if ($send)
                             {
                                 $template               = $this->ofabeemailer->template(array('email_code' => 'student_registration_notify'));
                                 $param_admin            = array();
                                 $param_admin['to'] 	    = array($this->config->item('site_email'));
                                 $param_admin['subject'] = $template['em_subject'];
                                 $contents               = array(
                                                             'user_name' => $data['us_name']
                                                            ,'site_name' => config_item('site_name')
                                                            ,'user_profile_link' => admin_url('user/profile/'.$new_user_id)
                                                         );
                                 $param_admin['body']    = $this->ofabeemailer->process_mail_content($contents, $template['em_message']);
                                 $send_admin             = $this->ofabeemailer->send_mail($param_admin);
                                 
                                 $this->session->set_flashdata('message', lang('confirm_mail'));
                                 redirect('/register');
                             }
                             else
                             {
                                 $this->session->set_flashdata('error',lang('error_register'));
                                 redirect('/register');
                             }
                         }
                         else
                         {
                             //$this->session->set_flashdata('error',lang('error_register'));
                             //redirect('/register');
                             $data['error']      = lang('error_register');
                             $data['institutes'] = $institutes; 
                             $data['branches']   = $branches; 
                             $post_data          = $this->input->post();
                             if(!empty($post_data))
                             {
                                 foreach($post_data as $key => $value)
                                 {
                                     $data[$key] = $value;
                                 }
                             }
                             $this->load->view($this->config->item('theme').'/register', $data);
                         } 
                     }
                 }
             }
         }
     }
 
     function verify($hash='')
     {
         $this->load->model(array('User_model'));
         $param_admin    = array();
         $verify         = $this->User_model->get_token(array('token'=>$hash));
         $notify_to      = array();
         if(isset($verify['id'])){
             $user_details                       = $this->User_model->user(array('id'=>$verify['et_user_id']));
             $notify_to[config_item('us_id')]    = array($verify['et_user_id']);
             if(!empty($user_details)){
                 $save_user                      = array();
                 $save_user['id']                = $user_details['id'];
                 $save_user['updated_date']      = date('Y-m-d H:i:s');
                 
                 if($verify['et_change_status'] == '1' && $user_details['us_status'] != 1)
                 {
                     $save_user['us_status']                     = '2';
                     // $_SESSION['user']['us_status']      = '2';
                 }
                 $save_user['us_email_verified']                 = '1';
                 if($this->auth->get_current_user_session('user')){
                     $_SESSION['user']['us_email_verified']      = '1';
                 }else{
                     $user_key               = "user_".$save_user['id'];
                     $profile_change_status  = array(
                                            "status" => "1"
                                        );
                     $this->memcache->set($user_key, $profile_change_status);
                 }
                 $this->User_model->save($save_user);
             }
             //Mark token as used 
             $verify['et_status']    = '0';
             $this->User_model->save_token($verify);
             //End mark token
 
             //Token for admin action.
             $this->load->model('Action_model');
             $access_token               = array();
             $access_token['at_token']   = bin2hex(random_bytes(32));
             $access_token['at_purpose'] = '1'; //Profile approval
             $access_token['at_params']  = json_encode(array('user_id' => $user_details['id']));
             $this->Action_model->save($access_token);
             //End of token for admin action.
 
             $this->session->set_flashdata('message','Your email id has been verified. Please login with your credentials.');
             $institute              = $this->User_model->users(array( 'institute_id'=>$user_details['us_institute_id'],'role_id'=>'8','status'=>'1','not_deleted'=>true, 'select' => 'users.id,users.us_email'));
             $to_email		        = $this->config->item('site_email');
             $template               = $this->ofabeemailer->template(array('email_code' => 'student_email_verified_notify'));
             $param_admin['to'] 	    = array($to_email);
             if(!empty($institute)) 
             {
                 foreach($institute as $i_admin)
                 {
                     $param_admin['to'][]    = $i_admin['us_email'];
                     $notify_to[$i_admin['id']] = array($verify['et_user_id']);
                 }
             }
             $param_admin['subject'] = $template['em_subject'];
             $contents               = array(
                                         'user_name' => $user_details['us_name']
                                        ,'site_name' => config_item('site_name')
                                        ,'user_profile_link' => site_url('actions/'.$access_token['at_token'])
                                       );
             $param_admin['body']    = $this->ofabeemailer->process_mail_content($contents, $template['em_message']);
             //echo '<pre>'; print_r($param_admin);die;
             $send_admin = $this->ofabeemailer->send_mail($param_admin);
             
             //Notify to Admin,I admin and priveleged users.
             /*$preveleged_users = $this->accesspermission->previleged_users(array('module' => 'user'));
             foreach($preveleged_users as $preveleged_user)
             {
                 $notify_to[$preveleged_user['id']] = array($verify['et_user_id']);
             }*/
 
             $site_admins_id                 = array();
             $institute_manager_id           = array();
 
             $site_admins                    = $this->User_model->get_user_by_role(array('role_id' => 1));
             foreach($site_admins as $site_admin)
             {
                 $site_admins_id[]           = $site_admin['id'];
             }
 
             $institute_managers             = $this->User_model->get_user_by_role(array('role_id' => 8));
             foreach($institute_managers as $institute_manager)
             {
                 $institute_manager_id[]     = $institute_manager['id'];
             }
             $preveleged_users               = array_merge($site_admins_id, $institute_manager_id);
 
             foreach($preveleged_users as $preveleged_user)
             {
                 $notify_to[$preveleged_user] = array($verify['et_user_id']);
             }
 
             $this->load->library('Notifier');
             $this->notifier->push(
                 array(
                     'action_code' => 'student_registered',
                     'assets' => array('student_name' => $user_details['us_name']),
                     'individual' => false,
                     'push_to' => $notify_to
                 )
             );
             //End notifying.
 
             redirect('/login');
         }else{
             $this->session->set_flashdata('error', lang('email_error_verify'));
             redirect('/login');
         }
     }
 
     function showerror(){
         $main_response = array();
         $main_response[0]['response'] = 'Error subscribing. Please contact admin.';
         $main_response[0]['category'] = 'Error';
         echo json_encode($main_response);
         die;
     }
 
     private function process_img($param)
     {
         $user_id = isset($param['user_id'])?$param['user_id']:0;
         $image_to_cp = ($user_id%11).'.jpg';
         $image_from = FCPATH.badge_upload_path().$image_to_cp;
         $image_to = FCPATH.user_upload_path().$user_id.'.jpg';
         if(copy($image_from,$image_to)){
             $I_data                 = array();
             $I_data['id']           = $user_id;
             $I_data['us_image']     = $user_id.'.jpg'."?v=".rand(10,1000);
             $this->User_model->save($I_data);
 
             $has_s3     = $this->settings->setting('has_s3');
             if( $has_s3['as_superadmin_value'] && $has_s3['as_siteadmin_value'] )
             {
                 $user_profile_path = user_upload_path().$user_id.'.jpg';
                 uploadToS3($user_profile_path, $user_profile_path);
                 unlink($user_profile_path);
             }
 
             return true;
         }else{
             return false;
         }
     }
 
     function vimeo(){
         $this->load->library('Vimeoupload');
         $action     = $_REQUEST['action'];
 
         switch($action){
             case 'upload':
                 $this->vimeoupload->set_config();
                 // $data = $this->vimeoupload->upload(array(
                 //     'path' => '/home/alex/Downloads/dolbycanyon.mkv',
                 //     'Name'  => 'Alex Lib Test',
                 //     'description' => 'Lib test by Alex Abraham.'
                 // ));
                 $videowebpath = 'https://s3-ap-southeast-1.amazonaws.com/sdpk/Alan+Walker+-+Faded+(Live+Performance).mp4';
                 $data = $this->vimeoupload->pull_upload(array('path'=>$videowebpath));
 
                 echo '<pre>';print_r($data);
             break;
             case 'edit':
                 $this->vimeoupload->set_config();
                 $data = $this->vimeoupload->status(array(
                     'uri' => '/videos/291646517'
                 ));
 
                 echo '<pre>';print_r($data);
             break;
             case 'delete':
                 $this->vimeoupload->set_config();
                 $data = $this->vimeoupload->delete(array(
                     'uri' => '/videos/291496128'
                 ));
 
                 echo '<pre>';print_r($data);
             break;
             case 'replace':
                 $this->vimeoupload->set_config();
                 $data = $this->vimeoupload->pull_replace(array(
                     'uri' => '/videos/291881078',
                     'video' => 'https://s3-ap-southeast-1.amazonaws.com/sdpk/Alan+Walker+-+Faded+(Live+Performance).mp4'
                 ));
 
                 echo '<pre>';print_r($data);
             break;
         }
     }
     function sendVerificationMail()
     {
         $this->load->model(array('User_model'));
         $user_data                          = array();
         $data                               = $_SESSION['user'];
         $user_data['user_id']               = $data['id'];
         $user_data['username']              = $data['us_name'];
         $user_data['user_type']             = $data['us_role_id'];
         $user_data['register_number']       = $data['us_register_number'];
         $user_data['useremail']             = $data['us_email'];
         $message_template                   = array();
         $message_template['username']       = $data['us_name'];
         $triggered_activity                 = 'student_registered';
         log_activity($triggered_activity, $user_data, $message_template);
         // $this->process_img(array('user_id'=>$user_data['user_id']));
 
         $email_token            = md5(openssl_random_pseudo_bytes(64));
         $to_email		        = $data['us_email'];
     
         //Save token for verification 
         $token                  = array();
         $token['et_user_id']    = $user_data['user_id'];
         $token['et_user_email'] = $data['us_email'];
         $token['et_account_id'] = config_item('id');
         $token['et_token']      = $email_token;
         $token['et_status']     = '1';
         $this->User_model->save_token($token);
         //End saving token 
         
         $template           = $this->ofabeemailer->template(array('email_code' => 'registration_mail'));
         $param              = array();
         $param['to'] 	    = array($to_email);
         $param['subject'] 	= $template['em_subject'];
         $contents           = array(
                                     'user_name' => $data['us_name']
                                    ,'site_name' => config_item('site_name')
                                    ,'verification_link' => site_url('register/verify/'.$email_token)
                                 );
         $this->load->model(array('User_model'));
         $param['body']      = $this->ofabeemailer->process_mail_content($contents, $template['em_message']);
         $param['subject']   = $this->ofabeemailer->process_mail_content($contents, $template['em_subject']);
         
         $send               = $this->ofabeemailer->send_mail($param);
         echo $contents['verification_link'];
     }
 
 }
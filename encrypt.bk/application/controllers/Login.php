<?php

class Login extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        $this->lang->load('login');
    }

    function index(){
        $this->login();
    }

    function login()
    {
        //echo session_id(); die;
        $this->auth->is_logged_in_common();
        //echo $this->session->flashdata('session_expired'); die;
        $this->load->helper('form');
        $this->load->library('user_agent');
        $refer =  $this->agent->referrer();
           
        $data['redirect']           = ($this->session->flashdata('redirect'))? $this->session->flashdata('redirect'): ( $refer ? $refer : "") ;
        if (strpos($data['redirect'], 'password_set') !== false && strpos($data['redirect'], 'dashboard') !== false) 
        {
            $data['redirect'] = '';
        }
        
        $submitted                  = $this->input->post('submitted');

        if ($submitted)
        {
            $username           = $this->input->post('username');
            $password           = $this->input->post('password');
            $remember           = true; //$this->input->post('remember');;
            $redirect           = $this->input->post('redirect');
            $login		        = $this->auth->login_admin($username, $password, $remember);
            if(!empty($login) && isset($login['us_deleted']) && $login['us_deleted'] == '0')
            { 
                switch($login['us_status'])
                {
                    case 1: 

                        $this->load->model('Authenticate_model');
                        $user_data                              = array();
                        $user_data['id']                        = $login['id'];
                        $user_data['us_session_id']             = session_id();
                        $this->Authenticate_model->save_fb_data($user_data);
                        $user_memcache_index                    = 'user_session_id_' . $login['id'];
                        $this->memcache->delete( $user_memcache_index );
                        $params                                 = array('us_session_id' => $user_data['us_session_id']);
                        $this->memcache->set($user_memcache_index, $params);

                        if ($redirect == '')
                        {
                            if($login['rl_type'] == '1'){
                                $redirect                       = redirect(admin_url('dashboard'));
                                exit;
                            }
                            else 
                            {
                                $redirect                       = site_url('/dashboard');
                            }
                        }
                        else
                        {
                            $redirect_obj                       = parse_url($redirect); 
                            $server_obj                         = parse_url(current_url());
                            if(isset($redirect_obj['host']) && isset($server_obj['host']) && $redirect_obj['host'] != $server_obj['host'])
                            {
                                $redirect                       = site_url('login');
                            }        
                        }

                        /*Log creation*/
                        $user_data                          = array();
                        $user_data['user_id']               = $login['id'];
                        $user_data['username']              = $login['us_name'];
                        $user_data['useremail']              = $login['us_email'];
                        $user_data['user_type']             = $login['us_role_id'];
                        $user_data['phone_number']          = $login['us_phone'];
                        $message_template                   = array();
                        $message_template['username']       = $login['us_name'];
                        $triggered_activity                 = 'user_login';
                        log_activity($triggered_activity, $user_data, $message_template);
                        
                        redirect($redirect);
                    break;
                    case 0: 
                        $this->session->set_flashdata('error', 'Your account is blocked. Contact admin for more details.');
                        redirect(site_url('/login'));
                    break;
                    case 2: 
                        $this->session->set_flashdata('error', 'Your account awaiting for admin aproval.');
                        redirect(site_url('/login'));
                    break;
                    case 3: 
                        $this->session->set_flashdata('error', 'Your account is not activated, Activate your account by clicking on the activation link in your email.');
                        redirect(site_url('/login'));
                    break;
                }
            }
            else
            {
                //this adds the redirect back to flash data if they provide an incorrect credentials
                $this->session->set_flashdata('redirect', $redirect);
                $this->session->set_flashdata('error', lang('error_authentication_failed'));
                redirect(site_url('/login'));
            }
        }
        
        $this->load->view($this->config->item('theme').'/login', $data);
    }

    //Forget password getting and encode the mailid and id
    public function forgot()
    {
        if($this->auth->is_logged_in_common()){
            redirect(site_url('login'));
        }
        $this->load->library('form_validation');
        $this->form_validation->set_rules('username', 'Email Address', 'required|valid_email');

        if ($this->form_validation->run() == FALSE)
        {
            $this->load->view($this->config->item('theme').'/forgot_password');
        }

        else
        {
            $this->load->model(array('Authenticate_model'));
            $this->load->model(array('User_model'));
            $data  	= $this->Authenticate_model->get_exist_email($this->input->post('username'));
            if(empty($data))
            {
                $this->session->set_flashdata('error', 'Requested account details not found.');
                redirect(site_url('login/forgot')); 
            }
            else
            {
                $id    	= $data['id'];
                $mail  	= $data['us_email'];
                $code       = md5(openssl_random_pseudo_bytes(64));
                
                //Save token for verification 
                $token                  = array();
                $token['et_user_id']    = $data['id'];
                $token['et_user_email'] = $data['us_email'];
                $token['et_account_id'] = config_item('id');
                $token['et_token']      = $code;
                $token['et_status']     = '1';
                $this->User_model->save_token($token);
                //End saving token
    
                //shooting mail
                $template           = $this->ofabeemailer->template(array('email_code' => 'forgot_password'));
                $param['to'] 	    = array($mail);
                $param['subject'] 	= $template['em_subject'];
                $contents           = array(
                                            'user_name' => $data['us_name']
                                            ,'site_name' => config_item('site_name')
                                            ,'reset_link' => site_url('login/password_update/'.$code)
                                        );
                $param['body']      = $this->ofabeemailer->process_mail_content($contents, $template['em_message']);
                $send               = $this->ofabeemailer->send_mail($param);
                //end
    
                //echo "<pre>"; print_r($send); die;
                if ($send)
                {
                    //print_r('send');die;
                    $this->session->set_flashdata('message', lang('mail_check'));
                    redirect(site_url('login/forgot')); 
                }
            }
        }
    }

    //redirecting function
    public function password_update_beta()
    {
        $this->load->helper('form');
        
        $code               = $this->uri->segment(3);
        $data['code']       = $code;
        
        $this->load->view($this->config->item('theme').'/set_password', $data);
    }
    
    public function password_update()
    {
        if($this->auth->is_logged_in_common())
        {
            redirect(site_url('login'));
        }

        $this->load->helper('form');
        
        $code               = $this->uri->segment(3);
        $data['token']      = $code;
        $this->load->view($this->config->item('theme').'/set_password_beta', $data);
    }

    /* new password updating the database and send mail to the user to inform the password reset successfully */
    public function password_set()
    {
        $this->load->model(array('Authenticate_model'));
        $this->load->model(array('User_model'));
        $password    	= $this->input->post('new_password');
        $cpassword 	  	= $this->input->post('confirm_new_password');
        $token     	  	= $this->input->post('token');

        if($password != $cpassword){
            $this->session->set_flashdata('error','Password doesnot match.');
            redirect(site_url('/login/password_update/'.$token));
        }

        if(strlen($password) < 6){
            $this->session->set_flashdata('error','Password is too short.');
            redirect(site_url('/login/password_update/'.$token));
        }

        if(strlen($password) > 15){
            $this->session->set_flashdata('error','Password is too large.');
            redirect(site_url('/login/password_update/'.$token));
        }

        $verify         = $this->User_model->get_token(array('token'=>$token));
        //echo '<pre>'; print_r($verify);die;

        if(!empty($verify)){
            $user_details   = $this->User_model->user(array('id'=>$verify['et_user_id']));
            // $user_type                          = $this->auth->is_logged_in_common($redirect=false);
            // $user                               = $this->auth->get_current_user_session($user_type);
            /*Log creation*/
            $user_data                          = array();
            $user_data['user_id']               = $user_details['id'];
            $user_data['username']              = $user_details['us_name'];
            $user_data['useremail']              = $user_details['us_email'];
            $user_data['user_type']             = $user_details['us_role_id'];
            $user_data['phone_number']          = $user_details['us_phone'];
            $message_template                   = array();
            $message_template['username']       = $user_details['us_name'];
            $triggered_activity                 = 'forgot_password';
            log_activity($triggered_activity, $user_data, $message_template);
            
            $query = $this->Authenticate_model->verify_user_data($user_details['us_email'],sha1($password),$user_details['id']);


            $param_admin            = array();
            $to_email		        = $verify['et_user_email'];
            $template               = $this->ofabeemailer->template(array('email_code' => 'student_password_update_notify'));
            $param_admin['to'] 	    = array($to_email);
            $param_admin['subject'] = $template['em_subject'];
            $contents               = array(
                                        'user_name' => $user_details['us_name']
                                       ,'site_name' => config_item('site_name')
                                       ,'site_link' => site_url('login')
                                      );
            $param_admin['body']    = $this->ofabeemailer->process_mail_content($contents, $template['em_message']);
            $send_admin = $this->ofabeemailer->send_mail($param_admin);



            //Mark token as used 
            $verify['et_status']    = '0';
            $this->User_model->save_token($verify);
            //End mark token

            if($send_admin){
               
                $this->session->set_flashdata('message', 'Your password has been reset successfully');
                redirect(site_url('/login'));
            }

        }else{
            $this->session->set_flashdata('error',"Authorization failed.");
            redirect(site_url('/login'));
        }
    }

    public function facebook()
    {
        $this->load->model(array('Authenticate_model'));
        $data           = $this->Authenticate_model->get_exist_email($this->input->post('email'));
        $save           = array();
        if( !empty($data))
        {
            $save['id']         = $data['id'];
            if($data['us_role_id'] != '2')
            {
                return false;
            }
        }
        else
        {
            $save['us_email']   = $this->input->post('email');
        }
        $save['us_name']        = $this->input->post('name');
        $save['us_account_id']  = $this->config->item('id');
        $save['us_status']      = '1';
        $save['us_role_id']     = '2';
        $query                  = $this->Authenticate_model->save_fb_data($save);

        $this->db->select('*');
        $this->db->from('users');
        $this->db->where('us_email', $this->input->post('email'));
        $this->db->where('us_account_id', $this->config->item('id'));
        $this->db->limit(1);
        $result     = $this->db->get();
        //echo $this->db->last_query();die;
        $result     = $result->row_array();

        if (sizeof($result) > 0)
        {
            $user           = array();
            $user['user']   = array();
            foreach( $result as $key => $value )
            {
                $user['user'][$key]	= $value;
            }
            unset($user['user']['us_password']);
            $this->session->set_userdata($user);
            return true;
        }
        else
        {
            return false;
        }
    }

    public function invite_friends(){
    	$emails = array();
    	$result_emails = array();
        $resultt_emails = array();
    	$this->load->model('User_model');
    	$emails = $this->input->post('email_id');
        $user = $this->auth->get_current_user_session('user');
    	$emails = json_decode($emails);
    	$email_arr = array();
    	foreach ($emails as $key => $value) {
    		if($user['us_email'] != $value){
                $result_emails[] = $value;
            } 
    	}
    	//$emails = array_values($emails);
    	$present_emails = $this->User_model->get_registered(array('emails'=>$result_emails));
        //echo json_encode($present_emails);die;
    	foreach ($present_emails as $key => $value) {
    		$resultt_emails[] = $value['us_email'];
    	}
    	$email_arr = array_diff($result_emails,$resultt_emails);
    	//print_r($email_arr);die;
    	$response = array();
    	//print_r($emails);
    	//echo $emails;
    	$redirect	  = $this->auth->is_logged_in_user(false, false);
    	if(!$redirect){
    		$response['success'] = false;
    		$response['message'] = 'You need to login to invite.';
    	}else if(empty($email_arr)){
    		$response['success'] = false;
    		$response['message'] = 'List is empty or given email id\'s are already registered.';
            $response['not_send'] = $present_emails;
            $response['send_count'] = count($email_arr);
    	}else{
            $response['success'] = true;
    		$response = $this->newsletter->invite($email_arr,$user);
            $response['not_send'] = $present_emails;
            $response['send_count'] = count($email_arr);
    	}

    	echo json_encode($response);

    }

    function redirect()
    {
        $this->session->set_flashdata('error', $this->session->flashdata('error'));
        echo '<script>location.href="'.site_url('login').'"</script>';
    }
}
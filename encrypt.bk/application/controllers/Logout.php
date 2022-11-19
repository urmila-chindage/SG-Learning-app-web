<?php

class Logout extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        $this->lang->load('login');
    }
	
    function index(){
        $user_type                          = $this->auth->is_logged_in_common($redirect=false);
        $user                               = $this->auth->get_current_user_session($user_type);
        if($user) 
        {
            /*Log creation*/
            $user_data                          = array();
            $user_data['user_id']               = $user['id'];
            $user_data['username']              = $user['us_name'];
            $user_data['useremail']              = $user['us_email'];
            $user_data['user_type']             = $user['us_role_id'];
            $user_data['phone_number']          = $user['us_phone'];
            $message_template                   = array();
            $message_template['username']       = $user['us_name'];
            $triggered_activity                 = 'user_logout';
            log_activity($triggered_activity, $user_data, $message_template);
        }
        $this->auth->logout('user');
        $this->session->unset_userdata('hide_live');
        //when someone logs out, automatically redirect them to the login page.
        $this->session->set_flashdata('message', lang('message_logged_out'));
        redirect('/login');
    }

}

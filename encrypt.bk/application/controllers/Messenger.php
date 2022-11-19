<?php
// from users, not in course 3.

class Messenger extends CI_Controller 
{
    function __construct()
    {
        parent::__construct();
    }

    function student()
    {
        $student  = $this->auth->get_current_user_session('user');
        if(empty($student))
        {
            redirect(site_url('login'));
        }
        $this->load->view($this->config->item('theme').'/chatbox_content_student');
    }
    function admin()
    {
        $admin  = $this->auth->get_current_user_session('admin');
        if(empty($admin))
        {
            redirect(site_url('login'));
        }
        $this->load->view($this->config->item('admin_folder').'/chatbox_content_admin');
    }

    function language()
    {
        $response               = array();
        $response['language']   = array();
        $response['language']   = get_instance()->lang->language;
        echo json_encode($response);
    }
}
?>
<?php
class Sales extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Kolkata');
        $this->__role_query_filter = array();
        $redirect               = $this->auth->is_logged_in(false, false);
        $this->__admin_index    = 'admin';
        $this->__loggedInUser   = $this->auth->get_current_user_session('admin');
    }
    
    function index()
    {
        $this->load->view($this->config->item('admin_folder').'/sales');
    }
     function teachers()
    {
        $this->load->view($this->config->item('admin_folder').'/teachers-percentage');
    }
}
    

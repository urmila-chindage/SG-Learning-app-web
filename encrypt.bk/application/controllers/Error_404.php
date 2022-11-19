<?php
class Error_404 extends CI_Controller
{
    public function __construct() 
        {
           parent::__construct(); 
        } 
        public function index() 
        { 
           $this->output->set_status_header('404'); 
           if($this->auth->is_logged_in_common(false) == 'admin')
           {
               $this->load->view($this->config->item('admin_folder').'/404_error.php');               
           }
           else
           {
               $this->load->view($this->config->item('theme').'/404_error.php');                              
           }
        } 
}
?>
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cms extends CI_Controller {

	function __construct()
    {
        parent::__construct();
        $redirect   = $this->auth->is_logged_in(false, false);
        if (!$redirect)
        {
            redirect('login');
        }
        $this->load->model(array('User_model', 'Course_model'));
        $this->lang->load('dashboard');
    }

    public function index()
    {
        $data                       = array();
        $breadcrumb                 = array();
        $breadcrumb[]               = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]               = array( 'label' => 'CMS', 'link' => admin_url('cms'), 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']         = $breadcrumb;
        $data['title']              = "CMS";

        $this->load->view($this->config->item('admin_folder').'/cms', $data);
    }

}
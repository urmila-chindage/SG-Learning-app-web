<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Survey extends CI_Controller 
{
    public $actions;
    function __construct()
    {
        parent::__construct();
        // $redirect   = $this->auth->is_logged_in_user(false, false);
        // if (!$redirect)
        // {
        //         redirect('login');
        // }
		$this->load->model(array('Page_model','Category_model'));
        $this->actions = config_item('actions');
    }
    function index()
    {
        $data                 = array();
        $session              = $this->auth->get_current_user_session('user');
		$data['survey_data']  = $this->Page_model->get_survey_data();
        $this->load->view($this->config->item('theme').'/survey_form', $data);
    }
}
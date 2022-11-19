<?php
class Log_action extends CI_Controller
{

    function __construct()
    {
        
        parent::__construct();
        $this->__role_query_filter = array();
        $redirect	= $this->auth->is_logged_in(false, false);
        $this->__admin_index = '';
        $this->__loggedInUser      = $this->auth->get_current_user_session('admin');
        
        if(!$redirect)
        {
            redirect('login');
        }
        $this->load->model(array('Course_model', 'Category_model', 'Tutor_model','Log_model'));
        $this->load->helper('form');
        $this->load->library(array('form_validation'));
      
     }
     function index(){
        redirect(admin_url('log_action/log_points'));
     }
     function log_points(){

        $data                = array();
        $breadcrumb          = array();
        $breadcrumb[]        = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]        = array( 'label' => 'Log Action Points', 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']  = $breadcrumb;

        $params                 = array();
        $params['select']       = 'id,la_action_name,la_points';
        $data['log_actions']    = $this->Log_model->log_action_points($params);
        $this->load->view('admin/log_points',$data);
     }
     function save_points(){

        $id     = $this->input->post('id');
        $point  = $this->input->post('point');
        
        $filter_param = array();
        $save_param   = array();
        $filter_param['id']         = $id;
        $save_param['la_points']    = $point;
        $result = $this->Log_model->change_points($save_param,$filter_param);
        if($result == true){
            $response       = array();
            $response['error'] = false;
            $response['message'] = 'Updated Successfully'; 
        }else{
            $response       = array();
            $response['error'] = true;
            $response['message'] = 'Not Updated'; 
        }
        echo json_encode($response);
     }
}
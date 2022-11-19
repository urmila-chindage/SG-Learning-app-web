<?php
class Grade extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Kolkata');
        $this->__role_query_filter = array();
        $this->__admin_index    = 'admin';
        $this->__loggedInUser   = $this->auth->get_current_user_session('admin');
        $this->__eventLimit     = 10;
        if (!$this->__loggedInUser)
        {
            redirect('login');
        }
        $this->actions = $this->config->item('actions');
        $this->load->model(array('Grade_model'));
    }
    
    function index()
    {
        $data                       = array();
        $breadcrumb                 = array();
        $breadcrumb[]               = array( 'label' => 'Home', 'link' => site_url('/admin'), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]               = array( 'label' => 'Grade', 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']         = $breadcrumb;
        $data['title']              = 'Grade';
        $data['grades']             = array();
        $grades                     = $this->Grade_model->getGrades();
        $data['grades']             = $grades;
        $data['actions']            = $this->actions;
        //echo '<pre>'; print_r($data['recently_viewed']);die;
        $this->load->view($this->config->item('admin_folder').'/grades', $data);
    }

    function basic($id = false){
        if(!$id){
            redirect(admin_url('grade'));
        }

        $grade_id                   = base64_decode($id);
        $data                       = array();
        $breadcrumb                 = array();
        $grade_data                 = $this->Grade_model->adminGrade(array('id'=>$grade_id));
        $breadcrumb[]               = array( 'label' => 'Home', 'link' => site_url('/admin'), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]               = array( 'label' => 'Grades', 'link' => admin_url('grade'), 'active' => '', 'icon' => '' );
        $breadcrumb[]               = array( 'label' => $grade_data['gr_name'], 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']         = $breadcrumb;
        $data['grade']              = $grade_data;
        $data['title']  = 'Grade - '.$grade_data['gr_name'];
        //echo "<pre>";print_r($event_data);die;
        $this->load->view($this->config->item('admin_folder').'/grade', $data);
    }


    function submit_grade(){
        $grade_id           = $this->input->post('grade_id');
        $gr_name            = $this->input->post('gr_name');
        $gr_range_from      = $this->input->post('gr_range_from');
        $gr_range_to        = $this->input->post('gr_range_to');

        $grade_update       = array();
        if($grade_id =='' ||$gr_name ==''||$gr_range_from ==''||$gr_range_to ==''){
            redirect(admin_url('grade'));
        }

        $grade_update['gr_name']            = $gr_name;
        $grade_update['gr_range_from']      = $gr_range_from;
        $grade_update['gr_range_to']        = $gr_range_to;
        $grade_update['action']             = '2';
        $grade_update['action_by']          = $this->__loggedInUser['id'];
        $grade_update['updated']            = date('Y-m-d H:i:s');

        $this->Grade_model->addGrade(array('id'=>base64_decode($grade_id),'values'=>$grade_update));

        redirect(admin_url('grade/basic/').$grade_id);
    }
}
    
?>
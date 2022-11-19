<?php
class Email_template extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Kolkata');
        $this->__role_query_filter = array();
        $redirect   = $this->auth->is_logged_in(false, false);
        $this->__admin_index    = 'admin';
        $this->__loggedInUser   = $this->auth->get_current_user_session('admin');
        if (!$redirect)
        {
                redirect('login');
        }
        $this->actions = $this->config->item('actions');
        $this->load->model(array('Email_template_model'));
    }
    
    function index()
    {
        $data                       = array();
        $breadcrumb                 = array();
        $breadcrumb[]               = array( 'label' => 'Home', 'link' => site_url('/admin'), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]               = array( 'label' => 'Email Templates', 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']         = $breadcrumb;
        $data['title']              = 'Email Templates';
        $data['actions']            = $this->actions;
        $this->load->view($this->config->item('admin_folder').'/email_templates', $data);
        
    }
    public function get_template()
    {
        $keyword                    = $this->input->post('keyword');
        $is_ajax                    = $this->input->post('is_ajax');
        $data['email_templates']    = array();
        $data['success']            = false;
        $email_param                = array();
        if(isset($keyword))
        {
            $email_param['keyword'] = $keyword;
        }
        
        $email_templates            = $this->Email_template_model->getEmailtemplates($email_param);
        if(!empty($email_templates))
        {
            $data['email_templates']    = $email_templates;
            $data['success']            = true;
        }
        echo json_encode($data);exit;
    }

    function basic($id = false){
        if(!$id){
            redirect(admin_url('email_template'));
        }

        $event_id                   = base64_decode($id);
        $data                       = array();
        $breadcrumb                 = array();
        $email_data                 = $this->Email_template_model->adminEmail(array('id'=>$event_id));
        $breadcrumb[]               = array( 'label' => 'Home', 'link' => site_url('/admin'), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]               = array( 'label' => 'Email Templates', 'link' => admin_url('email_template'), 'active' => '', 'icon' => '' );
        $breadcrumb[]               = array( 'label' => $email_data['em_name'], 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']         = $breadcrumb;
        $data['email_template']              = $email_data;
        $data['title']              = 'Event - '.$email_data['em_name'];
        //echo "<pre>";print_r($event_data);die;
        $this->load->view($this->config->item('admin_folder').'/email_template', $data);
    }

    function submit_email_template(){
        $email_id           = $this->input->post('email_id');
        $email_name         = $this->input->post('em_name');
        $email_subject      = $this->input->post('em_subject');
        $email_message      = $this->input->post('em_message');
        $email_update       = array();
        
        if($email_id =='' ||$email_name ==''||$email_subject ==''||trim(strip_tags($email_message)) ==''){
            redirect(admin_url('email_template'));
        }
        $email_update['em_name']            = $email_name;
        $email_update['em_message']         = $email_message;
        $email_update['em_subject']         = $email_subject;
        $email_update['action']             = '2';
        $email_update['action_by']          = $this->__loggedInUser['id'];
        $email_update['updated']            = date('Y-m-d H:i:s');

        $this->Email_template_model->addEmail(array('id'=>base64_decode($email_id),'values'=>$email_update));
        $email_data                 = $this->Email_template_model->adminEmail(array('id'=>base64_decode($email_id)));
        $this->memcache->delete('email_template_'.$email_data['em_code']);
        $this->memcache->delete('email_template_'.base64_decode($email_id));
        $user_data              = array();
        $user_data['user_id']   = $this->__loggedInUser['id'];
        $user_data['username']  = $this->__loggedInUser['us_name'];
        $user_data['useremail']  = $this->__loggedInUser['us_email'];
        $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
        
        $message_template                       = array();
        $message_template['username']           = $this->__loggedInUser['us_name'];
        $message_template['template_name']      = $this->input->post('em_name');
        
        $triggered_activity     = 'email_template_updated';
        log_activity($triggered_activity, $user_data, $message_template); 
        redirect(admin_url('email_template'));
    }

}
    
?>
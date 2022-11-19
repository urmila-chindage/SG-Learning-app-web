<?php
class Survey extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $redirect	= $this->auth->is_logged_in(false, false);
        if (!$redirect)
        {
            $redirect   = true;
            $content_editor    = $this->auth->is_logged_in(false, false, 'content_editor');
            if($content_editor)
            {
                $redirect = false;
            }
            if($redirect)
            {
                redirect('login');
            }
        }
        $this->actions = $this->config->item('actions');
        $this->load->model(array('Page_model'));
        $this->lang->load('survey');
    }
    
    function index()
    {
        $data                       = array();
        $breadcrumb                 = array();
        $breadcrumb[]               = array( 'label' => 'Home', 'link' => site_url('/admin'), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]               = array( 'label' => 'CMS', 'link' => admin_url('page'), 'active' => 'active', 'icon' => '' );
        $breadcrumb[]               = array( 'label' => lang('manage_survey'), 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']         = $breadcrumb;
        $data['title']              = lang('survey');
        $data['survey']             = $this->Page_model->get_survey_data();
        $this->load->view($this->config->item('admin_folder').'/survey', $data);
    }

    function language()
    {
        $response               = array();
        $response['language']   = array();
        $response['language']   = get_instance()->lang->language;
        echo json_encode($response);
    }

    function enable_survey()
    {
        $response       = array();         
        $survey_title   = $this->input->post('survey_title');
        $s_description  = $this->input->post('s_description');
        $start_date     = $this->input->post('start_date');
        $end_date       = $this->input->post('end_date');
        $s_content      = $this->input->post('s_content');

        $save                   = array();
        $save['s_title']        = $survey_title;
        $save['s_description']  = $s_description; 
        $save['s_start_date']   = $start_date;
        $save['s_end_date']     = $end_date;
        $save['s_html']         = $s_content;
        $save['s_account_id']   = $this->config->item('id');
        $save_count             = $this->Page_model->get_survey();
        if($save_count>0)
        {
            if($this->Page_model->update_survey($save))
            {
                $response['error']      = false;
                $response['message']    = lang('survey_updated_success');
            }
        }
        else
        {
            if($this->Page_model->save_survey($save))
            {
                $response['error']      = false;
                $response['message']    = lang('survey_created_success');
            }
        }
        echo json_encode($response);
    }
}
    
?>
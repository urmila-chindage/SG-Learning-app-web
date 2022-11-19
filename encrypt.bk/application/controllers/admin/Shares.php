<?php
class Shares extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Kolkata');
        $redirect   = $this->auth->is_logged_in(false, false, 'teacher');
        if (!$redirect)
        {
            redirect('login');
        }
        $this->__loggedInUser   = $this->auth->get_current_user_session('teacher');
        $this->load->model(array('Finance_model'));        
    }
    
    function index()
    {
        $this->monthly_teachers_shares();
    }
    
    function monthly_teachers_shares()
    {
        $data                       = array();
        $breadcrumb                 = array();
        $breadcrumb[]               = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]               = array( 'label' => 'My Shares', 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']         = $breadcrumb;
        $data['title']              = 'Teacher Shares';
        
        $offset_recieved    = 0;
        $param              = array();
        $param['period']    = date('Y-m');
        
        //calculating page numbers
        $per_page           = 20;
        $data['per_page']   = $per_page;
        $page_num           = 1;
        $offset             = 0;
        $param['offset'] = $offset;
        $param['limit']  = $per_page;
        $param['teacher_id']  = $this->__loggedInUser['id'];
        //end of calucalting page number
        
        $data['monthly_teachers_shares'] = $this->Finance_model->monthly_teachers_shares($param);
        $this->load->view($this->config->item('admin_folder').'/monthly_teachers_shares', $data);
    }
    
    function monthly_teachers_shares_json()
    {
        $period             = $this->input->post('period');
        $keyword            = $this->input->post('keyword');
        $offset_recieved    = $this->input->post('offset');

        $response                             = array();  
        $response['monthly_teachers_shares']  = array();
        $response['error']  = false;
        $param              = array();
        $param['keyword']   = $keyword;
        $param['period']    = $period;
        
        //calculating page numbers
        $per_page           = 20;
        $page_num           = 1;
        $offset             = 0;
        if(  $offset_recieved && $offset_recieved != 1 )
        {
            $page_num     = $offset_recieved;
            $offset       = $offset_recieved * $per_page;
            $offset       = ($offset - $per_page);
        }
        $param['offset'] = $offset;
        $param['limit']  = $per_page;
        $param['teacher_id']  = $this->__loggedInUser['id'];
        //end of calucalting page number
        $response['monthly_teachers_shares'] = $this->Finance_model->monthly_teachers_shares($param);
        echo json_encode($response);
    }
    
    function monthly_teachers_shares_details_json()
    {
        $data       = array();
        $teacher_id = $this->input->post('teacher_id');
        $period     = $this->input->post('period');
        //$teacher_id = 12;
        //$period     = '2017-03';
        $data['monthly_teachers_shares_details'] = array();
        $data['monthly_teachers_shares_details'] = $this->Finance_model->monthly_teachers_shares_details(array('teacher_id'=> $teacher_id, 'period'=> $period));
        //echo '<pre>'; print_r($data);die;
        echo json_encode($data);
    }
    
    function export_monthly_teacher_shares($params=false)
    {
        if(!$params)
        {
            redirect(admin_url('finance'));
        }
        $params      = base64_decode($params);
        $params      = explode('#', $params);
        
        $period     = isset($params[0])?$params[0]:date('Y-m');
        $keyword    = isset($params[1])?$params[1]:false;
        
        $param              = array();
        $data               = array();
        $data['period']     = $period;
        $param['period']    = $period;
        $param['keyword']   = $keyword;
        $param['teacher_id']  = $this->__loggedInUser['id'];
        
        $monthly_shares  = $this->Finance_model->monthly_teachers_shares($param);
        if(!empty($monthly_shares))
        {
            foreach ($monthly_shares as $monthly_share)
            {
                $monthly_share['details'] = $this->Finance_model->monthly_teachers_shares_details(array('teacher_id'=> $monthly_share['ps_teacher_id'], 'period'=> $period));
                $data['monthly_shares'][] = $monthly_share;  
            }
        }
        //echo '<pre>'; print_r($data);die;
        $this->load->view($this->config->item('admin_folder').'/monthly_shares_export', $data);
    }
    function language()
    {
        $response               = array();
        $response['language']   = array();
        $response['language']   = get_instance()->lang->language;
        echo json_encode($response);
    }
    
    function payment_share_details()
    {
        $data           = array();
        $share_id       = $this->input->post('share_id');
        $data['share'] = array();
        $data['share'] = $this->Finance_model->monthly_teachers_shares_details(array('share_id'=> $share_id));
        //echo '<pre>'; print_r($data);die;
        echo json_encode($data);        
    }
    function pdf_share($share_id=false)
    {
        if(!$share_id)
        {
            redirect(admin_url('dashboard'));
        }
        $data          = array();
        $data['share'] = array();
        $data['share'] = $this->Finance_model->monthly_teachers_shares_details(array('share_id'=> $share_id, 'teacher_id' => $this->__loggedInUser['id']));
        if(!$data['share'])
        {
            redirect(admin_url('shares'));
        }
        $this->load->view($this->config->item('admin_folder').'/invoice_share_export', $data);
    }

}

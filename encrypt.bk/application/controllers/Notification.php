<?php
class Notification extends CI_Controller {
    function __construct()
    {
        parent::__construct();
        $this->load->helper("url");
        $this->load->library("pagination");
        $this->lang->load('notification');
        $this->load->model(array('Homepage_model','Notification_model'));
    }
    
    function index($page=false){
        $data = array();
        $session                    = $this->auth->get_current_user_session('user');
        $data['session']            = $session;
        $config                     = array();
        $config["base_url"]         = site_url('/')."notification/index";
        $config["status"]           = 1;
        $total_row                  = $this->Notification_model->notifications(array('status'=>$config["status"],'not_deleted'=>'1','count'=>'1'));
         //print_r($total_row); die;
        $config["total_rows"]       = $total_row;
        $config["uri_segment"]      = 3;
        $config["per_page"]         = 13;
        $config['use_page_numbers'] = TRUE;
        $config['num_links']        = $total_row;
        $config['cur_tag_open']     = '&nbsp<a class="links">';
        $config['cur_tag_close']    = '</a>';
        $config['next_link']        = 'Next';
        $config['prev_link']        = 'Previous';
        $this->pagination->initialize($config);
        $segment                    = ($this->config->item('index_page')=='')?3:4;
        $page                       = $this->uri->segment($segment);
        if($page===NULL)
        {
            $page                   = "1";
        }
        $page                       = ($page - 1)* $config["per_page"];
        $data['notifications']      = $this->Notification_model->notifications(array('status'=>$config["status"],'limit'=>$config["per_page"], 'offset'=>$page,'not_deleted'=>'1'));
        // echo "<pre>";print_r($data['notifications']); die;
        $str_links                  = $this->pagination->create_links();
        $data['links']              = explode('&nbsp;',$str_links );
        //$data['notifications']    = $this->Homepage_model->notifications(array('direction'=>'DESC', 'status'=>'1'));
        $this->load->view($this->config->item('theme').'/notifications', $data);
    }
    
    function view($id)
    {
        $data = array();
        $session    = $this->auth->get_current_user_session('user');
        $data['session'] = $session;
        $data['notification_content'] = $this->Notification_model->notification(array('id'=>$id));
        
        $data['meta_original_title']                    = $data['notification_content']['n_title'];
        $data['meta_title']                             = $data['notification_content']['n_meta'];
        $data['meta_description']                       = $data['notification_content']['n_seo_title'];
        
        //echo "<pre>";print_r($data);die;
        
        $this->load->view($this->config->item('theme').'/notification', $data);
    }
}


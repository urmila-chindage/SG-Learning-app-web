<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Expert_lectures extends CI_Controller 
{
    function __construct()
    {
        parent::__construct();
        $this->load->model(array('Expertlectures_model'));
        $this->lang->load('expert_lectures');
    }
    
    public function index() 
    {
        $data                       = array();
        
        
        $offset_recieved    = 0;
        //calculating page numbers
        $per_page           = 8;
        $data['per_page']   = $per_page;
        $page_num           = 1;
        $offset             = 0;
        $param['offset'] = $offset;
        $param['limit']  = $per_page;
        //end of calucalting page number
        $param['direction'] = 'DESC';
        $param['status']    = '1';
        $param['not_deleted'] = '1';
        
        $data['expert_lectures']    = $this->Expertlectures_model->expert_lectures($param);
        
        $this->load->view($this->config->item('theme').'/expert_lecture', $data);
    }
    
    public function expert_lectures_json()
    {
        $offset_recieved    = $this->input->post('offset');
        $response           = array();
        
        //calculating page numbers
        $per_page           = 8;
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
        //end of calucalting page number
        
        $param['direction'] = 'DESC';
        $param['status']    = '1';
        $param['not_deleted'] = '1';
        
        $response['error']      = false;

        $response['expert_lectures']    = $this->Expertlectures_model->expert_lectures($param);
        
        echo json_encode($response);
    }
}
<?php
class Video extends CI_Controller {
    function __construct()
    {
        parent::__construct();
        
        $this->load->model(array('Category_model', 'Course_model','Page_model','User_model'));
    }
    
    function index()
    {        
        $this->load->view($this->config->item('admin_folder').'/video_test');
    }
    
    function upload_catalog_image_to_localserver()
    {
        //$catalog_id             = $this->input->post('id');
        echo "<pre>";
        print_r($_POST);
        print_r($_FILES);
        die;
        
    }
}
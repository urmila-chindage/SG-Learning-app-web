<?php
class Page extends CI_Controller {
    function __construct()
    {
        parent::__construct();
    }
    
    function view($id)
    {
        $data                 = array();
        $objects              = array();
        $objects['key']       = 'page_'.$id;
        $callback             = 'page';
        $status               = '1';
        $preview              = $this->input->get('preview');
        $params               = array('id' => $id,'status'=>$status);

        if($preview)
        {
            $objects['key']       = 'page_preview_'.$id;
            $params               = array('id' => $id);
        }
        $page_content         = $this->memcache->get($objects, $callback, $params);
        
        $session              = $this->auth->get_current_user_session('user');
        $data['session']      = $session;
        if(!empty($page_content))
        {
            $data['title']          = $page_content['p_title'];
            $data['page_content']   = $page_content;
            $data['category_id']    = $page_content['p_category'];
            
            $data['meta_original_title']                    = $data['page_content']['p_title'];
            $data['meta_title']                             = $data['page_content']['p_meta'];
            $data['meta_description']                       = $data['page_content']['p_seo_title'];
            
            //echo "<pre>";print_r($data);die;
            
            $this->load->view($this->config->item('theme').'/page', $data);
        }
        else
        {
            $data            = array();
            $session         = $this->auth->get_current_user_session('user');
            $data['session'] = $session;
            $this->load->view($this->config->item('theme').'/404_error.php', $data);                              

        }
    }
}


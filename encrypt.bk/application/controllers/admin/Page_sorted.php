<?php
Class Page_sorted extends CI_Controller
{	
    function __construct()
    {
        parent::__construct();
        $this->__logged_in_user   = $this->auth->get_current_user_session( 'admin' );
        if ( empty($this->__logged_in_user) )
        {
            redirect( 'login' );
        }
        $this->load->model( 'Page_model' );   
    }

   public function index()
   {
        $data                = array();

        $breadcrumb[]                       = array('label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>');
        $breadcrumb[]                       = array('label' => 'Pages', 'link' => admin_url().'page', 'active' => '', 'icon' => '');
        $breadcrumb[]                       = array('label' => 'Menu Manager', 'link' => '', 'active' => 'active', 'icon' => '');
        $data['breadcrumb']                 = $breadcrumb;


        $pages               = array();
        $filter_param        = array( 
                                        'order_by'  => 'p_position',
                                        'direction' => 'ASC',
                                        'select'    => 'id, p_parent_id, p_title, p_position, p_show_page_in'
                                    );
        $page_show_type      = array( 
                                        '0' => 'no_where',
                                        '1' => 'headers',
                                        '2' => 'footers',
                                        '3' => 'headers'
                                    );

        $data['headers']     = array();
        $data['footers']     = array();
        $data['no_where']    = array();
        $data['childs']      = array();
        $pages               = $this->Page_model->pages($filter_param);
        foreach( $pages as $page )
        {
            $show_page_in       = $page['p_show_page_in'];
            $page_id            = $page['id'];
            $page_parent_id     = $page['p_parent_id'];
            $page_show_heading  = $page_show_type[$show_page_in];

            switch( $page['p_parent_id'] )
            {
                case "0":   
                    $data[$page_show_heading][$page_id]  = $page;
                break;
                        
                default:    
                    if(!isset($data['childs'][$page_parent_id]))
                    {
                        $data['childs'][$page_parent_id] = array();
                    }
                    $data['childs'][$page_parent_id][]   = $page;
                break;
            }

        }
        $data['course'] = array('cb_title' => 'Page Ordering');
        $this->load->view($this->config->item('admin_folder') . '/page_sorter', $data);  
    }
    
    public function update_page_section_position()
    {
        $response               = array();
        $response['error']      = 'false';
        $response['message']    = lang('section_saved_success');
        $structure              = $this->input->post('structure');
        parse_str( $structure, $structure );
        if( isset( $structure['section_wrapper'] ) && !empty( $structure['section_wrapper'] ) )
        {
            $page_orders = array();
            foreach ( $structure['section_wrapper'] as $order => $id ) 
            {
                $save               = array();
                $save['id']         = $id;
                $save['p_position'] = ($order)+1;
                $page_orders[]      = $save;
            }
            $this->Page_model->save_page_section($page_orders);
        }
        $this->memcache->delete('pages'); 
        echo json_encode($response);
    }

    function update_page_lecture_position()
    {
        $response                   = array();
        $response['error']          = 'false';
        $response['message']        = lang('section_saved_success');
        $section_id                 = $this->input->post('section_id');
        $section_id                 = explode('_', $section_id);
        $section_id                 = $section_id[2] ? $section_id[2] : 0  ; 
        $response['section_id']     = $section_id;
        $structure                  = $this->input->post('structure');
        parse_str($structure, $structure);
        if( isset($structure['lecture_id']) ) 
        {
            $page_orders = array();
            foreach ( $structure['lecture_id'] as $order => $id ) 
            {
                $save                   = array();
                $save['id']             = $id;
                $save['p_parent_id']    = $section_id;
                $save['p_position']     = ($order)+1;
                $page_orders[]          = $save;
            }
            $this->Page_model->save_page_section($page_orders);
        }
        echo json_encode($response);
    }
}
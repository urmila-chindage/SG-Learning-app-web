<?php
Class Menu_sorted extends CI_Controller
{	
    function __construct()
    {
        parent::__construct();
        $this->__logged_in_user   = $this->auth->get_current_user_session( 'admin' );
        $this->load->library(array('form_validation'));
        if ( empty($this->__logged_in_user) )
        {
            redirect( 'login' );
        }
        $this->load->model( 'Menu_model' );   
    }
   public function index()
   {
        $data                   = array();

        $breadcrumb[]           = array('label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>');
        $breadcrumb[]           = array('label' => 'Menu Manager', 'link' => '', 'active' => 'active', 'icon' => '');
        $data['breadcrumb']     = $breadcrumb;


        $pages                  = array();
        $filter_param           = array( 
                                        'order_by'  => 'mm_sort_order',
                                        'direction' => 'ASC',
                                        'select'    => 'menu_manager.id, menu_manager.mm_parent_id, menu_manager.mm_name, menu_manager.mm_sort_order, menu_manager.mm_show_in'
                                    );
        $menu_show_type         = array( 
                                        '1' => 'headers',
                                        '2' => 'footers',
                                    );

        $data['headers']        = array();
        $data['footers']        = array();
        $menus                  = $this->Menu_model->menus($filter_param);
        foreach( $menus as $menu )
        {
            $show_in            = $menu['mm_show_in'];
            $menu_id            = $menu['id'];
            $menu_parent_id     = $menu['mm_parent_id'];
            $menu_show_heading  = $menu_show_type[$show_in];

            switch( $menu['mm_parent_id'] )
            {
                case "0":   
                    $data[$menu_show_heading][$menu_id]  = $menu;
                break;
                        
                default:    
                    if(!isset($data['childs'][$menu_parent_id]))
                    {
                        $data['childs'][$menu_parent_id] = array();
                    }
                    $data['childs'][$menu_parent_id][]   = $menu;
                break;
            }

        }
        //echo '<pre>';print_r($data);die;
        $this->load->view($this->config->item('admin_folder') . '/page_sorter', $data); 
   }
   public function saveMenu( $id = "" )
   {
        $this->form_validation->set_rules('menu_name', 'Menu Name','required');
        $this->form_validation->set_rules('menu_show_in', 'Menu Show In','required');
        if ($this->form_validation->run() == FALSE)
        {
            $data['errors']             = validation_errors(); 
            redirect($this->config->item('admin_folder').'/Menu_sorted');
        }
        else
        {
            if( !empty($id) )
            {
                $filter['update']       = true;
                $filter['id']           = $id;
                $message                = 'Menu updated successfully';
            }
            else
            {
                $filter['insert']       = true;
                $message                = 'Menu saved successfully';
            }
            
            $menu_name                  = $this->input->post('menu_name');
            $save['mm_name']            = ltrim($menu_name," ");
            $save['mm_show_in']         = $this->input->post('menu_show_in');
            $save['mm_item_type']       = $this->input->post('menu_item_type');
            $save['mm_created_date']    = date('Y-m-d');
            $save['mm_sort_order']      = 0;
            $save['mm_account_id']      = config_item('id');
            if( $save['mm_show_in'] == 2 || empty( $this->input->post('menu_parent') ))
            {
                $save['mm_parent_id']   = 0;
            }
            else
            {
                $save['mm_parent_id']   = $this->input->post('menu_parent');
            }
            $this->Menu_model->save($save,$filter);
            $template                   = 'message';
            $this->memcache->delete('menus'); 
            $savenext                   = $this->input->post('savenext');
            $this->session->set_flashdata($template, $message);
            redirect($this->config->item('admin_folder').'/Menu_sorted');
        }
   }
    
   public function update_page_section_position()
   {
       $response                        = array();
       $response['error']               = 'false';
       $response['message']             = lang('section_saved_success');
       $structure                       = $this->input->post('structure');
       parse_str( $structure, $structure );                                      
       if( isset( $structure['section_wrapper'] ) && !empty( $structure['section_wrapper'] ) )
       {
           $page_orders                 = array();
           foreach ( $structure['section_wrapper'] as $order => $id ) 
           {
               $save                    = array();
               $save['id']              = $id;
               $save['mm_sort_order']   = ($order)+1;
               $page_orders[]           = $save;
           }
           $this->Menu_model->save_page_section($page_orders);
       }
       $this->memcache->delete('menus'); 
       echo json_encode($response);
   }

   function update_page_lecture_position()
   {
       $response                        = array();
       $response['error']               = 'false';
       $response['message']             = lang('section_saved_success');
       $section_id                      = $this->input->post('section_id');
       $section_id                      = explode('_', $section_id);
       $section_id                      = $section_id[2] ? $section_id[2] : 0  ; 
       $response['section_id']          = $section_id;
       $structure                       = $this->input->post('structure');
       parse_str($structure, $structure);
       if( isset($structure['lecture_id']) ) 
       {
           $page_orders = array();
           foreach ( $structure['lecture_id'] as $order => $id ) 
           {
               $save                   = array();
               $save['id']             = $id;
               $save['mm_parent_id']   = $section_id;
               $save['mm_sort_order']  = ($order)+1;
               $page_orders[]          = $save;
           }
           $this->memcache->delete('menus'); 
           $this->Menu_model->save_page_section($page_orders);
       }
       echo json_encode($response);
   }
   
   public function checkChildExist( $menu_id )
   {
       $id              = $menu_id;
       $check_for_child = $this->Menu_model->checkChildExists($id);
       $linked_page     = $this->Menu_model->get_page_name($id);
       echo json_encode(array('child' => $check_for_child, 'linked_page' => $linked_page ));
   }
   
   public function delete( $id = "" )
   {
        $id                             = $this->input->post('menu_id');
        if( $this->Menu_model->delete($id) )
        {
            $response['message']        = 'Menu successfully deleted';
            $response['error']          = false;
            echo json_encode($response);
        }
        else 
        {
            $response['message']        = 'Something wenr wrong !';
            $response['error']          = true;
            echo json_encode($response);
        }
        $this->memcache->delete('menus'); 
       return;
   }
}
<?php
class Page extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $redirect                       = $this->auth->is_logged_in(false, false);
        if (!$redirect)
        {
            redirect('login');
        }
        $this->actions                  = $this->config->item('actions');
        $this->load->model(array('Page_model','Routes_model','Category_model'));
        $this->lang->load('page');
        $this->limit                    = 100;
        $this->__loggedInUser           = $this->auth->get_current_user_session('admin');
        $this->__page_privilege         = $this->accesspermission->get_permission(array('role_id' => $this->__loggedInUser['role_id'],'module' => 'page'));
        $this->__access                 = array( "view" => 1, "add" => 2, "edit" => 3, "delete" => 4 );
        if(!in_array($this->__access['view'], $this->__page_privilege))
        {
            redirect(admin_url());
        }
    }
    
    function index()
    {
        $data                           = array();
        $breadcrumb                     = array();
        $data['success']                = false;
        $data['message']                = 'Error to get pages';
        $breadcrumb[]                   = array( 'label' => 'Home', 'link' => site_url('/admin'), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]                   = array( 'label' => 'Pages', 'link' => admin_url('page'), 'active' => 'active', 'icon' => '' );
        //$breadcrumb[]                   = array( 'label' => lang('manage_pages'), 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']             = $breadcrumb;
        $data['title']                  = lang('pages');
        $data['limit']                  = $this->limit;

        $keyword                        = $this->input->get('keyword');
        $offset                         = $this->input->get('offset');
        $filters                        = $this->input->get('filter');
        $filter                         = $filters ? $filters : 'active'; 

        $showin                         = $this->input->get('showin');
        $showin                         = $showin ? $showin : '';

        $limit                          = $this->limit;
        $page                           = $offset;
        if($page===NULL||$page<=0)
        {
            $page                       = 1;
        }
        $page                           = ($page - 1)* $this->limit;
        if($keyword)
        {
            $keyword                    = explode('-', $keyword);
            $keyword                    = implode(' ',$keyword);
        }
        $filter_param                   = array(
                                                'order_by'     => 'id',
                                                'direction'    => 'DESC',
                                                'parent_id'    => '0',
                                                'keyword'      => $keyword,
                                                'filter'       => $filter,
                                                'limit'        => $limit,
                                                'offset'       => $page,
                                                'showin'       => $showin,
                                                'count'        => false
                                             );
        $pages                          = $this->Page_model->pages($filter_param);
        $data['pages']                  = array();
        if(!empty($pages))
        {
            $data['success']            = true;
            $data['message']            = 'Pages Fetched Successfully.';
            $data['pages']              = $pages;

        }
        $filter_count_param             = array(
                                                    'order_by'     => 'id',
                                                    'direction'    => 'DESC',
                                                    'parent_id'    => '0',
                                                    'keyword'      => $keyword,
                                                    'filter'       => $filter,
                                                    'showin'       => $showin,
                                                    'count'        => true,
                                                    'not_deleted'  => $filter != 'deleted' ? false : true
                                                );
        $total_pages                    = $this->Page_model->pages($filter_count_param);
        //echo '<pre>';print_r($total_pages);die;
        $data['total_pages']            = $total_pages;
        $data['limit']                  = $limit;
        $data['offset']                 = $offset;
        $this->load->view($this->config->item('admin_folder').'/page', $data);
    }
     
    function filter_pages()
    {
        $data                           = array();
        $data['success']                = false;
        $data['message']                = 'Error to get pages';

        $keyword                        = $this->input->post('keyword');
        $offset                         = $this->input->post('offset');
        $filter                         = $this->input->post('filter');
        $filter                         = ( $filter )? $filter : 'active';
        $showin                         = $this->input->post('showin');
        $showin                         = $showin ? $showin : '';
        $limit                          = $this->limit;
        $page                           = $offset;
        if($page===NULL||$page<=0)
        {
            $page                       = 1;
        }
        $page                           = ($page - 1)* $this->limit;
        if($keyword)
        {
            $keyword                    = explode('-', $keyword);
            $keyword                    = implode(' ',$keyword);
        }
        $filter_param                   = array(
                                                'order_by'     => 'id',
                                                'direction'    => 'DESC',
                                                'parent_id'    => '0',
                                                'keyword'      => $keyword,
                                                'filter'       => $filter,
                                                'showin'       => $showin,
                                                'limit'        => $limit,
                                                'offset'       => $page,
                                                'count'        => false
                                             );
        $pages                          = $this->Page_model->pages($filter_param);
        $data['pages']                  = array();
        if(!empty($pages))
        {
            $data['success']            = true;
            $data['message']            = 'Pages Fetched Successfully.';
            $data['pages']              = $pages;
        }
        $filter_count_param             = array(
                                                'order_by'     => 'id',
                                                'direction'    => 'DESC',
                                                'parent_id'    => '0',
                                                'keyword'      => $keyword,
                                                'showin'       => $showin,
                                                'filter'       => $filter,
                                                'count'        => true
                                            );
        $total_pages                    = $this->Page_model->pages($filter_count_param);
        $data['total_pages']            = $total_pages;
        echo json_encode($data);
    }
    
    function language()
    {
        $response                       = array();
        $response['language']           = array();
        $response['language']           = get_instance()->lang->language;
        echo json_encode($response);
    }
    
    function delete()
    {
        $response                       = array();
        $response['error']              = false;
        $page_id                        = $this->input->post('page_id');
        $save                           = array();
        $save['id']                     = $page_id;
        $save['action_by']              = $this->auth->get_current_admin('id');
        $save['action_id']              = $this->actions['delete'];
        $save['updated_date']           = date('Y-m-d H:i:s');
        $save['p_deleted']              = '1';
        $response['message']            = lang('page_delete_success');
        
        if($this->Page_model->save($save))
        {
            $this->Page_model->invalidateExistingMenus($page_id, array('mm_status' => 0));
        }
        else
        {
            $response['error']          = true;
            $response['message']        = lang('delete_page_failed');
        }
        
        $this->invalidate_pages(array('key'=>'menus'));
        $this->invalidate_pages(array('key'=>'page_menus'));
        $this->invalidate_pages();     
        echo json_encode($response);        
    }

    function restore()
    {
        $response                       = array();
        $response['error']              = false;
        $page_id                        = $this->input->post('page_id');
        $save                           = array();
        $save['id']                     = $page_id;
        $save['action_by']              = $this->auth->get_current_admin('id');
        $save['action_id']              = $this->actions['restore'];
        $save['updated_date']           = date('Y-m-d H:i:s');
        $save['p_deleted']              = '0';
        $save['p_status']               = '0';
        
        $response['message']    = lang('restore_page_success');
            
        if(!$this->Page_model->save($save))
        {
            $response['error']   = true;
            $response['message'] = lang('restore_page_failed');
        }  
        $this->invalidate_pages(array('key'=>'pages'));
        $this->invalidate_pages(array('key'=>'page_'.$page_id));
        $this->invalidate_pages(array('key'=>'page_preview_'.$page_id));    
        echo json_encode($response);        
    }

    function restore_page_bulk()
    {
        $response                       = array();
        $response['error']              = false;
        $page_ids                       = json_decode($this->input->post('pages'));
        if(empty($page_ids))
        {
            $response['error']          = true;
            $response['message']        = lang('id_missing');
        }
        $page_params                    = array();
        
        foreach ($page_ids as $page_id) {
            $save                       = array();
            $save['id']                 = $page_id;
            $save['p_deleted']          = '0';
            $save['action_by']          = $this->auth->get_current_admin('id');
            $save['updated_date']       = date('Y-m-d H:i:s');
            $save['action_id']          = $this->actions['delete'];
            $save['p_status']           = '0';
            $page_params[]              = $save;
        }
        $bulk_restore                    = $this->Page_model->save_bulk($page_params);
        $response['message']            = lang('bulk_restored');
        if(!$bulk_restore)
        {
            $response['error']          = true;
            $response['message']        = lang('restore_page_failed');
        }
        $this->invalidate_pages(array('key'=>'pages'));
        echo json_encode($response);
    }

    function delete_page_bulk()
    {
        $response                       = array();
        $response['error']              = false;
        $page_ids                       = json_decode($this->input->post('pages'));
        if(empty($page_ids))
        {
            $response['error']          = true;
            $response['message']        = lang('id_missing');
        }
        $page_params                    = array();
        
        foreach ($page_ids as $page_id) {
            $save                       = array();
            $save['id']                 = $page_id;
            $save['p_deleted']          = '1';
            $save['action_by']          = $this->auth->get_current_admin('id');
            $save['updated_date']       = date('Y-m-d H:i:s');
            $save['action_id']          = $this->actions['delete'];
            $page_params[]              = $save;
        }
        $bulk_delete                    = $this->Page_model->save_bulk($page_params);
        $response['message']            = lang('bulk_deleted');
        if(!$bulk_delete)
        {
            $response['error']          = true;
            $response['message']        = lang('delete_page_failed');
        }
        $this->invalidate_pages(array('key'=>'pages'));
        echo json_encode($response);
    }
    
    function change_status()
    {
        $response                       = array();
        $response['error']              = false;
        $page_id                        = $this->input->post('page_id');
        $status                         = $this->input->post('status');
        
        $save                           = array();
        $save['id']                     = $page_id;
        $save['action_by']              = $this->auth->get_current_admin('id');
        $save['updated_date']           = date('Y-m-d H:i:s');
        $save['p_status']               = $status;
        $save['action_id']              = $this->actions['activate'];
        $response['message']            = lang('page_activated');

        $mm_save                        = array();
        $mm_save['mm_item_connected_id']= $page_id;
        $mm_save['mm_status']           = $status;

        $page                           = $this->Page_model->page(array('id' => $page_id, 'select' => 'p_content, p_goto_external_url'));

        if(!$page['p_content'] && !$page['p_goto_external_url'])
        {
            $response['error']          = true;
            $response['message']        = lang('error_null_content');
            echo json_encode($response);
            return;
        }
        //print_r($page); exit;
        if($status == '0')
        {
            $response['message']        = lang('page_deactivated');
        }

        if($this->Page_model->save($save))
        {
            $this->Page_model->invalidateExistingMenus($page_id, array('mm_status' => $status));
        }
        else
        {
            $response['error']          = true;
            $response['message']        = lang('error_change_status');
        }

        $this->invalidate_pages(array('key'=>'pages'));   
        $this->invalidate_pages(array('key'=>'menus'));   
        $this->invalidate_pages(array('key'=>'page_'.$page_id));
        
        echo json_encode($response);
    }
    
    function change_status_bulk()
    {
        $response                       = array();
        $response['error']              = false;
        $status                         = $this->input->post('status');
        $page_ids                       = json_decode($this->input->post('pages'));
        $error_count                    = 0;
        $error_msg                      = '';
        $page_params                    = array();
        if(empty($page_ids))
        {
            $response['error']          = true;
            $response['message']        = lang('id_missing');
        }
       
        foreach ($page_ids as $page_id) {
            $save                       = array();
            $save['id']                 = $page_id;
            $save['p_status']           = $status;
            $save['action_by']          = $this->auth->get_current_admin('id');
            $save['updated_date']       = date('Y-m-d H:i:s');
            $save['action_id']          = $this->actions['activate'];
        
            $page                       = $this->Page_model->page(array('id' => $page_id, 'select' => 'p_title, p_content, p_goto_external_url'));
            
            if(!$page['p_content'] && !$page['p_goto_external_url'])
            {
                $error_count++;
                $error_msg             .= '<h5 style="font-size: 15px;">'.$error_count.'. <b>'.$page['p_title'].'</b></h5>';
            }
            else
            {
                $page_params[]          = $save;         
            }
        }

        $bulk_status                    = $this->Page_model->save_bulk($page_params);
        $response['message']            = lang('status_updated');
        if(!$bulk_status)
        {
            $response['error']          = true;
            $response['message']        = lang('error_change_status');
        }
        if($error_count > 0)
        {
                $errorpage = 'page';
            if($error_count > 1)
            {
                $errorpage = 'pages';
            }
            $response['error']          = true;
            $message                    = '<h5 style="margin-bottom: 15px; font-size: 18px;">Activation failed for following '.$errorpage.':</h5><h4>Page(s) do not have any content:</h4>';
            $response['message']        = $message.$error_msg;
        }
        $this->invalidate_pages(array('key'=>'pages'));
        echo json_encode($response);
    }
    
    public function create_page()
    {
        $response                       = array();
        $response['error']              = false;
        $response['message']            = lang('page_created_success');
        $page_name                      = $this->input->post('page_name');
        if($page_name == '')
        {
            $response['error']          = true;
            $response['message']        = lang('page_name_required');
            echo json_encode($response);exit;
        }
        $this->load->helper('text');
        $slug                           = $page_name;
        $slug	                        = url_title(convert_accented_characters($slug), 'dash', TRUE);
        $slug                           = $this->Routes_model->validate_slug($slug);
        $route['slug']	                = $slug;	
        $route_id                       = $this->Routes_model->save($route);
        
        $save                           = array();
        $save['id']                     = false;
        $save['p_title']                = $page_name;
        $save['action_id']              = $this->actions['create']; 
        $save['action_by']              = $this->auth->get_current_admin('id');
        $save['p_route_id']             = $route_id;
        $save['p_slug']                 = $slug;
        $save['p_account_id']           = $this->config->item('id');
        $page_id                        = $this->Page_model->save($save);		
        $response['id']                 = $page_id;
        
        $route['id']                    = $route_id;
        $route['slug']                  = $slug;
        $route['route']                 = 'page/view/'.$page_id;
        $route['r_account_id']	        = $this->config->item('id');
        $route['r_item_type']           = 'page';
        $route['r_item_id']             = $page_id;
        $this->Routes_model->save($route);
        $this->invalidate_pages(array('key'=>'pages'));
        echo json_encode($response);exit;
    }
    
    public function title_check($str)
    {
        if(strip_tags($str)=='')
        {
            $this->form_validation->set_message('title_check', 'The {field} field is invalid');
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }


    function basics( $page_id = false )
    {
        
        if(!$page_id){
            redirect($this->config->item('admin_folder').'/page');
        }

        $data               = array();
        $data['title']      = lang('pages');
        $this->load->model('Category_model');
        //$data['categories'] = $this->Category_model->categories(array('status'=>'1', 'parent_id'=>'0'));
        
        $this->load->helper('form');
        $this->load->library('form_validation');
        $data['id']                     = $page_id;
        $data['p_parent_id']            = $this->input->post('p_parent_id');
        $data['p_title']                = strip_tags($this->input->post('p_title'));
        $data['p_position']             = $this->input->post('p_position');
        $data['p_category']             = $this->input->post('p_category');
        $data['p_external_url']         = $this->input->post('p_external_url');
        $data['p_goto_external_url']    = $this->input->post('p_goto_external_url');
        $data['p_content']              = $this->input->post('p_content');
        $data['p_new_window']           = $this->input->post('p_new_window');
        $data['p_quick_link']           = $this->input->post('p_quick_link');
        $data['p_status']               = $this->input->post('p_status');
        $data['p_connected_menu']       = $this->input->post('mm_connected_to');
        
        if($page_id)
        {
            $filter_param_menu          = array( 
                                            'order_by'  => 'mm_sort_order',
                                            'direction' => 'ASC',
                                            'select'    => 'menu_manager.id, menu_manager.mm_parent_id, menu_manager.mm_name, menu_manager.mm_show_in, menu_manager.mm_item_connected_id, menu_manager.mm_item_connected_slug, pages.p_title'
                                        );
            //$menus     = $this->Page_model->menus($filter_param_menu);
           
            $page               = $this->Page_model->page(array('id' => $page_id));
            if(!$page){
                redirect($this->config->item('admin_folder').'/page');
            }
            //echo '<pre>';print_r($this->input->post());die;
            $page_menus = array();
            $page_menus['header'] = menu_pages(array('type' => 'header', 'backend' => true));
            $page_menus['footer'] = menu_pages(array('type' => 'footer', 'backend' => true));
            //echo '<pre>';print_r($page_menus);die;
            $data['menus']               = $page_menus;
            $data['id']                  = $page['id'];
            $data['p_parent_id']         = $page['p_parent_id'];
            $data['p_category']          = $page['p_category'];
            $data['p_title']             = $page['p_title'];
            $data['p_position']          = $page['p_position'];
            $data['p_external_url']      = $page['p_external_url'];
            $data['p_goto_external_url'] = $page['p_goto_external_url'];
            $data['p_content']           = $page['p_content'];
            $data['p_new_window']        = $page['p_new_window'];
            $data['p_show_page_in']      = $page['p_show_page_in'];
            $data['p_quick_link']        = $page['p_quick_link'];
            $data['p_status']            = $page['p_status'];
            $data['p_connected_menu']    = $page['p_connected_menu'];
        }
        
        $breadcrumb                      = array();
        $breadcrumb[]                    = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]                    = array( 'label' => 'Pages', 'link' => admin_url('page'), 'active' => '', 'icon' => '' );
        // $breadcrumb[]                    = array( 'label' => lang('manage_pages'), 'link' => admin_url('page'), 'active' => 'active', 'icon' => '' );
        $breadcrumb[]                    = array( 'label' => $page['p_title'], 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']              = $breadcrumb;
        
        $param                           = array();
        $param['parent_id']              = 0;
        $param['not_deleted']            = true;
        $param['p_status']               = '1';
        if($data['p_show_page_in'] == '1')
        {
            $param['p_show_page_in']     = '1';
            $param['header']             = true;
        }
        if($data['p_show_page_in'] == '2')
        {
            $param['p_show_page_in']     = '2';
            $param['footer']             = true;
        }
        if($data['p_show_page_in'] == '4')
        {
            if($data['p_category'] == '0'){
                //$this->session->set_flashdata('error', 'Select Page category');
            }else{
                $param['category_id']    = $data['p_category'];
            }
        }
        $data['page_tree']               = $this->Page_model->page_tree($param);
        
        $this->form_validation->set_rules('p_title', 'Page Title','required|trim|callback_title_check');
        $p_goto_external_url             = $this->input->post('p_goto_external_url');
        if( $p_goto_external_url == '1'){

            $this->form_validation->set_rules('p_external_url', 'External url','trim|required|callback_valid_url');
        }
        else{
            $this->form_validation->set_rules('p_content', 'Page Description','trim|required');
        }
        $this->form_validation->set_rules('p_position', 'Page position','trim|numeric');
        // $this->form_validation->set_rules('mm_connected_to', 'Connected To','trim|required|numeric');
        //     $p_show_page_in               = $this->input->post('p_show_page_in');
        // if( $p_show_page_in == '4'){
        //     $this->form_validation->set_rules('p_category','Page Category','required|callback_check_category');
        //     $this->form_validation->set_message('check_category', 'You need to select something other than the default page category');
        // }
        
        if ($this->form_validation->run() == FALSE)
        {
            $data['errors']              = validation_errors(); 
            //echo '<pre>';print_r($data); die;
            $this->load->view($this->config->item('admin_folder').'/page_basics',$data);
        }
        else
        {
            $save['id']                  = $page_id;
            $save['p_parent_id']         = $this->input->post('p_parent_id');
            $save['p_position']          = $this->input->post('p_position');
            $save['p_title']             = strip_tags(ltrim($this->input->post('p_title')));
            $save['p_category']          = $this->input->post('p_category');
            $save['p_external_url']      = $this->input->post('p_external_url');
            $save['p_goto_external_url'] = $this->input->post('p_goto_external_url');
            $save['p_content']           = $this->input->post('p_content');
            $save['p_new_window']        = $this->input->post('p_new_window');
            $save['p_connected_menu']    = $this->input->post('mm_connected_to');
            $save['p_status']            = $this->input->post('p_status');
            $save['p_quick_link']        = $this->input->post('p_quick_link');
            $save['action_by']           = $this->auth->get_current_admin('id');
            $save['action_id']           = $this->actions['update'];
            $save['updated_date']        = date('Y-m-d H:i:s');
            // echo '<pre>';print_r($save); die;
            // var_dump($_POST);die;
            if( $this->input->post('p_goto_external_url') == '1' )
            {
                $menu_save                   = array(
                                                    'mm_external_url'           => $save['p_external_url'],
                                                    'mm_connected_as_external'  => 1,
                                                    'mm_new_window'             => $save['p_new_window'],
                                                    'mm_status'                 => $save['p_status']
                                                );
            }
            else
            {
                $menu_save                   = array(
                                                'mm_item_connected_slug'        => $page['p_slug'],
                                                'mm_item_connected_id'          => $page_id,
                                                'mm_connected_as_external'      => 0,
                                                'mm_new_window'                 => $save['p_new_window'],
                                                'mm_status'                     => $save['p_status']
                                            );
            }

            $this->Page_model->invalidateExistingMenus($page_id, array('mm_item_connected_id' => 0, 'mm_item_connected_slug' => NULL));
            $save['id']                  = $this->Page_model->save($save);
            $this->memcache->delete('menus');
            $this->Page_model->saveConnectedMenu($save['p_connected_menu'], $menu_save );
            $this->invalidate_pages(array('key'=>'pages'));
            $this->invalidate_pages(array('key'=>'page_'.$page_id));
            $this->invalidate_pages(array('key'=>'page_preview_'.$page_id));
            $this->Page_model->save_page_tree_attribute(array('parent_id' => $save['id'], 'category_id' => $save['p_category']));
            $this->session->set_flashdata('message', lang('page_basics_saved'));
            
            redirect($this->config->item('admin_folder').'/page/basics/'.$page_id);
        }
    }
    
    function valid_url()
    {
        $external_url  = $this->input->post('p_external_url');
        $external_url = filter_var($external_url, FILTER_SANITIZE_URL);
        if(preg_match('/^(http|https):\\/\\/[a-z0-9_]+([\\-\\.]{1}[a-z_0-9]+)*\\.[_a-z]{2,5}'.'((:[0-9]{1,5})?\\/.*)?$/i' ,$external_url)){
            return TRUE;
        }

        return FALSE;
    }

    function check_category($post_string)
    {
        return $post_string == '0' ? FALSE : TRUE;
    }
    
    function seo( $page_id = false )
    {
        $data                           = array();
        $data['title']                  = lang('pages');
        if(!$page_id){
            redirect($this->config->item('admin_folder').'/page');
        }
        $this->load->helper('form');
        $this->load->library('form_validation');

        $data['id']                     = $page_id;
        $page['p_title']                = '';
        $data['p_seo_title']            = $this->input->post('p_seo_title');
        $data['p_slug']                 = $this->input->post('p_slug');
        $data['p_meta']                 = $this->input->post('p_meta');
        $data['p_route_id']             = 0;
        
        if($page_id)
        {
            $page                       = $this->Page_model->page(array('id' => $page_id));
            if(!$page)
            {
                redirect($this->config->item('admin_folder').'/page'); 
            }
            
            $data['id']                 = $page['id'];
            $data['p_title']            = $page['p_title'];
            $data['p_seo_title']        = $page['p_seo_title'];
            $data['p_slug']             = $page['p_slug'];
            $data['p_meta']             = $page['p_meta'];
            $data['p_route_id']         = $page['p_route_id'];
            $this->memcache->delete(base64_encode($data['p_slug'].config_item('id')), 0, true);
        }
        $breadcrumb                     = array();
        $breadcrumb[]                   = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]                   = array( 'label' => 'Pages', 'link' => admin_url('page'), 'active' => '', 'icon' => '' );
        $breadcrumb[]                   = array( 'label' => $page['p_title'], 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']             = $breadcrumb;

        $this->form_validation->set_rules('p_seo_title', 'Seo title','trim'); 
        $this->form_validation->set_rules('p_meta', 'Meta','trim');

        if ($this->form_validation->run() == FALSE)
        {
            $data['errors']             = validation_errors(); 
            $this->load->view($this->config->item('admin_folder').'/page_seo',$data);
        }
        else
        {
            $this->load->helper('text');
            $slug                       = $this->input->post('p_slug');
            if(empty($slug) || $slug=='')
            {
                $slug                   = $data['p_title'];
            }
            $slug	                    = url_title(convert_accented_characters($slug), 'dash', TRUE);
            if($page_id)
            {
                $slug		            = $this->Routes_model->validate_slug($slug, $page['p_route_id']);
                $route_id	            = $page['p_route_id'];
            }
            else
            {
                $slug                   = $this->Routes_model->validate_slug($slug);
                $route['slug']	        = $slug;	
                $route_id	            = $this->Routes_model->save($route);
            }

            $save                       = array();
            $save['id']                 = $page_id;
            $save['p_seo_title']        = $this->input->post('p_seo_title');
            $save['p_meta']             = $this->input->post('p_meta');
            $save['p_route_id']         = $route_id;
            $save['p_slug']             = $slug;
            $save['action_by']          = $this->auth->get_current_admin('id');
            $save['action_id']          = $this->actions['update'];
            $save['updated_date']       = date('Y-m-d H:i:s');
            $page_id                    = $this->Page_model->save($save);

            $route['id']	            = $route_id;
            $route['slug']	            = $slug;
            $route['route']	            = 'page/view/'.$page_id;
            $route['r_item_type']       = 'page';
            $route['r_item_id']         = $page_id;
            $this->Routes_model->save($route);
            $menu_save                  = array(
                                            'mm_item_connected_slug'        => $slug,
                                            'mm_item_connected_id'          => $page_id
                                        );
            $this->Page_model->saveConnectedMenu($page_id, $menu_save );
            $this->invalidate_pages(array('key'=>'pages'));
            $this->invalidate_pages(array('key'=>'menus'));
            $this->invalidate_pages(array('key'=>'page_'.$page_id));
            $this->invalidate_pages(array('key'=>'page_preview_'.$page_id));
            $this->session->set_flashdata('message', lang('page_seo_saved'));
            redirect($this->config->item('admin_folder').'/page/seo/'.$page_id);
        }
    }
    
    function get_header_pages()
    {
        $response = array();
        $response['pages'] = $this->Page_model->page_tree(array( 'status' => '1', 'header' => true, 'not_deleted' => true));
        echo json_encode($response);
    }

    function get_footer_pages()
    {
        $response = array();
        $response['pages'] = $this->Page_model->page_tree(array( 'status' => '1', 'footer' => true, 'not_deleted' => true));
        echo json_encode($response);
    }
    
    function get_all_pages()
    {
        $response = array();
        $response['pages'] = $this->Page_model->page_tree(array('parent_id' => 0));
        echo json_encode($response);
    }

    function get_category_pages()
    {
        $response           = array();
        $category_id        = $this->input->post('category_id');
        $response['pages']  = $this->Page_model->page_tree(array('category_id' => $category_id));
        echo json_encode($response);
    }

    function invalidate_pages($params = array())
    {
        if(isset($params['key']) && $params['key']!='')
        {
            $this->memcache->delete($params['key']);
        }
        else
        {
            $this->memcache->delete('pages');
        }
    }

    public function update_page_position()
    {
        $response                   = array();
        $response['error']          = false;
        $response['message']        = 'pages re ordered';
        $pages                      = $this->input->post('page_id');
        // echo "<pre>";print_r($pages); exit;
        $params = array();
        if(!empty($pages))
        {
            foreach ($pages as $order => $id) 
            {
                $params[$order]['p_order']  = ($order)+1;
                $params[$order]['id']       = $id;
            }
            //print_r($params); exit;
            if( !empty($params))
            {
                $this->Page_model->save_bulk($params);
                $this->invalidate_pages();
                echo json_encode($response);
                return true;
            }
        }
        $response['error']          = true;
        $response['message']        = 'failed to re order pages';
        echo json_encode($response);
        return false;
    }
    public function pageName()
    {
        $connecting_menu_id    = $this->input->post('connecting_menu_id'); 
        $connecting_page_id    = $this->input->post('connecting_page_id');
        
        $menudetails           = $this->Page_model->pageName(
                                                        array(
                                                                'connecting_menu_id' => $connecting_menu_id, 
                                                                'connecting_page_id' => $connecting_page_id
                                                            )
                                                        );
        echo json_encode($menudetails);
    }
}
    
?>
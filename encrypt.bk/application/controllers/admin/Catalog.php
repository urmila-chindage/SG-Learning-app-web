<?php
class Catalog extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $redirect	= $this->auth->is_logged_in(false, false);
        if (!$redirect)
        {
            redirect('login');
        }
        $this->actions = $this->config->item('actions');
        $this->load->model(array('Category_model', 'Catalog_model'));
        $this->lang->load('catalog');
    }

    function index()
    {
        $data                       = array();
        $breadcrumb                 = array();
        $breadcrumb[]               = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]               = array( 'label' => lang('catalog_bar_trainings'), 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']         = $breadcrumb;
        $data['title']              = lang('catalog_bar_trainings');
        $data['categories']         = $this->Category_model->categories(array('direction'=>'DESC', 'not_deleted'=>true));
        $data['catalogs']           = $this->Catalog_model->catalogs(array('direction'=>'DESC'));
        $this->load->view($this->config->item('admin_folder').'/catalogs', $data);
    }
    
    function catalog_json()
    {
        $data               = array();
        $data['catalogs']   = $this->Catalog_model->catalogs(array('direction'=>'DESC', 'category_id'=>  $this->input->post('category_id'), 'keyword'=>  $this->input->post('keyword'), 'filter'=>  $this->input->post('filter')));
        echo json_encode($data);
    }
    
    function change_status_bulk()
    {
        $response               = array();
        $response['error']      = false;
        $status                 = $this->input->post('status_bulk');
        $catalog_ids            = json_decode($this->input->post('catalogs'));
        if(!empty($catalog_ids))
        {
            foreach ($catalog_ids as $catalog_id) {
                $save                   = array();
                $save['id']             = $catalog_id;
                $save['c_status']       = $status;
                $save['action_by']      = $this->auth->get_current_admin('id');
                $save['updated_date']   = date('Y-m-d H:i:s');
                $save['action_id']      = $this->actions['activate'];
                $catalog                = $this->Catalog_model->catalog(array('id' => $catalog_id));
                if($catalog['c_image'] != 'default.jpg' || $status == '0')
                {
                    $this->Catalog_model->save($save);
                }
                else
                {
                    $response['error']   = true;
                    $response['message'] = lang('image_missing_on_activate');
                }
                
            }
        }
        $response['catalogs']    = $this->Catalog_model->catalogs(array('direction'=>'DESC', 'category_id'=>  $this->input->post('category_id'), 'keyword'=>  $this->input->post('keyword'), 'filter'=>  $this->input->post('filter')));
        echo json_encode($response);
    }
    
    function change_status()
    {
        $response               = array();
        $message                = '';
        $response['error']      = false;
        $catalog_id             = $this->input->post('catalog_id');
        $catalog                 = $this->Catalog_model->catalog(array('id' => $catalog_id));
        
        if($catalog['c_image'] == 'default.jpg' && $catalog['c_status'] == '0')
        {
            $response['error']   = true;
            $message .= lang('upload_image_to_publish').'<br>';
            
        }
        if($catalog['c_description'] == '')
        {
            $response['error']   = true;
            $message .= 'Please enter catalog description<br>';
        }
        if($catalog['c_category'] == 0)
        {
            $response['error']   = true;
            $message .= 'Please enter catalog category<br>';
        }
        if($catalog['c_promo'] == '')
        {
            $response['error']   = true;
            $message .= 'Please enter Youtube video URL<br>';
        }
        
        if($response['error'] == true){
            $response['message'] = $message;
            echo json_encode($response);exit;
        }

        $save                   = array();
        $save['id']             = $catalog_id;
        $save['action_by']      = $this->auth->get_current_admin('id');
        $save['updated_date']   = date('Y-m-d H:i:s');
        $save['c_status']       = '1';
        
        $save['action_id']      = $this->actions['activate'];
        $response['message']    = lang('activated');
                     
        $action_label   = $this->actions[$this->actions['activate']]['label'];
        $button_text    = lang('deactivate');

        $action_list    = '<a href="javascript:void(0);" data-target="#publish-course" data-toggle="modal" onclick="changeCatalogStatus(\''.$catalog['id'].'\', \''.lang('are_you_sure_to').' '.lang('deactivate').' '.  lang('catalog').' - '.$catalog['c_title'].' ?\',\''.lang('deactivate').'\',\''.lang('deactivate').'\')">'.lang('deactivate').'</a>';
        if($catalog['c_status'])
        {
            $action_list    = '<a href="javascript:void(0);" data-target="#publish-course" data-toggle="modal" onclick="changeCatalogStatus(\''.$catalog['id'].'\', \''.lang('are_you_sure_to').' '.lang('activate').' '.  lang('catalog').' - '.$catalog['c_title'].' ?\',\''.lang('activate').'\',\''.lang('activate').'\')">'.lang('activate').'</a>';
            $action_label   = $this->actions[$this->actions['deactivate']]['label'];
            $button_text    = lang('activate');
            
            $save['c_status']       = '0';
            $save['action_id']      = $this->actions['deactivate'];
            $response['message']    = lang('deactivated');
        }
        
        //set the database value
        $action_date    = date("d M Y", strtotime($save['updated_date']));
        $action_author  = $this->auth->get_current_admin('us_name');
        $action_author  = ($action_author)?$action_author:'Admin';

        //consider the record is deleted and set the value if record deleted
        $label_class    = 'spn-delete';
        $action_class   = 'label-danger';
        $action         = lang('deleted');
        //case if record is not deleted
        if($catalog['c_deleted'] == 0)
        {
            if($catalog['c_status'] == 0)
            {
                $action_class   = 'label-success';                                                                
                $label_class    = 'spn-active';                                        
                $action         = lang('active');
            }
            else
            {
                $action_class   = 'label-warning';                                                                
                $label_class    = 'spn-inactive';                                        
                $action         = lang('inactive');
            }
        }
        else
        {
            $action_label = $this->actions[$this->actions['delete']]['label'];
            unset($save['action_id']);
            unset($save['updated_date']);
            unset($save['action_by']);
        }
        
        if(!$this->Catalog_model->save($save))
        {
            $response['error']   = true;
            $response['message'] = lang('error_change_status');
        }

        $response['actions']                   = array();
        $response['actions']['action_label']   = $action_label;
        $response['actions']['action']         = $action;
        $response['actions']['action_date']    = $action_date;
        $response['actions']['action_author']  = $action_author;
        $response['actions']['label_class']    = $label_class;
        $response['actions']['action_class']   = $action_class;
        $response['actions']['button_text']    = $button_text;
        $response['actions']['label_text']     = $action_label.' by- '.$action_author.' on '.$action_date;
        
        $response['action_list'] = $action_list;        
        echo json_encode($response);
    }
    
    function restore()
    {
        $response               = array();
        $response['error']      = false;
        $catalog_id             = $this->input->post('catalog_id');
        $catalog                 = $this->Catalog_model->catalog(array('id' => $catalog_id));

        $save                   = array();
        $save['id']             = $catalog_id;
        $save['action_by']      = $this->auth->get_current_admin('id');
        $save['action_id']      = $this->actions['restore'];
        $save['updated_date']   = date('Y-m-d H:i:s');
        $save['c_deleted']     = '0';
        $save['c_status']      = '0';
        
        $response['message']    = lang('restore_catalog_success');
        $action_label           = $this->actions[$this->actions['restore']]['label'];
        
        //set the database value
        $action_date    = date("d M Y", strtotime($save['updated_date']));
        $action_author  = $this->auth->get_current_admin('us_name');
        $action_author  = ($action_author)?$action_author:'Admin';
        
        $button_text    = lang('activate');
        $action_class   = 'label-warning';                                                                
        $label_class    = 'spn-inactive';                                        
        $action         = lang('inactive');
            
        if(!$this->Catalog_model->save($save))
        {
            $response['error']   = true;
            $response['message'] = lang('restore_catalog_failed');
        }

        $response['actions']                   = array();
        $response['actions']['action_label']   = $action_label;
        $response['actions']['action']         = $action;
        $response['actions']['action_date']    = $action_date;
        $response['actions']['action_author']  = $action_author;
        $response['actions']['label_class']    = $label_class;
        $response['actions']['action_class']   = $action_class;
        $response['actions']['button_text']    = $button_text;
        $response['actions']['label_text']     = $action_label.' by- '.$action_author.' on '.$action_date;
        
        //$cb_action = ($catalog['c_status'])?'deactivate':'activate'; 
        $cb_action = 'activate'; 
        $action_list  = '';
        $action_list .= '<li id="status_btn_'.$catalog['id'].'">';
        $action_list .= '     <a onclick="changeCatalogStatus(\''.$catalog['id'].'\', \''.lang('are_you_sure_to').' '.lang($cb_action).' '.  lang('catalog').' - '.$catalog['c_title'].' ?\', \''.$button_text.'\', \''.$button_text.'\')" data-target="#publish-course" data-toggle="modal"  href="javascript:void(0);">'.$button_text.'</a>';
        $action_list .= '</li>';
        $action_list .= '<li>';
        $action_list .= '     <a href="'.admin_url().'catalog_settings/basics/'.$catalog['id'].'">'.lang('settings').'</a>';
        $action_list .= '</li>';
        $action_list .= '<li>';
        $action_list .= '     <a href="javascript:void(0);" onclick="deleteCatalog(\''.$catalog['id'].'\', \''.lang('are_you_sure_to').' '.lang('delete').' '.  lang('catalog').' - '.$catalog['c_title'].' ?\')" data-target="#publish-course" data-toggle="modal">'.lang('delete').'</a>';
        $action_list .= '</li>';
        
        $response['action_list'] = $action_list;        
        $response['status_button'] = '<a onclick="changeCatalogStatus(\''.$catalog['id'].'\', \''.lang('are_you_sure_to').' '.lang($cb_action).' '.  lang('catalog').' - '.$catalog['c_title'].' ?\', \''.$button_text.'\', \''.$button_text.'\')" data-target="#publish-course" data-toggle="modal"  href="javascript:void(0);">'.$button_text.'</a>';        
        echo json_encode($response);        
    }
    
    function delete()
    {
        $response               = array();
        $response['error']      = false;
        $catalog_id             = $this->input->post('catalog_id');
        $catalog                = $this->Catalog_model->catalog(array('id' => $catalog_id));
        if( !$catalog )
        {
            $response['error'] = true;
            $response['message'] = lang('catalog_not_found');
            echo json_encode($response);exit;
        }
        $save                   = array();
        $save['id']             = $catalog_id;
        $save['action_by']      = $this->auth->get_current_admin('id');
        $save['action_id']      = $this->actions['delete'];
        $save['updated_date']   = date('Y-m-d H:i:s');
        $save['c_deleted']     = '1';
        
        $response['message']    = lang('catalog_delete_success');
        $action_label           = $this->actions[$this->actions['delete']]['label'];
        
        //set the database value
        $action_date    = date("d M Y", strtotime($save['updated_date']));
        $action_author  = $this->auth->get_current_admin('us_name');
        $action_author  = ($action_author)?$action_author:'Admin';
        
        
        
        if(!$this->Catalog_model->save($save))
        {
            $response['error']   = true;
            $response['message'] = lang('restore_catalog_failed');
        }

        $response['actions']                   = array();
        $response['actions']['action']         = lang('deleted');
        $response['actions']['action_date']    = $action_date;
        $response['actions']['button_text']    = lang('restore');
        $response['actions']['label_text']     = $action_label.' by- '.$action_author.' on '.$action_date;
        
        $action_list  = '';
        $action_list .= '<li>';
        $action_list .= '     <a id="delete_btn" href="javascript:void(0);" data-target="#publish-course" data-toggle="modal" onclick="restoreCatalog(\''.$catalog['id'].'\', \''.lang('are_you_sure_to').' '.lang('restore').' '.  lang('catalog').' - '.$catalog['c_title'].' ?\')">'.lang('restore').'</a>';
        $action_list .= '</li>';
        
        $response['action_list'] = $action_list;        
        echo json_encode($response);        
    }
    
    
    public function create_catalog()
    {
        $response               = array();
        $response['error']      = false;
        $response['message']    = lang('catalog_created_success');
        $catalog_name           = $this->input->post('catalog_name');

        if(trim($catalog_name) == '')
        {
            $response['error']   = true;
            $response['message'] = lang('catalog_name_required');
            echo json_encode($response);exit;
        }
        $catalog_my = $this->Catalog_model->catalog(array('name' => $catalog_name));
        if( !empty($catalog_my))
        {
            $response['error']   = true;
            $response['message'] = lang('catalog_not_available');
            echo json_encode($response);exit;            
        }
        
        $this->load->helper('text');
        $slug   = $catalog_name;
        $slug	= url_title(convert_accented_characters($slug), 'dash', TRUE);
        $this->load->model('Routes_model');
       
        $slug           = $this->Routes_model->validate_slug($slug);
        $route['slug']	= $slug;	
        $route_id       = $this->Routes_model->save($route);
        
        
        $save                   = array();
        $save['id']             = false;
        $save['c_title']        = $catalog_name;
        $save['c_price']        = $this->input->post('catalog_price');
        $save['c_discount']     = $this->input->post('catalog_discount');
       
        $course_ids             = json_decode($this->input->post('course_ids'));
        $course_ids             = implode(',', $course_ids);
        $save['c_courses']      = $course_ids;
        
        $save['action_id']      = $this->actions['create']; 
        $save['action_by']      = $this->auth->get_current_admin('id');
        $save['c_route_id']     = $route_id;
        $save['c_slug']         = $slug;
        $save['c_account_id']   = $this->config->item('id');
        $catalog_id             = $this->Catalog_model->save($save);		
        $response['id']         = $catalog_id;
        
        $route['id']            = $route_id;
        $route['slug']          = $slug;
        $route['route']         = 'catalog/view/'.$catalog_id;
        $route['r_account_id']	= $this->config->item('id');
        $route['r_item_type']   = 'bundle';
        $route['r_item_id']     = $catalog_id;
        $this->Routes_model->save($route);
        
        echo json_encode($response);exit;
    }
    
    function language()
    {
        $response = array();
        $response['language'] = array();
        $response['language'] = get_instance()->lang->language;
        echo json_encode($response);
    }
}
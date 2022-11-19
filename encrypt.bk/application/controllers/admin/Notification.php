<?php
class Notification extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $redirect                               = $this->auth->is_logged_in(false, false);
        if (!$redirect)
        {
            redirect('login');
        }
        $this->actions                          = $this->config->item('actions');
        $this->load->model(array('Notification_model'));
        $this->lang->load('notification');
        $this->limit                            = 100;
        $this->__loggedInUser                   = $this->auth->get_current_user_session('admin');
        $this->__notification_privilege         = $this->accesspermission->get_permission(array('role_id' => $this->__loggedInUser['role_id'],'module' => 'notification'));
        $this->__access                         = array( "view" => 1, "add" => 2, "edit" => 3, "delete" => 4 );
        if(!in_array($this->__access['view'], $this->__notification_privilege))
        {
            redirect(admin_url());
        }
    }
    
    function index()
    {
        $data                                   = array();
        $breadcrumb                             = array();
        $breadcrumb[]                           = array( 'label' => 'Home', 'link' => site_url('/admin'), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]                           = array( 'label' => lang('manage_notifications'), 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']                     = $breadcrumb;
        $data['title']                          = lang('notifications');
        $data['limit']                          = $this->limit;

        $keyword                                = $this->input->get('keyword');
        $offset                                 = $this->input->get('offset');
        $filters                                = $this->input->get('filter');
        $filter                                 = $filters ? $filters : 'active';
        $limit                                  = $this->limit;
        $page                                   = $offset;
        if($page===NULL||$page<=0)
        {
            $page                               = 1;
        }
        $page                                   = ($page - 1)* $this->limit;
        if($keyword)
        {
            $keyword                            = explode('-', $keyword);
            $keyword                            = implode(' ',$keyword);
        }
        $filter_param                           = array(
                                                        'order_by'     => 'id',
                                                        'direction'    => 'DESC',
                                                        'keyword'      => $keyword,
                                                        'filter'       => $filter,
                                                        'limit'        => $limit,
                                                        'offset'       => $page,
                                                        'count'        => false
                                                        );
        $notifications                          = $this->Notification_model->notifications($filter_param);
        $data['notifications']                  = array();
        if(!empty($notifications))
        {
            $data['success']                    = true;
            $data['message']                    = 'Information Bars Fetched Successfully.';
            $data['notifications']              = $notifications;
        }
        $filter_count_param                     = array(
                                                        'order_by'     => 'id',
                                                        'direction'    => 'DESC',
                                                        'keyword'      => $keyword,
                                                        'filter'       => $filter,
                                                        'count'        => true
                                                        );
        $total_notifications                    = $this->Notification_model->notifications($filter_count_param);
        $data['total_notifications']            = $total_notifications;
        $data['limit']                          = $limit;
        $data['offset']                         = $offset;
        $this->load->view($this->config->item('admin_folder').'/notifications', $data);
    }
    
    /**
     *
     * This will return the Information Bar depends upon filtered and searched values.
     * @param    no param
     * @return   array 
     *
     */
    function filter_notifications()
    {
        $data                                   = array();
        $data['success']                        = false;
        $data['message']                        = 'Error to get Information Bars';

        $keyword                                = $this->input->post('keyword');
        $offset                                 = $this->input->post('offset');
        $filter                                 = $this->input->post('filter');
        $filter                                 = ( $filter )? $filter : 'active';
        $limit                                  = $this->limit;
        $page                                   = $offset;
        if($page===NULL||$page<=0)
        {
            $page                               = 1;
        }
        $page                                   = ($page - 1)* $this->limit;
        if($keyword)
        {
            $keyword                            = explode('-', $keyword);
            $keyword                            = implode(' ',$keyword);
        }
        $filter_param                           = array(
                                                        'order_by'     => 'id',
                                                        'direction'    => 'DESC',
                                                        'keyword'      => $keyword,
                                                        'filter'       => $filter,
                                                        'limit'        => $limit,
                                                        'offset'       => $page,
                                                        'count'        => false
                                                        );
        $notifications                          = $this->Notification_model->notifications($filter_param);
        $data['notifications']                  = array();
        if(!empty($notifications))
        {
            $data['success']                    = true;
            $data['message']                    = 'Information Bars Fetched Successfully.';
            $data['notifications']              = $notifications;
        }
        $filter_count_param                     = array(
                                                        'order_by'     => 'id',
                                                        'direction'    => 'DESC',
                                                        'keyword'      => $keyword,
                                                        'filter'       => $filter,
                                                        'count'        => true
                                                        );
        $total_notifications                    = $this->Notification_model->notifications($filter_count_param);
        $data['total_notifications']            = $total_notifications;
        echo json_encode($data);
    }
    
    function language()
    {
        $response                               = array();
        $response['language']                   = array();
        $response['language']                   = get_instance()->lang->language;
        echo json_encode($response);
    }
    
    /**
     *
     * This will delete the Information Bar.
     * @param    no param
     * @return   response message
     *
     */
    function delete_notification()
    {
        $response                               = array();
        $response['error']                      = false;
        $notification_id                        = $this->input->post('notification_id');
        $notification                           = $this->Notification_model->delete_notification($notification_id);
        if( !$notification )
        {
            $response['error']                  = true;
            $response['message']                = lang('error_delete_notification');
        }
        $response['message']                    = lang('delete_notification_success');
        $this->memcache->delete('notifications');
        echo json_encode($response);        
    }
    
    /**
     *
     * This will delete bulk Information Bars.
     * @param    no param
     * @return   response message
     *
     */
    function delete_notification_bulk()
    {
        $response                               = array();
        $response['error']                      = false;
        $notification_ids                       = json_decode($this->input->post('notifications'));
        $delete_notification                    = $this->Notification_model->delete_notification_bulk($notification_ids);
        if(!$delete_notification)
        {
            $response['error']                  = true;
            $response['message']                = lang('delete_notification_failed');
        }
        $response['message']                    = lang('notification_delete_success');
        $this->memcache->delete('notifications');
        echo json_encode($response); 
    }
    
    /**
     *
     * This will change the status of the Information Bar.
     * @param    no param
     * @return   response message
     *
     */
    function change_notification_status()
    {
        $response                               = array();
        $response['error']                      = false;
        $notification_id                        = $this->input->post('notification_id');
        $status                                 = $this->input->post('status');
        $notification_status_params             = array(
                                                        'id'              => $notification_id,
                                                        'n_status'        => $status,
                                                        'action_id'       => $this->actions['activate'],
                                                        'action_by'       => $this->auth->get_current_admin('id'),
                                                        'updated_date'    => date('Y-m-d H:i:s')
                                                    );

        $notification                            = $this->Notification_model->notification(array('id' => $notification_id, 'select' => 'n_content'));

        if(!$notification['n_content'])
        {
            $response['error']                   = true;
            $response['message']                 = lang('notification_description_null');
            echo json_encode($response);
            return;
        }
        $notification_status                    = $this->Notification_model->save($notification_status_params);
        if(!$notification_status)
        {
            $response['error']                  = true;
            $response['message']                = lang('error_change_status');
        }
        $this->memcache->delete('notifications');
        $response['message']                    = lang('change_status_success');   
        echo json_encode($response);
    }
    
    /**
     *
     * This will change the status of the bulk Information Bars.
     * @param    no param
     * @return   response message
     *
     */
    function change_notification_status_bulk()
    {
        $response                               = array();
        $response['error']                      = false;
        $status                                 = $this->input->post('status');
        $notification_ids                       = json_decode($this->input->post('notifications'));
        $error_count                            = 0;
        $error_msg                              = '';
        $notification_params                    = array();
        if(!empty($notification_ids))
        {
            foreach ($notification_ids as $notification_id) 
            {
                $save                           = array();
                $save['id']                     = $notification_id;
                $save['n_status']               = $status;
                $save['action_by']              = $this->auth->get_current_admin('id');
                $save['updated_date']           = date('Y-m-d H:i:s');
                $save['action_id']              = $this->actions[(($status)?'activate':'deactivate')];
                
                $notification                   = $this->Notification_model->notification(array('id' => $notification_id, 'select' => 'n_title, n_content'));
            
                if(!$notification['n_content'])
                {
                    $error_count++;
                    $error_msg                 .= 'The Information Bar named '.$notification['n_title'].' does not have Description</br>';
                }
                else
                {
                    $notification_params[]      = $save;         
                }
            }
        }
        $bulk_notification_status               = $this->Notification_model->save_bulk($notification_params);
        $response['message']                    = lang('change_status_success');
        if(!$bulk_notification_status)
        {
            $response['error']                  = true;
            $response['message']                = lang('error_change_status');
        }
        if($error_count > 0)
        {
            $response['error']                  = true;
            $action                             = 'Deactivation';
            if($status == 1)
            {
                $action                         = 'Activation';
            }
            $message                            = $action.' failed for following Information Bars:</br>';
            $response['message']                = $message.$error_msg;
        }
        $this->memcache->delete('notifications');
        echo json_encode($response);
    }
    
    /**
     *
     * This will create new Information Bar.
     * @param    no param
     * @return   response message
     *
     */
    public function create_notification()
    {
        $response                               = array();
        $response['error']                      = false;
        $response['message']                    = lang('notification_created_success');
                
        $notification_name                      = $this->input->post('notification_name');
        if($notification_name == '')
        {
            $response['error']                  = true;
            $response['message']                = lang('notification_name_required');
            echo json_encode($response);exit;
        }
        $notification                           = $this->Notification_model->notification(array('name' => $notification_name));
        if(!empty($notification))
        {
            $response['error']                  = true;
            $response['message']                = lang('notification_not_available');
            echo json_encode($response);exit;            
        }
        $this->load->helper('text');
        
        $save                                   = array();
        $save['id']                             = false;
        $save['n_title']                        = $notification_name;
        $save['action_id']                      = $this->actions['create']; 
        $save['action_by']                      = $this->auth->get_current_admin('id');
        $save['n_account_id']                   = $this->config->item('id');
        $save['n_expiry_date']                  = date('Y-m-d H:i:s');
        $notification_id                        = $this->Notification_model->save($save);		
        $response['id']                         = $notification_id;
        echo json_encode($response);exit;
    }
    
    /*public function title_check($str)
    {
        $str = str_replace(array("\"", "&quot;", "<", ">", "{", "}"), "", htmlspecialchars($str));
        $str = ltrim($str," ");
        
        
        if($str=='')
        {
            $this->form_validation->set_message('title_check', 'The {field} is invalid');
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }*/

    /**
     *
     * This will update the Information Bar.
     * @param    int notification_id
     * @return   response message
     *
     */
    function basics( $notification_id = false )
    {
        if(!$notification_id){
            redirect($this->config->item('admin_folder').'/notification');
        }
        $error                                  = 0;
        $data                                   = array();
        $data['title']                          = lang('notifications');
        
        $this->load->helper('form');
        $this->load->library('form_validation');
        
        $data['id']                             = $notification_id;
        $data['n_title']                        = strip_tags($this->input->post('n_title'));
        $data['n_content']                      = $this->input->post('n_content');
        $data['n_expiry_date']                  = $this->input->post('n_expiry_date');
        $data['n_status']                       = $this->input->post('n_status');
        $data['n_notification_bar_type']        = $this->input->post('n_bar_type');
        
        if($notification_id)
        {
            $notification                       = $this->Notification_model->notification(array('id' => $notification_id));
            if(!$notification){
                redirect($this->config->item('admin_folder').'/notification');
            }
            
            $data['id']                         = $notification['id'];
            $data['n_title']                    = $notification['n_title'];
            $data['n_content']                  = $notification['n_content'];
            $data['n_expiry_date']              = $notification['n_expiry_date'];
            $data['n_status']                   = $notification['n_status'];
            $data['n_notification_bar_type']    = $notification['n_notification_bar_type'];
            if($notification['n_title'] != strip_tags($this->input->post('n_title')))
            {
                $notification                   = $this->Notification_model->notification(array('name' => strip_tags($this->input->post('n_title'))));
                if(!empty($notification))
                {
                    $data['error']              = lang('notification_not_available'); 
                    $this->load->view($this->config->item('admin_folder').'/notification_basics',$data);
                    $error                      = 1;           
                }
            }
        }

        $breadcrumb                             = array();
        $breadcrumb[]                           = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]                           = array( 'label' => lang('manage_notifications'), 'link' => admin_url('notification'), 'active' => 'active', 'icon' => '' );
        $breadcrumb[]                           = array( 'label' => $data['n_title'], 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']                     = $breadcrumb;

        $this->form_validation->set_rules('n_title', 'Information Bar Title','required|trim'); 
        $this->form_validation->set_rules('n_content', 'Description','required|trim');
        $this->form_validation->set_rules('n_expiry_date', 'Expiry Date','required|trim'); 
        if($error == 0)
        {
            if ($this->form_validation->run() == FALSE)
            {
                $data['errors']                 = validation_errors(); 
                $this->load->view($this->config->item('admin_folder').'/notification_basics',$data);
            }
            else
            {
                $save['id']                     = $notification_id;
                $save['n_title']                = strip_tags($this->input->post('n_title', true));//ltrim(," ");
                $save['n_content']              = $this->input->post('n_content');
                $save['n_expiry_date']          = date('Y-m-d H:i:s',strtotime($this->input->post('n_expiry_date')));
                $save['n_status']               = $this->input->post('n_status');
                $save['action_by']              = $this->auth->get_current_admin('id');
                $save['n_notification_bar_type']= $this->input->post('n_bar_type');
                $save['action_id']              = $this->actions['update'];
                $save['updated_date']           = date('Y-m-d H:i:s');
                $this->Notification_model->save($save);
                $this->session->set_flashdata('message', lang('notification_basics_saved'));
                $this->memcache->delete('notifications');
                redirect($this->config->item('admin_folder').'/notification/basics/'.$notification_id);
            }
        }
    }
}
?>
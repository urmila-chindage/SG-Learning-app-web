<?php
class Sales_manager extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->__loggedInUser   = $this->auth->get_current_user_session('admin');
        if (empty($this->__loggedInUser))
        {
            redirect('login');
        }
        $this->load->model('Sales_manager_model');
        $this->lang->load('sales_manager');
    }

    function index()
    {
        $data                       = array();
        $data['title']              = lang('sales_management_title');
        $breadcrumb                 = array();
        $breadcrumb[]               = array( 'label' => 'Home', 'link' => admin_url(''), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]               = array( 'label' => lang('manage_sales'), 'link' => admin_url('sales_manager'), 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']         = $breadcrumb;
        $data['items']              = $this->Sales_manager_model->items();
        $this->load->view($this->config->item('admin_folder').'/sales_manager', $data);
    }

    function update_item_position()
    {   
        $response               = array();
        $item_position          = $this->input->post('item_position');
        parse_str($item_position, $item_position);
        if(isset($item_position['item_id']))
        {
            $this->Sales_manager_model->update_item_position($item_position['item_id']);
            $response['error']      = 'false';
            $response['message']    = lang('position_updated_success');
        }
        else
        {
            $response['error']  = 'true';
            $response['message'] = lang('position_updation_failed');
        }
        // $this->memcache->delete('top_courses');
        // $this->memcache->delete('all_courses');
        $this->memcache->delete('popular_courses');
        $this->memcache->delete('featured_courses');
        $this->memcache->delete('all_sorted_course');
        $this->memcache->delete('sales_manager_all_sorted_courses');
        echo json_encode($response);
    }

    function update_item_popular_status()
    {
        $popular_status              = ($this->input->post('popularStatus') == 0 || $this->input->post('popularStatus') == 1)? $this->input->post('popularStatus') : false;
        $item_id                     = (preg_match('/^[0-9]+$/', $this->input->post('itemId')) == true)? $this->input->post('itemId') : false;
        $response                    = array();
        if(($popular_status === false) || ($item_id === false))
        {
            $response['error']       = true;
            $response['message']     = lang('popular_status_updation_failed');
        }
        else 
        {
            if($popular_status == 1)
            {
                $popular_count       = $this->Sales_manager_model->check_popular_item_count($item_id);
            }
            else
            {
                $popular_count       = 0;
            }

            if($popular_count < 5)
            {
                $params                   = array();
                $params['popular_status'] = $popular_status;
                $params['id']             = $item_id;
                $result                   = $this->Sales_manager_model->update_item_featured_popular_status($params);
                $response['error']        = false;
                $response['message']      = lang('popular_status_updation_success');
                $this->memcache->delete('popular_courses');
            }
            elseif($popular_count > 5)
            {
                $response['error']   = true;
                $response['message'] = lang('popular_limit_exceed_message');
            }
            
        }
        echo json_encode($response);   
    }

    function update_item_featured_status()
    {
        $featured_status             = ($this->input->post('featuredStatus') == 0 || $this->input->post('featuredStatus') == 1)? $this->input->post('featuredStatus') : false;
        $item_id                     = (preg_match('/^[0-9]+$/', $this->input->post('itemId')) == true)? $this->input->post('itemId') : false;
        $response                    = array();
        if(($featured_status === false) || ($item_id === false))
        {
            $response['error']       = true;
            $response['message']     = lang('featured_status_updation_failed');
        }
        else
        {
            if($featured_status == 1)
            {
                $featured_count      = $this->Sales_manager_model->check_featured_item_count($item_id);
            }
            else
            {
                $featured_count      = 0;
            }
            
            if($featured_count < 5)
            {
                $params              = array();
                $params['featured_status'] = $featured_status;
                $params['id']        = $item_id;
                $result              = $this->Sales_manager_model->update_item_featured_popular_status($params);
                $response['error']   = false;
                $response['message'] = lang('featured_status_updation_success'); 
                $this->memcache->delete('featured_courses');
            }
            elseif($featured_count > 5)
            {
                $response['error']   = true;
                $response['message'] = lang('featured_limit_exceed_message');
            }
            
        }
        echo json_encode($response);

    }

    function update_item_position_swap()
    {
        $response                = array();
        $item_id                 = (preg_match('/^[0-9]+$/',$this->input->post('itemId')) == true)? $this->input->post('itemId') : false;
        $item_position           = (preg_match('/^[0-9]+$/',$this->input->post('itemPosition')) == true)? $this->input->post('itemPosition') : false;
        $next_item_id            = (preg_match('/^[0-9]+$/',$this->input->post('targetItemId')) == true)? $this->input->post('targetItemId') : false;
        $next_item_position      = (preg_match('/^[0-9]+$/',$this->input->post('targetItemPosition')) == true)? $this->input->post('targetItemPosition') : false;
        if($item_id === false || $item_position === false || $next_item_id === false || $next_item_position === false)
        {
            $response['error']   = true;
            $response['message'] = 'Missing parameters';
            $response['items']   = array();
            echo json_encode($response);
        }
        else
        {
            $items               = array($item_position => $item_id, $next_item_position => $next_item_id);
            $this->Sales_manager_model->update_item_position($items);
            $response['items']   = $this->Sales_manager_model->items();
            $response['error']   = false;
            $response['message'] = '';
            $this->memcache->delete('popular_courses');
            $this->memcache->delete('featured_courses');
            $this->memcache->delete('all_sorted_course');
            $this->memcache->delete('sales_manager_all_sorted_courses');
            echo json_encode($response);
            
        }
        
    }

    function language()
    {
        $response               = array();
        $response['language']   = array();
        $response['language']   = get_instance()->lang->language;
        echo json_encode($response);
    }
}
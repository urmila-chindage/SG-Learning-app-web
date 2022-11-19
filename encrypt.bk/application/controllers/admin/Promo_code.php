<?php
class Promo_code extends CI_Controller
{
    function __construct()
    {
        parent::__construct(); 
        
        $redirect   = $this->auth->is_logged_in(false, false);
        if (!$redirect) 
        {            
            redirect('login');
        }

        $this->load->library('Promocode');
        $this->lang->load('promocode');
        $this->limit                    = 100;
        $this->__loggedInUser           = $this->auth->get_current_user_session('admin');
        $this->__promocode_privilege    = $this->accesspermission->get_permission(array('role_id' => $this->__loggedInUser['role_id'],'module' => 'promo_code'));
        $this->__access                 = array( "view" => 1, "add" => 2, "edit" => 3, "delete" => 4 );
        if(!in_array($this->__access['view'], $this->__promocode_privilege))
        {
            redirect(admin_url());
        }
    }
    
    public function index()
    {
        $data                           = array();
        $breadcrumb                     = array();
        $breadcrumb[]                   = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]                   = array( 'label' => 'Discount Coupons', 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']             = $breadcrumb;
        $data['title']                  = 'Discount Coupon';
        $data['limit']                  = $this->limit;
        
        $keyword                        = $this->input->get('keyword');
        $offset                         = $this->input->get('offset');
        $filter                         = $this->input->get('filter');
        $filter                         = ( $filter )? $filter : 'active';
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
                                                'keyword'      => $keyword,
                                                'filter'       => $filter,
                                                'limit'        => $limit,
                                                'offset'       => $page,
                                                'count'        => false
                                             );
        $promocodes                     = $this->promocode->promocodes($filter_param);
        $data['promocodes']             = array();
        if(!empty($promocodes['body']['promocodes']))
        {
            $data['promocodes']         = $promocodes['body']['promocodes'];
        }
        $filter_count_param             = array(
                                                    'order_by'     => 'id',
                                                    'direction'    => 'DESC',
                                                    'keyword'      => $keyword,
                                                    'filter'       => $filter,
                                                    'count'        => true
                                                );
        $total_promocodes               = $this->promocode->promocodes($filter_count_param);
        $data['total_promocodes']       = $total_promocodes['body']['promocodes'];
        $data['limit']                  = $limit;
        $data['offset']                 = $offset;
        $this->load->view($this->config->item('admin_folder').'/promocodes', $data);
    }

    /**
     *
     * This will add or update the created and generated promocodes.
     * @param    int  $id
     * @return   array incase of add and response message incase of update. 
     *
     */
    public function save_promocode( $id = false )
    {
        $promocode_creation_type      = $this->input->post('promocode_creation_type');
        $promocode_name               = $this->input->post('promocode_name');
        $promocode_description        = $this->input->post('promocode_description');
        $promocode_user_permission    = $this->input->post('promocode_user_permission');
        $promocode_user_limit         = $this->input->post('promocode_user_limit');
        $promocode_discount_type      = $this->input->post('promocode_discount_type');
        $promocode_discount_rate      = $this->input->post('promocode_discount_rate');
        $promocode_count              = $this->input->post('promocode_count');
        $promocode_expiry_date        = $this->input->post('promocode_expiry_date');
        $promocode_created_date       = $this->input->post('promocode_created_date');

        $promocode                    = array(
                                                'promocode_id'              => $id,
                                                'promocode_creation_type'   => $promocode_creation_type,
                                                'promocode_name'            => $promocode_name,
                                                'promocode_description'     => $promocode_description,
                                                'promocode_user_permission' => $promocode_user_permission,
                                                'promocode_user_limit'      => $promocode_user_limit,
                                                'promocode_discount_type'   => $promocode_discount_type,
                                                'promocode_discount_rate'   => $promocode_discount_rate,
                                                'promocode_count'           => $promocode_count,
                                                'promocode_expiry_date'     => $promocode_expiry_date,
                                                'promocode_created_date'    => $promocode_created_date
                                             );
        $new_promocode                = $this->promocode->save_promocode($promocode);
        if($id)
        {
            if($new_promocode['header']['success'] == '1')
            {
                $this->session->set_flashdata('message', 'Discount Coupon details updated successfully');
            }
            else
            {
                $this->session->set_flashdata('error', $new_promocode['header']['message']);
            }
            redirect(admin_url('promo_code/promocode').$id);
        }
        else
        {
            echo json_encode($new_promocode);
        }
    }

    /**
     *
     * This will delete the promocode.
     * @param    no param
     * @return   response message
     *
     */
    public function delete_promocode()
    {
        $promocode_id                 = $this->input->post('promocode_id');
        $remove_promocode             = $this->promocode->delete_promocode($promocode_id);
        echo json_encode($remove_promocode);
    }

    /**
     *
     * This will return the promocode depends upon the parameter.
     * @param    int  $id
     * @return   array 
     *
     */
    public function promocode( $id = false )
    {
        if(!$id)
        {
            redirect(admin_url('promo_code'));
        }
        
        $param                        = array(
                                                'id'      => $id
                                             );
        $promocode                    = $this->promocode->promocode($param);
        if(empty($promocode))
        {
            $this->session->set_flashdata('message', 'Discount Coupon details are not available.');
            redirect(admin_url('promo_code'));
        }
        $data                         = array();
        $breadcrumb                   = array();
        $breadcrumb[]                 = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]                 = array( 'label' => 'Discount Coupons', 'link' => admin_url('promo_code'), 'active' => '', 'icon' => '' );
        $breadcrumb[]                 = array( 'label' => $promocode['body']['promocode']['pc_promo_code_name'], 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']           = $breadcrumb;
        $data['promocode']            = $promocode['body']['promocode'];
        $this->load->view($this->config->item('admin_folder').'/promocode', $data);
    } 
    
    /**
     *
     * This will return the promocode depends upon filtered and searched values.
     * @param    no param
     * @return   array 
     *
     */
    public function filter_promocodes()
    {
        $response                       = array();
        $response['header']             = array();
        $response['body']               = array();
        $response['header']['success']  = true;
        $response['header']['message']  = "Successfully fetched";

        $keyword                      = $this->input->post('keyword');
        $offset                       = $this->input->post('offset');
        $filter                       = $this->input->post('filter');
        $filter                       = ( $filter )? $filter : 'active';
        $limit                        = $this->limit;
        $page                         = $offset;
        if($page===NULL||$page<=0)
        {
            $page                     = 1;
        }
        $page                         = ($page - 1)* $this->limit;
        if($keyword)
        {
            $keyword                  = explode('-', $keyword);
            $keyword                  = implode(' ',$keyword);
        }
        $filter_param                 = array(
                                                'order_by'     => 'id',
                                                'direction'    => 'DESC',
                                                'keyword'      => $keyword,
                                                'filter'       => $filter,
                                                'limit'        => $limit,
                                                'offset'       => $page,
                                                'count'        => false
                                             );
        $promocodes                     = $this->promocode->promocodes($filter_param);
        $response['body']['promocodes'] = $promocodes['body']['promocodes'];

        $filter_count_param             =   array(
                                                    'order_by'     => 'id',
                                                    'direction'    => 'DESC',
                                                    'keyword'      => $keyword,
                                                    'filter'       => $filter,
                                                    'count'        => true
                                                );
        $total_promocodes                         = $this->promocode->promocodes($filter_count_param);
        $response['body']['total_promocodes']     = $total_promocodes['body']['promocodes'];
        $response['body']['limit']                = $limit;
        echo json_encode($response);
    }

    /**
     *
     * This will check the promocode is valid or not.
     * @param    array  $param
     * @return   array
     *
     */
    public function check_valid_promocode( $param = array() )
    {
        $valid_promocode              = $this->promocode->check_valid_promocode($param);
        return $valid_promocode;
    }
    
    /**
     *
     * This will keep records of the promocodes used by the students.
     * @param    array  $param
     * @return   array
     *
     */
    public function record_promocode_usage( $param = array() )
    {
        $promocode_usage_report      = $this->promocode->record_promocode_usage($param);
        return $promocode_usage_report;
    }

    /**
     *
     * This will change the status of the promocode.
     * @param    no param
     * @return   response message
     *
     */
    public function change_promocode_status()
    {
        $promocode_id                = $this->input->post('promocode_id');
        $status                      = $this->input->post('status');
        $promocode_status_params     = array(
                                                'promocode_id'     => $promocode_id,
                                                'status'           => $status
                                            );
        $promocode_status            = $this->promocode->change_promocode_status($promocode_status_params);
        echo json_encode($promocode_status);
    }
    
    /**
     *
     * This will change the status of the bulk promocodes.
     * @param    no param
     * @return   response message
     *
     */
    public function change_promocode_status_bulk()
    {
        $status                      = $this->input->post('status');
        $promocode_ids               = json_decode($this->input->post('promocodes'));
        $promocode_status_params     = array(
                                                'promocode_ids'     => $promocode_ids,
                                                'status'            => $status
                                            );
        $promocode_status            = $this->promocode->change_promocode_status_bulk($promocode_status_params);
        echo json_encode($promocode_status);
    }
    
    /**
     *
     * This will delete bulk promocodes.
     * @param    no param
     * @return   response message
     *
     */
    public function delete_promocode_bulk()
    {
        $promocode_ids                 = json_decode($this->input->post('promocodes'));
        $remove_promocodes             = $this->promocode->delete_promocode_bulk($promocode_ids);
        echo json_encode($remove_promocodes);
    }
    
     /**
     *
     * This will get all the users used promocodes.
     * @param    int promocode_id
     * @return   array
     *
     */
    public function users( $promocode_id = false )
    {
        $promocode_details            = array(
                                                'promocode_id'   => $promocode_id
                                            );
        $usage_report                 = $this->promocode->users($promocode_details);
        $data['promocode_name']       = $usage_report['body']['promocode_usage']['pc_promo_code_name'];
        $data['promocode_id']         = $promocode_id;
        $data['users']                = array();
        if($usage_report['body']['promocode_usage']['pc_user_detail'] != '')
        {
            $data['users']            = json_decode($usage_report['body']['promocode_usage']['pc_user_detail'],true);
        }
        //echo '<pre>'; print_r($usage_report); die;
        $this->load->view($this->config->item('admin_folder').'/promocode_user_report', $data);

    }

    /**
     *
     * This will return the promocodes to export.
     * @param    string
     * @return   array 
     *
     */
    public function export_promocode_report($param = false)
    {
        $param                        = base64_decode($param);
        $param                        = (array)json_decode($param);
        $filter                       = ( $param['filter'] )? $param['filter'] : 'active';
        $keyword                      = $param['keyword'];
        if($keyword)
        {
            $keyword                  = explode('-', $keyword);
            $keyword                  = implode(' ',$keyword);
        }
        $filter_param                 = array(
                                                'order_by'     => 'id',
                                                'direction'    => 'DESC',
                                                'keyword'      => $keyword,
                                                'filter'       => $filter,
                                                'count'        => false
                                            );
        $data['status']               = $filter;
        $data['promocodes']           = array();
        $promocodes                   = $this->promocode->promocodes($filter_param);
        if(!empty($promocodes['body']['promocodes']))
        {
            $data['promocodes']       = $promocodes['body']['promocodes'];
        }
        $this->load->view($this->config->item('admin_folder').'/export_promocode_report', $data);
    }

    /**
     *
     * This will return the promocode users report to export.
     * @param    string
     * @return   array 
     *
     */
    public function export_promocode_user_report($param = false)
    {
        $param                        = base64_decode($param);
        $param                        = (array)json_decode($param);
        $promocode_details            = array(
                                                'promocode_id'   =>  $param['promocodeId']
                                            );
        $usage_report                 = $this->promocode->users($promocode_details);
        $data['promocode_name']       = $usage_report['body']['promocode_usage']['pc_promo_code_name'];
        $data['user_reports']         = array();
        if($usage_report['body']['promocode_usage']['pc_user_detail'] != '')
        {
            $data['user_reports']     = json_decode($usage_report['body']['promocode_usage']['pc_user_detail'],true);
        }
        $this->load->view($this->config->item('admin_folder').'/export_promocode_user_report', $data);
    }
}
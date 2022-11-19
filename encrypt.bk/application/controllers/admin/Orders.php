<?php
class Orders extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->__loggedInUser   = $this->auth->get_current_user_session('admin');
        if (empty($this->__loggedInUser))
        {
            redirect('login');
        }
        $this->lang->load('orders');
        $this->load->model(array('Order_model'));
        $this->order_privilege  = $this->accesspermission->get_permission(array('role_id' => $this->__loggedInUser['role_id'], 'module' => 'orders'));
        $this->__access                 = array( "view" => 1);
        if(!in_array($this->__access['view'], $this->order_privilege))
        {
            redirect(admin_url());
        }
        $this->__limit = 100;
    }

    function index()
    { //print_r($_GET); die;
        $data                       = array();
        $breadcrumb                 = array();
        $breadcrumb[]               = array( 'label' => 'Home', 'link' => admin_url(''), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]               = array( 'label' => lang('manage_orders'), 'link' => admin_url('orders'), 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']         = $breadcrumb;
        $data['title']              = lang('orders');
        $data['show_load_button']   = false;
        $data['limit']              = $this->__limit;
        $offset                     = isset($_GET['offset'])?$_GET['offset']:0;
        
        $order_param                = array();
        $order_param['order_by']    = $this->input->get('startdate') || $this->input->get('enddate') ? 'payment_history.ph_payment_date' : 'payment_history.id';
        $order_param['direction']   = $this->input->get('startdate') ? 'ASC' :'DESC';
        $order_param['count']       = true;
        $filter                     = $this->input->get('filter');
        //print_r($filter); die;
        $order_param['startdate']   = $this->input->get('startdate');
        $order_param['enddate']     = $this->input->get('enddate');
        $order_param['type']        = $this->input->get('type');
        $keyword                    = $this->input->get('keyword');
        $order_param['filter']      = ($filter != null)? $filter : 'completed';
        $order_param['ph_status']   = array('0', '1','2');
        if($keyword != null)
        {
            $keyword_arr            = explode('-', $keyword);
            $keyword                = implode(' ',$keyword_arr);
            $order_param['keyword'] = $keyword;
        }
        
        $data['total_orders']       = $this->Order_model->orders($order_param);
    
        unset($order_param['count']);
        $order_param['limit']       = $this->__limit;
        
        //processing pagination
        $page             = $offset;
        if($page===NULL||$page<=0)
        {
            $page         = 1;
        }
        $page             = ($page - 1)* $this->__limit;
        //end

        $order_param['offset']     = $page;
        // $order_param['select']     = 'id, ph_order_id, ph_user_details, ph_item_name, ph_payment_date, ph_status'; UPDATE `payment_history` SET `ph_item_amount_received`= ph_item_discount_price WHERE `ph_tax_type` = '0' AND `ph_item_discount_price` > 0 AND `ph_item_amount_received` > 0
        $data['orders']            = $this->Order_model->orders($order_param);
        $data['controller']        = 'orders';
        $data['admin']             = $this->__loggedInUser;
        $this->load->view($this->config->item('admin_folder').'/orders', $data);
    }

    function orders_json()
    {
        $data                       = array();
        $data['show_load_button']   = false;
        $order_param                = array();
        
        $limit            = $this->__limit;
        $data['limit']    = $limit;
        $offset           = $this->input->post('offset');
        $page             = $offset;
        if($page===NULL||$page<=0)
        {
            $page         = 1;
        }
        $page             = ($page - 1)* $limit;
///print_r($this->input->post()); die;
        $order_param['order_by']       = $this->input->post('startdate') ? 'payment_history.ph_payment_date' : ($this->input->post('enddate') ? 'payment_history.ph_payment_date' : 'payment_history.id');
        $order_param['direction']      = $this->input->post('startdate') ? 'ASC' :'DESC';
        $order_param['keyword']        = $this->input->post('keyword');
        $order_param['filter']         = $this->input->post('filter');
        $order_param['startdate']      = $this->input->post('startdate');
        $order_param['enddate']        = $this->input->post('enddate');
        $order_param['type']           = $this->input->post('type');
        $order_param['count']          = true;
        $order_param['ph_status']      = array('0', '1');
        //$data['paramsposted']          = $order_param;
        $total_orders                  = $this->Order_model->orders($order_param);
        $data['total_orders']          = $total_orders;       
        unset($order_param['count']);
        $order_param['limit']          = $this->input->post('limit');
        $order_param['offset']         = $page;
        //$order_param['select']         = 'id, ph_order_id, ph_user_details, ph_item_name, ph_payment_date, ph_status';

        if($total_orders > ($this->__limit*$offset))
        {
            $data['show_load_button']  = true;
        }
        //echo '<pre>'; print_r($user_param);die;
        $data['orders']                = $this->Order_model->orders($order_param);
        echo json_encode($data);
    }

    function pdf($order_id=false) 
    {
        if(!$order_id)
        {
            redirect(admin_url('dashboard'));
        }
        $data            = array();
        $data['order']   = $this->Order_model->order(array('order_id' => $order_id));
        //echo '<pre>';print_r($data['order']);die;

        
        if($data['order']['ph_status']=='1')
        {
            $this->load->view($this->config->item('admin_folder').'/invoice_export', $data);
        }
        else
        {
            redirect(admin_url('dashboard'));
        }
    } 

    function language()
    {
        $response               = array();
        $response['language']   = array();
        $response['language']   = get_instance()->lang->language;
        echo json_encode($response);
    }

    public function order_info($id=''){
        if($id == ''){
            redirect(admin_url('orders'));
        }
        $data                       = array();
        $data['id']                 = $id;
        $order_param                = array();
        $order_param['order_id']    = $id;
        $data['orders']             = $this->Order_model->order($order_param);
        // echo "<pre>";print_r($data['orders']);exit;
        $this->load->view($this->config->item('admin_folder').'/order_details',$data);
    }

    public function export_sales_report($param = false)
    {
        $param                                        = base64_decode($param);
        $param                                        = (array)json_decode($param);
        
        $filter_param['filter']                       = $param['filter'] ? $param['filter'] : 'completed';
        $filter_param['startdate']                    = $param['startdate'] ? $param['startdate'] : '';
        $filter_param['enddate']                      = $param['enddate'] ? $param['enddate'] : '';
        $filter_param['type']                         = $param['type'] ? $param['type'] : 'all';
        $filter_param['order_by']                     = $param['startdate'] ? 'payment_history.ph_payment_date' : ($param['startdate'] ? 'payment_history.ph_payment_date' : 'payment_history.id');
        $filter_param['direction']                    = $param['startdate'] ? 'ASC' :'DESC';
        $filter_param['count']                        = false;
        $keyword                                      = $param['keyword'];
        $filter_param['ph_status']                    = array('0', '1');

        if($keyword)
        {
            $keyword                                  = explode('-', $keyword);
            $filter_param['keyword']                  = implode(' ', $keyword);
        }
        //$order['ph_status'] ? lang('complete') : lang('incomplete');
        $data['status']                               = $filter_param['filter'] == 'processing' ? 'Incomplete' :  $filter_param['filter'];
        $data['type']                                 = $filter_param['type'];
        $data['startdate']                            = $filter_param['startdate'];
        $data['enddate']                              = $filter_param['enddate'];
        $data['orders']                               = $this->Order_model->orders($filter_param);
        
        //echo '<pre>'; print_r($data['orders']);die;
        $this->load->view($this->config->item('admin_folder').'/export_sales_report', $data);
    }
}
?>
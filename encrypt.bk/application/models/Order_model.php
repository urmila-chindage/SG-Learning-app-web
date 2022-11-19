<?php 
Class Order_model extends CI_Model
{	
    function __construct()
    {
        parent::__construct();
    }

    public function orders($param = array())
    {
        
        $limit          = isset($param['limit']) ? $param['limit'] : 0;
        $offset         = isset($param['offset']) ? $param['offset'] : 0;
        $order_by       = isset($param['order_by']) ? $param['order_by'] : 'payment_history.id';
        $direction      = isset($param['direction']) ? $param['direction'] : 'DESC';
        $count          = isset($param['count']) ? $param['count'] : false;
        $keyword        = isset($param['keyword']) ? $param['keyword'] : '';
        $startdate      = isset($param['startdate']) ? $param['startdate'] : '';
        $enddate        = isset($param['enddate']) ? date('Y-m-d', strtotime($param['enddate']."+1 days")) : '';
        $filter         = isset($param['filter']) ? $param['filter'] : 0;
        $filter_type    = isset($param['type']) ? $param['type'] : 0;
        $select         = isset($param['select']) ? $param['select']:false;
        $ph_status      = isset($param['ph_status']) ? $param['ph_status']:'';
        if(!$select)
        {
            $select = 'payment_history.*,users.us_name';
        }
        
        $this->db->select($select);
        $this->db->join('users','users.id = payment_history.ph_user_id ','LEFT');
        //$this->db->group_by('payment_history.id'); 
        $this->db->order_by($order_by, $direction);
        if ($limit > 0) 
        {
            $this->db->limit($limit, $offset);
        }
        if ($keyword) 
        {
            $this->db->where('(payment_history.ph_item_name LIKE "%'.$keyword.'%" OR payment_history.ph_order_id LIKE "%'.$keyword.'%" OR users.us_name LIKE "%'.$keyword.'%" OR payment_history.ph_user_details LIKE "%'.$keyword.'%")');
        }
        
        if ($filter) 
        {
            switch ($filter) {
                case 'processing':
                    $this->db->where('payment_history.ph_status', '0');
                    break;
                case 'completed':
                    $this->db->where('payment_history.ph_status', '1');
                    break;
                case 'pending':
                    $this->db->where('payment_history.ph_status', '2');
                    break;
                default:
                    break;
            }
        }

        if($ph_status)
        {
            $this->db->where_in('payment_history.ph_status', $ph_status);
        }

        if ($filter_type) 
        {
            switch ($filter_type) {
                case 'course':
                    $this->db->where('payment_history.ph_item_type', '1');
                    break;
                case 'bundle':
                    $this->db->where('payment_history.ph_item_type', '2');
                    break;
                default:
                    break;
            }
        }

        $this->db->where('payment_history.ph_account_id', config_item('id'));

        if($startdate || $enddate)
        {
            if($startdate && $enddate)
            {
                $this->db->where("payment_history.ph_payment_date BETWEEN '$startdate' AND '$enddate'");
            }
            else if($startdate)
            {
                $this->db->where("payment_history.ph_payment_date >= '$startdate'");
            }
            else if($enddate)
            {
                $this->db->where("payment_history.ph_payment_date <= '$enddate'");
            }
        }
        if ($count) 
        {
            $result = $this->db->count_all_results('payment_history');
        } 
        else 
        {
            $result = $this->db->get('payment_history')->result_array();
        }

        // echo $this->db->last_query();exit;
        return $result; 
    }

    function order($param=array())
    {
        $order_id = isset($param['order_id'])?$param['order_id']:false;
        $return     = array();
        if($order_id)
        {
            $query  =  "SELECT payment_history.*, DATE_FORMAT(payment_history.ph_payment_date, '%d, %b %Y %r') as ph_payment_date ,DATE_FORMAT(payment_history.ph_payment_date, '%d-%m-%Y') as payment_date , course_basics.cb_title 
                        FROM payment_history 
                        LEFT JOIN users ON payment_history.ph_user_id = users.id 
                        LEFT JOIN course_basics ON payment_history.ph_item_id = course_basics.id  
                        WHERE ph_account_id = '".config_item('id')."' AND  payment_history.id=".$order_id;
            $return = $this->db->query($query)->row_array(); 
            
        }
        return $return;
    }

    public function get_pending_payment($params = array())
    {
        $user_id = $params['id'] ? $params['id'] : '';
        $item_id = $params['item_id'] ? $params['item_id'] : '';
        return $this->db->where(array('ph_user_id' => $user_id, 'ph_item_id' => $item_id, 'ph_status' => '0'))->get('payment_history')->row_array();
    }

}
?>
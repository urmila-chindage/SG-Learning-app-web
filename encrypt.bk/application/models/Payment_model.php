<?php
Class Payment_model extends CI_Model
{	
    function __construct()
    {
        parent::__construct();
    }
    
    function is_paid($param=array())
    {  
        if($param['course_id']){
            $this->db->where('ph_item_id', $param['course_id']);
        }
        if($param['user_id']){
            $this->db->where('ph_user_id', $param['user_id']);
        }
        
	return $this->db->get('purchase_history')->row_array();
    }

    function check_subscription($param=array())
    {
        if($param['course_id']){
            $this->db->where('cs_course_id', $param['course_id']);
        }
        if($param['user_id']){
            $this->db->where('cs_user_id', $param['user_id']);
        }

        return $this->db->get('course_subscription')->row_array();
    }

    function save($data)
    {
        // echo "<pre>";print_r($data);exit;
        $data['cs_account_id'] =  config_item('id');
        if($data['id'])
        {
            $this->db->where('id', $data['id']);
            $this->db->where('cs_account_id', config_item('id'));
            $this->db->update('course_subscription', $data);
            return $data['id'];
        }
        else
        {
            $this->db->insert('course_subscription', $data);
            return $this->db->insert_id();
        }
    }

    function save_history($data)
    {
        if(isset($data['id']) && $data['id'])
        {
            $this->db->where('id', $data['id']);
            $this->db->update('payment_history', $data);
            return $data['id'];
        }
        else
        {
            $this->db->insert('payment_history', $data);
        }
        //echo $this->db->last_query();die;
        return $this->db->insert_id();
    }

    public function save_history_bulk($data)
    {
        $order_id       = array();
        foreach($data as $payment_data)
        {
            $this->db->trans_start();
            $this->db->insert('payment_history', $payment_data);
            $order_id[] = $this->db->insert_id();
            $this->db->trans_complete();
        }
        return $order_id;
    }

    function update_history_bulk($data)
    {
        $orders_chunks  = array_chunk($data, 50);
        if(!empty($orders_chunks))
        {
            $this->db->trans_start();
            foreach($orders_chunks as $orders)
            {
                foreach($orders as $order)
                {
                    //$this->db->query("UPDATE payment_history SET ph_order_id = '".$order['ph_order_id']."', ph_item_other_details = '".$order['ph_item_other_details']."' WHERE id = '".$order['id']."';");
                    $this->db->where('id', $order['id']);
                    $this->db->update('payment_history', $order);
                    
                }
            }
            $this->db->trans_complete();
        }
    }

    /* Remove from wishlist after course subscription */
    function remove_wishlist($cw_data)
    {
        $this->db->where('cw_user_id', $cw_data['cs_user_id']);
        $this->db->where('cw_course_id', $cw_data['cs_course_id']);
        $this->db->delete('course_wishlist');
        return $cw_data['cs_course_id'];
    }
}
?>


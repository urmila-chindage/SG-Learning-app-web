<?php 
class Ofapay
{    
    private $CI;
    private $__items = array('course' => 1, 'catalog' => 2);
    private $__item;
    function __construct()
    {
        $this->CI = & get_instance();
        $this->CI->load->database();
        $this->CI->load->model(array('User_model'));
        $this->__item = 'course';
    }
    
    function save_payment($param=array())
    {
        //defining input
        $user_id      = isset($param['user_id'])?$param['user_id']:0;
        $course_id    = isset($param['item_id'])?$param['item_id']:0;
        $this->__item = isset($param['item_type'])?$param['item_type']:$this->__item;
        $amount       = isset($param['amount'])?$param['amount']:0;
        $payment_mode = isset($param['payment_mode'])?$param['payment_mode']:1;//1 => feepal, 2 = free course(standard), 3 => on site

        $ph_item_name      = isset($param['ph_item_name'])?$param['ph_item_name']:'';
        $ph_item_code      = isset($param['ph_item_code'])?$param['ph_item_code']:'';
        $ph_user_details      = isset($param['ph_user_details'])?$param['ph_user_details']:'';

        $course_price   = 0;
        $discount_price = 0;
        $save                                = array();
        if(isset($param['payment_mode']))
        {
            if($param['payment_mode']!=2)
            {
                $course_price   = $course['cb_price'];
                $discount_price = $course['cb_discount'];
            }else{

                $save['ph_status'] = (isset($param['ph_status']))?$param['ph_status']: '2';
                
            }
        }
        //End
        
        $this->CI->db->select('id, cb_price, cb_discount');
        $this->CI->db->where(array('id' => $course_id));
        $course             = $this->CI->db->get('course_basics')->row_array();
        $course['amount']   = $amount;
        
        //save the payment details in datbase
        
        $save['ph_order_id']                 = date('Y').date('m').date('d');//$this->__get_order_id();
        $save['ph_user_id']                  = $param['user_id'];
        $save['ph_item_id']                  = $course_id;
        $save['ph_item_type']                = $this->__items[$this->__item];
        $save['ph_item_amount_received']     = $amount;
        $save['ph_item_base_price']          = $course['cb_price'];
        $save['ph_account_id']               = $this->CI->config->item('id');
        $save['ph_item_discount_price']      = $course['cb_discount'];
        $save['ph_payment_date']             = date('Y-m-d H:i:s');
        $save['ph_item_name']                = $ph_item_name;
        $save['ph_item_code']                = $ph_item_code;
        $save['ph_user_details']             = json_encode($ph_user_details);

        // $shares                     = $this->__get_teachers_share($course);//get the share of teachers
        // $save['ph_teacher_share']   = $shares['total_share'];
        // $save['ph_teacher_share_percentage'] = 100;
        $save['ph_payment_mode']    = $payment_mode;

        //inserting pending payment hostory
        $this->CI->load->model(array("order_model","Payment_model"));
        $pending_order = $this->CI->order_model->get_pending_payment(array('id' => $param['user_id'], 'item_id' => $course_id));
        if(isset($pending_order['id']))
        {
            $save['ph_order_id']           = date('Y').date('m').date('d').$pending_order['id'];
            $save['id']                    = $pending_order['id'];
        }
        else
        {
            $payment_id                    = $this->CI->Payment_model->save_history($save);
            $save['id']                    = $payment_id;
            $save['ph_order_id']           = date('Y').date('m').date('d').$payment_id;
        }

        $payment_id                        = $this->CI->Payment_model->save_history($save);

        // $this->CI->db->insert('payment_history', $save);
        // $payment_id                 = $this->CI->db->insert_id();
        //End
        
        //save the shares
        if(!empty($shares['individual_share']))
        {
            foreach ($shares['individual_share'] as $teacher_id => $teacher_amount)
            {
                if($teacher_amount > 0 )
                {
                    $share                  = array();
                    $share['ps_payment_id'] = $payment_id;
                    $share['ps_account_id'] = $this->CI->config->item('id');
                    $share['ps_teacher_id'] = $teacher_id;
                    $share['ps_amount']     = $teacher_amount;
                    $this->CI->db->insert('payment_shares', $share);
                }
            }
        }
        //End
        //echo '<pre>'; print_r($param);
    }
    
    private function __get_teachers_share($course)
    {
        //echo '<pre>';print_r($course);die;
        $return                      = array();
        $return['individual_share']  = array();
        $return['total_share']       = 0;
        
        $query          = 'SELECT GROUP_CONCAT(course_tutors.ct_tutor_id) as ids FROM course_tutors LEFT JOIN users ON course_tutors.ct_tutor_id = users.id WHERE us_account_id = '.$this->CI->config->item('id').' AND us_role_id = 3 AND ct_course_id='.$course['id'];
        $tutors         = $this->CI->db->query($query)->row_array();
        //$tutors         = $this->CI->db->query('SELECT GROUP_CONCAT(ct_tutor_id) as ids  FROM course_tutors WHERE ct_course_id = '.$course['id'])->row_array();
        $tutors         = isset($tutors['ids'])?$tutors['ids']:'';
        $tutors         = explode(',', $tutors);
        
        $percentage     = isset($course['cb_revenue_share'])?$course['cb_revenue_share']:0;
        $teacher_share  = 0;
        if($percentage)
        {
            $return['total_share'] = $course['amount'] - (($course['amount']/100)*$percentage);
            if(!empty($tutors))
            {
                $individual_share = $return['total_share']/count($tutors);
                foreach ($tutors as $tutor)
                {
                    $return['individual_share'][$tutor] = $individual_share;
                }
            }
        }
        //echo '<pre>'; print_r($return);die;
        return $return;
    }
    private function __get_order_id()
    {
        $query       = 'SELECT COUNT(*) as total_sales FROM payment_history WHERE ph_payment_date >= "'.date('Y-m').'-01" AND ph_payment_date <= "'.date('Y-m').'-31" AND ph_account_id="'.$this->CI->config->item('id').'"';
        $total_sales = $this->CI->db->query($query)->row_array();
        $total_sales = $total_sales['total_sales'];
        //return date('Ymdhis').rand(1111,9999);
        return date('Ymd').($total_sales+1);
    }
}
?>
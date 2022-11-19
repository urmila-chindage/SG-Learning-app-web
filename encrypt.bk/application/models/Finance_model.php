<?php
Class Finance_model extends CI_Model
{	
    function __construct()
    {
        parent::__construct();
    }
    
    function monthly_purchases($param=array())
    {
        $period  = isset($param['period'])?$param['period']:false;
        $count   = isset($param['count'])?$param['count']:false;
        $limit 	 = isset($param['limit'])?$param['limit']:0;
        $offset  = isset($param['offset'])?$param['offset']:0;
        $keyword = isset($param['keyword'])?$param['keyword']:false;
        
        $limit_query = '';
        $where       = array();        
        
        //setting limit
        if($limit>0)
        {
            $limit_query = 'LIMIT '.$offset.', '.$limit;
        }
        //End
        
        //Processing where condition
        $where[] = ' ph_account_id = "'.config_item('id').'" ';
        if($period)
        {
            $where[] = ' ph_payment_date >= "'.$period.'-01" AND ph_payment_date <= "'.$period.'-31" ';
        }
        if($keyword)
        {
            $where[] = ' course_basics.cb_title LIKE "%'.$keyword.'%" ';
        }
        //End
        $where = $this->render_where($where);
        
        $query = 'SELECT ph_item_id, course_basics.cb_title, count(payment_history.id) as total_sales, ROUND(SUM(payment_history.ph_amount), 2) as total_amount 
                    FROM payment_history 
                    LEFT JOIN course_basics ON payment_history.ph_item_id = course_basics.id 
                     '.$where.' 
                    GROUP BY ph_item_id '.$limit_query;

        $result = $this->db->query($query); 
        if($count)
        {
            $result = $result->num_fields();
        }
        else
        {
            $result = $result->result_array();
        }
        return $result;
    }
    
    function monthly_purchases_details($param=array())
    {
        $period     = isset($param['period'])?$param['period']:false;
        $course_id  = isset($param['course_id'])?$param['course_id']:0;
        $count      = isset($param['count'])?$param['count']:false;
        $limit      = isset($param['limit'])?$param['limit']:0;
        $offset     = isset($param['offset'])?$param['offset']:0;

        $limit_query    = '';
        $where          = array();
        
        //setting limit
        if($limit>0)
        {
            $limit_query = 'LIMIT '.$offset.', '.$limit;
        }
        //End
        
        $where[] = ' ph_account_id = "'.config_item('id').'" ';

        if($period)
        {
            $where[] = ' ph_payment_date >= "'.$period.'-01" AND ph_payment_date <= "'.$period.'-31"';            
        }
        if($course_id)
        {
            $where[] = ' payment_history.ph_item_id ="'.$course_id.'" ';            
        }
        $where      = $this->render_where($where);
        $query      = "SELECT payment_history.id, payment_history.ph_order_id, payment_history.ph_user_id, payment_history.ph_payment_date as ph_payment_date_cp, DATE_FORMAT(payment_history.ph_payment_date, '%b %Y') as ph_payment_date , users.us_name, ROUND(payment_history.ph_amount, 2) as total_amount, payment_history.ph_amount, payment_history.ph_course_price, payment_history.ph_course_discount, payment_history.ph_teacher_share, payment_history.ph_teacher_share_percentage 
                        FROM payment_history 
                        LEFT JOIN users ON payment_history.ph_user_id = users.id ".$where.$limit_query;
        $result = $this->db->query($query); 
        if($count)
        {
            $result = $result->num_fields();
        }
        else
        {
            $result = $result->result_array();
        }
        return $result;
    }
    
    function monthly_teachers_shares($param=array())
    {
        $period = isset($param['period'])?$param['period']:false;
        $count  = isset($param['count'])?$param['count']:false;
        $limit 	= isset($param['limit'])?$param['limit']:0;
        $offset = isset($param['offset'])?$param['offset']:0;
        $keyword = isset($param['keyword'])?$param['keyword']:false;
        $teacher_id = isset($param['teacher_id'])?$param['teacher_id']:false;
        
        $limit_query = '';
        $where       = array();        
        
        //setting limit
        if($limit>0)
        {
            $limit_query = 'LIMIT '.$offset.', '.$limit;
        }
        //End
        if($period)
        {
            $where[] = ' payment_shares.ps_payment_date >= "'.$period.'-01" AND payment_shares.ps_payment_date <= "'.$period.'-31"';            
        }
        if($keyword)
        {
            $where[] = ' users.us_name LIKE "%'.$keyword.'%" ';
        }
        if($teacher_id)
        {
            $where[] = ' payment_shares.ps_teacher_id = "'.$teacher_id.'" ';
        }

        $where[] = ' ps_account_id = "'.config_item('id').'" ';
        
        $where       = $this->render_where($where);
        $query       = 'SELECT payment_shares.id, payment_shares.ps_payment_date, payment_shares.ps_teacher_id, users.us_name, ROUND(SUM(ps_amount), 2) AS total_amount, COUNT(ps_teacher_id) AS total_sales
                        FROM payment_shares
                        LEFT JOIN users ON payment_shares.ps_teacher_id = users.id '.$where.'
                        GROUP BY ps_teacher_id '.$limit_query; 
        //echo $query;die;
        $result = $this->db->query($query); 
        if($count)
        {
            $result = $result->num_fields();
        }
        else
        {
            $result = $result->result_array();
        }
        return $result;
    }
    
    function monthly_teachers_shares_details($param=array())
    {
        $period     = isset($param['period'])?$param['period']:false;
        $teacher_id = isset($param['teacher_id'])?$param['teacher_id']:0;
        $count      = isset($param['count'])?$param['count']:false;
        $limit      = isset($param['limit'])?$param['limit']:0;
        $offset     = isset($param['offset'])?$param['offset']:0;
        $share_id   = isset($param['share_id'])?$param['share_id']:0;

        $limit_query = '';
        $where       = array();        

        //setting limit
        if($limit>0)
        {
            $limit_query = 'LIMIT '.$offset.', '.$limit;
        }
        //End
        if($period)
        {
            $where[] = ' payment_shares.ps_payment_date >= "'.$period.'-01" AND payment_shares.ps_payment_date <= "'.$period.'-31"';            
        }
        if($share_id)
        {
            $where[] = ' payment_shares.id = "'.$share_id.'" ';            
        }
        if($teacher_id)
        {
            $where[] = ' payment_shares.ps_teacher_id = "'.$teacher_id.'" ';            
        }

        $where[]     = ' ph_account_id = "'.config_item('id').'" ';
        $where[]     = ' ps_account_id = "'.config_item('id').'" ';
        
        $where       = $this->render_where($where);

        $query = 'SELECT payment_shares.id as share_id, payment_shares.ps_payment_id, payment_shares.ps_amount as teacher_share, users.id as student_id, users.us_name as student_name, payment_history.ph_payment_date as ph_payment_date_cp, DATE_FORMAT(payment_history.ph_payment_date, "%b %Y") as ph_payment_date, payment_history.ph_amount as payed_amount, course_basics.cb_title, payment_history.ph_order_id, payment_history.ph_course_price, payment_history.ph_course_discount, payment_history.ph_teacher_share
                    FROM payment_shares 
                    LEFT JOIN payment_history ON payment_shares.ps_payment_id = payment_history.id 
                    LEFT JOIN course_basics ON payment_history.ph_item_id = course_basics.id 
                    LEFT JOIN users ON payment_history.ph_user_id = users.id '.$where.' '.$limit_query;
        $result = $this->db->query($query); 
        if($count)
        {
            $result = $result->num_fields();
        }
        else
        {
            if($share_id)
            {
                $result = $result->row_array();                            
            }
            else
            {
                $result = $result->result_array();            
            }
        }
        return $result;
    }
    
    function payment_details($param=array())
    {
        $payment_id = isset($param['payment_id'])?$param['payment_id']:false;
        $return = array();
        if($payment_id)
        {
            $query  =  "SELECT payment_history.id, payment_history.ph_order_id, payment_history.ph_user_id, payment_history.ph_payment_date as ph_payment_date_cp, DATE_FORMAT(payment_history.ph_payment_date, '%d, %b %Y %r') as ph_payment_date , users.us_name, ROUND(payment_history.ph_amount, 2) as total_amount, course_basics.cb_title, payment_history.ph_course_price, payment_history.ph_course_discount, payment_history.ph_teacher_share
                        FROM payment_history 
                        LEFT JOIN users ON payment_history.ph_user_id = users.id 
                        LEFT JOIN course_basics ON payment_history.ph_item_id = course_basics.id 
                        WHERE ph_account_id = '".config_item('id')."' AND  payment_history.id=".$payment_id;
            $return = $this->db->query($query)->row_array(); 
            
        }
        return $return;
    }
    
    function payment_shares($param=array())
    {
        $payment_id = isset($param['payment_id'])?$param['payment_id']:false;
        $return = array();
        if($payment_id)
        {
            $query  =  "SELECT payment_shares.ps_teacher_id, users.us_name, payment_shares.ps_amount
                        FROM payment_shares 
                        LEFT JOIN users ON payment_shares.ps_teacher_id = users.id 
                        WHERE ps_account_id = '".config_item('id')."' AND  payment_shares.ps_payment_id=".$payment_id;
            $return = $this->db->query($query)->result_array(); 
        }
        return $return;
    }
    
    private function render_where($where=array())
    {
        $where_query = '';
        if(!empty($where))
        {
            $where_query = 'WHERE '.implode(' AND ', $where);
        }
        return $where_query;
    }
}
?>
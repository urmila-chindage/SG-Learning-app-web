<?php

Class Homepage_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Kolkata');
    }
    function subscribed_course($param=array())
    {
        $status           = isset($param['status'])?$param['status']:'';
        $limit            = isset($param['limit'])?$param['limit']:'';
        
        $this->db->select("course_subscription.cs_course_id, count(course_subscription.cs_course_id) as subscriptions, course_basics.cb_title, course_basics.cb_price, course_basics.cb_discount, course_basics.cb_is_free, course_basics.cb_slug, course_basics.cb_image");
        $this->db->join('course_basics', 'course_subscription.cs_course_id = course_basics.id', 'left');
        //$this->db->join('course_tutors', 'course_tutors.ct_course_id = course_basics.id', 'left');
        
        if($status)
        {
            $this->db->where('course_basics.cb_status', $status);		
        }


        $this->db->where('course_basics.cb_deleted', '0');
        $this->db->where('course_basics.cb_account_id', config_item('id'));
        $this->db->group_by('course_subscription.cs_course_id');
        $this->db->order_by('subscriptions','DESC');
        $this->db->limit($limit);
        
        $result = $this->db->get('course_subscription')->result_array();
        //echo $this->db->last_query();die;
        return $result;
    }

    function home_course($param=array())
    {
        $sub_query      = array();
        $category_ids   = isset($param['category_ids'])?$param['category_ids']:array();
        $language_ids   = isset($param['language_ids'])?$param['language_ids']:array();
        $price_ids      = isset($param['price_ids'])?$param['price_ids']:array();
        $keyword        = isset($param['keyword'])?$param['keyword']:'';
        $rating         = isset($param['rating'])?$param['rating']:false;
        $limit          = isset($param['limit'])?$param['limit']:0;
        $offset         = isset($param['offset'])?$param['offset']:0;
        $order_by       = isset($param['order_by'])?$param['order_by']:'rating';
        $direction      = isset($param['direction'])?$param['direction']:'DESC';
        $total_count    = isset($param['count'])?$param['count']:false;
        
        $where_category = '';
        $where_price    = '';
        $where_language = '';
        
        $rating_query   = ', (SUM(cc_rating)/COUNT(cc_rating)) as rating ';
        $parent_select      = 'course_basics.id AS cs_course_id,course_basics.cb_title, course_basics.cb_price, course_basics.cb_discount, course_basics.cb_is_free, course_basics.cb_slug,"course" as item_type, course_basics.cb_image'.$rating_query;
        
        $query              = 'SELECT '.$parent_select;
        $query             .= 'FROM course_basics '; 
        $query             .= 'LEFT JOIN course_ratings ON course_ratings.cc_course_id = course_basics.id';
        $query             .= ' WHERE course_basics.cb_account_id = "'.config_item('id').'" AND course_basics.cb_status = "1" AND course_basics.cb_deleted = "0"';
        
        if( $keyword )
        {
            $query      .= ' AND course_basics.cb_title LIKE "%'.$keyword.'%"';        
        }
        
        if(!empty($category_ids))
        {
            $where_category = '(';
            $loop_count     = sizeof($category_ids);
            $count          = 1;
            foreach ($category_ids as $category_id)
            {
                $where_category .= ' CONCAT(",", cb_category, ",") LIKE CONCAT("%,", '.$category_id.', ",%") ';
                if($loop_count>$count)
                {
                    $where_category .= ' OR ';
                }
                $count++;
            }
            $where_category .= ')';
        }
        
        
        //echo $where_price;die;
        
        //appending where conditions
        if( $where_category != '' || $where_language != '' || $where_price != '')
        {
            $query .= ' AND (';
            
            
            $implode_where = array();
            if($where_category!='')
            {
                $implode_where[] = $where_category;
            }
            if($where_language!='')
            {
                $implode_where[] = $where_language;
            }
            if($where_price!='')
            {
                $implode_where[] = $where_price;
            }
            if(sizeof($implode_where))
            {
                $query .= implode(' AND ', $implode_where);
            }
            
            
            $query .= ' )';
        }
        
        $query .= ' GROUP BY course_basics.id';
        
        $query          .= ' ORDER BY '.$order_by.' '.$direction.' ';
        if($limit>0)
        {
        $query          .= ' LIMIT '.$offset.', '.$limit;
        }
        
       //echo $query;die;  
        
        $result = $this->db->query($query)->result_array();
        
        return $result;
    }
    
    function assigned_tutors($param=array())
    {
        $course_id = isset($param['course_id'])?$param['course_id']:0;

        $this->db->select('users.us_name');
        $this->db->join('users', 'course_tutors.ct_tutor_id = users.id', 'left');
        $this->db->where('course_tutors.ct_course_id', $course_id);
        $this->db->where('users.us_account_id',config_item('id'));		
        $result  = $this->db->get('course_tutors')->result_array();  
        return $result;        
    }
    
    function count_questions()
    {
        $this->db->select('*');
        $this->db->where('q_deleted','0');
        return $this->db->get('questions')->num_rows();
    }
    
    function count_courses()
    {
        $this->db->select('*');
        $this->db->where('cb_deleted','0');
        $this->db->where('cb_status','1');
        $this->db->where('cb_account_id',config_item('id'));
        return $this->db->get('course_basics')->num_rows();
    }
    
    function count_video_lectures()
    {
        $this->db->select('course_lectures.*');
        $this->db->from('course_lectures');
        $this->db->join('course_basics','course_basics.id = course_lectures.cl_course_id','left');
        $this->db->where('course_lectures.cl_deleted','0');
        $this->db->where('course_lectures.cl_status','1');
        $this->db->where('course_basics.cb_deleted','0');
        $this->db->where('course_basics.cb_status','1');
        $this->db->where('course_basics.cb_account_id',config_item('id'));
        //$this->db->where('cl_account_id',$this->config->item('id'));
        return $this->db->get()->num_rows();
    }
    
    function count_students_enrolled()
    {
        $this->db->select('*');
        $this->db->where('us_role_id', '2');
        $this->db->where('us_account_id',config_item('id'));
        $this->db->where('us_deleted','0');
        $this->db->where('us_status','1');
        return $this->db->get('users')->num_rows();
    }
    
    function notifications($param=array(),$args)
    {
        (isset($args['select']))?$this->db->select($args['select']):$this->db->select('notifications.*, users.us_name  as wa_name_author, web_actions.wa_name, web_actions.wa_code');
        $direction 		= isset($param['direction'])?$param['direction']:'DESC';
        $status 		= isset($param['status'])?$param['status']:'';
        $not_deleted    = isset($param['not_deleted'])?$param['not_deleted']:false;
        $order_by 		= isset($param['order_by'])?$param['order_by']:'id';
        $limit                  = isset($param['limit'])?$param['limit']:'';
        
        $this->db->join('users', 'notifications.action_by = users.id', 'left');
        $this->db->join('web_actions', 'notifications.action_id = web_actions.id', 'left');
        $this->db->where('notifications.n_account_id', config_item('id'));
        $this->db->order_by($order_by, $direction);
        
        if( $status != '' )
        {
            $this->db->where('notifications.n_status', $status); 
        }
        
        if( $not_deleted )
        {
            $this->db->where('notifications.n_deleted', '0'); 
        }
        
        if( $limit )
        {
            $this->db->limit($limit); 
        }
        
        $this->db->where('notifications.n_account_id', config_item('id'));
        
        $result = $this->db->get('notifications')->result_array();
        
        //echo $this->db->last_query();die;
        return $result;
    }

    function get_challenge_details($cid,$param = array()){ 

        date_default_timezone_set('Asia/Kolkata');
        $today = date('Y-m-d H:i:s');
        $select = isset($param['select'])?$param['select']:false;
        if($select){
            $this->db->select($select);
        }else{
            $this->db->select('*');
        }
        $this->db->from('challenge_zone');
        $this->db->where('cz_category', $cid);
        $this->db->where('cz_account_id', config_item('id'));
        $this->db->where('cz_status', '1');
        $this->db->where('cz_deleted', '0');
        $this->db->where('cz_start_date <=', $today);
        $this->db->order_by('cz_start_date', 'DESC');
        $this->db->limit(1);
        $return = $this->db->get()->result_array();
        //echo $this->db->last_query();die;
        return $return;
    }

    function get_banner()
    {
        $this->db->select('banner_name');
        $this->db->where('banner_active', '1');
        $this->db->where('banner_account_id',config_item('id'));
        $result  = $this->db->get('banner')->result_array();
        if($result)
        {
            return $result[0]['banner_name'];
        }
    }
    
    function check_challenge($param = array())
    {
        date_default_timezone_set('Asia/Kolkata');
        $today = date('Y-m-d H:i:s');
        
        $challenge_id 	= isset($param['challenge_id'])?$param['challenge_id']:'';
        $user_id 	= isset($param['user_id'])?$param['user_id']:'';
        
        $this->db->select('*');
        $this->db->where('cza_challenge_zone_id', $challenge_id);
        $this->db->where('cza_user_id', $user_id);
        $challenge = $this->db->get('challenge_zone_attempts');
        $count_challenge_attempt = $challenge->num_rows();
        
        return $count_challenge_attempt;
    }
    function get_latest_challenge_zone($param){
        if(isset($param['uid'])){
            $query = "SELECT challenge_zone.id,(SELECT COUNT(*) FROM challenge_zone_attempts WHERE cza_user_id=".$param['uid']." AND cza_challenge_zone_id=challenge_zone.id) AS status,challenge_zone.cz_category,challenge_zone.cz_start_date,challenge_zone.cz_start_date,challenge_zone.cz_end_date,challenge_zone.cz_title,categories.ct_name FROM challenge_zone LEFT JOIN categories ON challenge_zone.cz_category = categories.id WHERE cz_status = '1' AND challenge_zone.cz_account_id = ".config_item('id')." AND cz_deleted = '0' AND challenge_zone.cz_start_date <= '".date('Y-m-d H:i:s')."' ORDER BY cz_start_date DESC LIMIT 4";
        }else{
            $query = "SELECT challenge_zone.id,challenge_zone.cz_category,challenge_zone.cz_start_date,challenge_zone.cz_start_date,challenge_zone.cz_end_date,challenge_zone.cz_title,categories.ct_name FROM challenge_zone LEFT JOIN categories ON challenge_zone.cz_category = categories.id WHERE cz_status = '1' AND cz_deleted = '0' AND challenge_zone.cz_account_id = ".config_item('id')." AND challenge_zone.cz_start_date <= '".date('Y-m-d H:i:s')."' ORDER BY cz_start_date DESC LIMIT 4";
        }
        //echo $this->db->last_query();die;
        $result = $this->db->query($query);
        return $result->result();
    }
}
?>
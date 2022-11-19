<?php 
Class Wishlist_model extends CI_Model
{	
    function __construct()
    {
        parent::__construct();
    }

    function get_courses($param = array()){
    	
        if(isset($param['id'])){
            $this->db->where('course_basics.id',$param['id']);
        }

    	if(isset($param['select'])){
    		$this->db->select($param['select']);
    	}else{
    		$this->db->select('course_basics.id,course_basics.cb_title,course_basics.cb_position,course_basics.cb_category,course_basics.cb_is_free,course_basics.cb_access_validity,course_basics.cb_preview,course_basics.cb_preview_time,course_basics.cb_has_certificate,course_basics.cb_has_renewal,course_basics.cb_need_percentage,course_basics.cb_price,course_basics.cb_discount,course_basics.cb_validity,course_basics.cb_image,course_basics.cb_language,course_basics.cb_slug,course_basics.cb_revenue_share,course_basics.cb_route_id,course_basics.cb_status,course_basics.cb_approved,course_basics.cb_deleted,course_basics.created_date,course_basics.updated_date');
    	}

    	if(isset($param['deleted'])){
    		$this->db->where('course_basics.cb_deleted',$param['deleted']);
    	}

    	if(isset($param['active'])){
    		$this->db->where('course_basics.cb_status',$param['active']);
    	}

        if(isset($param['keyword'])){
            $this->db->like('course_basics.cb_title',$param['keyword']);
        }

    	if(isset($param['direction'])){
    		$this->db->order_by("course_basics.cb_title",$param['direction']);
    	}else{
    		$this->db->order_by("course_basics.cb_title",'ASC');
    	}

        $this->db->where('cb_account_id',$this->config->item('id'));
    	$this->db->from('course_basics');
    	$this->db->join('course_wishlist','course_basics.id = course_wishlist.cw_course_id','LEFT');
    	$this->db->group_by('course_wishlist.cw_course_id');

    	return $this->db->get()->result_array();

        //echo $this->db->last_query();
    }

    function get_users($param = array()){
        if(isset($param['select'])){
            $this->db->select($param['select']);
        }else{
            $this->db->select('users.id,users.us_name,users.us_email,users.us_image,users.us_phone,users.us_status,users.us_deleted,users.us_degree,users.us_native,users.us_language_speaks,users.created_date,course_wishlist.created_date AS wished_date');
        }
        $this->db->where('us_account_id',$this->config->item('id'));
        if(isset($param['deleted'])){
            $this->db->where('users.us_deleted',$param['deleted']);
        }

        if(isset($param['status'])){
            $this->db->where('users.us_status',$param['status']);
        }

        if(isset($param['course_id'])){
            $this->db->where('course_wishlist.cw_course_id',$param['course_id']);
        }

        if(isset($param['direction'])){
            $this->db->order_by("users.us_name",$param['direction']);
        }else{
            $this->db->order_by("users.us_name",'ASC');
        }

        $this->db->from('users');
        $this->db->join('course_wishlist','users.id = course_wishlist.cw_user_id','LEFT');

        return $this->db->get()->result_array();
    }

    function get_category($param){
        if(isset($param['select'])){
            $this->db->select($param['select']);
        }else{
            $this->db->select('id,ct_name');
        }

        if($param['category_id']){
            $this->db->where('id',$param['category_id']);
        }
        $this->db->where('categories.ct_account_id', config_item('id'));

        return $this->db->get('categories')->row_array();
    }
}
?>

<?php /*
function get_header_categories()
{
    $CI =& get_instance();
    $objects        = array();
    $objects['key'] = 'categories';
    $callback       = 'get_categories';
    $params         = array();
    $categories     = $CI->memcache->get($objects, $callback, $params);
    return $categories;
}


function get_category_pages($id,$parent_id)
{
    $CI =& get_instance();
    $CI->load->database();
   
    $CI->db->where('p_status',1);
    $CI->db->where('p_deleted',0);
    $CI->db->where('p_parent_id',$parent_id);
    $CI->db->where('p_category',$id);
    $CI->db->where('p_account_id', config_item('id'));
    $CI->db->order_by("p_position", "ASC");
    $page_tree = array();
    $pages = $CI->db->get("pages")->result_array();
    if(!empty($pages))
        {
            foreach ($pages as $page) {
                $page['children']       = get_category_pages($page['p_category'],$page['id']);
                $page_tree[$page['id']] = $page;
            }
        }
    return $page_tree;
}

function get_header_links($parent_id)
{
    $CI =& get_instance();
    $CI->load->database();
   
    $CI->db->where('p_status',1);
    $CI->db->where('p_show_page_in',1);
    $CI->db->where('p_deleted', 0);
    $CI->db->where('p_parent_id',$parent_id);
    $CI->db->where('p_account_id', config_item('id'));
    $CI->db->order_by("p_position", "ASC");
    
    $header_tree = array();
    $pages = $CI->db->get("pages")->result_array();
    if(!empty($pages))
        {
            foreach ($pages as $page) {
                $page['children']       = get_header_links($page['id']);
                $header_tree[$page['id']] = $page;
            }
        }
    return $header_tree;
}

function get_footer_links()
{
    $CI =& get_instance();
    $CI->load->database();
   
    $CI->db->where('p_status',1);
    $CI->db->where('p_quick_link',1);
    $CI->db->where('p_deleted', 0);
    $CI->db->where('p_account_id', config_item('id'));
    return $CI->db->get("pages")->result_array();
}

function get_sidebar_notification()
{
    $CI =& get_instance();
    $CI->load->database();
    
    $CI->db->where('n_status',1);
    $CI->db->where('n_title',0);
    $CI->db->where('n_deleted',0);
    $CI->db->where('n_account_id', config_item('id'));
    $CI->db->order_by('id','DESC');
    $CI->db->limit('3');
    return $CI->db->get("notifications")->result_array();
}

function get_sidebar_term()
{
    $CI =& get_instance();
    $CI->load->database();
    
    $CI->db->where('t_status','1');
    $CI->db->where('t_deleted','0');
    $CI->db->where('t_account_id', config_item('id'));
    $CI->db->order_by('created_date','DESC');
    $CI->db->limit('1');
    return $CI->db->get("terms")->row();
}

function get_challenge_zone($cat_id)
{
    date_default_timezone_set('Asia/Kolkata');
    $cz_today = date('Y-m-d H:i:s');
    
    $CI =& get_instance();
    $CI->load->database();
    $CI->db->where('cz_status','1');
    $CI->db->where('cz_deleted','0');
    $CI->db->where('cz_category', $cat_id);
    $CI->db->where('cz_account_id', config_item('id'));
    $CI->db->where('cz_start_date <=', $cz_today);
    $CI->db->order_by('cz_start_date', 'DESC');
    $CI->db->limit(1);
    $return = $CI->db->get('challenge_zone')->result_array();
    //echo $CI->db->last_query(); die;
    return $return;
}

function get_online_lectures()
{
    $CI =& get_instance();
    $CI->load->database();
    $session = $CI->auth->get_current_user_session('user');
    $query   = 'SELECT live_lectures.*,course_lectures.cl_lecture_name FROM live_lectures LEFT JOIN course_lectures ON course_lectures.id =  live_lectures.ll_lecture_id  WHERE ll_is_online IN("1","2") AND ll_course_id IN (SELECT cs_course_id FROM course_subscription WHERE cs_user_id="'.$session['id'].'")';
    $result  = $CI->db->query($query)->result_array();
    return $result;
}

function check_survey_status()
{
    $CI =& get_instance();
    $CI->load->database();
    $query   = 'SELECT * from survey';
    $result  = $CI->db->query($query)->row_array();
    if($result)
    {
        return $result;
    }
}

function get_total_wishes($param = array())
{
    $CI =& get_instance();
    $CI->load->database();
    if(isset($param['course_id'])){
        $query   = 'SELECT * FROM course_wishlist LEFT JOIN users ON course_wishlist.cw_user_id = users.id WHERE course_wishlist.cw_course_id = "'.$param['course_id'].'" AND users.us_deleted = "0" AND users.us_account_id = "'.$CI->config->item('id').'"';
    }else{
        $query   = 'SELECT * FROM course_wishlist LEFT JOIN users ON course_wishlist.cw_user_id = users.id WHERE  users.us_deleted = "0" AND users.us_account_id = "'.$CI->config->item('id').'"';
    }
    
    if(isset($param['count'])){
        $result  = $CI->db->query($query)->num_rows();
    }else{
        $result  = $CI->db->query($query)->row_array();
    }

    if($result)
    {
        return $result;
    }
}

function get_support_chat()
{
    $CI =& get_instance();
    $CI->load->database();
    $query   = 'SELECT * from support_chat WHERE support_chat_status = 1';
    $result  = $CI->db->query($query)->row_array();
    if($result)
    {
        return $result;
    }
}
function get_sidebar_contents($c_id,$class_name){
    $result = array();
    $CI =& get_instance();
    $CI->load->database();
    $session = $CI->auth->get_current_user_session('user');
    if($class_name != 'category'){
        if(isset($session['id'])){
            $query   = 'SELECT course_ratings_cp.*, course_basics.* FROM (SELECT cc_course_id, SUM(cc_rating)/COUNT(cc_course_id) as rating FROM course_ratings course_ratings_cp WHERE cc_course_id IN (SELECT id FROM course_basics WHERE cb_category="'.$c_id.'" AND course_basics.id NOT IN (SELECT cs_course_id FROM course_subscription WHERE cs_user_id = '.$session["id"].' AND cb_category = "'.$c_id.'" )) GROUP BY cc_course_id ORDER BY rating DESC LIMIT 0, 1) course_ratings_cp LEFT JOIN course_basics ON course_ratings_cp.cc_course_id = course_basics.id WHERE course_basics.cb_status="1" AND course_basics.cb_deleted="0"';
        }else{
            $query   = 'SELECT course_ratings_cp.*, course_basics.* FROM (SELECT cc_course_id, SUM(cc_rating)/COUNT(cc_course_id) as rating FROM course_ratings course_ratings_cp WHERE cc_course_id IN (SELECT id FROM course_basics WHERE cb_category="'.$c_id.'") GROUP BY cc_course_id ORDER BY rating DESC LIMIT 0, 1) course_ratings_cp LEFT JOIN course_basics ON course_ratings_cp.cc_course_id = course_basics.id  WHERE course_basics.cb_status="1" AND course_basics.cb_deleted="0"';
        }
        $result['rated_course'] = $CI->db->query($query)->row_array();

        $query = 'SELECT ct_slug FROM categories WHERE id="'.$c_id.'"';
        $result['course_slug'] = $CI->db->query($query)->row();



        if(isset($result['rated_course'])){
            $CI->db->select('*');
            $CI->db->where('cw_course_id', $result['rated_course']['id']);
            $CI->db->where('cw_user_id', $session['id']);
            $result1  = $CI->db->get('course_wishlist');
            
            if($result1->num_rows() > 0){
                $result['rated_course']['whishlist'] = '<span class="heart-icon heart-active" data-key="'.$result['rated_course']['id'].'" onclick="remove_wishlist('.$result['rated_course']['id'].', \''.$session['id'].'\', this)"><i class="icon-heart heart-altr"></i></span>';
            }
            else{
                $result['rated_course']['whishlist'] = '<span class="heart-icon" data-key="'.$result['rated_course']['id'].'" onclick="add_wishlist('.$result['rated_course']['id'].', \''.$session['id'].'\', this)"><i class="icon-heart heart-altr"></i></span>';
            }

            $course_id = $result['rated_course']['id'];
            $CI->db->select('users.id, users.us_name, users.us_image');
            $CI->db->from('course_tutors');
            $CI->db->join('users', 'users.id = course_tutors.ct_tutor_id');
            if($course_id){
                $CI->db->where('course_tutors.ct_course_id', $course_id);
            }
            $result['rated_course']['lectures'] = $CI->db->get()->result_array();
        }


        $result['admin']         = $CI->config->item('us_name');
    }
    
    
    if($class_name != 'challenge_zone'){
        date_default_timezone_set('Asia/Kolkata');
        $cz_today = date('Y-m-d H:i:s');
        $CI->db->select('id,cz_title,cz_category,cz_start_date,cz_end_date,cz_duration,cz_show_categories,cz_account_id,created_date,updated_date');
        $CI->db->where('cz_status','1');
        $CI->db->where('cz_deleted','0');
        $CI->db->where('cz_category', $c_id);
        $CI->db->where('cz_account_id', config_item('id'));
        $CI->db->where('cz_start_date <=', $cz_today);
        $CI->db->order_by('cz_start_date', 'DESC');
        $CI->db->limit(1);
        $result['challenge_zone'] = $CI->db->get('challenge_zone')->result_array();
        
        if(isset($session['id'])&&isset($result['challenge_zone'][0])){
            $query   = "SELECT id FROM challenge_zone_attempts WHERE cza_challenge_zone_id = ".$result['challenge_zone'][0]['id']." AND cza_user_id = ".$session['id'];
        
            $result['challenge_zone_status'] = $CI->db->query($query)->row_array();
        }

    }

    return $result;
    //echo '<pre>'; print_r($result);die;
}
<?php */ ?>
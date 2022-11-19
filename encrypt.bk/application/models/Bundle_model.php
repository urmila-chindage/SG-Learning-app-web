<?php

class Bundle_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    public function bundles($param = array()) 
    {
        $limit          = isset($param['limit']) ? $param['limit'] : 0;
        $offset         = isset($param['offset']) ? $param['offset'] : 0;
        $order_by       = isset($param['order_by']) ? $param['order_by'] : 'id';
        $direction      = isset($param['direction']) ? $param['direction'] : 'DESC';
        $status         = isset($param['status']) ? $param['status'] : false;
        $count          = isset($param['count']) ? $param['count'] : false;
        $not_deleted    = isset($param['not_deleted']) ? $param['not_deleted'] : false;
        $keyword        = isset($param['keyword']) ? $param['keyword'] : '';
        $filter         = isset($param['filter']) ? $param['filter'] : 0;
        $select         = isset($param['select']) ? $param['select'] : 'catalogs.*';
        $category_id    = isset($param['category_id']) ? $param['category_id'] : '';
        $category_id    = ($category_id == 'uncategorised') ? '0' : $category_id;
       
        $this->db->select($select);
        
        $this->db->order_by($order_by, $direction);
        if ($limit > 0) {
            $this->db->limit($limit, $offset);
        }

        if ($not_deleted) {
            $this->db->where('c_deleted', '0');
        }
        if ($category_id != 'all' && $category_id != '') {
            $this->db->where('c_category', $category_id);
        }

        if ($filter) {
            switch ($filter) {
                case 'active':
                    $status = '1';
                    $this->db->where('c_status', $status);
                    $this->db->where('c_deleted', '0');
                    break;
                case 'inactive':
                    $status = '0';
                    $this->db->where('c_status', $status);
                    $this->db->where('c_deleted', '0');
                    break;
                // case 'pending_approval':
                //     $this->db->where('c_deleted', '0');
                //     $status = '2';
                //     break;
                case 'deleted':
                    $this->db->where('c_deleted', '1');
                    break;

                default:
                    break;
            }
        }

        if ($status != '') {
            $this->db->where('c_status', $status);
        }

        if ($keyword) {
            $where  = "(`catalogs`.`c_title` LIKE '%".$keyword."%' OR ";
            $where .= "`catalogs`.`c_code` LIKE '%".$keyword."%')";
            $this->db->where($where);
        }

        $this->db->where('c_account_id', config_item('id'));

        if ($count) {
            $result = $this->db->count_all_results('catalogs');
        } else {
            $result = $this->db->get('catalogs')->result_array();
        }
        // echo $this->db->last_query();exit;
        return $result;
    }

    public function bundle($param = array())
    {
        $bundle_id      = isset($param['bundle_id']) ? $param['bundle_id'] : false;
        $select         = isset($param['select']) ? $param['select'] : '*,"bundle" as item_type';
        $direction      = isset($param['direction']) ? $param['direction'] : 'ASC';
        $not_deleted    = isset($param['not_deleted']) ? $param['not_deleted'] : '0';
        $order_by       = isset($param['order_by']) ? $param['order_by'] : 'c_title';
        $limit          = isset($param['limit']) ? $param['limit'] : 0;
        $offset         = isset($param['offset']) ? $param['offset'] : 0;
        $bundle_id_list = isset($param['bundle_id_list']) ? $param['bundle_id_list'] : false;
        $bundle_id_not  = isset($param['not_in']) ? $param['not_in'] : false;
        $status         = isset($param['status']) ? $param['status'] : false;
        $exclude_id     = isset($param['exclude_id'])?$param['exclude_id']:false;
        $name           = isset($param['name'])?$param['name']:false;
        $code           = isset($param['code'])?$param['code']:false;
        $route          = isset($param['route'])?$param['route']:false;
        
        $this->db->select($select);
        if($bundle_id){

            $this->db->where('catalogs.id', $bundle_id);
        }

        if($exclude_id)
        {
            $this->db->where('catalogs.id !=', $exclude_id); 
        }
        if($not_deleted)
        {
            $this->db->where('catalogs.c_deleted', $not_deleted);
        }
        $this->db->where('catalogs.c_account_id', config_item('id'));
        if($bundle_id_list){

            $this->db->where_in('catalogs.id', $bundle_id_list);
        }
        if($route)
        {
            $this->db->join('routes', 'catalogs.c_route_id = routes.id', 'left');
        }
        if($name){
            $this->db->where('catalogs.c_title', $name);
        }
        if($code){
            $this->db->where('catalogs.c_code', $code);
        }
        if($bundle_id_not){

            $this->db->where_not_in('catalogs.id', $bundle_id_not);
        }
        if ($status) {
            $this->db->where('catalogs.c_status', $status);
        }

        if ($limit > 0) {
            $this->db->limit($limit, $offset);
        }
        $this->db->order_by($order_by, $direction);
        
        $result = $this->db->get('catalogs');
        return $result->row_array();   
    }

    public function save($data ,$param = array())
    {

        $id     = isset($param['id'])?$param['id']:false;
        $update = isset($param['update'])?$param['update']:false;
        $data['c_account_id'] = config_item('id');
        if ($update && !empty($id)) {

            $this->db->where('id', $id);
            $this->db->where('c_account_id', config_item('id'));
            $this->db->update('catalogs', $data);
            return $id;
        } else {
            $this->db->insert('catalogs', $data);
            return $this->db->insert_id();
        }
    }
    public function enrolled($param = array())
    {
        $bundle_id          = isset($param['bundle_id']) ? $param['bundle_id'] : false;
        $count              = isset($param['count']) ? $param['count'] : false;
        $check_completion   = isset($param['check_completion']) ? $param['check_completion'] : false;
        $keyword            = isset($param['keyword']) ? $param['keyword'] : '';
        $filter             = isset($param['filter']) ? $param['filter'] : 0;
        $institute_id       = isset($param['institute_id'])? $param['institute_id'] : '';
        $branch_id          = isset($param['branch_id'])? $param['branch_id'] : '';
        $batch_id           = isset($param['batch_id'])? $param['batch_id'] : '';
        $order_by           = isset($param['order_by']) ? $param['order_by'] : 'users.us_name';
        $direction          = isset($param['direction']) ? $param['direction'] : 'ASC';
        $limit              = isset($param['limit']) ? $param['limit'] : 0;
        $offset             = isset($param['offset']) ? $param['offset'] : 0;
        $select             = isset($param['select'])? $param['select'] : 'bundle_subscription.*, users.us_name, users.us_email, users.us_image, users.us_phone';
        $approved           = isset($param['approved']) ? $param['approved'] : '';

        $this->db->select($select);
        $this->db->join('users', 'bundle_subscription.bs_user_id = users.id', 'left');
        $this->db->where('users.us_account_id', config_item('id'));
        if ($bundle_id) {
            $this->db->where('bundle_subscription.bs_bundle_id', $bundle_id);
        }

        if ($keyword) {
            $this->db->group_start();
            $this->db->or_like(['us_name' => $keyword, 'us_institute_code' => $keyword, 'us_phone' => $keyword]);
            $this->db->group_end();
        }

        if ($approved != '') {
            $this->db->where('bundle_subscription.bs_approved', $approved);
        }
        
        if ($institute_id) {
            $this->db->where('users.us_institute_id', $institute_id);
        }
        if($branch_id) {
            $this->db->where('users.us_branch', $branch_id);
        }
        if($batch_id) {
            $this->db->like('users.us_groups', $batch_id);
        }

        if ($filter) {
            switch ($filter) {
                case 'active':
                    $this->db->where('bundle_subscription.bs_approved', '1');
                    break;
                case 'suspended':
                    
                    $this->db->where_in('bundle_subscription.bs_approved', array('0','2'));
                    break;
                case 'completed':
                    $check_completion = true;
                    $this->db->where('(bundle_subscription.bs_percentage > 95 OR bundle_subscription.bs_completion_registered = "1")');
                    $method = $filter;
                    break;
                case 'incompleted':
                    $check_completion = true;
                    $this->db->where('bundle_subscription.bs_percentage < 95 AND bundle_subscription.bs_completion_registered = "0"');
                    $method = $filter;
                    break;
                case 'not_started':
                    $check_completion = true;
                    $this->db->where('bundle_subscription.bs_percentage = 0');
                    $method = $filter;
                    break;
                default:
                    break;
            }
        }
        //to prevent the deleted resoceds
        $this->db->where("users.id is NOT NULL");

        //end
        $this->db->order_by($order_by, $direction);
        if ($limit > 0) 
        {
            $this->db->limit($limit, $offset);
        }
        //$this->db->group_by('CONCAT(bs_bundle_id, "_", bs_user_id)');
        $result = $this->db->get('bundle_subscription')->result_array();
        // echo $this->db->last_query(); die;
        if ($count) {
            return sizeof($result);
        } else {
            return $result;
        }
    }
    public function save_subscription($data,$condition = array()){

        $update                 = isset($condition['update']) ? $condition['update'] : false;
        $data['bs_account_id']  = config_item('id');
        if($update){

            $user_id    = isset($condition['user_id'])?$condition['user_id']:false;
            $user_ids   = isset($condition['user_ids'])?$condition['user_ids']:false;
            $id         = isset($condition['id'])?$condition['id']:false;
            $bundle_id  = isset($condition['bundle_id'])?$condition['bundle_id']:false;

            if($user_ids){
                $this->db->where_in('bs_user_id', $user_ids);
            }
            if($user_id)
            { 
                $this->db->where('bs_user_id', $user_id);
            }
            if($bundle_id)
            { 
                $this->db->where('bs_bundle_id', $bundle_id);
            }
            if($id)
            { 
                $this->db->where('id', $id);
            }
            $this->db->where('bs_account_id', config_item('id'));
            $this->db->update('bundle_subscription', $data);
            return true;
            
            
         //echo $this->db->last_query();exit;
        }else{
            
            $this->db->insert('bundle_subscription', $data);
            return $this->db->insert_id();
        }
    }
    public function subscription_save_batch($data){

        if(!empty($data))
        {
            for($i = 0; $i < count($data); $i++){
                $data[$i]['bs_account_id']  = config_item('id');
            }
            $this->db->insert_batch('bundle_subscription', $data); 
        }
        return true;
    } 
    public function unsubscribe_user($param = array())
    {
        $user_id    = isset($param['user_id']) ? $param['user_id'] : false;
        $user_ids   = isset($param['user_ids']) ? $param['user_ids'] : false;
        $bundle_id  = isset($param['bundle_id']) ? $param['bundle_id'] : false;

        $this->db->where('bs_account_id', config_item('id'));
        if($bundle_id){
            $this->db->where('bs_bundle_id', $bundle_id);
        }
        if($user_id){
            $this->db->where('bs_user_id', $user_id);
        }
        if($user_ids){
            $this->db->where_in('bs_user_id', $user_ids);
        }
        
        $result = $this->db->delete('bundle_subscription');
        //echo $this->db->last_query();die;
        return $result;
       
    }

    public function save_rating($data)
    {
        $data['cc_account_id']  = config_item('id');
        $this->db->insert('bundle_ratings', $data);
        return true;
    }
    
    function load_reviews($param = array()) {

        $offset      = isset($param['offset']) ? $param['offset'] : 0;
        $limit       = isset($param['limit']) ? $param['limit'] : FALSE;
        $bundle_id   = isset($param['bundle_id']) ? $param['bundle_id'] : FALSE;
        $count       = isset($param['count']) ? $param['count'] : FALSE;
        if($bundle_id) 
        {
            $bundle_id = is_array($bundle_id) ? $bundle_id : array($bundle_id);
            $this->db->where_in('cc_bundle_id', $bundle_id);
        }
        if ($limit) 
        {
            $this->db->limit($limit, $offset);
        }
        $this->db->where('bundle_ratings.cc_account_id', config_item('id'));
        $this->db->order_by("bundle_ratings.created_date", "DESC");
        if($count)
        {
            return $this->db->count_all_results('bundle_ratings');
        }
        else
        {
            return $this->db->get('bundle_ratings')->result_array();
        }
    }

    public function save_review($data)
    {
        $data['cc_account_id'] = config_item('id');

        if (isset($data['id']) && $data['id']) {
            $this->db->where('id', $data['id']);
            $this->db->update('bundle_ratings', $data);
            return $data['id'];
        } else {
            $this->db->insert('bundle_ratings', $data);
            return $this->db->insert_id();
        }
    }

    public function change_review_status($save_param,$filter_param = array())
    {
        $review_id = isset($filter_param['review_id'])?$filter_param['review_id']:false;
        $update    = isset($filter_param['update'])?$filter_param['update']:false;
        $save_param['cc_account_id'] = config_item('id');
        if($update)
        {
            if($review_id)
            {
                $this->db->where('id',$review_id);
            }
            $this->db->where('cc_account_id', config_item('id'));
            $this->db->update('bundle_ratings',$save_param);
            return $review_id;
        }
        else
        {
            $this->db->insert('bundle_ratings',$save_param);
            return $this->db->insert_id();
        }
    }

    public function get_course_review($param = array())
    {
        $cc_bundle_id = isset($param['bundle_id']) ? $param['bundle_id'] : false;
        $order_by = isset($param['order_by']) ? $param['order_by'] : 'id';
        $direction = isset($param['direction']) ? $param['direction'] : 'DESC';
        $review_block = isset($param['block']) ? $param['block'] : '';

        $this->db->select('bundle_ratings.*, users.id as user_id, users.us_name, users.us_image');
        $this->db->join('users', 'bundle_ratings.cc_user_id = users.id', 'left');
        
        if ($review_block) {
            $this->db->where('bundle_ratings.cc_status', '1');
        }
        $this->db->where('cc_account_id', config_item('id'));
        $this->db->where('bundle_ratings.cc_bundle_id', $cc_bundle_id);
        $this->db->order_by($order_by, $direction);

        $result = $this->db->get('bundle_ratings')->result_array();
        //echo $this->db->last_query();die;
        return $result;

    }

    public function course_overall_rating($param = array())
    {
        $bundle_id  = isset($param['bundle_id']) ? $param['bundle_id'] : false;
        $cc_status  = isset($param['cc_status']) ? $param['cc_status'] : false;
        $select     = isset($param['select']) ? $param['select'] : 'cc_rating, AVG(cc_rating) AS rating, COUNT(id) AS ratings';
        $this->db->select($select);
        if ($bundle_id) {
            $this->db->where('cc_bundle_id', $bundle_id);
        }
        if($cc_status)
        {
            $this->db->where('cc_status', $cc_status);
        }
        $this->db->where('cc_rating >', '0');
        $this->db->group_by('cc_rating');
        $this->db->where('cc_account_id', config_item('id'));
        $result = $this->db->get('bundle_ratings')->result_array();
        //echo $this->db->last_query();
        //print_r($result); die;
        return $result; 
    }

    public function get_ratting($param = array())
    {

        $bundle_id  = isset($param['bundle_id']) ? $param['bundle_id'] : false;
        $cc_status = isset($param['cc_status']) ? $param['cc_status'] : false;
        $this->db->select('ROUND(AVG(cc_rating), 1) as avg');
        $this->db->from('bundle_ratings');
        if($cc_status)
        {
            $this->db->where('cc_status', $cc_status);
        }
        $this->db->where('cc_bundle_id', $bundle_id);
        $this->db->where('cc_account_id', config_item('id'));
        $result = $this->db->get()->row_array();
        if ($result['avg'] != '') {
            return $result['avg'];
        } else {
            return 0;
        }
    }

    public function db_get_rating($param = array())
    {
        $offset      = isset($param['offset']) ? $param['offset'] : 0;
        $limit       = isset($param['limit']) ? $param['limit'] : FALSE;
        $bundle_id   = isset($param['bundle_id']) ? $param['bundle_id'] : FALSE;
        $count       = isset($param['count']) ? $param['count'] : FALSE;
        if($bundle_id) 
        {
            $bundle_id = is_array($bundle_id) ? $bundle_id : array($bundle_id);
            $this->db->where_in('cc_bundle_id', $bundle_id);
        }
        if ($limit) 
        {
            $this->db->limit($limit, $offset);
        }
        
        if (isset($param['select'])) {
            $this->db->select($param['select']);
        } else {
            $this->db->select('*');
        }
        
        $cond = array('cc_status' => '1');
        $this->db->where($cond);

        //$this->db->order_by("course_reviews.created_date", "DESC");
        $this->db->where('cc_account_id', config_item('id'));

        if($count)
        {
            return $this->db->count_all_results('bundle_ratings');
        }
        else
        {
            return $this->db->get('bundle_ratings')->result_array();
        }
    }
    
    public function get_user_ratting($param = array())
    {
        $bundle_id = isset($param['bundle_id']) ? $param['bundle_id'] : false;
        $user_id = isset($param['user_id']) ? $param['user_id'] : false;

        $this->db->select('cc_rating');
        $this->db->from('bundle_ratings');
        $this->db->where('cc_bundle_id', $bundle_id);
        $this->db->where('cc_user_id', $user_id);
        $this->db->where('cc_account_id', config_item('id'));
        $result = $this->db->get()->row_array();
        if ($result['cc_rating'] != '') {
            return $result['cc_rating'];
        } else {
            return 0;
        }
    }

    public function fetch_subscribed_user($param = array()){

        $course_user_ids    = isset($param['combinations'])?$param['combinations']:false;
        $select             = "CONCAT(cs_course_id, '_' ,cs_user_id) as sample, id, cs_course_id, cs_user_id";
        
        $this->db->select($select);
        $this->db->from('course_subscription');
        if(!empty($course_user_ids)){
            $this->db->where_in("CONCAT(cs_course_id, '_' ,cs_user_id)",$course_user_ids);
        }
        $this->db->where('cs_account_id', config_item('id'));
        
        $result = $this->db->get();
        return $result->result_array();  
    }

    public function enrolled_bundles($param = array()){
    
        
        $user_id            = isset($param['user_id']) ? $param['user_id'] : false;
        $order_by           = isset($param['order_by']) ? $param['order_by'] : false;
        $expired_bundles    = isset($param['expired_bundles']) ? $param['expired_bundles'] : false;
        $this->db->select('catalogs.id as bundle_id,catalogs.id,catalogs.c_deleted,bundle_subscription.bs_end_date,bundle_subscription.created_date, catalogs.c_title,catalogs.c_slug,bundle_subscription.bs_bundle_id,bundle_subscription.bs_approved,bundle_subscription.bs_course_validity_status,catalogs.c_category,catalogs.c_image,"bundle" as item_type,catalogs.c_courses as bs_bundle_details');
        if ($user_id) {
            $this->db->where('bs_user_id', $user_id);
        }
        $this->db->join('catalogs', 'bundle_subscription.bs_bundle_id = catalogs.id', 'inner');
        $this->db->where('bs_account_id', config_item('id'));
        if($expired_bundles === false)
        {
            $this->db->where('bundle_subscription.bs_end_date >=', date('Y-m-d'));
        }
        if($order_by)
        {
            $this->db->order_by('bundle_subscription.id','DESC');
        }
        //$this->db->group_by('CONCAT(bs_bundle_id, "_", bs_user_id)'); 
        $result = $this->db->get('bundle_subscription')->result_array();
        return $result;
    }
    
    public function subscription_details($param = array()){
    
        $user_id    = isset($param['user_id']) ? $param['user_id'] : false;
        $bundle_id  = isset($param['bundle_id']) ? $param['bundle_id'] : false;
        $this->db->select('catalogs.id as bundle_id,catalogs.id,bundle_subscription.bs_end_date, catalogs.c_title,catalogs.c_slug,bundle_subscription.bs_bundle_id,bundle_subscription.bs_approved,bundle_subscription.bs_course_validity_status,catalogs.c_category,catalogs.c_image,"bundle" as item_type,catalogs.c_courses as bs_bundle_details');
        if ($user_id) {
            $this->db->where('bs_user_id', $user_id);
        }

        if ($bundle_id) {
            $this->db->where('bs_bundle_id', $bundle_id);
        }
        $this->db->where('bs_account_id', config_item('id'));
        $this->db->join('catalogs', 'bundle_subscription.bs_bundle_id = catalogs.id', 'inner');
        $result = $this->db->get('bundle_subscription')->row_array();
        return $result;
    }


    public function bundle_subscription_details($param = array())
    {
        $user_id        = isset($param['user_id']) ? $param['user_id'] : false;
        $bundle_id      = isset($param['bundle_id']) ? $param['bundle_id'] : false;
        $this->db->select('id,bs_user_id,bs_bundle_id,bs_end_date');

        if ($user_id) 
        {
            $this->db->where('bs_user_id', $user_id);
        }

        if ($bundle_id) 
        {
            $this->db->where('bs_bundle_id', $bundle_id);
        }

        $this->db->where('bs_account_id', config_item('id'));

        return $this->db->get('bundle_subscription')->row_array();
    }
    
    public function get_subscription_count($id)
    {

        $this->db->select('*');
        $this->db->from('bundle_subscription');
        $this->db->where('bs_bundle_id', $id);
        $this->db->where('bs_account_id', config_item('id'));
        $result = $this->db->get();
        return $result->num_rows();
    }
    public function short_bundles($param = array())
    {
        $bundle_id  = isset($param['bundle_id'])?$param['bundle_id']:'0';
        $select     = "catalogs.id,catalogs.c_courses, catalogs.c_code, catalogs.c_title, catalogs.c_price, catalogs.c_access_validity, catalogs.c_slug, catalogs.c_status, catalogs.c_deleted, catalogs.c_groups, catalogs.c_discount, catalogs.c_is_free,'bundle' as item_type,catalogs.c_category ,catalogs.c_image,catalogs.c_rating_enabled";
        $this->db->select($select);
        $this->db->from('catalogs');
        $this->db->where('catalogs.c_status','1');
        $this->db->where('catalogs.c_deleted','0');
        $this->db->group_by("catalogs.id");
        $this->db->where('catalogs.id',$bundle_id);
        $this->db->where('catalogs.c_account_id', config_item('id'));
        $result  = $this->db->get()->row_array();
        return $result;
    }
    public function get_all_match($param = array())
    {
        $match      = isset($param['match'])?$param['match']:false;
        $course_id  = isset($param['course_id'])?$param['course_id']:false;
        $select     = isset($param['select'])?$param['select']:"catalogs.id,catalogs.c_courses";
  
        $this->db->select($select);
        $this->db->from('catalogs');
        if($match)
        {
            $query          = '"id":"'.$course_id.'"';
            $this->db->like('c_courses',$query);
        }
        $this->db->where('catalogs.c_account_id', config_item('id'));
        $matched_bundles    = $this->db->get();
        $result             = $matched_bundles->result_array();
        return $result;
    }
    public function get_route($params = array())
    {
        $slug   = isset($params['slug'])?$params['slug']:'';
        $select = isset($params['select'])?$params['select']:'*';
        
        $this->db->where('slug',$slug);
        $this->db->select($select);
        $this->db->from('routes');
        $result     = $this->db->get();
        $response   = $result->row_array();
        return $response;
    }


    /*
        Purpose         : migrate course subscriptions by bundle
        Params          : bundle_id, course_ids
        Optional params : user_ids,removefrombundle
        Written by      : Mujeeb
        Date            : 19-11-2019
        Return          : removefrombundle ? removed user ids : user ids have no subscrition to the course
    */
    public function migrateCourseSubscription($params = array())
    {
        $update_subscription_queries                = array();
        $delete_subscription_queries                = array();
        $new_course_subcribers                      = array();
        //$all_bundle_courses_subscribed = array();
        if(isset($params['bundle_id']) && isset($params['course_ids']))
        {
            $course_ids                             = $params['course_ids'];
                                                    $this->db->select('bs_user_id');
                                                    $this->db->where('bs_bundle_id', $params['bundle_id']);
                                                    $this->db->where('bs_end_date >=', date('Y-m-d')); 
                                                    if(isset($params['user_ids']) && !empty($params['user_ids']))
                                                    {
                                                        $this->db->where_in('bs_user_id', $params['user_ids']);
                                                    }
            $bundle_subscription                    = $this->db->get('bundle_subscription')->result_array();
            if(!empty($bundle_subscription))
            {
                $bundle_subscribers                 = array();
                foreach ($bundle_subscription as $subscribers)
                {
                    array_push($bundle_subscribers,$subscribers['bs_user_id']);
                    $new_course_subcribers[$subscribers['bs_user_id']] = $params['course_ids'];
                }
                
                $course_subscription                = $this->db->select('id, cs_course_id, cs_bundle_id, cs_user_id')->where_in('cs_user_id', $bundle_subscribers)->where_in('cs_course_id', $course_ids)->get('course_subscription')->result_array();
                $subscribedCourse                   = array_column($course_subscription, 'cs_course_id');
                $course_subscribers                 = array();
                $course_subscription_removed        = array();
                // echo '<pre>'; print_r($course_subscription);die;
                if(!empty($course_subscription))
                {
                    $this->db->trans_start();
                    foreach($course_subscription as $c_subscribers)
                    {
                        //$all_bundle_courses_subscribed[]=$c_subscribers['cs_course_id'];
                        if(in_array($c_subscribers['cs_course_id'], $new_course_subcribers[$c_subscribers['cs_user_id']]))
                        {
                            $c_subscriber_key       = array_search($c_subscribers['cs_course_id'], $new_course_subcribers[$c_subscribers['cs_user_id']], true);
                            unset($new_course_subcribers[$c_subscribers['cs_user_id']][$c_subscriber_key]);
                            if(empty($new_course_subcribers[$c_subscribers['cs_user_id']]))
                            {
                                unset($new_course_subcribers[$c_subscribers['cs_user_id']]);
                            }
                        }

                        $bs_bundle_ids              = explode(',', $c_subscribers['cs_bundle_id']);
                        if(isset($params['removefrombundle']) && $params['removefrombundle'] === true)
                        {
                            $key1                   = array_search($params['bundle_id'], $bs_bundle_ids, true);
                            if(isset($bs_bundle_ids[$key1]) && $bs_bundle_ids[$key1] == $params['bundle_id'])
                            {
                                unset($bs_bundle_ids[$key1]);
                            }
                            
                            if(empty($bs_bundle_ids))
                            {
                                if($c_subscribers['id'])
                                {
                                    if($c_subscribers['cs_bundle_id'] == $params['bundle_id'])
                                    {
                                        $query          = "DELETE FROM `course_subscription` WHERE `course_subscription`.`id`  = $c_subscribers[id];";
                                        $this->db->query($query);
                                        //$delete_subscription_queries[] = $query;
                                        array_push($course_subscription_removed,$c_subscribers['cs_user_id']);
                                    }
                                }
                            }
                            else
                            {
                                $cs_bundle_id          = implode(',',$bs_bundle_ids);
                                $query                 = "UPDATE course_subscription SET cs_bundle_id = '".$cs_bundle_id."' WHERE course_subscription.id = $c_subscribers[id];";
                                $this->db->query($query);
                                $update_subscription_queries[] = $query;
                            }
                        }
                        else
                        {

                            if(!in_array($params['bundle_id'],$bs_bundle_ids))
                            {
                                $cs_bundle_id          = $c_subscribers['cs_bundle_id'].','.$params['bundle_id'];
                                $query                 = "UPDATE course_subscription SET cs_bundle_id = '".$cs_bundle_id."' WHERE course_subscription.id = $c_subscribers[id];";
                                $update_subscription_queries[] = $query;
                                $this->db->query($query);
                            }
                        }
                      
                        $this->memcache->delete('my_bundle_subscription_'.$params['bundle_id'].'_'.$c_subscribers['cs_user_id']);
                        $this->memcache->delete('enrolled_item_ids_'.$c_subscribers['cs_user_id']);
                        $this->memcache->delete('bundle_enrolled_'.$c_subscribers['cs_user_id']);
                        $this->memcache->delete('mobile_enrolled_'.$c_subscribers['cs_user_id']);
                    }
                    $this->db->trans_complete(); 
                }
                else
                {
                    //echo 'This bundle has no subscriptions - id = '.$params['bundle_id'].'<br />';
                    foreach($bundle_subscription as $bundle_subscribers)
                    {   
                        $new_course_subcribers[$bundle_subscribers['bs_user_id']] = $course_ids;
                    }

                }

                /*if(!empty($update_subscription_queries))
                {
                    $update_subscription_queries        = array_chunk($update_subscription_queries, 100);
                    if(!empty($update_subscription_queries))
                    {
                        foreach($update_subscription_queries as $update_query_object)
                        {
                            $update_query = implode(';', $update_query_object);
                            //echo '<pre>'.$update_query.'<br />';
                            $this->db->query($update_query);
                            //echo '<pre>'.$this->db->last_query().'<br />';
                        }
                    }
                }

                if(!empty($delete_subscription_queries))
                {
                    $delete_subscription_queries        = array_chunk($delete_subscription_queries, 100);
                    if(!empty($delete_subscription_queries))
                    {
                        foreach($delete_subscription_queries as $delete_query_object)
                        {
                            $delete_query = implode(';', $delete_query_object);
                            //echo '<pre>'.$delete_query.'<br />';
                            $this->db->query($delete_query);
                            //echo '<pre>'.$this->db->last_query().'<br />';
                        }
                    }
                }*/
                
                
                //echo '<pre>all_bundle_courses_subscribed';print_r($all_bundle_courses_subscribed);
                /*echo '<pre>update_subscription_queries';print_r($update_subscription_queries);
                echo '<pre>delete_subscription_queries';print_r($delete_subscription_queries);
                echo 'Bundle id = '. $params['bundle_id'].'<br />';
                echo '<pre>new_course_subcribers';      print_r($new_course_subcribers);
                echo '<pre>course_subscription_removed';print_r($course_subscription_removed);*/
                
                //die('-----------');
                if(isset($params['removefrombundle']) && $params['removefrombundle'] == true)
                {
                    return array_unique($course_subscription_removed);
                }

                if(!empty($new_course_subcribers))
                {
                    return $new_course_subcribers;
                }
                return false;
            }
            return false;
        }
        return false;
    }

    public function subscriptions($params = array())
    {
        $bundle_id  = isset($params['bundle_id']) ? $params['bundle_id'] : false;
        $this->db->select('id, bs_user_id');
        
        if ($bundle_id) 
        {
            $this->db->where('bs_bundle_id', $bundle_id);
        }
        $this->db->where('bs_account_id', config_item('id'));
        $result = $this->db->get('bundle_subscription')->result_array();
        return $result;
    }
    
}
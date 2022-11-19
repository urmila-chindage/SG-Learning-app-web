<?php

Class Announcement_Model extends CI_Model {

    
    function __construct() {
        parent::__construct();
    }

    function load_announcements($param = array()) {

        $offset            = isset($param['offset']) ? $param['offset'] : 0;
        $limit             = isset($param['limit']) ? $param['limit'] : FALSE;
        $course_id         = isset($param['course_id']) ? $param['course_id'] : FALSE;
        $subscription_date = isset($param['subscription_date']) ? $param['subscription_date'] : '';
    
        if ($course_id) {

            $course_id = is_array($course_id) ? $course_id : array($course_id);
            $this->db->where_in('an_course_id', $course_id);
        }
        
        if ($subscription_date) { 

            $subscription_date = is_array($subscription_date) ? $subscription_date : array($subscription_date);
            for ($i = 0; $i < count($subscription_date); $i++) {
                $this->db->where('announcement.an_created_date >= ', $subscription_date[$i]);
            }
        }
        if ($limit) {
            $this->db->limit($limit, $offset);
        }

        $this->db->select('announcement.id, announcement.an_title,announcement.an_created_date, announcement.an_description,announcement.an_date,announcement.an_course_id,users.us_name,announcement.an_sent_to,announcement.an_batch_ids,announcement.an_institution_ids,users.us_image');
        $this->db->join('users', 'users.id = announcement.an_created_by');
        $this->db->order_by("announcement.an_created_date", "desc");
        $query  = $this->db->get('announcement');
//        echo $this->db->last_query();
//        exit;
        $return = $query->result_array();

        return $return;
    }

    function ann_institution($params = array()) {
        //$this->db->where('us_role_id', 8);
        //$this->db->select('institute_basics.id, institute_basics.ib_name as us_name,institute_basics.ib_institute_code as us_institute_code');
        $this->db->select('institute_basics.id');
        $this->db->order_by("institute_basics.id", "asc");
        $return = $this->db->get('institute_basics')->result_array();
        return $return;
    }

    function ann_institution_by_id($params = array()) {
        $this->db->where_in('id', $params['id']);
        $this->db->select('institute_basics.id, institute_basics.ib_name as us_name,institute_basics.ib_institute_code as us_institute_code');
        $this->db->limit(1);
        $return = $this->db->get('institute_basics');
        $row    = $return->row_array();
        return $row;
    }

    function save($data) {

        if (!empty($data['id'])) {

            $this->db->where('id', $data['id']);
            $this->db->update('announcement', $data);
            return $data['id'];
        } else {
            $this->db->insert('announcement', $data);
            return $this->db->insert_id();
        }
    }

    function deleteAnnouncement($param=array()) {

        $id     = isset($param['id'])?$param['id']:false;
        $result = false;
        if ($id) {

            $this->db->where('id', $id);
            $result = $this->db->delete('announcement');
        }
        return $result;
    }

    public function subscription_details($param) {

        $user_id   = isset($param['user_id']) ? $param['user_id'] : FALSE;
        $course_id = isset($param['course_id']) ? $param['course_id'] : FALSE;

        $this->db->select('cs_course_id,cs_subscription_date');
        $this->db->from('course_subscription');

        if ($user_id) {
            $this->db->where(array('cs_user_id' => $user_id));
        }
        if ($course_id) {
            $this->db->where(array('cs_course_id' => $course_id));
            $this->db->limit(1);
        }

        $query  = $this->db->get();
        $result = $query->result_array();
        return $result;
    }
    public function fetch_user_details($param){

        $course_id  = isset($param['course_id']) ? $param['course_id'] : FALSE;
        $sdate      = isset($param['created']) ? $param['created'] : FALSE;
        
        $this->db->select('a.us_email,a.us_institute_id,a.us_groups');
        $this->db->from('users as a');
        $this->db->join('course_subscription as b','b.cs_user_id=a.id');
        $this->db->where('b.cs_course_id',$course_id);
        $this->db->where('b.cs_subscription_date <= ', $sdate);
        $this->db->where('a.us_email_verified','1');
        $this->db->where('a.us_deleted','0');

        $result=$this->db->get();
        return $result->result_array();
    }


    function load_reviews($param = array()) {

        $offset      = isset($param['offset']) ? $param['offset'] : 0;
        $limit       = isset($param['limit']) ? $param['limit'] : FALSE;
        $course_id   = isset($param['course_id']) ? $param['course_id'] : FALSE;
        $count       = isset($param['count']) ? $param['count'] : FALSE;
        if($course_id) 
        {
            $course_id = is_array($course_id) ? $course_id : array($course_id);
            $this->db->where_in('cc_course_id', $course_id);
        }
        if ($limit) 
        {
            $this->db->limit($limit, $offset);
        }
        $this->db->select('id, cc_course_id, cc_user_id, cc_user_name, cc_reviews, cc_admin_reply, created_date, cc_status, cc_rating, cc_user_image');
        $this->db->order_by("created_date", "DESC");
        if($count)
        {
            return $this->db->count_all_results('course_ratings');
        }
        else
        {
            return $this->db->get('course_ratings')->result_array();
        }
    }
    public function change_review_status($save_param,$filter_param = array())
    {
        $review_id = isset($filter_param['review_id'])?$filter_param['review_id']:false;
        $update    = isset($filter_param['update'])?$filter_param['update']:false;
        if($update)
        {
            if($review_id)
            {
                $this->db->where('id',$review_id);
            }
            $this->db->update('course_ratings',$save_param);
            return $review_id;
        }
        else
        {
            $this->db->insert('course_ratings',$save_param);
            return $this->db->insert_id();
        }
    }
}

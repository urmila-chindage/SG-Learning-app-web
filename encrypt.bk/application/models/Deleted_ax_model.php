<?php
Class Deleted_ax_model extends CI_Model
{	
    function __construct()
    {
        parent::__construct();
    }

    function cms_pages($param = array()){

        $this->db->select('id,p_route_id');
        $this->db->where('p_deleted', '1');
        $this->db->where('p_account_id', $this->config->item('id'));
        $this->db->where("DATE_FORMAT(updated_date, '%Y-%m-%d') <= DATE_FORMAT(NOW() + INTERVAL -30 DAY, '%Y-%m-%d') ");            
        return $this->db->get('pages')->result_array();
    }

    function recent_pages($param = array()){

        // $this->db->select('id');
        // $this->db->where('rvp_page_id',$param['page_id']);
        // return $this->db->get('recently_view_pages')->result_array();
        return array();
    }

    function terms($param = array()){

        $this->db->select('id,t_route_id');
        $this->db->where('t_deleted', '1');
        $this->db->where('t_account_id', $this->config->item('id'));
        $this->db->where("DATE_FORMAT(updated_date, '%Y-%m-%d') <= DATE_FORMAT(NOW() + INTERVAL -30 DAY, '%Y-%m-%d') ");            
        return $this->db->get('terms')->result_array();
    }

    function recent_terms($param = array()){

        $this->db->select('id');
        $this->db->where('rvt_term_id',$param['term_id']);
        return $this->db->get('recently_view_terms')->result_array();
    }

    function cms_notifications($param = array()){
        $this->db->select('id,n_route_id');
        $this->db->where('n_deleted', '1');
        $this->db->where('n_account_id', $this->config->item('id'));
        $this->db->where("DATE_FORMAT(updated_date, '%Y-%m-%d') <= DATE_FORMAT(NOW() + INTERVAL -30 DAY, '%Y-%m-%d') ");            

        return $this->db->get('notifications')->result_array();
    }

    function users($param = array()){
        $this->db->select('id');
        $this->db->where('us_deleted', '1');
        $this->db->where('us_account_id', $this->config->item('id'));
        $this->db->where("DATE_FORMAT(updated_date, '%Y-%m-%d') <= DATE_FORMAT(NOW() + INTERVAL -30 DAY, '%Y-%m-%d') ");            

        return $this->db->get('users')->result_array();
    }

    function subscribed_courses($param = array()){
        $this->db->select('id');
        $this->db->where('cs_user_id',$param['user_id']);
        return $this->db->get('course_subscription')->result_array();

    }

    function challenge_zones(){
        $this->db->select('id');
        $this->db->where('cz_deleted', '1');
        $this->db->where('cz_account_id', $this->config->item('id'));
        $this->db->where("DATE_FORMAT(updated_date, '%Y-%m-%d') <= DATE_FORMAT(NOW() + INTERVAL -30 DAY, '%Y-%m-%d') ");            

        return $this->db->get('challenge_zone')->result_array();   
    }

    function challenge_questions($param = array()){
        $this->db->select('id');
        $this->db->where('czq_challenge_zone_id', $param['challenge_id']);

        return $this->db->get('challenge_zone_questions')->result_array();      
    }

    function challenge_attempts($param = array()){
        $this->db->select('id');
        if(isset($param['challenge_id'])){
            $this->db->where('cza_challenge_zone_id', $param['challenge_id']);
        }
        if(isset($param['user_id'])){
            $this->db->where('cza_user_id', $param['user_id']);
        }

        return $this->db->get('challenge_zone_attempts')->result_array();      
    }

    function challenge_attempt_report($param = array()){
        $this->db->select('id');
        if(isset($param['attempt_id'])){
            $this->db->where('czr_attempt_id',$param['attempt_id']);
        }
        if(isset($param['user_id'])){
            $this->db->where('czr_user_id',$param['user_id']);
        }
        return $this->db->get('challenge_zone_report')->result_array();      
    }
    function route_id($param = array()){
        $this->db->select('*');
        $this->db->where('id',$param['route_id']);
        $this->db->where('r_account_id',$this->config->item('id'));
        return $this->db->get('routes')->row_array();
    }

    function expert_lectures(){
        $this->db->select('id');
        $this->db->where('el_deleted', '1');
        $this->db->where('el_account_id', $this->config->item('id'));
        $this->db->where("DATE_FORMAT(updated_date, '%Y-%m-%d') <= DATE_FORMAT(NOW() + INTERVAL -30 DAY, '%Y-%m-%d') ");            

        return $this->db->get('expert_lectures')->result_array(); 
    }

    function daily_news(){
        $this->db->select('id');
        $this->db->where('dnb_deleted', '1');
        $this->db->where('dnb_account_id', $this->config->item('id'));
        $this->db->where("DATE_FORMAT(updated_date, '%Y-%m-%d') <= DATE_FORMAT(NOW() + INTERVAL -30 DAY, '%Y-%m-%d') ");            

        return $this->db->get('daily_news_bulletin')->result_array(); 
    }

}
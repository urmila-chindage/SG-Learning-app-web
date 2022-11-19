<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends CI_Controller {

	function __construct()
    {
        parent::__construct();
        $this->load->model(array('Search_model','Course_model'));
        $this->lang->load('search');
    }

    function index_old(){


    	$query 		= $this->input->get('query');
    	$query 		= urldecode($query);
    	$query_arr  = explode(' ', $query);
    	if(sizeof($query_arr) > 1)
    	{
	    	$query_arr[] = $query;
    	}
    	$query_arr = array_reverse($query_arr);

    	$data 		= array();
    	$session    = $this->auth->get_current_user_session('user');
        $data['session'] = $session;
    	$courses_arr = array();

        $courses_arr = $this->Search_model->search_course($query_arr);
  		
    	$ratting = array();
    	foreach ($courses_arr as $key => $value) {
    		$courses_arr[$key]['ratting']				= $this->Course_model->get_ratting(array('course_id' => $value['id']));
    		$temp_arr = array();
    		array_push($temp_arr, 'rate_div_'.$key);
    		array_push($temp_arr, $courses_arr[$key]['ratting']);
    		array_push($ratting, $temp_arr);
    		$courses_arr[$key]['course_tutors']        = $this->Course_model->get_course_tutors(array('course_id' => $value['id']));
    		
                
                $wish_stat = $this->Course_model->get_whish_stat($value['id'], $session['id']);

                if($wish_stat == 1){
                    $courses_arr[$key]['wish_stat'] = '<span class="heart-icon heart-active" data-key="'.$value['id'].'" onclick="remove_wishlist('.$value['id'].', \''.$session['id'].'\', this)"><i class="icon-heart heart-altr"></i></span>';
                }
                else if($wish_stat == 0){
                    $courses_arr[$key]['wish_stat'] = '<span class="heart-icon" data-key="'.$value['id'].'" onclick="add_wishlist('.$value['id'].', \''.$session['id'].'\', this)"><i class="icon-heart heart-altr"></i></span>';
                }
                else if($wish_stat == 2){
                    $courses_arr[$key]['wish_stat'] = '';
                }
    	}

    	$data['courses']  = $courses_arr;
    	$data['rattings'] = json_encode($ratting);
    	$data['query']    = $query;
    	$data['admin']    = $this->config->item('acct_name');
    	//echo '<pre>';print_r($data);die();
    	$this->load->view($this->config->item('theme').'/search', $data);
    }
    
    function index(){


    	$query 		= $this->input->get('query');
        $search_keyword = $this->input->get('query');
        $query          = str_replace("'", "\'", $query);
    	$query 		= urldecode($query);
    	$query_arr      = explode(' ', $query);
        
    	if(sizeof($query_arr) > 1)
    	{
	    	$query_arr[] = $query;
    	}
    	$query_arr = array_reverse($query_arr);
        //echo '<pre>'; print_r($query_arr);die;
    	$data 		= array();
    	$session    = $this->auth->get_current_user_session('user');
        $data['session'] = $session;
    	$courses_arr = array();
        
        $languages          = $this->Course_model->languages(array('restrict_by_tutor_course' => true));
        if (!empty($languages)) {
            foreach ($languages as $language) {
                $data['languages'][$language['id']] = $language;
            }
        }

        $courses_arr = $this->Search_model->search_course($query_arr);
  		
    	$ratting = array();
    	foreach ($courses_arr as $key => $value) {
    		$courses_arr[$key]['ratting']			= $this->Course_model->get_ratting(array('course_id' => $value['id']));
    		$temp_arr = array();
    		array_push($temp_arr, 'rate_div_'.$key);
    		array_push($temp_arr, $courses_arr[$key]['ratting']);
    		array_push($ratting, $temp_arr);
    		$courses_arr[$key]['course_tutors']        = $this->Course_model->get_course_tutors(array('course_id' => $value['id']));
    		
                
                $wish_stat = $this->Course_model->get_whish_stat($value['id'], $session['id']);
                
                //echo $wish_stat;
                if($wish_stat == 1){
                    $courses_arr[$key]['wish_stat'] = '<span class="heart-icon heart-active" data-key="'.$value['id'].'" onclick="remove_wishlist('.$value['id'].', \''.$session['id'].'\', this)"><i class="icon-heart heart-altr"></i></span>';
                }
                else if($wish_stat == 0){
                    //die('etilertueirutt');
                    $courses_arr[$key]['wish_stat'] = '<span class="heart-icon" data-key="'.$value['id'].'" onclick="add_wishlist('.$value['id'].', \''.$session['id'].'\', this)"><i class="icon-heart heart-altr"></i></span>';
                }
                else if($wish_stat == 2){
                    $courses_arr[$key]['wish_stat'] = '';
                }
                
    	}

    	$data['courses']  = $courses_arr;
    	$data['rattings'] = json_encode($ratting);
    	$data['query']    = $search_keyword;
    	$data['admin']    = $this->config->item('acct_name');
    	//echo '<pre>';print_r($data);die(); 
    	$this->load->view($this->config->item('theme').'/search_beta', $data);
    }
    
}
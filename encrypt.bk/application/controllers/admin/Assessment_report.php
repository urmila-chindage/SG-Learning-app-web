<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Assessment_report extends CI_Controller {

	function __construct()
    {
        parent::__construct();
        $this->load->model(array('Course_model', 'User_model'));
        $this->lang->load('course');
    }

    function index()
    {
    	$data          		= array();
        $data['courses'] 	= $this->Course_model->courses(array("select"=>"course_basics.cb_title, course_basics.id", "status"=>"1", "not_deleted"=>"1", "order_by"=>"course_basics.id"));
    	
    	$this->load->view($this->config->item('admin_folder').'/assessment_report', $data);
    }

    function get_lectures(){
    	$course_id = $this->input->post("course_id");
    	$lectures = $this->Course_model->lectures(array("select"=>"cl_lecture_name, id", "course_id"=>$course_id, "status"=>"1", "lecture_type"=>"3"));
    	$lectures = count($lectures) > 0 ? $lectures : false;
    	echo json_encode(array("lectures"=>$lectures));
    }

    function get_assessments(){
    	$lecture_id = $this->input->post("lecture_id");
    	$sort = intval($this->input->post("sort"));
    	$search = $this->input->post("search");
    	$assessments = $this->Course_model->get_assessment_report(array("lecture_id"=>$lecture_id, "sort"=>$sort, "search"=>"%".$search."%"));
    	$assessments = count($assessments) > 0 ? $assessments : false;
    	echo json_encode(array("assessments"=>$assessments));
    }
}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_generated_test extends CI_Controller {

	function __construct()
    {
        parent::__construct();
        // $redirect   = $this->auth->is_logged_in_user(false, false);
        // if (!$redirect)
        // {
        //         redirect('login');
        // }
        $this->load->model(array('Homepage_model', 'Category_model', 'Course_model', 'Generate_test_model'));
        $this->lang->load('course_description');
    }

    function generate_test(){

    	$course_details_str = $this->input->post('course_details');
    	$category_id        = $this->input->post('category_id');
    	$course_details     = json_decode($course_details_str);
    	$user_id            = $this->input->post('user_id');
    	$duration           = $this->input->post('duration');
    	$count              = 0;
    	$test_title			= '';
        
        $category           = $this->Category_model->category(array('id'=>$category_id));

    	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < 10; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
        
        
	$test_title         = "Generated test on ".$category['ct_name'];

    	foreach ($course_details as $key => $value) {
    		$temp = json_decode($value);
    		if(!empty($temp)){
    			$count = $count + $temp[1];
    		}
    	}
    	$save = array();
    	$save['uga_title'] 		= $test_title;
    	$save['uga_category'] 	= $category_id;
    	$save['uga_number_of_questions'] = $count;
    	$save['uga_user_id']    = $user_id;
    	$save['uga_duration']   = $duration;
    	$last_insert_id			= $this->Generate_test_model->generate_assesment($save);
    	foreach ($course_details as $key => $value) {
    		$temp = json_decode($value);
    		if(!empty($temp)){
    			$questions = $this->Generate_test_model->get_questions($temp[0], $temp[1]);
    			foreach ($questions as $key2 => $question) {
    				$this->Generate_test_model->save_question($last_insert_id, $question['id']);
    			}
    		}
    	}

    	$link = site_url().'/material/myexam/'.$last_insert_id;
        $data = array();
        $data['link'] = $link;
        echo json_encode($data);
    }

    function get_category_list(){
        $difficulty = $this->input->post('difficulty');
        $data = array();
        $data['question_category']  = $this->Category_model->get_question_category(array('difficulty' => $difficulty ));
        $html ='';
        foreach ($data['question_category'] as $key => $category) {
            if($category['qc_category_count'] > 0){
                $html .= '<option value="'.$category['id'].'">'.$category['qc_category_name'].'</option>';

            } 
        }

        echo $html;
    }


}
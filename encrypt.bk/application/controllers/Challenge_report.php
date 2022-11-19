<?php
class Challenge_report extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->model(array('Category_model', 'Course_model','Page_model','Challenge_model'));
        $this->lang->load('challenge_zone');
    }

    function report($challenge_id = ''){

    	$data = array();
    	$session  = $this->auth->get_current_user_session('user');
        $data['session']           = $session;
    	$data['challenge_id'] = $challenge_id;
    	$data['users'] = $this->Challenge_model->challenge_zone_attempts(array('challenge_id' => $challenge_id));
    	

    	foreach($data['users'] as $key => $assessment){
    		
            $data['users'][$key]['assessment_report'] = $this->Challenge_model->challenge_report(array('attempted_id' => $assessment['attempted_id']));

            $data['users'][$key]['total_count']       = count($data['users'][$key]['assessment_report']);

            $temp_correct       = 0;
            $temp_wrong         = 0;
            $temp_not_attempted = 0;
            $data['users'][$key]['q_type'] = 1;
            foreach($data['users'][$key]['assessment_report'] as $key2 => $report){
            	
                if($report['czr_answer'] == '' || empty($report['czr_answer'])){
                    $temp_not_attempted++;
                }
                else{

                    if($report['q_type'] == 1){

                        if($report['q_answer'] == $report['czr_answer']){
                            $temp_correct++;
                        }
                        else{
                            $temp_wrong++;
                        }
                    }
                    else if($report['q_type'] == 2){

                        $user_answers = explode(',', $report['q_answer']);
                        $original_answers = explode(',', $report['czr_answer']);
                        sort($user_answers);
                        sort($original_answers);
                        if ($user_answers==$original_answers)
                        {
                            $temp_correct++;
                        }else{
                            $temp_wrong++;
                        }
                    }else if($report['q_type'] == 3){
                        $data['users'][$key]['q_type'] = 3;
                    }
                } 
            }
 
            $data['users'][$key]['count_not_tried'] = $temp_not_attempted;
            $data['users'][$key]['correct'] = $temp_correct;
            $data['users'][$key]['incorrect'] = $temp_wrong;
            $data['users'][$key]['percentage'] =  round(($temp_correct / $data['users'][$key]['total_count']) * 100, 2);
        }

        // $sort_arr = array();
        // foreach($data['users'] as $key => $user){
        // 	$sort_arr[$key] = $user['correct'];
        // }
        // array_multisort($sort_arr, SORT_DESC, $data['users']);

        usort($data['users'], function($a, $b) {

        	if(intval($b['correct']) != intval($a['correct'])){
        		return $b['correct'] - $a['correct'];
        	}
        	else{
        		return $a['cza_duration'] - $b['cza_duration'];
        	}
		    
		});

        //echo '<pre>';print_r($data);die();

        $this->load->view($this->config->item('theme').'/challenge_report', $data);
    }

    function details($challenge_id='', $user_id='', $attempted_id=''){

    	$data = array();
    	$session  = $this->auth->get_current_user_session('user');
        $data['session']           = $session;
    	$data['challenge_id'] = $challenge_id;
    	$data['user_id']      = $user_id;
        $data['attempted_id'] = $attempted_id;
        $data['current_challenge'] =  array();
    	$data['categories']         = $this->Category_model->categories(array('direction'=>'DESC','parent_id'=>'0'));
        $data['challenges']         = $this->Challenge_model->challenges(array('direction'=>'DESC', 'order_by'=>'cz_start_date', 'deleted'=>'0'));



        foreach($data['challenges'] as $challenge){
            if($challenge_id == $challenge['id']){
                $data['current_challenge'] = $challenge;
            }
        }


        foreach($data['categories'] as $category){
            if(!empty($data['current_challenge'])){
                if($category['id'] == $data['current_challenge']['cz_category']){
                    $data['current_category']  = $category;
                }
            }
        }

        if(empty($data['current_challenge'])){
            redirect('dashboard');
        }

        $data['users'] = $this->Challenge_model->challenge_zone_attempts(array('challenge_id' => $challenge_id, 'attempted_id' => $attempted_id));

        foreach($data['users'] as $key => $assessment){
        	$data['users'][$key]['assessment_report'] = $this->Challenge_model->challenge_report(array('attempted_id' => $assessment['attempted_id']));
            $data['users'][$key]['total_count']       = count($data['users'][$key]['assessment_report']);
            $temp_correct       = 0;
            $temp_wrong         = 0;
            $temp_not_attempted = 0;
            $data['users'][$key]['q_type'] = 1;
            foreach($data['users'][$key]['assessment_report'] as $key2 => $report){

            	if($report['czr_answer'] == '' || empty($report['czr_answer'])){
                    $temp_not_attempted++;
                    $data['users'][$key]['assessment_report'][$key2]['correct'] = 2;
                    $temp_options = explode(',', $report['q_options']);
                        $data['users'][$key]['assessment_report'][$key2]['qo_options']  = $temp_options;

                    $temp_options_value = array();
                        $temp_options_value = $this->Challenge_model->get_option_value($report['q_options']);

                        $data['users'][$key]['assessment_report'][$key2]['qo_options_value'] =
                        $temp_options_value;
                        $temp_q_ans   = '';
                        $chr = '';
                        foreach ($temp_options as $value) {
                            if($value == $report['q_answer']){

                                if($temp_q_ans == ''){
                                    $temp_q_ans = chr($chr);
                                }
                                else{
                                    $temp_q_ans = $temp_q_ans.','.chr($chr);
                                }

                            }
                            $chr++;
                        }

                        $data['users'][$key]['assessment_report'][$key2]['correct_answer'] = $temp_q_ans;

                }
                else{

                	if($report['q_type'] == 1){

                        if($report['q_answer'] == $report['czr_answer']){
                            $temp_correct++;
                            $data['users'][$key]['assessment_report'][$key2]['correct'] = 1;
                        }
                        else{
                            $temp_wrong++;
                            $data['users'][$key]['assessment_report'][$key2]['correct'] = 0;
                        }
                        $temp_options = explode(',', $report['q_options']);
                        $temp_arr     = array();
                        
                        $temp_options_value = array();
                        $temp_options_value = $this->Challenge_model->get_option_value($report['q_options']);

                        $data['users'][$key]['assessment_report'][$key2]['qo_options_value'] =
                        $temp_options_value;
                        $data['users'][$key]['assessment_report'][$key2]['qo_options']  = $temp_options;
                        $temp_q_ans   = '';
                        $temp_a_ans   = '';
                        $chr  = 65;
                        
                        foreach ($temp_options as $value) {
                            if($value == $report['q_answer']){

                                if($temp_q_ans == ''){
                                    $temp_q_ans = chr($chr);
                                }
                                else{
                                    $temp_q_ans = $temp_q_ans.','.chr($chr);
                                }

                            }

                            if($value == $report['czr_answer']){

                                if($temp_a_ans == ''){
                                    $temp_a_ans = chr($chr);
                                }
                                else{
                                    $temp_a_ans = $temp_a_ans.','.chr($chr);
                                }
                            }
                            $chr++;
                        }
                        $data['users'][$key]['assessment_report'][$key2]['correct_answer'] = $temp_q_ans;
                        $data['users'][$key]['assessment_report'][$key2]['user_answer'] = $temp_a_ans;

                    }
                    else if($report['q_type'] == 2){

                        $user_answers = explode(',', $report['q_answer']);
                        $original_answers = explode(',', $report['czr_answer']);
                        sort($user_answers);
                        sort($original_answers);
                        if ($user_answers==$original_answers)
                        {
                            $temp_correct++;
                            $data['users'][$key]['assessment_report'][$key2]['correct'] = 1;
                        }else{
                            $temp_wrong++;
                            $data['users'][$key]['assessment_report'][$key2]['correct'] = 0;
                        }

                        $temp_options = explode(',', $report['q_options']);
                        $data['users'][$key]['assessment_report'][$key2]['qo_options']  = $temp_options;
                        $temp_options_value = array();
                        $temp_options_value = $this->Challenge_model->get_option_value($report['q_options']);
                        $data['users'][$key]['assessment_report'][$key2]['qo_options_value'] =
                        $temp_options_value;

                        $temp_q_ans   = '';
                        $temp_a_ans   = '';
                        $chr  = 65;

                        foreach ($temp_options as  $value) {

                            $temp_q_opt = explode(',', $report['q_answer']);
                            
                            foreach ($temp_q_opt as  $opt) {
                                if($value == $opt){

                                    if($temp_q_ans == ''){
                                        $temp_q_ans = chr($chr);
                                    }
                                    else{
                                        $temp_q_ans = $temp_q_ans.','.chr($chr);
                                    }
                                }
                            }


                            $temp_a_opt = explode(',', $report['czr_answer']);
                            foreach ($temp_a_opt as  $opt) {
                                if($value == $opt){

                                    if($temp_a_ans == ''){
                                        $temp_a_ans = chr($chr);
                                    }
                                    else{
                                        $temp_a_ans = $temp_a_ans.','.chr($chr);
                                    }
                                }
                            }
                            
                            $chr++;
                        }

                        $data['users'][$key]['assessment_report'][$key2]['correct_answer'] = $temp_q_ans;
                        $data['users'][$key]['assessment_report'][$key2]['user_answer'] = $temp_a_ans;

                    }else if($report['q_type'] == 3){
                        $data['users'][$key]['q_type'] = 3;
                    }

                }
            }
            $data['users'][$key]['count_not_tried'] = $temp_not_attempted;
            $data['users'][$key]['correct'] = $temp_correct;
            $data['users'][$key]['incorrect'] = $temp_wrong;
            $data['users'][$key]['percentage'] =  round(($temp_correct / $data['users'][$key]['total_count']) * 100, 2);
        }

        $data['prev_id'] = '';
        $data['next_id'] = '';

        foreach ($data['users'] as $key => $assessment) {

            if($assessment['id'] == $user_id){
                
                $data['user']  = $assessment;
            }
        }

        //echo '<pre>';print_r($data);die();

    	$this->load->view($this->config->item('theme').'/challenge_details', $data);
    }

    function print_report($challenge_id, $user_id, $attempted_id){

    	$data = array();
    	$session  = $this->auth->get_current_user_session('user');
        $data['session']           = $session;
    	$data['challenge_id'] = $challenge_id;
    	$data['user_id']      = $user_id;
    	$data['categories']         = $this->Category_model->categories(array('direction'=>'DESC','parent_id'=>'0'));
        $data['challenges']         = $this->Challenge_model->challenges(array('direction'=>'DESC', 'order_by'=>'cz_start_date', 'deleted'=>'0'));

        foreach($data['challenges'] as $challenge){
            if($challenge_id == $challenge['id']){
                $data['current_challenge'] = $challenge;
            }
        }

        foreach($data['categories'] as $category){
            if($category['id'] == $data['current_challenge']['cz_category']){
                $data['current_category']  = $category;
            }
        }

        $data['users'] = $this->Challenge_model->challenge_zone_attempts(array('challenge_id' => $challenge_id, 'attempted_id' => $attempted_id));

        foreach($data['users'] as $key => $assessment){
        	$data['users'][$key]['assessment_report'] = $this->Challenge_model->challenge_report(array('attempted_id' => $assessment['attempted_id']));
            $data['users'][$key]['total_count']       = count($data['users'][$key]['assessment_report']);
            $temp_correct       = 0;
            $temp_wrong         = 0;
            $temp_not_attempted = 0;
            $data['users'][$key]['q_type'] = 1;
            foreach($data['users'][$key]['assessment_report'] as $key2 => $report){

            	if($report['czr_answer'] == '' || empty($report['czr_answer'])){
                    $temp_not_attempted++;
                    $data['users'][$key]['assessment_report'][$key2]['correct'] = 2;
                    $temp_options = explode(',', $report['q_options']);
                        $data['users'][$key]['assessment_report'][$key2]['qo_options']  = $temp_options;

                    $temp_options_value = array();
                        $temp_options_value = $this->Challenge_model->get_option_value($report['q_options']);

                        $data['users'][$key]['assessment_report'][$key2]['qo_options_value'] =
                        $temp_options_value;

                        foreach ($temp_options as $value) {
                            if($value == $report['q_answer']){

                                if($temp_q_ans == ''){
                                    $temp_q_ans = chr($chr);
                                }
                                else{
                                    $temp_q_ans = $temp_q_ans.','.chr($chr);
                                }

                            }
                            $chr++;
                        }

                        $data['users'][$key]['assessment_report'][$key2]['correct_answer'] = $temp_q_ans;

                }
                else{

                	if($report['q_type'] == 1){

                        if($report['q_answer'] == $report['czr_answer']){
                            $temp_correct++;
                            $data['users'][$key]['assessment_report'][$key2]['correct'] = 1;
                        }
                        else{
                            $temp_wrong++;
                            $data['users'][$key]['assessment_report'][$key2]['correct'] = 0;
                        }
                        $temp_options = explode(',', $report['q_options']);
                        $temp_arr     = array();
                        
                        $temp_options_value = array();
                        $temp_options_value = $this->Challenge_model->get_option_value($report['q_options']);

                        $data['users'][$key]['assessment_report'][$key2]['qo_options_value'] =
                        $temp_options_value;
                        $data['users'][$key]['assessment_report'][$key2]['qo_options']  = $temp_options;
                        $temp_q_ans   = '';
                        $temp_a_ans   = '';
                        $chr  = 65;
                        
                        foreach ($temp_options as $value) {
                            if($value == $report['q_answer']){

                                if($temp_q_ans == ''){
                                    $temp_q_ans = chr($chr);
                                }
                                else{
                                    $temp_q_ans = $temp_q_ans.','.chr($chr);
                                }

                            }

                            if($value == $report['czr_answer']){

                                if($temp_a_ans == ''){
                                    $temp_a_ans = chr($chr);
                                }
                                else{
                                    $temp_a_ans = $temp_a_ans.','.chr($chr);
                                }
                            }
                            $chr++;
                        }
                        $data['users'][$key]['assessment_report'][$key2]['correct_answer'] = $temp_q_ans;
                        $data['users'][$key]['assessment_report'][$key2]['user_answer'] = $temp_a_ans;

                    }
                    else if($report['q_type'] == 2){

                        $user_answers = explode(',', $report['q_answer']);
                        $original_answers = explode(',', $report['czr_answer']);
                        sort($user_answers);
                        sort($original_answers);
                        if ($user_answers==$original_answers)
                        {
                            $temp_correct++;
                            $data['users'][$key]['assessment_report'][$key2]['correct'] = 1;
                        }else{
                            $temp_wrong++;
                            $data['users'][$key]['assessment_report'][$key2]['correct'] = 0;
                        }

                        $temp_options = explode(',', $report['q_options']);
                        $data['users'][$key]['assessment_report'][$key2]['qo_options']  = $temp_options;
                        $temp_options_value = array();
                        $temp_options_value = $this->Challenge_model->get_option_value($report['q_options']);
                        $data['users'][$key]['assessment_report'][$key2]['qo_options_value'] =
                        $temp_options_value;

                        $temp_q_ans   = '';
                        $temp_a_ans   = '';
                        $chr  = 65;

                        foreach ($temp_options as  $value) {

                            $temp_q_opt = explode(',', $report['q_answer']);
                            
                            foreach ($temp_q_opt as  $opt) {
                                if($value == $opt){

                                    if($temp_q_ans == ''){
                                        $temp_q_ans = chr($chr);
                                    }
                                    else{
                                        $temp_q_ans = $temp_q_ans.','.chr($chr);
                                    }
                                }
                            }


                            $temp_a_opt = explode(',', $report['czr_answer']);
                            foreach ($temp_a_opt as  $opt) {
                                if($value == $opt){

                                    if($temp_a_ans == ''){
                                        $temp_a_ans = chr($chr);
                                    }
                                    else{
                                        $temp_a_ans = $temp_a_ans.','.chr($chr);
                                    }
                                }
                            }
                            
                            $chr++;
                        }

                        $data['users'][$key]['assessment_report'][$key2]['correct_answer'] = $temp_q_ans;
                        $data['users'][$key]['assessment_report'][$key2]['user_answer'] = $temp_a_ans;

                    }else if($report['q_type'] == 3){
                        $data['users'][$key]['q_type'] = 3;
                    }

                }
            }
            $data['users'][$key]['count_not_tried'] = $temp_not_attempted;
            $data['users'][$key]['correct'] = $temp_correct;
            $data['users'][$key]['incorrect'] = $temp_wrong;
            $data['users'][$key]['percentage'] =  round(($temp_correct / $data['users'][$key]['total_count']) * 100, 2);
        }

        $data['prev_id'] = '';
        $data['next_id'] = '';

        foreach ($data['users'] as $key => $assessment) {

            if($assessment['id'] == $user_id){
                
                $data['user']  = $assessment;
            }
        }

        //echo '<pre>';print_r($data);die();

    	$this->load->view($this->config->item('theme').'/challenge_print', $data);
    }

}
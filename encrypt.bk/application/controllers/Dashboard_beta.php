<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_beta extends CI_Controller {

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     *	- or -
     * 		http://example.com/index.php/welcome/index
     *	- or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see https://codeigniter.com/user_guide/general/urls.html
     */
    function __construct()
    {
        parent::__construct();
        $redirect	= $this->auth->is_logged_in_user(false, false);
        if (!$redirect)
        {
                redirect('login');
        }
        $this->load->model('Course_model');
        $this->lang->load('course');
        $this->load->model('User_model');
        $this->lang->load('dashboard');
        $this->lang->load('homepage');
        $this->load->model('User_generated_model');
        $this->load->model('Category_model');
        $this->load->model('Challenge_model');
        $this->load->model('Homepage_model');
    }

    public function index()
    {
        //unset($_SESSION['course']);
        
        $data     = array();
        $session  = $this->auth->get_current_user_session('user');
        $data['session']           = $session;

        $data['admin']             = $this->config->item('acct_name');
        $data['admin_name']        = $this->config->item('us_name');
        
        $data['today']      = date('Y-m-d');

        $data['user_details']      = $this->User_model->user(array('id'=> $session['id']));
        $data['course_details']    = $this->User_model->enrolled_course(array('user_id'  => $session['id']));
        $data['wishlist_courses']  = $this->User_model->wishlist_courses(array('user_id' => $session['id']));
        
        $data['categories']         = $this->Category_model->categories(array('direction'=>'DESC', 'parent_id'=>0, 'status'=>1));
        
        $data['challenges_stat']    = 0;

        foreach($data['categories'] as $key => $categories){
            $data['categories'][$key]['challenges']  = $this->Homepage_model->get_challenge_details($categories['id']);
            if(count($data['categories'][$key]['challenges']) > 0){
                $data['challenges_stat']    = 1;
                foreach ($data['categories'][$key]['challenges'] as $key2 => $challenge) {
                    $dt = new DateTime($challenge['cz_end_date']);
                    $today = new DateTime(); 
                    $today->setTime( 0, 0, 0 );
                    $match_date = new DateTime($dt->format('Y-m-d'));
                    $match_date->setTime( 0, 0, 0 );
                    $diff = $today->diff( $match_date );
                    $diffDays = (integer)$diff->format( "%R%a" );

                    if($diffDays >= 0){
                        $data['categories'][$key]['challenges'][$key2]['challenge_btn'] = 'CHALLENGE NOW';
                        $data['categories'][$key]['challenges'][$key2]['challenge_text']= 'Ends On';
                        $data['categories'][$key]['challenges'][$key2]['challenge_link'] = site_url().'/material/challenge/'.$challenge['id'];
                           $data['categories'][$key]['challenges'][$key2]['user_enddate']= $dt->format('M d Y g:i a');
                    }
                    else{
                        $data['categories'][$key]['challenges'][$key2]['challenge_btn'] = 'VIEW CHALLENGE REPORT';
                        $data['categories'][$key]['challenges'][$key2]['challenge_text']= 'Completed On';
                        $data['categories'][$key]['challenges'][$key2]['challenge_link'] = site_url().'/challenge_report/report/'.$challenge['id'];
                        $data['categories'][$key]['challenges'][$key2]['user_enddate']= $dt->format('M d Y g:i a');
                    }


                }
            }
        }

        $data['challenge_details']  = json_encode($data['categories']);
        
        foreach ($data['course_details'] as $key => $value) {
             $temp_arr = $this->User_model->get_tutors($value['cs_course_id']);
             $str      = '';
             foreach($temp_arr as $temp){
                if($str == ''){
                    $str   = $temp['us_name'];
                }
                else{
                    $str   = $str .', ' . $temp['us_name'];
                }
                
             }
             $data['course_details'][$key]['lecture_count']                 =  $this->User_model->get_lecture_count($value['cs_course_id']);
             $data['course_details'][$key]['assessment_count']              =  $this->User_model->get_lecture_count($value['cs_course_id'], array('type' => 3));
             $data['course_details'][$key]['lecture_completion_count']      =  $this->User_model->get_lecture_completion_count($value['cs_course_id'], $value['id']);
             $data['course_details'][$key]['assessment_completion_count']   =  $this->User_model->get_lecture_completion_count($value['cs_course_id'], $value['id'] ,array('type' => 3));
             $data['course_details'][$key]['assigned_tutors']               =  $str;
        }
        
        //echo "<pre>";print_r($data);die;
        foreach ($data['wishlist_courses'] as $key => $value) {
             $temp_arr                                  = $this->User_model->get_wishlist_courses($value['cw_course_id']);
             $data['wishlist_courses'][$key]['ratting'] = $this->User_model->get_course_rating($value['cw_course_id']); 
             $str                                       = '';
             foreach($temp_arr as $temp){
                if($str == ''){
                    $str   = $temp;
                }
                else{
                    $str   = $str .', ' . $temp;
                }
             }
            
             $data['wishlist_courses'][$key]['wished_courses']   =  $str;     
        }
        
        
        $data['user_course_enrolled']       = $this->User_model->enrolled_course(array('user_id' => $session['id'])); 
        $data['total_enrolled_course']      = sizeof($data['user_course_enrolled']);
        
        $data['user_attempted_assessment']  = $this->User_model->assessment_attempt(array('user_id' => $session['id']));
        $data['total_attempted_assessment'] = sizeof($data['user_attempted_assessment']);
        //echo "<pre>"; print_r($data['user_attempted_assessment']); die;
        
        $data['user_assessment_report'] = array();
        foreach ($data['user_attempted_assessment'] as $key => $assessment) {
            $data['user_assessment_report'][$assessment['aa_assessment_id']]     = $this->User_model->assessment_report(array('user_id' => $session['id'], 'assessment_id' => $assessment['aa_assessment_id']));
            $data['user_attempted_assessment'][$key]['total'] = sizeof( $data['user_assessment_report'][$assessment['aa_assessment_id']]);
            
            $data['user_attempted_assessment'][$key]['count_not_tried'] = 0;
            $data['user_attempted_assessment'][$key]['correct'] = 0;
            $data['user_attempted_assessment'][$key]['incorrect'] = 0;
            foreach ($data['user_assessment_report'][$assessment['aa_assessment_id']] as $questions) {
                if($questions['ar_answer'] == '' || empty($questions['ar_answer'])){
                    $data['user_attempted_assessment'][$key]['count_not_tried']++;
                }else{
                    if($questions['q_type'] == 1){
                        if($questions['q_answer'] == $questions['ar_answer']){
                            $data['user_attempted_assessment'][$key]['correct']++;
                        }else{
                            $data['user_attempted_assessment'][$key]['incorrect']++;
                        }
                    }elseif($questions['q_type'] == 2){
                        $user_answers = explode(',', $questions['ar_answer']);
                        $original_answers = explode(',', $questions['q_answer']);
                        sort($user_answers);
                        sort($original_answers);
                        if ($user_answers==$original_answers)
                        {
                            $data['user_attempted_assessment'][$key]['correct']++;
                        }else{
                            $data['user_attempted_assessment'][$key]['incorrect']++;
                        }
                    }
                }
                $success_percentage = ($data['user_attempted_assessment'][$key]['correct'] / $data['user_attempted_assessment'][$key]['total']) * 100;
                $data['user_attempted_assessment'][$key]['success_percent'] = $success_percentage;
            }
        }
        
        
        $data['user_attempted_challenge_zone']  = $this->User_model->challenge_zone_attempt(array('user_id' => $session['id']));
        $data['total_attempted_challenge_zone'] = sizeof($data['user_attempted_challenge_zone']);
        
        $data['user_challenge_zone_report'] = array();
        foreach ($data['user_attempted_challenge_zone'] as $key => $challenge_zone) {
            $data['user_challenge_zone_report'][$challenge_zone['cza_challenge_zone_id']]     = $this->User_model->challenge_zone_report(array('user_id' => $session['id'], 'challenge_zone_id' => $challenge_zone['cza_challenge_zone_id']));
            $data['user_attempted_challenge_zone'][$key]['total_cz'] = sizeof( $data['user_challenge_zone_report'][$challenge_zone['cza_challenge_zone_id']]);
            
            $data['user_attempted_challenge_zone'][$key]['cz_count_not_tried'] = 0;
            $data['user_attempted_challenge_zone'][$key]['cz_correct'] = 0;
            $data['user_attempted_challenge_zone'][$key]['cz_incorrect'] = 0;
            foreach ($data['user_challenge_zone_report'][$challenge_zone['cza_challenge_zone_id']] as $questions) {
                if($questions['czr_answer'] == '' || empty($questions['czr_answer'])){
                    $data['user_attempted_challenge_zone'][$key]['cz_count_not_tried']++;
                }else{
                    if($questions['q_type'] == 1){
                        if($questions['q_answer'] == $questions['czr_answer']){
                            $data['user_attempted_challenge_zone'][$key]['cz_correct']++;
                        }else{
                            $data['user_attempted_challenge_zone'][$key]['cz_incorrect']++;
                        }
                    }elseif($questions['q_type'] == 2){
                        $user_answers = explode(',', $questions['czr_answer']);
                        $original_answers = explode(',', $questions['q_answer']);
                        sort($user_answers);
                        sort($original_answers);
                        if ($user_answers==$original_answers)
                        {
                            $data['user_attempted_challenge_zone'][$key]['cz_correct']++;
                        }else{
                            $data['user_attempted_challenge_zone'][$key]['cz_incorrect']++;
                        }
                    }
                }
                $cz_success_percentage = ($data['user_attempted_challenge_zone'][$key]['cz_correct'] / $data['user_attempted_challenge_zone'][$key]['total_cz']) * 100;
                $data['user_attempted_challenge_zone'][$key]['cz_success_percent'] = $cz_success_percentage;
            }
        } 
        //echo "challenge";die; 
        
        $data['user_generated_test'] = $this->User_generated_model->attempted_test(array('user_id' => $session['id']));
        $data['total_attempted_user_generated_test'] = sizeof($data['user_generated_test']);
        
        foreach($data['user_generated_test'] as $key => $assessment){
            
            $data['user_generated_test'][$key]['assessment_report'] = $this->User_generated_model->assessment_report(array('attempted_id' => $assessment['attempted_id']));

            $data['user_generated_test'][$key]['total_count']       = count($data['user_generated_test'][$key]['assessment_report']);

            $temp_correct       = 0;
            $temp_wrong         = 0;
            $temp_not_attempted = 0;
            $data['user_generated_test'][$key]['q_type'] = 1;
            foreach($data['user_generated_test'][$key]['assessment_report'] as $key2 => $report){
                
                if($report['ugar_answer'] == '' || empty($report['ugar_answer'])){
                    $temp_not_attempted++;
                }
                else{

                    if($report['q_type'] == 1){

                        if($report['q_answer'] == $report['ugar_answer']){
                            $temp_correct++;
                        }
                        else{
                            $temp_wrong++;
                        }
                    }
                    else if($report['q_type'] == 2){

                        $user_answers = explode(',', $report['q_answer']);
                        $original_answers = explode(',', $report['ugar_answer']);
                        sort($user_answers);
                        sort($original_answers);
                        if ($user_answers==$original_answers)
                        {
                            $temp_correct++;
                        }else{
                            $temp_wrong++;
                        }
                    }else if($report['q_type'] == 3){
                        $data['user_generated_test'][$key]['q_type'] = 3;
                    }
                } 
            }
 
            $data['user_generated_test'][$key]['count_not_tried'] = $temp_not_attempted;
            $data['user_generated_test'][$key]['correct'] = $temp_correct;
            $data['user_generated_test'][$key]['incorrect'] = $temp_wrong;
            $data['user_generated_test'][$key]['percentage'] =  round(($temp_correct / $data['user_generated_test'][$key]['total_count']) * 100, 2);
        }
        
        //getting the fields asscodiated with this user
        $this->load->model('Settings_model');
        $data['user_profile_fields'] = array();
        $user_profile_fields         = $this->Settings_model->profile_field_values(array('user_id' => $session['id']));
        if(!empty($user_profile_fields))
        {
            foreach ($user_profile_fields as $field)
            {
                $data['user_profile_fields'][$field['upf_field_id']] = $field['upf_field_value'];
            }
        }
        
        $profile_blocks          = $this->Settings_model->blocks();
        $data['profile_blocks']  = array();
        if(!empty($profile_blocks))
        {
            foreach ($profile_blocks as $profile_block)
            {
                $profile_block['profile_fields']              = $this->Settings_model->profile_fields(array('block_id' => $profile_block['id']));
                $data['profile_blocks'][$profile_block['id']] = $profile_block;
            }
        }
        //End
        
        //echo '<pre>';print_r($data);die();
        $this->load->view($this->config->item('theme').'/dashboard_beta', $data);
    }

}
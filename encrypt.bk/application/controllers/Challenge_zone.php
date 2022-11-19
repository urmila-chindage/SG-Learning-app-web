<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Challenge_zone extends CI_Controller {

	function __construct()
    {
        parent::__construct();
        // $redirect   = $this->auth->is_logged_in_user(false, false);
        // if (!$redirect)
        // {
        //         redirect('login');
        // }
        $this->load->model(array('Challenge_model','Course_model'));
        $this->load->library('newsletter');
        //$this->lang->load('course_description');
    }

    function test()
    {
        $this->Challenge_model->test();
    }
    public function index(){
        $user = $this->auth->get_current_user_session('user');
        $data = array();
        $challenge_zone = array();
        
        $limit              = 4;
        $offset             = 0;

        $challenge_zone = array();
        $categories = $this->Challenge_model->get_challenge_categories(array('select'=>'id,ct_name','limit'=>$limit,'offset'=>$offset,'direction'=>'ASC'));
        foreach ($categories as $key => $category) {
            $challenge_zone[$key]['category_name'] = $category['ct_name'];
            $challenge_zone[$key]['category_id']   = $category['id'];
            $challenge_zone[$key]['challenges']          = $this->Challenge_model->get_challenges(array('user_id'=>isset($user['id'])?$user['id']:0,'category_id'=>$category['id'],'offset'=>'0','limit'=>'4','direction'=>'DESC','sort'=>'created_date','select'=>'id,cz_title,cz_start_date,cz_end_date,cz_duration,created_date,cz_category'));
            foreach ($challenge_zone[$key]['challenges'] as $key1 => $challenge) {
                $dt = new DateTime($challenge['cz_end_date']);
                $now_date = date('Y-m-d H:i:s');
                $now_date = new DateTime($now_date);
                $today = new DateTime($now_date->format('Y-m-d H:i:s')); 
               
                //$today->setTime( 0, 0, 0 );
                $match_date = new DateTime($dt->format('Y-m-d H:i:s'));
                //$match_date->setTime( 0, 0, 0 );
                 //print_r($today);print_r($match_date);die;

                $diff = $today->diff( $match_date );
                $elapsed = $diff->format('%y years %m months %a days %h hours %i minutes %S seconds');
                
                
                
                $diffDays = (integer)$diff->format( "%R%a%h%i%S" );
                $challenge_zone[$key]['challenges'][$key1]['date_status'] = ($diffDays>0)?1:0;
                switch( $diffDays ){
                    case 0:$challenge_zone[$key]['challenges'][$key1]['date_on'] = "Today ";break;
                    case +1:$challenge_zone[$key]['challenges'][$key1]['date_on'] = "Tomorrow ";break;
                    default:$challenge_zone[$key]['challenges'][$key1]['date_on'] = $dt->format('M d Y');
                }
                $challenge_zone[$key]['challenges'][$key1]['time'] = $dt->format('g:i a');
            }
        }

        //echo json_encode($challenge_zone);

        //echo '<pre>';print_r($challenge_zone);die;
        $data['session'] = $user;
        $data['total_challenges']=  $this->Challenge_model->get_challenge_categories(array('count'=>true));
        $data['challenge_zones'] = $challenge_zone;
        $data['limit']   = $limit;
        $data['user_id'] = isset($user['id'])?$user['id']:0;
        $this->load->view($this->config->item('theme').'/challenge_zone',$data);
    }

    public function ajax_load_challenge(){
        $user = $this->auth->get_current_user_session('user');
        $challenge_zone = array();

        $offset_recieved    = $this->input->post('offset');
        
        $limit              = $this->input->post('limit');
        $offset             = 0;
        if($offset_recieved && $offset_recieved !=0){
            $offset       = $offset_recieved * $limit;
        }

        $challenge_zone = array();
        $categories = $this->Challenge_model->get_challenge_categories(array('select'=>'id,ct_name','limit'=>$limit,'offset'=>$offset,'direction'=>'ASC'));
        foreach ($categories as $key => $category) {
            $challenge_zone[$key]['category_name'] = $category['ct_name'];
            $challenge_zone[$key]['category_id']   = $category['id'];
            $challenge_zone[$key]['challenges']          = $this->Challenge_model->get_challenges(array('user_id'=>isset($user['id'])?$user['id']:0,'category_id'=>$category['id'],'offset'=>'0','limit'=>'4','direction'=>'DESC','sort'=>'created_date','select'=>'id,cz_title,cz_start_date,cz_end_date,cz_duration,created_date,cz_category'));
            foreach ($challenge_zone[$key]['challenges'] as $key1 => $challenge) {
                $dt = new DateTime($challenge['cz_end_date']);
                $now_date = date('Y-m-d H:i:s');
                $now_date = new DateTime($now_date);
                $today = new DateTime($now_date->format('Y-m-d H:i:s')); 
               
                //$today->setTime( 0, 0, 0 );
                $match_date = new DateTime($dt->format('Y-m-d H:i:s'));
                //$match_date->setTime( 0, 0, 0 );
                 //print_r($today);print_r($match_date);die;

                $diff = $today->diff( $match_date );
                $elapsed = $diff->format('%y years %m months %a days %h hours %i minutes %S seconds');
                
                
                
                $diffDays = (integer)$diff->format( "%R%a%h%i%S" );
                $challenge_zone[$key]['challenges'][$key1]['date_status'] = ($diffDays>0)?1:0;
                switch( $diffDays ){
                    case 0:$challenge_zone[$key]['challenges'][$key1]['date_on'] = "Today ";break;
                    case +1:$challenge_zone[$key]['challenges'][$key1]['date_on'] = "Tomorrow ";break;
                    default:$challenge_zone[$key]['challenges'][$key1]['date_on'] = $dt->format('M d Y');
                }
                $challenge_zone[$key]['challenges'][$key1]['time'] = $dt->format('g:i a');
            }
        }

        echo json_encode($challenge_zone);
    }

    public function category($category_id=false){
        if(!$category_id)
        {
            redirect('dashboard');
        }
        
        $user = $this->auth->get_current_user_session('user');
        $data = array();
        $limit = 90;
        $data['limit'] = $limit;
        $data['user_id'] = $user['id'];
        $data['category']    = $this->Challenge_model->get_challenge_categories(array('select'=>'id,ct_name','category_id'=>$category_id));
        //getting category details
        $this->load->model(array('Category_model'));
        $category = $this->Category_model->category(array('id'=>$category_id, 'status'=>1));
        $data['title'] = $category['ct_name'];
        //End
                $data['challenge_zone_categories'] = $this->Challenge_model->get_challenge_categories(array('select'=>'id,ct_name','direction'=>'ASC','offset'=>'0','limit'=>'100'));
        $data['category_id'] = $category_id;
        $challenge_zone      = $this->Challenge_model->get_challenges(array('user_id'=>isset($user['id'])?$user['id']:0,'category_id'=>$category_id,'offset'=>'0','limit'=>$limit,'direction'=>'DESC','sort'=>'created_date','select'=>'id,cz_title,cz_start_date,cz_end_date,cz_duration,created_date,cz_category'));
            foreach ($challenge_zone as $key => $challenge) {
                $dt = new DateTime($challenge['cz_end_date']);
                $now_date = date('Y-m-d H:i:s');
                $now_date = new DateTime($now_date);
                $today = new DateTime($now_date->format('Y-m-d H:i:s')); 
               
                //$today->setTime( 0, 0, 0 );
                $match_date = new DateTime($dt->format('Y-m-d H:i:s'));
                //$match_date->setTime( 0, 0, 0 );
                 //print_r($today);print_r($match_date);die;

                $diff = $today->diff( $match_date );
                $elapsed = $diff->format('%y years %m months %a days %h hours %i minutes %S seconds');
                
                
                
                $diffDays = (integer)$diff->format( "%R%a%h%i%S" );
                $challenge_zone[$key]['date_status'] = ($diffDays>0)?1:0;
                switch( $diffDays ){
                    case 0:$challenge_zone[$key]['date_on'] = "Today ";break;
                    case +1:$challenge_zone[$key]['date_on'] = "Tomorrow ";break;
                    default:$challenge_zone[$key]['date_on'] = $dt->format('M d Y');
                }
                $challenge_zone[$key]['time'] = $dt->format('g:i a');
            }
        $data['challenges'] = $challenge_zone;

        //echo '<pre>';print_r($data);die;
        $this->load->view($this->config->item('theme').'/challenge_zone_category',$data);
    }

    public function questions($cz_id){
        
        $session = $this->auth->get_current_user_session('user');
        $response            = array();
        $response['error']   = 'false';
        $data                = array();
        $lecture             = array();
        
        if(isset($session)&&isset($cz_id)){

            $questions   = $this->Course_model->cz_questions(array('cz_assessment_id' => $cz_id));
            //$data['assessment_details']     = $this->Course_model->assessment_report_test(array('attempt_id' => $assessment_attempt_id)); 
            
            if(!empty($questions))
            {
                foreach($questions as $question)
                {
                    $lecture['question_type_wise'][$question['q_type']][]             = $question['id'];
                    $lecture['question_difficulty_type'][$question['q_difficulty']][] = $question['id'];
                    $question['options']                                              = $this->Course_model->options(array('q_answer' => $question['q_options']));
                    $lecture['questions'][$question['id']]                            = $question;
                }
            }

            $data['challenge_zone_details'] = $this->Challenge_model->challenge(array('id'=>$cz_id,'select'=>'challenge_zone.cz_title,challenge_zone.cz_end_date'));

            $dt = new DateTime($data['challenge_zone_details']['cz_end_date']);
            $now_date = date('Y-m-d H:i:s');
            $now_date = new DateTime($now_date);
            $today = new DateTime($now_date->format('Y-m-d H:i:s')); 
           
            //$today->setTime( 0, 0, 0 );
            $match_date = new DateTime($dt->format('Y-m-d H:i:s'));
            //$match_date->setTime( 0, 0, 0 );
             //print_r($today);print_r($match_date);die;

            $diff = $today->diff( $match_date );
            $elapsed = $diff->format('%y years %m months %a days %h hours %i minutes %S seconds');
            
            
            
            $diffDays = (integer)$diff->format( "%R%a%h%i%S" );

            $data['lecture']                = $lecture;
            //echo '<pre>'; print_r($data);die;
            if($diffDays>=0){
                redirect('/challenge_zone');
            }else{
                $this->load->view($this->config->item('theme').'/challenge_questions', $data);
            }


        }else{
            redirect('login');
        }
    }
    
    public function invite_friends(){
        $emails = array();
        $result_emails = array();
        $this->load->model('User_model');
        $emails = $this->input->post('email_id');
        $cz_id  = $this->input->post('challenge_zone_id');
        $user = $this->auth->get_current_user_session('user');
        $emails = json_decode($emails);
        $email_arr = array();
        $present_emails = array();
        $challenge_status = '0';
        if(!empty($emails)){
            foreach ($emails as $key => $value) {
                $email_arr[] = $value; 
            }
        }
        $challenge      = $this->Challenge_model->challenge(array('id'=>$cz_id,'select'=>'id,cz_title,cz_category,cz_start_date,cz_end_date,cz_duration,cz_status,cz_deleted,created_date'));

        $dt = new DateTime($challenge['cz_end_date']);
        $now_date = date('Y-m-d H:i:s');
        $now_date = new DateTime($now_date);
        $today = new DateTime($now_date->format('Y-m-d H:i:s')); 
       
        //$today->setTime( 0, 0, 0 );
        $match_date = new DateTime($dt->format('Y-m-d H:i:s'));
        //$match_date->setTime( 0, 0, 0 );
         //print_r($today);print_r($match_date);die;

        $diff = $today->diff( $match_date );
        $elapsed = $diff->format('%y years %m months %a days %h hours %i minutes %S seconds');
        
        
        
        $diffDays = (integer)$diff->format( "%R%a%h%i%S" );

        if($diffDays >= 0){
            $challenge_status = '1';
        }else{
            $challenge_status = '0';
        }

        //$emails = array_values($emails);
        /*if(!empty($email_arr)){
            $present_emails = $this->User_model->get_registered(array('emails'=>$email_arr,'role_id'=>'2','delete'=>'0'));
        }
        foreach ($present_emails as $key => $value) {
            if($user['us_email'] != $value['us_email']){
                $result_emails[] = $value['us_email'];
            }
        }
        $email_arr = $result_emails;*/
        //print_r($email_arr);die;
        $response = array();
        //$response['challenge'] = $challenge;
        //print_r($emails);
        //echo $emails;
        $redirect     = $this->auth->is_logged_in_user(false, false);
        if(!$redirect){
            $response['success'] = false;
            $response['message'] = 'You need to login to invite.';
        }else if(empty($email_arr)){
            $response['success'] = false;
            $response['message'] = 'List is empty or given email id\'s are not registered.';
        }else if($challenge_status == '0'){
            $response['success'] = false;
            $response['message'] = 'You can only invite friends to an ongoing challenge only.';
        }else{
            $response = $this->newsletter->invite_challenge($email_arr,$user,$challenge);
        }

        echo json_encode($response);

    }
    public function invite(){
        //echo $this->config->item('site_name');
        $this->load->view($this->config->item('theme').'/cz_invite');
    }

    /*public function transfer(){
    	$users = $this->Challenge_model->get_users();
    	//echo '<pre>'; print_r($users);die;

    	foreach($users as $user){
    		$param = array('userid'=>$user['id'],'status'=>'offline','isdevice'=>'0');
    		$this->Challenge_model->insert_status($param);
    	}

    	echo 'success';
    }*/
}
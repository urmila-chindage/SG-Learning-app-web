<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Material extends CI_Controller {
    public $__lecture_type_array;
    public $__lecture_type_keys_array;
    public $__lecture_type_icon_array;
    public $__redirect;
    function __construct()
    {
        parent::__construct();

        $this->__student            = '';
        $user_token                 = isset($_REQUEST['token'])?$_REQUEST['token']:'';
        // echo "<pre>";print_r($_REQUEST);exit;
        // $user_token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6IjMiLCJlbWFpbF9pZCI6InRoYW52ZWVyLmFld0BlbmZpbnRlY2hub2xvZ2llcy5jb20iLCJtb2JpbGUiOiI5MDQzMTcxNzE2In0.-XPIhA_ewsBuHtOleXUfdfvA4Fr26yxozoLEyWMeKFE';
        $this->user_token           = $user_token;
        if($user_token != '')
        {
            $verified_data          = $this->api_authentication->verify_token($user_token);
            // echo "<pre>";print_r($verified_data);exit;
            if($verified_data['token_verified'] == true)
            {
                $this->__student    = $verified_data['user'] ;
            }
        }
        else
        {
            // $redirect               = $this->auth->is_logged_in_user(false, false);
            $this->__student        = $this->auth->get_current_user_session('user');
        }

        
        $this->load->library('PHPWord');
        $this->load->library('session');
        $explicit_method  = array('live_class', 'save_lecture', 'convert_live_lecture');
        if (empty($this->__student) && !in_array($this->router->fetch_method(), $explicit_method))
        {
            $this->session->set_flashdata('redirect',current_url());
            redirect('login');
        }
        $this->lang->load('material');
        $this->load->model(array('Course_model', 'User_model', 'Challenge_model', 'User_generated_model'));
        $this->__lecture_type_array = array(  '1' => 'video',
                                              '2' => 'document',
                                              '3' => 'assesment',
                                              '4' => 'youtube',
                                              '5' => 'text',
                                              '6' => 'wikipedia',
                                              '7' => 'live',
                                              '8' => 'descriptive',
                                              '9' => 'recordedvideo',
                                              '10' => 'scorm',
                                              '11' => 'cisco_recorded_video',
                                              '12' => 'audio',
                                              'invalid_content' => 'invalid_content'
                                       );
        $this->__lecture_type_keys_array  = array();
        foreach ($this->__lecture_type_array as $id => $type) {
            $this->__lecture_type_keys_array[$type] = $id;
        }
        $this->__lecture_type_icon_array = array(   '1' => 'icon-play-circled2',
                                                    '2' => 'icon-doc-text',
                                                    '3' => 'icon-beaker',
                                                    '4' => 'icon-play-circled2',
                                                    '5' => 'icon-doc-text',
                                                    '6' => 'icon-doc-text',
                                                    '7' => 'icon-play-circled2',
                                                    '8' => 'icon-doc-text',
                                                    '9' => 'icon-play-circled2',
                                                    '10' => 'icon-play-circled2',
                                                    '11' => 'icon-play-circled2',
                                                    '12' => 'icon-play-circled2',
                                                    'invalid_content' => 'invalid_content'
                                            );
        $this->__redirect = 'dashboard';
        date_default_timezone_set('Asia/Kolkata');
       
    }
    
    public function index()
    {
        redirect($this->__redirect);
    }
    
    function download_lecture($id=false)
    {
        //echo config_item('youtube_api');
        if(!$id)
        {
            echo '</h3>File id missing</h3>';exit;
        }
        
        $lecture = $this->Course_model->lecture(array('id' => $id));
        if(!$lecture)
        {
            echo '</h3>Invalid file</h3>';exit;
        }
        
        if(!in_array($lecture['cl_lecture_type'], array('1', '2', '12')))
        {
            echo '</h3>Invalid file</h3>';exit;            
        }
        // $this->__student = $this->auth->get_current_user_session('user');
        $subscription = $this->User_model->subscription(array('user_id' => $this->__student['id'], 'course_id' => $lecture['cl_course_id']));
        if( !$subscription )
        {
            echo '</h3>Your are not authorised to access this file</h3>';exit;            
        }
        else
        {
            $today          = date('Y-m-d');        
            $expire         = date_diff(date_create($today),date_create($subscription['cs_end_date']));         
            $expire_in      = $expire->format("%R%a");      
            //echo $expire_in.'<br /><pre>'; print_r($subscription);die;
            if( $subscription['cs_approved'] != '1' || $expire_in < 0 || $lecture['cl_downloadable'] == '0')
            {
                echo '</h3>Your are not authorised to access this file</h3>';exit;                        
            }
        }
        
        switch ($lecture['cl_lecture_type'])
        {
            case "1":
                $path = video_upload_path(array('course_id' => $lecture['cl_course_id'])); 
            break;
            case "12":
                $path = audio_upload_path(array('course_id' => $lecture['cl_course_id'])); 
            break;
            default:
                $path = document_upload_path(array('course_id' => $lecture['cl_course_id'])); 
                break;
        }

        $fullPath = $path.$lecture['cl_org_file_name'];
        //echo $fullPath;die;
        if ($fd = fopen ($fullPath, "r")) {
            $fsize = filesize($fullPath);
            $path_parts = pathinfo($fullPath);
            $ext = strtolower($path_parts["extension"]);
            switch ($ext) {
                case "doc":
                header("Content-type: application/msword"); // add here more headers for diff. extensions
                header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a download
                break;
                case "pdf":
                header("Content-type: application/pdf"); // add here more headers for diff. extensions
                header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a download
                break;
                default;
                header("Content-type: application/octet-stream");
                header("Content-Disposition: filename=\"".$path_parts["basename"]."\"");
            }
            header("Content-length: $fsize");
            header("Cache-control: private"); //use this to open files directly
            while(!feof($fd)) {
                $buffer = fread($fd, 2048);
                echo $buffer;
            }
        }
        fclose ($fd);
        exit;
    }
    private function allowed_flow($id)
    {
        $http_referer = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:false;
        if($http_referer)
        {
            //echo '<pre>'; print_r($_SERVER);die;
            $http_url       = parse_url($http_referer, PHP_URL_PATH);
            $path_pieces    = explode('/', $http_url);
            $path_pieces    = array_filter($path_pieces);

            //echo '<pre>'; print_r($path_pieces);die;
            $material_index = 1;
            $dashboard_index = 2;
            if($this->config->item('index_page')!='')
            {
                $material_index = 2;
                $dashboard_index = 3;        
            }
            
            //true if referer not from coursen dashboard
            if(isset($path_pieces[$material_index]) && $path_pieces[$material_index] != 'material')
            {
                redirect('material/dashboard/'.$id);exit;
            }
            else
            {
                if(isset($path_pieces[$dashboard_index]) && ($path_pieces[$dashboard_index] != 'dashboard' && $path_pieces[$dashboard_index] != 'course'))
                {
                    redirect('material/dashboard/'.$id);exit;
                }
            }
        }
    }

    //By Alex
    function dashboard($c_id = false)
    {
        redirect('course/dashboard/'.$c_id);exit;
        if(!$c_id)
        {
            redirect('/404_error');
            
        }
        $param = array();
        
        $check_subscription = $this->Course_model->enrolled(array('course_id' => $c_id, 'count'=> '1'));
        $data['subscription_count'] = $check_subscription;
        
        $user = $this->auth->get_current_user_session('user');
        
        $get_rating                      = $this->Course_model->check_rating(array('course_id'=> $c_id, 'user_id'=> $this->__student['id']));      
        if(empty($get_rating))
        {       
            $data['check_user_rating'] = '0';       
        }
        else
        {       
        
            $data['check_user_rating'] = '1';       
        }
        
        
        $data['course_rating']      = $this->Course_model->get_user_ratting(array('course_id' => $c_id, 'user_id'=>$this->__student['id'] ));
        
        $param = array('user_id'=>$this->__student['id'],'course_id'=>$c_id);
        $data['course_details']                         = $this->Course_model->db_course_details($param);
        
        $this->load->model(array('Report_model'));
        
        $this->session->set_userdata(array(
            'item_id' => $c_id,
            'item_type' => 'course'
            ));

        
        
        $data['title']                                  = $this->config->item('site_name');

        
        //echo '<pre>'; print_r($data['course_details']);die;
        if(!isset($data['course_details']['cb_title']))
        {
            $this->index();
        }
        $data['meta_original_title']                    = $data['course_details']['cb_title'];
        $data['meta_title']                             = $data['course_details']['cb_meta'];
        $data['meta_description']                       = $data['course_details']['cb_meta_description'];
        if($c_id==false||empty($data['course_details'])){
            $this->load->view($this->config->item('theme').'/404_error');
        }
        unset($param);
        $param = array();

        $param = array('course_id' => $c_id,'select' => 'us_name','select'=>'users.id,users.us_name,users.us_role_id','role_filter'=>'3');
        $data['course_details']['course_tutors']        = $this->Course_model->get_course_tutors($param);
        $data['admin_name']                             = $this->config->item('us_name');
        $data['course_details']['languages']            = $this->Course_model->languages(array('select'=>'cl_lang_name','language_id'=>$data['course_details']['cb_language']));
        $data['course_details']['total_lectures']       = 0;
        $data['course_details']['completed_lectures']   = $this->Course_model->db_completed_lectures(array('user_id'=>$this->__student['id'],'course_id'=>$c_id));
        $data['course_details']['videos']               = 0;
        $data['course_details']['documents']            = 0;
        $data['course_details']['live_lectures']        = 0;
        $data['course_details']['assessments']          = 0;
        $data['course_details']['videos_length']        = 0;
        $db_sections = $this->Course_model->sections(array('course_id' => $c_id,'status'=>'1','order_by'=>'s_order_no','direction'=>'ASC'));
        $lectures = $this->Course_model->db_lectures(array('c_id'=>$c_id,'ses'=>$db_sections));
        //echo '<pre>';print_r($db_sections);die;
        foreach ($lectures as $lecture) {
            switch($lecture['cl_lecture_type']){
                case 1:$data['course_details']['videos']++; $data['course_details']['videos_length']        += $lecture['cl_duration'];break;
                case 2:$data['course_details']['documents']++;break;
                case 5:$data['course_details']['documents']++;break;
                case 6:$data['course_details']['documents']++;break;
                case 3:$data['course_details']['assessments']++;break;
                case 8:$data['course_details']['assessments']++;break;
                case 7:$data['course_details']['live_lectures']++;$data['course_details']['videos_length']      += $lecture['ll_duration']*60;break;
                case 4:$data['course_details']['videos']++;$data['course_details']['videos_length']        += $lecture['cl_duration'];break;
                case 9:$data['course_details']['videos']++;break;
                case 11:$data['course_details']['videos']++;break;
                case 12:$data['course_details']['videos']++;break;

            }
            $data['course_details']['total_lectures']++;
        }
        //echo $data['course_details']['videos_length'];die;
        $data['rating_per_page']                          = 4;
        $total_rating_temp                                = $this->Course_model->db_get_rating(array('course_id'=>$c_id));
        $data['course_details']['ratings']                = array_slice($total_rating_temp,0,$data['rating_per_page']);
        $data['course_details']['rating']['total_ratings']= count($total_rating_temp);
        $data['course_details']['rating']['one']          = 0;
        $data['course_details']['rating']['two']          = 0;
        $data['course_details']['rating']['three']        = 0;
        $data['course_details']['rating']['four']         = 0;
        $data['course_details']['rating']['five']         = 0;
        $rating_temp = 0;
        foreach ($total_rating_temp as $rating){
            switch($rating['cc_rating']){
                case 1:$data['course_details']['rating']['one']++;break;
                case 2:$data['course_details']['rating']['two']++;break;
                case 3:$data['course_details']['rating']['three']++;break;
                case 4:$data['course_details']['rating']['four']++;break;
                case 5:$data['course_details']['rating']['five']++;break;
            }
            $rating_temp+=$rating['cc_rating'];
        }
        $data['course_details']['rating']['average_rating'] = (!empty($total_rating_temp))?round($rating_temp/$data['course_details']['rating']['total_ratings'],1):0;
        

        $data['course_details']['sections'] = $this->Course_model->sections(array('course_id' => $c_id,'status'=>'1','order_by'=>'s_order_no','direction'=>'ASC'));
        $data['course_details']['lecture_count'] = $this->Course_model->lectures(array('direction'=>'ASC', 'status' => '1' , 'not_deleted'=>true, 'count' => true, 'course_id'=>  $c_id));
        $max_count = 10;
        $current_count = $max_count;
        foreach ($data['course_details']['sections'] as $key => $section){
            if($current_count>0){
                $data['course_details']['sections'][$key]['lectures']  = $this->Course_model->get_lectures(array('direction'=>'ASC','limit'=>$current_count, 'status' => 1 , 'order_by'=>'cl_order_no', 'course_id'=>  $c_id, 'section_id' => $section['id']));
                $current_count -= count($data['course_details']['sections'][$key]['lectures']);
                foreach ($data['course_details']['sections'][$key]['lectures'] as $key1 => $lecture){
                    $data['course_details']['sections'][$key]['lectures'][$key1]['unique'] = '';
                    switch($lecture['cl_lecture_type']){
                        case 1:
                            $num = gmdate("i",$lecture['cl_duration'])+(gmdate("H",$lecture['cl_duration'])*60);

                            $data['course_details']['sections'][$key]['lectures'][$key1]['unique'] = isset($lecture['cl_duration'])?(gmdate("i",$lecture['cl_duration'])+(gmdate("H",$lecture['cl_duration'])*60)<=9?sprintf("%02d", $num):gmdate("i",$lecture['cl_duration'])+(gmdate("H",$lecture['cl_duration'])*60)).':'.gmdate("s",$lecture['cl_duration']):'00:00';
                        break;
                        
                        case 12:
                            $num = gmdate("i",$lecture['cl_duration'])+(gmdate("H",$lecture['cl_duration'])*60);

                            $data['course_details']['sections'][$key]['lectures'][$key1]['unique'] = isset($lecture['cl_duration'])?(gmdate("i",$lecture['cl_duration'])+(gmdate("H",$lecture['cl_duration'])*60)<=9?sprintf("%02d", $num):gmdate("i",$lecture['cl_duration'])+(gmdate("H",$lecture['cl_duration'])*60)).':'.gmdate("s",$lecture['cl_duration']):'00:00';
                        break;

                        case 2:
                            $data['course_details']['sections'][$key]['lectures'][$key1]['unique'] = $lecture['cl_total_page'];
                        break;

                        case 8:
                            $data['course_details']['sections'][$key]['lectures'][$key1]['unique'] = $lecture['cl_total_page'];
                        break;

                        case 3:
                            $data['course_details']['sections'][$key]['lectures'][$key1]['unique'] = $this->Course_model->db_get_question_count($lecture['id']);
                        break;

                        case 4:
                            $data['course_details']['sections'][$key]['lectures'][$key1]['unique'] = '';
                        break;

                        case 5:
                            $data['course_details']['sections'][$key]['lectures'][$key1]['unique'] = '';
                        break;

                        case 6:
                            $data['course_details']['sections'][$key]['lectures'][$key1]['unique'] = '';
                        break;

                        case 7:
                            $data['course_details']['sections'][$key]['lectures'][$key1]['unique'] = $this->Course_model->db_get_ll_duration($lecture['id']);
                            $data['course_details']['sections'][$key]['lectures'][$key1]['unique'] = $data['course_details']['sections'][$key]['lectures'][$key1]['unique']['ll_duration']*60;
                            $num = gmdate("i",$data['course_details']['sections'][$key]['lectures'][$key1]['unique'])+(gmdate("H",$data['course_details']['sections'][$key]['lectures'][$key1]['unique'])*60);

                            $data['course_details']['sections'][$key]['lectures'][$key1]['unique'] = isset($data['course_details']['sections'][$key]['lectures'][$key1]['unique'])?(gmdate("i",$data['course_details']['sections'][$key]['lectures'][$key1]['unique'])+(gmdate("H",$data['course_details']['sections'][$key]['lectures'][$key1]['unique'])*60)<=9?sprintf("%02d", $num):gmdate("i",$data['course_details']['sections'][$key]['lectures'][$key1]['unique'])+(gmdate("H",$data['course_details']['sections'][$key]['lectures'][$key1]['unique'])*60)).':'.gmdate("s",$data['course_details']['sections'][$key]['lectures'][$key1]['unique']):'00:00';
                        break;

                    }
                }
            }
            
        }
        //echo '<pre>';print_r($data['course_details']['sections']);die;
        $data['discussions_per_page']                          = 4;
        $data['child_limit']                                   = 2;
        $data['course_details']['discussion_count'] = $this->Course_model->db_comments_count(array('c_id'=>$c_id));
        $data['course_details']['sections_count']   = $this->Course_model->db_section_count(array('course_id'=>$c_id));
        $data['course_details']['discussions']   = $this->Course_model->db_get_comments(array('u_id'=>$this->__student['id'],'c_id'=>$c_id,'order_by'=>'ASC','offset'=>'0','limit'=>$data['discussions_per_page'],'child_limit'=>$data['child_limit']),0);
        $assessments = $this->Course_model->db_get_assesments(array('select'=>'assessments.id,','course_id'=>$c_id));
        //echo '<pre>'; print_r($assessments); die('sdhsgfhg');
        $assessment_detail = '';
        $assessment_categories = '';
        $rank = 0;
        foreach ($assessments as $key => $assessment) {
            $rank = 0;
            $all_attempts                   = $this->Report_model->attempts(array('assessment_id' => $assessment['id']));
            $assessment_detail  = $this->Report_model->assesment(array('assessment_id' => $assessment['id']));
            $data['course_details']['rank_graph'][$key]['lecture']  = $this->Course_model->lecture(array('id' => $assessment_detail['a_lecture_id'],'select'=>'cl_lecture_name'));
            $data['course_details']['rank_graph'][$key]['my_rank'] = 0;
            $data['course_details']['rank_graph'][$key]['attempts'] = count($all_attempts); 
            if(!empty($all_attempts))
            {
                foreach ($all_attempts as $all_attempt)
                { 
                    $rank++;
                    if($all_attempt['aa_user_id'] == $this->__student['id'])
                    {
                        $data['course_details']['rank_graph'][$key]['my_rank']            = $rank;
                        $data['course_details']['rank_graph'][$key]['date']    = date("M j, Y", strtotime( $all_attempt['aa_attempted_date']));
                        break;
                    }
                }

            }
        }


        $temp_attempt = '';
        $temp_name    = '';
        if(!empty($assessments)){
            $assessment_categories = $this->Course_model->db_question_categories_in_assesment($assessments);
        
        foreach ($assessment_categories as $i => $assessment_category) {

            foreach ($assessments as $j => $assessment) {
                $temp_name                                = $this->Course_model->db_assesment_name($assessment['id']);
                $assessment_categories[$i]['assessment'][$j]['assessment_name'] = $temp_name['cl_lecture_name'];
                $temp_name                                  = $this->Course_model->db_total_marks(array('category_id'=>$assessment_category['id'],'assessment_id'=>$assessment['id']));
                $assessment_categories[$i]['assessment'][$j]['total_mark']      = $temp_name['total_mark'];
                $temp_attempt = $this->Course_model->db_latest_attempt(array('user_id'=>$this->__student['id'],'assessment_id'=>$assessment['id']));
                if($assessment_categories[$i]['assessment'][$j]['total_mark'] == ''){
                    $assessment_categories[$i]['assessment'][$j]['total_mark'] = -1;
                    $assessment_categories[$i]['assessment'][$j]['scored_mark'] = 0;
                }
                if(isset($temp_attempt['id'])){
                    $assessment_categories[$i]['assessment'][$j]['attended'] = 1;
                    $temp_name                                  = $this->Course_model->db_scored_marks(array('category_id'=>$assessment_category['id'],'assessment_id'=>$assessment['id'],'attempt_id'=>$temp_attempt['id']));
                    $assessment_categories[$i]['assessment'][$j]['scored_mark']     = $temp_name['scored_mark'];
                    $assessment_categories[$i]['assessment'][$j]['scored_mark']=($assessment_categories[$i]['assessment'][$j]['scored_mark'] == '')?0:$assessment_categories[$i]['assessment'][$j]['scored_mark'];
                }else{
                    $assessment_categories[$i]['assessment'][$j]['attended'] = 0;
                    $assessment_categories[$i]['assessment'][$j]['scored_mark']     = 0;
                }
            }

        }
        }
        $data['course_details']['assessment_categories'] = $assessment_categories;
        
        //echo '<pre>';print_r($data['course_details']['assessment_categories']);die;
        $this->load->view($this->config->item('theme').'/course_dashboard',$data);
        
    }

    function get_ajax_assesments(){
        
        $this->load->model(array('Report_model'));
        $course_id          = $this->input->post('course_id');
        $offset_recieved    = $this->input->post('offset');
        $per_page           = 3;
        $offset             = 0;
        if($offset_recieved && $offset_recieved !=0){
            $offset         = $offset_recieved * $per_page;
        }
        
        $param['offset']    = $offset;
        $param['limit']     = $per_page;
        $param['c_id']      = $course_id;
        $course_id          = ($course_id)?$course_id:0;

        $data                   = array();
        $data['assessments']    = array();
        $assessments            = $this->Course_model->db_get_assesments_detail(array('course_id'=>$course_id,'offset'=>$param['offset'],'limit'=>$param['limit']));
        foreach ($assessments as $key => $assessment){
            //echo '<pre>';print_r($assessment['cl_lecture_name']);
            $data['assessments'][$key]['name']          = $assessment['cl_lecture_name'];
            $data['assessments'][$key]['id']            = $assessment['id'];
            $data['assessments'][$key]['lecture_id']    = $assessment['lecture_id'];
            $attempts = $this->Report_model->attempts(array('assessment_id'=>$assessment['id']));
            foreach ($attempts as $key1 => $attempt){
                if($attempt['aa_user_id'] == $this->__student['id']){
                    $minutes                                                = floor($attempt['aa_duration'] / 60);
                    $seconds                                                = $attempt['aa_duration'] % 60;
                    $data['assessments'][$key]['attempt']['time_taken']     = $minutes.'.'.$seconds;
                    $data['assessments'][$key]['attempt']['attented_date']  = date("M j Y", strtotime( $attempt['aa_attempted_date']));
                    $data['assessments'][$key]['attempt']['rank']           = $key1+1;
                    $data['assessments'][$key]['attempt']['marks_obtained'] = $attempt['total_mark'];
                    $data['assessments'][$key]['attempt']['attempt_id']     = $attempt['id'];
                    break;
                }
            }
        }
        //echo '<pre>';print_r($data);die;

        echo json_encode($data);
    }

    function get_full_curriculum_json(){
        $course_id          = $this->input->post('c_id');

        $response               = array();
        $param                  = array();
        
        $response['sections'] = $this->Course_model->sections(array('course_id' => $course_id,'offset'=>'0', 'limit' => '', 'status'=>'1','order_by'=>'s_order_no','direction'=>'ASC'));
        foreach ($response['sections'] as $key => $section){
            $response['sections'][$key]['lectures']  = $this->Course_model->get_lectures(array('direction'=>'ASC', 'status' => 1 , 'order_by'=>'cl_order_no', 'course_id'=>  $course_id, 'section_id' => $section['id']));
            foreach ($response['sections'][$key]['lectures'] as $key1 => $lecture){
                $response['sections'][$key]['lectures'][$key1]['unique'] = '';
                
                switch($lecture['cl_lecture_type']){
                    case 1:
                        $num = gmdate("i",$lecture['cl_duration'])+(gmdate("H",$lecture['cl_duration'])*60);

                        $response['sections'][$key]['lectures'][$key1]['unique'] = isset($lecture['cl_duration'])?(gmdate("i",$lecture['cl_duration'])+(gmdate("H",$lecture['cl_duration'])*60)<=9?sprintf("%02d", $num):gmdate("i",$lecture['cl_duration'])+(gmdate("H",$lecture['cl_duration'])*60)).':'.gmdate("s",$lecture['cl_duration']):'00:00';
                    break;

                    case 2:
                        $response['sections'][$key]['lectures'][$key1]['unique'] = $lecture['cl_total_page'];
                    break;

                    case 3:
                        $response['sections'][$key]['lectures'][$key1]['unique'] = $this->Course_model->db_get_question_count($lecture['id']);
                    break;

                    case 8:
                        $response['sections'][$key]['lectures'][$key1]['unique'] = $lecture['cl_total_page'];
                    break;

                    case 4:
                        $response['sections'][$key]['lectures'][$key1]['unique'] = '';
                    break;

                    case 5:
                        $response['sections'][$key]['lectures'][$key1]['unique'] = '';
                    break;

                    case 6:
                        $response['sections'][$key]['lectures'][$key1]['unique'] = '';
                    break;

                    case 7:
                        $response['sections'][$key]['lectures'][$key1]['unique'] = $this->Course_model->db_get_ll_duration($lecture['id']);
                        $response['sections'][$key]['lectures'][$key1]['unique'] = $response['sections'][$key]['lectures'][$key1]['unique']['ll_duration']*60;
                        $num = gmdate("i",$response['sections'][$key]['lectures'][$key1]['unique'])+(gmdate("H",$response['sections'][$key]['lectures'][$key1]['unique'])*60);

                        $response['sections'][$key]['lectures'][$key1]['unique'] = isset($response['sections'][$key]['lectures'][$key1]['unique'])?(gmdate("i",$response['sections'][$key]['lectures'][$key1]['unique'])+(gmdate("H",$response['sections'][$key]['lectures'][$key1]['unique'])*60)<=9?sprintf("%02d", $num):gmdate("i",$response['sections'][$key]['lectures'][$key1]['unique'])+(gmdate("H",$response['sections'][$key]['lectures'][$key1]['unique'])*60)).':'.gmdate("s",$response['sections'][$key]['lectures'][$key1]['unique']):'00:00';
                    break;

                }
            }
        }
        //echo '<pre>';print_r($response['teachers']);;
        echo json_encode($response);
    }

    function get_rating_json(){
        $offset_recieved    = $this->input->post('offset');
        $course_id          = $this->input->post('c_id');
        $response               = array();
        $response['error']      = false;
        $param                  = array();
        
        //calculating page numbers
        $per_page           = 4;
        $page_num           = 1;
        $offset             = 0;
        if(  $offset_recieved && $offset_recieved != 1 )
        {
            $page_num       = $offset_recieved;
            $offset         = $offset_recieved * $per_page;
            $offset         = ($offset - $per_page);
        }
        $param['offset']    = $offset;
        $param['limit']     = $per_page;
        $param['course_id'] = $course_id;
        //end of calucalting page number
        
        $ratings                = $this->Course_model->db_get_rating($param);
        $response['ratings']    = $ratings;
        //echo '<pre>';print_r($response['teachers']);;
        echo json_encode($response);
    }
    //Ends By Alex

    function generate_certificate()     
    {       
        $course_id              =  $this->input->post('course_id');     
        // $user                   =  $this->auth->get_current_user_session('user');       
        $course                 =  $this->Course_model->course(array('id' => $course_id));      

        //make sure the subscription is not end     
        $data['course_details_saved']   = $course;              
        $data['attemp_zero_percentage'] = $this->Course_model->calculate_lecture_log(array('user_id'=>$this->__student['id'], 'course_id'=>$course_id, 'attempt'=>'zero'));        
        $data['attemp_full_percentage'] = $this->Course_model->calculate_lecture_log_full(array('user_id'=>$this->__student['id'], 'course_id'=>$course_id));      

        /*if(!$data['attemp_zero_percentage'])        
        {       
            $data['completed_percentage']   = $data['attemp_full_percentage'];      
        }       
        if(!$data['attemp_full_percentage'])        
        {       
            $data['completed_percentage']   =  $data['attemp_zero_percentage'];     
        }       
        if($data['attemp_full_percentage'] && $data['attemp_zero_percentage'])      
        {       
            $data['completed_percentage']   = ($data['attemp_zero_percentage'] + $data['attemp_full_percentage'])/2;        
        } */
        $data['completed_percentage']   = $data['attemp_zero_percentage'];      
        
        echo json_encode($data);        
    }   

    function save_remain_preview_time($id=false) 
    {       

        $course_id              = $this->input->post('course_id');      
        // $user                   = $this->auth->get_current_user_session('user');        
        $user_id                = $this->__student['id'];      
        $course                 = $this->Course_model->course(array('id'=> $course_id));        
        $course_preview_time    = $course['cb_preview_time'];       

        $get_user_time          = $this->Course_model->get_user_preview_time(array('course_id'=> $course_id, 'user_id'=> $user_id));        
        $user_preview_time      = $get_user_time['cpt_course_time'];        

        //$data['id']                 = $id;        
        if($get_user_time['id']) 
        {       
            $data['id']             = $get_user_time['id'];     
        }       


        $data['cpt_course_id']      = $course_id;       
        // $user                       = $this->auth->get_current_user_session('user');        
        $data['cpt_user_id']        = $this->__student['id'];      
        $data['cpt_course_time']    = ($user_preview_time == '0')?'5':($user_preview_time + 5);     

        $this->Course_model->save_remain_preview($data);        
        echo json_encode($data);        
    }

    function save_exam()
    {
        $debug                                 = array();
        // $user                                  = $this->auth->get_current_user_session('user');
        $assesment_id                          = $this->input->post('assesment_id');
        $answers                               = $this->input->post('answer');
        $check_need_valuation                  = false;
        $total_mark                            = 0;

        $answer_queues                         = $this->input->post('answer_queue');
        $this->_answer_queue                   = json_decode($answer_queues);


        $attempt_id                            = $this->input->post('attempt_id');
        $this->_answers                        = json_decode($answers);
        $this->_answers                        = $this->_answers->answer;
        $answer_time_log                       = json_decode($this->input->post('answer_time_log'));
        
        $assesment_objects                     = array();
        $assesment_objects['key']              = 'assesment_'.$assesment_id;

        $assesment_callback                    = 'assesment_details';
        $assesment_params                      = array();
        $assesment_params['assesment_id']      = $assesment_id;

        $assesment_details                     = $this->memcache->get($assesment_objects, $assesment_callback, $assesment_params);
        $assesment                             = $assesment_details['assesment_details'];
        $questions                             = $assesment_details['questions'];
        $course_id                             = $assesment_details['assesment_details']['a_course_id'];

        //Subscription invalidation update 
        $save_subs                            = array();
        $save_subs['cs_user_id']              = $this->__student['id'];
        $save_subs['cs_course_id']            = $course_id;
        $save_subs['cs_invalidate_topic']     = '1';
        $this->Course_model->save_last_played_lecture($save_subs); 
        $this->invalidate_subscription(array('user_id'=>$this->__student['id'],'course_id'=>$course_id));
        //End invalidation update.

        //getting attempt details
        //$this->memcache->delete('attempt_'.$attempt_id);
        $objects        = array();
        $objects['key'] = 'attempt_'.$attempt_id;
        $attempt        = $this->memcache->get($objects);
        if(!$attempt)  
        {
            $attempt            = $this->Course_model->attempt(array('select'=>'aa_duration,aa_assessment_detail','id'=>$attempt_id));
            $this->memcache->set($objects['key'], $attempt);
        } 
        // echo '<pre>'; print_r($attempt);die();
        //End
                
        //saving the assesment attempts
        $assessment_json                           = ($attempt['aa_assessment_detail']!=NULL)?json_decode($attempt['aa_assessment_detail'], true):array();
        if(empty($assessment_json)) {
            $exam_submission_data                  = array();
            $exam_submission_data['assesment_id']  = $assesment_id;
            $exam_submission_data['attempt_id']    = $attempt_id;
            $exam_submission_data['user_id']       = $this->__student['id'];
            $exam_submission_data['course_id']     = $assesment['a_course_id'];
            $exam_submission_data['lecture_id']    = $assesment['a_lecture_id'];
            if(!empty($questions))
            {
                foreach($questions as $question)
                {

                    $question_topic                                                         = array();
                    $question_topic['id']                                                   = $question['q_topic'];
                    $question_topic['topic_name']                                           = $question['qt_topic_name'];
                    $question_subject                                                       = array();
                    $question_subject['id']                                                 = $question['q_subject'];
                    $question_subject['subject_name']                                       = $question['qs_subject_name'];
                    $question['q_question'][1]                                              = strip_tags($question['q_question'][1]);
                    $question_options                                                       = $question['options'];
                    $options                                                                = array();
                    $option_count   = intval(0);
                    foreach($question_options as $question_option)
                    {
                        $option_value                            = '';
                        $options[$option_count]['id']            = $question_option['id'];
                        $options[$option_count]['qo_options'][1] = strip_tags($question_option['qo_options'][1],"<img>");
                        $option_count++;
                    }
                    $question['options']                                  = $options;
                    $question['q_explanation'][1]                         = strip_tags($question['q_explanation'][1]);
                    $exam_submission_data['questions'][$question['id']]                     = array();
                    $exam_submission_data['questions'][$question['id']]['report_id']        = '';
                    $exam_submission_data['questions'][$question['id']]['type']             = $question['q_type'];
                    $exam_submission_data['questions'][$question['id']]['q_question']       = $question['q_question'];
                    $exam_submission_data['questions'][$question['id']]['q_option']         = json_encode($question['options']);
                    $exam_submission_data['questions'][$question['id']]['q_actual_answer']  = isset($question['correct_answer'])?$question['correct_answer']:array();
                    $exam_submission_data['questions'][$question['id']]['q_negative_mark']  = $question['aq_negative_mark'];
                    $exam_submission_data['questions'][$question['id']]['q_positive_mark']  = $question['aq_positive_mark'];
                    $exam_submission_data['questions'][$question['id']]['q_explanation']    = $question['q_explanation'];
                    $exam_submission_data['questions'][$question['id']]['subject']          = json_encode($question_subject);
                    $exam_submission_data['questions'][$question['id']]['topics']           = json_encode($question_topic);
                    $exam_submission_data['questions'][$question['id']]['answer_time_log']  = '';
                    $exam_submission_data['questions'][$question['id']]['user_answers']     = '';
                    $exam_submission_data['questions'][$question['id']]['user_mark']        = '';
                }
            }
        } else {
            $exam_submission_data = $assessment_json;
        }

    //    echo '<pre>'; print_r($exam_submission_data);die();

        if(!empty($this->_answer_queue)) {
            foreach($this->_answer_queue as $key => &$answer_queue)
            {
                $save                            = array();
                $question_id                     = $answer_queue->question_id;
                $duration                        = $answer_queue->duration;
                $report_id                       = ($exam_submission_data['questions'][$question_id]['report_id']!='')?$exam_submission_data['questions'][$question_id]['report_id']:0;
                if($report_id!=0)
                {
                    $attempt_duration            = ($exam_submission_data['questions'][$question_id]['answer_time_log']!='')?$exam_submission_data['questions'][$question_id]['answer_time_log']:0;
                    $save['id']                  = $report_id;
                    $save['ar_duration']         = $attempt_duration+$duration;        
                }
                else
                {
                    $save['id']                  = false;
                    $save['ar_duration']         = $duration;             
                }
                $save['ar_attempt_id']           = $attempt_id;
                $save['ar_question_id']          = $key;
                $save['ar_course_id']            = $assesment['a_course_id'];
                $save['ar_lecture_id']           = $assesment['a_lecture_id'];
                $save['ar_user_id']              = $this->__student['id'];
                $save['ar_duration']             = $duration;
                switch ($questions[$key]['q_type'])
                {
                    case "1":
                        $answer                  = isset($answer_queue->answer)?$answer_queue->answer:'';
                        $save['ar_answer']       = $answer;
                        // $correct_answer          = $question['q_answer'];
                        if($questions[$key]['q_answer'] == $answer)
                        {
                            $save['ar_mark']     = isset($questions[$key]['aq_positive_mark'])?$questions[$key]['aq_positive_mark']:$questions[$key]['q_positive_mark'];
                        }
                        else
                        {
                            $save['ar_mark'] = 0;
                            if($answer)
                            {
                                $save['ar_mark'] = isset($questions[$key]['aq_negative_mark'])?$questions[$key]['aq_negative_mark']:$questions[$key]['q_negative_mark'];
                                $save['ar_mark'] = $save['ar_mark'];                                                         
                            }
                        }



                        // $answer                  = $answer_queue->answer;
                        // $save['ar_answer']       = $answer;
                        // if($questions[$key]['q_answer'] == $answer)
                        // {
                        //     $save['ar_mark']     = isset($questions[$key]['aq_positive_mark'])?$questions[$key]['aq_positive_mark']:$questions[$key]['q_positive_mark'];
                        // }
                        // else
                        // {
                        //     $save['ar_mark']     = 0;
                        //     if($answer)
                        //     {
                        //         $save['ar_mark'] = isset($questions[$key]['aq_negative_mark'])?$questions[$key]['aq_negative_mark']:$questions[$key]['q_negative_mark'];
                        //         $save['ar_mark'] = $save['ar_mark'];                                                       
                                
                        //     }
                        // }
                        
                    break;

                    case "2":
                        $answer         = $answer_queue->answer;
                        $correct_answer = $questions[$key]['q_answer'];
                        $save['ar_answer']          = $answer;
                        if($questions[$key]['q_answer'] == $answer)
                        {
                            $save['ar_mark']        = isset($questions[$key]['aq_positive_mark'])?$questions[$key]['aq_positive_mark']:$questions[$key]['q_positive_mark'];
                        }
                        else
                        {
                            $save['ar_mark'] = 0;
                            if($answer)
                            {
                                $save['ar_mark']         = isset($questions[$key]['aq_negative_mark'])?$questions[$key]['aq_negative_mark']:$questions[$key]['q_negative_mark'];
                                $save['ar_mark']         = $save['ar_mark'];                                                       
                            }
                        }
                            

                        // $key_answer              = explode(',', $questions[$key]['q_answer']);
                        // $answer                  = (array)$answer_queue->answer;
                        // sort($key_answer);
                        // sort($answer);
                        // $key_answer              = implode(',', $key_answer);
                        // $answer                  = implode(',', $answer);
                        // $save['ar_answer']       = $answer;
                        // if($key_answer == $answer)
                        // {
                        //     $save['ar_mark']     = isset($questions[$key]['aq_positive_mark'])?$questions[$key]['aq_positive_mark']:$questions[$key]['q_positive_mark'];                       
                        // }
                        // else
                        // {
                        //     $save['ar_mark']     = 0;
                        //     if($answer)
                        //     {
                            
                        //         $save['ar_mark'] = isset($questions[$key]['aq_negative_mark'])?$questions[$key]['aq_negative_mark']:$questions[$key]['q_negative_mark']; 
                        //         $save['ar_mark'] = (int)$save['ar_mark'];                                                       
                        //     }
                        // }

                    break;

                    case "3":
                        $answer                  = $answer_queue->answer;
                        $save['ar_answer']       = $answer_queue->answer;
                        $save['ar_mark']         = '';
                    break;

                    case "4":
                        $answer                  = $answer_queue->answer;
                        $save['ar_answer']       = $answer_queue->answer;
                        $save['ar_mark']         = '';
                    break;
                }
                $exam_submission_data['questions'][$question_id]['answer_time_log'] = $duration;
                $exam_submission_data['questions'][$question_id]['user_answers']    = $answer;
                $exam_submission_data['questions'][$question_id]['user_mark']       = $save['ar_mark'];
                $save_assessment_report           = $this->Course_model->save_assessment_report($save);
                $exam_submission_data['questions'][$question_id]['report_id']       = $save_assessment_report;
            }
            
        }
        // echo '<pre>'; print_r($exam_submission_data['questions']);die;

        if(!empty($assesment_details['questions']))
        {
            foreach($assesment_details['questions'] as $question)
            {
                switch ($question['q_type'])
                {
                    case "3":
                        $check_need_valuation    = true;
                    break;
                    case "4":
                        $check_need_valuation    = true;
                    break;
                }
            }
        }
        foreach($exam_submission_data['questions'] as $report_object) {
            $total_mark += $report_object['user_mark'];
        }
       
        $json_submission_data                   = json_encode($exam_submission_data);
        $attempt_param                          = array();
        $attempt_param['id']                    = $attempt_id;
        $attempt_param['aa_assessment_detail']  = $json_submission_data;
        $time_taken                             = $this->input->post('time_taken');
        $attempt_param['aa_duration']           = ($time_taken)?$time_taken:'0';
        $attempt_param['aa_completed']          = "1";
        $attempt_param['aa_mark_scored']        = $total_mark;
        $attempt_param['aa_total_mark']         = $assesment['a_mark'];

        //Updating lecture grade in course subscription table
        $log_param                              = array();
        $log_param['course_id']                 = $assesment['a_course_id'];
        $log_param['lecture_id']                = $assesment['a_lecture_id'];
        $log_param['user_id']                   = $this->__student['id'];
        //End of Updating lecture grade in course subscription table
        
        if($check_need_valuation == true)
        {
            //$debug['check_need_valuation'] = 'in';
            $attempt_param['aa_valuated']        = "0";
            $log_param['grade']                  = "-";
        } 
        else 
        {
            //$debug['check_need_valuation'] = 'out';
            $assessment_mark                     = $assesment['a_mark'];
            if($total_mark>=0)
            {
                $grade_percentage                = (($total_mark/$assessment_mark)*100);
            } else {
                $grade_percentage                = '0';
            }
            $grade                               = convert_percentage_to_grade($grade_percentage);
            $attempt_param['aa_valuated']        = "1";
            $attempt_param['aa_grade']           = $grade['gr_name'];

            //Updating lecture grade in course subscription table
            $log_param['grade']                  = $grade['gr_name'];
            $log_param['percentage_of_marks']    = $grade_percentage;
            //End of Updating lecture grade in course subscription table
        }
        // echo '<pre>';print_r($log_param);die;
        update_lecture_log_wiht_subscription($log_param);
        
        $submit_exam = $this->Course_model->save_assessment_attempts($attempt_param);
        if($submit_exam)
        {
            // $user               = $this->auth->get_current_user_session('user');
            $template           = $this->ofabeemailer->template(array('email_code' => 'assessment_submission_success'));
            $param['to']        = $this->__student['us_email'];
            $param['subject']   = $template['em_subject'];
            $contents = array(
                'site_name' => config_item('site_name')
                , 'username' => $this->__student['us_name']
                , 'course_title' => $assesment['a_course_title']
                , 'assessment'   => $assesment['a_title']
                , 'site_url' => site_url().'course/dashboard/'.$course_id.'?tab=quiz'
                , 'date' => date('d-m-Y')
            );
            // echo "<pre>";print_r($contents);exit;
            $param['body']      = $this->ofabeemailer->process_mail_content($contents, $template['em_message']);
            $this->ofabeemailer->send_mail($param);

            /*$preveleged_users = $this->accesspermission->previleged_users(array('module' => 'course_content'));
            foreach($preveleged_users as $preveleged_user)
            {
            $notify_to[$preveleged_user['id']] = array($this->__student['id']);
            }*/

            $objects                = array();
            $objects['key']         = 'course_notification_' . $course_id;
            $callback               = 'course_notification';
            $params                 = array('course_id' => $course_id);
            $discussion_forum       = $this->memcache->get($objects, $callback, $params);

            $preveleged_users       = $discussion_forum['preveleged_users'];

            foreach($preveleged_users as $preveleged_user)
            {
                $notify_to[$preveleged_user] = array($this->__student['id']);
            }
            
            //Notification
            $this->load->library('Notifier');
            $this->notifier->push(
                array(
                    'action_code' => 'quiz_submitted',
                    'assets' => array('quiz_name' => $assesment['a_title'],'course_name' => $assesment['a_course_title'],'course_id'=>$log_param['course_id']),
                    'target' => $assesment['a_lecture_id'],
                    'individual' => false,
                    'push_to' => $notify_to
                )
            );
            //End notification

            /*Log creation*/
            $user_data                              = array();
            $user_data['user_id']                   = $this->__student['id'];
            $user_data['username']                  = $this->__student['us_name'];
            $user_data['useremail']                 = $this->__student['us_email'];
            $user_data['user_type']                 = $this->__student['us_role_id'];
            $user_data['phone_number']              = $this->__student['us_phone'];
            $message_template                       = array();
            $message_template['username']           = $this->__student['us_name'];
            $message_template['quiz_title']         = $assesment['a_title'];
            $message_template['course_name']        = $assesment['a_course_title'];
            $triggered_activity                     = 'quiz_submitted';
            log_activity($triggered_activity, $user_data, $message_template);
            /*End log creation*/

            $delete_key        = 'score_'.$this->__student['id'];
            $this->memcache->delete($delete_key);

        }
        echo json_encode(array('error' => 'false', 'message' => 'Report submitted successfully', 'attempt_id' => $attempt_id, 'debug' => $debug));
    }
    
    function download_question_paper($id)
    {

        $result = $this->Course_model->get_descriptive_test_item($id);
        $path   = descriptive_question_path().$result['dt_file'];

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename=test.pdf');
        header('Pragma: no-cache');
        readfile($path);
    }
    
    function language()
    {
        $response               = array();
        $response['language']   = array();
        $response['language']   = get_instance()->lang->language;
        echo json_encode($response);
    }
    
    function last_played_lecture()
    {
        // $user           = $this->auth->get_current_user_session('user');
        $course_id      = $this->input->post('course_id');
        $subscription   = $this->User_model->subscription(array('user_id' => $this->__student['id'], 'course_id' => $course_id)); 
        if(isset($subscription['cs_last_played_lecture']) && $subscription['cs_last_played_lecture'] > 0)
        {
            echo json_encode(array('lecture_id' => $subscription['cs_last_played_lecture']));
        }
        else
        {
            echo json_encode(array('lecture_id' => $this->Course_model->get_first_lecture(array('course_id' => $course_id))));
        }
    }
    
    function save_rating_review()       
    {    
        // echo "haiiiii";exit;
        // $user           = $this->auth->get_current_user_session('user');   
        $course_id      = $this->input->post('course_id');      
        $rating_course  = $this->input->post('rating');     
        $review_course  = $this->input->post('review');
        $notify_to[config_item('us_id')]    = array($this->__student['id']);

        $today                              = date('Y-m-d H:i:s');
        $cc_admin_rating_id                 = md5($course_id." ".$today);
        
        $save_rating                        = array();
        $save_rating['cc_course_id']        = $course_id;
        $save_rating['cc_user_id']          = $this->__student['id'];
        $save_rating['cc_user_name']        = $this->__student['us_name'];
        $save_rating['cc_institute_id']     = $this->__student['us_institute_id'];
        $save_rating['cc_user_image']       = $this->__student['us_image']."?v=".rand(10,1000);
        $save_rating['cc_admin_rating_id']  = $cc_admin_rating_id;
        $save_rating['cc_rating']           = $rating_course;
        $save_rating['created_date']        = $today; 
        $save_rating['cc_reviews']          = $review_course;
        $save_rating['cc_status']           = '2';
        
        $rating_result = $this->Course_model->save_rating($save_rating);        
        $this->memcache->delete('subscription_'.$course_id.'_'.$this->__student['id']);
        $this->invalidate_course(array('course_id' => $course_id));
        if($rating_result){     
            //echo $this->db->last_query();exit;   
            echo json_encode('true');
            $institute              = $this->User_model->users(array( 'institute_id'=>$this->__student['us_institute_id'],'role_id'=>'8','status'=>'1','not_deleted'=>true, 'select' => 'users.id,users.us_email'));
            if(!empty($institute)) 
            {
                foreach($institute as $i_admin)
                {
                    $notify_to[$i_admin['id']] = array($this->__student['id']);
                }
            }
            // echo "<pre>";print_r($institute);exit;
            //Notify to Admin,I admin and priveleged users.
            /*$preveleged_users = $this->accesspermission->previleged_users(array('module' => 'course'));
            foreach($preveleged_users as $preveleged_user)
            {
                $notify_to[$preveleged_user['id']] = array($this->__student['id']);
            }*/

            $objects                = array();
            $objects['key']         = 'course_notification_' . $course_id;
            $callback               = 'course_notification';
            $params                 = array('course_id' => $course_id);
            $discussion_forum       = $this->memcache->get($objects, $callback, $params);

            $preveleged_users       = $discussion_forum['preveleged_users'];

            foreach($preveleged_users as $preveleged_user)
            {
                $notify_to[$preveleged_user] = array($this->__student['id']);
            }

            $objects        = array();
            $objects['key'] = 'course_'.$course_id;
            $callback       = 'course_details';
            $params         = array('id' => $course_id);
            $course_details         = $this->memcache->get($objects, $callback, $params); 
            
            $this->load->library('Notifier');
            $this->notifier->push(
                array(
                    'action_code' => 'course_rated',
                    'assets' => array('student_name'=>$this->__student['us_name'],'course_name' => $course_details['cb_title'],'course_id' => $course_id),
                    'target' => $course_id,
                    'individual' => false,
                    'push_to' => $notify_to
                )
            );
            //End notifying.

            /*Log creation*/
            $user_data                              = array();
            $user_data['user_id']                   = $this->__student['id'];
            $user_data['username']                  = $this->__student['us_name'];
            $user_data['useremail']                 = $this->__student['us_email'];
            $user_data['user_type']                 = $this->__student['us_role_id'];
            $user_data['phone_number']              = $this->__student['us_phone'];
            $message_template                       = array();
            $message_template['username']           = $this->__student['us_name'];
            $message_template['course_name']        = $course_details['cb_title'];
            $triggered_activity                     = 'course_rated';
            log_activity($triggered_activity, $user_data, $message_template);
            /*End log creation*/
        }       
    }


    function bundle_rating_review()       
    {    
        // echo "haiiiii";exit;
        // $user           = $this->auth->get_current_user_session('user');  
        $this->load->model('Bundle_model'); 
        $bundle_id      = $this->input->post('bundle_id');      
        $rating_course  = $this->input->post('rating');     
        $review_course  = $this->input->post('review');
        $notify_to[config_item('us_id')]    = array($this->__student['id']);
        //print_r($_POST);die;
        $today          = date('Y-m-d H:i:s');
        $cc_admin_rating_id     = md5($bundle_id." ".$today);
        
        $save_rating                        = array();
        $save_rating['cc_bundle_id']        = $bundle_id;
        $save_rating['cc_user_id']          = $this->__student['id'];
        $save_rating['cc_user_name']        = $this->__student['us_name'];
        $save_rating['cc_institute_id']     = $this->__student['us_institute_id'];
        $save_rating['cc_user_image']       = $this->__student['us_image']."?v=".rand(10,1000);
        $save_rating['cc_admin_rating_id']  = $cc_admin_rating_id;
        $save_rating['cc_rating']           = $rating_course;
        $save_rating['created_date']        = $today; 
        $save_rating['cc_reviews']          = $review_course;
        $save_rating['cc_status']           = '2';
        //print_R($save_rating);die;
        $rating_result = $this->Bundle_model->save_rating($save_rating);        
        $this->memcache->delete('my_bundle_subscription_'.$bundle_id.'_'.$this->__student['id']);
        
        $this->invalidate_course(array('bundle_' => $bundle_id));
        if($rating_result){     
            //echo $this->db->last_query();exit;   
            echo json_encode('true'); 
            $institute              = $this->User_model->users(array( 'institute_id'=>$this->__student['us_institute_id'],'role_id'=>'8','status'=>'1','not_deleted'=>true, 'select' => 'users.id,users.us_email'));
            if(!empty($institute)) 
            {
                foreach($institute as $i_admin)
                {
                    $notify_to[$i_admin['id']] = array($this->__student['id']);
                }
            }
            // echo "<pre>";print_r($institute);exit;
            //Notify to Admin,I admin and priveleged users.
            $preveleged_users = $this->accesspermission->previleged_users(array('module' => 'course'));
            foreach($preveleged_users as $preveleged_user)
            {
                $notify_to[$preveleged_user['id']] = array($this->__student['id']);
            }

            $objects        = array();
            $objects['key'] = 'bundle_'.$bundle_id;
            $callback       = 'bundle_details';
            $params         = array('id' => $bundle_id);
            $bundle_details         = $this->memcache->get($objects, $callback, $params); 
            
            $this->load->library('Notifier');
            $this->notifier->push(
                array(
                    'action_code' => 'bundle_rated',
                    'assets' => array('student_name'=>$this->__student['us_name'],'bundle_name' => $bundle_details['c_title'],'bundle_slug' => $bundle_details['c_slug'],'bundle_id' => $bundle_id),
                    'target' => $bundle_id,
                    'individual' => false,
                    'push_to' => $notify_to
                )
            );
            //End notifying.

            /*Log creation*/
            $user_data                              = array();
            $user_data['user_id']                   = $this->__student['id'];
            $user_data['username']                  = $this->__student['us_name'];
            $user_data['useremail']                 = $this->__student['us_email'];
            $user_data['user_type']                 = $this->__student['us_role_id'];
            $user_data['phone_number']              = $this->__student['us_phone'];
            $message_template                       = array();
            $message_template['username']           = $this->__student['us_name'];
            $message_template['bundle_name']        = $bundle_details['c_title'];
            $triggered_activity                     = 'bundle_rated';
            log_activity($triggered_activity, $user_data, $message_template);
            /*End log creation*/
        }       
    }
        
    function thankyou()
    {
        $this->load->view($this->config->item('theme'). '/thankyou');
    }
        
    function save_course_comment() 
    {       
        $comment        = $this->input->post('comment');        
        $course_id      = $this->input->post('course_id');      
        $parent_id      = $this->input->post('parent_id');      
        // $user           = $this->auth->get_current_user_session('user');        

        //$attempt        = $this->Course_model->get_answer_details( $lecture_id, $user['id']);     
        date_default_timezone_set("Asia/Kolkata");      
        $save                       = array();      
        $save['user_id']            = $this->__student['id'];      
        $save['created_date']       = date('Y-m-d H:i:s');      
        $save['comment_deleted']    = 0;        
        $save['comment']            = $comment;     
        $save['parent_id']          = $parent_id;       
        $save['course_id']          = $course_id;       
        $this->Course_model->save_course_comment($save);        

        $response                 = array();        

        $response['comments']     = array();        
        $comments                 = $this->Course_model->get_course_comments(array('course_id' => $course_id));     
        if(!empty($comments))       
        {       
            foreach ($comments as $comment)     
            {       
                //$comment['updated_date'] = date('M j, Y <br /> h:i a', strtotime($comment['updated_date']));      
                $response['comments'][] = $comment;     
            }       
        }       

        $response['user_image']    = (($this->__student['us_image'] == 'default.jpg')?default_user_path():user_path()).$this->__student['us_image'];      

        echo json_encode($response);        

    }

    function assesment_report_item($assessment_attempt_id = false)
    {
        $response                   = array();
        $response['error']          = 'false';
        $data                       = array();
        $lecture                    = array();
        $data['assesment_details']  = '';
        $data['lecture_name']       = '';
        
        //check the id exist or not
        if(!$assessment_attempt_id )
        {
           redirect(site_url());
        }
        else
        {
            $data['assessment_attempt_details']   = $this->Course_model->get_assessment_attempt_details(array('assessment_attempt_id' => $assessment_attempt_id));  
            if($data['assessment_attempt_details']['aa_valuated'] == 0){
                redirect(site_url('dashboard'));
            }
            
            if(!empty($data['assessment_attempt_details'])) 
            {
                $data['assesment_details']            = $this->Course_model->get_assesment(array('assesment_id' => $data['assessment_attempt_details']['aa_assessment_id']));
                $data['lecture_name']                 = $this->Course_model->get_lecture(array('lecture_id' => $data['assesment_details']['a_lecture_id']));
                $questions                            = $this->Course_model->questions(array('assesment_id' => $data['assessment_attempt_details']['aa_assessment_id']));
                
                $answers                              = $this->Course_model->answers(array('attempt_id' => $assessment_attempt_id));
                //$data['assessment_details']     = $this->Course_model->assessment_report_test(array('attempt_id' => $assessment_attempt_id)); 
                
                if(!empty($questions))
                {
                    foreach($questions as $question)
                    {
                        $lecture['category_questions'][$question['q_subject']][]          = $question['id'];
                        $lecture['question_type_wise'][$question['q_type']][]             = $question['id'];
                        $lecture['question_difficulty_type'][$question['q_difficulty']][] = $question['id'];
                        $question['options']                                              = $this->Course_model->options(array('q_answer' => $question['q_options']));
                        $lecture['questions'][$question['id']]                            = $question;
                    }
                }
          
                if(!empty($answers))
                {
                    foreach($answers as $answer)
                    {
                        $lecture['answers'][$answer['ar_question_id']]             = $answer;
                    }
                }

 
                $lecture['categories']          = array();
                $lecture['sl_no']               = array();
                $categories                     = $this->Course_model->exam_question_categories(array('assesment_id' => $data['assessment_attempt_details']['aa_assessment_id']));
                //echo '<pre>';print_r($lecture['category_questions']);die;
                $sl_no                          = 1;
                $test_pr                        = array();
                if(!empty($categories))
                {
                    foreach ($categories as $category)
                    {
                        $lecture['categories'][$category['subject_id']] = $category;
                        $lecture['sl_no'][$category['subject_id']]      = array();
                        if(isset($lecture['category_questions'][$category['subject_id']]) && !empty($lecture['category_questions'][$category['subject_id']]))
                        {
                            $test_pr[] = $lecture['category_questions'][$category['subject_id']];
                            foreach ($lecture['category_questions'][$category['subject_id']]  as $question_id)
                            {
                                $lecture['sl_no'][$category['subject_id']][$sl_no] = $question_id;
                                $sl_no++;
                            }
                        }
                    }
                }

                //echo '<pre>';print_r($lecture['category_questions']);echo '<br/>';
                //echo "<pre>";print_r($categories);die;
                $data['lecture']                = $lecture;
                $data['user_token']             = isset($_REQUEST['token'])?$_REQUEST['token']:'';
                $data['quick_report']           = isset($_REQUEST['quick_report'])?'true':'false';
                //echo '<pre>'; print_r($data);die;
                $this->load->view($this->config->item('theme').'/test_report', $data);
                
            }
            else
            {
                redirect(site_url());
            }
        }
    }

    /* Written By Alex */
    function get_answers($id = false){
        $response           = array();
        $question_id        = ($id)?$id:$this->input->post('question_id');
        if(!$question_id){
            $response['success'] = false;
            $response['message'] = 'Invalid question id';
            echo json_encode($response);
            exit(1);
        }
        $question           = $this->Course_model->get_question(array('question_id'=>$question_id));

        $options            = $this->Course_model->get_question_options($question['q_options']);

        $answers            = explode(',',$question['q_answer']);

        foreach ($options as $o_key => $option) {
            if(in_array($option['id'],$answers)){
                $options[$o_key]['stat'] = true;
            }else{
                $options[$o_key]['stat'] = false;
            }
        }
        $response['success'] = true;
        $response['message'] = 'Success question data fetched...';
        $response['question']= $question;
        $response['options'] = $options;

        //echo '<pre>'; print_r($response);die;

        echo json_encode($response);die;

    }
    /* End Written By Alex */
    // For online test by santhosh
    function test($assesment_id = 0)
    {   
        if(!$assesment_id)
        {
            redirect(site_url('dashboard'));  
        }
        $assesment_objects                   = array();
        $assesment_objects['key']            = 'assesment_'.$assesment_id;
        $assesment_callback                  = 'assesment_details';

        $assesment_params                    = array();
        $assesment_params['assesment_id']    = $assesment_id;
        //$this->memcache->delete('assesment_'.$assesment_id);
        $assesment_details                   = $this->memcache->get($assesment_objects, $assesment_callback, $assesment_params);
        if( !isset($assesment_details['assesment_details']) || empty($assesment_details['assesment_details']))
        {
            redirect(site_url('dashboard'));
        }
        $assesment      = $assesment_details['assesment_details'];
        $current_time   = strtotime(date('Y-m-d H:i:s'));
        // $user           = $this->auth->get_current_user_session('user');
        $check_attempt  = $this->Course_model->attempt(array('assesment_id' => $assesment_id, 'user_id' => $this->__student['id'],'order_by'=>'id','direction'=>'DESC','limit'=>'1','select' => 'id,aa_completed,aa_attempted_date, aa_duration'));
        $attempt_id     = 0;

        if(!empty($check_attempt))
        {
            $attempted_date = $check_attempt['aa_attempted_date'];
            if($attempted_date)
            {
                $last_attempted_time = strtotime("+".round($assesment['a_duration'])." minutes", strtotime($attempted_date));                
                if($current_time > $last_attempted_time)
                {
                    $attempt_id = 0;
                }
                else
                {
                    $attempt_id = ($check_attempt['aa_completed']==0)?$check_attempt['id']:0;                    
                }
            }
        }


        if($attempt_id == 0)
        {
            $update_attempts_status                       = array();
            $update_attempts_status['aa_assessment_id']   = $assesment_id;
            $update_attempts_status['aa_user_id']         = $this->__student['id'];
            $update_attempts_status['aa_latest']          = '0';
            $this->Course_model->update_assessment_status($update_attempts_status);

            $attempt                         = array();
            $attempt['id']                   = false;
            $attempt['aa_assessment_id']     = $assesment_id;
            $attempt['aa_user_id']           = $this->__student['id'];
            $attempt['aa_course_id']         = $assesment['a_course_id'];
            $attempt['aa_lecture_id']        = $assesment['a_lecture_id'];
            $attempt['aa_attempted_date']    = date('Y-m-d H:i:s');
            $attempt['aa_duration']          = 0;
            $attempt['aa_total_mark']        = $assesment['a_mark'];
            $attempt['aa_total_questions']   = $assesment['a_questions'];
            $attempt['aa_total_duration']    = $assesment['a_duration']*60;//convert to seconds
            $attempt['aa_grade_higher_value']= 0;
            $attempt_id                   = $this->Course_model->save_assessment_attempts($attempt);
        }
        $data                               = array();
        $data['assesment']                  = $assesment;
        $data['assesment_id']               = $assesment_id;
        $data['attempt_id']                 = $attempt_id; 
        $data['user_token']                 = $this->user_token;
        // echo '<pre>'; print_r($data);die;
        $this->load->view($this->config->item('theme'). '/assesment/online_test_beta', $data);
    }

    function test_assets($assesment_id = 0, $attempt_id = 0)
    {
        $assets                              = array();
        $assesment_objects                   = array();
        $assesment_objects['key']            = 'assesment_'.$assesment_id;
        $assesment_callback                  = 'assesment_details';
        $assesment_params                    = array();
        $assesment_params['assesment_id']    = $assesment_id;
        $assesment_details                   = $this->memcache->get($assesment_objects, $assesment_callback, $assesment_params);
        
        $questions                           = isset($assesment_details['questions']) ? $assesment_details['questions'] : '';
        $assesment                           = $assesment_details['assesment_details'];
        $assets['error']                     = false;
        if(!$assesment)
        {
            $assets['error']                 = true;
            $assets['message']               = 'Invalid Test id';
            echo json_encode($assets);die;
        }
        
        $assets['questions']                 = array();
        if($assesment['a_qgrouping'])
        {
            $assets['subjects']              = $this->Course_model->exam_question_categories(array('assesment_id' => $assesment_id));
        }
        $assets['subject_questions']         = array();
        $assets['question_type_wise']        = array();
        $assets['question_types']            = array('1' => array( 'SCQ', 'Single Choice'), '2' => array('MCQ', 'Multiple Choice'), '3' => array('SQ', 'Subjective'), '4' => array('SQ', 'Fill in the blanks'));
        $assets['questions']                 = array();
        $assets['questions_order']           = array();
        $assets['marked_preview']            = array();
        $assets['attended_questions']        = array();
        // $user                                = $this->auth->get_current_user_session('user');
        if($attempt_id)
        {
            $attempt    = $this->Course_model->attempt(array('id' => $attempt_id, 'assesment_id' => $assesment_id, 'user_id' => $this->__student['id'], 'select' => 'aa_duration,aa_marked_preview'));
            if(!$attempt)
            {
                $assets['error']    = true;
                $assets['message']  = 'Invalid attempt id';
                echo json_encode($assets);die;
            }
            
            
            /* get attempt time taken */
            $attempt_objects                = array();
            $attempt_objects['key']         = 'attempt_time_'.$attempt_id;
            $attempt_time                   = $this->memcache->get($attempt_objects);
            if($attempt_time) 
            {
                $attempt['aa_duration']     = $attempt_time['aa_duration'];
            }
            
            /* End of get attempt time taken */

            $assets['attempt']              = $attempt;
            $assets['marked_preview']       = ($attempt['aa_marked_preview'])?explode(',', $attempt['aa_marked_preview']):array();
            $assets['attended_questions']   = $this->Course_model->answers(array('attempt_id' => $attempt_id ));
        }

        if($assesment['a_qshuffling'])
        {
            shuffle($questions);               
        } 
        
        //$questions                         = $this->Course_model->questions($question_params);
 
        if(!empty($questions))
        {
            foreach($questions as $question)
            {
                unset($question['q_answer']);
                unset($question['correct_answer']);
                $assets['questions_order'][]                            = $question['id'];
                $assets['subject_questions'][$question['q_subject']][]  = $question['id'];
                $assets['question_type_wise'][$question['q_type']][]    = $question['id'];
                $assets['questions'][$question['id']]                   = $question;
            }
        }

        
        $is_ajax = $this->input->post('is_ajax');
        if($is_ajax)
        {
            echo json_encode($assets);        
        }
        else
        {
            echo '<pre>'; print_r($assets);die;        
        }
    }
    
    
    function save_answer()
    {
        $aa_mark_scored                      = 0;
        $total_duration                      = 0;
        $assets                              = array();
        $assesment_id                        = $this->input->post('assesment_id');
        $attempt_id                          = $this->input->post('attempt_id');
        $answers                             = $this->input->post('answer_queue');
        $this->_answers                      = json_decode($answers);
        $assesment_objects                   = array();
        $assesment_objects['key']            = 'assesment_'.$assesment_id;
        $assesment_callback                  = 'assesment_details';
        $assesment_params                    = array();
        $assesment_params['assesment_id']    = $assesment_id;
        $assesment_details                   = $this->memcache->get($assesment_objects, $assesment_callback, $assesment_params);
        
        $questions                           = $assesment_details['questions'];
        $assesment                           = $assesment_details['assesment_details'];
        
        $assets['error']                     = false;
        // $user                                = $this->auth->get_current_user_session('user');

        $save_attempt                        = array();
        $save_attempt['id']                  = $attempt_id;
        
        //getting attempt details
        $objects        = array();
        $objects['key'] = 'attempt_'.$attempt_id;
        $attempt        = $this->memcache->get($objects);
        if(!$attempt) 
        {
            $attempt            = $this->Course_model->attempt(array('select'=>'aa_duration,aa_assessment_detail','id'=>$attempt_id));
            $this->memcache->set($objects['key'], $attempt);
        } 
        //End

        $assessment_json                     = ($attempt['aa_assessment_detail']!=NULL)?json_decode($attempt['aa_assessment_detail']):array();
        if(empty($assessment_json)) {
            $exam_submission_data                  = array();
            $exam_submission_data['assesment_id']  = $assesment_id;
            $exam_submission_data['attempt_id']    = $attempt_id;
            $exam_submission_data['user_id']       = $this->__student['id'];
            $exam_submission_data['course_id']     = $assesment['a_course_id'];
            $exam_submission_data['lecture_id']    = $assesment['a_lecture_id'];
            if(!empty($questions))
            {
                foreach($questions as $question)
                {
                    $question_topic                                                         = array();
                    $question_topic['id']                                                   = $question['q_topic'];
                    $question_topic['topic_name']                                           = $question['qt_topic_name'];
                    $question_subject                                                       = array();
                    $question_subject['id']                                                 = $question['q_subject'];
                    $question_subject['subject_name']                                       = $question['qs_subject_name'];
                    $question_options                                                       = $question['options'];
                    $exam_submission_data['questions'][$question['id']]                     = array();
                    $exam_submission_data['questions'][$question['id']]['report_id']        = '';
                    $exam_submission_data['questions'][$question['id']]['type']             = $question['q_type'];
                    $exam_submission_data['questions'][$question['id']]['q_question']       = $question['q_question'];
                    $exam_submission_data['questions'][$question['id']]['q_option']         = json_encode($question['options']);
                    $exam_submission_data['questions'][$question['id']]['q_actual_answer']  = isset($question['correct_answer'])?$question['correct_answer']:array();
                    $exam_submission_data['questions'][$question['id']]['q_negative_mark']  = $question['aq_negative_mark'];
                    $exam_submission_data['questions'][$question['id']]['q_positive_mark']  = $question['aq_positive_mark'];
                    $exam_submission_data['questions'][$question['id']]['q_explanation']    = $question['q_explanation'];
                    $exam_submission_data['questions'][$question['id']]['subject']          = json_encode($question_subject);
                    $exam_submission_data['questions'][$question['id']]['topics']           = json_encode($question_topic);
                    $exam_submission_data['questions'][$question['id']]['answer_time_log']  = '';
                    $exam_submission_data['questions'][$question['id']]['user_answers']     = '';
                    $exam_submission_data['questions'][$question['id']]['user_mark']        = '';
                }
            
            }
        } else {
            $exam_submission_data           = json_decode(json_encode($assessment_json), True);
        }
        //$report_ids                      = array();
        foreach($this->_answers as $key => &$answer)
        {
            $save                            = array();
            $question_id                     = $answer->question_id;
            $duration                        = $answer->duration;
            $question                        = $questions[$question_id];
            $report_id                       = ($exam_submission_data['questions'][$question_id]['report_id']!='')?$exam_submission_data['questions'][$question_id]['report_id']:0;
            
            if($report_id!=0)
            {
                $attempt_duration            = ($exam_submission_data['questions'][$question_id]['answer_time_log']!='')?$exam_submission_data['questions'][$question_id]['answer_time_log']:0;
                $save['id']                  = $report_id;
                $save['ar_duration']         = $attempt_duration+$duration;        
            }
            else
            {
                $save['id']                  = false;
                $save['ar_duration']         = $duration;             
            }
            //$report_ids[]                    = $report_id;
            
            $save['ar_attempt_id']           = $attempt_id;
            $save['ar_question_id']          = $question_id;
            $save['ar_course_id']            = $assesment['a_course_id'];
            $save['ar_lecture_id']           = $assesment['a_lecture_id'];
            $save['ar_user_id']              = $this->__student['id'];
            $marked_right                    = false;

           
            switch ($question['q_type'])
            {
                case "1":
                    $answer                  = isset($answer->answer)?$answer->answer:'';
                    $save['ar_answer']       = $answer;
                    $correct_answer          = $question['q_answer'];
                    if($question['q_answer'] == $answer)
                    {
                        $marked_right        = true;
                        $save['ar_mark']     = isset($question['aq_positive_mark'])?$question['aq_positive_mark']:$question['q_positive_mark'];
                    }
                    else
                    {
                        $save['ar_mark'] = 0;
                        if($answer)
                        {
                            $save['ar_mark']  = isset($question['aq_negative_mark'])?$question['aq_negative_mark']:$question['q_negative_mark'];
                            $save['ar_mark']  = $save['ar_mark'];                                                       
                        }
                    }
                break;

                case "2":
                    
                    // echo $question['q_answer'].'=='.$answer; 
                    // die;
                    // $key_answer               = explode(',', $question['q_answer']);
                    // $correct_answer           = array();
                    // if(!empty($key_answer))
                    // {
                    //     foreach ($key_answer as $t_key)
                    //     {
                    //         $correct_answer[$t_key] = $t_key;
                    //     }
                    // }
                    // $answer = (array)$answer->answer;
                    // sort($key_answer);
                    // sort($answer);
                    // $key_answer                 = implode(',', $key_answer);
                    // $answer                     = implode(',', $answer);

                    $answer         = $answer->answer;
                    $correct_answer = $question['q_answer'];
                    $save['ar_answer']          = $answer;
                    if($question['q_answer'] == $answer)
                    {
                        $save['ar_mark']        = isset($question['aq_positive_mark'])?$question['aq_positive_mark']:$question['q_positive_mark'];
                        $marked_right           = true;
                    }
                    else
                    {
                        $save['ar_mark'] = 0;
                        if($answer)
                        {
                            $save['ar_mark']         = isset($question['aq_negative_mark'])?$question['aq_negative_mark']:$question['q_negative_mark'];
                            $save['ar_mark']         = $save['ar_mark'];                                                       
                        }
                    }
                break;

                case "3":
                    $answer                      = isset($answer->answer)?$answer->answer:'';
                    $save['ar_answer']           = $answer;
                    $save['ar_mark']             = '';
                    $correct_answer              = '';
                break;

                case "4":
                    $answer                      = isset($answer->answer)?$answer->answer:'';
                    $save['ar_answer']           = $answer;
                    $save['ar_mark']             = '';
                    $correct_answer              = '';
                break;
                
            }
            
            $exam_submission_data['questions'][$question_id]['answer_time_log'] = $duration;
            $exam_submission_data['questions'][$question_id]['user_answers']    = $answer;
            $exam_submission_data['questions'][$question_id]['user_mark']       = $save['ar_mark'];
            $save_report = $this->Course_model->save_assessment_report($save);
            $exam_submission_data['questions'][$question_id]['report_id']       = $save_report;


            $total_duration                                += $duration;
            $answer_report                       = array();
            $answer_report['question_id']        = $question_id;
            $answer_report['ar_answer']          = $correct_answer;
            $answer_report['marked_right']       = $marked_right;
        }

        foreach($exam_submission_data['questions'] as $report_object) {
            $aa_mark_scored += $report_object['user_mark'];
        }
        
        $json_submission_data                    = json_encode($exam_submission_data);
        $save_attempt['aa_assessment_detail']    = $json_submission_data;
        $save_attempt['aa_duration']             = $attempt['aa_duration']+$total_duration;
        $save_attempt['aa_valuated']             = '0';
        $save_attempt['aa_mark_scored']          = $aa_mark_scored;
        $this->Course_model->save_assessment_attempts($save_attempt);

        $assets['error']                     = false;
        $assets['message']                   = 'Answer saved';
        $assets['attempt_id']                = $attempt_id;
        //$assets['rports']                    = $report_ids;
        if($assesment['a_que_report'] == '1')
        {
            $assets['report']                = $answer_report;
        }
        $save_attempt_details                         = array();
        $save_attempt_details['aa_duration']          = $save_attempt['aa_duration'];
        $save_attempt_details['aa_assessment_detail'] = $json_submission_data;
        $this->memcache->set('attempt_'.$attempt_id, $save_attempt_details);
        echo json_encode($assets);die;
    }
    
    function mark_as_review($assesment_id = 0, $attempt_id = 0)
    {
        $assesment          = $this->Course_model->assesment(array('assessment_id' => $assesment_id, 'select' => 'a_qshuffling,a_mark,a_questions,a_duration'));
        $assets['error']    = false;
        if(!$assesment)
        {
            $assets['error'] = true;
            $assets['message'] = 'Invalid Test id';
            echo json_encode($assets);die;
        }

        $user       = $this->auth->get_current_user_session('user');
        $attempt    = $this->Course_model->attempt(array('id' => $attempt_id, 'assesment_id' => $assesment_id, 'user_id' => $user['id'], 'select' => 'aa_marked_preview, aa_duration, aa_mark_scored'));
        if(!$attempt)
        {
            //creating new attempt
            $user = $this->auth->get_current_user_session('user');
            $attempt = array();
            $attempt['id'] = false;
            $attempt['aa_assessment_id'] = $assesment_id;
            $attempt['aa_user_id'] = $user['id'];
            $attempt['aa_attempted_date'] = date('Y-m-d h:i:s');
            $attempt['aa_duration'] = 0;
            $attempt['aa_total_mark'] = $assesment['a_mark'];
            $attempt['aa_total_questions'] = $assesment['a_questions'];
            $attempt['aa_total_duration'] = $assesment['a_duration']*60;//convert to seconds
            $attempt_id = $this->Course_model->save_assessment_attempts($attempt);
            $attempt['aa_marked_preview'] = null;
        }
        $save                       = array();
        $save['id']                 = $attempt_id;
        $save['aa_assessment_id']   = $assesment_id;
        
        $aa_marked_preview          = $attempt['aa_marked_preview'];
        $aa_marked_preview          = explode(',', $aa_marked_preview);
        $aa_marked_preview[]        = $this->input->post('question_id');
        $aa_marked_preview          = implode(',', $aa_marked_preview);
        $save['aa_marked_preview']  = $aa_marked_preview;
        
        $attempt_id             = $this->Course_model->save_assessment_attempts($save);
        $assets['error']        = false;
        $assets['attempt_id']   = $attempt_id;
        $assets['message']      = 'Question mark for review';
        echo json_encode($assets);die;
    }

    function discard_review($assesment_id = 0, $attempt_id = 0)
    {
        $assesment          = $this->Course_model->assesment(array('assessment_id' => $assesment_id, 'select' => 'a_qshuffling,a_mark,a_questions,a_duration'));
        $assets['error']    = false;
        if(!$assesment)
        {
            $assets['error'] = true;
            $assets['message'] = 'Invalid Test id';
            echo json_encode($assets);die;
        }

        $user       = $this->auth->get_current_user_session('user');
        $attempt    = $this->Course_model->attempt(array('id' => $attempt_id, 'assesment_id' => $assesment_id, 'user_id' => $user['id'], 'select' => 'aa_marked_preview, aa_duration, aa_mark_scored'));
        if(!$attempt)
        {
            //creating new attempt
            $user = $this->auth->get_current_user_session('user');
            $attempt = array();
            $attempt['id'] = false;
            $attempt['aa_assessment_id'] = $assesment_id;
            $attempt['aa_user_id'] = $user['id'];
            $attempt['aa_attempted_date'] = date('Y-m-d h:i:s');
            $attempt['aa_duration'] = 0;
            $attempt['aa_total_mark'] = $assesment['a_mark'];
            $attempt['aa_total_questions'] = $assesment['a_questions'];
            $attempt['aa_total_duration'] = $assesment['a_duration']*60;//convert to seconds
            $attempt_id = $this->Course_model->save_assessment_attempts($attempt);
            $attempt['aa_marked_preview'] = null;
        }
        $save                       = array();
        $save['id']                 = $attempt_id;
        $save['aa_assessment_id']   = $assesment_id;
        
        $discard_review_ids         = array();
        $aa_marked_preview          = $attempt['aa_marked_preview'];
        $aa_marked_preview          = explode(',', $aa_marked_preview);
        $discard_review_ids[]       = $this->input->post('question_id');
        $discard_review             = array_diff($aa_marked_preview, $discard_review_ids);
        $discarded_reviews          = implode(',', $discard_review);
        $save['aa_marked_preview']  = $discarded_reviews;
        
        $attempt_id             = $this->Course_model->save_assessment_attempts($save);
        $assets['error']        = false;
        $assets['attempt_id']   = $attempt_id;
        $assets['message']      = 'Question mark for review';
        echo json_encode($assets);die;
    }

    function test_response($assesment_id = 0, $attempt_id = 0)
    {
        if( $this->uri->segment(3) == 'completed' )
        {
            $data                           = array();
            // $user                           = $this->auth->get_current_user_session('user');
            $data['end_message']            = 'Thank you';
            $data['evaluation_completed']   = "";
            $data['user_name']              = $this->__student['us_name'];
            $this->load->view($this->config->item('theme'). '/assesment/test_end', $data);   
        }
        else 
        {
            if(($assesment_id==0)||($attempt_id==0))
            {
                redirect(site_url('dashboard'));
            }
            $assesment           = $this->Course_model->assesment(array('assessment_id' => $assesment_id, 'select' => 'assessments.*'));
            if(!$assesment)
            {
                redirect(site_url('dashboard'));
            }
            if($attempt_id)
            {
                // $user       = $this->auth->get_current_user_session('user');
                $attempt    = $this->Course_model->attempt(array('id' => $attempt_id, 'assesment_id' => $assesment_id, 'user_id' => $this->__student['id'], 'select' => 'aa_duration, aa_marked_preview, aa_valuated, aa_mark_scored'));
                if(empty($attempt))
                {
                    redirect(site_url('dashboard'));            
                }
            }
            if($assesment['a_show_smessage'] == '1')
            {
                $data                   = array();
                $data['user_name']      = $this->__student['us_name'];
                $data['end_message']    = $assesment['a_smessage'];
                $evaluation_completed   = false;
                $valuated               = false;
                if($attempt['aa_valuated'] == '1')
                {
                    $valuated                   = true;
                    $data['score']              = $attempt['aa_mark_scored'];
                }
                $data['evaluation_completed']   = $valuated;
                $data['attempt_id']             = $attempt_id;
                $this->load->view($this->config->item('theme'). '/assesment/test_end', $data);        
            }
            else
            {
                if($this->user_token)
                {
                    redirect(site_url('dashboard/result_preview/'.$attempt_id.'/'.$this->user_token));
                }
                else
                {
                    redirect(site_url('dashboard/result_preview/'.$attempt_id));
                }
            }
        }
    }

    public function invalidate_course($param = array())
    {
        //Invalidate cache
        $course_id = isset($param['course_id']) ? $param['course_id'] : false;
        if ($course_id) {
            $this->memcache->delete('course_' . $course_id);
        } else {
            $this->memcache->delete('all_courses');
            $this->memcache->delete('sales_manager_all_sorted_courses');
            $this->memcache->delete('top_courses');
        }
        $this->memcache->delete('active_courses');
    }

    public function save_time()
    {
       
        $response               = array();
        $attempt_id             = $this->input->post('attempt_id');
        $time_taken             = $this->input->post('time_taken');
        $data                   = array();
        $data['id']             = $attempt_id;
        $data['aa_duration']    = $time_taken;

        /* set attempt time */
        $objects                = array();
        $objects['key']         = 'attempt_time_'.$attempt_id;
        $this->memcache->set($objects['key'], $data);

        /* End of set attempt time */ 
        
        // $attempt_id             = $this->input->post('attempt_id');
        // $time_taken             = $this->input->post('time_taken');
        // $data                   = array();
        // $data['id']             = $attempt_id;
        // $data['aa_duration']    = $time_taken;
        // $attempt_id             = $this->Course_model->save_assessment_attempts($data);
        $response['attempts']   = $data;
        $response['attempt_id'] = $attempt_id;
        $response['error']      = 'false';
        $response['message']    = 'Saved successfully!';
        echo json_encode($response);die;
    }

    public function invalidate_subscription($param = array())
    {
        //Invalidate cache
        $user_id = isset($param['user_id']) ? $param['user_id'] : false;
        $course_id = isset($param['course_id']) ? $param['course_id'] : false;
        if ($user_id && $course_id) {
            $this->memcache->delete('enrolled_' . $user_id);
            $this->memcache->delete('subscription_' . $course_id . '_' . $user_id);
            $objects_key        = 'enrolled_item_ids_' .$user_id;
            $this->memcache->delete($objects_key);
            
        }
        if ($user_id) {
            $this->memcache->delete('mobile_enrolled_'.$user_id);
            $this->memcache->delete('enrolled_' . $user_id);
            $objects_key        = 'enrolled_item_ids_' .$user_id;
            $this->memcache->delete($objects_key);
        }
    }
    
    function create_document()      
    {       
        $this->load->library('PHPWord');
        // $user               = $this->auth->get_current_user_session('user');        
        $course_id          = 163;//$this->input->post('course_id');      
        $course             = $this->Course_model->course(array('id' => $course_id));       
        $cert_details       = array('{Name}'=>$this->__student['us_name'],'{Course_name}'=>$course['cb_title']);       

        $docroot            =  certificate_upload_path().$this->__student['id'].'_'.$course_id.'/';        

        $this->load->model('Settings_model');
        $active_certificate = $this->Settings_model->get_active_certificate();
        $document           = $this->phpword->loadTemplate(certificate_upload_path().$active_certificate['cm_filename']);     

        $document->setValue('{Name}', ':'.' '.$this->__student['us_name']);        
        $document->setValue('{Course_name}', ''.' '.$course['cb_title']);       
        $document->setValue('{dd-mm-yyyy}', ''.' '.date('d-m-Y'));      

        if (!file_exists($docroot))         
        {       
            mkdir($docroot, 0777, true);        
        }       

        $document->save($docroot.$course['id'].'_'.$this->__student['id'].'.docx');        

        $user_id   = $this->Course_model->status_certificate_download(array('course_id' => $course_id,'user_id' => $this->__student['id']));       

        $this->load->library('ofabeeconverter');        
        $file_name_with_path    = $docroot.$course['id'].'_'.$this->__student['id'].'.docx';       
        $file_name              = $this->ofabeeconverter->file_name($file_name_with_path);      

        $config                 = array();      
        $config['input']        = $file_name_with_path;     
        $config['output']       = $docroot;     
        $config['s3_upload']    = false;    
        $this->ofabeeconverter->initialize_config($config);     
        $response = $this->ofabeeconverter->convert();  

        $convertion_objects = $response['convertion_objects'];        
        $dimension          = shell_exec('identify -verbose  -format "%Wx%H" '.$convertion_objects['output'].' 2>&1');
        $dimension          = explode('x', $dimension);
        $xaxis              = $dimension[0] - 350;

        $param              = array();
        $param['xaxis']     = $xaxis;
        $param['yaxis']     = '5';
        $param['qr_code']   = config_item('upload_folder').'/qrcode.gif';
        $param['input']     = $convertion_objects['output'];
        $param['output']    = $convertion_objects['output'];
        $this->place_qr_code($param);
        echo json_encode($this->__student['id']);                  
    }
    
    function place_qr_code($params = array())
    {
        $xaxis      = $params['xaxis'];
        $yaxis      = $params['yaxis'];
        $qr_code    = $params['qr_code'];
        $input      = $params['input'];
        $output     = $params['output'];
        shell_exec('composite -geometry "350x350 +'.$xaxis.'+'.$yaxis.'" '.$qr_code.' '.$input.' '.$output);
    }

    function download_certificate($user_id,$course_id)      
    {       
        $file   = certificate_upload_path().$user_id.'_'.$course_id.'/'.$course_id.'_'.$user_id.'/page.jpg';        
        $quoted = 'certificate.jpg';        
        $size   = filesize($file);      

        header('Content-Description: File Transfer');       
        header('Content-Type: image/jpeg');     
        header('Content-Disposition: attqrcode_pathachment; filename=' . $quoted);         
        header('Content-Transfer-Encoding: binary');        
        header('Connection: Keep-Alive');       
        header('Expires: 0');       
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');        
        header('Pragma: public');       
        header('Content-Length: ' . $size);     
        readfile($file);        
    }


}
?>
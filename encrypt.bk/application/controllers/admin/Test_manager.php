<?php
class Test_manager extends CI_Controller
{
    public $__lecture_type_array;
    public $__lecture_type_keys_array;
    function __construct()
    {
        
        parent::__construct();
        $this->__role_query_filter = array();
        $this->__loggedInUser   = $this->auth->get_current_user_session('admin');
        $redirect	= $this->auth->is_logged_in(false, false);
        $this->__admin_index = '';
        $this->limit        = 10;
        if (!$redirect)
        {
            $redirect   = true;
            $teacher    = $this->auth->is_logged_in(false, false, 'teacher');
            if($teacher)
            {
                $redirect = false;
                $this->__admin_index = 'teacher';
                $teacher = $this->auth->get_current_user_session('teacher');
                $this->__role_query_filter['teacher_id'] = $teacher['id'];                
            }
            $content_editor    = $this->auth->is_logged_in(false, false, 'content_editor');
            if($content_editor)
            {
                $redirect = false;
                $this->__admin_index    = 'content_editor';
                $content_editor         = $this->auth->get_current_user_session('content_editor');
                $this->__role_query_filter['editor_id'] = $content_editor['id'];                
            }
            if($redirect)
            {
                redirect('login');
            }
        }
        $this->__lecture_type_array = array(  '1' => 'video',
        '2' => 'document',
        '3' => 'quiz',
        '4' => 'youtube',
        '5' => 'text',
        '6' => 'wikipedia',
        '7' => 'live',
        '8' => 'assignment',
        '9' => 'recorded videos',
        '10' => 'scorm',
        '11' => 'cisco recorded videos',
        '12' => 'audio',
        '13' => 'survey',
        '14' => 'certificate'
        );

        $this->__lecture_type_keys_array  = array();
        foreach ($this->__lecture_type_array as $id => $type) {
        $this->__lecture_type_keys_array[$type] = $id;
        }

        $this->actions        = $this->config->item('actions');
        $this->load->model(array('Course_model', 'Category_model', 'Test_model','Language_model','Group_model'));
        $this->lang->load('course_settings');
    }

    function index()
    {
        redirect(admin_url('course'));exit;
        $data                       = array();
        $breadcrumb                 = array();
        $limit                      = $this->limit;
        $offset                     = 0;
        $breadcrumb[]               = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]               = array( 'label' => 'Quiz Manager', 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']         = $breadcrumb;
        $data['title']              = 'Quiz Manager';
        $data['tests']              = $this->Test_model->tests(array('limit'=>$limit));
        $data['total_tests']        = $this->Test_model->tests(array('count'=>true));
        $data['show_load_button']   = $data['total_tests']>$limit?true:false;
        //echo '<pre>';print_r($data['tests']);die;
        $this->load->view($this->config->item('admin_folder').'/tests', $data);
    }

    

    function test($testId = false){
        $data                       = array();
        $breadcrumb                 = array();
        $breadcrumb[]               = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]               = array( 'label' => 'Quiz Manager', 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']         = $breadcrumb;
        $data['title']              = 'Create Quiz';
        $data['categries']			= $this->Category_model->categories();
        $data['instructions']		= $this->Test_model->instructions(array('select'=>'assessment_instructions.id,assessment_instructions.ai_title'));
        $this->load->view($this->config->item('admin_folder').'/test', $data);
    }

    function validate_mark(){
        $lecture_id                 = base64_decode($this->input->post('lecture_id'));
        $tot_mark                   = $this->Test_model->test_details(array('test_id'=>$lecture_id,'select'=>'assessments.a_mark'));
        $response                   = array();
        $response['success']        = true;
        $response['total_mark']     = $tot_mark['a_mark'];
        echo json_encode($response);die();
    }

    function test_basics($testId = false){
        $data                       = array();
        $save                       = array();
        $testId                     = base64_decode($testId);
        if(!$testId){
            redirect(admin_url('test_manager'));
        }
        
        $testdetails               = $this->Test_model->test_details(array('test_id'=>$testId,'select'=>'course_lectures.id,course_lectures.cl_course_id,course_lectures.cl_status,assessments.a_instructions,assessments.id as assessment_id'));
        $tot_mark                  = $this->Test_model->test_questions(array('select'=>'sum(aq_positive_mark) as mark','assessment_id'=>$testdetails['assessment_id']));
        $course_id                 = $testdetails['cl_course_id'];
        $data['course_details']  = $this->Course_model->course(array('id'=>$course_id));
        if($this->__loggedInUser['rl_full_course']==0){
            $this->__quiz_permission  = $this->accesspermission->get_permission_course(
                                                        array(
                                                            'role_id' => $this->__loggedInUser['role_id'],
                                                            'module' => 'quiz_manager'
                                                            ,'user_id' => $this->__loggedInUser['id'],'course_id' => $course_id ));
        } else {
            $this->__quiz_permission  = $this->accesspermission->get_permission(
                                                        array(
                                                            'role_id' => $this->__loggedInUser['role_id'],
                                                            'module' => 'quiz_manager'
                                                            ));

        }
        if(in_array('3', $this->__quiz_permission))
        { 
        $assessment_id             = $testdetails['assessment_id'];

        $submitted                  = $this->input->post('submitted');
        $savenext                   = $this->input->post('savenext');
        if($submitted){
        	$instruction 						  = json_decode($testdetails['a_instructions'],true);
        	$recieved_instr 					  = $this->input->post('a_instruction');
        	if($recieved_instr != ''){
                $active_language 				  = $this->input->post('active_language');
        		$instruction[$active_language]	  = $recieved_instr;
        		$save['a_instructions']           = json_encode($instruction);
            }
            $save['id']                           = $testId;
            // var_dump($_FILES);die;
            if(isset($_FILES['lecture_image']) && $_FILES['lecture_image']['error'] !== 4)
            { 
                $allowed =  array('gif','png' ,'jpg', 'jpeg');
                $filename = $_FILES['lecture_image']['name'];
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                if(in_array($ext,$allowed))
                {
                    $lecture_id                    =  $save['id'] ;
                    $version                       = rand(0,300);
                    // var_dump($this->upload_course_lecture_image_to_localserver(array('course_id'=>$course_id , 'lecture_id'=>$lecture_id )));die;
                    if($this->upload_course_lecture_image_to_localserver(array('course_id'=>$course_id , 'lecture_id'=>$lecture_id )))
                    {
                        $save['cl_lecture_image']     =  $lecture_id.".jpg?v=".$version;
                    }
                }

            }
            // var_dump($save);die;
            $save['id']                           = $testId;
            $save['assessment_id']                = $assessment_id;
            $save['cl_limited_access']            = $this->input->post('cl_limited_access');
            $save['cl_lecture_name']              = $this->input->post('test_name');
            $save['a_category']                   = $this->input->post('test_category');
            $save['a_duration']                   = $this->input->post('test_duration');
            $save['a_mark']                       = $this->input->post('test_mark');
            $save['cl_lecture_type']              = '3';
            $save['action_id']                    = '4';
            $save['cl_status']                    = '0';
            $save['cl_deleted']                   = '0';
            $save['cl_account_id']                = config_item('id');
            $save['action_by']                    = $this->auth->get_current_admin('id');

            // var_dump($save);die;
            $this->Test_model->save($save);

            $this->invalidate_course(array('course_id' => $course_id));

            //end
            $keys                                 = 'assesment_'.$assesment_id;
            $assesment_objects                    = array();
            $assesment_objects['key']             = 'assesment_'.$assessment_id;
            $assesment_callback                   = 'assesment_details';
            $assesment_params                     = array();
            $assesment_params['assesment_id']     = $assessment_id;
            $this->memcache->delete($keys);
            $assesment_details                    = $this->memcache->get($assesment_objects, $assesment_callback, $assesment_params);
            $this->session->set_flashdata('message','Quiz details saved successfully.');   
            if($savenext)
            {
                redirect(admin_url('test_manager/test_settings'.'/'.base64_encode($testId)));
            } 
            else 
            {
                redirect(admin_url('test_manager/test_basics'.'/'.base64_encode($testId)));
            }
        }
        // var_dump($data);die;
        $test_details               = $this->Test_model->test_details(array('test_id'=>$testId,'select'=>'course_basics.cb_title, course_basics.id as course_id,course_lectures.id,course_lectures.cl_lecture_name,course_lectures.cl_lecture_image,course_lectures.cl_limited_access,assessments.id as assessment_id,assessments.a_category,assessments.a_duration,assessments.a_instructions,assessments.a_questions,assessments.a_mark'));
        $data['test']               = $test_details;
        $data['active_lang']		= isset($_SESSION['active_lang'])?$_SESSION['active_lang']:1;
        $data['categories']         = $this->Category_model->categories();
        $data['languages']       	= $this->Language_model->languages();
        $data['title']              = 'Create New Quiz';
        $data['default_instruction']= $this->get_instruction();
        $data['total_mark']         = $tot_mark[0]['mark'];

        $has_s3_enabled                         = $this->settings->setting('has_s3');
        if($has_s3_enabled && ($has_s3_enabled['as_siteadmin_value']))
        {
            $s3_settings                        = $has_s3_enabled['as_setting_value']['setting_value'];
            $s3_domain   = $s3_settings->cdn;
            $s3_url      = "https://".$s3_domain."/uploads/".config_item('acct_domain')."/course/".$course_id."/course/lecture/".$test_details['cl_lecture_image'];
            $default_url = "https://".$s3_domain."/uploads/default/course/default-lecture.jpg";
            $headers     = @get_headers($s3_url); 
            if($headers && strpos( $headers[0], '200'))
            {
                //echo "exist";die();
                $data['s3_lecture_image'] = $s3_url;
            }
            else
            {   
                //echo "not exist";die();
                $data['s3_lecture_image'] = $default_url;
            }
        }

        $this->load->view($this->config->item('admin_folder').'/test_basics', $data);
       } 
       else
       {
        redirect(admin_url());
       }
    }

    function test_settings($testId = false){
        $data                       = array();
        $save                       = array();
        $testId                     = base64_decode($testId);

        if(!$testId){
            redirect(admin_url('test_manager'));
        }
        $testdetails                = $this->Test_model->test_details(array('test_id'=>$testId,'select'=>'course_lectures.id,course_lectures.cl_status,course_lectures.cl_course_id,assessments.a_instructions,assessments.id as assessment_id'));

        $course_id                  = $testdetails['cl_course_id'];
        if($this->__loggedInUser['rl_full_course']==0){
             $this->__quiz_permission  = $this->accesspermission->get_permission_course(
                                                        array(
                                                            'role_id' => $this->__loggedInUser['role_id'],
                                                            'module' => 'quiz_manager'
                                                            ,'user_id' => $this->__loggedInUser['id'],'course_id' => $course_id ));
        } 
        else 
        {
            $this->__quiz_permission  = $this->accesspermission->get_permission(
                                                        array(
                                                            'role_id' => $this->__loggedInUser['role_id'],
                                                            'module' => 'quiz_manager'
                                                            ));

        }
        if(in_array('3', $this->__quiz_permission))
        { 
            $assessment_id                            = $testdetails['assessment_id'];
            $submitted                                = $this->input->post('submitted');
            $savenext                                 = $this->input->post('savenext');
            if($submitted){
                $save['id']                           = $testId;
                $save['assessment_id']                = $assessment_id;
                $save['a_qgrouping']                  = $this->input->post('test_question_grouping');
                $save['a_qgrouping']                  = (!$save['a_qgrouping'])?'0':$save['a_qgrouping'];
                $save['a_qshuffling']                 = $this->input->post('test_question_shuffling');
                $save['a_qshuffling']                 = (!$save['a_qshuffling'])?'0':$save['a_qshuffling'];
                $save['a_show_mark']                  = $this->input->post('test_show_mark');
                $save['a_show_mark']                  = (!$save['a_show_mark'])?'0':$save['a_show_mark'];
                $save['a_limit_navigation']           = $this->input->post('test_question_navigate');
                $save['a_limit_navigation']           = (!$save['a_limit_navigation'])?'0':$save['a_limit_navigation'];
                $save['a_fail_pass_message']          = $this->input->post('test_passfail_response');
                $save['a_fail_pass_message']          = (!$save['a_fail_pass_message'])?'0':$save['a_fail_pass_message'];
                $save['a_pass_message']               = $this->input->post('test_pass_message');
                $save['a_fail_message']               = $this->input->post('test_fail_message');
                $save['a_pass_percentage']            = $this->input->post('test_pass_percentage');
                $save['a_show_smessage']          	  = $this->input->post('test_submit_response');
                $save['a_show_smessage'] 	          = (!$save['a_show_smessage'])?'0':$save['a_show_smessage'];
                $save['a_smessage']                   = $this->input->post('test_submit_message');
                $save['a_attend_all']                 = $this->input->post('test_attend_all');
                $save['a_attend_all']                 = (!$save['a_attend_all'])?'0':$save['a_attend_all'];
                $save['a_submit_immediate']           = $this->input->post('test_submit_immediate');
                $save['a_submit_immediate']           = (!$save['a_submit_immediate'])?'0':$save['a_submit_immediate'];
                $save['a_has_pass_fail']              = $this->input->post('test_has_passfail');
                $save['a_has_pass_fail']              = (!$save['a_has_pass_fail'])?'0':$save['a_has_pass_fail'];
                $save['action_id']                    = '4';
                $save['cl_status']                    = '0';
                $save['a_que_report']                 = $this->input->post('test_que_report');
                $save['a_que_report']                 = (!$save['a_que_report'])?'0':$save['a_que_report'];
                $save['a_test_report']                = $this->input->post('test_end_report');
                $save['a_test_report']             	  = (!$save['a_test_report'])?'0':$save['a_test_report'];
                $save['action_id']                    = '2';
                $save['action_by']                    = $this->auth->get_current_admin('id');
                $save['assessment_id']                = $assessment_id;
                $this->Test_model->save($save);
            
                //end
                $keys                                 = 'assesment_'.$assesment_id;
                $assesment_objects                    = array();
                $assesment_objects['key']             = 'assesment_'.$assessment_id;
                $assesment_callback                   = 'assesment_details';
                $assesment_params                     = array();
                $assesment_params['assesment_id']     = $assessment_id;
                $this->memcache->delete($keys);
                $assesment_details                    = $this->memcache->get($assesment_objects, $assesment_callback, $assesment_params);
                $this->session->set_flashdata('message','Quiz settings saved successfully.');
                    if($savenext)
                    {
                        redirect(admin_url('test_manager/test_questions'.'/'.base64_encode($testId)));
                    } else {
                        redirect(admin_url('test_manager/test_settings'.'/'.base64_encode($testId)));
                    }
            
            }

            $testdetails                = $this->Test_model->test_details(array('test_id'=>$testId,'select'=>'course_basics.cb_title, course_basics.id as course_id, course_lectures.id,course_lectures.cl_lecture_name,assessments.id as assessment_id,assessments.a_qgrouping,assessments.a_qshuffling,assessments.a_show_mark,assessments.a_limit_navigation,assessments.a_fail_pass_message,assessments.a_pass_message,assessments.a_fail_message,assessments.a_show_smessage,assessments.a_smessage,assessments.a_pass_percentage,assessments.a_attend_all,assessments.a_has_pass_fail,assessments.a_que_report,assessments.a_test_report,assessments.a_submit_immediate'));
            $data['test']               = $testdetails;
            $data['title']              = 'Quiz Setting';
            $this->load->view($this->config->item('admin_folder').'/test_settings', $data);
        } 
        else
        {
            redirect(admin_url());
        }
    }

     function test_questions($testId = false){
        $data                       = array();
        $save                       = array();
        if(!$testId){
            redirect(admin_url('test_manager'));
        }
        $testId                     = base64_decode($testId);
        $submitted                  = $this->input->post('submitted');
        $savenext                   = $this->input->post('savenext');
        $testdetails                = $this->Test_model->test_details(array('test_id'=>$testId,'select'=>'course_basics.cb_title, course_basics.id as course_id, course_lectures.id,course_lectures.cl_lecture_name,assessments.id AS assessment_id,assessments.a_questions,assessments.a_mark'));
        $tot_mark                   = $this->Test_model->test_questions(array('select'=>'sum(aq_positive_mark) as mark','assessment_id'=>$testdetails['assessment_id']));
        $course_id                  = $testdetails['course_id'];
        if($this->__loggedInUser['rl_full_course']==0){
             $this->__quiz_permission  = $this->accesspermission->get_permission_course(
                                                        array(
                                                            'role_id' => $this->__loggedInUser['role_id'],
                                                            'module' => 'quiz_manager'
                                                            ,'user_id' => $this->__loggedInUser['id'],'course_id' => $course_id ));
        } else {
            $this->__quiz_permission  = $this->accesspermission->get_permission(
                                                        array(
                                                            'role_id' => $this->__loggedInUser['role_id'],
                                                            'module' => 'quiz_manager'
                                                            ));

        }
        if(in_array('3', $this->__quiz_permission))
        { 
            if($submitted) {
                $tot_positive_marks             = array();
                foreach($_POST['question'] as $question) {
                    $save                       = array();
                    $save['aq_positive_mark']   = $this->input->post('positive'.$question);
                    $tot_positive_marks[]       = $this->input->post('positive'.$question);
                    $save['aq_negative_mark']   = $this->input->post('negative'.$question);
                    $save['aq_negative_mark']   = -$save['aq_negative_mark'];
                    $this->Test_model->save_assessment_question(array('id'=>$question,'save'=>$save));
                }
                $total_marks                    = array_sum($tot_positive_marks);
                $update_mark                    = array();
                $update_mark['a_lecture_id']    = $testId;
                $update_mark['a_mark']          = $total_marks;
                $this->Test_model->update_assesment($update_mark);
                $status_param                   = array();
                $status_param['id']             = $testdetails['id'];
                $status_param['cl_status']      = '0';
                $status_param['action_id']      = '4';
                $status_param['action_by']      = $this->auth->get_current_admin('id');
                $this->Course_model->save_lecture($status_param);
                $this->invalidate_course(array('course_id' => $course_id));
                $assesment_id                   = $testdetails['assessment_id'];
                //end
                $keys = 'assesment_'.$assesment_id;
                $assesment_objects                   = array();
                $assesment_objects['key']            = 'assesment_'.$assesment_id;
                $assesment_callback                  = 'assesment_details';
                $assesment_params                    = array();
                $assesment_params['assesment_id']    = $assesment_id;
                $this->memcache->delete($keys);
                $assesment_details                   = $this->memcache->get($assesment_objects, $assesment_callback, $assesment_params);
                $this->session->set_flashdata('message','Quiz questions saved successfully.');
                if($savenext){
                    redirect(admin_url('test_manager/test_publishing'.'/'.base64_encode($testId)));
                } else {
                    redirect(admin_url('test_manager/test_questions'.'/'.base64_encode($testId)));
                }
            }
     
            $data['test']               = $testdetails;
            //$data['lecture']            = $testdetails;
            $questions                  = $this->Test_model->test_questions(array('assessment_id'=>$testdetails['assessment_id']));
            $data['questions']          = $questions;
            $data['title']              = 'Quiz Questions';
            $main_category              = $this->Category_model->categories(array('status' => '1', 'not_deleted'=>true));
            $data['q_parent_category']  = $main_category;
            $data['quiz_total_mark']    = $tot_mark[0]['mark'];
            //echo '<pre>'; print_r($questions);die;
            $this->load->view($this->config->item('admin_folder').'/test_questions', $data);
        } 
        else
        {
        redirect(admin_url());
        }
    }

    function test_publishing($testId = false)
    {        
        $testId                     = base64_decode($testId);
        if(!$testId)
        {
            redirect(admin_url('test_manager'));
        }
        $this->lang->load('course');

        $testdetails                = $this->Test_model->test_details(array('test_id'=>$testId,'select'=>'course_basics.cb_title, course_basics.id as course_id, course_lectures.id,course_lectures.cl_lecture_name,assessments.id as assessment_id,assessments.a_from,assessments.a_to,assessments.a_from_time,assessments.a_to_time,assessments.a_from_availability,assessments.a_to_availability,assessments.rule_availability,assessments.a_published,course_lectures.cl_status'));
        $lecture                    = $this->Course_model->lecture(array('direction'=>'DESC', 'id'=>  $testdetails['id']));
        $data                       = array();
        $data['title']              = $lecture['cl_lecture_name'];
        $lecture['cl_access_restriction'] = json_decode($lecture['cl_access_restriction'], true);
        $data['lecture']            = $lecture;
        $data['test']               = $testdetails;
        $this->__lecture_types_restriction = array(  '1' => 'video','2' => 'document','3' => 'quiz','4' => 'youtube','5' => 'text','6' => 'wikipedia','7' => 'live','8' => 'assignment','9' => 'recorded videos','10' => 'scorm','11' => 'cisco recorded videos','12' => 'audio','13' => 'survey','14' => 'certificate');
        $this->load->view($this->config->item('admin_folder').'/test_publishing', $data);

    }

    function delete_questions(){
    	$response 					= array();
    	$question_ids 				= $this->input->post('questions');
    	$assessment_id				= $this->input->post('assessment_id');
        $lecture_id                 = $this->input->post('lecture_id');
        $question_ids				= json_decode($question_ids,true);
        foreach ($question_ids as $qid){
    		$this->Test_model->delete_aquestion_bulk(array('id'=>$qid));
    	}
        $key                        = 'assesment_'.$assessment_id;
        $objects                    = array();
        $objects['key']             = $key;
        $assessment_cache           = $this->memcache->get($objects);
        if(!empty($assessment_cache))
        {
            $this->memcache->delete($key);   
        }

        $total_marks                = $this->Test_model->test_questions(array('select'=>'SUM(aq_positive_mark) as total_mark','assessment_id'=>$assessment_id));
        $total_mark                 = $total_marks[0]['total_mark'];

        $count_questions            = $this->Course_model->get_assessment_questions(array('count'=>true,'assesment_id'=>$assessment_id));
        $save                       = array();
        $save['a_lecture_id']       = base64_decode($lecture_id);
        $save['a_questions']        = $count_questions;
        $save['a_mark']             = $total_mark;
        $this->load->model('Test_model');
        $this->Test_model->update_assesment($save);

        //Deact lecture
        $status_param = array();
        $status_param['id']        = base64_decode($lecture_id);
        $status_param['cl_status'] = '0';
        $status_param['action_id'] = '4';
        $status_param['action_by'] = $this->auth->get_current_admin('id');
        $this->Course_model->save_lecture($status_param);
        $lecture                    = $this->Course_model->lecture(array('direction'=>'DESC', 'id'=>  $status_param['id']));
        $this->invalidate_course(array('course_id' => $lecture['cl_course_id']));
        //End deact lecture

    	$questions 					= $this->Test_model->test_questions(array('assessment_id'=>$assessment_id));
        $response['questions']		= $questions;

    	$response['success']		= true;
    	$response['message']		= 'Quesions deleted successfully.';

    	echo json_encode($response);
    }

    function test_assign($testId = false){
      
        $data                       = array();
        $save                       = array();
        $testId                     = base64_decode($testId);

        if(!$testId){
            redirect(admin_url('test_manager'));
        }
      
        $testdetails                = $this->Test_model->test_details(array('test_id'=>$testId,'select'=>'course_basics.cb_title, course_basics.id as course_id, course_lectures.id,course_lectures.cl_lecture_name,a_plans,assessments.id as assessment_id,assessments.a_from,assessments.a_to,assessments.a_from_time,assessments.a_to_time,assessments.a_duration,assessments.a_total_attempt'));
        
        $lecture_id                 = $testId;
        $override_details           = array();
        $override_detail            = $this->Test_model->override_details(array('lo_lecture_id'=>$lecture_id));
       
        $a=0;
        foreach($override_detail as $override_info){
            $override_details[$a]['id']               = $override_info['id']; 
            $override_details[$a]['start_date']       = $override_info['lo_start_date'];
            $override_details[$a]['end_date']         = $override_info['lo_end_date'];
            $override_details[$a]['start_time']       = $override_info['lo_start_time'];
            $override_details[$a]['end_time']         = $override_info['lo_end_time'];
            $override_details[$a]['duration']         = $override_info['lo_duration'];
            $override_details[$a]['attempts']         = $override_info['lo_attempts'];
            $override_details[$a]['period']           = $override_info['lo_period'];
            $override_details[$a]['period_type']      = $override_info['lo_period_type'];
            $override_batches                         = $this->Test_model->override_groups($override_info['lo_override_batches']);
            $override_details[$a]['groups']           = $override_batches['groups'];
            $override_details[$a]['group_id']         = $override_info['lo_override_batches'];
            $a++;
        }
    
        
        $submitted                  = $this->input->post('submitted');
        if($submitted) {
            $assessment_id          = $testdetails['assessment_id'];
            $save['a_institutions'] = implode(',', $this->input->post('institutions'));
            $save['a_groups']       = implode(',', $this->input->post('groups'));
            $save['id']             = $testId;
            $save['assessment_id']  = $assessment_id;
            $save['action_id']      = '2';
            $save['action_by']      = $this->auth->get_current_admin('id');
            $this->Test_model->save($save);
            $this->invalidate_course(array('course_id' => $testdetails['course_id']));
            //end
            $keys                                = 'assesment_'.$assessment_id;
            $assesment_objects                   = array();
            $assesment_objects['key']            = 'assesment_'.$assessment_id;
            $assesment_callback                  = 'assesment_details';
            $assesment_params                    = array();
            $assesment_params['assesment_id']    = $assessment_id;
            $this->memcache->delete($keys);
            $assesment_details                   = $this->memcache->get($assesment_objects, $assesment_callback, $assesment_params);
            redirect(admin_url('test_manager/test_assign'.'/'.base64_encode($testId)));
        }
     
        $data['test']                            = $testdetails;
        $data['override_details']                = $override_details;
        $breadcrumb                              = array();
        $breadcrumb[]                            = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]                            = array( 'label' => 'Quiz Manager', 'link' => admin_url('test_manager'), 'active' => '', 'icon' => '' );
        $breadcrumb[]                            = array( 'label' => $testdetails['cb_title'], 'link' => admin_url('coursebuilder/home/'.$testdetails['course_id']), 'active' => '', 'icon' => '' );
        $breadcrumb[]                            = array( 'label' => $testdetails['cl_lecture_name'], 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']                      = $breadcrumb;
        $data['title']                           = 'Quiz Batch Override';

        $course_id                               = empty($testdetails['course_id']) ? '' : $testdetails['course_id'];
        $select                                  = 'groups.id,groups.gp_name,groups.gp_institute_id,groups.gp_institute_code,groups.gp_year';
        $data['course_groups']                   = $this->Group_model->course_groups(array('course_id' => $course_id, 'select' => $select,'not_deleted' =>true));
        $institution_groups                      = array_unique(array_column($data['course_groups'], 'gp_institute_id'));
        $institution                             = array();
        if (!empty($institution_groups)) {
            foreach ($institution_groups as $institution_id) {
                $objects        = array();
                $objects['key'] = 'institute_' . $institution_id;
                $callback       = 'institute';
                $params         = array('id' => $institution_id);
                $institution[]  = $this->memcache->get($objects, $callback, $params);
            }
        }
        $data['institution']    = $institution;
        $this->load->view($this->config->item('admin_folder').'/test_assign', $data);   
    }

    private function get_instruction()
    {
        return '<div id="dvInstruction">
            <p class="headings-altr"><strong>General Instructions:</strong></p>
            <ol class="header-child-alt">
            <li>The countdown timer at the top right corner of your screen will display the time remaining for you to complete the exam. When the clock runs out the exam ends by default, you dont need to end or submit your exam.</li>
            <li>The question palette at the right of screen shows one of the following statuses of each of the questions numbered:
            <table style="margin-bottom: 3px;"><tbody>
            <tr><td>You have not visited the question yet. (  In Grey Color )</td>
            <td style="padding-left: 7px;"><div class="gray" style="width: 20px;height: 20px;border-radius: 4px;"></div></td></tr>
            </tbody>
            </table>
            <table style="margin-bottom: 3px;"><tbody>
            <tr><td>You have not answered the question. ( In Red Color )</td>
            <td style="padding-left: 7px;"><div class="red" style="width: 20px;height: 20px;border-radius: 4px;"></div></td>
            </tr>
            </tbody>
            </table>
            <table style="margin-bottom: 3px;"><tbody>
            <tr><td>You have answered the question. ( In Green Color )</td><td style="padding-left: 7px;"><div class="green" style="width: 20px;height: 20px;border-radius: 4px;"></div></td>
            </tr>
            </tbody>
            </table>
            <table style="margin-bottom: 3px;"><tbody>
            <tr><td>You have marked the question for review.( In Pink Color ) </td><td style="padding-left: 7px;"><div class="purpal" style="width: 20px;height: 20px;border-radius: 4px;"></div></td>
            </tr>
            </tbody>
            </table>
            </li>
            <li>Click on the question number on the question palette at the right of your screen to go to that numbered question directly. Note that using this option does NOT save your answer to the current question.
            </li>
            <li>Click on <b>Save & Next</b> button to save the answer of current question and to go to the next question in sequence.
            </li>
            <li>Click on <b>Mark for Review & Next</b> button to save the answer of current question, mark it for review, and to go to the next question in sequence.The <b>Mark for Review</b> status simply acts as a reminder that you have set to look at the question again. <em>If an answer is selected for a question that is Marked for Review, the answer will be considered in the final evaluation.</em>
            </li>
            <li>Click on <b>Clear Response</b> button to deselect a chosen answer.
            </li>
            <li>Click on <b>Submit and Finish</b> button to complement the Quiz, when you click this button, you get an option to review the questions again. If you want , you can review the questions by clicking the <b>Review</b> button or complete the Quiz by clicking <b>Submit</b> button.
            </li>
            </ol>
           </div>';
    }

    function save_assessment_override(){
        $response                  = array();
        $response['message']       = 'Batch Override saved successfully';
        $response['error']         = false;
        $message                   = '';
        $start_date                = $this->input->post('start_date'); 
        $end_date                  = $this->input->post('end_date'); 
        $start_time                = $this->input->post('start_time'); 
        $end_time                  = $this->input->post('end_time'); 
        $duration                  = $this->input->post('duration'); 
        $attempts                  = $this->input->post('attempts'); 
        $grace_period              = $this->input->post('grace_period'); 
        $grace_period_type         = $this->input->post('grace_period_type'); 
        $override_batch            = $this->input->post('override_batch');
        $lecture_id                = $this->input->post('lecture_id');
        $assign_option             = $this->input->post('assign_option');
        $override_batch_str        = implode (",", $override_batch);
        if($duration=="") {
            $message          .= 'Duration cannot be empty<br />';
        }
        if($attempts=="") {
            $message          .= 'Attempts cannot be empty<br />';
            $response['error'] = true;
        }
        if($end_date!="") {
            $end_date          = new DateTime($end_date);
            $end_date          = date_format ($end_date, 'Y-m-d' );
        } else {
            $end_date          = NULL;
        }
        if($start_date!="") {
            $start_date        = new DateTime($start_date);
            $start_date        = date_format ($start_date, 'Y-m-d' );
        } else {
            $start_date        = NULL;
        }
        if($response['error'] == false)
        {
            $testdetails                          = $this->Test_model->test_details(array('test_id'=>$lecture_id,'select'=>'course_lectures.id,course_lectures.cl_course_id,course_lectures.cl_status,assessments.a_instructions,assessments.id as assessment_id'));
            $course_id                            = $testdetails['cl_course_id'];
            $GLOBALS['override_id']               = '';
            $override                             = array();
            $override['id']                       = $assign_option;
            $override['lo_lecture_id']            = $lecture_id;
            $override['lo_start_date']            = $start_date;
            $override['lo_end_date']              = $end_date;
            $override['lo_start_time']            = $start_time;
            $override['lo_end_time']              = $end_time;
            $override['lo_duration']              = $duration;
            $override['lo_attempts']              = $attempts;
            $override['lo_course_id']             = $course_id;
            $override['lo_period']                = $grace_period;
            $override['lo_period_type']           = $grace_period_type;
            $override['lo_override_batches']      = $override_batch_str;
            $override['lo_lecture_type']          = $this->__lecture_type_keys_array['quiz'];
            $GLOBALS['lo_override_id']            = $this->Test_model->saveAssesmentOverride($override);
            $this->session->set_flashdata('message','Quiz batch override saved successfully.');
            $this->invalidate_course(array('course_id' => $course_id));
        }
        $response['id']                           = $GLOBALS['override_id'];
        echo json_encode($response);

    }

    function delete_assessment_override(){
        $response                  = array();
        $response['message']       = 'Batch Override saved successfully';
        $response['error']         = false;
        $id                        = $this->input->post('id'); 
        $param                     = array();
        $param['id']               = $id;
        if($this->Test_model->deleteAssesmentOverride($param)){
            echo json_encode($response);
        }
        
    }

    function get_assessment_override(){
        $response                     = array();
        $id                           = $this->input->post('id');   
        $override_detail              = $this->Test_model->override_details(array('id'=>$id));
        $response['override_detail']  = $override_detail[0];
        $response['override_batches'] = explode(",",$override_detail[0]['lo_override_batches']);
        echo json_encode($response);
    }

    function check_override_batch(){
        $group_id                     = $this->input->post('group_id'); 
        $response                     = $this->Group_model->check_override_batch($group_id);
        echo json_encode($response);
        // $response     = array();    
        // $group_id     = (preg_match('/^[1-9]+[0-9]*$/', $this->input->post('group_id')) == true)? $this->input->post('group_id') : false;
        // if($group_id !== false)
        // {
        //     $response = $this->Group_model->check_override_batch($group_id);
        // }
        // echo json_encode($response);
    }

    public function invalidate_course($param = array())
    {
        //Invalidate cache
        $course_id = isset($param['course_id']) ? $param['course_id'] : false;
        if ($course_id) {
            $this->memcache->delete('course_' . $course_id);
            $this->memcache->delete('course_mob' . $course_id);
        } else {
            $this->memcache->delete('courses');
            $this->memcache->delete('all_courses');
            $this->memcache->delete('sales_manager_all_sorted_courses');
            $this->memcache->delete('top_courses');
        }
        $this->memcache->delete('popular_courses');
        $this->memcache->delete('featured_courses');
        $this->memcache->delete('active_courses');
    }
    function upload_course_lecture_image_to_localserver($param=array())
    { 
        $course_id              = isset($param['course_id'])?$param['course_id']:'default';
        $lecture_id             = isset($param['lecture_id'])?$param['lecture_id']:'default';
        $directory              = course_upload_path(array('course_id' => $course_id))."lecture/";
        if(!file_exists($directory)){
            mkdir($directory, 0777, true);
        }
        $this->load->library('upload');
        $config                     = array();
        $config['upload_path']      =  $directory;
        $config['allowed_types']    = 'gif|jpg|png|jpeg';
        $new_name                   = $lecture_id.'.jpg';
        $config['file_name']        = $new_name;
        $config['overwrite']        = TRUE;
        $this->upload->initialize($config);
        $this->upload->do_upload('lecture_image');
        $uploaded_data = $this->upload->data();
        
        $config                     = array();
        $config['uploaded_data']    = $uploaded_data;
        // echo '<pre>';print_r($uploaded_data);die;
        //convert to given size and return orginal name 
        $config['course_id']        = $course_id;
        $config['lecture_id']       = $lecture_id;
        $config['width']            = 739;
        $config['height']           = 417;
        $config['orginal_name']     = true;
        $new_file                   = $this->crop_image($config, $lecture=true);//orginal name
        // var_dump($this->upload->do_upload('section_image'));die;
        // var_dump( $new_file   );die;
        $config['width']            = 300;
        $config['height']           = 160;
        $config['orginal_name']     = false;
        $new_file_medium            = $this->crop_image($config, $lecture=true);
        
        $config['width']            = 85;
        $config['height']           = 85;
        $config['orginal_name']     = false;
        $new_file_small             = $this->crop_image($config, $lecture=true);
        
        $has_s3     = $this->settings->setting('has_s3');
        $directory              = course_upload_path(array('course_id' => $course_id))."lecture/";

        if( $has_s3['as_superadmin_value'] && $has_s3['as_siteadmin_value'] )
        {
            $file_path          = course_upload_path(array('course_id' => $course_id))."lecture/".$new_file;
            $file_medium_path   = course_upload_path(array('course_id' => $course_id))."lecture/".$new_file_medium;
            $file_small_path    = course_upload_path(array('course_id' => $course_id))."lecture/".$new_file_small;
            uploadToS3($file_path, $file_path);
            uploadToS3($file_medium_path, $file_medium_path);
            uploadToS3($file_small_path, $file_small_path);
            // unlink($file_path);
            // unlink($file_medium_path);
            // unlink($file_small_path);
        }
        
        return true;
    }   
    function crop_image($config, $lecture=false)
    {
        $uploaded_data  = isset($config['uploaded_data'])?$config['uploaded_data']:array();
        $width          = isset($config['width'])?$config['width']:360;
        $height         = isset($config['height'])?$config['height']:160;
        $orginal_name   = isset($config['orginal_name'])?$config['orginal_name']:false;
        $course_id      = isset($config['course_id'])?$config['course_id']:0;
        if(!empty($lecture))
        {
            $lecture_id     = isset($config['lecture_id'])?$config['lecture_id']:0;
        }
        else
        {
            $section_id     = isset($config['section_id'])?$config['section_id']:0;
        }
                
        $source_path    = $uploaded_data['full_path'];
        // var_dump($source_path);die;
        // var_dump(file_exists($source_path));die;
        if(file_exists($source_path)){
        
        $DESIRED_IMAGE_WIDTH  = $width;
        $DESIRED_IMAGE_HEIGHT = $height;
        /*
         * Add file validation code here
         */

        list($source_width, $source_height, $source_type) = getimagesize($source_path);

        switch ($source_type)
        {
            case IMAGETYPE_GIF:
                $source_gdim = imagecreatefromgif($source_path);
                break;
            case IMAGETYPE_JPEG:
                $source_gdim = imagecreatefromjpeg($source_path);
                break;
            case IMAGETYPE_PNG:
                $source_gdim = imagecreatefrompng($source_path);
                break;
        }

        $source_aspect_ratio = $source_width / $source_height;
        $desired_aspect_ratio = $DESIRED_IMAGE_WIDTH / $DESIRED_IMAGE_HEIGHT;

        if ($source_aspect_ratio > $desired_aspect_ratio)
        {
            /*
             * Triggered when source image is wider
             */
            $temp_height = $DESIRED_IMAGE_HEIGHT;
            $temp_width = ( int ) ($DESIRED_IMAGE_HEIGHT * $source_aspect_ratio);
        } else {
            /*
             * Triggered otherwise (i.e. source image is similar or taller)
             */
            $temp_width = $DESIRED_IMAGE_WIDTH;
            $temp_height = ( int ) ($DESIRED_IMAGE_WIDTH / $source_aspect_ratio);
        }

        /*
         * Resize the image into a temporary GD image
         */

        $temp_gdim = imagecreatetruecolor($temp_width, $temp_height);
        imagecopyresampled(
            $temp_gdim,
            $source_gdim,
            0, 0,
            0, 0,
            $temp_width, $temp_height,
            $source_width, $source_height
        );

        /*
         * Copy cropped region from temporary image into the desired GD image
         */

        $x0 = ($temp_width - $DESIRED_IMAGE_WIDTH) / 2;
        $y0 = ($temp_height - $DESIRED_IMAGE_HEIGHT) / 2;
        $desired_gdim = imagecreatetruecolor($DESIRED_IMAGE_WIDTH, $DESIRED_IMAGE_HEIGHT);
        imagecopy(
            $desired_gdim,
            $temp_gdim,
            0, 0,
            $x0, $y0,
            $DESIRED_IMAGE_WIDTH, $DESIRED_IMAGE_HEIGHT
        );

        /*
         * Render the image
         * Alternatively, you can save the image in file-system or database
         */

        //header('Content-type: image/jpeg');
        
        if(!empty($lecture))
        {
            $directory          = course_upload_path(array('course_id' => $course_id))."lecture/";
        }
        else
        {
            $directory          = course_upload_path(array('course_id' => $course_id))."section/";
        }
        // $directory          = course_upload_path(array('course_id' => $course_id));
        $this->make_directory($directory);
        if($orginal_name)
        {
            $final_file = $uploaded_data['raw_name'].'.jpg';                    
        }
        else
        {
            $final_file = $uploaded_data['raw_name'].'_'.$width.'x'.$height.'.jpg';        
        }
        imagejpeg($desired_gdim, $directory.$final_file);

        /*
         * Add clean-up code here
         */
        //return $uploaded_data['raw_name'].'.jpg';
        return $final_file;
    }
    return false;
    }
    private function make_directory($path=false)
    {
        if(!$path )
        {
            return false;
        }
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }
    
}
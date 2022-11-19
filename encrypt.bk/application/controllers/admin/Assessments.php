<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Assessments extends CI_Controller {

	function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Kolkata');
        $this->__role_query_filter = array();
        $redirect               = $this->auth->is_logged_in(false, false);
        $this->__admin_index    = 'admin';
        $this->__loggedInUser   = $this->auth->get_current_user_session('admin');
        $this->__restrcited_method = array('groups');
        if (!$redirect)
        {
            if(in_array($this->router->fetch_method(), $this->__restrcited_method))
            {
                redirect('login');
            }
            $redirect   = true;
            $teacher    = $this->auth->is_logged_in(false, false, 'teacher');
            if($teacher)
            {
                $redirect = false;
                $this->__admin_index = 'teacher';
                $teacher = $this->auth->get_current_user_session('teacher');
                $this->__role_query_filter['teacher_id'] = $teacher['id'];                
                $this->__loggedInUser   = $this->auth->get_current_user_session('teacher');
            }
            $content_editor    = $this->auth->is_logged_in(false, false, 'content_editor');
            if($content_editor)
            {
                $redirect = false;
                $this->__admin_index    = 'content_editor';
                $content_editor         = $this->auth->get_current_user_session('content_editor');
                $this->__role_query_filter['editor_id'] = $content_editor['id'];                
                $this->__loggedInUser   = $this->auth->get_current_user_session('content_editor');
            }
            if($redirect)
            {
                redirect('login');
            }
        }
        $this->load->model(array('Course_model', 'User_model', 'Institute_model'));
        $this->lang->load('course');
        $this->__limit = 100;
        $this->report_privilege           = $this->accesspermission->get_permission(array('role_id' => $this->__loggedInUser['role_id'], 'module' => 'report'));
    }

    
    function get_lectures()
    {
    	$course_id = $this->input->post("course_id");
    	$lectures = $this->Course_model->lectures(array("select"=>"cl_lecture_name, id", "course_id"=>$course_id, "status"=>"1", "lecture_type"=>"3"));
    	$lectures = count($lectures) > 0 ? $lectures : false;
    	echo json_encode(array("lectures"=>$lectures));
    }

    function get_assessments()
    {
    	/*$lecture_id = $this->input->post("lecture_id");
    	$sort = intval($this->input->post("sort"));
    	$search = $this->input->post("search");
    	$assessments = $this->Course_model->get_assessment_report(array("lecture_id"=>$lecture_id, "sort"=>$sort, "search"=>"%".$search."%"));
    	//echo '<pre>'; print_r($assessments);die;
        $assessments = count($assessments) > 0 ? $assessments : false;*/
        $assessments = array();
    	echo json_encode(array("assessments"=>$assessments));
    }

    function language()
    {
        $response               = array();
        $response['language']   = array();
        $response['language']   = get_instance()->lang->language;
        echo json_encode($response);
    }

    function export($param = false)
    {
        if(!$param)
        {
            redirect(admin_url('assessments/report'));
        }
        $param = base64_decode($param);
        $param = (array)json_decode($param);
        $sort           = isset($param['filter'])?$param['filter']:false;
        $search         = isset($param['keyword'])?$param['keyword']:false;
        $lecture_id     = isset($param['lecture_id'])?$param['lecture_id']:false;
        $lecture        = $this->Course_model->lecture(array("select"=>"cl_lecture_name, id", "id"=>$lecture_id, "status"=>"1", "lecture_type"=>"3"));
        $assessments    = $this->Course_model->get_assessment_report(array("lecture_id"=>$lecture_id, "sort"=>$sort, "search"=>"%".$search."%"));
        $data           = array();
        $data['assessments']    = $assessments;
        $data['lecture']        = $lecture;
        $this->load->view(config_item('admin_folder').'/export_assessments', $data);
    }
 
    function attempt( $attempt_id = 0 )
    {
        if(!$attempt_id) 
        {
            $this->output_json(array('error' => true, 'message' => 'Attempt details not found' ));
        }
        $this->load->model('Report_model');

        $data            = array();
        $user_ids        = array();
        $data['users']   = array();

        $objects         = array();
        $objects['key']  = 'grade_scale';
        $callback        = 'grade_scale';
        $data['grade']   = $this->memcache->get($objects, $callback, array()); 

        //fetching attempt details
        $attempt_details = $this->Report_model->assessment_attempts(array('id' => $attempt_id));
        if(empty($attempt_details))
        {
            $this->output_json(array('error' => true, 'message' => 'Attempt details not found' ));
        }
        $attempt_details['aa_assessment_detail'] = json_decode($attempt_details['aa_assessment_detail'], true);
        $attempt_details['aa_attempted_date_fm'] = ($attempt_details['aa_attempted_date'])?date('d/m/Y', strtotime($attempt_details['aa_attempted_date'])):''; 
        $attempt_details['aa_evaluated_date_fm'] = ($attempt_details['aa_evaluated_date'])?date('d/m/Y', strtotime($attempt_details['aa_evaluated_date'])):''; 
        $data['attempt_details']                 = $attempt_details;
        //end

        //fetch student and tutor details
        $user_ids[] = $attempt_details['aa_user_id'];
        if(isset($attempt_details['aa_valuated_by']) && $attempt_details['aa_valuated_by'] > 0)
        {
            $user_ids[] = $attempt_details['aa_valuated_by'];
        }
        $users  = $this->User_model->users(array( 
                                                        'user_ids' => $user_ids,
                                                        'select' => 'users.id as user_id, us_image, us_name'
        ));
        foreach($users as $user)
        {
            $data['users'][$user['user_id']] = $user;
        }
        //End
        $this->output_json($data);
    }

    function save_evaluation()
    {
        $this->load->model('Report_model');
        $response        = array();
        $message         = array();
        $attempt_id      = $this->input->post('attempt_id');//14;
        $valuations      = $this->input->post('valuations');
        $valuations      = json_decode($valuations, true);//array('3462' => 0, '3463' => 1, '3464' => 1, '3466' => 1, '3467' => 0);
        if(!$attempt_id)
        {
            $message[] = 'Attempt id missing';
        }
        if(empty($valuations))
        {
            $message[] = 'No Valuation details found to save';
        }
        if( sizeof($message) > 0 )
        {
            $response['error']   = true;
            $response['message'] = implode('<br />', $message);
            $this->output_json($response);
        }
        $attempt_details    = $this->Report_model->assessment_attempts(array('id' => $attempt_id, 'select' => 'aa_assessment_detail,aa_total_mark,aa_user_id,aa_course_id,aa_lecture_id'));
        if(empty($attempt_details))
        {
            $response['error']   = true;
            $response['message'] = 'Invalid attempt id';
            $this->output_json($response);
        }
        $assessment_detail    = json_decode($attempt_details['aa_assessment_detail'], true);
        
        $params = array();
        foreach($valuations as $question_id => $mark)
        {
            //updating question marks in assessment report table
            $save                   = array();
            $save['ar_attempt_id']  = $attempt_id;
            $save['ar_question_id'] = $question_id;
            $save['ar_mark']        = $mark;
            $params[]               = $save;

            //updating question marks in assessment attempt table
            $assessment_detail['questions'][$question_id]['user_mark'] = $mark;
        }
        $this->Report_model->save_assessment_valuation($params);

        //saving assessment attempt table with fresh values
        $mark_scored = 0;
        foreach ($assessment_detail['questions'] as $question_id => $answer)
        {
            $mark_scored = $mark_scored+($answer['user_mark']);
        }
        $save                           = array();
        $save['id']                     = $attempt_id;
        $save['aa_valuated']            = '1';
        $save['aa_mark_scored']         = $mark_scored;
        $save['aa_valuated_by']         = $this->__loggedInUser['id'];
        $save['aa_evaluated_date']      = date('Y-m-d H:i:s');
        $percentage                     = ($mark_scored/$attempt_details['aa_total_mark'])*100;
        $grade_awarded                  = convert_percentage_to_grade($percentage);
        $save['aa_grade']               = isset($grade_awarded['gr_name']) ? $grade_awarded['gr_name'] : '';
        $save['aa_assessment_detail']   = json_encode($assessment_detail);
        $this->Report_model->save_assessment_attempts($save);
       
        //Updating lecture grade in course subscription table
        $log_param                        = array();
        $log_param['course_id']           = $attempt_details['aa_course_id'];
        $log_param['lecture_id']          = $attempt_details['aa_lecture_id'];
        $log_param['user_id']             = $attempt_details['aa_user_id'];
        $log_param['grade']               = isset($grade_awarded['gr_name']) ? $grade_awarded['gr_name'] : '';
        $log_param['percentage_of_marks'] = $percentage;
        update_lecture_log_wiht_subscription($log_param);
        //End of Updating lecture grade in course subscription table
        $course_id                        = $log_param['course_id'];
        $grade                            = $save['aa_grade'];
        $get_faculty_record = $this->User_model->user(array('select'=>'us_name,us_image','id'=>$this->__loggedInUser['id']));
        $response['faculty_name'] = $get_faculty_record['us_name'];
        $response['faculty_img']  = $get_faculty_record['us_image'];


        $response['grade']          = isset($grade_awarded['gr_name']) ? $grade_awarded['gr_name'] : '';
        $response['mark_scored']    = $mark_scored;
        $response['error']          = false;
        $response['message']        = 'Quiz evaluated successfully';

        $lecture                    = $this->Course_model->lecture(array('id' => $attempt_details['aa_lecture_id'],'select' => 'cl_lecture_name'));
        $course                     = $this->Course_model->course(array('id' => $attempt_details['aa_course_id'],'select'=>'cb_title'));
         //Notification
         $this->load->library('Notifier');
         $this->notifier->push(
             array(
                 'action_code' => 'quiz_graded',
                 'assets' => array('quiz_name' => $lecture['cl_lecture_name'],'course_name' => $course['cb_title'],'grade' => $grade, 'course_id'=>$course_id),
                 'target' => $attempt_details['aa_lecture_id'],
                 'individual' => true,
                 'push_to' => array($attempt_details['aa_user_id'])
             )
         );
         //End notification
        $this->output_json($response);
    }

    function save_grade()
    {
        $this->load->model('Report_model');
        $message         = array();
        $attempt_id      = $this->input->post('attempt_id');
        $grade           = $this->input->post('grade');
        if(!$attempt_id)
        {
            $message[] = 'Attempt id missing';
        }
        if(!$grade)
        {
            $message[] = 'Grade missing';
        }
        if( sizeof($message) > 0 )
        {
            $response['error']   = true;
            $response['message'] = implode('<br />', $message);
            $this->output_json($response);
        }
        $save                           = array();
        $save['id']                     = $attempt_id;
        $save['aa_grade']               = $grade;
        $save['aa_valuated']            = '1';
        $save['aa_valuated_by']         = $this->__loggedInUser['id'];
        $update_date                    = date('Y-m-d H:i:s');
        $save['aa_evaluated_date']      = $update_date;
        $this->Report_model->save_assessment_attempts($save);
        $attempt_details    = $this->Report_model->assessment_attempts(array('id' => $attempt_id, 'select' => 'aa_user_id,aa_course_id,aa_lecture_id'));
        
        $course_id          = $attempt_details['aa_course_id'];
        //Updating lecture grade in course subscription table
        $log_param                  = array();
        $log_param['course_id']     = $attempt_details['aa_course_id'];
        $log_param['lecture_id']    = $attempt_details['aa_lecture_id'];
        $log_param['user_id']       = $attempt_details['aa_user_id'];
        $log_param['grade']         = $grade;
        update_lecture_log_wiht_subscription($log_param);
        //End of Updating lecture grade in course subscription table

        $lecture                    = $this->Course_model->lecture(array('id' => $attempt_details['aa_lecture_id'],'select' => 'cl_lecture_name'));
        $course                     = $this->Course_model->course(array('id' => $attempt_details['aa_course_id'],'select'=>'cb_title'));
        $send_mail                  = $this->User_model->user(array('select'=>'us_email,us_name','id'=>$attempt_details['aa_user_id']));
        $template                   = $this->ofabeemailer->template(array('email_code' => 'assessment_grade'));
        //$param['to']                = array('santhoshkumar@enfintechnologies.com');
        $param['to']                = $send_mail['us_email'];
        $param['subject']           = $template['em_subject'];
        $contents                   = array(
                                        'site_name'         => config_item('site_name')
                                        , 'username'        => $send_mail['us_name']
                                        , 'grade'           => $grade
                                        , 'type'            => 'quiz'
                                        , 'course_title'    => $course['cb_title']
                                        , 'assessment'      => $lecture['cl_lecture_name']
                                        , 'site_url'        => site_url()
                                    );
        $param['body']              = $this->ofabeemailer->process_mail_content($contents, $template['em_message']);
        $this->ofabeemailer->send_mail($param);
        
        //Notification
        $this->load->library('Notifier');
        $this->notifier->push(
            array(
                'action_code' => 'quiz_graded',
                'assets' => array('quiz_name' => $lecture['cl_lecture_name'],'course_name' => $course['cb_title'],'grade' => $grade, 'course_id'=>$course_id),
                'target' => $attempt_details['aa_lecture_id'],
                'individual' => true,
                'push_to' => array($attempt_details['aa_user_id'])
            )
        );
        //End notification

        $get_faculty_record = $this->User_model->user(array('select'=>'us_name,us_image','id'=>$this->__loggedInUser['id']));
        $response['error']   = false;
        $response['message'] = 'Quiz graded successfully';
        $response['faculty_name'] = $get_faculty_record['us_name'];
        $response['faculty_img']  = $get_faculty_record['us_image'];
        $this->output_json($response);

    }

    function report($attempt_id = 0)
    {
        redirect('login');
        $data = array();
        $data['attempt_id'] = $attempt_id;
        $this->load->view(config_item('admin_folder').'/quiz_report_details', $data);
    }

    function output_json($response)
    {
        echo json_encode($response);exit;
    }

}
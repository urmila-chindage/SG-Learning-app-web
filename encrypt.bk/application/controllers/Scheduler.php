<?php

class Scheduler extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->model('Test_result_model');
        $this->load->model('Scheduler_model');
    }

    function index(){
        //$this->login();
    }

    function livecontents(){
        $response           = array();
        
        $event_id           = $this->input->post('sessionID');
        $event_id           = $event_id==''?false:$event_id;

        if(!$event_id){
            $response['status']     = 'failed';
            $response['message']    = 'Invalid event.';
            echo json_encode($response);
            exit();
        }

        $event_id           = base64_decode($event_id);

        $course_details     = $this->Scheduler_model->CourseLive(array('event_id'=>$event_id));
        $assessment_r       = array();
        if(isset($course_details['id'])){
            $assessments        = $this->Scheduler_model->Assessments(array('course_id'=>$course_details['id'],'select'=>'course_lectures.id,course_lectures.cl_lecture_name AS title,assessments.id AS assessment_id,assessments.id AS link'));
            $i = 0;
            foreach ($assessments as $a_key => $assessment) {
                $assessment_r[$i]['title']   = $assessment['title'];
                $assessment_r[$i]['link']    = site_url('material/test').'/'.$assessment['link'].'#instruction';
                $assessment_r[$i]['id']      = $assessment['assessment_id'];
                $i++;
            }

        }else{
            $response['status']     = 'failed';
            $response['message']    = 'Course not found.';
            echo json_encode($response);
            exit();
        }

        $details                        = array();

        $details['name']                = $course_details['cl_lecture_name'];
        $details['zone']                = 'GMT +5.30';
        $details['admin']               = config_item('us_name');
        $details['date']                = date('d M, Y',strtotime($course_details['ll_date']));
        $details['time']                = date('g:i A',strtotime($course_details['ll_time']));

        $response['status']             = 'success';
        $response['message']            = 'Successfully fetched.';
        $response['data']['details']    = $details;
        $response['data']['documents']  = $this->get_files($course_details['ll_files']);
        $response['data']['quiz']       = $assessment_r;

        echo json_encode($response);
        //echo '<pre>';print_r($course_details);die;
    }

    private function get_files($input = false){
        $files = $input?json_decode($input,true):array();
        //echo '<pre>';print_r($files);die;
        $i = 0;
        $return         = array();
        foreach ($files as $f_key => $file){
            //$files[$f_key]['link'] = livefiles_path().'/'.$file['link'];
            $return[$i]['title']   = $file['title'];
            $return[$i]['link']    = livefiles_path().'/'.$file['link'];
            $i++;
        }
        //echo '<pre>';print_r($files);die;

        return $return;
    }

    function assessments($assesment_id = false){
        
        $response                 = array();
        $question                 = array(); 
        $question_data            = array();
        //$question                 = array();
        
        $total_correct_questions  = 0;
        $count                    = 0;
        $assesments               = $this->Test_result_model->get_assesment(array('assesment_id' => $assesment_id  ));

        $total_questions          = $this->Test_result_model->get_total_correct_questions($assesment_id );
        //echo '<pre>'; print_r($total_questions); die;
        for($i=0; $i<count($total_questions); $i++) 
        {
           $question = $total_questions[$i]['q_question'];
           $question_data[$i]['qid']   = $total_questions[$i]['ar_question_id'];
           $question_data[$i]['total_correct']   = $total_questions[$i]['total_correct'];
           $question_data[$i]['question'] = base64_encode($question);
        }

        foreach ($total_questions as  $value)
        {
           $total_correct_questions =  (int)$total_correct_questions + (int)$value['total_correct']; 
           $count                   =  $count + 1;
        }
         
        $total_users                      = $this->Test_result_model->get_total_users($assesment_id ); 

        $response['data']['name']                 = $assesments['assesment_name'];
        $response['data']['correct_questions']    = $question_data;
        $response['data']['total_users_attended'] = $total_users[0];
        //echo '<pre>'; print_r($response);die();

        $this->load->view(config_item('theme').'/aquestionreport',$response);
    }

}
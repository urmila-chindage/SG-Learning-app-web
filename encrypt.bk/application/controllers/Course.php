<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Course extends CI_Controller {

	function __construct()
    {
        parent::__construct();
        $method = $this->router->fetch_method();
        
        if($method == 'topics')
        {
            $redirect     = $this->auth->is_logged_in_common(false);
        }
        else
        {
            $redirect     = $this->auth->is_logged_in_user(false, false);
        }
        $explicit_method  = array('view','listing','topics', 'load_reviews', 'vimeo', 'flowplayer');
        if (!$redirect && !in_array($method, $explicit_method))
        {
            $this->session->set_flashdata('redirect',current_url());
            redirect('login');
        }
        $this->lang->load('course_description');
        $this->__limit = 4;
    }

    function dashboard($course_id = false){

        $this->load->model('Course_model');
        $data                   = array();
        
        $review_per_page        = 10;
        $user                   = $this->auth->get_current_user_session('user');
        
        $objects                = array();
        $objects['key']         = 'course_'.$course_id;
        $callback               = 'course_details';
        $params                 = array('id' => $course_id, 'bundle' => true);
        $course_details         = $this->memcache->get($objects, $callback, $params);
        
        $objects                = array();
        $objects['key']         = 'enrolled_' . $user['id'];
        $callback               = 'my_subscriptions';
        $params                 = array('user_id' => $user['id'], 'courses_only' => true,'order_by' => true);
        $enrolled               = $this->memcache->get($objects, $callback, $params);
        foreach ($enrolled as $e_key => $enroll) 
        {
            $course_completion = 0;
            $course_completion += $enroll['cs_percentage'];
            $course_completion = round($course_completion);
        }
        
        if(!$course_details || $course_details['cb_deleted'] == '1')
        {
            $this->session->set_flashdata('error','This Course seems like deleted, please contact admin.'); 
            redirect('dashboard/courses');exit;
        }
        
        $objects                                        = array();
        $objects['key']                                 = 'subscription_'.$course_id.'_'.$user['id'];
        $callback                                       = 'subscription_details';
        $params                                         = array('user_id' => $user['id'],'course_id'=>$course_id);
        $course_details['subscription_old']             = $this->memcache->get($objects, $callback,$params);
        $s_param                                        = array('user_id' => $user['id'], 'course_id' => $course_id, 'select' => 'course_subscription.id,course_subscription.cs_course_id,course_subscription.cs_user_id,course_subscription.cs_course_validity_status,course_subscription.cs_approved,course_subscription.cs_certificate_issued,course_subscription.cs_forum_blocked,course_subscription.cs_percentage,course_subscription.cs_course_validity_status,course_subscription.cs_subscription_date,course_subscription.cs_start_date,course_subscription.cs_end_date,course_subscription.cs_auto_grade,course_subscription.cs_manual_grade,course_subscription.cs_lecture_log,course_subscription.cs_last_played_lecture');
        $course_details['subscription']                 = $this->Course_model->subscription_details($s_param);
        $course_details['subscription']['my_rating']    = $course_details['subscription_old']['my_rating'];
        $my_subscription_id                             = $course_details['subscription']['id'];
        $cs_percentage                                  = isset($course_details['subscription']['cs_percentage']) ? $course_details['subscription']['cs_percentage'] : 0;
        
        if(!isset($course_details['id']) || $course_details['subscription']['cs_approved'] == 0 || $course_details['subscription']['cs_approved'] == 2){
            redirect('dashboard');
        }

        $lectures_cp                                    = $course_details['subscription']['cs_lecture_log']!=''?json_decode($course_details['subscription']['cs_lecture_log'],true):array();//$this->Course_model->user_lectures(array('user_id'=>$user['id'],'course_id'=>$course_id));
        $lectures                                       = array();
        $completed_lectures                             = 0;
        $lecture_count                                  = 0;
        // $course_completion                              = 0;


        $lectures_override                              = array();
        $lecturesExclude                                = array();
        $currentLectures                                = array();
        
        foreach($lectures_cp as $log_key => $lecture)
        {
            $lectures[$log_key]    = $lecture;
            $lecturesExclude[]     = $log_key;
        }

        $lecture_override                               = $course_details['override'];
        $batches                                        = explode(',', $user['us_groups']);
        
        foreach($course_details['lectures'] as $l_key => $lecture)
        {
            $lecture_count++;
            
            $course_details['lectures'][$l_key]['ll_percentage']    = 0;
            $course_details['lectures'][$l_key]['ll_attempt']       = 0;
            $course_details['lectures'][$l_key]['ll_marks']         = 0;

            if(isset($lectures[$lecture['id']]))
            {
                $currentLectures[]                 = $lecture['id'];
                $lectures_override[$lecture['id']] = $lectures[$lecture['id']];
                if(isset($lectures[$lecture['id']]['percentage']) && $lectures[$lecture['id']]['percentage'] > 95)
                {
                    $completed_lectures++;
                }

                // if(isset($lectures[$lecture['id']]['percentage']))
                // {
                //     $course_completion += $lectures[$lecture['id']]['percentage'];  
                // }
            
                $course_details['lectures'][$l_key]['ll_percentage']    = isset($lectures[$lecture['id']]['percentage'])?$lectures[$lecture['id']]['percentage']:0;
                $course_details['lectures'][$l_key]['ll_attempt']       = isset($lectures[$lecture['id']]['views'])?$lectures[$lecture['id']]['views']:0;
                // $course_details['lectures'][$l_key]['ll_marks']         = $lectures[$lecture['id']]['marks'];
            }

            if(isset($lecture_override[$lecture['id']]))
            {
                foreach($batches as $batch)
                {
                    if(is_numeric($batch) && isset($lecture_override[$lecture['id']][$batch]))
                    {
                        $course_details['lectures'][$l_key]['cl_limited_access']  = $lecture_override[$lecture['id']][$batch]['lo_attempts'];
                    }
                }
            }
        }
        
        // $course_completion          = $lecture_count ? round(($course_completion / $lecture_count)) : 0;
        
        if($lecturesExclude != $currentLectures || !$course_completion || $cs_percentage != $course_completion)
        {
            $this->update_subscribed_lectures_log(array('subscription_id' => $my_subscription_id, 'course_completion' => $course_completion, 'l_logs' => $lectures_override));
            $this->memcache->delete('subscription_'.$course_id.'_'.$user['id']);
            $this->memcache->delete('enrolled_'.$user['id']);
            $this->memcache->delete('mobile_enrolled_'.$user['id']);
            $this->memcache->delete('course_'.$user['id']);
        }
        
        $course_details['lecture_count']        = $lecture_count;
        $course_details['completed_lectures']   = $completed_lectures;
        
        $data['course']                         = $course_details;
        $data['user']                           = $user;
        $data['admin']                          = $this->config->item('us_name');
        $data['course_completion']              = $course_completion;
        //print_r($data);die;
        $this->load->view($this->config->item('theme').'/c_dashboard_overview',$data);
    }

    function update_subscribed_lectures_log($params = array())
    {
        if(isset($params['subscription_id']) && !empty($params['l_logs']))
        {
            $save_param                             = array();
            $save_param['id']                       = $params['subscription_id'];
            $save_param['cs_percentage']            = $params['course_completion'];
            $save_param['cs_lecture_log']           = json_encode($params['l_logs']);
            $save_param['cs_lecture_log']           = json_encode($params['l_logs']);
            $update_date                            = date('Y-m-d H:i:s');
            $save['updated_date']                   = $update_date;
            
            $this->Course_model->save_last_played_lecture($save_param);
            //echo $this->db->last_query();die;
        }
    }

    function get_topic_progress($course_id = false)
    {
        $response           = array();
        if(!$course_id)
        {
            $response['success']    = false;
            $response['message']    = 'Invalid course id.';
            echo json_encode($response);die;
        }
        $user               = $this->auth->get_current_user_session('user');
        if(!isset($user['id']))
        {
            $response['success']    = false;
            $response['message']    = 'Invalid user.';
            echo json_encode($response);die;
        }

        $objects        = array();
        $objects['key'] = 'course_topic_'.$course_id.'_'.$user['id'];
        $callback       = 'topic_wise_progress';
        $params         = array('user_id' => $user['id'],'course_id'=>$course_id);
        //$this->memcache->delete($objects['key']);
        $topic_data     = $this->memcache->get($objects, $callback,$params);
        if($topic_data['cs_invalidate_topic']){
            $this->regenerate_topic(array('id'=>$topic_data['id'],'user_id' => $user['id'],'course_id'=>$course_id));
            $topic_data     = $this->memcache->get($objects, $callback,$params);
        }
        $topic_data['cs_topic_progress']    = $topic_data['cs_topic_progress']!=''?json_decode($topic_data['cs_topic_progress'],true):array();
        $topic_progress = array();
        foreach($topic_data['cs_topic_progress'] as $t_key => $tp)
        {
            $tp['id']           = $t_key;
            $topic_progress[]   = $tp;
        }
        $response['success']    = true;
        $response['message']    = 'Topic data fetch success.';
        $response['data']       = $topic_progress;
        echo json_encode($response);
    }

    function get_subject_progress($course_id = false)
    {
        $this->load->model('Course_model');
        $response                                               = array();
        if(!$course_id)
        {
            $response['success']                                = false;
            $response['message']                                = 'Invalid course id.';
            echo json_encode($response);die;
        }
        $user                                                   = $this->auth->get_current_user_session('user');
        if(!isset($user['id']))
        {
            $response['success']                                = false;
            $response['message']                                = 'Invalid user.';
            echo json_encode($response);die;
        }

        $topic_data                                             = $this->Course_model->course_topic_progress(array('user_id'=>$user['id'],'course_id'=>$course_id));
        if($topic_data['cs_invalidate_topic'])
        {
            $this->regenerate_topic(array('id'=>$topic_data['id'],'user_id' => $user['id'],'course_id'=>$course_id));
        }

        $subject_data                                           = $this->Course_model->course_subject_progress(array('user_id'=>$user['id'],'course_id'=>$course_id));
        
        $response['success']                                    = true;
        $response['message']                                    = 'Subject data fetch success.';
        $response['data']                                       = $subject_data;
        echo json_encode($response);
    }

    private function regenerate_topic($param = array())
    {
        $subscription_id    = isset($param['id'])?$param['id']:0;
        $user_id            = isset($param['user_id'])?$param['user_id']:0;
        $course_id          = isset($param['course_id'])?$param['course_id']:0;
        
        $this->load->model('Course_model');
        //Calculating topic data.
        $assessments_attempts   = $this->Course_model->assesment_attempt_details(array('user_id'=>$user_id,'completed'=>true,'course_id'=>$course_id));
        $attempt_data       = array();
        $subject_data       = array();
        foreach($assessments_attempts as $aa_key => $assessments_attempt)
        {
            $attempt_details    = json_decode($assessments_attempt['aa_assessment_detail'],true);
            foreach($attempt_details['questions'] as $aq_key => $assessment_question)
            {
                // echo '<pre>';print_r($assessment_question);die;
                $assessment_question['topics']    = json_decode($assessment_question['topics'],true);
                $assessment_question['subject']   = json_decode($assessment_question['subject'],true);
                if(!isset($attempt_data[$assessment_question['topics']['id']]))
                {
                    $attempt_data[$assessment_question['topics']['id']]         = array();
                    $attempt_data[$assessment_question['topics']['id']]['name'] = $assessment_question['topics']['topic_name'];
                    $attempt_data[$assessment_question['topics']['id']]['ra']   = 0; //Right
                    $attempt_data[$assessment_question['topics']['id']]['wa']   = 0; //Wrong
                }

                if(!isset($subject_data[$assessment_question['subject']['id']])){
                    $subject_data[$assessment_question['subject']['id']]         = array();
                    $subject_data[$assessment_question['subject']['id']]['name'] = $assessment_question['subject']['subject_name'];
                    $subject_data[$assessment_question['subject']['id']]['ra']   = 0; //Right
                    $subject_data[$assessment_question['subject']['id']]['wa']   = 0; //Wrong
                }

                if(isset($assessment_question['user_mark']) && $assessment_question['user_mark'] > 0)
                {
                    $attempt_data[$assessment_question['topics']['id']]['ra']++;
                    $subject_data[$assessment_question['subject']['id']]['ra']++;
                }else{
                    $subject_data[$assessment_question['subject']['id']]['wa']++;
                    $attempt_data[$assessment_question['topics']['id']]['wa']++;
                }

            }
        }
        $topic_array        = array();

        foreach($attempt_data as $ad_key => $ad)
        {
            $topic_array[$ad_key]['name']       = $ad['name'];
            $total                              = $ad['ra'] + $ad['wa'];
            $percentage                         = round(($ad['ra']/$total)*100);
            $topic_array[$ad_key]['percentage'] = $percentage;
        }

        $subject_array      = array();
        // echo '<pre>';print_r($subject_data);die;
        $subject_ids        = array();
        foreach($subject_data as $sd_key => $sd)
        {
            $subject_ids[]                          = $sd_key;
            $subject_array[$sd_key]['name']         = $sd['name'];
            $total                                  = $sd['ra'] + $sd['wa'];
            $percentage                             = round(($sd['ra']/$total)*100);
            $subject_array[$sd_key]['percentage']   = $percentage;
        }

        $subjects       = array();

        if(!empty($subject_ids)){
            $subjects       = $this->Course_model->user_subject(array('user_id'=>$user_id,'course_id'=>$course_id,'subjects'=>$subject_ids));
        }

        foreach($subjects as $s_key => $subject)
        {
            $subjects[$subject['sr_subject_id']]    = $subject;
            unset($subjects[$s_key]);
        }

        foreach($subject_array as $sa_key => $sub_array)
        {
            if(isset($subjects[$sa_key]))
            {
                if($subjects[$sa_key]['sr_percentage'] != $sub_array['percentage']){
                    $save                   = array();
                    $save['id']             = $subjects[$sa_key]['id'];
                    $save['sr_percentage']  = $sub_array['percentage'];
                    $this->Course_model->save_user_subject($save);
                }
            }
            else
            {
                $save                   = array();
                $save['sr_course_id']   = $course_id;
                $save['sr_user_id']     = $user_id;
                $save['sr_subject_id']  = $sa_key;
                $save['sr_percentage']  = $sub_array['percentage'];
                $this->Course_model->save_user_subject($save);
            }
        }

        // echo '<pre>';print_r($subject_array);die;
        $topic_array                    = json_encode($topic_array);
        $update                         = array();
        $update['id']                   = $subscription_id;
        $update['cs_topic_progress']    = $topic_array;
        $update['cs_invalidate_topic']  = '0';
        $this->Course_model->save_last_played_lecture($update);
        $this->memcache->delete('course_topic_'.$course_id.'_'.$user_id);
        return true;
    }

    function get_full_curriculum($course_id = false){
        $response               = array();
        if(!$course_id)
        {
            $response['success']    = false;
            $response['message']    = 'Invalid course id.';
            echo json_encode($response);die;
        }
        $user                   = $this->auth->get_current_user_session('user');

        if(!isset($user['id']))
        {
            $response['success']    = false;
            $response['message']    = 'Invalid user.';
            echo json_encode($response);die;
        }
        
        $objects        = array();
        $objects['key'] = 'course_'.$course_id;
        $callback       = 'course_details';
        $params         = array('id' => $course_id);
        $course_details         = $this->memcache->get($objects, $callback, $params); 
        $lectures               = $this->Course_model->user_lectures(array('user_id'=>$user['id'],'course_id'=>$course_id));
        $response['sections']   = $course_details['sections'];
        $response['lectures']   = $course_details['lectures'];
        
        $response['success']    = true;
        $response['message']    = 'Fetch curriculum success.';
        return print(json_encode($response));
    }

    function assessments($course_id = false){
        $response               = array();
        $this->load->model('Course_model');
        $user                   = $this->auth->get_current_user_session('user');
        if(!$course_id)
        {
            $response['success']    = false;
            $response['message']    = 'Invalid course id.';
            echo json_encode($response);die;
        }

        if(!isset($user['id']))
        {
            $response['success']    = false;
            $response['message']    = 'Invalid user.';
            echo json_encode($response);die;
        }

        $user_batches       = explode(',',$user['us_groups']);
        $batch_query        = '';
        if($user['us_groups'] != '')
        {
            foreach($user_batches as $b_key => $ub)
            {
                if($b_key == 0)
                {
                    $batch_query    .= ' CONCAT(",",lo_override_batches,",") LIKE "%,'.$ub.',%"';
                }
                else
                {
                    $batch_query    .= ' OR CONCAT(",",lo_override_batches,",") LIKE "%,'.$ub.',%"';
                }
            }
        }
        
        $objects        = array();
        $objects['key'] = 'course_'.$course_id;
        $callback       = 'course_details';
        $params         = array('id' => $course_id);
        $course_details         = $this->memcache->get($objects, $callback, $params); 

        $assessment_details     = $this->Course_model->assesment_details(array('course_id'=>$course_id));
        $override_data          = $this->Course_model->lecute_override(array('course_id' => $course_id,'batch_query' => $batch_query,'lecture_type'=>'3'));
        $assessments_attempts   = $this->Course_model->assesment_attempt_details(array('user_id'=>$user['id'],'course_id'=>$course_id));
        
        // Get subscription details
        $subscription_objects              = array();
        $subscription_objects['key']       = 'subscription_'.$course_id.'_'.$user['id'];
        $subscription_callback             = 'subscription_details';
        $subscription_params               = array();
        $subscription_params['user_id']    = $user['id'];
        $subscription_params['course_id']  = $course_id;
        $subscription_details              = $this->memcache->get($subscription_objects, $subscription_callback, $subscription_params);


        $user_lectures          = isset($subscription_details['cs_lecture_log'])?json_decode($subscription_details['cs_lecture_log'],true):array();//$this->Course_model->user_lectures(array('user_id'=>$user['id'],'course_id'=>$course_id));

        foreach($assessments_attempts as $aa_key => $aa)
        {
            unset($assessments_attempts[$aa_key]);
            $assessments_attempts[$aa['aa_lecture_id']]   = $aa;
        }

        foreach($assessment_details as $ad_key => $ad)
        {
            unset($assessment_details[$ad_key]);
            $assessment_details[$ad['a_lecture_id']]    = $ad;
        }

        $assessments            = array();
        $section_ids            = array();
        $assessment_sections    = array();

        foreach($course_details['lectures'] as $lecture)
        {
            if($lecture['cl_lecture_type'] == '3')
            {
                $assessments[]  = $lecture;
                if(!in_array($lecture['cl_section_id'],$section_ids))
                {
                    $section_ids[]  = $lecture['cl_section_id'];
                }
            }
        }

        foreach($course_details['sections'] as $section)
        {
            if(in_array($section['id'],$section_ids)){
                $assessment_sections[$section['id']]                = $section;
                $assessment_sections[$section['id']]['assessments'] = array();
            }
        }
        
        // echo $batch_query;die;

        foreach($override_data as $o_key => $od)
        {
            unset($override_data[$o_key]);
            $override_data[$od['lo_lecture_id']]     = $od;
        }

        foreach($assessments as $assessment)
        {
            $assessment['attempted']    = false;
            $assessment['attemptable']  = true;

            $restriction_data           = array('attempts'=>0,'my_attempts'=>0,'from_date'=>'','from_time'=>'','to_date'=>'','to_time'=>'');
            $assessment['attempt_data'] = array();

            $restriction_data['attempts']    = $assessment['cl_limited_access'];
            $restriction_data['my_attempts'] = 0;
            if(isset($assessments_attempts[$assessment['id']]))
            {
                $assessment['attempted']    = true;
                $assessments_attempts[$assessment['id']]['aa_duration'] = $assessments_attempts[$assessment['id']]['aa_duration']<3600?gmdate('i:s',$assessments_attempts[$assessment['id']]['aa_duration']):gmdate('H:i:s',$assessments_attempts[$assessment['id']]['aa_duration']);
                $assessments_attempts[$assessment['id']]['aa_total_duration'] = $assessments_attempts[$assessment['id']]['aa_total_duration']<3600?gmdate('i:s',$assessments_attempts[$assessment['id']]['aa_total_duration']):gmdate('H:i:s',$assessments_attempts[$assessment['id']]['aa_total_duration']);
                $assessments_attempts[$assessment['id']]['aa_attempted_date'] = date('Y M j S',strtotime($assessments_attempts[$assessment['id']]['aa_attempted_date']));
                if(isset($user_lectures[$assessment['id']]))
                {
                    $assessment['log_data']             = $user_lectures[$assessment['id']];
                    $restriction_data['my_attempts']    = isset($assessment['log_data']['views'])?$assessment['log_data']['views']:0;
                }
                $assessment['attempt_data'] = $assessments_attempts[$assessment['id']];
            }
            $assessment['a_data']       = $assessment_details[$assessment['id']];
            $assessment['o_data']       = array();

            $restriction_data['from_date']          = '';
            $restriction_data['from_time']          = '';
            if($assessment['a_data']['a_from_availability']){
                $restriction_data['from_date']          = $assessment['a_data']['a_from'];
                $restriction_data['from_time']          = $assessment['a_data']['a_from_time'];
            }
            $restriction_data['to_date']            = '';
            $restriction_data['to_time']            = '';
            if($assessment['a_data']['a_to_availability']){
                $restriction_data['to_date']            = $assessment['a_data']['a_to'];
                $restriction_data['to_time']            = $assessment['a_data']['a_to_time'];
            }

            if(isset($override_data[$assessment['id']]))
            {
                $restriction_data['from_date']      = $override_data[$assessment['id']]['lo_start_date'];
                $restriction_data['from_time']      = $override_data[$assessment['id']]['lo_start_time'];
                $restriction_data['to_date']        = $override_data[$assessment['id']]['lo_end_date'];
                $restriction_data['to_time']        = $override_data[$assessment['id']]['lo_end_time'];


                $override_data[$assessment['id']]['lo_end_date']    = date('Y M j S',strtotime($override_data[$assessment['id']]['lo_end_date']));
                $override_data[$assessment['id']]['lo_duration']    = ($override_data[$assessment['id']]['lo_duration']*60)<3600?gmdate('i:s',($override_data[$assessment['id']]['lo_duration']*60)):gmdate('H:i:s',($override_data[$assessment['id']]['lo_duration']*60));
                $assessment['o_data']                               = $override_data[$assessment['id']];
                $restriction_data['attempts']       = $override_data[$assessment['id']]['lo_attempts'];
            }

            if($assessment['a_data']['a_to_availability'])
            {
                $assessment['a_data']['a_to']     = date('Y M j S',strtotime($assessment['a_data']['a_to']));
            }
            else
            {
                $assessment['a_data']['a_to']     = '';
            }

            $now                = strtotime(date('Y-m-d H:i:s'));
            if($assessment['attemptable'] && $restriction_data['from_date'] != ''){
                $date_from          = $restriction_data['from_date'].' '.$restriction_data['from_time'];
                $date_from          = strtotime($date_from);
                $assessment['attemptable']  = $now>$date_from?true:false;
            }
            
            if($assessment['attemptable'] && $restriction_data['to_date'] != ''){
                $date_to            = $restriction_data['to_date'].' '.$restriction_data['to_time'];
                $date_to            = strtotime($date_to);

                if(isset($override_data[$assessment['id']]))
                {
                    switch($override_data[$assessment['id']]['lo_period_type']){
                        case 1:
                            $date_to     = $date_to + ($override_data[$assessment['id']]['lo_period']*(24*3600));
                        break;
                        case 2:
                            $date_to     = $date_to + ($override_data[$assessment['id']]['lo_period']*3600);
                        break;
                        case 3:
                            $date_to     = $date_to + ($override_data[$assessment['id']]['lo_period']*60);
                        break;
                    }
                }

                $assessment['attemptable']  = $now<$date_to?true:false;
            }

            if($assessment['attemptable'] && $restriction_data['attempts'] > 0){
                $assessment['attemptable']  = $restriction_data['my_attempts']>=$restriction_data['attempts']?false:true;
            }

            $assessment['a_data']['a_duration']   = ($assessment['a_data']['a_duration']*60)<3600?gmdate('i:s',($assessment['a_data']['a_duration']*60)):gmdate('H:i:s',($assessment['a_data']['a_duration']*60));
            $assessment_sections[$assessment['cl_section_id']]['assessments'][]     = $assessment;
        }
        $response['success']    = true;
        $response['data']       = $assessment_sections;
        //echo '<pre>';print_r($user);die;
        echo json_encode($response);
    }

    function assignments($course_id = false){
        $response               = array();
        $this->load->model('Course_model');
        $user                   = $this->auth->get_current_user_session('user');
        if(!$course_id)
        {
            $response['success']    = false;
            $response['message']    = 'Invalid course id.';
            echo json_encode($response);die;
        }

        if(!isset($user['id']))
        {
            $response['success']    = false;
            $response['message']    = 'Invalid user.';
            echo json_encode($response);die;
        }

        $user_batches       = explode(',',$user['us_groups']);
        $batch_query        = '';
        if($user['us_groups'] != '')
        {
            foreach($user_batches as $b_key => $ub)
            {
                if($b_key == 0)
                {
                    $batch_query    .= ' CONCAT(",",lo_override_batches,",") LIKE "%,'.$ub.',%"';
                }
                else
                {
                    $batch_query    .= ' OR CONCAT(",",lo_override_batches,",") LIKE "%,'.$ub.',%"';
                }
            }
        }
        
        $objects        = array();
        $objects['key'] = 'course_'.$course_id;
        $callback       = 'course_details';
        $params         = array('id' => $course_id);
        $course_details         = $this->memcache->get($objects, $callback, $params); 

        // echo '<pre>';print_r($grades);die;
        $assignment_details         = $this->Course_model->assignment_details(array('course_id'=>$course_id));
        $override_data              = $this->Course_model->lecute_override(array('course_id' => $course_id,'batch_query' => $batch_query,'lecture_type'=>'8'));
        $assignment_attempt_details = $this->Course_model->assignment_attempt_details(array('user_id'=>$user['id'],'course_id'=>$course_id));

        foreach($assignment_attempt_details as $aa_key => $aa)
        {
            unset($assignment_attempt_details[$aa_key]);
            $assignment_attempt_details[$aa['dtua_lecture_id']]   = $aa;
        }

        // echo '<pre>';print_r($assignment_details);die;
        foreach($assignment_details as $ad_key => $ad)
        {
            unset($assignment_details[$ad_key]);
            $assignment_details[$ad['dt_lecture_id']]    = $ad;
        }

        $assignments            = array();
        $section_ids            = array();
        $assignment_sections    = array();

        foreach($course_details['lectures'] as $lecture)
        {
            if($lecture['cl_lecture_type'] == '8')
            {
                $assignments[]  = $lecture;
            }
        }
        
        // echo $batch_query;die;

        foreach($override_data as $o_key => $od)
        {
            unset($override_data[$o_key]);
            $override_data[$od['lo_lecture_id']]     = $od;
        }

        foreach($assignments as $a_key => $assignment)
        {
            $assignment['attempt_data'] = array();
            
            $assignment['a_data']       = isset($assignment_details[$assignment['id']])?$assignment_details[$assignment['id']]:array();
            $assignment['o_data']       = array();
            $assignment['expired']      = false;

            if(isset($assignment['a_data']['dt_last_date']) && $assignment['a_data']['dt_last_date'] != '0000-00-00' && $assignment['a_data']['dt_last_date'] != '')
            {
                $startdate = $assignment['a_data']['dt_last_date'];
                $expire = strtotime($startdate);
                $today = strtotime("today midnight");

                if($today > $expire){
                    $assignment['expired']      = true;
                }
                $assignment['a_data']['dt_last_date']     = date('Y M j S',strtotime($assignment['a_data']['dt_last_date']));
            }else{
                $assignment['a_data']['dt_last_date'] = '';
            }

            if(isset($override_data[$assignment['id']]))
            {
                $startdate = $override_data[$assignment['id']]['lo_end_date'];
                $expire = strtotime($startdate);
                $today = strtotime("today midnight");

                switch($override_data[$assignment['id']]['lo_period_type']){
                    case 1:
                        $expire     = $expire + ($override_data[$assignment['id']]['lo_period']*(24*3600));
                    break;
                    case 2:
                        $expire     = $expire + ($override_data[$assignment['id']]['lo_period']*3600);
                    break;
                    case 3:
                        $expire     = $expire + ($override_data[$assignment['id']]['lo_period']*60);
                    break;
                }

                if($today > $expire){
                    $assignment['expired']      = true;
                }else{
                    $assignment['expired']      = false;
                }

                $override_data[$assignment['id']]['lo_end_date']  = date('Y M j S',strtotime($override_data[$assignment['id']]['lo_end_date']));
                $override_data[$assignment['id']]['lo_duration']   = ($override_data[$assignment['id']]['lo_duration']*60)<3600?gmdate('i:s',($override_data[$assignment['id']]['lo_duration']*60)):gmdate('H:i:s',($override_data[$assignment['id']]['lo_duration']*60));
                $assignment['o_data']   = $override_data[$assignment['id']];
            }

            $assignment['submitted']    = false;
            if(isset($assignment_attempt_details[$assignment['id']]))
            {
                $assignment['submitted']    = true;
                $assignment_attempt_details[$assignment['id']]['created_date']  = date('Y M j S',strtotime($assignment_attempt_details[$assignment['id']]['created_date']));
                $assignment['submission']   = $assignment_attempt_details[$assignment['id']];
            }

            $assignments[$a_key]     = $assignment;
        }
        $response['success']    = true;
        $response['data']       = $assignments;
        // echo '<pre>';print_r($response['data']);die;
        echo json_encode($response);
    }

    function load_chart($course_id = false,$user_id = false){
        $response               = array();
        if(!$course_id || !$user_id){
            $response['success']    = false;
            $response['message']    = 'Invalid course/userid provided.';
            return print(json_encode($response));
        }
        $this->load->model('Report_model');
        $this->load->model(array('Course_model'));
        $assessments            = $this->Course_model->db_get_assesments(array('select'=>'assessments.id,course_lectures.cl_lecture_name','course_id'=>$course_id));
        $assessment_detail      = array();
        $assessment_categories  = array();
        $rank = 0;
        foreach ($assessments as $key => $assessment) {
            $rank = 0;
            $all_attempts                               = $this->Report_model->attempts(array('assessment_id' => $assessment['id']));
            $response['rank_graph'][$key]['lecture']    = $assessment['cl_lecture_name'];
            $response['rank_graph'][$key]['my_rank']    = 0;
            $response['rank_graph'][$key]['attempts']   = count($all_attempts); 
            if(!empty($all_attempts))
            {
                foreach ($all_attempts as $all_attempt)
                { 
                    $rank++;
                    if($all_attempt['aa_user_id'] == $user_id)
                    {
                        $response['rank_graph'][$key]['my_rank']    = $rank;
                        $response['rank_graph'][$key]['date']       = date("M j, Y", strtotime( $all_attempt['aa_attempted_date']));
                        break;
                    }
                }

            }
        }


        $temp_attempt = array();
        $temp_mark    = array();
        if(!empty($assessments)){
            $assessment_categories              = $this->Course_model->db_question_categories_in_assesment($assessments);
        
            foreach ($assessment_categories as $i => $assessment_category) {

                foreach ($assessments as $j => $assessment) {
                    $assessment_categories[$i]['assessment'][$j]['assessment_name'] = $assessment['cl_lecture_name'];
                    $temp_mark                                  = $this->Course_model->db_total_marks(array('category_id'=>$assessment_category['id'],'assessment_id'=>$assessment['id']));
                    $assessment_categories[$i]['assessment'][$j]['total_mark']      = $temp_mark['total_mark'];
                    $temp_attempt                               = $this->Course_model->db_latest_attempt(array('user_id'=>$user_id,'assessment_id'=>$assessment['id']));
                    if($assessment_categories[$i]['assessment'][$j]['total_mark'] == ''){
                        $assessment_categories[$i]['assessment'][$j]['total_mark']  = null;
                        $assessment_categories[$i]['assessment'][$j]['scored_mark'] = 0;
                    }
                    if(isset($temp_attempt['id'])){
                        $assessment_categories[$i]['assessment'][$j]['attended']    = 1;
                        $temp_mark                                  = $this->Course_model->db_scored_marks(array('category_id'=>$assessment_category['id'],'assessment_id'=>$assessment['id'],'attempt_id'=>$temp_attempt['id']));
                        $assessment_categories[$i]['assessment'][$j]['scored_mark'] = $temp_mark['scored_mark'];
                        $assessment_categories[$i]['assessment'][$j]['scored_mark'] =($assessment_categories[$i]['assessment'][$j]['scored_mark'] == '')?0:$assessment_categories[$i]['assessment'][$j]['scored_mark'];
                    }else{
                        $assessment_categories[$i]['assessment'][$j]['attended']    = 0;
                        $assessment_categories[$i]['assessment'][$j]['scored_mark'] = 0;
                    }
                }

            }
        }

        $response['assessment_categories']  = $assessment_categories;

        echo '<pre>';print_r($response);
    }

    function load_reviews($course_id = false,$offset = 0){
        // $review_per_page        = 10;
        // $offset                 = $offset!=0?(($offset-1)*$review_per_page):$offset;
        // $response               = array();
        // if(!$course_id){
        //     $response['success']    = false;
        //     $response['message']    = 'Invalid course id.';
        //     return print(json_encode($response));
        // }
        $this->load->model(array('Course_model'));
        // $response['success']    = true;
        // $response['message']    = 'Fetch review success.';
        // $response['reviews']    = $this->Course_model->db_get_rating(array('course_id'=>$course_id,'limit'=>$review_per_page,'offset'=>$offset ));

        // return print(json_encode($response));
        
        $data           = array();
        $data['limit']  = empty($this->input->post('limit')) ? $this->__limit : $this->input->post('limit');
        $course_id      = empty($this->input->post('course_id')) ? false : $this->input->post('course_id');
        $is_ajax        = $this->input->post('is_ajax');
        $offset         = empty($this->input->post('offset')) ? 0 : $this->input->post('offset');
        
        $data['show_load_button']   = false;
        $data['default_user_path']  = default_user_path();
        $data['user_path']          = user_path();
        $data['title']              = 'Reviews';

        $reviews_param          = array('course_id' => $course_id, 'count' => true);
        $data['total_records']  = $this->Course_model->db_get_rating($reviews_param);

        $reviews_param          = array('course_id' => $course_id, 'limit' => $data['limit'], 'offset' => $offset);
        $reviews                = $this->Course_model->db_get_rating($reviews_param);
        // echo $this->db->last_query();exit;
        $data['start']          = $offset + $data['limit'];
        $count                  = empty($count) ? $data['total_records'] : $count;
        
        if ($data['start'] < $data['total_records']) 
        {
            $data['show_load_button'] = true;
        } 
        else 
        {
            $data['show_load_button'] = false;
        }
        
        $data['reviews']   = $reviews;
        $data['success']   = true;
        
        echo json_encode($data);
    }
    function details($id){
        $objects        = array();
        $objects['key'] = 'course_'.$id;
        $callback       = 'course_details';
        $params         = array('id' => $id);
        $course_details         = $this->memcache->get($objects, $callback, $params);
        echo "<pre>";print_r($course_details);exit;
    }
    
    function view($id)
    {
        //$this->load->view($this->config->item('theme').'/payment_success'); return;die;
        $user                                   = $this->auth->get_current_user_session('user');
        if(isset($user['id']))
        { 
            $objects                            = array(); 
            $objects['key']                     = 'subscription_'.$id.'_'.$user['id']; 
            $this->memcache->delete($objects['key']); 
            $callback                           = 'subscription_details'; 
            $params                             = array('user_id' => $user['id'],'course_id'=>$id);
            $subscription                       = $this->memcache->get($objects, $callback,$params);
            //print_r($subscription); die; 
            if(isset($subscription['cs_course_id']) && $subscription['cs_course_id'] == $id)
            { 
                redirect('course/dashboard/'.$id); return true; 
            } 
        }
         
        $objects        = array();
        $objects['key'] = 'course_'.$id;
        // $this->memcache->delete($objects['key']);
        $callback       = 'course_details';
        $params         = array('id' => $id);
        $course_details = $this->memcache->get($objects, $callback, $params);
        //print_r($course_details);die;
        if(empty($course_details))
        {
            $data            = array();
            $session         = $this->auth->get_current_user_session('user');
            $data['session'] = $session;
            $this->load->view($this->config->item('theme').'/404_error.php', $data); return;
        }
        $data                                     = array();
        $data['course']                           = $course_details;
        $data['course']['cb_validity_expired']    = false;
        $data['course']['cb_expire_on']           = date('d M, Y');
        $data['course']['enrolled']               = false;
            if(isset($user['id'])){
                $objects        = array();
                $objects['key'] = 'subscription_'.$id.'_'.$user['id'];
                $callback       = 'subscription_details';
                $params         = array('user_id' => $user['id'],'course_id'=>$id);
                $subscription   = $this->memcache->get($objects, $callback,$params);
                // echo '<pre>';print_r($subscription);die;
                if(isset($subscription['id'])){
                    $data['course']['enrolled'] = true;
                }
          $this->load->model('course_model');    
          $data['course']['remaning_preview_time'] = $this->course_model->get_user_preview_time(array('course_id' => $id, 'user_id' => $user['id']));
            }
          if($course_details['cb_access_validity'] == 2){
             
              $today      = date('Y-m-d H:i:s');
              $valid_till = $course_details['cb_validity_date'];
              $data['course']['cb_validity_expired'] = strtotime($valid_till)>strtotime($today)?false:true;
              if($data['course']['cb_validity_expired']){
                  $data['course']['self_enroll'] = '2';
              }
              $data['course']['cb_expire_on'] = date('d M, Y',strtotime($course_details['cb_validity_date']));
          }

          if($course_details['cb_status'] == '1'  || $course_details['cb_deleted'] == '0' )
          {
              if($course_details['cb_has_self_enroll'] == '1')
              {
                  $today = date('Y-m-d');
                  $valid_till = $course_details['cb_self_enroll_date'];
                  $data['course']['cb_validity_expired'] = strtotime($valid_till)>=strtotime($today)?false:true;
                  if($data['course']['cb_validity_expired']==false)
                  {
                      $data['course']['self_enroll'] = '1';
                     // $data['course']['cb_access_validity'] = '0';
                  }
                  else 
                  {
                      $data['course']['self_enroll'] = '0';
                      //$data['course']['cb_access_validity'] = '0';
                  }
                  $data['course']['cb_expire_on'] = date('d M, Y',strtotime($course_details['cb_self_enroll_date']));
              }
  
          }
          
          $data['session']        = $user;
          $data['course_id']      = $id;
          $data['title']          = $course_details['cb_title'].'-'.$this->config->item('site_name');
          
          $data['meta_original_title']                    = $data['course']['cb_title'];
          $data['meta_title']                             = $data['course']['cb_meta'];
          $data['meta_description']                       = $data['course']['cb_meta_description'];
          
          //$data['course']['cb_validity_expired'] = false;
        //echo "<pre>";print_r($data);exit;
          $this->load->view($this->config->item('theme').'/course_description_beta', $data);
      
    }

    function curriculam_list(){

        $this->load->model(array('Course_model'));
        $cid = $this->input->post('cid');

        $data['course_details']['sections']             = $this->Course_model->sections(array('course_id' => $cid,  'status'=> 1)); 

        foreach ($data['course_details']['sections'] as $key => $section) {
            
            $data['course_details']['sections'][$key]['lectures']  = $this->Course_model->get_lectures(array('direction'=>'ASC' , 'order_by'=>'cl_order_no', 'course_id'=>  $cid, 'section_id' => $section['id'], 'status' => 1));
        }
        echo json_encode(array('sections' => $data));exit;
    }
    
    function get_full_curriculum_json(){  
        $offset_recieved    = $this->input->post('offset');
        $course_id          = $this->input->post('c_id');

        $response               = array();
        $param                  = array();
        
        $this->load->model(array( 'Course_model'));
        $response['sections'] = $this->Course_model->sections(array('course_id' => $course_id, 'limit' => '', 'status'=>'1'));
        foreach ($response['sections'] as $key => $section){
            $response['sections'][$key]['lectures']  = $this->Course_model->get_lectures(array('direction'=>'ASC', 'status' => 1 , 'order_by'=>'cl_order_no', 'course_id'=>  $course_id, 'section_id' => $section['id']));
        }
        //echo '<pre>';print_r($response['teachers']);;
        echo json_encode($response);
    }

    function change_whishlist(){

        $cid    = $this->input->post('cid');
        $uid    = $this->input->post('uid');
        $stat   = $this->input->post('stat');
        $page   = $this->input->post('page');

        $session  = $this->auth->get_current_user_session('user');
        $data   = array();

        if(isset($session['id'])){

            $this->load->model(array( 'Course_model'));
            $this->Course_model->change_whishlist($cid, $uid, $stat);
            $str    = '';
            if($page == ''){
                if($stat == '1'){
                $str = '<i id="'.$cid.'" class="demo-icon icon-heart wish-added" onclick="remove_wishlist('.$cid.', '.$uid.')" rel="tooltip" title="Remove From Whishlist" ></i>';
                }
                else if($stat == '0'){
                    $str = '<i id="'.$cid.'" class="demo-icon icon-heart" onclick="add_wishlist('.$cid.', '.$uid.')" rel="tooltip" title="Add To Whishlist" ></i>';
                }
            }
            else if($page == 'search'){
                if($stat == '1'){
                $str = '<span class="heart-icon heart-active" data-key="'.$cid.'" onclick="remove_wishlist('.$cid.', \''.$uid.'\', this)"><i class="icon-heart heart-altr"></i></span>';
                }
                else if($stat == '0'){
                    $str = '<span class="heart-icon" data-key="'.$cid.'" onclick="add_wishlist('.$cid.', \''.$uid.'\', this)"><i class="icon-heart heart-altr"></i></span>';
                }
            }
            
            $data['stat'] = 1;
            $data['str']  = $str;

            echo json_encode($data);
        }
        else{

            $data['stat'] = 0;
            echo json_encode($data);
        }
        
    }

    function listing_old()
    {
        $data = array();
        $session    = $this->auth->get_current_user_session('user');
        $data['session'] = $session;
        $this->load->model(array('Course_model'));
        $this->load->model(array('User_model'));
        $data['course_list']            = $this->Course_model->courses(array('status'=> '1', 'not_deleted'=> '1', 'order_by' => 'cb_position', 'direction' => 'ASC'));

        $data['user_course_enrolled']   = $this->User_model->enrolled_course(array('user_id' => $session['id']));
        //echo '<pre>';print_r($session);die();
        $data['admin']              = $this->config->item('acct_name');
        $data['admin_name']         = $this->config->item('us_name');

        $ratting                    = array();
        
        foreach ($data['course_list'] as $key => $course) {
            $data['course_list'][$key]['assigned_tutors'] = $this->Course_model->assigned_tutors(array('course_id'=>$course['id']));
            $data['course_list'][$key]['ratting'] = $this->Course_model->get_ratting(array('course_id' => $course['id'],'cc_status' => '1'));
            $temp_arr = array();
            array_push($temp_arr, 'rate_div_'.$key);
            array_push($temp_arr, $data['course_list'][$key]['ratting']);
            array_push($ratting, $temp_arr);

            $wish_stat = $this->Course_model->get_whishlist_stat($course['id'], $session['id']);

            if($wish_stat == 1){
                $data['course_list'][$key]['wish_stat'] = '<i class="demo-icon icon-heart wish-icon-search wish-added" onclick="remove_wishlist('.$course['id'].', \''.$session['id'].'\', this)" rel="tooltip" data-key="'.$course['id'].'" title="Remove From Whishlist" ></i>';
            }
            else{
                $data['course_list'][$key]['wish_stat'] = '<i class="demo-icon icon-heart wish-icon-search" onclick="add_wishlist('.$course['id'].', \''.$session['id'].'\', this)" data-key="'.$course['id'].'" rel="tooltip" title="Add To Whishlist" ></i>';
            }
        }
        
        $data['rattings'] = json_encode($ratting);

        $this->load->view($this->config->item('theme').'/course_list', $data);

    }
    
    function listing_old1($keyword = "")
    {
        $this->load->model(array('Category_model'));
        $this->load->model(array('Course_model'));
        $this->load->model(array('User_model'));        
        $data = array();
        $data['languages']  = array();
        $session    = $this->auth->get_current_user_session('user');
        $data['session'] = $session;

        $data['user_course_enrolled']   = $this->User_model->enrolled_course(array('user_id' => $session['id']));
        //echo '<pre>';print_r($session);die();
        $data['admin']              = $this->config->item('acct_name');
        $data['admin_name']         = $this->config->item('us_name');
        
        $data['categories'] = $this->Category_model->categories(array('direction' => 'DESC','not_deleted'=>true, 'status'=>'1'));
        $languages          = $this->Course_model->languages(array('restrict_by_tutor_course' => true));
        if (!empty($languages)) {
            foreach ($languages as $language) {
                $data['languages'][$language['id']] = $language;
            }
        }
        
        $offset_recieved    = 0;
        $param              = array();
        
        //calculating page numbers
        $per_page           = 20;
        $data['per_page']   = $per_page;
        $page_num           = 1;
        $offset             = 0;
        $param['offset'] = $offset;
        $param['limit']  = $per_page;
        //end of calucalting page number
        $param['status']    = 1;
        $param['not_deleted']= 1;
        $param['order_by']  = 'cb_position';
        $param['direction'] = 'ASC';
         
        $this->load->model(array('Development_model'));
        $data['course_list']            = $this->Development_model->courses($param);

        $ratting                    = array();
        
        foreach ($data['course_list'] as $key => $course) 
        {
            $data['course_list'][$key]['assigned_tutors'] = $this->Course_model->assigned_tutors(array('course_id'=>$course['id']));
            $data['course_list'][$key]['ratting'] = $this->Course_model->get_ratting(array('course_id' => $course['id'],'cc_status' => '1'));
            $temp_arr = array();
            array_push($temp_arr, 'rate_div_'.$key);
            array_push($temp_arr, $data['course_list'][$key]['ratting']);
            array_push($ratting, $temp_arr);

            $wish_stat = $this->Course_model->get_whish_stat($course['id'], $session['id']);

            if($wish_stat == 1){
                $data['course_list'][$key]['wish_stat'] = '<span class="heart-icon heart-active" data-key="'.$course['id'].'" onclick="remove_wishlist('.$course['id'].', \''.$session['id'].'\', this)"><i class="icon-heart heart-altr"></i></span>';
            }
            else if($wish_stat == 0){
                $data['course_list'][$key]['wish_stat'] = '<span class="heart-icon" data-key="'.$course['id'].'" onclick="add_wishlist('.$course['id'].', \''.$session['id'].'\', this)"><i class="icon-heart heart-altr"></i></span>';
            }
            else if($wish_stat == 2){
                $data['course_list'][$key]['wish_stat'] = '';
            }
        }
        
        $data['rattings'] = json_encode($ratting); 

        $this->load->view($this->config->item('theme').'/course_list_beta', $data); 

    }

    function listing($query = "") 
    {
        $keyword                = $this->input->get('search', TRUE);
        $category               = $this->input->get('categoryid', TRUE);
        //$type                 = $this->input->get('type', TRUE);
        $keyword_arr            = explode('-', $keyword);
        
        $data                   = array();
        $data['keyword_arr']    = $keyword_arr;
        $data['category_id']    = $category!=''?$category:0;
        $data['limit']          = 8;
        $data['languages']      = array();
        $user                   = $this->auth->get_current_user_session('user');
        $data['session']        = $user;
        $data['admin']          = $this->config->item('acct_name');
        $data['admin_name']     = $this->config->item('us_name');
        
        $objects                = array();
        $objects['key']         = 'categories';
        $callback               = 'get_categories';
        $categories             = $this->memcache->get($objects, $callback,array()); 
        $data['categories']     = $categories;
        $objects                = array();
        // $objects['key']         = 'all_courses';
        // $callback               = 'all_courses';
        // $objects['key']         = 'all_sorted_course';
        // $callback               = 'all_sorted_course';
        $objects['key']         = 'sales_manager_all_sorted_courses';
        $callback               = 'sales_manager_all_sorted_courses';
        
        // if(isset($user['id']))
        // {   
        //     $objects            = array();
        //     $objects['key']     = 'all_courses_'.$user['id'];
        //     $callback           = 'all_courses';
        //     $notsubscribed      = array('not_subscribed' => true, 'user_id' => $user['id']);
        //     $courses            = $this->memcache->get($objects, $callback, $notsubscribed);
        // }
        // else
        // {
        //     $courses            = $this->memcache->get($objects, $callback, array());
        // }
        $courses                  = $this->memcache->get($objects, $callback, array());
         //print_r($courses);echo "<br><br>";die();
         //echo '<pre>';print_r($categories);die;
        if (isset($user['id'])) 
        {
            //courses
            $objects              = array();
            $objects['key']       = 'enrolled_' . $user['id'];
            $callback             = 'my_subscriptions';
            $params               = array('user_id' => $user['id']);
            $enrolled_courses     = array();//$this->memcache->get($objects, $callback, $params);
            $subscribed_courses   = $this->memcache->get($objects, $callback, $params);
            // echo '<pre>';print_r($enrolled_courses);die;
            $subscribed_course_id = array();
            foreach($subscribed_courses as $subscribed_course)
            {
                array_push($subscribed_course_id, $subscribed_course['course_id']);

            }
            foreach($courses as $key => $course)
            {
                $item_type                          = isset($course['item_type'])?$course['item_type']:'';
                if($item_type == 'course')
                {
                    if(in_array($course['id'],$subscribed_course_id))
                    {
                        unset($courses[$key]);
                    }
                }
                
            }
            foreach ($enrolled_courses as $enrolled) 
            {
                $course_completion      = 0;
                $lecture_count          = 0;
                $enrolled_courses[$enrolled['course_id']] = $enrolled;
                //echo $enrolled['course_id'].'-'.$enrolled['cs_percentage'].'<br/>';
                $course_completion += $enrolled['cs_percentage'];
                    foreach($enrolled['lectures'] as $lecture)
                    {
                        $lecture_count++;
                    }
                
                $enrolled_courses[$enrolled['course_id']]['course_completion']      = round($course_completion);
            
            }
            //bundles
            $objects            = array();
            $objects['key']     = 'bundle_enrolled_' . $user['id'];
            $callback           = 'my_bundle_subscriptions';
            $params             = array('user_id' => $user['id']);
            $enrolled_bundles   = $this->memcache->get($objects, $callback, $params);
            // echo '<pre>';print_r($enrolled_bundles);die;
            foreach ($enrolled_bundles as $enrolled) {
                $enrolled_bundles[$enrolled['bundle_id']] = $enrolled;
            }
             //echo '<pre>';print_r($courses);die;
            foreach ($courses as $c_key => $course) {
                $item_type                          = isset($course['item_type'])?$course['item_type']:'';
                if($item_type == 'bundle')
                {
                    $courses[$c_key]['enrolled']        = isset($enrolled_bundles[$course['id']]);
                   
                    if($courses[$c_key]['enrolled'])
                    {
                        unset($courses[$c_key]);
                        
                        // $courses[$c_key]['bs_approved'] = $enrolled_bundles[$course['id']]['bs_approved'];
                    }else{
                        $courses[$c_key]['bundle_length']   = isset($course['c_courses'])?count(json_decode($course['c_courses'],true)):'0';
                    }
                   
                }
                else
                {
                    $courses[$c_key]['enrolled']        = isset($enrolled_courses[$course['id']]);
                    if($courses[$c_key]['enrolled'])
                    {
                        $courses[$c_key]['cs_end_date']                 = $enrolled_courses[$course['id']]['cs_end_date'];
                        $courses[$c_key]['cs_course_validity_status']   = $enrolled_courses[$course['id']]['cs_course_validity_status'];
                        $courses[$c_key]['cs_approved']                 = $enrolled_courses[$course['id']]['cs_approved'];
                        $courses[$c_key]['percentage']                  = $enrolled_courses[$course['id']]['course_completion'];
                        $courses[$c_key]['course_completion']           = $enrolled_courses[$course['id']]['course_completion'];
                        $courses[$c_key]['cs_last_played_lecture']      = $enrolled_courses[$course['id']]['cs_last_played_lecture'];
                        $today                                      = date('Y-m-d');
                        $expire                                     = date_diff(date_create($today),date_create($courses[$c_key]['cs_end_date'])); 
                        $now                                        = time(); // or your date as well
                        $your_date                                  = strtotime($courses[$c_key]['cs_end_date'] .' +1 day');
                        $datediff                                   = $your_date - $now;
                        $courses[$c_key]['expired']                 = ceil($datediff / (60 * 60 * 24)) > 0?false:true;
                        $courses[$c_key]['expire_in']               = $expire->format("%R%a");
                        $courses[$c_key]['expire_in_days']          = $expire->format("%a");
                        $courses[$c_key]['validity_format_date']    = date('d-m-Y',strtotime($courses[$c_key]['cs_end_date']));
                    }
                    
                }
            }
        } else {
            foreach ($courses as $key => $course) {
                $courses[$key]['enrolled'] = false;
                if($course['item_type'] == 'bundle'){
                    $courses[$key]['bundle_length']   = isset($course['c_courses'])?count(json_decode($course['c_courses'],true)):'0';
                }
            }
        }
        
        $courses                = array_values($courses);
        $data['course_list']    = $courses;
        $objects                = array();
        $objects['key']         = 'notifications';
        $callback               = 'notifications';
        $data['notifications']  = $this->memcache->get($objects, $callback);
        
        // echo "<pre>".print_r($data);die();
        $this->load->view($this->config->item('theme').'/course_list_beta', $data); 
    }
    
    
    function courses_json()
    {
        $this->load->model(array('Course_model'));
        $category_filters       = json_decode($this->input->post('category_filters'));
        $language_filters       = json_decode($this->input->post('language_filters'));
        $price_filters          = json_decode($this->input->post('price_filters'));
        $sort_order             = $this->input->post('sort_filters');
        $keyword                = $this->input->post('keyword');
        $offset_recieved        = $this->input->post('offset');
        $direction              = 'ASC';
        
        $session                = $this->auth->get_current_user_session('user');
        $response['session']    = $session;
        
        
        switch ($sort_order){
            case '1':
                $order_by = 'cb_position';
                break;
            case '2':
                $order_by = 'cb_price ASC, cb_discount';
                $direction = 'ASC';
                break;
            case '3':
                $order_by = 'cb_price DESC, cb_discount';
                $direction = 'DESC';
                break;
            case '4':
                $order_by = 'rating';
                $direction = 'DESC';
                break;
            default:
                $order_by = 'cb_position';
                break;
        }
        
        $response               = array();  
        $response['error']      = false;
        $param                  = array();
        $param['keyword']       = $keyword;
        
        //calculating page numbers
        $per_page           = 4;
        $page_num           = 1;
        $offset             = 0;
        if(  $offset_recieved && $offset_recieved != 1 )
        {
            $page_num     = $offset_recieved;
            $offset       = $offset_recieved * $per_page;
            $offset       = ($offset - $per_page);
        }
        $param['offset'] = $offset;
        $param['limit']  = $per_page;
        //end of calucalting page number
        
        $param['status']    = 1;
        $param['not_deleted']= 1;
        $param['order_by']  = $order_by;
        $param['direction'] = $direction;
        
        if(!empty($category_filters))
        {
            $param['category_ids'] = $category_filters;
        }
        if(!empty($language_filters))
        {
            $param['language_ids'] = $language_filters;
        }
        if(!empty($price_filters))
        {
            $param['price_ids'] = $price_filters;
        }
        
        //echo '<pre>'; print_r($param);die;
        $this->load->model(array('Development_model'));
        $response['course_list']   = $this->Development_model->courses($param); 
        
        $ratting                    = array();
        foreach ($response['course_list'] as $key => $course) {
            $response['course_list'][$key]['assigned_tutors'] = $this->Course_model->assigned_tutors(array('course_id'=>$course['id']));
            $response['course_list'][$key]['ratting'] = $this->Course_model->get_ratting(array('course_id' => $course['id'],'cc_status' => '1'));
            $temp_arr = array();
            array_push($temp_arr, 'rate_div_'.$key);
            array_push($temp_arr, $response['course_list'][$key]['ratting']);
            array_push($ratting, $temp_arr);
            

            $wish_stat = $this->Course_model->get_whish_stat($course['id'], $session['id']);

            if($wish_stat == 1){
                $response['course_list'][$key]['wish_stat'] = '<span class="heart-icon heart-active" data-key="'.$course['id'].'" onclick="remove_wishlist('.$course['id'].', \''.$session['id'].'\', this)"><i class="icon-heart heart-altr"></i></span>';
            }
            else if($wish_stat == 0){
                $response['course_list'][$key]['wish_stat'] = '<span class="heart-icon" data-key="'.$course['id'].'" onclick="add_wishlist('.$course['id'].', \''.$session['id'].'\', this)"><i class="icon-heart heart-altr"></i></span>';
            }
            else if($wish_stat == 2){
                $response['course_list'][$key]['wish_stat'] = '';
            }
        }
        //echo '<pre>';print_r($response['teachers']);;
        echo json_encode($response);
    }

    /* Function for removing courses from user wish list
    * By Yadu Chandran  */
    function change_password()
    {
        $this->load->model(array('User_model'));
        $session                = $this->auth->get_current_user_session('user');   
        $response               = array();
        $data                   = array();
        $response['error']      = false;
        $current_password       = $this->input->post('current_pass');
        $new_password           = $this->input->post('new_password');
        $confirm_password       = $this->input->post('confirm_pass');
        $check_password         = $this->User_model->get_user_password(array('user_id'  => $session['id']));
        if(sha1($current_password)==$check_password)
        {
            $response['error']      = true;
            $response['message']    = "success";
            $filter                 = array();
            $filter['id']           = $session['id'];
            $data['us_password']    = sha1($confirm_password);
            $data['user_pass']      = $this->User_model->update_password($data,$filter);
            echo json_encode($response);
        }
        else
        {
            $response['error']    = true;
            $response['message']  = "Old password is incorrect";
            echo json_encode($response);
        }
    }

    function generate_test_view_old($id = false){
        $data = array();
        $session  = $this->auth->get_current_user_session('user');
        $data['session']  = $session;
        if($id){
            $data['category_id'] =$id;
        }
        $this->load->view($this->config->item('theme').'/generate-test', $data);
    }
    
    function generate_test_view($category_id=false)
    {
        $this->load->model(array('Category_model'));
        if(!$category_id || !$this->auth->get_current_user_session('user'))
        {
            redirect('dashboard');
        }
        $category = $this->Category_model->category(array('id'=>$category_id, 'status'=>1));
        if(!$category)
        {
            redirect('dashboard');
        }
        $data                        = array();
        $data['category_id']         = $category_id;
        $data['title']               = $category['ct_name'];
        $data['assessment_medium']   = array( '1' => 'Easy Level', '2' => 'Moderate Level', '3' => 'Hard Level');
        $data['assessment_duration'] = array( '30 mins', '45 mins', '1hr', '1hr, 15mins', '1hr, 30mins', '1hr, 45mins', '2hr', '2hr, 15mins', '2hr, 30mins', '2hr, 45mins', '3hrs');
        $data['categories']          = $this->Category_model->get_categories_with_questions(array('category_id'=>$category_id));
        //echo '<pre>'; print_r($data['categories']);die;
        //header details
        $this->load->model(array('Page_model'));
        $data['category_pages']     = $this->Page_model->pages(array('category'=>$category_id, 'direction'=>'DESC', 'status'=>'1'));
        //End
        $this->load->view($this->config->item('theme').'/generate-test', $data);
    }
    
    function generate_assessment_proceed()
    {
        $this->load->model(array('Category_model'));
        $response               = array();
        $response['error']      = false;
        $response['message']    = 'User test generated successfully';
        $category_id        = $this->input->post('category_id');
        $user               = $this->auth->get_current_user_session('user');
        if(!$category_id || !$user)
        {
            $response['error']      = true;
            $response['message']    = 'Invalid category id or user';
            echo json_encode($response);die;
        }
        $category = $this->Category_model->category(array('id'=>$category_id, 'status'=>1));
        if(!$category)
        {
            $response['error']      = true;
            $response['message']    = 'Invalid category id';
            echo json_encode($response);die;
        }

        $this->init_assessment_variables();
        $this->load->model(array('Generate_test_model'));
        $category               = $this->Category_model->category(array('id'=>$category_id));
        $mode                   = $this->input->post('mode');
        $duration               = $this->input->post('duration');
        $duration               = intval($duration-1);
        $assessment_categories  = $this->input->post('assessment_category');
        $assessment_categories  = json_decode($assessment_categories);

        //procesising question count
        $duration       = isset($this->__assessment_duration[$duration])?$this->__assessment_duration[$duration]:60;//in minutes
        $question_limit = $this->__question_count_rules[$mode][$duration];
        //End

        
        $questions = $this->Generate_test_model->generate_questions(array('limit'=>$question_limit, 'category_ids' => $assessment_categories, 'mode' => $mode));
        if(!empty($questions))
        {
            //creating user assessment
            $save                   = array();
            $save['uga_title'] 	= "Generated test on ".$category['ct_name'];
            $save['uga_category'] 	= $category_id;
            $save['uga_user_id']    = $user['id'];
            $save['uga_duration']   = $duration;
            $save['uga_number_of_questions'] = $question_limit;
            $user_assessment_id     = $this->Generate_test_model->generate_assesment($save);
            //End
            foreach ($questions as $question)
            {
                $this->Generate_test_model->save_question($user_assessment_id, $question['id']);
            }
            $response['link'] = site_url().'/material/myexam/'.$user_assessment_id;
        }
        else 
        {
            $response['error']      = true;
            $response['message']    = 'Sorry. We can\'t fetch enough question for selected criteria. Please try with another criteria.';            
        }
        echo json_encode($response);die;
    }
    
    private function init_assessment_variables()
    {
        $this->__assessment_medium      = array( '1', '2', '3' );
        $this->__assessment_duration    = array( '30', '45', '60', '75', '90', '105', '120', '135', '150', '165', '180');
        $this->__question_count_rules   = array();
        
        
        /*foreach($this->__assessment_medium as $medium)
        {
            echo '//easy mode and number of questions<br />';
            foreach($this->__assessment_duration as $duration)
            {
                    echo '$'.'this'.'->__question_count_rules['.$medium.']['.$duration.'] = 120;<br />';
            }
        }*/
        
        //easy mode and number of questions
        $this->__question_count_rules[1][30] = 30;
        $this->__question_count_rules[1][45] = 45;
        $this->__question_count_rules[1][60] = 60;//don't delete this index. its default
        $this->__question_count_rules[1][75] = 75;
        $this->__question_count_rules[1][90] = 90;
        $this->__question_count_rules[1][105] = 105;
        $this->__question_count_rules[1][120] = 120;
        $this->__question_count_rules[1][135] = 135;
        $this->__question_count_rules[1][150] = 150;
        $this->__question_count_rules[1][165] = 165;
        $this->__question_count_rules[1][180] = 180;
        
        //medium mode and number of questions
        $this->__question_count_rules[2][30] = 15;
        $this->__question_count_rules[2][45] = 22;
        $this->__question_count_rules[2][60] = 30;//don't delete this index. its default
        $this->__question_count_rules[2][75] = 33;
        $this->__question_count_rules[2][90] = 45;
        $this->__question_count_rules[2][105] = 52;
        $this->__question_count_rules[2][120] = 60;
        $this->__question_count_rules[2][135] = 66;
        $this->__question_count_rules[2][150] = 75;
        $this->__question_count_rules[2][165] = 82;
        $this->__question_count_rules[2][180] = 90;
        
        //hard mode and number of questions
        $this->__question_count_rules[3][30] = 5;
        $this->__question_count_rules[3][45] = 9;
        $this->__question_count_rules[3][60] = 12;//don't delete this index. its default
        $this->__question_count_rules[3][75] = 15;
        $this->__question_count_rules[3][90] = 18;
        $this->__question_count_rules[3][105] = 21;
        $this->__question_count_rules[3][120] = 24;
        $this->__question_count_rules[3][135] = 27;
        $this->__question_count_rules[3][150] = 30;
        $this->__question_count_rules[3][165] = 33;
        $this->__question_count_rules[3][180] = 36;

    }

    public function topics($course = 0){
        
        $objects                = array();
        $response               = array();
        // $objects['key']         = 'course_'.$course;
        // $callback               = 'course_details';
        // $params                 = array('id' => $course);
        // $course_details         = $this->memcache->get($objects, $callback, $params); 
        

        $response['success']    = true;
        $objects['key']         = 'course_lectures_'.$course;
        $callback               = 'lecture_info';
        $params                 = array('id' => $course);
        $response['lectures']   = $this->memcache->get($objects, $callback, $params); 
        
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        // header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, x-api-key ');
        echo json_encode($response);
    }   
    
    function currentTime()
    {
        echo date('d-m-Y h:i:s a'); die;
    }
    
    function vimeo($vimeo_id = '')
    {
        if($vimeo_id != '' )
        {

            
            $meta = '<meta http-equiv="Content-Security-Policy" content="default-src * gap:; script-src * ';
            $meta .= "'unsafe-inline' 'unsafe-eval'; connect-src *; img-src * data: blob: android-webview-video-poster:; style-src * 'unsafe-inline';";
            $meta .= '">';

            echo $meta.'<body style="margin:0px;padding:0px;overflow:hidden;background:#000;">
                    <iframe src="https://player.vimeo.com/video/'.$vimeo_id.'" style="overflow:hidden;height:100%;width:100%" height="100%" width="100%" frameborder="0" allow="autoplay; fullscreen" allowfullscreen playsinline></iframe>
                </body>';
            
        }
        else
        {
            echo 'No Video Found!';
        }
    }

    function flowplayer()
    {
        
    }
}
<?php
class Report extends CI_Controller
{
    var $__user;
    function __construct()
    {
        parent::__construct();
        $redirect	= $this->auth->is_logged_in_user(false, false);
        if (!$redirect)
        {
            redirect('login');
        }
        $this->__user = $this->auth->get_current_user_session('user');
        $this->load->model(array('Course_model', 'User_model', 'Report_model'));
        $this->__redirect = 'dashboard';
    }
    
    function assessment($assessment_id=false)
    {
        if(!$assessment_id)
        {
            redirect($this->__redirect);
        }
        $assessment  = $this->Report_model->assesment(array('assessment_id' => $assessment_id));
        if(!$assessment)
        {
            redirect($this->__redirect);
        }
        $subscription = $this->User_model->subscription(array('user_id' => $this->__user['id'], 'course_id' => $assessment['a_course_id']));
        if(!$subscription)
        {
            $this->session->flashdata('error', 'Please subscribe to this course to view its content');
            redirect($this->__redirect);
        }
        $data                   = array();
        $data['assessment']     = array();
        $data['attempts']       = array();
        $attempt_ids            = array();
        $limit                  = 10;
        
        $data['assessment']             = $assessment;
        $data['assessment']['lecture']  = $this->Course_model->lecture(array('id' => $assessment['a_lecture_id']));
        $all_attempts                   = $this->Report_model->attempts(array('assessment_id' => $assessment['assesment_id']));
        $data['attempts']               = $this->Report_model->attempts(array('assessment_id' => $assessment['assesment_id'], 'limit' => $limit));
        
        $data['my_rank']            = 0;
        $data['my_mark']            = 0;
        $data['my_attempt_id']      = 0;
        $rank                       = 0;
        $data['total_attempt']      = sizeof($all_attempts);
        $data['my_attempt_date']    = '';
        if(!empty($all_attempts))
        {
            foreach ($all_attempts as $all_attempt)
            { 
                $rank++;
                if($all_attempt['aa_user_id'] == $this->__user['id'])
                {
                    $data['my_mark']            = $all_attempt['total_mark'];
                    $data['my_rank']            = $rank;
                    $data['my_attempt_id']      = $all_attempt['id'];
                    $data['my_attempt_date']    = date("M j Y", strtotime( $all_attempt['aa_attempted_date']));
                    break;
                }
            }

        }
        //echo '<pre>';print_r($all_attempts);die;
        //echo '<pre>'; print_r($data);die;
        $this->load->view($this->config->item('theme').'/assessment_report_with_rank.php', $data);
    }
    
    function assessment_json()
    {
        
        $assesment_id       = $this->input->post('assesment_id');
        $offset_recieved    = $this->input->post('offset');
        
        $response               = array();  
        $response['attempts']   = array();
        $response['error']      = false;
        $param                  = array();
        $param['assesment_id']  = $assesment_id;
        
        //calculating page numbers
        $per_page           = 20;
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

        $response['attempts'] = $this->Report_model->attempts($param);
        echo json_encode($response);
    }
    
    function assessment_compare($attempt_ids = false)
    {
        if(!$attempt_ids)
        {
            redirect($this->__redirect);
        }
        $attempt_ids = $this->is_json(base64_decode($attempt_ids));
        if(!$attempt_ids)
        {            
            redirect($this->__redirect);
        }
        
        $attempt_ids = (array)$attempt_ids;
        if(empty($attempt_ids))
        {
            redirect($this->__redirect);
        }
        
        $assessment_id = 0;
        $attempt_list  = array();
        $data          = array();
        $data['users']  = array();
        foreach($attempt_ids as $attempt_id)
        {
            $attempt_object     = $this->Report_model->attempt(array('attempt_id'=>$attempt_id));
            $assessment_id      = $attempt_object['aa_assessment_id'];
            $user               = $this->User_model->user(array('id' => $attempt_object['aa_user_id']));
            
            $user_object                = array();
            $user_object['id']          = $user['id'];
            $user_object['name']        = $user['us_name'];
            $user_object['attempt_id']  = $attempt_id;
            $user_object['image']       = $user['us_image'];
            $data['users'][$attempt_id] = $user_object;
        }
        if(!$assessment_id)
        {
            redirect($this->__redirect);
        }

        $data['assessment']     = array();
        $assessment  = $this->Report_model->assesment(array('assessment_id' => $assessment_id));
        if(!$assessment)
        {
            redirect($this->__redirect);
        }
        $data['assessment']             = $assessment;
        $data['assessment']['lecture']  = $this->Course_model->lecture(array('id' => $assessment['a_lecture_id']));

        $rank = 0;
        $all_attempts      = $this->Report_model->attempts(array('assessment_id' => $assessment_id));
        if(!empty($all_attempts))
        {
            foreach ($all_attempts as $all_attempt)
            { 
                $rank++;
                if(in_array($all_attempt['id'], $attempt_ids))
                {
                    $data['users'][$all_attempt['id']]['rank']       = $rank;
                    $data['users'][$all_attempt['id']]['mark']       = $all_attempt['total_mark'];
                    $data['users'][$all_attempt['id']]['duration']   = $all_attempt['aa_duration'];
                }
            }

        }
        
        //echo '<pre>';
        $data['categories'] = array();
        $categories         = $this->Report_model->categories(array('assessment_id' => $assessment_id));
        //print_r($categories);
        
        $category_ids       = array();
        if(!empty($categories))
        {
            foreach($categories as $category)
            {
                $category_ids[] = $category['q_category'];
            }
        }
        //print_r($category_ids);
        
        $category_marks      = array();
        $category_marks_temp = $this->Report_model->category_marks(array('assessment_id' => $assessment_id, 'category_ids' => $category_ids));
        if(!empty($category_marks_temp))
        {
            foreach($category_marks_temp as $category_mark)
            {
                $category_marks[$category_mark['q_category']] = $category_mark;
            }
        }
        //print_r($category_marks);

        if(!empty($categories))
        {
            foreach($categories as $category)
            {
                $data['categories'][$category['q_category']] = array();
                $data['categories'][$category['q_category']]['name'] = $category['qc_category_name'];
                $data['categories'][$category['q_category']]['percentage'] = array();
                
                $user_category_marks_temp     = $this->Report_model->user_category_marks(array('assessment_id' => $assessment_id, 'category_id' => $category['q_category'], 'attempt_ids' => $attempt_ids));
                $user_category_marks = array();
                foreach($user_category_marks_temp as $user_category_mark_temp)
                {
                    $user_category_marks[$user_category_mark_temp['ar_attempt_id']] = $user_category_mark_temp;
                }
                
                
                foreach($attempt_ids as $attempt_id)
                {
                    $total_mark = 0;
                    if(isset($user_category_marks[$attempt_id]) && in_array($attempt_id, $user_category_marks[$attempt_id]))
                    {
                        if($category_marks[$category['q_category']]['total_marks'] > 0){
                            $total_mark = $user_category_marks[$attempt_id]['scored_marks']/$category_marks[$category['q_category']]['total_marks'];    
                        }else{
                            $total_mark = $user_category_marks[$attempt_id]['scored_marks'];
                        }
                        // $total_mark = $user_category_marks[$attempt_id]['scored_marks']/$category_marks[$category['q_category']]['total_marks'];
                        $total_mark = ($total_mark<0)?0:$total_mark;
                    }
                    $data['categories'][$category['q_category']]['percentage'][$attempt_id] = $total_mark;
                }
            }
        }
        //echo '<pre>'; print_r($data);die;
        $this->load->view($this->config->item('theme').'/assessment_report_with_compare.php', $data);
    }
    
    //for challenge zone
    function challenge_zone($challenge_zone_id=false)
    {
        if(!$challenge_zone_id)
        {
            redirect($this->__redirect);
        }
        $challenge_zone  = $this->Report_model->challenge_zone(array('challenge_zone_id' => $challenge_zone_id));
        if(!$challenge_zone)
        {
            redirect($this->__redirect);
        }

        $data                           = array();
        $data['challenge_zone_report']  = array();
        $data['attempts']               = array();
        $attempt_ids                    = array();
        $limit                          = 10;
        
        $data['challenge_zone_report']  = $challenge_zone;
        $all_attempts                   = $this->Report_model->challenge_attempts(array('challenge_zone_id' => $challenge_zone_id));
        $data['attempts']               = $this->Report_model->challenge_attempts(array('challenge_zone_id' => $challenge_zone_id, 'limit' => $limit));
        
        $data['my_rank']            = 0;
        $data['my_mark']            = 0;
        $data['my_attempt_id']      = 0;
        $rank                       = 0;
        $data['total_attempt']      = sizeof($all_attempts);
        $data['my_attempt_date']    = '';
        if(!empty($all_attempts))
        {
            foreach ($all_attempts as $all_attempt)
            { 
                $rank++;
                if($all_attempt['cza_user_id'] == $this->__user['id'])
                {
                    $data['my_mark']            = $all_attempt['total_mark'];
                    $data['my_rank']            = $rank;
                    $data['my_attempt_id']      = $all_attempt['id'];
                    $data['my_attempt_date']    = date("M j Y", strtotime( $all_attempt['cza_attempted_date']));
                    break;
                }
            }

        }

        //echo '<pre>'; print_r($data);die;
        $this->load->view($this->config->item('theme').'/challenze_zone_report_with_rank.php', $data);
    }
    
    function challenge_zone_json()
    {
        
        $challenge_zone_id  = $this->input->post('challenge_zone_id');
        $offset_recieved    = $this->input->post('offset');
        
        $response                   = array();  
        $response['attempts']       = array();
        $response['error']          = false;
        $param                      = array();
        $param['challenge_zone_id'] = $challenge_zone_id;
        
        //calculating page numbers
        $per_page           = 20;
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

        $response['attempts'] = $this->Report_model->challenge_attempts($param);
        echo json_encode($response);
    }
    
    function challenge_zone_compare($attempt_ids = false)
    {
        if(!$attempt_ids)
        {
            redirect($this->__redirect);
        }
        $attempt_ids = $this->is_json(base64_decode($attempt_ids));
        if(!$attempt_ids)
        {            
            redirect($this->__redirect);
        }
        
        $attempt_ids = (array)$attempt_ids;
        if(empty($attempt_ids))
        {
            redirect($this->__redirect);
        }
        
        $challenge_zone_id  = 0;
        $attempt_list       = array();
        $data               = array();
        $data['users']      = array();
        foreach($attempt_ids as $attempt_id)
        {
            $attempt_object     = $this->Report_model->challenge_attempt(array('attempt_id'=>$attempt_id));
            $challenge_zone_id  = $attempt_object['cza_challenge_zone_id'];
            $user               = $this->User_model->user(array('id' => $attempt_object['cza_user_id']));
            
            $user_object                = array();
            $user_object['id']          = $user['id'];
            $user_object['name']        = $user['us_name'];
            $user_object['attempt_id']  = $attempt_id;
            $user_object['image']       = $user['us_image'];
            $data['users'][$attempt_id] = $user_object;
        }
        if(!$challenge_zone_id)
        {
            redirect($this->__redirect);
        }

        $data['challenge_zone_report'] = array();
        $challenge_zone                = $this->Report_model->challenge_zone(array('challenge_zone_id' => $challenge_zone_id));
        if(!$challenge_zone)
        {
            redirect($this->__redirect);
        }
        $data['challenge_zone_report'] = $challenge_zone;

        $rank = 0;
        $all_attempts      = $this->Report_model->challenge_attempts(array('challenge_zone_id' => $challenge_zone_id));
        if(!empty($all_attempts))
        {
            foreach ($all_attempts as $all_attempt)
            { 
                $rank++;
                if(in_array($all_attempt['id'], $attempt_ids))
                {
                    $data['users'][$all_attempt['id']]['rank']       = $rank;
                    $data['users'][$all_attempt['id']]['mark']       = $all_attempt['total_mark'];
                    $data['users'][$all_attempt['id']]['duration']   = $all_attempt['cza_duration'];
                }
            }

        }
        //echo '<pre>';
        $data['categories'] = array();
        $categories         = $this->Report_model->challenge_zone_categories(array('challenge_zone_id' => $challenge_zone_id));
        //print_r($categories);
        
        $category_ids       = array();
        if(!empty($categories))
        {
            foreach($categories as $category)
            {
                $category_ids[] = $category['q_category'];
            }
        }
        //print_r($category_ids);
        
        $category_marks      = array();
        $category_marks_temp = $this->Report_model->challenge_zone_category_marks(array('challenge_zone_id' => $challenge_zone_id, 'category_ids' => $category_ids));
        if(!empty($category_marks_temp))
        {
            foreach($category_marks_temp as $category_mark)
            {
                $category_marks[$category_mark['q_category']] = $category_mark;
            }
        }
        //print_r($category_marks);

        if(!empty($categories))
        {
            foreach($categories as $category)
            {
                $data['categories'][$category['q_category']] = array();
                $data['categories'][$category['q_category']]['name'] = $category['qc_category_name'];
                $data['categories'][$category['q_category']]['percentage'] = array();
                
                $user_category_marks_temp     = $this->Report_model->challenge_zone_user_category_marks(array('challenge_zone_id' => $challenge_zone_id, 'category_id' => $category['q_category'], 'attempt_ids' => $attempt_ids));
                $user_category_marks = array();
                foreach($user_category_marks_temp as $user_category_mark_temp)
                {
                    $user_category_marks[$user_category_mark_temp['czr_attempt_id']] = $user_category_mark_temp;
                }
                
                
                foreach($attempt_ids as $attempt_id)
                {
                    $total_mark = 0;
                    if(isset($user_category_marks[$attempt_id]) && in_array($attempt_id, $user_category_marks[$attempt_id]))
                    {
                        $total_mark = $user_category_marks[$attempt_id]['scored_marks']/$category_marks[$category['q_category']]['total_marks'];
                        $total_mark = ($total_mark<0)?0:$total_mark;
                    }
                    $data['categories'][$category['q_category']]['percentage'][$attempt_id] = $total_mark;
                }
            }
            //print_r($data['categories']);
        }
        //echo '<pre>'; print_r($data);die;
        $this->load->view($this->config->item('theme').'/challenge_zone_report_with_compare.php', $data);
    }
    //end
    
    private function is_json($json_string) 
    {
        $json_data = json_decode($json_string);
        return (json_last_error() == JSON_ERROR_NONE) ? $json_data : FALSE;
    }
}
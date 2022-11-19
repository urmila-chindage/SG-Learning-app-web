<?php

class Generate_test extends CI_Controller
{
    private $__builder_view;
    public $__lecture_type_icons;
    public $__document_types = array('doc', 'docx', 'xls', 'pdf', 'ppt', 'pptx');
    function __construct()
    {
        parent::__construct();
        $skip_login                 = array('launch_conversion', 'dummy_conversion', 'process_conversion_queue');
        $this->__loggedInUser   = $this->auth->get_current_user_session('admin');
        $redirect   = $this->auth->is_logged_in(false, false);
        if (!$redirect && !in_array($this->router->fetch_method(), $skip_login))
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
            if($redirect && !in_array($this->router->fetch_method(), $skip_login))
            {
                redirect('login');
            }
        }        

        $this->load->model(array('Course_model', 'User_model', 'Category_model', 'Generate_test_model'));
        $this->lang->load('generate_test');
        $this->actions              = $this->config->item('actions');

      

        $this->__question_types   = array('single' => '1', 'multiple' => '2', 'subjective' => '3', 'fill_in_the_blanks' => '4');
        $this->__difficulty       = array('easy' => '1', 'medium' => '2', 'hard' => '3');
        $this->__single_type         = '1';
        $this->__multi_type          = '2';
        $this->__subjective_type     = '3';
        $this->__fill_in_the_blanks  = '4';
        
        $this->__limit            = 100;
        //echo '<pre>';print_r($this->actions);die;
        $this->__permission     = $this->accesspermission->get_permission(
                                                        array(
                                                            'role_id' => $this->__loggedInUser['role_id'],
                                                            'module' => 'question'
                                                            ));
                                                            // echo "<pre>"; print_r($this->__permission); die();
    }
    
    function index()
    { 
        if(in_array('1', $this->__permission)) //mem
        {
            $data                       = array();
            $breadcrumb                 = array();
            $breadcrumb[]               = array( 'label' => 'Home', 'link' => site_url('/admin'), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
            $breadcrumb[]               = array( 'label' => lang('question_bank'), 'link' => '', 'active' => 'active', 'icon' => '' );
            $data['breadcrumb']         = $breadcrumb;
            $data['title']              = lang('generate_test');
            $data['show_load_button']   = false;
            
            $data['lecture_id']         = isset($this->__import_lecture_id)?$this->__import_lecture_id:0;
            $data['assessment_id']      = isset($this->__import_assessment_id)?$this->__import_assessment_id:0;
            
            $q_param                    = array();
            $q_param['filter']          = ($this->input->get('filter') != null)? $this->input->get('filter') : '';
            $q_param['type']            = ($this->input->get('type') != null)? $this->input->get('type') : '';
            $q_param['category_id']     = ($this->input->get('category') != null)? $this->input->get('category') : '';
            $q_param['subject_id']      = ($this->input->get('subject') != null)? $this->input->get('subject') : '';
            $q_param['topic_id']        = ($this->input->get('topic') != null)? $this->input->get('topic') : '';
            $q_param['keyword']         = ($this->input->get('keyword') != null)? $this->input->get('keyword') : '';
            $q_param['direction']       = 'DESC';
            $q_param['not_deleted']     = '1';
            $q_param['limit']           = $this->__limit;
            $q_param['count']           = true;
            //$data['questions']  = $this->Generate_test_model->questions(array('direction'=>'DESC', 'keyword'=>  $this->input->post('keyword'), 'category_id' => $this->input->post('category_id'), 'topic_id' => $this->input->post('topic_id'), 'subject_id' => $this->input->post('subject_id'), 'filter'=>  $this->input->post('filter'), 'type'=>  $this->input->post('type'), 'not_deleted'=>  '1', 'limit' => $this->__limit, 'offset' => $page)); 
            $category_id                = '';
            $subject_id                 = '';
            if(($q_param['category_id']!='all') || ($q_param['category_id']!='')){
                $category_id            = $q_param['category_id'];
            } 
            if(($q_param['subject_id']!='all') || ($q_param['subject_id']!='')){
                $subject_id             = $q_param['subject_id'];
            }
            $data['topics']             = $this->Category_model->topics(array('select'=>'questions_topic.id,questions_topic.qt_topic_name','category_id' => $category_id, 'subject_id' => $subject_id,'qt_deleted'=>true));
            
            if(($q_param['category_id']!='all') && ($q_param['category_id']!='')){
                $data['subjects']       = $this->Category_model->subjects(array('select' =>'questions_subject.id,questions_subject.qs_subject_name','category_id' => $category_id,'qs_deleted' => true));
            }
            if($q_param['category_id']!='all'){
                $data['subjects']       = array(); 
            }
            $offset                     = 0;
            $_SESSION['question_param']= $q_param;
            $total_questions            = $this->Generate_test_model->questions($q_param);
            $data['total_questions']    = $total_questions;            
            if($total_questions > $this->__limit)
            {
                $data['show_load_button'] = true;            
            }
            unset($q_param['count']);
            $data['questions']          = $this->Generate_test_model->questions($q_param);
            $main_category              = $this->Category_model->categories(array(/*'status' => '1', */'not_deleted'=>true));
            $data['q_parent_category']  = $main_category;
            $data['question_topics']    = $this->Category_model->question_categories(array('status' => '1'));
            //echo '<pre>'; print_r($data);die;
            $active_web_language        = '1';
            $this->session->set_userdata('active_web_language', $active_web_language);
            $data['rl_full_course'] = false; 
            if ($this->__loggedInUser['rl_full_course'] != 1) {
                $data['rl_full_course'] = true; 
            }
            $this->load->view($this->config->item('admin_folder').'/generate_test', $data);
        }
        else
        {
            redirect(admin_url());
        }
    }
    
    
    function import($lecture_id = 0)
    {
        $lecture      = $this->Course_model->lecture(array('direction'=>'DESC', 'id'=>  $lecture_id));
        if( !$lecture )
        {
            redirect(admin_url('generate_test'));
        }
        $lecture['assesment']           = $this->Course_model->assesment(array('lecture_id' => $lecture['id'], 'course_id' => $lecture['cl_course_id']));
        $this->__import_assessment_id   = $lecture['assesment']['assesment_id'];
        $this->__import_lecture_id      = $lecture_id;
        $this->index();
    }

    function import_questions($lecture_id = 0)
    {
        $lecture                        = $this->Course_model->lecture(array('direction'=>'DESC', 'id'=>  $lecture_id));
        if( !$lecture )
        {
            redirect(admin_url('generate_test'));
        }
        
        $lecture['assesment']           = $this->Course_model->assesment(array('lecture_id' => $lecture['id'], 'course_id' => $lecture['cl_course_id']));
        $this->__import_assessment_id   = $lecture['assesment']['assesment_id'];
        $this->__import_lecture_id      = $lecture_id;
        $course_id                      = $lecture['cl_course_id'];

        $this->__quiz_permission        = $this->accesspermission->get_permission_course(
                                                        array(
                                                            'role_id' => $this->__loggedInUser['role_id'],
                                                            'module' => 'quiz_manager'
                                                            ,'user_id' => $this->__loggedInUser['id'],'course_id' => $course_id));
        //print_r($this->__quiz_permission);die;
        if(in_array('3', $this->__quiz_permission) || $this->__loggedInUser['role_id'] == '8')
        { 
            $data                       = array();
            $data['title']              = lang('generate_test');
            $data['show_load_button']   = false;
            
            $data['lecture_id']         = isset($this->__import_lecture_id)?$this->__import_lecture_id:0;
            $data['assessment_id']      = isset($this->__import_assessment_id)?$this->__import_assessment_id:0;
            
            $offset                     = 0;
            $total_questions            = $this->Generate_test_model->questions(array('not_deleted' => '1', 'count' => true ));
            $data['total_questions']    = $total_questions;            
            if($total_questions > $this->__limit)
            {
                $data['show_load_button']   = true;            
            }
            $data['questions']          = $this->Generate_test_model->questions(array('direction'=>'DESC', 'not_deleted' => '1', 'limit' => $this->__limit, 'offset' =>$offset ));
            $main_category              = $this->Category_model->categories(array('status' => '1', 'not_deleted'=>true));
            $data['q_parent_category']  = $main_category;
            $data['question_topics']    = $this->Category_model->question_categories(array('status' => '1'));
            //echo '<pre>'; print_r($data);die;
            $active_web_language        = '1';
            $this->session->set_userdata('active_web_language', $active_web_language);
            $this->load->view($this->config->item('admin_folder').'/import_question_bank',$data);
        }
        else
        {
            redirect(admin_url());
        }
    }
    
    private function deactivate_lecture($lecture_id){
        //Deact lecture
        $this->load->model('Test_model');
        $testdetails               = $this->Test_model->test_details(array('test_id'=>$lecture_id,'select'=>'course_lectures.id,course_lectures.cl_status,assessments.a_instructions'));
        $status_param              = array();
        $status_param['id']        = $testdetails['id'];
        $status_param['cl_status'] = '0';
        $status_param['action_id'] = '4';
        $status_param['action_by'] = $this->auth->get_current_admin('id');
        $this->Course_model->save_lecture($status_param);
        //End deact lecture
    }

    function import_question()
    {
        $this->load->model('Test_model');
        $response               = array();
        $response['error']      = false;
        $response['message']    = 'Question imported successfully';
        $assessment_id          = $this->input->post('assessment_id');
        $lecture_id             = $this->input->post('lectureId');
        $question_ids           = json_decode($this->input->post('question_ids'), true);
        $questions              = $this->Test_model->test_questions(array('assessment_id' => $assessment_id, 'select' => 'aq_question_id'));
    
        foreach($questions as $current_qs){
            if(in_array($current_qs['aq_question_id'], $question_ids)){
                unset($question_ids[$current_qs['aq_question_id']]);
            }
        }
        
        if(!empty($question_ids))
        {
            $question_count                 = 0;
            $assessment_questions           = array();
            foreach($question_ids as $question_id)
            {
                $question                   = $this->Generate_test_model->question(array('id'=> $question_id));
                $save                       = array();
                $save['aq_assesment_id']    = $assessment_id;
                $save['aq_question_id']     = $question_id;
                $save['aq_positive_mark']   = $question['q_positive_mark'];
                $save['aq_negative_mark']   = ($question['q_negative_mark'] < 0 ) ? $question['q_negative_mark'] : -$question['q_negative_mark'];
                $save['aq_status']          = '1';
                $assessment_questions[]     = $save;
                $question_count++;
                
            }
            $import_assessment_questions    = $this->Course_model->save_assesment_questions_bulk($assessment_questions);
            
            $assessment_details             = $this->Test_model->test_questions(array('select'=>'SUM(aq_positive_mark) as total_mark,count(*) as total_questions','assessment_id'=>$assessment_id));
            $total_mark                     = $assessment_details[0]['total_mark'];
            $total_questions                = $assessment_details[0]['total_questions'];
            $save                           = array();
            $save['a_lecture_id']           = $lecture_id;
            $save['a_questions']            = $total_questions;
            $save['a_mark']                 = $total_mark;
            $this->Test_model->update_assesment($save);
            
            /*Deactivate lecture*/
            $this->deactivate_lecture($lecture_id);
            /*End deactivate lecture*/

            $lecture                    = $this->Course_model->lecture(array('id'=>$lecture_id,'select'=>'course_lectures.cl_course_id as course_id'));
            $course_id                  = (isset($lecture['course_id']))?$lecture['course_id']:false;
            if($course_id)
            {
               $this->invalidate_course(array('course_id' => $course_id));
            }
            
            //$question_count = count($imported_ids);
            if($question_count>0){
                $question_html  = $question_count.' '.(($question_count>1)?'Questions':'Question').' imported successfully';
            } else {
                $question_html  = 'Same questions are already added in this quiz.</br> Please try again the import!';
            }
            $this->session->set_flashdata('message', $question_html);
        }
        echo json_encode($response);
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
    
    function generate_questions_json()
    {
        $data                       = array();
        $data['show_load_button']   = false;            
        $limit                      = $this->__limit;
        $offset                     = $this->input->post('offset');
        $page                       = $offset;
        if($page===NULL||$page<=0)
        {
            $page                   = 1;
        }
        $page                       = ($page - 1)* $limit;

        $total_questions            = $this->Generate_test_model->questions(array('keyword'=>  $this->input->post('keyword'), 'category_id' => $this->input->post('category_id'), 'topic_id' => $this->input->post('topic_id'), 'filter'=>  $this->input->post('filter'), 'subject_id' => $this->input->post('subject_id'), 'type'=>  $this->input->post('type'),  'not_deleted'=>  '1', 'count' => true));        
        $data['total_questions']    = $total_questions;       
        $data['limit']              = $this->__limit;
        if($total_questions > ($this->__limit*$offset))
        {
            $data['show_load_button']   = true;            
        }
      
        $q_param                        = array(    'direction'         => 'DESC', 
                                                    'keyword'           => $this->input->post('keyword'), 
                                                    'category_id'       => $this->input->post('category_id'), 
                                                    'topic_id'          => $this->input->post('topic_id'), 
                                                    'subject_id'        => $this->input->post('subject_id'), 
                                                    'filter'            => $this->input->post('filter'), 
                                                    'type'              => $this->input->post('type'), 
                                                    'not_deleted'       => '1', 
                                                    'limit'             => $this->__limit, 
                                                    'offset'            => $page
                                                );
        $_SESSION['question_param']    = $q_param;
        $data['questions']              = $this->Generate_test_model->questions($q_param);    
//processing web language
        $active_web_language = $this->session->userdata('active_web_language');
        if(!$active_web_language)
        {
            $active_web_language = '1';
            $this->session->set_userdata('active_web_language', $active_web_language);
        }
        $data['active_web_language'] = $active_web_language;
        
        //==============End of processing web language===============
        echo json_encode($data);
    }
    
    function upload_to_home_server()
    {
        $response                   = array();
        $response['file_object']    = array();
        $response['error']          = 'false';
        $response['message']        = lang('file_uploaded_success').' '.  lang('file_conversion_stated');
        $this->load->library('upload');
        $config                     = $this->get_upload_config($this->input->post('extension'));
        if( $config )
        {
            $this->upload->initialize($config);
            $uploaded = $this->upload->do_upload('file');   
            
            if( $uploaded )
            {
                $upload_data                = $this->upload->data();
                $response['file_object']    = $upload_data;
            }
            else
            {
                $response['error']      = 'true';
                $response['message']    = lang('error_uploading_file');                
            }
        }
        else
        {
            $response['error']      = 'true';
            $response['message']    = lang('error_uploading_file');
        }
        //echo '<pre>';print_r($data);die;
        echo json_encode($response);
    }
    
    function launch_conversion()
    {
        $this->load->library('ofabeeconverter'); 
        $config = $this->input->post();
        $myfile = fopen("uploads/dummy.txt", "w");
        $txt = json_encode($config);
        fwrite($myfile, $txt);
        fclose($myfile);
        $this->ofabeeconverter->initialize_config($config);
        $conversion = $this->ofabeeconverter->convert();     
        
        $lecture_id            = $config['lecture_id'];
        $lecture               = $this->Course_model->lecture(array('id'=>$lecture_id));
        $course                = $this->Course_model->course(array('id'=>$lecture['cl_course_id']));
        $param                 = array();
        $param['from']         = $this->config->item('site_email');
        $param['to']           = array($this->config->item('site_email'));
        if(isset($conversion['success']) && $conversion['success']==true)
        {
            $param['subject']      = "File conversion completed successfully";
            $param['body']         = "Hi Admin, <br/>A lecture named <b>".$lecture['cl_lecture_name']."</b> under the course <b>".$course['cb_title']."</b> has been successfully converted. If you are logged in please click <a href='".admin_url('coursebuilder/lecture/'.$lecture_id)."'>here</a>";
        }
        else
        {
            $param['subject']      = "File conversion error";
            $param['body']         = "Hi Admin, <br/>There is an error in converting a lecture named <b>".$lecture['cl_lecture_name']."</b> under the course <b>".$course['cb_title']."</b>. Error is as follows<br />".$conversion['message'];
        }
        $send = $this->ofabeemailer->send_mail($param);            
    }
    
    private function upload_file_to_local_server($lecture_id)
    {
        $response               = array();
        $response['error']      = 'false';
        $response['message']    = lang('file_uploaded_success').' '.  lang('file_conversion_stated');
        $this->load->library('upload');
        $config     = $this->get_upload_config($this->input->post('extension'));
        if( $config )
        {
            $this->upload->initialize($config);
            $uploaded = $this->upload->do_upload('file');   
            
            //upload log
            /*$myfile = fopen("uploads/upload.txt", "w");
            $txt = json_encode($this->upload->display_errors());
            fwrite($myfile, $txt);
            fclose($myfile);*/
            //end of log

            if( $uploaded )
            {
                $upload_data      =  $this->upload->data();
                //converstion starts
                $this->load->library('ofabeeconverter');
                $config                 = array();
                $config['input']        = $upload_data['full_path']; //video_upload_path().$upload_data['file_name']; 
                //$config['output']       = $this->config->item('upload_folder').'/videos/'.$this->config->item('acct_domain').'/'; 
                $config['s3_upload']    = false;
                $config['lecture_id']   = $lecture_id;
                $config['target_url']   = admin_url('coursebuilder/launch_conversion');
                $this->ofabeeconverter->initialize($config);
                //$this->ofabeeconverter->convert();
                //end
                $save                  = array();
                $save['id']            = $lecture_id;
                $save['cl_filename']   = $upload_data['raw_name']."?v=".rand(10,1000);
                $save['cl_org_file_name']= $upload_data['file_name']."?v=".rand(10,1000);
                $this->Course_model->save_lecture($save);
            }
            else
            {
                $response['error']      = 'true';
                $response['message']    = lang('error_uploading_file');                
            }
        }
        else
        {
            $response['error']      = 'true';
            $response['message']    = lang('error_uploading_file');
        }
        //echo '<pre>';print_r($data);die;
        //echo json_encode($response);
        return $response;
    }
    
    private function import_assesment($param)
    {
        $old_lecture                    = $param['old_lecture'];
        $new_section_id                 = $param['section_id'];
        $new_course_id                  = $param['course_id'];
        $sent_mail_on_import_creation   = $param['sent_mail'];
        
        /*
         * creating new lecture using old lecture details
         * inserting data into table 'course_lectures"
         */
        $save                   = $old_lecture;
        $save['id']             = false;
        $save['action_by']      = $this->auth->get_current_admin('id');
        $save['action_id']      = $this->actions['create'];            
        $save['cl_section_id']  = $new_section_id;
        $save['cl_course_id']   = $new_course_id;
        $save['cl_sent_mail_on_lecture_creation'] = $sent_mail_on_import_creation;
        $new_lecture_id         = $this->Course_model->save_lecture($save);
        //end
        
        /*
         * Inserting the data into the table "assessments"
         */
        $old_assesment          = $this->Course_model->assesment(array('lecture_id' => $old_lecture['id'], 'course_id' => $old_lecture['cl_course_id']));
        $old_assesment_id       = $old_assesment['assesment_id'];
        $save                   = array();
        $save['id']             = false;
        $save['a_course_id']    = $new_course_id;
        $save['a_lecture_id']   = $new_lecture_id;
        $save['a_instructions'] = $old_assesment['a_instructions'];
        $save['a_duration']     = $old_assesment['a_duration'];
        $save['action_by']      = $this->auth->get_current_admin('id');
        $save['action_id']      = $this->actions['create'];
        $new_assesment_id       = $this->Course_model->save_assesment($save);
        //end
        
        /*
         * fetch the questions related to old_assesment_id from table "assessment_questions"
         * and copy the questions to the tabel "questions". Insert the newsly created question id 
         * in the table "assessment_questions" with the new assesment id
         */
        $old_questions = $this->Course_model->questions(array('assesment_id'=>$old_assesment_id));
        if(!empty($old_questions))
        {
            foreach ($old_questions as $question) {
                $save                   = $question;
                $save['id']             = false;
                $save['q_course_id']    = $new_course_id;
                $save['action_by']      = $this->auth->get_current_admin('id');
                $save['action_id']      = $this->actions['create'];
                
                /*
                 * fetch the corresponding options
                 * and answer from old question and create it as a new one
                 */
                $options         = $this->Course_model->options(array('q_options' => $question['q_options']));
                $recieved_answer = $question['q_answer'];
                $q_options       = array();
                $q_answer        = array();

                if($question['q_type']== $this->__single_type)
                {
                    if( !empty($options))
                    {
                        foreach ($options as $option ) {
                            $save_option                = array();
                            $save_option['id']          = false;
                            $save_option['qo_options']  = $option['qo_options'];
                            $new_option_id              = $this->Course_model->save_option($save_option);
                            $q_options[]                = $new_option_id;
                            if($option['id'] == $recieved_answer)
                            {
                                $q_answer[] = $new_option_id;
                            }
                        }
                    }
                }
                else
                {
                    $recieved_answer = explode(',', $recieved_answer);
                    if( !empty($options))
                    {
                        foreach ($options as $option ) {
                            $save_option                = array();
                            $save_option['id']          = false;
                            $save_option['qo_options']  = $option['qo_options'];
                            $new_option_id              = $this->Course_model->save_option($save_option);
                            $q_options[]                = $new_option_id;
                            if(in_array($option['id'], $recieved_answer))
                            {
                                $q_answer[] = $new_option_id;
                            }
                        }
                    }
                }
                //End
                
                
                $save['q_options']      = implode(',', $q_options);
                $save['q_answer']       = implode(',', $q_answer);
                $new_question_id        = $this->Course_model->save_question($save);
                
                $save                       = array();
                $save['assesment_id']       = $new_assesment_id;
                $save['question_id']        = $new_question_id;
                $save['positive_mark']      = $question['q_positive_mark'];
                $save['negative_mark']      = $question['q_negative_mark'];
                $this->Course_model->save_assesment_question($save);

                /*Deactivate lecture*/
                $this->deactivate_lecture($new_lecture_id);
                /*End deactivate lecture*/
            }
        }
        //end
        //echo '<pre>'; print_r($old_lecture);
        return true;
    }

    function assesment_validation($lecture)
    {
        $response            = array();
        $response['error']   = 'false';
        $response['message'] = '';
        
        $assesment           = $this->Course_model->assesment(array('lecture_id' => $lecture['id'], 'course_id' => $lecture['cl_course_id']));
        $questions           = $this->Course_model->questions(array('assesment_id' => $assesment['assesment_id']));
        if(strip_tags($assesment['a_instructions']) == '')
        {
            $response['error']   = 'true';
            $response['message'] .= lang('instructions_missing_on_activating').'<br />';    
        }
        if(sizeof($questions) == 0)
        {
            $response['error']   = 'true';
            $response['message'] .= lang('question_missing_on_activating').'<br />';    
        }
        if($response['error'] == 'true')
        {              
            echo json_encode($response);exit;
        }
    }

    function save_assesment()
    {
        $response           = array();
        $response['error']  = 'false';
        $response['message']= '';
        $section_id         = $this->input->post('section_id');
        $section_name       = $this->input->post('section_name');
        $course_id          = $this->input->post('course_id');
        
        $save               = array();
        $save['id']         = $section_id;
        $save['s_name']     = $section_name;
        $save['s_course_id']= $course_id;
        
        if( !$section_id )
        {
            if( $save['s_name'] == '' )
            {
                $response['error']   = 'true';
                $response['message'].= lang('section_name_required');
            }
            else
            {
                if( $this->Course_model->section(array('filter_id'=>$save['id'], 'name'=>$save['s_name'])) > 0 )
                {
                    $response['error']   = 'true';
                    $response['message'].= lang('section_name_not_available');
                }
            }
        }
        
        if( $response['error'] == 'true' )
        {
            echo json_encode($response);exit;            
        }
        
        if( !$section_id )
        {
            $total              = $this->Course_model->sections(array('count'=>true, 'course_id'=> $save['s_course_id']));
            $save['s_order_no'] = ($total)+1;
            $save['action_by']  = $this->auth->get_current_admin('id');
            $save['action_id']  = $this->actions['create'];
            $section_id         = $this->Course_model->save_section($save);
        }
        
        $save                                       = array();
        $save['id']                                 = false;
        $save['cl_lecture_name']                    = $this->input->post('assesment_name');
        $save['cl_lecture_description']             = $this->input->post('assesment_description');
        $save['cl_course_id']                       = $course_id;
        $save['cl_section_id']                      = $section_id;
        $highest_order                              = $this->Course_model->lectures(array('count'=>true, 'section_id'=>$section_id, 'course_id'=> $course_id));
        $save['cl_order_no']                        = $highest_order+1;
        $save['cl_lecture_type']                    = $this->__lecture_type_keys_array['assesment'];
        $save['cl_sent_mail_on_lecture_creation']   = $this->input->post('sent_mail_on_assesment_creation');
        $save['action_by']                          = $this->auth->get_current_admin('id');
        $save['action_id']                          = $this->actions['create'];
        $lecture_id                                 = $this->Course_model->save_lecture($save);
        
        $save                        = array();
        $save['id']                  = false;
        $save['a_course_id']         = $course_id;
        $save['a_lecture_id']        = $lecture_id;
        $active_lang                 = isset($_SESSION['active_lang'])?$_SESSION['active_lang']:1;
        $instruction[$active_lang]   = $this->get_instruction();
        $save['a_instructions']      = json_encode($instruction);
        $save['a_show_categories']   = $this->input->post('show_categories');
        $save['a_show_categories']   = ($save['a_show_categories'])?'1':'0';
        $save['action_by']           = $this->auth->get_current_admin('id');
        $save['action_id']           = $this->actions['create'];
        $assesment_id                = $this->Course_model->save_assesment($save);
        
        $response['error']           = 'false';
        $response['message']         = lang('assesment_saved_success');
        $response['id']              = $lecture_id;
        echo json_encode($response);exit;            
    }
    
    private function get_instruction()
    {
        return '<div id="dvInstruction">
            <p class="headings-altr"><strong>General Instructions:</strong></p>
            <ol class="header-child-alt">
            <li>The clock has been set at the server and the countdown timer at the top right corner of your screen will display the time remaining for you to complete the exam. When the clock runs out the exam ends by default - you are not required to end or submit your exam.</li>
            <li>The question palette at the right of screen shows one of the following statuses of each of the questions numbered:
            <table style="margin-bottom: 3px;"><tbody>
            <tr><td>You have not visited the question yet. ( In White Color )</td>
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
            <tr><td>You have marked the for review.( In Pink Color ) </td><td style="padding-left: 7px;"><div class="purpal" style="width: 20px;height: 20px;border-radius: 4px;"></div></td>
            </tr>
            </tbody>
            </table>
            </li>
            <li>&nbsp;</li>
            <li>The Marked for Review status simply acts as a reminder that you have set to look at the question again. <em>If an answer is selected for a question that is Marked for Review, the answer will be considered in the final evaluation.</em></li>
            </ol>
            <p class="headings-altr"><strong>Navigating to a question :</strong></p>
            <ol start="5" class="header-child-alt">
            <li>To select a question to answer, you can do one of the following:
            <ol type="a">
            <li>Click on the question number on the question palette at the right of your screen to go to that numbered question directly. Note that using this option does NOT save your answer to the current question.</li>
            <li>Click on Save and Next to save answer to current question and to go to the next question in sequence.</li>
            <li>Click on Mark for Review and Next to save answer to current question, mark it for review, and to go to the next question in sequence.</li>
            </ol>
            </li>
            <li>You can view the entire paper by clicking on the <strong>Question Paper</strong> button.</li>
            </ol>
            <p class="headings-altr"><strong>Answering questions :</strong></p>
            <ol start="7" class="header-child-alt">
            <li>For multiple choice type question :
            <ol type="a">
            <li>To select your answer, click on one of the option buttons</li>
            <li>To change your answer, click the another desired option button</li>
            <li>To save your answer, you MUST click on <strong>Save & Next</strong></li>
            <li>To deselect a chosen answer, click on the chosen option again or click on the <strong>Clear Response</strong> button.</li>
            <li>To mark a question for review click on <strong>Mark for Review & Next</strong>.&nbsp;</li>
            </ol>
            </li>
            <li>For a numerical answer type question
            <ol type="a">
            <li>To enter a number as your answer, use the virtual numerical keypad</li>
            <li>A fraction (eg. 0.4 or -0.4) can be entered as an answer ONLY with \'0\' before the decimal point</li>
            <li>To save your answer, you MUST click on <strong>Save & Next</strong></li>
            <li>To clear your answer, click on the<strong> Clear Response </strong>button</li>
            </ol>
            </li>
            <li>To change an answer to a question, first select the question and then click on the new answer option followed by a click on the <strong>Save & Next</strong> button.</li>
            <li>Questions that are saved or marked for review after answering will ONLY be considered for evaluation.</li>
            </ol>
            <p class="headings-altr"><strong>Navigating through sections :</strong></p>
            <ol start="11" class="header-child-alt">
            <li>Sections in this question paper are displayed on the top bar of the screen. Questions in a section can be viewed by clicking on the section name. The section you are currently viewing is highlighted.</li>
            <li>After clicking the <strong>Save & Next</strong> button on the last question for a section, you will automatically be taken to the first question of the next section.</li>
            <li>You can move the mouse cursor over the section names to view the status of the questions for that section.</li>
            <li>You can shuffle between sections and questions anytime during the examination as per your convenience.</li>
            </ol></div>';
    }
    
    
    
    function get_question_category_list()
    { 
        $data           = array();
        $keyword        = $this->input->post('q_category');
        $categories     = $this->Category_model->question_categories(array('name'=>$keyword));
 
        $data['tags']   = array();
        if( sizeof($categories))
        {
            foreach( $categories as $category)
            {
                $category['name'] = $category['qc_category_name'];
                $data['tags'][]   = $category;
            }
        }
        echo json_encode($data);
    }
    
    function upload_question()
    {
        $directory                  = question_upload_path();
        $this->make_directory($directory);
        $config                     = array();
        $config['upload_path']      = $directory;
        $config['allowed_types']    = "*";      
        $config['encrypt_name']     = true;
        $this->load->library('upload');
        $this->upload->initialize($config);
        $uploaded = $this->upload->do_upload('file');   
        $assessment_id = $this->input->post('assessment_id');
        if($uploaded)
        {
            $upload_data                    =  $this->upload->data();
            $upload_data['assessment_id']   = $assessment_id;
            //echo '<pre>'; print_r($upload_data);die;
            if($upload_data['file_ext'] == '.xls')
            {
               
                $this->upload_question_xls($upload_data);
            }
            else if($upload_data['file_ext'] == '.docx')
            {
                $this->upload_question_doc($upload_data);
            }
            // else
            // {
            //     $this->upload_question_doc($upload_data);
            //     $response['message'] = lang('file_upload_success');
            //     $response['error']   = 'false';
            //     echo json_encode($response);
            // }
        }
        else
        {
            $response['message'] = lang('file_upload_failed');
            $response['error']   = 'true';
            echo json_encode($response);
        }
    }
    
    public function upload_question_doc($upload_data = false)
    {

        // echo $_SERVER['DOCUMENT_ROOT'].'<pre>';  print_r($upload_data);die;
        $response               = array();
        $response['message']    = lang('file_imported_success');
        $response['success']      = true;
        
        $full_path      = $upload_data['full_path'];
        $extract_path   = $upload_data['file_path'].$upload_data['raw_name'].'/';
        $command        = 'export HOME=/tmp && libreoffice --headless --convert-to html '.$full_path.' --outdir '.$extract_path.'';
        shell_exec($command);  
        $response['html_file']          = str_replace($_SERVER['DOCUMENT_ROOT'], base_url(), $extract_path ).''.$upload_data['raw_name'].'.html';
        $upload_data['question_path']   = question_path();
        $response['uploaded_object']    = $upload_data;
        echo json_encode($response);
        die;
    }
    
    function load_parsed_document()
    {
        $this->doc_details                          = $this->input->post('doc_details',false);
        $this->doc_objects                          = $this->input->post('doc_objects',false);
        $this->doc_objects                          = json_decode($this->doc_objects, true);
        $upload_data                                = $this->input->post('upload_data',false);
        //echo '<pre>'; print_r($this->doc_objects);die;
        //validating the dicument id for uniqness
        $message                                    = "";
        $validate_template                          = false;
        $validate_template_param                    = array();
        //$validate_template_param['dut_date']        = date('Y-m-d');
        $validate_template_param['dut_name']        = $this->doc_details['document_id'];
        $template_exist                             = $this->Course_model->get_docs_template($validate_template_param);
        if(sizeof($template_exist)>0)
        {
            $response['success']       = false;
            $message                  .= 'Seems the file imported with this Document ID.</br>';
            $message                  .= 'Please verify the template.</br>';
            $response['message']       = $message;
            echo json_encode($response);die();        
        }
        //End
        //inserting question
        $questions                       = array();
        $question_sl_no                  = 1;
        $validate_options                = false;
        $validate_answer                 = false; 
        $validate_type                   = false;
        $validate_difficulty             = false;
        $validate_subject                = false;
        $validate_topic                  = false;
        $validate_positive_mark          = false;
        $validate_question               = false;
        $invalid_template                = false;
        $size_of_question                = '';
        $validate_single_choice_answer   = false;
        $validate_multiple_choice_answer = false;
        $alphabets                       = range('A', 'Z');
        $errormessageoption              = false;
        foreach ($this->doc_objects as $question)
        {
            $size_of_question  = sizeof($question);
            $q_options       = array();
            $q_answer        = array();
            
            //preparing the values for first question
            $question_object                       = array();
            $question_object['id']                 = false;
            $question['q_type']                    = strip_tags($question['q_type']);
            $question_object['q_type']             = $question['q_type'];
            if($question['q_type']=='')
            {
                $validate_type  = true;
            }

            $question['q_difficulty']              = strip_tags($question['q_difficulty']);
            $question_object['q_difficulty']       = $question['q_difficulty'];
            if($question['q_difficulty']=='')
            {
                $validate_difficulty  = true;
            }

            $question['q_positive_mark']           = strip_tags($question['q_positive_mark']);
            $question_object['q_positive_mark']    = $question['q_positive_mark'];
            if($question['q_positive_mark']=='')
            {
                $validate_positive_mark  = true;
            }

            $question['q_negative_mark']           = strip_tags($question['q_negative_mark']);
            $question_object['q_negative_mark']    = $question['q_negative_mark'];
            $question_object['q_account_id']       = $this->config->item('id');

            $category                              = $this->input->post('category_id',false);//directly from dropdown
            $question_object['q_category']         = $category; 
            $question_object['q_tags']             = $question['q_tags'];
            $question_object['q_subject']          = $question['q_subject'];

            $question['q_subject']                 = strip_tags($question['q_subject']);
            if($question['q_subject']=='')
            {
                $validate_subject  = true;
            }
           
            $question['q_topic']                   = strip_tags($question['q_topic']);
            $question_object['q_topic']            = $question['q_topic'];
            if($question['q_topic']=='')
            {
                $validate_topic  = true;
            } 

            $question_object['q_question']         = array();            
            $question_object['q_options']          = array();  
            $question_object['q_explanation']      = array();  
            $question_object['q_subjects']         = array();
            $question_object['q_subjects']         = $this->Category_model->subjects(array('qs_deleted'=>true,'category_id'=>$category));
            $check_subject                         = $this->Category_model->subject(array('subject_name' => $question_object['q_subject'], 'category_id' => $category, 'qs_deleted' => true));//qs_deleted means to get question which is not deleted
            
            $question_object['q_topics']           = array(); 
            if(isset($check_subject['id']))
            {
                $question_object['q_topics']           = $this->Category_model->topics(array('qt_deleted'=>true,'category_id'=>$category,'subject_id'=>$check_subject['id']));
            }
            $question_object['q_answer']           = '';
            // if (trim(strip_tags($question['q_question'], '<img>')) == "") 
            // {
            //     $question['q_question'] = '';
            // }
            
            // if($question['q_question']=='')
            // {
            //     $validate_question  = true;
            // }
            $question_object['q_question'][1]      = ($question['q_question']);
            
            

            $question_object['q_explanation'][1]   = $question['q_explanation'];
            //processing options
            /* insert the new options*/
            switch($question_object['q_type'])
            {
                case $this->__single_type:
                    $recieved_answer                       = $question['q_answer'];
                    $question_object['q_answer']           = $recieved_answer;
                    $options                               = isset($question['q_option']) ? $question['q_option'] : '';
                    if( !empty($options))
                    {
                            $option_count           = intval(0);
                            foreach ($options as $op_id => $value ) 
                            {
                                if (trim($value) != "") 
                                {
                                    // $value   = strip_tags($value,"<img><table>");
                                    $question_object['q_options'][$option_count+1][1] = $value;
                                    $option_count++;
                                }
                            }
                    }
                    //print_r($question_object['q_options']); 
                        //print_r($question_object['q_answer']);
                    
                    
                    if(sizeof($question_object['q_options']) < 2)
                    {
                        $validate_options                = true;
                    }
                    if($question_object['q_answer']=='')
                    {
                        $validate_answer                 = true;
                    }
                    else
                    {
                        if(!in_array(trim(strtoupper($question_object['q_answer'])), array_slice($alphabets, 0, sizeof($question_object['q_options'])))){
                            $validate_single_choice_answer = true;
                            $errormessageoption            .= 'One of the Single choice questions is invalid at Question No: <b>'.$question_sl_no.'</b>';
                        }
                    }
                    
                    if($size_of_question!='12')
                    {
                        $invalid_template                = true;
                    }
                    break;
                    
                    case $this->__multi_type:
                        $recieved_answer                       = $question['q_answer'];
                        $question_object['q_answer']           = $recieved_answer;
                        $options                               = isset($question['q_option']) ? $question['q_option'] : '';
                        
                        if( !empty($options))
                        {
                                $option_count           = intval(0);
                                foreach ($options as $op_id => $value) 
                                {
                                    if (trim($value) != "") 
                                    {
                                        // $value      = strip_tags($value,"<img><table>");
                                        $question_object['q_options'][$option_count+1][1] = $value;
                                        $option_count++;
                                    }
                                }
                        }

                        if(sizeof($question_object['q_options'])<2)
                        {
                            $validate_options                = true;
                        }
                        if($question_object['q_answer']=='')
                        {
                            $validate_answer                 = true;
                        }
                        else
                        {
                            $multiple_answersp = explode(" ",strtolower($question_object['q_answer']));
                            $multiple_answers = explode(",",strtolower($question_object['q_answer']));
                            $alp = array();
                            for($i = 0; $i < sizeof($question_object['q_options']); $i++)
                            {
                                $alp[$i] = strtolower($alphabets[$i]);
                            }
                                for($j = 0; $j < count($multiple_answers); $j++){

                                    if(!in_array($multiple_answers[$j],$alp)){
                                        $validate_multiple_choice_answer = true;
                                    }
                                }

                            if($validate_multiple_choice_answer){
                                $errormessageoption            .= 'One of the Multiple choice questions is invalid at Question No: <b>'.$question_sl_no.'</b>';
                            }
                            
                        }
                        if($size_of_question!='12')
                        {
                            $invalid_template                = true;
                        }
               
                    break;
                    
                    case $this->__subjective_type:
                        if($size_of_question!='10')
                        {
                            $invalid_template                = true;
                        }
                    break;

                    case $this->__fill_in_the_blanks:
                        if($size_of_question!='10')
                        {
                            $invalid_template                = true;
                        }
                    break;
                    
            }
            if((!$validate_question) && (trim(strip_tags($question_object['q_type']))!='')  && (trim(strip_tags($question_object['q_positive_mark']))!=''))
            {
                $question_object['id']           = $question_sl_no;
                $questions[$question_sl_no]      = $question_object;
                $question_sl_no++;
            }

            
        } 

        
        $validate_template                      = false;
        $template_params                        = array();
        $template_params['dut_date']            = date('Y-m-d');
        $template_exists                        = $this->Course_model->get_docs_template($template_params);
        
        $exist_template_names                   = explode(',', $template_exists['dut_name']);
        $exist_template_names[]                 = $this->doc_details['document_id'];
        $this->doc_details['id']                = $template_exists['id'];
        $this->doc_details['document_id']       = implode(',', $exist_template_names);
         
        
        
        $response                   = array();
        $category_id                = $this->input->post('category_id',false);
        $lecture_id                 = $this->input->post('lecture_id',false);
        $message                    = '';
        if(($validate_options==false) && ($validate_multiple_choice_answer==false) && ($validate_single_choice_answer==false) && ($validate_answer==false) && ($validate_template==false) && ($validate_type==false) && ($validate_difficulty==false) && ($validate_subject==false) && ($validate_topic==false) && ($validate_positive_mark==false) && ($validate_question==false) && ($invalid_template==false))
        {
            $process                    = array();
            $process['category_id']     = $category_id;
            $process['questions']       = array();
            $process['assessment_id']   = 0;
            if(!empty($questions))
            {
                $process['questions'] = $questions;
            }
            if(isset($upload_data['assessment_id']) && $upload_data['assessment_id'] != '')
            {
                $process['assessment_id'] = $upload_data['assessment_id'];
            }
            if($lecture_id)
            {
                $process['lecture_id']       = $lecture_id;
            }
            
            $process['document']       = $this->doc_details;

            $response['success']       = true;
            $response['message']       = 'Review uploaded Questions';

            $key                       = 'qimpt'.$this->__loggedInUser['id'];
            //echo '<pre>';print_r($process);die();
            //$this->memcache->delete($key);
            $this->memcache->set($key,$process);
        } 
        else
        {
            if($invalid_template==true)
            {
                $response['success']       = false;
                $message                  .= 'Invalid template.</br>Some rows missed from the template.</br>';
                $message                  .= 'Please verify the template.</br>';
                $response['message']       = $message;
                echo json_encode($response);die();
            }
            
            if($validate_options==true)
            {
                $response['success']       = false;
                $message                  .= 'Single or Multiple choice questions should have atleast two options.</br>';
            }
            if($validate_answer==true)
            {
                $response['success']       = false;
                $message                  .= 'Single or Multiple choice questions should have atleast one answer.</br>';
            }
            if($validate_template==true)
            {
                $response['success']       = false;
                $message                  .= 'Seems the file imported with this Document ID.</br>';
            }
            if($validate_type==true)
            {
                $response['success']       = false;
                $message                  .= 'Question type is mandatory.</br>';
            }
            if($validate_difficulty==true)
            {
                $response['success']       = false;
                $message                  .= 'Question difficulty is mandatory.</br>';
            }
            if($validate_subject==true)
            {
                $response['success']       = false;
                $message                  .= 'Question subject is mandatory.</br>';
            }
            if($validate_topic==true)
            {
                $response['success']       = false;
                $message                  .= 'Question topic is mandatory.</br>';
            }
            if($validate_positive_mark==true)
            {
                $response['success']       = false;
                $message                  .= 'Positive mark is mandatory.</br>';
            }

            if($validate_single_choice_answer == true || $validate_multiple_choice_answer == true)
            {
                $response['success']       = false;
                $message                  .= $errormessageoption.'<br/>';
            }

            
            // if($validate_question==true)
            // {
            //     $response['success']       = false;
            //     $message                  .= 'Question is mandatory.</br>';
            // }
            $message                      .= 'Please verify the template.</br>';
            $response['message']           = $message;
        }
        
        echo json_encode($response);die();
    }
    private function trim_text_custom($words)
    {
        $peices = explode(PHP_EOL, $words);
        if(sizeof($peices) <= 1)
        {
            $peices = explode(' ', $words);    
        }
        $peices_tmp = array();
        if(!empty($peices))
        {
            foreach ($peices as $peice)
            {
                $peice = trim($peice);
                if($peice)
                {
                    $peices_tmp[] = $peice;        
                }
            }
        }
        $peices_tmp = implode(' ', $peices_tmp);
        return $peices_tmp;
    }
    
    private function check_question_category($category_name, $question_category_id)
    {
        $category_name = trim($category_name);
        if(!isset($this->q_categories[$category_name]))
        {
            $category = $this->Category_model->question_category(array('category_name' => $category_name, 'parent_category' => $question_category_id));
            if(!$category)
            {
                $save                       = array();
                $save['id']                 = false;
                $save['qc_category_name']   = $category_name;
                $save['qc_parent_id']       = $question_category_id;
                $save['qc_status']          = '1';
                $save['qc_account_id']      = $this->config->item('id');
                $save['action_id']          = '1';
                $save['action_by']          = $this->auth->get_current_admin('id');
                $save['updated_date']       = date('Y-m-d H:i:s');
                $category['id']             = $this->Category_model->save_question_category($save);
                //echo '<pre>'; print_r($save);die;
            }
            $this->q_categories[$category_name] = $category['id']; 
        }
        return $this->q_categories[$category_name];
    }
    
    private function parse_answer_key($key='A')
    {
            $parser = array('A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5, 'F' => 6, 'G' => 7, 'H' => 8, 'I' => 9, 'J' => 10, 'K' => 11, 'L' => 12, 'M' => 13, 'N' => 14, 'O' => 15, 'P' => 16, 'Q' => 17, 'R' => 18, 'S' => 19, 'T' => 20, 'U' => 21, 'V' => 22, 'W' => 23, 'X' => 24, 'Y' => 25, 'Z' => 26);
            return isset($parser[$key])?$parser[$key]:false;
    }
    
    private function getTextBetweenTags($html)
    {
        $position1      = strpos($html, '>')+1;
        $position2      = strrpos($html, "</td>");
        $html_temp      = substr($html, $position1, ($position2-$position1));
        return $html_temp;
    }
    
    private function sl_no()
    {
        $this->mechanism = 3;
        $this->question_number++;
    }
    
    private function document_id($row_html)
    {
        $temp_html  = $this->getTextBetweenTags($row_html);
        $line       = $this->trim_doc_objects($temp_html);
        $this->doc_details['document_id'] = $line;
    }

    private function document_name($row_html)
    {
        $temp_html  = $this->getTextBetweenTags($row_html);
        $line       = $this->trim_doc_objects($temp_html);
        $this->doc_details['document_name'] = $line;
    }


    private function set_question_type($row_html)
    {
        $temp_html      = $this->getTextBetweenTags($row_html);
        $line           = $this->trim_doc_objects($temp_html);
        $question_types = array( 'single_choice' =>  'single', 'multiple_choice' =>  'multiple', 'subjective' =>  'subjective','fillups' =>  'fillups' );
        $question_mode  = isset($question_types[$line])?$question_types[$line]:'none';
        $this->doc_objects[$this->question_number]['q_type'] = $this->__question_types[$question_mode];
    }

    private function set_difficulty($row_html)
    {
        $temp_html  = $this->getTextBetweenTags($row_html);
        $line       = $this->trim_doc_objects($temp_html);
        $this->doc_objects[$this->question_number]['q_difficulty'] = isset($this->__difficulty[$line])?$this->__difficulty[$line]:'';
    }
    
    private function set_positive_mark($row_html)
    {
        $temp_html  = $this->getTextBetweenTags($row_html);
        $line       = $this->trim_doc_objects($temp_html);
        $this->doc_objects[$this->question_number]['q_positive_mark'] = $line;
    }
    
    private function set_negative_mark($row_html)
    {
        $temp_html  = $this->getTextBetweenTags($row_html);
        $line       = $this->trim_doc_objects($temp_html);
        $this->doc_objects[$this->question_number]['q_negative_mark'] = $line;
    }
    
    private function set_direction($row_html)
    {
        $temp_html = $this->getTextBetweenTags($row_html);
        $this->doc_objects[$this->question_number]['q_directions'] = $temp_html;
    }
    
    private function set_question($row_html)
    {
        $temp_html = $this->getTextBetweenTags($row_html);
        $this->doc_objects[$this->question_number]['q_question'] = $temp_html;        
    }

    private function set_explanation($row_html)
    {
        $temp_html = $this->getTextBetweenTags($row_html);
        $this->doc_objects[$this->question_number]['q_explanation'] = $temp_html;                
    }
    
    private function set_answer($row_html)
    {
        $temp_html  = $this->getTextBetweenTags($row_html);
        $line       = strtoupper($this->trim_doc_objects($temp_html));
        $this->doc_objects[$this->question_number]['q_answer'] = $line;   
    }
    
    private function set_options($row_html)
    {
        $temp_html = $this->getTextBetweenTags($row_html);
        $this->doc_objects[$this->question_number]['q_option'][] = $temp_html;                
    }
    
    private function set_subject($row_html)
    {
        $temp_html  = $this->getTextBetweenTags($row_html);
        $line       = $this->trim_doc_objects($temp_html, false, false);
        $this->doc_objects[$this->question_number]['q_subject'] = $line;
    }

    private function set_topic($row_html)
    {
        $temp_html  = $this->getTextBetweenTags($row_html);
        $line       = $this->trim_doc_objects($temp_html, false, false);
        $this->doc_objects[$this->question_number]['q_topic'] = $line;
    }

    private function set_tags($row_html)
    {
        $temp_html  = $this->getTextBetweenTags($row_html);
        $line       = $this->trim_doc_objects($temp_html, false, false);
        $this->doc_objects[$this->question_number]['q_tags'] = $line;
    }
    
    private function trim_doc_objects($string, $string_to_lower=true, $replace_space = true)
    {
        $string_temp = $string;
        $string_temp = trim($string_temp);
        $string_temp = strip_tags($string_temp);
        if($replace_space)
        {
            $string_temp = str_replace(' ', '', $string_temp);        
        }
        $string_temp = str_replace('&#13;', '', $string_temp);
        $string_temp = trim($string_temp);        
        if($string_to_lower)
        {
            $string_temp = strtolower($string_temp);        
        }
        return $string_temp;
    }
    
    private function upload_question_csv()
    {
        $response        = array();
        $response['message'] = lang('file_imported_success');
        $response['error']   = 'false';
        $lecture_id      = $this->input->post('lecture_id');
        $lecture         = $this->Course_model->lecture(array('id'=>$lecture_id));
        $course_id       = $lecture['cl_course_id'];
        $assesment       = $this->Course_model->assesment(array('lecture_id' => $lecture_id, 'course_id' => $course_id));
        $assesment_id    = $assesment['assesment_id'];

        $file   = $upload_data['full_path'];
        $file   = fopen($file, "r") or die("Unable to open file!");
        $header = fgetcsv($file);
        //creating dynamic variable user the header name
        foreach ($header as $key => $value) {
            $$value = $key;
        }
        /*
         * Output after the above loop
        $question_type      = "0";
        $difficulty         = "1";
        $positive_mark      = "2";
        $negative_mark      = "3";
        $direction          = "4";
        $question           = "5";
        $explanation        = "6";
        $option             = "7";
        $answer             = "8";
        */
        //end of creating dynamic name

        while (($line = fgetcsv($file)) !== FALSE) {
        $q_options       = array();
        $q_answer        = array();
            $line[$option] = explode('{#}', $line[$option]);
            //preparing the values for first question
            //print_r($line);
            $question_object                       = array();
            $question_object['id']                 = false;
            $question_object['q_type']             = $this->__question_types[$line[$question_type]];
            $question_object['q_difficulty']       = $this->__difficulty[$line[$difficulty]];
            $question_object['q_positive_mark']    = $line[$positive_mark];
            $question_object['q_negative_mark']    = $line[$negative_mark];
            $question_object['q_directions']       = $line[$direction];
            $question_object['q_question']         = $line[$question];
            $question_object['q_explanation']      = $line[$explanation];
            $question_object['q_course_id']        = $course_id;
            //processing options
            $options    = $line[$option];
            //print_r($options);

            /* insert the new options*/
            $options         = $line[$option];
            if( $question_object['q_type'] == $this->__single_type)
            {
                $recieved_answer = trim($line[$answer]);
                if( !empty($options))
                {
                    foreach ($options as $op_id => $value ) {
                        $save               = array();
                        $save['id']         = false;
                        $save['qo_options'] = $value;
                        $option_id          = $this->Course_model->save_option($save);
                        $q_options[]        = $option_id;
                        if(intval($op_id+1) == $recieved_answer)
                        {
                            $q_answer[] = $option_id;
                        }
                    }
                }
            }
            else
            {
                $recieved_answer = explode(',', $line[$answer]);
                if( !empty($options))
                {
                    foreach ($options as $op_id => $value ) {
                        $save               = array();
                        $save['id']         = false;
                        $save['qo_options'] = $value;
                        $option_id          = $this->Course_model->save_option($save);
                        $q_options[]        = $option_id;
                        if(in_array(intval($op_id+1), $recieved_answer))
                        {
                            $q_answer[] = $option_id;
                        }
                    }
                }
            }
            /*End*/

            $question_object['q_options']          = implode(',', $q_options);
            $question_object['q_answer']           = implode(',', $q_answer);
            $question_id                           = $this->Course_model->save_question($question_object);

            $save                       = array();
            $save['assesment_id']       = $assesment_id;
            $save['question_id']        = $question_id;
            $save['positive_mark']      = $question_object['q_positive_mark'];
            $save['negative_mark']      = $question_object['q_negative_mark'];
            $this->Course_model->save_assesment_question($save);
            //end

            /*Deactivate lecture*/
            $this->deactivate_lecture($lecture_id);
            /*End deactivate lecture*/

           // print_r($question_object);
        }
        fclose($file);
    }
    
    function delete_question_bulk()
    {
        $question_ids = $this->input->post('question_ids');
        $question_ids = json_decode($question_ids, true);
        if(!empty($question_ids))
        {
            $this->Generate_test_model->delete_question_bulk($question_ids);
            /*Log creation*/
            $user_data                      = array();
            $user_data['user_id']           = $this->__loggedInUser['id'];
            $user_data['username']          = $this->__loggedInUser['us_name'];
            $user_data['useremail']          = $this->__loggedInUser['us_email'];
            $user_data['user_type']         = $this->__loggedInUser['us_role_id']; 
            $questions_count                = (sizeof($question_ids)>1)?sizeof($question_ids).' questions':'a question';
            $message_template               = array();
            $message_template['username']   = $this->__loggedInUser['us_name'];
            $message_template['count']      = $questions_count;
            $triggered_activity             = 'question_deleted';
            log_activity($triggered_activity, $user_data, $message_template);
        }
        $response               = array();
        $response['error']      = false;
        $response['message']    = lang('question_deleted_success');
        $this->session->set_flashdata('message', 'Questions deleted successfully.');
        echo json_encode($response);
    }
    
    function delete_question()
    {
        $response               = array();
        $response['error']      = 'false';
        $response['message']    = lang('question_deleted_success');
        $question_id            = $this->input->post('question_id');
        $question               = $this->Course_model->question(array('id'=>$question_id)); 
        if(!$question)
        {
            $response['error']      = 'true';
            $response['message']    = lang('question_deleted_failed');    
            echo json_encode($response);exit;
        }
        
        if(!$this->Generate_test_model->delete_question($question_id))
        {
            $response['error']      = 'true';
            $response['message']    = lang('question_deleted_failed');    
        } else {
            /*Log creation*/
            $user_data                      = array();
            $user_data['user_id']           = $this->__loggedInUser['id'];
            $user_data['username']          = $this->__loggedInUser['us_name'];
            $user_data['useremail']          = $this->__loggedInUser['us_email'];
            $user_data['user_type']         = $this->__loggedInUser['us_role_id']; ;
            $message_template               = array();
            $message_template['username']   = $this->__loggedInUser['us_name'];
            $message_template['count']      = 'a question';
            $triggered_activity             = 'question_deleted';
            log_activity($triggered_activity, $user_data, $message_template);
        }
        echo json_encode($response);        
    }
    
    function delete_assesment_question()
    {
        $response               = array();
        $response['error']      = 'false';
        $response['message']    = lang('question_deleted_success');
        $question_id            = $this->input->post('question_id');
        $assesment_id           = $this->input->post('assesment_id');
        if(!$this->Course_model->delete_assesment_question(array('aq_question_id' => $question_id, 'aq_assesment_id' => $assesment_id)))
        {
            $response['error']      = 'true';
            $response['message']    = lang('question_deleted_failed');    
        }
        echo json_encode($response);
    }
    
    private function get_upload_config($mechanism=false)
    {
        if(!$mechanism)
        {
            return false;
        }
        
        $video_formats          = array('mp4', 'flv', 'avi', 'f4v');
        $document_formats       = array('doc', 'docx', 'xls', 'pdf', 'ppt', 'pptx');
        $config                 = array();
        $config['encrypt_name'] = true;
        if(in_array($mechanism, $video_formats))
        {
            $this->__lecture_type       = $this->__lecture_type_keys_array['video'];
            $directory                  = video_upload_path();
            $this->make_directory($directory);
            $config['upload_path']      = $directory;
            $config['allowed_types']    = implode('|', $video_formats);      
            return $config;
        }
        
        if(in_array($mechanism, $document_formats))
        {
            $this->__lecture_type       = $this->__lecture_type_keys_array['document'];
            $directory                  = document_upload_path();
            $this->make_directory($directory);
            $config['upload_path']      = $directory;
            $config['allowed_types']    = implode('|', $document_formats);       
            return $config;
        }
    }
    
    function s3objetcs()
    {
        
        $response               = array();
        $response['error']      = true;
        $response['s3_object']  = array();
        $s3_setting             = $this->settings->setting('has_s3');
        if( $s3_setting['as_superadmin_value'] && $s3_setting['as_siteadmin_value'] )
        {
            $response['error']      = false;
            $s3_keys                = $s3_setting['as_setting_value']['setting_value'];

            define('S3_BUCKET', $s3_keys->s3_bucket);
            define('S3_KEY',    $s3_keys->s3_access);
            define('S3_SECRET', $s3_keys->s3_secret);
            define('S3_ACL',    'private');
            
            $now    = time() + (12 * 60 * 60 * 1000);
            $expire     = gmdate('Y-m-d\TH:i:s\Z', $now);
            $url    = 'https://' . S3_BUCKET . '.s3.amazonaws.com'; 
            $policy     = json_encode(  
                                        array(
                                                'expiration' => '2020-01-01T00:00:00Z',
                                                'conditions' => array(
                                                                        array( 'bucket' => S3_BUCKET ),
                                                                        array( 'acl' => S3_ACL ), 
                                                                        array( 'starts-with', '$key', '',),
                                                                        array( "success_action_status" => "201" )
                                                                    )
                                            )
                                    );  
            $base64Policy   = base64_encode($policy);
            $signature      = base64_encode(hash_hmac("sha1", $base64Policy, S3_SECRET, $raw_output = true));
            
            $response['s3_object']['access_key']                = S3_KEY;
            $response['s3_object']['acl']                       = S3_ACL;
            $response['s3_object']['success_action_status']     = '201';
            $response['s3_object']['policy']                    = $base64Policy;
            $response['s3_object']['signature']                 = $signature;
            $response['video_path']                             = video_upload_path();
            $response['document_path']                          = document_upload_path();
        }
        echo json_encode($response);
    }
    
    private function make_directory($path=false)
    {
        if(!$path )
        {
            return false;
        }
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
    }
    
    function language()
    {
        $response = array();
        $response['language'] = array();
        $response['language'] = get_instance()->lang->language;
        echo json_encode($response);
    }
    
    
    private function validate_template()
    {
        $sheet          = $this->__objPHPExcel->getSheet(0);
        $totalRows      = $sheet->getHighestRow();
        
        // calculating the last column
        $highestColumn  = $sheet->getHighestColumn();
        $header_rows    = $sheet->rangeToArray('A1' . ':' . $highestColumn.'1', NULL, TRUE, FALSE);
        $header_rows    = isset($header_rows[0])?$header_rows[0]:array();
        $header_count   = count($header_rows);
        if(!(($header_count < 20) || ($header_count > 20))) 
        {
            $totalColumns   = 1;
            if(!empty($header_rows))
            {
                foreach ($header_rows as $key => $h_rows)
                {
                    if($h_rows!='')
                    {
                        $$h_rows = $key;
                        $totalColumns++;                
                    }
                }
            }

            $validation_rule        = array();
            $validation_rule[0]     = 'Question_type';
            $validation_rule[1]     = 'Difficulty';
            $validation_rule[2]     = 'Positive_mark';
            $validation_rule[3]     = 'Negative_mark';
            $validation_rule[4]     = 'Tags';
            $validation_rule[5]     = 'Subject';
            $validation_rule[6]     = 'Topic';
            $validation_rule[7]     = 'Question';
            $validation_rule[8]     = 'option_1';
            $validation_rule[9]     = 'option_2';
            $validation_rule[10]    = 'option_3';
            $validation_rule[11]    = 'option_4';
            $validation_rule[12]    = 'option_5';
            $validation_rule[13]    = 'option_6';
            $validation_rule[14]    = 'option_7';
            $validation_rule[15]    = 'option_8';
            $validation_rule[16]    = 'option_9';
            $validation_rule[17]    = 'option_10';
            $validation_rule[18]    = 'Explanation';
            $validation_rule[19]    = 'Answer';
            
            $row                    = 1;
            $rowData                = $sheet->rangeToArray('A' . $row . ':' . $this->toAlpha($totalColumns) . $row, NULL, TRUE, FALSE);
            $rowData                = $rowData[0];
            
            $response                   = array();
            $response['success']        = true;
            $response['message']        = 'Validation passed';
            if($rowData[0] != $validation_rule[0]) {
                $response['success']        = false;
                $response['message']        = '<b>'.$validation_rule[0].'</b> is misspelled as <b>'.$rowData[0].'</b>'.'</b> - on cell  <b>'.$this->toAlpha(2).'1</b>';
                return $response;
            }
            if($rowData[1] != $validation_rule[1]) {
                $response['success']        = false;
                $response['message']        = '<b>'.$validation_rule[1].'</b> is misspelled as <b>'.$rowData[1].'</b>'.'</b> - on cell  <b>'.$this->toAlpha(3).'1</b>';
                return $response;
            }
            if($rowData[2] != $validation_rule[2]) {
                $response['success']        = false;
                $response['message']        = '<b>'.$validation_rule[2].'</b> is misspelled as <b>'.$rowData[2].'</b>'.'</b> - on cell  <b>'.$this->toAlpha(4).'1</b>';
                return $response;
            }
            if($rowData[3] != $validation_rule[3]) {
                $response['success']        = false;
                $response['message']        = '<b>'.$validation_rule[3].'</b> is misspelled as <b>'.$rowData[3].'</b>'.'</b> - on cell  <b>'.$this->toAlpha(5).'1</b>';
                return $response;
            }
            if($rowData[4] != $validation_rule[4]) {
                $response['success']        = false;
                $response['message']        = '<b>'.$validation_rule[4].'</b> is misspelled as <b>'.$rowData[4].'</b>'.'</b> - on cell  <b>'.$this->toAlpha(6).'1</b>';
                return $response;
            }
            if($rowData[5] != $validation_rule[5]){
                $response['success']        = false;
                $response['message']        = '<b>'.$validation_rule[5].'</b> is misspelled as <b>'.$rowData[5].'</b>'.'</b> - on cell  <b>'.$this->toAlpha(7).'1</b>';
                return $response;
            }
            if($rowData[6] != $validation_rule[6]){
                $response['success']        = false;
                $response['message']        = '<b>'.$validation_rule[6].'</b> is misspelled as <b>'.$rowData[6].'</b>'.'</b> - on cell  <b>'.$this->toAlpha(8).'1</b>';
                return $response;
            }
            if($rowData[$totalColumns-2] != $validation_rule[19]){
                $response['success']        = false;
                $response['message']        = '<b>'.$validation_rule[19].'</b> is misspelled as <b>'.$rowData[$totalColumns-2].'</b>'.'</b> - on cell  <b>'.$this->toAlpha($totalColumns).'1</b>';
                return $response;
            }

            $count                         = 7;
            $language                      = 1;
            for($i=7;$i < 20;$i++){
                if($rowData[$i] != $validation_rule[$count]) {
                    $response['success']   = false;
                    $response['message']   = '<b>'.$validation_rule[$count].'</b> is misspelled as <b>'.$rowData[$i].'</b> - on cell  <b>'.$this->toAlpha($i+2).'1</b>';
                return $response;
                }
                if($count == 20) {
                    $count = 6;
                }
                $count++;
            }
        } else {
            $response['success']        = false;
            $response['message']        = 'Issue with template structure.';
            return $response;
       }
       return $response;
    }

    function upload_question_xls($upload_data)
    {

        require_once APPPATH.'third_party/'.'PHPExcel/Classes/PHPExcel.php';
        $this->__inputFile     = $upload_data['file_name'];
        $this->__inputFileName = question_upload_path().$this->__inputFile;
        try 
        {
            $this->__inputFileType = PHPExcel_IOFactory::identify($this->__inputFileName);
            $this->__objReader = PHPExcel_IOFactory::createReader($this->__inputFileType);
            $this->__objPHPExcel = $this->__objReader->load($this->__inputFileName);
            //$this->__PHPExcel_Worksheet_MemoryDrawing = PHPExcel_Worksheet_MemoryDrawing;
        } 
        catch(Exception $e) 
        {
            die('Error loading file "'.pathinfo($this->__inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
        }

        $validation = $this->validate_template();
        if($validation['success'] == false)
        {
            echo json_encode($validation);die;
        }
        
        //creating image directory
        $inputFile     = explode('.', $this->__inputFile);
        $inputFile     = $inputFile[0];
        /*if($imageParam['store_type'] == 1){
            $inputFile     = $inputFile[0];
        }else{
            $inputFile     = 'temp';
        }*/

        $this->__imageDirectory= question_upload_path().$inputFile;
        if(!is_dir($this->__imageDirectory))
        {
            $oldmask = umask(0);
            mkdir($this->__imageDirectory, 0777);
            umask($oldmask);
        }
        
        
        //  Get worksheet dimensions


        $sheet          = $this->__objPHPExcel->getSheet(0); 
        $totalRows      = $sheet->getHighestRow(); 
        $header_names   = array();
        
        // calculating the last column
        $highestColumn  = $sheet->getHighestColumn();
        $header_rows    = $sheet->rangeToArray('A1' . ':' . $highestColumn.'1', NULL, TRUE, FALSE);
        $header_rows    = isset($header_rows[0])?$header_rows[0]:array();
        $totalColumns   = 1;

        if(!empty($header_rows))
        {
            foreach ($header_rows as $key => $h_rows)
            {
                if($h_rows!='')
                {
                    $$h_rows = $key;
                    $header_names[] = $h_rows;
                    $totalColumns++;                
                }
            }
        }
        //end of calculating the last column
        
        $this->__question_types     = array('single' => '1', 'multiple' => '2', 'subjective' => '3', 'fill in the blanks' => '4');
        $this->__difficulty         = array('easy' => '1', 'medium' => '2', 'hard' => '3');
        $this->__single_type        = '1';
        $this->__multi_type         = '2';
        $this->__subjective_type    = '3';
        $this->__fill_in_the_blanks = '4';
        $header_names_size          = sizeof($header_names);
        $defective_rows             = array();
        
        // echo '<pre>'; print_r($header_names);die();
        //  Loop through each row of the worksheet in turn
        $questions                       = array();
        $question_sl_no                  = 1;
        $validate_options                = false;
        $validate_answer                 = false; 
        $category_id                     = $this->input->post('category_id');//directly from dropdown
        $subjects                        = $this->Category_model->subjects(array('qs_deleted'=>0,'category_id'=>$category_id));

        for ($row = 2; $row <= $totalRows; $row++)
        { 
            $rowData                               = $sheet->rangeToArray('A' . $row . ':' . $this->toAlpha($totalColumns) . $row, NULL, TRUE, FALSE);
            $rowData                               = isset($rowData[0])?$rowData[0]:array();
            // echo '<pre>';print_r($rowData);die();
            $question_object                       = array();
            $question_object['id']                 = false;
            $question_object['q_type']             = strtolower($rowData[0]);
            $question_object['q_difficulty']       = strtolower($rowData[1]);
            $question_object['q_type']             = isset($this->__question_types[$question_object['q_type']])?$this->__question_types[$question_object['q_type']]:'';
            $question_object['q_difficulty']       = isset($this->__difficulty[$question_object['q_difficulty']])?$this->__difficulty[$question_object['q_difficulty']]:'';
            $question_object['q_positive_mark']    = abs($rowData[2]);
            $question_object['q_negative_mark']    = $rowData[3];
            $question_object['q_answer']           = $rowData[19];
            $question_object['q_account_id']       = $this->config->item('id');   
            
            //processing category
            $question_object['q_category']  = $category_id;   
            $question_object['q_tags']      = $rowData[4];
            $question_object['q_subject']   = $rowData[5];   
            $question_object['q_topic']     = $rowData[6]; 
            
            $question_object['q_subjects']  = $subjects;
            $check_subject                  = $this->Category_model->subject(array('subject_name' => $question_object['q_subject'], 'category_id' => $category_id));

            $question_object['q_question']        = array();            
            $question_object['q_options']         = array();  
            $question_object['q_explanation']     = array();   

            $question_object['q_topics']          = array(); 
            $question_object['q_topics']          = $this->Category_model->topics(array('qt_deleted'=>true,'category_id'=>$category_id,'subject_id'=>$check_subject['id']));
            $images                               = $this->scanImages(array('cell_id'=>$this->toAlpha(9).$row,'question_file'=>$inputFile,'store_type'=>0));
            $question_object['q_question'][1]     = $rowData[7].$images;
            for($op_count=0;$op_count<10;$op_count++) 
            {
                $images            = $this->scanImages(array('cell_id'=>$this->toAlpha($op_count+10).$row,'question_file'=>$inputFile,'store_type'=>0));
                $option_value      = $rowData[$op_count+8].$images;
                   //if(str_replace(" ","",strip_tags($option_value))!='')
                    if(trim($option_value)!='')
                    {
                        $question_object['q_options'][$op_count+1][1] = $option_value;                                    
                    }
            }

            $images                              = $this->scanImages(array('cell_id'=>$this->toAlpha(20).$row,'question_file'=>$inputFile,'store_type'=>0));
            $question_object['q_explanation'][1] = $rowData[18].$images;


            //validating rows for error
            $valid_row  = true;
            $defects    = array();

            $question_type = trim($question_object['q_type']);
            if( $question_type == '')
            {
                $valid_row  = false;
                $defects[]  = 'Question type is invalid in row number <b>'.$row.'</b>';
            }

            $question_difficulty = trim($question_object['q_difficulty']);
            if( $question_difficulty == '')
            {
                $valid_row  = false;
                $defects[]  = 'Question difficulty is invalid in row number <b>'.$row.'</b>';
            }

            $question_positive_mark = $question_object['q_positive_mark'];
            if( $question_positive_mark == '')
            {
                $valid_row  = false;
                $defects[]  = 'Positive mark is missing in row number <b>'.$row.'</b>';
            }
            $question_negative_mark = $question_object['q_negative_mark'];
            // if( $question_negative_mark == '')
            // {
            //     $valid_row  = false;
            //     $defects[]  = 'Negative mark is missing in row number <b>'.$row.'</b>';
            // }

            $question_negative_mark = $question_negative_mark ? $question_negative_mark : 0;
            if(trim($question_object['q_question'][1]) == '')
            {
                $valid_row  = false;
                $defects[] = 'Question is missing in row number <b>'.$row.'</b>';
            }

            switch($question_object['q_type'])
            {
                case $this->__single_type:
                    if(sizeof($question_object['q_options'])<2)
                    {
                        $valid_row  = false;
                        $defects[] = 'Question should have atleast two options in row number <b>'.$row.'</b>';
                    }
                    if(trim($question_object['q_answer']) == '')
                    {
                        $valid_row  = false;
                        $defects[] = 'Question should have an answer in row number <b>'.$row.'</b>';
                    }
                break;

                case $this->__multi_type:
                    if(sizeof($question_object['q_options'])<2)
                    {
                        $valid_row  = false;
                        $defects[] = 'Question should have atleast two options in row number <b>'.$row.'</b>';
                    }
                    if(trim($question_object['q_answer']) == '')
                    {
                        $valid_row  = false;
                        $defects[] = 'Question should have an answer in row number <b>'.$row.'</b>';
                    }
                break;
            }

            if(!$valid_row)
            {
                $defective_rows[$question_sl_no+1] = $defects;
            }
            else
            {
                $question_object['id']           = $question_sl_no;
                $questions[$question_sl_no]      = $question_object;
            }
            $question_sl_no++;
            //validation ends here
        }


        $response                   = array();
        $assesment_id               = (isset($upload_data['assessment_id']) && $upload_data['assessment_id'] != '')?$upload_data['assessment_id']:false; 
        $lecture_id                 = '';
        if($assesment_id)
        {
            $keys                               = 'assesment_'.$assesment_id;
            $assesment_objects                  = array();
            $assesment_objects['key']           = 'assesment_'.$assesment_id;
            $assesment_callback                 = 'assesment_details';
            $assesment_params                   = array();
            $assesment_params['assesment_id']   = $assesment_id;
            $assesment_details                  = $this->memcache->get($assesment_objects, $assesment_callback, $assesment_params);
            if(!empty($assesment_details) && isset($assesment_details['assesment_details']) && isset($assesment_details['assesment_details']['a_lecture_id']))
            {
                $lecture_id = $assesment_details['assesment_details']['a_lecture_id'];
            } 
        }
        $process                    = array();
        $process['lecture_id']      = ($lecture_id)?$lecture_id:'';
        $process['category_id']     = $category_id;
        $process['questions']       = array();
        $process['assessment_id']   = 0;
        $process['defective_rows']  = $defective_rows;
        if(!empty($questions))
        {
            $process['questions'] = $questions;
        }
        if(isset($upload_data['assessment_id']) && $upload_data['assessment_id'] != '')
        {
            $process['assessment_id'] = $upload_data['assessment_id'];
        }
        $response['success'] = true;
        $response['message'] = 'Review uploaded Questions';
        
        $key = 'qimpt'.$this->__loggedInUser['id'];
        $this->memcache->delete($key);
        $this->memcache->set($key,$process);
        
        echo json_encode($response);die();
    }


    function upload_question_preview()
    {
        // $key                       = 'qimpt'.$this->__loggedInUser['id'];
        // $questions_objects         = array();
        // $questions_objects['key']  = $key;
        // $uploaded_questions        = $this->memcache->get($questions_objects);
        // echo '<pre>'; print_r($uploaded_questions);die;
        $this->load->view($this->config->item('admin_folder').'/upload_question_preview');
    }

    function delete_preview_questions(){
        $response                  = array();
        $question_id               = $this->input->post('question');
        $key                       = 'qimpt'.$this->__loggedInUser['id'];
        $questions_objects         = array();
        $questions_objects['key']  = $key;
        $uploaded_questions        = $this->memcache->get($questions_objects);
        unset($uploaded_questions['questions'][$question_id]);
        $uploaded_questions['questions'] = array_combine(range(1, count($uploaded_questions['questions'])), array_values($uploaded_questions['questions']));
        $this->memcache->delete($key);
        $this->memcache->set($key,$uploaded_questions);
        $response['success']       = true;
        $response['message']       = 'Quesions deleted successfully.';
        echo json_encode($response);
    }
    
    function preview($id = false)
    {
        if(!$id)
        {
            $this->session->set_flashdata('error', 'Error in previwing question. Please try again');
            redirect(admin_url('generate_test'));
        }
        $question                   = $this->Generate_test_model->question(array('id' => $id));
        // print_r($question);
        // die();
        if(empty($question))
        {
            $this->session->set_flashdata('error', 'Error in previwing question. Please try again');
            redirect(admin_url('generate_test'));
        }
        
        $data           = array();
        $data['title']  = 'Question Form';
        $data['id']     = $id;
        //===============processing web language====================
        $active_web_language = $this->session->userdata('active_web_language');
        if(!$active_web_language)
        {
            $active_web_language     = '1';
            $this->session->set_userdata('active_web_language', $active_web_language);
        }
        $data['active_web_language'] = $active_web_language;
        
        $this->load->model('Language_model');
        $data['web_languages'] = $this->Language_model->languages();
        //==============End of processing web language===============
        
        $data['question_types']     = array('1' => 'Single Choice','2' => 'Multiple Choice' , '3' => 'Subjective Type','4' =>'Fill in the blanks');
        $data['difficulty']         = array( '1' => 'Easy' ,'2' => 'Medium','3'=> 'Hard');
        $data['selected']           = 'selected="selected"';
        
        $data['q_type']             = $question['q_type'];
        $data['q_code']             = $question['q_code'];
        $data['q_difficulty']       = $question['q_difficulty'];
        $category                   = $this->Category_model->category(array('id'=>$question['q_category']));
        $data['q_category']         = $category['ct_name'];
        $subject                    = $this->Category_model->subject(array('id'=>$question['q_subject']));
        $data['q_subject']          = $subject['qs_subject_name'];
        $topic                      = $this->Category_model->topic(array('id'=>$question['q_topic']));
        $data['q_topic']            = $topic['qt_topic_name'];
        $data['q_positive_mark']    = $question['q_positive_mark'];
        $data['q_negative_mark']    = $question['q_negative_mark'];

        $tags                       = '';
        if($question['q_tags'])
        {
            $tags                   = $this->Category_model->tags(array('ids'=>$question['q_tags']));  
            if(!empty($tags))
            {
                $tags_temp          = array();
                foreach ($tags as $tag)
                {
                   $tags_temp[]     = $tag['tg_name'];
                }
                $tags = implode(',', $tags_temp);
            }
        }
        $data['q_tags']             = $tags;

        $data['q_question']         = $question['q_question'];
        $data['q_explanation']      = htmlentities($question['q_explanation']);
        $data['qus_options']        = $question['q_options'];
        $data['qus_answer']         = $question['q_answer'];

        $data['q_options']          = $this->Course_model->options(array('q_answer' => $question['q_options']));
        $existing_options           = $data['q_options'];
        $data['q_answer']           = ($question['q_answer'])?explode(',', $question['q_answer']):array();
        //echo '<pre>';print_r($data['q_explanation']);die();
        $this->load->view($this->config->item('admin_folder').'/question_bank_form_preview', $data);
    }
    
    function confirm_import_questions() 
    {
        $selectedValues           = $this->input->post('selectedValues');
        $selectedArray            = json_decode($selectedValues, true);
        $process=array();
        $key                      = 'qimpt'.$this->__loggedInUser['id'];
        $questions_objects        = array();
        $questions_objects['key'] = $key;
        $process                  = $this->memcache->get($questions_objects);
        $lecture_id               = (isset($process['lecture_id']))?$process['lecture_id']:'0';
        $questions                = $process['questions'];
        $assessment_id            = $process['assessment_id'];
        $category_id              = $process['category_id'];
        if(isset($process['document']))
        {
            // echo '<pre>'; print_r($process);die;
            $document_details             = $process['document'];
            $document_param               = array();
            $document_param['id']         = (isset($document_details['id']))?$document_details['id']:false;
            $document_param['dut_date']   = (isset($document_details['date']))?$document_details['date']:false;
            $document_param['dut_name']   = (isset($document_details['document_id']))?$document_details['document_id']:'';
            $template_name                = $this->Course_model->save_docs_template($document_param);
        }
        //unset($process['document']);
        $question_ids             = array();
        $questions_options        = array(); 
        $response                 = array();
        $response['lecture_id']   = base64_encode($lecture_id);
        //confirm saving questions
        if(!empty($questions))
        {
            $questions_array      = array();
            $a =1;
            $subject_validation   = false;
            $topic_validation     = false;
            foreach ($questions as $question)
            {
                $question['id']  = false;
                $q_options       = array();
                $q_answer        = array();
                
                //processing tags
                $tags                 = '';
                $tags_recieved        = $question['q_tags'];
                $tags_recieved        = explode(',', $tags_recieved);
                if(!empty($tags_recieved))
                {
                    $tags           = array();
                    foreach($tags_recieved as $r_tag)
                    {
                        $r_tag      = strtolower($r_tag);
                        $tag_exist  = $this->Category_model->tag(array('tag_name' => $r_tag));
                        if(!empty($tag_exist))
                        {
                            $tags[] = $tag_exist['id'];
                        }
                        else
                        {
                            $save_tag                   = array();
                            $save_tag['id']             = false;
                            $save_tag['tg_name']        = $r_tag;
                            $save_tag['tg_account_id']  = config_item('id');
                            $tags[]                     = $this->Category_model->save_tag($save_tag);
                        }
                    }
                    $tags                  = implode(',', $tags);
                }
                $question['q_tags']        = $tags;

          
                //end processing tags
                $selected_subject          = $selectedArray[$a]['subject_id'];
                
                //processing subject
                if($selected_subject!=0){
                   $question['q_subject']  = $selected_subject; 
                } else {
                    $subject               = $question['q_subject'];
                    if($subject!='')
                    {
                        $subject_exist         = $this->Category_model->subject(array('subject_name' => $subject,'category_id'=> $category_id));
                        if(!empty($subject_exist))
                        {
                            $subject           = $subject_exist['id'];
                        }
                        else
                        {
                            $save_subject                       = array();
                            $save_subject['id']                 = false;
                            $save_subject['qs_category_id']     = $category_id;
                            $save_subject['qs_subject_name']    = $subject;
                            $save_subject['qs_account_id']      = config_item('id');
                            $subject                            = $this->Category_model->save_subject($save_subject);
                        }
                        $question['q_subject']                  = $subject;
                    } 
                    else 
                    {
                        $subject_validation = true;
                    }
                    
                }
                $selected_topic           = $selectedArray[$a]['topic_id'];

                if($selected_topic!=0) {
                   $question['q_topic']   = $selected_topic; 
                } else {
                    $topic                = $question['q_topic'];
                    if($topic!='')
                    {
                        $topic_exist          = $this->Category_model->topic(array('topic_name' => $topic,'subject_id' => $question['q_subject'],'category_id'=> $category_id));
                        if(!empty($topic_exist))
                        {
                            $topic            = $topic_exist['id'];
                        }
                        else
                        {
                            $save_topic                     = array();
                            $save_topic['id']               = false;
                            $save_topic['qt_category_id']   = $category_id;
                            $save_topic['qt_subject_id']    = $question['q_subject'];
                            $save_topic['qt_topic_name']    = $topic;
                            $save_topic['qt_account_id']    = config_item('id');
                            $topic                          = $this->Category_model->save_topic($save_topic);
                        }
                        $question['q_topic']       = $topic;  
                    }
                    else 
                    {
                        $topic_validation = true;
                    }
                    
                }
                switch($question['q_type'])
                {
                    case $this->__single_type:
                        $options                 = $question['q_options'];
                        $question['q_answer']    = $this->toNum(strtoupper($question['q_answer']));
                        $options_temp            = array();
                        
                        if( !empty($options))
                        {
                            foreach ($options as $op_id => $value ) 
                            {
                                $save               = array();
                                $save['id']         = false;
                                $save['qo_options'] = json_encode($value);
                                $options_temp[]     = $save;
                                
                            }
                            $unique_hash                     = sha1(date('ymdhis').rand(1000, 9999));
                            $questions_options[$unique_hash] = $options_temp;
                            $question['unique_hash']         = $unique_hash;
                        }
                        break;

                        case $this->__multi_type:
                            $options_temp           = array();
                            $options                = $question['q_options'];
                            $answer_array           = array();
                            $recieved_answer_list   = explode(',', $question['q_answer']);
                            foreach($recieved_answer_list as $recieved_answer_value)
                            {
                                $answer_array[]     = $this->toNum(strtoupper($recieved_answer_value));
                            }
                            $question_answer        = implode(',',$answer_array);
                            $question['q_answer']   = $question_answer;
                           
                            if(!empty($options))
                            {
                                foreach ($options as $op_id => $value ) 
                                {
                                    $save               = array();
                                    $save['id']         = false;
                                    $save['qo_options'] = json_encode($value);
                                    $options_temp[]     = $save;
                               
                                }
                                $unique_hash                     = sha1(date('ymdhis').rand(1000, 9999));
                                $questions_options[$unique_hash] = $options_temp;
                                $question['unique_hash']         = $unique_hash;
                            }
                        break;

                        case $this->__subjective_type:
                            $q_answer = array();
                            unset($question['q_options']);
                        break;

                        case $this->__fill_in_the_blanks:
                            $q_answer = array();
                            unset($question['q_options']);
                        break;

                        
                }
                unset($question['q_subjects']);
                unset($question['q_topics']);
          
                $question['q_question']    = json_encode($question['q_question']);
                $question_explanation      = array();
                $question_explanation['1'] = isset($question['q_explanation'])?($this->get_video_content($question['q_explanation'][1])):'';
                $question['q_explanation'] = (!empty($question_explanation))?json_encode($question_explanation):'';
                $questions_array[]         = $question; 
                $a++;
            }
            if(($subject_validation==false) && ($topic_validation==false))
            {
                $inserted_options              = $this->Course_model->insert_options_bulk($questions_options);
                $questions_temp                = array();
                foreach($questions_array as $hash_questions) 
                { 
                    switch($hash_questions['q_type'])
                    {
                        case $this->__single_type:
                            $q_answers                  = array();
                            $temp_options               = $inserted_options[$hash_questions['unique_hash']];
                            if( !empty($temp_options))
                            {
                                $received_answer_value       = $hash_questions['q_answer'];
                                $q_answers[]                 = isset($temp_options[$received_answer_value-1]) ? $temp_options[$received_answer_value-1] : '';
                                $hash_questions['q_options'] = implode(',',$temp_options);
                            }
                            break;

                            case $this->__multi_type:
                                $q_answers                  = array();
                                $temp_options               = $inserted_options[$hash_questions['unique_hash']];
                                if( !empty($temp_options))
                                {
                                    $received_answer_value  = explode(',',$hash_questions['q_answer']);
                                    foreach ($received_answer_value as $op_id) 
                                    {  
                                        $q_answers[]        = isset($temp_options[$op_id-1]) ? $temp_options[$op_id-1] : '';
                                    }
                                    $hash_questions['q_options'] = implode(',',$temp_options); 
                                }
                            break;

                            case $this->__subjective_type:
                                $q_answers                  = array();
                            break;

                            case $this->__fill_in_the_blanks:
                                $q_answers                  = array();
                            break;  
                    }
                    $correct_answer                         = implode(',', $q_answers);
                    $hash_questions['q_answer']             = $correct_answer; 
                    unset($hash_questions['unique_hash']);
                    
                    if(empty($hash_questions['q_negative_mark']))
                    {
                        $hash_questions['q_negative_mark'] = 0;
                    }
                    
                    $questions_temp[]                       = $hash_questions;
                }

                $inserted_questions = $this->Course_model->insert_questions_bulk($questions_temp);
                if($assessment_id)
                {
                    $keys                                = 'assesment_'.$assessment_id;
                    $assesment_objects                   = array();
                    $assesment_objects['key']            = 'assesment_'.$assessment_id;
                    $assesment_callback                  = 'assesment_details';
                    $assesment_params                    = array();
                    $assesment_params['assesment_id']    = $assessment_id;
                    $assesment_details                   = $this->memcache->get($assesment_objects, $assesment_callback, $assesment_params);
                    $course_id                           = (isset($assesment_details['assesment_details']['a_course_id']))?$assesment_details['assesment_details']['a_course_id']:false;
                    if($course_id)
                    {
                       $this->invalidate_course(array('course_id' => $course_id));
                    }

                    $assesment_questions_array      = array();
                    $assesment_positive_mark        = array();
                    foreach ($inserted_questions as $key => $value) {
                        $save                       = array();
                        $save['aq_assesment_id']    = $assessment_id;
                        $save['aq_question_id']     = $value;
                        $save['aq_positive_mark']   = $questions_array[$key]['q_positive_mark'];
                        $assesment_positive_mark[]  = $questions_array[$key]['q_positive_mark'];
                        $save['aq_negative_mark']   = ($questions_array[$key]['q_negative_mark'] < 0) ? $questions_array[$key]['q_negative_mark'] : -$questions_array[$key]['q_negative_mark'];
                        $save['aq_status']          = '1';
                        $assesment_questions_array[]= $save;
                        $question_ids[]             = $value;
                    }
                    $insert_assessment_questions = $this->Course_model->save_assesment_questions_bulk($assesment_questions_array);
                    $count_questions             = count($assesment_questions_array);
                    $total_mark                  = array_sum($assesment_positive_mark);
                    
                    $assessment_details          = $this->Course_model->assesment(array('select' => 'a_questions,a_mark','lecture_id' => $lecture_id));
                    $assessment_mark             = (isset($assessment_details['a_mark']))?$assessment_details['a_mark']:'0';
                    $assessment_questions        = (isset($assessment_details['a_questions']))?$assessment_details['a_questions']:'0';
                    $save                        = array();
                    $save['a_lecture_id']        = $lecture_id;
                    $save['a_questions']         = $assessment_questions+$count_questions;
                    $save['a_mark']              = $assessment_mark+$total_mark;
                    $this->load->model('Test_model');
                    $this->Test_model->update_assesment($save);

                    $key                    = 'assesment_' . $assessment_id;
                    $objects                = array();
                    $objects['key']         = $key;
                    $assessment_cache       = $this->memcache->get($objects);
                    if (!empty($assessment_cache)) {
                        $this->memcache->delete($key);
                    }
                    $this->deactivate_lecture($lecture_id);
                }
                    //$process['questions'][$a]['q_id'] = $question_id;
                $this->session->set_flashdata('success', sizeof($questions).' Questions imported successfully');
                $user_data                      = array();
                $user_data['user_id']           = $this->__loggedInUser['id'];
                $user_data['username']          = $this->__loggedInUser['us_name'];
                $user_data['useremail']          = $this->__loggedInUser['us_email'];
                $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
                $questions_count                = (sizeof($questions)>1)?sizeof($questions).' questions':sizeof($questions).' question';
                $message_template               = array();
                $message_template['username']   = $this->__loggedInUser['us_name'];
                $message_template['count']      = $questions_count;
                $triggered_activity             = 'question_imported';
                log_activity($triggered_activity, $user_data, $message_template);

                
                $response['question_ids']  = $question_ids;
                $response['success']       = 'true';
                $response['message']       = sizeof($questions).' Questions imported successfully';
                echo json_encode($response);die();
                
            } 
            else 
            {
                $response['success']       = 'false';
                if(($subject_validation==true) && ($topic_validation==true))
                {
                    $response['message']       = 'Subject and topic should not be empty!';
                } 
                else if(($subject_validation==true) && ($topic_validation==false))
                {
                    $response['message']       = 'Subject should not be empty!';
                } 
                else if(($subject_validation==false) && ($topic_validation==true))
                {
                    $response['message']       = 'Topic should not be empty!';
                }
                echo json_encode($response);die();
            }
        }  
    }

    function get_video_content($data)
    {
        $content = '';
        $content = str_replace("<","&lt;",$data);
        $content = str_replace(">","&gt;",$data);
        if (strpos($content, '[youtube]') !== false)
        {
            $url         = $this->get_string_between_tags($content,'[youtube]','[/youtube]');
            $video_frame = $this->replace_youtube_tag($content,$url);
            $video_string= '[youtube]'.$url.'[/youtube]';
            $content     = str_replace($video_string,$video_frame,$data);
        }
        return $content;
    }

    function get_string_between_tags($string, $start, $end)
    {
        $string     = ' ' . $string;
        $ini        = strpos($string, $start);
        if ($ini == 0) return '';
        $ini       += strlen($start);
        $len        = strpos($string, $end, $ini) - $ini;
        $url        = substr($string, $ini, $len);
        return $url;
    }

    function replace_youtube_tag($content,$url)
    {
        $video_id     = explode("?v=", $url); 
        if (empty($video_id[1]))
            $video_id = explode("/v/", $url);
        $video_id     = explode("&", $video_id[1]); 
        $video_id     = $video_id[0];
        $frame        = '<iframe width="560" height="315" src="https://www.youtube.com/embed/'.$video_id.'" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
        return $frame; 
    }

    function quiz_question_upload()
    {
        $lecture_id               = $this->input->post('lecture_id');
        $key                      = 'qimpt'.$this->__loggedInUser['id'];
        $questions_objects        = array();
        $questions_objects['key'] = $key;
        $process                  = $this->memcache->get($questions_objects);
        $process['lecture_id']    = $lecture_id;
        $this->memcache->set($key,$process);
        $response                 = array();
        $response['success']      = true;
        echo json_encode($response);
    }

    function scaniimages(){
        $imageParam               = array('cell_id'=>'J2','question_file'=>'exam_template.xls');
        $this->scanImages($imageParam);
    }

    function scanImages($imageParam = array()){
        $imageDirectory = $this->__imageDirectory;
        $imageString    = '';
        $new_file       = '';
        foreach ($this->__objPHPExcel->getActiveSheet()->getDrawingCollection() as $drawing) {
            $new_file   = '';
            //if($drawing instanceof $this->__PHPExcel_Worksheet_MemoryDrawing)
            {
                $cellID    = $drawing->getCoordinates();
                if($cellID == $imageParam['cell_id']){
                    ob_start();
                    call_user_func(
                        $drawing->getRenderingFunction(),
                        $drawing->getImageResource()
                    );
                    $imageContents = ob_get_contents();
                    ob_end_clean();

                    $filetype = $drawing->getMimeType();
                    $filename = md5(microtime());
                    switch ($filetype) {

                        case 'image/gif':
                            $image = imagecreatefromstring($imageContents);
                            imagegif($image,$_SERVER['DOCUMENT_ROOT'].'/'.$imageDirectory.'/'.$filename.".gif");
                            $new_file = "$filename.gif";
                            break;

                        case 'image/jpeg':
                            $image = imagecreatefromstring($imageContents);
                            imagejpeg($image,$_SERVER['DOCUMENT_ROOT'].'/'.$imageDirectory.'/'.$filename.".jpeg");
                            $new_file = "$filename.jpeg";
                            break;

                        case 'image/png':
                            $image = imagecreatefromstring($imageContents);
                            imagepng($image,$_SERVER['DOCUMENT_ROOT'].'/'.$imageDirectory.'/'.$filename.".png");
                            $new_file = "$filename.png";
                            break;

                        default:
                            continue 2;
                    }
                }
            }
            if($new_file != ''){
                $imageString   .= '<img src="'.question_path().$imageParam['question_file'].'/'.$new_file.'">';
            }
        }
        return $imageString;
    }
    
    function toAlpha($number)
    { 
        $alphabet = range('A','Z');
        $number--;
        $count = count($alphabet);
        $alpha = '';
        if($number <= $count)
        {
            return $alphabet[$number];
        }
        while($number > 0){
            $modulo     = ($number - 1) % $count;
            $alpha      = $alphabet[$modulo].$alpha;
            $number     = floor((($number - $modulo) / $count));
        }
        return $alpha;
    }
    function toNum($data) {
        $alphabet = array( 'A', 'B', 'C', 'D', 'E',
                           'F', 'G', 'H', 'I', 'J',
                           'K', 'L', 'M', 'N', 'O',
                           'P', 'Q', 'R', 'S', 'T',
                           'U', 'V', 'W', 'X', 'Y',
                           'Z'
                           );
        $alpha_flip = array_flip($alphabet);
        $return_value = -1;
        $length = strlen($data);
        for ($i = 0; $i < $length; $i++) {
            $return_value += isset($alpha_flip[$data[$i]]) ? ($alpha_flip[$data[$i]] + 1) * pow(26, ($length - $i - 1))+1 : '';
        }
        return $return_value;
    }
    
    
    
    function question($id=false, $assessment_id = false)
    { 
        // if($_POST)
        // {
        //     echo '<pre>';print_r($_POST);die();
        // }
         // get next question id
         $next_id            = 0;

         if( !empty($_SESSION['question_param']) && $id )
         {
             $q_param                 = $_SESSION['question_param'];
             $q_param['count']        = false;
             $q_param['limit']        = 1;
             $q_param['next_id_of']   = $id;
 
             $next_id                 = $this->Generate_test_model->questions($q_param);
             // echo "<pre>"; print_r($next_id);die;
             $next_id                 = (!empty($next_id))?$next_id[0]['id']:0;
 
         }
        $data           = array(); 
        $data['title']  = 'Question Form';
        
        $data['id']                 = $id;
        $data['assessment_id']      = $assessment_id;
        //===============processing web language====================
        $active_web_language = $this->session->userdata('active_web_language');
        if(!$active_web_language)
        {
            $active_web_language = '1';
            $this->session->set_userdata('active_web_language', $active_web_language);
        }
        $data['active_web_language'] = $active_web_language;
        
        $this->load->model('Language_model');
        $data['web_languages'] = $this->Language_model->languages();
        //==============End of processing web language===============
        
        $data['question_types']     = array('Single choice' => '1', 'Multiple choice' => '2', 'Subjective type' => '3', 'Fill in the blanks' => '4');
        $data['difficulty']         = array('Easy' => '1', 'Medium' => '2', 'Hard' => '3');
        $data['selected']           = 'selected="selected"';
        
        $data['q_type']             = $this->input->post('q_type');
        $data['q_difficulty']       = $this->input->post('q_difficulty');
        $data['q_category']         = $this->input->post('q_category');
        $data['q_subject']          = $this->input->post('q_subject');
        $data['q_topic']            = $this->input->post('q_topic');
        $data['q_positive_mark']    = $this->input->post('q_positive_mark');
        $data['q_negative_mark']    = ($this->input->post('q_negative_mark') < 0) ? $this->input->post('q_negative_mark') : (-$this->input->post('q_negative_mark'));
        // echo $data['q_negative_mark'];die;
        $data['q_tags']             = $this->input->post('q_tags');
        $data['q_question']         = $this->input->post('q_question');
        $data['q_explanation']      = $this->input->post('q_explanation');
        $data['q_options']          = $this->input->post('q_options');
        $data['q_answer']           = $this->input->post('q_answer');
        $data['from_test_manager']  = '';
        $data['course_categories']  = $this->Category_model->categories(array('not_deleted'=>true));
        $data['question_subjects']  = array();
        $data['question_topics']  = array();

        if(isset($_SERVER['HTTP_REFERER']))
        {
            $data['from_test_manager']  = (strpos($_SERVER['HTTP_REFERER'], 'test_manager/test_questions') !== false)?$_SERVER['HTTP_REFERER']:'';        
        }
        if($data['from_test_manager']=='')
        {
            $data['from_test_manager'] = $this->input->post('from_test_manager');
        }

        $existing_options           = array();
        
        if($id)
        {            
            $question                   = $this->Generate_test_model->question(array('id' => $id));
            $data['q_type']             = $question['q_type'];
            $data['q_code']             = $question['q_code'];
            $data['q_pending_status']   = $question['q_pending_status'];
            $data['q_difficulty']       = $question['q_difficulty'];
            $category                   = $this->Category_model->category(array('id'=>$question['q_category']));
            $data['q_category']         = $question['q_category'];
            $subject                    = $this->Category_model->subject(array('id'=>$question['q_subject']));
            $data['q_subject']          = $question['q_subject'];
            $topic                      = $this->Category_model->topic(array('id'=>$question['q_topic']));
            $data['q_topic']            = $question['q_topic'];
            $data['q_positive_mark']    = $question['q_positive_mark'];
            $data['q_negative_mark']    = ($question['q_negative_mark'] < 0) ? $question['q_negative_mark'] : -$question['q_negative_mark'];
            $data['course_categories']  = $this->Category_model->categories(array('not_deleted'=>true));
            
            $data['question_subjects']  = $this->Category_model->subjects(array('category_id'=>$question['q_category'],'qs_deleted'=>true));
            $data['question_topics']    = $this->Category_model->topics(array('category_id'=>$question['q_category'],'subject_id'=>$question['q_subject'],'qt_deleted'=>true));
            
            $tags                       = '';
            if($question['q_tags'])
            {
                $tags                   = $this->Category_model->tags(array('ids'=>$question['q_tags']));  
                if(!empty($tags))
                {
                    $tags_temp      = array();
                    foreach ($tags as $tag)
                    {
                       $tags_temp[] = $tag['tg_name'];
                    }
                    $tags           = implode(',', $tags_temp);
                }
            }
            $data['q_tags']             = $tags;
            
            $data['q_question']         = $question['q_question'];
            $data['q_explanation']      = $question['q_explanation'];

            $data['q_options']          = $this->Course_model->options(array('q_answer' => $question['q_options']));
            $existing_options           = $data['q_options'];
            $data['q_answer']           = ($question['q_answer'])?explode(',', $question['q_answer']):array();
        }
        if($assessment_id)
        {
            $data['assessment']          = $this->Course_model->assesment(array('assessment_id' => $assessment_id));
        }
        $data['next_id']                 = $next_id; 
        $this->load->library('form_validation');
        $this->form_validation->set_rules('q_question', lang('question'), 'trim|required');
        if ($this->form_validation->run() == false)
        {
            $data['error'] = validation_errors();
            $this->load->view($this->config->item('admin_folder').'/question_bank_form', $data);
        }
        else
        {
            // var_dump($_POST);die;

            $save_question                       = array();
            $save_question['id']                 = $id;
            $save_question['q_type']             = $this->input->post('q_type');
            $save_question['q_difficulty']       = $this->input->post('q_difficulty');
            
            //processing tags
            $tags                 = '';
            $tags_recieved        = $this->input->post('q_tags');
            $tags_recieved        = explode(',', $tags_recieved);
            if(!empty($tags_recieved))
            {
                $tags          = array();
                foreach($tags_recieved as $r_tag)
                {
                    $r_tag     = strtolower($r_tag);
                    $tag_exist = $this->Category_model->tag(array('tag_name' => $r_tag));
                    if(!empty($tag_exist))
                    {
                        $tags[] = $tag_exist['id'];
                    }
                    else
                    {
                        $save_tag                   = array();
                        $save_tag['id']             = false;
                        $save_tag['tg_name']        = $r_tag;
                        $save_tag['tg_account_id']  = config_item('id');
                        $tags[] = $this->Category_model->save_tag($save_tag);
                    }
                }
                $tags = implode(',', $tags);
            }
            $save_question['q_tags']        = $tags;
            //end processing tags

            $category                       = $this->input->post('q_category');
            $save_question['q_category']    = ($category != '')?$category:'1';
                        
            //processing subject
            $subject     = $this->input->post('q_subject');
            $save_question['q_subject']     = ($subject != '')?$subject:'1';  
            //end of precessing subject


            //processing subject
            $topic                          = $this->input->post('q_topic');
            $topic                          = ($topic != '')?$topic:'1';
            $save_question['q_topic']       = $topic;   
            //end of precessing subject

            $save_question['q_positive_mark']    = $this->input->post('q_positive_mark');
            $save_question['q_negative_mark']    = ($this->input->post('q_negative_mark') < 0) ? $this->input->post('q_negative_mark') : -$this->input->post('q_negative_mark');
            
            
            //processing question
            $old_question = array();
            $new_question = $this->input->post('q_question',false);
            if(isset($question['q_question']))
            {
                $old_question = (array)json_decode($question['q_question']);
                if(!(json_last_error() == JSON_ERROR_NONE))
                {
                    $old_question = array();
                }
            }
            //$old_question[$active_web_language] = addslashes($new_question);
            $old_question[$active_web_language] = ($new_question);
            $save_question['q_question']         = json_encode($old_question,JSON_UNESCAPED_UNICODE);
            //end
            
            //processing explanation
            $old_explanation = array();
            $new_explanation = $this->get_video_content($this->input->post('q_explanation'));
            //echo '<pre>'; print_r($this->input->post());die;
            if(isset($question['q_explanation']))
            {
                $old_explanation = (array)json_decode($question['q_explanation']);
                if(!(json_last_error() == JSON_ERROR_NONE))
                {
                    $old_explanation = array();
                }
            }
            //$old_explanation[$active_web_language] = addslashes($new_explanation);
            $old_explanation[$active_web_language] = ($new_explanation);
            $save_question['q_explanation']         = json_encode($old_explanation);
            //end
            
            $save_question['action_by']          = $this->auth->get_current_admin('id');
            $save_question['q_account_id']       = $this->config->item('id');
            $save_question['action_id']          = 1;
            $save_question['created_date']       = date('Y-m-d H:i:s');
            $save_question['updated_date']       = date('Y-m-d H:i:s');

            $save_question['q_options']          = '';
            $save_question['q_answer']           = '';
            //=============================processing question objects======================
            $existing_options_temp = array();
            if(!empty($existing_options))
            {
                foreach ($existing_options as $e_option)
                {
                    $existing_options_temp[$e_option['id']] = $e_option;
                }
            }
            $existing_options = $existing_options_temp;
            
            /*remove the deleted options*/
            $removed_options = json_decode($this->input->post('removed_options'));
            if( !empty($removed_options))
            {
                foreach ($removed_options as $option) {
                    $this->Course_model->delete_option($option);
                }
            }
            /*end*/
            
            /* update the existing options*/
            $options         = $this->input->post('option',false);
            $recieved_answer = $this->input->post('answer',false);
            $q_options       = array();
            $q_answer        = array();
            
            switch($save_question['q_type'])
            {
                case $this->__single_type:
                    if( !empty($options))
                    {
                        foreach ($options as $op_id => $value ) 
                        {
                            $save               = array();
                            $save['id']         = $op_id;
                            //processing answer
                            $old_answer = array();
                            $new_answer = $value;
                            if(isset($existing_options[$op_id]))
                            {
                                $old_answer = (array)json_decode($existing_options[$op_id]['qo_options']);
                                if(!(json_last_error() == JSON_ERROR_NONE))
                                {
                                    $old_answer = array();
                                }
                            }
                            //$old_answer[$active_web_language]   = addslashes($new_answer);
                            $old_answer[$active_web_language]   = ($new_answer);
                            $save['qo_options']                 = json_encode($old_answer);
                            //end fo processing answer
                            $this->Course_model->save_option($save);
                            $q_options[]        = $op_id;
                            if($op_id == $recieved_answer)
                            {
                                $q_answer[] = $op_id;
                            }
                        }
                    }
                break;
                case $this->__multi_type:
                    if( !empty($options))
                    {
                        foreach ($options as $op_id => $value ) 
                        {
                            $save               = array();
                            $save['id']         = $op_id;
                            //processing answer
                            $old_answer = array();
                            $new_answer = $value;
                            if(isset($existing_options[$op_id]))
                            {
                                $old_answer = (array)json_decode($existing_options[$op_id]['qo_options']);
                                if(!(json_last_error() == JSON_ERROR_NONE))
                                {
                                    $old_answer = array();
                                }
                            }
                            //$old_answer[$active_web_language]   = addslashes($new_answer);
                            $old_answer[$active_web_language]   = ($new_answer);
                            $save['qo_options']                 = json_encode($old_answer);
                            //end fo processing answer
                            $this->Course_model->save_option($save);
                            $q_options[]        = $op_id;
                            if(in_array($op_id, $recieved_answer))
                            {
                                $q_answer[] = $op_id;
                            }
                        }
                    }
                break;
                case $this->__subjective_type:
                if(!empty($save_question['q_options']))
                {
                    foreach ($save_question['q_options'] as $option) 
                    {
                        $this->Course_model->delete_option($option['id']);
                    }                   
                }
                break;
                case $this->__fill_in_the_blanks:
                if(!empty($save_question['q_options']))
                {
                    foreach ($save_question['q_options'] as $option) 
                    {
                        $this->Course_model->delete_option($option['id']);
                    }                   
                }
                break;

                
            }
            /*End*/
            
            /* insert the new options*/
            $options         = $this->input->post('option_new',false);
            $recieved_answer = $this->input->post('answer_new',false);
            
            switch($save_question['q_type'])
            {
                case $this->__single_type:
                    if( !empty($options))
                    {
                        foreach ($options as $op_id => $value ) 
                        {
                            $save               = array();
                            $save['id']         = false;
                            //$save['qo_options'] = json_encode(array($active_web_language=>addslashes($value)));
                            $save['qo_options'] = json_encode(array($active_web_language=>($value)));
                            $option_id          = $this->Course_model->save_option($save);
                            $q_options[]        = $option_id;
                            if($op_id == $recieved_answer)
                            {
                                    $q_answer[] = $option_id;
                            }
                        }
                    }
                break;
                case $this->__multi_type:
                    if( !empty($options))
                    {
                        foreach ($options as $op_id => $value ) 
                        {
                            $save               = array();
                            $save['id']         = false;
                            //$save['qo_options'] = json_encode(array($active_web_language=>addslashes($value)));
                            $save['qo_options'] = json_encode(array($active_web_language=>($value)));
                            $option_id          = $this->Course_model->save_option($save);
                            $q_options[]        = $option_id;
                            if(in_array($op_id, $recieved_answer))
                            {
                                $q_answer[]     = $option_id;
                            }
                        }
                    }
                break;
                case $this->__subjective_type:
                break;
                case $this->__fill_in_the_blanks:
                break;
                
            }
            /*End*/
            //echo '<pre>'; print_r($save_question);die;
            //=====================================End===================================
            
            //===============processing default values====================
            $default_field_values                    = array();
            $default_field_values['q_type']          = $save_question['q_type'];
            $default_field_values['q_difficulty']    = $save_question['q_difficulty'];
            $default_field_values['q_category']      = $this->input->post('q_category');
            $default_field_values['q_subject']       = $this->input->post('q_subject');
            $default_field_values['q_topic']         = $this->input->post('q_topic');
            $default_field_values['q_positive_mark'] = $this->input->post('q_positive_mark');
            $default_field_values['q_negative_mark'] = ($this->input->post('q_negative_mark') < 0) ? $this->input->post('q_negative_mark') : $this->input->post('q_negative_mark');
            $default_field_values['q_tags']          = $this->input->post('q_tags');
            $default_field_values['question_subjects']  = $this->Category_model->subjects(array('category_id'=>$default_field_values['q_category'],'qs_deleted'=>true));
            $default_field_values['question_topics']    = $this->Category_model->topics(array('category_id'=>$default_field_values['q_category'],'subject_id'=>$default_field_values['q_subject'],'qt_deleted'=>true));
            $this->session->set_userdata('default_field_values', $default_field_values);
            //==============End of processing default values===============

            
            $save_question['q_options']     = implode(',', $q_options);
            $save_question['q_answer']      = implode(',', $q_answer);
            $save_question['q_pending_status'] = $this->input->post('q_pending_status');
            //echo '<pre>'; print_r($save_question); die;
            $question_id                    = $this->Course_model->save_question($save_question);
   //echo '<pre>'; print_r($question_id); die;
            /*Log creation*/
            $user_data                      = array();
            $user_data['user_id']           = $this->__loggedInUser['id'];
            $user_data['username']          = $this->__loggedInUser['us_name'];
            $user_data['useremail']          = $this->__loggedInUser['us_email'];
            $user_data['user_type']         = $this->__loggedInUser['us_role_id']; ;
            $message_template               = array();
            $message_template['username']   = $this->__loggedInUser['us_name'];
            if($id){
                $triggered_activity         = 'question_updated';
            } else {
                $triggered_activity         = 'question_added';
            }
            log_activity($triggered_activity, $user_data, $message_template);
            //saving assesment and questio nconnection
            $assessment_url                 = '';
            if($assessment_id)
            {
                $assessment_url = '/'.$assessment_id;
                $save                       = array();
                $save['assesment_id']       = $assessment_id;
                $save['question_id']        = $question_id;
                $save['positive_mark']      = $save_question['q_positive_mark'];
                $save['negative_mark']      = ($save_question['q_negative_mark'] < 0) ? $save_question['q_negative_mark'] : $save_question['q_negative_mark'];
                $this->Course_model->save_assesment_question($save);
                
                $assesment_questions_deatail= $this->Course_model->get_assessment_questions(array('select'=>'count(*) as count,SUM(`aq_positive_mark`) as total_mark','assesment_id'=>$assessment_id));
                $count_questions            = $assesment_questions_deatail[0]['count'];
                $total_mark                 = $assesment_questions_deatail[0]['total_mark']; 
                $save                       = array();
                $assesment_lecture_id       = $data['assessment']['a_lecture_id'];
                $save['a_lecture_id']       = $assesment_lecture_id;
                $save['a_questions']        = $count_questions;
                $save['a_mark']             = $total_mark;
                $this->load->model('Test_model');
                $this->Test_model->update_assesment($save);

                /*Deactivate lecture*/
                $this->deactivate_lecture($assesment_lecture_id);
                /*End deactivate lecture*/

                /* Deactive  course  */
                $keys                                = 'assesment_'.$assessment_id;
                $assesment_objects                   = array();
                $assesment_objects['key']            = 'assesment_'.$assessment_id;
                $assesment_callback                  = 'assesment_details';
                $assesment_params                    = array();
                $assesment_params['assesment_id']    = $assessment_id;
                $assesment_details                   = $this->memcache->get($assesment_objects, $assesment_callback, $assesment_params);
                $course_id                           = (isset($assesment_details['assesment_details']['a_course_id']))?$assesment_details['assesment_details']['a_course_id']:false;
                if($course_id)
                {
                    $this->invalidate_course(array('course_id' => $course_id));
                }
                 /* Deactive  course  */
            }
            //End
            $this->session->set_flashdata('message', 'Question saved successfully.');
            $redirect = $this->input->post('redirect');
            switch ($redirect)
            {
                case "save":
                    $redirect_url = admin_url('generate_test');
                    /*if($data['from_test_manager'])
                    {
                        $redirect_url = $data['from_test_manager'];
                    }*/
                    if($assessment_id)
                    {
                        //$redirect_url = admin_url('test_manager/test_questions/'. base64_encode($assessment_id));
                        $redirect_url = admin_url('test_manager/test_questions/'. base64_encode($data['assessment']['a_lecture_id']));
                    }
                    redirect($redirect_url);
                    break;
                case "save_and_new":
                    redirect(admin_url('generate_test/question/0'.$assessment_url));
                    break;
                    case "save_and_next":
                    redirect(admin_url('generate_test/question/'.$next_id));
                    break;
                default:
                    redirect(admin_url('generate_test'));                
                    break;
            }
        }
    }
    

    function question_category()
    {
        $category_name          = $this->input->post('category');
        $response               = array();
        $response['error']      = false;
        $response['message']    = 'Listing categories';
        $category               = $this->Category_model->categories(array('limit' => 5, 'status' => '1', 'not_deleted'=>true, 'name' => $category_name));
        $response['category']   = $category;
        echo json_encode($response);
    }
    function question_subject()
    {
        $category_name          = $this->input->post('category');
        $category               = $this->Category_model->category(array('status' => '1', 'not_deleted'=>true, 'category_name' => $category_name));
        $response               = array();
        $response['subject']    = array();
        $response['error']      = false;
        $response['message']    = 'Listing subjects';
        //echo '<pre>'; print_r($category_name);print_r($category);die;
        if(!empty($category))
        {
            $subject_name           = $this->input->post('subject');
            $subject                = $this->Category_model->subjects(array('limit' => 5, 'subject_name' => $subject_name, 'category_id' => $category['id']));
            $response['subject']    = $subject;    
        }
        echo json_encode($response);
    }
    function question_topic()
    {
        $response               = array();
        $response['error']      = false;
        $response['message']    = 'Listing topics';
        $response['topic']      = array();

        $category_name          = $this->input->post('category');
        $category               = $this->Category_model->category(array('status' => '1', 'not_deleted'=>true, 'category_name' => $category_name));

        $subject_name           = $this->input->post('subject');
        $subject                = $this->Category_model->subject(array('subject_name' => $subject_name));

        if(!empty($category) && !empty($subject))
        {
            $topic_name             = $this->input->post('topic');
            $topic                  = $this->Category_model->topics(array('limit' => 5, 'category_id' => $category['id'], 'subject_id' => $subject['id'], 'topic_name' => $topic_name));
            $response['topic']      = $topic;
        }
        echo json_encode($response);
    }   
    
    function get_category_subjects()
    {
        $category_id = ($this->input->post('category_id'))!='all'?$this->input->post('category_id'):0;
        $response               = array();
        $response['subjects']   = array();
        $response['error']      = false;
        $response['message']    = 'Listing subjects';
        //echo '<pre>'; print_r($category_name);print_r($category);die;
        if($category_id)
        {
            $subject                = $this->Category_model->subjects(array('select' =>'questions_subject.id,questions_subject.qs_subject_name','category_id' => $category_id,'qs_deleted' => true));
            $response['subjects']   = $subject;    
        }
        echo json_encode($response);
    }
    
    function get_category_topics()
    {
        $category_id = $this->input->post('category_id');
        $subject_id  = $this->input->post('subject_id');
        
        $response               = array();
        $response['error']      = false;
        $response['message']    = 'Listing topics';
        $response['topics']     = array();
        
        if(($category_id) && ($subject_id))
        {
            $topic                  = $this->Category_model->topics(array('select'=>'questions_topic.id,questions_topic.qt_topic_name','category_id' => $category_id, 'subject_id' => $subject_id,'qt_deleted'=>true));
            $response['topics']     = $topic;
        }
        echo json_encode($response);
    }
}
?>
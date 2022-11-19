<?php
class Backup extends CI_Controller 
{
    private $__builder_view;
    public $__lecture_type_icons;
    public $__lecture_type_array;
    public $__lecture_type_keys_array;
    public $__report_type_array;
    public $__document_types            = array('doc', 'odt', 'docx', 'xls', 'pdf', 'ppt','xlsx','xls','ods','odp','pptx');
    public $__scorm_types               = array('zip');
    public $__audio_types               = array('mp3');
    public $__video_types               = array('mp4', 'flv', 'avi', 'f4v', 'mp3');
    private $__video_lecture_type       = 1;
    private $__document_lecture_type    = 2;
    private $__scorm_lecture_type       = 10;
    private $__cisco_lecture_type       = 11;
    public $access_content_types;
    private $__converter_url            = 'https://streaming.wincentreonline.com/index.php/converter/launch_conversion';
    private $__response_url;
    private $__database_api;
    private $__s3_path                  = 'https://multiuploadtest.s3.amazonaws.com/';
    
    function __construct()
    {
        parent::__construct();
        $this->__admin                  = $this->auth->get_current_user_session('admin');
        $skip_login                     = array('upload_video', 'delete_video', 'process_copy_queue', 'copy_file');
        
        if(empty($this->__admin) && !in_array($this->router->fetch_method(), $skip_login))
        {
            redirect('login');
        }
        
        $this->load->model(array('Backup_model'));

        $access_method                  = ($this->__admin['rl_full_course'] == '1')?'get_permission':'get_permission_course';
        $this->course_id                = ($this->uri->segment(4) != null) ? $this->uri->segment(4) : 0;
        $this->privilege                = array('view' => 1, 'add' => 2, 'edit' => 3, 'delete' => 4);
        $this->backup_privilege         = $this->accesspermission->$access_method(array('role_id' => $this->__admin['role_id'],'module' => 'backups','course_id'=>$this->course_id,'user_id'=> $this->__admin['id']));
        $this->course_content_privilege = $this->accesspermission->$access_method(array('role_id' => $this->__admin['role_id'], 'module' => 'course_content','course_id'=>$this->course_id,'user_id'=> $this->__admin['id']));
        if(!in_array($this->privilege['view'], $this->backup_privilege) && !in_array($this->router->fetch_method(), array('upload_video', 'delete_video', 'delete', 'backups')))
        {
            if(empty($this->__admin) && !in_array($this->router->fetch_method(), $skip_login))
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
                                              '8' => 'descriptive_test',
                                              '9' => 'recorded_videos',
                                              '10' => 'scorm',
                                              '11' => 'cisco_recorded_videos',
                                              '12' => 'audio',
                                              '13' => 'survey',
                                              '14' => 'certificate'
                                       );
        $this->__lecture_type_keys_array  = array();
        foreach ($this->__lecture_type_array as $id => $type) {
            $this->__lecture_type_keys_array[$type] = $id;
        }
        
    }
    /*
    purpose     : backup initialize
    params      : none
    usage-in    : inside Courses(Admin)
    edited      : none
    */

    function backup_initialize()
    { //echo redactor_upload_path(array('course_id' => 12));die;
        
        $response                   = array();
        $response['error']          = false;
        $response['message']        = 'Initialized course backup';
        $course_id                  = $this->input->post('course_id');
        $course                     = $this->Backup_model->course(array('course_id' => $course_id));
        
        if(empty($course))
        {
            $response['error']      = true;
            $response['message']    = 'Invalid course id';
        }
        $this->json_encode($response);
    }
    
    /*
    purpose     : backup lectures,sections,surveys,questions
    params      : none
    usage-in    : inside Courses(Admin)
    edited      : none
    */
    function backup_database()
    {
        $this->load->model(array('Course_model'));
        $has_s3_enabled                                 = $this->settings->setting('has_s3');
        $as_siteadmin_value                             = $has_s3_enabled['as_siteadmin_value'];
        $response                                       = array();
        $response['error']                              = false;
        $response['message']                            = 'Course is valid';
        $course_id                                      = $this->input->post('course_id');
        $lastBackupId                                   = $this->Backup_model->getLastCourseBackupId();
        $backup_id                                      = !empty($lastBackupId) ? $lastBackupId->id + 1 : 1;
        $course                                         = $this->Backup_model->course(array('course_id' => $course_id));
        
        if(empty($course))
        {
            $response['error']                          = true;
            $response['message']                        = 'Invalid course id';
            $this->json_encode($response);
        }
        //Extracting course lectures
        $lectures                                       = $this->Backup_model->course_lectures(array('course_id' => $course_id));
        $descriptive_tests                              = array();
        $dt_details                                     = $this->Course_model->assignment_details(array('course_id' => $course_id));

        if(!empty($dt_details))
        {
            foreach($dt_details as $details)
            {
                $descriptive_tests[$details['dt_lecture_id']] = $details;
            }
        }

        //echo '<pre>';print_r($lectures);die;
        $course['descriptive_tests']                    = $descriptive_tests;
        $course['lectures']                             = $lectures;
        $course['sections']                             = array();
        $course['quizes']                               = array();
        //End
        //extracting section details


        if($has_s3_enabled && ($has_s3_enabled['as_siteadmin_value']))
        {
            $s3_settings                                = $has_s3_enabled['as_setting_value']['setting_value'];
            $s3_bucket_url                              = '';//"https://".$s3_settings->s3_bucket.".s3.amazonaws.com/";
            $backup_folder_root                         = $s3_bucket_url.course_backup_upload_path();
            $course_source                              = $s3_bucket_url.course_assets_uploaded_path(array('course_id' => $course_id));
            $course_destination                         = $backup_folder_root.'backup_'.$course_id.'-'.$backup_id.'/';
        }
        else
        {
            $backup_folder_root                         = course_backup_upload_path();
            $this->make_directory($backup_folder_root);
            $course_source                              = course_assets_uploaded_path(array('course_id' => $course_id));
            $course_destination                         = $backup_folder_root.'backup_'.$course_id.'-'.$backup_id.'/';
            $this->make_directory($course_destination);
            $this->make_directory($course_destination.$course_id);
        }



        $sections                                       = $this->Backup_model->course_sections(array('course_id' => $course_id));
        
        if(!empty($sections))
        {
            $files_to_copy                              = array();
            $lecture_source                             = $this->get_upload_config('section_image', $course_id);
            $lecture_source_path                        = $lecture_source['upload_path'];
            $lecture_destination                        = $this->get_backup_config('section_image', $course_id);
            $lecture_destination_path                   = $course_destination.$lecture_destination['upload_path'];
            $lecture_destination_path                   = $lecture_destination_path;
            
            if(!$as_siteadmin_value)
            {
                $this->make_directory($lecture_destination_path);
            }

            foreach($sections as $section)
            {
                $course['sections'][$section['id']]                 = $section;

                if($section['s_image'] != 'default-section.jpg')
                {
                    $ext                                            = explode('.',$section['s_image']);
                    if(isset($ext[1]))
                    {
                        $file_name                                  = $ext[0];
                        $ext                                        = explode('?',$ext[1]);
                        $ext                                        = $ext[0];
                        $file_name_full                             = $file_name.'.'.$ext; 
                        $lecture_source_path1                       = $lecture_source_path.$file_name_full;

                        $files_to_copy_array                        = array();
                        $files_to_copy_array['source']              = $lecture_source_path1;
                        $files_to_copy_array['destination']         = $lecture_destination_path.$file_name_full;
                        $files_to_copy_array['status']              = '0';
                        $files_to_copy_array['lecture_id']          = '0';
                        $files_to_copy[]                            = $files_to_copy_array;

                        $file_name_full                             = $file_name.'_85x85.'.$ext; 
                        $lecture_source_path2                       = $lecture_source_path.$file_name_full;
                        $files_to_copy_array['source']              = $lecture_source_path2;
                        $files_to_copy_array['destination']         = $lecture_destination_path.$file_name_full;
                        $files_to_copy_array['status']              = '0';
                        $files_to_copy_array['lecture_id']          = '0';
                        $files_to_copy[]                            = $files_to_copy_array;

                        $file_name_full                             = $file_name.'_300x160.'.$ext; 
                        $lecture_source_path3                       = $lecture_source_path.$file_name_full;
                        $files_to_copy_array['source']              = $lecture_source_path3;
                        $files_to_copy_array['destination']         = $lecture_destination_path.$file_name_full;
                        $files_to_copy_array['status']              = '0';
                        $files_to_copy_array['lecture_id']          = '0';
                        $files_to_copy[]                            = $files_to_copy_array;

                    }
                }
            }
        }
        //End
        //Extracting lectures quiz and its assets
        $quizes                                                     = $this->Backup_model->course_quizes(array('course_id' => $course_id));
        $quiz_ids                                                   = array();
        if(!empty($quizes))
        {
            foreach($quizes as $quiz)
            {
                // unset($quiz['a_instructions']);
                $course['quizes'][$quiz['id']]                      = $quiz;
                $quiz_ids[]                                         = $quiz['id'];
            }
            $questions                                              = $this->Backup_model->course_quiz_questions(array('quiz_ids' => $quiz_ids));
            if(!empty($questions))
            {
                foreach($questions as $question)
                {
                    $course['quizes'][$question['aq_assesment_id']]['questions'][] = $question;
                }
            }
        }
        //end
        //Extracting lectures survey and its responses
        $surveys                                                    = $this->Backup_model->course_surveys(array('course_id' => $course_id));
        $survey_ids                                                 = array();
        if(!empty($surveys))
        {
            foreach($surveys as $survey)
            {
                $course['surveys'][$survey['id']]                   = $survey;
                $survey_ids[]                                       = $survey['id'];
            }
            $questions                                              = $this->Backup_model->course_survey_questions(array('survey_ids' => $survey_ids));
            if(!empty($questions))
            {
                foreach($questions as $question)
                {
                    $course['surveys'][$question['sq_survey_id']]['questions'][] = $question;
                }
            }
        }

        if(!empty($files_to_copy))
        {
            $this->memcache->set('section_images', $files_to_copy); 
        }
        //End
        $backup                                                     = array();
        $backup['id']                                               = $backup_id;
        $backup['cbk_course_id']                                    = $course_id;
        $backup['cbk_course_name']                                  = $course['cb_title'];
        $backup['cbk_course_code']                                  = $course['cb_code'];
        $backup['cbk_account_id']                                   = config_item('id');
        $backup['cbk_assets']                                       = json_encode($course);
        $response['backup_id']                                      = $this->Backup_model->backup_section($backup);
        $response['course_id']                                      = $course_id;
        $this->json_encode($response);
    }
    
    /*
    purpose     : backup assets,move all contents
    params      : none
    usage-in    : inside Courses(Admin)
    edited      : Mujeeb
    */
    function backup_assets()
    {
        $response                                                   = array();
        $response['error']                                          = false;
        $response['message']                                        = 'Course assets copied successfully';
        
        $course_id                                                  = $this->input->post('course_id');
        $backup_id                                                  = $this->input->post('backup_id');
        $course                                                     = $this->Backup_model->course(array('course_id' => $course_id));
        
        if(empty($course))
        {
            $response['error']                                      = true;
            $response['message']                                    = 'Invalid course id';
            $this->json_encode($response);
        }

        $has_s3_enabled                                             = $this->settings->setting('has_s3');
        $content_security                                           = $this->settings->setting('has_content_security');
        $content_security_status                                    = $content_security['as_setting_value']['setting_value']->content_security_status;
        $as_siteadmin_value                                         = $has_s3_enabled['as_siteadmin_value'];
        if($has_s3_enabled && ($has_s3_enabled['as_siteadmin_value']))
        {
            $s3_settings                                            = $has_s3_enabled['as_setting_value']['setting_value'];
            $s3_bucket_url                                          = '';//"https://".$s3_settings->s3_bucket.".s3.amazonaws.com/";
            $backup_folder_root                                     = $s3_bucket_url.course_backup_upload_path();
            $course_source                                          = $s3_bucket_url.course_assets_uploaded_path(array('course_id' => $course_id));
            $course_destination                                     = $backup_folder_root.'backup_'.$course_id.'-'.$backup_id.'/';
            $redactor_source                                        = redactor_upload_path(array('course_id' => $course_id));
            $redactor_destination                                   = $backup_folder_root.'backup_'.$course_id.'-'.$backup_id.'/';
            //$course_size                        = $this->s3_object_info($course_source);
            //$redactor_size                      = $this->s3_object_info($redactor_source);
            //print_r($this->s3ObjectInfo(array('object' => $course_source)));die; 

            $total_size                                             = 'aws';//$this->real_size($course_size+$redactor_size);
            
        }
        else
        {
            $backup_folder_root                                     = course_backup_upload_path();
            $this->make_directory($backup_folder_root);

            $course_source                                          = course_assets_uploaded_path(array('course_id' => $course_id));
            $course_destination                                     = $backup_folder_root.'backup_'.$course_id.'-'.$backup_id.'/';
            $this->make_directory($course_destination);
            $this->make_directory($course_destination.$course_id);
            $redactor_source                                        = redactor_upload_path(array('course_id' => $course_id));
            $redactor_destination                                   = $backup_folder_root.'backup_'.$course_id.'-'.$backup_id.'/redactor/';
            $this->make_directory($redactor_destination);
            $supportfile_backup_path                                = $course_destination.$course_id.'/support_files/';
            $this->make_directory($supportfile_backup_path);
            $documents_backup_path                                  = $course_destination.$course_id.'/documents/';
            $this->make_directory($documents_backup_path);
            $audio_backup_path                                      = $course_destination.$course_id.'/audio/';
            $this->make_directory($audio_backup_path);
            $scorm_backup_path                                      = $course_destination.$course_id.'/scorm/';
            $this->make_directory($scorm_backup_path);
            
            //Updating backup size scorm
            $course_size                                            = $this->folder_size($course_source);
            $redactor_size                                          = $this->folder_size($redactor_source);
            $total_size                                             = $this->real_size($course_size+$redactor_size);
        }

            
            $this->load->model('Course_model');
            $files_to_copy                                          = array();
            $lectures                                               = $this->Course_model->lectures(array('course_id' => $course_id, 'not_deleted' => true, 'select' => 'id, cl_org_file_name, cl_course_id, cl_lecture_type, cl_support_files, cl_lecture_image'));
            
            foreach($lectures as $lecture)
            {
            $lecture_source_path = '';
            $old_course_id                                          = $lecture['cl_course_id'];
            $org_file_name                                          = $lecture['cl_org_file_name'];
            $is_video_file                                          = false;

            if($lecture['cl_lecture_image'] != 'default-lecture.jpg')
            {
                $file_to_copy_queue                                 = array();
                $ext                                                = explode('.',$lecture['cl_lecture_image']);
                if(isset($ext[1]))
                {
                    $file_name                                      = $ext[0];
                    $ext                                            = explode('?',$ext[1]);
                    $ext                                            = $ext[0];
                    $file_name_full                                 = $file_name.'.'.$ext;
                    $lecture_source                                 = $this->get_upload_config('lecture_image', $lecture['cl_course_id']);
                    $lecture_source_path                            = $lecture_source['upload_path'];     
                    
                    $lecture_destination                            = $this->get_backup_config('lecture_image', $course_id);
                    $lecture_destination_path                       = $course_destination.$lecture_destination['upload_path'];
                    $lecture_destination_path                       = $lecture_destination_path;

                    if(!$as_siteadmin_value )
                    {
                        $this->make_directory($lecture_destination_path);
                    }
                
                    $files_to_copy_array                                = array();
                    $lecture_source_path1                               = $lecture_source_path.$file_name_full;
                    $files_to_copy_array['source']                      = $lecture_source_path1;
                    $files_to_copy_array['destination']                 = $lecture_destination_path.$file_name_full;
                    $files_to_copy_array['status']                      = '0';
                    $files_to_copy_array['lecture_id']                  = $lecture['id'];
                    $files_to_copy[]                                    = $files_to_copy_array;

                    $file_name_full                                     = $file_name.'_85x49.'.$ext;
                    $lecture_source_path2                               = $lecture_source_path.$file_name_full;
                    $files_to_copy_array['source']                      = $lecture_source_path2;
                    $files_to_copy_array['destination']                 = $lecture_destination_path.$file_name_full;
                    $files_to_copy_array['status']                      = '0';
                    $files_to_copy_array['lecture_id']                  = $lecture['id'];
                    $files_to_copy[]                                    = $files_to_copy_array;

                    $file_name_full                                     = $file_name.'_300x169.'.$ext;
                    $lecture_source_path3                               = $lecture_source_path.$file_name_full;
                    $files_to_copy_array['source']                      = $lecture_source_path3;
                    $files_to_copy_array['destination']                 = $lecture_destination_path.$file_name_full;
                    $files_to_copy_array['status']                      = '0';
                    $files_to_copy_array['lecture_id']                  = $lecture['id'];
                    $files_to_copy[]                                    = $files_to_copy_array;

                //$file_copy_param                            = array('source_path' => $lecture_source_path, 'target_path' => $lecture_destination_path);
                //$copy_status                                = $this->copy_file($file_copy_param);

                }
            }

            if( !empty($lecture['cl_org_file_name']) )
            {
                $file_to_copy_queue                                 = array();
                
                $ext                                                = explode('.',$lecture['cl_org_file_name']);
                if(isset($ext[1]))
                {
                    $file_name                                      = $ext[0];
                    $ext                                            = explode('?',$ext[1]);
                    $ext                                            = $ext[0];
                    $is_video_file                                  = in_array($ext, $this->__video_types);
                    $file_name_full                                 = $file_name.'.'.$ext;

                    $lecture_source                                 = $this->get_upload_config($ext, $lecture['cl_course_id']);
                    $lecture_source_path                            = $lecture_source['upload_path'];     
                    $lecture_source_path                            = $lecture_source_path.$file_name_full;
                    
                    $lecture_destination                            = $this->get_backup_config($ext, $course_id);
                    $lecture_destination_path                       = $course_destination.$lecture_destination['upload_path'];
                    
                    $lecture_destination_path                       = $lecture_destination_path.$file_name_full;

                    if($lecture['cl_lecture_type'] != $this->__lecture_type_keys_array['certificate'])
                    {
                        $files_to_copy_array                            = array();
                        $files_to_copy_array['source']                  = $lecture_source_path;
                        $files_to_copy_array['destination']             = $lecture_destination_path;
                        $files_to_copy_array['status']                  = '0';
                        $files_to_copy_array['lecture_id']              = $lecture['id'];
                        $files_to_copy[]                                = $files_to_copy_array;
                    }

                    if(!$as_siteadmin_value )
                    {
                        $lecture_destination                        = $this->get_backup_config($ext, $course_id);
                        $lecture_destination_path                   = $course_destination.$lecture_destination['upload_path'];
                        $this->make_directory($lecture_destination_path);
                    }
                }
                
            }

            if( !empty($lecture['cl_support_files']) )
            {
                $support_files                                      = json_decode($lecture['cl_support_files'],true);
                if(!empty($support_files))
                {
                    /*if( $as_siteadmin_value )
                    {*/
                        
                        foreach( $support_files as $files_key => $file)
                        {
                            $file_name                              = explode('?',$file['file_raw_name']);
                            $file['file_raw_name']                  = $file_name[0];
                            $file_with_exist_path                   = supportfile_upload_path(array('course_id' => $lecture['cl_course_id'])).$file['file_raw_name'];
                            $file_with_target_path                  = $course_destination.$course_id.'/support_files/'.$file['file_raw_name'];
                            $files_to_copy_array                    = array();
                            $files_to_copy_array['source']          = $file_with_exist_path;
                            $files_to_copy_array['destination']     = $file_with_target_path;
                            $files_to_copy_array['status']          = '0';
                            $files_to_copy_array['lecture_id']      = $lecture['id'];
                            $files_to_copy[]                        = $files_to_copy_array;
                        }
                        
                    /*}
                    else
                    {
                        foreach( $support_files as $files_key => $file)
                        {
                            $directory                              = $course_destination.$course_id.'/support_files/';
                            $file_with_exist_path                   = supportfile_upload_path(array('course_id' => $lecture['cl_course_id'])).$file['file_raw_name'];
                            $file_with_target_path                  = $directory.$file['file_raw_name'];
                            $this->make_directory($directory);
                            
                            $files_to_copy_array                    = array();
                            $files_to_copy_array['source']          = $file_with_exist_path;
                            $files_to_copy_array['destination']     = $file_with_target_path;
                            $files_to_copy_array['status']          = '0';
                            $files_to_copy_array['lecture_id']      = $lecture['id'];
                            $files_to_copy[]                        = $files_to_copy_array;
                        }
                    }*/
                    
                }
            }

            if( ($lecture['cl_lecture_type'] != $this->__lecture_type_keys_array['quiz']) && ($lecture['cl_lecture_type'] != $this->__lecture_type_keys_array['survey']))
            {

                if($lecture['cl_lecture_type'] == $this->__lecture_type_keys_array['descriptive_test'])
                {
                    $param                                                      = array('course_id' => $course_id, 'purpose' => 'assignment');
                    $directory                                                  = $course_destination.$course_id.'/assignment/';
                    $this->make_directory($directory);
                    $descriptive_test                                           = $this->Course_model->get_descriptive_test_item($lecture['id']);
                    if(!empty($descriptive_test))
                    {
                        //echo '<pre>'; print_r($descriptive_files);
                        $descriptive_files                                      = json_decode($descriptive_test['dt_uploded_files'],true);
                
                        if(!empty($descriptive_files))
                        { 
                            /*if( $as_siteadmin_value )
                            {*/
                                foreach( $descriptive_files as $files_key => $file)
                                {       
                                        $file_name                              = explode('?',$file['file_name']);
                                        $file_with_exist_path                   = assignment_upload_path(array('course_id' => $lecture['cl_course_id'],'purpose'=>'assignment')).$file_name[0];
                                        $file_with_target_path                  = $course_destination.$course_id.'/assignment/'.$file_name[0];
                                        $files_to_copy_array                    = array();
                                        $files_to_copy_array['source']          = $file_with_exist_path;
                                        $files_to_copy_array['destination']     = $file_with_target_path;
                                        $files_to_copy_array['status']          = '0';
                                        $files_to_copy_array['lecture_id']      = $lecture['id'];
                                        $files_to_copy[]                        = $files_to_copy_array;
                                }
                                    
                            /*}
                            else
                            {
                                foreach( $descriptive_files as $files_key => $file)
                                {
                                    $file_name                                  = explode('?',$file['file_name']);
                                    $file_with_exist_path                       = assignment_upload_path(array('course_id' => $lecture['cl_course_id'],'purpose'=>'assignment')).$file_name[0];
                                    $file_with_target_path                      = $course_destination.$course_id.'/assignment/'.$file_name[0];
                                    $files_to_copy_array                        = array();
                                    $files_to_copy_array['source']              = $file_with_exist_path;
                                    $files_to_copy_array['destination']         = $file_with_target_path;
                                    $files_to_copy_array['status']              = '0';
                                    $files_to_copy_array['lecture_id']          = $lecture['id'];
                                    $files_to_copy[]                            = $files_to_copy_array;
                                }
                            }*/
                            
                        }
                    }
                    
                }
                if($content_security_status == 1 )
                {
                    if(isset($is_video_file) && $is_video_file)
                    {
                        $queue                                                  = array();
                        $ext                                                    = explode('.',$org_file_name);
                        $file_name                                              = $ext[0];
                        $ext                                                    = explode('?',$ext[1]);
                        $ext                                                    = $ext[0];
                        $file_name_full                                         = $file_name.'.'.$ext;

                        $lecture_source                                         = $this->get_upload_config($ext, $course_id);
                        
                        $lecture_source_path                                    = $lecture_source['upload_path'];
                        $lecture_source_path                                    = $lecture_source_path.$file_name_full;

                        $lecture_destination                                    = $this->get_backup_config($ext, $course_id);
                        $lecture_destination_path                               = $course_destination.$lecture_destination['upload_path'];
                        $this->make_directory($lecture_destination_path);
                        $lecture_destination_path                               = $lecture_destination_path;

                        if($content_security_status)
                        {
                            $content_folder                                     = $lecture_destination_path.$file_name;
                            $this->make_directory($content_folder);
                            $files_to_copy_array                                = array();
                            $files_to_copy_array['source']                      = $lecture_source_path.$file_name;
                            $files_to_copy_array['destination']                 = $content_folder.$file_name;
                            $files_to_copy_array['status']                      = '0';
                            $files_to_copy_array['lecture_id']                  = $lecture['id'];
                            $files_to_copy[]                                    = $files_to_copy_array;
                        }
                    }
                }
              }
            }
            //
            $objects                                                            = array();
            $objects['key']                                                     = 'section_images';
            $section_images                                                     = $this->memcache->get($objects);
            $this->memcache->delete('section_images');
            if(!empty($section_images))
            {
                $files_to_copy_count                                            = count($files_to_copy);
                for($i = 0; $i < count($section_images); $i++)
                {
                    $files_to_copy[$files_to_copy_count+$i]                     = $section_images[$i];
                }
            }
            
            if(!empty($files_to_copy))
            {
                $copy_queue_json                                                = json_encode($files_to_copy);
                $copy_params                                                    = array();
                $copy_params['cq_source_course']                                = $course_id;
                $copy_params['cq_destination_course']                           = $course_id;
                $copy_params['cq_copy_json']                                    = $copy_queue_json;
                $copy_params['cq_status']                                       = '0';
                $copy_params['cq_backup_id']                                    = $backup_id;
                $this->Backup_model->save_file_copy_queue($copy_params);
            }

        $backup                                                                 = array();
        $backup['id']                                                           = $backup_id;
        $backup['cbk_course_id']                                                = $course_id;
        $backup['cbk_size']                                                     = $total_size;
        $this->Backup_model->backup($backup);

        $this->json_encode($response);
    }

    public function execInBackground($cmd) 
    {
       if (substr(php_uname(), 0, 7) == "Windows"){
           pclose(popen("start /B ". $cmd, "r"));
       }
       else {
        shell_exec($cmd . " > /dev/null &"); 
       }
   }

    /*
    purpose     : backup complete message
    params      : none
    usage-in    : inside Courses(Admin)
    edited      : none
    */
    function finalize_bakup()
    {
        $response                                                   = array();
        $response['error']                                          = false;
        $response['message']                                        = 'Course backup completed successfully';
        
        $this->json_encode($response);
    }

    /*
    purpose     : restore initialize
    params      : none
    usage-in    : inside Courses(Admin)
    edited      : none
    */
    function restore_initialize()
    {
        $response                                                   = array();
        $has_s3_enabled                                             = $this->settings->setting('has_s3');
        $as_siteadmin_value                                         = $has_s3_enabled['as_siteadmin_value'];
        $response['error']                                          = false;
        $response['message']                                        = 'Initialized course restore';
        $backup_id                                                  = $this->input->post('backup_id');
        $course_id                                                  = $this->input->post('course_id');
        $backup_details                                             = $this->Backup_model->backup_details(array('backup_id' => $backup_id));
        $copy_queue_details                                         = $this->Backup_model->get_copy_queue(array('status' => '1', 'cq_backup_id' => $backup_id, 'course_id' => $backup_details['cbk_course_id']));  
        
        if(empty($backup_details) || empty(json_decode($backup_details['cbk_assets'], true)['lectures']))
        {
            $response['error']                                      = true;
            $response['message']                                    = 'Invalid backup source requested';
            $this->json_encode($response);
            return false;
        }

        if(empty($copy_queue_details))
        {
            $response['error']                                      = true;
            $response['message']                                    = 'This backup is in queue, please wait till the backup process complete!';
            $this->json_encode($response);
            return false;
        }
        else
        {
            $this->load->model('Course_model');
            $subscribed_users                                       = $this->Course_model->course_enrolled(array('course_id' => $course_id, 'select' =>'cs_user_id'));

            if($has_s3_enabled && ($has_s3_enabled['as_siteadmin_value']))
            {
                $s3_settings                                        = $has_s3_enabled['as_setting_value']['setting_value'];
                $s3_bucket_url                                      = '';//"https://".$s3_settings->s3_bucket.".s3.amazonaws.com/";
                $backup_folder_root                                 = $s3_bucket_url.course_backup_upload_path();
                $course_source                                      = $s3_bucket_url.course_assets_uploaded_path(array('course_id' => $course_id));
                //$course_destination                         = $backup_folder_root.'backup_'.$course_id.'-'.$backup_id.'/';
            }
            else
            {
                $backup_folder_root                                 = course_backup_upload_path();
                //$this->make_directory($backup_folder_root);
                $course_source                                      = course_assets_uploaded_path(array('course_id' => $course_id));
                //$course_destination                         = $backup_folder_root.'backup_'.$course_id.'-'.$backup_id.'/';
                $this->make_directory($course_source);
                //$this->make_directory($course_destination.$course_id);
            }

            //print_r($subscribed_users);die;
            if(!empty($subscribed_users))
            {
                foreach($subscribed_users as $user)
                {
                    $this->memcache->delete('enrolled_'.$user['cs_user_id']);
                    $this->memcache->delete('mobile_enrolled_'.$user['cs_user_id']);
                }
            }

            $this->Backup_model->remove_course_assets(array('course_id' => $course_id));
            $this->Course_model->save(array('id' => $course_id, 'cb_groups' => NULL));
            $restore_key                                                = 'sample';//'csrimport'.$this->number_to_alpha($this->__admin['id']);
            $backup_details['target_course_id']                         = $course_id;
            $backup_details['cbk_assets']                               = json_decode($backup_details['cbk_assets'], true);
            
            //creating sections
            $sections   = array();
            if(!empty($backup_details['cbk_assets']['sections']))
            {

                $files_to_copy                                          = array();
                $lecture_source                                         = $this->get_upload_config('section_image', $course_id);
                $lecture_source_path                                    = $lecture_source['upload_path'];
                
                if(!$as_siteadmin_value)
                {
                    $this->make_directory($lecture_source_path);
                }
                foreach($backup_details['cbk_assets']['sections'] as $section)
                {
                    
                    if($section['s_image'] != 'default-section.jpg')
                    {
                        $ext                                            = explode('.',$section['s_image']);
                        if(isset($ext[1]))
                        {
                            $course_destination                         = $backup_folder_root.'backup_'.$section['s_course_id'].'-'.$backup_id.'/';
                            $file_name                                  = $ext[0];
                            $ext                                        = explode('?',$ext[1]);
                            $ext                                        = $ext[0];
                            $file_name_full                             = $file_name.'.'.$ext; 

                            $lecture_destination                        = $this->get_backup_config('section_image', $section['s_course_id']);
                            $lecture_destination_path                   = $course_destination.$lecture_destination['upload_path'];
                            $lecture_destination_path                   = $lecture_destination_path;

                            $lecture_source_path1                       = $lecture_source_path.$file_name_full;

                            $files_to_copy_array                        = array();
                            $files_to_copy_array['source']              = $lecture_destination_path.$file_name_full;
                            $files_to_copy_array['destination']         = $lecture_source_path1;
                            $files_to_copy_array['status']              = '0';
                            $files_to_copy_array['lecture_id']          = '0';
                            $files_to_copy[]                            = $files_to_copy_array;

                            $file_name_full                             = $file_name.'_85x85.'.$ext; 
                            $lecture_source_path2                       = $lecture_source_path.$file_name_full;
                            $files_to_copy_array['source']              = $lecture_destination_path.$file_name_full;
                            $files_to_copy_array['destination']         = $lecture_source_path2;
                            $files_to_copy_array['status']              = '0';
                            $files_to_copy_array['lecture_id']          = '0';
                            $files_to_copy[]                            = $files_to_copy_array;

                            $file_name_full                             = $file_name.'_300x160.'.$ext; 
                            $lecture_source_path3                       = $lecture_source_path.$file_name_full;
                            $files_to_copy_array['source']              = $lecture_destination_path.$file_name_full;
                            $files_to_copy_array['destination']         = $lecture_source_path3;
                            $files_to_copy_array['status']              = '0';
                            $files_to_copy_array['lecture_id']          = '0';
                            $files_to_copy[]                            = $files_to_copy_array;

                        }
                    }

                    $section['s_course_id']                             = $course_id;
                    $section['updated_date']                            = date('Y-m-d h:i:s');
                    $section['s_status']                                = '0';
                    $sections[]                                         = $section;
                }

                $backup_details['cbk_assets']['sections']               = $this->Backup_model->create_sections($sections);

                if(!empty($files_to_copy))
                {
                    $this->memcache->set('section_images_restore', $files_to_copy); 
                }
                
            }
            //End
            //echo '<pre>';print_r($sections);die;
            $this->memcache->set($restore_key, $backup_details);
        }
        $this->json_encode($response);
    }

    /*
    purpose     : restore backup lectures,sections,surveys,questions
    params      : none
    usage-in    : inside Courses(Admin)
    edited      : none
    */
    function restore_database()
    {
        $backup_id                                                  = $this->input->post('backup_id');
        $objects                                                    = array();
        $objects['key']                                             = 'sample';//'csrimport'.$this->number_to_alpha($this->__admin['id']);
        $backup_details                                             = $this->memcache->get($objects);
        //echo $this->input->post('backup_engine');
        //print_r($backup_details);die('zxcvbnm, 804');
        if(empty($backup_details))
        {
            $response                                               = array();
            $response['error']                                      = true;
            $response['message']                                    = 'Invalid backup source requested';
            $this->json_encode($response);
        }
        else
        {
            $backup_engine                                          = $this->input->post('backup_engine');
            $param                                                  = array();
            $param['backup_id']                                     = $backup_id;
            $param['backup_details']                                = $backup_details;
            $this->$backup_engine($param);
        }
    }

    private function simple_lecture($param)
    {
        $response                                               = array();
        $response['error']                                      = false;
        $response['message']                                    = 'Simple lectures are restored successfully';
        //echo '<pre>';print_r($param);die;
        $backup_details                                         = $param['backup_details'];
        $backup_id                                              = $param['backup_id'];
        $course_id                                              = $backup_details['target_course_id'];
        $course                                                 = $backup_details['cbk_assets'];
        $this->load->model('Course_model');
        $param['surveys']                                       = array();
        
        if(!empty($course['lectures']))
        {
            $backup_details                                     = $this->Backup_model->backup_details(array('backup_id' => $backup_id));
            $copy_queue_details                                 = $this->Backup_model->get_copy_queue(array('status' => '1', 'cq_backup_id' => $backup_id, 'course_id' => $backup_details['cbk_course_id']));   
            $copy_json                                          = json_decode($copy_queue_details['cq_copy_json'], true);
            $cq_source_course                                   = $copy_queue_details['cq_source_course'];
            $copy_queue_array                                   = array();
            $sourse_lecture_ids                                 = array();
            if(isset($copy_json[0]['lecture_id']))
            {
                $sourse_lecture_ids                             = array_column($copy_json, 'lecture_id');
            }

            $unique_sourse_lecture_ids                          = array_unique($sourse_lecture_ids);
            //$duplicates_lecture_ids                         = array_diff_assoc($sourse_lecture_ids, $unique_sourse_lecture_ids);
            
            $save_lectures                                      = array();
            $queued_lectures                                    = array();
            
            foreach($course['lectures'] as $lecture)
            {
                $sourse_lecture_id                              = $lecture['id'];
                if(!in_array($sourse_lecture_id, $unique_sourse_lecture_ids))
                {
                    if(!in_array($lecture['cl_lecture_type'], array(1, 3, 13, 8)))
                    {
                        $lecture['id']                          = false;
                        $lecture['cl_course_id']                = $course_id;
                        $lecture['cl_section_id']               = $course['sections'][$lecture['cl_section_id']]['id'];
                        $lecture['created_date']                = date('Y-m-d h:i:s');
                        $lecture['updated_date']                = date('Y-m-d h:i:s');
                        //$lecture['cl_status']               = '0';
                        $lecture['cl_copy_queue_id']            = NULL;
                        $save_lectures[]                        = $lecture;
                    }

                    if($lecture['cl_lecture_type'] == 13)
                    {
                        $lecture['cl_course_id']                = $course_id;
                        $lecture['cl_section_id']               = $course['sections'][$lecture['cl_section_id']]['id'];
                        $lecture['created_date']                = date('Y-m-d h:i:s');
                        $lecture['updated_date']                = date('Y-m-d h:i:s');
                        //$lecture['cl_status']               = '0';
                        $lecture['cl_copy_queue_id']            = NULL;
                        $param['surveys'][]                     = $lecture;
                    }

                    if($lecture['cl_lecture_type'] == 8)
                    {
                        $lecture['id']                          = false;
                        $lecture['cl_course_id']                = $course_id;
                        $lecture['cl_section_id']               = $course['sections'][$lecture['cl_section_id']]['id'];
                        $lecture['created_date']                = date('Y-m-d h:i:s');
                        $lecture['updated_date']                = date('Y-m-d h:i:s');
                        //$lecture['cl_status']               = '0';
                        $lecture['cl_copy_queue_id']            = NULL;
                        $descriptiveFiles                       = isset($course['descriptive_tests'][$sourse_lecture_id]) ? $course['descriptive_tests'][$sourse_lecture_id] : array();
                        //$descriptiveFiles                   = $this->Course_model->get_descriptive_test_item($sourse_lecture_id);
                        if(!empty($descriptiveFiles))
                        {
                            $lecture['cl_status']               = '0';
                            $newLectureId                       = $this->Backup_model->save_lecture($lecture);
                            $descriptiveFiles['id']             = false;
                            $descriptiveFiles['dt_course_id']   = $course_id;
                            $descriptiveFiles['dt_lecture_id']  = $newLectureId;
                            $this->Course_model->save_descriptive_test($descriptiveFiles);
                        }
                        else
                        {
                            $save_lectures[]                    = $lecture;
                        }
                    }
                }
                else
                {
                    $lecture['id']                              = false;
                    $lecture['oldLectureId']                    = $sourse_lecture_id;
                    $lecture['cl_course_id']                    = $course_id;
                    $lecture['cl_section_id']                   = $course['sections'][$lecture['cl_section_id']]['id'];
                    $lecture['created_date']                    = date('Y-m-d h:i:s');
                    $lecture['updated_date']                    = date('Y-m-d h:i:s');
                    $lecture['cl_status']                       = '0';

                    if(!empty($lecture['cl_org_file_name']) || !empty($lecture['cl_support_files']) || $lecture['cl_lecture_type'] == 8)
                    {
                        $lecture['cl_conversion_status']        = '6';
                    }

                    $lecture['cl_copy_queue_id']                = NULL;
                    $queued_lectures[$sourse_lecture_id]        = $lecture;

                    //can be commended after testing purposes
                    /*
                    if(!in_array($lecture['cl_lecture_type'], array(1, 3, 13)))
                    {
                        $lecture['id']                      = false;
                        $lecture['cl_course_id']            = $course_id;
                        //$lecture['cl_section_id']           = $course['sections'][$lecture['cl_section_id']]['id'];
                        $lecture['created_date']            = date('Y-m-d h:i:s');
                        $lecture['updated_date']            = date('Y-m-d h:i:s');
                        //$save_lectures[]                    = $lecture;
                        echo 'simple_lecture 661 <pre>';
                        print_r($lecture);
                    }*/

                    /*if($lecture['cl_lecture_type'] == 13)
                    {
                        $lecture['cl_course_id']            = $course_id;
                        $lecture['cl_section_id']           = $course['sections'][$lecture['cl_section_id']]['id'];
                        $lecture['created_date']            = date('Y-m-d h:i:s');
                        $lecture['updated_date']            = date('Y-m-d h:i:s');
                        //$param['surveys'][]                 = $lecture;
                        echo 'simple_lecture 692 <pre>';
                        print_r($lecture);
                    }*/
                    //can be commended after testing purposes*/
                }
            }

            /*echo '<pre> 1 - '; print_r($save_lectures);
            echo '<pre> 2 - '; print_r($param['surveys']);
            echo '<pre> 3 - '; print_r($queued_lectures);
            die(123654789);*/

            $new_lecture_ids                                    = array();
            
            foreach($copy_json as $copy_queue)
            { 
                if(isset($queued_lectures[$copy_queue['lecture_id']]['oldLectureId']) && $queued_lectures[$copy_queue['lecture_id']])
                {
                    $oldLectureId                               = $queued_lectures[$copy_queue['lecture_id']]['oldLectureId'];
                    
                    $restore_backup_queue                   = array();
                    $restore_backup_queue['source']         = $copy_queue['destination'];
                    $restore_backup_queue['destination']    = str_replace('/'.$cq_source_course.'/','/'.$course_id.'/',$copy_queue['source']);
                    $restore_backup_queue['status']         = '0';
                    
                    $oldDetails                                 = $queued_lectures[$copy_queue['lecture_id']]['oldLectureId'];
                    unset($queued_lectures[$copy_queue['lecture_id']]['oldLectureId']);
                    if(!isset($new_lecture_ids[$copy_queue['lecture_id']]))
                    {
                        $new_lecture_ids[$copy_queue['lecture_id']] = $this->Backup_model->save_lecture($queued_lectures[$copy_queue['lecture_id']]);
                       
                        if($queued_lectures[$copy_queue['lecture_id']]['cl_lecture_type'] == 8)
                        {
                            $descriptiveFiles                   = isset($course['descriptive_tests'][$oldLectureId]) ? $course['descriptive_tests'][$oldLectureId] : '';//
                            if(!empty($descriptiveFiles))
                            {
                                $descriptiveFiles['id']         = false;
                                $descriptiveFiles['dt_course_id']= $course_id;
                                $descriptiveFiles['dt_lecture_id']= $new_lecture_ids[$copy_queue['lecture_id']];
                                $this->Course_model->save_descriptive_test($descriptiveFiles);
                            }
                        }
                    }
                    $queued_lectures[$copy_queue['lecture_id']]['oldLectureId'] = $oldDetails;
                    $restore_backup_queue['lecture_id']         = $new_lecture_ids[$copy_queue['lecture_id']];
                    $copy_queue_array[]                         = $restore_backup_queue;
                }
            }
            
            if(!empty($save_lectures))
            {
                $this->Backup_model->save_lectures($save_lectures);
            }

            $objects                                                            = array();
            $objects['key']                                                     = 'section_images_restore';
            $section_images                                                     = $this->memcache->get($objects);
            $this->memcache->delete('section_images_restore');
            if(!empty($section_images))
            {
                $files_to_copy_count                                            = count($copy_queue_array);
                for($i = 0; $i < count($section_images); $i++)
                {
                    $copy_queue_array[$files_to_copy_count+$i]                  = $section_images[$i];
                }
            }
            
            if(!empty($copy_queue_array))
            {
                $copy_queue_json                                                = json_encode($copy_queue_array);
                $copy_params                                                    = array();
                $copy_params['cq_source_course']                                = $cq_source_course;
                $copy_params['cq_destination_course']                           = $course_id;
                $copy_params['cq_copy_json']                                    = $copy_queue_json;
                $copy_params['cq_status']                                       = '0';
                $copy_params['cq_backup_id']                                    = '0';
                $this->Backup_model->save_file_copy_queue($copy_params);
            }
            
            if(!empty($param['surveys']))
            {
                $this->surveys($param);
            }
        } 
        
        $this->json_encode($response);
    }


    private function surveys($param)
    {
        $backup_details = $param['backup_details'];
        $backup_id      = $param['backup_id'];
        $course_id      = $backup_details['target_course_id'];
        $course         = $backup_details['cbk_assets'];
        if(!empty($param['surveys']))
        {            
            $surveys           = array();
            $survey_questions  = array();
            $survey_lectures   = $this->Backup_model->save_lecture_queues($param['surveys']);
            if(!empty($survey_lectures))
            {
                foreach($backup_details['cbk_assets']['surveys'] as $survey)
                {
                    $survey['s_course_id']                      = $course_id;
                    $survey['s_lecture_id']                     = isset($survey_lectures[$survey['s_lecture_id']]['id']) ? $survey_lectures[$survey['s_lecture_id']]['id'] : 0;
                    unset($survey['questions']);
                    $surveys[]                                  = $survey;
                }
                $surveys                                        = $this->Backup_model->save_surveys($surveys);
                foreach($surveys as $old_survey_id => $survey)
                {
                    $questions                                  = $backup_details['cbk_assets']['surveys'][$old_survey_id];
                    $questions                                  = isset($questions['questions'])?$questions['questions']:array();
                    if(!empty($questions))
                    {
                        foreach($questions as $question)
                        {
                            $question['id']                     = false;
                            $question['sq_survey_id']           = $survey['id'];
                            $survey_questions[]                 = $question; 
                        }
                    }
                }
                if(!empty($survey_questions))
                {
                    $this->Backup_model->save_survey_questions($survey_questions);
                }
            }
        }
        
    }
    private function video($param)
    {
        $response                                               = array();
        $response['error']                                      = false;
        $response['message']                                    = 'Video lectures restored successfully';
        $backup_details                                         = $param['backup_details'];
        $backup_id                                              = $param['backup_id'];
        $course_id                                              = $backup_details['target_course_id'];
        $course                                                 = $backup_details['cbk_assets'];
        if(!empty($course['lectures']))
        {
            $save_lectures                                      = array();
            $videos_to_delete                                   = array();
            foreach($course['lectures'] as $lecture)
            {
                if($lecture['cl_lecture_type'] == 1)
                {
                    // $lecture['id']              = false;
                    $lecture['cl_course_id']                    = $course_id;
                    $lecture['cl_section_id']                   = $course['sections'][$lecture['cl_section_id']]['id'];
                    $lecture['created_date']                    = date('Y-m-d h:i:s');
                    $lecture['updated_date']                    = date('Y-m-d h:i:s');
                    $save_lectures[]                            = $lecture;
                    if($lecture['cl_filename'] && $backup_details['target_course_id'] == $backup_details['cbk_course_id'])
                    {
                        $videos_to_delete[]                     = $lecture['cl_filename'];
                    }
                }
            }
            
            if(!empty($videos_to_delete))
            {                
                $param                                          = array();
                $param['target_url']                            = admin_url('backup/delete_video');
                $param['video_ids']                             = json_encode($videos_to_delete);
                $this->launch_video_processor($param);
            }
            if(!empty($save_lectures)) //cl_lecture_name, cl_course_id, cl_section_id
            {    
                $cl_lecture_names                               = array_column($save_lectures, 'cl_lecture_name');
                $cl_course_ids                                  = array_column($save_lectures, 'cl_course_id');
                $cl_section_ids                                 = array_column($save_lectures, 'cl_section_id');
                $video_lectures                                 = $this->Backup_model->course_lectures(array('cl_lecture_names' => $cl_lecture_names, 'cl_course_ids' => $cl_course_ids, 'cl_section_ids' => $cl_section_ids));

                //$video_lectures         = $this->Backup_model->save_lecture_queues($save_lectures);
                
                $param                                          = array();
                $param['target_url']                            = admin_url('backup/upload_video');
                $param['videos']                                = array();
                foreach($video_lectures as $video)
                {
                    $param['videos'][]                          = array(
                                                                    'lecture_id'        => $video['id'],
                                                                    'path'              => base_url().video_upload_path(array('course_id' => $video['cl_course_id'])).$video['cl_org_file_name'],
                                                                    'name'              => $video['cl_lecture_name'],
                                                                    'description'       => $video['cl_lecture_description'],
                                                                    'vimeo_url'         => $video['cl_filename']
                                                                );
                }
                $param['videos'] = json_encode($param['videos']);
                $this->launch_video_processor($param);
            }
        }
        $this->json_encode($response);
        
    }
    public function upload_video()
    {
        if($_SERVER['HTTP_REQUEST_TOKEN'] == sha1(config_item('acct_domain').config_item('id')))
        {
            $this->load->library('Vimeoupload');
            $this->vimeoupload->set_config();
            
            $videos                                             = $this->input->post('videos');
            $videos                                             = json_decode($videos, true);
            foreach($videos as $video)
            {
                $vimeo_id                                       = explode('/',$video['vimeo_url'])[2];
                //echo $vimeo_id;
                $url                                            = "https://vimeo.com/api/oembed.json?url=https://vimeo.com/".$vimeo_id;
                $headers                                        = @get_headers($url,1); 
                if($headers && strpos( $headers[0], '200'))
                { 
                    // echo "video exist";
                    // return;
                    // die();
                }
                else
                {
                    $upload_data                                = $this->vimeoupload->pull_upload(array('path' => $video['path']));
                    if($upload_data['success'])
                    {
                        $save                                   = array();
                        $vimeo_url                              = isset($upload_data['data']['body']['uri'])?$upload_data['data']['body']['uri']:'';
                        $save['id']                             = $video['lecture_id'];
                        $save['cl_filename']                    = $vimeo_url."?v=".rand(10,1000);
                        $this->Backup_model->save_lecture($save);
                        $this->vimeoupload->edit(
                                                array(
                                                        'uri'        => $vimeo_url, 
                                                        'name'       => $video['name'], 
                                                        'description'=> $video['description']
                                                    )
                                                );
                    }    
                }
            }    
        }
    }

    public function delete_video()
    {
        if($_SERVER['HTTP_REQUEST_TOKEN'] == sha1(config_item('acct_domain').config_item('id')))
        {
            $video_ids = $this->input->post('video_ids');
            $video_ids = json_decode($video_ids, true);
            $this->load->library('Vimeoupload');
            $this->load->model('Course_model');
            $this->vimeoupload->set_config();
            $this->load->model('Course_model');
            foreach($video_ids as $video_id)
            {
                if($this->Course_model->get_videos_count(array('video_id' => $video_id) == 1))
                {
                    $this->vimeoupload->delete(array('uri'=>$video_id)); 
                }
            }
        }
    }

    private function launch_video_processor($config)
    {
        $curlHandle                                             = curl_init($config['target_url']);
        $defaultOptions                                         = array (
                                                                    CURLOPT_POST => 1,
                                                                    CURLOPT_POSTFIELDS => $config,
                                                                    CURLOPT_RETURNTRANSFER => false ,
                                                                    CURLOPT_TIMEOUT_MS => 1000,
                                                                );
        curl_setopt_array($curlHandle , $defaultOptions);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, FALSE);     
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2); 
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array(
            'request-token: '.sha1(config_item('acct_domain').config_item('id')),
        ));
        curl_exec($curlHandle);
        curl_close($curlHandle);
    }

    private function quiz($param)
    {
        $response                                               = array();
        $response['error']                                      = false;
        $response['message']                                    = 'Quiz lectures restored successfully';
        $backup_details                                         = $param['backup_details'];
        $backup_id                                              = $param['backup_id'];
        $course_id                                              = $backup_details['target_course_id'];
        $course                                                 = $backup_details['cbk_assets'];
        if(!empty($course['lectures']))
        {
            $save_lectures = array();
            foreach($course['lectures'] as $lecture)
            {
                if($lecture['cl_lecture_type'] == 3)
                {
                    $lecture['cl_course_id']                    = $course_id;
                    $lecture['cl_section_id']                   = $course['sections'][$lecture['cl_section_id']]['id'];
                    $lecture['created_date']                    = date('Y-m-d h:i:s');
                    $lecture['updated_date']                    = date('Y-m-d h:i:s');
                    $save_lectures[]                            = $lecture;
                }
            }
            
            if(!empty($save_lectures))
            {                
                $quizes                                         = array();
                $quiz_questions                                 = array();
                $quiz_lectures                                  = $this->Backup_model->save_lecture_queues($save_lectures);
                //print_r($quiz_lectures);
                //print_r($backup_details['cbk_assets']['quizes']); die;
                foreach($backup_details['cbk_assets']['quizes'] as $quiz)
                {
                    if(isset($quiz_lectures[$quiz['a_lecture_id']]['id']))
                    {
                        $quiz['a_course_id']                    = $course_id;
                        $quiz['a_lecture_id']                   = $quiz_lectures[$quiz['a_lecture_id']]['id'];
                        unset($quiz['questions']);
                        $quizes[]                               = $quiz;
                    }
                }
                $quizes                                         = $this->Backup_model->save_quizes($quizes);
                foreach($quizes as $old_quiz_id => $quiz)
                {
                    $questions                                  = isset($backup_details['cbk_assets']['quizes'][$old_quiz_id]['questions']) ? $backup_details['cbk_assets']['quizes'][$old_quiz_id]['questions'] : '';
                    if(!empty($questions))
                    {
                        foreach($questions as $question)
                        {
                            $question['id']                     = false;
                            $question['aq_assesment_id']        = $quiz['id'];
                            $quiz_questions[]                   = $question; 
                        }
                    }
                }
                if(!empty($quiz_questions))
                {
                    $this->Backup_model->save_quiz_questions($quiz_questions);
                }
            }
        }
        $this->json_encode($response);
    }

    function restore_assets()
    {
        //'restore_assets this is called before simple lecture';
        $response                                               = array();
        $response['error']                                      = false;
        $response['message']                                    = 'Course assets restored';
        $course_id                                              = $this->input->post('course_id');
        $backup_id                                              = $this->input->post('backup_id');
        $objects                                                = array();
        $objects['key']                                         = 'sample';//'csrimport'.$this->number_to_alpha($this->__admin['id']);
        $backup_details                                         = $this->memcache->get($objects);
        //echo '<pre>'; print_r($backup_details);
        if(empty($backup_details))
        {
            $response['error']                                  = true;
            $response['message']                                = 'Invalid backup source requested';
        }
        else
        {
            $backup_details                                     = $this->Backup_model->backup_details(array('backup_id' => $backup_id)); //echo '<pre>'; print_r($backup_details);die;
            $copy_queue_details                                 = $this->Backup_model->get_copy_queue(array('status' => '1', 'cq_backup_id' => $backup_id, 'course_id' => $backup_details['cbk_course_id']));   
            $copy_json                                          = json_decode($copy_queue_details['cq_copy_json'], true);
            $cq_source_course                                   = $copy_queue_details['cq_source_course'];

            /*
            $copy_queue_array = array();

            foreach($copy_json as $copy_queue)
            {
                $restore_backup_queue                           = array();
                $restore_backup_queue['source']                 = $copy_queue['destination'];
                $restore_backup_queue['destination']            = str_replace('/'.$cq_source_course.'/','/'.$course_id.'/',$copy_queue['source']);
                $restore_backup_queue['status']                 = '0';
                $restore_backup_queue['lecture_id']             = '0';
                $copy_queue_array[]                             = $restore_backup_queue;
            }
            */

            $objects                                            = array();
            $objects['key']                                     = 'copy_queue_array_'.$backup_id.'_'.$course_id;
            $callback                                           = '';
            $params                                             = array();
            $copy_queue_array                                   = $this->memcache->get($objects, $callback,$params);
            
                $has_s3_enabled                                 = $this->settings->setting('has_s3');
                $as_siteadmin_value                             = $has_s3_enabled['as_siteadmin_value'];
                if(!$as_siteadmin_value)
                {
                    $backup_folder_root                         = course_assets_uploaded_path();
                    if(!is_dir($backup_folder_root))
                    {
                        $this->make_directory($backup_folder_root);
                    }

                    $course_source                              = course_assets_uploaded_path(array('course_id' => $course_id));
                    if(!is_dir($course_source))
                    {
                        $this->make_directory($course_source);
                    }

                    $redactor_source                            = redactor_upload_path(array('course_id' => $course_id));
                    if(!is_dir($redactor_source))
                    {
                        $this->make_directory($redactor_source);
                    }

                    $lecture_source                             = $this->get_upload_config('mp4', $course_id);
                    $lecture_source_path                        = $lecture_source['upload_path'];
                    if(!is_dir($lecture_source_path))
                    {
                        $this->make_directory($lecture_source_path);
                    }

                    $lecture_source                             = $this->get_upload_config('docx', $course_id);
                    $lecture_source_path                        = $lecture_source['upload_path'];
                    if(!is_dir($lecture_source_path))
                    {
                        $this->make_directory($lecture_source_path);
                    }

                    $lecture_source                             = $this->get_upload_config('zip', $course_id);
                    $lecture_source_path                        = $lecture_source['upload_path'];
                    if(!is_dir($lecture_source_path))
                    {
                        $this->make_directory($lecture_source_path);
                    }

                    $lecture_source                             = $this->get_upload_config('mp3', $course_id);
                    $lecture_source_path                        = $lecture_source['upload_path'];
                    if(!is_dir($lecture_source_path))
                    {
                        $this->make_directory($lecture_source_path);
                    }
                    
                    $file_with_exist_path                       = supportfile_upload_path(array('course_id' => $course_id));
                    if(!is_dir($file_with_exist_path))
                    {
                        $this->make_directory($file_with_exist_path);
                    }

                    $file_with_exist_path                       = assignment_upload_path(array('course_id' => $course_id,'purpose'=>'assignment'));
                    if(!is_dir($file_with_exist_path))
                    {
                        $this->make_directory($file_with_exist_path);
                    }

                    $lecture_source                             = $this->get_upload_config('lecture_image', $course_id);
                    $lecture_source_path                        = $lecture_source['upload_path'];
                    if(!is_dir($lecture_source_path))
                    {
                        $this->make_directory($lecture_source_path);
                    }
                }
        }

        $this->json_encode($response);
    }

    function finalize_restore()
    {
        $response                                               = array();
        $response['error']                                      = false;
        $response['message']                                    = 'Course backup completed successfully';
        
        $course_id                                              = $this->input->post('course_id');
        $backup_id                                              = $this->input->post('backup_id');
        $restore_key                                            = 'sample';//'csrimport'.$this->number_to_alpha($this->__admin['id']);
        $this->memcache->delete($restore_key);
        $this->memcache->delete('course_'.$course_id);
        $this->json_encode($response);
    }
    private function json_encode($response = array())
    {
        echo json_encode($response);exit;
    }
    
    private function make_directory($path = false)
    {
        if(!$path )
        {
            return false;
        }
        if (!is_dir($path)) 
        {
            mkdir($path, 0777, true);
            //chmod($path, 0777);
        }
    }

    function delete()
    {
        $response                                               = array();
        $response['error']                                      = false;
        $response['message']                                    = 'Course backup deleted';
        $backup_id                                              = $this->input->post('backup_id');
        $backup_details                                         = $this->Backup_model->backup_details(array('backup_id' => $backup_id));
        if(empty($backup_details))
        {
            $response['error']                                  = true;
            $response['message']                                = 'Invalid backup source requested';
            $this->json_encode($response);    
        }
        
        $backup_folder_root                                     = course_backup_upload_path();
        $backup_source                                          = $backup_folder_root.'backup_'.$backup_details['cbk_course_id'].'-'.$backup_id;
        if(is_dir($backup_source))
        {
            $backup_source_delete_command                       = 'rm -rf  '.$backup_source;
            shell_exec($backup_source_delete_command);    
        }

        $this->Backup_model->delete(array('backup_id' => $backup_id));
        $this->json_encode($response);
    }

    function backups()
    {
        $response                                               = array();
        $response['error']                                      = false;
        $response['message']                                    = 'Listing course backups';
        $course_id                                              = $this->input->post('course_id');

        $response['backups']                                    = array();
        $backups                                                = $this->Backup_model->backups(array('select' => 'id, cbk_course_name, cbk_course_code, cbk_course_id, cbk_size, cbk_backup_date', 'excluded_course_id' => $course_id, 'order_by' => 'DESC'));
    //    echo "<pre>";print_r($backups);exit;
        if(!empty($backups))
        {
            foreach($backups as $backup)
            {
                $backup['cbk_backup_date']                      = date("F j, Y, g:i a", strtotime($backup['cbk_backup_date']));
                $response['backups'][]                          = $backup;
            }
        }
        $this->json_encode($response);    
    }


    function preview()
    {
        $data                                                   = array();
        $data['course_id']                                      = isset($_GET['course_id'])?$_GET['course_id']:0;
        $data['backup_id']                                      = isset($_GET['backup_id'])?$_GET['backup_id']:0;
        $data['destination_course_id']                          = isset($_GET['d_course_id'])?$_GET['d_course_id']:0;
        $this->load->view($this->config->item('admin_folder').'/backup_restore',$data);
    }
    private function number_to_alpha($number)
    {
        return $number;
        // $alphabet = range('A','Z');
        // $count = count($alphabet);
        // if($number <= $count)
        // {
        //     $alpha = $alphabet[$number-1];
        // }
        // else
        // {
        //     while($number > 0)
        //     {
        //         $modulo     = ($number - 1) % $count;
        //         $alpha      = $alphabet[$modulo].$alpha;
        //         $number     = floor((($number - $modulo) / $count));
        //     }    
        // }
        // return $alpha;
    }

    private function folder_size($directory)
    {
        $size = 0;
        foreach (glob(rtrim($directory, '/').'/*', GLOB_NOSORT) as $each) 
        {
            $size += is_file($each) ? filesize($each) : $this->folder_size($each);
        }
        return $size;
    }

    private function real_size($size)
    {
        if($size<1024)
        {
            $size = $size." Bytes";
        }
        elseif(($size<1048576)&&($size>1023))
        {
            $size = round($size/1024, 1)." KB";
        }
        elseif(($size<1073741824)&&($size>1048575))
        {
            $size = round($size/1048576, 1)." MB";
        }
        else
        {
            $size=round($size/1073741824, 1)." GB";
        }
        return $size;
    }

    private function get_backup_config($mechanism=false, $course_id = 0) //get_upload_config
    {
        if(!$mechanism)
        {
            return false;
        }
        
        $video_formats                                          = array('mp4', 'flv', 'avi', 'f4v');
        $document_formats                                       = array('doc', 'odt','ods','odp','xls','xlsx','docx', 'pdf', 'ppt', 'pptx');
        $scorm_formats                                          = array('zip');
        $audio_formats                                          = array('mp3');
        $config                                                 = array();
        $config['encrypt_name']                                 = true;
        if(in_array($mechanism, $video_formats))
        {
            $this->__lecture_type                               = $this->__lecture_type_keys_array['video'];
            $directory                                          = $course_id.'/videos/';//video_upload_path(array('course_id' => $course_id));
            //$this->make_directory($directory);
            $config['upload_path']                              = $directory;
            $config['allowed_types']                            = implode('|', $video_formats);      
            return $config;
        }
        
        if(in_array($mechanism, $document_formats))
        {
            $this->__lecture_type                               = $this->__lecture_type_keys_array['document'];
            $directory                                          = $course_id.'/documents/';//document_upload_path(array('course_id' => $course_id));
            //$this->make_directory($directory);
            $config['upload_path']                              = $directory;
            //echo '<pre>';print_r($document_formats);die();
            $config['allowed_types']                            = implode('|', $document_formats);    
            return $config;
        }

        
        if(in_array($mechanism, $scorm_formats))
        {
            $this->__lecture_type                               = $this->__lecture_type_keys_array['scorm'];
            $directory                                          = $course_id.'/scorm/';//scorm_upload_path(array('course_id' => $course_id));
            //$this->make_directory($directory);
            $config['upload_path']                              = $directory;
            $config['allowed_types']                            = implode('|', $scorm_formats);       
            return $config;
        }

        if(in_array($mechanism, $audio_formats))
        {
            $this->__lecture_type                               = $this->__lecture_type_keys_array['audio'];
            $directory                                          = $course_id.'/audio/';//audio_upload_path(array('course_id' => $course_id));
            //$this->make_directory($directory);
            $config['upload_path']                              = $directory;
            $config['allowed_types']                            = implode('|', $audio_formats);       
            return $config;
        }

        if($mechanism == 'lecture_image')
        {
            $directory                                          = $course_id.'/lecture/';//audio_upload_path(array('course_id' => $course_id));
            //$this->make_directory($directory);
            $config['upload_path']                              = $directory;
            $config['allowed_types']                            = 'jpg|jpeg';       
            return $config;
        }

        if($mechanism == 'section_image')
        {
            $directory                                          = $course_id.'/section/';//audio_upload_path(array('course_id' => $course_id));
            //$this->make_directory($directory);
            $config['upload_path']                              = $directory;
            $config['allowed_types']                            = 'jpg|jpeg';       
            return $config;
        }

        
    }

    private function get_upload_config($mechanism=false, $course_id = 0) //
    {
        if(!$mechanism)
        {
            return false;
        }
        
        $video_formats                                          = array('mp4', 'flv', 'avi', 'f4v');
        $document_formats                                       = array('doc', 'odt','ods','odp','xls','xlsx','docx', 'pdf', 'ppt', 'pptx');
        $scorm_formats                                          = array('zip');
        $audio_formats                                          = array('mp3');
        $config                                                 = array();
        $config['encrypt_name']                                 = true;

        if(in_array($mechanism, $video_formats))
        {
            $this->__lecture_type                               = $this->__lecture_type_keys_array['video'];
            $directory                                          = video_upload_path(array('course_id' => $course_id));
            //$this->make_directory($directory);
            $config['upload_path']                              = $directory;
            $config['allowed_types']                            = implode('|', $video_formats);      
            return $config;
        }
        
        if(in_array($mechanism, $document_formats))
        {
            $this->__lecture_type                               = $this->__lecture_type_keys_array['document'];
            $directory                                          = document_upload_path(array('course_id' => $course_id));
            $this->make_directory($directory);
            $config['upload_path']                              = $directory;
            //echo '<pre>';print_r($document_formats);die();
            $config['allowed_types']                            = implode('|', $document_formats);    
            return $config;
        }

        
        if(in_array($mechanism, $scorm_formats))
        {
            $this->__lecture_type                               = $this->__lecture_type_keys_array['scorm'];
            $directory                                          = scorm_upload_path(array('course_id' => $course_id));
            //$this->make_directory($directory);
            $config['upload_path']                              = $directory;
            $config['allowed_types']                            = implode('|', $scorm_formats);       
            return $config;
        }

        if(in_array($mechanism, $audio_formats))
        {
            $this->__lecture_type                               = $this->__lecture_type_keys_array['audio'];
            $directory                                          = audio_upload_path(array('course_id' => $course_id));
            //$this->make_directory($directory);
            $config['upload_path']                              = $directory;
            $config['allowed_types']                            = implode('|', $audio_formats);
            return $config;
        }
        
        if($mechanism == 'lecture_image')
        {
            $directory                                          = course_lecture_upload_path(array('course_id' => $course_id));
            //$this->make_directory($directory);
            $config['upload_path']                              = $directory;
            $config['allowed_types']                            = 'jpg|jpeg';
            return $config;
        }
        
        if($mechanism == 'section_image')
        {
            $directory                                          = course_section_upload_path(array('course_id' => $course_id));
            //$this->make_directory($directory);
            $config['upload_path']                              = $directory;
            $config['allowed_types']                            = 'jpg|jpeg';
            return $config;
        }
        
    }

    function setTimeout($timeout){
        // sleep for $timeout milliseconds.
        sleep(($timeout/1000));
        $this->someFunctionToExecute(1);
    }

    function someFunctionToExecute ($i) {
        $i++;
        echo 'The function executed!-'.$i;
    }

    function process_copy_queue($backupId = false)
    {
        $queue_param                                            = array();
        $queue_param['status']                                  = '0';
        if($backupId)
        {
            $queue_param['id']                                  = $backupId;
        }
        //$has_s3_enabled                                         = $this->settings->setting('has_s3');
        $get_copy_queue_data                                    = $this->Backup_model->get_copy_queue($queue_param);
        if(empty($get_copy_queue_data)){
            die('Currently no Jobs in queue.....');
            return false;
        }

        $copy_json                                              = json_decode($get_copy_queue_data['cq_copy_json'], true);
        $update_queue_data                                      = array();
        $status_result                                          = array_column($copy_json, 'status');
        
        if(in_array('0', $status_result))
        {
            $copy_queue_key = array_search("0",$status_result);

            if($copy_json[$copy_queue_key]['status'] == 0)
            {
                $s3_file_copy_param                             = array('source_path' => $copy_json[$copy_queue_key]['source'], 'target_path' => $copy_json[$copy_queue_key]['destination']);
                $copy_status                                    = $this->copy_file($s3_file_copy_param);
                
                if($copy_status == 1)
                {
                    $copy_json[$copy_queue_key]['source']       = $copy_json[$copy_queue_key]['source'];
                    $copy_json[$copy_queue_key]['destination']  = $copy_json[$copy_queue_key]['destination'];
                    $copy_json[$copy_queue_key]['status']       = '1';
                }
                else
                {
                    $copy_json[$copy_queue_key]['source']       = $copy_json[$copy_queue_key]['source'];
                    $copy_json[$copy_queue_key]['destination']  = $copy_json[$copy_queue_key]['destination'];
                    $copy_json[$copy_queue_key]['status']       = '2';
                    echo '<pre>Failed to copy';
                    //print_r($s3_file_copy_param);
                }
                
                echo 'One queue processed successfully.....';
            }
            
            $updated_json = json_encode($copy_json);
            $update_queue_data['cq_copy_json']                  = $updated_json;
        }
        else
        {
            $update_queue_data['cq_status']                     = '1';
            echo 'One row completed successfully.....';
        }

        $update_queue_data['id']                                = $get_copy_queue_data['id'];
        $this->Backup_model->save_file_copy_queue($update_queue_data);
        if(isset($update_queue_data['cq_status']) && $update_queue_data['cq_status'] == '1')
        {
            $lecture_ids                                        = array_column($copy_json, 'lecture_id');
            //print_r($lecture_ids);die;
            $process_name                                       = '';

            if($get_copy_queue_data['cq_backup_id'] > 0)
            {
                $process_name                                   = 'backup';
            }
            elseif($get_copy_queue_data['cq_backup_id'] == NULL)
            {
                $process_name                                   = 'import';
            }
            else
            {
                $process_name                                   = 'restore';
            }
            
            $count                                              = 0;
            $message                                            = array();
            $process_status                                     = '';
            $failed_lecture_ids                                 = array();
            $completed_lecture_ids                              = array();
            foreach($copy_json as $process)
            {
                $count++;
                if($process['status'] == 2)
                {
                    $message[]                                  = $process_name." failed on sub process ".$count;
                    $failed_lecture_ids[]                       = $process['lecture_id'];
                }
                elseif($process['status'] == 1)
                {
                    $message[]                                  = $process_name." completed sub process ".$count;
                    $completed_lecture_ids[]                    = $process['lecture_id'];
                }
            }

            if($process_name != 'backup')
            {
                if(!empty($failed_lecture_ids))
                {
                    $failed_lecture_ids                         = array_unique($failed_lecture_ids);
                    $this->Backup_model->updateLectureCopyStatus(['lecture_ids' => $failed_lecture_ids, 'cl_conversion_status' => '7', 'cl_copy_queue_id' => $get_copy_queue_data['id']]);
                }

                if(!empty($completed_lecture_ids))
                {
                    $completed_lecture_ids                      = array_unique($completed_lecture_ids);
                    $this->Backup_model->updateLectureCopyStatus(['lecture_ids' => $completed_lecture_ids, 'cl_conversion_status' => '3', 'cl_copy_queue_id' => $get_copy_queue_data['id']]);
                }
            }
            
            $message_str                                        = implode(",<br>", $message);
            $template_admin                                     = $this->ofabeemailer->template(array('email_code' => 'copy_process_status_mail'));
            $param_admin['to']                                  = 'nidheesh.p@enfintechnologies.com';//admin
            $param_admin['subject']                             = $process_name.' Process Updates';//$template_admin['em_subject'];
            $param_admin['strictly_to_recepient']               = true;
            $contents_admin                                     = array('message_body' => $message_str);
            $param_admin['body']                                = $this->ofabeemailer->process_mail_content($contents_admin, $template_admin['em_message']);
            $admin_mail_result                                  = $this->ofabeemailer->send_mail($param_admin);
        }

        $get_copy_queue_data                                    = $this->Backup_model->get_copy_queue($queue_param);
        if(!empty($get_copy_queue_data))
        {
            $curlHandle                                         = curl_init(admin_url()."backup/process_copy_queue");
            $defaultOptions                                     = array (
                                                                    CURLOPT_POST => 1,
                                                                    CURLOPT_POSTFIELDS => '',
                                                                    CURLOPT_RETURNTRANSFER => true ,
                                                                    CURLOPT_TIMEOUT_MS => 1000,
                                                                );
            curl_setopt($curlHandle, CURLOPT_TIMEOUT,1000);
            curl_setopt_array($curlHandle , $defaultOptions);
            curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, FALSE);     
            curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2); 
            curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array(
                'request-token: '.sha1(config_item('acct_domain').config_item('id')),
            ));
            $result                                             = curl_exec($curlHandle);
            $curl_errno                                         = curl_errno($curlHandle);
            $curl_error                                         = curl_error($curlHandle);
            curl_close($curlHandle);
    
                // if ($curl_errno > 0) {
                //     echo "cURL Error ($curl_errno): $curl_error\n";
                // } else {
                //     echo "Data received: $result\n";
                // }
        }
        //echo '<script> setTimeout( function(){ location.reload(); }, 3000); </script>';
    }

    function copy_file($param = array())
    {
        $configs                                                = array();
        $s3_configured                                          = false;
        $s3_multipart_settings                                  = $this->settings->setting('has_s3');
        if($s3_multipart_settings['as_superadmin_value'] && $s3_multipart_settings['as_siteadmin_value'])
        {
            if(isset($s3_multipart_settings['as_setting_value']) && isset($s3_multipart_settings['as_setting_value']['setting_value']))
            {
                $s3_configured                                  = true;
                $configs['settings']                            = $s3_multipart_settings['as_setting_value']['setting_value'];
            }
        }
       
        if($s3_configured)
        {
            $configs['folderpath']                              = '';
            $this->load->library('multipartupload', $configs);
            try {
                $this->multipartupload->copy_s3_file($param);
                $copy_status                                    = '1';
            }
            catch (Exception $e) {
                log_message('error', $e->getMessage());
                $copy_status                                    = '0';
                echo '<pre>Error: '.$e->getMessage().'<br />';
                print_r($param);
            }
            catch (InvalidArgumentException $e) {
                echo $e->getMessage();
            }
        }
        else
        {
            $lecture_copy_command                               = 'if cp -rf '.$param['source_path'].' '.$param['target_path'].' > /dev/null; then echo "1"; else echo "0"; fi;';
            $copy_status                                        = shell_exec($lecture_copy_command);
            if($copy_status == 0)
            {
                copy($param['source_path'], $param['target_path']);
            }
        }

        return $copy_status;
    }

    function s3ObjectInfo($param = array())
    {
        $configs                                                = array();
        $s3_configured                                          = false;
        $s3_multipart_settings                                  = $this->settings->setting('has_s3');
        if($s3_multipart_settings['as_superadmin_value'] && $s3_multipart_settings['as_siteadmin_value'])
        {
            if(isset($s3_multipart_settings['as_setting_value']) && isset($s3_multipart_settings['as_setting_value']['setting_value']))
            {
                $s3_configured                                  = true;
                $configs['settings']                            = $s3_multipart_settings['as_setting_value']['setting_value'];
            }
        }
       
        if($s3_configured)
        {
            $this->load->library('multipartupload', $configs);
            try {
               return $this->multipartupload->s3ObjectInfo($param);
            }
            catch (Exception $e) {
                echo $e->getMessage();
            }
            catch (InvalidArgumentException $e) {
                echo $e->getMessage();
            }
        }
    }

    function reInitializeCopy()
    {
        $lectureId                                              = $this->input->post('lectureId');
        $copy_queue_id                                          = $this->input->post('copy_queue_id');
        $response                                               = array();
        if($lectureId && $copy_queue_id)
        {
            $copy_queue_details                                 = $this->Backup_model->get_copy_queue(array('status' => '1', 'id' => $copy_queue_id));
    
            if(!empty($copy_queue_details) && isset($copy_queue_details['cq_copy_json']))
            {
                $copy_queue_array                               = json_decode($copy_queue_details['cq_copy_json'], true);
                $new_queue_array                                = array();
                foreach($copy_queue_array as $copy_queue)
                {
                    if($copy_queue['lecture_id'] == $lectureId && $copy_queue['status'] == '2')
                    {
                        $copy_queue['status']                   = '0';
                    }
                    $new_queue_array[]                          = $copy_queue;
                }

                $copy_queue_details['cq_status']                = '0';
                $copy_queue_details['cq_copy_json']             = json_encode($new_queue_array);
                $this->Backup_model->save_file_copy_queue($copy_queue_details);
                $this->Backup_model->save_lecture(array('id' => $lectureId, 'cl_conversion_status' => '6'));
                
                $response['message']                            = 'File copy successfully Re Initiated';
                $response['error']                              = false;
            }
            else
            {
                $response['message']                            = 'Re Initialization failed <br />(No files are in Queue for the selected lecture)';
                $response['error']                              = true;
            }             
        }
        else
        {
            $response['message']                                = 'Re Initialization failed <br />(lectureId OR copy_queue_id is not found)';
            $response['error']                                  = true;
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

}
?>
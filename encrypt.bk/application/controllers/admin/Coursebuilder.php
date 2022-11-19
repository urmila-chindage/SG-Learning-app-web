<?php
class Coursebuilder extends CI_Controller
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
        $this->__response_url       = admin_url('coursebuilder/conversion_completed');
        $this->__database_api       = admin_url('coursebuilder/update_conversion_status');
        $skip_login                 = array('launch_conversion','vimeo_upload','dummy_conversion', 'process_conversion_queue', 'process_conversion_queue_via_remote_server','send_mail_to_bulk_users', 'input_trigger_vimeo_upload', 'update_scorm_conversion_status', 'conversion_completed', 'update_conversion_status', 'copyS3toHomeserver');
        $this->__role_query_filter  = array();
        $this->__loggedInUser       = $this->auth->get_current_user_session('admin');
        $redirect	                = $this->auth->is_logged_in(false, false);
        
        if (!$redirect && !in_array($this->router->fetch_method(), $skip_login))
        {
            $redirect   = true;
            if($redirect && !in_array($this->router->fetch_method(), $skip_login))
            {
                redirect('login');
            }
        }

        $this->load->model(array('Course_model', 'User_model', 'Category_model', 'Liveservice_model'));
        $this->lang->load('course');
        $this->actions              = $this->config->item('actions');
        $this->__builder_view       = 'coursebuilder';
        $this->__lecture_type_icons = array( '1' => array('parent' => 'text-red', 'child' => 'course-icon video-icon-gray'),
                                             '2' => array('parent' => 'text-purple', 'child' => 'course-icon doc-icon-gray'),
                                             '3' => array('parent' => 'text-green', 'child' => 'course-icon quiz-icon-gray'),
                                             '4' => array('parent' => 'text-red', 'child' => 'course-icon video-icon-gray'),
                                             '5' => array('parent' => 'text-purple', 'child' => 'course-icon html-icon-gray'),
                                             '6' => array('parent' => 'text-green', 'child' => 'course-icon html-icon-gray'),
                                             '7' => array('parent' => 'text-red', 'child' => 'course-icon live-icon-gray'),
                                             '8' => array('parent' => 'text-green', 'child' => 'course-icon assignments-icon-gray'),
                                             '9' => array('parent' => 'text-red', 'child' => 'course-icon video-icon-gray'),
                                             '10' => array('parent' => 'text-red', 'child' => 'course-icon scorm-icon-gray'),
                                             '11' => array('parent' => 'text-red', 'child' => 'course-icon recorded-icon-gray'),
                                             '12' => array('parent' => 'text-red', 'child' => 'course-icon audio-icon-gray'),
                                             '13' => array('parent' => 'text-green', 'child' => 'course-icon survey-icon-gray'),
                                             '14' => array('parent' => 'text-green', 'child' => 'course-icon certificate-icon-gray'), 
                                             '15' => array('parent' => 'text-green', 'child' => 'course-icon video-icon-gray')                                             
                                        );
        $this->__lecture_type_array = array(  '1'   => 'video',
                                              '2'   => 'document',
                                              '3'   => 'quiz',
                                              '4'   => 'youtube',
                                              '5'   => 'text',
                                              '6'   => 'wikipedia',
                                              '7'   => 'live',
                                              '8'   => 'descriptive_test',
                                              '9'   => 'recorded_videos',
                                              '10'  => 'scorm',
                                              '11'  => 'cisco_recorded_videos',
                                              '12'  => 'audio',
                                              '13'  => 'survey',
                                              '14'  => 'certificate',
                                              '15'  => 'vimeo'
                                       );
        $this->__lecture_type_keys_array  = array();

        foreach ($this->__lecture_type_array as $id => $type) 
        {
            $this->__lecture_type_keys_array[$type] = $id;
        }
        // echo "<pre>";print_r($this->__lecture_type_keys_array);die;
        $this->__report_type_array = array(
                                            '3' => 'assesment_report',
                                            '8' => 'descriptive_test_report'
                                        );
        $this->__question_types             = array('single' => '1', 'multiple' => '2', 'subjective' => '3', 'range' => '4', 'dropdown' => '5');
        $this->__difficulty                 = array('easy' => '1', 'medium' => '2', 'hard' => '3');
        $this->__single_type                = '1';
        $this->__multi_type                 = '2';
        $this->__subjective_type            = '3';
        $this->privilege                    = array('view' => 1, 'add' => 2, 'edit' => 3, 'delete' => 4);
        $course_id                          = ($this->uri->segment(4) != null) ? $this->uri->segment(4) : 0;
        $access_method                      = ($this->__loggedInUser['rl_full_course'] == '1')?'get_permission':'get_permission_course';
        $this->course_content_privilege     = $this->accesspermission
                                                   ->$access_method(array(
                                                                            'role_id'   => $this->__loggedInUser['role_id'],
                                                                            'module'    => 'course_content',
                                                                            'course_id' => $course_id,
                                                                            'user_id'   => $this->__loggedInUser['id']));
        
        $this->access_content_types         = json_decode($this->__loggedInUser['rl_content_types'],true);
        
        $this->__lecture_types_restriction  = array(  '1' => 'video','2' => 'document','3' => 'quiz','4' => 'youtube','5' => 'text','6' => 'wikipedia','7' => 'live','8' => 'assignment','9' => 'recorded videos','10' => 'scorm','11' => 'cisco recorded videos','12' => 'audio','13' => 'survey','14' => 'certificate');
    }
    
    function update_scorm_conversion_status()
    {
        if($_SERVER['HTTP_REQUEST_TOKEN'] == sha1(config_item('acct_domain').config_item('id')))
        {
            $this->load->library('ofabeeconverter'); 
            $config = $this->input->post();
            $this->ofabeeconverter->initialize_config($config);
            $this->ofabeeconverter->convert();            
            $save                           = array();
            $save['id']                     = $config['lecture_id'];
            $save['cl_conversion_status']   = '3';
            $this->Course_model->save_lecture($save);
            $delete_scorm_file_cmd = 'rm -rf '.$config['output'].'/'.$config['file_name']; 
            shell_exec($delete_scorm_file_cmd);
            $delete_scorm_file_cmd = 'rm -rf '.$config['output'].'/'.substr($config['file_name'], 0,-4); 
            shell_exec($delete_scorm_file_cmd);
        }
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
        
        // $lecture_id            = $config['lecture_id'];
        // $lecture               = $this->Course_model->lecture(array('id'=>$lecture_id));
        // $course                = $this->Course_model->course(array('id'=>$lecture['cl_course_id']));
        // $param                 = array();
        // $param['from']         = config_item('site_name').'<'.$this->config->item('site_email').'>';;
        // $param['to']           = array('thanveer.a@enfintechnologies.com','alex@enfintechnologies.com');
        // if(isset($conversion['success']) && $conversion['success']==true)
        // {
        //     $param['subject']      = "File conversion completed successfully";
        //     $param['body']         = "Hi Admin, <br/>A lecture named <b>".$lecture['cl_lecture_name']."</b> under the course <b>".$course['cb_title']."</b> has been successfully converted. If you are logged in please click <a href='".admin_url('coursebuilder/lecture/'.$lecture_id)."'>here</a>";
        // }
        // else
        // {
        //     $param['subject']      = "File conversion error";
        //     $param['body']         = "Hi Admin, <br/>There is an error in converting a lecture named <b>".$lecture['cl_lecture_name']."</b> under the course <b>".$course['cb_title']."</b>. Error is as follows<br />".$conversion['message'];
        // }
        // $send = $this->ofabeemailer->send_mail($param);            
    }
    function mail_test(){
        $param                 = array();
        $param['from']         = config_item('site_name').'<'.$this->config->item('site_email').'>';
        $param['to']           = array('alex+1@enfintechnologies.com');
        //$param['bcc']          = array('jijith.j@enfintechnologies.com');
        $param['subject']      = "Test Mail";
        $param['body']         = "Hi User, <br/>A new section named <b>jdshjfdshg</b> has been added under course <b>sdfhdhgfghjfg</b> from ".$this->config->item('acct_name');
        echo '<pre>';print_r($param);die;
        $this->ofabeemailer->send_mail($param);
    }
    
    function process_conversion_queue()
    {
        $queue_param                        = array();
        $queue_param['conversion_status']   = 2;
        $queue_param['count']               = true;
        $queue_size = $this->Course_model->conversion_queue($queue_param);
        $param                      = array();
        $param['limit']             = 2 - $queue_size;
        $param['conversion_status'] = '1';
        //$param['count']             = true;
        if($param['limit'] > 0 )
        {
            $conversion_queue = $this->Course_model->conversion_queue($param);
            // echo '<pre>';print_r($conversion_queue);die();        
            if(sizeof($conversion_queue))
            {
                $this->load->library('ofabeeconverter'); 
                foreach ($conversion_queue as $queue)
                {
                    $config                 = array();
                    $config['queue_id']     = $queue['id'];
                    $config['input']        = $queue['file_path'];
                    $config['s3_upload']    = ($queue['s3_upload'])?true:false;
                    $config['from_cisco']   = ($queue['from_cisco'])?true:false;
                    $config['lecture_id']   = $queue['lecture_id'];
                    $config['output']       = ($queue['output_path'])?$queue['output_path']:$this->get_output_path($queue['file_path']);
                    $config['target_url']   = admin_url('coursebuilder/launch_conversion');
                    //echo '<pre>'; print_r($config);die;
                    $this->ofabeeconverter->initialize($config);
                }
            }
            
        }
    }
    
    private function get_output_path($path=false)
    {
        $path_temp = false;
        if($path)
        {
            $path_temp = explode('/', $path);
            unset($path_temp[sizeof($path_temp)-1]);
            $path_temp = implode('/', $path_temp);
        }
        return $path_temp.'/';
    }
    
    function test_convert()
    {
        $config = array();
        $config['namwe'] = 'OLP';
        $myfile = fopen("uploads/dummy.txt", "w");
        $txt = json_encode($config);
        fwrite($myfile, $txt);
        fclose($myfile);
        
    }
    
    function section_json()
    {
        $data               = array();
        $data['sections']   = $this->Course_model->sections(array('direction'=>'ASC','order_by'=>'s_order_no', 'course_id'=>  $this->input->post('course_id')));
        echo json_encode($data);
    }

    public function save_lecture()
    {
    
        $s3_setting_ofabee          = $this->settings->setting('has_s3_ofabee');
        $ofabee_s3_siteadmin_value  = $s3_setting_ofabee['as_siteadmin_value'];
        $s3_setting_ofabee          = $s3_setting_ofabee['as_setting_value']['setting_value'];
        $content_security           = $this->settings->setting('has_content_security');
        $content_security_status    = $content_security['as_setting_value']['setting_value']->content_security_status;
        
        $response               = array();
        $response['status']     = false;
        $response['message']    = lang('lecture_saved_success');
        $vimeo_video_data       = array();
        //saving section details
        $section_id             = $this->input->post('section_id');
        $section_name           = $this->input->post('section_name');  
        $course_id              = $this->input->post('course_id');
        $file_name_with_path    = $this->input->post('file_name'); 
        $preview_lecture        = $this->input->post('preview_lecture'); 
        $preview_lecture        = isset($preview_lecture)?$preview_lecture:0; 

        $save                   = array();
        
        if( !$section_id )
        {
            if( $section_name == '' )
            {
                $response['status']  = true;
                $response['message']= lang('section_name_required');
                echo json_encode($response);exit;            
            }
            
            if($this->Course_model->section(array('filter_id' => $section_id, 'course_id' => $course_id, 'name' => $section_name, 'deleted' => '0', 'count' => true)) > 0)
            {
                $response['status']  = true;
                $response['message'] = lang('section_name_not_available');
                echo json_encode($response);exit;
            }
           //die('123456789');
            $save['id']             = $section_id;
            $save['s_name']         = $section_name;
            $save['s_course_id']    = $course_id;
            $total                  = $this->Course_model->sections(array('count'=>true, 'course_id'=> $course_id));
            $save['s_order_no']     = ($total)+1;
            $save['action_by']      = $this->auth->get_current_admin('id');
            $save['action_id']      = $this->actions['create'];            
            $section_id             = $this->Course_model->save_section($save);
            
        }
        $save                       = array(); 
        if(isset($_FILES['lecture_image']) && $_FILES['lecture_image']['error'] !== 4)
        { 
            $allowed =  array('gif','png' ,'jpg', 'jpeg');
            $filename = $_FILES['lecture_image']['name'];
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if(in_array($ext,$allowed))
            {

                $lecture_id                         = $this->input->post('lecture_id');

                $version                            = rand(0,300);
                if($this->upload_course_lecture_image_to_localserver(array('course_id'=>$this->input->post('course_id'), 'lecture_id'=>$lecture_id )))
                {
                    $save['cl_lecture_image']               =  $lecture_id.".jpg?v=".$version;
                }
            }
            
        }               
        $save['id']                                = $this->input->post('lecture_id');
        $save['cl_section_id']                     = $section_id;
        $save['cl_course_id']                      = $course_id;
        $save['cl_lecture_name']                   = $this->input->post('lecture_name');
        $save['cl_lecture_preview']                = $preview_lecture;
        $save['cl_lecture_description']            = $this->input->post('lecture_description');
        $save['cl_sent_mail_on_lecture_creation']  = $this->input->post('sent_mail_on_lecture_creation');
        $save['cl_limited_access']                 = $this->input->post('lecture_access_limit');
        $save['cl_limited_access']                 = ($save['cl_limited_access'] )?$save['cl_limited_access'] :'0';
        $save['action_by']                         = $this->auth->get_current_admin('id');
        if( $save['id'] )
        {
            $save['action_id']                         = $this->actions['update'];
            $save['updated_date']                      = date('Y-m-d H:i:s');
        }
        else
        {
            $save['action_id']                         = $this->actions['create'];            
            $highest_order                             = $this->Course_model->lectures(array('count'=>true, 'section_id'=>$section_id, 'course_id'=> $save['cl_course_id']));
            $save['cl_order_no']                       = $highest_order+1;
        }
        $lecture_id =  $this->Course_model->save_lecture($save);
       
        //loggin the lecture creation
        $user_data              = array();
        $user_data['user_id']   = $this->__loggedInUser['id'];
        $user_data['username']  = $this->__loggedInUser['us_name'];
        $user_data['useremail'] = $this->__loggedInUser['us_email'];
        $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
        $message_template                     = array();
        $message_template['username']         = $this->__loggedInUser['us_name'];;
        $message_template['lecture_name']     =  $save['cl_lecture_name'];
        $message_template['course_name']      =  $this->input->post('course_name');
        $triggered_activity     = 'lecture_created';
        log_activity($triggered_activity, $user_data, $message_template);
        //End
        $this->invalidate_course(array('course_id' => $save['cl_course_id']));
        $this->invalidate_course();
        
        $from_s3                                    = false;
        $vimeo_video_data['cl_lecture_name']        = $save['cl_lecture_name'];
        $vimeo_video_data['cl_lecture_description'] = $save['cl_lecture_description'];
        
        $lecture                    = $this->Course_model->lecture(array('direction'=>'DESC', 'id'=>  $save['id']));
        $vimeo_video_data['type']   = isset($lecture['cl_lecture_type'])?$lecture['cl_lecture_type']:1;
        if( $this->input->post('from_s3') )
        {
            $this->load->library('ofabeeconverter'); 
            $from_s3               = true;
            $file_name             = $this->ofabeeconverter->file_name($file_name_with_path);
            $lecture_type          = $vimeo_video_data['type'];
            $save                           = array();
            $save['id']                     = $lecture_id;
            $save['cl_conversion_status']   = ($s3_setting_ofabee==true)?'5':'1'; //upload completed
            $save['cl_org_file_name']       = $file_name."?v=".rand(10,1000);
            $save['cl_filename']            = $this->ofabeeconverter->remove_extension($file_name);
            $extension                      = $this->ofabeeconverter->extension($file_name);
            
            if(in_array($extension, $this->__document_types))
            {
                $lecture_type    =  $this->__lecture_type_keys_array['document'];
            }
            if(in_array($extension, $this->__scorm_types))
            {
                $lecture_type    =  $this->__lecture_type_keys_array['scorm'];
            }
            if(in_array($extension, $this->__audio_types))
            {
                $lecture_type    =  $this->__lecture_type_keys_array['audio'];
            }
            $save['cl_lecture_type']    = $lecture_type;
            
            if(!in_array($extension, $this->__audio_types))
            {
                $save['cl_duration']    =  $this->get_duration($file_name_with_path);
            }
            $this->Course_model->save_lecture($save);
            
            if(in_array($extension, $this->__scorm_types))
            {
                $config                 = array();
                $config['update_db']    = true;
                $config['lecture_id']   = $lecture_id;
                $config['s3_upload']    = true;
                $config['file_name']    = $file_name;
                $config['input']        = $file_name_with_path;
                $config['output']       = scorm_upload_path(array('course_id' => $course_id ));
                $curlHandle         = curl_init(admin_url('coursebuilder/update_scorm_conversion_status'));
                $defaultOptions     = array (
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
                
            $vimeo_video_data['type']                   = $lecture_type;
            $vimeo_video_data['cl_org_file_name']       = $file_name_with_path;
            $vimeo_video_data['id']                     = $lecture_id;
        }
        $vimeo_lecture_id   = $this->input->post('lecture_id');
        
        if( $this->input->post('extension') )
        {
            $this->load->library('ofabeeconverter');        
            $upload_data                    = json_decode($this->input->post('upload_data'));       
            $upload_data                    = $upload_data->file_object;
            $is_video_file                  = in_array(substr($upload_data->file_ext, 1), $this->__video_types);
            $save                           = array();
            $save['id']                     = $lecture_id;
            $save['cl_conversion_status']   = '1'; //upload completed
            $save['cl_lecture_type']        =  $this->__lecture_type_keys_array['video'];
            $save['cl_org_file_name']       = $upload_data->file_name."?v=".rand(10,1000);
            $extension                      = $this->ofabeeconverter->extension($upload_data->file_name);
            $lecture_type                   = $save['cl_lecture_type'];
            if(in_array($extension, $this->__document_types))       
            {       
                $lecture_type                   =  $this->__lecture_type_keys_array['document'];        
            }
            
            if(in_array($extension, $this->__scorm_types))
            {
                $save['cl_conversion_status']   = '3';//upload completed
                $cl_filename                    = $this->input->post('file_name');
                $save['cl_filename']            = $cl_filename."?v=".rand(10,1000);       
                $lecture_type                   = $this->__lecture_type_keys_array['scorm'];
            }
            if(in_array($extension, $this->__audio_types))
            {
                $lecture_type                   =  $this->__lecture_type_keys_array['audio'];
            }
            $save['cl_lecture_type']            =  $lecture_type;
            
            if(in_array($extension, $this->__video_types))
            {
                $save['cl_duration']            = $this->input->post('duration');
            }
            $vimeo_video_data['type']                   = $lecture_type;
            $vimeo_video_data['cl_org_file_name']       = $upload_data->file_name;
            $vimeo_video_data['id']                     = $lecture_id;
            if(!in_array($vimeo_video_data['type'], array(1,10)))
            {
                $save['cl_filename']        = $upload_data->raw_name."?v=".rand(10,1000); 
            }
            if($content_security_status == 1 && $is_video_file)
            {
                $save['cl_filename']        = $upload_data->raw_name.'/'.$upload_data->raw_name.".m3u8?v=".rand(10,1000); 
            }
            $this->Course_model->save_lecture($save);
            //$this->upload_file_to_local_server($lecture_id);
            // //no need to convert audio files
            // if(!in_array($extension, $this->__audio_types) && !in_array($extension, $this->__scorm_types))
            // {
            //     //saving data in conversion queue
            //     $save                       = array();
            //     $save['id']                 = false;
            //     $save['lecture_id']         = $lecture_id;
            //     $save['s3_upload']          = '0';
            //     $save['file_path']          = $upload_data->full_path;
            //     $save['conversion_status']  = '1';//upload completed
            //     $this->Course_model->save_conversion_queue($save);
            //     //End
            // } 
            $response['save'] = $save;
            $response['type'] = $vimeo_video_data['type'];

            if( $ofabee_s3_siteadmin_value && $is_video_file )
            {
                if(!is_dir(video_upload_path(array('course_id' => $course_id))))
                {
                    mkdir(video_upload_path(array('course_id' => $course_id)), 0777, true);
                }
        

                $config                     = array();
                $config['savehomeserver']   = true;
                $config['src_path']         = "https://".$s3_setting_ofabee->s3_bucket.".s3.amazonaws.com/".video_upload_path(array('course_id' => $course_id)).$upload_data->file_name;
                $config['target_path']      = video_upload_path(array('course_id' => $course_id)).$upload_data->file_name;
                
                $curlHandle         = curl_init(admin_url('coursebuilder/copyS3toHomeserver'));
                $defaultOptions     = array (
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

        }
        
        if(isset($is_video_file) && $is_video_file)
        {
            if($content_security_status == 1 && isset($is_video_file) && $is_video_file)
            {            
                $queue                       = array();
                if($content_security_status)
                {
                    
    
                    $file_path                   = "https://".$s3_setting_ofabee->s3_bucket.".s3.amazonaws.com/".video_upload_path(array('course_id' => $course_id)).$upload_data->file_name;
                    $output_path                 = '/'.video_upload_path(array('course_id' => $course_id));
                    $queue['conversion_status']  = '6';//upload completed    
                }
                else
                {
                    $file_path                   = video_path(array('course_id' => $course_id)).$upload_data->file_name;
                    $output_path                 = '/'.video_upload_path(array('course_id' => $course_id));
                    $queue['conversion_status']  = '1';//upload completed    
                }
    
                $queue['id']                 = false;
                $queue['lecture_id']         = $lecture_id;
                $queue['s3_upload']          = '0';
                $queue['from_cisco']         = '0';
                $queue['file_path']          = $file_path;
                $queue['output_path']        = $output_path;
                $this->Course_model->save_conversion_queue($queue);
            }
            else
            {
             
                if(isset($lecture['id']) && isset($upload_data->file_name) && $lecture['cl_lecture_type'] == 1 && count(explode('/',$lecture['cl_filename']))>1)
                {
                    $this->load->library('Vimeoupload');
                    $this->vimeoupload->set_config();
                    $has_parent_lecture  = $this->Course_model->has_parent_lecture(array('lecture_id'=>$lecture['id']));
                    if(!$has_parent_lecture)
                    {
                        $this->vimeoupload->delete(array('uri'=>$lecture['cl_filename'])); 
                    }
                   
                }
                
                if($vimeo_video_data['type'] == 1 && $vimeo_lecture_id == '')
                {
                    if($from_s3)
                    {
                        $s3_settings          = $this->settings->setting('has_s3');
                        // echo '<pre>'; print_r($s3_settings);die('---');
                        if(($s3_settings['as_superadmin_value'] && $s3_settings['as_siteadmin_value']))
                        {
                            $s3_settings          = $s3_settings['as_setting_value']['setting_value'];
                            $video_url            = "https://".$s3_settings->s3_bucket.".s3.amazonaws.com/".video_upload_path(array('course_id' => $course_id)).$vimeo_video_data['cl_org_file_name'];
                        }
                        else
                        {
                            $video_url                  = "https://".$s3_setting_ofabee->s3_bucket.".s3.amazonaws.com/".video_upload_path(array('course_id' => $course_id)).$vimeo_video_data['cl_org_file_name'];
                            $video_url                  = $s3_setting_ofabee ? $video_url : $vimeo_video_data['cl_org_file_name'];
                        }
                        //echo $video_url.'-488';
                    }
                    else
                    {
                        $video_url      = video_path(array('course_id' => $course_id)).$vimeo_video_data['cl_org_file_name'];
                        //echo $video_url.'-493';
                    }
                     //echo $video_url; echo $vimeo_video_data['cl_org_file_name'];die();
                    $this->initiate_vimeo(array(
                        'lecture_id' => $vimeo_video_data['id'],
                        'path' => $video_url,
                        'name' => $vimeo_video_data['cl_lecture_name'],
                        'description' => $vimeo_video_data['cl_lecture_description']
                    ));
                }
                else
                {
                    $vimeo_video_data['id']     = $vimeo_lecture_id;
                    if($vimeo_video_data['type'] == 1)
                    {
                        if(isset($upload_data->file_name) || $file_name_with_path != '' )
                        {
                            if($from_s3)
                            {
                                $video_url      = $file_name_with_path;
                            }
                            else
                            {
                                $video_url      = video_path(array('course_id' => $course_id)).$vimeo_video_data['cl_org_file_name'];
                            }
                            //echo $video_url.'-516';
                            $this->initiate_vimeo(array(
                                'lecture_id' => $vimeo_video_data['id'],
                                'path' => $video_url,
                                'name' => $vimeo_video_data['cl_lecture_name'],
                                'description' => $vimeo_video_data['cl_lecture_description']
                            ));
                        }
                        else
                        {
                            $lecture      = $this->Course_model->lecture(array('direction'=>'DESC', 'id'=>  $vimeo_video_data['id']));
                            $this->load->library('Vimeoupload');
                            $this->vimeoupload->set_config();
                            $param = array( 
                                'uri' => $lecture['cl_filename'], 
                                'name' => $vimeo_video_data['cl_lecture_name'], 
                                'description' => $vimeo_video_data['cl_lecture_description']
                            );
                            $this->vimeoupload->edit($param);
                        }
                    }
                }
            }
        }
        /*if($extension == 'mp4')
        {
            $save                           = array();
            $save['id']                     = $lecture_id;
            $save['cl_duration']            = $this->get_duration($file_name_with_path);
            $this->Course_model->save_lecture($save);
        }*/
       
        // echo '<pre>';print_r($response);die;
        $response['lecture_id']     = $lecture_id;
        $response['status']         = false;
        $response['error']          = false;
        echo json_encode($response);
    }

  
    function copyS3toHomeserver()
    {
        if($this->input->post('savehomeserver'))
        {
            copy($this->input->post('src_path'), $this->input->post('target_path'));
        }
    }

    private function initiate_vimeo($params = array())
    {
        $post_params                = array();
        $post_params['lecture_id']  = isset($params['lecture_id'])?$params['lecture_id']:false;
        $post_params['path']        = isset($params['path'])?$params['path']:false;
        $post_params['name']        = isset($params['name'])?$params['name']:false;
        $post_params['description'] = isset($params['description'])?$params['description']:false;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,admin_url('coursebuilder/vimeo_upload'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($post_params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_exec($ch);
        curl_close ($ch);
    }
    
    public function vimeo_upload()
    {
        $save                       = array();
        $response                   = array();
        $lecture_id                 = $this->input->post('lecture_id');
        $video_path                 = $this->input->post('path');
        $video_name                 = $this->input->post('name');
        $video_description          = $this->input->post('description');
        if($lecture_id == ''){
            die;
        }
        if($video_path == ''){
            die;
        }
        $this->load->library('Vimeoupload');
        $this->vimeoupload->set_config();
        $upload_data        = $this->vimeoupload->pull_upload(array('path'=>$video_path)); 
        if($upload_data['success'])
        {
            $save                   = array();
            $vimeo_url              = isset($upload_data['data']['body']['uri'])?$upload_data['data']['body']['uri']:'';
            $save['id']             = $lecture_id;
            $save['cl_filename']    = $vimeo_url;
            $save['cl_lecture_type']= '1';
            $this->Course_model->save_lecture($save);
            $this->vimeoupload->edit(array('uri'=>$vimeo_url,'name'=>$video_name,'description'=>$video_description));
        }
        /*$myfile = fopen("uploads/upload.txt", "w");
        $txt = json_encode($upload_data);
        fwrite($myfile, $txt);
        fclose($myfile); */
    }
    
    function upload_live_files_to_home_server()
    {
        $response                   = array();
        $response['file_object']    = array();
        $response['error']          = 'false';
        $response['message']        = lang('file_uploaded_success').' '.  lang('file_conversion_stated');
        $this->load->library('upload');
        
        $config                     = array();
        $directory                  = livefiles_upload_path();
        $config['upload_path']      = $directory;
        $config['allowed_types']    = 'mp4|flv|avi|f4v|doc|odt|ods|odp|xlsx|docx|xls|pdf|ppt|pptx|png|jpg|jpeg';   
        $config['encrypt_name']     = true;
        //echo '<pre>'; print_r($_FILES);die;
        if( $config )
        {
            $this->upload->initialize($config);
            $uploaded = $this->upload->do_upload('file');   
            
            if( $uploaded )
            {
                $upload_data                = $this->upload->data();
                $response['file_object']    = array();            
                $response['file_object']['id']     = $upload_data['raw_name'];
                $response['file_object']['title']  = $this->input->post('file_name');
                $response['file_object']['title']  = substr($response['file_object']['title'], 0, 22).$upload_data['file_ext'];
                $response['file_object']['link']   = $upload_data['file_name'];
                $live_id = $this->input->post('live_id');
                
                $live_lecture = $this->Course_model->live_lecture(array('live_id' => $live_id));
                
                $live_files = $live_lecture['ll_files'];
                if(!$live_files)
                {
                    $live_files = array();
                }
                else
                {
                    $live_files = json_decode($live_files, true);
                }
                $live_files[$upload_data['raw_name']] = $response['file_object'];
                
                $save = array();
                $save['id'] = $live_id;
                $save['ll_files'] = json_encode($live_files);
                $this->Course_model->save_live_lectures($save);
            }
            else
            {
                $response['error']      = 'true';
                $response['message']    = $this->upload->display_errors();//lang('error_uploading_file');                
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
    
    function deleteLiveFiles()
    {
        $response     = array();
        $live_id      = $this->input->post('live_id');
        $file_id      = $this->input->post('file_id');
        $live_lecture = $this->Course_model->live_lecture(array('live_id' => $live_id));
  
        $live_files = $live_lecture['ll_files'];
        $live_files = json_decode($live_files,true);
        $directory  = livefiles_upload_path();
        //if($file_id && is_file($directory.'/'.$file_id))
        if(!empty($live_files))
        {
            foreach($live_files as $live_key => $live_file)
            {
                if($live_file['id'] == $file_id)
                {
                    $file_name = explode('/', $live_file['link']);
                    $file_name = $file_name[sizeof($file_name)-1];
                    if(file_exists($directory.$file_name))
                    {
                        unlink($directory.$file_name);
                        unset($live_files[$live_key]);
                        break;
                    }
                }
            }
        }
        $save = array();
        $save['id'] = $live_id;
        $save['ll_files'] = json_encode($live_files);
        $this->Course_model->save_live_lectures($save);
        
        $response['success'] = true;
        $response['message'] = 'File removed successfully';
        
        echo json_encode($response);
    }
    
    function upload_to_home_server()
    {
        $response                   = array();
        $response['file_object']    = array();
        $response['error']          = false;
        $response['message']        = lang('file_uploaded_success').' '.  lang('file_conversion_stated');
        $supportfile                = $this->input->get('supportfile');
        $this->load->library('upload');
        //echo "<pre>"; print_r($this->input->post()); die();
        $course_id                  = $this->input->post('course_id');
        $config                     = $this->get_upload_config($this->input->post('extension'), $course_id, $supportfile);
        if( $config )
        {
            //echo "<pre>"; print_r($config); die();   
            $this->upload->initialize($config);
            $uploaded = $this->upload->do_upload('file');   
            //$error = array('error' => $this->upload->display_errors())
            if( $uploaded )
            {
                $upload_data                = $this->upload->data();
                $response['file_object']    = $upload_data;
                if($upload_data['file_ext'] == '.zip')
                {
                    $this->load->library('ofabeeconverter'); 
                    $config                 = array();
                    $config['update_db']    = false;
                    $config['file_name']    = $this->input->post('file_name');
                    $config['input']        = $upload_data['full_path'];
                    $config['output']       = scorm_upload_path(array('course_id' => $course_id ));
                    $this->ofabeeconverter->initialize_config($config);
                    $converted              = $this->ofabeeconverter->convert();
                    if($converted['success'] != 1) 
                    {
                        $response['error']      = true;
                        $response['message']    = $converted['message'];
                        echo json_encode($response); exit;
                    }   
                    else
                    {
                        $response['cl_filename']= $converted['cl_filename'];
                    } 
                }
            }
            else
            {
                $response['error']      = true;
                $response['message']    = $this->upload->display_errors();//lang('error_uploading_file');                
            }
        }
        else
        {
            $response['error']      = 'true';
            $response['message']    = lang('error_uploading_file');
        }
        //echo '<pre>';print_r($response);die;
        echo json_encode($response);
    }
    function upload_supportfile_to_home_server()
    {
        
        $course_id                  = $this->input->post('course_id');
        $lecture_id                 = $this->input->post('lecture_id');
        $response                   = array();
        $response['file_object']    = array();
        $response['error']          = 'false';
        $response['message']        = lang('file_uploaded_success').' '.  lang('file_conversion_stated');
        $directory                  = supportfile_upload_path(array('course_id' => $course_id));
        if(!file_exists($directory)){
            mkdir($directory, 0777, true);
        }
        $this->load->library('upload');
        //echo "<pre>"; print_r($this->input->post()); die();
        $config                     = array();
        $config['upload_path']      =  $directory;
        $config['allowed_types']    = 'mp3|mp4|mpeg|flv|avi|f4v|doc|odt|ods|odp|xlsx|docx|xls|pdf|ppt|pptx|png|jpg|jpeg|zip';  
        $config['encrypt_name']     = true;
        //$config                     = $this->get_upload_config($this->input->post('extension'), $course_id);
        if( $config )
        {
            $this->upload->initialize($config);
            $uploaded = $this->upload->do_upload('file');   
            
            if( $uploaded )
            {
                $upload_data                = $this->upload->data();
                $response['file_object']    = $upload_data;
                // if($upload_data['file_ext'] == '.zip')
                // {
                //     $this->load->library('ofabeeconverter'); 
                //     $config                 = array();
                //     $config['update_db']    = false;
                //     $config['file_name']    = $this->input->post('file_name');
                //     $config['input']        = $upload_data['full_path'];
                //     $config['output']       = scorm_upload_path(array('course_id' => $course_id ));
                //     $this->ofabeeconverter->initialize_config($config);
                //     $converted              = $this->ofabeeconverter->convert();
                //     if($converted['success'] != 1) 
                //     {
                //         $response['error']      = 'true';
                //         $response['message']    = $converted['message'];
                //         echo json_encode($response); exit;
                //     }   
                //     else
                //     {
                //         $response['cl_filename']= $converted['cl_filename']."?v=".rand(10,1000);
                //     } 
                // }
            }
            else
            {
                $response['error']      = 'true';
                $response['message']    = $this->upload->display_errors();//lang('error_uploading_file');                
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
    /*
        uploads the file , insert lecture, and convert the files
        combination of upload_tohome_server and save_lecture
    **/
    function upload_to_home_server_scorm()
    {
        $response                   = array();
        $response['file_object']    = array();
        $response['error']          = 'false';
        $response['message']        = lang('file_uploaded_success').' '.  lang('file_conversion_stated');
        $this->load->library('upload');
        // echo "<pre>"; print_r($this->input->post()); die();
        $course_id                  = $this->input->post('course_id');
        $config                     = $this->get_upload_config($this->input->post('extension'), $course_id);
        //echo '<pre>'; print_r($_FILES);die;
        if( $config )
        {
            
            $this->upload->initialize($config);
            $uploaded = $this->upload->do_upload('file');   
            
            if( $uploaded )
            {
                $upload_data                = $this->upload->data();
                $response['file_object']    = $upload_data;
                $lecture_id             = $this->input->post('lecture_id');
                $this->load->library('ofabeeconverter'); 
                $config                 = array();
                // $config['lecture_id']   = $lecture_id;
                $config['update_db']    = false;
                $config['file_name']    = $this->input->post('file_name');
                $config['input']        = $upload_data['full_path'];
                $config['output']       = scorm_upload_path(array('course_id' => $course_id ));
                // echo '<pre>'; print_r($config);die;
                $this->ofabeeconverter->initialize_config($config);
                $converted  = $this->ofabeeconverter->convert();
                
                if($converted['success'] != 1) {
                    $response['error']      = 'true';
                    $response['message']    = $converted['message'];
                    echo json_encode($response); exit;
                }
                
                $section_id         = $this->input->post('section_id');
                
                $save               = array();
                $save['id']         = $section_id;
                $save['s_name']     = $this->input->post('section_name'); 
                $save['s_course_id']= $this->input->post('course_id');
                if( !$section_id )
                {
                    if( $save['s_name'] == '' )
                    {
                        $response['error']   = 'true';
                        $response['message'].= lang('section_name_required');
                    }
                    else
                    {
                        if( $this->Course_model->section(array('filter_id'=>$save['id'],'course_id'=>$save['s_course_id'],'name'=>$save['s_name'], 'deleted'=>'0')) > 0 )
                        {
                            $response['error']   = 'true';
                            $response['message'].= lang('section_name_not_available');
                        }
                    }
                }
                
                if( $response['error'] == "true")
                {
                    echo json_encode($response);exit;            
                }
                if( !$section_id )
                {
                    $total              = $this->Course_model->sections(array('count'=>true, 'course_id'=> $save['s_course_id']));
                    $save['s_order_no'] = ($total)+1;
                    $section_id         = $this->Course_model->save_section($save);
                }
                $save                                      = array();                
                if(isset($_FILES['lecture_image']) && $_FILES['lecture_image']['error'] !== 4)
                { 
                    $allowed =  array('gif','png' ,'jpg', 'jpeg');
                    $filename = $_FILES['lecture_image']['name'];
                    $ext = pathinfo($filename, PATHINFO_EXTENSION);
                    if(in_array($ext,$allowed))
                    {

                        $lecture_id                         = $this->input->post('lecture_id');
                        $version                            = rand(0,3);
                        if($this->upload_course_lecture_image_to_localserver(array('course_id'=>$this->input->post('course_id'), 'lecture_id'=>$this->input->post('lecture_id'))))
                        {
                            $save['cl_lecture_image']               =  $lecture_id.".jpg?v=".$version;
                        }
                    }
                    
                }                                                                        
                $save['id']                                = $this->input->post('lecture_id');
                $save['cl_section_id']                     = $section_id;
                $save['cl_course_id']                      = $this->input->post('course_id');
                $save['cl_lecture_name']                   = $this->input->post('lecture_name');
                $save['cl_lecture_description']            = $this->input->post('lecture_description');
                $save['cl_limited_access']                 = '0';
                $save['cl_lecture_type']                   = $this->__lecture_type_keys_array['scorm'];
                $save['cl_conversion_status']              = 3;
                $save['cl_filename']                       = $converted['cl_filename']."?v=".rand(10,1000);
                $save['action_by']                         = $this->auth->get_current_admin('id');
                if( $save['id'] )
                {
                    $save['updated_date']                  = date('Y-m-d H:i:s');
                }
                        
                $lecture_id =  $this->Course_model->save_lecture($save);
                $user_data              = array();
                $user_data['user_id']   = $this->__loggedInUser['id'];
                $user_data['username']  = $this->__loggedInUser['us_name'];
                $user_data['useremail']  = $this->__loggedInUser['us_email'];
                $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
                
                $message_template                     = array();
                $message_template['username']         = $this->__loggedInUser['us_name'];;
                $message_template['lecture_name']     =  $save['cl_lecture_name'];
                $message_template['course_name']      =  $this->input->post('course_name');
        
                $triggered_activity     = 'lecture_created';
                log_activity($triggered_activity, $user_data, $message_template);
                $response['error'] = 'false';
                $response['id']     = $lecture_id;
                $response['message'] = 'Uploaded successfully.';
                
            }
            else
            {
                $response['error']      = 'true';
                $response['message']    = $this->upload->display_errors();//lang('error_uploading_file');                
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
    
    function upload_from_dropbox()
    {
       // echo '<pre>'; print_r(json_decode($_POST['file']));die;
     //    echo '<pre>'; print_r($_POST);die;
        $response               = array();
        $response['status']     = 'false';
        $response['message']    = lang('video_saved_success');
        
        //saving section details
        $section_id = $this->input->post('section_id');
        if( !$section_id )
        {
            $section                 = array();
            $section['id']           = $section_id;
            $section['s_course_id']  = $this->input->post('course_id');
            $section['s_name']       = $this->input->post('section_name');                    
            $section_id              = $this->Course_model->save_section($section);
        }
        $save                                      = array();                
        $save['id']                                = $this->input->post('lecture_id');
        $save['cl_section_id']                     = $section_id;
        $course_id                                 = $this->input->post('course_id');
        $save['cl_course_id']                      = $course_id;
        $save['cl_lecture_name']                   = $this->input->post('lecture_name');
        $save['cl_lecture_description']            = $this->input->post('lecture_description');
        $save['cl_sent_mail_on_lecture_creation']  = $this->input->post('sent_mail_on_lecture_creation');
        $save['cl_limited_access']                 = $this->input->post('lecture_access_limit');
        $save['cl_limited_access']                 = ($save['cl_limited_access'] )?$save['cl_limited_access'] :'0';
        $save['cl_conversion_status']              = '1'; //upload completed
        $save['action_by']                         = $this->auth->get_current_admin('id');
        if( $save['id'] )
        {
            $save['action_id']                         = $this->actions['update'];
            $save['updated_date']                      = date('Y-m-d H:i:s');
        }
        else
        {
            $save['action_id']                         = $this->actions['create'];            
            $highest_order                             = $this->Course_model->lectures(array('count'=>true, 'section_id'=>$section_id, 'course_id'=> $save['cl_course_id']));
            $save['cl_order_no']                       = $highest_order+1;
        }
                
        $lecture_id =  $this->Course_model->save_lecture($save);
        
        $file           = json_decode($this->input->post('file'));
        $s3_setting     = $this->settings->setting('has_s3');        
        $has_s3         = false;
        if( $s3_setting['as_superadmin_value'] && $s3_setting['as_siteadmin_value'] )
        {
            $has_s3 = true;
        }
        $this->load->library('ofabeeconverter'); 
        $file_name             = $this->input->post('file_name'); 
        $save                  = array();
        $save['id']            = $lecture_id;
        $save['cl_filename']   = $this->ofabeeconverter->remove_extension($file_name);
        $extension                      = $this->ofabeeconverter->extension($file_name);
        if(in_array($extension, $this->__document_types))
        {
            $save['cl_lecture_type']    =  $this->__lecture_type_keys_array['document'];
        }
        $this->Course_model->save_lecture($save);
        $extension          = $this->input->post('extension');
        $engine             = $this->input->post('engine');
        $destination_paths  = array('video'=>  video_upload_path(array('course_id' => $course_id)), 'document' => document_upload_path(array('course_id' => $course_id)));
        $source_path        = $file->link;
        $destination_path   = $destination_paths[$engine];
        
        
        $config                 = array();
        if( $has_s3 )
        {   
            //uploadToS3($source_path,  $destination_path.$file_name);
            //copy($source_path, $destination_path.$file_name);            
            $config['input']        = $source_path;
            $config['file_name']    = $file_name;
            $input                  = $config['input'];
        }
        else
        {
            $destination_path   = $this->ofabeeconverter->absolute_path().$destination_path;
            copy($source_path, $destination_path.$file_name);            
            $config['input']    = $destination_path.$file_name;
            $input              = $config['input'];
        }
                       
        /*$config['s3_upload']    = $has_s3;
        $config['lecture_id']   = $lecture_id;
        $config['target_url']   = admin_url('coursebuilder/launch_conversion');
        $this->ofabeeconverter->initialize($config);*/
        
        //saving data in conversion queue
        $save                       = array();
        $save['id']                 = false;
        $save['lecture_id']         = $lecture_id;
        $save['s3_upload']          = ($has_s3)?'1':'0';
        $save['file_path']          = $input."?v=".rand(10,1000);
        $save['conversion_status']  = '1';//upload completed
        $this->Course_model->save_conversion_queue($save);
        //End
            
        $response['lecture_id']     = $lecture_id;
        echo json_encode($response); 
    }
    
    function test_conversion($queue_id=0)
    {
        $queue = $this->Course_model->conversion_queue_by_id($queue_id);
        if(!empty($queue))
        {
            //converstion starts
            $this->load->library('ofabeeconverter'); 
            $config                 = array();
            $config['queue_id']     = $queue['id'];
            $config['input']        = $queue['file_path'];
            $config['s3_upload']    = ($queue['s3_upload'])?true:false;
            $config['from_cisco']   = ($queue['from_cisco'])?true:false;
            $config['lecture_id']   = $queue['lecture_id'];
            $config['output']       = ($queue['output_path'])?$queue['output_path']:$this->get_output_path($queue['file_path']);
            $config['target_url']   = admin_url('coursebuilder/launch_conversion');
            $this->ofabeeconverter->initialize_config($config);
            $this->ofabeeconverter->convert();
            //end
        }
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
                $upload_data  	  =  $this->upload->data();
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
    function save_section()
    {
        $response           = array();
        $response['error']  = 'false';
        $response['message']= lang('section_created_success');
        
        $save               = array();
        $save['id']         = $this->input->post('section_id');
        $save['s_name']     = $this->input->post('section_name');
        $save['s_course_id']= $this->input->post('course_id');
        $course_title       = $this->input->post('course_name');
        if(trim($save['s_name']) == '' )
        {
            $response['error']  = 'true';
            $response['message']= lang('section_name_required');
            echo json_encode($response);exit;            
        }
        if( $this->Course_model->section(array('filter_id'=>$save['id'], 'course_id'=>$save['s_course_id'], 'name'=>$save['s_name'])) > 0 )
        {
            $response['error']  = 'true';
            $response['message']= lang('section_name_not_available');
            echo json_encode($response);exit;
        }
        $mstatus = 'section_created'; 
        if(!empty($save['id'])){
            
            $mstatus = 'section_updated'; 
        }
        
        $save['action_by']      = $this->auth->get_current_admin('id');
        $save['action_id']      = '1';
        $response['id']         = $this->Course_model->save_section($save);
        // var_dump($_FILES);die;
        if(isset($_FILES['section_image']) && $_FILES['section_image']['error'] !== 4)
        { 
            $allowed =  array('gif','png' ,'jpg', 'jpeg');
            $filename = $_FILES['section_image']['name'];
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if(in_array($ext,$allowed))
            {
                $version                    = rand(0,300);
                $this->upload_course_section_image_to_localserver(array('course_id'=>$this->input->post('course_id'), 'section_id'=>$response['id'] ));
                $save_sec['id']             = $response['id'];
                $save_sec['s_image']        = $response['id'].".jpg?v=".$version;
                $response['id']             = $this->Course_model->save_section($save_sec);
            }

        }
        $section                = $this->Course_model->section(array('id'=>$response['id']));
        $response['s_status']   = $section['s_status'];
        //saving sort order
        $structure  = $this->input->post('structure');
        parse_str($structure, $structure);
        if( !$save['id'] && isset($structure['section_wrapper']) && !empty($structure['section_wrapper']))
        {
            $last_section_order = 0;
            foreach ($structure['section_wrapper'] as $order => $id) {
                $sort_order               = array();
                $sort_order['s_order_no'] = ($order)+1;
                if($id == 0)
                {
                    $has_new_section          = true;
                    $sort_order['id']         = $response['id'];
                }
                else
                {
                    $sort_order['id']         = $id;
                }
                $last_section_order           = $sort_order['s_order_no'];
                $this->Course_model->save_section($sort_order);
            }
            if(!isset($has_new_section))
            {
                $sort_order = array();
                $sort_order['id'] = $response['id'];
                $sort_order['s_order_no'] = $last_section_order+1;
                $this->Course_model->save_section($sort_order);
            }
        }
        //end
        $user_data              = array();
        $user_data['user_id']   = $this->__loggedInUser['id'];
        $user_data['username']  = $this->__loggedInUser['us_name'];
        $user_data['useremail']  = $this->__loggedInUser['us_email'];
        $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
        $message_template                   = array();
        $message_template['username']       = $this->__loggedInUser['us_name'];;
        $message_template['course_name']    = $course_title ;
        $message_template['section_name']   = $save['s_name'] ;
        $triggered_activity                 = $mstatus;
        $this->invalidate_course(array('course_id' => $save['s_course_id']));
        $this->invalidate_course();
        log_activity($triggered_activity, $user_data, $message_template);
        echo json_encode($response);
    }
    
    function upload_course_section_image_to_localserver($param=array())
    { 
        $course_id              = isset($param['course_id'])?$param['course_id']:'default';
        $section_id             = isset($param['section_id'])?$param['section_id']:'default';
        $directory              = course_upload_path(array('course_id' => $course_id))."section/";
        if(!file_exists($directory)){
            mkdir($directory, 0777, true);
        }
        $this->load->library('upload');
        $config                     = array();
        $config['upload_path']      =  $directory;
        $config['allowed_types']    = 'gif|jpg|png|jpeg';
        $new_name                   = $section_id.'.jpg';
        $config['file_name']        = $new_name;
        $config['overwrite']        = TRUE;
        $this->upload->initialize($config);
        $this->upload->do_upload('section_image');
        $uploaded_data = $this->upload->data();
        
        $config                     = array();
        $config['uploaded_data']    = $uploaded_data;
        // echo '<pre>';print_r($uploaded_data);die;
        //convert to given size and return orginal name 
        $config['course_id']        = $course_id;
        $config['section_id']       = $section_id;
        $config['width']            = 500;
        $config['height']           = 354;
        $config['orginal_name']     = true;
        $new_file                   = $this->crop_image($config);//orginal name
        // var_dump($this->upload->do_upload('section_image'));die;
        // var_dump( $new_file   );die;
        $config['width']            = 300;
        $config['height']           = 160;
        $config['orginal_name']     = false;
        $new_file_medium            = $this->crop_image($config);
        
        $config['width']            = 85;
        $config['height']           = 85;
        $config['orginal_name']     = false;
        $new_file_small             = $this->crop_image($config);
        
        $has_s3     = $this->settings->setting('has_s3');
        $directory              = course_upload_path(array('course_id' => $course_id))."section/";

        if( $has_s3['as_superadmin_value'] && $has_s3['as_siteadmin_value'] )
        {
            $file_path          = course_upload_path(array('course_id' => $course_id))."section/".$new_file;
            $file_medium_path   = course_upload_path(array('course_id' => $course_id))."section/".$new_file_medium;
            $file_small_path    = course_upload_path(array('course_id' => $course_id))."section/".$new_file_small;
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
        if(file_exists($source_path))
        {
        
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
    
    function file_s3_copy($param = array())
    {
        $configs                = array();
        $s3_configured          = false;
        $s3_multipart_settings  = $this->settings->setting('has_s3');
        if($s3_multipart_settings['as_superadmin_value'] && $s3_multipart_settings['as_siteadmin_value'])
        {
            if(isset($s3_multipart_settings['as_setting_value']) && isset($s3_multipart_settings['as_setting_value']['setting_value']))
            {
                $s3_configured          = true;
                $configs['settings']    = $s3_multipart_settings['as_setting_value']['setting_value'];
            }
        }
       
        if($s3_configured)
        {
            $configs['folderpath']  = '';
            $this->load->library('multipartupload', $configs);
            $this->multipartupload->copy_s3_file($param);
        
        }
        else
        {
            echo json_encode(array('error' => true, 'message' => 'Seems multipart not configured.'));
        }
    }

    function import_lecture()
    {
        $response                       = array();
        if($this->input->method() == 'post')
        {

            // input validate start
            $course_id                      = ( null != $this->input->post('course_id'))? $this->input->post('course_id'):false;
            $lecture_ids                    = ( null != $this->input->post('lecture_selected'))?json_decode($this->input->post('lecture_selected')):false;
            $lecture_ids                    = ($lecture_ids && !empty($lecture_ids))?$lecture_ids:false;

            if( $course_id  && $lecture_ids )
            {

                if( sizeof($lecture_ids) == 0 )
                {
                    $response['error']          = 'true';
                    $response['message']        = lang('choose_atleast_one_lecture');
                    echo json_encode($response);
                    die;
                              
                }
                else 
                {
                    $section_id                     = ( null != $this->input->post('section_id'))?$this->input->post('section_id'):false;
                    
                    if( $section_id == false )
                    {
                        $section_name                   = ( null != $this->input->post('section_name') )?trim($this->input->post('section_name')):false;
                        
                        if( $section_name )
                        {
                            

                            if( $this->Course_model->section(array('filter_id'=>$section_id, 'name'=>$section_name, 'deleted'=>'0' ,'course_id'=>$course_id)) > 0 )
                            {
                                $response['error']          = 'true';
                                $response['message']        = lang('section_name_not_available');
                                echo json_encode($response);
                                die;
                                
                            } 
                            else
                            {
                                $save                           = array();
                                $save['id']                     = false;
                                $save['s_name']                 = $section_name;
                                $save['s_course_id']            = $course_id;
                                $total                          = $this->Course_model->sections(array('count'=>true, 'course_id'=> $course_id));
                                $save['s_order_no']             = ($total)+1;
                                $save['action_by']              = $this->auth->get_current_admin('id');
                                $save['action_id']              = $this->actions['create'];            
                                $section_id                     = $this->Course_model->save_section($save);
                            }
                        }
                        else
                        {
                            $response['error']       = 'true';
                            $response['message']     = lang('section_name_required');
                            echo json_encode($response);
                            die;
                           
                        }
                      
                    }

                    $sent_mail_on_import_creation           = $this->input->post('sent_mail_on_import_creation');

                    // import logic start here

                      // s3 
                    $s3_settings                            = $this->settings->setting('has_s3');
                    $has_s3_enabled                         = ($s3_settings['as_superadmin_value'] && $s3_settings['as_siteadmin_value']);

                    if(!$has_s3_enabled)
                    {
                        //ofabee s3
                        $s3_setting_ofabee                  = $this->settings->setting('has_s3_ofabee');
                        $s3_setting_ofabee                  = $s3_setting_ofabee['as_setting_value']['setting_value'];
                    }
                

                    //content security
                    $content_security                       = $this->settings->setting('has_content_security');
                    $content_security_status                = $content_security['as_setting_value']['setting_value']->content_security_status;


                    

                  
                    $response['error']                      = 'false';
                    $response['message']                    = lang('lecture_imported_success'); 
                    

                    if(is_array($lecture_ids))
                    {
                        $response['lectures']               = array();
                        $error_import                       = array();
                        $file_move_to_copy_queue            = array();

                        foreach ( $lecture_ids as $lecture_id )
                        {
                            $save                           = $this->Course_model->lecture(array('id'=>$lecture_id));
                            $commonfileto_copy_queue        = array();
                            $org_file_name                  = $save['cl_org_file_name'];
                            
                            $cl_conversion_status             = '6';

                            if(!empty($save))
                            {
                                //  parent_course_id - To identity leture is imported lecture
                                $parent_course_id           = $save['cl_course_id'];                                                
                                $is_video_file              = false;
                                // $not_quiz_and_survey        = (($save['cl_lecture_type'] != $this->__lecture_type_keys_array['quiz']) && ($save['cl_lecture_type'] != $this->__lecture_type_keys_array['survey']));
                                

                                $save['id']                                         = false;
                                $save['action_by']                                  = $this->auth->get_current_admin('id');
                                $save['action_id']                                  = $this->actions['create'];            
                                $save['cl_section_id']                              = $section_id;
                                $save['cl_course_id']                               = $course_id;
                                $save['cl_status']                                  = '0';
                                $save['cl_sent_mail_on_lecture_creation']           = $sent_mail_on_import_creation;
                                
                                if( (null != $save['cl_access_restriction']) && (!empty($save['cl_access_restriction'])) )
                                {
                                    $cl_access_restriction             =  json_decode($save['cl_access_restriction'],true) ;
                                    if(is_array($cl_access_restriction) && (!empty($cl_access_restriction)) )
                                    {
                                        if(isset($cl_access_restriction['activities']))
                                        {
                                            unset($cl_access_restriction['activities']);
                                            
                                            $cl_access_restriction              = (is_array($cl_access_restriction))?json_encode($cl_access_restriction):null;
                                            $save['cl_access_restriction']      = (is_string($cl_access_restriction))?$cl_access_restriction:null;
                                        } 
                                    }
                                    else
                                    {
                                        $save['cl_access_restriction'] = null;   
                                    }
                                }
                                
                                

                                if( (null != $save['cl_conversion_status']) && (!empty($save['cl_conversion_status'])) )
                                {
                                   
                                    $Is_quiz_or_survey_or_certificate    = (    ($this->__lecture_type_keys_array['quiz'] == $save['cl_lecture_type'])          ||
                                                                                ($this->__lecture_type_keys_array['survey'] == $save['cl_lecture_type'])        || 
                                                                                ($this->__lecture_type_keys_array['certificate'] == $save['cl_lecture_type'])
                                                                            );                                               
                                    if ($Is_quiz_or_survey_or_certificate)
                                    {
                                        $save['cl_conversion_status']  = '3';
                                    }
                                    else
                                    {
                                        $save['cl_conversion_status']           = '6';  
                                        $Is_youtube_or_vimeo_or_html            = ( ($this->__lecture_type_keys_array['youtube'] == $save['cl_lecture_type'])  || 
                                                                                    ($this->__lecture_type_keys_array['vimeo'] == $save['cl_lecture_type'])    || 
                                                                                    ($this->__lecture_type_keys_array['text'] == $save['cl_lecture_type'])  
                                                                                    );
                                        if( $Is_youtube_or_vimeo_or_html )
                                        {
                                            $save['cl_conversion_status'] = (null != $save['cl_support_files'] )?'6':'3';
                                        }
                                        else
                                        {
                                          
                                            $Is_assignment            = ( $this->__lecture_type_keys_array['descriptive_test'] == $save['cl_lecture_type']);
                                            if( $Is_assignment )
                                            {
                                               
                                                $descriptive_test      = $this->Course_model->get_descriptive_test_item($lecture_id);
                                                if(( null != $descriptive_test) &&  !empty($descriptive_test['dt_uploded_files']) )
                                                {
                                                   
                                                    $dt_files                       = json_decode($descriptive_test['dt_uploded_files'],true);
                                                  
                                                    $save['cl_conversion_status']   = (is_array($dt_files) && count($dt_files) > 0)?'6':'3';
                                                   
                                                }
                                                if(($save['cl_conversion_status'] == '3') && (null != $save['cl_support_files']) && (!empty($save['cl_support_files'])) )
                                                {
                                                   
                                                    $cl_support_files                       = json_decode($save['cl_support_files'],true);
                                                   
                                                    $save['cl_conversion_status']           = (is_array($cl_support_files) && (count($cl_support_files) > 0) )?'6':'3';
                                                }
                                                                                             
                                                   
                                                    
                                                  
                                             
                                                
                                               
                                            }                                                       
                                        }
                                        
                                      
                                    }
                                   
                                }

                               

                                $save['cl_parent_course_id']                = $parent_course_id;
                                $save['cl_parent_lecture_id']               = $lecture_id;
                                 // cl_conversion_status 
                                 //  6 - copy_progress 
                                 //  3 - lecture import completed


                                

                                $new_lecture_id                             = $this->Course_model->save_lecture($save);


                                $is_certificate                             = ($this->__lecture_type_keys_array['certificate'] == $save['cl_lecture_type']);
    
                                if( !empty($save['cl_org_file_name'])  &&  !$is_certificate)
                                {
                                    if( $has_s3_enabled )
                                    {
                                        $ext                        = explode('.',$save['cl_org_file_name']);
                                        $file_name                  = $ext[0];
                                        $ext                        = explode('?',$ext[1]);
                                        $ext                        = $ext[0];
                                        $is_video_file              = in_array($ext, $this->__video_types);
                                        $file_name_full             = $file_name.'.'.$ext;
                                        $lecture_source             = $this->get_upload_config($ext, $parent_course_id);
                                        $lecture_source_path        = $lecture_source['upload_path'];     
                                        $lecture_source_path        = $lecture_source_path.$file_name_full;

                                        $lecture_destination        = $this->get_upload_config($ext, $course_id);
                                        $lecture_destination_path   = $lecture_destination['upload_path'];
                                        $lecture_destination_path   = $lecture_destination_path.$file_name_full;

                                        $s3_file_copy_param         = array(    'source_path'       => $lecture_source_path,
                                                                                'target_path'       => $lecture_destination_path
                                                                            );
                                      
                                    }
                                    else
                                    {
                                        $ext                        = explode('.',$save['cl_org_file_name']);
                                        $file_name                  = $ext[0];
                                        $ext                        = explode('?',$ext[1]);
                                        $ext                        = $ext[0];
                                        $is_video_file              = in_array($ext, $this->__video_types);

                                        $lecture_source             = $this->get_upload_config($ext, $parent_course_id);
                                        $file_name_full             = $file_name.'.'.$ext;
                                        $lecture_source_path        = $lecture_source['upload_path'].$file_name_full;
                                        $lecture_destination        = $this->get_upload_config($ext, $course_id);
                                        $lecture_destination_path   = $lecture_destination['upload_path'];
                                       

                                        $this->make_directory($lecture_destination_path);

                                    }

                                    $file_to_copy_queue['source']          = $lecture_source_path;
                                    $file_to_copy_queue['destination']     = $lecture_destination_path;
                                    $file_to_copy_queue['status']          = '0';
                                    $file_to_copy_queue['lecture_id']      = $new_lecture_id;

                                   
                                    array_push($file_move_to_copy_queue, $file_to_copy_queue);
                                   
                                
                                }
                                //lecture image 
                                if( !empty($save['cl_lecture_image'])  && $save['cl_lecture_image'] != 'default-lecture.jpg')
                                {
                                    $file_name                             = explode('?',$save['cl_lecture_image']);
                                    $file['file_raw_name']                 = $file_name[0];
                                    $file_with_exist_path                  = course_lecture_upload_path(array('course_id' => $parent_course_id)).$file['file_raw_name'];
                                    if(!$has_s3_enabled)
                                    {
                                        $directory_param                       = array('course_id' => $course_id);
                                        $directory                             = course_lecture_upload_path($directory_param);
                                        $this->make_directory($directory);
                                    }
                                    $file_with_target_path                 = course_lecture_upload_path(array('course_id' => $course_id)).$file['file_raw_name'];
                                    $s3_file_copy_param                    = array(    'source_path'       => $file_with_exist_path,
                                                                                        'target_path'       => $file_with_target_path
                                                                                    );
                                    // $this->file_s3_copy($s3_file_copy_param);
                                    $file_to_copy_queue['source']          = $file_with_exist_path;
                                    $file_to_copy_queue['destination']     = $file_with_target_path;
                                    $file_to_copy_queue['status']          = '0';
                                    $file_to_copy_queue['lecture_id']      = $new_lecture_id;

                                    if(!$has_s3_enabled)
                                    {
                                        if(!file_exists(course_lecture_upload_path(array('course_id' => $course_id)))){
                                            mkdir(course_lecture_upload_path(array('course_id' => $course_id)), 0777, true);
                                        }
                                    }
                                   
    
                                    array_push($file_move_to_copy_queue, $file_to_copy_queue);
                                }


                                if( !empty($save['cl_support_files']) )
                                {
                                    $support_files              = json_decode($save['cl_support_files'],true);
                                    if(!empty($support_files))
                                    {
                                        if( $has_s3_enabled )
                                        {
                                            
                                            foreach( $support_files as $files_key => $file)
                                            {
                                                $file_name                             = explode('?',$file['file_raw_name']);
                                                $file['file_raw_name']                 = $file_name[0];
                                                $file_with_exist_path                  = supportfile_upload_path(array('course_id' => $parent_course_id)).$file['file_raw_name'];
                                                $file_with_target_path                 = supportfile_upload_path(array('course_id' => $course_id)).$file['file_raw_name'];
                                                $s3_file_copy_param                     = array(    'source_path'       => $file_with_exist_path,
                                                                                                    'target_path'       => $file_with_target_path
                                                                                                );
                                                // $this->file_s3_copy($s3_file_copy_param);
                                                $file_to_copy_queue['source']          = $file_with_exist_path;
                                                $file_to_copy_queue['destination']     = $file_with_target_path;
                                                $file_to_copy_queue['status']          = '0';
                                                $file_to_copy_queue['lecture_id']      = $new_lecture_id;
                
                                                array_push($file_move_to_copy_queue, $file_to_copy_queue);
                                            }
                                            
                                        }
                                        else
                                        {
                                            foreach( $support_files as $files_key => $file)
                                            {
                                                $directory                             = supportfile_upload_path(array('course_id' => $course_id));
                                                $file_with_exist_path                  = supportfile_upload_path(array('course_id' => $parent_course_id)).$file['file_raw_name'];
                                                $file_with_target_path                 = supportfile_upload_path(array('course_id' => $course_id)).$file['file_raw_name'];
                                                if(!file_exists($directory)){
                                                    mkdir($directory, 0777, true);
                                                }
                                                // $support_file_copy_command    = 'cp -rf '.$file_with_exist_path.' '.$file_with_target_path;
                                                // shell_exec($support_file_copy_command. " > /dev/null &");

                                                $file_to_copy_queue['source']          = $file_with_exist_path;
                                                $file_to_copy_queue['destination']     = $file_with_target_path;
                                                $file_to_copy_queue['status']          = '0';
                                                $file_to_copy_queue['lecture_id']      = $new_lecture_id;
                
                                                array_push($file_move_to_copy_queue, $file_to_copy_queue);
                                            }
                                        }
                                        
                                    }

                                   
                                
                                }       



                                switch($save['cl_lecture_type'])
                                {
                                    case $this->__lecture_type_keys_array['survey']:

                                        {
                                            
                                           
                                            $survey                                      = $this->Course_model->get_survey(array('lecture_id' => $lecture_id ,'select' => 'survey.*'));
                                        
                                            $save_survey                                 = array();
                                            $save_survey['id']                           = false;
                                            $save_survey['s_course_id']                  = $course_id;
                                            $save_survey['s_lecture_id']                 = $new_lecture_id;
                                            $save_survey['s_name']                       = $survey['s_name'];
                                            $save_survey['s_description']                = $survey['s_description'];
                                            $save_survey['s_tutor_id']                   = $survey['s_tutor_id'];
                                            $save_survey['s_tutor_name']                 = $survey['s_tutor_name'];
                                            $save_survey['created_by']                   = $this->auth->get_current_admin('id');
                                            $survey_id                                   = $this->Course_model->save_survey($save_survey);

                                            $survey_questions                            = $this->Course_model->survey_questions(array('survey_id'=>$survey['id']));

                                            foreach($survey_questions as $survey_q_keys => $suervery_question )
                                            {
                                                $save_survey_question                       = $suervery_question;

                                                $save_survey_question['id']                 = false;
                                                $save_survey_question['sq_survey_id']       = $survey_id;
                                                $save_survey_question['sq_course_id']       = $course_id;
                                                $save_survey_question['sq_lecture_id']      = $new_lecture_id;
                                                
                                                $this->Course_model->save_question_survey($save_survey_question);

                                            }
                                            break;
                                        }
                                    case $this->__lecture_type_keys_array['quiz']:
                                        {
                                            
                                            $assesment                              = array();
                                            $assesment['old_lecture']               = $save;
                                            $assesment['section_id']                = $section_id;
                                            $assesment['course_id']                 = $course_id;
                                            $assesment['sent_mail']                 = $sent_mail_on_import_creation;
                                            $assesment['new_lecture_id']            = $new_lecture_id;
                                            $assesment['parent_lecture_id']         = $lecture_id;;
                                            $assesment['parent_course_id']          = $parent_course_id;
                                            $this->import_assesment($assesment);
                                            break;
                                        }
                                    case $this->__lecture_type_keys_array['descriptive_test'] :
                                        {
                                                
                                            $param                                  = array('course_id' => $course_id, 'purpose' => 'assignment');
                                            $directory                              = assignment_upload_path($param);
                                            $this->make_directory($directory);
                                            $descriptive_test                       = $this->Course_model->get_descriptive_test_item($lecture_id);
                                            
                                            if(!empty($descriptive_test))
                                            {
                                            
                                                    $descriptive_files              = json_decode($descriptive_test['dt_uploded_files'],true);
                                                
                                                    if(!empty($descriptive_files))
                                                    {
                                                        if( $has_s3_enabled )
                                                        {
                                                            foreach( $descriptive_files as $files_key => $file)
                                                            {
                                                                //    $directory                             = assignment_upload_path(array('course_id' => $course_id,'purpose'=>'assignment'));
                                                                    $file_name                              = explode('?',$file['file_name']);
                                                                    $file_with_exist_path                   = assignment_upload_path(array('course_id' => $parent_course_id,'purpose'=>'assignment')).$file_name[0];
                                                                    $file_with_target_path                  = assignment_upload_path(array('course_id' => $course_id,'purpose'=>'assignment')).$file_name[0];
                                                                    $s3_file_copy_param                     = array(    'source_path'       => $file_with_exist_path,
                                                                                                                        'target_path'       => $file_with_target_path
                                                                                                                    );
                                                                // $this->file_s3_copy($s3_file_copy_param);
                                                                $file_to_copy_queue                    = array();
                                                                $file_to_copy_queue['source']          = $file_with_exist_path;
                                                                $file_to_copy_queue['destination']     = $file_with_target_path;
                                                                $file_to_copy_queue['status']          = '0';
                                                                $file_to_copy_queue['lecture_id']      = $new_lecture_id;
                                                                // $datatest[] = $file_to_copy_queue;
                                                                array_push($file_move_to_copy_queue, $file_to_copy_queue);
                                                            }
                                                            
                                                        
                                                            
                                                        }
                                                        else
                                                        {
                                                            foreach( $descriptive_files as $files_key => $file)
                                                            {
                                                                //    $directory                             = assignment_upload_path(array('course_id' => $course_id,'purpose'=>'assignment'));
                                                                    $file_name                              = explode('?',$file['file_name']);
                                                                    $file_with_exist_path                   = assignment_upload_path(array('course_id' => $parent_course_id,'purpose'=>'assignment')).$file_name[0];
                                                                    $file_with_target_path                  = assignment_upload_path(array('course_id' => $course_id,'purpose'=>'assignment')).$file_name[0];
                                                                
                                                            
                                                                    $file_to_copy_queue                    = array();
                                                                    $file_to_copy_queue['source']          = $file_with_exist_path;
                                                                    $file_to_copy_queue['destination']     = $file_with_target_path;
                                                                    $file_to_copy_queue['status']          = '0';
                                                                    $file_to_copy_queue['lecture_id']      = $new_lecture_id;
                                                                    array_push($file_move_to_copy_queue, $file_to_copy_queue);
                                                                    //     $support_file_copy_command              = 'cp -rf '.$file_with_exist_path.' '.$file_with_target_path;
                                                                
                                                                // shell_exec($support_file_copy_command. " > /dev/null &");
                                                            }
                                                        }
                                                        
                                                    }
                                                    
                                                    unset($descriptive_test['id']);
                                                    $descriptive_test['dt_course_id']           = $course_id;
                                                    $descriptive_test['dt_lecture_id']          = $new_lecture_id;
                                                    
                                                     $this->Course_model->save_descriptive_test($descriptive_test);
                                            }
                                            
                                            break;
                                        }
                                    default:
                                    {

                      
                                        if(null != $org_file_name )
                                        {
                                            $ext                        = explode('.',trim($org_file_name));
                                            $file_name                  = $ext[0];
                                            if( null != $ext[1] )
                                            {
                                                $ext                        = explode('?',$ext[1]);
                                                $ext                        = $ext[0];
                                                $is_video_file              = in_array($ext, $this->__video_types);

                                                // video import and conversion start
                                                // echo $content_security_status;die;
                                                if(isset($is_video_file) && $is_video_file)
                                                {
                                                    
                                                    if($content_security_status == 1 )
                                                    {            
                                                        $queue                          = array();
                                                        if($content_security_status)
                                                        {
                                                            
                                                            $file_name_full                        = $file_name.'.'.$ext;
                                                                
                                                            $lecture_source                        = $this->get_upload_config($ext, $parent_course_id);
                                                            $lecture_source_path                   = $lecture_source['upload_path'];     
                                                            $lecture_source_path                   = $lecture_source_path;
                                                        
                                                            $lecture_destination                   = $this->get_upload_config($ext, $course_id);
                                                            $lecture_destination_path              = $lecture_destination['upload_path'];
                                                            $lecture_destination_path              = $lecture_destination_path;
                                                            
                                
                                                            $file_to_copy_queue                    = array();
                                                            $file_to_copy_queue['source']          = $lecture_source_path;
                                                            $file_to_copy_queue['destination']     = $lecture_destination_path;
                                                            $file_to_copy_queue['status']          = '0';
                                                            $file_to_copy_queue['lecture_id']      = $new_lecture_id;

                                                            
                                
                                                            array_push($file_move_to_copy_queue, $file_to_copy_queue);
                                                        
                                                        
                                                        }
                                                        // else
                                                        // {
                                                        //     $file_path                      = video_path(array('course_id' => $course_id)).$file_name_full;
                                                        //     $output_path                    = '/'.video_upload_path(array('course_id' => $course_id));
                                                        //     $queue['conversion_status']     = '1';//upload completed    
                                                        // }
                                                        
                                                        
                                                        // $file_name_with_path                = $file_path;
                                                        // $queue['id']                        = false;
                                                        // $queue['lecture_id']                = $lecture_id;
                                                        // $queue['s3_upload']                 = '0';
                                                        // $queue['from_cisco']                = '0';
                                                        // $queue['file_path']                 = $file_path;
                                                        // $queue['output_path']               = $output_path;
                                                        // $this->Course_model->save_conversion_queue($queue);
                                                    }
                                                    else
                                                    {
                                                        
                                                        $vimeo_video_data                           = array();
                                                        $vimeo_lecture_id                           = $lecture_id;
                                                        $vimeo_video_data['id']                     = $vimeo_lecture_id;
                                                        $vimeo_video_data['type']                   = isset($save['cl_lecture_type'])?$save['cl_lecture_type']:1;
                                                        $vimeo_video_data['cl_lecture_name']        = $save['cl_lecture_name'];
                                                        $vimeo_video_data['cl_lecture_description'] = $save['cl_lecture_description'];
                                                        $vimeo_video_data['cl_org_file_name']       = $save['cl_org_file_name'];
                                                        $from_s3                                    = false;
                                                        
                                                        $file_path                                  = video_path(array('course_id' => $course_id)).$file_name_full;
                                                        
                                                        $file_name_with_path                        = $file_path;
                                                        $is_need_vimeo_conversion                   = false;
                                                            
                                                        if(isset($lecture_id) && isset( $file_name_full ) && $save['cl_lecture_type'] == 1 &&  $is_need_vimeo_conversion )
                                                        {
                                                                
                                                            $this->load->library('Vimeoupload');
                                                            $this->vimeoupload->set_config();
            
                                                            if($from_s3)
                                                            {
                                                                $video_url                  = "https://".$s3_setting_ofabee->s3_bucket.".s3.amazonaws.com/".video_upload_path(array('course_id' => $course_id)).$vimeo_video_data['cl_org_file_name'];
                                                                $video_url                  = $s3_setting_ofabee ? $video_url : $vimeo_video_data['cl_org_file_name'];  
                                                            }
                                                            else
                                                            {
                                                                $video_url                  = video_path(array('course_id' => $course_id)).$vimeo_video_data['cl_org_file_name'];
                                                            
                                                            }
                                                            
                                                            $this->initiate_vimeo(array(
                                                                'lecture_id'        => $vimeo_video_data['id'],
                                                                'path'              => $video_url,
                                                                'name'              => $vimeo_video_data['cl_lecture_name'],
                                                                'description'       => $vimeo_video_data['cl_lecture_description']
                                                            ));               
                                                        
                                                        
                                                        }  
                                                            
                                                        
                                                    }
                                                }

                                            }
                                           

                                        }
                                        
                                        

                                        
                                   
                                       
                                    
                                    }
                                       
                                }
                             
                            
                            }
                            else
                            {                                  
                                $error_import[]    =  $lecture_id;
                            }
                           
                        }

                        if(!empty($file_move_to_copy_queue))
                        {
                           
                            $copy_queue_json                        = json_encode($file_move_to_copy_queue);
                            $copy_params                            = array();
                            $copy_params['cq_source_course']        = $parent_course_id;
                            $copy_params['cq_destination_course']   = $course_id;
                            $copy_params['cq_copy_json']            = $copy_queue_json;
                            $copy_params['cq_status']               = '0';
                 
                            
                            $this->Course_model->save_copy_queue($copy_params);

                           
                        }
                        $response['lecture_icons']              = $this->__lecture_type_icons;
                        $response['lectures']                   = $this->Course_model->lectures(array('direction'=>'ASC', 'order_by'=>'cl_order_no', 'section_id'=>$section_id, 'course_id'=>$course_id, 'not_deleted'=> true));
                        $response['section']                    = $this->Course_model->section(array('id'=>$section_id));
                        echo json_encode($response);
                        die;
                     
                    }
                    else
                    {
                        $response['error']                          = true;
                        $response['message']                        = 'Request has empty lecture '; 
                        echo json_encode($response);
                        die;
                       
                    }
                }

                
            }
            else
            {
                $response['error']              = true;
                $response['message']            = 'Missing mandatory post  parameter.. ';
                echo json_encode($response);
                die;
            }
         
        }
        else
        {
           
            $response['error']              = true;
            $response['message']            = 'Post method only Allowed.. ';
            echo json_encode($response);
            die;
            
        }
        
        
        
    }


    
    
    function process_copy_queue()
    {
        $queue_param                        = array();
        $queue_param['status']              = '0';
        
        $get_copy_queue_data                = $this->Course_model->get_copy_queue($queue_param);
       
        if(empty($get_copy_queue_data))
        {
            return false;
        }

        $copy_json                      = json_decode($get_copy_queue_data['cq_copy_json'], true);

        

        $update_queue_data              = array();

        $status_result                  = array_column($copy_json, 'status');
        $status_lecture_result          = array_column($copy_json, 'status','lecture_id');
        $continue_queue                 = false;
        if(in_array('0', $status_result))
        {
            $copy_queue_key = array_search("0",$status_result);
            
            if($copy_json[$copy_queue_key]['status'] == 0)
            {
                $s3_file_copy_param = array('source_path' => $copy_json[$copy_queue_key]['source'], 'target_path' => $copy_json[$copy_queue_key]['destination']);
                $copy_status = $this->copy_file($s3_file_copy_param);
                if($copy_status == 1){
                    $copy_json[$copy_queue_key]['source']       = $copy_json[$copy_queue_key]['source'];
                    $copy_json[$copy_queue_key]['destination']  = $copy_json[$copy_queue_key]['destination'];
                    $copy_json[$copy_queue_key]['status']       = '1';
                    $copy_json[$copy_queue_key]['lecture_id']   = $copy_json[$copy_queue_key]['lecture_id'];                    
                }
                else{
                    $copy_json[$copy_queue_key]['source']       = $copy_json[$copy_queue_key]['source'];
                    $copy_json[$copy_queue_key]['destination']  = $copy_json[$copy_queue_key]['destination'];
                    $copy_json[$copy_queue_key]['status']       = '2';
                    $copy_json[$copy_queue_key]['lecture_id']   = $copy_json[$copy_queue_key]['lecture_id'];
                }
            }

         
            $updated_json                       = json_encode($copy_json);
            $update_queue_data['cq_copy_json']  = $updated_json;
            $continue_queue                     = true;

        }else{
            $update_queue_data['cq_status'] = '1';
            //print_r($copy_json);die();
        }
        $get_complete_lecture_id = array_search("1",array_reverse($status_lecture_result, true));
        if($get_complete_lecture_id != '' && $get_complete_lecture_id > 0){
            $param                          = array();
            $param['id']                    = $get_complete_lecture_id;
            $param['cl_conversion_status']  = '3';
            $this->Course_model->save_lecture($param); 
        }
        
        $update_queue_data['id'] = $get_copy_queue_data['id'];
        

        $this->Course_model->save_copy_queue($update_queue_data);
        if($continue_queue)
        {
            redirect(site_url('admin/coursebuilder/process_copy_queue'));
        }
    }

    function copy_file($param = array())
    {
        $configs = array();
        $s3_configured = false;
        $s3_multipart_settings = $this->settings->setting('has_s3');
        if($s3_multipart_settings['as_superadmin_value'] && $s3_multipart_settings['as_siteadmin_value'])
        {
            if(isset($s3_multipart_settings['as_setting_value']) && isset($s3_multipart_settings['as_setting_value']['setting_value']))
            {
                $s3_configured = true;
                $configs['settings'] = $s3_multipart_settings['as_setting_value']['setting_value'];
            }
        }
        if($s3_configured)
        {
            $configs['folderpath'] = '';
            $this->load->library('multipartupload', $configs);
            try 
            {
                $copy_status = $this->multipartupload->copy_s3_file($param);
                return '1';
            }
            catch (Exception $e) 
            {
                return '0';
                echo $e->getMessage();
            }
            catch (InvalidArgumentException $e) 
            {
                echo $e->getMessage();
            }
        }
        else
        {
        //echo 's3 not configured';die;
            $lecture_copy_command = 'if cp -rf '.$param['source_path'].' '.$param['source_path'].' > /dev/null; then echo "1"; else echo "0"; fi;';
            $copy_status = shell_exec($lecture_copy_command);
        }

        return $copy_status;
    }

    
    
    private function import_assesment($param)
    {
        
       

        $old_lecture                                = $param['old_lecture'];
        $new_section_id                             = $param['section_id'];
        $new_course_id                              = $param['course_id'];
        $sent_mail_on_import_creation               = $param['sent_mail'];
        $new_lecture_id                             = $param['new_lecture_id'];
        $parent_course_id                           = $param['parent_course_id'];
        $parent_lecture_id                          = $param['parent_lecture_id'];
       
        
        /*
         * Inserting the data into the table "assessments"
         */
        $old_assesment              = $this->Course_model->assesment(array('lecture_id' => $parent_lecture_id, 'course_id' => $parent_course_id,'select'=> 'assessments.*'));
        
        if(!empty($old_assesment))
        {
            $old_assesment_id       = $old_assesment['id'];
            $save                   = array();
            $save                   = $old_assesment;
            $save['id']             = false;
            $save['a_course_id']    = $new_course_id;
            $save['a_lecture_id']   = $new_lecture_id;
            $save['a_instructions'] = $old_assesment['a_instructions'];
            $save['a_duration']     = (null != $old_assesment['a_duration'])?$old_assesment['a_duration']:'0';
            $save['action_by']      = $this->auth->get_current_admin('id');
            $save['action_id']      = $this->actions['create'];
            $new_assesment_id       = $this->Course_model->save_assesment($save);

            if( null != $new_assesment_id )
            {
                $old_questions = $this->Course_model->questions(array('assesment_id'=>$old_assesment_id, 'not_deleted'=>'1','not_join'=>true));
               
        
                if( (null != $old_questions ) && (!empty($old_questions)) )
                {
                    foreach ($old_questions as $question) {
                        
                        $save                   = $question;
                        $save['id']             = false;
                        $save['q_course_id']    = $new_course_id;
                        $save['action_by']      = $this->auth->get_current_admin('id');
                        $save['action_id']      = $this->actions['create'];
                        
                       
                        $options         = $this->Course_model->options(array('q_options' => $question['q_options']));
                        if( (null != $options) && !empty($options) )
                        {
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

                            if(null != $new_question_id )
                            {
                                $this->Course_model->save_assesment_question(array( 'assesment_id'      => $new_assesment_id, 
                                'question_id'       => $new_question_id,
                                'positive_mark'     => $save['q_positive_mark'],
                                'negative_mark'     => $save['q_negative_mark']
                            ));
                            }
                          
                           
                        }
                        else
                        {
                           
                            $new_question_id        = $this->Course_model->save_question($save);   
                            if(null != $new_question_id )
                            {
                                $this->Course_model->save_assesment_question(array( 'assesment_id'      => $new_assesment_id, 
                                'question_id'       => $new_question_id,
                                'positive_mark'     => $save['q_positive_mark'],
                                'negative_mark'     => $save['q_negative_mark']
                            ));
                            }
                          
                        }
                        
                    }
                }
            }
        }
        
        
        
        
        
     
       
     
       
        return true;
    }
    
    function update_section_position()
    {
        $response               = array();
        $response['error']      = 'false';
        $response['message']    = lang('section_saved_success');
        //$section_id = $this->input->post('section_id');
        //$position   = $this->input->post('position');
        $structure              = $this->input->post('structure');
        $course_id              = $this->input->post('course_id');
        parse_str($structure, $structure);
        if( isset($structure['section_wrapper']) && !empty($structure['section_wrapper']))
        {
            foreach ($structure['section_wrapper'] as $order => $id) {
                $save = array();
                $save['id']         = $id;
                $save['s_order_no'] = ($order)+1;
                $this->Course_model->save_section($save);
            }
        }
        $this->invalidate_course(array('course_id'=>$course_id));
        echo json_encode($response);
    }
    
    function update_lecture_position()
    {
        $response                   = array();
        $response['error']          = 'false';
        $response['message']        = lang('section_saved_success');
        $section_id                 = $this->input->post('section_id');
        $course_id                  = $this->input->post('course_id');
        $section_id                 = explode('_', $section_id);
        $section_id                 = $section_id[2];
        $response['section_id']     = $section_id;
        $structure                  = $this->input->post('structure');
        parse_str($structure, $structure);
        if(isset($structure['lecture_id']))
        {
            $structure['lecture_id'] = array_filter($structure['lecture_id']);

            if( !empty($structure['lecture_id']))
            {
                foreach ($structure['lecture_id'] as $order => $id) {
                    $save                   = array();
                    $save['id']             = $id;
                    $save['cl_section_id']  = $section_id;
                    $save['cl_order_no']    = ($order)+1;
                    $this->Course_model->save_lecture($save);
                }
            }
            else
            {
                $s_save                   = array();
                $s_save['id']             = $section_id;
                //$s_save['s_status']       = '0';       
                $this->Course_model->save_section($s_save);    
                $response['cl_status']    = '0';
            }
        }
        echo json_encode($response);
        $this->invalidate_course(array('course_id'=>$course_id));
    }
    
    function send_mail_to_bulk_users()
    {
        $params = $this->input->post('params');
        /*$myfile = fopen("uploads/mail_params.txt", "w");
        fwrite($myfile, $params);
        fclose($myfile);*/
        $params = json_decode($params);
        if(!empty($params))
        {
            foreach($params as $param)
            {
                $mail = array();
                foreach($param as $key => $value)
                {
                    $mail[$key] = $value;
                }
                $this->ofabeemailer->send_mail($mail);
                
                //----------------------------------------
                /*$filename = "/var/www/vhosts/ofabee.com/beta.ofabee.com/uploads/mail_params.txt";
                $fp = fopen($filename, "r");
                $content = fread($fp, filesize($filename));
                $content = $content+1;
                fclose($fp);
                $myfile = fopen($filename, "w");
                fwrite($myfile, $content);
                fclose($myfile);*/
                //---------------------------------------
            }
        }
    }
    
    function send_mail_to_bulk_users_curl($params=array())
    {
        /*$params = array();
        $params[] = array('from' => $this->config->item('site_email'), 'to' => array('thanveer.a@enfintechnologies.com'), 'subject' => 'This is test messsage', 'body' => 'This is test body');
        $params[] = array('from' => $this->config->item('site_email'), 'to' => array('thanveer.a@enfintechnologies.com'), 'subject' => 'This is test messsage', 'body' => 'This is test body');
        $params[] = array('from' => $this->config->item('site_email'), 'to' => array('thanveer.a@enfintechnologies.com'), 'subject' => 'This is test messsage', 'body' => 'This is test body');
        */
        $post_fields = array();
        $post_fields['params'] = json_encode($params);
        
        $curlHandle = curl_init(admin_url('coursebuilder/send_mail_to_bulk_users'));
	    $defaultOptions = array (
		CURLOPT_POST => 1,
		CURLOPT_POSTFIELDS => $post_fields,
		CURLOPT_RETURNTRANSFER => false ,
		CURLOPT_TIMEOUT_MS => 1000,
        );
        curl_setopt_array($curlHandle , $defaultOptions);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, FALSE);     
            curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2); 
        curl_exec($curlHandle);
        curl_close($curlHandle);
    }
    
    function change_section_status()
    { 
        $response               = array();
        $response['course']     = array();
        
        $course_id              = $this->input->post('course_id');
        $section_details        = $this->Course_model->section(array('id'=>$this->input->post('section_id')));
        $section_lectures       = $this->Course_model->lectures(array('section_id'=>$this->input->post('section_id'), 'course_id'=>$course_id, 'skip_copy_progres_lecture'=>true));
        if($section_details['s_status'] == '0'){ 
            if(sizeof($section_lectures) == 0){
                $response['error'] = true;
                $response['message'] = 'Add Atleast one lecture to activate the section';
                echo json_encode($response);
                exit;
            }
            
        }
 
        $status  = $this->input->post('status');
        
        foreach($section_lectures as $lecture)
        {
            if(isset($lecture['cl_lecture_type']) && $lecture['cl_lecture_type'] > 0 && $status == '1')
            {
                $method = $this->__lecture_type_array[$lecture['cl_lecture_type']];
                if( in_array($method, array('quiz', 'descriptive_test', 'survey')) )
                {
                    $method = $method.'_validation';
                    $result = $this->$method($lecture);
                    if($result['error']  == 'true')
                    {       
                        $response['error']      = true;
                        $response['message']    = $result['message'];
                        echo json_encode($response);exit;
                    }
                }
            
            }
        }
        
        $save                   = array();
        $save['id']             = $this->input->post('section_id');
        $save['s_status']       = $this->input->post('status');
        $save['action_by']      = $this->auth->get_current_admin('id');
        $save['updated_date']   = date('Y-m-d H:i:s');
        $response['id']         = $this->Course_model->save_section($save);
	
        $response['lectures']   = $section_lectures;
        $lecture_ids            = array_column($section_lectures,'id');
        
        $save_lecture                   = array();
        $filter_param                   = array();
        $filter_param['ids']            = $lecture_ids;
        $filter_param['update']         = true;
        $status_lecture                 = $this->input->post('status');
        if( $status_lecture == 1){
            $save_lecture['cl_status']  = '1';
            $mstatus                    = 'section_activated';
        }else{
            $save_lecture['cl_status']  = '0';
            $mstatus                    = 'section_deactivated';
        }
        $save_lecture['action_by']      = $this->auth->get_current_admin('id');
        $save_lecture['updated_date']   = date('Y-m-d H:i:s');

        if(empty($lecture_ids))
        {
            $response['error']          = true;
            $response['message']        = 'Sorry! Section status could not be updated! Please try again later!';
            echo json_encode($response);exit;
        }

        $save_lecture_status            = $this->Course_model->save_lecture_new($save_lecture,$filter_param);

        if(!$save_lecture_status)
        {
            $response['error']          = true;
            $response['message']        = 'Sorry! Section status could not be updated! Please try again later!';
            echo json_encode($response);exit;
        }
        
        $user_data              = array();
        $user_data['user_id']   = $this->__loggedInUser['id'];
        $user_data['username']  = $this->__loggedInUser['us_name'];
        $user_data['useremail']  = $this->__loggedInUser['us_email'];
        $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
        
        $message_template                   = array();
        $message_template['username']       = $this->__loggedInUser['us_name'];;
        $message_template['course_name']    = $this->input->post('course_name');
        $message_template['section_name']   = $this->input->post('section_name');
        $triggered_activity                 = $mstatus;
        log_activity($triggered_activity, $user_data, $message_template); 
        if($status!='1'){ 
            $filter_param               = array();
            $filter_param['select']     = 'id';
            $filter_param['status']     = '1';
            $filter_param['count']      = true;
            $filter_param['course_id']  = $course_id;
            $section_count              = $this->Course_model->sections($filter_param);
        }

        $response['error']      = false;
        $response['message']    = lang('section_saved_success');
        $cb_status              = $this->checkcoursecontent(array('course_id'=>$course_id));
        $response['cb_status']  = $cb_status['cb_status'];
        $this->invalidate_course(array('course_id'=> $course_id ));
        $descriptive_param      = array();
        $descriptive_param['dt_status'] = $status;

        foreach($response['lectures'] as $lecture)
        {
            $this->Course_model->update_descriptive_test($descriptive_param, $lecture['id']);
        }
        echo json_encode($response);
    }
    
    function change_lecture_status($param = array())
    {
        
        if(!empty($param))
        {
            $status                 = $param['status'];
            $status                 = (intval($status))?'1':'0';// 0 => trying to deactivate, 1 => trying to activate
            $lecture_id             = $param['lecture_id'];
            $course_id              = $param['course_id'];    
        }
        else
        {
            $status                 = $this->input->post('status');
            $status                 = (intval($status))?'1':'0';// 0 => trying to deactivate, 1 => trying to activate
            $lecture_id             = $this->input->post('lecture_id');
            $course_id              = $this->input->post('course_id');    
        }
        $lecture_param              = array();
        $response['course']         = array();
        $lecture_param['id']        = $lecture_id;
        $lecture_param['select']    = 'id,cl_lecture_name, cl_lecture_type, cl_course_id, cl_section_id, cl_sent_mail_on_lecture_creation';
        $lecture                    = $this->Course_model->lecture($lecture_param);
        $course_id                  = isset($lecture['cl_course_id'])?$lecture['cl_course_id']:0;
        
        if(isset($lecture['cl_lecture_type']) && $lecture['cl_lecture_type'] > 0 && $status == '1')
        {
            $method = $this->__lecture_type_array[$lecture['cl_lecture_type']];
            if( in_array($method, array('quiz', 'descriptive_test', 'survey')) )
            {
                $method = $method.'_validation';
                $result = $this->$method($lecture);
                if($result['error']  == 'true')
                {       
                    $response['error']      = true;
                    $response['message']    = $result['message'];
                    echo json_encode($response);exit;
                }
            }
           
        }
        
        $lecture_param                      = array();
        $lecture_param['not_deleted']       = '1';
        $lecture_param['status']            = '1';
        $lecture_param['section_id']        = $lecture['cl_section_id'];
        $lecture_param['count']             = true;
        $lecture_param['course_id']         = $course_id;
        
        $active_lectures_count              = $this->Course_model->lectures($lecture_param);
        $mstatus = 'lecture_deactivated';
        if($status == '1')
        {
            $mstatus = 'lecture_activated';
            $active_lectures_count = intval($active_lectures_count + 1);
        }
        else
        {
            $active_lectures_count = intval($active_lectures_count - 1);
        }
        
        $save                   = array();
        $save['id']             = $lecture_id;
        $save['cl_status']      = $status;
        $save['action_by']      = $this->auth->get_current_admin('id');
        $save['updated_date']   = date('Y-m-d H:i:s');
        $response['id']         = $this->Course_model->save_lecture($save);
        $descriptive_param      = array();
        $descriptive_param['dt_status'] = $status;
        $this->Course_model->update_descriptive_test($descriptive_param, $lecture_id);
        

        if($status == 1 && $lecture['cl_sent_mail_on_lecture_creation'] == 1)
        {
            // $this->Course_model->save_lecture(array('id' => $response['id'], 'cl_sent_mail_on_lecture_creation' => '0'));
            $course = $this->Course_model->course(array('id' => $lecture['cl_course_id']));
            // echo "ddd";die;
            $course_users = $this->Course_model->course_enrolled(array(
                                                                        'select' => 'course_subscription.id,course_subscription.cs_user_id, course_subscription.cs_user_name, users.us_email',
                                                                        'course_id' => $course['id'],
                                                                        'user_email_needed' => true
                                                                    ));
            $course_user_email = array();
            foreach($course_users as $course_user)
            {
                array_push($course_user_email,$course_user['us_email']);
            }
            $notification_action = 'lecture_created';
            $notification_param = 'lecture';
            $notify_to = array_column($course_users,'cs_user_id');
            // var_dump($notify_to);die;
            //Notification
            $this->load->library('Notifier');
            $this->notifier->push(
                array(
                    'action_code' => $notification_action,
                    'assets' => array($notification_param => $lecture['cl_lecture_name'],'lecture_name' =>$lecture['cl_lecture_name'], 'course_name' => $course['cb_title'],'course_id' => $course['id']),
                    'target' => $lecture['id'],
                    'individual' => true,
                    'push_to' => $notify_to
                    )
                );
                //End notification
            $params['from']         = config_item('site_name').'<'.$this->config->item('site_email').'>';;
            $params['to']           = $course_user_email;
            $params['subject']      = "New lecture added under ".$course['cb_title'];
            $params['body']         = "Hi <br/>A new lecture named <b>".$lecture['cl_lecture_name']."</b> has been added under course <b>".$course['cb_title']."</b> from ".$this->config->item('acct_name');
         
            $send = $this->ofabeemailer->send_mail($params);
        }
        
        
        // if($status == 1 && $lecture['cl_sent_mail_on_lecture_creation'] == 1)
        // {
        //     $this->Course_model->save_lecture(array('id' => $response['id'], 'cl_sent_mail_on_lecture_creation' => '0'));
        //     $course = $this->Course_model->course(array('id' => $lecture['cl_course_id']));
        //     $course_users = $this->Course_model->course_enrolled(array(
        //         'select'            => 'course_subscription.id,course_subscription.cs_user_id, course_subscription.cs_user_name, users.us_email',
        //         'course_id'         => $course['id'],
        //         'user_email_needed' => true
                
        //     ));
        //     $notification_action = 'lecture_created';
        //     $notification_param = 'lecture';
            
        //     $notify_to = array_column($course_users,'cs_user_id');
            
        //     //Notification
        //     $this->load->library('Notifier');
        //     $this->notifier->push(
        //         array(
        //             'action_code' => $notification_action,
        //             'assets' => array($notification_param => $lecture['cl_lecture_name'],'lecture_name' =>$lecture['cl_lecture_name'], 'course_name' => $course['cb_title'],'course_id' => $course['id']),
        //             'target' => $lecture['id'],
        //             'individual' => true,
        //             'push_to' => $notify_to
        //             )
        //         );
        //         //End notification
        //     $params['from']         = config_item('site_name').'<'.$this->config->item('site_email').'>';;
        //     $params['to']           = array($course_users['us_email']);
        //     $params['subject']      = "New lecture added under ".$course['cb_title'];
        //     $params['body']         = "Hi ".$course_users['cs_user_name'].", <br/>A new lecture named <b>".$lecture['cl_lecture_name']."</b> has been added under course <b>".$course['cb_title']."</b> from ".$this->config->item('acct_name');
         
        //     $send = $this->ofabeemailer->send_mail($params);
        // }
        
        $section_param                      = array();
        $section_param['id']                = $lecture['cl_section_id'];
        $section                            = $this->Course_model->section($section_param);
        $response['active_lecture_count']   = ($active_lectures_count>0)?$active_lectures_count:0; //(isset($section['s_status']) && $section['s_status'] == '1')?1:0;
       
        
        $user_data              = array();
        $user_data['user_id']   = $this->__loggedInUser['id'];
        $user_data['username']  = $this->__loggedInUser['us_name'];
        $user_data['useremail']  = $this->__loggedInUser['us_email'];
        $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
        
        $message_template                   = array();
        $message_template['username']       = $this->__loggedInUser['us_name'];;
        $message_template['course_name']    = $this->input->post('course_name');
        $message_template['lecture_name']   = $lecture['cl_lecture_name'];
        
        $triggered_activity                 = $mstatus;
        log_activity($triggered_activity, $user_data, $message_template); 
        if($status!='1'){
            $filter_param               = array();
            $filter_param['select']     = 'id';
            $filter_param['status']     = '1';
            $filter_param['count']      = true;
            $filter_param['course_id']  = $course_id;
            $section_count              = $this->Course_model->sections($filter_param);
           
        }
        $this->invalidate_course(array('course_id'=>$course_id));
        if(!empty($section))
        {
            $response['section_id'] = $section['id'];
            $response['error']      = false;
            $response['message']    = 'Lecture Status Changed successfully';
            $cb_status              = $this->checkcoursecontent(array('course_id'=>$course_id));
            $response['cb_status']  = $cb_status['cb_status'];
        }
        else
        {    
            $response['error']      = true;
            $response['message']    = 'Failed to Change';
        }
        if(!empty($param))
        {
            return $response;
        }
        else
        {
            echo json_encode($response);exit;
        }
        if($save['cl_status'] == 1 && $lecture['cl_lecture_type'] == 7)
        {
            $course = $this->Course_model->course(array('id' => $lecture['cl_course_id']));
            $course_users = $this->Course_model->course_enrolled(array(
                'select' => 'id,cs_user_id',
                'course_id' => $course['id']
            ));
            $notification_action = 'live_scheduled';
            $notification_param = 'live_name';
    
            $notify_to = array_column($course_users,'cs_user_id');
    
            //Notification
            $this->load->library('Notifier');
            $this->notifier->push(
                array(
                    'action_code' => $notification_action,
                    'assets' => array($notification_param => $lecture['cl_lecture_name'],'course_name' => $course['cb_title'],'course_id' => $course['id']),
                    'target' => $lecture['id'],
                    'individual' => true,
                    'push_to' => $notify_to
                )
            );
            //End notification
        }
    }
    function survey_validation($lecture){
        
        $response                   = array();
       
        $filter_param               = array();
        $filter_param['lecture_id'] = $lecture['id'];
        $filter_param['course_id']  = $lecture['cl_course_id'];
        $survey                     = $this->Course_model->survey($filter_param);
        
        if(!empty($survey)){
            $survey_param               = array();
            $survey_param['survey_id']  = $survey['survey_id'];
            $survey_questions           = $this->Course_model->survey_questions_count($survey_param);
            
            if($survey_questions > 0){
                $response['error']          = 'false';
                $response['message']        = '';
            }else{
                $response['error']   = 'true';
                $response['message'] = 'Must need atleast one question to activate '. '<br />'; 
            }
        }else{
            $response['error']   = 'true';
            $response['message'] = 'No such survey found<br />';
        }
       
        return $response;
    }
    function descriptive_test_validation(){
        return true;
    }
        
    function quiz_validation($lecture){
        $lecture['return_array'] = true;
        return $this->assesment_validation($lecture);
    }
    
    function assesment_validation($lecture)
    {
        $response            = array();
        $response['error']   = 'false';
        $response['message'] = '';
        
        $assesment           = $this->Course_model->assesment(array('lecture_id' => $lecture['id'], 'course_id' => $lecture['cl_course_id']));
        $questions           = $this->Course_model->questions(array('assesment_id' => $assesment['assesment_id']/*, 'not_deleted'=>'1'*/));
        $this->load->model('Test_model');
        $total_marks                = $this->Test_model->test_questions(array('select'=>'SUM(aq_positive_mark) as total_mark','assessment_id'=>$assesment['assesment_id']));
        $quiz_marks                 = $total_marks[0]['total_mark'];
        $total_marks                = $this->Test_model->test_details(array('test_id'=>$lecture['id'],'select'=>'assessments.a_mark'));
        $total_mark                 = $total_marks['a_mark']; 
        if(strip_tags($assesment['a_instructions']) == '')
        {
            $response['error']   = 'true';
            $response['message'] .= lang('instructions_missing_on_activating').'<br />';    
        }
        if(sizeof($questions) == 0)
        {
            $response['error']   = 'true';
            $response['message'] .= lang('question_missing_on_activating').'<br />';    
        } else {
            if($quiz_marks!=$total_mark){
                $response['error']   = 'true';
                $response['message'] .= 'Defined mark in step 1 is different than overall marks in step 3!<br />';
            }
        }
        if($response['error'] == 'true')
        {              
            if(isset($lecture['return_array'])&&$lecture['return_array']==true)
            {
                return $response;
            }
            else
            {
                echo json_encode($response);exit;
            }
        }
    }
    
    
    function delete_section()
    {
        $section_id             = $this->input->post('section_id');
        $course_id              = $this->input->post('course_id');
        $lecture_param               = array();
        $lecture_param['section_id'] = $section_id;
        $lecture_param['course_id']  = $course_id;
        $lecture_param['select']     = 'id,cl_lecture_type,cl_course_id,cl_section_id';
        $section_lectures            = $this->Course_model->lectures($lecture_param);
        $lectures                    = array_column($section_lectures,'id');
        $filter_param                = array();
        $filter_param['lecture_ids'] = $lectures;
        $filter_param['section_id']  = $section_id;
        $filter_param['course_id']   = $course_id;
        $errorCount                  = 0;
        foreach($section_lectures as $lecture)
        {
            if(isset($lecture['cl_lecture_type'])&& $lecture['cl_lecture_type'] > 0)
            {
                $lecture_type   =  $lecture['cl_lecture_type'];
                $method         = $this->__lecture_type_array[$lecture_type];
                if(in_array($method, array('quiz', 'descriptive_test', 'survey')))
                {
                    $method = 'clear_'.$method.'_log';
                    $result = $this->$method($lecture);
                    if($result['error']  == true)
                    {   
                        $errorCount++;
                        $message[]    = $result['message'];
                    }
                }
            }
        }
        $this->invalidate_course(array('course_id'=>$course_id));
        
        if($errorCount == 0)
        {
            $this->Course_model->remove_logs($filter_param);
            $this->Course_model->remove_lectures($lecture_param);
            $this->Course_model->remove_sections($lecture_param);
            $cb_status              = $this->checkcoursecontent(array('course_id'=>$course_id));
            $user_data              = array();
            $user_data['user_id']   = $this->__loggedInUser['id'];
            $user_data['username']  = $this->__loggedInUser['us_name'];
            $user_data['useremail']  = $this->__loggedInUser['us_email'];
            $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
            
            $message_template                   = array();
            $message_template['username']       = $this->__loggedInUser['us_name'];;
            $message_template['course_name']    = $this->input->post('course_name');
            $message_template['section_name']   = $this->input->post('section_name');
            
            $triggered_activity                 = 'section_deleted';
            log_activity($triggered_activity, $user_data, $message_template);
            $response               = array();
            $response['error']      = false;
            $response['message']    = lang('section_deleted_success');
            $response['cb_status']  = $cb_status['cb_status'];
            echo json_encode($response);
        }
        else
        {
            $response               = array();
            $response['error']      = true;
            $response['message']    = implode('<br />', $message);
            echo json_encode($response);
        }
    }
    
    function delete_lecture()
    {
        $response   = array();
        //$lecture_id = $this->input->post('lecture_id');
        $lecture_id = ($this->input->post('lecture_id') !== null) ? $this->input->post('lecture_id') : false;
        $course_id  = $this->input->post('course_id');
        if($lecture_id !== false)
        { 

            $lecture_param              = array();
            $lecture_param['direction'] = 'DESC';
            $lecture_param['id']        = $lecture_id;
            $lecture_param['select']    = 'id,cl_lecture_name,cl_lecture_type,cl_course_id,cl_section_id';
            $lecture                    = $this->Course_model->lecture($lecture_param);
            $errorCount                 = 0;
            if(isset($lecture['cl_lecture_type'])&& $lecture['cl_lecture_type'] > 0)
            {
                $lecture_type   =  $lecture['cl_lecture_type'];
                $method         = $this->__lecture_type_array[$lecture_type];
                if(in_array($method, array('quiz', 'descriptive_test', 'survey')))
                {
                    $method = 'clear_'.$method.'_log';
                    $result = $this->$method($lecture);
                    if($result['error']  == true)
                    {
                        
                        $errorCount++;
                        $message[]    = $result['message'];
                    }
                }
            }
        }
        $user_data              = array();
        $user_data['user_id']   = $this->__loggedInUser['id'];
        $user_data['username']  = $this->__loggedInUser['us_name'];
        $user_data['useremail']  = $this->__loggedInUser['us_email'];
        $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
        
        $message_template                   = array();
        $message_template['username']       = $this->__loggedInUser['us_name'];;
        $message_template['course_name']    = $this->input->post('course_name');
        $message_template['lecture_name']   = $lecture['cl_lecture_name'];
        
        $triggered_activity                 = 'lecture_deleted';
        log_activity($triggered_activity, $user_data, $message_template); 
        $this->invalidate_course(array('course_id'=>$course_id));
        if($errorCount==0)
        {
            $filter_param                = array();
            $filter_param['lecture_id']  = $lecture['id'];
            $this->Course_model->remove_logs($filter_param);
            $this->Course_model->remove_lectures($filter_param);
            if(isset($lecture['cl_lecture_type'])&& $lecture['cl_lecture_type'] == 1)
            {
                $has_parent_lecture  = $this->Course_model->has_parent_lecture(array('lecture_id'=>$lecture['id']));
                if(!$has_parent_lecture)
                {
                    $this->load->library('Vimeoupload');
                    $this->vimeoupload->set_config();
                   
                    $this->vimeoupload->delete(array('uri'=>$lecture['cl_filename'])); 
                }
            }
            $section_param          = array();
            $section_param['id']    = $lecture['cl_section_id'];
            $section                = $this->Course_model->section($section_param);
            $cb_status              = $this->checkcoursecontent(array('course_id'=>$course_id));
            $response['section_id'] = $section['id'];
            $response['active_lecture_count']   = (isset($section['s_status']) && $section['s_status'] == '1')?1:0;
            
            $message_template                   = array();
            $message_template['username']       = $this->__loggedInUser['us_name'];;
            $message_template['course_name']    = $this->input->post('course_name');
            $message_template['lecture_name']   = $lecture['cl_lecture_name'];
            
            $triggered_activity                 = 'lecture_deleted';
            log_activity($triggered_activity, $user_data, $message_template); 
            $this->invalidate_course(array('course_id'=>$course_id));
            if($errorCount==0)
            {
                $filter_param                = array();
                $filter_param['lecture_id']  = $lecture['id'];
                $this->Course_model->remove_logs($filter_param);
                $this->Course_model->remove_lectures($filter_param);
                $section_param          = array();
                $section_param['id']    = $lecture['cl_section_id'];
                $section                = $this->Course_model->section($section_param);
                $cb_status              = $this->checkcoursecontent(array('course_id'=>$course_id));
                $response['section_id'] = $section['id'];
                $response['active_lecture_count']   = (isset($section['s_status']) && $section['s_status'] == '1')?1:0;
                
                $response['error']      = 'false';
                $response['message']    = lang('lecture_deleted_success');
                $response['cb_status']  = $cb_status['cb_status'];
                echo json_encode($response);
            }   
            else
            {
                $response               = array();
                $response['error']      = 'true';
                $response['message']    = implode('<br />', $message);
                echo json_encode($response);
            }
        }
        else
        {
            $response = array();
            $response['error'] = 'true';
            $response['message'] = 'Something went wrong!! Please try again';
            echo json_encode($response);
        }
    }

    function checkcoursecontent($params = array())
    {
        $lectures = $this->Course_model->lectures(array('count'=>true, 'course_id'=> $params['course_id'], 'status' => '1'));
       
        $sections = $this->Course_model->section(array('count'=>true, 'course_id'=> $params['course_id'], 'status' => '1'));
        
        if(!$lectures || !$sections){
            $save                   = array();
            $save['id']             = $params['course_id'];
            $save['action_by']      = $this->auth->get_current_admin('id');
            $save['updated_date']   = date('Y-m-d H:i:s');
            $save['cb_status']      = '0';
            
            if ($this->Course_model->save($save)) {
                $user_data              = array();
                $user_data['user_id']   = $this->__loggedInUser['id'];
                $user_data['username']  = $this->__loggedInUser['us_name'];
                $user_data['useremail']  = $this->__loggedInUser['us_email'];
                $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
                
                $message_template                   = array();
                $message_template['username']       = $this->__loggedInUser['us_name'];
                $course                             = $this->Course_model->course(array('id' => $params['course_id'], 'select' => 'cb_title'));
                $message_template['course_name']    = $course['cb_title'];
                $mstatus                            = 'course_deactivated'; 
                log_activity($mstatus, $user_data, $message_template); 
                $this->memcache->delete('course_'.$params['course_id']);
                $this->memcache->delete('course_mob'.$params['course_id']);
                return array('cb_status' => '0');
            }
        }
        return array('cb_status' => '1');
    }

    function save_assesment()
    {
        $response           = array();
        $response['error']  = false;
        $response['message']= '';
        $section_id         = $this->input->post('section_id');
        $section_id         = $section_id==''?0:$section_id;
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
                $response['error']   = true;
                $response['message'].= lang('section_name_required');
            }
            else
            {
                if( $this->Course_model->section(array('filter_id'=>$save['id'],'course_id'=>$course_id,'name'=>$save['s_name'], 'deleted'=>'0')) > 0 )
                {
                    $response['error']   = true;
                    $response['message'].= lang('section_name_not_available');
                }
            }
        }
        
        if( $response['error'] == true )
        {
            echo json_encode($response);exit;            
        }
        
        if( !$section_id )
        {
            $total              = $this->Course_model->sections(array('count'=>true, 'course_id'=> $save['s_course_id']));
            $save['s_order_no'] = ($total)+1;
            $save['action_by']  = $this->auth->get_current_admin('id');
            $save['action_id']  = 1;
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
        $save['cl_lecture_type']                    = $this->__lecture_type_keys_array['quiz'];
        $save['cl_sent_mail_on_lecture_creation']   = $this->input->post('sent_mail_on_assesment_creation');
        $save['action_by']                          = $this->auth->get_current_admin('id');
        $save['action_id']                          = 1;
        $save['cl_sent_mail_on_lecture_creation']   = $this->input->post('sent_mail_on_assesment_creation');

        $lecture_id                                 = $this->Course_model->save_lecture($save);
        
        $this->invalidate_course(array('course_id' => $save['cl_course_id']));
        $this->invalidate_course();
        $user_data             = array();
        $user_data['user_id']  = $this->__loggedInUser['id'];
        $user_data['username']  = $this->__loggedInUser['us_name'];
        $user_data['useremail']  = $this->__loggedInUser['us_email'];
        $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
        
        $message_template                     = array();
        $message_template['username']         = $this->__loggedInUser['us_name'];;
        $message_template['lecture_name']     =  $save['cl_lecture_name'];
        $message_template['course_name']      =  $this->input->post('course_name');
        $triggered_activity     = 'lecture_created';
        log_activity($triggered_activity, $user_data, $message_template);
        $instruction           = array();
        $save                  = array();
        $save['id']            = false;
        $save['a_course_id']   = $course_id;
        $save['a_lecture_id']  = $lecture_id;
        $active_lang           = isset($_SESSION['active_lang'])?$_SESSION['active_lang']:1;
        $instruction[$active_lang] = $this->get_instruction();
        $save['a_instructions']    = json_encode($instruction);
        $save['a_show_categories'] = $this->input->post('show_categories');
        $save['a_show_categories'] = ($save['a_show_categories'])?'1':'0';
        $save['action_by']     = $this->auth->get_current_admin('id');
        $save['action_id']     = 1;
        $assesment_id          = $this->Course_model->save_assesment($save);
        
        $response['error']   = false;
        $response['message'] = lang('assesment_saved_success');
        $response['id']      = $lecture_id;
        echo json_encode($response);exit;            
    }
    function save_survey()
    {
        $response           = array();
        $response['error']  = false;
        $response['message']= '';
        $section_id         = $this->input->post('section_id');
        $section_id         = $section_id==''?0:$section_id;
        $section_name       = $this->input->post('section_name');
        $course_id          = $this->input->post('course_id');
        $save               = array();
        $save['id']         = $section_id;
        $save['s_name']     = $section_name;
        $save['s_course_id']= $course_id;
        $this->invalidate_course(array('course_id' => $course_id));
        $this->invalidate_course();
        if( !$section_id )
        {
            if( $save['s_name'] == '' )
            {
                $response['error']   = 'true';
                $response['message'].= lang('section_name_required');
            }
            else
            {
                if( $this->Course_model->section(array('filter_id'=>$save['id'],'course_id'=>$course_id,'name'=>$save['s_name'], 'deleted'=>'0')) > 0 )
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
            $save['action_id']  = '1';
            $section_id         = $this->Course_model->save_section($save);
        }
        $save                                       = array();
        $save['id']                                 = false;
        $save['cl_lecture_name']                    = $this->input->post('survey_name');
        $save['cl_lecture_description']             = $this->input->post('survey_description');
        $save['cl_course_id']                       = $course_id;
        $save['cl_section_id']                      = $section_id;
        $highest_order                              = $this->Course_model->lectures(array('count'=>true, 'section_id'=>$section_id, 'course_id'=> $course_id));
        $save['cl_order_no']                        = $highest_order+1;
        $save['cl_lecture_type']                    = $this->__lecture_type_keys_array['survey'];
        $save['cl_limited_access']                  = '1';
        $save['action_by']                          = $this->auth->get_current_admin('id');
        $save['action_id']                          = $this->actions['create'];
        $save['cl_sent_mail_on_lecture_creation']   = $this->input->post('sent_mail_on_survey_creation');
        $lecture_id                                 = $this->Course_model->save_lecture($save);
        
        $user_data              = array();
        $user_data['user_id']   = $this->__loggedInUser['id'];
        $user_data['username']  = $this->__loggedInUser['us_name'];
        $user_data['useremail']  = $this->__loggedInUser['us_email'];
        $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
        
        $message_template                     = array();
        $message_template['username']         = $this->__loggedInUser['us_name'];;
        $message_template['lecture_name']     =  $save['cl_lecture_name'];
        $message_template['course_name']      =  $this->input->post('course_name');
        $triggered_activity     = 'lecture_created';
        log_activity($triggered_activity, $user_data, $message_template);
        
        $save                   = array();
        $save['id']             = false;
        $save['s_course_id']    = $course_id;
        $save['s_lecture_id']   = $lecture_id;
        $save['s_name']         = $this->input->post('survey_name');
        $save['s_description']  = $this->input->post('survey_description');
        $save['s_tutor_id']     = $this->input->post('tutor_id');
        $save['s_tutor_id']     = ($save['s_tutor_id'])?$save['s_tutor_id']:0;
        $save['s_tutor_name']   = $this->input->post('tutor_name');
        $save['s_tutor_name']   = ($save['s_tutor_name'])?$save['s_tutor_name']:'';
        $save['created_by']     = $this->auth->get_current_admin('id');
        $survey_id              = $this->Course_model->save_survey($save);
        
       
        $response['error']   = false;
        $response['message'] = 'Survey added successfully';
        $response['id']      = $lecture_id;
        echo json_encode($response);exit;            
    }
    
    private function get_instruction()
    {
        return '<div id="dvInstruction">
            <p class="headings-altr"><strong>General Instructions:</strong></p>
            <ol class="header-child-alt">
            <li>The clock has been set at the server and the countdown timer at the top right corner of your screen will display the time remaining for you to complete the exam. When the clock runs out the exam ends by default - you are not required to end or submit your exam.</li><li>The Marked for Review status simply acts as a reminder that you have set to look at the question again. <em>If an answer is selected for a question that is Marked for Review, the answer will be considered in the final evaluation.</em></li></ol><p><br></p></div>';
    }
    
    function save_html()
    {
        $response           = array();
        $response['error']  = 'false';
        $response['message']= '';
        $section_id         = $this->input->post('section_id');
        $section_name       = $this->input->post('section_name');
        $course_id          = $this->input->post('course_id');
        
        $this->invalidate_course(array('course_id' => $course_id));
        $this->invalidate_course();
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
                if( $this->Course_model->section(array('filter_id'=>$save['id'], 'name'=>$save['s_name'], 'deleted'=>'0')) > 0 )
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
        $save['cl_lecture_name']                    = $this->input->post('html_name' ,true);
        $save['cl_lecture_description']             = $this->input->post('lecture_description');
        $save['cl_lecture_description']             = $this->input->post('html_description');
        $save['cl_course_id']                       = $course_id;
        $save['cl_section_id']                      = $section_id;
        $highest_order                              = $this->Course_model->lectures(array('count'=>true, 'section_id'=>$section_id, 'course_id'=> $course_id));
        $save['cl_order_no']                        = $highest_order+1;
        $save['cl_lecture_type']                    = $this->__lecture_type_keys_array['text'];
        $save['action_by']                          = $this->auth->get_current_admin('id');
        $save['action_id']                          = $this->actions['create'];
        $save['cl_sent_mail_on_lecture_creation']   = $this->input->post('sent_mail_on_htmlcode_creation');
        $lecture_id                                 = $this->Course_model->save_lecture($save);
         
        $user_data              = array();
        $user_data['user_id']   = $this->__loggedInUser['id'];
        $user_data['username']  = $this->__loggedInUser['us_name'];
        $user_data['useremail']  = $this->__loggedInUser['us_email'];
        $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
        
        $message_template                     = array();
        $message_template['username']         = $this->__loggedInUser['us_name'];;
        $message_template['lecture_name']     =  $save['cl_lecture_name'];
        $message_template['course_name']      =  $this->input->post('course_name');
        $triggered_activity     = 'lecture_created';
        log_activity($triggered_activity, $user_data, $message_template);
        $response['error']   = 'false';
        $response['message'] = lang('html_saved_success');
        $response['id']      = $lecture_id;
        echo json_encode($response);exit;            
    }
    
    function save_html_detail()
    {
        $response           = array();
        $response['error']  = 'false';
        $response['message']= '';
        
        $save               = array();
        
        if( $response['error'] == 'true' )
        {
            echo json_encode($response);exit;            
        }
        
        $save                                       = array();
        $save['id']                                 = $this->input->post('lecture_id');
        $save['cl_lecture_description']             = $this->input->post('lecture_description'); 

        if(isset($_FILES['lecture_image']) && $_FILES['lecture_image']['error'] !== 4)
        { 
            $allowed =  array('gif','png' ,'jpg', 'jpeg');
            $filename = $_FILES['lecture_image']['name'];
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if(in_array($ext,$allowed))
            {
                $lecture_id                         = $this->input->post('lecture_id');
                $version                            = rand(0,300);
                if($this->upload_course_lecture_image_to_localserver(array('course_id'=>$this->input->post('course_id'), 'lecture_id'=>$lecture_id, $version )))
                {
                    $save['cl_lecture_image']               =  $lecture_id.".jpg?v=".$version;
                }
            }

        }
        $save['cl_lecture_name']                    = $this->input->post('lecture_name' ,true);
        $save['cl_lecture_description']             = $this->input->post('lecture_description'); 
        $save['cl_lecture_content']                 = html_entity_decode($this->input->post('lecture_content'));
        //$save['cl_limited_access']                  = $this->input->post('cl_limited_access');
        $save['cl_sent_mail_on_lecture_creation']   = $this->input->post('sent_mail_on_lecture_creation');
        $save['action_by']                          = $this->auth->get_current_admin('id');
        $save['action_id']                          = $this->actions['create'];
        $lecture_id                                 = $this->Course_model->save_lecture($save);
        $response['error']   = 'false';
        $response['message'] = lang('lecture_saved_success');
        $response['id']      = $lecture_id;
        echo json_encode($response);exit;            
    }
    
    function save_live_lecture() 
    {
        $response           = array();
        $response['error']  = 'false';
        $response['message']= '';
        $section_id         = $this->input->post('section_id');
        $section_name       = $this->input->post('section_name');
        $course_id          = $this->input->post('course_id');
        $schedule_date      = $this->input->post('schedule_date');
        
        $timestamp          = strtotime($schedule_date);
        
        $schedule_date      = date('Y-m-d', $timestamp);
        $start_time         = $this->input->post('start_time');
        $duration           = $this->input->post('duration');
        $studio_id          = $this->input->post('studio_id');
        $ll_mode            = $this->input->post('ll_mode');
        $ll_mode            = ($ll_mode)?$ll_mode:'2';
        
        $save               = array();
        $save['id']         = $section_id;
        $save['s_name']     = $section_name;
        $save['s_course_id']= $course_id;
        $this->invalidate_course(array('course_id' => $course_id));
        $this->invalidate_course();
     
        if( !$section_id )
        {
            if( $save['s_name'] == '' )
            {
                $response['error']   = 'true';
                $response['message'].= lang('section_name_required');
            }
            else
            {
                if( $this->Course_model->section(array('filter_id'=>$save['id'], 'course_id'=>$course_id, 'name'=>$save['s_name'], 'deleted'=>'0')) > 0 )
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
            $save['action_id']  = '1';
            $section_id         = $this->Course_model->save_section($save);
        }
        
        $save                                       = array();
        if(isset($_FILES['lecture_image']) && $_FILES['lecture_image']['error'] !== 4)
        { 
            $allowed =  array('gif','png' ,'jpg', 'jpeg');
            $filename = $_FILES['lecture_image']['name'];
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if(in_array($ext,$allowed))
            {
                $lecture_id                         = $this->input->post('lecture_id');
                $version                            = rand(0,300);
                if($this->upload_course_lecture_image_to_localserver(array('course_id'=>$this->input->post('course_id'), 'lecture_id'=>$lecture_id )))
                {
                    $save['cl_lecture_image']               =  $lecture_id.".jpg?v=".$version;
                }
            }

        }

        $save['id']                                 = $this->input->post('lecture_id');
        $save['cl_lecture_name']                    = $this->input->post('live_lecture_name');
        $save['cl_lecture_description']             = $this->input->post('live_lecture_description');
        $save['cl_course_id']                       = $course_id;
        $save['cl_section_id']                      = $section_id;
        if( !$save['id'] )
        {
            $highest_order                          = $this->Course_model->lectures(array('count'=>true, 'section_id'=>$section_id, 'course_id'=> $course_id));
            $save['cl_order_no']                    = $highest_order+1;
            $save['cl_lecture_type']                = $this->__lecture_type_keys_array['live'];
            $save['action_id']                      = '1';
        }
        else
        {
            $save['updated_date']                   = date('Y-m-d H:i:s');            
        }
        $save['cl_limited_access']                  = '0';
        $save['action_by']                          = $this->auth->get_current_admin('id');
        $save['cl_sent_mail_on_lecture_creation']   = $this->input->post('sent_mail_on_live_lecture_creation');
        $live_lecture_id                            = $this->Course_model->save_lecture($save);
        $user_data              = array();
        $user_data['user_id']   = $this->__loggedInUser['id'];
        $user_data['username']  = $this->__loggedInUser['us_name'];
        $user_data['useremail']  = $this->__loggedInUser['us_email'];
        $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
        
        $message_template                     = array();
        $message_template['username']         = $this->__loggedInUser['us_name'];;
        $message_template['lecture_name']     =  $save['cl_lecture_name'];
        $message_template['course_name']      =  $this->input->post('course_name');
        $triggered_activity     = 'lecture_created';
        log_activity($triggered_activity, $user_data, $message_template);     
        $save                   = array();
        $save['id']             = $this->input->post('live_lecture_id');
        $save['ll_lecture_id']  = $live_lecture_id;
        $save['ll_course_id']   = $course_id;
        $save['ll_date']        = $schedule_date;
        $save['ll_studio_id']   = $studio_id;
        $save['ll_time']        = date('H:i:s', strtotime($start_time));
        $save['ll_duration']    = $duration;
        $save['ll_mode']        = $ll_mode;
        $live_id                = $this->Course_model->save_live_lecture($save);
        
        
        $response['error']   = 'false';
        $response['message'] = lang('live_lecture_saved_success');
        $response['id']      = $live_lecture_id;
        $this->load->library('Notifier');
        $this->notifier->push(
                array(
                    'action_code' => 'live_scheduled',
                    'assets' => array('student_name' => 'Thanveer Ahmed'),
                    'individual' => true,
                    'push_to' => array(8,9,10,11,12)
                )
            );
        echo json_encode($response);
    }
    public function save_youtube(){
        
        $response               = array();
        $response['error']      = false;
        $response['message']    = '';
        $section_id         = $this->input->post('section_id');
        $section_name       = $this->input->post('section_name');
        $course_id          = $this->input->post('course_id');
        $lecture_id         = $this->input->post('lecture_id');
        $lecture_name       = $this->input->post('youtube_name');
        $filename           = $this->input->post('youtube_url');
        $description        = $this->input->post('youtube_description');
        $save               = array();
        if(isset($_FILES['lecture_image']) && $_FILES['lecture_image']['error'] !== 4)
        { 
            $allowed =  array('gif','png' ,'jpg', 'jpeg');
            $filenames = $_FILES['lecture_image']['name'];
            $ext = pathinfo($filenames, PATHINFO_EXTENSION);
            if(in_array($ext,$allowed))
            {
                $lecture_id                         = $this->input->post('lecture_id');

                $version                            = rand(0,300);
                if($this->upload_course_lecture_image_to_localserver(array('course_id'=>$this->input->post('course_id'), 'lecture_id'=>$lecture_id )))
                {
                    $lecture_image_name               =  $lecture_id.".jpg?v=".$version;
                }
            }

        }
        $this->invalidate_course(array('course_id' => $course_id));
        $this->invalidate_course();
        
        $save['id']         = $section_id;
        $save['s_name']     = $section_name;
        $save['s_course_id']= $course_id;
        $lecture_type       = $this->lecture_type_from_url($filename);
        $url                = $filename;
        if(!$lecture_type)
        {
            $response['error']   = true;
            $response['message'].= 'Invalid url detected.';
            echo json_encode($response);exit;            
        }
    
        $regs = array();
       

        $filename       = $this->generate_youtube_url($filename);
        $values         = '';
        $url__vimeo_type = '';
        if (preg_match('/youtube\.com\/watch\?v=([^\&\?\/]+)/', $filename, $id)) 
        {
            $values = $id[1];
        } else if (preg_match('/youtube\.com\/embed\/([^\&\?\/]+)/', $filename, $id)) {
            $values = $id[1];
        } else if (preg_match('/youtube\.com\/v\/([^\&\?\/]+)/', $filename, $id)) {
            $values = $id[1];
        } else if (preg_match('/youtu\.be\/([^\&\?\/]+)/', $filename, $id)) {
            $values = $id[1];
        }
        else if (preg_match('/youtube\.com\/verify_age\?next_url=\/watch%3Fv%3D([^\&\?\/]+)/', $filename, $id)) {
            $values = $id[1];
        }
        else if (preg_match('%^https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)(?:[?]?.*)$%im', $url, $regs)) {
            $values             = $regs[3];
            $url__vimeo_type    = 'vimeo';
        }
        if($values!=''){
            $key          = config_item('youtube_api');
            if(!empty($url__vimeo_type))
            {
                $lecture_type = '15';
                $url          = "https://vimeo.com/api/oembed.json?url=".$url;
                $filename     = 'https://player.vimeo.com/video/'.$values;  
            }
            else{
                $lecture_type = '4';
                $url          = "https://www.googleapis.com/youtube/v3/videos?part=contentDetails&id=".$values."&key=".$key;
            }
            $dur          = file_get_contents($url);
            $duration     = json_decode($dur, true);
            if( !empty($duration['pageInfo']['totalResults']) || !empty($url__vimeo_type) ){
                $vTime = 0;
                if(!empty($url__vimeo_type))
                {
                    $vTime = $duration['duration'];
                    $video_duration = $this->time2seconds(gmdate("H:i:s", $vTime));
                }
                else
                {
                    foreach ($duration['items'] as $vidTime) {
                        $vTime= $vidTime['contentDetails']['duration'];
                        $duration = new DateInterval($vTime);
                        $video_duration = $this->time2seconds($duration->format('%H:%I:%S'));
                    }
                }
                if( !$section_id )
                {
                    if( $save['s_name'] == '' )
                    {
                        $response['error']   = true;
                        $response['message'].= lang('section_name_required');
                    }
                    else
                    {
                        if( $this->Course_model->section(array('filter_id'=>$save['id'],'course_id'=>$course_id,'name'=>$save['s_name'], 'deleted'=>'0')) > 0 )
                        {
                            $response['error']   = true;
                            $response['message'].= lang('section_name_not_available');
                        }
                    }
                }

                if( $response['error'] == true)
                {
                    echo json_encode($response);exit;            
                }
                
                if( !$section_id )
                {
                    $total              = $this->Course_model->sections(array('count'=>true, 'course_id'=> $save['s_course_id']));
                    $save['s_order_no'] = ($total)+1;
                    $section_id         = $this->Course_model->save_section($save);
                }
                $save                           = array(); 
                $save['cl_lecture_image']          = isset($lecture_image_name) ? $lecture_image_name : "";
                $save['id']                     = $lecture_id;
                $save['cl_lecture_name']        = $lecture_name;
                $save['cl_filename']            = $filename;
                $save['cl_lecture_description'] = $description;
                $save['cl_course_id']           = $course_id;
                $save['cl_section_id']          = $section_id;
                $highest_order                  = $this->Course_model->lectures(array('count'=>true, 'section_id'=>$section_id, 'course_id'=> $course_id));
                $save['cl_order_no']            = $highest_order+1;
                $save['cl_lecture_type']        = $lecture_type;
                //$save['cl_duration']            = $this->time2seconds($duration->format('%H:%I:%S'));
                $save['cl_sent_mail_on_lecture_creation']   = $this->input->post('sent_mail_on_youtube_creation');
                $save['cl_duration']            = $video_duration;
                $lecture_id                     = $this->Course_model->save_lecture($save);
                if($lecture_id!=''){
                   
                    $user_data              = array();
                    $user_data['user_id']   = $this->__loggedInUser['id'];
                    $user_data['username']  = $this->__loggedInUser['us_name'];
                    $user_data['useremail']  = $this->__loggedInUser['us_email'];
                    $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
                    
                    $message_template                     = array();
                    $message_template['username']         = $this->__loggedInUser['us_name'];;
                    $message_template['lecture_name']     =  $save['cl_lecture_name'];
                    $message_template['course_name']      =  $this->input->post('course_name');
            
                    $triggered_activity     = 'lecture_created';
                    log_activity($triggered_activity, $user_data, $message_template);
                    $response            = array();
                    $response['error']   = false;
                    $response['message'] = lang('lecture_saved_success');
                    $response['id']      = $lecture_id;
                    echo json_encode($response);exit; 
                }else{
                    $response            = array();
                    $response['error']   = true;
                    $response['message'] = 'Failed to save lecture';
                    $response['id']      = $lecture_id;
                    echo json_encode($response);exit; 
                }
            }else{
                $response            = array();
                $response['error']   = true;
                $response['message'] = 'Invalid Url.Please check the Url';
                $response['id']      = $lecture_id;
                echo json_encode($response);exit; 
            }
        }else{
            $response            = array();
            $response['error']   = true;
            $response['message'] = 'Invalid Url.Please check the Url';
            $response['id']      = $lecture_id;
            echo json_encode($response);exit; 
        }
    }
      
    function time2seconds($time='00:00:00')
    {
        list($hours, $mins, $secs) = explode(':', $time);
        return ($hours * 3600 ) + ($mins * 60 ) + $secs;
    }
    
    function save_recorded_video()
    {
        $response           = array();
        $response['error']  = 'false';
        $response['message']= '';
        $section_id         = $this->input->post('section_id');
        $section_name       = $this->input->post('section_name');
        $course_id          = $this->input->post('course_id');
        $this->invalidate_course(array('course_id' => $course_id));
        $this->invalidate_course();
        
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
                if( $this->Course_model->section(array('filter_id'=>$save['id'], 'name'=>$save['s_name'], 'deleted'=>'0')) > 0 )
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
        $save['id']                                 = $this->input->post('lecture_id');
        $save['cl_lecture_name']                    = $this->input->post('recorded_name');
        $save['cl_filename']                        = $this->input->post('recorded_url');
        $save['cl_lecture_description']             = $this->input->post('recorded_description');
        $save['cl_limited_access']                  = $this->input->post('cl_limited_access');
        $save['cl_limited_access']                  = ($save['cl_limited_access'])?$save['cl_limited_access']:'0';
        $save['cl_course_id']                       = $course_id;
        $save['cl_section_id']                      = $section_id;
        $highest_order                              = $this->Course_model->lectures(array('count'=>true, 'section_id'=>$section_id, 'course_id'=> $course_id));
        $save['cl_order_no']                        = $highest_order+1;
        $save['cl_lecture_type']                    = $this->__lecture_type_keys_array['recorded_videos'];
        $save['cl_sent_mail_on_lecture_creation']   = $this->input->post('sent_mail_on_recorded_creation');
        $save['action_by']                          = $this->auth->get_current_admin('id');
        $save['action_id']                          = $this->actions['create'];
        $lecture_id                                 = $this->Course_model->save_lecture($save);
        
        $response['error']   = 'false';
        $response['message'] = lang('lecture_saved_success');
        $response['id']      = $lecture_id;
        echo json_encode($response);exit;            
    }
    private function generate_youtube_url($url=false)
    {
        $pattern = 
        '%^# Match any youtube URL
        (?:https?://)?  # Optional scheme. Either http or https
        (?:www\.)?      # Optional www subdomain
        (?:             # Group host alternatives
          youtu\.be/    # Either youtu.be,
        | youtube\.com  # or youtube.com
          (?:           # Group path alternatives
            /embed/     # Either /embed/
          | /v/         # or /v/
          | /watch\?v=  # or /watch\?v=
          )             # End path alternatives.
        )               # End host alternatives.
        ([\w-]{11,13})  # Allow 10-12 for 11 char youtube id.
        $%x'
        ;
        // echo $result = preg_match($pattern, $url, $matches);
        $result = preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[\w\-?&!#=,;]+/[\w\-?&!#=/,;]+/|(?:v|e(?:mbed)?)/|[\w\-?&!#=,;]*[?&]v=)|youtu\.be/)([\w-]{11})(?:[^\w-]|\Z)%i', $url, $matches);

        if ($result) {
            //return $matches[1];
            return 'https://www.youtube.com/embed/'.$matches[1];
        }
        return false;
    }
    
    function save_wikipedia()
    {
        $response           = array();
        $response['error']  = 'false';
        $response['message']= '';
        
        $save               = array();
        
        if( $response['error'] == 'true' )
        {
            echo json_encode($response);exit;            
        }
        
        $save                                       = array();
        $save['id']                                 = $this->input->post('lecture_id');
        $save['cl_lecture_name']                    = $this->input->post('lecture_name');
        $save['cl_lecture_description']             = $this->input->post('lecture_description');
        $save['cl_limited_access']                  = $this->input->post('cl_limited_access');
        $save['cl_limited_access']                  = ($save['cl_limited_access'])?$save['cl_limited_access']:'0';
        $save['cl_sent_mail_on_lecture_creation']   = $this->input->post('sent_mail_on_lecture_creation');
        $save['action_by']                          = $this->auth->get_current_admin('id');
        $save['action_id']                          = $this->actions['create'];
        $lecture_id                                 = $this->Course_model->save_lecture($save);
        $response['error']   = 'false';
        $response['message'] = lang('lecture_saved_success');
        $response['id']      = $lecture_id;
        echo json_encode($response);exit;            
    }
    
    function save_assesment_detail()
    {
        $response           = array();
        $response['error']  = 'false';
        $response['message']= '';
        
        $save               = array();
        $pass_percentage    = $this->input->post('pass_percentage');
        if(!$pass_percentage || !is_numeric($pass_percentage))
        {
            
            $response['error']   = 'true';
            $response['message'].= 'Enter a valid percentage';
        }
        else
        {
            if($pass_percentage < 25)
            {
                $response['error']   = 'true';
                $response['message'].= 'Pass password must be greater that 25';        
            }
            
        }
        
        if( $response['error'] == 'true' )
        {
            echo json_encode($response);exit;            
        }
        
        $save                                       = array();
        $save['id']                                 = $this->input->post('lecture_id');
        $save['cl_lecture_name']                    = $this->input->post('lecture_name');
        $save['cl_lecture_description']             = $this->input->post('lecture_description');
        $save['cl_sent_mail_on_lecture_creation']   = $this->input->post('sent_mail_on_lecture_creation');
        $save['cl_limited_access']                  = $this->input->post('cl_limited_access');
        $save['action_by']                          = $this->auth->get_current_admin('id');
        $save['action_id']                          = $this->actions['create'];
        $lecture_id                                 = $this->Course_model->save_lecture($save);
        
        $assesment              = $this->Course_model->assesment(array('lecture_id' => $this->input->post('lecture_id'), 'course_id' => $this->input->post('course_id')));        
        $save                      = array();
        $save['id']                = isset($assesment['assesment_id'])?$assesment['assesment_id']:false;
        $save['a_instructions']    = $this->input->post('lecture_instruction');
        $save['a_course_id']       = $this->input->post('course_id');
        $save['a_lecture_id']      = $this->input->post('lecture_id');
        $save['a_duration']        = $this->input->post('assesment_duration');
        $save['a_show_categories'] = $this->input->post('show_categories');
        $save['a_pass_percentage'] = $pass_percentage;
        $save['a_show_categories'] = ($save['a_show_categories'])?'1':'0';
        $save['action_by']         = $this->auth->get_current_admin('id');
        $save['action_id']         = $this->actions['create'];
        $assesment_id              = $this->Course_model->save_assesment($save);
        $this->invalidate_course(array('course_id' => $save['a_course_id']));
        $this->invalidate_course();
        $response['error']          = 'false';
        $response['message']        = lang('lecture_saved_success');
        $response['id']             = $lecture_id;
        $response['assesment_id']   = $assesment_id;
        echo json_encode($response);exit;            
    }
    function save_survey_detail() 
    {
        $response           = array();
        $response['error']  = 'false';
        $response['message']= '';
        if( $response['error'] == 'true' )
        {
            echo json_encode($response);exit;            
        }
        // echo "<pre>"; print_r($_FILES); die();
        $save                                       = array();
        if(isset($_FILES['lecture_image']) && $_FILES['lecture_image']['error'] !== 4)
        { 
            $allowed =  array('gif','png' ,'jpg', 'jpeg');
            $filename = $_FILES['lecture_image']['name'];
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if(in_array($ext,$allowed))
            {
                $lecture_id                         = $this->input->post('lecture_id');
                $version                            = rand(0,300);
                if($this->upload_course_lecture_image_to_localserver(array('course_id'=>$this->input->post('course_id'), 'lecture_id'=>$lecture_id )))
                {
                    $save['cl_lecture_image']               =  $lecture_id.".jpg?v=".$version;
                }
            }

        }
        $save['id']                                 = $this->input->post('lecture_id');
        $save['cl_lecture_name']                    = $this->input->post('lecture_name');
        $save['cl_lecture_description']             = $this->input->post('lecture_description');
        $save['action_by']                          = $this->auth->get_current_admin('id');
        $save['action_id']                          = '1';
        $lecture_id                                 = $this->Course_model->save_lecture($save);
        
        $survey                    = $this->Course_model->survey(array('lecture_id' => $this->input->post('lecture_id'), 'course_id' => $this->input->post('course_id')));        
        $save                      = array();
        $save['id']                = isset($survey['survey_id'])?$survey['survey_id']:false;
       
        $save['s_course_id']        = $this->input->post('course_id');
        $save['s_lecture_id']       = $this->input->post('lecture_id');
        $save['s_name']             = $this->input->post('lecture_name');
        $save['s_description']      = $this->input->post('lecture_description');
        $save['s_tutor_id']         = $this->input->post('tutor_id');
        $save['s_tutor_name']       = $this->input->post('tutor_name');
        $save['created_by']         = $this->auth->get_current_admin('id');
        $survey_id                  = $this->Course_model->save_survey($save);
        $this->invalidate_course(array('course_id' => $save['s_course_id']));
        $this->invalidate_course();
        $response['error']          = 'false';
        $response['message']        = 'Survey details saved successfully';
        $response['id']             = $lecture_id;
        $response['survey_id']      = $survey_id;
        echo json_encode($response);exit;  
    }
    function save_descriptive_detail()
    {
        $response           = array();
        $response['error']  = 'false';
        $response['message']= '';
        
        $save               = array();
        
        if( $response['error'] == 'true' )
        {
            echo json_encode($response);exit;            
        }
        $save                                       = array();
        if(isset($_FILES['lecture_image']) && $_FILES['lecture_image']['error'] !== 4)
        { 
            $allowed =  array('gif','png' ,'jpg', 'jpeg');
            $filename = $_FILES['lecture_image']['name'];
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if(in_array($ext,$allowed))
            {
                $lecture_id                         = $this->input->post('lecture_id');
                $version                            = rand(0,300);
                if($this->upload_course_lecture_image_to_localserver(array('course_id'=>$this->input->post('course_id'), 'lecture_id'=>$lecture_id )))
                {
                    $save['cl_lecture_image']               =  $lecture_id.".jpg?v=".$version;
                }
            }

        }
        $course_id                                  = $this->input->post('course_id');
        $save['id']                                 = $this->input->post('lecture_id');
        //$save['cl_course_id']                       = $this->input->post('course_id');
        $save['cl_lecture_name']                    = $this->input->post('test_title');
        $save['cl_lecture_description']             = $this->input->post('test_description');
        // $save['cl_lecture_content']                 = $this->input->post('lecture_content');
        $save['action_by']                          = $this->auth->get_current_admin('id');
        $save['action_id']                          = $this->actions['create'];
        $lecture_id                                 = $this->Course_model->save_lecture($save);
        $save                                       = array();
        $save['dt_name']                            = $this->input->post('test_title');
        $save['dt_description']                     = $this->input->post('test_description');
        $save['dt_last_date']                       = date_format(new Datetime($this->input->post('submission_date')),'Y-m-d');  
        $save['dt_words_limit']                     = $this->input->post('descriptive_words_limit');
        $save['dt_total_mark']                      = $this->input->post('total_mark');
        $save['dt_uploded_files']                   = $this->input->post('uploaded_files');
        $this->Course_model->update_descriptive_test($save, $lecture_id);
        $save                                       = array();
        $save['dt_uploded_files']                   = $this->input->post('uploaded_files');
        $this->Course_model->update_descriptive_test($save, $lecture_id);
        $this->invalidate_course(array('course_id' => $course_id));
        $this->invalidate_course();
        $response['error']                          = 'false';
        $response['message']                        = lang('lecture_saved_success');
        $response['id']                             = $lecture_id;
        echo json_encode($response);exit;
    }
    
    public function lecture($id=false)
    {
        if(!$id)
        {
            redirect(admin_url('course'));
        }
        $lecture      = $this->Course_model->lecture(array('direction'=>'DESC', 'id'=>  $id));
        //echo '<pre>';print_r($lecture);die();
        if(!$lecture)
        {
            redirect(admin_url('course'));
        }     
        if($this->__loggedInUser['rl_full_course']==0){
            $this->course_content_privilege   = $this->accesspermission->get_permission_course(array(
                                                    'role_id' => $this->__loggedInUser['role_id'], 
                                                    'module' => 'course_content',
                                                    'user_id' => $this->__loggedInUser['id'],
                                                    'course_id' => $lecture['cl_course_id']
                                                ));
        } else {
            $this->course_content_privilege   = $this->accesspermission->get_permission(array(
                                                        'role_id' => $this->__loggedInUser['role_id'], 
                                                        'module' => 'course_content'
                                                    ));
        }
        
        if(!in_array($this->privilege['edit'], $this->course_content_privilege))
        {
            redirect($this->config->item('admin_folder').'/coursebuilder/home/'.$lecture['cl_course_id']); exit;
        }
        if($lecture['cl_lecture_type']==3){
           
            redirect(admin_url('test_manager/test_basics/'.base64_encode($id)));
        }   
        $course_param               = $this->__role_query_filter;
        $course_param['id']         = $lecture['cl_course_id'];
        $course_basic               = $this->Course_model->course($course_param);
        $this->invalidate_course(array('course_id' => $lecture['cl_course_id']));
        $this->invalidate_course();
        if(!$course_basic){
            redirect($this->config->item('admin_folder').'/course');
        }
        $lecture_type = (isset($lecture['cl_lecture_type'])&& $lecture['cl_lecture_type'] > 0)?$lecture['cl_lecture_type']:$this->__lecture_type_keys_array['video'];
        $method       = $this->__lecture_type_array[$lecture_type];
        $lecture['course_name'] = $course_basic['cb_title'];
        $lecture['cl_access_restriction'] = json_decode($lecture['cl_access_restriction'], true);
        $lecture['cl_support_files']      = (($lecture['cl_support_files']!=NULL) || ($lecture['cl_support_files']!=''))?json_decode($lecture['cl_support_files'], true):array();
        // print_r($method);die;
        $this->$method($lecture);
    }    
    
    private function youtube($lecture)
    {
        $data                       = array();
        $data['title']              = $lecture['cl_lecture_name'];
        $data['lecture']            = $lecture;
        $data['lecture_icons']      = $this->__lecture_type_icons;
        // echo '<pre>';print_r($lecture);die;
        $data['course_details']  = $this->Course_model->course(array('id'=> $lecture['cl_course_id']));

        $has_s3_enabled                         = $this->settings->setting('has_s3');
        if($has_s3_enabled && ($has_s3_enabled['as_siteadmin_value']))
        {
            $s3_settings                        = $has_s3_enabled['as_setting_value']['setting_value'];
            $s3_domain   = $s3_settings->cdn;
            $s3_url      = "https://".$s3_domain."/uploads/".config_item('acct_domain')."/course/".$lecture['cl_course_id']."/course/lecture/".$lecture['cl_lecture_image'];
            $default_url = "https://".$s3_domain."/uploads/default/course/default-lecture.jpg";
            $headers     = @get_headers($s3_url); 
            if($headers && strpos( $headers[0], '200'))
            {
                //echo "exist";die();
                $data['s3_lecture_image'] = $s3_url;
            }
            else
            {
                $data['s3_lecture_image'] = $default_url;
            }
        }

        $this->load->view($this->config->item('admin_folder').'/'.$this->__builder_view.'/youtube', $data);
    }

    private function vimeo($lecture)
    {
        $data                       = array();
        $data['title']              = $lecture['cl_lecture_name'];
        $data['lecture']            = $lecture;
        $data['lecture_icons']      = $this->__lecture_type_icons;
        $data['course_details']  = $this->Course_model->course(array('id'=>$lecture['cl_course_id']));

        $has_s3_enabled                         = $this->settings->setting('has_s3');
        if($has_s3_enabled && ($has_s3_enabled['as_siteadmin_value']))
        {
            $s3_settings                        = $has_s3_enabled['as_setting_value']['setting_value'];
            $s3_domain   = $s3_settings->cdn;
            $s3_url      = "https://".$s3_domain."/uploads/".config_item('acct_domain')."/course/".$lecture['cl_course_id']."/course/lecture/".$lecture['cl_lecture_image'];
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

        // echo '<pre>';print_r($data);die;
        $this->load->view($this->config->item('admin_folder').'/'.$this->__builder_view.'/youtube', $data);
    }
    
    private function recorded_videos($lecture)
    {
        $data                       = array();
        $data['title']              = $lecture['cl_lecture_name'];
        $data['lecture']            = $lecture;
        $data['lecture_icons']      = $this->__lecture_type_icons;
        
        $live_details               = $this->Course_model->get_live_recording(array('lecture_id' => $lecture['id']));
        if(!empty($live_details)){
            $data['recorded_details'] = $live_details;
        }
        //echo '<pre>';print_r($lecture);die;
        $this->load->view($this->config->item('admin_folder').'/'.$this->__builder_view.'/recorded_video', $data);
    }
    private function scorm($lecture)
    {
        $data                       = array();
        $data['title']              = $lecture['cl_lecture_name'];
        $data['lecture']            = $lecture;
        $data['lecture_icons']      = $this->__lecture_type_icons;
        $data['course_details']  = $this->Course_model->course(array('id'=> $lecture['cl_course_id']));

        $has_s3_enabled                         = $this->settings->setting('has_s3');
        if($has_s3_enabled && ($has_s3_enabled['as_siteadmin_value']))
        {
            $s3_settings                        = $has_s3_enabled['as_setting_value']['setting_value'];
            $s3_domain   = $s3_settings->cdn;
            $s3_url      = "https://".$s3_domain."/uploads/".config_item('acct_domain')."/course/".$lecture['cl_course_id']."/course/lecture/".$lecture['cl_lecture_image'];
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

        // echo '<pre>';print_r($data['course_details']);die;
        $this->load->view($this->config->item('admin_folder').'/'.$this->__builder_view.'/scorm', $data);
    }
    private function assesment($lecture)
    {
        $data                           = array();
        $data['title']                  = $lecture['cl_lecture_name'];
        $data['lecture_id']             = $lecture['id'];
        $lecture['assesment']           = $this->Course_model->assesment(array('lecture_id' => $lecture['id'], 'course_id' => $lecture['cl_course_id']));
        $assesment_id                   = $lecture['assesment']['assesment_id'];
        //$lecture['questions']           = $this->Course_model->questions(array('assesment_id' => $lecture['assesment']['assesment_id'], 'not_deleted'=>'1'));
        //$data['lecture']                = $lecture;
        //$data['lecture_icons']          = $this->__lecture_type_icons;
       
        //echo '<pre>';print_r($lecture);die;
        redirect(admin_url('test_manager/test_basics/'.base64_encode($assesment_id)));
    }
    private function survey($lecture)
    {
        $data                       = array();
        $data['title']              = $lecture['cl_lecture_name'];
        $data['course_id']          = $lecture['cl_course_id'];
        $data['lecture_id']         = $lecture['id'];
        // $data['lecture_id']         = $lecture['id'];
        $lecture['survey']          = $this->Course_model->survey(array('lecture_id' => $lecture['id'], 'course_id' => $lecture['cl_course_id']));
        $lecture['questions']       = $this->Course_model->survey_questions(array('survey_id' => $lecture['survey']['survey_id']));
        $data['tutors']             = $this->Course_model->get_course_tutors(array('course_id' => $lecture['cl_course_id']));
        $data['lecture']            = $lecture;
        $data['course_details']  = $this->Course_model->course(array('id'=> $data['course_id']));

        $data['lecture_icons']      = $this->__lecture_type_icons;

        $has_s3_enabled                         = $this->settings->setting('has_s3');
        if($has_s3_enabled && ($has_s3_enabled['as_siteadmin_value']))
        {
            $s3_settings                        = $has_s3_enabled['as_setting_value']['setting_value'];
            $s3_domain   = $s3_settings->cdn;
            $s3_url      = "https://".$s3_domain."/uploads/".config_item('acct_domain')."/course/".$lecture['cl_course_id']."/course/lecture/".$lecture['cl_lecture_image'];
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

        // echo "<pre>"; print_r($lecture); die();
        $this->load->view($this->config->item('admin_folder').'/'.$this->__builder_view.'/survey', $data);
    }
    private function descriptive_test($lecture){
        $data                           = array();
        $data['title']                  = $lecture['cl_lecture_name'];
        $data['lecture_id']             = $lecture['id'];
        $data['test_details']           = $this->Course_model->get_desctriptive_question($lecture['id']);
        $data['lecture']                = $lecture;
        $data['lecture_icons']          = $this->__lecture_type_icons;
        $data['default_instruction']    = $this->assignment_instruction();
        $this->load->model('Grade_model');
        $this->load->model('Test_model');
        $data['grades']                 = $this->Grade_model->adminGrade(array('select'=>'id,gr_name,gr_range_from,gr_range_to','gr_deleted'=>true));
        $override_details               = array();
        $override_detail                = $this->Test_model->override_details(array('lo_lecture_id'=>$lecture['id']));
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
            $override_batches                       =   $this->Test_model->override_groups($override_info['lo_override_batches']);
            $override_details[$a]['groups']           = $override_batches['groups'];
            $override_details[$a]['group_id']         = $override_info['lo_override_batches'];
            $a++;
        }
        $data['override_details']   = $override_details;
        $this->load->model('Group_model');
        $course_id = empty($lecture['cl_course_id']) ? '' : $lecture['cl_course_id'];
        $select = 'groups.id,groups.gp_name,groups.gp_institute_id,groups.gp_institute_code,groups.gp_year';
        $data['course_groups'] = $this->Group_model->course_groups(array('course_id' => $course_id, 'select' => $select));
        $institution_groups = array_unique(array_column($data['course_groups'], 'gp_institute_id'));
        $institution        = array();
        if (!empty($institution_groups)) {
            foreach ($institution_groups as $institution_id) {
                $objects = array();
                $objects['key'] = 'institute_' . $institution_id;
                $callback = 'institute';
                $params = array('id' => $institution_id);
                $institution[] = $this->memcache->get($objects, $callback, $params);
            }
        }
        $data['course_details']  = $this->Course_model->course(array('id'=>$course_id));
        $data['institution'] = $institution;

        $has_s3_enabled                         = $this->settings->setting('has_s3');
        if($has_s3_enabled && ($has_s3_enabled['as_siteadmin_value']))
        {
            $s3_settings                        = $has_s3_enabled['as_setting_value']['setting_value'];
            // var_dump( $s3_settings);die;
            $s3_domain   = $s3_settings->cdn;
            $s3_url      = "https://".$s3_domain."/uploads/".config_item('acct_domain')."/course/".$lecture['cl_course_id']."/course/lecture/".$lecture['cl_lecture_image'];
            $default_url = "https://".$s3_domain."/uploads/default/course/default-lecture.jpg";
            $headers     = @get_headers($s3_url);
            if($headers && strpos( $headers[0], '200'))
            {
                // echo "exist";die();
                // echo $s3_url;die();
                $data['s3_lecture_image'] = $s3_url;
                
            }
            else
            {
                // echo $default_url;die();
                $data['s3_lecture_image'] = $default_url;
            }
        }


        $this->load->view($this->config->item('admin_folder').'/'.$this->__builder_view.'/descriptive_test_view', $data);
    }
    
    public function survey_question($id = false, $lecture_id = false)
    {        
        $response           = array();
        $response['error']  = 'false';
        $response['message']= '';
        // echo "<pre>"; print_r($this->input->post()); die();
        $id         = $this->input->post('q_id');
        $question   = $this->Course_model->survey_question(array('id' => $id));
        if($question){
            $response['question'] = $question;
        }
        else {
            $response['error']      = 'true';
            $response['message']    = '';
        }
        
        echo json_encode($response);exit;
        
    }
    public function save_survey_question() {
        $response           = array();
        $response['error']  = 'false';
        $response['message']= '';
        $data['lecture_id']         = $this->input->post('lecture_id');
        $lecture                    = $this->Course_model->lecture(array(
                                                        'direction'=>'DESC', 
                                                        'select' => 'id, cl_course_id, cl_section_id',
                                                        'id'=>  $data['lecture_id']
                                                    ));
        $course_param               = $this->__role_query_filter;
        $course_param['id']         = $lecture['cl_course_id'];
        $course_param['select']     = 'id, cb_title, cb_code';
        $course_basic               = $this->Course_model->course($course_param);
        // echo "<pre>"; print_r($this->input->post()); die();
        if(!$course_basic){
            redirect($this->config->item('admin_folder').'/course');
        }
        
        
        $data                       = array();
        $filter                     = array();
        $id                         = $this->input->post('q_id');
        $data['id']                 = false;
        if($id!= 0){
            $data['id']             = $id;
            $filter['update']       = true;
        }
        $data['sq_type']            = $this->input->post('q_type');
            
        $data['sq_question']         = $this->input->post('q_question');
        $data['sq_course_id']        = $lecture['cl_course_id'];
        $data['sq_lecture_id']       = $lecture['id'];
        $data['sq_survey_id']        = $this->input->post('survey_id');
        
        $options                     = $this->input->post('q_options');
        $data['sq_options']          = json_encode($options);
        $data['sq_low_limit']        = $this->input->post('q_low_range');
        $data['sq_high_limit']       = $this->input->post('q_high_range');
        $data['sq_required']         = ($this->input->post('is_required') != null)? $this->input->post('is_required') : "0";
        $question_id                = $this->Course_model->save_question_survey($data,$filter);
        // echo $this->db->last_query();exit;
        if($question_id) {
            $question               = $this->Course_model->survey_question(array('id' => $question_id));
            $response['error']      = 'false';
            $response['message']    = 'Survey question saved successfully';
            $response['id']         = $question_id;
            $response['question']   = $question;
        }
        else 
        {
            $response['error']  = 'true';
            $response['message'] = 'Question save failed';
        }
        $this->invalidate_course(array('course_id' => $lecture['cl_course_id']));
        $this->invalidate_course();
        
        echo json_encode($response);exit;
    }
    public function question($id=false, $lecture_id=false)
    {
        if(!$lecture_id)
        {
            redirect(admin_url('course'));
        }
        
        $this->load->library('form_validation');
        $data                       = array();
        $data['title']              = lang('question_form');
        $data['lecture_id']         = $lecture_id;
        $lecture                    = $this->Course_model->lecture(array('direction'=>'DESC', 'id'=>  $lecture_id));
        
        $course_param               = $this->__role_query_filter;
        $course_param['id']         = $lecture['cl_course_id'];
        $course_basic               = $this->Course_model->course($course_param);
        if(!$course_basic){
            redirect($this->config->item('admin_folder').'/course');
        }
        
        $lecture['assesment']       = $this->Course_model->assesment(array('lecture_id' => $lecture['id'], 'course_id' => $lecture['cl_course_id']));
        $data['lecture']            = $lecture;
        $data['lecture_icons']      = $this->__lecture_type_icons;
        
        $data['id']                 = $id;
        $data['q_type']             = $this->input->post('q_type');
        $data['q_difficulty']       = $this->input->post('q_difficulty');
        $data['q_category']         = $this->input->post('q_category');
        $data['q_positive_mark']    = $this->input->post('q_positive_mark');
        $data['q_negative_mark']    = $this->input->post('q_negative_mark');
        $data['q_directions']       = $this->input->post('q_directions');
        $data['q_question']         = $this->input->post('q_question');
        $data['q_explanation']      = $this->input->post('q_explanation');
        $data['q_options']          = $this->input->post('q_options');
        $data['q_answer']           = $this->input->post('q_answer');
        $data['q_course_id']        = $this->input->post('q_course_id');
        $data['q_status']           = $this->input->post('q_status');
        $data['q_account_id']       = $this->config->item('id');
        $data['action_by']          = $this->auth->get_current_admin('id');
        $data['action_id']          = $this->actions['create'];
        $data['created_date']       = date('Y-m-d H:i:s');
        $data['updated_date']       = date('Y-m-d H:i:s');
        if($id)
        {
            $question = $this->Course_model->question(array('id' => $id));
            if(!$question)
            {
                redirect(admin_url('course'));
            }
            $data['q_type']             = $question['q_type'];
            $data['q_difficulty']       = $question['q_difficulty'];
            
            $question_category_name     = $this->Category_model->question_category(array('id'=>$question['q_category']));
            $data['q_category']         = $question_category_name['qc_category_name'];
            
            $data['q_positive_mark']    = $question['q_positive_mark'];
            $data['q_negative_mark']    = $question['q_negative_mark'];
            $data['q_directions']       = $question['q_directions'];
            $data['q_question']         = $question['q_question'];
            $data['q_explanation']      = $question['q_explanation'];
            $data['q_options']          = $this->Course_model->options(array('q_answer' => $question['q_options']));
            $data['q_answer']           = ($question['q_answer'])?explode(',', $question['q_answer']):array();
            $data['q_course_id']        = $question['q_course_id'];
            $data['q_status']           = $question['q_status'];
            $data['q_account_id']       = $this->config->item('id');
            $data['action_by']          = $this->auth->get_current_admin('id');
            $data['action_id']          = $this->actions['create'];
            $data['updated_date']       = date('Y-m-d H:i:s');
            //echo '<pre>' ; print_r($data['q_options']);die;
        }
        $this->form_validation->set_rules('q_question', lang('question'), 'required');
        if ($this->form_validation->run() == false)
        {
            $data['error'] = validation_errors();
            $this->load->view($this->config->item('admin_folder').'/'.$this->__builder_view.'/question_form', $data);        
        }
        else
        {
            $data                       = array();
            $data['id']                 = $id;
            $data['q_type']             = $this->input->post('q_type');
            $data['q_difficulty']       = $this->input->post('q_difficulty');
            
            $category                   = $this->Category_model->question_category(array('category_name'=>$this->input->post('q_category')));
            if(!$category)
            {
                $category                   = array();
                $category['id']             = false;
                $q_category_course = $this->input->post('q_category') ;
                if($q_category_course!= '')
                {
                    $category['qc_category_name']   = $this->input->post('q_category');
                    $category['qc_parent_id']       = $course_basic['cb_category'];
                    $category['qc_status']          = '1';
                    $category['qc_account_id']      = $this->config->item('id');
                    $category['action_by']          = $this->auth->get_current_admin('id');
                    $category['action_id']          = $this->actions['create'];
                    $category['id']                 = $this->Category_model->save_question_category($category);
                }
            } 
            $data['q_category']         = ($category['id'] != '')?$category['id']:'1';
            $data['q_positive_mark']    = $this->input->post('q_positive_mark');
            $data['q_negative_mark']    = $this->input->post('q_negative_mark');
            $data['q_directions']       = $this->input->post('q_directions');
            $data['q_question']         = $this->input->post('q_question');
            $data['q_explanation']      = $this->input->post('q_explanation');
            $data['q_course_id']        = $lecture['cl_course_id'];
            $data['q_status']           = $this->input->post('q_status');
            $data['q_status']           = ($data['q_status'])?$data['q_status']:0;
            $data['action_by']          = $this->auth->get_current_admin('id');
            $data['q_account_id']       = $this->config->item('id');
            $data['action_id']          = $this->actions['create'];
            $data['created_date']       = date('Y-m-d H:i:s');
            $data['updated_date']       = date('Y-m-d H:i:s');
            $data['q_options']          = '';
            $data['q_answer']           = '';
            //=============================processing question objects======================
            
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
            $options         = $this->input->post('option');
            $recieved_answer = $this->input->post('answer');
            $q_options       = array();
            $q_answer        = array();
            
			switch($data['q_type'])
			{
				case $this->__single_type:
					if( !empty($options))
					{
						foreach ($options as $op_id => $value ) {
							$save               = array();
							$save['id']         = $op_id;
							$save['qo_options'] = $value;
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
						foreach ($options as $op_id => $value ) {
							$save               = array();
							$save['id']         = $op_id;
							$save['qo_options'] = $value;
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
				if(!empty($data['q_options']))
				{
					foreach ($data['q_options'] as $option) 
					{
						$this->Course_model->delete_option($option['id']);
					}					
				}
				break;
			}
            /*End*/
            
            /* insert the new options*/
            $options         = $this->input->post('option_new');
            $recieved_answer = $this->input->post('answer_new');
			switch($data['q_type'])
			{
				case $this->__single_type:
					if( !empty($options))
					{
						foreach ($options as $op_id => $value ) {
							$save               = array();
							$save['id']         = false;
							$save['qo_options'] = $value;
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
						foreach ($options as $op_id => $value ) {
							$save               = array();
							$save['id']         = false;
							$save['qo_options'] = $value;
							$option_id          = $this->Course_model->save_option($save);
							$q_options[]        = $option_id;
							if(in_array($op_id, $recieved_answer))
							{
								$q_answer[] = $option_id;
							}
						}
					}
				break;
				case $this->__subjective_type:
				break;
			}
            /*End*/
            //=====================================End===================================
            $data['q_options']          = implode(',', $q_options);
            $data['q_answer']           = implode(',', $q_answer);
            $question_id                = $this->Course_model->save_question($data);
            //saving assesment and questio nconnection
            $assesment_id = $lecture['assesment']['assesment_id'];
            $this->Course_model->save_assesment_question(array('assesment_id' => $assesment_id, 'question_id' => $question_id));
            //End
            $this->invalidate_course(array('course_id' => $lecture['cl_course_id']));
            $this->invalidate_course();
            redirect(admin_url('coursebuilder/lecture/'.$lecture_id));
        }
    }
    
    function get_question_category_list()
    { 
        $data 	 		= array();
        $keyword 		= $this->input->post('q_category');
        $categories		= $this->Category_model->question_categories(array('name'=>$keyword));
        
        //echo "<pre>";print_r($categories);die;
        $data['tags'] 	= array();
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
        if($uploaded)
        {
            $upload_data  	  =  $this->upload->data();
            //echo '<pre>'; print_r($upload_data);die;
            if($upload_data['file_ext'] == '.csv')
            {
                $this->upload_question_csv($upload_data);
            }
            else
            {
                $this->upload_question_doc($upload_data);
            }
            $response['message'] = lang('file_upload_success');
            $response['error']   = 'false';
        }
        else
        {
            $response['message'] = lang('file_upload_failed');
            $response['error']   = 'true';
        }
        
        echo json_encode($response);
    }
    function save_descriptive_test(){
        $response           = array();
        $response['error']  = 'false';
        $response['message']= '';
        $section_id         = $this->input->post('descriptive_section_id');
        $section_name       = $this->input->post('descriptive_section_name');
        $course_id          = $this->input->post('course_id');
        $total_mark         = $this->input->post('total_mark');
        
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
                if( $this->Course_model->section(array('filter_id'=>$save['id'], 'name'=>$save['s_name'], 'deleted'=>'0')) > 0 )
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
        $save['cl_lecture_name']                    = $this->input->post('descriptive_test_name');
        $save['cl_lecture_description']             = $this->input->post('descriptive_test_description');
        $save['cl_course_id']                       = $course_id;
        $save['cl_section_id']                      = $section_id;
        $highest_order                              = $this->Course_model->sections(array('count'=>true, 'section_id'=>$section_id, 'course_id'=> $course_id));
        $save['cl_order_no']                        = $highest_order+1;
        $save['cl_lecture_type']                    = $this->__lecture_type_keys_array['descriptive_test'];
        $save['action_by']                          = $this->auth->get_current_admin('id');
        $save['action_id']                          = $this->actions['create'];
        $save['cl_sent_mail_on_lecture_creation']   = $this->input->post('sent_mail_on_descriptive_creation');
        $lecture_id                                 = $this->Course_model->save_lecture($save);
        $user_data              = array();
        $user_data['user_id']   = $this->__loggedInUser['id'];
        $user_data['username']  = $this->__loggedInUser['us_name'];
        $user_data['useremail']  = $this->__loggedInUser['us_email'];
        $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
        
        $message_template                     = array();
        $message_template['username']         = $this->__loggedInUser['us_name'];;
        $message_template['lecture_name']     =  $save['cl_lecture_name'];
        $message_template['course_name']      =  $this->input->post('course_name');
        $triggered_activity     = 'lecture_created';
        log_activity($triggered_activity, $user_data, $message_template);
        $updated_date   = date('Y-m-d H:i:s');
        $lastdate                                   = date("Y-m-d", strtotime($this->input->post('submission_date')));
        
        $save_descriptive                   = array();
        $save_descriptive['dt_name']        = $this->input->post('descriptive_test_name');
        $save_descriptive['dt_description'] = $this->input->post('descriptive_test_description');
        $save_descriptive['dt_last_date']   = $lastdate; 
        $save_descriptive['dt_course_id']   = $course_id; 
        $save_descriptive['dt_words_limit'] = $this->input->post('descriptive_words_limit');
        $save_descriptive['action_by']      = $this->auth->get_current_admin('id');
        $save_descriptive['action_id']      = $this->actions['update'];
        $save_descriptive['updated_date']   = $updated_date;
        $save_descriptive['dt_lecture_id']  = $lecture_id;
        $save_descriptive['dt_total_mark']  = $total_mark;
        $save_descriptive['dt_instruction'] = $this->assignment_instruction();
        $this->Course_model->save_descriptive_test($save_descriptive);
        $this->invalidate_course(array('course_id' => $course_id));
        $this->invalidate_course();
        
        $response['error']   = 'false';
        $response['message'] = lang('assignment_saved_success');
        $response['id']      = $lecture_id;
        echo json_encode($response);exit;
    }
    
    function descriptive_question_upload(){
        $lecture_id                 = $this->input->post('lecture_id');
        
        $config                     = array();
        $config['upload_path']      = './'.  descriptive_question_path();
        if(!file_exists($config['upload_path'])){
            mkdir($config['upload_path'], 0777, true);
        }
        
        $config['allowed_types']    = 'pdf';
        $config['encrypt_name']     = true;
        $this->load->library('upload',$config);
        $this->upload->initialize($config);
        $this->upload->do_upload('file');
        $data = $this->upload->data();
        //upload log
        $myfile = fopen("uploads/upload.txt", "w");
        $txt = json_encode($this->upload->display_errors());
        fwrite($myfile, $txt);
        fclose($myfile);
        //end of log
        $save                               = array();
        $save['id']                         = $lecture_id;
        $save['cl_filename']                = $data['raw_name']."?v=".rand(10,1000);
        $save['cl_conversion_status']       = '1';
        $this->Course_model->save_lecture($save);
        
        $save                   = array();
        $save['dt_file']        = $data['file_name'];
        $this->Course_model->change_descriptive_file($save, $lecture_id);
        
        //converstion starts
        $this->load->library('ofabeeconverter');
        $config                 = array();
        $config['input']        = descriptive_question_path().$data['file_name']; 
        $config['output']       = FCPATH.descriptive_question_path(); 
        $config['s3_upload']    = false;
        $config['lecture_id']   = $lecture_id;
        $config['lecture_type'] = 8;
        $config['target_url']   = admin_url('coursebuilder/launch_conversion');
        $this->ofabeeconverter->initialize($config);
        //end
    }
    
    public function upload_question_doc($upload_data = false)
    {
        $this->load->model(array('Category_model'));
        $response        		= array();
        $response['message']	= lang('file_imported_success');
        $response['error']   	= 'false';
		
        $lecture_id      = $this->input->post('lecture_id');
        $lecture         = $this->Course_model->lecture(array('id'=>$lecture_id));
        $course_id       = $lecture['cl_course_id'];
        $assesment       = $this->Course_model->assesment(array('lecture_id' => $lecture_id, 'course_id' => $course_id));
        $assesment_id    = $assesment['assesment_id'];
        
        
        $full_path    	= $upload_data['full_path'];
        $extract_path 	= $upload_data['file_path'].$upload_data['raw_name'];
        //$full_path      = FCPATH.'uploads\question\localhost\e4e4ed866e184493ad147fc975be5f9f.zip';
        //$extract_path   = FCPATH.'uploads\question\localhost\e4e4ed866e184493ad147fc975be5f9f';
        
        $html_file      = '';
        $command = 'export HOME=/tmp && libreoffice --headless --convert-to html '.$full_path.' --outdir '.$extract_path.'';
        //echo $command;die;
        shell_exec($command);
        $html_file      = $extract_path.'/'.$upload_data['raw_name'].'.html';
        /*
	//creating directory. this will be created only if it is not yet created
        $directory      = question_upload_path();
        $this->make_directory($directory);
	//End
        //Unzp the file to the above directory
        $zip 			= new ZipArchive;
        if ($zip->open($full_path) === TRUE)
        {
            $zip->extractTo($extract_path);
            $zip->close();
        }
        //end
		
        //Find the name of html file 
	$extacted_files = scandir($extract_path);
        $html_file      = '';
        foreach ($extacted_files as $file)
        {
            if(strpos($file,'.htm'))
            {
                $html_file = $extract_path.'/'.$file;
            }
        }
        //End
	*/
        
        
        //echo '<pre>'; print_r($upload_data);die;
        /*        
        Array
        (
            [file_name] => 081f67b84f81484b7b47e932874ce6b2.zip
            [file_type] => application/zip
            [file_path] => /var/www/html/ofabeeversion3/uploads/question/localhost/
            [full_path] => /var/www/html/ofabeeversion3/uploads/question/localhost/081f67b84f81484b7b47e932874ce6b2.zip
            [raw_name] => 081f67b84f81484b7b47e932874ce6b2
            [orig_name] => e4e4ed866e184493ad147fc975be5f9f.zip
            [client_name] => e4e4ed866e184493ad147fc975be5f9f.zip
            [file_ext] => .zip
            [file_size] => 14.81
            [is_image] => 
            [image_width] => 
            [image_height] => 
            [image_type] => 
            [image_size_str] => 
        )
        */
        
        libxml_use_internal_errors(true);
        $html   = ($html_file);
        $doc    = new DOMDocument;
        $doc->loadHTMLFile($html);
        $columns   		= $doc->getElementsByTagName('td');
        $this->doc_objects      = array();
	    $column_count 		= 1;//this will be incremented in each loop. this is used to identify the loop is odd loop OR even loop
        $this->question_number  = 0;//for $this->doc_objects index
        $this->mechanism 	= 1;//ths is pointed to $method array. this will be increment in each loop(even loop only)
        $methods   		= array(
                                            1 => 'sl_no', 2 => 'set_question_type', 3 => 'set_difficulty',
                                            4 => 'set_direction', 5 => 'set_question', 6 => 'set_options',
                                            7 => 'set_answer', 8 => 'set_positive_mark', 9 => 'set_negative_mark',
                                            10 => 'set_catagory', 11 => 'set_explanation'
                        		);
        $end_of_option          = false;
        $subjective 		= false;
        $this->q_categories     = array();
        $image_count            = 0;
	//$upload_data['raw_name'] = 'e4e4ed866e184493ad147fc975be5f9f';
        //echo "<pre>";print_r($columns);die;
        foreach ($columns as $column)
        {
            $even_column = 	(($column_count%2) == 0)?true:false;
            // setting image path 
            $find_image =  $column->getElementsByTagName('img'); 
            
            foreach($find_image as $image)
            {
                //$image->setAttribute('src', question_path().$upload_data['raw_name'].'/'.$image->getAttribute('src'));
                $imageName = question_upload_path().$upload_data['raw_name'].'/image_'.($image_count).'.jpg';
                $imagePath = question_path().$upload_data['raw_name'].'/image_'.($image_count).'.jpg';
                
                $imageData = $image->getAttribute('src'); 
                $imageData = explode('base64,', $imageData);
                $imageData = isset($imageData[1])?$imageData[1]:'';
                $imageData = base64_decode($imageData); 
                $imageFile = fopen($imageName, "w");
                fwrite($imageFile, $imageData);
                fclose($imageFile);
                $image->setAttribute('src', $imagePath);
                $image_count++;
            } 
			
            //removing style and class      
            $find_p =  $column->getElementsByTagName('p'); 
            foreach($find_p as $p)
            {
                $p->removeAttribute('class');
                $p->removeAttribute('style');
            } 
			//save html      
            $column_html = trim($doc->saveXML($column));   
			
            //check the end of the question piece
            if( strtolower($this->trim_doc_objects($column_html)) == 'sl_no')
            {
                $this->sl_no();//reset the variables
            }
			//confirms the option is over
            if( strtolower($this->trim_doc_objects($column_html)) == 'answer')
            {
                /*
                 * switch to method set_answer. this is because the variable $this->mechanism is 
                 * reseted to 5, when its value is 6. this is to save all the option in option array.
                 * once all the option issaved then we swict to answer
                 */
                $this->mechanism = 7;
            }
			
            //checking whether the question isd subjecvtive
            if( strtolower($this->trim_doc_objects($column_html)) == 'subjective' )
            {
                    $subjective = true;
            }
			
            if(isset($methods[$this->mechanism]) && $this->mechanism > 0 && $even_column == true)
            {
		//call coresponding method to set the values in array $this->doc_objects
                $current_method = $methods[$this->mechanism];
                $this->$current_method($column_html);
				
                /*
                 * Basically subjective question dont have option. So in this case, when we reach 5(set_question)
                 * we skip method set_option and set_answer
                 */
                if($subjective==true && $this->mechanism==5)
                {
                        $this->mechanism = 7;
                        $subjective = false;
                }
				
                //recursing methos to set the option
                if($this->mechanism==6)
                {
                        $this->mechanism = 5;
                }
				
	        $this->mechanism++;
            }
			
            $column_count++;
        }
        //echo '<pre>'; print_r($this->doc_objects);die('===');
        
        //inserting question
        foreach ($this->doc_objects as $question)
        {
            
            $q_options       = array();
            $q_answer        = array();
            
            //preparing the values for first question
            $question_object                       = array();
            $question_object['id']                 = false;
            $question_object['q_type']             = $question['q_type'];
            $question_object['q_difficulty']       = $question['q_difficulty'];
            $question_object['q_positive_mark']    = $question['q_positive_mark'];
            $question_object['q_negative_mark']    = $question['q_negative_mark'];
            $question_object['q_directions']       = $question['q_directions'];
            $question_object['q_question']         = $question['q_question'];
            $question_object['q_explanation']      = $question['q_explanation'];
            $question_object['q_course_id']        = $course_id;
            $question_object['q_account_id']       = $this->config->item('id');            
			
            //processing options
            /* insert the new options*/
            switch($question_object['q_type'])
            {
                case $this->__single_type:
                    $options            = $question['q_option'];
                    $recieved_answer 	= $this->parse_answer_key(trim($question['q_answer']));
                    if( !empty($options))
                    {
                            foreach ($options as $op_id => $value ) 
                            {
                                //$trimmed_value = preg_replace('/\s+/', '', strip_tags($value));
                                $trimmed_value      = strip_tags($value,"<img>");
                                if( $trimmed_value != '' || (intval($op_id+1) == $recieved_answer))
                                {
                                    $save               = array();
                                    $save['id']         = false;
                                    $save['qo_options'] = $value;
                                    $option_id          = $this->Course_model->save_option($save);
                                    $q_options[]        = $option_id;
                                }
                                if(intval($op_id+1) == $recieved_answer)
                                {
                                        $q_answer[] = $option_id;
                                }
                            }
                    }
                    break;
                    
                    case $this->__multi_type:
                        $options            = $question['q_option'];
                        $recieved_answer    = explode(',', $question['q_answer']);
                        if(!empty($recieved_answer))
                        {
                            foreach($recieved_answer as $key => $value)
                            {
                                $recieved_answer[$key] = $this->parse_answer_key($value);
                            }
                        }
                        if( !empty($options))
                        {
                                foreach ($options as $op_id => $value ) 
                                {
                                    //$trimmed_value = preg_replace('/\s+/', '', strip_tags($value));
                                    $trimmed_value      = strip_tags($value,"<img>");
                                    if( $trimmed_value != '' || (in_array(intval($op_id+1), $recieved_answer)))
                                    {                                        
                                        $save               = array();
                                        $save['id']         = false;
                                        $save['qo_options'] = $value;
                                        $option_id          = $this->Course_model->save_option($save);
                                        $q_options[]        = $option_id;
                                    }
                                    if(in_array(intval($op_id+1), $recieved_answer))
                                    {
                                            $q_answer[] = $option_id;
                                    }
                                }
                        }
                    break;
                    
                    case $this->__subjective_type:
                    break;
            }
            /*End*/
            $question_object['q_category']         = $this->check_question_category($this->trim_text_custom($question['q_category']));
            $question_object['q_options']          = implode(',', $q_options);
            $question_object['q_answer']           = implode(',', $q_answer);
            $question_id                           = $this->Course_model->save_question($question_object);
            $this->Course_model->save_assesment_question(array('assesment_id' => $assesment_id, 'question_id' => $question_id));
            //end
           // print_r($question_object);
        }
        //End
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
    
    private function check_question_category($category_name)
    {
        if(!isset($this->q_categories[$category_name]))
        {
            $category = $this->Category_model->question_category(array('category_name' => $category_name));
            if(!$category)
            {
                $save                       = array();
                $save['id']                 = false;
                $save['qc_category_name']   = $category_name;
                $save['qc_status']          = '1';
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
        $this->mechanism = 1;
        $this->question_number++;
    }
	
    private function set_question_type($row_html)
    {
        $temp_html  	= $this->getTextBetweenTags($row_html);
        $line       	= $this->trim_doc_objects($temp_html);
	$question_types = array( 'single_choice' =>  'single', 'multiple_choice' =>  'multiple', 'subjective' =>  'subjective', );
        $question_mode  = isset($question_types[$line])?$question_types[$line]:'subjective';
        $this->doc_objects[$this->question_number]['q_type'] = $this->__question_types[$question_mode];
    }
    private function set_difficulty($row_html)
    {
        $temp_html  = $this->getTextBetweenTags($row_html);
        $line       = $this->trim_doc_objects($temp_html);
        $this->doc_objects[$this->question_number]['q_difficulty'] = isset($this->__difficulty[$line])?$this->__difficulty[$line]:$this->__difficulty['easy'];
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
	
    private function set_catagory($row_html)
    {
        $temp_html  = $this->getTextBetweenTags($row_html);
        $line       = $this->trim_doc_objects($temp_html, false, false);
        $this->doc_objects[$this->question_number]['q_category'] = $line;
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
            $question_object['q_account_id']       = $this->config->item('id');
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
            $this->Course_model->save_assesment_question(array('assesment_id' => $assesment_id, 'question_id' => $question_id));
            //end
           // print_r($question_object);
        }
        fclose($file);
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
        
        if(!$this->Course_model->delete_question($question_id))
        {
            $response['error']      = 'true';
            $response['message']    = lang('question_deleted_failed');    
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
        $count_questions        = $this->Course_model->questions(array('assesment_id'=>$assesment_id, 'not_deleted'=>true));
        $get_assessment         = $this->Course_model->get_assesment(array('assesment_id'=>$assesment_id));
        
        $response['question_count'] = sizeof($count_questions);
        if($count_questions == 0){
            $save_lecture               = array();
            $save_lecture['id']         = $get_assessment['a_lecture_id'];
            //echo $get_assessment['a_lecture_id'];die;
            $save_lecture['cl_status']  = '0';
            $this->Course_model->save_lecture($save_lecture);
        }
        echo json_encode($response);
    }
    function delete_survey_question()
    {
        $response               = array();
        $question_id            = $this->input->post('question_id');
        $survey_id              = $this->input->post('survey_id');
        $course_name              = $this->input->post('course_name');
        $filter_param               = array();
        $filter_param['survey_id']  = $survey_id;
        $filter_param['select']     = 'sq_survey_id,sq_lecture_id,sq_course_id';
        $survey                     = $this->Course_model->survey_question($filter_param);
        $result                     = $this->Course_model->delete_survey_question(array('id' => $question_id));
        if(!$result)
        {
            $response['error']      = 'true';
            $response['message']    = lang('question_deleted_failed');    
        }else{
            $user_response_param                = array();
            $user_response_param['question_id'] = $question_id;
            $this->Course_model->delete_survey_user_response($user_response_param);
            $survey_param               = array();
            $survey_param['survey_id']  = $survey_id;
            $survey_questions           = $this->Course_model->survey_questions_count($survey_param);
            if($survey_questions == 0){
                $data = array(
                    'status'        => 0,
                    'lecture_id'    => $survey['sq_lecture_id'],
                    'course_id'     => $survey['sq_course_id'],
                    'course_name'   => $course_name
                );
                $response = $this->change_lecture_status($data);
                $response['status'] = '0';
            }
            $response['error']      = 'false';
            $response['message']    = lang('question_deleted_success');
        }
        // echo "<pre>";print_r($response);exit;
        echo json_encode($response);
    }
    
    function lecture_downloadable()
    {
        $lecture_id     = $this->input->post('lecture_id');
        $downloadable   = $this->input->post('downloadable');
        $save                       = array();
        $save['id']                 = $lecture_id;
        $save['cl_downloadable']    = $downloadable;
        $this->Course_model->save_lecture($save);
        echo $downloadable;
    }
    
    private function video($lecture)
    {
        $data                               = array();
        $data['title']                      = $lecture['cl_lecture_name'];
        $data['lecture']                    = $lecture;
        $data['lecture_icons']              = $this->__lecture_type_icons;
        $data['course_details']             = $this->Course_model->course(array('id'=>$lecture['cl_course_id']));

        $content_security                   = $this->settings->setting('has_content_security');
        $content_security_status            = $content_security['as_setting_value']['setting_value']->content_security_status;
        $data['content_security_status']    = $content_security_status;
        $has_s3_enabled                         = $this->settings->setting('has_s3');
        if($has_s3_enabled && ($has_s3_enabled['as_siteadmin_value']))
        {
            $s3_settings                        = $has_s3_enabled['as_setting_value']['setting_value'];
            $s3_domain         = $s3_settings->cdn;
            $s3_url    = "https://".$s3_domain."/uploads/".config_item('acct_domain')."/course/".$lecture['cl_course_id']."/course/lecture/".$lecture['cl_lecture_image'];
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

        $this->load->view($this->config->item('admin_folder').'/'.$this->__builder_view.'/video', $data);
    }
    
    private function audio($lecture)
    {
        $data                       = array();
        $data['title']              = $lecture['cl_lecture_name'];
        $data['lecture']            = $lecture;
        $data['lecture_icons']      = $this->__lecture_type_icons;
        $data['course_details']  = $this->Course_model->course(array('id'=>$lecture['cl_course_id']));

        $has_s3_enabled                         = $this->settings->setting('has_s3');
        if($has_s3_enabled && ($has_s3_enabled['as_siteadmin_value']))
        {
            $s3_settings                        = $has_s3_enabled['as_setting_value']['setting_value'];
            $s3_domain         = $s3_settings->cdn;
            $s3_url    = "https://".$s3_domain."/uploads/".config_item('acct_domain')."/course/".$lecture['cl_course_id']."/course/lecture/".$lecture['cl_lecture_image'];
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

        // echo '<pre>';print_r($data);die;
        $this->load->view($this->config->item('admin_folder').'/'.$this->__builder_view.'/audio', $data);
    }
    
    private function document($lecture)
    {
        $data                       = array();
        $data['title']              = $lecture['cl_lecture_name'];
        $data['lecture']            = $lecture;
        $data['lecture_icons']      = $this->__lecture_type_icons;
        $data['course_details']  = $this->Course_model->course(array('id'=>$lecture['cl_course_id']));
        $has_s3_enabled                         = $this->settings->setting('has_s3');
        if($has_s3_enabled && ($has_s3_enabled['as_siteadmin_value']))
        {
            $s3_settings                        = $has_s3_enabled['as_setting_value']['setting_value'];
            $s3_domain         = $s3_settings->cdn;
            $s3_url    = "https://".$s3_domain."/uploads/".config_item('acct_domain')."/course/".$lecture['cl_course_id']."/course/lecture/".$lecture['cl_lecture_image'];
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
        $this->load->view($this->config->item('admin_folder').'/'.$this->__builder_view.'/document', $data);
    }
    private function text($lecture)
    {
        $data                       = array();
        $data['title']              = $lecture['cl_lecture_name'];
        $data['lecture']            = $lecture;
        $data['lecture_icons']      = $this->__lecture_type_icons;
        // echo '<pre>';print_r($data['lecture']);die;
        $data['course_details']  = $this->Course_model->course(array('id'=>$lecture['cl_course_id']));
        $has_s3_enabled                         = $this->settings->setting('has_s3');
        if($has_s3_enabled && ($has_s3_enabled['as_siteadmin_value']))
        {
            $s3_settings                        = $has_s3_enabled['as_setting_value']['setting_value'];
            $s3_domain         = $s3_settings->cdn;
            $s3_url    = "https://".$s3_domain."/uploads/".config_item('acct_domain')."/course/".$lecture['cl_course_id']."/course/lecture/".$lecture['cl_lecture_image'];
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
        $this->load->view($this->config->item('admin_folder').'/'.$this->__builder_view.'/html', $data);            
    }
    private function wikipedia($lecture)
    {
        $data                       = array();
        $data['title']              = $lecture['cl_lecture_name'];
        $data['lecture']            = $lecture;
        $data['lecture_icons']      = $this->__lecture_type_icons;
        //echo '<pre>';print_r($lecture);die;
        $this->load->view($this->config->item('admin_folder').'/'.$this->__builder_view.'/wikipedia', $data);
    }
    private function live($lecture)
    {
        $data                       = array();
        $data['title']              = $lecture['cl_lecture_name'];
        $data['lecture_icons']      = $this->__lecture_type_icons;
        $live_lecture               = $this->Course_model->live_lecture(array('lecture_id' => $lecture['id'], 'course_id' => $lecture['cl_course_id']));
        // echo '<pre>';print_r($lecture);die;
        if(!empty($live_lecture))
        {
            foreach( $live_lecture as $key => $value)
            {
                $lecture[$key] = $value;
            }
        }
        $data['lecture']            = $lecture;
    
        $data['studios']            = $this->Course_model->studios();
        $data['course_details']     = $this->Course_model->course(array('id'=>$lecture['cl_course_id']));

        $has_s3_enabled             = $this->settings->setting('has_s3');
        if($has_s3_enabled && ($has_s3_enabled['as_siteadmin_value']))
        {
            $s3_settings            = $has_s3_enabled['as_setting_value']['setting_value'];
            $s3_domain              = $s3_settings->cdn;
            $s3_url                 = "https://".$s3_domain."/uploads/".config_item('acct_domain')."/course/".$lecture['cl_course_id']."/course/lecture/".$lecture['cl_lecture_image'];
            $default_url            = "https://".$s3_domain."/uploads/default/course/default-lecture.jpg";
            $headers                = @get_headers($s3_url); 
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

        // echo '<pre>';print_r($data);die; 
        $this->load->view($this->config->item('admin_folder').'/'.$this->__builder_view.'/live', $data);            
    }
    
    function publish_record_live() 
    {
        $lecture_id = $this->input->post('lecture_id');
        $record_id  = $this->input->post('recording_id');
        $lecture_details = $this->Course_model->lecture(array('id' => $lecture_id));
        
        $recorded_details = $this->Course_model->recorded_video(array('id' => $record_id));
        
        $response   = array();
        $response['lecture_name']   = $lecture_details['cl_lecture_name'];
        $response['recorded_name']  = $recorded_details['llr_title'];
        
        echo json_encode($response);
        
    }
    function publish_recorded_live()
    {
        $lecture_id = $this->input->post('lecture_id');
        $live_id    = $this->input->post('live_id');
        $record_id  = $this->input->post('recording_id');
        
        $lecture_details = $this->Course_model->lecture(array('id' => $lecture_id));
        
        //echo "<pre>";print_r($lecture_details);die;
        
        $save_lecture = array();
        $save_lecture['cl_status']  = '0';
        $save_lecture['cl_deleted'] = '0';
        $save_lecture['id']         = $lecture_id;
        
        $this->Course_model->save_lecture($save_lecture);
        
        $live_recording_details = $this->Course_model->get_live_recording(array('live_id' => $live_id, 'id' => $record_id));
        //echo "<pre>";print_r($live_recording_details);die;
        //echo $total_live_recordings."----".$active_live_recordings_count;die;
        
       
            $save_recorded_video                    = array();
            $save_recorded_video['id']              = false;
            $save_recorded_video['cl_lecture_name'] = $lecture_details['cl_lecture_name']." - ".$live_recording_details['llr_title'];
            $save_recorded_video['cl_filename']     = $live_recording_details['llr_clip_id'];
            $save_recorded_video['cl_course_id']    = $lecture_details['cl_course_id'];
            $save_recorded_video['cl_section_id']   = $lecture_details['cl_section_id'];     
            $highest_order                          = $this->Course_model->sections(array('count'=>true, 'section_id'=>$lecture_details['cl_section_id'], 'course_id'=> $lecture_details['cl_course_id']));
            $save_recorded_video['cl_order_no']     = $highest_order+1;
            $save_recorded_video['cl_lecture_type'] = '9';
            $save_recorded_video['cl_status']       = '1';
            $save_recorded_video['action_by']       = $this->auth->get_current_admin('id');
            $save_recorded_video['action_id']       = $this->actions['create'];
            
            $course_lecture_id      = $this->Course_model->save_lecture($save_recorded_video);
            
            $update_lecture_id                   = array();
            $update_lecture_id['llr_live_id']    = $live_id;
            $update_lecture_id['llr_lecture_id'] = $course_lecture_id;
            
            //echo "<pre>";print_r($update_lecture_id);die;  
            
            $this->Course_model->update_live_recording($update_lecture_id);
            
            $get_live_attend_users  = $this->Course_model->get_live_users_attended(array('live_id' => $live_id));
            
            
            $save_activate_record               = array();
            $save_activate_record['id']         = $live_recording_details['id'];
            $save_activate_record['llr_status'] = '1';
            
            $this->Liveservice_model->save_live_recording($save_activate_record);
            
            
            foreach ($get_live_attend_users as $attended_users)
            {
                $save_live_lecture_users_log                    = array();
                $save_live_lecture_users_log['id']              = false;
                $save_live_lecture_users_log['ll_lecture_id']   = $course_lecture_id;
                $save_live_lecture_users_log['ll_user_id']      = $attended_users['llu_user_id'];
                $save_live_lecture_users_log['ll_percentage']   = '100';
                $save_live_lecture_users_log['ll_attempt']      = '1';
                
                $this->Course_model->save_lecture_log($save_live_lecture_users_log);
            }
            
            $total_live_recordings  = $this->Course_model->get_live_recordings(array('live_id' => $live_id, 'count' => '1'));
            $active_live_recordings = $this->Course_model->get_live_recordings(array('live_id' => $live_id, 'status' => '1'));
            $active_live_recordings_count = sizeof($active_live_recordings);
            
            /*$save_live_lecture_users_log                    = array();
            $save_live_lecture_users_log['id']              = false;
            $save_live_lecture_users_log['ll_lecture_id']   = $course_lecture_id;
            $save_live_lecture_users_log['ll_user_id']      = $live_recording['llu_user_id'];
            $save_live_lecture_users_log['ll_percentage']   = '100';
            $save_live_lecture_users_log['ll_attempt']      = '1';
            
            $this->Course_model->save_lecture_log($save_live_lecture_users_log);
            
            $this->Course_model->delete_live_users($live_recording['llr_live_id']);*/
            
            
        if($total_live_recordings == $active_live_recordings_count){
            
            $save_lecture_delete                = array();
            $save_lecture_delete['cl_status']   = '0';
            $save_lecture_delete['cl_deleted']  = '1';
            $save_lecture_delete['id']          = $lecture_id;
            
            
            $this->Course_model->save_lecture($save_lecture_delete);
            
            //echo "<pre>";print_r($save_lecture_delete);die;
            
            $this->Course_model->delete_live_users($live_id);
        }
        $response = array();
        $response['message'] = "true";
        $response['course_id'] = $lecture_details['cl_course_id'];
        $this->invalidate_course(array('course_id'=>$save_recorded_video['cl_course_id']));
        echo json_encode($response);
        
    }
    
    private function lecture_type_from_url($url)
    {
        $url_temp = $url;
        /*$url_temp   = 'https://en.wikipedia.org/wiki/Dorothy';
        $url_temp   = 'https://www.youtube.com/watch?v=7LcFZNuZaVY&t=95s';
        $url_temp = 'https://youtu.be/z6Olg2YRPC4';*/
        $url_temp = explode('.', $url_temp);
        if (strpos($url, 'vimeo') > 0) {
            $url_temp[1] = 'vimeo';
            $type        = 'vimeo';
        }
        /*echo '<pre>'; print_r($url_temp);die;*/
        $return   = '';
        $type     = '';
        if (isset($url_temp[1])&& $url_temp[1] != '')
        {
            $type = $url_temp[1];
            if (strpos($type,'be/') !== false) {
                $type     = 'youtube';
            }
            if(!in_array($type, array('youtube', 'wikipedia', 'vimeo', 'video')))
            {
                $type   = '';
                $return = '';
            }
            else
            {
                $return = $this->__lecture_type_keys_array[$type];
            }
        }

        // var_dump($type);die;
        return $return;
    }
    
    private function get_upload_config($mechanism=false, $course_id = 0, $support_file = false)
    {
        if(!$mechanism)
        {
            return false;
        }
        $video_formats          = array('mp4', 'flv', 'avi', 'f4v');
        $document_formats       = array('doc', 'odt','ods','odp','xls','xlsx','docx', 'pdf', 'ppt', 'pptx');
        $scorm_formats          = array('zip');
        $audio_formats          = array('mp3');
        $image_formats          = array('jpg', 'png', 'jpeg');
        $config                 = array();
        $config['encrypt_name'] = true;
        if(in_array($mechanism, $video_formats))
        {
            $this->__lecture_type       = $this->__lecture_type_keys_array['video'];
            $directory                  = video_upload_path(array('course_id' => $course_id));
            $this->make_directory($directory);
            $config['upload_path']      = $directory;
            $config['allowed_types']    = implode('|', $video_formats);      
            return $config;
        }
        
        if(in_array($mechanism, $document_formats))
        {
            $this->__lecture_type       = $this->__lecture_type_keys_array['document'];
            $directory                  = document_upload_path(array('course_id' => $course_id));
            if($support_file)
            {
                $directory                  = supportfile_upload_path(array('course_id' => $course_id));
            }
            $this->make_directory($directory);
            $config['upload_path']      = $directory;
            //echo '<pre>';print_r($document_formats);die();
            $config['allowed_types']    = implode('|', $document_formats);    
            return $config;
        }

        
        if(in_array($mechanism, $scorm_formats))
        {
            $this->__lecture_type       = $this->__lecture_type_keys_array['scorm'];
            $directory                  = scorm_upload_path(array('course_id' => $course_id));
            $this->make_directory($directory);
            $config['upload_path']      = $directory;
            $config['allowed_types']    = implode('|', $scorm_formats);       
            return $config;
        }
        if(in_array($mechanism, $audio_formats))
        {
            $this->__lecture_type       = $this->__lecture_type_keys_array['audio'];
            $directory                  = audio_upload_path(array('course_id' => $course_id));
            $this->make_directory($directory);
            $config['upload_path']      = $directory;
            $config['allowed_types']    = implode('|', $audio_formats);       
            return $config;
        }

        if(in_array($mechanism, $image_formats))
        {
            //$this->__lecture_type       = $this->__lecture_type_keys_array['video'];
            $directory                  = supportfile_upload_path(array('course_id' => $course_id));
            $this->make_directory($directory);
            $config['upload_path']      = $directory;
            $config['allowed_types']    = implode('|', $image_formats);      
            return $config;
        }
    }
    
    
    function index()
    {
        redirect($this->config->item('admin_folder').'/course');
    }
    
    function home($id=false)
    {
        // if(!in_array($this->privilege['view'], $this->course_privilege))
        // {
        //     redirect(admin_url()); exit;
        // }
    //    echo "<pre>";print_r('dtdsgsd');exit;
        if( !$id )
        {
            $this->session->set_flashdata('message', lang('course_not_found'));
            redirect($this->config->item('admin_folder').'/course');
        }
        if($this->__loggedInUser['rl_full_course']==0){
            $this->course_content_privilege   = $this->accesspermission->get_permission_course(array(
                                                    'role_id' => $this->__loggedInUser['role_id'], 
                                                    'module' => 'course_content',
                                                    'user_id' => $this->__loggedInUser['id'],
                                                    'course_id' => $id
                                                ));
        } else {
            $this->course_content_privilege   = $this->accesspermission->get_permission(array(
                                                        'role_id' => $this->__loggedInUser['role_id'], 
                                                        'module' => 'course_content'
                                                    ));
        }
        $this->report_privilege           = $this->accesspermission->get_permission(array('role_id' => $this->__loggedInUser['role_id'], 'module' => 'report'));
        if(!in_array($this->privilege['view'], $this->course_content_privilege))
        {
            redirect($this->config->item('admin_folder').'/course/basic/'.$id); exit;
        }
        // echo "<pre>"; print_r($this->course_content_privilege); die();
        if($this->__loggedInUser['rl_full_course']==0){
            $this->__quiz_permission  = $this->accesspermission->get_permission_course(
                                                        array(
                                                            'role_id' => $this->__loggedInUser['role_id'],
                                                            'module' => 'quiz_manager',
                                                            'user_id' => $this->__loggedInUser['id'],
                                                            'course_id' => $id ));
        } else {
            $this->__quiz_permission  = $this->accesspermission->get_permission(
                                                        array(
                                                            'role_id' => $this->__loggedInUser['role_id'],
                                                            'module' => 'quiz_manager'
                                                            ));
        }
        $course_param         = $this->__role_query_filter;
        $course_param['id']   = $id;
        $course               = $this->Course_model->course($course_param);
        $this->load->model("Grade_model");
        if( !$course )
        {
            $this->session->set_flashdata('message', lang('course_not_found'));
            redirect($this->config->item('admin_folder').'/course');
        }
        
        if($course['cb_deleted'] == '1')
        {
            $this->session->set_flashdata('message', lang('course_not_found'));
            redirect($this->config->item('admin_folder').'/course');
        }
        
        $data['id']                 = $id;
        $data['title']              = $course['cb_title'];
        $data['course']             = $course;
        $sections                   = $this->Course_model->sections(array('direction'=>'ASC', 'order_by'=>'s_order_no', 'course_id'=>  $id));
        $data['grades']             = $this->Grade_model->adminGrade(array('select'=>'id,gr_name,gr_range_from,gr_range_to','gr_deleted'=>true));
        $data['sections']           = array();
        $data['total_sections']     = sizeof($sections);
        $data['lecture_icons']      = $this->__lecture_type_icons;
        $data['total_lecture']      = 0;
        if(!empty($sections))
        {
            foreach ($sections as $section) 
            {
                $section['lecture']     = $this->Course_model->lectures(array('direction'=>'ASC' , 'order_by'=>'cl_order_no', 'course_id'=>  $id, 'section_id' => $section['id']));
                $data['total_lecture']  = sizeof($section['lecture'])+$data['total_lecture'];
                $section['total']       = sizeof($section['lecture']);
                $data['sections'][]     = $section;
            }
        }
        $data['studios']                = $this->Course_model->studios();

        $has_s3_enabled                         = $this->settings->setting('has_s3');
        if($has_s3_enabled && ($has_s3_enabled['as_siteadmin_value']))
        {
            $s3_settings                        = $has_s3_enabled['as_setting_value']['setting_value'];
            $s3_domain                          = $s3_settings->cdn;
            $s3_url                             = "https://".$s3_domain."/uploads/".config_item('acct_domain')."/course/".$id."/course/section/";
            $default_url                        = "https://".$s3_domain."/uploads/default/course/default-section.jpg";
            $headers                            = @get_headers($s3_url); 
            $data['s3_section_image'] = $s3_url;
            // if($headers && strpos( $headers[0], '200'))
            // {
            //     //echo "exist";die();
            //     $data['s3_section_image'] = $s3_url;
            // }
            // else
            // {
            //     //echo "not exist";die();
            //     $data['s3_section_image'] = $default_url;
            // }
        }

        // echo '<pre>'; print_r($data);die;
        $this->load->view($this->config->item('admin_folder').'/'.$this->__builder_view.'/home', $data);
    }
    function get_section_details()
    {
        $response = array();
        $section_id = $this->input->post('id');
        $data = $this->Course_model->section(['id' => $section_id]);
        $response['data'] = $data;
        echo json_encode($response);
    }

    function check_file_exist()
    {
        $response  = array();
        $course_id =  $this->input->post('course_id');
        $section_id = $this->input->post('section_id');
        $s3_url    = $this->input->post('file_url');
        $has_s3_enabled                         = $this->settings->setting('has_s3');
        if($has_s3_enabled && ($has_s3_enabled['as_siteadmin_value']))
        {
            $s3_settings                        = $has_s3_enabled['as_setting_value']['setting_value'];
            $s3_domain         = $s3_settings->cdn;
            //$s3_url    = "https://".$s3_domain."/uploads/".config_item('acct_domain')."/course/".$course_id."/course/section/".$section_id;
            $default_url = "https://".$s3_domain."/uploads/default/course/default-section.jpg";
            $headers     = @get_headers($s3_url); 
            $data['s3_section_image'] = $s3_url;
            if($headers && strpos( $headers[0], '200'))
            {
                // echo "exist";die();
                //$data['s3_section_image'] = $s3_url;
                $response['s3'] = true;
                $response['s3_section_url'] = $s3_url;
            }
            else
            {
                // echo "not exist";die();
                //$data['s3_section_image'] = $default_url;
                $response['s3'] = true;
                $response['s3_section_url'] = $default_url;
            }
        }
        else
        {
            $headers = @get_headers($s3_url);
            if($headers && strpos( $headers[0], '200'))
            {
                $response['s3'] = false;
                $response['file_exist'] = true;
                
            }
            else
            {
                $response['s3'] = false;
                $response['file_exist'] = false;
            }
        }
        echo json_encode($response);
    }
    
    function course_objects()
    {
        $data                       = array();
        $data['sections']           = array();
        $course_id                  = $this->input->post('course_id');
        $sections                   = $this->Course_model->sections(array('direction'=>'ASC', 'order_by'=>'s_order_no', 'course_id'=>  $course_id));
        //echo '<pre>';print_r($sections);
        if(!empty($sections))
        {
            foreach ($sections as $section) {
                $section['lecture']     = $this->Course_model->lectures(array( 'avoid_lecture_types' => array('7'), 'direction'=>'ASC' , 'order_by'=>'cl_order_no', 'course_id'=>  $course_id, 'section_id' => $section['id'] , 'skip_copy_progres_lecture'=>true));
                $data['sections'][]     = $section;
            }
        }
        $data['lecture_icons']      = $this->__lecture_type_icons;
        //echo '<pre>';print_r($data);
        echo json_encode($data);
    }
    
    function change_status($param = array())
    { 
        
        $course_id = isset($param['course_id'])?$param['course_id']:$this->input->post('course_id');
        $course_param           = array();
        $course_param['id']     = $course_id;
        
        $course_param['select'] = 'id, cb_image,cb_description,cb_category,cb_status, cb_title, cb_deleted';
        $course                 = $this->Course_model->course($course_param);
        if ($course['cb_image'] == 'default.jpg' && $course['cb_status'] == '0') {
            $response['error']      = true;
            $response['message']    = lang('upload_image_to_publish');
            echo json_encode($response);
            exit;
        }
        if (strip_tags($course['cb_description']) == '' && $course['cb_status'] == '0') {
            $response['error']      = true;
            $response['message']    = lang('decription_to_publish');
            echo json_encode($response);
            exit;
        }
        if ($course['cb_category'] == '' && $course['cb_status'] == '0') {
            $response['error']      = true;
            $response['message']    = lang('category_to_publish');
            echo json_encode($response);
            exit;
        }
        $course_lectures_count = $this->Course_model->get_course_lecture_count(array('course_id' => $course_id));
        
        if ($course_lectures_count < 1 && $course['cb_status'] == '0') {
            $response['error']      = true;
            $response['message']    = lang('error_change_status');
            echo json_encode($response);
            exit;
        }
        
        $save                   = array();
        $save['id']             = $course_id;
        $save['action_by']      = $this->auth->get_current_admin('id');
        $save['updated_date']   = date('Y-m-d H:i:s');
        
        $save['cb_status']      = '1';
        $mstatus                = 'course_activated'; 
        
        if ($course['cb_status'] == '1') {            
            $save['cb_status']   = '0';
            $mstatus             = 'course_deactivated'; 
            $response['message'] = lang('unpublished');
        }

        if($course['cb_status']   == '0')
        {
            $cb_status              = $this->checkcoursecontent(array('course_id'=>$course_id));
            $response['cb_status']  = $cb_status['cb_status'];
            if($cb_status['cb_status'] == '0')
            {
                $response['error']      = true;
                $response['message']    = 'Please activate atleast one lecture first!';
                echo json_encode($response);
                exit;
            }
        }
           $response['message']  = lang('published');
        
        if (!$this->Course_model->save($save)) {
            $response['error']   = true;
            $response['message'] = lang('error_change_status');
        }else{
            $response = array();
            $response['status']= $save['cb_status'];
            $response['error'] = false;
            $user_data              = array();
            $user_data['user_id']   = $this->__loggedInUser['id'];
            $user_data['username']  = $this->__loggedInUser['us_name'];
            $user_data['useremail'] = $this->__loggedInUser['us_email'];
            $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
            
            $message_template                   = array();
            $message_template['username']       = $this->__loggedInUser['us_name'];;
            $message_template['course_name']    = $course['cb_title'];
            
            $triggered_activity                 = $mstatus;
            log_activity($triggered_activity, $user_data, $message_template); 
        }
        $this->invalidate_course(array('course_id' => $course_id));
        $this->invalidate_course();
        if(!empty($param)){
            return $response;
        }else{
            echo json_encode($response);
        }
        
    }
    
    function restore()
    {
        $response               = array();
        $response['error']      = false;
        $course_id              = $this->input->post('course_id');
        $course                 = $this->Course_model->course(array('id' => $course_id));
        $save                   = array();
        $save['id']             = $course_id;
        $save['action_by']      = $this->auth->get_current_admin('id');
        $save['action_id']      = $this->actions['restore'];
        $save['updated_date']   = date('Y-m-d H:i:s');
        $save['cb_deleted']     = '0';
        $save['cb_status']      = '0';
        
        $response['message']    = lang('restore_course_success');
        $action_label           = $this->actions[$this->actions['restore']]['label'];
        
        //set the database value
        $action_date    = date("d M Y", strtotime($save['updated_date']));
        $action_author  = $this->auth->get_current_admin('us_name');
        $action_author  = ($action_author)?$action_author:'Admin';
        
        $button_text    = lang('activate');
        $action_class   = 'label-warning';                                                                
        $label_class    = 'spn-inactive';                                        
        $action         = lang('inactive');
            
        if(!$this->Course_model->save($save))
        {
            $response['error']   = true;
            $response['message'] = lang('restore_course_failed');
        }
        $response['actions']                   = array();
        $response['actions']['action_label']   = $action_label;
        $response['actions']['action']         = $action;
        $response['actions']['action_date']    = $action_date;
        $response['actions']['action_author']  = $action_author;
        $response['actions']['label_class']    = $label_class;
        $response['actions']['action_class']   = $action_class;
        $response['actions']['button_text']    = $button_text;
        $response['actions']['label_text']     = $action_label.' by- '.$action_author.' on '.$action_date;
        
        $cb_action = ($course['cb_status'])?'unpublish':'publish'; 
        $action_list  = '';
        $action_list .= '<li>';
        $action_list .= '     <a onclick="changeCourseStatus(\''.$course['id'].'\', \''.lang($cb_action).' '.$course['cb_title'].' '.  lang('course').'\', \''.lang('change_status_message').' '.lang($cb_action).'\')" data-target="#activate" data-toggle="modal" id="status_btn_'.$course['id'].'" href="javascript:void(0);">'.$button_text.'</a>';
        $action_list .= '</li>';
        $action_list .= '<li>';
        $action_list .= '     <a data-target="#create-catalog" data-toggle="modal" href="javascript:void(0)">Add to Catalog</a>';
        $action_list .= '</li>';
        $action_list .= '<li>';
        $action_list .= '     <a href="./course-basics.html">Settings</a>';
        $action_list .= '</li>';
        
        $response['action_list'] = $action_list;        
        echo json_encode($response);        
    }
    
    function s3objetcs()
    {
        $course_id = $this->input->post('course_id');
        $response               = array();
        $response['error']      = true;
        $response['s3_object']  = array();

        $s3_configured          = false;
        $s3_setting             = $this->settings->setting('has_s3');
        if($s3_setting['as_superadmin_value'] && $s3_setting['as_siteadmin_value'])
        {
            if(isset($s3_setting['as_setting_value']) && isset($s3_setting['as_setting_value']['setting_value']))
            {
                $s3_configured          = true;
            }
        }
        
        if(!$s3_configured)
        {
            $s3_setting   = $this->settings->setting('has_s3_ofabee');
            if($s3_setting['as_superadmin_value'] && $s3_setting['as_siteadmin_value'])
            {
                if(isset($s3_setting['as_setting_value']) && isset($s3_setting['as_setting_value']['setting_value']))
                {
                    $s3_configured          = true;
                }
            }    
        }



        if( $s3_configured )
        {
            $response['error']      = false;
            $s3_keys                = $s3_setting['as_setting_value']['setting_value'];
            define('S3_BUCKET', $s3_keys->s3_bucket);
            define('S3_KEY', 	$s3_keys->s3_access);
            define('S3_SECRET', $s3_keys->s3_secret);
            define('S3_ACL', 	'private');
            
            $now 	= time() + (12 * 60 * 60 * 1000);
            $expire     = gmdate('Y-m-d\TH:i:s\Z', $now);
            $url 	= 'https://' . S3_BUCKET . '.s3.amazonaws.com'; 
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
            $param = array();
            if($course_id)
            {
                $param = array('course_id' => $course_id);
            }
            $upload_path                                = array();
            $upload_path['supportfile_upload_path']     = supportfile_upload_path($param);
            $upload_path['video_upload_path']           = video_upload_path($param);
            $upload_path['livefiles_upload_path']       = livefiles_upload_path($param);
            $upload_path['document_upload_path']        = document_upload_path($param);
            $upload_path['redactor_upload_path']        = redactor_upload_path($param);
            $upload_path['course_upload_path']          = course_upload_path($param);
            $upload_path['scorm_upload_path']           = scorm_upload_path($param);
            $upload_path['audio_upload_path']           = audio_upload_path($param);
            $upload_path['assignment_upload_path']      = assignment_upload_path($param);
            $upload_path['course_assets_uploaded_path'] = course_assets_uploaded_path($param);
            $response['upload_path']                    = $upload_path;
        }
        echo json_encode($response);
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
    
    function language()
    {
        $response = array();
        $response['language'] = array();
        $response['language'] = get_instance()->lang->language;
        echo json_encode($response);
    }
    
    public function report($id=false)
    {
        redirect(admin_url('assessments/report'));
        $lecture          = '';
        $lecture['is_id'] = false;
        if($id)
        {
            $lecture          = $this->Course_model->lecture(array('direction'=>'DESC', 'id'=>  $id));
            $lecture['is_id'] = true;
        }
        else
        {
            $lecture          = $this->Course_model->lecture(array('direction'=>'DESC', 'status'=>  1, 'not_deleted'=>  '1', 'cl_lecture_type'=>  '3'));
            $lecture['is_id'] = false;
        }
        $lecture_type = (isset($lecture['cl_lecture_type'])&& $lecture['cl_lecture_type'] > 0)?$lecture['cl_lecture_type']:$this->__lecture_type_keys_array['video'];
        /*echo '<pre>';
        print_r($lecture); 
        print_r($lecture_type); echo '<br />'; 
        print_r($this->__report_type_array); 
        die;*/
        //echo $lecture_type;die;
        $method       = isset($this->__report_type_array[$lecture_type])?$this->__report_type_array[$lecture_type]:'empty_report';
        $this->$method($lecture);
    }
    
    private function empty_report()
    {
        $data                       = array();
        $breadcrumb                 = array();
        $breadcrumb[]               = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]               = array( 'label' => lang('courses'), 'link' => admin_url('course'), 'active' => 'active', 'icon' => '' );
        $breadcrumb[]               = array( 'label' => lang('report'), 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']         = $breadcrumb;
        $data['title']              = lang('assesment_report');
        $this->load->view($this->config->item('admin_folder').'/header.php',$data);
        $this->load->view($this->config->item('admin_folder').'/'.$this->__builder_view.'/assesment_report_empty', $data);
        $this->load->view($this->config->item('admin_folder').'/footer.php');        
    }
    private function assesment_report($lecture)
    {
        if( !$lecture )
        {
            redirect(admin_url('course'));
        }
        $course                     = $this->Course_model->course(array('id'=>$lecture['cl_course_id']));
        $breadcrumb                 = array();
        $breadcrumb[]               = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]               = array( 'label' => lang('report'), 'link' => admin_url('coursebuilder/report'), 'active' => 'active', 'icon' => '' );
        $breadcrumb[]               = array( 'label' => lang('assesment_report'), 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']         = $breadcrumb;
        $data['title']              = lang('assesment_report');
        if($lecture['is_id'])
        {
            $data['is_id'] = 1;
        }
        else
        {
            $data['is_id'] = 0;
        }
        $course_param               = $this->__role_query_filter;
        $course_param['direction']  = 'DESC'; 
        $course_param['not_deleted'] = '1';
        $course_param['status']     = '1';
        $data['courses']            = $this->Course_model->courses($course_param);
        $data['current_course']     = '';
        $data['current_course_id']  = '';
        $data['current_lecture']    = ''; 
        //$data['courses']            = $this->Course_model->get_all_courses(array('account_id' => $this->config->item('id')));
        //echo '<pre>';print_r($data['courses']);die;
        foreach ($data['courses'] as $key => $val) {
            if ($val['id'] === $lecture['cl_course_id']) {
                $data['current_course']     = $val['cb_title'];
                $data['current_course_id']  = $val['id'];
            }
        }
        $data['tests']      = $this->Course_model->get_selected_assesments($data['current_course_id']);
        foreach ($data['tests'] as $key => $val) {
            if ($val['id'] === $lecture['id']) {
                $data['current_lecture']     = $val['cl_lecture_name'];
            }
        }
        
        $data['lecture_id'] = $lecture['id'];
        $data['users']      = $this->Course_model->assessment_attempt(array('course_id' => $lecture['id']));
        foreach ($data['users'] as $key => $assessment) {
            $data['user_assessment_report'][$assessment['attempt_id']]     = $this->Course_model->assessment_report(array('assessment_id' => $assessment['attempt_id']));
            $data['users'][$key]['total'] = sizeof( $data['user_assessment_report'][$assessment['attempt_id']]);
            
            $data['users'][$key]['count_not_tried'] = 0;
            $data['users'][$key]['correct'] = 0;
            $data['users'][$key]['incorrect'] = 0;
            $data['users'][$key]['q_type'] = 0;
            foreach ($data['user_assessment_report'][$assessment['attempt_id']] as $questions) {
                if($questions['ar_answer'] == '' || empty($questions['ar_answer'])){
                    $data['users'][$key]['count_not_tried']++;
                }else{
                    if($questions['q_type'] == 1){
                        $data['users'][$key]['q_type'] = 1;
                        if($questions['q_answer'] == $questions['ar_answer']){
                            $data['users'][$key]['correct']++;
                        }else{
                            $data['users'][$key]['incorrect']++;
                        }
                    }elseif($questions['q_type'] == 2){
                        $data['users'][$key]['q_type'] = 2;
                        $user_answers = explode(',', $questions['ar_answer']);
                        $original_answers = explode(',', $questions['q_answer']);
                        sort($user_answers);
                        sort($original_answers);
                        if ($user_answers==$original_answers)
                        {
                            $data['users'][$key]['correct']++;
                        }else{
                            $data['users'][$key]['incorrect']++;
                        }
                    }elseif($questions['q_type'] == 3){
                        $data['users'][$key]['q_type'] = 3;
                    }
                }
                $success_percentage = round(($data['users'][$key]['correct'] / $data['users'][$key]['total']) * 100, 2);
                $data['users'][$key]['success_percent'] = $success_percentage;
            }
        }
        //echo "<pre>";print_r($data);die();
        $this->load->view($this->config->item('admin_folder').'/header.php',$data);
        $this->load->view($this->config->item('admin_folder').'/'.$this->__builder_view.'/assesment_report', $data);
        $this->load->view($this->config->item('admin_folder').'/footer.php');
    }
    private function descriptive_test_report($lecture)
    {
        
        if( !$lecture )
        {
            redirect(admin_url('course')); 
        }
        $course = $this->Course_model->course(array('id'=>$lecture['cl_course_id']));
        
        $breadcrumb                 = array();
        $breadcrumb[]               = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]               = array( 'label' => lang('trainings'), 'link' => admin_url('course'), 'active' => 'active', 'icon' => '' );
        $breadcrumb[]               = array( 'label' => $course['cb_title'], 'link' => admin_url('course/basic/'.$course['id']), 'active' => 'active', 'icon' => '' );
        $breadcrumb[]               = array( 'label' => lang('training_content'), 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']         = $breadcrumb;
        $data['title'] = lang('descriptive_test');
        $data['users']      = $this->Course_model->get_attended_descriptive_test($lecture['id']);
        $data['courses']    = $this->Course_model->get_all_courses(array('account_id' => $this->config->item('id')));
        //echo '<pre>';print_r($data);die();
        foreach ($data['courses'] as $key => $val) {
            if ($val['id'] === $lecture['cl_course_id']) {
                $data['current_course']     = $val['cb_title'];
                $data['current_course_id']  = $val['id'];
            }
        }
        $data['tests']      = $this->Course_model->get_selected_tests($data['current_course_id'], false);
        foreach ($data['tests'] as $key => $val) {
            if ($val['id'] === $lecture['id']) {
                $data['current_lecture']     = $val['cl_lecture_name'];
            }
        }
        $data['lecture_id'] = $lecture['id'];
        $this->load->view($this->config->item('admin_folder').'/descriptive_test_answer', $data);
    }
    function evaluate_assessment($lecture_id, $user_id, $attempt_id){
        if(!$lecture_id || !$user_id||!$attempt_id){
            redirect(admin_url('course'));
        }
        $course = $this->Course_model->course(array('id'=> $lecture_id));
        $breadcrumb                 = array();
        $breadcrumb[]               = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]               = array( 'label' => lang('trainings'), 'link' => admin_url('course'), 'active' => 'active', 'icon' => '' );
        $breadcrumb[]               = array( 'label' => $course['cb_title'], 'link' => admin_url('course/basic/'.$course['id']), 'active' => 'active', 'icon' => '' );
        $breadcrumb[]               = array( 'label' => lang('training_content'), 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']         = $breadcrumb;
        $data['title']              = lang('assesments');
        $data['lecture_id']         = $lecture_id;
        $data['user_id']            = $user_id;
        $lecture      = $this->Course_model->lecture(array('direction'=>'DESC', 'id'=>  $lecture_id));
        $data['lecture'] = $lecture;
        $course = $this->Course_model->course(array('id'=>$lecture['cl_course_id']));
        $data['course']             = $course;
        $data['attempt_id'] = $attempt_id;
        $data['prev_id']    = '';
        $data['next_id']    = '';
        $data['attempt_id_prev'] = '';
        $data['attempt_id_next'] = '';
        $data['users']      = $this->Course_model->assessment_attempt(array('course_id' => $lecture['id']));
        foreach ($data['users'] as $key => $assessment) {
            $assessment_report  = $this->Course_model->assessment_report(array('assessment_id' => $attempt_id));
            $data['user_assessment_report'][$assessment['aa_assessment_id']]     = $assessment_report;
            $data['users'][$key]['total'] = sizeof( $data['user_assessment_report'][$assessment['aa_assessment_id']]);
            
            $data['users'][$key]['count_not_tried'] = 0;
            $data['users'][$key]['correct'] = 0;
            $data['users'][$key]['incorrect'] = 0;
            $data['users'][$key]['q_type'] = 0;
            foreach ($data['user_assessment_report'][$assessment['aa_assessment_id']] as $questions) {
                if($questions['ar_answer'] == '' || empty($questions['ar_answer'])){
                    $data['users'][$key]['count_not_tried']++;
                }else{
                    if($questions['q_type'] == 1){
                        $data['users'][$key]['q_type'] = 1;
                        if($questions['q_answer'] == $questions['ar_answer']){
                            $data['users'][$key]['correct']++;
                        }else{
                            $data['users'][$key]['incorrect']++;
                        }
                    }elseif($questions['q_type'] == 2){
                        $data['users'][$key]['q_type'] = 2;
                        $user_answers = explode(',', $questions['ar_answer']);
                        $original_answers = explode(',', $questions['q_answer']);
                        sort($user_answers);
                        sort($original_answers);
                        if ($user_answers==$original_answers)
                        {
                            $data['users'][$key]['correct']++;
                        }else{
                            $data['users'][$key]['incorrect']++;
                        }
                    }elseif($questions['q_type'] == 3){
                        $data['users'][$key]['q_type'] = 3;
                    }
                }
                $success_percentage = ($data['users'][$key]['correct'] / $data['users'][$key]['total']) * 100;
                $data['users'][$key]['success_percent'] = $success_percentage;
                $data['users'][$key]['assessment']      = $assessment;
                $data['users'][$key]['aa_duration']     = $questions['aa_duration'];
                $data['users'][$key]['aa_attempted_date']     = $questions['aa_attempted_date'];
                foreach ($assessment_report as $key1 => $value) {
                    $assessment_report[$key1]['options'] = $this->Course_model->get_question_options($value['q_options']);
                    if($value['q_type'] == 1){
                        if($value['q_answer'] == $value['ar_answer']){
                            $assessment_report[$key1]['correct'] = 1;
                        }
                        else{
                            $assessment_report[$key1]['correct'] = 0;
                        }
                        $temp_q    = $value['q_answer'];
                        $temp_a    = $value['ar_answer'];
                        if($temp_q == $temp_a){
                            $assessment_report[$key1]['correct'] = 1;
                        }
                        else{
                            if($temp_a != ''){
                                $assessment_report[$key1]['correct'] = 0;
                            }
                            else{
                                $assessment_report[$key1]['correct'] = 2;
                            }
                        }
                        $temp_qstr = '';
                        $temp_astr = '';
                        $chr = 65;
                        foreach($assessment_report[$key1]['options'] as $temp_val){
                            if($temp_val['id'] == $temp_q){
                                if($temp_qstr == ''){
                                    $temp_qstr = chr($chr);
                                }
                                else{
                                    $temp_qstr = $temp_qstr.','.chr($chr);
                                }
                            }
                            if($temp_val['id'] == $temp_a){
                                if($temp_astr == ''){
                                    $temp_astr = chr($chr);
                                }
                                else{
                                    $temp_astr = $temp_astr.','.chr($chr);
                                }
                            }
                            $chr++;
                        }
                        $assessment_report[$key1]['correct_answer'] = $temp_qstr;
                        $assessment_report[$key1]['user_answer'] = $temp_astr;
                    }
                    else if($value['q_type'] == 2){
                        
                        $temp_q = explode(',',$value['q_answer']);
                        $temp_a = explode(',',$value['ar_answer']);
                        sort($temp_q);
                        sort($temp_a);
                        $arr = array_diff($temp_q, $temp_a);
                        if($temp_q == $temp_a){
                            $assessment_report[$key1]['correct'] = 1;
                        }else{
                            if(implode(',', $temp_a) == ''){
                                $assessment_report[$key1]['correct'] = 2;
                            }
                            else{
                                $assessment_report[$key1]['correct'] = 0;
                            }
                        }
                        /*if(count($arr) > 0){
                            
                            if(implode(',', $temp_a) == ''){
                                $assessment_report[$key1]['correct'] = 2;
                            }
                            else{
                                $assessment_report[$key1]['correct'] = 0;
                            }
                        }
                        else{
                            $assessment_report[$key1]['correct'] = 1;
                        }*/
                        $temp_qstr = '';
                        $temp_astr = '';
                        $chr = 65;
                        
                        foreach($assessment_report[$key1]['options'] as $temp_val){
                            foreach ($temp_q as $value) {
                                if($temp_val['id'] == $value){
                                    if($temp_qstr == ''){
                                        $temp_qstr = chr($chr);
                                    }
                                    else{
                                        $temp_qstr = $temp_qstr.','.chr($chr);
                                    }
                                }
                            }
                            foreach ($temp_a as $value) {
                                if($temp_val['id'] == $value){
                                    if($temp_astr == ''){
                                        $temp_astr = chr($chr);
                                    }
                                    else{
                                        $temp_astr = $temp_astr.','.chr($chr);
                                    }
                                }
                            }
                            $chr++;
                        }
                        
                        $assessment_report[$key1]['correct_answer'] = $temp_qstr;
                        $assessment_report[$key1]['user_answer'] = $temp_astr;
                    }
                }
                $data['users'][$key]['assessment_report'] = $assessment_report;
            }
        }
        foreach ($data['users'] as $key3 => $assessment) {
            if(($assessment['id'] == $user_id) && ($assessment['attempt_id'] == $attempt_id)){
                
                if(isset($data['users'][($key3 - 1)])){
                    $data['prev_id']    = $data['users'][($key3 - 1)]['id'];
                    $data['attempt_id_prev']    = $data['users'][($key3 - 1)]['attempt_id'];
                }
                if(isset($data['users'][($key3 + 1)])){
                    $data['next_id']    = $data['users'][($key3 + 1)]['id'];
                    $data['attempt_id_next']    = $data['users'][($key3 + 1)]['attempt_id'];
                }
                $data['user']  = $assessment;
            }
        }
        
        //echo '<pre>'; print_r($data['user']);die();
        
        //getting other users report too
        $data['colleagues'] = $this->Course_model->get_assessment_report(array("lecture_id"=>$lecture_id));
        //End
        //echo '<pre>'; print_r($data['colleagues']);die;
        $this->load->view($this->config->item('admin_folder').'/evaluate_assessment', $data);
    }
    function print_assessment($lecture_id, $user_id, $attempt_id){
        if(!$lecture_id || !$user_id || !$attempt_id){
            redirect(admin_url('course'));
        }
        $course = $this->Course_model->course(array('id'=> $lecture_id));
        $breadcrumb                 = array();
        $breadcrumb[]               = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]               = array( 'label' => lang('trainings'), 'link' => admin_url('course'), 'active' => 'active', 'icon' => '' );
        $breadcrumb[]               = array( 'label' => $course['cb_title'], 'link' => admin_url('course/basic/'.$course['id']), 'active' => 'active', 'icon' => '' );
        $breadcrumb[]               = array( 'label' => lang('training_content'), 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']         = $breadcrumb;
        $data['title']              = lang('assesments');
        $data['lecture_id']         = $lecture_id;
        $data['user_id']            = $user_id;
        $data['attempt_id']         = $attempt_id;
        $lecture      = $this->Course_model->lecture(array('direction'=>'DESC', 'id'=>  $lecture_id));
        $data['lecture'] = $lecture;
        $course = $this->Course_model->course(array('id'=>$lecture['cl_course_id']));
        $data['course']             = $course;
        
        $data['prev_id']    = '';
        $data['next_id']    = '';
        $data['users']      = $this->Course_model->assessment_attempt(array('course_id' => $lecture['id']));
        foreach ($data['users'] as $key => $assessment) {
            $assessment_report  = $this->Course_model->assessment_report(array('assessment_id' => $attempt_id));
            $data['user_assessment_report'][$assessment['aa_assessment_id']]     = $assessment_report;
            $data['users'][$key]['total'] = sizeof( $data['user_assessment_report'][$assessment['aa_assessment_id']]);
            
            $data['users'][$key]['count_not_tried'] = 0;
            $data['users'][$key]['correct'] = 0;
            $data['users'][$key]['incorrect'] = 0;
            $data['users'][$key]['q_type'] = 0;
            foreach ($data['user_assessment_report'][$assessment['aa_assessment_id']] as $questions) {
                if($questions['ar_answer'] == '' || empty($questions['ar_answer'])){
                    $data['users'][$key]['count_not_tried']++;
                }else{
                    if($questions['q_type'] == 1){
                        $data['users'][$key]['q_type'] = 1;
                        if($questions['q_answer'] == $questions['ar_answer']){
                            $data['users'][$key]['correct']++;
                        }else{
                            $data['users'][$key]['incorrect']++;
                        }
                    }elseif($questions['q_type'] == 2){
                        $data['users'][$key]['q_type'] = 2;
                        $user_answers = explode(',', $questions['ar_answer']);
                        $original_answers = explode(',', $questions['q_answer']);
                        sort($user_answers);
                        sort($original_answers);
                        if ($user_answers==$original_answers)
                        {
                            $data['users'][$key]['correct']++;
                        }else{
                            $data['users'][$key]['incorrect']++;
                        }
                    }elseif($questions['q_type'] == 3){
                        $data['users'][$key]['q_type'] = 3;
                    }
                }
                $success_percentage = ($data['users'][$key]['correct'] / $data['users'][$key]['total']) * 100;
                $data['users'][$key]['success_percent'] = $success_percentage;
                $data['users'][$key]['assessment']      = $assessment;
                $data['users'][$key]['aa_duration']     = $questions['aa_duration'];
                $data['users'][$key]['aa_attempted_date']     = $questions['aa_attempted_date'];
                foreach ($assessment_report as $key1 => $value) {
                    $assessment_report[$key1]['options'] = $this->Course_model->get_question_options($value['q_options']);
                    if($value['q_type'] == 1){
                        if($value['q_answer'] == $value['ar_answer']){
                            $assessment_report[$key1]['correct'] = 1;
                        }
                        else{
                            $assessment_report[$key1]['correct'] = 0;
                        }
                        $temp_q    = $value['q_answer'];
                        $temp_a    = $value['ar_answer'];
                        if($temp_q == $temp_a){
                            $assessment_report[$key1]['correct'] = 1;
                        }
                        else{
                            if($temp_a != ''){
                                $assessment_report[$key1]['correct'] = 0;
                            }
                            else{
                                $assessment_report[$key1]['correct'] = 2;
                            }
                        }
                        $temp_qstr = '';
                        $temp_astr = '';
                        $chr = 65;
                        foreach($assessment_report[$key1]['options'] as $temp_val){
                            if($temp_val['id'] == $temp_q){
                                if($temp_qstr == ''){
                                    $temp_qstr = chr($chr);
                                }
                                else{
                                    $temp_qstr = $temp_qstr.','.chr($chr);
                                }
                            }
                            if($temp_val['id'] == $temp_a){
                                if($temp_astr == ''){
                                    $temp_astr = chr($chr);
                                }
                                else{
                                    $temp_astr = $temp_astr.','.chr($chr);
                                }
                            }
                            $chr++;
                        }
                        $assessment_report[$key1]['correct_answer'] = $temp_qstr;
                        $assessment_report[$key1]['user_answer'] = $temp_astr;
                    }
                    else if($value['q_type'] == 2){
                        
                        $temp_q = explode(',',$value['q_answer']);
                        $temp_a = explode(',',$value['ar_answer']);
                        sort($temp_q);
                        sort($temp_a);
                        $arr = array_diff($temp_q, $temp_a);
                        if($temp_q == $temp_a){
                            $assessment_report[$key1]['correct'] = 1;
                        }else{
                            if(implode(',', $temp_a) == ''){
                                $assessment_report[$key1]['correct'] = 2;
                            }
                            else{
                                $assessment_report[$key1]['correct'] = 0;
                            }
                        }
                        /*if(count($arr) > 0){
                            
                            if(implode(',', $temp_a) == ''){
                                $assessment_report[$key1]['correct'] = 2;
                            }
                            else{
                                $assessment_report[$key1]['correct'] = 0;
                            }
                        }
                        else{
                            $assessment_report[$key1]['correct'] = 1;
                        }*/
                        $temp_qstr = '';
                        $temp_astr = '';
                        $chr = 65;
                        
                        foreach($assessment_report[$key1]['options'] as $temp_val){
                            foreach ($temp_q as $value) {
                                if($temp_val['id'] == $value){
                                    if($temp_qstr == ''){
                                        $temp_qstr = chr($chr);
                                    }
                                    else{
                                        $temp_qstr = $temp_qstr.','.chr($chr);
                                    }
                                }
                            }
                            foreach ($temp_a as $value) {
                                if($temp_val['id'] == $value){
                                    if($temp_astr == ''){
                                        $temp_astr = chr($chr);
                                    }
                                    else{
                                        $temp_astr = $temp_astr.','.chr($chr);
                                    }
                                }
                            }
                            $chr++;
                        }
                        
                        $assessment_report[$key1]['correct_answer'] = $temp_qstr;
                        $assessment_report[$key1]['user_answer'] = $temp_astr;
                    }
                }
                $data['users'][$key]['assessment_report'] = $assessment_report;
            }
        }
        foreach ($data['users'] as $key => $assessment) {
            if($assessment['id'] == $user_id){
                if(isset($data['users'][($key - 1)])){
                    $data['prev_id']    = $data['users'][($key - 1)]['id'];
                }
                if(isset($data['users'][($key + 1)])){
                    $data['next_id']    = $data['users'][($key + 1)]['id'];
                }
                $data['user']  = $assessment;
            }
        }
        
        //echo '<pre>'; print_r($data['user']);die();
        $this->load->view($this->config->item('admin_folder').'/print_assessment', $data);
    }
    function save_assessment_explanatory(){
        $ar_data      = $this->input->post('ar_data');
        //$ar_mark    = $this->input->post('ar_mark');
        foreach ($ar_data as $key => $value) {
            $data = $this->Course_model->save_assessment_explanatory($key, $value);
        }
        $assessment     = $this->User_model->assessment_from_attempt(array('attempt_id'=>$data['ar_attempt_id']));
        /*End by Alex*/
        $scored_mark = $this->Course_model->get_attempt_mark(array('attempt_id' => $data['ar_attempt_id']));
        //echo '<pre>';print_r($scored_mark);die;
        $this->Course_model->update_attempt_mark(array('attempt_id'=>$data['ar_attempt_id'],'mark'=>$scored_mark['mark']));
        $this->Course_model->changeValuationflag(array('attempt_id'=>$data['ar_attempt_id'],'flag'=>'set')); //set 1 for valuated else 0
        /*End by Alex*/
        
        $this->update_percentage(array('user_id'=>$assessment['aa_user_id'],'lecture_id'=>$assessment['a_lecture_id']));
        echo json_encode($data);
    }
    private function update_percentage($param = array()){
        $user_id            = isset($param['user_id'])?$param['user_id']:0;
        $lecture_id         = isset($param['lecture_id'])?$param['lecture_id']:0;
        $lecture_details    = $this->Course_model->lecture_details(array('lecture_id'=>$lecture_id));
        $assessment         = array();
        $descriptive        = array();
        $log_history        = $this->User_model->get_lecture_log(array('lecture_id'=>$lecture_id,'user_id'=>$user_id));
        $save               = array();
        switch($lecture_details['cl_lecture_type']){
            case 3:
                $assessment['basic'] = $this->User_model->assessment_details(array('lecture_id'=>$lecture_id,'user_id'=>$user_id));
                $assessment['attempt'] = $this->User_model->assessment_attempt_details(array('lecture_id'=>$lecture_id,'user_id'=>$user_id));
                $assessment['attempt']['scored_mark'] = $assessment['attempt']['scored_mark']>=0?$assessment['attempt']['scored_mark']:0;
                $percentage = ($assessment['attempt']['scored_mark']/$assessment['basic']['total_mark'])*100;
                $percentage = round($percentage,2);
                if(isset($log_history['id'])){
                    $save['ll_marks'] = $percentage;
                    $this->User_model->add_lecture_log(array('id'=>$log_history['id'],'values'=>$save));
                }
            break;
            case 8:
                $descriptive = $this->User_model->descriptive_details(array('lecture_id'=>$lecture_id,'user_id'=>$user_id));
                $descriptive['scored_mark'] = $descriptive['scored_mark']>=0?$descriptive['scored_mark']:0;
                $percentage = ($descriptive['scored_mark']/$descriptive['dt_total_mark'])*100;
                $percentage = round($percentage,2);
                if(isset($log_history['id'])){
                    $save['ll_marks'] = $percentage;
                    $this->User_model->add_lecture_log(array('id'=>$log_history['id'],'values'=>$save));
                }
            break;
        }
    }
    function evaluate_descriptive($lecture_id, $user_id, $attempt_id){
        if(!$lecture_id || !$user_id || !$attempt_id){
            redirect(admin_url('course'));
        }
        $lecture      = $this->Course_model->lecture(array('direction'=>'DESC', 'id'=>  $lecture_id));
        $data['lecture'] = $lecture;
        $course = $this->Course_model->course(array('id'=>$lecture['cl_course_id']));
        $data['course']             = $course;
        $breadcrumb                 = array();
        $breadcrumb[]               = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]               = array( 'label' => lang('trainings'), 'link' => admin_url('course'), 'active' => 'active', 'icon' => '' );
        $breadcrumb[]               = array( 'label' => $course['cb_title'], 'link' => admin_url('course/basic/'.$course['id']), 'active' => 'active', 'icon' => '' );
        $breadcrumb[]               = array( 'label' => lang('training_content'), 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']         = $breadcrumb;
        $data['attempt_id'] = $attempt_id;
        $data['title']      = lang('descriptive_test');
        $data['lecture_id'] = $lecture_id;
        $data['user_id']    = $user_id;
        $data['user']       = $this->Course_model->get_user_data($user_id);
        $data['answer_details'] = $this->Course_model->get_answer_details(
            $lecture_id, $user_id);
        $data['comments']   = $this->Course_model->get_comments(array('attempt_id' => $attempt_id, 'user_id' => $user_id));
        $data['question']   = $this->Course_model->get_descriptive_test_item($lecture_id);
        $data['users']      = $this->Course_model->get_attended_descriptive_test($lecture_id);
        $data['admin_name'] = $this->auth->get_current_admin('us_name');
        $admin = $this->auth->get_current_user_session('admin');
        $data['admin_image'] = $admin['us_image'];
        $data['prev_id']    = '';
        $data['next_id']    = '';
        //echo "<pre>";print_r($data['users'] );die;
        foreach($data['users'] as $key => $user){
            if($user['dtua_user_id'] == $user_id){
                if(isset($data['users'][($key - 1)])){
                    $data['prev_id']            = $data['users'][($key - 1)]['dtua_user_id'];
                    $data['prev_attempt_id']    = $data['users'][($key - 1)]['attempted_id'];
                }
                if(isset($data['users'][($key + 1)])){
                    $data['next_id']            = $data['users'][($key + 1)]['dtua_user_id'];
                    $data['next_attempt_id']    = $data['users'][($key + 1)]['attempted_id'];
                }
            }
        }
        $this->load->view($this->config->item('admin_folder').'/evaluate_descriptive', $data);
    }
    function savecomment(){
        
        $cmnt       = $this->input->post('cmnt');
        $attempt_id = $this->input->post('attempt_id');
        $lecture_id = $this->input->post('lecture_id');
        $user_id    = $this->input->post('user_id');
        $admin_id   = $this->auth->get_current_admin('id');
               
        $lecture_details = $this->Course_model->lecture(array('id'=>$lecture_id));
        $course_details  = $this->Course_model->course(array('id'=>$lecture_details['cl_course_id']));   
        $user_details    = $this->auth->get_current_user_session('admin');
        
        date_default_timezone_set("Asia/Kolkata"); 
        $save                  = array();
        $save['da_attempt_id'] = $attempt_id;
        $save['da_user_id']    = $admin_id;
        $save['status']        = 1;
        $save['updated_date']  = date('Y-m-d H:i:s');
        $save['action_by']     = $admin_id;
        $save['action_id']     = 2;
        $save['comment']       = $cmnt;
        
        $response   = array();
        $response['user_img'] = $user_details['us_image'];
        $response['comment_id'] = $this->Course_model->savecomment($save);
        echo json_encode($response);
        
        $param['from']         = config_item('site_name').'<'.$this->config->item('site_email').'>';;
        $param['to']           = array($user_details['us_email']);
        $param['subject']      = "Assignment - response received";
        $param['body']         = "Hi ".$user_details['us_name'].", <br/>Response from <b><i>".$this->config->item('us_name')."</i></b> for <b>".$course_details['cb_title']." - ".$lecture_details['cl_lecture_name']."</b> has been received.<br/> Message : ".$cmnt;
        
        $send = $this->ofabeemailer->send_mail($param);
    }
    
    public function delete_comment()
    {
        $comment_id = $this->input->post('comment_id');
        $assignment = $this->input->post('file');
        $this->Course_model->delete_comment(array('comment_id' => $comment_id));
        if($assignment != ""){
            unlink(assignment_upload_path().$assignment);
        }
    }
    function savemark(){
        $mark        = $this->input->post('mark');
        $lecture_id  = $this->input->post('lecture_id');
        $user_id     = $this->input->post('user_id');
        $user_details= $this->Course_model->get_user_data($user_id);
        $lecture_details = $this->Course_model->lecture(array('id'=>$lecture_id));
        $course_details  = $this->Course_model->course(array('id'=>$lecture_details['cl_course_id']));
        $where       = array();
        $where['dtua_lecture_id'] = $lecture_id;
        $where['dtua_user_id']    = $user_id;
        $save       = array();
        $save['mark'] = $mark;
        $save['updated_date'] = Date('Y-m-d H:i:s');
        $this->Course_model->savemark($save, $where);
        $this->update_percentage(array('user_id'=>$user_id,'lecture_id'=>$lecture_id));
        $param                 = array();
        $param['from']         = config_item('site_name').'<'.$this->config->item('site_email').'>';;
        $param['to']           = array($user_details['us_email']);
        $param['subject']      = "Assignment evaluation completed";
        $param['body']         = "Hi ".$user_details['us_name'].", <br/>Your assignment for <b>".$lecture_details['cl_lecture_name']."</b> under the course <b>".$course_details['cb_title']."</b> has been evaluated. Please login to your account to see the details.";
        $send = $this->ofabeemailer->send_mail($param);
        echo json_encode(array('status'=>'success'));
    }
    function download_descriptive_test($id){
        $result = $this->Course_model->get_descriptive_test_item($id);
        $path   = descriptive_question_path().$result['dt_file'];
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename=test.pdf');
        header('Pragma: no-cache');
        readfile($path);
    }
    function select_descriptive_test(){
        $lecture_id = $this->input->post('lecture_id');
        $users      = $this->Course_model->get_attended_descriptive_test($lecture_id);
        $str        = '';
        foreach ($users as $key => $user) {
            $dt = new DateTime($user['updated_date']);
            $str    =   $str .'<tr class="rTableRow">';
            $str    =   $str . '<td class="rTableCell">';
            $str    =   $str .     '<a href="./userprofile.html">';
            $str    =   $str .          '<span class="icon-wrap-round img">';
            $str    =   $str .               '<img src="';
            $str    =   $str . (($user['us_image'] == 'default.jpg')?default_user_path():  user_path()).$user['us_image'];
            $str    =   $str . '" />';
            $str    =   $str . '</span>';
            $str    =   $str . "<span class='line-h36'>".$user['us_name']."</span></a>";
            $str    =   $str . "</td>";
            $str    =   $str . "<td class='rTableCell'>";
            $str    =   $str . strtoupper($dt->format('M d Y')).'</td>';
            $str    =   $str . '<td class="rTableCell font-green">';
            if($user['mark'] == -1){
                $str    =   $str . 'Not evaluated yet';
            }
            else{
                $str    =   $str . $user['mark'].' Marks';
            }         
            $str    =   $str . '</td>';
            $str    =   $str . '<td class="rTableCell">';
            $str    =   $str . '<a href="'.admin_url().'coursebuilder/evaluate_descriptive/'.$user['dtua_lecture_id'].'/'.$user['dtua_user_id'].'/'.$user['attempted_id'].'" class="btn btn-green" > EVALUATE</a></td>';
            $str    =   $str . '</tr>';
                        
        }
        echo $str;
    }
    
    function select_assessment_test(){
        $lecture_id = $this->input->post('lecture_id');
        $data['users']      = $this->Course_model->assessment_attempt(array('course_id' => $lecture_id));
        foreach ($data['users'] as $key => $assessment) {
            $data['user_assessment_report'][$assessment['aa_assessment_id']]     = $this->Course_model->assessment_report(array('assessment_id' => $assessment['attempt_id']));
            $data['users'][$key]['total'] = sizeof( $data['user_assessment_report'][$assessment['aa_assessment_id']]);
            
            $data['users'][$key]['count_not_tried'] = 0;
            $data['users'][$key]['correct'] = 0;
            $data['users'][$key]['incorrect'] = 0;
            $data['users'][$key]['q_type'] = 0;
            foreach ($data['user_assessment_report'][$assessment['aa_assessment_id']] as $questions) {
                if($questions['ar_answer'] == '' || empty($questions['ar_answer'])){
                    $data['users'][$key]['count_not_tried']++;
                }else{
                    if($questions['q_type'] == 1){
                        $data['users'][$key]['q_type'] = 1;
                        if($questions['q_answer'] == $questions['ar_answer']){
                            $data['users'][$key]['correct']++;
                        }else{
                            $data['users'][$key]['incorrect']++;
                        }
                    }elseif($questions['q_type'] == 2){
                        $data['users'][$key]['q_type'] = 2;
                        $user_answers = explode(',', $questions['ar_answer']);
                        $original_answers = explode(',', $questions['q_answer']);
                        sort($user_answers);
                        sort($original_answers);
                        if ($user_answers==$original_answers)
                        {
                            $data['users'][$key]['correct']++;
                        }else{
                            $data['users'][$key]['incorrect']++;
                        }
                    }elseif($questions['q_type'] == 3){
                        $data['users'][$key]['q_type'] = 3;
                    }
                }
                $success_percentage = ($data['users'][$key]['correct'] / $data['users'][$key]['total']) * 100;
                $data['users'][$key]['success_percent'] = $success_percentage;
            }
        }
        $str        = '';
        foreach ($data['users'] as $key => $user) {
            $str        = $str . '<tr class="rTableRow">';
            $str        = $str . '  <td class="rTableCell">';
            $str        = $str . '      <a href="'.admin_url().'coursebuilder/evaluate_assessment/'.$lecture_id.'/'.$user['id'].'/'.$user['attempt_id'].'">';
            $str        = $str . '<span class="icon-wrap-round img">';
            $str        = $str . '<img src="'. (($user['us_image'] == 'default.jpg')?default_user_path():  user_path()).$user['us_image'].'">';
            $str        = $str . '</span>';
            $str        = $str . '<span class="line-h36">'.$user['us_name'].'</span></a></td>';
            $str        = $str . '<td class="rTableCell">';
            $dt         = new DateTime($user['aa_attempted_date']);
            $str        = $str . strtoupper($dt->format('M d Y')). '</td>';
            
            if($user['q_type'] == 3){
                $str    = $str . '<td class="rTableCell">';
                $str    = $str . 'Explanatory question found, Need Manual evaluation</td>';
                $str    = $str . '<td class="rTableCell"></td>';
                $str    = $str . '<td class="rTableCell"></td>';
                $str    = $str . '<td class="rTableCell"></td>';
                $str    = $str . '<td class="rTableCell"><a href="'.admin_url().'coursebuilder/evaluate_assessment/'.$lecture_id.'/'.$user['id'].'/'.$user['attempt_id'];
                $str    = $str . '" class="btn btn-green" > EVALUATE</a></td>';
            }
            else{
                $str    = $str . '<td class="rTableCell font-green">'.$user['correct'].'  Correct</td>';
                $str    = $str . '<td class="rTableCell font-red">'.$user['incorrect'].' Wrong</td>';
                $str    = $str . '<td class="rTableCell font-lgt-grey">'.$user['count_not_tried'].' Not Tried</td>';
                $str    = $str . '<td class="rTableCell font-green">'.$user['success_percent'].' % success</td>';
                $str    = $str . '<td class="rTableCell"> '.$user['a_duration'].' min</td>';
                
            }
            $str    = $str . '</tr>';
        }
        echo $str;
    }
    function select_assessment_test_course(){
        $course_id = $this->input->post('course_id');
        $tests     = $this->Course_model->get_selected_assesments($course_id);
        $str       = '';
        foreach($tests as $test){ 
        $str      = $str . '<li id="test_old">';
        $str      = $str . '<a href="#" onclick="select_test('.$test['id'].', this)" >'.$test['cl_lecture_name'].'</a></li>';
         
        }
        $data     = array();
        $data['str'] = $str;
        $data['current_id'] = '';
        $data['current_name']='';
        if(count($tests) > 0){
            $data['current_id']     = $tests[0]['id'];
            $data['current_name']   = $tests[0]['cl_lecture_name'];
        }
        echo json_encode($data);
    }
    function select_descriptive_test_course(){
        $course_id = $this->input->post('course_id');
        $tests     = $this->Course_model->get_selected_tests($course_id);
        $str       = '';
        foreach($tests as $test){ 
        $str      = $str . '<li id="test_old">';
        $str      = $str . '<a href="#" onclick="select_test('.$test['id'].', this)" >'.$test['cl_lecture_name'].'</a></li>';
         
        }
        $data     = array();
        $data['str'] = $str;
        $data['current_id'] = '';
        $data['current_name']='';
        if(count($tests) > 0){
            $data['current_id']     = $tests[0]['id'];
            $data['current_name']   = $tests[0]['cl_lecture_name'];
        }
        echo json_encode($data);
    }
    
    function export_assessment_report($lectureid){
        $cnt                = 1;
        $updated_date       = date('Y-m-d-H-i-s');
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment;Filename=Descriptive_test_".$updated_date.".xls");
        $data['users']      = $this->Course_model->assessment_attempt(array('course_id' => $lectureid));
        foreach ($data['users'] as $key => $assessment) {
            $data['user_assessment_report'][$assessment['aa_assessment_id']]     = $this->Course_model->assessment_report(array('assessment_id' => $assessment['attempt_id']));
            $data['users'][$key]['total'] = sizeof( $data['user_assessment_report'][$assessment['aa_assessment_id']]);
            
            $data['users'][$key]['count_not_tried'] = 0;
            $data['users'][$key]['correct'] = 0;
            $data['users'][$key]['incorrect'] = 0;
            $data['users'][$key]['q_type'] = 0;
            foreach ($data['user_assessment_report'][$assessment['aa_assessment_id']] as $questions) {
                if($questions['ar_answer'] == '' || empty($questions['ar_answer'])){
                    $data['users'][$key]['count_not_tried']++;
                }else{
                    if($questions['q_type'] == 1){
                        $data['users'][$key]['q_type'] = 1;
                        if($questions['q_answer'] == $questions['ar_answer']){
                            $data['users'][$key]['correct']++;
                        }else{
                            $data['users'][$key]['incorrect']++;
                        }
                    }elseif($questions['q_type'] == 2){
                        $data['users'][$key]['q_type'] = 2;
                        $user_answers = explode(',', $questions['ar_answer']);
                        $original_answers = explode(',', $questions['q_answer']);
                        sort($user_answers);
                        sort($original_answers);
                        if ($user_answers==$original_answers)
                        {
                            $data['users'][$key]['correct']++;
                        }else{
                            $data['users'][$key]['incorrect']++;
                        }
                    }elseif($questions['q_type'] == 3){
                        $data['users'][$key]['q_type'] = 3;
                    }
                }
                $success_percentage = ($data['users'][$key]['correct'] / $data['users'][$key]['total']) * 100;
                $data['users'][$key]['success_percent'] = $success_percentage;
            }
        }
    
        $userHTML = "";
            
            $userHTML       .= '<h2><center>Assessment Report</center></h2>';
            $userHTML       .= '   <table class="table table-bordered " border="1" >';
            $userHTML       .= '   <thead>';
            $userHTML       .= '   <tr>';
            $userHTML       .= '   <th><h3>Sl.No</h3></th>';
            $userHTML       .= '   <th><h3>Name</h3></th>';
            $userHTML       .= '   <th><h3>Date</h3></th>';
            $userHTML       .= '   <th><h3>Correct</h3></th>';
            $userHTML       .= '   <th><h3>Wrong</h3></th>';
            $userHTML       .= '   <th><h3>Not tried</h3></th>';
            $userHTML       .= '   <th><h3>Success Percentage</h3></th>';
            $userHTML       .= '   <th><h3>Duration</h3></th>';
            $userHTML       .= '   </tr>';
            $userHTML       .= '    </thead>';
            $userHTML       .= '    <tbody>  ';
            foreach ($data['users'] as $key => $user) {
                $userHTML   .= '<tr>';
                $userHTML   .= '    <td>';
                $userHTML   .= '        '.$cnt;
                $userHTML   .= '    </td>';
                $userHTML   .= '    <td>';
                $userHTML   .= '        '.$user['us_name'];
                $userHTML   .= '    </td>';
                $userHTML   .= '    <td>';
                $dt          = new DateTime($user['aa_attempted_date']);
                $userHTML   .= '        '.$dt->format('M d Y');
                $userHTML   .= '    </td>';
                $userHTML   .= '    <td>';
                $userHTML   .= '        '.$user['correct'];
                $userHTML   .= '    </td>';
                $userHTML   .= '    <td>';
                $userHTML   .= '        '.$user['incorrect'];
                $userHTML   .= '    </td>';
                $userHTML   .= '    <td>';
                $userHTML   .= '        '.$user['count_not_tried'];
                $userHTML   .= '    </td>';
                $userHTML   .= '    <td>';
                if(isset($user['success_percent'])){
                    $userHTML   .= '        '.$user['success_percent'];
                }
                else{
                    $userHTML   .= '        '.'0';
                }
                $userHTML   .= '    </td>';
                $userHTML   .= '    <td>';
                $userHTML   .= '        '.$user['a_duration'];
                $userHTML   .= '    </td>';
                $userHTML   .= '</tr>';
                $cnt++;
            }
            $userHTML       .= '    </tbody>'; 
            $userHTML       .= '  </table>';
            
            
        echo $userHTML;
    }
    function export_descriptive_test($lectureid){
        $cnt                = 1;
        $updated_date       = date('Y-m-d-H-i-s');
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment;Filename=Descriptive_test_".$updated_date.".xls");
        $users      = $this->Course_model->get_attended_descriptive_test($lectureid);
        $userHTML = "";
            
            $userHTML       .= '<h2><center>User Details </center></h2>';
            $userHTML       .= '   <table class="table table-bordered " border="1" >';
            $userHTML       .= '   <thead>';
            $userHTML       .= '   <tr>';
            $userHTML       .= '   <th><h3>Sl.No</h3></th>';
            $userHTML       .= '   <th><h3>Name</h3></th>';
            $userHTML       .= '   <th><h3>Date</h3></th>';
            $userHTML       .= '   <th><h3>Marks</h3></th>';
            $userHTML       .= '   </tr>';
            $userHTML       .= '    </thead>';
            $userHTML       .= '    <tbody>  ';
            foreach ($users as $key => $user) {
                $userHTML   .= '<tr>';
                $userHTML   .= '    <td>';
                $userHTML   .= '        '.$cnt;
                $userHTML   .= '    </td>';
                $userHTML   .= '    <td>';
                $userHTML   .= '        '.$user['us_name'];
                $userHTML   .= '    </td>';
                $userHTML   .= '    <td>';
                $dt          = new DateTime($user['updated_date']);
                $userHTML   .= '        '.$dt->format('M d Y');
                $userHTML   .= '    </td>';
                $userHTML   .= '    <td>';
                $userHTML   .= '        '.$user['mark'];
                $userHTML   .= '    </td>';
                $userHTML   .= '</tr>';
                $cnt++;
            }
            $userHTML       .= '    </tbody>'; 
            $userHTML       .= '  </table>';
            
            
        echo $userHTML;
    }
    
    function configure_live()
    {
        $live_id        = $this->input->post('live_id');
        $course_id      = $this->input->post('course_id');
        $make_online    = $this->input->post('make_online');
        $response       = array();
        $response['already_live'] = 0;
        if($make_online == 1)
        {
            $response['already_live'] = sizeof($this->Course_model->get_online_live_lectures(array('live_id' => $live_id,'course_id' => $course_id)));
            if($response['already_live'] > 0 )
            {
                echo json_encode($response);                
            }
            else
            {
                $this->configure_live_confirmed();            
            }
        }
        else
        {
            $this->configure_live_confirmed();
        }
    }
    
    function configure_live_confirmed()
    {
        $live_id        = $this->input->post('live_id');
        $course_id      = $this->input->post('course_id');
        $make_online    = $this->input->post('make_online');
        if($make_online == 1 || $make_online == 2)
        {            
            $this->Course_model->save_live_lecture_bulk(array('course_id' => $course_id));//making all live lecture offline
        }
        $save                   = array();
        $save['id']             = $live_id;
        $save['ll_course_id']   = $course_id;
        //$save['ll_is_online']   = $make_online;
        $this->Course_model->save_live_lecture($save);
        echo json_encode($save);
    }
    
    function recorded_json_old($json = true)
    {
        /*$this->__coSpaceId = '9e293046-d970-49e1-8820-ea3423ffd07c';
        $this->__coSpaceId = $this->input->post('cospace_id');
        $directory  = FCPATH.'cisco/spaces/'.$this->__coSpaceId;;
        $files      = scandir($directory);*/
        /*echo phpinfo();die;
        $file= cisco_upload_path().'9e293046-d970-49e1-8820-ea3423ffd07c/20170731061326+0000.mp4';
        $time = exec("$ffmpeg -i /path/".$file." 2>&1 | 
        grep Duration | cut -d ' ' -f 4 | sed s/,//");
        echo $time;die;*/
        $spaces_path    = FCPATH.cisco_upload_path(); 
        //$spaces_path    = '/mnt/spaces/';
        $spaces         = scandir($spaces_path);
        $this->__files  = array();
        unset($spaces[0]);
        unset($spaces[1]);
        // echo '<pre>'; print_r($spaces);die;
        if(!empty($spaces))
        {
            foreach($spaces as $space)
            {
                $files  = scandir($spaces_path.$space);
                unset($files[0]);
                unset($files[1]);
                if(!empty($files))
                {
                    $this->__files[$space] = array();
                    foreach($files as $file)
                    {
                        $this->__files[$space][] = $file;
                    }
                }
            }
        }
        $response                       = array();
        $response['recorded_videos']    = array();
        if(sizeof($this->__files))
        {
            foreach($this->__files as $space_id => $files)
            {
                 if(sizeof($files))
                {
                    foreach($files as $file)
                    {
                        $file_date = substr($file, 0, 8);
                        if(!isset($response['recorded_videos'][$file_date]))
                        {
                            $response['recorded_videos'][$file_date]            = array();
                            $response['recorded_videos'][$file_date]['files']   = array();
                            $response['recorded_videos'][$file_date]['label']   = date('F j, Y', strtotime($file_date));
                        }
                        $response['recorded_videos'][$file_date]['files'][]     = array( 
                                                                                        'filename' => $file,
                                                                                        'space_id' => $space_id,
                                                                                        'full_path' => $space_id.'/'.$file
                                                                                    );
                        
                    }
                }
            }
        }
        if($json)
        {
            echo json_encode($response);        
        }
        else
        {
            return $response;
        }
    }
    
    function create_recorded_video()
    {
        die;
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
                if( $this->Course_model->section(array('filter_id'=>$save['id'], 'name'=>$save['s_name'], 'deleted'=>'0')) > 0 )
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
        $save['id']                                 = $this->input->post('lecture_id');
        $save['cl_lecture_name']                    = $this->input->post('record_name');
        $cisco_file_name                            = $this->input->post('recorded_video_id');
        $file_name                                  = explode('/', $cisco_file_name);
        $file_name                                  = $file_name[sizeof($file_name)-1];
        $file_name                                  = str_replace('+', '-', $file_name);
        $file_name                                  = date('y-m-d-h-i-s').rand(1000, 9999).$file_name;
        $save['cl_filename']                        = $cisco_file_name."?v=".rand(10,1000);
        $save['cl_lecture_description']             = $save['cl_lecture_name'];
        $save['cl_org_file_name']                   = $file_name."?v=".rand(10,1000);
        $save['cl_limited_access']                  = $this->input->post('cl_limited_access');
        $save['cl_limited_access']                  = ($save['cl_limited_access'])?$save['cl_limited_access']:'0';
        $save['cl_course_id']                       = $course_id;
        $save['cl_section_id']                      = $section_id;
        $highest_order                              = $this->Course_model->lectures(array('count'=>true, 'section_id'=>$section_id, 'course_id'=> $course_id));
        $save['cl_order_no']                        = $highest_order+1;
        $save['cl_lecture_type']                    = '11';
        $save['cl_sent_mail_on_lecture_creation']   = '0';
        $save['action_by']                          = $this->auth->get_current_admin('id');
        $save['action_id']                          = $this->actions['create'];
        $save['cl_duration']                        = 0;
        $lecture_id                                 = $this->Course_model->save_lecture($save);
        
        $response['error']   = 'false';
        $response['message'] = lang('lecture_saved_success');
        $response['id']      = $lecture_id;
        //sending request to copy video from cisco space to ours
        $video_source           = cisco_upload_path().$cisco_file_name;
        if(file_exists($video_source))
        {
            $curl_param                         = array();
            $curl_param['target']               = admin_url('coursebuilder/input_trigger_vimeo_upload');
            $curl_param['video_source']         = $video_source;
            $curl_param['video_destination']    = video_upload_path(array('course_id' => $course_id)).$file_name;
            $curl_param['file_name']            = $file_name;
            $curl_param['lecture_id']           = $lecture_id;
            $curl_param['lecture_name']         = $save['cl_lecture_name'];
            $curl_param['lecture_description']  = $save['cl_lecture_description'];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $curl_param['target']);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($curl_param));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
            curl_exec($ch);
            curl_close ($ch);
        }
        //End
        echo json_encode($response);exit;
    }
    function input_trigger_vimeo_upload()
    {
        $param = $this->input->post();
        foreach($param as $key => $value)
        {
            $$key = $value;
        }
        if(file_exists($video_source))
        {
            //create directory if not exists
            $folders            = explode('/', $video_destination);
            $folder_depth       = sizeof($folders);
            unset($folders[$folder_depth-1]);//deleting the index that contins file name
            $directory_to_create = implode('/', $folders);
            if(!is_dir($directory_to_create))
            {
                mkdir($directory_to_create, 0777, true);
            }   
            //end
            $video_copy_command     = 'cp -rf '.$video_source.' '.$video_destination;
            shell_exec($video_copy_command); 
            
            $has_s3             = $this->settings->setting('has_s3');
            if( $has_s3['as_superadmin_value'] && $has_s3['as_siteadmin_value'] )
            {
                uploadToS3($video_source, $video_destination);
                $video_delete_command     = 'rm -rf '.$video_destination;
                shell_exec($video_delete_command); 
            }   
            $save = array();
            $save['id'] = $lecture_id;
            $save['cl_conversion_status'] = '3';
            $this->Course_model->save_lecture($save);
            
            $this->initiate_vimeo(array(
                'lecture_id' => $lecture_id,
                'path' => uploads_url().$video_destination,
                'name' => $lecture_name,
                'description' => $lecture_description
            ));
    
        }
        // $save                       = array();
        // $save['id']                 = $lecture_id;
        // $lecture_id                 = $this->Course_model->save_lecture($save);
    }
    
    function cisco_recorded_videos($lecture)
    {
        $data                       = array();
        $data['title']              = $lecture['cl_lecture_name'];
        $data['lecture']            = $lecture;
        $data['lecture_icons']      = $this->__lecture_type_icons;
        // echo '<pre>';print_r($lecture);die;
        $this->load->view($this->config->item('admin_folder').'/'.$this->__builder_view.'/cisco_recorded_videos', $data);
    }
    
    function save_cisco_recorded_video_detail()
    {
        $response           = array();
        $response['error']  = 'false';
        $response['message']= '';
        
        $save               = array();
        
        if( $response['error'] == 'true' )
        {
            echo json_encode($response);exit;            
        }
        
        $save                                       = array();
        $save['id']                                 = $this->input->post('lecture_id');
        $save['cl_lecture_name']                    = $this->input->post('lecture_name' ,true);
        $save['cl_limited_access']                  = $this->input->post('cl_limited_access');
        $save['cl_filename']                        = $this->input->post('recorder_video_id');
        
        //find the duration
        $ffmpeg_path         = config_item('ffmpeg');  
        $vid                 = realpath(FCPATH.cisco_upload_path().$save['cl_filename']);
        ob_start();
        $ffmpeg_check_cmd    = $ffmpeg_path." -i \"{$vid}\" 2>&1";
        passthru($ffmpeg_check_cmd);
        $durationOut         = ob_get_contents();
        ob_end_clean();
        $search              ='/Duration: (.*?),/';
        $duration            = preg_match($search, $durationOut, $matches, PREG_OFFSET_CAPTURE, 3);
        $duration            = $matches[1][0];
        $timearray           = explode(":", $duration);
        $hr                  = 3600*$timearray[0];
        $min                 = 60*$timearray[1];
        $sec                 = $timearray[2];
        $ttime               = $hr+$min+$sec; 
        $durationArray       = explode(".",$ttime);
        //$duration            = $durationArray[0];
        $save['cl_duration'] = isset($durationArray[0])?$durationArray[0]:0;
        //End
        $save['action_by']                          = $this->auth->get_current_admin('id');
        $save['action_id']                          = $this->actions['create'];
        $lecture_id                                 = $this->Course_model->save_lecture($save);
        $response['error']   = 'false';
        $response['message'] = lang('lecture_saved_success');
        $response['id']      = $lecture_id;
                
        //Check the file conversion already happened. If not saving data in conversion queue
        $filename    = explode('/', $save['cl_filename']);
        $filename    = $filename[sizeof($filename)-1];
        $file_label  = substr($filename, 0, -4);
        $lecture               = $this->Course_model->lecture(array('id'=>$lecture_id, 'select' => 'cl_course_id'));
        if(!file_exists(FCPATH.video_upload_path(array('course_id' => $lecture['cl_course_id'])).$filename))
        {
            $queue                       = array();
            $queue['id']                 = false;
            $queue['lecture_id']         = $lecture_id;
            $queue['s3_upload']          = '0';
            $queue['from_cisco']         = '1';
            $queue['file_path']          = FCPATH.cisco_upload_path().$save['cl_filename']."?v=".rand(10,1000);
            $queue['output_path']        = FCPATH.video_upload_path(array('course_id' => $lecture['cl_course_id']));
            $queue['conversion_status']  = '1';//upload completed
            $this->Course_model->save_conversion_queue($queue);
        }
        else
        {
            $save                           = array();
            $save['id']                     = $lecture_id;
            $save['cl_filename']            = $file_label."?v=".rand(10,1000);
            $save['cl_lecture_type']        = '1';
            $save['cl_conversion_status']   = '3';
            $save['cl_org_file_name']       = $filename."?v=".rand(10,1000);
            $this->Course_model->save_lecture($save);
        }
        //End
        
        echo json_encode($response);exit;            
    }
    
    private function get_duration($file_path)
    {
        $ffmpeg_path         = config_item('ffmpeg');  
        $vid                 = realpath($file_path);
        //$vid                 = $file_path;
        ob_start();
        $ffmpeg_check_cmd    = $ffmpeg_path." -i \"{$vid}\" 2>&1";
        passthru($ffmpeg_check_cmd);
        $durationOut         = ob_get_contents();
        ob_end_clean();
        $search              ='/Duration: (.*?),/';
        $duration            = preg_match($search, $durationOut, $matches, PREG_OFFSET_CAPTURE, 3);
        $duration            = isset($matches[1][0])?$matches[1][0]:'00:00:00';
        $timearray           = explode(":", $duration);
        $hr                  = 3600*$timearray[0];
        $min                 = 60*$timearray[1];
        $sec                 = $timearray[2];
        $ttime               = $hr+$min+$sec; 
        $durationArray       = explode(".",$ttime);
        $duration            = $durationArray[0];
        return $duration;
    }
    
    
    function change_language($language_id)
    {
        $this->session->set_userdata('active_web_language', $language_id);
        echo json_encode(array('error'=>false, 'message' => 'Language switched'));
    }
    function invalidate_course($param = array()){
        //Invalidate cache
        $course_id  = isset($param['course_id'])?$param['course_id']:false;
        if($course_id){
            $this->memcache->delete('course_'.$course_id);
            $this->memcache->delete('course_mob'.$course_id);
            $this->memcache->delete('mobile_course_'.$course_id);
        }else{
            $this->memcache->delete('all_courses');
            $this->memcache->delete('sales_manager_all_sorted_courses');
            $this->memcache->delete('courses');
        }
        $this->memcache->delete('all_sorted_course');
        $this->memcache->delete('popular_courses');
        $this->memcache->delete('featured_courses');
        $this->memcache->delete('active_courses');
    }
    
    function invalidate_subscription($param = array()){
        //Invalidate cache
        $user_id    = isset($param['user_id'])?$param['user_id']:false;
        $course_id  = isset($param['course_id'])?$param['course_id']:false;
        if($user_id && $course_id){
            $this->memcache->delete('enrolled_'.$user_id);
            $this->memcache->delete('subscription_'.$course_id.'_'.$user_id);
            $this->memcache->delete('enrolled_item_ids_'.$user_id);
        }
        if($user_id){
            $this->memcache->delete('enrolled_'.$user_id);
            $this->memcache->delete('enrolled_item_ids_'.$user_id);
        }
    }
    public function save_certificate(){
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
                if( $this->Course_model->section(array('filter_id'=>$save['id'], 'name'=>$save['s_name'], 'deleted'=>'0')) > 0 )
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
            $save['action_by']  = $this->__loggedInUser['id'];
            $section_id         = $this->Course_model->save_section($save);
        }
        
        $save                                       = array();
        $save['id']                                 = $this->input->post('lecture_id');
        $save['cl_lecture_name']                    = $this->input->post('certificate_title');
        $save['cl_lecture_description']             = $this->input->post('certificate_description');
        $save['cl_filename']                        = $this->input->post('certificate_id');
        $org_filename                               = $this->input->post('certificate_file');
        $save['cl_org_file_name']                   = $org_filename."?v=".rand(10,1000);
        $save['cl_course_id']                       = $course_id;
        $save['cl_section_id']                      = $section_id;
        $highest_order                              = $this->Course_model->lectures(array('count'=>true, 'section_id'=>$section_id, 'course_id'=> $course_id));
        $save['cl_order_no']                        = $highest_order+1;
        $save['cl_lecture_type']                    = $this->__lecture_type_keys_array['certificate'];
        $save['action_by']                          = $this->__loggedInUser['id'];
        //echo '<pre>'; print_r($save);die;
        $save['cl_sent_mail_on_lecture_creation']   = $this->input->post('sent_mail_on_certificate_creation');
        $lecture_id                                 = $this->Course_model->save_lecture($save);
        if(!empty($lecture_id)){
            
            $user_data              = array();
            $user_data['user_id']   = $this->__loggedInUser['id'];
            $user_data['username']  = $this->__loggedInUser['us_name'];
            $user_data['useremail']  = $this->__loggedInUser['us_email'];
            $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
            
            $message_template                     = array();
            $message_template['username']         = $this->__loggedInUser['us_name'];;
            $message_template['lecture_name']     =  $save['cl_lecture_name'];
            $message_template['course_name']      =  $this->input->post('course_name');
            $triggered_activity     = 'lecture_created';
            log_activity($triggered_activity, $user_data, $message_template); 
            $response['error']   = false;
            $response['message'] = lang('lecture_saved_success');
            $response['id']      = $lecture_id;
        }else{
            $response['error']   = true;
            $response['message'] = 'Failed to save lecture';
            $response['id']      = $lecture_id;
        }
        echo json_encode($response);exit;  
    }
    public function save_certificate_only()
    {
        $save                                       = array();
        $save['id']                                 = $this->input->post('lecture_id');
        $save['cl_filename']                        = $this->input->post('certificate_id');
        $org_filename                               = $this->input->post('certificate_file');
        $save['cl_org_file_name']                   = $org_filename."?v=".rand(10,1000);
        $lecture_id                                 = $this->Course_model->save_lecture($save);
        $response            = array();
        $response['error']   = false;
        $response['message'] = lang('lecture_saved_success');
        echo json_encode($response);exit;  
    }
    public function certificate($lecture){
        $data                       = array();
        $data['title']              = $lecture['cl_lecture_name'];
        $data['lecture']            = $lecture;
        // echo "<pre>";print_r($lecture);exit;
        $data['lecture_icons']      = $this->__lecture_type_icons;
        $param                      = array();
        $data['certificates']       = $this->Course_model->all_certificates($param);;
        //echo '<pre>';print_r($data);die;
        $this->load->view($this->config->item('admin_folder').'/'.$this->__builder_view.'/certificate', $data);
    
    }
    public function clear_quiz_log($lecture){
        $filter_param               = array();
        $filter_param['lecture_id'] = $lecture['id'];
        $filter_param['course_id']  = $lecture['cl_course_id'];
        $filter_param['section_id'] = $lecture['cl_section_id'];
        $this->Course_model->remove_asessment_report($filter_param);
        $this->Course_model->remove_asessment_attempts($filter_param);
        // $this->Course_model->remove_assessment_questions($filter_param);
        $result = $this->Course_model->remove_assessments($filter_param);
       
        if($result){
            $response               = array();
            $response['error']      = false;
            $response['message']    = 'successfully removed';
        }else{
            $response               = array();
            $response['error']      = true;
            $response['message']    = 'Quiz details not deleted.';
        }
        
        return $response;
    }
    public function clear_survey_log($lecture){
        $filter_param               = array();
        $filter_param['lecture_id'] = $lecture['id'];
        $filter_param['course_id']  = $lecture['cl_course_id'];
        $filter_param['section_id'] = $lecture['cl_section_id'];
        $this->Course_model->delete_survey_user_response($filter_param);
        $this->Course_model->delete_survey_question($filter_param);
        $result = $this->Course_model->delete_survey($filter_param);
        if($result){
            $response               = array();
            $response['error']      = false;
            $response['message']    = 'successfully removed';
        }else{
            $response               = array();
            $response['error']      = true;
            $response['message']    = 'Survey details not deleted.';
        }
        
        return $response;
    }
    public function clear_descriptive_test_log($lecture){
        $filter_param               = array();
        $filter_param['lecture_id'] = $lecture['id'];
        $filter_param['course_id']  = $lecture['cl_course_id'];
        $filter_param['section_id'] = $lecture['cl_section_id'];
        $this->Course_model->remove_descrptive_answered($filter_param);
        $result = $this->Course_model->remove_descrptive_tests($filter_param);
       
        if($result){
            $response               = array();
            $response['error']      = false;
            $response['message']    = 'successfully removed';
        }else{
            $response               = array();
            $response['error']      = true;
            $response['message']    = 'Assignment details not deleted.';
        }
        
        return $response;
    }
    public function clear_live_log($lecture){
      
        $filter_param               = array();
        $filter_param['lecture_id'] = $lecture['id'];
        $filter_param['course_id']  = $lecture['cl_course_id'];
        $filter_param['section_id'] = $lecture['cl_section_id'];
        $result = $this->Course_model->delete_live_lecture($filter_param);
        if($result){
            $response               = array();
            $response['error']      = false;
            $response['message']    = 'successfully removed';
        }else{
            $response               = array();
            $response['error']      = true;
            $response['message']    = 'Live lecture details not deleted.';
        }
        
        return $response;
    }
    
    function save_access_restriction()
    {
        // echo '<pre>'; print_r($this->input->post());die;
        $save                       = array();
        $save['id']                 = $this->input->post('access_lecture_id');
        $save['cl_limited_access']  = $this->input->post('cl_limited_access');
        $save['cl_limited_access']  = ($save['cl_limited_access'])?$save['cl_limited_access']:0;
        if(!empty($save['id']))
        {
            $save['cl_access_restriction']  = $this->input->post('restriction');
            $available_from       = $save['cl_access_restriction']['available_from'];
            if($available_from['active'] == 1)
            {
                if($available_from['date'] != '' && $available_from['time'] != '')
                {
                    $available_from_timestamp       = $available_from['date'].' '.$available_from['time'];
                    $available_from_timestamp       = date("m-d-Y H:i:s", strtotime($available_from_timestamp));        
                    $available_from['timestamp']    = $available_from_timestamp;
                    $save['cl_access_restriction']['available_from'] = $available_from;
                }
            }
            $available_till       = $save['cl_access_restriction']['available_till'];
            if($available_till['active'] == 1)
            {
                if($available_till['date'] != '' && $available_till['time'] != '')
                {
                    $available_till_timestamp       = $available_till['date'].' '.$available_till['time'];
                    $available_till_timestamp       = date("m-d-Y H:i:s", strtotime($available_till_timestamp));        
                    $available_till['timestamp']    = $available_till_timestamp;
                    $save['cl_access_restriction']['available_till'] = $available_till;
                }
            }
            $cl_access_restriction          = $save['cl_access_restriction']['activities'];
            $save['cl_access_restriction']['activities'] = array();
            if(!empty($cl_access_restriction))
            {
                foreach($cl_access_restriction as $activities)
                {
                    $save['cl_access_restriction']['activities'][] = $activities;
                }
            }
            $save['cl_access_restriction'] = json_encode($save['cl_access_restriction']);
            $this->Course_model->save_lecture($save);
            $assessment_id = $this->input->post('assessment_id');
            if($assessment_id)
            {
                $keys = 'assesment_'.$assessment_id;
                $assesment_objects                   = array();
                $assesment_objects['key']            = 'assesment_'.$assessment_id;
                $assesment_callback                  = 'assesment_details';
                $assesment_params                    = array();
                $assesment_params['assesment_id']    = $assessment_id;
                $this->memcache->delete($keys);
                $assesment_details                   = $this->memcache->get($assesment_objects, $assesment_callback, $assesment_params);    
            }
            $redirect = $this->input->post('redirect_to');
            $lecture_details    = $this->Course_model->lecture_details(array('lecture_id'=>$save['id']));
            if(isset($lecture_details['cl_course_id'])){
                $this->invalidate_course(array('course_id' => $lecture_details['cl_course_id']));
            }
            if( !$redirect )
            {
                $redirect = admin_url('coursebuilder/lecture/'.$save['id']);
            }
            redirect($redirect);die; 
        }
        redirect(admin_url('course'));
    }
    function save_support_file()
    {
        $response                   = array();
        $response['error']          = false;
        $save                       = array();
        $course_id                  = $this->input->post('course_id');
        $save['id']                 = $this->input->post('lecture_id');
        $file_response              = $this->input->post('file_response');
        $file_response              = is_array($file_response)?(object)$file_response:json_decode($file_response);
        if(is_array($file_response->file_object))
        {
            $filename                   = $file_response->file_object['file_name'];
            $raw_filename               = $file_response->file_object['file_name'];
            $raw_name                   = $file_response->file_object['raw_name'];
        }
        else
        {
            $filename                   = $file_response->file_object->orig_name;
            $raw_filename               = $file_response->file_object->file_name;
            $raw_name                   = $file_response->file_object->raw_name;    
        }
        
        $lecture_details                          = $this->Course_model->lecture_details(array('select'=>'id,cl_support_files','lecture_id'=>$save['id']));
        $file_details                             = ($lecture_details['cl_support_files']!='')?json_decode($lecture_details['cl_support_files'], true):array();;
        $file_details                             = (array) $file_details;
        $file_details[$raw_name]['file_name']     = $filename;
        $file_details[$raw_name]['file_raw_name'] = $raw_filename;
        $save['cl_support_files']                 = json_encode($file_details);
        if($this->Course_model->save_lecture($save))
        {
            $assessment_id = $this->input->post('assessment_id');
            if($assessment_id)
            {
                $keys = 'assesment_'.$assessment_id;
                $assesment_objects                   = array();
                $assesment_objects['key']            = 'assesment_'.$assessment_id;
                $assesment_callback                  = 'assesment_details';
                $assesment_params                    = array();
                $assesment_params['assesment_id']    = $assessment_id;
                $this->memcache->delete($keys);
                $assesment_details                   = $this->memcache->get($assesment_objects, $assesment_callback, $assesment_params);    
            }
            
            $this->invalidate_course(array('course_id' => $course_id));
            $response['msg']                         = "Support file saved successfully";
        }
        else
        {
            $response['error']                       = true;
            $response['msg']                         = "Error in saving details!";
        }
        echo json_encode($response);exit;  
    }
    function remove_support_file()
    {
        $response                   = array();
        $response['error']          = false;
        $save                       = array();
        $course_id                  = $this->input->post('course_id');
        $save['id']                 = $this->input->post('lecture_id');
        $file_rawname               = $this->input->post('file');
        $lecture_details            = $this->Course_model->lecture_details(array('select'=>'id,cl_support_files','lecture_id'=>$save['id']));
        $file_details               = ($lecture_details['cl_support_files']!='')?json_decode($lecture_details['cl_support_files'], true):array();;
        unset($file_details[$file_rawname]);
        $save['cl_support_files']   = json_encode($file_details);
        if($this->Course_model->save_lecture($save))
        {
            $assessment_id = $this->input->post('assessment_id');
            if($assessment_id)
            {
                $keys = 'assesment_'.$assessment_id;
                $assesment_objects                   = array();
                $assesment_objects['key']            = 'assesment_'.$assessment_id;
                $assesment_callback                  = 'assesment_details';
                $assesment_params                    = array();
                $assesment_params['assesment_id']    = $assessment_id;
                $this->memcache->delete($keys);
                $assesment_details                   = $this->memcache->get($assesment_objects, $assesment_callback, $assesment_params);    
            }
            
            $this->invalidate_course(array('course_id' => $course_id));
            $response['msg']                         = "Support file saved successfully";
        }
        else
        {
            $response['error']                       = true;
            $response['msg']                         = "Error in saving details!";
        }
        echo json_encode($response);exit;  
    }
    
    function save_lecture_override(){
        $response                  = array();
        $response['error']         = false;
        $message                   = '';
        $end_date                  = $this->input->post('last_date'); 
        $attempts                  = $this->input->post('attempts'); 
        $grace_period              = $this->input->post('grace_period'); 
        $grace_period_type         = $this->input->post('grace_period_type'); 
        $override_batch            = $this->input->post('override_batch');
        $lecture_id                = $this->input->post('lecture_id');
        $assign_option             = $this->input->post('assign_option');
        $override_batch_str        = implode (",", $override_batch);
        if($end_date==""){
            $end_date=NULL;
            $message .= 'End date cannot be empty<br />';
            $response['error'] = true;
        }
        $this->load->model(array('Test_model','Course_model'));
        $lecture               = $this->Course_model->lecture(array('id'=>$lecture_id));
        if($end_date!=""){
            $end_date   = new DateTime($end_date);
            $end_date   = date_format ($end_date, 'Y-m-d' );
            
            
            if($response['error'] == false)
            {
                $GLOBALS['override_id']               = '';
                $override                             = array();
                $override['id']                       = $assign_option;
                $override['lo_lecture_id']            = $lecture_id;
                $override['lo_lecture_type']          = $this->__lecture_type_keys_array['descriptive_test'];
                $override['lo_end_date']              = $end_date;
            }
            $override['lo_attempts']              = $attempts;
            $override['lo_period']                = $grace_period;
            $override['lo_period_type']           = $grace_period_type;
            $override['lo_override_batches']      = $override_batch_str;
            $override['lo_course_id']             = $lecture['cl_course_id'];
            $GLOBALS['lo_override_id']            = $this->Test_model->saveAssesmentOverride($override);
        }
        $response['id']                           = $GLOBALS['lo_override_id'];
        if(isset($response['id'])){
            $response['exist']                    = $assign_option;
            $response['message']                  = 'Lecture override saved successfully';
            $override_batches                     = $this->Test_model->override_groups($override_batch_str);
            $response['groups']                   = $override_batches['groups'];
            $response['group_id']                 = $override_batch_str;
            $response['end_date']                 = date("d-m-Y", strtotime($end_date));
            $response['attempts']                 = $attempts;
            $response['period']                   = $grace_period;
            $response['period_type']              = $grace_period_type;
        } else {
            $response['message']                  = 'Error in saving details.';
            $response['error']                    = true;
        }
        $this->invalidate_course(array('course_id' => $lecture['cl_course_id']));
        echo json_encode($response);
    }
    function save_descriptive_files()
    {
        $response           = array();
        $response['error']  = 'false';
        $response['message']= '';
        
        $save               = array();
        
        if( $response['error'] == 'true' )
        {
            echo json_encode($response);exit;            
        }
        $lecture_id                                 = $this->input->post('lecture_id');
        $assignment                                 = $this->Course_model->get_desctriptive_question($lecture_id);
        $course_id                                  = $assignment['dt_course_id'];
        $save                                       = array();
        $save['id']                                 = $lecture_id;
        $save['cl_lecture_content']                 = $this->input->post('lecture_content');
        $save['action_by']                          = $this->auth->get_current_admin('id');
        $save['action_id']                          = $this->actions['create'];
        $lecture_id                                 = $this->Course_model->save_lecture($save);
        $save                                       = array();
        $save['dt_uploded_files']                   = $this->input->post('uploaded_files');
        $dt_uploded_files                           = array_filter(json_decode($save['dt_uploded_files'], true));
        $save['dt_uploded_files']                   = json_encode($dt_uploded_files);
        $save['dt_instruction']                     = $this->input->post('lecture_content');
        
     
        
        //cleaning deleted files
        $files_prevent_from_clean = array();
        $new_files                = $dt_uploded_files;
        if(!empty($new_files))
        {
            foreach($new_files as $n_file)
            {
                $files_prevent_from_clean[] = $n_file['file_name'];
            }
        }
        $old_files      = json_decode($assignment['dt_uploded_files'], true);
        if(!empty($old_files))
        {
            foreach($old_files as $o_file)
            {
                //echo $o_file['file_name'];
                if(!in_array($o_file['file_name'], $files_prevent_from_clean))
                {
                    $path  = site_url().assignment_upload_path().'course/'.$course_id.'/assignment/'.$o_file['file_name'];
                    if(file_exists($path))
                    {
                        unlink($path);
                    }
                }
            }
        }
        //End
        $this->invalidate_course(array('course_id' => $course_id));
        $this->invalidate_course();
        $this->Course_model->update_descriptive_test($save, $lecture_id);
        $response['error']                          = 'false';
        $response['message']                        = lang('lecture_saved_success');
        $response['id']                             = $lecture_id;
        echo json_encode($response);exit;
    }
    private function assignment_instruction()
    {
        return '<p>For every paragraph, think about the main idea that you want to communicate in that paragraph and write a clear topic sentence which tells the reader what you are going to talk about. A main idea is more than a piece of content that you found while you were researching, it is often a point that you want to make about the information that you are discussing. Consider how you are going to discuss that idea (what is the paragraph plan). For example, are you: listing a number of ideas, comparing and contrasting the views of different authors, describing problems and solutions, or describing causes and effects?</p>
        <p><strong>List paragraphs</strong> should include words like: similarly, additionally, next, another example, as well, furthermore, another, firstly, secondly, thirdly, finally, and so on.&nbsp;</p>
        <p><strong>Cause and effect </strong>paragraphs should include words like: consequently, as a result, therefore, outcomes included, results indicated, and so on.&nbsp;</p>
        <p><strong>Compare and contrast</strong> paragraphs should include words like: on the other hand, by contrast, similarly, in a similar way, conversely, alternatively, and so on.</p>
        <p>&nbsp;<strong>Problem solution</strong> paragraphs should include words like: outcomes included, identified problems included, other concerns were overcome by, and so on.</p>
        <p></p>';
    }
    function arrange_survey_question()
    {
        $orders = $this->input->post('survey_question_order');
        $this->Course_model->save_survey_question_order($orders);
    }
    function course_tutors()
    {
        $course_id      = $this->input->post('course_id');
        $data           = array();
        $data['tutors'] = $this->Course_model->get_course_tutors(array('course_id' => $course_id, 'role_filter' => '3'));
        echo json_encode($data);exit;
    }
    function recorded_json($json = true)
    {
        $this->load->model('Cisco_model');
        $data                   = array();
        $param                  = array();
        $data['recorded_dates'] = $this->Cisco_model->cisco_recording_dates();
        $date                   = $this->input->post('date');
        if($date)
        {
            $param['date'] = $date;
        }
        else
        {
            $param['limit']     = 5;
            $param['offset']    = 0;
        }
        $data['recordings']     = $this->Cisco_model->cisco_recordings($param);
        if($json)
        {
            echo json_encode($data);
        }
        else
        {
            return $data;
        }
    }
    function upload_to_aws_server()
    {
        $configs                = array();
        $s3_configured          = false;
        $s3_multipart_settings  = $this->settings->setting('has_s3');
        if($s3_multipart_settings['as_superadmin_value'] && $s3_multipart_settings['as_siteadmin_value'])
        {
            if(isset($s3_multipart_settings['as_setting_value']) && isset($s3_multipart_settings['as_setting_value']['setting_value']))
            {
                $s3_configured              = true;
                $configs['settings']        = $s3_multipart_settings['as_setting_value']['setting_value'];
            }
        }
        else
        {
            $s3_multipart_settings_ofabee   = $this->settings->setting('has_s3_ofabee');
            if($s3_multipart_settings_ofabee['as_superadmin_value'] && $s3_multipart_settings_ofabee['as_siteadmin_value'])
            {
                if(isset($s3_multipart_settings_ofabee['as_setting_value']) && isset($s3_multipart_settings_ofabee['as_setting_value']['setting_value']))
                {
                    $s3_configured          = true;
                    $configs['settings']    = $s3_multipart_settings_ofabee['as_setting_value']['setting_value'];
                }
            }    
        }
         //echo '<pre>'; 
         //print_r($this->input->post());
        // print_r($s3_multipart_settings_ofabee);
        // die;
        if($s3_configured)
        {
            $configs['folderpath']  = '';
            $this->load->library('multipartupload', $configs);
            echo $this->multipartupload->initialize($this->input->post());die;
        }
        else
        {
            echo json_encode(array('error' => true, 'message' => 'Seems multipart not configured.'));
        }
    }
    
    function process_conversion_queue_via_remote_server()
    {
        $queue_param                        = array();
        $queue_param['conversion_status']   = 2;
        $queue_param['count']               = true;
        $queue_size = $this->Course_model->conversion_queue($queue_param);
        $param                      = array();
        $param['limit']             = 2 - $queue_size;
        $param['conversion_status'] = '1';
        //$param['count']             = true;
        if($param['limit'] > 0 )
        {
            $conversion_queue = $this->Course_model->conversion_queue($param);
            //echo '<pre>';print_r($conversion_queue);die();        
            if(sizeof($conversion_queue))
            {
                $this->load->library('ofabeeconverter'); 
                foreach ($conversion_queue as $queue)
                {
                    $config                 = array();
                    $config['queue_id']     = $queue['id'];
                   
                    $config['input']        = $queue['file_path'];
                    $config['target_url']   = $this->__converter_url;                    
                    $config['output']       = ($queue['output_path'])?$queue['output_path']:(str_replace($_SERVER['DOCUMENT_ROOT'], '', $this->get_output_path($queue['file_path'])));
                    $config['s3_upload']    = ($queue['s3_upload'])?true:false;
                    $config['from_cisco']   = ($queue['from_cisco'])?true:false;
                    $config['lecture_id']   = $queue['lecture_id'];
                    $config['response_url'] = $this->__response_url;
                    $config['database_api'] = $this->__database_api;
                    //echo '<pre>'; print_r($config);die; 
                    $this->ofabeeconverter->initialize($config);
                }
            }
        }
        $queue_param                        = array();
        $queue_param['conversion_status']   = 7;
        $queue_param['count']               = true;
        $queue_size = $this->Course_model->conversion_queue($queue_param);
        $param                      = array();
        $param['limit']             = 2 - $queue_size;
        $param['conversion_status'] = '6';
        //$param['count']             = true;
        if($param['limit'] > 0 )
        {
            $this->load->library('ofabeeconverter'); 
            $copy_queue = $this->Course_model->conversion_queue($param);
            //echo '<pre>';print_r($copy_queue);die();        
            if(sizeof($copy_queue))
            {
                $this->load->library('ofabeeconverter'); 
                foreach ($copy_queue as $queue)
                {
                    $s_config                         = array();
                    $s_config['id']                   = $queue['id'];
                    $s_config['conversion_status']    = '7';
                    $this->Course_model->save_conversion_queue($s_config);
                    $contextOptions = array(
                     "ssl" => array(
                     "verify_peer"      => false,
                     "verify_peer_name" => false,
                     ),
                    );
                    // the copy or upload shebang
                    $filename           = $this->ofabeeconverter->file_name($queue['file_path']);
                    $destination_path   = $_SERVER['DOCUMENT_ROOT'].$queue['output_path'];                    
                    $destination_path   = str_replace("//","/", $destination_path);
                    // ini_set('display_errors', 1);
                    // ini_set('display_startup_errors', 1);
                    // error_reporting(E_ALL);
                    if(!is_dir($destination_path))
                    {
                        mkdir($destination_path, 0777, true);
                    }
                    $target_server_path = $destination_path.'/'.$filename;
                    if(copy( $queue['file_path'], $target_server_path, stream_context_create( $contextOptions ) ))
                    {
                        $config                         = array();
                        $config['id']                   = $queue['id'];
                        $config['file_path']            = $destination_path.'/'.$filename;
                        $config['conversion_status']    = 1;
                        $this->Course_model->save_conversion_queue($config);
                    }
                }
            }
        }
    }
    
    function conversion_completed()
    {
        $config     = $this->input->post();
        $input      = $config['input'];
        $output     = $config['output'];
        $filename   = $config['filename'];
        $org_file_name = $config['org_file_name'];
        
        $destination_path = $_SERVER['DOCUMENT_ROOT'].$output;
        if(!file_exists($destination_path))
        {
            mkdir($destination_path, 0777, true);
        }
        
        
        $contextOptions = array(
         "ssl" => array(
         "verify_peer"      => false,
         "verify_peer_name" => false,
         ),
        );
        // the copy or upload shebang
        $myfile = fopen( $_SERVER['DOCUMENT_ROOT']."/uploads/dummy.txt", "w");
        $txt = json_encode($destination_path.'/'.$filename);
        fwrite($myfile, $txt);
        fclose($myfile);
        if(copy( $input, $destination_path.'/'.$filename.'.zip', stream_context_create( $contextOptions ) ))
        {
            $zip  = new ZipArchive;
            if ($zip->open($destination_path.'/'.$filename.'.zip') === TRUE)
            {
                $zip->extractTo($destination_path.'/'.$filename);
                $zip->close();
            }
        }
        
        if(file_exists($destination_path.'/'.$filename.'.zip'))
        {
            unlink($destination_path.'/'.$filename.'.zip');
        }
        // if(file_exists($_SERVER['DOCUMENT_ROOT'].'/uploads_beta/'.$org_file_name))
        // {
        //     $file_move_command = "mv uploads_beta/".$org_file_name." ".$destination_path."/".$org_file_name;
        //     shell_exec($file_move_command);
        // }
    }
    
    function update_conversion_status($service='')
    {
        if($service)
        {
            switch ($service)
            {
                case "lecture":
                    $save = $this->input->post();
                    $this->Course_model->save_lecture($save);
                    break;
                case "queue":
                    $save = $this->input->post();
                    $this->Course_model->save_conversion_queue($save);
                    break;
                case "shoot_conversion_mail":
                        $config                = $this->input->post();
                        $lecture_id            = $config['lecture_id'];
                        $lecture               = $this->Course_model->lecture(array('id'=>$lecture_id));
                        $course                = $this->Course_model->course(array('id'=>$lecture['cl_course_id']));
                        $param                 = array();
                        $param['from']         = config_item('site_name').'<'.$this->config->item('site_email').'>';
                        //$param['to']           = array('neerajaroraclasses@gmail.com');
                        //$param['bcc']          = array('thanveer.a@enfintechnologies.com','alex@enfintechnologies.com');
                        $param['to']           = array($this->config->item('site_email'));
                        //$param['to']           = array('thanveer.a@enfintechnologies.com');
                        // $param['bcc']          = array('thanveer.a@enfintechnologies.com','hariharan.b@enfintechnologies.com','alex@enfintechnologies.com');
                        if($config['success']==true)
                        {
                            $param['subject']      = "File conversion completed successfully";
                            //$param['body']         = "Hi Admin, <br/>A lecture named <b>".$lecture['cl_lecture_name']."</b> under the course <b>".$course['cb_title']."</b> has been successfully converted. If you are logged in please click <a href='".admin_url('coursebuilder/lecture/'.$lecture_id)."'>here</a>";
                            $param['body']         = "Hi Admin, <br/>A lecture named <b>".$lecture['cl_lecture_name']."</b> under the course <b>".$course['cb_title']."</b> has been successfully converted.";
                        }
                        else
                        {
                            $param['subject']      = "File conversion error";
                            $param['body']         = "Hi Admin, <br/>There is an error in converting a lecture named <b>".$lecture['cl_lecture_name']."</b> under the course <b>".$course['cb_title']."</b>. Error is as follows<br />".serialize($config);//$conversion['message'];
                        }
                        $send = $this->ofabeemailer->send_mail($param); 
                    break;
            }
        }
    }

   public function youtubeTest(){
    if (preg_match('/youtube\.com\/watch\?v=([^\&\?\/]+)/', $filename, $id)) 
    {
        $values = $id[1];
    } else if (preg_match('/youtube\.com\/embed\/([^\&\?\/]+)/', $filename, $id)) {
        $values = $id[1];
    } else if (preg_match('/youtube\.com\/v\/([^\&\?\/]+)/', $filename, $id)) {
        $values = $id[1];
    } else if (preg_match('/youtu\.be\/([^\&\?\/]+)/', $filename, $id)) {
        $values = $id[1];
    }
    else if (preg_match('/youtube\.com\/verify_age\?next_url=\/watch%3Fv%3D([^\&\?\/]+)/', $filename, $id)) {
        $values = $id[1];
    }
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
        $config['width']            = 500;
        $config['height']           = 281;
        $config['orginal_name']     = true;
        $new_file                   = $this->crop_image($config, $lecture=true);//orginal name
        // var_dump($this->upload->do_upload('section_image'));die;
        // var_dump( $new_file   );die;
        $config['width']            = 300;
        $config['height']           = 169;
        $config['orginal_name']     = false;
        $new_file_medium            = $this->crop_image($config, $lecture=true);
        
        $config['width']            = 85;
        $config['height']           = 49;
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
    
    function reinitialize_video_lecture()
    {
        $lecture_id                                 = $this->input->post('lecture_id');
        $response                                   = array();
        $response['error']                          = true;
        $response['message']                        = 'Unable to Re-initialize this lecture.';
        if(isset($lecture_id))
        {
            $lecture_data                           = array();
            $lecture_data['id']                     = $lecture_id;
            $lecture_data['cl_conversion_status']   = 1;
            $lecture_conversion_status              = $this->Course_model->update_lecture_conversion_status($lecture_data);

            $queue_data                             = array();
            $queue_data['lecture_id']               = $lecture_id;
            $queue_data['conversion_status']        = 1;
            $queue_conversion_status                = $this->Course_model->update_queue_conversion_status($queue_data);

            if($lecture_conversion_status && $queue_conversion_status)
            {
                $response['error']                  = false;
                $response['message']                = 'Lecture Re-initialized Successfully!';
            }
        }
        echo json_encode($response);
    }

    /*
    Purpose      : To get uploaded vimeo video duration
    Params       : lecture_id, video_id
    Developer    : Lineesh
    */
    
    public function vimeo_duration( $params = array() )
    {
        $lecture_id                 = isset($params['lecture_id'])?$params['lecture_id']:'';
        $video_id                   = isset($params['video_id'])?$params['video_id']:'';
        $save['id']                 = $lecture_id;
        $save['cl_duration']        = 0;
        if($video_id)
        {
            $vimeo_link             = "https://vimeo.com/api/oembed.json?url=https://vimeo.com/".$video_id;
            $dur                    = file_get_contents($vimeo_link);
            $duration               = json_decode($dur, true);
            if(!empty($duration))
            {
                $vTime              = $duration['duration'];
                $video_duration     = $this->time2seconds(gmdate("H:i:s", $vTime));  
                $save['cl_duration'] =  $video_duration;
            }
        }
        $this->Course_model->save_lecture($save);
    }
}
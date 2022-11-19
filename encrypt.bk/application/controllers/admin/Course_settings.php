<?php
class Course_settings extends CI_Controller
{

    function __construct()
    {
        
        parent::__construct();
        $this->__role_query_filter = array();
        $redirect	= $this->auth->is_logged_in(false, false);
        $this->__admin_index = '';
        $this->__loggedInUser      = $this->auth->get_current_user_session('admin');
        
        if(!$redirect)
        {
            redirect('login');
        }

        $this->actions        = $this->config->item('actions');
        $this->load->model(array('Course_model','Bundle_model', 'Category_model', 'Tutor_model'));
        $this->lang->load(array('course_settings', 'course'));

        $this->load->helper('form');
        $this->load->library(array('form_validation','upload'));
        $this->privilege = array('view' => 1, 'add' => 2, 'edit' => 3, 'delete' => 4);

        //fetching user privilege
        $this->course_id        = ($this->uri->segment(4) != null) ? $this->uri->segment(4) : 0;
        $params                 = array();
        $params['role_id']      = $this->__loggedInUser['role_id'];
        $params['user_id']      = $this->__loggedInUser['id'];
        $params['course_id']    = $this->course_id;
        $params['module']       = 'announcement';

        $access_method                    = ($this->__loggedInUser['rl_full_course'] == '1')?'get_permission':'get_permission_course';
        $this->userPrivilege              = $this->accesspermission->$access_method($params);
        $this->student_enroll_privilege   = $this->accesspermission->$access_method(array('role_id' => $this->__loggedInUser['role_id'],'module' => 'student_enrollment','course_id'=>$params['course_id'],'user_id'=> $this->__loggedInUser['id']));
        $this->batch_enroll_privilege     = $this->accesspermission->$access_method(array('role_id' => $this->__loggedInUser['role_id'],'module' => 'batch_enrollment','course_id'=>$params['course_id'],'user_id'=> $this->__loggedInUser['id']));
        $this->assign_faculty_privilege   = $this->accesspermission->$access_method(array('role_id' => $this->__loggedInUser['role_id'],'module' => 'assign_faculty','course_id'=>$params['course_id'],'user_id'=> $this->__loggedInUser['id']));
        $this->course_content_privilege   = $this->accesspermission->$access_method(array('role_id' => $this->__loggedInUser['role_id'], 'module' => 'course_content','course_id'=>$params['course_id'],'user_id'=> $this->__loggedInUser['id']));
        $this->user_privilege             = $this->accesspermission->get_permission(array('role_id' => $this->__loggedInUser['role_id'],'module' => 'user'));   
        $this->course_privilege           = $this->accesspermission->get_permission(array('role_id' => $this->__loggedInUser['role_id'],'module' => 'course'));
        $this->forum_privilege            = $this->accesspermission->get_permission(array('role_id' => $this->__loggedInUser['role_id'], 'module' => 'course_forum'));
        $this->report_privilege           = $this->accesspermission->get_permission(array('role_id' => $this->__loggedInUser['role_id'], 'module' => 'report'));
        $this->backup_privilege           = $this->accesspermission->get_permission(array('role_id' => $this->__loggedInUser['role_id'], 'module' => 'backups'));
        $this->review_permission          = $this->accesspermission->get_permission(array('role_id' => $this->__loggedInUser['role_id'], 'module' => 'review' ));
    }

    function index()
    {
        redirect(admin_url('course'));
    }
    
    function upload_course_image_to_localserver($param=array())
    { 
        $course_id              = isset($param['course_id'])?$param['course_id']:'default';
        $directory              = course_upload_path(array('course_id' => $course_id));
        if(!file_exists($directory)){
            mkdir($directory, 0777, true);
        }
        
        $config                     = array();
        $config['upload_path']      =  $directory;
        $config['allowed_types']    = 'gif|jpg|png|jpeg';
        $new_name                   = $course_id.'.jpg';
        $config['file_name']        = $new_name;
        $config['overwrite']        = TRUE;

        $this->upload->initialize($config);
        $this->upload->do_upload('cb_image');
        $uploaded_data = $this->upload->data();

        $save                   = array();
        $save['id']             = $course_id;
        $save['cb_image']       = $uploaded_data['file_name']."?v=".rand(10,1000);
        $save['action_by']      = $this->auth->get_current_admin('id');
        $save['action_id']      = $this->actions['create'];
        $save['updated_date']   = date('Y-m-d H:i:s');
        $this->Course_model->save($save);
        
        $config                     = array();
        $config['uploaded_data']    = $uploaded_data;
        //echo '<pre>';print_r($uploaded_data);die;
        //convert to given size and return orginal name
        $config['course_id']        = $course_id;
        $config['width']            = 739;
        $config['height']           = 417;
        $config['orginal_name']     = true;
        $new_file                   = $this->crop_image($config);//orginal name
        
        $config['width']            = 300;
        $config['height']           = 160;
        $config['orginal_name']     = false;
        $new_file_medium            = $this->crop_image($config);
        
        $config['width']            = 85;
        $config['height']           = 85;
        $config['orginal_name']     = false;
        $new_file_small             = $this->crop_image($config);
        
        $has_s3     = $this->settings->setting('has_s3');
        if( $has_s3['as_superadmin_value'] && $has_s3['as_siteadmin_value'] )
        {
            $file_path          = course_upload_path(array('course_id' => $course_id)).$new_file;
            $file_medium_path   = course_upload_path(array('course_id' => $course_id)).$new_file_medium;
            $file_small_path    = course_upload_path(array('course_id' => $course_id)).$new_file_small;
            uploadToS3($file_path, $file_path);
            uploadToS3($file_medium_path, $file_medium_path);
            uploadToS3($file_small_path, $file_small_path);
            unlink($file_path);
            unlink($file_medium_path);
            unlink($file_small_path);
        }
        
        echo json_encode(array('course_image' => course_path(array('course_id' => $course_id)).$new_file_medium));
    }
     
    function crop_image($config)
    {
        $uploaded_data  = isset($config['uploaded_data'])?$config['uploaded_data']:array();
        $width          = isset($config['width'])?$config['width']:360;
        $height         = isset($config['height'])?$config['height']:160;
        $orginal_name   = isset($config['orginal_name'])?$config['orginal_name']:false;
        $course_id      = isset($config['course_id'])?$config['course_id']:0;
                
        $source_path    = $uploaded_data['full_path'];

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
        $directory          = course_upload_path(array('course_id' => $course_id));
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
    
    function save_s3_responseURL()
    {
        $updated_date           = date('Y-m-d H:i:s');
        $course_id              = $this->input->post('id');
        $course_img_url         = $this->input->post('filepath');
        
        $encode_url             = urldecode($course_img_url);
        $url_keys               = parse_url($encode_url); // parse the url
        $url_path               = explode("/", $url_keys['path']); // splitting the path
        $course_img_name        = end($url_path); // get the value of the last element 
        
        $save['id']             = $course_id;
        $save['cb_image']       = $course_img_name."?v=".rand(10,1000);;
        $save['action_by']      = $this->auth->get_current_admin('id');
        $save['action_id']      = $this->actions['create'];
        $save['updated_date']   = $updated_date;
        
        $this->Course_model->save($save);
        $this->session->set_flashdata('message', lang('course_image_saved')); 
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

            $now            = time() + (12 * 60 * 60 * 1000);
            $expire         = gmdate('Y-m-d\TH:i:s\Z', $now);
            $url            = 'https://' . S3_BUCKET . '.s3.amazonaws.com'; 
            $policy         = json_encode(  
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
            
            $upload_path    = course_upload_path();
            if(!file_exists($upload_path)){
                mkdir($upload_path, 0777, true);
            }
            
            $response['s3_object']['upload_path']               = $upload_path;
            $response['s3_object']['url']                       = $url;
            $response['s3_object']['access_key']                = S3_KEY;
            $response['s3_object']['acl']                       = S3_ACL;
            $response['s3_object']['success_action_status']     = '201';
            $response['s3_object']['policy']                    = $base64Policy;
            $response['s3_object']['signature']                 = $signature;
            $response['course_path']                            = course_upload_path();
            
        }
        echo json_encode($response);
    }
    
    function get_tutors(){
        $data = array();
        $data['tutors'] = $this->Tutor_model->tutors(array('direction'=>'DESC', 'status' => '1', 'role' => '3'));
        echo json_encode($data);
    }
    
    function save_tutor() {

        $course_id  = (preg_match('/^[1-9]+[0-9]*$/', $this->input->post('id')) == true)? $this->input->post('id') : false;
        $tutors     = json_decode($this->input->post('tutors'));
        if($course_id)
        {
            $course     = $this->Course_model->course(array('id' => $course_id));
            $notify_to  = array();

            $tutor_all = array();
            $tutor_names = array();
            $data = array();
            $data['tutors_all'] = $this->Tutor_model->get_tutors_assigned_course(array('course'=>$course_id));
            foreach($data['tutors_all'] as $tutor)
            {
                $tutor_all[] = $tutor['ct_tutor_id'];
            }

            $flag = 0;
            $action             = (preg_match('/^[a-zA-Z\s]*$/', $this->input->post('tutor_action')) == true)? $this->input->post('tutor_action') : false;
            if($action == 'unassigned')
            { 
                $t_array = array_diff($tutor_all,$tutors);
                foreach($t_array as $server_tutor){
                    // if(!in_array($server_tutor, $tutors)){
                        $del['ct_tutor_id']  = $server_tutor;
                        $del['ct_course_id'] = $course_id;
                        $this->Tutor_model->delete($del);    
                    // }
                    $flag++;
                }
            }
            else if($action == 'assigned')
            {
                foreach($tutors as $client_tutor){
                    if(!in_array($client_tutor, $tutor_all)){
                        $save = array();
                        $save['ct_tutor_id'] = $client_tutor;
                        $save['ct_course_id']= $course_id;
                        $this->Tutor_model->save($save);
                        $flag++;
                        $notify_to[] = $client_tutor;
                    }
                }
            }

            $this->memcache->delete('course_notification_' . $course_id);

            $user_data              = array();
            $user_data['user_id']   = $this->__loggedInUser['id'];
            $user_data['username']  = $this->__loggedInUser['us_name'];
            $user_data['useremail']  = $this->__loggedInUser['us_email'];
            $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
            
            $message_template                    = array();
            $message_template['username']        = $this->__loggedInUser['us_name'];;
            $message_template['course_name']     = $course['cb_title'];
            $message_template['action']          = $action;
            $message_template['count']           = $flag++;;

            $triggered_activity                  = 'faculty_assigned_to_course';
            log_activity($triggered_activity, $user_data, $message_template); 
            $this->invalidate_course(array('course_id' => $course_id));
            $this->invalidate_course();
            $course_id      = $course_id;
            $faculties      = $this->Course_model->get_course_tutors(array('course_id' => $course_id, 'select' => 'users.id, us_name, us_role_id, roles.rl_name'));
            echo json_encode($faculties);

            if(!empty($notify_to))
            {
                //Notification
                $this->load->library('Notifier');
                $this->notifier->push(
                    array(
                        'action_code' => 'faculty_assigned_to_course',
                        'assets' => array('course_name' => $course['cb_title'],'course_id' => $course['id']),
                        'target' => $course_id,
                        'individual' => true,
                        'push_to' => $notify_to
                    )
                );
                //End notification
            }
        }
        else
        {
            echo json_encode(array());
        }
    }

    // function save_tutor(){

    //     $tutor_all          = array();
    //     $data               = array();
    //     $course_id          = $this->input->post('id');
    //     $tutors             = json_decode($this->input->post('tutors'));  
    //     // echo "<pre>";print_r($tutors);exit;
    //     $course             = $this->Course_model->course(array('id' => $course_id));
    //     $data['tutors_all'] = $this->Tutor_model->get_tutors_assigned_course(array('course'=>$course_id));
    //     $tutor_all          = array_column($data['tutors_all'],'ct_tutor_id');
    //     $all_tutors         = array_merge($tutor_all,$tutors);
    //     $limited_tutors     = array_unique($all_tutors);
        
    //     $action             = $this->input->post('tutor_action');
    //     if($action == 'assigned')
    //     {
    //         foreach($tutors as $client_tutor){
    //             if(!in_array($client_tutor, $tutor_all)){
    //                 $save                   = array();
    //                 $save['ct_tutor_id']    = $client_tutor;
    //                 $save['ct_course_id']   = $course_id;
    //                 $tutor_data[]           = $save;
    //             }
    //         }
    //         $this->Tutor_model->save_tutors($tutor_data);
    //     }
    //     else if($action == 'unassigned'&&!empty($limited_tutors))
    //     {
    //         $delete_param               = array();
    //         $delete_param['course_id']  = $course_id;
    //         $delete_param['tutor_ids']  = $limited_tutors;
            
    //         $this->Tutor_model->delete($delete_param);
    //     }
        

    //     $user_data              = array();
    //     $user_data['user_id']   = $this->__loggedInUser['id'];
    //     $user_data['username']  = $this->__loggedInUser['us_name'];
    //     $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
        
    //     $message_template                    = array();
    //     $message_template['username']        = $this->__loggedInUser['us_name'];;
    //     $message_template['course_name']     = $course['cb_title'];
    //     $message_template['action']          = $action;
    //     $message_template['count']           = count($tutors);

    //     $triggered_activity                  = 'faculty_assigned_to_course';
    //     log_activity($triggered_activity, $user_data, $message_template); 

    //     $this->invalidate_course(array('course_id' => $course_id));
    //     $this->invalidate_course();
    //     $course_id      = $course_id;
    //     $faculties      = $this->Course_model->get_course_tutors(array('course_id' => $course_id, 'select' => 'users.id, us_name, us_role_id, roles.rl_name'));
    //     echo json_encode($faculties);
    // }
      
    function language_check()
    {
        $this->form_validation->set_message('language_check', 'Invalid Language');
        return FALSE;
    }
    
    function category_check()
    {
        $this->form_validation->set_message('category_check', 'Invalid Category');
        return FALSE;
    }
    
    function basics($id = false)
    {
        if($id)
        {
            if($this->__loggedInUser['rl_full_course'] != '1')
            {
                $param['current_logged_user']   = $this->__loggedInUser['id'];
                $access_course_list             = $this->Course_model->course_permission($param);
                $course_id_list                 = array_column($access_course_list, 'ct_course_id');
                if (!in_array($id, $course_id_list))
                {
                    $this->session->set_flashdata('message', lang('course_not_found'));
                    redirect($this->config->item('admin_folder') . '/course');
                    exit;
                }
            }
            $course_param               = $this->__role_query_filter;
            $course_param['course_id']  = $id;
            $course_param['limit']      = 1;
            $course_basic               = $this->Course_model->course_new($course_param);
            if(empty($course_basic)){
                redirect($this->config->item('admin_folder').'/course');
            }
            
            $data['id']                     = $course_basic['id'];
            $data['cb_title']               = $course_basic['cb_title'];
            $data['cb_short_description']   = $course_basic['cb_short_description'];
            $data['cb_description']         = $course_basic['cb_description'];

            $this->memcache->delete('categories');
            $objects                = array();
            $objects['key']         = 'categories';
            $callback               = 'get_categories';
            $categories             = $this->memcache->get($objects, $callback,array('inactive' => true)); 
            $data['categories']     = $categories;

            $objects                = array();
            $objects['key']         = 'languages';
            $callback               = 'course_languages';
            $languages              = $this->memcache->get($objects, $callback,array()); 
            $data['languages']      = $languages;
            
            // echo "<pre>";print_r($course_basic);die;

            $data['course_upload_path'] = course_upload_path(array('course_id' => $id));
            $data['cb_self_enroll_date']= $course_basic['cb_self_enroll_date'];
            $data['cb_image']           = $course_basic['cb_image'];
            $data['cb_access_validity'] = $course_basic['cb_access_validity'];
            $data['cb_validity']        = $course_basic['cb_validity'];
            $data['cb_validity_date']   = $course_basic['cb_validity_date'];
            $data['cb_preview']         = $course_basic['cb_preview'];
            $data['cb_preview_time']    = $course_basic['cb_preview_time'];
            $data['cb_has_certificate'] = $course_basic['cb_has_certificate'];
            $data['cb_has_self_enroll'] = $course_basic['cb_has_self_enroll'];
            $data['cb_virtual_class']   = $course_basic['cb_virtual_class'];
            $data['cb_virtual_room']    = $course_basic['cb_virtual_room'];
            $data['cb_is_free']         = $course_basic['cb_is_free'];
            $data['cb_price']           = $course_basic['cb_price'];
            $data['cb_discount']        = $course_basic['cb_discount'];
            $data['cb_tax_method']      = $course_basic['cb_tax_method'];
            $data['cb_category']        = explode(',', $course_basic['cb_category']);
            $data['cb_language']        = explode(',',$course_basic['cb_language']);
            
            $data['cb_has_lecture_image']           = $course_basic['cb_has_lecture_image'];
            $data['cb_what_u_get']                  = json_decode($course_basic['cb_what_u_get']);
            $data['cb_requirements']                = json_decode($course_basic['cb_requirements']);
            $data['cb_has_rating']                  = $course_basic['cb_has_rating'];
            $data['cb_has_show_total_enrolled']     = $course_basic['cb_has_show_total_enrolled'];
            $data['cb_total_video_hours']           = $course_basic['cb_total_video_hours'];
            $gst_setting                = $this->settings->setting('has_tax');

            if ($gst_setting['as_setting_value']['setting_value']->cgst && $gst_setting['as_setting_value']['setting_value']->cgst != '') {
                $data['cgst'] = $gst_setting['as_setting_value']['setting_value']->cgst;
            } 
            else 
            {
                $data['cgst'] = 0;  
            } 

            if ($gst_setting['as_setting_value']['setting_value']->sgst && $gst_setting['as_setting_value']['setting_value']->sgst != '') {
                $data['sgst'] = $gst_setting['as_setting_value']['setting_value']->sgst;
            } 
            else 
            {
                $data['sgst'] = 0;  
            } 
        }
        else
        {
            redirect($this->config->item('admin_folder').'/course');
        }
        
        $breadcrumb         = array();
        $breadcrumb[]       = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]       = array( 'label' => lang('course_bar_trainings'), 'link' => admin_url('course'), 'active' => 'active', 'icon' => '' );
        $breadcrumb[]       = array( 'label' => $course_basic['cb_title'], 'link' => admin_url('course/basic/' . $course_basic['id']), 'active' => 'active', 'icon' => '' );
        $breadcrumb[]       = array('label' => 'Settings', 'link' => '', 'active' => 'active', 'icon' => '');
        $data['breadcrumb'] = $breadcrumb;
        
        //created by thanveer
        $data['course'] = array('id' => $id);
        //end
     
        $this->form_validation->set_rules('cb_title', 'Course Title','required');
        $this->form_validation->set_rules('cb_description', 'Course Description','required');
        $this->form_validation->set_rules('cb_access_validity', 'Course Access Validity', 'required');
        
        $data['assign_faculty_privilege'] = $this->assign_faculty_privilege;
        //echo "<pre>";print_r($data['categories']);die;
        if ($this->form_validation->run() == FALSE)
        {
            $data['errors'] = validation_errors(); 
            $this->load->view($this->config->item('admin_folder').'/course_basics',$data);
        } 
        else
        {
            $save['id']                     = $id;
            $save['cb_title']               = str_replace(array("\"", "&quot;", "<", ">", "{", "}"), "", htmlspecialchars($this->input->post('cb_title')));
            $save['cb_title']               = ltrim($save['cb_title']," ");
            $save['cb_description']         = $this->input->post('cb_description');
            $save['cb_has_self_enroll']     = $this->input->post('cb_has_self_enroll');
            $save['cb_has_lecture_image']   = $this->input->post('cb_has_lecture_image');
            $valid_date                     = strtotime($this->input->post('cb_self_enroll_date'));
            $self_enroll_date               = empty($valid_date)?null:date('Y-m-d',$valid_date);
            $save['cb_self_enroll_date']    = ($this->input->post('cb_has_self_enroll') == '1')? $self_enroll_date :null;
            
            $save['cb_category']            = $this->input->post('cb_category');
            if(!empty($save['cb_category'])){
                $save['cb_category']        = implode(',', $save['cb_category']);
            }
            //echo '<pre>'; print_r($this->input->post('cb_category'));die;
            $save['cb_language']            = $this->input->post('cb_language');
            if(!empty($save['cb_language'])){
                $save['cb_language']            = implode(',', $save['cb_language']);
            }
            
            $save['cb_validity_date']       = NULL;
            $save['cb_access_validity']     = $this->input->post('cb_access_validity');
            $save['cb_virtual_class']       = $this->input->post('cb_virtual_class');
            $save['cb_virtual_room']        = $this->input->post('cb_virtual_room');
            $save['cb_is_free']             = $this->input->post('cb_is_free');
            $save['cb_price']               = $this->input->post('cb_price');
            $save['cb_discount']            = $this->input->post('cb_discount');
            $save['cb_tax_method']          = $this->input->post('cb_tax_method');
            
            $save['cb_what_u_get']                  = json_encode($this->input->post('cb_what_u_get'));
            $save['cb_requirements']                = json_encode($this->input->post('cb_requirements'));
            $save['cb_has_rating']                  = $this->input->post('cb_has_rating');
            $save['cb_has_show_total_enrolled']     = $this->input->post('cb_has_show_total_enrolled');
            $save['cb_total_video_hours']           = $this->input->post('cb_total_video_hours');
            $save['cb_short_description']           = $this->input->post('cb_short_description');
            
            if($save['cb_access_validity'] == 1 ){

                $save['cb_validity']        = $this->input->post('cb_validity');
                $save['cb_validity_date']   = 0;
            }
            else if( $save['cb_access_validity'] == 2){

                $save['cb_validity_date']   = date('Y-m-d',strtotime($this->input->post('cb_validity_date')));
                $save['cb_validity']        = 0;
            }else{
                $save['cb_validity_date']   = 0;
                $save['cb_validity']        = 0;
            }
            
            /*====================== file uploading ======================== */
            //As file upload error "No file selected" is number 4,
            //When checking by name or tmp_name, there might be other reasons why these fields didn't get populated, and you may miss these.
            
            if($_FILES['cb_image']['error'] !== 4)
            { 
                $allowed =  array('gif','png' ,'jpg', 'jpeg');
                $filename = $_FILES['cb_image']['name'];
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                if(in_array($ext,$allowed))
                {
                    $this->upload_course_image_to_localserver(array('course_id'=>$id));
                }
            }
       
            /*================================================================= */
            $save['cb_preview']             = $this->input->post('cb_preview');
            $preview_time_sec               = ($this->input->post('cb_preview_time') * 60);
            $save['cb_preview_time']        = ($this->input->post('cb_preview') == '1')?$preview_time_sec:'0';
            $save['cb_has_certificate']     = $this->input->post('cb_has_certificate');
            $save['action_by']              = $this->__loggedInUser['id'];
            $save['action_id']              = '1';
            $save['updated_date']           = date('Y-m-d H:i:s');
            // echo '<pre>'; print_r($save);die;
            $this->Course_model->save($save);
                
            //to change coursename included in bundle
            if($data['cb_title'] != $save['cb_title'])
            {
                $objects            = array();
                $objects['key']     = 'all_bundles';
                $callback           = 'all_bundles';
                $bundles            = $this->memcache->get($objects, $callback,array('match' => true, 'course_id' => $id));
                //echo "<pre>";print_r($bundles);die();
                foreach($bundles as $bundle_key => $bundle){
                   
                    $included_courses = isset($bundle['c_courses'])?json_decode($bundle['c_courses'],true):array();
                    
                    if(!empty($included_courses)){
                        
                        foreach($included_courses as $included_key => $included_course){
                            
                            if($id == $included_course['id']){

                                $included_courses[$included_key]['course_name'] = $save['cb_title'];
                            }
                            
                        }
                       
                    }
                    $included_courses           = json_encode($included_courses);
                    
                    $bundle_save                = array();
                    $filter_param               = array();
                    $bundle_save['c_courses']   = $included_courses;
                    $filter_param['update']     = true;
                    $filter_param['id']         = $bundle['id'];
                    $this->Bundle_model->save($bundle_save , $filter_param);
                }
            }
             
            $user_data              = array();
            $user_data['user_id']   = $this->__loggedInUser['id'];
            $user_data['username']  = $this->__loggedInUser['us_name'];
            $user_data['useremail']  = $this->__loggedInUser['us_email'];
            $user_data['user_type'] = $this->__loggedInUser['us_role_id'];
            
            $message_template                    = array();
            $message_template['username']        = $this->__loggedInUser['us_name'];;
            $message_template['course_name']     =  $save['cb_title'];

            $triggered_activity                  = 'course_updated';
            log_activity($triggered_activity, $user_data, $message_template); 
            $this->invalidate_course(array('course_id' => $id));
            $this->invalidate_course();

            $template = 'message';
            $message  = lang('course_basics_saved');
            
            $savenext = $this->input->post('savenext');
            $this->session->set_flashdata($template, $message);
            if($savenext){
                redirect($this->config->item('admin_folder').'/course_settings/advanced/'.$id);
            }else{
                redirect($this->config->item('admin_folder').'/course_settings/basics/'.$id);
            }
        }
    }
    
    function advanced($id = 0)
    {
        if(!in_array(1, $this->assign_faculty_privilege) || $this->__loggedInUser['role_id'] == '3' || $this->__loggedInUser['role_id'] == '8')
        {
            //$this->session->set_flashdata('error', 'You dont have permission to access this module');
            redirect($this->config->item('admin_folder').'/coursebuilder/home/'.$id);		
        }

        $course_param                   = $this->__role_query_filter;
        $course_param['id']             = $id;
        $course_param['not_deleted']    = true;
        $course_param['select']         = 'id, cb_title';
        $course                         = $this->Course_model->course($course_param);
        if(!$course)
        {
            redirect($this->config->item('admin_folder').'/course');
        }

        $data               = array();
        $data['permission'] = array();
        foreach($this->privilege as $access => $key)
        {
            $data['permission'][$access] = in_array($key, $this->assign_faculty_privilege)?1:0;
        }

        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->model('Role_model');
        $breadcrumb         = array();
        $breadcrumb[]       = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]       = array( 'label' => lang('course_bar_trainings'), 'link' => admin_url('course'), 'active' => 'active', 'icon' => '' );
        $breadcrumb[]       = array( 'label' => $course['cb_title'], 'link' => admin_url('course/basic/' . $course['id']), 'active' => 'active', 'icon' => '' );
        $breadcrumb[]       = array('label' => 'Assign Faculties', 'link' => '', 'active' => 'active', 'icon' => '');
        $data['breadcrumb'] = $breadcrumb;
        $data['course']     = $course;
        $data['faculty_roles'] = $this->Role_model->roles(array('select' => 'id, rl_name', 'full_course_access' => '0'));
        // echo $this->db->last_query();exit;
        $data['assign_faculty_privilege'] = $this->assign_faculty_privilege;
        //$data['faculties']  = $this->Course_model->get_course_tutors(array('course_id' => $course['id'], 'select' => 'users.id, us_name, us_role_id'));
        //echo '<pre>'; print_r($data);die;
        $this->form_validation->set_rules('submitted', 'Course Promo','required');

        if ($this->form_validation->run() == FALSE)
        {
            $data['errors'] = validation_errors(); 
            $this->load->view($this->config->item('admin_folder').'/course_advanced',$data);
        }
        else
        {

        }
        

        // //created by thanveer
        // $data['course'] = array('id' => $id);
        // //end


        // $this->form_validation->set_rules('submitted', 'Course Promo','required');

        // if ($this->form_validation->run() == FALSE)
        // {
        //     $data['errors'] = validation_errors(); 
        //     $this->load->view($this->config->item('admin_folder').'/course_advanced',$data);
        // }
        // else
        // {
        //     $save['id']                     = $id;
        //     $save['cb_promo']               = $this->input->post('cb_promo');
        //     $save['action_by']              = $this->auth->get_current_admin('id');
        //     $save['action_id']              = $this->actions['update'];
        //     $save['updated_date']           = $updated_date;
           
            
        //     $this->Course_model->save($save);
        //     $this->invalidate_course(array('course_id' => $save['id']));
        //     $this->invalidate_course();
        //     $this->session->set_flashdata('message', lang('course_advanced_saved'));
        //     $savenext = $this->input->post('savenext');
        //     if($savenext){
        //         redirect($this->config->item('admin_folder').'/course_settings/seo/'.$id);
        //     }else{
        //         redirect($this->config->item('admin_folder').'/course_settings/advanced/'.$id);
        //     }
        // }
    }
    function seo($id = false)
    {
        $updated_date       = date('Y-m-d H:i:s');
        $data               = array();
        $data['title']      = lang('course_bar_trainings');
        $data['courses']    = $this->Course_model->courses(array('direction'=>'DESC'));
        
        $this->load->helper('form');
        $this->load->library('form_validation');
        $data['id']                     = '';
        $data['cb_slug']                = $this->input->post('cb_slug');
        $data['cb_meta']                = $this->input->post('cb_meta');
        $data['cb_title']               = $this->input->post('cb_title');
        $data['cb_meta_description']    = $this->input->post('cb_meta_description');
        if($id)
        {
            $course_param               = $this->__role_query_filter;
            $course_param['id']         = $id;
            if(isset($this->__role_query_filter['editor_id']) && $this->__role_query_filter['editor_id'] > 0 )
            {
                $course_param['not_deleted'] = true;
            }
            $course_basic               = $this->Course_model->course($course_param);
            if(!$course_basic){
                redirect($this->config->item('admin_folder').'/course');
            }
            
            $data['id']                 = $course_basic['id'];
            $data['cb_slug']            = $course_basic['cb_slug'];
            
            $data['course_rating']      = $this->Course_model->get_ratting(array('course_id' => $course_basic['id'])); 
            
            $data['cb_meta']            = $course_basic['cb_meta'];
            $data['cb_title']           = $course_basic['cb_title'];
            $data['cb_image']           = $course_basic['cb_image'];
            $data['cb_price']           = $course_basic['cb_price'];
            $data['cb_discount']        = $course_basic['cb_discount'];
            $data['cb_meta_description']= $course_basic['cb_meta_description'];
            $data['cb_is_free']         = $course_basic['cb_is_free'];
            $this->memcache->delete(base64_encode($data['cb_slug'].config_item('id')), 0, true);
        }else{
            redirect($this->config->item('admin_folder').'/course');
        }

        //created by thanveer
        $data['course'] = array('id' => $id);
        //end

        $breadcrumb         = array();
        $breadcrumb[]       = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]       = array( 'label' => lang('course_bar_trainings'), 'link' => admin_url('course'), 'active' => 'active', 'icon' => '' );
        $breadcrumb[]       = array( 'label' => $course_basic['cb_title'], 'link' => admin_url('course/basic/' . $course_basic['id']), 'active' => 'active', 'icon' => '' );
        $breadcrumb[]       = array('label' => 'Advanced',  'link' => '', 'active' => 'active', 'icon' => '');

        $data['breadcrumb'] = $breadcrumb;
        $data['assign_faculty_privilege'] = $this->assign_faculty_privilege;
        $this->form_validation->set_rules('cb_slug', 'Friendly URL','required');

        if ($this->form_validation->run() == FALSE)
        {
            $data['errors'] = validation_errors(); 
            $this->load->view($this->config->item('admin_folder').'/course_seo',$data);
        }
        else
        {
            
            $this->load->helper('text');
            //first check the slug field
            $slug = $this->input->post('cb_slug');

            //if it's empty assign the name field
            if(empty($slug) || $slug=='')
            {
                $slug = $this->input->post('cb_title');
            }
            $slug	= url_title(convert_accented_characters($slug), 'dash', TRUE);
            $this->load->model('Routes_model');
            $route_id	= $course_basic['cb_route_id'];
            $slug	= $this->Routes_model->validate_slug($slug, $route_id);

            $save['id']                     = $id;
            $save['cb_slug']                = $slug;
            $save['cb_meta']                = $this->input->post('cb_meta');
            $save['cb_title']               = $this->input->post('cb_title');
            $save['cb_meta_description']    = $this->input->post('cb_meta_description');
            $save['cb_route_id']            = $route_id;
            $save['action_by']              = $this->auth->get_current_admin('id');
            $save['action_id']              = $this->actions['update'];
            $save['updated_date']           = $updated_date;
            $this->Course_model->save($save);
            $user_data              = array();
            $user_data['user_id']   = $this->__loggedInUser['id'];
            $user_data['username']  = $this->__loggedInUser['us_name'];
            $user_data['useremail']  = $this->__loggedInUser['us_email'];
            $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
            
            $message_template                    = array();
            $message_template['username']        = $this->__loggedInUser['us_name'];;
            $message_template['course_name']     =  $save['cb_title'];

            $triggered_activity                  = 'course_advanced_updated';
            log_activity($triggered_activity, $user_data, $message_template); 

            $route['id']	= $route_id;
            $route['slug']	= $slug;
            $route['route']	= 'course/view/'.$id;
            $route['r_item_type']   = 'course';
            $route['r_item_id']     = $id;
            $this->Routes_model->save($route);
            $this->invalidate_course(array('course_id' => $id));
            $this->session->set_flashdata('message', lang('course_seo_saved'));
            redirect($this->config->item('admin_folder').'/course_settings/seo/'.$id);
        }
    }


    /*
    * To retrive and update the revenue tab details in course settings 
    * created by : Neethu KP
    * created at 19/01/2017
    */
    function revenue($id = false)
    {
        $updated_date       = date('Y-m-d H:i:s');
        $data               = array();
        $data['title']      = lang('course_bar_trainings');
        $data['courses']    = $this->Course_model->courses(array('direction'=>'DESC'));
        
        $this->load->helper('form');
        $this->load->library('form_validation');
        $data['id']                     = '';
        $data['cb_revenue_share']       = $this->input->post('cb_revenue_share');
        //print_r($_POST);exit;
        if($id)
        {
            $course_param               = $this->__role_query_filter;
            $course_param['id']         = $id;
            if(isset($this->__role_query_filter['editor_id']) && $this->__role_query_filter['editor_id'] > 0 )
            {
                $course_param['not_deleted'] = true;
            }
            $course_basic               = $this->Course_model->course($course_param);
            if(!$course_basic){
                redirect($this->config->item('admin_folder').'/course');
            }
            
            $data['id']                 = $course_basic['id'];
            $data['cb_revenue_share']   = $course_basic['cb_revenue_share'];
            $data['cb_image']           = $course_basic['cb_image'];
            $data['cb_title']           = $course_basic['cb_title'];
            $data['course_rating']      = $this->Course_model->get_ratting(array('course_id' => $course_basic['id']));
            
            $data['cb_image']           = $course_basic['cb_image'];
            $data['cb_price']           = $course_basic['cb_price'];
            $data['cb_discount']        = $course_basic['cb_discount'];
            $data['cb_is_free']         = $course_basic['cb_is_free'];
        }else{
            redirect($this->config->item('admin_folder').'/course');
        }
        $breadcrumb         = array();
        $breadcrumb[]       = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]       = array( 'label' => lang('course_bar_trainings'), 'link' => admin_url('course'), 'active' => 'active', 'icon' => '' );
        $breadcrumb[]       = array( 'label' => $course_basic['cb_title'], 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb'] = $breadcrumb;
        $this->form_validation->set_rules('cb_revenue_share', 'Revenue Share','required');

        if ($this->form_validation->run() == FALSE)
        {
            $data['errors'] = validation_errors(); 
            $this->load->view($this->config->item('admin_folder').'/course_revenue_sharing',$data);
        }
        else
        {
            $save['id']                     = $id;
            $save['cb_revenue_share']       = $this->input->post('cb_revenue_share');
            $save['action_id']              = $this->actions['update'];
            $save['updated_date']           = $updated_date;
           
            
            $this->Course_model->save($save);
            $this->session->set_flashdata('message', lang('course_revenue_saved'));
            redirect($this->config->item('admin_folder').'/course_settings/revenue/'.$id);
        }
    }
       
    function language()
    {
        $response               = array();
        $response['language']   = array();
        $response['language']   = get_instance()->lang->language;
        echo json_encode($response);
    }

    function getcoursetutor(){

        $cid    = (preg_match('/^[1-9]+[0-9]*$/', $this->input->post('courseid')) == true) ? $this->input->post('courseid') : false;
        $result = $this->Tutor_model->get_tutor_name_by_course($cid);
        $temp   = '';
        foreach ($result as $key => $value) {
             $temp  = (($value['us_image'] == 'default.jpg')?default_user_path():  user_path()).$value['us_image'];
           
            $result[$key]['img_org'] = '<div class="dsp-box">'.'<img alt="'.$value['us_name'].'" src="'.$temp.'" />'.'</div>';
        }
        
        echo json_encode($result);
    }

    public function invalidate_course($param = array())
    {
        //Invalidate cache
        $course_id = isset($param['course_id']) ? $param['course_id'] : false;
        if ($course_id) {
            $this->memcache->delete('course_' . $course_id);
            $this->memcache->delete('course_mob'. $course_id);
            $this->memcache->delete('mobile_course_'. $course_id);
        } else {
            $this->memcache->delete('all_courses');
            $this->memcache->delete('sales_manager_all_sorted_courses');
            $this->memcache->delete('top_courses');
        }
        $this->memcache->delete('all_sorted_course');
        $this->memcache->delete('popular_courses');
        $this->memcache->delete('featured_courses');
        $this->memcache->delete('active_courses');
        
    }

    function get_assigned_tutors()
    {
        $course_id = $this->input->post('course_id');
        // $course_id      = (preg_match('/^[1-9]+[0-9]*$/', $this->input->post('course_id')) == true) ? $this->input->post('course_id') : false;
        $faculties      = $this->Course_model->get_course_tutors(array('course_id' => $course_id, 'select' => 'users.id, us_name, us_role_id, roles.rl_name'));
        echo json_encode($faculties);
    }

    function get_restricted_access_faculties()
    {
        $faculties      = $this->Course_model->get_restricted_access_faculties();
        echo json_encode($faculties);
    }
    public function get_language_list(){

        $objects                = array();
        $objects['key']         = 'languages';
        $callback               = 'course_languages';
        $languages              = $this->memcache->get($objects, $callback,array()); 
        $keyword                = $this->input->post('cb_language');
                
        $result = array_filter($languages, function ($item) use ($keyword) {
            if (stripos($item['cl_lang_name'], $keyword) !== false) {
                return true;
            }
            return false;
        });
        echo json_encode($result);
    }
}
<?php
class Profile extends CI_Controller
{
    private $__loggedInId   = 0;
    public  $__badges       = array('0'=>'None', '1' => 'Starter', '2'=> 'Expert', '3' => 'Certified');
    function __construct()
    {
        parent::__construct();
        $this->__admin = $this->auth->get_current_user_session('admin');
        
        if(empty($this->__admin))
        {
            redirect('login');
        }
        $this->actions = $this->config->item('actions');
        $this->lang->load('faculty');
    }
    
    function index()
    {
        if(!$this->__admin['id'])
        {
            redirect(admin_url('faculties'));
        }
        $this->load->model(array('Faculty_model'));
        $faculty = $this->Faculty_model->faculty(array( 'id' => $this->__admin['id'], 'rating' => true, 'sample' => true));
        if(!$faculty)
        {
            redirect(admin_url('faculties'));
        }
        $this->load->model(array('User_model', 'Course_model'));
        $data                   = array();
        $breadcrumb             = array();
        $breadcrumb[]           = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]           = array( 'label' => 'Profile', 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']     = $breadcrumb;
        $data['badges']         = $this->__badges;
        $faculty['courses']     = $this->Faculty_model->course_tutors(array('tutor_id' => $faculty['id']));
        //echo '<pre>'; print_r($faculty['courses']);die;
        $faculty['us_youtube_url'] = json_decode($faculty['us_youtube_url']);
        
        //processing faculty languages
        $language_speaks_ids    = explode(',', $faculty['us_language_speaks']);
        $language_names         = array();
        if(!empty($language_speaks_ids))
        {
            foreach($language_speaks_ids as $language_id)
            {
                $lang_object = $this->Course_model->course_language(array('language_id'=> $language_id));
                if($lang_object)
                {
                    $language_names[] = $lang_object['cl_lang_name'];
                }
            }
        }
        if(!empty($language_names))
        {
            $faculty['us_language_speaks'] = implode(',', $language_names);
        }
        //End
        
        //processing faculty location 
        $this->load->model(array('Location_model'));
        $data['states']     = array();
        $states             = $this->Location_model->states();
        if(!empty($states))
        {
            foreach($states as $state)
            {
                $data['states'][$state['id']] = $state;
            }
        }
        $data['faculty_city']   = ($faculty['us_native'])?$this->Location_model->city(array('id' => $faculty['us_native'])):0;
        $data['cities'] = array();
        if($data['faculty_city'])
        {
            $data['cities'] = $this->Location_model->cities(array('state_id' => $data['faculty_city']['state_id']));
        }
        //End
        
        
        $data['faculty']        = $faculty;
        $language_error         = '';
        $language_ids           = array();
        $data['is_super_admin'] = $this->auth->is_superadmin();
       // echo '<pre>'; print_r($_POST);die;

        $this->load->library('form_validation');
        $this->form_validation->set_rules('us_name', 'lang:name', 'required');
        $this->form_validation->set_rules('us_email', 'lang:email', 'required|valid_email|callback_email_check['.$this->__admin['id'].']');
        $this->form_validation->set_rules('us_phone', 'lang:phone', 'required');
        $this->form_validation->set_rules('us_about', 'lang:about_faculty', 'required');
        $us_youtube_url = $this->input->post('us_youtube_url');
        if(!empty($us_youtube_url)){
            $this->form_validation->set_rules('us_youtube_url[]', 'lang:Youtube_URL', 'valid_url');
        }

        
        //processing languages
        $languages = $this->input->post('us_language_speaks');
        $languages = explode(',', $languages);

        if(!empty($languages))
        {
            foreach($languages as $language)
            {
                if($language)
                {
                    $lang_object = $this->Course_model->course_language(array('language_name'=> $language));
                    if($lang_object)
                    {
                        $language_ids[] = $lang_object['id'];
                    }
                    else
                    {
                        $language_error .= 'Unknown language <b>'.$language.'</b><br />';
                    }
                }
            }
        }
        //End
            
            
        if(empty($language_ids) && $faculty['us_role_id'] == 3)
        {  
            $this->form_validation->set_rules('us_language_speaks', 'Invalid Language', 'callback_language_check');
        }
        
        if($faculty['us_role_id'] == 3)
        {
            $this->form_validation->set_rules('us_degree', 'lang:qualification', 'required');
            $this->form_validation->set_rules('us_experiance', 'lang:experiance', 'required|max_length[3]|numeric');
            
            $this->form_validation->set_rules('us_degree', 'lang:faculty_degree', 'required');
            $this->form_validation->set_rules('us_native', 'lang:faculty_native', 'required');
        }
        else
        {
            $this->form_validation->set_rules('us_degree', 'lang:qualification', '');
            $this->form_validation->set_rules('us_experiance', 'lang:experiance', 'max_length[3]|numeric');            
        }
        
        $old_password       = $this->input->post('old_password');
        $password           = $this->input->post('password');
        $confirm_password   = $this->input->post('confirm_password');
        
        if($old_password.$password.$confirm_password != '')
        {
            $this->form_validation->set_rules('old_password', 'lang:old_password', 'trim|required|callback_oldpassword_check');
            $this->form_validation->set_rules('password', 'lang:password', 'trim|required|min_length[6]');
            $this->form_validation->set_rules('confirm_password', 'lang:confirm_password', 'trim|required|matches[password]');            
        }
        
        if ($this->form_validation->run() == FALSE)
        {
            $data['error'] = validation_errors();
            $this->load->view($this->config->item('admin_folder').'/profile_form', $data);
        }
        else
        {
            $save                       = array();
            $save['id']                 = $this->__admin['id'];
            $save['us_name']            = $this->input->post('us_name');
            if($password)
            {
                $save['us_password']        = sha1($password);
            }
            $save['us_email']           = $this->input->post('us_email');
            $save['us_phone']           = $this->input->post('us_phone');
            $save['us_experiance']      = $this->input->post('us_experiance');
            $save['us_degree']          = $this->input->post('us_degree');
            $save['us_about']           = $this->input->post('us_about');
            $save['us_native']          = $this->input->post('us_native');
            //$save['us_language_speaks'] = $this->input->post('us_language_speaks');
            $save['us_language_speaks'] = implode(',', $language_ids);
            $save['us_youtube_url']     = ($this->input->post('us_youtube_url')) ? json_encode($this->input->post('us_youtube_url')) : '';
            $save['us_badge']           = $this->input->post('us_badge');
            $save['action_id']          = $this->actions['update']; 
            $save['action_by']          = $this->auth->get_current_admin('id');
            $save['us_account_id']      = $this->config->item('id');
            //echo '<pre>'; print_r($save);die;
            $this->Faculty_model->save($save);	
            
            $user_data              = array();
            $user_data['user_id']   = $this->__admin['id'];
            $user_data['username']  = $this->input->post('us_name');
            $user_data['useremail']  = $this->input->post('us_email');
            $user_data['user_type'] = $this->__admin['us_role_id']; ;
            
            $message_template                           = array();
            $message_template['username']               = $save['us_name'] ;

            $triggered_activity     = 'admin_profile_data_changed';
            log_activity($triggered_activity, $user_data, $message_template); 
            //updating session
            $admin                      = array();
            $this->__admin['us_name']   = $save['us_name'];
            $this->__admin['us_email']  = $save['us_email'];
            $admin['admin']             = $this->__admin;
            $this->session->set_userdata($admin);
            //End

            $template    = 'success';
            $message     = lang('faculty_updated_success');
            if($language_error!='')
            {
                $template  = 'error';
                $message  .= '<br />'.$language_error;                
            }
            
            $this->session->set_flashdata($template, $message);            
            redirect(admin_url('profile'));
        }
    }
    
    function cities()
    {
        $this->load->model(array('Location_model'));
        $state_id           = $this->input->post('state_id');
        $response           = array();
        $response['cities'] = $this->Location_model->cities(array('state_id' => $state_id));
        echo json_encode($response);exit;
    }
    
    public function oldpassword_check($old_password)
    {
        $this->load->model(array('Faculty_model'));
        $old_password_hash      = sha1($old_password);
        $user                   = $this->Faculty_model->faculty(array('id'=>$this->__admin['id']));
        $old_password_db_hash   = $user['us_password'];
        if($old_password_hash != $old_password_db_hash)
        {
           $this->form_validation->set_message('oldpassword_check', 'Old password not match');
           return FALSE;
        } 
        return TRUE;
    }

    function upload_user_image()
    {
        $response               = array();
        $response['error']      = 'false';
        $response['message']    = lang('faculty_image_saved');
        $this->load->model(array('User_model', 'Course_model'));
        $user_id                = $this->__admin['id'];//$this->input->post('id');
        $directory              = user_upload_path();
        if(!file_exists($directory)){
            mkdir($directory, 0777, true);
        }
        $config                     = array();
        $config['upload_path']      =  $directory;
        $config['allowed_types']    = 'gif|jpg|png|jpeg';
        //$new_name                   = $_FILES['file']['name'];
        //$new_name                   = explode('.', $new_name);
        $new_name                   = $user_id.'.jpg';//$user_id.'.'.$new_name[sizeof($new_name)-1];
        $config['file_name']        = $new_name;
        $config['overwrite']        = TRUE;
        $this->load->library('upload');
        $this->upload->initialize($config);
        $upload = $this->upload->do_upload('file');
        if($upload)
        {
            $uploaded_data  = $this->upload->data();
            $new_file       = $this->crop_image($uploaded_data);
            $has_s3         = $this->settings->setting('has_s3');
            if( $has_s3['as_superadmin_value'] && $has_s3['as_siteadmin_value'] )
            {
                $user_profile_path = user_upload_path().$new_file;
                uploadToS3($user_profile_path, $user_profile_path);
                unlink($user_profile_path);
            }
            $response['user_image'] = user_path().$new_file.'?v='.rand(100, 999); 
            $save                   = array();
            $save['id']             = $user_id;
            $save['us_image']       = $new_file.'?v='.rand(100, 999);   
            $this->User_model->save($save);
        }
        else
        {
            $response['error']      = 'true';
            $response['message']    = 'Error in updating faculty image';            
        }
        echo json_encode($response);
    }
    
    function crop_image($uploaded_data)
    {
        $source_path = $uploaded_data['full_path'];
        define('DESIRED_IMAGE_WIDTH', 155);
        define('DESIRED_IMAGE_HEIGHT', 155);
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
        $desired_aspect_ratio = DESIRED_IMAGE_WIDTH / DESIRED_IMAGE_HEIGHT;

        if ($source_aspect_ratio > $desired_aspect_ratio)
        {
            /*
             * Triggered when source image is wider
             */
            $temp_height = DESIRED_IMAGE_HEIGHT;
            $temp_width = ( int ) (DESIRED_IMAGE_HEIGHT * $source_aspect_ratio);
        } else {
            /*
             * Triggered otherwise (i.e. source image is similar or taller)
             */
            $temp_width = DESIRED_IMAGE_WIDTH;
            $temp_height = ( int ) (DESIRED_IMAGE_WIDTH / $source_aspect_ratio);
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

        $x0 = ($temp_width - DESIRED_IMAGE_WIDTH) / 2;
        $y0 = ($temp_height - DESIRED_IMAGE_HEIGHT) / 2;
        $desired_gdim = imagecreatetruecolor(DESIRED_IMAGE_WIDTH, DESIRED_IMAGE_HEIGHT);
        imagecopy(
            $desired_gdim,
            $temp_gdim,
            0, 0,
            $x0, $y0,
            DESIRED_IMAGE_WIDTH, DESIRED_IMAGE_HEIGHT
        );

        /*
         * Render the image
         * Alternatively, you can save the image in file-system or database
         */

        //header('Content-type: image/jpeg');
        $directory          = user_upload_path();
        $this->make_directory($directory);
        imagejpeg($desired_gdim, $directory.$uploaded_data['raw_name'].'.jpg');

        /*
         * Add clean-up code here
         */
        return $uploaded_data['raw_name'].'.jpg';
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
    
    public function email_check($email, $id)
    {
        $this->load->model(array('Faculty_model'));
        $email_available = $this->Faculty_model->faculty(array('email'=>$email, 'exclude_id'=>$id));
        if (!empty($email_available) || (isset($this->__super_admin['us_email']) && $email == $this->__super_admin['us_email']))
        {
                $this->form_validation->set_message('email_check', 'Email Id <b>'.$email.'</b> is used by <b>'.((strlen($email_available['rl_name'])>30)?substr($email_available['rl_name'], 0, 25).'...':$email_available['rl_name']).'</b> named <b>'.$email_available['us_name'].'</b>');
                return FALSE;
        }
        else
        {
                return TRUE;
        }
    }

}

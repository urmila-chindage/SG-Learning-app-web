<?php
class Faculties extends CI_Controller
{
    public $__badges = array('0'=>'None', '1' => 'Show Badge');
    function __construct()
    {   
        parent::__construct();
        date_default_timezone_set('Asia/Kolkata');
        $this->__role_query_filter = array();
        $redirect               = $this->auth->is_logged_in(false, false);
        $this->__loggedInUser   = $this->auth->get_current_user_session('admin');
        $this->__admin_index    = 'admin';
        
        if (!$redirect)
        {
            redirect('login');
        }

        if(isset($this->__loggedInUser['us_role_id']) && $this->__loggedInUser['us_role_id'] == 8)
        {
            $this->__role_query_filter['institute_id'] = $this->__loggedInUser['id'];                                
        }
        // $this->actions = $this->config->item('actions');
        $this->load->model(array('Faculty_model', 'User_model', 'Course_model', 'Role_model'));
        $this->lang->load('faculty');
        $this->__super_admin = $this->Faculty_model->super_admin($this->__loggedInUser['id']);

        $this->__limit  = 50;

        $this->__permission     = $this->accesspermission->get_permission(
                                            array(
                                                'role_id' => $this->__loggedInUser['role_id'],
                                                'module' => 'faculty'
                                                )
                                            );       
        $this->course_privilege           = $this->accesspermission->get_permission(array('role_id' => $this->__loggedInUser['role_id'],'module' => 'course'));
    }
    
    function index()
    {
        if(!in_array('1', $this->__permission))
        {
            redirect(admin_url()); exit;
        }
        $data                       = array();
        $breadcrumb                 = array();
        $breadcrumb[]               = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]               = array( 'label' => lang('manage_faculties'), 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']         = $breadcrumb;
        $data['title']              = lang('faculties');
        $inst_params = array(
            'type'=>'1', 
            'select'=>'id,rl_name',
            'deleted'=>'0',
            'status'=>'1'
        );

        if(isset($this->__role_query_filter['institute_id'])){
            $inst_params['institute_id'] = $this->__role_query_filter['institute_id'];
        }
        
        $data['roles']              = $this->Role_model->roles($inst_params);
        $data['faculties']          = array();
        $role_ids                   = array();
        foreach($data['roles'] as $role)
        {
            $role_ids[] = $role['id'];
        }
        $param                  = array();
        $param['role_ids']      = $role_ids;
        $param['exclude_ids']   = $this->__loggedInUser['id'];
        $param['count']         = true;
        $param['status']        = '1';//us_account_id
        $param['not_deleted']   = true;
        $total_faculties        = $this->Faculty_model->faculties($param);
        unset($param['count']);
        $param['limit']         = $this->__limit;
        $param['offset']        = 0;
        $param['select']        = 'users.id, us_name, us_email, us_image, us_about, us_phone, us_role_id, us_institute_id, us_experiance, us_degree, us_language_speaks, us_expertise, us_youtube_url, us_deleted, us_status, roles.rl_name, CONCAT(city_name,", ",state_name) as us_native';
        
        $param['status']        = '1';
        $param['not_deleted']   = true;
        $faculties              = $this->Faculty_model->faculties($param);
        //print_r($faculties);die;
        if(!empty($faculties))
        {
            foreach ($faculties as $faculty)
            {
                $faculty['courses']                = $this->Faculty_model->course_tutors(array('tutor_id' => $faculty['id']));
                // $data['faculties'][$faculty['id']] = $faculty;
                $data['faculties'][]                 = $faculty;
            }
        }
        
        $data['total_faculties']= $total_faculties;
        $data['limit']          = $this->__limit;
        $data['show_load_button']   = false;
        if($data['total_faculties'] > $this->__limit)
        {
            $data['show_load_button']   = true;            
        }
        $languages = $this->Course_model->languages();
        $data['languages'] = array();
        if(!empty($languages))
        {
            foreach ($languages as $language)
            {
                $data['languages'][$language['id']] = $language['cl_lang_name'];
            }
        }
        $data['permissions']        = $this->__permission;
        $data['course_privilege']   = $this->course_privilege;
        $data['full_course_access'] = $this->__loggedInUser['rl_full_course'];
        // echo '<pre>'; print_r($data);die;
        $this->load->view($this->config->item('admin_folder').'/faculties', $data);
    }

    function faculties_json()
    {
        $data               = array();
        $data['show_load_button']       = false;           

        $limit            = $this->input->post('limit');
        $offset           = $this->input->post('offset');
        $page             = $offset;
        if($page===NULL||$page<=0)
        {
            $page         = 1;
        }
        $page             = ($page - 1)* $limit;

        $data['roles']              = $this->Role_model->roles(array(
                                                    'type'=>'1', 
                                                    'select'=>'id,rl_name',
                                                    'deleted'=>'0',
                                                    'status'=>'1'
                                                ));
        $role_ids                   = array();
        foreach($data['roles'] as $role)
        {
            $role_ids[] = $role['id'];
        }
        $param                   = array();
        $param['role_ids']       = $role_ids;
        $param['exclude_ids']   = $this->__loggedInUser['id'];
        $param['keyword']        = $this->input->post('keyword');
        $param['keyword']        = trim($param['keyword']);
        $param['role_id']        = $this->input->post('role_id');

        if($param['keyword'])
        {
            $param['order_by']        = 'us_name';
            $param['direction']       = 'ASC';
        }
        // $param['not_deleted']    = true;
        $param['filter']        = $this->input->post('filter');
        $param['count']          = true;
        $data['total_faculties']   = $this->Faculty_model->faculties($param);

        unset($param['count']);
        $param['limit']          = $this->input->post('limit');
        $param['offset']         = $page;
        if($data['total_faculties'] > ($this->__limit*$offset))
        {
            $data['show_load_button']  = true;
        }
        $param['select']        = 'users.id, us_name, us_email, us_image, us_about, us_phone, us_role_id, us_institute_id, us_experiance, us_degree, us_language_speaks, us_expertise, us_youtube_url, us_deleted, us_status, roles.rl_name, roles.rl_full_course, CONCAT(city_name,", ",state_name) as us_native';
        $faculties              = $this->Faculty_model->faculties($param);
        
        if(!empty($faculties))
        {
            foreach ($faculties as $faculty)
            {
                $faculty['courses']                = $this->Faculty_model->course_tutors(array('tutor_id' => $faculty['id']));
                // $data['faculties'][$faculty['id']] = $faculty;
                $data['faculties'][]                 = $faculty;
            }
        }

        $data['limit']      = $limit;
        echo json_encode($data);
    }

    function get_institutes()
    {
        $this->load->model('Institute_model');
        $institutes     = $this->Institute_model->institutes(
                                                array(
                                                    'select' => 'id, ib_name, ib_institute_code,',
                                                    'not_deleted' => 'true',
                                                    'status' =>'1'
                                                ));
        $response               = array();
        $response['institutes'] = $institutes;
        echo json_encode($response);
    }
    
    function language()
    {
        $response = array();
        $response['language'] = array();
        $response['language'] = get_instance()->lang->language;
        echo json_encode($response);
    }
        
    public function create_faculty()
    {
        $response               = array();
        $response['error']      = false;
        $response['message']    = lang('faculty_created_success');

        if(in_array('2', $this->__permission))
        {
            $this->load->library('form_validation');
            $this->form_validation->set_error_delimiters('', '</br>');
            $this->form_validation->set_rules('faculty_name', 'lang:name', 'required');
            $this->form_validation->set_rules('faculty_email', 'lang:email', 'required|valid_email|callback_email_check[0]');
            $this->form_validation->set_rules('faculty_password', 'lang:password', 'required|min_length[6]');
            $this->form_validation->set_rules('faculty_role', 'lang:faculty_role', 'required');
            if ($this->form_validation->run() == FALSE)
            {
                $response['error']   = true;
                $response['message'] = validation_errors();
            }
            else
            {
                $save                   = array();
                $save['id']             = false;
                $save['us_name']        = $this->input->post('faculty_name');
                $save['us_status']      = '1';
                $save['us_email']       = $this->input->post('faculty_email');
                $password               = $this->input->post('faculty_password');
                $save['us_password']    = sha1($password);
                $save['us_role_id']     = $this->input->post('faculty_role');
                $save['us_institute_id'] = $this->input->post('faculty_institute');
                $save['us_email_verified'] = '1';
                $save['action_id']      = '1'; 
                $save['action_by']      = $this->auth->get_current_admin('id');
                $save['us_account_id']  = $this->config->item('id');

                $response['id']         = $this->Faculty_model->save($save);
                /*Log creation*/
                $user_data                      = array();
                $user_data['user_id']           = $this->__loggedInUser['id'];
                $user_data['username']          = $this->__loggedInUser['us_name'];
                $user_data['useremail']          = $this->__loggedInUser['us_email'];
                $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
                $user_data['phone_number']      = $this->__loggedInUser['us_phone'];
                $message_template               = array();
                $message_template['username']   = $this->__loggedInUser['us_name'];
                $triggered_activity             = 'faculty_created';
                log_activity($triggered_activity, $user_data, $message_template);	

                $send_mail = $this->input->post('send_mail');
                if( $send_mail == '1')
                {

                    $template               = $this->ofabeemailer->template(array('email_code' => 'faculty_welcome_mail'));
                    $param['to'] 	        = array($save['us_email']);
                    // $param['subject']       = $template['em_subject'];
                    $contents               = array(
                                                    'user_name' => $save['us_name']
                                                    ,'site_name' => config_item('site_name') 
                                                    ,'email_id' => $save['us_email']
                                                    ,'password' => $password
                                                    ,'site_url_login' => site_url('login')
                                                );
                    $param['subject']   = $this->ofabeemailer->process_mail_content($contents, $template['em_subject']);
                    $param['body']      = $this->ofabeemailer->process_mail_content($contents, $template['em_message']);
                    $this->ofabeemailer->send_mail($param);
                }
            }
        }
        else
        {
            $response['error']          = true;
            $response['message']        = 'You have no permission to add Faculty';
        }
        echo json_encode($response);exit;
    }    
    
    public function email_check($email, $id)
    {
        $email_available = $this->Faculty_model->faculty(array('email'=>$email, 'exclude_id'=>$id));
        if (!empty($email_available) || $email == $this->__super_admin['us_email'])
        {
                $this->form_validation->set_message('email_check', 'Email Id <b>'.$email.'</b> is used by <b>'.((strlen($email_available['rl_name'])>30)?substr($email_available['rl_name'], 0, 25).'...':$email_available['rl_name']).'</b> named <b>'.$email_available['us_name'].'</b>');
                return FALSE;
        }
        else
        {
                return TRUE;
        }
    }

    public function phone_check($phone,$id)
    {
        $phone_available = $this->Faculty_model->faculty(array('us_phone'=>$phone, 'exclude_id'=>$id, 'select' => 'users.id, users.us_name, users.us_phone, roles.rl_name'));
        //$this->db->last_query();die;
        if (!empty($phone_available))
        {
                $this->form_validation->set_message('phone_check', 'Phone number <b>'.$phone.'</b> is used by <b>'.((strlen($phone_available['rl_name'])>30)?substr($phone_available['rl_name'], 0, 25).'...':$phone_available['rl_name']).'</b> named <b>'.$phone_available['us_name'].'</b>');
                return FALSE;
        }
        else
        {
                return TRUE;
        }
    }
    
    function language_check()
    {
        $this->form_validation->set_message('language_check', 'Invalid Language');
        return FALSE;
    }
    
    
    function faculty($id=false)
    {
        if(in_array('3', $this->__permission))
        {
            if(!$id || ($id == $this->__super_admin['id']))
            {
                redirect(admin_url('faculties'));
            }
            $faculty = $this->Faculty_model->faculty(array(
                                                'id'=>$id,
                                                'select' => 'users.id, us_name, us_email, us_image, us_about, us_phone, us_role_id, us_institute_id, us_experiance, us_degree, us_language_speaks, us_expertise, us_youtube_url, us_deleted, us_status, roles.rl_name, roles.rl_full_course, us_native, us_category_id, us_badge'
                                            ));
            // echo "<pre>"; print_r($faculty); die();
            if(!$faculty)
            {
                redirect(admin_url('faculties'));
            }
            
            $this->load->model('Category_model');
            $data                   = array();
            $breadcrumb             = array();
            $breadcrumb[]           = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
            $breadcrumb[]           = array( 'label' => lang('manage_faculties'), 'link' => admin_url('faculties'), '' => 'active', 'icon' => '' );
            $breadcrumb[]           = array( 'label' => $faculty['us_name'], 'link' => '', 'active' => 'active', 'icon' => '' );
            $data['breadcrumb']     = $breadcrumb;
            $data['badges']         = $this->__badges;
            $faculty['courses']     = $this->Faculty_model->course_tutors(array('tutor_id' => $faculty['id']));
            
            $faculty['us_youtube_url'] = json_decode($faculty['us_youtube_url']);
            // echo "<pre>"; print_r($faculty); die();
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
                $faculty['us_language_speaks'] = implode(', ', $language_names);
            }
            //End

            //processing faculty categories
            $category_ids     = explode(',', $faculty['us_category_id']);
            $category_names   = array();
            if(!empty($category_ids))
            {
                foreach($category_ids as $category_id)
                {
                    $category_object = $this->Category_model->category(array('id'=>$category_id));
                    if($category_object)
                    {
                        $category_names[] = $category_object['ct_name'];
                    }
                }
            }
            if(!empty($category_names))
            {
                $faculty['us_category_id'] = implode(',', $category_names);
            }
            //End


            //processing faculty categories
            $expertise_ids     = explode(',', $faculty['us_expertise']);
            $expertise_names   = array();
            if(!empty($expertise_ids))
            {
                foreach($expertise_ids as $expertise_id)
                {
                    $expertise_object = $this->Faculty_model->expertise(array('id'=>$expertise_id));
                    if($expertise_object)
                    {
                        $expertise_names[] = $expertise_object['fe_title'];
                    }
                }
            }
            if(!empty($expertise_names))
            {
                $faculty['us_expertise'] = implode(',', $expertise_names);
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
            $data['faculty_city']   = $this->Location_model->faculty_city(array('id' => $faculty['us_native']));
            $data['cities'] = array();
            if($data['faculty_city'])
            {
                $data['cities'] = $this->Location_model->cities(array('state_id' => $data['faculty_city']['state_id']));
            }
            //End
                
            $data['faculty']        = $faculty;
            $language_error         = '';
            $language_ids           = array();
            
            $this->load->library('form_validation');
            $this->form_validation->set_rules('us_name', 'lang:name', 'required');
            $this->form_validation->set_rules('us_email', 'lang:email', 'required|valid_email|callback_email_check['.$id.']');
            $this->form_validation->set_rules('us_phone', 'lang:phone', 'required|callback_phone_check['.$id.']');
            $this->form_validation->set_rules('us_about', 'lang:about_faculty', 'required');
            $youtube_url = $this->input->post('us_youtube_url');
            if(!empty($youtube_url)){
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
                        $lang_object = $this->Course_model->course_language(array('language_name'=> trim($language)));
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
            
            //processing expertise
            $expertises     = $this->input->post('us_expertise');//catepory names
            $expertises     = explode(',', $expertises);
            $expertise_ids  = array();
            if(!empty($expertises))
            {
                foreach($expertises as $expertise)
                {
                    if($expertise)
                    {
                        $expertise_object = $this->Faculty_model->expertise(array('expertise_name'=>$expertise));
                        if($expertise_object)
                        {
                            $expertise_ids[] = $expertise_object['id'];
                        }
                        else 
                        {
                            $expertise_ids[] = $this->Faculty_model->save_expertise(array('id'=>false, 'fe_title'=>$expertise, 'fe_account_id' => $this->config->item('id')));           
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
                $this->form_validation->set_rules('us_experiance', 'lang:experiance', 'required|max_length[5]|numeric');
                
                $this->form_validation->set_rules('us_degree', 'lang:faculty_degree', 'required');
                $this->form_validation->set_rules('us_native', 'lang:faculty_native', 'required');
            }
            else
            {
                $this->form_validation->set_rules('us_degree', 'lang:qualification', '');
                $this->form_validation->set_rules('us_experiance', 'lang:experiance', 'max_length[5]|numeric');            
            }
            
            if ($this->form_validation->run() == FALSE)
            {
                $data['error'] = validation_errors();
                // echo "<pre>"; print_r($data); die(); 
                $this->load->view($this->config->item('admin_folder').'/faculty_form', $data);
            }
            else
            {
                $save                       = array();
                $save['id']                 = $id;
                $save['us_name']            = $this->input->post('us_name');
                $save['us_email']           = $this->input->post('us_email');
                $save['us_phone']           = $this->input->post('us_phone');
                $experience                 = explode('.',$this->input->post('us_experiance'));
                $exp_error      = '';
                if($experience[0]<0||$experience[0]>59){
                    $exp_error      = 'Invalid experience.<br />';
                }else{
                    if(isset($experience[1])&&($experience[1]<0||$experience[0]>11)){
                        $exp_error      = 'Invalid experience.<br />';
                    }
                }
                $experience[1]              = isset($experience[1])?$experience[1]:0;
                $experience                 = $experience[0]*12+$experience[1];
                if($exp_error != ''){
                    $experience = 0;
                }
                $save['us_experiance']      = $experience;
                $save['us_degree']          = $this->input->post('us_degree');
                $save['us_about']           = $this->input->post('us_about');
                $save['us_native']          = $this->input->post('us_native');
                //$save['us_language_speaks'] = $this->input->post('us_language_speaks');
                $save['us_category_id'] = implode(',', $category_ids);
                $save['us_expertise']   = implode(',', $expertise_ids);
                $save['us_language_speaks'] = implode(',', $language_ids);
                $save['us_youtube_url'] = ($this->input->post('us_youtube_url')) ? json_encode($this->input->post('us_youtube_url')) : '';
                $save['action_id']      = '1'; 
                $save['action_by']      = $this->auth->get_current_admin('id');
                $save['us_account_id']  = $this->config->item('id');
                //echo '<pre>'; print_r($save);exit;
                $this->Faculty_model->save($save);

                /*Log creation*/
                $user_data                      = array();
                $user_data['user_id']           = $this->__loggedInUser['id'];
                $user_data['username']          = $this->__loggedInUser['us_name'];
                $user_data['useremail']          = $this->__loggedInUser['us_email'];
                $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
                $user_data['phone_number']      = $this->__loggedInUser['us_phone'];
                $message_template               = array();
                $message_template['username']   = $this->__loggedInUser['us_name'];
                $triggered_activity             = 'faculty_updated';
                log_activity($triggered_activity, $user_data, $message_template);
                
                $template    = 'success';
                $message     = lang('faculty_updated_success');
                if($language_error!='' || $exp_error != '')
                {
                    $template  = 'error';
                    $message  .= '<br />'.$language_error.$exp_error;                
                }
                $this->session->set_flashdata($template, $message);            
                redirect(admin_url('faculties/faculty/'.$id));
            }
        }
        else
        {
            redirect(admin_url());
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
    
    function change_status()
    {
        $response               = array();
        $response['error']      = false;
        $faculty_id             = $this->input->post('faculty_id');
        $faculty                = $this->Faculty_model->faculty(array('id' => $faculty_id, 'select' => 'users.id,us_institute_id,us_name, us_email ,us_status, us_email_verified,us_role_id'));

        $save                   = array();
        $save['id']             = $faculty_id;
        $save['action_by']      = $this->auth->get_current_admin('id');
        $save['updated_date']   = date('Y-m-d H:i:s');
        $save['us_status']      = '1';
        $save['action_id']      = '1';
        $response['message']    = lang('activated');
        if($faculty['us_status'])
        {
            $faculty['us_status']   = '0';
            $response['message']    = lang('deactivated');
            $save['us_status']      = '0';
            $save['action_id']      = '1';
        }

        if($save['us_status'] == 1){
            //Check institute status.
            $objects        = array();
            $objects['key'] = 'institute_' . $faculty['us_institute_id'];
            $callback       = 'institute';
            $params         = array('id' => $faculty['us_institute_id']);
            $institute      = $this->memcache->get($objects, $callback, $params);
            if($faculty['us_role_id'] == 8 && ($institute['ib_deleted'] == 1 || $institute['ib_status'] == 0)){
                $response['error']      = true;
                $response['message']    = 'Faculty institute is not active/deleted.';
                echo json_encode($response);die;
            }
            //End check institute status.
        }
        
        if( !$this->Faculty_model->save($save) )
        {
            $response['error']   = true;
            $response['message'] = lang('error_change_status');
        }
        else
        {
            $notification_action = 'faculty_account_activated';
            $notify_to = array($faculty_id);
            if($save['us_status']==0)
            {
                $notification_action = 'faculty_account_deactivated';
                /*Log creation*/
                $user_data                      = array();
                $user_data['user_id']           = $this->__loggedInUser['id'];
                $user_data['username']          = $this->__loggedInUser['us_name'];
                $user_data['useremail']          = $this->__loggedInUser['us_email'];
                $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
                $user_data['phone_number']      = $this->__loggedInUser['us_phone'];
                $message_template               = array();
                $message_template['username']   = $this->__loggedInUser['us_name'];
                $message_template['count']      = 'a faculty';
                $triggered_activity             = 'faculty_deactivated_bulk';
                log_activity($triggered_activity, $user_data, $message_template);
            } 
            else 
            {
                 /*Log creation*/
                 $user_data                      = array();
                 $user_data['user_id']           = $this->__loggedInUser['id'];
                 $user_data['username']          = $this->__loggedInUser['us_name'];
                 $user_data['useremail']          = $this->__loggedInUser['us_email'];
                 $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
                 $user_data['phone_number']      = $this->__loggedInUser['us_phone'];
                 $message_template               = array();
                 $message_template['username']   = $this->__loggedInUser['us_name'];
                 $message_template['count']      = 'a faculty';
                 $triggered_activity             = 'faculty_activated_bulk';
                 log_activity($triggered_activity, $user_data, $message_template);
            }

            //Notification
            $this->load->library('Notifier');
            $this->notifier->push(
                array(
                    'action_code' => $notification_action,
                    'assets' => array('faculty_name' => $faculty['us_name']),
                    'individual' => true,
                    'push_to' => $notify_to
                )
            );
            //End notification

            if($faculty['us_email_verified'] == '1')
            {
                $body  = 'Hi '.$faculty['us_name'].'<br />Faculty Account has been '. $response['message'] .' by admin.<br />';
                if($response['message'] == lang('deactivated'))
                {
                    $body .= 'Please contact admin for details.<br/>';
                }
                $this->ofabeemailer->send_mail(array('to' => array($faculty['us_email']), 'subject' => 'Account '.$response['message'], 'body' => $body));                
            }            
        }
        $faculty             = $this->Faculty_model->faculty(array(
                                                        'id' => $faculty_id,
                                                        'select' => 'users.id, us_name, us_email, us_image, us_about, us_phone, us_role_id, us_institute_id, us_experiance, us_degree, us_language_speaks, us_expertise, us_youtube_url, us_deleted, us_status, roles.rl_name, CONCAT(city_name,", ",state_name) as us_native'
                                                    ));
        $faculty['courses']  = $this->Faculty_model->course_tutors(array('tutor_id' => $faculty['id']));
        $response['faculty'] = $faculty;
        echo json_encode($response);
    }
    
    function restore()
    {
        $response               = array();
        $response['error']      = false;
        $faculty_id             = $this->input->post('faculty_id');
        $faculty                = $this->Faculty_model->faculty(array('id' => $faculty_id));

        //Check institute status.
        $objects        = array();
        $objects['key'] = 'institute_' . $faculty['us_institute_id'];
        $callback       = 'institute';
        $params         = array('id' => $faculty['us_institute_id']);
        $institute      = $this->memcache->get($objects, $callback, $params);

        if($faculty['us_role_id'] == 8 && ($institute['ib_deleted'] == 1 || $institute['ib_status'] == 0)){
            $response['error']      = true;
            $response['message']    = 'Faculty institute is not active/deleted.';
            echo json_encode($response);die;
        }
        //End check institute status.

        $save                   = array();
        $save['id']             = $faculty_id;
        $save['action_by']      = $this->auth->get_current_admin('id');
        $save['action_id']      = '1';
        $save['updated_date']   = date('Y-m-d H:i:s');
        $save['us_deleted']     = '0';
        $save['us_status']      = '0';
        $response['message']    = lang('restore_faculty_success');
        if(!$this->Faculty_model->save($save))
        {
            $response['error']   = true;
            $response['message'] = lang('restore_faculty_failed');
        } else 
        {
             /*Log creation*/
             $user_data                      = array();
             $user_data['user_id']           = $this->__loggedInUser['id'];
             $user_data['username']          = $this->__loggedInUser['us_name'];
             $user_data['useremail']          = $this->__loggedInUser['us_email'];
             $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
             $user_data['phone_number']      = $this->__loggedInUser['us_phone'];
             $message_template               = array();
             $message_template['username']   = $this->__loggedInUser['us_name'];
             $triggered_activity             = 'faculty_restored';
             log_activity($triggered_activity, $user_data, $message_template);
        }
        $faculty             = $this->Faculty_model->faculty(array(
                                                            'id' => $faculty_id,
                                                            'select' => 'users.id, us_name, us_email, us_image, us_about, us_phone, us_role_id, us_institute_id, us_experiance, us_degree, us_language_speaks, us_expertise, us_youtube_url, us_deleted, us_status, roles.rl_name, CONCAT(city_name,", ",state_name) as us_native'
                                                        ));
        $faculty['courses']  = $this->Faculty_model->course_tutors(array('tutor_id' => $faculty['id']));
        $response['faculty'] = $faculty;
        echo json_encode($response);        
    }
    
    function delete()
    {        
        
        $response               = array();
        $response['error']      = false;
        $faculty_id             = $this->input->post('faculty_id');
        $faculty                = $this->Faculty_model->faculty(array(
                                                            'id' => $faculty_id,
                                                            'select' => 'users.id, us_name, us_email, us_email_verified, us_image, us_about, us_phone, us_role_id, us_institute_id, us_experiance, us_degree, us_language_speaks, us_expertise, us_youtube_url, us_deleted, us_status, roles.rl_name, CONCAT(city_name,", ",state_name) as us_native'
                                                        ));
        if( !$faculty || ($faculty_id == $this->__super_admin['id']))
        {
            $response['error'] = true;
            $response['message'] = lang('faculty_not_found');
            echo json_encode($response);exit;
        }
        $save                   = array();
        $save['id']             = $faculty_id;
        $save['action_by']      = $this->auth->get_current_admin('id');
        $save['action_id']      = '1';
        $save['updated_date']   = date('Y-m-d H:i:s');
        $save['us_deleted']     = '1';
        
        $response['message']    = lang('faculty_delete_success');
        if(!$this->Faculty_model->save($save))
        {
            $response['error']   = true;
            $response['message'] = lang('delete_faculty_failed');
        }
        else
        {
            /*Log creation*/
            $deleted_count                  = 'a faculty';
            $user_data                      = array();
            $user_data['user_id']           = $this->__loggedInUser['id'];
            $user_data['username']          = $this->__loggedInUser['us_name'];
            $user_data['useremail']          = $this->__loggedInUser['us_email'];
            $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
            $user_data['phone_number']      = $this->__loggedInUser['us_phone'];
            $message_template               = array();
            $message_template['username']   = $this->__loggedInUser['us_name'];
            $message_template['count']      = $deleted_count;
            $triggered_activity             = 'faculty_deleted';
            log_activity($triggered_activity, $user_data, $message_template);

            //Notification
            $this->load->library('Notifier');
            $this->notifier->push(
                array(
                    'action_code' => 'faculty_account_deleted',
                    'individual' => true,
                    'push_to' => array($faculty_id)
                )
            );
            //End notification

            if($faculty['us_email_verified'] == '1')
            {
                $body  = 'Hi '.$faculty['us_name'].'<br />Faculty Account has been deleted by admin.<br />';
                $body .= 'Please contact admin for details.<br/>';
                $this->ofabeemailer->send_mail(array('to' => array($faculty['us_email']), 'subject' => 'Account deleted', 'body' => $body));
            }            
        }
        $faculty             = $this->Faculty_model->faculty(array(
                                                        'id' => $faculty_id,
                                                        'select' => 'users.id, us_name, us_email, us_image, us_about, us_phone, us_role_id, us_institute_id, us_experiance, us_degree, us_language_speaks, us_expertise, us_youtube_url, us_deleted, us_status, roles.rl_name, CONCAT(city_name,", ",state_name) as us_native'
                                                    ));
        $faculty['courses']  = $this->Faculty_model->course_tutors(array('tutor_id' => $faculty['id']));
        if(!empty($faculty['id']))
        {
            $this->Faculty_model->unassign_faculty(array('ct_tutor_id' => $faculty['id']));
        }
        else
        {
            $response['error']   = true;
            $response['message'] = lang('delete_faculty_failed');
        }
        $course_ids = $this->input->post('faculty_courses_ids');
        $this->invalidate_course(array('course_id' => $course_ids));
        //echo $this->db->last_query();die;
        $response['faculty'] = $faculty;
        echo json_encode($response);        
    }
    
    function reset_password()
    {
        $response               = array();
        $response['error']      = 'false';
        $response['message']    = lang('faculty_password_reset_success');
      
        $faculty_id             = $this->input->post('faculty_id');
        $faculty                = $this->Faculty_model->faculty(array(
                                                        'id' => $faculty_id,
                                                        'select' => 'users.id, us_name, us_email, us_email_verified, us_image, us_about, us_phone, us_role_id, us_institute_id, us_experiance, us_degree, us_language_speaks, us_expertise, us_youtube_url, us_deleted, us_status, roles.rl_name, CONCAT(city_name,", ",state_name) as us_native'
                                                    ));
        if( !$faculty || ($faculty_id == $this->__super_admin['id']) )
        {
            $response['error'] = true;
            $response['message'] = lang('faculty_not_found');
            echo json_encode($response);exit;
        }

        $save                       = array();
        $save['id']                 = $faculty['id'];
        $password                   = $this->generate_string(8);
        $save['us_password']        = sha1($password);
        $save['us_reset_password']  = '1';
        $this->Faculty_model->save($save);

        /*Log creation*/
        $user_data                      = array();
        $user_data['user_id']           = $this->__loggedInUser['id'];
        $user_data['username']          = $this->__loggedInUser['us_name'];
        $user_data['useremail']          = $this->__loggedInUser['us_email'];
        $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
        $user_data['phone_number']      = $this->__loggedInUser['us_phone'];
        $message_template               = array();
        $message_template['username']   = $this->__loggedInUser['us_name'];
        $triggered_activity             = 'faculty_password_reset';
        log_activity($triggered_activity, $user_data, $message_template);

        if($faculty['us_email_verified'] == '1')
        {
            $body = 'Hi '.$faculty['us_name'].'<br />Your password has been reset by admin. Please user the below credentials to login into the webiste<br />';
            $body .= 'Email : <b>'.$faculty['us_email'].'</b><br />';
            $body .= 'Password : <b>'.$password.'</b><br />';
            $body .= 'Url : <a href="'.site_url('login').'">'.site_url('login').'</b>';
            $this->ofabeemailer->send_mail(array('to' => array($faculty['us_email']), 'subject' => lang('password_reset'), 'body' => $body));
        }        
        echo json_encode($response); 
    }
    
    private function generate_string($length)
    {
        $seed = str_split('abcdefghijklmnopqrstuvwxyz'.'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.'0123456789!@#$%^&*()'); 
        shuffle($seed); 
        $rand = '';
        foreach (array_rand($seed, $length) as $k)
        {
            $rand .= $seed[$k];
        }
        return $rand;
    }
    
    function faculty_course()
    {
        $response                    = array();
        $response['faculty_courses'] = array();
        $faculty_id                  = $this->input->post('faculty_id');
        $existing_courses            = $this->Faculty_model->course_tutors(array(
                                                        'tutor_id' => $faculty_id,
                                                        'select' => 'course_tutors.ct_course_id, course_basics.cb_title'
                                                    ));
        if(!empty($existing_courses))
        {
            foreach ($existing_courses as $existing_course)
            {
                $response['faculty_courses'][] = $existing_course['ct_course_id'];
            }
        }        
        $course_param                   = array();
        $course_param['not_deleted']    = '1';
        $response['courses']            = $this->Course_model->courses($course_param);
        echo json_encode($response);
    }
    
    function add_faculty_to_course()
    {
        $faculty_id     = $this->input->post('faculty_id');
        $courses        = $this->input->post('courses');
        $courses        = json_decode($courses);
        $existing_courses = $this->Faculty_model->course_tutors(array(
                                                        'tutor_id' => $faculty_id,
                                                        'select' => 'course_tutors.ct_course_id, course_basics.cb_title'
                                                    ));
        $notifications  = array();
        $existing_courses_ids = array();
        if(!empty($existing_courses))
        {
            foreach ($existing_courses as $existing_course)
            {
                $existing_courses_ids[] = $existing_course['ct_course_id'];
            }
        }
                
        if(!empty($courses))
        {
            foreach ($courses as $course_id)
            {
                if(!in_array($course_id, $existing_courses_ids))
                {
                    $save                 = array();
                    $save['ct_tutor_id']  = $faculty_id;
                    $save['ct_course_id'] = $course_id;
                    $this->Faculty_model->assign_faculty($save);
                    $course             = $this->Course_model->course(array('id' => $course_id));

                    if(!isset($notifications[$save['ct_course_id']]['assign']))
                    {
                        $notifications[$save['ct_course_id']]['assign']['name'] = $course['cb_title'];
                        $notifications[$save['ct_course_id']]['assign']['id']   = $course['id'];
                        $notifications[$save['ct_course_id']]['assign']['users'] = array();
                    }
                    $notifications[$save['ct_course_id']]['assign']['users'][] = $faculty_id;
                }
            }
        }
        
        if(!empty($existing_courses_ids))
        {
            foreach ($existing_courses_ids as $existing_courses_id)
            {
                if(!in_array($existing_courses_id, $courses))
                {
                    $delete                 = array();
                    $delete['ct_tutor_id']  = $faculty_id;
                    $delete['ct_course_id'] = $existing_courses_id;
                    if( !empty($delete['ct_tutor_id']) && !empty($delete['ct_course_id']) )
                    {
                        $this->Faculty_model->unassign_faculty($delete);
                    }
                    else
                    {
                        $response['error']   = true;
                        $response['message'] = lang('delete_faculty_failed');
                    }
                   
                    // $course             = $this->Course_model->course(array('id' => $existing_courses_id));

                    // if(!isset($notifications[$save['ct_course_id']]['unassign']))
                    // {
                    //     $notifications[$save['ct_course_id']]['unassign']['name'] = $course['cb_title'];
                    //     $notifications[$save['ct_course_id']]['unassign']['id']   = $course['id'];
                    //     $notifications[$save['ct_course_id']]['unassign']['users'][] = array();
                    // }
                    // $notifications[$save['ct_course_id']]['unassign']['users'][] = $faculty_id;
                }
            }
        }
        
        $response               = array();
        $response['error']      = 'false';
        $response['message']    = lang('faculty_added_to_course');
        
        $faculty                = $this->Faculty_model->faculty(array(
                                                                    'id' => $faculty_id,
                                                                    'select' => 'users.id, us_name, us_email, us_image, us_about, us_phone, us_role_id, us_institute_id, us_experiance, us_degree, us_language_speaks, us_expertise, us_youtube_url, us_deleted, us_status, roles.rl_name, CONCAT(city_name,", ",state_name) as us_native'
                                                                ));
        $faculty['courses']     = $this->Faculty_model->course_tutors(array(
                                                                    'tutor_id' => $faculty_id,
                                                                    'select' => 'course_tutors.ct_course_id, course_basics.cb_title'
                                                                ));
        foreach($courses as $course_id){
            $this->invalidate_course(array('course_id' => $course_id));
        }
        $this->invalidate_course();
        $response['faculty']    = $faculty;
        echo json_encode($response);

        foreach($notifications as $notification)
        {
            if(isset($notification['assign']))
            {
                //Notification
                $this->load->library('Notifier');
                $this->notifier->push(
                    array(
                        'action_code' => 'faculty_assigned_to_course',
                        'assets' => array('course_id' => $notification['assign']['id'],'course_name' => $notification['assign']['name']),
                        'target' => $notification['assign']['id'],
                        'individual' => true,
                        'push_to' => $notification['assign']['users']
                    )
                );
                //End notification
            }

            // if(isset($notification['unassign']))
            // {
            //     //Notification
            //     $this->load->library('Notifier');
            //     $this->notifier->push(
            //         array(
            //             'action_code' => 'faculty_assigned_to_course',
            //             'assets' => array(),
            //             'target' => $course,
            //             'individual' => true,
            //             'push_to' => $notify_to
            //         )
            //     );
            //     //End notification
            // }
        }
    }
    
    function unassign_course()
    {        
        $faculty_id     = $this->input->post('faculty_id');
        $course_id      = $this->input->post('course_id');
        $response               = array();
        $response['error']      = 'false';
        $response['message']    = lang('faculty_removed_to_course');
        $delete                 = array();
        $delete['ct_tutor_id']  = $faculty_id;
        $delete['ct_course_id'] = $course_id;
        if( !empty($delete['ct_tutor_id']) && !empty($delete['ct_course_id']) )
        {
            $this->Faculty_model->unassign_faculty($delete);
        }
        else
        {
            $response['error']   = true;
            $response['message'] = lang('delete_faculty_failed');
        }

        $course     = $this->Course_model->course(array('id' => $course_id));

        $this->invalidate_course(array('course_id' => $course_id));
        $this->invalidate_course();

        echo json_encode($response);
    }
    
    function delete_faculties_bulk()
    {
        $faculty_ids   = json_decode($this->input->post('faculties'));
        if(!empty($faculty_ids))
        {
            $notify_to = array();
            foreach ($faculty_ids as $faculty_id) {
                if($faculty_id != $this->__super_admin['id'])
                {
                    $save                   = array();
                    $save['id']             = $faculty_id;
                    $save['us_deleted']     = '1';
                    $save['action_by']      = $this->auth->get_current_admin('id');
                    $save['updated_date']   = date('Y-m-d H:i:s');
                    $save['action_id']      = '1';
                    $this->Faculty_model->save($save);
                    $notify_to[] = $faculty_id;
                }
            }

            //Notification
            $this->load->library('Notifier');
            $this->notifier->push(
                array(
                    'action_code' => 'faculty_account_deleted',
                    'individual' => true,
                    'push_to' => $notify_to
                )
            );
            //End notification

            /*Log creation*/
            $deleted_count                  = sizeof($faculty_ids).' faculties';
            $user_data                      = array();
            $user_data['user_id']           = $this->__loggedInUser['id'];
            $user_data['username']          = $this->__loggedInUser['us_name'];
            $user_data['useremail']          = $this->__loggedInUser['us_email'];
            $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
            $user_data['phone_number']      = $this->__loggedInUser['us_phone'];
            $message_template               = array();
            $message_template['username']   = $this->__loggedInUser['us_name'];
            $message_template['count']      = $deleted_count;
            $triggered_activity             = 'faculty_deleted';
            log_activity($triggered_activity, $user_data, $message_template);
        }
        $data               = array();
        $data['faculties']  = $this->Faculty_model->faculties(array(
                                                                'faculty_ids' => $faculty_ids, 
                                                                'select' => 'users.id, us_name, us_email, us_email_verified, us_image, us_about, us_phone, us_role_id, us_institute_id, us_experiance, us_degree, us_language_speaks, us_expertise, us_youtube_url, us_deleted, us_status, roles.rl_name, CONCAT(city_name,", ",state_name) as us_native'
                                                            ));
        foreach($data['faculties'] as $faculty)
        {
            if($faculty['us_email_verified'] == '1')
            {
                $body  = 'Hi '.$faculty['us_name'].'<br />Faculty Account has been deleted by admin.<br />';
                $body .= 'Please contact admin for details.<br/>';
                $this->ofabeemailer->send_mail(array('to' => array($faculty['us_email']), 'subject' => 'Account deleted', 'body' => $body));
            }            
        }
        echo json_encode($data);
    }
    
    function change_status_bulk()
    {
        $status         = $this->input->post('status');
        $label          = ($status)?'activated':'deactivated';
        $faculty_ids    = json_decode($this->input->post('faculties'));
        $notification_action = 'faculty_account_activated';
        $notify_to      = array();
        if(!empty($faculty_ids))
        {
            foreach ($faculty_ids as $faculty_id) {
                if($faculty_id != $this->__super_admin['id'])
                {
                    $save                   = array();
                    $save['id']             = $faculty_id;
                    $save['us_status']      = $status;
                    $save['action_by']      = $this->auth->get_current_admin('id');
                    $save['updated_date']   = date('Y-m-d H:i:s');
                    $save['action_id']      = '1';
                    $this->Faculty_model->save($save);
                    $notify_to[]            = $faculty_id;
                }
            }
        }
        $notification_action = 'faculty_account_activated';
        if($status==0)
        {
            $notification_action = 'faculty_account_deactivated';
            /*Log creation*/
            $user_data                      = array();
            $user_data['user_id']           = $this->__loggedInUser['id'];
            $user_data['username']          = $this->__loggedInUser['us_name'];
            $user_data['useremail']          = $this->__loggedInUser['us_email'];
            $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
            $user_data['phone_number']      = $this->__loggedInUser['us_phone'];
            $message_template               = array();
            $message_template['username']   = $this->__loggedInUser['us_name'];
            $message_template['count']      = sizeof($faculty_ids).' faculties';
            $triggered_activity             = 'faculty_deactivated_bulk';
            log_activity($triggered_activity, $user_data, $message_template);
        } 
        else 
        {
                /*Log creation*/
                $user_data                      = array();
                $user_data['user_id']           = $this->__loggedInUser['id'];
                $user_data['username']          = $this->__loggedInUser['us_name'];
                $user_data['useremail']          = $this->__loggedInUser['us_email'];
                $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
                $user_data['phone_number']      = $this->__loggedInUser['us_phone'];
                $message_template               = array();
                $message_template['username']   = $this->__loggedInUser['us_name'];
                $message_template['count']      = sizeof($faculty_ids).' faculties';
                $triggered_activity             = 'faculty_activated_bulk';
                log_activity($triggered_activity, $user_data, $message_template);
        }

        //Notification
        $this->load->library('Notifier');
        $this->notifier->push(
            array(
                'action_code' => $notification_action,
                'individual' => true,
                'push_to' => $notify_to
            )
        );
        //End notification

        $data               = array();
        $data['faculties']  = $this->Faculty_model->faculties(array(
                                                'faculty_ids' => $faculty_ids,
                                                'select' => 'users.id, us_name, us_email, us_email_verified, us_image, us_about, us_phone, us_role_id, us_institute_id, us_experiance, us_degree, us_language_speaks, us_expertise, us_youtube_url, us_deleted, us_status, roles.rl_name, CONCAT(city_name,", ",state_name) as us_native'
                                            ));
        foreach($data['faculties'] as $faculty)
        {
            if($faculty['us_email_verified'] == '1')
            {
                $body  = 'Hi '.$faculty['us_name'].'<br />Faculty Account has been '. $label .' by admin.<br />';
                if($label == 'deactivated')
                {
                    $body .= 'Please contact admin for details.<br/>';
                }
                $this->ofabeemailer->send_mail(array('to' => array($faculty['us_email']), 'subject' => 'Account '.$label, 'body' => $body));                
            }            
        }                                            
        echo json_encode($data);
    }

    function send_message()
    {
        $response['error']      = false;
        $response['message']    = 'Message has been sent sucessfully';
        $error                  = 0;
        $subject            = $this->input->post('send_user_subject');
        $message            = base64_decode($this->input->post('send_user_message'));
        $faculty_ids        = $this->input->post('faculty_ids');
        $faculty_ids        = json_decode($faculty_ids);
        $faculty_email_ids  = array();
        $faculties            = $this->Faculty_model->faculties(array(
                                            'faculty_ids' => $faculty_ids,
                                            'select' => 'users.id as user_id, users.us_email',
                                            'us_email_verified' => '1'
                                        ));
        // echo "<pre>"; print_r($faculties); die();
        if(!empty($faculties))
        {
            $system_message     = array();
            $random_message_id  = rand(1000, 9999);
            $date_time          = date(DateTime::ISO8601);

            foreach($faculties as $faculty)
            {
                $system_message[] = array(
                    "messageId" => $random_message_id,
                    "senderId" => $this->__loggedInUser['id'],
                    "senderName" => $this->__loggedInUser['us_name'],
                    "senderImage" => user_path().$this->__loggedInUser['us_image'],
                    "receiverId" => $faculty['user_id'],
                    "message" => $message,
                    "datetime" => $date_time
                );

                $faculty_email_ids[] = $faculty['us_email'];
            }

            //sending notification
            if(!empty($system_message))
            {
                $this->load->library('JWT');
                $payload                     = array();
                $payload['id']               = $this->__loggedInUser['id'];
                $payload['email_id']         = $this->__loggedInUser['us_email'];
                $payload['register_number']  = '';
                $token                       = $this->jwt->encode($payload, config_item('jwt_token')); 
                $response['notified']        = send_notification_to_mongo($system_message, $token);
            }
            //End
                        
            $param                                  = array();
            $param['subject'] 	                    = $subject;
            $param['body'] 		                    = $message;
            $param['to']                            = $faculty_email_ids;
            $param['strictly_to_recepient']         = true;
            $send = $this->ofabeemailer->send_mail($param);
        }
        echo json_encode($response);
    }
    
    function upload_user_image()
    {
        $user_id                = $this->input->post('id');
        $directory              = user_upload_path();
        if(!file_exists($directory)){
            mkdir($directory, 0777, true);
        }
        $config                     = array();
        $config['upload_path']      =  $directory;
        $config['allowed_types']    = 'gif|jpg|png|jpeg';
        $new_name                   = $user_id.'.jpg';
        $config['file_name']        = $new_name;
        $config['overwrite']        = TRUE;
        $this->load->library('upload');
        $this->upload->initialize($config);
        $this->upload->do_upload('file');
        $uploaded_data = $this->upload->data();
        
        $new_file               = $this->crop_image($uploaded_data);
        
        $has_s3     = $this->settings->setting('has_s3');
        if( $has_s3['as_superadmin_value'] && $has_s3['as_siteadmin_value'] )
        {
            $user_profile_path = user_upload_path().$new_file;
            uploadToS3($user_profile_path, $user_profile_path);
            unlink($user_profile_path);
        }

        $save                   = array();
        $save['id']             = $user_id;
        $save['us_image']       = $new_name."?v=".rand(10,1000);;
        $save['action_by']      = $this->auth->get_current_admin('id');
        $save['action_id']      = '1';
        $save['updated_date']   = date('Y-m-d H:i:s');
        $this->Faculty_model->save($save);

        $response               = array();
        $response['error']      = 'false';
        $response['message']    = lang('faculty_image_saved');
        $response['user_image'] = user_path().$new_file;
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

    function check_valid_language(){

        $languages = $this->input->post('us_language_speaks');
        $languages = explode(',', $languages);
        $language_error = "";
        $language_ids = array();
        $response = array();
        $response['error'] = false;
        $response['message'] = '';
        if(!empty($languages))
        {
            foreach($languages as $language)
            {
                if($language)
                {
                    $lang_object = $this->Course_model->course_language(array('language_name'=> trim($language)));
                    if($lang_object)
                    {
                        if(in_array($lang_object['id'], $language_ids)){
                            $language_error .= 'You have already added the language <b>'.$language.'</b><br />';
                            $response['error'] = true;
                        }
                        $language_ids[] = $lang_object['id'];
                    }
                    else
                    {
                        $response['error'] = true;
                        $language_error .= 'Invalid language <b>'.$language.'</b>.<br />';
                    }
                }
            }
            $response['message'] = $language_error;    
            echo json_encode($response);
        }
    }
    
    function get_expertise_list()
    { 
        $this->load->model('Faculty_model');
        $data 	 		= array();
        $keyword 		= $this->input->post('expertise_keyword');
        $expertises		= $this->Faculty_model->expertises(array('name'=>$keyword));
        $data['tags'] 	= array();
        if( sizeof($expertises))
        {
            foreach( $expertises as $expertise)
            {
                $expertise['name'] = $expertise['fe_title'];
                $data['tags'][]   = $expertise;
            }
        }
        echo json_encode($data);
    }

    function import_faculties()
    {
        $affected_rows      = array();      
        $directory          = $this->config->item('upload_folder').'/faculty/'.$this->config->item('acct_domain');
        if (!file_exists($directory)) 
        {
            mkdir($directory, 0777, true);
        }
        $config                     = array();
        $config['upload_path']      = $directory;
        $config['allowed_types']    = "csv";      
        $config['encrypt_name']     = true;
        $this->load->library('upload');
        $this->upload->initialize($config);
        $uploaded = $this->upload->do_upload('file');

        if($uploaded)
        {
            $upload_data        =  $this->upload->data();
            $file               = $upload_data['full_path'];
            $file               = fopen($file, "r") or die(json_encode(array('status' => 3, 'message' => 'Unable to open file')));
            $header             = fgetcsv($file);
            $template_header    = array("faculty_name", "faculty_email", "faculty_password", "faculty_phone", "faculty_qualification", "faculty_about", "faculty_experience"/*, "faculty_institute_code"*/);
            $difference         = array_merge(array_diff($template_header, $header), array_diff($header, $template_header));

            if(!empty($difference))
            {
                echo json_encode(array('status' => 3, 'message' => 'Invalid template')); die();
            }
            foreach ($header as $h_key => $h_value) 
            {
                $$h_value = strip_tags($h_key);
            }
            $row                = 0;
            $success_rows       = 0;
            $input_buffer_size  = 200;
            $role_id            = $this->input->post('role_id');
            $faculty_objects    = array();
            $input_email_ids    = array();
            $duplicate_emails   = array();
            $column_dropdown    = array();
            $email_content      = array();
            $save_faculties     = array();
            $ins_objects        = array();
            $input_mobile_numbers = array();
            $ins_objects['key'] = 'institutes';
            $callback           = 'institutes';
            $institutes         = $this->memcache->get($ins_objects, $callback, array()); 
            $this->institutes   = array();
            if(!empty($institutes))
            {
                foreach($institutes as $institute)
                {
                    $this->institutes[$institute['ib_institute_code']] = $institute;
                }    
            }
            while (($faculty = fgetcsv($file, 10000, ",")) !== FALSE)
            {
                if(empty(array_filter($faculty)))
                {
                    $row++;
                    continue;
                }

                $faculty[$faculty_email]    = strip_tags($faculty[$faculty_email]);
                $input_email_ids[]          = $faculty[$faculty_email];
                $faculty_objects[]          = $faculty;
                $input_mobile_numbers[]     = $faculty[$faculty_phone];
                if($row%$input_buffer_size == 0)
                {
                    $email_ids = $this->User_model->users_by_columns(array('select' => 'us_email', 'email_ids' => $input_email_ids));
                    if(sizeof($email_ids) > 0 )
                    {
                        foreach($email_ids as $object)
                        {
                            $duplicate_emails[] = $object['us_email'];
                        }    
                    }
                    $input_email_ids        = array();
                }
                $row++;
            }
            if(sizeof($input_email_ids) > 0 )
            {
                $email_ids = $this->User_model->users_by_columns(array('select' => 'us_email', 'email_ids' => $input_email_ids));
                if(sizeof($email_ids) > 0 )
                {
                    foreach($email_ids as $object)
                    {
                        $duplicate_emails[] = $object['us_email'];
                    }    
                }
            }

            if(empty($faculty_objects))
            {
                echo json_encode(array('status' => 3, 'message' => 'Template is empty')); die();
            }

            foreach($faculty_objects as $row => $faculty_obj)
            { 
                foreach ($template_header as $h_key => $h_value) 
                {
                    $faculty_obj[$$h_value] = trim($faculty_obj[$$h_value]);
                }

                $defective_columns              = array();
                $defect_reason                  = array();

                if(strlen($faculty_obj[$faculty_name]) > 50)
                {
                    $defective_columns[] = $faculty_name;
                    $defect_reason[$faculty_name] = 'Exceeded limit';
                }
                if(!is_numeric($faculty_obj[$faculty_phone]) || strlen($faculty_obj[$faculty_phone]) > 11)
                {
                    $defective_columns[] = $faculty_phone;
                    $defect_reason[$faculty_phone] = 'Invalid Number';
                }
                else{
                    $phoneExists = $this->User_model->check_user_exist(array('select' => 'us_phone', 'phone' => $faculty_obj[$faculty_phone]));
                        if($phoneExists){
                            $defective_columns[] = $faculty_phone;
                            $defect_reason[$faculty_phone] = 'Number in use';
                        }
                        else
                        {
                            $phone_counts = array_count_values($input_mobile_numbers);

                            if(isset($phone_counts[$faculty_obj[$faculty_phone]]) && $phone_counts[$faculty_obj[$faculty_phone]] > 1)
                            {
                                $defective_columns[] = $faculty_phone;
                                $defect_reason[$faculty_phone] = 'Phone number exists';
                            }
                        }

                    }
                if(!is_numeric($faculty_obj[$faculty_experience])) 
                {
                    $defective_columns[] = $faculty_experience;
                    $defect_reason[$faculty_experience] = 'Invalid Experience';
                }
                if($faculty_obj[$faculty_email] == '')
                {
                    $defective_columns[] = $faculty_email;
                }
                else
                {
                    if(filter_var($faculty_obj[$faculty_email], FILTER_VALIDATE_EMAIL) === FALSE)
                    {
                        $defective_columns[] = $faculty_email;
                    }
                    else
                    {
                        $emailExists = $this->User_model->check_user_exist(array('select' => 'us_email', 'email' => $faculty_obj[$faculty_email]));
                        if($emailExists){
                            $defective_columns[] = $faculty_email;
                            $defect_reason[$faculty_email] = 'Email in use';
                        }
                        else
                        {
                            $email_counts = array_count_values($input_email_ids);

                            if(isset($email_counts[$faculty_obj[$faculty_email]]) && $email_counts[$faculty_obj[$faculty_email]] > 1)
                            {
                                $defective_columns[] = $faculty_email;
                                $defect_reason[$faculty_email] = 'Phone number exists';
                            }
                        }  
                    }     
                }
                if($faculty_obj[$faculty_password] == '')
                {
                    $defective_columns[] = $faculty_password;
                }

                if(sizeof($defective_columns) == 0)
                {
                    if(in_array($faculty_obj[$faculty_email], $duplicate_emails))
                    {
                        $defective_columns[] = $faculty_email;
                        $affected_rows[$row] = array('defect_columns' => $defective_columns , 'row' => $faculty_obj, 'row_number' => $row, 'type' => 'duplicate_data_row' );
                    }    
                    else
                    {
                        $faculty  = array();
                        if( $role_id  == '8' )
                        {
                            $institute_code     = $faculty_obj[$faculty_institute_code];
                            if(isset($this->institutes[$institute_code]) && !empty($this->institutes[$institute_code]))
                            {
                                $institute                  = $this->institutes[$institute_code];
                                $faculty['us_institute_id'] = $institute['id'];
                            }
                            else
                            {            
                                $column_dropdown[$row.'_'.$faculty_institute_code] = '<select class="all_institutes_selector '.$institute_code.'" name="content['.$row.'][row]['.$faculty_institute_code.']" data-toggle="tooltip" data-original-title="Invalid Entry !"><option value="">Choose Institute</option></select>';
                                $defective_columns[] = $faculty_institute_code;
                                $affected_rows[$row] = array('defect_columns' => array($faculty_institute_code), 'row' => $faculty_obj, 'row_number' => $row, 'type' => 'invalid_data_row' );
                                continue;
                            }
                        }
                        
                        $faculty['id']                    = false;
                        $faculty['us_name']               = $faculty_obj[$faculty_name];
                        $faculty['us_email']              = $faculty_obj[$faculty_email];
                        $password                         = $faculty_obj[$faculty_password];                                    
                        $faculty['us_password']           = sha1($password);
                        $faculty['us_role_id']            = $role_id;
                        $faculty['us_phone']              = $faculty_obj[$faculty_phone];
                        $faculty['us_degree']             = $faculty_obj[$faculty_qualification];
                        $faculty['us_about']              = $faculty_obj[$faculty_about];
                        
                        $experience     = explode('.',$faculty_obj[$faculty_experience]);
                        $exp_error      = '';
                        if($experience[0]<0||$experience[0]>59)
                        {
                            $exp_error      = 'Invalid experience.<br />';
                        }
                        else
                        {
                            if(isset($experience[1])&&($experience[1]<0||$experience[0]>11))
                            {
                                $exp_error      = 'Invalid experience.<br />';
                            }
                        }
                        $experience[1]              = isset($experience[1])?$experience[1]:0;
                        $experience                 = $experience[0]*12+$experience[1];
                        if($exp_error != '')
                        {
                            $experience     = 0;
                        }
                        $faculty['us_experiance']         = $experience;
                        $faculty['us_status']             = '1';
                        $faculty['us_email_verified']     = '1';
                        $faculty['action_id']             = '1'; 
                        $faculty['action_by']             = $this->auth->get_current_admin('id');
                        $faculty['us_account_id']         = $this->config->item('id');
                        // $this->Faculty_model->save($faculty);
                        $save_faculties[]                 = $faculty;
    
                        $new_email_param                = array();
                        $new_email_param['email']       = $faculty['us_email'];
                        $new_email_param['contents']    = array(
                                                                'user_name' => $faculty['us_name']
                                                                ,'site_name' => config_item('site_name') 
                                                                ,'email_id' => $faculty['us_email']
                                                                ,'password' => $password
                                                                ,'site_url_login' => site_url('login')
                                                            );
                        $email_content[] = $new_email_param;
                        $success_rows++;
                    }
                }
                else
                {
                    $affected_rows[$row] = array( 'defect_columns' => $defective_columns, 'row' => $faculty_obj, 'row_number' => $row, 'type' => 'invalid_data_row', 'defect_reason' => $defect_reason );
                }
                $row++;
            }
            fclose($file);
            if(sizeof($save_faculties) > 0 )
            {
                $this->Faculty_model->insert_faculties_bulk($save_faculties);
            }
            if( sizeof($email_content) > 0 )
            {
                 $this->process_bulk_mail($email_content,'faculty_welcome_mail');
            }

            $response               = array();
            $response['status']     = 1;
            $response['message']    = '';
            $message                = array();
            $status                 = 1; // 1 => succees, 2 => warning, 3 => error

            if( $success_rows > 0 )
            {
                $message[] = (($success_rows>1)?(' '.$success_rows.' Faculties'):'1 Faculty').' imported successfully';
                /*Log creation*/
                $imported_count                 = ($success_rows>1)?$success_rows.' faculties':'a faculty';
                $user_data                      = array();
                $user_data['user_id']           = $this->__loggedInUser['id'];
                $user_data['username']          = $this->__loggedInUser['us_name'];
                $user_data['useremail']          = $this->__loggedInUser['us_email'];
                $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
                $user_data['phone_number']      = $this->__loggedInUser['us_phone'];
                $message_template               = array();
                $message_template['username']   = $this->__loggedInUser['us_name'];
                $message_template['count']      = $imported_count;
                $triggered_activity             = 'faculties_imported';
                log_activity($triggered_activity, $user_data, $message_template);
            }

            $total_affected = sizeof($affected_rows);
            if( $total_affected > 0)
            {
                $memcache_index = 'faculty_import'.$this->__loggedInUser['id'];
                $response['redirect_url'] = admin_url('faculties/preview').$memcache_index;
                $status = 2;
                $message[] = 'We found some problem with the csv you uploaded. We are redirecting you to preview page.';

                $preview_contents                   = array();
                $preview_contents['headers']        = $template_header;
                $preview_contents['role_id']        = $role_id;
                $preview_contents['content']        = $affected_rows;
                $preview_contents['total_rows']     = $row;
                $preview_contents['inserted']       = $success_rows;
                $preview_contents['failed']         = $total_affected;
                $preview_contents['column_dropdown']= $column_dropdown;
                $preview_contents['back_to_home']   = admin_url('faculties');
                $this->memcache->set($memcache_index, $preview_contents);
            }
            //echo '<pre>'; print_r($affected_rows);die;
            $response['status'] = $status;
            $response['message'] = implode('<br />', $message);
            echo json_encode($response);
        }
        else
        {
           echo json_encode(array('status' => 3 , 'message'=>$this->upload->display_errors())); die; 
        }
    }

    function preview($key='')
    {
        if(!$key)
        {
            redirect(admin_url('faculties'));
        }
        $faculties_content = $this->memcache->get(array('key' => $key));
        if(empty($faculties_content['content']))
        {
            redirect(admin_url('faculties'));
        }
        $email_content      = array();
        $data               = array();
        $affected_rows      = array();
        $data['excell']     = $faculties_content;
        $data['action']     = admin_url('faculties/preview').$key;
        $this->load->library('form_validation');
        if ($this->input->server('REQUEST_METHOD') != 'POST')
        {
            $this->load->view($this->config->item('admin_folder').'/import_preview', $data);
        }
        else
        {
            $preview_data       = $this->input->post('preview_data');
            $faculties          = json_decode($preview_data, true);
            $row                = 0;
            $success_rows       = 0;
            $input_buffer_size  = 200;
            $faculty_objects    = array();
            $input_email_ids    = array();
            $input_mobile_numbers = array();
            $duplicate_emails   = array();
            $role_id            = $faculties_content['role_id'];
            foreach ($faculties_content['headers'] as $h_key => $h_value) 
            {
                $$h_value = strip_tags($h_key);
            }

            foreach($faculties as $row => $faculty)
            {
                if(empty(array_filter($faculty)))
                {
                    continue;
                }
                $faculty[$faculty_email]    = strip_tags($faculty[$faculty_email]);
                $input_email_ids[]          = $faculty[$faculty_email];
                $input_mobile_numbers       = $faculty[$faculty_phone]; 
                $faculty_objects[]          = $faculty;
                if($row%$input_buffer_size == 0)
                {
                    $email_ids = $this->User_model->users_by_columns(array('select' => 'us_email', 'email_ids' => $input_email_ids));
                    if(sizeof($email_ids) > 0 )
                    {
                        foreach($email_ids as $object)
                        {
                            $duplicate_emails[] = $object['us_email'];
                        }    
                    }
                    $input_email_ids        = array();
                }
            }
            if(sizeof($input_email_ids) > 0 )
            {
                $email_ids = $this->User_model->users_by_columns(array('select' => 'us_email', 'email_ids' => $input_email_ids));
                if(sizeof($email_ids) > 0 )
                {
                    foreach($email_ids as $object)
                    {
                        $duplicate_emails[] = $object['us_email'];
                    }    
                }
            }

            if(empty($faculty_objects))
            {
                redirect(admin_url('faculties'));
            }

            $email_content      = array();
            $ins_objects        = array();
            $column_dropdown    = array();
            $save_faculties     = array();
            $ins_objects['key'] = 'institutes';
            $callback           = 'institutes';
            $institutes         = $this->memcache->get($ins_objects, $callback, array()); 
            $this->institutes   = array();
            if(!empty($institutes))
            {
                foreach($institutes as $institute)
                {
                    $this->institutes[$institute['ib_institute_code']] = $institute;
                }    
            }

            foreach($faculty_objects as $row => $faculty_obj)
            { 
                foreach ($faculties_content['headers'] as $h_key => $h_value) 
                {
                    $faculty_obj[$$h_value] = trim($faculty_obj[$$h_value]);
                }

                $defective_columns  = array();
                $defect_reason      = array();
    
                if(strlen($faculty_obj[$faculty_name]) > 50)
                {
                    $defective_columns[] = $faculty_name;
                    $defect_reason[$faculty_name] = 'Exceeded limit';
                }
                if(!is_numeric($faculty_obj[$faculty_phone]) || strlen($faculty_obj[$faculty_phone]) > 11)
                {
                    $defective_columns[] = $faculty_phone;
                    $defect_reason[$faculty_phone] = 'Invalid Number';
                }
                else{
                $phoneExists = $this->User_model->check_user_exist(array('select' => 'us_phone', 'phone' => $faculty_obj[$faculty_phone]));
                    if($phoneExists){
                        $defective_columns[] = $faculty_phone;
                        $defect_reason[$faculty_phone] = 'Number in use';
                    }
                    else
                    {
                        $phone_counts = array_count_values($input_mobile_numbers);

                        if(isset($phone_counts[$faculty_obj[$faculty_phone]]) && $phone_counts[$faculty_obj[$faculty_phone]] > 1)
                        {
                            $defective_columns[] = $faculty_phone;
                            $defect_reason[$faculty_phone] = 'Phone number exists';
                        }
                    }
                }

                if(!is_numeric($faculty_obj[$faculty_experience])) 
                {
                    $defective_columns[] = $faculty_experience;
                    $defect_reason[$faculty_experience] = 'Invalid Experience';
                }
                if($faculty_obj[$faculty_email] == '')
                {
                    $defective_columns[] = $faculty_email;
                }
                else
                {
                    if(filter_var($faculty_obj[$faculty_email], FILTER_VALIDATE_EMAIL) === FALSE)
                    {
                        $defective_columns[] = $faculty_email;
                    }
                    else
                    {
                        $emailExists = $this->User_model->check_user_exist(array('select' => 'us_email', 'email' => $faculty_obj[$faculty_email]));
                        if($emailExists){
                            $defective_columns[] = $faculty_email;
                            $defect_reason[$faculty_email] = 'Email in use';
                        } 
                        else
                        {
                            $email_counts = array_count_values($input_email_ids);

                            if(isset($email_counts[$faculty_obj[$faculty_email]]) && $email_counts[$faculty_obj[$faculty_email]] > 1)
                            {
                                $defective_columns[] = $faculty_email;
                                $defect_reason[$faculty_email] = 'Phone number exists';
                            }
                        }  
                    }    
                }
                if($faculty_obj[$faculty_password] == '')
                {
                    $defective_columns[] = $faculty_password;
                }

                if(sizeof($defective_columns) == 0)
                {
                    if(in_array($faculty_obj[$faculty_email], $duplicate_emails))
                    {
                        $defective_columns[] = $faculty_email;
                        $affected_rows[$row] = array('defect_columns' => $defective_columns , 'row' => $faculty_obj, 'row_number' => $row, 'type' => 'duplicate_data_row' );
                    }    
                    else
                    {
                        $faculty  = array();
                        if( $role_id  == '8' )
                        {
                            $institute_code     = $faculty_obj[$faculty_institute_code];
                            if(isset($this->institutes[$institute_code]) && !empty($this->institutes[$institute_code]))
                            {
                                $institute                  = $this->institutes[$institute_code];
                                $faculty['us_institute_id'] = $institute['id'];
                            }
                            else
                            {   
                                $column_dropdown[$row.'_'.$faculty_institute_code] = '<select class="all_institutes_selector '.$institute_code.'" name="content['.$row.'][row]['.$faculty_institute_code.']" data-toggle="tooltip" data-original-title="Invalid Entry !"><option value="">Choose Institute</option></select>';
                                $defective_columns[] = $faculty_institute_code;
                                $affected_rows[$row] = array('defect_columns' => array($faculty_institute_code), 'row' => $faculty_obj, 'row_number' => $row, 'type' => 'invalid_data_row' );
                                continue;
                            }
                        }
                        $faculty['id']                    = false;
                        $faculty['us_name']               = $faculty_obj[$faculty_name];
                        $faculty['us_email']              = $faculty_obj[$faculty_email];
                        $password                         = $faculty_obj[$faculty_password];                                    
                        $faculty['us_password']           = sha1($password);
                        $faculty['us_role_id']            = $role_id;
                        $faculty['us_phone']              = $faculty_obj[$faculty_phone];
                        $faculty['us_degree']             = $faculty_obj[$faculty_qualification];
                        $faculty['us_about']              = $faculty_obj[$faculty_about];
                        
                        $experience     = explode('.',$faculty_obj[$faculty_experience]);
                        $exp_error      = '';
                        if($experience[0]<0||$experience[0]>59)
                        {
                            $exp_error      = 'Invalid experience.<br />';
                        }
                        else
                        {
                            if(isset($experience[1])&&($experience[1]<0||$experience[0]>11))
                            {
                                $exp_error      = 'Invalid experience.<br />';
                            }
                        }
                        $experience[1]              = isset($experience[1])?$experience[1]:0;
                        $experience                 = $experience[0]*12+$experience[1];
                        if($exp_error != '')
                        {
                            $experience     = 0;
                        }
                        $faculty['us_experiance']         = $experience;
                        $faculty['us_status']             = '1';
                        $faculty['us_email_verified']     = '1';
                        $faculty['action_id']             = '1'; 
                        $faculty['action_by']             = $this->auth->get_current_admin('id');
                        $faculty['us_account_id']         = $this->config->item('id');
                        // $this->Faculty_model->save($faculty);
                        $save_faculties[]                 = $faculty;
    
                        $new_email_param                = array();
                        $new_email_param['email']       = $faculty['us_email'];
                        $new_email_param['contents']    = array(
                                                                'user_name' => $faculty['us_name']
                                                                ,'site_name' => config_item('site_name') 
                                                                ,'email_id' => $faculty['us_email']
                                                                ,'password' => $password
                                                                ,'site_url_login' => site_url('login')
                                                            );
                        $email_content[] = $new_email_param;

                        $success_rows++;
                    }
                }
                else
                {
                    $affected_rows[$row] = array( 'defect_columns' => $defective_columns, 'row' => $faculty_obj, 'row_number' => $row, 'type' => 'invalid_data_row', 'defect_reason' => $defect_reason );
                }
            }

            if(sizeof($save_faculties) > 0 )
            {
                $this->Faculty_model->insert_faculties_bulk($save_faculties);
            }
        
            if( sizeof($email_content) > 0 )
            {
                 $this->process_bulk_mail($email_content,'faculty_welcome_mail');
            }

            if( sizeof($success_rows) > 0 )
            {
                /*Log creation*/
                $imported_count                 = ($success_rows>1)?$success_rows.' faculties':'a faculty';
                $user_data                      = array();
                $user_data['user_id']           = $this->__loggedInUser['id'];
                $user_data['username']          = $this->__loggedInUser['us_name'];
                $user_data['useremail']          = $this->__loggedInUser['us_email'];
                $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
                $user_data['phone_number']      = $this->__loggedInUser['us_phone'];
                $message_template               = array();
                $message_template['username']   = $this->__loggedInUser['us_name'];
                $message_template['count']      = $imported_count;
                $triggered_activity             = 'faculties_imported';
                log_activity($triggered_activity, $user_data, $message_template);
            }


            $total_affected = sizeof($affected_rows);
            if( $total_affected > 0 )
            {
                $memcache_index = $key;
                $redirect_url   = admin_url('faculties/preview').$memcache_index;
                $preview_contents                   = array();
                $preview_contents['headers']        = $faculties_content['headers'];
                $preview_contents['role_id']        = $role_id;
                $preview_contents['content']        = $affected_rows;
                $preview_contents['total_rows']     = $row;
                $preview_contents['column_dropdown']= $column_dropdown;
                $preview_contents['inserted']       = $success_rows;
                $preview_contents['failed']         = $total_affected;
                $preview_contents['back_to_home']   = admin_url('faculties');
                $this->memcache->set($memcache_index, $preview_contents);
                redirect($redirect_url);                    
            }
            else
            {
                $memcache_index = $key;
                $this->memcache->delete($memcache_index);
                             
                $response               = array();
                $response['success']    = true;
                $response['message']    = 'Faculties Imported Successfully';
                $this->session->set_flashdata('popup',$response);
                redirect(admin_url('faculties'));
            }
        }
    }

    public function invalidate_course($param = array())
    {
        //Invalidate cache
        $course_id = isset($param['course_id']) ? $param['course_id'] : false;
        if ($course_id) {
            if(is_array($course_id))
            {
                for($i = 0; $i < count($course_id); $i++){
                    $this->memcache->delete('course_' . $course_id[$i]);
                    $this->memcache->delete('course_mob' . $course_id[$i]);
                    $this->memcache->delete('course_notification_' . $course_id[$i]);
                }
            }
            else
            {
                $this->memcache->delete('course_' . $course_id);
                $this->memcache->delete('course_mob' . $course_id);
                $this->memcache->delete('course_notification_' . $course_id);
            }
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

    function process_bulk_mail($email_param,$email_code)
    {
        
        $data               = array(
            'email_code'    => $email_code,
            'data'          => json_encode($email_param)
        );
        $curlHandle         = curl_init(site_url()."cron_job/bulk_process_mail");
        $defaultOptions     = array (
                                CURLOPT_POST => 1,
                                CURLOPT_POSTFIELDS => $data,
                                CURLOPT_RETURNTRANSFER => false ,
                                CURLOPT_TIMEOUT_MS => 1000,
                             );
        curl_setopt_array($curlHandle , $defaultOptions);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, FALSE);     
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2); 
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array(
            'request-token: '.sha1(config_item('acct_domain').config_item('id')),
        ));
        $result = curl_exec($curlHandle);
        curl_close($curlHandle);

    } 
}
?>
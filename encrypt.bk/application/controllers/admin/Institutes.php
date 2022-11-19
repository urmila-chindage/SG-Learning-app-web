<?php
class Institutes extends CI_Controller
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
        else
        {
            $admin = $this->auth->get_current_user_session('admin');
            
        }
        $this->actions = $this->config->item('actions');
        $this->load->model(array('Institute_model', 'User_model', 'Course_model'));
        $this->lang->load('institute');
        // $this->__super_admin = $this->Institute_model->super_admin($admin['id']);
        $this->__limit  = 50;

        $this->__permission     = $this->accesspermission->get_permission(
                                                    array(
                                                        'role_id' => $this->__loggedInUser['role_id'],
                                                        'module' => 'institutes'
                                                        ));   
        $this->event_privilege  = $this->accesspermission->get_permission(array('role_id' => $this->__loggedInUser['role_id'],'module' => 'event'));                                               
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
        $breadcrumb[]               = array( 'label' => lang('manage_institutes'), 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']         = $breadcrumb;
        $data['title']              = lang('institutes');
        $data['institutes']         = array();
        
        $param                      = array();
        $param['count']             = true;
        $param['status']            = '1';
        $param['not_deleted']       = true;
        $data['total_institutes']   = $this->Institute_model->institutes($param);
        unset($param['count']);
        $param['select']        = 'id, ib_name, ib_institute_code, ib_phone, ib_image, ib_address, ib_head_name, ib_head_phone, ib_officer_name, ib_officer_email, ib_officer_phone, ib_class_code, ib_class_strength, ib_about, ib_deleted, ib_status';
        
        $param['limit']         = $this->__limit;
        $param['offset']        = 0;
        $param['status']        = '1';
        $param['not_deleted']   = true;
        $data['show_load_button']   = false;
        $institutes             = $this->Institute_model->institutes($param);

        if($data['total_institutes'] > $this->__limit)
        {
            $data['show_load_button']   = true;            
        }
        
        $data['limit']          = $this->__limit;

        $data['institutes']     = $institutes;
        $languages = $this->Course_model->languages();
        $data['languages']      = array();
        if(!empty($languages))
        {
            foreach ($languages as $language)
            {
                $data['languages'][$language['id']] = $language['cl_lang_name'];
            }
        }
        $data['permissions']    = $this->__permission;
        
        $this->load->view($this->config->item('admin_folder').'/institutes', $data);
        
    }

    function institutes_json()
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

        $param                   = array();
        $param['keyword']        = $this->input->post('keyword');
        $param['keyword']        = trim($param['keyword']);
        $param['filter']         = $this->input->post('filter');
        // $param['not_deleted']    = true;
        $param['count']          = true;
        $data['total_institutes']   = $this->Institute_model->institutes($param);

        unset($param['count']);
        $param['limit']          = $this->input->post('limit');
        $param['offset']         = $page;
        if($data['total_institutes'] > ($this->__limit*$offset))
        {
            $data['show_load_button']  = true;
        }
        $param['select']      = 'id, ib_name, ib_institute_code, ib_phone, ib_image, ib_address, ib_head_name, ib_head_phone, ib_officer_name, ib_officer_email, ib_officer_phone, ib_class_code, ib_about, ib_deleted, ib_status';
        $data['institutes']   = $this->Institute_model->institutes($param);
        
        $data['limit']      = $limit;
        echo json_encode($data);
    }
    
    function language()
    {
        $response = array();
        $response['language'] = array();
        $response['language'] = get_instance()->lang->language;
        echo json_encode($response);
    }
        
    public function create_institute()
    {
        $active_academic_year   = $this->Course_model->get_active_academic_year();    
        $academic_year_id       = $active_academic_year['id'];
        $academic_year_code     = $active_academic_year['ay_year_code'];
        $response               = array();
        $response['error']      = false;
        $response['message']    = lang('institute_created_success');
              
        
        if(in_array('2', $this->__permission))
        {
            $this->load->library('form_validation');
            $this->form_validation->set_error_delimiters('', '</br>');
            $this->form_validation->set_rules('institute_name', 'lang:name', 'required');
            $this->form_validation->set_rules('institute_code', 'lang:institute_code', 'required|max_length[6]|callback_code_check[0]');
            $this->form_validation->set_rules('institute_email', 'lang:email', 'required|valid_email|callback_email_check[0]');
            $this->form_validation->set_rules('institute_password', 'lang:password', 'required');
            if ($this->form_validation->run() == FALSE)
            {
                $response['error']   = true;
                $response['message'] = validation_errors();
            }
            else
            {
                $institute                          = array();
                $institute['ib_name']               = $this->input->post('institute_name');
                $institute['ib_institute_code']     = $this->input->post('institute_code');
                $institute['ib_head_email']         = $this->input->post('institute_email');
                $institute['ib_account_id']         = $this->config->item('id');

                $user                               = array();
                $user['id']                         = false;
                $user['us_name']                    = 'Admin - '.$this->input->post('institute_name');
                $user['us_email']                   = $this->input->post('institute_email');
                $password                           = $this->input->post('institute_password');
                $user['us_password']                = sha1($password);
                $user['us_role_id']                 = '8';
                $user['us_institute_id']            = '0';
                $user['us_institute_code']          = $this->input->post('institute_code');
                $user['us_email_verified']          = '1';
                $user['us_status']                  = '1';
                $user['action_id']                  = '1'; 
                $user['action_by']                  = $this->auth->get_current_admin('id');
                $user['us_account_id']              = $this->config->item('id');

                $params                 = array();
                $params['institute']    = $institute;
                $params['user']         = $user;
                $institute_id           = $this->Institute_model->save_institute($params);
                $response['id']         = $institute_id;
                $this->memcache->delete('institutes');

                /*Log creation*/
                $user_data                      = array();
                $user_data['user_id']           = $this->__loggedInUser['id'];
                $user_data['username']          = $this->__loggedInUser['us_name'];
                $user_data['useremail']          = $this->__loggedInUser['us_email'];
                $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
                $message_template               = array();
                $message_template['username']   = $this->__loggedInUser['us_name'];
                $message_template['institute']  = $this->input->post('institute_code').'-'.$this->input->post('institute_name');
                $triggered_activity             = 'institute_created';
                log_activity($triggered_activity, $user_data, $message_template);

                //Register course to course consolidated report table
                $courses         = $this->Course_model->course_for_consolidation();
                if(!empty($courses))
                {
                    $consolidations = array();
                    foreach($courses as $course)
                    {
                        $consolidations[] = array( 
                                                    'id'                        => false,
                                                    'ccr_course_id'             => $course['id'],
                                                    'ccr_institute_id'          => $institute_id,
                                                    'ccr_total_enrolled'        => 0,
                                                    'ccr_total_completed'       => 0,
                                                    'ccr_academic_year_id'      => $academic_year_id,
                                                    'ccr_academic_year_code'    => $academic_year_code,
                                                    'ccr_account_id'            => config_item('id')
                                                );
                    }
                    $this->Course_model->save_consolidation($consolidations);
                }
                //End

                
                $send_mail = $this->input->post('send_mail');
                if( $send_mail == '1')
                {

                    $template               = $this->ofabeemailer->template(array('email_code' => 'institute_welcome_mail'));
                    $param['to'] 	        = array($user['us_email']);
                    // $param['subject']       = $template['em_subject'];
                    $contents               = array(
                                                    'user_name' => $user['us_name']
                                                    ,'site_name' => config_item('site_name') 
                                                    ,'email_id' => $user['us_email']
                                                    ,'password' => $password
                                                    ,'site_url_login' => site_url('login')
                                                );
                    $param['cc']          = array('support@sglearningapp.com');
                    $param['subject']   = $this->ofabeemailer->process_mail_content($contents, $template['em_subject']);
                    $param['body']      = $this->ofabeemailer->process_mail_content($contents, $template['em_message']);
                    $this->ofabeemailer->send_mail($param);

                    // $body  = 'Hi '.$user['us_name'].'<br />Institute Account has been created by admin using following credentials. Please use the below credentials to login into the webiste<br />';
                    // $body .= 'Email : <b>'.$user['us_email'].'</b><br />';
                    // $body .= 'Password : <b>'.$password.'</b><br />';
                    // $body .= 'Url : <a href="'.site_url('login').'">'.site_url('login').'</b>';
                    // $this->ofabeemailer->send_mail(array('to' => array($user['us_email']), 'subject' => lang('account_created'), 'body' => $body));                
                }

            }
        }
        else
        {
            $response['error']          = true;
            $response['message']        = 'You have no permission to add Institute';
        }
       
        echo json_encode($response);exit;
    }
    
    
    public function email_check($email, $id)
    {
        $email_available = $this->User_model->user(array(
                                                    'email'=>$email
                                                ));
        if (!empty($email_available) )
        {
                $this->form_validation->set_message('email_check', 'Email Id <b>'.$email.'</b> is used ');
                return FALSE;
        }
        else
        {
                return TRUE;
        }
    }
    function code_check($code, $id)
    {
        $param              = array();
        $param['exclude_id']        = ($id != '0')? $id : false;
        $param['ib_institute_code'] = $code;
        $param['select']    = 'ib_institute_code, ib_name';
        
        $code_available = $this->Institute_model->institute($param);
        
        if (!empty($code_available))
        {
            $this->form_validation->set_message('code_check', 'The Institute code <b>'.$code.'</b> is already used by <br/><b>'.($code_available['ib_name']).'</b>');
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }
    function class_code_check($code, $id)
    {
        $param              = array();
        $param['exclude_id']    = ($id != '0')? $id : false;
        $param['ib_class_code'] = $code;
        $param['select']    = 'ib_class_code, ib_name';
        
        $code_available = $this->Institute_model->institute($param);
        // echo "<pre>"; print_r($code_available); 
        // die();
        if (!empty($code_available))
        {
            $this->form_validation->set_message('class_code_check', 'The Institute class code <b>'.$code.'</b> is already used by <br/><b>'.($code_available['ib_name']).'</b>');
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
    
    
    function institute($id=false)
    {
        if(in_array('3', $this->__permission))
        {
            if(!$id)
            {
                redirect(admin_url('institutes'));
            }
            
            $institute['ib_address']        = '';
            $institute['ib_head_name']      = '';
            $institute['ib_head_email']     = '';
            $institute['ib_head_phone']     = '';
            $institute['ib_officer_name']   = '';
            $institute['ib_officer_email']  = '';
            $institute['ib_officer_phone']  = '';
            $institute['ib_class_code']     = '';
 
            // echo '<pre>';print_r($_POST); die();
            $institute = $this->Institute_model->institute(array('id'=>$id));
            if(!$institute)
            {
                redirect(admin_url('institutes'));
            }

            $data                   = array();
            $data['institute_data'] = $institute;

            if($this->input->post('id'))
            {
                $save                       = array();
                $save['id']                 = $id;
                $save['ib_name']            = $this->input->post('ib_name');
                $save['ib_institute_code']  = $this->input->post('ib_institute_code');
                $save['ib_phone']           = $this->input->post('ib_phone');
                $save['ib_about']           = $this->input->post('ib_about');
                $save['ib_native']          = $this->input->post('ib_native');            
                
                $save['ib_address']        = $this->input->post('ib_address');
                $save['ib_head_name']      = $this->input->post('ib_head_name');
                $save['ib_head_email']     = $this->input->post('ib_head_email');
                $save['ib_head_phone']     = $this->input->post('ib_head_phone');
                $save['ib_officer_name']   = $this->input->post('ib_officer_name');
                $save['ib_officer_email']  = $this->input->post('ib_officer_email');
                $save['ib_officer_phone']  = $this->input->post('ib_officer_phone');
                $save['ib_class_code']     = $this->input->post('ib_class_code');
                $save['ib_class_strength'] = $this->input->post('ib_class_strength');
                $institute = $save;

                /*Log creation*/
                $user_data                      = array();
                $user_data['user_id']           = $this->__loggedInUser['id'];
                $user_data['username']          = $this->__loggedInUser['us_name'];
                $user_data['useremail']          = $this->__loggedInUser['us_email'];
                $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
                $message_template               = array();
                $message_template['username']   = $this->__loggedInUser['us_name'];
                $message_template['institute']  = $this->input->post('ib_institute_code').'-'.$this->input->post('ib_name');
                $triggered_activity             = 'institute_updated';
                log_activity($triggered_activity, $user_data, $message_template);
            }

            // echo '<pre>';print_r($institute); die();
            $data['title']          = lang('institutes');
            $breadcrumb             = array();
            $breadcrumb[]           = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
            $breadcrumb[]           = array( 'label' => lang('manage_institutes'), 'link' => admin_url('institutes'), '' => 'active', 'icon' => '' );
            $breadcrumb[]           = array( 'label' => $institute['ib_name'], 'link' => '', 'active' => 'active', 'icon' => '' );
            $data['breadcrumb']     = $breadcrumb;
            
            //processing institute location 
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
            $data['instituted_city']  = $this->Location_model->faculty_city(array('id' => $data['institute_data']['ib_native']));
            $data['institute_city']   = $this->Location_model->faculty_city(array('id' => $institute['ib_native']));
            $data['cities'] = array();
            if($data['institute_city'])
            {
                $data['cities'] = $this->Location_model->cities(array('state_id' => $data['institute_city']['state_id']));
            }
            //End
                
            $data['institute']              = $institute;
            $language_error         = '';
            $language_ids           = array();

            $this->load->library('form_validation');
            $this->form_validation->set_rules('ib_name', 'lang:name', 'required');
            $this->form_validation->set_rules('ib_institute_code', 'lang:institute_code', 'required|max_length[6]|callback_code_check['.$id.']');        
            $this->form_validation->set_rules('ib_phone', 'lang:phone', 'required');
            $this->form_validation->set_rules('ib_about', 'lang:about_institute', 'required');
            $this->form_validation->set_rules('ib_class_code', 'Institute Class Code', 'required|callback_class_code_check['.$id.']');

            if ($this->form_validation->run() == FALSE)
            {
                $data['institute']  = $institute;
                $data['error']      = validation_errors();
                $this->load->view($this->config->item('admin_folder').'/institute_form', $data);
            }
            else
            {
                if($this->Institute_model->update_institute($save))
                {
                    $this->load->model('Group_model');
                    $group                      = array();
                    $group['gp_institute_code'] = $save['ib_institute_code'];
                    $group['gp_institute_id']   = $save['id'];
                    $this->Group_model->update_institute_code($group);
                }

                $save_userdata                      = array();
                $save_userdata['us_institute_code'] = $save['ib_institute_code'];
                $filter_param                       = array();
                $filter_param['institute_id']       = $id; 
                $filter_param['update']             = true; 
                $this->User_model->save_userdata($save_userdata,$filter_param);               
                $template    = 'success';
                $message     = lang('institute_updated_success');
                if($language_error!='')
                {
                    $template  = 'error';
                    $message  .= '<br />'.$language_error;                
                }
                $this->memcache->delete('institute_'.$id);
                $this->memcache->delete('institutes');
                $this->session->set_flashdata($template, $message);            
                redirect(admin_url('institutes/institute/'.$id));
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
        $institute_id             = $this->input->post('institute_id');
        $institute                = $this->Institute_model->institute(array(
                                                                'id' => $institute_id,
                                                                'select' => 'id, ib_status,ib_institute_code,ib_name'
                                                            ));

        $save                   = array();
        $save['id']             = $institute_id;
        $save['ib_status']      = '1';
        $response['message']    = lang('activated');
        if($institute['ib_status'])
        {
            $institute['ib_status']   = '0';
            $response['message']    = lang('deactivated');
            $save['ib_status']      = '0';
        }
        
        if(!$this->Institute_model->update_institute($save))
        {
            $response['error']   = true;
            $response['message'] = lang('error_change_status');
        }
        else
        {
            /*Log creation*/
            $user_data                      = array();
            $user_data['user_id']           = $this->__loggedInUser['id'];
            $user_data['username']          = $this->__loggedInUser['us_name'];
            $user_data['useremail']          = $this->__loggedInUser['us_email'];
            $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
            $message_template               = array();
            $message_template['username']   = $this->__loggedInUser['us_name'];
            $message_template['institute']  = $institute['ib_institute_code'].'-'.$institute['ib_name'];
            if($save['ib_status'] == '0')
            {
                $triggered_activity         = 'institute_deactivated';
            } else {
                $triggered_activity         = 'institute_activated';  
            }
            log_activity($triggered_activity, $user_data, $message_template);

            $param                  = array();
            $param['us_institute_id'] = $institute_id;
            if($save['ib_status'] == '0')
            {
                $param['us_status']         = '0';
            }else{
                $param['us_status']         = '1';
            }
            
            $this->Institute_model->update_institute_users($param);

            if($save['ib_status'] == 1){
                $param                  = array();
                $param['us_institute_id'] = $institute_id;
                $param['us_role_id']    = '8';
                $param['us_status']     = '1';
                $this->Institute_model->update_institute_users($param);
            }

            $param             = array();
            $param['institute_id']  = $institute_id;
            $param['select']    = 'us_email, us_name, us_email_verified';
            $param['email_verified'] = '1';
            $users              = $this->Institute_model->get_user_details($param);

            foreach($users as $user)
            {
                $body  = 'Hi '.$user['us_name'].'<br />Account has been '. $response['message'] .' by admin.<br />';
                if($response['message'] == lang('deactivated'))
                {
                    $body .= 'Please contact admin for details.<br/>';
                }
                $this->ofabeemailer->send_mail(array('to' => array($user['us_email']), 'subject' => 'Account '.$response['message'], 'body' => $body));
            }
            
        }
        $this->memcache->delete('institutes');
        $this->memcache->delete('institute_'.$institute_id);
        $institute             = $this->Institute_model->institute(array('id' => $institute_id));
        $response['institute'] = $institute;
        echo json_encode($response);
    }
    
    function restore()
    {
        $response               = array();
        $response['error']      = false;
        $institute_id             = $this->input->post('institute_id');
        $institute                = $this->Institute_model->institute(array(
                                                                'id' => $institute_id,
                                                                'select' => 'id,ib_name,ib_institute_code'
                                                            ));

        $save                   = array();
        $save['id']             = $institute_id;
        $save['ib_deleted']     = '0';
        $save['ib_status']      = '0';
        $response['message']    = lang('restore_institute_success');
        if(!$this->Institute_model->update_institute($save))
        {
            $response['error']   = true;
            $response['message'] = lang('restore_institute_failed');
        }
        else
        {
            $param                  = array();
            $param['us_institute_id']  = $institute_id;
            $param['us_deleted']    = '0';
            $param['us_status']     = '0';
            $this->Institute_model->update_institute_users($param);
            /*Log creation*/
            $user_data                      = array();
            $user_data['user_id']           = $this->__loggedInUser['id'];
            $user_data['username']          = $this->__loggedInUser['us_name'];
            $user_data['useremail']          = $this->__loggedInUser['us_email'];
            $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
            $message_template               = array();
            $message_template['username']   = $this->__loggedInUser['us_name'];
            $message_template['institute']  = $institute['ib_institute_code'].'-'.$institute['ib_name'];
            $triggered_activity             = 'institute_restored';
            log_activity($triggered_activity, $user_data, $message_template);
        }   
        $institute             = $this->Institute_model->institute(array('id' => $institute_id));
        $this->memcache->delete('institutes');
        $this->memcache->delete('institute_'.$institute_id);
        
        $response['institute'] = $institute;
        echo json_encode($response);        
    }
    
    function delete()
    {
        $response               = array();
        $response['error']      = false;
        $institute_id           = $this->input->post('institute_id');
        $institute              = $this->Institute_model->institute(
                                                            array(
                                                                'id' => $institute_id,
                                                                'limit'=>'1',
                                                                'select' => 'id,ib_name,ib_institute_code,ib_head_name,ib_head_email'
                                                            ));
                                                            
        if( !$institute )
        {
            $response['error'] = true;
            $response['message'] = lang('institute_not_found');
            echo json_encode($response);exit;
        }
        $save                   = array();
        $save['id']             = $institute_id;
        $save['ib_deleted']     = '1';
        
        $response['message']    = lang('institute_delete_success');
        
        if(!$this->Institute_model->update_institute($save))
        {
            $response['error']   = true;
            $response['message'] = lang('delete_institute_failed');
        }
        else
        {
            /*Log creation*/
            $user_data                      = array();
            $user_data['user_id']           = $this->__loggedInUser['id'];
            $user_data['username']          = $this->__loggedInUser['us_name'];
            $user_data['useremail']          = $this->__loggedInUser['us_email'];
            $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
            $message_template               = array();
            $message_template['username']   = $this->__loggedInUser['us_name'];
            $message_template['institute']  = $institute['ib_institute_code'].'-'.$institute['ib_name'];
            $triggered_activity             = 'institute_deleted';
            log_activity($triggered_activity, $user_data, $message_template);

            $mail_params        = array();
            $notify_to          = array();
            if($institute['ib_head_email'])
            {
                $notify_to[]        = $institute['ib_head_email'];
            }
            $institute_admins   = $this->User_model->users(array( 'institute_id' => $institute_id, 'role_id' => '8', 'status' => '1', 'not_deleted' => true, 'select' => 'users.us_email, users.id'));
            if(!empty($institute_admins)) 
            {
                foreach($institute_admins as $i_admin)
                {
                    $notify_to[] = $i_admin['us_email'];
                }
            }    

            $param                      = array();
            $param['us_institute_id']   = $institute_id;
            $param['us_role_id']        = '8';
            $param['us_deleted']        = '1';
            $this->Institute_model->update_institute_users($param);

            $notify_to = array_unique($notify_to);
            if(!empty($notify_to)) 
            {
                $body               = 'Hi '.$institute['ib_head_name'].'<br />Institution named <b>'.$institute['ib_institute_code'].'-'.$institute['ib_name'].'</b> and accounts under this institution are deleted.<br />';
                $body               .= 'Please contact admin for details.<br/>';
                $param              = array();
                $param['to']        = $notify_to;
                $param['subject']   = 'Institute Deleted - '.$institute['ib_name'];
                $param['body']      = $body;
                $mail_params        = $param;
                $this->ofabeemailer->send_mail($mail_params);    
            }
            // if(sizeof($mail_params) > 0)
            // {
            //     $this->shoot_bulk_mail_curl_jobs($mail_params, site_url('cron_job/shoot_bulk_mail_with_unique_body'));
            // }
        }
        $this->memcache->delete('institutes');
        $this->memcache->delete('institute_'.$institute_id);
        $institute             = $this->Institute_model->institute(array('id' => $institute_id));
        
        $response['institute'] = $institute;
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
    
    function delete_institutes_bulk()
    {
        $institute_ids   = json_decode($this->input->post('institutes'));
        // echo '<pre>';print_r($institute_ids);die;
        if(!empty($institute_ids))
        {

            $param             = array();
            $param['us_institute_ids']  = $institute_ids;
            $param['select']    = 'us_email, us_name, us_email_verified';
            $param['email_verified'] = '1';
            $users              = $this->Institute_model->get_user_details($param);
            $mail_params        = array();
            foreach($users as $user)
            {
                $body  = 'Hi '.$user['us_name'].'<br />Account has been deleted by admin.<br />';
                $body .= 'Please contact admin for details.<br/>';
                $param              = array();
                $param['to']        = array($user['us_email']);
                $param['subject']   = 'Account Deleted';
                $param['body']      = $body;
                $mail_params[]      = $param;
            }

            $save                   = array();
            // $save['id']             = $institute_id;
            $save['ids']            = $institute_ids;
            $save['ib_deleted']     = '1';
            $this->Institute_model->update_institute($save);

            $param                  = array();
            $param['us_institute_ids']  = $institute_ids;
            $param['us_deleted']    = '1';
            $this->Institute_model->update_institute_users($param);

            if(sizeof($mail_params) > 0)
            {
                $this->shoot_bulk_mail_curl_jobs($mail_params, site_url('cron_job/shoot_bulk_mail_with_unique_body'));
            }
        }
        /*Log creation*/
        $delete_count                   = (sizeof($institute_ids)>1)?sizeof($institute_ids).' institutes': 'a institute';
        $user_data                      = array();
        $user_data['user_id']           = $this->__loggedInUser['id'];
        $user_data['username']          = $this->__loggedInUser['us_name'];
        $user_data['useremail']          = $this->__loggedInUser['us_email'];
        $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
        $message_template               = array();
        $message_template['username']   = $this->__loggedInUser['us_name'];
        $message_template['count']      = $delete_count;
        $triggered_activity             = 'institute_deleted_bulk';
        log_activity($triggered_activity, $user_data, $message_template);
        $data               = array();
        $data['institutes']  = $this->Institute_model->institutes(array('institute_ids' => $institute_ids));
        $this->memcache->delete('institutes');
        echo json_encode($data);
    }
    
    function change_status_bulk()
    {
        $status         = $this->input->post('status');
        $institute_ids    = json_decode($this->input->post('institutes'));
        foreach($institute_ids as $ins_id){
            $this->memcache->delete('institute_'.$ins_id);
        }
        $mail_params = array();
        if(!empty($institute_ids))
        {
            $param                      = array();
            $param['us_institute_ids']  = $institute_ids;                
            $param['us_status']         = $status;                
            $this->Institute_model->update_institute_users($param);
    
            $save                   = array();
            $save['ids']            = $institute_ids;
            $save['ib_status']      = $status;
            $this->Institute_model->update_institute($save);

            $param                      = array();
            $param['us_institute_ids']  = $institute_ids;
            $param['select']            = 'us_email, us_name, us_email_verified';
            $param['email_verified']    = '1';
            $users                      = $this->Institute_model->get_user_details($param);
            foreach($users as $user)
            {
                if($status == '0') 
                {
                    $subject = 'Account Deactivated';
                    $body  = 'Hi '.$user['us_name'].'<br />Account has been deactivated by admin.<br />';
                    $body .= 'Please contact admin for details.<br/>';
                }
                else
                {
                    $subject = 'Account Activated';
                    $body  = 'Hi '.$user['us_name'].'<br />Account has been activated by admin.<br />';
                    $body .= 'Please login to the site with your email id/phone and password.<br/>';
                }
                $param              = array();
                $param['to']        = array($user['us_email']);
                $param['subject']   = $subject;
                $param['body']      = $body;
                $mail_params[]      = $param;
            }

            if(sizeof($mail_params) > 0)
            {
                $this->shoot_bulk_mail_curl_jobs($mail_params, site_url('cron_job/shoot_bulk_mail_with_unique_body'));
            }
        }

        /*Log creation*/
        $institutes_count               = (sizeof($institute_ids)>1)?sizeof($institute_ids).' institutes': 'a institute';
        $user_data                      = array();
        $user_data['user_id']           = $this->__loggedInUser['id'];
        $user_data['username']          = $this->__loggedInUser['us_name'];
        $user_data['useremail']          = $this->__loggedInUser['us_email'];
        $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
        $message_template               = array();
        $message_template['username']   = $this->__loggedInUser['us_name'];
        $message_template['count']      = $institutes_count;
        if($status == '0') 
        {
            $triggered_activity         = 'institute_deactivated_bulk';
        } 
        else 
        {
            $triggered_activity         = 'institute_activated_bulk';
        }
        log_activity($triggered_activity, $user_data, $message_template);

        $data                   = array();
        $data['institutes']     = $this->Institute_model->institutes(array('institute_ids' => $institute_ids));
        //echo '<pre>'; print_r($data['institutes']);die;
        $this->memcache->delete('institutes');
        echo json_encode($data);
    }

    private function shoot_bulk_mail_curl_jobs($data = '', $target = '') {
        $data = json_encode($data);
        $curlHandle         = curl_init($target);
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
        curl_exec($curlHandle);
        curl_close($curlHandle);
    }

    function send_message()
    {
        $error              = 0;
        $response['error']  = false;
        $response['message'] = 'Message has been sent sucessfully';
        $subject        = $this->input->post('send_user_subject');
        $message        = base64_decode($this->input->post('send_user_message'));
        $institute_ids  = $this->input->post('institute_ids');
        $institute_ids  = json_decode($institute_ids);
        $admin_email_ids = array();
        $admin_emails        = $this->Institute_model->get_user_details(array(
                                            'us_institute_ids' => $institute_ids,
                                            'select' => 'users.id as user_id, us_email, us_name, us_email_verified',
                                            'role_id' => '8',
                                            'email_verified' => '1'
                                        ));
        // print_r($admin_emails);
        // die();
        if(!empty($admin_emails))
        {
            $system_message     = array();
            $random_message_id  = rand(1000, 9999);
            $date_time          = date(DateTime::ISO8601);

            foreach($admin_emails as $admin_email)
            {
                $system_message[] = array(
                    "messageId" => $random_message_id,
                    "senderId" => $this->__loggedInUser['id'],
                    "senderName" => $this->__loggedInUser['us_name'],
                    "senderImage" => user_path().$this->__loggedInUser['us_image'],
                    "receiverId" => $admin_email['user_id'],
                    "message" => $message,
                    "datetime" => $date_time
                );

                $admin_email_ids[] = $admin_email['us_email'];
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
            $param['to']                            = $admin_email_ids;
            $param['strictly_to_recepient']         = true;
            $send = $this->ofabeemailer->send_mail($param);
        }  

        echo json_encode($response);
    }

    function upload_user_image()
    {
        $response               = array();
        $response['error']      = 'false';
        $response['message']    = lang('institute_image_saved');
        $institute_id             = $this->input->post('id');
        $directory              = institute_upload_path();
        if(!file_exists($directory)){
            mkdir($directory, 0777, true);
        }
        $config                     = array();
        $config['upload_path']      =  $directory;
        $config['allowed_types']    = 'gif|jpg|png|jpeg';
        $config['encrypt_name']     = true;
        $this->load->library('upload');
        $this->upload->initialize($config);
        $upload = $this->upload->do_upload('file');
        if($upload)
        {
            $uploaded_data = $this->upload->data();
            $save                   = array();
            $save['id']             = $institute_id;
            $save['ib_image']       = $uploaded_data['raw_name'].'.jpg'."?v=".rand(10,1000);;
            $this->Institute_model->update_institute($save);
            $new_file               = $this->crop_image($uploaded_data);

            // $has_s3     = $this->settings->setting('has_s3');
            // if( $has_s3['as_superadmin_value'] && $has_s3['as_siteadmin_value'] )
            // {
            //     uploadToS3(institute_upload_path().$new_file, institute_upload_path().$new_file);
            // }
            $response['user_image']      = institute_path().$new_file;
        }
        else
        {
            $response['error']      = 'true';
            $response['message']    = 'Error in updating institute image';   
            $response['error_msg']  =  $this->upload->display_errors();
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
        $directory          = institute_upload_path();
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

        if(!empty($languages))
        {
            foreach($languages as $language)
            {
                if($language)
                {
                    $lang_object = $this->Course_model->course_language(array('language_name'=> $language));
                    if($lang_object)
                    {
                        if(in_array($lang_object['id'], $language_ids)){
                            $language_error .= 'You have already added the language <b>'.$language.'</b><br />';
                        }
                        $language_ids[] = $lang_object['id'];
                    }
                    else
                    {
                        $language_error .= 'Invalid language <b>'.$language.'</b>.<br />';
                    }
                }
            }

            echo $language_error;
        }
    }

    function import_institutes()
    {
        $directory              = $this->config->item('upload_folder').'/institute/'.$this->config->item('acct_domain');
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
        $active_academic_year = $this->Course_model->get_active_academic_year();    
        $academic_year_id     = $active_academic_year['id'];
        $academic_year_code   = $active_academic_year['ay_year_code'];

        if($uploaded)
        {
            $upload_data        =  $this->upload->data();
            $file               = $upload_data['full_path'];
            $file               = fopen($file, "r") or die(json_encode(array('status' => 3, 'message' => 'Unable to open file!')));
            $header             = fgetcsv($file);
            $template_header    = array("institute_name", "institute_code", "admin_email", "admin_password", "institute_address", "institute_phone", "institute_head_name", "institute_head_email", "institute_head_phone", "institute_officer_name", "institute_officer_email", "institute_officer_phone", "institute_about", "institute_class_code");
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
            $institute_objects  = array();
            $input_email_ids    = array();
            $input_classes      = array();
            $input_codes        = array();
            $duplicate_emails   = array();
            $duplicate_classes  = array();
            $duplicate_codes    = array();
            $email_content      = array();
            $save_institutes    = array();
            $admin_passwords    = array();
            $affected_rows      = array();

            while (($institute = fgetcsv($file, 10000, ",")) !== FALSE)
            {
                if(empty(array_filter($institute)))
                {
                    $row++;
                    continue;
                }

                $institute[$admin_email]    = strip_tags($institute[$admin_email]);
                $input_email_ids[]          = $institute[$admin_email];
                $institute_objects[]        = $institute;
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

                    $classes = $this->Institute_model->institutes_by_columns(array('select' => 'ib_class_code', 'classes' => $input_classes));
                    if(sizeof($classes) > 0 )
                    {
                        foreach($classes as $object)
                        {
                            $duplicate_classes[] = $object['ib_class_code'];
                        }    
                    }

                    $codes = $this->Institute_model->institutes_by_columns(array('select' => 'ib_institute_code', 'codes' => $input_codes));
                    if(sizeof($codes) > 0 )
                    {
                        foreach($codes as $object)
                        {
                            $duplicate_codes[] = $object['ib_institute_code'];
                        }    
                    }
                    $input_email_ids    = array();
                    $input_classes      = array();
                    $input_codes        = array();
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
            if(sizeof($input_classes) > 0 )
            {
                $classes = $this->Institute_model->institutes_by_columns(array('select' => 'ib_class_code', 'classes' => $input_classes));
                if(sizeof($classes) > 0 )
                {
                    foreach($classes as $object)
                    {
                        $duplicate_classes[] = $object['ib_class_code'];
                    }    
                }
            }
            if(sizeof($input_codes) > 0 )
            {
                $codes = $this->Institute_model->institutes_by_columns(array('select' => 'ib_institute_code', 'codes' => $input_codes));
                if(sizeof($codes) > 0 )
                {
                    foreach($codes as $object)
                    {
                        $duplicate_codes[] = $object['ib_institute_code'];
                    }    
                }
            }

            if(empty($institute_objects))
            {
                echo json_encode(array('status' => 3, 'message' => 'Template is empty')); die();
            }


            foreach($institute_objects as $row => $institute_obj)
            { 
                foreach ($template_header as $h_key => $h_value) 
                {
                    $institute_obj[$$h_value] = trim($institute_obj[$$h_value]);
                }

                $defective_columns              = array();
                $defect_reason                  = array();

                if($institute_obj[$institute_name] == '')
                {
                    $defective_columns[] = $institute_name;
                } 
                else 
                {
                    if(strlen($institute_obj[$institute_name]) > 50){
                        $defective_columns[] = $institute_name;
                        $defect_reason[$institute_name] = 'Exceeded limit';
                    }
                }
                if($institute_obj[$institute_code] == '')
                {
                    $defective_columns[] = $institute_code;
                } 
                else 
                {
                    if(strlen($institute_obj[$institute_code]) > 6){
                        $defective_columns[] = $institute_code;
                        $defect_reason[$institute_code] = 'Exceeded limit';
                    }
                }

                if($institute_obj[$institute_class_code] == '')
                {
                    $defective_columns[] = $institute_class_code;
                }
                else 
                {
                    if(strlen($institute_obj[$institute_class_code]) > 10){
                        $defective_columns[] = $institute_class_code;
                        $defect_reason[$institute_class_code] = 'Exceeded limit';
                    }
                }

                if($institute_obj[$admin_email] == '')
                {
                    $defective_columns[] = $admin_email;
                }
                else
                {
                    if(filter_var($institute_obj[$admin_email], FILTER_VALIDATE_EMAIL) === FALSE)
                    {
                        $defective_columns[] = $admin_email;
                    }    
                }
                if($institute_obj[$admin_password] == '')
                {
                    $defective_columns[] = $admin_password;
                }
                else 
                {
                    if( strlen($institute_obj[$admin_password]) < 6 ) {
                        $defective_columns[] = $admin_password;
                        $defect_reason[$admin_password] = 'Weak password';
                    }
                }
                if($institute_obj[$institute_phone])
                { 
                    $institute_obj_phone = str_replace(' ', '',$institute_obj[$institute_phone]);
                    if(!is_numeric($institute_obj_phone) || strlen($institute_obj_phone) > 11 || strlen($institute_obj_phone) < 10)
                    {
                        $defective_columns[] = $institute_phone;
                        $defect_reason[$institute_phone] = 'Invalid Number';
                    }
                }else{
                        $defective_columns[] = $institute_phone;
                        $defect_reason[$institute_phone] = 'Institute Phone number required';
                }

                if($institute_obj[$institute_head_phone])
                {
                    $institute_obj_head_phone = str_replace(' ', '',$institute_obj[$institute_head_phone]);
                    if(!is_numeric($institute_obj_head_phone) || strlen($institute_obj_head_phone) > 11 || strlen($institute_obj_head_phone) < 10)
                    {
                        $defective_columns[] = $institute_head_phone;
                        $defect_reason[$institute_head_phone] = 'Invalid Number';
                    }
                }else{
                    $defective_columns[] = $institute_head_phone;
                    $defect_reason[$institute_head_phone] = 'Head Phone number required';
                }

                if($institute_obj[$institute_officer_phone])
                {
                    $institute_obj_officer_phone = str_replace(' ', '',$institute_obj[$institute_officer_phone]);
                    if(!is_numeric($institute_obj_officer_phone) || strlen($institute_obj_officer_phone) > 11  || strlen($institute_obj_officer_phone) < 10)
                    {
                        $defective_columns[] = $institute_officer_phone;
                        $defect_reason[$institute_officer_phone] = 'Invalid Number';
                    }
                }
//echo '<pre>'; print_r($defect_reason); print_r($defective_columns); print_r($institute_obj);   
//die;
                if(filter_var($institute_obj[$institute_head_email], FILTER_VALIDATE_EMAIL) === FALSE)
                {
                    $defective_columns[] = $institute_head_email;
                }
                if($institute_obj[$institute_officer_email])
                {
                    if(filter_var($institute_obj[$institute_officer_email], FILTER_VALIDATE_EMAIL) === FALSE)
                    {
                        $defective_columns[] = $institute_officer_email;
                    }
                }

                if(sizeof($defective_columns) == 0)
                {
                    if(in_array($institute_obj[$admin_email], $duplicate_emails))
                    {
                        $defective_columns[] = $admin_email;
                    }

                    if(in_array($institute_obj[$institute_code], $duplicate_codes))
                    {
                        $defective_columns[] = $institute_code;
                    }

                    if(in_array($institute_obj[$institute_class_code], $duplicate_classes))
                    {
                        $defective_columns[] = $institute_class_code;
                    }

                    if(sizeof($defective_columns) == 0)
                    {
                        $institute                          = array();
                        $institute['ib_name']               = $institute_obj[$institute_name];
                        $institute['ib_institute_code']     = $institute_obj[$institute_code];
                        $institute['ib_address']            = $institute_obj[$institute_address];
                        $institute['ib_phone']              = $institute_obj[$institute_phone];
                        $institute['ib_head_name']          = $institute_obj[$institute_head_name];
                        $institute['ib_head_email']         = $institute_obj[$institute_head_email];
                        $institute['ib_head_phone']         = $institute_obj[$institute_head_phone];
                        $institute['ib_officer_name']       = $institute_obj[$institute_officer_name];
                        $institute['ib_officer_email']      = $institute_obj[$institute_officer_email];
                        $institute['ib_officer_phone']      = $institute_obj[$institute_officer_phone];
                        $institute['ib_class_code']         = $institute_obj[$institute_class_code];
                        $institute['ib_about']              = $institute_obj[$institute_about];

                        $user                               = array();
                        $user['id']                         = false;
                        $user['us_name']                    = 'Admin - '.$institute_obj[$institute_name];
                        $user['us_email']                   = $institute_obj[$admin_email];
                        $password                           = $institute_obj[$admin_password];
                        $user['us_password']                = sha1($password);
                        $user['us_role_id']                 = '8';
                        $user['us_institute_id']            = '0';
                        $user['us_institute_code']          = $institute_obj[$institute_code];
                        $user['us_status']                  = '1';
                        $user['us_email_verified']          = '1';
                        $user['action_id']                  = '1'; 
                        $user['action_by']                  = $this->auth->get_current_admin('id');
                        $user['us_account_id']              = $this->config->item('id');

                        $params                 = array();
                        $params['institute']    = $institute;
                        $params['user']         = $user;
                        // $response['id']         = $this->Institute_model->save_institute($params);
                        $save_institutes[]      = $params;
                        $admin_passwords[sha1($user['us_email'])] = $password;          

                        $success_rows++;
                    } 
                    else
                    {
                        $affected_rows[$row] = array('defect_columns' => $defective_columns ,  'row' => $institute_obj, 'row_number' => $row, 'type' => 'duplicate_data_row' );
                    }
                }
                else
                {
                    $affected_rows[$row] = array( 'defect_columns' => $defective_columns, 'row' => $institute_obj, 'row_number' => $row, 'type' => 'invalid_data_row', 'defect_reason' => $defect_reason );
                }
                $row++;
            }
            fclose($file);

            if(!empty($save_institutes))
            {
                $institutes      = $this->Institute_model->insert_institutes_bulk($save_institutes);
                $courses         = $this->Course_model->course_for_consolidation();
                $consolidations  = array();
                
                
                foreach($institutes as $institute)
                {
                    $this->memcache->delete('institute_'.$institute['id']);
                    if(!empty($courses))
                    {
                        foreach($courses as $course)
                        {
                            $consolidations[] = array( 
                                                        'id' => false,
                                                        'ccr_course_id' => $course['id'],
                                                        'ccr_institute_id' => $institute['id'],
                                                        'ccr_total_enrolled' => 0,
                                                        'ccr_total_completed' => 0,
                                                        'ccr_academic_year_id' => $academic_year_id,
                                                        'ccr_academic_year_code' => $academic_year_code,
                                                        'ccr_account_id'            => config_item('id')
                                                    );
                        }
                    }
    
                    // $user                           = $institute['user'];
                    $new_email_param                = array();
                    $new_email_param['email']       = $institute['us_email'];
                    $new_email_param['contents']    = array(
                                                        'user_name' => $institute['us_name']
                                                        ,'site_name' => config_item('site_name') 
                                                        ,'email_id' => $institute['us_email']
                                                        ,'password' => $admin_passwords[sha1($institute['us_email'])]
                                                        ,'site_url_login' => site_url('login')
                                                      );
                    $email_content[]                = $new_email_param;
                }
            }
            $this->memcache->delete('institutes');
            if(!empty($consolidations))
            {
                $this->Course_model->save_consolidation($consolidations);
            }

            if( sizeof($email_content) > 0 )
            {
                 $this->process_bulk_mail($email_content,'institute_welcome_mail');
            }

            $response               = array();
            $response['status']     = 1;
            $response['message']    = '';
            $message                = array();
            $status                 = 1; // 1 => succees, 2 => warning, 3 => error

            if( $success_rows > 0 )
            {
                $message[] = (($success_rows>1)?(' '.$success_rows.' Institutes'):'1 Institute').' imported successfully';
            }

            $total_affected = sizeof($affected_rows);
            if( $total_affected > 0)
            {
                /*Log creation*/
                $imported_count                 = ($total_affected>1)?$total_affected.' institutes': 'a institute';
                $user_data                      = array();
                $user_data['user_id']           = $this->__loggedInUser['id'];
                $user_data['username']          = $this->__loggedInUser['us_name'];
                $user_data['useremail']          = $this->__loggedInUser['us_email'];
                $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
                $message_template               = array();
                $message_template['username']   = $this->__loggedInUser['us_name'];
                $message_template['count']      = $imported_count;
                $triggered_activity             = 'institutes_imported';
                log_activity($triggered_activity, $user_data, $message_template);

                $memcache_index = ('insimprt'.$this->__loggedInUser['id']);
                $response['redirect_url'] = admin_url('institutes/preview').$memcache_index;
                $status = 2;
                $message[] = 'We found some problem with the csv you uploaded. We are redirecting you to preview page.';

                $preview_contents                   = array();
                $preview_contents['headers']        = $template_header;
                $preview_contents['content']        = $affected_rows;
                $preview_contents['total_rows']     = $row;
                $preview_contents['inserted']       = $success_rows;
                $preview_contents['failed']         = $total_affected;
                $preview_contents['back_to_home']   = admin_url('institutes');
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
            redirect(admin_url('institutes'));
        }
        $institutes_content = $this->memcache->get(array('key' => $key));
        if(empty($institutes_content['content']))
        {
            redirect(admin_url('institutes'));
        }

        $data                 = array();
        $affected_rows        = array();
        $data['excell']       = $institutes_content;
        $data['import_key']   = $key;
        $template_header      = $data['excell']['headers'];
        $data['action']       = admin_url('institutes/preview').$key;
        $this->load->library('form_validation');
        $active_academic_year = $this->Course_model->get_active_academic_year();    
        $academic_year_id     = $active_academic_year['id'];
        $academic_year_code   = $active_academic_year['ay_year_code'];
        if ($this->input->server('REQUEST_METHOD') != 'POST')
        {
            $this->load->view($this->config->item('admin_folder').'/import_preview', $data);
        }
        else
        {
            $preview_data       = $this->input->post('preview_data');
            $institutes         = json_decode($preview_data, true);
            $row                = 0;
            $success_rows       = 0;
            $input_buffer_size  = 200;
            $column_dropdown    = array();
            $institute_objects  = array();
            $input_email_ids    = array();
            $input_classes      = array();
            $input_codes        = array();
            $duplicate_emails   = array();
            $duplicate_classes  = array();
            $duplicate_codes    = array();
            $save_institutes    = array();
            $admin_passwords    = array();
            $email_content      = array();

            foreach ($institutes_content['headers'] as $h_key => $h_value) 
            {
                $$h_value = strip_tags($h_key);
            }
            foreach($institutes as $row => $institute)
            {
                if(empty(array_filter($institute)))
                {
                    continue;
                }
                $institute[$admin_email]    = strip_tags($institute[$admin_email]);
                $input_email_ids[]          = $institute[$admin_email];
                $institute_objects[]        = $institute;
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

                    $classes = $this->Institute_model->institutes_by_columns(array('select' => 'ib_class_code', 'classes' => $input_classes));
                    if(sizeof($classes) > 0 )
                    {
                        foreach($classes as $object)
                        {
                            $duplicate_classes[] = $object['ib_class_code'];
                        }    
                    }

                    $codes = $this->Institute_model->institutes_by_columns(array('select' => 'ib_institute_code', 'codes' => $input_codes));
                    if(sizeof($codes) > 0 )
                    {
                        foreach($codes as $object)
                        {
                            $duplicate_codes[] = $object['ib_institute_code'];
                        }    
                    }
                    $input_email_ids    = array();
                    $input_classes      = array();
                    $input_codes        = array();
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
            if(sizeof($input_classes) > 0 )
            {
                $classes = $this->Institute_model->institutes_by_columns(array('select' => 'ib_class_code', 'classes' => $input_classes));
                if(sizeof($classes) > 0 )
                {
                    foreach($classes as $object)
                    {
                        $duplicate_classes[] = $object['ib_class_code'];
                    }    
                }
            }
            if(sizeof($input_codes) > 0 )
            {
                $codes = $this->Institute_model->institutes_by_columns(array('select' => 'ib_institute_code', 'codes' => $input_codes));
                if(sizeof($codes) > 0 )
                {
                    foreach($codes as $object)
                    {
                        $duplicate_codes[] = $object['ib_institute_code'];
                    }    
                }
            }

            if(empty($institute_objects))
            {
                redirect(admin_url('institutes'));
            }

            foreach($institute_objects as $row => $institute_obj)
            { 
                foreach ($template_header as $h_key => $h_value) 
                {
                    $institute_obj[$$h_value] = trim($institute_obj[$$h_value]);
                }

                $defective_columns              = array();
                $defect_reason                  = array();

                if($institute_obj[$institute_name] == '')
                {
                    $defective_columns[] = $institute_name;
                } 
                else if (preg_match('/[\'^£$%*()}{@#~?><>,|=_+¬-]/', $institute_obj[$institute_name]))
                {
                    $defective_columns[]            = $institute_name;
                    $defect_reason[$institute_name] = 'No Special Characters allowed';
                }
                else 
                {
                    if(strlen($institute_obj[$institute_name]) > 50){
                        $defective_columns[] = $institute_name;
                        $defect_reason[$institute_name] = 'Exceeded limit';
                    }
                }
                if($institute_obj[$institute_code] == '')
                {
                    $defective_columns[] = $institute_code;
                } 
                else 
                {
                    if(strlen($institute_obj[$institute_code]) > 6){
                        $defective_columns[] = $institute_code;
                        $defect_reason[$institute_code] = 'Exceeded limit';
                    }
                }

                if($institute_obj[$institute_class_code] == '')
                {
                    $defective_columns[] = $institute_class_code;
                }
                else 
                {
                    if(strlen($institute_obj[$institute_class_code]) > 10){
                        $defective_columns[] = $institute_class_code;
                        $defect_reason[$institute_class_code] = 'Exceeded limit';
                    }
                }

                if($institute_obj[$admin_email] == '')
                {
                    $defective_columns[] = $admin_email;
                }
                else
                {
                    if(filter_var($institute_obj[$admin_email], FILTER_VALIDATE_EMAIL) === FALSE)
                    {
                        $defective_columns[] = $admin_email;
                    }    
                }
                if($institute_obj[$admin_password] == '')
                {
                    $defective_columns[] = $admin_password;
                }
                else 
                {
                    if( strlen($institute_obj[$admin_password]) < 6 ) {
                        $defective_columns[] = $admin_password;
                        $defect_reason[$admin_password] = 'Weak password';
                    }
                }

                if($institute_obj[$institute_phone])
                { 
                    $institute_obj_phone = str_replace(' ', '',$institute_obj[$institute_phone]);
                    if(!is_numeric($institute_obj_phone) || strlen($institute_obj_phone) > 11 || strlen($institute_obj_phone) < 10)
                    {
                        $defective_columns[] = $institute_phone;
                        $defect_reason[$institute_phone] = 'Invalid Number';
                    }
                }else{
                        $defective_columns[] = $institute_phone;
                        $defect_reason[$institute_phone] = 'Institute Phone number required';
                }

                if($institute_obj[$institute_head_phone])
                {
                    $institute_obj_head_phone = str_replace(' ', '',$institute_obj[$institute_head_phone]);
                    if(!is_numeric($institute_obj_head_phone) || strlen($institute_obj_head_phone) > 11 || strlen($institute_obj_head_phone) < 10)
                    {
                        $defective_columns[] = $institute_head_phone;
                        $defect_reason[$institute_head_phone] = 'Invalid Number';
                    }
                }else{
                    $defective_columns[] = $institute_head_phone;
                    $defect_reason[$institute_head_phone] = 'Head Phone number required';
                }

                if($institute_obj[$institute_officer_phone])
                {
                    $institute_obj_officer_phone = str_replace(' ', '',$institute_obj[$institute_officer_phone]);
                    if(!is_numeric($institute_obj_officer_phone) || strlen($institute_obj_officer_phone) > 11  || strlen($institute_obj_officer_phone) < 10)
                    {
                        $defective_columns[] = $institute_officer_phone;
                        $defect_reason[$institute_officer_phone] = 'Invalid Number';
                    }
                }

                if(filter_var($institute_obj[$institute_head_email], FILTER_VALIDATE_EMAIL) === FALSE)
                {
                    $defective_columns[] = $institute_head_email;
                }
                
                if($institute_obj[$institute_officer_email])
                {
                    if(filter_var($institute_obj[$institute_officer_email], FILTER_VALIDATE_EMAIL) === FALSE)
                    {
                        $defective_columns[] = $institute_officer_email;
                    }
                }
                
                if(sizeof($defective_columns) == 0)
                {
                    if(in_array($institute_obj[$admin_email], $duplicate_emails))
                    {
                        $defective_columns[] = $admin_email;
                    }

                    if(in_array($institute_obj[$institute_code], $duplicate_codes))
                    {
                        $defective_columns[] = $institute_code;
                    }

                    if(in_array($institute_obj[$institute_class_code], $duplicate_classes))
                    {
                        $defective_columns[] = $institute_class_code;
                    }

                    if(sizeof($defective_columns) == 0)
                    {
                        $institute                          = array();
                        $institute['ib_name']               = $institute_obj[$institute_name];
                        $institute['ib_institute_code']     = $institute_obj[$institute_code];
                        $institute['ib_address']            = $institute_obj[$institute_address];
                        $institute['ib_phone']              = $institute_obj[$institute_phone];
                        $institute['ib_head_name']          = $institute_obj[$institute_head_name];
                        $institute['ib_head_email']         = $institute_obj[$institute_head_email];
                        $institute['ib_head_phone']         = $institute_obj[$institute_head_phone];
                        $institute['ib_officer_name']       = $institute_obj[$institute_officer_name];
                        $institute['ib_officer_email']      = $institute_obj[$institute_officer_email];
                        $institute['ib_officer_phone']      = $institute_obj[$institute_officer_phone];
                        $institute['ib_class_code']         = $institute_obj[$institute_class_code];
                        $institute['ib_about']              = $institute_obj[$institute_about];

                        $user                               = array();
                        $user['id']                         = false;
                        $user['us_name']                    = 'Admin - '.$institute_obj[$institute_name];
                        $user['us_email']                   = $institute_obj[$admin_email];
                        $password                           = $institute_obj[$admin_password];
                        $user['us_password']                = sha1($password);
                        $user['us_role_id']                 = '8';
                        $user['us_institute_id']            = '0';
                        $user['us_institute_code']          = $institute_obj[$institute_code];
                        $user['us_status']                  = '1';
                        $user['us_email_verified']          = '1';
                        $user['action_id']                  = '1'; 
                        $user['action_by']                  = $this->auth->get_current_admin('id');
                        $user['us_account_id']              = $this->config->item('id');

                        $params                 = array();
                        $params['institute']    = $institute;
                        $params['user']         = $user;
                        // $response['id']         = $this->Institute_model->save_institute($params);
                        $save_institutes[]      = $params;
                        $admin_passwords[sha1($user['us_email'])] = $password;          

                        $success_rows++;
                    } 
                    else
                    {
                        $affected_rows[$row] = array('defect_columns' => $defective_columns ,  'row' => $institute_obj, 'row_number' => $row, 'type' => 'duplicate_data_row' );
                    }
                }
                else
                {
                    $affected_rows[$row] = array( 'defect_columns' => $defective_columns, 'row' => $institute_obj, 'row_number' => $row, 'type' => 'invalid_data_row', 'defect_reason' => $defect_reason );
                }
            }
            
            if(!empty($save_institutes))
            {
                $courses         = $this->Course_model->course_for_consolidation();
                $consolidations  = array();
                $institutes      = $this->Institute_model->insert_institutes_bulk($save_institutes);
                
                foreach($institutes as $institute)
                {
                    $this->memcache->delete('institute_'.$institute['id']);
                    if(!empty($courses))
                    {
                        foreach($courses as $course)
                        {
                            $consolidations[] = array( 
                                                        'id' => false,
                                                        'ccr_course_id' => $course['id'],
                                                        'ccr_institute_id' => $institute['id'],
                                                        'ccr_total_enrolled' => 0,
                                                        'ccr_total_completed' => 0,
                                                        'ccr_academic_year_id' => $academic_year_id,
                                                        'ccr_academic_year_code' => $academic_year_code,
                                                        'ccr_account_id'            => config_item('id')
                                                    );
                        }
                    }
                    
                    // $user                           = $institute['user'];
                    $new_email_param                = array();
                    $new_email_param['email']       = $institute['us_email'];
                    $new_email_param['contents']    = array(
                                                        'user_name' => $institute['us_name']
                                                        ,'site_name' => config_item('site_name') 
                                                        ,'email_id' => $institute['us_email']
                                                        ,'password' => $admin_passwords[sha1($institute['us_email'])]
                                                        ,'site_url_login' => site_url('login')
                                                      );
                    $email_content[]                = $new_email_param;
                }  
                $this->memcache->delete('institutes');  
            }
            
            if(!empty($consolidations))
            {
                $this->Course_model->save_consolidation($consolidations);
            }

            if( sizeof($email_content) > 0 )
            {
                 $this->process_bulk_mail($email_content,'institute_welcome_mail');
            }

            $total_affected = sizeof($affected_rows);
            if( $total_affected > 0 )
            {
                $memcache_index                     = $key;
                $redirect_url                       = admin_url('institutes/preview').$memcache_index;
                $preview_contents                   = array();
                $preview_contents['headers']        = $institutes_content['headers'];
                $preview_contents['content']        = $affected_rows;
                $preview_contents['total_rows']     = $row;
                $preview_contents['inserted']       = $success_rows;
                $preview_contents['failed']         = $total_affected;
                $preview_contents['back_to_home']   = admin_url('institutes');
                $this->memcache->set($memcache_index, $preview_contents);
                redirect($redirect_url);                    
            }
            else
            {
                $memcache_index = $key;
                $this->memcache->delete($memcache_index);

                $response               = array();
                $response['success']    = true;
                $response['message']    = 'Institutes Imported Successfully';
                $this->session->set_flashdata('popup',$response);
                redirect(admin_url('institutes'));
            }
        }
    }

    function export_preview($key=false)
    {
        if(!$key)
        {
            redirect(admin_url('institutes'));
        }
        $key                    = base64_decode($key);
        $institutes_content     = ($this->memcache->get(array('key' => $key)))?$this->memcache->get(array('key' => $key)):array();
        if(!isset($institutes_content['content']))
        {
            redirect(admin_url('institutes'));
        }
        $data                   = array();
        $data['institutes']     = $institutes_content['content'];
        $this->load->view($this->config->item('admin_folder').'/export_institutes_import', $data);
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

    function export_institutes($param = false)
    {
        if(!$param)
        {
            redirect(admin_url('institutes'));
        }

        $param = json_decode(base64_decode($param), true);
        if(json_last_error() != JSON_ERROR_NONE)
        {
            redirect(admin_url('institutes'));
        }

        $data               = array();
        $data['institutes'] = array();
        $keyword            = isset($param['keyword'])?strtolower($param['keyword']):'';
        $filter             = isset($param['filter'])?$param['filter']:'all';
        
        
        $param                   = array();
        $param['keyword']        = trim($keyword);
        $param['filter']         = $filter;
        $data['institutes']      = $this->Institute_model->institutes($param);
        
        $this->load->view(config_item('admin_folder').'/export_institute', $data);    

    }
}
?>
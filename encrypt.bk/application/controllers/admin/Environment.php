<?php
class Environment extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $skip_login                 = array('upload_certificate');
        $this->__loggedInUser   = $this->auth->get_current_user_session('admin');
        if (!$this->__loggedInUser)
        {
            redirect('login');
        }
        // Check whether super admin
        if(!in_array($this->__loggedInUser['id'], array(config_item('super_admin')))  && !in_array($this->router->fetch_method(), $skip_login))
        {
            redirect('login');           
        }
        $this->actions        = $this->config->item('actions');
        $this->load->model(array('Settings_model', 'Category_model', 'Course_model', 'Challenge_model','Generate_test_model'));
        $this->lang->load('environment');
        $this->event_privilege            = $this->accesspermission->get_permission(array('role_id' => $this->__loggedInUser['role_id'],'module' => 'event'));
    }

    function index()
    {
        $this->load->helper('form');
        $data               = array();
        $data['title']      = lang('settings');
        $breadcrumb         = array();
        $breadcrumb[]       = array( 'label' => 'Home', 'link' => site_url('/admin'), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]       = array( 'label' => lang('settings'), 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb'] = $breadcrumb;
        //$data['settings_website']   = $this->Settings_model->get_settings_id('29');
        $data['banners']        = $this->Settings_model->get_banners();
        $data['mobile_banners'] = $this->Settings_model->get_mobile_banners();
        $data['certificates']   = $this->Settings_model->get_certificate();
        //echo "<pre>";print_r($data['certificates']);die;
        $data['support']        = $this->Settings_model->get_support_chat_data();
        $data['testimonials']   = $this->Settings_model->get_testimonials();
        $data['s3_setting_web'] = $this->settings->setting('website');
        //echo '<pre>'; print_r($data['s3_setting_web']); die;
        $data['drop_setting']   = $this->settings->setting('has_dropbox');
        $data['fb_setting']     = $this->settings->setting('has_facebook');
        $data['social_links']   = $this->settings->setting('social_links');

        $data['s3_account']     = $this->settings->setting('has_s3');

        $data['cdn_url']        = $this->settings->setting('has_cdn');
        $data['gst_setting']    = $this->settings->setting('has_tax');
        //$data['gateway_setting']    = $this->settings->setting('has_payment');
       
        //Mail subscription
        $data['mail_subscription'] = $this->settings->setting('has_mail_subscription');
        $data['zoho']              = $this->settings->setting('has_zoho');
        $data['mailchimp']         = $this->settings->setting('has_mailchimp');
        $data['content_security']  = $this->settings->setting('has_content_security'); 
        $data['restrictUser']      = $this->settings->setting('restrict_user_login'); 
        //print $this->db->last_query(); die;
        //echo '<pre>';print_r($data['restrictUser']);die();
        //mail subscription end

        /* Analytics Start */
        $data['testimonial_home_count'] = '6';
        $data['google_analytics']       = $this->settings->setting('has_google_analytics');

        //echo '<pre>';print_r($data['google_analytics']['as_setting_value']['setting_value']);die;

        /* Analytics End */

        $data['has_cdn']        = false;
        if( $data['cdn_url']['as_superadmin_value'] && $data['cdn_url']['as_siteadmin_value'] )
        {
            $data['has_cdn']    = true;
        }

        $data['s3_mail_account']     = $this->settings->setting('has_mail');
        $data['smtp_account']        = $this->settings->setting('has_smtp');

        $data['has_s3_mail']         = false;
        if( $data['s3_mail_account']['as_superadmin_value'] && $data['s3_mail_account']['as_siteadmin_value'] )
        {
            $data['has_s3_mail']     = true;
        }

        $data['has_s3']         = false;
        if( $data['s3_account']['as_superadmin_value'] && $data['s3_account']['as_siteadmin_value'] )
        {
            $data['has_s3']     = true;
        }

        $data['has_smtp']       = false;
        if( $data['smtp_account']['as_superadmin_value'] && $data['smtp_account']['as_siteadmin_value'] )
        {
            $data['has_smtp']   = true;
        }

        $data['has_facebook']         = false;
        if( $data['fb_setting']['as_superadmin_value'] && $data['fb_setting']['as_siteadmin_value'] )
        {
            $data['has_facebook']     = true;
        }

        $data['has_dropbox']         = false;
        if( $data['drop_setting']['as_superadmin_value'] && $data['drop_setting']['as_siteadmin_value'] )
        {
            $data['has_dropbox']     = true;
        }
        
        $profile_blocks          = $this->Settings_model->blocks();
        $data['profile_blocks']  = array();
        if(!empty($profile_blocks))
        {
            foreach ($profile_blocks as $profile_block)
            {
                $profile_block['profile_fields']              = $this->Settings_model->profile_fields(array('block_id' => $profile_block['id']));
                $data['profile_blocks'][$profile_block['id']] = $profile_block;
            }
        }
        
        $data['course_categories']  = $this->Category_model->categories(array('not_deleted'=>true));
        
        $data['question_categories']  = $this->Category_model->question_categories(array('status'=>'1', 'not_deleted'=>true));
        foreach($data['question_categories'] as $key => $q_cat){
            $data['question_categories'][$key]['count'] = $this->Category_model->ques_count(array('topic_id'=>$q_cat['id']));
        }

        /*payment settings start*/
        $payment_setting_key        = $this->settings->setting('payment_gateway');
        $payment_setting_id         = $payment_setting_key['id'];
        $payment_settings           = $this->Settings_model->setting($payment_setting_id);
        $setting_value              = json_decode($payment_settings['as_setting_value'], true)['setting_value'];
        $data['payment_settings']   = $setting_value;
        /*payment settings end*/
        
        // echo '<pre>'; print_r($data);die;
        $this->load->view($this->config->item('admin_folder').'/settings',$data);
    }
    
    
    function upload_certificate()
    {
        $response                   = array();
        $response['error']          = false;
        $response['message']        = 'Certificate template upload successfully';
        $response['certificate']    = array();
        
        $file       = $this->input->post('file');
        $certificate_upload_path = certificate_upload_path();
        $this->make_directory($certificate_upload_path);
        
        $this->load->library(array('ofabeeconverter', 'upload'));
        
        $config                    = array();
        $config['upload_path']     = $certificate_upload_path;
        $config['allowed_types']   = 'docx';
        $config['encrypt_name']    = true;
        $this->upload->initialize($config);
        $uploaded = $this->upload->do_upload('file');
        
        if( $uploaded )
        {
            $upload                     = $this->upload->data();
            $save                       = array();
            $save['id']                 = false;
            $save['cm_filename']        = $upload['file_name']."?v=".rand(10,1000);
            $save['cm_account_id']      = config_item('id');
            $save['cm_is_active']       = '0';
            $save['cm_image']           = $upload['raw_name'].'/'.$upload['raw_name'].'.jpg'."?v=".rand(10,1000);
            $save['id']                 = $this->Settings_model->save_certificate($save);
            $response['certificate']    = $save;
            
            $config                 = array();
            $config['input']        = $upload['full_path'];
            $config['s3_upload']    = false;
            $config['output']       = $_SERVER['DOCUMENT_ROOT'].'/'.$certificate_upload_path;
            $config['update_db']    = false;
            $this->ofabeeconverter->initialize_config($config);
            $converter_response = $this->ofabeeconverter->convert();   
            $convertion_objects = $converter_response['convertion_objects'];        
            $dimension          = shell_exec('identify -verbose  -format "%Wx%H" '.$convertion_objects['output'].' 2>&1');
            $dimension          = explode('x', $dimension);
            $xaxis              = $dimension[0] - 350;
    
            $param              = array();
            $param['xaxis']     = $xaxis;
            $param['yaxis']     = '5';
            $param['qr_code']   = config_item('upload_folder').'/qrcode.gif';
            $param['input']     = $convertion_objects['output'];
            $param['output']    = $convertion_objects['output'];
            $this->place_qr_code($param);
            
            $width                  = '180';
            $height                 = '140';
            $certificate_path       = $certificate_upload_path.$upload['raw_name'].'/';
            
            $upload['full_path']    = $certificate_path.'/page.jpg';
            $jpg_file               = $certificate_path.$this->crop_image($upload, $width, $height, $certificate_path, 'jpg');

            $width                  = '360';
            $height                 = '280';
            $certificate_path       = $certificate_upload_path.$upload['raw_name'].'/';
            $upload['full_path']    = $certificate_path.'/page.jpg';
            $jpeg_file              = $certificate_path.$this->crop_image($upload, $width, $height, $certificate_path, 'jpeg');
            
            unlink($certificate_upload_path.$upload['raw_name'].'.pdf');
            unlink($certificate_upload_path.$upload['raw_name'].'/page.jpg');
            //rmdir($certificate_upload_path.$upload['raw_name']);

            $has_s3     = $this->settings->setting('has_s3');
            if( $has_s3['as_superadmin_value'] && $has_s3['as_siteadmin_value'] )
            {
                $files                                                  = array();
                $files[$certificate_upload_path.$upload['file_name']]   = $certificate_upload_path.$upload['file_name'];
                $files[$jpg_file]                                       = $jpg_file;
                $files[$jpeg_file]                                      = $jpeg_file;
                uploadToS3Bulk($files);
                unlink($certificate_upload_path.$upload['file_name']);
                if($upload['raw_name'])
                {
                    shell_exec('rm -rf  '.$certificate_upload_path.$upload['raw_name']);
                }
            }

            $user_data              = array();
            $user_data['user_id']   = $this->__loggedInUser['id'];
            $user_data['username']  = $this->__loggedInUser['us_name'];
            $user_data['useremail']  = $this->__loggedInUser['us_email'];
            $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
            
            $message_template                       = array();
            $message_template['username']           = $this->__loggedInUser['us_name'];

            $triggered_activity     = 'certificate_uploaded';
            log_activity($triggered_activity, $user_data, $message_template); 
        }
        else
        {
            $response['error']      = true;
            $response['message']    = $this->upload->display_errors();//lang('error_uploading_file');                
        }
        echo json_encode($response);
    }
    
    function place_qr_code($params = array())
    {
        $xaxis      = $params['xaxis'];
        $yaxis      = $params['yaxis'];
        $qr_code    = $params['qr_code'];
        $input      = $params['input'];
        $output     = $params['output'];
        shell_exec('composite -geometry "350x350 +'.$xaxis.'+'.$yaxis.'" '.$qr_code.' '.$input.' '.$output);
    }

    function launch_conversion()
    {
        $this->load->library('ofabeeconverter');
        $this->load->library('upload');
        
        $file_path              = $this->input->post('file');
        $directory              = template_upload_path();
        
        $this->make_directory($directory);
        
        $config_file                    = array();
        $config_file['upload_path']     = $directory;
        $config_file['allowed_types']   = 'docx';
        $config_file['encrypt_name']    = true;
        $config_file['file_name']       = $file_path;
        
        if( $config_file )
        {
            $this->upload->initialize($config_file);
            $uploaded = $this->upload->do_upload('file');
            //echo "<pre>";print_r($_FILES);die;
            
             if( $uploaded )
            {
                $upload_data                = $this->upload->data();
                
                $check_certificate      = $this->Settings_model->get_exist_certificate($this->config->item('id'));
                
                $save_certificate                   = array();
                $save_certificate['id']             = isset($check_certificate['id'])?$check_certificate['id']:false;
                $save_certificate['cm_filename']    = $upload_data['raw_name'];
                $save_certificate['cm_account_id']  = $this->config->item('id');
                $save_certificate['cm_is_active']   = '1';
                
                $this->Settings_model->update_certificate($save_certificate);
                
                $response['error']          = 'false';
                $response['file_object']    = $upload_data;
                $response['template_path']  = $directory;
                //echo "<pre>";print_r($upload_data);die;
            }
            else
            {
                $response['error']      = 'true';
                $response['message']    = $this->upload->display_errors();//lang('error_uploading_file');                
            }

            $config                 = array();
            $config['input']        = $directory.$upload_data['file_name'];
            $config['s3_upload']    = false;
            $config['output']       = template_upload_path();
            $config['update_db']    = false;
            $this->ofabeeconverter->initialize_config($config);
            $this->ofabeeconverter->convert();   
        }
        
        echo json_encode($response);
        
//        $config = $this->input->post();
//        $myfile = fopen("uploads/dummy.txt", "w");
//        $txt = json_encode($config);
//        fwrite($myfile, $txt);
//        fclose($myfile);
//        
//        $lecture_id            = $config['lecture_id'];
//        $lecture               = $this->Course_model->lecture(array('id'=>$lecture_id));
//        $course                = $this->Course_model->course(array('id'=>$lecture['cl_course_id']));
//        $param                 = array();
//        $param['from']         = $this->config->item('site_email');
//        $param['to']           = array('thanveer.a@enfintechnologies.com');
//        if(isset($conversion['success']) && $conversion['success']==true)
//        {
//            $param['subject']      = "File conversion completed successfully";
//            $param['body']         = "Hi Admin, <br/>A lecture named <b>".$lecture['cl_lecture_name']."</b> under the course <b>".$course['cb_title']."</b> has been successfully converted. If you are logged in please click <a href='".admin_url('coursebuilder/lecture/'.$lecture_id)."'>here</a>";
//        }
//        else
//        {
//            $param['subject']      = "File conversion error";
//            $param['body']         = "Hi Admin, <br/>There is an error in converting a lecture named <b>".$lecture['cl_lecture_name']."</b> under the course <b>".$course['cb_title']."</b>. Error is as follows<br />".$conversion['message'];
//        }
//        $send = $this->ofabeemailer->send_mail($param);            
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

    function save()
    {
        //echo '<pre>'; print_r($this->memcache->get(array('key' => 'setting_has_mail')));die;
        $save                           = array();
        $response                       = array(); 
        $hidden_id                      = '';
        
        $hidden_contact_id = $this->input->post('hidden_contact_id');
        if($hidden_contact_id)
        {
            
            $hidden_id                      = $this->input->post('hidden_contact_id');
            $contact_phone                  = str_replace(',', '<br/>',$this->input->post('contact_phone'));
            $contact_email                  = str_replace(',', '<br/>',$this->input->post('contact_email'));
            $whatsapp_number                = $this->input->post('whatsapp_number');
            $contact_address                = str_replace(array('<br/>', '<br />', '<br>', '&nbsp;'), '', $this->input->post('contact_address'));
            //$contact_address                = str_replace('</p>', '<br/>', $contact_address);
            
        }

        $hidden_gst_id = $this->input->post('hidden_gst_id');
        if($hidden_gst_id)
        {
            $hidden_id                      = $this->input->post('hidden_gst_id');
            $sgst                           = $this->input->post('sgst');
            $cgst                           = $this->input->post('cgst');
        }

        $dropbox_hidden = $this->input->post('dropbox_hidden');
        if($dropbox_hidden)
        {
            $hidden_id                      = $this->input->post('dropbox_hidden');
            $dropbox_text                   = $this->input->post('dropbox_text');
            $dropbox_checkbox               = $this->input->post('dropbox_checkbox');
        }
        $fb_app_hidden = $this->input->post('fb_app_hidden');
        $social_link_hidden = $this->input->post('social_link_hidden');
        if( $fb_app_hidden && $social_link_hidden)
        {
            $app_text       = $this->input->post('app_text');
            $hidden_id      = $this->input->post('fb_app_hidden');
        }
        
        $newsletter_hidden_key = $this->input->post('newsletter_hidden_key');
        if($newsletter_hidden_key){
        	$hidden_id = $this->input->post('newsletter_hidden_key');
        	$newsletter_onoff = $this->input->post('newsletter_onoff');
        }

        $s3_hidden_key = $this->input->post('s3_hidden_key');
        $smtp_hidden_key = $this->input->post('smtp_hidden_key');
        if( $s3_hidden_key && $smtp_hidden_key)
        {
            $s3_email          = $this->input->post('s3_email');
            $s3_app_text       = $this->input->post('s3_app_text');
            $s3_secret_text    = $this->input->post('s3_secret_text');
            $hidden_id         = $this->input->post('s3_hidden_key');
        }
        $storage_hidden_key = $this->input->post('storage_hidden_key');
        $cdn_hidden_key     = $this->input->post('cdn_hidden_key');
        if( $storage_hidden_key && $cdn_hidden_key)
        {
            $storage_app_key_text       = $this->input->post('storage_app_key_text');
            $storage_secret_key_text    = $this->input->post('storage_secret_key_text');
            $storage_region_text        = $this->input->post('storage_region_text');
            $storage_bucket_text        = $this->input->post('storage_bucket_text');
            $storage_cdn_text           = $this->input->post('storage_cdn_text');
            $hidden_id                  = $this->input->post('storage_hidden_key');
        }
        $hidden_basic_id = $this->input->post('hidden_basic_id');
        if($hidden_basic_id)
        {
            $banner_text      = $this->input->post('banner_text');
            $title_text       = strip_tags(htmlspecialchars($this->input->post('title_text')));
            $meta_description = $this->input->post('meta_description');
            $hidden_id        = $this->input->post('hidden_basic_id');
            $this->memcache->delete('home');
        }
        $gateway_hidden_id = $this->input->post('gateway_basic_id');
        if($gateway_hidden_id){

            $hidden_id        = $this->input->post('gateway_basic_id');
        }

        $setting                        = $this->Settings_model->get_settings_id($hidden_id);
        // echo 'setting_'.$setting['sk_key'];die;
        //$data['website_id']             = $setting['id'];
        $save['id']                     = $setting['id'];
        //$setting                        = $this->Settings_model->setting($this->input->post('setting_id'));
        
        if( !empty($setting['as_setting_value']))
        {            
            $setting_field                  = json_decode($setting['as_setting_value'],true);
            switch ($setting_field['setting_attribute']['type'])
            {
                case 'input':
                    //$setting_field->setting_value                   = (object)$this->input->post('setting_value');
                $hidden_contact_id = $this->input->post('hidden_contact_id');
                if($hidden_contact_id)
                {
                    $setting_field['setting_value']['site_email']   = $contact_email;
                    $setting_field['setting_value']['site_phone']   = $contact_phone;
                    $setting_field['setting_value']['site_whatsapp_number']   = $whatsapp_number;
                    $setting_field['setting_value']['site_address'] = $contact_address;
                }

                $hidden_gst_id = $this->input->post('hidden_gst_id');
                if($hidden_gst_id)
                {
                    //$hidden_id                      = $this->input->post('hidden_gst_id');
                    $setting_field['setting_value']['sgst']  = round($this->input->post('sgst'),2);
                    $setting_field['setting_value']['cgst']  = round($this->input->post('cgst'),2);
                }
                
                // Gateway settings
                $gateway_id         = $this->input->post('gateway_basic_id');
                if($gateway_id){

                    $gateway_title  = $this->input->post('gateway_key');
                    $inputs         = $this->input->post('inputs');
                    $status         = $this->input->post('status');
                    
                    if(!empty($inputs)){
                        foreach($inputs as $input_key => $input_value){
                            
                            $setting_field['setting_value'][$gateway_title]['credentials'][$input_key] = $input_value;
                        }
                    }
                    $setting_field['setting_value'][$gateway_title]['status'] = $status;
                    
                }
                
                $dropbox_hidden = $this->input->post('dropbox_hidden');
                if($dropbox_hidden)
                {
                    $setting_field['setting_value']['secret_key']   = $dropbox_text;
                    $save['as_siteadmin_value']                     = $dropbox_checkbox;
                }
                $fb_app_onoff = $this->input->post('fb_app_onoff');
                if($fb_app_onoff)
                {
                    $setting_field['setting_value']['app_id']       = $app_text;
                    if($fb_app_onoff=='on')
                    {
                        $save['as_siteadmin_value']                     = 1;
                    }
                    if($fb_app_onoff=='off')
                    {
                        $save['as_siteadmin_value']                     = 0;
                    }
                }

                $s3_onoff = $this->input->post('s3_onoff');
                if($s3_onoff)
                {
                    $setting_field['setting_value']['mail_email']        = $s3_email;
                    $setting_field['setting_value']['mail_key']          = $s3_app_text;
                    $setting_field['setting_value']['mail_secret']       = $s3_secret_text;
                    if($s3_onoff=='on')
                    {
                        $save['as_siteadmin_value']                     = 1;
                    }
                    if($s3_onoff=='off')
                    {
                        $save['as_siteadmin_value']                     = 0;
                    }
                }
                $storage_s3_onoff = $this->input->post('storage_s3_onoff');
                if($storage_s3_onoff)
                {
                    $setting_field['setting_value']['s3_access']       = $storage_app_key_text;
                    $setting_field['setting_value']['s3_secret']       = $storage_secret_key_text;
                    $setting_field['setting_value']['s3_bucket']       = $storage_bucket_text;
                    $setting_field['setting_value']['cdn']             = $storage_cdn_text;
                    $setting_field['setting_value']['s3_region']       = $storage_region_text;
                    
                    if($storage_s3_onoff=='on')
                    {
                        $save['as_siteadmin_value']                     = 1;
                    }
                    if($storage_s3_onoff=='off')
                    {
                        $save['as_siteadmin_value']                     = 0;
                    }
                }
                
                $newsletter_hidden_key = $this->input->post('newsletter_hidden_key');
                if($newsletter_hidden_key){
                    $newsletter_onoff = $this->input->post('newsletter_onoff');
                	if($newsletter_onoff=='on'){
                		$save['as_siteadmin_value'] =  1;
                	}else{
                		$save['as_siteadmin_value'] = 0;
                	}
                }
                
                $hidden_basic_id = $this->input->post('hidden_basic_id');
                if($hidden_basic_id)
                {
                    $setting_field['setting_value']['banner_text']      = $banner_text;
                    $setting_field['setting_value']['site_name']        = $title_text;
                    $setting_field['setting_value']['meta_description'] = $meta_description;
                }

                    $save['as_setting_value']       = json_encode($setting_field);

                    $user_data              = array();
                    $user_data['user_id']   = $this->__loggedInUser['id'];
                    $user_data['username']  = $this->__loggedInUser['us_name'];
                    $user_data['useremail']  = $this->__loggedInUser['us_email'];
                    $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
                    
                    $message_template                           = array();
                    $message_template['username']               = $this->__loggedInUser['us_name'];;
                    
                    $triggered_activity     = 'basic_settings_updated';
                    log_activity($triggered_activity, $user_data, $message_template); 
                break;

                case 'checkbox':
                    if(sizeof($setting_field->setting_value) > 0 )
                    {
                        foreach( $setting_field->setting_value as $key => $value)
                        {
                            $setting_field->setting_value->$key = 0;                                
                        }
                    }

                    if(sizeof($this->input->post('setting_value')) > 0 )
                    {
                        foreach( $this->input->post('setting_value') as $key => $value)
                        {
                            if( $value == 1 )
                            {
                                $setting_field->setting_value->$key = 1;
                            }
                            else
                            {
                                $setting_field->setting_value->$key = 0;                                
                            }
                        }
                    }
                    $save['as_setting_value']       = json_encode($setting_field);
                    $this->Settings_model->save($save);
                break;

                case 'radio':
                    if(sizeof($setting_field->setting_value) > 0 )
                    {
                        foreach( $setting_field->setting_value as $key => $value)
                        {
                            $setting_field->setting_value->$key = 0;                                
                        }
                    }
                    $radio = $this->input->post('radio');
                    if(isset($setting_field->setting_value->$radio))
                    {
                        $setting_field->setting_value->$radio = 1;
                    }             
                    $save['as_setting_value']       = json_encode($setting_field);
                    $this->Settings_model->save($save);
                break;

                default:
                break;
            }
        }
        //$save['as_superadmin_value']    = $this->input->post('as_superadmin_value');
        //$save['as_siteadmin_value']     = $this->input->post('as_siteadmin_value');
        $save['updated_date']           = date("Y-m-d H:i:s");
        $this->memcache->delete('setting_'.$setting['sk_key']);//invalidate the memcache index
        if($this->Settings_model->save($save))
        {   
                $zoho_hidden_key = $this->input->post('zoho_hidden_key');
        	if($zoho_hidden_key){
        		$setting = $this->Settings_model->get_settings_id($this->input->post('zoho_hidden_key'));
        		$save['id']                     = $setting['id'];
                $setting_field                  = json_decode($setting['as_setting_value'],true);
                $setting_field['setting_value']['api_key']      = $this->input->post('zoho_api');
                
                $zoho_onoff = $this->input->post('zoho_onoff');
                if($zoho_onoff=='on'){
                	$save['as_siteadmin_value'] = 1;	
                }else{
                	$save['as_siteadmin_value'] = 0;
                }
                $save['as_setting_value']       = json_encode($setting_field);
                $save['updated_date']           = date("Y-m-d H:i:s");
                if($this->Settings_model->save($save)){
                    $mailchimp_hidden_key = $this->input->post('mailchimp_hidden_key');
                	if($mailchimp_hidden_key){
                		$setting = $this->Settings_model->get_settings_id($this->input->post('mailchimp_hidden_key'));
		        		$save['id']                     = $setting['id'];
		                $setting_field                  = json_decode($setting['as_setting_value'],true);
		                $setting_field['setting_value']['api_key']      = $this->input->post('mailchimp_api');
                                
                                $mailchimp_onoff = $this->input->post('mailchimp_onoff');
		                if($mailchimp_onoff=='on'){
		                	$save['as_siteadmin_value'] = 1;	
		                }else{
		                	$save['as_siteadmin_value'] = 0;
		                }
		                $save['as_setting_value']       = json_encode($setting_field);
		                $save['updated_date']           = date("Y-m-d H:i:s");
		                if($this->Settings_model->save($save)){
		                	
		                }

                	}
                }
        	}
            $social_link_hidden = $this->input->post('social_link_hidden');
            if($social_link_hidden)
            {
                $setting                        = $this->Settings_model->get_settings_id($this->input->post('social_link_hidden'));
                //$data['website_id']             = $setting['id'];
                $save['id']                     = $setting['id'];
                $setting_field                  = json_decode($setting['as_setting_value'],true);
                $setting_field['setting_value']['facebook']      = $this->input->post('fb_link_text');
                $setting_field['setting_value']['twitter']       = $this->input->post('twitter_link_text');
                $setting_field['setting_value']['google_plus']   = $this->input->post('youtube_link_text');
                $save['as_setting_value']       = json_encode($setting_field);
                $save['updated_date']           = date("Y-m-d H:i:s");
                $save['as_siteadmin_value']     = 1;
                $this->Settings_model->save($save);
                $this->memcache->delete('setting_'.$setting['sk_key']);//invalidate the memcache index
            }
            $cdn_hidden_key = $this->input->post('cdn_hidden_key');
            if($cdn_hidden_key)
            {
                $setting                        = $this->Settings_model->get_settings_id($this->input->post('cdn_hidden_key'));
                //$data['website_id']             = $setting['id'];
                $save['id']                     = $setting['id'];
                $setting_field                  = json_decode($setting['as_setting_value'],true);
                $storage_cdn_text               = $this->input->post('storage_cdn_text');
                $setting_field['setting_value']['cdn']  = $storage_cdn_text;

                $save['as_setting_value']       = json_encode($setting_field);
                $save['updated_date']           = date("Y-m-d H:i:s");
                
                $storage_cdn_onoff = $this->input->post('storage_cdn_onoff');
                if($storage_cdn_onoff=='on')
                {
                    $save['as_siteadmin_value']     = 1;
                }
                if($storage_cdn_onoff=='off')
                {
                    $save['as_siteadmin_value']     = 0;
                }
                $this->Settings_model->save($save);
                //echo '<pre>';print_r($save); die;
            }

            $smtp_hidden_key = $this->input->post('smtp_hidden_key');
            if($smtp_hidden_key)
            {
                $setting                        = $this->Settings_model->get_settings_id($this->input->post('smtp_hidden_key'));
                //$data['website_id']             = $setting['id'];
                $save['id']                     = $setting['id'];
                $setting_field                  = json_decode($setting['as_setting_value'],true);
                $setting_field['setting_value']['host']           = $this->input->post('smtp_user_host');
                $setting_field['setting_value']['port']           = $this->input->post('smtp_user_port');
                $setting_field['setting_value']['user_name']      = $this->input->post('smtp_user_text');
                $setting_field['setting_value']['password']       = $this->input->post('smtp_pass_text');

                $save['as_setting_value']       = json_encode($setting_field);
                $save['updated_date']           = date("Y-m-d H:i:s");
                $smtp_onoff = $this->input->post('smtp_onoff');
                if($smtp_onoff=='off')
                {
                    $save['as_siteadmin_value']     = 0;
                }
                if($smtp_onoff=='on')
                {
                    $save['as_siteadmin_value']     = 1;
                }
                $this->Settings_model->save($save);
            }

            $response['error']      = false;
            $response['message']    = lang('contact_updated_success');
        }
        else
        {
            $response['error']      = true;
            $response['message']    = lang('contact_updated_error');
        }
        echo json_encode($response);
    }

    
    /* Function for content security */
    function content_security()
    {
        $response                       = array();   
        $response['error']              = false;   
        $response['message']            = "Settings saved successfully!";  
        $security_checkbox_value        = $this->input->post('security_checkbox_value');
        $setting_key                    = $this->input->post('setting_key');
        $setting                        = $this->Settings_model->get_settings_id($setting_key);
        //$data['website_id']             = $setting['id'];
        $save['id']                     = $setting['id'];
        $setting_field                  = json_decode($setting['as_setting_value'],true);
        $setting_field['setting_value']['content_security_status']  = $security_checkbox_value;


        $save['as_setting_value']       = json_encode($setting_field);
        $save['updated_date']           = date("Y-m-d H:i:s");
        if($this->Settings_model->save($save))
        {
            $this->memcache->delete('setting_has_content_security');//invalidate the memcache index
        }
        else
        {
            $response['error']              = false;   
            $response['message']            = "Error in saving settings!";    
        }
        echo json_encode($response);  
    }


    /* Function for content security */
    function restricted_login()
    {
        $response                       = array();   
        $response['error']              = false;   
        $response['message']            = "User login restriction settings saved successfully!";  
        $login_restricted               = $this->input->post('login_restricted');
        $setting_key                    = $this->input->post('setting_key');
        $setting                        = $this->Settings_model->get_settings_id($setting_key);
        //$data['website_id']             = $setting['id'];
        $save['id']                     = $setting['id'];
        $setting_field                  = json_decode($setting['as_setting_value'],true);
        $setting_field['setting_value']['login_restricted']  = $login_restricted;


        $save['as_setting_value']       = json_encode($setting_field);
        $save['updated_date']           = date("Y-m-d H:i:s");
        if($this->Settings_model->save($save))
        {
            $this->memcache->delete('setting_restrict_user_login');//invalidate the memcache index
        }
        else
        {
            $response['error']              = false;   
            $response['message']            = "Error in saving settings!";    
        }
        echo json_encode($response);  
    }


    /* Function for support chat insert & update */
    function support_chat()
    {
        $response              = array();         
        $chat_checkbox_value   = $this->input->post('chat_checkbox_value');
        $chat_textbox_value    = $this->input->post('chat_textbox_value');
       

        $save                           = array();
        $save['support_chat_script']    = $chat_textbox_value;
        $save['support_chat_status']    = $chat_checkbox_value; 
        $save['support_chat_account_id']= $this->config->item('id'); 
        $save_count                     = $this->Settings_model->get_support_chat_count();

        if($save_count>0)
        {
            if($this->Settings_model->update_support_chat($save))
            {
                $response['error']      = false;
                $response['message']    = lang('support_chat_updated_success');
            }
        }
        else
        {
            if($this->Settings_model->save_support_chat($save))
            {
                $response['error']      = false;
                $response['message']    = lang('support_chat_created_success');
            }
        }
        $this->memcache->delete('support_chat');//invalidate the memcache index
        echo json_encode($response);  
    }

    /* Function for support chat insert & update */
    function support_chat_off()
    {
        $response              = array();         
        $chat_checkbox_value   = $this->input->post('chat_checkbox_value');
        $chat_textbox_value    = $this->input->post('chat_textbox_value');

        $save                           = array();
         
        
        $save_count                     = $this->Settings_model->get_support_chat_count();

        if($save_count>0)
        {
            $save['support_chat_status']    = $chat_checkbox_value;
            if($this->Settings_model->update_support_chat($save))
            {
                $response['error']      = false;
                $response['message']    = lang('support_chat_updated_success');
            }
        }
        else
        {
            $save['support_chat_script']    = $chat_textbox_value;
            $save['support_chat_status']    = $chat_checkbox_value;

            if($this->Settings_model->save_support_chat($save))
            {
                $response['error']      = false;
                $response['message']    = lang('support_chat_created_success');
            }
        }
        $this->memcache->delete('support_chat');//invalidate the memcache index
        echo json_encode($response);
    }

    /* Analytics */

    function analytics_off()
    {
        $response                   = array();         
        $save                       = array();
        $analytics_hidden           = $this->input->post('analytics_hidden');
        $setting                    = $this->settings->setting('has_google_analytics');
        $save['id']                 = $setting['id'];
        $save['as_siteadmin_value'] = 0;
        if($this->Settings_model->save($save))
        {
            $this->memcache->delete('setting_has_google_analytics');
            $response['error']      = false;
            $response['message']    = 'Analytics disabled.';
        }
        else
        {
            $response['error']      = true;
            $response['message']    = 'Error disabling analytics.';
        }
        echo json_encode($response);
    }

    function analytics()
    {
        $response           = array();         
        $save               = array();
        
        $analytics_hidden   = $this->input->post('analytics_hidden');
        $enable_analytics   = $this->input->post('analytics_checkbox_value');
        $script             = $this->input->post('analytics_textbox_value');
        $setting            = $this->settings->setting('has_google_analytics');   
        $access_url         = $this->input->post('analytics_access_url');

        $setting['as_setting_value']['setting_value']->script       = $script;
        $setting['as_setting_value']['setting_value']->access_url   = $access_url;

        $save['id']                   = $setting['id'];
        $save['as_siteadmin_value']   = $enable_analytics;
        $save['as_setting_value']     = json_encode($setting['as_setting_value']);
        
        if($this->Settings_model->save($save))
        {
            $this->memcache->delete('setting_has_google_analytics');
            $response['error']      = false;
            $response['message']    = 'Analytics settings updated.';
        }
        else
        {
            $response['error']      = true;
            $response['message']    = 'Error updating settings.';
        }
        echo json_encode($response);
    }

    /* End Analytics*/

    /* Function for dropbox off */
    function dropbox_off()
    {
        $response                 = array();         
        $save                     = array();
        
        $dropbox_hidden           = $this->input->post('dropbox_hidden');
        $setting                  = $this->Settings_model->get_settings_id($dropbox_hidden);
        //$data['website_id']             = $setting['id'];
        $save['id']                   = $setting['id'];
        $save['as_siteadmin_value']   = $this->input->post('dropbox_checkbox_value');
        if($this->Settings_model->save($save))
        {
                $response['error']      = false;
                $response['message']    = lang('contact_updated_success');
        }
        else
        {
                $response['error']      = true;
                $response['message']    = lang('contact_updated_error');
        }
        echo json_encode($response);
    }


    function upload_favicon()
    {
        $response                   = array();
        $response['error']          = true;
        $response['message']        = 'Failed to upload';
        
        $extension                  = $this->input->post('extension');

        $config                     = array();
        $config['upload_path']      =  $_SERVER["DOCUMENT_ROOT"].'/'.favicon_upload_path();
        $config['allowed_types']    = 'png';
        $config['maintain_ratio']   = true;
        $config['file_name']        = 'favicon.'.$extension; 
        $config['overwrite']        = TRUE;

        $this->load->library('upload');
        $this->upload->initialize($config);
        $upload = $this->upload->do_upload('favicon');
        if($upload)
        {
            $response['user_image'] = base_url(favicon_upload_path($config['file_name']));
            $response['error']      = false;
            $response['message']    = 'Site favicon updated successfully';
            $uploaded_data = $this->upload->data();
        }
        else
        {
            $response['error']      = true;
            $response['user_image'] = base_url('default.png');
            $response['message']    = 'Error in updating site logo<br >'.$this->upload->display_errors();            
        }
        echo json_encode($response);
    }
    
    function upload_logo_image()
    {
        $response               = array();
        $response['error']      = true;
        $response['message']    = 'Failed to upload';
        $directory              = logo_upload_path();
        if(!file_exists($directory)){
            mkdir($directory, 0777, true);
        }
        $extension = $this->input->post('extension');

        $config                     = array();
        $config['upload_path']      =  $directory;
        $config['allowed_types']    = 'gif|jpg|png|jpeg';
        $config['maintain_ratio']   = true;
        $config['file_name']        = config_item('id').'.'.$extension;
        $config['overwrite']        = TRUE;

        $this->load->library('upload');
        $this->upload->initialize($config);
        $upload = $this->upload->do_upload('file');
        if($upload)
        {
            $response['error']      = false;
            $response['message']    = 'Site logo updated successfully';
            $uploaded_data = $this->upload->data();
            //saving logo details in settings table
            $website = $this->settings->setting('website');
            // $website['as_setting_value']['setting_value']->site_logo = $uploaded_data['raw_name'].'.'.$extension;
            $website['as_setting_value']['setting_value']->site_logo = config_item('id').'.'.$extension;
            $save                       = array();
            $save['id']                 = $website['id'];
            $save['as_setting_value']   = json_encode($website['as_setting_value']);
            $this->Settings_model->save($save);
            //enf
            $width   = '274';
            $height  = '105';
            $path_full  = logo_upload_path();
            
            
            $path = $uploaded_data['full_path'];
            $mime = getimagesize($path);
            // New save location
            $directory          = $path_full;
            $this->make_directory($directory);

            //$new_file   = $this->cropImageLogo($uploaded_data,$width,$height,$path_full,$extension);
            $this->load->library('simpleimage');
            $this->simpleimage->fromFile($uploaded_data['full_path']);
            $this->simpleimage->autoOrient();
            $this->simpleimage->bestFit(300, 600);
            $this->simpleimage->toFile($uploaded_data['full_path'], $mime['mime']);
            
            // $new_file   = $uploaded_data['raw_name'].'.'.$extension;
            $new_file   = config_item('id').'.'.$extension;
            $has_s3     = $this->settings->setting('has_s3');
            if( $has_s3['as_superadmin_value'] && $has_s3['as_siteadmin_value'] )
            {
                uploadToS3(logo_upload_path().$new_file, logo_upload_path().$new_file);
                unlink(logo_upload_path().$new_file);
            }
            $response['user_image']      = uploads_url().logo_upload_path().$new_file.'?v='.rand(100, 999);
            $this->memcache->delete('setting_'.$website['id']);//invalidate the memcache index
        }
        else
        {
            $response['error']      = true;
            $response['message']    = 'Error in updating site logo<br >'.$this->upload->display_errors();            
        }
        echo json_encode($response);
    }

    function upload_testimonial_image()
    {
        $response               = array();
        $response['error']      = false;
        $response['message']    = 'Testimonial created successfully';
        $directory              = testimonial_upload_path();
        $upload_flag            = $this->input->post('upload_flag');
        //echo "<pre>";print_r($this->input->post());die;
        if($upload_flag == '2' || $upload_flag == '1')
        {

            $testimonial_edit_id        = $this->input->post('testimonial_id');
            $response['testimonial_id'] = $testimonial_edit_id;
            if(!file_exists($directory))
            {
                mkdir($directory, 0777, true);
            }
            $config                     = array();
            $config['upload_path']      =  $directory;
            $config['allowed_types']    = 'jpg|jpeg|JPG|JPEG';
            $config['encrypt_name']     = true;
            $config['max_width']        = 90;
            $config['max_height']       = 90;
            $this->load->library('upload');
            $this->upload->initialize($config);
            $upload = $this->upload->do_upload('file');
            //echo "<pre>";print_r($upload);die;
            if($upload)
            {
                $uploaded_data = $this->upload->data();
                
                //saving logo details in settings table
                $extension                  = $this->input->post('extension');
                $save                       = array();
                $testimonial_edit_id        = $this->input->post('testimonial_id');
                $save['id']                 = isset($testimonial_edit_id) ? $testimonial_edit_id : false;
                $save['t_name']             = $this->input->post('testimonial_name');
                $save['t_text']             = $this->input->post('testimonial_text');
                $save['t_other_detail']     = $this->input->post('testimonial_detail');
                $save['t_featured']         = $this->input->post('testimonial_featured');
                $save['t_account_id']       = $this->config->item('id');
                $save['t_image']            = $uploaded_data['raw_name'].'.'.$extension."?v=".rand(10,1000);;
                $save['t_status']           = 1;
                $save['created_date']       = date("Y-m-d H:i:s");
                $testimonial_id             = $this->Settings_model->save_testimonial($save);
                //end

                $width      = '64';
                $height     = '64';
                $path_full  = testimonial_crop_upload_path();
                $new_file   = $this->crop_image($uploaded_data, $width, $height,$path_full, $extension);
                $has_s3     = $this->settings->setting('has_s3');
                if( $has_s3['as_superadmin_value'] && $has_s3['as_siteadmin_value'] )
                {
                    $testimonial_uploaded_path              = testimonial_upload_path().$new_file;
                    $testimonial_crop_uploaded_path         = testimonial_crop_upload_path().$new_file;
                    $files                                  = array();
                    $files[$testimonial_uploaded_path]      = $testimonial_uploaded_path;
                    $files[$testimonial_crop_uploaded_path] = $testimonial_crop_uploaded_path;
                    uploadToS3Bulk($files);
                    unlink($testimonial_uploaded_path);
                    unlink($testimonial_crop_uploaded_path);

                }
                $response['user_name']          = $save['t_name'];
                $response['testimonial_text']   = $save['t_text'];
                $response['testimonial_id']     = $testimonial_id;
                $response['user_other_detail']  = $save['t_other_detail'];
                $response['featured_testimonial']= $save['t_featured'];
                $response['user_image']         = testimonial_crop_path().$new_file;
                $response['user_image_file']    = $new_file;

                $this->memcache->delete('testimonials');
                $this->memcache->delete('home');
            }
            else
            {
                $response['error']      = true;
                $response['message']    = 'Error in updating testimonial image<br >'.$this->upload->display_errors();            
            }
        }
        else
        {
            $testimonial_edit_id        = $this->input->post('testimonial_id');
            $save['id']                 = isset($testimonial_edit_id) ? $testimonial_edit_id : false;
            $save['t_name']             = $this->input->post('testimonial_name');
            $save['t_text']             = $this->input->post('testimonial_text');
            $save['t_other_detail']     = $this->input->post('testimonial_detail');
            $save['t_featured']         = $this->input->post('testimonial_featured');
            $testimonial_id             = $this->Settings_model->save_testimonial($save);
            
            $response['user_name']          = $save['t_name'];
            $response['testimonial_text']   = $save['t_text'];
            $response['testimonial_id']     = $testimonial_id;
            $response['user_other_detail']  = $save['t_other_detail'];
            $response['featured_testimonial']= $save['t_featured'];
            $user_image                     = $this->input->post('testimonial_img');
            $response['user_image']         = testimonial_crop_path().$user_image;
            $response['user_image_file']    = $user_image;

            $this->memcache->delete('testimonials');
            $this->memcache->delete('home');
        }
        
        echo json_encode($response);
    }

    function update_featured_testimonial()
    {
        $response               = array();
        $response['error']      = false;
        $response['message']    = 'Testimonial updated successfully';

        $save                       = array();
        $testimonial_edit_id        = $this->input->post('testimonial_id');
        $save['id']                 = isset($testimonial_edit_id) ? $testimonial_edit_id : false;
        $save['t_featured']         = $this->input->post('testimonial_featured');
        $testimonial_id             = $this->Settings_model->save_testimonial($save);

        if($testimonial_id > 0)
        {
            $response['testimonial_id']     = $testimonial_id;
            $this->memcache->delete('home');
        }
        else
        {
            $response['error']      = true;
            $response['message']    = 'Testimonial was not found';            
        }

        echo json_encode($response);
    }

    function upload_banner_image()
    {
        $response               = array();
        $response['error']      = 'false';
        $response['message']    = 'Banner uploaded successfully';
        $directory              = banner_upload_path();
        if(!file_exists($directory)){
            mkdir($directory, 0777, true);
        }
        $config                     = array();
        $config['upload_path']      =  $directory;
        $config['allowed_types']    = 'jpg|jpeg';
        $config['encrypt_name']     = true;
        $this->load->library('upload');
        $this->upload->initialize($config);
        $upload = $this->upload->do_upload('file');
        if($upload)
        {
            $uploaded_data = $this->upload->data();
            //saving logo details in settings table
            $extension = $this->input->post('extension');
            $save                       = array();
            $save['banner_name']        = $uploaded_data['raw_name'].'.'.$extension."?v=".rand(10,1000);
            $save['banner_active']      = 0;
            $save['banner_type']        = 1;
            $save['banner_account_id']  = $this->config->item('id');
            $save['created_date']       = date("Y-m-d H:i:s");

            $banner_id                  = $this->Settings_model->save_banner($save);

            $user_data              = array();
            $user_data['user_id']   = $this->__loggedInUser['id'];
            $user_data['username']  = $this->__loggedInUser['us_name'];
            $user_data['useremail']  = $this->__loggedInUser['us_email'];
            $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
            
            $message_template                           = array();
            $message_template['username']               = $this->__loggedInUser['us_name'];;
            
            $triggered_activity     = 'banner_upload';
            log_activity($triggered_activity, $user_data, $message_template); 

            //end
            $width      = '90';
            $height     = '90';
            $path_full  = banner_crop_upload_path();
            $new_file   = $this->crop_image($uploaded_data,$width,$height,$path_full,$extension);
            $has_s3     = $this->settings->setting('has_s3');
            if( $has_s3['as_superadmin_value'] && $has_s3['as_siteadmin_value'] )
            {
                $banner_uploaded_path = banner_upload_path().$new_file;
                $banner_crop_uploaded_path = banner_crop_upload_path().$new_file;
                $files                              = array();
                $files[$banner_uploaded_path]       = $banner_uploaded_path;
                $files[$banner_crop_uploaded_path]  = $banner_crop_uploaded_path;
                uploadToS3Bulk($files);
                unlink($banner_uploaded_path);
                unlink($banner_crop_uploaded_path);
            }
            $response['banner_id']      = $banner_id;
            $response['user_image']     = banner_crop_path().$new_file;
        }
        else
        {
            $response['error']      = 'true';
            $response['message']    = 'Error in updating testimonial image<br >'.$this->upload->display_errors();            
        }
        echo json_encode($response);
    }
    
    function crop_image($uploaded_data,$width,$height,$path,$extension)
    {
        $source_path = $uploaded_data['full_path'];
        // define('DESIRED_IMAGE_WIDTH', $width);
        // define('DESIRED_IMAGE_HEIGHT', $height);
        $DESIRED_IMAGE_WIDTH = $width;
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
        $directory          = $path;
        $this->make_directory($directory);
        imagejpeg($desired_gdim, $directory.$uploaded_data['raw_name'].'.'.$extension);

        /*
         * Add clean-up code here
         */
        return $uploaded_data['raw_name'].'.'.$extension;
    }
    
    function cropImageLogo($uploaded_data,$new_width,$new_height,$targetPath,$extension)
    {
        $path = $uploaded_data['full_path'];

        $mime = getimagesize($path);

        if($mime['mime']=='image/png') { 
            $src_img = imagecreatefrompng($path);
        }
        if($mime['mime']=='image/jpg' || $mime['mime']=='image/jpeg' || $mime['mime']=='image/pjpeg') {
            $src_img = imagecreatefromjpeg($path);
        }   

        $old_x          =   imageSX($src_img);
        $old_y          =   imageSY($src_img);
        
        echo $old_x.'------'.$old_y;

        if($old_x > $old_y) 
        {
            $thumb_w    =   $new_width;
            $thumb_h    =   $old_y*($new_height/$old_x);
        }

        if($old_x < $old_y) 
        {
            $thumb_w    =   $old_x*($new_width/$old_y);
            $thumb_h    =   $new_height;
        }

        if($old_x == $old_y) 
        {
            $thumb_w    =   $new_width;
            $thumb_h    =   $new_height;
        }

        $dst_img        =   ImageCreateTrueColor($thumb_w,$thumb_h);

        imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y); 


        // New save location
        $directory          = $targetPath;
        $this->make_directory($directory);

        if($mime['mime']=='image/png') {
            $result = imagepng($dst_img,$directory.$uploaded_data['raw_name'].'.'.$extension,8);
        }
        if($mime['mime']=='image/jpg' || $mime['mime']=='image/jpeg' || $mime['mime']=='image/pjpeg') {
            $result = imagejpeg($dst_img,$directory.$uploaded_data['raw_name'].'.'.$extension,80);
        }

        imagedestroy($dst_img); 
        imagedestroy($src_img);

        return $uploaded_data['raw_name'].'.'.$extension;
    }
    
    function crop_image_testimonials($uploaded_data,$width,$height,$path)
    {
        $source_path = $uploaded_data['full_path'];
        define('DESIRED_IMAGE_WIDTH_NEW', $width);
        define('DESIRED_IMAGE_HEIGHT_NEW', $height);
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
        $desired_aspect_ratio = DESIRED_IMAGE_WIDTH_NEW / DESIRED_IMAGE_HEIGHT_NEW;

        if ($source_aspect_ratio > $desired_aspect_ratio)
        {
            /*
             * Triggered when source image is wider
             */
            $temp_height = DESIRED_IMAGE_HEIGHT_NEW;
            $temp_width = ( int ) (DESIRED_IMAGE_HEIGHT_NEW * $source_aspect_ratio);
        } else {
            /*
             * Triggered otherwise (i.e. source image is similar or taller)
             */
            $temp_width = DESIRED_IMAGE_WIDTH_NEW;
            $temp_height = ( int ) (DESIRED_IMAGE_WIDTH_NEW / $source_aspect_ratio);
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

        $x0 = ($temp_width - DESIRED_IMAGE_WIDTH_NEW) / 2;
        $y0 = ($temp_height - DESIRED_IMAGE_HEIGHT_NEW) / 2;
        $desired_gdim = imagecreatetruecolor(DESIRED_IMAGE_WIDTH_NEW, DESIRED_IMAGE_HEIGHT_NEW);
        imagecopy(
            $desired_gdim,
            $temp_gdim,
            0, 0,
            $x0, $y0,
            DESIRED_IMAGE_WIDTH_NEW, DESIRED_IMAGE_HEIGHT_NEW
        );

        /*
         * Render the image
         * Alternatively, you can save the image in file-system or database
         */

        //header('Content-type: image/jpeg');
        $directory          = $path;
        $this->make_directory($directory);
        imagejpeg($desired_gdim, $directory.$uploaded_data['raw_name'].'_'.$width.'x'.$height.'.jpg');

        /*
         * Add clean-up code here
         */
        return $uploaded_data['raw_name'].'.jpg';
    }
    
    function language()
    {
        $response = array();
        $response['language'] = array();
        $response['language'] = get_instance()->lang->language;
        echo json_encode($response);
    }

    function remove_testimonial()
    {
        $response               = array();
        $response['error']      = false;
        $testimonial_id         = $this->input->post('testimonial_id');
        if($testimonial_id)
        {
            if($this->Settings_model->remove_testimonial($testimonial_id))
            {
                $response['error']   = false;
                $response['message'] = 'Testimonial removed successfully';

                $this->memcache->delete('testimonials');
                $this->memcache->delete('home');
            }
            else
            {
                $response['error']   = true;
                $response['message'] = 'Some error occured. Try again';
            }
        }
        echo json_encode($response);
    }
    
    function change_certificate_status()
    {
        $response          = array();
        $save              = array();
        //$response['error'] = 
        $certificate_id = $this->input->post('certificate_id');
        if($certificate_id)
        {
            $save['id']                 = $certificate_id;
            $save['cm_is_active']       = '1';
            $save['cm_account_id']      = $this->config->item('id');
            $this->Settings_model->change_certificate_status($save);
            $response['error']   = false;
        }
        echo json_encode($response);
    }
    
    function change_banner_status()
    {
        $response          = array();
        $save              = array();
        //$response['error'] = 
        $id = $this->input->post('banner_id');
        if($id)
        {
            $save['id']            = $id;
            $save['banner_active'] = 1;
            $save['banner_account_id'] = $this->config->item('id');

            $this->Settings_model->change_banner_status($save);
            $response['error']   = false;
            $this->memcache->delete('home');
        }
        echo json_encode($response);
    }
    
    function profile_field()
    {
        $response               = array();
        $response['message']    = '';
        $response['error']      = false;
        $field_id               = $this->input->post('id');
        
        if(!$field_id)
        {
            $response['message']    = 'Field id missing.';
            $response['error']      = true;
            echo json_encode($response);die;
        }
        $response['profile_field'] = $this->Settings_model->profile_field(array('id' => $field_id));
        if(empty($response['profile_field']))
        {
            $response['message'] = 'Requested profile field not found.';
            $response['error'] = true;
        }
        echo json_encode($response);die;
    }
    
    function edit_field()
    {
        
        $response               = array();
        $response['message']    = '';
        $response['error']      = false;
        $field_id               = $this->input->post('id');

        $response['blocks'] = $this->Settings_model->blocks();           
        if($field_id > 0)
        {
            $response['profile_field'] = $this->Settings_model->profile_field(array('id' => $field_id));
            if(empty($response['profile_field']))
            {
                $response['message'] = 'Requested profile field not found.';
                $response['error'] = true;
            }
        }
        echo json_encode($response);die;
    }
    
    function save_profile_field()
    {
        $response               = array();
        $response['message']    = 'Profile field saved successfully';
        $response['error']      = false;

        $id                     = $this->input->post('id');
        $field_label            = $this->input->post('field_label');
        $field_name             = str_replace(" ","_",$field_label);
        // $field_name             = str_replace(".","_",$field_name);
        $field_name             = md5($field_name).date('ymdhis');
        $field_mandatory        = $this->input->post('field_mandatory');
        $field_auto_suggestion  = $this->input->post('field_auto_suggestion');
        $field_placeholder      = $this->input->post('field_placeholder');
        $field_default_value    = $this->input->post('field_default_value');
        $field_input_type       = $this->input->post('field_input_type');
        
        // if($this->Settings_model->block(array('exclude_id'=>$save['id'], 'pb_name' => $save['pb_name'])) > 0 )
        // {
        //     $response['error']  = true;
        //     $response['message']= 'Block name already exist';
        //     echo json_encode($response);
        //     exit;
        // }
        //Save the block
        $block_id = $this->input->post('block_id');
        if( !$block_id )
        {
            $block                 = array();
            $block['id']           = $block_id;
            $block['pb_name']      = $this->input->post('block_name');      
            $block['pb_order']     = $this->Settings_model->blocks(array('count' => true));
            $block['pb_order']     = $block['pb_order']+1;
            $block_id              = $this->Settings_model->save_block($block);
        }

        //validation starts
        $message = '';
        if($field_label == '')
        {
            $message .= 'Field label cannot be empty<br />';
            $response['error'] = true;
        }
        if($field_name == '')
        {
            $message .= 'Field name cannot be empty<br />';
            $response['error'] = true;
        }
        else
        { 
            if($this->Settings_model->profile_field(array('pf_label'=> $field_label, 'exclude_id' => $id)))
            {
                $message .= 'Field name is currently in use. Please use another<br />';
                $response['error'] = true;            
            }
            // echo $field_name.'-'.$id.'-'.$message;
            // print_r($this->input->post());
            // print_r($response);
            // echo $this->db->last_query();
            // die('123456789');
        }
        if($field_mandatory == '')
        {
            $message .= 'Is this field mandatory?<br />';
            $response['error'] = true;
        }
        if($response['error'] == false)
        {
            $save                        = array();
            $save['id']                  = $id;
            $save['pf_label']            = $field_label;
            $mstatus                     = 'updated';
            if(!$id)
            {
                $save['pf_name']         = $field_name;         
                $mstatus                 = 'added';       
            }
            $save['pf_mandatory']        = $field_mandatory;
            $save['pf_auto_suggestion']  = $field_auto_suggestion;
            $save['pf_placeholder']      = $field_placeholder;
            $save['pf_block_id']         = $block_id;
            $save['pf_field_input_type'] = $field_input_type;
            if($field_input_type == 2)
            {
                $field_default_value = explode(',', $field_default_value);
                $field_default_value = (array_filter($field_default_value, function($value) { return trim($value) !== ''; }));
                $field_default_value = implode(',', $field_default_value);
                $save['pf_auto_suggestion'] = '0';
            }
            else
            {
                $save['pf_default_value']   = '';
                $field_default_value        = '';
            }
            $save['pf_default_value']    = $field_default_value;
            
            if(!$id)
            {
                $save['pf_order']    = $this->Settings_model->profile_fields(array('count' =>true));                
                $save['pf_order']    = $save['pf_order']+1;
            }
            $save['pf_account_id']       = $this->config->item('id');
            $response['id']              = $this->Settings_model->save_profile_field($save);
            $response['field']           = $this->Settings_model->profile_field(array('id' => $response['id']));
            
            $user_data              = array();
            $user_data['user_id']   = $this->__loggedInUser['id'];
            $user_data['username']  = $this->__loggedInUser['us_name'];
            $user_data['useremail']  = $this->__loggedInUser['us_email'];
            $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
            
            $message_template                           = array();
            $message_template['username']               = $this->__loggedInUser['us_name'];;
            $message_template['field_label']            = $save['pf_label'];
            $message_template['action']                 = $mstatus;

            $triggered_activity                         = 'profile_field_added';
            log_activity($triggered_activity, $user_data, $message_template); 
        }
        else
        {
            $response['message'] = $message;            
            $response['error']   = true;            
        }
        $response['block']   = $this->Settings_model->block(array('id' => $block_id ));
        echo json_encode($response);
        //end
    }
    
    function delete_field()
    {
        $response               = array();
        $response['message']    = '';
        $response['error']      = false;
        $field_id               = $this->input->post('field_id');
        
        if(!$field_id)
        {
            $response['message']    = 'Field id missing.';
            $response['error']      = true;
            echo json_encode($response);die;
        }
        $profile_field = $this->Settings_model->profile_field(array('id' => $field_id));
        if(empty($profile_field))
        {
            $response['message'] = 'Requested profile field not found.';
            $response['error'] = true;
            echo json_encode($response);die;
        }
        
        if($this->Settings_model->delete_field(array('id'=>$field_id)))
        {
            $response['message'] = 'Field deleted successfully.';
            $response['error'] = false;
        }
        echo json_encode($response);die;
    }
    
    function update_field_position()
    {
        $response               = array();
        $response['error']      = 'false';
        $response['message']    = 'Field saved successfully';
        $block_id               = $this->input->post('block_id');
        $block_id               = explode('_', $block_id);
        $block_id               = $block_id[2];
        $structure              = $this->input->post('structure');
        parse_str($structure, $structure);
        //echo '<pre>'; print_r($structure);die;
        if( isset($structure['field_id']))
        {
            $structure['field_id'] = array_filter($structure['field_id']);
            if(!empty($structure['field_id']))
            {
                foreach ($structure['field_id'] as $order => $id) 
                {
                    $save                = array();
                    $save['id']          = $id;
                    $save['pf_block_id'] = $block_id;
                    $save['pf_order']    = ($order)+1;
                    $this->Settings_model->save_profile_field($save);
                }    
            }
        }
        echo json_encode($response);
    }
    
    function save_block()
    {
        $response           = array();
        $response['error']  = false;
        $response['message']= 'Block created successfully';
        
        $save                   = array();
        $save['id']             = $this->input->post('block_id');
        $save['pb_name']        = $this->input->post('block_name');
        if(trim($save['pb_name']) == '' )
        {
            $response['error']  = true;
            $response['message']= 'Block name required<br />';
            echo json_encode($response);exit;            
        }
        if( $this->Settings_model->block(array('exclude_id'=>$save['id'], 'pb_name' => $save['pb_name'])) > 0 )
        {
            $response['error']  = true;
            $response['message']= 'Block name already exist';
            echo json_encode($response);exit;
        }
        if( !$save['id'])
        {
            $position           = $this->input->post('position');
            if( $position )
            {
                $blocks       = $this->Settings_model->blocks();
                //echo '<pre>'; print_r($blocks);die;
                if( !empty($blocks))
                {
                    $position_temp = 1;
                    foreach ($blocks as $block)
                    {
                        //$position_temp = $sections['s_order_no'];
                        if( $position_temp == $position )
                        {
                            $save['pb_order'] = $block['pb_order'];
                            $order_number     = $block['pb_order'];
                            $order_number++;
                            $this->Settings_model->save_block(array('id'=>$block['id'], 'pb_order' => $order_number));
                        }
                        if( $position_temp > $position )
                        {
                            $order_number++;
                            $this->Settings_model->save_block(array('id'=>$block['id'], 'pb_order' => $order_number));
                        }
                        $position_temp++;
                    }
                }
                //echo '<pre>'; print_r($sections);die;
            }
            else
            {
                $total            = $this->Settings_model->blocks(array('count'=>true));
                $save['pb_order'] = ($total)+1;
            }
        }
        $response['id']         = $this->Settings_model->save_block($save);
        echo json_encode($response);
    }
    
    function update_block_position()
    {
        $response               = array();
        $response['error']      = 'false';
        $response['message']    = 'Block saved successfully';
        $structure              = $this->input->post('structure');
        parse_str($structure, $structure);
        if( isset($structure['block']) && !empty($structure['block']))
        {
            foreach ($structure['block'] as $order => $id) {
                $save             = array();
                $save['id']       = $id;
                $save['pb_order'] = ($order)+1;
                $this->Settings_model->save_block($save);
            }
        }
        echo json_encode($response);
    }
    
    function delete_block()
    {
        $response               = array();
        $response['message']    = '';
        $response['error']      = false;
        $block_id               = $this->input->post('block_id');
        
        if(!$block_id)
        {
            $response['message']    = 'Block id missing.';
            $response['error']      = true;
            echo json_encode($response);die;
        }
        $block = $this->Settings_model->block(array('id' => $block_id));
        if(empty($block))
        {
            $response['message'] = 'Requested block not found.';
            $response['error'] = true;
            echo json_encode($response);die;
        }
        
        if($this->Settings_model->delete_block(array('id'=>$block_id)))
        {
            $response['message'] = 'Block deleted successfully.';
            $response['error'] = false;
        }
        echo json_encode($response);die;
    }
    
    
    function edit_category()
    {
        $category_id  = $this->input->post('id');
        
        $response               = array();
        $response['message']    = '';
        $response['error']      = false;
        
        if($category_id > 0)
        {
            $response['category'] = $this->Category_model->category(array('id' => $category_id));
            $response['category']['ct_name'] = strip_tags($response['category']['ct_name']);
            if(empty($response['category']))
            {
                $response['message'] = 'Requested category not found.';
                $response['error'] = true;
            }
        }
        echo json_encode($response);die;   
    }
    
    function save_category()
    {
        $response               = array();
        $response['message']    = 'Category saved successfully';
        $response['error']      = false;

        $cat_name               = strip_tags($this->input->post('cat_name'));
        $cat_id                 = $this->input->post('cat_id');

        //validation starts
        $message = '';
        
        if(trim($cat_name) == '')
        {
            if(trim($this->input->post('cat_name')) == ''){
                $message .= 'Category name cannot be empty<br />';
            }else{
                $message .= 'Invalid category name<br />';
            }
            $response['error'] = true;
        }
        else
        {
            $category   = $this->Category_model->category(array('category_name'=>$cat_name, 'not_deleted'=> true));
            if($category)
            {                
                $message .= 'Category name is currently in use. Please use another<br />';
                $response['error'] = true; 
            }
        }
        $GLOBALS['category'] = '';
        if($response['error'] == false)
        {
            $check_category                       = $this->Category_model->category(array('id'=>$cat_id, 'not_deleted'=> true));
            
            //echo "<pre>";print_r($check_category);
            
            if(!$check_category)
            {  //echo "not cat";die;
                
                //configuring the slug for new category
                $this->load->helper('text');
                $slug   = $cat_name;
                $slug	= url_title(convert_accented_characters($slug), 'dash', TRUE);
                $this->load->model('Routes_model');

                $slug           = $this->Routes_model->validate_slug($slug);
                $route['slug']	= $slug;	
                $route_id       = $this->Routes_model->save($route);
                //End
                
                $category                   = array();
                
                $category['id']             = false;
                if($cat_name != '')
                {
                    
                    $category['ct_name']        = $cat_name;
                    $category['ct_status']      = '1';
                    $category['ct_account_id']  = $this->config->item('id');
                    $category['action_by']      = $this->auth->get_current_admin('id');
                    $category['action_id']      = $this->actions['create'];
                    $category['ct_route_id']    = $route_id;
                    $category['ct_slug']        = $slug;
                    $category['id']             = $this->Category_model->save($category);
                    $GLOBALS['category']        = $category['id'];
                    
                    $response['exist']          = '0';
                    
                    $route['id']                = $route_id;
                    $route['slug']              = $slug;
                    $route['route']             = 'category/view/'.$category['id'];
                    $route['r_account_id']	    = $this->config->item('id');
                    $route['r_item_type']       = 'category';
                    $route['r_item_id']         = $category['id'];
                    $this->Routes_model->save($route);
                }
            }else{
                $category_update            = array();
                //echo "cat update";die;
                $this->load->helper('text');
                $slug   = $cat_name;
                $slug	= url_title(convert_accented_characters($slug), 'dash', TRUE);
                $this->load->model('Routes_model');
                $slug           = $this->Routes_model->validate_slug($slug);
                
                
                
                $GLOBALS['category']             = $check_category['id'];
                $response['exist']               = '1';
                $category_update['ct_name']      = $cat_name;
                $category_update['id']           = $check_category['id'];
                $category_update['ct_slug']      = $slug;
                $category_update['action_by']    = $this->auth->get_current_admin('id');
                $category_update['action_id']    = $this->actions['update'];
                $category_update['updated_date'] = date("Y-m-d H:i:s");
                $category_update['ct_account_id']  = $this->config->item('id');
                $category_update['id']           = $this->Category_model->save($category_update);
               
                $route_update['id']                = $check_category['ct_route_id'];
                $route_update['slug']              = $slug;
                
                $this->Routes_model->save($route_update);
                
                //echo $category_update['id'];die; 
            }
        }
        else
        {
            $response['message'] = $message;            
            $response['error']   = true;            
        }
        $response['category']   = $this->Category_model->category(array('id' => $GLOBALS['category'] ));
        echo json_encode($response);
        //end
    }
    
    function check_category_connection()
    {
        $category_id = $this->input->post('cat_id');
        
        $response               = array();
        $response['error']      = false;
        $message                = '';
        $response['message']    = '';
        
        $check_courses = $this->Course_model->courses(array('category_id' => $category_id, 'not_deleted'=>true));
        
        //echo "<pre>";print_r($check_courses);die;
        if(!empty($check_courses))
        {
            $message .= ' - Courses<br/>';            
            $response['error']   = true; 
        }
        
        $check_challenge = $this->Challenge_model->challenges(array('category_id'=>$category_id));
        if(!empty($check_challenge))
        {
            $message .= ' - Challenges<br/>';            
            $response['error']   = true; 
        }
        
                
        $check_generate_test_questions = $this->Generate_test_model->questions(array('category_id'=>$category_id));
        if(!empty($check_daily_news))
        {
            $message .= ' - Questions<br/>';            
            $response['error']   = true; 
        }
        
        if($response['error'] ==  true){
            $response['message'] = 'Please unassign the following from this category<br/>';
            $response['message'] .= $message;
        }
        echo json_encode($response);
    }
    
    function delete_category()
    {
        $category_id = $this->input->post('cat_id');
        
        $category_delete = $this->Category_model->delete($category_id);
        
        $response['message'] = 'Category successfully deleted';            
        $response['error']   = false; 
        
        echo json_encode($response);
    }
    
    function migrate_category()
    {
        $migrate_from   = $this->input->post('previous_cat_id');
        $migrate_to     = $this->input->post('cat_id');
        
        $response   = array();
        $response['error'] = false;
        $response['message'] = 'Category migrated successfully';
        
        $course_data                   = array();
        $course_data['cb_category']    = $migrate_to;
        $course_data['updated_date']   = date('Y-m-d H:i:s');
        $course_data['action_by']      = $this->auth->get_current_admin('id');
        $course_data['action_id']      = $this->actions['update'];
        
        $course_migrate = $this->Settings_model->update_course_category($migrate_from,$course_data);
        
        $challenge_data                   = array();
        $challenge_data['cz_category']    = $migrate_to;
        $challenge_data['updated_date']   = date('Y-m-d H:i:s');
        $challenge_data['action_by']      = $this->auth->get_current_admin('id');
        $challenge_data['action_id']      = $this->actions['update'];
        
        $challenge_migrate = $this->Settings_model->update_challenge_category($migrate_from,$challenge_data);
        
        /*$terms_data                   = array();
        $terms_data['t_category']     = $migrate_to;
        $terms_data['updated_date']   = date('Y-m-d H:i:s');
        $terms_data['action_by']      = $this->auth->get_current_admin('id');
        $terms_data['action_id']      = $this->actions['update'];
        
        $terms_migrate = $this->Settings_model->update_terms_category($migrate_from,$terms_data);
        
        $dailynews_data                   = array();
        $dailynews_data['dnb_category']   = $migrate_to;
        $dailynews_data['updated_date']   = date('Y-m-d H:i:s');
        $dailynews_data['action_by']      = $this->auth->get_current_admin('id');
        $dailynews_data['action_id']      = $this->actions['update'];
        
        $dailynews_migrate = $this->Settings_model->update_dailynews_category($migrate_from,$dailynews_data);*/
        
        $question_data                   = array();
        $question_data['qc_parent_id']   = $migrate_to;
        $question_data['updated_date']   = date('Y-m-d H:i:s');
        $question_data['action_by']      = $this->auth->get_current_admin('id');
        $question_data['action_id']      = $this->actions['update'];
        
        $question_migrate = $this->Settings_model->update_question_category($migrate_from,$question_data);
        
        echo json_encode($response);
    }
    
    function get_category()
    {
        $category_id = $this->input->post('cat_id');
        $response = array();
        $response['error'] = false;
        
        $category_listing = $this->Category_model->categories(array('not_deleted'=>true));
        $response['filter_category'] = $category_listing;
        
        //echo "<pre>";print_r($category_listing);die;
        echo json_encode($response);
    }

    function get_question_category()
    {
        $category_id =       $this->input->post('parent_cat_id');
        $response = array();
        $response['error'] = false;
        if($category_id == 0){
            $category_listing = $this->Category_model->ques_categories();
            $response['filter_category'] = $category_listing;
        }else{
            $question_cat_list = $this->Category_model->question_categories(array('not_deleted'=>true,'parent_id'=>$category_id,'status'=>'1'));
            $response['filter_category'] = $question_cat_list;
        }
        
        //echo "<pre>";print_r($category_listing);die;
        echo json_encode($response);
    }
    
    function edit_question_topic()
    {
        $category_id  = $this->input->post('id');
        
        $response               = array();
        $response['message']    = '';
        $response['error']      = false;
        
        if($category_id > 0)
        {
            $response['que_category'] = $this->Category_model->question_category(array('id' => $category_id));
            
            //echo "<pre>";print_r($response['que_category']);die;
            if(empty($response['que_category']))
            {
                $response['message'] = 'Requested category not found.';
                $response['error'] = true;
            }
        }
        echo json_encode($response);die;  
    }
    
    function save_question_topic()
    {
        $response               = array();
        $response['message']    = 'Topic saved successfully';
        $response['error']      = false;

        $cat_name               = strip_tags($this->input->post('cat_name'));
        $parent_question_cat    = $this->input->post('new_parent_category');
        $parent_question_cat_old= $this->input->post('old_parent_category');
        $cat_id                 = $this->input->post('cat_id');

        //validation starts
        $message = '';
        if($parent_question_cat == '')
        {
            $message .= 'Please select parent category<br />';
            $response['error'] = true;
        }
        
        if(trim($cat_name) == '')
        {
            if(trim($this->input->post('cat_name')) == ''){
                $message .= 'Topic name cannot be empty<br />';
            }else{
                $message .= 'Invalid Topic name<br />';
            }
            
            $response['error'] = true;
        }
        else
        {
            $category = $this->Category_model->question_category(array('category_name' => $cat_name, 'parent_category' => $parent_question_cat));
            if($category)
            {                
                $message .= 'Category name is currently in use. Please use another<br />';
                $response['error'] = true; 
            }
        }
        $GLOBALS['category'] = '';
        if($response['error'] == false)
        {
            $check_category   = $this->Category_model->question_category(array('id' => $cat_id, 'parent_category' => $parent_question_cat_old));
            
            //echo "<pre>";print_r($check_category);die;
            $category                   = array();
            $category_update            = array();
            $category['id']             = false;
            if(!$check_category)
            {  
                $category['qc_category_name']   = $cat_name;
                $category['qc_parent_id']       = $parent_question_cat;
                $category['qc_status']          = '1';
                $category['qc_account_id']      = $this->config->item('id');
                $category['action_id']          = '1';
                $category['action_by']          = $this->auth->get_current_admin('id');
                $category['updated_date']       = date('Y-m-d H:i:s');
                $category['id']                 = $this->Category_model->save_question_category($category);
                $GLOBALS['category']            = $category['id'];
                
                $response['exist']              = '0';
                //echo '<pre>'; print_r($save);die;
            
            }else{
                
                $GLOBALS['category']                = $check_category['id'];
                $response['exist']                  = '1';
                $category_update['qc_category_name']= $cat_name;
                $category_update['qc_parent_id']    = $parent_question_cat;
                $category_update['id']              = $check_category['id'];
                $category_update['action_by']       = $this->auth->get_current_admin('id');
                $category_update['action_id']       = $this->actions['update'];
                $category_update['updated_date']    = date("Y-m-d H:i:s");
                $cat_update['id']                   = $this->Category_model->save_question_category($category_update);
                
            }
        }
        else
        {
            $response['message'] = $message;            
            $response['error']   = true;            
        }
        $response['ques_category']   = $this->Category_model->question_category(array('id' => $GLOBALS['category'] ));
        $response['ques_category']['count'] = $this->Category_model->ques_count(array('topic_id'=>$GLOBALS['category']));
        echo json_encode($response);
    }
    
    function check_question_topic_connection()
    {
        $topic_id = $this->input->post('cat_id');
        
        $response               = array();
        $response['error']      = false;
        $message                = '';
        $response['message']    = '';
        
        $check_questions = $this->Generate_test_model->questions(array('topic_id' => $topic_id,'not_deleted'=>true));
        
        //echo "<pre>";print_r($check_courses);die;
        if(!empty($check_questions))
        {
            $message .= ' - Questions<br/>';            
            $response['error']   = true; 
        }
        
        if($response['error'] ==  true){
            $response['message'] = 'Please unassign the following from this category<br/>';
            $response['message'] .= $message;
        }
        echo json_encode($response);
    }
    
    function delete_question_topic()
    {
        $topic_id = $this->input->post('cat_id');
        
        $data       = array();
        $data['qc_deleted'] = '1';
        
        $topic_delete = $this->Generate_test_model->delete_topic($topic_id, $data);
        
        $response['message'] = 'Topic successfully deleted';            
        $response['error']   = false; 
        
        echo json_encode($response);
    }

    function migrate_question_category(){

        $from_cat = $this->input->post('from');
        $to_cat   = $this->input->post('to');
        if(isset($from_cat)&&isset($to_cat)){
            $data = array();
            $data['from_count'] = $this->Category_model->ques_count(array('topic_id'=>$from_cat));
            if($from_cat != ''&&$to_cat != ''&&$from_cat != 0&&$to_cat != 0){
                $resp = $this->Category_model->migrate_ques_cat(array('from_cat'=>$from_cat,'to_cat'=>$to_cat));
                if($resp){
                    $data['to']      = $to_cat;
                    $data['from']    = $from_cat;
                    $data['to_count'] = $this->Category_model->ques_count(array('topic_id'=>$to_cat));
                    $data['error']   = false;
                    $data['message'] = 'Question migration success.';
                }else{
                    $data['error']   = true;
                    $data['message'] = 'Error occured during migration.';
                }
            }else{
                $data['error']   = true;
                $data['message'] = 'Error occured check selected categories.';
            }
            echo json_encode($data);
        }else{
            redirect(admin_url());
        }
    }

    public function save_payment_gateway_settings() 
    {   
        $response           = array();
        $active             = 0;
        $updated_setting    = $this->input->post();
        if(isset($updated_setting['has_enable']))
        {
           $active = 1; 
        }
        $payment_setting_key        = $this->settings->setting('payment_gateway');
        $payment_setting_id         = $payment_setting_key['id'];
        $payment_settings           = $this->Settings_model->setting($payment_setting_id);
        $setting_value              = json_decode($payment_settings['as_setting_value'], true);
        $payment_gateway_type       = $updated_setting['payment_gateway_type'];
        $setting_value['setting_value'][$payment_gateway_type]['creditionals'] = $updated_setting[$payment_gateway_type];
        $setting_value['setting_value'][$payment_gateway_type]['basic']['active'] = $active;
        $updated_setting_value      = array(
                                                "id" => $payment_setting_id, 
                                                "as_setting_value" => json_encode($setting_value)
                                      );
        $save_setting_value         = $this->Settings_model->save($updated_setting_value);
        $this->memcache->delete('setting_payment_gateway');//invalidate the memcache index
        if($save_setting_value)
        {
            $response['updated_setting_value']   = $setting_value;
            $response['error']                   = false;
            $response['message']                 = 'Payment gateway settings updated successfully.';
        }
        else
        {
            $response['error']                   = true;
            $response['message']                 = 'Payment gateway settings updated failed.';
        }
        echo json_encode($response);
    }

    function upload_mobile_banner_image()
    {
        $response                               = array();
        $response['error']                      = 'false';
        $response['message']                    = 'Mobile Banner uploaded successfully';
        $width                                  = $this->input->post('width');
        $height                                 = $this->input->post('height');
        if($width < $height)
        {
            $response['error']                  = 'true';
            $response['message']                = 'Please upload an image of min size 720 x 405 and max size 1280 x 720';
        }
        elseif($width < 720 && $height < 405 && $width > 1280 && $height > 720)
        {
            $response['error']                  = 'true';
            $response['message']                = 'Please upload an image of min size 720 x 405 and max size 1280 x 720';
        }
        else
        {
            $directory                              = mobile_banner_upload_path();
            if(!file_exists($directory))
            {
                mkdir($directory, 0777, true);
            }
            $config                                 = array();
            $config['upload_path']                  =  $directory;
            $config['allowed_types']                = 'jpg|jpeg';
            $config['encrypt_name']                 = true;
            $this->load->library('upload');
            $this->upload->initialize($config);
            $upload                                 = $this->upload->do_upload('file');
            if($upload)
            {
                $uploaded_data                      = $this->upload->data();

                //Converting to webp format start
                $file                               = $uploaded_data['full_path'];
                $image                              = imagecreatefromjpeg($file);
                ob_start();
                imagejpeg($image,NULL,100);
                $cont                               = ob_get_contents();
                ob_end_clean();
                $content                            = imagecreatefromstring($cont);
                imagewebp($content, $uploaded_data['file_path'].$uploaded_data['raw_name'].'.webp');
                //Converting to webp format end

                //saving mobile banner details in settings table
                $extension                          = $this->input->post('extension');
                $save                               = array();
                $save['mb_original_title']          = $uploaded_data['raw_name'].'.'.$extension;
                $save['mb_converted_title']          = $uploaded_data['raw_name'].'.webp';
                $save['mb_type']                    = 1;
                $save['mb_status']                  = 0;
                $save['mb_order']                   = 0;
                $save['mb_account_id']              = $this->config->item('id');
                $save['updated_date']               = date("Y-m-d H:i:s");

                $mobile_banner_id                   = $this->Settings_model->save_mobile_banner($save); 

                $this->Settings_model->update_mobile_banner_order($mobile_banner_id);

                //end
                $width                              = '90';
                $height                             = '90';
                $path_full                          = mobile_banner_crop_upload_path();
                $new_file                           = $this->crop_image($uploaded_data,$width,$height,$path_full,$extension);
                
                $has_s3                             = $this->settings->setting('has_s3');
                
                if( $has_s3['as_superadmin_value'] && $has_s3['as_siteadmin_value'] )
                {
                    $mobile_banner_uploaded_path                = mobile_banner_upload_path().$uploaded_data['file_name'];
                    $mobile_banner_webp_uploaded_path           = mobile_banner_upload_path().$uploaded_data['raw_name'].'.webp';
                    $mobile_banner_crop_uploaded_path           = mobile_banner_crop_upload_path().$new_file;
                    $files                                      = array();
                    $files[$mobile_banner_uploaded_path]        = $mobile_banner_uploaded_path;
                    $files[$mobile_banner_webp_uploaded_path]   = $mobile_banner_webp_uploaded_path;
                    $files[$mobile_banner_crop_uploaded_path]   = $mobile_banner_crop_uploaded_path;
                    uploadToS3Bulk($files);
                    unlink($mobile_banner_uploaded_path);
                    unlink($mobile_banner_webp_uploaded_path);
                    unlink($mobile_banner_crop_uploaded_path);
                }
                $response['mobile_banner_id']       = $mobile_banner_id;
                $response['user_image']             = mobile_banner_crop_path().$new_file;
            }
            else
            {
                $response['error']                  = 'true';
                $response['message']                = 'Error in uploading mobile banner image<br >'.$this->upload->display_errors();            
            }
        }
        echo json_encode($response);
    }

    function save_mobile_banner_order()
    {
        $structure                              = $this->input->post('structure');
        parse_str($structure, $structure);
        $this->Settings_model->save_mobile_banner_order($structure['mobile_banner']); 
    }

    function remove_mobile_banner()
    {
        $response                               = array();
        $response['error']                      = false;
        $mobile_banner_id                       = $this->input->post('mobile_banner_id');
        if($mobile_banner_id)
        {
            $banner_param                       = array();
            $banner_param['banner_id']          = $mobile_banner_id;
            $banner_param['select']             = 'mb_original_title,mb_converted_title';
            $mobile_banner                      = $this->Settings_model->get_mobile_banner($banner_param); 
            if(file_exists(mobile_banner_upload_path().$mobile_banner['mb_original_title']))
            {
                unlink(mobile_banner_upload_path().$mobile_banner['mb_original_title']);
            }
            if(file_exists(mobile_banner_upload_path().$mobile_banner['mb_converted_title']))
            {
                unlink(mobile_banner_upload_path().$mobile_banner['mb_converted_title']);
            }
            if(file_exists(mobile_banner_crop_upload_path().$mobile_banner['mb_original_title']))
            {
                unlink(mobile_banner_crop_upload_path().$mobile_banner['mb_original_title']);
            }
            
            if($this->Settings_model->delete_mobile_banner($mobile_banner_id))
            {
                $response['error']              = false;
                $response['message']            = 'Mobile banner removed successfully';
            }
            else
            {
                $response['error']              = true;
                $response['message']            = 'Sorry! Unable to remove mobile banner.';
            }
        }
        echo json_encode($response);
    }

    function update_mobile_banner()
    {
        $response                               = array();
        $response['error']                      = false;
        $mobile_banner_id                       = $this->input->post('mobile_banner_id');
        $mobile_banner_status                   = $this->input->post('mobile_banner_status');
        if($mobile_banner_id)
        {
            $banner_param                       = array();
            $banner_param['id']                 = $mobile_banner_id;
            $banner_param['mb_status']          = $mobile_banner_status;
            $updated_banner                     = $this->Settings_model->update_mobile_banner($banner_param);
            if($updated_banner)
            {
                $response['error']              = false;
                $response['message']            = 'Mobile banner updated successfully';
            }
            else
            {
                $response['error']              = true;
                $response['message']            = 'Unable to update mobile banner';
            }
        }
        echo json_encode($response);
    }
}



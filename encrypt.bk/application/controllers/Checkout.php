<?php

class Checkout extends CI_Controller {
    
    function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Kolkata');
        //$redirect	= $this->auth->is_logged_in(false, false, 'user');
        $redirect = $this->auth->is_logged_in_user(false, false, 'user');
        if (!$redirect)
        {
            redirect('login');
        }
        $this->actions        = $this->config->item('actions');
        $this->load->model(array('Bundle_model','Homepage_model', 'Payment_model', 'Course_model','User_model', 'Order_model'));
        //$this->lang->load('checkout');
        $this->load->library('Promocode');
        $this->lang->load('promocode');
    }
    
    public function standard($id) 
    {
        $data                               = array();
        $session                            = $this->auth->get_current_user_session('user');
        $data['session']                    = $session;
        $item                               = base64_decode($this->uri->segment(4));
        $notify_to[config_item('us_id')]    = array($session['id']);
        $objects                            = array();
        $objects['key']                     = 'course_'.$id;
        $callback                           = 'course_details';
        $params                             = array('id' => $id);
        $course                             = $this->memcache->get($objects, $callback, $params);
        $data['course']                     = $course;

        if($course['cb_has_self_enroll'] == 0)
        {
            $this->session->set_flashdata('error', 'This course doesnot support self enrollment contact Admin for more.');
            redirect('dashboard');
        }

        if(!$course['cb_is_free'] || $course['cb_is_free'] !='1')
        {
            $this->session->set_flashdata('error', 'This course doesnot support free enrollment contact Admin for more.');
            redirect('dashboard');
        }

        $course_subscription_date   = date("Y-m-d H:i:s");
        $course_validity            = (($course['cb_validity']) && $course['cb_validity'] > 0)?$course['cb_validity']:1;
        $course_startdate           = date("Y-m-d",time());  
        
        if($course['cb_access_validity'] == 2)
        {
            $course_enddate   = $course['cb_validity_date'];
        }
        else
        {
            $course_enddate   = ($course['cb_access_validity'] == 0) ? date('Y-m-d', strtotime('+3000 days')) : date('Y-m-d', time() + ($course_validity - 1) * 60 * 24 * 60);
        }

        $cs_save                            = array();
        
        if($subscription = $this->Payment_model->check_subscription(array('course_id'=>$id, 'user_id'=>$session['id'])))
        {
            $cs_save['id']                      = $subscription['id'];
            $cs_save['action_id']               = $this->actions['update'];
            $cs_save['updated_date']            = $course_subscription_date;
        }
        else
        {
            $cs_save['id']                      = false;
            $cs_save['action_id']               = $this->actions['create'];
        }
       
        $cs_save['cs_user_groups']          = $session['us_groups'];
        $cs_save['cs_course_id']            = $id;
        $cs_save['cs_user_id']              = $session['id'];
        $cs_save['cs_approved']             = '1';
        $cs_save['cs_subscription_date']    = $course_subscription_date;
        $cs_save['cs_start_date']           = $course_startdate;
        $cs_save['cs_end_date']             = $course_enddate;
        $cs_save['action_by']               = $session['id'];
        $cs_save['cs_course_validity_status']= $course['cb_access_validity'];
        
        if($this->Payment_model->save($cs_save))
        {
           
            //Invalidate cahe
            $this->memcache->delete('enrolled_'.$session['id']);
            $this->memcache->delete('mobile_enrolled_'.$session['id']);
            $this->memcache->delete('enrolled_item_ids_'.$session['id']);
            $this->Payment_model->remove_wishlist($cs_save);
        }
        
        $this->load->library('Ofapay');
        
        $param                  = array();
        $param['user_id']       = $session['id'];
        $param['item_id']       = $id;
        $param['amount']        = '0';
        $param['payment_mode']  = '2';
        $param['ph_item_name']  = $course['cb_title'];
        $param['ph_item_code']  = $course['cb_code'];
        $param['ph_payment_date']= date('Y-m-d H:i:s');
        
        $param['ph_user_details']  = array('name' => $session['us_name'], 'email' => $session['us_email'], 'phone'=> $session['us_phone']);
        $param['ph_status']        = '1';
        
        $this->ofapay->save_payment($param);
        
        //send notification to admin
        $this->load->model('Tutor_model');
        $tutors             = $this->Tutor_model->get_tutor_name_by_course($course['id']);

        $param              = array();
        $param['ids']       = array();
        $param['ids'][]     = $this->config->item('us_id');
        $mail_ids           = array($this->config->item('site_email'));
        
        if(!empty($tutors))
        {
            foreach($tutors as $tutor)
            {
                $param['ids'][] = $tutor['id'];
                $mail_ids[]     = $tutor['us_email'];
            }
        }
        //End
        $institute_admins = array();

        //Institute admin
        $institute              = $this->User_model->users(array( 'institute_id'=>$session['us_institute_id'],'role_id'=>'8','status'=>'1','not_deleted'=>true, 'select' => 'users.us_email,users.id'));
        if(!empty($institute)) 
        {
            // echo "<pre>";print_r($institute);exit;
            foreach($institute as $i_admin)
            {
                //$institute_admins[]  = $i_admin['id'];
                if($i_admin['id'])
                {
                    $notify_to[$i_admin['id']] = array($session['id']);
                }
                $mail_ids[]          = $i_admin['us_email'];
            }
        }
        //End ins admin
        
        //Send email using template.
        $template               = $this->ofabeemailer->template(array('email_code' => 'approve_enrollment'));
        $param_admin            = array();
        $param_admin['to'] 	    = $mail_ids;
        $param_admin['subject'] = $template['em_subject'];
        $contents               = array(
                                    'student_name' => $session['us_name'],
                                    'course_name'=> $course['cb_title']
                                    ,'site_name' => config_item('site_name')
                                    ,'approval_link' => admin_url('course/users').$course['id'].'?&filter=suspended&offset=1'
                                );
        $param_admin['body']      = $this->ofabeemailer->process_mail_content($contents, $template['em_message']);
        $send = $this->ofabeemailer->send_mail($param_admin);
        //End send email
        $this->session->set_flashdata('message', 'Your course subscription is successful.');

        //Notify to Admin,I admin and priveleged users.
        /*$preveleged_users = $this->accesspermission->previleged_users(array('module' => 'course'));
        foreach($preveleged_users as $preveleged_user)
        {
            $notify_to[$preveleged_user['id']] = array($session['id']);
        }
 
        $notify_to[$session['us_institute_id']] = array($session['id']);*/

        $objects                = array();
        $objects['key']         = 'course_notification_' . $id;
        $callback               = 'course_notification';
        $params                 = array('course_id' => $id);
        $discussion_forum       = $this->memcache->get($objects, $callback, $params);

        $preveleged_users       = $discussion_forum['preveleged_users'];

        foreach($preveleged_users as $preveleged_user)
        {
            $notify_to[$preveleged_user] = array($session['id']);
        }
        
        $this->load->library('Notifier');
        $this->notifier->push(
            array(
                'action_code' => 'course_subscribed',
                'assets' => array('course_name' => $course['cb_title'],'student_name'=>$session['us_name'],'course_id' => $course['id']),
                'target' => $course['id'],
                'individual' => false,
                'push_to' => $notify_to
            )
        );
        //End notifying.

        /*Log creation*/
        $user                               = $session;
        $user_data                          = array();
        $user_data['user_id']               = $user['id'];
        $user_data['username']              = $user['us_name'];
        $user_data['useremail']              = $user['us_email'];
        $user_data['user_type']             = $user['us_role_id'];
        $user_data['phone_number']          = $user['us_phone'];
        $message_template                   = array();
        $message_template['username']       = $user['us_name'];
        $message_template['course_name']    = ' '.$course['cb_title'];
        $triggered_activity                 = 'course_subscribed';
        log_activity($triggered_activity, $user_data, $message_template);
        $this->memcache->delete('all_courses');
        $this->memcache->delete('sales_manager_all_sorted_courses');
        $this->memcache->delete('popular_courses');
        $this->memcache->delete('featured_courses');
        redirect('dashboard');
    }



    public function instamojo_request($course_id = false)
    {
        $user   = $this->auth->get_current_user_session('user');
        if(!$user)
        {
            redirect('dashboard');
        }
        $this->load->model(array('Course_model'));
        $objects            = array();
        $objects['key']     = 'course_'.$course_id;
        $callback           = 'course_details';
        $params             = array('id' => $course_id);
        $course             = $this->memcache->get($objects, $callback, $params);
        if($course['cb_has_self_enroll'] == 0)
        {
            $this->session->set_flashdata('error', 'This course doesnot support self enrollment contact Admin for more.');
            redirect('dashboard');
        }
        $course_price       = ($course['cb_discount']!=0)?$course['cb_discount']:$course['cb_price'];
        if($course['cb_tax_method']=='1')
        {
            $gst_setting         = $this->settings->setting('has_tax');
            $cgst                = ($gst_setting['as_setting_value']['setting_value']->cgst != '')?$gst_setting['as_setting_value']['setting_value']->cgst:'0';
            $sgst                = ($gst_setting['as_setting_value']['setting_value']->sgst != '')?$gst_setting['as_setting_value']['setting_value']->sgst:'0';
            $sgst_price          = ($sgst / 100) * $course_price;
            $cgst_price          = ($cgst / 100) * $course_price;
            $total_course_price  = $course_price+$sgst_price+$cgst_price;
        }
        else 
        {
            $total_course_price  = $course_price;
        }
        $this->session->set_userdata(array('instamojo_course_id' => $course_id));
        if(!$course)
        {
            redirect('dashboard');
        }

        $payment_keys           = $this->settings->setting('has_payment');
        $params                 = array();
        $params['api_key']      = $payment_keys['as_setting_value']['setting_value']->instamojo->credentials->key;
        $params['auth_token']   = $payment_keys['as_setting_value']['setting_value']->instamojo->credentials->secret;
        $params['endpoint']     = 'https://test.instamojo.com/api/1.1/';
        $this->load->library('instamojo',$params);
        $response = $this->instamojo->paymentRequestCreate(array(
            "purpose" => "Purchasing course ".$course['cb_title'],
            "amount" => $total_course_price,
            "send_email" => false,
            "email" => $user['us_email'],
            "redirect_url" => site_url('checkout/instamojo_response')
        ));
        header('Location: '.$response['longurl'], TRUE, $code);
    }
        
    public function instamojo_response()
    {
        $session                = $this->auth->get_current_user_session('user');
        $response               = array();
        $response['error']      = false;
        $course_id              = $this->session->userdata('instamojo_course_id');
        $payment_keys           = $this->settings->setting('has_payment');
        $params                 = array();
        $params['api_key']      = $payment_keys['as_setting_value']['setting_value']->instamojo->credentials->key;
        $params['auth_token']   = $payment_keys['as_setting_value']['setting_value']->instamojo->credentials->secret;
        $params['endpoint']     = 'https://test.instamojo.com/api/1.1/';
        $this->load->library('instamojo',$params);
        $payment_objects        = $this->input->get();
        $payment_request_id     = isset($payment_objects['payment_request_id'])?$payment_objects['payment_request_id']:false;
        $payment_id             = isset($payment_objects['payment_id'])?$payment_objects['payment_id']:false;
        
        $instamojo_response     = $this->instamojo->paymentRequestPaymentStatus($payment_request_id, $payment_id);
        
        $payment_details        = $instamojo_response['payment'];
       
        
        $objects                = array();
        $objects['key']         = 'course_'.$course_id;
        $callback               = 'course_details';
        $params                 = array('id' => $course_id);
        $course                 = $this->memcache->get($objects, $callback, $params);

        $user_details                               = array();
        $user_details['name']                       = $session['us_name'];
        $user_details['email']                      = $session['us_email'];
        $user_details['phone']                      = $session['us_phone'];

        $payment_param                              = array();
        $payment_param['id']                        = false;
        $payment_param['ph_user_id']                = $session['id'];
        $payment_param['ph_user_details']           = json_encode($user_details);//1
        $payment_param['ph_item_id']                = $course['id'];
        $payment_param['ph_item_type']              = '1';
        $payment_param['ph_item_other_details']    = json_encode($course);
        $payment_param['ph_item_name']              = $course['cb_title'];
        $payment_param['ph_item_base_price']        = $course['cb_price'];
        $payment_param['ph_item_discount_price']    = $course['cb_discount'];
        $payment_param['ph_tax_type']               = $course['cb_tax_method'];
        $course_price                               = ($course['cb_discount']!=0)?$course['cb_discount']:$course['cb_price'];
        $gst_setting                                = $this->settings->setting('has_tax');
        $cgst                                       = ($gst_setting['as_setting_value']['setting_value']->cgst != '')?$gst_setting['as_setting_value']['setting_value']->cgst:'0';
        $sgst                                       = ($gst_setting['as_setting_value']['setting_value']->sgst != '')?$gst_setting['as_setting_value']['setting_value']->sgst:'0';
        //$sgst_price                                 = ($sgst / 100) * $course_price;
        //$cgst_price                                 = ($cgst / 100) * $course_price;
        //cb_tax_method = 1 is exclusive
        //250/100 + 12*12 inclusive tax amount
        //250/100 + 12*100 inclusive taxable amount
        
        if($bundle['cb_tax_method'] != '1')
        { //inclusive
            $sgst_price                                 = $course_price / (100 + $sgst) * $sgst;//($sgst / 100) * $bundle_price;
            $cgst_price                                 = $course_price / (100 + $cgst) * $cgst;
            $totalTaxPercentage                         = $cgst + $sgst;
            $total_course_price                         = $course_price;
            
        }
        else
        {
            $sgst_price                                 = ($sgst / 100) * $course_price;
            $cgst_price                                 = ($cgst / 100) * $course_price;
            $total_course_price                         = $course_price + ($sgst_price + $cgst_price);   
        }

        $payment_tax_object                         = array();
        $payment_tax_object['sgst']['percentage']   = $sgst;
        $payment_tax_object['sgst']['amount']       = round($sgst_price, 2); 
        $payment_tax_object['cgst']['percentage']   = $cgst;
        $payment_tax_object['cgst']['amount']       = round($cgst_price, 2); 
        $payment_param['ph_tax_objects']            = json_encode($payment_tax_object);
        
        
        $payment_param['ph_item_amount_received']   = $payment_details['amount'];//round($total_course_price, 2);//
        $payment_param['ph_payment_mode']           = '1';
        $transaction_details                        = array();
        $transaction_details['transaction_id']      = $payment_details['payment_id'];
        $transaction_details['bank']                = $payment_details['billing_instrument'];
        $payment_param['ph_transaction_id']         = $payment_details['payment_id'];
        $payment_param['ph_transaction_details']    = json_encode($transaction_details);
        $payment_param['ph_account_id']             = config_item('id');
        $payment_param['ph_payment_gateway_used']   = 'instamojo';
        $payment_param['ph_status']                 = '0';
        $payment_param['ph_payment_date']           = date('Y-m-d H:i:s');

        $insert_id                                  = $this->session->userdata('ofabee_payment_id');
        if(empty($insert_id))
        {
            $insert_id                              = $this->Payment_model->save_history($payment_param);
        }
        
        if($insert_id)
        {
            $order_id                               = date('Y').date('m').date('d').$insert_id;
            
            $order_param                            = array();
            $payment_param['id']                    = $insert_id;
            $payment_param['ph_order_id']           = $order_id;
            if($this->Payment_model->save_history($payment_param))
            {
                $response['message']        = 'Payment has been received successfully!';
                $course_subscription_date   = date("Y-m-d H:i:s");
                $course_validity            = (($course['cb_validity']) && $course['cb_validity'] > 0)?$course['cb_validity']:1;
                $course_startdate           = date("Y-m-d",time());  
                if($course['cb_access_validity'] == 2)
                {
                    $course_enddate   = $course['cb_validity_date'];
                }
                else
                {
                    $course_enddate   = ($course['cb_access_validity'] == 0) ? date('Y-m-d', strtotime('+3000 days')) : date('Y-m-d', time() + ($course_validity - 1) * 60 * 24 * 60);
                }
        
                $cs_save                                = array();
                if($subscription = $this->Payment_model->check_subscription(array('course_id'=>$course['id'], 'user_id'=>$session['id'])))
                {
                    $cs_save['id']                      = $subscription['id'];
                    $cs_save['action_id']               = $this->actions['update'];
                    $cs_save['updated_date']            = $course_subscription_date;
                }
                else
                {
                    $cs_save['id']                      = false;
                    $cs_save['action_id']               = $this->actions['create'];
                }
                $cs_save['cs_user_groups']          = $session['us_groups'];
                $cs_save['cs_course_id']            = $course['id'];
                $cs_save['cs_user_id']              = $session['id'];
                $cs_save['cs_approved']             = '1';
                $cs_save['cs_subscription_date']    = $course_subscription_date;
                $cs_save['cs_start_date']           = $course_startdate;
                $cs_save['cs_end_date']             = $course_enddate;
                $cs_save['action_by']               = $session['id'];
                $cs_save['cs_course_validity_status']= $course['cb_access_validity'];
                if($this->Payment_model->save($cs_save))
                {
                    //Invalidate cahe
                    $this->memcache->delete('enrolled_'.$session['id']);
                    $this->memcache->delete('mobile_enrolled_'.$session['id']);
                    $this->Payment_model->remove_wishlist($cs_save);
                }
                //send notification to admin
                $this->load->model('Tutor_model');
                $tutors             = $this->Tutor_model->get_tutor_name_by_course($course['id']);
                //echo '<pre>';print_r($tutors);die;
                $param              = array();
                $param['ids']       = array();
                $param['ids'][]     = $this->config->item('us_id');
                $mail_ids           = array($this->config->item('site_email'));
                if(!empty($tutors))
                {
                    foreach($tutors as $tutor)
                    {
                        $param['ids'][] = $tutor['id'];
                        $mail_ids[]     = $tutor['us_email'];
                    }
                }
                //End
                $institute_admins = array();

                //Institute admin
                $institute              = $this->User_model->users(array( 'institute_id'=>$session['us_institute_id'],'role_id'=>'8','status'=>'1','not_deleted'=>true, 'select' => 'users.us_email,users.id'));
                if(!empty($institute)) 
                {
                    // echo "<pre>";print_r($institute);exit;
                    foreach($institute as $i_admin)
                    {
                        //$institute_admins[]  = $i_admin['id'];
                        if($i_admin['id'])
                        {
                            $notify_to[$i_admin['id']] = array($session['id']);
                        }
                        $mail_ids[]          = $i_admin['us_email'];
                    }
                }
                //End ins admin
                
                //Send email using template.
                $template               = $this->ofabeemailer->template(array('email_code' => 'approve_enrollment'));
                $param_admin            = array();
                $param_admin['to'] 	    = $mail_ids;
                $param_admin['subject'] = $template['em_subject'];
                $contents               = array(
                                            'student_name' => $session['us_name'],
                                            'course_name'=> $course['cb_title']
                                            ,'site_name' => config_item('site_name')
                                        );
                $param_admin['body']      = $this->ofabeemailer->process_mail_content($contents, $template['em_message']);
                $send = $this->ofabeemailer->send_mail($param_admin);
                //End send email
                $this->session->set_flashdata('message', 'Your course subscription is successful ');

                //Notify to Admin,I admin and priveleged users.
                /*$preveleged_users = $this->accesspermission->previleged_users(array('module' => 'course'));
                foreach($preveleged_users as $preveleged_user)
                {
                    $notify_to[$preveleged_user['id']] = array($session['id']);
                }
        
                $notify_to[$session['us_institute_id']] = array($session['id']);*/

                $objects                = array();
                $objects['key']         = 'course_notification_' . $course_id;
                $callback               = 'course_notification';
                $params                 = array('course_id' => $course_id);
                $discussion_forum       = $this->memcache->get($objects, $callback, $params);

                $preveleged_users       = $discussion_forum['preveleged_users'];

                foreach($preveleged_users as $preveleged_user)
                {
                    $notify_to[$preveleged_user] = array($session['id']);
                }
                
                $this->load->library('Notifier');
                $this->notifier->push(
                    array(
                        'action_code' => 'course_subscribed',
                        'assets' => array('course_name' => $course['cb_title'],'student_name'=>$session['us_name'],'course_id' =>$course_id),
                        'target' => $course['id'],
                        'individual' => false,
                        'push_to' => $notify_to
                    )
                );
                //End notifying.
                $notification_param                 = array();
                $notification_param['student_name'] = $session['us_name'];
                $notification_param['course_name']  = $course['cb_title'];
                $notification_param['users']        = $notify_to;
                $notification_param['course_id']    = $course['id'];
                $this->send_notification($notification_param);
                
                /*Log creation*/
                $user                               = $session;
                $user_data                          = array();
                $user_data['user_id']               = $user['id'];
                $user_data['username']              = $user['us_name'];
                $user_data['useremail']              = $user['us_email'];
                $user_data['user_type']             = $user['us_role_id'];
                $user_data['phone_number']          = $user['us_phone'];
                $message_template                   = array();
                $message_template['username']       = $user['us_name'];
                $message_template['course_name']    = ' '.$course['cb_title'];
                $triggered_activity                 = 'course_subscribed';
                log_activity($triggered_activity, $user_data, $message_template);
            }
        }
        $this->session->unset_userdata('instamojo_course_id');
        $this->memcache->delete('all_courses');
        $this->memcache->delete('sales_manager_all_sorted_courses');
        redirect('dashboard');
        // echo json_encode($response);die();    
    }

    function payment_request($item_id = false,$item_type = false)
    {
        $payment_keys        = $this->settings->setting('payment_gateway');
        $razorpay            = $payment_keys['as_setting_value']['setting_value']->razorpay;
        $razorpay_values     = json_decode(json_encode($razorpay), true);
        if($razorpay_values['creditionals']['key'] != '' && $razorpay_values['creditionals']['secret'] != '' && $razorpay_values['basic']['active'] != 0)
        {
            $this->razorpay_request($item_id,$razorpay,$item_type);
        }
        else
        {
            $this->load->view($this->config->item('theme').'/payment_pending');
        }
        //echo '<pre>'; print_r($razorpay_values);die;
       
    }

    function razorpay_request($item_id = false,$payment_keys=array(),$type= false)
    {
        $this->load->library('user_agent');
        //$this->session->unset_userdata('promocode');
        $this->session->set_userdata(array('razorpay_item_id' => $item_id));
        $this->session->set_userdata(array('razorpay_item_type' => $type));
        if(!$item_id)
        {
            redirect('dashboard');
        }
        $user                = $this->auth->get_current_user_session('user');
        switch ($type) 
        {
            case "1":
                $objects                        = array();
                $objects['key']                 = 'course_'.$item_id;
                $callback                       = 'course_details';
                $params                         = array('id' => $item_id);
                $course                         = $this->memcache->get($objects, $callback, $params);
                $item_name                      = $course['cb_title'];
                $item_code                      = $course['cb_code'];
                $item_base_price                = $course['cb_price'];
                $item_discount_price            = $course['cb_discount'];
                $tax_type                       = $course['cb_tax_method'];
                $item_price                     = ($course['cb_discount']!=0)?$course['cb_discount']:$course['cb_price'];
                $item_image                     = $course['cb_image'];
                $item_image                     = (($item_image == 'default.jpg') ? default_course_path() : course_path(array('course_id' => $item_id))) . $item_image;
                $item_details                   = $course;
                break;
            case "2":
                $objects                        = array();
                $objects['key']                 = 'bundle_'.$item_id;
                $callback                       = 'bundle_details';
                $params                         = array('id' => $item_id);
                $bundle                         = $this->memcache->get($objects, $callback, $params);
                $item_name                      = $bundle['c_title'];
                $item_code                      = $bundle['c_code'];
                $item_base_price                = $bundle['c_price'];
                $item_discount_price            = $bundle['c_discount'];
                $tax_type                       = $bundle['c_tax_method'];
                $item_price                     = ($bundle['c_discount']!=0)?$bundle['c_discount']:$bundle['c_price']; 
                $item_image                     = $bundle['c_image'];
                $item_image                     = (($item_image == 'default.jpg') ? default_catalog_path() : catalog_path(array('bundle_id' => $item_id))) . $item_image;
                
                $item_details                   = array(
                                'id'                => $bundle['id'],
                                'c_title'           => $bundle['c_title'],
                                'c_code'            => $bundle['c_code'],
                                //'c_courses'         => json_decode($bundle['c_courses']),
                                'c_courses'         => $bundle['c_courses'],
                                'c_access_validity' => $bundle['c_access_validity'],
                                'c_validity'        => $bundle['c_validity'],
                                'c_validity_date'   => $bundle['c_validity_date'],
                                'c_price'           => $bundle['c_price'],
                                'c_discount'        => $bundle['c_discount'],
                                'c_tax_method'      => $bundle['c_tax_method']
                            );
                break;
        }

        $promocode                              = $this->session->userdata('promocode');
        if($promocode)
        {
            $user_details                       = array();
            $user_details['id']                 = $user['id'];
            $user_details['name']               = $user['us_name'];
            $user_details['email']              = $user['us_email'];
            $user_detail[$user['id']]           = (isset($user_details))?$user_details:array();

            $param                              = array();
            $param['promocode']                 = $promocode;
            $param['user_details']              = json_encode($user_detail);
            $promocode_response                 = $this->promocode->check_valid_promocode($param);
            if($promocode_response['header']['success'])
            {
                $discout_type                   = (isset($promocode_response['body']['promocode']['pc_discount_type']))?$promocode_response['body']['promocode']['pc_discount_type']:1;
                if($discout_type==1) 
                {
                    $discount_rate    = ($promocode_response['body']['promocode']['pc_discount_rate']!=NULL)?$promocode_response['body']['promocode']['pc_discount_rate']:0;
                } 
                else 
                {
                    $discount_percentage  = ($promocode_response['body']['promocode']['pc_discount_rate']!=NULL)?$promocode_response['body']['promocode']['pc_discount_rate']:0;
                    $discount_rate        = ($discount_percentage!=0)?round((($discount_percentage/100) * $item_price),2):0;
                }
                
                $item_price               = ($discount_rate<$item_price)?($item_price-$discount_rate):0;           
                
            }
        }

        if($tax_type == '1')
        {
            $gst_setting         = $this->settings->setting('has_tax');
            $cgst                = ($gst_setting['as_setting_value']['setting_value']->cgst != '') ? $gst_setting['as_setting_value']['setting_value']->cgst:'0';
            $sgst                = ($gst_setting['as_setting_value']['setting_value']->sgst != '') ? $gst_setting['as_setting_value']['setting_value']->sgst:'0';
            $sgst_price          = ($sgst / 100) * $item_price;
            $cgst_price          = ($cgst / 100) * $item_price;
            $total_price         = $item_price+$sgst_price+$cgst_price;
        }
        else 
        {
            $total_price  = $item_price;
        }
        
                //inserting pending payment hostory
                $this->load->model('order_model');
                $pending_order = $this->order_model->get_pending_payment(array('id' => $user['id'], 'item_id' => $item_id));
                if(isset($pending_order['id']))
                {
                    $insert_id                                  = $pending_order['id'];
                    $order_id                                   = $pending_order['ph_order_id'];
                $this->session->set_userdata(array('ofabee_payment_id'   => $insert_id, 'ofabee_order_id' => $order_id));
                }
                else
                {
                    $user_details                               = array();
                    $user_details['name']                       = $user['us_name'];
                    $user_details['email']                      = $user['us_email'];
                    $user_details['phone']                      = $user['us_phone'];

                    $payment_param                              = array();
                    $payment_param['id']                        = false;
                    $payment_param['ph_user_id']                = $user['id'];
                    $payment_param['ph_user_details']           = json_encode($user_details);//2 
                    $payment_param['ph_item_other_details']    = json_encode($item_details);
                    $payment_param['ph_promocode']              = '';
                    $payment_param['ph_item_id']                = $item_id;
                    $payment_param['ph_item_type']              = $type;
                    $payment_param['ph_item_name']              = $item_name;
                    $payment_param['ph_item_base_price']        = $item_base_price;
                    $payment_param['ph_item_discount_price']    = $item_discount_price;
                    $payment_param['ph_tax_type']               = $tax_type;
                    $payment_param['ph_item_code']              = $item_code;

                    $gst_setting                                = $this->settings->setting('has_tax');
                    $cgst                                       = ($gst_setting['as_setting_value']['setting_value']->cgst != '')?$gst_setting['as_setting_value']['setting_value']->cgst:'0';
                    $sgst                                       = ($gst_setting['as_setting_value']['setting_value']->sgst != '')?$gst_setting['as_setting_value']['setting_value']->sgst:'0';
                    //$sgst_price                                 = ($sgst / 100) * $course_price;
                    //$cgst_price                                 = ($cgst / 100) * $course_price;
                    //cb_tax_method = 1 is exclusive
                    //250/100 + 12*12 inclusive tax amount
                    //250/100 + 12*100 inclusive taxable amount
                    
                    if($tax_type != '1')
                    { //inclusive
                        $sgst_price                                 = $item_price / (100 + $sgst) * $sgst;//($sgst / 100) * $bundle_price;
                        $cgst_price                                 = $item_price / (100 + $cgst) * $cgst;
                        $totalTaxPercentage                         = $cgst + $sgst;
                        $total_course_price                         = $item_price;
                    }
                    else
                    {
                        $sgst_price                                 = ($sgst / 100) * $item_price;
                        $cgst_price                                 = ($cgst / 100) * $item_price;
                        $total_course_price                         = $item_price + ($sgst_price + $cgst_price);
                    }

                    $payment_tax_object                         = array();
                    $payment_tax_object['sgst']['percentage']   = $sgst;
                    $payment_tax_object['sgst']['amount']       = round($sgst_price, 2); 
                    $payment_tax_object['cgst']['percentage']   = $cgst;
                    $payment_tax_object['cgst']['amount']       = round($cgst_price, 2); 

                    $payment_param['ph_tax_objects']            = json_encode($payment_tax_object);
                    $payment_param['ph_item_amount_received']   = '0';
                    $payment_param['ph_payment_mode']           = '1';

                    $payment_param['ph_transaction_id']         = '';
                    $payment_param['ph_transaction_details']    = '';
                    $payment_param['ph_account_id']             = config_item('id');;
                    $payment_param['ph_payment_gateway_used']   = 'razorpay';
                    $payment_param['ph_status']                 = '2';
                    $payment_param['ph_payment_date']           = date('Y-m-d H:i:s');
                    
                    $insert_id                                  = $this->Payment_model->save_history($payment_param);
                    if($insert_id){
                        $order_id  = date('Y').date('m').date('d').$insert_id;
                        $this->session->set_userdata(array('ofabee_payment_id'   => $insert_id, 'ofabee_order_id' => $order_id));
                        $order_param                            = array();
                        $order_param['id']                      = $insert_id;
                        $order_param['ph_order_id']             = $order_id;
                        $this->Payment_model->save_history($order_param);
                    }
                }
                //inserting pending payment hostory

        if($total_price >= 1){
            $api_key             = $payment_keys->creditionals->key;
            $auth_token          = $payment_keys->creditionals->secret;

            $config              = array(
                                            'key' => $api_key,
                                            'secret' => $auth_token
                                    );
            $this->load->library('razorpay', $config);
            $total_price        = ($total_price>1)?$total_price:1;
            $total_price        = round($total_price, 2);
            $order_object       = array(
                                        'amount'          => $total_price * 100, // 2000 rupees in paise
                                        'payment_capture' => 1 // auto capture
                                    );
            $order              = $this->razorpay->create_order($order_object);
            $request            = array(
                                    "key"               => $config['key'],
                                    "amount"            => $order_object['amount'],
                                    "name"              => $item_name,
                                    "description"       => "",
                                    "image"             => $item_image,
                                    "prefill"           => array(
                                                            "name"              => $user["us_name"],
                                                            "email"             => $user["us_email"],
                                                            "contact"           => $user["us_phone"],
                                                            ),
                                    "notes"             => array(
                                                            "address"           => " "
                                                            ),
                                    "theme"             => array(
                                                            "color"             => "#F37254"
                                                            ),
                                    "order_id"          => $order['order_id'],
                                );
            $data               = array('request' => json_encode($request));
            $data['refer']      = $this->agent->referrer();
            $this->load->view($this->config->item('theme').'/razorpay/razorpay_checkout', $data);
            
        }
        else
        {
            $param              = array();
            $param['item_type'] = $type;
            $param['item_id']   = $item_id;
            $this->discount_enrollment($param);
        }
    }

    function razorpay_response()
    {    
        $session             = $this->auth->get_current_user_session('user');
        $item_id             = $this->session->userdata('razorpay_item_id');
        $item_type           = $this->session->userdata('razorpay_item_type');
        
        $promocode_details   = array();
        switch ($item_type) 
        {
            case "1":
                $item_type_label    = 'course';
                $objects            = array();
                $objects['key']     = 'course_'.$item_id;
                $callback           = 'course_details';
                $params             = array('id' => $item_id);
                $course             = $this->memcache->get($objects, $callback, $params);
                $item_name          = $course['cb_title'];
                $item_code          = $course['cb_code'];
                $item_base_price    = $course['cb_price'];
                $item_discount_price= $course['cb_discount'];
                $tax_type           = $course['cb_tax_method'];
                $item_price         = ($course['cb_discount']!=0)?$course['cb_discount']:$course['cb_price'];
                $item_details       = $course;
                break;
            case "2":
                $subscription_param = array();
                $item_type_label    = 'bundle';
                $objects            = array();
                $objects['key']     = 'bundle_'.$item_id;
                $callback           = 'bundle_details';
                $params             = array('id' => $item_id);
                $bundle             = $this->memcache->get($objects, $callback, $params);
                $item_name          = $bundle['c_title'];
                $item_code          = $bundle['c_code'];
                $item_base_price    = $bundle['c_price'];
                $item_discount_price= $bundle['c_discount'];
                $tax_type           = $bundle['c_tax_method'];
                $item_price         = ($bundle['c_discount']!=0)?$bundle['c_discount']:$bundle['c_price'];
                $subscription_param['bs_bundle_details'] = array(
                                                                'id'                => $bundle['id'],
                                                                'c_title'           => $bundle['c_title'],
                                                                'c_code'            => $bundle['c_code'],
                                                                'c_courses'         => json_decode($bundle['c_courses']),
                                                                'c_access_validity' => $bundle['c_access_validity'],
                                                                'c_validity'        => $bundle['c_validity'],
                                                                'c_validity_date'   => $bundle['c_validity_date'],
                                                                'c_price'           => $bundle['c_price'],
                                                                'c_discount'        => $bundle['c_discount'],
                                                                'c_tax_method'      => $bundle['c_tax_method']
                                                            );
                $item_details       = $subscription_param['bs_bundle_details'];
                break;
        }

        $promocode                                 = $this->session->userdata('promocode');
        //echo '<pre>';print_r($promocode);die();
        if($promocode)
        {
            $user_detail                           = array();
            $user_details                          = array();
            $user_details['id']                    = $session['id'];
            $user_details['name']                  = $session['us_name'];
            $user_details['email']                 = $session['us_email'];
            $user_details['phone']                 = $session['us_phone'];
            $user_details['itemType']              = $item_type_label;
            $user_details['itemName']              = $item_name;
            $user_details['applied_on']            = date('d-m-Y H:i:s');
            $user_detail[$session['id']]           = (isset($user_details))?$user_details:array();
            $param                                 = array();
            $param['promocode']                    = $promocode;
            $param['user_details']                 = json_encode($user_detail);
            $promocode_response                    = $this->promocode->check_valid_promocode($param);
            
            if($promocode_response['header']['success'])
            {
                
                $discout_type                   = (isset($promocode_response['body']['promocode']['pc_discount_type']))?$promocode_response['body']['promocode']['pc_discount_type']:1;
                if($discout_type==1) 
                {
                    $discount_percentage        = 0;
                    $discount_rate              = ($promocode_response['body']['promocode']['pc_discount_rate']!=NULL)?$promocode_response['body']['promocode']['pc_discount_rate']:0;
                } 
                else 
                {
                    $discount_percentage        = ($promocode_response['body']['promocode']['pc_discount_rate']!=NULL)?$promocode_response['body']['promocode']['pc_discount_rate']:0;
                    $discount_rate              = ($discount_percentage!=0)?round((($discount_percentage/100) * $item_price),2):0;
                } 
                
                $item_price                     = ($discount_rate<$item_price)?($item_price-$discount_rate):0;           
               
                /* save promocode details */
                $promocode_details['promocode']           = $promocode;
                $promocode_details['discout_type']        = $discout_type;
                $promocode_details['discount_percentage'] = $discount_percentage;
                $promocode_details['discount_rate']       = $discount_rate;
                $promocode_details['item_net_amount']     = $item_price;
                $save_promocode                           = $this->promocode->record_promocode_usage($param);
            }
        }
        //echo '<pre>';print_r($promocode_details);die();
        

        $response            = array();
        $response['error']   = false;
        $razorpay_order_id   = $this->session->userdata('razorpay_order_id');
        if(!$razorpay_order_id)
        {
            redirect('dashboard');exit;
        }
        $payment_keys        = $this->settings->setting('payment_gateway');
        $api_key             = $payment_keys['as_setting_value']['setting_value']->razorpay->creditionals->key;
        $auth_token          = $payment_keys['as_setting_value']['setting_value']->razorpay->creditionals->secret;
        $config              = array(
                                        'key' => $api_key,
                                        'secret' => $auth_token
                                );
        $this->load->library('razorpay', $config);
        $payload                        = $this->input->post();
        $payload['razorpay_order_id']   = $razorpay_order_id;
        $razorpay_response              = $this->razorpay->verify_payment_signature($payload);
        $payment_response               = $razorpay_response['payment'];
        $payment_details                = array();
        foreach($payment_response as $key=>$value) 
        {
            $payment_details[$key]      = $value;
        }
        $user_details                               = array();
        $user_details['name']                       = $session['us_name'];
        $user_details['email']                      = $session['us_email'];
        $user_details['phone']                      = $session['us_phone'];

        $payment_param                              = array();
        $payment_param['id']                        = false;
        $payment_param['ph_user_id']                = $session['id'];
        $payment_param['ph_user_details']           = json_encode($user_details);//3
        $payment_param['ph_promocode']              = json_encode($promocode_details);
        $payment_param['ph_item_other_details']     = json_encode($item_details);
        $payment_param['ph_item_id']                = $item_id;
        $payment_param['ph_item_type']              = $item_type;
        $payment_param['ph_item_name']              = $item_name;
        $payment_param['ph_item_base_price']        = $item_base_price;
        $payment_param['ph_item_discount_price']    = $item_discount_price;
        $payment_param['ph_tax_type']               = $tax_type;
        $payment_param['ph_item_code']              = $item_code;
        $course_price                               = ($item_discount_price != 0 ) ? $item_discount_price : $item_base_price;
        $gst_setting                                = $this->settings->setting('has_tax');
        $cgst                                       = ($gst_setting['as_setting_value']['setting_value']->cgst != '')?$gst_setting['as_setting_value']['setting_value']->cgst:'0';
        $sgst                                       = ($gst_setting['as_setting_value']['setting_value']->sgst != '')?$gst_setting['as_setting_value']['setting_value']->sgst:'0';
        //$sgst_price                                 = ($sgst / 100) * $course_price;
        //$cgst_price                                 = ($cgst / 100) * $course_price;
        
        //cb_tax_method = 1 is exclusive
        //250/100 + 12*12 inclusive tax amount
        //250/100 + 12*100 inclusive taxable amount 
        
        if($tax_type != '1')
        { //inclusive
            $sgst_price                                 = $item_price / (100 + $sgst) * $sgst;//($sgst / 100) * $bundle_price;
            $cgst_price                                 = $item_price / (100 + $cgst) * $cgst;
            $totalTaxPercentage                         = $cgst + $sgst;
            $total_course_price                         = $item_price;
        }
        else
        {
            $sgst_price                                 = ($sgst / 100) * $item_price;
            $cgst_price                                 = ($cgst / 100) * $item_price;
            $total_course_price                         = $item_price + ($sgst_price + $cgst_price);
        }

        $payment_tax_object                         = array();
        $payment_tax_object['sgst']['percentage']   = $sgst;
        $payment_tax_object['sgst']['amount']       = round($sgst_price, 2); 
        $payment_tax_object['cgst']['percentage']   = $cgst;
        $payment_tax_object['cgst']['amount']       = round($cgst_price, 2); 
        $payment_param['ph_tax_objects']            = json_encode($payment_tax_object);
    
        $payment_param['ph_item_amount_received']   = $payment_details['amount']/100;//round($total_course_price, 2);//
        $payment_param['ph_payment_mode']           = '1';

        $transaction_details                        = array();
        $transaction_details['transaction_id']      = $payment_details['id'];
        $transaction_details['bank']                = $payment_details['bank'];
        $payment_param['ph_transaction_id']         = $payment_details['id'];
        $payment_param['ph_transaction_details']    = json_encode($transaction_details);
        $payment_param['ph_account_id']             = config_item('id');;
        $payment_param['ph_payment_gateway_used']   = 'razorpay';
        $payment_param['ph_status']                 = '0';
        $payment_param['ph_payment_date']           = date('Y-m-d H:i:s');
        $subscription_param['bs_payment_details']   = $payment_param;//2


        $insert_id                                  = $this->session->userdata('ofabee_payment_id');
        if(empty($insert_id))
        {
            $insert_id                              = $this->Payment_model->save_history($payment_param);
        }
        
        if($insert_id)
        {
            $order_id                               = date('Y').date('m').date('d').$insert_id;
            
            $order_param                            = array();
            $payment_param['id']                    = $insert_id;
            $payment_param['ph_order_id']           = $order_id;
            $payment_param['ph_status']             = '1';
            if($this->Payment_model->save_history($payment_param))
            {
                
                $subscription_param['id']   = $item_id;
                $subscription_param['type'] = $item_type;
                if($item_type == 2)
                {
                    $subscription_param['bundle_id'] = $bundle['id'];
                }
                $message                    = "";
                
                
                switch ($item_type) 
                {
                    case "1":
                        $objects                = array();
                        $objects['key']         = 'course_notification_' . $item_id;
                        $callback               = 'course_notification';
                        $params                 = array('course_id' => $item_id);
                        $discussion_forum       = $this->memcache->get($objects, $callback, $params);

                        $preveleged_users       = $discussion_forum['preveleged_users'];

                        foreach($preveleged_users as $preveleged_user)
                        {
                            $notify_to[$preveleged_user] = array($session['id']);
                        }
                        //Push notification
                        $this->load->library('Notifier');
                        $notify_param =  array(
                            'action_code'   => 'purchase_notify',
                            'assets'        => array('item_type'=>'course','item_name' => $item_name,'student_name' => isset($session['us_name'])?$session['us_name']:''),
                            'target'        => $item_id,
                            'push_to'       => $notify_to
                        );
                        // echo "<pre>";print_r($notify_param);exit;
                        $this->course_subscription($subscription_param);
                        $this->notifier->push($notify_param);
                        //End notification 
                        $message               = 'Your course subscription is successful';
                        break;
                    case "2":
                        $objects                = array();
                        $objects['key']         = 'bundle_notification_' . $item_id;
                        $callback               = 'bundle_notification';
                        $params                 = array('bundle_id' => $item_id);
                        $all_users              = $this->memcache->get($objects, $callback, $params);
            
                        $preveleged_users       = $all_users['preveleged_users'];
            
                        foreach($preveleged_users as $preveleged_user)
                        {
                            $notify_to[$preveleged_user] = array($session['id']);
                        }
                        //Push notification
                        $this->load->library('Notifier');
                        $notify_param =array(
                            'action_code'   => 'purchase_notify',
                            'assets'        => array('item_type'=>'bundle','item_name' => $item_name,'student_name' => isset($session['us_name'])?$session['us_name']:''),
                            'target'        => $item_id,
                            'push_to'       => $notify_to
                        );
                        // echo "<pre>";print_r($notify_param);exit;
                        $this->notifier->push($notify_param);
                        //End notification 
                        $this->bundle_subscription($subscription_param);
                        $message               = 'Your bundle subscription is successful';
                        break;
                }
            }
        }
        $this->session->unset_userdata('razorpay_order_id');
        $this->session->unset_userdata('razorpay_item_id');
        $this->session->unset_userdata('razorpay_item_type');
        $this->session->unset_userdata('promocode');
        $this->session->set_flashdata('success', $message);
        //redirect('dashboard');
        //redirect('checkout/payment_success', $insert_id);
        $this->payment_success($insert_id);
        // echo json_encode($response);die();
        //Course Subscription
        //course subscription end
           
    }

    public function payment_success( $order_id = false )
    {
        $data['order_id']  = $order_id;
        $this->load->view($this->config->item('theme').'/payment_success', $data);
    }

    public function download_invoice( $order_id = false )
    {
        // if(!$order_id)
        // {
        //     redirect('dashboard');
        // }
        // $data               = array();
        // $data['order']      = $this->Order_model->order(array('order_id' => $order_id));
        // $this->load->view($this->config->item('theme').'/order_invoice', $data);
        if(!$order_id)
        {
            redirect(admin_url('dashboard'));
        }
        $data            = array();
        $data['order']   = $this->Order_model->order(array('order_id' => $order_id));
        //echo '<pre>';print_r($data['order']);die;

        
        if($data['order']['ph_status']=='1')
        {
            $this->load->view($this->config->item('theme').'/order_invoice', $data);
        }
        else
        {
            redirect(admin_url('dashboard'));
        }
    }

    public function discount_enrollment($param){

        $session             = $this->auth->get_current_user_session('user');
        $item_id             = $param['item_id'];
        $item_type           = $param['item_type'];
        $promocode_details   = array();
        switch ($item_type) 
        {
            case "1":
                $item_type_label    = 'course';
                $objects            = array();
                $objects['key']     = 'course_'.$item_id;
                $callback           = 'course_details';
                $params             = array('id' => $item_id);
                $course             = $this->memcache->get($objects, $callback, $params);
                $item_name          = $course['cb_title'];
                $item_base_price    = $course['cb_price'];
                $item_discount_price= $course['cb_discount'];
                $tax_type           = $course['cb_tax_method'];
                $item_price         = ($course['cb_discount']!=0)?$course['cb_discount']:$course['cb_price'];
                $item_details       = $course;
                break;
            case "2": 
            
                $subscription_param = array();
                $item_type_label    = 'bundle';
                $objects            = array();
                $objects['key']     = 'bundle_'.$item_id;
                $callback           = 'bundle_details';
                $params             = array('id' => $item_id);
                $bundle             = $this->memcache->get($objects, $callback, $params);
                $item_name          = $bundle['c_title'];
                $item_base_price    = $bundle['c_price'];
                $item_discount_price= $bundle['c_discount'];
                $tax_type           = $bundle['c_tax_method'];
                $item_price         = ($bundle['c_discount']!=0)?$bundle['c_discount']:$bundle['c_price'];
                $subscription_param['bs_bundle_details'] = array(
                                                                'id'                => $bundle['id'],
                                                                'c_title'           => $bundle['c_title'],
                                                                'c_code'            => $bundle['c_code'],
                                                                'c_courses'         => json_decode($bundle['c_courses']),
                                                                'c_access_validity' => $bundle['c_access_validity'],
                                                                'c_validity'        => $bundle['c_validity'],
                                                                'c_validity_date'   => $bundle['c_validity_date'],
                                                                'c_price'           => $bundle['c_price'],
                                                                'c_discount'        => $bundle['c_discount'],
                                                                'c_tax_method'      => $bundle['c_tax_method']
                                                            );
                $item_details       = $subscription_param['bs_bundle_details'];
                break;
        }

        $promocode                                 = $this->session->userdata('promocode');
        if($promocode)
        {
            $user_details                          = array();
            $user_details['id']                    = $session['id'];
            $user_details['name']                  = $session['us_name'];
            $user_details['email']                 = $session['us_email'];
            $user_details['phone']                 = $session['us_phone'];
            $user_details['itemType']              = $item_type_label;
            $user_details['itemName']              = $item_name;
            $user_details['applied_on']            = date('d-m-Y H:i:s');
            $user_detail[$session['id']]           = (isset($user_details))?$user_details:array();
            $param                                 = array();
            $param['promocode']                    = $promocode;
            $param['user_details']                 = json_encode($user_detail);
            $promocode_response                    = $this->promocode->check_valid_promocode($param);
            if($promocode_response['header']['success'])
            {
                
                $discout_type                   = (isset($promocode_response['body']['promocode']['pc_discount_type']))?$promocode_response['body']['promocode']['pc_discount_type']:1;
                if($discout_type==1) 
                {
                    $discount_percentage        = 0;
                    $discount_rate              = ($promocode_response['body']['promocode']['pc_discount_rate']!=NULL)?$promocode_response['body']['promocode']['pc_discount_rate']:0;
                } 
                else 
                {
                    $discount_percentage        = ($promocode_response['body']['promocode']['pc_discount_rate']!=NULL)?$promocode_response['body']['promocode']['pc_discount_rate']:0;
                    $discount_rate              = ($discount_percentage!=0)?round((($discount_percentage/100) * $item_price),2):0;
                } 
                $item_price                     = ($discount_rate<$item_price)?($item_price-$discount_rate):0;           
               
                /* save promocode details */
                $promocode_details['promocode']           = $promocode;
                $promocode_details['discout_type']        = $discout_type;
                $promocode_details['discount_percentage'] = $discount_percentage;
                $promocode_details['discount_rate']       = round($discount_rate, 2);
                $promocode_details['item_net_amount']     = $item_price;
                $save_promocode                           = $this->promocode->record_promocode_usage($param);
            }
        }

        $user_details                               = array();
        $user_details['name']                       = $session['us_name'];
        $user_details['email']                      = $session['us_email'];
        $user_details['phone']                      = $session['us_phone'];

        $payment_param                              = array();
        $payment_param['id']                        = false;
        $payment_param['ph_user_id']                = $session['id'];
        $payment_param['ph_user_details']           = json_encode($user_details);//4
        $payment_param['ph_promocode']              = json_encode($promocode_details);
        $payment_param['ph_item_other_details']    = json_encode($item_details);
        $payment_param['ph_item_id']                = $item_id;
        $payment_param['ph_item_type']              = $item_type;
        $payment_param['ph_item_name']              = $item_name;
        $payment_param['ph_item_base_price']        = $item_base_price;
        $payment_param['ph_item_discount_price']    = $item_discount_price;
        $payment_param['ph_tax_type']               = $tax_type;
           
        //$course_price                               = ($course['cb_discount']!=0)?$course['cb_discount']:$course['cb_price'];
        $gst_setting                                = $this->settings->setting('has_tax');
        $cgst                                       = ($gst_setting['as_setting_value']['setting_value']->cgst != '')?$gst_setting['as_setting_value']['setting_value']->cgst:'0';
        $sgst                                       = ($gst_setting['as_setting_value']['setting_value']->sgst != '')?$gst_setting['as_setting_value']['setting_value']->sgst:'0';
        
        $sgst_price                                 = '0';
        $cgst_price                                 = '0';
        $total_course_price                         = '0';
        $payment_tax_object                         = array();
        $payment_tax_object['sgst']['percentage']   = $sgst;
        $payment_tax_object['sgst']['amount']       = round($sgst_price, 2); 
        $payment_tax_object['cgst']['percentage']   = $cgst;
        $payment_tax_object['cgst']['amount']       = round($cgst_price, 2); 
        $payment_param['ph_tax_objects']            = json_encode($payment_tax_object);
        
        $payment_param['ph_item_amount_received']   = '0';
        $payment_param['ph_payment_mode']           = '1';

        $transaction_details                        = array();
        $transaction_details['transaction_id']      = '-';
        $transaction_details['bank']                = '-';
        $payment_param['ph_transaction_id']         = '-';
        $payment_param['ph_transaction_details']    = json_encode($transaction_details);
        $payment_param['ph_account_id']             = config_item('id');;
        $payment_param['ph_payment_gateway_used']   = '-';
        $payment_param['ph_status']                 = '0';
        $payment_param['ph_payment_date']           = date('Y-m-d H:i:s');
        $subscription_param['bs_payment_details']   = $payment_param;//1  

        $insert_id                                  = $this->session->userdata('ofabee_payment_id');
        if(empty($insert_id))
        {
            $insert_id                              = $this->Payment_model->save_history($payment_param);
        }
        
        if($insert_id)
        {
            $order_id                               = date('Y').date('m').date('d').$insert_id;
            $order_param                            = array();
            $payment_param['id']                    = $insert_id;
            $payment_param['ph_order_id']           = $order_id;
            $payment_param['ph_status']             = '1';
            if($this->Payment_model->save_history($payment_param))
            {
                
                $subscription_param['id']   = $item_id;
                $subscription_param['type'] = $item_type;
                if($item_type == 2)
                {
                    $subscription_param['bundle_id'] = $bundle['id'];
                }
                $message                    = "";
                
                
                switch ($item_type) 
                {
                    
                    case "1":

                        $objects                = array();
                        $objects['key']         = 'course_notification_' . $item_id;
                        $callback               = 'course_notification';
                        $params                 = array('course_id' => $item_id);
                        $discussion_forum       = $this->memcache->get($objects, $callback, $params);

                        $preveleged_users       = $discussion_forum['preveleged_users'];

                        foreach($preveleged_users as $preveleged_user)
                        {
                            $notify_to[$preveleged_user] = array($session['id']);
                        }

                        $message               = 'Your course subscription is successful';
                        //Push notification
                        $this->load->library('Notifier');
                        $notify_param =  array(
                            'action_code'   => 'purchase_notify',
                            'assets'        => array('item_type'=>'course','item_name' => $item_name,'student_name' => isset($session['us_name'])?$session['us_name']:''),
                            'target'        => $item_id,
                            'push_to'       => $notify_to
                        );
                        // echo "<pre>";print_r($notify_param);exit;
                        $this->course_subscription($subscription_param);
                        $this->notifier->push($notify_param);
                        //End notification 
                        
                        break;
                    case "2":

                        $objects                = array();
                        $objects['key']         = 'bundle_notification_' . $item_id;
                        $callback               = 'bundle_notification';
                        $params                 = array('bundle_id' => $item_id);
                        $all_users              = $this->memcache->get($objects, $callback, $params);
            
                        $preveleged_users       = $all_users['preveleged_users'];
            
                        foreach($preveleged_users as $preveleged_user)
                        {
                            $notify_to[$preveleged_user] = array($session['id']);
                        }

                        //Push notification
                        $this->load->library('Notifier');
                        $notify_param =array(
                            'action_code'   => 'purchase_notify',
                            'assets'        => array('item_type'=>'bundle','item_name' => $item_name,'student_name' => isset($session['us_name'])?$session['us_name']:''),
                            'target'        => $item_id,
                            'push_to'       => $notify_to
                        );
                        // echo "<pre>";print_r($notify_param);exit;
                        $this->notifier->push($notify_param);
                        //End notification //
                        $this->bundle_subscription($subscription_param);
                        $message               = 'Your bundle subscription is successful';
                        break;
                }
            }
        }
        $this->session->unset_userdata('razorpay_order_id');
        $this->session->unset_userdata('razorpay_item_id');
        $this->session->unset_userdata('razorpay_item_type');
        $this->session->unset_userdata('promocode');
        $this->session->set_flashdata('success', $message);
        redirect('dashboard');
    }
    public function free_enrollment_bundle($item_id = false,$item_type = false){
        $subscription_param         = array();
        $subscription_param['id']   = $item_id;
        $subscription_param['type'] = $item_type;
        $status_count               = 0;

        $bundle_param               = array();
        $bundle_param['select']     = 'id,c_title,c_code,c_courses,c_access_validity,c_validity,c_validity_date,c_price,c_discount,c_tax_method,c_is_free';  
        $bundle_param['bundle_id']  = $item_id;
        $bundle                     = $this->Bundle_model->bundle($bundle_param);
        
        if(!$bundle['c_is_free'] || $bundle['c_is_free'] !='1')
        {
            $this->session->set_flashdata('error', 'Sorry! The course is not available for free right now!');
            redirect('dashboard');
            return false;
        }
        //die('free enrollment');
        $subscription_param['bs_bundle_details'] = array(
                                    'id'                => $bundle['id'],
                                    'c_title'           => $bundle['c_title'],
                                    'c_code'            => $bundle['c_code'],
                                    'c_courses'         => json_decode($bundle['c_courses']),
                                    'c_access_validity' => $bundle['c_access_validity'],
                                    'c_validity'        => $bundle['c_validity'],
                                    'c_validity_date'   => $bundle['c_validity_date'],
                                    'c_price'           => $bundle['c_price'],
                                    'c_discount'        => $bundle['c_discount'],
                                    'c_tax_method'      => $bundle['c_tax_method'],
                                    'bundle_id'         => $bundle['id']
                                );
        $subscription_param['bundle_id'] = $bundle['id'];
        //echo "<pre>";print_r($subscription_param);die;
        if($this->bundle_subscription($subscription_param)){

            $user                   = $this->auth->get_current_user_session('user');
            $user_id                = $user['id'];
            $objects                = array();
            $objects['key']         = 'bundle_notification_' . $item_id;
            $callback               = 'bundle_notification';
            $params                 = array('bundle_id' => $item_id);
            $all_users              = $this->memcache->get($objects, $callback, $params);
            
            $gst_setting                                = $this->settings->setting('has_tax');
            $cgst                                       = ($gst_setting['as_setting_value']['setting_value']->cgst != '')?$gst_setting['as_setting_value']['setting_value']->cgst:'0';
            $sgst                                       = ($gst_setting['as_setting_value']['setting_value']->sgst != '')?$gst_setting['as_setting_value']['setting_value']->sgst:'0';
            $payment_tax_object                         = array();
            $payment_tax_object['sgst']['percentage']   = $sgst;
            $payment_tax_object['sgst']['amount']       = 0; 
            $payment_tax_object['cgst']['percentage']   = $cgst;
            $payment_tax_object['cgst']['amount']       = 0;  


            $preveleged_users       = $all_users['preveleged_users'];

            foreach($preveleged_users as $preveleged_user)
            {
                $notify_to[$preveleged_user] = array($user_id);
            }
            
            //Push notification
            $this->load->library('Notifier');
            $this->notifier->push(
                array(
                    'action_code'   => 'bundle_subscribed',
                    'assets'        => array('bundle_name' => $bundle['c_title'],'student_name' => isset($user['us_name'])?$user['us_name']:'','bundle_id' => $item_id),
                    'target'        => $item_id,
                    'individual'    => false,
                    'push_to'       => $notify_to
                )
            );
            //End notification
            
            $status_count++;
        }

        if($this->course_subscription($subscription_param)){
            $status_count++;
        }

        if($status_count == 2){

            $user_details                               = array();
            $user_details['name']                       = $user['us_name'];
            $user_details['email']                      = $user['us_email'];
            $user_details['phone']                      = $user['us_phone'];

            $payment_param                              = array();
            $payment_param['id']                        = false;
            $payment_param['ph_user_id']                = $user['id'];
            $payment_param['ph_user_details']           = json_encode($user_details);//2 
            $payment_param['ph_item_other_details']     = json_encode( $subscription_param['bs_bundle_details']);
            $payment_param['ph_promocode']              = '';
            $payment_param['ph_item_id']                = $item_id;
            $payment_param['ph_item_type']              = $item_type;
            $payment_param['ph_item_name']              = $bundle['c_title'];
            $payment_param['ph_item_base_price']        = $bundle['c_price'];
            $payment_param['ph_item_discount_price']    = $bundle['c_discount'];
            $payment_param['ph_tax_type']               = $bundle['c_tax_method'];
            $payment_param['ph_item_code']              = $bundle['c_code'];
            $payment_param['ph_tax_objects']            = json_encode($payment_tax_object);
            $payment_param['ph_item_amount_received']   = '0';
            $payment_param['ph_payment_mode']           = '2';
            $payment_param['ph_transaction_id']         = '';
            $payment_param['ph_transaction_details']    = '';
            $payment_param['ph_account_id']             = config_item('id');;
            $payment_param['ph_payment_gateway_used']   = '';
            $payment_param['ph_status']                 = '1';
           
            $payment_param['ph_payment_date']           = date('Y-m-d H:i:s');
            
            //inserting pending payment hostory
            $this->load->model('order_model');
            $pending_order = $this->order_model->get_pending_payment(array('id' => $user['id'], 'item_id' => $item_id));
            if(isset($pending_order['id']))
            {
                $ph_insert_id                           = $pending_order['id'];
                $order_id                               = date('Y').date('m').date('d').$ph_insert_id;
                $this->session->set_userdata(array('ofabee_payment_id'   => $insert_id, 'ofabee_order_id' => $order_id));
            }
            else
            {
                $ph_insert_id                           = $this->Payment_model->save_history($payment_param);
                $order_id                               = date('Y').date('m').date('d').$ph_insert_id;
            }

            $payment_param_of_order_id['id']            = $ph_insert_id;
            $payment_param_of_order_id['ph_order_id']   = $order_id;
            $this->Payment_model->save_history($payment_param_of_order_id);      

            redirect('dashboard');
        }
    }
    
    private function bundle_subscription($param = array()){
        
        $bundle_id                  = isset($param['id'])?$param['id']:false;
        if(!$bundle_id) {
            redirect('dashboard');
        }
        $user                       = $this->auth->get_current_user_session('user');
        $user_id                    = $user['id'];
        $enrolled_courses           = array();
        $notification_ids           = array();
        $payment_data               = array();
        $bundle_param               = array();

        $bundle_param['select']     = 'id,c_title,c_code,c_courses,c_access_validity,c_validity,c_validity_date,c_price,c_discount,c_tax_method';  
        $bundle_param['bundle_id']  = $bundle_id;
        $bundle                     = $this->Bundle_model->bundle($bundle_param);
        $user_bundle_subscription   = $this->Bundle_model->bundle_subscription_details(array('bundle_id' => $bundle_id, 'user_id' => $user_id));
        $bundle_subscription_id     = !empty($user_bundle_subscription) && $user_bundle_subscription['id'] ? $user_bundle_subscription['id'] : false;
        $update                     = !empty($user_bundle_subscription) && $user_bundle_subscription['id'] ? true : false;

        if(!empty($user_id))
        {
            $email_content                      = array();
            $save_userdata                      = array();
            $save_subscribe                     = array();
            $bundle_name                        = $bundle['c_title'];

            $save                               = array();
            $save['bs_bundle_id']               = $bundle_id;
            $save['bs_user_id']                 = $user_id;
            $save['bs_subscription_date']       = date('Y-m-d H:i:s');
            $save['bs_start_date']              = date('Y-m-d');
            $save['bs_course_validity_status']  = $bundle['c_access_validity'];
            $save['bs_user_groups']             = '';
            $save['bs_payment_details']         = isset($param['bs_payment_details']) ? json_encode($param['bs_payment_details']) : '';//4
            $save['bs_bundle_details']          = isset($param['bs_bundle_details']) ? json_encode($param['bs_bundle_details']) : '';
            
            $bundle_id                          = isset($param['id']) ? $param['id'] : false;
            $notification_ids[]                 = $user_id;
            if ($bundle['c_access_validity'] == 2) 
            {
                $bundle_enddate                 = $bundle['c_validity_date'];
            } 
            else if ($bundle['c_access_validity'] == 0) 
            {
                $bundle_enddate                 = date('Y-m-d', strtotime('+3000 days'));
            } 
            else 
            {
                $duration                       = ($bundle['c_validity']) ? $bundle['c_validity']-1 : 0;
                $bundle_enddate                 = date('Y-m-d', strtotime('+' . $duration . ' days'));
            }
                            
            $save['bs_end_date']                = $bundle_enddate;
            $save['bs_approved']                = '1';
            $save['action_by']                  = $user_id;         
            if($save)
            {
                $this->Bundle_model->save_subscription($save, array('update' => $update, 'id' => $bundle_subscription_id));

                $bundle_courses                 = json_decode($bundle['c_courses'],true);
                $course_ids                     = !empty($bundle_courses) ? array_column($bundle_courses, 'id') : array();
                if(!empty($course_ids))
                {
                    $users_to_subscribe         = $this->Bundle_model->migrateCourseSubscription(array('bundle_id' => $bundle_id, 'course_ids' => $course_ids, 'user_ids' => array($user_id)));
                    if(!empty($users_to_subscribe))
                    {
                        $this->course_subscription(array('id' => $bundle_id, 'bundle_id' => $bundle_id, 'type' => '2'));    
                    }
                }

                if ($bundle_id) {
                    $this->memcache->delete('bundle_' . $bundle_id);
                    $this->memcache->delete('bundle_enrolled_' . $user_id);
                    $this->memcache->delete('enrolled_item_ids_' . $user_id);
                    $this->memcache->delete('mobile_enrolled_'.$user_id);
                    // $this->memcache->delete('my_bundle_subscriptions');
                } else {
                    $this->memcache->delete('all_courses');
                    $this->memcache->delete('sales_manager_all_sorted_courses');
                    $this->memcache->delete('top_courses');
                }
            }              
            
            return true;
        } else {
            return false;
        }
    }

    private function course_subscription($param = array()) 
    {
        $session             = $this->auth->get_current_user_session('user');
        $user_id             = $session['id'];
        $id                  = $param['id'];
        $type                = $param['type'];
        $courses             = array();
        $message             = '';
        $bundle_id           = isset($param['bundle_id']) ? $param['bundle_id'] : 0;
        $return              = false;
        switch ($type) 
        {
            case "1":
                $courses[]             = $id;
                $course_objects        = array();
                $course_objects['key'] = 'course_'.$id;
                $course_callback       = 'course_details';
                $course_params         = array('id' => $id);
                $course_details        = $this->memcache->get($course_objects, $course_callback, $course_params);
                $message               = 'Your course subscription is successful';
                $item_name              = $course_details['cb_title']; 
                $course_validity_status = $course_details['cb_access_validity'];
                $course_validity            = (($course_details['cb_validity']) && $course_details['cb_validity'] > 0)?$course_details['cb_validity']:1;
                if($course_details['cb_access_validity'] == 2)
                {
                    $course_enddate   = $course_details['cb_validity_date'];
                }
                else
                {
                    $course_enddate   = ($course_details['cb_access_validity'] == 0) ? date('Y-m-d', strtotime('+3000 days')) : date('Y-m-d', time() + ($course_validity - 1) * 60 * 24 * 60);
                }
                break;
            case "2":
                $objects        = array();
                $objects['key'] = 'bundle_'.$id;
                $callback       = 'bundle_details';
                $params         = array('id' => $id);
                $bundles        = $this->memcache->get($objects, $callback, $params);
                $course_details = json_decode($bundles['c_courses'],TRUE);
                
                foreach($course_details as $course_detail)
                {
                    $courses[]          = $course_detail['id'];
                }
                $item_name              = $bundles['c_title'];
                $message                = 'Your bundle subscription is successful';
                $course_validity_status = $bundles['c_access_validity'];
                if ($bundles['c_access_validity'] == 2) 
                {
                    $course_enddate     = $bundles['c_validity_date'];
                } 
                else if ($bundles['c_access_validity'] == 0) 
                {
                    $course_enddate     = date('Y-m-d', strtotime('+3000 days'));
                } 
                else 
                {
                    $duration           = ($bundles['c_validity']) ? $bundles['c_validity']-1 : 0;
                    $course_enddate     = date('Y-m-d', strtotime('+' . $duration . ' days'));
                }                    
                break;
        }
        if(!empty($courses))
        {
            foreach($courses as $course_id)
            {
                $course_objects        = array();
                $course_objects['key'] = 'course_'.$course_id;
                $course_callback       = 'course_details';
                $course_params         = array('id' => $course_id);
                $course                = $this->memcache->get($course_objects, $course_callback, $course_params);

                $course_subscription_date   = date("Y-m-d H:i:s");
                $course_startdate           = date("Y-m-d",time());  
                $cs_save                                = array();
                if($subscription = $this->Payment_model->check_subscription(array('course_id'=>$course_id, 'user_id'=>$session['id'])))
                {
                    $cs_save['id']                      = $subscription['id'];
                    $cs_save['action_id']               = $this->actions['update'];
                    $cs_save['updated_date']            = $course_subscription_date;
                    if($type == 2)
                    {
                        $bs_bundle_ids                  = explode(',', $subscription['cs_bundle_id']);
                    
                        if(!in_array($bundle_id, $bs_bundle_ids))
                        {
                            $cs_save['cs_bundle_id']        = $subscription['cs_bundle_id'].','.$bundle_id;
                        }
                    }
                }
                else
                {
                    $cs_save['id']                      = false;
                    $cs_save['action_id']               = $this->actions['create'];
                    if($type == 2)
                    {
                        $cs_save['cs_bundle_id']        = $bundle_id;
                    }
                }
                $cs_save['cs_user_groups']          = $session['us_groups'];
                $cs_save['cs_course_id']            = $course_id;
                $cs_save['cs_user_id']              = $session['id'];
                $cs_save['cs_approved']             = '1';
                

                $cs_save['cs_subscription_date']    = $course_subscription_date;
                $cs_save['cs_start_date']           = $course_startdate;
                $cs_save['cs_end_date']             = $course_enddate;
                $cs_save['action_by']               = $session['id'];
                $cs_save['cs_course_validity_status']= $course_validity_status;
                if($this->Payment_model->save($cs_save))
                {
                    $return = true;
                    //Invalidate cahe
                    $this->memcache->delete('enrolled_'.$session['id']);  
                    $this->memcache->delete('mobile_enrolled_'.$session['id']);                  
                    if ($course_id) 
                    {
                        $this->memcache->delete('course_' . $course_id);
                        $this->memcache->delete('my_subscriptions');
                        $this->memcache->delete('enrolled_item_ids_'.$session['id']);
                    } 
                    else
                    {
                        $this->memcache->delete('all_courses');
                        $this->memcache->delete('sales_manager_all_sorted_courses');
                        $this->memcache->delete('top_courses');
                    }
                    $this->Payment_model->remove_wishlist($cs_save);
                }
        
                //send notification to admin
                $this->load->model('Tutor_model');
                $tutors             = $this->Tutor_model->get_tutor_name_by_course($course_id);
                //echo '<pre>';print_r($tutors);die;            
            
            
                $param              = array();
                $param['ids']       = array();
                $param['ids'][]     = $this->config->item('us_id');
                $mail_ids           = array($this->config->item('site_email'));
                if(!empty($tutors))
                {
                    foreach($tutors as $tutor)
                    {
                        $param['ids'][] = $tutor['id'];
                        $mail_ids[]     = $tutor['us_email'];
                    }
                }
                //End
                $institute_admins = array();

                //Institute admin
                $institute              = $this->User_model->users(array( 'institute_id'=>$session['us_institute_id'],'role_id'=>'8','status'=>'1','not_deleted'=>true, 'select' => 'users.us_email,users.id'));
                if(!empty($institute)) 
                {
                    // echo "<pre>";print_r($institute);exit;
                    foreach($institute as $i_admin)
                    {
                        //$institute_admins[]  = $i_admin['id'];
                        if($i_admin['id'])
                        {
                            $notify_to[$i_admin['id']] = array($session['id']);
                        }
                        $mail_ids[]          = $i_admin['us_email'];
                    }
                }
                //End ins admin
                
               
                // //Notify to Admin,I admin and priveleged users.
                // $preveleged_users = $this->accesspermission->previleged_users(array('module' => 'course'));
                // foreach($preveleged_users as $preveleged_user)
                // {
                //     $notify_to[$preveleged_user['id']] = array($session['id']);
                // }
        
                // $notify_to[$session['us_institute_id']] = array($session['id']);
                
                // $this->load->library('Notifier');
                // $this->notifier->push(
                //     array(
                //         'action_code' => 'course_subscribed',
                //         'assets' => array('course_name' => $course['cb_title'],'student_name'=>$session['us_name'],'course_id' =>$course_id),
                //         'target' => $course_id,
                //         'individual' => false,
                //         'push_to' => $notify_to
                //     )
                // );
                //End notifying.
            }

            
            switch ($type) 
            {
                case "1":
                    //Send email using template.
                    $template               = $this->ofabeemailer->template(array('email_code' => 'approve_enrollment'));
                    $param_admin            = array();
                    $param_admin['to']      = $mail_ids;
                    $param_admin['subject'] = $template['em_subject'];
                    $contents               = array(
                                                'student_name' => $session['us_name'],
                                                'course_name'=> $item_name
                                                ,'site_name' => config_item('site_name')
                                            );
                    $param_admin['body']      = $this->ofabeemailer->process_mail_content($contents, $template['em_message']);
                    $send = $this->ofabeemailer->send_mail($param_admin);

                    /*Log creation*/
                    $user                               = $session;
                    $user_data                          = array();
                    $user_data['user_id']               = $user['id'];
                    $user_data['username']              = $user['us_name'];
                    $user_data['useremail']              = $user['us_email'];
                    $user_data['user_type']             = $user['us_role_id'];
                    $user_data['phone_number']          = $user['us_phone'];
                    $message_template                   = array();
                    $message_template['username']       = $user['us_name'];
                    $message_template['course_name']    = ' '.$item_name;
                    $triggered_activity                 = 'course_subscribed';
                    log_activity($triggered_activity, $user_data, $message_template);
                    break;
                case "2":
                // echo "<pre>";print_r();
                
                    //Send email using template.
                    $template               = $this->ofabeemailer->template(array('email_code' => 'bundle_enrollment'));
                    $param_admin            = array();
                    $param_admin['to']      = $mail_ids;
                    $param_admin['subject'] = $template['em_subject'];
                    $contents               = array(
                                                'student_name' => $session['us_name'],
                                                'bundle_name'=> $item_name
                                                ,'site_name' => config_item('site_name'));
                    $param_admin['body']      = $this->ofabeemailer->process_mail_content($contents, $template['em_message']);
                    $send = $this->ofabeemailer->send_mail($param_admin);
                    //End send emailcourse
                    $this->session->set_flashdata('message', $message);
                    

                    /*Log creation*/
                    $user                               = $session;
                    $user_data                          = array();
                    $user_data['user_id']               = $user['id'];
                    $user_data['username']              = $user['us_name'];
                    $user_data['useremail']              = $user['us_email'];
                    $user_data['user_type']             = $user['us_role_id'];
                    $user_data['phone_number']          = $user['us_phone'];
                    $message_template                   = array();
                    $message_template['username']       = $user['us_name'];
                    $message_template['bundle_name']    = ' '.$item_name;
                    $triggered_activity                 = 'bundle_subscribed';
                    log_activity($triggered_activity, $user_data, $message_template);
                    break;
            }

          
            
             
        }
        $this->memcache->delete('all_courses');    
        $this->memcache->delete('sales_manager_all_sorted_courses');    
        return $return;
        
    }

    function send_notification($param = array()){

        $course_name    = isset($param['course_name'])?$param['course_name']:'';
        $student_name   = isset($param['student_name'])?$param['student_name']:'';
        $users          = isset($param['users'])?$param['users']:'';
        $course_id      = isset($param['course_id'])?$param['course_id']:'';
        $this->load->library('Notifier');
        $this->notifier->push(
            array(
                'action_code' => 'student_paid_to_course',
                'assets' => array('student_name' => $student_name,'course_name'=>$course_name),
                'target' => $course_id,
                'individual' => false,
                'push_to' => $users
            )
        );
    }

    /**
     * Promo code checking 
     */
    public function promocode_usage()
    {
        $session                        = $this->auth->get_current_user_session('user');
        $user                           = array();
        $user['id']                     = $session['id'];
        $user['name']                   = $session['us_name'];
        $user['email']                  = $session['us_email'];
        $user_details[$session['id']]   = (isset($user))?$user:array();
        $promo_code                     = trim($this->input->post('promo_code'));
        $param                          = array();
        $param['promocode']             = $promo_code;
        $param['user_details']          = json_encode($user_details);
        $response                       = $this->promocode->check_valid_promocode($param);
        if($response['header']['success'])
        {
            $this->session->set_userdata(array('promocode' => $promo_code));
        }
        echo json_encode($response);die();
    }

    public function reset_coupon()
    {
        $response           = array();
        $response['error']  = false;
        $promocode          = $this->session->userdata('promocode');
        if($promocode)
        {
            $this->session->unset_userdata('promocode');
            $response['message']  = 'Promocode resetted successfully';
        }
        echo json_encode($response);die();
    }


    //optimizing the payment operation for reusability- kiran
    // public function save_payment_history($param = array())
    // {
    //     $item_id                 = isset($param['item_id'])?$param['item_id']:'';
    //     $item_type               = isset($param['item_type'])?$param['item_type']:'1';
    //     $item_name               = isset($param['item_name'])?$param['item_name']:'';
    //     $item_base_price         = isset($param['item_base_price'])?$param['item_base_price']:'0';
    //     $item_discount_price     = isset($param['item_discount_price'])?$param['item_discount_price']:'0';
    //     $tax_type                = isset($param['tax_type'])?$param['tax_type']:'';
    //     $sgst_price              = isset($param['sgst_price'])?$param['sgst_price']:'0';
    //     $cgst_price              = isset($param['cgst_price'])?$param['cgst_price']:'0';
    //     $payment_gate            = isset($param['payment_gate'])?$param['payment_gate']:'-';
    //     $payment_bank            = isset($param['payment_bank'])?$param['payment_bank']:'-';
    //     $payment_id              = isset($param['payment_id'])?$param['payment_id']:'-';
    //     $payment_amount          = isset($param['payment_amount'])?($param['payment_amount']/100):'0';

    //     $logged_user                                = $this->auth->get_current_user_session('user');
    //     $user_details                               = array();
    //     $user_details['name']                       = $logged_user['us_name'];
    //     $user_details['email']                      = $logged_user['us_email'];
    //     $user_details['phone']                      = $logged_user['us_phone'];

    //     $payment_param                              = array();
    //     $payment_param['id']                        = false;
    //     $payment_param['ph_user_id']                = $logged_user['id'];
    //     $payment_param['ph_user_details']           = json_encode($user_details);
    //     $payment_param['ph_promocode']              = json_encode($promocode_details);
    //     $payment_param['ph_item_id']                = $item_id;
    //     $payment_param['ph_item_type']              = $item_type;
    //     $payment_param['ph_item_name']              = $item_name;
    //     $payment_param['ph_item_base_price']        = $item_base_price;
    //     $payment_param['ph_item_discount_price']    = $item_discount_price;
    //     $payment_param['ph_tax_type']               = $tax_type;
    //     $gst_setting                                = $this->settings->setting('has_tax');
    //     $cgst                                       = ($gst_setting['as_setting_value']['setting_value']->cgst != '')?$gst_setting['as_setting_value']['setting_value']->cgst:'0';
    //     $sgst                                       = ($gst_setting['as_setting_value']['setting_value']->sgst != '')?$gst_setting['as_setting_value']['setting_value']->sgst:'0';
        
    //     $payment_tax_object                         = array();
    //     $payment_tax_object['sgst']['percentage']   = $sgst;
    //     $payment_tax_object['sgst']['amount']       = $sgst_price; 
    //     $payment_tax_object['cgst']['percentage']   = $cgst;
    //     $payment_tax_object['cgst']['amount']       = $cgst_price; 
    //     if($item_price > 0)
    //     {
    //         $sgst_price                             = ($sgst / 100) * $item_price;
    //         $cgst_price                             = ($cgst / 100) * $item_price;
    //         $total_course_price                     = $item_price + $sgst_price + $cgst_price;
    //     }
        
    //     $transaction_details                        = array();
    //     $transaction_details['transaction_id']      = $payment_id;
    //     $transaction_details['bank']                = $payment_bank;
    //     $payment_param['ph_tax_objects']            = json_encode($payment_tax_object);
    //     $payment_param['ph_item_amount_received']   = $payment_amount;
    //     $payment_param['ph_payment_mode']           = '1';
    //     $payment_param['ph_transaction_id']         = $payment_id;
    //     $payment_param['ph_transaction_details']    = json_encode($transaction_details);
    //     $payment_param['ph_account_id']             = config_item('id');;
    //     $payment_param['ph_payment_gateway_used']   = $payment_gate;
    //     $payment_param['ph_status']                 = '0';
    //     $payment_param['ph_payment_date']           = date('Y-m-d H:i:s');

    //     $insert_id = $this->Payment_model->save_history($payment_param);
    //     if($insert_id)
    //     {
    //         $order_id                     = date('Y').date('m').date('d').$insert_id;
    //         $order_param                  = array();
    //         $order_param['id']            = $insert_id;
    //         $order_param['ph_order_id']   = $order_id;
    //         $order_param['ph_status']     = '1';
    //         if($this->Payment_model->save_history($order_param))
    //         {
    //             return true;
    //         }
    //         else
    //         {
    //             return false;
    //         }
    //     }
       
    // }
}


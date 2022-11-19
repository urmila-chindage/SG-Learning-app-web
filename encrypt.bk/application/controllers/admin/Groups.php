<?php
class Groups extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Kolkata');
        $this->__role_query_filter  = array();
        $this->__admin_index        = 'admin';
        $this->__loggedInUser       = $this->auth->get_current_user_session('admin');
        if(empty($this->__loggedInUser))
        {
            redirect('login');
        }
        
        if ($this->is_institution_manager())
        {
            $this->__role_query_filter['institute_id'] = $this->__loggedInUser['us_institute_id']; 
        }            

        $this->actions = $this->config->item('actions');
        $this->load->model(array('Group_model', 'User_model', 'Course_model'));
        $this->lang->load('groups');
        $this->__limit = 100;
        $this->__limit_user = 10;
        $this->__permission     = $this->accesspermission->get_permission(
                                                        array(
                                                            'role_id' => $this->__loggedInUser['role_id'],
                                                            'module' => 'batch'
                                                            ));  
        $this->event_privilege            = $this->accesspermission->get_permission(array('role_id' => $this->__loggedInUser['role_id'],'module' => 'event')); 
    }
    
    private function is_institution_manager()
    {
        return $this->__loggedInUser['role_id'] == 8;
    }

    function index()
    {
        if(!in_array('1', $this->__permission))
        {
            redirect(admin_url());
        }
        $data                       = array();
        $breadcrumb                 = array();
        $breadcrumb[]               = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]               = array( 'label' => lang('manage_groups'), 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']         = $breadcrumb;
        $data['title']              = lang('groups');
        $data['limit']              = $this->__limit;
        $data['limit_user']         = $this->__limit_user;
        $offset                     = 0;
        $data['show_load_button']   = false;
        
        $group_param                = $this->__role_query_filter;
        $group_param['direction']   = 'DESC';
        $group_param['not_deleted'] = true;
        $group_param['user_id']     = $this->__loggedInUser['id'];
        $group_param['role_id']     = $this->__loggedInUser['role_id'];
        $group_param['count']       = true;
        $data['total_groups']       = $this->Group_model->groups($group_param);
        unset($group_param['count']);
        $group_param['select']      = 'id, CONCAT(gp_institute_code," - ",gp_year," - ",gp_name) as batch_name , gp_name, gp_institute_code, gp_year, gp_status, gp_institute_id';
        $group_param['limit']       = $this->__limit;
        $group_param['offset']      = $offset;
        $groups                     = $this->Group_model->groups($group_param);
        if($data['total_groups'] > $this->__limit)
        {
            $data['show_load_button']   = true;            
        }
        $data['groups']             = array();
        //echo '<pre>'; print_r($groups);die;
        if(!empty($groups))
        {
            $total_groups  = count($groups);
            $group_row = 1;
            foreach ($groups as $group)
            {
                $group['users_count']          = $this->Group_model->group_users(
                                                                    array(
                                                                        'group_id' => $group['id'],
                                                                        'count'    => true
                                                                    ));
                if($group_row == 1)
                {
                    $group['users_offset']         = 1;
                    $group['users']                = $this->Group_model->group_users(
                                                    array(
                                                        'group_id' => $group['id'],
                                                        'limit'    => $this->__limit_user,
                                                        'select'   => 'users.id, users.us_name, users.us_image, users.us_email'
                                                    ));
                }
                else
                {
                    $group['users_offset']  = 0;
                    $group['users']         = array();
                }
                $group_row++;
                $data['groups'][]  = $group;
            }
        }
        
        $data['permissions']    = $this->__permission;
        
        if($this->is_institution_manager())
        {
            $data['institute_admin']        = 'true';
            //Read institutes form memcached
            $objects                        = array();
            $objects['key']                 = 'institute_'.$this->__loggedInUser['us_institute_id'];
            $callback                       = 'institute';
            $institutes                     = $this->memcache->get($objects, $callback, array('id' => $this->__loggedInUser['us_institute_id'])); 
            $data['institutes']             = $institutes;
            //End
        }
        else
        {
            $data['institute_admin']        = 'false';
            //Read institutes form memcached
            $objects                        = array();
            $objects['key']                 = 'institutes';
            $callback                       = 'institutes';
            $institutes                     = $this->memcache->get($objects, $callback, array()); 
            $data['institutes']             = $institutes;
            //End
        }
        
        $this->load->view($this->config->item('admin_folder').'/groups', $data);
    }
    
    function groups_json()
    {
        $data               = array();
        $data['show_load_button']       = false;            
        $group_param        = $this->__role_query_filter;
        
        $limit            = $this->input->post('limit');
        $offset           = $this->input->post('offset');
        $page             = $offset;
        if($page===NULL||$page<=0)
        {
            $page         = 1;
        }
        $page             = ($page - 1)* $limit;

        $group_param['keyword']        = $this->input->post('keyword');
        $group_param['direction']      = 'DESC';
        $group_param['not_deleted']     = true;
        $group_param['count']          = true;
        $group_param['user_id']        = $this->__loggedInUser['id'];
        $group_param['role_id']         = $this->__loggedInUser['role_id'];
        if ($this->is_institution_manager())
        {
            $group_param['institute_id'] = $this->__loggedInUser['us_institute_id']; 
        }  
        $total_groups                  = $this->Group_model->groups($group_param);
        $data['total_groups']          = $total_groups;       
        unset($group_param['count']);
        $group_param['limit']          = $this->input->post('limit');
        $group_param['offset']         = $page;
        if($total_groups > ($this->__limit*$offset))
        {
            $data['show_load_button']  = true;
        }
        $group_param['select']      = 'id, CONCAT(gp_institute_code," - ",gp_year," - ",gp_name) as batch_name , gp_name, gp_institute_code, gp_year, gp_status, gp_institute_id';
        $group_param['order_by']    = 'id';
        $group_param['direction']   = 'DESC';
        $groups                     = $this->Group_model->groups($group_param);
        $data['groups']             = array();
        if(!empty($groups))
        {
            foreach ($groups as $group)
            {
                $group['users_count']          = $this->Group_model->group_users(
                                                                        array(
                                                                            'group_id' => $group['id'],
                                                                            'count'    => true
                                                                        ));
               
                $group['users_offset']  = 0;
                $group['users']         = array();
                // $group['users']                = $this->Group_model->group_users(
                //                                                         array(
                //                                                             'group_id' => $group['id'],
                //                                                             'limit'    => 5,
                //                                                             'select'   => 'users.id, users.us_name, users.us_image, users.us_email'
                //                                                         ));
                $data['groups'][]  = $group;
            }
        }
        $data['limit'] = $limit;
        echo json_encode($data);
    }

    function  group_users_json()
    {
        $limit            = $this->input->post('limit');
        $offset           = $this->input->post('offset');
        $page             = $offset;
        if($page===NULL||$page<=0)
        {
            $page         = 1;
        }
        $page             = ($page - 1)* $limit;

        $param              = array();
        $param['group_id']  = $this->input->post('group_id');
        $param['limit']     = $this->input->post('limit');
        $param['offset']    = $page;
        $param['select']    = 'users.id, users.us_name, users.us_image, users.us_email';
        $group_users        = $this->Group_model->group_users($param);

        // echo "<pre>";
        // print_r($group_users);
        // die;
        echo json_encode($group_users);
    }
    
    /*
    purpose     : create new batch
    params      : none
    usage-in    : Batches(Admin)
    edited      : kiran(12/08)
    */
    function save()
    {
        $response                       = array();
        if(in_array('2', $this->__permission))
        {
            $group_name                 = strip_tags(trim($this->input->post('group_name')));
            $institute_id               = $this->input->post('institute_id');
            $id                         = ($this->input->post('id') != null)? $this->input->post('id') : false;
            if($group_name == '')
            {
                $response['error']      = true;
                $response['message']    = 'Invalid batch name';
            }
            else
            {
                $param                  = array();
                $param['select']        = 'id';
                $param['name']          = $group_name;
                $param['institute_id']  = $institute_id;
                $param['deleted']       = true;
                if( $id > 0 )
                {
                    $param['exclude_id'] = $id;
                }

                $check_group_exist      = $this->Group_model->group($param);
                
                $param                  = array();
                if(!empty($check_group_exist))
                {
                    $response['error']          = true;
                    $response['message']        = 'Batch name not available';            
                }
                else
                {                    $response['error']      = false;
                    $save                   = array();
                    $save['id']             = $id;
                    $save['gp_account_id']  = $this->config->item('id');
                    $save['gp_name']        = $group_name;
                    $save['gp_created_by']  = $this->__loggedInUser['id'];
                    $save['gp_institute_id']= $institute_id;    
                    // $course_details         = $this->memcache->get('course_'.$course_id, 'course_details', array('id'=>$course_id));
                    $institute_details      = $this->memcache->get(array('key'=>'institute_'.$institute_id), 'institute', array('id' => $institute_id));            
                    
                    $save['gp_institute_code']  = $institute_details['ib_institute_code'];
                    $save['gp_year']            = $this->input->post('group_year');
                    $save['id']                 = $this->Group_model->save($save);  
                    
                    /*Log creation*/
                    if($id){
                        $user_data                      = array();
                        $user_data['user_id']           = $this->__loggedInUser['id'];
                        $user_data['username']          = $this->__loggedInUser['us_name'];
                        $user_data['useremail']          = $this->__loggedInUser['us_email'];
                        $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
                        $user_data['phone_number']      = $this->__loggedInUser['us_phone'];
                        $message_template               = array();
                        $message_template['username']   = $this->__loggedInUser['us_name'];
                        $message_template['batch']      = $group_name;
                        $triggered_activity             = 'batch_updated';
                        log_activity($triggered_activity, $user_data, $message_template);
                    } else {
                        $user_data                      = array();
                        $user_data['user_id']           = $this->__loggedInUser['id'];
                        $user_data['username']          = $this->__loggedInUser['us_name'];
                        $user_data['useremail']          = $this->__loggedInUser['us_email'];
                        $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
                        $user_data['phone_number']      = $this->__loggedInUser['us_phone'];
                        $message_template               = array();
                        $message_template['username']   = $this->__loggedInUser['us_name'];
                        $message_template['batch']      = $group_name;
                        $triggered_activity             = 'batch_created';
                        log_activity($triggered_activity, $user_data, $message_template);
                    }
                    

                    $group                      = array();
                    $group['id']                = $save['id'];
                    $group['batch_name']        = $save['gp_institute_code']." - ".$save['gp_year']." - ".$save['gp_name'];
                    $group['gp_institute_code'] = $save['gp_institute_code'];
                    $group['gp_institute_id']   = $save['gp_institute_id'];
                    $group['gp_name']           = $save['gp_name'];
                    $group['gp_status']         = "1";
                    $group['gp_year']           = $save['gp_year'];
                    $group['users']             = array();
                    $group['users_count']       = 0;
                    $group['users_offset']      = 1;
                    $response['group']          = $group;      
                }
            } 
        }
        else
        {
            $response['error']          = true;
            $response['message']        = 'You have no permission to add batch';
        }
                
        echo json_encode($response);  
        exit;    
    }
    
    function users()
    {
        $user   = $this->__loggedInUser;
        $response           = array();
        $exclude_group_id   = $this->input->post('exclude_group_id');
        $keyword            = $this->input->post('keyword');
        $institute_id       = ($this->input->post('institute_id') !== null)? $this->input->post('institute_id'): '';
        $teacher_id         = isset($this->__role_query_filter['teacher_id'] )? $this->__role_query_filter['teacher_id'] :false ;
        $response['users']  = $this->Group_model->group_members(
                                        array(
                                            'exclude_group_id' => $exclude_group_id, 
                                            'keyword' => $keyword , 
                                            'teacher_id' => $teacher_id, 
                                            'not_deleted'=>true,
                                            'institute_id' => $institute_id )
                                        );
        echo json_encode($response);
    }
    /*
    purpose     : Add user to group
    params      : none
    usage-in    : Batches(Admin)
    edited      : kiran(12/08)
    */
    function save_group_users()
    {
        $group_id               = $this->input->post('group_id');
        $user_ids               = $this->input->post('user_ids');
        $group_name             = $this->input->post('group_name');
        $user_ids               = json_decode($user_ids);
        $user_data              = array();
        $subscription_data      = array();
        $course_notifications   = array();
        if(!empty($user_ids)) //
        {
            $group_courses      = $this->Group_model->group_courses(array('group_id' => $group_id,'select'=>'id,cb_code,cb_title,cb_price,cb_discount,cb_tax_method,cb_is_free,cb_access_validity,cb_validity,cb_validity_date'));
            if(!empty($group_courses))
            {
                $this->load->model('User_model');
            }
            // echo '<pre>'; print_r($group_courses);die;
            $payment_data           = array();

            foreach ($user_ids as $user_id)
            {
                $user               = $this->User_model->get_user_details(array('id' => $user_id, 'select' => 'id, us_groups, us_name, us_email, us_phone'));
                $user_groups        = $user['us_groups']; 
                $user_groups        = explode(',', $user_groups);
                $user_groups[]      = $group_id;
                $user_groups        = implode(',', array_unique($user_groups));

                $save               = array();
                $save['id']         = $user_id;
                $save['us_groups']  = $user_groups;
                $user_data[]        = $save;
                
                if(!empty($group_courses))
                {
                    foreach ($group_courses as $course)
                    {
                        $save_subscription                                  = array();
                        $save_subscription['cs_user_id']                    = $user_id;
                        $save_subscription['cs_course_id']                  = $course['id'];
                        $save_subscription['cs_approved']                   = '1';

                        $course_subscription_date   = date("Y-m-d H:i:s");
                        $course_validity            = (($course['cb_validity']) && $course['cb_validity'] > 0)?$course['cb_validity']:1;
                        $course_startdate           = date("Y-m-d",time());

                        if($course['cb_access_validity'] == 2)
                        {
                            $course_enddate   = $course['cb_validity_date'];
                        }
                        else
                        {
                            $course_enddate   = ($course['cb_access_validity'] == 0)?'2070-12-31':date('Y-m-d', time() + ($course_validity - 1) * 60 * 24 * 60);
                        }

                        $save_subscription['updated_date']            = $course_subscription_date;
                        $save_subscription['cs_subscription_date']    = $course_subscription_date;
                        $save_subscription['cs_start_date']           = $course_startdate;
                        $save_subscription['cs_end_date']             = $course_enddate;
                        $save_subscription['action_by']               = $this->__loggedInUser['id'];

                        if(!isset($course_notifications[$course['id']]))
                        {
                            $course_notifications[$course['id']]            = array();
                            $course_notifications[$course['id']]['name']    = $course['cb_title'];
                            $course_notifications[$course['id']]['users']   = array();
                        }
                        $course_notifications[$course['id']]['users'][]     = $user_id;
                        $this->User_model->save_subscription($save_subscription);

                        /* Payment Details */
                        $user_details                               = array();
                        $user_details['name']                       = $user['us_name'];
                        $user_details['email']                      = $user['us_email'];
                        $user_details['phone']                      = $user['us_phone'];

                        $payment_param                              = array();
                        $payment_param['ph_user_id']                = $user['id'];
                        $payment_param['ph_user_details']           = json_encode($user_details);
                        $payment_param['ph_item_id']                = $course['id'];
                        $payment_param['ph_item_type']              = '1';
                        $payment_param['ph_item_code']              = $course['cb_code'];
                        $payment_param['ph_item_name']              = $course['cb_title'];
                        $payment_param['ph_item_base_price']        = $course['cb_price'];
                        $payment_param['ph_item_discount_price']    = $course['cb_discount'];
                        $payment_param['ph_tax_type']               = $course['cb_tax_method'];

                        $course_price                               = ($course['cb_discount']!=0)?$course['cb_discount']:$course['cb_price'];
                        
                        if($course['cb_is_free'] == '1')
                        {
                            $payment_param['ph_item_base_price']        = 0;
                            $payment_param['ph_item_discount_price']    = 0;
                            $course_price = 0;
                        }
                        
                        $gst_setting                                = $this->settings->setting('has_tax');
                        $cgst                                       = ($gst_setting['as_setting_value']['setting_value']->cgst != '')?$gst_setting['as_setting_value']['setting_value']->cgst:'0';
                        $sgst                                       = ($gst_setting['as_setting_value']['setting_value']->sgst != '')?$gst_setting['as_setting_value']['setting_value']->sgst:'0';
                        $sgst_price                                 = ($sgst / 100) * $course_price;
                        $cgst_price                                 = ($cgst / 100) * $course_price;
                        //cb_tax_method = 1 is exclusive
                        

                        if($course['cb_tax_method'] == '1')
                        {
                            $total_course_price         = $course_price+$sgst_price+$cgst_price;
                        }
                        else 
                        {
                            $total_course_price         = $course_price;
                            $sgst_price                 = ($course_price / (100 + $sgst)) * $sgst;
                            $cgst_price                 = ($course_price / (100 + $cgst)) * $cgst;
                        }
                        
                        $payment_tax_object                         = array();
                        $payment_tax_object['sgst']['percentage']   = $sgst;
                        $payment_tax_object['sgst']['amount']       = round($sgst_price, 2); 
                        $payment_tax_object['cgst']['percentage']   = $cgst;
                        $payment_tax_object['cgst']['amount']       = round($cgst_price, 2); 

                        $payment_param['ph_tax_objects']            = json_encode($payment_tax_object);
                        
                        $payment_param['ph_item_amount_received']   = round($total_course_price, 2);
                        $payment_param['ph_payment_mode']           = '3';

                        $transaction_details                        = array();
                        $transaction_details['transaction_id']      = '';
                        $transaction_details['bank']                = 'By cash';
                        $payment_param['ph_transaction_id']         = '';
                        $payment_param['ph_transaction_details']    = json_encode($transaction_details);
                        $payment_param['ph_account_id']             = config_item('id');
                        $payment_param['ph_payment_gateway_used']   = 'Offline';
                        $payment_param['ph_status']                 = '1';
                        $payment_param['ph_payment_date']           = date('Y-m-d H:i:s');
                        unset($course['cb_groups']);
                        $payment_param['ph_item_other_details']     = json_encode($course);
                        $payment_data[]                             = $payment_param;
                    }
                }

                $this->memcache->delete('enrolled_'.$user_id);
                $this->memcache->delete('mobile_enrolled_'.$user_id);
            }

            if($payment_data)
            {
                $this->load->model('Payment_model');
                $order_ids = $this->Payment_model->save_history_bulk($payment_data);
        
                if(!empty($order_ids))
                {
                    $order_data                      = array();
                    foreach($order_ids as $order_id)
                    {
                        $order_param                 = array();
                        $order_param['id']           = $order_id;
                        $order_param['ph_order_id']  = date('Y').date('m').date('d').$order_id;
                        $order_data[]                = $order_param;
                    }
                    $this->Payment_model->update_history_bulk($order_data);
                }
            }
            
            if(!empty($user_data)){
                $this->User_model->save_user($user_data);
            }

            $student_count                  = (sizeof($user_ids)>1)?sizeof($user_ids).' students':'a student';
            $user_data                      = array();
            $user_data['user_id']           = $this->__loggedInUser['id'];
            $user_data['username']          = $this->__loggedInUser['us_name'];
            $user_data['useremail']         = $this->__loggedInUser['us_email'];
            $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
            $user_data['phone_number']      = $this->__loggedInUser['us_phone'];
            $message_template               = array();
            $message_template['username']   = $this->__loggedInUser['us_name'];
            $message_template['count']      = $student_count;
            $message_template['batch_name'] = $group_name;
            $triggered_activity             = 'student_added_to_batch';
            
            log_activity($triggered_activity, $user_data, $message_template);
            
        }
        $response                   = array();
        $response['error']          = false;
        $response['group_users']    = $this->Group_model->group_users(array('group_id' => $group_id, 'select'=>'id,us_name,us_email, us_image'));

        // echo '<pre>'; print_r($response);die('ddd');
        echo json_encode($response);
        
        foreach($course_notifications as $course_id => $course_notification)
        {
            //Notification
            $this->load->library('Notifier');
            $this->notifier->push(
                array(
                    'action_code' => 'course_subscribed',
                    'assets' => array('course_name' => $course_notification['name'],'course_id' => $course_id),
                    'target' => $course_id,
                    'individual' => true,
                    'push_to' => $course_notification['users']
                )
            );
            //End notification
        }
    }
    
    function remove_users_from_group()
    {
        $limit          = $this->input->post('limit');
        $offset         = $this->input->post('offset');
        $group_id       = $this->input->post('group_id');
        $user_ids       = json_decode($this->input->post('user_ids'));
        // $group_users    = $this->Group_model->group_users(array('group_id' => $group_id, 'select'=>'id,us_name,us_email, us_image'));
        /*echo '<pre>'; 
        print_r($user_ids); 
        print_r($group_users); 
        die('dfgsdfgsfg');*/
        // echo '<pre>'; print_r($this->input->post());die;
        
        if(!empty($user_ids))
        {
            // foreach ($user_ids as $user_id)
            // {
            //     $user               = $this->User_model->user(array('id' => $user_id));
            //     $user_groups        = trim(str_replace(",".$group_id.",", ",", ",".$user['us_groups'].","), ',');
            //     $save               = array();
            //     $save['id']         = $user_id;
            //     $save['us_groups']  = $user_groups;
            //     $this->User_model->save($save);
            // }

            $this->Group_model->remove_user_from_group($user_ids, $group_id);

            $student_count                  = (sizeof($user_ids)>1)?sizeof($user_ids).' students':'a student';
            $user_data                      = array();
            $user_data['user_id']           = $this->__loggedInUser['id'];
            $user_data['username']          = $this->__loggedInUser['us_name'];
            $user_data['useremail']          = $this->__loggedInUser['us_email'];
            $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
            $user_data['phone_number']      = $this->__loggedInUser['us_phone'];
            $message_template               = array();
            $message_template['username']   = $this->__loggedInUser['us_name'];
            $message_template['count']      = $student_count;
            $message_template['batch_name'] = $this->input->post('group_name');
            $triggered_activity             = 'student_removed_from_batch';
            log_activity($triggered_activity, $user_data, $message_template);
        }
        $response                   = array();
        $response['error']          = false;
        // $response['group_users']    = $this->Group_model->group_users(array('group_id' => $group_id, 'select'=>'id,us_name,us_email, us_image'));
        $param              = array();
        $param['group_id']  = $this->input->post('group_id');
        //processing limit and offset
        $total_deleted      = sizeof($user_ids);
        $param['limit']     = $total_deleted;
        $param['offset']    = ($limit*$offset)-$total_deleted;
        $param['offset']    = ($param['offset'] < 0)?0:$param['offset'];
        //end
        $param['select']    = 'users.id, users.us_name, users.us_image, users.us_email';
        $response['group_users']        = $this->Group_model->group_users($param);
        echo json_encode($response);        
    }
    
    function remove_group()
    {
        $group_id       = $this->input->post('group_id');
        $group_name     = $this->input->post('group_name');
        $group_users    = $this->Group_model->group_users(array('group_id' => $group_id));
        $group_data     = array();
        if(!empty($group_users))
        {
            foreach ($group_users as $user)
            {
                $user               = $this->User_model->user(array('id' => $user['id']));
                $user_groups        = trim(str_replace(",".$group_id.",", ",", ",".$user['us_groups'].","), ',');
                $save               = array();
                $save['id']         = $user['id'];
                $save['us_groups']  = $user_groups;
                $group_data[]       = $save;
                
            }
            if(!empty($group_data)){
                $this->User_model->save_user($group_data);
            }
        }
        $save               = array();
        $save['id']         = $group_id;
        $save['gp_deleted'] = '1';
        if($this->Group_model->save($save)){
            $user_data                      = array();
            $user_data['user_id']           = $this->__loggedInUser['id'];
            $user_data['username']          = $this->__loggedInUser['us_name'];
            $user_data['useremail']          = $this->__loggedInUser['us_email'];
            $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
            $user_data['phone_number']      = $this->__loggedInUser['us_phone'];
            $message_template               = array();
            $message_template['username']   = $this->__loggedInUser['us_name'];
            $message_template['batch']      = $group_name;
            $triggered_activity             = 'batch_deleted';
            log_activity($triggered_activity, $user_data, $message_template);
        }

        //check if a batch is overridden if yes then remove it from override table
        $check_override_batch    = $this->Group_model->check_override_batch($group_id);
        if(!empty($check_override_batch)){
            foreach($check_override_batch as $override_batch){
                $save_override['id']    = $override_batch['id'];
                $override_batches       = explode(",",$override_batch['lo_override_batches']);
                if (($key = array_search($group_id, $override_batches)) !== false) {
                    unset($override_batches[$key]);
                }
                $save_override['lo_override_batches']= (!empty($override_batches))?implode(",",$override_batches):'';
                $this->load->model('Test_model');
                if(!empty($override_batches)){
                    $this->Test_model->saveAssesmentOverride($save_override);
                } else {
                    $this->Test_model->deleteAssesmentOverride(array('id'=>$override_batch['id']));
                }
            }
        }
        $filter_param               = array();
        $filter_param['select']     = 'id,cb_groups';
        $filter_param['group_id']   = $group_id;
        $course_group_list          = $this->Group_model->group_courses($filter_param);

        foreach($course_group_list as $course_group){

            $groups_list    = explode(',',$course_group['cb_groups']);
            $sorted_list    = array_diff($groups_list, array($group_id));
            $new_group_list = implode(',',$sorted_list);

            $save_param                 = array();
            $save_param['id']           = $course_group['id'];
            $save_param['cb_groups']    = $new_group_list;
            $this->Course_model->save($save_param);
        }
        $response                   = array();
        $response['error']          = false;
        $response['message']        = 'Batch removed successfully';
        echo json_encode($response);        
    }
    /*
    purpose     : batch wise messaging to user
    params      : none
    usage-in    : Batches(Admin)
    edited      : kiran(12/08)
    */
    function send_message_group() 
    {
        $response['error']      = false;
        $response['message']    = 'Message has been sent sucessfully';
        $subject                = $this->input->post('send_user_subject');
        $message                = base64_decode($this->input->post('send_user_message'));
        $group_id               = $this->input->post('group_id');

        if(!empty($group_id))
        {
            $admin_email_ids            = array();
            $filter_param               = array();
            $filter_param['group_id']   = $group_id;
            $filter_param['select']     = 'id,us_name,us_email, us_email_verified';
            $filter_param['verified']   = 'true';
            $user_emails                = $this->Group_model->group_users($filter_param);
            if(!empty($user_emails))
            {
                $system_message         = array();
                $random_message_id      = rand(1000, 9999);
                $date_time              = date(DateTime::ISO8601);
                $user_email_ids         = array();
                foreach($user_emails as $user_email)
                {
                    $system_message[]   = array(
                        "messageId"     => $random_message_id,
                        "senderId"      => $this->__loggedInUser['id'],
                        "senderName"    => $this->__loggedInUser['us_name'],
                        "senderImage"   => user_path().$this->__loggedInUser['us_image'],
                        "receiverId"    => $user_email['id'],
                        "message"       => $message,
                        "datetime"      => $date_time
                    );
                    $user_email_ids[] = $user_email['us_email'];
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
            }
        }
        else
        {
            $user_email     = $this->input->post('user_email');
            $user_email_ids = array($user_email);
        }
        
        if(!empty($user_email_ids))
        {
            $param                          = array();
            $param['subject'] 	            = $subject;
            $param['body'] 		            = $message;
            $param['to']                    = $user_email_ids;
            $param['strictly_to_recepient'] = true;
            $send                           = $this->ofabeemailer->send_mail($param);
        }
        echo json_encode($response);
    }
    
    function language()
    {
        $response = array();
        $response['language'] = array();
        $response['language'] = get_instance()->lang->language;
        echo json_encode($response);
    }

    function get_courses()
    {
        $param                  = array();
        $response               = array();
        $response['courses']    = $this->Course_model->get_all_courses($param);
        echo json_encode($response);        
    }

    function get_institutes()
    {
        $response               = array();
        // print_r($this->__loggedInUser);
        if($this->is_institution_manager())         // Institute admin
        {
            $this->load->model('Institute_model');
            $user_id                    = $this->__loggedInUser['id'];
            $param                      = array();
            $param['ib_institute_id']   = $user_id;
            $institute                  = $this->Institute_model->get_institute($param);
            $response['institute']      = $institute;
            $response['institute_admin']    = true;
        }
        else                                                // Other users
        {
            $params                 = array();
            $institutes             = $this->memcache->get('institution','get_institutions',$params);
            $response['institutes'] = $institutes;
            $response['institute_admin']    = false;
        }
        echo json_encode($response);
    }

    function get_group() 
    {
        $response               = array();
        $group_id           = $this->input->post('group_id');

        $params             = array();
        $params['id']       = $group_id;
        $params['select']   = 'id, gp_name, gp_institute_id, gp_year';
        $group              = $this->Group_model->group($params);

        $response['group']  = $group;
        echo json_encode($response);
    }
    function  group_course_json()
    {
        $param['group_id']  = $this->input->post('group_id');
        $param['select']    = 'course_basics.cb_title, course_basics.cb_status';
        $group_users        = $this->Group_model->group_courses($param);
        echo json_encode($group_users);
    }
}
    

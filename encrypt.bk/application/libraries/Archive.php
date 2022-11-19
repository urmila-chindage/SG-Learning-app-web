<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Archive
{
    var $CI;
    function __construct()
    {
        $this->CI = & get_instance();
        $this->CI->load->driver('cache');
        $this->CI->load->model('Archive_model');
        $this->CI->load->model('Course_model');
    }

    /*
    * save course archive data 
    */
    public function archive_course($params = array())
    {
        $response                                  = array();
        $response['error']                         = false;
        $course_ids                                = (isset($params['course_ids']))?$params['course_ids']:false;
        $save_archive                              = array();
        $courses                                   = array();
        $users                                     = array();
        $subscriptions                             = array();
        
        // Get courses details
        $courses_param                             = array();
        $courses_param['select']                   = 'id,cb_title,cb_code,cb_is_free';
        $courses_param['course_id_list']           = $course_ids;
        $course_details                            = $this->get_course_details($courses_param);
        foreach($course_details as $course_detail)
        {
            $courses[$course_detail['id']]         = $course_detail;
        }

        // Get enrolled users
        $enrolled_user_param                       = array();
        $enrolled_user_param['select']             = 'cs_course_id,cs_user_id';
        $enrolled_user_param['course_ids']         = $course_ids;
        $enrolled_users                            = $this->CI->Course_model->course_enrolled($enrolled_user_param);
        $course_users                              = array();
        $enrolled_users_id                         = array();
        foreach($enrolled_users as $enrolled_user)
        {
            if(!isset($course_users[$enrolled_user['cs_course_id']]))
            {
                $course_users[$enrolled_user['cs_course_id']] = array();
            }
            $course_users[$enrolled_user['cs_course_id']][] = $enrolled_user['cs_user_id'];
            $enrolled_users_id[] = $enrolled_user['cs_user_id'];
        }
        $enrolled_users_id                         = array_unique($enrolled_users_id);
        
        // Get enrolled users details
        $users_param                               = array();
        $users_param['select']                     = 'id,us_name,us_email,us_register_number,us_image,us_about,us_phone,us_role_id,us_institute_id,us_branch,us_branch_code,us_institute_code,us_register_no,us_groups';
        $users_param['user_ids']                   = $enrolled_users_id;
        $user_details                              = $this->get_user_details($users_param);
        foreach($user_details as $user_detail)
        {
            $users[$user_detail['id']]             = $user_detail;
        }

        // Get subscription details
        $subscription_param                        = array();
        $subscription_param['select']              = 'id,cs_course_id,cs_user_id,cs_user_name,cs_user_groups,cs_user_institute,cs_subscription_date,cs_start_date,cs_end_date,cs_certificate_issued,cs_download_certificate,cs_percentage,cs_lecture_log,cs_auto_grade,cs_manual_grade';
        $subscription_param['course_ids']          = $course_ids;
        $subscription_details                      = $this->get_subscription_details($subscription_param);
        foreach($subscription_details as $subscription_detail)
        {
            $subscriptions[$subscription_detail['cs_course_id']][$subscription_detail['cs_user_id']]            = $subscription_detail;
        }
        
        // Set archive data
        if(!empty($courses))
        {
            foreach ($courses as $course)
            {
                $enrolled_users                               = (isset($course_users[$course['id']]))?$course_users[$course['id']]:array();
                foreach($enrolled_users as $enrolled_user)
                {
                    $archive_params                           = array();
                    $archive_params['sa_course_id']           = $course['id'];
                    $archive_params['sa_course_title']        = (isset($course['cb_title']))?$course['cb_title']:NULL;
                    $archive_params['sa_course_code']         = (isset($course['cb_code']))?$course['cb_code']:NULL;
                    $archive_params['sa_course_details']      = json_encode($course);
                    $archive_params['sa_user_id']             = $enrolled_user; 
                    $user_data                                = (isset($users[$enrolled_user]))?$users[$enrolled_user]:array();        
                    $archive_params['sa_user_name']           = (isset($user_data['us_name']))?$user_data['us_name']:NULL;
                    $archive_params['sa_user_email']          = (isset($user_data['us_email']))?$user_data['us_email']:NULL;
                    $archive_params['sa_user_register_number']= (isset($user_data['us_register_number']))?$user_data['us_register_number']:NULL;
                    $archive_params['sa_user_phone']          = (isset($user_data['us_phone']))?$user_data['us_phone']:NULL;
                    $archive_params['sa_user_institute_id']   = (isset($user_data['us_institute_id']))?$user_data['us_institute_id']:NULL;
                    $archive_params['sa_user_groups']         = (isset($user_data['us_groups']))?$user_data['us_groups']:NULL;
                    $archive_params['sa_user_details']        = json_encode($user_data);
                    $subscription_data                        = (isset($subscriptions[$course['id']][$enrolled_user]))?$subscriptions[$course['id']][$enrolled_user]:array();
                    $archive_params['sa_cs_startdate']        = (isset($subscription_data['cs_start_date']))?$subscription_data['cs_start_date']:NULL;
                    $archive_params['sa_cs_enddate']          = (isset($subscription_data['cs_end_date']))?$subscription_data['cs_end_date']:NULL;
                    $archive_params['sa_subscription_details']= json_encode($subscription_data);
                    $archive_params['sa_account_id']          = config_item('id');
                    $save_archive[]                           =  $archive_params; 
                } 
            }
        }

        //echo '<pre>'; print_r($save_archive);die('109');
        // Save archive data
        if(!empty($save_archive))
        {
            $this->CI->Archive_model->save_archive_process($save_archive);
            $response['message']    = 'Subscription archieved successfully!';
        }
        else
        {
            $response['error']      = true;
            $response['message']    = 'No data to archive!';
        }
        //echo json_encode($response);
    }

    /*
    * save user archive data 
    */
    public function archive_user($params = array())
    {
        $response                                  = array();
        $response['error']                         = false;
        $user_ids                                  = (isset($params['user_ids']))?$params['user_ids']:false;
        $save_archive                              = array();
        $courses                                   = array();
        $users                                     = array();
        $subscriptions                             = array();

        // Get enrolled courses
        $enrolled_course_param                     = array();
        $enrolled_course_param['select']           = 'cs_course_id,cs_user_id';
        $enrolled_course_param['user_ids']         = $user_ids;
        $enrolled_courses                          = $this->CI->Course_model->course_enrolled($enrolled_course_param);
        $course_users                              = array();
        $enrolled_courses_id                       = array();
        foreach($enrolled_courses as $enrolled_course)
        {
            if(!isset($course_users[$enrolled_course['cs_course_id']]))
            {
                $course_users[$enrolled_course['cs_course_id']] = array();
            }
            $course_users[$enrolled_course['cs_course_id']][] = $enrolled_course['cs_user_id'];
            $enrolled_courses_id[] = $enrolled_course['cs_course_id'];
        }
        $enrolled_courses_id                       = array_unique($enrolled_courses_id);
        
        // Get courses details
        $courses_param                             = array();
        $courses_param['select']                   = 'id,cb_title,cb_code,cb_is_free';
        $courses_param['course_id_list']           = $enrolled_courses_id;
        $course_details                            = $this->get_course_details($courses_param);
        foreach($course_details as $course_detail)
        {
            $courses[$course_detail['id']]         = $course_detail;
        }

        // Get enrolled users details
        $users_param                               = array();
        $users_param['select']                     = 'id,us_name,us_email,us_register_number,us_image,us_about,us_phone,us_role_id,us_institute_id,us_branch,us_branch_code,us_institute_code,us_register_no,us_groups';
        $users_param['user_ids']                   = $user_ids;
        $user_details                              = $this->get_user_details($users_param);
        foreach($user_details as $user_detail)
        {
            $users[$user_detail['id']]             = $user_detail;
        }

        // Get subscription details
        $subscription_param                        = array();
        $subscription_param['select']              = 'id,cs_course_id,cs_user_id,cs_user_name,cs_user_groups,cs_user_institute,cs_subscription_date,cs_start_date,cs_end_date,cs_certificate_issued,cs_download_certificate,cs_percentage,cs_lecture_log,cs_auto_grade,cs_manual_grade';
        $subscription_param['course_ids']          = $enrolled_courses_id;
        $subscription_details                      = $this->get_subscription_details($subscription_param);
        foreach($subscription_details as $subscription_detail)
        {
            $subscriptions[$subscription_detail['cs_course_id']][$subscription_detail['cs_user_id']]            = $subscription_detail;
        }
        
        // Set archive data
        if(!empty($courses))
        {
            foreach ($courses as $course)
            {
                $enrolled_users                               = (isset($course_users[$course['id']]))?$course_users[$course['id']]:array();
                foreach($enrolled_users as $enrolled_user)
                {
                    $archive_params                           = array();
                    $archive_params['sa_course_id']           = $course['id'];
                    $archive_params['sa_course_title']        = (isset($course['cb_title']))?$course['cb_title']:NULL;
                    $archive_params['sa_course_code']         = (isset($course['cb_code']))?$course['cb_code']:NULL;
                    $archive_params['sa_course_details']      = json_encode($course);
                    $archive_params['sa_user_id']             = $enrolled_user; 
                    $user_data                                = (isset($users[$enrolled_user]))?$users[$enrolled_user]:array(); 
                    $archive_params['sa_user_name']           = (isset($user_data['us_name']))?$user_data['us_name']:NULL;
                    $archive_params['sa_user_email']          = (isset($user_data['us_email']))?$user_data['us_email']:NULL;
                    $archive_params['sa_user_register_number']= (isset($user_data['us_register_number']))?$user_data['us_register_number']:NULL;
                    $archive_params['sa_user_phone']          = (isset($user_data['us_phone']))?$user_data['us_phone']:NULL;
                    $archive_params['sa_user_institute_id']   = (isset($user_data['us_institute_id']))?$user_data['us_institute_id']:NULL;
                    $archive_params['sa_user_groups']         = (isset($user_data['us_groups']))?$user_data['us_groups']:NULL;      
                    $archive_params['sa_user_details']        = json_encode($user_data);
                    $subscription_data                        = (isset($subscriptions[$course['id']][$enrolled_user]))?$subscriptions[$course['id']][$enrolled_user]:array();
                    $archive_params['sa_cs_startdate']        = (isset($subscription_data['cs_start_date']))?$subscription_data['cs_start_date']:NULL;
                    $archive_params['sa_cs_enddate']          = (isset($subscription_data['cs_end_date']))?$subscription_data['cs_end_date']:NULL;
                    $archive_params['sa_subscription_details']= json_encode($subscription_data);
                    $archive_params['sa_account_id']          = config_item('id');
                    $save_archive[]                           =  $archive_params; 
                } 
            }
        }

        //echo '<pre>'; print_r($save_archive);die('216');
        // Save archive data
        if(!empty($save_archive))
        {
            $this->CI->Archive_model->save_archive_process($save_archive);
            $response['message']    = 'Subscription archieved successfully!';
        }
        else
        {
            $response['error']      = true;
            $response['message']    = 'No data to archive!';
        }
        //echo json_encode($response);
    }

    /*
    * save subscription archive data 
    */
    public function subscription_archive_process($params = array())
    {
        $response                                  = array();
        $response['error']                         = false;
        $user_id                                   = (isset($params['user_id']))?$params['user_id']:false;
        $user_ids                                  = (isset($params['user_ids']))?$params['user_ids']:false;
        $course_id                                 = (isset($params['course_id']))?$params['course_id']:false;
        $save_archive                              = array();
        
        // Get courses details
        $courses_param                             = array();
        $courses_param['select']                   = 'id,cb_title,cb_code,cb_is_free';
        $courses_param['course_id']                = $course_id;
        $course_details                            = $this->get_course_details($courses_param);
        $course_details                            = (isset($course_details[0]))?$course_details[0]:array();

        // Get enrolled users details
        $users_param                               = array();
        $users_param['select']                     = 'id,us_name,us_email,us_register_number,us_image,us_about,us_phone,us_role_id,us_institute_id,us_branch,us_branch_code,us_institute_code,us_register_no,us_groups';
        $users_param['user_ids']                   = $user_ids ? $user_ids : array($user_id);
        $user_details                              = $this->get_user_details($users_param);
        $user_details                              = (isset($user_details))?$user_details:array();
        
        // Get subscription details
        $subscription_param                        = array();
        $subscription_param['select']              = 'id,cs_course_id,cs_user_id,cs_user_name,cs_user_groups,cs_user_institute,cs_subscription_date,cs_start_date,cs_end_date,cs_certificate_issued,cs_download_certificate,cs_percentage,cs_lecture_log,cs_auto_grade,cs_manual_grade';
        $subscription_param['course_id']           = $course_id;
        
        
        // Set archive data
        $archive_params                           = array();
        for($i = 0; $i < count($user_details); $i++)
        {
            $subscription_param['user_id']            = $user_details[$i]['id'];
            $subscription_details                     = $this->get_subscription_details($subscription_param);
            $subscription_details                     = (isset($subscription_details))?$subscription_details:array();

            $archive_params['sa_course_id']           = $course_id;
            $archive_params['sa_course_title']        = (isset($course_details['cb_title']))?$course_details['cb_title']:NULL;
            $archive_params['sa_course_code']         = (isset($course_details['cb_code']))?$course_details['cb_code']:NULL;
            $archive_params['sa_course_details']      = json_encode($course_details);
            $archive_params['sa_user_id']             = (isset($user_details[$i]['id']))?$user_details[$i]['id']:NULL;
            $archive_params['sa_user_name']           = (isset($user_details[$i]['us_name']))?$user_details[$i]['us_name']:NULL;
            $archive_params['sa_user_email']          = (isset($user_details[$i]['us_email']))?$user_details[$i]['us_email']:NULL;
            $archive_params['sa_user_register_number']= (isset($user_details[$i]['us_register_number']))?$user_details[$i]['us_register_number']:NULL;
            $archive_params['sa_user_phone']          = (isset($user_details[$i]['us_phone']))?$user_details[$i]['us_phone']:NULL;
            $archive_params['sa_user_institute_id']   = (isset($user_details[$i]['us_institute_id']))?$user_details[$i]['us_institute_id']:NULL;
            $archive_params['sa_user_groups']         = (isset($user_details[$i]['us_groups']))?$user_details[$i]['us_groups']:NULL;
            $archive_params['sa_user_details']        = json_encode($user_details[$i]);
            $archive_params['sa_cs_startdate']        = (isset($subscription_details['cs_start_date']))?$subscription_details['cs_start_date']:NULL;
            $archive_params['sa_cs_enddate']          = (isset($subscription_details['cs_end_date']))?$subscription_details['cs_end_date']:NULL;
            $archive_params['sa_subscription_details']= json_encode($subscription_details);
            $archive_params['sa_account_id']          = config_item('id');
            $save_archive[]                           =  $archive_params; 
        }
        //echo '<pre>'; print_r($save_archive);die('262');
        
        // Save archive data
        if(!empty($save_archive))
        {
            $this->CI->Archive_model->save_archive_process($save_archive);
            $response['message']    = 'Subscription archieved successfully!';
        }
        else
        {
            $response['error']      = true;
            $response['message']    = 'No data to archive!';
        }
        //echo json_encode($response);
    }

    /*
    * get user details 
    */
    private function get_user_details($param=array())
    {
        $this->CI->load->model('User_model');
        $user_details        = $this->CI->User_model->users($param);
        return $user_details;
    }

    /*
    * get course details 
    */
    private function get_course_details($param=array())
    {
        $course_details            = $this->CI->Course_model->course_new($param);
        return $course_details;
    }

    /*
    * get subscription details 
    */
    private function get_subscription_details($param=array())
    {
        $subscription_details = $this->CI->Course_model->subscription_details($param);
        //print_r($subscription_details); die('324');
        return $subscription_details;
    }

}
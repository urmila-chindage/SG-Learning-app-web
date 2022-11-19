<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Homepage extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->auth->is_logged_in_user(false, false, 'user');
    }

    public function index()
    {   
        $objects        = array();
        $objects['key'] = 'home';
        $callback       = 'home';
        $home_cache     = $this->memcache->get($objects, $callback);
        $user           = $this->auth->get_current_user_session('user');
        $data           = array();
        // if(!$home_cache) 
        // {
        //     $this->load->model(array('Homepage_model', 'Course_model','Category_model','Settings_model'));
        //     $data['banner']         = $this->Homepage_model->get_banner();
        //     $data['title']          = $this->config->item('site_name');
        //     $data['categories']     = $this->Category_model->categories(array('direction'=>'ASC', 'parent_id'=>0, 'status'=>1, 'not_deleted' => true, 'limit' => 4));
        //     $data['testimonials']   = $this->Settings_model->get_testimonials(array('select'=>'t_name, t_other_detail, t_image, t_text', 'limit' => 5, 'featured' => true));
        //     $homepage_data          = $this->memcache->set($objects['key'], $data);
        // } 
        // else 
        // {
        //     $data = $home_cache;
        // }
        $data = $home_cache;

        // $objects        = array();
        // $objects['key'] = 'notifications';
        // $callback       = 'notifications';
        // $data['information_bars'] = $this->memcache->get($objects, $callback);
        
        $objects        = array();
        $objects['key'] = 'top_courses';
        $callback       = 'top_courses';
        $top_courses     = $this->memcache->get($objects, $callback, array('limit' => '8'));
        
        if (isset($user['id'])) 
        {
            $courses            = array();
            $objects            = array();
            $objects['key']     = 'enrolled_' . $user['id'];
            $callback           = 'my_subscriptions';
            $params             = array('user_id' => $user['id']);
            $enrolled_courses   = $this->memcache->get($objects, $callback, $params);
            //echo '<pre>';print_r($enrolled_courses);die;
            if(!empty($enrolled_courses))
            {
                foreach ($enrolled_courses as $enrolled) 
                {
                    $courses[$enrolled['course_id']]    = $enrolled;
                    $course_completion                  = 0;
                    $lecture_count                      = 0;
                    foreach($enrolled['lectures'] as $lecture)
                    {
                        $lecture_count++;
                    }

                    $course_completion += $enrolled['cs_percentage'];
                    $enrolled_courses[$enrolled['course_id']]['course_completion']      = $lecture_count ? round(($course_completion / $lecture_count)) : 0;
                    $courses[$enrolled['course_id']]['course_completion']               = $course_completion;//$lecture_count ? round(($course_completion / $lecture_count)) : 0;
                    // if($this->config->item('id') == 41 &&  $enrolled['course_id'] == 2)
                    // {
                    //     echo $lecture_count ."<>".$course_completion;die;
                    // }
                }    
            }
            //echo '<pre>';print_r($enrolled_courses);die;
            //bundles
            $objects            = array();
            $objects['key']     = 'bundle_enrolled_' . $user['id'];
            $callback           = 'my_bundle_subscriptions';
            $params             = array('user_id' => $user['id']);
            $enrolled_bundles   = $this->memcache->get($objects, $callback, $params);
            // 
            foreach ($enrolled_bundles as $enrolled) {
                $enrolled_bundles[$enrolled['bundle_id']] = $enrolled;
            }

            if(!empty($top_courses))
            {
                foreach ($top_courses as $t_key => $top_course) 
                {

                    $item_type    = isset($top_course['item_type'])?$top_course['item_type']:'';
                    if($item_type == 'bundle')
                    {
                        
                        $top_courses[$t_key]['enrolled']        = isset($enrolled_bundles[$top_course['id']]);
                   
                        if($top_courses[$t_key]['enrolled'])
                        {
                            unset($top_courses[$t_key]);
                            
                            // $courses[$c_key]['bs_approved'] = $enrolled_bundles[$course['id']]['bs_approved'];
                        }else{
                            $top_courses[$t_key]['bundle_length']   = isset($top_course['c_courses'])?count(json_decode($top_course['c_courses'],true)):'0';
                        }

                    }
                    else if($item_type == 'course')
                    {
                        $top_course_id                      = isset($top_course['cs_course_id'])?$top_course['cs_course_id']:'';
                        $top_courses[$t_key]['enrolled']    = isset($courses[$top_course_id]);
                        if ($top_courses[$t_key]['enrolled']) 
                        {
                            $top_courses[$t_key]['course_id']                    = $top_course_id;
                            $top_courses[$t_key]['cs_end_date']                  = $courses[$top_course_id]['cs_end_date'];
                            $top_courses[$t_key]['cs_course_validity_status']    = $courses[$top_course_id]['cs_course_validity_status'];
                            $top_courses[$t_key]['cs_approved']                  = $courses[$top_course_id]['cs_approved'];
                            $top_courses[$t_key]['cs_last_played_lecture']       = $courses[$top_course_id]['cs_last_played_lecture'];
                            $top_courses[$t_key]['percentage']                   = $courses[$top_course_id]['course_completion'];
                            $top_courses[$t_key]['course_completion']            = $courses[$top_course_id]['course_completion'];
                            $top_courses[$t_key]['cb_is_free']                   = $top_course['cb_is_free']; 
                            $top_courses[$t_key]['cb_price']                     = $top_course['cb_price'];
                            $top_courses[$t_key]['cb_discount']                  = $top_course['cb_discount'];
                            

                          
                            /*$today      = date('Y-m-d');
                            $expire     = date_diff(date_create($today),date_create($courses[$top_course_id]['cs_end_date'])); 
                            
                            $now        = time(); // or your date as well
                            $your_date  = strtotime($courses[$top_course_id]['cs_end_date'] .' +1 day');
                            $datediff   = $your_date - $now;

                            $top_courses[$t_key]['expired']              = ceil($datediff / (60 * 60 * 24)) > 0?false:true;
                            $top_courses[$t_key]['expire_in']            = $expire->format("%R%a");
                            $top_courses[$t_key]['expire_in_days']       = $expire->format("%a");
                            $top_courses[$t_key]['validity_format_date'] = date('d-m-Y',strtotime($courses[$top_course_id]['cs_end_date']));*/

                            $now                                        = time(); // or your date as well $enroll['cs_end_date']
                            $start_date                                 = strtotime(date('Y-m-d'));
                            $your_date                                  = strtotime($courses[$top_course_id]['cs_end_date'].' + 1 days');
                            $datediff                                   = $your_date - $now;
                            $today                                      = date('Y-m-d');
                            $expire                                     = date_diff(date_create($today), date_create($top_courses[$t_key]['cs_end_date']));
                            $top_courses[$t_key]['expired']             = ceil($datediff / (60 * 60 * 24)) > 0? false:true;
                            $top_courses[$t_key]['expire_in']           = $expire->format("%R%a");
                            $expires_in                                 = ceil($datediff / (60 * 60 * 24));// ($your_date - $start_date)/60/60/24;
                            $top_courses[$t_key]['expire_in_days']      = $expires_in;
                            $top_courses[$t_key]['validity_format_date']= date('d-m-Y', strtotime($top_courses[$t_key]['cs_end_date']));

                        }
                    }
                } 
                //echo '<pre>';print_r($top_courses);die;   
            }
        } 
        else
        {
            
            if(!empty($top_courses))
            {
                // echo "<pre>";print_r($top_courses);exit;
                foreach ($top_courses as $key => $course) 
                {
                    $top_courses[$key]['enrolled'] = false;
                    if($course['item_type'] == 'bundle'){
                        $top_courses[$key]['bundle_length']   = isset($course['c_courses'])?count(json_decode($course['c_courses'],true)):'0';
                    }
                }    
            }
        }
        $data['top_course'] = array_slice($top_courses,0,8);

       
     

        /* Events */
        $range_from              = date("Y-m-d");
        $event_objects            = array();
        $event_objects['key']     = 'events';
        $event_callback           = 'events';
       
        $event_params             = array('date_from' => $range_from);
        $latest_events            = $this->memcache->get($event_objects, $event_callback, $event_params);
        $data['events']           = $latest_events;

        /* Courses count */
        $course_objects           = array();
        $course_objects['key']    = 'all_courses';
        $course_callback          = 'all_courses';
        $all_courses              = $this->memcache->get($course_objects, $course_callback,array('not_subscribed' => true));
        $total_courses            = (isset($all_courses))?sizeof($all_courses):'0';
        $data['total_courses']    = $total_courses;

        /* Universities count */
        $institute_objects        = array();
        $institute_objects['key'] = 'institutes';
        $institute_callback       = 'institutes';
        $all_institutes           = $this->memcache->get($institute_objects, $institute_callback,array());
        $total_institutes         = sizeof($all_institutes);
        $data['total_institutes'] = (isset($total_institutes))?$total_institutes:'0';

        /* Users Count */
        $user_param               = array();
        $user_param['role_id']    = '2';
        $user_param['count']      = true;
        $user_objects             = array();
        $user_objects['key']      = 'total_students';
        $user_callback            = 'total_students';
        $total_students           = $this->memcache->get($user_objects, $user_callback, $user_param);
        $data['total_students']   = (isset($total_students))?$total_students:'0';
        
        //$this->memcache->delete('enrolled_students');
        /* Enrolled students count */
        $students_objects         = array();
        $students_objects['key']  = 'enrolled_students';
        $students_callback        = 'enrolled_students';
        $enrolled_students        = $this->memcache->get($students_objects, $students_callback, array());
        $data['enrolled_students']= $enrolled_students;

        // foreach($latest_events as $latest_event)
        // {
        //     $nameOfDay = date('D', strtotime($latest_event['ev_date']));
        //     $event_date= explode('-',$latest_event['ev_date']);
        //     //echo $event_date['2'];
        // }

        //  echo '<pre>';print_r($top_courses);die;
        $data['session'] = $user;
        $this->load->view($this->config->item('theme') . '/homepage', $data);
    }


    public function get_live_lectures()
    {
        $data = array();
        $data['live_lectures'] = get_online_lectures();
        echo json_encode($data);
    }

    public function set_hide_live()
    {
        $hide_live = $this->input->post('hide_live');
        $_SESSION['hide_live'] = $hide_live;
    }

    function send_aws_mail_thread()
    {
        if($_SERVER['HTTP_REQUEST_TOKEN'] == sha1(config_item('acct_domain').config_item('id')))
        {
            $request = $this->input->post();
            if(isset($request['force_recipient']) && $request['force_recipient'] == true)
            {
                $bcc = json_decode($request['bcc']);
                //echo '<pre>'; print_r($bcc);die;
                if(!empty($bcc))
                {
                    foreach($bcc as $to)
                    {
                        $request['bcc'] = array($to);
                        $this->ofabeemailer->send_aws_mail_thread($request);    
                    }
                }
            }
            else
            {
                $request['bcc'] = json_decode($request['bcc']);
                $this->ofabeemailer->send_aws_mail_thread($request);    
            }
        }
        else
        {
            echo 'Unauthorized request.<br />';
        }
    }
    function get_in_touch()
    {
     
        $response               = array();
        $response['error']      = true;
        $response['success']    = 0;
        $response['message']    = 'email sending failed';
       
        $name                   = $this->input->post('name');
        $email                  = $this->input->post('email');
        $message                = ( $this->input->post('message') ) ? base64_decode( $this->input->post('message') ) : '';
       
        $template               = $this->ofabeemailer->template( array('email_code' => 'Contact_mail') );
        
        $param                  = array();
        $param['from']          = config_item('site_name').'<'.$this->config->item('site_email').'>';
        $param['to']            = array( $this->config->item('site_email') ,'vini@enfintechnologies.com');
        // $param['to']            = array( trim($to_mail) );
        $param['subject']       = $template['em_subject'];
       
        $contents               = array (
            'site_name'         => config_item('site_name')
            , 'user_name'       => $name
            , 'user_email'      => $email
            , 'user_message'    => $message
        );
        $param['body']          = $this->ofabeemailer->process_mail_content($contents, $template['em_message']);
        $send                   = $this->ofabeemailer->send_mail($param);
       
        if( $send['success'] == 1 )
        {
            $response['error']      = false;
            $response['success']    = 1;
            $response['message']    = 'email sending successsfully';
        }
        echo json_encode($response);
        die;
    }

    function share(){
        ?>
        <button id="shareButton">Share</button>
        <script>
        var shareButton = document.getElementById('shareButton');
            shareButton.addEventListener('click', event => {
                /*if (navigator.share) {
                    navigator.share({
                    title: 'WebShare API Demo',
                    url: 'https://SGlearningapp.com/'
                    }).then(() => {
                    console.log('Thanks for sharing!');
                    })
                    .catch(console.error);
                } else {
                    shareDialog.classList.add('is-open');
                }*/

                if (navigator.share) {
                    navigator.share({
                        title: 'Web Fundamentals',
                        text: 'Check out Web Fundamentals — it rocks!',
                        url: 'https://developers.google.com/web',
                    })
                        .then(() => console.log('Successful share'))
                        .catch((error) => console.log('Error sharing', error));
                    }

                });
        </script>
        <?php
    }
}

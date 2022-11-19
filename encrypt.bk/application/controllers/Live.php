<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Live extends CI_Controller 
{
    public $actions;
    function __construct()
    {
        parent::__construct();
        $admin_session    = $this->auth->get_current_user_session('admin');
        $user_session     = $this->auth->get_current_user_session('user');
         if (!$admin_session && !$user_session)
         {
                 redirect('login');
         }
        $this->actions = config_item('actions');
        
        $this->load->model(array('Course_model'));

    }
    function play($id)
    {
        $admin_session  = $this->auth->get_current_user_session('admin');
        $user_session  = $this->auth->get_current_user_session('user');
        $rtmp_url = 'rtmp://138.201.196.47:1935/vod';
        $liveid   = '0';
        $role = 'viewer';
        $user_id = 'guest';
        if($admin_session){
          $role = 'presenter';
          $user_id = $admin_session['id'];
          $user_name = $admin_session['us_name'];
        }else if($user_session){
          $role    = 'viewer'; 
          $user_id = $user_session['id'];
          $user_name = $user_session['us_name'];
        }
        $key = 'frg_4hjy';
        $this->load->library('ofacrypt');
        $data              = array();
        $data['v1']  = $this->ofacrypt->encrypt($id,$key);// stream id
        $data['v2']  = $this->ofacrypt->encrypt($rtmp_url,$key); // rtmp url
        $data['v3']  = $this->ofacrypt->encrypt($user_id,$key); // user id
        $data['v4']  = $this->ofacrypt->encrypt('0',$key); 
        $data['v5']  = $this->ofacrypt->encrypt('0',$key); 
        $data['v6']  = $this->ofacrypt->encrypt($liveid,$key); // course id
        $data['v7']  = $this->ofacrypt->encrypt(base_url(),$key); // domain ( https://[domainname]/)
        $data['v8']  = $this->ofacrypt->encrypt($liveid,$key); // appid (institute name)
        $data['v9']  = $this->ofacrypt->encrypt($liveid,$key); // subtopic id 
        $data['v10'] = $this->ofacrypt->encrypt('0',$key); // bandwidth value 35000
        $data['v11'] = $this->ofacrypt->encrypt('0',$key); // quality 90
        $data['v12'] = $this->ofacrypt->encrypt($role,$key); 
        $data['v13'] = $this->ofacrypt->encrypt('live_service/',$key); // screenshare active 0
        $this->load->view($this->config->item('theme').'/play', $data);
    }
    function join($liveid='')
    {
        if($liveid == ''){
            redirect('dashboard');
        }else{
            $get_live_details = $this->Course_model->get_course_live(array('id' => $liveid));
            $course_name      = $this->Course_model->course(array('id' => $get_live_details['ll_course_id']));
            $lecture_name     = $this->Course_model->lecture(array('id'=> $get_live_details['ll_lecture_id']));
            
            $admin_session  = $this->auth->get_current_user_session('admin');
            $user_session  = $this->auth->get_current_user_session('user');
            $rtmp_url = 'rtmp://138.201.196.47:1935/ofabeevirtualclass';
            $role = 'viewer';
            $user_id = 'guest';
            $user_name = 'guest';

            if($admin_session){
              $role = 'presenter';
              $user_id = $admin_session['id'];
              $user_name = $admin_session['us_name'];
            }else if($user_session){
              $role    = 'viewer'; 
              $user_id = $user_session['id'];
              $user_name = $user_session['us_name'];
            }else if($teacher_session){
              $role = 'presenter';
              $user_id = $teacher_session['id'];
              $user_name = $teacher_session['us_name'];
            }
            $key = 'frg_4hjy';
            $this->load->library('ofacrypt');
            $data              = array();
            $x   = '';
            $data['role'] = $role;
            $data['v1']  = $this->ofacrypt->encrypt($rtmp_url,$key);// RTMP urls
            $data['v2']  = $this->ofacrypt->encrypt($liveid,$key); // institute name
            $data['v3']  = $this->ofacrypt->encrypt($role,$key); // role , presenter or viewer
            $data['v4']  = $this->ofacrypt->encrypt($user_id,$key); // user id
            $data['v5']  = $this->ofacrypt->encrypt($user_name,$key); // name of user
            $data['v6']  = $this->ofacrypt->encrypt($liveid,$key); // course id
            $data['v7']  = $this->ofacrypt->encrypt(base_url(),$key); // domain ( https://[domainname]/)
            $data['v8']  = $this->ofacrypt->encrypt($liveid,$key); // appid (institute name)
            $data['v9']  = $this->ofacrypt->encrypt($liveid,$key); // subtopic id 
            $data['v10'] = $this->ofacrypt->encrypt('35000',$key); // bandwidth value 35000
            $data['v11'] = $this->ofacrypt->encrypt('90',$key); // quality 90
            $data['v12'] = $this->ofacrypt->encrypt('0',$key); // screenshare active 0
            $data['v13'] = $this->ofacrypt->encrypt('wowza',$key); // screenshare active 0
            $data['v14'] = $this->ofacrypt->encrypt('/usr/local/WowzaStreamingEngine-4.5.0/content/',$key); // screenshare active 0
            $data['v15'] = $this->ofacrypt->encrypt('live_service/',$key); // screenshare active 0
            $data['live_id'] = $liveid;
            $data['course_name'] = $course_name['cb_title'];
            $data['lecture_name']= $lecture_name['cl_lecture_name'];
            $session           = $this->auth->get_current_user_session('user');
            
            $this->load->view($this->config->item('theme').'/live', $data);
        }
    } 
    function getLiveStatus($liveid){
        $get_live_details = $this->Course_model->get_course_live(array('id' => $liveid));
        $data =  array();
        $data['status'] = $get_live_details['ll_is_online'];
        echo json_encode($data);
        die;
    }
    function golive($liveid)
    {
        if($liveid == ''){
            redirect('dashboard');
        }else{
            $get_live_details = $this->Course_model->get_course_live(array('id' => $liveid));
            $course_name      = $this->Course_model->course(array('id' => $get_live_details['ll_course_id']));
            $lecture_name     = $this->Course_model->lecture(array('id'=> $get_live_details['ll_lecture_id']));

            $admin_session  = $this->auth->get_current_user_session('admin');
            $user_session  = $this->auth->get_current_user_session('user');
            $teacher_session  = $this->auth->get_current_user_session('teacher');
            $role = 'viewer';
            $user_id = 'guest';
            $user_name = 'guest';
            $user_image = default_user_path().'default.jpg';
            if($admin_session){
              $role = '1';
              $user_id = $admin_session['id'];
              $user_name = $admin_session['us_name'];
              $user_image = $admin_session['us_image'];
              $user_email = $admin_session['us_email'];
              if($user_image == 'default.jpg'){
                $user_image = default_user_path().$user_image;
              }else{
                $user_image = user_path().$user_image;
              }
            }else if($user_session){
              $role    = '0'; 
              $user_id = $user_session['id'];
              $user_name = $user_session['us_name'];
              $user_image = $user_session['us_image'];  
              $user_email = $user_session['us_email'];
              if($user_image == 'default.jpg'){
                $user_image = default_user_path().$user_image;
              }else{
                $user_image = user_path().$user_image;
              }
            }
            else if($teacher_session){
                $role = '1';
                $user_id = $teacher_session['id'];
                $user_name = $teacher_session['us_name'];
                $user_image = $teacher_session['us_image'];
                $user_email = $teacher_session['us_email'];
                if($user_image == 'default.jpg'){
                  $user_image = default_user_path().$user_image;
                }else{
                  $user_image = user_path().$user_image;
                }
            }
            $data              = array();
            $x   = '';
            $data['role']        = $role;
            $data['user_id']     = $user_id;
            $data['user_name']   = $user_name;
            $data['user_email']  = $user_email;
            $data['mode']        = $role;
            $data['room']        = $liveid;
            $data['user_image']  = $user_image;
            $data['course_name'] = $course_name['cb_title'];
            $data['lecture_name']= $lecture_name['cl_lecture_name'];
            $session           = $this->auth->get_current_user_session('user');
            $this->load->view($this->config->item('theme').'/golive', $data);
        }
    } 
    function beta()
    {
        $data = array();
        $this->load->view($this->config->item('theme').'/live', $data);
    }
    
    public function change_live_status_onclose()
    {
        $live_id        = $this->input->post('live_id');
        $make_online    = $this->input->post('make_online');
        
        $save                   = array();
        $save['id']             = $live_id;
        $save['ll_is_online']   = $make_online;
        $this->Course_model->save_live_lecture($save);
        echo json_encode($save);
        
    }
    
    function download_xml()
    {
        $destination = "http://onlineprofesor.com/olplive.xml";
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($destination).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($destination));
        readfile($destination);
        exit();
    }
    /*function save_lecture()
    {
        $this->load->model(array('Course_model'));

        
        $lecture_id             = $this->input->post('lecture_id');
        $live_lecture           = $this->Course_model->lecture(array('id' => $lecture_id));
        $new_position           = intval($live_lecture['cl_order_no']+1);

        
        $save                           = array();
        $save['id']                     = false;
        $save['cl_lecture_name']        = $this->input->post('tittle');
        $save['cl_lecture_description'] = $this->input->post('description');
        $save['cl_filename']            = $this->input->post('video_name');
        $save['cl_course_id']           = $this->input->post('course_id');
        $save['cl_section_id']          = $this->input->post('sec_id');
        $save['cl_order_no']            = $new_position;
        $save['cl_lecture_type']        = '1';
        $save['cl_conversion_status']   = '2';
        $save['id']                     = $this->Course_model->save_lecture($save);
        
        //setting the new lecture position
        $lectures               = $this->Course_model->lectures(array('direction'=>'ASC' , 'order_by'=>'cl_order_no', 'course_id'=>  $save['cl_course_id'], 'section_id' => $save['cl_section_id']));
        if(!empty($lectures))
        {
            foreach ($lectures as $lecture)
            {
                if($lecture['cl_order_no'] >= $new_position)
                {
                    $save                   = array();
                    $save['id']             = $lecture['id'];
                    $save['cl_order_no']    = ++$new_position;
                    $this->Course_model->save_lecture($save);
                }
            }
        }
        //End
        

    }*/
}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        $this->__role_query_filter = array();
        $redirect               = $this->auth->is_logged_in(false, false);       
        if (!$redirect)
        {
            redirect('login');
        }
        $method = $this->router->fetch_method();
        if(!in_array($method, array('index')))
        {
            $this->load->model(array('User_model', 'Course_model'));
        }
        $this->lang->load('dashboard');
        
    }
    
    
    public function index()
    {
        $data                   = array();
        $data['title']          = lang('dashboard');
        $this->load->view($this->config->item('admin_folder').'/dashboard', $data);
        //$this->load->library(array('archive'));
        //$param                  = array();
        //$param['course_ids']     = array('1','3','6');
        //$param['user_ids']       = array('31800','2858','570','19798');
        //$param['course_id']     = 3;
        //$param['user_id']       = 2858;
        //$this->archive->user_archive_process($param);
    }
            
    function logout()
    {
        $this->auth->logout();
        //when someone logs out, automatically redirect them to the login page.
        $this->session->set_flashdata('message', lang('message_logged_out'));
        redirect($this->config->item('admin_folder').'/login');
    }
    
    function language()
    {
        $response               = array();
        $response['language']   = array();
        $response['language']   = get_instance()->lang->language;
        echo json_encode($response);
    }

    private function event(){
        $this->load->library(array('ofabeeevents'));
        //$this->ofabeeevents->addEvent(array('event_id'=>1,'user_id'=>4,'ename'=>'Test Event','edescription'=>'Event description','edate'=>date('Y-m-d')));

        $this->ofabeeevents->getEvents(array('user_id'=>3));
    }

    function notifications()
    {
        $response               = array();
        $session                = $this->auth->get_current_user_session('admin');
        
        $this->load->library('Notifier');
        
        $response['success']    = true;

        if($session['notification']['count'] > 0)
        {
            $response['message']    = 'Notifications fetch success.';
            $notifications          = $this->notifier->fetch(
                                        array(
                                            'user_id' => $session['id']
                                        )
                                    );
            $response['notifications']          = $notifications['notifications'];
            $session['notification']['count']   = 0;
            $this->session->set_userdata(array('admin' => $session));
            //echo $this->db->last_query();die('last query');
        }
        else
        {
            $response['message']    = 'Notifications read success.';
            $notifications = $this->notifier->read(
                array(
                    'user_id' => $session['id']
                )
            );
            $response['notifications'] = $notifications['notifications'];
        }
        
        echo json_encode($response);
    }

    function read_notification()
    {
        $response               = array();
        $session                = $this->auth->get_current_user_session('admin');
        
        $this->load->library('Notifier');
        
        $response['success']    = true;
        $response['message']    = 'Notifications marking success.';

        $notification_id        = $this->input->post('notification');

        if(!$notification_id)
        {
            $response['success']    = false;
            $response['message']    = 'Notifications marking failure.';
            echo json_encode($response);die;
        }

        $this->notifier->mark_as_read(
            array(
                'user_id' => $session['id'],
                'notification_id' => $notification_id
            )
        );

        echo json_encode($response);
    }

    public function notification_count()
    {
        $response           = array();
        $session            = $this->auth->get_current_user_session('admin');
        $this->load->library('Notifier');
        $notification_count = $this->notifier->get_notifiction_count(array('user_id' => $session['id']));

        $session['notification']['count'] = $notification_count;
        $this->session->set_userdata(array('admin' => $session));

        $response['success']    = true;
        $response['message']    = 'Count fetch success.';
        $response['count']      = $notification_count;
        echo json_encode($response);
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Events extends CI_Controller {

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     *	- or -
     * 		http://example.com/index.php/welcome/index
     *	- or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see https://codeigniter.com/user_guide/general/urls.html
     */
    function __construct()
    {
        parent::__construct();
        $redirect	= $this->auth->is_logged_in_user(false, false);
        if (!$redirect)
        {
                redirect('login');
        }
        $this->load->library(array('ofabeeevents'));
        $this->load->library('session');
    }

    function index(){
        redirect(site_url('dashboard'));
    }

    function event($id = false){
        if(!$id){
            redirect(site_url('dashboard'));
        }

        $event_id           = base64_decode($id);
        $response           = array();
        $event              = array();
        $user               = $this->auth->get_current_user_session('user');
        $event              = $this->ofabeeevents->getEvents(array('id'=>$event_id,'user_id'=>$user['id']));

        $response['success']= true;
        $response['message']= 'Event fetching success.';
        $response['data']   = $event;

        // echo '<pre>';print_r($event);die;
        if(count($event)>0){
            $response['title']  = $event['events']['ev_name'];
            $this->load->view($this->config->item('theme').'/event', $response);
        }else{
            $this->session->set_flashdata('error','The requested event is not available!');
            redirect(site_url('dashboard'));
        }
    }
}

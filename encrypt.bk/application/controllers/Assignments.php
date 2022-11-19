<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Assignments extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        $redirect	= $this->auth->is_logged_in_user(false, false);
        if (!$redirect)
        {
                redirect('login');
        }
        $this->load->model('Course_model');
        $this->lang->load('course');
        $this->load->model('User_model');
        $this->lang->load('dashboard');
        $this->lang->load('homepage');
        $this->load->library('session');
    }

    function index()
    {
        $session = $this->auth->get_current_user_session('user');
        $data = $this->Course_model->get_user_assignments($session['id']);
        $prev_id = 0;
        foreach ($data as $key => $value) {
            if($value['course_id'] != $prev_id) {
                $prev_id = $value['course_id'];
                echo '<h2>'.$value['course_title'].'</h2>';
            }
            echo '<h4><a href="'.site_url('/materials/course/'.$value['course_id'].'#'.$value['lecture_id']).'">'.$value['assignment'].'</a></h4>';
            if($this->validateDate($value['last_date'], 'Y-m-d')){
                $last_date = new DateTime($value['last_date']);
                echo '<span><em>Last Date</em>: '.$last_date->format('jS M Y').'</span>&nbsp;';
            } else {
                echo '<span><em>Last Date</em>: NA</span>&nbsp;';
            }
            if($this->validateDate($value['submit_date'])){
                $submit_date = new DateTime($value['submit_date']);
                echo '<span><em>Submitted Date</em>: '.$submit_date->format('jS M Y').'</span>';
            }
        }
    }

    private function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

}
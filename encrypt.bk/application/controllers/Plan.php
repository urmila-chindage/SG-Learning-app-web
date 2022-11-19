<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Plan extends CI_Controller {

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
        $this->load->model('Plan_model');
    }

    public function index()
    {
        
        $data       = array();
        $user       = $this->auth->get_current_user_session('user');
        $week_range = array('-2', '-1', '0', '1', '2');
        foreach($week_range as $range)
        {
            $study_plan                 = $this->Plan_model->plan(array('user_id' => $user['id'], 'week' => $this->get_week_format(array('range' => $range))));
            $study_plan['sp_lectures']  = json_decode($study_plan['sp_lectures']);
            $data[$this->get_week_format(array('range' => $range))] = $this->Plan_model->plan_report(array('user_id' => $user['id'], 'lecture_ids' => $study_plan['sp_lectures']));        
        }
        echo '<pre>'; print_r($data);
    }
    
    public function assign_week()
    {
        $response       = array();
        $error          = false;
        $message        = '';
        
        $user           = $this->auth->get_current_user_session('user');
        $default_week   = array('this_week' => '0', 'next_week' => '1');
        $week           = $this->input->post('week');//0 or 1 0r any date(2017-06-26)
        $lecture_id     = $this->input->post('lecture_id');
        
        //validation starts here
        if(!$week)
        {
            $error = true;
            $message .= 'Week format missing<br />';
        }
        if(!$lecture_id)
        {
            $error = true;
            $message .= 'Lecture id missing<br />';
        }
        
        if($error)
        {
            $response['error']   = true;
            $response['message'] = $message;
            echo json_encode($response);exit;
        }
        //validation ends here
        
        if( $week && isset($default_week[$week]))// if 0 or 1
        {
            $range = $this->get_week_format(array('range' => $default_week[$week]));
        }
        else
        {
            $range = $this->get_week_format(array('date' => $week));
        }
        $study_plan = $this->Plan_model->plan(array('user_id' => $user['id'], 'week' => $range));
        $lectures   = (array)json_decode($study_plan['sp_lectures']);
        $lectures[] = $lecture_id;
        $lectures   = array_unique($lectures);
        
        $save                   = array();
        $save['sp_user_id']     = $user['id'];
        $save['sp_week_format'] = $range;
        $save['sp_lectures']    = json_encode($lectures);
        $this->Plan_model->save_plan($save);
        $response['error']      = $error;
        $response['message']    = 'Lecture assigned';
        echo json_encode($response);
    }
    
    public function unassign_week()
    {
        $response       = array();
        $error          = false;
        $message        = '';

        $user           = $this->auth->get_current_user_session('user');
        $week           = $this->input->post('week');//25-06-2017<=>01-07-2017
        $lecture_id     = $this->input->post('lecture_id');
        $study_plan     = $this->Plan_model->plan(array('user_id' => $user['id'], 'week' => $week));
        $lectures       = (array)json_decode($study_plan['sp_lectures']);
        
        //validation starts here
        if(!$week)
        {
            $error = true;
            $message .= 'Week format missing<br />';
        }
        if(!$lecture_id)
        {
            $error = true;
            $message .= 'Lecture id missing<br />';
        }
        
        if($error)
        {
            $response['error']   = true;
            $response['message'] = $message;
            echo json_encode($response);exit;
        }
        //validation ends here
        
        if(($lecture_id = array_search($lecture_id, $lectures)) !== false)
        {
            unset($lectures[$lecture_id]);
        }
        
        $save                   = array();
        $save['sp_user_id']     = $user['id'];
        $save['sp_week_format'] = $study_plan['sp_week_format'];
        $save['sp_lectures']    = json_encode($lectures);
        $this->Plan_model->save_plan($save);
        $response['error']      = $error;
        $response['message']    = 'Lecture unassigned';
        echo json_encode($response);
    }
    
    private function get_week_format($param = array())
    {
        $range  = isset($param['range'])?$param['range']:0;
        $date   = isset($param['date'])?$param['date']:date('Y-m-d');
        if($range)
        {
            $week = strtotime($range." week +1 day");        
        }
        else
        {
            $week = strtotime($date);
            $day = date('D', $week);
            switch($day)
            {
                case "Sun":
                    $date = date('Y-m-d', strtotime('+1 day', strtotime($date)));
                    $week = strtotime($date);
                    break;
                case "Sat":
                    $date = date('Y-m-d', strtotime('-1 day', strtotime($date)));
                    $week = strtotime($date);
                    break;
                default:
                    break;
            }
        }

        $start_week     = strtotime("last sunday midnight",$week);
        $end_week       = strtotime("next saturday",$start_week);

        $start_week     = date("d-m-Y",$start_week);
        $end_week       = date("d-m-Y",$end_week);

        return $start_week.'<=>'.$end_week ;
    }

}

<?php
class Advanced_report extends CI_Controller 
{
    
    function __construct()
    {
        parent::__construct();
        $redirect	= $this->auth->is_logged_in(false, false);
        if(!$redirect)
        {
            redirect('login');
        }
        $this->load->model(array('Report_model', 'Location_model'));
        $this->__limit = 100;
    }
    
    function index()
    {
        $data = array();
        $data['report_headers'] = $this->Report_model->report_headers();
        /*$report_headers = $this->Report_model->report_headers();
        if(!empty($report_headers))
        {
            foreach($report_headers as $report_header)
            {
                $data['report_headers'][$report_header['upf_field_id']][] = $report_header['upf_field_value_group'];
            }
        }*/
        $data['limit']              = $this->__limit;
        $data['cities']             = $this->Location_model->cities(array('state_id'=>'27'));//cities from kerala
        $data['subscribed_courses'] = $this->Report_model->subscribed_courses();
        $data['header_labels']      = array();
        $header_labels              = $this->Report_model->report_header_labels();
        if(!empty($header_labels))
        {
            foreach($header_labels as $header_label)
            {
                $data['header_labels'][$header_label['id']] = $header_label['pf_label'];
            }
        }
       // echo '<pre>'; print_r($data);die;
        $this->load->view($this->config->item('admin_folder').'/advanced_report', $data);
    }
    
    function filter_users($json=true)
    {
        //echo '<pre>'; print_r($this->input->post());die;
        $response                   = array();
        $response['show_load_button']   = false;
        $param                      = array();
        $param['keyword']           = $this->input->post('keyword');
        $locations                  = $this->input->post('locations');
        $param['locations']         = json_decode($locations);
        $header_filters             = $this->input->post('header_filters');
        $param['header_filters']    = (array)json_decode($header_filters);
        $course_ids                 = $this->input->post('course_ids');
        $param['course_ids']        = json_decode($course_ids);
        $param['count']             = true;
        
        
        $limit              = $this->__limit;
        $response['limit']  = $limit;
        $offset             = $this->input->post('offset');
        $page               = $offset;
        if($page===NULL||$page<=0)
        {
            $page           = 1;
        }
        $page               = ($page - 1)* $limit;

        //testing values
        /*$param['keyword']               = '';
        $param['location']              = '4';
        $param['header_filters']['19']  = array('trivandrum');
        $param['header_filters']['24']  = array('ABC college of Engineering', 'National college of Engineering');
        $param['course_ids']            = array('27');*/
        //end
        
        $total_reports              = $this->Report_model->users($param);
        $response['total_reports']  = $total_reports;       
        unset($param['count']);
        $param['limit']             = $this->__limit;
        $param['offset']            = $page;

        if($total_reports > ($this->__limit*$offset))
        {
            $response['show_load_button']  = true;
        }

        $filtered_object            = $this->Report_model->users($param);
        $response['users']          = array(); 
        if(!empty($filtered_object))
        {
            foreach($filtered_object as $users)
            {
                $users['fields']                 = array();
                $fields             = $users['us_profile_fields'];
                if($fields)
                {
                    $fields = explode('{#}', $fields);
                }
                $temp_fields = array();
                if(!empty($fields))
                {
                   foreach($fields as $field)
                   {
                       $field = substr($field, 2);
                       $field = substr($field, 0, -2);
                       $temp_field = explode('{=>}', $field);
                       $users['fields'][$temp_field[0]] = $temp_field[1];
                   }
                }
                //echo '<pre>'; print_r($users);die;
                $response['users'][$users['id']] = $users;
            }
        }

        //echo '<pre>'; print_r($response);die;
        echo json_encode($response);
    }
    
    function export()
    {
        $data = array();
        $data['users']          = array(); 
        
        //echo '<pre>';print_r($this->input->post());
        
        $data['cities']             = $this->Location_model->cities(array('state_id'=>'27'));//cities from kerala
        $data['subscribed_courses'] = $this->Report_model->subscribed_courses();
        $data['header_labels']      = array();
        $header_labels              = $this->Report_model->report_header_labels();
        if(!empty($header_labels))
        {
            foreach($header_labels as $header_label)
            {
                $data['header_labels'][$header_label['id']] = $header_label['pf_label'];
            }
        }

        $param                      = array();
        $param['keyword']           = $this->input->post('keyword');
        $param['locations']         = $this->input->post('user_region');
        $param['header_filters']    = $this->input->post('header_filters');
        $param['course_ids']        = $this->input->post('user_courses');
        
        $filtered_object            = $this->Report_model->users($param);
        //echo '<pre>';print_r($param);
        if(!empty($filtered_object))
        {
            foreach($filtered_object as $users)
            {
                $users['fields']                 = array();
                $fields             = $users['us_profile_fields'];
                if($fields)
                {
                    $fields = explode('{#}', $fields);
                }
                $temp_fields = array();
                if(!empty($fields))
                {
                   foreach($fields as $field)
                   {
                       $field = substr($field, 2);
                       $field = substr($field, 0, -2);
                       $temp_field = explode('{=>}', $field);
                       $users['fields'][$temp_field[0]] = $temp_field[1];
                   }
                }
                $data['users'][$users['id']] = $users;
            }
        }
        //echo '<pre>'; print_r($data);die;
        $this->load->view($this->config->item('admin_folder').'/advanced_report_export', $data);
    }
}
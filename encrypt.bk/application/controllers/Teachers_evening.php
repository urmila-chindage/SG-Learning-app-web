<?php

class Teachers extends CI_Controller {

    function __construct() 
    {
        parent::__construct();
        $this->load->model(array('Faculty_model', 'User_model', 'Course_model', 'Category_model'));
        $this->lang->load('teachers');
        $this->load->model('Development_model');
    }

    function index($keyword = false) 
    {
        $data               = array();
        $data['teachers']   = array();
        $data['languages']  = array();
        $data['title']      = lang('teachers');
        $keyword            = urldecode($keyword);
        $data['keyword']    = $keyword;
        
        $data['categories'] = $this->Category_model->categories(array('direction' => 'DESC'));
        $languages          = $this->Course_model->languages(array('restrict_by_tutor_course' => true));
        if (!empty($languages)) {
            foreach ($languages as $language) {
                $data['languages'][$language['id']] = $language;
            }
        }
        
        
        
        $offset_recieved    = 0;
        $param              = array();
        $param['keyword']   = $keyword;
        
        //calculating page numbers
        $per_page           = 15;
        $data['per_page']   = $per_page;
        $page_num           = 1;
        $offset             = 0;
        $param['offset'] = $offset;
        $param['limit']  = $per_page;
        //end of calucalting page number
        
        
        $teachers   = $this->Development_model->teachers($param);
        if (!empty($teachers)) {
            foreach ($teachers as $teacher) {
                $teacher['courses'] = $this->Faculty_model->course_tutors(array('tutor_id' => $teacher['id']));
                $data['teachers'][$teacher['id']] = $teacher;
            }
        }
        
        
        //echo '<pre>';print_r($data);die;
        $this->load->view($this->config->item('theme') . '/teachers', $data);
    }
    
    function teachers_json()
    {
        $category_filters   = (array)json_decode($this->input->post('category_filters'));
        $language_filters   = (array)json_decode($this->input->post('language_filters'));
        $location_filters   = (array)json_decode($this->input->post('location_filters'));
        $rating_filters     = (array)json_decode($this->input->post('rating_filters'));
        $keyword            = $this->input->post('keyword');
        $offset_recieved    = $this->input->post('offset');
        
        $response               = array();  
        $response['teachers']   = array();
        $response['error']      = false;
        $param                  = array();
        $param['keyword']       = $keyword;
        
        //calculating page numbers
        $per_page           = 15;
        $page_num           = 1;
        $offset             = 0;
        if(  $offset_recieved && $offset_recieved != 1 )
        {
            $page_num     = $offset_recieved;
            $offset       = $offset_recieved * $per_page;
            $offset       = ($offset - $per_page);
        }
        $param['offset'] = $offset;
        $param['limit']  = $per_page;
        //end of calucalting page number
        
        if(!empty($category_filters))
        {
            $param['category_ids'] = $category_filters;
        }
        if(!empty($language_filters))
        {
            $param['language_ids'] = $language_filters;
        }
        if(!empty($location_filters))
        {
            $this->load->model('Location_model');
            $param['locations'] = array();
            foreach ($location_filters as $key => $city_name)
            {
               $location = $this->Location_model->city(array('city_name'=>$city_name));
               if(!empty($location))
               {               
                   $param['locations'][$location['id']] = $location['id'];
               }
            }
        }
        if(!empty($rating_filters))
        {
            $param['ratings'] = $rating_filters;
        }
        $teachers   = $this->Development_model->teachers($param);
        if (!empty($teachers)) {
            foreach ($teachers as $teacher) {
                $teacher['courses'] = $this->Faculty_model->course_tutors(array('tutor_id' => $teacher['id']));
                $response['teachers'][$teacher['id']] = $teacher;
            }
        }
        //echo '<pre>';print_r($response['teachers']);;
        echo json_encode($response);
    }
    
    function send_enquiry_mail()
    {
        $tutor_id  = $this->input->post('tutor_id');
        $user_name = $this->input->post('user_name');
        $message   = $this->input->post('user_message');
        $tutor     = $this->Faculty_model->faculty(array('id' => $tutor_id));
        if($tutor)
        {
            $param                 = array();
            $param['from']         = $this->config->item('site_email');
            //$param['to']           = array('thanveer.a@enfintechnologies.com');
            $param['to']           = array($tutor['us_email']);
            $param['subject']      = "Enquiry mail from ".$user_name;
            $param['body']         = "Hi ".$tutor['us_name'].", <br/>A student named <b>".$user_name."</b> has send you an enquiry message. The message is as follows <br />".$message;
            $send = $this->ofabeemailer->send_mail($param); 
        }
        echo 1;
    }
    
    function cities()
    {
        $this->load->model('Location_model');
        $data 	 	= array();
        $keyword 	= $this->input->post('city_name');
        $cities		= $this->Location_model->cities(array('city_name'=>$keyword));
        $data['tags'] 	= array();
        if( sizeof($cities))
        {
            foreach( $cities as $city)
            {
                $data['tags'][]   = $city;
            }
        }
        echo json_encode($data);

    }
    
    function language()
    {
        $response = array();
        $response['language'] = array();
        $response['language'] = get_instance()->lang->language;
        echo json_encode($response);
    }


    /*
    * To view the profile details of teachers 
    * Created by : Neethu KP
    * Created at : 09/01/2017
    */
    function view($id=false)
    {
        $faculty = $this->Faculty_model->faculty(array('id'=>$id, 'rating' => true , 'role_id' => 3));
        if(empty($faculty)){
            header("Location:".base_url().'index.php/error_404/index');
        }
        $data                   = array();
        $faculty['courses']     = $this->Faculty_model->course_details(array('tutor_id' => $faculty['id']));
        
        //processing faculty languages
        $language_speaks_ids    = explode(',', $faculty['us_language_speaks']);
        $language_names         = array();
        if(!empty($language_speaks_ids))
        {
            foreach($language_speaks_ids as $language_id)
            {
                $lang_object = $this->Course_model->course_language(array('language_id'=> $language_id));
                if($lang_object)
                {
                    $language_names[] = $lang_object['cl_lang_name'];
                }
            }
        }
        if(!empty($language_names))
        {
            $faculty['us_language_speaks'] = implode(',', $language_names);
        }
        //End
        //processing faculty location 
        $this->load->model(array('Location_model'));
        $data['faculty_city']   = $this->Location_model->city(array('id' => $faculty['us_native']));
        if($data['faculty_city']){
            $data['faculty_state']   = $this->Location_model->state(array('id' => $data['faculty_city']['state_id']));
        }

        $data['faculty']        = $faculty;
        $language_error         = '';
        $language_ids           = array();

        if(!empty($faculty['courses'])){
            $result = $this->Faculty_model->get_all_reviews(array('courseIds' => array_column($faculty['courses'], 'ct_course_id') ,'tutor_id' => $faculty['id']) );
            $data['reviews']       = $result['reviews'];
            $data['reviews_count'] = $result['review_count'];
            $data['rate_rowcount'] = $result['rate_rowcount'];
            for($i= 1; $i <= 5 ;$i++){
                $rate_count[$i]     = $this->Course_model->get_rating_count(array('course_id' => array_column($faculty['courses'], 'ct_course_id') , 'count_review' => $i ));
            }

            $data['rate_count'] = $rate_count;
            
            if(sizeof($faculty['courses']) == 1){
                    $students_num = $this->Faculty_model->get_students_count(array('courseIds' => array_column($faculty['courses'], 'ct_course_id')) );
                    $data['students_count'] = $students_num->student_count;

            }

        }     
        //echo '<pre>';print_r($data);exit;
        $this->load->view($this->config->item('theme').'/teachers_profile', $data);
       
    }
}

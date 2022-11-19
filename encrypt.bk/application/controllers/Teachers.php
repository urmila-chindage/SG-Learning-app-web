<?php

class Teachers extends CI_Controller {

    function __construct() 
    {
        parent::__construct();
        $this->load->model(array('Faculty_model', 'User_model', 'Course_model', 'Category_model'));
        $this->lang->load('teachers');
        $this->load->model('Development_model');
        
        $redirect               = $this->auth->is_logged_in(false, false, 'user');
        $this->__loggedInUser   = $this->auth->get_current_user_session('user');
        $this->__restrcited_method = array('groups');
    }

    function index($keyword = false) 
    {
        $data               = array();
        $data['teachers']   = array();
        $data['languages']  = array();
        $data['title']      = lang('teachers');
        $keyword            = urldecode($keyword);
        $data['keyword']    = $keyword;
        $data['UserId']     = $this->__loggedInUser['id'];
        
        $data['categories'] = $this->Category_model->tutor_categories(array('direction' => 'DESC'));
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
        $per_page           = 20;
        $data['per_page']   = $per_page;
        $page_num           = 1;
        $offset             = 0;
        $param['offset'] = $offset;
        $param['limit']  = $per_page;
        //end of calucalting page number
        
        $data['total_teachers'] = $this->Development_model->teachers(array('keyword' => $keyword, 'count' => true));
        $teachers   = $this->Development_model->teachers($param);
        if (!empty($teachers)) {
            foreach ($teachers as $teacher) {
                $teacher['courses']   = $this->Faculty_model->course_tutors(array('tutor_id' => $teacher['id']));
                $teacher['expertise'] = $this->Faculty_model->expertises(array('ids' => explode(',', $teacher['us_expertise'])));
                $data['teachers'][] = $teacher;
            }
        }
        
        //getting teacher location
        $data['teacher_locations'] = $this->Faculty_model->teacher_locations(true);
        //End
        
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
        $per_page           = 20;
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
            /*$this->load->model('Location_model');
            $param['locations'] = array();
            foreach ($location_filters as $key => $city_name)
            {
               $location = $this->Location_model->city(array('city_name'=>$city_name));
               if(!empty($location))
               {               
                   $param['locations'][$location['id']] = $location['id'];
               }
            }*/
            $param['locations'] = array();
            foreach ($location_filters as $city_id)
            {
                $param['locations'][$city_id] = $city_id;
            }
        }
        if(!empty($rating_filters))
        {
            $param['ratings'] = $rating_filters;
        }
        
        $count_param            = $param;
        $count_param['count']   = true;
        unset($count_param['offset']);
        unset($count_param['limit']);
        $response['total_teachers'] = $this->Development_model->teachers($count_param);
        
        $teachers   = $this->Development_model->teachers($param);
        if (!empty($teachers)) {
            foreach ($teachers as $teacher) {
                $teacher['courses'] = $this->Faculty_model->course_tutors(array('tutor_id' => $teacher['id']));
                $teacher['expertise'] = $this->Faculty_model->expertises(array('ids' => explode(',', $teacher['us_expertise'])));
                $response['teachers'][] = $teacher;
            }
        }
        //echo '<pre>';print_r($response['teachers']);;
        echo json_encode($response);
    }
    
    function become_teacher()
    {
        $this->load->view($this->config->item('theme') . '/become_teacher');
    }
    function tutor_registration()
    {
        $this->load->view($this->config->item('theme') . '/tutor_registration');
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
        if(!$faculty)
        {
            $this->load->view($this->config->item('theme').'/404_error');
        }
        else
        {
            $data                   = array();
            $data['UserId']         = $this->__loggedInUser['id'];
            $faculty_courses        = $this->Faculty_model->course_details(array('tutor_id' => $faculty['id']));
            $faculty['courses']     = array();
            if(!empty($faculty_courses))
            {
                foreach($faculty_courses as $f_courses)
                {
                    $faculty['courses'][] = $f_courses['course_id'];
                }
            }
            $faculty['us_youtube_url'] = json_decode($faculty['us_youtube_url']);
            
            //echo '<pre>'; print_r($faculty['courses']);die;
            //processing faculty languages
            $this->memcache->delete('course_languages'); 
            $objects            = array();
            $objects['key']     = 'course_languages';
            $callback           = 'course_languages';
            $params             = array();
            $course_languages   = $this->memcache->get($objects, $callback, $params); 
            
            $language_speaks_ids    = explode(',', $faculty['us_language_speaks']);

            $language_names         = array();
            if(!empty($language_speaks_ids))
            {
                foreach($language_speaks_ids as $language_id)
                {
                    $language_names[] = isset($course_languages[$language_id]['cl_lang_name'])?$course_languages[$language_id]['cl_lang_name']:'';
                }
            }
            if(!empty($language_names))
            {
                $faculty['us_language_speaks'] = implode(', ', $language_names);
            }
            //End
            $faculty['expertise'] = $this->Faculty_model->expertises(array('ids' => explode(',', $faculty['us_expertise'])));
        
            //processing faculty location 
            $this->load->model(array('Location_model'));
            $data['faculty_city']     = array();
            $data['faculty_state']   = array();
            $data['faculty_city']['city_name']     = '';
            $data['faculty_state']['state_name']   = '';
            if($faculty['us_native'])
            {
                $data['faculty_city']   = $this->Location_model->city(array('id' => $faculty['us_native']));
                if($data['faculty_city'])
                {
                    $data['faculty_state']   = $this->Location_model->state(array('id' => $data['faculty_city']['state_id']));
                }            
            }
            $data['faculty']        = $faculty;
            //echo '<pre>';print_r($data);exit;
            $this->load->view($this->config->item('theme').'/teachers_profile', $data);
        }
        
       
    }

    function send_message()
    {
        $subject        = $this->input->post('message_subject');
        $message        = base64_decode($this->input->post('message_body'));
        $faculty_id     = $this->input->post('faculty_id');

        $message_param                      = array();
        $message_param['id']                = $faculty_id;
        $message_param['select']            = 'users.id as user_id, users.us_email';
        $message_param['us_email_verified'] = '1';

        $users            = $this->Faculty_model->faculty($message_param);

        //echo "<pre>"; print_r($users); die();
        if(!empty($users))
        {

            $system_message     = array();
            $random_message_id  = rand(1000, 9999);
            $date_time          = date(DateTime::ISO8601);
            $system_message[] = array(
                "messageId" => $random_message_id,
                "senderId" => $this->__loggedInUser['id'],
                "senderName" => $this->__loggedInUser['us_name'],
                "senderImage" => user_path().$this->__loggedInUser['us_image'],
                "receiverId" => $users['user_id'],
                "message" => $message,
                "datetime" => $date_time
            );
            $user_email_id = $users['us_email'];


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
            
            $param                                  = array();
            $param['subject'] 	                    = $subject;
            $param['body'] 		                    = $message;
            $param['to']                            = $user_email_id;
            $param['force_recepient']         = true;
            $send = $this->ofabeemailer->send_mail($param);
            if($send)
            {
                $response['success']=true;
                $response['message']='Message sent successfully';
            }
            else
            {
                $response['success']=false;
                $response['message']='Message failed to sent';
            }
        }
        echo json_encode($response);
    }
}

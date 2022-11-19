<?php
class Tutor extends CI_Controller
{
    public $__badges = array('0'=>'None', '1' => 'Show Badge');
    function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Kolkata');
        $this->actions = $this->config->item('actions');
        $this->load->model(array('Faculty_model', 'User_model', 'Course_model'));
        $this->lang->load('faculty');
        $this->__super_admin = $this->Faculty_model->super_admin();
    }
    
    function index()
    {
    }
    
    function view($id=false)
    {
        $faculty = $this->Faculty_model->faculty(array('id'=>$id, 'rating' => true));
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
            for($i= 1; $i <= 5 ;$i++){
                $rate_count[$i]     = $this->Course_model->get_rating_count(array('course_id' => array_column($faculty['courses'], 'ct_course_id') , 'count_review' => $i ));
            }

            $data['rate_count'] = $rate_count;

        }     
        echo '<pre>';print_r($data);exit;
        $this->load->view($this->config->item('theme').'/teachers_profile', $data);
       
    }

    function dynamic(){
        $this->load->view($this->config->item('theme').'/dynamic-page');
    }
    
   
    
 
  
    
    
}
?>
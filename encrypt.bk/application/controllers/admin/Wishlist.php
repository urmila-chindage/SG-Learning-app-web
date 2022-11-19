<?php
class Wishlist extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Kolkata');
        $this->__role_query_filter = array();
        $redirect               = $this->auth->is_logged_in(false, false);
        $this->__admin_index    = 'admin';
        $this->__loggedInUser   = $this->auth->get_current_user_session('admin');
        if (!$redirect)
        {
            $redirect   = true;
            $teacher    = $this->auth->is_logged_in(false, false, 'teacher');
            if($teacher)
            {
                $redirect = false;
                $this->__admin_index = 'teacher'; 
                $teacher = $this->auth->get_current_user_session('teacher');
                $this->__role_query_filter['teacher_id'] = $teacher['id'];              
                $this->__loggedInUser   = $this->auth->get_current_user_session('teacher');
            }
            if($redirect)
            {
                redirect('login');
            }

        }
        if($this->__admin_index != 'admin'){
            redirect(admin_url());
        }
        
        $this->actions = $this->config->item('actions');
        $this->load->model(array('Wishlist_model', 'User_model'));
        $this->lang->load('groups');
    }
    
    function index($keyword = false)
    {

        $data                       = array();
        $breadcrumb                 = array();
        $breadcrumb[]               = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]               = array( 'label' => 'Report', 'link' => admin_url('report/course'), 'active' => '', 'icon' => '' );
        $breadcrumb[]               = array( 'label' => 'Wishlist Report', 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']         = $breadcrumb;
        $data['title']              = 'Wishlist Management';
        $data['keyword']              = $keyword;
        
        $wish_course_param = array();

        $wish_course_param['direction'] = 'ASC';
        $wish_course_param['active'] = '1';
        $wish_course_param['deleted'] = '0';

        $wishlist_courses = $this->Wishlist_model->get_courses($wish_course_param);
        $new_wishlist_courses = array();
        //echo '<pre>';print_r($wishlist_courses);die;

        if(!empty($wishlist_courses))
        {
            foreach ($wishlist_courses as $key => $courses)
            {
                $users = $this->Wishlist_model->get_users(array('course_id'=>$courses['id'],'deleted'=>'0','status'=>'1'));
                if(count($users)>0){
                    $new_wishlist_courses[$courses['id']] = $courses;
                $new_wishlist_courses[$courses['id']]['users'] = $users;
                }
            }
        }
        $data['wishlist'] = $new_wishlist_courses;
        //echo '<pre>'; print_r($wishlist_courses);die;
        $this->load->view($this->config->item('admin_folder').'/wishlist', $data);
        
    }

    function view($keyword = false)
    {
        $this->index($keyword);
    }



    function strength()
    {
		
		$data                       = array();
		$breadcrumb                 = array();
		$breadcrumb[]               = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
		$breadcrumb[]               = array( 'label' => 'Report', 'link' => admin_url('coursebuilder/report'), 'active' => '', 'icon' => '' );
		$breadcrumb[]               = array( 'label' => 'Wishlist Report', 'link' => '', 'active' => 'active', 'icon' => '' );
		$data['breadcrumb']         = $breadcrumb;
		$this->load->view($this->config->item('admin_folder').'/strength_list', $data);
	}


    function assignment()
    {
		$data                       = array();
		$breadcrumb                 = array();
		$breadcrumb[]               = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
		$breadcrumb[]               = array( 'label' => 'Report', 'link' => admin_url('coursebuilder/report'), 'active' => '', 'icon' => '' );
		$breadcrumb[]               = array( 'label' => 'Wishlist Report', 'link' => '', 'active' => 'active', 'icon' => '' );
		$data['breadcrumb']         = $breadcrumb;
		
		$this->load->view($this->config->item('admin_folder').'/assignment_report', $data);
	}


    function course()
    {
		$data                       = array();
		$breadcrumb                 = array();
		$breadcrumb[]               = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
		$breadcrumb[]               = array( 'label' => 'Report', 'link' => admin_url('coursebuilder/report'), 'active' => '', 'icon' => '' );
		$breadcrumb[]               = array( 'label' => 'Wishlist Report', 'link' => '', 'active' => 'active', 'icon' => '' );
		$data['breadcrumb']         = $breadcrumb;
		
		$this->load->view($this->config->item('admin_folder').'/course_report', $data);
	}



    function language()
    {
        $response = array();
        $response['language'] = array();
        $response['language'] = get_instance()->lang->language;
        echo json_encode($response);
    }

    function export($key = false){
        
        $data = array();
        $wish_course_param = array();
        $new_wishlist_courses = array();

        if($key){
            $decoded = base64_decode($key);
            $attributes = explode('#',$decoded);
            if($attributes[1] == 0){
                $wish_course_param['direction'] = 'ASC';
                $wish_course_param['active'] = '1';
                $wish_course_param['deleted'] = '0';
                if($attributes[2]!=''){
                    $wish_course_param['keyword'] = $attributes[2];
                }
                $wishlist_courses = $this->Wishlist_model->get_courses($wish_course_param);
                //echo '<pre>';print_r($wishlist_courses);die;

                if(!empty($wishlist_courses))
                {
                    foreach ($wishlist_courses as $key => $courses)
                    {
                        $category = $this->Wishlist_model->get_category(array('category_id'=>$courses['cb_category']));
                        $users = $this->Wishlist_model->get_users(array('course_id'=>$courses['id'],'deleted'=>'0','status'=>'1'));
                        if(count($users)>0){
                            $new_wishlist_courses[$courses['id']] = $courses;
                            $new_wishlist_courses[$courses['id']]['users'] = $users;
                            $new_wishlist_courses[$courses['id']]['category_name'] = $category['ct_name'];
                        }
                    }
                }
                $data['wishlist'] = $new_wishlist_courses;
                //echo '<pre>';print_r($new_wishlist_courses);die;
            }else{
                $wish_course_param['direction'] = 'ASC';
                $wish_course_param['active'] = '1';
                $wish_course_param['deleted'] = '0';
                $wish_course_param['id'] = $attributes[1];
                $wishlist_courses = $this->Wishlist_model->get_courses($wish_course_param);
                //echo '<pre>';print_r($wishlist_courses);die;

                if(!empty($wishlist_courses))
                {
                    foreach ($wishlist_courses as $key => $courses)
                    {
                        $users = $this->Wishlist_model->get_users(array('course_id'=>$courses['id'],'deleted'=>'0','status'=>'1'));
                        $category = $this->Wishlist_model->get_category(array('category_id'=>$courses['cb_category']));
                        if(count($users)>0){
                            $new_wishlist_courses[$courses['id']] = $courses;
                            $new_wishlist_courses[$courses['id']]['users'] = $users;
                            $new_wishlist_courses[$courses['id']]['category_name'] = $category['ct_name'];
                        }
                    }
                }
                $data['wishlist'] = $new_wishlist_courses;
                //echo '<pre>';print_r($new_wishlist_courses);die;
            }
            $this->load->view($this->config->item('admin_folder').'/export_wishlist', $data);
        }else{
            redirect(admin_url());
        }
    }

    function topic(){

        $course_id  = 2;
        $user_id    = 3;
        $response   = array();

        if($user_id == 0){
            $response['success']    = 0;
            $response['message']    = 'Invalid user.';
            echo json_encode($response);exit;
        }

        $category_wise = $this->User_model->topic_wise_progress(array('course_id'=>$course_id,'user_id'=>$user_id));

        $response['success']        = 1;
        $response['topic_progress'] = $category_wise;

        echo json_encode($response);

    }

    public function test(){

        $user_id    = 4;   
        $category_wise = $this->User_model->get_course_assessments(array('user_id'=>$user_id));

        echo '<pre>'; print_r($category_wise);
    }

    public function calendar($param = array('date'=>'2017-07-21')){
        $range                          = array();
        $date_input                     = $param['date'];
        $range['from']                  = date("Y-m-01", strtotime($date_input));
        $range['to']                    = date("Y-m-t", strtotime($date_input));

        $lectures['assignments']       = $this->User_model->get_assignment_datewise(array('user_id'=>4,'from'=>$range['from'],'to'=>$range['to']));
        $lectures['live']              = $this->User_model->get_live_datewise(array('user_id'=>4,'from'=>$range['from'],'to'=>$range['to']));
        
        echo '<pre>';
        echo '======================before sort===============';
        print_r($lectures);

        $events = array();
        $sorted_array = array();
        foreach ($lectures['assignments'] as $assignment)
        {
            $events[]                        = $assignment;
            $assignment['type']              = 'assignment';
            $sorted_array[$assignment['id']] = $assignment['dt_last_date'];
        }
        foreach ($lectures['live'] as $live)
        {
            $events[]                   = $live;
            $live['type']               = 'live';
            $sorted_array[$live['id']]  = $live['ll_date'];
        }
        print_r($sorted_array);
        print_r($events);

        array_multisort($sorted_array, SORT_ASC, $events);
        
        echo '======================after sort===============';
        print_r($events);


    }


    /* --- Test purpose by Alex --- */

    function export_course_report($course_id=0)
    {

        $this->load->model(array('Course_model', 'Report_model'));
        $course_id  = ($course_id)?$course_id: $this->input->post('course_id');
        $user_name  = $this->input->post('user_name');
        $course     = array();
        if($course_id)
        {
            $course     = $this->Course_model->course(array('id' => $course_id));        
        }
        //echo '<pre>'; print_r($course);die;
        $response   = array();
        $response['subscribers']        = array();
        $response['lectures']           = array();
        $response['limit']              = 10;
        $response['course_id']          = $course_id;
        $response['selected_course']    = '';

        $course_param                   = $this->__role_query_filter;
        $course_param['direction']      = 'id'; 
        $course_param['not_deleted']    = '1';
        $course_param['select']         = 'course_basics.id, course_basics.cb_title';

        if(!empty($course))
        {
            $response['selected_course']    = $course['cb_title'];
            $response['lectures']    = $this->Course_model->lectures(array('course_id' => $course_id, 'not_deleted' => true, 'select' => 'course_lectures.id, course_lectures.cl_lecture_name'));
            $subscribers             = $this->Report_model->enrolled_report(array( 'limit' => $response['limit'], 'offset' => 1, 'course_id' => $course_id, 'keyword' => $user_name));

            if(!empty($subscribers))
            {
                foreach ($subscribers as $subscriber)
                {
                    $subscriber['lectures'] = $this->Report_model->lecture_completed_status(array('course_id' => $subscriber['cs_course_id'], 'user_id' => $subscriber['cs_user_id']));
                    $response['subscribers'][] = $subscriber; 
                }
            }
            //echo '<pre>'; print_r($response);die;
        }
        $this->load->view($this->config->item('admin_folder').'/export_course_report', $response);
    }

    /* --- End of Test purpose by Alex --- */
}
    

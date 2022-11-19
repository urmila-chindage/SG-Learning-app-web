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
        $breadcrumb[]               = array( 'label' => 'Report', 'link' => admin_url('coursebuilder/report'), 'active' => '', 'icon' => '' );
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

}
    

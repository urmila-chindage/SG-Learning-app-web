<?php
class Category extends CI_Controller {
    function __construct()
    {
        parent::__construct();
        
        $this->load->model(array('Category_model', 'Course_model','Page_model','User_model'));
        $this->lang->load('category');
    }
    
    function view_old($id)
    {
        $data = array();
        $session    = $this->auth->get_current_user_session('user');
        $data['session'] = $session;

        
        $category   = $this->Category_model->category(array('id'=>$id));
        $data['category_content']   = $category;
        $data['category_pages']     = $this->Page_model->pages(array('category'=>$id, 'direction'=>'DESC', 'status'=>'1'));
        $data['category_course']    = $this->Course_model->courses(array('status'=> '1', 'category_id'=> $id, 'not_deleted'=> '1', 'order_by' => 'cb_position', 'direction' => 'ASC'));
        $data['category_id']        = $id;
        
        

        $_SESSION['category']['category_id']      = $id;
        $_SESSION['category']['category_slug']    = $category['ct_slug'];
        $_SESSION['category']['category_name']    = $category['ct_name'];

        $data['question_category']  = $this->Category_model->get_question_category(array('cat_id' => $id));
        
        $data['admin']              = $this->config->item('acct_name');
        $data['admin_name']         = $this->config->item('us_name');
        $data['user_course_enrolled'] = $this->User_model->enrolled_course(array('user_id' => $session['id'])); 
        $ratting                    = array();
        
        foreach ($data['category_course'] as $key => $course) {
            $data['category_course'][$key]['assigned_tutors'] = $this->Course_model->assigned_tutors(array('course_id'=>$course['id']));
            $data['category_course'][$key]['ratting'] = $this->Course_model->get_ratting(array('course_id' => $course['id']));
            $temp_arr = array();
            array_push($temp_arr, 'rate_div_'.$key);
            array_push($temp_arr, $data['category_course'][$key]['ratting']);
            array_push($ratting, $temp_arr);

            $wish_stat = $this->Course_model->get_whishlist_stat($course['id'], $session['id']);

            if($wish_stat == 1){
                $data['category_course'][$key]['wish_stat'] = '<i class="demo-icon icon-heart wish-icon-search" onclick="remove_wishlist('.$course['id'].', \''.$session['id'].'\', this)" rel="tooltip" data-key="'.$course['id'].'" title="Remove From Whishlist" ></i>';
            }
            else if($wish_stat == 0){
                $data['category_course'][$key]['wish_stat'] = '<i class="demo-icon icon-heart-empty wish-icon-search" onclick="add_wishlist('.$course['id'].', \''.$session['id'].'\', this)" data-key="'.$course['id'].'" rel="tooltip" title="Add To Whishlist" ></i>';
            }else if($wish_stat == 2){
                $data['category_course'][$key]['wish_stat'] = '';
            }
        }
        
        $data['rattings'] = json_encode($ratting);
        //echo "<pre>";print_r($data);die;
        $this->load->view($this->config->item('theme').'/category', $data);
    }
    
    function view($id)
    {
        $data = array();
        $session    = $this->auth->get_current_user_session('user');
        $data['session'] = $session;

        
        $category   = $this->Category_model->category(array('id'=>$id));
        $data['category_content']   = $category;
        $data['category_pages']     = $this->Page_model->pages(array('category'=>$id, 'direction'=>'DESC', 'status'=>'1'));
        
        $data['category_id']        = $id;
        $data['title'] = $category['ct_name'];
        

        $_SESSION['category']['category_id']      = $id;
        $_SESSION['category']['category_slug']    = $category['ct_slug'];
        $_SESSION['category']['category_name']    = $category['ct_name'];

        $data['question_category']  = $this->Category_model->get_question_category(array('cat_id' => $id));
        
        $data['admin']              = $this->config->item('acct_name');
        $data['admin_name']         = $this->config->item('us_name');
        $data['user_course_enrolled'] = $this->User_model->enrolled_course(array('user_id' => $session['id'])); 
        
        $offset_recieved    = 0;
        $param              = array();
        
        //calculating page numbers
        $per_page           = 9;
        $data['per_page']   = $per_page;
        $page_num           = 1;
        $offset             = 0;
        $param['offset'] = $offset;
        $param['limit']  = $per_page;
        //end of calucalting page number
        $param['status']    = 1;
        $param['not_deleted']= 1;
        $param['category_id']=$id;
        $param['order_by']  = 'cb_position';
        $param['direction'] = 'ASC';
        
        $data['category_course']    = $this->Course_model->courses($param);
        
        
        $ratting                    = array();
        
        foreach ($data['category_course'] as $key => $course) {
            $data['category_course'][$key]['assigned_tutors'] = $this->Course_model->assigned_tutors(array('course_id'=>$course['id']));
            $data['category_course'][$key]['ratting'] = $this->Course_model->get_ratting(array('course_id' => $course['id']));
            $temp_arr = array();
            array_push($temp_arr, 'rate_div_'.$key);
            array_push($temp_arr, $data['category_course'][$key]['ratting']);
            array_push($ratting, $temp_arr);

            
            $wish_stat = $this->Course_model->get_whish_stat($course['id'], $session['id']);

            if($wish_stat == 1){
                $data['category_course'][$key]['wish_stat'] = '<span class="heart-icon heart-active" data-key="'.$course['id'].'" onclick="remove_wishlist('.$course['id'].', \''.$session['id'].'\', this)"><i class="icon-heart heart-altr"></i></span>';
            }
            else if($wish_stat == 0){
                $data['category_course'][$key]['wish_stat'] = '<span class="heart-icon" data-key="'.$course['id'].'" onclick="add_wishlist('.$course['id'].', \''.$session['id'].'\', this)"><i class="icon-heart heart-altr"></i></span>';
            }
            else if($wish_stat == 2){
                $data['category_course'][$key]['wish_stat'] = '';
            }
        }
        
        $data['rattings'] = json_encode($ratting);
        //echo "<pre>";print_r($data);die;
        $this->load->view($this->config->item('theme').'/category_beta', $data);
    }
    
    function category_courses_json()
    {
        $response = array();
        $session    = $this->auth->get_current_user_session('user');
        $response['session'] = $session;
        $id                 = $this->input->post('category_id');
        $offset_recieved    = $this->input->post('offset');

        
        $category   = $this->Category_model->category(array('id'=>$id));
        $data['category_content']   = $category;
        $data['category_pages']     = $this->Page_model->pages(array('category'=>$id, 'direction'=>'DESC', 'status'=>'1'));
        
        
        

        $_SESSION['category']['category_id']      = $id;
        $_SESSION['category']['category_slug']    = $category['ct_slug'];
        $_SESSION['category']['category_name']    = $category['ct_name'];

        $data['question_category']  = $this->Category_model->get_question_category(array('cat_id' => $id));
        
        $param              = array();
        $response['error']      = false;
        $param                  = array();
        
        
        //calculating page numbers
        $per_page           = 9;
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
        
        $param['status']    = 1;
        $param['not_deleted']= 1;
        $param['category_id']= $id;
        $param['order_by']  = 'cb_position';
        $param['direction'] = 'ASC';
        
        $response['category_course']    = $this->Course_model->courses($param);
        
        
        $ratting                    = array();
        
        foreach ($response['category_course'] as $key => $course) {
            $response['category_course'][$key]['assigned_tutors'] = $this->Course_model->assigned_tutors(array('course_id'=>$course['id']));
            $response['category_course'][$key]['ratting'] = $this->Course_model->get_ratting(array('course_id' => $course['id']));
            $temp_arr = array();
            array_push($temp_arr, 'rate_div_'.$key);
            array_push($temp_arr, $response['category_course'][$key]['ratting']);
            array_push($ratting, $temp_arr);

            
            $wish_stat = $this->Course_model->get_whish_stat($course['id'], $session['id']);

            if($wish_stat == 1){
                $response['category_course'][$key]['wish_stat'] = '<span class="heart-icon heart-active" data-key="'.$course['id'].'" onclick="remove_wishlist('.$course['id'].', \''.$session['id'].'\', this)"><i class="icon-heart heart-altr"></i></span>';
            }
            else if($wish_stat == 0){
                $response['category_course'][$key]['wish_stat'] = '<span class="heart-icon" data-key="'.$course['id'].'" onclick="add_wishlist('.$course['id'].', \''.$session['id'].'\', this)"><i class="icon-heart heart-altr"></i></span>';
            }
            else if($wish_stat == 2){
                $response['category_course'][$key]['wish_stat'] = '';
            }
        }
        echo json_encode($response);
    }
    

}
<?php
class Question_manager extends CI_Controller
{

    function __construct()
    {
        
        parent::__construct();
        $this->__role_query_filter = array();
        $this->__loggedInUser   = $this->auth->get_current_user_session('admin');
        $redirect	= $this->auth->is_logged_in(false, false);
        $this->__admin_index = '';
        $this->limit        = 10;
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
            }
            $content_editor    = $this->auth->is_logged_in(false, false, 'content_editor');
            if($content_editor)
            {
                $redirect = false;
                $this->__admin_index    = 'content_editor';
                $content_editor         = $this->auth->get_current_user_session('content_editor');
                $this->__role_query_filter['editor_id'] = $content_editor['id'];                
            }
            if($redirect)
            {
                redirect('login');
            }
        }
        $this->__question_permission           = $this->accesspermission->get_permission(array(
                'role_id' => $this->__loggedInUser['role_id'],
                'module' => 'question'
                ));
    
        $this->__course_permission             = $this->accesspermission->get_permission(array(
                    'role_id' => $this->__loggedInUser['role_id'],
                    'module' => 'course'
                    ));
        $this->actions        = $this->config->item('actions');
        $this->load->model(array('Settings_model', 'Category_model','Course_model','Generate_test_model'));
        //$this->lang->load('course_settings');
        $this->lang->load('category');
    }

    function index()
    {
        $data                       = array();
        $breadcrumb                 = array();
        $limit                      = $this->limit;
        $offset                     = 0;
        $breadcrumb[]               = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        //$breadcrumb[]               = array( 'label' => 'Question Bank', 'link' => admin_url('generate_test/'), 'active' => '', 'icon' => '' );
        $breadcrumb[]               = array( 'label' => 'Category Manager', 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']         = $breadcrumb;
        $data['title']              = 'Category Manager';
        $data['course_categories']  = $this->Category_model->categories(array('select'=>'categories.id,categories.ct_name, categories.ct_status','not_deleted'=>true, 'order_by' => 'ct_order', 'direction' => 'ASC'));
        $selected_category          = (isset($data['course_categories'][0]['id']))?$data['course_categories'][0]['id']:0;
        $data['selected_category']  = ($selected_category)?$selected_category:0;
        $data['question_subjects']  = $this->Category_model->subjects(array('select'=>'questions_subject.id,questions_subject.qs_subject_name,questions_subject.qs_category_id','category_id'=>$selected_category,'qs_deleted'=>true));
        $selected_subject           = (isset($data['question_subjects'][0]['id']))?$data['question_subjects'][0]['id']:0;
        $data['selected_subject']   = ($selected_subject)?$selected_subject:0;
        $data['question_topics']    = $this->Category_model->topics(array('select'=>'questions_topic.id,questions_topic.qt_topic_name','category_id'=>$selected_category,'subject_id'=>$selected_subject,'qt_deleted'=>true));
        $data['question_manager']   = $this->__question_permission;
        $data['category_manager']   = $this->__course_permission;
        $this->load->view($this->config->item('admin_folder').'/question_manager', $data);
    }

    function edit_category()
    {
        $category_id            = $this->input->post('id');
        $response               = array();
        $response['message']    = '';
        $response['error']      = false;
        
        if($category_id > 0)
        {
            $response['category'] = $this->Category_model->category(array('id' => $category_id));
            $response['category']['ct_name'] = strip_tags($response['category']['ct_name']);
            if(empty($response['category']))
            {
                $response['message'] = 'Requested category not found.';
                $response['error'] = true;
            }
        }
        $this->invalidate_category();
        echo json_encode($response);die;   
    }

    function save_category()
    {
        $response               = array();
        $response['message']    = 'Category saved successfully';
        $response['error']      = false;

        $category_name          = strip_tags($this->input->post('cat_name'));
        $category_id            = $this->input->post('cat_id');

        //validation starts
        $message                = '';
        //echo '<pre>'; print_r($category_id); exit;
        if(trim($category_name) == '')
        {
            if(trim($this->input->post('cat_name')) == '')
            {
                $message       .= 'Category name cannot be empty<br />';
            } 
            else
            {
                $message       .= 'Invalid category name<br />';
            }
            $response['error']  = true;
        }
            $check_category                 = $this->Category_model->category(array('id'=>$category_id,'select'=>'ct_route_id, ct_name', 'not_deleted'=> true));

            $categories                     = $this->Category_model->category(array('category_name'=>ltrim($category_name), 'not_deleted'=> true));
            
            if(isset($categories['ct_name']) && $categories['ct_name'] != $check_category['ct_name'])
            {
                $message              .= $category_name.' is currently in use. Please use another<br />';
                $response['error']     = true;
            }
            
       //print_r($categories['ct_name']); die;
       
        $GLOBALS['category']    = '';
        $category = '';
        if($response['error']   == false)
        {
            
            //echo '<pre>'; print_r($category_id); exit;
            $category                     = array();
            if($check_category){
                
                $this->load->helper('text');
                $slug                     = $category_name;
                $slug                     = url_title(convert_accented_characters($slug), 'dash', TRUE);
                $this->load->model('Routes_model');
                $slug                     = $this->Routes_model->validate_slug($slug);
                $GLOBALS['category']      = $category_id;
                $response['exist']        = '1';
                $category['ct_name']      = $category_name;
                $category['id']           = $category_id;
                $category['ct_slug']      = $slug;
                $category['action_by']    = $this->auth->get_current_admin('id');
                $category['action_id']    = $this->actions['update'];
                $category['updated_date'] = date("Y-m-d H:i:s");
                $this->Category_model->save($category);
                $route_update['id']       = $check_category['ct_route_id'];
                $route_update['slug']     = $slug;
                $route_update['r_item_type']    = 'category';
                $route_update['r_item_id']      = $category_id;
                $this->Routes_model->save($route_update); 

                /*Log creation*/
                $user_data                      = array();
                $user_data['user_id']           = $this->__loggedInUser['id'];
                $user_data['username']          = $this->__loggedInUser['us_name'];
                $user_data['useremail']          = $this->__loggedInUser['us_email'];
                $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
                $message_template               = array();
                $message_template['username']   = $this->__loggedInUser['us_name'];
                $message_template['category']   = $category_name;
                $triggered_activity             = 'category_updated';
                log_activity($triggered_activity, $user_data, $message_template);
                $this->invalidate_category();
            } 
            else 
            {
                $response['message'] = 'Invalid category name.';            
                $response['error']   = true;  
            } 
            
        }
        else
        {
            $response['message'] = $message;             
            $response['error']   = true;             
        } 
        $response['category']    = $category; 
        echo json_encode($response); 

    }

    function save_bulk_category()
    {
        $response               = array();
        $response['message']    = 'Categories saved successfully';
        $response['error']      = false;
        
        $cat_names              = strip_tags($this->input->post('cat_names'));
        //print_r($cat_names); die;
        //validation starts
        $message                = '';
        $category_names         = preg_replace("/((\r?\n)|(\r\n?))/", ',', $cat_names);
        $category_names         = array_unique(explode(',', $category_names));
        if(trim($cat_names) == '')
        {
            if(trim($this->input->post('cat_names')) == '') 
            {
                $message        .= 'Category names cannot be empty<br />';
            }
            else
            {
                $message        .= 'Invalid category names<br />';
            }
            $response['error']   = true;
        }
        else
        {   
            $category_exists           = array();
            foreach($category_names as $category_name){
                $categories            = $this->Category_model->category(array('category_name'=>ltrim($category_name), 'not_deleted'=> true));
                if($categories)
                {
                    $category_exists[] = $categories['ct_name'];
                }
                $categories            = '';
            }
            $exist_category_names      = implode(',', $category_exists);
            if(!empty($category_exists))
            {                
                $message              .= $exist_category_names.' are currently in use. Please use another<br />';
                $response['error']     = true; 
            }
        }
        if($response['error'] == false)
        {
                $this->load->helper('text');
                $this->load->model('Routes_model');
            foreach($category_names as $category_name) {
                $slug                  = $category_name;
                $slug                  = url_title(convert_accented_characters($slug), 'dash', TRUE);
                $slug                  = $this->Routes_model->validate_slug($slug);
                $route['slug']         = $slug;    
                $route_id              = $this->Routes_model->save($route);
                //End
                $category              = array();
                $category['id']        = false;
                if($category_name != '')
                {
                    $category['ct_name']        = $category_name;
                    $category['ct_status']      = '1';
                    $category['ct_account_id']  = $this->config->item('id');
                    $category['action_by']      = $this->auth->get_current_admin('id');
                    $category['action_id']      = $this->actions['create'];
                    $category['ct_route_id']    = $route_id;
                    $category['ct_slug']        = $slug;
                    $category['id']             = $this->Category_model->save($category);
                    $update                     = array();
                    $update['id']               = $category['id'];
                    $update['ct_order']         = $category['id'];
                    $update['id']               = $this->Category_model->save($update);
                    $response['exist']          = '0';
                    $route['id']                = $route_id;
                    $route['slug']              = $slug;
                    $route['route']             = 'category/view/'.$category['id'];
                    $route['r_account_id']      = $this->config->item('id');
                    $route['r_item_type']       = 'category';
                    $route['r_item_id']         = $category['id'];
                    $this->Routes_model->save($route);
                    $response['category'][]     = $category;
                }   
            }
            $category_names_str                 = implode(',', $category_names);
            /*Log creation*/
            $category_count                 = (sizeof($category_names)>1)?'the categories':'a category';
            $user_data                      = array();
            $user_data['user_id']           = $this->__loggedInUser['id'];
            $user_data['username']          = $this->__loggedInUser['us_name'];
            $user_data['useremail']          = $this->__loggedInUser['us_email'];
            $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
            $message_template               = array();
            $message_template['username']   = $this->__loggedInUser['us_name'];
            $message_template['categories'] = $category_count.' '.$category_names_str;
            $triggered_activity             = 'category_created';
            log_activity($triggered_activity, $user_data, $message_template);
        }
        else
        {
            $response['message'] = $message;            
            $response['error']   = true;            
        }
        $this->invalidate_category();
        echo json_encode($response);
        //end
    }
    
    function check_category_connection()
    {
        $this->load->model('category_model');
        $category_id = $this->input->post('cat_id');
        $response                   = array();
        $response['error']          = false;
        $message                    = '';
        $response['message']        = '';
        $response['courses']        = false;
        $response['subjects']       = false;
        $response['questions']      = false;
        $response['status']         = $this->category_model->category(array('select' => 'ct_status', 'id' => $category_id))['ct_status'];
        $check_courses              = $this->Course_model->check_course_assign($category_id);
        
        $count                      = '';
        if($check_courses['cb_title']!='')
        {
            
            $unsign_courses      = explode(',',$check_courses['cb_title']);
            $count               = count($unsign_courses);
            $response['courses']    = $count;
            $count               =($count > 1)?'s':'';
            if($count)
            {
                
                for($i = 0; $i < $count; $i++)
                {
                    $message    .= ($i+1).'. '.$unsign_courses[$i].'<br/>';
                }
            }
            else
            {
                $message            .= $check_courses['cb_title'].'<br/>';  
            }
                      
            $response['error']   = true; 
        }
       
        $check_subjects          = $this->Category_model->subjects(array('qs_deleted'=>true,'category_id'=>$category_id));
        
        
        if(!empty($check_subjects))
        {
            $response['subjects']   = count($check_subjects);
            $message .= ' - Question Subjects<br/>';            
            $response['error']   = true; 
        }

        $check_generate_test_questions = $this->Generate_test_model->questions(array('category_id'=>$category_id, 'not_deleted'=>true));
        
        if(!empty($check_generate_test_questions))
        {
            $response['questions']   = count($check_generate_test_questions);
            $message            .= ' - Questions<br/>';            
            $response['error']   = true; 
        }
        
        if($response['error'] ==  true) {
            $response['message']  = 'Please unassign the following course'.$count.' from this category<br/>';
            $response['message'] .= $message;
        }
        echo json_encode($response);
    }
    
    function delete_category()
    {
        $category_id                    = $this->input->post('cat_id');
        $category_name                  = $this->input->post('category_name');
        $category_delete                = $this->Category_model->delete($category_id);
        /*Log creation*/
        $user_data                      = array();
        $user_data['user_id']           = $this->__loggedInUser['id'];
        $user_data['username']          = $this->__loggedInUser['us_name'];
        $user_data['useremail']          = $this->__loggedInUser['us_email'];
        $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
        $message_template               = array();
        $message_template['username']   = $this->__loggedInUser['us_name'];
        $message_template['category']   = $category_name;
        $triggered_activity             = 'category_deleted';
        log_activity($triggered_activity, $user_data, $message_template);
        $response            = array();
        $response['message'] = 'Category successfully deleted';            
        $response['error']   = false; 
        $this->invalidate_category();
        echo json_encode($response);
    }
    
    function migrate_category()
    {
        $migrate_from                  = $this->input->post('previous_cat_id');
        $migrate_to                    = $this->input->post('cat_id');
        $from_category                 = $this->input->post('from_category');
        $to_category                   = $this->input->post('to_category');
        $response                      = array();
        $response['error']             = false;
        $response['message']           = 'Category migrated successfully';
        
        $course_data                   = array();
        $course_data['cb_category']    = $migrate_to;
        $course_data['updated_date']   = date('Y-m-d H:i:s');
        $course_data['action_by']      = $this->auth->get_current_admin('id');
        $course_data['action_id']      = $this->actions['update'];
        
        $course_migrate                = $this->Settings_model->update_course_category($migrate_from,$course_data);
        $question_data                 = array();
        $question_data['q_category']   = $migrate_to;
        $question_migrate              = $this->Settings_model->update_question_category($migrate_from,$question_data);

        $question_subject_data                   = array();
        $question_subject_data['qs_category_id'] = $migrate_to;
        $subject_migrate                         = $this->Settings_model->update_subject_category($migrate_from,$question_subject_data);

        $question_topic_data                     = array();
        $question_topic_data['qt_category_id']   = $migrate_to;
        $topic_migrate                           = $this->Settings_model->update_topic_category($migrate_from,$question_topic_data);
        
        /*Log creation*/
        $user_data                      = array();
        $user_data['user_id']           = $this->__loggedInUser['id'];
        $user_data['username']          = $this->__loggedInUser['us_name'];
        $user_data['useremail']          = $this->__loggedInUser['us_email'];
        $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
        $message_template               = array();
        $message_template['username']   = $this->__loggedInUser['us_name'];
        $message_template['migrate']    = 'from '.$from_category.' to '.$to_category;
        $triggered_activity             = 'category_migrated';
        log_activity($triggered_activity, $user_data, $message_template);
        
        echo json_encode($response);
    }
    
    function get_category()
    {
        $category_id                 = $this->input->post('cat_id');
        $response                    = array();
        $response['error']           = false;
        $category_listing            = $this->Category_model->categories(array('not_deleted'=>true));
        $response['filter_category'] = $category_listing;
        echo json_encode($response);
    }
    /* Question Subject */

    function edit_question_subject()
    {
        $subject_id             = $this->input->post('id');
        $response               = array();
        $response['message']    = '';
        $response['error']      = false;
        
        if($subject_id > 0)
        {
            $response['subject']                    = $this->Category_model->subject(array('id' => $subject_id));
            $response['subject']['qs_subject_name'] = strip_tags($response['subject']['qs_subject_name']);
            if(empty($response['subject']))
            {
                $response['message']                = 'Requested subject not found.';
                $response['error']                  = true;
            }
        }
        echo json_encode($response);die;   
    }

    function save_question_subject()
    {
        $response               = array();
        $response['message']    = '';
        $response['error']      = false;

        $category               = strip_tags($this->input->post('category'));
        $subject_id             = strip_tags($this->input->post('subject_id'));
        $subject_name           = $this->input->post('subject_name');
        $category_name          = $this->input->post('category_name');

        //validation starts
        $message               = '';
        if($category == 0)
        {
            $message          .= 'Please select course category<br />';
            $response['error'] = true;
        }
        
        if(trim($subject_name) == '')
        {
            if(trim($this->input->post('subject_name')) == ''){
                $message      .= 'Subject name cannot be empty<br />';
            }else{
                $message      .= 'Invalid Subject name<br />';
            }
            $response['error'] = true;
        }
        else
        {  
            if($subject_id==0) {
                $subject_array         = $this->Category_model->subject(array('subject_name' => $subject_name, 'category_id' => $category));
                if($subject_array)
                {                
                    $message          .= 'Subject name is currently in use. Please use another<br />';
                    $response['error'] = true; 
                }
            }
        }
        if($response['error'] == false)
        {
            $excludes                   = true;
            
            $check_subject              = $this->Category_model->subject(array('subject_id' => $subject_id, 'id' => $subject_id));

           //
            $subject                    = array();
            $subject_update             = array();
            $subject['id']              = false;
            if($check_subject)
            {
                $check_exists                      = $this->Category_model->subject(array('subject_name'=>ltrim($subject_name), 'subject_id' => $subject_id, 'category_id'=>$category,'qs_deleted'=> true, 'excludes' => $excludes));
                if(!empty($check_exists)){
                    //print_r($check_subject); echo $this->db->last_query();die;
                    $response['message']    = 'Subject name is currently in use inside the selected category. Please use another<br />';
                    $response['error']      = true;
                }
                else
                {
                    $response['exist']                 = '1';
                    $subject_update['qs_subject_name'] = $subject_name;
                    $subject_update['qs_category_id']  = $category;
                    $subject_update['action_by']       = $this->auth->get_current_admin('id');
                    $subject_update['action_id']       = $this->actions['update'];
                    $subject_update['updated_date']    = date("Y-m-d H:i:s");
                    $subject_update['id']              = $subject_id;
                    $this->Category_model->save_subject($subject_update);
                    $response['message']               = 'Subject saved successfully';
                    $response['error']                 = false;
                    /*Log creation*/
                    $user_data                      = array();
                    $user_data['user_id']           = $this->__loggedInUser['id'];
                    $user_data['username']          = $this->__loggedInUser['us_name'];
                    $user_data['useremail']          = $this->__loggedInUser['us_email'];
                    $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
                    $message_template               = array();
                    $message_template['username']   = $this->__loggedInUser['us_name'];
                    $message_template['subject']    = $subject_name;
                    $message_template['category']   = $category_name;
                    $triggered_activity             = 'sub_category_updated';
                    log_activity($triggered_activity, $user_data, $message_template);
                }
            }
            else 
            {
                $response['message'] = 'Invalid subject.';            
                $response['error']   = true;  
            }
        }
        else
        {
            $response['message'] = $message;            
            $response['error']   = true;            
        }
        $response['ques_subject']= $subject_update;
        echo json_encode($response);
    }

    function check_subject_connection()
    {
        $subject_id             = $this->input->post('subject_id');
        $response               = array();
        $categoryId             = '';
        $questionId             = '';
        $link                   = '?filter=all&type=all&offset=1&';
        $response['error']      = false;
        $message                = '';
        $response['message']    = '';
        $check_topics           = $this->Category_model->topics(array('qt_deleted'=>true,'subject_id'=>$subject_id));

        //print_r($check_topics);//generate_test?category=37&subject=148&topic=76
        if(!empty($check_topics))
        {   
            $categoryId          = $check_topics[0]['qt_category_id'];
            $link               .= 'category='.$categoryId.'&subject='.$subject_id;
            $message            .= ' - Question Topics <br/>';/*<a target="_blank" href="'.site_url('admin/generate_test/'.$link).'">click here to see assigned Topics</a>*/
            $response['error']   = true; 
        }
        $check_generate_test_questions = $this->Generate_test_model->questions(array('subject_id'=>$subject_id,'not_deleted'=>true));
        //print_r($check_generate_test_questions); filter=all&type=all&category=all&subject=all&topic=all&offset=1
        if(!empty($check_generate_test_questions))
        {
            $questionId          = $check_generate_test_questions[0]['id'];
            $link               .= 'topic='.$questionId;
            $message            .= ' - Questions <a target="_blank" href="'.site_url('admin/generate_test/'.$link).'">click here to see assigned Questions</a><br/>';            
            $response['error']   = true; 
        }
        if($response['error']    ==  true){
            $response['message']  = 'Please unassign the following from this subject<br/>';
            $response['message'] .= $message;
        }
        echo json_encode($response);
    }

    function delete_question_subject()
    {
        $subject_id                 = $this->input->post('subject_id');
        $subject_name               = $this->input->post('subject_name');
        $category_name              = $this->input->post('category_name');
        $data                       = array();
        $data['qs_deleted']         = '1';
        $subject_delete             = $this->Generate_test_model->delete_subject($subject_id, $data);

        /*Log creation*/
        $user_data                      = array();
        $user_data['user_id']           = $this->__loggedInUser['id'];
        $user_data['username']          = $this->__loggedInUser['us_name'];
        $user_data['useremail']          = $this->__loggedInUser['us_email'];
        $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
        $message_template               = array();
        $message_template['username']   = $this->__loggedInUser['us_name'];
        $message_template['subject']    = $subject_name;
        $message_template['category']    = $category_name;
        $triggered_activity             = 'sub_category_deleted';
        log_activity($triggered_activity, $user_data, $message_template);

        $response['message']        = 'Subject successfully deleted';      
        $response['error']          = false;       
        $response['subject_id']     = $subject_id; 
        echo json_encode($response);
    }

    function save_bulk_subject()
    {
        $response               = array();
        $response['message']    = 'Subjects saved successfully';
        $response['error']      = false;

        $sub_names              = strip_tags($this->input->post('sub_names'));
        $course_category        = $this->input->post('course_category');
        $category_name          = $this->input->post('category_name');
       //print_r($_POST); return;
        //validation starts
        $message                = '';

        if($course_category == 0){
            $message           .= 'Please choose category.<br />';
            $response['error']  = true;
        }

        $subject_names          = preg_replace("/((\r?\n)|(\r\n?))/", ',', $sub_names);
        if(trim($sub_names) == '')
        {
            if(trim($this->input->post('sub_names')) == ''){
                $message       .= 'Subject names cannot be empty<br />';
            }else{
                $message       .= 'Invalid subject names<br />';
            }
            $response['error']  = true;
        }
        else
        {  
            $subject_names          = array_map('trim',(explode(',', $subject_names)));
            $subject_names          = array_unique($subject_names);
            $subject_exists         = array(); 
            
            $excludes = false;
            $editQuestionSubjectSubId = false;
            if($this->input->post('category')){
                $excludes  = true; 
                $editQuestionSubjectSubId = $this->input->post('editQuestionSubjectSubId');
            }
            foreach($subject_names as $sub_name)
            {
                $subjects           = $this->Category_model->subject(array('subject_name'=>trim($sub_name), 'category_id'=>$course_category,'qs_deleted'=> true, 'excludes' => $excludes));
               //echo $this->db->last_query();die;
                if($subjects){
                    
                    if($excludes){

                        if($subjects['id'] == $editQuestionSubjectSubId)
                        {
                            $subject_exists[] = $subjects['qs_subject_name'];
                        }
                        
                    }
                    else
                    {
                        $subject_exists[] = $subjects['qs_subject_name'];
                    }
                }
                
                $subjects        = '';
            }

            $exist_subject_names    = implode(',', $subject_exists);
            //print_r(implode(',',$subject_names));
            //print_r($exist_subject_names); die;
            if(!empty($subject_exists))
            {                
                $message           .= '<b>'.$exist_subject_names.'</b> is currently in use. Please use another<br />';
                $response['error']  = true; 
            }
        }
        $GLOBALS['subject'] = array();
        if($response['error'] == false)
        {
            foreach($subject_names as $subject_name){
                $subject                           = array();
                $subject['id']                     = $editQuestionSubjectSubId;
                if($subject_name != '')
                {
                    $subject['qs_subject_name']    = $subject_name;
                    $subject['qs_category_id']     = $course_category;
                    $subject['qs_status']          = '1';
                    $subject['qs_account_id']      = $this->config->item('id');
                    $subject['action_by']          = $this->auth->get_current_admin('id');
                    $subject['action_id']          = $this->actions['create'];
                    //print_R($subject); die;
                    $subject['id']                 = $this->Category_model->save_subject($subject);
                    $GLOBALS['subject']            = $subject['id'];
                    $response['exist']             = '0';
                    $response['subject'][]         = $subject;
                }  
            }
            /*Log creation*/
            $subject_names_str                  = implode(',', $subject_names);
            $subject_count                      = (sizeof($subject_names)>1)?'the subjects':'a subject';
            $user_data                          = array();
            $user_data['user_id']               = $this->__loggedInUser['id'];
            $user_data['username']              = $this->__loggedInUser['us_name'];
            $user_data['useremail']              = $this->__loggedInUser['us_email'];
            $user_data['user_type']             = $this->__loggedInUser['us_role_id'];
            $message_template                   = array();
            $message_template['username']       = $this->__loggedInUser['us_name'];
            $message_template['subjects']       = $subject_count.' '.$subject_names_str;
            $message_template['category']       = $category_name;
            $triggered_activity                 = 'sub_category_created';
            log_activity($triggered_activity, $user_data, $message_template);
        }
        else
        {
            $response['message'] = $message;            
            $response['error']   = true;            
        }
        echo json_encode($response);
    }

    

    function get_subjects()
    {
        $category_id         = $this->input->post('course_category');
        $response            = array();
        $response['error']   = false;
        $subject_listing     = $this->Category_model->subjects(array('select'=>'questions_subject.id,questions_subject.qs_subject_name,questions_subject.qs_category_id','qs_deleted'=>true,'category_id'=>$category_id));
        $response['subject'] = $subject_listing;
        echo json_encode($response);
    }

     /* Question Topic */

    function edit_question_topic()
    {
        $topic_id               = $this->input->post('id');
        $response               = array();
        $response['message']    = '';
        $response['error']      = false;
        if($topic_id > 0)
        {
            $response['topic']                  = $this->Category_model->topic(array('id' => $topic_id));
            $response['topic']['qt_topic_name'] = strip_tags($response['topic']['qt_topic_name']);
            if(empty($response['topic']))
            {
                $response['message']            = 'Requested topic not found.';
                $response['error']              = true;
            }
        }
        echo json_encode($response);die;   
    }

    function save_question_topic()
    {
        $response               = array();
        $response['message']    = '';
        $response['error']      = false;

        $category               = strip_tags($this->input->post('category'));
        $subject_id             = strip_tags($this->input->post('question_subject'));
        $topic_id               = strip_tags($this->input->post('topic_id'));
        $topic_name             = $this->input->post('topic_name');
        $category_name          = $this->input->post('category_name');
        $subject_name           = $this->input->post('subject_name');
        
        //validation starts
        $message                = '';
        if($category == '')
        {
            $message           .= 'Please select course category<br />';
            $response['error'] = true;
        }

        if($subject_id == 0)
        {
            $message           .= 'Please select question subject<br />';
            $response['error']  = true;
        }
        
        if(trim($topic_name) == '')
        {
            if(trim($this->input->post('topic_name')) == '') {
                $message      .= 'Topic name cannot be empty<br />';
            } else {
                $message      .= 'Invalid topic name<br />';
            }
            $response['error'] = true;
        }
       
        if($response['error'] == false)
        {
            $check_topic                     = $this->Category_model->topic(array('id' => $topic_id));
            $topic_update                    = array();
            if($check_topic)
            {  $topics                             = $this->Category_model->topic(array('topic_name'=>trim($topic_name), 'category_id' =>$category, 'subject_id' =>$subject_id, 'excludes' => $topic_id,'qt_deleted'=> true));
               if(!empty($topics))
               {
                $response['message']    = 'Topic name already used in the selected category and subject!<br/>';
                $response['error']      = true;
                //print_r($topics);echo $this->db->last_query();die;
               }
               else
               {
                $response['exist']                 = '1';
                $topic_update['qt_topic_name']     = $topic_name;
                $topic_update['qt_category_id']    = $category;
                $topic_update['qt_subject_id']     = $subject_id;
                $topic_update['action_by']         = $this->auth->get_current_admin('id');
                $topic_update['action_id']         = $this->actions['update'];
                $topic_update['updated_date']      = date("Y-m-d H:i:s");
                $topic_update['id']                = $topic_id;
                $this->Category_model->save_topic($topic_update);
                $response['message']    = 'Topic saved successfully';
                $response['error']      = false;
                /*Log creation*/
                $user_data                          = array();
                $user_data['user_id']               = $this->__loggedInUser['id'];
                $user_data['username']              = $this->__loggedInUser['us_name'];
                $user_data['useremail']              = $this->__loggedInUser['us_email'];
                $user_data['user_type']             = $this->__loggedInUser['us_role_id'];
                $message_template                   = array();
                $message_template['username']       = $this->__loggedInUser['us_name'];
                $message_template['topic']          = $topic_name;
                $message_template['category']       = $category_name;
                $message_template['subject']        = $subject_name;
                $triggered_activity                 = 'topics_updated';
                log_activity($triggered_activity, $user_data, $message_template);
               }
            } 
            else 
            {
                $response['message']  = "Invalid topic";            
                $response['error']    = true;  
            }
            
        }
        else
        {
            $response['message']  = $message;            
            $response['error']    = true;            
        }
        $response['ques_topic']   = $topic_update;
        echo json_encode($response);
    }

    function check_topic_connection()
    {
        $category_id            = $this->input->post('category_id');
        $topic_id               = $this->input->post('topic_id');
        $response               = array();
        $response['error']      = false;
        $link                   = '?filter=all&type=all&offset=1&';
        $message                = '';
        $response['message']    = '';
      
        $check_generate_test_questions = $this->Generate_test_model->questions(array('topic_id'=>$topic_id,'not_deleted'=>true, 'count' => true));
        if($check_generate_test_questions > 0)
        {
            $link               .= 'category='.$category_id.'&topic='.$topic_id;
            $message            .= ' - Questions <a target="_blank" href="'.site_url('admin/generate_test/'.$link).'">click here to see assigned Questions</a><br/>';            
            $response['error']   = true; 
        }
        
        if($response['error'] ==  true){
            $response['message']  = 'Please unassign the following from this topic<br/>';
            $response['message'] .= $message;
        }
        echo json_encode($response);
    }

    function delete_question_topic()
    {
        $topic_id                   = $this->input->post('topic_id');
        $topic_name                 = $this->input->post('topic_name');
        $category_name              = $this->input->post('category_name');
        $subject_name               = $this->input->post('subject_name');
        $data                       = array();
        $data['qt_deleted']         = '1';
        $topic_delete               = $this->Generate_test_model->delete_topic($topic_id, $data);
        /*Log creation*/
        $user_data                          = array();
        $user_data['user_id']               = $this->__loggedInUser['id'];
        $user_data['username']              = $this->__loggedInUser['us_name'];
        $user_data['useremail']              = $this->__loggedInUser['us_email'];
        $user_data['user_type']             = $this->__loggedInUser['us_role_id'];
        $message_template                   = array();
        $message_template['username']       = $this->__loggedInUser['us_name'];
        $message_template['topic']          = $topic_name;
        $message_template['category']       = $category_name;
        $message_template['subject']        = $subject_name;
        $triggered_activity                 = 'topics_deleted';
        log_activity($triggered_activity, $user_data, $message_template);

        $response['message']        = 'Topic successfully deleted';      
        $response['error']          = false;       
        $response['topic_id']       = $topic_id; 
        echo json_encode($response);
    }

    function get_topics()
    {
        $category_id                = $this->input->post('course_category');
        $subject_id                 = $this->input->post('question_subject');
        $response = array();
        $response['error']          = false;
        $topic_listing              = $this->Category_model->topics(array('select'=>'questions_topic.id,questions_topic.qt_topic_name','qt_deleted'=>true,'category_id'=>$category_id,'subject_id'=>$subject_id));
        $response['topic']          = $topic_listing;
        echo json_encode($response);
    }

    function save_bulk_topic()
    {
        $response               = array();
        $response['message']    = 'Topics saved successfully';
        $response['error']      = false;

        $bulk_topics            = strip_tags($this->input->post('bulk_topics'));
        $course_category        = $this->input->post('course_category');
        $subject_id             = $this->input->post('subject_id');
        $category_name          = $this->input->post('category_name');
        $subject_name           = $this->input->post('subject_name');
        
        //validation starts
        $message                = '';
        $topic_names            = preg_replace("/((\r?\n)|(\r\n?))/", ',', $bulk_topics);
        if($subject_id==0){
            $message           .= 'Please choose subject<br />';
            $response['error']  = true;
        }

        if(trim($bulk_topics) == '')
        {
            if(trim($this->input->post('bulk_topics')) == ''){
                $message        .= 'Topic names cannot be empty<br />';
            }else{
                $message        .= 'Invalid topic names<br />';
            }
            $response['error']   = true;
        }
        else
        {  
            $topic_names            = array_map('trim',(explode(',', $topic_names)));
            $topic_names            = array_unique($topic_names);
            $topic_exists           = array();
            foreach($topic_names as $topic_name){
                $topics             = $this->Category_model->topic(array('topic_name'=>trim($topic_name), 'category_id'=>$course_category,'subject_id'=>$subject_id,'qt_deleted'=> true));
                //print_r($topics); die;
                if($topics) {
                    $topic_exists[] = $topics['qt_topic_name'];
                }
                $topics          = '';
            }
            $exist_topic_names      = implode(',', $topic_exists);
            if(!empty($topic_exists))
            {                
                $message           .= $exist_topic_names.' are currently in use. Please use another<br />';
                $response['error']  = true; 
            }
        }
        if($response['error'] == false)
        {
            foreach($topic_names as $topic_name)
            {
                $topic                           = array();
                $topic['id']                     = false;
                if($topic_name != '')
                {
                    $topic['qt_topic_name']      = $topic_name;
                    $topic['qt_category_id']     = $course_category;
                    $topic['qt_subject_id']      = $subject_id;
                    $topic['qt_status']          = '1';
                    $topic['qt_account_id']      = $this->config->item('id');
                    $topic['action_id']          = '1';
                    $topic['action_by']          = $this->auth->get_current_admin('id');
                    $topic['updated_date']       = date('Y-m-d H:i:s');
                    $topic['id']                 = $this->Category_model->save_topic($topic);
                    $response['exist']           = '0';
                    $response['topic'][]         = $topic;
                } 
            }
            /*Log creation*/
            $topic_names_str                = implode(',', $topic_names);
            $topic_count                    = (sizeof($topic_names)>1)?'the topics':'a topic';
            $user_data                      = array();
            $user_data['user_id']           = $this->__loggedInUser['id'];
            $user_data['username']          = $this->__loggedInUser['us_name'];
            $user_data['useremail']          = $this->__loggedInUser['us_email'];
            $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
            $message_template               = array();
            $message_template['username']   = $this->__loggedInUser['us_name'];
            $message_template['topics']     = $topic_count.' '.$topic_names_str;
            $message_template['category']   = $category_name;
            $message_template['subject']    = $subject_name;
            $triggered_activity             = 'topics_created';
            log_activity($triggered_activity, $user_data, $message_template);

        }
        else
        {
            $response['message'] = $message;            
            $response['error']   = true;            
        }
        echo json_encode($response);
    }
    function merge_subject()
    {
        $response               = array();
        $response['message']    = 'Subject merged successfully';
        $response['error']      = false;
        $merge_subjects         = $this->input->post('merge_subjects');
        $merge_subject_name     = $this->input->post('merge_subject_name');
        $merged_subject_names   = $this->input->post('merged_subject_names');
        $category_id            = $this->input->post('category_id');
        $category_name          = $this->input->post('category_name');

         //validation starts
        $message                = '';
        if(empty($merge_subjects))
        {
            $message           .= 'Please select question subjects<br />';
            $response['error']  = true;
        }
        
        if(trim($merge_subject_name) == '')
        {
            if(trim($this->input->post('merge_subject_name')) == ''){
                $message       .= 'Subject name cannot be empty<br />';
            }else{
                $message       .= 'Invalid subject name<br />';
            }
            $response['error']  = true;
        }
        else
        {
            $subject_array         = $this->Category_model->subject(array('subject_name' => $merge_subject_name, 'category_id' => $category_id, 'qs_deleted' => true));
            if($subject_array)
            {                
                $message          .= 'Subject name is currently in use. Please use another<br />';
                $response['error'] = true; 
            }
        }
        $GLOBALS['subject_id']                      = '';
        if($response['error'] == false)
        {
            $subject                                = array();
            $subject['id']                          = false;
            $subject['qs_subject_name']             = $merge_subject_name;
            $subject['qs_category_id']              = $category_id;
            $subject['qs_status']                   = '1';
            $subject['qs_account_id']               = $this->config->item('id');
            $subject['action_id']                   = '1';
            $subject['action_by']                   = $this->auth->get_current_admin('id');
            $subject['updated_date']                = date('Y-m-d H:i:s');
            $subject['id']                          = $this->Category_model->save_subject($subject);
            $GLOBALS['subject_id']                  = $subject['id'];
            $update_subject                         = array();
            $update_subject['subject_id']           = $GLOBALS['subject_id']; 
            $update_subject['merge_subject_ids']    = $merge_subjects;   
            $update_subjects                        = $this->Category_model->merge_subject($update_subject);
            $merged_subject_str                     = implode(",",$merged_subject_names);

            /*Log creation*/
            $user_data                      = array();
            $user_data['user_id']           = $this->__loggedInUser['id'];
            $user_data['username']          = $this->__loggedInUser['us_name'];
            $user_data['useremail']          = $this->__loggedInUser['us_email'];
            $user_data['user_type']         = $this->__loggedInUser['us_role_id'];
            $message_template               = array();
            $message_template['username']   = $this->__loggedInUser['us_name'];
            $message_template['subjects']   = 'the subjects '.$merged_subject_str.' to '.$merge_subject_name;
            $message_template['category']   = $category_name;
            $triggered_activity             = 'sub_category_merged';
            log_activity($triggered_activity, $user_data, $message_template);

            $response['merge_subject_ids']          = $merge_subjects; 
            $response['subject_id']                 = $GLOBALS['subject_id'];
            $response['exist']                      = '0';

        }
        else
        {
            $response['message'] = $message;            
            $response['error']   = true;            
        }
        echo json_encode($response);
    }

    function merge_topic()
    {
        $response               = array();
        $response['message']    = 'Topic merged successfully';
        $response['error']      = false;
        $merge_topics         = $this->input->post('merge_topics');
        $merge_topic_name       = $this->input->post('merge_topic_name');
        $merged_topic_names     = $this->input->post('merged_topic_names');
        $category_id            = $this->input->post('category_id');
        $subject_id             = $this->input->post('subject_id');
        $category_name          = $this->input->post('category_name');
        $subject_name           = $this->input->post('subject_name');
         //validation starts
        $message                = '';
        if(empty($merge_topics))
        {
            $message           .= 'Please select question topics<br />';
            $response['error']  = true;
        }
        
        if(trim($merge_topic_name) == '')
        {
            if(trim($this->input->post('merge_topic_name')) == ''){
                $message       .= 'Topic name cannot be empty<br />';
            }else{
                $message       .= 'Invalid topic name<br />';
            }
            
            $response['error']  = true;
        }
        else
        {
            $topic_array           = $this->Category_model->topic(array('topic_name' => $merge_topic_name, 'category_id' => $category_id, 'subject_id'=> $subject_id));
            if($topic_array)
            {                
                $message          .= 'Topic name is currently in use. Please use another<br />';
                $response['error'] = true; 
            }
        }
        if($response['error'] == false)
        {
            $topic                       = array();
            $topic['id']                 = false;
            $topic['qt_topic_name']      = $merge_topic_name;
            $topic['qt_category_id']     = $category_id;
            $topic['qt_subject_id']      = $subject_id;
            $topic['qt_status']          = '1';
            $topic['qt_account_id']      = $this->config->item('id');
            $topic['action_id']          = '1';
            $topic['action_by']          = $this->auth->get_current_admin('id');
            $topic['updated_date']       = date('Y-m-d H:i:s');
            $topic['id']                 = $this->Category_model->save_topic($topic);
            $update_topic                       = array();
            $update_topic['subject_id']         = $subject_id;
            $update_topic['topic_id']           = $topic['id']; 
            $update_topic['merge_topic_ids']    = $merge_topics;   
            $update_topic                       = $this->Category_model->merge_topic($update_topic);
            $response['merge_topic_ids']        = $merge_topics; 
            $response['topic_id']               = $topic['id']; 
            $response['exist']                  = '0';

            $merged_topic_str                   = implode(",",$merged_topic_names);

            /*Log creation*/
            $user_data                          = array();
            $user_data['user_id']               = $this->__loggedInUser['id'];
            $user_data['username']              = $this->__loggedInUser['us_name'];
            $user_data['useremail']              = $this->__loggedInUser['us_email'];
            $user_data['user_type']             = $this->__loggedInUser['us_role_id'];
            $message_template                   = array();
            $message_template['username']       = $this->__loggedInUser['us_name'];
            $message_template['topics']         = 'the topics '.$merged_topic_str.' to '.$merge_topic_name;
            $message_template['category']       = $category_name;
            $message_template['subject']        = $subject_name;
            $triggered_activity                 = 'topics_merged';
            log_activity($triggered_activity, $user_data, $message_template);
        }
        else
        {
            $response['message']                = $message;            
            $response['error']                  = true;            
        } 
        echo json_encode($response);
    } 
    
    public function change_category_status()
    {
        $response                 = array();
        $response['error']        = false;
        $category_id              = $this->input->post('category_id');
        $category_param           = array();
        $category_param['id']     = $category_id;
        $category_param['select'] = 'categories.id, categories.ct_name, categories.ct_status';
        //$category                 = $this->Category_model->category($category_param);
        
        $save                     = array();
        $save['id']               = $category_id;
        $save['action_by']        = $this->auth->get_current_admin('id');
        $save['updated_date']     = date('Y-m-d H:i:s');
        
        $save['ct_status']        = $this->input->post('ct_status');
        $mstatus                  = 'category_activated';
        $response['message']      = lang('published');
        if($save['ct_status'] == '0')
        {
            $mstatus              = 'category_deactivated'; 
            $response['message']  = lang('unpublished');
        }

        //echo '<pre>'; print_r($response); exit;
        if (!$this->Category_model->save($save)){
            $response['error']    = true;
            $response['message']  = lang('error_change_status');
        }

        $response['category']     = $this->Category_model->category($category_param);
        $response['category']['manager'] = $this->__course_permission;
        $user_data                = array();
        $user_data['user_id']     = $this->__loggedInUser['id'];
        $user_data['username']    = $this->__loggedInUser['us_name'];
        $user_data['useremail']    = $this->__loggedInUser['us_email'];
        $user_data['user_type']   = $this->__loggedInUser['us_role_id']; ;
        
        $message_template                   = array();
        $message_template['username']       = $this->__loggedInUser['us_name'];;
        $message_template['category_name']  = $response['category']['ct_name'];
        
        $triggered_activity                 = $mstatus;
        log_activity($triggered_activity, $user_data, $message_template); 

        $this->invalidate_category();
        echo json_encode($response);
    }

    function invalidate_category()
    {
        $this->memcache->delete('home');
        $this->memcache->delete('categories');
        $this->memcache->delete('popular_courses');
        $this->memcache->delete('featured_courses');
        $this->memcache->delete('all_sorted_course');
        $objects                = array();
        $objects['key']         = 'categories';
        $callback               = 'get_categories';
        $categories             = $this->memcache->get($objects, $callback, array());
    } 

    function language()
    {
        $response = array();
        $response['language'] = array();
        $response['language'] = get_instance()->lang->language;
        echo json_encode($response);
    }

    function get_courses_by_category()
    {
        $category_id        = $this->input->get('id');
        $objects            = array();
        $objects['key']     = 'all_courses';
        $callback           = 'all_courses';
        $courses            = $this->memcache->get($objects, $callback, array('cb_status' => '1'));
        $return             = array();
        foreach($courses as $course)
        {
            if(isset($course['cb_category']) && $course['cb_category'] == $category_id)
            {
                $return[]   = $course;
            }
        }
        
        echo json_encode($return); return;
    }

    function get_courses_questions(){

    }

    function update_category_position()
    {
        $response                   = array();
        $response['error']          = 'false';
        $response['message']        = 'categories re ordered';
        $categories                 = $this->input->post('category_id');
        // echo "<pre>";print_r($categories); exit;
        
        if( !empty($categories))
        {
            $this->Category_model->save_bulk($categories);
        }
        $this->invalidate_category();
        echo json_encode($response);
    }
}
   
<?php
class Forum extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $redirect	= $this->auth->is_logged_in(false, false);
        if (!$redirect)
        {
            $redirect   = true;
            $teacher    = $this->auth->is_logged_in(false, false, 'teacher');
            if($teacher)
            {
                $redirect = false;
                $teacher = $this->auth->get_current_user_session('teacher');
                
            }
            if($redirect)
            {
                $redirect   = true;
                $content_editor    = $this->auth->is_logged_in(false, false, 'content_editor');
                if($content_editor)
                {
                    $redirect = false;
                    $content_editor = $this->auth->get_current_user_session('content_editor');
                    
                }
                if($redirect)
                {
                    redirect('login');
                }
            }
        }
        $this->load->library("pagination");
        $this->actions = $this->config->item('actions');
        $this->load->model('Forum_model');
        $this->lang->load('user');
    }
    
    function index($slug = false)
    {
        $data = array();
        if($slug === false){
        	
            $config                     = array();
            $total_row                  = $this->Forum_model->forum_count();
            $config["total_rows"]       = $total_row;
            $config["per_page"]         = 4;
            $config['num_links']        = $total_row;
            $this->pagination->initialize($config);
            $page                       = 1;
            if($page===NULL||$page<=0)
            {
                $page                   = "1";
            }
            $page                       = ($page - 1)* $config["per_page"];
        	$forums = $this->Forum_model->list_forum(array('offset'=>$page,'limit'=>$config["per_page"],'order_by'=>'forums.forum_created','direction'=>'DESC'));
            $str_links                  = $this->pagination->create_links();
            $data['current_page'] = $this->pagination->cur_page;
            $data['pages'] = ceil($total_row/$this->pagination->per_page);
            $data['total_forum'] = $total_row;
            $data['forum_limit'] = $config['per_page'];
            if($data['current_page']==0){
            	$data['current_page'] = 1;
            }
            //Pagination end

            foreach ($forums as $key => $forum) {
            	//$forum['created'] = $this->Forum_model->get_username_from_user_id($forum['u_id']);
                $forums[$key]['forum_created'] = $this->get_time($forum['forum_created'],true);
                $forums[$key]['forum_modified'] = $this->get_time($forum['forum_modified'],true);
                
                if ($forums[$key]['total_topics']>0) {
                    // $forum has topics
                    $forums[$key]['latest_topic']            = $this->Forum_model->get_forum_latest_topic($forum['id']);
                    if(isset($forums[$key]['latest_topic'])){
                        //echo '<pre>';print_r($forums[$key]['latest_topic']);
                        //echo '<pre>';print_r($forum);die;
                        $forums[$key]['latest_topic']['permalink'] = $forum['forum_slug'] . '/' . $forums[$key]['latest_topic']['topic_slug'];
                        $forums[$key]['latest_topic']['author']    = $this->Forum_model->get_username_from_user_id($forums[$key]['latest_topic']['user_id']);
                        $forums[$key]['latest_topic']['topic_created'] = $this->get_time($forums[$key]['latest_topic']['topic_created']);
                    }else{
                        // $forum doesn't have topics yet
                        $forums[$key]['latest_topic'] = array();
                        $forums[$key]['latest_topic']['permalink'] = '';
                        $forums[$key]['latest_topic']['author']    = '';
                        $forums[$key]['latest_topic']['topic_created'] = '';
                    }
                } else {

                    // $forum doesn't have topics yet
                    $forums[$key]['latest_topic'] = array();
                    $forums[$key]['latest_topic']['permalink'] = '';
                    $forums[$key]['latest_topic']['author']    = '';
                    $forums[$key]['latest_topic']['topic_created'] = '';

                }
            }
                //echo '<pre>'; print_r($data);die;
            $data['forums'] = $forums;
            $breadcrumb  = '<ul class="olp-breadcrumb">';
            $breadcrumb .= '<li class="olp-bread-lists"><a href="'.admin_url().'">Admin Home</a></li><li class="olp-bread-lists bread-arrow"><span> <i class="icon-right-open"></i></span></li>';
            $breadcrumb .= '<li class="olp-bread-lists"><a class="bread-active" href="javascript:void(0);">Forum Home</a></li>';
            $breadcrumb .= '</ul>';
            $data['forum_style'] = TRUE;
            $data['breadcrumb1'] = $breadcrumb;

            $this->load->view($this->config->item('admin_folder').'/forum', $data);
        }else{
            // get id from slug
            $forum_id = $this->Forum_model->get_forum_id_from_forum_slug($slug);
            if(!isset($forum_id)){
                redirect(admin_url('forum'));
            }else{
                // create objects
                $forum    = $this->Forum_model->get_forum($forum_id);
                $per_page = 4;
                //pagination start
                $config                     = array();
                $total_row                  = $this->Forum_model->topic_count($forum_id);
                $config['num_links']        = $total_row;
                $page                       = 1;
                if($page===NULL||$page<=0)
                {
                    $page                   = "1";
                }
                $page                       = ($page - 1)* $per_page;
                $topics = $this->Forum_model->list_forum_topics(array('forum_id'=>$forum_id,'offset'=>0,'limit'=>$per_page,'order_by'=>'topic_created','direction'=>'DESC'));
                //Pagination end
                
                foreach ($topics as $key => $topic) {
                    
                    $topics[$key]['user_id']                 = $this->Forum_model->get_username_from_user_id($topic['user_id']);
                    $topics[$key]['user_id']                 = $topics[$key]['user_id']['us_name']; 
                    $forum_slug 							 = $this->Forum_model->get_forum_slug($topic['forum_id']);
                    $topics[$key]['permalink']      		 = $forum_slug['forum_slug'] . '/' . $topic['topic_slug'];
                    if($topics[$key]['total_comments']<=0){
                        $topics[$key]['comments_user_id']    = '';
                        
                        $topics[$key]['topic_created']       = $this->get_time($topic['topic_created'],true);
                        $topics[$key]['topic_modified']      = $this->get_time($topic['topic_modified'],true);
                        
                    }else{
                        $topics[$key]['comments_user_id']     = $this->Forum_model->get_username_from_user_id($topic['comments_user_id']);
                        $topics[$key]['comments_user_id']     = $topics[$key]['comments_user_id']['us_name'];
                        $topics[$key]['topic_created']                  = $this->get_time($topic['topic_created'],true);
                        $topics[$key]['topic_modified']                 = $this->get_time($topic['topic_modified'],true);
                        $topics[$key]['comment_created'] 				= $this->get_time($topic['comment_created']);
                    }
                }
                $breadcrumb  = '<ul class="olp-breadcrumb">';
                $breadcrumb .= '<li class="olp-bread-lists"><a href="'.admin_url().'">Admin Home</a></li><li class="olp-bread-lists bread-arrow"><span> <i class="icon-right-open"></i></span></li>';
                $breadcrumb .= '<li class="olp-bread-lists"><a href="' . admin_url('forum') . '">Forum Home</a></li><li class="olp-bread-lists bread-arrow"><span> <i class="icon-right-open"></i></span></li>';
                $breadcrumb .= '<li class="olp-bread-lists"><a class="bread-active" href="javascript:void(0)">' . (strlen($forum->forum_name) > 15 ? substr($forum->forum_name,0,12)."..." : $forum->forum_name) . '</a> </li>';
                $breadcrumb .= '</ul>';
                // assign created objects to the data object
                $data['forum_style'] = TRUE;
                $data['forum'] = $forum;
                $data['topics'] = $topics;
                $data['breadcrumb1'] = $breadcrumb;
                $data['total_topics'] = $total_row;
                $data['current_page'] = $this->pagination->cur_page;
                $data['pages'] = ceil($total_row/$per_page);
                if($data['pages']==0){
                	$data['pages'] = 1;
                }
                if($data['current_page']==0){
	            	$data['current_page'] = 1;
	            }
	            $data['forum_topic_limit'] = $per_page;
                // load views and send data
                //echo '<pre>'; print_r($data);die();
                $this->load->view($this->config->item('admin_folder').'/forum_single', $data);
            }
        }
    }

	function get_ajax_topic(){
		$keyword= '';
	    $offset = $this->input->post('offset');
	    $limit  = $this->input->post('limit');
	    $keyword= $this->input->post('keyword');
	    $listing_type = $this->input->post('listing_type');
	    $forum_id = $this->input->post('forum_id');
	    $order_by = '';
	    $direction = '';

	    if($listing_type == 1){
	        $order_by = 'views';
	        $direction = 'DESC';
	    }else if($listing_type == 2){
	        $order_by = 'total_comments';
	        $direction = 'DESC';
	    }else if($listing_type == 3){
            $order_by = 'topic_created';
            $direction = 'DESC';
        }else{
	        $order_by = 'comment_created';
	        $direction = 'DESC';
	    }

	    $page                       = $offset;
	    if($page===NULL||$page<=0)
	    {
	        $page                   = "1";
	    }
	    $page                       = ($page - 1)* $limit;

	    $topics = $this->Forum_model->list_forum_topics(array('forum_id'=>$forum_id,'offset'=>$page,'limit'=>$limit,'order_by'=>$order_by,'direction'=>$direction,'keyword'=>$keyword));

	    foreach ($topics as $key => $topic) {
	                
	        $topics[$key]['user_id']                 = $this->Forum_model->get_username_from_user_id($topic['user_id']);
	        $topics[$key]['user_id']                 = $topics[$key]['user_id']['us_name']; 
	        $forum_slug 							 = $this->Forum_model->get_forum_slug($topic['forum_id']);
	        $topics[$key]['permalink']      		 = $forum_slug['forum_slug'] . '/' . $topic['topic_slug'];
	        if($topics[$key]['total_comments']<=0){
	            $topics[$key]['comments_user_id']    = '';
	            
	            $topics[$key]['topic_created']       = $this->get_time($topic['topic_created'],true);
	            $topics[$key]['topic_modified']      = $this->get_time($topic['topic_modified'],true);
	            
	        }else{
	            $topics[$key]['comments_user_id']     = $this->Forum_model->get_username_from_user_id($topic['comments_user_id']);
	            $topics[$key]['comments_user_id']     = $topics[$key]['comments_user_id']['us_name'];
	            $topics[$key]['topic_created']                  = $this->get_time($topic['topic_created'],true);
	            $topics[$key]['topic_modified']                 = $this->get_time($topic['topic_modified'],true);
	            $topics[$key]['comment_created'] 				= $this->get_time($topic['comment_created']);
	        }
	    }
	    
	    $total_row                  = $this->Forum_model->list_forum_topics(array('forum_id'=>$forum_id,'order_by'=>$order_by,'direction'=>$direction,'keyword'=>$keyword,'count'=>true));
	    $data['pages'] = ceil($total_row/$limit);
	    $data['topics'] = $topics;
	    $data['total_topics'] = $total_row;
	    echo json_encode($data);

	}    


    function get_ajax_forum(){

        $offset = $this->input->post('offset');
        $limit  = $this->input->post('limit');
        $listing_type = $this->input->post('listing_type');
        $order_by = '';
        $direction = '';

        if($listing_type == 1){
            $order_by = 'forum_topic_comments_cp.total_comments';
            $direction = 'ASC';
        }else if($listing_type == 2){
            $order_by = 'forum_topics_cp.total_topics';
            $direction = 'DESC';
        }else{
            $order_by = 'forums.forum_created';
            $direction = 'DESC';
        }

        $page                       = $offset;
        if($page===NULL||$page<=0)
        {
            $page                   = "1";
        }
        $page                       = ($page - 1)* $limit;

        $forums = $this->Forum_model->list_forum(array('offset'=>$page,'limit'=>$limit,'order_by'=>$order_by,'direction'=>$direction));

        //echo '<pre>';print_r($forums);die;

        foreach ($forums as $key => $forum) {
            if ($forums[$key]['total_topics']>0) {
                // $forum has topics
                $forums[$key]['latest_topic']            = $this->Forum_model->get_forum_latest_topic($forum['id']);
                if(isset($forums[$key]['latest_topic'])){
                    //echo '<pre>';print_r($forums[$key]['latest_topic']);
                    //echo '<pre>';print_r($forum);die;
                    $forums[$key]['latest_topic']['permalink'] = $forum['forum_slug'] . '/' . $forums[$key]['latest_topic']['topic_slug'];
                    $forums[$key]['latest_topic']['author']    = $this->Forum_model->get_username_from_user_id($forums[$key]['latest_topic']['user_id']);
                    $forums[$key]['latest_topic']['topic_created'] = $this->get_time($forums[$key]['latest_topic']['topic_created']);
                }else{
                    // $forum doesn't have topics yet
                    $forums[$key]['latest_topic'] = array();
                    $forums[$key]['latest_topic']['permalink'] = '';
                    $forums[$key]['latest_topic']['author']    = '';
                    $forums[$key]['latest_topic']['topic_created'] = '';
                }
            } else {

                // $forum doesn't have topics yet
                $forums[$key]['latest_topic'] = array();
                $forums[$key]['latest_topic']['permalink'] = '';
                $forums[$key]['latest_topic']['author']    = '';
                $forums[$key]['latest_topic']['topic_created'] = '';

            }
        }
        //echo '<pre>'; print_r($data);die;
        $data['forums'] = $forums;

        echo json_encode($data);

    }


    function create_forum(){
        $data = array();
        $admin = $this->auth->get_current_user_session('admin');
        if(!isset($admin)){
            $admin = $this->auth->get_current_user_session('teacher');
            if(!isset($admin)){
                $admin = $this->auth->get_current_user_session('content_editor');
            }
        }
        // load form helper and validation library
        $this->load->helper('form');
        $this->load->library('form_validation');
        
        // set validation rules
        $this->form_validation->set_rules('title','Forum Title', 'trim|required|min_length[4]|max_length[100]');
        $this->form_validation->set_rules('description','Description', 'trim|max_length[300]');
        $breadcrumb  = '<ul class="olp-breadcrumb">';
        $breadcrumb .= '<li class="olp-bread-lists"><a href="'.admin_url().'">Admin Home</a></li><li class="olp-bread-lists bread-arrow"><span> <i class="icon-right-open"></i></span></li>';
        $breadcrumb .= '<li class="olp-bread-lists"><a href="' . site_url('admin/forum') . '">Forum Index</a></li><li class="olp-bread-lists bread-arrow"><span> <i class="icon-right-open"></i></span></li>';
        $breadcrumb .= '<li class="olp-bread-lists"><a class="bread-active" href="javascript:void(0)">Create Forum</a> </li>';
        $breadcrumb .= '</ul>';
        $data['breadcrumb1'] = $breadcrumb;
        $data['forum_style'] = TRUE;
        if ($this->form_validation->run() === false) {
            
            // keep what the user has entered previously on fields
            $data['ftitle']       = $this->input->post('title');
            $data['description'] = $this->input->post('description');
            
            // validation not ok, send validation errors to the view
            $this->load->view($this->config->item('admin_folder').'/forum_create', $data); 
            
        } else {
            
            // set variables from the form
            $title       = $this->input->post('title');
            $description = $this->input->post('description');
            $forum_slug  = $this->Forum_model->validate_forum_slug(strtolower(url_title($title)));
            $args = array(
                'forum_name'        => $title,
                'forum_slug'        => $forum_slug,
                'forum_description' => $description,
                'u_id'              => $admin['id'],
                'forum_created'     => date('Y-m-j H:i:s')
            );
            if ($this->Forum_model->create_forum($args)) {
                
                
                // forum creation ok
                redirect(admin_url('forum')); 
                
            } else {
                
                // forum creation failed, this should never happen
                $data->error = 'There was a problem creating the new forum. Please try again.';
                
                // send error to the view
                $this->load->view($this->config->item('admin_folder').'/forum_create', $data); 
                
            }
            
        }

    }

    public function display_topic($forum_slug, $topic_slug) {
        $admin = $this->auth->get_current_user_session('admin');
        if(!isset($admin)){
            $admin = $this->auth->get_current_user_session('teacher');
            if(!isset($admin)){
                $admin = $this->auth->get_current_user_session('content_editor');
            }
        }
        // load form helper and validation library
        $this->load->helper('form');
        $this->load->library('form_validation');
        // create the data object
        $data = array();

        // get ids from slugs
		$forum_id = $this->Forum_model->get_forum_id_from_forum_slug($forum_slug);
		$topic_id = $this->Forum_model->get_topic_id_from_topic_slug($topic_slug);
		if(!isset($forum_id)||!isset($topic_id)){
            redirect(admin_url('forum'));
        }else{
            // create objects
            $forum = $this->Forum_model->get_forum($forum_id);
            $topic = $this->Forum_model->get_topic($topic_id);
            $topic->author = $this->Forum_model->get_username_from_user_id($topic->user_id);
            $topic->author = $topic->author['us_name'];
            $role = $this->Forum_model->get_user_role_from_user_id($topic->user_id);
            $topic->author_role = $this->Forum_model->get_user_role_from_role_id($role);
            $topic->author_image = $this->Forum_model->get_userimage_from_user_id($topic->user_id);
            $topic->author_post_count = $this->Forum_model->get_user_post_count($topic->user_id);
            $datetime = strtotime($topic->topic_created);
            $topic->created_date = date('M d, Y',$datetime);
            $topic->created_time = date('h:i a',$datetime);
            //echo '<pre>';print_r($topic);die;
                //pagination start
                $config                     = array();
                $total_row                  = $this->Forum_model->post_count($topic_id);
                 //print_r($total_row); die;
                $per_page 			        = 2;
                $page                       = 1;
                $child_limit				= 2;
                if($page===NULL)
                {
                    $page                   = "1";
                }
                $page                       = ($page - 1)* $per_page;
                $posts = $this->Forum_model->get_replies(array('child_limit'=>$child_limit,'limit'=>$per_page,'offset'=>0,'topic_id'=>$topic->id),0);
                //Pagination end

            foreach ($posts as $parent_key => $post) {
                    $role = $this->Forum_model->get_user_role_from_user_id($posts[$parent_key]['user_id']);
    	            $posts[$parent_key]['author_role'] = $this->Forum_model->get_user_role_from_role_id($role);
    	            $posts[$parent_key]['author_post_count'] = $this->Forum_model->get_user_post_count($posts[$parent_key]['user_id']);
    	            $posts[$parent_key]['comment_created'] = $this->get_time_comment($post['comment_created'],true);
                    
                foreach ($posts[$parent_key]['children'] as $child_key => $cpost) {
                    $role = $this->Forum_model->get_user_role_from_user_id($cpost['user_id']);
                    $posts[$parent_key]['children'][$child_key]['author_role'] = $this->Forum_model->get_user_role_from_role_id($role);
		            $posts[$parent_key]['children'][$child_key]['author_post_count'] = $this->Forum_model->get_user_post_count($cpost['user_id']);
		            $posts[$parent_key]['children'][$child_key]['comment_created'] = $this->get_time_comment($cpost['comment_created'],true);
                }
            }

            $data['posts']              = $posts;

            $breadcrumb  = '<ul class="olp-breadcrumb">';
            $breadcrumb .= '<li class="olp-bread-lists"><a href="'.admin_url().'">Admin Home</a></li><li class="olp-bread-lists bread-arrow"><span> <i class="icon-right-open"></i></span></li>';
            $breadcrumb .= '<li class="olp-bread-lists"><a href="' . site_url('admin/forum') . '">Forum Index</a><li class="olp-bread-lists bread-arrow"><span> <i class="icon-right-open"></i></span></li>';
            $breadcrumb .= '<li class="olp-bread-lists"><a href="' . site_url('admin/forum/'.$forum->forum_slug) . '">' . (strlen($forum->forum_name) > 15 ? substr($forum->forum_name,0,12)."..." : $forum->forum_name) . '</a><li class="olp-bread-lists bread-arrow"><span> <i class="icon-right-open"></i></span></li>';
            $breadcrumb .= '<li class="olp-bread-lists"><a class="bread-active" href="javascript:void(0)">' . (strlen($topic->topic_name) > 15 ? substr($topic->topic_name,0,12)."..." : $topic->topic_name) . '</a></li>';
            $breadcrumb .= '</ul>';
            
            $data['forum_style'] = TRUE;
            $data['forum']      = $forum;
            $data['topic']      = $topic;
            $data['breadcrumb1'] = $breadcrumb;
            $data['total_posts'] = $total_row;
            $data['current_page'] = $this->pagination->cur_page;
            $data['pages'] = ceil($total_row/$per_page);
            $data['topic_comment_limit'] = $per_page;
            if($data['pages']==0){
            	$data['pages']=1;
            }
            $data['child_limit'] = $child_limit;
            $data['mentions']    = $this->get_users($topic_id);
            $data['forum_id']    = $forum_id;
            //$data['json_participants'] = $this->get_users($topic_id);
            $data['login']  = $this->auth->get_current_user_session('user');
            
            //print_r($data);die;
            $this->load->view($this->config->item('admin_folder').'/forum_topic_single', $data);
        }

    }

    public function reported($forum_slug, $topic_slug,$report_id){
        $admin = $this->auth->get_current_user_session('admin');
        if(!isset($admin)){
            $admin = $this->auth->get_current_user_session('teacher');
            if(!isset($admin)){
                $admin = $this->auth->get_current_user_session('content_editor');
            }
        }
        // load form helper and validation library
        $this->load->helper('form');
        $this->load->library('form_validation');
        // create the data object
        $data = array();

        $report_page_number = 1;
        $report_parent_id   = 0;

        // get ids from slugs
		$forum_id = $this->Forum_model->get_forum_id_from_forum_slug($forum_slug);
		$topic_id = $this->Forum_model->get_topic_id_from_topic_slug($topic_slug);
		$commentreported = $this->Forum_model->get_reported_comment($report_id);
		$report_parent_id = $commentreported['parent_id'];
		if(!isset($forum_id)||!isset($topic_id)||!isset($report_id)){
            redirect(admin_url('forum'));
        }else{
            // create objects
            $forum = $this->Forum_model->get_forum($forum_id);
            $topic = $this->Forum_model->get_topic($topic_id);
            $topic->author = $this->Forum_model->get_username_from_user_id($topic->user_id);
            $topic->author = $topic->author['us_name'];
            $role = $this->Forum_model->get_user_role_from_user_id($topic->user_id);
            $topic->author_role = $this->Forum_model->get_user_role_from_role_id($role);
            $topic->author_image = $this->Forum_model->get_userimage_from_user_id($topic->user_id);
            $topic->author_post_count = $this->Forum_model->get_user_post_count($topic->user_id);
            $datetime = strtotime($topic->topic_created);
            $topic->created_date = date('M d, Y',$datetime);
            $topic->created_time = date('H:i a',$datetime);
            //echo '<pre>';print_r($topic);die;
                //pagination start
                $config                     = array();
                $total_row                  = $this->Forum_model->post_count($topic_id);
                 //print_r($total_row); die;
                $per_page 			        = 2;
                $page                       = 1;
                $child_limit				= 2;
                if($page===NULL)
                {
                    $page                   = "1";
                }
                $page                       = ($page - 1)* $per_page;
                $posts = $this->Forum_model->get_replies(array('child_limit'=>$child_limit,'limit'=>$per_page,'offset'=>0,'topic_id'=>$topic->id),0);
                $posts_report = $this->Forum_model->get_replies(array('child_limit'=>0,'offset'=>0,'topic_id'=>$topic->id),0);
                //Pagination end
                $report_position = 1;
                $report_finded = 0;
            foreach ($posts_report as $parent_key => $post) {
            	if($commentreported['parent_id'] == 0){
	            	if(($commentreported['id'] == $post['id']) && ($report_finded == 0)){
						$report_finded = 1;
					}
					if($report_finded == 0){
						$report_position++;
					}
	            }else{
	            	if(($commentreported['parent_id'] == $post['id']) && ($report_finded == 0)){
						$report_finded = 1;
					}
					if($report_finded == 0){
						$report_position++;
					}
	            }
            }
            foreach ($posts as $parent_key => $post) {
                    $role = $this->Forum_model->get_user_role_from_user_id($posts[$parent_key]['user_id']);
    	            $posts[$parent_key]['author_role'] = $this->Forum_model->get_user_role_from_role_id($role);
    	            $posts[$parent_key]['author_post_count'] = $this->Forum_model->get_user_post_count($posts[$parent_key]['user_id']);
    	            $posts[$parent_key]['comment_created'] = $this->get_time_comment($post['comment_created'],true);
                foreach ($posts[$parent_key]['children'] as $child_key => $cpost) {
                    $role = $this->Forum_model->get_user_role_from_user_id($cpost['user_id']);
                    $posts[$parent_key]['children'][$child_key]['author_role'] = $this->Forum_model->get_user_role_from_role_id($role);
		            $posts[$parent_key]['children'][$child_key]['author_post_count'] = $this->Forum_model->get_user_post_count($cpost['user_id']);
		            $posts[$parent_key]['children'][$child_key]['comment_created'] = $this->get_time_comment($cpost['comment_created'],true);
                }
            }

            $report_page_number = ceil($report_position/$per_page);
            $data['posts']              = $posts;

            $breadcrumb  = '<ul class="olp-breadcrumb">';
            $breadcrumb .= '<li class="olp-bread-lists"><a href="'.admin_url().'">Admin Home</a></li><li class="olp-bread-lists bread-arrow"><span> <i class="icon-right-open"></i></span></li>';
            $breadcrumb .= '<li class="olp-bread-lists"><a href="' . site_url('admin/forum') . '">Forum Index</a><li class="olp-bread-lists bread-arrow"><span> <i class="icon-right-open"></i></span></li>';
            $breadcrumb .= '<li class="olp-bread-lists"><a href="' . site_url('admin/forum/'.$forum->forum_slug) . '">' . (strlen($forum->forum_name) > 15 ? substr($forum->forum_name,0,12)."..." : $forum->forum_name) . '</a><li class="olp-bread-lists bread-arrow"><span> <i class="icon-right-open"></i></span></li>';
            $breadcrumb .= '<li class="olp-bread-lists"><a class="bread-active" href="javascript:void(0)">' . (strlen($topic->topic_name) > 15 ? substr($topic->topic_name,0,12)."..." : $topic->topic_name) . '</a></li>';
            $breadcrumb .= '</ul>';
            $data['reportdata']['parentid'] = $report_parent_id;
            $data['reportdata']['reportid'] = $report_id;
            
            $data['reportdata']['pagenumber'] = $report_page_number;
            $data['reportdata']['finded'] = $report_finded;
            $data['forum_style'] = TRUE;
            $data['forum']      = $forum;
            $data['topic']      = $topic;
            $data['breadcrumb1'] = $breadcrumb;
            $data['total_posts'] = $total_row;
            $data['current_page'] = $this->pagination->cur_page;
            $data['pages'] = ceil($total_row/$per_page);
            $data['topic_comment_limit'] = $per_page;
            if($data['pages']==0){
            	$data['pages']=1;
            }
            $data['child_limit'] = $child_limit;
            $data['mentions']    = $this->get_users($topic_id);
            $data['forum_id']    = $forum_id;
            //$data['json_participants'] = $this->get_users($topic_id);
            $data['login']  = $this->auth->get_current_user_session('user');
            
            //echo '<pre>';print_r($data['posts']);die;
            $this->load->view($this->config->item('admin_folder').'/forum_reported', $data);
        }

    }

    public function create_forum_topic($forum_slug) {
        $admin = $this->auth->get_current_user_session('admin');
        if(!isset($admin)){
            $admin = $this->auth->get_current_user_session('teacher');
            if(!isset($admin)){
                $admin = $this->auth->get_current_user_session('content_editor');
            }
        }
        // create the data object
        $data = array();
        
        // set variables from the the URI
        $forum_slug = $this->uri->segment(3);
        $forum_id   = $this->Forum_model->get_forum_id_from_forum_slug($forum_slug);
        $forum      = $this->Forum_model->get_forum($forum_id);
        if(!isset($forum_id)){
            redirect(admin_url('forum'));
        }else{        
            // load form helper and validation library
            $this->load->helper('form');
            $this->load->library('form_validation');
            
            // set validation rules
            $this->form_validation->set_rules('title', 'Topic Title', 'trim|required|min_length[4]|max_length[100]');
            $this->form_validation->set_rules('content', 'Description', 'required|min_length[4]');
            $breadcrumb  = '<ul class="olp-breadcrumb">';
            $breadcrumb .= '<li class="olp-bread-lists"><a href="'.admin_url().'">Admin Home</a></li><li class="olp-bread-lists bread-arrow"><span> <i class="icon-right-open"></i></span></li>';
            $breadcrumb .= '<li class="olp-bread-lists"><a href="' . admin_url('forum') . '">Forum Index</a></li><li class="olp-bread-lists bread-arrow"><span> <i class="icon-right-open"></i></span></li>';
            $breadcrumb .= '<li class="olp-bread-lists"><a href="' . admin_url('forum/'.$forum->forum_slug) . '">'.(strlen($forum->forum_name) > 15 ? substr($forum->forum_name,0,12)."..." : $forum->forum_name).'</a></li><li class="olp-bread-lists bread-arrow"><span> <i class="icon-right-open"></i><li class="olp-bread-lists"><a href="javascript:void(0);" class="bread-active">New topic</a></li>';
            $breadcrumb .= '</ul>';
            $data['breadcrumb1'] = $breadcrumb;
            $data['forum_style'] = TRUE;
            if ($this->form_validation->run() === false) {
                
                // keep what the user has entered previously on fields
                $data['title']   = $this->input->post('title');
                $data['content'] = $this->input->post('content');
                
                // validation not ok, send validation errors to the view
                $this->load->view($this->config->item('admin_folder').'/forum_topic_create', $data);
                
            } else {
                
                // set variables from the form
                $title   = $this->input->post('title');
                $content = $this->input->post('content');
                $slug    = $this->Forum_model->validate_topic_slug(strtolower(url_title($this->input->post('title'))));
                $arg = array(
                        'topic_name'      => $title,
                        'topic_slug'       => $slug,
                        'user_id'    => $admin['id'],
                        'forum_id'   => $forum_id,
                        'topic_created' => date('Y-m-j H:i:s'),
                        'description'   => $content,
                        'topic_account_id' => $this->config->item('id')
                        );
                if ($this->Forum_model->create_topic($arg)) {
                    // topic creation ok
                    $this->Forum_model->forum_update($forum_id);
                    redirect(admin_url('forum/'.$forum_slug));
                    
                } else {
                    
                    // topic creation failed, this should never happen
                    $data->error = 'There was a problem creating your new topic. Please try again.';
                    
                    // send error to the view
                    $this->load->view($this->config->item('admin_folder').'/forum_topic_create', $data);
                    
                }
                
            }
        }
        
    }
    function delete_ajax_forum()
    {
        $response = array();
            $forum_id = $this->input->post('forum_id');
            $admin_id = $this->input->post('admin_id');
            $per_page = $this->input->post('per_page');
            if(!isset($forum_id)||$forum_id == ''){
                $response['success'] = false;
                $response['message'] = 'Invalid forum.';
            }else{
            	$admin = $this->auth->get_current_user_session('admin');
            	if(isset($admin)&&!empty($admin)){
            		$topics   = $this->Forum_model->get_forum_topics($forum_id,0,0);
	                foreach ($topics as $topic) {
	                    $this->Forum_model->delete_forum_topic_comments($topic->id);
	                }
	                $this->Forum_model->delete_forum_topics($forum_id);
	                $this->Forum_model->delete_forum($forum_id);

	                $total_row                  = $this->Forum_model->forum_count();
            		$num_pages = ceil($total_row/$per_page);
	                $response['success'] = true;
                	$response['message'] = 'Forum deleted successfully.';
                	$response['pages']   = $num_pages;
                	$response['forum_count']= $total_row;
            	}else{
            		$response['success'] = false;
                	$response['message'] = 'Required previlege missing.';
            	}
            }
            echo json_encode($response);

    }
    public function delete_ajax_topic() {
        
        // create the data object
        $response = array();
        $forum_id = $this->input->post('forum_id');
        $topic_id = $this->input->post('topic_id');
        $per_page = $this->input->post('per_page');
        $admin = $this->auth->get_current_user_session('admin');

        if(!isset($forum_id)||!isset($topic_id)){
            $response['success'] = false;
            $response['message'] = 'Invalid topic.';
        }else{
        	if(isset($admin)&&!empty($admin)){
	            $this->Forum_model->forum_update($forum_id);
	            $this->Forum_model->delete_forum_topic_comments($topic_id);
	            $this->Forum_model->delete_forum_topic($topic_id);
	            $total_row                  = $this->Forum_model->topic_count($forum_id);
	        	$num_pages = ceil($total_row/$per_page);
	            $response['success'] = true;
            	$response['message'] = 'Topic deleted successfully.';
            	$response['pages']   = $num_pages;
            	$response['topic_count']= $total_row;
        	}else{
	        	$response['success'] = false;
            	$response['message'] = 'Required previlege missing.';
        	}
        }

        echo json_encode($response);
    }

    public function delete_comment(){
    	$comment_id = $this->input->post('comment_id');
    	$parent_id  = $this->input->post('parent_id');
    	$child_limit = $this->input->post('child_limit');
        $forum_id = $this->input->post('forum_id');
        $topic_id = $this->input->post('topic_id');
        $per_page = $this->input->post('limit');
        $response = array();
        $posts    = array();
        $admin = $this->auth->get_current_user_session('admin');
        if(empty($comment_id)||$comment_id == ''){
        	$response['success'] = false;
            $response['message'] = 'Invalid comment.';
        }else{
        	if(isset($admin)&&!empty($admin)){
        		$child_comment_count = $this->Forum_model->get_child_comment_count($comment_id);
        		if(count($child_comment_count)>0){
		            $this->Forum_model->delete_forum_topic_child_comment($comment_id);
		        }
		        $this->Forum_model->delete_forum_topic_comment($comment_id);
	            $this->Forum_model->forum_update($forum_id);
	            $this->Forum_model->topic_update($topic_id);
	            $total_row                  = $this->Forum_model->post_count($topic_id);
	            $num_pages = ceil($total_row/$per_page);
	            if($parent_id != 0){
	            	$posts = $this->Forum_model->get_replies(array('comment_id'=>$parent_id,'limit'=>$per_page,'child_limit'=>$child_limit),0);

		            foreach ($posts as $parent_key => $post) {
		                    $role = $this->Forum_model->get_user_role_from_user_id($posts[$parent_key]['user_id']);
		                    $posts[$parent_key]['author_role'] = $this->Forum_model->get_user_role_from_role_id($role);
		                    $posts[$parent_key]['author_post_count'] = $this->Forum_model->get_user_post_count($posts[$parent_key]['user_id']);
		                    $posts[$parent_key]['comment_created'] = $this->get_time_comment($post['comment_created'],true);
		                foreach ($posts[$parent_key]['children'] as $child_key => $cpost) {
		                    $role = $this->Forum_model->get_user_role_from_user_id($cpost['user_id']);
		                    $posts[$parent_key]['children'][$child_key]['author_role'] = $this->Forum_model->get_user_role_from_role_id($role);
		                    $posts[$parent_key]['children'][$child_key]['author_post_count'] = $this->Forum_model->get_user_post_count($cpost['user_id']);
		                    $posts[$parent_key]['children'][$child_key]['comment_created'] = $this->get_time_comment($cpost['comment_created'],true);
		                }
		            }
	            }
	            $response['posts']   = $posts;
	            $response['success'] = true;
	        	$response['message'] = 'Comment deleted successfully.';
	        	$response['pages']   = $num_pages;
	        	$response['reply_count']= $total_row;
        	}else{
        		$response['success'] = false;
            	$response['message'] = 'Required previlege missing.';
        	}
        }
        echo json_encode($response);
    }

    function get_time($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        if($diff->y>=1){
            return date('D M d',strtotime($datetime)).', '.date('Y',strtotime($datetime)).' '.date('h:i a',strtotime($datetime));
        }else{
            if($diff->m>=1){
                return date('D M d',strtotime($datetime)).', '.date('Y',strtotime($datetime)).' '.date('h:i a',strtotime($datetime));
            }else{
                if($diff->d>=1||$diff->h>=12){
                    return date('D M d',strtotime($datetime)).', '.date('Y',strtotime($datetime)).' '.date('h:i a',strtotime($datetime));

                }else{
                    if($diff->h>=1){
                        return 'Today at '.date('h:i a',strtotime($datetime));
                    }else{
                        if($diff->i>1&&$diff->i<30){
                            return 'Few minutes ago...';
                        }else{
                            if($diff->s<50){
                                return 'Few seconds ago...';
                            }else{
                                return 'Just now...';
                            }
                        }
                    }
                }
            }
        }
    }

    function get_time_comment($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        if($diff->y>=1){
            return '<span class="cmt-post-date">'.date('M d',strtotime($datetime)).', '.date('Y',strtotime($datetime)).' '.'</span><span class="cmt-post-current-time">'.date('h:i a',strtotime($datetime)).'</span>';
        }else{
            if($diff->m>=1){
                return '<span class="cmt-post-date">'.date('M d',strtotime($datetime)).', '.date('Y',strtotime($datetime)).' '.'</span><span class="cmt-post-current-time">'.date('h:i a',strtotime($datetime)).'</span>';
            }else{
                if($diff->d>=1||$diff->h>=12){
                    return '<span class="cmt-post-date">'.date('M d',strtotime($datetime)).', '.date('Y',strtotime($datetime)).' '.'</span><span class="cmt-post-current-time">'.date('h:i a',strtotime($datetime)).'</span>';

                }else{
                    if($diff->h>=1){
                        return '<span class="cmt-post-date">Today</span><span class="cmt-post-current-time">'.date('h:i a',strtotime($datetime)).'</span>';
                    }else{
                        if($diff->i>1&&$diff->i<30){
                            return '<span class="cmt-post-current-time">Few minutes ago...</span>';
                        }else{
                            if($diff->s<50){
                                return '<span class="cmt-post-current-time">Few seconds ago...</span>';
                            }else{
                                return '<span class="cmt-post-current-time">Just now...</span>';
                            }
                        }
                    }
                }
            }
        }
    }

    

    public function get_users($topic_id){
        $result = $this->Forum_model->get_users_in_topic($topic_id);
        $users = array();
        $u_arr = array();
        $i=0;
        foreach ($result as $result_s) {
            $users[$i] = $result_s->user_id;
            $i++;
        }
        $users = array_unique($users);
        $users = array_values($users);
        //print_r($users);die;
        for($i=0;$i<count($users);$i++){
            $u_row = $this->Forum_model->get_user_cred($users[$i]);
            $u_arr[$i]['id'] = $u_row->id;
            $u_arr[$i]['name'] = $u_row->us_name;
            $u_arr[$i]['email'] = $u_row->id;
            $u_arr[$i]['img'] = (($u_row->us_image == 'default.jpg') ? default_user_path() : user_path()) . $u_row->us_image;

        }
        return $u_arr;
    }

    function get_ajax_childs(){
    	$offset = $this->input->post('offset');
        $limit  = $this->input->post('limit');
        $parent = $this->input->post('parent');

        $admin = $this->auth->get_current_user_session('user');
        $report_user_id = isset($admin['id'])?$admin['id']:0;

        $page                       = $offset;
        if($page===NULL||$page<=0)
        {
            $page                   = "1";
        }
        $page                       = ($page - 1)* $limit;

    	$childs = $this->Forum_model->get_child_replies(array('offset'=>$page,'limit'=>$limit,'order_by'=>'DESC'),$parent);

    	foreach($childs as $child_key => $child){
    		$role = $this->Forum_model->get_user_role_from_user_id($child['user_id']);
            $childs[$child_key]['author_role'] = $this->Forum_model->get_user_role_from_role_id($role);
            $childs[$child_key]['author_post_count'] = $this->Forum_model->get_user_post_count($child['user_id']);
            $childs[$child_key]['comment_created'] = $this->get_time_comment($child['comment_created'],true);
            $childs[$child_key]['report_status'] = $this->Forum_model->get_comment_stat(array('count'=>true,'user_id'=>$report_user_id,'comment_id'=>$child['id']));
    	}
    	$data['childs'] = $childs;
    	echo json_encode($data);
    }

    function get_ajax_parents(){
    	$offset = $this->input->post('offset');
        $limit  = $this->input->post('limit');
        $topic_id = $this->input->post('topic');
        $child_limit = $this->input->post('child_limit');
        $report_parent = $this->input->post('report_parent');

        $admin = $this->auth->get_current_user_session('user');
        $report_user_id = isset($admin['id'])?$admin['id']:0;

        $page 	= $offset;
        if($page===NULL)
        {
            $page                   = "1";
        }
        $page                       = ($page - 1)* $limit;
        $posts = $this->Forum_model->get_replies(array('report_parent'=>$report_parent,'child_limit'=>$child_limit,'limit'=>$limit,'offset'=>$page,'topic_id'=>$topic_id),0);
        //Pagination end


	    foreach ($posts as $parent_key => $post) {
		        $role = $this->Forum_model->get_user_role_from_user_id($posts[$parent_key]['user_id']);
		        $posts[$parent_key]['author_role'] = $this->Forum_model->get_user_role_from_role_id($role);
		        $posts[$parent_key]['author_post_count'] = $this->Forum_model->get_user_post_count($posts[$parent_key]['user_id']);
		        $posts[$parent_key]['comment_created'] = $this->get_time_comment($post['comment_created'],true);
                $posts[$parent_key]['report_status'] = $this->Forum_model->get_comment_stat(array('count'=>true,'user_id'=>$report_user_id,'comment_id'=>$post['id']));
		        //$posts[$parent_key]['created'] = $this->get_time($posts[$parent_key]['comment_created']);
		    foreach ($posts[$parent_key]['children'] as $child_key => $cpost) {
		        $role = $this->Forum_model->get_user_role_from_user_id($cpost['user_id']);
		        $posts[$parent_key]['children'][$child_key]['author_role'] = $this->Forum_model->get_user_role_from_role_id($role);
		        $posts[$parent_key]['children'][$child_key]['author_post_count'] = $this->Forum_model->get_user_post_count($cpost['user_id']);
		        $posts[$parent_key]['children'][$child_key]['comment_created'] = $this->get_time_comment($cpost['comment_created'],true);
                $posts[$parent_key]['children'][$child_key]['report_status'] = $this->Forum_model->get_comment_stat(array('count'=>true,'user_id'=>$report_user_id,'comment_id'=>$cpost['id']));
		    }
		}
    	$data['comments'] = $posts;
    	echo json_encode($data);
    }

    public function ajax_comment(){
        $user  = $this->auth->get_current_user_session('admin');
		if(!isset($user)){
			$user = $this->auth->get_current_user_session('teacher');
			if(!isset($user)){
			    $user = $this->auth->get_current_user_session('content_editor');
			}
		}
        $response = array();
        if(isset($user['id'])){
            $topic_id = $this->input->post('topic_id');
            $forum_id = $this->input->post('forum_id');
            $user_id = $user['id'];
            $comment = $this->input->post('comment');
            $parent_id = $this->input->post('parent_id');
            $url       = $this->input->post('url');
            $child_limit = $this->input->post('child_limit');
            $arr = array('topic_comment'=>$comment,'user_id'=>$user_id,'topic_id'=>$topic_id,'parent_id'=>$parent_id,'comment_created'=>date('Y-m-j H:i:s'));
            if($parent_id != 0){
                $this->Forum_model->update_comment($parent_id);
            }
            $row = $this->Forum_model->add_child_comment($arr);
            $this->Forum_model->forum_update($forum_id);
            $this->Forum_model->topic_update($topic_id);
            $limit = $this->input->post('limit');
            $limit = $limit==0?1:$limit;
            $child_limit = $child_limit==0?1:$child_limit;
            
            $posts = $this->Forum_model->get_replies(array('comment_id'=>$parent_id,'limit'=>$limit,'child_limit'=>$child_limit),0);

            foreach ($posts as $parent_key => $post) {
                    $role = $this->Forum_model->get_user_role_from_user_id($posts[$parent_key]['user_id']);
                    $posts[$parent_key]['author_role'] = $this->Forum_model->get_user_role_from_role_id($role);
                    $posts[$parent_key]['author_post_count'] = $this->Forum_model->get_user_post_count($posts[$parent_key]['user_id']);
                    $posts[$parent_key]['comment_created'] = $this->get_time_comment($post['comment_created'],true);
                    $posts[$parent_key]['report_status'] = $this->Forum_model->get_comment_stat(array('count'=>true,'user_id'=>$user['id'],'comment_id'=>$post['id']));
                foreach ($posts[$parent_key]['children'] as $child_key => $cpost) {
                    $role = $this->Forum_model->get_user_role_from_user_id($cpost['user_id']);
                    $posts[$parent_key]['children'][$child_key]['author_role'] = $this->Forum_model->get_user_role_from_role_id($role);
                    $posts[$parent_key]['children'][$child_key]['author_post_count'] = $this->Forum_model->get_user_post_count($cpost['user_id']);
                    $posts[$parent_key]['children'][$child_key]['comment_created'] = $this->get_time_comment($cpost['comment_created'],true);
                    $posts[$parent_key]['children'][$child_key]['report_status'] = $this->Forum_model->get_comment_stat(array('count'=>true,'user_id'=>$user['id'],'comment_id'=>$cpost['id']));
                }
            }
            $per_page                   = $this->input->post('limit');
            $topic_id                   = $this->input->post('topic_id');
            $total_row                  = $this->Forum_model->post_count($topic_id);
            $pages                      = ceil($total_row/$limit);
            $response['pages'] = $pages;
            $response['posts'] = $posts;
            $response['total_replies'] = $total_row;
            $response['success'] = true;
            $response['code']    = 200;
            $response['message'] = 'Successfull';
        }else{
            $response['success'] = false;
            $response['code']    = 401;
            $response['message'] = 'Unauthorized access.';
        }

        echo json_encode($response);
    }


}
?>
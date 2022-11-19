<?php
class Event extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Kolkata');
        $this->__loggedInUser   = $this->auth->get_current_user_session('admin');
        if (empty($this->__loggedInUser))
        {
            redirect('login');
        }

        $this->privilege        = array('view' => 1, 'add' => 2, 'edit' => 3, 'delete' => 4);
        $this->event_privilege  = $this->accesspermission->get_permission(array('role_id' => $this->__loggedInUser['role_id'],'module' => 'event'));   
        if (!in_array($this->privilege['view'], $this->event_privilege))
        {
            redirect(admin_url('dashboard'));
        }

        $this->actions              = $this->config->item('actions');
        if(isset($this->__loggedInUser['us_role_id']) && $this->__loggedInUser['us_role_id'] == 8)
        {
            $this->__role_query_filter['institute_id'] = $this->__loggedInUser['id'];                                
        }
        $this->load->model(array('Events_model','Course_model'));
        $this->load->library('ofabeeevents');
        $this->privilege         = array('view' => 1, 'add' => 2, 'edit' => 3, 'delete' => 4);
        $this->event_privilege   = $this->accesspermission->get_permission(array('role_id' => $this->__loggedInUser['role_id'],'module' => 'event'));
        $this->privilege = array('view' => 1, 'add' => 2, 'edit' => 3, 'delete' => 4);
        $this->limit             = 50;
    }
    
    function index()
    {
        $data                       = array();
        $breadcrumb                 = array();
        $breadcrumb[]               = array( 'label' => 'Home', 'link' => site_url('/admin'), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]               = array( 'label' => 'Events', 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']         = $breadcrumb;
        $data['title']              = 'Events';
        $offset                     = isset($_GET['offset'])?$_GET['offset']:0;


        $event_param               = array();
        $event_param['order_by']   = 'id';
        $event_param['direction']  = 'DESC';

        $filter                    = $this->input->get('filter');
        $event_param['filter']     = ( $filter )? $filter : 'active';

        $keyword                   = $this->input->get('keyword');
        if($keyword)
        {
            $keyword               = explode('-', $keyword);
            $keyword               = implode(' ',$keyword);
            $event_param['keyword']= $keyword;
        }

        $event_param['count']      = true;
        $data['total_events']      = $this->Events_model->getEventsAdmin($event_param);

        $event_param['limit']      = $this->limit;
        //processing pagination
        $page       = $offset;
        if($page===NULL||$page<=0)
        {
            $page   = 1;
        }
        $page       = ($page - 1)* $this->limit;
        //end

        $event_param['offset']     = $page;
        $event_param['select']     = 'id, ev_name, ev_status,ev_date';
        // echo '<pre>'; print_r($event_param);die;
        unset($event_param['count']);
        $data['events']            = $this->Events_model->getEventsAdmin($event_param);
        $data['limit']             = $this->limit;
        $instituteParams    = array();
        $objects            = array();
        $objects['key']     = 'institutes';
        $callback           = 'institutes';
        if(isset($this->__role_query_filter['institute_id'])){
            $instituteParams['institute_id'] = $this->__role_query_filter['institute_id'];

            $this->load->model('Institute_model');
            $select = 'id, ib_name, ib_institute_code';
            
            $data['institutes'] = $this->Institute_model->institute(array(
                'select' => $select,
                'id'    => $instituteParams['institute_id']
            ));

        }else{
        
            $data['institutes'] = $this->memcache->get($objects, $callback, $instituteParams); 
        }

        $data['courses']    = array();
        $objects            = array();
        $objects['key']     = 'all_courses';
        $callback           = 'all_courses';//8
        
        $courses            = $this->memcache->get($objects, $callback, $instituteParams); 
        //echo "<pre>";print_r($data['institutes']);exit;
        if(!empty($courses))
        {
            foreach($courses as $course)
            {
                if($course['item_type'] == 'course')
                {
                    $data['courses'][$course['id']] = array(
                        'id' => $course['id'],
                        'cb_title' => (isset($course['cb_title']))?$course['cb_title']:"",                                
                        'cb_code' => (isset($course['cb_code']))?$course['cb_code']:"",                                
                    );
                }
                
            }
        }        
        $data['studios'] = $this->Course_model->studios();
        $data['batches'] = $this->Events_model->institute_batches($instituteParams); 
        //print_r($data['batches']);die;
        $this->load->view($this->config->item('admin_folder').'/events', $data);
    }

    function language()
    {
        $response               = array();
        $response['language']   = array();
        $response['language']   = get_instance()->lang->language;
        echo json_encode($response);
    }


    function ajaxgetevents()
    {
        $data                      = array();
        $offset                    = $this->input->post('offset');
        $event_param               = array();
        $event_param['order_by']   = 'id';
        $event_param['direction']  = 'DESC';

        $filter                    = $this->input->post('filter');
        $event_param['filter']     = ( $filter )? $filter : 'active';

        $keyword                   = $this->input->post('keyword');
        if($keyword)
        {
            $keyword               = explode('-', $keyword);
            $keyword               = implode(' ',$keyword);
            $event_param['keyword']= $keyword;
        }

        $event_param['count']      = true;
        $data['total_events']      = $this->Events_model->getEventsAdmin($event_param);
        $event_param['limit']      = $this->limit;
        
        //processing pagination
        $page       = $offset;
        if($page===NULL||$page<=0)
        {
            $page   = 1;
        }
        $page       = ($page - 1)* $this->limit;
        //end

        $event_param['offset']     = $page;
        $event_param['select']     = 'id, ev_name, ev_status,ev_date';
        // echo '<pre>'; print_r($event_param);die;
        unset($event_param['count']);
        $data['events']            = $this->Events_model->getEventsAdmin($event_param);
        $data['limit']             = $this->limit;
        $data['success']           = true;
        echo json_encode($data);
    }

    function addnewevent()
    {
    	$response 		= array();
    	$event_name 	= $this->input->post('event_name');
        $event_desc     = $this->input->post('event_description');
        $event_date     = $this->input->post('event_date');
        $event_time     = $this->input->post('event_time');
        $event_type     = $this->input->post('event_type');
        $event_studio   = $this->input->post('event_studio');
       
        $save 		                  = array();
    	$save['ev_name'] 		      = $event_name;
        $save['ev_date']              = date('Y-m-d',strtotime($event_date));
        $save['ev_description']       = $event_desc;
        $save['ev_time']              = date('H:i:s',strtotime($event_time));
        $save['ev_type']		      = $event_type;
        $save['ev_studio_id']		  = ($event_studio=='')?0:$event_studio;
    	$save['ev_account']	          = config_item('id');
    	$save['ev_status']	          = '1';
    	$save['ev_deleted']	          = '0';
    	$save['action']		          = '1';
    	$save['action_by']	          = $this->__loggedInUser['id'];
    	$save['updated']		      = date('Y-m-d H:i:s');
        //echo json_encode($save);die;
    	$event_id = $this->Events_model->addEvent(array('values'=>$save));

        $user_data              = array();
        $user_data['user_id']   = $this->__loggedInUser['id'];
        $user_data['username']  = $this->__loggedInUser['us_name'];
        $user_data['useremail']  = $this->__loggedInUser['us_email'];
        $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
        
        $message_template                   = array();
        $message_template['username']       = $this->__loggedInUser['us_name'];;
        $message_template['event_name']     = $event_name;
        $message_template['action']         = 'added';
        $triggered_activity                 = "event";
        log_activity($triggered_activity, $user_data, $message_template);

    	$response['success'] 	= true;
		$response['message']	= 'Event created.';
		$response['event_id']	= $event_id;
		echo json_encode($response);
    }

    function basic($id = false)
    {
       
        if (!in_array($this->privilege['edit'], $this->event_privilege))
        {
            redirect(admin_url('event'));
        }

        if(!$id)
        {
            redirect(admin_url('event'));
        }

        $event                 = $this->Events_model->adminEvent(array('id'=>$id));
        if(empty($event))
        {
            redirect(admin_url('event'));
        }

        $event_id                   = $id;
        $data                       = array();
        $breadcrumb                 = array();
        $breadcrumb[]               = array( 'label' => 'Home', 'link' => site_url('/admin'), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]               = array( 'label' => 'Events', 'link' => admin_url('event'), 'active' => '', 'icon' => '' );
        $breadcrumb[]               = array( 'label' => $event['ev_name'], 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']         = $breadcrumb;
        $data['event']              = $event;
        $data['title']              = 'Event - '.$event['ev_name'];
        $data['studios']            = $this->Course_model->studios();
        $this->load->library('form_validation');
        $this->form_validation->set_rules('id', 'Event id', 'required');
        
        if ($this->form_validation->run() == FALSE)
        {
            $this->load->view($this->config->item('admin_folder').'/event', $data);
        }
        else
        {
            $event_name 	= $this->input->post('ev_name');
            $event_desc     = $this->input->post('ev_sdescription');
            $event_date     = $this->input->post('ev_date');
            $event_time     = $this->input->post('ev_time');
            $event_type     = $this->input->post('event_type');
            $studio_id      = $this->input->post('studio_id');
            $event_date     = strtotime(str_replace("-", "/", $event_date));
            
            $save 		                  = array();
            $save['ev_name'] 		      = $event_name;
            $save['ev_date']              = date('Y-m-d',$event_date);
            $save['ev_description']       = $event_desc;
            $save['ev_time']              = date('H:i:s',strtotime($event_time));
            $save['ev_type']		      = $event_type;
            $save['ev_studio_id']		  = $studio_id;
            $save['ev_account']	          = config_item('id');
            $save['ev_status']	          = '1';
            $save['ev_deleted']	          = '0';
            $save['action']		          = '1';
            $save['action_by']	          = $this->__loggedInUser['id'];
            $save['updated']		      = date('Y-m-d H:i:s');

            $user_data              = array();
            $user_data['user_id']   = $this->__loggedInUser['id'];
            $user_data['username']  = $this->__loggedInUser['us_name'];
            $user_data['useremail']  = $this->__loggedInUser['us_email'];
            $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
            
            $message_template                   = array();
            $message_template['username']       = $this->__loggedInUser['us_name'];;
            $message_template['event_name']     = $event_name;
            $message_template['action']         = 'updated';
            $triggered_activity                 = "event";
            log_activity($triggered_activity, $user_data, $message_template);
            //echo json_encode($save);die;
            $this->session->set_flashdata('message', 'Event details updated successfully');
            $event_id = $this->Events_model->addEvent(array('values'=>$save, 'id' => $id));
            redirect(admin_url('event/basic').$id);
        }
    }

    function change_status()
    {
        if (!in_array($this->privilege['edit'], $this->event_privilege))
        {
            $response['success']    = true;
            $response['message']    = 'Permission required to change status';
            echo json_encode($response);exit();
        }

        $response       = array();
        $event_id       = $this->input->post('event_id');
        $status         = $this->input->post('status');
        $event_name          = $this->input->post('event_name');


        if(!$event_id)
        {
            $response['success']    = false;
            $response['message']    = 'Event id missing';
            echo json_encode($response);exit();
        }
        $action = 'deactivated';
        if($status=='1'){
            $action = 'activated';
        }
        $event_update               = array();
        $event_update['updated']    = date('Y-m-d H:i:s');
        $event_update['action_by']  = $this->__loggedInUser['id'];
        $event_update['ev_status']  = $status;
        $this->Events_model->addEvent(array('id'=>$event_id,'values'=>$event_update));
        $event_response             = $this->Events_model->getEventsAdmin(array('id'=>$event_id));
        $response['error']          = false;
        $response['message']        = 'Event status changed successfully.'; 
        $response['event']          = $event_response;

        $user_data              = array();
        $user_data['user_id']   = $this->__loggedInUser['id'];
        $user_data['username']  = $this->__loggedInUser['us_name'];
        $user_data['useremail']  = $this->__loggedInUser['us_email'];
        $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
        
        $message_template                   = array();
        $message_template['username']       = $this->__loggedInUser['us_name'];;
        $message_template['event_name']     = $event_name;
        $message_template['action']         = $action;
        $triggered_activity                 = "event";
        log_activity($triggered_activity, $user_data, $message_template);
        echo json_encode($response);
    }

    function delete_event()
    {
        if (!in_array($this->privilege['delete'], $this->event_privilege))
        {
            $response['success']    = true;
            $response['message']    = 'Permission required to change status';
            echo json_encode($response);exit();
        }

        $response       = array();
        $event_id       = $this->input->post('event_id');
        $event_name     = $this->input->post('event_name');
        if(!$event_id)
        {
            $response['success']    = false;
            $response['message']    = 'Event id missing';
            echo json_encode($response);exit();
        }

        $user_data              = array();
        $user_data['user_id']   = $this->__loggedInUser['id'];
        $user_data['username']  = $this->__loggedInUser['us_name'];
        $user_data['useremail']  = $this->__loggedInUser['us_email'];
        $user_data['user_type'] = $this->__loggedInUser['us_role_id']; ;
        
        $message_template                   = array();
        $message_template['username']       = $this->__loggedInUser['us_name'];;
        $message_template['event_name']     = $event_name;
        $message_template['action']         = 'deleted';
        $triggered_activity                 = "event";
        log_activity($triggered_activity, $user_data, $message_template);

        $notify_param                   = array();
        $notify_param['event_id']       = $event_id;
        $notify_param['event_name']     = $event_name;
        $param                          = array();
        $param['data']                  = $notify_param;
        $param['url']                   = "cron_job/notify_event_deleted";
        $this->send_notifications($param);

        $response['error']    = false;
        $response['message']  = 'Event deleted successfully';
        echo json_encode($response);
    }

    function send_invitation()
    {
        $event_id           = $this->input->post('event_id');
        $invitation_type    = $this->input->post('invitation_type');
        $event_name         = $this->input->post('event_name');
        $event              = $this->Events_model->adminEvent(array('id'=>$event_id));
        if(empty($event))
        {
            $response               = array();
            $response['success']    = false;
            $response['message']    = 'Invalid event';
            echo json_encode($response);exit();
        }
        $notify_param                  = array();
        $notify_param['request_type']  = $invitation_type;
        $notify_param['event_id']      = $event_id;
        $notify_param['event_name']    = $event_name;

        $save       = array();
        $save['id'] = $event_id;
        switch($invitation_type)
        {
            case "course":
                $course_selected            = $this->input->post('course_selected');
                $course_selected            = ($course_selected)?$course_selected:array();
                $notify_param['request_id'] = $course_selected;
                $course_ids                 = ($event['ev_course_id'])?explode(',', $event['ev_course_id']):array();
                $save['ev_course_id']       = array_merge($course_ids, $course_selected);
                $save['ev_course_id']       = ($save['ev_course_id'])?implode(',', $save['ev_course_id']):'';
            break;
            case "institute":
                $institute_selected         = $this->input->post('institute_selected');
                $institute_selected         = ($institute_selected)?$institute_selected:array();
                $notify_param['request_id'] = $institute_selected;
                $institute_ids              = ($event['ev_institute_id'])?explode(',', $event['ev_institute_id']):array();
                $save['ev_institute_id']    = array_merge($institute_ids, $institute_selected);
                $save['ev_institute_id']    = ($save['ev_institute_id'])?implode(',', $save['ev_institute_id']):'';
            break;
            case "batch":
                $batch_selected             = $this->input->post('batch_selected');
                $batch_selected             = ($batch_selected)?$batch_selected:array();
                $notify_param['request_id'] = $batch_selected;
                $batch_ids                  = ($event['ev_batch_id'])?explode(',', $event['ev_batch_id']):array();
                $save['ev_batch_id']        = array_merge($batch_ids, $batch_selected);
                $save['ev_batch_id']        = ($save['ev_batch_id'])?implode(',', $save['ev_batch_id']):'';
            break;
        }
        
        $this->Events_model->addEvent(array('values'=>$save, 'id' => $event_id));
        $response               = array();
        $response['success']    = true;
        $response['message']    = 'Participants invited successfully';

        $param                  = array();
        $param['data']          = $notify_param;
        $param['url']           = "cron_job/notify_event";
        $this->send_notifications($param);
        echo json_encode($response);exit();
    }

    private function send_notifications($param){
        
        $send_param         = $param['data'];
        $send_url           = $param['url'];
        $curlHandle         = curl_init(site_url().$send_url);
        $defaultOptions     = array (
                                CURLOPT_POST => 1,
                                CURLOPT_POSTFIELDS => json_encode($send_param),
                                CURLOPT_RETURNTRANSFER => false ,
                                CURLOPT_TIMEOUT_MS => 100,
                             );
        curl_setopt_array($curlHandle , $defaultOptions);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, FALSE);     
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2); 
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array(
            'request-token: '.sha1(config_item('acct_domain').config_item('id')),
        ));
        $result = curl_exec($curlHandle);
        // echo "<pre>";print_r($result);exit;
        curl_close($curlHandle);
    }
}
<?php
class Tasks extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->lang->load('tasks');
        $this->__role_query_filter  = array();
        $this->__restrcited_method  = array('groups');
        $this->__admin_index        = 'admin';
        $this->__loggedInUser       = $this->auth->get_current_user_session('admin');
        $redirect                   = $this->auth->is_logged_in(false, false);
        if (!$redirect){            
            redirect('login');
        }
        $this->__limit = 10;
        $params                 = array();
        $params['role_id']      = $this->__loggedInUser['role_id'];
        $params['user_id']      = $this->__loggedInUser['id'];
        $params['module']       = 'tasks';
       
        $this->privilege = array('view' => 1, 'add' => 2, 'edit' => 3, 'delete' => 4);

        $this->userPrivilege              = $this->accesspermission->get_permission($params);
        
    }

    public function index()
    {
        $this->load->model(array('Faculty_model', 'Task_model'));
        $data                   = array();
        $breadcrumb             = array();
        $limit                  = $this->__limit;
        $offset                 = 0;
        $breadcrumb[]           = array('label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>');
        $breadcrumb[]           = array('label' => lang('manage_tasks'), 'link' => '', 'active' => 'active', 'icon' => '');
        $data['breadcrumb']     = $breadcrumb;
        $data['title']          = lang('manage_tasks');

        $this->load->model('User_model');
        $param                  = array();
        $param['select']        = 'users.id, us_name, us_email, us_image, us_phone';
        $param['status']        = '1';
        $param['role_ids_not']  = '2';
        $param['not_deleted']   = true;
        $param['order_by']      = 'id';
        $param['direction']     = 'DESC';
        $param['limit']         = 25;
        $faculties              = $this->User_model->get_users($param);

        $param                  = array();
        $param['not_deleted']   = true;
        $param['keyword']       = $this->input->get('keyword');
        $filter                 = $this->input->get('filter');
        $filter == 'deleted' ? $param['not_deleted']   = false : 1+1;
        $param['filter']        = $filter != 'deleted' ? $filter : '';
        $param['priority']      = $this->input->get('priority');
        if($this->__loggedInUser['role_id'] != '1')
        {
            $param['faculty_id']= $this->__loggedInUser['id'];
        }
        $tasks                  = $this->Task_model->tasks($param);
        $param['count']         = true;
        $total_tasks            = $this->Task_model->tasks($param);
        $data['total_tasks']    = $total_tasks;
        $data['limit']          = 100;
        $data['tasks']          = $tasks;
        $data['task_privilege'] = array();
        $data['faculties']      = json_encode($faculties);
        $data['lastTaskId']     = $this->Task_model->lastTask()['id'];
        $this->load->view($this->config->item('admin_folder') . '/tasks', $data);
    }

    function getAssignee()
    {
        $this->load->model('User_model');
        $param                  = array();
        $param['select']        = 'users.id, us_name, us_email, us_image, us_phone';
        $param['status']        = '1';
        $param['role_ids_not']  = '2';
        $param['not_deleted']   = true;
        $param['keyword']       = $this->input->get('keyword');
        $param['order_by']      = 'id';
        $param['direction']     = 'DESC';
        $faculties              = $this->User_model->get_users($param);
        header('Content-Type: application/json');
        echo json_encode($faculties);
    }

    function tasks_json(){
        $this->load->model(array('Faculty_model', 'Task_model'));


        $data                       = array();
        $data['show_load_button']   = false;
        $user_param                 = $this->__role_query_filter;
        
        $limit            = $this->__limit;
        $data['limit']    = $limit;
        $offset           = $this->input->post('offset');
        $page             = $offset;
        if($page===NULL||$page<=0)
        {
            $page                           = 1;
        }
        $page                               = ($page - 1)* $limit;
        $param                              = array();
        $user_param['order_by']             = 'id';
        $user_param['direction']            = 'DESC';
        $user_param['priority']             = $this->input->post('priority');
        $user_param['tasks_id']             = $this->input->post('tasks_id');
        
        
        $user_param['status']               = $this->input->post('status');
        $user_param['not_deleted']          = $this->input->post('not_deleted');
        $user_param['count']                = true;

        $param['not_deleted']               = true;
        if($this->__loggedInUser['role_id'] != '1')
        {
            $param['faculty_id']= $this->__loggedInUser['id'];
        }
        $param['keyword']                   = $this->input->post('keyword');
        $param['priority']                  = $this->input->post('priority');
        $filter                             = $this->input->post('filter');
        $filter == 'deleted' ? $param['not_deleted']   = false : 1+1;
        $param['filter']                    = $filter != 'deleted' ? $filter : '';
        $tasks                              = $this->Task_model->tasks($param);
        $param['count']                     = true;
        $total_tasks                        = $this->Task_model->tasks($param);
        
        $data['total_tasks']                = $total_tasks;
        $data['limit']                      = 100;
        $data['tasks']                      = $tasks;
        $data['task_privilege']             = array();
        echo json_encode($data);
    }

    function language()
    {
        $response                           = array();
        $response['language']               = array();
        $response['language']               = get_instance()->lang->language;
        echo json_encode($response);
    }
    
    function create_task()
    {
        $this->load->model(array('User_model', 'Task_model'));
        $param                              = array();
        $param['select']                    = 'users.id, us_name, us_email, us_image, us_phone';
        $param['status']                    = '1';
        $param['faculty_ids']               = $this->input->post('faculties');
        $param['not_deleted']               = true;

        $save                               = array();
        $save['id']                         = $this->input->post('taskId'); 
        $task_tittle                        = $this->input->post('task_tittle');
        $task_description                   = $this->input->post('task_description');
        $task_due_date                      = $this->input->post('task_due_date');
        $task_priority                      = $this->input->post('task_priority');
        
        if($this->input->post('faculties')){
            $faculty                        = implode(',',$this->input->post('faculties'));
        }

        if($this->input->post('assignee')){
            $faculty                        = $this->input->post('assignee');
            $param['id']                    = $faculty;
        }

        if($task_tittle)
        {
            $save['ft_tittle']              = $task_tittle;
        }

        if($task_description)
        {
            $save['ft_description']         = $task_description;
        }

        if($task_due_date)
        {
            $save['ft_due_date']            = $task_due_date;
        }

        if($task_priority)
        {
            $save['ft_priority']            = $task_priority;
        }

        if(isset($faculty) && $faculty)
        {
            $save['ft_assignees']           = $faculty;
            $faculties                      = $this->User_model->get_user_details($param);
            $save['ft_assignee_details']    = json_encode($faculties);
        }
        
        $save['ft_action_by']               = $this->__loggedInUser['id'];
        $save['ft_updated_at']              = date('Y-m-d H:i:s');

        if($lastTaskId = $this->Task_model->save($save))
        {
            echo json_encode(array('error' => false, 'message' => 'Task successfully created!', 'lastTaskId' => $lastTaskId));
        }
        else
        {
            echo json_encode(array('error' => true, 'message' => 'Task creation failed!'));
        }

    }

    function update_task()
    {
        $this->load->model(array('User_model', 'Task_model'));
        $param                              = array();
        $param['select']                    = 'users.id, us_name, us_email, us_image, us_phone';
        $param['status']                    = '1';
        $param['faculty_ids']               = $this->input->post('faculties');
        $param['not_deleted']               = true;

        $save                               = array();
        $save['id']                         = $this->input->post('taskId'); 
        $task_tittle                        = $this->input->post('task_tittle');
        $task_description                   = $this->input->post('task_description');
        $task_due_date                      = $this->input->post('task_due_date');
        $task_priority                      = $this->input->post('task_priority');
        
        if($this->input->post('faculties')){
            $faculty                        = implode(',',$this->input->post('faculties'));
        }

        if($this->input->post('assignee')){
            $faculty                        = $this->input->post('assignee');
            $param['id']                    = $faculty;
        }

        if($task_tittle)
        {
            $save['ft_tittle']              = $task_tittle;
        }

        if($task_description)
        {
            $save['ft_description']         = $task_description;
        }

        if($task_due_date)
        {
            $save['ft_due_date']            = $task_due_date;
        }

        if($task_priority)
        {
            $save['ft_priority']            = $task_priority;
        }

        if(isset($faculty) && $faculty)
        {
            $save['ft_assignees']           = $faculty;
            $faculties                      = $this->User_model->get_user_details($param);
            $save['ft_assignee_details']    = json_encode($faculties);
        }
        
        $save['ft_action_by']               = $this->__loggedInUser['id'];
        $save['ft_updated_at']              = date('Y-m-d H:i:s');

        if($this->Task_model->save($save))
        {
            echo json_encode(array('error' => false, 'message' => 'Task successfully updated!'));
        }
        else
        {
            echo json_encode(array('error' => true, 'message' => 'Task updation failed!'));
        }

    }

    public function getTask()
    {
        $this->load->model('Task_model');
        $param                  = array();
        $param['id']            = $this->input->post('taskId');
        $tasks                  = $this->Task_model->task($param);
        header('Content-Type: application/json');
        echo json_encode($tasks);
    }

    function delete(){
        $this->load->model('Task_model');
        $param                              = array();
        $param['id']                        = $this->input->post('taskId');
        $param['ft_deleted']                = '1';
        if($this->Task_model->save($param)){
            $response = array('error' => false, 'message' => 'Task has been deleted!');
        }
        else
        {
            $response = array('error' => true, 'message' => 'Failed to delete the task!');
        }
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    function restore()
    {
        $this->load->model('Task_model');
        $param                              = array();
        $param['id']                        = $this->input->post('taskId');
        $param['ft_deleted']                = '0';
        if($this->Task_model->save($param)){
            $response = array('error' => false, 'message' => 'Task has been restored!');
        }
        else
        {
            $response = array('error' => true, 'message' => 'Failed to restore the task!');
        }
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    function changeStatus()
    {
        $this->load->model('Task_model');
        $param                              = array();
        $param['id']                        = $this->input->post('taskId');
        $param['ft_status']                 = $this->input->post('status');
        if($this->Task_model->save($param)){
            $params                         = array();
            $params['id']                   = $param['id'];
            $tasks                          = $this->Task_model->task($params);
            $response = array('error' => false, 'message' => 'Task status has been updated!', 'tasks' => $tasks);
        }
        else
        {
            $response = array('error' => true, 'message' => 'Failed to change status!');
        }
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    function removeAssignee(){
        $this->load->model('Task_model');
        $param                              = array();
        $param['id']                        = $this->input->post('taskId');
        $param['ft_assignees']              = NULL;
        $param['ft_assignee_details']       = NULL;
        if($this->Task_model->save($param)){
            $params                         = array();
            $params['id']                   = $param['id'];
            $tasks                          = $this->Task_model->task($params);
            $response = array('error' => false, 'message' => 'The assigne has been removed from the task!', 'tasks' => $tasks);
        }
        else
        {
            $response = array('error' => true, 'message' => 'Failed to remove the assignee!');
        }
        header('Content-Type: application/json');
        echo json_encode($response);  
    }

    function changePriority()
    {
        $this->load->model('Task_model');
        $param                              = array();
        $param['id']                        = $this->input->post('taskId');
        $param['ft_priority']               = $this->input->post('priority');
        if($this->Task_model->save($param)){
            $params                         = array();
            $params['id']                   = $param['id'];
            $tasks                          = $this->Task_model->task($params);
            $response = array('error' => false, 'message' => 'Task Priority has been updated!', 'tasks' => $tasks);
        }
        else
        {
            $response = array('error' => true, 'message' => 'Failed to change the Priority!');
        }
        header('Content-Type: application/json');
        echo json_encode($response);
    }
    
}
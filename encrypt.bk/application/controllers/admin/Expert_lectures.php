<?php
class Expert_lectures extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $redirect	= $this->auth->is_logged_in(false, false);
        if (!$redirect)
        {
            $redirect   = true;
            $content_editor    = $this->auth->is_logged_in(false, false, 'content_editor');
            if($content_editor)
            {
                $redirect = false;
            }
            if($redirect)
            {
                redirect('login');
            }
        }
        $this->actions = $this->config->item('actions');
        $this->load->model(array('Expertlectures_model'));
        $this->lang->load('expert_lectures');
    }
    
    function index()
    {
        $data                       = array();
        $breadcrumb                 = array();
        $breadcrumb[]               = array( 'label' => 'Home', 'link' => site_url('/admin'), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]               = array( 'label' => 'CMS', 'link' => admin_url('page'), 'active' => 'active', 'icon' => '' );
        $breadcrumb[]               = array( 'label' => lang('manage_expert_lectures'), 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']         = $breadcrumb;
        $data['title']              = lang('expert_lectures');
        $data['expert_lectures']    = $this->Expertlectures_model->expert_lectures(array('direction'=>'DESC', 'order_by' => 'id', 'parent_id' => 0));
        //echo '<pre>'; print_r($data['recently_viewed']);die;
        $this->load->view($this->config->item('admin_folder').'/expert_lectures', $data);
    }
    
    function expert_lectures_json()
    {
        $data                       = array();
        $data['expert_lectures']    = $this->Expertlectures_model->expert_lectures(array('direction'=>'DESC', 'keyword'=>  $this->input->post('keyword'),  'filter'=>  $this->input->post('filter'), 'status'=>  $this->input->post('status'), 'not_deleted'=>  $this->input->post('not_deleted')));
        echo json_encode($data);
    }

    public function create_expert_lecture()
    {
        $response               = array();
        $response['error']      = false;
        $response['message']    = lang('expert_lecture_created_success');
                
        $expert_lecture_name = $this->input->post('expert_name');
        if( $expert_lecture_name == '')
        {
            $response['error']   = true;
            $response['message'] = lang('expert_name_required');
            echo json_encode($response);exit;
        }
        $expert_lecture = $this->Expertlectures_model->expert_lecture(array('name' => $expert_lecture_name));
        if( !empty($expert_lecture))
        {
            $response['error']   = true;
            $response['message'] = lang('expert_not_available');
            echo json_encode($response);exit;            
        }
        
        
        $save                   = array();
        $save['id']             = false;
        $save['el_title']       = $expert_lecture_name;
        $save['action_id']      = $this->actions['create']; 
        $save['action_by']      = $this->auth->get_current_admin('id');
        $save['el_account_id']  = $this->config->item('id');
        $expert_id              = $this->Expertlectures_model->save($save);     
        $response['id']         = $expert_id;
        echo json_encode($response);exit;
    }

    public function title_check($str)
    {
        if(strip_tags($str)=='')
        {
            $this->form_validation->set_message('title_check', 'The {field} field is invalid');
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    function basics($expert_lecture_id = false)
    {
        if(!$expert_lecture_id){
            redirect($this->config->item('admin_folder').'/expert_lectures');
        }
        $data               = array();
        $data['title']      = lang('expert_lectures');
        
        $this->load->helper('form');
        $this->load->library('form_validation');
        
        $data['id']                      = $expert_lecture_id;
        $data['el_title']                = strip_tags($this->input->post('el_title'));
        $data['el_url']                  = $this->input->post('el_url');
        $data['el_status']               = $this->input->post('el_status');
        
        //echo '<pre>'; print_r($data['terms']);die;
        if($expert_lecture_id)
        {
            $expert_lecture               = $this->Expertlectures_model->expert_lecture(array('id' => $expert_lecture_id));
            if(!$expert_lecture){
                redirect($this->config->item('admin_folder').'/expert_lectures');
            }
            
            if($expert_lecture['el_deleted'] == '1')
            {
                redirect($this->config->item('admin_folder').'/expert_lectures');
            }

            $data['id']                     = $expert_lecture['id'];
            $data['el_title']               = $expert_lecture['el_title'];
            $data['el_url']                 = $expert_lecture['el_url'];
            $data['el_image']               = $expert_lecture['el_image'];
            $data['el_thumbnail']           = $expert_lecture['el_thumbnail'];
            $data['el_status']              = $expert_lecture['el_status'];
        }
        $breadcrumb                 = array();
        $breadcrumb[]               = array( 'label' => 'Home', 'link' => site_url('/admin'), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]               = array( 'label' => 'CMS', 'link' => admin_url('page'), 'active' => 'active', 'icon' => '' );
        $breadcrumb[]               = array( 'label' => lang('manage_expert_lectures'), 'link' => admin_url('expert_lectures'), 'active' => 'active', 'icon' => '' );
        $breadcrumb[]               = array( 'label' => $expert_lecture['el_title'], 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']         = $breadcrumb;

        $this->form_validation->set_rules('el_title', 'Expert Lecture Title','required|trim|callback_title_check');
        $this->form_validation->set_rules('el_url', 'Expert Lecture URL','required');
        
        if ($this->form_validation->run() == FALSE)
        {
            $data['errors'] = validation_errors(); 
            $this->load->view($this->config->item('admin_folder').'/expert_lecture_basics',$data);
        }
       
        else
        {
            $save['id']                     = $expert_lecture_id;

           // $save['el_title']               = str_replace(array("\"", "&quot;", "<", ">", "{", "}"), "", htmlspecialchars($this->input->post('el_title')));
            //$save['el_title']               = ltrim($save['el_title']," ");

            $save['el_title']               = strip_tags(ltrim($this->input->post('el_title'), true));

            $save['el_url']                 = $this->input->post('el_url');
            $el_image                       = $this->input->post('el_image');
            $save['el_image']               = $el_image."?v=".rand(10,1000);
            $el_thumbnail                   = $this->input->post('el_thumbnail');
            $save['el_thumbnail']           = $el_thumbnail."?v=".rand(10,1000);
            $save['el_status']              = $this->input->post('el_status');
            $save['action_by']              = $this->auth->get_current_admin('id');
            $save['action_id']              = $this->actions['update'];
            $save['updated_date']           = date('Y-m-d H:i:s');
            //echo '<pre>';print_r($save);die;
            $this->Expertlectures_model->save($save);
            $this->session->set_flashdata('message', lang('expert_lecture_basics_saved'));
            redirect($this->config->item('admin_folder').'/expert_lectures/basics/'.$expert_lecture_id);
        }
    }

    function change_status()
    {
        $response               = array();
        $response['error']      = false;
        $expert_lecture_id      = $this->input->post('expert_lecture_id');
        $expert_lecture         = $this->Expertlectures_model->expert_lecture(array('id' => $expert_lecture_id));

        $save                   = array();
        $save['id']             = $expert_lecture_id;
        $save['action_by']      = $this->auth->get_current_admin('id');
        $save['updated_date']   = date('Y-m-d H:i:s');
        $save['el_status']       = '1';
        
        $save['action_id']      = $this->actions['activate'];
        $response['message']    = lang('activated');
                     
        $action_label   = $this->actions[$this->actions['activate']]['label'];
        $button_text    = lang('deactivate');

        $action_list    = '<a href="javascript:void(0);" data-target="#activate_expert_lecture" data-toggle="modal" onclick="changeVideoStatus(\''.$expert_lecture['id'].'\', \''. base64_encode(lang('are_you_sure_to').' '.lang('deactivate').' '.lang('expert_lecture').' - '.$expert_lecture['el_title'].'?').'\',\'\',\''.lang('deactivate').'\')">'.lang('deactivate').'</a>';
        if($expert_lecture['el_status'])
        {
            $action_list    = '<a href="javascript:void(0);" data-target="#activate_expert_lecture" data-toggle="modal" onclick="changeVideoStatus(\''.$expert_lecture['id'].'\', \''.base64_encode(lang('are_you_sure_to').' '.lang('activate').'  '.lang('expert_lecture').' - '.$expert_lecture['el_title'].'?').'\',\'\',\''.lang('activate').'\')">'.lang('activate').'</a>';
            $action_label   = $this->actions[$this->actions['deactivate']]['label'];
            $button_text    = lang('activate');
            
            $save['el_status']      = '0';
            $save['action_id']      = $this->actions['deactivate'];
            $response['message']    = lang('deactivated');
        }
        
        //set the database value
        $action_date    = date("d M Y", strtotime($save['updated_date']));
        $action_author  = $this->auth->get_current_admin('us_name');
        $action_author  = ($action_author)?$action_author:'Admin';

        //consider the record is deleted and set the value if record deleted
        $label_class    = 'spn-delete';
        $action_class   = 'label-danger';
        $action         = lang('deleted');
        //case if record is not deleted
        if($expert_lecture['el_deleted'] == 0)
        {
            if($expert_lecture['el_status'] == 0)
            {
                $action_class   = 'label-success';                                                                
                $label_class    = 'spn-active';                                        
                $action         = lang('active');
            }
            else
            {
                $action_class   = 'label-warning';                                                                
                $label_class    = 'spn-inactive';                                        
                $action         = lang('inactive');
            }
        }
        else
        {
            $action_label = $this->actions[$this->actions['delete']]['label'];
            unset($save['action_id']);
            unset($save['updated_date']);
            unset($save['action_by']);
        }
        
        if(!$this->Expertlectures_model->save($save))
        {
            $response['error']   = true;
            $response['message'] = lang('error_change_status');
        }

        $response['actions']                   = array();
        $response['actions']['action_label']   = $action_label;
        $response['actions']['action']         = $action;
        $response['actions']['status']         = ($expert_lecture['el_status']==1)?0:1;
        $response['actions']['deleted']        = $expert_lecture['el_deleted'];
        $response['actions']['action_date']    = $action_date;
        $response['actions']['action_author']  = $action_author;
        $response['actions']['label_class']    = $label_class;
        $response['actions']['action_class']   = $action_class;
        $response['actions']['button_text']    = $button_text;
        $response['actions']['label_text']     = $action_label.' by- '.$action_author.' on '.$action_date;
        
        $response['action_list'] = $action_list;        
        echo json_encode($response);
    }

    function change_status_bulk()
    {
        $status     = $this->input->post('status');
        $expert_lectures_ids   = json_decode($this->input->post('expert_lectures'));
        if(!empty($expert_lectures_ids))
        {
            foreach ($expert_lectures_ids as $expert_lecture_id) {
                $save                   = array();
                $save['id']             = $expert_lecture_id;
                $save['el_status']      = $status;
                $save['action_by']      = $this->auth->get_current_admin('id');
                $save['updated_date']   = date('Y-m-d H:i:s');
                $save['action_id']      = $this->actions['activate'];
                $this->Expertlectures_model->save($save);
            }
        }
        $data                       = array();
        $data['expert_lectures']    = $this->Expertlectures_model->expert_lectures(array('direction'=>'DESC', 'order_by' => 'id'));
        echo json_encode($data);
    }

    function delete()
    {
        $response               = array();
        $response['error']      = false;
        $expert_lecture_id      = $this->input->post('expert_lecture_id');
        $expert_lecture         = $this->Expertlectures_model->expert_lecture(array('id' => $expert_lecture_id));
        if( !$expert_lecture )
        {
            $response['error'] = true;
            $response['message'] = lang('no_expert_lectures_found');
            echo json_encode($response);exit;
        }
        $save                   = array();
        $save['id']             = $expert_lecture_id;
        $save['action_by']      = $this->auth->get_current_admin('id');
        $save['action_id']      = $this->actions['delete'];
        $save['updated_date']   = date('Y-m-d H:i:s');
        $save['el_deleted']      = '1';
        
        $response['message']    = lang('expert_delete_success');
        $action_label           = $this->actions[$this->actions['delete']]['label'];
        
        //set the database value
        $action_date    = date("d M Y", strtotime($save['updated_date']));
        $action_author  = $this->auth->get_current_admin('us_name');
        $action_author  = ($action_author)?$action_author:'Admin';
        
        
        
        if(!$this->Expertlectures_model->save($save))
        {
            $response['error']   = true;
            $response['message'] = lang('delete_expert_lecture_failed');
        }

        $response['actions']                   = array();
        $response['actions']['action']         = lang('deleted');
        $response['actions']['action_date']    = $action_date;
        $response['actions']['button_text']    = lang('restore');
        $response['actions']['label_text']     = $action_label.' by- '.$action_author.' on '.$action_date;
        
        $action_list  = '';
        $action_list .= '<li>';
        $action_list .= '     <a id="delete_btn" href="javascript:void(0);" data-target="#activate_expert_lecture" data-toggle="modal" onclick="restoreVideo(\''.$expert_lecture['id'].'\', \''. base64_encode(lang('are_you_sure_to').' '.lang('restore').' '.lang('expert_lecture').' - '.$expert_lecture['el_title'].'?').'\',\''.lang('restore').'\')">'.lang('restore').'</a>';
        $action_list .= '</li>';
        
        $response['action_list'] = $action_list;        
        echo json_encode($response);        
    }
    
    function delete_video_bulk()
    {
        $expert_lectures_ids   = json_decode($this->input->post('expert_lectures'));
        if(!empty($expert_lectures_ids))
        {
            foreach ($expert_lectures_ids as $expert_lectures_id) {
                $save                   = array();
                $save['id']             = $expert_lectures_id;
                $save['el_deleted']     = '1';
                $save['action_by']      = $this->auth->get_current_admin('id');
                $save['updated_date']   = date('Y-m-d H:i:s');
                $save['action_id']      = $this->actions['delete'];
                $this->Expertlectures_model->save($save);
            }
        }
        $data             = array();
        $data['expert_lectures']    = $this->Expertlectures_model->expert_lectures(array('direction'=>'DESC', 'order_by' => 'id'));
        echo json_encode($data);
    }

    function restore()
    {
        $response               = array();
        $response['error']      = false;
        $expert_lecture_id      = $this->input->post('expert_lecture_id');
        $expert_lecture         = $this->Expertlectures_model->expert_lecture(array('id' => $expert_lecture_id));

        $save                   = array();
        $save['id']             = $expert_lecture_id;
        $save['action_by']      = $this->auth->get_current_admin('id');
        $save['action_id']      = $this->actions['restore'];
        $save['updated_date']   = date('Y-m-d H:i:s');
        $save['el_deleted']     = '0';
        $save['el_status']      = '0';
        
        $response['message']    = lang('restore_video_success');
        $action_label           = $this->actions[$this->actions['restore']]['label'];
        
        //set the database value
        $action_date    = date("d M Y", strtotime($save['updated_date']));
        $action_author  = $this->auth->get_current_admin('us_name');
        $action_author  = ($action_author)?$action_author:'Admin';
        
        $button_text    = lang('activate');
        $action_class   = 'label-warning';                                                                
        $label_class    = 'spn-inactive';                                        
        $action         = lang('inactive');
            
        if(!$this->Expertlectures_model->save($save))
        {
            $response['error']   = true;
            $response['message'] = lang('restore_video_failed');
        }

        $response['actions']                   = array();
        $response['actions']['action_label']   = $action_label;
        $response['actions']['action']         = $action;
        $response['actions']['action_date']    = $action_date;
        $response['actions']['action_author']  = $action_author;
        $response['actions']['label_class']    = $label_class;
        $response['actions']['action_class']   = $action_class;
        $response['actions']['button_text']    = $button_text;
        $response['actions']['label_text']     = $action_label.' by- '.$action_author.' on '.$action_date;
        
        $cb_action = ($expert_lecture['el_status'])?'activate':'deactivate'; 
        $action_list  = '';

        $action_list .= '<li id="status_btn_'.$expert_lecture['id'].'">';
        $cb_status = 'activate';
        $cb_action = $cb_status;
        $action_list .= '    <a href="javascript:void(0);" data-toggle="modal" onclick="changeVideoStatus(\''.$expert_lecture['id'].'\', \''. base64_encode(lang('are_you_sure_to').' '.lang($cb_action).' '.lang('expert_lecture').' - '.$expert_lecture['el_title'].' ?').'\', \''.lang('change_status_message').' '.lang($cb_action).'\',\''.lang($cb_status).'\')" data-target="#activate_expert_lecture">'.lang($cb_status).'</a>';
        $action_list .= '</li>';
        $action_list .= '<li>';
        $action_list .= '    <a href="'.admin_url('expert_lectures/basics/').$expert_lecture['id'].'" >'.lang('settings').'</a>';
        $action_list .= '</li>';
        $action_list .= '<li>';
        $action_list .= '    <a href="javascript:void(0);" id="delete_btn_'.$expert_lecture['id'].'" data-toggle="modal" onclick="deleteVideo(\''.$expert_lecture['id'].'\', \''.base64_encode(lang('are_you_sure_to').' '.lang('delete').' - '.$expert_lecture['el_title'].' ?').'\',\''.lang('delete').'\')" data-target="#activate_expert_lecture">'.lang('delete').'</a>';
        $action_list .= '</li>';
        $response['action_list'] = $action_list;        
        echo json_encode($response);        
    }
    
    function language()
    {
        $response               = array();
        $response['language']   = array();
        $response['language']   = get_instance()->lang->language;
        echo json_encode($response);
    }
    
}
    
?>
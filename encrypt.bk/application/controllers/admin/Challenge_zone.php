<?php
class Challenge_zone extends CI_Controller
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
        $this->load->model(array('Category_model','Challenge_model','Course_model'));
        $this->lang->load('challenge_zone');
        
        $this->__question_types   = array('single' => '1', 'multiple' => '2', 'subjective' => '3');
        $this->__difficulty       = array('easy' => '1', 'medium' => '2', 'hard' => '3');
        $this->__single_type      = '1';
        $this->__multi_type       = '2';
        $this->__subjective_type  = '3';

    }
    
    function index()
    {
        $data                       = array();
        $breadcrumb                 = array();
        $breadcrumb[]               = array( 'label' => 'Home', 'link' => site_url('/admin'), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]               = array( 'label' => lang('manage_challenge'), 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']         = $breadcrumb;
        $data['title']              = lang('challenge_zones');
        $data['categories']         = $this->Category_model->categories(array('direction'=>'DESC','parent_id'=>'0', 'not_deleted'=>true));
        $data['challenges']         = $this->Challenge_model->challenges(array('direction'=>'DESC', 'order_by'=>'cz_start_date', 'not_deleted'=>true));

        //echo '<pre>'; print_r($data);die;
        $this->load->view($this->config->item('admin_folder').'/challenge_zones', $data);
    }
    
    function check_start_date(){

        $startdate      = $this->input->post('startdate');
        $catid          = $this->input->post('catid');
        $challenge_id   = $this->input->post('challenge_id');
        $data           = array();
        $error_count    = 0;
        $data['msg']    = '';
        if($startdate == '' || $catid == '' || $catid == 0){
            if($startdate==''){
                $error_count++;
                $data['msg'] .= 'please enter start date <br />';
                $data['stat'] = 0;
            }
            if($catid=='' || $catid == 0){
                $error_count++;
                $data['msg'] .= 'please select a '+lang('challenge_zones')+' category <br />';
                $data['stat'] = 0;
            }

        }
        else{

                $datearr   = explode('/', $startdate);
                $newdate   = $datearr[2].'-'.$datearr[0].'-'.$datearr[1];
                
                $stat      = $this->Challenge_model->check_start_date(array('newdate' => $newdate, 'category_id' => $catid, 'challenge_id' => $challenge_id, 'account_id' => $this->config->item('id')));
                if($stat){
                    $data['stat'] = $stat;
                    $data['msg']  = '';
                }
                else{
                    $data['stat'] = $stat;
                    $data['msg']  = 'Date Already Assigned, Select Another Date';
                }
        }
        echo json_encode($data);       
    }
    
    function get_question_category_list()
    { 
        $data 	 		= array();
        $keyword 		= $this->input->post('q_category');
        $category               = $this->input->post('challenge_category');
        $categories		= $this->Category_model->question_categories(array('name'=>$keyword, 'category'=>$category));
        
        //echo "<pre>";print_r($categories);die;
        $data['tags'] 	= array();
        if( sizeof($categories))
        {
            foreach( $categories as $category)
            {
                $category['name'] = $category['qc_category_name'];
                $data['tags'][]   = $category;
            }
        }
        echo json_encode($data);
    }
    
    function save_challenge_detail()
    {
        $response           = array();
        $response['error']  = 'false';
        $response['message']= '';
        
        $save               = array();
        if(trim(strip_tags($this->input->post('challenge_name'))) == '')
        {
            $response['error'] = 'true';;
            $response['message'] = 'Invalid title';
        }
        if( $response['error'] == 'true' )
        {
            echo json_encode($response);exit;            
        }
        
        $save                                       = array();
        $save['id']                                 = $this->input->post('challenge_id');
        $save['cz_title']                    = strip_tags($this->input->post('challenge_name'));
        $save['cz_instructions']             = $this->input->post('challenge_instruction');
        $save['cz_category']   = $this->input->post('challenge_category');
        $save['cz_duration']                  = $this->input->post('challenge_duration');
        $save['cz_show_categories']    = $this->input->post('show_categories');
        $save['cz_show_categories'] = ($save['cz_show_categories'])?'1':'0';
        //echo '<pre>'; print_r($this->input->post('show_categories'));die;
        $save_start_date                = date("Y-m-d", strtotime($this->input->post('challenge_start_date')));
        $save_start_time                = date("H:i:s", strtotime($this->input->post('challenge_start_time')));
        $save_start_date_time           = $save_start_date." ".$save_start_time;
        $save['cz_start_date']          = $save_start_date_time;
        
        $save_end_date                  = date("Y-m-d", strtotime($this->input->post('challenge_end_date')));
        $save_end_time                  = date("H:i:s", strtotime($this->input->post('challenge_end_time')));
        $save_end_date_time             = $save_end_date." ".$save_end_time;
        $save['cz_end_date']            = $save_end_date_time;
        $save['cz_account_id']          = $this->config->item('id');
        $save['action_by']                          = $this->auth->get_current_admin('id');
        //$save['action_id']                          = $this->actions['create'];
        
        //echo "<pre>";print_r($save);die;
        
        $this->Challenge_model->save($save);

        $response['error']          = 'false';
        $response['message']        = "Challenge saved successfully";
        $response['id']             = $save['id'];
        echo json_encode($response);exit;            
    }
    
    private function get_instruction()
    {
        return '<div id="dvInstruction">
            <p class="headings-altr"><strong>General Instructions:</strong></p>
            <ol class="header-child-alt">
            <li>The clock has been set at the server and the countdown timer at the top right corner of your screen will display the time remaining for you to complete the exam. When the clock runs out the exam ends by default - you are not required to end or submit your exam.</li>
            <li>The question palette at the right of screen shows one of the following statuses of each of the questions numbered:
            <table style="margin-bottom: 3px;"><tbody>
            <tr><td>You have not visited the question yet. ( In White Color )</td>
            <td style="padding-left: 7px;"><div class="gray" style="width: 20px;height: 20px;border-radius: 4px;"></div></td></tr>
            </tbody>
            </table>
            <table style="margin-bottom: 3px;"><tbody>
            <tr><td>You have not answered the question. ( In Red Color )</td>
            <td style="padding-left: 7px;"><div class="red" style="width: 20px;height: 20px;border-radius: 4px;"></div></td>
            </tr>
            </tbody>
            </table>
            <table style="margin-bottom: 3px;"><tbody>
            <tr><td>You have answered the question. ( In Green Color )</td><td style="padding-left: 7px;"><div class="green" style="width: 20px;height: 20px;border-radius: 4px;"></div></td>
            </tr>
            </tbody>
            </table>
            <table style="margin-bottom: 3px;"><tbody>
            <tr><td>You have marked the for review.( In Pink Color ) </td><td style="padding-left: 7px;"><div class="purpal" style="width: 20px;height: 20px;border-radius: 4px;"></div></td>
            </tr>
            </tbody>
            </table>
            </li>
            <li>&nbsp;</li>
            <li>The Marked for Review status simply acts as a reminder that you have set to look at the question again. <em>If an answer is selected for a question that is Marked for Review, the answer will be considered in the final evaluation.</em></li>
            </ol>
            <p class="headings-altr"><strong>Navigating to a question :</strong></p>
            <ol start="5" class="header-child-alt">
            <li>To select a question to answer, you can do one of the following:
            <ol type="a">
            <li>Click on the question number on the question palette at the right of your screen to go to that numbered question directly. Note that using this option does NOT save your answer to the current question.</li>
            <li>Click on Save and Next to save answer to current question and to go to the next question in sequence.</li>
            <li>Click on Mark for Review and Next to save answer to current question, mark it for review, and to go to the next question in sequence.</li>
            </ol>
            </li>
            <li>You can view the entire paper by clicking on the <strong>Question Paper</strong> button.</li>
            </ol>
            <p class="headings-altr"><strong>Answering questions :</strong></p>
            <ol start="7" class="header-child-alt">
            <li>For multiple choice type question :
            <ol type="a">
            <li>To select your answer, click on one of the option buttons</li>
            <li>To change your answer, click the another desired option button</li>
            <li>To save your answer, you MUST click on <strong>Save & Next</strong></li>
            <li>To deselect a chosen answer, click on the chosen option again or click on the <strong>Clear Response</strong> button.</li>
            <li>To mark a question for review click on <strong>Mark for Review & Next</strong>.&nbsp;</li>
            </ol>
            </li>
            <li>For a numerical answer type question
            <ol type="a">
            <li>To enter a number as your answer, use the virtual numerical keypad</li>
            <li>A fraction (eg. 0.4 or -0.4) can be entered as an answer ONLY with \'0\' before the decimal point</li>
            <li>To save your answer, you MUST click on <strong>Save & Next</strong></li>
            <li>To clear your answer, click on the<strong> Clear Response </strong>button</li>
            </ol>
            </li>
            <li>To change an answer to a question, first select the question and then click on the new answer option followed by a click on the <strong>Save & Next</strong> button.</li>
            <li>Questions that are saved or marked for review after answering will ONLY be considered for evaluation.</li>
            </ol>
            <p class="headings-altr"><strong>Navigating through sections :</strong></p>
            <ol start="11" class="header-child-alt">
            <li>Sections in this question paper are displayed on the top bar of the screen. Questions in a section can be viewed by clicking on the section name. The section you are currently viewing is highlighted.</li>
            <li>After clicking the <strong>Save & Next</strong> button on the last question for a section, you will automatically be taken to the first question of the next section.</li>
            <li>You can move the mouse cursor over the section names to view the status of the questions for that section.</li>
            <li>You can shuffle between sections and questions anytime during the examination as per your convenience.</li>
            </ol></div>';
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

    
    function basics($id = false)
    {
        $data                           = array();
        $this->load->helper('form');
        $this->load->library('form_validation');
        $data['id']                     = '';
        $data['cz_title']               = strip_tags($this->input->post('cz_title'));
        $data['cz_instructions']        = $this->input->post('cz_instructions');
        $data['cz_category']            = $this->input->post('cz_category');
        $data['cz_start_date']          = $this->input->post('cz_start_date');
        $data['cz_end_date']            = $this->input->post('cz_end_date');
        $data['cz_start_time']          = $this->input->post('cz_start_time');
        $data['cz_end_time']            = $this->input->post('cz_end_time');
        $data['cz_duration']            = $this->input->post('cz_duration');

        if($id)
        {
            $challenge                  = $this->Challenge_model->challenge(array('id' => $id));
            if(!$challenge){
                redirect($this->config->item('admin_folder').'/challenge_zone');
            }
            
            $data['id']                 = $challenge['id'];
            $data['challenge_id']       = $challenge['id'];
            $data['cz_title']           = $challenge['cz_title'];
            $data['cz_instructions']    = $challenge['cz_instructions'];
            $data['cz_category']        = $challenge['cz_category'];
            $data['cz_show_categories']        = $challenge['cz_show_categories'];
            $data['categories']         = $this->Category_model->categories(array('direction'=>'DESC','parent_id'=>'0','not_deleted'=>true));
            $start_date_time            = explode(" ", $challenge['cz_start_date']);
            $start_date                 = date('m/d/Y', strtotime($start_date_time[0]));
            $start_time                 = date('h:i A', strtotime($start_date_time[1]));
            $start_time_session         = date('A', strtotime($start_date_time[1]));
            $data['cz_start_date']      = $start_date;
            $data['cz_start_time']      = $start_time;
            $data['cz_stime_session']   = $start_time_session;
            $end_date_time              = explode(" ", $challenge['cz_end_date']);
            $end_date                   = date('m/d/Y', strtotime($end_date_time[0]));
            $end_time                   = date('h:i A', strtotime($end_date_time[1]));
            $end_time_session           = date('A', strtotime($end_date_time[1]));
            $data['cz_end_date']        = $end_date;
            $data['cz_end_time']        = $end_time;
            $data['cz_etime_session']   = $end_time_session;
            $data['cz_duration']        = $challenge['cz_duration'];
            
            $data['questions']          = $this->Challenge_model->questions(array('challenge_id' => $challenge['id'], 'not_deleted' =>'1'));
        }else{
            redirect($this->config->item('admin_folder').'/challenge_zone');
        }
        
        $this->form_validation->set_rules('cz_title',lang('challenge_zones')+' Title','required|trim|callback_title_check');
        
        if ($this->form_validation->run() == FALSE)
        {
            //echo "<pre>";print_r($data);die;
            $data['errors'] = validation_errors(); 
            $this->load->view($this->config->item('admin_folder').'/challenge_zone',$data);
        }
       
        else
        {
            $save['id']                     = $id;

            //$save['cz_title']               = str_replace(array("\"", "&quot;", "<", ">", "{", "}"), "", strip_tags($this->input->post('cz_title')));
            //$save['cz_title']               = ltrim($save['cz_title']," ");
            $save['cz_title']               = strip_tags($this->input->post('cz_title'));

            //$save['cz_title']               = ltrim($this->input->post('cz_title')," ");

            $save['cz_instructions']        = $this->input->post('cz_instructions');
            $save['cz_category']            = $this->input->post('cz_category');
            
            $start_date                     = $this->input->post('start_date');
            $start_time                     = $this->input->post('start_time');
            $end_date                       = $this->input->post('end_date');
            $end_time                       = $this->input->post('end_time');
            
            $save_start_date                = date("Y-m-d", strtotime($start_date));
            $save_start_time                = date("H:i:s", strtotime($start_time));
            $save_start_date_time           = $save_start_date." ".$save_start_time;
            $save['cz_start_date']          = $save_start_date_time;
            
            $save_end_date                  = date("Y-m-d", strtotime($end_date));
            $save_end_time                  = date("H:i:s", strtotime($end_time));
            $save_end_date_time             = $save_end_date." ".$save_end_time;
            $save['cz_end_date']            = $save_end_date_time;
            
            $save['cz_duration']            = $this->input->post('cz_duration');
            $save['action_by']              = $this->auth->get_current_admin('id');
            $save['action_id']              = $this->actions['update'];
            $save['updated_date']           = $updated_date;

            $this->Challenge_model->save($save);
            $this->session->set_flashdata('message', lang('challenge_saved'));
            redirect($this->config->item('admin_folder').'/challenge_zone/basics/'.$id);
        }
        //echo '<pre>';print_r($data);die;
    }
    
    public function question($id=false, $challenge_id=false)
    {
        if(!$challenge_id)
        {
            redirect(admin_url('challenge_zone'));
        }
        
        $this->load->library('form_validation');
        $data                       = array();
        $data['title']              = lang('question_form');
        $data['challenge_id']       = $challenge_id;
        $challenge                  = $this->Challenge_model->challenge(array('direction'=>'DESC', 'id'=>  $challenge_id));
        $data['challenge']          = $challenge;
        
        $data['cz_id']              = $challenge['id'];
        $data['cz_title']           = $challenge['cz_title'];
        $data['cz_instructions']    = $challenge['cz_instructions'];
        $data['cz_category']        = $challenge['cz_category'];
        $data['categories']         = $this->Category_model->categories(array('direction'=>'DESC','parent_id'=>'0'));
        $start_date_time            = explode(" ", $challenge['cz_start_date']);
        $start_date                 = date('m/d/Y', strtotime($start_date_time[0]));
        $start_time                 = date('h:i A', strtotime($start_date_time[1]));
        $start_time_session         = date('A', strtotime($start_date_time[1]));
        $data['cz_start_date']      = $start_date;
        $data['cz_start_time']      = $start_time;
        $data['cz_stime_session']   = $start_time_session;
        $end_date_time              = explode(" ", $challenge['cz_end_date']);
        $end_date                   = date('m/d/Y', strtotime($end_date_time[0]));
        $end_time                   = date('h:i A', strtotime($end_date_time[1]));
        $end_time_session           = date('A', strtotime($end_date_time[1]));
        $data['cz_end_date']        = $end_date;
        $data['cz_end_time']        = $end_time;
        $data['cz_etime_session']   = $end_time_session;
        $data['cz_duration']        = $challenge['cz_duration'];
        
        $data['id']                 = $id;
        $data['q_type']             = $this->input->post('q_type');
        $data['q_difficulty']       = $this->input->post('q_difficulty');
        $data['q_category']         = $this->input->post('q_category');
        $data['q_positive_mark']    = $this->input->post('q_positive_mark');
        $data['q_negative_mark']    = $this->input->post('q_negative_mark');
        $data['q_directions']       = $this->input->post('q_directions');
        $data['q_question']         = $this->input->post('q_question');
        $data['q_explanation']      = $this->input->post('q_explanation');
        $data['q_options']          = $this->input->post('q_options');
        $data['q_answer']           = $this->input->post('q_answer');
        $data['q_status']           = $this->input->post('q_status');
        $data['action_by']          = $this->auth->get_current_admin('id');
        $data['action_id']          = $this->actions['create'];
        $data['created_date']       = date('Y-m-d H:i:s');
        $data['updated_date']       = date('Y-m-d H:i:s');

        if($id)
        {
            $question = $this->Challenge_model->question(array('id' => $id));
            if(!$question)
            {
                redirect(admin_url('challenge_zone'));
            }
            $data['q_type']             = $question['q_type'];
            $data['q_difficulty']       = $question['q_difficulty'];
            
            $question_category_name     = $this->Category_model->question_category(array('id'=>$question['q_category']));
            $data['q_category']         = $question_category_name['qc_category_name'];
            
            $data['q_positive_mark']    = $question['q_positive_mark'];
            $data['q_negative_mark']    = $question['q_negative_mark'];
            $data['q_directions']       = $question['q_directions'];
            $data['q_question']         = $question['q_question'];
            $data['q_explanation']      = $question['q_explanation'];
            $data['q_options']          = $this->Challenge_model->options(array('q_answer' => $question['q_options']));
            $data['q_answer']           = ($question['q_answer'])?explode(',', $question['q_answer']):array();
            $data['q_status']           = $question['q_status'];
            $data['action_by']          = $this->auth->get_current_admin('id');
            $data['action_id']          = $this->actions['create'];
            $data['updated_date']       = date('Y-m-d H:i:s');
            //echo '<pre>' ; print_r($data['q_options']);die;
        }
        $this->form_validation->set_rules('q_question', lang('question'), 'required');

        if ($this->form_validation->run() == false)
        {
            //echo "<pre>";print_r($data);die;
            $data['error'] = validation_errors();
            $this->load->view($this->config->item('admin_folder').'/challenge_question_form', $data);        
        }
        else 
        {
            //echo "<pre>";print_r($this->input->post());die;
            $data                       = array();
            $data['id']                 = $id;
            $data['q_type']             = $this->input->post('q_type');
            $data['q_difficulty']       = $this->input->post('q_difficulty');
            
            $category                   = $this->Category_model->question_category(array('category_name'=>$this->input->post('q_category')));
            if(!$category)
            {
                $category                   = array();
                $category['id']             = false;
                if($this->input->post('q_category') != '')
                {
                    $category['qc_category_name']   = $this->input->post('q_category');
                    $category['qc_status']          = '1';
                    $category['qc_account_id']      = $this->config->item('id');
                    $category['action_by']          = $this->auth->get_current_admin('id');
                    $category['action_id']          = $this->actions['create'];
                    $category['id']                 = $this->Category_model->save_question_category($category);
                }
            } 
            $data['q_category']         = $category['id'];
            
            $data['q_positive_mark']    = $this->input->post('q_positive_mark');
            $data['q_negative_mark']    = $this->input->post('q_negative_mark');
            $data['q_directions']       = $this->input->post('q_directions');
            $data['q_question']         = $this->input->post('q_question');
            $data['q_explanation']      = $this->input->post('q_explanation');
            $data['q_status']           = $this->input->post('q_status');
            $data['q_status']           = ($data['q_status'])?$data['q_status']:0;
            $data['action_by']          = $this->auth->get_current_admin('id');
            $data['action_id']          = $this->actions['create'];
            $data['created_date']       = date('Y-m-d H:i:s');
            $data['updated_date']       = date('Y-m-d H:i:s');

            $data['q_options']          = '';
            $data['q_answer']           = '';
            //=============================processing question objects======================
            
            /*remove the deleted options*/
            $removed_options = json_decode($this->input->post('removed_options'));
            if( !empty($removed_options))
            {
                foreach ($removed_options as $option) {
                    $this->Challenge_model->delete_option($option);
                }
            }
            /*end*/

            /* update the existing options*/
            $options         = $this->input->post('option');
            $recieved_answer = $this->input->post('answer');
            $q_options       = array();
            $q_answer        = array();
            
			switch($data['q_type'])
			{
				case $this->__single_type:
					if( !empty($options))
					{
						foreach ($options as $op_id => $value ) {
							$save               = array();
							$save['id']         = $op_id;
							$save['qo_options'] = $value;
							$this->Challenge_model->save_option($save);
							$q_options[]        = $op_id;
							if($op_id == $recieved_answer)
							{
								$q_answer[] = $op_id;
							}
						}
					}
				break;
				case $this->__multi_type:
					if( !empty($options))
					{
						foreach ($options as $op_id => $value ) {
							$save               = array();
							$save['id']         = $op_id;
							$save['qo_options'] = $value;
							$this->Challenge_model->save_option($save);
							$q_options[]        = $op_id;
							if(in_array($op_id, $recieved_answer))
							{
								$q_answer[] = $op_id;
							}
						}
					}
				break;
				case $this->__subjective_type:
				if(!empty($data['q_options']))
				{
					foreach ($data['q_options'] as $option) 
					{
						$this->Challenge_model->delete_option($option['id']);
					}					
				}
				break;
			}
            /*End*/
            
            /* insert the new options*/
            $options         = $this->input->post('option_new');
            $recieved_answer = $this->input->post('answer_new');
			switch($data['q_type'])
			{
				case $this->__single_type:
					if( !empty($options))
					{
						foreach ($options as $op_id => $value ) {
							$save               = array();
							$save['id']         = false;
							$save['qo_options'] = $value;
							$option_id          = $this->Challenge_model->save_option($save);
							$q_options[]        = $option_id;
							if($op_id == $recieved_answer)
							{
								$q_answer[] = $option_id;
							}
						}
					}
				break;
				case $this->__multi_type:
					if( !empty($options))
					{
						foreach ($options as $op_id => $value ) {
							$save               = array();
							$save['id']         = false;
							$save['qo_options'] = $value;
							$option_id          = $this->Challenge_model->save_option($save);
							$q_options[]        = $option_id;
							if(in_array($op_id, $recieved_answer))
							{
								$q_answer[] = $option_id;
							}
						}
					}
				break;
				case $this->__subjective_type:
				break;
			}
            /*End*/
            //=====================================End===================================
            $data['q_options']          = implode(',', $q_options);
            $data['q_answer']           = implode(',', $q_answer);
            $question_id                = $this->Challenge_model->save_question($data);
            //saving assesment and questio nconnection
            $this->Challenge_model->save_challenge_question(array('challenge_id' => $challenge_id, 'question_id' => $question_id));
            //End
            
            //echo "<pre>"; print_r($data);die;
            
            redirect(admin_url('challenge_zone/question/'.$question_id.'/'.$challenge_id));
        }
    }
    
    function upload_question()
    {

        $directory                  = question_upload_path();
        $this->make_directory($directory);
        $config                     = array();
        $config['upload_path']      = $directory;
        $config['allowed_types']    = "*";      
        $config['encrypt_name']     = true;
        $this->load->library('upload');
        $this->upload->initialize($config);
        $uploaded = $this->upload->do_upload('file');   
        if($uploaded)
        {
            $upload_data  	  =  $this->upload->data();
            //echo '<pre>'; print_r($upload_data);die;
            if($upload_data['file_ext'] == '.csv')
            {
                $this->upload_question_csv($upload_data);
            }
            else
            {
                $this->upload_question_doc($upload_data);
            }
            $response['message'] = lang('file_upload_success');
            $response['error']   = 'false';
        }
        else
        {
            $response['message'] = lang('file_upload_failed');
            $response['error']   = 'true';
        }
        
        echo json_encode($response);
    }
    
    public function upload_question_doc($upload_data = false)
    {
        $response        		= array();
        $response['message']	= lang('file_imported_success');
        $response['error']   	= 'false';
		
        $challenge_id    = $this->input->post('challenge_id');
        $challenge       = $this->Challenge_model->challenge(array('challenge_id' => $challenge_id));
        
        
        $full_path    	= $upload_data['full_path'];
        $extract_path 	= $upload_data['file_path'].$upload_data['raw_name'].'/';
        //$full_path      = FCPATH.'uploads\question\localhost\e4e4ed866e184493ad147fc975be5f9f.zip';
        //$extract_path   = FCPATH.'uploads\question\localhost\e4e4ed866e184493ad147fc975be5f9f';
        
        $html_file      = '';
        $command = 'libreoffice --headless --convert-to html '.$full_path.' --outdir '.$extract_path.'';
        shell_exec($command);
        $html_file      = $extract_path.'/'.$upload_data['raw_name'].'.html';
        
        /*
	//creating directory. this will be created only if it is not yet created
        $directory      = question_upload_path();
        $this->make_directory($directory);
	//End
        //Unzp the file to the above directory
        $zip 			= new ZipArchive;
        if ($zip->open($full_path) === TRUE)
        {
            $zip->extractTo($extract_path);
            $zip->close();
        }
        //end
		
        //Find the name of html file 
	$extacted_files = scandir($extract_path);
        $html_file      = '';
        foreach ($extacted_files as $file)
        {
            if(strpos($file,'.htm'))
            {
                $html_file = $extract_path.'/'.$file;
            }
        }
        //End
        */
        //echo '<pre>'; print_r($upload_data);die;
        /*        
        Array
        (
            [file_name] => 081f67b84f81484b7b47e932874ce6b2.zip
            [file_type] => application/zip
            [file_path] => /var/www/html/ofabeeversion3/uploads/question/localhost/
            [full_path] => /var/www/html/ofabeeversion3/uploads/question/localhost/081f67b84f81484b7b47e932874ce6b2.zip
            [raw_name] => 081f67b84f81484b7b47e932874ce6b2
            [orig_name] => e4e4ed866e184493ad147fc975be5f9f.zip
            [client_name] => e4e4ed866e184493ad147fc975be5f9f.zip
            [file_ext] => .zip
            [file_size] => 14.81
            [is_image] => 
            [image_width] => 
            [image_height] => 
            [image_type] => 
            [image_size_str] => 
        )
        */
        
        libxml_use_internal_errors(true);
        $html   = ($html_file);
        $doc    = new DOMDocument;
        $doc->loadHTMLFile($html);
        $columns   		= $doc->getElementsByTagName('td');
        $this->doc_objects      = array();
	$column_count 		= 1;//this will be incremented in each loop. this is used to identify the loop is odd loop OR even loop
        $this->question_number  = 0;//for $this->doc_objects index
        $this->mechanism 	= 1;//ths is pointed to $method array. this will be increment in each loop(even loop only)
        $methods   		= array(
                                            1 => 'sl_no', 2 => 'set_question_type', 3 => 'set_difficulty',
                                            4 => 'set_direction', 5 => 'set_question', 6 => 'set_options',
                                            7 => 'set_answer', 8 => 'set_positive_mark', 9 => 'set_negative_mark',
                                            10 => 'set_catagory', 11 => 'set_explanation'
                        		);
        $end_of_option          = false;
        $subjective 		= false;
        $image_count            = 0;
	//$upload_data['raw_name'] = 'e4e4ed866e184493ad147fc975be5f9f';
        foreach ($columns as $column)
        {
            $even_column = 	(($column_count%2) == 0)?true:false;
            // setting image path 
            $find_image =  $column->getElementsByTagName('img'); 
            
            foreach($find_image as $image)
            {
                $imageName = question_upload_path().$upload_data['raw_name'].'/image_'.($image_count).'.jpg';
                $imagePath = question_path().$upload_data['raw_name'].'/image_'.($image_count).'.jpg';
                
                $imageData = $image->getAttribute('src'); 
                $imageData = explode('base64,', $imageData);
                $imageData = isset($imageData[1])?$imageData[1]:'';
                $imageData = base64_decode($imageData); 

                $imageFile = fopen($imageName, "w");
                fwrite($imageFile, $imageData);
                fclose($imageFile);

                $image->setAttribute('src', $imagePath);
                $image_count++;
            } 
			
            //removing style and class      
            $find_p =  $column->getElementsByTagName('p'); 
            foreach($find_p as $p)
            {
                $p->removeAttribute('class');
                $p->removeAttribute('style');
            } 
			//save html      
            $column_html = trim($doc->saveXML($column));   
			
            //check the end of the question piece
            if( strtolower($this->trim_doc_objects($column_html)) == 'sl_no')
            {
                $this->sl_no();//reset the variables
            }
			//confirms the option is over
            if( strtolower($this->trim_doc_objects($column_html)) == 'answer')
            {
                /*
                 * switch to method set_answer. this is because the variable $this->mechanism is 
                 * reseted to 5, when its value is 6. this is to save all the option in option array.
                 * once all the option issaved then we swict to answer
                 */
                $this->mechanism = 7;
            }
			
            //checking whether the question isd subjecvtive
            if( strtolower($this->trim_doc_objects($column_html)) == 'subjective' )
            {
                    $subjective = true;
            }
            
            //echo "<pre>";print_r($this->$methods[$this->mechanism]);die;
			
            if(isset($methods[$this->mechanism]) && $this->mechanism > 0 && $even_column == true)
            {
		//call coresponding method to set the values in array $this->doc_objects
                $current_method = $methods[$this->mechanism];
                $this->$current_method($column_html);
				
                /*
                 * Basically subjective question dont have option. So in this case, when we reach 5(set_question)
                 * we skip method set_option and set_answer
                 */
                if($subjective==true && $this->mechanism==5)
                {
                        $this->mechanism = 7;
                        $subjective = false;
                }
				
                //recursing methos to set the option
                if($this->mechanism==6)
                {
                        $this->mechanism = 5;
                }
				
	        $this->mechanism++;
            }
			
            $column_count++;
        }
        //echo '<pre>'; print_r($this->doc_objects);die('===');
        
        //inserting question
        foreach ($this->doc_objects as $question)
        {
            
            $q_options       = array();
            $q_answer        = array();
            
            //preparing the values for first question
            $question_object                       = array();
            $question_object['id']                 = false;
            $question_object['q_type']             = $question['q_type'];
            $question_object['q_difficulty']       = $question['q_difficulty'];
            $question_object['q_positive_mark']    = $question['q_positive_mark'];
            $question_object['q_negative_mark']    = $question['q_negative_mark'];
            $question_object['q_directions']       = $question['q_directions'];
            $question_object['q_question']         = $question['q_question'];
            $question_object['q_explanation']      = $question['q_explanation'];
            
			
            //processing options
            /* insert the new options*/
            switch($question_object['q_type'])
            {
                case $this->__single_type:
                    $options            = $question['q_option'];
                    $recieved_answer 	= $this->parse_answer_key(trim($question['q_answer']));
                    if( !empty($options))
                    {
                        foreach ($options as $op_id => $value ) 
                        {
                            //$trimmed_value      = preg_replace('/\s+/', '', strip_tags($value));
                            $trimmed_value      = strip_tags($value,"<img>");
                            if( $trimmed_value != '' || (intval($op_id+1) == $recieved_answer))
                            {
                                $save               = array();
                                $save['id']         = false;
                                $save['qo_options'] = $value;
                                $option_id          = $this->Challenge_model->save_option($save);
                                $q_options[]        = $option_id;
                            }
                            if(intval($op_id+1) == $recieved_answer)
                            {
                                $q_answer[] = $option_id;
                            }
                        }
                    }
                    break;
                    
                    case $this->__multi_type:
                        $options            = $question['q_option'];
                        $recieved_answer    = explode(',', $question['q_answer']);
                        if(!empty($recieved_answer))
                        {
                                foreach($recieved_answer as $key => $value)
                                {
                                        $recieved_answer[$key] = $this->parse_answer_key($value);
                                }
                        }
                        if( !empty($options))
                        {
                            foreach ($options as $op_id => $value ) 
                            {
                                //$trimmed_value      = preg_replace('/\s+/', '', strip_tags($value));
                                $trimmed_value      = strip_tags($value,"<img>");
                                if( $trimmed_value != '' || (in_array(intval($op_id+1), $recieved_answer)))
                                {
                                    $save               = array();
                                    $save['id']         = false;
                                    $save['qo_options'] = $value;
                                    $option_id          = $this->Challenge_model->save_option($save);
                                    $q_options[]        = $option_id;
                                }
                                if(in_array(intval($op_id+1), $recieved_answer))
                                {
                                        $q_answer[] = $option_id;
                                }
                            }
                        }
                    break;
                    
                    case $this->__subjective_type:
                    break;
            }
            /*End*/

            $question_object['q_category']         = $this->check_question_category($this->trim_text_custom($question['q_category']));
            $question_object['q_options']          = implode(',', $q_options);
            $question_object['q_answer']           = implode(',', $q_answer);
            $question_id                           = $this->Challenge_model->save_question($question_object);

            $this->Challenge_model->save_challenge_question(array('challenge_id' => $challenge_id, 'question_id' => $question_id));
            //end
           // print_r($question_object);
        }
        //End
    }
    
    private function trim_text_custom($words)
    {
        $peices = explode(PHP_EOL, $words);
        if(sizeof($peices) <= 1)
        {
            $peices = explode(' ', $words);    
        }
        $peices_tmp = array();
        if(!empty($peices))
        {
            foreach ($peices as $peice)
            {
                $peice = trim($peice);
                if($peice)
                {
                    $peices_tmp[] = $peice;        
                }
            }
        }
        $peices_tmp = implode(' ', $peices_tmp);
        return $peices_tmp;
    }
    
    private function check_question_category($category_name)
    {
        if(!isset($this->q_categories[$category_name]))
        {
            $category = $this->Category_model->question_category(array('category_name' => $category_name));
            if(!$category)
            {
                $save                       = array();
                $save['id']                 = false;
                $save['qc_category_name']   = $category_name;
                $save['qc_status']          = '1';
                $save['action_id']          = '1';
                $save['action_by']          = $this->auth->get_current_admin('id');
                $save['updated_date']       = date('Y-m-d H:i:s');
                $category['id']             = $this->Category_model->save_question_category($save);
                //echo '<pre>'; print_r($save);die;
            }
            $this->q_categories[$category_name] = $category['id']; 
        }
        return $this->q_categories[$category_name];
    }
    
    private function make_directory($path=false)
    {
        if(!$path )
        {
            return false;
        }
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }
    
    private function parse_answer_key($key='A')
    {
            $parser = array('A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5, 'F' => 6, 'G' => 7, 'H' => 8, 'I' => 9, 'J' => 10, 'K' => 11, 'L' => 12, 'M' => 13, 'N' => 14, 'O' => 15, 'P' => 16, 'Q' => 17, 'R' => 18, 'S' => 19, 'T' => 20, 'U' => 21, 'V' => 22, 'W' => 23, 'X' => 24, 'Y' => 25, 'Z' => 26);
            return isset($parser[$key])?$parser[$key]:false;
    }
    
    private function getTextBetweenTags($html)
    {
        $position1      = strpos($html, '>')+1;
        $position2      = strrpos($html, "</td>");
        $html_temp      = substr($html, $position1, ($position2-$position1));
        return $html_temp;
    }
    
    private function sl_no()
    {
        $this->mechanism = 1;
        $this->question_number++;
    }
	
    private function set_question_type($row_html)
    {
        $temp_html  	= $this->getTextBetweenTags($row_html);
        $line       	= $this->trim_doc_objects($temp_html);
	$question_types = array( 'single_choice' =>  'single', 'multiple_choice' =>  'multiple', 'subjective' =>  'subjective', );
        $question_mode  = isset($question_types[$line])?$question_types[$line]:'subjective';
        $this->doc_objects[$this->question_number]['q_type'] = $this->__question_types[$question_mode];
    }

    private function set_difficulty($row_html)
    {
        $temp_html  = $this->getTextBetweenTags($row_html);
        $line       = $this->trim_doc_objects($temp_html);
        $this->doc_objects[$this->question_number]['q_difficulty'] = isset($this->__difficulty[$line])?$this->__difficulty[$line]:$this->__difficulty['easy'];
    }
    
    private function set_positive_mark($row_html)
    {
        $temp_html  = $this->getTextBetweenTags($row_html);
        $line       = $this->trim_doc_objects($temp_html);
        $this->doc_objects[$this->question_number]['q_positive_mark'] = $line;
    }
    
    private function set_negative_mark($row_html)
    {
        $temp_html  = $this->getTextBetweenTags($row_html);
        $line       = $this->trim_doc_objects($temp_html);
        $this->doc_objects[$this->question_number]['q_negative_mark'] = $line;
    }
    
    private function set_direction($row_html)
    {
        $temp_html = $this->getTextBetweenTags($row_html);
        $this->doc_objects[$this->question_number]['q_directions'] = $temp_html;
    }
    
    private function set_question($row_html)
    {
        $temp_html = $this->getTextBetweenTags($row_html);
        $this->doc_objects[$this->question_number]['q_question'] = $temp_html;        
    }

    private function set_explanation($row_html)
    {
        $temp_html = $this->getTextBetweenTags($row_html);
        $this->doc_objects[$this->question_number]['q_explanation'] = $temp_html;                
    }
    
    private function set_answer($row_html)
    {
        $temp_html  = $this->getTextBetweenTags($row_html);
        $line       = strtoupper($this->trim_doc_objects($temp_html));
        $this->doc_objects[$this->question_number]['q_answer'] = $line;   
    }
    
    private function set_options($row_html)
    {
        $temp_html = $this->getTextBetweenTags($row_html);
        $this->doc_objects[$this->question_number]['q_option'][] = $temp_html;                
    }
    
    private function set_catagory($row_html)
    {
        $temp_html  = $this->getTextBetweenTags($row_html);
        $line       = $this->trim_doc_objects($temp_html, false, false);
        $this->doc_objects[$this->question_number]['q_category'] = $line;
    }
    
    private function trim_doc_objects($string, $string_to_lower=true, $replace_space = true)
    {
        $string_temp = $string;
        $string_temp = trim($string_temp);
        $string_temp = strip_tags($string_temp);
        if($replace_space)
        {
            $string_temp = str_replace(' ', '', $string_temp);        
        }
        $string_temp = str_replace('&#13;', '', $string_temp);
        $string_temp = trim($string_temp);        
        if($string_to_lower)
        {
            $string_temp = strtolower($string_temp);        
        }
        return $string_temp;
    }
    
    private function upload_question_csv()
    {
        $response               = array();
        $response['message']    = lang('file_imported_success');
        $response['error']      = 'false';
        
        $challenge_id           = $this->input->post('challenge_id');
        $challenge              = $this->Challenge_model->challenge(array('challenge_id' => $challenge_id));

        $file   = $upload_data['full_path'];
        $file   = fopen($file, "r") or die("Unable to open file!");
        $header = fgetcsv($file);
        //creating dynamic variable user the header name
        foreach ($header as $key => $value) {
            $$value = $key;
        }
        /*
         * Output after the above loop
        $question_type      = "0";
        $difficulty         = "1";
        $positive_mark      = "2";
        $negative_mark      = "3";
        $direction          = "4";
        $question           = "5";
        $explanation        = "6";
        $option             = "7";
        $answer             = "8";
        */
        //end of creating dynamic name

        while (($line = fgetcsv($file)) !== FALSE) {
        $q_options       = array();
        $q_answer        = array();
            $line[$option] = explode('{#}', $line[$option]);
            //preparing the values for first question
            //print_r($line);
            $question_object                       = array();
            $question_object['id']                 = false;
            $question_object['q_type']             = $this->__question_types[$line[$question_type]];
            $question_object['q_difficulty']       = $this->__difficulty[$line[$difficulty]];
            $question_object['q_positive_mark']    = $line[$positive_mark];
            $question_object['q_negative_mark']    = $line[$negative_mark];
            $question_object['q_directions']       = $line[$direction];
            $question_object['q_question']         = $line[$question];
            $question_object['q_explanation']      = $line[$explanation];
            //processing options
            $options    = $line[$option];
            //print_r($options);

            /* insert the new options*/
            $options         = $line[$option];
            if( $question_object['q_type'] == $this->__single_type)
            {
                $recieved_answer = trim($line[$answer]);
                if( !empty($options))
                {
                    foreach ($options as $op_id => $value ) {
                        $save               = array();
                        $save['id']         = false;
                        $save['qo_options'] = $value;
                        $option_id          = $this->Challenge_model->save_option($save);
                        $q_options[]        = $option_id;
                        if(intval($op_id+1) == $recieved_answer)
                        {
                            $q_answer[] = $option_id;
                        }
                    }
                }
            }
            else
            {
                $recieved_answer = explode(',', $line[$answer]);
                if( !empty($options))
                {
                    foreach ($options as $op_id => $value ) {
                        $save               = array();
                        $save['id']         = false;
                        $save['qo_options'] = $value;
                        $option_id          = $this->Challenge_model->save_option($save);
                        $q_options[]        = $option_id;
                        if(in_array(intval($op_id+1), $recieved_answer))
                        {
                            $q_answer[] = $option_id;
                        }
                    }
                }
            }
            /*End*/

            $question_object['q_options']          = implode(',', $q_options);
            $question_object['q_answer']           = implode(',', $q_answer);
            $question_id                           = $this->Challenge_model->save_question($question_object);

            $this->Challenge_model->save_challenge_question(array('challenge_id' => $challenge_id, 'question_id' => $question_id));
            //end
           // print_r($question_object);
        }
        fclose($file);
    }
    
    function delete_challenge_question()
    {
        $response               = array();
        $response['error']      = 'false';
        $response['message']    = lang('question_deleted_success');
        $question_id            = $this->input->post('question_id');
        $challenge_id           = $this->input->post('challenge_id');
        
        
        if(!$this->Challenge_model->delete_challenge_question(array('czq_question_id' => $question_id, 'czq_challenge_zone_id' => $challenge_id)))
        {
            $response['error']      = 'true';
            $response['message']    = lang('question_deleted_failed');    
        }
        $challenge_question_count = $this->Challenge_model->questions(array('challenge_id'=>$challenge_id, 'not_deleted'=>true, 'count'=>true));
        $response['question_count'] = $challenge_question_count;
        if($challenge_question_count == 0){
            $save_challenge = array();
            $save_challenge['id'] = $challenge_id;
            $save_challenge['cz_status'] = '0';
            $save_challenge['action_id'] = $this->actions['deactivate'];
            $save_challenge['action_by'] = $this->auth->get_current_admin('id');
            $save_challenge_zone = $this->Challenge_model->save($save_challenge);
        }
        echo json_encode($response);
    }
    
    function challenge_json()
    {
        $data               = array();
        $data['challenges'] = $this->Challenge_model->challenges(array('direction'=>'DESC', 'order_by'=>'cz_start_date', 'category_id'=>  $this->input->post('category_id'), 'deleted'=>'0'));
        echo json_encode($data);
    }
    
    function change_status()
    {
        $response               = array();
        $response['error']      = false;
        $challenge_id           = $this->input->post('challenge_id');
        $challenge              = $this->Challenge_model->challenge(array('id' => $challenge_id));
        
        $challenge_question     = $this->Challenge_model->questions(array('challenge_id'=>$challenge_id,'not_deleted'=>true, 'count'=>true));
        
        
        
        if($challenge_question == 0){
            $response['error'] = true;
            $response['message'] = lang('challenge_add_questions');
            echo json_encode($response);exit;
        }
        if( !$challenge )
        {
            $response['error'] = true;
            $response['message'] = lang('challenge_not_found');
            echo json_encode($response);exit;
        }
        $save                   = array();
        $save['id']             = $challenge_id;
        $save['action_by']      = $this->auth->get_current_admin('id');
        $save['action_id']      = $this->actions['activate'];
        $save['updated_date']   = date('Y-m-d H:i:s');
        $save['cz_status']      = '1';
        
        $response['message']    = lang('challenge_activate_success');  
        
        $action_date    = date("d M Y", strtotime($save['updated_date']));
        $action_author  = $this->auth->get_current_admin('us_name');
        $action_author  = ($action_author)?$action_author:'Admin';
        
        $response['action_date'] = $action_date;
        $response['action_author'] = $action_author;
        
        if(!$this->Challenge_model->save($save))
        {
            $response['error']   = true;
            $response['message'] = lang('activate_challenge_failed');
        }    
        
        if(date("Y-m-d", strtotime($challenge['cz_start_date'])) == date("Y-m-d"))
        {
            //send notification to admin
            $subscribed_users = $this->Challenge_model->category_subsciption_users(array('category_id'=>$challenge['cz_category']));
            if(!empty($subscribed_users))
            {
                $param              = array();
                $param['ids']       = array();
                $mailer             = array();
                $mailer['to']       = array();
                foreach($subscribed_users as $subscribed_user)
                {
                    $param['ids'][]  = $subscribed_user['cs_user_id'];
                    if($subscribed_user['us_email'])
                    {
                        $mailer['to'][]  = $subscribed_user['us_email'];                    
                    }
                }
                $challenge_name = $challenge['cz_title'];
                $start_date     = $challenge['cz_start_date'];
                $notify_challenge_name  = (strlen($challenge_name)>50)?substr($challenge_name, 0, 47).'...':$challenge_name;
                $challenge_zone_time    = date("F j, Y", strtotime($start_date)).' '.date("g:i a", strtotime($start_date));

                $mailer['from']         = $this->config->item('site_email');
                $mailer['subject']      = 'A new challenge zone "'.$challenge_name.'" has been added';
                $mailer['body']         = 'Hi,<br/> Greetings from "'.$this->config->item('site_name').'".<br/>A new challenge zone <b>"'.$notify_challenge_name.'"</b> has been added on <b>"'.$challenge_zone_time.'"</b>. Please click <a href="'.site_url('material/challenge/'.$challenge_id).'">here</a> to attend this ONLINE TEST.';
                $this->ofabeemailer->send_mail($mailer);            
            }
            //End            
        }
            
        echo json_encode($response);
    }
    
    function delete()
    {
        $response               = array();
        $response['error']      = false;
        $challenge_id           = $this->input->post('challenge_id');
        $challenge              = $this->Challenge_model->challenge(array('id' => $challenge_id));
        if( !$challenge )
        {
            $response['error'] = true;
            $response['message'] = lang('challenge_not_found');
            echo json_encode($response);exit;
        }
        $save                   = array();
        $save['id']             = $challenge_id;
        $save['action_by']      = $this->auth->get_current_admin('id');
        $save['action_id']      = $this->actions['delete'];
        $save['updated_date']   = date('Y-m-d H:i:s');
        $save['cz_deleted']     = '1';
        
        $response['message']    = lang('challenge_delete_success');       
        
        if(!$this->Challenge_model->save($save))
        {
            $response['error']   = true;
            $response['message'] = lang('delete_challenge_failed');
        }      
        echo json_encode($response);        
    }
    
    function delete_challenge_bulk()
    {
        $challenge_ids   = json_decode($this->input->post('challenges'));
        if(!empty($challenge_ids))
        {
            foreach ($challenge_ids as $challenge_id) {
                $save                   = array();
                $save['id']             = $challenge_id;
                $save['cz_deleted']     = '1';
                $save['action_by']      = $this->auth->get_current_admin('id');
                $save['updated_date']   = date('Y-m-d H:i:s');
                $save['action_id']      = $this->actions['delete'];
                $this->Challenge_model->save($save);
            }
        }
        $data               = array();
        $data['challenges'] = $this->Challenge_model->challenges(array('direction'=>'DESC', 'order_by'=>'cz_start_date', 'category_id'=>  $this->input->post('category_id'), 'deleted'=>'0'));
        echo json_encode($data);
    }
    
    function create_challenge()
    {
        $challenge_name           = $this->input->post('challenge_name');
        $challenge_category       = $this->input->post('challenge_category');
        $challenge_description    = $this->input->post('challenge_description');
        $challenge_duration       = $this->input->post('challenge_duration');
        $start_date               = $this->input->post('start_date');
        $start_time               = $this->input->post('start_time');
        $end_date                 = $this->input->post('end_date');
        $end_time                 = $this->input->post('end_time');
        
        $save                   = array();
        $save['id']             = false;
        $save['cz_title']       = $challenge_name;
        $save['cz_category']    = $challenge_category;
        
        $save_start_date        = date("Y-m-d", strtotime($start_date));
        $save_start_time        = date("H:i:s", strtotime($start_time));
        $save_start_date_time   = $save_start_date." ".$save_start_time;
        $save['cz_start_date']  = $save_start_date_time;
        $save['cz_duration']    = $challenge_duration;
        $save['cz_show_categories']    = $this->input->post('show_categories');
        $save['cz_show_categories'] = ($save['cz_show_categories'])?'1':'0';
        $save['cz_instructions']= $this->get_instruction();
        
        $save_end_date          = date("Y-m-d", strtotime($end_date));
        $save_end_time          = date("H:i:s", strtotime($end_time));
        $save_end_date_time     = $save_end_date." ".$save_end_time;
        $save['cz_end_date']    = $save_end_date_time;
        $save['action_id']      = $this->actions['create']; 
        $save['action_by']      = $this->auth->get_current_admin('id');
        $save['cz_account_id']  = $this->config->item('id');
        
        //echo '<pre>';print_r($save); die;
        
        $challenge_id           = $this->Challenge_model->save($save);
        $arr                    = array();
        $arr['error']           = false;
        $arr['id']              = $challenge_id;
        
        /*if($save_start_date == date("Y-m-d"))
        {
            //send notification to admin
            $subscribed_users = $this->Challenge_model->category_subsciption_users(array('category_id'=>$challenge_category));
            if(!empty($subscribed_users))
            {
                $param              = array();
                $param['ids']       = array();
                $mailer             = array();
                $mailer['to']       = array();
                foreach($subscribed_users as $subscribed_user)
                {
                    $param['ids'][]  = $subscribed_user['cs_user_id'];
                    if($subscribed_user['us_email'])
                    {
                        $mailer['to'][]  = $subscribed_user['us_email'];                    
                    }
                }
                $notify_challenge_name  = (strlen($challenge_name)>50)?substr($challenge_name, 0, 47).'...':$challenge_name;
                $challenge_zone_time    = date("F j, Y", strtotime($start_date)).' '.date("g:i a", strtotime($start_time));
                
                
                $mailer['from']         = $this->config->item('site_email');
                $mailer['subject']      = "A new challenge zone <b>".$challenge_name."</b> has been added";
                $mailer['body']         = "Hi,<br/> Greetings from ".$this->config->item('site_name').". A new challenge zone <b>'.$notify_challenge_name.'</b> has been added on <b>'.$challenge_zone_time.'</b>. Please click <a href='".site_url('material/challenge/'.$challenge_id)."'>here</a> to attend this challenge.";
                $this->ofabeemailer->send_mail($mailer);            
            }
            //End            
        }*/
        
        echo json_encode($arr);
    }
    
    function language()
    {
        $response = array();
        $response['language'] = array();
        $response['language'] = get_instance()->lang->language;
        echo json_encode($response);
    }

    function report($challenge_id){
        
        if( !$challenge_id )
        {
            $this->session->set_flashdata('message', lang('challenge_not_found'));
            redirect($this->config->item('admin_folder').'/challenge_zone');
        }

        $data                       = array();
        $breadcrumb                 = array();
        $breadcrumb[]               = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]               = array( 'label' => lang('manage_challenge'), 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']         = $breadcrumb;
        $data['title']              = lang('challenge_zones');
        $data['categories']         = $this->Category_model->categories(array('direction'=>'DESC','parent_id'=>'0'));
        $data['challenges']         = $this->Challenge_model->challenges(array('direction'=>'DESC', 'order_by'=>'cz_start_date', 'deleted'=>'0'));
        $data['title']              = lang('challenge_zones');

        $data['challenge_id']       = $challenge_id;
        /*foreach($data['challenges'] as $challenge){
            if($challenge_id == $challenge['id']){
                $data['current_challenge'] = $challenge;
            }
        }*/
        $data['current_challenge']         = $this->Challenge_model->challenge(array('id'=>$challenge_id, 'deleted' => '0'));


        if(empty($data['current_challenge'])){
            redirect(admin_url('challenge_zone'));
        }

        foreach($data['categories'] as $category){
            if($category['id'] == $data['current_challenge']['cz_category']){
                $data['current_category']  = $category;
            }
        }


        /*
        echo '<pre>';
        print_r($data['current_challenge']);
        print_r($data['challenges']);
        die();
*/


        $current_challenges = array();

        foreach($data['challenges'] as $challenge){
            if($data['current_challenge']['cz_category'] == $challenge['cz_category']){
                $data['current_challenge'] = $challenge;
                array_push($current_challenges, $challenge); 
            }
        }

        $data['current_challenges'] = $current_challenges;

//        echo $challenge_id;die;
        //echo '<pre>';print_r($data);die();
/*
        $data['users'] = $this->Challenge_model->challenge_zone_attempts(array('challenge_id' => $challenge['id']));
        
        //echo "<pre>";print_r($data['users']);die;

        foreach($data['users'] as $key => $assessment){
            //echo "<pre>";print_r($assessment['attempted_id']);die;
            $data['user_challenge_report'][$assessment['attempted_id']] = $this->Challenge_model->challenge_report(array('attempted_id' => $assessment['attempted_id']));
//echo "<pre>";print_r($data['user_challenge_report'][$assessment['attempted_id']]);die;
            //$data['users'][$key]['assessment_report'] = $this->Challenge_model->challenge_report(array('attempted_id' => $assessment['attempted_id']));
            $data['users'][$key]['total_count']       = sizeof($data['user_challenge_report'][$assessment['attempted_id']]);
            
            $data['users'][$key]['count_not_tried'] = 0;
            $data['users'][$key]['correct'] = 0;
            $data['users'][$key]['incorrect'] = 0;
            $data['users'][$key]['q_type'] = 0;
            
            
            foreach($data['user_challenge_report'][$assessment['attempted_id']] as $report){

                if($report['czr_answer'] == '' || empty($report['czr_answer'])){
                    $data['users'][$key]['count_not_tried']++;
                }
                else{

                    if($report['q_type'] == 1){
                        $data['users'][$key]['q_type'] = 1;
                        if($report['q_answer'] == $report['czr_answer']){
                            $data['users'][$key]['correct']++;
                        }
                        else{
                            $data['users'][$key]['incorrect']++;
                        }
                    }
                    else if($report['q_type'] == 2){
                        $data['users'][$key]['q_type'] = 2;
                        $user_answers = explode(',', $report['czr_answer']);
                        $original_answers = explode(',', $report['q_answer']);
                        sort($user_answers);
                        sort($original_answers);
                        if ($user_answers==$original_answers)
                        {
                            $data['users'][$key]['correct']++;
                        }else{
                            $data['users'][$key]['incorrect']++;
                        }
                    }else if($report['q_type'] == 3){
                        $data['users'][$key]['q_type'] = 3;
                    }
                } 
            }
            
            $success_percentage = round(($data['users'][$key]['correct'] / $data['users'][$key]['total_count']) * 100, 2);
            $data['users'][$key]['percentage'] = $success_percentage;

        }*/

        //echo '<pre>'; print_r( $data['users']);die;
        $this->load->view($this->config->item('admin_folder').'/challenge_zone_report', $data);
    }

    function select_test(){

        $challenge_id = $this->input->post('challenge_id');
        

        $data['challenges']         = $this->Challenge_model->challenges(array('direction'=>'DESC', 'order_by'=>'cz_start_date', 'deleted'=>'0'));
        
        

        foreach($data['challenges'] as $challenge){
            if($challenge_id == $challenge['id']){
                $data['current_challenge'] = $challenge;
            }
        }
        
        

        $data['users'] = $this->Challenge_model->challenge_zone_attempts(array('challenge_id' => $challenge_id));
        //echo "<pre>";print_r($data['users']);die;

        foreach($data['users'] as $key => $assessment){


            $data['users'][$key]['assessment_report'] = $this->Challenge_model->challenge_report(array('attempted_id' => $assessment['attempted_id']));
            $data['users'][$key]['total_count']       = count($data['users'][$key]['assessment_report']);
            $temp_correct       = 0;
            $temp_wrong         = 0;
            $temp_not_attempted = 0;
            $data['users'][$key]['q_type'] = 1;
            foreach($data['users'][$key]['assessment_report'] as $key2 => $report){

                if($report['czr_answer'] == '' || empty($report['czr_answer'])){
                    $temp_not_attempted++;
                }
                else{

                    if($report['q_type'] == 1){

                        if($report['q_answer'] == $report['czr_answer']){
                            $temp_correct++;
                        }
                        else{
                            $temp_wrong++;
                        }
                    }
                    else if($report['q_type'] == 2){

                        $user_answers = explode(',', $report['q_answer']);
                        $original_answers = explode(',', $report['czr_answer']);
                        sort($user_answers);
                        sort($original_answers);
                        if ($user_answers==$original_answers)
                        {
                            $temp_correct++;
                        }else{
                            $temp_wrong++;
                        }
                    }else if($report['q_type'] == 3){
                        $data['users'][$key]['q_type'] = 3;
                    }
                } 
            }
 
            $data['users'][$key]['count_not_tried'] = $temp_not_attempted;
            $data['users'][$key]['correct'] = $temp_correct;
            $data['users'][$key]['incorrect'] = $temp_wrong;
            $data['users'][$key]['percentage'] =  round(($temp_correct / $data['users'][$key]['total_count']) * 100, 2);

        }

        $str   = '';

        foreach ($data['users'] as $key => $user) {
            
            $str   = $str . '<tr class="rTableRow">';
            $str   = $str . '       <td class="rTableCell">';
            $str   = $str . '   <a target="_blank" href="'.admin_url().'challenge_zone/evaluate_challenge/'.$data['current_challenge']['id'].'/'.$user['uid'].'" >';
            $str   = $str . '   <span class="icon-wrap-round img">';
            $str   = $str . '    <img src="'.(($user['us_image'] == 'default.jpg')?default_user_path():  user_path()).$user['us_image'].'" >';
            $str   = $str . '    </span>';
            $str   = $str . '    <span class="line-h36">'.$user['us_name'].'</span>';
            $str   = $str . ' </a>';
            $str   = $str . '        </td>';
            $str   = $str . '   <td class="rTableCell">';
            $dt = new DateTime($user['cza_attempted_date']);
            $str   = $str . strtoupper($dt->format('M d Y'));
            $str   = $str . '   </td>';

            if($user['q_type'] == 3){
                $str   = $str . '<td class="rTableCell">';
                $str   = $str . 'Explanatory question found, Need Manual evaluation</td>';
                $str   = $str . '<td class="rTableCell">';
                $str   = $str . '<a target="_blank"  href="'.admin_url().'challenge_zone/evaluate_challenge/'.$data['current_challenge']['id'].'/'.$user['id'].'"';
                $str   = $str . ' class="btn btn-green" > EVALUATE</a></td>';

            }
            else {
                $str   = $str. '<td class="rTableCell font-green">'.$user['correct'].' Correct</td>';
                $str   = $str. '<td class="rTableCell font-red">'.$user['incorrect'].' Wrong</td>';
                $str   = $str. '<td class="rTableCell font-lgt-grey">'.$user['count_not_tried'].' Not Tried</td>';
                $str   = $str. '<td class="rTableCell font-green"> '.$user['percentage'].' % success</td>';
                $str   = $str. '<td class="rTableCell"> '.$user['cz_duration'].' min</td>';

            }
            $str   = $str. '</tr>';
            echo $str;
        }
        
    }

    function select_category(){



        $data['categories']         = $this->Category_model->categories(array('direction'=>'DESC','parent_id'=>'0'));
        $data['challenges']         = $this->Challenge_model->challenges(array('direction'=>'DESC', 'order_by'=>'cz_start_date', 'deleted'=>'0'));

        $temp_current = '';
        $cattegory_id = $this->input->post('cattegory_id');
        $str          = '';
        foreach($data['challenges'] as $key => $test){ 
            if($test['cz_category'] == $cattegory_id){
                if($str == ''){
                    $temp_current = $key;
                }
                $str      = $str . '<li>';
                $str      = $str . '<a href="#" onclick="select_test('.$test['id'].')" >'.$test['cz_title'].'</a></li>';
            }
        }

        
        $data['str'] = $str;
        $data['current_id'] = '';
        $data['current_name']='';

        if($temp_current != ''){
            $data['current_id']     = $data['challenges'][$temp_current]['id'];
            $data['current_name']   = $data['challenges'][$temp_current]['cz_title'];
        }

        echo json_encode($data);
    }

    function evaluate_challenge($challenge_id, $user_id){

        if(!$challenge_id || !$user_id){
            redirect(admin_url('challenge_zone'));
        }

        $data                       = array();
        $data['challenge_id']       = $challenge_id;
        $data['user_id']            = $user_id;
        $breadcrumb                 = array();
        $breadcrumb[]               = array( 'label' => 'Home', 'link' => admin_url(), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]               = array( 'label' => lang('manage_challenge'), 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']         = $breadcrumb;
        $data['title']              = lang('challenge_zones');
        $data['categories']         = $this->Category_model->categories(array('direction'=>'DESC','parent_id'=>'0'));
        $data['challenges']         = $this->Challenge_model->challenges(array('direction'=>'DESC', 'order_by'=>'cz_start_date', 'deleted'=>'0'));
        $data['title'] = lang('challenge_zones');

        foreach($data['challenges'] as $challenge){
            if($challenge_id == $challenge['id']){
                $data['current_challenge'] = $challenge;
            }
        }

        //echo '<pre>';print_r($data);die();

        foreach($data['categories'] as $category){
            if($category['id'] == $data['current_challenge']['cz_category']){
                $data['current_category']  = $category;
            }
        }

        $data['users'] = $this->Challenge_model->challenge_zone_attempts(array('challenge_id' => $challenge_id));
        
        foreach($data['users'] as $key => $assessment){

            $data['users'][$key]['assessment_report'] = $this->Challenge_model->challenge_report(array('attempted_id' => $assessment['attempted_id']));
            $data['users'][$key]['total_count']       = count($data['users'][$key]['assessment_report']);
            $temp_correct       = 0;
            $temp_wrong         = 0;
            $temp_not_attempted = 0;
            $data['users'][$key]['q_type'] = 1;
            foreach($data['users'][$key]['assessment_report'] as $key2 => $report){

                if($report['czr_answer'] == '' || empty($report['czr_answer'])){
                    $temp_not_attempted++;
                    $data['users'][$key]['assessment_report'][$key2]['correct'] = 2;
                    $temp_options = explode(',', $report['q_options']);
                        $data['users'][$key]['assessment_report'][$key2]['qo_options']  = $temp_options;

                    $temp_options_value = array();
                        $temp_options_value = $this->Challenge_model->get_option_value($report['q_options']);

                        $data['users'][$key]['assessment_report'][$key2]['qo_options_value'] =
                        $temp_options_value;
			$temp_q_ans = '';
			$chr = '';

                        foreach ($temp_options as $value) {
                            if($value == $report['q_answer']){

                                if($temp_q_ans == ''){
                                    $temp_q_ans = chr($chr);
                                }
                                else{
                                    $temp_q_ans = $temp_q_ans.','.chr($chr);
                                }

                            }
                            $chr++;
                        }

                        $data['users'][$key]['assessment_report'][$key2]['correct_answer'] = $temp_q_ans;

                }
                else{

                    if($report['q_type'] == 1){

                        if($report['q_answer'] == $report['czr_answer']){
                            $temp_correct++;
                            $data['users'][$key]['assessment_report'][$key2]['correct'] = 1;
                        }
                        else{
                            $temp_wrong++;
                            $data['users'][$key]['assessment_report'][$key2]['correct'] = 0;
                        }
                        $temp_options = explode(',', $report['q_options']);
                        $temp_arr     = array();
                        
                        $temp_options_value = array();
                        $temp_options_value = $this->Challenge_model->get_option_value($report['q_options']);

                        $data['users'][$key]['assessment_report'][$key2]['qo_options_value'] =
                        $temp_options_value;
                        $data['users'][$key]['assessment_report'][$key2]['qo_options']  = $temp_options;
                        $temp_q_ans   = '';
                        $temp_a_ans   = '';
                        $chr  = 65;
                        
                        foreach ($temp_options as $value) {
                            if($value == $report['q_answer']){

                                if($temp_q_ans == ''){
                                    $temp_q_ans = chr($chr);
                                }
                                else{
                                    $temp_q_ans = $temp_q_ans.','.chr($chr);
                                }

                            }

                            if($value == $report['czr_answer']){

                                if($temp_a_ans == ''){
                                    $temp_a_ans = chr($chr);
                                }
                                else{
                                    $temp_a_ans = $temp_a_ans.','.chr($chr);
                                }
                            }
                            $chr++;
                        }
                        $data['users'][$key]['assessment_report'][$key2]['correct_answer'] = $temp_q_ans;
                        $data['users'][$key]['assessment_report'][$key2]['user_answer'] = $temp_a_ans;

                    }
                    else if($report['q_type'] == 2){

                        $user_answers = explode(',', $report['q_answer']);
                        $original_answers = explode(',', $report['czr_answer']);
                        sort($user_answers);
                        sort($original_answers);
                        if ($user_answers==$original_answers)
                        {
                            $temp_correct++;
                            $data['users'][$key]['assessment_report'][$key2]['correct'] = 1;
                        }else{
                            $temp_wrong++;
                            $data['users'][$key]['assessment_report'][$key2]['correct'] = 0;
                        }

                        $temp_options = explode(',', $report['q_options']);
                        $data['users'][$key]['assessment_report'][$key2]['qo_options']  = $temp_options;
                        $temp_options_value = array();
                        $temp_options_value = $this->Challenge_model->get_option_value($report['q_options']);
                        $data['users'][$key]['assessment_report'][$key2]['qo_options_value'] =
                        $temp_options_value;

                        $temp_q_ans   = '';
                        $temp_a_ans   = '';
                        $chr  = 65;

                        foreach ($temp_options as  $value) {

                            $temp_q_opt = explode(',', $report['q_answer']);
                            
                            foreach ($temp_q_opt as  $opt) {
                                if($value == $opt){

                                    if($temp_q_ans == ''){
                                        $temp_q_ans = chr($chr);
                                    }
                                    else{
                                        $temp_q_ans = $temp_q_ans.','.chr($chr);
                                    }
                                }
                            }


                            $temp_a_opt = explode(',', $report['czr_answer']);
                            foreach ($temp_a_opt as  $opt) {
                                if($value == $opt){

                                    if($temp_a_ans == ''){
                                        $temp_a_ans = chr($chr);
                                    }
                                    else{
                                        $temp_a_ans = $temp_a_ans.','.chr($chr);
                                    }
                                }
                            }
                            
                            $chr++;
                        }

                        $data['users'][$key]['assessment_report'][$key2]['correct_answer'] = $temp_q_ans;
                        $data['users'][$key]['assessment_report'][$key2]['user_answer'] = $temp_a_ans;

                    }else if($report['q_type'] == 3){
                        $data['users'][$key]['q_type'] = 3;
                    }
                } 
            }
 
            $data['users'][$key]['count_not_tried'] = $temp_not_attempted;
            $data['users'][$key]['correct'] = $temp_correct;
            $data['users'][$key]['incorrect'] = $temp_wrong;
            $data['users'][$key]['percentage'] =  round(($temp_correct / $data['users'][$key]['total_count']) * 100, 2);

        }

        $data['prev_id'] = '';
        $data['next_id'] = '';
        //echo '<pre>'; print_r($data['users']);die;
        foreach ($data['users'] as $key => $assessment) {
            
            if($assessment['uid'] == $user_id){
                if(isset($data['users'][($key - 1)])){
                    $data['prev_id']    = $data['users'][($key - 1)]['uid'];
                }
                if(isset($data['users'][($key + 1)])){
                    $data['next_id']    = $data['users'][($key + 1)]['uid'];
                }
                $data['user']  = $assessment;
            }
        }

       // echo '<pre>'; print_r( $data['user']);die;
        $this->load->view($this->config->item('admin_folder').'/evaluate_challenge', $data);
    }

    function save_explanatory(){

        $czr_id     = $this->input->post('czr_id');
        $czr_mark   = $this->input->post('czr_mark');

        $this->Challenge_model->save_explanatory($czr_id, $czr_mark);
    }

    function print_challenge($challenge_id, $user_id){

        if(!$challenge_id || !$user_id){
            redirect(admin_url('challenge_zone'));
        }

        $data                       = array();
        $data['challenge_id']       = $challenge_id;
        $data['user_id']            = $user_id;
        $breadcrumb                 = array();
        $breadcrumb[]               = array( 'label' => 'Home', 'link' => base_url('/admin'), 'active' => '', 'icon' => '<i class="fa fa-dashboard"></i>' );
        $breadcrumb[]               = array( 'label' => lang('manage_challenge'), 'link' => '', 'active' => 'active', 'icon' => '' );
        $data['breadcrumb']         = $breadcrumb;
        $data['title']              = lang('challenge_zones');
        $data['categories']         = $this->Category_model->categories(array('direction'=>'DESC','parent_id'=>'0'));
        $data['challenges']         = $this->Challenge_model->challenges(array('direction'=>'DESC', 'order_by'=>'cz_start_date', 'deleted'=>'0'));
        $data['title'] = lang('challenge_zones');

        foreach($data['challenges'] as $challenge){
            if($challenge_id == $challenge['id']){
                $data['current_challenge'] = $challenge;
            }
        }

        foreach($data['categories'] as $category){
            if($category['id'] == $data['current_challenge']['cz_category']){
                $data['current_category']  = $category;
            }
        }

        $data['users'] = $this->Challenge_model->challenge_zone_attempts(array('challenge_id' => $challenge_id));

        foreach($data['users'] as $key => $assessment){

            $data['users'][$key]['assessment_report'] = $this->Challenge_model->challenge_report(array('attempted_id' => $assessment['attempted_id']));
            $data['users'][$key]['total_count']       = count($data['users'][$key]['assessment_report']);
            $temp_correct       = 0;
            $temp_wrong         = 0;
            $temp_not_attempted = 0;
            $data['users'][$key]['q_type'] = 1;
            foreach($data['users'][$key]['assessment_report'] as $key2 => $report){

                if($report['czr_answer'] == '' || empty($report['czr_answer'])){
                    $temp_not_attempted++;
                    $data['users'][$key]['assessment_report'][$key2]['correct'] = 2;
                    $temp_options = explode(',', $report['q_options']);
                        $data['users'][$key]['assessment_report'][$key2]['qo_options']  = $temp_options;

                    $temp_options_value = array();
                        $temp_options_value = $this->Challenge_model->get_option_value($report['q_options']);

                        $data['users'][$key]['assessment_report'][$key2]['qo_options_value'] =
                        $temp_options_value;
                        $chr  = 65;
                        $temp_q_ans   = '';
                        foreach ($temp_options as $value) {
                            if($value == $report['q_answer']){

                                if($temp_q_ans == ''){
                                    $temp_q_ans = chr($chr);
                                }
                                else{
                                    $temp_q_ans = $temp_q_ans.','.chr($chr);
                                }

                            }
                            $chr++;
                        }

                        $data['users'][$key]['assessment_report'][$key2]['correct_answer'] = $temp_q_ans;

                }
                else{

                    if($report['q_type'] == 1){

                        if($report['q_answer'] == $report['czr_answer']){
                            $temp_correct++;
                            $data['users'][$key]['assessment_report'][$key2]['correct'] = 1;
                        }
                        else{
                            $temp_wrong++;
                            $data['users'][$key]['assessment_report'][$key2]['correct'] = 0;
                        }
                        $temp_options = explode(',', $report['q_options']);
                        $temp_arr     = array();
                        
                        $temp_options_value = array();
                        $temp_options_value = $this->Challenge_model->get_option_value($report['q_options']);

                        $data['users'][$key]['assessment_report'][$key2]['qo_options_value'] =
                        $temp_options_value;
                        $data['users'][$key]['assessment_report'][$key2]['qo_options']  = $temp_options;
                        $temp_q_ans   = '';
                        $temp_a_ans   = '';
                        $chr  = 65;
                        
                        foreach ($temp_options as $value) {
                            if($value == $report['q_answer']){

                                if($temp_q_ans == ''){
                                    $temp_q_ans = chr($chr);
                                }
                                else{
                                    $temp_q_ans = $temp_q_ans.','.chr($chr);
                                }

                            }

                            if($value == $report['czr_answer']){

                                if($temp_a_ans == ''){
                                    $temp_a_ans = chr($chr);
                                }
                                else{
                                    $temp_a_ans = $temp_a_ans.','.chr($chr);
                                }
                            }
                            $chr++;
                        }
                        $data['users'][$key]['assessment_report'][$key2]['correct_answer'] = $temp_q_ans;
                        $data['users'][$key]['assessment_report'][$key2]['user_answer'] = $temp_a_ans;

                    }
                    else if($report['q_type'] == 2){

                        $user_answers = explode(',', $report['q_answer']);
                        $original_answers = explode(',', $report['czr_answer']);
                        sort($user_answers);
                        sort($original_answers);
                        if ($user_answers==$original_answers)
                        {
                            $temp_correct++;
                            $data['users'][$key]['assessment_report'][$key2]['correct'] = 1;
                        }else{
                            $temp_wrong++;
                            $data['users'][$key]['assessment_report'][$key2]['correct'] = 0;
                        }

                        $temp_options = explode(',', $report['q_options']);
                        $data['users'][$key]['assessment_report'][$key2]['qo_options']  = $temp_options;
                        $temp_options_value = array();
                        $temp_options_value = $this->Challenge_model->get_option_value($report['q_options']);
                        $data['users'][$key]['assessment_report'][$key2]['qo_options_value'] =
                        $temp_options_value;

                        $temp_q_ans   = '';
                        $temp_a_ans   = '';
                        $chr  = 65;

                        foreach ($temp_options as  $value) {

                            $temp_q_opt = explode(',', $report['q_answer']);
                            
                            foreach ($temp_q_opt as  $opt) {
                                if($value == $opt){

                                    if($temp_q_ans == ''){
                                        $temp_q_ans = chr($chr);
                                    }
                                    else{
                                        $temp_q_ans = $temp_q_ans.','.chr($chr);
                                    }
                                }
                            }


                            $temp_a_opt = explode(',', $report['czr_answer']);
                            foreach ($temp_a_opt as  $opt) {
                                if($value == $opt){

                                    if($temp_a_ans == ''){
                                        $temp_a_ans = chr($chr);
                                    }
                                    else{
                                        $temp_a_ans = $temp_a_ans.','.chr($chr);
                                    }
                                }
                            }
                            
                            $chr++;
                        }

                        $data['users'][$key]['assessment_report'][$key2]['correct_answer'] = $temp_q_ans;
                        $data['users'][$key]['assessment_report'][$key2]['user_answer'] = $temp_a_ans;

                    }else if($report['q_type'] == 3){
                        $data['users'][$key]['q_type'] = 3;
                    }
                } 
            }
 
            $data['users'][$key]['count_not_tried'] = $temp_not_attempted;
            $data['users'][$key]['correct'] = $temp_correct;
            $data['users'][$key]['incorrect'] = $temp_wrong;
            $data['users'][$key]['percentage'] =  round(($temp_correct / $data['users'][$key]['total_count']) * 100, 2);

        }

        $data['prev_id'] = '';
        $data['next_id'] = '';

        foreach ($data['users'] as $key => $assessment) {

            if($assessment['uid'] == $user_id){
                if(isset($data['users'][($key - 1)])){
                    $data['prev_id']    = $data['users'][($key - 1)]['id'];
                }
                if(isset($data['users'][($key + 1)])){
                    $data['next_id']    = $data['users'][($key + 1)]['id'];
                }
                $data['user']  = $assessment;
            }
        }

        //echo '<pre>'; print_r($data);die;
        $this->load->view($this->config->item('admin_folder').'/print_challenge', $data);
    }

    function export_challange_report($challenge_id){

        $cnt                = 1;
        $updated_date       = date('Y-m-d-H-i-s');
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment;Filename=Descriptive_test_".$updated_date.".xls");

        $data['users'] = $this->Challenge_model->challenge_zone_attempts(array('challenge_id' => $challenge_id));

        foreach($data['users'] as $key => $assessment){

            $data['users'][$key]['assessment_report'] = $this->Challenge_model->challenge_report(array('attempted_id' => $assessment['attempted_id']));
            $data['users'][$key]['total_count']       = count($data['users'][$key]['assessment_report']);
            $temp_correct       = 0;
            $temp_wrong         = 0;
            $temp_not_attempted = 0;
            $data['users'][$key]['q_type'] = 1;
            foreach($data['users'][$key]['assessment_report'] as $key2 => $report){

                if($report['czr_answer'] == '' || empty($report['czr_answer'])){
                    $temp_not_attempted++;
                }
                else{

                    if($report['q_type'] == 1){

                        if($report['q_answer'] == $report['czr_answer']){
                            $temp_correct++;
                        }
                        else{
                            $temp_wrong++;
                        }
                    }
                    else if($report['q_type'] == 2){

                        $user_answers = explode(',', $report['q_answer']);
                        $original_answers = explode(',', $report['czr_answer']);
                        sort($user_answers);
                        sort($original_answers);
                        if ($user_answers==$original_answers)
                        {
                            $temp_correct++;
                        }else{
                            $temp_wrong++;
                        }
                    }else if($report['q_type'] == 3){
                        $data['users'][$key]['q_type'] = 3;
                    }
                } 
            }
 
            $data['users'][$key]['count_not_tried'] = $temp_not_attempted;
            $data['users'][$key]['correct'] = $temp_correct;
            $data['users'][$key]['incorrect'] = $temp_wrong;
            $data['users'][$key]['percentage'] =  round(($temp_correct / $data['users'][$key]['total_count']) * 100, 2);

        }

        //echo '<pre>';print_r($data);die();
        

        $userHTML = "";
        $userHTML       .= '<h2><center>User Details </center></h2>';
        $userHTML       .= '   <table class="table table-bordered " border="1" >';
        $userHTML       .= '   <thead>';
        $userHTML       .= '   <tr>';
        $userHTML       .= '   <th><h3>Sl.No</h3></th>';
        $userHTML       .= '   <th><h3>Name</h3></th>';
        $userHTML       .= '   <th><h3>Date</h3></th>';
        $userHTML       .= '   <th><h3>Total Questions</h3></th>';
        $userHTML       .= '   <th><h3>Correct</h3></th>';
        $userHTML       .= '   <th><h3>Wrong</h3></th>';
        $userHTML       .= '   <th><h3>Not tried</h3></th>';
        $userHTML       .= '   <th><h3>Success Percentage</h3></th>';
        $userHTML       .= '   <th><h3>Duration</h3></th>';
        $userHTML       .= '   </tr>';
        $userHTML       .= '    </thead>';
        $userHTML       .= '    <tbody>  ';

            foreach ($data['users'] as $key => $user) {
                $userHTML   .= '<tr>';
                $userHTML   .= '    <td>';
                $userHTML   .= '        '.$cnt;
                $userHTML   .= '    </td>';
                $userHTML   .= '    <td>';
                $userHTML   .= '        '.$user['us_name'];
                $userHTML   .= '    </td>';
                $userHTML   .= '    <td>';
                $dt          = new DateTime($user['cza_attempted_date']);
                $userHTML   .= '        '.$dt->format('M d Y');
                $userHTML   .= '    </td>';
                $userHTML   .= '    <td>';
                $userHTML   .= '        '.$user['total_count'];
                $userHTML   .= '    </td>';
                $userHTML   .= '    <td>';
                $userHTML   .= '        '.$user['correct'];
                $userHTML   .= '    </td>';
                $userHTML   .= '    <td>';
                $userHTML   .= '        '.$user['incorrect'];
                $userHTML   .= '    </td>';
                $userHTML   .= '    <td>';
                $userHTML   .= '        '.$user['count_not_tried'];
                $userHTML   .= '    </td>';
                $userHTML   .= '    <td>';
                $userHTML   .= '        '.$user['percentage'];
                $userHTML   .= '    </td>';
                $userHTML   .= '    <td>';
                $userHTML   .= '        '.$user['cz_duration'];
                $userHTML   .= '    </td>';
                $userHTML   .= '</tr>';
                $cnt++;
            }
            $userHTML       .= '    </tbody>'; 
            $userHTML       .= '  </table>';

            echo $userHTML;
    }
}

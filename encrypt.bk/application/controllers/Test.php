<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
    
        function unitTest(){
            $this->load->library('unit_test');
            $this->unit->run(1, TRUE);
            echo $this->unit->report();
            //print_r($this->unit->result());
        }

        function notify($id=false, $message= 'This is second sample push notification')
        {
            $this->load->library('ofabeenotifier');
            $param              = array();
            if($id)
            {
                $param['ids']       = array($id);            
            }
            else
            {
                $param['ids']       = array(15);            
            }
            $param['message']   = $message;
            $param['link']      = 'http://test.onlineprofesor.com/login';
            $response           = $this->ofabeenotifier->push_notification($param);
            echo '<pre>'; print_r($response);
        }
        
        function notification($id = 0)
        {
            $this->load->library('ofabeenotifier');
            echo '<pre>'; print_r($this->ofabeenotifier->user_notification(array('user_id'=>$id)));
        }
        
        function my_notification($id = false)
        {
            $this->load->library('ofabeenotifier');
            $data = array();
            if($id)
            {
                $user['id'] = $id;
            }
            else
            {
                $user = $this->auth->get_current_user_session('user');            
            }
            $data['site_notification'] = $this->ofabeenotifier->user_notification(array('user_id'=>$user['id']));
            $this->load->view($this->config->item('theme').'/site_notification', $data);
        }
        
        function mark_as_read($id = 0, $message_id = false)
        {
            $this->load->library('ofabeenotifier');
            $param = array();
            $param['user_id'] = $id;
            if($message_id)
            {
                $param['message_id'] = /*array('17-02-14-02-02-15-7498', '17-02-14-02-02-49-1373', '17-02-14-02-02-54-9162');//*/$message_id;            
            }
            echo '<pre>'; print_r($this->ofabeenotifier->mark_as_read($param));
        }
    
	public function index()
	{
		$this->load->view('welcome_message');
	}
        
        function instamojo($course_id = false)
        {
            $user   = $this->auth->get_current_user_session('user');
            if(!$user)
            {
                redirect('dashboard');
            }
            
            $this->load->model(array('Course_model'));
            $course = $this->Course_model->course(array('id' => $course_id));
            if(!$course)
            {
                redirect('dashboard');
            }
            $params                 = array();
            $params['api_key']      = '9890472149be399cc09dd9defd23cca7';
            $params['auth_token']   = '87e06b52c6d580c2a27b67c38734da95';
            $params['endpoint']     = 'https://test.instamojo.com/api/1.1/';
            $this->load->library('instamojo',$params);
            
            $response = $this->instamojo->paymentRequestCreate(array(
                "purpose" => "Purchasing course ".$course['cb_title'],
                "amount" => $course['cb_price'],
                "send_email" => false,
                "email" => $user['us_email'],
                "redirect_url" => site_url('test/instamojo_response')
            ));
            header('Location: '.$response['longurl'], TRUE, $code);
        }
        
        public function instamojo_response()
        {
            $params                 = array();
            $params['api_key']      = '9890472149be399cc09dd9defd23cca7';
            $params['auth_token']   = '87e06b52c6d580c2a27b67c38734da95';
            $params['endpoint']     = 'https://test.instamojo.com/api/1.1/';
            $this->load->library('instamojo',$params);
            echo '<pre>';
            print_r($this->input->get());
            $payment_objects = $this->input->get();
            $payment_request_id = isset($payment_objects['payment_request_id'])?$payment_objects['payment_request_id']:false;
            $payment_id         = isset($payment_objects['payment_id'])?$payment_objects['payment_id']:false;
            
            $response = $this->instamojo->paymentRequestPaymentStatus($payment_request_id, $payment_id);
            print_r($response);       
         }

        public function route($id=0)
        {
            echo 'from the router =='.$id;
        }

        public function configs()
        {
            echo '<pre>'; 
            print_r($this->session->userdata('admin'));
            print_r($this->config->item('site_email'));
        }
        
        public function crypt()
        {
            ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
            $this->load->library('ofacrypt');
           // echo $this->ofacrypt->encrypt('hello world', 'frg_4hjy');
            echo '<br />';
            echo $this->ofacrypt->decrypt('NMNgNwN8NTN77l72NpNmNoN6NKNSN7NtNmNsNe7lN.7HNcNJNE7uNC7l7f76NJNU7-Nc7k74757.Ne7-N-NK79Nj717BN_7l7170N_ND7fNL7R7INCNNNQN-7BNeN_NONh7Q3937616430343037636236633363653734396562633065393238633039613762jbjKjpjhjL7aN1NyjGN2j7', 'frg_4hjy');
        }
        
        public function check() 
        {
            echo $this->config->item('acct_domain').'..';
            //echo '<pre>'; print_r($this->db->get('accounts')->result_array());;
        }
        
        public function sms()
        {
           $response = $this->ofabeemessenger->send_sms(array('phone_number' => '9043171716', 'message' => 'Loerum ipsum sit amit'));
           echo '<pre>'; print_r($response);
        }
        
         public function dummy()
        {
            $param['from']		= 'nithin.p@enfintechnologies.com';
            $param['to']            = array('thanveer.a@enfintechnologies.com','harish2al@gmail.com');
            $param['subject'] 	= 'test mail';
            $param['body'] 		= 'loerum ipsumm sit amit';
            print_r($param);
           $response = $this->ofabeemailer->send_mail($param);
           echo '<pre>'; print_r($response);
        }
        
        
        public function upload_view()
        {
         
            $this->load->helper('form');
          echo form_open_multipart(site_url('test/upload'));
          echo '  single file upload<br /><input name="single" id="" type="file" /><br />---------------------------------------------------------------<br />';
          //echo '  another single file upload<br /><input name="another_single" id="" type="file" /><br />---------------------------------------------------------------<br />';
          //echo '  multiple file upload<br /><input name="multiple[]" id="" multiple="multiple" type="file" /><br />---------------------------------------------------------------<br />';
          echo form_submit(array('name' => 'submit', 'value' => 'Save'));
          echo form_close();
        }
        
        public function upload()
        {
            echo '<pre>'; print_r($_FILES);
            
            
            $this->load->library('upload');

            $config['upload_path']		= 'uploads';
            $config['allowed_types']            = '*';
            $config['encrypt_name']		= true;
            $this->upload->initialize($config);
            $uploaded = $this->upload->do_upload('single');         
          //  $uploaded = $this->upload->do_upload('another_single');
            
            /*$config                             = array();
            $config['upload_path']		= 'uploads/test';
            $config['allowed_types']            = '*';
            $config['encrypt_name']		= true;
            $this->upload->initialize($config);
            $uploaded = $this->upload->do_upload_multiple('multiple');*/
            print_r($this->upload->display_errors());   
                
        }
        
        function convert()
        {
            $this->load->library('ofabeeconverter');
            $config                 = array();
            $config['input']        = $this->config->item('upload_folder'). '/videos/sample.mp4'; 
            $config['s3_upload']    = false;
            $config['lecture_id']   = 1;
            $this->ofabeeconverter->initifalsealize($config);
            $this->ofabeeconverter->convert();
        }
        
        
        
        function iframe()
        {
            ?>
            <html>
              <head>
              </head>
          <body style="margin: 0" >
              <iframe id="form-iframe" style="overflow:auto;border:none; margin: 0; position:fixed;width:100%;height:100%;" src="http://amasi.ofabee.com/cme-education-iframe"  scrolling="no"></iframe>
          </body>
          </html>



<?php
        }

        
}

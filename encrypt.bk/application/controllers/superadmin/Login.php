<?php
class Login extends CI_Controller {

    function __construct()
    {
        parent::__construct();
    }
    
    function index()
    {
        $signature = $this->input->post('signature');


        
        if(!$signature)
        {
            show_404();  die;        
        }
        $this->load->library('ofacrypt');     
        $credentitals = $this->ofacrypt->decrypt($this->input->post('signature'), $this->get_ofacrypt_key());
        $credentitals = explode('#', $credentitals);
        // echo "<pre>";print_r($credentitals);die;
        $login		= $this->auth->login_superadmin($credentitals[0], $credentitals[1]);

        if( $login )
        {
            if((isset($_REQUEST['from_super_admin'])) && ($_REQUEST['from_super_admin'] == 1) )
            {
                redirect(admin_url());
            }
            else
            {
                redirect(admin_url('welcome'));
            }
            
        }
        else
        {
            show_404();   die;           
        }
    }

    private function get_ofacrypt_key()
    {
        return '4tra3j';
    }
    
    
    public function render_form()
    {
        $this->load->helper('form');     
        $this->load->library('ofacrypt');
        echo form_open(site_url('superadmin/login'));
        $data = array('name' => 'signature', 'style' => 'width:70%; height:50px;', 'value' => $this->ofacrypt->encrypt('nithin.p@enfintechnologies.com#8cb2237d0679ca88db6464eac60da96345513964', $this->get_ofacrypt_key()));
        echo form_textarea($data);
        echo '<br />';
        echo form_submit('', 'login');
        echo form_close();
    }
}
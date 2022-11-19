<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ofabee extends CI_Controller {

	public function __construct()
    {
		header("Access-Control-Allow-Origin: http://accounts.enfinlabs.dev");
        parent::__construct();
    }

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
	public function index()
	{
            $data           = array();
            $data['title']  = $this->config->item('site_name'); 
            $default_view = $this->config->item('homepage');
            $default_view = ($default_view)?$default_view:'welcome_message';
            $this->load->view($default_view, $data);
	}

	function resetAccountMemcache(){
        $this->load->library('ofacrypt');
        $account_id = $this->ofacrypt->decrypt($this->input->post('data'), '4tra3j');
        $setting_website = 'setting_website'.$this->account_to_alpha($account_id);
        $this->memcache->resetAccountMemcache($setting_website);
        $setting_website = $account_id.'_web_configs';
        $this->memcache->resetAccountMemcache($setting_website);
	}
	
	function account_to_alpha($number)
    {
        $alpha = '';
        $alphabet = range('a','z');
        $count = count($alphabet);
        if($number <= $count)
        {
            $alpha = $alphabet[$number-1];
        }
        else
        {
            while($number > 0)
            {
                $modulo     = ($number - 1) % $count;
                $alpha      = $alphabet[$modulo].$alpha;
                $number     = floor((($number - $modulo) / $count));
            }    
        }
        return $alpha;
    }
}

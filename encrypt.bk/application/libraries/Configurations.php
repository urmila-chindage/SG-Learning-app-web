<?php
class Configurations
{
    public $CI;
    public function __construct()
    {
        $this->CI = &get_instance();
        //$this->CI->load->database();
        $this->run_web_debugger();
        $this->configure();
    }
    private function run_web_debugger()
    {
        // echo $this->CI->config->item('server_name');
    }
    private function configure()
    {
        $result             = array();
        $setting_key        = 'setting_website'.$this->account_to_alpha(config_item('id'));
        $_memcache_object   = config_item('memcache_object');
        $result             = $_memcache_object->get($setting_key);
        $result             = isset($result[0]) ? $result[0] : array();
        if(empty($result))
        {
            $result = $this->CI->settings->setting('website');
            $_memcache_object->set($setting_key, array($result), 7200);
        }
        if(isset($result['as_setting_value']) && sizeof($result['as_setting_value']) > 0)
        {
            foreach ($result['as_setting_value']['setting_value'] as $key => $value)
            {
                $this->CI->config->set_item($key, $value);        
            }
        }
    }
    private function account_to_alpha($number)
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
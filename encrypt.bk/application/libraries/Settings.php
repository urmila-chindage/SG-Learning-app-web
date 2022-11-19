<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Ofabee Settings Library
 *
 * @package     CodeIgniter
 * @subpackage	Library
 * @category	Library
 * @author	Ofabee Team
 */
class Settings
{
    var $CI;
    function __construct()
    {
        $this->CI =& get_instance();
    }
    
    public function setting($key)
    {
        $memcache_key       = 'setting_'.$key;
        $result             = $this->CI->memcache->get(array('key' => $memcache_key));
        
        if(empty($result))
        {
            $this->CI->load->database();
            $this->CI->db->select('account_settings.*, settings_keys.sk_key');
            $this->CI->db->from('account_settings');
            $this->CI->db->join('settings_keys', 'account_settings.as_key_id = settings_keys.id');
            $this->CI->db->where(array('account_settings.as_account_id' => $this->get_current_account(), 'settings_keys.sk_key' => $key));
            $result  = $this->CI->db->get()->row_array();    
            if($result)
            {
                $result['as_setting_value'] = $this->render_key_setting($result['as_setting_value']);        
            }
            $this->CI->memcache->set($memcache_key, $result);
        }
        return $result; 
    }
    
    public function render_key_setting($setting)
    {
      return (array)json_decode($setting);
    }
    
    public function get_current_account($key='id')
    {
        return $this->CI->config->item($key);
    }
    
    public function feature_enabled($key)
    {
        $return = false;
        $setting = $this->setting($key);
        if($setting)
        {
            if($setting['as_superadmin_value'] == 1 && $setting['as_siteadmin_value'] == 1 )
            {
                $return = true;
            }
        }
        return $return;
    }
}
?>
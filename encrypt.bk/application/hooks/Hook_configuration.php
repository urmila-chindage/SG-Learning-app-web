<?php
Class Hook_configuration
{    
    // public function configure()
    // {
    //     $CI =& get_instance();

    //     $row = $CI->db->get_where('account_settings', array('id' => 1))->row();

    //     //$CI->config->set_item('base_url', $row->base_url);
    //   echo '<pre>'; print_r($row);

    // }

    public function check_user_session()
    {
       $this->CI            =& get_instance();
       $this->CI->load->library('memcache');
       $object_key          = [];
       if($this->CI->auth->get_current_user_session('user') && empty($this->CI->auth->get_current_user_session('admin')))
       {
          $user_id           = $this->CI->auth->get_current_user_session('user')['id'];
          $object_key['key'] = 'user_'.$user_id;
         //  var_dump($this->CI->memcache->get($object_key));die;
           if($this->CI->memcache->get($object_key))
           {
              $this->CI->session->unset_userdata('user');
              // $this->CI->memcache->get($object_key);
              $this->CI->memcache->delete($object_key['key']);
              $this->CI->auth->is_logged_in_user();
          }
       }
    }
}
?>
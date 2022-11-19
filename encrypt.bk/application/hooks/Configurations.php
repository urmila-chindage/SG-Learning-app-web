<!-- < ?php
Class Configurations
{    
    public function configure()
    {
        $CI =& get_instance();

        $row = $CI->db->get_where('account_settings', array('id' => 1))->row();

        //$CI->config->set_item('base_url', $row->base_url);
      echo '<pre>'; print_r($row);

    }
}
?> -->
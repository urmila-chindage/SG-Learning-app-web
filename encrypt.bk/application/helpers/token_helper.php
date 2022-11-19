<?php 
if ( ! function_exists('fetch_user_token'))
{
    function fetch_user_token($usertype = 'admin')
    {  
        $CI                          = & get_instance();
        $CI->load->library('JWT');
        $CI->load->library('auth');
        $user                        = $CI->auth->get_current_user_session($usertype);
        $payload                     = array();
        $payload['id']               = $user['id'];
        $payload['email_id']         = $user['us_email'];
        $payload['register_number']  = '';
        $token                       = $CI->jwt->encode($payload, config_item('jwt_token')); 
        return $token; 
    }
}
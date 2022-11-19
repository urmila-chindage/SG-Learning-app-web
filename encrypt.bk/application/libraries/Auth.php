<?php if(!defined('BASEPATH')){ exit('No direct script access allowed');}
class Auth
{
    public $CI;
    public $notification_refresh_interval;

    public function __construct()
    {
        $this->CI                               = &get_instance();
        $this->CI->load->helper('url');
        $this->cookie_name                      = 'SDPKAdmin';
        $this->notification_refresh_interval    = '+30 minutes';
    }

    public function get_current_user_session( $index = 'admin' )
    {
        $session             = $this->CI->session->userdata( $index );
        if( $index == 'user')
        {
            if(!$this->__validate_user($session))
            {
                return false;
            }
        }
        return $session;
    }

    public function is_superadmin()
    {
        $super_admins                           = array(2);
        $admin_id                               = $this->get_current_admin();
        return in_array( $admin_id, $super_admins );
    }

    public function get_current_admin( $key = 'id' )
    {
        $admin                                  = $this->get_current_user_session( 'admin' );
        if( $admin ) 
        {
            return isset( $admin[$key] ) ? $admin[$key] : false;
        }
        $teacher                                = $this->get_current_user_session( 'teacher' );
        if( $teacher ) 
        {
            return isset( $teacher[$key] ) ? $teacher[$key] : false;
        }
        $content_editor                         = $this->get_current_user_session( 'content_editor' );
        if( $content_editor) 
        {
            return isset( $content_editor[$key] ) ? $content_editor[$key] : false;
        }
        $finance_manager                        = $this->get_current_user_session( 'finance_manager' );
        if( $finance_manager ) 
        {
            return isset( $finance_manager[$key] ) ? $finance_manager[$key] : false;
        }
        return false;
    }

    public function get_current_teacher( $key   = 'id' )
    {
        $admin                                  = $this->get_current_user_session( 'teacher' );
        return isset( $admin[$key] ) ? $admin[$key] : false;
    }

    /* The below functions are login & logout for admins */
    public function login_admin( $username, $password, $remember = false )
    {
        // make sure the username doesn't go into the query as false or 0
        if(!$username) 
        {
            return false;
        }
        $this->CI->load->model( 'Authenticate_model' );
        $result                                 = $this->CI->Authenticate_model->login_admin( $username, $password );
        if( sizeof( $result ) > 0 )
        {
            if( $result['us_status'] == '1' && $result['us_deleted'] == '0' )
            {
                $session_index                  = ( $result['rl_type'] == 1 ? 'admin' : 'user' );
                $this->CI->load->model('Settings_model');
                $data['user_profile_fields']    = array();
                $user_profile_fields            = $this->CI->Settings_model->profile_field_values( array('user_id' => $result['id']) );
                $user_profile_fields            = isset( $user_profile_fields['us_profile_fields'] ) ? explode('{#}', $user_profile_fields['us_profile_fields']) : array();
                
                if( !empty( $user_profile_fields ) ) 
                {
                    foreach( $user_profile_fields as $field ) 
                    {
                        $field                  = substr( $field, 2 );
                        $field                  = substr( $field, 0, -2 );
                        $temp_field             = explode('{=>}', $field);
                        $key                    = isset( $temp_field[0] ) ? $temp_field[0] : 0;
                        $value                  = isset( $temp_field[1] ) ? $temp_field[1] : '';
                        $data['user_profile_fields'][$key] = $value;
                    }
                }
                //generating excluded fields; this is mandatory fields with values. becuse no need to place it in form.
                $data['excluded_user_profile_fields'] = array();
                if( !empty( $data['user_profile_fields'] ) ) 
                {
                    foreach( $data['user_profile_fields'] as $u_field_id => $u_field_value ) 
                    {
                        if(trim( $u_field_value) != '' ) 
                        {
                            $data['excluded_user_profile_fields'][] = $u_field_id;
                        }
                    }
                }
                //getting mandatory fields
                $data['mandatory_profile_fields'] = $this->CI->Settings_model->profile_fields(array('strict_order' => true));
                $has_mandatory = 0;
                if( !empty( $data['mandatory_profile_fields'] ) ) 
                {
                    foreach( $data['mandatory_profile_fields'] as $field ) 
                    {
                        if( !in_array( $field['id'], $data['excluded_user_profile_fields'] ) ) 
                        {
                            $has_mandatory++;
                        }
                    }
                }
                $result['us_profile_completed'] = ( $has_mandatory ) ? 0 : 1;
                $admin                          = array();
                $admin[$session_index]          = array();
                foreach( $result as $key => $value ) 
                {
                    $admin[$session_index][$key]= $value;
                }
                //setting institute deyails
                $objects                        = array();
                $objects['key']                 = 'institute_'.$result['us_institute_id'];
                $callback                       = 'institute';
                $institute                      = $this->CI->memcache->get( $objects, $callback, array('id' => $result['us_institute_id'] ) );        
                if( !empty( $institute ) )
                {
                    $admin[$session_index]['institute_name'] = $institute['ib_name'];
                    $admin[$session_index]['institute_code'] = $institute['ib_institute_code'];
                }
                //End
                //setting branch details
                $objects                        = array();
                $objects['key']                 = 'branch_'.$result['us_branch'];
                $callback                       = 'branch';
                $branch                         = $this->CI->memcache->get( $objects, $callback, array('id' => $result['us_branch'] ) ); 
                if( !empty( $branch ) )
                {
                    $admin[$session_index]['branch_name'] = $branch['branch_name'];
                }        
                //end
                // //End
                unset( $admin[$session_index]['us_password'] );
                if( $remember ) 
                {
                    $loginCred                  = json_encode( array('username' => $username, 'password' => $password) );
                    $loginCred                  = base64_encode( $this->aes256Encrypt( $loginCred ) );
                    //remember the user for 6 months
                    $this->generateCookie( $loginCred, strtotime( '+6 months' ) );
                }
                $this->CI->session->set_userdata( $admin );
            }
            return $result;
        } 
        else 
        {
            return false;
        }
    }
    /* The below functions are login & logout for admins */
    public function login_superadmin( $username, $password, $remember = false )
    {
        // make sure the username doesn't go into the query as false or 0
        if( !$username ) 
        {
            return false;
        }
        $this->CI->load->model( 'Authenticate_model' );
        $result                                 = $this->CI->Authenticate_model->login_superadmin( $username, $password, $remember );
        if(sizeof( $result ) > 0) 
        {
            $admin                              = array();
            $admin['admin']                     = array();
            foreach( $result as $key => $value ) 
            {
                $admin['admin'][$key]           = $value;
            }
            unset( $admin['admin']['us_password'] );
            if( $remember ) 
            {
                $loginCred                      = json_encode( array('username' => $username, 'password' => $password) );
                $loginCred                      = base64_encode( $this->aes256Encrypt( $loginCred ) );
                //remember the user for 6 months
                $this->generateCookie( $loginCred, strtotime('+6 months') );
            }
            $this->CI->session->set_userdata( $admin );
            return true;
        } 
        else 
        {
            return false;
        }
    }
    public function is_logged_in( $redirect = false, $default_redirect = true, $session_index = 'admin' )
    {
        $session                                = $this->CI->session->userdata( $session_index );
        if( !$session ) 
        {
            //check the cookie
            if( isset( $_COOKIE[$this->cookie_name] ) ) 
            {
                //the cookie is there, lets log the customer back in.
                $info                           = $this->aes256Decrypt(base64_decode( $_COOKIE[$this->cookie_name]));
                $cred                           = json_decode( $info, true );
                if( is_array( $cred ) )
                {
                    $login                      = $this->login_admin( $cred['username'], $cred['password'] );
                    if( $login['us_status'] == '1' && $login['us_deleted'] == '0' )
                    {
                        if( $login['rl_type'] == '1' )
                        {
                            return $this->is_logged_in( $redirect, $default_redirect );
                        }
                        else
                        {
                            redirect( site_url( 'dashboard' ) );
                        }
                    } 
                    else 
                    {
                        $this->logout();
                    }
                }
            }
            if( $redirect ) 
            {
                $this->CI->session->set_flashdata( 'redirect', $redirect );
            }
            if( $default_redirect ) 
            {
                redirect( config_item('theme') . '/login' );
            }
            return false;
        } 
        else 
        { 
            return true;
        }
    }
    public function is_logged_in_common( $redirect = true )
    {
        $session                                = $this->CI->session->userdata('user');
        if( $session ) 
        {
            // $this->notification_count_refresh(array('type' => 'user','data' => $session));
            if( $redirect ) 
            {
                redirect('dashboard');
            } 
            else 
            {
                return 'user';
            }
        }
        $session                                = $this->CI->session->userdata('admin');
        if( $session ) 
        {
            if( $redirect ) 
            {
                redirect('admin');
            } 
            else 
            {
                return 'admin';
            }
        }
        //check the cookie
        if( isset( $_COOKIE[$this->cookie_name] ) ) 
        {
            //the cookie is there, lets log the customer back in.
            $info                               = $this->aes256Decrypt( base64_decode( $_COOKIE[$this->cookie_name] ) );
            $cred                               = json_decode( $info, true );
            if( is_array( $cred ) )
            {   
                $login                          = $this->login_admin( $cred['username'], $cred['password'] );
                if( $login['us_status'] == '1' && $login['us_deleted'] == '0' )
                {
                    return $this->is_logged_in_common( $redirect );
                } 
                else 
                {
                    $this->logout();
                }
            }
        }
        return false;
    }

    public function is_logged_in_user( $redirect = false, $default_redirect = true )
    {
        $session                                 = $this->CI->session->userdata('user');
        if( !$session ) 
        {
            //check the cookie
            if( isset( $_COOKIE[$this->cookie_name] ) ) 
            {
                //the cookie is there, lets log the customer back in.
                $info                           = $this->aes256Decrypt( base64_decode( $_COOKIE[$this->cookie_name] ) );
                $cred                           = json_decode( $info, true );
                if( is_array( $cred ) )
                {
                    $login                      = $this->login_admin( $cred['username'], $cred['password'] );
                    if( $login['us_status'] == '1' && $login['us_deleted'] == '0' )
                    {
                        if( $login['rl_type'] == '1' )
                        {
                            redirect(admin_url());
                        }
                        else
                        {
                            return $this->is_logged_in_user( $redirect, $default_redirect );
                        }
                    } 
                    else 
                    {
                        $this->logout();
                    }
                }
            }
            if( $redirect ) 
            {
                $this->CI->session->set_flashdata('redirect', $redirect);
            }
            if( $default_redirect ) 
            {
                redirect(config_item('theme') . '/login');
            }
            return false;
        } 
        else 
        {
            if(!$this->__validate_user( $session ))
            {
                $this->logout('session_validate');
            }
            
            return true;
        }
    }

    private function __validate_user( $session )
    {
        if(!empty($session['id']) && $session['rl_type']!='1' )
        {
            $restriction                        = $this->CI->settings->setting('restrict_user_login');
            if($restriction['as_setting_value']['setting_value']->login_restricted)
            {
                $objects                        = array();
                $objects['key']                 = 'user_session_id_'.$session['id'];
                $callback                       = 'current_user_session';
                $session                        = $this->CI->memcache->get($objects, $callback, array('id' => $session['id']));
                if( empty( $session ) || empty( $session['us_session_id'] ) || $session['us_session_id'] != session_id() )
                {
                    return false;
                }
                return true;
            }
            return true;
        }
        return true;
    }

    public function logout( $index = 'admin' )
    {
        $this->clear_memcache_index( array( 'profile_blocks' ) );
        $this->CI->session->unset_userdata( 'admin' );
        $this->CI->session->unset_userdata( 'user' );
        $this->generateCookie( '[]', strtotime( '-1 months' ) );
        if($index == 'session_validate')
        {
            $this->CI->session->set_flashdata('error', 'You have been logged out because you have logged in another device!');
        }
        redirect( 'login/redirect' );
    }

    private function clear_memcache_index( $index = array() )
    {
        if( !empty( $index ) )
        {
            foreach( $index as $key )
            {
                $this->CI->memcache->delete( $key );
            }
        }
    }

    public function reset_password( $username )
    {
        $admin                              = $this->get_admin_by_username( $username );
        if( $admin ) 
        {
            $this->CI->load->helper( 'string' );
            $this->CI->load->library( 'email' );
            $new_password                   = random_string( 'alnum', 8 );
            $admin['password']              = sha1( $new_password );
            $this->save_admin( $admin );
            $this->CI->email->from( config_item( 'email' ), config_item( 'site_name' ) );
            $this->CI->email->to( $admin['email'] );
            $this->CI->email->subject( config_item( 'site_name' ) . ': Admin Password Reset' );
            $this->CI->email->message( 'Your password has been reset to ' . $new_password . '.' );
            $this->CI->email->send();
            return true;
        } 
        else 
        {
            return false;
        }
    }

    /*
    This function gets the admin by their username address and returns the values in an array
    it is not intended to be called outside this class
     */
    private function get_admin_by_username( $username )
    {
        $this->CI->load->model( 'Authenticate_model' );
        $result                             = $this->CI->Authenticate_model->get_admin_by_username( $username );
        if( sizeof( $result ) > 0 ) 
        {
            return $result;
        } 
        else 
        {
            return false;
        }
    }

    private function generateCookie( $data, $expire )
    {
        setcookie( $this->cookie_name, $data, $expire, '/', $_SERVER['HTTP_HOST'] );
    }

    private function aes256Encrypt( $data )
    {
        $key                            = config_item( 'encryption_key' );
        if( 32 !== strlen( $key ) ) 
        {
            $key                        = hash( 'SHA256', $key, true );
        }
        $padding                        = 16 - (strlen( $data ) % 16 );
        $data                           .= str_repeat( chr( $padding), $padding );
        return mcrypt_encrypt( MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, str_repeat("\0", 16) );
    }

    private function aes256Decrypt( $data )
    {
        $key                            = config_item( 'encryption_key' );
        if( 32 !== strlen( $key )) 
        {
            $key                        = hash( 'SHA256', $key, true );
        }
        $data                           = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, str_repeat("\0", 16));
        $padding                        = ord( $data[strlen( $data) - 1] );
        return substr( $data, 0, -$padding );
    }
}
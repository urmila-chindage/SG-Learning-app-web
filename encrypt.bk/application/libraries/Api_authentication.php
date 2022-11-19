<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Api_authentication
{
    var $CI;
    function __construct(){

        $this->CI = & get_instance();
        $this->CI->load->driver('cache');
        $this->CI->load->model('User_model');
    }

    public function create_token($params = array())
    {
        
        $phone                  = isset($params['phone'])?$params['phone']:'';
        $email                  = isset($params['email'])?$params['email']:'';
        $response               = array();
        $response['user']       = array();
        $response['token']      = '';
        if(!empty($phone)||!empty($email))
        {
            $this->CI->load->library('JWT');
            $filter_param           = array();
            if(!empty($phone))
            {
                $filter_param['phone']  = $phone;
            }
            elseif(!empty($email))
            {
                $filter_param['email']  = $email;
            }
            
            $filter_param['limit']      = '1';
            $filter_param['select']     = 'id,us_name,us_email,us_phone';
            $check_response             = $this->CI->User_model->check_user_exist($filter_param);
            
            if(!empty($check_response))
            {
                $payload                = array();
                $payload['id']          = isset($check_response['id'])?$check_response['id']:'';
                $payload['email_id']    = isset($check_response['us_email'])?$check_response['us_email']:'';
                $payload['mobile']      = isset($check_response['us_phone'])?$check_response['us_phone']:'';
                $token                  = $this->CI->jwt->encode($payload, config_item('jwt_token')); 
                $user_id                = isset($check_response['id'])?$check_response['id']:'';
                if($token)
                {
                    $data                       = array();
                    $filter                     = array();
                    $data['us_token']           = $token;
                    $filter['update']           = true;
                    $filter['id']               = isset($check_response['id'])?$check_response['id']:0;
                    $this->CI->User_model->save_userdata($data,$filter);
                    $object_key                 = 'userdetails_'.$user_id;
                    $this->CI->memcache->delete($object_key);
                }
                
                $objects            = array();
                $objects['key']     = 'userdetails_'.$user_id;
                $callback           = 'check_user_valid';
                $params             = array('user_id' => $user_id);
                $user               = $this->CI->memcache->get($objects, $callback, $params);
                $response['user']   = $user;
                $response['token']  = $token;
            }
            
        }   
        return $response;     
    }

    public function verify_token($token = '')
    {
        $this->CI->load->library('JWT');
        
        $response                   = array();
        $response['user']           = array();
        $response['token_verified'] = false;
        
        $key                        = config_item('jwt_token');
        $dumpload                   = $this->CI->jwt->decode($token,$key);
        $user_id                    = isset($dumpload->id)?$dumpload->id : '';
        if($user_id != '')
        {
            $objects                = array();
            $objects['key']         = 'userdetails_'.$user_id;
            $callback               = 'check_user_valid';
            $params                 = array('user_id' => $user_id);
            $user                   = $this->CI->memcache->get($objects, $callback, $params);
            $token_exist            = empty($user['us_token'])?'':$user['us_token'];
            if($token_exist == $token)
            {
                $response['user']            = $user;
                $response['token_verified']  = true;
            }
        }
        return $response;
    }

    public function process_img($param = array())
    {
        $user_id                        = isset($param['user_id'])?$param['user_id']:0;
        $image_to_cp                    = ($user_id%11).'.jpg';
        $image_from                     = FCPATH.badge_upload_path().$image_to_cp;
        $image_to                       = FCPATH.user_upload_path().$user_id.'.jpg';
        if((!empty($image_from)) && (!empty($image_to)))
        {
            if(copy($image_from,$image_to))
            {
                $I_data                     = array();
                $I_data['id']               = $user_id;
                $I_data['us_image']         = $user_id.'.jpg'."?v=".rand(10,1000);
                $this->CI->User_model->save($I_data);

                $has_s3     = $this->CI->settings->setting('has_s3');
                if( $has_s3['as_superadmin_value'] && $has_s3['as_siteadmin_value'] )
                {
                    $user_profile_path = user_upload_path().$user_id.'.jpg';
                    uploadToS3($user_profile_path, $user_profile_path);
                    unlink($user_profile_path);
                }
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
        
    }
}
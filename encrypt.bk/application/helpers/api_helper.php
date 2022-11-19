<?php
/**
 * THE SOFTWARE.
 *
 * @package	Ofabee
 * @author	Enfin Technologies
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/*
purpose     : send response
developed   : kiran
edited      : none
*/
if ( ! function_exists('send_response'))
{
    function send_response($status_code = '404',$headers = array(),$body = array())
    {
        $response               = array();
        $response['metadata']   = set_headers($headers);
        $response['data']       = set_body($body);
        header('Content-Type: application/json');
        // http_response_code($status_code);
        echo json_encode($response);exit;
    }
    
}

/*
purpose     : set headers for response
developed   : kiran
edited      : none
*/
if ( ! function_exists('set_headers'))
{
    function set_headers($headers = array())
    {
        $response_header = array( 'error' => false, 'message' => '' );
        if(!empty($headers))
        {
            foreach($headers as $header_key => $header_value)
            {
                $response_header[$header_key] = $header_value;
            }
        }
        return $response_header;
    }
}

/*
purpose     : set body for response
developed   : kiran
edited      : none
*/
if ( ! function_exists('set_body'))
{
    function set_body($body = array())
    {
        $response_body = array();
        if(!empty($body))
        {
            foreach($body as $body_key => $body_value)
            {
                $response_body[$body_key] = $body_value;
            }
        }
        return (!empty($response_body))?$response_body:(object)$response_body;
    }
}

/*
purpose     : search item in array
developed   : kiran
edited      : none
*/
// if ( ! function_exists('search_item'))
// {
//     function search_item($needle = '',$slug = '',$haystack = array())
//     {
//         $search_response = array();
//         if(!empty($haystack) && !empty($slug))
//         {
//             foreach($haystack as $item_key => $item_value)
//             {
//                 if(strpos(strtolower($item_value[$slug]), strtolower($needle)) !== false)
//                 {
//                     array_push($search_response, $item_value);
//                 }
//             }
//         }
//         return $search_response;
//     }
// }

if ( ! function_exists('search_item'))
{
    function search_item($needle = '',$slug = '',$haystack = array())
    {
        $search_response = array();
        if(!empty($haystack) && !empty($slug))
        {
            foreach($haystack as $item_key => $item_value)
            {
                if(strpos(strtolower($item_value[$slug]), strtolower($needle)) !== false)
                {
                    array_push($search_response, $item_value);
                }
            }
        }
        return $search_response;
    }
}
/*
purpose     : search category in array
developed   : kiran
edited      : none
*/
if(! function_exists('category_search'))
{
    function category_search($category_ids = '',$slug = '',$haystack = array())
    {
        $search_response        = array();
        $search_result          = array();
        $search_others          = array();
        $category_ids           = explode(',',$category_ids);
        foreach($haystack as $item_key => $item_value)
        {
            $item_categories    = explode(',',$item_value[$slug]);
            $category_exist     = !empty(array_intersect($category_ids, $item_categories));
            if($category_exist)
            {
                array_push($search_result, $item_value);
            }
            else
            {
                array_push($search_others, $item_value);
            }
        }
        $search_response        = array_merge($search_result,$search_others);
        return $search_response;
    }
}

/*
purpose     : send sms for mobile api
developed   : kiran
edited      : none
*/
if(! function_exists('send_sms'))
{
    function send_sms($params)
    {
        $mobile     = isset($params['phone'])?$params['phone']:'';
        $otp        = isset($params['otp'])?$params['otp']:'';
        $hash_key   = isset($params['hash_key'])?$params['hash_key']:'';
        $CI         = & get_instance();
        $CI->load->library(array('ofabeesms'));

        if($mobile)
        {
            $sms_params                 = array();
            $sms_params['number']       = $mobile;
            $sms_params['message']      = 'Your Otp is: '.$otp;
            if($hash_key)
            {
                $sms_params['message']  = '<#> Your Otp is: '.$otp.' '.$hash_key;
            }
            
            $CI->ofabeesms->send_trans_sms($sms_params);
        }       
    }
    
}

/*
purpose     : send email for mobile api
developed   : kiran
edited      : none
*/
if(! function_exists('send_email'))
{
    function send_email($params)
    {
        $email = isset($params['email'])?$params['email']:'';
        $otp    = isset($params['otp'])?$params['otp']:'';
        $CI     = & get_instance();
        if($email != '')
        {
            $email_params               = array();
            $email_params['from']       = config_item('site_name');
            $email_params['to']         = $email;
            $email_params['subject']    = 'Verify Otp';        
            $email_params['body']       = 'Your Otp is: '.$otp;  
            $CI->ofabeemailer->send_mail($email_params);
        }
    }
}

/*
purpose     : generate otp for sms
developed   : kiran
edited      : none
*/
if(! function_exists('generate_otp'))
{
    function generate_otp()
    {
        $generator      = "1357902468";
        $otp            = '';
        for ($i = 1; $i <= 5; $i++) 
        { 
            $otp       .= substr($generator, (rand()%(strlen($generator))), 1); 
        } 
        return $otp;
    }
}
/*
purpose     : generate otp for sms
developed   : kiran
edited      : none
*/
if(! function_exists('make_new_directory'))
{
    function make_new_directory($path = false)
    {
        if(!$path)
        {
            return false;
        }
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }
}
    

<?php 
class Ofabeemessenger
{    
    var $CI;
    var $sms_keys;
    function __construct()
    {
        $this->CI =& get_instance();
    }
        
    /*
     * method to send sms
     * params details for send_sms
     *  phone_number    ===> single mail id or multiple mail ids(variable or an array)      
     *  message         ===> basically a message body
     */
    public function send_sms( $request = array() )
    {
       $response            = array();
       $response['success'] = true;
       $response['message'] = 'SMS send successfully';
       
       //checking whether the website has previlagge to send the sms
       $sms_settings = $this->CI->settings->setting('has_sms');
       
       if($sms_settings['as_superadmin_value'] == 0)
       {
            $response['success'] = false;
            $response['message'] = 'Super Admin disabled this feature. Please contact super admin for further details.';           
            return $response;
       }

       if($sms_settings['as_siteadmin_value'] == 0)
       {
            $response['success'] = false;
            $response['message'] = 'Site Admin disabled this feature.';           
            return $response;
       }
       //End
       
       $this->sms_keys = $sms_settings['as_setting_value']['setting_value'];
       $this->set_request_variable($request);     

       //Sending SMS
       $ch 		= curl_init(); 
       $headers 	= array("User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.0.8) Gecko/20061025 Firefox/1.5.0.8"); 
       curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
       curl_setopt($ch, CURLOPT_URL, $this->get_api_url()); 
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
       $json_response	= curl_exec($ch); 
       $response	= json_decode($json_response);
       $submitted  	= true;
       if($submitted != 101 || $submitted != 102 || $submitted != 103 || $submitted != 104 || $submitted != 105 || $submitted != 106 || $submitted != 107 || $submitted != 108 || $submitted != 109 || $submitted != 110)
       {
           
       }
       else
       {
            $response['success'] = false;
            $response['message'] = 'SMS not send';
       }
       curl_close($ch);
       //End
       return $response;
    }
    
    private function get_api_url()
    {
        return "http://alerts.sinfini.com/api/v3/index.php?method=sms&api_key=".$this->get_sms_value('api_key')."&to=".$this->phone_number."&sender=".$this->get_sms_value('sender')."&message=".$this->message."&unicode=1"; 
    }
    
    /*
     * method to slice array index to variable
     * request       ===> this is an array contains the parameter      
     */
    private function set_request_variable($request)
    {
        if(sizeof($request))
        {
            foreach ($request as $key => $value) 
            {
                $this->$key = $value;
            }
        }
    }
    
    private function get_sms_value($key)
    {
        return $this->sms_keys->$key;
    }
}
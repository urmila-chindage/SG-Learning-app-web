<?php 
// require $_SERVER['DOCUMENT_ROOT'].'/application/libraries/vendor/autoload.php';
require_once APPPATH."third_party/vendor/autoload.php";
use Aws\Ses\SesClient;
class Ofabeemailer
{    
    var $CI;
    var $mail_keys;
    function __construct()
    {
        $this->CI =& get_instance();
    }
    function send_mail($request = array() )
    {
        /*$data['smtp_account']    = $this->CI->settings->setting('has_smtp');
        if( $data['smtp_account']['as_superadmin_value'] && $data['smtp_account']['as_siteadmin_value'] )
        {
            return $this->send_mail_smtp( $request );
        }*/
        $data['s3_mail_account']     = $this->CI->settings->setting('has_mail');
        if( $data['s3_mail_account']['as_superadmin_value'] && $data['s3_mail_account']['as_siteadmin_value'] )
        {
            //return $this->send_mail_aws( $request, $data['s3_mail_account']);
            return $this->send_mail_aws( $request );
        }
        else
        {
            $response['success'] = false;
            $response['message'] = 'Admin disabled this feature. Please contact super admin for further details.';           
            return $response;
        }
    }
    /*
     * method to send mail old function
     * params details for send_mail old function
     *  to       ===> single mail id or multiple mail ids(variable or an array)      
     *  from     ===> send mail id
     *  subject  ===> mail subject
     *  body     ===> basically a message body
     */
    private function send_mail_aws( $request = array() )
    { 
        $mail_settings      = $this->CI->settings->setting('has_mail');
        $mailTo             = $mail_settings['as_setting_value']['setting_value']->mail_email; // live
        // $mailTo             = 'info@'.get_server_identifier(); //testing. comment when live
        $this->modified_to  = $mailTo;
        $this->curl_url     = site_url('homepage/send_aws_mail_thread'); 
        $this->set_request_variable($request);
        $to                 = isset($this->to)?$this->to:array(); 
        $cc                 = isset($this->cc)?$this->cc:array(); 
        $bcc                = isset($this->bcc)?$this->bcc:array(); 
        $to                 = is_array($to)?$to:array($to); 
        $all_mails          = array_merge($to, $cc, $bcc); 
        //echo "<pre>";print_r($all_mails);exit;
        //modify input $request
        $request['to']      = (isset($this->force_recipient) && $this->force_recipient == true)? '' : $this->modified_to;
        unset($request['cc']); 
        //end
        $request['is_curl'] = true; 
        $request['bcc']     = json_encode($all_mails);
        $curlHandle         = curl_init($this->curl_url);
        // echo '<pre>'; print_r($curlHandle);die; 
        $defaultOptions     = array (
                                CURLOPT_POST => 1,
                                CURLOPT_POSTFIELDS => $request,
                                CURLOPT_RETURNTRANSFER => false ,
                                CURLOPT_TIMEOUT_MS => 1000,
                             );
        curl_setopt_array($curlHandle , $defaultOptions);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, FALSE);     
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2); 
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array(
            'request-token: '.sha1(config_item('acct_domain').config_item('id')),
        ));
        // echo config_item('acct_domain');
        // echo config_item('id');
        $response = curl_exec($curlHandle);
       
        curl_close($curlHandle);
        // if((config_item('id') == 6) || (config_item('id') == 27))
        // {
        //    echo "<pre>"; print_r($response);die; 
        // }
        return array('success' => true, 'message' => 'Message send success');
    }
    
    function send_aws_mail_thread( $request = array() )
    {
        $request['bcc'] = !is_array($request['bcc'])?(array)$request['bcc']:$request['bcc'];
        $request['bcc'] = array_unique($request['bcc']);
        
        if (($key = array_search($request['to'], $request['bcc'])) !== false) {
            unset($request['bcc'][$key]);
        }
        
        
        $this->set_request_variable($request);
        $destinations   = array_chunk($this->bcc, 40);
        $queue_size     = sizeof($destinations);
        if($queue_size>1)
        {
            for($i=0; $i<$queue_size;$i++)
            {   
                $request['bcc'] = $destinations[$i];
                $this->send_mail_aws($request);
                //sleep(2);
            }
        }
        else
        {
            
            if(isset($destinations[0]) && sizeof($destinations[0]) == 1)
            {
                $request['to']      = $destinations[0][0];
                $request['bcc']     = array();
            }
            else
            {
                $request['bcc']      = isset($destinations[0]) ? $destinations[0] : array();
            }
            //echo '<pre>'; print_r($request);die;
            $this->set_request_variable($request);
            $response            = array();
            $response['success'] = true;
            $response['message'] = 'Message send successfully';
            
            $mail_settings   = $this->CI->settings->setting('has_mail');
            $this->mail_keys = $mail_settings['as_setting_value']['setting_value'];
            
            $client      = SesClient::factory(array(
                                                 'key'    => $this->get_mail_value('mail_key'),
                                                 'secret' => $this->get_mail_value('mail_secret'),
                                                 'region' => $this->get_mail_value('mail_region')
                                             ));
             if(sizeof($this->bcc)>0 || $this->to != '')
             {
                 $msg                                        = array();
                 $msg['Source']                              = config_item('site_name').'<'.$this->get_mail_value('mail_email').'>'; //$this->from;
                 $msg['Destination']['ToAddresses']          = array($this->to);
                 $msg['Destination']['BccAddresses']         = $this->bcc;
                 $msg['Message']['Subject']['Data']          = $this->subject;
                 $msg['Message']['Subject']['Charset']       = "UTF-8";
                 $msg['Message']['Body']['Text']['Data']     = "Text data of email";
                 $msg['Message']['Body']['Text']['Charset']  = "UTF-8";
                 $msg['Message']['Body']['Html']['Data']     = $this->build_mail_template($this->body);
                 $msg['Message']['Body']['Html']['Charset']  = "UTF-8";
                 try {
                    //echo '<pre>';print_r($msg);die();
                     $result                 = $client->sendEmail($msg);
                    // var_dump($result);die;
                    // if((config_item('id') == 6) || (config_item('id') == 27))
                    // {
                    //     echo '<pre>'; print_r($client);die('s--');
                    // }
                     $response['success']    = true;
                     $response['result']     = $result;
                 } catch (Exception $e) {
                     $response['success'] = false;
                     $response['message'] = $e->getMessage();
                 }
             }
             if(isset($this->is_curl) && $this->is_curl == true)
             {
                //echo 'test1';die();
                 return json_encode($response);
             }
             else
             {
                //echo 'test2';die();
                 return $response;
             }
        }
    }
    /*
     * Replace static content with dynamic content
     */
    public function process_mail_content($contents = array(), $phrase = '')
    {
        $search = array();
        $replace = array();
        if(!empty($contents))
        {
            foreach($contents as $key => $value)
            {
                $search[] = '{'.$key.'}';
                $replace[] = $value;
            }
        }
        return str_replace($search, $replace, $phrase);
    }
    function template($param = array())
    {
        $email_code = isset($param['email_code'])?$param['email_code']:'';
        return $this->CI->memcache->get( 
                                            array(
                                                    'key' => $email_code
                                            ),
                                            'mail_template',
                                            array(
                                                'email_code' => $email_code
                                            )
                                        );
    }
    private function build_mail_template($content = '')
    {
        $logo = config_item('site_logo');
        $logo = ($logo == 'default.png')?base_url('uploads/site/logo/default.png'):logo_path().$logo;
        return '<div style="background-color:#f2f2f2">
                    <center>
                            <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" style="background-color:#f2f2f2">
                            <tbody><tr>
                                    <td align="center" valign="top"  style="padding:40px 20px">
                                    <table border="0" cellpadding="0" cellspacing="0"  style="width:600px">
                                            <tbody><tr>
                                            <td align="center" valign="top">
                                                    <a href="'.site_url().'" title="'.config_item('site_name').'" style="text-decoration:none" rel="noreferrer" target="_blank">
                                                        <img src="'.$logo.'" alt="'.config_item('site_name').'" height="" style="border:0;color:#5fb851!important;font-family:Helvetica,Arial,sans-serif;font-size:60px;font-weight:bold;height:auto!important;letter-spacing:-4px;line-height:100%;outline:none;text-align:center;text-decoration:none;max-width:120px;">
                                                    </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td valign="top" style="padding-top:30px;padding-bottom:30px">
                                                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:#ffffff;border-collapse:separate!important;border-radius:4px">
                                                    <tbody><tr>
                                                        <td valign="top" style="color:#606060;font-family:Helvetica,Arial,sans-serif;font-size:15px;line-height:150%;padding-top:40px;padding-right:40px;padding-bottom:30px;padding-left:40px;">
                                                        '.$content.'
                                                        </td>
                                                    </tr>
                                                </tbody></table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="center" valign="top">
                                                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                    <tbody><tr>
                                                            <td align="center" valign="top"  style="color:#606060;font-family:Helvetica,Arial,sans-serif;font-size:13px;line-height:125%">
                                                                &copy; '.date('Y').' '.config_item('site_name').', All Rights Reserved.
                                                            <br>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                            <td align="center" valign="top" style="padding-top:30px">
                                                        </td>
                                                    </tr>
                                                </tbody></table>
                                            </td>
                                        </tr>
                                    </tbody></table>
                                </td>
                            </tr>
                        </tbody></table>
                    </center>
                </div>';
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
    
    private function get_mail_value($key)
    {
        return $this->mail_keys->$key;
    }
    
    
    /*
     * method to send mail
     * params details for send_mail
     *  to       ===> single mail id or multiple mail ids(variable or an array)      
     *  from     ===> send mail id
     *  subject  ===> mail subject
     *  body     ===> basically a message body
     */
    public function send_mail_smtp( $request = array() )
    {
       require_once('class.phpmailer.php');
       $response            = array();
       $response['success'] = true;
       $response['message'] = 'Message send successfully';
       
       //checking whether the website has previlagge to send the sms
       $mail_settings = $this->CI->settings->setting('has_smtp');
       
       if($mail_settings['as_superadmin_value'] == 0)
       {
            $response['success'] = false;
            $response['message'] = 'Super Admin disabled this feature. Please contact super admin for further details.';           
            return $response;
       }
       if($mail_settings['as_siteadmin_value'] == 0)
       {
            $response['success'] = false;
            $response['message'] = 'Site Admin disabled this feature.';           
            return $response;
       }
       //End
       
       //echo '<pre>'; print_r($mail_settings);
       $this->mail_keys = $mail_settings['as_setting_value']['setting_value'];
       //echo '<pre>'; print_r($request);
       $this->set_request_variable($request);            
       $msg        = array();
       $msg['Source']  = $this->from;
        
       if(sizeof($this->to) > 0 )
       {
           foreach ($this->to as $to)
           {
             $msg['Destination']['ToAddresses'][] = $to;           
           }
       }
        $mail = new PHPMailer();
        $mail->IsSMTP();                          // set mailer to use SMTP
        $mail->Host     =  $this->get_mail_value('host'); // specify main and backup server "smtp.skillsjunxion.com";
        $mail->SMTPAuth = true;     // turn on SMTP authentication
        $mail->Username = $this->get_mail_value('user_name');// SMTP username  "info@skillsjunxion.com";  
        $mail->Password = $this->get_mail_value('password');// SMTP password    "Ictak@Info#2016"; 
        $mail->From     = $this->get_mail_value('user_name');               //    "info@skillsjunxion.com";
        $mail->FromName = $this->from;
        foreach ($this->to as $to) {
            $mail->AddAddress($to);
        }
        //$mail->AddAddress($this->to);
        $mail->AddReplyTo($this->get_mail_value('user_name'), "Information");
        $mail->WordWrap = 50;                                 // set word wrap to 50 characters
        $mail->IsHTML(true);                                  // set email format to HTML
        $mail->Subject = $this->subject;
        $mail->Body    = $this->body;
        //$mail->AltBody = $this->to;
       try{
           $result                 = $mail->Send();
            //$msg_id                 = $result->get('MessageId');
           $response['success']    = true;
        } catch (Exception $e) {
           $response['success'] = false;
           $response['message'] = $mail->ErrorInfo;
       }        
       return $response;
    }
}
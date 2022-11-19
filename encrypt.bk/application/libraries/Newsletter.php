<?php 
class Newsletter
{    
    var $CI;
    var $mail_keys;
    private $_ci;
    function __construct()
    {
        $this->CI =& get_instance();
        $this->_ci =& get_instance();
        $this->_ci->load->library("MailChimp");
        $this->_ci->load->library("Ofabeemailer");
    }
    
    /*
     * method to add an email to list
     * params details for adding a member to list
     *  first_name       ===> Users first name[Must be a string]      
     *  last_name        ===> Users last name[Must be a string]
     *  email_address    ===> Valid email address
     */
    
    function subscribe_mail_service()
    {
        $data['has_mail_subscription']    = $this->CI->settings->setting('has_mail_subscription');
        if( $data['has_mail_subscription']['as_superadmin_value'] && $data['has_mail_subscription']['as_siteadmin_value'] )
        {
            $data['has_mailchimp']    = $this->CI->settings->setting('has_mailchimp');
            if( $data['has_mailchimp']['as_superadmin_value'] && $data['has_mailchimp']['as_siteadmin_value'] )
            {
                return 'mailchimp';
            }

            $data['has_zoho']     = $this->CI->settings->setting('has_zoho');
            if( $data['has_zoho']['as_superadmin_value'] && $data['has_zoho']['as_siteadmin_value'] )
            {
                return 'zoho';
            }
        }else{
            return 'disabled';
        }
    }
    function subscribe_mail($request = array() )
    {
       $data['has_mailchimp']    = $this->CI->settings->setting('has_mailchimp');
       if( $data['has_mailchimp']['as_superadmin_value'] && $data['has_mailchimp']['as_siteadmin_value'] )
        {
            return $this->subscribe_mail_mailchimp( $request );
        }

      $data['has_zoho']     = $this->CI->settings->setting('has_zoho');
      if( $data['has_zoho']['as_superadmin_value'] && $data['has_zoho']['as_siteadmin_value'] )
      {
          return $this->subscribe_mail_zoho( $request);
      }
    }
    function subscribe_mail_mailchimp($request = array()){
        $mailchimp    = $this->CI->settings->setting('has_mailchimp');
        $list_id_json = $mailchimp['as_setting_value']['setting_value'];
        $list_id = $request['list_id'];  //MailChimp List Id
        $mcfname = $request['first_name'];
        $mclname = $request['last_name'];
        $result = $this->_ci->mailchimp->post("lists/$list_id/members", [ 'email_address' => $request['email_address'], 'merge_fields' => ['FNAME'=>$mcfname, 'LNAME'=>$mclname], 'status' => 'subscribed', ]);
        if($result['status']=='400'){
            return "success";
        }
        else if($result['status']=='subscribed'){
            return 'success';
        }else if($result['status']=='404'){
            return '404';
        }else{
            print_r($result);
        }


    }
    function subscribe_mail_zoho($request = array()){
        $zoho    = $this->CI->settings->setting('has_zoho');
        $api_key = $zoho['as_setting_value']['setting_value']->api_key;
        $list_id = $request['list_id'];
        $url = 'https://campaigns.zoho.com/api/json/listsubscribe
';
        $user_credentials = array('First Name'=>$request['first_name'],'Last Name'=>$request['last_name'],'Contact Email'=>$request['email_address']);
        //The JSON data.
        $jsonData = array(
            'authtoken' => $api_key,
            'listkey' => $list_id,
            'scope'=>'CampaignsAPI',
            'version'=>'1',
            'resfmt'=>'[XML/JSON]',
            'contactinfo'=>json_encode($user_credentials)
        );

        //Encode the array into JSON.
        $jsonDataEncoded = json_encode($user_credentials);

        //Initialize connection 
        $ch = curl_init('https://campaigns.zoho.com/api/json/listsubscribe?'); 
        curl_setopt($ch, CURLOPT_VERBOSE, 1);//standard i/o streams 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);// Turn off the server and peer verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//Set to return data to string ($response) 
        curl_setopt($ch, CURLOPT_POST, 1);//Regular post 
        //Set post fields 
        $query = "authtoken={$api_key}&listkey={$list_id}&scope=CampaignsAPI&version=1&resfmt=JSON&contactinfo={$jsonDataEncoded}";
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);// Set the request as a POST FIELD for curl. 
        //Execute cUrl session 
        $response = curl_exec($ch);
        $responses = json_decode($response);
        //print_r($responses);
        if($responses->message=="User successfully subscribed."){
            return 'success';
        }else if($responses->message=='This email address already exists in the list. However, any additional information will be updated in the existing contact.'){
            return 'success';
        }else if(isset($response->Code)){
            if($responses->Code=='2501'){
                return '404';
            }else if($responses->Code=='1007'){
                return '405';
            }
        }else if(isset($response->code)){
            if($responses->code=='2501'){
                return '404';
            }else if($responses->code=='1007'){
                return '405';
            }
        }
        curl_close($ch);
    }



    function create_mail_list($request = array() )
    {

        $data['has_mail_subscription']    = $this->CI->settings->setting('has_mail_subscription');
        if( $data['has_mail_subscription']['as_superadmin_value'] && $data['has_mail_subscription']['as_siteadmin_value'] )
        {
            $data['has_mailchimp']    = $this->CI->settings->setting('has_mailchimp');
            if( $data['has_mailchimp']['as_superadmin_value'] && $data['has_mailchimp']['as_siteadmin_value'] )
            {
                return $this->create_mail_list_mailchimp( $request );
            }

            $data['has_zoho']     = $this->CI->settings->setting('has_zoho');
            if( $data['has_zoho']['as_superadmin_value'] && $data['has_zoho']['as_siteadmin_value'] )
            {
                return $this->create_mail_list_zoho( $request);
            }
        }else{
            return 'disabled';
        }
    }
    function create_mail_list_zoho($request = array()){
        $zoho    = $this->CI->settings->setting('has_zoho');
        $api_key = $zoho['as_setting_value']['setting_value']->api_key;
        $url = 'https://campaigns.zoho.com/api/addlistandcontacts
';
        $listname = $request['list_name'];
        $sub_email = $request['email'];

        //Initialize connection 
        $ch = curl_init('https://campaigns.zoho.com/api/addlistandcontacts?'); 
        curl_setopt($ch, CURLOPT_VERBOSE, 1);//standard i/o streams 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);// Turn off the server and peer verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//Set to return data to string ($response) 
        curl_setopt($ch, CURLOPT_POST, 1);//Regular post 
        //Set post fields 
        $query = "authtoken={$api_key}&scope=CampaignsAPI&resfmt=JSON&listname={$listname}&signupform=private&mode=newlist&emailids={$sub_email}";
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);// Set the request as a POST FIELD for curl.
        //Execute cUrl session 
        $response = curl_exec($ch);
        $responses = json_decode($response);
        if(isset($responses->listkey)){
            $resp = array('list_id'=>$responses->listkey,'service'=>'zoho');
            return $resp;
        }else{
            return '405';
        }
        /*if($responses->message=="User successfully subscribed."){
            return 'success';
        }else{
            return "Already subscribed...!";
        }*/
        curl_close($ch);
    }
    function create_mail_list_mailchimp($request = array()){
        $mailchimp    = $this->CI->settings->setting('has_mailchimp');
        $list_id_json = $mailchimp['as_setting_value']['setting_value'];
        $list_id = $list_id_json->api_key;  //MailChimp List Id
        $result = $this->_ci->mailchimp->post('lists', ['name' => $request['list_name'],'permission_reminder' => 'Customized list.','email_type_option' => false,'contact' => ['company' => 'Online Profesor','address1' =>$this->_ci->config->item('site_address'),'address2' => '','city' => 'Trivandrum','state' => 'Kerala','zip' => '689691','country' => 'IN','phone' => $this->_ci->config->item('site_phone')],'campaign_defaults' => ['from_name' => $this->_ci->config->item('site_name'),'from_email' => $this->_ci->config->item('site_email'),'subject' => 'New Mail Campaign','language' => 'US']]);
        if($result['id']){
            $resp = array('list_id'=>$result['id'],'service'=>'mailchimp');
            return $resp;
        }
    }
    function invite($email_arr,$us_arr){
        $response                  = array();
        $count                     = 0;
        $mail                      = array();
        $response_mail             = '';
        $data['has_invitation']    = $this->CI->settings->setting('has_invitation');
        if( $data['has_invitation']['as_superadmin_value'] && $data['has_invitation']['as_siteadmin_value'] )
        {
            //$this->_ci->load('Ofabeemailer');
            
            //$mail['to'] = $to;
            $mail['from'] = $us_arr['us_name'].'<'.$this->_ci->config->item('site_email').'>';
            $mail['subject'] = $us_arr['us_name'].' Invited you to '.$this->_ci->config->item('site_name');
            $mail['body'] = '<h2>Join '.$us_arr['us_name'].' And your other friends in '.$this->_ci->config->item('site_name').'</h2>'.$data['has_invitation']['as_setting_value']['setting_value']->mail_body;
            foreach ($email_arr as $key => $value){
                $mail['to'][$key] = $value;
                $response_mail = $this->_ci->ofabeemailer->send_mail($mail);
                $count++;
            }
            //echo json_encode($response_mail);die;
            $response['success'] = true;
            $response['message'] = ($count>1)?'Invitaion has been send to '.$count.' users.':'Invitaion send to '.$count.' user.';
            $response['response']= $response_mail;
        }else{
            $response['success'] = false;
            $response['message'] = "Site admin has disabled this feature";
            $response['response']= $response_mail;
        }

        return $response;
    }

    function invite_challenge($email_arr,$us_arr,$challenge){
        $response                  = array();
        $count                     = 0;
        $mail                      = array();
        $response_mail             = '';
        $data['has_cz_invitation']    = $this->CI->settings->setting('has_cz_invitation');
        if( $data['has_cz_invitation']['as_superadmin_value'] && $data['has_cz_invitation']['as_siteadmin_value'] )
        {
            //$this->_ci->load('Ofabeemailer');
            
            //$mail['to'] = $to;
            $ds = new DateTime($challenge['cz_start_date']);
            $de = new DateTime($challenge['cz_end_date']);

            $mail['from'] = $this->_ci->config->item('site_name').'<'.$this->_ci->config->item('site_email').'>';
            $mail['subject'] = 'Challenge Zone Invitaion by '.$us_arr['us_name'];
            $mail['body'] = $us_arr['us_name'].' has invited you to attend the Challenge Zone';
            $mail['body'] .= '<h2>'.$challenge['cz_title'].'</h2>'.'['.$ds->format('M d Y').' '.$ds->format('h:m A').' - '.$de->format('M d Y').' '.$de->format('h:m A').']<br/>'.'Click the <a href="'.site_url('material/challenge/'.$challenge['id']).'">link</a> to participate.';
            foreach ($email_arr as $key => $value){
                $mail['to'][0] = $value;
                $response_mail = $this->_ci->ofabeemailer->send_mail($mail);
                $count++;
            }
            //echo json_encode($response_mail);die;
            $response['success'] = true;
            $response['message'] = ($count>1)?'Invitaion has been send to '.$count.' users.':'Invitaion send to '.$count.' user.';
            $response['succ_count'] = $count;
            $response['response']= $response_mail;
        }else{
            $response['success'] = false;
            $response['message'] = "Site admin has disabled this feature";
            $response['response']= $response_mail;
        }

        return $response;
    }
}
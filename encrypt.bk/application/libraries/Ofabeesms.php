<?php

// class Ofabeesms {

//     private $api_url;
//     private $time;
//     private $unicode;
//     private $working_key;
//     private $sender_id;
//     private $api_method;
//     public $to;
//     private $CI;
//     private $limit;

//     function __construct() {
//         $this->CI          = & get_instance();
//         $this->api_method  = 'sms';
//         $this->working_key = 'gj593pf42o0w1s4b2p5c0880o8mzcm29';
//         $this->sender_id   = '	MAVENS';
//         $this->api_url     = 'http://api-alerts.solutionsinfini.com/v3/';
//     }

//     /**
//      * function to send sms
//      * 
//      */
//     function send_trans_sms($param = array()) {
        
//         $to       = $param['number'];
//         $message  = $param['message'];
//         //return $this->process_sms($to,$message,$dlr_url = "",$type="xml",$time="null",$unicode="null");
//         $this->process_sms($to, $message, $dlr_url  = "", $type     = "xml", $time     = "null", $unicode  = "null");
//         $response = array('success' => true, 'message' => 'sent');
//         return $response;
//     }

//     /**
//      * function to schedule sms
//      * 
//      */
//     function schedule_sms($to, $message, $dlr_url = "", $type = "xml", $time) {
//         $this->process_sms($to, $message, $dlr_url, $type    = "xml", $time, $unicode = '');
//     }

//     /**
//      * function to send unicode message
//      */
//     function unicode_sms($to, $message, $dlr_url = "", $type = "xml", $unicode) {
//         $this->process_sms($to, $message, $dlr_url, $type = "xml", $time = '', $unicode);
//     }

//     /**
//      * function to send out sms
//      * @param string_type $to : is mobile number where message needs to be send
//      * @param string_type $message :it is message content
//      * @param string_type $dlr_url: it is used for delivering report to client
//      * @param string_type $type: type in which report is delivered
//      * @return output		$this->api=$apiurl;
//      */
//     function process_sms($to, $message, $dlr_url = "", $type = "xml", $time = '', $unicode = '') {
//         $message  = urlencode($message);
//         $dlr_url  = urlencode($dlr_url);
//         $this->to = $to;
//         $to       = substr($to, -10);
//         $arrayto  = array("9", "8", "7");
//         $to_check = substr($to, 0, 1);

//         if (in_array($to_check, $arrayto))
//             $this->to = $to;
//         else
//             // echo "invalid number";
//         if ($time == 'null')
//             $time     = '';
//         else
//             $time     = urlencode($time);
//         $time     = "&time=$time";
//         if ($unicode == 'null')
//             $unicode  = '';
//         else
//             $unicode  = "&unicode=$unicode";

//         // $url = "$this->api_url/index.php?method=$this->api_method&api_key=$this->working_key&sender=$this->sender_id&to=$to&message=$message&format=$type&dlr_url=$dlr_url$time$unicode";
//         $url = "$this->api_url/index.php?method=$this->api_method&api_key=$this->working_key&sender=$this->sender_id&to=$to&message=$message&format=$type&dlr_url=$dlr_url$unicode";
//         return $this->execute($url);
//     }

//     /**
//      * function to check message delivery status
//      * string_type $mid : it is message id 
//      */
//     function messagedelivery_status($mid) {
//         $url = "$this->api_url/index.php?method=sms.status&api_key=$this->working_key&id=$mid&format=xml";
//         $this->execute($url);
//     }

//     /**
//      * function to check group message delivery
//      *  string_type $gid: it is group id
//      */
//     function groupdelivery_status($gid) {
//         $url = "$this->start$this->api_url/index.php?method=sms.groupstatus&api_key=$this->working_key&groupid=$gid&format=xml";
//         $this->execute($url);
//     }

//     /**
//      * function to request to clent url
//      */
//     function execute($url) {
        
//         $ch     = curl_init();
//         // curl_setopt($ch, CURLOPT_POST, true);
//         curl_setopt($ch, CURLOPT_URL, $url);
//         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//         $output = curl_exec($ch);
//         // echo "<pre>";print_r($output);exit;
//         curl_close($ch);
//         return $output;
//     }

// }




class Ofabeesms {

        private $api_url;
        private $time;
        private $unicode;
        private $working_key;
        private $sender_id;
        private $api_method;
        public $to;
        private $CI;
        private $limit;
    
        function __construct() {
            $this->CI          = & get_instance();
            // $this->api_method  = 'sms';
            $this->working_key = '5a7f50d08ad8ecfc73ca';
            $this->sender_id   = 'ENFOFA';
            $this->api_url     = 'http://sms.xpresssms.in/api/api.php?ver=1&mode=1&action=push_sms&type=1&route=2&login_name=ofabee&';
        }
    
        /**
         * function to send sms
         * 
         */
        function send_trans_sms($param = array()) 
        {
            $to       = $param['number'];
            $message  = $param['message'];
            //return $this->process_sms($to,$message,$dlr_url = "",$type="xml",$time="null",$unicode="null");
            $this->process_sms($to, $message);
            $response = array('success' => true, 'message' => 'sent');
            return $response;
        }
    
            
        /**
         * function to send out sms
         * @param string_type $to : is mobile number where message needs to be send
         * @param string_type $message :it is message content
         * @param string_type $dlr_url: it is used for delivering report to client
         * @param string_type $type: type in which report is delivered
         * @return output		$this->api=$apiurl;
         */
        function process_sms($to, $message) {

            $message  = urlencode($message);
            $this->to = $to;
            $to       = substr($to, -10);
            $arrayto  = array("9", "8", "7");
            $to_check = substr($to, 0, 1);

            if (in_array($to_check, $arrayto))
            {
                $this->to = $to;
            }
            $url = "$this->api_url&api_password=$this->working_key&message=$message&number=$this->to&sender=$this->sender_id";
            // $url = "$this->api_url/index.php?method=$this->api_method&api_key=$this->working_key&sender=$this->sender_id&to=$to&message=$message&format=$type&dlr_url=$dlr_url$unicode";
            return $this->execute($url);
        }
    
        /**
         * function to request to clent url
         */
        function execute($url) {
            
            $ch     = curl_init();
            // curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $output = curl_exec($ch);
            // echo "<pre>";print_r($output);exit;
            curl_close($ch);
            
            return $output;
        }
    
    }
?>
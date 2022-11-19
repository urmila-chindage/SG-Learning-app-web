<?php 
class Ofabeeevents
{    
    private $CI;
    private $limit;
    function __construct()
    {
        $this->CI = & get_instance();
        $this->CI->load->model(array('Events_model'));
    }
    
    function addEvent($param = array()){
        $event_name             = isset($param['ename'])?$param['ename']:'';
        $event_description      = isset($param['edescription'])?$param['edescription']:'';
        $event_date             = isset($param['edate'])?$param['edate']:'';
        $event_author           = isset($param['user_id'])?$param['user_id']:0;
        $event_id               = isset($param['event_id'])?$param['event_id']:false;

        $insert                 = array();
        $response               = array();
        $response['success']    = true;
        $response['message']    = 'Event creation success';

        if($event_name == ''){
            $response['success']    = false;
            $response['message']    = 'Event name is required';
            return $response;
            exit();
        }

        if($event_author == 0){
            $response['success']    = false;
            $response['message']    = 'Event author is required';
            return $response;
            exit();
        }

        if($event_description == ''){
            $response['success']    = false;
            $response['message']    = 'Event description is required';
            return $response;
            exit();
        }

        if($event_date == ''){
            $response['success']    = false;
            $response['message']    = 'Event date is required';
            return $response;
            exit();
        }

        $insert                 = array('id'=>$event_id,'values'=>array('created_by'=>$event_author,'ev_name'=>$event_name,'ev_description'=>$event_description,'ev_date'=>$event_date,'ev_account'=>config_item('id')));
        $insert_response = $this->CI->Events_model->addEvent($insert);
        if($insert_response == true){
            if($event_id){
                $response['success']    = true;
                $response['message']    = 'Event updated.';
            }else{
                $response['success']    = true;
                $response['message']    = 'Event created.';
                $response['event_id']   = $insert_response;
            }
        }else{
            $response['success']    = false;
            $response['message']    = 'Failed to create or update event.';
        }

        return $response;
    }

    function getEvents($param = array()){
        $user_id    = isset($param['user_id'])?$param['user_id']:false;
        $event_type = isset($param['event_type'])?$param['event_type']:false;
        $event_id   = isset($param['id'])?$param['id']:false;

        $date       = isset($param['date'])?$param['date']:false;
        $date_from  = isset($param['date_from'])?$param['date_from']:false;
        $date_to    = isset($param['date_to'])?$param['date_to']:false;

        $condition  = array();
        $response   = array();

        if(isset($param['user']))
        {
            $user       = $param['user'];
            $institute  = isset($user['institute_id'])?$user['institute_id']:false;
            $batches    = isset($user['batches'])?array_filter(explode(',', $user['batches'])):array();
            $courses    = isset($user['courses'])?$user['courses']:array();    

            if($institute)
            {
                $condition['institute_id']       = $institute;
            }
    
            if(!empty($batches))
            {
                $condition['batches']       = $batches;
            }
    
            if(!empty($courses))
            {
                $condition['courses']       = $courses;
            }
        }



        if($user_id){
            $condition['user_id']       = $user_id;
        }
        if($event_type){
            $condition['event_type']    = $event_type;
        }
        if($date){
            $condition['date']          = $date;
        }
        if($date_from){
            $condition['date_from']     = $date_from;
        }
        if($date_to){
            $condition['date_to']       = $date_to;
        }

        if($event_id){
            $condition['id']            = $event_id;
        }
        
        $events                         = $this->CI->Events_model->getEvents($condition);
        //  echo '<pre>' ; print_r($events);die('lib');
        // echo $this->CI->db->last_query();exit;

         if($events)
         {
            if(count($events)>0){
                $response['success']        = true;
                $response['message']        = 'Successfully fetched.';
                $response['events']         = $events;
            }else{
                $response['success']        = false;
                $response['message']        = 'No events sceduled.';
            }

            return $response;
        }

    }
    
}
?>
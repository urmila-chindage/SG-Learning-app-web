<?php 
Class Events_model extends CI_Model
{	
    function __construct()
    {
        parent::__construct();
    }
    
    function getEventsAdmin($param = array())
    {    
        $count          = isset($param['count'])?$param['count']:false;
        $order_by       = isset($param['order_by'])?$param['order_by']:'events.ev_name';
        $direction      = isset($param['direction'])?$param['direction']:'ASC';
        $keyword        = isset($param['keyword'])?$param['keyword']:false;
        $limit          = isset($param['limit'])?$param['limit']:false;
        $filter         = (isset($param['filter']))?$param['filter']:'';
        $offset         = isset($param['offset'])?$param['offset']:"0";
        $select         = isset($param['select'])?$param['select']:'events.id,events.ev_name,events.ev_description,events.ev_type,events.ev_date,events.ev_time,events.ev_deleted,events.ev_status,events.action,events.action_by,events.created,events.updated';

        $this->db->select($select);
        if(isset($param['id']))
        {
            $this->db->where('events.id',$param['id']);
        }
        if($keyword)
        {
        
            $this->db->like('events.ev_name',$keyword);
        }

        $this->db->order_by($order_by,$direction);
        if($limit)
        {
            $this->db->limit($limit,$offset);
        }

        if($filter != ''){
            switch ($filter) {
                case 'inactive':
                    $this->db->where('events.ev_status','0');
                break;
                
                case 'active':
                    $this->db->where('events.ev_status','1');
                break;
            }
        }
        $this->db->where('ev_account',config_item('id'));
        $this->db->where('events.ev_deleted','0');
        if(isset($param['id']))
        {
            $result = $this->db->get('events')->row_array();
        }
        else
        {
            if($count) 
            {
                $result = $this->db->count_all_results('events');
            } 
            else 
            {
                $result = $this->db->get('events')->result_array();
            }
        }
        //echo $this->db->last_query();die;
        return $result;
    }

    function addEvent($param = array()){
        $values = isset($param['values'])?$param['values']:false;
        $id     = isset($param['id'])?$param['id']:false;
        $return = false;

        if($values){
            if($id){
                $this->db->where('events.id',$id);
                $this->db->where('ev_account',config_item('id'));
                $this->db->update('events',$values);
                $return = true;
            }else{
                $this->db->insert('events',$values);
                $return = $this->db->insert_id();
            }
        }
        return $return;
    }

    function adminEvent($param = array()){
        $event_id       = isset($param['id'])?$param['id']:false;
        $select         = isset($param['select'])?$param['select']:'*';

        $this->db->select($select);

        if($event_id){
            $this->db->where('id',$event_id);
        }
        $this->db->where('ev_account',config_item('id'));
        if($event_id){
            $result = $this->db->get('events')->row_array();
        }else{
            $result = $this->db->get('events')->result_array();
        }

        return $result;
    }

    function institute_batches($params = array())
    {
        $institute_id = isset($params['institute_id']) ? $params['institute_id'] : false;
        $this->db->select('id, gp_name, gp_institute_id, gp_institute_code, gp_year');

        if($institute_id)
        {

            $this->db->where('gp_institute_id',$institute_id);
        }

        $this->db->where(array(
            'gp_account_id' => config_item('id'),
            'gp_deleted' => '0',
            'gp_status' => '1',
        ));
        
        return $this->db->get('groups')->result_array();
    }
    public function get_events($param){

        $select             = isset($param['select'])?$param['select']:'*';
        $id                 = isset($param['event_id'])?$param['event_id']:false;
        $count              = isset($param['count'])?$param['count']:false;
        $date               = isset($param['date'])?$param['date']:false;
        $preference         = isset($param['preference'])?$param['preference']:false;
        $preference_type    = isset($param['preference_type'])?$param['preference_type']:false;

        $this->db->select($select);
        if($id){

            $this->db->where('id',$id);
        }
        if($date){
            $where = "ev_date >= CURDATE()";
            $this->db->where($where);
        }
        if($preference){

            switch($preference_type){

                case 'day':
                    $where = "ev_date = DATE_ADD(CURDATE(),INTERVAL 1 DAY)";
                    $this->db->where($where);
                break;
                case 'week':
                     $where = "ev_date = DATE_ADD(CURDATE(),INTERVAL 7 DAY)";
                    $this->db->where($where);
                break;
                case 'month':
                    $where = "ev_date = DATE_ADD(CURDATE(),INTERVAL 31 DAY)";
                    $this->db->where($where);
                break;

            }
        }
        $result = $this->db->get('events');
        // echo $this->db->last_query();exit;
        if($count == '1'){

            return $result->row_array();
        }else{
            return $result->result_array();
        }
            
    }

    function getEvents($param = array()){
        //echo '<pre>' ; print_r($param);die;
        $institute  = isset($param['institute_id'])?$param['institute_id']:false;
        $batches    = isset($param['batches'])?$param['batches']:array();
        $courses    = isset($param['courses'])?$param['courses']:array();

        $user_id    = isset($param['user_id'])?$param['user_id']:false;
        $event_type = isset($param['event_type'])?$param['event_type']:false;
        $event_id   = isset($param['id'])?$param['id']:false;

        $date       = isset($param['date'])?$param['date']:false;
        $date_from  = isset($param['date_from'])?$param['date_from']:false;
        $date_to    = isset($param['date_to'])?$param['date_to']:false;

        $this->db->select('events.id,events.ev_name,events.ev_description,events.ev_type,events.ev_date,events.ev_time,events.created,CONCAT(events.ev_date," ",events.ev_time) AS events_date_time, events.ev_course_id, events.ev_batch_id, events.ev_institute_id');
        // $this->db->join('events','event_participants.ep_event_id = events.id','left');
        // if($user_id){
        //     $this->db->where('event_participants.ep_user_id',$user_id);
        // }

        $where = array();
        if($institute)
        {
            $where[] = "CONCAT(',', events.ev_institute_id, ',') LIKE ('%,".$institute.",%')";
        }
        if(!empty($batches))
        { //print_r($batches); die;
            $where_temp = array();
            foreach($batches as $batch)
            {
                $where_temp[] = "CONCAT(',', events.ev_batch_id, ',') LIKE ('%,".$batch.",%')";
            }
            $where[] = "(".implode(' OR ', $where_temp).")";
        }
        if(!empty($courses))
        {
            $where_temp = array();
            foreach($courses as $course)
            {
                $where_temp[] = "CONCAT(',', events.ev_course_id, ',') LIKE ('%,".$course.",%')";
            }
            $where[] = "(".implode(' OR ', $where_temp).")";
        }
        
        if(!empty($where))
        {
            $this->db->where("(".implode(' OR ', $where).")");
        }

        if($event_type){
            $this->db->where('events.ev_type',$event_type);
        }

        if($event_id){
            $this->db->where('events.id',$event_id);
        }

        if($date){
            $this->db->where('events.ev_date',$date);   
        }

        if($date_from && $date_to){
            $this->db->where('events.ev_date >=', $date_from);
            $this->db->where('events.ev_date <=', $date_to);
        }

        if($date_from && (!$date_to)){
            $this->db->where('events.ev_date >=', $date_from);
            //$this->db->where('events.ev_date <=', $date_to);
        }

        $this->db->where('events.ev_account',config_item('id'));
        $this->db->where('events.ev_deleted','0');
        $this->db->where('events.ev_status','1');

        if($event_id){
            $response = $this->db->get('events')->row_array();
        }else{
            $response = $this->db->get('events')->result_array();
        }
        //echo $this->db->last_query();die;
        return $response;
    }

    function delete_event($param = array())
    {
        $event_id = isset($param['event_id'])?$param['event_id']:0;
        $this->db->where('id', $event_id);
        if( $event_id )
        {
            $this->db->delete('events');
        }
        else
        {
            return false;
        }
    }
}
<?php 
Class Challenge_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Kolkata');
    }

    function challenges($param=array())
    {
        $limit 			= isset($param['limit'])?$param['limit']:0;
        $offset 		= isset($param['offset'])?$param['offset']:0;
        $order_by 		= isset($param['order_by'])?$param['order_by']:'id';
        $direction 		= isset($param['direction'])?$param['direction']:'DESC';
        $status 		= isset($param['status'])?$param['status']:0;
        $deleted        = isset($param['deleted'])?$param['deleted']:0;
        $not_deleted    = isset($param['not_deleted'])?$param['not_deleted']:false;
        $count          = isset($param['count'])?true:false;
        $category_id 	= isset($param['category_id'])?$param['category_id']:'';
        $category_id    = ($category_id == 'uncategorised')?'0':$category_id;
        
        $this->db->select('challenge_zone.*, users.us_name as wa_name_author, web_actions.wa_name, web_actions.wa_code');
        $this->db->join('users', 'challenge_zone.action_by = users.id', 'left');
        $this->db->join('web_actions', 'challenge_zone.action_id = web_actions.id', 'left');
        $this->db->order_by($order_by, $direction);
        
        if($limit>0)
        {
            $this->db->limit($limit, $offset);
        }
        
        if( !$deleted )
        {
            $this->db->where('challenge_zone.cz_deleted', '0'); 
        }
        
        if($not_deleted){
            $this->db->where('challenge_zone.cz_deleted', '0');
        }
        
        if( $category_id != 'all' && $category_id != '' )
        {
            $this->db->where('cz_category', $category_id); 
        }
        
        if($order_by)
        {
            $this->db->order_by($order_by, "desc");
        }
        
        if( $status )
        {
            $this->db->where('cz_status', 1); 
        }

        $this->db->where('challenge_zone.cz_account_id', config_item('id'));

        $result = $this->db->get('challenge_zone');
        if($count){
            return $result->num_rows();
        }else{
            return $result->result_array();
        }
    }
    
    function challenge($param=array())
    {
        if(isset($param['select'])){
            $this->db->select($param['select']);
        }else{
            $this->db->select('challenge_zone.*');
        }
        if( isset($param['status'])) 
        {
                $this->db->where('challenge_zone.cz_status', 1);
        }
        if( isset($param['deleted'])) 
        {
                $this->db->where('challenge_zone.cz_deleted', $param['deleted'] );
        }
        if( isset($param['id'])) 
        {
            $this->db->where('challenge_zone.id', $param['id']);
        }
        $this->db->where('challenge_zone.cz_account_id', config_item('id'));
	   return $this->db->get('challenge_zone')->row_array();	
    }
    
    function save($data)
    {
        $data['cz_account_id'] = config_item('id');
    	if($data['id'])
    	{
                $this->db->where('id', $data['id']);
                $this->db->update('challenge_zone', $data);
                return $data['id'];
            }
    	else
    	{

                $this->db->insert('challenge_zone', $data);
                return $this->db->insert_id();
    	}
    }
    
    function question($param=array())
    {
        if( isset($param['status'])) 
	{
            $this->db->where('q_status', '1');
	}
	if( isset($param['id'])) 
	{
            $this->db->where('id', $param['id']);
	}
    $this->db->where('q_account_id', config_item('id'));
	$result = $this->db->get('questions')->row_array();	
        return $result;
    }
    
    function delete_challenge_question($param=array())
    {
        $this->db->where($param);
        $this->db->delete('challenge_zone_questions');
        return true;
    }
    
    function options($param=array())
    {
        $q_options = isset($param['q_options'])?$param['q_options']:false;
        $q_answer = isset($param['q_answer'])?$param['q_answer']:false;
        if( $q_options ) 
	{
            $this->db->where_in('id', array_map('intval', explode(',', $q_options)));
            $result = $this->db->get('questions_options')->result_array();	
            return $result;
	}
        if( $q_answer ) 
	{
            $this->db->where_in('id', array_map('intval', explode(',', $q_answer)));
            $result = $this->db->get('questions_options')->result_array();	
            return $result;
	}
        //echo $this->db->last_query();die;
    }
    
    function delete_option($option_id)
    {
        $this->db->where('id', $option_id);		
        $this->db->delete('questions_options');
    }
    
    function save_option($data)
    {
	if($data['id'])
	{
            $this->db->where('id', $data['id']);
            $this->db->update('questions_options', $data);
            return $data['id'];
        }
	else
	{
            $this->db->insert('questions_options', $data);
            return $this->db->insert_id();
	}
    }
    
    function save_question($data)
    {
    $data['q_account_id'] = config_item('id');
	if($data['id'])
	{
            $this->db->where('id', $data['id']);
            $this->db->update('questions', $data);
            return $data['id'];
        }
	else
	{
            $this->db->insert('questions', $data);
            return $this->db->insert_id();
	}
    }
    
    function save_challenge_question($param = array())
    {
        $challenge_id   = $param['challenge_id'];
        $question_id    = $param['question_id'];
        if($this->db->where(array('czq_challenge_zone_id'=>$challenge_id, 'czq_question_id'=>$question_id))->count_all_results('challenge_zone_questions')==0)
        {
            $save                           = array();
            $save['czq_challenge_zone_id']  = $challenge_id;
            $save['czq_question_id']        = $question_id;
            $save['czq_status']             = '1';
            $this->db->insert('challenge_zone_questions', $save);
        }
    }
    
    function questions($param=array())
    {
        $challenge_id   = isset($param['challenge_id'])?$param['challenge_id']:0;
        $not_deleted    = isset($param['not_deleted'])?$param['not_deleted']:0;
        $count       	= isset($param['count'])?$param['count']:false;
        
        $this->db->select('questions.*');
        $this->db->join('questions', 'challenge_zone_questions.czq_question_id = questions.id', 'left');

        if( $challenge_id )
        {
            $this->db->where('challenge_zone_questions.czq_challenge_zone_id', $challenge_id);
        }
        
        if( $not_deleted )
        {
            $this->db->where('questions.q_deleted', '0');
        }
        if( $count )
        {
            $result = $this->db->count_all_results('challenge_zone_questions');            
        }
        else{
            $result =  $this->db->get('challenge_zone_questions')->result_array();	
        }
        //echo $this->db->last_query();die;
        return $result;
    }

    function challenge_zone_attempts($param=array()){
        
        $challenge_id   = isset($param['challenge_id'])?$param['challenge_id']:0;
        $attempted_id   = isset($param['attempted_id'])?$param['attempted_id']:false;
        $user_id            = isset($param['user_id'])?$param['user_id']:false;
        $approved           = isset($param['approved'])?$param['approved']:'';
        
        $this->db->select('users.*,users.id as uid, challenge_zone_attempts.cza_challenge_zone_id, challenge_zone_attempts.cza_user_id, challenge_zone_attempts.cza_attempted_date, challenge_zone_attempts.cza_duration, challenge_zone.cz_duration, challenge_zone.id, challenge_zone_attempts.id as attempted_id');
        $this->db->join('users', 'users.id = challenge_zone_attempts.cza_user_id', 'left');
        $this->db->join('challenge_zone', 'challenge_zone.id = challenge_zone_attempts.cza_challenge_zone_id', 'left');
        $this->db->join('categories', 'categories.id = challenge_zone.cz_category', 'left');
        
        /*$this->db->select('users.*,challenge_zone_attempts.cza_attempted_date, challenge_zone_attempts.id as attempted_id, challenge_zone.cz_duration, challenge_zone_attempts.cza_duration');
        $this->db->from('challenge_zone_attempts');
        $this->db->join('users', 'users.id = challenge_zone_attempts.cza_user_id', 'left');
        $this->db->join('challenge_zone', 'challenge_zone.id = challenge_zone_attempts.cza_challenge_zone_id', 'left');
        $this->db->join('categories', 'categories.id = challenge_zone.cz_category', 'left');*/
        
        if($challenge_id){
            $this->db->where('challenge_zone.id', $challenge_id);
        }
        if($attempted_id){
            $this->db->where('challenge_zone_attempts.id', $attempted_id);
        }
        if($user_id){
            $this->db->where('challenge_zone_attempts.cza_user_id', $user_id);
        }
        
        $return = $this->db->get('challenge_zone_attempts')->result_array();
        //echo $this->db->last_query();die;
        return $return;
    }

    function challenge_report($param=array()){
        
        $attempted_id   = isset($param['attempted_id'])?$param['attempted_id']:false;
        $user_id        = isset($param['user_id'])?$param['user_id']:false;
        
        
        $this->db->select('users.*, challenge_zone_attempts.id, challenge_zone_attempts.cza_challenge_zone_id, challenge_zone_attempts.cza_user_id, challenge_zone_attempts.cza_attempted_date, challenge_zone_questions.czq_question_id, questions.q_type, questions.q_question, questions.q_answer, questions.q_options, questions.q_positive_mark, questions.q_negative_mark , challenge_zone_report.czr_question_id, challenge_zone_report.czr_answer, challenge_zone_report.czr_mark, challenge_zone_report.id as czr_id');
        $this->db->join('users', 'users.id = challenge_zone_attempts.cza_user_id', 'left');
        $this->db->join('challenge_zone_questions', 'challenge_zone_questions.czq_challenge_zone_id = challenge_zone_attempts.cza_challenge_zone_id', 'left' );
        $this->db->join('questions','questions.id = challenge_zone_questions.czq_question_id', 'left');
        $this->db->join('challenge_zone_report','challenge_zone_report.czr_question_id = challenge_zone_questions.czq_question_id AND challenge_zone_report.czr_attempt_id = challenge_zone_attempts.id', 'left');
        
        /*$this->db->select('questions.q_type, questions.q_positive_mark, questions.q_negative_mark, questions.q_question, questions.q_options, questions.q_answer, challenge_zone_report.czr_answer, challenge_zone_report.czr_mark, challenge_zone_report.id as czr_id');
        $this->db->from('challenge_zone_attempts');
        $this->db->join('challenge_zone_report','challenge_zone_report.czr_attempt_id = challenge_zone_attempts.id','left');
        $this->db->join('questions','questions.id = challenge_zone_report.czr_question_id','left');*/
        
        if($user_id)
        {
            $this->db->where('users.id', $user_id);     
        }

        if($attempted_id)
        {
            $this->db->where('challenge_zone_attempts.id', $attempted_id);       
        }
        $result = $this->db->get('challenge_zone_attempts')->result_array();
        //echo $attempted_id."----------".$this->db->last_query();die;
        return $result;
    }

    function get_option_value($str){

        $this->db->select('*');
        $this->db->from('questions_options');
        $this->db->where_in('id', explode(',', $str));
        return $this->db->get()->result_array();
    }

    function save_explanatory($czr_id, $czr_mark){

        $this->db->where('id', $czr_id);
        $this->db->update('challenge_zone_report', array('czr_mark' => $czr_mark));
    }

    function check_start_date($param = array()){

        $newdate        = isset($param['newdate'])?$param['newdate']:false;
        $category_id    = isset($param['category_id'])?$param['category_id']:false;
        $account_id     = isset($param['account_id'])?$param['account_id']:false;
        $challenge_id   = isset($param['challenge_id'])?$param['challenge_id']:false;

        $this->db->select('*');
        $this->db->where('cz_category', $category_id);
        $this->db->where('DATE(cz_start_date) <=', $newdate);
        $this->db->where('DATE(cz_end_date) >=', $newdate);
        $this->db->where('cz_status', '1');
        $this->db->where('cz_deleted', '0');
        if($challenge_id){
            $this->db->where('id !=', $challenge_id);
        }
        $this->db->where('cz_account_id', $account_id);
        $result = $this->db->get('challenge_zone');
        if($result->num_rows() > 0){
            return 0;
        }
        else{
            return 1;
        }
    }

    function save_challenge_attempts($data)
    {
        if($data['id'])
        {
            $this->db->where('id', $data['id']);
            $this->db->update('challenge_zone_attempts', $data);
            return $data['id'];
        }
        else
        {
            $this->db->insert('challenge_zone_attempts', $data);
            return $this->db->insert_id();
        }
    }

    function challenge_question_categories($param)
    {
        $challenge_id = isset($param['challenge_id'])?$param['challenge_id']:false;
        if(!$challenge_id)
        {
            return false;
        }
        $query = 'SELECT exam_question.q_category as category_id, questions_category.qc_category_name 
                  FROM (SELECT exam_question.q_category FROM questions exam_question WHERE exam_question.id IN ( SELECT czq_question_id FROM challenge_zone_questions WHERE czq_challenge_zone_id ='.$challenge_id.' ) GROUP BY exam_question.q_category) exam_question 
                  LEFT JOIN questions_category ON exam_question.q_category = questions_category.id ORDER BY category_id ASC';
        return $this->db->query($query)->result_array();
    }

    function save_challenge_report($data)
    {
    if($data['id'])
    {
            $this->db->where('id', $data['id']);
            $this->db->update('challenge_zone_report', $data);
            return $data['id'];
        }
    else
    {
            $this->db->insert('challenge_zone_report', $data);
            return $this->db->insert_id();
    }
    }
    
    function check_challenge($param = array())
    {
        date_default_timezone_set('Asia/Kolkata');
        $today = date('Y-m-d H:i:s');
        
        $challenge_id 	= isset($param['challenge_id'])?$param['challenge_id']:'';
        $user_id 	= isset($param['user_id'])?$param['user_id']:'';
        
        $this->db->select('*');
        $this->db->where('cza_challenge_zone_id', $challenge_id);
        $this->db->where('cza_user_id', $user_id);
        $challenge = $this->db->get('challenge_zone_attempts');
        $count_challenge_attempt = $challenge->num_rows();
        
        return $count_challenge_attempt;
    }


    //Start Alex

    function get_challenge_categories($param = array()){
        if(isset($param['category_id'])){
            $query = 'SELECT '.$param['select'].' FROM categories WHERE id IN (SELECT cz_category FROM challenge_zone where cz_deleted = "0" AND cz_status = "1" AND cz_account_id = '.config_item('id').' AND cz_category ='.$param['category_id'].')';
            return $this->db->query($query)->row_array();
        }else{
            if(isset($param['count'])){
                $query = 'SELECT * FROM categories WHERE id IN (SELECT cz_category FROM challenge_zone where cz_deleted = "0" AND cz_status = "1" AND cz_account_id = '.config_item('id').' GROUP BY cz_category)';
                return $this->db->query($query)->num_rows();
            }else{
                $query = 'SELECT '.$param['select'].' FROM categories WHERE id IN (SELECT cz_category FROM challenge_zone where cz_deleted = "0" AND cz_status = "1" AND cz_account_id = '.config_item('id').' GROUP BY cz_category) ORDER BY id '.$param['direction'].' LIMIT '.$param['offset'].','.$param['limit'];
                return $this->db->query($query)->result_array();
            }
        }
    }

    function get_challenges($param = array()){
        if(isset($param['user_id'])){
            $query = "SELECT challenge_zone.id,(SELECT COUNT(*) FROM challenge_zone_attempts WHERE cza_user_id=".$param['user_id']." AND cza_challenge_zone_id=challenge_zone.id) AS status,challenge_zone.cz_category,challenge_zone.cz_start_date,challenge_zone.cz_start_date,challenge_zone.cz_end_date,challenge_zone.cz_title,categories.ct_name FROM challenge_zone LEFT JOIN categories ON challenge_zone.cz_category = categories.id WHERE cz_status = '1' AND cz_deleted = '0' AND cz_account_id = ".config_item('id')." AND cz_category = ".$param['category_id']." AND challenge_zone.cz_start_date <= '".date('Y-m-d H:i:s')."' ORDER BY cz_start_date DESC LIMIT ".$param['limit'];
        }else{
            $query = "SELECT challenge_zone.id,(SELECT COUNT(*) FROM challenge_zone_attempts WHERE cza_user_id='0' AND cza_challenge_zone_id=challenge_zone.id) AS status,challenge_zone.cz_category,challenge_zone.cz_start_date,challenge_zone.cz_start_date,challenge_zone.cz_end_date,challenge_zone.cz_title,categories.ct_name FROM challenge_zone LEFT JOIN categories ON challenge_zone.cz_category = categories.id WHERE cz_status = '1' AND cz_deleted = '0' AND cz_account_id = ".config_item('id')." AND challenge_zone.cz_start_date <= '".date('Y-m-d H:i:s')."' AND cz_category = ".$param['category_id']." ORDER BY cz_start_date DESC LIMIT ".$param['limit'];
        }

        return $this->db->query($query)->result_array();
    }

    function get_users(){
        $this->db->select('id');
        $this->db->distinct();
        $this->db->from('users');
        return $this->db->get()->result_array();
    }

    //End Alex

    //Thanveer

    function category_subsciption_users($param=array())
    {
        $category_id = isset($param['category_id'])?$param['category_id']:false;
        $return = array();
        if($category_id)
        {
            $query = "SELECT cs_user_id, us_email FROM course_subscription LEFT JOIN users ON course_subscription.cs_user_id = users.id WHERE us_account_id = ".config_item('id')." AND cs_course_id IN (SELECT id FROM course_basics WHERE cb_status = '1' AND cb_deleted = '0' AND cb_category = ".$category_id." ) GROUP BY cs_user_id";
            $return = $this->db->query($query)->result_array();
        }
        return $return;
    }

    //End thanveer
    
    function get_upcomming_challenge()
    {
        $query = "SELECT id, cz_title, cz_category, cz_start_date, cz_end_date FROM challenge_zone WHERE DATE_FORMAT(cz_start_date, '%Y-%m-%d') = DATE_FORMAT(NOW() + INTERVAL 1 DAY, '%Y-%m-%d') AND cz_account_id = ".config_item('id');
        return $this->db->query($query)->result_array();
    }
}

?>

<?php 
Class Plan_model extends CI_Model
{	
    function __construct()
    {
        parent::__construct();
    }
    
    function plan($data=array())
    {
        $week    = isset($data['week'])?$data['week']:false;
        $user_id = isset($data['user_id'])?$data['user_id']:false;
        if($week)
        {
            $this->db->where('sp_week_format', $week);
        }
        if($user_id)
        {
            $this->db->where('sp_user_id', $user_id);
        }
        $result = $this->db->get('study_plans')->row_array();
        return $result;
    }
    
    function plan_report($data=array())
    {
        $user_id        = isset($data['user_id'])?$data['user_id']:false;
        $lecture_ids    = isset($data['lecture_ids'])?$data['lecture_ids']:array();
        $return         = array();
        if($user_id && !empty($lecture_ids))
        {
            $total_lectures = count($lecture_ids);
            $query = '  SELECT (SUM(lecture_log_cp.ll_percentage_new)/COUNT(lecture_log_cp.ll_percentage_new)) as percentage
                        FROM (
                                SELECT ll_user_id, ll_lecture_id, ll_percentage, ll_attempt, 
                                (CASE
                                    WHEN ll_attempt > 1 THEN 100
                                    ELSE ll_percentage
                                END ) AS ll_percentage_new
                        FROM lecture_log lecture_log_cp 
                        WHERE ll_user_id = '.$user_id.' AND ll_lecture_id IN ('. implode(',', $lecture_ids).')
                        ORDER BY ll_user_id ASC ) lecture_log_cp';
            $return = $this->db->query($query)->row_array();
            
        }
        return $return;
    }
    
    function save_plan($data)
    {
        $plan = $this->plan(array('week' => $data['sp_week_format'], 'user_id' => $data['sp_user_id']));
        if($plan)
	{
            $this->db->where('sp_user_id', $data['sp_user_id']);
            $this->db->where('sp_week_format', $data['sp_week_format']);
            $this->db->update('study_plans', $data);
            return $plan['id'];
        }
	else
	{
            $this->db->insert('study_plans', $data);
            return $this->db->insert_id();
	}        

    }
}
?>

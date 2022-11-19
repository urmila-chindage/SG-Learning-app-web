<?php 
Class Push_model extends CI_Model
{	
    function __construct()
    {
        parent::__construct();
    }
	
    function user_messages($param=array())
    {
        $id = isset($param['id'])?$param['id']:0;
	if( $id ) 
	{
            $this->db->where('user_messages.um_user_id', $id);
            return $this->db->get('user_messages')->row_array();	
	}
    }

    function save_user_messages($data)
    {
        $message = $this->user_messages(array('id'=>$data['um_user_id']));
	if($message)
	{
            $this->db->where('um_user_id', $data['um_user_id']);
            $this->db->update('user_messages', $data);
            return $data['um_user_id'];
        }
	else
	{
            $this->db->insert('user_messages', $data);
            return $this->db->insert_id();
	}
    }
    function get_upcomming_challenge()
    {
        $query = "SELECT challenge_zone.id, challenge_zone.cz_title, challenge_zone.cz_category, challenge_zone.cz_start_date, challenge_zone.cz_end_date, accounts.acct_domain FROM challenge_zone LEFT JOIN accounts ON challenge_zone.cz_account_id = accounts.id WHERE cz_status = '1' AND DATE_FORMAT(cz_start_date, '%Y-%m-%d') = DATE_FORMAT(NOW() + INTERVAL 1 DAY, '%Y-%m-%d') ";
        return $this->db->query($query)->result_array();
    }
    
    function category_subsciption_users($param=array())
    {
        $category_id = isset($param['category_id'])?$param['category_id']:false;
        $return = array();
        if($category_id)
        {
            $query = "SELECT cs_user_id, us_email FROM course_subscription LEFT JOIN users ON course_subscription.cs_user_id = users.id WHERE cs_course_id IN (SELECT id FROM course_basics WHERE cb_status = '1' AND cb_deleted = '0' AND cb_category = ".$category_id." ) GROUP BY cs_user_id";
            $return = $this->db->query($query)->result_array();
        }
        return $return;
    }

}
?>

<?php
 class Archive_model extends CI_Model
 {
    function __construct()
    {
        parent::__construct();
    }

    public function save_archive_process($save_params)
    {
        $this->db->insert_batch('subscription_archive', $save_params); 
    } 

    public function subscription_archive_flag($save_params)
    {
        $this->db->trans_start();
        foreach($save_params as $save_param)
        {
            $this->db->where('cs_course_id', $save_param['cs_course_id']);
            $this->db->where('cs_user_id', $save_param['cs_user_id']);
            $this->db->update('course_subscription', $save_param);
        }
        $this->db->trans_complete(); 
    } 

    public function archive_list($param = array())
    {
        $keyword        = isset($param['keyword']) ? $param['keyword'] : false;
        $cs_start_date  = isset($param['cs_start_date']) ? $param['cs_start_date'] : false;
        $cs_end_date    = isset($param['cs_end_date']) ? $param['cs_end_date'] : false;
        $limit          = isset($param['limit']) ? $param['limit'] : 0;
        $offset         = isset($param['offset']) ? $param['offset'] : 0;
        $count          = isset($param['count']) ? $param['count'] : false;
        $select         = isset($param['select']) ? $param['select'] : '*';
        $where          = '';
        $return         = array();
        // var_dump($cs_start_date);die;
        if ($keyword) {
            $where .= ' AND ( subscription_archive.sa_user_name LIKE "%' . $keyword . '%" OR subscription_archive.sa_user_register_number LIKE "%' . $keyword . '%" OR subscription_archive.sa_course_title LIKE "%' . $keyword . '%" OR subscription_archive.sa_course_code LIKE "%' . $keyword . '%" ) ';
        }

        if ($cs_start_date) {
            $where .= ' AND ( subscription_archive.sa_cs_startdate >= "'.$cs_start_date.'")';
        }

        if ($cs_end_date) {
            $where .= ' AND ( subscription_archive.sa_cs_enddate <= "'.$cs_end_date.'")';
        }

        $limit_query = '';
        if (!$count) {
            if ($limit > 0) {
                $limit_query = 'LIMIT ' . $offset . ',' . $limit;
            }
        }
        $this->db->where("(sa_account_id = '0' OR sa_account_id='".config_item('id')."')");
        $query = "SELECT " . $select . " FROM subscription_archive WHERE 1 " . $where . " ORDER BY id desc " . $limit_query;
        if ($count) {
            $return = $this->db->query($query)->num_rows();
        } else {
            $return = $this->db->query($query)->result_array();
        }
        //echo $this->db->last_query();die;
        // if (!$count)
        // {
        //      echo $this->db->last_query();die;
        // }
        return $return;
    }

}
?>
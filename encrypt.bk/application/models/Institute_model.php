<?php
class Institute_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        //$this->exclude_ids = array('2', '1');
        $this->exclude_ids = array(config_item('super_admin'));
    }

    public function super_admin($id = 2)
    {
        return $this->db->get_where('users', array('id' => $id))->row_array();
    }
    public function institutes($param = array())
    {
        $select     = (isset($param['select']) ? $param['select'] : '*');
        $limit      = isset($param['limit']) ? $param['limit'] : 0;
        $offset     = isset($param['offset']) ? $param['offset'] : 0;
        $order_by   = isset($param['order_by']) ? $param['order_by'] : 'institute_basics.id';
        $direction  = isset($param['direction']) ? $param['direction'] : 'DESC';
        $status     = isset($param['status']) ? $param['status'] : '';
        $count      = isset($param['count']) ? $param['count'] : false;
        $not_deleted = isset($param['not_deleted']) ? $param['not_deleted'] : false;
        $keyword    = isset($param['keyword']) ? $param['keyword'] : '';
        $ids        = isset($param['institute_ids']) ? $param['institute_ids'] : '';
        $filter     = isset($param['filter']) ? $param['filter'] : 0;

        $this->db->order_by($order_by, $direction);
        if ($limit > 0) {
            $this->db->limit($limit, $offset);
        }
        if ($keyword) 
        {
            $this->db->where('(institute_basics.ib_name LIKE "%'.$keyword.'%" OR institute_basics.ib_institute_code LIKE "%'.$keyword.'%")');
        }
        if ($not_deleted) {
            $this->db->where('institute_basics.ib_deleted', '0');
        }

        if ($filter) 
        {
            switch ($filter) 
            {
                case 'active':
                    $status = '1';
                    $this->db->where('ib_deleted', '0');
                    break;
                case 'inactive':
                    $this->db->where('ib_deleted', '0');
                    $status = '0';
                    break;
                case 'pending_approval':
                    $this->db->where('ib_deleted', '0');
                    $status = '2';
                    break;
                case 'deleted':
                    $this->db->where('ib_deleted', '1');
                    break;

                default:
                    break;
            }
        }

        if ($status != '') {
            $this->db->where('institute_basics.ib_status', $status);
        }
        if($ids != '')
        {
            $this->db->where_in('institute_basics.id', $ids);
        }
        // $this->db->where('ib_account_id', config_item('id'));

            $this->db->where('(ib_account_id = '.config_item('id').' or ib_account_id = 0)');

        $this->db->select($select);
        $this->db->from('institute_basics');
        if ($count) {
            $result = $this->db->count_all_results();
        } else {
            $result = $this->db->get()->result_array();
        }
        //echo $this->db->last_query();
        return $result;
    }

    public function institute($param = array())
    {
        $select     = (isset($param['select']) ? $param['select'] : '*');
        $status     = isset($param['status']) ? $param['status'] : '';
        $limit      = isset($param['limit'])?$param['limit']:false;

        if (isset($param['name'])) {
            if (isset($param['id'])) {
                $this->db->where('institute_basics.id!=', $param['id']);
            }
            $this->db->like('institute_basics.ib_name', $param['name']);
        }
        if (isset($param['id'])) {
            $this->db->where('institute_basics.id', $param['id']);
        }
        if(isset($param['ib_institute_code'])) {
            if (isset($param['exclude_id'])) {
                $this->db->where('institute_basics.id!=', $param['exclude_id']);
            }
            $this->db->where('institute_basics.ib_institute_code', $param['ib_institute_code']);
        }
        if(isset($param['ib_class_code'])) {
            if (isset($param['exclude_id'])) {
                $this->db->where('institute_basics.id!=', $param['exclude_id']);
            }
            $this->db->where('institute_basics.ib_class_code', $param['ib_class_code']);
        }
        $this->db->select($select);
        $this->db->from('institute_basics');
        if($limit){
            $this->db->limit($limit);
        }
        return $this->db->get()->row_array();
        
    }
    
    public function update_institute($data)
    {
        if(isset($data['id']) && $data['id'])
        {
            $this->db->where('id', $data['id']);
            return $this->db->update('institute_basics', $data);
        }
        elseif(isset($data['ids']) && !empty($data['ids']))
        {
            $ids = $data['ids'];
            $this->db->where_in('id', $ids);
            unset($data['ids']);
            return $this->db->update('institute_basics', $data);            
        }
        else
        {
            return false;
        }
    }
    
    function update_institute_users($data)
    {
        if(isset($data['us_institute_id']))
        {
            $this->db->where('us_institute_id', $data['us_institute_id']);
        }
        if(isset($data['us_institute_ids']) && sizeof($data['us_institute_ids'])>0)
        {
            $us_institute_ids = $data['us_institute_ids'];
            $this->db->where_in('us_institute_id', $us_institute_ids);
            unset($data['us_institute_ids']);
        }
        if(isset($data['us_role_id']))
        {
            $this->db->where('us_role_id', $data['us_role_id']);
        }        
        return $this->db->update('users', $data);
    }

    function save_institute($params)
    {
        $institute           = $params['institute'];
        $user                = $params['user'];

        $this->db->trans_start();

            $this->db->insert('institute_basics', $institute);
            $institute_id               = $this->db->insert_id();
            $user['us_institute_id']    = $institute_id;
            $this->db->insert('users', $user);

        $this->db->trans_complete();

        return ($this->db->trans_status() != FALSE)? $institute_id:FALSE;        
    }

    function get_user_details($param = array())
    {
        $select         = (isset($param['select']) ? $param['select'] : '*');
        $institute_id   = (isset($param['institute_id']) ? $param['institute_id'] : '');
        $role_id        = (isset($param['role_id']) ? $param['role_id'] : '');
        $email_verified = (isset($param['email_verified']) ? $param['email_verified'] : '');
        $this->db->select($select);

        if(isset($param['us_institute_ids']) && sizeof($param['us_institute_ids'])>0)
        {
            $this->db->where_in('us_institute_id', $param['us_institute_ids']);
        }

        if($institute_id != '')
        {
            $this->db->where('us_institute_id', $institute_id);
        }
        if($role_id != '')
        {
            $this->db->where('us_role_id', $role_id);
        }
        else
        {
            $role_ids       = (isset($param['role_ids']) ? $param['role_ids'] : array());
            if(!empty($role_ids))
            {
                $this->db->where_in('us_role_id', $role_ids);
            }
        }

        if($email_verified != '')
        {
            $this->db->where('us_email_verified', $email_verified);
        }
        
        return $this->db->get('users')->result_array();
        // return $this->db->last_query();
    }

    public function get_institutes($params = array())
    {
        $select = (isset($params['select']) ? $params['select'] : '*');
        $this->db->select($select);
        return $this->db->get('institute_basics')->result_array();
    }

    public function get_institute($params = array())
    {
        $ib_institute_id = isset($params['ib_institute_id']) ? $params['ib_institute_id'] : false;
        $id = isset($params['id'])?$params['id']:0;
        $select = (isset($params['select']) ? $params['select'] : '*');
        $this->db->select($select);
        if ($ib_institute_id) {
            $this->db->where('ib_institute_id', $ib_institute_id);
        }
        if ($id) {
            $this->db->where('id', $params['id']);
        }
        return $this->db->get('institute_basics')->row_array();
    }

    function get_branches()
    {
        $return = $this->db->get('branch')->result_array();
        return $return;
    }
    function get_branch($param=array())
    {
        return $this->db->get_where('branch', array('id' => $param['id']))->row_array();
    }

    function institutes_by_columns( $param = array() )
    {
        $select     = isset($param['select'])?$param['select']:'id';
        $classes    = isset($param['classes'])?$param['classes']:array();
        $codes      = isset($param['codes'])?$param['codes']:array();
        
        $this->db->select($select);
        if(!empty($classes))
        {
            $this->db->where_in('ib_class_code', $classes);
        }
        if(!empty($codes))
        {
            $this->db->where_in('ib_institute_code', $codes);
        }
        $return = $this->db->get('institute_basics')->result_array();
        // echo $this->db->last_query();die;
        return $return;
    }

    function insert_institutes_bulk($save_institutes)
    {
        $institutes_chunks  = array_chunk($save_institutes, 100);
        $result             = array();
        if(!empty($institutes_chunks))
        {
            foreach($institutes_chunks as $institutes)
            {
                $this->db->trans_start();
                foreach($institutes as $institute_object)
                {
                    $institute  = $institute_object['institute'];
                    $user       = $institute_object['user'];
            
                    $this->db->insert('institute_basics', $institute);
                    $user['us_institute_id']    = $this->db->insert_id();
                    $this->db->insert('users', $user);
                    $user['id']                 = $this->db->insert_id();
                    $result[]                   = $user;
                }
                $this->db->trans_complete(); 
            }
        }
        return $result;
    }

}

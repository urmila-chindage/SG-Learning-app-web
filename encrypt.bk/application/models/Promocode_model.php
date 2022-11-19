<?php 
Class Promocode_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function save_promocode( $param = array() )
    {
        $id                 = isset($param['id'])?$param['id']:false;
        $return             = false;
        $promocode_params   = array(
                                        'promocode_name' => $param['pc_promo_code_name'],
                                        'exclude_id'     => $id
                                    );
        $promocode          = $this->promocode($promocode_params);
        // checking the uniqueness of the promocode.
        
        $param['pc_account_id'] = config_item('id');
        if(empty($promocode))
        {
            if($id)
            {
                $this->db->where('id',$id);
                $this->db->where('pc_account_id', config_item('id'));
                $this->db->update('promo_code',$param);
                $return            = $id;
                // $promocode_reports = array(
                //                             'pcr_promo_code_id'=> $id,
                //                             'pcr_promo_code'   => $param['pc_promo_code_name'],
                //                             'pcr_maximum_usage'=> $param['pc_user_limit']
                //                             );
                // $this->db->where('pcr_promo_code_id',$id);                          
                // $this->db->update('promo_code_report',$promocode_reports);
            }
            else
            {
                $this->db->insert('promo_code',$param);
                $return            = $this->db->insert_id();
                // $promocode_reports = array(
                //                             'id'               => false,
                //                             'pcr_promo_code_id'=> $this->db->insert_id(),
                //                             'pcr_promo_code'   => $param['pc_promo_code_name'],
                //                             'pcr_user_count'   => 0,
                //                             'pcr_maximum_usage'=> $param['pc_user_limit'],
                //                             'pcr_user_detail'  => ''
                //                             );
                // $this->db->insert('promo_code_report',$promocode_reports);
            }
        }
        return $return;
    }

    function promocode( $param = array() )
    {
        $id                 = isset($param['id'])?$param['id']:false;
        $promocode_name     = isset($param['promocode_name'])?$param['promocode_name']:false;
        $exclude_id         = isset($param['exclude_id'])?$param['exclude_id']:false;
        if($id)
        {
            $this->db->where('id',$id );
        }
        if($promocode_name)
        {
            $this->db->where('pc_promo_code_name',$promocode_name); 
        }
        if($exclude_id)
        {
            $this->db->where_not_in('id', $exclude_id); 
        } 
        $this->db->where('pc_account_id', config_item('id'));
        $result             = $this->db->get('promo_code');
        // echo $this->db->last_query();die;
        return $result->row_array();
    }

    function promocodes( $param = array() )
    {
        $order_by           = isset($param['order_by'])?$param['order_by']:'id';
        $direction          = isset($param['direction'])?$param['direction']:'ASC';
        $keyword            = isset($param['keyword'])?$param['keyword']:false;
        $promocode_names    = isset($param['promocode_names'])?$param['promocode_names']:array();
        $limit              = isset($param['limit'])?$param['limit']:false;
        $offset             = isset($param['offset'])?$param['offset']:"0";
        $count              = isset($param['count'])?$param['count']:false;
        $filter             = isset($param['filter'])?$param['filter']:'';
        $status             = '';

        if($keyword)
        {
            $this->db->like('promo_code.pc_promo_code_name',$keyword);
        }
        if(!empty($promocode_names))
        {
            $this->db->where_in('pc_promo_code_name',$promocode_names);
        }
        if($limit)
        {
            $this->db->limit($limit,$offset);
        }
        if($filter != ''){
            switch ($filter) {
                case 'inactive':
                    $status = '0';
                    $inactive_where = ' ( promo_code.pc_expiry_date >= "'.date('Y-m-d').'" AND ( promo_code.pc_user_limit = 0 OR promo_code.pc_user_count < promo_code.pc_user_limit ) ) ';
                    $this->db->where($inactive_where);
                break;
                
                case 'active':
                    $status = '1';
                    $active_where = ' ( promo_code.pc_expiry_date >= "'.date('Y-m-d').'" AND ( promo_code.pc_user_limit = 0 OR promo_code.pc_user_count < promo_code.pc_user_limit ) ) ';
                    $this->db->where($active_where);
                break;

                case 'expired':
                    $expired_where = ' ( promo_code.pc_expiry_date < "'.date('Y-m-d').'" OR (promo_code.pc_user_limit > 0 AND promo_code.pc_user_count >= promo_code.pc_user_limit ) ) ';
                    $this->db->where($expired_where);
                break;
            }
        }

        $this->db->order_by($order_by, $direction);

        if($status != '')
        {
            $this->db->where('promo_code.pc_status', $status); 
        }

        $this->db->where('pc_account_id', config_item('id'));

        if($count) 
        {
            $result         = $this->db->count_all_results('promo_code');
            return $result;
        }
        else
        {
            $result         = $this->db->get('promo_code');
            return $result->result_array();
        }
    }

    function delete_promocode( $promocode_id )
    {
        $this->db->where('id',$promocode_id);
        $this->db->where('pc_account_id', config_item('id'));
        $this->db->delete('promo_code');

        // $this->db->where('pcr_promo_code_id',$promocode_id);
        // $this->db->delete('promo_code_report');
        return true;
    }

    function save_generated_promocodes( $generated_promocodes = array() )
    {
        //$this->db->insert_batch('promo_code', $generated_promocodes);
        $account_id             =   config_item('id');
        if(!empty($generated_promocodes))
        {
            $this->db->trans_start();
            foreach($generated_promocodes as $generated_promocode)
            {
                $generated_promocode['pc_account_id'] = $account_id;
                $this->db->insert('promo_code', $generated_promocode);
                // $promocode_reports = array(
                //                             'id'               => false,
                //                             'pcr_promo_code_id'=> $this->db->insert_id(),
                //                             'pcr_promo_code'   => $generated_promocode['pc_promo_code_name'],
                //                             'pcr_user_count'   => 0,
                //                             'pcr_maximum_usage'=> $generated_promocode['pc_user_limit'],
                //                             'pcr_user_detail'  => ''
                //                             );
                // $this->db->insert('promo_code_report',$promocode_reports);
            }
            $this->db->trans_complete();
            return true;
        }
        return false;
    }

    function check_valid_promocode( $param = array() )
    {
        //print_r($param);
       
        $this->db->where('pc_promo_code_name',$param['pc_promo_code_name']);
        $this->db->where('pc_account_id', config_item('id'));
        $result                = $this->db->get('promo_code')->row_array();
        $record_result         = false;
        if(!empty($result))
        {
            if($result['pc_expiry_date'] < date('Y-m-d'))
            {
                $record_result     = 'expired';
                return $record_result; 
            }
            if($result['pc_status'] == '0')
            {
                $record_result     = 'deactivated';
                return $record_result;
            }
            if($result['pc_user_limit'] != '0')
            {
                if($result['pc_user_count'] >= $result['pc_user_limit'])
                {
                    $record_result = 'limit_exceeded'; 
                    return $record_result;
                }
            }
            if($result['pc_user_detail'] != '')
            {
                $exist_users = json_decode($result['pc_user_detail'],true);
                $new_user    = json_decode($param['pc_user_detail'],true);
                $user_id     = array_keys($new_user);
                if(array_key_exists($user_id[0], $exist_users))
                {
                    $record_result = 'already_used'; 
                    return $record_result;
                }
            }
        }
        else
        {
            $record_result     = 'invalid';
            return $record_result;
        }
        $record_result     = $result;
        return $record_result;
    }

    function record_promocode_usage( $param = array() )
    {
        $this->db->where('pc_promo_code_name',$param['pc_promo_code_name']);
        $this->db->where('pc_account_id', config_item('id'));
        $result                = $this->db->get('promo_code')->row_array();
        $record_result         = false;

        if(!empty($result))
        {
            if($result['pc_expiry_date'] < date('Y-m-d'))
            {
                $record_result     = 'expired';
                return $record_result; 
            }
            if($result['pc_status'] == '0')
            {
                $record_result     = 'deactivated';
                return $record_result;
            }
            if($result['pc_user_limit'] != '0')
            {
                if($result['pc_user_count'] >= $result['pc_user_limit'])
                {
                    $record_result = 'limit_exceeded'; 
                    return $record_result;
                }
            }
            if($result['pc_user_detail'] != '')
            {
                $exist_users = json_decode($result['pc_user_detail'],true);
                $new_user    = json_decode($param['pc_user_detail'],true);
                $user_id     = array_keys($new_user);
                if(array_key_exists($user_id[0], $exist_users))
                {
                    $record_result = 'already_used'; 
                    return $record_result;
                }
            }
        }
        else
        {
            $record_result     = 'invalid';
            return $record_result;
        }
        if ($result['pc_user_count'] == 0) 
        {
            $user_count    = '1';
            $user_details  = json_decode($param['pc_user_detail'],true);
        }
        else
        {
            $user_count    = $result['pc_user_count'] + 1;
            $user_details  = json_decode($result['pc_user_detail'],true);
            $new_user      = json_decode($param['pc_user_detail'],true);
            $user_id       = array_keys($new_user);
            $user_id       = (isset($user_id))?$user_id[0]:false;
            if($user_id)
            {
                $user_details[$user_id] = $new_user[$user_id];
            }
        }
        
        $promocode_reports = array(
                                    'pc_user_count'      => $user_count,
                                    'pc_user_detail'     => json_encode($user_details)
                                  );
        $this->db->where('pc_promo_code_name',$param['pc_promo_code_name']);
        $this->db->where('pc_account_id', config_item('id'));
        $this->db->update('promo_code',$promocode_reports);
        $record_result     = $result;
        return $record_result;
    }
    
    function change_promocode_status( $promocode_status_params = array() )
    {
        $this->db->where('id', $promocode_status_params['id']);
        $this->db->where('pc_account_id', config_item('id'));
        $this->db->update('promo_code', $promocode_status_params);
        return true;
    }

    function change_promocode_status_bulk( $promocode_status_params = array() )
    {
        if(!empty($promocode_status_params))
        {
            $this->db->trans_start();
            foreach($promocode_status_params as $promocode_status_param)
            {
                $this->db->where('id', $promocode_status_param['id']);
                $this->db->where('pc_account_id', config_item('id'));
                $this->db->update('promo_code', $promocode_status_param);
            }
            $this->db->trans_complete();
            return true;
        }
        return false;

    }

    function delete_promocode_bulk( $promocode_ids = array() )
    {
        if(!empty($promocode_ids))
        {
            $this->db->trans_start();
            foreach($promocode_ids as $promocode_id)
            {
                $this->db->where('id',$promocode_id);
                $this->db->where('pc_account_id', config_item('id'));
                $this->db->delete('promo_code');
            }
            $this->db->trans_complete();
            return true;
        }
        return false;
    }
}

  
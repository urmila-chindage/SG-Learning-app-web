<?php

 class Onboarding_model extends CI_Model
 {
     function __construct()
    {
        parent::__construct();
    }

    public function get_domain_by_name($param = array())
    {
        $this->db->select('*');
        $this->db->from('accounts');
        $this->db->where('acct_domain',$param['domain_name']);
        $query = $this->db->get();
        if ($query->num_rows() >= 1 ) 
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function saveDomainData($data)
    {
        $this->db->insert('accounts', $data);
        return $this->db->insert_id();
    }

    public function saveUserData($data)
    {
        $this->db->insert('users', $data);
        
        return $this->db->insert_id();
    }  

    public function saveInstituteData($data)
    {
        $this->db->insert('institute_basics', $data);
        return $this->db->insert_id();
    } 

    public function setting_skeletons()
    {
        $result	= $this->db->get('settings_keys');
        return $result->result_array();        
    }

    public function account_setting($key_id, $account_id)
    {
        $this->db->select('account_settings.*, settings_keys.sk_key');
        $this->db->from('account_settings');
        $this->db->where(array('account_settings.as_key_id' => $key_id, 'account_settings.as_account_id' => $account_id));
        $this->db->join('settings_keys', 'account_settings.as_key_id = settings_keys.id', 'join');
        $result = $this->db->get();
        return $result->row_array();
    }

    public function saveAccountSettings($data)
    {
        if($data['id'])
        {
            $this->db->where(array('account_settings.id' => $data['id']));
            $this->db->update('account_settings', $data); 
            return true;
        }
        else
        {
            $this->db->insert('account_settings', $data);  
            return true;
        }
    }
    
    public function saveDomainCategories( $categories = array() )
    {
        if(!empty($categories))
        {
            $this->db->trans_start();
            foreach($categories as $category)
            {
                $this->db->insert('categories',$category);
            }
            $this->db->trans_complete();
            return true;
        }
        return false;
    }

    public function account_holder($id)
    {
        $this->db->select('users.*, accounts.acct_domain');
        $this->db->join('accounts', 'users.us_account_id=accounts.id');
        $this->db->where(array('users.us_role_id' => 1, 'users.us_account_id' => $id));
        $result = $this->db->get('users')->row_array();
        return $result;
    }

    public function get_account($id)
    {
        $this->db->select('accounts.*');
        
        $this->db->where(array('accounts.id' => $id));
        $result = $this->db->get('accounts')->row_array();
        return $result;
    }

    public function get_email_status($param = array())
    {
        $this->db->select('id');
        $this->db->from('users');
        $this->db->where('us_email',$param['user_email']);
        $query = $this->db->get();
        if ($query->num_rows() >= 1 ) 
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function saveProfileSettings($param = array())
    {
        $this->db->select('account_settings.*, settings_keys.sk_key');
        $this->db->from('account_settings');
        $this->db->join('settings_keys', 'account_settings.as_key_id = settings_keys.id');
        $this->db->where(array('account_settings.as_account_id' => $param['domain_id'], 'settings_keys.sk_key' => $param['key']));
        $result  = $this->db->get()->row_array();    
        if($result)
        {
            $result['as_setting_value'] = (array)json_decode($result['as_setting_value']);        
        }
        
        return $result;
    }

    public function saveProfileImage($param = array())
    {
        $this->db->where(array('account_settings.id' => $param['id']));
        $this->db->where(array('account_settings.as_account_id' => $param['as_account_id']));
        $this->db->update('account_settings', $param); 
        return true;
    }
 }
?>
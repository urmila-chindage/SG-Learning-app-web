<?php

 class Authenticate_model extends CI_Model
 {
     function __construct()
    {
        parent::__construct();
    }

    	
//get email and password to check authentication
    public function get_exist_email($email)
    {
       	$this->db->select('*');
        $this->db->from('users');
        $this->db->where('us_email',$email);
        $this->db->where('us_account_id',config_item('id'));
        $query = $this->db->get();
        if ($query->num_rows() >= 1 ) 
        {
            return $query->row_array();
        }
        else{
            return false;
        }
    }
    //update the new password
    public function verify_user_data($email,$password,$id)
    {
    	$this->db->set('us_password',$password);
    	$this->db->where('id',$id);
    	$this->db->where('us_email',$email);
    	$query = $this->db->update('users');
    	if ($query) 
    	{
            return TRUE;
    	}else{
            return false;
        }
    }

    public function save_fb_data($data)
    {
        if ($data['id'])
        {
            $this->db->where('id', $data['id']);
            $this->db->update('users', $data);
            return $data['id'];
        }
        else
        {
            $this->db->insert('users', $data);
            return $this->db->insert_id();
        }
    }

    public function saveUserData($data)
    {
        $this->db->insert('users', $data);
        return $this->db->insert_id();
    }

    //activate user account
    function verifyEmailID($key)
    {
        $this->db->select('id,us_name,us_email');
        $this->db->where('md5(us_email)', $key);
        return $this->db->get('users')->row_array();
        //return true;
    }


    //by thanveer and alex for optimizing db conection
    function login_admin($username='', $password='')
    {
        $this->db->select('users.*, roles.rl_name, roles.rl_status, roles.rl_type, rl_deleted, roles.rl_account, roles.id as role_id, rl_full_course,rl_content_types');
        $this->db->from('users');
        //$this->db->where('us_email', $username);
        $this->db->where('(us_email="'.$username.'" OR us_phone="'.$username.'")');
        $this->db->where('us_account_id', $this->config->item('id'));
        $this->db->where('us_password',  sha1($password));
        $this->db->join('roles', 'roles.id = users.us_role_id', 'left');
        $this->db->limit(1);
        $result     = $this->db->get()->row_array();
        return $result;
    }

    function login_user($username='', $password='')
    {
        $this->db->select('users.id, users.us_name, users.us_email, users.us_image, users.us_phone, users.us_phone_verfified, users.us_email_verified, users.us_role_id, users.us_category_id, users.us_status, users.us_deleted');
        $this->db->from('users');
        //$this->db->where('us_email', $username);
        $this->db->where('(us_email="'.$username.'" OR us_phone="'.$username.'")');
        $this->db->where('us_account_id', $this->config->item('id'));
        $this->db->where('us_password',  sha1($password));
        $this->db->limit(1);
        $result     = $this->db->get()->row_array();
        return $result;
    }
    
    function login_superadmin($username, $password, $remember=false)
    {
        //$this->db->select('*');
        $this->db->select('users.*, roles.rl_name, roles.rl_status, roles.rl_type, rl_deleted, roles.rl_account, roles.id as role_id, rl_full_course,rl_content_types');
        $this->db->from('users');
        $this->db->where('us_email', $username);
        // $this->db->where('us_account_id', $this->config->item('id'));
        $this->db->where('us_password',  ($password));
        $this->db->join('roles', 'roles.id = users.us_role_id', 'left');
        $this->db->limit(1);
        $result     = $this->db->get()->row_array();
        // echo $this->db->last_query();
        return $result;
    }

    function get_admin_by_username($username)
    {
        $this->db->select('*');
        $this->db->where('username', $username);
        $this->db->limit(1);
        $result = $this->db->get('admin')->row_array();
        return $result;
    }
    //end

    function get_user_by_field($param=array())
    {
        if(isset($param['email']))
        {
            $this->db->where('LOWER(us_email)', strtolower($param['email']));
        }
        if(isset($param['register_number']))
        {
            $this->db->where('us_role_id', '2');
            $this->db->where('LOWER(us_register_number)', strtolower($param['register_number']));
        }
        if(isset($param['phone_number']))
        {
            $this->db->where('us_phone', $param['phone_number']);
        }

        if(isset($param['mobile']))
        {
            $this->db->or_where('us_phone', $param['mobile']);
        }
        if(isset($param['explicit_user_id']))
        {
            $this->db->where('id!=', $param['explicit_user_id']);
        }
        $this->db->where('us_account_id',config_item('id'));
       
        return $this->db->from("users")->count_all_results();
       
    }

    function get_user_by_email($param=array())
    {
        if(isset($param['email']))
        {
            $this->db->where('us_email', $param['email']);
        }
        return $this->db->from("users")->count_all_results();
    }

    public function validate_user_session($user = array())
    {
        $this->db->select('us_session_id');
        $this->db->where('id', $user['id']);
        return $this->db->get('users')->row_array();
    }
   
 }
?>
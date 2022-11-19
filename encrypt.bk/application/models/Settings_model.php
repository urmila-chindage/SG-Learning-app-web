<?php 
Class Settings_model extends CI_Model
{	
    function settings()
    {
        $this->db->select('account_settings.*, settings_keys.sk_key');
        $this->db->from('account_settings');
        $this->db->where('account_settings.as_account_id', $this->config->item('id'));
        $this->db->join('settings_keys', 'account_settings.as_key_id = settings_keys.id', 'join');
        $result = $this->db->get();
        return $result->result_array();
    }
    function get_banners()
    {
        $this->db->select('banner.*');
        $this->db->from('banner');
        $this->db->where('banner_account_id',config_item('id'));
        $this->db->order_by('id', 'DESC');
        $result = $this->db->get();
        return $result->result_array();
    }

    function get_testimonials($param=array())
    {
        $select     = isset($param['select'])?$param['select']:'testimonials.*';
        $limit      = isset($param['limit'])?$param['limit']:false;
        $featured   = isset($param['featured'])?$param['featured']: false;

        $this->db->select($select);
        $this->db->from('testimonials');
        $this->db->where('t_status','1');
        $this->db->where('t_account_id',config_item('id'));
        $this->db->order_by('id', 'DESC');
        if($featured)
        {
            $this->db->where('t_featured','1');
        }
        if($limit)
        {
            $this->db->limit($limit);
        }
        $result = $this->db->get();
        return $result->result_array();
    }

    function get_settings_id($id)
    {
        $this->db->select('account_settings.*, settings_keys.sk_key');
        $this->db->from('account_settings');
        $this->db->join('settings_keys', 'account_settings.as_key_id = settings_keys.id');
        $this->db->where(array('account_settings.as_key_id' => $id, 'account_settings.as_account_id' => $this->config->item('id')));
        $result = $this->db->get();
        //print $this->db->last_query(); die;
        return $result->row_array();
    }

    function setting($id)
    {
        $this->db->select('account_settings.*, settings_keys.sk_key');
        $this->db->from('account_settings');
        $this->db->where(array('account_settings.id' => $id, 'account_settings.as_account_id' => $this->config->item('id')));
        $this->db->join('settings_keys', 'account_settings.as_key_id = settings_keys.id', 'join');
        $result = $this->db->get();
        return $result->row_array();
    }
    
    function save($data)
    {
        $this->db->where(array('account_settings.id' => $data['id']));
        $this->db->where(array('account_settings.as_account_id' => config_item('id')));
        $this->db->update('account_settings', $data); 
        //print $this->db->last_query(); die;
        return true;
    }

    function change_certificate_status($data)
    {
        $this->db->set('cm_is_active', '0');
        $this->db->where(array('certificate_manage.cm_account_id' => config_item('id')));
        $this->db->update('certificate_manage');

        $this->db->where(array('certificate_manage.cm_account_id' => config_item('id'), 'certificate_manage.id' => $data['id']));
        $this->db->update('certificate_manage', $data); 
        //print $this->db->last_query(); die;
        return true;
    }
    function change_banner_status($data)
    {

        $this->db->set('banner_active', '0');
        $this->db->where(array('banner.banner_account_id' => config_item('id')));
        $this->db->update('banner');

        $this->db->where(array('banner.banner_account_id' => config_item('id'), 'banner.id' => $data['id']));
        $this->db->update('banner', $data); 
        //print $this->db->last_query(); die;
        return true;
    }
    
    

    //Code for support chat controller//
    function get_support_chat_count()
    {   $this->db->where('support_chat_account_id',config_item('id'));
        return $this->db->count_all_results('support_chat');
    }

    function update_support_chat($data)
    {   
        $this->db->update('support_chat', $data);
        return 1;
    }

    function save_support_chat($data)
    {   
        $this->db->insert('support_chat', $data);
        return $this->db->insert_id();
    }

    function save_testimonial($data)
    {   
        if($data['id']) {
            $this->db->where('id', $data['id']);
            $this->db->update('testimonials', $data);
            return $data['id'];
        } 
        else {
            $this->db->insert('testimonials', $data);
            return $this->db->insert_id();
        }
    }

    function save_banner($data)
    {   
        $this->db->insert('banner', $data);
        return $this->db->insert_id();
    }

    function remove_testimonial($id)
    {
        $this->db->select('testimonials.t_image');
        $this->db->from('testimonials');
        $this->db->where(array('testimonials.id' => $id));
        $result = $this->db->get();
        $result->row_array();

        $this->db->where('id', $id);
        $this->db->delete('testimonials');

        //sunlink(base_url().testimonial_path().$result['t_image']);
        return $id;
    }
    
    function get_support_chat_data()
    {   
        $this->db->select('support_chat.*');
        $this->db->from('support_chat');
        $this->db->where('support_chat_account_id',config_item('id'));
        return $this->db->get()->row_array();
    }

    function save_drobox_off($id)
    {
        $this->db->where(array('account_settings.id' => $data['id']));
        $this->db->update('account_settings', $data); 
        //print $this->db->last_query(); die;
        return true;
    }
    
    //by thanveer
    function blocks($param=array())
    {
        $order_by   = isset($param['order_by'])?$param['order_by']:'pb_order';
        $direction  = isset($param['direction'])?$param['direction']:'ASC';
        $limit      = isset($param['limit'])?$param['limit']:0;
        $offset     = isset($param['offset'])?$param['offset']:0;
        $count      = isset($param['count'])?$param['count']:false;
        
        $this->db->order_by($order_by, $direction);
        if($limit>0)
        {
            $this->db->limit($limit, $offset);
        }
        $this->db->where('pb_account_id', config_item('id'));
        if( $count )
        {
            $result = $this->db->count_all_results('profile_blocks');            
        }
        else
        {
            $result = $this->db->get('profile_blocks')->result_array();
        }
        //echo $this->db->last_query();die;
        return $result;
    }
    
    public function block($param=array())
    {
        $id             = isset($param['id'])?$param['id']:false;
        $exclude_id     = isset($param['exclude_id'])?$param['exclude_id']:false;
        $block_name     = isset($param['pb_name'])?$param['pb_name']:false;
        
        if($id) 
    	{
            $this->db->where('id', $id);
    	}
        if($exclude_id) 
    	{
            $this->db->where('id!=', $exclude_id);
    	}
        if($block_name) 
    	{
            $this->db->where('pb_name', $block_name);
    	}
        $this->db->where('pb_account_id', config_item('id'));
        return $this->db->get('profile_blocks')->row_array();
    }
    
    function profile_fields($param = array()) {
        $order_by     = isset($param['order_by']) ? $param['order_by'] : 'pf_order';
        $direction    = isset($param['direction']) ? $param['direction'] : 'ASC';
        $limit        = isset($param['limit']) ? $param['limit'] : 0;
        $offset       = isset($param['offset']) ? $param['offset'] : 0;
        $count        = isset($param['count']) ? $param['count'] : false;
        $block_id     = isset($param['block_id']) ? $param['block_id'] : false;
        $mandatory    = isset($param['mandatory']) ? $param['mandatory'] : false;
        $strict_order = isset($param['strict_order']) ? $param['strict_order'] : false;

        if ($strict_order) {
            $this->db->select('profile_fields.*');
        }
        else {
            $this->db->select('profile_fields.*');
        }

        if ($block_id > 0) {
            $this->db->where('pf_block_id', $block_id);
        }
        if ($limit > 0) {
            $this->db->limit($limit, $offset);
        }
        if ($mandatory) {
            $this->db->where('pf_mandatory', '1');
        }
        if ($strict_order) {
            $this->db->join('profile_blocks', 'profile_fields.pf_block_id = profile_blocks.id', 'left');
            $this->db->order_by('profile_blocks.pb_order ASC, profile_fields.pf_order ASC');
            $this->db->where('pf_block_id!=', '0');
        }
        else {
            $this->db->order_by($order_by, $direction);
        }
        $this->db->where('pf_account_id', config_item('id'));
        if ($count) {
            $result = $this->db->count_all_results('profile_fields');
        }
        else {
            $result = $this->db->get('profile_fields')->result_array();
        }

        // if($strict_order)
        // {
        //     echo $this->db->last_query();die;
        // } 

        return $result;
    }
    
    public function profile_field($param=array())
    {
        $id             = isset($param['id'])?$param['id']:false;
        $exclude_id     = isset($param['exclude_id'])?$param['exclude_id']:false;
        $field_name     = isset($param['field_name'])?$param['field_name']:false;
        $pf_label       = isset($param['pf_label']) ? $param['pf_label'] : false;
        
        if($id) 
    	{
            $this->db->where('id', $id);
        }
        
        if($exclude_id) 
    	{
            $this->db->where('id!=', $exclude_id);
        }
        
        if($field_name) 
    	{
            $this->db->where('pf_name', $field_name);
        }
        
        if($pf_label) 
    	{
            $this->db->where('pf_label', $pf_label);
    	}
        $this->db->where('pf_account_id', config_item('id'));
        $return = $this->db->get('profile_fields')->row_array();
        return $return;
    }
    
    
    function save_profile_field($data)
    {
        $data['pf_account_id'] = config_item('id');
    	if($data['id'])
    	{
            $this->db->where('id', $data['id']);
            $this->db->update('profile_fields', $data);
            return $data['id'];
        }else
    	{
            $this->db->insert('profile_fields', $data);
            return $this->db->insert_id();
    	}
    }
    function save_block($data)
    {
        $data['pb_account_id'] = config_item('id');
    	if($data['id'])
    	{
            $this->db->where('id', $data['id']);
            $this->db->update('profile_blocks', $data);
            return $data['id'];
        }else
    	{
            $this->db->insert('profile_blocks', $data);
            return $this->db->insert_id();
    	}
    }
    
    function delete_field($data)
    {
        //deleting profile fields
        $this->db->where(array('id' => $data['id'], 'pf_account_id' => config_item('id')));
        $this->db->delete('profile_fields');
        
        //deleting values related to this field from the table 
        $this->db->where('upf_field_id', $data['id']);
        $this->db->delete('profile_field_values');
        
        return true;
    }
    
    function delete_block($data)
    {
        //getting the block fields
        $block_fields = $this->profile_fields(array('block_id'=>$data['id']));
        if(!empty($block_fields))
        {
            foreach($block_fields as $block_field)
            {
                //deleting fields and field values related to current field id
                $this->delete_field(array('id' => $block_field['id']));
            }
        }
        //finally delete the block
        $this->db->where('id', $data['id']);
        $this->db->delete('profile_blocks');

        return true;
    }
    
    /*function profile_field_values($param=array())
    {
        $return = array();
        if(isset($param['user_id']) && $param['user_id'] > 0 )
        {
            $this->db->where('upf_user_id', $param['user_id']);
            $return = $this->db->get('profile_field_values')->result_array();
        }
        return $return;
    }*/
    
    function profile_field_values($param=array())
    {
        $return = array();
        if(isset($param['user_id']) && $param['user_id'] > 0 )
        {
            $this->db->select('us_profile_fields');
            $this->db->where('id', $param['user_id']);
            $return = $this->db->get('users')->row_array();
        }
        return $return;
    }
    
    function profile_field_value($param=array())
    {
        $return = array();
        if(isset($param['user_id']) && $param['user_id'] > 0  && isset($param['field_id']) && $param['field_id'] > 0 )
        {
            $this->db->where('upf_user_id', $param['user_id']);
            $this->db->where('upf_field_id', $param['field_id']);
            $return = $this->db->get('profile_field_values')->row_array();
        }
        return $return;
    }
    
    function save_profile_field_value($data)
    {
        $this->db->where('id', $data['id']);
        $this->db->update('users', $data);
        return $data['id'];
	/*if($data['id'])
	{
            $this->db->where('id', $data['id']);
            $this->db->update('profile_field_values', $data);
            return $data['id'];
        }
	else
	{
            $this->db->insert('profile_field_values', $data);
            return $this->db->insert_id();
	}*/
        
    }
    //End
    
    function update_course_category($id,$param)
    {
        $this->db->where('cb_category',$id);
        $this->db->update('course_basics',$param);
        //echo $this->db->last_query();die;
    }
    
    function update_challenge_category($id, $param)
    {
        $this->db->where('cz_category',$id);
        $this->db->update('challenge_zone',$param);
    }
    
    function update_terms_category($id, $param)
    {
        $this->db->where('t_category',$id);
        $this->db->update('terms',$param);
        //echo $this->db->last_query();die;
    }
    
    function update_dailynews_category($id, $param)
    {
        $this->db->where('dnb_category',$id);
        $this->db->update('daily_news_bulletin',$param);
    }
    
    function update_question_category($id, $param)
    {
        $this->db->where('q_category',$id);
        $this->db->update('questions',$param);
    }

    function update_subject_category($id, $param)
    {
        $this->db->where('qs_category_id',$id);
        $this->db->update('questions_subject',$param);
    }
    
    function update_topic_category($id, $param)
    {
        $this->db->where('qt_category_id',$id);
        $this->db->update('questions_topic',$param);
    }
    
    
    function update_certificate($param)
    {
        if($param['id']){
            $this->db->where('cm_account_id',$id);
            $this->db->update('certificate_manage',$param);
        }else
        {
            $this->db->insert('certificate_manage', $param);
            return $this->db->insert_id();
        }
    }
    
    function get_certificate()
    {
        $this->db->select('*');
        $this->db->from('certificate_manage');
        $this->db->where('cm_account_id',config_item('id'));
        $this->db->order_by('id', 'DESC');
        $result = $this->db->get();
        return $result->result_array();
    }
    
    function get_exist_certificate($id)
    {
        $this->db->select('certificate_manage.*');
        $this->db->from('certificate_manage');
        $this->db->where('cm_account_id',$id);
        $result = $this->db->get();
        return $result->result_array();
    }
    
    function save_certificate($data)
    {   
        $this->db->insert('certificate_manage', $data);
        return $this->db->insert_id();
    }
    
    function get_active_certificate()
    {
        $this->db->select('certificate_manage.*');
        $this->db->where('cm_account_id',config_item('id'));
        $this->db->where('cm_is_active', '1');
        $result = $this->db->get('certificate_manage');
        return $result->row_array();        
    }

    function get_mobile_settings()
    {
        $this->db->select('mobile_settings.*');
        $result = $this->db->get('mobile_settings');
        return $result->result_array(); 
    }

    function get_mobile_banners()
    {
        $this->db->select('mobile_banners.*');
        $this->db->from('mobile_banners');
        $this->db->where('mb_account_id',config_item('id'));
        $this->db->order_by('mb_order', 'ASC');
        $result = $this->db->get();
        return $result->result_array();
    }

    function save_mobile_banner($data)
    {   
        $this->db->insert('mobile_banners', $data);
        return $this->db->insert_id();
    }

    function update_mobile_banner_order($id)
    {
        $this->db->where('id !=', $id);
        $this->db->set('mb_order', 'mb_order+1', FALSE);
        $this->db->update('mobile_banners');
    }

    function save_mobile_banner_order($mb_orders = array())
    {
        if(!empty($mb_orders))
        {
            $this->db->trans_start();
            $new_order                  = array();
            foreach($mb_orders as $key=>$mb_order)
            {
                $new_order['mb_order']  = $key;
                $this->db->where('id', $mb_order);
                $this->db->where('mb_account_id', config_item('id'));
                $this->db->update('mobile_banners', $new_order);
            }
            $this->db->trans_complete();
            return true;
        }
        return false;
    }

    function get_mobile_banner($param = array())
    {
        $banner_id      = isset($param['banner_id']) ? $param['banner_id'] : false;
        $select         = isset($param['select']) ? $param['select'] : '*';
        $this->db->select($select);
        if($banner_id)
        {
            $this->db->where('id', $banner_id);
        }
        $this->db->where('mb_account_id', config_item('id'));
        $result = $this->db->get('mobile_banners');
        return $result->row_array();
    }

    function delete_mobile_banner($id)
    {
        if(isset($id))
        {
        $this->db->where('id', $id);
        $this->db->where('mb_account_id', config_item('id'));
        $this->db->delete('mobile_banners');
        return true;
        }
        return false;
    }

    function update_mobile_banner( $param = array() )
    {
        $banner_id              = isset($param['id'])?$param['id']:'';
        if($banner_id)
        {
            $this->db->where('id', $banner_id);
            $this->db->where('mb_account_id', config_item('id'));
            $this->db->update('mobile_banners', $param);
            return true;
        }
        return false;
    }
}
?>
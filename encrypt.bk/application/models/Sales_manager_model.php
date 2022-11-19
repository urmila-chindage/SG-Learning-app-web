<?php
class Sales_manager_model extends CI_Model 
{
	function __construct()
	{
		parent::__construct();		
    }
    
    function items()
    {
        $this->db->order_by('iso_item_sort_order', 'ASC');
        $this->db->order_by('id', 'DESC'); 
        return $this->db->get_where('item_sort_order', array('iso_item_status' => '1', 'iso_item_deleted' => '0', 'iso_account_id' => config_item('id')))->result_array();
    }

    function update_item_position($item_positions)
    {
        if(!empty($item_positions))
        {
            $this->db->trans_start();
            foreach($item_positions as $position => $item)
            {
                $save                        = array();
                $save['id']                  = $item;
                $save['iso_item_sort_order'] = $position;
                $this->db->where('iso_account_id', config_item('id'));
                $this->db->where('id', $item);
                $this->db->update('item_sort_order', $save);
            }
            $this->db->trans_complete();     
        }
    }

    // function update_item_popular_status($popular_status, $item_id)
    // {
    //     $data = array('iso_item_popular' => $popular_status);
    //     $this->db->set($data);
    //     $this->db->where('id', $item_id);
    //     $this->db->where('iso_account_id', config_item('id'));
    //     return $this->db->update('item_sort_order', $data);
    // }

    // function update_item_featured_status($featured_status, $item_id)
    // {
    //     $data = array('iso_item_featured' => $featured_status);
    //     $this->db->set($data);
    //     $this->db->where('id', $item_id);
    //     $this->db->where('iso_account_id', config_item('id'));
    //     return $this->db->update('item_sort_order', $data);
    // }

    function update_item_featured_popular_status($params = array())
    {
        $data            = array();
        $id              = ((isset($params['id'])) ? $params['id'] : false); // sales manager id;
        $item_id         = ((isset($params['item_id'])) ? $params['item_id'] : false); // course or bundle id
        $item_ids        = ((isset($params['item_ids'])) ? $params['item_ids'] : false); // multiple course or bundle ids
        $item_type       = ((isset($params['item_type'])) ? $params['item_type'] : false);
        $featured_status = ((isset($params['featured_status'])) ? $params['featured_status'] : false);
        $popular_status  = ((isset($params['popular_status'])) ? $params['popular_status'] : false);

        if($featured_status !== false)
        {
            $data['iso_item_featured'] = $featured_status;
        }
        if($popular_status !== false)
        {
            $data['iso_item_popular']  = $popular_status;
        }
        $this->db->set($data);
        if($item_type)
        {
            $this->db->where('iso_item_type', $item_type);
        }
        if($id)
        {
            $this->db->where('id', $id);
        }
        if($item_id)
        {
            $this->db->where('iso_item_id', $item_id);
        }
        if($item_ids)
        {
            $this->db->where_in('iso_item_id', $item_ids);
        }
        $this->db->where('iso_account_id', config_item('id'));
        return $this->db->update('item_sort_order', $data);
    }

    function check_popular_item_count()
    {
        $result = array();
        $this->db->select('count(iso_item_popular) as count');
        $this->db->from('item_sort_order');
        $this->db->where(array('iso_item_popular' => 1,'iso_item_status' => '1', 'iso_item_deleted' => '0','iso_account_id' => config_item('id')));
        $result = $this->db->get()->row_array();
        return $result['count'];
    }
    
    function check_featured_item_count()
    {
        $result = array();
        $this->db->select('count(iso_item_featured) as count');
        $this->db->from('item_sort_order');
        $this->db->where(array('iso_item_featured' => 1,'iso_item_status' => '1', 'iso_item_deleted' => '0','iso_account_id' => config_item('id')));
        $result = $this->db->get()->row_array();
        return $result['count'];
    }

}
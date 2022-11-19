<?php 
Class Catalog_model extends CI_Model
{	
    function __construct()
    {
        parent::__construct();
    }
	
    function catalogs($param=array())
    {
        $limit 			= isset($param['limit'])?$param['limit']:0;
        $offset 		= isset($param['offset'])?$param['offset']:0;
        $order_by 		= isset($param['order_by'])?$param['order_by']:'id';
        $direction 		= isset($param['direction'])?$param['direction']:'DESC';
        $status 		= isset($param['status'])?$param['status']:'';

        $category_id 		= isset($param['category_id'])?$param['category_id']:'';
        $category_id            = ($category_id == 'uncategorised')?'0':$category_id;
        $keyword 		= isset($param['keyword'])?$param['keyword']:'';
        $filter 		= isset($param['filter'])?$param['filter']:0;
        
        $this->db->select('catalogs.*, users.us_name, web_actions.wa_name, web_actions.wa_code');
        $this->db->join('users', 'catalogs.action_by = users.id', 'left');
        $this->db->join('web_actions', 'catalogs.action_id = web_actions.id', 'left');
        $this->db->order_by($order_by, $direction);
        if($limit>0)
        {
            $this->db->limit($limit, $offset);
        }

        if( $category_id != 'all' && $category_id != '' )
        {
            $this->db->where('c_category', $category_id); 
        }

        if( $keyword )
        {
            $this->db->like('c_title', $keyword); 
        }

        if( $filter )
        {
            switch ($filter) {
                case 'active':
                    $status = '1';
                    break;
                case 'inactive':
                    $this->db->where('c_deleted', '0'); 
                    $status = '0';
                    break;
                case 'deleted':
                    $this->db->where('c_deleted', '1'); 
                    break;

                default:
                    break;
            }
        }
        
        if( $status != '' )
        {
            $this->db->where('c_status', $status); 
        }

        $this->db->where('c_account_id', config_item('id'));
        $result = $this->db->get('catalogs');
        //echo $this->db->last_query();die;
        return $result->result_array();
    }
    
    function catalog($param=array())
    {
       if( isset($param['status'])) 
	{
            $this->db->where('c_status', 1);
	}
        if( isset($param['name'])) 
	{
            $this->db->like('c_title', $param['name']);
	}
	if( isset($param['id'])) 
	{
            $this->db->where('id', $param['id']);
	}
        $this->db->where('c_account_id', config_item('id'));
	return $this->db->get('catalogs')->row_array();
    }
    
    function catalog_courses($param=array())
    {
        $this->db->select('catalogs.c_courses');
        $this->db->where('id', $param['catalog_id']);
	$result     =  $this->db->get('catalogs')->row_array();	
        $course_ids = ($result['c_courses'])?$result['c_courses']:'0';
        $query = "SELECT course_basics.id, course_basics.cb_title, course_basics.cb_price
                  FROM course_basics
                  WHERE course_basics.id IN (".$course_ids.")";
	$result =  $this->db->query($query)->result_array();	
        //echo '<pre>';print_r($result);die;
        return $result;
    }

    function save($data)
    {
	if($data['id'])
	{
            $this->db->where('id', $data['id']);
            $this->db->update('catalogs', $data);
            return $data['id'];
        }
	else
	{
            $this->db->insert('catalogs', $data);
            return $this->db->insert_id();
	}
    }	
    
    function delete($id, $confirm_delete = false)
    {
        if($confirm_delete)
        {
            $this->db->where('id', $id);		
            $this->db->delete('catalogs');
        }
        else
        {
            $save               = array();
            $save['id']         = $id;
            $save['c_deleted']  = 1;
            $this->save($save);
        }
    }	
}
?>
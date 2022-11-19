<?php 
Class Menu_model extends CI_Model
{	
    function __construct()
    {
        parent::__construct();
    }
    function menus( $param = array() )
    {
        $order_by 		= isset($param['order_by'])?$param['order_by']:'id';
        $direction 		= isset($param['direction'])?$param['direction']:'DESC';
        $select 		= isset($param['select'])?$param['select']:'pages.*';
                
        $this->db->select($select);
        $this->db->join('pages', 'pages.id = menu_manager.mm_item_connected_id', 'left');
        $this->db->where('menu_manager.mm_account_id', config_item('id'));
        $this->db->order_by($order_by, $direction);

        $result = $this->db->get('menu_manager')->result_array();
        return $result;
    }
    public function save($data, $param = array())
    {
        $update = isset($param['update'])?$param['update']:false;
        $data['mm_account_id'] = config_item('id');
        if ($update) {
            $this->db->where('id', $param['id']);
            $this->db->where('mm_account_id', config_item('id'));
            $this->db->update('menu_manager', $data);
            return $id;
        } else {
            $this->db->insert('menu_manager', $data);
            return $this->db->insert_id();
        }
    }
    
    public function save_page_section($data)
    {
        if(!empty($data))
        {
            $this->db->trans_start();
            foreach($data as $page)
            {
                if ($page['id'])
                {
                    $this->db->where('id', $page['id']);
                    $this->db->where('mm_account_id', config_item('id'));
                    $this->db->update('menu_manager', $page);
                }
               
            }
        $this->db->trans_complete();
        }
    }

    public function checkChildExists($id)
    {
        $this->db->select('id');
        $this->db->where('mm_parent_id',$id);
        $this->db->where('mm_account_id', config_item('id'));
        $result = $this->db->get('menu_manager')->num_rows();
        return $result;
    }

    public function get_page_name($id)
    {
       return $this->db->query("SELECT IF(menu_manager.mm_item_connected_id = 0 , '0', (SELECT p_title FROM `pages` WHERE id = menu_manager.mm_item_connected_id)) as page_title FROM `menu_manager` WHERE id = ".$id." and mm_item_type = 'page'")->row_array();

    }

    public function delete($id)
    {
        $this->db->where('id',$id);
        $this->db->where('mm_account_id', config_item('id'));
        $this->db->delete('menu_manager');
        return true;
    }
}
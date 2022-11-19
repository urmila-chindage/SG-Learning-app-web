<?php 
Class Page_model extends CI_Model
{	
    function __construct()
    {
        parent::__construct();
    }
    
    function pages( $param = array() )
    {
        
        $limit 			= isset($param['limit'])?$param['limit']:0;
        $offset 		= isset($param['offset'])?$param['offset']:0;
        $order_by 		= isset($param['order_by'])?$param['order_by']:'id';
        $direction 		= isset($param['direction'])?$param['direction']:'DESC';
        $status 		= isset($param['status'])?$param['status']:'';
        $count       	= isset($param['count']) ? $param['count'] : false;
        $not_deleted    = isset($param['not_deleted']) ? $param['not_deleted']:false;

        $keyword 		= isset($param['keyword'])?$param['keyword']:'';
        $filter 		= isset($param['filter'])?$param['filter']:0;
        $showin         = isset($param['showin'])?$param['showin']:'';
        $select 		= isset($param['select'])?$param['select']:'pages.*';
        
        $p_show_page_in 	= isset($param['p_show_page_in'])!='' ? $param['p_show_page_in']:'';
        
        $this->db->select($select);

        $this->db->order_by($order_by, $direction);
        $this->db->where('pages.p_account_id', config_item('id'));
        if($limit)
        {
            $this->db->limit($limit, $offset);
        }
        if($keyword)
        {
            $this->db->like('pages.p_title', $keyword); 
        }
        if($not_deleted)
        {
            $this->db->where('pages.p_deleted', '0'); 
        }
        if($p_show_page_in != '' )
        {
            $this->db->where('pages.p_show_page_in', $p_show_page_in);             
        }
        if($filter)
        {
            switch ($filter) {
                case 'active':
                    $status = '1';
                    $this->db->where('pages.p_deleted', '0'); 
                    break;
                case 'inactive':
                    $status = '0';
                    $this->db->where('pages.p_deleted', '0'); 
                    break;
                case 'deleted':
                    $this->db->where('pages.p_deleted', '1'); 
                    break;
                default:
                    break;
            }
        }

        if($showin)
        {
            switch ($showin) {
                case 'header':
                    $this->db->where('pages.p_show_page_in', '1'); 
                    break;
                case 'footer':
                    $this->db->where('pages.p_show_page_in', '2'); 
                    break;
                case 'headerfooter':
                    $this->db->where('pages.p_show_page_in', '3'); 
                    break;
                case 'nowhere':
                    $this->db->where('pages.p_show_page_in', '0'); 
                    break;
                default:
                    break;
            }
        }
        
        
        if($status != '')
        {
            $this->db->where('pages.p_status', $status); 
        }
        if($count)
        {
            $result = $this->db->count_all_results('pages');            
        }
        else
        {
            $result = $this->db->get('pages')->result_array();
        }
        //print_r($count);
        //echo $this->db->last_query();die;
        return $result;
    }
    
    function page( $param=array() )
    {
        
        $page_name              = isset($param['name'])?$param['name']:false;
        $id                     = isset($param['id'])?$param['id']:false;
        $status                 = isset($param['status'])?$param['status']:'';
        $select                 = isset($param['select'])?$param['select']:'';
        if(!empty($select))
        {
            $this->db->select($select);
        }
        if($status) 
	    {
            $this->db->where('pages.p_status', 1);
	    }
        if($page_name) 
	    {
            if($id) 
            {
                $this->db->where('pages.id!=', $id);
            }
            $this->db->where('p_title', $page_name);
	    }
	    if($id) 
	    {
            $this->db->where('pages.id', $id);
        }
        $this->db->where('p_account_id', config_item('id'));
        $return = $this->db->get('pages')->row_array();	
        return $return;
    }
    
    function save( $data )
    {
        $data['p_account_id']  = config_item('id');
	    if($data['id'])
	    {
            $this->db->where('id', $data['id']);
            $this->db->where('p_account_id', config_item('id'));
            $this->db->update('pages', $data);
            return $data['id'];
        }
	    else
	    {
            $this->db->insert('pages', $data);
            return $this->db->insert_id();
	    }
    }

    function save_bulk( $page_params = array() )
    {
        if(!empty($page_params))
        {
            $this->db->trans_start();
            foreach($page_params as $page_param)
            {
                $this->db->where('id', $page_param['id']);
                $this->db->where('p_account_id', config_item('id'));
                $this->db->update('pages', $page_param);
            }
            $this->db->trans_complete();
            return true;
        }
        return false;
    }
    
    function save_page_tree_attribute($param)
    {
        $parent_id      = isset($param['parent_id'])?$param['parent_id']:0;
        $category_id    = isset($param['category_id'])?$param['category_id']:'';
        //echo '<pre>'; print_r($param);die;
        $this->db->where('p_parent_id', $parent_id);
        
        if($category_id != '')
        {
            $this->db->set('p_category', $category_id);
            $this->db->update('pages');        
        }

        $this->db->where('p_parent_id', $parent_id);
        $this->db->where('p_account_id', config_item('id'));
        $pages = $this->db->get('pages')->result_array();
        if(!empty($pages))
        {
            foreach ($pages as $page) {
                $param['parent_id']     = $page['id'];
                $this->save_page_tree_attribute($param);
            }
        }
        return true;
    }
    
    function page_tree($param)
    {
        $parent_id      = isset($param['parent_id'])?$param['parent_id']:0;
        $status         = isset($param['status'])?$param['status']:false;
        $category_id    = isset($param['category_id'])?$param['category_id']:false;
        $header         = isset($param['header'])?$param['header']:false;
        $footer         = isset($param['footer'])?$param['footer']:false;
        $not_deleted    = isset($param['not_deleted'])?$param['not_deleted']:false;
        
        if($not_deleted)
        {
            $this->db->where('p_deleted', '0');
        }
        if($status)
        {
            $this->db->where('p_status', $status);
        }
        
        if($header)
        {
            $this->db->where('p_show_page_in', '1');
        }

        if($footer)
        {
            $this->db->where('p_show_page_in', '10');
        }
        
        if($category_id)
        {
            $this->db->where('p_category', $category_id);
        }
        
        $this->db->where('p_parent_id', $parent_id);    
        $this->db->where('p_account_id', config_item('id'));
        
        $page_tree = array();
        $pages = $this->db->get('pages')->result_array();
        if(!empty($pages))
        {
            foreach ($pages as $page) {
                $param['parent_id']     = $page['id'];
                $page['children']       = $this->page_tree($param);
                $page_tree[$page['id']] = $page;
            }
        }
        return $page_tree;
    }

    function save_recently_view($data)
    {
    	// if( $this->db->get_where('recently_view_pages', array('rvp_user_id' => $data['rvp_user_id'], 'rvp_page_id' => $data['rvp_page_id']))->result())
    	// {
        //         $this->db->where(array('rvp_user_id' => $data['rvp_user_id'], 'rvp_page_id' => $data['rvp_page_id']));
        //         $this->db->update('recently_view_pages', $data);
        //     }
    	// else
    	// {
        //         $this->db->insert('recently_view_pages', $data);
    	// }
        return true;
    }

    //Code for survey controller//
    function save_survey($data)
    {   
        $data['s_account_id']   = config_item('id');
        $this->db->insert('survey', $data);
        return $this->db->insert_id();
    }
    function update_survey($data)
    {   
        $this->db->update('survey', $data);
        return $data['s_title'];
    }
    function get_survey()
    {   
        return $this->db->where('s_account_id', config_item('id'))->count_all_results('survey');
    }
    function get_survey_data()
    {   
        $results = array();
        $this->db->select('survey.*');
        $this->db->from('survey');
        $this->db->where('s_account_id',config_item('id'));
        return $this->db->get()->row_array();
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
                    $this->db->where('p_account_id', config_item('id'));
                    $this->db->update('pages', $page);
                }
                else
                {
                    $this->db->insert('pages', $page);
                }
            }
        $this->db->trans_complete();
        }
    }

    function menus( $param = array())
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

    function getMenus(){
        
        $menu = array();

        $child = $this->db->query("SELECT
                                        pt.id as parent_id,
                                        pt.mm_show_in as show_in,
                                        ch.id as child_id,
                                        ch.mm_item_connected_slug as menu_slug,
                                        ch.mm_item_connected_id as item_connected,
                                        ch.mm_name as child_name
                                    FROM 
                                        menu_manager pt
                                    LEFT JOIN menu_manager ch
                                        ON ch.mm_parent_id = pt.id
                                    WHERE
                                        ch.mm_parent_id > 0"
                                        );

        $parent = $this->db->query("SELECT
                                        pg.id as page_id, 
                                        pg.p_title as page_title,
                                        pg.p_slug as page_slug,
                                        pg.p_connected_menu as page_connected_to,
                                        mn.mm_name as child_name,
                                        mn.id as child_menu_id
                                    FROM 
                                        pages pg
                                    INNER JOIN menu_manager mn
                                        ON mn.mm_item_connected_id = pg.id
                                    WHERE
                                        mn.mm_parent_id = 0 AND pg.p_deleted = 0"
                                        );
        
        $menu['child'] = $child->result();
        $menu['parent'] = $parent->result();
        return $menu;
    }

    public function saveConnectedMenu($connected_menu = "", $menu_save)
    {
        $this->db->where('id',$connected_menu);
        $this->db->where('mm_account_id', config_item('id'));
        if( $this->db->update('menu_manager',$menu_save))
        {
            return true;
        }
        else
        {
            return false;
        }
        // $this
    }

    public function pageName($params = array())
    {
        $this->db->select('pages.p_title, menu_manager.mm_name');

        if( !empty( $params['connecting_menu_id'] ) )
        {
            $this->db->where('menu_manager.id', $params['connecting_menu_id']);
        }

        if( !empty( $params['connecting_page_id'] ) )
        {
            $this->db->where('menu_manager.mm_item_connected_id <>', $params['connecting_page_id']);
        }
        
        $this->db->join('pages','pages.id = menu_manager.mm_item_connected_id');
        $this->db->where('menu_manager.mm_account_id', config_item('id'));

        return $this->db->get('menu_manager')->row_array();

    }

    public function invalidateExistingMenus($connected_id, $params = array())
    {
        $this->db->where('mm_item_connected_id', $connected_id);
        $this->db->where('mm_account_id', config_item('id'));
        if( $this->db->update('menu_manager', $params))
        {
            return $this->db->affected_rows();
        }
        
    }
}
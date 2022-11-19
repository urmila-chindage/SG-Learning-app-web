<?php 
Class Task_model extends CI_Model
{	
    function __construct()
    {
        parent::__construct();
    }
    

    /*
    * To get all the Tasks
    * Created by : Mujeeb
    * Created at : 29/10/2019
    */

    function tasks($param = array())
    {
        $limit 			= isset($param['limit'])?$param['limit']:0;
        $offset 		= isset($param['offset'])?$param['offset']:0;
        $order_by 		= isset($param['order_by'])?$param['order_by']:'id';
        $direction 		= isset($param['direction'])?$param['direction']:'DESC';
        $status 		= isset($param['status'])?$param['status']:'';
        $count          = isset($param['count'])?$param['count']:false;
        $not_deleted    = isset($param['not_deleted'])?$param['not_deleted']:false;
        $keyword 		= isset($param['keyword'])?$param['keyword']:'';
        $filter 		= isset($param['filter'])?$param['filter']:0;
        $faculty_id     = isset($param['faculty_id'])?$param['faculty_id']:0;
        $priority       = isset($param['priority'])?$param['priority']:NULL;
        $this->db->order_by($order_by, $direction);
        if($limit>0)
        {
            $this->db->limit($limit, $offset);
        }
        if( $keyword )
        {
            $this->db->like('ft_tittle', $keyword); 
        }
        if( $priority != NULL )
        {
            $this->db->where('ft_priority', $priority); 
        }

        if( $not_deleted )
        {
            $this->db->where('ft_deleted', '0'); 
        }
        else
        {
            $this->db->where('ft_deleted', '1');
        }

        if( $filter )
        {
            switch ($filter) {
                case 'new':
                    $status = 'new';
                    break;
                case 'pending':
                    $status = 'pending';
                    break;
                case 'progress':
                    $status = 'progress';
                    break;
                case 'completed':
                    $status = 'completed';
                    break;

                default:
                    break;
            }
        }
        if( $status != '' )
        {
            $this->db->where('ft_status', $status); 
        }
        if($faculty_id)
        {
            $this->db->where('FIND_IN_SET('.$faculty_id.', ft_assignees)');            
        }
        
        $this->db->where('ft_account_id', config_item('id'));
        
        if( $count )
        {
            $result = $this->db->count_all_results('faculty_tasks');            
        }
        else
        {
            $result = $this->db->get('faculty_tasks')->result_array();
        }
        return $result;
    }

    /*
    * To get details of a Task
    * Created by : Mujeeb
    * Created at : 29/10/2019
    */
    function task($param=array())
    {
        $task_id 		= isset($param['id'])?$param['id']:'';
        return $this->db->where('id', $task_id)->get('faculty_tasks')->row_array();
    }

    /*
    * To get last Task
    * Created by : Mujeeb
    * Created at : 29/10/2019
    */
    function lastTask($param=array())
    {
        return $this->db->select('id')->order_by('id', 'DESC')->get('faculty_tasks')->row_array();
    }
    
    /*
    * To save Tasks
    * Created by : Mujeeb
    * Created at : 29/10/2019
    */

    function save($data)
    {
        $data['ft_account_id'] = config_item('id');
        if($data['id'])
        {
            $task = $this->db->select('id')->where('id',$data['id'])->get('faculty_tasks')->row();
            if(empty($task))
            {
                $this->db->insert('faculty_tasks', $data);
                return $this->db->insert_id();
            }
            else
            {
                $this->db->where('id', $data['id']);
                $this->db->where('ft_account_id', config_item('id'));
                $this->db->update('faculty_tasks', $data);
                return $data['id'];
            }
        }
        else
        {
            $this->db->insert('faculty_tasks', $data);
            return $this->db->insert_id();
	    }
    }

    function save_bulk($task)
    {
        $return  = array();
        $this->db->trans_start();
        foreach($task as $data)
        {
            $data['ft_account_id'] = config_item('id');
            $this->db->insert('assessment_report', $data);
            $data['id'] = $this->db->insert_id();
            $return[] = $data;
        }
        
        $this->db->trans_complete();
        return $return;
    }

    function delete($params = array()){
        return $this->db->where('id', $params['id'])->delete('faculty_tasks');
    }
    
    

}
?>
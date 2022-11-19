<?php 
Class Faculty_model extends CI_Model
{	
    function __construct()
    {
        parent::__construct();
        //$this->exclude_ids = array('2', '1');
        // $this->exclude_ids = array(config_item('super_admin'));
        $this->exclude_ids = array(config_item('super_admin'));
    }
    
    function super_admin($id = 2)
    {
        return $this->db->get_where('users', array('id' => $id))->row_array();
    }

    function faculties($param = array())
    {
        $limit 			= isset($param['limit'])?$param['limit']:0;
        $offset 		= isset($param['offset'])?$param['offset']:0;
        $order_by 		= isset($param['order_by'])?$param['order_by']:'users.id';
        $direction 		= isset($param['direction'])?$param['direction']:'DESC';
        $status 		= isset($param['status'])?$param['status']:'';
        $count          = isset($param['count'])?$param['count']:false;
        $not_deleted    = isset($param['not_deleted'])?$param['not_deleted']:false;
        $keyword 		= isset($param['keyword'])?$param['keyword']:'';
        $filter 		= isset($param['filter'])?$param['filter']:0;
        $role_id        = isset($param['role_id'])?$param['role_id']:false;
        $faculty_ids    = isset($param['faculty_ids'])?$param['faculty_ids']:array();
        $role_ids       = isset($param['role_ids'])?$param['role_ids']:0;
        $role_ids_not   = isset($param['role_ids_not'])?$param['role_ids_not']:0;
        $select         = isset($param['select'])? $param['select'] : 'users.*, cities_cp.*, roles.id as role_id, roles.rl_name, roles.rl_type, roles.rl_default_role, roles.rl_full_course';
        // $exclude_ids    = isset($param['exclude_ids'])? $param['exclude_ids']:'';
        if(isset($param['exclude_ids'])){
            $this->exclude_ids[] = $param['exclude_ids'];
        }
        $this->db->order_by($order_by, $direction);
        if($limit>0)
        {
            $this->db->limit($limit, $offset);
        }
        if( $keyword )
        {
            $this->db->like('users.us_name', $keyword); 
        }
        if( $not_deleted )
        {
            $this->db->where('users.us_deleted', '0'); 
        }
        if( $filter )
        {
            switch ($filter) {
                case 'active':
                    $status = '1';
                    $this->db->where('users.us_deleted', '0'); 
                    break;
                case 'inactive':
                    $status = '0';
                    $this->db->where('users.us_deleted', '0'); 
                    break;
                case 'deleted':
                    $this->db->where('users.us_deleted', '1'); 
                    break;

                default:
                    break;
            }
        }
        if( $status != '' )
        {
            $this->db->where('users.us_status', $status); 
        }
        if($role_id)
        {
            $this->db->where('users.us_role_id', $role_id);            
        }
        if($role_ids)
        {
            $this->db->where_in('users.us_role_id', $role_ids);
        }
        if($role_ids_not)
        {
            $this->db->where_not_in('users.us_role_id', $role_ids);
        }
        if(!empty($faculty_ids))
        {
            $this->db->where_in('users.id', $faculty_ids);
        }
        $this->db->where('users.us_account_id', config_item('id'));  
        $this->db->where_not_in('users.id', $this->exclude_ids);
        $this->db->join('roles', 'users.us_role_id = roles.id', 'left');
        $this->db->join('(SELECT cities_cp.id as city_id, cities_cp.city_name , states.state_name FROM cities cities_cp LEFT JOIN states ON cities_cp.state_id = states.id) cities_cp', 'users.us_native = cities_cp.city_id', 'left');
        // $this->db->from('users');
        $this->db->select($select);
        if( $count )
        {
            $result = $this->db->count_all_results('users');            
        }
        else
        {
            $result = $this->db->get('users')->result_array();
            //echo $this->db->last_query();die;
        }
        // echo $this->db->last_query();die;
        return $result;
    }

    function faculty($param=array())
    {
        $role_id    = isset($param['role_id'])?$param['role_id']:false;
        $select     = isset($param['select'])? $param['select'] : 'users.*, cities_cp.*, roles.id as role_id, roles.rl_name, roles.rl_type, roles.rl_default_role, roles.rl_full_course';

        $this->db->select($select); 
        if( isset($param['status'])) 
        {
            $this->db->where('users.us_status', 1);
        }
        if( isset($param['name'])) 
        {
            if( isset($param['id'])) 
            {
                $this->db->where('users.id!=', $param['id']);
            }
            $this->db->like('us_name', $param['name']);
        }
        if( isset($param['email'])) 
        {
            if( isset($param['exclude_id'])) 
            {
                $this->db->where('users.id!=', $param['exclude_id']);
            }
            $this->db->where('users.us_email', $param['email']);
        }

        if( isset($param['us_phone'])) 
        {
            if( isset($param['exclude_id'])) 
            {
                $this->db->where('users.id!=', $param['exclude_id']);
            }
            $this->db->where('users.us_phone', $param['us_phone']);
            
        }
        
        if( isset($param['id'])) 
        {
            $this->db->where('users.id', $param['id']);
        }
        if(isset($param['ids']))
        {
            $this->db->where_in('users.id', $param['ids']);
        }
        if($role_id)
        {
            $this->db->where('users.us_role_id', $role_id);
        }
        if(isset($param['us_email_verified']))
        {
            $this->db->where('us_email_verified', $param['us_email_verified']);
        }
        $this->db->where('users.us_account_id', config_item('id'));  
        $this->db->join('roles', 'users.us_role_id = roles.id', 'left');
        $this->db->join('(SELECT cities_cp.id as city_id, cities_cp.city_name , states.state_name FROM cities cities_cp LEFT JOIN states ON cities_cp.state_id = states.id) cities_cp', 'users.us_native = cities_cp.city_id', 'left');
        // $this->db->where('users.us_account_id', config_item('id'));
        $result = $this->db->get('users')->row_array();	
        /*if(isset($param['sample']))
        {
            echo '<pre>'; print_r($result);die('-');
        }*/
        //echo $this->db->last_query();die;
        return $result;
    }

    function user($param=array())
    {
        $role_id    = isset($param['role_id'])?$param['role_id']:false;
        $select     = isset($param['select'])? $param['select'] : 'users.*, cities_cp.*, roles.id as role_id, roles.rl_name, roles.rl_type, roles.rl_default_role, roles.rl_full_course';

        $this->db->select($select); 
        if( isset($param['status'])) 
        {
            $this->db->where('users.us_status', 1);
        }
        
        if( isset($param['email'])) 
        {
            $this->db->where('users.us_email', $param['email']);
        }

        if( isset($param['us_phone'])) 
        {
            $this->db->where('users.us_phone', $param['us_phone']);
        }
        
        if( isset($param['id'])) 
        {
            $this->db->where('users.id', $param['id']);
        }

        if(isset($param['ids']))
        {
            $this->db->where_in('users.id', $param['ids']);
        }

        if($role_id)
        {
            $this->db->where('users.us_role_id', $role_id);
        }

        if(isset($param['us_email_verified']))
        {
            $this->db->where('us_email_verified', $param['us_email_verified']);
        }

        $this->db->where('users.us_account_id', config_item('id'));
        $result = $this->db->get('users')->row_array();
        return $result;
    }
    
    function roles($param = array())
    {
        $ids = isset($param['ids'])?$param['ids']:false;
        //echo "<pre>";print_r($ids);die;
        $query = "SELECT * FROM roles WHERE rl_type = 1 AND rl_status = 1";
        $where = " AND (rl_account = 0 OR rl_account = 1)  ";
        if($ids)
        {
            $where .= ' OR id IN ('.implode(',', $ids).') ';
        }
        $result = $this->db->query($query.$where)->result_array();
        //echo $this->db->last_query();die;
        return $result;
    }
    
    function save_expertise($data)
    {
        if($data['id'])
        {
            $this->db->where('id', $data['id']);
            $this->db->update('faculty_expertise', $data);
            return $data['id'];
        }
        else
        {
            $this->db->insert('faculty_expertise', $data);
            return $this->db->insert_id();
	    }        
    }
    
    function save($data)
    {
        $data['us_account_id'] = config_item('id');
        if($data['id'])
        {
            $this->db->where('id', $data['id']);
            $this->db->where('us_account_id', config_item('id'));
            $this->db->update('users', $data);
            return $data['id'];
        }
        else
        {
            $this->db->insert('users', $data);
            return $this->db->insert_id();
	    }
    }
    
    function course_tutors($param = array())
    {
        $tutor_id   = isset($param['tutor_id'])?$param['tutor_id']:false;
        $course_id  = isset($param['course_id'])?$param['course_id']:false;
        $count      = isset($param['count'])?$param['count']:false;
        $select     = isset($param['select'])? $param['select'] : 'course_tutors.*, course_basics.cb_title';
        
        $this->db->select($select);
        $this->db->join('course_basics', 'course_tutors.ct_course_id = course_basics.id', 'left');
        
        $this->db->where('course_basics.cb_deleted!=', '1');     
        if($tutor_id)
        {
            $this->db->where('course_tutors.ct_tutor_id', $tutor_id);            
        }
        if($course_id)
        {
            $this->db->where('course_tutors.ct_course_id', $course_id);            
        }
        if( $count )
        {
            $result = $this->db->count_all_results('course_tutors');            
        }
        else
        {
            $result = $this->db->get('course_tutors')->result_array();
        }
        //echo $this->db->last_query();die;
        return $result;
    }


    /*
    * To get the course details of given course id and tutor id.
    * Created by : Neethu KP
    * Created at : 09/01/2017
    */
    function course_details($param = array()){

        $tutor_id   = isset($param['tutor_id'])?$param['tutor_id']:false;
        $course_id  = isset($param['course_id'])?$param['course_id']:false;
        $count      = isset($param['count'])?$param['count']:false;
        
        $this->db->select('course_tutors.ct_course_id as course_id');
        if($tutor_id)
        {
            $this->db->where('course_tutors.ct_tutor_id', $tutor_id);            
        }
        if($course_id)
        {
            $this->db->where('course_tutors.ct_course_id', $course_id);            
        }
        if( $count )
        {
            $result = $this->db->count_all_results('course_tutors');            
        }
        else
        {   
            $this->db->group_by('course_tutors.ct_course_id');
            $result = $this->db->get('course_tutors')->result_array();
        }
        //echo $this->db->last_query();die;
        return $result;

    }


    function unassign_faculty($data){
        if(isset($data['ct_course_id']))
        {
        $this->db->where('ct_course_id', $data['ct_course_id']);
        }
        $this->db->where('ct_tutor_id', $data['ct_tutor_id']);
        
        // if(isset($data['faculty_courses_ids']))
        // {
        //     $this->db->where_in('ct_course_id', implode(',',$data['faculty_courses_ids']));
        // }
        
        $this->db->delete('course_tutors');
    }
    
    function assign_faculty($data){
        $this->db->where('ct_course_id', $data['ct_course_id']);
        $this->db->where('ct_tutor_id', $data['ct_tutor_id']);
        $this->db->insert('course_tutors', $data);  
    }

    /*
    * To get all the reviews and ratings of given course Ids
    * Created by : Neethu KP
    * Created at : 09/01/2017
    */
    function get_all_reviews($param = array()){

        $tutor_id   = isset($param['tutor_id'])?$param['tutor_id']:false;
        $courseIds  = isset($param['courseIds'])?$param['courseIds']:false;

        $this->db->select('cc_user_name,cc_user_image,cc_rating,course_ratings.created_date,cc_reviews');
        $this->db->where_in('cc_course_id',$courseIds);
        $this->db->where('cc_reviews != ', '');
        $this->db->where('cc_status = ', 1 );
        $result = $this->db->get('course_ratings');
        $rowcount = $result->num_rows();

        $this->db->select('*');
        $this->db->where('cc_rating != ', '');
        $this->db->where_in('cc_course_id',$courseIds);
        $rate_result = $this->db->get('course_ratings');
        $rate_rowcount = $rate_result->num_rows();

        return array('reviews' => $result->result_array() , 'review_count' => $rowcount ,'rate_rowcount' => $rate_rowcount);
       
    }

    function expertises($param=array())
    {
        $limit 			= isset($param['limit'])?$param['limit']:0;
        $offset 		= isset($param['offset'])?$param['offset']:0;
        $order_by 		= isset($param['order_by'])?$param['order_by']:'id';
        $direction 		= isset($param['direction'])?$param['direction']:'DESC';
        $name    		= isset($param['name'])?$param['name']:'';
        $ids        	= isset($param['ids'])?$param['ids']:array();
		
        $this->db->select('faculty_expertise.*');
        $this->db->order_by($order_by, $direction);
        if($limit>0)
        {
            $this->db->limit($limit, $offset);
        }
        if( $name )
        {
            $this->db->like('fe_title', $name); 
        }
        if( !empty($ids) )
        {
            $this->db->where_in('id', $ids); 
        }
        $this->db->where('fe_account_id', config_item('id'));
        $result = $this->db->get('faculty_expertise');
        return $result->result_array();
    }
    
    /*
    * To get the count of students who have subscribed given courses
    * Created by : Neethu KP
    * Created at :11/01/2017
    */

    function get_students_count($param = array()){

        $courseIds  = isset($param['courseIds'])?$param['courseIds']:false;

        $this->db->select('count(cs_user_id) as student_count');
        $this->db->where_in('cs_course_id',$courseIds);
        $result = $this->db->get('course_subscription');
        return $result->row();
    }
    
    function teacher_locations($filter=false)
    {
        $filter_input = ($filter)?' AND us_account_id="'.config_item('id').'" AND us_deleted="0" AND us_status="1"':'';
        $query = 'SELECT * FROM cities WHERE id IN (SELECT us_native FROM users WHERE us_role_id = 3  '.$filter_input.' )';
        return $this->db->query($query)->result_array();
    }
    function mentor_locations($filter=false)
    {
        $filter_input = ($filter)?' AND us_account_id="'.config_item('id').'" AND us_deleted="0" AND us_status="1"':'';
        $query = 'SELECT * FROM cities WHERE id IN (SELECT us_native FROM users WHERE us_role_id = 6 '.$filter_input.')';
        return $this->db->query($query)->result_array();
    }
    
    function expertise($param=array())
    {
        if( isset($param['expertise_name'])) 
	    {
            $this->db->where('fe_title', $param['expertise_name']);
	    }
        if( isset($param['id'])) 
        {
            $this->db->where('id', $param['id']);
	    }
        $this->db->where('fe_account_id', config_item('id')); 
        if(isset($param['count']) && $param['count'] == true)
        {
            return $this->db->count_all_results('faculty_expertise');                        
        }
        else
        {
            $return  = $this->db->get('faculty_expertise')->row_array();	
            return $return;
        }
    }

    function insert_faculties_bulk($save_faculties)
    {
        $faculties_chunks  = array_chunk($save_faculties, 50);
        if(!empty($faculties_chunks))
        {
            foreach($faculties_chunks as $faculties)
            {
                $this->db->trans_start();
                foreach($faculties as $faculty)
                {
                    $faculty['us_account_id'] = config_item('id');
                    $this->db->insert('users', $faculty);
                }
                $this->db->trans_complete(); 
            }
        }
    }

}
?>
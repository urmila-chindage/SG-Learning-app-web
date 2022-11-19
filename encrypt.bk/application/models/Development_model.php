<?php 
Class Development_model extends CI_Model
{	
    function __construct()
    {
        parent::__construct();
    }
    
    function teachers($param=array())
    {
        $sub_query      = array();
        $category_ids   = isset($param['category_ids'])?$param['category_ids']:array();
        $language_ids   = isset($param['language_ids'])?$param['language_ids']:array();
        $ratings        = isset($param['ratings'])?$param['ratings']:array();
        $locations      = isset($param['locations'])?$param['locations']:array();
        $role_id        = isset($param['role_id'])?$param['role_id']:3;
        $keyword 	= isset($param['keyword'])?$param['keyword']:'';
        
        
        $limit 		= isset($param['limit'])?$param['limit']:0;
        $offset 	= isset($param['offset'])?$param['offset']:0;
        $order_by 	= isset($param['order_by'])?$param['order_by']:'us_badge';
        $direction 	= isset($param['direction'])?$param['direction']:'DESC';
        $total_count    = isset($param['count'])?$param['count']:false;
        
        $where_category = '';
        $where_language = '';
        $where_location = '';
        $where_ratings  = '';

        
        $rating_query    = ', (SELECT (SUM(cc_rating)/COUNT(cc_rating))  FROM course_ratings course_ratings_beta WHERE cc_course_id IN (SELECT ct_course_id FROM course_tutors WHERE ct_tutor_id = users.id)) as rating ';
        $parent_select   = 'users.*, roles.rl_name, CONCAT(cities_cp.city_name,", ",  cities_cp.state_name) as us_native '.$rating_query;
        
        $query           = 'SELECT '.$parent_select;
        $query          .= 'FROM users ';
        $query          .= 'LEFT JOIN roles ON users.us_role_id = roles.id ';
        $query          .= 'LEFT JOIN (SELECT cities_cp.id as city_id, cities_cp.city_name , states.state_name FROM cities cities_cp LEFT JOIN states ON cities_cp.state_id = states.id) cities_cp ON users.us_native = cities_cp.city_id ';
        $query          .= 'WHERE users.us_account_id = '.config_item('id').' AND users.us_status = "1" AND  users.us_deleted = "0" AND users.us_role_id = "3" ';
       
        $category_query_on_empty_cat_ids = '';
        /*if( $keyword && empty($category_ids) )
        {
            //processing where condition for category
            $category_keywords_empty = $this->db->query('SELECT GROUP_CONCAT(id) as cat_ids FROM categories WHERE ct_status = 1 AND ct_account_id = '.config_item('id').' AND ct_name LIKE "%'.$keyword.'%"')->row_array();
            if(isset($category_keywords_empty['cat_ids']) && $category_keywords_empty['cat_ids'] != '')
            {
                $category_keywords_empty = explode(',', $category_keywords_empty['cat_ids']);
                if(!empty($category_keywords_empty))
                {
                    $category_query_on_empty_cat_ids = '(';
                    $loop_count     = sizeof($category_keywords_empty);
                    $count          = 1;
                    foreach ($category_keywords_empty as $cat_id)
                    {
                        $category_query_on_empty_cat_ids .= ' CONCAT(",", us_category_id, ",") LIKE CONCAT("%,", '.$cat_id.', ",%") ';
                        if($loop_count>$count)
                        {
                            $category_query_on_empty_cat_ids .= ' OR ';
                        }
                        $count++;
                    }
                    $category_query_on_empty_cat_ids .= ') OR ';
                }
            }
            //end
        }*/
        
        if(!empty($category_ids))
        {
            $where_category = '(';
            /*$loop_count     = sizeof($category_ids);
            $count          = 1;
            foreach ($category_ids as $category_id)
            {
                $where_category .= ' CONCAT(",", us_category_id, ",") LIKE CONCAT("%,", '.$category_id.', ",%") ';
                if($loop_count>$count)
                {
                    $where_category .= ' OR ';
                }
                $count++;
            }*/
            $where_category .= ' users.id IN (SELECT ct_tutor_id FROM course_tutors WHERE ct_course_id IN (SELECT id FROM course_basics WHERE cb_category IN ('.implode(',',$category_ids).') ) GROUP BY ct_tutor_id) ';
            $where_category .= ')';
        }

        if(!empty($language_ids))
        {
            $where_language = '(';
            $loop_count     = sizeof($language_ids);
            $count          = 1;
            foreach ($language_ids as $language_id)
            {
                $where_language .= ' CONCAT(",", us_language_speaks, ",") LIKE CONCAT("%,", '.$language_id.', ",%") ';
                if($loop_count>$count)
                {
                    $where_language .= ' OR ';
                }
                $count++;
            }
            $where_language .= ')';
        }

        if(!empty($locations))
        {
            $where_location = '(';
            $loop_count     = sizeof($locations);
            $count          = 1;
            foreach ($locations as $location)
            {
                $where_location .= ' us_native = '.$location;
                if($loop_count>$count)
                {
                    $where_location .= ' OR ';
                }
                $count++;
            }
            $where_location .= ')';
        }

        //appending where conditions
        if( $where_category != '' || $where_language != '' || $where_location != '' )
        {
            $query .= ' AND (';
            
            
            $implode_where = array();
            if($where_category!='')
            {
                $implode_where[] = $where_category;
            }
            if($where_language!='')
            {
                $implode_where[] = $where_language;
            }
            if($where_location!='')
            {
                $implode_where[] = $where_location;
            }
            if(sizeof($implode_where))
            {
                $query .= implode(' AND ', $implode_where);
            }
            
            
            $query .= ' )';
        }
        //end
        
        /*
         * processing where conditions
         */
        if($keyword)
        {
            //processing course. this means getting tutor id from the course which match th keyword(matichin course) 
            $course_keyword_query = '';
            //$course_keyword       = $this->db->query('SELECT GROUP_CONCAT(ct_tutor_id) as keyword_tutor_ids  FROM course_tutors WHERE ct_course_id IN (SELECT id FROM course_basics WHERE cb_title LIKE "%'.$keyword.'%" OR cb_category IN (SELECT id FROM categories WHERE ct_name LIKE "%'.$keyword.'%" ))')->row_array();
            $course_keyword       = $this->db->query('SELECT GROUP_CONCAT(ct_tutor_id) as keyword_tutor_ids  FROM course_tutors WHERE ct_course_id IN (SELECT id FROM course_basics WHERE cb_title LIKE "%'.$keyword.'%" )')->row_array();
            if(isset($course_keyword['keyword_tutor_ids']) && $course_keyword['keyword_tutor_ids'] != '')
            {
                $course_keyword_query = ' OR users.id IN ('.$course_keyword['keyword_tutor_ids'].') ';
            }
            //End
            
            $expertise_keyword_query = '';
            $expertise_where = array();
            $expertises = $this->db->query("SELECT id FROM faculty_expertise WHERE fe_title LIKE '%".$keyword."%'")->result_array();
            if(!empty($expertises))
            {
                foreach($expertises as $expertise)
                {
                    $expertise_where[] = '  CONCAT(",", users.us_expertise, ",") LIKE CONCAT("%,", '.$expertise['id'].', ",%") ';
                }
                $expertise_keyword_query = ' OR '.implode(' OR ', $expertise_where );
            }
            
            $query .= ' AND ('.$category_query_on_empty_cat_ids.' users.us_name LIKE "%'.$keyword.'%" OR users.us_about LIKE "%'.$keyword.'%" '.$course_keyword_query.' '.$expertise_keyword_query.' )';
            //echo $query;die;
        }
        
        if(!empty($ratings))
        {
            $where_ratings = ' HAVING ( ';
            $loop_count     = sizeof($ratings);
            $count          = 1;
            foreach ($ratings as $rating)
            {
                $where_ratings .= ' (rating >= '.$rating.' AND rating < '.($rating+1).')';
                if($loop_count>$count)
                {
                    $applied_filter = true;
                    $where_ratings .= ' OR ';
                }
                $count++;
            }
            $where_ratings .= ')';
            $query .= $where_ratings;
        }

        /*
         * End of processing where conditions
         */
        $query          .= ' ORDER BY '.$order_by.' '.$direction.' ';
        if($limit>0)
        {
        $query          .= ' LIMIT '.$offset.', '.$limit;
        }

        //echo $query;die;
        
        if( $total_count )
        {
            $result = $this->db->query($query)->result_array();
            $result = count($result);
        }
        else
        {
            $result = $this->db->query($query)->result_array();
        }
        //echo '<pre>'; print_r($result); die;
        return $result;
    }
    
    function mentors($param=array())
    {
        $sub_query      = array();
        $category_ids   = isset($param['category_ids'])?$param['category_ids']:array();
        $language_ids   = isset($param['language_ids'])?$param['language_ids']:array();
        $ratings        = isset($param['ratings'])?$param['ratings']:array();
        $locations      = isset($param['locations'])?$param['locations']:array();
        $role_id        = isset($param['role_id'])?$param['role_id']:6;
        $keyword 	= isset($param['keyword'])?$param['keyword']:'';
        $user_id        = isset($param['id'])?$param['id']:'';
        
        
        $limit 		= isset($param['limit'])?$param['limit']:0;
        $offset 	= isset($param['offset'])?$param['offset']:0;
        $order_by 	= isset($param['order_by'])?$param['order_by']:'us_badge';
        $direction 	= isset($param['direction'])?$param['direction']:'DESC';
        $total_count    = isset($param['count'])?$param['count']:false;
        
        $where_category = '';
        $where_language = '';
        $where_location = '';
        $where_ratings  = '';

        
        $parent_select   = 'users.*, roles.rl_name, CONCAT(cities_cp.city_name,", ",  cities_cp.state_name) as us_native, mentor_ratings_cp.*';
        
        $query           = 'SELECT '.$parent_select;
        $query          .= 'FROM users ';
        $query          .= 'LEFT JOIN roles ON users.us_role_id = roles.id ';
        $query          .= 'LEFT JOIN (SELECT cities_cp.id as city_id, cities_cp.city_name , states.state_name FROM cities cities_cp LEFT JOIN states ON cities_cp.state_id = states.id) cities_cp ON users.us_native = cities_cp.city_id ';
        $query          .= 'LEFT JOIN (SELECT mentor_ratings_cp.mr_mentor_id, (SUM(mr_rating)/COUNT(mr_rating)) as rating FROM mentor_ratings mentor_ratings_cp GROUP BY mr_mentor_id ) mentor_ratings_cp ON mentor_ratings_cp.mr_mentor_id = users.id ';
        if($user_id != ''){
            $query          .= 'WHERE users.us_account_id = '.config_item('id').' AND users.us_status = "1" AND  users.us_deleted = "0" AND users.us_role_id = "6" AND users.id = '.$user_id.' ';
        }else{
            $query          .= 'WHERE users.us_account_id = '.config_item('id').' AND users.us_status = "1" AND  users.us_deleted = "0" AND users.us_role_id = "6" ';
        }
       
        $category_query_on_empty_cat_ids = '';
        if( $keyword && empty($category_ids) )
        {
            //processing where condition for category
            $category_keywords_empty = $this->db->query('SELECT GROUP_CONCAT(id) as cat_ids FROM categories WHERE ct_status = 1 AND ct_account_id = '.config_item('id').' AND ct_name LIKE "%'.$keyword.'%"')->row_array();
            if(isset($category_keywords_empty['cat_ids']) && $category_keywords_empty['cat_ids'] != '')
            {
                $category_keywords_empty = explode(',', $category_keywords_empty['cat_ids']);
                if(!empty($category_keywords_empty))
                {
                    $category_query_on_empty_cat_ids = '(';
                    $loop_count     = sizeof($category_keywords_empty);
                    $count          = 1;
                    foreach ($category_keywords_empty as $cat_id)
                    {
                        $category_query_on_empty_cat_ids .= ' CONCAT(",", us_category_id, ",") LIKE CONCAT("%,", '.$cat_id.', ",%") ';
                        if($loop_count>$count)
                        {
                            $category_query_on_empty_cat_ids .= ' OR ';
                        }
                        $count++;
                    }
                    $category_query_on_empty_cat_ids .= ') OR ';
                }
            }
            //end
        }
        
        if(!empty($category_ids))
        {
            $where_category = '(';
            $loop_count     = sizeof($category_ids);
            $count          = 1;
            foreach ($category_ids as $category_id)
            {
                $where_category .= ' CONCAT(",", us_category_id, ",") LIKE CONCAT("%,", '.$category_id.', ",%") ';
                if($loop_count>$count)
                {
                    $where_category .= ' OR ';
                }
                $count++;
            }
            $where_category .= ')';
        }

        if(!empty($language_ids))
        {
            $where_language = '(';
            $loop_count     = sizeof($language_ids);
            $count          = 1;
            foreach ($language_ids as $language_id)
            {
                $where_language .= ' CONCAT(",", us_language_speaks, ",") LIKE CONCAT("%,", '.$language_id.', ",%") ';
                if($loop_count>$count)
                {
                    $where_language .= ' OR ';
                }
                $count++;
            }
            $where_language .= ')';
        }

        if(!empty($locations))
        {
            $where_location = '(';
            $loop_count     = sizeof($locations);
            $count          = 1;
            foreach ($locations as $location)
            {
                $where_location .= ' us_native = '.$location;
                if($loop_count>$count)
                {
                    $where_location .= ' OR ';
                }
                $count++;
            }
            $where_location .= ')';
        }

        //appending where conditions
        if( $where_category != '' || $where_language != '' || $where_location != '' )
        {
            $query .= ' AND (';
            
            
            $implode_where = array();
            if($where_category!='')
            {
                $implode_where[] = $where_category;
            }
            if($where_language!='')
            {
                $implode_where[] = $where_language;
            }
            if($where_location!='')
            {
                $implode_where[] = $where_location;
            }
            if(sizeof($implode_where))
            {
                $query .= implode(' AND ', $implode_where);
            }
            
            
            $query .= ' )';
        }
        //end
        
        /*
         * processing where conditions
         */
        if($keyword)
        {
            //processing course. this means getting tutor id from the course which match th keyword(matichin course) 
            $course_keyword_query = '';
            $course_keyword       = $this->db->query('SELECT GROUP_CONCAT(id) as keyword_mentor_ids FROM categories WHERE ct_name LIKE "%'.$keyword.'%"')->row_array();
            if(isset($course_keyword['keyword_mentor_ids']) && $course_keyword['keyword_mentor_ids'] != '')
            {
                $course_keyword_query = ' OR users.id IN ('.$course_keyword['keyword_mentor_ids'].') ';
            }
            //End
            //echo $course_keyword_query;die;
            $query .= ' AND ('.$category_query_on_empty_cat_ids.' users.us_name LIKE "%'.$keyword.'%" OR users.us_about LIKE "%'.$keyword.'%" '.$course_keyword_query.' )';
        }
        
        if(!empty($ratings))
        {
            $where_ratings = ' HAVING ( ';
            $loop_count     = sizeof($ratings);
            $count          = 1;
            foreach ($ratings as $rating)
            {
                $where_ratings .= ' (mentor_ratings_cp.rating >= '.$rating.' AND mentor_ratings_cp.rating < '.($rating+1).')';
                if($loop_count>$count)
                {
                    $applied_filter = true;
                    $where_ratings .= ' OR ';
                }
                $count++;
            }
            $where_ratings .= ')';
            $query .= $where_ratings;
        }

        /*
         * End of processing where conditions
         */
        $query          .= ' ORDER BY '.$order_by.' '.$direction.' ';
        if($limit>0)
        {
        $query          .= ' LIMIT '.$offset.', '.$limit;
        }

        //echo $query;die;
        if($user_id != ''){
            $result = $this->db->query($query)->row_array();
        }else{
            if( $total_count )
            {
                $result = $this->db->query($query)->result_array();
                $result = count($result);
            }
            else
            {
                $result = $this->db->query($query)->result_array();
            }
        }
        //echo '<pre>'; print_r($param); 
        return $result;
    }
    
    function courses($param=array())
    {
        $sub_query      = array();
        $category_ids   = isset($param['category_ids'])?$param['category_ids']:array();
        $language_ids   = isset($param['language_ids'])?$param['language_ids']:array();
        $price_ids      = isset($param['price_ids'])?$param['price_ids']:array();
        $keyword 	= isset($param['keyword'])?$param['keyword']:'';
        
        $limit 		= isset($param['limit'])?$param['limit']:0;
        $offset 	= isset($param['offset'])?$param['offset']:0;
        $order_by 	= isset($param['order_by'])?$param['order_by']:'cb_position';
        $direction 	= isset($param['direction'])?$param['direction']:'DESC';
        $total_count    = isset($param['count'])?$param['count']:false;
        
        $where_category = '';
        $where_price    = '';
        $where_language = '';
        
        $rating_query    = ', (SUM(cc_rating)/COUNT(cc_rating)) as rating ';
        $parent_select   = 'course_basics.*'.$rating_query;
        
        $query           = 'SELECT '.$parent_select;
        $query          .= 'FROM course_basics '; 
        $query          .= 'LEFT JOIN course_ratings ON course_ratings.cc_course_id = course_basics.id';
        $query          .= ' WHERE course_basics.cb_account_id = "'.config_item('id').'" AND course_basics.cb_status = "1" AND course_basics.cb_deleted = "0"';
        
        if( $keyword )
        {
            $query .= ' AND course_basics.cb_title LIKE "%'.$keyword.'%"';        
        }
        
        if(!empty($category_ids))
        {
            $where_category = '(';
            $loop_count     = sizeof($category_ids);
            $count          = 1;
            foreach ($category_ids as $category_id)
            {
                $where_category .= ' CONCAT(",", cb_category, ",") LIKE CONCAT("%,", '.$category_id.', ",%") ';
                if($loop_count>$count)
                {
                    $where_category .= ' OR ';
                }
                $count++;
            }
            $where_category .= ')';
        }

        if(!empty($language_ids))
        {
            $where_language = '(';
            $loop_count     = sizeof($language_ids);
            $count          = 1;
            foreach ($language_ids as $language_id)
            {
                $where_language .= ' CONCAT(",", cb_language, ",") LIKE CONCAT("%,", '.$language_id.', ",%") ';
                if($loop_count>$count)
                {
                    $where_language .= ' OR ';
                }
                $count++;
            }
            $where_language .= ')';
        }
        
        if(!empty($price_ids))
        {
            $where_price = ' (';
            $loop_count     = sizeof($price_ids);
            $count          = 1;
            foreach ($price_ids as $price_id)
            {
                if($price_id == '1')
                {
                    $where_price .= ' (cb_is_free = 1 OR cb_price <= 0) ';
                }else if($price_id == '2')
                {
                    $where_price .= ' (cb_price > 0)';
                }
                if($loop_count>$count)
                {
                    $where_price .= ' OR ';
                }
                $count++;
            }
            $where_price .= ')';
        }
        //echo $where_price;die;
        
        //appending where conditions
        if( $where_category != '' || $where_language != '' || $where_price != '')
        {
            $query .= ' AND (';
            
            
            $implode_where = array();
            if($where_category!='')
            {
                $implode_where[] = $where_category;
            }
            if($where_language!='')
            {
                $implode_where[] = $where_language;
            }
            if($where_price!='')
            {
                $implode_where[] = $where_price;
            }
            if(sizeof($implode_where))
            {
                $query .= implode(' AND ', $implode_where);
            }
            
            
            $query .= ' )';
        }
        
        $query .= ' GROUP BY course_basics.id';
        
        $query          .= ' ORDER BY '.$order_by.' '.$direction.' ';
        if($limit>0)
        {
        $query          .= ' LIMIT '.$offset.', '.$limit;
        }
        
       //echo $query;die;  
        
        $result = $this->db->query($query)->result_array();
        
        return $result;
    }
    
    
    function mentor_languages()
    {
        $return          = array();
        $languages_query = 'SELECT GROUP_CONCAT(us_language_speaks) as us_language_speaks FROM users WHERE us_language_speaks IS NOT NULL AND us_role_id = 6 AND us_deleted="0" AND us_status="1" AND us_account_id="'.config_item('id').'"';
        $languages       = $this->db->query($languages_query)->row_array();
        $languages       = isset($languages['us_language_speaks'])?$languages['us_language_speaks']:'';
        if($languages)
        {
            $return = $this->db->query("SELECT * FROM course_language WHERE id IN (".$languages.")")->result_array();
        }
        return $return;
    }

    function mentor_categories($param = array()){

        $category_array = explode(",", $param['category']);
        $this->db->select('ct_name');
        $this->db->where(array('ct_deleted'=>'0','ct_status'=>'1','ct_account_id'=>$this->config->item('id')));
        $this->db->where_in('id',$category_array);
        $return = $this->db->get('categories')->result_array();

        return $return;
    }
    function db_mentor_languages($param = array()){

        $category_array = explode(",", $param['lang_ids']);
        $this->db->select('cl_lang_name');
        $this->db->where_in('id',$category_array);
        $return = $this->db->get('course_language')->result_array();

        return $return;
    }
    function get_mentor_ratings($param = array()){
        $this->db->select('*');
        $this->db->where('mr_mentor_id',$param['id']);
        return $this->db->get('mentor_ratings')->result_array();
    }

    function get_rated_course($param = array()){
        if(isset($param['user_id'])){
            $query   = 'SELECT course_ratings_cp.*, course_basics.* FROM (SELECT cc_course_id, SUM(cc_rating)/COUNT(cc_course_id) as rating FROM course_ratings course_ratings_cp WHERE cc_course_id IN (SELECT id FROM course_basics WHERE cb_category='.$param["course_id"].' AND course_basics.id NOT IN (SELECT cs_course_id FROM course_subscription WHERE cs_user_id = '.$param["user_id"].' AND cb_category = '.$param['course_id'].' )) GROUP BY cc_course_id ORDER BY rating DESC LIMIT 0, 1) course_ratings_cp LEFT JOIN course_basics ON course_ratings_cp.cc_course_id = course_basics.id WHERE course_basics.cb_status="1" AND course_basics.cb_deleted="0"';
        }else{
            $query   = 'SELECT course_ratings_cp.*, course_basics.* FROM (SELECT cc_course_id, SUM(cc_rating)/COUNT(cc_course_id) as rating FROM course_ratings course_ratings_cp WHERE cc_course_id IN (SELECT id FROM course_basics WHERE cb_category='.$param["course_id"].') GROUP BY cc_course_id ORDER BY rating DESC LIMIT 0, 1) course_ratings_cp LEFT JOIN course_basics ON course_ratings_cp.cc_course_id = course_basics.id  WHERE course_basics.cb_status="1" AND course_basics.cb_deleted="0"';
        }
        $result = $this->db->query($query)->result_array();
        return $result;
    }
    function get_course_lectures($course_id){
        $this->db->select('users.id, users.us_name, users.us_image');
        $this->db->from('course_tutors');
        $this->db->join('users', 'users.id = course_tutors.ct_tutor_id');
        if($course_id){
            $this->db->where('course_tutors.ct_course_id', $course_id);
        }
        return $this->db->get()->result_array();
    }

    function get_course_rating_count($course_id){
        $this->db->select('*');
        $this->db->from('course_ratings');
        if($course_id){
            $this->db->where('cc_course_id', $course_id);
        }
        return $this->db->get()->num_rows();
    }

    function get_course_subs_count($course_id){
        $this->db->select('*');
        $this->db->from('course_subscription');
        if($course_id){
            $this->db->where('cs_course_id', $course_id);
        }
        return $this->db->get()->num_rows();
    }

    function whish_stat($param = array()){
        $this->db->select('*');
        $this->db->where('cw_course_id',$param['course_id']);
        $this->db->where('cw_user_id',$param['user_id']);
        return $this->db->get('course_wishlist')->num_rows();
    }
    function db_check_mentor_rating($param = array()){
        $this->db->select('*');
        $this->db->where('mr_user_id',$param['user_id']);
        $this->db->where('mr_mentor_id',$param['mentor_id']);
        if(isset($param['count'])){
            return $this->db->get('mentor_ratings')->num_rows();
        }else{
            return $this->db->get('mentor_ratings')->row_array();
        }
    }
    function db_update_mentor_rating($param = array()){
        $args = array('mr_user_id'=>$param['user_id'],'mr_review'=>$param['review'],'mr_mentor_id'=>$param['mentor_id'],'mr_rating'=>$param['rating']);
        $this->db->where('mr_user_id',$param['user_id']);
        $this->db->where('mr_mentor_id',$param['mentor_id']);
        $this->db->update('mentor_ratings',$args);
    }
    function db_insert_mentor_rating($param = array()){
        $args = array('mr_user_id'=>$param['user_id'],'mr_mentor_id'=>$param['mentor_id'],'mr_rating'=>$param['rating'],'mr_review'=>$param['review']);
        $this->db->insert('mentor_ratings',$args);
    }
    function db_update_mentor_profile($param = array()){
        $arr = array('us_about'=>$param['about']);
        $this->db->where('id',$param['mentor_id']);
        return $this->db->update('users',$arr);
    }
    
    function get_mentor_reviews($param = array()){
        if(isset($param['select'])){
            $this->db->select($param['select']);
        }else{
            $this->db->select('users.us_image, mentor_ratings.created_date,mentor_ratings.mr_review,users.us_name,mentor_ratings.mr_rating');
        }
        $this->db->from('mentor_ratings');
        $this->db->join('users','mentor_ratings.mr_user_id = users.id');
        $this->db->where('mentor_ratings.mr_mentor_id',$param['mentor_id']);
        $this->db->where('mentor_ratings.mr_review != ""');
        if(isset($param['offset'])&&$param['offset']!=0){
            $this->db->limit($param['limit'],$param['offset']);
        }else{
            if(isset($param['limit'])){
                $this->db->limit($param['limit'],0);
            }
        }
        if(isset($param['direction'])){
            $this->db->order_by('',$param['direction']);
        }

        $return = $this->db->get()->result_array();
        //echo $this->db->last_query();
        return $return;
    }
}
?>